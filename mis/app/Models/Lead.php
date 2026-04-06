<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Lead extends Model
{
    /** @var list<string> */
    public const STATUSES = ['new', 'contacted', 'qualified', 'lost', 'converted'];

    protected $fillable = [
        'pipeline_stage_id', 'offering_id', 'assigned_user_id',
        'company_name', 'contact_name', 'email', 'phone', 'message', 'source', 'status', 'meta',
        'deal_value_pence', 'expected_close_date', 'loss_reason',
        'utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content',
    ];

    protected function casts(): array
    {
        return [
            'meta' => 'array',
            'expected_close_date' => 'date',
            'deal_value_pence' => 'integer',
        ];
    }

    public function activities(): HasMany
    {
        return $this->hasMany(LeadActivity::class)->orderByDesc('created_at');
    }

    public function scopeOpen(Builder $q): Builder
    {
        return $q->whereNotIn('status', ['converted', 'lost']);
    }

    public function pipelineStage(): BelongsTo
    {
        return $this->belongsTo(PipelineStage::class);
    }

    public function offering(): BelongsTo
    {
        return $this->belongsTo(Offering::class);
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function client(): HasOne
    {
        return $this->hasOne(Client::class, 'lead_id');
    }
}
