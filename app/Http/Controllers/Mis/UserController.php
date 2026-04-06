<?php

namespace App\Http\Controllers\Mis;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', User::class);

        $users = User::query()->orderBy('name')->paginate(25);

        return view('mis.users.index', compact('users'));
    }

    public function create(): View
    {
        $this->authorize('create', User::class);

        return view('mis.users.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', User::class);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'is_admin' => ['nullable', 'boolean'],
        ]);

        $roleRaw = $request->input('mis_role');
        $misRolePick = in_array($roleRaw, ['finance', 'va'], true) ? $roleRaw : null;

        $isAdmin = $request->boolean('is_admin');
        $misRole = $isAdmin ? null : $misRolePick;

        User::query()->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'is_admin' => $isAdmin,
            'mis_role' => $misRole,
            'email_verified_at' => now(),
        ]);

        return redirect()->route('mis.users.index')->with('status', 'User created.');
    }

    public function edit(User $user): View
    {
        $this->authorize('update', $user);

        return view('mis.users.edit', compact('user'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $this->authorize('update', $user);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'is_admin' => ['nullable', 'boolean'],
        ]);

        $roleRaw = $request->input('mis_role');
        $misRolePick = in_array($roleRaw, ['finance', 'va'], true) ? $roleRaw : null;

        $isAdmin = $request->boolean('is_admin');
        $misRole = $isAdmin ? null : $misRolePick;

        $payload = [
            'name' => $data['name'],
            'email' => $data['email'],
            'is_admin' => $isAdmin,
            'mis_role' => $misRole,
        ];
        if (! empty($data['password'])) {
            $payload['password'] = $data['password'];
        }
        if (! $request->boolean('is_admin')
            && $user->is_admin
            && User::query()->where('is_admin', true)->count() <= 1) {
            return back()->withErrors(['is_admin' => 'At least one admin account is required.'])->onlyInput('is_admin');
        }

        $user->update($payload);

        return redirect()->route('mis.users.index')->with('status', 'User updated.');
    }

    public function destroy(User $user): RedirectResponse
    {
        $this->authorize('delete', $user);

        $user->delete();

        return redirect()->route('mis.users.index')->with('status', 'User removed.');
    }
}
