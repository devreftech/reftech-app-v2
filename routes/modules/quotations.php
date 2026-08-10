<?php

use App\Http\Controllers\QuotationController;
use App\Http\Controllers\UnitQuotationController;
use App\Http\Controllers\SuoController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    // Quotation Resource & Management
    Route::resource('/quotation', QuotationController::class);
    Route::resource('/unit-quotation', UnitQuotationController::class);

    // Unit Quotation Custom Endpoints
    Route::prefix('unit-quotation')->group(function () {
        Route::get('/pics/{clientId}', [UnitQuotationController::class, 'getPics'])->name('unit-quotation.pics');
        Route::post('/{id}/revise', [UnitQuotationController::class, 'revise'])->name('unit-quotation.revise');
        Route::post('/{id}/change-status', [UnitQuotationController::class, 'changeStatus'])->name('unit-quotation.change-status');
        Route::post('/{id}/upload-po', [UnitQuotationController::class, 'uploadPO'])->name('unit-quotation.upload-po');
        Route::post('/{id}/cancel-po', [UnitQuotationController::class, 'cancelPO'])->name('unit-quotation.cancel-po');
        Route::post('/{id}/approve-cancel', [UnitQuotationController::class, 'approveCancel'])->name('unit-quotation.approve-cancel');
        Route::post('/{id}/reject-cancel', [UnitQuotationController::class, 'rejectCancel'])->name('unit-quotation.reject-cancel');
        Route::post('/{id}/request-next-invoice', [UnitQuotationController::class, 'requestNextInvoice'])->name('unit-quotation.request-next-invoice');
        Route::get('/{id}/print', [UnitQuotationController::class, 'print'])->name('unit-quotation.print');
    });

    // SUO Integration
    Route::post('/suo/from-quotation/{quotationId}', [SuoController::class, 'storeFromQuotation'])->name('suo.storeFromQuotation');
    Route::post('/suo/from-unit-quotation/{unitQuotationId}', [SuoController::class, 'storeFromUnitQuotation'])->name('suo.storeFromUnitQuotation');
});
