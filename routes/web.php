<?php

use App\Http\Controllers\DemoRequestController;
use App\Http\Controllers\GuestChatController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\SupportChatController;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Welcome')->name('home');
Route::inertia('/how-it-works', 'HowItWorks')->name('how-it-works');
Route::inertia('/pricing', 'Pricing')->name('pricing');
Route::inertia('/about', 'About')->name('about');
Route::inertia('/contact', 'Contact')->name('contact');
Route::inertia('/privacy', 'Privacy')->name('privacy');

Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');

Route::post('/demo-requests', [DemoRequestController::class, 'store'])
    ->middleware('throttle:6,1')
    ->name('demo-requests.store');

Route::prefix('chat')->name('chat.')->group(function (): void {
    Route::get('/conversation', [GuestChatController::class, 'show'])
        ->middleware('throttle:60,1')
        ->name('show');
    Route::post('/conversations', [GuestChatController::class, 'store'])
        ->middleware('throttle:4,1')
        ->name('store');
    Route::get('/messages', [GuestChatController::class, 'messages'])
        ->middleware('throttle:60,1')
        ->name('messages');
    Route::post('/messages', [GuestChatController::class, 'send'])
        ->middleware('throttle:30,1')
        ->name('messages.store');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function (Request $request): RedirectResponse {
        return $request->user()?->is_support_agent === true
            ? redirect()->route('support.conversations.index')
            : redirect()->route('home');
    })->name('dashboard');

    Route::middleware('support-agent')->prefix('support')->name('support.')->group(function (): void {
        Route::get('/conversations', [SupportChatController::class, 'index'])
            ->name('conversations.index');
        Route::get('/conversations/{conversation}', [SupportChatController::class, 'show'])
            ->name('conversations.show');
        Route::get('/conversations/{conversation}/messages', [SupportChatController::class, 'messages'])
            ->name('conversations.messages');
        Route::post('/conversations/{conversation}/messages', [SupportChatController::class, 'reply'])
            ->name('conversations.messages.store');
        Route::patch('/conversations/{conversation}/status', [SupportChatController::class, 'updateStatus'])
            ->name('conversations.status');
    });
});

require __DIR__.'/settings.php';
