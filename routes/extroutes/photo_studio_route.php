<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PhotoStudioController;

Route::prefix('dashboard')
    ->middleware('auth')
    ->name('dashboard.')
    ->group(function () {
        Route::prefix('user')->name('user.')->group(function () {
            Route::get('photo-studio', [PhotoStudioController::class, 'index'])->name('photo-studio.index');
            Route::post('photo-studio', [PhotoStudioController::class, 'store'])->name('photo-studio.store');
            Route::get('photo-studio/{photoStudio}/delete', [PhotoStudioController::class, 'delete'])->name('photo-studio.delete');
        });
    });
