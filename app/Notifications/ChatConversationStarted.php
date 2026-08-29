<?php

namespace App\Notifications;

use App\Models\ChatConversation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ChatConversationStarted extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public ChatConversation $conversation)
    {
        $this->afterCommit();
    }

    /** @return array<int, string> */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $conversation = $this->conversation->loadMissing('messages');
        $firstMessage = $conversation->messages->first();

        return (new MailMessage)
            ->subject("New website chat from {$conversation->visitor_name}")
            ->greeting('A new website conversation has started')
            ->line("Visitor: {$conversation->visitor_name} ({$conversation->visitor_email})")
            ->line("Reference: #{$conversation->referenceNumber()}")
            ->line('Message: '.$firstMessage->body)
            ->action('Open support inbox', route('support.conversations.show', $conversation))
            ->line('Sign in to the DocuFlow support inbox to reply.')
            ->salutation('DocuFlow UG');
    }
}
