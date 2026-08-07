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
use Illuminate\Support\Facades\Cache;
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

        $sales = collect();
        $sorted = collect();

        // Ranking sales & $sales list ini cuma dipakai oleh dashboard Sales, Admin, dan Sales Manager.
        // Role lain (Accounting, Finance Manager, Logistic, Support, dst) tidak menerima variabel ini,
        // jadi query berat di bawah ini dilewati supaya dashboard mereka tidak ikut menanggung bebannya.
        if (in_array(Auth::user()->role, ['Sales', 'Admin', 'Sales Manager'], true)) {
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
        }

        if (Auth::user()->role == 'Sales') {
            $salesService = new \App\Services\Dashboard\SalesDashboardService();
            $salesData = $salesService->getDashboardData(Auth::id(), $yearNow, $monthNow, $dateNow, $sorted, $sales, $notulens);

            return view("pages.sales.dashboard", $salesData);
        } elseif (Auth::user()->role == 'Support') {
            $supportService = new \App\Services\Dashboard\SupportDashboardService();
            $supportData = $supportService->getDashboardData($notulens, $yearNow, $monthNow);

            return view("pages.sales.dashboard", $supportData);

        } elseif (Auth::user()->role == 'Admin') {
            $adminService = new \App\Services\Dashboard\AdminDashboardService();
            $adminData = $adminService->getDashboardData($sorted, $sales, $notulens, $yearNow, $monthNow, $dateNow);

            return view("pages.sales.dashboard", $adminData);
        } elseif (Auth::user()->role == 'Accounting') {
            $accountingService = new \App\Services\Dashboard\AccountingDashboardService();
            $accountingData = $accountingService->getDashboardData($notulens);

            return view("pages.sales.dashboard", $accountingData);
        } elseif (Auth::user()->role == 'Finance Manager') {
            $financeView = request()->query('view', 'finance');
            if (!in_array($financeView, ['finance', 'accounting', 'logistic', 'workshop'], true)) {
                $financeView = 'finance';
            }

            $financeExtraData = match ($financeView) {
                'accounting' => (new \App\Services\Dashboard\AccountingDashboardService())->getAccountingDashboardData(),
                'logistic' => (new \App\Services\Dashboard\LogisticDashboardService())->getLogisticDashboardData(),
                'workshop' => (new \App\Services\Dashboard\WorkshopDashboardService())->getWorkshopDashboardData(),
                default => (new \App\Services\Dashboard\FinanceDashboardService())->getFinanceDashboardData(),
            };

            $financeData = array_merge(
                compact('notulens', 'financeView'),
                $financeExtraData
            );

            return view("pages.sales.dashboard", $financeData);
        } elseif (Auth::user()->role == 'Sales Manager') {
            $smService = new \App\Services\Dashboard\SalesManagerDashboardService();
            $smData = $smService->getDashboardData($sorted, $sales, $notulens);

            return view("pages.sales.dashboard", $smData);
        } elseif (Auth::user()->role == 'Logistic') {
            $logisticService = new \App\Services\Dashboard\LogisticDashboardService();
            $logisticData = $logisticService->getDashboardData($notulens);

            return view("pages.sales.dashboard", $logisticData);
        } else {
            $defaultService = new \App\Services\Dashboard\DefaultDashboardService();
            $defaultData = $defaultService->getDashboardData($notulens);

            return view("pages.sales.dashboard", $defaultData);
        }

        // dd($leveledProspect);
    }

    public function ajaxView(\Illuminate\Http\Request $request)
    {
        $dateNow = Carbon::now();
        $monthNow = $dateNow->month;
        $yearNow = $dateNow->year;
        $notulens = Notulen::join('mention_notulen as m', 'm.id_notulen', '=', 'notulen.id')
            ->join('users as u', 'm.id_mention', '=', 'u.id')
            ->get(['notulen.*', 'u.name', 'm.level']);

        $salesOrder = [4, 3, 2, 1, 32, 41, 16, 22];
        $sales = User::where('role', 'Sales')
            ->where('active', '1')
            ->with('latestTarget')
            ->with('latestRole')
            ->get()
            ->sortBy(function ($sale) use ($salesOrder) {
                $pos = array_search($sale->id, $salesOrder);
                return $pos === false ? 999 : $pos;
            })
            ->values();

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

            if (in_array($sale->id, $teamIds)) {
                $teamTotalPO += $poTotalPricePerSales;
                $teamTotalTarget += $targetPerSales;
                continue;
            }

            $percentage = $targetPerSales > 0 ? round(($poTotalPricePerSales / $targetPerSales) * 100, 2) : 0;
            $result[] = [
                'name' => $sale->name,
                'area' => $sale->latestRole->area ?? '-',
                'percentage' => $percentage,
            ];
        }

        $teamPercentage = $teamTotalTarget > 0 ? round(($teamTotalPO / $teamTotalTarget) * 100, 2) : 0;
        $result[] = [
            'name' => 'Team Ecommerce',
            'area' => 'Online',
            'percentage' => $teamPercentage,
        ];

        $sorted = collect($result)->sortByDesc('percentage')->values();

        $adminService = new \App\Services\Dashboard\AdminDashboardService();
        $adminData = $adminService->getDashboardData($sorted, $sales, $notulens, $yearNow, $monthNow, $dateNow);

        $view = $adminData['adminView'] ?? 'sales';
        $html = view('pages.sales.dashboard_view_content', $adminData)->render();

        return response()->json([
            'status' => 'success',
            'view' => $view,
            'html' => $html
        ]);
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
        $totalProspect = Quotation::join('prospect as p', 'quotation.id', '=', 'p.id_quotation')
            ->whereNotNull('id_quotation')
            ->whereYear('estimated_date', $yearNow)
            ->whereMonth('estimated_date', $monthNow)
            ->where('quotation.id_sales', $sales)
            ->whereIn('status', ['80', '90'])
            ->where('quotation.level', '1')
            ->where('is_primary', '1')
            ->sum('nett');
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

        if (!$target || $target->total == 0) {
            return 0;
        }

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
        if (!$target || $target->leads == 0) {
            return 0;
        }
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
        if (!$target || $target->dc == 0) {
            return 0;
        }
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
        if (!$target || $target->quote == 0) {
            return 0;
        }
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
        if ($allProspect == 0) {
            return 0;
        }
        $prospectTarget = ($filteredProspect / $allProspect) * 100;
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
        $filteredProspectPO = Quotation::whereYear('po_date', $yearNow)->whereMonth('po_date', $monthNow)->where('id_support', $support)->where('status', '100')->where('level', '1')->where('is_primary', '1')->count()
            + UnitQuotation::where('status', 'po_received')->where('is_latest', 1)->whereYear('po_received', $yearNow)->whereMonth('po_received', $monthNow)->where('id_support', $support)->count();
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
        $totalProspectPO = Quotation::whereYear('po_date', $yearNow)->whereMonth('po_date', $monthNow)->where('id_support', $support)->where('status', '100')->where('level', '1')->where('is_primary', '1')->sum('nett')
            + UnitQuotation::where('status', 'po_received')->where('is_latest', 1)->whereYear('po_received', $yearNow)->whereMonth('po_received', $monthNow)->where('id_support', $support)->sum(DB::raw('total - tax_amount'));
        $formattedPO = number_format($totalProspectPO, 0, ",", ".");
        return $formattedPO;
    }
}
