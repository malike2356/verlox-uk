<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * GET smoke test for marketing pages, profile, and MIS index/create screens (no route parameters).
 */
class MisPagesSmokeTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_marketing_pages_return_ok(): void
    {
        foreach ([
            'marketing.home',
            'marketing.virtual-assistant',
            'marketing.book',
            'embed.booking',
        ] as $name) {
            $response = $this->get(route($name));
            $response->assertOk();
        }
    }

    public function test_mis_get_pages_return_ok_for_verified_admin(): void
    {
        $user = User::factory()->admin()->create();

        $this->actingAs($user);

        $routes = [
            'mis.dashboard',
            'mis.help.index',
            'mis.network.index',
            'mis.leads.index',
            'mis.clients.index',
            'mis.clients.create',
            'mis.pipeline.index',
            'mis.pipeline.stages.index',
            'mis.pipeline.stages.create',
            'mis.conversations.index',
            'mis.conversations.create',
            'mis.documents.index',
            'mis.documents.create',
            'mis.quotations.index',
            'mis.quotations.create',
            'mis.contracts.index',
            'mis.contract-templates.index',
            'mis.contract-templates.create',
            'mis.finance.dashboard',
            'mis.finance.receivables',
            'mis.finance.expenses.index',
            'mis.exports.leads',
            'mis.invoices.index',
            'mis.invoices.create',
            'mis.bookings.index',
            'mis.bookings.availability',
            'mis.event-types.index',
            'mis.offerings.index',
            'mis.offerings.create',
            'mis.pricing-plans.index',
            'mis.pricing-plans.create',
            'mis.content-blocks.index',
            'mis.users.index',
            'mis.users.create',
            'mis.settings.edit',
            'mis.zoho.index',
            'mis.va.dashboard',
            'mis.va.assistants.index',
            'mis.va.assistants.create',
            'mis.va.client-accounts.index',
            'mis.va.client-accounts.create',
            'mis.va.time-logs.index',
            'mis.va.time-logs.create',
            'profile.edit',
        ];

        foreach ($routes as $name) {
            $response = $this->get(route($name));
            $this->assertSame(
                200,
                $response->getStatusCode(),
                "Expected 200 for route [{$name}]"
            );
        }

        $calendar = $this->get(route('mis.bookings.index', ['view' => 'calendar']));
        $this->assertSame(200, $calendar->getStatusCode(), 'Expected 200 for mis.bookings.index?view=calendar');
    }
}
