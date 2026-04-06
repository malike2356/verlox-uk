<?php

namespace App\Http\Controllers\Mis;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\LegalDocument;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class LegalDocumentController extends Controller
{
    public function index(): View
    {
        $documents = LegalDocument::query()
            ->orderBy('category')
            ->orderBy('title')
            ->get();

        return view('mis.legal-documents.index', compact('documents'));
    }

    public function create(): View
    {
        return view('mis.legal-documents.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:legal_documents,slug'],
            'category' => ['required', 'string', 'max:64'],
            'effective_at' => ['nullable', 'date'],
            'status' => ['required', 'in:'.implode(',', LegalDocument::STATUSES)],
            'body_html' => ['required', 'string'],
        ]);

        $slug = $data['slug'] ?: Str::slug($data['title']);

        LegalDocument::create([
            'title' => $data['title'],
            'slug' => $slug,
            'category' => $data['category'],
            'effective_at' => $data['effective_at'] ?? null,
            'status' => $data['status'],
            'body_html' => $data['body_html'],
        ]);

        return redirect()->route('mis.legal-documents.index')->with('status', 'Legal document saved.');
    }

    public function edit(LegalDocument $legalDocument): View
    {
        return view('mis.legal-documents.edit', compact('legalDocument'));
    }

    public function update(Request $request, LegalDocument $legalDocument): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:legal_documents,slug,'.$legalDocument->id],
            'category' => ['required', 'string', 'max:64'],
            'effective_at' => ['nullable', 'date'],
            'status' => ['required', 'in:'.implode(',', LegalDocument::STATUSES)],
            'body_html' => ['required', 'string'],
        ]);

        $legalDocument->update($data);

        return redirect()->route('mis.legal-documents.index')->with('status', 'Legal document updated.');
    }

    public function destroy(LegalDocument $legalDocument): RedirectResponse
    {
        $legalDocument->delete();

        return redirect()->route('mis.legal-documents.index')->with('status', 'Legal document deleted.');
    }

    public function downloadHtml(LegalDocument $legalDocument)
    {
        $filename = $legalDocument->slug.'.html';
        $html = "<!doctype html>\n<html lang=\"en\">\n<head>\n<meta charset=\"utf-8\">\n<meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">\n<title>".e($legalDocument->title)."</title>\n</head>\n<body>\n".$legalDocument->body_html."\n</body>\n</html>\n";

        return response($html, 200, [
            'Content-Type' => 'text/html; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }

    public function createDocumentRecord(LegalDocument $legalDocument): RedirectResponse
    {
        $filename = 'legal/'.$legalDocument->slug.'-'.now()->format('Ymd-His').'.html';
        $html = $legalDocument->body_html;

        Storage::disk('local')->put('documents/'.$filename, $html);

        $fullPath = storage_path('app/documents/'.$filename);
        $size = is_file($fullPath) ? filesize($fullPath) : null;

        Document::create([
            'client_id' => null,
            'title' => $legalDocument->title,
            'file_path' => 'documents/'.$filename,
            'mime' => 'text/html',
            'size_bytes' => $size === false ? null : $size,
        ]);

        return redirect()->route('mis.documents.index')->with('status', 'Legal document saved to Documents.');
    }
}

