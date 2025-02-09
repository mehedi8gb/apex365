<?php

namespace App\Models;

class Spinner extends Model
{
    protected $fillable = [
        'rotation_point',
        'spin_time',
    ];

    protected $casts = [
       'spin_time' => 'datetime',
    ];
}
