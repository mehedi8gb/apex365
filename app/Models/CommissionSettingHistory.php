<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommissionSettingHistory extends Model
{
    protected $fillable = [
        'commission_setting_id',
        'admin_id',
        'old_levels',
        'new_levels',
    ];

    protected $casts = [
        'old_levels' => 'array',
        'new_levels' => 'array',
    ];

    public function commissionSetting(): BelongsTo
    {
        return $this->belongsTo(CommissionSetting::class);
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
