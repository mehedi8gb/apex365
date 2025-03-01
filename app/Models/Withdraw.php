<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Withdraw extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'amount', 'payment_method', 'mobile_number', 'status'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
