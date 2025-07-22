<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'username' => 'required|string|max:255|unique:'.User::class,
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'avatar' => 'required|in:/images/trainer/1.png,/images/trainer/2.png',
        ]);

        $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'last_login' => now(),
            'avatar' => $request->avatar,
            'unlocked_avatars' => json_encode(["/images/trainer/1.png", "/images/trainer/2.png"]),
            'background' => '/images/section-me-background.jpg',
            'unlocked_backgrounds' => json_encode(['/images/section-me-background.jpg']),
        ]);

        $user = $user->fresh();

        $user->assignRole('user');

        try {
            $user->sendEmailVerificationNotification();
        } catch (\Symfony\Component\Mailer\Exception\TransportExceptionInterface $e) {
            $message = $e->getMessage();
            if (str_contains($message, '554') || str_contains($message, '5.7.1')) {
                Log::warning('Envoi email de vérification bloqué pour spam', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'exception' => $message
                ]);
                return redirect()->back()->withErrors([
                    'email' => 'Votre adresse email a été détectée comme spam. Veuillez contacter le support.'
                ]);
            }
            throw $e;
        }

        Auth::login($user);

        return redirect(route('verification.notice', absolute: false));
    }
}
