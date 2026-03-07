<?php

use App\Http\Controllers\PasswordLoginController;
use App\Http\Controllers\SSOBPSController;
use Illuminate\Support\Facades\Route;


// SSO Route
Route::get('/', [SSOBPSController::class, 'ssoBPSRedirect'])
    ->name('sso.redirect');

Route::get('login_sso', [SSOBPSController::class, 'ssoBPSLogin'])
    ->name('sso.login');

Route::get('logout', [SSOBPSController::class, 'logout'])
    ->name('logout');

Route::middleware('guest')->group(function () {
    Route::post('login-password', [PasswordLoginController::class, 'store'])
        ->name('password.login');
});


