<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Http\Resources\DashboardResource;
use App\Http\Resources\V2\DashboardResourceV2;
use Illuminate\Http\Request;

class DashboardControllerV2 extends Controller
{
    public function index(Request $request)
    {
        $data = DashboardResourceV2::collectionData($request->boolean('revalidate'));

        return sendSuccessResponse('Dashboard data retrieved successfully', $data);
    }
}
