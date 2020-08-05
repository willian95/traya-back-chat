<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Service extends Model
{
  use SoftDeletes;
  protected $table="services";

  protected $fillable = [
    'name',
    'description',
    'logo',
  ];

  public function users()
  {
    return $this->hasMany('App\ServicesUser', 'service_id');
  }
}
