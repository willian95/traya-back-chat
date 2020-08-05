<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
  protected $table="notifications";

  protected $fillable = [
    'user_id',
    'text',
    'read',
    'is_worker',
    'hiring_id'
  ];

  public function user()
  {
    return $this->belongsTo('App\User', 'user_id')->withTrashed();
  }
}
