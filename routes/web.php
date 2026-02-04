<?php

use App\Http\Controllers\MainFeatureController;
use App\Http\Controllers\SSOBPSController;

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

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

// Login Route
// Route::middleware('guest')->group(function () {
//     Route::get('login', function () {
//         return Inertia::render('LoginPage');
//     })
//         ->name('login');
// });

