<?php

use App\Http\Controllers\QuotationController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\UnitQuotationController;
use App\Http\Controllers\SuoController;
use App\Http\Controllers\ArchiveController;
use App\Models\Client;
use App\Models\Pic;
use App\Models\Quotation;
use Carbon\Carbon;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    // Quotation Resource & Management
    Route::get('/quotation/products/search', [QuotationController::class, 'searchProducts'])->name('quotation.products.search');
    Route::get('/quotation/card-stats', [QuotationController::class, 'cardStats'])->name('quotation.card-stats');
    Route::resource('/quotation', QuotationController::class);
    Route::get('/quotation/leads/create', [QuotationController::class, 'create'])->name('create.quotation');
    Route::get('/prospect-quotation', [QuotationController::class, 'prospect_quote'])->name('quotation.prospect');
    Route::get('/po', [QuotationController::class, 'po_quote'])->name('quotation.po');
    Route::get('/loss', [QuotationController::class, 'loss_quote'])->name('quotation.loss');
    Route::post('/quotation/{id}/change_status', [QuotationController::class, 'change_status'])->name('status.change.quotation');
    Route::post('/quotation/{id}/add_comment', [QuotationController::class, 'add_comment'])->name('add-comment.quotation');
    Route::post('/quotation/{id}/view_comment', [QuotationController::class, 'view_comment'])->name('view-comment.quotation');
    Route::post('/quotation/{id}/change_po', [QuotationController::class, 'change_po'])->name('change-po.quotation');
    Route::post('/quotation/{id}/cancel_po', [QuotationController::class, 'cancel_po'])->name('status.cancel.quotation');
    Route::post('/quotation/{id}/convert_flag', [QuotationController::class, 'convert_flag'])->name('convert-flag.quotation');
    Route::post('/quotation/{id}/convert_po', [QuotationController::class, 'convert_po'])->name('convert-po.quotation');
    Route::post('/quotation/{id}/request_bp', [QuotationController::class, 'request_bp'])->name('request-bp.quotation');
    Route::post('/quotation/{id}/request_next_invoice', [QuotationController::class, 'request_next_invoice'])->name('request_next_invoice.quotation');
    Route::post('/quotation/{id}/upload_po', [QuotationController::class, 'upload_po'])->name('upload-po.quotation');
    Route::post('/quotation/{id}/mentions', [QuotationController::class, 'add_mention'])->name('add_mention.quotation');
    Route::get('/quotation/{id}/download_po', [QuotationController::class, 'download_po'])->name('download-po.quotation');
    Route::delete('/quotation/{id}/delete_po', [QuotationController::class, 'delete_po'])->name('delete-po.quotation');
    Route::post('/quotation/{id}/insert_fee', [QuotationController::class, 'insert_fee'])->name('insert_fee.quotation');
    Route::post('/quotation/{id}/insert_fee_service', [QuotationController::class, 'insert_fee_service'])->name('insert_fee_service.quotation');
    Route::post('/quotation/{id}/delete_fee', [QuotationController::class, 'delete_fee'])->name('delete_fee.quotation');
    Route::post('/quotation/{id}/add_payment', [QuotationController::class, 'add_payment'])->name('add_payment.quotation');
    Route::post('/quotation/{id}/proof_payment', [QuotationController::class, 'proof_payment'])->name('proof_payment.quotation');
    Route::post('/quotation/{id}/change_primary', [QuotationController::class, 'change_primary'])->name('change_primary.quotation');
    Route::get('/quotation/{id}/download_payment', [QuotationController::class, 'download_payment'])->name('download-payment.quotation');
    Route::delete('/quotation/{id}/delete_payment', [QuotationController::class, 'delete_payment'])->name('delete-payment.quotation');
    Route::post('/quotation/{id}/confirm_payment', [QuotationController::class, 'confirm_payment'])->name('confirm-payment.quotation');
    Route::get('/quotation/revision/{id}', [QuotationController::class, 'edit_revisi'])->name('revisi.quotation');
    Route::get('/quotation/edit-sparepart/{id}', [QuotationController::class, 'edit_parts'])->name('edit-sparepart.quotation');
    Route::get('/quotation/edit-service/{id}', [QuotationController::class, 'edit_service'])->name('edit-service.quotation');
    Route::patch('/quotation/edit-sparepart/{id}', [QuotationController::class, 'update_part'])->name('update-sparepart.quotation');
    Route::patch('/quotation/edit-service/{id}', [QuotationController::class, 'update_service'])->name('edit-service.quotation');
    Route::get('/quotation/revision-overhaul/{id}', [QuotationController::class, 'revisionService'])->name('revisi-overhaul.quotation');
    Route::get('/quotation/print/{id}', [QuotationController::class, 'print_quote'])->name('print.quotation');
    Route::get('/quotation/pdf/{id}', [QuotationController::class, 'pdf_quote'])->name('pdf.quotation');
    Route::get('/quotation/sales/{id}', [QuotationController::class, 'sales_quotation'])->name('sales.quotation');
    Route::get('/po/sales/{id}', [QuotationController::class, 'sales_po'])->name('sales.po');
    Route::get('/quotation/sparepart/{id}', [QuotationController::class, 'replacementDetailSparepart'])->name('detail.replacement');
    Route::get('/quotation/unit/{id}', [QuotationController::class, 'replacementDetailUnit'])->name('detail.replacement');
    Route::get('/quotation/client/{id}', function ($id) {
        $client = Client::find($id);
        return response()->json($client);
    });
    Route::get('/quotation/pic/{id}', function ($id) {
        $client = Pic::where('pic.id_client', $id)->get();
        return response()->json($client);
    });
    Route::get('/quote/unit', [QuotationController::class, 'quotationUnit'])->name('index-unit.quotation');
    Route::get('/quote/unit/create', [QuotationController::class, 'quotationCreateUnit'])->name('create-unit.quotation');
    Route::get('/quote/unit-detail/{id}', [UnitController::class, 'quotationDetail'])->name('detail.quote-unit');
    Route::get('/quote/service/create', [QuotationController::class, 'createService'])->name('create-service.quotation');
    Route::post('/quote/service/store', [QuotationController::class, 'storeService'])->name('store-service.quotation');
    Route::get('/quote/service-show/{id}', [QuotationController::class, 'showService'])->name('show-service.quotation');
    Route::post('/quote/service-update/{id}', [QuotationController::class, 'updateService'])->name('update-service.quotation');
    Route::delete('/quote/service-delete/{id}', [QuotationController::class, 'destroyService'])->name('delete-service.quotation');
    Route::get('/quote/service-print/{id}', [QuotationController::class, 'printService'])->name('service-print.quotation');
    Route::get('/quote/service-print-no-image/{id}', [QuotationController::class, 'printNoImageService'])->name('service-print-no-image.quotation');

    Route::post('/quote/choose-machine', [QuotationController::class, 'chooseMachine'])->name('quotation.choose-machine');
    Route::post('/quote/overhaul/store', [QuotationController::class, 'storeOverhaul'])->name('store-overhaul.quotation');
    Route::get('/quote/overhaul-create/{id}', [QuotationController::class, 'createOverhaul'])->name('create-overhaul.quotation');
    Route::get('/quote/overhaul-show/{id}', [QuotationController::class, 'showOverhaul'])->name('show-overhaul.quotation');
    Route::get('/quote/overhaul-print/{id}', [QuotationController::class, 'printOverhaul'])->name('overhaul-print.quotation');
    Route::get('/quote/overhaul-print-no-image/{id}', [QuotationController::class, 'printNoImageOverhaul'])->name('overhaul-print-no-image.quotation');
    Route::get('/quote/overhaul-revision/{id}', [QuotationController::class, 'revisionOverhaul'])->name('overhaul-revision.quotation');
    Route::post('/quote/overhaul-update/{id}', [QuotationController::class, 'updateOverhaul'])->name('overhaul-update.quotation');
    Route::get('/quotation/edit-overhaul/{id}', [QuotationController::class, 'editOverhaul'])->name('edit-overhaul.quotation');
    Route::patch('/quotation/edit-overhaul/{id}', [QuotationController::class, 'updateOverhaulDirect'])->name('edit-overhaul.quotation');

    // Unit Quotation — URI publik "smart-quote" (rebrand tampilan), nama route tetap
    // "unit-quotation.*" biar semua route()/pemanggilan lain di app gak perlu diubah.
    Route::resource('smart-quote', UnitQuotationController::class)->names('unit-quotation');
    Route::get('/smart-quote/{id}/print', [UnitQuotationController::class, 'print'])->name('unit-quotation.print');
    Route::get('/smart-quote/pics/{clientId}', [UnitQuotationController::class, 'getPics'])->name('unit-quotation.pics');
    Route::get('/smart-quote/clients-by-sales/{salesId}', [UnitQuotationController::class, 'getClientsBySales'])->name('unit-quotation.clients-by-sales');
    Route::post('/smart-quote/{id}/change-status', [UnitQuotationController::class, 'changeStatus'])->name('unit-quotation.change-status');
    Route::post('/smart-quote/{id}/revise', [UnitQuotationController::class, 'revise'])->name('unit-quotation.revise');
    Route::post('/smart-quote/{id}/upload-po', [UnitQuotationController::class, 'uploadPO'])->name('unit-quotation.upload-po');
    Route::post('/smart-quote/{id}/request-next-invoice', [UnitQuotationController::class, 'requestNextInvoice'])->name('unit-quotation.request-next-invoice');
    Route::post('/smart-quote/{id}/add-payment', [UnitQuotationController::class, 'addPayment'])->name('unit-quotation.add-payment');
    Route::get('/notifications/payment/unread', [UnitQuotationController::class, 'unreadPaymentNotifications'])->name('notifications.payment.unread');
    Route::post('/notifications/payment/{id}/read', [UnitQuotationController::class, 'markPaymentNotificationRead'])->name('notifications.payment.read');
    Route::post('/smart-quote/payment/{id}/proof', [UnitQuotationController::class, 'proofPayment'])->name('unit-quotation.proof-payment');
    Route::delete('/smart-quote/payment/{id}/proof', [UnitQuotationController::class, 'deleteProof'])->name('unit-quotation.delete-proof');
    Route::delete('/smart-quote/payment/{id}', [UnitQuotationController::class, 'deletePayment'])->name('unit-quotation.delete-payment');
    Route::patch('/smart-quote/payment/{id}', [UnitQuotationController::class, 'updatePayment'])->name('unit-quotation.update-payment');
    Route::post('/smart-quote/{id}/cancel-po', [UnitQuotationController::class, 'cancelPO'])->name('unit-quotation.cancel-po');
    Route::post('/smart-quote/{id}/toggle-hide-title', [UnitQuotationController::class, 'toggleHideTitle'])->name('unit-quotation.toggle-hide-title');
    Route::post('/smart-quote/{id}/approve-cancel', [UnitQuotationController::class, 'approveCancelPO'])->name('unit-quotation.approve-cancel');
    Route::post('/smart-quote/{id}/reject-cancel', [UnitQuotationController::class, 'rejectCancelPO'])->name('unit-quotation.reject-cancel');
    Route::post('/smart-quote/{id}/update-pending', [UnitQuotationController::class, 'updatePendingPo'])->name('unit-quotation.update-pending');
    Route::post('/smart-quote/{id}/delivery', [UnitQuotationController::class, 'storeDelivery'])->name('unit-quotation.storeDelivery');
    Route::post('/smart-quote/{id}/comment', [UnitQuotationController::class, 'storeComment'])->name('unit-quotation.storeComment');
    Route::post('/smart-quote/comments/{id}/update', [UnitQuotationController::class, 'updateComment'])->name('unit-quotation.updateComment');
    Route::delete('/smart-quote/comments/{id}', [UnitQuotationController::class, 'destroyComment'])->name('unit-quotation.destroyComment');

    // SUO (Sales Urgent Order)
    Route::get('/suo', [SuoController::class, 'index'])->name('suo.index');
    Route::get('/suo/create', [SuoController::class, 'create'])->name('suo.create');
    Route::post('/suo', [SuoController::class, 'store'])->name('suo.store');
    Route::get('/suo/{id}', [SuoController::class, 'show'])->name('suo.show');
    Route::post('/suo/{id}/check-stock', [SuoController::class, 'checkStock'])->name('suo.checkStock');
    Route::post('/suo/{id}/approve', [SuoController::class, 'approve'])->name('suo.approve');
    Route::get('/suo/{id}/suggest-booking', [SuoController::class, 'suggestBookingNumber'])->name('suo.suggestBooking');
    Route::post('/suo/{id}/delivery', [SuoController::class, 'storeDelivery'])->name('suo.storeDelivery');
    Route::get('/suo/{id}/convert', [SuoController::class, 'convert'])->name('suo.convert');
    Route::post('/suo/{id}/mark-converted', [SuoController::class, 'markConverted'])->name('suo.markConverted');
    Route::get('/suo/{id}/linkable-quotations', [SuoController::class, 'linkableQuotations'])->name('suo.linkableQuotations');
    Route::post('/suo/{id}/link-quotation', [SuoController::class, 'linkQuotation'])->name('suo.linkQuotation');
    Route::post('/suo/from-quotation/{quotationId}', [SuoController::class, 'storeFromQuotation'])->name('suo.storeFromQuotation');
    Route::post('/suo/from-unit-quotation/{unitQuotationId}', [SuoController::class, 'storeFromUnitQuotation'])->name('suo.storeFromUnitQuotation');
    // AJAX data
    Route::get('/db/suo/sales', [SuoController::class, 'dataSales'])->name('suo.data.sales');
    Route::get('/db/suo/logistic', [SuoController::class, 'dataLogistic'])->name('suo.data.logistic');
    Route::get('/db/suo/accounting', [SuoController::class, 'dataAccounting'])->name('suo.data.accounting');
    // Logistic & Accounting index pages
    Route::get('/suo-logistic', [SuoController::class, 'logisticIndex'])->name('suo.logistic.index');
    Route::get('/suo-accounting', [SuoController::class, 'accountingIndex'])->name('suo.accounting.index');

    // Quotation Archive
    Route::get('/archive/quotation', [ArchiveController::class, 'archive_quotation'])->name('archive.quotation');
    Route::post('/un-archive/quotation/{id}', [ArchiveController::class, 'unarchive_quotation'])->name('unarchive.quotation');
    Route::delete('/delete-archive/quotation/{id}', [ArchiveController::class, 'delete_archive_quotation'])->name('delete-archive.quotation');

    // Legacy AJAX endpoints (app/api/quotation/*)
    Route::get('/db/quotation', function () {
        require_once base_path('app/api/quotation/connection.php');
    });
    Route::get('/db/quotation/admin', function () {
        require_once base_path('app/api/quotation/connectionAdmin.php');
    });
    Route::get('/db/quotation/unit', function () {
        require_once base_path('app/api/quotation/connectionUnit.php');
    });
    Route::get('/db/quotation/admin/unit', function () {
        require_once base_path('app/api/quotation/connectionAdminUnit.php');
    });
    Route::get('/db/quotation/archive', function () {
        require_once base_path('app/api/quotation/connectionArchive.php');
    });
    Route::get('/db/quotation/hot', function () {
        require_once base_path('app/api/quotation/connectionHotProspect.php');
    });
    Route::get('/db/quotation/hot/admin', function () {
        require_once base_path('app/api/quotation/connectionHotProspectAdmin.php');
    });
    Route::get('/db/quotation/admin/tab', function () {
        require_once base_path('app/api/quotation/connectionAdminTab.php');
    });
    Route::get('/db/quotation/admin/project', function () {
        require_once base_path('app/api/quotation/connectionAdminProject.php');
    });
    Route::get('/db/quotation/prospect', function () {
        require_once base_path('app/api/quotation/connectionProspect.php');
    });
    Route::get('/db/quotation/prospect/sales', function () {
        require_once base_path('app/api/quotation/connectionProspectSales.php');
    });
    Route::get('/db/quotation/sales/{id}', function ($id) {
        $dateNow = Carbon::now();
        $monthNow = $dateNow->month;
        $quotation = Quotation::join('pic', 'pic.id', '=', 'quotation.id_pic')->join('client', 'client.id', '=', 'pic.id_client')->whereMonth('estimated_date', $monthNow)->where('quotation.id_sales', $id)->where('quotation.level', '1')->where('quotation.is_primary', '1')->get(['quotation.*', 'client.company']);
        return response()->json(['data' => $quotation]);
    });
    Route::get('/db/quotation/mentions/{id}', function ($id) {
        $dateNow = Carbon::now();
        $monthNow = $dateNow->month;
        $quotation = Quotation::join('pic', 'pic.id', '=', 'quotation.id_pic')->join('client', 'client.id', '=', 'pic.id_client')->whereMonth('estimated_date', $monthNow)->where('quotation.id_sales', $id)->where('quotation.level', '1')->where('quotation.is_primary', '1')->get(['quotation.*', 'client.company']);
        return response()->json(['data' => $quotation]);
    });
    Route::get('/db/po/sales/{id}', function ($id) {
        $dateNow = Carbon::now();
        $monthNow = $dateNow->month;
        $quotation = Quotation::join('pic', 'pic.id', '=', 'quotation.id_pic')->join('client', 'client.id', '=', 'pic.id_client')->whereMonth('po_date', $monthNow)->where('quotation.id_sales', $id)->where('status', '100')->orderBy('po_date', 'asc')->where('quotation.level', '1')->where('quotation.is_primary', '1')->get(['quotation.*', 'client.company']);
        return response()->json(['data' => $quotation]);
    });
    Route::get('/db/quotation/invoice', function () {
        // Regular quotation requests
        $service = Quotation::join('pic', 'pic.id', '=', 'quotation.id_pic')
            ->join('client', 'client.id', '=', 'pic.id_client')
            ->join('invoice', 'invoice.id_quotation', '=', 'quotation.id')
            ->join('users', 'users.id', '=', 'quotation.id_sales')
            ->where('status', '100')
            ->whereNotNull('client.npwp')
            ->whereNotNull('quotation.po_file')
            ->whereNull('invoice.no_invoice')
            ->get([
                'quotation.no_quote', 'invoice.no_po', 'quotation.po_date',
                'quotation.harga_total', 'client.company', 'users.name', 'users.image as sales_image',
                'invoice.id', 'invoice.type',
                \DB::raw("'service' AS row_type"),
            ]);

        // Unit quotation requests
        $unit = \App\Models\Invoice::join('unit_quotation as uq', 'uq.id', '=', 'invoice.id_unit_quotation')
            ->join('client', 'client.id', '=', 'uq.id_client')
            ->join('users', 'users.id', '=', 'uq.id_sales')
            ->whereNull('invoice.no_invoice')
            ->whereNotNull('invoice.id_unit_quotation')
            ->whereNull('invoice.rejected_at')
            ->get([
                'uq.no_quote', 'uq.po_number as no_po',
                \DB::raw('uq.created_at AS po_date'),
                'uq.total as harga_total', 'client.company', 'users.name', 'users.image as sales_image',
                'invoice.id', 'invoice.type',
                \DB::raw("'unit' AS row_type"),
            ]);

        return response()->json(['data' => $service->merge($unit)->values()]);
    });
});
