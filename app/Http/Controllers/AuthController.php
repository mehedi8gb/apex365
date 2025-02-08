<?php

namespace App\Http\Controllers;

use App\Helpers\ReferralHelper;
use App\Http\Resources\UserResource;
use App\Mail\OTPMail;
use App\Models\Account;
use App\Models\Commission;
use App\Models\Leaderboard;
use App\Models\Referral;
use App\Models\ReferralCode;
use App\Models\ReferralUser;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email:rfc,dns|unique:users,email|required_without:phone',
            'phone' => 'nullable|string|unique:users,phone|max:15|required_without:email',
            'password' => 'required|string|min:6',
            'nid' => 'required|string|min:10|max:17',
            'address' => 'required|string|max:255',
            'referralId' => 'required|string|exists:referral_codes,code', // Must exist in referral_codes table
        ]);

        try {
            DB::beginTransaction();

            // 1. Find the referrer user
            $referrerAndCode = ReferralCode::where('code', $validated['referralId'])->firstOrFail();

            // 3. Create new user
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'nid' => $request->nid,
                'address' => $request->address,
                'password' => Hash::make($request->password),
            ]);


            DB::commit();

            // 4. Create the referral chain and link user to referrers
            ReferralHelper::createReferralChain($user, $referrerAndCode);

            // 5. Distribute points based on the referral chain
            ReferralHelper::distributeReferralPoints();

            // 6. Update leaderboard for each referrer (based on points)
            ReferralHelper::updateLeaderboard();

            // Generate a referral code for the user
            ReferralHelper::generateReferralCode($user);

            // Assign the role to the user
            $user->assignRole('customer');

            if ($request->filled('email')) {
                $credentials = $request->only(['email', 'password']);
            } elseif ($request->filled('phone')) {
                $credentials = $request->only(['phone', 'password']);
            } else {
                return sendErrorResponse('Email or Phone is required', 422);
            }

            Auth::attempt($credentials);

            // Generate refresh token (optional: store securely if needed)
            $refreshToken = JWTAuth::claims(['refresh' => true])->fromUser(Auth::user());

            $data = [
                'transaction_id_required' => ! (bool) auth()->user()->transaction_id,
                'access_token' => $refreshToken ?? 'null',
            ];

            return sendSuccessResponse('Customer registered successfully', $data, 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Registration failed', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Login a user and issue tokens
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'nullable|email:rfc,dns|required_without:phone',
            'phone' => 'nullable|string|max:15|required_without:email',
            'password' => 'required|string',
        ]);

        if ($request->filled('email')) {
            $credentials = $request->only(['email', 'password']);
        } elseif ($request->filled('phone')) {
            $credentials = $request->only(['phone', 'password']);
        } else {
            return sendErrorResponse('Email or Phone is required', 422);
        }

        if (! Auth::attempt($credentials)) {
            return sendErrorResponse('Invalid credentials', 401);
        }

        // Generate refresh token (optional: store securely if needed)
        $refreshToken = JWTAuth::fromUser(Auth::user());

        $data = [
            'transaction_id_required' => ! (bool) auth()->user()->transaction_id,
            'access_token' => $refreshToken,
        ];

        return sendSuccessResponse('Login successful', $data);
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

        return sendSuccessResponse('OTP sent successfully');
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

        return sendSuccessResponse('OTP validated successfully', ['token' => $token]);
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

        if (! $user && $token->doesntExist()) {
            return sendErrorResponse('Unauthorized', 401);
        }

        if (Hash::check($validated['newPassword'], $user->password)) {
            return sendErrorResponse('New password cannot be the same as the old password', 400);
        }

        if (Carbon::parse($token->first()->created_at)->diffInMinutes(now()) > 30) {
            return sendErrorResponse('Token expired', 401);
        }

        $user = User::where('email', $validated['email'])->first();
        $user->password = bcrypt($validated['newPassword']);
        $user->save();

        return sendSuccessResponse('Password reset successfully');
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

            return sendSuccessResponse('User logged out successfully');
        } catch (JWTException $e) {
            return sendErrorResponse('Unable to logout', 401);
        }
    }

    /**
     * Get the authenticated user
     */
    public function me(): JsonResponse
    {
        $user = auth('api')->user();

        $data = [
            'user' => new UserResource($user),
        ];

        return sendSuccessResponse('User details', $data);
    }

    private function validatePhone($phone): false|string
    {
        try {
            $phoneUtil = PhoneNumberUtil::getInstance();
            $numberProto = $phoneUtil->parse($phone, 'BD'); // BD = Bangladesh
            if ($phoneUtil->isValidNumber($numberProto)) {
                self::$phone = $phoneUtil->format($numberProto, PhoneNumberFormat::E164); // +8801XXXXXXXXX

                return true;
            }

            return false;
        } catch (NumberParseException $e) {
            return false;
        }
    }
}
