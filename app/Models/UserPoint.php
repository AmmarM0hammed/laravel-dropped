<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPoint extends Model
{

    protected $fillable = [
        'user_id',
        'map_id',
        'points',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }


    public function map()
    {
        return $this->belongsTo(Map::class, 'map_id');
    }
}
