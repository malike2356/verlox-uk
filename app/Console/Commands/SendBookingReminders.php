<?php

namespace App\Console\Commands;

use App\Mail\BookingReminderMail;
use App\Models\Booking;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendBookingReminders extends Command
{
    protected $signature = 'bookings:send-reminders';

    protected $description = 'Send 24h and 1h reminder emails for upcoming confirmed bookings';

    public function handle(): int
    {
        $now = now()->utc();

        // 24h window: bookings starting 23h–25h from now that haven't had a reminder
        $start24 = $now->copy()->addHours(23);
        $end24 = $now->copy()->addHours(25);

        Booking::query()
            ->where('status', 'confirmed')
            ->whereNull('reminder_24h_sent_at')
            ->whereBetween('starts_at', [$start24, $end24])
            ->each(function (Booking $booking): void {
                Mail::to($booking->contact_email)->send(new BookingReminderMail($booking, '24h'));
                $booking->update(['reminder_24h_sent_at' => now()]);
                $this->info("24h reminder sent → {$booking->contact_email} (#{$booking->id})");
            });

        // 1h window: bookings starting 45min–75min from now
        $start1h = $now->copy()->addMinutes(45);
        $end1h = $now->copy()->addMinutes(75);

        Booking::query()
            ->where('status', 'confirmed')
            ->whereNull('reminder_1h_sent_at')
            ->whereBetween('starts_at', [$start1h, $end1h])
            ->each(function (Booking $booking): void {
                Mail::to($booking->contact_email)->send(new BookingReminderMail($booking, '1h'));
                $booking->update(['reminder_1h_sent_at' => now()]);
                $this->info("1h reminder sent → {$booking->contact_email} (#{$booking->id})");
            });

        return self::SUCCESS;
    }
}
