@extends('layouts.marketing-site')

@section('title', 'Contact | '.$settings->company_name)

@push('head')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" crossorigin="anonymous">
@endpush

@push('vite')
    @vite(['resources/js/marketing-home.js'])
@endpush

@section('content')
@php
    $contactEmail = $settings->support_email ?: 'contact@verlox.uk';
    $contactMailto = 'mailto:'.$contactEmail;
    $t = fn(string $key, string $fallback) => e($blocks->get($key)?->body ?: $fallback);
    $h = function(string $key, string $fallback) use ($blocks): string {
        $b = $blocks->get($key);
        if (!$b || ($b->body === null || $b->body === '')) return $fallback;
        return match($b->type) { 'html' => $b->body, 'textarea' => nl2br(e($b->body)), default => e($b->body) };
    };
@endphp

@include('marketing.partials.topbar')

<main id="main">
    <section class="section section--alt">
        <div class="container">
            <header class="section__head reveal">
                <p class="section__eyebrow">{!! $t('contact_eyebrow', 'Contact') !!}</p>
                <h1 class="section__title">{!! $t('contact_title', 'Leave your details - we will follow up') !!}</h1>
                <p class="section__subtitle">{!! $h('contact_subtitle', 'Tell us what you do and what you are interested in. We will come back with the next step.') !!}</p>
            </header>

            <div class="contact">
                <form class="form reveal" id="contact-form" method="post" action="{{ route('leads.store') }}">
                    @csrf
                    <input type="hidden" name="source" value="web:contact-page">
                    <input type="text" name="company" tabindex="-1" autocomplete="off" value="" style="display:none" aria-hidden="true">
                    <input type="hidden" name="form_ts" value="">

                    @if (session('status'))
                        <p class="form__status form__status--ok" role="status">{{ session('status') }}</p>
                    @endif
                    @if ($errors->any())
                        <p class="form__status form__status--err" role="alert">{{ $errors->first() }}</p>
                    @endif

                    <div class="form__grid">
                        <label class="field">
                            <span class="field__label">Name</span>
                            <input class="field__input" name="contact_name" autocomplete="name" value="{{ old('contact_name') }}" required />
                        </label>
                        <label class="field">
                            <span class="field__label">Email</span>
                            <input class="field__input" name="email" type="email" autocomplete="email" value="{{ old('email') }}" required />
                        </label>
                        <label class="field">
                            <span class="field__label">Phone</span>
                            <input class="field__input" name="phone" autocomplete="tel" value="{{ old('phone') }}" />
                        </label>
                        <label class="field">
                            <span class="field__label">Business type</span>
                            <input class="field__input" name="business_type" autocomplete="organization-title" value="{{ old('business_type') }}" placeholder="e.g. SaaS, agency, trades, e-commerce" />
                        </label>
                        <label class="field field--full">
                            <span class="field__label">What are you looking for?</span>
                            <select class="field__input" name="interest">
                                @php($interest = old('interest', 'service'))
                                <option value="service" @selected($interest === 'service')>A service (build, automation, security)</option>
                                <option value="product" @selected($interest === 'product')>A product / package</option>
                                <option value="network" @selected($interest === 'network')>Just networking - keep in touch</option>
                            </select>
                        </label>
                        <label class="field field--full">
                            <span class="field__label">Service or product (optional)</span>
                            <select class="field__input" name="offering_id">
                                <option value="">Not sure yet</option>
                                @foreach ($offerings as $o)
                                    <option value="{{ $o->id }}" @selected((string) old('offering_id') === (string) $o->id)>{{ $o->name }}</option>
                                @endforeach
                            </select>
                        </label>
                        <label class="field field--full">
                            <span class="field__label">Notes (optional)</span>
                            <textarea class="field__input field__textarea" name="message" rows="5"
                                      placeholder="A few lines about what you need, timelines, and any links…">{{ old('message') }}</textarea>
                        </label>
                    </div>

                    <div class="form__actions">
                        <button class="btn btn--primary" type="submit">Send details</button>
                        <p class="form__note">We respond from <a href="{{ $contactMailto }}">{{ $contactEmail }}</a>.</p>
                    </div>
                </form>

                <aside class="contact__info reveal reveal--delay-2" aria-label="Company info">
                    <span class="contact__info-label">Company</span>
                    <p class="contact__info-name">{{ $settings->company_name }}</p>
                    <p class="contact__info-meta">Engineering &amp; delivery, United Kingdom</p>
                    <div class="contact__info-links">
                        <a class="contact__info-link" href="{{ $contactMailto }}">{{ $contactEmail }}</a>
                        <a class="contact__info-link" href="{{ url('/') }}">{{ parse_url(config('app.url'), PHP_URL_HOST) ?: request()->getHost() }}</a>
                    </div>
                    <div class="contact__info-divs">
                        <h3>Divisions</h3>
                        <p class="contact__div-item">
                            <strong>Verlox Cyber</strong>
                            Cybersecurity services, reviews, hardening, and incident support.
                        </p>
                        <p class="contact__div-item">
                            <strong>Verlox IT</strong>
                            IT services, managed support, systems delivery, and operations.
                        </p>
                    </div>
                </aside>
            </div>
        </div>
    </section>
</main>

@include('marketing.partials.footer')
@endsection

