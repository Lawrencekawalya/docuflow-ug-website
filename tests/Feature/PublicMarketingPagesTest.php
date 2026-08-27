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

    public function test_approved_public_business_details_are_exposed(): void
    {
        $this->get(route('pricing'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->where('docuflow.contact.email', 'lawkawalya@gmail.com')
                ->where('docuflow.contact.phone', '+256 755400297')
                ->where('docuflow.contact.whatsapp', '+256 778864614')
                ->where('docuflow.pricing.starter.monthly', 150000)
                ->where('docuflow.pricing.starter.setup', 500000)
                ->where('docuflow.pricing.starter.allowance', 100)
                ->where('docuflow.pricing.growth.monthly', 300000)
                ->where('docuflow.pricing.growth.setup', 500000)
                ->where('docuflow.pricing.growth.allowance', 300)
                ->where('docuflow.pricing.professional.monthly', 500000)
                ->where('docuflow.pricing.professional.setup', 750000)
                ->where('docuflow.pricing.professional.allowance', 750)
                ->where('docuflow.pricing.terms.overage', config('docuflow.pricing.terms.overage'))
                ->where('docuflow.pricing.terms.cancellation', config('docuflow.pricing.terms.cancellation')));
    }
}
