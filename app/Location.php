<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Location extends Model
{
  use SoftDeletes;
  protected $table="locations";

  protected $fillable = [
    'name',
    'description',
  ];

  function claimLocality(){
    return $this->hasOne(ClaimLocality::class);
  }

}
