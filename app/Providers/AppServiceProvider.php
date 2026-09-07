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

                    // AP Due Date Alerts (Due Today, Due Soon <= 7 days, Overdue)
                    $today = \Carbon\Carbon::today();
                    $unpaidInvoices = \App\Models\ProductIn::whereNotNull('invoice')
                        ->whereIn('accept', ['0', '2'])
                        ->with(['supplier', 'purchaseOrder'])
                        ->get();

                    $apDueToday = [];
                    $apDueSoon = [];
                    $apOverdue = [];

                    foreach ($unpaidInvoices as $item) {
                        $remaining = $item->remaining_payable;
                        if ($remaining <= 0) continue;

                        $dueDateStr = $item->due_date;
                        if (!$dueDateStr) continue;

                        $due = \Carbon\Carbon::parse($dueDateStr)->startOfDay();
                        $notifItem = (object) [
                            'id' => $item->id,
                            'invoice' => $item->invoice ?: $item->no_product_in,
                            'supplier_name' => $item->supplier?->supplier ?? $item->supplier ?? 'Supplier',
                            'remaining' => $remaining,
                            'due_date' => $dueDateStr,
                            'url' => route('payable.show_invoice', $item->id),
                        ];

                        if ($due->isToday()) {
                            $notifItem->status_type = 'today';
                            $notifItem->status_badge = 'JATUH TEMPO HARI INI';
                            $notifItem->badge_class = 'bg-danger text-white';
                            $notifItem->avatar_bg = 'bg-danger';
                            $notifItem->icon = 'mdi-calendar-alert';
                            $notifItem->priority = 1;
                            $apDueToday[] = $notifItem;
                        } elseif ($today->gt($due)) {
                            $daysOverdue = $today->diffInDays($due);
                            $notifItem->status_type = 'overdue';
                            $notifItem->status_badge = "LEWAT JATUH TEMPO ({$daysOverdue} Hari)";
                            $notifItem->badge_class = 'bg-label-danger';
                            $notifItem->avatar_bg = 'bg-label-danger';
                            $notifItem->icon = 'mdi-alert-circle-outline';
                            $notifItem->days_overdue = $daysOverdue;
                            $notifItem->priority = 3;
                            $apOverdue[] = $notifItem;
                        } elseif ($today->diffInDays($due, false) <= 7) {
                            $daysLeft = $today->diffInDays($due, false);
                            $notifItem->status_type = 'due_soon';
                            $notifItem->status_badge = $daysLeft == 1 ? 'BESOK JATUH TEMPO' : "JATUH TEMPO {$daysLeft} HARI LAGI";
                            $notifItem->badge_class = 'bg-warning text-dark';
                            $notifItem->avatar_bg = 'bg-label-warning';
                            $notifItem->icon = 'mdi-clock-alert-outline';
                            $notifItem->days_left = $daysLeft;
                            $notifItem->priority = 2;
                            $apDueSoon[] = $notifItem;
                        }
                    }

                    // Urutkan overdue dari yang paling lama/kritis, due soon dari yang terdekat
                    usort($apOverdue, fn($a, $b) => $b->days_overdue <=> $a->days_overdue);
                    usort($apDueSoon, fn($a, $b) => $a->days_left <=> $b->days_left);

                    $apNotifications = collect(array_merge($apDueToday, $apDueSoon, array_slice($apOverdue, 0, 10)));
                    $view->with('apNotifications', $apNotifications);
                    $view->with('apDueTodayCount', count($apDueToday));
                    $view->with('apDueSoonCount', count($apDueSoon));
                    $view->with('apOverdueCount', count($apOverdue));
                    $view->with('apTotalAlertCount', count($apDueToday) + count($apDueSoon));
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
