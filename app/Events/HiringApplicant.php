<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class HiringApplicant implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels,Dispatchable;

    /**
     * Create a new event instance.
     *
     * @return void
     */
     public $user_id;
     public $message;
     public $entity;
     public function __construct($message,$user_id,$entity)
     {
         $this->message=$message;
         $this->user_id=$user_id;
         $this->entity=$entity;
     }

     public function broadcastWith()
     {
         return [
            "message"=> $this->message,
             "user_id"=>$this->user_id,
             "hiring"=>$this->entity
         ];
     }

     public function broadcastAs()
     {
         return 'notificationUser';
     }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return ['notification-'.$this->user_id];
    }
}
