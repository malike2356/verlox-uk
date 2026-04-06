<?php

namespace App\Http\Controllers;

use App\Mail\BookingConfirmedMail;
use App\Models\Booking;
use App\Models\BookingEventType;
use App\Models\BookingQuestion;
use App\Models\CompanySetting;
use App\Models\Lead;
use App\Models\PipelineStage;
use App\Services\BookingSlotService;
use App\Services\IcsBuilder;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\View\View;

class PublicBookingController extends Controller
{
    // ── Public API: event types ───────────────────────────────────────

    public function eventTypes(): JsonResponse
    {
        $types = BookingEventType::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get(['id', 'name', 'slug', 'description', 'duration_minutes', 'color', 'price_pence', 'price_caption']);

        return response()->json([
            'event_types' => $types->map(fn (BookingEventType $t) => [
                'id' => $t->id,
                'name' => $t->name,
                'slug' => $t->slug,
                'description' => $t->description,
                'duration_minutes' => $t->duration_minutes,
                'color' => $t->color,
                'price_label' => $t->priceLabel(),
            ]),
        ]);
    }

    // ── Public API: intake questions ──────────────────────────────────

    public function questions(Request $request): JsonResponse
    {
        $data = $request->validate([
            'event_type_id' => ['nullable', 'integer', 'exists:booking_event_types,id'],
        ]);

        $questions = BookingQuestion::query()
            ->where(function ($q) use ($data) {
                $q->whereNull('event_type_id');
                if (! empty($data['event_type_id'])) {
                    $q->orWhere('event_type_id', $data['event_type_id']);
                }
            })
            ->orderBy('sort_order')
            ->get(['id', 'label', 'field_type', 'options', 'is_required']);

        return response()->json(['questions' => $questions]);
    }

    // ── Slot & calendar endpoints ─────────────────────────────────────

    public function slots(Request $request, BookingSlotService $slots): JsonResponse
    {
        $data = $request->validate([
            'date' => ['required', 'date'],
            'timezone' => ['nullable', 'string', 'max:64'],
            'event_type_id' => ['nullable', 'integer'],
            'reschedule_booking_id' => ['nullable', 'integer'],
            'manage_token' => ['nullable', 'string', 'max:64'],
        ]);
        $tz = $data['timezone'] ?? 'Europe/London';
        $ignoreId = $this->resolveRescheduleIgnoreId(
            $data['reschedule_booking_id'] ?? null,
            $data['manage_token'] ?? null
        );

        return response()->json([
            'slots' => $slots->slotsForDate($data['date'], $tz, $ignoreId, $data['event_type_id'] ?? null),
        ]);
    }

    public function calendar(Request $request, BookingSlotService $slots): JsonResponse
    {
        $data = $request->validate([
            'year' => ['required', 'integer', 'min:2000', 'max:2100'],
            'month' => ['required', 'integer', 'min:1', 'max:12'],
            'timezone' => ['nullable', 'string', 'max:64'],
            'event_type_id' => ['nullable', 'integer'],
            'reschedule_booking_id' => ['nullable', 'integer'],
            'manage_token' => ['nullable', 'string', 'max:64'],
        ]);
        $tz = $data['timezone'] ?? 'Europe/London';
        $ignoreId = $this->resolveRescheduleIgnoreId(
            $data['reschedule_booking_id'] ?? null,
            $data['manage_token'] ?? null
        );

        $month = (int) $data['month'];
        $start = Carbon::create((int) $data['year'], $month, 1, 0, 0, 0, $tz);
        $days = [];
        for ($d = $start->copy(); $d->month === $month; $d->addDay()) {
            $dateStr = $d->format('Y-m-d');
            $days[(string) $d->day] = $slots->slotsForDate($dateStr, $tz, $ignoreId, $data['event_type_id'] ?? null)->isNotEmpty();
        }

        return response()->json(['days' => $days]);
    }

