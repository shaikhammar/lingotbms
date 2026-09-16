<?php

use App\Modules\Notes\Http\Controllers\NotesController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('notes', [NotesController::class, 'index'])->name('notes.index');
});
