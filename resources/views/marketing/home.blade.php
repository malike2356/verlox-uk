@extends('layouts.marketing-site')

@push('vite')
    @vite(['resources/js/marketing-home.js'])
@endpush

@push('head')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" crossorigin="anonymous">
@endpush

@section('content')
@php
    $contactEmail  = $settings->support_email ?: 'contact@verlox.uk';
    $contactMailto = 'mailto:'.$contactEmail;
    // Helper: get plain text from a block, with fallback
    $t = fn(string $key, string $fallback) => e($blocks->get($key)?->body ?: $fallback);
    // Helper: get HTML-safe content (html/textarea types), with fallback
    $h = function(string $key, string $fallback) use ($blocks): string {
        $b = $blocks->get($key);
        if (!$b || ($b->body === null || $b->body === '')) return $fallback;
        return match($b->type) {
            'html'     => $b->body,
            'textarea' => nl2br(e($b->body)),
            default    => e($b->body),
        };
    };
@endphp
@include('marketing.partials.topbar')

<main id="main">

    <section class="hero">
        <div class="hero__bg" aria-hidden="true">
            <div class="hero__orb hero__orb--1"></div>
            <div class="hero__orb hero__orb--2"></div>
            <div class="hero__orb hero__orb--3"></div>
        </div>

        <div class="hero__content">
            <div class="container">
                <div class="hero__inner">
                    <p class="eyebrow">{!! $t('marketing_hero_eyebrow', 'Software Engineering & Automations') !!}</p>
                    <h1 class="hero__title">{!! $h('marketing_hero_title', 'Custom software, AI-powered systems, and cyber-secure websites.') !!}</h1>
                    <div class="hero__subtitle hero__subtitle--rich">{!! $h('marketing_hero_subtitle', '<p class="hero__lede">You need more than code. You need security that holds up, automation that actually works, and people who can manage it all.</p>') !!}</div>
                    <div class="hero__cta">
                        <a class="btn btn--primary" href="{{ route('marketing.contact') }}">Request a callback</a>
                        <a class="btn btn--ghost" href="#work">See our services</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="marquee" aria-hidden="true">
            <div class="marquee__track">
                <span class="marquee__item">Websites</span><span class="marquee__sep">·</span>
                <span class="marquee__item">Platforms</span><span class="marquee__sep">·</span>
                <span class="marquee__item">Security Hardening</span><span class="marquee__sep">·</span>
                <span class="marquee__item">Automation</span><span class="marquee__sep">·</span>
                <span class="marquee__item">AI Workflows</span><span class="marquee__sep">·</span>
                <span class="marquee__item">WordPress</span><span class="marquee__sep">·</span>
                <span class="marquee__item">Dashboards</span><span class="marquee__sep">·</span>
                <span class="marquee__item">API Integrations</span><span class="marquee__sep">·</span>
                <span class="marquee__item">{{ $settings->company_name }}</span><span class="marquee__sep">·</span>
                <span class="marquee__item">Websites</span><span class="marquee__sep">·</span>
                <span class="marquee__item">Platforms</span><span class="marquee__sep">·</span>
                <span class="marquee__item">Security Hardening</span><span class="marquee__sep">·</span>
                <span class="marquee__item">Automation</span><span class="marquee__sep">·</span>
                <span class="marquee__item">AI Workflows</span><span class="marquee__sep">·</span>
                <span class="marquee__item">WordPress</span><span class="marquee__sep">·</span>
                <span class="marquee__item">Dashboards</span><span class="marquee__sep">·</span>
                <span class="marquee__item">API Integrations</span><span class="marquee__sep">·</span>
                <span class="marquee__item">{{ $settings->company_name }}</span><span class="marquee__sep">·</span>
            </div>
        </div>
    </section>

    <section id="work" class="section--services">
        <div class="container">
            <header class="section__head section__head--left reveal">
                <p class="section__eyebrow">{!! $t('marketing_services_eyebrow', 'Services') !!}</p>
                <h2 class="section__title">{!! $t('marketing_services_title', 'What we build') !!}</h2>
                <p class="section__subtitle">{!! $h('marketing_services_subtitle', 'From first brief to production launch: design, engineering, security, and delivery.') !!}</p>
            </header>

            <div class="service-bento">
                <div class="service-block reveal">
                    <span class="service-block__watermark" aria-hidden="true">01</span>
                    <p class="service-block__num">01</p>
                    <h3 class="service-block__title">{!! $t('marketing_service_1_title', 'High‑impact web presence') !!}</h3>
                    <p class="service-block__desc">{!! $h('marketing_service_1_desc', 'One‑page sites, full marketing sites, structured copy, and lead capture tied back to real business goals.') !!}</p>
                </div>
                <div class="service-block reveal reveal--delay-1">
                    <span class="service-block__watermark" aria-hidden="true">02</span>
                    <p class="service-block__num">02</p>
                    <h3 class="service-block__title">{!! $t('marketing_service_2_title', 'Systems that scale') !!}</h3>
                    <p class="service-block__desc">{!! $h('marketing_service_2_desc', 'Multi‑tenant platforms, admin panels, and analytics that work for both ops teams and leadership.') !!}</p>
                </div>
                <div class="service-block reveal reveal--delay-2">
                    <span class="service-block__watermark" aria-hidden="true">03</span>
                    <p class="service-block__num">03</p>
                    <h3 class="service-block__title">{!! $t('marketing_service_3_title', 'Secure by design') !!}</h3>
                    <p class="service-block__desc">{!! $h('marketing_service_3_desc', 'Secure‑by‑default infrastructure, logging, least‑privilege access, and sensible hardening at every layer.') !!}</p>
                </div>
                <div class="service-block reveal reveal--delay-3">
                    <span class="service-block__watermark" aria-hidden="true">04</span>
                    <p class="service-block__num">04</p>
                    <h3 class="service-block__title">{!! $t('marketing_service_4_title', 'AI & automation') !!}</h3>
                    <p class="service-block__desc">{!! $h('marketing_service_4_desc', 'AI‑powered workflows, integrations, and reporting that reduce manual work and surface the right signals.') !!}</p>
                </div>
            </div>
        </div>
    </section>

    @include('marketing.partials.pricing-plans-section', [
        'eyebrow'  => $blocks->get('marketing_pricing_eyebrow')?->body  ?: 'Packages',
        'title'    => $blocks->get('marketing_pricing_title')?->body    ?: 'Engagement options',
        'subtitle' => $blocks->get('marketing_pricing_subtitle')?->body ?: 'Starting points we publish on the site - scope and pricing are confirmed on a call.',
    ])

    <section class="section section--va-promo" id="virtual-assistants">
        <div class="container">
            <div class="va-promo reveal">
                <div class="va-promo__body">
                    <p class="va-promo__eyebrow">{!! $t('marketing_va_eyebrow', 'Staffing') !!}</p>
                    <h2 class="va-promo__title">{!! $t('marketing_va_title', 'Managed virtual assistants') !!}</h2>
                    <p class="va-promo__text">{!! $h('marketing_va_body', 'Retainer-based support for admin, inbox, social, CRM, and operations.') !!}</p>
                </div>
                <div class="va-promo__actions">
                    <a class="btn btn--primary" href="{{ route('marketing.virtual-assistant') }}">Explore VA services</a>
                    <a class="btn btn--ghost" href="{{ route('marketing.book') }}">Book a call</a>
                </div>
            </div>
        </div>
    </section>

    <section id="builds" class="section--showcase">
        <div class="container">
            <header class="section__head section__head--left reveal">
                <p class="section__eyebrow">{!! $t('marketing_portfolio_eyebrow', 'Portfolio') !!}</p>
                <h2 class="section__title">{!! $t('marketing_portfolio_title', "Platforms behind our clients' operations") !!}</h2>
                <p class="section__subtitle">{!! $h('marketing_portfolio_subtitle', "A snapshot of the systems that sit underneath our clients' day‑to‑day work.") !!}</p>
            </header>

            <div class="builds-featured reveal">
                <div>
                    <p class="builds-featured__label">{!! $t('marketing_featured_label', 'Featured build') !!}</p>
                    <h3 class="builds-featured__title">
                        <a href="{{ $blocks->get('marketing_featured_url')?->body ?: 'https://propreneur.co.uk/' }}"
                           target="_blank" rel="noreferrer">
                            {!! $t('marketing_featured_name', 'Propreneur') !!}
                        </a>
                    </h3>
                    <p class="builds-featured__desc">
                        {!! $h('marketing_featured_desc', 'A full multi-tenant SaaS platform: the operating system for property entrepreneurs. Engineered and delivered end-to-end by '.$settings->company_name.'.') !!}
                    </p>
                </div>
                <div class="builds-featured__cta">
                    <a class="btn btn--outline-light"
                       href="{{ $blocks->get('marketing_featured_url')?->body ?: 'https://propreneur.co.uk/' }}"
                       target="_blank" rel="noreferrer">Visit site →</a>
                    <a class="btn btn--gold" href="#contact">Build something similar</a>
                </div>
            </div>

            <div class="platform-grid reveal">
                <div class="platform-card">
                    <h3 class="platform-card__title">Marketing &amp; launch sites</h3>
                    <p class="platform-card__desc">High‑conversion marketing sites and launch pages for B2B, SaaS, and platforms.</p>
                </div>
                <div class="platform-card">
                    <h3 class="platform-card__title">Labyrinth CMS</h3>
                    <p class="platform-card__desc">Security‑first CMS with full e‑commerce and a dedicated theme marketplace.</p>
                </div>
                <div class="platform-card">
                    <h3 class="platform-card__title">TuinApp</h3>
                    <p class="platform-card__desc">Multi‑tenant MIS powering workforce, scheduling, payroll, and client ops for service businesses.</p>
                </div>
                <div class="platform-card">
                    <h3 class="platform-card__title">ABBIS</h3>
                    <p class="platform-card__desc">Business intelligence and operations platform for borehole drilling and field operations.</p>
                </div>
            </div>
        </div>
    </section>

    <section id="book-call" class="section">
        <div class="container">
            <div class="book-cta reveal">
                <div class="book-cta__body">
                    <p class="book-cta__eyebrow">{!! $t('marketing_book_eyebrow', 'Schedule') !!}</p>
                    <h2 class="book-cta__title">{!! $t('marketing_book_title', 'Start with a conversation') !!}</h2>
                    <p class="book-cta__desc">{!! $h('marketing_book_desc', '30 minutes. No sales script. Just a straight talk about what you\'re building and whether we\'re the right team for it.') !!}</p>
                    <ul class="book-cta__facts">
                        <li><i class="fa-regular fa-clock"></i> 30-minute video call</li>
                        <li><i class="fa-regular fa-calendar"></i> Pick any weekday slot</li>
                        <li><i class="fa-solid fa-check"></i> Free, no commitment</li>
                    </ul>
                </div>
                <div class="book-cta__action">
                    <a class="btn btn--primary btn--lg" href="{{ route('marketing.book') }}">Book a free call →</a>
                    <p class="book-cta__note">{!! $t('marketing_book_note', 'Slots available Mon–Fri, 09:00–17:00 UK time') !!}</p>
                </div>
            </div>
        </div>
    </section>

    <section id="contact" class="section section--alt">
        <div class="container">
            <header class="section__head reveal">
                <p class="section__eyebrow">{!! $t('marketing_contact_eyebrow', 'Get in touch') !!}</p>
                <h2 class="section__title">{!! $t('marketing_contact_title', "Let's build something") !!}</h2>
                <p class="section__subtitle">{!! $h('marketing_contact_subtitle', "Tell us what you're working on. We'll come back with a clear next step.") !!}</p>
            </header>

            <div class="contact">
                <form class="form reveal" id="contactForm" method="post" action="{{ route('leads.store') }}">
                    @csrf
                    <input type="hidden" name="offering_id" id="offering_id" value="{{ old('offering_id') }}">
                    <input type="hidden" name="source" value="web:contact">
                    <input type="text" name="company" tabindex="-1" autocomplete="off" value="" style="display:none" aria-hidden="true">
                    <input type="hidden" name="form_ts" id="formTs" value="">

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
                        <label class="field field--full">
                            <span class="field__label">What do you need?</span>
                            <textarea class="field__input field__textarea" name="message" rows="5" required
                                placeholder="Website, platform, automation, security review…">{{ old('message') }}</textarea>
                        </label>
                    </div>

                    <div class="form__actions">
                        <button class="btn btn--primary" type="submit">Send message</button>
                        <p class="form__note">We'll reply from <a href="{{ $contactMailto }}">{{ $contactEmail }}</a> directly.</p>
                    </div>
                </form>

                <aside class="contact__info reveal reveal--delay-2" aria-label="Company info">
                    <span class="contact__info-label">Company</span>
                    <p class="contact__info-name">{{ $settings->company_name }}</p>
                    <p class="contact__info-meta">{!! $t('marketing_contact_tagline', 'Engineering & delivery, United Kingdom') !!}</p>
                    <div class="contact__info-links">
                        <a class="contact__info-link" href="{{ $contactMailto }}">{{ $contactEmail }}</a>
                        <a class="contact__info-link" href="{{ url('/') }}">{{ parse_url(config('app.url'), PHP_URL_HOST) ?: request()->getHost() }}</a>
                    </div>
                    <div class="contact__info-divs">
                        <h3>Divisions</h3>
                        {!! $h('marketing_divisions_html', '<p class="contact__div-item"><strong>Verlox Cyber</strong> Cybersecurity services, reviews, hardening, and incident support.</p><p class="contact__div-item"><strong>Verlox IT</strong> IT services, managed support, systems delivery, and operations.</p>') !!}
                    </div>
                </aside>
            </div>
        </div>
    </section>

</main>

@include('marketing.partials.footer')
@endsection
