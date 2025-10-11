<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\CommissionResource;
use App\Http\Resources\LeaderboardResource;
use App\Models\Commission;
use Exception;

class CommissionsControllerV1 extends Controller
{
    /**
     * @throws Exception
     */
    public function purchaseIndex()
    {
        $query = Commission::query();
        $query->where('user_id', auth()->id())->ofType('purchase');

        $result = handleApiRequest(request(), $query, ['fromUser:id,name'], CommissionResource::class);

        return sendSuccessResponse("Purchase commissions retrieved successfully", $result);
    }

    /**
     * @throws Exception
     */
    public function signupIndex()
    {
        $query = Commission::query();
        $query->where('user_id', auth()->id())
            ->where(function ($query) {
                $query->whereHas('commissionSetting', function ($q) {
                    $q->where('type', 'signup');
                })->orWhereNull('commission_type_id');
            });

        $result = handleApiRequest(request(), $query, ['fromUser:id,name'], CommissionResource::class);
        // Get history
        $history = new LeaderboardResource(auth()->user()->leaderboard);

        // Rebuild $result to insert history after meta
        $resultWithHistory = [
            'meta' => $result['meta'] ?? [],
            'history' => $history,
            'result' => $result['result'] ?? []
        ];

        return sendSuccessResponse("Signup commissions retrieved successfully", $resultWithHistory);
    }
}
