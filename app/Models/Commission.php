<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Commission extends Model
{
    protected $fillable = ['user_id', 'from_user_id', 'amount', 'level'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function fromUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }
}
