<?php

use App\Http\Controllers\ExpeditionController;
use App\Http\Controllers\InfernalTowerController;
use App\Http\Controllers\OpeningController;
use App\Http\Controllers\PokemonUpgradeController;
use App\Http\Controllers\FriendController;
use Illuminate\Support\Facades\Route;


Route::middleware(['auth', 'verified'])->prefix('api')->group(function () {
    Route::post('/opening', [OpeningController::class, 'open'])->name('opening.open');

    Route::get('/upgradable-pokemons', [PokemonUpgradeController::class, 'getUpgradablePokemons'])->name('upgradable-pokemons');
    Route::get('/requirements/{pokedexId}', [PokemonUpgradeController::class, 'getUpgradeRequirements'])->name('requirements');
    Route::post('/slot-pokemons/{pokedexId}', [PokemonUpgradeController::class, 'getAvailablePokemonsForSlot'])->name('slot-pokemons');

    Route::post('/expeditions/{expedition}/start', [ExpeditionController::class, 'start'])->name('api.expeditions.start');
    Route::post('/expeditions/claim', [ExpeditionController::class, 'claim'])->name('api.expeditions.claim');


    Route::post('/tower/attempt', [InfernalTowerController::class, 'attempt'])->name('api.tower.attempt');

    Route::get('/friends/suggestions', [FriendController::class, 'suggestions']);
    Route::get('/friends/pending-requests', [FriendController::class, 'getPendingRequests']);
    Route::post('/friends/refresh', [FriendController::class, 'refresh']);
});
