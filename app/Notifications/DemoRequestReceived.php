<?php

namespace App\Notifications;

use App\Models\DemoRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DemoRequestReceived extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public DemoRequest $demoRequest)
    {
        $this->afterCommit();
    }

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $request = $this->demoRequest;

        return (new MailMessage)
            ->subject("New DocuFlow demo request from {$request->business_name}")
            ->greeting('A new demo request has arrived')
            ->line("Contact: {$request->full_name} ({$request->work_email})")
            ->line("Business: {$request->business_name}")
            ->line('Documents: '.implode(', ', $request->document_types))
            ->line('Monthly volume: '.($request->monthly_document_volume ?? 'Not specified'))
            ->line('Preferred contact: '.($request->preferred_contact_method ?? 'Not specified'))
            ->line('Phone / WhatsApp: '.($request->phone ?? 'Not supplied'))
            ->line('Location: '.($request->location ?? 'Not supplied'))
            ->line('Current process: '.($request->current_process ?? 'Not supplied'))
            ->line('Biggest challenge: '.($request->biggest_challenge ?? 'Not supplied'))
            ->line('Additional details: '.($request->message ?? 'None'))
            ->line("Lead record ID: {$request->id}")
            ->salutation('DocuFlow UG');
    }
}
