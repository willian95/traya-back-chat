<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseApiController;
use Illuminate\Support\Collection;
use App\Favorite;
use App\Hiring;
use DB;
use App\Models\BackpackUser as User;

class FavoriteController extends BaseApiController
{
    
    function store(Request $request){

        try{

            $favorite = new Favorite;
            $favorite->auth_id = $request->auth_id;
            $favorite->user_id = $request->user_id;
            $favorite->save();

            return response()->json(["success" => true, "msg" => "¡Muy bien! Se agregó a tus Favoritos"]);

        }catch(\Exception $e){
            return reponse()->json(["success" => false, "msg" => "Error en el servidor"]);
        }

    }

    function check(Request $request){

        try{

            $favoriteCheck = false;
            if(Favorite::where("auth_id", $request->auth_id)->where("user_id", $request->user_id)->first()){
                $favoriteCheck = true;
            }

            return response()->json(["success" => true, "favoriteCheck" => $favoriteCheck]);

        }catch(\Exception $e){
            return reponse()->json(["success" => false, "msg" => "Error en el servidor", "err" => $e->getMessage(), "ln" => $e->getLine()]);
        }

    }

    function delete(Request $request){

        try{

            $favorite = Favorite::where("auth_id", $request->auth_id)->where("user_id", $request->user_id)->first();
            $favorite->delete();

            return response()->json(["success" => true, "msg" => "Favorito eliminado"]);

        }catch(\Exception $e){
            return reponse()->json(["success" => false, "msg" => "Error en el servidor", "err" => $e->getMessage(), "ln" => $e->getLine()]);
        }

    }

    function fetch(Request $request){

        try{
            $userArray = [];
            $users = Favorite::where("auth_id", $request->auth_id)->select("user_id")->get();
            foreach($users as $user){
                array_push($userArray, $user->user_id);
            }
            
            $usr = User::whereIn("id", $userArray)->with("profile", "services")->orderBy("name")->get();

            foreach($usr as &$us){

                $ratingSum = DB::table("ratings")->where("user_id", $us->id)->sum("rating");
                $ratingCount = DB::table("ratings")->where("user_id", $us->id)->count();

                if($ratingCount > 0)
                    $averageRating = ($ratingSum / $ratingCount);
                else
                    $averageRating = 0;
                

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
                  $us['image']=url($us->profile->image);
                
                //$us['completed_services']=Hiring::where('bidder_id',$us->id)->whereIn('service_id',$data['services'])->where('status_id',4)->count();
                $us['averageRating']=$us->averageRating;
                $us['averageRatingFloat']=(float)$averageRating;
                $us['averageRatingInt']=(int)$averageRating;
                $us['ratingPercent']=$us->ratingPercent(5);
                $us['services_text']=$services_text;
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

            return response()->json(["success" => true, "favorites" => $collection]);

        }catch(\Exception $e){
            return response()->json(["success" => false, "msg" => "Error en el servidor", "err" => $e->getMessage(), "ln" => $e->getLine()]);
        }

    }

}
