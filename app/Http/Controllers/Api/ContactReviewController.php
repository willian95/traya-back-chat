<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseApiController;
use App\ContactReview;
use App\Contacts;
use Carbon\Carbon;
use App\Hiring;
use App\Service;
use App\HiringHistory;


class ContactReviewController extends BaseApiController
{
    
    function checkContactReview(Request $request){
        
        try{

            $askQuestion = false;
            $userReceiver = null;
            $service = null;

            $contactReview = ContactReview::where("user_id", $request->user_id)->where("question_date", "<=", Carbon::now())->where("first_question_answer", null)->first();
            $service = Service::where("id", $contactReview->service_id)->first();
            $contact = Contacts::with("receiver")->where("id", $contactReview->contact_id)->first();

            if($contactReview){
                $userReceiver = $contact->receiver;
                $askQuestion = true;
            }

            return response()->json(["success" => true, "askQuestion" => $askQuestion, "userReceiver" => $userReceiver, "contactReview" => $contactReview, "service" => $service]);

        }catch(\Exception $e){

            return response()->json(["success" => false, "msg" => "Error en el servidor", "err" => $e->getMessage(), "ln" => $e->getLine()]);

        }

    }

    function answerFirstQuestion(Request $request){

        try{

            $contactReview = ContactReview::where("id", $request->contact_review_id)->first();
            $contactReview->first_question_answer = $request->answer;
            $contactReview->update();

            return response()->json(["success" => true, "showNextQuestion" => $request->answer]);

        }catch(\Exception $e){
            return response()->json(["success" => false, "msg" => "Error en el servidor", "err" => $e->getMessage(), "ln" => $e->getLine()]);
        }

    }

    function answerSecondQuestion(Request $request){

        try{
            $hiring = null;
            $contactReview = ContactReview::where("id", $request->contact_review_id)->first();
            $contactReview->second_question_answer = $request->answer;
            $contactReview->update();

            $contact = Contacts::where("id", $contactReview->contact_id)->first();

            if($request->answer == true){
                
                $hiring = new Hiring;
                $hiring->applicant_id = $contactReview->user_id;
                $hiring->bidder_id = $contact->receiver_id;
                $hiring->service_id = $contactReview->service_id;
                $hiring->status_id = 3;
                $hiring->save();

                $history = new HiringHistory;
                $history->hiring_id = $hiring->id;
                $history->status_id = 1;
                $history->user_id = $hiring->applicant_id;
                $history->save();

                $history = new HiringHistory;
                $history->hiring_id = $hiring->id;
                $history->status_id = 2;
                $history->user_id = $hiring->bidder_id;
                $history->save();

                $history = new HiringHistory;
                $history->hiring_id = $hiring->id;
                $history->status_id = 3;
                $history->user_id = $hiring->applicant_id;
                $history->save();

            }

            return response()->json(["success" => true, "hiring" => $hiring]);

        }catch(\Exception $e){
            return response()->json(["success" => false, "msg" => "Error en el servidor", "err" => $e->getMessage(), "ln" => $e->getLine()]);
        }

    }

}
