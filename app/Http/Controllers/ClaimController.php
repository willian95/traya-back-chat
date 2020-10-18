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
                    $path = saveImage($request->file,'images/'.$randomCode.'.jpg');

                    $claimImage = new ClaimImage;
                    $claimImage->claim_id = $claim->id;
                    $claimImage->image = $path;
                    $claimImage->save();
                }
            
            }else{

                

            }

            return response()->json(["success" => true, "msg" => "Reclamo realizado"]);

        }catch(\Exception $e){
            return response()->json(["success" => false, "msg" =>  "Hubo un problema", "err" => $e->getMessage(), "ln" => $e->getLine()]);
        }

    }

}
