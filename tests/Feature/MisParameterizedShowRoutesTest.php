<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Client;
use App\Models\Contract;
use App\Models\ContractTemplate;
use App\Models\Conversation;
use App\Models\Invoice;
use App\Models\Lead;
use App\Models\PipelineStage;
use App\Models\Quotation;
use App\Models\User;
use App\Models\VaAssistant;
use App\Models\VaClientAccount;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * GET show/edit routes that need real model IDs (admin user).
 */
class MisParameterizedShowRoutesTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->admin()->create();
    }

    public function test_show_and_edit_routes_return_ok_with_seeded_records(): void
    {
        $stage = PipelineStage::query()->create([
            'name' => 'New',
            'sort_order' => 0,
            'color_hex' => '#64748b',
        ]);

        $lead = Lead::query()->create([
            'pipeline_stage_id' => $stage->id,
            'contact_name' => 'Test Lead',
            'email' => 'lead-'.uniqid('', true).'@example.test',
            'status' => 'new',
        ]);

        $client = Client::query()->create([
            'contact_name' => 'Test Client',
            'email' => 'client-'.uniqid('', true).'@example.test',
        ]);

        $quotation = Quotation::query()->create([
            'number' => 'Q-'.uniqid(),
            'client_id' => $client->id,
            'status' => 'draft',
        ]);

        $invoice = Invoice::query()->create([
            'number' => 'I-'.uniqid(),
            'client_id' => $client->id,
            'status' => 'draft',
        ]);

        $template = ContractTemplate::query()->create([
            'name' => 'Standard',
            'slug' => 'std-'.uniqid(),
            'body' => 'Terms…',
            'is_default' => true,
        ]);

        $contract = Contract::query()->create([
            'number' => 'C-'.uniqid(),
            'contract_template_id' => $template->id,
            'client_id' => $client->id,
            'status' => 'draft',
            'body_snapshot' => 'Snapshot',
        ]);

        $booking = Booking::query()->create([
            'starts_at' => now()->addDay(),
            'ends_at' => now()->addDay()->addHour(),
            'contact_name' => 'Booker',
            'contact_email' => 'book-'.uniqid('', true).'@example.test',
        ]);

        $conversation = Conversation::query()->create([
            'client_id' => $client->id,
            'subject' => 'Hello',
        ]);

        $vaAccount = VaClientAccount::query()->create([
            'company_name' => 'VA Co',
            'contact_name' => 'Contact',
            'email' => 'va-acct-'.uniqid('', true).'@example.test',
        ]);

        $assistant = VaAssistant::query()->create([
            'full_name' => 'Assistant One',
            'email' => 'va-asst-'.uniqid('', true).'@example.test',
            'hourly_rate_gbp' => 25,
        ]);

        $this->actingAs($this->admin);

        $routes = [
            route('mis.leads.show', $lead),
            route('mis.clients.show', $client),
            route('mis.clients.edit', $client),
            route('mis.quotations.show', $quotation),
            route('mis.invoices.show', $invoice),
            route('mis.contracts.show', $contract),
            route('mis.bookings.show', $booking),
            route('mis.conversations.show', $conversation),
            route('mis.va.client-accounts.show', $vaAccount),
            route('mis.va.assistants.edit', $assistant),
        ];

        foreach ($routes as $url) {
            $this->get($url)->assertOk();
        }

        $this->assertSame(200, $this->get(route('mis.finance.expenses.index'))->getStatusCode());
    }
}
