<?php

namespace Database\Seeders;

use App\Models\BookingAvailabilityRule;
use App\Models\CompanySetting;
use App\Models\ContentBlock;
use App\Models\ContractTemplate;
use App\Models\LegalDocument;
use App\Models\Offering;
use App\Models\OfferingType;
use App\Models\PipelineStage;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class VeloxSeeder extends Seeder
{
    public function run(): void
    {
        $settings = CompanySetting::current();
        $settings->update([
            'company_name' => 'Verlox UK',
            'tagline' => 'Websites • Platforms • Automation',
            'support_email' => 'contact@verlox.ukk',
            'mail_from_name' => 'Verlox UK',
            'mail_from_address' => 'contact@verlox.ukk',
            'address_line1' => 'United Kingdom',
        ]);

        // Plain text: User model uses the `hashed` cast (do not Hash::make here).
        User::query()->updateOrCreate(
            ['email' => 'admin@verlox.uk'],
            [
                'name' => 'Verlox Admin',
                'password' => 'ChangeMe!Velox2026',
                'is_admin' => true,
                'email_verified_at' => now(),
            ]
        );

        $stages = [
            ['name' => 'New', 'sort_order' => 10, 'color_hex' => '#64748b'],
            ['name' => 'Qualified', 'sort_order' => 20, 'color_hex' => '#0ea5e9'],
            ['name' => 'Proposal', 'sort_order' => 30, 'color_hex' => '#a855f7'],
            ['name' => 'Won', 'sort_order' => 40, 'color_hex' => '#22c55e'],
            ['name' => 'Lost', 'sort_order' => 50, 'color_hex' => '#ef4444'],
        ];
        foreach ($stages as $row) {
            PipelineStage::query()->firstOrCreate(['name' => $row['name']], $row);
        }

        $offeringTypes = [
            ['name' => 'Demo', 'slug' => 'demo', 'display_order' => 10],
            ['name' => 'Trial', 'slug' => 'trial', 'display_order' => 20],
            ['name' => 'Consultation', 'slug' => 'consultation', 'display_order' => 30],
            ['name' => 'Quote', 'slug' => 'quote', 'display_order' => 40],
            ['name' => 'Contact', 'slug' => 'contact', 'display_order' => 50],
            ['name' => 'Purchase', 'slug' => 'purchase', 'display_order' => 60],
        ];
        foreach ($offeringTypes as $row) {
            OfferingType::query()->firstOrCreate(['slug' => $row['slug']], $row + ['is_active' => true]);
        }

        $typesBySlug = OfferingType::query()->pluck('id', 'slug'); // slug => id

        $offerings = [
            ['name' => 'Product demo', 'slug' => 'demo', 'type_slug' => 'demo', 'summary' => 'Walkthrough of a Velox product', 'display_order' => 10],
            ['name' => 'Trial access', 'slug' => 'trial', 'type_slug' => 'trial', 'summary' => 'Time-limited trial environment', 'display_order' => 20],
            ['name' => 'Consultation', 'slug' => 'consultation', 'type_slug' => 'consultation', 'summary' => 'Architecture and delivery planning', 'display_order' => 30],
            ['name' => 'Quotation', 'slug' => 'quotation', 'type_slug' => 'quote', 'summary' => 'Scoped estimate for your build', 'display_order' => 40],
            ['name' => 'Contact', 'slug' => 'contact', 'type_slug' => 'contact', 'summary' => 'General enquiry', 'display_order' => 50],
            ['name' => 'Starter engagement', 'slug' => 'starter', 'type_slug' => 'purchase', 'summary' => 'Fixed discovery and roadmap pack', 'price_pence' => 250000, 'display_order' => 60],
        ];
        foreach ($offerings as $row) {
            $typeSlug = $row['type_slug'];
            $typeId = $typesBySlug[$typeSlug] ?? null;

            Offering::query()->firstOrCreate(
                ['slug' => $row['slug']],
                [
                    'name' => $row['name'],
                    'slug' => $row['slug'],
                    'summary' => $row['summary'] ?? null,
                    'type' => $typeSlug,
                    'offering_type_id' => $typeId,
                    'price_pence' => $row['price_pence'] ?? null,
                    'display_order' => $row['display_order'] ?? 0,
                ]
            );
        }

        ContentBlock::query()->updateOrCreate(
            ['key' => 'marketing_hero_eyebrow'],
            [
                'title' => 'Hero eyebrow',
                'type' => 'text',
                'body' => 'UK Engineering & Delivery',
                'sort_order' => 5,
                'is_active' => true,
            ]
        );
        ContentBlock::query()->updateOrCreate(
            ['key' => 'marketing_hero_title'],
            [
                'title' => 'Hero title',
                'type' => 'html',
                'body' => 'Engineering-grade SaaS and platforms,<br>delivered with clarity.',
                'sort_order' => 10,
                'is_active' => true,
            ]
        );
        ContentBlock::query()->updateOrCreate(
            ['key' => 'marketing_hero_subtitle'],
            [
                'title' => 'Hero subtitle',
                'type' => 'html',
                'body' => '<p class="hero__lede">From discovery through launch, we deliver secure foundations, measurable outcomes, and pragmatic automation: websites, platforms, and systems that are conversion-ready, security-hardened, and AI-capable from day one.</p>',
                'sort_order' => 20,
                'is_active' => true,
            ]
        );

        $blocks = [
            ['key' => 'marketing_services_heading', 'title' => 'Services heading', 'type' => 'textarea', 'body' => 'What we deliver', 'sort_order' => 30],
            ['key' => 'marketing_services_body', 'title' => 'Services body', 'type' => 'textarea', 'body' => 'Bespoke web applications, integrations, billing flows, and long-term support aligned to your roadmap.', 'sort_order' => 40],
        ];
        foreach ($blocks as $row) {
            ContentBlock::query()->firstOrCreate(['key' => $row['key']], $row + ['is_active' => true]);
        }

        ContractTemplate::query()->firstOrCreate(
            ['slug' => 'msa-default'],
            [
                'name' => 'Master services agreement',
                'is_default' => true,
                'body' => '<h1>Services agreement</h1><p>This agreement is between <strong>{{company_name}}</strong> (company number {{company_number}}) and <strong>{{client_name}}</strong>{{client_company}} (the Client).</p><p>Scope, fees, and milestones are defined in quotation {{quotation_number}} for a total of {{quotation_total}} unless otherwise amended in writing.</p><p>Delivery will follow mutually agreed timelines. Either party may terminate for material breach with 14 days notice where not remedied.</p><p>Governing law: England and Wales.</p><p>Signed for the supplier on {{today}}.</p>',
            ]
        );

        BookingAvailabilityRule::query()->whereIn('weekday', [1, 2, 3, 4, 5])->delete();
        foreach ([1, 2, 3, 4, 5] as $wd) {
            BookingAvailabilityRule::create([
                'weekday' => $wd,
                'start_time' => '09:00:00',
                'end_time' => '17:00:00',
            ]);
        }

        $effective = now()->toDateString();

        $docs = [
            [
                'category' => 'privacy',
                'title' => 'Privacy Policy',
                'body_html' => implode("\n", [
                    '<h1>Privacy Policy</h1>',
                    '<p><strong>Effective date:</strong> '.$effective.'</p>',
                    '<p>This policy explains how Verlox UK collects, uses, and protects personal data when you use our website and when you contact us for services. We aim to handle personal data in accordance with UK GDPR and the Data Protection Act 2018.</p>',
                    '<h2>Who we are</h2>',
                    '<p>Verlox UK (the “Company”) is the controller for personal data processed through this website and our engagement processes.</p>',
                    '<p>If you have questions, contact us at <a href="mailto:hello@verlox.uk">hello@verlox.uk</a>.</p>',
                    '<h2>What data we collect</h2>',
                    '<ul>',
                    '<li>Contact details (name, email, phone) when you submit an enquiry or communicate with us</li>',
                    '<li>Project information you provide (requirements, timelines, budgets, and relevant context)</li>',
                    '<li>Technical data (IP address, user agent, basic request logs) for security and operations</li>',
                    '</ul>',
                    '<h2>How we use your data</h2>',
                    '<ul>',
                    '<li>Respond to enquiries and provide proposals and services</li>',
                    '<li>Operate and secure our systems, prevent abuse, and maintain audit logs</li>',
                    '<li>Comply with legal obligations (e.g., accounting and tax)</li>',
                    '</ul>',
                    '<h2>Legal bases</h2>',
                    '<ul>',
                    '<li>Contract (to deliver services you request)</li>',
                    '<li>Legitimate interests (to secure and improve our services)</li>',
                    '<li>Legal obligation (where applicable)</li>',
                    '</ul>',
                    '<h2>Retention</h2>',
                    '<p>We retain data only as long as necessary for the purposes above, including maintaining business records and complying with legal requirements.</p>',
                    '<h2>Your rights</h2>',
                    '<p>You may request access, correction, deletion, or restriction of your personal data. Email <a href="mailto:hello@verlox.uk">hello@verlox.uk</a>.</p>',
                ]),
            ],
            [
                'category' => 'terms',
                'title' => 'Website Terms of Use',
                'body_html' => implode("\n", [
                    '<h1>Website Terms of Use</h1>',
                    '<p><strong>Effective date:</strong> '.$effective.'</p>',
                    '<p>These terms govern your use of the Verlox UK website. By accessing this website, you agree to these terms.</p>',
                    '<h2>Use of the website</h2>',
                    '<p>You may use the website for lawful purposes. You must not attempt to disrupt or compromise the site or its security.</p>',
                    '<h2>Enquiries</h2>',
                    '<p>Submitting an enquiry does not create a binding contract. Any services will be governed by a written agreement (for example, a statement of work or master services agreement).</p>',
                    '<h2>Intellectual property</h2>',
                    '<p>Website content and branding are owned by Verlox UK or its licensors. You may not copy or redistribute content without permission.</p>',
                    '<h2>Disclaimer</h2>',
                    '<p>Content is provided for general information and may change. We do not guarantee completeness or suitability for your purposes.</p>',
                    '<h2>Limitation of liability</h2>',
                    '<p>To the maximum extent permitted by law, we are not liable for indirect or consequential losses arising from use of the website.</p>',
                ]),
            ],
            [
                'category' => 'cookies',
                'title' => 'Cookie Policy',
                'body_html' => implode("\n", [
                    '<h1>Cookie Policy</h1>',
                    '<p><strong>Effective date:</strong> '.$effective.'</p>',
                    '<p>Cookies are small text files stored on your device. We use cookies that are necessary for site functionality and security, and may use analytics cookies to understand usage.</p>',
                    '<h2>Essential cookies</h2>',
                    '<ul><li>Session and security cookies (for example, CSRF protection)</li></ul>',
                    '<h2>Managing cookies</h2>',
                    '<p>You can control cookies using your browser settings. Disabling essential cookies may prevent parts of the site from working.</p>',
                ]),
            ],
            [
                'category' => 'aup',
                'title' => 'Acceptable Use Policy',
                'body_html' => implode("\n", [
                    '<h1>Acceptable Use Policy</h1>',
                    '<p><strong>Effective date:</strong> '.$effective.'</p>',
                    '<p>This policy sets out rules for using our website and any client portals or systems we provide.</p>',
                    '<h2>Prohibited behaviour</h2>',
                    '<ul>',
                    '<li>Attempting unauthorised access to systems or accounts</li>',
                    '<li>Uploading malware or abusive traffic</li>',
                    '<li>Using forms to send spam or fraudulent messages</li>',
                    '</ul>',
                    '<h2>Security</h2>',
                    '<p>We may block traffic or suspend access where we believe there is misuse or risk to systems.</p>',
                ]),
            ],
            [
                'category' => 'refunds',
                'title' => 'Refund and Cancellation Policy',
                'body_html' => implode("\n", [
                    '<h1>Refund and Cancellation Policy</h1>',
                    '<p><strong>Effective date:</strong> '.$effective.'</p>',
                    '<p>This policy describes refunds and cancellations for Verlox UK services.</p>',
                    '<h2>Project work</h2>',
                    '<p>For fixed-scope projects, payment terms will be specified in the agreed quotation/statement of work. Work started or milestones delivered are typically non-refundable.</p>',
                    '<h2>Retainers</h2>',
                    '<p>For monthly retainers, you may cancel with notice as stated in your agreement. Unused time may expire at the end of the billing period unless otherwise agreed in writing.</p>',
                    '<h2>Billing errors</h2>',
                    '<p>If there is a billing error, contact <a href="mailto:hello@verlox.uk">hello@verlox.uk</a> within 14 days.</p>',
                ]),
            ],
        ];

        foreach ($docs as $doc) {
            $slug = Str::slug($doc['title']);
            LegalDocument::query()->updateOrCreate(
                ['slug' => $slug],
                $doc + ['slug' => $slug, 'status' => 'published', 'effective_at' => $effective]
            );
        }
    }
}
