<?php

use App\Http\Controllers\CustomersController;
use App\Http\Controllers\LeadsController;
use App\Http\Controllers\ProspectController;
use App\Http\Controllers\CrmController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    // Customers
    Route::resource('/customers', CustomersController::class);
    Route::get('/customers/detail/{id}', [CustomersController::class, 'show'])->name('detail.customers');
    Route::get('/key-accounts', [CustomersController::class, 'keyAccounts'])->name('key-accounts.index');

    // Leads
    Route::resource('/leads', LeadsController::class);
    Route::get('/leads-by-sales', [LeadsController::class, 'indexBySales'])->name('index-sales.leads');
    Route::get('/leads/detail/{id}', [LeadsController::class, 'show'])->name('detail.leads');
    Route::post('/leads/action/{id}', [LeadsController::class, 'storeActionWithLeads'])->name('action.leads');
    Route::post('/leads/visit/{id}', [LeadsController::class, 'storeVisitWithLeads'])->name('visit.leads');
    Route::post('/leads/convert/{id}', [LeadsController::class, 'convertToCustomers'])->name('convert.leads');
    Route::post('/leads/send-intro/{id}', [LeadsController::class, 'sendIntroEmail'])->name('leads.send-intro');

    // Prospect & CRM
    Route::resource('/prospect', ProspectController::class);
    Route::get('/existing/yearly/{id}', [CrmController::class, 'detailPerYear'])->name('existing.yearly');
});
