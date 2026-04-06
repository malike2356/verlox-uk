<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Client;
use App\Models\Contract;
use App\Models\Conversation;
use App\Models\Document;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\Lead;
use App\Models\Message;
use App\Models\Offering;
use App\Models\PricingPlan;
use App\Models\Quotation;
use App\Models\QuotationLine;
use App\Models\User;
use App\Models\VaAssistant;
use App\Models\VaClientAccount;
use App\Models\VaEngagement;
use App\Models\VaTimeLog;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class MisSmartSearchService
{
    private const LIMIT = 8;

    /** Postgres LIKE is case-sensitive; use ILIKE so mixed-case data still matches. */
    private function likeOp(): string
    {
        return DB::connection()->getDriverName() === 'pgsql' ? 'ilike' : 'like';
    }

    /**
     * @return list<array{title: string, items: list<array{label: string, meta: string, url: string}>}>
     */
    public function search(string $query, User $user): array
    {
        $trim = trim($query);
        if ($trim === '') {
            return [];
        }
        if (strlen($trim) < 2 && ! preg_match('/^\d+$/', $trim)) {
            return [];
        }

        if ($user->isMisVaOnly()) {
            return $this->appendHelpResults($trim, $this->searchVaScope($trim));
        }

        return $this->appendHelpResults($trim, $this->searchFullMis($trim, $user));
    }

    /**
     * @return list<array{title: string, items: list<array{label: string, meta: string, url: string}>}>
     */
    private function searchVaScope(string $q): array
    {
        $categories = [];
        $nid = $this->numericId($q);
        $like = $this->like($q);

        $accounts = VaClientAccount::query()
            ->where(function (Builder $w) use ($like, $nid): void {
                $w->where('company_name', $this->likeOp(), $like)
                    ->orWhere('contact_name', $this->likeOp(), $like)
                    ->orWhere('email', $this->likeOp(), $like)
                    ->orWhere('phone', $this->likeOp(), $like);
                if ($nid !== null) {
                    $w->orWhere('id', $nid);
                }
            })
            ->orderByDesc('updated_at')
            ->limit(self::LIMIT)
            ->get();

        if ($accounts->isNotEmpty()) {
            $categories[] = [
                'title' => 'VA clients',
                'items' => $accounts->map(fn (VaClientAccount $a) => [
                    'label' => $a->company_name ?: $a->contact_name ?: 'Client #'.$a->id,
                    'meta' => $a->email ?? $a->status,
                    'url' => route('mis.va.client-accounts.show', $a),
                ])->all(),
            ];
        }

        $engagements = VaEngagement::query()
            ->with(['clientAccount', 'assistant'])
            ->where(function (Builder $w) use ($like, $nid): void {
                $w->where('notes', $this->likeOp(), $like)
                    ->orWhere('tier', $this->likeOp(), $like)
                    ->orWhere('status', $this->likeOp(), $like);
                if ($nid !== null) {
                    $w->orWhere('id', $nid);
                }
            })
            ->orderByDesc('updated_at')
            ->limit(self::LIMIT)
            ->get();

        if ($engagements->isNotEmpty()) {
            $categories[] = [
                'title' => 'VA engagements',
                'items' => $engagements->map(function (VaEngagement $e) {
                    $acc = $e->clientAccount;

                    return [
                        'label' => 'Engagement #'.$e->id.($acc ? ' - '.($acc->company_name ?? '') : ''),
                        'meta' => $e->status.(($a = $e->assistant) ? ' · '.$a->full_name : ''),
                        'url' => route('mis.va.engagements.edit', $e),
                    ];
                })->all(),
            ];
        }

        $assistants = VaAssistant::query()
            ->where(function (Builder $w) use ($like, $nid): void {
                $w->where('full_name', $this->likeOp(), $like)
                    ->orWhere('email', $this->likeOp(), $like)
                    ->orWhere('phone', $this->likeOp(), $like);
                if ($nid !== null) {
                    $w->orWhere('id', $nid);
                }
            })
            ->orderBy('full_name')
            ->limit(self::LIMIT)
            ->get();

        if ($assistants->isNotEmpty()) {
            $categories[] = [
                'title' => 'Assistants',
                'items' => $assistants->map(fn (VaAssistant $a) => [
                    'label' => $a->full_name,
                    'meta' => $a->email ?? $a->availability,
                    'url' => route('mis.va.assistants.edit', $a),
                ])->all(),
            ];
        }

        $logs = VaTimeLog::query()
            ->where(function (Builder $w) use ($like, $nid): void {
                $w->where('task_description', $this->likeOp(), $like);
                if ($nid !== null) {
                    $w->orWhere('id', $nid);
                }
            })
            ->orderByDesc('work_date')
            ->limit(self::LIMIT)
            ->get();

        if ($logs->isNotEmpty()) {
            $categories[] = [
                'title' => 'VA time logs',
                'items' => $logs->map(fn (VaTimeLog $t) => [
                    'label' => 'Time log #'.$t->id,
                    'meta' => (string) $t->work_date,
                    'url' => route('mis.va.time-logs.index'),
                ])->all(),
            ];
        }

        return $categories;
    }

    /**
     * @return list<array{title: string, items: list<array{label: string, meta: string, url: string}>}>
     */
    private function searchFullMis(string $q, User $user): array
    {
        $categories = [];
        $nid = $this->numericId($q);
        $like = $this->like($q);

        $leads = Lead::query()
            ->where(function (Builder $w) use ($like, $nid): void {
                $w->where('company_name', $this->likeOp(), $like)
                    ->orWhere('contact_name', $this->likeOp(), $like)
                    ->orWhere('email', $this->likeOp(), $like)
                    ->orWhere('phone', $this->likeOp(), $like)
                    ->orWhere('source', $this->likeOp(), $like)
                    ->orWhere('message', $this->likeOp(), $like)
                    ->orWhere('loss_reason', $this->likeOp(), $like);
                if ($nid !== null) {
                    $w->orWhere('id', $nid);
                }
            })
            ->orderByDesc('updated_at')
            ->limit(self::LIMIT)
            ->get();

        if ($leads->isNotEmpty()) {
            $categories[] = [
                'title' => 'Leads',
                'items' => $leads->map(fn (Lead $l) => [
                    'label' => $l->company_name ?: $l->contact_name ?: 'Lead #'.$l->id,
                    'meta' => $l->email ?? $l->status,
                    'url' => route('mis.leads.show', $l),
                ])->all(),
            ];
        }

        $clients = Client::query()
            ->where(function (Builder $w) use ($like, $nid): void {
                $w->where('company_name', $this->likeOp(), $like)
                    ->orWhere('contact_name', $this->likeOp(), $like)
                    ->orWhere('email', $this->likeOp(), $like)
                    ->orWhere('phone', $this->likeOp(), $like)
                    ->orWhere('address', $this->likeOp(), $like)
                    ->orWhere('notes', $this->likeOp(), $like);
                if ($nid !== null) {
                    $w->orWhere('id', $nid);
                }
            })
            ->orderByDesc('updated_at')
            ->limit(self::LIMIT)
            ->get();

        if ($clients->isNotEmpty()) {
            $categories[] = [
                'title' => 'Clients',
                'items' => $clients->map(fn (Client $c) => [
                    'label' => $c->company_name ?: $c->contact_name ?: 'Client #'.$c->id,
                    'meta' => $c->email ?? '',
                    'url' => route('mis.clients.show', $c),
                ])->all(),
            ];
        }

        $invoices = Invoice::query()
            ->with('client')
            ->where(function (Builder $w) use ($like, $nid): void {
                $w->where('number', $this->likeOp(), $like)
                    ->orWhereHas('lines', function (Builder $lq) use ($like): void {
                        $lq->where('description', $this->likeOp(), $like);
                    })
                    ->orWhereHas('client', function (Builder $cq) use ($like): void {
                        $this->scopeClientTextMatches($cq, $like);
                    });
                if ($nid !== null) {
                    $w->orWhere('id', $nid);
                }
            })
            ->orderByDesc('id')
            ->limit(self::LIMIT)
            ->get();

        if ($invoices->isNotEmpty()) {
            $categories[] = [
                'title' => 'Invoices',
                'items' => $invoices->map(fn (Invoice $i) => [
                    'label' => $i->number ?: 'Invoice #'.$i->id,
                    'meta' => $i->status.($i->client ? ' · '.($i->client->company_name ?? '') : ''),
                    'url' => route('mis.invoices.show', $i),
                ])->all(),
            ];
        }

        $quotations = Quotation::query()
            ->with('client')
            ->where(function (Builder $w) use ($like, $nid): void {
                $w->where('number', $this->likeOp(), $like)
                    ->orWhere('terms', $this->likeOp(), $like)
                    ->orWhereHas('lines', function (Builder $lq) use ($like): void {
                        $lq->where('description', $this->likeOp(), $like);
                    })
                    ->orWhereHas('client', function (Builder $cq) use ($like): void {
                        $this->scopeClientTextMatches($cq, $like);
                    });
                if ($nid !== null) {
                    $w->orWhere('id', $nid);
                }
            })
            ->orderByDesc('id')
            ->limit(self::LIMIT)
            ->get();

        if ($quotations->isNotEmpty()) {
            $categories[] = [
                'title' => 'Quotations',
                'items' => $quotations->map(fn (Quotation $q2) => [
                    'label' => $q2->number ?: 'Quotation #'.$q2->id,
                    'meta' => $q2->status,
                    'url' => route('mis.quotations.show', $q2),
                ])->all(),
            ];
        }

        $contracts = Contract::query()
            ->with('client')
            ->where(function (Builder $w) use ($like, $nid): void {
                $w->where('number', $this->likeOp(), $like)
                    ->orWhere('body_snapshot', $this->likeOp(), $like)
                    ->orWhereHas('client', function (Builder $cq) use ($like): void {
                        $this->scopeClientTextMatches($cq, $like);
                    });
                if ($nid !== null) {
                    $w->orWhere('id', $nid);
                }
            })
            ->orderByDesc('id')
            ->limit(self::LIMIT)
            ->get();

        if ($contracts->isNotEmpty()) {
            $categories[] = [
                'title' => 'Contracts',
                'items' => $contracts->map(fn (Contract $c) => [
                    'label' => $c->number ?: 'Contract #'.$c->id,
                    'meta' => $c->status,
                    'url' => route('mis.contracts.show', $c),
                ])->all(),
            ];
        }

        $bookings = Booking::query()
            ->where(function (Builder $w) use ($like, $nid): void {
                $w->where('contact_name', $this->likeOp(), $like)
                    ->orWhere('contact_email', $this->likeOp(), $like)
                    ->orWhere('internal_notes', $this->likeOp(), $like);
                if ($nid !== null) {
                    $w->orWhere('id', $nid);
                }
            })
            ->orderByDesc('starts_at')
            ->limit(self::LIMIT)
            ->get();

        if ($bookings->isNotEmpty()) {
            $categories[] = [
                'title' => 'Bookings',
                'items' => $bookings->map(fn (Booking $b) => [
                    'label' => $b->contact_name ?: 'Booking #'.$b->id,
                    'meta' => $b->starts_at?->format('Y-m-d H:i') ?? $b->status,
                    'url' => route('mis.bookings.show', $b),
                ])->all(),
            ];
        }

        $documents = Document::query()
            ->with('client')
            ->where(function (Builder $w) use ($like, $nid): void {
                $w->where('title', $this->likeOp(), $like)
                    ->orWhere('file_path', $this->likeOp(), $like);
                if ($nid !== null) {
                    $w->orWhere('id', $nid);
                }
            })
            ->orderByDesc('id')
            ->limit(self::LIMIT)
            ->get();

        if ($documents->isNotEmpty()) {
            $categories[] = [
                'title' => 'Documents',
                'items' => $documents->map(fn (Document $d) => [
                    'label' => $d->title ?: basename((string) $d->file_path),
                    'meta' => $d->client?->company_name ?? '',
                    'url' => route('mis.documents.index'),
                ])->all(),
            ];
        }

        $conversations = Conversation::query()
            ->where(function (Builder $w) use ($like, $nid): void {
                $w->where('subject', $this->likeOp(), $like)
                    ->orWhereHas('messages', function (Builder $mq) use ($like): void {
                        $mq->where('body', $this->likeOp(), $like);
                    });
                if ($nid !== null) {
                    $w->orWhere('id', $nid);
                }
            })
            ->orderByDesc('last_activity_at')
            ->limit(self::LIMIT)
            ->get();

        if ($conversations->isNotEmpty()) {
            $categories[] = [
                'title' => 'Messages',
                'items' => $conversations->map(fn (Conversation $c) => [
                    'label' => $c->subject ?: 'Thread #'.$c->id,
                    'meta' => $c->last_activity_at?->diffForHumans() ?? '',
                    'url' => route('mis.conversations.show', $c),
                ])->all(),
            ];
        }

        $expenses = Expense::query()
            ->where(function (Builder $w) use ($like, $nid): void {
                $w->where('vendor', $this->likeOp(), $like)
                    ->orWhere('description', $this->likeOp(), $like)
                    ->orWhere('reference', $this->likeOp(), $like)
                    ->orWhere('category', $this->likeOp(), $like);
                if ($nid !== null) {
                    $w->orWhere('id', $nid);
                }
            })
            ->orderByDesc('date')
            ->limit(self::LIMIT)
            ->get();

        if ($expenses->isNotEmpty()) {
            $categories[] = [
                'title' => 'Expenses',
                'items' => $expenses->map(fn (Expense $e) => [
                    'label' => $e->vendor ?: $e->description ?: 'Expense #'.$e->id,
                    'meta' => $e->date?->format('Y-m-d') ?? '',
                    'url' => route('mis.finance.expenses.index'),
                ])->all(),
            ];
        }

        if ($user->is_admin) {
            $users = User::query()
                ->where(function (Builder $w) use ($like, $nid): void {
                    $w->where('name', $this->likeOp(), $like)
                        ->orWhere('email', $this->likeOp(), $like);
                    if ($nid !== null) {
                        $w->orWhere('id', $nid);
                    }
                })
                ->orderBy('name')
                ->limit(self::LIMIT)
                ->get();

            if ($users->isNotEmpty()) {
                $categories[] = [
                    'title' => 'Users',
                    'items' => $users->map(fn (User $u) => [
                        'label' => $u->name,
                        'meta' => $u->email,
                        'url' => route('mis.users.edit', $u),
                    ])->all(),
                ];
            }

            $offerings = Offering::query()
                ->where(function (Builder $w) use ($like, $nid): void {
                    $w->where('name', $this->likeOp(), $like)
                        ->orWhere('slug', $this->likeOp(), $like)
                        ->orWhere('summary', $this->likeOp(), $like)
                        ->orWhere('description', $this->likeOp(), $like);
                    if ($nid !== null) {
                        $w->orWhere('id', $nid);
                    }
                })
                ->orderBy('display_order')
                ->orderBy('name')
                ->limit(self::LIMIT)
                ->get();

            if ($offerings->isNotEmpty()) {
                $categories[] = [
                    'title' => 'Offerings',
                    'items' => $offerings->map(fn (Offering $o) => [
                        'label' => $o->name,
                        'meta' => $o->slug,
                        'url' => route('mis.offerings.edit', $o),
                    ])->all(),
                ];
            }

            $plans = PricingPlan::query()
                ->where(function (Builder $w) use ($like, $nid): void {
                    $w->where('name', $this->likeOp(), $like)
                        ->orWhere('slug', $this->likeOp(), $like)
                        ->orWhere('tagline', $this->likeOp(), $like)
                        ->orWhere('description', $this->likeOp(), $like);
                    if ($nid !== null) {
                        $w->orWhere('id', $nid);
                    }
                })
                ->orderBy('display_order')
                ->orderBy('name')
                ->limit(self::LIMIT)
                ->get();

            if ($plans->isNotEmpty()) {
                $categories[] = [
                    'title' => 'Pricing plans',
                    'items' => $plans->map(fn (PricingPlan $p) => [
                        'label' => $p->name,
                        'meta' => $p->tagline ?? $p->slug,
                        'url' => route('mis.pricing-plans.edit', $p),
                    ])->all(),
                ];
            }
        }

        return $categories;
    }

    /**
     * Match MIS client name / email / phone (used for invoice, quote, contract search).
     */
    private function scopeClientTextMatches(Builder $clientQuery, string $like): void
    {
        $clientQuery->where(function (Builder $w) use ($like): void {
            $w->where('company_name', $this->likeOp(), $like)
                ->orWhere('contact_name', $this->likeOp(), $like)
                ->orWhere('email', $this->likeOp(), $like)
                ->orWhere('phone', $this->likeOp(), $like);
        });
    }

    private function like(string $q): string
    {
        return '%'.addcslashes($q, '%_\\').'%';
    }

    private function numericId(string $q): ?int
    {
        if (! preg_match('/^\d+$/', trim($q))) {
            return null;
        }
        $id = (int) trim($q);

        return $id > 0 ? $id : null;
    }

    /**
     * Add curated /mis/help deep links. This is NOT file scanning; it's a small keyword map.
     *
     * @param  list<array{title: string, items: list<array{label: string, meta: string, url: string}>}>  $categories
     * @return list<array{title: string, items: list<array{label: string, meta: string, url: string}>}>
     */
    private function appendHelpResults(string $q, array $categories): array
    {
        $needle = mb_strtolower(trim($q));
        if ($needle === '') {
            return $categories;
        }

        $items = [];
        $helpBase = route('mis.help.index');

        $map = [
            // General
            ['k' => ['help', 'docs', 'documentation', 'guide', 'how', 'workflow'], 'label' => 'MIS help and documentation', 'meta' => 'Overview, roles, workflows, troubleshooting', 'anchor' => 'overview'],
            ['k' => ['role', 'roles', 'admin', 'finance', 'va', 'access', 'login', 'sign in', 'signin', 'verification', 'verified', '403'], 'label' => 'Signing in and roles', 'meta' => 'Access rules and role limitations', 'anchor' => 'access'],
            ['k' => ['sidebar', 'menu', 'navigation', 'nav'], 'label' => 'Sidebar by role', 'meta' => 'What each role can see', 'anchor' => 'navigation'],

            // Workflows
            ['k' => ['lead', 'leads', 'convert', 'conversion', 'client'], 'label' => 'Workflow: Lead to client', 'meta' => 'Qualify, log activity, convert', 'anchor' => 'wf-lead-client'],
            ['k' => ['pipeline', 'stage', 'stages', 'kanban', 'board'], 'label' => 'Workflow: Pipeline board', 'meta' => 'Drag leads between stages', 'anchor' => 'wf-pipeline'],
            ['k' => ['quote', 'quotation', 'quotations', 'contract', 'contracts', 'invoice', 'invoices'], 'label' => 'Workflow: Quotation to contract to invoice', 'meta' => 'Quote, accept, generate contract, invoice', 'anchor' => 'wf-quote-invoice'],
            ['k' => ['ar', 'receivable', 'receivables', 'accounts receivable', 'overdue'], 'label' => 'Workflow: Accounts receivable', 'meta' => 'Open invoices and ageing buckets', 'anchor' => 'wf-ar'],
            ['k' => ['workspace', 'hub', 'tabs'], 'label' => 'Workflow: Client workspace', 'meta' => 'Client hub tabs and linked records', 'anchor' => 'wf-client-hub'],
            ['k' => ['booking', 'bookings', 'calendar', 'availability'], 'label' => 'Workflow: Bookings and leads', 'meta' => 'Bookings, lead linkage, availability', 'anchor' => 'wf-bookings'],
            ['k' => ['assistant', 'assistants', 'engagement', 'engagements', 'time', 'time log', 'timelog', 'va delivery'], 'label' => 'Workflow: VA delivery', 'meta' => 'Accounts, engagements, time logs, approvals', 'anchor' => 'wf-va'],
            ['k' => ['export', 'exports', 'csv', 'board pack', 'board packs'], 'label' => 'Workflow: Exports and board packs', 'meta' => 'Leads/invoices/expenses CSV exports', 'anchor' => 'wf-exports'],

            // Reference
            ['k' => ['module', 'modules', 'reference'], 'label' => 'Module reference', 'meta' => 'What each area does', 'anchor' => 'modules'],
            ['k' => ['status', 'statuses', 'fields', 'utm', 'loss reason'], 'label' => 'Statuses and key fields', 'meta' => 'Lead statuses, key fields, definitions', 'anchor' => 'data'],
            ['k' => ['integrations', 'stripe', 'zoho', 'google', 'oauth', 'webhook'], 'label' => 'Integrations', 'meta' => 'Stripe, Zoho Books, Google Calendar', 'anchor' => 'integrations'],
            ['k' => ['public', 'api', 'embed'], 'label' => 'Public site and APIs', 'meta' => 'Embed booking and public endpoints', 'anchor' => 'public'],
            ['k' => ['troubleshoot', 'error', 'issue', 'broken', 'cannot', 'cant'], 'label' => 'Troubleshooting', 'meta' => 'Common problems and fixes', 'anchor' => 'troubleshooting'],
        ];

        foreach ($map as $row) {
            foreach ($row['k'] as $kw) {
                if ($kw !== '' && str_contains($needle, $kw)) {
                    $items[] = [
                        'label' => $row['label'],
                        'meta' => $row['meta'],
                        'url' => $helpBase.'#'.$row['anchor'],
                    ];
                    break;
                }
            }
        }

        if (empty($items)) {
            return $categories;
        }

        array_unshift($categories, [
            'title' => 'Help',
            'items' => array_values(array_slice($items, 0, self::LIMIT)),
        ]);

        return $categories;
    }
}
