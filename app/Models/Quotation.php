<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Quotation extends Model
{
    protected $fillable = [
        'number', 'client_id', 'lead_id', 'status', 'valid_until', 'currency',
        'subtotal_pence', 'tax_pence', 'total_pence', 'terms', 'zoho_estimate_id',
    ];

    protected function casts(): array
    {
        return [
            'valid_until' => 'date',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function lines(): HasMany
    {
        return $this->hasMany(QuotationLine::class);
    }

    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
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
