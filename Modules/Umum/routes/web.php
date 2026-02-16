<?php

use Illuminate\Support\Facades\Route;
use Modules\Umum\Http\Controllers\UmumController;


Route::get('welcome', [UmumController::class, 'welcome_page'])
    ->name('login');
