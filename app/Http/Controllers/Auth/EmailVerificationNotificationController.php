<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EmailVerificationNotificationController extends Controller
{
    /**
     * Send a new email verification notification.
     */
    public function store(Request $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(route('dashboard', absolute: false));
        }

        try {
            $request->user()->sendEmailVerificationNotification();
        } catch (\Symfony\Component\Mailer\Exception\TransportExceptionInterface $e) {
            $message = $e->getMessage();
            if (str_contains($message, '554') || str_contains($message, '5.7.1')) {
                Log::warning('Envoi email de vérification bloqué pour spam', [
                    'user_id' => $request->user()->id,
                    'email' => $request->user()->email,
                    'exception' => $message
                ]);
                return back()->withErrors([
                    'email' => 'Votre adresse email a été détectée comme spam. Veuillez contacter le support.'
                ]);
            }
            throw $e;
        }

        return back()->with('status', 'verification-link-sent');
    }
}
