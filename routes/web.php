<?php

use App\Http\Controllers\PasswordLoginController;
use App\Http\Controllers\SSOBPSController;
use Illuminate\Support\Facades\Route;

// DEBUG. ILEGAL ROUTE (TOLONG DIKOMEN KALAU DI PRODUCTION)
Route::get('bypass', [SSOBPSController::class, 'bypassLogin'])
    ->name('bypass');

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


