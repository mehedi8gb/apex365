<?php

namespace App\Http\Controllers;

use App\Mail\OTPMail;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required|email|unique:users,email',
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:15',
            'password' => 'required|string|min:6',
            'role' => 'required|string|in:agent,student,university,staff',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'password' => Hash::make($validated['password']),
        ]);

        // Assign the role to the user
        $user->assignRole($validated['role']);

        Auth::attempt($request->only('email', 'password'));

        // Generate refresh token (optional: store securely if needed)
        $refreshToken = JWTAuth::claims(['refresh' => true])->fromUser(Auth::user());

        $data = [
            'access_token' => $refreshToken,
        ];

        return $this->sendSuccessResponse('User registered successfully', $data, 201);
    }

    /**
     * Login a user and issue tokens
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $credentials = $request->only(['email', 'password']);

        if (!$token = Auth::attempt($credentials)) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        // Generate refresh token (optional: store securely if needed)
        $refreshToken = JWTAuth::fromUser(Auth::user());

        $data = [
            'access_token' => $refreshToken,
        ];

        return $this->sendSuccessResponse('Login successful', $data);
    }

    /**
     * Forgot Password (Generate OTP)
     */
    public function forget(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $otp = rand(1000, 9999); // Generate a 4-digit OTP

        // Ideally store OTP in the database or cache
        $user = User::where('email', $validated['email'])->first();
        $user->password_reset_code = $otp; // Add an `otp` column in the `users` table
        $user->save();

        // Send OTP via email (simulated here)
        Mail::to($user->email)->send(new OTPMail($otp));

        return $this->sendSuccessResponse('OTP sent successfully');
    }

    /**
     * Validate OTP
     */
    public function validateCode(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required|email|exists:users,email',
            'otp' => 'required',
        ]);

        $user = User::where('email', $validated['email'])->first();

        if ($user->password_reset_code != $validated['otp']) {
            return response()->json(['error' => 'Invalid OTP'], 400);
        }

        $token = Str::random(60);
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $validated['email']], // Condition to check if the record already exists
            [
                'token' => $token,               // New token to insert or update
                'created_at' => now(),           // Update the timestamp to current time
            ]
        );


        $user->password_reset_code = null;
        $user->save();

        return $this->sendSuccessResponse('OTP validated successfully', ['token' => $token]);
    }

    /**
     * Reset Password
     */
    public function resetPassword(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'token' => 'required',
            'email' => 'required|email|exists:users,email',
            'newPassword' => 'required|string|min:6',
        ]);
        $user = User::where('email', $validated['email'])->first();
        $token = DB::table('password_reset_tokens')->
        where('email', $validated['email'])->where('token', $validated['token']);

        if (!$user && $token->doesntExist()) {
            return $this->sendErrorResponse('Unauthorized', 401);
        }

        if (Hash::check($validated['newPassword'], $user->password)) {
            return $this->sendErrorResponse('New password cannot be the same as the old password', 400);
        }

        if (Carbon::parse($token->first()->created_at)->diffInMinutes(now()) > 30) {
            return $this->sendErrorResponse('Token expired', 401);
        }

        $user = User::where('email', $validated['email'])->first();
        $user->password = bcrypt($validated['newPassword']);
        $user->save();

        return $this->sendSuccessResponse('Password reset successfully');
    }

    /**
     * Refresh Token
     */
    public function refresh(): JsonResponse
    {
        try {
            $newAccessToken = JWTAuth::refresh();
            return response()->json([
                'access_token' => $newAccessToken,
                'expires_in' => auth('api')->factory()->getTTL() * 60,
            ]);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Unable to refresh token'], 401);
        }
    }

    /**
     * Logout and invalidate tokens
     */
    public function logout(): JsonResponse
    {
        try {
            auth('api')->logout();
            return $this->sendSuccessResponse('User logged out successfully');
        } catch (JWTException $e) {
            return $this->sendErrorResponse('Unable to logout', 401);
        }
    }

    /**
     * Get the authenticated user
     */
    public function me(): JsonResponse
    {
        $user = auth('api')->user();
        $data = [
            'user' => $user,
            'role' => $user->getRoleNames(),
        ];
        return $this->sendSuccessResponse('User details', $data);
    }
}

