<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\CommissionResource;
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

        return sendSuccessResponse("Commissions retrieved successfully", $result);
    }

    /**
     * @throws Exception
     */
    public function signupIndex()
    {
        $query = Commission::query();
        $query->where('user_id', auth()->id())->ofType('signup');

        $result = handleApiRequest(request(), $query, ['fromUser:id,name'], CommissionResource::class);

        return sendSuccessResponse("Commissions retrieved successfully", $result);
    }
}
