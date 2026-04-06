<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OfferingType extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'display_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function offerings(): HasMany
    {
        return $this->hasMany(Offering::class);
    }
}

