<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PricingPlanFeature extends Model
{
    protected $fillable = ['pricing_plan_id', 'label', 'sort_order', 'is_included'];

    protected function casts(): array
    {
        return [
            'is_included' => 'boolean',
        ];
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(PricingPlan::class, 'pricing_plan_id');
    }
}
