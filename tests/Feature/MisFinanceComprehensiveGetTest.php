<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Every non-super MIS GET route a finance user should be allowed to open (no route parameters).
 */
class MisFinanceComprehensiveGetTest extends TestCase
{
    use RefreshDatabase;

    public function test_finance_user_gets_200_on_all_standard_mis_get_routes(): void
    {
        $user = User::factory()->misFinance()->create();
        $this->actingAs($user);

        $routes = [
            'mis.dashboard',
            'mis.help.index',
            'mis.network.index',
            'mis.leads.index',
            'mis.clients.index',
            'mis.clients.create',
            'mis.pipeline.index',
            'mis.conversations.index',
            'mis.conversations.create',
            'mis.documents.index',
            'mis.documents.create',
            'mis.quotations.index',
            'mis.quotations.create',
            'mis.contracts.index',
            'mis.finance.dashboard',
            'mis.finance.receivables',
            'mis.finance.expenses.index',
            'mis.exports.leads',
            'mis.exports.invoices',
            'mis.exports.expenses',
            'mis.invoices.index',
            'mis.invoices.create',
            'mis.bookings.index',
            'mis.zoho.index',
            'mis.va.dashboard',
            'mis.va.assistants.index',
            'mis.va.assistants.create',
            'mis.va.client-accounts.index',
            'mis.va.client-accounts.create',
            'mis.va.time-logs.index',
            'mis.va.time-logs.create',
        ];

        foreach ($routes as $name) {
            $response = $this->get(route($name));
            $this->assertSame(
                200,
                $response->getStatusCode(),
                "Expected 200 for route [{$name}] as finance user"
            );
        }

        $this->get(route('mis.bookings.index', ['view' => 'calendar']))->assertOk();
        $this->get(route('mis.bookings.calendar'))->assertOk();
    }
}
