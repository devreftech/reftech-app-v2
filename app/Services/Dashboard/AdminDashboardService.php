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
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AdminDashboardService
{
    /**
     * Get dashboard data payload for Admin role
     */
    public function getDashboardData($sorted, $sales, $notulens, $yearNow, $monthNow, $dateNow)
    {
        $prCount = PurchaseRequest::where('status', '0')->count();

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

        $totalProspectSupport = Quotation::whereYear('estimated_date', $yearNow)->whereMonth('estimated_date', $monthNow)
            ->where('id_sales', $firstSalesId)->whereIn('status', ['20', '30', '40', '60', '80'])->where('level', '1')->where('is_primary', '1')->sum('nett');

        $totalForecast = Quotation::whereYear('estimated_date', $yearNow)->whereMonth('estimated_date', $monthNow)
            ->where('id_sales', $firstSalesId)->where('status', '80')->where('level', '1')->where('is_primary', '1')->sum('nett');

        $totalQuotation = Quotation::whereYear('estimated_date', $yearNow)->whereMonth('estimated_date', $monthNow)
            ->where('id_sales', $firstSalesId)->where('level', '1')->where('is_primary', '1')->sum('nett');

        $totalProspect = Quotation::join('prospect as p', 'quotation.id', '=', 'p.id_quotation')
            ->whereNotNull('id_quotation')->whereYear('estimated_date', $yearNow)->whereMonth('estimated_date', $monthNow)
            ->where('quotation.id_sales', $firstSalesId)->whereIn('status', ['80', '90'])->where('quotation.level', '1')->where('is_primary', '1')->sum('nett');

        $totalHotProspect = Quotation::whereYear('estimated_date', $yearNow)->whereMonth('estimated_date', $monthNow)
            ->where('id_sales', $firstSalesId)->whereIn('status', ['80', '90'])->where('level', '1')->where('is_primary', '1')->sum('nett');

        $totalLoss = Quotation::whereYear('estimated_date', $yearNow)->whereMonth('estimated_date', $monthNow)
            ->where('id_sales', $firstSalesId)->where('status', '0')->where('level', '1')->where('is_primary', '1')->sum('nett');

        $totalPO = Quotation::whereYear('po_date', $yearNow)
            ->whereMonth('po_date', $monthNow)
            ->where('id_sales', $firstSalesId)
            ->where('status', '100')
            ->where('level', '1')
            ->where('is_primary', '1')
            ->sum('nett')
            + UnitQuotation::where('status', 'po_received')
                ->where('is_latest', 1)
                ->whereYear('po_received', $yearNow)
                ->whereMonth('po_received', $monthNow)
                ->where('id_sales', $firstSalesId)
                ->sum(DB::raw('total - tax_amount'));

        $filteredLeads = Client::whereYear('created_at', $yearNow)->whereMonth('created_at', $monthNow)->where('id_sales', $firstSalesId)->count();

        $filteredDC = Activities::join('client as c', 'activities.id_client', '=', 'c.id')
            ->whereYear('date', $yearNow)->whereMonth('date', $monthNow)->where('c.id_sales', $firstSalesId)
            ->where('status', 'Responded')->whereIn('activities.name', ['Daily Call', 'Follow Up'])->count();

        $filteredCRM = Activities::join('client as c', 'activities.id_client', '=', 'c.id')
            ->join(DB::raw('(SELECT id_client, status FROM crm_status WHERE id IN (SELECT MAX(id) FROM crm_status GROUP BY id_client)) as cs'), 'c.id', '=', 'cs.id_client')
            ->whereYear('date', $yearNow)->whereMonth('date', $monthNow)->where('c.id_sales', $firstSalesId)
            ->where('activities.status', 'Responded')->where('activities.name', 'CRM')->where('cs.status', '2')->count(DB::raw('DISTINCT c.id'));

        $filteredQuote = Quotation::whereYear('estimated_date', $yearNow)->whereMonth('estimated_date', $monthNow)
            ->where('id_sales', $firstSalesId)->where('level', '1')->where('is_primary', '1')->count();

        $filteredProspect = Prospect::whereNotNull('id_quotation')->whereMonth('date', $monthNow)->whereYear('date', $yearNow)->count();

        $allProspect = Prospect::whereMonth('date', $monthNow)->whereYear('date', $yearNow)->count();

        $filteredPO = Quotation::whereYear('po_date', $yearNow)
            ->whereMonth('po_date', $monthNow)
            ->where('id_sales', $firstSalesId)
            ->where('status', '100')
            ->where('level', '1')
            ->where('is_primary', '1')
            ->count()
            + UnitQuotation::where('status', 'po_received')
                ->where('is_latest', 1)
                ->whereYear('po_received', $yearNow)
                ->whereMonth('po_received', $monthNow)
                ->where('id_sales', $firstSalesId)
                ->count();

        $filteredVisit = Activities::join('client as c', 'activities.id_client', '=', 'c.id')
            ->whereYear('date', $yearNow)->whereMonth('date', $monthNow)->where('c.id_sales', $firstSalesId)
            ->where('status', 'Responded')->where('name', 'Visit')->count();

        $weekDataSales = User::where('role', 'sales')->get();

        $dataDc = $this->getWeekDataDC($weekDataSales);
        $dataCRM = $this->getWeekDataCRM($weekDataSales);
        $dataVisit = $this->getWeekDataVisit($weekDataSales);
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

    protected function getWeekDataDC($sales)
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

        $allData = Activities::select('c.id_sales', DB::raw('WEEK(date, 4) as week_num'), DB::raw('COUNT(*) as total'))
            ->join('client as c', 'activities.id_client', '=', 'c.id')
            ->whereBetween('date', [$firstDayOfMonth, $lastDayOfMonth])
            ->whereIn('activities.name', ['Daily Call', 'Follow Up'])
            ->where('status', 'Responded')
            ->groupBy('c.id_sales', DB::raw('WEEK(date, 4)'))
            ->get()
            ->groupBy('id_sales')
            ->map(fn($items) => $items->pluck('total', 'week_num'));

        $fullMonthData = [];
        foreach ($sales as $sale) {
            $weeklyData = [];
            $salesData = $allData->get($sale->id, collect());

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

    protected function getWeekDataCRM($sales)
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

        $allData = Activities::select('c.id_sales', DB::raw('WEEK(date, 4) as week_num'), DB::raw('COUNT(*) as total'))
            ->join('client as c', 'activities.id_client', '=', 'c.id')
            ->whereBetween('date', [$firstDayOfMonth, $lastDayOfMonth])
            ->where('activities.name', 'Crm')
            ->where('status', 'Responded')
            ->groupBy('c.id_sales', DB::raw('WEEK(date, 4)'))
            ->get()
            ->groupBy('id_sales')
            ->map(fn($items) => $items->pluck('total', 'week_num'));

        $fullMonthData = [];
        foreach ($sales as $sale) {
            $weeklyData = [];
            $salesData = $allData->get($sale->id, collect());

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

    protected function getWeekDataVisit($sales)
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

        $allData = Activities::select('c.id_sales', DB::raw('WEEK(date, 4) as week_num'), DB::raw('COUNT(*) as total'))
            ->join('client as c', 'activities.id_client', '=', 'c.id')
            ->whereBetween('date', [$firstDayOfMonth, $lastDayOfMonth])
            ->where('activities.name', 'Visit')
            ->where('status', 'Responded')
            ->groupBy('c.id_sales', DB::raw('WEEK(date, 4)'))
            ->get()
            ->groupBy('id_sales')
            ->map(fn($items) => $items->pluck('total', 'week_num'));

        $fullMonthData = [];
        foreach ($sales as $sale) {
            $weeklyData = [];
            $salesData = $allData->get($sale->id, collect());

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

        $allData = Quotation::select('id_sales', DB::raw('WEEK(estimated_date, 4) as week_num'), DB::raw('COUNT(*) as total'))
            ->whereBetween('estimated_date', [$firstDayOfMonth, $lastDayOfMonth])
            ->where('level', '1')
            ->where('is_primary', '1')
            ->groupBy('id_sales', DB::raw('WEEK(estimated_date, 4)'))
            ->get()
            ->groupBy('id_sales')
            ->map(fn($items) => $items->pluck('total', 'week_num'));

        $fullMonthData = [];
        foreach ($sales as $sale) {
            $weeklyData = [];
            $salesData = $allData->get($sale->id, collect());

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

        $allData = Quotation::select('id_sales', DB::raw('WEEK(po_date, 4) as week_num'), DB::raw('COUNT(*) as total'))
            ->whereBetween('po_date', [$firstDayOfMonth, $lastDayOfMonth])
            ->where('status', '100')
            ->where('level', '1')
            ->where('is_primary', '1')
            ->groupBy('id_sales', DB::raw('WEEK(po_date, 4)'))
            ->get()
            ->groupBy('id_sales')
            ->map(fn($items) => $items->pluck('total', 'week_num'));

        $fullMonthData = [];
        foreach ($sales as $sale) {
            $weeklyData = [];
            $salesData = $allData->get($sale->id, collect());

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

        $fullMonthData = [];
        foreach ($sales as $sale) {
            $weeklyData = [];
            $salesData = $allData->get($sale->id, collect());

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

    protected function getDataOverview()
    {
        $month = Carbon::now()->month;
        $year = Carbon::now()->year;

        $users = User::with('clients')->where('role', 'Sales')->get();

        $allDC = Activities::join('client as c', 'activities.id_client', '=', 'c.id')
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->where('status', 'Responded')
            ->whereIn('name', ['Daily Call', 'Follow Up'])
            ->groupBy('c.id')
            ->get();

        $allActivities = Activities::join('client as c', 'activities.id_client', '=', 'c.id')
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->where('status', 'Responded')
            ->where('name', 'CRM')
            ->groupBy('c.id')
            ->get();

        $allLeads = Client::whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->get();

        $allQuotes = Quotation::whereMonth('estimated_date', $month)
            ->whereYear('estimated_date', $year)
            ->where('level', '1')
            ->where('is_primary', '1')
            ->get();

        $allPOs = Quotation::whereMonth('po_date', $month)
            ->whereYear('po_date', $year)
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

            $clientIds = $user->clients->pluck('id');
            $userDC = $allDC->where('id_sales', $user->id);
            $userCRM = $allActivities->whereIn('id_client', $clientIds);
            $userLeads = $allLeads->where('id_sales', $user->id);
            $userQuotes = $allQuotes->where('id_sales', $user->id);
            $userPOs = $allPOs->where('id_sales', $user->id);

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
