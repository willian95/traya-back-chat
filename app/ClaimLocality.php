<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ClaimLocality extends Model
{
    function location(){
        return $this->belongsTo(Location::class);
    }
}
