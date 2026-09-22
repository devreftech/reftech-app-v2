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
        $existing = Client::with('crmStatus')->findOrFail($id);
        $currentCrmStatus = (string) ($existing->crmStatus->first()?->status ?? '2');
        $machines = Machine::where('id_client', $id)->with('forecastHistories')->get();
        $charge = PIC::where('id_client', $id)->get();
        $plants = ClientPlant::where('id_client', $id)->get();
        $callhis = Activities::where('id_client', $id)->whereIn('name', ['Daily Call', 'Follow Up', 'CRM', 'Visit'])->get();
        $visit = Activities::where('id_client', $id)->where('name', 'Visit')->get();
        $quote = Quotation::join('pic', 'pic.id', '=', 'quotation.id_pic')->where('pic.id_client', $id)->where('level', '1')->get('quotation.*');

        $activeRegularQuoteCount = Quotation::join('pic', 'pic.id', '=', 'quotation.id_pic')
            ->where('pic.id_client', $id)
            ->where('quotation.level', '1')
            ->where('quotation.is_primary', '1')
            ->whereIn('quotation.status', ['20', '30', '40', '60', '80'])
            ->count();

        $activeUnitQuoteCount = \App\Models\UnitQuotation::where(function ($q) use ($id) {
                $q->where('id_client', $id)->orWhereHas('pic', function ($p) use ($id) {
                    $p->where('id_client', $id);
                });
            })
            ->where('is_latest', 1)
            ->whereNotIn('status', ['po_received', 'loss', 'cancelled'])
            ->count();

        $activeQuoteCount = $activeRegularQuoteCount + $activeUnitQuoteCount;

        $poYears = Quotation::join('pic', 'pic.id', '=', 'quotation.id_pic')
            ->where('pic.id_client', $id)
            ->where('quotation.level', '1')
            ->effectivePrimary()
            ->where('quotation.status', '100')
            ->whereNotNull('quotation.po_date')
            ->selectRaw('DISTINCT YEAR(quotation.po_date) as year')
            ->pluck('year')
            ->push($yearsNow)
            ->unique()
            ->sortDesc()
            ->values();

        $poChartStartYear = $yearsNow - 4;

        $poYearlyByYear = Quotation::join('pic', 'pic.id', '=', 'quotation.id_pic')
            ->where('pic.id_client', $id)
            ->where('quotation.level', '1')
            ->effectivePrimary()
            ->where('quotation.status', '100')
            ->whereNotNull('quotation.po_date')
            ->whereYear('quotation.po_date', '>=', $poChartStartYear)
            ->selectRaw('YEAR(quotation.po_date) as year, SUM(quotation.nett) as total')
            ->groupBy('year')
            ->pluck('total', 'year');

        $unitPoYearlyByYear = \App\Models\UnitQuotation::where(function ($q) use ($id) {
                $q->where('id_client', $id)->orWhereHas('pic', function ($p) use ($id) {
                    $p->where('id_client', $id);
                });
            })
            ->where('is_latest', 1)
            ->where('status', 'po_received')
            ->whereNotNull('po_received')
            ->whereYear('po_received', '>=', $poChartStartYear)
            ->selectRaw('YEAR(po_received) as year, SUM(total - IFNULL(tax_amount, 0)) as total')
            ->groupBy('year')
            ->pluck('total', 'year');

        $poYearlyLabels = [];
        $poYearlyTotals = [];
        for ($year = $poChartStartYear; $year <= $yearsNow; $year++) {
            $poYearlyLabels[] = (string) $year;
            $poYearlyTotals[] = (int) (($poYearlyByYear[$year] ?? 0) + ($unitPoYearlyByYear[$year] ?? 0));
        }
        $poCurrentYearTotal = (int) (($poYearlyByYear[$yearsNow] ?? 0) + ($unitPoYearlyByYear[$yearsNow] ?? 0));

        $previousYear = $yearsNow - 1;
        $poPreviousYearTotal = (int) (($poYearlyByYear[$previousYear] ?? 0) + ($unitPoYearlyByYear[$previousYear] ?? 0));

        $poGrowthPercentage = 0;
        $poGrowthDirection = 'neutral'; // 'up', 'down', 'neutral'

        if ($poPreviousYearTotal > 0) {
            $diff = $poCurrentYearTotal - $poPreviousYearTotal;
            $poGrowthPercentage = round(($diff / $poPreviousYearTotal) * 100, 1);
            if ($poGrowthPercentage > 0) {
                $poGrowthDirection = 'up';
            } elseif ($poGrowthPercentage < 0) {
                $poGrowthDirection = 'down';
            } else {
                $poGrowthDirection = 'neutral';
            }
        } elseif ($poCurrentYearTotal > 0) {
            $poGrowthPercentage = 100;
            $poGrowthDirection = 'up';
        } else {
            $poGrowthPercentage = 0;
            $poGrowthDirection = 'neutral';
        }

        $quotationStatusMap = [
            '20' => ['label' => 'Send Quotation', 'color' => 'secondary'],
            '30' => ['label' => 'Inquiry Accepted', 'color' => 'dark'],
            '40' => ['label' => 'Progress Follow Up', 'color' => 'info'],
            '60' => ['label' => 'Negotiation / Revisi', 'color' => 'primary'],
            '80' => ['label' => 'Hot Prospect', 'color' => 'warning'],
            '100' => ['label' => 'Done PO', 'color' => 'success'],
            '0' => ['label' => 'Loss', 'color' => 'danger'],
        ];

        $sales = User::where('role', 'sales')->where('active', '1')->where('id', '!=', 23)->get();
        $issue = Issues::all();
        $unit = SerialProduct::whereNotNull('detail')->get();
        $crmhis = $this->data($id);
        $machinehis = $this->getServicePerMonth($id);
        $service = Reports::join('pic', 'pic.id', '=', 'reports.id_pic')->where('pic.id_client', $id)->get('reports.*');
        $noSaleProspect = Prospect::whereNULL('id_sales')->whereNull('provide')->count();
        $leveledProspect = Prospect::whereNULL('level')->where('id_sales', Auth::id())->count();

        // ══════════════════════════════════════════════════════════════════════
        // ── UNIFIED CUSTOMER COMPLETE HISTORY & AUDIT TRAIL TIMELINE ──────────
        // ══════════════════════════════════════════════════════════════════════
        $activityTimeline = collect();

        // 1. Registrasi Awal Customer
        $createdDate = $existing->created_at 
            ? Carbon::parse($existing->created_at) 
            : ($existing->created_date ? Carbon::parse($existing->created_date) : null);

        if ($createdDate) {
            $activityTimeline->push([
                'date' => $createdDate,
                'title' => 'Customer Terdaftar di Sistem',
                'category' => 'Registrasi',
                'type' => 'data',
                'status' => 'Created',
                'color' => 'primary',
                'icon' => 'mdi-domain-plus',
                'user_name' => $existing->sales?->name ?: 'System',
                'note' => 'Profil awal customer didaftarkan (Sales: ' . ($existing->sales?->name ?: '-') . ', Area: ' . ($existing->area ?: '-') . ')',
                'diffs' => [],
                'no_quote' => null,
                'url' => null,
            ]);
        }

        // 2. Activity Logs (Perubahan NPWP, Profil, PIC, Plant, Status)
        $picIds = Pic::where('id_client', $id)->pluck('id')->toArray();
        $plantIds = ClientPlant::where('id_client', $id)->pluck('id')->toArray();
        $crmStatusIds = CrmStatus::where('id_client', $id)->pluck('id')->toArray();

        $activityLogs = \App\Models\ActivityLog::with('user')
            ->where(function ($q) use ($id, $picIds, $plantIds, $crmStatusIds) {
                $q->where(function ($sub) use ($id) {
                    $sub->where('subject_type', 'App\Models\Client')->where('subject_id', $id);
                })
                ->orWhere(function ($sub) use ($picIds) {
                    $sub->where('subject_type', 'App\Models\Pic')->whereIn('subject_id', $picIds);
                })
                ->orWhere(function ($sub) use ($plantIds) {
                    $sub->where('subject_type', 'App\Models\ClientPlant')->whereIn('subject_id', $plantIds);
                })
                ->orWhere(function ($sub) use ($crmStatusIds) {
                    $sub->where('subject_type', 'App\Models\CrmStatus')->whereIn('subject_id', $crmStatusIds);
                })
                ->orWhere('properties->id_client', $id);
            })
            ->get();

        $fieldLabels = [
            'npwp' => ['label' => 'Nomor NPWP', 'type' => 'tax', 'category' => 'Perpajakan', 'icon' => 'mdi-file-certificate-outline', 'color' => 'info'],
            'subAddress' => ['label' => 'Alamat Faktur Pajak (SPPKP)', 'type' => 'tax', 'category' => 'Perpajakan', 'icon' => 'mdi-file-document-edit-outline', 'color' => 'info'],
            'company' => ['label' => 'Nama Perusahaan', 'type' => 'data', 'category' => 'Perubahan Data', 'icon' => 'mdi-office-building-cog', 'color' => 'primary'],
            'address' => ['label' => 'Alamat Pabrik / Kantor', 'type' => 'data', 'category' => 'Perubahan Data', 'icon' => 'mdi-map-marker', 'color' => 'primary'],
            'area' => ['label' => 'Wilayah / Area', 'type' => 'data', 'category' => 'Perubahan Data', 'icon' => 'mdi-crosshairs-gps', 'color' => 'primary'],
            'phone' => ['label' => 'No. Telepon Kantor', 'type' => 'data', 'category' => 'Perubahan Data', 'icon' => 'mdi-phone', 'color' => 'primary'],
            'mobile' => ['label' => 'Mobile Phone', 'type' => 'data', 'category' => 'Perubahan Data', 'icon' => 'mdi-cellphone', 'color' => 'primary'],
            'email' => ['label' => 'Email Kantor', 'type' => 'data', 'category' => 'Perubahan Data', 'icon' => 'mdi-email', 'color' => 'primary'],
            'unit' => ['label' => 'Unit Mesin Utama', 'type' => 'data', 'category' => 'Perubahan Data', 'icon' => 'mdi-cog', 'color' => 'primary'],
            'ru' => ['label' => 'Status R/U', 'type' => 'data', 'category' => 'Perubahan Data', 'icon' => 'mdi-repeat', 'color' => 'primary'],
            'source' => ['label' => 'Lead Source', 'type' => 'data', 'category' => 'Perubahan Data', 'icon' => 'mdi-source-branch', 'color' => 'primary'],
            'info' => ['label' => 'Entitas / Via', 'type' => 'data', 'category' => 'Perubahan Data', 'icon' => 'mdi-domain', 'color' => 'primary'],
        ];

        foreach ($activityLogs as $log) {
            $user = $log->user?->name ?: 'Admin/Sales';
            $props = $log->properties ?? [];
            $oldVals = $props['old_values'] ?? [];
            $newVals = $props['new_values'] ?? [];
            $diffs = [];

            if ($log->subject_type === 'App\Models\Client') {
                if ($log->action === 'updated') {
                    $hasTaxChange = false;
                    foreach ($newVals as $fKey => $nVal) {
                        if (isset($fieldLabels[$fKey])) {
                            $oVal = $oldVals[$fKey] ?? '-';
                            if ($fKey === 'npwp' || $fKey === 'subAddress') {
                                $hasTaxChange = true;
                            }
                            $diffs[] = [
                                'field' => $fieldLabels[$fKey]['label'],
                                'old' => empty($oVal) || $oVal === '0' ? '(kosong)' : $oVal,
                                'new' => empty($nVal) || $nVal === '0' ? '(kosong)' : $nVal,
                            ];
                        }
                    }

                    if (!empty($diffs)) {
                        $activityTimeline->push([
                            'date' => Carbon::parse($log->created_at),
                            'title' => $hasTaxChange ? 'Data Perpajakan / NPWP Diperbarui' : 'Data Profil Customer Diperbarui',
                            'category' => $hasTaxChange ? 'Perpajakan' : 'Perubahan Data',
                            'type' => $hasTaxChange ? 'tax' : 'data',
                            'status' => 'Updated',
                            'color' => $hasTaxChange ? 'info' : 'primary',
                            'icon' => $hasTaxChange ? 'mdi-file-certificate-outline' : 'mdi-pencil-box-outline',
                            'user_name' => $user,
                            'note' => count($diffs) . ' data diperbarui oleh ' . $user,
                            'diffs' => $diffs,
                            'no_quote' => null,
                            'url' => null,
                        ]);
                    }
                }
            } elseif ($log->subject_type === 'App\Models\Pic') {
                $actionName = $log->action === 'created' ? 'Ditambahkan' : ($log->action === 'deleted' ? 'Dihapus' : 'Diperbarui');
                $activityTimeline->push([
                    'date' => Carbon::parse($log->created_at),
                    'title' => 'Kontak PIC ' . $actionName,
                    'category' => 'PIC',
                    'type' => 'data',
                    'status' => ucfirst($log->action),
                    'color' => $log->action === 'deleted' ? 'danger' : 'info',
                    'icon' => 'mdi-account-tie',
                    'user_name' => $user,
                    'note' => $log->description,
                    'diffs' => [],
                    'no_quote' => null,
                    'url' => null,
                ]);
            } elseif ($log->subject_type === 'App\Models\ClientPlant') {
                $actionName = $log->action === 'created' ? 'Ditambahkan' : ($log->action === 'deleted' ? 'Dihapus' : 'Diperbarui');
                $activityTimeline->push([
                    'date' => Carbon::parse($log->created_at),
                    'title' => 'Lokasi Plant Pabrik ' . $actionName,
                    'category' => 'Plant',
                    'type' => 'data',
                    'status' => ucfirst($log->action),
                    'color' => $log->action === 'deleted' ? 'danger' : 'warning',
                    'icon' => 'mdi-factory',
                    'user_name' => $user,
                    'note' => $log->description,
                    'diffs' => [],
                    'no_quote' => null,
                    'url' => null,
                ]);
            } elseif ($log->subject_type === 'App\Models\CrmStatus') {
                $activityTimeline->push([
                    'date' => Carbon::parse($log->created_at),
                    'title' => 'Status Siklus Customer Diperbarui',
                    'category' => 'Status',
                    'type' => 'data',
                    'status' => 'Updated',
                    'color' => 'warning',
                    'icon' => 'mdi-sync',
                    'user_name' => $user,
                    'note' => $log->description,
                    'diffs' => [],
                    'no_quote' => null,
                    'url' => null,
                ]);
            }
        }

        // 3. Riwayat CRM Activities & Visits
        foreach ($callhis as $history) {
            $isVisit = in_array(strtolower($history->name), ['visit', 'kunjungan']);
            $activityTimeline->push([
                'date' => Carbon::parse($history->date),
                'title' => $history->action ?: ($isVisit ? 'Kunjungan / Visit Onsite' : 'Catatan CRM'),
                'category' => $isVisit ? 'Visit' : 'CRM',
                'type' => $isVisit ? 'visit' : 'crm',
                'status' => $history->status ?: 'Tercatat',
                'color' => match ($history->name) {
                    'Daily Call' => 'info',
                    'Follow Up' => 'warning',
                    'Visit' => 'danger',
                    default => 'primary',
                },
                'icon' => $isVisit ? 'mdi-car-estate' : 'mdi-phone-in-talk-outline',
                'user_name' => $existing->sales?->name ?: 'Sales',
                'note' => $history->note,
                'diffs' => [],
                'no_quote' => null,
                'url' => null,
            ]);
        }

        // 4. Quotation Regular (Dibuat & Deal PO)
        foreach ($quote as $q) {
            $statusInfo = $quotationStatusMap[$q->status] ?? ['label' => $q->status, 'color' => 'secondary'];
            $qPrice = $q->nett ?: $q->total_price;

            // Event Quotation Dibuat
            $activityTimeline->push([
                'date' => $q->created_at ? Carbon::parse($q->created_at) : Carbon::parse($q->estimated_date),
                'title' => 'Penawaran Regular Diterbitkan',
                'category' => 'Quotation',
                'type' => 'quotation',
                'status' => $statusInfo['label'],
                'color' => $statusInfo['color'],
                'icon' => 'mdi-file-document-outline',
                'user_name' => $existing->sales?->name ?: 'Sales',
                'note' => 'Nilai: Rp ' . number_format($qPrice, 0, ',', '.') . ($q->note ? ' — ' . $q->note : ''),
                'diffs' => [],
                'no_quote' => $q->no_quote,
                'url' => route('quotation.show', $q->id),
            ]);

            // Event Transaksi Sukses PO (Jika sudah deal)
            if ($q->status == '100' || !empty($q->po_date) || !empty($q->po_file)) {
                $activityTimeline->push([
                    'date' => $q->po_date ? Carbon::parse($q->po_date) : ($q->updated_at ? Carbon::parse($q->updated_at) : Carbon::now()),
                    'title' => '🎉 Transaksi Sukses Purchase Order (Deal)',
                    'category' => 'Purchase Order',
                    'type' => 'po',
                    'status' => 'Done PO',
                    'color' => 'success',
                    'icon' => 'mdi-cart-check',
                    'user_name' => $existing->sales?->name ?: 'Sales',
                    'note' => 'PO Diterima Senilai Rp ' . number_format($qPrice, 0, ',', '.') . ($q->po_file ? ' (File PO Terlampir)' : ''),
                    'diffs' => [],
                    'no_quote' => $q->no_quote,
                    'url' => route('quotation.show', $q->id),
                ]);
            }
        }

        // 5. Smart Quote Unit
        $unitQuotes = \App\Models\UnitQuotation::where(function ($q) use ($id) {
            $q->where('id_client', $id)->orWhereHas('pic', function ($p) use ($id) {
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
                'date' => $uq->created_at ? Carbon::parse($uq->created_at) : Carbon::parse($uq->date),
                'title' => 'Smart Quote Unit Diterbitkan',
                'category' => 'Smart Quote',
                'type' => 'quotation',
                'status' => $statusInfo['label'],
                'color' => $statusInfo['color'],
                'icon' => 'mdi-file-percent-outline',
                'user_name' => $existing->sales?->name ?: 'Sales',
                'note' => 'Total Nilai: Rp ' . number_format($uq->total, 0, ',', '.') . ($uq->note ? ' — ' . $uq->note : ''),
                'diffs' => [],
                'no_quote' => $uq->no_quote,
                'url' => route('unit-quotation.show', $uq->id),
            ]);

            if ($uq->status === 'po_received' || !empty($uq->po_received)) {
                $activityTimeline->push([
                    'date' => $uq->po_received ? Carbon::parse($uq->po_received) : ($uq->updated_at ? Carbon::parse($uq->updated_at) : Carbon::now()),
                    'title' => '🎉 Transaksi Sukses PO Smart Quote Unit (Deal)',
                    'category' => 'Purchase Order',
                    'type' => 'po',
                    'status' => 'Done PO',
                    'color' => 'success',
                    'icon' => 'mdi-cart-check',
                    'user_name' => $existing->sales?->name ?: 'Sales',
                    'note' => 'PO Unit Diterima Senilai Rp ' . number_format($uq->total, 0, ',', '.'),
                    'diffs' => [],
                    'no_quote' => $uq->no_quote,
                    'url' => route('unit-quotation.show', $uq->id),
                ]);
            }
        }

        // 6. Laporan Servis Mesin (Reports)
        foreach ($service as $rep) {
            $activityTimeline->push([
                'date' => Carbon::parse($rep->date),
                'title' => 'Laporan Servis & Maintenance Mesin',
                'category' => 'Servis Mesin',
                'type' => 'service',
                'status' => $rep->status ?: 'Servis Selesai',
                'color' => 'info',
                'icon' => 'mdi-wrench-outline',
                'user_name' => 'Teknisi',
                'note' => 'No. Servis: ' . ($rep->no_service ?: '-') . ($rep->activity ? ' — ' . $rep->activity : ''),
                'diffs' => [],
                'no_quote' => null,
                'url' => null,
            ]);
        }

        $activityTimeline = $activityTimeline->sortByDesc('date')->values();


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
                'poYearlyLabels',
                'poYearlyTotals',
                'poCurrentYearTotal',
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
                'yearsNow',
                'currentCrmStatus',
                'activeQuoteCount',
                'poPreviousYearTotal',
                'poGrowthPercentage',
                'poGrowthDirection'
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
        $date = $request->date ? Carbon::parse($request->date) : Carbon::today();
        
        // Hitung week otomatis kalender kerja Senin-Minggu
        $dayOfMonth = (int) $date->day;
        $firstDayOfMonth = (int) $date->copy()->startOfMonth()->dayOfWeekIso; // 1 (Mon) - 7 (Sun)
        $offset = ($firstDayOfMonth - 1);
        $autoWeek = max(1, min(5, (int) floor(($dayOfMonth + $offset - 1) / 7) + 1));

        $action = new Activities;
        $action->id_client = $id;
        $action->name = ($request->action === 'Visit') ? 'Visit' : 'CRM';
        $action->status = $request->status ?? 'Responded';
        $action->week = !empty($request->week) ? (int)$request->week : $autoWeek;
        $action->action = $request->action ?? 'Phone Office';
        $action->note = $request->note ?? '-';
        $action->date = $date->format('Y-m-d');
        $action->follow_up = $request->follow_up ?: $date->copy()->addMonth()->format('Y-m-d');
        $activitiesSave = $action->save();
        if ($activitiesSave) {
            // Update customer status to Non-Aktif ('3') atau Bangkrupt ('1') jika opsi dipilih pada status Not Respon
            if ($request->status === 'Not Respon') {
                $newStatus = null;
                if ($request->filled('customer_status_action')) {
                    $newStatus = (string) $request->customer_status_action;
                } elseif ($request->filled('set_non_active')) {
                    $newStatus = '3';
                } elseif ($request->filled('set_bangkrupt')) {
                    $newStatus = '1';
                }

                if (in_array($newStatus, ['1', '3'])) {
                    $statusRecord = CrmStatus::where('id_client', $id)->first();
                    if (!$statusRecord) {
                        CrmStatus::create([
                            'id_client' => $id,
                            'status' => $newStatus,
                        ]);
                    } else {
                        $statusRecord->status = $newStatus;
                        $statusRecord->save();
                        
                        // Bersihkan jika ada record duplikat lama
                        CrmStatus::where('id_client', $id)->where('id', '!=', $statusRecord->id)->delete();
                    }
                }
            }

            $msg = ($request->action === 'Visit') ? "Aktivitas Visit berhasil dicatat" : "Aktivitas CRM berhasil dicatat";
            return redirect("/existing/" . $id)->with("success", $msg);
        }
    }

    public function updateStatusAtDropdown(Request $request, $id)
    {
        $request->validate([
            'status' => 'required',
        ]);

        $statusValue = (string) $request->status;
        $existing = CrmStatus::where('id_client', $id)->get();

        if ($existing->isEmpty()) {
            CrmStatus::create([
                'id_client' => $id,
                'status' => $statusValue,
            ]);
        } else {
            // Keep the first record and remove any lingering duplicates if any exist
            $primary = $existing->first();
            $primary->status = $statusValue;
            $primary->save();

            if ($existing->count() > 1) {
                CrmStatus::where('id_client', $id)->where('id', '!=', $primary->id)->delete();
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Status customer berhasil diperbarui',
            'status' => $statusValue,
        ]);
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
        if (!$client) {
            abort(404);
        }
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

        if (!function_exists('App\Http\Controllers\getMonthName')) {
            function getMonthName($monthNumber)
            {
                return \Carbon\Carbon::create()->month($monthNumber)->format('F');
            }
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
