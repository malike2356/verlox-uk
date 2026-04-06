@extends('layouts.mis')

@section('title', 'Booking')
@section('heading', 'Booking')

@section('content')
    <div class="rounded-2xl border border-gray-200 dark:border-slate-800 bg-white/90 dark:bg-slate-900/40 p-4 text-sm space-y-2 max-w-lg">
        <p><span class="text-gray-500 dark:text-slate-500">Name:</span> <span class="text-gray-900 dark:text-white">{{ $booking->contact_name }}</span></p>
        <p><span class="text-gray-500 dark:text-slate-500">Email:</span> <span class="text-gray-900 dark:text-white">{{ $booking->contact_email }}</span></p>
        <p><span class="text-gray-500 dark:text-slate-500">Starts:</span> <span class="text-gray-900 dark:text-white font-mono">{{ $booking->starts_at->timezone($booking->timezone)->format('Y-m-d H:i') }}</span></p>
        <p><span class="text-gray-500 dark:text-slate-500">Ends:</span> <span class="text-gray-900 dark:text-white font-mono">{{ $booking->ends_at->timezone($booking->timezone)->format('Y-m-d H:i') }}</span></p>
        @if($booking->meeting_url)
            <p><a href="{{ $booking->meeting_url }}" class="text-verlox-accent break-all">{{ $booking->meeting_url }}</a></p>
        @endif
        <form method="post" action="{{ route('mis.bookings.status', $booking) }}">@csrf @method('patch')
            <select name="status" onchange="this.form.submit()" class="rounded-lg border border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-950 px-2 py-1 text-gray-900 dark:text-white">
                <option value="confirmed" @selected($booking->status === 'confirmed')>confirmed</option>
                <option value="cancelled" @selected($booking->status === 'cancelled')>cancelled</option>
            </select>
        </form>
        @if(auth()->user()->is_admin)
            <form method="post" action="{{ route('mis.bookings.destroy', $booking) }}" class="pt-2 border-t border-gray-200 dark:border-slate-800" onsubmit="return confirm('Permanently remove this booking from the system?');">@csrf @method('delete')
                <button type="submit" class="text-xs text-red-400">Delete booking (admin)</button>
            </form>
        @endif
    </div>
@endsection
