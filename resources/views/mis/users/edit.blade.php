@extends('layouts.mis')

@section('title', 'Edit user')
@section('heading', 'Edit user')

@section('content')
    <form method="post" action="{{ route('mis.users.update', $user) }}" class="max-w-md space-y-4 text-sm">
        @csrf @method('patch')
        <div>
            <label class="text-xs text-gray-500 dark:text-slate-300">Name</label>
            <input name="name" required value="{{ old('name', $user->name) }}" class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-600 bg-white dark:bg-slate-800 px-3 py-2 text-gray-900 dark:text-slate-100 placeholder:text-gray-500 dark:placeholder:text-slate-400">
        </div>
        <div>
            <label class="text-xs text-gray-500 dark:text-slate-300">Email</label>
            <input type="email" name="email" required value="{{ old('email', $user->email) }}" class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-600 bg-white dark:bg-slate-800 px-3 py-2 text-gray-900 dark:text-slate-100 placeholder:text-gray-500 dark:placeholder:text-slate-400">
        </div>
        <div>
            <label class="text-xs text-gray-500 dark:text-slate-300">New password (leave blank to keep)</label>
            <x-password-input name="password" autocomplete="new-password" rounded="rounded-lg" class="mt-1 block w-full" />
        </div>
        <div>
            <label class="text-xs text-gray-500 dark:text-slate-300">Confirm new password</label>
            <x-password-input name="password_confirmation" autocomplete="new-password" rounded="rounded-lg" class="mt-1 block w-full" />
        </div>
        <label class="flex items-center gap-2 text-gray-700 dark:text-slate-200">
            <input type="checkbox" name="is_admin" value="1" id="user-is-admin" @checked(old('is_admin', $user->is_admin)) class="size-4 rounded border-gray-300 accent-[#C9A84C] dark:border-slate-500 dark:bg-slate-800">
            Admin (full MIS)
        </label>
        <div id="mis-role-wrap">
            <label class="text-xs text-gray-500 dark:text-slate-300">MIS role (non-admin)</label>
            <select name="mis_role" class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-600 bg-white dark:bg-slate-800 px-3 py-2 text-gray-900 dark:text-slate-100">
                <option value="" @selected(old('mis_role', $user->mis_role) === null || old('mis_role', $user->mis_role) === '')>None (no MIS)</option>
                <option value="finance" @selected(old('mis_role', $user->mis_role) === 'finance')>Finance</option>
                <option value="va" @selected(old('mis_role', $user->mis_role) === 'va')>VA only</option>
            </select>
        </div>
        <button type="submit" class="rounded-xl bg-verlox-accent px-5 py-2 text-sm font-semibold text-on-verlox-accent">Save</button>
        <a href="{{ route('mis.users.index') }}" class="block text-xs text-verlox-accent">Back</a>
    </form>
@endsection
