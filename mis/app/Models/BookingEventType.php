<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BookingEventType extends Model
{
    protected $fillable = ['name', 'slug', 'description', 'duration_minutes', 'color', 'price_pence', 'price_caption', 'is_active', 'sort_order'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'price_pence' => 'integer',
        ];
    }

    /**
     * Shown on the public booking widget (marketing + embed).
     */
    public function priceLabel(): ?string
    {
        if ($this->price_caption !== null && $this->price_caption !== '') {
            return $this->price_caption;
        }
        if ($this->price_pence !== null) {
            return '£'.number_format($this->price_pence / 100, 2);
        }

        return null;
    }

    public function questions(): HasMany
    {
        return $this->hasMany(BookingQuestion::class, 'event_type_id')->orderBy('sort_order');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'event_type_id');
    }
}
