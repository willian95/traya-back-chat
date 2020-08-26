<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseApiController;
use Auth;
use App\Hiring;
use App\Profile;
use App\HiringHistory;
use App\Notification;
use App\Http\Requests\CreateHiringRequest;
use App\Http\Requests\CreateMt4HiringRequest;
use App\Http\Requests\UpdateHiringRequest;
use App\Models\BackpackUser as User;
use App\Status;//Status
use App\Contacts;
use App\ContactReview;
use Carbon\Carbon;

class HiringsController extends BaseApiController
{

    public function hiringTransformer($data){
      $hirings=[];
      foreach($data as $hiring){
        $historyStatus=[];
        $timeLastHistory="";
        foreach($hiring->history as $history){
          $historyStatus[]=[
            'user'=>$history->user->name,
            'status'=>$history->status->name,
            'status_id'=>$history->status_id,
            'comment'=>$history->comment,
            'created_at' =>$history->created_at->format('d-m-Y'),
            'created_at_time' =>$history->created_at->format('H:i:s')
          ];
          $dateLastHistory=$history->created_at->format('d-m-Y');
          $timeLastHistory=$history->created_at->format('H:i:s');
        }//foreach history statuses
        $logo=null;
        if($hiring->service->logo)
          $logo=url($hiring->service->logo);
        $bidder=\Backpack\Base\app\Models\BackpackUser::where('id',$hiring->bidder->id)->first();
        $hirings[]=[
          'id'=>$hiring->id,
          'applicant'=>[
            'id'=>$hiring->applicant->id,
            'name'=>$hiring->applicant->name,
            'phone'=>$hiring->applicant->profile->phone,
            'description'=>$hiring->applicant->profile->description,
            'image'=>url($hiring->applicant->profile->image),
            'last_sesion'=>$hiring->applicant->last_login
          ],
          'bidder'=>[
            'id'=>$hiring->bidder->id,
            'name'=>$hiring->bidder->name,
            'phone'=>$hiring->bidder->profile->phone,
            'description'=>$hiring->bidder->profile->description,
            'image'=>url($hiring->bidder->profile->image),
            'last_sesion'=>$hiring->bidder->last_login,
            'averageRating'=>$bidder->averageRating,
            'averageRatingFloat'=>(float)$bidder->averageRating,
            'averageRatingInt'=>(int)$bidder->averageRating,
            'ratingPercent'=>$bidder->ratingPercent(5)
          ],
          'service'=>$hiring->service->name,
          'service_logo'=>$logo,
          'description'=>$hiring->description,
          'status'=>$hiring->status->name,
          'status_id'=>$hiring->status->id,
          'description'=>$hiring->description,
          'history'=>$historyStatus,
          'date_last_history'=>$dateLastHistory,
          'time_last_history'=>$timeLastHistory
        ];
      }//hiring
      return $hirings;
    }//hiringTransformer()
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
      try {
        if(!isset($_GET['user_id']))
          throw new \Exception(json_encode(['User id is required']),401);
        $user_id=$_GET['user_id'];
        $hirings=Hiring::query();
        $user=User::find($user_id);
        if($user->hasRole('Demandante'))
          $hirings->where('applicant_id',$user_id);
        else
          $hirings->where('bidder_id',$user_id);
        $filters=isset($request->filters) ? json_decode($request->filters) : (object)[];
        if(isset($filters->status_id)){
          is_array($filters->status_id) ? true : $filters->status_id = [$filters->status_id];
          $hirings->whereIn('status_id',$filters->status_id);
        }
        $orderBy="DESC";
        if(isset($request->orderBy)){
          if($request->orderBy=="ASC"){
            $orderBy="ASC";
          }
        }
        $hirings->orderBy('updated_at',$orderBy);
        $hirings=$hirings->get();
        $hirings=$this->hiringTransformer($hirings);
        $response=[
          'data'=>$hirings
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

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      try {
        $data=$request->all();
        $this->validateRequestApi(new CreateHiringRequest($data));
        $user=Auth::guard('api')->user() ? Auth::guard('api')->user() : Auth::user();
        $data['applicant_id']=$user->id;
        $data['status_id']=1;//En espera de ok.
        $hiring=Hiring::create($data);

        $hiringHistory=HiringHistory::create([
          'hiring_id'=>$hiring->id,
          'status_id'=>1,
          'user_id'=>$user->id,
        ]);
        //Notificación a usuario trabajador que tiene una solicitud de servicio.
        Notification::create([
          'user_id'=>$request->bidder_id,
          'text'=>'Hola, '.$hiring->applicant->name.' quiere contratarte para el servicio '.$hiring->service->name.', está a la espera tu respuesta',
          'hiring_id'=>$hiring->id,
          'is_worker' => 1
        ]);

        $deviceToken = User::where('id', $request->bidder_id)->pluck('device_token')->toArray();

        fcm()
            ->to($deviceToken)
            ->data([
                'title' => "Atención",
                'body' => "Hola, ".$hiring->applicant->name." quiere contratarte para el servicio ".$hiring->service->name.", está a la espera tu respuesta",
                "page" => "hiring",
                "hiring_id" => $hiring->id
            ])
            ->send();
        
        /*event(new \App\Events\HiringApplicant('Hola, '.$hiring->applicant->name.' quiere contratarte para el servicio '.$hiring->service->name.', está a la espera de tu respuesta.',$request->bidder_id,$hiring));*/
        $response=[
          'msg'=>'¡Genial! lo has contratado.',
          'device_token' => $deviceToken
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

    function storeContact(Request $request){

      $user=Auth::guard('api')->user() ? Auth::guard('api')->user() : Auth::user();
      $contact = new Contacts;
      $contact->receiver_id = $request->receiver_id;
      $contact->type = $request->type;
      $contact->caller_id = $user->id;
      $contact->save();

      $contactReview = new ContactReview;
      $contactReview->question_date = Carbon::now()->addDays(3);
      $contactReview->user_id = $user->id;
      $contactReview->contact_id = $contact->id;
      $contactReview->service_id = $request->service_id;
      $contactReview->save();

    }

    /**
     * MT 4 Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

     public function mt4(Request $request){
       //Mt 4 Get service_id and array of users id
       try {
         $data=$request->all();
         $this->validateRequestApi(new CreateMt4HiringRequest($data));
         $user=Auth::guard('api')->user() ? Auth::guard('api')->user() : Auth::user();
         $users=User::query();
         $users->whereIn('id',$request->users);
         $users=$users->get();
         foreach($users as $usr){
           $hiring=Hiring::create([
             'bidder_id'=>$usr->id,
             'applicant_id'=>$user->id,
             'status_id'=>1,
             'service_id'=>$request->service_id,
             'description'=>$request->description
           ]);
           $hiringHistory=HiringHistory::create([
             'hiring_id'=>$hiring->id,
             'status_id'=>1,
             'user_id'=>$user->id
           ]);
           //Notificación a usuario trabajador que tiene una solicitud de servicio.
           Notification::create([
             'user_id'=>$usr->id,
             'text'=>'Hola, '.$hiring->applicant->name.' quiere contratarte para el servicio '.$hiring->service->name.', está a la espera tu respuesta.',
             'hiring_id'=>$hiring->id,
             'is_worker' => true
           ]);

           $deviceToken = User::where('id', $request->bidder_id)->pluck('device_token')->toArray();

           fcm()
               ->to($deviceToken)
               ->data([
                  'title' => "Atención",
                  'body' => "Hola, ".$hiring->applicant->name." quiere contratarte para el servicio ".$hiring->service->name.", está a la espera tu respuesta",
                  "page" => "hiring",
                  "hiring_id" => $hiring->id
               ])
               ->send();

           
           /*event(new \App\Events\HiringApplicant('Hola, '.$hiring->applicant->name.' quiere contratarte para el servicio '.$hiring->service->name.', está a la espera tu respuesta.',$usr->id,$hiring));
           \Log::info('User id to notification'.$usr->id);*/
         }
         $response=[
           'msg'=>'¡Genial! lo has contratado.'
         ];
       } catch (\Exception $e) {
         //Message Error
         $status = 500;
         $response = [
           //'errors' => $e->getMessage()
           'errors' => "Por favor, seleccione uno o más trabajadores"
         ];
       }
       return response()->json($response, $status ?? 200);

     }//mt4
     // public function mt4(Request $request){
     //   //Mt 4 Get service_id and store hiring to first 4 users.
     //   try {
     //     $data=$request->all();
     //     $this->validateRequestApi(new CreateMt4HiringRequest($data));
     //     $user=Auth::guard('api')->user() ? Auth::guard('api')->user() : Auth::user();
     //     $users=User::query();
     //     $users->whereHas('services', function ($query) use ($data) {
     //       $query->where('service_id',$data['service_id']);
     //     });
     //     $users->orderBy('created_at','ASC');
     //     $users->whereIn('users.id', function($query) use($data){
     //       $query->select('user_id')
     //       ->from('services_users')
     //       ->where('service_id',$data['service_id']);
     //     });
     //     $users->limit(4);
     //     $users=$users->get();
     //     foreach($users as $usr){
     //       $hiring=Hiring::create([
     //         'bidder_id'=>$usr->id,
     //         'applicant_id'=>$user->id,
     //         'status_id'=>1,
     //         'service_id'=>$request->service_id
     //       ]);
     //       $hiringHistory=HiringHistory::create([
     //         'hiring_id'=>$hiring->id,
     //         'status_id'=>1,
     //         'user_id'=>$user->id
     //       ]);
     //       //Notificación a usuario trabajador que tiene una solicitud de servicio.
     //       Notification::create([
     //         'user_id'=>$usr->id,
     //         'text'=>'Tienes una solicitud de servicio '.$hiring->service->name.' pendiente por aceptar/rechazar.',
     //         'hiring_id'=>$hiring->id
     //       ]);
     //       event(new \App\Events\HiringApplicant('Tienes una solicitud de servicio '.$hiring->service->name.' pendiente por aceptar/rechazar.',$usr->id,$hiring));
     //       \Log::info('User id to notification'.$usr->id);
     //     }
     //     $response=[
     //       'msg'=>'Contrataciones creadas exitosamente'
     //     ];
     //   } catch (\Exception $e) {
     //     //Message Error
     //     $status = 500;
     //     $response = [
     //       'errors' => $e->getMessage()
     //     ];
     //   }
     //   return response()->json($response, $status ?? 200);
     //
     // }//mt4

    /**
     * Update resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
      try {
        $data=$request->all();
        $this->validateRequestApi(new UpdateHiringRequest($data));
        $user=Auth::guard('api')->user() ? Auth::guard('api')->user() : Auth::user();
        $hiring=Hiring::where('id',$request->hiring_id)->first();
        $status=$hiring->status_id;
        if($status==5 || $status==4)
          throw new \Exception('Ups! no es posible borrar de tu historial las solicitudes canceladas y/o calificadas.');
        if($status==3 && $request->status_id==5){
          if($hiring->applicant_id==$user->id){
            //Si es applicant
            throw new \Exception('Hola, No olvides calificar el trabajo, es muy importante para la comunidad');
          }
          else{
            //Si es trabajador
            throw new \Exception('Lo sentimos, no puedes realizar esta acción.');
          }
        }
        $hiring=Hiring::where('id',$request->hiring_id)->update(['status_id'=>$request->status_id]);
        $hiring=Hiring::where('id',$request->hiring_id)->first();
        //Section notications
        if($request->status_id==2){
          //Notification: Trabajador disponible - Dirigido a: applicante (Contratante)
          Notification::create([
            'user_id'=>$hiring->applicant_id,
            'text'=>'¡Genial! El trabajador '.$hiring->bidder->name.' ha notificado que está disponible para el servicio '.$hiring->service->name.'.',
            'hiring_id'=>$hiring->id
          ]);

          $deviceToken = User::where('id', $hiring->applicant_id)->pluck('device_token')->toArray();

          fcm()
              ->to($deviceToken)
              ->data([
                  'title' => "Atención",
                  'body' => '¡Genial! El trabajador '.$hiring->bidder->name.' ha notificado que está disponible para el servicio '.$hiring->service->name.'.',
                  "page" => "hiring",
                  "hiring_id" => $hiring->id
              ])
              ->send();

          /*event(new \App\Events\HiringApplicant('¡Genial! El trabajador '.$hiring->bidder->name.' ha notificado que está disponible para el servicio '.$hiring->service->name.'.',$hiring->applicant_id,$hiring));*/
        }else if($request->status_id==3){
          //Notification: Trabajador contratado - Dirigido a: bidder (trabajador)
          Notification::create([
            'user_id'=>$hiring->bidder_id,
            'text'=>'¡Felicitaciones! Has sido contratado por '.$hiring->applicant->name.' para el servicio '.$hiring->service->name.'.',
            'hiring_id'=>$hiring->id,
            'is_worker' => true
          ]);

          $deviceToken = User::where('id', $hiring->bidder_id)->pluck('device_token')->toArray();

          fcm()
            ->to($deviceToken)
            ->data([
                'title' => "Atención",
                'body' => '¡Felicitaciones! Has sido contratado por '.$hiring->applicant->name.' para el servicio '.$hiring->service->name.'.',
                "page" => "hiring",
                "hiring_id" => $hiring->id
            ])
            ->send();

          /*event(new \App\Events\HiringApplicant('¡Felicitaciones! Has sido contratado por '.$hiring->applicant->name.' para el servicio '.$hiring->service->name.'.',$hiring->bidder_id,$hiring));*/
        }
        // else if($request->status_id==5){
        //   //Notification: Contratación cancelada - Dirigido a:
        //
        //   event(new \App\Events\HiringApplicant('Has sido contratado por '.$hiring->applicant->name.' para el servicio '.$hiring->service->name.'.',$hiring->bidder_id));
        // }
        //Section notications
        $hiringHistory=HiringHistory::create([
          'hiring_id'=>$request->hiring_id,
          'status_id'=>$request->status_id,
          'user_id'=>$user->id,
          'comment'=>isset($request->comment) ? $request->comment : ''
        ]);

