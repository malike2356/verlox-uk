<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BookingQuestion extends Model
{
    protected $fillable = ['event_type_id', 'label', 'field_type', 'options', 'is_required', 'sort_order'];

    protected $casts = [
        'options' => 'array',
        'is_required' => 'boolean',
    ];

    public function eventType(): BelongsTo
    {
        return $this->belongsTo(BookingEventType::class, 'event_type_id');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(BookingAnswer::class, 'question_id');
    }
}
