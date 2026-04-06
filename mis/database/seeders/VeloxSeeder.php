<?php

namespace Database\Seeders;

use App\Models\BookingAvailabilityRule;
use App\Models\CompanySetting;
use App\Models\ContentBlock;
use App\Models\ContractTemplate;
use App\Models\Offering;
use App\Models\PipelineStage;
use App\Models\User;
use Illuminate\Database\Seeder;

class VeloxSeeder extends Seeder
{
    public function run(): void
    {
        $settings = CompanySetting::current();
        $settings->update([
            'company_name' => 'Verlox UK',
            'tagline' => 'Websites • Platforms • Automation',
            'support_email' => 'hello@verlox.uk',
            'mail_from_name' => 'Verlox UK',
            'mail_from_address' => 'hello@verlox.uk',
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

        $offerings = [
            ['name' => 'Product demo', 'slug' => 'demo', 'type' => 'demo', 'summary' => 'Walkthrough of a Velox product', 'display_order' => 10],
            ['name' => 'Trial access', 'slug' => 'trial', 'type' => 'trial', 'summary' => 'Time-limited trial environment', 'display_order' => 20],
            ['name' => 'Consultation', 'slug' => 'consultation', 'type' => 'consultation', 'summary' => 'Architecture and delivery planning', 'display_order' => 30],
            ['name' => 'Quotation', 'slug' => 'quotation', 'type' => 'quote', 'summary' => 'Scoped estimate for your build', 'display_order' => 40],
            ['name' => 'Contact', 'slug' => 'contact', 'type' => 'contact', 'summary' => 'General enquiry', 'display_order' => 50],
            ['name' => 'Starter engagement', 'slug' => 'starter', 'type' => 'purchase', 'summary' => 'Fixed discovery and roadmap pack', 'price_pence' => 250000, 'display_order' => 60],
        ];
        foreach ($offerings as $row) {
            Offering::query()->firstOrCreate(['slug' => $row['slug']], $row);
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
    }
}
