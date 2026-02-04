<?php

use Illuminate\Support\Facades\Route;
use Modules\Know\Http\Controllers\KnowController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('knows', KnowController::class)->names('know');
});
