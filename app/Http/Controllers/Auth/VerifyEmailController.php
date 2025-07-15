<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     */
    public function __invoke(Request $request): RedirectResponse
    {
        $userId = $request->route('id');
        $hash = $request->route('hash');

        if (!$request->hasValidSignature()) {
            abort(403, 'Lien de vérification invalide ou expiré.');
        }

        $user = User::find($userId);

        if (!$user) {
            abort(404, 'Utilisateur introuvable.');
        }

        if (sha1($user->getEmailForVerification()) !== $hash) {
            abort(403, 'Lien de vérification invalide.');
        }

        if ($user->hasVerifiedEmail()) {
            return redirect()->route('login')->with('status', 'Votre email est déjà vérifié. Vous pouvez vous connecter.');
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        Auth::login($user);

        return redirect(route('me', absolute: false).'?verified=1');
    }
}
