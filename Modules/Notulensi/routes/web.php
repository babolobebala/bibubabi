<?php

use Illuminate\Support\Facades\Route;
use Modules\Notulensi\Http\Controllers\NotulensiController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::prefix('app/notulensi')->name('notulensi.')->group(function () {
        Route::get('/', [NotulensiController::class, 'index'])->name('index');
    });
});
