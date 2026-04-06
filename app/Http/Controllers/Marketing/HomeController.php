<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use App\Models\CompanySetting;
use App\Models\ContentBlock;
use App\Models\PricingPlan;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $settings = CompanySetting::current();
        $blocks = ContentBlock::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('key')
            ->get()
            ->keyBy('key');

        $heroEyebrow = $this->blockPlain($blocks, 'marketing_hero_eyebrow', 'UK Engineering & Delivery');
        $heroTitleHtml = $this->blockHtml($blocks, 'marketing_hero_title', "Engineering-grade SaaS and platforms,<br>\ndelivered with clarity.");
        $heroSubtitleHtml = $this->blockHtml($blocks, 'marketing_hero_subtitle', '<p class="hero__lede">From discovery through launch, we deliver secure foundations, measurable outcomes, and pragmatic automation: websites, platforms, and systems that are conversion-ready, security-hardened, and AI-capable from day one.</p>');

        $pricingPlans = PricingPlan::query()
            ->active()
            ->ordered()
            ->forHome()
            ->with('features')
            ->get();

        return view('marketing.home', compact(
            'settings',
            'blocks',
            'heroEyebrow',
            'heroTitleHtml',
            'heroSubtitleHtml',
            'pricingPlans',
        ));
    }

    private function blockPlain(Collection $blocks, string $key, string $default): string
    {
        $b = $blocks->get($key);
        if (! $b || $b->body === null || $b->body === '') {
            return $default;
        }

        return $b->type === 'html' ? strip_tags($b->body) : $b->body;
    }

    private function blockHtml(Collection $blocks, string $key, string $default): string
    {
        $b = $blocks->get($key);
        if (! $b || $b->body === null || $b->body === '') {
            return $default;
        }

        return match ($b->type) {
            'html' => $b->body,
            'textarea' => nl2br(e($b->body)),
            default => e($b->body),
        };
    }
}
