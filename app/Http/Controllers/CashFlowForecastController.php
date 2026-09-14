<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use App\Models\ExpenseBudget;
use App\Models\Payment;
use App\Models\ProductIn;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;

class CashFlowForecastController extends Controller
{
    /**
     * Display Cash Flow Projection & Forecast.
     */
    public function index(Request $request)
    {
        $horizonDays = (int) $request->get('horizon', 60); // 30, 60, or 90 days
        $today = Carbon::today();
        $endDate = Carbon::today()->addDays($horizonDays);

        // 1. Current Liquid Cash & Bank Position
        $bankAccounts = Bank::where('is_active', 1)->orderBy('bank')->get();
        $currentLiquidCash = (float) $bankAccounts->sum('saldo');

        // 2. Projected Inflows (Unpaid AR by Due Date)
        // Sparepart Inflows
        $spInflows = Payment::join('quotation as q', 'q.id', '=', 'payment.id_quotation')
            ->join('pic', 'pic.id', '=', 'q.id_pic')
            ->join('client as c', 'c.id', '=', 'pic.id_client')
            ->where('payment.level', 0)
            ->where('payment.amount', '>', 0)
            ->select([
                'payment.id',
                'payment.amount',
                'payment.due_date',
                'payment.date as invoice_date',
                'payment.method',
                'payment.type',
                'c.company as partner_name',
                'q.no_quote as ref_number',
            ])
            ->get();

        // Unit Inflows
        $uqInflows = Payment::join('unit_quotation as uq', 'uq.id', '=', 'payment.id_unit_quotation')
            ->join('client as c', 'c.id', '=', 'uq.id_client')
            ->where('payment.level', 0)
            ->where('payment.amount', '>', 0)
            ->select([
                'payment.id',
                'payment.amount',
                'payment.due_date',
                'payment.date as invoice_date',
                'payment.method',
                'payment.type',
                'c.company as partner_name',
                'uq.no_quote as ref_number',
            ])
            ->get();

        $allInflows = $spInflows->concat($uqInflows)->map(function ($item) use ($today) {
            $dueDate = $item->due_date ? Carbon::parse($item->due_date) : $today;
            $item->target_date = $dueDate->isPast() ? $today->toDateString() : $dueDate->toDateString();
            $item->is_overdue = $dueDate->isPast() && !$dueDate->isToday();
            $item->category = 'Piutang AR (Sales)';
            return $item;
        });

        // 3. Projected Outflows (Unpaid AP / Purchase Invoices)
        // Unpaid ProductIn
        $apOutflows = ProductIn::leftJoin('supplier as s', 's.id', '=', 'product_in.id_supplier')
            ->where('product_in.accept', '0')
            ->where('product_in.total', '>', 0)
            ->select([
                'product_in.id',
                'product_in.total as amount',
                'product_in.date_payment as due_date',
                'product_in.date as invoice_date',
                'product_in.invoice as ref_number',
                DB::raw("COALESCE(s.supplier, product_in.supplier, 'Supplier') as partner_name"),
            ])
            ->get()
            ->map(function ($item) use ($today) {
                // If date_payment is empty, assume 30 days from invoice date
                $dueDate = $item->due_date 
                    ? Carbon::parse($item->due_date) 
                    : ($item->invoice_date ? Carbon::parse($item->invoice_date)->addDays(30) : $today);
                
                $item->target_date = $dueDate->isPast() ? $today->toDateString() : $dueDate->toDateString();
                $item->is_overdue = $dueDate->isPast() && !$dueDate->isToday();
                $item->category = 'Hutang AP (Supplier)';
                return $item;
            });

        // 4. Monthly OPEX Budget Allocation (Divided into Weekly recurring obligations)
        $currentYear = $today->year;
        $budgetSum = (float) ExpenseBudget::where('year', $currentYear)->sum('annual_budget');
        $weeklyOpex = $budgetSum > 0 ? ($budgetSum / 52) : (50000000 / 4); // fallback 12.5jt/week if no budget

        // 5. Aggregate weekly projection buckets
        $numWeeks = (int) ceil($horizonDays / 7);
        $weeklyBuckets = [];
        $runningCash = $currentLiquidCash;

        $chartCategories = [];
        $chartInflows = [];
        $chartOutflows = [];
        $chartBalances = [];

        $totalProjectedInflow = 0;
        $totalProjectedOutflow = 0;

        for ($w = 0; $w < $numWeeks; $w++) {
            $wStart = $today->copy()->addDays($w * 7);
            $wEnd = $wStart->copy()->addDays(6);

            $wStartStr = $wStart->toDateString();
            $wEndStr = $wEnd->toDateString();

            $wInflow = (float) $allInflows->filter(function ($item) use ($wStartStr, $wEndStr) {
                return $item->target_date >= $wStartStr && $item->target_date <= $wEndStr;
            })->sum('amount');

            $wApOutflow = (float) $apOutflows->filter(function ($item) use ($wStartStr, $wEndStr) {
                return $item->target_date >= $wStartStr && $item->target_date <= $wEndStr;
            })->sum('amount');

            $wOutflow = $wApOutflow + $weeklyOpex;

            $totalProjectedInflow += $wInflow;
            $totalProjectedOutflow += $wOutflow;

            $runningCash = $runningCash + $wInflow - $wOutflow;

            $label = ($w === 0 ? 'Mgg 1 (Saat ini)' : 'Mgg ' . ($w + 1)) . ' (' . $wStart->format('d/m') . ')';

            $chartCategories[] = $label;
            $chartInflows[] = (int) $wInflow;
            $chartOutflows[] = (int) $wOutflow;
            $chartBalances[] = (int) $runningCash;

            $weeklyBuckets[] = [
                'week_number' => $w + 1,
                'label' => $label,
                'period' => $wStart->format('d M') . ' - ' . $wEnd->format('d M Y'),
                'inflow' => $wInflow,
                'outflow' => $wOutflow,
                'opex' => $weeklyOpex,
                'ap' => $wApOutflow,
                'net' => $wInflow - $wOutflow,
                'ending_cash' => $runningCash,
            ];
        }

        $finalProjectedCash = $runningCash;

        return view('pages.finance.cashflow-forecast.index', compact(
            'horizonDays',
            'currentLiquidCash',
            'bankAccounts',
            'totalProjectedInflow',
            'totalProjectedOutflow',
            'finalProjectedCash',
            'weeklyBuckets',
            'allInflows',
            'apOutflows',
            'chartCategories',
            'chartInflows',
            'chartOutflows',
            'chartBalances'
        ));
    }
}
