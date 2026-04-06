@php
    /** @var \App\Models\VaClientAccount $va_client_account */
@endphp
<div class="space-y-3 max-w-2xl">
    <div>
        <label class="text-xs text-gray-500 dark:text-slate-500">Link to MIS client (optional)</label>
        <select name="mis_client_id" class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-950 px-3 py-2 text-gray-900 dark:text-white text-sm">
            <option value="">None</option>
            @foreach ($misClients as $c)
                <option value="{{ $c->id }}" @selected(old('mis_client_id', $va_client_account->mis_client_id) == $c->id)>
                    {{ $c->contact_name }} @if($c->company_name) ({{ $c->company_name }}) @endif - {{ $c->email }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="grid sm:grid-cols-2 gap-3">
        <div>
            <label class="text-xs text-gray-500 dark:text-slate-500">Company name</label>
            <input name="company_name" value="{{ old('company_name', $va_client_account->company_name) }}" required class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-950 px-3 py-2 text-gray-900 dark:text-white">
        </div>
        <div>
            <label class="text-xs text-gray-500 dark:text-slate-500">Contact name</label>
            <input name="contact_name" value="{{ old('contact_name', $va_client_account->contact_name) }}" required class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-950 px-3 py-2 text-gray-900 dark:text-white">
        </div>
    </div>
    <div class="grid sm:grid-cols-2 gap-3">
        <div>
            <label class="text-xs text-gray-500 dark:text-slate-500">Email</label>
            <input type="email" name="email" value="{{ old('email', $va_client_account->email) }}" required class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-950 px-3 py-2 text-gray-900 dark:text-white">
        </div>
        <div>
            <label class="text-xs text-gray-500 dark:text-slate-500">Phone</label>
            <input name="phone" value="{{ old('phone', $va_client_account->phone) }}" class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-950 px-3 py-2 text-gray-900 dark:text-white">
        </div>
    </div>
    <div class="grid sm:grid-cols-2 gap-3">
        <div>
            <label class="text-xs text-gray-500 dark:text-slate-500">Tier</label>
            <select name="tier" class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-950 px-3 py-2 text-gray-900 dark:text-white">
                @foreach (\App\Models\VaClientAccount::$tiers as $t)
                    <option value="{{ $t }}" @selected(old('tier', $va_client_account->tier ?? 'starter') === $t)>{{ $t }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="text-xs text-gray-500 dark:text-slate-500">Status</label>
            <select name="status" class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-950 px-3 py-2 text-gray-900 dark:text-white">
                @foreach (\App\Models\VaClientAccount::$statuses as $s)
                    <option value="{{ $s }}" @selected(old('status', $va_client_account->status ?? 'onboarding') === $s)>{{ $s }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="grid sm:grid-cols-3 gap-3">
        <div>
            <label class="text-xs text-gray-500 dark:text-slate-500">Monthly rate (£)</label>
            <input type="number" step="0.01" min="0" name="monthly_rate_gbp" value="{{ old('monthly_rate_gbp', $va_client_account->monthly_rate_gbp ?? 0) }}" required class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-950 px-3 py-2 text-gray-900 dark:text-white">
        </div>
        <div>
            <label class="text-xs text-gray-500 dark:text-slate-500">Hours included / mo</label>
            <input type="number" min="0" max="1000" name="hours_included" value="{{ old('hours_included', $va_client_account->hours_included ?? 0) }}" required class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-950 px-3 py-2 text-gray-900 dark:text-white">
        </div>
        <div>
            <label class="text-xs text-gray-500 dark:text-slate-500">Overage £/hr</label>
            <input type="number" step="0.01" min="0" name="overage_rate_gbp" value="{{ old('overage_rate_gbp', $va_client_account->overage_rate_gbp ?? 0) }}" required class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-950 px-3 py-2 text-gray-900 dark:text-white">
        </div>
    </div>
    <div class="grid sm:grid-cols-3 gap-3">
        <div>
            <label class="text-xs text-gray-500 dark:text-slate-500">Contract start</label>
            <input type="date" name="contract_start" value="{{ old('contract_start', optional($va_client_account->contract_start)->format('Y-m-d')) }}" class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-950 px-3 py-2 text-gray-900 dark:text-white">
        </div>
        <div>
            <label class="text-xs text-gray-500 dark:text-slate-500">Contract end</label>
            <input type="date" name="contract_end" value="{{ old('contract_end', optional($va_client_account->contract_end)->format('Y-m-d')) }}" class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-950 px-3 py-2 text-gray-900 dark:text-white">
        </div>
        <div>
            <label class="text-xs text-gray-500 dark:text-slate-500">Minimum term end</label>
            <input type="date" name="minimum_term_end" value="{{ old('minimum_term_end', optional($va_client_account->minimum_term_end)->format('Y-m-d')) }}" class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-950 px-3 py-2 text-gray-900 dark:text-white">
        </div>
    </div>
    <div class="grid sm:grid-cols-2 gap-3">
        <div>
            <label class="text-xs text-gray-500 dark:text-slate-500">Stripe customer id</label>
            <input name="stripe_customer_id" value="{{ old('stripe_customer_id', $va_client_account->stripe_customer_id) }}" class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-950 px-3 py-2 text-gray-900 dark:text-white font-mono text-xs">
        </div>
        <div>
            <label class="text-xs text-gray-500 dark:text-slate-500">Account manager</label>
            <input name="account_manager" value="{{ old('account_manager', $va_client_account->account_manager) }}" class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-950 px-3 py-2 text-gray-900 dark:text-white">
        </div>
    </div>
    <div>
        <label class="text-xs text-gray-500 dark:text-slate-500">Notes</label>
        <textarea name="notes" rows="3" class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-950 px-3 py-2 text-gray-900 dark:text-white text-sm">{{ old('notes', $va_client_account->notes) }}</textarea>
    </div>
</div>
