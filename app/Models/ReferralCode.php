<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class ReferralCode extends Model
{
    use HasFactory;

    protected $fillable = ['code', 'is_active', 'metadata'];

//    public function user()
//    {
//        return $this->belongsTo(User::class, 'assigned_to');
//    }
}
