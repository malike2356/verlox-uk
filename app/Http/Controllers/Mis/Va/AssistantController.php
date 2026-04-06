<?php

namespace App\Http\Controllers\Mis\Va;

use App\Http\Controllers\Controller;
use App\Models\VaAssistant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AssistantController extends Controller
{
    public function index(): View
    {
        $assistants = VaAssistant::query()->orderBy('full_name')->paginate(20);

        return view('mis.va.assistants.index', compact('assistants'));
    }

    public function create(): View
    {
        return view('mis.va.assistants.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['skills'] = $this->parseSkills($request->input('skills_raw'));
        $data['is_active'] = $request->boolean('is_active', true);

        VaAssistant::query()->create($data);

        return redirect()->route('mis.va.assistants.index')->with('status', 'Assistant created.');
    }

    public function edit(VaAssistant $va_assistant): View
    {
        return view('mis.va.assistants.edit', ['assistant' => $va_assistant]);
    }

    public function update(Request $request, VaAssistant $va_assistant): RedirectResponse
    {
        $data = $this->validated($request);
        $data['skills'] = $this->parseSkills($request->input('skills_raw'));
        $data['is_active'] = $request->boolean('is_active');
        $va_assistant->update($data);

        return redirect()->route('mis.va.assistants.index')->with('status', 'Assistant updated.');
    }

    public function destroy(VaAssistant $va_assistant): RedirectResponse
    {
        $va_assistant->update(['is_active' => false, 'availability' => 'inactive']);

        return redirect()->route('mis.va.assistants.index')->with('status', 'Assistant marked inactive.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request): array
    {
        return $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'country' => ['nullable', 'string', 'max:128'],
            'timezone' => ['required', 'string', 'max:64'],
            'hourly_rate_gbp' => ['required', 'numeric', 'min:0'],
            'availability' => ['required', 'in:'.implode(',', VaAssistant::$availabilities)],
            'perform_score' => ['nullable', 'numeric', 'between:1,5'],
            'wise_email' => ['nullable', 'email', 'max:255'],
            'payment_currency' => ['required', 'string', 'size:3'],
            'phone' => ['nullable', 'string', 'max:64'],
            'notes' => ['nullable', 'string', 'max:10000'],
        ]);
    }

    private function parseSkills(?string $raw): ?array
    {
        if ($raw === null || trim($raw) === '') {
            return null;
        }

        $parts = preg_split('/[,;\n\r]+/', $raw, -1, PREG_SPLIT_NO_EMPTY);
        if ($parts === false) {
            return null;
        }

        return array_values(array_filter(array_map('trim', $parts)));
    }
}
