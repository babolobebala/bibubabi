<?php

use Illuminate\Support\Facades\Route;
use Modules\Drive\Http\Controllers\DriveController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::prefix('app/drive')->name('drive.')->group(function () {
        Route::get('/index', [DriveController::class, 'index'])->name('index');
        Route::get('/admin', [DriveController::class, 'admin'])->name('admin');
    });
});
