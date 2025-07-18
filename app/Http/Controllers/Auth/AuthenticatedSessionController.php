<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => session('status'),
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $user = Auth::user();

        if ($user->hasTotpEnabled() && !session('totp_verified')) {
            session(['totp_user_id' => $user->id]);
            Auth::logout();

            return redirect()->route('totp.verify');
        }

        $request->session()->regenerate();
        User::where('id', $user->id)->update(['last_login' => now()]);

        if (!$user->hasVerifiedEmail()) {
            return redirect()->route('verification.notice');
        }

        return redirect()->intended(route('me', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }

    public function showTotpVerification(): Response|RedirectResponse
    {
        if (!session('totp_user_id')) {
            return redirect()->route('login');
        }

        return Inertia::render('Auth/TotpVerification');
    }

    public function verifyTotp(Request $request): RedirectResponse
    {
        $request->validate([
            'code' => 'required|string|size:6'
        ]);

        $userId = session('totp_user_id');
        if (!$userId) {
            return redirect()->route('login');
        }

        $user = User::find($userId);
        if (!$user || !$user->verifyTotpCode($request->code)) {
            throw ValidationException::withMessages([
                'code' => ['Code TOTP incorrect']
            ]);
        }

        session()->forget('totp_user_id');
        session(['totp_verified' => true]);

        Auth::login($user);
        $request->session()->regenerate();
        $user->update(['last_login' => now()]);

        return redirect()->intended(route('me', absolute: false));
    }

    public function setupTotp(): Response
    {
        $user = Auth::user();

        return Inertia::render('Profile/TotpSetup', [
            'qrUrl' => $user->getTotpQrUrl(),
            'secret' => $user->totp_secret,
            'enabled' => $user->totp_enabled
        ]);
    }

    public function enableTotp(Request $request): RedirectResponse
    {
        $request->validate([
            'code' => 'required|string|size:6'
        ]);

        $user = Auth::user();

        if (!$user->enableTotp($request->code)) {
            throw ValidationException::withMessages([
                'code' => ['Code TOTP incorrect']
            ]);
        }

        return redirect()->route('profile.edit')->with('success', 'Authentification à deux facteurs activée avec succès !');
    }

    public function disableTotp(): RedirectResponse
    {
        $user = Auth::user();
        $user->disableTotp();

        return redirect()->route('profile.edit')->with('success', 'Authentification à deux facteurs désactivée avec succès !');
    }
}
