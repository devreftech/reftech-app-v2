<?php

namespace App\Http\Controllers;

use App\Models\Activities;
use App\Models\Client;
use App\Models\ClientPlant;
use App\Models\Comment;
use App\Models\CrmStatus;
use App\Models\Issues;
use App\Models\Machine;
use App\Models\Pic;
use App\Models\Prospect;
use App\Models\Quotation;
use App\Models\Reports;
use App\Models\SerialProduct;
use App\Models\Unit;
use App\Models\User;
use App\Models\Visit;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CrmController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    /**
     * Query notifikasi comment (Comment Admin + quotation/prospect comment union) yang tadinya
     * diduplikasi verbatim di index/indexBySales/indexByStatus/indexBangkrupt/ruIndex/show.
     * Pakai pola joinSub (bukan orWhere di dalam foreach) supaya query plan stabil.
     */
    protected function getCommentWidgets(): array
    {
        $myCommentsSub = Comment::select('id_status', DB::raw('MAX(created_at) as my_last_comment_at'))
            ->where('id_user', Auth::id())
            ->groupBy('id_status');

        $baseCommentsQuery = Comment::join('change_status as c', 'c.id', '=', 'comment.id_status')
            ->join('quotation as q', 'q.id', '=', 'c.id_quotation')
            ->join('users as u', 'u.id', '=', 'comment.id_user')
            ->joinSub($myCommentsSub, 'my_comments', function ($join) {
                $join->on('comment.id_status', '=', 'my_comments.id_status');
            })
            ->where('comment.id_user', '!=', Auth::id())
            ->whereRaw('comment.created_at > my_comments.my_last_comment_at');

        $commentAdmin = (clone $baseCommentsQuery)
            ->orderBy('comment.id_status')
            ->orderByDesc('comment.created_at')
            ->get(['q.id as idQ', 'comment.id as idC', 'comment.id_user', 'comment.level', 'comment.comment', 'comment.date', 'q.no_quote', 'u.name', 'u.image']);

        $unreadCommentAdmin = (clone $baseCommentsQuery)
            ->where('comment.level', '1')
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

        $comment = (clone $quotationComment)->union(clone $prospectComment)
            ->orderBy('date', 'DESC')
            ->take(5)
            ->get();

        $unreadQuotationComment = (clone $quotationComment)->where('o.level', '1');
        $unreadProspectComment = (clone $prospectComment)->where('comment.level', '1');
        $unreadComment = $unreadQuotationComment->union($unreadProspectComment)
            ->orderBy('date', 'DESC')
            ->take(5)
            ->get();

        return compact('commentAdmin', 'unreadCommentAdmin', 'comment', 'unreadComment');
    }

    public function index()
    {
        $leveledProspect = Prospect::whereNULL('level')->where('id_sales', Auth::id())->count();
        $noSaleProspect = Prospect::whereNULL('id_sales')->whereNull('provide')->count();
        ['commentAdmin' => $commentAdmin, 'unreadCommentAdmin' => $unreadCommentAdmin, 'comment' => $comment, 'unreadComment' => $unreadComment] = $this->getCommentWidgets();

        return view("pages.sales.existing.index", compact('leveledProspect', 'comment', 'unreadComment', 'commentAdmin', 'unreadCommentAdmin', 'noSaleProspect'));
    }
    public function indexBySales()
    {
        $leveledProspect = Prospect::whereNULL('level')->where('id_sales', Auth::id())->count();
        $noSaleProspect = Prospect::whereNULL('id_sales')->whereNull('provide')->count();
        $sales = User::where('role', 'sales')->where('active', '1')->whereNotIn('id', [16, 23])->get();
        $customersCountBySales = Client::where('role', 'Customers')
            ->select('id_sales', DB::raw('count(*) as total'))
            ->groupBy('id_sales')
            ->pluck('total', 'id_sales');
        ['commentAdmin' => $commentAdmin, 'unreadCommentAdmin' => $unreadCommentAdmin, 'comment' => $comment, 'unreadComment' => $unreadComment] = $this->getCommentWidgets();

        return view("pages.sales.existing.indexBySales", compact('sales','leveledProspect', 'comment', 'unreadComment', 'commentAdmin', 'unreadCommentAdmin', 'noSaleProspect', 'customersCountBySales'));
    }
    public function indexByStatus()
    {
        $leveledProspect = Prospect::whereNULL('level')->where('id_sales', Auth::id())->count();
        $noSaleProspect = Prospect::whereNULL('id_sales')->whereNull('provide')->count();
        $sales = User::where('role', 'sales')->where('active', '1')->whereNotIn('id', [16, 23])->get();
        ['commentAdmin' => $commentAdmin, 'unreadCommentAdmin' => $unreadCommentAdmin, 'comment' => $comment, 'unreadComment' => $unreadComment] = $this->getCommentWidgets();

        return view("pages.sales.existing.indexByStatus", compact('sales','leveledProspect', 'comment', 'unreadComment', 'commentAdmin', 'unreadCommentAdmin', 'noSaleProspect'));
    }
    public function indexBangkrupt()
    {
        $leveledProspect = Prospect::whereNULL('level')->where('id_sales', Auth::id())->count();
        $noSaleProspect = Prospect::whereNULL('id_sales')->whereNull('provide')->count();
        ['commentAdmin' => $commentAdmin, 'unreadCommentAdmin' => $unreadCommentAdmin, 'comment' => $comment, 'unreadComment' => $unreadComment] = $this->getCommentWidgets();

        return view("pages.sales.existing.bangkrupt", compact('leveledProspect', 'comment', 'unreadComment', 'commentAdmin', 'unreadCommentAdmin', 'noSaleProspect'));
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
        $dateNow = Carbon::now();
        $monthNow = $dateNow->month;
        $yearsNow = $dateNow->year;
        $existing = Client::find($id);
        $machines = Machine::where('id_client', $id)->with('forecastHistories')->get();
        $charge = PIC::where('id_client', $id)->get();
        $plants = ClientPlant::where('id_client', $id)->get();
        $callhis = Activities::where('id_client', $id)->whereIn('name', ['Daily Call', 'Follow Up', 'CRM'])->get();
        $visit = Activities::where('id_client', $id)->where('name', 'Visit')->get();
        $quote = Quotation::join('pic', 'pic.id', '=', 'quotation.id_pic')->where('pic.id_client', $id)->where('level', '1')->get('quotation.*');

        $poYears = Quotation::join('pic', 'pic.id', '=', 'quotation.id_pic')
            ->where('pic.id_client', $id)
            ->where('quotation.level', '1')
            ->where('quotation.is_primary', '1')
            ->where('quotation.status', '100')
            ->whereNotNull('quotation.po_date')
            ->selectRaw('DISTINCT YEAR(quotation.po_date) as year')
            ->pluck('year')
            ->push($yearsNow)
            ->unique()
            ->sortDesc()
            ->values();

        $quotationStatusMap = [
            '20' => ['label' => 'Send Quotation', 'color' => 'secondary'],
            '30' => ['label' => 'Inquiry Accepted', 'color' => 'dark'],
            '40' => ['label' => 'Progress Follow Up', 'color' => 'info'],
            '60' => ['label' => 'Negotiation / Revisi', 'color' => 'primary'],
            '80' => ['label' => 'Hot Prospect', 'color' => 'warning'],
            '100' => ['label' => 'Done PO', 'color' => 'success'],
            '0' => ['label' => 'Loss', 'color' => 'danger'],
        ];

        $activityTimeline = collect();

        foreach ($callhis as $history) {
            $activityTimeline->push([
                'date' => Carbon::parse($history->date),
                'title' => $history->action,
                'category' => $history->name,
                'status' => $history->status,
                'note' => $history->note,
                'color' => match ($history->name) {
                    'Daily Call' => 'info',
                    'Follow Up' => 'warning',
                    default => 'primary',
                },
                'no_quote' => null,
                'url' => null,
            ]);
        }

        foreach ($quote as $q) {
            $statusInfo = $quotationStatusMap[$q->status] ?? ['label' => $q->status, 'color' => 'secondary'];

            $activityTimeline->push([
                'date' => $q->created_at ?? Carbon::parse($q->estimated_date),
                'title' => 'Quotation Dibuat',
                'category' => 'Quotation',
                'status' => $statusInfo['label'],
                'note' => $q->note,
                'color' => $statusInfo['color'],
                'no_quote' => $q->no_quote,
                'url' => route('quotation.show', $q->id),
            ]);
        }

        $activityTimeline = $activityTimeline->sortByDesc('date')->values();

        $sales = User::where('role', 'sales')->get();
        $issue = Issues::all();
        $unit = SerialProduct::whereNotNull('detail')->get();
        // dd($unit);
        $crmhis = $this->data($id);
        $machinehis = $this->getServicePerMonth($id);
        // dd($yearsNow);
        $service = Reports::join('pic', 'pic.id', '=', 'reports.id_pic')->where('pic.id_client', $id)->get('reports.*');
        // dd($quote);
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
        $comment = $quotationComment->union($prospectComment)
            ->orderBy('date', 'DESC')
            ->take(5)
            ->get();
        $unreadComment = $quotationComment->union($prospectComment)
            ->orderBy('date', 'DESC')
            ->where('o.level', '1')
            ->take(5)
            ->get();
        return view(
            'pages.sales.existing.detail',
            compact(
                'existing',
                'callhis',
                'quote',
                'activityTimeline',
                'plants',
                'poYears',
                'leveledProspect',
                'noSaleProspect',
                'comment',
                'unreadComment',
                'commentAdmin',
                'unreadCommentAdmin',
                'sales',
                'unit',
                'charge',
                'issue',
                'crmhis',
                'service',
                'visit',
                'machines',
                'monthNow',
                'yearsNow'
            )
        );
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
        $rule = [
            'company' =>
                'required',

            'email' =>
                'required',

            'phone' =>
                'required',

            'unit' =>
                'required',

            'source' =>
                'required',

            'mobile' =>
                'required',

            'address' =>
                'required',

            'area' =>
                'required',

            'npwp' =>
                'required',
        ];

        $message = [
            'company.required' => 'Field Company Wajib Diisi',
            'email.required' => 'Field Email Company Wajib Diisi',
            'phone.required' => 'Field Phone Wajib Diisi',
            'ru.required' => 'Wajib Pilih Reseller atau User',
            'unit.required' => 'Field Unit Wajib Diisi',
            'source.required' => 'Field Source Wajib Diisi',
            'mobile.required' => 'Field Mobile Wajib Diisi',
            'address.required' => 'Field Address Wajib Diisi',
            'area.required' => 'Field Area Wajib Diisi',
            'npwp.required' => 'Field npwp Wajib Diisi',
        ];

        $this->validate($request, $rule, $message);

        $existings = Client::find($id);
        $existings->company = $request->company;
        $existings->email = $request->email;
        $existings->phone = $request->phone;
        $existings->ru = $request->ru;
        $existings->unit = $request->unit;
        $existings->source = $request->source;
        $existings->npwp = $request->npwp;
        $existings->mobile = $request->mobile;
        if (Auth::user()->id == 1 || Auth::user()->id == 16) {
            $existings->info = $request->info;
        }
        $existings->address = $request->address;
        $existings->subAddress = $request->subAddress;
        $existings->area = $request->area;
        $existingsave = $existings->save();

        if ($existingsave) {
            return redirect('/existing/' . $id)->with('message', 'data telah diUpdate');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $hasCompletedQuote = Quotation::join('pic', 'pic.id', '=', 'quotation.id_pic')
            ->where('pic.id_client', $id)
            ->where(function ($q) {
                $q->where('quotation.status', '100')->orWhereNotNull('quotation.po_file');
            })
            ->exists();
        if ($hasCompletedQuote) return 0;

        $existingD = Client::find($id);
        $picD = Pic::where('id_client', $id)->get();
        $activitiesD = Activities::where('id_client', $id)->get();
        $visitD = Visit::where('id_client', $id)->get();
        $quoteD = Quotation::join('pic', 'pic.id', '=', 'quotation.id_pic')->where('pic.id_client', $id)->get();

        $delExisting = $existingD->delete();
        if ($picD != NULL) {
            foreach ($picD as $pic) {
                $delpic = $pic->delete();
            }
        }
        if ($activitiesD != NULL) {
            foreach ($activitiesD as $activities) {
                $delActivities = $activities->delete();
            }
        }
        if ($visitD != NULL) {
            foreach ($visitD as $visit) {
                $delVisits = $visit->delete();
            }
        }
        if ($quoteD != NULL) {
            foreach ($quoteD as $quote) {
                $delQuote = $quote->delete();
            }
        }

        if ($delExisting || $delActivities || $delVisits || $delQuote || $delpic) {
            return 1;
        } else {
            return 0;
        }
    }

    public function storeActionWithCrm(Request $request, $id)
    {
        $action = new Activities;
        $action->id_client = $id;
        $action->name = "CRM";
        $action->status = $request->status;
        $action->week = $request->week;
        $action->action = $request->action;
        $action->note = $request->note;
        $action->date = $request->date;
        $action->follow_up = $request->follow_up;
        $activitiesSave = $action->save();
        if ($activitiesSave) {
            return redirect("/existing/" . $id)->with("success", "Data telah ditambahkan");
        }
    }

    public function updateStatusAtDropdown(Request $request, $id)
    {
        $crmStat = CrmStatus::where('id_client', $id)->first();

        if (!$crmStat) {
            $crmStat = new CrmStatus();
            $crmStat->id_client = $id;
        }

        $crmStat->status = $request->status;

        if ($crmStat->save()) {
            return response()->json(['success' => 'Status berhasil diperbarui']);
        }

        return response()->json(['error' => 'Gagal menyimpan perubahan status'], 500);
    }

    public function ruIndex()
    {
        $leveledProspect = Prospect::whereNULL('level')->where('id_sales', Auth::id())->count();
        $noSaleProspect = Prospect::whereNULL('id_sales')->whereNull('provide')->count();



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
        $comment = $quotationComment->union($prospectComment)
            ->orderBy('date', 'DESC')
            ->take(5)
            ->get();
        $unreadComment = $quotationComment->union($prospectComment)
            ->orderBy('date', 'DESC')
            ->where('o.level', '1')
            ->take(5)
            ->get();

        return view("pages.sales.clients.ru.index", compact('leveledProspect', 'comment', 'unreadComment', 'commentAdmin', 'unreadCommentAdmin', 'noSaleProspect'));
    }
    public function detailPerYear($id)
    {
        $dateNow = Carbon::now();
        $yearNow = $dateNow->year;
        $client = Client::find($id);
        $picIds = Pic::where('id_client', $id)->pluck('id'); // hanya ambil ID-nya
        $quotePO = Quotation::whereIn('id_pic', $picIds)
            ->whereYear('po_date', 2025)
            ->where('status', '100')
            ->where('is_primary', '1')
            ->where('level', '1')
            ->orderBy('po_date')
            ->get();
        return view(
            'pages.sales.existing.yearly',
            compact('client', 'quotePO')
        );
    }
    protected function getDataPerMonth()
    {
        // Misalkan Anda ingin mengambil data untuk semester pertama tahun 2024
        $startSemester = Carbon::parse('2024-01-01');
        $endSemester = $startSemester->copy()->addMonths(6)->subDay(); // Akhir semester adalah 6 bulan setelah mulai

        $dataPerSixMonths = [];

        $startMonth = $startSemester->copy();
        $endMonth = $endSemester->copy();

        // Loop untuk setiap bulan dalam periode enam bulan
        while ($startMonth->lte($endMonth)) {
            $startDate = $startMonth->copy()->startOfMonth();
            $endDate = $startMonth->copy()->endOfMonth();

            // Penanganan untuk bulan-bulan dengan sedikit hari
            if ($startDate->daysInMonth < 4) {
                // Jika hari dalam bulan kurang dari 4, langsung lanjut ke bulan berikutnya
                $startMonth->addMonth();
                continue;
            }

            $dataPerMonth = [];

            // Ambil data untuk setiap minggu dalam bulan ini
            $currentDate = $startDate->copy();
            while ($currentDate->lte($endDate)) {
                $startWeek = $currentDate->copy()->startOfWeek();
                $endWeek = $currentDate->copy()->endOfWeek();

                // Hitung jumlah hari dalam minggu ini
                $daysInWeek = $endWeek->diffInDays($startWeek) + 1;

                // Jika jumlah hari dalam minggu lebih dari 3, pertimbangkan sebagai satu minggu
                if ($daysInWeek >= 4) {
                    $data = Activities::whereBetween('created_at', [$startWeek, $endWeek])->get();

                    if ($data->isNotEmpty()) {
                        // Jika ada data, tambahkan ke array dataPerMonth
                        $dataPerMonth[] = [
                            'week_start' => $startWeek->format('Y-m-d'),
                            'week_end' => $endWeek->format('Y-m-d'),
                            'data' => $data->pluck('created_at')->toArray(),
                        ];
                    } else {
                        // Jika tidak ada data, tambahkan tanda "-"
                        $dataPerMonth[] = [
                            'week_start' => $startWeek->format('Y-m-d'),
                            'week_end' => $endWeek->format('Y-m-d'),
                            'data' => '-',
                        ];
                    }
                }

                // Pindahkan ke minggu berikutnya
                $currentDate->addWeek();
            }

            $dataPerSixMonths[] = [
                'month' => $startMonth->format('F Y'),
                'data_per_month' => $dataPerMonth,
            ];

            // Pindahkan ke bulan berikutnya
            $startMonth->addMonth();
        }

        return $dataPerSixMonths;
    }
    protected function data($id)
    {
        $currentMonth = date('n'); // 'n' returns the month without leading zeros
        $currentYear = date('Y');

        // Determine the start date based on the current month
        if ($currentMonth >= 1 && $currentMonth <= 6) {
            // January to June
            $startSemester = Carbon::parse($currentYear . '-01-01'); // 1 January of the current year
        } else {
            // July to December
            $startSemester = Carbon::parse($currentYear . '-07-01'); // 1 July of the current year
        }
        // Misalkan semester berlangsung selama 16 minggu
        $numOfWeeks = 26;

        $dataPerMonth = [];
        $dataPerSixMonth = [];

        for ($week = 0; $week < $numOfWeeks; $week++) {
            $currentWeek = $startSemester->copy()->addWeeks($week);
            $startDate = $currentWeek->copy()->startOfWeek();
            $endDate = $currentWeek->copy()->endOfWeek();

            // Hitung jumlah hari dalam minggu ini
            $daysInWeek = $endDate->diffInDays($startDate) + 1;

            // Jika jumlah hari dalam minggu lebih dari 4, pertimbangkan sebagai satu minggu
            if ($daysInWeek > 4) {
                $month = $currentWeek->format('F Y');
                $data = Activities::whereBetween('date', [$startDate, $endDate])->where('id_client', $id)->get();
                if ($data->isNotEmpty()) {
                    // dd($data);
                    // Jika ada data, tambahkan ke array dataPerMonth
                    $dataPerMonth[$month][] = [
                        'week_start' => $startDate->format('Y-m-d'),
                        'week_end' => $endDate->format('Y-m-d'),
                        'data' => $data->map(function ($item) {
                            $carbonDate = Carbon::parse($item->date);
                            return $carbonDate->format('m-d');
                        }),
                        'note' => $data->map(function ($item) {
                            return $item->note;
                        }),
                    ];
                } else {
                    // Jika tidak ada data, tambahkan tanda "-"
                    $dataPerMonth[$month][] = [
                        'week_start' => $startDate->format('Y-m-d'),
                        'week_end' => $endDate->format('Y-m-d'),
                        'data' => '-',
                        'note' => '-',
                    ];
                }
            }
        }
        $dataPerSixMonth[] = [
            'month' => $month,
            'data' => $dataPerMonth,
        ];

        return $dataPerMonth;
    }
    public function getServicePerMonth($id)
    {

        $machines = Machine::where('id_client', $id)->with('reports')->get();
        // dd($machines);
        $results = [];

        // Fungsi untuk mengubah angka bulan menjadi nama bulan menggunakan Carbon
        function getMonthName($monthNumber)
        {
            return Carbon::create()->month($monthNumber)->format('F'); // Menghasilkan nama bulan penuh seperti January, February, dll.
        }

        foreach ($machines as $machine) {
            $serviceReportsByMonth = [];

            // Inisialisasi array bulan
            for ($i = 1; $i <= 12; $i++) {
                $serviceReportsByMonth[getMonthName($i)] = [
                    'month' => getMonthName($i),
                    'service' => 'no service'
                ];
            }

            // Isi array bulan dengan data dari laporan servis yang ada
            foreach ($machine->reports as $report) {
                $month = Carbon::parse($report->date)->month; // Angka bulan (1-12)
                $monthName = getMonthName($month);
                $serviceReportsByMonth[$monthName] = [
                    'month' => $monthName,
                    'service' => $report->no_service // Ganti dengan field yang sesuai
                ];
            }

            $results[] = [
                'machine' => $machine->brand, // Ganti dengan field yang sesuai
                'Service' => array_values($serviceReportsByMonth)
            ];
        }

        return response()->json($results);
    }
}
