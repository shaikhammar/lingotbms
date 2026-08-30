<?php

use App\Modules\Settings\Http\Controllers\TenantSettingController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('settings/business', [TenantSettingController::class, 'edit'])->name('settings.business.edit');

    Route::put('settings/business', [TenantSettingController::class, 'update'])->name('settings.business.update');
});
