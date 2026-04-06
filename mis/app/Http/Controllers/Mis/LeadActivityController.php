<?php

namespace App\Http\Controllers\Mis;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LeadActivityController extends Controller
{
    public function store(Request $request, Lead $lead): RedirectResponse
    {
        $data = $request->validate([
            'type' => ['required', 'in:note,call,email'],
            'body' => ['required', 'string', 'max:10000'],
        ]);

        $lead->activities()->create([
            'user_id' => $request->user()->id,
            'type' => $data['type'],
            'body' => $data['body'],
        ]);

        return back()->with('status', 'Activity logged.');
    }
}
