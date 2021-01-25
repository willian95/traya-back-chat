<?php

namespace App\Http\Controllers\Api;
use App\Location;
use App\Notification;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseApiController;
// use App\Http\Controllers\Controller;
use Validator;
use App\Profile;
use App\Hiring;
use App\Http\Requests\CreateLocationRequest;
use App\Http\Requests\UpdateLocationRequest;
use App\ClaimLocality;
use App\User;

class LocationController extends BaseApiController
{
    public function index(){
      $locations=Location::orderBy('name','desc')->get();
      return response()->json(['data'=>$locations],200);
    }//index()

    public function store(Request $request){
      try {
        $this->validateRequestApi(new CreateLocationRequest($request->all()));
        $data=$request->all();
        $location=Location::firstOrCreate([
          'name'=>$request->name
        ],$data);
        $response=[
          'data' => $location,
          'msg'=>'Registro satisfactorio'
        ];
        
        $claim = new ClaimLocality;
        $claim->location_id = $location->id;
        $claim->save();

      } catch (\Exception $e) {
        //Message Error
        $status = 500;
        $response = [
          'errors' => $e->getMessage()
        ];
      }
      return response()->json($response, $status ?? 200);
    }//store

    public function updateLocation($id,Request $request){
      try {
         
        $this->validateRequestApi(new UpdateLocationRequest($request->all()));
        $data=$request->all();
        unset($data['_method']);
        unset($data['token']);
        $location=Location::where('id','!=',$id)->where('name',$request->name)->first();
        if(!$location)
        Location::where('id',$id)->update($data);
        

        $user_array_id = [];
        $profiles = Profile::where('location_id', $id)->get();

        foreach($profiles as $profile){

          array_push($user_array_id, $profile->user_id);

        }

        //return response()->json(User::where("id", 437)->first());

        $devicesArray=[];

        $devices = User::whereIn("id", $user_array_id)->whereNotNull("device_token")->get();

        
        foreach($devices as $device){

          array_push($devicesArray, $device->device_token);

        }
       
        fcm()
            ->to($devicesArray)
            ->notification([
              'title' => "📍Atención Comunidad Traya",
              'body' => $request->description,
            ])
            ->send();


            $response=[
              'msg'=>'Actualización exitosa',
              "devices" => $devicesArray,
              "users" => $devices,
              "profiles" => $profiles,
              "userArray" => $user_array_id
            ];

      } catch (\Exception $e) {
        //Message Error
        $status = 500;
        $response = [
          'errors' => $e->getMessage()
        ];
      }
      return response()->json($response, $status ?? 200);
    }//update

    public function delete($id){
      try {
        $location=Location::where('id',$id)->delete();
        $response=[
          'msg'=>'Localización eliminada exitosamente'
        ];
      } catch (\Exception $e) {
        //Message Error
        $status = 500;
        $response = [
          'errors' => $e->getMessage()
        ];
      }
      return response()->json($response, $status ?? 200);
    }

    public function find($id){

      try{

        $location=Location::find($id);
        $response=[
          'data'=>$location
        ];

      }catch(\Exception $e){
        $status = 500;
        $response = [
          'errors' => $e->getMessage()
        ];
      }

      return response()->json($response, $status ?? 200);

    }

}
