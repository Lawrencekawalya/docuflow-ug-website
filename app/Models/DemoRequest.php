<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $full_name
 * @property string $business_name
 * @property string $work_email
 * @property string|null $phone
 * @property string|null $location
 * @property array<int, string> $document_types
 * @property string|null $monthly_document_volume
 * @property string|null $current_process
 * @property string|null $biggest_challenge
 * @property string|null $preferred_contact_method
 * @property string|null $message
 * @property string $status
 * @property Carbon|null $notification_dispatched_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable([
    'full_name',
    'business_name',
    'work_email',
    'phone',
    'location',
    'document_types',
    'monthly_document_volume',
    'current_process',
    'biggest_challenge',
    'preferred_contact_method',
    'message',
    'status',
    'notification_dispatched_at',
])]
class DemoRequest extends Model
{
    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'document_types' => 'array',
            'notification_dispatched_at' => 'datetime',
        ];
    }
}
