<?php

use App\Http\Controllers\CustomersController;
use App\Http\Controllers\LeadsController;
use App\Http\Controllers\ProspectController;
use App\Http\Controllers\CrmController;
use Illuminate\Support\Facades\Route;

// No auth middleware here - matches this route's historical (pre-refactor) behavior.
Route::get('/existing/yearly/{id}', [CrmController::class, 'detailPerYear'])->name('existing.yearly');

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

    // Existing Customers (CRM lifecycle)
    Route::resource('/existing', CrmController::class);
    Route::get('/customer-by-sales', [CrmController::class, 'indexBySales'])->name('index-sales.customers');
    Route::get('/customer-by-status', [CrmController::class, 'indexByStatus'])->name('index-status.customers');
    Route::get('/existing-bangkrupt', [CrmController::class, 'indexBangkrupt'])->name('index.bangkrupt');
    Route::post('/existing/action/{id}', [CrmController::class, 'storeActionWithCrm'])->name('action.crm');
    Route::post('/existing/update-status/{id}', [CrmController::class, 'updateStatusAtDropdown'])->name('update-status.crm');
    Route::get('/ru', [CrmController::class, 'ruIndex'])->name('ru.index');

    // Prospect
    Route::resource('/prospect', ProspectController::class);
    Route::post('/prospect/add_sales/{id}', [ProspectController::class, 'add_sales'])->name('add_sales.prospect');
    Route::post('/prospect/without_quotation/{id}', [ProspectController::class, 'without_quotation'])->name('without_quotation.prospect');
    Route::post('/prospect/with_quotation/{id}', [ProspectController::class, 'with_quotation'])->name('with_quotation.prospect');
    Route::post('/prospect/onProcessFU/{id}', [ProspectController::class, 'onProcessFU'])->name('onProcessFU.prospect');
    Route::post('/prospect/no_respond/{id}', [ProspectController::class, 'no_respond'])->name('no_respond.prospect');
    Route::post('/prospect/no_provide/{id}', [ProspectController::class, 'no_provide'])->name('no_provide.prospect');
    Route::get('/prospect/create_quotation/{id}', [ProspectController::class, 'create_quotation'])->name('create_quotation.prospect');
    Route::post('/prospect/store_quotation/{id}', [ProspectController::class, 'store_quotation'])->name('store_quotation.prospect');
    Route::post('/prospect/choose_quotation/{id}', [ProspectController::class, 'choose_quotation'])->name('choose_quotation.prospect');
    Route::post('/prospect/add_comment/{id}', [ProspectController::class, 'add_comment'])->name('add_comment.prospect');
    Route::post('/prospect/{id}/view_comment', [ProspectController::class, 'view_comment'])->name('view_comment.prospect');
    Route::get('/prospect/monthly-leads/{sales}', [ProspectController::class, 'monthlyLeads'])->name('monthly_leads.prospect');
});
