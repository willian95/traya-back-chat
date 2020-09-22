<?php

namespace App\Http\Controllers;
use App\User;

use Illuminate\Http\Request;

class FCMController extends Controller
{
    
    function sendNotification(){

        fcm()
            ->to(["cojqYdb9eEc:APA91bGql5pUfB7kV4MzqmTmhpm3O1raaQPlfxxS93k5AxzwLf8mfsQMGC2Na-kUOPRcxhoGPHEtwYBOtc7culB5_IK8gyUmCZ9zcONThaG6PNDvM_ugkvFdGJrG41S3QPlcKPjXrgte"])
            ->notification([
                'title' => "test",
                'body' => "test"
            ])
            ->send();

        //$notification = 'Notificación enviada a todos los usuarios (Android).';
        return response()->json("sended");

    }

}
