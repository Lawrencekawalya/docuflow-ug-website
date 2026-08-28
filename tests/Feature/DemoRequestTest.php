<?php

namespace Tests\Feature;

use App\Models\DemoRequest;
use App\Notifications\DemoRequestAcknowledged;
use App\Notifications\DemoRequestReceived;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class DemoRequestTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array<string, mixed>
     */
    private function validPayload(): array
    {
        return [
            'full_name' => 'Sarah Namara',
            'business_name' => 'Mbarara Accounts Ltd',
            'work_email' => 'sarah@example.com',
            'phone' => '+256 700 000 000',
            'location' => 'Mbarara',
            'document_types' => ['invoices', 'receipts'],
            'monthly_document_volume' => '100-500',
            'current_process' => 'We enter invoice data into a spreadsheet.',
            'biggest_challenge' => 'The process is slow and errors are difficult to trace.',
            'preferred_contact_method' => 'whatsapp',
            'message' => 'We would like to see an invoice demonstration.',
            'website' => '',
        ];
    }

    public function test_a_valid_demo_request_is_persisted_before_notification(): void
    {
        Notification::fake();
        config(['docuflow.leads.email' => 'leads@docuflow.test']);

        $this->post(route('demo-requests.store'), $this->validPayload())
            ->assertRedirect(route('contact'))
            ->assertSessionHas('toast.type', 'success');

        $request = DemoRequest::query()->sole();

        $this->assertSame('Sarah Namara', $request->full_name);
        $this->assertSame(['invoices', 'receipts'], $request->document_types);
        $this->assertNotNull($request->notification_dispatched_at);
        Notification::assertSentOnDemand(
            DemoRequestReceived::class,
            fn (DemoRequestReceived $notification, array $channels, AnonymousNotifiable $notifiable): bool => $notifiable->routes['mail'] === 'leads@docuflow.test',
        );
        Notification::assertSentOnDemand(
            DemoRequestAcknowledged::class,
            fn (DemoRequestAcknowledged $notification, array $channels, AnonymousNotifiable $notifiable): bool => $notifiable->routes['mail'] === 'sarah@example.com',
        );
    }

    public function test_a_request_is_saved_and_the_requester_is_acknowledged_when_no_internal_recipient_is_configured(): void
    {
        Notification::fake();
        config(['docuflow.leads.email' => null]);

        $this->post(route('demo-requests.store'), $this->validPayload())
            ->assertRedirect(route('contact'));

        $this->assertDatabaseCount('demo_requests', 1);
        Notification::assertSentOnDemandTimes(DemoRequestReceived::class, 0);
        Notification::assertSentOnDemand(DemoRequestAcknowledged::class);
    }

    public function test_the_acknowledgement_sets_the_48_hour_expectation_and_support_reply_address(): void
    {
        config(['docuflow.contact.email' => 'support@syntaxsystems.co']);
        $request = DemoRequest::query()->create(collect($this->validPayload())->except('website')->all());

        $mail = (new DemoRequestAcknowledged($request))->toMail(new AnonymousNotifiable);

        $this->assertSame('We received your DocuFlow demo request', $mail->subject);
        $this->assertContains(
            'We have received your request. A member of our team will contact you within 48 hours to learn more about your document workflow and arrange the next step.',
            $mail->introLines,
        );
        $this->assertContains('Your request reference is #1000.', $mail->introLines);
        $this->assertSame([['support@syntaxsystems.co', 'DocuFlow UG']], $mail->replyTo);
    }

    public function test_public_reference_numbers_start_at_1000(): void
    {
        $request = new DemoRequest;

        $request->id = 1;
        $this->assertSame(1000, $request->referenceNumber());

        $request->id = 4;
        $this->assertSame(1003, $request->referenceNumber());
    }

    public function test_required_demo_fields_are_validated(): void
    {
        $this->from(route('contact'))
            ->post(route('demo-requests.store'), [])
            ->assertRedirect(route('contact'))
            ->assertSessionHasErrors(['full_name', 'business_name', 'work_email', 'document_types']);

        $this->assertDatabaseEmpty('demo_requests');
    }

    public function test_honeypot_submissions_are_rejected(): void
    {
        $payload = $this->validPayload();
        $payload['website'] = 'https://spam.example';

        $this->from(route('contact'))
            ->post(route('demo-requests.store'), $payload)
            ->assertRedirect(route('contact'))
            ->assertSessionHasErrors('website');

        $this->assertDatabaseEmpty('demo_requests');
    }
}
