<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Helpers\ReferralHelper;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasRoles;
    use HasFactory;
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'nid',
        'address',
        'points',             // points associated with the user
    ];

    public function referralCode()
    {
        return $this->hasOne(ReferralUser::class, 'user_id', 'id');
    }

    public function theReferralCode(): HasOne
    {
        return $this->hasOne(ReferralCode::class, 'user_id');
    }


    public function referralUsers(): HasMany
    {
        return $this->hasMany(ReferralUser::class);
    }

    public function referrals()
    {
        return $this->hasMany(ReferralUser::class, 'referrer_id'); // Referrals made by this user
    }


    public function leaderboard(): HasOne
    {
        return $this->hasOne(Leaderboard::class);
    }

    public function commissions(): HasMany
    {
        return $this->hasMany(Commission::class);
    }

    public function account(): HasOne
    {
        return $this->hasOne(Account::class);
    }

    public function transactionIds(): HasMany
    {
        return $this->hasMany(Transaction::class, 'userId');
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims(): array
    {
        return [
            'email' => $this->email,
            'name' => $this->name,
            'role' => $this->getRoleNames(),
        ];
    }

    public function getProfilePhotoUrlAttribute(): Application|string|\Illuminate\Contracts\Routing\UrlGenerator|null
    {
        return $this->profile_photo_path
            ? url('storage/' . $this->profile_photo_path)
            : null;
    }
}
