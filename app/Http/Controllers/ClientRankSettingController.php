<?php

namespace App\Http\Controllers;

use App\Http\Resources\V2\RankSettingResource;
use App\Services\Admin\AdminRankService;
use Symfony\Component\HttpFoundation\Response;

class ClientRankSettingController extends Controller
{
    public function __construct(private readonly AdminRankService $service) {}

    public function index()
    {
        $results = $this->service->all();

        return sendSuccessResponse(
            'Get all ranks',
            RankSettingResource::collection($results),
            Response::HTTP_OK
        );
    }
}
