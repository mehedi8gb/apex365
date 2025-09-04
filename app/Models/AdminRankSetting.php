<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminRankSetting extends Model
{
    protected $fillable = ['name', 'threshold', 'coins'];
}
