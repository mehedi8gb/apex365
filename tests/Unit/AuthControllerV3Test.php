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
            'referralId' => $payload['referralId'] ?? 'REF-12345678',
            'password' => '123456',
            'phone' => '+880' . str_pad(rand(1000000000, 9999999999), 10, '0', STR_PAD_LEFT),
            'name' => $payload['name'] ?? 'User ' . rand(1, 1000),
            'nid' => (string)rand(20000000000000000, 29999999999999999),
            'address' => 'Fake Address',
            'date_of_birth' => '2002-04-08',
            'transactionId' => 'TRX-000111-AAAJJJ',
        ];

        $response = $this->postJson('/api/auth/register', array_merge($default, $payload));
        $response->assertStatus(Response::HTTP_CREATED);
        return array_merge($default, $payload);
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
        // Step 1: Register a user
        $payload = $this->registerUser(['name' => 'First User']);

        $token = $this->loginUser($payload['phone']);

        // Step 2: Fetch authenticated user details
        $meData = $this->fetchMe($token);

        // Assertions
        $this->assertEquals('First User', $meData['user']['name']);
        $this->assertArrayHasKey('referral_code', $meData['user']);
        $this->assertArrayHasKey('balance', $meData['user']);
    }

    #[Test]
    public function it_can_build_sequential_referral_chain()
    {
        $this->withoutExceptionHandling();

        // Step 1: Register root user
        $rootUser = $this->registerUser(['name' => 'Root User']);
        $rootToken = $this->loginUser($rootUser['phone']);
        $rootData = $this->fetchMe($rootToken);
        $currentReferralCode = $rootData['user']['referral_code'];

        $users = [
            ['name' => 'Root User', 'data' => $rootData]
        ];

        // Step 2: Register 5 nodes in chain (each referred by the last user)
        foreach (range(1, 5) as $i) {
            $newUser = $this->registerUser([
                'name' => "Node $i",
                'referralId' => $currentReferralCode,
            ]);

            $newToken = $this->loginUser($newUser['phone']);
            $newData = $this->fetchMe($newToken);

            $users[] = ['name' => "Node $i", 'data' => $newData];
            $currentReferralCode = $newData['user']['referral_code'];
        }

        // Step 3: Validate referral chains
        foreach ($users as $index => $user) {
            $chain = $user['data']['user']['referred_by_chain'];

            // Node i should have exactly i items in chain
            $this->assertCount($index, $chain, "{$user['name']} should have {$index} in chain");

            // Verify that chain is ordered properly back to root
            $expectedNames = array_reverse(array_column(array_slice($users, 0, $index), 'name'));
            $actualNames = array_column($chain, 'name');

            $this->assertEquals(
                $expectedNames,
                $actualNames,
                "Referral chain mismatch for {$user['name']}"
            );
        }
    }

}
