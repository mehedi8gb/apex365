<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReferralCode extends Model
{
    use HasFactory;

    protected $fillable = ['code', 'user_id', 'type'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
