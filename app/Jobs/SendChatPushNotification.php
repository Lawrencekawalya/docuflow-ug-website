<?php

namespace App\Jobs;

use App\Models\ChatMessage;
use App\Models\SupportDevice;
use App\Services\FirebaseCloudMessaging;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendChatPushNotification implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    /** @var array<int, int> */
    public array $backoff = [10, 60, 300];

    public function __construct(public int $messageId) {}

    public function handle(FirebaseCloudMessaging $messaging): void
    {
        if (! $messaging->configured()) {
            return;
        }

        $message = ChatMessage::query()->with('conversation')->find($this->messageId);

        if ($message === null || $message->sender_type !== 'visitor') {
            return;
        }

        $conversation = $message->conversation;
        $title = "New chat from {$conversation->visitor_name}";
        $body = mb_strimwidth($message->body, 0, 140, '…');

        SupportDevice::query()
            ->whereHas('user', fn ($query) => $query->where('is_support_agent', true))
            ->each(function (SupportDevice $device) use ($messaging, $message, $conversation, $title, $body): void {
                $isValid = $messaging->send($device->token, $title, $body, [
                    'type' => 'chat_message',
                    'conversation_id' => (string) $conversation->id,
                    'message_id' => (string) $message->id,
                    'reference' => (string) $conversation->referenceNumber(),
                ]);

                if (! $isValid) {
                    $device->delete();
                }
            });
    }
}
