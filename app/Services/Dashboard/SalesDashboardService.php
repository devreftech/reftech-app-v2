<?php

namespace App\Services\Dashboard;

use App\Models\Client;
use App\Models\Issues;
use App\Models\Prospect;
use App\Models\Quotation;
use App\Models\UnitQuotation;
use App\Models\Target;
use App\Models\Activities;
use App\Models\Comment;
use App\Models\SalesOnline;
use App\Models\Reports;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class SalesDashboardService
{
    /**
     * Get dashboard data payload for Sales role
     */
    public function getDashboardData($salesUserId, $yearNow, $monthNow, $dateNow, $sorted, $sales, $notulens)
    {
        $allowedUserIds = [1, 2, 3, 4, 32];
        $showWelcomeAlert = false;

        if (in_array((int) $salesUserId, $allowedUserIds, true)) {
            $welcomeAlertKey = 'sales_welcome_alert_' . $salesUserId . '_' . $dateNow->toDateString();
            if (!Cache::has($welcomeAlertKey)) {
                Cache::put($welcomeAlertKey, true, $dateNow->copy()->endOfDay());
                $showWelcomeAlert = true;
            }
        }

        // 1A. Active Leads Count (For IDs 1, 2, 3, 32)
        $activeLeadsCount = Client::where('id_sales', $salesUserId)
            ->where('role', 'Leads')
            ->count();

        // 1B. CRM Potensial Count (For ID 4)
        $crmPotensialCount = Client::join(DB::raw('(SELECT id_client, status FROM crm_status WHERE id IN (SELECT MAX(id) FROM crm_status GROUP BY id_client)) as cs'), 'client.id', '=', 'cs.id_client')
            ->where('client.id_sales', $salesUserId)
            ->where('cs.status', '2')
            ->count();

        // 2. Total Penawaran Aktif (Belum PO)
        $activeSpQuotationCount = Quotation::where('id_sales', $salesUserId)
            ->where('level', '1')
            ->where('is_primary', '1')
            ->whereNotIn('status', ['100', '0'])
            ->count();

        $activeUnitQuotationCount = UnitQuotation::where('id_sales', $salesUserId)
            ->where('is_latest', 1)
            ->whereNotIn('status', ['po_received', 'loss'])
            ->count();

        $totalActiveQuotationCount = $activeSpQuotationCount + $activeUnitQuotationCount;

        // 3. Total Hot Prospect (Mau Closing)
        $hotProspectSpQuery = Quotation::where('id_sales', $salesUserId)
            ->where('level', '1')
            ->where('is_primary', '1')
            ->where('status', '80');

        $hotProspectSpCount = (clone $hotProspectSpQuery)->count();
        $hotProspectSpNominal = (clone $hotProspectSpQuery)->sum('nett');

        $hotProspectUnitQuery = UnitQuotation::where('id_sales', $salesUserId)
            ->where('is_latest', 1)
            ->where('status', 'hot_prospect');

        $hotProspectUnitCount = (clone $hotProspectUnitQuery)->count();
        $hotProspectUnitNominal = (clone $hotProspectUnitQuery)->sum(DB::raw('total - tax_amount'));

        $totalHotProspectCount = $hotProspectSpCount + $hotProspectUnitCount;
        $totalHotProspectNominal = $hotProspectSpNominal + $hotProspectUnitNominal;

        $clients = Client::where('id_sales', $salesUserId)->get();
        $issue = Issues::all();
        
        $leveledProspect = Prospect::whereNULL('level')->where('id_sales', $salesUserId)->count();
        $weekPerMonth = $this->getWeekperMonth();
        $dailyCall = $this->getDailyCallSales($salesUserId, $yearNow, $monthNow);
        $customers = $this->getCustomersSales($salesUserId, $yearNow, $monthNow);
        $quotation = $this->getQuotationSales($salesUserId, $yearNow, $monthNow);
        $po = $this->getPoSales($salesUserId, $yearNow, $monthNow);
        $leads = $this->getLeadsSales($salesUserId, $yearNow, $monthNow);
        $visit = $this->getVisitSales($salesUserId, $yearNow, $monthNow);

        $poTotalPrice = Quotation::whereYear('po_date', $yearNow)
            ->whereMonth("po_date", $monthNow)
            ->where("id_sales", $salesUserId)
            ->where("status", "100")
            ->where('level', '1')
            ->where('is_primary', '1')
            ->sum('nett')
            + UnitQuotation::where('status', 'po_received')
            ->where('is_latest', 1)
            ->whereYear('po_received', $yearNow)
            ->whereMonth('po_received', $monthNow)
            ->where('id_sales', $salesUserId)
            ->sum(DB::raw('total - tax_amount'));

        $formattedTotalPrice = $this->formatNumber($poTotalPrice);
        $target = Target::where('id_sales', $salesUserId)->first();
        $prospects = Prospect::where('id_sales', $salesUserId)->whereNull('level')->get();
        
        $nextFollow = Activities::join('client as c', 'c.id', '=', 'activities.id_client')
            ->join('users as s', 'c.id_sales', '=', 's.id')
            ->select(['activities.id', 'c.company', 'activities.note', 'activities.follow_up as start', 'activities.follow_up as end', 'activities.name'])
            ->where('c.id_sales', $salesUserId)
            ->groupBy('c.company')
            ->orderBy('activities.follow_up')
            ->get();

        $quotationComment = Quotation::join('change_status as c', 'c.id_quotation', '=', 'quotation.id')
            ->join('comment as o', 'o.id_status', '=', 'c.id')
            ->join('users as u', 'u.id', '=', 'o.id_user')
            ->where('quotation.id_sales', $salesUserId)
            ->where('o.type', 'quotation')
            ->where('o.id_user', '!=', $salesUserId)
            ->orderBy('o.date', 'DESC')
            ->select(['quotation.id as idQ', 'o.id as idC', 'o.id_user', 'o.level', 'o.comment', 'o.date', 'o.type', 'quotation.no_quote', 'u.name', 'u.image']);

        $prospectComment = Comment::join('prospect as p', 'comment.id_prospect', '=', 'p.id')
            ->join('users as u', 'u.id', '=', 'comment.id_user')
            ->join('pic as pi', 'pi.id', '=', 'p.id_pic')
            ->join('client as c', 'c.id', '=', 'pi.id_client')
            ->where('p.id_sales', $salesUserId)
            ->where('comment.type', 'prospect')
            ->where('comment.id_user', '!=', $salesUserId)
            ->orderBy('comment.date', 'DESC')
            ->select(['p.id as idP', 'comment.id as idC', 'comment.id_user', 'comment.level', 'comment.comment', 'comment.date', 'comment.type', 'c.company', 'u.name', 'u.image']);

        $comment = (clone $quotationComment)->union(clone $prospectComment)
            ->orderBy('date', 'DESC')
            ->take(5)
            ->get();
        $unreadComment = (clone $quotationComment)->union(clone $prospectComment)
            ->orderBy('date', 'DESC')
            ->where('o.level', '1')
            ->take(5)
            ->get();

        // Sales Online
        $weeklyOnline = SalesOnline::where('id_sales', $salesUserId)
            ->whereIn('type', ['Akurasi', 'Delivery', 'Response', 'Rating', 'Customer'])
            ->whereYear('date', $yearNow)
            ->whereRaw('WEEK(date, 1) = ?', [$dateNow->weekOfYear])
            ->get()->keyBy('type');

        $akurasi = $weeklyOnline->get('Akurasi');
        $delivery = $weeklyOnline->get('Delivery');
        $response = $weeklyOnline->get('Response');
        $rating = $weeklyOnline->get('Rating');
        $customer = $weeklyOnline->get('Customer');

        $todayOnline = SalesOnline::where('id_sales', $salesUserId)
            ->whereIn('type', ['Video', 'SW', 'product'])
            ->whereDate('date', $dateNow)
            ->get()->groupBy('type');

        $video = $todayOnline->get('Video', collect())->first();
        $sw = $todayOnline->get('SW', collect())->first();
        $product = $todayOnline->get('product', collect());

        $monthlyOnline = SalesOnline::where('id_sales', $salesUserId)
            ->whereIn('type', ['Akurasi', 'Delivery', 'Response', 'Rating', 'Customer', 'Video', 'SW'])
            ->whereMonth('date', $monthNow)
            ->whereYear('date', $yearNow)
            ->get()->groupBy('type');

        $akurasiCount = $monthlyOnline->get('Akurasi', collect());
        $deliveryCount = $monthlyOnline->get('Delivery', collect());
        $responseCount = $monthlyOnline->get('Response', collect());
        $ratingCount = $monthlyOnline->get('Rating', collect());
        $customerCount = $monthlyOnline->get('Customer', collect());
        $videoCount = $monthlyOnline->get('Video', collect());
        $SWCount = $monthlyOnline->get('SW', collect());

        $productCount = SalesOnline::where('id_sales', $salesUserId)
            ->where('type', 'Product')
            ->whereMonth('date', $monthNow)
            ->whereYear('date', $yearNow)
            ->count();

        $POCount = Quotation::where('id_sales', $salesUserId)
            ->where('is_primary', '1')
            ->where('status', '100')
            ->where('level', '1')
            ->whereMonth('po_date', Carbon::now())
            ->whereYear('po_date', Carbon::now())
            ->count();

        $jumlahCustomer = Client::join(DB::raw('(SELECT id_client, status FROM crm_status WHERE id IN (SELECT MAX(id) FROM crm_status GROUP BY id_client)) as cs'), 'client.id', '=', 'cs.id_client')
            ->where('role', 'Customers')
            ->where('id_sales', $salesUserId)
            ->where('cs.status', '2')
            ->count();

        $reportsCount = Reports::join('machine as m', 'm.id', '=', 'reports.id_machine')
            ->join('client as c', 'c.id', '=', 'm.id_client')
            ->join('users as u', 'u.id', '=', 'c.id_sales')
            ->where('u.id', $salesUserId)
            ->where('reports.viewed', 0)
            ->count();

        $salesCharts = $this->getSalesDashboardCharts($salesUserId, $leads->count(), $quotation->count(), $po->count());

        $forecastController = new \App\Http\Controllers\ForecastController();
        $forecastData = $forecastController->getForecastDataArray($salesUserId, $yearNow);

        return array_merge(compact(
            'sorted',
            'reportsCount',
            'sales',
            'akurasi',
            'delivery',
            'response',
            'rating',
            'video',
            'sw',
            'customer',
            'product',
            'akurasiCount',
            'deliveryCount',
            'responseCount',
            'ratingCount',
            'videoCount',
            'customerCount',
            'SWCount',
            'POCount',
            'productCount',
            'jumlahCustomer',
            'notulens',
            'prospects',
            'leveledProspect',
            'formattedTotalPrice',
            'weekPerMonth',
            'target',
            'poTotalPrice',
            'visit',
            'dailyCall',
            'quotation',
            'po',
            'leads',
            'issue',
            'clients',
            'customers',
            'unreadComment',
            'comment',
            'showWelcomeAlert',
            'activeLeadsCount',
            'crmPotensialCount',
            'totalActiveQuotationCount',
            'totalHotProspectCount',
            'totalHotProspectNominal'
        ), $salesCharts, $forecastData);
    }

    protected function getQuotationSales($salesUserId, $yearNow, $monthNow)
    {
        return Quotation::whereYear('estimated_date', $yearNow)
            ->whereMonth("estimated_date", $monthNow)
            ->where("id_sales", $salesUserId)
            ->where('level', '1')
            ->where('is_primary', '1')
            ->get();
    }

    protected function getVisitSales($salesUserId, $yearNow, $monthNow)
    {
        return Activities::select('activities.*')
            ->join('client as c', 'activities.id_client', '=', 'c.id')
            ->join('users as u', 'c.id_sales', '=', 'u.id')
            ->whereYear('date', $yearNow)
            ->whereMonth("date", $monthNow)
            ->where('u.id', $salesUserId)
            ->where('status', 'Responded')
            ->where('activities.name', 'Visit')
            ->count();
    }

    protected function getDailyCallSales($salesUserId, $yearNow, $monthNow)
    {
        return Activities::select('activities.*')
            ->join('client as c', 'activities.id_client', '=', 'c.id')
            ->join('users as u', 'c.id_sales', '=', 'u.id')
            ->whereYear('date', $yearNow)
            ->whereMonth("date", $monthNow)
            ->where('u.id', $salesUserId)
            ->where('status', 'Responded')
            ->whereIn('activities.name', ['Daily Call', 'Follow Up'])
            ->distinct('c.id')
            ->count();
    }

    protected function getPoSales($salesUserId, $yearNow, $monthNow)
    {
        $po = Quotation::whereYear('po_date', $yearNow)
            ->whereMonth("po_date", $monthNow)
            ->where("id_sales", $salesUserId)
            ->where("status", "100")
            ->where('level', '1')
            ->where('is_primary', '1')
            ->get();

        $unitPo = UnitQuotation::where('status', 'po_received')
            ->where('is_latest', 1)
            ->whereYear('po_received', $yearNow)
            ->whereMonth('po_received', $monthNow)
            ->where('id_sales', $salesUserId)
            ->get();

        return $po->concat($unitPo);
    }

    protected function getLeadsSales($salesUserId, $yearNow, $monthNow)
    {
        return Client::whereYear('created_at', $yearNow)
            ->whereMonth("created_at", $monthNow)
            ->where("id_sales", $salesUserId)
            ->get();
    }

    protected function getCustomersSales($salesUserId, $yearNow, $monthNow)
    {
        return Activities::select('activities.*')
            ->join('client as c', 'activities.id_client', '=', 'c.id')
            ->join('users as u', 'c.id_sales', '=', 'u.id')
            ->whereYear('date', $yearNow)
            ->whereMonth("date", $monthNow)
            ->where('u.id', $salesUserId)
            ->where('status', 'Responded')
            ->where('activities.name', 'CRM')
            ->distinct('c.id')
            ->count();
    }

    protected function getWeekperMonth()
    {
        $dateNow = Carbon::now();
        $yearNow = $dateNow->year;
        $monthNow = $dateNow->month;
        $firstDayOfMonth = "{$yearNow}-{$monthNow}-01";
        $lastDayOfMonth = date('Y-m-t', strtotime($firstDayOfMonth));

        $weekEnd = date('W', strtotime($lastDayOfMonth));
        $fullMonthData = [];
        for ($week = 1; $week <= $weekEnd; $week++) {
            $weekKey = "{$week}";

            $weekDays = date('t', strtotime($weekKey));
            if ($weekDays >= 4) {
                $fullMonthData[$weekKey] = [
                    'week' => $weekKey,
                ];
            }
        }
        return $fullMonthData;
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
        $formattedAngka = rtrim($formattedAngka, ',');

        return $formattedAngka . " " . $satuan[$i];
    }

    private function getSalesDashboardCharts(int $salesId, int $leadsCount, int $quotationCount, int $poCount): array
    {
        $dateNow = Carbon::now();
        $monthNow = $dateNow->month;
        $yearNow = $dateNow->year;

        $salesFunnelLabels = ['New Leads', 'Quotation', 'PO Won'];
        $salesFunnelSeries = [$leadsCount, $quotationCount, $poCount];
        $salesWinRate = $quotationCount > 0 ? round(($poCount / $quotationCount) * 100, 1) : 0;

        $statusCounts = Quotation::where('id_sales', $salesId)
            ->where('level', '1')
            ->where('is_primary', '1')
            ->whereYear('estimated_date', $yearNow)
            ->whereMonth('estimated_date', $monthNow)
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $inProgress = 0;
        $hotProspect = 0;
        foreach (['20', '30', '40', '60'] as $s) {
            $inProgress += $statusCounts->get($s, 0);
        }
        foreach (['80', '90'] as $s) {
            $hotProspect += $statusCounts->get($s, 0);
        }

        $salesStatusLabels = ['Loss', 'In Progress', 'Hot Prospect', 'PO Won'];
        $salesStatusSeries = [
            $statusCounts->get('0', 0),
            $inProgress,
            $hotProspect,
            $statusCounts->get('100', 0),
        ];

        $monthlyRangeStart = Carbon::now()->subMonths(5)->startOfMonth();
        $monthlyRangeEnd = Carbon::now()->endOfMonth();

        $quoteCountByMonth = Quotation::where('id_sales', $salesId)
            ->where('level', '1')->where('is_primary', '1')
            ->whereBetween('estimated_date', [$monthlyRangeStart, $monthlyRangeEnd])
            ->selectRaw("DATE_FORMAT(estimated_date, '%Y-%m') as ym, COUNT(*) as total")
            ->groupBy('ym')
            ->pluck('total', 'ym');

        $poCountByMonth = Quotation::where('id_sales', $salesId)
            ->where('level', '1')->where('is_primary', '1')
            ->where('status', '100')
            ->whereBetween('po_date', [$monthlyRangeStart, $monthlyRangeEnd])
            ->selectRaw("DATE_FORMAT(po_date, '%Y-%m') as ym, COUNT(*) as total")
            ->groupBy('ym')
            ->pluck('total', 'ym');

        $salesMonthlyLabels = [];
        $salesMonthlyQuote = [];
        $salesMonthlyPO = [];
        for ($i = 5; $i >= 0; $i--) {
            $m = Carbon::now()->subMonths($i);
            $ym = $m->format('Y-m');
            $salesMonthlyLabels[] = $m->locale('id')->translatedFormat('M Y');
            $salesMonthlyQuote[] = (int) $quoteCountByMonth->get($ym, 0);
            $salesMonthlyPO[] = (int) $poCountByMonth->get($ym, 0);
        }

        $sourceData = Client::where('id_sales', $salesId)
            ->select(DB::raw("COALESCE(NULLIF(source, ''), 'Lainnya') as source"), DB::raw('count(*) as total'))
            ->groupBy('source')
            ->orderByDesc('total')
            ->limit(6)
            ->get();

        return compact(
            'salesFunnelLabels',
            'salesFunnelSeries',
            'salesWinRate',
            'salesStatusLabels',
            'salesStatusSeries',
            'salesMonthlyLabels',
            'salesMonthlyQuote',
            'salesMonthlyPO',
            'sourceData',
        );
    }
}
