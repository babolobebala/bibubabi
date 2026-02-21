<?php

use Illuminate\Support\Facades\Route;
use Modules\Tool\Http\Controllers\ToolController;

Route::get('cobacoba', [ToolController::class, 'cobacoba'])
    ->name('cobacoba');
