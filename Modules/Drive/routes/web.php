<?php

use Illuminate\Support\Facades\Route;
use Modules\Drive\Http\Controllers\DriveController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::prefix('app/drive')->name('drive.')->group(function () {
        Route::get('/index', [DriveController::class, 'index'])->name('index');
        Route::get('/admin', [DriveController::class, 'admin'])->name('admin');
        Route::post('/admin', [DriveController::class, 'store'])->name('store');
        Route::put('/admin/{drive}', [DriveController::class, 'update'])->name('update');
        Route::delete('/admin/{drive}', [DriveController::class, 'destroy'])->name('destroy');
    });
});
