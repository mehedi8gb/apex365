<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class SpinnerItems extends Model
{
    use HasFactory;

    protected $fillable = ['items'];

    protected $casts = [
        'items' => 'array',
    ];
}
