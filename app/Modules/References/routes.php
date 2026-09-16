<?php

use App\Modules\References\Http\Controllers\ServiceController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('settings/services', [ServiceController::class, 'index'])->name('settings.services.index');
    Route::get('settings/services/create', [ServiceController::class, 'create'])->name('settings.services.create');
    Route::put('settings/services', [ServiceController::class, 'store'])->name('settings.services.store');
    Route::get('settings/services/{service}/edit', [ServiceController::class, 'edit'])->name('settings.services.edit');
    Route::patch('settings/services/{service}', [ServiceController::class, 'update'])->name('settings.services.update');
    Route::put('settings/services/{service}/archive', [ServiceController::class, 'archive'])->name('settings.services.archive');
    Route::put('settings/services/{service}/restore', [ServiceController::class, 'restore'])->name('settings.services.restore');
});
