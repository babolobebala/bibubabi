<?php

use Illuminate\Support\Facades\Route;
use Modules\Know\Http\Controllers\KnowController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('knows', KnowController::class)->names('know');
});
