<?php

use Illuminate\Support\Facades\Route;
use Modules\Tool\Http\Controllers\ToolController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::prefix('app/tools')->name('tools.')->group(function () {
        Route::get('/geotagging-gambar', [ToolController::class, 'geotagging'])->name('geotagging');
        Route::get('/generator-dokumen', [ToolController::class, 'documentGenerator'])->name('document-generator');
        Route::get('/generator-dokumen/template', [ToolController::class, 'documentGeneratorTemplate'])->name('document-generator.template');
        Route::get('/generator-dokumen-docx', [ToolController::class, 'documentGeneratorDocx'])->name('document-generator-docx');
    });
});
