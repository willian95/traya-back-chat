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
                    "type" => "chat",
                    "bidder_id" => $sender->id
                ])
                ->send();


            

            return response()->json(["success" => true, "message" => $message, "messageTime" => $message->created_at->format('H:i d-m-Y')]);

        }catch(\Exception $e){

            return response()->json(["success" => false, "err" => $e->getMessage(), "ln" => $e->getLine(), "msg" => "Error en el servidor"]);
        }

    }

    function fetch(Request $request){

        try{

            $lastMessage = null;
            $messageArray = [];

            if($request->lastMessage == null){
                $take = 20;
                $offset = Message::whereIn("sender_id", [$request->senderId, $request->receiverId])->whereIn("receiver_id", [$request->receiverId, $request->senderId])->count() - $take;
                $messages = Message::whereIn("sender_id", [$request->senderId, $request->receiverId])->whereIn("receiver_id", [$request->receiverId, $request->senderId])->offset($offset)->take($take)->get();
                
                $lastMessage = $messages[0]->id;

            }else{  
                $take = 20;
                $offset = Message::whereIn("sender_id", [$request->senderId, $request->receiverId])->whereIn("receiver_id", [$request->receiverId, $request->senderId])->where('id', '<', $request->lastMessage)->count() - $take;                
                $messages = Message::whereIn("sender_id", [$request->senderId, $request->receiverId])->whereIn("receiver_id", [$request->receiverId, $request->senderId])->offset($offset)->take($take)->where('id', '<', $request->lastMessage)->orderBy('id', 'desc')->get();
                
                $lastMessage = $messages[0]->id;

            }
            $hasMoreMessages = false;
            if(Message::whereIn("sender_id", [$request->senderId, $request->receiverId])->whereIn("receiver_id", [$request->receiverId, $request->senderId])->where('id', '<', $lastMessage)->count() > 0){
                $hasMoreMessages = true;
            }

            foreach($messages as $message){

                $messageArray[] = [

                    "message" => $message,
                    "time" => $message->created_at->format("H:i d/m/Y")

                ];

            }

            return response()->json(["success" => true, "messages" => $messageArray, "lastMessage" => $lastMessage, "hasMoreMessages" => $hasMoreMessages]);

        }catch(\Exception $e){
            return response()->json(["success" => false, "err" => $e->getMessage(), "ln" => $e->getLine(), "msg" => "Error en el servidor"]);
        }

    }

    function chats(Request $request){

        try{    

            $receiversArray = [];
            $sendersArray = [];

            $receivers = Message::where("sender_id", $request->userId)->orWhere("receiver_id", $request->userId)->groupBy("receiver_id")->orderBy("created_at", "desc")->select("receiver_id")->get();
            $senders = Message::where("sender_id", $request->userId)->orWhere("receiver_id", $request->userId)->groupBy("sender_id")->orderBy("created_at", "desc")->select("sender_id")->get();

            foreach($receivers as $receiver){
                array_push($receiversArray, $receiver->receiver_id);
            }

            foreach($senders as $sender){
                array_push($sendersArray, $sender->sender_id);
            }
            
            $chats = array_unique(array_merge($receiversArray, $sendersArray));
            $chats = array_diff($chats, [$request->userId]);

            $users = User::whereIn("id", $chats)->with("profile")->get();
            $messages = Message::whereIn("sender_id", $chats)->orWhereIn("receiver_id", $chats)->orderBy("created_at", "desc")->get();

            return response()->json(["success" => true, "users" => $users, "messages" => $messages]);

        }catch(\Exception $e){
            return response()->json(["success" => false, "err" => $e->getMessage(), "ln" => $e->getLine(), "msg" => "Error en el servidor"]);
        }

    }

    function deleteMessage(Request $request){

        try{

            Message::where("id", $request->id)->first()->delete();
            return response()->json(["success" => true]);

        }catch(\Exception $e){
            return response()->json(["success" => false, "err" => $e->getMessage(), "ln" => $e->getLine(), "msg" => "Error en el servidor"]);
        }

    }

    function deleteAll(Request $request){

        try{

            $receiversArray = [];
            $sendersArray = [];

            $receivers = Message::where("sender_id", $request->user_id)->orWhere("receiver_id", $request->user_id)->groupBy("receiver_id")->select("receiver_id")->get();
            $senders = Message::where("sender_id", $request->user_id)->orWhere("receiver_id", $request->user_id)->groupBy("sender_id")->select("sender_id")->get();

            /*foreach($receivers as $receiver){

                array_push($receiversArray, $receiver->receiver_id);

            }

            foreach($senders as $sender){

                array_push($sendersArray, $sender->sender_id);

            }*/

            

            //Message::whereIn("sender_id", $reques)->orWhere("receiver_id", $request->sender_id)->delete();
            //return response()->json(["success" => true]);

        }catch(\Exception $e){
            return response()->json(["success" => false, "err" => $e->getMessage(), "ln" => $e->getLine(), "msg" => "Error en el servidor"]);
        }

    }

    function deleteConversation(Request $request){

        try{

            if($request->type == "one"){
                
                Message::whereIn("sender_id", [$request->user_id, $request->receiver_id])->whereIn("receiver_id", [$request->user_id, $request->receiver_id])->delete();
                return response()->json(["success" => true]);
            
            }else if($request->type == "all"){

                

                $receiversArray = [];
                $sendersArray = [];

                $receivers = Message::where("sender_id", $request->user_id)->orWhere("receiver_id", $request->user_id)->groupBy("receiver_id")->select("receiver_id")->get();
                $senders = Message::where("sender_id", $request->user_id)->orWhere("receiver_id", $request->user_id)->groupBy("sender_id")->select("sender_id")->get();

                foreach($receivers as $receiver){

                    array_push($receiversArray, $receiver->receiver_id);

                }

                foreach($senders as $sender){

                    array_push($sendersArray, $sender->sender_id);

                }

                Message::whereIn("sender_id", $senders)->where("receiver_id", $request->user_id)->delete();
                Message::whereIn("receiver_id", $receivers)->where("sender_id", $request->user_id)->delete();
                Message::where("sender_id", $request->user_id)->orWhere("receiver_id", $request->user_id)->delete();
                //Message::whereIn("sender_id", $reques)->orWhere("receiver_id", $request->sender_id)->delete();
                return response()->json(["success" => true]);

            }

            //return response()->json($request->all());

        }catch(\Exception $e){
            return response()->json(["success" => false, "err" => $e->getMessage(), "ln" => $e->getLine(), "msg" => "Error en el servidor"]);
        }

    }

}
