<?php

namespace App\Http\Controllers\Mis;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\Lead;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DataExportController extends Controller
{
    public function leads(): StreamedResponse
    {
        $filename = 'leads-'.now()->format('Y-m-d').'.csv';

        return response()->streamDownload(function (): void {
            $out = fopen('php://output', 'w');
            fputcsv($out, [
                'id', 'contact_name', 'email', 'company', 'status', 'stage', 'deal_value_pence',
                'expected_close', 'source', 'utm_source', 'utm_medium', 'utm_campaign', 'created_at',
            ]);
            Lead::query()
                ->with('pipelineStage')
                ->orderByDesc('id')
                ->chunk(500, function ($chunk) use ($out): void {
                    foreach ($chunk as $l) {
                        fputcsv($out, [
                            $l->id,
                            $l->contact_name,
                            $l->email,
                            $l->company_name,
                            $l->status,
                            $l->pipelineStage?->name,
                            $l->deal_value_pence,
                            $l->expected_close_date?->format('Y-m-d'),
                            $l->source,
                            $l->utm_source,
                            $l->utm_medium,
                            $l->utm_campaign,
                            $l->created_at?->toIso8601String(),
                        ]);
                    }
                });
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function invoices(): StreamedResponse
    {
        $filename = 'invoices-'.now()->format('Y-m-d').'.csv';

        return response()->streamDownload(function (): void {
            $out = fopen('php://output', 'w');
            fputcsv($out, [
                'number', 'client', 'status', 'total_pence', 'paid_pence', 'issued_at', 'due_at', 'lead_id',
            ]);
            Invoice::query()
                ->with('client')
                ->orderByDesc('id')
                ->chunk(500, function ($chunk) use ($out): void {
                    foreach ($chunk as $inv) {
                        fputcsv($out, [
                            $inv->number,
                            $inv->client?->contact_name,
                            $inv->status,
                            $inv->total_pence,
                            $inv->paid_pence,
                            $inv->issued_at?->format('Y-m-d'),
                            $inv->due_at?->format('Y-m-d'),
                            $inv->lead_id,
                        ]);
                    }
                });
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function expenses(): StreamedResponse
    {
        $filename = 'expenses-'.now()->format('Y-m-d').'.csv';

        return response()->streamDownload(function (): void {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['id', 'description', 'amount_pence', 'currency', 'date', 'category', 'created_at']);
            Expense::query()->orderByDesc('id')->chunk(500, function ($chunk) use ($out): void {
                foreach ($chunk as $e) {
                    fputcsv($out, [
                        $e->id,
                        $e->description,
                        $e->amount_pence,
                        $e->currency,
                        $e->date?->format('Y-m-d'),
                        $e->category,
                        $e->created_at?->toIso8601String(),
                    ]);
                }
            });
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