        if(isset($request->calification) && isset($request->bidder_id)){
          $bidder=\Backpack\Base\app\Models\BackpackUser::find($request->bidder_id);
          $rating = new \willvincent\Rateable\Rating;
          $rating->rating = $request->calification;
          $rating->user_id = $user->id;
          // $rating->user_id = $request->bidder_id;
          $bidder->ratings()->save($rating);
          Notification::create([
            'user_id'=>$hiring->bidder_id,
            'text'=>'¡Genial! '. $hiring->applicant->name.' ha calificado tu servicio de '.$hiring->service->name.', su calificación y comentarios apareceran en tu perfil.',
            'hiring_id'=>$hiring->id,
            'is_worker' => true
          ]);

          $deviceToken = User::where('id', $hiring->bidder_id)->pluck('device_token')->toArray();

          fcm()
            ->to($deviceToken)
            ->data([
                'title' => "Atención",
                'body' => '¡Genial! '. $hiring->applicant->name.' ha calificado tu servicio de '.$hiring->service->name.', su calificación y comentarios apareceran en tu perfil.',
                "page" => "hiring",
                "hiring_id" => $hiring->id
            ])
            ->send();

          
          /*event(new \App\Events\HiringApplicant('¡Genial! '. $hiring->applicant->name.' ha calificado tu servicio de '.$hiring->service->name.', su calificación y comentarios apareceran en tu perfil.', $hiring->bidder_id,$hiring));*/
        }//calification


