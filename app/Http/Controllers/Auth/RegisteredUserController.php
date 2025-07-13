<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Pokedex;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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
            'username' => ['required', 'string', 'min:3', 'max:20', 'regex:/^[a-zA-Z0-9]+$/', 'unique:'.User::class],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'starter_pokemon' => ['required', 'integer', 'in:1,4,7'], // Bulbizarre, Salamèche, Carapuce
            'terms' => ['required', 'accepted'],
        ], [
            'username.regex' => 'Le nom d\'utilisateur ne peut contenir que des lettres et des chiffres.',
            'username.unique' => 'Ce nom d\'utilisateur est déjà pris.',
            'email.unique' => 'Cette adresse email est déjà utilisée.',
            'starter_pokemon.in' => 'Veuillez choisir un starter valide.',
            'terms.accepted' => 'Vous devez accepter les conditions d\'utilisation.',
        ]);

        $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'cash' => 1000, // Argent de départ
            'level' => 1,
            'exp' => 0,
        ]);

        // Ajouter le Pokémon starter au Pokédex de l'utilisateur
        Pokedex::create([
            'user_id' => $user->id,
            'pokemon_id' => $request->starter_pokemon,
            'captured_at' => now(),
            'is_shiny' => false,
            'level' => 5, // Niveau de départ du starter
        ]);

        event(new Registered($user));

        Auth::login($user);

        // Rediriger vers la page de vérification d'email
        return redirect(route('verification.notice'));
    }
}