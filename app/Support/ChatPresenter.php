<?php

namespace App\Support;

use App\Models\ChatConversation;
use App\Models\ChatMessage;

final class ChatPresenter
{
    /** @return array<string, mixed> */
    public static function conversation(ChatConversation $conversation): array
    {
        return [
            'id' => $conversation->id,
            'reference' => $conversation->referenceNumber(),
            'visitor_name' => $conversation->visitor_name,
            'visitor_email' => $conversation->visitor_email,
            'status' => $conversation->status,
            'last_message_at' => $conversation->last_message_at->toIso8601String(),
            'messages' => $conversation->messages
                ->map(fn (ChatMessage $message): array => self::message($message))
                ->values()
                ->all(),
        ];
    }

    /** @return array<string, mixed> */
    public static function message(ChatMessage $message): array
    {
        return [
            'id' => $message->id,
            'sender_type' => $message->sender_type,
            'body' => $message->body,
            'read_at' => $message->read_at?->toIso8601String(),
            'created_at' => $message->created_at?->toIso8601String(),
        ];
    }
}
