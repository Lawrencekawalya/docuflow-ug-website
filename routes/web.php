<?php

use App\Http\Controllers\DemoRequestController;
use App\Http\Controllers\SitemapController;
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

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');
});

require __DIR__.'/settings.php';
