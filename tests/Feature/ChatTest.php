<?php

namespace Tests\Feature;

use App\Models\ChatConversation;
use App\Models\ChatMessage;
use App\Models\User;
use App\Notifications\ChatConversationStarted;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class ChatTest extends TestCase
{
    use RefreshDatabase;

    /** @return array{ChatConversation, string} */
    private function conversation(string $status = 'open'): array
    {
        $token = Str::random(64);
        $conversation = ChatConversation::query()->create([
            'visitor_token_hash' => hash('sha256', $token),
            'visitor_name' => 'Sarah Namara',
            'visitor_email' => 'sarah@example.com',
            'status' => $status,
            'last_message_at' => now(),
        ]);
        $conversation->messages()->create([
            'sender_type' => 'visitor',
            'body' => 'Can DocuFlow process our invoices?',
        ]);

        return [$conversation, $token];
    }

    public function test_a_visitor_can_start_a_persisted_conversation(): void
    {
        Notification::fake();
        config(['docuflow.leads.email' => 'support@docuflow.test']);

        $response = $this->postJson(route('chat.store'), [
            'visitor_name' => 'Sarah Namara',
            'visitor_email' => 'sarah@example.com',
            'message' => 'Can DocuFlow process our invoices?',
            'website' => '',
        ]);

        $response
            ->assertCreated()
            ->assertCookie('docuflow_chat')
            ->assertJsonPath('conversation.reference', 1000)
            ->assertJsonPath('conversation.messages.0.sender_type', 'visitor');

        $conversation = ChatConversation::query()->sole();
        $this->assertSame(64, strlen($conversation->visitor_token_hash));
        $this->assertSame('sarah@example.com', $conversation->visitor_email);
        $this->assertNotNull($conversation->notification_dispatched_at);
        $this->assertDatabaseHas('chat_messages', [
            'chat_conversation_id' => $conversation->id,
            'sender_type' => 'visitor',
            'body' => 'Can DocuFlow process our invoices?',
        ]);
        Notification::assertSentOnDemand(
            ChatConversationStarted::class,
            fn (ChatConversationStarted $notification, array $channels, AnonymousNotifiable $notifiable): bool => $notifiable->routes['mail'] === 'support@docuflow.test',
        );
    }

    public function test_chat_start_fields_and_honeypot_are_validated(): void
    {
        Notification::fake();

        $this->postJson(route('chat.store'), [
            'visitor_name' => '',
            'visitor_email' => 'not-an-email',
            'message' => '',
            'website' => 'https://spam.example',
        ])->assertUnprocessable()
            ->assertJsonValidationErrors([
                'visitor_name',
                'visitor_email',
                'message',
                'website',
            ]);

        $this->assertDatabaseEmpty('chat_conversations');
        Notification::assertNothingSent();
    }

    public function test_a_visitor_can_only_read_and_send_to_their_cookie_owned_conversation(): void
    {
        [$conversation, $token] = $this->conversation();

        $this->withCredentials()
            ->withCookie('docuflow_chat', $token)
            ->getJson(route('chat.show'))
            ->assertOk()
            ->assertJsonPath('conversation.id', $conversation->id);

        $this->postJson(route('chat.messages.store'), [
            'message' => 'We process about 300 each month.',
        ])->assertCreated();

        $this->assertDatabaseHas('chat_messages', [
            'chat_conversation_id' => $conversation->id,
            'sender_type' => 'visitor',
            'body' => 'We process about 300 each month.',
        ]);

        $this->withCookie('docuflow_chat', Str::random(64))
            ->postJson(route('chat.messages.store'), ['message' => 'Intrusion'])
            ->assertNotFound();
    }

    public function test_support_inbox_requires_a_verified_support_agent(): void
    {
        $this->get(route('support.conversations.index'))
            ->assertRedirect(route('login'));

        $ordinaryUser = User::factory()->create();
        $this->actingAs($ordinaryUser)
            ->get(route('support.conversations.index'))
            ->assertForbidden();

        $supportAgent = User::factory()->create();
        $supportAgent->forceFill(['is_support_agent' => true])->save();
        $this->actingAs($supportAgent)
            ->get(route('support.conversations.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Support/Conversations')
                ->has('conversations'));
    }

    public function test_support_access_can_be_granted_and_revoked_explicitly(): void
    {
        $user = User::factory()->create(['email' => 'support@syntaxsystems.co']);

        $this->artisan('chat:grant-support', ['email' => $user->email])
            ->expectsOutputToContain('Support inbox access granted')
            ->assertSuccessful();
        $this->assertTrue($user->refresh()->is_support_agent);

        $this->artisan('chat:grant-support', [
            'email' => $user->email,
            '--revoke' => true,
        ])->expectsOutputToContain('Support inbox access revoked')
            ->assertSuccessful();
        $this->assertFalse($user->refresh()->is_support_agent);
    }

    public function test_support_can_read_reply_close_and_reopen_a_conversation(): void
    {
        [$conversation, $token] = $this->conversation();
        $supportAgent = User::factory()->create();
        $supportAgent->forceFill(['is_support_agent' => true])->save();

        $this->actingAs($supportAgent)
            ->get(route('support.conversations.show', $conversation))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Support/Conversations')
                ->where('selectedConversation.reference', 1000));

        $this->assertNotNull(
            $conversation->messages()->where('sender_type', 'visitor')->firstOrFail()->read_at,
        );

        $this->postJson(route('support.conversations.messages.store', $conversation), [
            'message' => 'Yes. We can arrange a workflow review.',
        ])->assertCreated()
            ->assertJsonPath('message.sender_type', 'support');

        $supportMessage = ChatMessage::query()
            ->where('sender_type', 'support')
            ->sole();
        $this->assertSame($supportAgent->id, $supportMessage->user_id);

        $this->withCredentials()
            ->withCookie('docuflow_chat', $token)
            ->getJson(route('chat.messages', ['after' => 1]))
            ->assertOk()
            ->assertJsonPath('messages.0.body', 'Yes. We can arrange a workflow review.');

        $this->actingAs($supportAgent)
            ->patchJson(route('support.conversations.status', $conversation), [
                'status' => 'closed',
            ])->assertOk()
            ->assertJsonPath('status', 'closed');

        $this->withCookie('docuflow_chat', $token)
            ->postJson(route('chat.messages.store'), ['message' => 'One more question'])
            ->assertUnprocessable();

        $this->actingAs($supportAgent)
            ->patchJson(route('support.conversations.status', $conversation), [
                'status' => 'open',
            ])->assertOk()
            ->assertJsonPath('status', 'open');
    }
}
