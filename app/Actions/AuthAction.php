<?php

namespace App\Actions;

use App\Jobs\ProcessReferralChain;
use App\Models\ReferralCode;
use App\Models\Transaction;
use App\Models\User;
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
        $transaction = self::validateTransaction($validated['transactionId']);

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

    /**
     * @throws Exception
     */
    private static function validateTransaction(string $transactionId): Transaction
    {
        $transaction = Transaction::where('transactionId', $transactionId)->firstOrFail();

        if (! App::isLocal() && $transaction->userId !== null) {
            throw new Exception('Transaction id already used, try with a new one');
        }

        return $transaction;
    }

    private static function createUser(array $data): User
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
            'nid' => $data['nid'],
            'address' => $data['address'],
            'password' => Hash::make($data['password']),
        ]);
    }

    /**
     * @throws Throwable
     */
    //    private static function applyReferralChain(User $user, ReferralCode $referrer): void
    //    {
    //        $helper = new ReferralHelper();
    //        $helper->createReferralChain($user, $referrer);
    //        $helper->distributeReferralPoints();
    //        $helper->updateReferralLeaderboard();
    //        $helper->generateReferralCode();
    //    }

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
