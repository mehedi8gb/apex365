<?php


namespace Tests\Unit;

use App\Enums\SupportTicketStatus;
use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class SupportTicketV1Test extends TestCase
{
    use RefreshDatabase;

    private $adminUser = null;
    private $normalUser = null;

    private $adminToken = null;
    private $normalToken = null;

    protected function setUp(): void
    {
        parent::setUp();
        Artisan::call('db:seed', ['--class' => 'RolesAndPermissionsSeeder']);

        $adminUser = UserFactory::times(1)->create([
            'phone' => '+8801000000000'
        ])->first();
        $adminUser->assignRole('admin');
        $this->adminUser = $adminUser;
        $this->adminToken = JWTAuth::claims(['refresh' => true])->fromUser($adminUser);

        $normalUser = UserFactory::times(1)->create([
            'phone' => '+8802000000000'
        ])->first();
        $normalUser->assignRole('customer');
        $this->normalUser = $normalUser;
        $this->normalToken = JWTAuth::claims(['refresh' => true])->fromUser($normalUser);



    }

    // ---------- Helper Methods ----------

    protected function createUser(array $payload = []): array
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

    protected function createTicket(string $token, string $subject = null): array
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/v1/client/support/tickets', [
                'subject' => $subject ?? 'Test Ticket ' . rand(1, 1000),
            ]);
        $response->assertStatus(Response::HTTP_CREATED);

        return $response->json('data');
    }

    protected function sendMessage(string $token, int $ticketId, string $message, array $attachments = []): array
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson("/api/v1/client/support/tickets/{$ticketId}/messages", [
                'message' => $message,
                'attachments' => $attachments,
            ]);
        $response->assertStatus(Response::HTTP_CREATED);

        return $response->json('data');
    }

    protected function fetchMessages(string $token, int $ticketId): array
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson("/api/v1/client/support/tickets/{$ticketId}/messages");
        $response->assertStatus(Response::HTTP_OK);

        return $response->json('data');
    }

    // ---------- Test Cases ----------

    #[Test]
    public function it_can_create_ticket_and_send_message()
    {
        $this->withoutExceptionHandling();
        // Step 1: Create a user
//        $user = $this->createUser(['name' => 'Support User']);
//        $token = $this->loginUser($user['phone']);

        // Step 2: Create a support ticket
        $ticket = $this->createTicket($this->normalToken, 'Issue with account');
        $this->assertEquals('Issue with account', $ticket['subject']);
        $this->assertEquals(SupportTicketStatus::PENDING->value, $ticket['status']);

        // Step 3: Send a message on the ticket
        $message = $this->sendMessage($this->normalToken, $ticket['id'], 'I need help with my account');
        $this->assertEquals('I need help with my account', $message['message']);
//        $this->assertEquals($user['phone'], $message['sender']['phone']);

        // Step 4: Fetch all messages
        $messages = $this->fetchMessages($this->normalToken, $ticket['id']);
        $this->assertCount(1, $messages);
        $this->assertEquals($message['id'], $messages[0]['id']);
    }

    #[Test]
    public function admin_can_update_ticket_status()
    {
        // Step 1: Create user and ticket
        $user = $this->createUser();
        $token = $this->loginUser($user['phone']);
        $ticket = $this->createTicket($token);

        // Step 2: Admin login
        $adminToken = $this->loginUser('+8801000000000'); // make sure admin exists

        // Step 3: Admin updates ticket status
        $response = $this->withHeader('Authorization', 'Bearer ' . $adminToken)
            ->patchJson("/api/v1/support/tickets/{$ticket['id']}/status", [
                'status' => 'closed',
            ]);
        $response->assertStatus(Response::HTTP_OK);
        $this->assertEquals('closed', $response->json('data.status'));
    }
}
