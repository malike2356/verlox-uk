<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    /** @var list<string> */
    public const STATUSES = ['draft', 'sent', 'partial', 'paid', 'overdue', 'written_off'];

    protected $fillable = [
        'number', 'client_id', 'quotation_id', 'contract_id', 'offering_id', 'lead_id', 'status',
        'stripe_checkout_session_id', 'stripe_payment_intent_id',
        'issued_at', 'due_at', 'sent_at', 'last_reminder_at', 'next_reminder_at', 'written_off_at',
        'currency', 'subtotal_pence', 'tax_pence', 'total_pence', 'paid_pence',
        'zoho_invoice_id',
    ];

    protected function casts(): array
    {
        return [
            'issued_at' => 'date',
            'due_at' => 'date',
            'sent_at' => 'datetime',
            'last_reminder_at' => 'datetime',
            'next_reminder_at' => 'datetime',
            'written_off_at' => 'datetime',
        ];
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function balanceOutstandingPence(): int
    {
        return max(0, (int) $this->total_pence - (int) $this->paid_pence);
    }

    public function scopeReceivable(Builder $q): Builder
    {
        return $q->whereIn('status', ['sent', 'partial', 'overdue'])
            ->whereNull('written_off_at');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function quotation(): BelongsTo
    {
        return $this->belongsTo(Quotation::class);
    }

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }

    public function offering(): BelongsTo
    {
        return $this->belongsTo(Offering::class);
    }

    public function lines(): HasMany
    {
        return $this->hasMany(InvoiceLine::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function recalculateTotals(): void
    {
        $subtotal = (int) $this->lines()->sum('line_total_pence');
        $this->subtotal_pence = $subtotal;
        $this->tax_pence = (int) round($subtotal * 0.2);
        $this->total_pence = $this->subtotal_pence + $this->tax_pence;
        $this->save();
    }
}
