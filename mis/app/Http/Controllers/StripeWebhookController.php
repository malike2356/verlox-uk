<?php

namespace App\Http\Controllers;

use App\Models\CompanySetting;
use App\Models\Invoice;
use App\Models\Payment;
use App\Services\AccountingSyncService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Stripe\Event;
use Stripe\Stripe;
use Stripe\Webhook;

class StripeWebhookController extends Controller
{
    public function __invoke(Request $request, AccountingSyncService $accounting): Response
    {
        $settings = CompanySetting::current();
        $secret = $settings->stripe_webhook_secret ?: config('services.stripe.webhook_secret');
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');

        if (! $secret || ! $sigHeader) {
            return response('Webhook not configured', 400);
        }

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $secret);
        } catch (\Throwable) {
            return response('Invalid signature', 400);
        }

        if (DB::table('processed_stripe_events')->where('stripe_event_id', $event->id)->exists()) {
            return response('ok', 200);
        }

        try {
            DB::transaction(function () use ($event, $accounting): void {
                if ($event->type === 'checkout.session.completed') {
                    $this->handleCheckoutCompleted($event, $accounting);
                }
                DB::table('processed_stripe_events')->insert([
                    'stripe_event_id' => $event->id,
                    'processed_at' => now(),
                ]);
            });
        } catch (\Throwable $e) {
            Log::error('stripe_webhook_failed', [
                'event_id' => $event->id,
                'type' => $event->type,
                'error' => $e->getMessage(),
            ]);

            return response('error', 500);
        }

        return response('ok', 200);
    }

    private function handleCheckoutCompleted(Event $event, AccountingSyncService $accounting): void
    {
        $session = $event->data->object;
        $invoiceId = $session->metadata->invoice_id ?? null;
        if (! $invoiceId) {
            return;
        }

        $invoice = Invoice::query()->lockForUpdate()->find($invoiceId);
        if (! $invoice || $invoice->status === 'paid') {
            return;
        }

        $settings = CompanySetting::current();
        $apiKey = $settings->stripe_secret_key ?: config('services.stripe.secret');
        if ($apiKey) {
            Stripe::setApiKey($apiKey);
        }

        $paymentIntentId = $session->payment_intent ?? null;
        $sessionTotalPence = isset($session->amount_total) ? (int) $session->amount_total : (int) $invoice->total_pence;

        $newPaid = min((int) $invoice->total_pence, (int) $invoice->paid_pence + $sessionTotalPence);

        $status = $newPaid >= (int) $invoice->total_pence ? 'paid' : 'partial';

        $invoice->update([
            'status' => $status,
            'paid_pence' => $newPaid,
            'stripe_payment_intent_id' => $paymentIntentId ?? $invoice->stripe_payment_intent_id,
        ]);

        Payment::create([
            'invoice_id' => $invoice->id,
            'amount_pence' => $sessionTotalPence,
            'stripe_payment_intent_id' => $paymentIntentId,
            'status' => 'succeeded',
            'raw' => ['session_id' => $session->id ?? null, 'event_id' => $event->id],
        ]);

        $accounting->syncInvoiceToZoho($invoice->fresh(['client', 'lines', 'offering', 'quotation']));
    }
}
