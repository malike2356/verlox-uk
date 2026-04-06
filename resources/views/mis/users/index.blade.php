@extends('layouts.mis')

@section('title', 'Users')
@section('heading', 'Users')

@section('content')
    <div class="flex justify-end mb-4">
        <a href="{{ route('mis.users.create') }}" class="rounded-lg bg-verlox-accent px-3 py-1.5 text-sm font-semibold text-on-verlox-accent">Add user</a>
    </div>
    <div class="overflow-x-auto rounded-2xl border border-gray-200 dark:border-slate-800">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-100 dark:bg-slate-900 text-left text-xs uppercase text-gray-500 dark:text-slate-500">
            <tr>
                <th class="px-3 py-2">Name</th>
                <th class="px-3 py-2">Email</th>
                <th class="px-3 py-2">Role</th>
                <th class="px-3 py-2"></th>
            </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-slate-800">
            @foreach ($users as $user)
                <tr class="hover:bg-gray-50 dark:hover:bg-slate-900/60">
                    <td class="px-3 py-2 text-gray-900 dark:text-white">{{ $user->name }}</td>
                    <td class="px-3 py-2 text-gray-600 dark:text-slate-400">{{ $user->email }}</td>
                    <td class="px-3 py-2 text-gray-500 dark:text-slate-500">{{ $user->is_admin ? 'Admin' : 'User' }}</td>
                    <td class="px-3 py-2 text-right space-x-2">
                        <a href="{{ route('mis.users.edit', $user) }}" class="text-verlox-accent text-xs">Edit</a>
                        @can('delete', $user)
                            <form method="post" action="{{ route('mis.users.destroy', $user) }}" class="inline" onsubmit="return confirm('Remove this user?');">
                                @csrf @method('delete')
                                <button type="submit" class="text-red-600 dark:text-red-400 text-xs">Remove</button>
                            </form>
                        @endcan
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $users->links() }}</div>
@endsection
