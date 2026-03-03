<?php

use App\Http\Controllers\GoogleDriveAuthController;
use App\Http\Controllers\GoogleDriveFileController;
use App\Http\Controllers\GoogleDrivePageController;
use App\Http\Controllers\NotificationManagerController;
use App\Http\Controllers\PasswordLoginController;
use App\Http\Controllers\SSOBPSController;
use App\Http\Controllers\TestController;
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

// Testing
Route::middleware('auth')->group(function () {
    Route::get('test', [TestController::class, 'TestPage'])
        ->name('test');

    Route::get('/google-drive', [GoogleDrivePageController::class, 'index'])
        ->name('google.drive.page');

    Route::get('/google-drive/files', [GoogleDriveFileController::class, 'index'])
        ->name('google.drive.files.index');

    Route::post('/google-drive/files', [GoogleDriveFileController::class, 'store'])
        ->name('google.drive.files.store');

    Route::patch('/google-drive/files/{fileId}', [GoogleDriveFileController::class, 'update'])
        ->name('google.drive.files.update');

    Route::delete('/google-drive/files/{fileId}', [GoogleDriveFileController::class, 'destroy'])
        ->name('google.drive.files.destroy');

    Route::middleware(['role:super_admin'])->group(function () {
        Route::get('test_auth', [TestController::class, 'TestAuthPage'])
            ->name('test.2');

        Route::get('/auth/google/drive', [GoogleDriveAuthController::class, 'redirect'])
            ->name('google.drive.redirect');

        Route::get('/auth/google/drive/callback', [GoogleDriveAuthController::class, 'callback'])
            ->name('google.drive.callback');
    });
});

// Notification
Route::post('/notifications/subscribe', [NotificationManagerController::class, 'subscribe']);
Route::post('/notifications/unsubscribe', [NotificationManagerController::class, 'unsubscribe']);
Route::post('/notifications/unbind', [NotificationManagerController::class, 'unbind']);
Route::get('/notifications/send', [NotificationManagerController::class, 'send'])->middleware(['auth']);
Route::get('/notifications/history', [NotificationManagerController::class, 'history'])->middleware(['auth']);
Route::post('/notifications/read', [NotificationManagerController::class, 'markAsRead'])->middleware(['auth']);
