<?php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseApiController;
use App\Service;
use App\Http\Requests\CreateServiceRequest;
use App\Http\Requests\UpdateServiceRequest;
use App\Http\Requests\DeleteServiceRequest;
class ServicesController extends BaseApiController
{

  public function index(Request $request){
    try {
      $parameters=$this->parametersUrl();
      $services=Service::query();
      $filters=isset($request->filters) ? json_decode($request->filters) : (object)[];

      if(isset($filters->name))
        $services->where('name', 'like', "%$filters->name%");
      
      $services->orderBy('name','ASC');
      $services=$services->get();
      //dd($filters);
      foreach($services as &$service){
        $usersServicePerLocality=0;
        if($service->logo){
          $service->logo=url($service->logo);
        }
        if(isset($filters->location_id)){
          $location_id=$filters->location_id;
          return response()->json("entre");
          $service2=Service::query();
          $service2->where('id',$service->id);
          $items = $service2->with('users.user.profile')->whereHas('users.user.profile', function($query) use($location_id){
            $query->where('profiles.location_id',$location_id);
          })->get();
          $countWorkers = 0;
          foreach($items as $item){

            foreach($item->users as $worker){
              if($worker->user->profile->location_id == $location_id){
                $countWorkers++;
              }
            }

            
          }

          $service2=$service2->first();
          if($service2){
            if($service2->users)
            //$service->usersPerLocality=count($service2->users);
            $service->usersPerLocality = $countWorkers;
            //$service->usersPerLocality=count();
          }else{
            $service->usersPerLocality=0;
          }
        }
        if(isset($filters->name)){
          //$location_id=$filters->location_id;
          $service2=Service::query();
          $service2->where('id',$service->id);
          $service2->with('users', 'users.user', 'user.profile');
          $service2=$service2->first();

          //dd($service2);

          if($service2){
            if($service2->users){
              foreach($item->users as $worker){
                if($worker->user->profile->location_id == $location_id){
                  $countWorkers++;
                }
              }
            }
            $service->usersPerLocality=count($service2->users);
          }else{
            $service->usersPerLocality=0;
          }
          
          /*$service2->with('users')->whereHas('users.user.profile',function($query) use($location_id){
            $query->where('location_id',$location_id);
          });
          $service2=$service2->first();
          if($service2){
            if($service2->users)
            $service->usersPerLocality=count($service2->users);
          }else{
            $service->usersPerLocality=0;
          }*/
        }
      }//foreach
      $response=[
        'data'=>$services
      ];
    } catch (\Exception $e) {
      //Message Error
      $status = 500;
      $response = [
        'errors' => $e->getTrace(),
        "ln" => $e->getLine()
      ];
    }
    return response()->json($response, $status ?? 200);
  }//index()


  public function store(Request $request){
    
    try {
      $this->validateRequestApi(new CreateServiceRequest($request->all()));
      $logo=null;
      if(isset($request->logo) && $request->logo){
        $logo=$request->logo;
        unset($request['logo']);
      }

      $service= new Service;
      $service->name = $request->name;
      $service->description = $request->description;
      $service->location_id = $request->locationId;

      $service->save();

      if($logo){
        $service->logo=saveImage($logo,'services/'.$service->id.'.jpg');
        $service->update();
      }
      $response=[
        'data'=>$service,
        'msg'=>'Servicio creado satisfactoriamente'
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

  public function update(Request $request){
    try {
      $this->validateRequestApi(new UpdateServiceRequest($request->all()));
      $logo=null;
      if(isset($request->logo) && $request->logo){
        $logo=$request->logo;
        unset($request['logo']);
      }
      Service::where('id',$request->service_id)->update([
        "name"=>$request->name,
        "description"=>$request->description,
        "location_id" => $request->locationId
      ]);
      if($logo){
        $logo=saveImage($logo,'services/'.$request->service_id.'.jpg');
        Service::where('id',$request->service_id)->update(["logo"=>$logo]);
      }
      $response=[
        'data'=>"",
        'msg'=>'Servicio actualizado satisfactoriamente'
      ];
    } catch (\Exception $e) {
      //Message Error
      $status = 500;
      $response = [
        'errors' => $e->getMessage()
      ];
    }
    return response()->json($response, $status ?? 200);
  }//update()

  public function delete($id){
    try {
      // $this->validateRequestApi(new DeleteServiceRequest($request->all()));
      Service::where('id',$id)->delete();
      $response=[
        'data'=>"",
        'msg'=>'Servicio borrado satisfactoriamente'
      ];
    } catch (\Exception $e) {
      //Message Error
      $status = 500;
      $response = [
        'errors' => $e->getMessage()
      ];
    }
    return response()->json($response, $status ?? 200);
  }//delete()
}
