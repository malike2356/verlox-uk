@extends('layouts.marketing-site')

@section('title', 'Virtual assistants | '.$settings->company_name)

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
    <section class="va-page-hero">
        <div class="container">
            <div class="va-page-hero__inner reveal">
                <p class="va-page-hero__eyebrow">{!! $t('va_hero_eyebrow', 'Managed service') !!}</p>
                <h1 class="va-page-hero__title">{!! $t('va_hero_title', 'Virtual assistants for UK businesses') !!}</h1>
                <p class="va-page-hero__lede">
                    {!! $h('va_hero_lede', $settings->company_name.' sources, vets, and manages skilled virtual assistants - you get one accountable partner, predictable monthly hours, and UK-facing contracts and data protection - without running international payroll yourself.') !!}
                </p>
                <div class="va-page-hero__cta">
                    <a class="btn btn--primary" href="{{ route('marketing.book') }}">Book a discovery call</a>
                    <a class="btn btn--ghost" href="#enquiry">Request a proposal</a>
                </div>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <header class="section__head section__head--left reveal">
                <p class="section__eyebrow">{!! $t('va_why_eyebrow', 'Why teams use us') !!}</p>
                <h2 class="section__title">{!! $t('va_why_title', 'Operations support, without the hiring overhead') !!}</h2>
            </header>
            <div class="va-value-grid">
                <div class="va-value-card reveal">
                    <i class="fa-solid fa-shield-halved va-value-card__icon" aria-hidden="true"></i>
                    <h3 class="va-value-card__title">{!! $t('va_card_1_title', 'Single point of contact') !!}</h3>
                    <p class="va-value-card__desc">{!! $h('va_card_1_desc', 'You contract with us; we handle quality, replacements, and performance.') !!}</p>
                </div>
                <div class="va-value-card reveal reveal--delay-1">
                    <i class="fa-solid fa-clock va-value-card__icon" aria-hidden="true"></i>
                    <h3 class="va-value-card__title">{!! $t('va_card_2_title', 'Retainer or hourly tiers') !!}</h3>
                    <p class="va-value-card__desc">{!! $h('va_card_2_desc', 'Structured monthly hours with clear overage rates.') !!}</p>
                </div>
                <div class="va-value-card reveal reveal--delay-2">
                    <i class="fa-solid fa-user-check va-value-card__icon" aria-hidden="true"></i>
                    <h3 class="va-value-card__title">{!! $t('va_card_3_title', 'Vetted assistants') !!}</h3>
                    <p class="va-value-card__desc">{!! $h('va_card_3_desc', 'Contractors engaged under robust agreements.') !!}</p>
                </div>
            </div>
        </div>
    </section>

    <section class="section section--alt">
        <div class="container">
            <header class="section__head section__head--left reveal">
                <p class="section__eyebrow">{!! $t('va_skills_eyebrow', 'Capabilities') !!}</p>
                <h2 class="section__title">{!! $t('va_skills_title', 'What a VA can take off your plate') !!}</h2>
                <p class="section__subtitle">{!! $h('va_skills_subtitle', 'Typical workstreams we place and supervise - scope is agreed per client in writing.') !!}</p>
            </header>
            <ul class="va-skill-list reveal">
                {!! $h('va_skills_list', '<li><i class="fa-solid fa-check va-skill-list__mark" aria-hidden="true"></i> General administration - inbox, scheduling, data entry, travel</li><li><i class="fa-solid fa-check va-skill-list__mark" aria-hidden="true"></i> Social media - scheduling, community, light reporting</li><li><i class="fa-solid fa-check va-skill-list__mark" aria-hidden="true"></i> Customer service - email/chat, CRM updates, triage</li>') !!}
            </ul>
        </div>
    </section>

    @include('marketing.partials.pricing-plans-section', [
        'sectionId' => 'va-pricing',
        'eyebrow'   => $blocks->get('va_pricing_eyebrow')?->body  ?: 'Retainers',
        'title'     => $blocks->get('va_pricing_title')?->body    ?: 'Typical VA engagement tiers',
        'subtitle'  => $blocks->get('va_pricing_subtitle')?->body ?: 'Indicative packages managed in the MIS - hours, tools, and commercials are agreed in writing per client.',
    ])

    <section class="section" id="enquiry">
        <div class="container">
            <div class="va-enquiry reveal">
                <div>
                    <p class="section__eyebrow">{!! $t('va_enquiry_eyebrow', 'Next step') !!}</p>
                    <h2 class="section__title">{!! $t('va_enquiry_title', 'Tell us what you need covered') !!}</h2>
                    <p class="section__subtitle">{!! $h('va_enquiry_subtitle', "We'll reply with tier options, indicative hours, and onboarding steps. No obligation.") !!}</p>
                    <p class="va-enquiry__meta">
                        <a class="btn btn--primary" href="{{ route('marketing.book') }}">Book a call</a>
                    </p>
                </div>
                <form class="form" method="post" action="{{ route('leads.store') }}">
                    @csrf
                    <input type="hidden" name="source" value="web:va">
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
                            <span class="field__label">Company (optional)</span>
                            <input class="field__input" name="company_name" autocomplete="organization" value="{{ old('company_name') }}" />
                        </label>
                        <label class="field field--full">
                            <span class="field__label">Hours, tools, and outcomes</span>
                            <textarea class="field__input field__textarea" name="message" rows="5" required
                                placeholder="Roughly how many hours per month? Which tools (Google, Microsoft, CRM)? What should “done” look like?">{{ old('message') }}</textarea>
                        </label>
                    </div>
                    <div class="form__actions">
                        <button class="btn btn--primary" type="submit">Send enquiry</button>
                        <p class="form__note">We respond from <a href="{{ $contactMailto }}">{{ $contactEmail }}</a>.</p>
                    </div>
                </form>
            </div>
        </div>
    </section>
</main>

@include('marketing.partials.footer')
@endsection
