<?php

namespace App\Http\Controllers\Mis;

use App\Http\Controllers\Controller;
use App\Models\ContentBlock;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\File;
use Illuminate\View\View;

class ContentBlockController extends Controller
{
    private const ALLOWED_TYPES = 'text,textarea,html,markdown,image_url';

    public function index(): View
    {
        $blocks = ContentBlock::query()->orderBy('sort_order')->orderBy('key')->get();

        $grouped = $blocks->groupBy(fn ($b) => ucwords(str_replace('_', ' ', explode('_', $b->key)[0])));

        $stats = [
            'total'    => $blocks->count(),
            'active'   => $blocks->where('is_active', true)->count(),
            'inactive' => $blocks->where('is_active', false)->count(),
            'sections' => $grouped->count(),
        ];

        return view('mis.content-blocks.index', compact('grouped', 'stats'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'key'        => ['required', 'string', 'max:128', 'unique:content_blocks,key', 'regex:/^[a-z0-9_]+$/'],
            'title'      => ['nullable', 'string', 'max:255'],
            'type'       => ['required', 'in:' . self::ALLOWED_TYPES],
            'body'       => ['nullable', 'string', 'max:200000'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        ContentBlock::create([
            'key'        => $data['key'],
            'title'      => $data['title'] ?? null,
            'type'       => $data['type'],
            'body'       => $data['body'] ?? null,
            'sort_order' => $data['sort_order'] ?? 0,
            'is_active'  => true,
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
            'title'      => ['nullable', 'string', 'max:255'],
            'type'       => ['nullable', 'in:' . self::ALLOWED_TYPES],
            'body'       => ['nullable', 'string', 'max:200000'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active'  => ['nullable', 'boolean'],
        ]);

        $contentBlock->update([
            'title'      => $data['title'] ?? $contentBlock->title,
            'type'       => $data['type'] ?? $contentBlock->type,
            'body'       => $data['body'] ?? null,
            'sort_order' => $data['sort_order'] ?? $contentBlock->sort_order,
            'is_active'  => isset($data['is_active']) ? (bool) $data['is_active'] : $contentBlock->is_active,
        ]);

        return back()->with('status', 'content-block-saved');
    }

    public function duplicate(ContentBlock $contentBlock): RedirectResponse
    {
        $new = $contentBlock->replicate();
        $new->key = $contentBlock->key . '_copy_' . time();
        $new->title = ($contentBlock->title ? $contentBlock->title . ' (copy)' : null);
        $new->is_active = false;
        $new->save();

        return redirect()
            ->route('mis.content-blocks.edit', $new)
            ->with('status', 'Block duplicated. Update the key and activate when ready.');
    }

    public function destroy(ContentBlock $contentBlock): RedirectResponse
    {
        $contentBlock->delete();

        return redirect()->route('mis.content-blocks.index')->with('status', 'Block deleted.');
    }

    /** Upload an image from the Quill editor and return the public URL. */
    public function uploadImage(Request $request): JsonResponse
    {
        $request->validate([
            'image' => ['required', File::types(['png', 'jpg', 'jpeg', 'gif', 'webp'])->max(4096)],
        ]);

        $path = $request->file('image')->store('content-images', 'public');

        return response()->json(['url' => Storage::disk('public')->url($path)]);
    }
}
