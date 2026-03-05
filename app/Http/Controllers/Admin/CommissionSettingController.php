<?php

// app/Http/Controllers/Admin/CommissionSettingController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\CommissionSettingHistoryResource;
use App\Http\Resources\CommissionSettingResource;
use App\Models\CommissionSetting;
use App\Models\CommissionSettingHistory;
use App\Services\Admin\CommissionService;
use Exception;
use Illuminate\Http\Request;
use Throwable;

class CommissionSettingController extends Controller
{
    public function __construct(private readonly CommissionService $service) {}

    /**
     * @throws Exception
     */
    public function index()
    {
        $query = CommissionSetting::query();

        $results = handleApiRequest(request(), $query);

        return sendSuccessResponse('Get all commissions values', $results);
    }

    /**
     * @throws Throwable
     */
    public function update(Request $request, string $type)
    {
        $request->validate([
            'levels' => 'required|array',
        ]);

        $setting = $this->service->update($type, $request->levels);

        return sendSuccessResponse('Commission setting updated successfully', CommissionSettingResource::make($setting));
    }

    /**
     * @throws Exception
     */
    public function commissionsHistory()
    {
        $query = CommissionSettingHistory::query();

        $results = handleApiRequest(request(), $query, ['admin', 'commissionSetting']);

        return sendSuccessResponse('Commission change history', $results);
    }
}
