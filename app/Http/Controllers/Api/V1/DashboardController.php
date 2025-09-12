<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\DashboardResource;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $data = DashboardResource::collectionData($request->boolean('revalidate'));

        return sendSuccessResponse('Dashboard data retrieved successfully', $data);
    }
}
