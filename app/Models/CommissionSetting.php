<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommissionSetting extends Model
{
    protected $fillable = ['type', 'levels'];
    protected $casts = [
        'levels' => 'array', // auto-cast JSON to array
    ];
}
