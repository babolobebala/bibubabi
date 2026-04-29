<?php

use Illuminate\Support\Facades\Route;
use Modules\Know\Http\Controllers\KnowController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::prefix('app/know')->name('know.')->group(function () {
        Route::get('/index', [KnowController::class, 'index'])->name('index');
        Route::post('/admin', [KnowController::class, 'store'])->name('store');
    });
});
