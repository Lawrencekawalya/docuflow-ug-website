<?php

use App\Http\Controllers\DemoRequestController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Welcome')->name('home');
Route::inertia('/how-it-works', 'HowItWorks')->name('how-it-works');
Route::inertia('/pricing', 'Pricing')->name('pricing');
Route::inertia('/about', 'About')->name('about');
Route::inertia('/contact', 'Contact')->name('contact');
Route::inertia('/privacy', 'Privacy')->name('privacy');

Route::get('/sitemap.xml', function () {
    return response()->view('sitemap', [
        'urls' => [
            route('home'),
            route('how-it-works'),
            route('pricing'),
            route('about'),
            route('contact'),
            route('privacy'),
        ],
    ])->header('Content-Type', 'application/xml');
})->name('sitemap');

Route::post('/demo-requests', [DemoRequestController::class, 'store'])
    ->middleware('throttle:6,1')
    ->name('demo-requests.store');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');
});

require __DIR__.'/settings.php';
