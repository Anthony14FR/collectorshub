<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/ForgotPassword', [
            'status' => session('status'),
        ]);
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'login' => 'required|string',
        ]);

        $login = $request->input('login');

        $user = User::where('email', $login)
                   ->orWhere('username', $login)
                   ->first();

        if (!$user) {
            throw ValidationException::withMessages([
                'login' => ['Aucun compte trouvé avec ces informations.'],
            ]);
        }

        try {
            $status = Password::sendResetLink([
                'email' => $user->email
            ]);
        } catch (\Symfony\Component\Mailer\Exception\TransportExceptionInterface $e) {
            $message = $e->getMessage();
            if (str_contains($message, '554') || str_contains($message, '5.7.1')) {
                Log::warning('Envoi email de reset password bloqué pour spam', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'exception' => $message
                ]);
                return back()->withErrors([
                    'login' => 'Votre adresse email a été détectée comme spam. Veuillez contacter le support.'
                ]);
            }
            throw $e;
        }

        if ($status == Password::RESET_LINK_SENT) {
            return back()->with('status', __($status));
        }

        throw ValidationException::withMessages([
            'login' => [trans($status)],
        ]);
    }
}
