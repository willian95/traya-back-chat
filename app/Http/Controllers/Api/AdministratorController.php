<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseApiController;
use App\Models\BackpackUser as User;
use Illuminate\Support\Str;
use App\Ad;
use App\AdType;
use Auth;
use File;

class AdministratorController extends BaseApiController
{
    
    function enableAdministrator(Request $request){

        $userR=User::find($request->user_id);
        if($userR->hasRole('Demandante')){
            $userR->removeRole('Demandante');
        }else if($userR->hasRole('Ofertante')){
            $userR->removeRole('Ofertante');
        }
        $userR->assignRole('Administrador Localidad');

        return response()->json(["success" => true, "data" => $request->user_id]);

    }

    function disableAdministrator(Request $request){

        $userR=User::find($request->user_id);
        $userR->removeRole('Administrador Localidad');
        $userR->assignRole('Ofertante');

        return response()->json(["success" => true, "data" => $request->user_id]);

    }

    function administratorsByLocation(Request $request){

        try {
            $auth=Auth::guard('api')->user() ? Auth::guard('api')->user() : Auth::user();
            $rows=User::query();
            //$rows=User2::query();
            $rows=$rows->with("profile","services")->whereNotIn('id',[$auth->id]);

            $filters=isset($request->filters) ? json_decode($request->filters) : (object)[];
            if(isset($filters->trashed))
              $rows->onlyTrashed();
            $rows=$rows->get();
            $users=[];
            $administrators=[];
            $randomCode=Str::random(4);
            foreach($rows as $user){
              if($user->profile['location_id'] == $request->location_id){

                $image=null;
                if(starts_with($user->profile['image'], 'https://'))
                    $image=$user->profile['image']."?test=".$randomCode;
                else
                    $image=url($user->profile['image']);
                $services_text="";
                if(count($user->services)==1)
                    $services_text=$user->services[0]->name;
                foreach($user->services as $service){
                    if($services_text=="")
                    $services_text=$service->name;
                    else
                    $services_text=", ".$service->name;
                }//services
        
                
                if($user->roles[0]->name != 'Administrador Localidad')
                {
                    $users[]=[
                        'id' => $user->id,
                        'user' => $user,
                        'image'=>$image,
                        'services'=>$user->services,
                        'services_text'=>$services_text,
                        'roles' => $user->roles,
                        'role_name' => $user->roles[0]->name,
                        'averageRating'=>$user->averageRating,
                        'averageRatingFloat'=>(float)$user->averageRating,
                        'averageRatingInt'=>(int)$user->averageRating,
                        'ratingPercent'=>$user->ratingPercent(5)
                    ];

                }else{

                    $administrators[]=[
                        'id' => $user->id,
                        'user' => $user,
                        'image'=>$image,
                        'services'=>$user->services,
                        'services_text'=>$services_text,
                        'roles' => $user->roles,
                        'role_name' => $user->roles[0]->name,
                        'averageRating'=>$user->averageRating,
                        'averageRatingFloat'=>(float)$user->averageRating,
                        'averageRatingInt'=>(int)$user->averageRating,
                        'ratingPercent'=>$user->ratingPercent(5)
                    ];

                }

              }
            }//foreach
      
            $response=[
              'data'=>$users,
              'administrators' => $administrators
            ];
          } catch (\Exception $e) {
            $status = 500;
            $response = [
              'errors' => $e->getMessage(),
              'line'=> $e->getLine()
            ];
          }
          return response()->json($response, $status ?? 200);

    }

    function getAdTypes(){

        $adTypes = AdType::all();

        return response()->json($adTypes);
    }

    function storeAd(Request $request){

        try{

            $randomCode=Str::random(15);
            saveImage($request->file,'ads/'.$randomCode.'.jpg');

            //$data = getimagesize('ads/'.$randomCode.'.jpg');
            //if(($request->ad_type_id == 1 && $data[0] == 379 && $data[1] == 90) || ($request->ad_type_id == 2 && $data[0] == 379 && $data[1] == 180) || ($request->ad_type_id == 3 && $data[0] == 379 && $data[1] == 270)){

                $ad = new Ad();
                $ad->ad_type_id = $request->ad_type_id;
                $ad->location_id = $request->locality_id;
                $ad->name = $randomCode.".jpg";
                $ad->save();

                return response()->json(["success" => true, "msg" => "Anuncio publicado"]);

            /*}else{

                if(File::exists('ads/'.$randomCode.'.jpg')) {
                    File::delete('ads/'.$randomCode.'.jpg');
                }

                return response()->json(["success" => false, "msg" => "Tamaño de imagen erróneo"]);

            }*/

            

        }catch(\Exception $e){  

            return response()->json(["success" => false, "msg" => "Error en el servidor", "error" => $e->getMessage()]);

        }

    }

    function getAds($location_id){

        if($location_id != 0){
            $ads = Ad::where('location_id', $location_id)->with('adType')->get();
        }else{
            $ads = Ad::with('adType')->get();
        }
        

        return response()->json(["ads" => $ads]);

    }

    function deleteAd($id){

        try{

            $ad = Ad::find($id);
            
            if(File::exists('ads/'.$ad->name)) {
                File::delete('ads/'.$ad->name);
            }

            $ad->delete();

            return response()->json(["success" => true, "msg" => "Anuncio eliminado"]);

        }catch(\Exception $e){

            return response()->json(["success" => false, "msg" => "Error en el servidor", "error" => $e->getMessage()]);

        }

        

    }


}
