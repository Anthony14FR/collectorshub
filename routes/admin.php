<?php

use App\Http\Controllers\Admin\ClubController;
use App\Http\Controllers\Admin\CrudPromoCodeController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ExpeditionController;
use App\Http\Controllers\Admin\GameConfigurationController;
use App\Http\Controllers\Admin\ItemController;
use App\Http\Controllers\Admin\LogController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\Pokemons\PokemonController;
use App\Http\Controllers\Admin\SuccessController;
use App\Http\Controllers\Admin\Users\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'admin'])
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

        // PromoCodes
        Route::resource('promocodes', CrudPromoCodeController::class);

        // Items
        Route::resource('items', ItemController::class);

        // Sucess
        Route::resource('success', SuccessController::class);

        // Notifications
        Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');
        Route::get('notifications/create', [NotificationController::class, 'create'])->name('notifications.create');
        Route::post('notifications', [NotificationController::class, 'store'])->name('notifications.store');

        // Game Configuration
        Route::get('game-configuration', [GameConfigurationController::class, 'index'])->name('game-configuration.index');
        Route::post('game-configuration/update', [GameConfigurationController::class, 'update'])->name('game-configuration.update');
        Route::post('game-configuration/reset-category', [GameConfigurationController::class, 'resetCategory'])->name('game-configuration.reset-category');

        // Logs
        Route::get('logs', [LogController::class, 'index'])->name('logs.index');
        Route::get('logs/debug', [LogController::class, 'debug'])->name('logs.debug');

        // Clubs
        Route::get('clubs', [ClubController::class, 'index'])->name('clubs.index');
        Route::get('clubs/{club}', [ClubController::class, 'show'])->name('clubs.show');
        Route::delete('clubs/{club}', [ClubController::class, 'destroy'])->name('clubs.destroy');
    });
