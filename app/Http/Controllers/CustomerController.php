<?php

namespace App\Http\Controllers;

use App\Http\Resources\CustomerResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = User::role('customer');
        $results = handleApiRequest($request, $user);

        return sendSuccessResponse('Customer records retrieved successfully', $results);
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
        ]);

        $validated['password'] = bcrypt($validated['password']);
        $user = User::create($validated);
        $user->assignRole('customer');

        return sendSuccessResponse('Customer created successfully', CustomerResource::make($user));
    }

    // show function to show the data
    public function show($id): JsonResponse
    {
        $user = User::role('customer')->find($id);

        if (! $user) {
            return sendErrorResponse('Customer not found', 404);
        }

        return sendSuccessResponse('Customer record retrieved successfully', new CustomerResource($user));
    }

    // update function to update the data
    public function update(Request $request, $id): JsonResponse
    {
        $user = User::role('customer')->find($id);

        if (! $user) {
            return sendErrorResponse('Customer not found', 404);
        }

        $validated = $request->validate([
            'name' => 'nullable|string',
            'email' => 'nullable|email|unique:users,email,'.$user->id,
            'phone' => 'nullable|unique:users,phone'.$user->id,
            'nid' => 'nullable|string',
            'address' => 'nullable|string',
            'password' => 'nullable|string',
        ]);

        if ($request->has('password')) {
            $validated['password'] = bcrypt($validated['password']);
        }

        $user->update($validated);

        return sendSuccessResponse('Customer record updated successfully', new CustomerResource($user));
    }

    // destroy function to delete the data
    public function destroy($id): JsonResponse
    {
        $user = User::role('customer')->find($id);

        if (! $user) {
            return sendErrorResponse('Customer not found', 404);
        }

        $user->delete();

        return sendSuccessResponse('Customer record deleted successfully');
    }
}
