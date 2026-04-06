<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MisAccessAndRolesTest extends TestCase
{
    use RefreshDatabase;

    public function test_unverified_user_is_redirected_from_mis_routes(): void
    {
        $user = User::factory()->admin()->unverified()->create();

        $this->actingAs($user)
            ->get(route('mis.dashboard'))
            ->assertRedirect(route('verification.notice'));
    }

    public function test_user_without_mis_role_cannot_access_mis(): void
    {
        $user = User::factory()->withoutMisAccess()->create();

        $this->actingAs($user)
            ->get(route('mis.dashboard'))
            ->assertForbidden();
    }

    public function test_finance_user_can_open_standard_mis_routes(): void
    {
        $user = User::factory()->misFinance()->create();
        $this->actingAs($user);

        foreach ([
            'mis.dashboard',
            'mis.help.index',
            'mis.leads.index',
            'mis.clients.index',
            'mis.finance.dashboard',
            'mis.invoices.index',
        ] as $route) {
            $this->get(route($route))->assertOk();
        }
    }

    public function test_va_only_user_redirected_from_crm_to_va_dashboard(): void
    {
        $user = User::factory()->misVa()->create();
        $this->actingAs($user);

        $this->get(route('mis.leads.index'))
            ->assertRedirect(route('mis.va.dashboard'));

        $this->get(route('mis.clients.index'))
            ->assertRedirect(route('mis.va.dashboard'));
    }

    public function test_va_only_user_can_open_dashboard_help_and_va_routes(): void
    {
        $user = User::factory()->misVa()->create();
        $this->actingAs($user);

        $this->get(route('mis.va.dashboard'))->assertOk();
        $this->get(route('mis.dashboard'))->assertOk();
        $this->get(route('mis.help.index'))->assertOk();
        $this->getJson(route('mis.search', ['q' => '99']))->assertOk();
    }
}
