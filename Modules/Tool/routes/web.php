<?php

use Illuminate\Support\Facades\Route;
use Modules\Tool\Http\Controllers\ToolController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::prefix('app/tools')->name('tools.')->group(function () {
        Route::get('/', [ToolController::class, 'index'])->name('index');
        Route::get('/geotagging-gambar', [ToolController::class, 'geotagging'])->name('geotagging');
    });
});
