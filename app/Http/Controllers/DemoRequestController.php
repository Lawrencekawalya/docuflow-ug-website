<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDemoRequest;
use App\Models\DemoRequest;
use App\Notifications\DemoRequestReceived;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Notification;

class DemoRequestController extends Controller
{
    public function store(StoreDemoRequest $request): RedirectResponse
    {
        $demoRequest = DemoRequest::query()->create($request->safe()->except('website'));
        $recipient = config('docuflow.leads.email');

        if (is_string($recipient) && $recipient !== '') {
            Notification::route('mail', $recipient)->notify(new DemoRequestReceived($demoRequest));
            $demoRequest->update(['notification_dispatched_at' => now()]);
        }

        return to_route('contact')->with('toast', [
            'type' => 'success',
            'message' => "Thanks — your demo request has been received. We'll review your workflow and contact you to arrange the next step.",
        ]);
    }
}
