<?php

use Illuminate\Support\Facades\Route;
use Modules\Pst\Http\Controllers\PstController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::prefix('app/pst')->name('pst.')->group(function () {
        Route::get('/', [PstController::class, 'index']);
        Route::get('/index', [PstController::class, 'index'])->name('index');
        Route::get('/admin', [PstController::class, 'admin'])->name('admin');
    });
});
