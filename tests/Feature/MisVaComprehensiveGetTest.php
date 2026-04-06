<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * VA-only users: allowed overview routes + full VA module; CRM-style routes redirect.
 */
class MisVaComprehensiveGetTest extends TestCase
{
    use RefreshDatabase;

    public function test_va_user_gets_200_on_allowed_mis_routes(): void
    {
        $user = User::factory()->misVa()->create();
        $this->actingAs($user);

        foreach ([
            'mis.dashboard',
            'mis.help.index',
            'mis.network.index',
            'mis.va.dashboard',
            'mis.va.assistants.index',
            'mis.va.assistants.create',
            'mis.va.client-accounts.index',
            'mis.va.client-accounts.create',
            'mis.va.time-logs.index',
            'mis.va.time-logs.create',
        ] as $name) {
            $this->get(route($name))
                ->assertOk();
        }
    }

    public function test_va_user_redirected_from_crm_and_finance_routes(): void
    {
        $user = User::factory()->misVa()->create();
        $this->actingAs($user);
        $target = route('mis.va.dashboard');

        foreach ([
            'mis.leads.index',
            'mis.clients.index',
            'mis.pipeline.index',
            'mis.finance.dashboard',
            'mis.invoices.index',
            'mis.zoho.index',
        ] as $name) {
            $this->get(route($name))
                ->assertRedirect($target);
        }
    }
}
