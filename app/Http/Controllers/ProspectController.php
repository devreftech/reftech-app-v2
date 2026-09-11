<?php

namespace App\Http\Controllers;

use App\Models\ChangeStatus;
use App\Models\Client;
use App\Models\Comment;
use App\Models\DetailQuotation;
use App\Models\MentionComment;
use App\Models\Pic;
use App\Models\Product;
use App\Models\Prospect;
use App\Models\ProspectNotification;
use App\Models\Quotation;
use App\Models\Termncon;
use App\Models\UnitQuotation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ProspectController extends Controller
{
    /**
     * User id penerima pop-up notifikasi "Prospect Baru".
     * Sengaja dibatasi ke orang tertentu (bukan per-role) atas permintaan:
     *   5  => Angel Irene
     *   7  => Atmin Development
     *   38 => Regita Dwi
     */
    public const PROSPECT_NOTIF_RECIPIENT_IDS = [5, 7, 38];

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Smart Quote (unit_quotation) ikut dihitung di pipeline support: id_support
        // turun dari prospect/client, nilai pakai basis pre-PPN & pre-fee (analog
        // kolom `nett` di quotation lama), status dipetakan:
        //   forecast = belum PO & belum loss | prospek tinggi = hot_prospect/negotiation
        $sqNett = 'total - IFNULL(tax_amount, 0) - IFNULL(fee, 0)';
        $sqBase       = UnitQuotation::where('is_latest', 1)->where('id_support', Auth::user()->id);
        $sqBaseAdmin  = UnitQuotation::where('is_latest', 1)->whereNotNull('id_support');

        $quotation = Quotation::where('id_support', Auth::user()->id)->where('level', '1')->where('is_primary', '1')->get();
        $forecast = Quotation::where('id_support', Auth::user()->id)->where('level', '1')->where('is_primary', '1')->whereIn('status', ['20', '30', '40', '60', '80'])->sum('nett')
            + (clone $sqBase)->whereNotIn('status', ['po_received', 'loss'])->sum(DB::raw($sqNett));
        $prospect = Quotation::where('id_support', Auth::user()->id)->where('level', '1')->where('is_primary', '1')->where('status', '80')->sum('nett')
            + (clone $sqBase)->whereIn('status', ['hot_prospect', 'negotiation'])->sum(DB::raw($sqNett));
        $po = Quotation::where('id_support', Auth::user()->id)->where('status', '100')->where('level', '1')->where('is_primary', '1')->sum('nett')
            + (clone $sqBase)->where('status', 'po_received')->sum(DB::raw($sqNett));
        $loss = Quotation::where('id_support', Auth::user()->id)->where('status', '0')->where('level', '1')->where('is_primary', '1')->sum('nett')
            + (clone $sqBase)->where('status', 'loss')->sum(DB::raw($sqNett));
        $quotationAdmin = Quotation::whereNotNull('id_support')->where('level', '1')->get();
        $forecastAdmin = Quotation::whereIn('status', ['20', '30', '40', '60', '80'])->whereNotNull('id_support')->where('level', '1')->where('is_primary', '1')->sum('nett')
            + (clone $sqBaseAdmin)->whereNotIn('status', ['po_received', 'loss'])->sum(DB::raw($sqNett));
        $prospectAdmin = Quotation::where('status', '80')->whereNotNull('id_support')->where('level', '1')->where('is_primary', '1')->sum('nett')
            + (clone $sqBaseAdmin)->whereIn('status', ['hot_prospect', 'negotiation'])->sum(DB::raw($sqNett));
        $poAdmin = Quotation::where('status', '100')->whereNotNull('id_support')->where('level', '1')->where('is_primary', '1')->sum('nett')
            + (clone $sqBaseAdmin)->where('status', 'po_received')->sum(DB::raw($sqNett));
        $lossAdmin = Quotation::where('status', '0')->whereNotNull('id_support')->where('level', '1')->where('is_primary', '1')->sum('nett')
            + (clone $sqBaseAdmin)->where('status', 'loss')->sum(DB::raw($sqNett));

        // Jumlah dokumen penawaran Smart Quote per tahap (dipakai di sub-label kartu,
        // digabung dengan count dari koleksi $quotation/$quotationAdmin di view).
        $sqDocForecast      = (clone $sqBase)->whereNotIn('status', ['po_received', 'loss'])->count();
        $sqDocProspect      = (clone $sqBase)->whereIn('status', ['hot_prospect', 'negotiation'])->count();
        $sqDocPo            = (clone $sqBase)->where('status', 'po_received')->count();
        $sqDocLoss          = (clone $sqBase)->where('status', 'loss')->count();
        $sqDocForecastAdmin = (clone $sqBaseAdmin)->whereNotIn('status', ['po_received', 'loss'])->count();
        $sqDocProspectAdmin = (clone $sqBaseAdmin)->whereIn('status', ['hot_prospect', 'negotiation'])->count();
        $sqDocPoAdmin       = (clone $sqBaseAdmin)->where('status', 'po_received')->count();
        $sqDocLossAdmin     = (clone $sqBaseAdmin)->where('status', 'loss')->count();
        $prospects = Prospect::where('id_sales', Auth::id())->whereNull('level')->get();
        $noSaleProspect = Prospect::whereNULL('id_sales')->whereNull('provide')->count();
        $leveledProspect = Prospect::whereNULL('level')->where('id_sales', Auth::id())->count();

        // Sales specific metrics
        $salesNewProspectCount   = Prospect::where('id_sales', Auth::id())->whereNull('level')->count();
        $salesFuProspectCount    = Prospect::where('id_sales', Auth::id())->where('level', '9')->count();
        $salesTotalAssigned      = $salesNewProspectCount + $salesFuProspectCount;
        $salesQuoteForecast      = Quotation::where('id_sales', Auth::id())->where('level', '1')->where('is_primary', '1')->whereIn('status', ['20', '30', '40', '60', '80'])->sum('nett');
        $salesQuoteForecastCount = Quotation::where('id_sales', Auth::id())->where('level', '1')->where('is_primary', '1')->whereIn('status', ['20', '30', '40', '60', '80'])->count();
        $salesHotProspect        = Quotation::where('id_sales', Auth::id())->where('level', '1')->where('is_primary', '1')->where('status', '80')->sum('nett');
        $salesHotProspectCount   = Quotation::where('id_sales', Auth::id())->where('level', '1')->where('is_primary', '1')->where('status', '80')->count();
        $salesPo                 = Quotation::where('id_sales', Auth::id())->where('status', '100')->where('level', '1')->where('is_primary', '1')->sum('nett');
        $salesPoCount            = Quotation::where('id_sales', Auth::id())->where('status', '100')->where('level', '1')->where('is_primary', '1')->count();
        $salesLoss               = Quotation::where('id_sales', Auth::id())->where('status', '0')->where('level', '1')->where('is_primary', '1')->sum('nett');
        $salesLossCount          = Quotation::where('id_sales', Auth::id())->where('status', '0')->where('level', '1')->where('is_primary', '1')->count();

        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth = $now->copy()->endOfMonth();

        $availableWeeks = [];
        $cursor = $startOfMonth->copy();
        $wNum = 1;
        while ($cursor->lte($endOfMonth)) {
            $wStart = $cursor->copy()->startOfDay();
            $wEnd = $cursor->copy()->endOfWeek(Carbon::SUNDAY)->endOfDay();
            if ($wEnd->gt($endOfMonth)) {
                $wEnd = $endOfMonth->copy()->endOfDay();
            }

            $availableWeeks[] = [
                'week' => $wNum,
                'start' => $wStart,
                'end' => $wEnd,
                'label' => 'Week '.$wNum.' ('.$wStart->format('d').'–'.$wEnd->format('d').' '.$wStart->format('M Y').')',
            ];

            $cursor = $wEnd->copy()->startOfDay()->addDay();
            $wNum++;
        }

        $defaultWeek = 1;
        foreach ($availableWeeks as $index => $w) {
            if ($now->gte($w['start']) && $now->lte($w['end'])) {
                $defaultWeek = $index + 1;
                break;
            }
        }

        $selectedWeekNum = max(1, min((int) request('week', $defaultWeek), count($availableWeeks)));
        $selectedWeek = $availableWeeks[$selectedWeekNum - 1];

        $startOfWeek = $selectedWeek['start'];
        $endOfWeek = $selectedWeek['end'];

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
        $comment = (clone $quotationComment)->union(clone $prospectComment)
            ->orderBy('date', 'DESC')
            ->take(5)
            ->get();
        $unreadComment = (clone $quotationComment)->where('o.level', '1')->union(
            (clone $prospectComment)->where('comment.level', '1')
        )
            ->orderBy('date', 'DESC')
            ->take(5)
            ->get();

        // Hitung jumlah prospek yang dibuat oleh setiap sales dalam minggu ini dan bulan berjalan
        // Exclude sales yang tidak aktif menerima prospect: 23 (Nada), 16 (Mohamad Didik)
        $salesLeads = User::where(function ($query) {
                $query->where('role', 'Sales')
                    ->orWhere('id', 38);
            })
            ->where('active', '1')
            ->whereNotIn('id', [23, 16])
            ->orderBy('name')
            ->withCount(['prospects as weekly_leads' => function ($query) use ($startOfWeek, $endOfWeek) {
                $query->whereBetween('created_at', [$startOfWeek, $endOfWeek]);
            }])
            ->withCount(['prospects as monthly_leads' => function ($query) use ($startOfMonth, $endOfMonth) {
                $query->whereBetween('created_at', [$startOfMonth, $endOfMonth]);
            }])
            ->get();

        $domainList = Client::where('source', 'Website')
            ->whereNotNull('source_detail')
            ->where('source_detail', '!=', '')
            ->distinct()
            ->orderBy('source_detail')
            ->pluck('source_detail');

        $salesList = User::where(function ($query) {
            $query->where('role', 'Sales')
                ->orWhere('id', 38);
        })->where('active', '1')->where('id', '!=', 23)->orderBy('name')->get(['id', 'name']);

        $defaultCategories = [
            'Service Compressor',
            'Rental Compressor',
            'Sparepart Compressor',
            'Instalasi Piping',
            'Air Audit',
            'Fire System',
            'HVAC System',
            'Unit Baru/Second',
        ];
        $categoryList = Prospect::distinct()
            ->whereNotNull('category')
            ->whereNotIn('category', ['-', ''])
            ->pluck('category')
            ->merge($defaultCategories)
            ->unique()
            ->values()
            ->toArray();

        $availableYears = Prospect::selectRaw('YEAR(date) as year')
            ->whereNotNull('date')
            ->where('date', '!=', '0000-00-00')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year');
        if (!$availableYears->contains($now->year)) {
            $availableYears->prepend($now->year);
        }

        return view('pages.support.prospect.index', compact(
            'prospects',
            'comment',
            'unreadComment',
            'commentAdmin',
            'unreadCommentAdmin',
            'quotation',
            'leveledProspect',
            'noSaleProspect',
            'quotationAdmin',
            'forecast',
            'prospect',
            'po',
            'loss',
            'forecastAdmin',
            'prospectAdmin',
            'poAdmin',
            'lossAdmin',
            'sqDocForecast',
            'sqDocProspect',
            'sqDocPo',
            'sqDocLoss',
            'sqDocForecastAdmin',
            'sqDocProspectAdmin',
            'sqDocPoAdmin',
            'sqDocLossAdmin',
            'salesLeads',
            'availableWeeks',
            'selectedWeekNum',
            'domainList',
            'salesList',
            'categoryList',
            'availableYears',
            'salesNewProspectCount',
            'salesFuProspectCount',
            'salesTotalAssigned',
            'salesQuoteForecast',
            'salesQuoteForecastCount',
            'salesHotProspect',
            'salesHotProspectCount',
            'salesPo',
            'salesPoCount',
            'salesLoss',
            'salesLossCount'
        ));
    }

    /**
     * List of prospects created by a given sales in the current month,
     * used by the "Monthly Leads Distribution" modal on prospect index.
     *
     * @return \Illuminate\Http\Response
     */
    public function monthlyLeads($sales)
    {
        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth = $now->copy()->endOfMonth();

        $salesUser = User::findOrFail($sales);

        $prospects = Prospect::with(['pic.client'])
            ->where('id_sales', $sales)
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($p) {
                [$q] = $p->resolveQuotation();

                return [
                    'id' => $p->id,
                    'company' => $p->pic->client->company ?? '-',
                    'category' => $p->category,
                    'kebutuhan' => $p->kebutuhan,
                    'date' => $p->date ? Carbon::parse($p->date)->format('d-m-Y') : '-',
                    'status' => $q->status ?? null,
                    'nett' => $q->nett ?? ($q->total ?? null),
                ];
            });

        return response()->json([
            'name' => $salesUser->name,
            'data' => $prospects,
        ]);
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
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rule = [
            'company' => 'required',

            'email' => 'required',

            'phone' => 'required',

            'ru' => 'required',

            'source' => 'required',

            'mobile' => 'required',

            'address' => 'required',

            'unit' => 'required',

            'area' => 'required',

            'category' => 'required',

            'new_category' => 'required_if:category,__add_new__',

            'source_detail' => 'nullable|string|max:100',

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
            'company.required' => 'Field company Wajib Diisi',
            'email.required' => 'Field Email Perusahaan Wajib Diisi',
            'phone.required' => 'Field Phone Wajib Diisi',
            'ru.required' => 'Wajib Pilih Reseller atau User',
            'source.required' => 'Field Source Wajib Diisi',
            'mobile.required' => 'Field Mobile Wajib Diisi',
            'address.required' => 'Field Address Wajib Diisi',
            'unit.required' => 'Field Unit Wajib Diisi',
            'area.required' => 'Field Area Wajib Diisi',
            'category.required' => 'Field Category Wajib Dipilih',
            'new_category.required_if' => 'Nama Kategori Baru Wajib Diisi jika memilih tambah kategori baru',
            'source_detail.max' => 'Domain maksimal 100 karakter',
            // 'namePic.required'=> 'Field Nama PIC Wajib Diisi',
            // 'emailPic.required'=> 'Field Email PIC Wajib Diisi',
            // 'phonePic.required'=> 'Field Nomor PIC Wajib Diisi',
            // 'position.required'=> 'Field Posisi PIC Wajib Diisi',
        ];

        $this->validate($request, $rule, $message);
        // dd($request);
        //masukan data ke table leads(client)
        $client = new Client();
        $client->id_sales = Auth::id();
        $client->id_support = Auth::id();
        $client->id_issues = 1;
        $client->company = $request->company;
        $client->email = $request->email; // Menyimpan Email Company
        $client->phone = $request->phone;
        $client->ru = $request->ru;
        $client->web = '-';
        $client->source_detail = $request->filled('source_detail') ? strtolower(trim($request->source_detail)) : null;
        $client->unit = $request->unit; // Menyimpan Unit
        $client->image = 'profile.jpg';
        $client->source = $request->source;
        $client->created_date = Carbon::today()->toDateString();
        $client->role = 'Leads';
        $client->npwp = '0';
        $client->mobile = $request->mobile;
        $client->address = $request->address;
        $client->subAddress = $request->filled('subAddress') ? $request->subAddress : $request->address;
        $client->area = $request->area;
        $clientSave = $client->save();

        // masukan data ke table PIC
        $pic = new Pic();
        $pic->id_client = $client->id;
        $pic->name_pic = $request->namePic;
        $pic->position = $request->position;
        $pic->email_pic = $request->emailPic;
        $pic->phone_pic = $request->phonePic;
        $picsave = $pic->save();

        $finalCategory = ($request->category === '__add_new__' && $request->filled('new_category'))
            ? trim($request->new_category)
            : $request->category;

        $prospect = new Prospect();
        $prospect->id_sales = null;
        $prospect->id_quotation = null;
        $prospect->id_pic = $pic->id;
        $prospect->id_support = Auth::id();
        $prospect->category = $finalCategory ?: '-';
        $prospect->kebutuhan = $request->prospect;
        $prospect->date = Carbon::now();
        $prospect->level = null;
        $prospect->provide = null;
        $prospectSave = $prospect->save();

        if ($prospectSave) {
            $this->notifyProspectCreated($prospect);

            return redirect('prospect')->with('message', 'data telah ditambahkan');
        }
    }

    /**
     * Kirim notifikasi pop-up "Prospect Baru" ke penerima yang ditentukan
     * (lihat self::PROSPECT_NOTIF_RECIPIENT_IDS) begitu prospect baru dari tim Support
     * tersimpan, supaya bisa langsung dibantu follow up tanpa diingatkan manual lewat WhatsApp.
     *
     * Satu baris per penerima (pola sama dengan UnitQuotationController::notifyInvoiceRequested()).
     * Dibungkus try/catch agar kegagalan notifikasi tidak menggagalkan pembuatan prospect.
     */
    private function notifyProspectCreated(Prospect $prospect): void
    {
        try {
            $adminIds = User::where('role', 'Admin')->where('active', '1')->pluck('id')->toArray();
            $recipientIds = array_unique(array_merge(self::PROSPECT_NOTIF_RECIPIENT_IDS, $adminIds));
            foreach ($recipientIds as $uid) {
                ProspectNotification::firstOrCreate(
                    ['id_prospect' => $prospect->id, 'id_user' => $uid, 'type' => 'prospect_created'],
                    ['is_read' => false]
                );
            }
        } catch (\Throwable $e) {
            Log::error('Gagal membuat notifikasi prospect baru: ' . $e->getMessage());
        }
    }

    /**
     * Kirim notifikasi pop-up & lonceng "Prospect Ditugaskan" ke akun sales yang ditunjuk
     * agar sales bisa langsung memfollow up prospek tanpa perlu cek manual.
     */
    private function notifyProspectAssigned(Prospect $prospect, int $salesId): void
    {
        try {
            ProspectNotification::create([
                'id_prospect' => $prospect->id,
                'id_user'     => $salesId,
                'type'        => 'prospect_assigned',
                'is_read'     => false,
            ]);
        } catch (\Throwable $e) {
            Log::error('Gagal membuat notifikasi penugasan prospect: ' . $e->getMessage());
        }
    }

    /**
     * Endpoint polling navbar untuk penerima notifikasi prospect & komentar — mengambil notifikasi
     * prospect baru serta komentar/mention yang belum dibaca supaya bisa dimunculkan sebagai floating toast
     * dan membunyikan alert suara secara otomatis tanpa reload.
     */
    public function unreadProspectNotifications()
    {
        if (!Auth::check()) {
            return response()->json(['count' => 0, 'items' => []]);
        }

        $userId = Auth::id();

        // 1. Notifikasi Prospect (Baru & Ditugaskan)
        $notifs = ProspectNotification::where('id_user', $userId)
            ->with(['prospect.pic.client', 'prospect.support'])
            ->orderByDesc('created_at')
            ->take(15)
            ->get();

        $items = $notifs->map(function ($n) {
            $p = $n->prospect;
            $pic = $p ? $p->pic : null;
            $client = $pic ? $pic->client : null;

            return [
                'id' => (string) $n->id,
                'type' => $n->type,
                'is_read' => (bool) $n->is_read,
                'company' => $client->company ?? ($p->company_name ?? '-'),
                'pic_name' => $pic->name_pic ?? null,
                'support_name' => $p && $p->support ? $p->support->name : null,
                'category' => $p->category ?? null,
                'kebutuhan' => Str::limit((string) ($p->kebutuhan ?? ''), 90),
                'url' => $p ? route('prospect.show', $p->id) : url('prospect'),
                'created_at' => optional($n->created_at)->diffForHumans(),
            ];
        });

        // 2. Mention Comments pada Prospect untuk user ini
        $handledCommentIds = [];
        try {
            $unreadMentions = MentionComment::where('id_mention', $userId)
                ->where('level', '0')
                ->whereHas('comment', function ($q) use ($userId) {
                    $q->where('id_user', '!=', $userId);
                })
                ->with(['comment.user', 'comment.prospect.pic.client'])
                ->latest('id')
                ->take(10)
                ->get();

            foreach ($unreadMentions as $m) {
                $c = $m->comment;
                if (!$c) continue;
                $p = $c->prospect;
                $pic = $p ? $p->pic : null;
                $client = $pic ? $pic->client : null;
                $handledCommentIds[] = $c->id;

                $timeAgo = 'Baru saja';
                if ($c->created_at instanceof \Carbon\Carbon) {
                    $timeAgo = $c->created_at->diffForHumans();
                } elseif (!empty($c->date)) {
                    try {
                        $timeAgo = \Carbon\Carbon::parse($c->date)->diffForHumans();
                    } catch (\Throwable $ex) {}
                }

                $items->push([
                    'id' => 'mention_' . $m->id,
                    'type' => 'comment_mention',
                    'is_read' => false,
                    'company' => $client->company ?? ($p->company_name ?? 'Prospek #' . ($p ? $p->id : '')),
                    'author_name' => $c->user->name ?? 'User',
                    'author_image' => $c->user && $c->user->image ? url($c->user->image) : null,
                    'comment' => Str::limit($c->comment, 90),
                    'url' => $p ? route('prospect.show', $p->id) . '#viewComment' : url('prospect'),
                    'created_at' => $timeAgo,
                ]);
            }
        } catch (\Throwable $e) {
            Log::error('Error fetching unread mentions for toast: ' . $e->getMessage());
        }

        // 3. Komentar Unread pada Prospect milik Sales
        try {
            $unreadComments = Comment::where('type', 'prospect')
                ->where('level', '1')
                ->where('id_user', '!=', $userId)
                ->whereNotIn('id', $handledCommentIds)
                ->whereHas('prospect', function ($q) use ($userId) {
                    $q->where('id_sales', $userId);
                })
                ->with(['user', 'prospect.pic.client'])
                ->latest('id')
                ->take(10)
                ->get();

            foreach ($unreadComments as $c) {
                $p = $c->prospect;
                $pic = $p ? $p->pic : null;
                $client = $pic ? $pic->client : null;

                $timeAgo = 'Baru saja';
                if ($c->created_at instanceof \Carbon\Carbon) {
                    $timeAgo = $c->created_at->diffForHumans();
                } elseif (!empty($c->date)) {
                    try {
                        $timeAgo = \Carbon\Carbon::parse($c->date)->diffForHumans();
                    } catch (\Throwable $ex) {}
                }

                $items->push([
                    'id' => 'comment_' . $c->id,
                    'type' => 'prospect_comment',
                    'is_read' => false,
                    'company' => $client->company ?? ($p->company_name ?? 'Prospek #' . ($p ? $p->id : '')),
                    'author_name' => $c->user->name ?? 'User',
                    'author_image' => $c->user && $c->user->image ? url($c->user->image) : null,
                    'comment' => Str::limit($c->comment, 90),
                    'url' => $p ? route('prospect.show', $p->id) . '#viewComment' : url('prospect'),
                    'created_at' => $timeAgo,
                ]);
            }
        } catch (\Throwable $e) {
            Log::error('Error fetching unread comments for toast: ' . $e->getMessage());
        }

        $unreadCount = $items->where('is_read', false)->count();

        return response()->json([
            'count' => $unreadCount,
            'items' => $items->values(),
        ]);
    }

    /**
     * Tandai satu notifikasi prospect/komentar sebagai sudah dibaca. Scope user id
     * memastikan user hanya bisa menandai notifikasi miliknya sendiri.
     */
    public function markProspectNotificationRead($id)
    {
        $userId = Auth::id();

        if (str_starts_with($id, 'mention_')) {
            $mentionId = substr($id, 8);
            MentionComment::where('id', $mentionId)
                ->where('id_mention', $userId)
                ->update(['level' => '1']);
        } elseif (str_starts_with($id, 'comment_')) {
            $commentId = substr($id, 8);
            Comment::where('id', $commentId)
                ->update(['level' => '2']);
            MentionComment::where('id_comment', $commentId)
                ->where('id_mention', $userId)
                ->update(['level' => '1']);
        } else {
            ProspectNotification::where('id', $id)
                ->where('id_user', $userId)
                ->update(['is_read' => true]);
        }

        return response()->json(['ok' => true]);
    }

    /**
     * Tandai SEMUA notifikasi milik user yang sedang login sebagai sudah dibaca
     * (ProspectNotification, UnitQuotationPaymentNotification, PrDiscussionMention, MentionComment, Comment).
     */
    public function markAllNotificationsRead()
    {
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
        }

        $userId = Auth::id();

        try {
            // 1. Prospect Notifications (Prospect Baru & Ditugaskan)
            ProspectNotification::where('id_user', $userId)
                ->where('is_read', false)
                ->update(['is_read' => true]);

            // 2. Transaksi / Payment Notifications (Invoice, Kontrak, Payment)
            if (class_exists(\App\Models\UnitQuotationPaymentNotification::class)) {
                \App\Models\UnitQuotationPaymentNotification::where('id_user', $userId)
                    ->where('is_read', false)
                    ->update(['is_read' => true]);
            }

            // 3. PR Discussion Mentions
            if (class_exists(\App\Models\PrDiscussionMention::class)) {
                \App\Models\PrDiscussionMention::where('id_user_mention', $userId)
                    ->where('level', '0')
                    ->update(['level' => '1']);
            }

            // 4. Mention Comments
            if (class_exists(\App\Models\MentionComment::class)) {
                \App\Models\MentionComment::where('id_mention', $userId)
                    ->where('level', '0')
                    ->update(['level' => '1']);
            }

            // 5. Comments on user's prospects and quotations
            if (class_exists(\App\Models\Comment::class)) {
                Comment::whereHas('prospect', function ($q) use ($userId) {
                        $q->where('id_sales', $userId);
                    })
                    ->where('level', '1')
                    ->where('id_user', '!=', $userId)
                    ->update(['level' => '2']);

                Comment::whereHas('status.quotation', function ($q) use ($userId) {
                        $q->where('id_sales', $userId);
                    })
                    ->where('level', '1')
                    ->where('id_user', '!=', $userId)
                    ->update(['level' => '2']);
            }

            return response()->json([
                'success' => true,
                'message' => 'Semua notifikasi berhasil ditandai sebagai sudah dibaca'
            ]);
        } catch (\Throwable $e) {
            Log::error('Error marking all notifications as read: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menandai notifikasi: ' . $e->getMessage()
            ], 500);
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
        $prospect = Prospect::find($id);
        [$quotation, $quotationIsSmart] = $prospect->resolveQuotation();
        $allQuotation = Quotation::where('id_pic', $prospect->id_pic)->get();
        $pic = Pic::where('id', $prospect->id_pic)->first();
        $client = Client::where('id', $pic->id_client)->first();
        $sales = User::where(function ($query) {
            $query->where('role', 'Sales')
                ->orWhere('id', 38);
        })->where('active', '1')->where('id', '!=', 23)->orderBy('name')->get();
        $noSaleProspect = Prospect::whereNULL('id_sales')->whereNull('provide')->count();
        $leveledProspect = Prospect::whereNULL('level')->where('id_sales', Auth::id())->count();
        $prospectComments = Comment::where('id_prospect', $id)->where('type', 'prospect')->with('mention')->get();
        $user = User::whereNot('id', Auth::user()->id)->get();
        // dd($prospectComments);

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
        $allUsers = User::where('active', '1')->get(['id', 'name', 'image', 'role']);

        return view('pages.support.prospect.detail', compact('allQuotation', 'prospect', 'comment', 'prospectComments', 'unreadComment', 'commentAdmin', 'quotation', 'quotationIsSmart', 'unreadCommentAdmin', 'leveledProspect', 'noSaleProspect', 'pic', 'client', 'sales', 'user', 'allUsers'));
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
     * Dipakai oleh tombol "Edit Prospect" di halaman detail untuk mengubah isian
     * "Kebutuhan / Detail Prospek" (tabel prospect) serta "Informasi Perusahaan &
     * Kontak PIC" (tabel client + pic) sekaligus dalam satu form.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $prospect = Prospect::findOrFail($id);
        $pic = Pic::findOrFail($prospect->id_pic);
        $client = Client::findOrFail($pic->id_client);

        $rule = [
            'company' => 'required|string|max:255',
            'email' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:100',
            'ru' => 'nullable|string|max:50',
            'source' => 'nullable|string|max:100',
            'source_detail' => 'nullable|string|max:100',
            'unit' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'subAddress' => 'nullable|string',
            'area' => 'nullable|string|max:255',
            'category' => 'nullable|string|max:100',
            'kebutuhan' => 'nullable|string',
            'date' => 'nullable|date',
            'namePic' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
            'emailPic' => 'nullable|string|max:255',
            'phonePic' => 'nullable|string|max:100',
        ];

        $message = [
            'company.required' => 'Field Nama Perusahaan Wajib Diisi',
            'source_detail.max' => 'Domain maksimal 100 karakter',
            'date.date' => 'Format Tanggal tidak valid',
        ];

        $this->validate($request, $rule, $message);

        $client->company = $request->company;
        $client->email = $request->email;
        $client->phone = $request->phone;
        $client->ru = $request->ru;
        $client->source = $request->source;
        $client->source_detail = $request->filled('source_detail') ? strtolower(trim($request->source_detail)) : null;
        $client->unit = $request->unit;
        $client->address = $request->address;
        $client->subAddress = $request->subAddress;
        $client->area = $request->area;
        $client->save();

        $pic->name_pic = $request->namePic;
        $pic->position = $request->position;
        $pic->email_pic = $request->emailPic;
        $pic->phone_pic = $request->phonePic;
        $pic->save();

        $prospect->category = $request->category;
        $prospect->kebutuhan = $request->kebutuhan;
        if ($request->filled('date')) {
            $prospect->date = $request->date;
        }
        $prospect->save();

        return redirect()->route('prospect.show', $prospect->id)->with('message', 'Detail prospek berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $prospect = Prospect::find($id);
        $pic = Pic::find($prospect->id_pic);
        $client = Client::find($pic->id_client);

        $prospectDel = $prospect->delete();
        $picDel = $pic->delete();
        $clientDel = $client->delete();
        if ($prospectDel && $picDel && $clientDel) {
            return 1;
        } else {
            return 0;
        }

    }

    public function add_sales(Request $request, $id)
    {
        // dd($request->all());
        $prospect = Prospect::find($id);
        $pic = Pic::find($prospect->id_pic);
        $client = Client::find($pic->id_client);
        if ($request->provideCheck == 1) {
            $prospect->provide = '1';
            $prospect->id_sales = $request->sales;
            $client->id_sales = $request->sales;
        } else {
            $prospect->provide = '0';
            $prospect->id_sales = null;
        }
        $prospectSave = $prospect->save();
        if ($client) {
            $client->save();
        }
        if ($prospectSave) {
            if ($request->provideCheck == 1 && !empty($request->sales)) {
                $this->notifyProspectAssigned($prospect, (int) $request->sales);
            }

            return redirect('prospect')->with('message', 'data telah ditambahkan');
        }
    }

    public function onProcessFU($id)
    {
        $prospect = Prospect::find($id);
        $prospect->level = '9';
        $prospectSave = $prospect->save();
        if ($prospectSave) {
            return 1;
        } else {
            return 0;
        }
    }

    public function without_quotation($id)
    {
        $prospect = Prospect::find($id);
        $prospect->level = '0';
        $prospectSave = $prospect->save();
        if ($prospectSave) {
            return 1;
        } else {
            return 0;
        }
    }

    public function with_quotation($id)
    {
        $prospect = Prospect::find($id);
        $prospect->level = '1';
        $prospectSave = $prospect->save();
        if ($prospectSave) {
            return 1;
        } else {
            return 0;
        }
    }

    public function no_respond($id)
    {
        $prospect = Prospect::find(id: $id);
        $prospect->level = '2';
        $prospectSave = $prospect->save();
        if ($prospectSave) {
            return 1;
        } else {
            return 0;
        }
    }

    public function no_provide($id)
    {
        $prospect = Prospect::find($id);
        $prospect->provide = '0';
        $prospect->level = null;

        $prospectSave = $prospect->save();
        if ($prospectSave) {
            return 1;
        } else {
            return 0;
        }
    }

    public function create_quotation($id)
    {
        return redirect()->route('unit-quotation.create', ['prospect_id' => $id]);
    }

    public function store_quotation(Request $request, $id)
    {
        $prospect = Prospect::find($id);
        $rule = [
            'no_quote' => 'required',
            'title' => 'required',
            'product' => 'required',
            'detail_product' => 'required',
            'expired_date' => 'required',
            'validity' => 'required',
            'pricing' => 'required',
            'delivery_process' => 'required',
            'payment' => 'required',
            'shipping' => 'required',
        ];
        $message = [
            'no_quote.required' => 'Field No Quote Wajib Diisi',
            'title.required' => 'Field Title Wajib Diisi',
            'product.required' => 'Field Product Wajib Diisi',
            'detail_product.required' => 'Field Detail Product Wajib Diisi',
            'expired_date.required' => 'Wajib isi Expired Date',
            'termcon.required' => 'Field Term and Conditions Wajib Diisi',
            'shipping.required' => 'Quotation Wajib memiliki harga Antar',
        ];
        $this->validate($request, $rule, $message);
        // dd($request);
        // Masukan Data ke Tabel Quotataion
        $quotation = new Quotation();
        $quotation->id_pic = $prospect->id_pic;
        $quotation->id_sales = $prospect->id_sales;
        $quotation->id_service = null;
        $quotation->id_support = $prospect->id_support;
        $quotation->is_primary = '1';
        $quotation->primary_id = 0;
        $quotation->num_rev = 0;
        $quotation->destination = $request->destination;
        $quotation->no_pr = null;
        $quotation->status = '20';
        $quotation->status_date = Carbon::today();
        $quotation->note = '-';
        $quotation->expired_date = $request->expired_date;
        $quotation->po_date = null;
        $quotation->po_file = null;
        $quotation->quote_for = $request->type;
        $quotation->type = 'Sparepart';
        $quotation->level = '1';
        $quotation->estimated_date = $request->estimated_date;
        if ($request->tax != null) {
            $quotation->tax = $request->tax;
        } else {
            $quotation->tax = 0;
        }
        $quotation->shipping = $request->shipping;
        $quotation->no_quote = $request->no_quote;
        $quotation->title = $request->title;
        $quotation->subtotal = $request->subtotal;
        if ($request->diskon != null) {
            $quotation->diskon = $request->diskon;
        } else {
            $quotation->diskon = 0;
        }
        $quotation->fee = 0;
        $quotation->nett = $request->subtotal - $request->diskon;
        $quotation->total_no_tax = $request->total_no_tax;
        $quotation->harga_total = $request->harga_total;
        $quoteSave = $quotation->save();
        $quotation->primary_id = $quotation->id;
        $quotation->save();
        if ($quoteSave) {
            // Masukan Data Ke Tabel Detail Quotataion
            foreach ($request->product as $item => $value) {
                $dQuote = new DetailQuotation();
                $dQuote->id_quotation = $quotation->id;
                $dQuote->id_equivalent = $request->product[$item];
                $dQuote->detail_product = $request->detail_product[$item];
                $dQuote->price = $request->price[$item];
                $dQuote->fee = 0;
                $dQuote->qty = $request->qty[$item];
                $dQuote->info_qty = $request->info_qty[$item];
                $dQuote->disc = $request->disc[$item];
                $dQuote->amount = $request->amount[$item];
                $dQuote->pph = 0;
                $dQuoteSave = $dQuote->save();
            }
            $stats = new ChangeStatus;
            $stats->id_quotation = $quotation->id;
            $stats->date = Carbon::now();
            $stats->note = 'Quotation has been created';
            $stats->status = '10';
            $stats->save();
            if ($dQuoteSave) {
                // Masukan Data ke dalam Tabel Term n Condition
                $termncon = new Termncon();
                $termncon->id_quotation = $quotation->id;
                $termncon->validity = $request->validity;
                $termncon->pricing = $request->pricing;
                $termncon->delivery_process = $request->delivery_process;
                $termncon->payment = $request->payment;
                $termncon->note = $request->note;
                $termnconSave = $termncon->save();
            }
        }
        $prospect->id_quotation = $quotation->id;
        $prospectSave = $prospect->save();
        if ($prospectSave) {
            return redirect('/quotation/'.$quotation->id)->with('message', 'data telah di tambahkan');
        }
    }

    public function choose_quotation(Request $request, $id)
    {
        $prospect = Prospect::find($id);
        $prospect->id_quotation = $request->id_quotation;
        $prospect->level = '1';
        $prospectSave = $prospect->save();
        if ($prospectSave) {
            return redirect('/quotation/'.$request->id_quotation);
        }
    }

    public function add_comment(Request $request, $id)
    {
        // dd($request->all());
        $comment = new Comment;
        $comment->id_status = null;
        $comment->id_prospect = $id;
        $comment->id_user = Auth::user()->id;
        $comment->date = Carbon::now();
        $comment->comment = $request->comment;
        $comment->level = '1';
        $comment->type = 'prospect';
        $commentSave = $comment->save();
        if (@$request->mention) {
            foreach ($request->mention as $key => $value) {
                $mention = new MentionComment();
                $mention->id_comment = $comment->id;
                $mention->id_mention = $value;
                $mention->level = '0';
                // dd($mention);
                $mention->save();
            }
        }
        if ($commentSave) {
            return redirect('/prospect/'.$id.'#viewComment')->with('massage', 'Data berhasil di buat');
        }
    }

    public function view_comment($id)
    {
        $comment = Comment::find($id);

        if ($comment) {
            $comment->level = '2';
            $comment->save();

            // Return response JSON
            return response()->json(['message' => 'Comment updated successfully!']);
        } else {
            // Return error jika tidak ditemukan
            return response()->json(['message' => 'Comment not found'], 404);
        }
    }

    protected function convertToRoman($month)
    {
        $romanMonth = [
            1 => 'I',
            2 => 'II',
            3 => 'III',
            4 => 'IV',
            5 => 'V',
            6 => 'VI',
            7 => 'VII',
            8 => 'VIII',
            9 => 'IX',
            10 => 'X',
            11 => 'XI',
            12 => 'XII',
        ];

        return $romanMonth[$month];
    }
}
