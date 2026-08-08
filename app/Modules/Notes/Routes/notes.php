<?php

use App\Modules\Notes\Http\Controllers\NotesController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth'])->group(function () {
    Route::get('notes', [NotesController::class, 'index'])->name('notes.index');
});
