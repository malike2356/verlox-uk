@extends('layouts.marketing')

@section('content')
<div class="min-h-screen flex items-center justify-center px-4 py-12">
    <div class="w-full max-w-md rounded-[22px] border border-gray-200 dark:border-slate-800 bg-white/95 dark:bg-[#0F223C]/90 backdrop-blur p-6 shadow-sm dark:shadow-none">
        <h1 class="text-xl font-display font-semibold text-gray-900 dark:text-white">{{ $offering->name }}</h1>
        @if($offering->summary)<p class="mt-2 text-sm text-gray-600 dark:text-[#B8C0D8]">{{ $offering->summary }}</p>@endif
        <p class="mt-4 text-2xl font-mono text-verlox-accent">{{ number_format($offering->price_pence / 100, 2) }} {{ $offering->currency }} <span class="text-sm text-gray-500 dark:text-[#9AA5B9] font-sans">ex VAT, plus VAT at checkout</span></p>
        @if ($errors->any())
            <ul class="mt-4 text-sm text-red-600 dark:text-red-400 list-disc ms-4">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        @endif
        <form method="post" action="{{ route('checkout.start', $offering) }}" class="mt-6 space-y-3 text-sm">
            @csrf
            <div><label class="text-xs text-gray-500 dark:text-[#9AA5B9]">Name</label><input name="contact_name" value="{{ old('contact_name') }}" required class="mt-1 w-full rounded-xl border border-gray-300 dark:border-slate-700 bg-white dark:bg-[#0F223C] px-3 py-2 text-gray-900 dark:text-white"></div>
            <div><label class="text-xs text-gray-500 dark:text-[#9AA5B9]">Email</label><input type="email" name="email" value="{{ old('email') }}" required class="mt-1 w-full rounded-xl border border-gray-300 dark:border-slate-700 bg-white dark:bg-[#0F223C] px-3 py-2 text-gray-900 dark:text-white"></div>
            <div><label class="text-xs text-gray-500 dark:text-[#9AA5B9]">Company</label><input name="company_name" value="{{ old('company_name') }}" class="mt-1 w-full rounded-xl border border-gray-300 dark:border-slate-700 bg-white dark:bg-[#0F223C] px-3 py-2 text-gray-900 dark:text-white"></div>
            <button type="submit" class="w-full rounded-full bg-verlox-accent text-on-verlox-accent py-3 text-sm font-semibold hover:opacity-90 transition-opacity">Pay with Stripe</button>
        </form>
        <p class="mt-4 text-xs text-gray-500 dark:text-[#9AA5B9]"><a href="{{ route('marketing.home') }}" class="text-verlox-accent text-verlox-accent-hover">Back</a></p>
    </div>
</div>
@endsection
