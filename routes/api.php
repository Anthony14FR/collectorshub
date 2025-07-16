<?php

use App\Http\Controllers\ExpeditionController;
use App\Http\Controllers\OpeningController;
use App\Http\Controllers\PokemonUpgradeController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->prefix('api')->group(function () {
    Route::post('/opening', [OpeningController::class, 'open'])->name('opening.open');

    Route::get('/upgradable-pokemons', [PokemonUpgradeController::class, 'getUpgradablePokemons'])->name('upgradable-pokemons');
    Route::get('/requirements/{pokedexId}', [PokemonUpgradeController::class, 'getUpgradeRequirements'])->name('requirements');
    Route::post('/slot-pokemons/{pokedexId}', [PokemonUpgradeController::class, 'getAvailablePokemonsForSlot'])->name('slot-pokemons');

    Route::post('/expeditions/{expedition}/start', [ExpeditionController::class, 'start'])->name('api.expeditions.start');
    Route::post('/expeditions/claim', [ExpeditionController::class, 'claim'])->name('api.expeditions.claim');

    Route::get('/friends/suggestions', [\App\Http\Controllers\FriendController::class, 'suggestions']);
    Route::get('/friends/pending-requests', [\App\Http\Controllers\FriendController::class, 'getPendingRequests']);
    Route::post('/friends/refresh', [\App\Http\Controllers\FriendController::class, 'refresh']);
});
