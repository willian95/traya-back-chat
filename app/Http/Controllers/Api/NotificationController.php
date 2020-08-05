<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Notification;
use Auth;

class NotificationController extends Controller
{
    public function notificationTransformer($collection){
      $array=[];
      foreach($collection as $data){
        $array[]=[
          'id'=>$data->id,
          'user'=>[
            'name'=>$data->user->name,
            'email'=>$data->user->email,
            'last_login'=>$data->user->last_login,
          ],
          'text'=>$data->text,
          'read'=>$data->read,
          'hiring_id'=>$data->hiring_id,
          'created_at_date'=>$data->created_at->format('d-m-Y'),
          'created_at_time'=>$data->created_at->format('H:i:s')

        ];
      }//foreach collections
      return json_decode(json_encode($array));
    }//notificationTransformer()

    public function getNotifications($id,$worker, Request $request){

      $notifications=Notification::query();
      $notifications->where('user_id',$id);
      $filters=isset($request->filters) ? json_decode($request->filters) : (object)[];
      if(isset($filters->read))
        $notifications->where('read',$filters->read);
        if($worker == "true"){
          $notifications->where('is_worker', true);
        }else{
          $notifications->where('is_worker', false);
        }
      $notifications=$notifications->orderBy('created_at','desc')->get();
      return response()->json(['data'=>$this->notificationTransformer($notifications)]);
    }//getNotifications()

    public function markRead($id){
      try {
        //Mark read notification
        $user=Auth::guard('api')->user() ? Auth::guard('api')->user() : Auth::user();
        $notification=Notification::where('id',$id)->where('user_id',$user->id)->first();
        if(!$notification)
          throw new \Exception('La notificación no existe.');
        $notification->read=1;
        $notification->update();
        $response=[
          'msg'=>'Notificación marcada como leída correctamente.'
        ];
      } catch (\Exception $e) {
        //Message Error
        $status = 500;
        $response = [
          'errors' => $e->getMessage()
        ];
      }
      return response()->json($response, $status ?? 200);
    }//updateNotification

}
