<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SpinnerLeaderboard extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'spin_id',
        'rank',
        'points',
        'reward',
        'timestamp',
    ];

    protected $casts = [
        'timestamp' => 'datetime',
    ];

    // Relationship with User
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Relationship with Spinner
    public function spinner(): BelongsTo
    {
        return $this->belongsTo(Spinner::class, 'spin_id');
    }
}
