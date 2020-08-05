<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Mail\Contact;
use \Illuminate\Support\Facades\Mail;

class MailController extends Controller
{
    public function sendEmail(Request $request){
          
        Mail::to(["soporte.traya@gmail.com"])->send(new Contact("nombre: ".$request->nombre." Tel:".$request->tel, $request->email, "Mensaje: ".$request->mensaje));

        return response()->json(["message" => "Mensaje enviado", "success" => true]);

      }//recoveryPassword
}
