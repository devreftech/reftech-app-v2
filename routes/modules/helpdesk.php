<?php

use App\Http\Controllers\HelpdeskController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\LibraryController;
use App\Http\Controllers\NotulenController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    // Helpdesk
    Route::get('/helpdesk', [HelpdeskController::class, 'index'])->name('helpdesk.index');
    Route::post('/helpdesk', [HelpdeskController::class, 'store'])->name('helpdesk.store');
    Route::patch('/helpdesk/status/{id}', [HelpdeskController::class, 'updateStatus'])->name('helpdesk.update-status');

    // Activity Log & System Audit
    Route::get('/activity-log', [ActivityLogController::class, 'index'])->name('activity-log.index');

    // Library
    Route::post('/library/store/marktool', [LibraryController::class, 'store_marktool'])->name('store_marktool.library');
    Route::post('/library/store/brosur', [LibraryController::class, 'store_brosur'])->name('store_brosur.library');
    Route::post('/library/store/partlist', [LibraryController::class, 'store_partlist'])->name('store_partlist.library');
    Route::post('/library/store/manbook', [LibraryController::class, 'store_manbook'])->name('store_manbook.library');

    // Notulen
    Route::resource('/notulen', NotulenController::class);
});