        $response=[
          'msg'=>'¡Felicitaciones, tu solicitud de contratación se ha actualizado!',
          'hiring'=>$hiring
        ];
        $status=(int)200;
      } catch (\Exception $e) {
        //Message Error
        $status = 500;
        $response = [
          'errors' => $e->getMessage()
        ];
      }
      return response()->json($response, $status ?? 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
          $hiring=Hiring::findOrFail($id);
          $hiring=Hiring::where('id',$id)->with('history','applicant.profile','bidder.services.service','service','status')->first();
          $historyStatus=[];
          foreach($hiring->history as $history){
            $historyStatus[]=[
              'user'=>$history->user->name,
              'status'=>$history->status->name,
              'status_id'=>$history->status_id,
              'comment'=>$history->comment,
              'created_at'=>$history->created_at->format('d-m-Y'),
              'created_at_time' =>$history->created_at->format('H:i:s')
            ];
          }//foreach history statuses
          $response=[
            'id'=>$id,
            'applicant'=>[
              'id'=>$hiring->applicant->id,
              'name'=>$hiring->applicant->name,
              'phone'=>$hiring->applicant->profile->phone,
              'address' => $hiring->applicant->profile->domicile,
              'description'=>$hiring->applicant->profile->description,
              'image'=>url($hiring->applicant->profile->image),
            ],
            'bidder'=>[
              'id'=>$hiring->bidder->id,
              'name'=>$hiring->bidder->name,
              'phone'=>$hiring->bidder->profile->phone,
              'address' => $hiring->bidder->profile->domicile,
              'description'=>$hiring->bidder->profile->description,
              'image'=>url($hiring->bidder->profile->image),
              'services'=>$hiring->bidder->services,
              'hiringCompleted'=>Hiring::where('bidder_id',$hiring->bidder->id)->where('status_id',4)->count()
            ],
            'service'=>$hiring->service->name,
            'status'=>$hiring->status->name,
            'status_id'=>$hiring->status->id,
            'description'=>$hiring->description,
            'status_id'=>$hiring->status_id,
            'history'=>$historyStatus,
            'statusesHirings'=>Status::all()
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

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function getMaps($hiring_id){

      $hiring = Hiring::find($hiring_id);
      return response()->json($hiring);

    }

    public function updateShowMap(Request $request, $hiring_id){

      try{
        $hiring = Hiring::find($hiring_id);

        if($request->rol_id == 1){
          $hiring->show_applicant_map = 1;

          $deviceToken = User::where('id', $hiring->bidder_id)->pluck('device_token')->toArray();

          fcm()
            ->to($deviceToken)
            ->data([
                'title' => "Atención",
                'body' => $hiring->applicant->name." ha permitido que accedas a ver su ubicación, en la pantalla de solicitudes particulares verás el destino en Google Maps",
                "page" => "hiring",
                "hiring_id" => $hiring->id
            ])
            ->send();

          /*event(new \App\Events\HiringApplicant($hiring->applicant->name.' ha permitido que accedas a ver su ubicación, en la pantalla de solicitudes particulares verás el destino en Google Maps',$hiring->bidder->id,$hiring));*/

        }else if($request->rol_id == 2){
          
          $hiring->show_bidder_map = 1;

          $deviceToken = User::where('id', $hiring->applicant_id)->pluck('device_token')->toArray();

          fcm()
            ->to($deviceToken)
            ->data([
                'title' => "Atención",
                'body' => $hiring->bidder->name." ha permitido que accedas a ver su ubicación, en la pantalla de solicitudes particulares verás el destino en Google Maps",
                "page" => "hiring",
                "hiring_id" => $hiring->id 
            ])
            ->send();

          /*event(new \App\Events\HiringApplicant($hiring->bidder->name.' ha permitido que accedas a ver su ubicación, en la pantalla de solicitudes particulares verás el destino en Google Maps',$hiring->applicant->id, $hiring));*/

        }

        $hiring->update();

        return response()->json(["success" => true, "msg" => "Ya puedes ser localizado"]);

      }catch(\Exception $e){

        return response()->json(["success" => false, "msg" => "Error en el servidor"]);

      }

    }

    public function getUserPosition(Request $request){

      $user = User::find($request->userId);
      return response()->json($user);

    }

    public function deleteAllHistories(Request $request){

      try{

        $hiringsArray = [];
        $hirings = Hiring::where("applicant_id", $request->user_id)->where("status_id", ">=", 4)->get();
        return response()->json($hirings);
        foreach($hirings as $hiring){
          array_push($hiringsArray, $hiring->id);
        }

        return response()->json($hiringsArray);

      }catch(\Exception $e){

        return response()->json(["success" => false, "msg" => "Error en el servidor", "err" => $e->getMessage()]);

      }

    }

    public function countActiveHiring(Request $request){

      //return response()->json("hola");

      try{

        $applicantCount = Hiring::where('applicant_id', $request->user_id)->where('status_id', '<', 4)->count();
        $bidderCount = Hiring::where('bidder_id', $request->user_id)->where('status_id', '<', 4)->count();

        return response()->json(["success" => true, "applicantCount" => $applicantCount, "bidderCount" => $bidderCount]);

      }catch(\Exception $e){

        return response()->json(["success" => false, "msg" => "Error en el servidor", "error" => $e->getMessage()]);

      }

    }

}
