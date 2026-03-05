<?php

namespace App\Http\Controllers\Api\V3;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateUserProfileRequest;
use App\Http\Requests\UpdateUserRequest;
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
        $user = $this->userService->getAuthenticatedUserWithRelations();

        if (!$user) {
            return sendErrorResponse('User not found', 404);
        }

        return sendSuccessResponse(
            'User details',
            new UserResourceV3($user)
        );
    }

    public function updateProfile(UpdateUserProfileRequest $request)
    {
        $data = $this->userService->updateAuthenticatedUser($request->validated());

        return sendSuccessResponse(
            'Profile updated successfully',
            new UserResourceV3($data)
        );
    }
}
