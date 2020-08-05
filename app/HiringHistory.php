<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HiringHistory extends Model
{
  protected $table="hiring_histories";

  protected $fillable = [
    'hiring_id',
    'status_id',
    'user_id',
    'comment',
  ];

  public function user()
  {
    return $this->belongsTo('App\User', 'user_id')->withTrashed();
  }
  public function status()
  {
    return $this->belongsTo('App\Status', 'status_id');
  }
}
