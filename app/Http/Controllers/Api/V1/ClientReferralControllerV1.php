<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\SupportTicketStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\ClientSupportTicketResourceV1;
use App\Services\V1\ReferralServiceV1;
use App\Services\V1\SupportTicketServiceV1;
use Exception;
use Illuminate\Http\Request;

class ClientReferralControllerV1 extends Controller
{
    public function __construct(protected ReferralServiceV1 $service)
    {
    }

    /**
     * @throws Exception
     */
    public function index()
    {
        $data = $this->service->getAllReferralUsersForAuthUser();

        return sendSuccessResponse('Referred profiles retrieved successfully', $data);
    }
}
