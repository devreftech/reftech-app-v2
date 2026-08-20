<?php

namespace App\Providers;

use App\Models\HelpdeskTicket;
use App\Models\PrDiscussionMention;
use App\Models\PurchaseRequest;
use App\Models\UnitQuotation;
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
                // Samakan dengan filter di PurchaseController::index() — PR dengan
                // id_pending yatim (pending_po sudah terhapus) tidak akan pernah bisa
                // ditampilkan di tabel manapun, jadi jangan ikut dihitung di badge juga.
                $prCount = PurchaseRequest::where('status', '0')
                    ->whereIn('id_pending', \App\Models\PendingPO::pluck('id'))
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
    }
}
