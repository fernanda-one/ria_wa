<?php

use App\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\Route;

Route::prefix('ilsya/files')->withoutMiddleware(VerifyCsrfToken::class)->middleware('auth')->group(function () {
    Route::get('/', [\App\Http\Controllers\FileManager::class, 'index'])->name('ilsya.files.index');
    Route::post('/upload', [\App\Http\Controllers\FileManager::class, 'upload'])->name('ilsya.files.upload')->middleware('isdemo');
    Route::post('/delete', [\App\Http\Controllers\FileManager::class, 'delete'])->name('ilsya.files.delete')->middleware('isdemo');
});
