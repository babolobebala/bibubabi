<?php

use Illuminate\Support\Facades\Route;
use Modules\Core\Http\Controllers\CoreController;

Route::middleware('auth')->group(function () {
    Route::get('app', [CoreController::class, 'index'])
        ->name('home');
    Route::get('menu-cepat', [CoreController::class, 'quickMenu'])
        ->name('core.quick-menu');
    Route::get('notification', [CoreController::class, 'notification'])
        ->name('core.notification');
});
