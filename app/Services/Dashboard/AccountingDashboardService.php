<?php

namespace App\Services\Dashboard;

use App\Models\Comment;
use App\Models\Contract;
use App\Models\Quotation;
use App\Models\Invoice;
use App\Models\Prospect;
use App\Models\PendingPO;
use App\Models\Payment;
use App\Models\Reminder;
use App\Models\DetailExpense;
use App\Models\ProductIn;
use App\Models\Expense;
use App\Models\FixedAsset;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AccountingDashboardService
{
    /**
     * Get dashboard data payload for Accounting role
     */
    public function getDashboardData($notulens)
    {
        $firstComments = Comment::where('id_user', Auth::id())
            ->groupBy('id_status')
            ->get();
        $statusIds = $firstComments->pluck('id_status')->toArray();
        $dates = $firstComments->pluck('created_at', 'id_status');
        
        $commentsQuery = Comment::join('change_status as c', 'c.id', '=', 'comment.id_status')
            ->join('quotation as q', 'q.id', '=', 'c.id_quotation')
            ->join('users as u', 'u.id', '=', 'comment.id_user')
            ->whereIn('comment.id_status', $statusIds)
            ->where(function ($query) use ($dates) {
                foreach ($dates as $statusId => $createdAt) {
                    $query->orWhere(function ($subQuery) use ($statusId, $createdAt) {
                        $subQuery->where('comment.id_status', $statusId)
                            ->whereRaw('TIMESTAMPDIFF(SECOND, ?, comment.created_at) > 0', [$createdAt]);
                    });
                }
            })
            ->where('comment.id_user', '!=', Auth::id());

        $commentAdmin = $commentsQuery->orderBy('comment.id_status')
            ->orderByDesc('comment.created_at')
            ->get(['q.id as idQ', 'comment.id as idC', 'comment.id_user', 'comment.level', 'comment.comment', 'comment.date', 'q.no_quote', 'u.name', 'u.image']);

        // Filter untuk komentar dengan level '1'
        $unreadCommentAdmin = $commentsQuery->where('comment.level', '1')
            ->orderBy('comment.id_status')
            ->orderByDesc('comment.created_at')
            ->get(['q.id as idQ', 'comment.id as idC', 'comment.id_user', 'comment.level', 'comment.comment', 'comment.date', 'q.no_quote', 'u.name', 'u.image']);

        $requestContract = Contract::join('quotation as q', 'q.id', '=', 'contract.id_quotation')
            ->join('pic as p', 'p.id', '=', 'q.id_pic')
            ->join('client as c', 'c.id', '=', 'p.id_client')
            ->join('users as u', 'u.id', '=', 'q.id_sales')
            ->where('contract.level', '0')
            ->count();

        $requestInvoice = Quotation::join('pic', 'pic.id', '=', 'quotation.id_pic')
            ->join('client', 'client.id', '=', 'pic.id_client')
            ->join('invoice', 'invoice.id_quotation', '=', 'quotation.id')
            ->join('users', 'users.id', '=', 'quotation.id_sales')
            ->where('status', '100')
            ->whereNotNull('quotation.po_file')
            ->whereNull('invoice.no_invoice')
            ->count()
            + Invoice::pendingUnitRequest()->count();

        $noSaleProspect = Prospect::whereNULL('id_sales')->whereNull('provide')->count();
        $newCount = PendingPO::where('status', operator: 0)
            ->where('type', 'Non Project')
            ->count();
        $listCount = PendingPO::whereIn('pending_po.status', [1, 2, 3, 4])
            ->where('type', 'Non Project')
            ->count();
        $deliveryCount = PendingPO::where('pending_po.status', 5)
            ->where('type', 'Non Project')
            ->count();

        $start1 = Carbon::createFromDate(null, 1, 1);  // 1 Januari tahun ini
        $end1 = Carbon::createFromDate(null, 6, 30); // 30 Juni tahun ini
        $start2 = Carbon::createFromDate(null, 7, 1);  // 1 Juli tahun ini
        $end2 = Carbon::createFromDate(null, 12, 31); // 31 Desember tahun ini

        $allInvoice1 = Invoice::join('quotation as q', 'q.id', '=', 'invoice.id_quotation')
            ->join('pic as p', 'q.id_pic', '=', 'p.id')
            ->join('client as c', 'p.id_client', '=', 'c.id')
            ->whereBetween('invoice.date', [$start1, $end1])
            ->where('q.status', '100')
            ->where('q.level', '1')
            ->where('q.is_primary', '1')
            ->groupBy('q.id')
            ->select('q.harga_total', 'c.info')
            ->get();

        $allInvoice2 = Invoice::join('quotation as q', 'q.id', '=', 'invoice.id_quotation')
            ->join('pic as p', 'q.id_pic', '=', 'p.id')
            ->join('client as c', 'p.id_client', '=', 'c.id')
            ->whereBetween('invoice.date', [$start2, $end2])
            ->where('q.status', '100')
            ->where('q.level', '1')
            ->where('q.is_primary', '1')
            ->groupBy('invoice.id')
            ->select('q.harga_total', 'c.info')
            ->get();

        $paidInvoice1 = Invoice::join('quotation as q', 'q.id', '=', 'invoice.id_quotation')
            ->join('payment as pay', 'pay.id_quotation', '=', 'q.id')
            ->join('pic as p', 'q.id_pic', '=', 'p.id')
            ->join('client as c', 'p.id_client', '=', 'c.id')
            ->whereBetween('invoice.date', [$start1, $end1])
            ->where('pay.level', '1')
            ->groupBy('pay.id')
            ->select('pay.id', 'pay.amount', 'c.info')
            ->get();

        $paidInvoice2 = Invoice::join('quotation as q', 'q.id', '=', 'invoice.id_quotation')
            ->join('payment as pay', 'pay.id_quotation', '=', 'q.id')
            ->join('pic as p', 'q.id_pic', '=', 'p.id')
            ->join('client as c', 'p.id_client', '=', 'c.id')
            ->whereBetween('invoice.date', [$start2, $end2])
            ->where('pay.level', '1')
            ->groupBy('pay.id')
            ->select('pay.amount', 'c.info')
            ->get();

        $unpaidInvoice1 = Quotation::with(['invoice', 'payment', 'pic.client'])
            ->whereHas('invoice', function ($q) use ($start1, $end1) {
                $q->whereBetween('date', [$start1, $end1]);
            })
            ->where('status', '100')
            ->where('level', '1')
            ->orderBy('quotation.id')
            ->get()
            ->map(function ($q) {
                $info = $q->pic->client->info ?? '-';
                $harga_total = $q->harga_total;
                $id = $q->id;
                $payments = $q->payment;

                $unpaid_amount = 0;

                if ($payments->isEmpty()) {
                    $unpaid_amount = $harga_total;
                }
                elseif ($payments->count() === 1) {
                    $p = $payments->first();
                    $unpaid_amount = $p->level == 0 ? $harga_total : 0;
                }
                else {
                    $dp = $payments->where('type', 'DP')->first();
                    $second = $payments->where('type', '!=', 'DP')->first();

                    if ($dp && $dp->level == 0) {
                        $unpaid_amount = $harga_total;
                    } elseif ($dp && $dp->level == 1 && (!$second || $second->level == 0)) {
                        $unpaid_amount = $harga_total - $dp->amount;
                    } else {
                        $unpaid_amount = 0;
                    }
                }

                return (object) [
                    'id' => $id,
                    'unpaid_amount' => $unpaid_amount,
                    'harga_total' => $harga_total,
                    'info' => $info,
                ];
            });

        $unpaidInvoice2 = Quotation::with(['invoice', 'payment', 'pic.client'])
            ->whereHas('invoice', function ($q) use ($start2, $end2) {
                $q->whereBetween('date', [$start2, $end2]);
            })
            ->where('status', '100')
            ->where('level', '1')
            ->get()
            ->map(function ($q) {
                $q->info = $q->pic->client->info ?? '-';
                $payments = $q->payment;

                if ($payments->isEmpty()) {
                    $q->unpaid_amount = $q->harga_total;
                    return $q;
                }

                if ($payments->count() === 1) {
                    $p = $payments->first();
                    $q->unpaid_amount = $p->level == 0 ? $q->harga_total : 0;
                    return $q;
                }

                if ($payments->count() >= 2) {
                    $dp = $payments->where('type', 'DP')->first();
                    $second = $payments->where('type', '!=', 'DP')->first();

                    if ($dp && $dp->level == 0) {
                        $q->unpaid_amount = $q->harga_total;
                    }
                    elseif ($dp && $dp->level == 1 && (!$second || $second->level == 0)) {
                        $q->unpaid_amount = $q->harga_total - $dp->amount;
                    }
                    else {
                        $q->unpaid_amount = 0;
                    }
                }

                return $q;
            });

        $unpaidGeneral1 = $unpaidInvoice1->sum('unpaid_amount');
        $unpaidKojisha1 = $unpaidInvoice1->where('info', 'Kojisha')->sum('unpaid_amount');
        $unpaidReftech1 = $unpaidInvoice1->where('info', 'Reftech')->sum('unpaid_amount');
        $unpaidGeneral2 = $unpaidInvoice2->sum('unpaid_amount');
        $unpaidReftech2 = $unpaidInvoice2->where('info', 'Reftech')->sum('unpaid_amount');
        $unpaidKojisha2 = $unpaidInvoice2->where('info', 'Kojisha')->sum('unpaid_amount');

        $outstandingInvoice1 = Invoice::join('quotation as q', 'q.id', '=', 'invoice.id_quotation')
            ->join('payment as pay', 'pay.id_quotation', '=', 'q.id')
            ->join('pic as p', 'q.id_pic', '=', 'p.id')
            ->join('client as c', 'p.id_client', '=', 'c.id')
            ->whereBetween('invoice.date', [$start1, $end1])
            ->where('pay.type', "Tempo")
            ->where('pay.level', 0)
            ->groupBy('pay.id')
            ->select('pay.amount', 'c.info')
            ->get();

        $outstandingInvoice2 = Invoice::join('quotation as q', 'q.id', '=', 'invoice.id_quotation')
            ->join('payment as pay', 'pay.id_quotation', '=', 'q.id')
            ->join('pic as p', 'q.id_pic', '=', 'p.id')
            ->join('client as c', 'p.id_client', '=', 'c.id')
            ->whereBetween('invoice.date', [$start2, $end2])
            ->where('pay.type', "Tempo")
            ->where('pay.level', 0)
            ->groupBy('pay.id')
            ->select('pay.amount', 'c.info')
            ->get();

        $overdueInvoice1 = Invoice::join('quotation as q', 'q.id', '=', 'invoice.id_quotation')
            ->join('payment as pay', 'pay.id_quotation', '=', 'q.id')
            ->join('pic as p', 'q.id_pic', '=', 'p.id')
            ->join('client as c', 'p.id_client', '=', 'c.id')
            ->whereBetween('invoice.date', [$start1, $end1])
            ->where('pay.type', "Tempo")
            ->where('pay.level', 0)
            ->whereDate('pay.due_date', '<=', Carbon::today())
            ->groupBy('pay.id')
            ->get();

        $overdueInvoice2 = Invoice::join('quotation as q', 'q.id', '=', 'invoice.id_quotation')
            ->join('payment as pay', 'pay.id_quotation', '=', 'q.id')
            ->join('pic as p', 'q.id_pic', '=', 'p.id')
            ->join('client as c', 'p.id_client', '=', 'c.id')
            ->whereBetween('invoice.date', [$start2, $end2])
            ->where('pay.type', "Tempo")
            ->where('pay.level', 0)
            ->whereDate('pay.due_date', '<=', Carbon::today())
            ->groupBy('pay.id')
            ->get();

        $reminder = Reminder::join('payment as p', 'p.id', '=', 'reminder.id_payment')
            ->join('quotation as q', 'q.id', '=', 'p.id_quotation')
            ->join('invoice as i', 'i.id_quotation', '=', 'q.id')
            ->join('pic as pic', 'q.id_pic', '=', 'pic.id')
            ->join('client as c', 'pic.id_client', '=', 'c.id')
            ->select('reminder.*', 'i.no_invoice', 'p.amount', 'c.company')
            ->limit(5)->get();

        $nodueCount = Payment::where('type', 'Tempo')->whereNull('due_date')->count();

        $accountingWidgets = $this->getAccountingDashboardData($requestInvoice);

        return array_merge(compact(
            'requestContract',
            'requestInvoice',
            'newCount',
            'listCount',
            'notulens',
            'deliveryCount',
            'nodueCount',
            'noSaleProspect',
            'commentAdmin',
            'unreadCommentAdmin',
            'allInvoice1',
            'allInvoice2',
            'paidInvoice1',
            'paidInvoice2',
            'unpaidGeneral1',
            'unpaidReftech1',
            'unpaidKojisha1',
            'unpaidGeneral2',
            'unpaidReftech2',
            'unpaidKojisha2',
            'outstandingInvoice1',
            'outstandingInvoice2',
            'overdueInvoice1',
            'overdueInvoice2',
            'reminder'
        ), $accountingWidgets);
    }

    public function getAccountingDashboardData(?int $requestInvoice = null): array
    {
        $dateNow = Carbon::now();
        $monthNow = $dateNow->month;
        $yearNow = $dateNow->year;

        $requestInvoice = $requestInvoice ?? (Quotation::join('pic', 'pic.id', '=', 'quotation.id_pic')
            ->join('client', 'client.id', '=', 'pic.id_client')
            ->join('invoice', 'invoice.id_quotation', '=', 'quotation.id')
            ->join('users', 'users.id', '=', 'quotation.id_sales')
            ->where('status', '100')
            ->whereNotNull('quotation.po_file')
            ->whereNull('invoice.no_invoice')
            ->count()
            + Invoice::pendingUnitRequest()->count());

        $acctMonthStart = Carbon::create($yearNow, $monthNow, 1)->startOfMonth();
        $acctMonthEnd = Carbon::create($yearNow, $monthNow, 1)->endOfMonth();
        $acctPrevMonth = Carbon::create($yearNow, $monthNow, 1)->subMonth();
        $acctPrevMonthStart = $acctPrevMonth->copy()->startOfMonth();
        $acctPrevMonthEnd = $acctPrevMonth->copy()->endOfMonth();

        $acctInvoiceBelumDibuatCount = $requestInvoice;
        $acctInvoiceBelumDibuatTotal = Quotation::join('pic', 'pic.id', '=', 'quotation.id_pic')
            ->join('client', 'client.id', '=', 'pic.id_client')
            ->join('invoice', 'invoice.id_quotation', '=', 'quotation.id')
            ->where('status', '100')
            ->whereNotNull('quotation.po_file')
            ->whereNull('invoice.no_invoice')
            ->sum('quotation.harga_total');

        $acctApUnpaidCount = ProductIn::where('accept', '0')->count();
        $acctApUnpaidTotal = ProductIn::where('accept', '0')->sum('total');
        $acctApSupplierCount = ProductIn::where('accept', '0')->distinct('supplier')->count('supplier');
        $acctApPaidMonthCount = ProductIn::where('accept', '1')
            ->whereBetween('date', [$acctMonthStart, $acctMonthEnd])->count();
        $acctApPaidMonthTotal = ProductIn::where('accept', '1')
            ->whereBetween('date', [$acctMonthStart, $acctMonthEnd])->sum('total');
        $acctApTotalAll = ProductIn::count();
        $acctApPaidTotalAll = ProductIn::where('accept', '1')->count();

        $acctFixedAssetMonthCount = FixedAsset::whereBetween('created_at', [$acctMonthStart, $acctMonthEnd])->count();
        $acctFixedAssetMonthTotal = FixedAsset::whereBetween('created_at', [$acctMonthStart, $acctMonthEnd])->sum('total');

        $acctTempoPayments = Payment::where('type', 'Tempo')->where('level', 0)->whereNotNull('due_date')->get(['amount', 'due_date']);
        $acctArAgingBuckets = ['current' => 0, '1_30' => 0, '31_60' => 0, 'over_60' => 0];
        $acctToday = Carbon::today();
        foreach ($acctTempoPayments as $tp) {
            $diff = $acctToday->diffInDays(Carbon::parse($tp->due_date), false);
            if ($diff > 0) {
                $acctArAgingBuckets['current'] += $tp->amount;
            } elseif ($diff >= -30) {
                $acctArAgingBuckets['1_30'] += $tp->amount;
            } elseif ($diff >= -60) {
                $acctArAgingBuckets['31_60'] += $tp->amount;
            } else {
                $acctArAgingBuckets['over_60'] += $tp->amount;
            }
        }
        $acctArOutstanding = array_sum($acctArAgingBuckets);
        $acctArCustomerCount = Payment::join('quotation as q', 'q.id', '=', 'payment.id_quotation')
            ->join('pic as p', 'p.id', '=', 'q.id_pic')
            ->where('payment.type', 'Tempo')
            ->where('payment.level', 0)
            ->distinct('p.id_client')
            ->count('p.id_client');

        $acctExpenseMonth = DetailExpense::join('expense as e', 'e.id', '=', 'detail_expense.id_expense')
            ->whereBetween('e.date', [$acctMonthStart, $acctMonthEnd])->sum('detail_expense.amount');
        $acctExpensePrevMonth = DetailExpense::join('expense as e', 'e.id', '=', 'detail_expense.id_expense')
            ->whereBetween('e.date', [$acctPrevMonthStart, $acctPrevMonthEnd])->sum('detail_expense.amount');
        $acctExpenseChangePct = $acctExpensePrevMonth > 0
            ? round((($acctExpenseMonth - $acctExpensePrevMonth) / $acctExpensePrevMonth) * 100, 1)
            : 0;

        $acctCogsMonth = Quotation::join('detail_quotation', 'quotation.id', '=', 'detail_quotation.id_quotation')
            ->join('serial_product', 'detail_quotation.id_equivalent', '=', 'serial_product.id')
            ->whereBetween('quotation.po_date', [$acctMonthStart->toDateString(), $acctMonthEnd->toDateString()])
            ->where('quotation.status', '100')->where('quotation.level', '1')->where('quotation.is_primary', '1')
            ->sum('serial_product.price');
        $acctCogsPrevMonth = Quotation::join('detail_quotation', 'quotation.id', '=', 'detail_quotation.id_quotation')
            ->join('serial_product', 'detail_quotation.id_equivalent', '=', 'serial_product.id')
            ->whereBetween('quotation.po_date', [$acctPrevMonthStart->toDateString(), $acctPrevMonthEnd->toDateString()])
            ->where('quotation.status', '100')->where('quotation.level', '1')->where('quotation.is_primary', '1')
            ->sum('serial_product.price');
        $acctCogsChangePct = $acctCogsPrevMonth > 0
            ? round((($acctCogsMonth - $acctCogsPrevMonth) / $acctCogsPrevMonth) * 100, 1)
            : 0;

        $acctExpenseByAccount = DetailExpense::join('expense as e', 'e.id', '=', 'detail_expense.id_expense')
            ->join('account as a', 'a.id', '=', 'detail_expense.id_account')
            ->whereBetween('e.date', [$acctMonthStart, $acctMonthEnd])
            ->select('a.name', DB::raw('SUM(detail_expense.amount) as total'))
            ->groupBy('a.name')
            ->orderByDesc('total')
            ->limit(6)
            ->get();

        $acctRecentSupplierBill = ProductIn::orderByDesc('date')->limit(5)
            ->get(['id', 'no_product_in', 'supplier', 'total', 'date', 'accept']);
        $acctRecentCustomerInvoice = Invoice::join('quotation as q', 'q.id', '=', 'invoice.id_quotation')
            ->join('pic as p', 'p.id', '=', 'q.id_pic')
            ->join('client as c', 'c.id', '=', 'p.id_client')
            ->orderByDesc('invoice.date')
            ->limit(5)
            ->get(['invoice.id', 'invoice.no_invoice', 'invoice.date', 'q.harga_total', 'c.company']);
        $acctRecentExpense = Expense::orderByDesc('date')->limit(5)->get(['id', 'no_expense', 'date', 'amount', 'memo']);
        $acctRecentFixedAsset = FixedAsset::orderByDesc('created_at')->limit(5)->get(['id', 'code', 'desc', 'total', 'created_at']);

        return compact(
            'acctInvoiceBelumDibuatCount',
            'acctInvoiceBelumDibuatTotal',
            'acctApUnpaidCount',
            'acctApUnpaidTotal',
            'acctApSupplierCount',
            'acctApPaidMonthCount',
            'acctApPaidMonthTotal',
            'acctApTotalAll',
            'acctApPaidTotalAll',
            'acctFixedAssetMonthCount',
            'acctFixedAssetMonthTotal',
            'acctArAgingBuckets',
            'acctArOutstanding',
            'acctArCustomerCount',
            'acctTempoPayments',
            'acctExpenseMonth',
            'acctExpenseChangePct',
            'acctCogsMonth',
            'acctCogsChangePct',
            'acctExpenseByAccount',
            'acctRecentSupplierBill',
            'acctRecentCustomerInvoice',
            'acctRecentExpense',
            'acctRecentFixedAsset',
        );
    }
}
