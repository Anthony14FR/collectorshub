<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\User;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): Response
    {
        return Inertia::render('Profile/Edit', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => session('status'),
            'userRoles' => User::ROLES,
            'userStatuses' => User::STATUSES,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit');
    }

    public function updateAvatar(Request $request): RedirectResponse
    {
        $user = $request->user();
        $unlocked = $user->unlocked_avatars;
        if (is_string($unlocked)) {
            $unlocked = json_decode($unlocked, true);
        }
        $unlocked = $unlocked ?? [];

        $request->validate([
            'avatar' => [
                'required',
                function ($attribute, $value, $fail) use ($unlocked) {
                    if (!in_array($value, $unlocked)) {
                        $fail('Avatar non débloqué.');
                    }
                }
            ]
        ]);

        $user->avatar = $request->avatar;
        $user->save();

        return back()->with('status', 'Avatar mis à jour !');
    }

    public function updateBackground(Request $request): RedirectResponse
    {
        $user = $request->user();
        $unlocked = $user->unlocked_backgrounds;
        if (is_string($unlocked)) {
            $unlocked = json_decode($unlocked, true);
        }
        $unlocked = $unlocked ?? [];

        $request->validate([
            'background' => [
                'required',
                function ($attribute, $value, $fail) use ($unlocked) {
                    if ($value === '/images/section-me-background.jpg') {
                        return;
                    }
                    if (!in_array($value, $unlocked)) {
                        $fail('Background non débloqué.');
                    }
                }
            ]
        ]);

        $user->background = $request->background;
        $user->save();

        return back()->with('status', 'Background mis à jour !');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
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
