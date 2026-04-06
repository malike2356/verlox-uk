<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\Lead;
use App\Models\PipelineStage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MisSmartSearchTest extends TestCase
{
    use RefreshDatabase;

    private function makeStage(): PipelineStage
    {
        return PipelineStage::query()->create([
            'name' => 'New',
            'sort_order' => 0,
            'color_hex' => '#64748b',
        ]);
    }

    public function test_search_returns_json_categories_for_admin(): void
    {
        $user = User::factory()->admin()->create();
        $stage = $this->makeStage();
        Lead::query()->create([
            'pipeline_stage_id' => $stage->id,
            'company_name' => 'Acme SearchCo',
            'contact_name' => 'Pat Example',
            'email' => 'pat@example.test',
            'status' => 'new',
        ]);

        $response = $this->actingAs($user)->getJson(route('mis.search', ['q' => 'Acme']));

        $response->assertOk();
        $response->assertJsonStructure(['categories']);
        $data = $response->json('categories');
        $this->assertNotEmpty($data);
        $this->assertSame('Leads', $data[0]['title']);
        $this->assertStringContainsString('Acme', $data[0]['items'][0]['label']);
    }

    public function test_search_matches_numeric_lead_id(): void
    {
        $user = User::factory()->admin()->create();
        $stage = $this->makeStage();
        $lead = Lead::query()->create([
            'pipeline_stage_id' => $stage->id,
            'company_name' => 'Hidden Name XYZ123',
            'contact_name' => 'X',
            'email' => 'x-'.uniqid('', true).'@example.test',
            'status' => 'new',
        ]);

        $response = $this->actingAs($user)->getJson(route('mis.search', ['q' => (string) $lead->id]));

        $response->assertOk();
        $categories = $response->json('categories');
        $this->assertNotEmpty($categories);
        $leadCat = collect($categories)->firstWhere('title', 'Leads');
        $this->assertNotNull($leadCat);
        $this->assertNotEmpty($leadCat['items']);
    }

    public function test_search_includes_help_links_for_keyword_queries(): void
    {
        $user = User::factory()->admin()->create();

        $response = $this->actingAs($user)->getJson(route('mis.search', ['q' => 'pipeline']));
        $response->assertOk();

        $categories = $response->json('categories');
        $helpCat = collect($categories)->firstWhere('title', 'Help');
        $this->assertNotNull($helpCat);
        $this->assertNotEmpty($helpCat['items'] ?? []);
        $this->assertStringContainsString('#wf-pipeline', (string) ($helpCat['items'][0]['url'] ?? ''));
    }

    public function test_search_finds_invoice_by_client_company_when_number_unrelated(): void
    {
        $user = User::factory()->admin()->create();
        $client = Client::query()->create([
            'lead_id' => null,
            'company_name' => 'Globex Holdings Ltd',
            'contact_name' => 'Ada',
            'email' => 'ada@globex.test',
        ]);
        Invoice::query()->create([
            'number' => 'INV-ZZ-999',
            'client_id' => $client->id,
            'status' => 'draft',
            'currency' => 'GBP',
        ]);

        $response = $this->actingAs($user)->getJson(route('mis.search', ['q' => 'Globex']));

        $response->assertOk();
        $categories = $response->json('categories');
        $invCat = collect($categories)->firstWhere('title', 'Invoices');
        $this->assertNotNull($invCat);
        $this->assertNotEmpty($invCat['items']);
        $this->assertStringContainsString('INV-ZZ-999', (string) ($invCat['items'][0]['label'] ?? ''));
    }

    public function test_va_user_search_is_scoped(): void
    {
        $user = User::factory()->misVa()->create();
        $stage = $this->makeStage();
        Lead::query()->create([
            'pipeline_stage_id' => $stage->id,
            'company_name' => 'Should Not Appear For VA',
            'contact_name' => 'Y',
            'email' => 'y-'.uniqid('', true).'@example.test',
            'status' => 'new',
        ]);

        $response = $this->actingAs($user)->getJson(route('mis.search', ['q' => 'Should']));

        $response->assertOk();
        $categories = $response->json('categories');
        $titles = collect($categories)->pluck('title')->all();
        $this->assertNotContains('Leads', $titles);
    }
}
