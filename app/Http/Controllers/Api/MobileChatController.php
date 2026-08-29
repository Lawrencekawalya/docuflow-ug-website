<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreChatMessageRequest;
use App\Models\ChatConversation;
use App\Models\ChatMessage;
use App\Support\ChatPresenter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class MobileChatController extends Controller
{
    public function index(): JsonResponse
    {
        $conversations = ChatConversation::query()
            ->with('latestMessage')
            ->withCount([
                'messages as unread_count' => fn (Builder $query) => $query
                    ->where('sender_type', 'visitor')
                    ->whereNull('read_at'),
            ])
            ->latest('last_message_at')
            ->limit(100)
            ->get()
            ->map(fn (ChatConversation $conversation): array => $this->summary($conversation))
            ->values();

        return response()->json([
            'conversations' => $conversations,
            'unread_count' => $conversations->sum('unread_count'),
        ]);
    }

    public function show(ChatConversation $conversation): JsonResponse
    {
        $this->markRead($conversation);
        $conversation->load(['messages' => fn ($query) => $query->oldest('id')]);

        return response()->json([
            'conversation' => ChatPresenter::conversation($conversation),
        ]);
    }

    public function messages(Request $request, ChatConversation $conversation): JsonResponse
    {
        $this->markRead($conversation);
        $after = max(0, $request->integer('after'));

        return response()->json([
            'status' => $conversation->fresh()?->status,
            'messages' => $conversation->messages()
                ->where('id', '>', $after)
                ->oldest('id')
                ->get()
                ->map(fn (ChatMessage $message): array => ChatPresenter::message($message))
                ->values(),
        ]);
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
        ], 201);
    }

    public function updateStatus(Request $request, ChatConversation $conversation): JsonResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['open', 'closed'])],
        ]);

        $conversation->update(['status' => $validated['status']]);

        return response()->json(['status' => $conversation->status]);
    }

    /** @return array<string, mixed> */
    private function summary(ChatConversation $conversation): array
    {
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
    }

    private function markRead(ChatConversation $conversation): void
    {
        $conversation->messages()
            ->where('sender_type', 'visitor')
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }
}
