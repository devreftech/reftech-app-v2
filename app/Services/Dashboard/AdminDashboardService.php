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
use App\Models\Prospect;
use App\Models\PendingPO;
use App\Models\PurchaseRequest;
use App\Models\Contract;
use App\Models\Comment;
use App\Models\Reports;
use App\Models\SalesOnline;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class AdminDashboardService
{
    /**
     * Ambil data mingguan (per week_num) buat satu "sale" dari $allData yang udah
     * di-groupBy('id_sales') — baris "Sales Project" gabungan punya beberapa id
     * sekaligus ($sale->id_sales_list), jadi datanya perlu dijumlahin per minggu
     * dari semua id itu, bukan lookup satu id doang kayak sales individu biasa.
     */
    private function weeklyDataForSale($allData, $sale)
    {
        $ids = $sale->id_sales_list ?? [$sale->id];
        $merged = collect();
        foreach ($ids as $id) {
            foreach ($allData->get($id, collect()) as $week => $total) {
                $merged[$week] = ($merged[$week] ?? 0) + $total;
            }
        }
        return $merged;
    }

    /**
     * Get dashboard data payload for Admin role
     */
    public function getDashboardData($sorted, $sales, $notulens, $yearNow, $monthNow, $dateNow)
    {
        $validPendingIds = \App\Models\PendingPO::where(function ($q) {
            $q->whereNotNull('id_quotation')->orWhereNotNull('id_unit_quotation');
        })->pluck('id');
        $prCount = PurchaseRequest::where('status', '0')
            ->whereIn('id_pending', $validPendingIds)
            ->has('details')
            ->count();

        // Daily Welcome Alert for Admin
        $showAdminWelcomeAlert = false;
        $adminUserId = Auth::id();
        if ($adminUserId) {
            $welcomeAlertKey = 'admin_welcome_alert_' . $adminUserId . '_' . $dateNow->toDateString();
            if (!Cache::has($welcomeAlertKey)) {
                Cache::put($welcomeAlertKey, true, $dateNow->copy()->endOfDay());
                $showAdminWelcomeAlert = true;
            }
        }

        // 1. Review Pekerjaan Kemarin (Service Report)
        // Jika login hari Senin -> review pekerjaan Jumat, Sabtu & Minggu
        $isMonday = $dateNow->isMonday();
        if ($isMonday) {
            $srStartDate = $dateNow->copy()->subDays(3)->startOfDay(); // Jumat
            $srEndDate = $dateNow->copy()->subDays(1)->endOfDay();     // Minggu
            $yesterdayServiceReportCount = Reports::whereBetween('date', [$srStartDate->toDateString(), $srEndDate->toDateString()])->count();
            $serviceReportPeriodLabel = 'Pekerjaan Jumat, Sabtu & Minggu';
        } else {
            $yesterdayDate = $dateNow->copy()->subDay()->toDateString();
            $yesterdayServiceReportCount = Reports::whereDate('date', $yesterdayDate)->count();
            $serviceReportPeriodLabel = 'Pekerjaan Kemarin (' . $dateNow->copy()->subDay()->isoFormat('dddd, D MMM') . ')';
        }

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

        $adminPoTotals = Cache::remember("admin_dash_po_totals_{$yearNow}_{$monthNow}", 300, function () use ($yearNow, $monthNow) {
            $total = Quotation::whereYear('po_date', $yearNow)
                ->whereMonth('po_date', $monthNow)
                ->where('status', '100')
                ->where('level', '1')
                ->where('is_primary', '1')
                ->sum('nett')
                + UnitQuotation::where('status', 'po_received')
                    ->where('is_latest', 1)
                    ->whereYear('po_received', $yearNow)
                    ->whereMonth('po_received', $monthNow)
                    ->sum(DB::raw('total - tax_amount'));

            return [
                'total' => $total,
                'formatted' => $this->formatNumber($total),
            ];
        });

        $poTotalPriceAdmin = $adminPoTotals['total'];
        $formattedTotalPriceAdmin = $adminPoTotals['formatted'];

        $salesOrder = [4, 3, 2, 1, 32, 41, 16, 22];
        $sales = User::where('role', 'Sales')
            ->where('active', '1')
            ->get()
            ->sortBy(function ($sale) use ($salesOrder) {
                $pos = array_search($sale->id, $salesOrder);
                return $pos === false ? 999 : $pos;
            })
            ->values();

        $firstSales = $sales->first() ?? User::find(1);
        $firstSalesId = $firstSales ? $firstSales->id : null;
        $targett = Target::where('id_sales', $firstSalesId)->first('total');

        $targetAllSales = Cache::remember("admin_dash_target_all_sales", 300, function () {
            return Target::join('users as u', 'u.id', '=', 'target.id_sales')
                ->where('u.role', 'Sales')
                ->where('u.active', '1')
                ->sum('target.total');
        });

        $targetsBySale = Target::whereIn('id_sales', $sales->pluck('id'))
            ->groupBy('id_sales')
            ->get()
            ->keyBy('id_sales');

        $targetSales = $sales->map(function ($sale) use ($targetsBySale) {
            $target = $targetsBySale->get($sale->id);
            return $target ? collect([$target]) : collect();
        });

        $firstDayOfMonth = "{$yearNow}-{$monthNow}-01";
        $lastDayOfMonth = date('Y-m-t', strtotime($firstDayOfMonth));

        $salesIds = $sales->pluck('id')->toArray();

        // 1. Quotation aggregation by id_sales
        $quoteAggs = Quotation::whereBetween('estimated_date', [$firstDayOfMonth, $lastDayOfMonth])
            ->whereIn('id_sales', $salesIds)
            ->where('level', '1')
            ->where('is_primary', '1')
            ->groupBy('id_sales')
            ->selectRaw("
                id_sales,
                COUNT(*) as filtered_quote,
                COALESCE(SUM(nett), 0) as total_quotation,
                COALESCE(SUM(CASE WHEN status IN ('20','30','40','60','80') THEN nett ELSE 0 END), 0) as total_prospect_support,
                COALESCE(SUM(CASE WHEN status = '80' THEN nett ELSE 0 END), 0) as total_forecast,
                COALESCE(SUM(CASE WHEN status IN ('80','90') THEN nett ELSE 0 END), 0) as total_hot_prospect,
                COALESCE(SUM(CASE WHEN status = '0' THEN nett ELSE 0 END), 0) as total_loss
            ")
            ->get()->keyBy('id_sales');

        // 2. UnitQuotation aggregation by id_sales
        $unitQuoteAggs = UnitQuotation::whereBetween('date', [$firstDayOfMonth, $lastDayOfMonth])
            ->whereIn('id_sales', $salesIds)
            ->where('is_latest', 1)
            ->groupBy('id_sales')
            ->selectRaw("
                id_sales,
                COUNT(*) as filtered_quote,
                COALESCE(SUM(total - IFNULL(tax_amount, 0) - IFNULL(fee, 0)), 0) as total_quotation,
                COALESCE(SUM(CASE WHEN status IN ('draft','sent','negotiation','revision','hot_prospect') THEN (total - IFNULL(tax_amount, 0) - IFNULL(fee, 0)) ELSE 0 END), 0) as total_prospect_support,
                COALESCE(SUM(CASE WHEN status = 'hot_prospect' THEN (total - IFNULL(tax_amount, 0) - IFNULL(fee, 0)) ELSE 0 END), 0) as total_forecast,
                COALESCE(SUM(CASE WHEN status = 'hot_prospect' THEN (total - IFNULL(tax_amount, 0) - IFNULL(fee, 0)) ELSE 0 END), 0) as total_hot_prospect,
                COALESCE(SUM(CASE WHEN status = 'loss' THEN (total - IFNULL(tax_amount, 0) - IFNULL(fee, 0)) ELSE 0 END), 0) as total_loss
            ")
            ->get()->keyBy('id_sales');

        // 3. PO aggregation by id_sales
        $poQuoteAggs = Quotation::whereBetween('po_date', [$firstDayOfMonth, $lastDayOfMonth])
            ->whereIn('id_sales', $salesIds)
            ->where('status', '100')
            ->where('level', '1')
            ->where('is_primary', '1')
            ->groupBy('id_sales')
            ->selectRaw('id_sales, COALESCE(SUM(nett), 0) as total_po, COUNT(*) as po_count')
            ->get()->keyBy('id_sales');

        $poUnitAggs = UnitQuotation::where('status', 'po_received')
            ->where('is_latest', 1)
            ->whereYear('po_received', $yearNow)
            ->whereMonth('po_received', $monthNow)
            ->whereIn('id_sales', $salesIds)
            ->groupBy('id_sales')
            ->selectRaw('id_sales, COALESCE(SUM(total - tax_amount), 0) as total_po, COUNT(*) as po_count')
            ->get()->keyBy('id_sales');

        // 4. Prospect nominal
        $prospectAggs = Quotation::join('prospect as p', 'quotation.id', '=', 'p.id_quotation')
            ->whereNotNull('id_quotation')
            ->whereYear('estimated_date', $yearNow)
            ->whereMonth('estimated_date', $monthNow)
            ->whereIn('quotation.id_sales', $salesIds)
            ->whereIn('status', ['80', '90'])
            ->where('quotation.level', '1')
            ->where('quotation.is_primary', '1')
            ->groupBy('quotation.id_sales')
            ->selectRaw('quotation.id_sales, SUM(nett) as total_prospect')
            ->get()->keyBy('id_sales');

        // 5. Leads count by sales
        $leadsAggs = Client::whereBetween('created_at', [$firstDayOfMonth . ' 00:00:00', $lastDayOfMonth . ' 23:59:59'])
            ->whereIn('id_sales', $salesIds)
            ->groupBy('id_sales')
            ->selectRaw('id_sales, COUNT(*) as cnt')
            ->get()->keyBy('id_sales');

        // 6. Activities DC & Visit by sales
        $dcVisitAggs = Activities::join('client as c', 'activities.id_client', '=', 'c.id')
            ->whereBetween('date', [$firstDayOfMonth, $lastDayOfMonth])
            ->whereIn('c.id_sales', $salesIds)
            ->where('activities.status', 'Responded')
            ->groupBy('c.id_sales')
            ->selectRaw("
                c.id_sales,
                COUNT(DISTINCT CASE WHEN activities.name IN ('Daily Call','Follow Up') THEN c.id ELSE NULL END) as filtered_dc,
                COUNT(DISTINCT CASE WHEN activities.name = 'Visit' THEN c.id ELSE NULL END) as filtered_visit
            ")
            ->get()->keyBy('id_sales');

        // 7. Activities CRM by sales
        $crmAggs = Activities::join('client as c', 'activities.id_client', '=', 'c.id')
            ->join(DB::raw('(SELECT id_client, status FROM crm_status WHERE id IN (SELECT MAX(id) FROM crm_status GROUP BY id_client)) as cs'), 'c.id', '=', 'cs.id_client')
            ->whereYear('date', $yearNow)
            ->whereMonth('date', $monthNow)
            ->whereIn('c.id_sales', $salesIds)
            ->where('activities.status', 'Responded')
            ->where('activities.name', 'CRM')
            ->where('cs.status', '2')
            ->groupBy('c.id_sales')
            ->selectRaw('c.id_sales, COUNT(DISTINCT c.id) as cnt')
            ->get()->keyBy('id_sales');

        // 8. Prospect count by sales
        $prospectCountAggs = Prospect::whereNotNull('id_quotation')
            ->whereMonth('date', $monthNow)
            ->whereYear('date', $yearNow)
            ->whereIn('id_sales', $salesIds)
            ->groupBy('id_sales')
            ->selectRaw('id_sales, COUNT(*) as cnt')
            ->get()->keyBy('id_sales');

        // 9. Online Metrics (for Didik / Ecommerce)
        $onlineProducts = SalesOnline::where('type', 'Product')
            ->whereMonth('date', $monthNow)
            ->whereYear('date', $yearNow)
            ->whereIn('id_sales', $salesIds)
            ->groupBy('id_sales')
            ->selectRaw('id_sales, COUNT(*) as cnt')
            ->get()->keyBy('id_sales');

        $onlineVideos = SalesOnline::where('type', 'Video')
            ->whereMonth('date', $monthNow)
            ->whereYear('date', $yearNow)
            ->whereIn('id_sales', $salesIds)
            ->get()
            ->groupBy('id_sales');

        $onlineStats = SalesOnline::whereIn('type', ['Stat', 'Delivery', 'Response', 'Rating'])
            ->whereMonth('date', $monthNow)
            ->whereYear('date', $yearNow)
            ->whereIn('id_sales', $salesIds)
            ->groupBy('id_sales', 'type')
            ->selectRaw('id_sales, type, AVG(average) as avg_val')
            ->get();

        $salesOverviewData = [];
        foreach ($sales as $sale) {
            $sid = $sale->id;
            $target = $targetsBySale->get($sid);
            $targetLeads = $target?->leads ?? 0;
            $targetDc = $target?->dc ?? 0;
            $targetQuote = $target?->quote ?? 0;
            $targetTotalPo = $target?->total ?? 0;
            $targetCrmCount = $targetCrm[$sid] ?? 0;

            $leads = (int) ($leadsAggs[$sid]->cnt ?? 0);
            $dc = (int) ($dcVisitAggs[$sid]->filtered_dc ?? 0);
            $visit = (int) ($dcVisitAggs[$sid]->filtered_visit ?? 0);
            $crm = (int) ($crmAggs[$sid]->cnt ?? 0);
            $quote = (int) (($quoteAggs[$sid]->filtered_quote ?? 0) + ($unitQuoteAggs[$sid]->filtered_quote ?? 0));
            $prospectCount = (int) ($prospectCountAggs[$sid]->cnt ?? 0);
            $poCount = (int) (($poQuoteAggs[$sid]->po_count ?? 0) + ($poUnitAggs[$sid]->po_count ?? 0));

            $totalQuotationVal = (float) (($quoteAggs[$sid]->total_quotation ?? 0) + ($unitQuoteAggs[$sid]->total_quotation ?? 0));
            $totalProspectSupportVal = (float) (($quoteAggs[$sid]->total_prospect_support ?? 0) + ($unitQuoteAggs[$sid]->total_prospect_support ?? 0));
            $totalForecastVal = (float) (($quoteAggs[$sid]->total_forecast ?? 0) + ($unitQuoteAggs[$sid]->total_forecast ?? 0));
            $totalProspectVal = (float) ($prospectAggs[$sid]->total_prospect ?? 0);
            $totalHotProspectVal = (float) (($quoteAggs[$sid]->total_hot_prospect ?? 0) + ($unitQuoteAggs[$sid]->total_hot_prospect ?? 0));
            $totalPoVal = (float) (($poQuoteAggs[$sid]->total_po ?? 0) + ($poUnitAggs[$sid]->total_po ?? 0));
            $totalLossVal = (float) (($quoteAggs[$sid]->total_loss ?? 0) + ($unitQuoteAggs[$sid]->total_loss ?? 0));

            // Online specific
            $prodCount = (int) ($onlineProducts[$sid]->cnt ?? 0);
            $vidScore = 0;
            if (isset($onlineVideos[$sid])) {
                foreach ($onlineVideos[$sid] as $ov) {
                    if (!empty($ov->ig)) $vidScore += 30;
                    if (!empty($ov->tiktok)) $vidScore += 30;
                    if (!empty($ov->tokped)) $vidScore += 30;
                }
            }
            $statVal = 0; $deliveryVal = 0; $responseVal = 0; $ratingVal = 0;
            foreach ($onlineStats->where('id_sales', $sid) as $os) {
                if ($os->type === 'Stat') $statVal = (float) $os->avg_val;
                if ($os->type === 'Delivery') $deliveryVal = (float) $os->avg_val;
                if ($os->type === 'Response') $responseVal = (float) $os->avg_val;
                if ($os->type === 'Rating') $ratingVal = (float) $os->avg_val;
            }

            $salesOverviewData[$sid] = [
                'leads'                  => $leads,
                'target_leads'           => $targetLeads,
                'percent_leads'          => $targetLeads > 0 ? round(($leads / $targetLeads) * 100) : 0,
                'dc'                     => $dc,
                'target_dc'              => $targetDc,
                'percent_dc'             => $targetDc > 0 ? round(($dc / $targetDc) * 100) : 0,
                'visit'                  => $visit,
                'crm'                    => $crm,
                'target_crm'             => $targetCrmCount,
                'percent_crm'            => $targetCrmCount > 0 ? round(($crm / $targetCrmCount) * 100) : 0,
                'quote'                  => $quote,
                'target_quote'           => $targetQuote,
                'percent_quote'          => $targetQuote > 0 ? round(($quote / $targetQuote) * 100) : 0,
                'prospect_count'         => $prospectCount,
                'po_count'               => $poCount,
                'total_quotation'        => $totalQuotationVal,
                'total_prospect_support' => $totalProspectSupportVal,
                'total_forecast'         => $totalForecastVal,
                'total_prospect'         => $totalProspectVal,
                'total_hot_prospect'     => $totalHotProspectVal,
                'total_po'               => $totalPoVal,
                'total_loss'             => $totalLossVal,
                'target_total_po'        => $targetTotalPo,
                'percent_po'             => $targetTotalPo > 0 ? round(($totalPoVal / $targetTotalPo) * 100) : 0,
                // Online
                'online_product'         => $prodCount,
                'online_video'           => $vidScore,
                'online_stat'            => $statVal,
                'online_delivery'        => $deliveryVal,
                'online_response'        => $responseVal,
                'online_rating'          => $ratingVal,
            ];
        }

        // Backward compatibility for firstSales
        $firstSalesData = $salesOverviewData[$firstSalesId] ?? [];
        $filteredQuote = $firstSalesData['quote'] ?? 0;
        $totalQuotation = $firstSalesData['total_quotation'] ?? 0;
        $totalProspectSupport = $firstSalesData['total_prospect_support'] ?? 0;
        $totalForecast = $firstSalesData['total_forecast'] ?? 0;
        $totalHotProspect = $firstSalesData['total_hot_prospect'] ?? 0;
        $totalLoss = $firstSalesData['total_loss'] ?? 0;
        $totalProspect = $firstSalesData['total_prospect'] ?? 0;
        $totalPO = $firstSalesData['total_po'] ?? 0;
        $filteredPO = $firstSalesData['po_count'] ?? 0;
        $filteredLeads = $firstSalesData['leads'] ?? 0;
        $filteredDC = $firstSalesData['dc'] ?? 0;
        $filteredVisit = $firstSalesData['visit'] ?? 0;
        $filteredCRM = $firstSalesData['crm'] ?? 0;
        $filteredProspect = Prospect::whereNotNull('id_quotation')->whereMonth('date', $monthNow)->whereYear('date', $yearNow)->count();
        $allProspect = Prospect::whereMonth('date', $monthNow)->whereYear('date', $yearNow)->count();

        // "Sales Project" — quotation yang dibuat oleh Admin/Sales Manager (bukan Sales individu),
        // digabung jadi satu angka karena project cuma nerima & ngolah data, gak punya target sendiri.
        $projectSalesIds = User::whereIn('role', ['Admin', 'Sales Manager'])->where('active', '1')->pluck('id');

        // projectQuoteCount & projectQuoteNominal (count + sum dari filter yang sama)
        // digabung jadi 1 query per tabel — sebelumnya 4 query.
        $projectQuoteAgg = Quotation::whereBetween('estimated_date', [$firstDayOfMonth, $lastDayOfMonth])
            ->whereIn('id_sales', $projectSalesIds)->where('level', '1')->where('is_primary', '1')
            ->selectRaw('COUNT(*) as cnt, COALESCE(SUM(nett), 0) as total_nett')
            ->first();

        $projectUnitAgg = UnitQuotation::whereYear('date', $yearNow)->whereMonth('date', $monthNow)
            ->whereIn('id_sales', $projectSalesIds)->where('is_latest', 1)
            ->selectRaw('COUNT(*) as cnt, COALESCE(SUM(total), 0) as total_nett')
            ->first();

        $projectQuoteCount = (int) $projectQuoteAgg->cnt + (int) $projectUnitAgg->cnt;
        $projectQuoteNominal = (float) $projectQuoteAgg->total_nett + (float) $projectUnitAgg->total_nett;

        // "Marketing Team" — agregat performa seluruh akun Support/Marketing
        $supportUserIds = User::where('role', 'Support')->where('active', '1')->pluck('id');
        $marketingAgg = Cache::remember("admin_dash_marketing_agg_{$yearNow}_{$monthNow}", 300, function () use ($supportUserIds, $yearNow, $monthNow, $firstDayOfMonth, $lastDayOfMonth) {
            if ($supportUserIds->isEmpty()) {
                return [
                    'prospect' => 0,
                    'provided' => 0,
                    'notProvided' => 0,
                    'quoteCount' => 0,
                    'poCount' => 0,
                    'quoteNominal' => 0,
                    'hotProspectNominal' => 0,
                    'poNominal' => 0,
                ];
            }

            $prospect = Prospect::whereYear('date', $yearNow)
                ->whereMonth('date', $monthNow)
                ->whereIn('id_support', $supportUserIds)
                ->count();

            $provided = Prospect::whereYear('date', $yearNow)
                ->whereMonth('date', $monthNow)
                ->where('provide', '!=', '0')
                ->whereIn('id_support', $supportUserIds)
                ->count();

            $notProvided = Prospect::whereYear('date', $yearNow)
                ->whereMonth('date', $monthNow)
                ->where('provide', '0')
                ->whereIn('id_support', $supportUserIds)
                ->count();

            $quoteCount = Quotation::whereYear('estimated_date', $yearNow)
                ->whereMonth('estimated_date', $monthNow)
                ->whereIn('id_support', $supportUserIds)
                ->where('level', '1')
                ->where('is_primary', '1')
                ->count()
                + UnitQuotation::where('is_latest', 1)
                    ->whereYear('date', $yearNow)
                    ->whereMonth('date', $monthNow)
                    ->whereIn('id_support', $supportUserIds)
                    ->count();

            $poCount = Quotation::whereYear('po_date', $yearNow)
                ->whereMonth('po_date', $monthNow)
                ->whereIn('id_support', $supportUserIds)
                ->where('status', '100')
                ->where('level', '1')
                ->where('is_primary', '1')
                ->count()
                + UnitQuotation::where('is_latest', 1)
                    ->where('status', 'po_received')
                    ->whereYear('po_received', $yearNow)
                    ->whereMonth('po_received', $monthNow)
                    ->whereIn('id_support', $supportUserIds)
                    ->count();

            $quoteNominal = (float) Quotation::whereBetween('estimated_date', [$firstDayOfMonth, $lastDayOfMonth])
                ->whereIn('id_support', $supportUserIds)
                ->where('level', '1')
                ->where('is_primary', '1')
                ->sum('nett')
                + (float) UnitQuotation::whereBetween('date', [$firstDayOfMonth, $lastDayOfMonth])
                    ->whereIn('id_support', $supportUserIds)
                    ->where('is_latest', 1)
                    ->sum(DB::raw('total - IFNULL(tax_amount, 0) - IFNULL(fee, 0)'));

            $hotProspectNominal = (float) Quotation::whereBetween('estimated_date', [$firstDayOfMonth, $lastDayOfMonth])
                ->whereIn('id_support', $supportUserIds)
                ->whereIn('status', ['80', '90'])
                ->where('level', '1')
                ->where('is_primary', '1')
                ->sum('nett')
                + (float) UnitQuotation::whereBetween('date', [$firstDayOfMonth, $lastDayOfMonth])
                    ->whereIn('id_support', $supportUserIds)
                    ->where('is_latest', 1)
                    ->where('status', 'hot_prospect')
                    ->sum(DB::raw('total - IFNULL(tax_amount, 0) - IFNULL(fee, 0)'));

            $poNominal = (float) Quotation::whereBetween('po_date', [$firstDayOfMonth, $lastDayOfMonth])
                ->whereIn('id_support', $supportUserIds)
                ->where('status', '100')
                ->where('level', '1')
                ->where('is_primary', '1')
                ->sum('nett')
                + (float) UnitQuotation::where('status', 'po_received')
                    ->where('is_latest', 1)
                    ->whereBetween('po_received', [$firstDayOfMonth, $lastDayOfMonth])
                    ->whereIn('id_support', $supportUserIds)
                    ->sum(DB::raw('total - IFNULL(tax_amount, 0) - IFNULL(fee, 0)'));

            return [
                'prospect' => $prospect,
                'provided' => $provided,
                'notProvided' => $notProvided,
                'quoteCount' => $quoteCount,
                'poCount' => $poCount,
                'quoteNominal' => $quoteNominal,
                'hotProspectNominal' => $hotProspectNominal,
                'poNominal' => $poNominal,
            ];
        });

        $weekDataSales = User::activeSalesAndProjectAdmins();

        $cachePrefix = "admin_dash_w_{$yearNow}_{$monthNow}_";

        $weekActivities = Cache::remember($cachePrefix . 'activities', 300, function () use ($weekDataSales) {
            return $this->getWeekDataActivitiesCombined($weekDataSales);
        });
        $dataDc = $weekActivities['dc'];
        $dataCRM = $weekActivities['crm'];
        $dataVisit = $weekActivities['visit'];

        $dataQuote = Cache::remember($cachePrefix . 'quote', 300, function () use ($weekDataSales) {
            return $this->getWeekDataQuote($weekDataSales);
        });
        $dataOverview = $this->getDataOverview();

        $dataLeads = Cache::remember($cachePrefix . 'leads', 300, function () use ($weekDataSales) {
            return $this->getWeekDataLeads($weekDataSales);
        });
        $dataPO = Cache::remember($cachePrefix . 'po', 300, function () use ($weekDataSales) {
            return $this->getWeekDataPO($weekDataSales);
        });

        $targetCrm = Cache::remember("admin_dash_target_crm", 300, function () {
            return Client::join(DB::raw('(SELECT id_client, status FROM crm_status WHERE id IN (SELECT MAX(id) FROM crm_status GROUP BY id_client)) as cs'), 'client.id', '=', 'cs.id_client')
                ->where('role', 'Customers')
                ->where('cs.status', '2')
                ->select('id_sales', DB::RAW('COUNT(*) as total'))
                ->groupBy('id_sales')
                ->pluck('total', 'id_sales')->toArray();
        });

        // Comment Buat Admin (dioptimalkan: 1 query terindeks, filter in-memory untuk unread)
        $firstComments = Comment::where('id_user', Auth::id())
            ->groupBy('id_status')
            ->get();

        $statusIds = $firstComments->pluck('id_status')->filter()->toArray();
        $dates = $firstComments->pluck('created_at', 'id_status');

        $commentAdmin = collect();
        $unreadCommentAdmin = collect();

        if (!empty($statusIds)) {
            $commentsQuery = Comment::join('change_status as c', 'c.id', '=', 'comment.id_status')
                ->join('quotation as q', 'q.id', '=', 'c.id_quotation')
                ->join('users as u', 'u.id', '=', 'comment.id_user')
                ->whereIn('comment.id_status', $statusIds)
                ->where(function ($query) use ($dates) {
                    foreach ($dates as $statusId => $createdAt) {
                        $query->orWhere(function ($subQuery) use ($statusId, $createdAt) {
                            $subQuery->where('comment.id_status', $statusId)
                                ->where('comment.created_at', '>', $createdAt);
                        });
                    }
                })
                ->where('comment.id_user', '!=', Auth::id());

            $commentAdmin = $commentsQuery->orderBy('comment.id_status')
                ->orderByDesc('comment.created_at')
                ->get(['q.id as idQ', 'comment.id as idC', 'comment.id_user', 'comment.level', 'comment.comment', 'comment.date', 'q.no_quote', 'u.name', 'u.image']);

            $unreadCommentAdmin = $commentAdmin->where('level', '1')->values();
        }

        // End Comment Admin
        $newCount = PendingPO::where('status', operator: 0)
            ->where('type', 'Non Project')
            ->count();
        $listCount = PendingPO::whereIn('pending_po.status', [1, 2, 3, 4])
            ->where('type', 'Non Project')
            ->count();
        $deliveryCount = PendingPO::where('pending_po.status', 5)
            ->where('type', 'Non Project')
            ->count();

        // Admin / Developer bisa berpindah antar dashboard divisi lewat switcher menu
        $defaultView = (Auth::check() && Auth::user()->isDeveloper()) ? 'developer' : 'sales';
        $adminView = request()->query('view', $defaultView);
        if (!in_array($adminView, ['sales', 'salesmanager', 'accounting', 'finance', 'logistic', 'workshop', 'projectmanager', 'clientvendor', 'developer'], true)) {
            $adminView = $defaultView;
        }

        $adminExtraData = match ($adminView) {
            'salesmanager' => (new SalesManagerDashboardService())->getSalesManagerDashboardData(),
            'accounting' => (new AccountingDashboardService())->getAccountingDashboardData(),
            'finance' => (new FinanceDashboardService())->getFinanceDashboardData(),
            'logistic' => (new LogisticDashboardService())->getLogisticDashboardData(),
            'workshop' => (new WorkshopDashboardService())->getWorkshopDashboardData(),
            'projectmanager' => (new ProjectManagerDashboardService())->getProjectManagerDashboardData(),
            'clientvendor' => (new ClientVendorDashboardService())->getDashboardData(),
            'developer' => (new DeveloperDashboardService())->getDeveloperDashboardData(),
            default => [],
        };

        $forecastData = [];
        if ($adminView === 'sales') {
            $forecastController = new \App\Http\Controllers\ForecastController();
            $forecastData = $forecastController->getForecastDataArray($firstSalesId, $yearNow);
        }

        return array_merge(
            compact(
                'showAdminWelcomeAlert',
                'yesterdayServiceReportCount',
                'serviceReportPeriodLabel',
                'sorted',
                'requestContract',
                'requestInvoice',
                'newCount',
                'listCount',
                'deliveryCount',
                'dataOverview',
                'noSaleProspect',
                'notulens',
                'totalProspectSupport',
                'totalForecast',
                'targetSales',
                'targetCrm',
                'sales',
                'totalPO',
                'filteredLeads',
                'filteredPO',
                'filteredCRM',
                'filteredVisit',
                'filteredDC',
                'filteredQuote',
                'filteredProspect',
                'allProspect',
                'poTotalPriceAdmin',
                'formattedTotalPriceAdmin',
                'totalQuotation',
                'totalProspect',
                'totalHotProspect',
                'totalLoss',
                'dataQuote',
                'dataLeads',
                'dataPO',
                'dataDc',
                'dataCRM',
                'dataVisit',
                'commentAdmin',
                'unreadCommentAdmin',
                'targett',
                'targetAllSales',
                'prCount',
                'adminView',
                'firstSales',
                'projectQuoteCount',
                'projectQuoteNominal',
                'marketingAgg',
                'salesOverviewData',
            ),
            $adminExtraData,
            $forecastData,
            ['year' => $yearNow]
        );
    }

    protected function formatNumber($number)
    {
        $satuan = ["", "ribu", "juta", "miliar", "triliun", "quadrillion"];

        $i = 0;
        while ($number >= 1000) {
            $number /= 1000;
            $i++;
        }

        $formattedAngka = number_format($number, 2, ',', '.');
        $formattedAngka = rtrim($formattedAngka, '0');
        $formattedAngka = rtrim($formattedAngka, '.');

        return $formattedAngka . ' ' . $satuan[$i];
    }

    /**
     * Gabungan getWeekDataDC + getWeekDataCRM + getWeekDataVisit — ketiganya query
     * tabel & join yang sama (Activities join client), cuma beda filter nama
     * activity. Sebelumnya 3 query terpisah, sekarang 1 query lalu dipecah di PHP.
     */
    protected function getWeekDataActivitiesCombined($sales)
    {
        $dateNow = Carbon::now();
        $yearNow = $dateNow->year;
        $monthNow = $dateNow->month;
        $firstDayOfMonth = "{$yearNow}-{$monthNow}-01";
        $lastDayOfMonth = date('Y-m-t', strtotime($firstDayOfMonth));

        $firstDayOfWeek = date('N', strtotime($firstDayOfMonth));
        $weekEnd = date('W', strtotime($firstDayOfMonth));
        $endWeek = date('W', strtotime($lastDayOfMonth));
        $weekStart = $firstDayOfWeek > 1 ? $weekEnd + 1 : $weekEnd;

        $rows = Activities::select('c.id_sales', DB::raw('WEEK(date, 4) as week_num'), 'activities.name', DB::raw('COUNT(*) as total'))
            ->join('client as c', 'activities.id_client', '=', 'c.id')
            ->whereBetween('date', [$firstDayOfMonth, $lastDayOfMonth])
            ->whereIn('activities.name', ['Daily Call', 'Follow Up', 'Crm', 'Visit'])
            ->where('status', 'Responded')
            ->groupBy('c.id_sales', DB::raw('WEEK(date, 4)'), 'activities.name')
            ->get();

        $dcData = $rows->whereIn('name', ['Daily Call', 'Follow Up'])
            ->groupBy('id_sales')
            ->map(function ($items) {
                $merged = collect();
                foreach ($items as $item) {
                    $merged[$item->week_num] = ($merged[$item->week_num] ?? 0) + $item->total;
                }
                return $merged;
            });

        $crmData = $rows->where('name', 'Crm')->groupBy('id_sales')->map(fn($items) => $items->pluck('total', 'week_num'));
        $visitData = $rows->where('name', 'Visit')->groupBy('id_sales')->map(fn($items) => $items->pluck('total', 'week_num'));

        return [
            'dc' => $this->buildWeeklyFullMonth($sales, $dcData, $weekStart, $endWeek, $yearNow),
            'crm' => $this->buildWeeklyFullMonth($sales, $crmData, $weekStart, $endWeek, $yearNow),
            'visit' => $this->buildWeeklyFullMonth($sales, $visitData, $weekStart, $endWeek, $yearNow),
        ];
    }

    private function buildWeeklyFullMonth($sales, $allData, $weekStart, $endWeek, $yearNow)
    {
        $fullMonthData = [];
        foreach ($sales as $sale) {
            $weeklyData = [];
            $salesData = $this->weeklyDataForSale($allData, $sale);

            for ($week = $weekStart; $week <= $endWeek; $week++) {
                $weekKey = "{$week}";
                $weekDays = date('t', strtotime("{$yearNow}-W{$weekKey}"));
                if ($weekDays >= 4) {
                    $weeklyData[$weekKey] = $salesData->get($week, 0);
                }
            }
            $fullMonthData[$sale->name] = $weeklyData;
        }
        return $fullMonthData;
    }

    protected function getWeekDataQuote($sales)
    {
        $dateNow = Carbon::now();
        $yearNow = $dateNow->year;
        $monthNow = $dateNow->month;
        $firstDayOfMonth = "{$yearNow}-{$monthNow}-01";
        $lastDayOfMonth = date('Y-m-t', strtotime($firstDayOfMonth));

        $firstDayOfWeek = date('N', strtotime($firstDayOfMonth));
        $weekEnd = date('W', strtotime($firstDayOfMonth));
        $endWeek = date('W', strtotime($lastDayOfMonth));
        $weekStart = $firstDayOfWeek > 1 ? $weekEnd + 1 : $weekEnd;

        $spData = Quotation::select('id_sales', DB::raw('WEEK(estimated_date, 4) as week_num'), DB::raw('COUNT(*) as total'))
            ->whereBetween('estimated_date', [$firstDayOfMonth, $lastDayOfMonth])
            ->where('level', '1')
            ->where('is_primary', '1')
            ->groupBy('id_sales', DB::raw('WEEK(estimated_date, 4)'))
            ->get();

        $unitData = UnitQuotation::select('id_sales', DB::raw('WEEK(date, 4) as week_num'), DB::raw('COUNT(*) as total'))
            ->whereBetween('date', [$firstDayOfMonth, $lastDayOfMonth])
            ->where('is_latest', 1)
            ->groupBy('id_sales', DB::raw('WEEK(date, 4)'))
            ->get();

        $allData = $spData->concat($unitData)
            ->groupBy('id_sales')
            ->map(function ($items) {
                return $items->groupBy('week_num')->map(fn($wItems) => $wItems->sum('total'));
            });

        return $this->buildWeeklyFullMonth($sales, $allData, $weekStart, $endWeek, $yearNow);
    }

    protected function getWeekDataPO($sales)
    {
        $dateNow = Carbon::now();
        $yearNow = $dateNow->year;
        $monthNow = $dateNow->month;
        $firstDayOfMonth = "{$yearNow}-{$monthNow}-01";
        $lastDayOfMonth = date('Y-m-t', strtotime($firstDayOfMonth));

        $firstDayOfWeek = date('N', strtotime($firstDayOfMonth));
        $weekEnd = date('W', strtotime($firstDayOfMonth));
        $endWeek = date('W', strtotime($lastDayOfMonth));
        $weekStart = $firstDayOfWeek > 1 ? $weekEnd + 1 : $weekEnd;

        $spData = Quotation::select('id_sales', DB::raw('WEEK(po_date, 4) as week_num'), DB::raw('COUNT(*) as total'))
            ->whereBetween('po_date', [$firstDayOfMonth, $lastDayOfMonth])
            ->where('status', '100')
            ->where('level', '1')
            ->where('is_primary', '1')
            ->groupBy('id_sales', DB::raw('WEEK(po_date, 4)'))
            ->get();

        $unitData = UnitQuotation::select('id_sales', DB::raw('WEEK(po_received, 4) as week_num'), DB::raw('COUNT(*) as total'))
            ->whereBetween('po_received', [$firstDayOfMonth, $lastDayOfMonth])
            ->where('status', 'po_received')
            ->where('is_latest', 1)
            ->groupBy('id_sales', DB::raw('WEEK(po_received, 4)'))
            ->get();

        $allData = $spData->concat($unitData)
            ->groupBy('id_sales')
            ->map(function ($items) {
                return $items->groupBy('week_num')->map(fn($wItems) => $wItems->sum('total'));
            });

        return $this->buildWeeklyFullMonth($sales, $allData, $weekStart, $endWeek, $yearNow);
    }

    protected function getWeekDataLeads($sales)
    {
        $dateNow = Carbon::now();
        $yearNow = $dateNow->year;
        $monthNow = $dateNow->month;
        $firstDayOfMonth = "{$yearNow}-{$monthNow}-01";
        $lastDayOfMonth = date('Y-m-t', strtotime($firstDayOfMonth));

        $firstDayOfWeek = date('N', strtotime($firstDayOfMonth));
        $weekEnd = date('W', strtotime($firstDayOfMonth));
        $endWeek = date('W', strtotime($lastDayOfMonth));
        $weekStart = $firstDayOfWeek > 1 ? $weekEnd + 1 : $weekEnd;

        $allData = Client::select('id_sales', DB::raw('WEEK(created_at, 4) as week_num'), DB::raw('COUNT(*) as total'))
            ->whereBetween('created_at', [$firstDayOfMonth, $lastDayOfMonth])
            ->groupBy('id_sales', DB::raw('WEEK(created_at, 4)'))
            ->get()
            ->groupBy('id_sales')
            ->map(fn($items) => $items->pluck('total', 'week_num'));

        return $this->buildWeeklyFullMonth($sales, $allData, $weekStart, $endWeek, $yearNow);
    }

    /**
     * Data overview untuk inisialisasi skeleton modal Overview.
     * Isi tabel rekap KPI mingguan dimuat lewat AJAX saat modal dibuka (detail-overview.weekly-kpi).
     */
    protected function getDataOverview()
    {
        $users = User::activeSalesAndProjectAdmins();

        return $users->map(function ($user) {
            return [
                'salesId' => $user->id,
                'sales' => $user->name,
            ];
        })->toArray();
    }
}
