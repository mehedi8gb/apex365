<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminRankSetting;
use App\Services\Admin\AdminRankService;
use Illuminate\Http\Request;

class AdminRankSettingController extends Controller
{
    public function __construct(private readonly AdminRankService $service) {}

    /**
     * @throws \Exception
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
            '*.name' => 'required|string',
            '*.threshold' => 'required|integer|min:1',
            '*.coins' => 'required|numeric|min:0',
        ]);

        $this->service->updateRanks($data);

        return response()->json(['message' => 'Admin rank settings updated successfully']);
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

