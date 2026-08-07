<?php

namespace App\Http\Controllers;

use App\Models\Activities;
use App\Models\Client;
use App\Models\Comment;
use App\Models\Prospect;
use App\Models\Quotation;
use App\Models\Target;
use App\Models\UnitQuotation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReportsController extends Controller
{
    public function index(Request $request)
    {
        if (Auth::user()->role == 'Support') {
            return redirect()->route('reports.support', ['year' => now()->year, 'month' => now()->month]);
        }

        $monthNow = (int) $request->query('month', Carbon::now()->month);
        $yearNow = (int) $request->query('year', Carbon::now()->year);

        // Calculate previous & next month/year
        $currentCarbon = Carbon::createFromDate($yearNow, $monthNow, 1);
        $prevCarbon = (clone $currentCarbon)->subMonth();
        $nextCarbon = (clone $currentCarbon)->addMonth();

        $prevMonth = $prevCarbon->month;
        $prevYear = $prevCarbon->year;
        $nextMonth = $nextCarbon->month;
        $nextYear = $nextCarbon->year;

        // Dynamic Years List
        $startYear = 2022;
        $endYear = max(Carbon::now()->year + 1, $yearNow);
        $yearsList = range($endYear, $startYear);

        $dataDc = $this->getWeekDataDC($monthNow, $yearNow);
        $dataCRM = $this->getWeekDataCRM($monthNow, $yearNow);
        $dataVisit = $this->getWeekDataVisit($monthNow, $yearNow);
        $dataQuote = $this->getWeekDataQuote($monthNow, $yearNow);
        $dataPo = $this->getWeekDataPo($monthNow, $yearNow);
        $dataLeads = $this->getWeekDataLeads($monthNow, $yearNow);
        $target = Target::where('id_sales', Auth::user()->id)->first();
        $targetCrm = Client::where('role', 'Customers')->where('id_sales', Auth::user()->id)->count();

        // sales
        $totalDC = Activities::rightJoin('client', 'client.id', '=', 'activities.id_client')->whereMonth('date', $monthNow)->whereYear('date', $yearNow)->where('status', 'Responded')->whereIn('name', ['Daily Call', 'Follow Up'])->where('client.id_sales', Auth::user()->id)->count();
        $totalCRM = Activities::rightJoin('client', 'client.id', '=', 'activities.id_client')->whereMonth('date', $monthNow)->whereYear('date', $yearNow)->where('status', 'Responded')->where('name', 'CRM')->where('client.id_sales', Auth::user()->id)->count();
        $totalVisit = Activities::rightJoin('client', 'client.id', '=', 'activities.id_client')->whereMonth('date', $monthNow)->whereYear('date', $yearNow)->where('status', 'Responded')->where('name', 'Visit')->where('client.id_sales', Auth::user()->id)->count();
        $totalQuote = Quotation::whereIn('status', ['20', '30', '40', '60', '80'])->whereMonth('estimated_date', $monthNow)->whereYear('estimated_date', $yearNow)->where('id_sales', Auth::user()->id)->where('level', '1')->where('is_primary', '1')->count();
        $totalPO = Quotation::where('status', '100')->whereMonth('po_date', $monthNow)->where('id_sales', Auth::user()->id)->where('level', '1')->where('is_primary', '1')->count();
        $totalLeads = Client::whereMonth('created_at', $monthNow)->whereYear('created_at', $yearNow)->where('id_sales', Auth::user()->id)->count();
        $amountSales = Quotation::whereMonth('po_date', $monthNow)->whereYear('po_date', $yearNow)->where('status', '100')->where('id_sales', Auth::user()->id)->where('level', '1')->where('is_primary', '1')->sum('nett');
        $amountProspect = Quotation::whereMonth('estimated_date', $monthNow)->whereYear('estimated_date', $yearNow)->where('status', '80')->where('id_sales', Auth::user()->id)->where('level', '1')->where('is_primary', '1')->sum('nett');
        $amountQuote = Quotation::whereMonth('estimated_date', $monthNow)->whereYear('estimated_date', $yearNow)->whereIn('status', ['20', '30', '40', '60', '80'])->where('id_sales', Auth::user()->id)->where('level', '1')->where('is_primary', '1')->sum('nett');
        $quotation = Quotation::where('status', '100')->whereMonth('po_date', $monthNow)->whereYear('po_date', $yearNow)->where('id_sales', Auth::user()->id)->where('level', '1')->where('is_primary', '1')->get();

        // Unit quotation PO bulan ini — nominal tanpa PPN = total - tax_amount
        $unitQuotationPO = UnitQuotation::with(['client', 'statusHistory' => fn($q) => $q->where('status', 'po_received')->latest()])
            ->where('id_sales', Auth::id())
            ->where('status', 'po_received')
            ->where('is_latest', 1)
            ->whereYear('po_received', $yearNow)
            ->whereMonth('po_received', $monthNow)
            ->get();

        $amountSalesUnit = $unitQuotationPO->sum(fn($uq) => $uq->total - $uq->tax_amount);
        $amountSales    += $amountSalesUnit;
        $totalPO        += $unitQuotationPO->count();
        $noSaleProspect = Prospect::whereNULL('id_sales')->whereNull('provide')->count();
        $leveledProspect = Prospect::whereNULL('level')->where('id_sales', Auth::id())->count();


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
        return view("pages.sales.report.index", compact("targetCrm", "noSaleProspect", 'comment', 'unreadComment', 'commentAdmin', 'unreadCommentAdmin', 'leveledProspect', "quotation", "unitQuotationPO", "dataDc", "dataQuote", "dataPo", "dataLeads", "target", "dataCRM", "dataVisit", "totalDC", "totalCRM", "totalQuote", "totalVisit", "totalPO", "totalLeads", "amountSales", "amountQuote", "amountProspect", "monthNow", "yearNow", "prevMonth", "prevYear", "nextMonth", "nextYear", "yearsList"));
    }

    protected function getWeekDataDC($monthNow, $yearNow)
    {
        $firstDayOfMonth = sprintf('%04d-%02d-01', $yearNow, $monthNow);
        $lastDayOfMonth = date('Y-m-t', strtotime($firstDayOfMonth));

        $firstDayOfWeek = date('N', strtotime($firstDayOfMonth));
        $weekEnd = date('W', strtotime($firstDayOfMonth));
        $endWeek = date('W', strtotime($lastDayOfMonth));
        $weekStart = $firstDayOfWeek > 1 ? $weekEnd + 1 : $weekEnd;

        $dCallPerWeek = Activities::select(DB::raw('CONCAT(YEAR(date), "-", MONTH(date), "-W", WEEK(date, 4)) as date'), DB::raw('WEEK(date, 4) as week'), DB::raw('COUNT(*) as total'))
            ->join('client as c', 'activities.id_client', '=', 'c.id')
            ->join('users as u', 'c.id_sales', '=', 'u.id')
            ->whereBetween('date', [$firstDayOfMonth, $lastDayOfMonth])
            ->where('id_sales', Auth::user()->id)
            ->whereIn('activities.name', ['Daily Call', 'Follow Up'])
            ->where('status', 'Responded')
            ->groupBy('week')
            ->orderBy('week')
            ->pluck('total', 'week');

        $fullMonthData = [];
        for ($week = $weekStart; $week <= $endWeek; $week++) {
            $weekKey = "{$week}";

            $weekDays = date('t', strtotime($weekKey));
            if ($weekDays >= 4) {
                $fullMonthData[$weekKey] = [
                    'week' => $weekKey,
                    'total' => isset($dCallPerWeek[$weekKey]) ? $dCallPerWeek[$weekKey] : 0,
                ];
            }
        }

        return $fullMonthData;
    }

    protected function getWeekDataCRM($monthNow, $yearNow)
    {
        $firstDayOfMonth = sprintf('%04d-%02d-01', $yearNow, $monthNow);
        $lastDayOfMonth = date('Y-m-t', strtotime($firstDayOfMonth));

        $firstDayOfWeek = date('N', strtotime($firstDayOfMonth));
        $weekEnd = date('W', strtotime($firstDayOfMonth));
        $endWeek = date('W', strtotime($lastDayOfMonth));
        $weekStart = $firstDayOfWeek > 1 ? $weekEnd + 1 : $weekEnd;

        $dCallPerWeek = Activities::select(DB::raw('CONCAT(YEAR(date), "-", MONTH(date), "-W", WEEK(date, 4)) as date'), DB::raw('WEEK(date, 4) as week'), DB::raw('COUNT(*) as total'))
            ->join('client as c', 'activities.id_client', '=', 'c.id')
            ->join('users as u', 'c.id_sales', '=', 'u.id')
            ->whereBetween('date', [$firstDayOfMonth, $lastDayOfMonth])
            ->where('id_sales', Auth::user()->id)
            ->where('activities.name', 'CRM')
            ->where('status', 'Responded')
            ->groupBy('week')
            ->orderBy('week')
            ->pluck('total', 'week');

        $fullMonthData = [];
        for ($week = $weekStart; $week <= $endWeek; $week++) {
            $weekKey = "{$week}";

            $weekDays = date('t', strtotime($weekKey));
            if ($weekDays >= 4) {
                $fullMonthData[$weekKey] = [
                    'week' => $weekKey,
                    'total' => isset($dCallPerWeek[$weekKey]) ? $dCallPerWeek[$weekKey] : 0,
                ];
            }
        }

        return $fullMonthData;
    }
    protected function getWeekDataVisit($monthNow, $yearNow)
    {
        $firstDayOfMonth = sprintf('%04d-%02d-01', $yearNow, $monthNow);
        $lastDayOfMonth = date('Y-m-t', strtotime($firstDayOfMonth));

        $firstDayOfWeek = date('N', strtotime($firstDayOfMonth));
        $weekEnd = date('W', strtotime($firstDayOfMonth));
        $endWeek = date('W', strtotime($lastDayOfMonth));
        $weekStart = $firstDayOfWeek > 1 ? $weekEnd + 1 : $weekEnd;

        $dCallPerWeek = Activities::select(DB::raw('CONCAT(YEAR(date), "-", MONTH(date), "-W", WEEK(date, 4)) as date'), DB::raw('WEEK(date, 4) as week'), DB::raw('COUNT(*) as total'))
            ->join('client as c', 'activities.id_client', '=', 'c.id')
            ->join('users as u', 'c.id_sales', '=', 'u.id')
            ->whereBetween('date', [$firstDayOfMonth, $lastDayOfMonth])
            ->where('id_sales', Auth::user()->id)
            ->where('activities.name', 'Visit')
            ->where('status', 'Responded')
            ->groupBy('week')
            ->orderBy('week')
            ->pluck('total', 'week');

        $fullMonthData = [];
        for ($week = $weekStart; $week <= $endWeek; $week++) {
            $weekKey = "{$week}";

            $weekDays = date('t', strtotime($weekKey));
            if ($weekDays >= 4) {
                $fullMonthData[$weekKey] = [
                    'week' => $weekKey,
                    'total' => isset($dCallPerWeek[$weekKey]) ? $dCallPerWeek[$weekKey] : 0,
                ];
            }
        }

        return $fullMonthData;
    }
    protected function getWeekDataQuote($monthNow, $yearNow)
    {
        $firstDayOfMonth = sprintf('%04d-%02d-01', $yearNow, $monthNow);
        $lastDayOfMonth = date('Y-m-t', strtotime($firstDayOfMonth));

        $firstDayOfWeek = date('N', strtotime($firstDayOfMonth));
        $weekEnd = date('W', strtotime($firstDayOfMonth));
        $endWeek = date('W', strtotime($lastDayOfMonth));
        $weekStart = $firstDayOfWeek > 1 ? $weekEnd + 1 : $weekEnd;

        $dCallPerWeek = Quotation::select(DB::raw('CONCAT(YEAR(estimated_date), "-", MONTH(estimated_date), "-W", WEEK(estimated_date, 4)) as estimated_date'), DB::raw('WEEK(estimated_date, 4) as week'), DB::raw('COUNT(*) as total'))
            ->whereBetween('estimated_date', [$firstDayOfMonth, $lastDayOfMonth])
            ->where('id_sales', Auth::user()->id)
            ->where('level', '1')->where('is_primary', '1')
            ->groupBy('week')
            ->orderBy('week')
            ->pluck('total', 'week');
        $fullMonthData = [];
        for ($week = $weekStart; $week <= $endWeek; $week++) {
            $weekKey = "{$week}";

            $weekDays = date('t', strtotime($weekKey));
            if ($weekDays >= 4) {
                $fullMonthData[$weekKey] = [
                    'week' => $weekKey,
                    'total' => isset($dCallPerWeek[$weekKey]) ? $dCallPerWeek[$weekKey] : 0,
                ];
            }
        }
        return $fullMonthData;
    }
    protected function getWeekDataPo($monthNow, $yearNow)
    {
        $firstDayOfMonth = sprintf('%04d-%02d-01', $yearNow, $monthNow);
        $lastDayOfMonth = date('Y-m-t', strtotime($firstDayOfMonth));

        $firstDayOfWeek = date('N', strtotime($firstDayOfMonth));
        $weekEnd = date('W', strtotime($firstDayOfMonth));
        $endWeek = date('W', strtotime($lastDayOfMonth));
        $weekStart = $firstDayOfWeek > 1 ? $weekEnd + 1 : $weekEnd;

        $dCallPerWeek = Quotation::select(DB::raw('CONCAT(YEAR(po_date), "-", MONTH(po_date), "-W", WEEK(po_date, 4)) as po_date'), DB::raw('WEEK(po_date, 4) as week'), DB::raw('COUNT(*) as total'))
            ->whereBetween('po_date', [$firstDayOfMonth, $lastDayOfMonth])
            ->where('id_sales', Auth::user()->id)
            ->where('level', '1')->where('is_primary', '1')
            ->where('status', '100')
            ->groupBy('week')
            ->orderBy('week')
            ->pluck('total', 'week');

        $unitPerWeek = UnitQuotation::select(DB::raw('WEEK(po_received, 4) as week'), DB::raw('COUNT(*) as total'))
            ->whereBetween('po_received', [$firstDayOfMonth, $lastDayOfMonth])
            ->where('id_sales', Auth::user()->id)
            ->where('status', 'po_received')
            ->where('is_latest', 1)
            ->groupBy('week')
            ->orderBy('week')
            ->pluck('total', 'week');

        $fullMonthData = [];
        for ($week = $weekStart; $week <= $endWeek; $week++) {
            $weekKey = "{$week}";

            $weekDays = date('t', strtotime($weekKey));
            if ($weekDays >= 4) {
                $fullMonthData[$weekKey] = [
                    'week' => $weekKey,
                    'total' => (isset($dCallPerWeek[$weekKey]) ? $dCallPerWeek[$weekKey] : 0) + (isset($unitPerWeek[$weekKey]) ? $unitPerWeek[$weekKey] : 0),
                ];
            }
        }
        return $fullMonthData;
    }
    protected function getWeekDataLeads($monthNow, $yearNow)
    {
        $firstDayOfMonth = sprintf('%04d-%02d-01', $yearNow, $monthNow);
        $lastDayOfMonth = date('Y-m-t', strtotime($firstDayOfMonth));

        $firstDayOfWeek = date('N', strtotime($firstDayOfMonth));
        $weekEnd = date('W', strtotime($firstDayOfMonth));
        $endWeek = date('W', strtotime($lastDayOfMonth));
        $weekStart = $firstDayOfWeek > 1 ? $weekEnd + 1 : $weekEnd;

        $dCallPerWeek = Client::select(DB::raw('CONCAT(YEAR(created_at), "-", MONTH(created_at), "-W", WEEK(created_at, 4)) as created_at'), DB::raw('WEEK(created_at, 4) as week'), DB::raw('COUNT(*) as total'))
            ->whereBetween('created_at', [$firstDayOfMonth, $lastDayOfMonth])
            ->where('id_sales', Auth::user()->id)
            ->groupBy('week')
            ->orderBy('week')
            ->pluck('total', 'week');
        $fullMonthData = [];
        for ($week = $weekStart; $week <= $endWeek; $week++) {
            $weekKey = "{$week}";

            $weekDays = date('t', strtotime($weekKey));
            if ($weekDays >= 4) {
                $fullMonthData[$weekKey] = [
                    'week' => $weekKey,
                    'total' => isset($dCallPerWeek[$weekKey]) ? $dCallPerWeek[$weekKey] : 0,
                ];
            }
        }
        return $fullMonthData;
    }
}
