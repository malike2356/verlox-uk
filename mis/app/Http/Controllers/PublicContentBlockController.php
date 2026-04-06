<?php

namespace App\Http\Controllers;

use App\Models\ContentBlock;
use Illuminate\Http\JsonResponse;

class PublicContentBlockController extends Controller
{
    /**
     * Active CMS blocks for the marketing site or headless consumers.
     */
    public function __invoke(): JsonResponse
    {
        $blocks = ContentBlock::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('key')
            ->get(['key', 'title', 'type', 'body']);

        return response()->json(['blocks' => $blocks]);
    }
}
