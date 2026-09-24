<?php

namespace App\Services\Dashboard;

use App\Models\Payment;
use App\Models\Quotation;
use App\Models\DetailExpense;
use App\Models\LabaRugi;
use App\Models\SalesTargetHistory;
use App\Models\Invoice;
use App\Models\Expense;
use App\Models\Client;
use App\Models\ProductIn;
use App\Models\Bank;
use App\Models\ExpenseBudget;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;

class FinanceDashboardService
{
    /**
     * Get dashboard data payload for Finance Manager role
     */
    public function getDashboardData($notulens)
    {
        $financeWidgets = $this->getFinanceDashboardData();

        return array_merge(compact('notulens'), $financeWidgets);
    }

    public function getFinanceDashboardData(): array
    {
        $dateNow = Carbon::now();
        $monthNow = $dateNow->month;
        $yearNow = $dateNow->year;

        $startYear = Carbon::create($yearNow, 1, 1)->startOfYear();
        $startMonth = Carbon::create($yearNow, $monthNow, 1)->startOfMonth();
        $endMonth = Carbon::create($yearNow, $monthNow, 1)->endOfMonth();

        // Aging Receivable: payment Tempo yang belum lunas (level=0) dengan due_date
        $tempoPayments = Payment::where('type', 'Tempo')
            ->where('level', 0)
            ->whereNotNull('due_date')
            ->get(['amount', 'due_date']);

        $financeAgingBuckets = ['current' => 0, '1_30' => 0, '31_60' => 0, 'over_60' => 0];
        $todayDate = Carbon::today();
        foreach ($tempoPayments as $tp) {
            $diff = $todayDate->diffInDays(Carbon::parse($tp->due_date), false);
            if ($diff > 0) {
                $financeAgingBuckets['current'] += $tp->amount;
            } elseif ($diff >= -30) {
                $financeAgingBuckets['1_30'] += $tp->amount;
            } elseif ($diff >= -60) {
                $financeAgingBuckets['31_60'] += $tp->amount;
            } else {
                $financeAgingBuckets['over_60'] += $tp->amount;
            }
        }
        $financeOutstandingAR = array_sum($financeAgingBuckets);

        // Cash Position: total likuiditas kas & bank aktif dan pemisahan entitas Reftech vs Kojisha
        $banks = Bank::where('is_active', 1)->orderByDesc('saldo')->get();
        $financeCashPosition = (float) $banks->sum('saldo');
        $financeActiveBankCount = (int) $banks->count();

        $financeBankReftech = $banks->filter(function ($b) {
            return strtolower($b->entity ?? 'reftech') === 'reftech';
        })->values();

        $financeBankKojisha = $banks->filter(function ($b) {
            return strtolower($b->entity ?? '') === 'kojisha';
        })->values();

        $financeCashReftech = (float) $financeBankReftech->sum('saldo');
        $financeCashKojisha = (float) $financeBankKojisha->sum('saldo');

        $financeBankChartLabels = [];
        $financeBankChartSeries = [];
        foreach ($banks as $b) {
            $entityName = strtoupper($b->entity ?: 'REFTECH');
            $noRekShort = $b->no_rek ? ' (' . substr($b->no_rek, -4) . ')' : '';
            $financeBankChartLabels[] = "{$b->bank} - {$entityName}{$noRekShort}";
            $financeBankChartSeries[] = (float) $b->saldo;
        }

        // Aging Payable: tagihan supplier belum lunas (accept IN ('0', '2')) dengan nomor invoice
        $baseAP = ProductIn::whereIn('accept', ['0', '2'])->whereNotNull('invoice');
        $apToday = now()->toDateString();
        $financeAgingPayableBuckets = [
            'current' => (float) $baseAP->clone()->whereRaw('DATEDIFF(?, COALESCE(date_invoice, date)) BETWEEN 0 AND 30', [$apToday])->sum('total'),
            '31_60'   => (float) $baseAP->clone()->whereRaw('DATEDIFF(?, COALESCE(date_invoice, date)) BETWEEN 31 AND 60', [$apToday])->sum('total'),
            '61_90'   => (float) $baseAP->clone()->whereRaw('DATEDIFF(?, COALESCE(date_invoice, date)) BETWEEN 61 AND 90', [$apToday])->sum('total'),
            'over_90' => (float) $baseAP->clone()->whereRaw('DATEDIFF(?, COALESCE(date_invoice, date)) > 90', [$apToday])->sum('total'),
        ];
        $financeOutstandingAP = array_sum($financeAgingPayableBuckets);

        // Daily Welcome Alert for Finance
        $showFinanceWelcomeAlert = false;
        $financeUserId = Auth::id();
        if ($financeUserId) {
            $welcomeAlertKey = 'finance_welcome_alert_' . $financeUserId . '_' . $todayDate->toDateString();
            if (!Cache::has($welcomeAlertKey)) {
                Cache::put($welcomeAlertKey, true, $todayDate->copy()->endOfDay());
                $showFinanceWelcomeAlert = true;
            }
        }

        // Tagihan Supplier (AP) Overdue: lebih dari 30 hari / lewat jatuh tempo
        $baseAPOverdue = ProductIn::whereIn('accept', ['0', '2'])
            ->whereNotNull('invoice')
            ->whereRaw('DATEDIFF(?, COALESCE(date_invoice, date)) > 30', [$apToday]);
        $financeApOverdueCount = (int) (clone $baseAPOverdue)->count();
        $financeApOverdueNominal = (float) (clone $baseAPOverdue)->sum('total');

        // Piutang Pelanggan (AR) Overdue: Tempo level=0 dengan due_date <= hari ini
        $spOverdueQuery = Payment::join('quotation as q', 'q.id', '=', 'payment.id_quotation')
            ->where('payment.type', 'Tempo')
            ->where('payment.level', 0)
            ->whereNotNull('payment.due_date')
            ->whereDate('payment.due_date', '<=', $todayDate);

        $uqOverdueQuery = Payment::join('unit_quotation as uq', 'uq.id', '=', 'payment.id_unit_quotation')
            ->where('payment.type', 'Tempo')
            ->where('payment.level', 0)
            ->whereNotNull('payment.due_date')
            ->whereDate('payment.due_date', '<=', $todayDate);

        $financeArOverdueCount = (int) ((clone $spOverdueQuery)->count() + (clone $uqOverdueQuery)->count());
        $financeArOverdueNominal = (float) ((clone $spOverdueQuery)->sum('payment.amount') + (clone $uqOverdueQuery)->sum('payment.amount'));

        // Revenue / COGS / Expense bulan berjalan
        $financeRevenueMonth = Quotation::whereBetween('po_date', [$startMonth->toDateString(), $endMonth->toDateString()])
            ->where('status', '100')->where('level', '1')->where('is_primary', '1')->sum('nett');
        $financeExpenseMonth = DetailExpense::join('expense as e', 'e.id', '=', 'detail_expense.id_expense')
            ->whereBetween('e.date', [$startMonth->toDateString(), $endMonth->toDateString()])
            ->sum('detail_expense.amount');

        // Revenue / COGS / Expense YTD (untuk Profit Summary & Net Profit KPI)
        $financeRevenueYTD = Quotation::whereBetween('po_date', [$startYear->toDateString(), $dateNow->toDateString()])
            ->where('status', '100')->where('level', '1')->where('is_primary', '1')->sum('nett');
        $financeCOGSYTD = Quotation::join('detail_quotation', 'quotation.id', '=', 'detail_quotation.id_quotation')
            ->join('serial_product', 'detail_quotation.id_equivalent', '=', 'serial_product.id')
            ->whereBetween('quotation.po_date', [$startYear->toDateString(), $dateNow->toDateString()])
            ->where('quotation.status', '100')->where('quotation.level', '1')->where('quotation.is_primary', '1')
            ->sum('serial_product.price');
        $financeExpenseYTD = DetailExpense::join('expense as e', 'e.id', '=', 'detail_expense.id_expense')
            ->whereBetween('e.date', [$startYear->toDateString(), $dateNow->toDateString()])
            ->sum('detail_expense.amount');
        $financeOtherIncomeYTD = LabaRugi::whereBetween('date', [$startYear->toDateString(), $dateNow->toDateString()])
            ->where('type', 'Pendapatan Lain')->sum('amount');
        $financeOtherChargeYTD = LabaRugi::whereBetween('date', [$startYear->toDateString(), $dateNow->toDateString()])
            ->where('type', 'Beban Lain')->sum('amount');

        $financeGrossProfitYTD = $financeRevenueYTD - $financeCOGSYTD;
        $financeNetProfitYTD = $financeGrossProfitYTD - $financeExpenseYTD + $financeOtherIncomeYTD - $financeOtherChargeYTD;
        $financeMarginYTD = $financeRevenueYTD > 0 ? round($financeNetProfitYTD / $financeRevenueYTD * 100, 1) : 0;

        // Revenue Target: dari fitur Sales Target (sales_target_histories), target tahunan dibagi rata 12 bulan
        $financeAnnualTarget  = SalesTargetHistory::where('year', $yearNow)->sum('target_annual');
        $financeMonthlyTarget = $financeAnnualTarget > 0 ? (int) round($financeAnnualTarget / 12) : 0;
        $financeRevenueAchievement = $financeMonthlyTarget > 0 ? round($financeRevenueMonth / $financeMonthlyTarget * 100, 1) : 0;

        // Revenue & Expense per bulan (Jan..bulan berjalan) untuk line chart
        $yearStart = Carbon::create($yearNow, 1, 1)->startOfMonth()->toDateString();
        $yearToDateEnd = Carbon::create($yearNow, $monthNow, 1)->endOfMonth()->toDateString();

        $revenueByMonth = Quotation::whereBetween('po_date', [$yearStart, $yearToDateEnd])
            ->where('status', '100')->where('level', '1')->where('is_primary', '1')
            ->selectRaw('MONTH(po_date) as m, SUM(nett) as total')
            ->groupBy('m')
            ->pluck('total', 'm');

        $expenseByMonth = DetailExpense::join('expense as e', 'e.id', '=', 'detail_expense.id_expense')
            ->whereBetween('e.date', [$yearStart, $yearToDateEnd])
            ->selectRaw('MONTH(e.date) as m, SUM(detail_expense.amount) as total')
            ->groupBy('m')
            ->pluck('total', 'm');

        // Expense Budget: dari modul ExpenseBudget
        $expenseBudgetRecord = ExpenseBudget::where('year', $yearNow)->where('entity', 'all')->first()
            ?? ExpenseBudget::where('year', $yearNow)->first();
        $financeMonthlyBudget = $expenseBudgetRecord ? (int) $expenseBudgetRecord->getBudgetForMonth($monthNow) : 0;
        $financeAnnualBudget  = $expenseBudgetRecord ? (int) $expenseBudgetRecord->annual_budget : 0;
        $financeExpenseBudgetAchievement = $financeMonthlyBudget > 0 ? round(($financeExpenseMonth / $financeMonthlyBudget) * 100, 1) : 0;

        $financeMonthlyLabels = [];
        $financeMonthlyRevenue = [];
        $financeMonthlyExpense = [];
        $financeMonthlyTargetSeries = [];
        $financeMonthlyBudgetSeries = [];
        for ($m = 1; $m <= $monthNow; $m++) {
            $financeMonthlyLabels[] = Carbon::create($yearNow, $m, 1)->translatedFormat('M');
            $financeMonthlyRevenue[] = (int) $revenueByMonth->get($m, 0);
            $financeMonthlyExpense[] = (int) $expenseByMonth->get($m, 0);
            $financeMonthlyTargetSeries[] = $financeMonthlyTarget;
            $financeMonthlyBudgetSeries[] = $expenseBudgetRecord ? (int) $expenseBudgetRecord->getBudgetForMonth($m) : 0;
        }

        // Recent activity: gabungan Invoice, Payment, Expense terbaru
        $financeRecentInvoice = Invoice::join('quotation as q', 'q.id', '=', 'invoice.id_quotation')
            ->orderBy('invoice.date', 'desc')
            ->limit(5)
            ->get(['invoice.id as doc_id', 'invoice.no_invoice as ref', 'invoice.date as tanggal', 'q.nett as nominal', DB::raw("'Invoice' as tipe")]);
        $financeRecentPayment = Payment::orderBy('date', 'desc')
            ->limit(5)
            ->get(['payment.id as doc_id', DB::raw("CONCAT('PAY-', payment.id) as ref"), 'date as tanggal', 'amount as nominal', DB::raw("'Payment' as tipe")]);
        $financeRecentExpense = Expense::orderBy('date', 'desc')
            ->limit(5)
            ->get(['expense.id as doc_id', 'no_expense as ref', 'date as tanggal', 'amount as nominal', DB::raw("'Expense' as tipe")]);
        $financeRecentActivity = $financeRecentInvoice
            ->concat($financeRecentPayment)
            ->concat($financeRecentExpense)
            ->sortByDesc('tanggal')
            ->take(10)
            ->values();

        // Key Accounts removed per user request
        $financeKeyAccounts = collect();

        return compact(
            'financeCashPosition',
            'financeActiveBankCount',
            'financeCashReftech',
            'financeCashKojisha',
            'financeBankReftech',
            'financeBankKojisha',
            'financeBankChartLabels',
            'financeBankChartSeries',
            'financeAgingBuckets',
            'financeAgingPayableBuckets',
            'financeOutstandingAR',
            'financeOutstandingAP',
            'financeRevenueMonth',
            'financeExpenseMonth',
            'financeRevenueYTD',
            'financeCOGSYTD',
            'financeExpenseYTD',
            'financeGrossProfitYTD',
            'financeNetProfitYTD',
            'financeMarginYTD',
            'financeAnnualTarget',
            'financeMonthlyTarget',
            'financeRevenueAchievement',
            'financeMonthlyLabels',
            'financeMonthlyRevenue',
            'financeMonthlyExpense',
            'financeMonthlyTargetSeries',
            'financeMonthlyBudget',
            'financeAnnualBudget',
            'financeExpenseBudgetAchievement',
            'financeMonthlyBudgetSeries',
            'financeRecentActivity',
            'financeKeyAccounts',
            'showFinanceWelcomeAlert',
            'financeApOverdueCount',
            'financeApOverdueNominal',
            'financeArOverdueCount',
            'financeArOverdueNominal',
        );
    }
}
