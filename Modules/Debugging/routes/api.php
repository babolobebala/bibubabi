<?php

use Illuminate\Support\Facades\Route;
use Modules\Debugging\Http\Controllers\DebuggingController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('debuggings', DebuggingController::class)->names('debugging');
});
