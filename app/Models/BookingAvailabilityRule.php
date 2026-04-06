<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingAvailabilityRule extends Model
{
    protected $fillable = ['weekday', 'start_time', 'end_time'];
}
