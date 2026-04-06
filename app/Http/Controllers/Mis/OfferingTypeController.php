<?php

namespace App\Http\Controllers\Mis;

use App\Http\Controllers\Controller;
use App\Models\Offering;
use App\Models\OfferingType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class OfferingTypeController extends Controller
{
    public function index(): View
    {
        $types = OfferingType::query()
            ->withCount('offerings')
            ->orderBy('display_order')
            ->orderBy('name')
            ->get();

        return view('mis.offering-types.index', compact('types'));
    }

    public function create(): View
    {
        return view('mis.offering-types.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:64'],
            'display_order' => ['nullable', 'integer', 'min:0', 'max:99999'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $base = $data['slug'] ? Str::slug($data['slug']) : Str::slug($data['name']);
        $slug = $base;
        $i = 1;
        while (OfferingType::query()->where('slug', $slug)->exists()) {
            $slug = $base.'-'.$i;
            $i++;
        }

        OfferingType::query()->create([
            'name' => $data['name'],
            'slug' => $slug,
            'display_order' => $data['display_order'] ?? (int) (OfferingType::query()->max('display_order') + 10),
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('mis.offering-types.index')->with('status', 'Offering type created.');
    }

    public function edit(OfferingType $offeringType): View
    {
        return view('mis.offering-types.edit', ['type' => $offeringType]);
    }

    public function update(Request $request, OfferingType $offeringType): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:64', 'unique:offering_types,slug,'.$offeringType->id],
            'display_order' => ['nullable', 'integer', 'min:0', 'max:99999'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $offeringType->update([
            'name' => $data['name'],
            'slug' => Str::slug($data['slug']),
            'display_order' => $data['display_order'] ?? $offeringType->display_order,
            'is_active' => $request->boolean('is_active'),
        ]);

        // Keep legacy offerings.type in sync for any downstream use.
        Offering::query()
            ->where('offering_type_id', $offeringType->id)
            ->update(['type' => $offeringType->slug]);

        return redirect()->route('mis.offering-types.index')->with('status', 'Offering type saved.');
    }

    public function destroy(OfferingType $offeringType): RedirectResponse
    {
        if (Offering::query()->where('offering_type_id', $offeringType->id)->exists()) {
            return back()->with('error', 'Move offerings off this type before deleting it.');
        }

        $offeringType->delete();

        return redirect()->route('mis.offering-types.index')->with('status', 'Offering type deleted.');
    }
}

