<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class HiringBidder
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */

    public $user_id;
    public $message;
    public function __construct($message,$user_id)
    {
        $this->message=$message;
        $this->user_id=$user_id;
    }
    
    public function broadcastWith()
    {
        return [
           "message"=> $this->message,
           "user_id"=>$this->user_id
        ];
    }

    public function broadcastAs()
    {
        return 'notification';
    }


   /**
    * Get the channels the event should broadcast on.
    *
    * @return \Illuminate\Broadcasting\Channel|array
    */
   public function broadcastOn()
   {
       return new Channel('notification-'.$this->user_id);
   }
}
