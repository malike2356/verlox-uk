<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * All mis.super GET routes must return 403 for non-admin MIS users (e.g. finance).
 */
class MisSuperRoutesForbiddenTest extends TestCase
{
    use RefreshDatabase;

    public function test_finance_user_gets_403_on_every_super_admin_get_route(): void
    {
        $user = User::factory()->misFinance()->create();
        $this->actingAs($user);

        $routes = [
            'mis.contract-templates.index',
            'mis.contract-templates.create',
            'mis.event-types.index',
            'mis.bookings.availability',
            'mis.offerings.index',
            'mis.offerings.create',
            'mis.pricing-plans.index',
            'mis.pricing-plans.create',
            'mis.pipeline.stages.index',
            'mis.pipeline.stages.create',
            'mis.content-blocks.index',
            'mis.users.index',
            'mis.users.create',
            'mis.settings.edit',
            'mis.google-calendar.connect',
        ];

        foreach ($routes as $name) {
            $response = $this->get(route($name));
            $this->assertSame(
                403,
                $response->getStatusCode(),
                "Expected 403 for route [{$name}] as finance user"
            );
        }
    }
}
