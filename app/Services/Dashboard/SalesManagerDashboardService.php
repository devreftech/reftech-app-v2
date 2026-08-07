<?php

namespace App\Services\Dashboard;

use App\Models\User;
use App\Models\Target;
use App\Models\Quotation;
use App\Models\UnitQuotation;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Activities;
use App\Models\DetailQuotation;
use App\Models\RevQuote;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SalesManagerDashboardService
{
    /**
     * Get dashboard data payload for Sales Manager role
     */
    public function getDashboardData($sorted, $sales, $notulens)
    {
        $smWidgets = $this->getSalesManagerDashboardData();

        return array_merge(compact('sorted', 'sales', 'notulens'), $smWidgets);
    }

    public function getSalesManagerDashboardData(): array
    {
        $dateNow = Carbon::now();
        $monthNow = $dateNow->month;
        $yearNow = $dateNow->year;
        $prevMonth = Carbon::create($yearNow, $monthNow, 1)->subMonth();

        $activeSales = User::where('role', 'Sales')->where('active', '1')->with('latestTarget')->orderByDesc('id')->get();
        $activeSalesIds = $activeSales->pluck('id');

        // ==== KPI: Target vs Actual bulan berjalan ====
        $smTargetMonth = Target::join('users as u', 'u.id', '=', 'target.id_sales')
            ->where('u.role', 'Sales')->where('u.active', '1')
            ->sum('target.total');

        $smActualMonth = Quotation::whereYear('po_date', $yearNow)->whereMonth('po_date', $monthNow)
            ->where('status', '100')->where('level', '1')->where('is_primary', '1')->sum('nett')
            + UnitQuotation::where('status', 'po_received')->where('is_latest', 1)->whereYear('po_received', $yearNow)->whereMonth('po_received', $monthNow)->sum(DB::raw('total - tax_amount'));

        $smActualPrevMonth = Quotation::whereYear('po_date', $prevMonth->year)->whereMonth('po_date', $prevMonth->month)
            ->where('status', '100')->where('level', '1')->where('is_primary', '1')->sum('nett')
            + UnitQuotation::where('status', 'po_received')->where('is_latest', 1)->whereYear('po_received', $prevMonth->year)->whereMonth('po_received', $prevMonth->month)->sum(DB::raw('total - tax_amount'));

        $smAchievement = $smTargetMonth > 0 ? round($smActualMonth / $smTargetMonth * 100, 1) : 0;
        $smVsLastMonthPct = $smActualPrevMonth > 0
            ? round((($smActualMonth - $smActualPrevMonth) / $smActualPrevMonth) * 100, 1)
            : 0;

        $smQuotationTotal = Quotation::whereYear('estimated_date', $yearNow)->whereMonth('estimated_date', $monthNow)
            ->where('level', '1')->where('is_primary', '1')->count();

        $smSoTotal = Quotation::whereYear('po_date', $yearNow)->whereMonth('po_date', $monthNow)
            ->where('status', '100')->where('level', '1')->where('is_primary', '1')->count()
            + UnitQuotation::where('status', 'po_received')->where('is_latest', 1)->whereYear('po_received', $yearNow)->whereMonth('po_received', $monthNow)->count();

        $smInvoiceTotal = Invoice::whereYear('date', $yearNow)->whereMonth('date', $monthNow)->count();

        // ==== Pipeline by Stage ====
        $smStatusCounts = Quotation::whereYear('estimated_date', $yearNow)->whereMonth('estimated_date', $monthNow)
            ->where('level', '1')->where('is_primary', '1')
            ->select('status', DB::raw('count(*) as total'))->groupBy('status')->pluck('total', 'status');

        $smLeadCount = Client::join('users as u', 'u.id', '=', 'client.id_sales')
            ->where('u.role', 'Sales')
            ->whereYear('client.created_at', $yearNow)->whereMonth('client.created_at', $monthNow)->count();

        $smPipelineLabels = ['Lead', 'Quotation', 'Negotiation', 'Waiting PO', 'Closed Won'];
        $smPipelineSeries = [
            (int) $smLeadCount,
            (int) ($smStatusCounts->get('20', 0) + $smStatusCounts->get('30', 0) + $smStatusCounts->get('40', 0)),
            (int) $smStatusCounts->get('60', 0),
            (int) ($smStatusCounts->get('80', 0) + $smStatusCounts->get('90', 0)),
            (int) $smStatusCounts->get('100', 0),
        ];
        $smPipelineTotal = array_sum($smPipelineSeries);

        // ==== Revenue by Quotation Status ====
        $smRevenueByStatus = Quotation::whereYear('estimated_date', $yearNow)->whereMonth('estimated_date', $monthNow)
            ->where('level', '1')->where('is_primary', '1')
            ->select('status', DB::raw('sum(nett) as total'))->groupBy('status')->pluck('total', 'status');
        $smRevenueStatusLabels = ['In Progress', 'Negotiation', 'Hot Prospect', 'PO Won'];
        $smRevenueStatusSeries = [
            (float) ($smRevenueByStatus->get('20', 0) + $smRevenueByStatus->get('30', 0) + $smRevenueByStatus->get('40', 0)),
            (float) $smRevenueByStatus->get('60', 0),
            (float) ($smRevenueByStatus->get('80', 0) + $smRevenueByStatus->get('90', 0)),
            (float) $smRevenueByStatus->get('100', 0),
        ];

        // ==== Sales Team Performance ====
        $smPoPerSales = Quotation::whereYear('po_date', $yearNow)->whereMonth('po_date', $monthNow)
            ->where('status', '100')->where('level', '1')->where('is_primary', '1')
            ->groupBy('id_sales')->selectRaw('id_sales, SUM(nett) as total_nett, COUNT(*) as total_count')
            ->get()->keyBy('id_sales');

        $smUnitPoPerSales = UnitQuotation::where('status', 'po_received')
            ->where('is_latest', 1)
            ->whereYear('po_received', $yearNow)->whereMonth('po_received', $monthNow)
            ->groupBy('id_sales')->selectRaw('id_sales, SUM(total - tax_amount) as total_nett, COUNT(*) as total_count')
            ->get()->keyBy('id_sales');

        $smQuotePerSales = Quotation::whereYear('estimated_date', $yearNow)->whereMonth('estimated_date', $monthNow)
            ->where('level', '1')->where('is_primary', '1')
            ->groupBy('id_sales')->selectRaw('id_sales, COUNT(*) as total_count')
            ->pluck('total_count', 'id_sales');

        $smForecastPerSales = Quotation::whereYear('estimated_date', $yearNow)->whereMonth('estimated_date', $monthNow)
            ->whereIn('status', ['20', '30', '40', '60', '80'])->where('level', '1')->where('is_primary', '1')
            ->groupBy('id_sales')->selectRaw('id_sales, SUM(nett) as total_nett')
            ->pluck('total_nett', 'id_sales');

        $smOutstandingPerSales = Payment::join('quotation as q', 'q.id', '=', 'payment.id_quotation')
            ->where('payment.type', 'Tempo')->where('payment.level', 0)
            ->groupBy('q.id_sales')->selectRaw('q.id_sales as id_sales, SUM(payment.amount) as total_amount')
            ->pluck('total_amount', 'id_sales');

        $smTeamIds = [16, 23];
        $smEcommerce = [
            'target' => 0.0,
            'actual' => 0.0,
            'quotation_count' => 0,
            'po_count' => 0,
            'outstanding' => 0.0,
            'forecast' => 0.0,
            'new_leads' => 0,
            'daily_call' => 0,
            'crm' => 0,
        ];

        $smActivityExcludeIds = [41];

        // ==== Batch per-sales metrics (avoid N+1 inside the loop below) ====
        $smNewLeadsPerSales = Client::whereIn('id_sales', $activeSalesIds)
            ->whereYear('created_at', $yearNow)->whereMonth('created_at', $monthNow)
            ->groupBy('id_sales')->selectRaw('id_sales, COUNT(*) as total')
            ->pluck('total', 'id_sales');

        $smDailyCallPerSales = Activities::join('client as c', 'activities.id_client', '=', 'c.id')
            ->whereIn('c.id_sales', $activeSalesIds)
            ->whereYear('activities.date', $yearNow)->whereMonth('activities.date', $monthNow)
            ->where('activities.status', 'Responded')
            ->whereIn('activities.name', ['Daily Call', 'Follow Up'])
            ->groupBy('c.id_sales')->selectRaw('c.id_sales as id_sales, COUNT(*) as total')
            ->pluck('total', 'id_sales');

        $smCrmPerSales = Activities::join('client as c', 'activities.id_client', '=', 'c.id')
            ->join(DB::raw('(SELECT id_client, status FROM crm_status WHERE id IN (SELECT MAX(id) FROM crm_status GROUP BY id_client)) as cs'), 'c.id', '=', 'cs.id_client')
            ->whereIn('c.id_sales', $activeSalesIds)
            ->whereYear('activities.date', $yearNow)->whereMonth('activities.date', $monthNow)
            ->where('activities.status', 'Responded')->where('activities.name', 'CRM')
            ->where('cs.status', '2')
            ->groupBy('c.id_sales')->selectRaw('c.id_sales as id_sales, COUNT(DISTINCT c.id) as total')
            ->pluck('total', 'id_sales');

        $smTeamPerformance = [];
        $smTeamActivity = [];
        foreach ($activeSales as $sale) {
            $target = $sale->latestTarget->total ?? 0;
            $actualRow     = $smPoPerSales->get($sale->id);
            $unitActualRow = $smUnitPoPerSales->get($sale->id);
            $actual   = ($actualRow->total_nett ?? 0) + ($unitActualRow->total_nett ?? 0);
            $poCount  = ($actualRow->total_count ?? 0) + ($unitActualRow->total_count ?? 0);
            $quotationCount = (int) $smQuotePerSales->get($sale->id, 0);
            $outstanding = (float) $smOutstandingPerSales->get($sale->id, 0);
            $forecast = (float) $smForecastPerSales->get($sale->id, 0);

            $newLeads = (int) $smNewLeadsPerSales->get($sale->id, 0);
            $dailyCall = (int) $smDailyCallPerSales->get($sale->id, 0);
            $crm = (int) $smCrmPerSales->get($sale->id, 0);

            if (in_array($sale->id, $smTeamIds)) {
                $smEcommerce['target'] += $target;
                $smEcommerce['actual'] += $actual;
                $smEcommerce['quotation_count'] += $quotationCount;
                $smEcommerce['po_count'] += $poCount;
                $smEcommerce['outstanding'] += $outstanding;
                $smEcommerce['forecast'] += $forecast;
                $smEcommerce['new_leads'] += $newLeads;
                $smEcommerce['daily_call'] += $dailyCall;
                $smEcommerce['crm'] += $crm;
                continue;
            }

            $smTeamPerformance[] = [
                'name' => $sale->name,
                'image' => $sale->image,
                'target' => (float) $target,
                'actual' => (float) $actual,
                'achievement' => $target > 0 ? round($actual / $target * 100, 1) : 0,
                'quotation_count' => $quotationCount,
                'po_count' => (int) $poCount,
                'outstanding' => $outstanding,
                'forecast' => $forecast,
            ];

            if (!in_array($sale->id, $smActivityExcludeIds)) {
                $smTeamActivity[] = [
                    'name' => $sale->name,
                    'image' => $sale->image,
                    'new_leads' => $newLeads,
                    'daily_call' => $dailyCall,
                    'crm' => $crm,
                    'quotation_count' => $quotationCount,
                    'po_count' => (int) $poCount,
                ];
            }
        }

        $smTeamPerformance[] = [
            'name' => 'Team E-Commerce',
            'image' => null,
            'target' => $smEcommerce['target'],
            'actual' => $smEcommerce['actual'],
            'achievement' => $smEcommerce['target'] > 0 ? round($smEcommerce['actual'] / $smEcommerce['target'] * 100, 1) : 0,
            'quotation_count' => $smEcommerce['quotation_count'],
            'po_count' => $smEcommerce['po_count'],
            'outstanding' => $smEcommerce['outstanding'],
            'forecast' => $smEcommerce['forecast'],
        ];

        usort($smTeamPerformance, fn($a, $b) => $b['achievement'] <=> $a['achievement']);

        // ==== Outstanding Approval ====
        $smDiscountOver10 = DetailQuotation::join('quotation as q', 'q.id', '=', 'detail_quotation.id_quotation')
            ->where('detail_quotation.disc', '>', 10)
            ->whereYear('q.estimated_date', $yearNow)->whereMonth('q.estimated_date', $monthNow)
            ->where('q.level', '1')->where('q.is_primary', '1')
            ->distinct('q.id')->count('q.id');

        $smRevisionCount = RevQuote::join('quotation as q', 'q.id', '=', 'rev_quote.id_quotation')
            ->whereYear('rev_quote.created_at', $yearNow)->whereMonth('rev_quote.created_at', $monthNow)
            ->count();

        // ==== Alert ====
        $smExpiredCount = Quotation::where('expired_date', '<', $dateNow)
            ->whereNotIn('status', ['100', '0'])->where('level', '1')->where('is_primary', '1')->count();

        $smPoBelumMasukCount = Quotation::where('status', '100')->whereNull('po_file')
            ->where('level', '1')->where('is_primary', '1')->count();

        $smInvoiceBelumDibuatCount = Quotation::join('pic', 'pic.id', '=', 'quotation.id_pic')
            ->join('client', 'client.id', '=', 'pic.id_client')
            ->join('invoice', 'invoice.id_quotation', '=', 'quotation.id')
            ->where('quotation.status', '100')
            ->whereNotNull('quotation.po_file')
            ->whereNull('invoice.no_invoice')->count();

        $smOverdueInvoiceCount = Payment::where('type', 'Tempo')->where('level', 0)
            ->whereDate('due_date', '<=', Carbon::today())->count();

        // ==== Customer Activity ====
        $smVisitToday = Activities::join('client as c', 'c.id', '=', 'activities.id_client')
            ->join('users as s', 'c.id_sales', '=', 's.id')
            ->where('s.role', 'Sales')
            ->where('activities.name', 'Visit')
            ->whereDate('activities.date', $dateNow)
            ->select(['activities.id', 'c.company', 's.name as sales_name', 'activities.status'])
            ->get();

        $smFollowUpToday = Activities::join('client as c', 'c.id', '=', 'activities.id_client')
            ->join('users as s', 'c.id_sales', '=', 's.id')
            ->where('s.role', 'Sales')
            ->whereDate('activities.follow_up', $dateNow)
            ->select(['activities.id', 'c.company', 's.name as sales_name', 'activities.note'])
            ->get();

        // ==== Recent PO Won ====
        $smRecentPoWon = Quotation::join('pic', 'pic.id', '=', 'quotation.id_pic')
            ->join('client', 'client.id', '=', 'pic.id_client')
            ->join('users as u', 'u.id', '=', 'quotation.id_sales')
            ->leftJoin('invoice', 'invoice.id_quotation', '=', 'quotation.id')
            ->where('quotation.status', '100')
            ->where('quotation.level', '1')->where('quotation.is_primary', '1')
            ->orderByDesc('quotation.po_date')
            ->limit(8)
            ->get([
                'quotation.id',
                'quotation.no_quote',
                'quotation.nett',
                'quotation.po_date',
                'client.company',
                'u.name as sales_name',
                DB::raw("CASE WHEN quotation.po_file IS NULL THEN 'PO Belum Masuk' ELSE 'PO Diterima' END as po_status"),
                DB::raw("CASE WHEN invoice.no_invoice IS NULL THEN 'Belum Invoice' ELSE 'Invoice Dibuat' END as invoice_status"),
            ]);

        // ==== Forecast 3 Bulan ====
        $smForecastLabels = [];
        $smForecastSeries = [];
        for ($i = 0; $i <= 2; $i++) {
            $m = Carbon::now()->addMonths($i);
            $smForecastLabels[] = $m->locale('id')->translatedFormat('M Y');
            $smForecastSeries[] = (float) Quotation::whereYear('estimated_date', $m->year)->whereMonth('estimated_date', $m->month)
                ->whereIn('status', ['20', '30', '40', '60', '80'])
                ->where('level', '1')->where('is_primary', '1')->sum('nett');
        }

        return compact(
            'smTargetMonth',
            'smActualMonth',
            'smAchievement',
            'smVsLastMonthPct',
            'smQuotationTotal',
            'smSoTotal',
            'smInvoiceTotal',
            'smPipelineLabels',
            'smPipelineSeries',
            'smPipelineTotal',
            'smRevenueStatusLabels',
            'smRevenueStatusSeries',
            'smTeamPerformance',
            'smTeamActivity',
            'smDiscountOver10',
            'smRevisionCount',
            'smExpiredCount',
            'smPoBelumMasukCount',
            'smInvoiceBelumDibuatCount',
            'smOverdueInvoiceCount',
            'smVisitToday',
            'smFollowUpToday',
            'smRecentPoWon',
            'smForecastLabels',
            'smForecastSeries',
        );
    }
}
