<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VaClientAccount extends Model
{
    protected $table = 'va_client_accounts';

    /** @var list<string> */
    public static array $tiers = ['starter', 'growth', 'professional', 'enterprise', 'adhoc'];

    /** @var list<string> */
    public static array $statuses = ['prospect', 'onboarding', 'active', 'paused', 'churned'];

    protected $fillable = [
        'mis_client_id', 'company_name', 'contact_name', 'email', 'phone', 'tier', 'status',
        'monthly_rate_gbp', 'hours_included', 'overage_rate_gbp', 'contract_start', 'contract_end',
        'minimum_term_end', 'stripe_customer_id', 'account_manager', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'monthly_rate_gbp' => 'decimal:2',
            'overage_rate_gbp' => 'decimal:2',
            'contract_start' => 'date',
            'contract_end' => 'date',
            'minimum_term_end' => 'date',
        ];
    }

    public function misClient(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'mis_client_id');
    }

    public function engagements(): HasMany
    {
        return $this->hasMany(VaEngagement::class, 'va_client_account_id');
    }

    public function timeLogs(): HasMany
    {
        return $this->hasMany(VaTimeLog::class, 'va_client_account_id');
    }

    public function npsResponses(): HasMany
    {
        return $this->hasMany(VaNpsResponse::class, 'va_client_account_id');
    }

    public function communicationLogs(): HasMany
    {
        return $this->hasMany(VaCommunicationLog::class, 'va_client_account_id');
    }
}
