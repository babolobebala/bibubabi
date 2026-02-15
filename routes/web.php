<?php

use App\Http\Controllers\NotificationManagerController;
use App\Http\Controllers\SSOBPSController;
use App\Http\Controllers\TestController;
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
Route::middleware('guest')->group(function () {
    Route::get('login', function () {
        return Inertia::render('LoginPage');
    })
        ->name('login');
});

// Testing
Route::middleware('auth')->group(function () {
    Route::get('test', [TestController::class, 'TestPage'])
        ->name('main.home');

    Route::middleware(['role:super_admin'])->group(function () {
        Route::get('test_auth', [TestController::class, 'TestAuthPage'])
            ->name('test.2');
    });
});

// Notification
Route::post('/notifications/subscribe', [NotificationManagerController::class, 'subscribe']);
Route::post('/notifications/unsubscribe', [NotificationManagerController::class, 'unsubscribe']);
Route::get('/notifications/send', [NotificationManagerController::class, 'send'])->middleware(['auth', 'role:super_admin']);
