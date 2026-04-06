<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingDateOverride extends Model
{
    protected $fillable = ['date', 'type', 'start_time', 'end_time', 'note'];

    protected $casts = ['date' => 'date'];
}
