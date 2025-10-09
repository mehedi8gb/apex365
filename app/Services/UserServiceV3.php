<?php

namespace App\Services;

use App\Models\Commission;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Pagination\LengthAwarePaginator;

class UserServiceV3
{
    public function getAuthenticatedUserWithRelations(): ?User
    {
        return User::with([
            'roles',
            'account:id,user_id,balance,total_withdrawn',
            'referredBy:referrer_id,user_id',
            'withdraws:id,user_id,amount,status',
            'leaderboard:user_id,total_nodes,total_commissions,total_earned_coins,profile_rank',
            'theReferralCode:id,user_id,code',
        ])->withCount('commissions')->find(auth()->id());
    }

    public function getPaginatedPurchaseCommissions(int $userId, int $page = 1): array|LengthAwarePaginator
    {
        return Commission::with('fromUser:id,name')
            ->where('user_id', $userId)
            ->ofType('purchase')
            ->latest()
            ->paginate(15, ['*'], 'purchase_commissions_page', $page);
    }

    public function getPaginatedSignupCommissions(int $userId, int $page = 1): array|LengthAwarePaginator
    {
        return Commission::with(['fromUser:id,name', 'commissionSetting:id,type'])
            ->where('user_id', $userId)
            ->where(function ($query) {
                $query->whereHas('commissionSetting', function ($q) {
                    $q->where('type', 'signup');
                })->orWhereNull('commission_type_id');
            })
            ->latest()
            ->paginate(15, ['*'], 'signup_commissions_page', $page);
    }

    public function updateAuthenticatedUser(array $data): ?User
    {
        $user = auth()->user();

        // Hash only if password is present
        if (!empty($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }

        // Update only non-null validated fields
        $filtered = array_filter($data, fn($v) => !is_null($v));
        $user->update($filtered);

        return $user->fresh();
    }

}
