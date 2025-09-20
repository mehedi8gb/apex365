<?php

namespace Tests\Unit;

use Illuminate\Support\Facades\Artisan;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class AuthControllerV3Test extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Artisan::call('migrate:fresh');
        Artisan::call('db:seed', ['--class' => 'TestDatabaseSeeder']);
    }

    // ---------- Helper Methods ----------
    protected function registerUser(array $payload = []): array
    {
        $default = [
            'referralId'    => $payload['referralId'] ?? null,
            'password'      => '123456',
            'phone'         => '+880' . str_pad(rand(1000000000, 9999999999), 10, '0', STR_PAD_LEFT),
            'name'          => $payload['name'] ?? 'User ' . rand(1, 1000),
            'nid'           => (string)rand(20000000000000000, 29999999999999999),
            'address'       => 'Fake Address',
            'date_of_birth' => '2002-04-08',
            'transactionId' => 'TRX-' . rand(100000, 999999) . '-TEST',
        ];

        $response = $this->postJson('/api/auth/register', array_merge($default, $payload));
        $response->assertStatus(Response::HTTP_CREATED);
        return $response->json('data');
    }

    protected function loginUser(string $phone, string $password = '123456'): string
    {
        $response = $this->postJson('/api/auth/login', [
            'phone' => $phone,
            'password' => $password,
        ]);
        $response->assertStatus(200);
        return $response->json('data.access_token');
    }

    protected function fetchMe(string $token): array
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/v3/client/me');
        $response->assertStatus(200);
        return $response->json('data');
    }

    // ---------- Test Cases ----------

    #[Test]
    public function it_can_register_login_and_fetch_authenticated_user_details()
    {
        $this->withoutExceptionHandling();
        $payload = [
            'referralId' => 'REF-12345678',
            'password' => '123456',
            'phone'        => '+880' . str_pad(rand(1000000000, 9999999999), 10, '0', STR_PAD_LEFT),
            'name'         => 'First User',
            'nid'          => (string)rand(20000000000000000, 29999999999999999), // 17 digits
            'address' => 'Fake Address',
            'date_of_birth' => '2002-04-08',
            'transactionId' => 'TRX-000111-AAAJJJ',
        ];
        // Step 1: Register a user
        $this->registerUser($payload);

        $token = $this->loginUser($payload['phone']);

        // Step 2: Fetch authenticated user details
        $meData = $this->fetchMe($token);

        // Assertions
        $this->assertEquals('First User', $meData['user']['name']);
        $this->assertArrayHasKey('referral_code', $meData['user']);
        $this->assertArrayHasKey('balance', $meData['user']);
    }

    #[Test]
    public function it_can_build_referral_chain_and_check_nodes()
    {
        // Step 1: Register root user
        $rootUser = $this->registerUser(['name' => 'Root User']);
        $rootToken = $this->loginUser($rootUser['phone']);
        $rootData = $this->fetchMe($rootToken);
        $rootReferralCode = $rootData['user']['referral_code'];

        // Step 2: Register 5 nodes under root referral
        $nodes = [];
        for ($i = 1; $i <= 5; $i++) {
            $nodes[] = $this->registerUser([
                'name' => "Node $i",
                'referralId' => $rootReferralCode,
            ]);
        }

        // Step 3: Fetch root user again to check referral chain
        $rootDataAfter = $this->fetchMe($rootToken);

        // Basic assertions
        $this->assertEquals('Root User', $rootDataAfter['user']['name']);
        $this->assertCount(5, $rootDataAfter['user']['referred_by_chain'] ?? []);

        foreach ($nodes as $index => $node) {
            $this->assertStringContainsString('Node', $node['name']);
            $this->assertArrayHasKey('id', $node);
        }
    }
}
