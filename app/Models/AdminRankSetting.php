<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminRankSetting extends Model
{
    protected $fillable = [
        'id',
        'name',
        'threshold',
        'coins'
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'coins' => 'float',
            'threshold' => 'integer',
        ];
    }
}
