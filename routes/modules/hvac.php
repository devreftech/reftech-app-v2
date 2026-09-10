<?php

use App\Http\Controllers\Hvac\HvacMasterCatalogController;
use App\Http\Controllers\Hvac\HvacProjectController;
use App\Http\Controllers\Hvac\HvacRoomController;
use Illuminate\Support\Facades\Route;
Route::middleware(['auth'])->prefix('hvac')->name('hvac.')->group(function () {
    // 1. Quick Calculator (Standalone Sales Estimator)
    Route::get('/quick-calculator', [HvacRoomController::class, 'standaloneQuickCalculator'])->name('quick-calculator');
    Route::post('/quick-calculator/save', [HvacRoomController::class, 'saveStandaloneQuick'])->name('quick-calculator.save');

    // 2. Projects
    Route::get('/projects', [HvacProjectController::class, 'index'])->name('project.index');
    Route::get('/projects/create', [HvacProjectController::class, 'create'])->name('project.create');
    Route::post('/projects', [HvacProjectController::class, 'store'])->name('project.store');
    Route::get('/projects/{id}', [HvacProjectController::class, 'show'])->name('project.show');
    Route::get('/projects/{id}/edit', [HvacProjectController::class, 'edit'])->name('project.edit');
    Route::put('/projects/{id}', [HvacProjectController::class, 'update'])->name('project.update');
    Route::delete('/projects/{id}', [HvacProjectController::class, 'destroy'])->name('project.destroy');

    // 2. Rooms
    Route::get('/projects/{projectId}/rooms/create', [HvacRoomController::class, 'create'])->name('room.create');
    Route::post('/projects/{projectId}/rooms', [HvacRoomController::class, 'store'])->name('room.store');
    Route::get('/rooms/{id}/edit', [HvacRoomController::class, 'edit'])->name('room.edit');
    Route::put('/rooms/{id}', [HvacRoomController::class, 'update'])->name('room.update');
    Route::get('/rooms/{id}/result', [HvacRoomController::class, 'result'])->name('room.result');
    Route::get('/rooms/{id}/print', [HvacRoomController::class, 'printReport'])->name('room.print');
    Route::delete('/rooms/{id}', [HvacRoomController::class, 'destroy'])->name('room.destroy');

    // 3. Master Data & Catalog
    Route::get('/master-catalog', [HvacMasterCatalogController::class, 'index'])->name('master.index');
    Route::post('/master-catalog/ac', [HvacMasterCatalogController::class, 'storeAc'])->name('master.ac.store');
    Route::post('/master-catalog/material', [HvacMasterCatalogController::class, 'storeMaterial'])->name('master.material.store');
    Route::post('/master-catalog/glass', [HvacMasterCatalogController::class, 'storeGlass'])->name('master.glass.store');
});
