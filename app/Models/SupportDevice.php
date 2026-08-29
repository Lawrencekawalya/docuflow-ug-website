<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property string $token_hash
 * @property string $token
 * @property string $platform
 * @property string|null $device_name
 * @property Carbon $last_seen_at
 */
#[Fillable(['user_id', 'token_hash', 'token', 'platform', 'device_name', 'last_seen_at'])]
class SupportDevice extends Model
{
    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'token' => 'encrypted',
            'last_seen_at' => 'datetime',
        ];
    }
}
