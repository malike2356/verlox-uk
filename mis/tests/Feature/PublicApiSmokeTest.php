<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicApiSmokeTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_json_endpoints_return_ok(): void
    {
        $this->getJson(route('public.content-blocks'))
            ->assertOk()
            ->assertJsonStructure(['blocks']);

        $this->getJson(route('public.event-types'))
            ->assertOk()
            ->assertJsonStructure(['event_types']);

        $this->getJson(route('public.booking.questions'))
            ->assertOk()
            ->assertJsonStructure(['questions']);
    }

    public function test_public_booking_calendar_and_slots_accept_valid_params(): void
    {
        $this->getJson(route('public.booking.calendar', [
            'year' => 2026,
            'month' => 4,
            'timezone' => 'Europe/London',
        ]))
            ->assertOk()
            ->assertJsonStructure(['days']);

        $this->getJson(route('public.booking.slots', [
            'date' => '2026-04-15',
            'timezone' => 'Europe/London',
        ]))
            ->assertOk()
            ->assertJsonStructure(['slots']);
    }

    public function test_checkout_success_page_returns_ok(): void
    {
        $this->get(route('checkout.success'))->assertOk();
    }
}
