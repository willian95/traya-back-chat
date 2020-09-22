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

class LocationController extends BaseApiController
{
    public function index(){
      $locations=Location::orderBy('name','asc')->get();
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
      } catch (\Exception $e) {
        //Message Error
        $status = 500;
        $response = [
          'errors' => $e->getMessage()
        ];
      }
      return response()->json($response, $status ?? 200);
    }//store

    public function update($id,Request $request){
      try {
        $this->validateRequestApi(new UpdateLocationRequest($request->all()));
        $data=$request->all();
        unset($data['_method']);
        unset($data['token']);
        $location=Location::where('id','!=',$id)->where('name',$request->name)->first();
        if(!$location)
        Location::where('id',$id)->update($data);
        $response=[
          'msg'=>'Actualización exitosa',
        ];

        $profiles = Profile::where('location_id', $id)->get();
        $hiring = Hiring::first();

        foreach($profiles as $profile){

          /*Notification::create([
            'user_id'=>$profile->user_id,
            'text'=>"Localidad actualizada",
            'hiring_id'=>0
          ]);*/
          //if(strlen($request->description) > 0)
            //event(new \App\Events\HiringApplicant($request->description, $profile->user_id, $hiring));

        }

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
