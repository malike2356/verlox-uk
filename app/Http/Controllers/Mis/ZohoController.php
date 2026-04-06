<?php

namespace App\Http\Controllers\Mis;

use App\Http\Controllers\Controller;
use App\Models\ZohoSyncLog;
use App\Services\ZohoBooksClient;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ZohoController extends Controller
{
    public function index(): View
    {
        $logs = ZohoSyncLog::query()->orderByDesc('id')->limit(50)->get();

        return view('mis.zoho.index', compact('logs'));
    }

    public function test(ZohoBooksClient $client): RedirectResponse
    {
        $result = $client->testConnection();

        return back()->with($result['ok'] ? 'status' : 'error', $result['message']);
    }
}
