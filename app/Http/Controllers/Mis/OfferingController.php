<?php

namespace App\Http\Controllers\Mis;

use App\Http\Controllers\Controller;
use App\Models\Offering;
use App\Models\OfferingType;
use App\Models\PricingPlan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class OfferingController extends Controller
{
    public function index(): View
    {
        $offerings = Offering::query()
            ->with('offeringType')
            ->orderBy('display_order')
            ->get();

        return view('mis.offerings.index', compact('offerings'));
    }

    public function create(): View
    {
        $types = OfferingType::query()
            ->where('is_active', true)
            ->orderBy('display_order')
            ->orderBy('name')
            ->get();

        return view('mis.offerings.create', compact('types'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'summary' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string', 'max:20000'],
            'offering_type_id' => ['required', 'integer', 'exists:offering_types,id'],
            'display_order' => ['nullable', 'integer', 'min:0'],
            'price_pence' => ['nullable', 'integer', 'min:0'],
            'currency' => ['nullable', 'string', 'size:3'],
            'stripe_price_id' => ['nullable', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ]);
        $base = $data['slug'] ? Str::slug($data['slug']) : Str::slug($data['name']);
        $slug = $base;
        $i = 1;
        while (Offering::query()->where('slug', $slug)->exists()) {
            $slug = $base.'-'.$i;
            $i++;
        }
        $type = OfferingType::query()->findOrFail($data['offering_type_id']);
        Offering::create([
            'name' => $data['name'],
            'slug' => $slug,
            'summary' => $data['summary'] ?? null,
            'description' => $data['description'] ?? null,
            'offering_type_id' => $type->id,
            // Keep legacy column populated for backwards compatibility.
            'type' => $type->slug,
            'display_order' => $data['display_order'] ?? 0,
            'price_pence' => $data['price_pence'] ?? null,
            'currency' => strtoupper($data['currency'] ?? 'GBP'),
            'stripe_price_id' => $data['stripe_price_id'] ?? null,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('mis.offerings.index')->with('status', 'Offering created.');
    }

    public function edit(Offering $offering): View
    {
        $types = OfferingType::query()
            ->orderBy('display_order')
            ->orderBy('name')
            ->get();

        return view('mis.offerings.edit', compact('offering', 'types'));
    }

    public function update(Request $request, Offering $offering): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:offerings,slug,'.$offering->id],
            'summary' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string', 'max:20000'],
            'offering_type_id' => ['required', 'integer', 'exists:offering_types,id'],
            'display_order' => ['nullable', 'integer', 'min:0'],
            'price_pence' => ['nullable', 'integer', 'min:0'],
            'currency' => ['nullable', 'string', 'size:3'],
            'stripe_price_id' => ['nullable', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ]);
        $type = OfferingType::query()->findOrFail($data['offering_type_id']);
        $offering->update([
            ...$data,
            // keep legacy column populated for any old code paths
            'type' => $type->slug,
            'currency' => strtoupper($data['currency'] ?? 'GBP'),
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('mis.offerings.index')->with('status', 'Offering saved.');
    }

    public function destroy(Offering $offering): RedirectResponse
    {
        if (PricingPlan::query()->where('offering_id', $offering->id)->exists()) {
            return redirect()->route('mis.offerings.index')
                ->with('error', 'Detach this offering from all pricing plans before deleting.');
        }

        $offering->delete();

        return redirect()->route('mis.offerings.index')->with('status', 'Offering deleted.');
    }
}
