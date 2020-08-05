<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AdType extends Model
{
    public function ad(){
        return $this->hasMany('App\Ad', 'ad_type_id');
    }
}
