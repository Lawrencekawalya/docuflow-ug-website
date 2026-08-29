<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SupportDevice;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MobileDeviceController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'token' => ['required', 'string', 'max:4096'],
            'platform' => ['required', Rule::in(['android', 'ios'])],
            'device_name' => ['nullable', 'string', 'max:120'],
        ]);

        $device = SupportDevice::query()->updateOrCreate(
            ['token_hash' => hash('sha256', $validated['token'])],
            [
                'user_id' => $request->user()?->id,
                'token' => $validated['token'],
                'platform' => $validated['platform'],
                'device_name' => $validated['device_name'] ?? null,
                'last_seen_at' => now(),
            ],
        );

        return response()->json(['device_id' => $device->id]);
    }
}
