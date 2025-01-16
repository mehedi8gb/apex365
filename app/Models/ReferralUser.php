<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReferralUser extends Model
{
    use HasFactory;
    protected $fillable = ['referralId', 'user_id'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function referral(): BelongsTo
    {
        return $this->belongsTo(Referral::class);
    }
}
