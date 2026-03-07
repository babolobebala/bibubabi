<?php

use Illuminate\Support\Facades\Route;
use Modules\Admin\Http\Controllers\UserController;
use Modules\Admin\Http\Controllers\RoleController;

Route::middleware(['auth', 'verified'])->prefix('app/admin')->name('admin.')->group(function () {
    Route::get('/users', [UserController::class, 'index'])->name('users');
    Route::put('/users/{user}/password', [UserController::class, 'updatePassword'])->name('users.password');
    Route::put('/users/{user}/role', [UserController::class, 'updateRole'])->name('users.role');
    Route::get('/roles', [RoleController::class, 'index'])->name('roles');
});
