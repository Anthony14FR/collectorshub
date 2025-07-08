<?php

use App\Http\Controllers\OpeningController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->prefix('api')->group(function () {
    Route::post('/opening', [OpeningController::class, 'open'])->name('opening.open');
}); 