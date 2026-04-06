<?php

namespace App\Http\Controllers;

use App\Models\LegalDocument;
use Illuminate\Http\Response;
use Illuminate\View\View;

class PublicLegalController extends Controller
{
    public function show(string $slug): View|Response
    {
        $doc = LegalDocument::query()
            ->where('slug', $slug)
            ->where('status', 'published')
            ->first();

        if (! $doc) {
            abort(404);
        }

        return view('legal.show', compact('doc'));
    }
}

