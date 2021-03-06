<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Claim;
use App\ClaimImage;
use App\ClaimLocality;

class ClaimController extends Controller
{
    
    function store(Request $request){

        try{

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

            $claimLocality = ClaimLocality::where("location_id", $request->locality)->firstOrFail();

            if($claimLocality){
                $data = ["description" => $claim->description, "images" => ClaimImage::where("claim_id", $claim->id)->get(), "name" => $request->name, "phone" => $request->phone, "email" => $request->email, "domicile" => $request->domicile];
                
                if($request->type == 1){
                    $title = "Reclamo";
                }else{
                    $title = "Sugerencia";
                }

                $sanitizeEmails = str_replace(" ", "", $claimLocality->emails);

                foreach(explode(",", $sanitizeEmails) as $email){
                    if($email != ""){
                        $to_name = "Admin";
                        $to_email = $email;
                        
                        \Mail::send("emails.claim", $data, function($message) use ($to_name, $to_email, $title) {

                            $message->to($to_email, $to_name)->subject("¡".$title."!");
                            $message->from( env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));

                        });
                    }
                }
            
                return response()->json(["success" => true, "msg" => "Reclamo enviado", "test" => explode(",", $sanitizeEmails)]);
            }

            
    

        }catch(\Exception $e){
            return response()->json(["success" => false, "msg" =>  "Hubo un problema", "err" => $e->getMessage(), "ln" => $e->getLine()]);
        }

    }

    function adminFetchLocations(){

        $locations = ClaimLocality::with("location")->get();

        return response()->json(["locations" => $locations]);

    }

    function update(Request $request){

        $location = ClaimLocality::where("id", $request->id)->first();
        $location->emails = $request->emails;
        $location->active = 1;
        $location->update();

        return response()->json(["success" => true, "msg" => "Línea de reclamos actualizada"]);

    }

    function deactivate(Request $request){

        $location = ClaimLocality::where("id", $request->id)->first();
        $location->active = 0;
        $location->update();

        return response()->json(["success" => true, "msg" => "Línea de reclamos desactivada"]);

    }

    function getClaimNumber(){
        
        return response()->json(["number" => str_pad(Claim::count() + 1,4,"0", STR_PAD_LEFT)]);

    }

}
