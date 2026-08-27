<?php

namespace Tests\Feature;

use Inertia\Testing\AssertableInertia as Assert;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class PublicMarketingPagesTest extends TestCase
{
    /**
     * @return array<string, array{string, string}>
     */
    public static function publicPages(): array
    {
        return [
            'home' => ['home', 'Welcome'],
            'how it works' => ['how-it-works', 'HowItWorks'],
            'pricing' => ['pricing', 'Pricing'],
            'about' => ['about', 'About'],
            'contact' => ['contact', 'Contact'],
            'privacy' => ['privacy', 'Privacy'],
        ];
    }

    #[DataProvider('publicPages')]
    public function test_public_marketing_pages_are_available_to_guests(string $routeName, string $component): void
    {
        $this->get(route($routeName))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component($component)
                ->has('docuflow.contact')
                ->has('docuflow.pricing'));
    }

    public function test_sitemap_lists_the_public_pages(): void
    {
        $this->get(route('sitemap'))
            ->assertOk()
            ->assertHeader('Content-Type', 'application/xml')
            ->assertSee(route('home'))
            ->assertSee(route('contact'));
    }
}
