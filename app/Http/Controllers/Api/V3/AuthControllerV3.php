<?php

namespace App\Http\Controllers\Api\V3;

use App\Http\Controllers\Controller;
use App\Http\Resources\V3\UserResourceV3;
use App\Services\UserServiceV3;
use Illuminate\Http\JsonResponse;

class AuthControllerV3 extends Controller
{
    public function __construct(protected UserServiceV3 $userService)
    {
    }

    public function me(): JsonResponse
    {
        $user = $this->userService->getAuthenticatedUserWithRelations(auth()->id());

        if (!$user) {
            return sendErrorResponse('User not found', 404);
        }

        return sendSuccessResponse(
            'User details',
            new UserResourceV3($user)
        );
    }
}
