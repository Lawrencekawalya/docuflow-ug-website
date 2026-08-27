<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function __invoke(): Response
    {
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
    }
}
