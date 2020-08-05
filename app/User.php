<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Backpack\CRUD\CrudTrait; // <------------------------------- this one
use Spatie\Permission\Traits\HasRoles;// <---------------------- and this one
use Tymon\JWTAuth\Contracts\JWTSubject;
use willvincent\Rateable\Rateable;
use Illuminate\Database\Eloquent\SoftDeletes;


class User extends Authenticatable implements JWTSubject
{
  use Notifiable;
  use CrudTrait; // <----- this
  use HasRoles; // <------ and this
  use Rateable;
  use SoftDeletes;

  /**
  * The attributes that are mass assignable.
  *
  * @var array
  */
  protected $fillable = [
    'name', 'email', 'password','last_login', 'device_token', 'longitude', 'latitude'
  ];

  /**
  * The attributes that should be hidden for arrays.
  *
  * @var array
  */
  protected $hidden = [
    'provider_name', 'provider_id', 'password', 'remember_token', 'facebook_id'
  ];

  public function profile(){
    return $this->hasOne('App\Profile','user_id');
  }
  public function services(){
    return $this->hasMany('App\ServicesUser','user_id');
  }
  public function notifications(){
    return $this->hasMany('App\Notification','user_id');
  }
  public function hiringsBidder(){
    return $this->hasMany('App\Hiring','bidder_id');
  }//bidder
  public function getJWTIdentifier()	    {
    return $this->getKey();
  }
  public function getJWTCustomClaims()	    {
    return [];
  }

  public function addNew($input)
  {
      $check = static::where('facebook_id',$input['facebook_id'])->first();


      if(is_null($check)){
          return static::create($input);
      }


      return $check;
  }

  protected $casts = [
    'device_token' => 'string',
  ];

}
