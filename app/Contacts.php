<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Contacts extends Model
{
    
    public function caller(){
        return $this->belongsTo('App\User', 'caller_id');
    }

    public function receiver(){
        return $this->belongsTo('App\User', 'receiver_id');
    }

}
