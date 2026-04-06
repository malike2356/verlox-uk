<?php

namespace App\Http\Controllers\Mis;

use App\Http\Controllers\Controller;
use App\Services\MisSmartSearchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SmartSearchController extends Controller
{
    public function __invoke(Request $request, MisSmartSearchService $search): JsonResponse
    {
        $q = (string) $request->query('q', '');

        return response()->json([
            'categories' => $search->search($q, $request->user()),
        ]);
    }
}
