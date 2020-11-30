<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseApiController;
use App\ServicesUser;
use App\Hiring;
use App\Http\Requests\CreateServicesUserRequest;
use App\Http\Requests\GetServicesUserRequest;
use DB;
use App\Models\BackpackUser as User;
use Illuminate\Support\Collection;
use Auth;
use Carbon\Carbon;

class ServicesUserController extends BaseApiController
{
  public function store(Request $request){
    try {
      DB::beginTransaction();
      $data=$request->all();
      $user=Auth::guard('api')->user() ? Auth::guard('api')->user() : Auth::user();
      $user=User::find($user->id);
       ServicesUser::where('user_id',$user->id)->delete();
      foreach($data['services'] as $service){
        // $this->validateRequestApi(new CreateServicesUserRequest($service));
       
        ServicesUser::firstOrCreate([
          'service_id'=>$service,
          'user_id'=>$user->id
        ]);
      }//foreach
      if(isset($request->description))
        $profile=\App\Profile::where('user_id',$user->id)->update(["description"=>$request->description]);
      $response=[
        'data'=>'',
        'msg'=>'Servicio(s) asociados correctamente'
      ];
      DB::commit();
    } catch (\Exception $e) {
      //Message Error
      DB::rollBack();
      $status = 500;
      $response = [
        'errors' => $e->getMessage()
      ];
    }
    return response()->json($response, $status ?? 200);

  }//store

  public function users(Request $request){
    try {
      $data=$request->all();

      if(isset($data['services']))
      is_array($data['services']) ? true : $data['services'] = [$data['services']];
      $this->validateRequestApi(new GetServicesUserRequest($data));

      //Get users of service
      $usr=\Backpack\Base\app\Models\BackpackUser::query();
      $usr->whereHas('services', function ($query) use ($data) {
        $query->whereIn('service_id',$data['services']);
      });
      if(isset($data['location_id'])){
        $usr->whereHas('profile', function ($query) use ($data) {
          $query->where('location_id',$data['location_id']);
        });
      }
      $filters=isset($request->filters) ? json_decode($request->filters) : (object)[];
      if(isset($filters->name))
        $usr->where('name', 'like', "%$filters->name%");
      $usr->orderBy('name','ASC');
      $usr=$usr->with(['profile'])->get();
      //$usr=$usr->with(['profile'])->toSql();

      
      
      foreach($usr as &$us){
        $services_text="";
        if(count($us->services)==1)
          $services_text=$us->services[0]->name;
        foreach($us->services as $service){
          if($services_text=="")
            $services_text=$service->service->name;
          else
            $services_text=", ".$service->service->name;
        }//services
        if($us->profile->image)

          $lastLogin = "";
          if($us->last_login->lt(Carbon::now()->subDays(7))){
            $lastLogin = "Hace más de una semana";
          }else{
            $lastLogin = $us->last_login;
          }

          $us['image']=url($us->profile->image);
        $us['completed_services']=Hiring::where('bidder_id',$us->id)->whereIn('service_id',$data['services'])->where('status_id',4)->count();
        $us['averageRating']=$us->averageRating;
        $us['averageRatingFloat']=(float)$us->averageRating;
        $us['averageRatingInt']=(int)$us->averageRating;
        $us['ratingPercent']=$us->ratingPercent(5);
        $us['services_text']=$services_text;
        $us['last_login']= $lastLogin;
        $comments=Hiring::where('bidder_id',$us->id)->where('status_id',4)->with('latestHistory')->get();
        $arrayComments=[];
        foreach($comments as $com){
          $arrayComments[]=[
            'userName'=>$com->latestHistory->user->name,
            'userEmail'=>$com->latestHistory->user->email,
            'comment'=>$com->latestHistory->comment,
            'created_at_date'=>$com->latestHistory->created_at->format('d-m-Y'),
            'created_at_time'=>$com->latestHistory->created_at->format('H:i:s'),
          ];
        }
        $us['comments']=$arrayComments;
      }

      $collection = new Collection($usr);
      $max = 0;
      $min = 0;
      $i = 0;

       $sorted = $collection->sortByDesc('completed_services');
      //$sorted = $collection->sortBy('name');

      //$response=['data'=>$sorted->values()->all()];
      $response=['data'=>$sorted->values()->all()];
    } catch (\Exception $e) {
      $status = 500;
      $response = [
        'errors' => $e->getMessage()
      ];
    }//catch
    return response()->json($response, $status ?? 200);
  }//users()
}
