<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
  protected $table="profiles";

  protected $fillable = [
    'phone',
    'description',
    'image',
    'positive_calification',
    'negative_calification',
    'user_id',
    'location_id',
    'domicile'
  ];

  public function location()
  {
    return $this->belongsTo('App\Location', 'location_id');
  }

}
