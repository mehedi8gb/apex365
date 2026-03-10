<?php

namespace App\Actions;

use App\Jobs\ProcessReferralChain;
use App\Models\ReferralCode;
use App\Models\Transaction;
use App\Models\User;
use App\Services\TransactionValidatorService;
use Exception;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Throwable;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthAction
{
    /**
     * @throws Throwable
     */
    public static function register(array $validated): array
    {
        $validator   = app(TransactionValidatorService::class);
        $transaction = $validator->validateForCommission($validated['transactionId']);

        $referrerAndCode = ReferralCode::where('code', $validated['referralId'])->firstOrFail();

        $user = self::createUser($validated);
        $transaction->update(['userId' => $user->id]);

        ProcessReferralChain::dispatch($user, $referrerAndCode);

        $user->assignRole('customer');

        $credentials = self::getCredentials($validated);

        if (! Auth::attempt($credentials)) {
            throw new Exception('Authentication failed after registration');
        }

        $token = self::generateToken(Auth::user());

        return [
            'transaction_id_required' => ! (bool) auth()->user()->transaction_id,
            'access_token' => $token,
        ];
    }

    private static function createUser(array $data): User
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
            'nid' => $data['nid'],
            'address' => $data['address'],
            'date_of_birth' => $data['date_of_birth'] ?? null,
            'password' => Hash::make($data['password']),
        ]);
    }

    /**
     * @throws Exception
     */
    private static function getCredentials(array $data): array
    {
        if (! empty($data['email'])) {
            return ['email' => $data['email'], 'password' => $data['password']];
        }

        if (! empty($data['phone'])) {
            return ['phone' => $data['phone'], 'password' => $data['password']];
        }

        throw new Exception('Email or Phone is required for login');
    }

    private static function generateToken(User $user): string
    {
        return JWTAuth::claims(['refresh' => true])->fromUser($user);
    }
}
