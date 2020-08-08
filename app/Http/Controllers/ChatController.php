<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\MessageStoreRequest;
use App\User;
use App\Message;

class ChatController extends Controller
{
    function store(MessageStoreRequest $request){

        try{

            $message = new Message;
            $message->message = $request->message;
            $message->sender_id = $request->senderId;
            $message->receiver_id = $request->receiverId;
            $message->save();

            $sender = User::where("id", $request->senderId)->first();
            $receiver = User::where("id", $request->receiverId)->first();
            $deviceToken = User::where('id', $request->receiverId)->pluck('device_token')->toArray();

            fcm()
                ->to($deviceToken)
                ->data([
                    'title' => "Atención",
                    'body' => $sender->name." te ha enviado un mensaje",
                    //"page" => "",
                    //"hiring_id" => $hiring->id
                ])
                ->send();

            return response()->json(["success" => true]);

        }catch(\Exception $e){

            return response()->json(["success" => false, "err" => $e->getMessage(), "ln" => $e->getLine(), "msg" => "Error en el servidor"]);
        }

    }

    function fetch(Request $request){

        try{

            $lastMessage = null;

            if($request->lastMessage == null){
                $take = 5;
                $offset = Message::whereIn("sender_id", [$request->senderId, $request->receiverId])->whereIn("receiver_id", [$request->receiverId, $request->senderId])->count() - $take;
                $messages = Message::whereIn("sender_id", [$request->senderId, $request->receiverId])->whereIn("receiver_id", [$request->receiverId, $request->senderId])->offset($offset)->take($take)->get();
                
                $lastMessage = $messages[0]->id;

            }else{  
                
                $messages = Message::whereIn("sender_id", [$request->senderId, $request->receiverId])->whereIn("receiver_id", [$request->receiverId, $request->senderId])->take(5)->where('id', '<', $lastMessage)->get();
                
                $lastMessage = $messages[0]->id;

            }

            return response()->json(["success" => true, "messages" => $messages, "lastMessage" => $lastMessage]);

        }catch(\Exception $e){
            return response()->json(["success" => false, "err" => $e->getMessage(), "ln" => $e->getLine(), "msg" => "Error en el servidor"]);
        }

    }

}
