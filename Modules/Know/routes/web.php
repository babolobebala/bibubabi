<?php

use Illuminate\Support\Facades\Route;
use Modules\Know\Http\Controllers\KnowController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::prefix('app/know')->name('know.')->group(function () {
        Route::get('/index', [KnowController::class, 'index'])->name('index');
        Route::get('/admin', [KnowController::class, 'admin'])->name('admin');
        Route::post('/admin', [KnowController::class, 'store'])->name('store');
        Route::post('/admin/categories', [KnowController::class, 'storeCategory'])->name('admin.categories.store');
        Route::put('/admin/categories/{knowCategory}', [KnowController::class, 'updateCategory'])->name('admin.categories.update');
        Route::delete('/admin/categories/{knowCategory}', [KnowController::class, 'destroyCategory'])->name('admin.categories.destroy');
    });
});
