<?php

namespace App\Http\Controllers\Mis;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BookingAvailabilityRule;
use App\Models\BookingDateOverride;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function index(Request $request): View
    {
        $view = $request->query('view', 'list');
        $bookings = Booking::query()
            ->with('lead')
            ->orderBy('starts_at')
            ->paginate(30);

        return view('mis.bookings.index', compact('bookings', 'view'));
    }

    public function calendarFeed(): JsonResponse
    {
        $bookings = Booking::query()
            ->where('status', '!=', 'cancelled')
            ->where('starts_at', '>=', now()->subMonths(1))
            ->orderBy('starts_at')
            ->limit(500)
            ->get();

        $events = $bookings->map(fn (Booking $b) => [
            'id' => $b->id,
            'title' => $b->contact_name.' ('.$b->contact_email.')',
            'start' => $b->starts_at->toIso8601String(),
            'end' => $b->ends_at->toIso8601String(),
            'url' => route('mis.bookings.show', $b),
            'backgroundColor' => '#0ea5e9',
            'borderColor' => '#0284c7',
        ]);

        return response()->json($events);
    }

    public function show(Booking $booking): View
    {
        $booking->load('lead');

        return view('mis.bookings.show', compact('booking'));
    }

    public function updateStatus(Request $request, Booking $booking): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:confirmed,cancelled'],
        ]);
        $booking->update(['status' => $data['status']]);

        return back()->with('status', 'Booking updated.');
    }

    public function availability(): View
    {
        $rules = BookingAvailabilityRule::query()->orderBy('weekday')->orderBy('start_time')->get();
        $overrides = BookingDateOverride::query()->orderBy('date')->get();

        return view('mis.bookings.availability', compact('rules', 'overrides'));
    }

    public function storeOverride(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'date' => ['required', 'date', 'after_or_equal:today'],
            'type' => ['required', 'in:unavailable,hours'],
            'start_time' => ['required_if:type,hours', 'nullable', 'date_format:H:i'],
            'end_time' => ['required_if:type,hours', 'nullable', 'date_format:H:i', 'after:start_time'],
            'note' => ['nullable', 'string', 'max:200'],
        ]);

        BookingDateOverride::create([
            'date' => $data['date'],
            'type' => $data['type'],
            'start_time' => $data['type'] === 'hours' ? ($data['start_time'].':00') : null,
            'end_time' => $data['type'] === 'hours' ? ($data['end_time'].':00') : null,
            'note' => $data['note'] ?? null,
        ]);

        return back()->with('status', 'Date override saved.');
    }

    public function destroyOverride(BookingDateOverride $bookingDateOverride): RedirectResponse
    {
        $bookingDateOverride->delete();

        return back()->with('status', 'Override removed.');
    }

    public function storeRule(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'weekday' => ['required', 'integer', 'min:0', 'max:6'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
        ]);
        BookingAvailabilityRule::create([
            'weekday' => $data['weekday'],
            'start_time' => $data['start_time'].':00',
            'end_time' => $data['end_time'].':00',
        ]);

        return back()->with('status', 'Availability rule added.');
    }

    public function destroyRule(BookingAvailabilityRule $bookingAvailabilityRule): RedirectResponse
    {
        $bookingAvailabilityRule->delete();

        return back()->with('status', 'Rule removed.');
    }

    public function destroy(Booking $booking): RedirectResponse
    {
        $booking->delete();

        return redirect()->route('mis.bookings.index')->with('status', 'Booking removed from the calendar.');
    }
}
