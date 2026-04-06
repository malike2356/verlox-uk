<?php

namespace App\Http\Controllers\Mis;

use App\Http\Controllers\Controller;
use App\Models\Offering;
use App\Models\PricingPlan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class OfferingController extends Controller
{
    public function index(): View
    {
        $offerings = Offering::query()->orderBy('display_order')->get();

        return view('mis.offerings.index', compact('offerings'));
    }

    public function create(): View
    {
        return view('mis.offerings.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'summary' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string', 'max:20000'],
            'type' => ['required', 'in:demo,purchase,trial,consultation,quote,contact'],
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
        Offering::create([
            'name' => $data['name'],
            'slug' => $slug,
            'summary' => $data['summary'] ?? null,
            'description' => $data['description'] ?? null,
            'type' => $data['type'],
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
        return view('mis.offerings.edit', compact('offering'));
    }

    public function update(Request $request, Offering $offering): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:offerings,slug,'.$offering->id],
            'summary' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string', 'max:20000'],
            'type' => ['required', 'in:demo,purchase,trial,consultation,quote,contact'],
            'display_order' => ['nullable', 'integer', 'min:0'],
            'price_pence' => ['nullable', 'integer', 'min:0'],
            'currency' => ['nullable', 'string', 'size:3'],
            'stripe_price_id' => ['nullable', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ]);
        $offering->update([
            ...$data,
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
