<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_the_login_page()
    {
        $response = $this->get(route('dashboard'));
        $response->assertRedirect(route('login'));
    }

    public function test_support_agents_are_redirected_from_the_legacy_dashboard_to_the_inbox(): void
    {
        $user = User::factory()->create();
        $user->forceFill(['is_support_agent' => true])->save();

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertRedirect(route('support.conversations.index'));
    }

    public function test_other_users_are_redirected_from_the_legacy_dashboard_to_the_website(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertRedirect(route('home'));
    }
}
