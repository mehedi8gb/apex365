<?php

namespace App\Http\Controllers;

use App\Http\Resources\CustomerResource;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = User::query();
        $results = handleApiRequest($request, $user);

        return sendSuccessResponse('Records retrieved successfully', $results);
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

        return sendSuccessResponse('Customer created successfully', CustomerResource::make($user));
    }

    // show function to show the data
    public function show($id): JsonResponse
    {
        $user = User::find($id);

        if (! $user) {
            return sendErrorResponse('Customer not found', 404);
        }

        return sendSuccessResponse('Customer record retrieved successfully', Customer::show($user));
    }

    // update function to update the data
    public function update(Request $request, $id): JsonResponse
    {
        $user = User::find($id);

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
            'role' => 'nullable|string|in:customer,staff,admin',
        ]);

        if ($request->has('password')) {
            $validated['password'] = bcrypt($validated['password']);
        }

        if ($request->has('role')) {
            $user->syncRoles($validated['role']);
        }

        $user->update($validated);

        return sendSuccessResponse('Customer record updated successfully', new CustomerResource($user));
    }

    // destroy function to delete the data
    public function destroy($id): JsonResponse
    {
        $user = User::find($id);

        if (! $user) {
            return sendErrorResponse('Customer not found', 404);
        }

        $user->delete();

        return sendSuccessResponse('Customer record deleted successfully');
    }
}
