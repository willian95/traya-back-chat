<?php

namespace App\Http\Controllers\Api;
/*si tiene array*/
use Illuminate\Http\Request;
//use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\BaseApiController;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\BackpackUser as User;
//use App\User;
use App\Profile;
use \App\Mail\Contact;
use \App\Mail\RecoveryPasswordMail;
use \Illuminate\Support\Facades\Mail;
use App\Http\Requests\RegisterFormRequest;
use App\Http\Requests\RecoveryPasswordRequest;
use App\ServicesUser;
use App\Service;
use Illuminate\Support\Str;
use App\User as User2;
use App\ShowForm;
//use Redirect;
//class AuthController extends Controller
use \Illuminate\Mail\PendingMail;
class AuthController extends BaseApiController
{

  public function changeAddress(Request $request){

    if($request->address != ""){
      
      $profile = Profile::where('user_id', \Auth::user()->id)->first();
      $profile->domicile = $request->address;
      $profile->update();
      
      return response()->json(["success" => true, "msg" => "Domicilio actualizado"]);

    }else{

      return response()->json(["success" => false, "msg" => "Campo ubicación es obligatorio"]);

    }

  }

  public function index(Request $request){
    try {
      $auth=Auth::guard('api')->user() ? Auth::guard('api')->user() : Auth::user();
      $rows=User::query();
      // $rows=User2::query();
      $rows=$rows->with("profile","services")->whereNotIn('id',[$auth->id]);
      $filters=isset($request->filters) ? json_decode($request->filters) : (object)[];
      if(isset($filters->trashed))
        $rows->onlyTrashed();
      $rows=$rows->get();
      $users=[];
      $randomCode=Str::random(4);
      foreach($rows as $user){
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
      }//foreach

      $response=[
        'data'=>$users
      ];
    } catch (\Exception $e) {
      $status = 500;
      $response = [
        'errors' => $e->getMessage()
      ];
    }
    return response()->json($response, $status ?? 200);
  }//index

  public function delete($id){
    try {
      \DB::beginTransaction();
      User::where('id',$id)->delete();
      $response=[
        'data'=>"",
        'msg'=>'Usuario borrado satisfactoriamente'
      ];
      \DB::commit();
    } catch (\Exception $e) {
      \DB::rollBack();
      //Message Error
      $status = 500;
      $response = [
        'errors' => $e->getMessage()
      ];
    }
    return response()->json($response, $status ?? 200);
  }//delete()

  public function restore($id){
    try {
      \DB::beginTransaction();
      User::where('id',$id)->withTrashed()->restore();
      $response=[
        'data'=>"",
        'msg'=>'Usuario restaurado satisfactoriamente'
      ];
      \DB::commit();
    } catch (\Exception $e) {
      \DB::rollBack();
      //Message Error
      $status = 500;
      $response = [
        'errors' => $e->getMessage()
      ];
    }
    return response()->json($response, $status ?? 200);
  }//delete()

  public function contact(Request $request){
  	//$mail="soporte.traya@gmail.com";
    Mail::to(['soporte.traya@gmail.com'])->send(new Contact($request->name,$request->email,$request->message));
    return response()->json(['data'=>'success'],200);
  }

  public function recoveryPassword(Request $request){
    try {
      //return Redirect::to("http://williantest.sytes.net/api/recoveryPassword");
      \DB::beginTransaction();
      $data=$request->all();
      $this->validateRequestApi(new RecoveryPasswordRequest($data));
      $randomCode=Str::random(8);
      $user=User::where('email',$request->email)->first();
      $user->password=bcrypt($randomCode);
      $user->update();
      \DB::commit();
      Mail::to([$request->email])->send(new RecoveryPasswordMail($randomCode));
      $response=[
        'data'=>'',
        'randomCode'=>$randomCode,
        'msg'=>'Se ha enviado la nueva contraseña a tu correo electrónico.'
      ];
    } catch (\Exception $e) {
      \DB::rollBack();
      $status = 500;
      $response = [
        'errors' => $e->getMessage()
      ];
    }
    return response()->json($response, $status ?? 200);
  }//recoveryPassword

