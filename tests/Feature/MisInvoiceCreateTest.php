<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MisInvoiceCreateTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_creates_draft_invoice_with_lines_and_redirects_to_show(): void
    {
        $user = User::factory()->admin()->create();
        $client = Client::query()->create([
            'contact_name' => 'Acme Ltd',
            'email' => 'acme-'.uniqid('', true).'@example.test',
        ]);

        $this->actingAs($user)->post(route('mis.invoices.store'), [
            'client_id' => $client->id,
            'currency' => 'GBP',
            'issued_at' => '2026-04-01',
            'due_at' => '2026-04-15',
            'lines' => [
                ['description' => 'Widget setup', 'quantity' => 2, 'unit_price' => 50.00],
                ['description' => 'Support', 'quantity' => 1, 'unit_price' => 25.50],
            ],
        ])->assertRedirect();

        $invoice = Invoice::query()->where('client_id', $client->id)->first();
        $this->assertNotNull($invoice);
        $this->assertSame('draft', $invoice->status);
        $this->assertSame(12550, $invoice->subtotal_pence);
        $this->assertSame(2510, $invoice->tax_pence);
        $this->assertSame(15060, $invoice->total_pence);
        $this->assertCount(2, $invoice->lines);
    }
}
