<?php

use App\Models\ContentBlock;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $blocks = [
            // ── Hero ──────────────────────────────────────────────────
            ['key' => 'marketing_hero_eyebrow',  'title' => 'Hero eyebrow',  'type' => 'text',     'sort_order' => 10, 'body' => 'Software Engineering & Automations'],
            ['key' => 'marketing_hero_title',    'title' => 'Hero title',    'type' => 'html',     'sort_order' => 11, 'body' => 'Custom software, AI-powered systems, and cyber-secure websites with <span class="optional">optional</span> <span class="virtual">virtual</span> assistants to run them.'],
            ['key' => 'marketing_hero_subtitle', 'title' => 'Hero subtitle', 'type' => 'html',     'sort_order' => 12, 'body' => '<p>You need more than code. You need security that holds up, automation that actually works, and people who can manage it all. That\'s exactly what we deliver.</p>'],

            // ── Services section ──────────────────────────────────────
            ['key' => 'marketing_services_eyebrow',  'title' => 'Services eyebrow',  'type' => 'text',     'sort_order' => 20, 'body' => 'Services'],
            ['key' => 'marketing_services_title',    'title' => 'Services title',    'type' => 'text',     'sort_order' => 21, 'body' => 'What we build'],
            ['key' => 'marketing_services_subtitle', 'title' => 'Services subtitle', 'type' => 'textarea', 'sort_order' => 22, 'body' => 'From first brief to production launch: design, engineering, security, and delivery.'],

            ['key' => 'marketing_service_1_title', 'title' => 'Service 1 title', 'type' => 'text',     'sort_order' => 23, 'body' => 'High‑impact web presence'],
            ['key' => 'marketing_service_1_desc',  'title' => 'Service 1 desc',  'type' => 'textarea', 'sort_order' => 24, 'body' => 'One‑page sites, full marketing sites, structured copy, and lead capture tied back to real business goals.'],
            ['key' => 'marketing_service_2_title', 'title' => 'Service 2 title', 'type' => 'text',     'sort_order' => 25, 'body' => 'Systems that scale'],
            ['key' => 'marketing_service_2_desc',  'title' => 'Service 2 desc',  'type' => 'textarea', 'sort_order' => 26, 'body' => 'Multi‑tenant platforms, admin panels, and analytics that work for both ops teams and leadership.'],
            ['key' => 'marketing_service_3_title', 'title' => 'Service 3 title', 'type' => 'text',     'sort_order' => 27, 'body' => 'Secure by design'],
            ['key' => 'marketing_service_3_desc',  'title' => 'Service 3 desc',  'type' => 'textarea', 'sort_order' => 28, 'body' => 'Secure‑by‑default infrastructure, logging, least‑privilege access, and sensible hardening at every layer.'],
            ['key' => 'marketing_service_4_title', 'title' => 'Service 4 title', 'type' => 'text',     'sort_order' => 29, 'body' => 'AI & automation'],
            ['key' => 'marketing_service_4_desc',  'title' => 'Service 4 desc',  'type' => 'textarea', 'sort_order' => 30, 'body' => 'AI‑powered workflows, integrations, and reporting that reduce manual work and surface the right signals.'],

            // ── Pricing section ───────────────────────────────────────
            ['key' => 'marketing_pricing_eyebrow',  'title' => 'Pricing eyebrow',  'type' => 'text',     'sort_order' => 40, 'body' => 'Packages'],
            ['key' => 'marketing_pricing_title',    'title' => 'Pricing title',    'type' => 'text',     'sort_order' => 41, 'body' => 'Engagement options'],
            ['key' => 'marketing_pricing_subtitle', 'title' => 'Pricing subtitle', 'type' => 'textarea', 'sort_order' => 42, 'body' => 'Starting points we publish on the site - scope and pricing are confirmed on a call.'],

            // ── VA promo (homepage strip) ─────────────────────────────
            ['key' => 'marketing_va_eyebrow', 'title' => 'VA promo eyebrow', 'type' => 'text',     'sort_order' => 50, 'body' => 'Staffing'],
            ['key' => 'marketing_va_title',   'title' => 'VA promo title',   'type' => 'text',     'sort_order' => 51, 'body' => 'Managed virtual assistants'],
            ['key' => 'marketing_va_body',    'title' => 'VA promo body',    'type' => 'textarea', 'sort_order' => 52, 'body' => 'Retainer-based support for admin, inbox, social, CRM, and operations - vetted assistants, UK-facing agreements, and one accountable partner. Ideal if you need capacity without adding headcount complexity.'],

            // ── Portfolio section ─────────────────────────────────────
            ['key' => 'marketing_portfolio_eyebrow',  'title' => 'Portfolio eyebrow',  'type' => 'text',     'sort_order' => 60, 'body' => 'Portfolio'],
            ['key' => 'marketing_portfolio_title',    'title' => 'Portfolio title',    'type' => 'text',     'sort_order' => 61, 'body' => "Platforms behind our clients' operations"],
            ['key' => 'marketing_portfolio_subtitle', 'title' => 'Portfolio subtitle', 'type' => 'textarea', 'sort_order' => 62, 'body' => "A snapshot of the systems that sit underneath our clients' day‑to‑day work."],

            ['key' => 'marketing_featured_label', 'title' => 'Featured build label', 'type' => 'text',     'sort_order' => 63, 'body' => 'Featured build'],
            ['key' => 'marketing_featured_name',  'title' => 'Featured build name',  'type' => 'text',     'sort_order' => 64, 'body' => 'Propreneur'],
            ['key' => 'marketing_featured_url',   'title' => 'Featured build URL',   'type' => 'text',     'sort_order' => 65, 'body' => 'https://propreneur.co.uk/'],
            ['key' => 'marketing_featured_desc',  'title' => 'Featured build desc',  'type' => 'textarea', 'sort_order' => 66, 'body' => "A full multi-tenant SaaS platform: the operating system for property entrepreneurs. 13 integrated modules covering deal pipelines, investor CRM, compliance tracking, tenancy management, mentoring, and an AI assistant (Carina). Includes 11 specialist property calculators (BRR, HMO, R2R, SA, Flip) and integrations with Airbnb, Rightmove, Land Registry, Open Banking, and major accounting platforms. Engineered and delivered end-to-end by Verlox on Laravel 10, multi-tenant architecture."],

            // ── Book-a-call section ───────────────────────────────────
            ['key' => 'marketing_book_eyebrow', 'title' => 'Book CTA eyebrow', 'type' => 'text',     'sort_order' => 70, 'body' => 'Schedule'],
            ['key' => 'marketing_book_title',   'title' => 'Book CTA title',   'type' => 'text',     'sort_order' => 71, 'body' => 'Start with a conversation'],
            ['key' => 'marketing_book_desc',    'title' => 'Book CTA desc',    'type' => 'textarea', 'sort_order' => 72, 'body' => '30 minutes. No sales script. Just a straight talk about what you\'re building and whether we\'re the right team for it.'],
            ['key' => 'marketing_book_note',    'title' => 'Book CTA footnote','type' => 'text',     'sort_order' => 73, 'body' => 'Slots available Mon–Fri, 09:00–17:00 UK time'],

            // ── Contact section (homepage) ────────────────────────────
            ['key' => 'marketing_contact_eyebrow',  'title' => 'Contact eyebrow',  'type' => 'text',     'sort_order' => 80, 'body' => 'Get in touch'],
            ['key' => 'marketing_contact_title',    'title' => 'Contact title',    'type' => 'text',     'sort_order' => 81, 'body' => "Let's build something"],
            ['key' => 'marketing_contact_subtitle', 'title' => 'Contact subtitle', 'type' => 'textarea', 'sort_order' => 82, 'body' => "Tell us what you're working on. We'll come back with a clear next step."],
            ['key' => 'marketing_contact_tagline',  'title' => 'Contact aside tagline', 'type' => 'text', 'sort_order' => 83, 'body' => 'Engineering & delivery, United Kingdom'],
            ['key' => 'marketing_divisions_html',   'title' => 'Divisions (HTML)',      'type' => 'html', 'sort_order' => 84, 'body' => '<p class="contact__div-item"><strong>Verlox Cyber</strong> Cybersecurity services, reviews, hardening, and incident support.</p><p class="contact__div-item"><strong>Verlox IT</strong> IT services, managed support, systems delivery, and operations.</p>'],

            // ── VA page ───────────────────────────────────────────────
            ['key' => 'va_hero_eyebrow', 'title' => 'VA page hero eyebrow', 'type' => 'text',     'sort_order' => 100, 'body' => 'Managed service'],
            ['key' => 'va_hero_title',   'title' => 'VA page hero title',   'type' => 'text',     'sort_order' => 101, 'body' => 'Virtual assistants for UK businesses'],
            ['key' => 'va_hero_lede',    'title' => 'VA page hero lede',    'type' => 'textarea', 'sort_order' => 102, 'body' => 'Verlox sources, vets, and manages skilled virtual assistants - you get one accountable partner, predictable monthly hours, and UK-facing contracts and data protection - without running international payroll yourself.'],

            ['key' => 'va_why_eyebrow', 'title' => 'VA "why us" eyebrow', 'type' => 'text', 'sort_order' => 103, 'body' => 'Why teams use us'],
            ['key' => 'va_why_title',   'title' => 'VA "why us" title',   'type' => 'text', 'sort_order' => 104, 'body' => 'Operations support, without the hiring overhead'],

            ['key' => 'va_card_1_title', 'title' => 'VA card 1 title', 'type' => 'text',     'sort_order' => 105, 'body' => 'Single point of contact'],
            ['key' => 'va_card_1_desc',  'title' => 'VA card 1 desc',  'type' => 'textarea', 'sort_order' => 106, 'body' => 'You contract with us; we handle quality, replacements, and performance - aligned with how our VA division operates day to day.'],
            ['key' => 'va_card_2_title', 'title' => 'VA card 2 title', 'type' => 'text',     'sort_order' => 107, 'body' => 'Retainer or hourly tiers'],
            ['key' => 'va_card_2_desc',  'title' => 'VA card 2 desc',  'type' => 'textarea', 'sort_order' => 108, 'body' => 'Structured monthly hours with clear overage rates - so finance and ops always know what to expect.'],
            ['key' => 'va_card_3_title', 'title' => 'VA card 3 title', 'type' => 'text',     'sort_order' => 109, 'body' => 'Vetted assistants'],
            ['key' => 'va_card_3_desc',  'title' => 'VA card 3 desc',  'type' => 'textarea', 'sort_order' => 110, 'body' => 'Contractors engaged under robust agreements, with confidentiality and data-processing expectations suited to client work.'],

            ['key' => 'va_skills_eyebrow',  'title' => 'VA skills eyebrow',  'type' => 'text',     'sort_order' => 111, 'body' => 'Capabilities'],
            ['key' => 'va_skills_title',    'title' => 'VA skills title',    'type' => 'text',     'sort_order' => 112, 'body' => 'What a VA can take off your plate'],
            ['key' => 'va_skills_subtitle', 'title' => 'VA skills subtitle', 'type' => 'textarea', 'sort_order' => 113, 'body' => 'Typical workstreams we place and supervise - scope is agreed per client in writing.'],
            ['key' => 'va_skills_list',     'title' => 'VA skills list (HTML)', 'type' => 'html',  'sort_order' => 114, 'body' => '<li><i class="fa-solid fa-check va-skill-list__mark" aria-hidden="true"></i> General administration - inbox, scheduling, data entry, travel</li><li><i class="fa-solid fa-check va-skill-list__mark" aria-hidden="true"></i> Social media - scheduling, community, light reporting</li><li><i class="fa-solid fa-check va-skill-list__mark" aria-hidden="true"></i> Customer service - email/chat, CRM updates, triage</li><li><i class="fa-solid fa-check va-skill-list__mark" aria-hidden="true"></i> Marketing support - campaigns, research, newsletters</li><li><i class="fa-solid fa-check va-skill-list__mark" aria-hidden="true"></i> Recruitment admin - CV screening, ATS, interview scheduling</li><li><i class="fa-solid fa-check va-skill-list__mark" aria-hidden="true"></i> E‑commerce ops - listings, orders, supplier comms</li>'],

            ['key' => 'va_pricing_eyebrow',  'title' => 'VA pricing eyebrow',  'type' => 'text',     'sort_order' => 115, 'body' => 'Retainers'],
            ['key' => 'va_pricing_title',    'title' => 'VA pricing title',    'type' => 'text',     'sort_order' => 116, 'body' => 'Typical VA engagement tiers'],
            ['key' => 'va_pricing_subtitle', 'title' => 'VA pricing subtitle', 'type' => 'textarea', 'sort_order' => 117, 'body' => 'Indicative packages managed in the MIS - hours, tools, and commercials are agreed in writing per client.'],

            ['key' => 'va_enquiry_eyebrow',  'title' => 'VA enquiry eyebrow',  'type' => 'text',     'sort_order' => 118, 'body' => 'Next step'],
            ['key' => 'va_enquiry_title',    'title' => 'VA enquiry title',    'type' => 'text',     'sort_order' => 119, 'body' => 'Tell us what you need covered'],
            ['key' => 'va_enquiry_subtitle', 'title' => 'VA enquiry subtitle', 'type' => 'textarea', 'sort_order' => 120, 'body' => "We'll reply with tier options, indicative hours, and onboarding steps. No obligation."],

            // ── Contact page ──────────────────────────────────────────
            ['key' => 'contact_eyebrow',  'title' => 'Contact page eyebrow',  'type' => 'text',     'sort_order' => 130, 'body' => 'Contact'],
            ['key' => 'contact_title',    'title' => 'Contact page title',    'type' => 'text',     'sort_order' => 131, 'body' => 'Leave your details - we will follow up'],
            ['key' => 'contact_subtitle', 'title' => 'Contact page subtitle', 'type' => 'textarea', 'sort_order' => 132, 'body' => 'Tell us what you do and what you are interested in. We will come back with the next step.'],
        ];

        $now = now();
        foreach ($blocks as $block) {
            ContentBlock::firstOrCreate(
                ['key' => $block['key']],
                array_merge($block, ['is_active' => true, 'created_at' => $now, 'updated_at' => $now])
            );
        }
    }

    public function down(): void
    {
        // Seeder migration — intentionally not reversing content
    }
};
