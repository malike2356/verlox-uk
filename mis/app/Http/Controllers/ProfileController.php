<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        $user = $request->user();
        $sessions = collect();

        if (config('session.driver') === 'database') {
            $sessions = DB::table('sessions')
                ->where('user_id', $user->id)
                ->orderByDesc('last_activity')
                ->get(['id', 'ip_address', 'user_agent', 'last_activity']);
        }

        $view = $user->canAccessMis() ? 'profile.edit-mis' : 'profile.edit';

        return view($view, [
            'user' => $user,
            'sessions' => $sessions,
        ]);
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();

        $user->fill($request->safe()->only(['name', 'email', 'phone', 'job_title']));

        if ($request->hasFile('avatar')) {
            $user->deleteStoredAvatar();
            $user->avatar_path = $request->file('avatar')->store('profile-photos/'.$user->id, 'public');
        } elseif ($request->boolean('remove_avatar')) {
            $user->deleteStoredAvatar();
            $user->avatar_path = null;
        }

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    public function logoutOtherSessions(Request $request): RedirectResponse
    {
        if (config('session.driver') !== 'database') {
            return Redirect::route('profile.edit')->with('error', __('Session storage is not database-backed; other devices cannot be listed or signed out from here.'));
        }

        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $currentId = $request->session()->getId();

        DB::table('sessions')
            ->where('user_id', $request->user()->id)
            ->where('id', '!=', $currentId)
            ->delete();

        return Redirect::route('profile.edit')->with('status', __('Signed out of other browsers and devices.'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
