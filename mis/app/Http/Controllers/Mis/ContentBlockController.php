<?php

namespace App\Http\Controllers\Mis;

use App\Http\Controllers\Controller;
use App\Models\ContentBlock;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContentBlockController extends Controller
{
    public function index(): View
    {
        $blocks = ContentBlock::query()->orderBy('sort_order')->orderBy('key')->get();

        // Group by key prefix (first segment before underscore)
        $grouped = $blocks->groupBy(fn ($b) => ucwords(str_replace('_', ' ', explode('_', $b->key)[0])));

        return view('mis.content-blocks.index', compact('grouped'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'key' => ['required', 'string', 'max:128', 'unique:content_blocks,key', 'regex:/^[a-z0-9_]+$/'],
            'title' => ['nullable', 'string', 'max:255'],
            'type' => ['required', 'in:text,textarea,html,image_url'],
            'body' => ['nullable', 'string', 'max:50000'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        ContentBlock::create([
            'key' => $data['key'],
            'title' => $data['title'] ?? null,
            'type' => $data['type'],
            'body' => $data['body'] ?? null,
            'sort_order' => $data['sort_order'] ?? 0,
            'is_active' => true,
        ]);

        return back()->with('status', 'Content block created.');
    }

    public function edit(ContentBlock $contentBlock): View
    {
        return view('mis.content-blocks.edit', compact('contentBlock'));
    }

    public function update(Request $request, ContentBlock $contentBlock): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'type' => ['nullable', 'in:text,textarea,html,image_url'],
            'body' => ['nullable', 'string', 'max:50000'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $contentBlock->update([
            'title' => $data['title'] ?? $contentBlock->title,
            'type' => $data['type'] ?? $contentBlock->type,
            'body' => $data['body'] ?? null,
            'sort_order' => $data['sort_order'] ?? $contentBlock->sort_order,
            'is_active' => isset($data['is_active']) ? (bool) $data['is_active'] : $contentBlock->is_active,
        ]);

        return back()->with('status', 'Content updated.');
    }

    public function destroy(ContentBlock $contentBlock): RedirectResponse
    {
        $contentBlock->delete();

        return redirect()->route('mis.content-blocks.index')->with('status', 'Block deleted.');
    }
}
