<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Offering extends Model
{
    protected $fillable = [
        'name', 'slug', 'summary', 'description', 'type', 'display_order',
        'price_pence', 'currency', 'stripe_price_id', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class);
    }
}
