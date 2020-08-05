<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseApiController;
use App\Config;
class ConfigController extends BaseApiController
{

  public function index(Request $request){
    try {
      $config=Config::first();
      $response=[
        'data'=>$config
      ];
    } catch (\Exception $e) {
      //Message Error
      $status = 500;
      $response = [
        'errors' => $e->getMessage()
      ];
    }
    return response()->json($response, $status ?? 200);
  }//index()

  public function store(Request $request){
    try {
      $config=Config::first();
      if(!$config){
        $config=Config::create(["active"=>true]);
      }
      $data=[
        "active"=>true
      ];
      if(isset($request->active)){
        $data['active']=$request->active;
      }
      $config->active=$data['active'];
      $config->update();
      $response=[
        'msg'=>'Actualización exitosa'
      ];
    } catch (\Exception $e) {
      //Message Error
      $status = 500;
      $response = [
        'errors' => $e->getMessage()
      ];
    }
    return response()->json($response, $status ?? 200);

  }//store

}
