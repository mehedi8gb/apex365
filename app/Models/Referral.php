<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Referral extends Model
{
    use HasFactory;
    protected $fillable = ['referralId'];

    public static function generateReferralCode(): string
    {
        return Str::uuid();
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'referral_user');
    }
}
