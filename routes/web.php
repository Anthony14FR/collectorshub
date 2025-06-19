<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PokedexController;
use App\Http\Controllers\PokemonController;
use App\Http\Controllers\PromoCodeController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    // Routes pour Profil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Routes pour Pokedex
    Route::get('/pokedex/user-pokemons', [PokedexController::class, 'getUserPokemons'])->name('pokedex.user-pokemons');
    Route::post('/pokedex/{id}/add-to-team', [PokedexController::class, 'addToTeam'])->name('pokedex.add-to-team');
    Route::post('/pokedex/{id}/remove-from-team', [PokedexController::class, 'removeFromTeam'])->name('pokedex.remove-from-team');

    // Routes pour Pokemon
    Route::get('/pokemon', action: [PokemonController::class, 'index'])->name('pokemon.index');
    Route::get('/pokemon/search', [PokemonController::class, 'search'])->name('pokemon.search');
    Route::get('/pokemon/{idOrName}', [PokemonController::class, 'show'])->name('pokemon.show');

    // Routes pour PromoCode
    Route::get('/promocodes', [PromoCodeController::class, 'index'])->name('promocodes.index');
    Route::get('/promocodes/create', [PromoCodeController::class, 'create'])->name('promocodes.create');
    Route::post('/promocodes', [PromoCodeController::class, 'store'])->name('promocodes.store');
    Route::post('/promocodes/use', [PromoCodeController::class, 'useCode'])->name('promocodes.use');
    Route::delete('/promocodes/{id}', [PromoCodeController::class, 'destroy'])->name('promocodes.destroy');
});

require __DIR__.'/admin.php';
require __DIR__.'/auth.php';
