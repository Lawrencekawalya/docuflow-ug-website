<?php

namespace Tests\Feature;

use App\Models\ChatConversation;
use App\Models\SupportDevice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class MobileSupportApiTest extends TestCase
{
    use RefreshDatabase;

    private function supportAgent(array $attributes = []): User
    {
        $user = User::factory()->create([
            'email' => 'support@syntaxsystems.co',
            'password' => 'correct-password',
            'email_verified_at' => now(),
            ...$attributes,
        ]);
        $user->forceFill(['is_support_agent' => true])->save();

        return $user;
    }

    private function conversation(): ChatConversation
    {
        $conversation = ChatConversation::query()->create([
            'visitor_token_hash' => hash('sha256', Str::random(64)),
            'visitor_name' => 'Sarah Namara',
            'visitor_email' => 'sarah@example.com',
            'status' => 'open',
            'last_message_at' => now(),
        ]);
        $conversation->messages()->create([
            'sender_type' => 'visitor',
            'body' => 'Can you help with our invoices?',
        ]);

        return $conversation;
    }

    public function test_support_agent_can_log_in_and_log_out_of_the_mobile_app(): void
    {
        $user = $this->supportAgent();

        $login = $this->postJson(route('api.mobile.login'), [
            'email' => $user->email,
            'password' => 'correct-password',
            'device_name' => 'Lawrence Android',
        ])->assertOk()
            ->assertJsonPath('user.email', $user->email)
            ->assertJsonStructure(['token']);

        $token = $login->json('token');
        $this->assertIsString($token);

        $this->withToken($token)
            ->getJson(route('api.mobile.me'))
            ->assertOk()
            ->assertJsonPath('user.name', $user->name);

        $this->withToken($token)
            ->postJson(route('api.mobile.logout'))
            ->assertOk();

        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_mobile_login_rejects_invalid_unverified_and_non_support_accounts(): void
    {
        $supportAgent = $this->supportAgent();

        $this->postJson(route('api.mobile.login'), [
            'email' => $supportAgent->email,
            'password' => 'wrong-password',
            'device_name' => 'Android',
        ])->assertUnprocessable();

        $ordinaryUser = User::factory()->create([
            'email' => 'ordinary@example.com',
            'password' => 'correct-password',
        ]);

        $this->postJson(route('api.mobile.login'), [
            'email' => $ordinaryUser->email,
            'password' => 'correct-password',
            'device_name' => 'Android',
        ])->assertForbidden();

        $supportAgent->forceFill(['email_verified_at' => null])->save();

        $this->postJson(route('api.mobile.login'), [
            'email' => $supportAgent->email,
            'password' => 'correct-password',
            'device_name' => 'Android',
        ])->assertForbidden();
    }

    public function test_mobile_agent_can_register_a_device_read_and_reply_to_conversations(): void
    {
        $user = $this->supportAgent();
        $conversation = $this->conversation();
        $token = $user->createToken('Test phone', ['support-chat'])->plainTextToken;

        $this->withToken($token)
            ->postJson(route('api.mobile.devices.store'), [
                'token' => 'firebase-device-token',
                'platform' => 'android',
                'device_name' => 'Lawrence phone',
            ])->assertOk()
            ->assertJsonStructure(['device_id']);

        $device = SupportDevice::query()->sole();
        $this->assertSame('firebase-device-token', $device->token);
        $this->assertSame($user->id, $device->user_id);

        $this->withToken($token)
            ->getJson(route('api.mobile.conversations.index'))
            ->assertOk()
            ->assertJsonPath('conversations.0.reference', 1000)
            ->assertJsonPath('unread_count', 1);

        $this->withToken($token)
            ->getJson(route('api.mobile.conversations.show', $conversation))
            ->assertOk()
            ->assertJsonPath('conversation.visitor_name', 'Sarah Namara');

        $this->assertNotNull($conversation->messages()->firstOrFail()->fresh()->read_at);

        $this->withToken($token)
            ->postJson(route('api.mobile.conversations.messages.store', $conversation), [
                'message' => 'Yes, we can help with that workflow.',
            ])->assertCreated()
            ->assertJsonPath('message.sender_type', 'support');

        $this->withToken($token)
            ->patchJson(route('api.mobile.conversations.status', $conversation), [
                'status' => 'closed',
            ])->assertOk()
            ->assertJsonPath('status', 'closed');
    }

    public function test_mobile_api_requires_a_support_token(): void
    {
        $this->getJson(route('api.mobile.conversations.index'))->assertUnauthorized();

        $ordinaryUser = User::factory()->create();
        $token = $ordinaryUser->createToken('Test phone')->plainTextToken;

        $this->withToken($token)
            ->getJson(route('api.mobile.conversations.index'))
            ->assertForbidden();
    }
}
