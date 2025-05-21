<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PokedexController;
use App\Http\Controllers\PokemonController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Home/Home');
});

// Public route for the Pokedex
Route::get('/pokedex', [PokemonController::class, 'getAllPokemons'])->name('pokedex.public');

Route::get('/me', function () {
    return Inertia::render('Me/Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Routes pour Pokedex
    Route::get('/me/pokedex', [PokedexController::class, 'getUserPokemons'])->name('me.pokedex');
    Route::post('/pokedex/{id}/add-to-team', [PokedexController::class, 'addToTeam'])->name('pokedex.add-to-team');
    Route::post('/pokedex/{id}/remove-from-team', [PokedexController::class, 'removeFromTeam'])->name('pokedex.remove-from-team');

    // Routes pour Pokemon
    Route::get('/pokemon', action: [PokemonController::class, 'index'])->name('pokemon.index');
    Route::get('/pokemon/search', [PokemonController::class, 'search'])->name('pokemon.search');
    Route::get('/pokemon/{idOrName}', [PokemonController::class, 'show'])->name('pokemon.show');
});

require __DIR__.'/auth.php';
