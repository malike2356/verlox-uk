<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VaEngagement extends Model
{
    protected $table = 'va_engagements';

    /** @var list<string> */
    public static array $statuses = ['draft', 'active', 'paused', 'ended'];

    protected $fillable = [
        'va_client_account_id', 'va_assistant_id', 'tier', 'hours_per_month',
        'client_rate_monthly_gbp', 'va_hourly_rate_gbp', 'start_date', 'end_date', 'status', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'client_rate_monthly_gbp' => 'decimal:2',
            'va_hourly_rate_gbp' => 'decimal:2',
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    public function clientAccount(): BelongsTo
    {
        return $this->belongsTo(VaClientAccount::class, 'va_client_account_id');
    }

    public function assistant(): BelongsTo
    {
        return $this->belongsTo(VaAssistant::class, 'va_assistant_id');
    }

    public function timeLogs(): HasMany
    {
        return $this->hasMany(VaTimeLog::class, 'va_engagement_id');
    }

    public function estimatedMonthlyVaCost(): string
    {
        $hours = (string) $this->hours_per_month;
        $rate = (string) $this->va_hourly_rate_gbp;

        return bcmul($rate, $hours, 2);
    }

    public function estimatedGrossProfit(): string
    {
        $revenue = (string) $this->client_rate_monthly_gbp;
        $cost = $this->estimatedMonthlyVaCost();

        return bcsub($revenue, $cost, 2);
    }
}
