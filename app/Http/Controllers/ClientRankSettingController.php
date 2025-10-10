<?php

namespace App\Http\Controllers;

use App\Services\Admin\AdminRankService;
use Symfony\Component\HttpFoundation\Response;

class ClientRankSettingController extends Controller
{
    public function __construct(private readonly AdminRankService $service) {}

    public function index()
    {
        $results = $this->service->allAsObjects();

        return sendSuccessResponse('Get all rank values', $results, Response::HTTP_OK);
    }
}
