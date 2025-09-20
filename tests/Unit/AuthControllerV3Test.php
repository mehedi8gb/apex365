<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class AuthControllerV3Test extends TestCase
{
//    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Artisan::call('migrate:fresh');
        Artisan::call('db:seed', ['--class' => 'TestDatabaseSeeder']);

    }


    #[Test]
    public function it_can_register_and_fetch_authenticated_user_details()
    {
        $this->withoutExceptionHandling();
        // Step 1: Fake registration payload
        $registerPayload = [
            'referralId' => 'REF-12345678',
            'password' => '123456',
            'phone'        => '+880' . str_pad(rand(1000000000, 9999999999), 10, '0', STR_PAD_LEFT),
            'name'         => 'NEW MAN ' . rand(1, 100),
            'nid'          => (string)rand(20000000000000000, 29999999999999999), // 17 digits
            'address' => 'Fake Address',
            'date_of_birth' => '2002-04-08',
            'transactionId' => 'TRX-000111-AAAJJJ',
        ];

        // Step 2: Register new user
        $registerResponse = $this->postJson('/api/auth/register', $registerPayload);

        $registerResponse->assertStatus(Response::HTTP_CREATED)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'transaction_id_required',
                    'access_token'
                ],
            ]);

        // Step 3: Login with registered user
        $loginPayload = [
            'phone' => $registerPayload['phone'],
            'password' => $registerPayload['password'],
        ];

        $loginResponse = $this->postJson('/api/auth/login', $loginPayload);

        $loginResponse->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'transaction_id_required',
                    'access_token'
                ],
            ]);

        $token = $loginResponse->json('data.access_token');

        // Step 4: Call /v3/client/me with Bearer token
        $meResponse = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/v3/client/me');

        $meResponse->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'user' => [
                        'id',
                        'role',
                        'name',
                        'phone',
                        'balance',
                        'total_withdrawn_approved',
                        'total_pending_withdrawal',
                        'total_suspended_withdrawal',
                        'nid',
                        'address',
                        'date_of_birth',
                        'profile_picture',
                        'referral_code',
                        'account_created_at',
                        'referred_by_chain',
                    ],
                    'leaderboard' => [
                        'total_commissions',
                        'total_nodes',
                        'total_earned_coins',
                        'profile_rank',
                    ],
                    'commissions' => [
                        'purchase_commissions',
                        'purchase_commissions_pagination',
                        'signup_commissions',
                        'signup_commissions_pagination',
                    ],
                ],
            ]);
    }
}
