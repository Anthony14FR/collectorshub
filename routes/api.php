<?php

use App\Http\Controllers\OpeningController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->prefix('api')->group(function () {
    Route::post('/opening', [OpeningController::class, 'open'])->name('opening.open');
    Route::patch('/avatar', [ProfileController::class, 'updateAvatar'])->name('avatar.update');
});
