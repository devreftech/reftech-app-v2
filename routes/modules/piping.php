<?php

use App\Http\Controllers\PipingMaterialController;
use App\Http\Controllers\PipingRabController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    // 1. Master Material Piping & Vendor Pricelists
    Route::get('/piping-materials/search-api', [PipingMaterialController::class, 'search'])->name('piping-materials.search');
    Route::post('/piping-materials/{id}/vendor-prices', [PipingMaterialController::class, 'storeVendorPrice'])->name('piping-materials.vendor-prices.store');
    Route::delete('/piping-materials/vendor-prices/{id}', [PipingMaterialController::class, 'deleteVendorPrice'])->name('piping-materials.vendor-prices.destroy');
    Route::resource('piping-materials', PipingMaterialController::class)->only(['index', 'store', 'update', 'destroy']);

    // 2. Estimasi / RAB Proyek Piping
    Route::post('/piping-rab/suppliers/quick-store', [PipingRabController::class, 'quickStoreSupplier'])->name('piping-rab.suppliers.quick-store');
    Route::post('/piping-rab/items/{id}/update-supplier', [PipingRabController::class, 'updateItemSupplier'])->name('piping-rab.items.update-supplier');
    Route::post('/piping-rab/{id}/revise', [PipingRabController::class, 'revise'])->name('piping-rab.revise');
    Route::post('/piping-rab/{id}/convert', [PipingRabController::class, 'convertToQuotation'])->name('piping-rab.convert');
    Route::resource('piping-rab', PipingRabController::class);
});
