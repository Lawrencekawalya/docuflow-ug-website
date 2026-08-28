<?php

namespace App\Notifications;

use App\Models\DemoRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DemoRequestAcknowledged extends Notification implements ShouldQueue
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
        $message = (new MailMessage)
            ->subject('We received your DocuFlow demo request')
            ->greeting("Hello {$request->full_name},")
            ->line("Thank you for requesting a DocuFlow demo for {$request->business_name}.")
            ->line('We have received your request. A member of our team will contact you within 48 hours to learn more about your document workflow and arrange the next step.')
            ->line("Your request reference is #{$request->referenceNumber()}.")
            ->line('If you need to add anything in the meantime, reply directly to this email.')
            ->salutation('The DocuFlow UG team');

        $replyTo = config('docuflow.contact.email');

        if (is_string($replyTo) && $replyTo !== '') {
            $message->replyTo($replyTo, 'DocuFlow UG');
        }

        return $message;
    }
}
