<?php
namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserShopController extends Controller
{
    // Method to update user's shop details
    public function updateShopDetails(Request $request): JsonResponse
    {
        // Validate the incoming request data
        $validated = $request->validate([
            'business_name' => 'nullable|string|max:255',
            'business_type' => 'nullable|string|max:255',
            'shop_email' => 'nullable|email|max:255',
            'shop_phone' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            'shop_address' => 'nullable|string|max:255',
            'topbar_announcement' => 'nullable|string|max:255',
            'shop_qr_code' => 'nullable|string|max:255', // QR code is optional
            'password' => 'nullable|string|min:8',
        ]);

        // Get the currently authenticated user
        $user = Auth::user();

        // Update only the provided fields (skip null values)
        $user->update(array_filter($validated, fn($value) => $value !== null));

        if (isset($validated['password'])) {
            // If password is provided, hash it and update
            $user->password = bcrypt($validated['password']);
            $user->save();
        }

        return response()->json([
            'message' => 'Shop details updated successfully',
            'data' => $user,
        ], 200);
    }

    public function getShopDetails()
    {
        // Get the currently authenticated user
        $user = Auth::user();

        return response()->json([
            'message' => 'Shop details',
            'data' => $user,
        ], 200);
    }
}
