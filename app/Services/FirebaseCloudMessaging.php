<?php

namespace App\Services;

use Google\Auth\Credentials\ServiceAccountCredentials;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class FirebaseCloudMessaging
{
    private const MESSAGING_SCOPE = 'https://www.googleapis.com/auth/firebase.messaging';

    public function configured(): bool
    {
        $projectId = config('services.firebase.project_id');
        $credentials = config('services.firebase.credentials');

        return is_string($projectId)
            && $projectId !== ''
            && is_string($credentials)
            && $credentials !== ''
            && is_readable($credentials);
    }

    /**
     * @param  array<string, string>  $data
     *
     * @throws ConnectionException|RequestException
     */
    public function send(string $deviceToken, string $title, string $body, array $data): bool
    {
        if (! $this->configured()) {
            return true;
        }

        $projectId = (string) config('services.firebase.project_id');
        $response = Http::withToken($this->accessToken())
            ->acceptJson()
            ->timeout(15)
            ->post("https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send", [
                'message' => [
                    'token' => $deviceToken,
                    'notification' => [
                        'title' => $title,
                        'body' => $body,
                    ],
                    'data' => $data,
                    'android' => [
                        'priority' => 'high',
                        'notification' => [
                            'channel_id' => 'docuflow_chats',
                            'sound' => 'default',
                        ],
                    ],
                    'apns' => [
                        'payload' => [
                            'aps' => [
                                'sound' => 'default',
                                'badge' => 1,
                            ],
                        ],
                    ],
                ],
            ]);

        if ($response->successful()) {
            return true;
        }

        $status = $response->json('error.status');

        if ($response->status() === 404 || in_array($status, ['NOT_FOUND', 'UNREGISTERED'], true)) {
            return false;
        }

        $response->throw();

        return true;
    }

    private function accessToken(): string
    {
        return Cache::remember('firebase.messaging.access_token', now()->addMinutes(50), function (): string {
            $credentialsPath = (string) config('services.firebase.credentials');
            $credentials = new ServiceAccountCredentials(self::MESSAGING_SCOPE, $credentialsPath);
            $token = $credentials->fetchAuthToken();
            $accessToken = $token['access_token'] ?? null;

            if (! is_string($accessToken) || $accessToken === '') {
                throw new RuntimeException('Firebase service-account authentication did not return an access token.');
            }

            return $accessToken;
        });
    }
}
