<?php

use Illuminate\Support\Facades\Route;
use Modules\Notulensi\Http\Controllers\NotulensiController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('notulensis', NotulensiController::class)->names('notulensi');
});
