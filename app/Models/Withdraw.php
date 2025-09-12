<?php

namespace App\Models;

use App\Enums\WithdrawStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Withdraw extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'amount', 'payment_method', 'mobile_number', 'status'];

    protected function casts(): array
    {
        return [
            'status' => WithdrawStatus::class
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
