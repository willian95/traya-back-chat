<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Api\BaseApiController;
use App\Models\BackpackUser as User;
use App\User as User2;
use App\Profile;
use Carbon\Carbon;
use App\HiringHistory;
use App\Contacts;
use App\Location;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use \Illuminate\Support\Facades\Mail;
use PDF;

class StatisticsController extends BaseApiController
{
    
    function UsersTypeCount(Request $request){
        
        $workerRoles = 0;
        $userRoles = 0;

        $profiles= Profile::where('location_id', $request->location_id)->get();
        
        foreach($profiles as $profile){

            $user = User::find($profile->user_id);

            if($user->hasRole('Demandante')){
                $workerRoles++;
            }else if($user->hasRole('Ofertante')){
                $userRoles++;
            }
        }
        
        $usersThreeMonthsAgo = $this->countUserRegisteredThreeMonthsAgo($request->location_id);
        $contractsThreeMonthsAgo = $this->countContractsRegisteredThreeMonthsAgo($request->location_id);
        $contactsThreeMonthsAgo = $this->countContactsThreeMonthsAgo($request->location_id);

        $totalContracts = $this->totalContractsThreeMonthsAgo($request->location_id);
        $totalContacts = $this->totalContactsThreeMonthsAgo($request->location_id);

        return response()->json(["workerRoles" => $workerRoles, "userRoles" => $userRoles, "usersThreeMonthsAgo" => $usersThreeMonthsAgo, "contractsThreeMonthsAgo" => $contractsThreeMonthsAgo, "contactsThreeMonthsAgo" => $contactsThreeMonthsAgo, "totalContracts" => $totalContracts, "totalContacts" => $totalContacts]);

    }

    function countUserRegisteredThreeMonthsAgo($locationId){

        $users = [];

        $users[] = [
            "count" => Profile::whereBetween("created_at", [Carbon::now()->subDays(Carbon::now()->day - 1)->format('Y-m-d h:m:s'), Carbon::now()->format('Y-m-d h:m:s')])->where('location_id', $locationId)->count(),
            "month" => Carbon::now()->format('M')
        ];

        for($i = 1; $i <= 3; $i++){

            $userCount = Profile::whereBetween('created_at', [Carbon::now()->startOfMonth()->subMonths($i)->format('Y-m-d'), Carbon::now()->startOfMonth()->subMonths($i)->addDays(Carbon::now()->subMonths($i)->daysInMonth)->format('Y-m-d')])->where('location_id', $locationId)->count();

            $users[] = [
                "count" => $userCount,
                "month" => Carbon::now()->subMonths($i)->format('M')
            ];

        }



        return $users;
    }

    function countContractsRegisteredThreeMonthsAgo($locationId){

        $contracts = [];
        $totalContracts = 0;

        $contracts[] = [
            "count" => HiringHistory::whereBetween("hiring_histories.created_at", [Carbon::now()->subDays(Carbon::now()->day - 1)->format('Y-m-d'), Carbon::now()->format('Y-m-d')])->where('hiring_histories.status_id', 3)->join('profiles', 'profiles.user_id', '=', 'hiring_histories.user_id')->where('profiles.location_id', $locationId)->count(),
            "month" => Carbon::now()->format('M')
        ];

        for($i = 1; $i <= 3; $i++){

                $contractCount = HiringHistory::whereBetween('hiring_histories.created_at', [Carbon::now()->startOfMonth()->subMonths($i)->format('Y-m-d'), Carbon::now()->startOfMonth()->subMonths($i)->addDays(Carbon::now()->subMonths($i)->daysInMonth)->format('Y-m-d')])->where('hiring_histories.status_id', 3)->join('profiles', 'profiles.user_id', '=', 'hiring_histories.user_id')->where('profiles.location_id', $locationId)->count();

            $contracts[] = [
                "count" => $contractCount,
                "month" => Carbon::now()->subMonths($i)->format('M')
            ];

        }
        return $contracts;
    }

