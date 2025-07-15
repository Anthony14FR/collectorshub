<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ExpeditionController;
use App\Http\Controllers\Admin\NotificationController;
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

        // Expeditions
        Route::resource('expeditions', ExpeditionController::class);
        Route::post('expeditions/{expedition}/toggle', [ExpeditionController::class, 'toggle'])->name('expeditions.toggle');
        Route::post('expeditions/{expedition}/duplicate', [ExpeditionController::class, 'duplicate'])->name('expeditions.duplicate');

        // Notifications
        Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');
        Route::get('notifications/create', [NotificationController::class, 'create'])->name('notifications.create');
        Route::post('notifications', [NotificationController::class, 'store'])->name('notifications.store');

    });
