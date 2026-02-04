<?php

use Illuminate\Support\Facades\Route;
use Modules\Umum\Http\Controllers\UmumController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('umums', UmumController::class)->names('umum');
});
