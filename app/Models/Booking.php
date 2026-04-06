<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Booking extends Model
{
    protected $fillable = [
        'event_type_id', 'starts_at', 'ends_at', 'contact_name', 'contact_email',
        'meeting_url', 'status', 'lead_id', 'internal_notes', 'timezone',
        'manage_token', 'reminder_24h_sent_at', 'reminder_1h_sent_at',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'reminder_24h_sent_at' => 'datetime',
            'reminder_1h_sent_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Booking $booking): void {
            if (empty($booking->manage_token)) {
                $booking->manage_token = Str::random(48);
            }
        });
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function eventType(): BelongsTo
    {
        return $this->belongsTo(BookingEventType::class, 'event_type_id');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(BookingAnswer::class)->with('question');
    }

    public function tokenMatches(?string $token): bool
    {
        return is_string($token)
            && $this->manage_token !== null
            && hash_equals($this->manage_token, $token);
    }
}
