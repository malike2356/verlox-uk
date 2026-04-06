<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CalendarIntegration extends Model
{
    protected $fillable = [
        'provider', 'access_token', 'refresh_token',
        'token_expires_at', 'calendar_id', 'owner_email',
    ];

    protected $casts = [
        'token_expires_at' => 'datetime',
    ];

    protected $hidden = ['access_token', 'refresh_token'];

    public static function google(): ?self
    {
        return static::where('provider', 'google')->first();
    }

    public function isTokenExpired(): bool
    {
        return $this->token_expires_at && $this->token_expires_at->isPast();
    }
}