  public function register(Request $request){
    try {
      $this->validateRequestApi(new RegisterFormRequest($request->all()));
      $user = new User;
      $user->email = $request->email;
      $user->name = $request->name;
      $user->password = bcrypt($request->password);
      $user->save();
      if($request->rol_id==1)
        $user->assignRole('Demandante');
      else
        $user->assignRole('Ofertante');
      $user->roles()->sync([$request->rol_id]);
      if(isset($request->image)){
        if($request->image == 'https://traya.com.ar/traya-backend/public/assets/images/generic-user.png')
        $image = "profiles/generic-user.png";
        else if($request->image)
        $image=saveImage($request->image,'profiles/'.$user->id.'.jpg');
        else
        $image="profiles/generic-user.png";
      }else
      $image="profiles/generic-user.png";
      if($image==null || !$image)
        $image="profiles/generic-user.png";

      $profile=Profile::create([
        'phone'=>$request->phone,
        'description'=>$request->description,
        'image'=>$image,
        'positive_calification'=>0,
        'negative_calification'=>0,
        'user_id'=>$user->id,
        'domicile'=>$request->domicile,
        'location_id'=>$request->location_id
      ]);
      $response=[
        'data' => $user,
        'profile'=>$profile,
        'msg'=>'Registro satisfactorio'
      ];
    } catch (\Exception $e) {
      //Message Error
      $status = 500;
      $response = [
        'errors' => $e->getMessage()
      ];
    }
    return response()->json($response, $status ?? 200);
  }//register()
  public function login(Request $request){
    $credentials = $request->only('email', 'password');
    if (!$token = JWTAuth::attempt($credentials)) {
      return response([
        'status' => 'error',
        'error' => 'invalid.credentials',
        'msg' => '¡El e-mail y/o la contraseña son incorrectas! vuelva a intentarlo...'
        //'msg' => 'Credenciales inválidas.'
      ], 400);
    }//!token
    $user=Auth::guard('api')->user() ? Auth::guard('api')->user() : Auth::user();
    // $user=User::find($user->id);
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
  }//login()

  public function dataUser($id){
    $user=\Backpack\Base\app\Models\BackpackUser::query();
    $user->with(['profile','services']);
    $user=$user->where('id',$id)->first();
    $image=null;
    $randomCode=Str::random(4);
    if(starts_with($user->profile->image, 'https://'))
      $image=$user->profile->image."?test=".$randomCode;
    else
      $image=url($user->profile->image);
    $services_text="";
    if(count($user->services)==1)
      $services_text=$user->services[0]->name;
    foreach($user->services as $service){
      if($services_text=="")
        $services_text=$service->name;
      else
        $services_text=", ".$service->name;
    }//foreach services
  return response()->json([
    'status' => 'success',
    'user' => $user,
    'image'=>$image,
    'services_text'=>$services_text,
    'roles' => User::find($user->id)->roles,
    'averageRating'=>$user->averageRating,
    'averageRatingFloat'=>(float)$user->averageRating,
    'averageRatingInt'=>(int)$user->averageRating,
    'ratingPercent'=>$user->ratingPercent(5)
  ]);

  }//dataUser()


  public function user(Request $request){
    $user=Auth::guard('api')->user() ? Auth::guard('api')->user() : Auth::user();
    $image=null;
    $randomCode=Str::random(4);
    if(starts_with($user->profile->image, 'https://'))
      $image=$user->profile->image."?test=".$randomCode;
    else
      $image=url($user->profile->image);
      $services_text="";
      if(count($user->services)==1)
        $services_text=$user->services[0]->name;
      foreach($user->services as $service){
        if($services_text=="")
          $services_text=$service->name;
        else
          $services_text=", ".$service->name;
      }//services

      $services = [];

      foreach($user->services as $userService){
        $service = Service::find($userService->service_id);
        $services[] = [
          "name" => $service->name
        ];
      }

    return response()->json([
      'status' => 'success',
      'user' => $user,
      'profile'=>$user->profile,
      'image'=>$image,
      'services'=>$user->services,
      'services_text'=>$services_text,
      'roles' => User::find($user->id)->roles,
      'averageRating'=>$user->averageRating,
      'averageRatingFloat'=>(float)$user->averageRating,
      'averageRatingInt'=>(int)$user->averageRating,
      'ratingPercent'=>$user->ratingPercent(5),
      'servicesNameArray' => $services
    ]);
  }//user()
  /**	     * Log out
  * Invalidate the token, so user cannot use it anymore
  * They have to relogin to get a new token
  *	     * @param Request $request
  */
  public function logout(Request $request) {
    $this->validate($request, ['token' => 'required']);
    try {
      JWTAuth::invalidate($request->input('token'));
      return response()->json([
        'status' => 'success',
        'msg' => 'Esperamos tu regreso, gracias por usar TRAYA.'
      ]);
    } catch (JWTException $e) {
      // something went wrong whilst attempting to encode the token
      return response()->json([
        'status' => 'error',
        'msg' => 'ups no se ha podido cerrar la sesión, por favor intente de nuevo mas tarde.'
      ]);
    }
  }//logout()
  public function refresh(){
    return response()->json([
      'status' => 'success'
    ]);
  }//refresh()

