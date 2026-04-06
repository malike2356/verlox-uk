<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MisHelpPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_help_page_is_ok_for_verified_admin(): void
    {
        $user = User::factory()->admin()->create();

        $response = $this->actingAs($user)->get(route('mis.help.index'));

        $response->assertOk();
    }

    public function test_help_page_contains_anchor_sections_and_toc(): void
    {
        $user = User::factory()->admin()->create();
        $html = $this->actingAs($user)->get(route('mis.help.index'))->getContent();

        foreach ([
            'overview',
            'access',
            'navigation',
            'workflows',
            'wf-lead-client',
            'wf-pipeline',
            'wf-quote-invoice',
            'wf-ar',
            'wf-client-hub',
            'wf-bookings',
            'wf-va',
            'wf-exports',
            'modules',
            'data',
            'integrations',
            'public',
            'troubleshooting',
        ] as $id) {
            $this->assertStringContainsString('id="'.$id.'"', $html, 'Missing section anchor #'.$id);
        }

        $this->assertStringContainsString('id="help-toc-heading"', $html);
        $this->assertStringContainsString('aria-labelledby="help-toc-heading"', $html);
        $this->assertStringContainsString(route('mis.network.index'), $html);
        $this->assertStringContainsString(route('mis.leads.index'), $html);
    }

    public function test_help_page_ok_for_finance_and_va_users(): void
    {
        $this->actingAs(User::factory()->misFinance()->create())
            ->get(route('mis.help.index'))
            ->assertOk();

        $this->actingAs(User::factory()->misVa()->create())
            ->get(route('mis.help.index'))
            ->assertOk();
    }

    public function test_help_page_shows_admin_only_module_copy_for_admin(): void
    {
        $user = User::factory()->admin()->create();
        $html = $this->actingAs($user)->get(route('mis.help.index'))->getContent();

        $this->assertStringContainsString('Company settings', $html);
        $this->assertStringContainsString('Offerings &amp; checkout', $html);
    }

    public function test_help_page_hides_admin_only_module_copy_for_finance_user(): void
    {
        $user = User::factory()->misFinance()->create();
        $html = $this->actingAs($user)->get(route('mis.help.index'))->getContent();

        $this->assertStringNotContainsString('Company settings', $html);
        $this->assertStringNotContainsString('Offerings &amp; checkout', $html);
    }
}
