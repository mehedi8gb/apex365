<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateAdminRanksRequest;
use App\Models\AdminRankSetting;
use App\Services\Admin\AdminRankService;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

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

        return sendSuccessResponse('Get all rank values', $results, Response::HTTP_OK);
    }

    public function update(UpdateAdminRanksRequest $request)
    {
        $data = $request->validated();

        $data = $this->service->updateRanks($data);

        return sendSuccessResponse('Admin rank settings updated successfully', $data, Response::HTTP_ACCEPTED);
    }

    public function delete(Request $request)
    {
        $data = $request->validate([
            'id' => 'required|exists:admin_rank_settings,id',
        ]);

        $this->service->delete($data['id']);

        return sendSuccessResponse('Admin rank settings deleted successfully', [], Response::HTTP_NO_CONTENT);
    }
}

