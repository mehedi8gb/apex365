<?php

namespace App\Http\Controllers;

use App\Http\Resources\CommissionResource;
use App\Http\Resources\ReferralResource;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserDashboardController extends Controller
{
    public function getUserData(Request $request): JsonResponse
    {
        $user = Auth::user();
        if (!$user) {
            return sendErrorResponse('Unauthorized', 401);
        }

        // Fetch user's commissions
        $commissions = Transaction::where(function ($query) use ($user) {
            $query->where('referrer_level_1', $user->id)
                ->orWhere('referrer_level_2', $user->id)
                ->orWhere('referrer_level_3', $user->id);
        })->get();

        // Fetch 3 levels of referral users
        $level1Users = User::whereHas('referrals', function ($query) use ($user) {
            $query->where('referred_by', $user->id);
        })->get();

        $level2Users = User::whereHas('referrals', function ($query) use ($level1Users) {
            $query->whereIn('referred_by', $level1Users->pluck('id'));
        })->get();

        $level3Users = User::whereHas('referrals', function ($query) use ($level2Users) {
            $query->whereIn('referred_by', $level2Users->pluck('id'));
        })->get();

        return sendSuccessResponse('User dashboard data retrieved successfully', [
            'commissions' => CommissionResource::collection($commissions),
            'referrals' => [
                'level_1' => ReferralResource::collection($level1Users),
                'level_2' => ReferralResource::collection($level2Users),
                'level_3' => ReferralResource::collection($level3Users),
            ],
        ]);
    }
}
