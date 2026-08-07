<?php

namespace App\Http\Controllers;

// use App\Http\Requests\StoreLeadsRequest;

use App\Models\Client;
use App\Models\Comment;
use App\Models\CrmStatus;
use App\Models\Issues;
use App\Models\Machine;
use App\Models\Prospect;
use App\Models\SerialProduct;
use App\Models\User;
use App\Models\Activities;
use App\Models\Visit;
use App\Models\Quotation;
use App\Models\Pic;
use App\Models\ClientPlant;
use App\Models\Reports;
use DB;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class LeadsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $client = collect();
        $issue = Issues::get();
        $sales = User::where('role', 'sales')->get();
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
        return view('pages.sales.clients.leads.index', compact('noSaleProspect', 'comment', 'unreadComment', 'commentAdmin', 'unreadCommentAdmin', 'leveledProspect', 'client', 'sales', 'issue'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        abort(404);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rule = [
            'company' =>
                'required',

            'phone' =>
                'required',

            'ru' =>
                'required',

            'unit' =>
                'required',

            'source' =>
                'required',

            'address' =>
                'required',

            'area' =>
                'required',

            // 'namePic' =>
            //     'required',

            // 'emailPic' =>
            //     'required',

            // 'phonePic' =>
            //     'required',

            // 'position' =>
            //     'required',
        ];

        $message = [
            'company.required' => 'Field Company Wajib Diisi',
            'phone.required' => 'Field Phone Wajib Diisi',
            'ru.required' => 'Wajib Pilih Reseller atau User',
            'unit.required' => 'Field Unit Wajib Diisi',
            'source.required' => 'Field Source Wajib Diisi',
            'address.required' => 'Field Address Wajib Diisi',
            'area.required' => 'Field Area Wajib Diisi',
            // 'namePic.required'=> 'Field Nama PIC Wajib Diisi',
            // 'emailPic.required'=> 'Field Email PIC Wajib Diisi',
            // 'phonePic.required'=> 'Field Nomor PIC Wajib Diisi',
            // 'position.required'=> 'Field Posisi PIC Wajib Diisi',
        ];

        $this->validate($request, $rule, $message);
        // dd(Auth::id());
        //masukan data ke table leads(client)
        $leads = new Client;
        $leads->id_sales = Auth::id();
        $leads->id_support = NULL;
        $leads->id_issues = 1;
        $leads->company = $request->company;
        $leads->email = $request->email;
        $leads->phone = $request->phone;
        $leads->ru = $request->ru;
        $leads->unit = $request->unit;
        $leads->image = 'profile.jpg';
        $leads->source = $request->source;
        $leads->created_date = Carbon::today()->toDateString();
        $leads->role = 'Leads';
        if ($request->npwp != NULL) {
            $leads->npwp = $request->npwp;
        } else {
            $leads->npwp = NULL;
        }
        $leads->mobile = $request->mobile;
        if (in_array(Auth::id(), [1, 16, 23])) {
            $leads->info = $request->info;
        } else {
            $leads->info = "Reftech";
        }
        // if (Auth::id() == 2 || Auth::id() == 16 || Auth::id() == 23) {
        //     $leads->info = $request->info;
        // }
        $leads->address = $request->address;
        $leads->subAddress = $request->subAddress;
        $leads->week = (int) ceil(Carbon::today()->day / 7);
        $leads->area = $request->area;
        $leadsave = $leads->save();

        // masukan data ke table PIC
        // $pic = new Pic;
        // $pic->id_client = $leads->id;
        // $pic->name_pic = $request->namePic;
        // $pic->position = $request->position;
        // $pic->email_pic = $request->emailPic;
        // $pic->phone_pic = $request->phonePic;
        // $picsave = $pic->save();

        if ($leadsave) {
            return redirect('leads')->with('message', 'data telah ditambahkan');
        }
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

        $existing = Client::where('id', $id)->first();
        $leads = Client::where('id', $id)->first();
        $charge = PIC::where('id_client', $id)->get();
        $unit = SerialProduct::whereNotNull('detail')->get();
        $machines = Machine::where('id_client', $id)->get();
        $plants = ClientPlant::where('id_client', $id)->get();
        $callhis = Activities::where('id_client', $id)->whereIn('name', ['Daily Call', 'Follow Up', 'CRM'])->get();
        $visit = Activities::where('id_client', $id)->where('name', 'Visit')->get();
        $quote = Quotation::join('pic', 'pic.id', '=', 'quotation.id_pic')->where('pic.id_client', $id)->where('level', '1')->get('quotation.*');
        $sales = User::where('role', 'sales')->get();
        $issue = Issues::all();
        $service = Reports::join('pic', 'pic.id', '=', 'reports.id_pic')->where('pic.id_client', $id)->get('reports.*');
        $noSaleProspect = Prospect::whereNULL('id_sales')->whereNull('provide')->count();
        $leveledProspect = Prospect::whereNULL('level')->where('id_sales', Auth::id())->count();

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

        $unitQuotes = \App\Models\UnitQuotation::where(function($q) use ($id) {
            $q->where('id_client', $id)->orWhereHas('pic', function($p) use ($id) {
                $p->where('id_client', $id);
            });
        })->where('is_latest', 1)->get();

        foreach ($unitQuotes as $uq) {
            $unitStatusMap = [
                'po_received' => ['label' => 'Done PO', 'color' => 'success'],
                'loss' => ['label' => 'Loss', 'color' => 'danger'],
                'cancelled' => ['label' => 'Loss', 'color' => 'danger'],
                'hot_prospect' => ['label' => 'Hot Prospect', 'color' => 'warning'],
                'negotiation' => ['label' => 'Negotiation / Revisi', 'color' => 'primary'],
                'revision' => ['label' => 'Negotiation / Revisi', 'color' => 'primary'],
                'sent' => ['label' => 'Send Quotation', 'color' => 'secondary'],
                'draft' => ['label' => 'Send Quotation', 'color' => 'secondary'],
            ];
            $statusInfo = $unitStatusMap[$uq->status] ?? ['label' => $uq->status, 'color' => 'info'];

            $activityTimeline->push([
                'date' => $uq->created_at ?? Carbon::parse($uq->date),
                'title' => 'Penawaran Unit Dibuat',
                'category' => 'Quotation Unit',
                'status' => $statusInfo['label'],
                'note' => $uq->note ?? $uq->title,
                'color' => $statusInfo['color'],
                'no_quote' => $uq->no_quote,
                'url' => route('unit-quotation.show', $uq->id),
            ]);
        }

        $activityTimeline = $activityTimeline->sortByDesc('date')->values();
        $crmhis = $this->data($id);

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
        return view('pages.sales.clients.leads.detail', compact(
            'existing', 'callhis', 'quote', 'activityTimeline', 'plants', 'poYears', 'leveledProspect', 'noSaleProspect',
            'comment', 'unreadComment', 'commentAdmin', 'unreadCommentAdmin', 'sales', 'unit', 'charge', 'issue',
            'service', 'visit', 'machines', 'monthNow', 'yearsNow', 'leads', 'crmhis'
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
        abort(404);
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
            'company.required' => 'Field company Wajib Diisi',
            'email.required' => 'Email Perusahaan Wajib Diisi',
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

        //masukan data ke table leads(client)
        $leads = Client::find($id);
        $leads->company = $request->company;
        $leads->email = $request->email;
        $leads->phone = $request->phone;
        $leads->ru = $request->ru;
        $leads->unit = $request->unit;
        $leads->source = $request->source;
        $leads->npwp = $request->npwp;
        $leads->mobile = $request->mobile;
        if (Auth::user()->id == 1 || Auth::user()->id == 16) {
            $leads->info = $request->info;
        }
        $leads->address = $request->address;
        $leads->subAddress = $request->subAddress;
        $leads->area = $request->area;
        $leadsave = $leads->save();

        if ($leadsave) {
            return redirect('/leads/detail/' . $id)->with('message', 'data telah diUpdate');
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

        $leadsD = Client::find($id);
        $picD = Pic::where('id_client', $id)->get();
        $activitiesD = Activities::where('id_client', $id)->get();
        $visitD = Visit::where('id_client', $id)->get();
        $quoteD = Quotation::join('pic', 'pic.id', '=', 'quotation.id_pic')->where('pic.id_client', $id)->get();

        $delLeads = $leadsD->delete();
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

        if ($delLeads || $delActivities || $delVisits || $delQuote || $delpic) {
            return 1;
        } else {
            return 0;
        }
    }

    public function storeActionWithLeads(Request $request, $id)
    {
        // dd($request->all());
        $leads = Client::where("id", $id)->first();
        $leads->id_issues = $request->issues;
        if ($request->issues == '5') {
            $leads->role = 'Customers';
            $status = new CrmStatus;
            $status->id_client = $id;
            $status->status = 2;
            $statSave = $status->save();
        }
        $isuSave = $leads->save();

        $action = new Activities;
        $action->id_client = $id;
        if ($leads->activities != Null) {
            $action->name = "Follow Up";
        } else {
            $action->name = "Daily Call";
        }
        $action->status = $request->status;
        $action->action = $request->action;
        $action->week = $request->week;
        $action->note = $request->note;
        $action->date = \Carbon\Carbon::today();
        $action->follow_up = $request->follow_up;
        $activitiesSave = $action->save();
        if ($isuSave && $activitiesSave || $statSave) {
            if ($request->issues == '5') {
                return redirect("/existing/" . $id)->with("success", "Data telah ditambahkan");
            } else {
                return redirect("/leads/detail/" . $id)->with("success", "Data telah ditambahkan");
            }
        }
    }
    public function storeVisitWithLeads(Request $request, $id)
    {
        $leads = Client::where("id", $id)->first();
        $leads->id_issues = $request->issues;
        if ($request->issues == '5') {
            $leads->role = 'Customers';
            $status = new CrmStatus;
            $status->id_client = $id;
            $status->status = 2;
            $statSave = $status->save();
        }
        $isuSave = $leads->save();

        $action = new Activities;
        $action->id_client = $id;
        $action->name = 'Visit';
        $action->status = $request->status;
        $action->action = 'Visit';
        $action->note = $request->note;
        $action->date = \Carbon\Carbon::today();
        $action->follow_up = $request->follow_up;
        $activitiesSave = $action->save();
        if ($isuSave && $activitiesSave || $statSave) {
            if ($request->issues == '5') {
                return redirect("/existing/" . $id)->with("success", "Data telah ditambahkan");
            } else {
                return redirect("/leads/detail/" . $id)->with("success", "Data telah ditambahkan");
            }
        }
    }

    public function convertToCustomers(Request $request, $id)
    {
        $leads = Client::where("id", $id)->first();
        $leads->role = 'Customers';
        $leadsSave = $leads->save();
        $status = new CrmStatus;
        $status->id_client = $id;
        $status->status = 2;
        $statSave = $status->save();
        if ($leadsSave && $statSave) {
            return 1;
        } else {
            return 0;
        }

    }

    public function indexBySales()
    {
        // $id = 1;
        // $data = DB::table('client as c')
        //     ->select(
        //         'c.*',
        //         'p.name_pic',
        //         'i.issue',
        //         'u.name',
        //         DB::raw("DATE_FORMAT(MAX(a.date), '%d-%m-%Y') as date"),
        //         DB::raw("DATE_FORMAT(MAX(a.follow_up), '%d-%m-%Y') as follow_up"),
        //         DB::raw("MAX(a.note) as note")
        //     )
        //     ->leftJoin('issues as i', 'c.id_issues', '=', 'i.id')
        //     ->join('users as u', 'c.id_sales', '=', 'u.id')
        //     ->leftJoin('pic as p', 'c.id', '=', 'p.id_client')
        //     ->leftJoin('activities as a', 'a.id_client', '=', 'c.id')
        //     ->where('c.role', 'Leads')
        //     ->where('u.id', $id)
        //     ->groupBy(
        //         'c.id',
        //         'p.name_pic',
        //         'i.issue',
        //         'u.name'
        //     )
        //     ->orderByDesc('c.id')
        //     ->get();

        // dd($data);

        $client = Client::where("role", "Leads")->get();
        $issue = Issues::get();
        $sales = User::where('role', 'sales')->where('active', '1')->whereNotIn('id', [16, 23])->get();
        $leadsCountBySales = Client::where('role', 'Leads')
            ->select('id_sales', DB::raw('count(*) as total'))
            ->groupBy('id_sales')
            ->pluck('total', 'id_sales');
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
        return view('pages.sales.clients.leads.indexBySales', compact('noSaleProspect', 'comment', 'unreadComment', 'commentAdmin', 'unreadCommentAdmin', 'leveledProspect', 'client', 'sales', 'issue', 'leadsCountBySales'));
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
        $results = [];

        foreach ($machines as $machine) {
            $serviceReportsByMonth = [];

            // Inisialisasi array bulan
            for ($i = 1; $i <= 12; $i++) {
                $monthName = Carbon::create()->month($i)->format('F');
                $serviceReportsByMonth[$monthName] = [
                    'month' => $monthName,
                    'service' => 'no service'
                ];
            }

            // Isi array bulan dengan data dari laporan servis yang ada
            foreach ($machine->reports as $report) {
                $month = Carbon::parse($report->date)->month;
                $monthName = Carbon::create()->month($month)->format('F');
                $serviceReportsByMonth[$monthName] = [
                    'month' => $monthName,
                    'service' => $report->no_service
                ];
            }

            $results[] = [
                'machine' => $machine->brand,
                'Service' => array_values($serviceReportsByMonth)
            ];
        }

        return response()->json($results);
    }
}
