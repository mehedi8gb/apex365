<?php

namespace App\Models;

use App\Enums\EarningType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserCoin extends Model
{
    protected $fillable = [
        'user_id',
        'coins',
        'reason',
        'rank',
    ];

    protected $casts = [
        'reason' => EarningType::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
