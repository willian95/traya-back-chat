<?php

namespace App\Http\Controllers;
use App\User;

use Illuminate\Http\Request;

class FCMController extends Controller
{
    
    function sendNotification(){

        fcm()
            ->to(["fNPJIEAX8t4:APA91bHimxfVWK2qP4GuNHvwN34l0Y-NlFTuwBIzyVkRlwlwFDTLIWZu1vsIttb7_qmBTirAJWBICngF954EiQJvb88UXrPovm6tm7r5BPfJhV2JAgb-cMlFvbF2v4GpSOMxsMxv_jbh", "fIi6NB2iza0:APA91bHxswZh58kWO8h-THbxVMq5TQqr6gSuJmvZlkc5romVCEWQqXj1VBFBxlJzEsjoSaEtDTl9XH8ioUlzMlHNLAzTtEMrkQteDCRAFkXhvlVFSq-MAjD80cL6V6HDScQlEcR6ptKs"])
            ->notification([
                'title' => "test",
                'body' => "test"
            ])
            ->send();

        //$notification = 'Notificación enviada a todos los usuarios (Android).';
        return response()->json("sended");

    }

}
