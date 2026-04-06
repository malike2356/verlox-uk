<?php

namespace App\Http\Controllers\Mis;

use App\Http\Controllers\Controller;
use App\Models\BookingEventType;
use App\Models\BookingQuestion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class EventTypeController extends Controller
{
    public function index(): View
    {
        $types = BookingEventType::query()
            ->with('questions')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return view('mis.event-types.index', compact('types'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:500'],
            'duration_minutes' => ['required', 'integer', 'min:5', 'max:480'],
            'color' => ['nullable', 'string', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'price_gbp' => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
            'price_caption' => ['nullable', 'string', 'max:160'],
        ]);

        BookingEventType::create([
            'name' => $data['name'],
            'slug' => Str::slug($data['name']).'-'.Str::random(4),
            'description' => $data['description'] ?? null,
            'duration_minutes' => $data['duration_minutes'],
            'color' => $data['color'] ?? '#6366f1',
            'price_pence' => $this->optionalPence($data['price_gbp'] ?? null),
            'price_caption' => $this->optionalString($data['price_caption'] ?? null),
            'is_active' => true,
        ]);

        return back()->with('status', 'Event type created.');
    }

    public function update(Request $request, BookingEventType $eventType): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:500'],
            'duration_minutes' => ['required', 'integer', 'min:5', 'max:480'],
            'color' => ['nullable', 'string', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'is_active' => ['nullable', 'boolean'],
            'price_gbp' => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
            'price_caption' => ['nullable', 'string', 'max:160'],
        ]);

        $eventType->update([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'duration_minutes' => $data['duration_minutes'],
            'color' => $data['color'] ?? $eventType->color,
            'price_pence' => $this->optionalPence($data['price_gbp'] ?? null),
            'price_caption' => $this->optionalString($data['price_caption'] ?? null),
            'is_active' => $request->boolean('is_active'),
        ]);

        return back()->with('status', 'Event type updated.');
    }

    public function destroy(BookingEventType $eventType): RedirectResponse
    {
        $eventType->delete();

        return redirect()->route('mis.event-types.index')->with('status', 'Event type deleted.');
    }

    // ── Questions ────────────────────────────────────────────────────

    public function storeQuestion(Request $request, BookingEventType $eventType): RedirectResponse
    {
        $data = $request->validate([
            'label' => ['required', 'string', 'max:255'],
            'field_type' => ['required', 'in:text,textarea,select'],
            'options' => ['nullable', 'string'],
            'is_required' => ['nullable', 'boolean'],
        ]);

        $options = null;
        if ($data['field_type'] === 'select' && ! empty($data['options'])) {
            $options = array_values(array_filter(array_map('trim', explode("\n", $data['options']))));
        }

        $eventType->questions()->create([
            'label' => $data['label'],
            'field_type' => $data['field_type'],
            'options' => $options,
            'is_required' => $request->boolean('is_required'),
            'sort_order' => $eventType->questions()->count(),
        ]);

        return back()->with('status', 'Question added.');
    }

    public function destroyQuestion(BookingEventType $eventType, BookingQuestion $question): RedirectResponse
    {
        $question->delete();

        return back()->with('status', 'Question removed.');
    }

    private function optionalPence(null|int|float|string $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        return (int) round((float) $value * 100);
    }

    private function optionalString(?string $value): ?string
    {
        if ($value === null || trim($value) === '') {
            return null;
        }

        return $value;
    }
}
