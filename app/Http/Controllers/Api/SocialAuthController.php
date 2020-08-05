<?php

namespace App\Http\Controllers\Api;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseApiController;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\BackpackUser as User;
use App\User as User2;
use App\Service;
use Illuminate\Support\Str;
use App\Profile;
use App\Location;

class SocialAuthController extends BaseApiController
{
    function socialAuth(Request $request){
        if($request->iosLogin != true){
            $credentials = User2::where('email', $request->email)->first();
        }else{
            $credentials = User2::where('ios_id', $request->userId)->first();
        }

        if($credentials == null){
            $socialAuth = "";
            if($request->facebookLogin == true){
                $socialAuth = "facebook";
            }else if($request->googleLogin == true){
                $socialAuth = "google";
            }else if($request->iosLogin == true){
                $socialAuth = "ios";
            }

            $credentials = $this->registerSocialAuth($request->email, $request->name, $request->userId, $socialAuth);

        }

        $credentials->device_token = $request->deviceToken;
        $credentials->update();
        //return response()->json($credentials);

        if (!$token = JWTAuth::fromUser($credentials)) {
            return response([
                'status' => 'error',
                'error' => 'invalid.credentials',
                'msg' => '¡El e-mail y/o la contraseña son incorrectas! vuelva a intentarlo...'
                //'msg' => 'Credenciales inválidas.'
            ], 400);
        }//!token
        //$user=Auth::guard('api')->user() ? Auth::guard('api')->user() : Auth::user();
        $user=User::find($credentials->id);
        $image=null;
        $randomCode=Str::random(4);
        
        if(starts_with($user->profile->image, 'https://'))
            $image=$user->profile->image."?test=".$randomCode;
        else
            $image=url($user->profile->image);

        $user->last_login=new \DateTime;
        $user->device_token = $request->deviceToken;
        $user->update();
        $services_text="";
        if(count($user->services)==1)
            $services_text=$user->services[0]->name;
        
            foreach($user->services as $service){
            if($services_text=="")
                $services_text=$service->name;
            else
                $services_text=", ".$service->name;
        }//services
        
        return response()->json([
            'status' => 'success',
            'token' => 'Bearer '.$token,
            'tokenCode' => $token,
            'image'=>$image,
            'user' => $user,
            'profile' => $user->profile,
            "location"=>$user->profile->location,
            'roles' => User::find($user->id)->roles,
            'services'=>$user->services,
            'services_text'=>$services_text,
            'averageRating'=>$user->averageRating,
            'averageRatingFloat'=>(float)$user->averageRating,
            'averageRatingInt'=>(int)$user->averageRating,
            'ratingPercent'=>$user->ratingPercent(5)
        ]);

    }

    function registerSocialAuth($email, $displayName, $userId, $socialAuth){
        try {
            
            $user = new User;
            $user->email = $email;
            $user->name = $displayName;
            $user->is_register_completed = false;
            if($socialAuth == 'google')
                $user->google_id = $userId;
            else if($socialAuth == 'facebook')
                $user->facebook_id = $userId;
            else if($socialAuth == 'ios')
                $user->ios_id = $userId;
            $user->save();
            
            $user->assignRole('Ofertante');
            $user->roles()->sync([1]);

            $image="https://traya.com.ar/traya-backend/public/assets/images/generic-user.png";
            
            $location = Location::first();

            $profile = new Profile;
            $profile->phone = "+54934";
            $profile->description = "";
            $profile->image = $image;
            $profile->positive_calification = 0;
            $profile->negative_calification = 0;
            $profile->user_id = $user->id;
            $profile->domicile = "";
            $profile->location_id = $location->id;
            $profile->save();
            
        } catch (\Exception $e) {
            //Message Error
            $status = 500;
            $response = [
                'errors' => $e->getMessage()
            ];
        }

        $credentials = User2::find($user->id);
        return $credentials;

    }


}
