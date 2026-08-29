<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $visitor_token_hash
 * @property string $visitor_name
 * @property string $visitor_email
 * @property string $status
 * @property Carbon $last_message_at
 * @property Carbon|null $notification_dispatched_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable([
    'visitor_token_hash',
    'visitor_name',
    'visitor_email',
    'status',
    'last_message_at',
    'notification_dispatched_at',
])]
class ChatConversation extends Model
{
    public function referenceNumber(): int
    {
        return $this->id + 999;
    }

    /** @return HasMany<ChatMessage, $this> */
    public function messages(): HasMany
    {
        return $this->hasMany(ChatMessage::class);
    }

    /** @return HasOne<ChatMessage, $this> */
    public function latestMessage(): HasOne
    {
        return $this->hasOne(ChatMessage::class)->latestOfMany();
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'last_message_at' => 'datetime',
            'notification_dispatched_at' => 'datetime',
        ];
    }
}
