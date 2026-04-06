<?php

namespace App\Services;

use App\Models\CalendarIntegration;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class GoogleCalendarService
{
    private const AUTH_URL = 'https://accounts.google.com/o/oauth2/v2/auth';

    private const TOKEN_URL = 'https://oauth2.googleapis.com/token';

    private const FREEBUSY_URL = 'https://www.googleapis.com/calendar/v3/freeBusy';

    public function authUrl(string $redirectUri): string
    {
        return self::AUTH_URL.'?'.http_build_query([
            'client_id' => config('services.google.client_id'),
            'redirect_uri' => $redirectUri,
            'response_type' => 'code',
            'scope' => 'https://www.googleapis.com/auth/calendar.readonly',
            'access_type' => 'offline',
            'prompt' => 'consent',
        ]);
    }

    public function exchangeCode(string $code, string $redirectUri): CalendarIntegration
    {
        $res = Http::asForm()->post(self::TOKEN_URL, [
            'code' => $code,
            'client_id' => config('services.google.client_id'),
            'client_secret' => config('services.google.client_secret'),
            'redirect_uri' => $redirectUri,
            'grant_type' => 'authorization_code',
        ])->throw()->json();

        $profile = Http::withToken($res['access_token'])
            ->get('https://www.googleapis.com/oauth2/v2/userinfo')
            ->json();

        return CalendarIntegration::updateOrCreate(
            ['provider' => 'google'],
            [
                'access_token' => $res['access_token'],
                'refresh_token' => $res['refresh_token'] ?? null,
                'token_expires_at' => now()->addSeconds($res['expires_in'] ?? 3600),
                'owner_email' => $profile['email'] ?? null,
            ]
        );
    }

    public function refreshToken(CalendarIntegration $integration): void
    {
        $res = Http::asForm()->post(self::TOKEN_URL, [
            'client_id' => config('services.google.client_id'),
            'client_secret' => config('services.google.client_secret'),
            'refresh_token' => $integration->refresh_token,
            'grant_type' => 'refresh_token',
        ])->throw()->json();

        $integration->update([
            'access_token' => $res['access_token'],
            'token_expires_at' => now()->addSeconds($res['expires_in'] ?? 3600),
        ]);
    }

    /**
     * Returns busy intervals for a date range as array of [start, end] Carbon pairs.
     *
     * @return array<array{start: Carbon, end: Carbon}>
     */
    public function busySlots(Carbon $from, Carbon $to): array
    {
        $integration = CalendarIntegration::google();
        if (! $integration || ! $integration->access_token) {
            return [];
        }

        if ($integration->isTokenExpired()) {
            $this->refreshToken($integration);
            $integration->refresh();
        }

        $calendarId = $integration->calendar_id ?: 'primary';

        $res = Http::withToken($integration->access_token)
            ->post(self::FREEBUSY_URL, [
                'timeMin' => $from->toIso8601String(),
                'timeMax' => $to->toIso8601String(),
                'items' => [['id' => $calendarId]],
            ])->json();

        $busy = $res['calendars'][$calendarId]['busy'] ?? [];

        return array_map(fn ($b) => [
            'start' => Carbon::parse($b['start']),
            'end' => Carbon::parse($b['end']),
        ], $busy);
    }
}
