<?php

use Illuminate\Support\Facades\Route;
use Modules\Core\Http\Controllers\CoreController;

Route::middleware('auth')->group(function () {
    Route::get('home', [CoreController::class, 'index'])
        ->name('home');
});
