<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VaAssistant extends Model
{
    protected $table = 'va_assistants';

    /** @var list<string> */
    public static array $availabilities = ['available', 'assigned', 'unavailable', 'inactive'];

    protected $fillable = [
        'full_name', 'email', 'country', 'timezone', 'hourly_rate_gbp', 'skills', 'availability',
        'perform_score', 'wise_email', 'payment_currency', 'phone', 'notes', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'hourly_rate_gbp' => 'decimal:2',
            'perform_score' => 'decimal:2',
            'skills' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function engagements(): HasMany
    {
        return $this->hasMany(VaEngagement::class, 'va_assistant_id');
    }

    public function timeLogs(): HasMany
    {
        return $this->hasMany(VaTimeLog::class, 'va_assistant_id');
    }

    public function communicationLogs(): HasMany
    {
        return $this->hasMany(VaCommunicationLog::class, 'va_assistant_id');
    }
}
