<?php

use App\Http\Controllers\Admin\Users\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        // Routes pour CRUD Users
        Route::resource('users', UserController::class);
}); 