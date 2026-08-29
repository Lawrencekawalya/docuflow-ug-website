<?php

namespace App\Http\Controllers;

use App\Http\Requests\StartChatConversationRequest;
use App\Http\Requests\StoreChatMessageRequest;
use App\Jobs\SendChatPushNotification;
use App\Models\ChatConversation;
use App\Models\ChatMessage;
use App\Notifications\ChatConversationStarted;
use App\Support\ChatPresenter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Cookie;

class GuestChatController extends Controller
{
    private const COOKIE_NAME = 'docuflow_chat';

    public function show(Request $request): JsonResponse
    {
        $conversation = $this->conversationFor($request);

        if ($conversation === null) {
            return response()->json(['conversation' => null])->header('Cache-Control', 'no-store');
        }

        $conversation->load(['messages' => fn ($query) => $query->oldest('id')]);

        return response()->json([
            'conversation' => ChatPresenter::conversation($conversation),
        ])->header('Cache-Control', 'no-store');
    }

    public function store(StartChatConversationRequest $request): JsonResponse
    {
        $existing = $this->conversationFor($request);

        if ($existing !== null) {
            $existing->load(['messages' => fn ($query) => $query->oldest('id')]);

            return response()->json([
                'conversation' => ChatPresenter::conversation($existing),
            ])->header('Cache-Control', 'no-store');
        }

        $token = Str::random(64);
        $validated = $request->validated();

        [$conversation, $message] = DB::transaction(function () use ($token, $validated): array {
            $conversation = ChatConversation::query()->create([
                'visitor_token_hash' => hash('sha256', $token),
                'visitor_name' => $validated['visitor_name'],
                'visitor_email' => $validated['visitor_email'],
                'status' => 'open',
                'last_message_at' => now(),
            ]);

            $message = $conversation->messages()->create([
                'sender_type' => 'visitor',
                'body' => $validated['message'],
            ]);

            return [$conversation, $message];
        });

        SendChatPushNotification::dispatch($message->id)->afterCommit();

        $recipient = config('docuflow.leads.email');

        if (is_string($recipient) && $recipient !== '') {
            Notification::route('mail', $recipient)
                ->notify(new ChatConversationStarted($conversation));
            $conversation->update(['notification_dispatched_at' => now()]);
        }

        $conversation->load(['messages' => fn ($query) => $query->oldest('id')]);

        return response()->json([
            'conversation' => ChatPresenter::conversation($conversation),
        ], 201)->cookie($this->visitorCookie($token))->header('Cache-Control', 'no-store');
    }

    public function messages(Request $request): JsonResponse
    {
        $conversation = $this->requireConversation($request);
        $after = max(0, $request->integer('after'));

        $conversation->messages()
            ->where('sender_type', 'support')
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $messages = $conversation->messages()
            ->where('id', '>', $after)
            ->oldest('id')
            ->get()
            ->map(fn (ChatMessage $message): array => ChatPresenter::message($message))
            ->values();

        $latestReadVisitorMessage = $conversation->messages()
            ->where('sender_type', 'visitor')
            ->whereNotNull('read_at')
            ->latest('id')
            ->first();

        return response()->json([
            'status' => $conversation->status,
            'messages' => $messages,
            'visitor_read_receipt' => $latestReadVisitorMessage === null
                ? null
                : [
                    'through_id' => $latestReadVisitorMessage->id,
                    'read_at' => $latestReadVisitorMessage->read_at?->toIso8601String(),
                ],
        ])->header('Cache-Control', 'no-store');
    }

    public function send(StoreChatMessageRequest $request): JsonResponse
    {
        $conversation = $this->requireConversation($request);

        if ($conversation->status !== 'open') {
            throw ValidationException::withMessages([
                'message' => 'This conversation has been closed. Start a new enquiry from the contact page if you still need help.',
            ]);
        }

        $message = $conversation->messages()->create([
            'sender_type' => 'visitor',
            'body' => $request->validated('message'),
        ]);

        $conversation->update(['last_message_at' => now()]);

        SendChatPushNotification::dispatch($message->id)->afterCommit();

        return response()->json([
            'message' => ChatPresenter::message($message),
        ], 201)->header('Cache-Control', 'no-store');
    }

    private function conversationFor(Request $request): ?ChatConversation
    {
        $token = $request->cookie(self::COOKIE_NAME);

        if (! is_string($token) || strlen($token) < 32) {
            return null;
        }

        return ChatConversation::query()
            ->where('visitor_token_hash', hash('sha256', $token))
            ->first();
    }

    private function requireConversation(Request $request): ChatConversation
    {
        return $this->conversationFor($request) ?? abort(404);
    }

    private function visitorCookie(string $token): Cookie
    {
        return cookie(
            self::COOKIE_NAME,
            $token,
            60 * 24 * 30,
            '/',
            config('session.domain'),
            (bool) (config('session.secure') ?? app()->isProduction()),
            true,
            false,
            'lax',
        );
    }
}