  public function update(Request $request){
    $user_id=Auth::guard('api')->user() ? Auth::guard('api')->user()->id : Auth::user()->id;
    $image=null;
    if(isset($request['image']) && $request->image){
      if (!starts_with($request->image, 'http')){
        $request['image']=saveImage($request->image,'profiles/'.$user_id.'.jpg');
        $image=url($request['image']);
      }else
        unset($request['image']);
    }
    else
      unset($request['image']);
    $dataUser=[];
    if(isset($request->name))
    $dataUser['name']=$request->name;
    if(isset($request->password)){
      if($request->password)
        $dataUser['password']=bcrypt($request->password);
    }
    $user=User::where('id',$user_id)->update($dataUser);
    $user=User::find($user_id);
    $user->is_register_completed = 1;
    $user->update();
    if(isset($request->rol_id)){
      $userR=User::find($user_id);
      if($request->rol_id==1){
        if(!$userR->hasRole('Demandante')){
          $userR->removeRole('Ofertante');
          $userR->assignRole('Demandante');
        }//role demandante
      }else if($request->rol_id == 2){
        if(!$userR->hasRole('Ofertante')){
          $userR->removeRole('Demandante');
          $userR->assignRole('Ofertante');
        }
      }//else
    }//rol_id
    $profile=Profile::where('user_id',$user->id)->update($request->only(['phone','image','description','domicile','location_id']));
    $profile=Profile::where('user_id',$user->id)->first();
    if(!$image){
      $image=url($profile->image);
    }
    if(isset($request->services)){
      ServicesUser::where('user_id',$user->id)->delete();
      foreach($request->services as $service){
        ServicesUser::create([
          'service_id'=>$service,
          'user_id'=>$user->id
        ]);
      }//foreach
    }

    return response()->json(['msg'=>'¡Felicidades! Tus datos actualizados correctamente','user'=>$user,'profile'=>$profile,'image'=>$image]);
  }//update()

  public function updateImage(Request $request){

    try{

      //dd($request->all());
      $user=User::where("id", $request->userId)->first();
      $image=null;
      
      $image =saveImage($request->image,'profiles/'.$request->userId.'.jpg');
      //return response()->json(["msg" => $image]);
      $profile = Profile::where("user_id", $request->userId)->first();
      $profile->image = $image;
      $profile->update();
      
      return response()->json(['msg'=>'¡Felicidades! Has actualizado tu imagen de perfil', "success" => true, "image" => url('/')."/".$image]);

    }catch(\Exception $e){

      return response()->json(['msg'=>'Ha ocurrido un error', "success" => false, "err" => $e->getMessage(), "ln" => $e->getLine()]);

    }
  }

  public function updateCamera(Request $request, $user_id){

    
    $target_path = "profiles/";
    
    $target_path = $target_path . basename( $_FILES['file']['name']);
    
    
    if (move_uploaded_file($_FILES['file']['tmp_name'], $target_path)) {
      rename($target_path, "profiles/".$user_id.".jpg");

      $user = Profile::where("user_id", $user_id)->first();
      $user->image = url('/')."/"."profiles/".$user_id.".jpg";
      $user->update();
      return response()->json(["user" => $user]);
      return response()->json(['msg'=>'¡Felicidades! Has actualizado tu imagen de perfil', "success" => true]);
    } else {
      return response()->json(['msg'=>'Error en el servidor', "success" => false]);
    }

    /*try{

      //dd($request->all());
      //return response()->json(["msg" => $request->all()]);
      $user=User::where("id", $user_id)->first();
      $image=null;
      
      $image =saveImage($request->image,'profiles/'.$user_id.'.jpg');
      
      $profile = Profile::where("user_id", $user_id)->first();
      $profile->image = $image;
      $profile->update();
      
      return response()->json(['msg'=>'¡Felicidades! Has actualizado tu imagen de perfil', "success" => true, "image" => url('/')."/".$image]);

    }catch(\Exception $e){

      return response()->json(['msg'=>'Ha ocurrido un error', "success" => false, "err" => $e->getMessage(), "ln" => $e->getLine()]);

    }*/
  }


  public function updateApk(Request $request){
    $versionApk=$request->versionApk;
    $versionToday=\DB::table('updatesApk')->where('id', 1)->value('version');

    if ($versionApk==$versionToday){
      $version=true;
    }else{
      $version=false;
    } 
    //$version="ole";
  
    
    return response()->json(['data'=>$version]);
  }//refresh()


  function storeLastAction(Request $request){

    $user = User::find($request->user_id);
    $user->last_login = new \DateTime;
    $user->update();

    return response()->json("success");
  }  

  function updateFCMToken(Request $request){

    if($request->deviceToken != null){
      $user = User::find($request->user_id);
      $user->device_token = $request->deviceToken;
      $user->update();

     
    }
    
    return response()->json("success");
  }

  function showForm(){

    $show = ShowForm::first();
    return response()->json(["success" => $show]);

  }

}
