<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreChatMessageRequest;
use App\Models\ChatConversation;
use App\Models\ChatMessage;
use App\Support\ChatPresenter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class SupportChatController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Support/Conversations', [
            'conversations' => $this->conversationList(),
            'selectedConversation' => null,
        ]);
    }

    public function show(ChatConversation $conversation): Response
    {
        $this->markVisitorMessagesRead($conversation);
        $conversation->load(['messages' => fn ($query) => $query->oldest('id')]);

        return Inertia::render('Support/Conversations', [
            'conversations' => $this->conversationList(),
            'selectedConversation' => ChatPresenter::conversation($conversation),
        ]);
    }

    public function messages(Request $request, ChatConversation $conversation): JsonResponse
    {
        $this->markVisitorMessagesRead($conversation);
        $after = max(0, $request->integer('after'));
        $messages = $conversation->messages()
            ->where('id', '>', $after)
            ->oldest('id')
            ->get()
            ->map(fn (ChatMessage $message): array => ChatPresenter::message($message))
            ->values();

        return response()->json([
            'status' => $conversation->fresh()?->status,
            'messages' => $messages,
        ])->header('Cache-Control', 'no-store');
    }

    public function reply(StoreChatMessageRequest $request, ChatConversation $conversation): JsonResponse
    {
        if ($conversation->status !== 'open') {
            throw ValidationException::withMessages([
                'message' => 'Reopen this conversation before replying.',
            ]);
        }

        $message = $conversation->messages()->create([
            'user_id' => $request->user()?->id,
            'sender_type' => 'support',
            'body' => $request->validated('message'),
        ]);

        $conversation->update(['last_message_at' => now()]);

        return response()->json([
            'message' => ChatPresenter::message($message),
        ], 201)->header('Cache-Control', 'no-store');
    }

    public function updateStatus(Request $request, ChatConversation $conversation): JsonResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['open', 'closed'])],
        ]);

        $conversation->update(['status' => $validated['status']]);

        return response()->json([
            'status' => $conversation->status,
        ])->header('Cache-Control', 'no-store');
    }

    /** @return array<int, array<string, mixed>> */
    private function conversationList(): array
    {
        return ChatConversation::query()
            ->with('latestMessage')
            ->withCount([
                'messages as unread_count' => fn (Builder $query) => $query
                    ->where('sender_type', 'visitor')
                    ->whereNull('read_at'),
            ])
            ->latest('last_message_at')
            ->limit(100)
            ->get()
            ->map(function (ChatConversation $conversation): array {
                /** @var ChatMessage|null $latestMessage */
                $latestMessage = $conversation->latestMessage;

                return [
                    'id' => $conversation->id,
                    'reference' => $conversation->referenceNumber(),
                    'visitor_name' => $conversation->visitor_name,
                    'visitor_email' => $conversation->visitor_email,
                    'status' => $conversation->status,
                    'last_message_at' => $conversation->last_message_at->toIso8601String(),
                    'latest_message' => $latestMessage?->body,
                    'unread_count' => (int) $conversation->getAttribute('unread_count'),
                ];
            })
            ->all();
    }

    private function markVisitorMessagesRead(ChatConversation $conversation): void
    {
        $conversation->messages()
            ->where('sender_type', 'visitor')
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }
}
