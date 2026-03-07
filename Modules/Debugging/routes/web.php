<?php

use Illuminate\Support\Facades\Route;
use Modules\Debugging\Http\Controllers\DebuggingController;
use Modules\Debugging\Http\Controllers\GoogleDriveAuthController;
use Modules\Debugging\Http\Controllers\GoogleDriveFileController;
use Modules\Debugging\Http\Controllers\GoogleDrivePageController;
use Modules\Debugging\Http\Controllers\NotificationManagerController;
use Modules\Debugging\Http\Controllers\TestController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::prefix('app/debugging')->name('debugging.')->group(function () {
        Route::get('/', [DebuggingController::class, 'index'])->name('index');
    });
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
