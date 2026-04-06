<?php

namespace App\Http\Controllers\Mis;

use App\Http\Controllers\Controller;
use App\Models\CalendarIntegration;
use App\Services\GoogleCalendarService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class GoogleCalendarController extends Controller
{
    public function redirect(GoogleCalendarService $google): RedirectResponse
    {
        $redirectUri = route('mis.google-calendar.callback');

        return redirect($google->authUrl($redirectUri));
    }

    public function callback(Request $request, GoogleCalendarService $google): RedirectResponse
    {
        if ($request->has('error')) {
            return redirect()->route('mis.settings.edit')
                ->with('error', 'Google Calendar access denied.');
        }

        try {
            $redirectUri = route('mis.google-calendar.callback');
            $google->exchangeCode($request->input('code'), $redirectUri);
        } catch (\Throwable $e) {
            logger()->error('Google Calendar OAuth failed', ['error' => $e->getMessage()]);

            return redirect()->route('mis.settings.edit')
                ->with('error', 'Google Calendar connection failed: '.$e->getMessage());
        }

        return redirect()->route('mis.settings.edit')
            ->with('status', 'Google Calendar connected.');
    }

    public function disconnect(): RedirectResponse
    {
        CalendarIntegration::where('provider', 'google')->delete();

        return redirect()->route('mis.settings.edit')
            ->with('status', 'Google Calendar disconnected.');
    }
}
