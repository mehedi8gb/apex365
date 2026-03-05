<?php

namespace App\Models;

class CommissionSetting extends Model
{
    protected $fillable = ['type', 'levels'];
    protected $casts = [
        'levels' => 'array', // auto-cast JSON to array
    ];
}
