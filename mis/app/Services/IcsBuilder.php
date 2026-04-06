<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\CompanySetting;
use Illuminate\Support\Str;

class IcsBuilder
{
    /** Safe ASCII download filename for calendar attachments (company-branded). */
    public static function attachmentFilename(): string
    {
        $slug = Str::slug(CompanySetting::current()->company_name ?? 'booking');

        return ($slug !== '' ? $slug : 'booking').'-meeting.ics';
    }

    public function forBooking(Booking $booking): string
    {
        $settings = CompanySetting::current();
        $uid = 'velox-booking-'.$booking->id.'@verlox';
        $dtStamp = gmdate('Ymd\THis\Z');
        $start = $booking->starts_at->copy()->timezone('UTC')->format('Ymd\THis\Z');
        $end = $booking->ends_at->copy()->timezone('UTC')->format('Ymd\THis\Z');
        $summary = 'Meeting with '.$settings->company_name;
        $desc = $booking->meeting_url ? 'Join: '.$booking->meeting_url : '';

        $lines = [
            'BEGIN:VCALENDAR',
            'VERSION:2.0',
            'PRODID:-//Velox MIS//Booking//EN',
            'CALSCALE:GREGORIAN',
            'METHOD:PUBLISH',
            'BEGIN:VEVENT',
            'UID:'.$uid,
            'DTSTAMP:'.$dtStamp,
            'DTSTART:'.$start,
            'DTEND:'.$end,
            'SUMMARY:'.$this->escape($summary),
            'DESCRIPTION:'.$this->escape($desc),
        ];
        if ($booking->meeting_url) {
            $lines[] = 'LOCATION:'.$this->escape($booking->meeting_url);
        }
        $lines[] = 'END:VEVENT';
        $lines[] = 'END:VCALENDAR';

        return implode("\r\n", $lines)."\r\n";
    }

    private function escape(string $text): string
    {
        return str_replace(["\r", "\n", ',', ';', '\\'], ['', '\\n', '\\,', '\\;', '\\\\'], $text);
    }
}
