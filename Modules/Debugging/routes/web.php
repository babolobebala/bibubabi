<?php

use Illuminate\Support\Facades\Route;
use Modules\Debugging\Http\Controllers\DebuggingController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::prefix('app/debugging')->name('debugging.')->group(function () {
        Route::get('/', [DebuggingController::class, 'index'])->name('index');
    });
});
