<?php

use App\Http\Controllers\Api\MobileAuthController;
use App\Http\Controllers\Api\MobileChatController;
use App\Http\Controllers\Api\MobileDeviceController;
use Illuminate\Support\Facades\Route;

Route::prefix('mobile')->name('api.mobile.')->group(function (): void {
    Route::post('/login', [MobileAuthController::class, 'login'])
        ->middleware('throttle:5,1')
        ->name('login');

    Route::middleware(['auth:sanctum', 'support-agent'])->group(function (): void {
        Route::get('/me', [MobileAuthController::class, 'me'])->name('me');
        Route::post('/logout', [MobileAuthController::class, 'logout'])->name('logout');
        Route::post('/devices', [MobileDeviceController::class, 'store'])->name('devices.store');

        Route::get('/conversations', [MobileChatController::class, 'index'])
            ->name('conversations.index');
        Route::get('/conversations/{conversation}', [MobileChatController::class, 'show'])
            ->name('conversations.show');
        Route::get('/conversations/{conversation}/messages', [MobileChatController::class, 'messages'])
            ->name('conversations.messages');
        Route::post('/conversations/{conversation}/messages', [MobileChatController::class, 'reply'])
            ->name('conversations.messages.store');
        Route::patch('/conversations/{conversation}/status', [MobileChatController::class, 'updateStatus'])
            ->name('conversations.status');
    });
});
