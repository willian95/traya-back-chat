<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ad extends Model
{

    use SoftDeletes;

    public function adType(){
        return $this->belongsTo('App\AdType', 'ad_type_id');
    }
}
