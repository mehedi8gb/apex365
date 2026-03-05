<?php

// app/Http/Controllers/Api/V2/CommissionSettingController.php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Services\Admin\CommissionService;

class CommissionSettingController extends Controller
{
    public function __construct(private readonly CommissionService $service) {}

    public function index()
    {
        $structuredCommissions = $this->service->getAllStructured();

        return sendSuccessResponse('Get all commissions values', $structuredCommissions);
    }
}
