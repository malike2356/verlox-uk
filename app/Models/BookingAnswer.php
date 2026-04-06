<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingAnswer extends Model
{
    protected $fillable = ['booking_id', 'question_id', 'answer'];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(BookingQuestion::class, 'question_id');
    }
}
