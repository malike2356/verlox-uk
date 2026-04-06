<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VaNpsResponse extends Model
{
    protected $table = 'va_nps_responses';

    protected $fillable = [
        'va_client_account_id', 'score', 'comment', 'period_month', 'period_year', 'submitted_at',
    ];

    protected function casts(): array
    {
        return [
            'submitted_at' => 'datetime',
        ];
    }

    public function clientAccount(): BelongsTo
    {
        return $this->belongsTo(VaClientAccount::class, 'va_client_account_id');
    }
}
