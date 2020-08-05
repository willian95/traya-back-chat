<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ServicesUser extends Model
{
  protected $table="services_users";

  protected $fillable = [
    'user_id',
    'service_id',
  ];

  public function service()
  {
    return $this->belongsTo('App\Service', 'service_id');
  }
  public function user()
  {
    return $this->belongsTo('App\User', 'user_id')->withTrashed();
  }
}
