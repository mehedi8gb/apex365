<?php

namespace App\Http\Controllers;

use App\Actions\CustomerAction;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\CustomerResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /**
     * @throws \Exception
     */
    public function index(Request $request): JsonResponse
    {
        // 1. Base user query with eager loading and commissions count
        $query = User::query();

        $result = handleApiRequest($request, $query, [
            'roles',
            'account:id,user_id,balance,total_withdrawn',
            'withdraws:id,user_id,amount,status',
            'leaderboard:user_id,total_nodes,total_commissions,total_earned_coins,profile_rank',
            'theReferralCode:id,user_id,code',
        ]);

        return sendSuccessResponse('Customers retrieved successfully', $result);
    }

    // make store function to store the data
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'email' => 'nullable|email|unique:users,email|required_without:phone',
            'phone' => 'nullable|string|unique:users,phone|required_without:email',
            'nid' => 'required|string',
            'address' => 'required|string',
            'password' => 'required|string',
            'role' => 'required|string|in:customer,staff,admin',
        ]);

        $validated['password'] = bcrypt($validated['password']);
        $user = User::create($validated);
        $user->assignRole($validated['role']);

        return sendSuccessResponse('Records created successfully', CustomerResource::make($user));
    }

    // show function to show the data
    public function show($id): JsonResponse
    {
        $user = User::find($id);

        if (! $user) {
            return sendErrorResponse('Customer not found', 404);
        }

        return sendSuccessResponse('Records retrieved successfully', CustomerResource::make($user));
    }

    // update function to update the data
    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        if ($user->hasRole('admin')) {
            return sendErrorResponse('You cannot update admin user', 403);
        }

        $user = CustomerAction::handleUpdate($request->validated(), $user);

        return sendSuccessResponse('Record updated successfully', new CustomerResource($user));
    }

    // destroy function to delete the data
    public function destroy($id): JsonResponse
    {
        $user = User::find($id);

        if (! $user) {
            return sendErrorResponse('Record not found', 404);
        }

        $user->delete();

        return sendSuccessResponse('Record record deleted successfully');
    }
}
