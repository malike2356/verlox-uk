<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VaTimeLog extends Model
{
    protected $table = 'va_time_logs';

    protected $fillable = [
        'va_engagement_id', 'va_assistant_id', 'va_client_account_id', 'work_date',
        'hours_logged', 'task_description', 'is_approved', 'approved_by', 'approved_at',
    ];

    protected function casts(): array
    {
        return [
            'work_date' => 'date',
            'hours_logged' => 'decimal:2',
            'is_approved' => 'boolean',
            'approved_at' => 'datetime',
        ];
    }

    public function engagement(): BelongsTo
    {
        return $this->belongsTo(VaEngagement::class, 'va_engagement_id');
    }

    public function assistant(): BelongsTo
    {
        return $this->belongsTo(VaAssistant::class, 'va_assistant_id');
    }

    public function clientAccount(): BelongsTo
    {
        return $this->belongsTo(VaClientAccount::class, 'va_client_account_id');
    }
}
