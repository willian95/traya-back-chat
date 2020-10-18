<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Claim;
use App\ClaimImage;

class ClaimController extends Controller
{
    
    function store(Request $request){

        try{

            if($request->type == 1){
                
                $claim = new Claim;
                $claim->description = $request->description;
                $claim->claim_number = Claim::count() + 1;
                $claim->save();

                foreach($request->images as $image){

                    $randomCode=Str::random(15);
                    $path = saveImage($image,'images/'.$randomCode.'.jpg');

                    $claimImage = new ClaimImage;
                    $claimImage->claim_id = $claim->id;
                    $claimImage->image = $path;
                    $claimImage->save();
                }

                $data = ["description" => $claim->description, "images" => ClaimImage::where("claim_id", $claim->id)->get()];
                $to_name = "Admin";
                $to_email = "rodriguezwillian95@gmail.com";

                \Mail::send("emails.claim", $data, function($message) use ($to_name, $to_email) {

                    $message->to($to_email, $to_name)->subject("¡Línea de reclamo!");
                    $message->from( env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
    
                });
            
                return response()->json(["msg" => "Reclamo enviado"]);
            
            }else{

                

            }

            return response()->json(["success" => true, "msg" => "Reclamo realizado"]);

        }catch(\Exception $e){
            return response()->json(["success" => false, "msg" =>  "Hubo un problema", "err" => $e->getMessage(), "ln" => $e->getLine()]);
        }

    }

}
