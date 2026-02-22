<?php

use Illuminate\Support\Facades\Route;
use Modules\Core\Http\Controllers\CoreController;

Route::middleware('auth')->group(function () {
    Route::get('app', [CoreController::class, 'index'])
        ->name('home');
});