    function countContactsThreeMonthsAgo($locationId){

        $contacts = [];
        $totalContacts = 0;

        $contacts[] = [
            "count" => Contacts::whereBetween("contacts.created_at", [Carbon::now()->subDays(Carbon::now()->day - 1)->format('Y-m-d h:m:s'), Carbon::now()->format('Y-m-d h:m:s')])->join('profiles', 'profiles.user_id', '=', 'contacts.caller_id')->where('profiles.location_id', $locationId)->count(),
            "month" => Carbon::now()->format('M')
        ];

        for($i = 1; $i <= 3; $i++){

            $contactCount = Contacts::whereBetween('contacts.created_at', [Carbon::now()->startOfMonth()->subMonths($i)->format('Y-m-d h:m:s'), Carbon::now()->startOfMonth()->subMonths($i)->addDays(Carbon::now()->subMonths($i)->daysInMonth)->format('Y-m-d h:m:s')])->join('profiles', 'profiles.user_id', '=', 'contacts.caller_id')->where('profiles.location_id', $locationId)->count();

            $contacts[] = [
                "count" => $contactCount,
                "month" => Carbon::now()->subMonths($i)->format('M')
            ];

        }
        
        return $contacts;

    }

    function totalContractsThreeMonthsAgo($locationId){

        $totalContracts = 0;

        $totalContracts = HiringHistory::where('hiring_histories.status_id', 3)->join('profiles', 'profiles.user_id', '=', 'hiring_histories.user_id')->where('profiles.location_id', $locationId)->count();

        return $totalContracts;

    }

    function totalContactsThreeMonthsAgo($locationId){

        $totalContacts = Contacts::join('profiles', 'profiles.user_id', '=', 'contacts.caller_id')->where('profiles.location_id', $locationId)->count();

        return $totalContacts;

    }

    function UsersByLocation(Request $request){

        try {

            $take = 20;
            $skip = ($request->page - 1) * $take;

            $auth=Auth::guard('api')->user() ? Auth::guard('api')->user() : Auth::user();
            $rows=User::query();
            $rowsCount = User::query();
            //$rows=User2::query();
            $rows=$rows->with("profile","services")->skip($skip)->take($take)->whereNotIn('id',[$auth->id]);
            $rowsCount=$rowsCount->with("profile","services")->whereNotIn('id',[$auth->id]);

            $filters=isset($request->filters) ? json_decode($request->filters) : (object)[];
            if(isset($filters->trashed))
            {
              $rows->onlyTrashed();
              $rowsCount->onlyTrashed();
            }
            $rows=$rows->orderBy("id", "desc")->get();
            $rowsCount = $rowsCount->count();
            $users=[];
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

              }
            }//foreach
      
            $response=[
              'data'=>$users,
              "usersCount" => $rowsCount
            ];
          } catch (\Exception $e) {
            $status = 500;
            $response = [
              'errors' => $e->getMessage(),
              'line'=> $e->getLine()
            ];
          }
          return response()->json($response, $status ?? 200);

        //return response()->json($request->all());

    }

    function sendReport(Request $request){

      $workerRoles = 0;
      $userRoles = 0;

      $profiles= Profile::where('location_id', $request->location_id)->get();
      $location = Location::find($request->location_id);
      
      foreach($profiles as $profile){

        $user = User::find($profile->user_id);

        if($user->hasRole('Demandante')){
            $workerRoles++;
        }else if($user->hasRole('Ofertante')){
            $userRoles++;
        }

      }
      
      $usersThreeMonthsAgo = $this->countUserRegisteredThreeMonthsAgo($request->location_id);
      $contractsThreeMonthsAgo = $this->countContractsRegisteredThreeMonthsAgo($request->location_id);
      $contactsThreeMonthsAgo = $this->countContactsThreeMonthsAgo($request->location_id);

      $totalContracts = $this->totalContractsThreeMonthsAgo($request->location_id);
      $totalContacts = $this->totalContactsThreeMonthsAgo($request->location_id);

      $pdf = PDF::loadView('pdf.report',["location" => $location->name, "workerRoles" => $workerRoles, "userRoles" => $userRoles, "newUsers" => $usersThreeMonthsAgo, "contracts" => $contractsThreeMonthsAgo, "totalContracts" => $totalContracts, "contacts" => $contactsThreeMonthsAgo, "totalContacts" => $totalContacts]);
      $pdf->save('ReporteTraya.pdf');
      
      Mail::send('layouts.emails.report', [], function ($message) use($request) {
    
        $message->to($request->email)->subject('Reporte');;
        $message->attach(public_path("ReporteTraya.pdf"));
      });

      return response()->json(["msg" => "Reporte enviado al correo: ".$request->email]);

      /*return response()->json(["workerRoles" => $workerRoles, "userRoles" => $userRoles, "usersThreeMonthsAgo" => $usersThreeMonthsAgo, "contractsThreeMonthsAgo" => $contractsThreeMonthsAgo, "contactsThreeMonthsAgo" => $contactsThreeMonthsAgo, "totalContracts" => $totalContracts, "totalContacts" => $totalContacts]);*/

    }

}
