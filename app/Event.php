<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    //
    protected $fillable = [
        'owner_id',
        'name',
        'start',
        'end'
    ];

    public function users()
    {
        return $this->belongsToMany('App\User','statuses','event_id','user_id')->withPivot('status')->withTimestamps();
    }
}
