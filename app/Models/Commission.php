<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Commission extends Model
{
    protected $fillable = [
        'user_id',
        'from_user_id',
        'amount',
        'level',
        'commission_type_id'
    ];

    /**
     * Each commission belongs to a commission setting (type).
     */
    public function commissionSetting(): BelongsTo
    {
        return $this->belongsTo(CommissionSetting::class, 'commission_type_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function fromUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }

    public function scopeOfType($query, string $type)
    {
        return $query->whereHas('commissionSetting', fn($q) => $q->where('type', $type));
    }

}
