<?php

namespace App\Providers;

use App\Models\HelpdeskTicket;
use App\Models\PrDiscussionMention;
use App\Models\PurchaseRequest;
use App\Models\UnitQuotation;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        // App has no Tailwind CSS loaded (Bootstrap/Sneat admin theme), but Laravel's
        // default paginator view is Tailwind-styled — every ->links() call rendered
        // unstyled raw markup. Switch to the Bootstrap pagination view site-wide.
        Paginator::useBootstrap();

        // Inject $prMentions & $pendingCancelQuotes ke navbar agar notifikasi
        // muncul di semua halaman tanpa mengubah setiap controller.
        View::composer('layouts.sales.navbar', function ($view) {
            if (Auth::check()) {
                $prMentions = PrDiscussionMention::where('id_user_mention', Auth::id())
                    ->where('level', '0')
                    ->with(['discussion.pending'])
                    ->orderByDesc('created_at')
                    ->take(10)
                    ->get();
                $view->with('prMentions', $prMentions);

                if (in_array(Auth::user()->role, ['Admin', 'Accounting', 'Finance'])) {
                    $pendingCancelQuotes = UnitQuotation::with(['client', 'sales'])
                        ->where('cancel_request', 1)
                        ->orderByDesc('updated_at')
                        ->get();
                    $view->with('pendingCancelQuotes', $pendingCancelQuotes);
                }

                if (in_array(Auth::user()->role, ['Accounting', 'Admin', 'Sales'])) {
                    // Daftar ini menampilkan notifikasi terbaru terlepas dari status baca,
                    // supaya klik "tandai dibaca" cuma menghilangkan penanda merahnya —
                    // bukan menghapus notifikasi dari daftar. Badge merah tetap hitung dari unread saja.
                    $paymentNotifications = \App\Models\UnitQuotationPaymentNotification::where('id_user', Auth::id())
                        ->with(['unitQuotation.client', 'payment', 'invoice'])
                        ->orderByDesc('created_at')
                        ->take(15)
                        ->get();
                    $view->with('paymentNotifications', $paymentNotifications);
                    $view->with('paymentUnreadCount', $paymentNotifications->where('is_read', false)->count());
                }
            }
        });

        // Inject $prCount ke sidebar agar badge "Purchase Request" (New Purchase)
        // muncul di semua halaman untuk role Admin/Accounting, bukan cuma di Dashboard.
        View::composer('components.dashboard.sidebar', function ($view) {
            if (Auth::check() && in_array(Auth::user()->role, ['Admin', 'Accounting'])) {
                // Samakan persis dengan filter di PurchaseController::index():
                // 1. PR status 0 (New Purchase)
                // 2. id_pending memiliki quotation/unit_quotation valid
                // 3. Memiliki detail item (bukan draft kosong)
                $validPendingIds = \App\Models\PendingPO::where(function ($q) {
                    $q->whereNotNull('id_quotation')->orWhereNotNull('id_unit_quotation');
                })->pluck('id');

                $prCount = PurchaseRequest::where('status', '0')
                    ->whereIn('id_pending', $validPendingIds)
                    ->has('details')
                    ->count();

                $view->with('prCount', $prCount);
            }
        });

        // Inject $openTicketCount ke sidebar agar badge "Helpdesk" muncul
        // untuk Admin, konsisten dengan badge $prCount di atas.
        View::composer('components.dashboard.sidebar', function ($view) {
            if (Auth::check() && Auth::user()->role == 'Admin') {
                $openTicketCount = HelpdeskTicket::where('status', 'Open')->count();
                $view->with('openTicketCount', $openTicketCount);
            }
        });

        // Badge "Service Reports" untuk role ServiceM: jumlah report yang masih
        // nunggu approval (pending). Approve dulu baru kehitung di badge Sales.
        View::composer('components.dashboard.sidebar', function ($view) {
            if (Auth::check() && in_array(Auth::user()->role, ['ServiceM', 'Admin'])) {
                $srPendingApprovalCount = \App\Models\Reports::where('approval_status', 'pending')->count();
                $view->with('srPendingApprovalCount', $srPendingApprovalCount);
            }
        });
    }
}
