<?php

namespace App\Http\Controllers;

use App\Http\Resources\CommissionResource;
use App\Http\Resources\ReferralResource;
use App\Http\Resources\TransactionResource;
use App\Http\Resources\UserResource;
use App\Mail\OTPMail;
use App\Models\Account;
use App\Models\Commission;
use App\Models\Leaderboard;
use App\Models\Referral;
use App\Models\ReferralCode;
use App\Models\ReferralUser;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Log;
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

            // 1. Find the referral code
            $referralCode = ReferralCode::where('code', $validated['referralId'])->firstOrFail();

            // 2. Get the referrer based on the code
            $referrer = User::find($referralCode->user_id); // Admin or user

            // 3. Create new user
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'nid' => $request->nid,
                'address' => $request->address,
                'password' => Hash::make($request->password),
            ]);

            Account::create([
                'user_id' => $user->id,
                'balance' => 1000.00,
            ]);

            // 4. Create the referral chain and link user to referrers
            $referralUsers = $this->createReferralChain($user, $referralCode, $referrer);

            // 5. Distribute points based on the referral chain
            $this->distributeReferralPoints($user, $referralUsers);

            // 6. Update leaderboard for each referrer (based on points)
            $this->updateLeaderboard($user, $referralUsers);

            $newReferralCode = $this->generateReferralCode($user);

            DB::commit();

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
                'transaction_id_required' => !(bool)auth()->user()->transaction_id,
                'access_token' => $refreshToken ?? 'null'
            ];

            return sendSuccessResponse('Customer registered successfully', $data, 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Registration failed', 'message' => $e->getMessage()], 500);
        }
    }

    public function createReferralChain(User $user, $referralCode, $referrer = null): array
    {
        $referralUsers = [];
        $currentReferrer = $referrer ?? User::find(1); // If no referrer, assign Admin (ID 1)
        $level = 1;

        while ($currentReferrer && $level <= 4) {
            // Ensure each referral entry is stored uniquely for the user
            $referralUsers[$level] = ReferralUser::create([
                'user_id' => $user->id,         // New user
                'referrer_id' => $currentReferrer->id, // Who referred this user
                'referral_code_id' => $referralCode->id,
                'level' => $level,
            ]);

            // Move to the next referrer (up the chain)
            $currentReferrer = $currentReferrer->referrer;
            $level++;
        }
        return $referralUsers;
    }



    public function distributeReferralPoints(User $user, array $referralUsers): array
    {
        // Points distribution per level
        $commissionAmounts = [30, 20, 10, 5];
        $commissions = [];
        $currentReferrer = $referralUsers[1];
        $level = $currentReferrer->level;

        while ($currentReferrer && $level <= 4) {

                $commissions[$level] = Commission::create([
                    'user_id' => $user->id, // User who triggered the commission
                    'from_user_id' => $currentReferrer->id,  // Referrer who gets the points
                    'level' => $level,
                    'amount' => $commissionAmounts[$level - 1],
                ]);
            $currentReferrer = $currentReferrer->referrer;
            $level++;
        }
            return $commissions;
    }



    public function updateLeaderboard(User $user, array $referralUsers): void
    {
        // Points distribution per level
        $pointsDistribution = [30, 20, 10, 5];
        $currentReferrer = $referralUsers[1];
        $level = $currentReferrer->level;
        $leaderBoards = [];

        while ($currentReferrer && $level <= 4) {
            $referrerId = $currentReferrer->id; // Referrer should get points
            $points = $pointsDistribution[$level - 1];
            // Update leaderboard (insert or update points)
            $leaderboard = Leaderboard::firstOrNew(['user_id' => $user->id]);

            $leaderboard->total_commission = ($leaderboard->total_commission ?? 0) + $points;
            $leaderboard->total_nodes = ($leaderboard->total_nodes ?? 0) + 0;

            $leaderboard->save();
            $leaderBoards[$level] = $leaderboard; // Store updated record in the array


            if ($referrerId){
                $commission = Commission::where('user_id', $user->id)
                                    ->where('from_user_id', $referrerId)->first();

                if ($commission){
                    $leaderboard = Leaderboard::firstOrNew(['user_id' => $referrerId]);

                    $leaderboard->total_commission = ($leaderboard->total_commission ?? 0) + $commission->amount;
                    $leaderboard->total_nodes = ($leaderboard->total_nodes ?? 0) + 1;

                    $leaderboard->save();

                    $leaderBoards[$level + 1] = $leaderboard;
                }
            }
            $currentReferrer = $currentReferrer->referrer;
            $level++;
        }
    }



    private function generateReferralCode($user)
    {
        $referralCode = ReferralCode::create([
            'code' => Str::random(8),
            'type' => 'user',
            'user_id' => $user->id,
        ]);

        return $referralCode->code;
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

        if (!Auth::attempt($credentials)) {
            return sendErrorResponse('Invalid credentials', 401);
        }

        // Generate refresh token (optional: store securely if needed)
        $refreshToken = JWTAuth::fromUser(Auth::user());

        $data = [
            'transaction_id_required' => !(bool)auth()->user()->transaction_id,
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

        if (!$user && $token->doesntExist()) {
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
            $numberProto = $phoneUtil->parse($phone, "BD"); // BD = Bangladesh
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

