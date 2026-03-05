<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Leaderboard extends Model
{
    protected $fillable = [
        'user_id',
        'total_commissions',
        'total_nodes',
        'total_earned_coins',
        'profile_rank',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
