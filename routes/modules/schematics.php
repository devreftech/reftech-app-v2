<?php

use App\Http\Controllers\SchematicController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    Route::post('/schematics/{id}/duplicate', [SchematicController::class, 'duplicate'])->name('schematics.duplicate');
    Route::resource('schematics', SchematicController::class);
});