    public function store(Request $request, BookingSlotService $slotService): JsonResponse
    {
        $data = $request->validate([
            'start' => ['required', 'date'],
            'end' => ['required', 'date', 'after:start'],
            'contact_name' => ['required', 'string', 'max:255'],
            'contact_email' => ['required', 'email', 'max:255'],
            'timezone' => ['nullable', 'string', 'max:64'],
            'event_type_id' => ['nullable', 'integer', 'exists:booking_event_types,id'],
            'answers' => ['nullable', 'array'],
            'answers.*' => ['nullable', 'string', 'max:2000'],
            'create_lead' => ['nullable', 'boolean'],
        ]);

        $settings = CompanySetting::current();
        $tz = $data['timezone'] ?? ($settings->booking_timezone ?: 'Europe/London');
        $meetingUrl = $settings->meeting_link_template;
        $eventTypeId = $data['event_type_id'] ?? null;

        $startIso = Carbon::parse($data['start'])->utc()->toIso8601String();
        $endIso = Carbon::parse($data['end'])->utc()->toIso8601String();

        $slotService->assertSlotBookable($startIso, $endIso, $tz, null, $eventTypeId);

        $leadId = null;
        if ($request->boolean('create_lead')) {
            $stage = PipelineStage::query()->orderBy('sort_order')->first();
            if ($stage) {
                $lead = Lead::create([
                    'pipeline_stage_id' => $stage->id,
                    'contact_name' => $data['contact_name'],
                    'email' => $data['contact_email'],
                    'source' => 'booking',
                    'message' => 'Scheduled via public calendar.',
                    'status' => 'new',
                ]);
                $leadId = $lead->id;
            }
        }

        if ($leadId === null) {
            $matchLead = Lead::query()
                ->whereRaw('LOWER(email) = ?', [strtolower($data['contact_email'])])
                ->whereNotIn('status', ['converted', 'lost'])
                ->first();
            if ($matchLead) {
                $leadId = $matchLead->id;
            }
        }

        $booking = Booking::create([
            'event_type_id' => $eventTypeId,
            'starts_at' => $data['start'],
            'ends_at' => $data['end'],
            'contact_name' => $data['contact_name'],
            'contact_email' => $data['contact_email'],
            'meeting_url' => $meetingUrl,
            'status' => 'confirmed',
            'lead_id' => $leadId,
            'timezone' => $tz,
        ]);

        // Save intake answers
        if (! empty($data['answers'])) {
            foreach ($data['answers'] as $questionId => $answer) {
                if ($answer !== null && $answer !== '') {
                    $booking->answers()->create([
                        'question_id' => $questionId,
                        'answer' => $answer,
                    ]);
                }
            }
        }

        Mail::to($booking->contact_email)->send(new BookingConfirmedMail($booking));

        return response()->json([
            'ok' => true,
            'booking_id' => $booking->id,
            'ics_download' => URL::temporarySignedRoute(
                'public.booking.ics',
                now()->addDays(14),
                ['booking' => $booking->id]
            ),
            'manage_url' => route('public.booking.manage', [
                'booking' => $booking->id,
                'token' => $booking->manage_token,
            ]),
        ]);
    }

    public function embed(Request $request): View
    {
        $settings = CompanySetting::current();

        return view('embed.booking', [
            'settings' => $settings,
            'rescheduleBooking' => null,
            'rescheduleToken' => null,
        ]);
    }

    public function manage(Booking $booking, string $token): View|RedirectResponse
    {
        if (! $booking->tokenMatches($token)) {
            abort(404);
        }

        $settings = CompanySetting::current();

        return view('booking.manage', [
            'booking' => $booking,
            'token' => $token,
            'settings' => $settings,
        ]);
    }

    public function cancel(Request $request, Booking $booking, string $token): RedirectResponse
    {
        if (! $booking->tokenMatches($token)) {
            abort(404);
        }
        if ($booking->status === 'cancelled') {
            return back()->with('status', 'Already cancelled.');
        }
        $booking->update(['status' => 'cancelled']);

        return back()->with('status', 'Your booking has been cancelled.');
    }

    public function rescheduleForm(Booking $booking, string $token): View|RedirectResponse
    {
        if (! $booking->tokenMatches($token)) {
            abort(404);
        }
        if ($booking->status === 'cancelled') {
            return redirect()->route('public.booking.manage', [$booking, $token])
                ->with('error', 'Cancelled bookings cannot be rescheduled.');
        }

        $settings = CompanySetting::current();

        return view('embed.booking', [
            'settings' => $settings,
            'rescheduleBooking' => $booking,
            'rescheduleToken' => $token,
        ]);
    }

    public function reschedule(Request $request, Booking $booking, string $token, BookingSlotService $slotService): JsonResponse
    {
        if (! $booking->tokenMatches($token)) {
            abort(404);
        }
        if ($booking->status === 'cancelled') {
            return response()->json(['message' => 'Booking is cancelled.'], 422);
        }

        $data = $request->validate([
            'start' => ['required', 'date'],
            'end' => ['required', 'date', 'after:start'],
            'timezone' => ['nullable', 'string', 'max:64'],
        ]);

        $settings = CompanySetting::current();
        $tz = $data['timezone'] ?? $booking->timezone ?? ($settings->booking_timezone ?: 'Europe/London');

        $startIso = Carbon::parse($data['start'])->utc()->toIso8601String();
        $endIso = Carbon::parse($data['end'])->utc()->toIso8601String();

        $slotService->assertSlotBookable($startIso, $endIso, $tz, $booking->id);

        $booking->update([
            'starts_at' => $data['start'],
            'ends_at' => $data['end'],
            'timezone' => $tz,
        ]);

        Mail::to($booking->contact_email)->send(new BookingConfirmedMail($booking));

        return response()->json([
            'ok' => true,
            'manage_url' => route('public.booking.manage', [
                'booking' => $booking->id,
                'token' => $booking->manage_token,
            ]),
        ]);
    }

    public function ics(Booking $booking): Response
    {
        $ics = app(IcsBuilder::class)->forBooking($booking);

        $filename = IcsBuilder::attachmentFilename();

        return response($ics, 200, [
            'Content-Type' => 'text/calendar; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }

    private function resolveRescheduleIgnoreId(?int $bookingId, ?string $token): ?int
    {
        if (! $bookingId || ! $token) {
            return null;
        }
        $booking = Booking::query()->find($bookingId);
        if (! $booking || ! $booking->tokenMatches($token)) {
            return null;
        }

        return $booking->id;
    }
}
