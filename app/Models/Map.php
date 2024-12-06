<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Map extends Model
{
    protected $guarded;


    public function userPoints()
    {
        return $this->hasMany(UserPoint::class, 'map_id');
    }
}
