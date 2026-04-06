<?php

namespace App\Http\Controllers\Mis;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Models\ContractTemplate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContractTemplateController extends Controller
{
    public function index(): View
    {
        $templates = ContractTemplate::query()->orderBy('name')->get();

        return view('mis.contract-templates.index', compact('templates'));
    }

    public function create(): View
    {
        return view('mis.contract-templates.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:contract_templates,slug'],
            'body' => ['required', 'string'],
            'is_default' => ['nullable', 'boolean'],
        ]);
        if ($request->boolean('is_default')) {
            ContractTemplate::query()->update(['is_default' => false]);
        }
        ContractTemplate::create([
            'name' => $data['name'],
            'slug' => $data['slug'],
            'body' => $data['body'],
            'is_default' => $request->boolean('is_default'),
        ]);

        return redirect()->route('mis.contract-templates.index')->with('status', 'Template saved.');
    }

    public function edit(ContractTemplate $contractTemplate): View
    {
        return view('mis.contract-templates.edit', compact('contractTemplate'));
    }

    public function update(Request $request, ContractTemplate $contractTemplate): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:contract_templates,slug,'.$contractTemplate->id],
            'body' => ['required', 'string'],
            'is_default' => ['nullable', 'boolean'],
        ]);
        if ($request->boolean('is_default')) {
            ContractTemplate::query()->where('id', '!=', $contractTemplate->id)->update(['is_default' => false]);
        }
        $contractTemplate->update([
            'name' => $data['name'],
            'slug' => $data['slug'],
            'body' => $data['body'],
            'is_default' => $request->boolean('is_default'),
        ]);

        return redirect()->route('mis.contract-templates.index')->with('status', 'Template updated.');
    }

    public function destroy(ContractTemplate $contractTemplate): RedirectResponse
    {
        if (Contract::query()->where('contract_template_id', $contractTemplate->id)->exists()) {
            return redirect()->route('mis.contract-templates.index')
                ->with('error', 'Templates referenced by contracts cannot be deleted.');
        }

        if ($contractTemplate->is_default) {
            $next = ContractTemplate::query()->where('id', '!=', $contractTemplate->id)->orderBy('id')->first();
            if ($next) {
                $next->update(['is_default' => true]);
            }
        }

        $contractTemplate->delete();

        return redirect()->route('mis.contract-templates.index')->with('status', 'Template deleted.');
    }
}
