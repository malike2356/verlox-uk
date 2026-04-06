<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Contract extends Model
{
    protected $fillable = [
        'number', 'contract_template_id', 'quotation_id', 'client_id', 'status',
        'body_snapshot', 'signed_at', 'effective_from', 'effective_until',
    ];

    protected function casts(): array
    {
        return [
            'signed_at' => 'datetime',
            'effective_from' => 'date',
            'effective_until' => 'date',
        ];
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(ContractTemplate::class, 'contract_template_id');
    }

    public function quotation(): BelongsTo
    {
        return $this->belongsTo(Quotation::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }
}
