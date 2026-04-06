<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\BookingAvailabilityRule;
use App\Models\BookingDateOverride;
use App\Models\BookingEventType;
use App\Models\CompanySetting;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class BookingSlotService
{
    public function slotsForDate(
        string $date,
        string $timezone = 'Europe/London',
        ?int $ignoreBookingId = null,
        ?int $eventTypeId = null
    ): Collection {
        $settings = CompanySetting::current();

        // Use event type duration if provided, else fall back to company setting
        $eventType = $eventTypeId
            ? BookingEventType::find($eventTypeId)
            : null;
        $slotMinutes = $eventType
            ? max(15, (int) $eventType->duration_minutes)
            : max(15, (int) $settings->booking_slot_minutes);

        $bufferMinutes = max(0, (int) $settings->booking_buffer_minutes);
        $minNoticeHours = max(0, (int) $settings->booking_min_notice_hours);
        $maxDaysAhead = max(1, (int) $settings->booking_max_days_ahead);

        $day = Carbon::parse($date, $timezone)->startOfDay();
        $today = Carbon::now($timezone)->startOfDay();
        $lastBookable = $today->copy()->addDays($maxDaysAhead);

        if ($day->lt($today) || $day->gt($lastBookable)) {
            return collect();
        }

        // Check for date-specific overrides first
        $overrides = BookingDateOverride::query()
            ->whereDate('date', $day->toDateString())
            ->get();

        if ($overrides->where('type', 'unavailable')->isNotEmpty()) {
            return collect(); // Date is blocked
        }

        // Use override hours if set, otherwise fall back to weekly rules
        if ($overrides->where('type', 'hours')->isNotEmpty()) {
            $rules = $overrides->where('type', 'hours')->map(fn ($o) => (object) [
                'start_time' => $o->start_time,
                'end_time' => $o->end_time,
            ]);
        } else {
            $dayOfWeek = $day->dayOfWeek;
            $rules = BookingAvailabilityRule::query()->where('weekday', $dayOfWeek)->get();
            if ($rules->isEmpty()) {
                return collect();
            }
        }

        $minStart = Carbon::now($timezone)->addHours($minNoticeHours);

        // Fetch Google Calendar busy slots for this day
        $dayStart = Carbon::parse($date, $timezone)->startOfDay()->utc();
        $dayEnd = Carbon::parse($date, $timezone)->endOfDay()->utc();
        $googleBusy = [];
        try {
            $googleBusy = app(GoogleCalendarService::class)->busySlots($dayStart, $dayEnd);
        } catch (\Throwable) {
            // Google Calendar unavailable — proceed without it
        }

        $slots = collect();
        foreach ($rules as $rule) {
            $start = Carbon::parse($date.' '.$rule->start_time, $timezone);
            $end = Carbon::parse($date.' '.$rule->end_time, $timezone);
            while ($start->copy()->addMinutes($slotMinutes)->lte($end)) {
                $slotEnd = $start->copy()->addMinutes($slotMinutes);
                if ($slotEnd->gt($end)) {
                    break;
                }
                if ($start->lt($minStart)) {
                    $start->addMinutes($slotMinutes);

                    continue;
                }
                $slotStartUtc = $start->copy()->utc();
                $slotEndUtc = $slotEnd->copy()->utc();
                $googleBlocked = false;
                foreach ($googleBusy as $busy) {
                    if ($slotStartUtc->lt($busy['end']) && $slotEndUtc->gt($busy['start'])) {
                        $googleBlocked = true;
                        break;
                    }
                }

                if (! $googleBlocked && ! $this->overlapsBooked($slotStartUtc, $slotEndUtc, $bufferMinutes, $ignoreBookingId)) {
                    $slots->push([
                        'start' => $slotStartUtc->toIso8601String(),
                        'end' => $slotEndUtc->toIso8601String(),
                        'label' => $start->format('H:i').' – '.$slotEnd->format('H:i'),
                    ]);
                }
                $start->addMinutes($slotMinutes);
            }
        }

        return $slots->values();
    }

    /**
     * @param  string  $startIso  UTC ISO8601
     * @param  string  $endIso  UTC ISO8601
     */
    public function assertSlotBookable(
        string $startIso,
        string $endIso,
        string $timezone,
        ?int $ignoreBookingId = null,
        ?int $eventTypeId = null
    ): void {
        $settings = CompanySetting::current();
        $eventType = $eventTypeId ? BookingEventType::find($eventTypeId) : null;
        $slotMinutes = $eventType
            ? max(15, (int) $eventType->duration_minutes)
            : max(15, (int) $settings->booking_slot_minutes);
        $bufferMinutes = max(0, (int) $settings->booking_buffer_minutes);
        $minNoticeHours = max(0, (int) $settings->booking_min_notice_hours);
        $maxDaysAhead = max(1, (int) $settings->booking_max_days_ahead);

        $slotStart = Carbon::parse($startIso)->utc();
        $slotEnd = Carbon::parse($endIso)->utc();

        if ($slotEnd->lte($slotStart)) {
            throw ValidationException::withMessages(['start' => ['Invalid slot range.']]);
        }

        $duration = (int) round($slotStart->diffInMinutes($slotEnd));
        if ($duration !== $slotMinutes) {
            throw ValidationException::withMessages(['start' => ['Slot length does not match configured duration.']]);
        }

        $localStart = $slotStart->copy()->timezone($timezone);
        $localDay = $localStart->format('Y-m-d');
        $allowed = $this->slotsForDate($localDay, $timezone, $ignoreBookingId, $eventTypeId);
        $match = $allowed->first(function (array $s) use ($slotStart, $slotEnd) {
            return Carbon::parse($s['start'])->equalTo($slotStart) && Carbon::parse($s['end'])->equalTo($slotEnd);
        });
        if (! $match) {
            throw ValidationException::withMessages(['start' => ['This time is no longer available.']]);
        }

        $minStartUtc = Carbon::now($timezone)->addHours($minNoticeHours)->utc();
        if ($slotStart->lt($minStartUtc)) {
            throw ValidationException::withMessages(['start' => ['Too soon. Choose a later time.']]);
        }

        $maxEndUtc = Carbon::now($timezone)->addDays($maxDaysAhead)->endOfDay()->utc();
        if ($slotStart->gt($maxEndUtc)) {
            throw ValidationException::withMessages(['start' => ['Too far in the future.']]);
        }

        if ($this->overlapsBooked($slotStart, $slotEnd, $bufferMinutes, $ignoreBookingId)) {
            throw ValidationException::withMessages(['start' => ['This time was just taken.']]);
        }
    }

    private function overlapsBooked(Carbon $slotStart, Carbon $slotEnd, int $bufferMinutes, ?int $ignoreBookingId = null): bool
    {
        $expandStart = $slotStart->copy()->subMinutes($bufferMinutes);
        $expandEnd = $slotEnd->copy()->addMinutes($bufferMinutes);

        return Booking::query()
            ->when($ignoreBookingId, fn ($q) => $q->where('id', '!=', $ignoreBookingId))
            ->where('status', '!=', 'cancelled')
            ->where(function ($q) use ($expandStart, $expandEnd) {
                $q->whereBetween('starts_at', [$expandStart, $expandEnd])
                    ->orWhereBetween('ends_at', [$expandStart, $expandEnd])
                    ->orWhere(function ($q2) use ($expandStart, $expandEnd) {
                        $q2->where('starts_at', '<=', $expandStart)->where('ends_at', '>=', $expandEnd);
                    });
            })
            ->exists();
    }
}
