<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminRankSetting;
use App\Services\Admin\AdminRankService;
use Exception;
use Illuminate\Http\Request;

class AdminRankSettingController extends Controller
{
    public function __construct(private readonly AdminRankService $service) {}

    /**
     * @throws Exception
     */
    public function index()
    {
        $query = AdminRankSetting::query();
        $query->orderBy('threshold', 'desc');

        $results = handleApiRequest(request(), $query);;

        return sendSuccessResponse('Get all rank values', $results);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            '*.id' => 'sometimes|exists:admin_rank_settings,id',
            '*.name' => 'sometimes|string',
            '*.threshold' => 'sometimes|integer|min:1',
            '*.coins' => 'sometimes|numeric|min:0',
        ]);

        $data = $this->service->updateRanks($data);

        return sendSuccessResponse('Admin rank settings updated successfully', $data);
    }

    public function delete(Request $request)
    {
        $data = $request->validate([
            'id' => 'required|exists:admin_rank_settings,id',
        ]);

        $this->service->delete($data['id']);

        return sendSuccessResponse('Admin rank settings deleted successfully', [], 204);
    }
}

