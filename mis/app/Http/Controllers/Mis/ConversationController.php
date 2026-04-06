<?php

namespace App\Http\Controllers\Mis;

use App\Http\Controllers\Controller;
use App\Mail\OutboundClientMessageMail;
use App\Models\Client;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class ConversationController extends Controller
{
    public function index(): View
    {
        $conversations = Conversation::query()
            ->with('client')
            ->orderByDesc('last_activity_at')
            ->paginate(25);

        return view('mis.conversations.index', compact('conversations'));
    }

    public function create(Request $request): View
    {
        $clientId = $request->query('client_id');
        $clients = Client::query()->orderBy('contact_name')->limit(500)->get();

        return view('mis.conversations.create', compact('clients', 'clientId'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'client_id' => ['required', 'exists:clients,id'],
            'subject' => ['nullable', 'string', 'max:255'],
            'body' => ['required', 'string', 'max:20000'],
            'send_email' => ['nullable', 'boolean'],
        ]);

        $client = Client::query()->findOrFail($data['client_id']);
        $conversation = Conversation::create([
            'client_id' => $client->id,
            'subject' => $data['subject'] ?? 'Conversation',
            'last_activity_at' => now(),
        ]);

        $bodyHtml = nl2br(e($data['body']));
        Message::create([
            'conversation_id' => $conversation->id,
            'user_id' => $request->user()->id,
            'direction' => 'outbound',
            'body' => $data['body'],
        ]);

        if ($request->boolean('send_email')) {
            $subjectLine = $data['subject'] ?: 'Message from '.config('app.name');
            Mail::to($client->email)->send(new OutboundClientMessageMail($client, $subjectLine, $bodyHtml));
        }

        return redirect()->route('mis.conversations.show', $conversation)->with('status', 'Message recorded.');
    }

    public function show(Conversation $conversation): View
    {
        $conversation->load(['client', 'messages.user']);

        return view('mis.conversations.show', compact('conversation'));
    }

    public function reply(Request $request, Conversation $conversation): RedirectResponse
    {
        $data = $request->validate([
            'body' => ['required', 'string', 'max:20000'],
            'send_email' => ['nullable', 'boolean'],
        ]);

        $conversation->client->email;
        $bodyHtml = nl2br(e($data['body']));
        Message::create([
            'conversation_id' => $conversation->id,
            'user_id' => $request->user()->id,
            'direction' => 'outbound',
            'body' => $data['body'],
        ]);
        $conversation->update(['last_activity_at' => now()]);

        if ($request->boolean('send_email')) {
            $subject = $conversation->subject ?: 'Message from '.config('app.name');
            Mail::to($conversation->client->email)->send(
                new OutboundClientMessageMail($conversation->client, 'Re: '.$subject, $bodyHtml)
            );
        }

        return back()->with('status', 'Reply sent.');
    }

    public function destroy(Conversation $conversation): RedirectResponse
    {
        $conversation->delete();

        return redirect()->route('mis.conversations.index')->with('status', 'Conversation deleted.');
    }
}
