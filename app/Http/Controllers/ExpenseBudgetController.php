<?php

namespace App\Http\Controllers;

use App\Models\DetailExpense;
use App\Models\Expense;
use App\Models\ExpenseBudget;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ExpenseBudgetController extends Controller
{
    /**
     * Display the Expense Budget dashboard, monthly comparison,
     * category breakdown, and multi-year budget history.
     */
    public function index(Request $request)
    {
        $currentYear = (int) date('Y');
        $selectedYear = (int) ($request->get('year', $currentYear));
        $selectedEntity = $request->get('entity', 'all');

        // Available years (from expense records + budget records + current year)
        $expenseYears = DB::table('expense')
            ->selectRaw('YEAR(date) as y')
            ->whereNotNull('date')
            ->distinct()
            ->pluck('y')
            ->toArray();

        $budgetYears = ExpenseBudget::select('year')->distinct()->pluck('year')->toArray();

        $allYears = array_unique(array_merge([$currentYear, $currentYear + 1], $expenseYears, $budgetYears));
        rsort($allYears);

        // Fetch current budget record
        $budget = ExpenseBudget::with('creator')
            ->where('year', $selectedYear)
            ->where('entity', $selectedEntity)
            ->first();

        // Calculate Monthly Actual, Budget, Variance for the selected year
        $monthlyRows = [];
        $totalActualYear = 0;
        $totalBudgetYear = $budget ? (float) $budget->annual_budget : 0;
        $now = Carbon::now();

        for ($m = 1; $m <= 12; $m++) {
            $mCarbon = Carbon::create($selectedYear, $m, 1);
            $mStart = $mCarbon->copy()->startOfMonth()->toDateString();
            $mEnd = $mCarbon->copy()->endOfMonth()->toDateString();

            $actualQuery = DetailExpense::join('expense as e', 'e.id', '=', 'detail_expense.id_expense')
                ->leftJoin('bank as b', 'b.id', '=', 'e.id_bank')
                ->whereBetween('e.date', [$mStart, $mEnd]);

            if ($selectedEntity !== 'all') {
                $actualQuery->where('b.entity', $selectedEntity);
            }

            $actual = (float) $actualQuery->sum('detail_expense.amount');
            $txCount = (int) $actualQuery->count('detail_expense.id');

            $monthBudget = $budget ? $budget->getBudgetForMonth($m) : 0;
            $variance = $monthBudget - $actual; // Positif = hemat/sisa, Negatif = overbudget
            $pctUsed = $monthBudget > 0 ? round(($actual / $monthBudget) * 100, 1) : 0;

            $isPassedOrCurrent = $selectedYear < $now->year || ($selectedYear == $now->year && $m <= $now->month);

            if ($isPassedOrCurrent) {
                $totalActualYear += $actual;
            }

            $monthlyRows[$m] = [
                'month_num'    => $m,
                'month_name'   => $mCarbon->translatedFormat('F'),
                'budget'       => $monthBudget,
                'actual'       => $actual,
                'tx_count'     => $txCount,
                'variance'     => $variance,
                'pct_used'     => $pctUsed,
                'is_current'   => ($selectedYear == $now->year && $m == $now->month),
                'is_future'    => ($selectedYear == $now->year && $m > $now->month) || ($selectedYear > $now->year),
                'status'       => $monthBudget > 0
                    ? ($actual > $monthBudget ? 'over' : ($pctUsed >= 85 ? 'warning' : 'safe'))
                    : ($actual > 0 ? 'no_budget' : 'empty'),
            ];
        }

        $remainingBudget = $totalBudgetYear - $totalActualYear;
        $overallPctUsed = $totalBudgetYear > 0 ? round(($totalActualYear / $totalBudgetYear) * 100, 1) : 0;

        // -------------------------------------------------------------
        // 1. Rincian Belanja per Kategori Akun (Expense Category / COA)
        // -------------------------------------------------------------
        $catQuery = DetailExpense::join('expense as e', 'e.id', '=', 'detail_expense.id_expense')
            ->leftJoin('account as a', 'a.id', '=', 'detail_expense.id_account')
            ->leftJoin('bank as b', 'b.id', '=', 'e.id_bank')
            ->whereYear('e.date', $selectedYear);

        if ($selectedEntity !== 'all') {
            $catQuery->where('b.entity', $selectedEntity);
        }

        $categoryBreakdown = $catQuery
            ->select(
                'a.id as account_id',
                'a.code as account_code',
                'a.name as account_name',
                'a.category as account_category',
                DB::raw('SUM(detail_expense.amount) as total_amount'),
                DB::raw('COUNT(detail_expense.id) as tx_count')
            )
            ->groupBy('a.id', 'a.code', 'a.name', 'a.category')
            ->orderByDesc('total_amount')
            ->get()
            ->map(function ($cat) use ($totalActualYear) {
                $cat->pct_of_total = $totalActualYear > 0 ? round(($cat->total_amount / $totalActualYear) * 100, 1) : 0;
                return $cat;
            });

        // -------------------------------------------------------------
        // 2. Sub-Departemen / Departmental Budget Breakdown & Realization
        // -------------------------------------------------------------
        $departmentDefs = ExpenseBudget::getDepartmentDefinitions();
        $departmentStats = [];

        foreach ($departmentDefs as $key => $def) {
            $departmentStats[$key] = array_merge($def, [
                'budget_annual'  => 0,
                'budget_monthly' => 0,
                'actual'         => 0,
                'tx_count'       => 0,
                'variance'       => 0,
                'pct_used'       => 0,
                'pct_of_total'   => 0,
                'status'         => 'empty',
                'accounts'       => [],
            ]);

            if ($budget) {
                $deptBudget = $budget->getDepartmentBudget($key);
                $departmentStats[$key]['budget_annual'] = (float) ($deptBudget['annual'] ?? 0);
                $departmentStats[$key]['budget_monthly'] = (float) ($deptBudget['monthly'] ?? 0);
            }
        }

        // Map category accounts into departments
        foreach ($categoryBreakdown as $cat) {
            $deptKey = ExpenseBudget::mapAccountToDepartment($cat->account_code, $cat->account_name);
            if (!isset($departmentStats[$deptKey])) {
                $deptKey = 'operasional';
            }
            $cat->dept_key = $deptKey;
            $cat->dept_name = $departmentDefs[$deptKey]['name'] ?? 'Operasional';

            $departmentStats[$deptKey]['actual'] += (float) $cat->total_amount;
            $departmentStats[$deptKey]['tx_count'] += (int) $cat->tx_count;
            $departmentStats[$deptKey]['accounts'][] = $cat;
        }

        // Compute variances, pct, status for each department
        $totalDeptBudgetYear = 0;
        foreach ($departmentStats as $key => &$dStat) {
            $bAnn = $dStat['budget_annual'];
            $act = $dStat['actual'];
            $totalDeptBudgetYear += $bAnn;

            $dStat['variance'] = $bAnn - $act;
            $dStat['pct_used'] = $bAnn > 0 ? round(($act / $bAnn) * 100, 1) : 0;
            $dStat['pct_of_total'] = $totalActualYear > 0 ? round(($act / $totalActualYear) * 100, 1) : 0;

            if ($bAnn > 0) {
                if ($act > $bAnn) {
                    $dStat['status'] = 'over';
                } elseif ($dStat['pct_used'] >= 85) {
                    $dStat['status'] = 'warning';
                } else {
                    $dStat['status'] = 'safe';
                }
            } elseif ($act > 0) {
                $dStat['status'] = 'no_budget';
            } else {
                $dStat['status'] = 'empty';
            }
        }
        unset($dStat);

        // -------------------------------------------------------------
        // 3. Data History Budget Tahunan (Multi-Year Budget History)
        // -------------------------------------------------------------
        $historyYearsData = [];
        foreach ($allYears as $yearItem) {
            $histBudget = ExpenseBudget::with('creator')
                ->where('year', $yearItem)
                ->where('entity', $selectedEntity)
                ->first();

            $histActualQuery = DetailExpense::join('expense as e', 'e.id', '=', 'detail_expense.id_expense')
                ->leftJoin('bank as b', 'b.id', '=', 'e.id_bank')
                ->whereYear('e.date', $yearItem);

            if ($selectedEntity !== 'all') {
                $histActualQuery->where('b.entity', $selectedEntity);
            }

            $histActual = (float) $histActualQuery->sum('detail_expense.amount');
            $histTxCount = (int) $histActualQuery->count('detail_expense.id');
            $histCeiling = $histBudget ? (float) $histBudget->annual_budget : 0;
            $histVariance = $histCeiling - $histActual;
            $histPctUsed = $histCeiling > 0 ? round(($histActual / $histCeiling) * 100, 1) : 0;

            $histStatus = 'empty';
            if ($histCeiling > 0) {
                if ($histActual > $histCeiling) {
                    $histStatus = 'over';
                } elseif ($histPctUsed >= 85) {
                    $histStatus = 'warning';
                } else {
                    $histStatus = 'safe';
                }
            } elseif ($histActual > 0) {
                $histStatus = 'no_budget';
            }

            $historyYearsData[] = [
                'year'         => $yearItem,
                'entity'       => $selectedEntity,
                'budget'       => $histBudget,
                'ceiling'      => $histCeiling,
                'actual'       => $histActual,
                'tx_count'     => $histTxCount,
                'variance'     => $histVariance,
                'pct_used'     => $histPctUsed,
                'status'       => $histStatus,
                'notes'        => $histBudget?->notes,
                'updated_at'   => $histBudget?->updated_at,
                'creator_name' => $histBudget?->creator?->name ?? 'Sistem',
                'is_selected'  => ($yearItem == $selectedYear),
            ];
        }

        // -------------------------------------------------------------
        // 4. Chart Series Data Preparation
        // -------------------------------------------------------------
        $chartMonthlyLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        $chartBudgetSeries = array_values(array_column($monthlyRows, 'budget'));
        $chartActualSeries = array_values(array_column($monthlyRows, 'actual'));

        // Donut Chart: Department Series
        $chartDeptLabels = [];
        $chartDeptSeries = [];
        $chartDeptColors = [];
        foreach ($departmentStats as $dKey => $dVal) {
            if ($dVal['actual'] > 0) {
                $chartDeptLabels[] = $dVal['short'];
                $chartDeptSeries[] = (float) $dVal['actual'];
                $chartDeptColors[] = $dVal['color'];
            }
        }

        // Top 5 Categories for detail view
        $topCategories = $categoryBreakdown->take(5);
        $chartCatLabels = [];
        $chartCatSeries = [];
        foreach ($topCategories as $tc) {
            $chartCatLabels[] = $tc->account_name;
            $chartCatSeries[] = (float) $tc->total_amount;
        }
        if ($categoryBreakdown->count() > 5) {
            $otherAmount = (float) $categoryBreakdown->slice(5)->sum('total_amount');
            if ($otherAmount > 0) {
                $chartCatLabels[] = 'Lain-lain';
                $chartCatSeries[] = $otherAmount;
            }
        }

        // Multi-Year History Bar Chart (in ascending order)
        $sortedHist = array_reverse($historyYearsData);
        $chartHistoryYears = array_values(array_column($sortedHist, 'year'));
        $chartHistoryBudgets = array_values(array_column($sortedHist, 'ceiling'));
        $chartHistoryActuals = array_values(array_column($sortedHist, 'actual'));

        return view('pages.finance.budget.index', compact(
            'allYears',
            'selectedYear',
            'selectedEntity',
            'budget',
            'monthlyRows',
            'totalBudgetYear',
            'totalActualYear',
            'remainingBudget',
            'overallPctUsed',
            'categoryBreakdown',
            'departmentDefs',
            'departmentStats',
            'totalDeptBudgetYear',
            'historyYearsData',
            'chartMonthlyLabels',
            'chartBudgetSeries',
            'chartActualSeries',
            'chartDeptLabels',
            'chartDeptSeries',
            'chartDeptColors',
            'chartCatLabels',
            'chartCatSeries',
            'chartHistoryYears',
            'chartHistoryBudgets',
            'chartHistoryActuals'
        ));
    }

    /**
     * AJAX endpoint to fetch monthly expense transaction drilldown details.
     */
    public function monthlyDetails(Request $request)
    {
        $year = (int) $request->get('year', date('Y'));
        $month = (int) $request->get('month', date('n'));
        $entity = $request->get('entity', 'all');

        $mCarbon = Carbon::create($year, $month, 1);
        $mStart = $mCarbon->copy()->startOfMonth()->toDateString();
        $mEnd = $mCarbon->copy()->endOfMonth()->toDateString();

        $query = DetailExpense::join('expense as e', 'e.id', '=', 'detail_expense.id_expense')
            ->leftJoin('account as a', 'a.id', '=', 'detail_expense.id_account')
            ->leftJoin('bank as b', 'b.id', '=', 'e.id_bank')
            ->whereBetween('e.date', [$mStart, $mEnd]);

        if ($entity !== 'all') {
            $query->where('b.entity', $entity);
        }

        $transactions = $query
            ->select(
                'e.id as expense_id',
                'e.date',
                'e.no_expense',
                'e.no_invoice',
                'e.no_cheque',
                'detail_expense.memo as detail_memo',
                'e.memo as expense_memo',
                'detail_expense.amount',
                'a.code as account_code',
                'a.name as account_name',
                'a.category as account_category',
                'b.bank as bank_name',
                'b.entity as bank_entity'
            )
            ->orderBy('e.date', 'desc')
            ->get()
            ->map(function ($tx) {
                return [
                    'id'               => $tx->expense_id,
                    'date'             => Carbon::parse($tx->date)->format('d M Y'),
                    'no_expense'       => $tx->no_expense ?? '-',
                    'no_invoice'       => $tx->no_invoice ?? '-',
                    'no_cheque'        => $tx->no_cheque ?? '-',
                    'memo'             => $tx->detail_memo ?: ($tx->expense_memo ?: '-'),
                    'amount'           => (float) $tx->amount,
                    'amount_formatted' => 'Rp ' . number_format($tx->amount, 0, ',', '.'),
                    'account_code'     => $tx->account_code ?? '-',
                    'account_name'     => $tx->account_name ?? 'Biaya Lainnya',
                    'bank_name'        => $tx->bank_name ?? '-',
                    'bank_entity'      => strtoupper($tx->bank_entity ?? 'ALL'),
                ];
            });

        return response()->json([
            'status'           => 'success',
            'year'             => $year,
            'month'            => $month,
            'month_name'       => $mCarbon->translatedFormat('F'),
            'total_amount'     => $transactions->sum('amount'),
            'total_formatted'  => 'Rp ' . number_format($transactions->sum('amount'), 0, ',', '.'),
            'count'            => $transactions->count(),
            'transactions'     => $transactions,
        ]);
    }

    /**
     * Store or update expense budget configuration.
     */
    public function storeOrUpdate(Request $request)
    {
        $request->validate([
            'year'                 => 'required|integer|min:2020|max:2050',
            'entity'               => 'required|string|in:all,reftech,kojisha',
            'annual_budget'        => 'nullable|numeric|min:0',
            'monthly_budget'       => 'nullable|numeric|min:0',
            'notes'                => 'nullable|string|max:1000',
            'use_custom_monthly'   => 'nullable|boolean',
            'sync_total_from_depts'=> 'nullable|boolean',
            'dept_budget'          => 'nullable|array',
        ]);

        $year = (int) $request->year;
        $entity = $request->entity;
        $annual = (float) $request->annual_budget;
        $monthly = (float) $request->monthly_budget;

        // Process departmental sub-budgets
        $deptBudgetsInput = $request->input('dept_budget', []);
        $cleanDeptBudgets = [];
        $totalDeptAnnual = 0;

        foreach (ExpenseBudget::getDepartmentDefinitions() as $deptKey => $deptDef) {
            $annVal = isset($deptBudgetsInput[$deptKey]['annual']) ? (float) $deptBudgetsInput[$deptKey]['annual'] : 0;
            $monVal = isset($deptBudgetsInput[$deptKey]['monthly']) ? (float) $deptBudgetsInput[$deptKey]['monthly'] : 0;

            if ($annVal > 0 && $monVal <= 0) {
                $monVal = round($annVal / 12);
            } elseif ($monVal > 0 && $annVal <= 0) {
                $annVal = $monVal * 12;
            }

            if ($annVal > 0 || $monVal > 0) {
                $cleanDeptBudgets[$deptKey] = [
                    'annual'  => $annVal,
                    'monthly' => $monVal,
                ];
                $totalDeptAnnual += $annVal;
            }
        }

        // If user set department sub-budgets and checked sync or left main annual 0
        if ($totalDeptAnnual > 0 && ($annual <= 0 || $request->boolean('sync_total_from_depts'))) {
            $annual = $totalDeptAnnual;
            $monthly = round($totalDeptAnnual / 12);
        }

        // Auto calculation if one is left 0
        if ($annual > 0 && $monthly <= 0) {
            $monthly = round($annual / 12);
        } elseif ($monthly > 0 && $annual <= 0) {
            $annual = $monthly * 12;
        }

        $data = [
            'annual_budget'      => $annual,
            'monthly_budget'     => $monthly,
            'department_budgets' => !empty($cleanDeptBudgets) ? $cleanDeptBudgets : null,
            'notes'              => $request->notes,
            'created_by'         => Auth::id(),
        ];

        // Specific months breakdown if provided
        if ($request->boolean('use_custom_monthly')) {
            $totalCustom = 0;
            for ($m = 1; $m <= 12; $m++) {
                $val = $request->input('m' . $m);
                $data['m' . $m] = is_null($val) || $val === '' ? null : (float) $val;
                if (!is_null($data['m' . $m])) {
                    $totalCustom += $data['m' . $m];
                }
            }
            if ($totalCustom > 0) {
                $data['annual_budget'] = $totalCustom;
                $data['monthly_budget'] = round($totalCustom / 12);
            }
        } else {
            // Reset custom months if using flat monthly
            for ($m = 1; $m <= 12; $m++) {
                $data['m' . $m] = null;
            }
        }

        ExpenseBudget::updateOrCreate(
            ['year' => $year, 'entity' => $entity],
            $data
        );

        $entityLabel = strtoupper($entity);
        return redirect()->route('finance.expense-budget.index', ['year' => $year, 'entity' => $entity])
            ->with('success', "Plafon Annual Budget tahun {$year} ({$entityLabel}) berhasil disimpan.");
    }
}
