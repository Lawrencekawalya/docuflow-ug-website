<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SupportDevice;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class MobileAuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string'],
            'device_name' => ['required', 'string', 'max:120'],
        ]);

        $user = User::query()->where('email', mb_strtolower($validated['email']))->first();

        if ($user === null || ! Hash::check($validated['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The supplied credentials are incorrect.'],
            ]);
        }

        if (! $user->is_support_agent) {
            abort(403, 'This account does not have access to the support inbox.');
        }

        if ($user->email_verified_at === null) {
            abort(403, 'Verify this email address before using the support app.');
        }

        $user->tokens()->where('name', $validated['device_name'])->delete();
        $token = $user->createToken($validated['device_name'], ['support-chat'])->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => $this->user($user),
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        return response()->json(['user' => $this->user($user)]);
    }

    public function logout(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'fcm_token' => ['nullable', 'string', 'max:4096'],
        ]);

        if (isset($validated['fcm_token'])) {
            SupportDevice::query()
                ->where('user_id', $request->user()?->id)
                ->where('token_hash', hash('sha256', $validated['fcm_token']))
                ->delete();
        }

        $request->user()?->currentAccessToken()?->delete();

        return response()->json(['message' => 'Signed out.']);
    }

    /** @return array<string, int|string> */
    private function user(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
        ];
    }
}
