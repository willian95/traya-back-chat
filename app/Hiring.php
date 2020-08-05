<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
class Hiring extends Model
{

  protected $table="hirings";

  protected $fillable = [
    'applicant_id',
    'bidder_id',
    'service_id',
    'status_id',
    'description',
  ];

  public function applicant()
  {
    return $this->belongsTo('App\User', 'applicant_id')->withTrashed();
  }
  public function bidder()
  {
    return $this->belongsTo('App\User', 'bidder_id')->withTrashed();
  }
  public function service()
  {
    return $this->belongsTo('App\Service', 'service_id')->withTrashed();
  }
  public function status()
  {
    return $this->belongsTo('App\Status', 'status_id');
  }
  public function history()
  {
    return $this->hasMany(HiringHistory::class);
  }

  public function latestHistory()
    {
        return $this->hasOne(HiringHistory::class)->latest();
    }
}
