<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Http\Resources\V3\UserResourceV3;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class AuthControllerV2 extends Controller
{
    public function me(): JsonResponse
    {
        // Fetch the user directly with eager loading to minimize queries
        $user = User::with([
            'roles',
            'account:id,user_id,balance,total_withdrawn',
            'referredBy:referrer_id,user_id',
            'withdraws:id,user_id,amount,status',
            'leaderboard:user_id,total_nodes,total_commissions,total_earned_coins,profile_rank',
            'theReferralCode:id,user_id,code',
        ])->withCount('commissions')
            ->find(auth()->id()); // Retrieve the authenticated user by ID

        if (! $user) {
            return sendErrorResponse('User not found', 404);
        }

        return sendSuccessResponse('User details', [
            'user' => new UserResourceV3($user),
        ]);
    }
}
