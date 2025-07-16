<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ExpeditionController;
use App\Http\Controllers\Admin\Pokemons\PokemonController;
use App\Http\Controllers\Admin\Users\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        // Dashboard
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        Route::post('/cache/clear', [DashboardController::class, 'clearCache'])->name('cache.clear');

        // Users
        Route::resource('users', UserController::class);

        // Pokemons
        Route::resource('pokemons', PokemonController::class);

        // Expeditions
        Route::resource('expeditions', ExpeditionController::class);
        Route::post('expeditions/{expedition}/toggle', [ExpeditionController::class, 'toggle'])->name('expeditions.toggle');
        Route::post('expeditions/{expedition}/duplicate', [ExpeditionController::class, 'duplicate'])->name('expeditions.duplicate');

    });
