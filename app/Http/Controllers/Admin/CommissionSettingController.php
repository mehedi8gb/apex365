<?php

// app/Http/Controllers/Admin/CommissionSettingController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CommissionSettingHistory;
use App\Services\Admin\CommissionService;
use Illuminate\Http\Request;

class CommissionSettingController extends Controller
{
    public function __construct(private CommissionService $service) {}

    public function index()
    {
        return response()->json($this->service->getAll());
    }

    public function update(Request $request, string $type)
    {
        $request->validate([
            'levels' => 'required|array',
        ]);

        $setting = $this->service->update($type, $request->levels);

        return response()->json($setting);
    }

    /**
     * @throws \Exception
     */
    public function commissionsHistory()
    {
        $query = CommissionSettingHistory::query();

        $results = handleApiRequest(request(), $query, ['admin', 'commissionSetting']);;

        return sendSuccessResponse('Commission change history', $results);
    }
}

