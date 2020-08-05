<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseApiController;
use App\Ad;

class AdsController extends BaseApiController
{
    function index(Request $request){
        
        /*$message = false;
        
        if($request->seenAds != null){

            $ads = Ad::where('location_id', $request->location_id)->whereNotIn('id', $request->seenAds)->get();
            $countAds = Ad::count();
            if(count($request->seenAds) >= $countAds){
                $message = true;
            }else{
                $message = false;
            }
        }
        else{

            $ads = Ad::where('location_id', $request->location_id)->get();

        }

        return response()->json(["ads" => $ads, "destroyStorage" =>$message]);*/

        $adsArray = null;
        $exists = 1;
        $ad = [];
        $random = 0;
        $entre = 0;
        

        if(Ad::where('location_id', $request->location_id)->count() > 0){
            if($request->upperAdWeight == 0){

                if($request->seenAds != null){
                    $adsArray = Ad::where('location_id', $request->location_id)->whereNotIn('id', $request->seenAds)->get();
    
                    if(count($adsArray) == 0){
                        $adsArray = Ad::where('location_id', $request->location_id)->get();
                    }
    
                }else{
                    $adsArray = Ad::where('location_id', $request->location_id)->get();
                }
    
                if(count($adsArray) > 1){
                    $random = rand(1, count($adsArray)-1);
                    $ad = $adsArray->values()->get($random);
                }else{
                    $ad = $adsArray->values()->get(0);
                }
                
    
            }else{
    
                if($request->seenAds != null){
                    
                    $adsArray = Ad::where('location_id', $request->location_id)->where('ad_type_id', '<=', (3 - $request->upperAdWeight))->inRandomOrder()->whereNotIn('id', $request->seenAds)->get();
                    $exists = Ad::where('location_id', $request->location_id)->where('ad_type_id', '<=', (3 - $request->upperAdWeight))->inRandomOrder()->whereNotIn('id', $request->seenAds)->count();

                    if($exists == 0){
                        $adsArray = Ad::where('location_id', $request->location_id)->inRandomOrder()->where('ad_type_id', '<=', (3 - $request->upperAdWeight))->get();
                        $exists = Ad::where('location_id', $request->location_id)->inRandomOrder()->where('ad_type_id', '<=', (3 - $request->upperAdWeight))->count();
                    }
    
                }else{
                    $adsArray = Ad::where('location_id', $request->location_id)->inRandomOrder()->where('ad_type_id', '<=', (3 - $request->upperAdWeight))->get();
                    $exists = Ad::where('location_id', $request->location_id)->inRandomOrder()->where('ad_type_id', '<=', (3 - $request->upperAdWeight))->count();
                }
    
                if($exists > 0){
                    $random = rand(0, count($adsArray)-1);
                    //if($random > -1)
                    $ad = $adsArray->values()->get($random);
                }
    
            }
        }else{
            return response()->json(['noAds' => true]);
        }
       

        return response()->json(['ads' => [$ad], 'request' => $request->all(), "exists" => $exists, "array" => $adsArray, "seen" => $request->seenAds, "fewerAds" => Ad::where('location_id', $request->location_id)->get(), ]);
        
    }
}
