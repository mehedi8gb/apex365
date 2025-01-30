<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReferralUser extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'referrer_id', 'referral_code_id', 'level'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function referrer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referrer_id');
    }
}
