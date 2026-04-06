<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MisGuestAndBoundaryTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login_from_mis_routes(): void
    {
        $this->get(route('mis.dashboard'))->assertRedirect(route('login'));
        $this->get(route('mis.help.index'))->assertRedirect(route('login'));
        $this->get(route('mis.search', ['q' => 'ab']))->assertRedirect(route('login'));
        $this->get(route('mis.leads.index'))->assertRedirect(route('login'));
    }

    public function test_authenticated_user_without_mis_access_gets_403_on_mis(): void
    {
        $user = User::factory()->withoutMisAccess()->create();
        $this->actingAs($user);

        $this->get(route('mis.dashboard'))->assertForbidden();
        $this->get(route('mis.help.index'))->assertForbidden();
        $this->get(route('mis.search', ['q' => 'ab']))->assertForbidden();
    }

    public function test_unverified_user_can_access_profile_which_is_auth_only(): void
    {
        $user = User::factory()->admin()->unverified()->create();
        $this->actingAs($user)
            ->get(route('profile.edit'))
            ->assertOk();
    }

    public function test_unverified_user_is_redirected_from_app_dashboard_route(): void
    {
        $user = User::factory()->admin()->unverified()->create();
        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertRedirect(route('verification.notice'));
    }

    public function test_verified_user_without_mis_sees_default_dashboard_not_mis(): void
    {
        $user = User::factory()->withoutMisAccess()->create();
        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk();
    }
}
