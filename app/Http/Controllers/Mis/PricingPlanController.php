<?php

namespace App\Http\Controllers\Mis;

use App\Http\Controllers\Controller;
use App\Models\Offering;
use App\Models\PricingPlan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PricingPlanController extends Controller
{
    public function index(): View
    {
        $plans = PricingPlan::query()
            ->with('offering')
            ->withCount('features')
            ->orderBy('display_order')
            ->orderBy('id')
            ->get();

        return view('mis.pricing-plans.index', compact('plans'));
    }

    public function create(): View
    {
        return $this->formView(new PricingPlan([
            'currency' => 'GBP',
            'billing_period' => 'monthly',
            'display_order' => 0,
            'show_on_home' => true,
            'show_on_book' => true,
            'show_on_va' => true,
            'is_active' => true,
            'is_featured' => false,
        ]), '');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $slug = $this->uniqueSlug($request->input('slug'), $data['name']);

        $plan = PricingPlan::create(array_merge($data, ['slug' => $slug]));
        $this->syncFeatures($plan, (string) $request->input('features_text', ''));

        return redirect()->route('mis.pricing-plans.index')->with('status', 'Pricing plan created.');
    }

    public function edit(PricingPlan $pricingPlan): View
    {
        $pricingPlan->load('features');
        $featuresText = $pricingPlan->features->map(function ($f) {
            $line = $f->label;
            if (! $f->is_included) {
                $line = '!'.$line;
            }

            return $line;
        })->implode("\n");

        return $this->formView($pricingPlan, $featuresText);
    }

    public function update(Request $request, PricingPlan $pricingPlan): RedirectResponse
    {
        $data = $this->validated($request, $pricingPlan);
        $slugInput = $request->filled('slug') ? $request->input('slug') : $pricingPlan->slug;
        $slug = $this->uniqueSlug($slugInput, $data['name'], $pricingPlan->id);

        $pricingPlan->update(array_merge($data, ['slug' => $slug]));
        $this->syncFeatures($pricingPlan, (string) $request->input('features_text', ''));

        return redirect()->route('mis.pricing-plans.index')->with('status', 'Pricing plan updated.');
    }

    public function destroy(PricingPlan $pricingPlan): RedirectResponse
    {
        $pricingPlan->delete();

        return redirect()->route('mis.pricing-plans.index')->with('status', 'Pricing plan deleted.');
    }

    private function formView(PricingPlan $plan, string $featuresText): View
    {
        $offerings = Offering::query()->orderBy('name')->get(['id', 'name']);
        $billingPeriods = PricingPlan::BILLING_PERIODS;
        $ctaRoutes = array_combine(
            PricingPlan::ALLOWED_CTA_ROUTES,
            PricingPlan::ALLOWED_CTA_ROUTES
        );

        return view('mis.pricing-plans.form', [
            'plan' => $plan,
            'featuresText' => $featuresText,
            'offerings' => $offerings,
            'billingPeriods' => $billingPeriods,
            'ctaRoutes' => $ctaRoutes,
            'isEdit' => $plan->exists,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request, ?PricingPlan $existing = null): array
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('pricing_plans', 'slug')->ignore($existing?->id),
            ],
            'tagline' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:10000'],
            'price_gbp' => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
            'compare_at_gbp' => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
            'currency' => ['required', 'string', 'size:3'],
            'billing_period' => ['required', 'string', 'in:'.implode(',', array_keys(PricingPlan::BILLING_PERIODS))],
            'sessions_included' => ['nullable', 'integer', 'min:1', 'max:9999'],
            'price_display_override' => ['nullable', 'string', 'max:255'],
            'cta_label' => ['nullable', 'string', 'max:120'],
            'cta_route' => ['nullable', 'string', 'max:255', Rule::in(PricingPlan::ALLOWED_CTA_ROUTES)],
            'cta_url' => ['nullable', 'string', 'max:2048', 'url'],
            'offering_id' => ['nullable', 'integer', 'exists:offerings,id'],
            'show_on_home' => ['nullable', 'boolean'],
            'show_on_book' => ['nullable', 'boolean'],
            'show_on_va' => ['nullable', 'boolean'],
            'display_order' => ['nullable', 'integer', 'min:0', 'max:999999'],
            'is_active' => ['nullable', 'boolean'],
            'is_featured' => ['nullable', 'boolean'],
            'features_text' => ['nullable', 'string', 'max:50000'],
        ]);

        $pricePence = $this->optionalPence($validated['price_gbp'] ?? null);
        $comparePence = $this->optionalPence($validated['compare_at_gbp'] ?? null);

        return [
            'name' => $validated['name'],
            'tagline' => $validated['tagline'] ?? null,
            'description' => $validated['description'] ?? null,
            'price_pence' => $pricePence,
            'compare_at_pence' => $comparePence,
            'currency' => strtoupper($validated['currency']),
            'billing_period' => $validated['billing_period'],
            'sessions_included' => $validated['sessions_included'] ?? null,
            'price_display_override' => $validated['price_display_override'] ?? null,
            'cta_label' => $validated['cta_label'] ?? null,
            'cta_route' => $validated['cta_route'] ?? null,
            'cta_url' => $validated['cta_url'] ?? null,
            'offering_id' => $validated['offering_id'] ?? null,
            'show_on_home' => $request->boolean('show_on_home'),
            'show_on_book' => $request->boolean('show_on_book'),
            'show_on_va' => $request->boolean('show_on_va'),
            'display_order' => (int) ($validated['display_order'] ?? 0),
            'is_active' => $request->boolean('is_active'),
            'is_featured' => $request->boolean('is_featured'),
        ];
    }

    private function optionalPence(null|int|float|string $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        return (int) round((float) $value * 100);
    }

    private function uniqueSlug(?string $slugInput, string $name, ?int $ignoreId = null): string
    {
        $base = ($slugInput !== null && $slugInput !== '') ? Str::slug($slugInput) : Str::slug($name);
        if ($base === '') {
            $base = 'plan';
        }
        $slug = $base;
        $i = 1;
        while (PricingPlan::query()
            ->when($ignoreId !== null, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->where('slug', $slug)
            ->exists()) {
            $slug = $base.'-'.$i;
            $i++;
        }

        return $slug;
    }

    private function syncFeatures(PricingPlan $plan, string $raw): void
    {
        $plan->features()->delete();
        $lines = preg_split("/\r\n|\n|\r/", $raw) ?: [];
        $order = 0;
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '') {
                continue;
            }
            $included = true;
            if (str_starts_with($line, '!')) {
                $included = false;
                $line = ltrim(substr($line, 1));
            }
            if ($line === '') {
                continue;
            }
            $plan->features()->create([
                'label' => $line,
                'sort_order' => $order++,
                'is_included' => $included,
            ]);
        }
    }
}
