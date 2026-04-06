<?php

namespace App\Http\Controllers;

use App\Mail\NewLeadInternalMail;
use App\Models\CompanySetting;
use App\Models\Lead;
use App\Models\Offering;
use App\Models\PipelineStage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class LeadCaptureController extends Controller
{
    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $data = $request->validate([
            'contact_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:64'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'message' => ['nullable', 'string', 'max:10000'],
            'offering_id' => ['nullable', 'exists:offerings,id'],
            'source' => ['nullable', 'string', 'max:64'],
            'utm_source' => ['nullable', 'string', 'max:128'],
            'utm_medium' => ['nullable', 'string', 'max:128'],
            'utm_campaign' => ['nullable', 'string', 'max:128'],
            'utm_term' => ['nullable', 'string', 'max:128'],
            'utm_content' => ['nullable', 'string', 'max:128'],
        ]);

        $stage = PipelineStage::query()->orderBy('sort_order')->firstOrFail();
        $offeringId = $data['offering_id'] ?? null;
        if ($offeringId) {
            $offering = Offering::query()->find($offeringId);
            $source = $data['source'] ?? ($offering ? 'web:'.$offering->slug : 'web');
        } else {
            $source = $data['source'] ?? 'web';
        }

        $utmPayload = array_filter([
            'utm_source' => $data['utm_source'] ?? $request->query('utm_source'),
            'utm_medium' => $data['utm_medium'] ?? $request->query('utm_medium'),
            'utm_campaign' => $data['utm_campaign'] ?? $request->query('utm_campaign'),
            'utm_term' => $data['utm_term'] ?? $request->query('utm_term'),
            'utm_content' => $data['utm_content'] ?? $request->query('utm_content'),
        ], fn ($v) => $v !== null && $v !== '');

        $existing = Lead::query()
            ->whereRaw('LOWER(email) = ?', [strtolower($data['email'])])
            ->whereNotIn('status', ['converted', 'lost'])
            ->first();

        if ($existing) {
            $note = 'Repeat web form submission.';
            if (! empty($data['message'])) {
                $note .= "\n\n".$data['message'];
            }
            $existing->activities()->create([
                'user_id' => null,
                'type' => 'note',
                'body' => $note,
            ]);
            foreach ($utmPayload as $k => $v) {
                if ($existing->{$k} === null || $existing->{$k} === '') {
                    $existing->{$k} = $v;
                }
            }
            if (! empty($utmPayload)) {
                $existing->save();
            }
            $lead = $existing;
        } else {
            $lead = Lead::create(array_merge([
                'pipeline_stage_id' => $stage->id,
                'offering_id' => $offeringId,
                'company_name' => $data['company_name'] ?? null,
                'contact_name' => $data['contact_name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'message' => $data['message'] ?? null,
                'source' => $source,
                'status' => 'new',
            ], $utmPayload));
        }

        $lead->load('pipelineStage');

        $to = CompanySetting::current()->support_email ?? config('mail.from.address');
        if ($to) {
            Mail::to($to)->send(new NewLeadInternalMail($lead));
        }

        if ($request->expectsJson()) {
            return response()->json(['ok' => true, 'message' => 'Thank you. We have received your request and will respond shortly.']);
        }

        $status = 'Thank you. We have received your request and will respond shortly.';

        $source = (string) ($data['source'] ?? '');
        $referer = (string) $request->headers->get('referer', '');

        $anchor = match (true) {
            Str::startsWith($source, 'web:contact') => '#contact',
            Str::startsWith($source, 'web:va') => '#enquiry',
            default => null,
        };

        if ($anchor && $referer !== '') {
            return redirect()->to($referer.$anchor)->with('status', $status);
        }

        return back()->with('status', $status);
    }
}
