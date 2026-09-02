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

        $poTotalPriceAdmin = Quotation::whereYear('po_date', $yearNow)
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

        $formattedTotalPriceAdmin = $this->formatNumber($poTotalPriceAdmin);

        $salesOrder = [4, 3, 2, 1, 32, 41, 16, 22];
        $sales = User::whereIn('role', ['Sales', 'Support'])
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

        $targetAllSales = Target::join('users as u', 'u.id', '=', 'target.id_sales')
            ->where('u.role', 'Sales')
            ->where('u.active', '1')
            ->sum('target.total');

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

        // Quotation stats untuk firstSalesId, digabung jadi satu query pakai
        // conditional aggregation + UnitQuotation
        $quoteAgg = Quotation::whereBetween('estimated_date', [$firstDayOfMonth, $lastDayOfMonth])
            ->where('id_sales', $firstSalesId)
            ->where('level', '1')
            ->where('is_primary', '1')
            ->selectRaw("
                COUNT(*) as filtered_quote,
                COALESCE(SUM(nett), 0) as total_quotation,
                COALESCE(SUM(CASE WHEN status IN ('20','30','40','60','80') THEN nett ELSE 0 END), 0) as total_prospect_support,
                COALESCE(SUM(CASE WHEN status = '80' THEN nett ELSE 0 END), 0) as total_forecast,
                COALESCE(SUM(CASE WHEN status IN ('80','90') THEN nett ELSE 0 END), 0) as total_hot_prospect,
                COALESCE(SUM(CASE WHEN status = '0' THEN nett ELSE 0 END), 0) as total_loss
            ")
            ->first();

        $unitQuoteAgg = UnitQuotation::whereBetween('date', [$firstDayOfMonth, $lastDayOfMonth])
            ->where('id_sales', $firstSalesId)
            ->where('is_latest', 1)
            ->selectRaw("
                COUNT(*) as filtered_quote,
                COALESCE(SUM(total - IFNULL(tax_amount, 0) - IFNULL(fee, 0)), 0) as total_quotation,
                COALESCE(SUM(CASE WHEN status IN ('draft','sent','negotiation','revision','hot_prospect') THEN (total - IFNULL(tax_amount, 0) - IFNULL(fee, 0)) ELSE 0 END), 0) as total_prospect_support,
                COALESCE(SUM(CASE WHEN status = 'hot_prospect' THEN (total - IFNULL(tax_amount, 0) - IFNULL(fee, 0)) ELSE 0 END), 0) as total_forecast,
                COALESCE(SUM(CASE WHEN status = 'hot_prospect' THEN (total - IFNULL(tax_amount, 0) - IFNULL(fee, 0)) ELSE 0 END), 0) as total_hot_prospect,
                COALESCE(SUM(CASE WHEN status = 'loss' THEN (total - IFNULL(tax_amount, 0) - IFNULL(fee, 0)) ELSE 0 END), 0) as total_loss
            ")
            ->first();

        $filteredQuote = (int) $quoteAgg->filtered_quote + (int) ($unitQuoteAgg->filtered_quote ?? 0);
        $totalQuotation = (float) $quoteAgg->total_quotation + (float) ($unitQuoteAgg->total_quotation ?? 0);
        $totalProspectSupport = (float) $quoteAgg->total_prospect_support + (float) ($unitQuoteAgg->total_prospect_support ?? 0);
        $totalForecast = (float) $quoteAgg->total_forecast + (float) ($unitQuoteAgg->total_forecast ?? 0);
        $totalHotProspect = (float) $quoteAgg->total_hot_prospect + (float) ($unitQuoteAgg->total_hot_prospect ?? 0);
        $totalLoss = (float) $quoteAgg->total_loss + (float) ($unitQuoteAgg->total_loss ?? 0);

        $totalProspect = Quotation::join('prospect as p', 'quotation.id', '=', 'p.id_quotation')
            ->whereNotNull('id_quotation')->whereYear('estimated_date', $yearNow)->whereMonth('estimated_date', $monthNow)
            ->where('quotation.id_sales', $firstSalesId)->whereIn('status', ['80', '90'])->where('quotation.level', '1')->where('is_primary', '1')->sum('nett');

        // totalPO & filteredPO (sum + count dari filter yang sama) digabung jadi
        // 1 query per tabel (Quotation, UnitQuotation) — sebelumnya 4 query.
        $poQuotationAgg = Quotation::whereBetween('po_date', [$firstDayOfMonth, $lastDayOfMonth])
            ->where('id_sales', $firstSalesId)
            ->where('status', '100')
            ->where('level', '1')
            ->where('is_primary', '1')
            ->selectRaw('COALESCE(SUM(nett), 0) as total_nett, COUNT(*) as cnt')
            ->first();

        $poUnitAgg = UnitQuotation::where('status', 'po_received')
            ->where('is_latest', 1)
            ->whereYear('po_received', $yearNow)
            ->whereMonth('po_received', $monthNow)
            ->where('id_sales', $firstSalesId)
            ->selectRaw('COALESCE(SUM(total - tax_amount), 0) as total_nett, COUNT(*) as cnt')
            ->first();

        $totalPO = (float) $poQuotationAgg->total_nett + (float) $poUnitAgg->total_nett;
        $filteredPO = (int) $poQuotationAgg->cnt + (int) $poUnitAgg->cnt;

        $filteredLeads = Client::whereBetween('created_at', [$firstDayOfMonth . ' 00:00:00', $lastDayOfMonth . ' 23:59:59'])->where('id_sales', $firstSalesId)->count();

        // filteredDC & filteredVisit sama-sama Activities join client dengan filter
        // identik kecuali nama activity — digabung jadi 1 query (sebelumnya 2 query).
        $dcVisitAgg = Activities::join('client as c', 'activities.id_client', '=', 'c.id')
            ->whereBetween('date', [$firstDayOfMonth, $lastDayOfMonth])->where('c.id_sales', $firstSalesId)
            ->where('status', 'Responded')
            ->selectRaw("
                COALESCE(SUM(CASE WHEN activities.name IN ('Daily Call','Follow Up') THEN 1 ELSE 0 END), 0) as filtered_dc,
                COALESCE(SUM(CASE WHEN activities.name = 'Visit' THEN 1 ELSE 0 END), 0) as filtered_visit
            ")
            ->first();
        $filteredDC = (int) $dcVisitAgg->filtered_dc;
        $filteredVisit = (int) $dcVisitAgg->filtered_visit;

        $filteredCRM = Activities::join('client as c', 'activities.id_client', '=', 'c.id')
            ->join(DB::raw('(SELECT id_client, status FROM crm_status WHERE id IN (SELECT MAX(id) FROM crm_status GROUP BY id_client)) as cs'), 'c.id', '=', 'cs.id_client')
            ->whereYear('date', $yearNow)->whereMonth('date', $monthNow)->where('c.id_sales', $firstSalesId)
            ->where('activities.status', 'Responded')->where('activities.name', 'CRM')->where('cs.status', '2')->count(DB::raw('DISTINCT c.id'));

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

        $weekDataSales = User::activeSalesAndProjectAdmins();

        $weekActivities = $this->getWeekDataActivitiesCombined($weekDataSales);
        $dataDc = $weekActivities['dc'];
        $dataCRM = $weekActivities['crm'];
        $dataVisit = $weekActivities['visit'];
        $dataQuote = $this->getWeekDataQuote($weekDataSales);
        $dataOverview = $this->getDataOverview();

        $dataLeads = $this->getWeekDataLeads($weekDataSales);
        $dataPO = $this->getWeekDataPO($weekDataSales);

        $targetCrm = Client::join(DB::raw('(SELECT id_client, status FROM crm_status WHERE id IN (SELECT MAX(id) FROM crm_status GROUP BY id_client)) as cs'), 'client.id', '=', 'cs.id_client')
            ->where('role', 'Customers')
            ->where('cs.status', '2')
            ->select('id_sales', DB::RAW('COUNT(*) as total'))
            ->groupBy('id_sales')
            ->pluck('total', 'id_sales')->toArray();

        // Comment Buat Admin
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

        // Ambil semua komentar yang relevan
        $commentAdmin = $commentsQuery->orderBy('comment.id_status')
            ->orderByDesc('comment.created_at')
            ->get(['q.id as idQ', 'comment.id as idC', 'comment.id_user', 'comment.level', 'comment.comment', 'comment.date', 'q.no_quote', 'u.name', 'u.image']);

        // Filter untuk komentar dengan level '1'
        $unreadCommentAdmin = $commentsQuery->where('comment.level', '1')
            ->orderBy('comment.id_status')
            ->orderByDesc('comment.created_at')
            ->get(['q.id as idQ', 'comment.id as idC', 'comment.id_user', 'comment.level', 'comment.comment', 'comment.date', 'q.no_quote', 'u.name', 'u.image']);

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

        // Admin bisa berpindah antar dashboard divisi lewat dropdown menu
        $adminView = request()->query('view', 'sales');
        if (!in_array($adminView, ['sales', 'salesmanager', 'accounting', 'finance', 'logistic', 'workshop'], true)) {
            $adminView = 'sales';
        }

        $adminExtraData = match ($adminView) {
            'salesmanager' => (new SalesManagerDashboardService())->getSalesManagerDashboardData(),
            'accounting' => (new AccountingDashboardService())->getAccountingDashboardData(),
            'finance' => (new FinanceDashboardService())->getFinanceDashboardData(),
            'logistic' => (new LogisticDashboardService())->getLogisticDashboardData(),
            'workshop' => (new WorkshopDashboardService())->getWorkshopDashboardData(),
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

    protected function getDataOverview()
    {
        $month = Carbon::now()->month;
        $year = Carbon::now()->year;
        $firstDayOfMonth = "{$year}-{$month}-01";
        $lastDayOfMonth = date('Y-m-t', strtotime($firstDayOfMonth));

        $users = User::activeSalesAndProjectAdmins();
        $users->load('clients');

        $allDC = Activities::join('client as c', 'activities.id_client', '=', 'c.id')
            ->whereBetween('date', [$firstDayOfMonth, $lastDayOfMonth])
            ->where('status', 'Responded')
            ->whereIn('name', ['Daily Call', 'Follow Up'])
            ->groupBy('c.id')
            ->get();

        $allActivities = Activities::join('client as c', 'activities.id_client', '=', 'c.id')
            ->whereBetween('date', [$firstDayOfMonth, $lastDayOfMonth])
            ->where('status', 'Responded')
            ->where('name', 'CRM')
            ->groupBy('c.id')
            ->get();

        $allLeads = Client::whereBetween('created_at', [$firstDayOfMonth . ' 00:00:00', $lastDayOfMonth . ' 23:59:59'])
            ->get();

        $allQuotes = Quotation::whereBetween('estimated_date', [$firstDayOfMonth, $lastDayOfMonth])
            ->where('level', '1')
            ->where('is_primary', '1')
            ->get();

        $allPOs = Quotation::whereBetween('po_date', [$firstDayOfMonth, $lastDayOfMonth])
            ->where('status', '100')
            ->where('level', '1')
            ->where('is_primary', '1')
            ->get();

        $data = [];

        foreach ($users as $user) {
            $leadCounts = collect([1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0]);
            $dcCounts = collect([1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0]);
            $crmCounts = collect([1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0]);
            $quoteCounts = collect([1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0]);
            $poCounts = collect([1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0]);

            // Baris "Sales Project" gabungan punya beberapa id_sales sekaligus
            // ($user->id_sales_list) — filter di bawah pakai whereIn, bukan cuma
            // id satu user, biar datanya beneran teragregat dari semua Admin itu.
            $ids = $user->id_sales_list ?? [$user->id];
            $clientIds = count($ids) > 1
                ? Client::whereIn('id_sales', $ids)->pluck('id')
                : $user->clients->pluck('id');
            $userDC = $allDC->whereIn('id_sales', $ids);
            $userCRM = $allActivities->whereIn('id_client', $clientIds);
            $userLeads = $allLeads->whereIn('id_sales', $ids);
            $userQuotes = $allQuotes->whereIn('id_sales', $ids);
            $userPOs = $allPOs->whereIn('id_sales', $ids);

            foreach ($userCRM as $activity) {
                $week = Carbon::parse($activity->date)->weekOfMonth;
                $crmCounts->put($week, $crmCounts->get($week) + 1);
            }

            foreach ($userDC as $dc) {
                $week = Carbon::parse($dc->date)->weekOfMonth;
                $dcCounts->put($week, $dcCounts->get($week) + 1);
            }

            foreach ($userLeads as $lead) {
                $week = Carbon::parse($lead->created_at)->weekOfMonth;
                $leadCounts->put($week, $leadCounts->get($week) + 1);
            }

            foreach ($userQuotes as $quote) {
                $week = Carbon::parse($quote->estimated_date)->weekOfMonth;
                $quoteCounts->put($week, $quoteCounts->get($week) + 1);
            }

            foreach ($userPOs as $po) {
                $week = Carbon::parse($po->po_date)->weekOfMonth;
                $poCounts->put($week, $poCounts->get($week) + 1);
            }

            $data[] = [
                'salesId' => $user->id,
                'sales' => $user->name,
                'leads' => $leadCounts,
                'dc' => $dcCounts,
                'crm' => $crmCounts,
                'quote' => $quoteCounts,
                'po' => $poCounts,
            ];
        }

        return $data;
    }
}
