<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VaCommunicationLog extends Model
{
    protected $table = 'va_communication_logs';

    protected $fillable = [
        'related_type', 'va_client_account_id', 'va_assistant_id', 'type', 'summary', 'details', 'created_by',
    ];

    public function clientAccount(): BelongsTo
    {
        return $this->belongsTo(VaClientAccount::class, 'va_client_account_id');
    }

    public function assistant(): BelongsTo
    {
        return $this->belongsTo(VaAssistant::class, 'va_assistant_id');
    }
}
