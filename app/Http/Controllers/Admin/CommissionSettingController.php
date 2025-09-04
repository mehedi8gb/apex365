<?php

// app/Http/Controllers/Admin/CommissionSettingController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CommissionSetting;
use App\Models\CommissionSettingHistory;
use App\Services\Admin\CommissionService;
use Exception;
use Illuminate\Http\Request;

class CommissionSettingController extends Controller
{
    public function __construct(private CommissionService $service) {}

    /**
     * @throws Exception
     */
    public function index()
    {
        $query = CommissionSetting::query();

        $results = handleApiRequest(request(), $query);

        return sendSuccessResponse('Get all commissions values', $results);
    }

    public function update(Request $request, string $type)
    {
        $request->validate([
            'levels' => 'required|array',
        ]);

        $setting = $this->service->update($type, $request->levels);

        return sendSuccessResponse('Commission setting updated successfully', $setting);
    }

    public function delete($id, string $type)
    {
        $this->service->delete($id);
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
