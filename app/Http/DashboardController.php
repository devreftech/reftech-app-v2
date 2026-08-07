<?php

namespace App\Http\Controllers;

use App\Models\Activities;
use App\Models\Client;
use App\Models\Comment;
use App\Models\Contract;
use App\Models\DetailExpense;
use App\Models\DetailProduct;
use App\Models\DetailQuotation;
use App\Models\DetailUser;
use App\Models\Expense;
use App\Models\FixedAsset;
use App\Models\Invoice;
use App\Models\Issues;
use App\Models\LabaRugi;
use App\Models\Machine;
use App\Models\MonitoringActivities;
use App\Models\Notulen;
use App\Models\Payment;
use App\Models\PendingPO;
use App\Models\ProductIn;
use App\Models\ProductOut;
use App\Models\PurchaseRequest;
use App\Models\Product;
use App\Models\Prospect;
use App\Models\Quotation;
use App\Models\Reminder;
use App\Models\Reports;
use App\Models\ReqVisit;
use App\Models\RevQuote;
use App\Models\SalesOnline;
use App\Models\SalesTargetHistory;
use App\Models\SerialProduct;
use App\Models\Suo;
use App\Models\Target;
use App\Models\UnitQuotation;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        if (Auth::user()->role == 'Technician') {
            return redirect()->route('service-reports.index');
        }

        $dateNow = Carbon::now();
        $monthNow = $dateNow->month;
        $yearNow = $dateNow->year;
        $notulens = Notulen::join('mention_notulen as m', 'm.id_notulen', '=', 'notulen.id')->join('users as u', 'm.id_mention', '=', 'u.id')->get(['notulen.*', 'u.name', 'm.level']);

        $sales = User::where('role', 'Sales')
            ->where('active', '1')
            ->with('latestTarget')
            ->with('latestRole')
            ->orderByDesc('id')
            ->get();

        $result = [];
        $teamIds = [16, 23];

        $teamTotalPO = 0;
        $teamTotalTarget = 0;

        $poPerSales = Quotation::whereYear('po_date', $yearNow)
            ->whereMonth('po_date', $monthNow)
            ->where('status', '100')
            ->where('level', '1')
            ->where('is_primary', '1')
            ->groupBy('id_sales')
            ->selectRaw('id_sales, SUM(nett) as total_nett')
            ->pluck('total_nett', 'id_sales');

        $unitPoPerSales = UnitQuotation::where('status', 'po_received')
            ->where('is_latest', 1)
            ->whereYear('po_received', $yearNow)
            ->whereMonth('po_received', $monthNow)
            ->groupBy('id_sales')
            ->selectRaw('id_sales, SUM(total - tax_amount) as total_nett')
            ->pluck('total_nett', 'id_sales');

        foreach ($sales as $sale) {

            $targetPerSales = $sale->latestTarget->total ?? 0;
            $poTotalPricePerSales = $poPerSales->get($sale->id, 0) + $unitPoPerSales->get($sale->id, 0);

            // 🔥 kalau termasuk team ecommerce
            if (in_array($sale->id, $teamIds)) {
                $teamTotalPO += $poTotalPricePerSales;
                $teamTotalTarget += $targetPerSales;
                continue; // skip masuk ke result individual
            }

            $percentage = $targetPerSales > 0
                ? round(($poTotalPricePerSales / $targetPerSales) * 100, 2)
                : 0;

            $result[] = [
                'name' => $sale->name,
                'area' => $sale->latestRole->area ?? '-',
                'percentage' => $percentage,
            ];
        }

        ## 🔥 Tambahin team ecommerce di akhir

        $teamPercentage = $teamTotalTarget > 0
            ? round(($teamTotalPO / $teamTotalTarget) * 100, 2)
            : 0;

        $result[] = [
            'name' => 'Team Ecommerce',
            'area' => 'Online',
            'percentage' => $teamPercentage,
        ];

        ## 🚀 Sorting tetap sama

        $sorted = collect($result)
            ->sortByDesc('percentage')
            ->values();

        if (Auth::user()->role == 'Sales') {
            $clients = Client::where('id_sales', Auth::id())->get();
            $issue = Issues::all();
            // dd($clients);
            $leveledProspect = Prospect::whereNULL('level')->where('id_sales', Auth::id())->count();
            $weekPerMonth = $this->getWeekperMonth();
            $dailyCall = $this->getDailyCallSales();
            $customers = $this->getCustomersSales();
            $quotation = $this->getQuotationSales();
            $po = $this->getPoSales();
            $leads = $this->getLeadsSales();
            $visit = $this->getVisitSales();
            $poTotalPrice = Quotation::whereYear('po_date', $yearNow)->whereMonth("po_date", $monthNow)->where("id_sales", Auth::user()->id)->where("status", "100")->where('level', '1')->where('is_primary', '1')->sum('nett')
                + UnitQuotation::where('status', 'po_received')->where('is_latest', 1)->whereYear('po_received', $yearNow)->whereMonth('po_received', $monthNow)->where('id_sales', Auth::user()->id)->sum(DB::raw('total - tax_amount'));
            // $noPayment = DB::table('quotation as q')
            //     ->whereYear('q.po_date', $yearNow)
            //     ->whereMonth('q.po_date', $monthNow)
            //     ->where('q.id_sales', Auth::user()->id)
            //     ->where('q.status', '100')
            //     ->where('q.level', '1')
            //     ->where('q.is_primary', '1')
            //     ->whereNotExists(function ($query) {
            //         $query->select(DB::raw(1))
            //             ->from('payment as p')
            //             ->whereRaw('p.id_quotation = q.id');
            //     })
            //     ->select(DB::raw('q.nett as total'));

            // $withPayment = DB::table('payment as p')
            //     ->join('quotation as q', 'q.id', '=', 'p.id_quotation')
            //     ->whereYear('p.date', $yearNow)
            //     ->whereMonth('p.date', $monthNow)
            //     ->where('q.id_sales', Auth::user()->id)
            //     ->where('q.status', '100')
            //     ->where('q.level', '1')
            //     ->where('q.is_primary', '1')
            //     ->select(DB::raw('(p.amount - p.pph - p.cost) as total'));

            // $poTotalPrice = DB::query()
            //     ->fromSub(
            //         $noPayment->unionAll($withPayment),
            //         'x'
            //     )
            //     ->sum('total');
            $formattedTotalPrice = $this->formatNumber($poTotalPrice);
            $target = Target::where('id_sales', Auth::user()->id)->first();
            $prospects = Prospect::where('id_sales', Auth::id())->whereNull('level')->get();
            $nextFollow = Activities::join('client as c', 'c.id', '=', 'activities.id_client')
                ->join('users as s', 'c.id_sales', '=', 's.id')
                ->select(['activities.id', 'c.company', 'activities.note', 'activities.follow_up as start', 'activities.follow_up as end', 'activities.name'])
                ->where('c.id_sales', Auth::id())
                ->groupBy('c.company')
                ->orderBy('activities.follow_up')
                ->get();

            $quotationComment = Quotation::join('change_status as c', 'c.id_quotation', '=', 'quotation.id')
                ->join('comment as o', 'o.id_status', '=', 'c.id')
                ->join('users as u', 'u.id', '=', 'o.id_user')
                ->where('quotation.id_sales', Auth::id())
                ->where('o.type', 'quotation')  // Pastikan filter type di sini
                ->where('o.id_user', '!=', Auth::id())
                ->orderBy('o.date', 'DESC')
                ->select(['quotation.id as idQ', 'o.id as idC', 'o.id_user', 'o.level', 'o.comment', 'o.date', 'o.type', 'quotation.no_quote', 'u.name', 'u.image']);

            // Query untuk mengambil data dengan type "prospect"
            $prospectComment = Comment::join('prospect as p', 'comment.id_prospect', '=', 'p.id')
                ->join('users as u', 'u.id', '=', 'comment.id_user')
                ->join('pic as pi', 'pi.id', '=', 'p.id_pic')
                ->join('client as c', 'c.id', '=', 'pi.id_client')
                ->where('p.id_sales', Auth::id())
                ->where('comment.type', 'prospect')  // Pastikan filter type di sini
                ->where('comment.id_user', '!=', Auth::id())
                ->orderBy('comment.date', 'DESC')
                ->select(['p.id as idP', 'comment.id as idC', 'comment.id_user', 'comment.level', 'comment.comment', 'comment.date', 'comment.type', 'c.company', 'u.name', 'u.image']);

            // Menggabungkan kedua query menggunakan union
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
            $weeklyOnline = SalesOnline::where('id_sales', Auth::user()->id)
                ->whereIn('type', ['Akurasi', 'Delivery', 'Response', 'Rating', 'Customer'])
                ->whereYear('date', $yearNow)
                ->whereRaw('WEEK(date, 1) = ?', [$dateNow->weekOfYear])
                ->get()->keyBy('type');

            $akurasi = $weeklyOnline->get('Akurasi');
            $delivery = $weeklyOnline->get('Delivery');
            $response = $weeklyOnline->get('Response');
            $rating = $weeklyOnline->get('Rating');
            $customer = $weeklyOnline->get('Customer');

            $todayOnline = SalesOnline::where('id_sales', Auth::user()->id)
                ->whereIn('type', ['Video', 'SW', 'product'])
                ->whereDate('date', $dateNow)
                ->get()->groupBy('type');

            $video = $todayOnline->get('Video', collect())->first();
            $sw = $todayOnline->get('SW', collect())->first();
            $product = $todayOnline->get('product', collect());

            $monthlyOnline = SalesOnline::where('id_sales', Auth::user()->id)
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
            $productCount = SalesOnline::where('id_sales', Auth::user()->id)->where('type', 'Product')->whereMonth('date', $monthNow)->whereYear('date', $yearNow)->count();
            $POCount = Quotation::where('id_sales', Auth::user()->id)->where('is_primary', '1')->where('status', '100')->where('level', '1')->whereMonth('po_date', Carbon::now())->whereYear('po_date', Carbon::now())->count();

            $jumlahCustomer = Client::join(DB::raw('(SELECT id_client, status FROM crm_status WHERE id IN (SELECT MAX(id) FROM crm_status GROUP BY id_client)) as cs'), 'client.id', '=', 'cs.id_client')->where('role', 'Customers')->where('id_sales', Auth::user()->id)->where('cs.status', '2')->count();
            $reportsCount = Reports::join('machine as m', 'm.id', '=', 'reports.id_machine')
                ->join('client as c', 'c.id', '=', 'm.id_client')
                ->join('users as u', 'u.id', '=', 'c.id_sales')
                ->where('u.id', Auth::user()->id)
                ->where('reports.viewed', 0)->count();

            $salesCharts = $this->getSalesDashboardCharts(Auth::id(), $leads->count(), $quotation->count(), $po->count());

            return view(
                "pages.sales.dashboard",
                array_merge(compact(
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
                ), $salesCharts)
            );
        } elseif (Auth::user()->role == 'Support') {
            // Prospect Monthly (By Support - Sandhy)
            $year = now()->year;
            $month = now()->month;
            $support = Auth::id();

            $previousMonth = now()->subMonth();
            $yearPrev = $previousMonth->year;
            $monthPrev = $previousMonth->month;


            $prospect = Prospect::whereYear('date', $year)
                ->whereMonth('date', $month)
                ->where('id_support', $support)
                ->count();

            $provided = Prospect::whereYear('date', $year)
                ->whereMonth('date', $month)
                ->where('provide', '!=', '0')
                ->where('id_support', $support)
                ->count();

            $quotation = Quotation::whereYear('estimated_date', $year)
                ->whereMonth('estimated_date', $month)
                ->where('id_support', $support)
                ->where('level', '1')
                ->where('is_primary', '1')
                ->count();

            $po = Quotation::whereYear('po_date', $year)
                ->whereMonth('po_date', $month)
                ->where('id_support', $support)
                ->where('status', '100')
                ->where('level', '1')
                ->where('is_primary', '1')
                ->count();

            $loss = Quotation::whereYear('estimated_date', $year)
                ->whereMonth('estimated_date', $month)
                ->where('id_support', $support)
                ->where('status', '0')
                ->where('level', '1')
                ->where('is_primary', '1')
                ->count();

            $prospectLastMonth = Prospect::whereYear('date', $yearPrev)
                ->whereMonth('date', $monthPrev)
                ->where('id_support', $support)
                ->count();

            // dd($quotation);
            $diffProspect = $prospect - $prospectLastMonth;

            $closingRate = $quotation > 0 ? round(($po / $quotation) * 100, 1) : 0;
            $conversionRate = $prospect > 0 ? round(($quotation / $prospect) * 100, 1) : 0;
            $providedRate = $prospect > 0 ? round(($provided / $prospect) * 100, 1) : 0;
            $targetProspect = Target::where('id_sales', Auth::id())->first()->prospect ?? 100;
            $progress = $targetProspect > 0
                ? round(($prospect / $targetProspect) * 100, 1)
                : 0;

            return view(
                "pages.sales.dashboard",
                compact(
                    'notulens',
                    'prospect',
                    'provided',
                    'quotation',
                    'po',
                    'loss',
                    'closingRate',
                    'conversionRate',
                    'providedRate',
                    'targetProspect',
                    'diffProspect'
                )
            );

        } elseif (Auth::user()->role == 'Admin') {
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
                ->count();
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
            $sales = User::whereIn('role', ['Sales', 'Support'])->where('active', '1')->orderByDesc('id')->get();
            $firstSales = User::find(1);
            $targett = Target::where('id_sales', $firstSales->id)->first('total');
            $targetAllSales = Target::join('users as u', 'u.id', '=', 'target.id_sales')->where('u.role', 'Sales')->where('u.active', '1')->sum('target.total');
            // dd($targetAllSales);
            $targetSales = $sales->map(function ($sale) {
                return $sale->target()->groupBy('id_sales')->get();
            });
            // dd($totalPO);
            // $totalProspectQuote = Quotation::whereYear('date', $yearNow)->whereMonth('date', $monthNow)->whereNotNull('id_support')->where('status', '!=', '100')->where('level', '1')->where('is_primary', '1')->sum('nett');
            // $prospectedQuotation = Prospect::join('quotation as q', 'q.id', '=', 'prospect.id_quotation')->whereYear('date', $yearNow)->whereMonth('date', $monthNow)->where('provide', '!=', '0')->where('status', '!=', '100')->where('q.level', '1')->where('is_primary', '1')->count();
            // $prospectedPO = Prospect::join('quotation as q', 'q.id', '=', 'prospect.id_quotation')->whereYear('date', $yearNow)->whereMonth('date', $monthNow)->where('provide', '!=', '0')->where('status', '100')->where('q.level', '1')->where('is_primary', '1')->count();
            // $prospectedQuotationTotal = Prospect::join('quotation as q', 'q.id', '=', 'prospect.id_quotation')->whereYear('date', $yearNow)->whereMonth('date', $monthNow)->where('provide', '!=', '0')->where('status', '!=', '100')->where('q.level', '1')->where('is_primary', '1')->sum('q.nett');
            // $prospectedPOTotal = Prospect::join('quotation as q', 'q.id', '=', 'prospect.id_quotation')->whereYear('date', $yearNow)->whereMonth('date', $monthNow)->where('provide', '!=', '0')->where('status', '100')->where('q.level', '1')->where('is_primary', '1')->sum('q.nett');
            // dd($totalProspectQuote);
            $totalProspectSupport = Quotation::whereYear('estimated_date', $yearNow)->whereMonth('estimated_date', $monthNow)->where('id_sales', $firstSales->id)->whereIn('status', ['20', '30', '40', '60', '80'])->where('level', '1')->where('is_primary', '1')->sum('nett');
            $totalForecast = Quotation::whereYear('estimated_date', $yearNow)->whereMonth('estimated_date', $monthNow)->where('id_sales', $firstSales->id)->where('status', '80')->where('level', '1')->where('is_primary', '1')->sum('nett');

            $totalQuotation = Quotation::whereYear('estimated_date', $yearNow)->whereMonth('estimated_date', $monthNow)->where('id_sales', $firstSales->id)->where('level', '1')->where('is_primary', '1')->sum('nett');
            $totalProspect = Quotation::join('prospect as p', 'quotation.id', '=', 'p.id_quotation')->whereNotNull('id_quotation')->whereYear('estimated_date', $yearNow)->whereMonth('estimated_date', $monthNow)->where('quotation.id_sales', $firstSales->id)->whereIn('status', ['80', '90'])->where('quotation.level', '1')->where('is_primary', '1')->sum('nett');
            $totalHotProspect = Quotation::whereYear('estimated_date', $yearNow)->whereMonth('estimated_date', $monthNow)->where('id_sales', $firstSales->id)->whereIn('status', ['80', '90'])->where('level', '1')->where('is_primary', '1')->sum('nett');
            $totalLoss = Quotation::whereYear('estimated_date', $yearNow)->whereMonth('estimated_date', $monthNow)->where('id_sales', $firstSales->id)->where('status', '0')->where('level', '1')->where('is_primary', '1')->sum('nett');
            $totalPO = Quotation::whereYear('po_date', $yearNow)->whereMonth('po_date', $monthNow)->where('id_sales', $firstSales->id)->where('status', '100')->where('level', '1')->where('is_primary', '1')->sum('nett');
            $filteredLeads = Client::whereYear('created_at', $yearNow)->whereMonth('created_at', $monthNow)->where('id_sales', $firstSales->id)->count();
            $filteredDC = Activities::join('client as c', 'activities.id_client', '=', 'c.id')->whereYear('date', $yearNow)->whereMonth('date', $monthNow)->where('c.id_sales', $firstSales->id)->where('status', 'Responded')->whereIn('activities.name', ['Daily Call', 'Follow Up'])->count();
            $filteredCRM = Activities::join('client as c', 'activities.id_client', '=', 'c.id')->join(DB::raw('(SELECT id_client, status FROM crm_status WHERE id IN (SELECT MAX(id) FROM crm_status GROUP BY id_client)) as cs'), 'c.id', '=', 'cs.id_client')->whereYear('date', $yearNow)->whereMonth('date', $monthNow)->where('c.id_sales', $firstSales->id)->where('activities.status', 'Responded')->where('activities.name', 'CRM')->where('cs.status', '2')->count(DB::raw('DISTINCT c.id'));
            $filteredQuote = Quotation::whereYear('estimated_date', $yearNow)->whereMonth('estimated_date', $monthNow)->where('id_sales', $firstSales->id)->where('level', '1')->where('is_primary', '1')->count();
            $filteredProspect = Prospect::whereNotNull('id_quotation')->whereMonth('date', $monthNow)->whereYear('date', $yearNow)->count();
            $allProspect = Prospect::whereMonth('date', $monthNow)->whereYear('date', $yearNow)->count();
            $filteredPO = Quotation::whereYear('po_date', $yearNow)->whereMonth('po_date', $monthNow)->where('id_sales', $firstSales->id)->where('status', '100')->where('level', '1')->where('is_primary', '1')->count();
            $filteredVisit = Activities::join('client as c', 'activities.id_client', '=', 'c.id')->whereYear('date', $yearNow)->whereMonth('date', $monthNow)->where('c.id_sales', $firstSales->id)->where('status', 'Responded')->where('name', 'Visit')->count();

            $dataDc = $this->getWeekDataDC();
            $dataCRM = $this->getWeekDataCRM();
            $dataVisit = $this->getWeekDataVisit();
            $dataQuote = $this->getWeekDataQuote();
            $dataOverview = $this->getDataOverview();

            // dd($dataOverview);
            $dataLeads = $this->getWeekDataLeads();
            $dataPO = $this->getWeekDataPO();
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

            // Admin bisa berpindah antar dashboard divisi lewat dropdown menu (Sales/Sales Manager/Accounting/Finance/Logistic/Workshop)
            $adminView = request()->query('view', 'sales');
            if (!in_array($adminView, ['sales', 'salesmanager', 'accounting', 'finance', 'logistic', 'workshop'], true)) {
                $adminView = 'sales';
            }

            $adminExtraData = match ($adminView) {
                'salesmanager' => $this->getSalesManagerDashboardData(),
                'accounting' => $this->getAccountingDashboardData(),
                'finance' => $this->getFinanceDashboardData(),
                'logistic' => $this->getLogisticDashboardData(),
                'workshop' => $this->getWorkshopDashboardData(),
                default => [],
            };

            return view(
                "pages.sales.dashboard",
                array_merge(
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
                        'totalPO',
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
                    ),
                    $adminExtraData
                )
            );
        } elseif (Auth::user()->role == 'Accounting') {
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
                ->count();
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
            // dd($paidInvoice1);
            $paidInvoice2 = Invoice::join('quotation as q', 'q.id', '=', 'invoice.id_quotation')
                ->join('payment as pay', 'pay.id_quotation', '=', 'q.id')
                ->join('pic as p', 'q.id_pic', '=', 'p.id')
                ->join('client as c', 'p.id_client', '=', 'c.id')
                ->whereBetween('invoice.date', [$start2, $end2])
                ->where('pay.level', '1')
                ->groupBy('pay.id')
                ->select('pay.amount', 'c.info')
                ->get();

            // $unpaidInvoice = Invoice::
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

                    // 0 PAYMENT
                    if ($payments->isEmpty()) {
                        $unpaid_amount = $harga_total;
                    }

                    // 1 PAYMENT
                    elseif ($payments->count() === 1) {
                        $p = $payments->first();
                        $unpaid_amount = $p->level == 0 ? $harga_total : 0;
                    }

                    // 2 PAYMENT (DP + BP/Tempo)
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

            // dd($unpaidInvoice1);
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

                    // ✅ 0 PAYMENT
                    if ($payments->isEmpty()) {
                        $q->unpaid_amount = $q->harga_total;
                        return $q;
                    }

                    // ✅ 1 PAYMENT
                    if ($payments->count() === 1) {
                        $p = $payments->first();
                        $q->unpaid_amount = $p->level == 0 ? $q->harga_total : 0;
                        return $q;
                    }

                    // ✅ 2 PAYMENT (umumnya DP + BP/Tempo)
                    if ($payments->count() >= 2) {
                        $dp = $payments->where('type', 'DP')->first();
                        $second = $payments->where('type', '!=', 'DP')->first();

                        // kalau DP belum cair
                        if ($dp && $dp->level == 0) {
                            $q->unpaid_amount = $q->harga_total;
                        }
                        // kalau DP cair, tapi payment kedua belum (atau belum ada)
                        elseif ($dp && $dp->level == 1 && (!$second || $second->level == 0)) {
                            $q->unpaid_amount = $q->harga_total - $dp->amount;
                        }
                        // kalau DP dan payment kedua sudah cair semua
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

            // ==== Accounting Dashboard widgets ====
            extract($this->getAccountingDashboardData());

            return view(
                "pages.sales.dashboard",
                compact(
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
                    'reminder',
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
                )
            );
        } elseif (Auth::user()->role == 'Finance Manager') {
            extract($this->getFinanceDashboardData());

            return view(
                "pages.sales.dashboard",
                compact(
                    'financeAgingBuckets',
                    'financeOutstandingAR',
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
                    'financeRecentActivity',
                    'notulens',
                    'financeKeyAccounts',
                )
            );
        } elseif (Auth::user()->role == 'Sales Manager') {
            extract($this->getSalesManagerDashboardData());

            return view(
                "pages.sales.dashboard",
                compact(
                    'sorted',
                    'sales',
                    'notulens',
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
                )
            );
        } elseif (Auth::user()->role == 'Logistic') {
            extract($this->getLogisticDashboardData());

            return view(
                "pages.sales.dashboard",
                compact(
                    'logSoBaruCount',
                    'logPrPendingCount',
                    'logSuoPendingCount',
                    'logIncomingPendingCount',
                    'logLowStockCount',
                    'logSoNewCount',
                    'logSoListCount',
                    'logSoDeliveryCount',
                    'logSoDoneCount',
                    'logSoStatusSeries',
                    'logPrFromSo',
                    'logIncomingPending',
                    'logLowStock',
                    'logReceivingLabels',
                    'logReceivingSeries',
                    'logRecentActivity',
                    'notulens',
                )
            );
        } else {
            $today = Carbon::now()->toDateString();
            $commodity = Product::count();
            $dproduct = DetailProduct::count();
            $sproduct = SerialProduct::count();
            $user = User::find('25');
            $monitoring = MonitoringActivities::whereDate('date', $today)->first();

            $newCount = PendingPO::where('status', operator: 0)
                ->where('type', 'Non Project')
                ->count();
            $listCount = PendingPO::whereIn('pending_po.status', [1, 2, 3, 4])
                ->where('type', 'Non Project')
                ->count();
            $deliveryCount = PendingPO::where('pending_po.status', 5)
                ->where('type', 'Non Project')
                ->count();
            $doneCount = PendingPO::where('pending_po.status', 6)
                ->where('type', 'Non Project')
                ->count();

            $visits = ReqVisit::whereNull('date')->get();
            $visited = ReqVisit::whereNotNull('date')->whereNull('visit_date')->get();
            return view(
                "pages.sales.dashboard",
                compact(
                    'user',
                    'newCount',
                    'listCount',
                    'deliveryCount',
                    'notulens',
                    'commodity',
                    'dproduct',
                    'sproduct',
                    'visits',
                    'visited'
                )
            );
        }

        // dd($leveledProspect);
    }

    private function getSalesDashboardCharts(int $salesId, int $leadsCount, int $quotationCount, int $poCount): array
    {
        $dateNow = Carbon::now();
        $monthNow = $dateNow->month;
        $yearNow = $dateNow->year;

        // Funnel: New Leads -> Quotation -> PO Won (bulan berjalan)
        $salesFunnelLabels = ['New Leads', 'Quotation', 'PO Won'];
        $salesFunnelSeries = [$leadsCount, $quotationCount, $poCount];
        $salesWinRate = $quotationCount > 0 ? round(($poCount / $quotationCount) * 100, 1) : 0;

        // Quotation by status (bulan berjalan)
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

        // Trend 6 bulan terakhir: Quotation vs PO
        $salesMonthlyLabels = [];
        $salesMonthlyQuote = [];
        $salesMonthlyPO = [];
        for ($i = 5; $i >= 0; $i--) {
            $m = Carbon::now()->subMonths($i);
            $salesMonthlyLabels[] = $m->locale('id')->translatedFormat('M Y');
            $salesMonthlyQuote[] = Quotation::where('id_sales', $salesId)
                ->where('level', '1')->where('is_primary', '1')
                ->whereYear('estimated_date', $m->year)
                ->whereMonth('estimated_date', $m->month)
                ->count();
            $salesMonthlyPO[] = Quotation::where('id_sales', $salesId)
                ->where('level', '1')->where('is_primary', '1')
                ->where('status', '100')
                ->whereYear('po_date', $m->year)
                ->whereMonth('po_date', $m->month)
                ->count();
        }

        // Prospect / Leads by source (all time, scoped ke sales ini)
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

    private function getAccountingDashboardData(): array
    {
        $dateNow = Carbon::now();
        $monthNow = $dateNow->month;
        $yearNow = $dateNow->year;

        $requestInvoice = Quotation::join('pic', 'pic.id', '=', 'quotation.id_pic')
            ->join('client', 'client.id', '=', 'pic.id_client')
            ->join('invoice', 'invoice.id_quotation', '=', 'quotation.id')
            ->join('users', 'users.id', '=', 'quotation.id_sales')
            ->where('status', '100')
            ->whereNotNull('quotation.po_file')
            ->whereNull('invoice.no_invoice')
            ->count();

        $acctMonthStart = Carbon::create($yearNow, $monthNow, 1)->startOfMonth();
        $acctMonthEnd = Carbon::create($yearNow, $monthNow, 1)->endOfMonth();
        $acctPrevMonth = Carbon::create($yearNow, $monthNow, 1)->subMonth();
        $acctPrevMonthStart = $acctPrevMonth->copy()->startOfMonth();
        $acctPrevMonthEnd = $acctPrevMonth->copy()->endOfMonth();

        // Customer Invoice - PO approved, belum dibuatkan invoice
        $acctInvoiceBelumDibuatCount = $requestInvoice;
        $acctInvoiceBelumDibuatTotal = Quotation::join('pic', 'pic.id', '=', 'quotation.id_pic')
            ->join('client', 'client.id', '=', 'pic.id_client')
            ->join('invoice', 'invoice.id_quotation', '=', 'quotation.id')
            ->where('status', '100')
            ->whereNotNull('quotation.po_file')
            ->whereNull('invoice.no_invoice')
            ->sum('quotation.harga_total');

        // Supplier Bill (Product In) - AP
        $acctApUnpaidCount = ProductIn::where('accept', '0')->count();
        $acctApUnpaidTotal = ProductIn::where('accept', '0')->sum('total');
        $acctApSupplierCount = ProductIn::where('accept', '0')->distinct('supplier')->count('supplier');
        $acctApPaidMonthCount = ProductIn::where('accept', '1')
            ->whereBetween('date', [$acctMonthStart, $acctMonthEnd])->count();
        $acctApPaidMonthTotal = ProductIn::where('accept', '1')
            ->whereBetween('date', [$acctMonthStart, $acctMonthEnd])->sum('total');
        $acctApTotalAll = ProductIn::count();
        $acctApPaidTotalAll = ProductIn::where('accept', '1')->count();

        // Fixed Asset baru bulan ini
        $acctFixedAssetMonthCount = FixedAsset::whereBetween('created_at', [$acctMonthStart, $acctMonthEnd])->count();
        $acctFixedAssetMonthTotal = FixedAsset::whereBetween('created_at', [$acctMonthStart, $acctMonthEnd])->sum('total');

        // Account Receivable - outstanding & aging (payment Tempo belum lunas)
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

        // Expense & COGS bulan ini vs bulan lalu
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

        // Beban per akun bulan ini
        $acctExpenseByAccount = DetailExpense::join('expense as e', 'e.id', '=', 'detail_expense.id_expense')
            ->join('account as a', 'a.id', '=', 'detail_expense.id_account')
            ->whereBetween('e.date', [$acctMonthStart, $acctMonthEnd])
            ->select('a.name', DB::raw('SUM(detail_expense.amount) as total'))
            ->groupBy('a.name')
            ->orderByDesc('total')
            ->limit(6)
            ->get();

        // Dokumen terbaru
        $acctRecentSupplierBill = ProductIn::orderByDesc('date')->limit(5)
            ->get(['no_product_in', 'supplier', 'total', 'date', 'accept']);
        $acctRecentCustomerInvoice = Invoice::join('quotation as q', 'q.id', '=', 'invoice.id_quotation')
            ->join('pic as p', 'p.id', '=', 'q.id_pic')
            ->join('client as c', 'c.id', '=', 'p.id_client')
            ->orderByDesc('invoice.date')
            ->limit(5)
            ->get(['invoice.no_invoice', 'invoice.date', 'q.harga_total', 'c.company']);
        $acctRecentExpense = Expense::orderByDesc('date')->limit(5)->get(['no_expense', 'date', 'amount', 'memo']);
        $acctRecentFixedAsset = FixedAsset::orderByDesc('created_at')->limit(5)->get(['code', 'desc', 'total', 'created_at']);

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

    private function getFinanceDashboardData(): array
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
        $financeMonthlyLabels = [];
        $financeMonthlyRevenue = [];
        $financeMonthlyExpense = [];
        $financeMonthlyTargetSeries = [];
        for ($m = 1; $m <= $monthNow; $m++) {
            $mStart = Carbon::create($yearNow, $m, 1)->startOfMonth()->toDateString();
            $mEnd = Carbon::create($yearNow, $m, 1)->endOfMonth()->toDateString();
            $financeMonthlyLabels[] = Carbon::create($yearNow, $m, 1)->translatedFormat('M');
            $financeMonthlyRevenue[] = (int) Quotation::whereBetween('po_date', [$mStart, $mEnd])
                ->where('status', '100')->where('level', '1')->where('is_primary', '1')->sum('nett');
            $financeMonthlyExpense[] = (int) DetailExpense::join('expense as e', 'e.id', '=', 'detail_expense.id_expense')
                ->whereBetween('e.date', [$mStart, $mEnd])->sum('detail_expense.amount');
            $financeMonthlyTargetSeries[] = $financeMonthlyTarget;
        }

        // Recent activity: gabungan Invoice, Payment, Expense terbaru
        $financeRecentInvoice = Invoice::join('quotation as q', 'q.id', '=', 'invoice.id_quotation')
            ->orderBy('invoice.date', 'desc')
            ->limit(5)
            ->get(['invoice.no_invoice as ref', 'invoice.date as tanggal', 'q.nett as nominal', DB::raw("'Invoice' as tipe")]);
        $financeRecentPayment = Payment::orderBy('date', 'desc')
            ->limit(5)
            ->get([DB::raw("CONCAT('PAY-', payment.id) as ref"), 'date as tanggal', 'amount as nominal', DB::raw("'Payment' as tipe")]);
        $financeRecentExpense = Expense::orderBy('date', 'desc')
            ->limit(5)
            ->get(['no_expense as ref', 'date as tanggal', 'amount as nominal', DB::raw("'Expense' as tipe")]);
        $financeRecentActivity = $financeRecentInvoice
            ->concat($financeRecentPayment)
            ->concat($financeRecentExpense)
            ->sortByDesc('tanggal')
            ->take(10)
            ->values();

        // Top 10 Key Accounts YTD
        $financeKeyAccounts = Quotation::join('pic', 'pic.id', '=', 'quotation.id_pic')
            ->join('client', 'client.id', '=', 'pic.id_client')
            ->whereBetween('quotation.po_date', [$startYear->toDateString(), $dateNow->toDateString()])
            ->where('quotation.status', '100')
            ->where('quotation.level', '1')
            ->where('quotation.is_primary', '1')
            ->select(
                'client.id',
                'client.company',
                DB::raw('SUM(quotation.nett) as total_po'),
                DB::raw('COUNT(quotation.id) as count_po')
            )
            ->groupBy('client.id', 'client.company')
            ->orderByDesc('total_po')
            ->limit(10)
            ->get();

        return compact(
            'financeAgingBuckets',
            'financeOutstandingAR',
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
            'financeRecentActivity',
            'financeKeyAccounts',
        );
    }

    private function getLogisticDashboardData(): array
    {
        $lowStockThreshold = 5;

        // KPI cards
        $logSoBaruCount = PendingPO::where('status', 0)->where('type', 'Non Project')->count();
        $logPrPendingCount = PurchaseRequest::where('status', '0')->count();
        $logSuoPendingCount = Suo::where('status', 'submitted')->count();
        $logIncomingPendingCount = ProductIn::where('accept', '0')->count();
        $logLowStockCount = DetailProduct::whereRaw('(stock + warehouse_stock) > 0 AND (stock + warehouse_stock) < ?', [$lowStockThreshold])->count();

        // Status Sales Order (Non Project) breakdown - sama dengan pola Admin/Accounting
        $logSoNewCount = $logSoBaruCount;
        $logSoListCount = PendingPO::whereIn('pending_po.status', [1, 2, 3, 4])->where('type', 'Non Project')->count();
        $logSoDeliveryCount = PendingPO::where('pending_po.status', 5)->where('type', 'Non Project')->count();
        $logSoDoneCount = PendingPO::where('pending_po.status', 6)->where('type', 'Non Project')->count();
        $logSoStatusSeries = [$logSoNewCount, $logSoListCount, $logSoDeliveryCount, $logSoDoneCount];

        // PR otomatis dari Sales Order (stok tidak cukup)
        $logPrFromSo = PurchaseRequest::whereNotNull('id_pending')
            ->where('status', '0')
            ->with(['pending', 'equivalent.product'])
            ->orderByDesc('date')
            ->take(6)
            ->get();

        // Incoming Goods - Pending Receipt
        $logIncomingPending = ProductIn::where('accept', '0')
            ->with('supp')
            ->orderByDesc('date')
            ->take(6)
            ->get();

        // Stok Hampir Habis (masih ada stok, tapi di bawah ambang batas)
        $logLowStock = DetailProduct::selectRaw('detail_product.*, (stock + warehouse_stock) as total_stock')
            ->with('product')
            ->havingRaw('total_stock > 0 AND total_stock < ?', [$lowStockThreshold])
            ->orderBy('total_stock')
            ->take(6)
            ->get();

        // Penerimaan Barang 7 hari terakhir
        $logReceivingLabels = [];
        $logReceivingSeries = [];
        for ($i = 6; $i >= 0; $i--) {
            $day = Carbon::now()->subDays($i);
            $logReceivingLabels[] = $day->format('d/m');
            $logReceivingSeries[] = ProductIn::where('accept', '1')->whereDate('updated_at', $day->toDateString())->count();
        }

        // Aktivitas terbaru: gabungan PR dibuat, barang diterima, barang dikirim
        $logRecentPr = PurchaseRequest::orderByDesc('created_at')
            ->limit(5)
            ->get(['no_pr as ref', 'created_at as tanggal', 'qty as ket', DB::raw("'PR Dibuat' as tipe")]);
        $logRecentIncoming = ProductIn::where('accept', '1')
            ->orderByDesc('updated_at')
            ->limit(5)
            ->get(['no_do as ref', 'updated_at as tanggal', 'supplier as ket', DB::raw("'Barang Diterima' as tipe")]);
        $logRecentOutgoing = ProductOut::orderByDesc('created_at')
            ->limit(5)
            ->get([DB::raw("CONCAT('DO-', id) as ref"), 'created_at as tanggal', 'detail_client as ket', DB::raw("'Barang Dikirim' as tipe")]);
        $logRecentActivity = $logRecentPr
            ->concat($logRecentIncoming)
            ->concat($logRecentOutgoing)
            ->sortByDesc('tanggal')
            ->take(8)
            ->values();

        return compact(
            'logSoBaruCount',
            'logPrPendingCount',
            'logSuoPendingCount',
            'logIncomingPendingCount',
            'logLowStockCount',
            'logSoNewCount',
            'logSoListCount',
            'logSoDeliveryCount',
            'logSoDoneCount',
            'logSoStatusSeries',
            'logPrFromSo',
            'logIncomingPending',
            'logLowStock',
            'logReceivingLabels',
            'logReceivingSeries',
            'logRecentActivity',
        );
    }

    private function getWorkshopDashboardData(): array
    {
        // Scope: unit Fixed Asset kategori "Mesin" (unit workshop/heavy equipment).
        // "Kendaraan Kantor" sengaja tidak dihitung di sini — beda konteks (fleet kantor, bukan unit workshop).
        $workshopUnits = FixedAsset::where('type', 'Mesin')->with('unit')->get();
        $workshopTotalUnit = $workshopUnits->count();

        $statusLabels = ['OK', 'Rental', 'Service', 'Breakdown', 'Reserved'];
        $workshopStatusCounts = [];
        foreach ($statusLabels as $label) {
            $workshopStatusCounts[$label] = $workshopUnits->where('status_unit', $label)->count();
        }
        // Unit yang statusnya belum diklasifikasi Admin (null) atau sudah Sold, dikelompokkan terpisah
        // supaya tidak "hilang" dari total tapi juga tidak mengotori 5 kategori utama mockup.
        $workshopStatusOther = $workshopUnits->whereNotIn('status_unit', $statusLabels)->count();

        $workshopKondisiBaru = $workshopUnits->where('kondisi', 'Baru')->count();
        $workshopKondisiSecond = $workshopUnits->where('kondisi', 'Second')->count();

        $workshopQcChecking = $workshopUnits->where('qc_status', 'checking')->count();
        $workshopQcOk = $workshopUnits->where('qc_status', 'ok')->count();
        $workshopQcReject = $workshopUnits->where('qc_status', 'reject')->count();

        $workshopTotalNilaiAset = $workshopUnits->sum('total');

        // Unit yang perlu perhatian: sedang Service atau Breakdown
        $workshopAttentionUnits = $workshopUnits
            ->whereIn('status_unit', ['Service', 'Breakdown'])
            ->sortByDesc('updated_at')
            ->take(10)
            ->values();

        // Unit yang baru masuk (acquisition terbaru)
        $workshopRecentUnits = $workshopUnits
            ->sortByDesc('created_at')
            ->take(6)
            ->values();

        // --- Vehicle Maintenance ---
        // Data real dari fixed_asset (type='Kendaraan') + vehicle_maintenance_log.
        // Due-date per kategori diambil dari riwayat TERBARU (tanggal paling akhir) per jenis
        // (Servis / STNK & Pajak / Ganti Kaleng); kalau belum pernah dicatat, statusnya '-' (secondary).
        $today = Carbon::now();
        $statusFromDays = function (?int $days) {
            if ($days === null) {
                return ['label' => '-', 'color' => 'secondary'];
            }
            if ($days < 0) {
                return ['label' => 'Overdue', 'color' => 'danger'];
            }
            if ($days <= 14) {
                return ['label' => 'Due Soon', 'color' => 'warning'];
            }
            return ['label' => 'OK', 'color' => 'success'];
        };

        $vehicleAssets = FixedAsset::where('type', 'Kendaraan')->with('maintenanceLogs')->get();

        $daysUntil = function ($log) use ($today) {
            if (!$log || !$log->tanggal_jatuh_tempo) {
                return null;
            }
            return (int) $today->diffInDays(Carbon::parse($log->tanggal_jatuh_tempo), false);
        };

        $workshopVehicles = $vehicleAssets->map(function ($fixed) use ($daysUntil, $statusFromDays) {
            $servisLog = $fixed->maintenanceLogs->where('jenis', 'Servis')->sortByDesc('tanggal')->first();
            $pajakLog = $fixed->maintenanceLogs->where('jenis', 'STNK & Pajak')->sortByDesc('tanggal')->first();
            $kalengLog = $fixed->maintenanceLogs->where('jenis', 'Ganti Kaleng')->sortByDesc('tanggal')->first();

            $servisStatus = $statusFromDays($daysUntil($servisLog));
            $pajakStatus = $statusFromDays($daysUntil($pajakLog));
            $kalengStatus = $statusFromDays($daysUntil($kalengLog));

            // Status keseluruhan = yang paling genting di antara ketiganya
            $rank = ['danger' => 3, 'warning' => 2, 'success' => 1, 'secondary' => 0];
            $overall = collect([$servisStatus, $pajakStatus, $kalengStatus])->sortByDesc(fn ($s) => $rank[$s['color']])->first();

            return (object) [
                'plat' => $fixed->plat_nomor ?: '-',
                'jenis' => trim(($fixed->jenis_kendaraan ?: '') . ' ' . ($fixed->merk_model ?: '')) ?: $fixed->code,
                'servis_terakhir' => $servisLog ? Carbon::parse($servisLog->tanggal) : null,
                'servis_berikutnya' => $servisLog && $servisLog->tanggal_jatuh_tempo ? Carbon::parse($servisLog->tanggal_jatuh_tempo) : null,
                'servis_status' => $servisStatus,
                'pajak_due' => $pajakLog && $pajakLog->tanggal_jatuh_tempo ? Carbon::parse($pajakLog->tanggal_jatuh_tempo) : null,
                'pajak_status' => $pajakStatus,
                'ganti_kaleng_due' => $kalengLog && $kalengLog->tanggal_jatuh_tempo ? Carbon::parse($kalengLog->tanggal_jatuh_tempo) : null,
                'ganti_kaleng_status' => $kalengStatus,
                'overall_status' => $overall,
            ];
        });

        $workshopVehicleTotal = $workshopVehicles->count();
        $workshopVehicleServisDue = $workshopVehicles->filter(fn ($v) => $v->servis_status['color'] !== 'success')->count();
        $workshopVehiclePajakDue = $workshopVehicles->filter(fn ($v) => $v->pajak_status['color'] !== 'success')->count();
        $workshopVehicleKalengDue = $workshopVehicles->filter(fn ($v) => $v->ganti_kaleng_status['color'] !== 'success')->count();
        $workshopVehicleOverdueCount = $workshopVehicles->filter(fn ($v) => $v->overall_status['color'] === 'danger')->count();

        // Klasifikasi 1 kendaraan = 1 kategori paling genting, buat donut Overview (total harus pas = $workshopVehicleTotal)
        $rank = ['danger' => 3, 'warning' => 2, 'success' => 1, 'secondary' => 0];
        $workshopVehicleOverviewCounts = ['Ready' => 0, 'Servis Due' => 0, 'STNK/Pajak Due' => 0, 'Ganti Kaleng Due' => 0];
        foreach ($workshopVehicles as $v) {
            $candidates = [
                'Servis Due' => $v->servis_status,
                'STNK/Pajak Due' => $v->pajak_status,
                'Ganti Kaleng Due' => $v->ganti_kaleng_status,
            ];
            $mostUrgentKey = collect($candidates)->sortByDesc(fn ($s) => $rank[$s['color']])->keys()->first();
            $mostUrgent = $candidates[$mostUrgentKey];
            $workshopVehicleOverviewCounts[$mostUrgent['color'] === 'success' ? 'Ready' : $mostUrgentKey]++;
        }

        return compact(
            'workshopTotalUnit',
            'workshopStatusCounts',
            'workshopStatusOther',
            'workshopKondisiBaru',
            'workshopKondisiSecond',
            'workshopQcChecking',
            'workshopQcOk',
            'workshopQcReject',
            'workshopTotalNilaiAset',
            'workshopAttentionUnits',
            'workshopVehicles',
            'workshopVehicleTotal',
            'workshopVehicleServisDue',
            'workshopVehiclePajakDue',
            'workshopVehicleKalengDue',
            'workshopVehicleOverdueCount',
            'workshopVehicleOverviewCounts',
            'workshopRecentUnits',
        );
    }

    private function getSalesManagerDashboardData(): array
    {
        $dateNow = Carbon::now();
        $monthNow = $dateNow->month;
        $yearNow = $dateNow->year;
        $prevMonth = Carbon::create($yearNow, $monthNow, 1)->subMonth();

        $activeSales = User::where('role', 'Sales')->where('active', '1')->orderByDesc('id')->get();

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

        // ==== Pipeline by Stage (bulan berjalan, dipetakan dari status quotation) ====
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

        // ==== Revenue by Quotation Status (pengganti Revenue by Product - kategori Product tidak reliable) ====
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

        // id 16 & 23 = tim e-commerce, digabung jadi satu baris "Team E-Commerce" (sama seperti pola $teamIds di atas)
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

        // Denny Tonthawi (id 41) dikecualikan dari card Team Activity atas permintaan user
        $smActivityExcludeIds = [41];

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

            // KPI aktivitas bulan berjalan (pola sama seperti $filteredLeads/$filteredDC/$filteredCRM di branch Admin)
            $newLeads = Client::where('id_sales', $sale->id)
                ->whereYear('created_at', $yearNow)->whereMonth('created_at', $monthNow)->count();
            $dailyCall = Activities::join('client as c', 'activities.id_client', '=', 'c.id')
                ->where('c.id_sales', $sale->id)
                ->whereYear('activities.date', $yearNow)->whereMonth('activities.date', $monthNow)
                ->where('activities.status', 'Responded')
                ->whereIn('activities.name', ['Daily Call', 'Follow Up'])->count();
            $crm = Activities::join('client as c', 'activities.id_client', '=', 'c.id')
                ->join(DB::raw('(SELECT id_client, status FROM crm_status WHERE id IN (SELECT MAX(id) FROM crm_status GROUP BY id_client)) as cs'), 'c.id', '=', 'cs.id_client')
                ->where('c.id_sales', $sale->id)
                ->whereYear('activities.date', $yearNow)->whereMonth('activities.date', $monthNow)
                ->where('activities.status', 'Responded')->where('activities.name', 'CRM')
                ->where('cs.status', '2')->count(DB::raw('DISTINCT c.id'));

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

        // ==== Recent PO Won (pengganti Project Monitoring - tidak ada modul Project di sistem ini) ====
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

        // ==== Forecast 3 Bulan (pipeline terbuka berdasarkan estimated_date, termasuk deal yang estimasi closing-nya di bulan depan) ====
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

    public function overviewIndex()
    {
        $sales = User::where('role', 'Sales')->get();
        $totalPO = $sales->map(function ($sale) {
            $dateNow = Carbon::now();
            $monthNow = $dateNow->month;
            $yearNow = $dateNow->year;
            return $sale->quotation()->whereYear('po_date', $yearNow)->whereMonth('po_date', $monthNow)->where('status', '100')->where('level', '1')->where('is_primary', '1')->sum('nett');
        });
        $totalForecast = $sales->map(function ($sale) {
            $dateNow = Carbon::now();
            $monthNow = $dateNow->month;
            $yearNow = $dateNow->year;
            $total = $sale->quotation()->whereYear('estimated_date', $yearNow)->whereMonth('estimated_date', $monthNow)->whereIn('status', ['20', '30', '40', '60', '80'])->where('level', '1')->where('is_primary', '1')->sum('nett');
            return number_format($total, 2, ',', '.');
        });
        $filteredPO = $sales->map(function ($sale) {
            $dateNow = Carbon::now();
            $monthNow = $dateNow->month;
            $yearNow = $dateNow->year;
            return $sale->quotation()->whereYear('po_date', $yearNow)->whereMonth('po_date', $monthNow)->where('status', '100')->where('level', '1')->where('is_primary', '1')->count();
        });
        $filteredLeads = $sales->map(function ($sale) {
            $dateNow = Carbon::now();
            $monthNow = $dateNow->month;
            $yearNow = $dateNow->year;
            return $sale->client()->whereYear('created_at', $yearNow)->whereMonth('created_at', $monthNow)->count();
        });
        $filteredQuote = $sales->map(function ($sale) {
            $dateNow = Carbon::now();
            $monthNow = $dateNow->month;
            $yearNow = $dateNow->year;
            return $sale->quotation()->whereYear('estimated_date', $yearNow)->whereMonth('estimated_date', $monthNow)->where('level', '1')->where('is_primary', '1')->count();
        });
        $filteredDC = $sales->map(function ($sale) {
            $dateNow = Carbon::now();
            $monthNow = $dateNow->month;
            $yearNow = $dateNow->year;
            return $sale->clients->flatMap(function ($client) use ($monthNow, $yearNow) {
                return $client->activities()->whereYear('date', $yearNow)->whereMonth('date', $monthNow)->where('status', 'Responded')->whereIn('name', ['Daily Call', 'Follow Up'])->distinct('client.id')->get();
            })->count();
        });
        $filteredCRM = $sales->map(function ($sale) {
            $dateNow = Carbon::now();
            $monthNow = $dateNow->month;
            $yearNow = $dateNow->year;
            return $sale->clients->flatMap(function ($client) use ($monthNow, $yearNow) {
                return $client->activities()->whereYear('date', $yearNow)->whereMonth('date', $monthNow)->where('status', 'Responded')->where('name', 'CRM')->groupBy('client.id')->get();
            })->count();
        });
        $filteredVisit = $sales->map(function ($sale) {
            $dateNow = Carbon::now();
            $monthNow = $dateNow->month;
            $yearNow = $dateNow->year;
            return $sale->clients->flatMap(function ($client) use ($monthNow, $yearNow) {
                return $client->activities()->whereYear('date', $yearNow)->whereMonth('date', $monthNow)->where('status', 'Responded')->where('name', 'Visit')->get();
            })->count();
        });
        $targett = $sales->map(function ($sale) {
            return $sale->target()->pluck('total')->sum();
        });
        // dd($targett);
        return view('pages.admin.overview', compact('visit', 'dailyCall', 'quotation', 'po', 'customers', 'sales', 'totalPO', 'totalForecast', 'filteredLeads', 'filteredPO', 'filteredQuote', 'filteredDC', 'filteredVisit', 'filteredCRM', 'targett'));
    }
    public function notifIndex()
    {
        $before60 = Carbon::now()->subDays(60);
        $now = Carbon::now();
        $authId = Auth::id();

        // =======================
        // 1. Ambil comment admin
        // =======================
        $firstComments = Comment::where('id_user', $authId)
            ->select('id_status', DB::raw('MIN(created_at) as first_created_at'))
            ->groupBy('id_status')
            ->pluck('first_created_at', 'id_status');

        $statusIds = $firstComments->keys();

        $commentsQueryBase = Comment::join('change_status as c', 'c.id', '=', 'comment.id_status')
            ->join('quotation as q', 'q.id', '=', 'c.id_quotation')
            ->join('users as u', 'u.id', '=', 'comment.id_user')
            ->whereIn('comment.id_status', $statusIds)
            ->where('comment.id_user', '!=', $authId)
            ->orderByDesc('comment.date')
            ->whereBetween('comment.created_at', [$before60, $now])
            ->where(function ($query) use ($firstComments) {
                foreach ($firstComments as $statusId => $createdAt) {
                    $query->orWhere(function ($subQuery) use ($statusId, $createdAt) {
                        $subQuery->where('comment.id_status', $statusId)
                            ->where('comment.created_at', '>', $createdAt);
                    });
                }
            });

        $commentAdmin = (clone $commentsQueryBase)
            ->orderBy('comment.id_status')
            ->orderByDesc('comment.date')
            ->get([
                'q.id as idQ',
                'comment.id as idC',
                'comment.id_user',
                'comment.level',
                'comment.comment',
                'comment.date',
                'q.no_quote',
                'u.name',
                'u.image'
            ]);

        $unreadCommentAdmin = (clone $commentsQueryBase)
            ->where('comment.level', '1')
            ->orderBy('comment.id_status')
            ->orderByDesc('comment.date')
            ->get([
                'q.id as idQ',
                'comment.id as idC',
                'comment.id_user',
                'comment.level',
                'comment.comment',
                'comment.date',
                'q.no_quote',
                'u.name',
                'u.image'
            ]);

        $notifAdmin = (clone $commentsQueryBase)
            ->orderBy('comment.id_status')
            ->orderByDesc('comment.date')
            ->get([
                'q.id as idQ',
                'comment.id as idC',
                'comment.id_user',
                'comment.level',
                'comment.comment',
                'comment.date',
                'q.no_quote',
                'u.name',
                'u.image'
            ]);
        // =======================
        // 2. Unread comment (sales)
        // =======================
        $unreadComment = Quotation::join('change_status as c', 'c.id_quotation', '=', 'quotation.id')
            ->join('comment as o', 'o.id_status', '=', 'c.id')
            ->join('users as u', 'u.id', '=', 'o.id_user')
            ->where('quotation.id_sales', $authId)
            ->where('o.id_user', '!=', $authId)
            ->where('o.level', '1')
            ->whereBetween('o.created_at', [$before60, $now])
            ->orderBy('o.date', 'DESC')
            ->get([
                'quotation.id as idQ',
                'o.id as idC',
                'o.id_user',
                'o.level',
                'o.comment',
                'o.date',
                'quotation.no_quote',
                'u.name',
                'u.image'
            ]);

        // =======================
        // 3. Comment quotation & prospect
        // =======================
        $quotationComment = Quotation::join('change_status as c', 'c.id_quotation', '=', 'quotation.id')
            ->join('comment as o', 'o.id_status', '=', 'c.id')
            ->join('users as u', 'u.id', '=', 'o.id_user')
            // ->where('quotation.id_sales', $authId)
            ->where('o.type', 'quotation')
            ->where('o.id_user', '!=', $authId)
            ->whereBetween('o.created_at', [$before60, $now])
            ->orderBy('o.date', 'DESC')
            ->select([
                'quotation.id as idQ',
                'o.id as idC',
                'o.id_user',
                'o.level',
                'o.comment',
                'o.date',
                'o.type',
                'quotation.no_quote',
                'u.name',
                'u.image'
            ]);

        $prospectComment = Comment::join('prospect as p', 'comment.id_prospect', '=', 'p.id')
            ->join('users as u', 'u.id', '=', 'comment.id_user')
            ->join('pic as pi', 'pi.id', '=', 'p.id_pic')
            ->join('client as c', 'c.id', '=', 'pi.id_client')
            // ->where('p.id_sales', $authId)
            ->where('comment.type', 'prospect')
            ->where('comment.id_user', '!=', $authId)
            ->whereBetween('comment.created_at', [$before60, $now])
            ->orderBy('comment.date', 'DESC')
            ->select([
                'p.id as idP',
                'comment.id as idC',
                'comment.id_user',
                'comment.level',
                'comment.comment',
                'comment.date',
                'comment.type',
                'c.company',
                'u.name',
                'u.image'
            ]);

        $comment = $quotationComment
            ->union($prospectComment)
            ->orderBy('date', 'DESC')
            ->get();

        // dd($comment[1]);

        // =======================
        // 4. Activities
        // =======================
        $activities = DB::table('quotation')
            ->select(
                'id',
                'created_at',
                DB::raw("'quotation' as type"),
                'no_quote as detail',
                'num_rev as vers',
                DB::raw("'-' as status")
            )
            ->whereBetween('created_at', [$before60, $now])
            ->where('id_sales', $authId)
            ->unionAll(
                DB::table('activities')
                    ->select(
                        'activities.id',
                        'activities.created_at',
                        DB::raw("'activities' as type"),
                        'client.company as detail',
                        'activities.status as vers',
                        'activities.name as status'
                    )
                    ->join('client', 'client.id', '=', 'activities.id_client')
                    ->where('client.id_sales', $authId)
                    ->whereBetween('activities.created_at', [$before60, $now])
            )
            ->unionAll(
                DB::table('comment')
                    ->select(
                        'q.id',
                        'comment.created_at',
                        DB::raw("'comment' as type"),
                        'comment.comment as detail',
                        'q.no_quote as vers',
                        'u.name as status'
                    )
                    ->join('change_status as c', 'c.id', '=', 'comment.id_status')
                    ->join('quotation as q', 'q.id', '=', 'c.id_quotation')
                    ->join('users as u', 'u.id', '=', 'q.id_sales')
                    ->where('comment.id_user', $authId)
                    ->whereBetween('comment.created_at', [$before60, $now])
            )
            ->orderBy('created_at', 'desc')
            ->get();

        return view('pages.activity', compact(
            'unreadComment',
            'unreadCommentAdmin',
            'comment',
            'commentAdmin',
            'notifAdmin',
            'activities'
        ));
    }
    // public function notifIndex()
    // {
    //     // Comment Buat Admin
    //     $firstComments = Comment::where('id_user', Auth::id())
    //         ->groupBy('id_status')
    //         ->get();

    //     $statusIds = $firstComments->pluck('id_status')->toArray();
    //     $dates = $firstComments->pluck('created_at', 'id_status');

    //     $commentsQuery = Comment::join('change_status as c', 'c.id', '=', 'comment.id_status')
    //         ->join('quotation as q', 'q.id', '=', 'c.id_quotation')
    //         ->join('users as u', 'u.id', '=', 'comment.id_user')
    //         ->whereIn('comment.id_status', $statusIds)
    //         ->where(function ($query) use ($dates) {
    //             foreach ($dates as $statusId => $createdAt) {
    //                 $query->orWhere(function ($subQuery) use ($statusId, $createdAt) {
    //                     $subQuery->where('comment.id_status', $statusId)
    //                         ->whereRaw('TIMESTAMPDIFF(SECOND, ?, comment.created_at) > 0', [$createdAt]);
    //                 });
    //             }
    //         })
    //         ->where('comment.id_user', '!=', Auth::id());

    //     // Ambil semua komentar yang relevan
    //     $commentAdmin = $commentsQuery->orderBy('comment.id_status')
    //         ->orderByDesc('comment.created_at')
    //         ->get(['q.id as idQ', 'comment.id as idC', 'comment.id_user', 'comment.level', 'comment.comment', 'comment.date', 'q.no_quote', 'u.name', 'u.image']);

    //     // Filter untuk komentar dengan level '1'
    //     $unreadCommentAdmin = $commentsQuery->where('comment.level', '1')
    //         ->orderBy('comment.id_status')
    //         ->orderByDesc('comment.created_at')
    //         ->get(['q.id as idQ', 'comment.id as idC', 'comment.id_user', 'comment.level', 'comment.comment', 'comment.date', 'q.no_quote', 'u.name', 'u.image']);

    //     $before60 = Carbon::now()->subDays(60);
    //     $notifAdmin = $commentsQuery->orderBy('comment.id_status')
    //         ->orderBy('comment.created_at')
    //         ->whereBetween('comment.created_at', [$before60, Carbon::now()])
    //         ->get(['q.id as idQ', 'comment.id as idC', 'comment.id_user', 'comment.level', 'comment.comment', 'comment.date', 'q.no_quote', 'u.name', 'u.image']);
    //     // End Comment Admin

    //     $unreadComment = Quotation::join('change_status as c', 'c.id_quotation', '=', 'quotation.id')
    //         ->join('comment as o', first: 'o.id_status', operator: '=', second: 'c.id')
    //         ->join('users as u', 'u.id', '=', 'o.id_user')
    //         ->where('quotation.id_sales', Auth::id())
    //         ->whereNot('id_user', Auth::id())
    //         ->where('o.level', '1')
    //         ->orderBy('o.date', 'DESC')
    //         ->get(['quotation.id as idQ', 'o.id as idC', 'o.id_user', 'o.level', 'o.comment', 'o.date', 'quotation.no_quote', 'u.name', 'u.image']);


    //     $quotationComment = Quotation::join('change_status as c', 'c.id_quotation', '=', 'quotation.id')
    //         ->join('comment as o', 'o.id_status', '=', 'c.id')
    //         ->join('users as u', 'u.id', '=', 'o.id_user')
    //         ->where('quotation.id_sales', Auth::id())
    //         ->where('o.type', 'quotation')  // Pastikan filter type di sini
    //         ->whereBetween('o.created_at', [$before60, Carbon::now()])
    //         ->where('o.id_user', '!=', Auth::id())
    //         ->orderBy('o.date', 'DESC')
    //         ->select(['quotation.id as idQ', 'o.id as idC', 'o.id_user', 'o.level', 'o.comment', 'o.date', 'o.type', 'quotation.no_quote', 'u.name', 'u.image']);

    //     // Query untuk mengambil data dengan type "prospect"
    //     $prospectComment = Comment::join('prospect as p', 'comment.id_prospect', '=', 'p.id')
    //         ->join('users as u', 'u.id', '=', 'comment.id_user')
    //         ->join('pic as pi', 'pi.id', '=', 'p.id_pic')
    //         ->join('client as c', 'c.id', '=', 'pi.id_client')
    //         ->where('p.id_sales', Auth::id())
    //         ->where('comment.type', 'prospect')  // Pastikan filter type di sini
    //         ->where('comment.id_user', '!=', Auth::id())
    //         ->whereBetween('comment.created_at', [$before60, Carbon::now()])
    //         ->orderBy('comment.date', 'DESC')
    //         ->select(['p.id as idP', 'comment.id as idC', 'comment.id_user', 'comment.level', 'comment.comment', 'comment.date', 'comment.type', 'c.company', 'u.name', 'u.image']);

    //     // Menggabungkan kedua query menggunakan union
    //     $comment = $quotationComment->union($prospectComment)
    //         ->orderBy('date', 'DESC')
    //         ->get();

    //     // dd($comment->first()->type);
    //     // dd($unreadCommentAdmin);
    //     $activities = DB::table('quotation')
    //         ->select('id', 'created_at', DB::raw("'quotation' as type"), 'no_quote as detail', 'num_rev as vers', DB::raw("'-' as status"))
    //         ->whereBetween('created_at', [$before60, Carbon::now()]) // Mengambil data 7 hari ke belakang
    //         ->where('id_sales', Auth::id())
    //         ->unionAll(
    //             DB::table('activities')
    //                 ->select('activities.id', 'activities.created_at', DB::raw("'activities' as type"), 'client.company as detail', 'status as vers', 'name as status')
    //                 ->join('client', 'client.id', '=', 'activities.id_client')
    //                 ->where('id_sales', Auth::id())
    //                 ->whereBetween('activities.created_at', [$before60, Carbon::now()])
    //         )
    //         ->unionAll(
    //             DB::table('comment')
    //                 ->select('q.id', 'comment.created_at', DB::raw("'comment' as type"), 'comment.comment as detail', 'no_quote as vers', 'name as status')
    //                 ->join('change_status as c', 'c.id', '=', 'comment.id_status')
    //                 ->join('quotation as q', 'q.id', '=', 'c.id_quotation')
    //                 ->join('users as u', 'u.id', '=', 'q.id_sales')
    //                 ->where('id_user', Auth::id())
    //                 ->whereBetween('comment.created_at', [$before60, Carbon::now()])
    //         )
    //         ->orderBy('created_at', 'desc') // Mengurutkan berdasarkan created_at
    //         ->get();
    //     // dd($activities);
    //     return view('pages.activity', compact('unreadComment', 'unreadCommentAdmin', 'comment', 'commentAdmin', 'notifAdmin', 'activities'));
    // }
    public function dateNotif($date)
    {
        $quotationComment = Quotation::join('change_status as c', 'c.id_quotation', '=', 'quotation.id')
            ->join('comment as o', 'o.id_status', '=', 'c.id')
            ->join('users as u', 'u.id', '=', 'o.id_user')
            ->where('quotation.id_sales', Auth::id())
            ->where('o.type', 'quotation')  // Pastikan filter type di sini
            ->whereDate('o.created_at', $date)
            ->where('o.id_user', '!=', Auth::id())
            ->orderBy('o.date', 'DESC')
            ->select(['quotation.id as idQ', 'o.id as idC', 'o.id_user', 'o.level', 'o.comment', 'o.date', 'o.type', 'quotation.no_quote', 'u.name', 'u.image']);

        // Query untuk mengambil data dengan type "prospect"
        $prospectComment = Comment::join('prospect as p', 'comment.id_prospect', '=', 'p.id')
            ->join('users as u', 'u.id', '=', 'comment.id_user')
            ->join('pic as pi', 'pi.id', '=', 'p.id_pic')
            ->join('client as c', 'c.id', '=', 'pi.id_client')
            ->where('p.id_sales', Auth::id())
            ->where('comment.type', 'prospect')  // Pastikan filter type di sini
            ->where('comment.id_user', '!=', Auth::id())
            ->whereDate('comment.created_at', $date)
            ->orderBy('comment.date', 'DESC')
            ->select(['p.id as idP', 'comment.id as idC', 'comment.id_user', 'comment.level', 'comment.comment', 'comment.date', 'comment.type', 'c.company', 'u.name', 'u.image']);

        // Menggabungkan kedua query menggunakan union
        $comment = $quotationComment->union($prospectComment)
            ->orderBy('date', 'DESC')
            ->get();
        return $comment;
    }
    public function dateActivity($date)
    {
        $activities = DB::table('quotation')
            ->select('id', 'created_at', DB::raw("'quotation' as type"), 'no_quote as detail', 'num_rev as vers', DB::raw("'-' as status"))
            ->whereDate('created_at', $date) // Mengambil data 7 hari ke belakang
            ->where('id_sales', Auth::id())
            ->unionAll(
                DB::table('activities')
                    ->select('activities.id', 'activities.created_at', DB::raw("'activities' as type"), 'client.company as detail', 'status as vers', 'name as status')
                    ->join('client', 'client.id', '=', 'activities.id_client')
                    ->where('id_sales', Auth::id())
                    ->whereDate('activities.created_at', $date)
            )
            ->unionAll(
                DB::table('comment')
                    ->select('q.id', 'comment.created_at', DB::raw("'comment' as type"), 'comment.comment as detail', 'no_quote as vers', 'name as status')
                    ->join('change_status as c', 'c.id', '=', 'comment.id_status')
                    ->join('quotation as q', 'q.id', '=', 'c.id_quotation')
                    ->join('users as u', 'u.id', '=', 'q.id_sales')
                    ->where('id_user', Auth::id())
                    ->whereDate('comment.created_at', $date)
            )
            ->orderBy('created_at', 'desc') // Mengurutkan berdasarkan created_at
            ->get();

        return $activities;
    }
    public function dateNotifAdmin($date)
    {

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

        $adminNotif = $commentsQuery->orderBy('comment.id_status')
            ->orderBy('comment.created_at')
            ->whereDate('comment.created_at', $date)
            ->get(['q.id as idQ', 'comment.id as idC', 'comment.id_user', 'comment.level', 'comment.comment', 'comment.date', 'q.no_quote', 'u.name', 'u.image']);

        return $adminNotif;
    }
    // Ajax Sales Kanan
    public function totalQuotationAdmin($sales)
    {
        $dateNow = Carbon::now();
        $monthNow = $dateNow->month;
        $yearNow = $dateNow->year;
        $totalQuotation = Quotation::whereYear('estimated_date', $yearNow)->whereMonth('estimated_date', $monthNow)->where('id_sales', $sales)->where('level', '1')->where('is_primary', '1')->sum('nett');
        return $totalQuotation;
    }
    public function totalProspectAdmin($sales)
    {
        $dateNow = Carbon::now();
        $monthNow = $dateNow->month;
        $yearNow = $dateNow->year;
        $totalProspect = Quotation::join('prospect as p', 'quotation.id', '=', 'p.id_quotation')->whereNotNull('id_quotation')->whereYear('estimated_date', $yearNow)->whereMonth('estimated_date', $monthNow)->where('id_sales', $sales)->whereIn('status', ['80', '90'])->where('level', '1')->where('is_primary', '1')->sum('nett');
        return $totalProspect;
    }
    public function totalHotProspectAdmin($sales)
    {
        $dateNow = Carbon::now();
        $monthNow = $dateNow->month;
        $yearNow = $dateNow->year;
        $totalHotProspect = Quotation::whereYear('estimated_date', $yearNow)->whereMonth('estimated_date', $monthNow)->where('id_sales', $sales)->whereIn('status', ['80', '90'])->where('level', '1')->where('is_primary', '1')->sum('nett');
        return $totalHotProspect;
    }

    public function totalLossAdmin($sales)
    {
        $dateNow = Carbon::now();
        $monthNow = $dateNow->month;
        $yearNow = $dateNow->year;
        $totalProspect = Quotation::whereYear('estimated_date', $yearNow)->whereMonth('estimated_date', $monthNow)->where('id_sales', $sales)->where('status', '0')->where('level', '1')->where('is_primary', '1')->sum('nett');
        return $totalProspect;
    }
    public function totalPoAdmin($sales)
    {
        $dateNow = Carbon::now();
        $monthNow = $dateNow->month;
        $yearNow = $dateNow->year;

        $totalPO = Quotation::whereYear('po_date', $yearNow)
            ->whereMonth('po_date', $monthNow)
            ->where('id_sales', $sales)
            ->where('status', '100')
            ->where('level', '1')
            ->where('is_primary', '1')
            ->sum('nett')
            + UnitQuotation::where('status', 'po_received')
                ->where('is_latest', 1)
                ->whereYear('po_received', $yearNow)
                ->whereMonth('po_received', $monthNow)
                ->where('id_sales', $sales)
                ->sum(DB::raw('total - tax_amount'));

        return $totalPO;
    }
    public function totalTargetPoAdmin($sales)
    {
        $dateNow = Carbon::now();
        $monthNow = $dateNow->month;
        $yearNow = $dateNow->year;

        $totalPO = Quotation::whereYear('po_date', $yearNow)
            ->whereMonth('po_date', $monthNow)
            ->where('id_sales', $sales)
            ->where('status', '100')
            ->where('level', '1')
            ->where('is_primary', '1')
            ->sum('nett')
            + UnitQuotation::where('status', 'po_received')
                ->where('is_latest', 1)
                ->whereYear('po_received', $yearNow)
                ->whereMonth('po_received', $monthNow)
                ->where('id_sales', $sales)
                ->sum(DB::raw('total - tax_amount'));

        $target = Target::where('id_sales', $sales)->first('total');

        $totalTarget = ($totalPO / $target->total) * 100;

        return round($totalTarget, 2);
    }

    public function filteredPOAdmin($sales)
    {
        $dateNow = Carbon::now();
        $monthNow = $dateNow->month;
        $yearNow = $dateNow->year;
        $filteredPO = Quotation::whereYear('po_date', $yearNow)->whereMonth('po_date', $monthNow)->where('id_sales', $sales)->where('status', '100')->where('level', '1')->where('is_primary', '1')->count()
            + UnitQuotation::where('status', 'po_received')->where('is_latest', 1)->whereYear('po_received', $yearNow)->whereMonth('po_received', $monthNow)->where('id_sales', $sales)->count();
        return $filteredPO;
    }
    // Ajax Kiri Sales
    public function filteredLeadsAdmin($sales)
    {
        $dateNow = Carbon::now();
        $monthNow = $dateNow->month;
        $yearNow = $dateNow->year;
        $filteredLeads = Client::whereYear('created_at', $yearNow)->whereMonth('created_at', $monthNow)->where('id_sales', $sales)->count();
        return $filteredLeads;
    }
    public function filteredPercentLeadsAdmin($sales)
    {
        $dateNow = Carbon::now();
        $monthNow = $dateNow->month;
        $yearNow = $dateNow->year;
        $filteredLeads = Client::whereYear('created_at', $yearNow)->whereMonth('created_at', $monthNow)->where('id_sales', $sales)->count();
        $target = Target::where('id_sales', $sales)->first('leads');
        $leadsTarget = ($filteredLeads / $target->leads) * 100;
        return round($leadsTarget);
    }
    public function filteredTargetLeadsAdmin($sales)
    {
        $target = Target::where('id_sales', $sales)->first('leads');
        return $target;
    }
    public function filteredDcAdmin($sales)
    {
        $dateNow = Carbon::now();
        $monthNow = $dateNow->month;
        $yearNow = $dateNow->year;
        $filteredDC = Activities::join('client as c', 'activities.id_client', '=', 'c.id')
            ->whereYear('date', $yearNow)
            ->whereMonth('date', $monthNow)
            ->where('c.id_sales', $sales)
            ->where('status', 'Responded')
            ->whereIn('name', ['Daily Call', 'Follow Up'])
            ->distinct('c.id')
            ->count();
        return $filteredDC;
    }
    public function filteredTargetDcAdmin($sales)
    {
        $target = Target::where('id_sales', $sales)->first('dc');
        return $target;
    }
    public function filteredPercentDcAdmin($sales)
    {
        $dateNow = Carbon::now();
        $monthNow = $dateNow->month;
        $yearNow = $dateNow->year;
        $filteredDC = Activities::join('client as c', 'activities.id_client', '=', 'c.id')
            ->whereYear('date', $yearNow)
            ->whereMonth('date', $monthNow)
            ->where('c.id_sales', $sales)
            ->where('status', 'Responded')
            ->whereIn('name', ['Daily Call', 'Follow Up'])
            ->distinct('c.id')
            ->count();
        $target = Target::where('id_sales', $sales)->first('dc');
        $dcTarget = ($filteredDC / $target->dc) * 100;
        return round($dcTarget);
    }
    public function filteredCrmAdmin($sales)
    {
        $dateNow = Carbon::now();
        $monthNow = $dateNow->month;
        $yearNow = $dateNow->year;
        $filteredCRM = Activities::join('client as c', 'activities.id_client', '=', 'c.id')->join(DB::raw('(SELECT id_client, status FROM crm_status WHERE id IN (SELECT MAX(id) FROM crm_status GROUP BY id_client)) as cs'), 'c.id', '=', 'cs.id_client')->whereYear('date', $yearNow)->whereMonth('date', $monthNow)->where('c.id_sales', $sales)->where('activities.status', 'Responded')->where('activities.name', 'CRM')->where('cs.status', '2')->count(DB::raw('DISTINCT c.id'));
        return $filteredCRM;
    }
    public function filteredTargetCRMAdmin($sales)
    {
        $target = Client::join(DB::raw('(SELECT id_client, status FROM crm_status WHERE id IN (SELECT MAX(id) FROM crm_status GROUP BY id_client)) as cs'), 'client.id', '=', 'cs.id_client')->where('role', 'Customers')->where('id_sales', $sales)->where('cs.status', '2')->count();
        return $target;
    }
    public function filteredPercentCRMAdmin($sales)
    {
        $dateNow = Carbon::now();
        $monthNow = $dateNow->month;
        $yearNow = $dateNow->year;
        $filteredCRM = Activities::join('client as c', 'activities.id_client', '=', 'c.id')->join(DB::raw('(SELECT id_client, status FROM crm_status WHERE id IN (SELECT MAX(id) FROM crm_status GROUP BY id_client)) as cs'), 'c.id', '=', 'cs.id_client')->whereYear('date', $yearNow)->whereMonth('date', $monthNow)->where('c.id_sales', $sales)->where('activities.status', 'Responded')->where('activities.name', 'CRM')->where('cs.status', '2')->count(DB::raw('DISTINCT c.id'));
        $target = Client::join(DB::raw('(SELECT id_client, status FROM crm_status WHERE id IN (SELECT MAX(id) FROM crm_status GROUP BY id_client)) as cs'), 'client.id', '=', 'cs.id_client')->where('role', 'Customers')->where('id_sales', $sales)->where('cs.status', '2')->count();
        $crmTarget = $target > 0 ? ($filteredCRM / $target) * 100 : 0;
        return round($crmTarget);
    }
    public function filteredQuoteAdmin($sales)
    {
        $dateNow = Carbon::now();
        $monthNow = $dateNow->month;
        $yearNow = $dateNow->year;
        $filteredQuote = Quotation::whereYear('estimated_date', $yearNow)->whereMonth('estimated_date', $monthNow)->where('id_sales', $sales)->where('level', '1')->where('is_primary', '1')->count();
        return $filteredQuote;
    }
    public function filteredTargetQuoteAdmin($sales)
    {
        $target = Target::where('id_sales', $sales)->first('quote');
        return $target;
    }
    public function filteredPercentQuoteAdmin($sales)
    {
        $dateNow = Carbon::now();
        $monthNow = $dateNow->month;
        $yearNow = $dateNow->year;
        $filteredQuote = Quotation::whereYear('po_date', $yearNow)->whereMonth('po_date', $monthNow)->where('id_sales', $sales)->where('level', '1')->where('is_primary', '1')->count();
        $target = Target::where('id_sales', $sales)->first('quote');
        $quoteTarget = ($filteredQuote / $target->quote) * 100;
        return round($quoteTarget);
    }
    public function filteredProspectAdmin($sales)
    {
        $dateNow = Carbon::now();
        $monthNow = $dateNow->month;
        $yearNow = $dateNow->year;
        $filteredProspect = Prospect::whereNotNull('id_quotation')->whereMonth('date', $monthNow)->whereYear('date', $yearNow)->where('id_sales', $sales)->count();
        return $filteredProspect;
    }
    public function filteredAllProspectAdmin($sales)
    {
        $dateNow = Carbon::now();
        $monthNow = $dateNow->month;
        $yearNow = $dateNow->year;
        $allProspect = Prospect::whereNotNull('id_quotation')->whereMonth('date', $monthNow)->whereYear('date', $yearNow)->where('id_sales', $sales)->count();
        return $allProspect;
    }
    public function filteredPercentProspectAdmin($sales)
    {
        $dateNow = Carbon::now();
        $monthNow = $dateNow->month;
        $yearNow = $dateNow->year;
        $filteredProspect = Prospect::whereNotNull('id_quotation')->whereMonth('date', $monthNow)->whereYear('date', $yearNow)->where('id_sales', $sales)->count();
        $allProspect = Prospect::whereMonth('date', $monthNow)->whereYear('date', $yearNow)->where('id_sales', $sales)->count();
        $prospectTarget = ($filteredProspect / $allProspect ?? 0) * 100;
        return round($prospectTarget);
    }

    // Ajax Kiri Online
    public function filteredProductAdmin($sales)
    {
        $dateNow = Carbon::now();
        $monthNow = $dateNow->month;
        $yearNow = $dateNow->year;
        $product = SalesOnline::where('type', 'Product')->whereMonth('date', $monthNow)->whereYear('date', $yearNow)->where('id_sales', $sales)->count();
        return $product;
    }
    public function filteredSWAdmin($sales)
    {
        $monthNow = now()->month;
        $yearNow = now()->year;

        $airendSum = SalesOnline::where('type', 'SW')
            ->whereMonth('date', $monthNow)
            ->whereYear('date', $yearNow)
            ->where('id_sales', $sales)
            ->sum('airend');

        $kojishaSum = SalesOnline::where('type', 'SW')
            ->whereMonth('date', $monthNow)
            ->whereYear('date', $yearNow)
            ->where('id_sales', $sales)
            ->sum('kojisha');

        return $airendSum + $kojishaSum;
    }
    public function filteredVideoAdmin($sales)
    {
        $monthNow = now()->month;
        $yearNow = now()->year;

        $totalVideo = 0;

        $video = SalesOnline::where('type', 'Video')
            ->whereMonth('date', $monthNow)
            ->whereYear('date', $yearNow)
            ->where('id_sales', $sales)
            ->get();

        if ($video->isEmpty()) {
            return 0; // Hindari pembagian nol
        }

        foreach ($video as $item) {
            if (!empty($item->ig)) {
                $totalVideo += 30;
            }
            if (!empty($item->tiktok)) {
                $totalVideo += 30;
            }
            if (!empty($item->tokped)) {
                $totalVideo += 30;
            }
        }

        return $totalVideo / $video->count();
    }
    public function filteredStatAdmin($sales)
    {
        $monthNow = now()->month;
        $yearNow = now()->year;

        $stat = SalesOnline::where('type', 'Akurasi')
            ->whereMonth('date', $monthNow)
            ->whereYear('date', $yearNow)
            ->where('id_sales', $sales)
            ->get();

        if ($stat->isEmpty()) {
            return 0; // atau null, tergantung kebutuhan
        }

        return $stat->sum('average') / $stat->count();
    }
    public function filteredDeliveryAdmin($sales)
    {
        $monthNow = now()->month;
        $yearNow = now()->year;

        $delivery = SalesOnline::where('type', 'Delivery')
            ->whereMonth('date', $monthNow)
            ->whereYear('date', $yearNow)
            ->where('id_sales', $sales)
            ->get();

        if ($delivery->isEmpty()) {
            return 0; // atau null, tergantung kebutuhan
        }

        return $delivery->sum('average') / $delivery->count();
    }
    public function filteredCustomerAdmin($sales)
    {
        $monthNow = now()->month;
        $yearNow = now()->year;

        $customer = SalesOnline::where('type', 'Customer')
            ->whereMonth('date', $monthNow)
            ->whereYear('date', $yearNow)
            ->where('id_sales', $sales)
            ->get();

        if ($customer->isEmpty()) {
            return 0; // atau null, tergantung kebutuhan
        }

        return $customer->sum('average') / $customer->count();
    }
    public function filteredResponseAdmin($sales)
    {
        $monthNow = now()->month;
        $yearNow = now()->year;

        $response = SalesOnline::where('type', 'Response')
            ->whereMonth('date', $monthNow)
            ->whereYear('date', $yearNow)
            ->where('id_sales', $sales)
            ->get();

        if ($response->isEmpty()) {
            return 0; // atau null, tergantung kebutuhan
        }

        return $response->sum('average') / $response->count();
    }
    public function filteredRatingAdmin($sales)
    {
        $monthNow = now()->month;
        $yearNow = now()->year;

        $rating = SalesOnline::where('type', 'Rating')
            ->whereMonth('date', $monthNow)
            ->whereYear('date', $yearNow)
            ->where('id_sales', $sales)
            ->get();

        if ($rating->isEmpty()) {
            return 0; // atau null, tergantung kebutuhan
        }

        return $rating->sum('average') / $rating->count();
    }

    public function filteredVisitAdmin($sales)
    {
        $dateNow = Carbon::now();
        $monthNow = $dateNow->month;
        $yearNow = $dateNow->year;
        $filteredVisit = Activities::join('client as c', 'activities.id_client', '=', 'c.id')->whereYear('date', $yearNow)->whereMonth('date', $monthNow)->where('c.id_sales', $sales)->where('status', 'Responded')->where('name', 'Visit')->count();
        return $filteredVisit;
    }

    // Ajax Support
    public function filteredProspect($support)
    {
        $dateNow = Carbon::now();
        $monthNow = $dateNow->month;
        $yearNow = $dateNow->year;
        $filteredprospect = Prospect::whereYear('date', $yearNow)->whereMonth('date', $monthNow)->where('id_support', $support)->count();
        return $filteredprospect;
    }
    public function filteredProvide($support)
    {
        $dateNow = Carbon::now();
        $monthNow = $dateNow->month;
        $yearNow = $dateNow->year;
        $filteredprospect = Prospect::whereYear('date', $yearNow)->whereMonth('date', $monthNow)->where('id_support', $support)->count();
        $filteredProvide = Prospect::whereYear('date', $yearNow)->whereMonth('date', $monthNow)->where('provide', '!=', '0')->where('id_support', $support)->count();
        $percentedProvide = $filteredprospect > 0
            ? round(($filteredProvide / $filteredprospect) * 100, 2)
            : 0;
        return response()->json([
            'prospect' => $filteredprospect,
            'provide' => $filteredProvide,
            'percent' => $percentedProvide
        ]);
    }
    public function filteredProspectedQuotation($support)
    {
        $dateNow = Carbon::now();
        $monthNow = $dateNow->month;
        $yearNow = $dateNow->year;
        $filteredProspectQuote = Quotation::whereYear('estimated_date', $yearNow)->whereMonth('estimated_date', $monthNow)->where('id_support', $support)->where('level', '1')->where('is_primary', '1')->count();
        $filteredProvide = Prospect::whereYear('date', $yearNow)->whereMonth('date', $monthNow)->where('provide', '!=', '0')->where('id_support', $support)->count();
        $percentedQuotation = $filteredProvide > 0
            ? round(($filteredProspectQuote / $filteredProvide) * 100, 2)
            : 0;
        return response()->json([
            'quotation' => $filteredProspectQuote,
            'provide' => $filteredProvide,
            'percent' => $percentedQuotation
        ]);
    }
    public function filteredNotProvide($support)
    {
        $dateNow = Carbon::now();
        $monthNow = $dateNow->month;
        $yearNow = $dateNow->year;
        $filteredprospect = Prospect::whereYear('date', $yearNow)->whereMonth('date', $monthNow)->where('id_support', $support)->count();
        $filteredNotProvide = Prospect::whereYear('date', $yearNow)->whereMonth('date', $monthNow)->where('provide', '==', '0')->where('id_support', $support)->count();
        $percentedProvide = $filteredprospect > 0
            ? round(($filteredNotProvide / $filteredprospect) * 100, 2)
            : 0;
        return response()->json([
            'prospect' => $filteredprospect,
            'provide' => $filteredNotProvide,
            'percent' => $percentedProvide
        ]);
    }
    public function filteredProspectedPO($support)
    {
        $dateNow = Carbon::now();
        $monthNow = $dateNow->month;
        $yearNow = $dateNow->year;
        $filteredProspectPO = Quotation::whereYear('po_date', $yearNow)->whereMonth('po_date', $monthNow)->where('id_support', $support)->where('status', '100')->where('level', '1')->where('is_primary', '1')->count();
        $filteredProspectQuote = Quotation::whereYear('estimated_date', $yearNow)->whereMonth('estimated_date', $monthNow)->where('id_support', $support)->where('level', '1')->where('is_primary', '1')->count();
        $percentedQuotation = $filteredProspectQuote > 0
            ? round(($filteredProspectPO / $filteredProspectQuote) * 100, 2)
            : 0;
        return response()->json([
            'quotation' => $filteredProspectQuote,
            'po' => $filteredProspectPO,
            'percent' => $percentedQuotation
        ]);
    }
    public function totalProspectedQuotation($support)
    {
        $dateNow = Carbon::now();
        $monthNow = $dateNow->month;
        $yearNow = $dateNow->year;
        $totalProspectQuote = Quotation::whereYear('estimated_date', $yearNow)->whereMonth('estimated_date', $monthNow)->where('id_support', $support)->where('status', '!=', '100')->where('level', '1')->where('is_primary', '1')->sum('nett');
        $formattedQuote = number_format($totalProspectQuote, 0, ",", ".");
        return $formattedQuote;
    }
    public function totalProspectedProspect($support)
    {
        $dateNow = Carbon::now();
        $monthNow = $dateNow->month;
        $yearNow = $dateNow->year;
        $totalProspectProspect = Quotation::whereYear('estimated_date', $yearNow)->whereMonth('estimated_date', $monthNow)->where('id_support', $support)->whereIn('status', ['80', '90'])->where('level', '1')->where('is_primary', '1')->sum('nett');
        $formattedProspect = number_format($totalProspectProspect, 0, ",", ".");
        return $formattedProspect;
    }
    public function totalProspectedPO($support)
    {
        $dateNow = Carbon::now();
        $monthNow = $dateNow->month;
        $yearNow = $dateNow->year;
        $totalProspectPO = Quotation::whereYear('po_date', $yearNow)->whereMonth('po_date', $monthNow)->where('id_support', $support)->where('status', '100')->where('level', '1')->where('is_primary', '1')->sum('nett');
        $formattedPO = number_format($totalProspectPO, 0, ",", ".");
        return $formattedPO;
    }

    protected function formatNumber($number)
    {
        $satuan = ["", "ribu", "juta", "miliar", "triliun", "quadrillion"]; // Sesuaikan dengan kebutuhan

        $i = 0;
        while ($number >= 1000) {
            $number /= 1000;
            $i++;
        }

        // Menggunakan number_format untuk menghindari angka pecahan yang panjang
        $formattedAngka = number_format($number, 2, ',', '.');
        // $formattedAngka = number_format($number, ($i == 0 || $number >= 10) ? 2 : 0, ',', '.');

        // Menghilangkan angka pecahan jika nol di belakang koma
        $formattedAngka = rtrim($formattedAngka, '0');

        // Menghilangkan koma di belakang angka jika tidak ada angka pecahan
        $formattedAngka = rtrim($formattedAngka, '.');

        return $formattedAngka . ' ' . $satuan[$i];
    }

    protected function getDailyCallPersales()
    {
        $sales = User::where('role', 'Sales')->get();

        $totalActivitiesBySale = [];

        foreach ($sales as $sale) {
            // Mengambil semua activities untuk setiap client dalam Sale
            $activities = $sale->clients->flatMap(function ($client) {
                return $client->activities;
            });

            // Menghitung total activities
            $totalActivities = $activities->count();

            // Menyimpan total activities dalam array
            $totalActivitiesBySale[$sale->id] = $totalActivities;
        }

        // Tampilkan hasil
        dd($totalActivitiesBySale);
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
    protected function getWeekDataDC()
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

        $sales = User::where('role', 'sales')->get();

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
    protected function getWeekDataCRM()
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

        $sales = User::where('role', 'sales')->get();

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
    protected function getWeekDataVisit()
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

        $sales = User::where('role', 'sales')->get();

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
    protected function getWeekDataQuote()
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

        $sales = User::where('role', 'sales')->get();

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
    protected function getWeekDataPO()
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

        $sales = User::where('role', 'sales')->get();

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
    protected function getWeekDataLeads()
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

        $sales = User::where('role', 'sales')->get();

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

    protected function getQuotationSales()
    {
        $dateNow = Carbon::now();
        $monthNow = $dateNow->month;
        $yearNow = $dateNow->year;
        $quotation = Quotation::whereYear('estimated_date', $yearNow)->whereMonth("estimated_date", $monthNow)->where("id_sales", Auth::user()->id)->where('level', '1')->where('is_primary', '1')->get();
        return $quotation;
    }
    protected function getVisitSales()
    {
        $dateNow = Carbon::now();
        $monthNow = $dateNow->month;
        $yearNow = $dateNow->year;
        $visit = Activities::select('activities.*')
            ->join('client as c', 'activities.id_client', '=', 'c.id')
            ->join('users as u', 'c.id_sales', '=', 'u.id')
            ->whereYear('date', $yearNow)
            ->whereMonth("date", $monthNow)
            ->where('u.id', Auth::user()->id)
            ->where('status', 'Responded')
            ->where('activities.name', 'Visit')
            ->count();
        return $visit;
    }
    protected function getDailyCallSales()
    {
        $dateNow = Carbon::now();
        $monthNow = $dateNow->month;
        $yearNow = $dateNow->year;
        $dailyCall = Activities::select('activities.*')
            ->join('client as c', 'activities.id_client', '=', 'c.id')
            ->join('users as u', 'c.id_sales', '=', 'u.id')
            ->whereYear('date', $yearNow)
            ->whereMonth("date", $monthNow)
            ->where('u.id', Auth::user()->id)
            ->where('status', 'Responded')
            ->whereIn('activities.name', ['Daily Call', 'Follow Up'])
            ->distinct('c.id')
            ->count();
        return $dailyCall;
    }
    protected function getPoSales()
    {
        $dateNow = Carbon::now();
        $monthNow = $dateNow->month;
        $yearNow = $dateNow->year;
        $po = Quotation::whereYear('po_date', $yearNow)->whereMonth("po_date", $monthNow)->where("id_sales", Auth::user()->id)->where("status", "100")->where('level', '1')->where('is_primary', '1')->get();
        $unitPo = UnitQuotation::where('status', 'po_received')->where('is_latest', 1)->whereYear('po_received', $yearNow)->whereMonth('po_received', $monthNow)->where('id_sales', Auth::user()->id)->get();

        return $po->concat($unitPo);
    }
    protected function getLeadsSales()
    {
        $dateNow = Carbon::now();
        $monthNow = $dateNow->month;
        $yearNow = $dateNow->year;
        $leads = Client::whereYear('created_at', $yearNow)->whereMonth("created_at", $monthNow)->where("id_sales", Auth::user()->id)->get();

        return $leads;
    }
    protected function getCustomersSales()
    {
        $dateNow = Carbon::now();
        $monthNow = $dateNow->month;
        $yearNow = $dateNow->year;
        $customers = Activities::select('activities.*')
            ->join('client as c', 'activities.id_client', '=', 'c.id')
            ->join('users as u', 'c.id_sales', '=', 'u.id')
            ->whereYear('date', $yearNow)
            ->whereMonth("date", $monthNow)
            ->where('u.id', Auth::user()->id)
            ->where('status', 'Responded')
            ->where('activities.name', 'CRM')
            ->distinct('c.id')
            ->count();
        return $customers;
    }
    // protected function getDataOverview()
    // {
    //     $users = User::where('role', 'Sales')->get();

    //     $data = [];
    //     $month = Carbon::now()->month;
    //     $year = Carbon::now()->year;

    //     foreach ($users as $user) {
    //         $leadCounts = collect([
    //             1 => 0,
    //             2 => 0,
    //             3 => 0,
    //             4 => 0,
    //             5 => 0,
    //         ]);

    //         $crmCounts = collect([
    //             1 => 0,
    //             2 => 0,
    //             3 => 0,
    //             4 => 0,
    //             5 => 0,
    //         ]);

    //         $quoteCounts = collect([
    //             1 => 0,
    //             2 => 0,
    //             3 => 0,
    //             4 => 0,
    //             5 => 0,
    //         ]);

    //         $poCounts = collect([
    //             1 => 0,
    //             2 => 0,
    //             3 => 0,
    //             4 => 0,
    //             5 => 0,
    //         ]);

    //         // Ambil semua clients milik user
    //         foreach ($user->clients as $client) {
    //             $activities = $client->activities()
    //                 ->where('name', 'CRM')
    //                 ->whereMonth('date', $month)
    //                 ->whereYear('date', $year)
    //                 ->get();

    //             foreach ($activities as $activity) {
    //                 $week = (int) $activity->week;

    //                 if (isset($crmCounts[$week])) {
    //                     $crmCounts->put($week, $crmCounts->get($week, 0) + 1);
    //                 }
    //             }
    //         }

    //         $leads = $user->clients()
    //             ->whereMonth('created_at', $month)
    //             ->whereYear('created_at', $year)
    //             ->get();

    //         foreach ($leads as $lead) {
    //             $week = (int) $lead->week;

    //             if (isset($leadCounts[$week])) {
    //                 $leadCounts->put($week, $leadCounts->get($week, 0) + 1);
    //             }
    //         }
    //         $quotations = $user->quotation()
    //             ->whereMonth('estimated_date', $month)
    //             ->whereYear('estimated_date', $year)
    //             ->where('level', '1')
    //             ->where('is_primary', '1')
    //             ->get();

    //         foreach ($quotations as $quote) {
    //             $week = (int) $quote->week;

    //             if (isset($quoteCounts[$week])) {
    //                 $quoteCounts->put($week, $quoteCounts->get($week, 0) + 1);
    //             }
    //         }

    //         $POs = $user->quotation()
    //             ->whereMonth('estimated_date', $month)
    //             ->whereYear('estimated_date', $year)
    //             ->where('status', '100')
    //             ->where('level', '1')
    //             ->where('is_primary', '1')
    //             ->get();

    //         foreach ($POs as $po) {
    //             $week = (int) $po->week;

    //             if (isset($poCounts[$week])) {
    //                 $poCounts->put($week, $poCounts->get($week, 0) + 1);
    //             }
    //         }

    //         $data[] = [
    //             'salesId' => $user->id,
    //             'sales' => $user->name,
    //             'lead' => $leadCounts,
    //             'crm' => $crmCounts,
    //             'quote' => $quoteCounts,
    //             'po' => $poCounts,
    //         ];
    //     }
    //     return $data;
    // }

    protected function getDataOverview()
    {
        $month = Carbon::now()->month;
        $year = Carbon::now()->year;

        $users = User::with('clients')->where('role', 'Sales')->get();

        // Ambil semua data sekaligus
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
            // inisialisasi counter minggu (1–5)
            $leadCounts = collect([1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0]);
            $dcCounts = collect([1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0]);
            $crmCounts = collect([1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0]);
            $quoteCounts = collect([1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0]);
            $poCounts = collect([1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0]);

            // Filter data yang sesuai sales
            $clientIds = $user->clients->pluck('id');
            $userDC = $allDC->where('id_sales', $user->id);
            $userCRM = $allActivities->whereIn('id_client', $clientIds);
            $userLeads = $allLeads->where('id_sales', $user->id);
            $userQuotes = $allQuotes->where('id_sales', $user->id);
            $userPOs = $allPOs->where('id_sales', $user->id);

            // Hitung CRM
            foreach ($userCRM as $activity) {
                $week = Carbon::parse($activity->date)->weekOfMonth;
                $crmCounts->put($week, $crmCounts->get($week) + 1);
            }

            // Hitung DC
            foreach ($userDC as $dc) {
                $week = Carbon::parse($dc->date)->weekOfMonth;
                $dcCounts->put($week, $dcCounts->get($week) + 1);
            }

            // Hitung Leads
            foreach ($userLeads as $lead) {
                $week = Carbon::parse($lead->created_at)->weekOfMonth;
                $leadCounts->put($week, $leadCounts->get($week) + 1);
            }

            // Hitung Quotes
            foreach ($userQuotes as $quote) {
                $week = Carbon::parse($quote->estimated_date)->weekOfMonth;
                $quoteCounts->put($week, $quoteCounts->get($week) + 1);
            }

            // Hitung POs
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
