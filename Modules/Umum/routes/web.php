<?php

use Illuminate\Support\Facades\Route;
use Modules\Umum\Http\Controllers\UmumController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('umums', UmumController::class)->names('umum');
});
