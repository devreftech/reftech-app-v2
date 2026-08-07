<?php

namespace App\Http\Controllers;

use App\Models\Activities;
use App\Models\Client;
use App\Models\Comment;
use App\Models\Prospect;
use App\Models\Quotation;
use App\Models\SalesOnline;
use App\Models\SalesReports;
use App\Models\SalesTargetHistory;
use App\Models\Target;
use App\Models\UnitQuotation;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OverviewController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (Auth::user()->role == 'Sales') {
            $reportId = request()->query('report_id');
            if ($reportId && str_starts_with($reportId, 'full_')) {
                $year = (int) str_replace('full_', '', $reportId);
                return $this->renderSalesOverviewDataFullYear($year);
            }
            if ($reportId) {
                $report = SalesReports::find($reportId);
            } else {
                $year = now()->year;
                $semester = now()->month <= 6 ? 1 : 2;
                $report = SalesReports::where('year', $year)->where('semester', $semester)->first();
                if (!$report) {
                    $report = SalesReports::orderBy('year', 'desc')->orderBy('semester', 'desc')->first();
                }
            }
            if ($report) {
                return $this->renderSalesOverviewData($report);
            }
        }

        $sales = User::where('role', 'Sales')->where('active', '1')->get();
        $support = User::find(22);
        return view('pages.overview', compact('sales', 'support'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (str_starts_with($id, 'full_')) {
            $year = (int) str_replace('full_', '', $id);
            return $this->renderSalesOverviewDataFullYear($year);
        }
        $report = SalesReports::find($id);
        if (!$report) {
            abort(404, 'Data semester tidak ditemukan.');
        }
        return $this->renderSalesOverviewData($report);
    }

    protected function renderSalesOverviewData($report, $salesId = null)
    {
        $salesId = $salesId ?: Auth::id();
        $user = User::find($salesId) ?: Auth::user();
        $year = $report->year;

        $getDCS1 = $this->getMonthlyDataDCSales(1, $year, $salesId);
        $getDCS2 = $this->getMonthlyDataDCSales(2, $year, $salesId);
        $getCRMS1 = $this->getMonthlyDataCRMSales(1, $year, $salesId);
        $getCRMS2 = $this->getMonthlyDataCRMSales(2, $year, $salesId);
        $getVisitS1 = $this->getMonthlyDataVisitSales(1, $year, $salesId);
        $getVisitS2 = $this->getMonthlyDataVisitSales(2, $year, $salesId);
        $getQuoteS1 = $this->getMonthlyDataQuoteSales(1, $year, $salesId);
        $getQuoteS2 = $this->getMonthlyDataQuoteSales(2, $year, $salesId);
        $getPOS1 = $this->getMonthlyDataPOSales(1, $year, $salesId);
        $getPOS2 = $this->getMonthlyDataPOSales(2, $year, $salesId);
        $getLeadsS1 = $this->getMonthlyDataLeadsSales(1, $year, $salesId);
        $getLeadsS2 = $this->getMonthlyDataLeadsSales(2, $year, $salesId);
        $getPOModalS1 = $this->getMonthlyDataPOModalSales(1, $year, $salesId);
        $getPOModalS2 = $this->getMonthlyDataPOModalSales(2, $year, $salesId);
        $getTotalForecastS1 = $this->getMonthlyDataTotalForecastSales(1, $year, $salesId);
        $getTotalForecastS2 = $this->getMonthlyDataTotalForecastSales(2, $year, $salesId);
        $getTotalPOS1 = $this->getMonthlyDataTotalPOSales(1, $year, $salesId);
        $getTotalPOS2 = $this->getMonthlyDataTotalPOSales(2, $year, $salesId);
        $getTotalQuoteNominalS1 = $this->getMonthlyDataTotalQuoteNominalSales(1, $year, $salesId);
        $getTotalQuoteNominalS2 = $this->getMonthlyDataTotalQuoteNominalSales(2, $year, $salesId);
        $targett = Target::where('id_sales', $salesId)->pluck('total')->sum();
        $noSaleProspect = Prospect::whereNULL('id_sales')->whereNull('provide')->count();
        $leveledProspect = Prospect::whereNULL('level')->count();

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
        $quotationComment = Quotation::join('change_status as c', 'c.id_quotation', '=', 'quotation.id')
            ->join('comment as o', 'o.id_status', '=', 'c.id')
            ->join('users as u', 'u.id', '=', 'o.id_user')
            ->where('quotation.id_sales', $salesId)
            ->where('o.type', 'quotation')  // Pastikan filter type di sini
            ->where('o.id_user', '!=', Auth::id())
            ->orderBy('o.date', 'DESC')
            ->select(['quotation.id as idQ', 'o.id as idC', 'o.id_user', 'o.level', 'o.comment', 'o.date', 'o.type', 'quotation.no_quote', 'u.name', 'u.image']);

        // Query untuk mengambil data dengan type "prospect"
        $prospectComment = Comment::join('prospect as p', 'comment.id_prospect', '=', 'p.id')
            ->join('users as u', 'u.id', '=', 'comment.id_user')
            ->join('pic as pi', 'pi.id', '=', 'p.id_pic')
            ->join('client as c', 'c.id', '=', 'pi.id_client')
            ->where('p.id_sales', $salesId)
            ->where('comment.type', 'prospect')  // Pastikan filter type di sini
            ->where('comment.id_user', '!=', Auth::id())
            ->orderBy('comment.date', 'DESC')
            ->select(['p.id as idP', 'comment.id as idC', 'comment.id_user', 'comment.level', 'comment.comment', 'comment.date', 'comment.type', 'c.company', 'u.name', 'u.image']);

        // Menggabungkan kedua query menggunakan union
        $comment = $quotationComment->union($prospectComment)
            ->orderBy('date', 'DESC')
            ->take(5)
            ->get();
        $unreadComment = $quotationComment->union($prospectComment)
            ->orderBy('date', 'DESC')
            ->where('o.level', '1')
            ->take(5)
            ->get();
        $getDC = $report->semester == 1 ? $getDCS1 : $getDCS2;
        $getCRM = $report->semester == 1 ? $getCRMS1 : $getCRMS2;
        $getVisit = $report->semester == 1 ? $getVisitS1 : $getVisitS2;
        $getQuote = $report->semester == 1 ? $getQuoteS1 : $getQuoteS2;
        $getPO = $report->semester == 1 ? $getPOS1 : $getPOS2;
        $getLeads = $report->semester == 1 ? $getLeadsS1 : $getLeadsS2;
        $getPOModal = $report->semester == 1 ? $getPOModalS1 : $getPOModalS2;
        $getTotalForecast = $report->semester == 1 ? $getTotalForecastS1 : $getTotalForecastS2;
        $getTotalPO = $report->semester == 1 ? $getTotalPOS1 : $getTotalPOS2;
        $getTotalQuoteNominal = $report->semester == 1 ? $getTotalQuoteNominalS1 : $getTotalQuoteNominalS2;
        $allReports = SalesReports::orderBy('year', 'desc')->orderBy('semester', 'desc')->get();
        $s1Report = $allReports->where('year', $report->year)->where('semester', 1)->first();
        $s2Report = $allReports->where('year', $report->year)->where('semester', 2)->first();
        $yearsList = $allReports->pluck('year')->unique()->sortDesc();

        return view('pages.overview', compact(
            'noSaleProspect', 'comment', 'unreadComment', 'commentAdmin', 'unreadCommentAdmin',
            'leveledProspect', 'report', 'getDC', 'getCRM', 'getVisit', 'getQuote', 'getPO',
            'getLeads', 'getPOModal', 'getTotalForecast', 'getTotalPO', 'getTotalQuoteNominal', 'targett', 'allReports',
            's1Report', 's2Report', 'yearsList', 'user'
        ));
    }

    protected function renderSalesOverviewDataFullYear($year, $salesId = null)
    {
        $salesId = $salesId ?: Auth::id();
        $user = User::find($salesId) ?: Auth::user();
        $report = (object) [
            'id' => 'full_' . $year,
            'year' => $year,
            'semester' => 'full'
        ];

        $getDCS1 = $this->getMonthlyDataDCSales(1, $year, $salesId);
        $getDCS2 = $this->getMonthlyDataDCSales(2, $year, $salesId);
        $getCRMS1 = $this->getMonthlyDataCRMSales(1, $year, $salesId);
        $getCRMS2 = $this->getMonthlyDataCRMSales(2, $year, $salesId);
        $getVisitS1 = $this->getMonthlyDataVisitSales(1, $year, $salesId);
        $getVisitS2 = $this->getMonthlyDataVisitSales(2, $year, $salesId);
        $getQuoteS1 = $this->getMonthlyDataQuoteSales(1, $year, $salesId);
        $getQuoteS2 = $this->getMonthlyDataQuoteSales(2, $year, $salesId);
        $getPOS1 = $this->getMonthlyDataPOSales(1, $year, $salesId);
        $getPOS2 = $this->getMonthlyDataPOSales(2, $year, $salesId);
        $getLeadsS1 = $this->getMonthlyDataLeadsSales(1, $year, $salesId);
        $getLeadsS2 = $this->getMonthlyDataLeadsSales(2, $year, $salesId);
        $getPOModalS1 = $this->getMonthlyDataPOModalSales(1, $year, $salesId);
        $getPOModalS2 = $this->getMonthlyDataPOModalSales(2, $year, $salesId);
        $getTotalForecastS1 = $this->getMonthlyDataTotalForecastSales(1, $year, $salesId);
        $getTotalForecastS2 = $this->getMonthlyDataTotalForecastSales(2, $year, $salesId);
        $getTotalPOS1 = $this->getMonthlyDataTotalPOSales(1, $year, $salesId);
        $getTotalPOS2 = $this->getMonthlyDataTotalPOSales(2, $year, $salesId);
        $getTotalQuoteNominalS1 = $this->getMonthlyDataTotalQuoteNominalSales(1, $year, $salesId);
        $getTotalQuoteNominalS2 = $this->getMonthlyDataTotalQuoteNominalSales(2, $year, $salesId);

        $getDC = $getDCS1 + $getDCS2;
        $getCRM = $getCRMS1 + $getCRMS2;
        $getVisit = $getVisitS1 + $getVisitS2;
        $getQuote = $getQuoteS1 + $getQuoteS2;
        $getPO = $getPOS1 + $getPOS2;
        $getLeads = $getLeadsS1 + $getLeadsS2;
        $getPOModal = $getPOModalS1 + $getPOModalS2;
        $getTotalForecast = $getTotalForecastS1 + $getTotalForecastS2;
        $getTotalPO = $getTotalPOS1 + $getTotalPOS2;
        $getTotalQuoteNominal = $getTotalQuoteNominalS1 + $getTotalQuoteNominalS2;

        $targett = Target::where('id_sales', $salesId)->pluck('total')->sum() * 2;
        $noSaleProspect = Prospect::whereNULL('id_sales')->whereNull('provide')->count();
        $leveledProspect = Prospect::whereNULL('level')->count();

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

        $commentAdmin = $commentsQuery->orderBy('comment.id_status')
            ->orderByDesc('comment.created_at')
            ->get(['q.id as idQ', 'comment.id as idC', 'comment.id_user', 'comment.level', 'comment.comment', 'comment.date', 'q.no_quote', 'u.name', 'u.image']);

        $unreadCommentAdmin = $commentsQuery->where('comment.level', '1')
            ->orderBy('comment.id_status')
            ->orderByDesc('comment.created_at')
            ->get(['q.id as idQ', 'comment.id as idC', 'comment.id_user', 'comment.level', 'comment.comment', 'comment.date', 'q.no_quote', 'u.name', 'u.image']);

        $quotationComment = Quotation::join('change_status as c', 'c.id_quotation', '=', 'quotation.id')
            ->join('comment as o', 'o.id_status', '=', 'c.id')
            ->join('users as u', 'u.id', '=', 'o.id_user')
            ->where('quotation.id_sales', Auth::id())
            ->where('o.type', 'quotation')
            ->where('o.id_user', '!=', Auth::id())
            ->orderBy('o.date', 'DESC')
            ->select(['quotation.id as idQ', 'o.id as idC', 'o.id_user', 'o.level', 'o.comment', 'o.date', 'o.type', 'quotation.no_quote', 'u.name', 'u.image']);

        $prospectComment = Comment::join('prospect as p', 'comment.id_prospect', '=', 'p.id')
            ->join('users as u', 'u.id', '=', 'comment.id_user')
            ->join('pic as pi', 'pi.id', '=', 'p.id_pic')
            ->join('client as c', 'c.id', '=', 'pi.id_client')
            ->where('p.id_sales', Auth::id())
            ->where('comment.type', 'prospect')
            ->where('comment.id_user', '!=', Auth::id())
            ->orderBy('comment.date', 'DESC')
            ->select(['p.id as idP', 'comment.id as idC', 'comment.id_user', 'comment.level', 'comment.comment', 'comment.date', 'comment.type', 'c.company', 'u.name', 'u.image']);

        $comment = $quotationComment->union($prospectComment)
            ->orderBy('date', 'DESC')
            ->take(5)
            ->get();
        $unreadComment = $quotationComment->union($prospectComment)
            ->orderBy('date', 'DESC')
            ->where('o.level', '1')
            ->take(5)
            ->get();

        $allReports = SalesReports::orderBy('year', 'desc')->orderBy('semester', 'desc')->get();
        $s1Report = $allReports->where('year', $year)->where('semester', 1)->first();
        $s2Report = $allReports->where('year', $year)->where('semester', 2)->first();
        $yearsList = $allReports->pluck('year')->unique()->sortDesc();

        return view('pages.overview', compact(
            'noSaleProspect', 'comment', 'unreadComment', 'commentAdmin', 'unreadCommentAdmin',
            'leveledProspect', 'report', 'getDC', 'getCRM', 'getVisit', 'getQuote', 'getPO',
            'getLeads', 'getPOModal', 'getTotalForecast', 'getTotalPO', 'getTotalQuoteNominal', 'targett', 'allReports',
            's1Report', 's2Report', 'yearsList'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function semesterOverviewsales($id)
    {
        $user = User::find($id);
        $noSaleProspect = Prospect::whereNULL('id_sales')->whereNull('provide')->count();
        $leveledProspect = Prospect::whereNULL('level')->count();
        return view('pages.admin.overview.semester', compact('noSaleProspect', 'leveledProspect', 'user'));
    }
    public function detailSemesterOverview($sales, $date)
    {
        $user = User::find($sales);
        $dates = $date;
        $dateOver = '01-' . $date;
        $dateCarbon = Carbon::createFromFormat('d-m-Y', $dateOver);

        $month = $dateCarbon->month;
        $year = $dateCarbon->year;
        $quotation = Quotation::where('status', '100')->where('id_sales', $sales)->where('level', '1')->where('is_primary', '1')->whereMonth('po_date', $month)->whereYear('po_date', $year)->get();
        // admin
        $target = Target::where('id_sales', $sales)->first();
        $totalDC = Activities::rightJoin('client', 'client.id', '=', 'activities.id_client')->whereMonth('date', $month)->whereYear('date', $year)->where('status', 'Responded')->whereIn('name', ['Daily Call', 'Follow Up'])->where('client.id_sales', $sales)->distinct('client.id')->count();
        $totalCRM = Activities::rightJoin('client', 'client.id', '=', 'activities.id_client')->whereMonth('date', $month)->whereYear('date', $year)->where('status', 'Responded')->where('name', 'CRM')->where('client.id_sales', $sales)->distinct('client.id')->count();
        $totalVisit = Activities::rightJoin('client', 'client.id', '=', 'activities.id_client')->whereMonth('date', $month)->whereYear('date', $year)->where('status', 'Responded')->where('name', 'Visit')->where('client.id_sales', $sales)->count();
        $totalQuote = Quotation::whereIn('status', ['20', '30', '40', '60', '80'])->whereMonth('estimated_date', $month)->whereYear('estimated_date', $year)->where('id_sales', $sales)->where('level', '1')->where('is_primary', '1')->count();
        $totalPO = Quotation::where('status', '100')->whereMonth('po_date', $month)->whereYear('po_date', $year)->where('id_sales', $sales)->where('level', '1')->where('is_primary', '1')->count()
            + UnitQuotation::where('status', 'po_received')->where('is_latest', 1)->whereMonth('po_received', $month)->whereYear('po_received', $year)->where('id_sales', $sales)->count();
        $totalLoss = Quotation::where('status', '0')->whereMonth('estimated_date', $month)->whereYear('estimated_date', $year)->where('id_sales', $sales)->where('level', '1')->where('is_primary', '1')->count();
        $totalProspect = Quotation::where('status', '80')->whereMonth('estimated_date', $month)->whereYear('estimated_date', $year)->where('id_sales', $sales)->where('level', '1')->where('is_primary', '1')->count();
        $totalLeads = Client::whereMonth('created_at', $month)->whereYear('created_at', $year)->where('id_sales', $sales)->count();
        $amountSales = Quotation::whereMonth('po_date', $month)->whereYear('po_date', $year)->where('status', '100')->where('id_sales', $sales)->where('level', '1')->where('is_primary', '1')->sum('nett')
            + UnitQuotation::where('status', 'po_received')->where('is_latest', 1)->whereMonth('po_received', $month)->whereYear('po_received', $year)->where('id_sales', $sales)->sum(DB::raw('total - tax_amount'));
        $amountProspect = Quotation::whereMonth('estimated_date', $month)->whereYear('estimated_date', $year)->where('status', '80')->where('id_sales', $sales)->where('level', '1')->where('is_primary', '1')->sum('nett');
        $amountQuote = Quotation::whereMonth('estimated_date', $month)->whereYear('estimated_date', $year)->whereIn('status', ['20', '30', '40', '60', '80'])->where('id_sales', $sales)->where('level', '1')->where('is_primary', '1')->sum('nett');
        $amountQuoteLoss = Quotation::whereMonth('estimated_date', $month)->whereYear('estimated_date', $year)->where('status', '0')->where('id_sales', $sales)->where('level', '1')->where('is_primary', '1')->sum('nett');
        $noSaleProspect = Prospect::whereNULL('id_sales')->whereNull('provide')->count();
        $leveledProspect = Prospect::whereNULL('level')->count();
        $jumlahCustomer = Client::where('role', 'Customers')->where('id_sales', $sales)->count();
        // support
        $filteredProspect = Prospect::whereYear('date', $year)->whereMonth('date', $month)->where('id_support', $sales)->count();
        $filteredProvide = Prospect::whereYear('date', $year)->whereMonth('date', $month)->where('provide', '!=', '0')->where('id_support', $sales)->count();
        $filteredProspectQuote = Quotation::whereYear('estimated_date', $year)->whereMonth('estimated_date', $month)->where('id_support', $sales)->where('level', '1')->where('is_primary', '1')->count();
        $filteredProspectPO = Quotation::whereYear('po_date', $year)->whereMonth('po_date', $month)->where('id_support', $sales)->where('status', '100')->where('level', '1')->where('is_primary', '1')->count()
            + UnitQuotation::where('status', 'po_received')->where('is_latest', 1)->whereYear('po_received', $year)->whereMonth('po_received', $month)->where('id_support', $sales)->count();
        $totalProspectQuote = Quotation::whereYear('estimated_date', $year)->whereMonth('estimated_date', $month)->where('id_support', $sales)->where('status', '!=', '100')->where('level', '1')->where('is_primary', '1')->sum('nett');
        $totalProspectPO = Quotation::whereYear('po_date', $year)->whereMonth('po_date', $month)->where('id_support', $sales)->where('status', '100')->where('level', '1')->where('is_primary', '1')->sum('nett')
            + UnitQuotation::where('status', 'po_received')->where('is_latest', 1)->whereYear('po_received', $year)->whereMonth('po_received', $month)->where('id_support', $sales)->sum(DB::raw('total - tax_amount'));
        // Overview Ari
        $akurasiCount = SalesOnline::where('id_sales', $sales)->where('type', 'Akurasi')->whereMonth('date', Carbon::now())->whereYear('date', Carbon::now())->get();
        $deliveryCount = SalesOnline::where('id_sales', $sales)->where('type', 'Delivery')->whereMonth('date', Carbon::now())->whereYear('date', Carbon::now())->get();
        $responseCount = SalesOnline::where('id_sales', $sales)->where('type', 'Response')->whereMonth('date', Carbon::now())->whereYear('date', Carbon::now())->get();
        $ratingCount = SalesOnline::where('id_sales', $sales)->where('type', 'Rating')->whereMonth('date', Carbon::now())->whereYear('date', Carbon::now())->get();
        $customerCount = SalesOnline::where('id_sales', $sales)->where('type', 'Customer')->whereMonth('date', Carbon::now())->whereYear('date', Carbon::now())->get();
        $videoCount = SalesOnline::where('id_sales', $sales)->where('type', 'Video')->whereMonth('date', Carbon::now())->whereYear('date', Carbon::now())->get();
        $SWCount = SalesOnline::where('id_sales', $sales)->where('type', 'SW')->whereMonth('date', Carbon::now())->whereYear('date', Carbon::now())->get();
        $productCount = SalesOnline::where('id_sales', $sales)->where('type', 'Product')->whereMonth('date', Carbon::now())->whereYear('date', Carbon::now())->count();
        $POCount = Quotation::where('id_sales', $sales)->where('is_primary', '1')->where('status', '100')->where('level', '1')->whereMonth('po_date', Carbon::now())->whereYear('po_date', Carbon::now())->count()
            + UnitQuotation::where('id_sales', $sales)->where('status', 'po_received')->where('is_latest', 1)->whereMonth('po_received', Carbon::now())->whereYear('po_received', Carbon::now())->count();
        $onlineSales = SalesOnline::selectRaw("DATE_FORMAT(date, '%d-%m-%Y') as date")
            ->selectRaw("GROUP_CONCAT(product SEPARATOR '|') as product")
            ->selectRaw("GROUP_CONCAT(desc_product SEPARATOR '|') as desc_product")
            ->whereMonth('date', Carbon::now())
            ->whereYear('date', Carbon::now())
            ->groupBy(DB::raw("DATE_FORMAT(date, '%d-%m-%Y')"))
            ->orderBy(DB::raw("STR_TO_DATE(DATE_FORMAT(date, '%d-%m-%Y'), '%d-%m-%Y')"))
            ->get();

        $onSale = $onlineSales->map(function ($row) {
            $productArray = explode('|', $row->product);

            return [
                'date' => $row->date,
                'qty' => count($productArray),
                'link' => [
                    'product' => $productArray,
                    'desc_product' => explode('|', $row->desc_product)
                ]
            ];
        });
        // dd($onSale);

        return view('pages.admin.overview.kpi', compact('onSale', 'POCount', 'productCount', 'SWCount', 'videoCount', 'customerCount', 'ratingCount', 'responseCount', 'deliveryCount', 'akurasiCount', 'totalProspect', 'totalProspectPO', 'totalProspectQuote', 'filteredProvide', 'filteredProspectPO', 'filteredProspectQuote', 'filteredProspect', 'jumlahCustomer', 'noSaleProspect', 'leveledProspect', 'user', 'dates', 'quotation', "totalDC", "totalCRM", "totalQuote", "totalVisit", "totalPO", "totalLoss", "totalLeads", "amountSales", "amountQuote", "amountQuoteLoss", "amountProspect", "target"));
    }

    public function overviewAdmin($semester, $sales)
    {
        if (str_starts_with($semester, 'full_')) {
            $year = (int) str_replace('full_', '', $semester);
            return $this->renderSalesOverviewDataFullYear($year, $sales);
        }

        $report = SalesReports::find($semester);
        if (!$report) {
            $report = SalesReports::where('semester', $semester)->orderBy('year', 'desc')->first()
                ?? SalesReports::orderBy('year', 'desc')->first();
        }

        return $this->renderSalesOverviewData($report, $sales);
    }
    public function reportCurrent()
    {
        $now = Carbon::now();
        $semester = $now->month > 6 ? 2 : 1;

        $report = SalesReports::where('semester', $semester)
            ->where('year', $now->year)
            ->first();

        if (!$report) {
            abort(404, 'Data semester untuk tahun ini belum tersedia.');
        }

        return redirect()->route('report.semester', $report->id);
    }

    public function supportReport(Request $request, $year = null, $month = null)
    {
        $now   = Carbon::now();
        $year  = (int) ($year  ?? $now->year);
        $month = (int) ($month ?? $now->month);

        if ($year < 2000 || $year > $now->year + 1) {
            $year = $now->year;
        }
        if ($month < 1 || $month > 12) {
            $month = $now->month;
        }

        $semesterParam = $request->query('semester');
        $mode          = $semesterParam ? 'semester' : 'monthly';
        $semester      = $semesterParam ? (int) $semesterParam : null;
        if ($semester && !in_array($semester, [1, 2])) {
            $semester = 1;
        }

        if ($mode === 'semester') {
            $firstDay = $semester == 1 ? "{$year}-01-01" : "{$year}-07-01";
            $lastDay  = $semester == 1
                ? date('Y-m-t', strtotime("{$year}-06-01"))
                : date('Y-m-t', strtotime("{$year}-12-01"));
        } else {
            $firstDay = Carbon::create($year, $month, 1)->startOfMonth()->toDateString();
            $lastDay  = Carbon::create($year, $month, 1)->endOfMonth()->toDateString();
        }

        $supportId = Auth::id();

        $yearList = SalesReports::select('year')->distinct()->orderBy('year', 'desc')->pluck('year');
        if (!$yearList->contains($year)) {
            $yearList = $yearList->push($year)->sortDesc()->values();
        }

        // 5 summary cards — scoped to this support/marketing user only
        $poCount      = Quotation::whereBetween('po_date', [$firstDay, $lastDay])->where('id_support', $supportId)->where('status', '100')->where('level', '1')->where('is_primary', '1')->count()
            + UnitQuotation::where('status', 'po_received')->where('is_latest', 1)->whereBetween('po_received', [$firstDay, $lastDay])->where('id_support', $supportId)->count();
        $poTotal      = Quotation::whereBetween('po_date', [$firstDay, $lastDay])->where('id_support', $supportId)->where('status', '100')->where('level', '1')->where('is_primary', '1')->sum('nett')
            + UnitQuotation::where('status', 'po_received')->where('is_latest', 1)->whereBetween('po_received', [$firstDay, $lastDay])->where('id_support', $supportId)->sum(DB::raw('total - tax_amount'));
        $quoteCount   = Quotation::whereBetween('estimated_date', [$firstDay, $lastDay])->where('id_support', $supportId)->whereIn('status', ['20', '40', '60', '80'])->where('level', '1')->where('is_primary', '1')->count();
        $quoteTotal   = Quotation::whereBetween('estimated_date', [$firstDay, $lastDay])->where('id_support', $supportId)->whereIn('status', ['20', '40', '60', '80'])->where('level', '1')->where('is_primary', '1')->sum('nett');
        $lossCount    = Quotation::whereBetween('estimated_date', [$firstDay, $lastDay])->where('id_support', $supportId)->where('status', '0')->where('level', '1')->where('is_primary', '1')->count();
        $lossTotal    = Quotation::whereBetween('estimated_date', [$firstDay, $lastDay])->where('id_support', $supportId)->where('status', '0')->where('level', '1')->where('is_primary', '1')->sum('nett');
        $quoteOnCount = Quotation::whereBetween('estimated_date', [$firstDay, $lastDay])->where('id_support', $supportId)->where('level', '1')->where('is_primary', '1')->count();

        // Marketing Report funnel — same support-scoped filter
        $mktProspectCount = Prospect::whereBetween('date', [$firstDay, $lastDay])->where('id_support', $supportId)->count();
        $mktQuoteCount    = Quotation::whereBetween('estimated_date', [$firstDay, $lastDay])->where('id_support', $supportId)->where('level', '1')->where('is_primary', '1')->count();
        $mktQuoteTotal    = Quotation::whereBetween('estimated_date', [$firstDay, $lastDay])->where('id_support', $supportId)->where('level', '1')->where('is_primary', '1')->sum('nett');
        $mktPoCount       = Quotation::whereBetween('po_date', [$firstDay, $lastDay])->where('id_support', $supportId)->where('status', '100')->where('level', '1')->where('is_primary', '1')->count()
            + UnitQuotation::where('status', 'po_received')->where('is_latest', 1)->whereBetween('po_received', [$firstDay, $lastDay])->where('id_support', $supportId)->count();
        $mktPoTotal       = Quotation::whereBetween('po_date', [$firstDay, $lastDay])->where('id_support', $supportId)->where('status', '100')->where('level', '1')->where('is_primary', '1')->sum('nett')
            + UnitQuotation::where('status', 'po_received')->where('is_latest', 1)->whereBetween('po_received', [$firstDay, $lastDay])->where('id_support', $supportId)->sum(DB::raw('total - tax_amount'));

        $mktProspectBySource = Prospect::join('pic', 'pic.id', '=', 'prospect.id_pic')
            ->join('client', 'client.id', '=', 'pic.id_client')
            ->whereBetween('prospect.date', [$firstDay, $lastDay])
            ->where('prospect.id_support', $supportId)
            ->selectRaw('COALESCE(client.source, "Other") as source, COUNT(*) as total')
            ->groupBy('source')->orderByDesc('total')->get();

        $mktProspectByCategory = Prospect::whereBetween('date', [$firstDay, $lastDay])
            ->where('id_support', $supportId)
            ->selectRaw('COALESCE(category, "Uncategorized") as category, COUNT(*) as total')
            ->groupBy('category')->orderByDesc('total')->get();

        $mktProspectByArea = Prospect::join('pic', 'pic.id', '=', 'prospect.id_pic')
            ->join('client', 'client.id', '=', 'pic.id_client')
            ->whereBetween('prospect.date', [$firstDay, $lastDay])
            ->where('prospect.id_support', $supportId)
            ->selectRaw('COALESCE(NULLIF(client.area, ""), "Unknown") as area, COUNT(*) as total')
            ->groupBy('area')->orderByDesc('total')->get();

        $mktProspectByDomain = Prospect::join('pic', 'pic.id', '=', 'prospect.id_pic')
            ->join('client', 'client.id', '=', 'pic.id_client')
            ->whereBetween('prospect.date', [$firstDay, $lastDay])
            ->where('prospect.id_support', $supportId)
            ->where('client.source', 'Website')
            ->whereNotNull('client.source_detail')
            ->where('client.source_detail', '!=', '')
            ->selectRaw('client.source_detail as domain, COUNT(*) as total')
            ->groupBy('domain')->orderByDesc('total')->get();

        $mktProspectByStatus = Prospect::whereBetween('date', [$firstDay, $lastDay])
            ->where('id_support', $supportId)
            ->selectRaw('
                SUM(CASE WHEN provide IS NULL THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN provide = "1"  THEN 1 ELSE 0 END) as provided,
                SUM(CASE WHEN provide = "0"  THEN 1 ELSE 0 END) as no_provide
            ')
            ->first();

        $mktPerPerson = Prospect::join('users', 'users.id', '=', 'prospect.id_support')
            ->whereBetween('prospect.date', [$firstDay, $lastDay])
            ->where('prospect.id_support', $supportId)
            ->selectRaw('
                users.id, users.name, users.image,
                COUNT(*) as total,
                SUM(CASE WHEN prospect.provide = "1"  THEN 1 ELSE 0 END) as provided,
                SUM(CASE WHEN prospect.provide = "0"  THEN 1 ELSE 0 END) as no_provide,
                SUM(CASE WHEN prospect.provide IS NULL THEN 1 ELSE 0 END) as pending
            ')
            ->groupBy('users.id', 'users.name', 'users.image')
            ->orderByDesc('total')
            ->get();

        $mktLossCount = Quotation::whereBetween('estimated_date', [$firstDay, $lastDay])->where('id_support', $supportId)->where('status', '0')->where('level', '1')->where('is_primary', '1')->count();
        $mktLossTotal = Quotation::whereBetween('estimated_date', [$firstDay, $lastDay])->where('id_support', $supportId)->where('status', '0')->where('level', '1')->where('is_primary', '1')->sum('nett');

        return view('pages.support.report.index', compact(
            'mode', 'year', 'month', 'yearList', 'semester',
            'poCount', 'poTotal', 'quoteCount', 'quoteTotal',
            'lossCount', 'lossTotal', 'quoteOnCount',
            'mktProspectCount', 'mktQuoteCount', 'mktQuoteTotal',
            'mktPoCount', 'mktPoTotal',
            'mktProspectBySource', 'mktProspectByCategory', 'mktProspectByArea', 'mktProspectByDomain',
            'mktProspectByStatus', 'mktPerPerson', 'mktLossCount', 'mktLossTotal'
        ));
    }

    public function reportsSemester($semester)
    {
        $report = SalesReports::find($semester);
        $semester = SalesReports::all();
        $startMonth = $report->semester == 1 ? 1 : 7;
        $endMonth = $report->semester == 1 ? 6 : 12;
        if ($report->semester == 1) {
            $firstDayOfMonth = "{$report->year}-1-01";
            $firstDayOfLastMonth = "{$report->year}-6-01";
            $lastDayOfMonth = date('Y-m-t', strtotime($firstDayOfLastMonth));
        } else {
            $firstDayOfMonth = "{$report->year}-07-01";
            $firstDayOfLastMonth = "{$report->year}-12-01";
            $lastDayOfMonth = date('Y-m-t', strtotime($firstDayOfLastMonth));
        }
        $poCount = Quotation::whereBetween('po_date', [$firstDayOfMonth, $lastDayOfMonth])->where('status', '100')->where('level', '1')->where('is_primary', '1')->count()
            + UnitQuotation::where('status', 'po_received')->where('is_latest', 1)->whereBetween('po_received', [$firstDayOfMonth, $lastDayOfMonth])->count();
        $lossCount = Quotation::whereBetween('estimated_date', [$firstDayOfMonth, $lastDayOfMonth])->where('status', '0')->where('level', '1')->where('is_primary', '1')->count();
        $quoteCount = Quotation::whereBetween('estimated_date', [$firstDayOfMonth, $lastDayOfMonth])->whereIn('status', ['20', '40', '60', '80', '90'])->where('level', '1')->where('is_primary', '1')->count();
        $quoteOnCount = Quotation::whereBetween('estimated_date', [$firstDayOfMonth, $lastDayOfMonth])->where('level', '1')->where('is_primary', '1')->count();
        $poTotal = Quotation::whereBetween('po_date', [$firstDayOfMonth, $lastDayOfMonth])->where('status', '100')->where('level', '1')->where('is_primary', '1')->sum('nett')
            + UnitQuotation::where('status', 'po_received')->where('is_latest', 1)->whereBetween('po_received', [$firstDayOfMonth, $lastDayOfMonth])->sum(DB::raw('total - tax_amount'));
        $lossTotal = Quotation::whereBetween('estimated_date', [$firstDayOfMonth, $lastDayOfMonth])->where('status', '0')->where('level', '1')->where('is_primary', '1')->sum('nett');
        $quoteTotal = Quotation::whereBetween('estimated_date', [$firstDayOfMonth, $lastDayOfMonth])->whereIn('status', ['20', '40', '60', '80', '90'])->where('level', '1')->where('is_primary', '1')->sum('nett');
        $quoteOnTotal = Quotation::whereBetween('estimated_date', [$firstDayOfMonth, $lastDayOfMonth])->where('level', '1')->where('is_primary', '1')->sum('nett');
        $sales = User::where('role', 'Sales')->where('active', '1')->get();
        $totalTarget = $report->target ? intval($report->target / 6) : 0;
        $support = User::find('22');
        $dataSupport = DB::table('quotation')
            ->selectRaw('MONTH(po_date) as bulan, SUM(nett) as total')
            ->whereNotNull('id_support')
            ->where('status', 100)
            ->where('level', '1')
            ->where('is_primary', '1')
            ->whereBetween('po_date', [$firstDayOfMonth, $lastDayOfMonth])
            ->groupBy(DB::raw('MONTH(po_date)'))
            ->get();
        $poTotalSupport    = Quotation::whereBetween('po_date', [$firstDayOfMonth, $lastDayOfMonth])->whereNotNull('id_support')->where('status', '100')->where('level', '1')->where('is_primary', '1')->sum('nett');
        $quoteTotalSupport = Quotation::whereBetween('estimated_date', [$firstDayOfMonth, $lastDayOfMonth])->whereNotNull('id_support')->where('level', '1')->where('is_primary', '1')->sum('nett');
        $quoteCountSupport = Quotation::whereBetween('estimated_date', [$firstDayOfMonth, $lastDayOfMonth])->whereNotNull('id_support')->where('level', '1')->where('is_primary', '1')->count();

        // Marketing report — semester
        $smktProspectCount = Prospect::whereBetween('date', [$firstDayOfMonth, $lastDayOfMonth])->whereNotNull('id_support')->count();
        $smktQuoteCount    = Quotation::whereBetween('estimated_date', [$firstDayOfMonth, $lastDayOfMonth])->whereNotNull('id_support')->where('level', '1')->where('is_primary', '1')->count();
        $smktQuoteTotal    = Quotation::whereBetween('estimated_date', [$firstDayOfMonth, $lastDayOfMonth])->whereNotNull('id_support')->where('level', '1')->where('is_primary', '1')->sum('nett');
        $smktPoCount       = Quotation::whereBetween('po_date', [$firstDayOfMonth, $lastDayOfMonth])->whereNotNull('id_support')->where('status', '100')->where('level', '1')->where('is_primary', '1')->count()
            + UnitQuotation::where('status', 'po_received')->where('is_latest', 1)->whereBetween('po_received', [$firstDayOfMonth, $lastDayOfMonth])->whereNotNull('id_support')->count();
        $smktPoTotal       = Quotation::whereBetween('po_date', [$firstDayOfMonth, $lastDayOfMonth])->whereNotNull('id_support')->where('status', '100')->where('level', '1')->where('is_primary', '1')->sum('nett')
            + UnitQuotation::where('status', 'po_received')->where('is_latest', 1)->whereBetween('po_received', [$firstDayOfMonth, $lastDayOfMonth])->whereNotNull('id_support')->sum(DB::raw('total - tax_amount'));
        $smktLossCount     = Quotation::whereBetween('estimated_date', [$firstDayOfMonth, $lastDayOfMonth])->whereNotNull('id_support')->where('status', '0')->where('level', '1')->where('is_primary', '1')->count();
        $smktLossTotal     = Quotation::whereBetween('estimated_date', [$firstDayOfMonth, $lastDayOfMonth])->whereNotNull('id_support')->where('status', '0')->where('level', '1')->where('is_primary', '1')->sum('nett');

        $smktProspectByStatus = Prospect::whereBetween('date', [$firstDayOfMonth, $lastDayOfMonth])
            ->whereNotNull('id_support')
            ->selectRaw('
                SUM(CASE WHEN provide IS NULL THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN provide = "1"  THEN 1 ELSE 0 END) as provided,
                SUM(CASE WHEN provide = "0"  THEN 1 ELSE 0 END) as no_provide
            ')
            ->first();

        $smktPerPerson = Prospect::join('users', 'users.id', '=', 'prospect.id_support')
            ->whereBetween('prospect.date', [$firstDayOfMonth, $lastDayOfMonth])
            ->whereNotNull('prospect.id_support')
            ->selectRaw('
                users.id, users.name, users.image,
                COUNT(*) as total,
                SUM(CASE WHEN prospect.provide = "1"  THEN 1 ELSE 0 END) as provided,
                SUM(CASE WHEN prospect.provide = "0"  THEN 1 ELSE 0 END) as no_provide,
                SUM(CASE WHEN prospect.provide IS NULL THEN 1 ELSE 0 END) as pending
            ')
            ->groupBy('users.id', 'users.name', 'users.image')
            ->orderByDesc('total')
            ->get();

        $smktProspectBySource = Prospect::join('pic', 'pic.id', '=', 'prospect.id_pic')
            ->join('client', 'client.id', '=', 'pic.id_client')
            ->whereBetween('prospect.date', [$firstDayOfMonth, $lastDayOfMonth])
            ->whereNotNull('prospect.id_support')
            ->selectRaw('COALESCE(client.source, "Other") as source, COUNT(*) as total')
            ->groupBy('source')->orderByDesc('total')->get();

        $smktProspectByDomain = Prospect::join('pic', 'pic.id', '=', 'prospect.id_pic')
            ->join('client', 'client.id', '=', 'pic.id_client')
            ->whereBetween('prospect.date', [$firstDayOfMonth, $lastDayOfMonth])
            ->whereNotNull('prospect.id_support')
            ->where('client.source', 'Website')
            ->whereNotNull('client.source_detail')
            ->where('client.source_detail', '!=', '')
            ->selectRaw('client.source_detail as domain, COUNT(*) as total')
            ->groupBy('domain')->orderByDesc('total')->get();

        $smktProspectByCategory = Prospect::whereBetween('date', [$firstDayOfMonth, $lastDayOfMonth])
            ->whereNotNull('id_support')
            ->selectRaw('COALESCE(category, "Uncategorized") as category, COUNT(*) as total')
            ->groupBy('category')->orderByDesc('total')->get();

        // dd($dataSupport);

        // Batch semua metrik per sales user (sebelumnya ~9 query di dalam foreach = N+1)
        $salesIds = $sales->pluck('id');

        $poTotalBySales = Quotation::whereBetween('po_date', [$firstDayOfMonth, $lastDayOfMonth])
            ->whereIn('id_sales', $salesIds)->where('status', '100')->where('level', '1')->where('is_primary', '1')
            ->groupBy('id_sales')->selectRaw('id_sales, SUM(nett) as total')->pluck('total', 'id_sales');

        $poTotalUnitBySales = UnitQuotation::where('status', 'po_received')->where('is_latest', 1)
            ->whereBetween('po_received', [$firstDayOfMonth, $lastDayOfMonth])->whereIn('id_sales', $salesIds)
            ->groupBy('id_sales')->selectRaw('id_sales, SUM(total - tax_amount) as total')->pluck('total', 'id_sales');

        $bulananAll = DB::table('quotation')
            ->select('id_sales', DB::raw('MONTH(po_date) as bulan'), DB::raw('SUM(nett) as total'))
            ->whereIn('id_sales', $salesIds)->where('status', 100)->where('level', '1')->where('is_primary', '1')
            ->whereBetween('po_date', [$firstDayOfMonth, $lastDayOfMonth])
            ->groupBy('id_sales', DB::raw('MONTH(po_date)'))
            ->get()->groupBy('id_sales');

        $bulananUnitAll = DB::table('unit_quotation')
            ->select('id_sales', DB::raw('MONTH(po_received) as bulan'), DB::raw('SUM(total - tax_amount) as total'))
            ->whereIn('id_sales', $salesIds)->where('status', 'po_received')->where('is_latest', 1)
            ->whereBetween('po_received', [$firstDayOfMonth, $lastDayOfMonth])
            ->groupBy('id_sales', DB::raw('MONTH(po_received)'))
            ->get()->groupBy('id_sales');

        $prospectCountBySales = Prospect::whereBetween('date', [$firstDayOfMonth, $lastDayOfMonth])
            ->whereIn('id_sales', $salesIds)->whereNotNull('id_support')
            ->groupBy('id_sales')->selectRaw('id_sales, COUNT(*) as total')->pluck('total', 'id_sales');

        $quoteCountBySales = Quotation::whereBetween('estimated_date', [$firstDayOfMonth, $lastDayOfMonth])
            ->whereIn('id_sales', $salesIds)->whereNotNull('id_support')->where('level', '1')->where('is_primary', '1')
            ->groupBy('id_sales')->selectRaw('id_sales, COUNT(*) as total')->pluck('total', 'id_sales');

        $poCountBySales = Quotation::whereBetween('po_date', [$firstDayOfMonth, $lastDayOfMonth])
            ->whereIn('id_sales', $salesIds)->whereNotNull('id_support')->where('status', '100')->where('level', '1')->where('is_primary', '1')
            ->groupBy('id_sales')->selectRaw('id_sales, COUNT(*) as total')->pluck('total', 'id_sales');

        $poCountUnitBySales = UnitQuotation::where('status', 'po_received')->where('is_latest', 1)
            ->whereBetween('po_received', [$firstDayOfMonth, $lastDayOfMonth])->whereIn('id_sales', $salesIds)->whereNotNull('id_support')
            ->groupBy('id_sales')->selectRaw('id_sales, COUNT(*) as total')->pluck('total', 'id_sales');

        $targetBySales = Target::whereIn('id_sales', $salesIds)
            ->groupBy('id_sales')->selectRaw('id_sales, SUM(total) as total')->pluck('total', 'id_sales');

        $data = [];

        foreach ($sales as $user) {
            $poTotalSales = ((float) $poTotalBySales->get($user->id, 0)) + ((float) $poTotalUnitBySales->get($user->id, 0));
            $bulanan = ($bulananAll->get($user->id) ?? collect())->pluck('total', 'bulan')->toArray();
            $bulananUnit = ($bulananUnitAll->get($user->id) ?? collect())->pluck('total', 'bulan')->toArray();
            $jumlah = [];
            for ($i = $startMonth; $i <= $endMonth; $i++) {
                $jumlah[] = [
                    'bulan' => $i,
                    'total' => (int) ($bulanan[$i] ?? 0) + (int) ($bulananUnit[$i] ?? 0)
                ];
            }

            $smktProspectForSales = (int) $prospectCountBySales->get($user->id, 0);
            $smktQuoteForSales    = (int) $quoteCountBySales->get($user->id, 0);
            $smktPoForSales       = ((int) $poCountBySales->get($user->id, 0)) + ((int) $poCountUnitBySales->get($user->id, 0));

            $data[] = [
                'id'          => $user->id,
                'image'       => $user->image,
                'name'        => $user->name,
                'target'      => (float) $targetBySales->get($user->id, 0),
                'total'       => $poTotalSales,
                'jumlah'      => $jumlah,
                'mktProspect' => $smktProspectForSales,
                'mktQuote'    => $smktQuoteForSales,
                'mktPo'       => $smktPoForSales,
            ];
        }
        // dd($data);
        return view('pages.admin.report', compact(
            'poCount',
            'lossCount',
            'quoteCount',
            'quoteOnCount',
            'poTotal',
            'lossTotal',
            'quoteTotal',
            'quoteOnTotal',
            'sales',
            'data',
            'totalTarget',
            'report',
            'semester',
            'support',
            'dataSupport',
            'poTotalSupport',
            'quoteTotalSupport',
            'quoteCountSupport',
            'smktProspectCount', 'smktQuoteCount', 'smktQuoteTotal',
            'smktPoCount', 'smktPoTotal', 'smktLossCount', 'smktLossTotal',
            'smktProspectByStatus', 'smktPerPerson',
            'smktProspectBySource', 'smktProspectByCategory', 'smktProspectByDomain'
        ));
    }

    public function reportsByYear($year)
    {
        $yearList = SalesReports::select('year')->distinct()->orderBy('year', 'desc')->pluck('year');

        $firstDay = "{$year}-01-01";
        $lastDay  = "{$year}-12-31";

        $poCount      = Quotation::whereBetween('po_date', [$firstDay, $lastDay])->where('status', '100')->where('level', '1')->where('is_primary', '1')->count()
            + UnitQuotation::where('status', 'po_received')->where('is_latest', 1)->whereBetween('po_received', [$firstDay, $lastDay])->count();
        $lossCount    = Quotation::whereBetween('estimated_date', [$firstDay, $lastDay])->where('status', '0')->where('level', '1')->where('is_primary', '1')->count();
        $quoteCount   = Quotation::whereBetween('estimated_date', [$firstDay, $lastDay])->whereIn('status', ['20', '40', '60', '80', '90'])->where('level', '1')->where('is_primary', '1')->count();
        $quoteOnCount = Quotation::whereBetween('estimated_date', [$firstDay, $lastDay])->where('level', '1')->where('is_primary', '1')->count();
        $poTotal      = Quotation::whereBetween('po_date', [$firstDay, $lastDay])->where('status', '100')->where('level', '1')->where('is_primary', '1')->sum('nett')
            + UnitQuotation::where('status', 'po_received')->where('is_latest', 1)->whereBetween('po_received', [$firstDay, $lastDay])->sum(DB::raw('total - tax_amount'));
        $lossTotal    = Quotation::whereBetween('estimated_date', [$firstDay, $lastDay])->where('status', '0')->where('level', '1')->where('is_primary', '1')->sum('nett');
        $quoteTotal   = Quotation::whereBetween('estimated_date', [$firstDay, $lastDay])->whereIn('status', ['20', '40', '60', '80', '90'])->where('level', '1')->where('is_primary', '1')->sum('nett');
        $quoteOnTotal = Quotation::whereBetween('estimated_date', [$firstDay, $lastDay])->where('level', '1')->where('is_primary', '1')->sum('nett');

        $sales    = User::where('role', 'Sales')->where('active', '1')->get();
        $support  = User::find('22');
        $reportS1 = SalesReports::where('year', $year)->where('semester', 1)->first();
        $reportS2 = SalesReports::where('year', $year)->where('semester', 2)->first();
        $totalTarget = intval((($reportS1->target ?? 0) + ($reportS2->target ?? 0)) / 12);

        $dataSupportRaw = DB::table('quotation')
            ->selectRaw('MONTH(po_date) as bulan, SUM(nett) as total')
            ->whereNotNull('id_support')
            ->where('status', 100)
            ->where('level', '1')
            ->where('is_primary', '1')
            ->whereYear('po_date', $year)
            ->groupBy(DB::raw('MONTH(po_date)'))
            ->pluck('total', 'bulan')
            ->toArray();

        $dataSupport = [];
        for ($i = 1; $i <= 12; $i++) {
            $dataSupport[$i] = (int) ($dataSupportRaw[$i] ?? 0);
        }
        $poTotalSupport    = array_sum($dataSupport);
        $quoteTotalSupport = Quotation::whereYear('estimated_date', $year)->whereNotNull('id_support')->where('level', '1')->where('is_primary', '1')->sum('nett');
        $quoteCountSupport = Quotation::whereYear('estimated_date', $year)->whereNotNull('id_support')->where('level', '1')->where('is_primary', '1')->count();

        $data = [];
        foreach ($sales as $user) {
            $bulanan = DB::table('quotation')
                ->selectRaw('MONTH(po_date) as bulan, SUM(nett) as total')
                ->where('id_sales', $user->id)
                ->where('status', 100)
                ->where('level', '1')
                ->where('is_primary', '1')
                ->whereYear('po_date', $year)
                ->groupBy(DB::raw('MONTH(po_date)'))
                ->pluck('total', 'bulan')
                ->toArray();
            $bulananUnit = DB::table('unit_quotation')
                ->selectRaw('MONTH(po_received) as bulan, SUM(total - tax_amount) as total')
                ->where('id_sales', $user->id)
                ->where('status', 'po_received')
                ->where('is_latest', 1)
                ->whereYear('po_received', $year)
                ->groupBy(DB::raw('MONTH(po_received)'))
                ->pluck('total', 'bulan')
                ->toArray();

            $jumlah = [];
            for ($i = 1; $i <= 12; $i++) {
                $jumlah[$i] = (int) ($bulanan[$i] ?? 0) + (int) ($bulananUnit[$i] ?? 0);
            }

            $data[] = [
                'id'     => $user->id,
                'image'  => $user->image,
                'name'   => $user->name,
                'target' => Target::where('id_sales', $user->id)->sum('total'),
                'total'  => array_sum($jumlah),
                'jumlah' => $jumlah,
            ];
        }

        return view('pages.admin.report-year', compact(
            'year', 'yearList',
            'poCount', 'lossCount', 'quoteCount', 'quoteOnCount',
            'poTotal', 'lossTotal', 'quoteTotal', 'quoteOnTotal',
            'totalTarget', 'data', 'support', 'dataSupport', 'poTotalSupport',
            'quoteTotalSupport', 'quoteCountSupport',
            'reportS1', 'reportS2'
        ));
    }

    public function reportMonthly($year = null, $month = null)
    {
        $now   = Carbon::now();
        $year  = (int) ($year  ?? $now->year);
        $month = (int) ($month ?? $now->month);

        $firstDay = Carbon::create($year, $month, 1)->startOfMonth()->toDateString();
        $lastDay  = Carbon::create($year, $month, 1)->endOfMonth()->toDateString();

        $sales = User::where('role', 'Sales')->where('active', '1')->get();

        $poCount      = Quotation::whereBetween('po_date', [$firstDay, $lastDay])->where('status', '100')->where('level', '1')->where('is_primary', '1')->count()
            + UnitQuotation::where('status', 'po_received')->where('is_latest', 1)->whereBetween('po_received', [$firstDay, $lastDay])->count();
        $poTotal      = Quotation::whereBetween('po_date', [$firstDay, $lastDay])->where('status', '100')->where('level', '1')->where('is_primary', '1')->sum('nett')
            + UnitQuotation::where('status', 'po_received')->where('is_latest', 1)->whereBetween('po_received', [$firstDay, $lastDay])->sum(DB::raw('total - tax_amount'));
        $quoteCount   = Quotation::whereBetween('estimated_date', [$firstDay, $lastDay])->whereIn('status', ['20', '40', '60', '80'])->where('level', '1')->where('is_primary', '1')->count();
        $quoteTotal   = Quotation::whereBetween('estimated_date', [$firstDay, $lastDay])->whereIn('status', ['20', '40', '60', '80'])->where('level', '1')->where('is_primary', '1')->sum('nett');
        $lossCount    = Quotation::whereBetween('estimated_date', [$firstDay, $lastDay])->where('status', '0')->where('level', '1')->where('is_primary', '1')->count();
        $lossTotal    = Quotation::whereBetween('estimated_date', [$firstDay, $lastDay])->where('status', '0')->where('level', '1')->where('is_primary', '1')->sum('nett');
        $quoteOnCount = Quotation::whereBetween('estimated_date', [$firstDay, $lastDay])->where('level', '1')->where('is_primary', '1')->count();
        $totalTarget  = Target::whereIn('id_sales', $sales->pluck('id'))->sum('total');

        $data = [];
        foreach ($sales as $user) {
            $leads   = Client::whereMonth('created_at', $month)->whereYear('created_at', $year)->where('id_sales', $user->id)->count();
            $dc      = Activities::join('client as c', 'activities.id_client', '=', 'c.id')
                        ->whereBetween('activities.date', [$firstDay, $lastDay])
                        ->where('c.id_sales', $user->id)
                        ->whereIn('activities.name', ['Daily Call', 'Follow Up'])
                        ->where('activities.status', 'Responded')
                        ->distinct('activities.id_client')
                        ->count('activities.id_client');
            $crm     = Activities::join('client as c', 'activities.id_client', '=', 'c.id')
                        ->whereBetween('activities.date', [$firstDay, $lastDay])
                        ->where('c.id_sales', $user->id)
                        ->where('activities.name', 'CRM')
                        ->where('activities.status', 'Responded')
                        ->distinct('activities.id_client')
                        ->count('activities.id_client');
            $userQuoteCount   = Quotation::whereBetween('estimated_date', [$firstDay, $lastDay])->where('id_sales', $user->id)->where('level', '1')->where('is_primary', '1')->count();
            $userQuoteTotal   = Quotation::whereBetween('estimated_date', [$firstDay, $lastDay])->where('id_sales', $user->id)->where('level', '1')->where('is_primary', '1')->sum('nett');
            $userProspectCount = Quotation::where('status', '80')->whereBetween('estimated_date', [$firstDay, $lastDay])->where('id_sales', $user->id)->where('level', '1')->where('is_primary', '1')->count();
            $userPoCount      = Quotation::whereBetween('po_date', [$firstDay, $lastDay])->where('id_sales', $user->id)->where('status', '100')->where('level', '1')->where('is_primary', '1')->count()
                + UnitQuotation::where('status', 'po_received')->where('is_latest', 1)->whereBetween('po_received', [$firstDay, $lastDay])->where('id_sales', $user->id)->count();
            $userPoTotal      = Quotation::whereBetween('po_date', [$firstDay, $lastDay])->where('id_sales', $user->id)->where('status', '100')->where('level', '1')->where('is_primary', '1')->sum('nett')
                + UnitQuotation::where('status', 'po_received')->where('is_latest', 1)->whereBetween('po_received', [$firstDay, $lastDay])->where('id_sales', $user->id)->sum(DB::raw('total - tax_amount'));
            $userLossCount    = Quotation::whereBetween('estimated_date', [$firstDay, $lastDay])->where('id_sales', $user->id)->where('status', '0')->where('level', '1')->where('is_primary', '1')->count();
            $target = Target::where('id_sales', $user->id)->sum('total');

            $mktProspectForSales = Prospect::whereBetween('date', [$firstDay, $lastDay])->where('id_sales', $user->id)->whereNotNull('id_support')->count();
            $mktQuoteForSales    = Quotation::whereBetween('estimated_date', [$firstDay, $lastDay])->where('id_sales', $user->id)->whereNotNull('id_support')->where('level', '1')->where('is_primary', '1')->count();
            $mktPoForSales       = Quotation::whereBetween('po_date', [$firstDay, $lastDay])->where('id_sales', $user->id)->whereNotNull('id_support')->where('status', '100')->where('level', '1')->where('is_primary', '1')->count()
                + UnitQuotation::where('status', 'po_received')->where('is_latest', 1)->whereBetween('po_received', [$firstDay, $lastDay])->where('id_sales', $user->id)->whereNotNull('id_support')->count();

            $data[] = [
                'id'                  => $user->id,
                'name'                => $user->name,
                'image'               => $user->image,
                'leads'               => $leads,
                'dc'                  => $dc,
                'crm'                 => $crm,
                'quoteCount'          => $userQuoteCount,
                'quoteTotal'          => $userQuoteTotal,
                'prospectCount'       => $userProspectCount,
                'poCount'             => $userPoCount,
                'poTotal'             => $userPoTotal,
                'lossCount'           => $userLossCount,
                'target'              => $target,
                'mktProspect'         => $mktProspectForSales,
                'mktQuote'            => $mktQuoteForSales,
                'mktPo'               => $mktPoForSales,
            ];
        }

        usort($data, fn($a, $b) => $b['poTotal'] <=> $a['poTotal']);

        $yearList = SalesReports::select('year')->distinct()->orderBy('year', 'desc')->pluck('year');

        // Marketing funnel
        $mktProspectCount  = Prospect::whereMonth('date', $month)->whereYear('date', $year)->whereNotNull('id_support')->count();
        $mktQuoteCount     = Quotation::whereMonth('estimated_date', $month)->whereYear('estimated_date', $year)->whereNotNull('id_support')->where('level', '1')->where('is_primary', '1')->count();
        $mktQuoteTotal     = Quotation::whereMonth('estimated_date', $month)->whereYear('estimated_date', $year)->whereNotNull('id_support')->where('level', '1')->where('is_primary', '1')->sum('nett');
        $mktPoCount        = Quotation::whereMonth('po_date', $month)->whereYear('po_date', $year)->whereNotNull('id_support')->where('status', '100')->where('level', '1')->where('is_primary', '1')->count()
            + UnitQuotation::where('status', 'po_received')->where('is_latest', 1)->whereMonth('po_received', $month)->whereYear('po_received', $year)->whereNotNull('id_support')->count();
        $mktPoTotal        = Quotation::whereMonth('po_date', $month)->whereYear('po_date', $year)->whereNotNull('id_support')->where('status', '100')->where('level', '1')->where('is_primary', '1')->sum('nett')
            + UnitQuotation::where('status', 'po_received')->where('is_latest', 1)->whereMonth('po_received', $month)->whereYear('po_received', $year)->whereNotNull('id_support')->sum(DB::raw('total - tax_amount'));

        $mktProspectBySource = Prospect::join('pic', 'pic.id', '=', 'prospect.id_pic')
            ->join('client', 'client.id', '=', 'pic.id_client')
            ->whereMonth('prospect.date', $month)
            ->whereYear('prospect.date', $year)
            ->whereNotNull('prospect.id_support')
            ->selectRaw('COALESCE(client.source, "Other") as source, COUNT(*) as total')
            ->groupBy('source')
            ->orderByDesc('total')
            ->get();

        $mktProspectByCategory = Prospect::whereMonth('date', $month)
            ->whereYear('date', $year)
            ->whereNotNull('id_support')
            ->selectRaw('COALESCE(category, "Uncategorized") as category, COUNT(*) as total')
            ->groupBy('category')
            ->orderByDesc('total')
            ->get();

        $mktProspectByArea = Prospect::join('pic', 'pic.id', '=', 'prospect.id_pic')
            ->join('client', 'client.id', '=', 'pic.id_client')
            ->whereMonth('prospect.date', $month)
            ->whereYear('prospect.date', $year)
            ->whereNotNull('prospect.id_support')
            ->selectRaw('COALESCE(NULLIF(client.area, ""), "Unknown") as area, COUNT(*) as total')
            ->groupBy('area')
            ->orderByDesc('total')
            ->get();

        $mktProspectByDomain = Prospect::join('pic', 'pic.id', '=', 'prospect.id_pic')
            ->join('client', 'client.id', '=', 'pic.id_client')
            ->whereMonth('prospect.date', $month)
            ->whereYear('prospect.date', $year)
            ->whereNotNull('prospect.id_support')
            ->where('client.source', 'Website')
            ->whereNotNull('client.source_detail')
            ->where('client.source_detail', '!=', '')
            ->selectRaw('client.source_detail as domain, COUNT(*) as total')
            ->groupBy('domain')
            ->orderByDesc('total')
            ->get();

        // Status prospect (pending / provided / no provide)
        $mktProspectByStatus = Prospect::whereMonth('date', $month)
            ->whereYear('date', $year)
            ->whereNotNull('id_support')
            ->selectRaw('
                SUM(CASE WHEN provide IS NULL THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN provide = "1"  THEN 1 ELSE 0 END) as provided,
                SUM(CASE WHEN provide = "0"  THEN 1 ELSE 0 END) as no_provide
            ')
            ->first();

        // Per marketing person
        $mktPerPerson = Prospect::join('users', 'users.id', '=', 'prospect.id_support')
            ->whereMonth('prospect.date', $month)
            ->whereYear('prospect.date', $year)
            ->whereNotNull('prospect.id_support')
            ->selectRaw('
                users.id, users.name, users.image,
                COUNT(*) as total,
                SUM(CASE WHEN prospect.provide = "1"  THEN 1 ELSE 0 END) as provided,
                SUM(CASE WHEN prospect.provide = "0"  THEN 1 ELSE 0 END) as no_provide,
                SUM(CASE WHEN prospect.provide IS NULL THEN 1 ELSE 0 END) as pending
            ')
            ->groupBy('users.id', 'users.name', 'users.image')
            ->orderByDesc('total')
            ->get();

        // Loss dari marketing leads
        $mktLossCount = Quotation::whereMonth('estimated_date', $month)->whereYear('estimated_date', $year)->whereNotNull('id_support')->where('status', '0')->where('level', '1')->where('is_primary', '1')->count();
        $mktLossTotal = Quotation::whereMonth('estimated_date', $month)->whereYear('estimated_date', $year)->whereNotNull('id_support')->where('status', '0')->where('level', '1')->where('is_primary', '1')->sum('nett');

        return view('pages.admin.report-monthly', compact(
            'year', 'month', 'yearList',
            'data', 'poCount', 'poTotal',
            'quoteCount', 'quoteTotal',
            'lossCount', 'lossTotal',
            'quoteOnCount', 'totalTarget',
            'mktProspectCount', 'mktQuoteCount', 'mktQuoteTotal',
            'mktPoCount', 'mktPoTotal', 'mktProspectBySource', 'mktProspectByCategory', 'mktProspectByArea',
            'mktProspectByDomain',
            'mktProspectByStatus', 'mktPerPerson', 'mktLossCount', 'mktLossTotal'
        ));
    }

    public function reportFinance($year = null, $month = null)
    {
        $now   = Carbon::now();
        $year  = (int) ($year  ?? $now->year);
        $month = (int) ($month ?? $now->month);

        $firstDay = Carbon::create($year, $month, 1)->startOfMonth()->toDateString();
        $lastDay  = Carbon::create($year, $month, 1)->endOfMonth()->toDateString();

        $sales = User::where('role', 'Sales')->where('active', '1')->get();

        $poCount      = Quotation::whereBetween('po_date', [$firstDay, $lastDay])->where('status', '100')->where('level', '1')->where('is_primary', '1')->count()
            + UnitQuotation::where('status', 'po_received')->where('is_latest', 1)->whereBetween('po_received', [$firstDay, $lastDay])->count();
        $poTotal      = Quotation::whereBetween('po_date', [$firstDay, $lastDay])->where('status', '100')->where('level', '1')->where('is_primary', '1')->sum('nett')
            + UnitQuotation::where('status', 'po_received')->where('is_latest', 1)->whereBetween('po_received', [$firstDay, $lastDay])->sum(DB::raw('total - tax_amount'));
        $quoteCount   = Quotation::whereBetween('estimated_date', [$firstDay, $lastDay])->whereIn('status', ['20', '40', '60', '80'])->where('level', '1')->where('is_primary', '1')->count();
        $quoteTotal   = Quotation::whereBetween('estimated_date', [$firstDay, $lastDay])->whereIn('status', ['20', '40', '60', '80'])->where('level', '1')->where('is_primary', '1')->sum('nett');
        $lossCount    = Quotation::whereBetween('estimated_date', [$firstDay, $lastDay])->where('status', '0')->where('level', '1')->where('is_primary', '1')->count();
        $lossTotal    = Quotation::whereBetween('estimated_date', [$firstDay, $lastDay])->where('status', '0')->where('level', '1')->where('is_primary', '1')->sum('nett');
        $quoteOnCount = Quotation::whereBetween('estimated_date', [$firstDay, $lastDay])->where('level', '1')->where('is_primary', '1')->count();

        // Target dari fitur Sales Target (sales_target_histories): target tahunan per tahun terpilih, dibagi rata 12 bulan
        $totalTarget  = SalesTargetHistory::where('year', $year)->sum('target_annual');
        $monthlyTarget = $totalTarget > 0 ? (int) round($totalTarget / 12) : 0;

        $winRate           = $quoteOnCount > 0 ? round(($poCount / $quoteOnCount) * 100, 1) : 0;
        $lossRate          = $quoteOnCount > 0 ? round(($lossCount / $quoteOnCount) * 100, 1) : 0;
        $targetAchievement = $monthlyTarget > 0 ? round(($poTotal / $monthlyTarget) * 100, 1) : 0;

        // Trend PO Total per bulan (Jan-Des) untuk tahun terpilih, dibandingkan target rata-rata per bulan
        $trendLabels  = [];
        $trendPoTotal = [];
        for ($m = 1; $m <= 12; $m++) {
            $mStart = Carbon::create($year, $m, 1)->startOfMonth()->toDateString();
            $mEnd   = Carbon::create($year, $m, 1)->endOfMonth()->toDateString();
            $trendLabels[]  = Carbon::create($year, $m, 1)->translatedFormat('M');
            $trendPoTotal[] = (int) Quotation::whereBetween('po_date', [$mStart, $mEnd])->where('status', '100')->where('level', '1')->where('is_primary', '1')->sum('nett')
                + (int) UnitQuotation::where('status', 'po_received')->where('is_latest', 1)->whereBetween('po_received', [$mStart, $mEnd])->sum(DB::raw('total - tax_amount'));
        }
        $trendMonthlyTarget = $monthlyTarget;

        $yearList = SalesReports::select('year')->distinct()->orderBy('year', 'desc')->pluck('year');

        return view('pages.finance.reports.index', compact(
            'year', 'month', 'yearList',
            'poCount', 'poTotal',
            'quoteCount', 'quoteTotal',
            'lossCount', 'lossTotal',
            'quoteOnCount', 'totalTarget', 'monthlyTarget',
            'winRate', 'lossRate', 'targetAchievement',
            'trendLabels', 'trendPoTotal', 'trendMonthlyTarget'
        ));
    }

    protected function getMonthlyDataDC($semester, $year)
    {
        if ($semester == 1) {
            $firstDayOfMonth = "{$year}-1-01";
            $firstDayOfLastMonth = "{$year}-6-01";
            $lastDayOfMonth = date('Y-m-t', strtotime($firstDayOfLastMonth));

            $dCallPerMonth = Activities::select(
                DB::raw('MONTH(date) as month'),
                'activities.id_client'
            )
                ->join('client as c', 'activities.id_client', '=', 'c.id')
                ->join('users as u', 'c.id_sales', '=', 'u.id')
                ->whereBetween('date', [$firstDayOfMonth, $lastDayOfMonth])
                ->where('id_sales', Auth::user()->id)
                ->whereIn('activities.name', ['Daily Call', 'Follow Up'])
                ->where('status', 'Responded')
                ->groupBy('month', 'activities.id_client')
                ->get()
                ->groupBy('month')
                ->mapWithKeys(fn($items, $month) => [$month => $items->count()]);

            $fullMonthData = [];
            for ($month = 1; $month <= 6; $month++) {
                $monthKey = "{$month}";
                $carbonMonth = Carbon::parse($firstDayOfMonth);
                $formattedMonth = isset($plusMonth) ? $plusMonth->format('F') : $carbonMonth->format('F');
                $monthDays = date('t', strtotime($monthKey));
                if ($monthDays >= 4) {
                    $fullMonthData[$monthKey] = [
                        'monthKey' => $monthKey,
                        'month' => $formattedMonth,
                        'total' => isset($dCallPerMonth[$monthKey]) ? $dCallPerMonth[$monthKey] : 0,
                    ];
                }
                $plusMonth = isset($plusMonth) ? $plusMonth->addMonth() : $carbonMonth->addMonth();
            }
            // dd($fullMonthData);

            return $fullMonthData;
        } else {
            $firstDayOfMonth = "{$year}-07-01";
            $firstDayOfLastMonth = "{$year}-12-01";
            $lastDayOfMonth = date('Y-m-t', strtotime($firstDayOfLastMonth));

            $dCallPerMonth = Activities::select(
                DB::raw('MONTH(date) as month'),
                'activities.id_client'
            )
                ->join('client as c', 'activities.id_client', '=', 'c.id')
                ->join('users as u', 'c.id_sales', '=', 'u.id')
                ->whereBetween('date', [$firstDayOfMonth, $lastDayOfMonth])
                ->where('id_sales', Auth::user()->id)
                ->whereIn('activities.name', ['Daily Call', 'Follow Up'])
                ->where('status', 'Responded')
                ->groupBy('month', 'activities.id_client')
                ->get()
                ->groupBy('month')
                ->mapWithKeys(fn($items, $month) => [$month => $items->count()]);

            $fullMonthData = [];
            for ($month = 7; $month <= 12; $month++) {
                $monthKey = "{$month}";
                $carbonMonth = Carbon::parse($firstDayOfMonth);
                $formattedMonth = isset($plusMonth) ? $plusMonth->format('F') : $carbonMonth->format('F');
                $monthDays = date('t', strtotime($monthKey));
                if ($monthDays >= 4) {
                    $fullMonthData[$monthKey] = [
                        'monthKey' => $monthKey,
                        'month' => $formattedMonth,
                        'total' => isset($dCallPerMonth[$monthKey]) ? $dCallPerMonth[$monthKey] : 0,
                    ];
                }
                $plusMonth = isset($plusMonth) ? $plusMonth->addMonth() : $carbonMonth->addMonth();
            }
            // dd($fullMonthData);

            return $fullMonthData;
        }
    }
    protected function cardMonthlyDC($semester, $year)
    {
        if ($semester == 1) {
            $firstDayOfMonth = "{$year}-1-01";
            $firstDayOfLastMonth = "{$year}-06-01";
            $lastDayOfMonth = date('Y-m-t', strtotime($firstDayOfLastMonth));

            $dCallPerMonth = Activities::select(
                DB::raw('MONTH(date) as month'),
                'activities.id_client'
            )
                ->join('client as c', 'activities.id_client', '=', 'c.id')
                ->join('users as u', 'c.id_sales', '=', 'u.id')
                ->whereBetween('date', [$firstDayOfMonth, $lastDayOfMonth])
                ->where('id_sales', Auth::user()->id)
                ->whereIn('activities.name', ['Daily Call', 'Follow Up'])
                ->where('status', 'Responded')
                ->groupBy('month', 'activities.id_client')
                ->get()
                ->groupBy('month')
                ->mapWithKeys(fn($items, $month) => [$month => $items->count()]);

            $fullMonthData = [];
            for ($month = 1; $month <= 6; $month++) {
                $monthKey = "{$month}";
                $carbonMonth = Carbon::parse($firstDayOfMonth);
                $formattedMonth = isset($plusMonth) ? $plusMonth->format('F') : $carbonMonth->format('F');
                $monthDays = date('t', strtotime($monthKey));
                if ($monthDays >= 4) {
                    // Cek apakah data untuk bulan tersebut ada dalam $dCallPerMonth
                    // Jika tidak ada, maka jumlahnya 0
                    $total = isset($dCallPerMonth[$monthKey]) ? $dCallPerMonth[$monthKey] : 0;
                    // Tambahkan total ke dalam array $fullMonthData
                    $fullMonthData[] = $total;
                }
                $plusMonth = isset($plusMonth) ? $plusMonth->addMonth() : $carbonMonth->addMonth();
            }

            return response()->json($fullMonthData);
        } else {
            $firstDayOfMonth = "{$year}-07-01";
            $firstDayOfLastMonth = "{$year}-12-01";
            $lastDayOfMonth = date('Y-m-t', strtotime($firstDayOfLastMonth));

            $dCallPerMonth = Activities::select(
                DB::raw('MONTH(date) as month'),
                'activities.id_client'
            )
                ->join('client as c', 'activities.id_client', '=', 'c.id')
                ->join('users as u', 'c.id_sales', '=', 'u.id')
                ->whereBetween('date', [$firstDayOfMonth, $lastDayOfMonth])
                ->where('id_sales', Auth::user()->id)
                ->whereIn('activities.name', ['Daily Call', 'Follow Up'])
                ->where('status', 'Responded')
                ->groupBy('month', 'activities.id_client')
                ->get()
                ->groupBy('month')
                ->mapWithKeys(fn($items, $month) => [$month => $items->count()]);

            $fullMonthData = [];
            for ($month = 7; $month <= 12; $month++) {
                $monthKey = "{$month}";
                $carbonMonth = Carbon::parse($firstDayOfMonth);
                $formattedMonth = isset($plusMonth) ? $plusMonth->format('F') : $carbonMonth->format('F');
                $monthDays = date('t', strtotime($monthKey));
                if ($monthDays >= 4) {
                    $fullMonthData[$monthKey] = [
                        'month' => $formattedMonth,
                        'total' => isset($dCallPerMonth[$monthKey]) ? $dCallPerMonth[$monthKey] : 0,
                    ];
                }
                $plusMonth = isset($plusMonth) ? $plusMonth->addMonth() : $carbonMonth->addMonth();
            }
            // dd($fullMonthData);

            return $fullMonthData;
        }
    }
    protected function getMonthlyDataCRM($semester, $year)
    {
        if ($semester == 1) {
            $firstDayOfMonth = "{$year}-01-01";
            $firstDayOfLastMonth = "{$year}-06-01";
            $lastDayOfMonth = date('Y-m-t', strtotime($firstDayOfLastMonth));

            $dCallPerMonth = Activities::select(
                DB::raw('MONTH(date) as month'),
                'activities.id_client'
            )
                ->join('client as c', 'activities.id_client', '=', 'c.id')
                ->join('users as u', 'c.id_sales', '=', 'u.id')
                ->whereBetween('date', [$firstDayOfMonth, $lastDayOfMonth])
                ->where('id_sales', Auth::user()->id)
                ->where('activities.name', 'CRM')
                ->where('status', 'Responded')
                ->groupBy(DB::raw('MONTH(date)'), 'activities.id_client')
                ->get()
                ->groupBy('month')
                ->mapWithKeys(fn($items, $month) => [$month => $items->count()]);

            $fullMonthData = [];
            for ($month = 1; $month <= 6; $month++) {
                $monthKey = "{$month}";
                $carbonMonth = Carbon::parse($firstDayOfMonth);
                $formattedMonth = isset($plusMonth) ? $plusMonth->format('F') : $carbonMonth->format('F');
                $monthDays = date('t', strtotime($monthKey));
                if ($monthDays >= 4) {
                    $fullMonthData[$monthKey] = [
                        'month' => $formattedMonth,
                        'total' => isset($dCallPerMonth[$monthKey]) ? $dCallPerMonth[$monthKey] : 0,
                    ];
                }
                $plusMonth = isset($plusMonth) ? $plusMonth->addMonth() : $carbonMonth->addMonth();
            }
            // dd($fullMonthData);

            return $fullMonthData;
        } else {
            $firstDayOfMonth = "{$year}-07-01";
            $firstDayOfLastMonth = "{$year}-12-01";
            $lastDayOfMonth = date('Y-m-t', strtotime($firstDayOfLastMonth));

            $dCallPerMonth = Activities::select(
                DB::raw('MONTH(date) as month'),
                'activities.id_client'
            )
                ->join('client as c', 'activities.id_client', '=', 'c.id')
                ->join('users as u', 'c.id_sales', '=', 'u.id')
                ->whereBetween('date', [$firstDayOfMonth, $lastDayOfMonth])
                ->where('id_sales', Auth::user()->id)
                ->where('activities.name', 'CRM')
                ->where('status', 'Responded')
                ->groupBy(DB::raw('MONTH(date)'), 'activities.id_client')
                ->get()
                ->groupBy('month')
                ->mapWithKeys(fn($items, $month) => [$month => $items->count()]);

            $fullMonthData = [];
            for ($month = 7; $month <= 12; $month++) {
                $monthKey = "{$month}";
                $carbonMonth = Carbon::parse($firstDayOfMonth);
                $formattedMonth = isset($plusMonth) ? $plusMonth->format('F') : $carbonMonth->format('F');
                $monthDays = date('t', strtotime($monthKey));
                if ($monthDays >= 4) {
                    $fullMonthData[$monthKey] = [
                        'month' => $formattedMonth,
                        'total' => isset($dCallPerMonth[$monthKey]) ? $dCallPerMonth[$monthKey] : 0,
                    ];
                }
                $plusMonth = isset($plusMonth) ? $plusMonth->addMonth() : $carbonMonth->addMonth();
            }
            // dd($fullMonthData);

            return $fullMonthData;
        }
    }
    protected function getMonthlyDataVisit($semester, $year)
    {
        if ($semester == 1) {
            $firstDayOfMonth = "{$year}-01-01";
            $firstDayOfLastMonth = "{$year}-06-01";
            $lastDayOfMonth = date('Y-m-t', strtotime($firstDayOfLastMonth));

            $dCallPerMonth = Activities::select(DB::raw('CONCAT(YEAR(date), "-", MONTH(date)) as date'), DB::raw('month(date) as month'), DB::raw('COUNT(*) as total'))
                ->join('client as c', 'activities.id_client', '=', 'c.id')
                ->join('users as u', 'c.id_sales', '=', 'u.id')
                ->whereBetween('date', [$firstDayOfMonth, $lastDayOfMonth])
                ->where('id_sales', Auth::user()->id)
                ->where('activities.name', 'Visit')
                ->where('status', 'Responded')
                ->groupBy('month')
                ->orderBy('month')
                ->pluck('total', 'month');

            $fullMonthData = [];
            for ($month = 1; $month <= 6; $month++) {
                $monthKey = "{$month}";
                $carbonMonth = Carbon::parse($firstDayOfMonth);
                $formattedMonth = isset($plusMonth) ? $plusMonth->format('F') : $carbonMonth->format('F');
                $monthDays = date('t', strtotime($monthKey));
                if ($monthDays >= 4) {
                    $fullMonthData[$monthKey] = [
                        'month' => $formattedMonth,
                        'total' => isset($dCallPerMonth[$monthKey]) ? $dCallPerMonth[$monthKey] : 0,
                    ];
                }
                $plusMonth = isset($plusMonth) ? $plusMonth->addMonth() : $carbonMonth->addMonth();
            }
            // dd($fullMonthData);

            return $fullMonthData;
        } else {
            $firstDayOfMonth = "{$year}-07-01";
            $firstDayOfLastMonth = "{$year}-12-01";
            $lastDayOfMonth = date('Y-m-t', strtotime($firstDayOfLastMonth));

            $dCallPerMonth = Activities::select(DB::raw('CONCAT(YEAR(date), "-", MONTH(date)) as date'), DB::raw('month(date) as month'), DB::raw('COUNT(*) as total'))
                ->join('client as c', 'activities.id_client', '=', 'c.id')
                ->join('users as u', 'c.id_sales', '=', 'u.id')
                ->whereBetween('date', [$firstDayOfMonth, $lastDayOfMonth])
                ->where('id_sales', Auth::user()->id)
                ->where('activities.name', 'Visit')
                ->where('status', 'Responded')
                ->groupBy('month')
                ->orderBy('month')
                ->pluck('total', 'month');

            $fullMonthData = [];
            for ($month = 7; $month <= 12; $month++) {
                $monthKey = "{$month}";
                $carbonMonth = Carbon::parse($firstDayOfMonth);
                $formattedMonth = isset($plusMonth) ? $plusMonth->format('F') : $carbonMonth->format('F');
                $monthDays = date('t', strtotime($monthKey));
                if ($monthDays >= 4) {
                    $fullMonthData[$monthKey] = [
                        'month' => $formattedMonth,
                        'total' => isset($dCallPerMonth[$monthKey]) ? $dCallPerMonth[$monthKey] : 0,
                    ];
                }
                $plusMonth = isset($plusMonth) ? $plusMonth->addMonth() : $carbonMonth->addMonth();
            }
            // dd($fullMonthData);

            return $fullMonthData;
        }
    }
    protected function getMonthlyDataQuote($semester, $year)
    {
        if ($semester == 1) {
            $firstDayOfMonth = "{$year}-01-01";
            $firstDayOfLastMonth = "{$year}-06-01";
            $lastDayOfMonth = date('Y-m-t', strtotime($firstDayOfLastMonth));

            $dCallPerMonth = Quotation::select(DB::raw('month(created_at) as month'), DB::raw('COUNT(*) as total'))
                ->whereBetween('created_at', [$firstDayOfMonth, $lastDayOfMonth])
                ->where('id_sales', Auth::user()->id)
                ->where('level', '1')->where('is_primary', '1')
                ->groupBy('month')
                ->pluck('total', 'month');

            $unitPerMonth = UnitQuotation::select(DB::raw('month(created_at) as month'), DB::raw('COUNT(*) as total'))
                ->whereBetween('created_at', [$firstDayOfMonth, $lastDayOfMonth])
                ->where('id_sales', Auth::user()->id)
                ->where('is_latest', 1)
                ->groupBy('month')
                ->pluck('total', 'month');

            $fullMonthData = [];
            for ($month = 1; $month <= 6; $month++) {
                $monthKey = "{$month}";
                $carbonMonth = Carbon::parse($firstDayOfMonth);
                $formattedMonth = isset($plusMonth) ? $plusMonth->format('F') : $carbonMonth->format('F');
                $monthDays = date('t', strtotime($monthKey));
                if ($monthDays >= 4) {
                    $qTotal = isset($dCallPerMonth[$monthKey]) ? (int)$dCallPerMonth[$monthKey] : 0;
                    $uTotal = isset($unitPerMonth[$monthKey]) ? (int)$unitPerMonth[$monthKey] : 0;
                    $fullMonthData[$monthKey] = [
                        'month' => $formattedMonth,
                        'total' => $qTotal + $uTotal,
                    ];
                }
                $plusMonth = isset($plusMonth) ? $plusMonth->addMonth() : $carbonMonth->addMonth();
            }

            return $fullMonthData;
        } else {
            $firstDayOfMonth = "{$year}-07-01";
            $firstDayOfLastMonth = "{$year}-12-01";
            $lastDayOfMonth = date('Y-m-t', strtotime($firstDayOfLastMonth));

            $dCallPerMonth = Quotation::select(DB::raw('month(created_at) as month'), DB::raw('COUNT(*) as total'))
                ->whereBetween('created_at', [$firstDayOfMonth, $lastDayOfMonth])
                ->where('id_sales', Auth::user()->id)
                ->where('level', '1')->where('is_primary', '1')
                ->groupBy('month')
                ->pluck('total', 'month');

            $unitPerMonth = UnitQuotation::select(DB::raw('month(created_at) as month'), DB::raw('COUNT(*) as total'))
                ->whereBetween('created_at', [$firstDayOfMonth, $lastDayOfMonth])
                ->where('id_sales', Auth::user()->id)
                ->where('is_latest', 1)
                ->groupBy('month')
                ->pluck('total', 'month');

            $fullMonthData = [];
            for ($month = 7; $month <= 12; $month++) {
                $monthKey = "{$month}";
                $carbonMonth = Carbon::parse($firstDayOfMonth);
                $formattedMonth = isset($plusMonth) ? $plusMonth->format('F') : $carbonMonth->format('F');
                $monthDays = date('t', strtotime($monthKey));
                if ($monthDays >= 4) {
                    $qTotal = isset($dCallPerMonth[$monthKey]) ? (int)$dCallPerMonth[$monthKey] : 0;
                    $uTotal = isset($unitPerMonth[$monthKey]) ? (int)$unitPerMonth[$monthKey] : 0;
                    $fullMonthData[$monthKey] = [
                        'month' => $formattedMonth,
                        'total' => $qTotal + $uTotal,
                    ];
                }
                $plusMonth = isset($plusMonth) ? $plusMonth->addMonth() : $carbonMonth->addMonth();
            }

            return $fullMonthData;
        }
    }

    protected function getMonthlyDataPO($semester, $year)
    {
        if ($semester == 1) {
            $firstDayOfMonth = "{$year}-01-01";
            $firstDayOfLastMonth = "{$year}-06-01";
            $lastDayOfMonth = date('Y-m-t', strtotime($firstDayOfLastMonth));

            $dCallPerMonth = Quotation::select(DB::raw('month(po_date) as month'), DB::raw('COUNT(*) as total'))
                ->whereBetween('po_date', [$firstDayOfMonth, $lastDayOfMonth])
                ->where('id_sales', Auth::user()->id)
                ->where('level', '1')->where('is_primary', '1')
                ->where('status', '100')
                ->groupBy('month')
                ->pluck('total', 'month');

            $unitPoPerMonth = UnitQuotation::select(DB::raw('month(po_received) as month'), DB::raw('COUNT(*) as total'))
                ->whereBetween('po_received', [$firstDayOfMonth, $lastDayOfMonth])
                ->where('id_sales', Auth::user()->id)
                ->where('status', 'po_received')
                ->where('is_latest', 1)
                ->groupBy('month')
                ->pluck('total', 'month');

            $fullMonthData = [];
            for ($month = 1; $month <= 6; $month++) {
                $monthKey = "{$month}";
                $carbonMonth = Carbon::parse($firstDayOfMonth);
                $formattedMonth = isset($plusMonth) ? $plusMonth->format('F') : $carbonMonth->format('F');
                $monthDays = date('t', strtotime($monthKey));
                if ($monthDays >= 4) {
                    $qTotal = isset($dCallPerMonth[$monthKey]) ? (int)$dCallPerMonth[$monthKey] : 0;
                    $uTotal = isset($unitPoPerMonth[$monthKey]) ? (int)$unitPoPerMonth[$monthKey] : 0;
                    $fullMonthData[$monthKey] = [
                        'month' => $formattedMonth,
                        'total' => $qTotal + $uTotal,
                    ];
                }
                $plusMonth = isset($plusMonth) ? $plusMonth->addMonth() : $carbonMonth->addMonth();
            }

            return $fullMonthData;
        } else {
            $firstDayOfMonth = "{$year}-07-01";
            $firstDayOfLastMonth = "{$year}-12-01";
            $lastDayOfMonth = date('Y-m-t', strtotime($firstDayOfLastMonth));

            $dCallPerMonth = Quotation::select(DB::raw('month(po_date) as month'), DB::raw('COUNT(*) as total'))
                ->whereBetween('po_date', [$firstDayOfMonth, $lastDayOfMonth])
                ->where('id_sales', Auth::user()->id)
                ->where('level', '1')->where('is_primary', '1')
                ->where('status', '100')
                ->groupBy('month')
                ->pluck('total', 'month');

            $unitPoPerMonth = UnitQuotation::select(DB::raw('month(po_received) as month'), DB::raw('COUNT(*) as total'))
                ->whereBetween('po_received', [$firstDayOfMonth, $lastDayOfMonth])
                ->where('id_sales', Auth::user()->id)
                ->where('status', 'po_received')
                ->where('is_latest', 1)
                ->groupBy('month')
                ->pluck('total', 'month');

            $fullMonthData = [];
            for ($month = 7; $month <= 12; $month++) {
                $monthKey = "{$month}";
                $carbonMonth = Carbon::parse($firstDayOfMonth);
                $formattedMonth = isset($plusMonth) ? $plusMonth->format('F') : $carbonMonth->format('F');
                $monthDays = date('t', strtotime($monthKey));
                if ($monthDays >= 4) {
                    $qTotal = isset($dCallPerMonth[$monthKey]) ? (int)$dCallPerMonth[$monthKey] : 0;
                    $uTotal = isset($unitPoPerMonth[$monthKey]) ? (int)$unitPoPerMonth[$monthKey] : 0;
                    $fullMonthData[$monthKey] = [
                        'month' => $formattedMonth,
                        'total' => $qTotal + $uTotal,
                    ];
                }
                $plusMonth = isset($plusMonth) ? $plusMonth->addMonth() : $carbonMonth->addMonth();
            }

            return $fullMonthData;
        }
    }

    protected function getMonthlyDataLeads($semester, $year)
    {
        if ($semester == 1) {
            $firstDayOfMonth = "{$year}-01-01";
            $firstDayOfLastMonth = "{$year}-06-01";
            $lastDayOfMonth = date('Y-m-t', strtotime($firstDayOfLastMonth));

            $dCallPerMonth = Client::select(DB::raw('CONCAT(YEAR(created_at), "-", MONTH(created_at)) as date'), DB::raw('month(created_at) as month'), DB::raw('COUNT(*) as total'))
                ->whereBetween('created_at', [$firstDayOfMonth, $lastDayOfMonth])
                ->where('id_sales', Auth::user()->id)
                ->groupBy('month')
                ->orderBy('month')
                ->pluck('total', 'month');

            $fullMonthData = [];
            for ($month = 1; $month <= 6; $month++) {
                $monthKey = "{$month}";
                $carbonMonth = Carbon::parse($firstDayOfMonth);
                $formattedMonth = isset($plusMonth) ? $plusMonth->format('F') : $carbonMonth->format('F');
                $monthDays = date('t', strtotime($monthKey));
                if ($monthDays >= 4) {
                    $fullMonthData[$monthKey] = [
                        'month' => $formattedMonth,
                        'total' => isset($dCallPerMonth[$monthKey]) ? $dCallPerMonth[$monthKey] : 0,
                    ];
                }
                $plusMonth = isset($plusMonth) ? $plusMonth->addMonth() : $carbonMonth->addMonth();
            }

            return $fullMonthData;
        } else {
            $firstDayOfMonth = "{$year}-07-01";
            $firstDayOfLastMonth = "{$year}-12-01";
            $lastDayOfMonth = date('Y-m-t', strtotime($firstDayOfLastMonth));

            $dCallPerMonth = Client::select(DB::raw('CONCAT(YEAR(created_at), "-", MONTH(created_at)) as date'), DB::raw('month(created_at) as month'), DB::raw('COUNT(*) as total'))
                ->whereBetween('created_at', [$firstDayOfMonth, $lastDayOfMonth])
                ->where('id_sales', Auth::user()->id)
                ->groupBy('month')
                ->orderBy('month')
                ->pluck('total', 'month');

            $fullMonthData = [];
            for ($month = 7; $month <= 12; $month++) {
                $monthKey = "{$month}";
                $carbonMonth = Carbon::parse($firstDayOfMonth);
                $formattedMonth = isset($plusMonth) ? $plusMonth->format('F') : $carbonMonth->format('F');
                $monthDays = date('t', strtotime($monthKey));
                if ($monthDays >= 4) {
                    $fullMonthData[$monthKey] = [
                        'month' => $formattedMonth,
                        'total' => isset($dCallPerMonth[$monthKey]) ? $dCallPerMonth[$monthKey] : 0,
                    ];
                }
                $plusMonth = isset($plusMonth) ? $plusMonth->addMonth() : $carbonMonth->addMonth();
            }

            return $fullMonthData;
        }
    }

    protected function getMonthlyDataPOModal($semester, $year)
    {
        if ($semester == 1) {
            $firstDayOfMonth = "{$year}-01-01";
            $firstDayOfLastMonth = "{$year}-06-01";
            $lastDayOfMonth = date('Y-m-t', strtotime($firstDayOfLastMonth));

            $dCallPerMonth = Quotation::select('quotation.*')
                ->selectRaw('MONTH(po_date) as month')
                ->whereBetween('po_date', [$firstDayOfMonth, $lastDayOfMonth])
                ->where('quotation.id_sales', Auth::user()->id)
                ->where('status', '100')
                ->where('level', '1')->where('is_primary', '1')
                ->get();

            $unitQuotes = UnitQuotation::with('client')
                ->select('unit_quotation.*')
                ->selectRaw('MONTH(po_received) as month')
                ->whereBetween('po_received', [$firstDayOfMonth, $lastDayOfMonth])
                ->where('id_sales', Auth::user()->id)
                ->where('status', 'po_received')
                ->where('is_latest', 1)
                ->get();

            $fullMonthData = [];
            for ($month = 1; $month <= 6; $month++) {
                $monthKey = "{$month}";
                $carbonMonth = Carbon::parse($firstDayOfMonth);
                $formattedMonth = isset($plusMonth) ? $plusMonth->format('F') : $carbonMonth->format('F');
                $monthDays = date('t', strtotime($monthKey));
                if ($monthDays >= 4) {
                    $qList = $dCallPerMonth->where('month', $monthKey)->map(function($q) {
                        $arr = $q->toArray();
                        $arr['source'] = 'quotation';
                        return $arr;
                    });
                    $uList = $unitQuotes->where('month', $monthKey)->map(function($u) {
                        return [
                            'id' => $u->id,
                            'no_quote' => $u->no_quote,
                            'company' => $u->client->company ?? 'Client Di Hapus',
                            'title' => $u->title ?? '-',
                            'estimated_date' => $u->po_received ?? $u->date,
                            'nett' => ($u->total - $u->tax_amount),
                            'source' => 'unit_quotation',
                            'month' => $u->month,
                        ];
                    });
                    $mergedList = $qList->concat($uList)->values()->toArray();

                    $fullMonthData[$monthKey] = [
                        'monthKey' => $monthKey,
                        'month' => $formattedMonth,
                        'data' => count($mergedList) > 0 ? $mergedList : null,
                    ];
                }
                $plusMonth = isset($plusMonth) ? $plusMonth->addMonth() : $carbonMonth->addMonth();
            }

            return $fullMonthData;
        } else {
            $firstDayOfMonth = "{$year}-07-01";
            $firstDayOfLastMonth = "{$year}-12-01";
            $lastDayOfMonth = date('Y-m-t', strtotime($firstDayOfLastMonth));

            $dCallPerMonth = Quotation::select('quotation.*')
                ->selectRaw('MONTH(po_date) as month')
                ->whereBetween('po_date', [$firstDayOfMonth, $lastDayOfMonth])
                ->where('quotation.id_sales', Auth::user()->id)
                ->where('status', '100')
                ->where('level', '1')->where('is_primary', '1')
                ->get();

            $unitQuotes = UnitQuotation::with('client')
                ->select('unit_quotation.*')
                ->selectRaw('MONTH(po_received) as month')
                ->whereBetween('po_received', [$firstDayOfMonth, $lastDayOfMonth])
                ->where('id_sales', Auth::user()->id)
                ->where('status', 'po_received')
                ->where('is_latest', 1)
                ->get();

            $fullMonthData = [];
            for ($month = 7; $month <= 12; $month++) {
                $monthKey = "{$month}";
                $carbonMonth = Carbon::parse($firstDayOfMonth);
                $formattedMonth = isset($plusMonth) ? $plusMonth->format('F') : $carbonMonth->format('F');
                $monthDays = date('t', strtotime($monthKey));
                if ($monthDays >= 4) {
                    $qList = $dCallPerMonth->where('month', $monthKey)->map(function($q) {
                        $arr = $q->toArray();
                        $arr['source'] = 'quotation';
                        return $arr;
                    });
                    $uList = $unitQuotes->where('month', $monthKey)->map(function($u) {
                        return [
                            'id' => $u->id,
                            'no_quote' => $u->no_quote,
                            'company' => $u->client->company ?? 'Client Di Hapus',
                            'title' => $u->title ?? '-',
                            'estimated_date' => $u->po_received ?? $u->date,
                            'nett' => ($u->total - $u->tax_amount),
                            'source' => 'unit_quotation',
                            'month' => $u->month,
                        ];
                    });
                    $mergedList = $qList->concat($uList)->values()->toArray();

                    $fullMonthData[$monthKey] = [
                        'monthKey' => $monthKey,
                        'month' => $formattedMonth,
                        'data' => count($mergedList) > 0 ? $mergedList : null,
                    ];
                }
                $plusMonth = isset($plusMonth) ? $plusMonth->addMonth() : $carbonMonth->addMonth();
            }

            return $fullMonthData;
        }
    }

    protected function getMonthlyDataTotalForecast($semester, $year)
    {
        if ($semester == 1) {
            $firstDayOfMonth = "{$year}-01-01";
            $firstDayOfLastMonth = "{$year}-06-01";
            $lastDayOfMonth = date('Y-m-t', strtotime($firstDayOfLastMonth));

            $dCallPerMonth = Quotation::select(DB::raw('month(estimated_date) as month'), DB::raw('SUM(nett) as total'))
                ->whereBetween('estimated_date', [$firstDayOfMonth, $lastDayOfMonth])
                ->where('id_sales', Auth::user()->id)
                ->where('level', '1')->where('is_primary', '1')
                ->whereIn('status', ['20', '30', '40', '60', '80', '100'])
                ->groupBy('month')
                ->pluck('total', 'month');

            $unitForecastPerMonth = UnitQuotation::select(DB::raw('month(created_at) as month'), DB::raw('SUM(total - tax_amount) as total'))
                ->whereBetween('created_at', [$firstDayOfMonth, $lastDayOfMonth])
                ->where('id_sales', Auth::user()->id)
                ->where('is_latest', 1)
                ->whereIn('status', ['draft', 'quotation_sent', 'waiting_po', 'po_received'])
                ->groupBy('month')
                ->pluck('total', 'month');

            $fullMonthData = [];
            for ($month = 1; $month <= 6; $month++) {
                $monthKey = "{$month}";
                $carbonMonth = Carbon::parse($firstDayOfMonth);
                $formattedMonth = isset($plusMonth) ? $plusMonth->format('F') : $carbonMonth->format('F');
                $monthDays = date('t', strtotime($monthKey));
                if ($monthDays >= 4) {
                    $qTotal = isset($dCallPerMonth[$monthKey]) ? (float)$dCallPerMonth[$monthKey] : 0;
                    $uTotal = isset($unitForecastPerMonth[$monthKey]) ? (float)$unitForecastPerMonth[$monthKey] : 0;
                    $fullMonthData[$monthKey] = [
                        'month' => $formattedMonth,
                        'total' => $qTotal + $uTotal,
                    ];
                }
                $plusMonth = isset($plusMonth) ? $plusMonth->addMonth() : $carbonMonth->addMonth();
            }

            return $fullMonthData;
        } else {
            $firstDayOfMonth = "{$year}-07-01";
            $firstDayOfLastMonth = "{$year}-12-01";
            $lastDayOfMonth = date('Y-m-t', strtotime($firstDayOfLastMonth));

            $dCallPerMonth = Quotation::select(DB::raw('month(estimated_date) as month'), DB::raw('SUM(nett) as total'))
                ->whereBetween('estimated_date', [$firstDayOfMonth, $lastDayOfMonth])
                ->where('id_sales', Auth::user()->id)
                ->where('level', '1')->where('is_primary', '1')
                ->whereIn('status', ['20', '30', '40', '60', '80', '100'])
                ->groupBy('month')
                ->pluck('total', 'month');

            $unitForecastPerMonth = UnitQuotation::select(DB::raw('month(created_at) as month'), DB::raw('SUM(total - tax_amount) as total'))
                ->whereBetween('created_at', [$firstDayOfMonth, $lastDayOfMonth])
                ->where('id_sales', Auth::user()->id)
                ->where('is_latest', 1)
                ->whereIn('status', ['draft', 'quotation_sent', 'waiting_po', 'po_received'])
                ->groupBy('month')
                ->pluck('total', 'month');

            $fullMonthData = [];
            for ($month = 7; $month <= 12; $month++) {
                $monthKey = "{$month}";
                $carbonMonth = Carbon::parse($firstDayOfMonth);
                $formattedMonth = isset($plusMonth) ? $plusMonth->format('F') : $carbonMonth->format('F');
                $monthDays = date('t', strtotime($monthKey));
                if ($monthDays >= 4) {
                    $qTotal = isset($dCallPerMonth[$monthKey]) ? (float)$dCallPerMonth[$monthKey] : 0;
                    $uTotal = isset($unitForecastPerMonth[$monthKey]) ? (float)$unitForecastPerMonth[$monthKey] : 0;
                    $fullMonthData[$monthKey] = [
                        'month' => $formattedMonth,
                        'total' => $qTotal + $uTotal,
                    ];
                }
                $plusMonth = isset($plusMonth) ? $plusMonth->addMonth() : $carbonMonth->addMonth();
            }

            return $fullMonthData;
        }
    }

    protected function getMonthlyDataTotalPO($semester, $year)
    {
        if ($semester == 1) {
            $firstDayOfMonth = "{$year}-01-01";
            $firstDayOfLastMonth = "{$year}-06-01";
            $lastDayOfMonth = date('Y-m-t', strtotime($firstDayOfLastMonth));

            $dCallPerMonth = Quotation::select(DB::raw('month(po_date) as month'), DB::raw('SUM(nett) as total'))
                ->whereBetween('po_date', [$firstDayOfMonth, $lastDayOfMonth])
                ->where('id_sales', Auth::user()->id)
                ->where('level', '1')->where('is_primary', '1')
                ->where('status', '100')
                ->groupBy('month')
                ->pluck('total', 'month');

            $unitPoPerMonth = UnitQuotation::select(DB::raw('month(po_received) as month'), DB::raw('SUM(total - tax_amount) as total'))
                ->whereBetween('po_received', [$firstDayOfMonth, $lastDayOfMonth])
                ->where('id_sales', Auth::user()->id)
                ->where('status', 'po_received')
                ->where('is_latest', 1)
                ->groupBy('month')
                ->pluck('total', 'month');

            $fullMonthData = [];
            for ($month = 1; $month <= 6; $month++) {
                $monthKey = "{$month}";
                $carbonMonth = Carbon::parse($firstDayOfMonth);
                $formattedMonth = isset($plusMonth) ? $plusMonth->format('F') : $carbonMonth->format('F');
                $monthDays = date('t', strtotime($monthKey));
                if ($monthDays >= 4) {
                    $qTotal = isset($dCallPerMonth[$monthKey]) ? (float)$dCallPerMonth[$monthKey] : 0;
                    $uTotal = isset($unitPoPerMonth[$monthKey]) ? (float)$unitPoPerMonth[$monthKey] : 0;
                    $fullMonthData[$monthKey] = [
                        'month' => $formattedMonth,
                        'total' => $qTotal + $uTotal,
                    ];
                }
                $plusMonth = isset($plusMonth) ? $plusMonth->addMonth() : $carbonMonth->addMonth();
            }

            return $fullMonthData;
        } else {
            $firstDayOfMonth = "{$year}-07-01";
            $firstDayOfLastMonth = "{$year}-12-01";
            $lastDayOfMonth = date('Y-m-t', strtotime($firstDayOfLastMonth));

            $dCallPerMonth = Quotation::select(DB::raw('month(po_date) as month'), DB::raw('SUM(nett) as total'))
                ->whereBetween('po_date', [$firstDayOfMonth, $lastDayOfMonth])
                ->where('id_sales', Auth::user()->id)
                ->where('level', '1')->where('is_primary', '1')
                ->where('status', '100')
                ->groupBy('month')
                ->pluck('total', 'month');

            $unitPoPerMonth = UnitQuotation::select(DB::raw('month(po_received) as month'), DB::raw('SUM(total - tax_amount) as total'))
                ->whereBetween('po_received', [$firstDayOfMonth, $lastDayOfMonth])
                ->where('id_sales', Auth::user()->id)
                ->where('status', 'po_received')
                ->where('is_latest', 1)
                ->groupBy('month')
                ->pluck('total', 'month');

            $fullMonthData = [];
            for ($month = 7; $month <= 12; $month++) {
                $monthKey = "{$month}";
                $carbonMonth = Carbon::parse($firstDayOfMonth);
                $formattedMonth = isset($plusMonth) ? $plusMonth->format('F') : $carbonMonth->format('F');
                $monthDays = date('t', strtotime($monthKey));
                if ($monthDays >= 4) {
                    $qTotal = isset($dCallPerMonth[$monthKey]) ? (float)$dCallPerMonth[$monthKey] : 0;
                    $uTotal = isset($unitPoPerMonth[$monthKey]) ? (float)$unitPoPerMonth[$monthKey] : 0;
                    $fullMonthData[$monthKey] = [
                        'month' => $formattedMonth,
                        'total' => $qTotal + $uTotal,
                    ];
                }
                $plusMonth = isset($plusMonth) ? $plusMonth->addMonth() : $carbonMonth->addMonth();
            }

            return $fullMonthData;
        }
    }
    protected function getMonthlyDataDCSales($semester, $year, $sales)
    {
        if ($semester == 1) {
            $firstDayOfMonth = "{$year}-01-01";
            $firstDayOfLastMonth = "{$year}-06-01";
            $lastDayOfMonth = date('Y-m-t', strtotime($firstDayOfLastMonth));

            $dCallPerMonth = Activities::select(
                DB::raw('MONTH(date) as month'),
                'activities.id_client'
            )
                ->join('client as c', 'activities.id_client', '=', 'c.id')
                ->join('users as u', 'c.id_sales', '=', 'u.id')
                ->whereBetween('date', [$firstDayOfMonth, $lastDayOfMonth])
                ->where('id_sales', $sales)
                ->whereIn('activities.name', ['Daily Call', 'Follow Up'])
                ->where('status', 'Responded')
                ->groupBy('month', 'activities.id_client')
                ->get()
                ->groupBy('month')
                ->mapWithKeys(fn($items, $month) => [$month => $items->count()]);

            $fullMonthData = [];
            for ($month = 1; $month <= 6; $month++) {
                $monthKey = "{$month}";
                $carbonMonth = Carbon::parse($firstDayOfMonth);
                $formattedMonth = isset($plusMonth) ? $plusMonth->format('F') : $carbonMonth->format('F');
                $monthDays = date('t', strtotime($monthKey));
                if ($monthDays >= 4) {
                    $fullMonthData[$monthKey] = [
                        'monthKey' => $monthKey,
                        'month' => $formattedMonth,
                        'total' => isset($dCallPerMonth[$monthKey]) ? $dCallPerMonth[$monthKey] : 0,
                    ];
                }
                $plusMonth = isset($plusMonth) ? $plusMonth->addMonth() : $carbonMonth->addMonth();
            }
            // dd($fullMonthData);

            return $fullMonthData;
        } else {
            $firstDayOfMonth = "{$year}-07-01";
            $firstDayOfLastMonth = "{$year}-12-01";
            $lastDayOfMonth = date('Y-m-t', strtotime($firstDayOfLastMonth));

            $dCallPerMonth = Activities::select(
                DB::raw('MONTH(date) as month'),
                'activities.id_client'
            )
                ->join('client as c', 'activities.id_client', '=', 'c.id')
                ->join('users as u', 'c.id_sales', '=', 'u.id')
                ->whereBetween('date', [$firstDayOfMonth, $lastDayOfMonth])
                ->where('id_sales', $sales)
                ->whereIn('activities.name', ['Daily Call', 'Follow Up'])
                ->where('status', 'Responded')
                ->groupBy('month', 'activities.id_client')
                ->get()
                ->groupBy('month')
                ->mapWithKeys(fn($items, $month) => [$month => $items->count()]);

            $fullMonthData = [];
            for ($month = 7; $month <= 12; $month++) {
                $monthKey = "{$month}";
                $carbonMonth = Carbon::parse($firstDayOfMonth);
                $formattedMonth = isset($plusMonth) ? $plusMonth->format('F') : $carbonMonth->format('F');
                $monthDays = date('t', strtotime($monthKey));
                if ($monthDays >= 4) {
                    $fullMonthData[$monthKey] = [
                        'monthKey' => $monthKey,
                        'month' => $formattedMonth,
                        'total' => isset($dCallPerMonth[$monthKey]) ? $dCallPerMonth[$monthKey] : 0,
                    ];
                }
                $plusMonth = isset($plusMonth) ? $plusMonth->addMonth() : $carbonMonth->addMonth();
            }
            // dd($fullMonthData);

            return $fullMonthData;
        }
    }
    protected function cardMonthlyDCSales($semester, $year, $sales)
    {
        if ($semester == 1) {
            $firstDayOfMonth = "{$year}-01-01";
            $firstDayOfLastMonth = "{$year}-06-01";
            $lastDayOfMonth = date('Y-m-t', strtotime($firstDayOfLastMonth));

            $dCallPerMonth = Activities::select(
                DB::raw('MONTH(date) as month'),
                'activities.id_client'
            )
                ->join('client as c', 'activities.id_client', '=', 'c.id')
                ->join('users as u', 'c.id_sales', '=', 'u.id')
                ->whereBetween('date', [$firstDayOfMonth, $lastDayOfMonth])
                ->where('id_sales', $sales)
                ->whereIn('activities.name', ['Daily Call', 'Follow Up'])
                ->where('status', 'Responded')
                ->groupBy('month', 'activities.id_client')
                ->get()
                ->groupBy('month')
                ->mapWithKeys(fn($items, $month) => [$month => $items->count()]);

            $fullMonthData = [];
            for ($month = 1; $month <= 6; $month++) {
                $monthKey = "{$month}";
                $carbonMonth = Carbon::parse($firstDayOfMonth);
                $formattedMonth = isset($plusMonth) ? $plusMonth->format('F') : $carbonMonth->format('F');
                $monthDays = date('t', strtotime($monthKey));
                if ($monthDays >= 4) {
                    // Cek apakah data untuk bulan tersebut ada dalam $dCallPerMonth
                    // Jika tidak ada, maka jumlahnya 0
                    $total = isset($dCallPerMonth[$monthKey]) ? $dCallPerMonth[$monthKey] : 0;
                    // Tambahkan total ke dalam array $fullMonthData
                    $fullMonthData[] = $total;
                }
                $plusMonth = isset($plusMonth) ? $plusMonth->addMonth() : $carbonMonth->addMonth();
            }

            return $fullMonthData;
        } else {
            $firstDayOfMonth = "{$year}-07-01";
            $firstDayOfLastMonth = "{$year}-12-01";
            $lastDayOfMonth = date('Y-m-t', strtotime($firstDayOfLastMonth));

            $dCallPerMonth = Activities::select(
                DB::raw('MONTH(date) as month'),
                'activities.id_client'
            )
                ->join('client as c', 'activities.id_client', '=', 'c.id')
                ->join('users as u', 'c.id_sales', '=', 'u.id')
                ->whereBetween('date', [$firstDayOfMonth, $lastDayOfMonth])
                ->where('id_sales', $sales)
                ->whereIn('activities.name', ['Daily Call', 'Follow Up'])
                ->where('status', 'Responded')
                ->groupBy('month', 'activities.id_client')
                ->get()
                ->groupBy('month')
                ->mapWithKeys(fn($items, $month) => [$month => $items->count()]);

            $fullMonthData = [];
            for ($month = 7; $month <= 12; $month++) {
                $monthKey = "{$month}";
                $carbonMonth = Carbon::parse($firstDayOfMonth);
                $formattedMonth = isset($plusMonth) ? $plusMonth->format('F') : $carbonMonth->format('F');
                $monthDays = date('t', strtotime($monthKey));
                if ($monthDays >= 4) {
                    $fullMonthData[$monthKey] = [
                        'month' => $formattedMonth,
                        'total' => isset($dCallPerMonth[$monthKey]) ? $dCallPerMonth[$monthKey] : 0,
                    ];
                }
                $plusMonth = isset($plusMonth) ? $plusMonth->addMonth() : $carbonMonth->addMonth();
            }
            // dd($fullMonthData);

            return $fullMonthData;
        }
    }

    protected function getMonthlyDataCRMSales($semester, $year, $sales)
    {
        if ($semester == 1) {
            $firstDayOfMonth = "{$year}-01-01";
            $firstDayOfLastMonth = "{$year}-06-01";
            $lastDayOfMonth = date('Y-m-t', strtotime($firstDayOfLastMonth));

            $dCallPerMonth = Activities::select(
                DB::raw('MONTH(date) as month'),
                'activities.id_client'
            )
                ->join('client as c', 'activities.id_client', '=', 'c.id')
                ->join('users as u', 'c.id_sales', '=', 'u.id')
                ->whereBetween('date', [$firstDayOfMonth, $lastDayOfMonth])
                ->where('id_sales', $sales)
                ->where('activities.name', 'CRM')
                ->where('status', 'Responded')
                ->groupBy(DB::raw('MONTH(date)'), 'activities.id_client')
                ->get()
                ->groupBy('month')
                ->mapWithKeys(fn($items, $month) => [$month => $items->count()]);

            $fullMonthData = [];
            for ($month = 1; $month <= 6; $month++) {
                $monthKey = "{$month}";
                $carbonMonth = Carbon::parse($firstDayOfMonth);
                $formattedMonth = isset($plusMonth) ? $plusMonth->format('F') : $carbonMonth->format('F');
                $monthDays = date('t', strtotime($monthKey));
                if ($monthDays >= 4) {
                    $fullMonthData[$monthKey] = [
                        'month' => $formattedMonth,
                        'total' => isset($dCallPerMonth[$monthKey]) ? $dCallPerMonth[$monthKey] : 0,
                    ];
                }
                $plusMonth = isset($plusMonth) ? $plusMonth->addMonth() : $carbonMonth->addMonth();
            }
            // dd($fullMonthData);

            return $fullMonthData;
        } else {
            $firstDayOfMonth = "{$year}-07-01";
            $firstDayOfLastMonth = "{$year}-12-01";
            $lastDayOfMonth = date('Y-m-t', strtotime($firstDayOfLastMonth));

            $dCallPerMonth = Activities::select(
                DB::raw('MONTH(date) as month'),
                'activities.id_client'
            )
                ->join('client as c', 'activities.id_client', '=', 'c.id')
                ->join('users as u', 'c.id_sales', '=', 'u.id')
                ->whereBetween('date', [$firstDayOfMonth, $lastDayOfMonth])
                ->where('id_sales', $sales)
                ->where('activities.name', 'CRM')
                ->where('status', 'Responded')
                ->groupBy(DB::raw('MONTH(date)'), 'activities.id_client')
                ->get()
                ->groupBy('month')
                ->mapWithKeys(fn($items, $month) => [$month => $items->count()]);

            $fullMonthData = [];
            for ($month = 7; $month <= 12; $month++) {
                $monthKey = "{$month}";
                $carbonMonth = Carbon::parse($firstDayOfMonth);
                $formattedMonth = isset($plusMonth) ? $plusMonth->format('F') : $carbonMonth->format('F');
                $monthDays = date('t', strtotime($monthKey));
                if ($monthDays >= 4) {
                    $fullMonthData[$monthKey] = [
                        'month' => $formattedMonth,
                        'total' => isset($dCallPerMonth[$monthKey]) ? $dCallPerMonth[$monthKey] : 0,
                    ];
                }
                $plusMonth = isset($plusMonth) ? $plusMonth->addMonth() : $carbonMonth->addMonth();
            }
            // dd($fullMonthData);

            return $fullMonthData;
        }
    }
    protected function getMonthlyDataVisitSales($semester, $year, $sales)
    {
        if ($semester == 1) {
            $firstDayOfMonth = "{$year}-01-01";
            $firstDayOfLastMonth = "{$year}-06-01";
            $lastDayOfMonth = date('Y-m-t', strtotime($firstDayOfLastMonth));

            $dCallPerMonth = Activities::select(DB::raw('CONCAT(YEAR(date), "-", MONTH(date)) as date'), DB::raw('month(date) as month'), DB::raw('COUNT(*) as total'))
                ->join('client as c', 'activities.id_client', '=', 'c.id')
                ->join('users as u', 'c.id_sales', '=', 'u.id')
                ->whereBetween('date', [$firstDayOfMonth, $lastDayOfMonth])
                ->where('id_sales', $sales)
                ->where('activities.name', 'Visit')
                ->where('status', 'Responded')
                ->groupBy('month')
                ->orderBy('month')
                ->pluck('total', 'month');

            $fullMonthData = [];
            for ($month = 1; $month <= 6; $month++) {
                $monthKey = "{$month}";
                $carbonMonth = Carbon::parse($firstDayOfMonth);
                $formattedMonth = isset($plusMonth) ? $plusMonth->format('F') : $carbonMonth->format('F');
                $monthDays = date('t', strtotime($monthKey));
                if ($monthDays >= 4) {
                    $fullMonthData[$monthKey] = [
                        'month' => $formattedMonth,
                        'total' => isset($dCallPerMonth[$monthKey]) ? $dCallPerMonth[$monthKey] : 0,
                    ];
                }
                $plusMonth = isset($plusMonth) ? $plusMonth->addMonth() : $carbonMonth->addMonth();
            }
            // dd($fullMonthData);

            return $fullMonthData;
        } else {
            $firstDayOfMonth = "{$year}-07-01";
            $firstDayOfLastMonth = "{$year}-12-01";
            $lastDayOfMonth = date('Y-m-t', strtotime($firstDayOfLastMonth));

            $dCallPerMonth = Activities::select(DB::raw('CONCAT(YEAR(date), "-", MONTH(date)) as date'), DB::raw('month(date) as month'), DB::raw('COUNT(*) as total'))
                ->join('client as c', 'activities.id_client', '=', 'c.id')
                ->join('users as u', 'c.id_sales', '=', 'u.id')
                ->whereBetween('date', [$firstDayOfMonth, $lastDayOfMonth])
                ->where('id_sales', $sales)
                ->where('activities.name', 'Visit')
                ->where('status', 'Responded')
                ->groupBy('month')
                ->orderBy('month')
                ->pluck('total', 'month');

            $fullMonthData = [];
            for ($month = 7; $month <= 12; $month++) {
                $monthKey = "{$month}";
                $carbonMonth = Carbon::parse($firstDayOfMonth);
                $formattedMonth = isset($plusMonth) ? $plusMonth->format('F') : $carbonMonth->format('F');
                $monthDays = date('t', strtotime($monthKey));
                if ($monthDays >= 4) {
                    $fullMonthData[$monthKey] = [
                        'month' => $formattedMonth,
                        'total' => isset($dCallPerMonth[$monthKey]) ? $dCallPerMonth[$monthKey] : 0,
                    ];
                }
                $plusMonth = isset($plusMonth) ? $plusMonth->addMonth() : $carbonMonth->addMonth();
            }
            // dd($fullMonthData);

            return $fullMonthData;
        }
    }
    protected function getMonthlyDataQuoteSales($semester, $year, $sales)
    {
        if ($semester == 1) {
            $firstDayOfMonth = "{$year}-01-01";
            $firstDayOfLastMonth = "{$year}-06-01";
            $lastDayOfMonth = date('Y-m-t', strtotime($firstDayOfLastMonth));


            $dCallPerMonth = Quotation::select(DB::raw('CONCAT(YEAR(estimated_date), "-", MONTH(estimated_date)) as date'), DB::raw('month(estimated_date) as month'), DB::raw('COUNT(*) as total'))
                ->whereBetween('estimated_date', [$firstDayOfMonth, $lastDayOfMonth])
                ->where('id_sales', $sales)
                ->where('level', '1')->where('is_primary', '1')
                ->groupBy('month')
                ->orderBy('month')
                ->pluck('total', 'month');

            $fullMonthData = [];
            for ($month = 1; $month <= 6; $month++) {
                $monthKey = "{$month}";
                $carbonMonth = Carbon::parse($firstDayOfMonth);
                $formattedMonth = isset($plusMonth) ? $plusMonth->format('F') : $carbonMonth->format('F');
                $monthDays = date('t', strtotime($monthKey));
                if ($monthDays >= 4) {
                    $fullMonthData[$monthKey] = [
                        'month' => $formattedMonth,
                        'total' => isset($dCallPerMonth[$monthKey]) ? $dCallPerMonth[$monthKey] : 0,
                    ];
                }
                $plusMonth = isset($plusMonth) ? $plusMonth->addMonth() : $carbonMonth->addMonth();
            }
            // dd($fullMonthData);

            return $fullMonthData;
        } else {
            $firstDayOfMonth = "{$year}-07-01";
            $firstDayOfLastMonth = "{$year}-12-01";
            $lastDayOfMonth = date('Y-m-t', strtotime($firstDayOfLastMonth));

            $dCallPerMonth = Quotation::select(DB::raw('CONCAT(YEAR(estimated_date), "-", MONTH(estimated_date)) as date'), DB::raw('month(estimated_date) as month'), DB::raw('COUNT(*) as total'))
                ->whereBetween('estimated_date', [$firstDayOfMonth, $lastDayOfMonth])
                ->where('id_sales', $sales)
                ->where('level', '1')->where('is_primary', '1')
                ->groupBy('month')
                ->orderBy('month')
                ->pluck('total', 'month');

            $fullMonthData = [];
            for ($month = 7; $month <= 12; $month++) {
                $monthKey = "{$month}";
                $carbonMonth = Carbon::parse($firstDayOfMonth);
                $formattedMonth = isset($plusMonth) ? $plusMonth->format('F') : $carbonMonth->format('F');
                $monthDays = date('t', strtotime($monthKey));
                if ($monthDays >= 4) {
                    $fullMonthData[$monthKey] = [
                        'month' => $formattedMonth,
                        'total' => isset($dCallPerMonth[$monthKey]) ? $dCallPerMonth[$monthKey] : 0,
                    ];
                }
                $plusMonth = isset($plusMonth) ? $plusMonth->addMonth() : $carbonMonth->addMonth();
            }
            // dd($fullMonthData);

            return $fullMonthData;
        }
    }
    protected function getMonthlyDataPOSales($semester, $year, $sales)
    {
        if ($semester == 1) {
            $firstDayOfMonth = "{$year}-01-01";
            $firstDayOfLastMonth = "{$year}-06-01";
            $lastDayOfMonth = date('Y-m-t', strtotime($firstDayOfLastMonth));

            $dCallPerMonth = Quotation::select(DB::raw('CONCAT(YEAR(po_date), "-", MONTH(po_date)) as date'), DB::raw('month(po_date) as month'), DB::raw('COUNT(*) as total'))
                ->whereBetween('po_date', [$firstDayOfMonth, $lastDayOfMonth])
                ->where('id_sales', $sales)
                ->where('status', '100')
                ->where('level', '1')->where('is_primary', '1')
                ->groupBy('month')
                ->orderBy('month')
                ->pluck('total', 'month');

            $unitPerMonth = UnitQuotation::select(DB::raw('month(po_received) as month'), DB::raw('COUNT(*) as total'))
                ->whereBetween('po_received', [$firstDayOfMonth, $lastDayOfMonth])
                ->where('id_sales', $sales)
                ->where('status', 'po_received')
                ->where('is_latest', 1)
                ->groupBy('month')
                ->orderBy('month')
                ->pluck('total', 'month');

            $fullMonthData = [];
            for ($month = 1; $month <= 6; $month++) {
                $monthKey = "{$month}";
                $carbonMonth = Carbon::parse($firstDayOfMonth);
                $formattedMonth = isset($plusMonth) ? $plusMonth->format('F') : $carbonMonth->format('F');
                $monthDays = date('t', strtotime($monthKey));
                if ($monthDays >= 4) {
                    $fullMonthData[$monthKey] = [
                        'month' => $formattedMonth,
                        'total' => (isset($dCallPerMonth[$monthKey]) ? $dCallPerMonth[$monthKey] : 0) + (isset($unitPerMonth[$monthKey]) ? $unitPerMonth[$monthKey] : 0),
                    ];
                }
                $plusMonth = isset($plusMonth) ? $plusMonth->addMonth() : $carbonMonth->addMonth();
            }
            // dd($fullMonthData);

            return $fullMonthData;
        } else {
            $firstDayOfMonth = "{$year}-07-01";
            $firstDayOfLastMonth = "{$year}-12-01";
            $lastDayOfMonth = date('Y-m-t', strtotime($firstDayOfLastMonth));

            $dCallPerMonth = Quotation::select(DB::raw('CONCAT(YEAR(po_date), "-", MONTH(po_date)) as date'), DB::raw('month(po_date) as month'), DB::raw('COUNT(*) as total'))
                ->whereBetween('po_date', [$firstDayOfMonth, $lastDayOfMonth])
                ->where('id_sales', $sales)
                ->where('status', '100')
                ->where('level', '1')->where('is_primary', '1')
                ->groupBy('month')
                ->orderBy('month')
                ->pluck('total', 'month');

            $unitPerMonth = UnitQuotation::select(DB::raw('month(po_received) as month'), DB::raw('COUNT(*) as total'))
                ->whereBetween('po_received', [$firstDayOfMonth, $lastDayOfMonth])
                ->where('id_sales', $sales)
                ->where('status', 'po_received')
                ->where('is_latest', 1)
                ->groupBy('month')
                ->orderBy('month')
                ->pluck('total', 'month');

            $fullMonthData = [];
            for ($month = 7; $month <= 12; $month++) {
                $monthKey = "{$month}";
                $carbonMonth = Carbon::parse($firstDayOfMonth);
                $formattedMonth = isset($plusMonth) ? $plusMonth->format('F') : $carbonMonth->format('F');
                $monthDays = date('t', strtotime($monthKey));
                if ($monthDays >= 4) {
                    $fullMonthData[$monthKey] = [
                        'month' => $formattedMonth,
                        'total' => (isset($dCallPerMonth[$monthKey]) ? $dCallPerMonth[$monthKey] : 0) + (isset($unitPerMonth[$monthKey]) ? $unitPerMonth[$monthKey] : 0),
                    ];
                }
                $plusMonth = isset($plusMonth) ? $plusMonth->addMonth() : $carbonMonth->addMonth();
            }
            // dd($fullMonthData);

            return $fullMonthData;
        }
    }
    protected function getMonthlyDataLossSales($semester, $year, $sales)
    {
        if ($semester == 1) {
            $firstDayOfMonth = "{$year}-01-01";
            $firstDayOfLastMonth = "{$year}-06-01";
            $lastDayOfMonth = date('Y-m-t', strtotime($firstDayOfLastMonth));

            $dCallPerMonth = Quotation::select(DB::raw('CONCAT(YEAR(estimated_date), "-", MONTH(estimated_date)) as date'), DB::raw('month(estimated_date) as month'), DB::raw('COUNT(*) as total'))
                ->whereBetween('estimated_date', [$firstDayOfMonth, $lastDayOfMonth])
                ->where('id_sales', $sales)
                ->where('status', '0')
                ->where('level', '1')->where('is_primary', '1')
                ->groupBy('month')
                ->orderBy('month')
                ->pluck('total', 'month');

            $fullMonthData = [];
            for ($month = 1; $month <= 6; $month++) {
                $monthKey = "{$month}";
                $carbonMonth = Carbon::parse($firstDayOfMonth);
                $formattedMonth = isset($plusMonth) ? $plusMonth->format('F') : $carbonMonth->format('F');
                $monthDays = date('t', strtotime($monthKey));
                if ($monthDays >= 4) {
                    $fullMonthData[$monthKey] = [
                        'month' => $formattedMonth,
                        'total' => isset($dCallPerMonth[$monthKey]) ? $dCallPerMonth[$monthKey] : 0,
                    ];
                }
                $plusMonth = isset($plusMonth) ? $plusMonth->addMonth() : $carbonMonth->addMonth();
            }
            // dd($fullMonthData);

            return $fullMonthData;
        } else {
            $firstDayOfMonth = "{$year}-07-01";
            $firstDayOfLastMonth = "{$year}-12-01";
            $lastDayOfMonth = date('Y-m-t', strtotime($firstDayOfLastMonth));

            $dCallPerMonth = Quotation::select(DB::raw('CONCAT(YEAR(estimated_date), "-", MONTH(estimated_date)) as date'), DB::raw('month(estimated_date) as month'), DB::raw('COUNT(*) as total'))
                ->whereBetween('estimated_date', [$firstDayOfMonth, $lastDayOfMonth])
                ->where('id_sales', $sales)
                ->where('status', '0')
                ->where('level', '1')->where('is_primary', '1')
                ->groupBy('month')
                ->orderBy('month')
                ->pluck('total', 'month');

            $fullMonthData = [];
            for ($month = 7; $month <= 12; $month++) {
                $monthKey = "{$month}";
                $carbonMonth = Carbon::parse($firstDayOfMonth);
                $formattedMonth = isset($plusMonth) ? $plusMonth->format('F') : $carbonMonth->format('F');
                $monthDays = date('t', strtotime($monthKey));
                if ($monthDays >= 4) {
                    $fullMonthData[$monthKey] = [
                        'month' => $formattedMonth,
                        'total' => isset($dCallPerMonth[$monthKey]) ? $dCallPerMonth[$monthKey] : 0,
                    ];
                }
                $plusMonth = isset($plusMonth) ? $plusMonth->addMonth() : $carbonMonth->addMonth();
            }
            // dd($fullMonthData);

            return $fullMonthData;
        }
    }
    protected function getMonthlyDataProspect($semester, $year, $sales)
    {
        if ($semester == 1) {
            $firstDayOfMonth = "{$year}-01-01";
            $firstDayOfLastMonth = "{$year}-06-01";
            $lastDayOfMonth = date('Y-m-t', strtotime($firstDayOfLastMonth));

            $dCallPerMonth = Prospect::select(DB::raw('CONCAT(YEAR(date), "-", MONTH(date)) as date'), DB::raw('month(date) as month'), DB::raw('COUNT(*) as total'))
                ->whereBetween('date', [$firstDayOfMonth, $lastDayOfMonth])
                ->where('id_support', $sales)
                ->groupBy('month')
                ->orderBy('month')
                ->pluck('total', 'month');

            $fullMonthData = [];
            for ($month = 1; $month <= 6; $month++) {
                $monthKey = "{$month}";
                $carbonMonth = Carbon::parse($firstDayOfMonth);
                $formattedMonth = isset($plusMonth) ? $plusMonth->format('F') : $carbonMonth->format('F');
                $monthDays = date('t', strtotime($monthKey));
                if ($monthDays >= 4) {
                    $fullMonthData[$monthKey] = [
                        'month' => $formattedMonth,
                        'total' => isset($dCallPerMonth[$monthKey]) ? $dCallPerMonth[$monthKey] : 0,
                    ];
                }
                $plusMonth = isset($plusMonth) ? $plusMonth->addMonth() : $carbonMonth->addMonth();
            }
            // dd($fullMonthData);

            return $fullMonthData;
        } else {
            $firstDayOfMonth = "{$year}-07-01";
            $firstDayOfLastMonth = "{$year}-12-01";
            $lastDayOfMonth = date('Y-m-t', strtotime($firstDayOfLastMonth));

            $dCallPerMonth = Prospect::select(DB::raw('CONCAT(YEAR(date), "-", MONTH(date)) as date'), DB::raw('month(date) as month'), DB::raw('COUNT(*) as total'))
                ->whereBetween('date', [$firstDayOfMonth, $lastDayOfMonth])
                ->where('id_support', $sales)
                ->groupBy('month')
                ->orderBy('month')
                ->pluck('total', 'month');

            $fullMonthData = [];
            for ($month = 7; $month <= 12; $month++) {
                $monthKey = "{$month}";
                $carbonMonth = Carbon::parse($firstDayOfMonth);
                $formattedMonth = isset($plusMonth) ? $plusMonth->format('F') : $carbonMonth->format('F');
                $monthDays = date('t', strtotime($monthKey));
                if ($monthDays >= 4) {
                    $fullMonthData[$monthKey] = [
                        'month' => $formattedMonth,
                        'total' => isset($dCallPerMonth[$monthKey]) ? $dCallPerMonth[$monthKey] : 0,
                    ];
                }
                $plusMonth = isset($plusMonth) ? $plusMonth->addMonth() : $carbonMonth->addMonth();
            }
            // dd($fullMonthData);

            return $fullMonthData;
        }
    }
    protected function getMonthlyDataProvideProspect($semester, $year, $sales)
    {
        if ($semester == 1) {
            $firstDayOfMonth = "{$year}-01-01";
            $firstDayOfLastMonth = "{$year}-06-01";
            $lastDayOfMonth = date('Y-m-t', strtotime($firstDayOfLastMonth));

            $dCallPerMonth = Prospect::select(DB::raw('CONCAT(YEAR(date), "-", MONTH(date)) as date'), DB::raw('month(date) as month'), DB::raw('COUNT(*) as total'))
                ->whereBetween('date', [$firstDayOfMonth, $lastDayOfMonth])
                ->where('id_support', $sales)
                ->where('provide', '!=', '0')
                ->groupBy('month')
                ->orderBy('month')
                ->pluck('total', 'month');

            $fullMonthData = [];
            for ($month = 1; $month <= 6; $month++) {
                $monthKey = "{$month}";
                $carbonMonth = Carbon::parse($firstDayOfMonth);
                $formattedMonth = isset($plusMonth) ? $plusMonth->format('F') : $carbonMonth->format('F');
                $monthDays = date('t', strtotime($monthKey));
                if ($monthDays >= 4) {
                    $fullMonthData[$monthKey] = [
                        'month' => $formattedMonth,
                        'total' => isset($dCallPerMonth[$monthKey]) ? $dCallPerMonth[$monthKey] : 0,
                    ];
                }
                $plusMonth = isset($plusMonth) ? $plusMonth->addMonth() : $carbonMonth->addMonth();
            }
            // dd($fullMonthData);

            return $fullMonthData;
        } else {
            $firstDayOfMonth = "{$year}-07-01";
            $firstDayOfLastMonth = "{$year}-12-01";
            $lastDayOfMonth = date('Y-m-t', strtotime($firstDayOfLastMonth));

            $dCallPerMonth = Prospect::select(DB::raw('CONCAT(YEAR(date), "-", MONTH(date)) as date'), DB::raw('month(date) as month'), DB::raw('COUNT(*) as total'))
                ->whereBetween('date', [$firstDayOfMonth, $lastDayOfMonth])
                ->where('id_support', $sales)
                ->where('provide', '!=', '0')
                ->groupBy('month')
                ->orderBy('month')
                ->pluck('total', 'month');

            $fullMonthData = [];
            for ($month = 7; $month <= 12; $month++) {
                $monthKey = "{$month}";
                $carbonMonth = Carbon::parse($firstDayOfMonth);
                $formattedMonth = isset($plusMonth) ? $plusMonth->format('F') : $carbonMonth->format('F');
                $monthDays = date('t', strtotime($monthKey));
                if ($monthDays >= 4) {
                    $fullMonthData[$monthKey] = [
                        'month' => $formattedMonth,
                        'total' => isset($dCallPerMonth[$monthKey]) ? $dCallPerMonth[$monthKey] : 0,
                    ];
                }
                $plusMonth = isset($plusMonth) ? $plusMonth->addMonth() : $carbonMonth->addMonth();
            }
            // dd($fullMonthData);

            return $fullMonthData;
        }
    }
    protected function getMonthlyDataQuoteProspect($semester, $year, $sales)
    {
        if ($semester == 1) {
            $firstDayOfMonth = "{$year}-01-01";
            $firstDayOfLastMonth = "{$year}-06-01";
            $lastDayOfMonth = date('Y-m-t', strtotime($firstDayOfLastMonth));


            $dCallPerMonth = Quotation::select(DB::raw('CONCAT(YEAR(estimated_date), "-", MONTH(estimated_date)) as date'), DB::raw('month(estimated_date) as month'), DB::raw('COUNT(*) as total'))
                ->whereBetween('estimated_date', [$firstDayOfMonth, $lastDayOfMonth])
                ->where('id_support', $sales)
                ->where('level', '1')->where('is_primary', '1')
                ->groupBy('month')
                ->orderBy('month')
                ->pluck('total', 'month');

            $fullMonthData = [];
            for ($month = 1; $month <= 6; $month++) {
                $monthKey = "{$month}";
                $carbonMonth = Carbon::parse($firstDayOfMonth);
                $formattedMonth = isset($plusMonth) ? $plusMonth->format('F') : $carbonMonth->format('F');
                $monthDays = date('t', strtotime($monthKey));
                if ($monthDays >= 4) {
                    $fullMonthData[$monthKey] = [
                        'month' => $formattedMonth,
                        'total' => isset($dCallPerMonth[$monthKey]) ? $dCallPerMonth[$monthKey] : 0,
                    ];
                }
                $plusMonth = isset($plusMonth) ? $plusMonth->addMonth() : $carbonMonth->addMonth();
            }
            // dd($fullMonthData);

            return $fullMonthData;
        } else {
            $firstDayOfMonth = "{$year}-07-01";
            $firstDayOfLastMonth = "{$year}-12-01";
            $lastDayOfMonth = date('Y-m-t', strtotime($firstDayOfLastMonth));

            $dCallPerMonth = Quotation::select(DB::raw('CONCAT(YEAR(estimated_date), "-", MONTH(estimated_date)) as date'), DB::raw('month(estimated_date) as month'), DB::raw('COUNT(*) as total'))
                ->whereBetween('estimated_date', [$firstDayOfMonth, $lastDayOfMonth])
                ->where('id_support', $sales)
                ->where('level', '1')->where('is_primary', '1')
                ->groupBy('month')
                ->orderBy('month')
                ->pluck('total', 'month');

            $fullMonthData = [];
            for ($month = 7; $month <= 12; $month++) {
                $monthKey = "{$month}";
                $carbonMonth = Carbon::parse($firstDayOfMonth);
                $formattedMonth = isset($plusMonth) ? $plusMonth->format('F') : $carbonMonth->format('F');
                $monthDays = date('t', strtotime($monthKey));
                if ($monthDays >= 4) {
                    $fullMonthData[$monthKey] = [
                        'month' => $formattedMonth,
                        'total' => isset($dCallPerMonth[$monthKey]) ? $dCallPerMonth[$monthKey] : 0,
                    ];
                }
                $plusMonth = isset($plusMonth) ? $plusMonth->addMonth() : $carbonMonth->addMonth();
            }
            // dd($fullMonthData);

            return $fullMonthData;
        }
    }
    protected function getMonthlyDataPOProspect($semester, $year, $sales)
    {
        if ($semester == 1) {
            $firstDayOfMonth = "{$year}-01-01";
            $firstDayOfLastMonth = "{$year}-06-01";
            $lastDayOfMonth = date('Y-m-t', strtotime($firstDayOfLastMonth));

            $dCallPerMonth = Quotation::select(DB::raw('CONCAT(YEAR(po_date), "-", MONTH(po_date)) as date'), DB::raw('month(po_date) as month'), DB::raw('COUNT(*) as total'))
                ->whereBetween('po_date', [$firstDayOfMonth, $lastDayOfMonth])
                ->where('id_support', $sales)
                ->where('status', '100')
                ->where('level', '1')->where('is_primary', '1')
                ->groupBy('month')
                ->orderBy('month')
                ->pluck('total', 'month');

            $fullMonthData = [];
            for ($month = 1; $month <= 6; $month++) {
                $monthKey = "{$month}";
                $carbonMonth = Carbon::parse($firstDayOfMonth);
                $formattedMonth = isset($plusMonth) ? $plusMonth->format('F') : $carbonMonth->format('F');
                $monthDays = date('t', strtotime($monthKey));
                if ($monthDays >= 4) {
                    $fullMonthData[$monthKey] = [
                        'month' => $formattedMonth,
                        'total' => isset($dCallPerMonth[$monthKey]) ? $dCallPerMonth[$monthKey] : 0,
                    ];
                }
                $plusMonth = isset($plusMonth) ? $plusMonth->addMonth() : $carbonMonth->addMonth();
            }
            // dd($fullMonthData);

            return $fullMonthData;
        } else {
            $firstDayOfMonth = "{$year}-07-01";
            $firstDayOfLastMonth = "{$year}-12-01";
            $lastDayOfMonth = date('Y-m-t', strtotime($firstDayOfLastMonth));

            $dCallPerMonth = Quotation::select(DB::raw('CONCAT(YEAR(po_date), "-", MONTH(po_date)) as date'), DB::raw('month(po_date) as month'), DB::raw('COUNT(*) as total'))
                ->whereBetween('po_date', [$firstDayOfMonth, $lastDayOfMonth])
                ->where('id_support', $sales)
                ->where('status', '100')
                ->where('level', '1')->where('is_primary', '1')
                ->groupBy('month')
                ->orderBy('month')
                ->pluck('total', 'month');

            $fullMonthData = [];
            for ($month = 7; $month <= 12; $month++) {
                $monthKey = "{$month}";
                $carbonMonth = Carbon::parse($firstDayOfMonth);
                $formattedMonth = isset($plusMonth) ? $plusMonth->format('F') : $carbonMonth->format('F');
                $monthDays = date('t', strtotime($monthKey));
                if ($monthDays >= 4) {
                    $fullMonthData[$monthKey] = [
                        'month' => $formattedMonth,
                        'total' => isset($dCallPerMonth[$monthKey]) ? $dCallPerMonth[$monthKey] : 0,
                    ];
                }
                $plusMonth = isset($plusMonth) ? $plusMonth->addMonth() : $carbonMonth->addMonth();
            }
            // dd($fullMonthData);

            return $fullMonthData;
        }
    }
    protected function getMonthlyDataLeadsSales($semester, $year, $sales)
    {
        if ($semester == 1) {
            $firstDayOfMonth = "{$year}-01-01";
            $firstDayOfLastMonth = "{$year}-06-01";
            $lastDayOfMonth = date('Y-m-t', strtotime($firstDayOfLastMonth));

            $dCallPerMonth = Client::select(DB::raw('CONCAT(YEAR(created_at), "-", MONTH(created_at)) as date'), DB::raw('month(created_at) as month'), DB::raw('COUNT(*) as total'))
                ->whereBetween('created_at', [$firstDayOfMonth, $lastDayOfMonth])
                ->where('id_sales', $sales)
                ->groupBy('month')
                ->orderBy('month')
                ->pluck('total', 'month');

            $fullMonthData = [];
            for ($month = 1; $month <= 6; $month++) {
                $monthKey = "{$month}";
                $carbonMonth = Carbon::parse($firstDayOfMonth);
                $formattedMonth = isset($plusMonth) ? $plusMonth->format('F') : $carbonMonth->format('F');
                $monthDays = date('t', strtotime($monthKey));
                if ($monthDays >= 4) {
                    $fullMonthData[$monthKey] = [
                        'month' => $formattedMonth,
                        'total' => isset($dCallPerMonth[$monthKey]) ? $dCallPerMonth[$monthKey] : 0,
                    ];
                }
                $plusMonth = isset($plusMonth) ? $plusMonth->addMonth() : $carbonMonth->addMonth();
            }
            // dd($fullMonthData);

            return $fullMonthData;
        } else {
            $firstDayOfMonth = "{$year}-07-01";
            $firstDayOfLastMonth = "{$year}-12-01";
            $lastDayOfMonth = date('Y-m-t', strtotime($firstDayOfLastMonth));

            $dCallPerMonth = Client::select(DB::raw('CONCAT(YEAR(created_at), "-", MONTH(created_at)) as date'), DB::raw('month(created_at) as month'), DB::raw('COUNT(*) as total'))
                ->whereBetween('created_at', [$firstDayOfMonth, $lastDayOfMonth])
                ->where('id_sales', $sales)
                ->groupBy('month')
                ->orderBy('month')
                ->pluck('total', 'month');

            $fullMonthData = [];
            for ($month = 7; $month <= 12; $month++) {
                $monthKey = "{$month}";
                $carbonMonth = Carbon::parse($firstDayOfMonth);
                $formattedMonth = isset($plusMonth) ? $plusMonth->format('F') : $carbonMonth->format('F');
                $monthDays = date('t', strtotime($monthKey));
                if ($monthDays >= 4) {
                    $fullMonthData[$monthKey] = [
                        'month' => $formattedMonth,
                        'total' => isset($dCallPerMonth[$monthKey]) ? $dCallPerMonth[$monthKey] : 0,
                    ];
                }
                $plusMonth = isset($plusMonth) ? $plusMonth->addMonth() : $carbonMonth->addMonth();
            }
            // dd($fullMonthData);

            return $fullMonthData;
        }
    }
    protected function getMonthlyDataPOModalSales($semester, $year, $sales)
    {
        $user = User::find($sales);
        if ($semester == 1) {
            $firstDayOfMonth = "{$year}-01-01";
            $firstDayOfLastMonth = "{$year}-06-01";
            $lastDayOfMonth = date('Y-m-t', strtotime($firstDayOfLastMonth));

            if ($user->role == 'Sales') {
                $dCallPerMonth = Quotation::select('quotation.*')
                    ->selectRaw('MONTH(po_date) as month')
                    ->whereBetween('po_date', [$firstDayOfMonth, $lastDayOfMonth])
                    ->where('quotation.id_sales', $sales)
                    ->where('status', '100')
                    ->where('level', '1')->where('is_primary', '1')
                    ->get();
                $unitPerMonth = UnitQuotation::with('client')
                    ->whereBetween('po_received', [$firstDayOfMonth, $lastDayOfMonth])
                    ->where('id_sales', $sales)
                    ->where('status', 'po_received')
                    ->where('is_latest', 1)
                    ->get();
            } else {
                $dCallPerMonth = Quotation::select('quotation.*')
                    ->selectRaw('MONTH(po_date) as month')
                    ->whereBetween('po_date', [$firstDayOfMonth, $lastDayOfMonth])
                    ->where('quotation.id_support', $sales)
                    ->where('status', '100')
                    ->where('level', '1')->where('is_primary', '1')
                    ->get();
                $unitPerMonth = collect(); // unit_quotation tidak punya id_support
            }
            // dd($sales);
            $fullMonthData = [];
            for ($month = 1; $month <= 6; $month++) {
                $monthKey = "{$month}";
                $carbonMonth = Carbon::parse($firstDayOfMonth);
                $formattedMonth = isset($plusMonth) ? $plusMonth->format('F') : $carbonMonth->format('F');
                $monthDays = date('t', strtotime($monthKey));
                if ($monthDays >= 4) {
                    $dataForMonth = collect($dCallPerMonth->where('month', $monthKey)->toArray())
                        ->map(fn($row) => $row + ['source' => 'quotation']);
                    $unitDataForMonth = $unitPerMonth
                        ->filter(fn($u) => (int) Carbon::parse($u->po_received)->month === (int) $monthKey)
                        ->map(fn($u) => [
                            'id' => $u->id,
                            'no_quote' => $u->no_quote,
                            'title' => $u->title,
                            'nett' => $u->total - $u->tax_amount,
                            'estimated_date' => $u->po_received,
                            'company' => $u->client->company ?? 'Client Di Hapus',
                            'source' => 'unit_quotation',
                        ]);
                    $fullMonthData[$monthKey] = [
                        'monthKey' => $monthKey,
                        'month' => $formattedMonth,
                        'data' => $dataForMonth->concat($unitDataForMonth)->values()->toArray(),
                    ];
                }
                $plusMonth = isset($plusMonth) ? $plusMonth->addMonth() : $carbonMonth->addMonth();
            }
            // dd($fullMonthData);

            return $fullMonthData;
        } else {
            $firstDayOfMonth = "{$year}-07-01";
            $firstDayOfLastMonth = "{$year}-12-01";
            $lastDayOfMonth = date('Y-m-t', strtotime($firstDayOfLastMonth));

            if ($user->role == 'Sales') {
                $dCallPerMonth = Quotation::select('quotation.*')
                    ->selectRaw('MONTH(po_date) as month')
                    ->whereBetween('po_date', [$firstDayOfMonth, $lastDayOfMonth])
                    ->where('quotation.id_sales', $sales)
                    ->where('status', '100')
                    ->where('level', '1')->where('is_primary', '1')
                    ->get();
                $unitPerMonth = UnitQuotation::with('client')
                    ->whereBetween('po_received', [$firstDayOfMonth, $lastDayOfMonth])
                    ->where('id_sales', $sales)
                    ->where('status', 'po_received')
                    ->where('is_latest', 1)
                    ->get();
            } else {
                $dCallPerMonth = Quotation::select('quotation.*')
                    ->selectRaw('MONTH(po_date) as month')
                    ->whereBetween('po_date', [$firstDayOfMonth, $lastDayOfMonth])
                    ->where('quotation.id_support', $sales)
                    ->where('status', '100')
                    ->where('level', '1')->where('is_primary', '1')
                    ->get();
                $unitPerMonth = collect(); // unit_quotation tidak punya id_support
            }
            // dd($dCallPerMonth);
            $fullMonthData = [];
            for ($month = 7; $month <= 12; $month++) {
                $monthKey = "{$month}";
                $carbonMonth = Carbon::parse($firstDayOfMonth);
                $formattedMonth = isset($plusMonth) ? $plusMonth->format('F') : $carbonMonth->format('F');
                $monthDays = date('t', strtotime($monthKey));
                if ($monthDays >= 4) {
                    $dataForMonth = collect($dCallPerMonth->where('month', $monthKey)->toArray())
                        ->map(fn($row) => $row + ['source' => 'quotation']);
                    $unitDataForMonth = $unitPerMonth
                        ->filter(fn($u) => (int) Carbon::parse($u->po_received)->month === (int) $monthKey)
                        ->map(fn($u) => [
                            'id' => $u->id,
                            'no_quote' => $u->no_quote,
                            'title' => $u->title,
                            'nett' => $u->total - $u->tax_amount,
                            'estimated_date' => $u->po_received,
                            'company' => $u->client->company ?? 'Client Di Hapus',
                            'source' => 'unit_quotation',
                        ]);
                    $fullMonthData[$monthKey] = [
                        'monthKey' => $monthKey,
                        'month' => $formattedMonth,
                        'data' => $dataForMonth->concat($unitDataForMonth)->values()->toArray(),
                    ];
                }
                $plusMonth = isset($plusMonth) ? $plusMonth->addMonth() : $carbonMonth->addMonth();
            }
            // dd($fullMonthData);

            return $fullMonthData;
        }
    }
    protected function getMonthlyDataTotalForecastSales($semester, $year, $sales)
    {
        if ($semester == 1) {
            $firstDayOfMonth = "{$year}-01-01";
            $firstDayOfLastMonth = "{$year}-06-01";
            $lastDayOfMonth = date('Y-m-t', strtotime($firstDayOfLastMonth));


            $dCallPerMonth = Quotation::select(DB::raw('CONCAT(YEAR(estimated_date), "-", MONTH(estimated_date)) as date'), DB::raw('month(estimated_date) as month'), DB::raw('SUM(nett) as total'))
                ->whereBetween('estimated_date', [$firstDayOfMonth, $lastDayOfMonth])
                ->where('id_sales', $sales)
                ->where('level', '1')->where('is_primary', '1')
                ->whereIn('status', ['20', '30', '40', '60', '80', '100'])
                ->groupBy('month')
                ->orderBy('month')
                ->pluck('total', 'month');

            $fullMonthData = [];
            for ($month = 1; $month <= 6; $month++) {
                $monthKey = "{$month}";
                $carbonMonth = Carbon::parse($firstDayOfMonth);
                $formattedMonth = isset($plusMonth) ? $plusMonth->format('F') : $carbonMonth->format('F');
                $monthDays = date('t', strtotime($monthKey));
                if ($monthDays >= 4) {
                    $fullMonthData[$monthKey] = [
                        'month' => $formattedMonth,
                        'total' => isset($dCallPerMonth[$monthKey]) ? $dCallPerMonth[$monthKey] : 0,
                    ];
                }
                $plusMonth = isset($plusMonth) ? $plusMonth->addMonth() : $carbonMonth->addMonth();
            }
            // dd($fullMonthData);

            return $fullMonthData;
        } else {
            $firstDayOfMonth = "{$year}-07-01";
            $firstDayOfLastMonth = "{$year}-12-01";
            $lastDayOfMonth = date('Y-m-t', strtotime($firstDayOfLastMonth));

            $dCallPerMonth = Quotation::select(DB::raw('CONCAT(YEAR(estimated_date), "-", MONTH(estimated_date)) as date'), DB::raw('month(estimated_date) as month'), DB::raw('SUM(nett) as total'))
                ->whereBetween('estimated_date', [$firstDayOfMonth, $lastDayOfMonth])
                ->where('id_sales', $sales)
                ->where('level', '1')->where('is_primary', '1')
                ->whereIn('status', ['20', '30', '40', '60', '80'])
                ->groupBy('month')
                ->orderBy('month')
                ->pluck('total', 'month');

            $fullMonthData = [];
            for ($month = 7; $month <= 12; $month++) {
                $monthKey = "{$month}";
                $carbonMonth = Carbon::parse($firstDayOfMonth);
                $formattedMonth = isset($plusMonth) ? $plusMonth->format('F') : $carbonMonth->format('F');
                $monthDays = date('t', strtotime($monthKey));
                if ($monthDays >= 4) {
                    $fullMonthData[$monthKey] = [
                        'month' => $formattedMonth,
                        'total' => isset($dCallPerMonth[$monthKey]) ? $dCallPerMonth[$monthKey] : 0,
                    ];
                }
                $plusMonth = isset($plusMonth) ? $plusMonth->addMonth() : $carbonMonth->addMonth();
            }
            // dd($fullMonthData);

            return $fullMonthData;
        }
    }
    protected function getMonthlyDataTotalForecastProspect($semester, $year, $sales)
    {
        if ($semester == 1) {
            $firstDayOfMonth = "{$year}-01-01";
            $firstDayOfLastMonth = "{$year}-06-01";
            $lastDayOfMonth = date('Y-m-t', strtotime($firstDayOfLastMonth));

            $dCallPerMonth = Quotation::select(DB::raw('CONCAT(YEAR(estimated_date), "-", MONTH(estimated_date)) as date'), DB::raw('month(estimated_date) as month'), DB::raw('SUM(nett) as total'))
                ->whereBetween('estimated_date', [$firstDayOfMonth, $lastDayOfMonth])
                ->where('id_support', $sales)
                ->where('level', '1')->where('is_primary', '1')
                ->whereIn('status', ['20', '30', '40', '60', '80', '100'])
                ->groupBy('month')
                ->orderBy('month')
                ->pluck('total', 'month');

            $fullMonthData = [];
            for ($month = 1; $month <= 6; $month++) {
                $monthKey = "{$month}";
                $carbonMonth = Carbon::parse($firstDayOfMonth);
                $formattedMonth = isset($plusMonth) ? $plusMonth->format('F') : $carbonMonth->format('F');
                $monthDays = date('t', strtotime($monthKey));
                if ($monthDays >= 4) {
                    $fullMonthData[$monthKey] = [
                        'month' => $formattedMonth,
                        'total' => isset($dCallPerMonth[$monthKey]) ? $dCallPerMonth[$monthKey] : 0,
                    ];
                }
                $plusMonth = isset($plusMonth) ? $plusMonth->addMonth() : $carbonMonth->addMonth();
            }
            // dd($fullMonthData);

            return $fullMonthData;
        } else {
            $firstDayOfMonth = "{$year}-07-01";
            $firstDayOfLastMonth = "{$year}-12-01";
            $lastDayOfMonth = date('Y-m-t', strtotime($firstDayOfLastMonth));

            $dCallPerMonth = Quotation::select(DB::raw('CONCAT(YEAR(estimated_date), "-", MONTH(estimated_date)) as date'), DB::raw('month(estimated_date) as month'), DB::raw('SUM(nett) as total'))
                ->whereBetween('estimated_date', [$firstDayOfMonth, $lastDayOfMonth])
                ->where('id_support', $sales)
                ->where('level', '1')->where('is_primary', '1')
                ->whereIn('status', ['20', '30', '40', '60', '80'])
                ->groupBy('month')
                ->orderBy('month')
                ->pluck('total', 'month');

            $fullMonthData = [];
            for ($month = 7; $month <= 12; $month++) {
                $monthKey = "{$month}";
                $carbonMonth = Carbon::parse($firstDayOfMonth);
                $formattedMonth = isset($plusMonth) ? $plusMonth->format('F') : $carbonMonth->format('F');
                $monthDays = date('t', strtotime($monthKey));
                if ($monthDays >= 4) {
                    $fullMonthData[$monthKey] = [
                        'month' => $formattedMonth,
                        'total' => isset($dCallPerMonth[$monthKey]) ? $dCallPerMonth[$monthKey] : 0,
                    ];
                }
                $plusMonth = isset($plusMonth) ? $plusMonth->addMonth() : $carbonMonth->addMonth();
            }
            // dd($fullMonthData);

            return $fullMonthData;
        }
    }
    protected function getMonthlyDataTotalPOSales($semester, $year, $sales)
    {
        if ($semester == 1) {
            $firstDayOfMonth = "{$year}-01-01";
            $firstDayOfLastMonth = "{$year}-06-01";
            $lastDayOfMonth = date('Y-m-t', strtotime($firstDayOfLastMonth));

            $dCallPerMonth = Quotation::select(DB::raw('CONCAT(YEAR(po_date), "-", MONTH(po_date)) as date'), DB::raw('month(po_date) as month'), DB::raw('SUM(nett) as total'))
                ->whereBetween('po_date', [$firstDayOfMonth, $lastDayOfMonth])
                ->where('id_sales', $sales)
                ->where('level', '1')->where('is_primary', '1')
                ->where('status', '100')
                ->groupBy('month')
                ->orderBy('month')
                ->pluck('total', 'month');

            $unitPerMonth = UnitQuotation::select(DB::raw('month(po_received) as month'), DB::raw('SUM(total - tax_amount) as total'))
                ->whereBetween('po_received', [$firstDayOfMonth, $lastDayOfMonth])
                ->where('id_sales', $sales)
                ->where('status', 'po_received')
                ->where('is_latest', 1)
                ->groupBy('month')
                ->orderBy('month')
                ->pluck('total', 'month');

            $fullMonthData = [];
            for ($month = 1; $month <= 6; $month++) {
                $monthKey = "{$month}";
                $carbonMonth = Carbon::parse($firstDayOfMonth);
                $formattedMonth = isset($plusMonth) ? $plusMonth->format('F') : $carbonMonth->format('F');
                $monthDays = date('t', strtotime($monthKey));
                if ($monthDays >= 4) {
                    $fullMonthData[$monthKey] = [
                        'month' => $formattedMonth,
                        'total' => (isset($dCallPerMonth[$monthKey]) ? $dCallPerMonth[$monthKey] : 0) + (isset($unitPerMonth[$monthKey]) ? $unitPerMonth[$monthKey] : 0),
                    ];
                }
                $plusMonth = isset($plusMonth) ? $plusMonth->addMonth() : $carbonMonth->addMonth();
            }
            // dd($fullMonthData);

            return $fullMonthData;
        } else {
            $firstDayOfMonth = "{$year}-07-01";
            $firstDayOfLastMonth = "{$year}-12-01";
            $lastDayOfMonth = date('Y-m-t', strtotime($firstDayOfLastMonth));

            $dCallPerMonth = Quotation::select(DB::raw('CONCAT(YEAR(po_date), "-", MONTH(po_date)) as date'), DB::raw('month(po_date) as month'), DB::raw('SUM(nett) as total'))
                ->whereBetween('po_date', [$firstDayOfMonth, $lastDayOfMonth])
                ->where('id_sales', $sales)
                ->where('level', '1')->where('is_primary', '1')
                ->where('status', '100')
                ->groupBy('month')
                ->orderBy('month')
                ->pluck('total', 'month');
            // dd($dCallPerMonth);

            $unitPerMonth = UnitQuotation::select(DB::raw('month(po_received) as month'), DB::raw('SUM(total - tax_amount) as total'))
                ->whereBetween('po_received', [$firstDayOfMonth, $lastDayOfMonth])
                ->where('id_sales', $sales)
                ->where('status', 'po_received')
                ->where('is_latest', 1)
                ->groupBy('month')
                ->orderBy('month')
                ->pluck('total', 'month');

            $fullMonthData = [];
            for ($month = 7; $month <= 12; $month++) {
                $monthKey = "{$month}";
                $carbonMonth = Carbon::parse($firstDayOfMonth);
                $formattedMonth = isset($plusMonth) ? $plusMonth->format('F') : $carbonMonth->format('F');
                $monthDays = date('t', strtotime($monthKey));
                if ($monthDays >= 4) {
                    $fullMonthData[$monthKey] = [
                        'month' => $formattedMonth,
                        'total' => (isset($dCallPerMonth[$monthKey]) ? $dCallPerMonth[$monthKey] : 0) + (isset($unitPerMonth[$monthKey]) ? $unitPerMonth[$monthKey] : 0),
                    ];
                }
                $plusMonth = isset($plusMonth) ? $plusMonth->addMonth() : $carbonMonth->addMonth();
            }

            return $fullMonthData;
        }
    }
    protected function getMonthlyDataTotalPOProspect($semester, $year, $sales)
    {
        if ($semester == 1) {
            $firstDayOfMonth = "{$year}-01-01";
            $firstDayOfLastMonth = "{$year}-06-01";
            $lastDayOfMonth = date('Y-m-t', strtotime($firstDayOfLastMonth));


            $dCallPerMonth = Quotation::select(DB::raw('CONCAT(YEAR(po_date), "-", MONTH(po_date)) as date'), DB::raw('month(po_date) as month'), DB::raw('SUM(nett) as total'))
                ->whereBetween('po_date', [$firstDayOfMonth, $lastDayOfMonth])
                ->where('id_support', $sales)
                ->where('level', '1')->where('is_primary', '1')
                ->where('status', '100')
                ->groupBy('month')
                ->orderBy('month')
                ->pluck('total', 'month');

            $fullMonthData = [];
            for ($month = 1; $month <= 6; $month++) {
                $monthKey = "{$month}";
                $carbonMonth = Carbon::parse($firstDayOfMonth);
                $formattedMonth = isset($plusMonth) ? $plusMonth->format('F') : $carbonMonth->format('F');
                $monthDays = date('t', strtotime($monthKey));
                if ($monthDays >= 4) {
                    $fullMonthData[$monthKey] = [
                        'month' => $formattedMonth,
                        'total' => isset($dCallPerMonth[$monthKey]) ? $dCallPerMonth[$monthKey] : 0,
                    ];
                }
                $plusMonth = isset($plusMonth) ? $plusMonth->addMonth() : $carbonMonth->addMonth();
            }
            // dd($fullMonthData);

            return $fullMonthData;
        } else {
            $firstDayOfMonth = "{$year}-07-01";
            $firstDayOfLastMonth = "{$year}-12-01";
            $lastDayOfMonth = date('Y-m-t', strtotime($firstDayOfLastMonth));

            $dCallPerMonth = Quotation::select(DB::raw('CONCAT(YEAR(po_date), "-", MONTH(po_date)) as date'), DB::raw('month(po_date) as month'), DB::raw('SUM(nett) as total'))
                ->whereBetween('po_date', [$firstDayOfMonth, $lastDayOfMonth])
                ->where('id_support', $sales)
                ->where('level', '1')->where('is_primary', '1')
                ->where('status', '100')
                ->groupBy('month')
                ->orderBy('month')
                ->pluck('total', 'month');
            // dd($dCallPerMonth);

            $fullMonthData = [];
            for ($month = 7; $month <= 12; $month++) {
                $monthKey = "{$month}";
                $carbonMonth = Carbon::parse($firstDayOfMonth);
                $formattedMonth = isset($plusMonth) ? $plusMonth->format('F') : $carbonMonth->format('F');
                $monthDays = date('t', strtotime($monthKey));
                if ($monthDays >= 4) {
                    $fullMonthData[$monthKey] = [
                        'month' => $formattedMonth,
                        'total' => isset($dCallPerMonth[$monthKey]) ? $dCallPerMonth[$monthKey] : 0,
                    ];
                }
                $plusMonth = isset($plusMonth) ? $plusMonth->addMonth() : $carbonMonth->addMonth();
            }

            return $fullMonthData;
        }
    }

    protected function getMonthlyDataTotalQuoteNominalSales($semester, $year, $sales)
    {
        if ($semester == 1) {
            $firstDayOfMonth = "{$year}-01-01 00:00:00";
            $firstDayOfLastMonth = "{$year}-06-01";
            $lastDayOfMonth = date('Y-m-t 23:59:59', strtotime($firstDayOfLastMonth));

            $spPerMonth = Quotation::select(DB::raw('month(created_at) as month'), DB::raw('SUM(harga_total) as total'))
                ->whereBetween('created_at', [$firstDayOfMonth, $lastDayOfMonth])
                ->where('id_sales', $sales)
                ->where('level', '1')->where('is_primary', '1')
                ->groupBy('month')
                ->orderBy('month')
                ->pluck('total', 'month');

            $unitPerMonth = UnitQuotation::select(DB::raw('month(created_at) as month'), DB::raw('SUM(total) as total'))
                ->whereBetween('created_at', [$firstDayOfMonth, $lastDayOfMonth])
                ->where('id_sales', $sales)
                ->where('is_latest', 1)
                ->groupBy('month')
                ->orderBy('month')
                ->pluck('total', 'month');

            $fullMonthData = [];
            for ($month = 1; $month <= 6; $month++) {
                $monthKey = "{$month}";
                $carbonMonth = Carbon::parse("{$year}-01-01");
                $formattedMonth = isset($plusMonth) ? $plusMonth->format('F') : $carbonMonth->format('F');
                $fullMonthData[$monthKey] = [
                    'month' => $formattedMonth,
                    'total' => (float) (isset($spPerMonth[$monthKey]) ? $spPerMonth[$monthKey] : 0) + (float) (isset($unitPerMonth[$monthKey]) ? $unitPerMonth[$monthKey] : 0),
                ];
                $plusMonth = isset($plusMonth) ? $plusMonth->addMonth() : $carbonMonth->addMonth();
            }

            return $fullMonthData;
        } else {
            $firstDayOfMonth = "{$year}-07-01 00:00:00";
            $firstDayOfLastMonth = "{$year}-12-01";
            $lastDayOfMonth = date('Y-m-t 23:59:59', strtotime($firstDayOfLastMonth));

            $spPerMonth = Quotation::select(DB::raw('month(created_at) as month'), DB::raw('SUM(harga_total) as total'))
                ->whereBetween('created_at', [$firstDayOfMonth, $lastDayOfMonth])
                ->where('id_sales', $sales)
                ->where('level', '1')->where('is_primary', '1')
                ->groupBy('month')
                ->orderBy('month')
                ->pluck('total', 'month');

            $unitPerMonth = UnitQuotation::select(DB::raw('month(created_at) as month'), DB::raw('SUM(total) as total'))
                ->whereBetween('created_at', [$firstDayOfMonth, $lastDayOfMonth])
                ->where('id_sales', $sales)
                ->where('is_latest', 1)
                ->groupBy('month')
                ->orderBy('month')
                ->pluck('total', 'month');

            $fullMonthData = [];
            for ($month = 7; $month <= 12; $month++) {
                $monthKey = "{$month}";
                $carbonMonth = Carbon::parse("{$year}-07-01");
                $formattedMonth = isset($plusMonth) ? $plusMonth->format('F') : $carbonMonth->format('F');
                $fullMonthData[$monthKey] = [
                    'month' => $formattedMonth,
                    'total' => (float) (isset($spPerMonth[$monthKey]) ? $spPerMonth[$monthKey] : 0) + (float) (isset($unitPerMonth[$monthKey]) ? $unitPerMonth[$monthKey] : 0),
                ];
                $plusMonth = isset($plusMonth) ? $plusMonth->addMonth() : $carbonMonth->addMonth();
            }

            return $fullMonthData;
        }
    }
}
