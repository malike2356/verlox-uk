<?php

namespace App\Http\Controllers\Mis;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Document;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class DocumentController extends Controller
{
    public function index(): View
    {
        $documents = Document::query()->with('client')->orderByDesc('id')->paginate(30);

        return view('mis.documents.index', compact('documents'));
    }

    public function create(Request $request): View
    {
        $clientId = $request->query('client_id');
        $clients = Client::query()->orderBy('contact_name')->limit(500)->get();

        return view('mis.documents.create', compact('clients', 'clientId'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'client_id' => ['nullable', 'exists:clients,id'],
            'title' => ['required', 'string', 'max:255'],
            'file' => ['required', 'file', 'max:10240'],
        ]);

        $path = $request->file('file')->store('documents', 'local');
        $file = $request->file('file');

        Document::create([
            'client_id' => $data['client_id'] ?? null,
            'title' => $data['title'],
            'file_path' => $path,
            'mime' => $file->getMimeType(),
            'size_bytes' => $file->getSize(),
        ]);

        return redirect()->route('mis.documents.index')->with('status', 'Document uploaded.');
    }

    public function destroy(Document $document): RedirectResponse
    {
        if (Storage::disk('local')->exists($document->file_path)) {
            Storage::disk('local')->delete($document->file_path);
        }
        $document->delete();

        return redirect()->route('mis.documents.index')->with('status', 'Document removed.');
    }
}
