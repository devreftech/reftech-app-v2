<?php

namespace App\Http\Controllers;

use App\Models\DetailProduct;
use App\Models\DetailStockOpname;
use App\Models\StockOpname;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OpnameController extends Controller
{
    public function index()
    {
        $totalSessions = StockOpname::count();
        $thisYearSessions = StockOpname::where('year', Carbon::now()->year)->count();
        $totalItemsAudited = DetailStockOpname::count();
        $totalDiscrepancyItems = DetailStockOpname::where('selisih', '!=', 0)->count();

        $opnames = StockOpname::with('user')
            ->withCount('detail')
            ->withCount(['detail as selisih_count' => function ($query) {
                $query->where('selisih', '!=', 0);
            }])
            ->orderBy('date', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        return view('pages.warehouse.opname.index', compact(
            'totalSessions',
            'thisYearSessions',
            'totalItemsAudited',
            'totalDiscrepancyItems',
            'opnames'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'periode' => 'required',
        ]);

        $date = $request->date ? Carbon::parse($request->date) : Carbon::today();

        $opname = new StockOpname;
        $opname->id_user = Auth::id();
        $opname->date = $date->format('Y-m-d');
        $opname->year = $request->year ? (int)$request->year : $date->year;
        $opname->periode = $request->periode;
        $opname->note = $request->note ?? '-';
        $opnameSave = $opname->save();

        if ($opnameSave) {
            return redirect()->route('opname.show', $opname->id)->with('success', 'Sesi Stock Opname berhasil dibuat! Silakan mulai verifikasi fisik produk.');
        }

        return redirect()->back()->with('error', 'Gagal membuat sesi Stock Opname.');
    }

    public function destroy($id)
    {
        $opname = StockOpname::find($id);
        if (!$opname) {
            return redirect()->route('opname.index')->with('error', 'Data Stock Opname tidak ditemukan.');
        }

        if ($opname->isLocked() && !in_array(Auth::user()->role ?? '', ['Super Admin', 'Developer'])) {
            return redirect()->route('opname.show', $id)->with('error', 'Sesi Stock Opname yang telah difinalisasi dan dikunci tidak dapat dihapus.');
        }

        DetailStockOpname::where('id_stock_opname', $id)->delete();
        $opname->delete();

        return redirect()->route('opname.index')->with('success', 'Data Stock Opname beserta rinciannya berhasil dihapus.');
    }

    public function show($id)
    {
        $opname = StockOpname::with(['user', 'userCompleted'])->find($id);
        if (!$opname) {
            return redirect()->route('opname.index')->with('error', 'Stock Opname tidak ditemukan');
        }

        $totalSku = DetailProduct::where('is_opname', 1)->count();
        $countedSku = DetailStockOpname::where('id_stock_opname', $id)->count();

        $selisihStats = DB::table('detail_stock_opname as dso')
            ->join('detail_product as dp', 'dp.id', '=', 'dso.id_product')
            ->where('dso.id_stock_opname', $id)
            ->select(
                DB::raw('COUNT(CASE WHEN dso.selisih != 0 THEN 1 END) as total_selisih_count'),
                DB::raw('COUNT(CASE WHEN (COALESCE(dso.stock_bdg, 0) - COALESCE(dp.stock, 0)) != 0 THEN 1 END) as selisih_bdg_count'),
                DB::raw('COUNT(CASE WHEN (COALESCE(dso.stock_bks, 0) - COALESCE(dp.warehouse_stock, 0)) != 0 THEN 1 END) as selisih_bks_count'),
                DB::raw('SUM(dso.selisih) as sum_total_selisih'),
                DB::raw('SUM(COALESCE(dso.stock_bdg, 0) - COALESCE(dp.stock, 0)) as sum_selisih_bdg'),
                DB::raw('SUM(COALESCE(dso.stock_bks, 0) - COALESCE(dp.warehouse_stock, 0)) as sum_selisih_bks'),
                DB::raw('MIN(dso.created_at) as first_input_at'),
                DB::raw('MAX(dso.updated_at) as last_input_at')
            )
            ->first();

        // Milestone timeline aktivitas harian
        $dailyTimeline = DB::table('detail_stock_opname')
            ->where('id_stock_opname', $id)
            ->select(
                DB::raw('DATE(updated_at) as log_date'),
                DB::raw('COUNT(*) as total_items'),
                DB::raw('COUNT(CASE WHEN stock_bdg IS NOT NULL THEN 1 END) as bdg_counted'),
                DB::raw('COUNT(CASE WHEN stock_bks IS NOT NULL THEN 1 END) as bks_counted'),
                DB::raw('COUNT(CASE WHEN selisih != 0 THEN 1 END) as selisih_items')
            )
            ->groupBy(DB::raw('DATE(updated_at)'))
            ->orderBy('log_date', 'asc')
            ->get();

        $stats = [
            'total_sku' => $totalSku,
            'counted_sku' => $countedSku,
            'uncounted_sku' => max(0, $totalSku - $countedSku),
            'progress_percent' => $totalSku > 0 ? round(($countedSku / $totalSku) * 100, 1) : 0,
            'total_selisih_count' => $selisihStats->total_selisih_count ?? 0,
            'selisih_bdg_count' => $selisihStats->selisih_bdg_count ?? 0,
            'selisih_bks_count' => $selisihStats->selisih_bks_count ?? 0,
            'sum_total_selisih' => $selisihStats->sum_total_selisih ?? 0,
            'sum_selisih_bdg' => $selisihStats->sum_selisih_bdg ?? 0,
            'sum_selisih_bks' => $selisihStats->sum_selisih_bks ?? 0,
            'first_input_at' => $selisihStats->first_input_at ?? null,
            'last_input_at' => $selisihStats->last_input_at ?? null,
            'is_all_counted' => ($totalSku > 0 && $countedSku >= $totalSku),
            'is_locked' => $opname->isLocked(),
        ];

        return view('pages.warehouse.opname.detail', compact('opname', 'stats', 'dailyTimeline'));
    }

    public function data_items($id)
    {
        $opname = StockOpname::find($id);
        $isLocked = $opname ? $opname->isLocked() : false;

        $prevStockOpnameId = StockOpname::where('id', '<', $id)
            ->orderBy('id', 'desc')
            ->value('id');

        $currentUserId = Auth::id();
        $userRole = Auth::user()->role ?? '';
        $isAdmin = in_array($userRole, ['Admin', 'Super Admin', 'Director', 'Warehouse Manager', 'Operational Manager']);

        $data = DB::table('detail_product as dp')
            ->leftJoin('product as p', 'p.id', '=', 'dp.id_product')
            ->leftJoin('detail_stock_opname as dso', function ($join) use ($id) {
                $join->on('dso.id_product', '=', 'dp.id')
                    ->where('dso.id_stock_opname', '=', $id);
            })
            ->leftJoin('detail_stock_opname as prev', function ($join) use ($prevStockOpnameId) {
                $join->on('prev.id_product', '=', 'dp.id')
                    ->where('prev.id_stock_opname', '=', $prevStockOpnameId);
            })
            ->leftJoin('users as u_bdg', 'u_bdg.id', '=', 'dso.id_user_bdg')
            ->leftJoin('users as u_bks', 'u_bks.id', '=', 'dso.id_user_bks')
            ->where(function ($q) {
                $q->where('dp.is_opname', 1)
                  ->orWhereNotNull('dso.id');
            })
            ->select(
                'dp.id as product_id',
                'dp.id_product as parent_product_id',
                'dp.replacement',
                'dp.is_opname',
                'p.description',
                'p.unit',
                'p.go',
                'dp.stock as sistem_bdg',
                'dp.warehouse_stock as sistem_bks',
                DB::raw('(COALESCE(dp.stock, 0) + COALESCE(dp.warehouse_stock, 0)) as sistem_total'),
                'dso.id as detail_opname_id',
                'dso.stock_bdg as fisik_bdg',
                'dso.id_user_bdg',
                'u_bdg.name as user_bdg_name',
                'u_bdg.image as user_bdg_image',
                'dso.stock_bks as fisik_bks',
                'dso.id_user_bks',
                'u_bks.name as user_bks_name',
                'u_bks.image as user_bks_image',
                'dso.stock_gudang as fisik_total',
                'dso.selisih as selisih_total',
                'dso.note',
                'dso.updated_at',
                DB::raw('CASE WHEN dso.id IS NOT NULL THEN 1 ELSE 0 END as is_counted'),
                DB::raw('CASE WHEN dso.stock_bdg IS NOT NULL THEN (dso.stock_bdg - COALESCE(dp.stock, 0)) ELSE (0 - COALESCE(dp.stock, 0)) END as selisih_bdg'),
                DB::raw('CASE WHEN dso.stock_bks IS NOT NULL THEN (dso.stock_bks - COALESCE(dp.warehouse_stock, 0)) ELSE (0 - COALESCE(dp.warehouse_stock, 0)) END as selisih_bks'),
                DB::raw('COALESCE(prev.stock_sistem, 0) as prev_qty')
            )
            ->orderByDesc('sistem_total')
            ->orderBy('dp.replacement', 'asc')
            ->get();

        return response()->json([
            'current_stock_opname_id' => $id,
            'previous_stock_opname_id' => $prevStockOpnameId,
            'current_user_id' => $currentUserId,
            'is_admin' => $isAdmin,
            'is_locked' => $isLocked,
            'server_time' => Carbon::now()->toDateTimeString(),
            'data' => $data
        ]);
    }

    public function sync_updates(Request $request, $id)
    {
        $opname = StockOpname::find($id);
        $isLocked = $opname ? $opname->isLocked() : false;
        $userRole = Auth::user()->role ?? '';
        $isAdmin = in_array($userRole, ['Admin', 'Super Admin', 'Director', 'Warehouse Manager', 'Operational Manager']);

        $since = $request->since ? Carbon::parse($request->since) : null;

        $query = DB::table('detail_stock_opname as dso')
            ->join('detail_product as dp', 'dp.id', '=', 'dso.id_product')
            ->leftJoin('users as u_bdg', 'u_bdg.id', '=', 'dso.id_user_bdg')
            ->leftJoin('users as u_bks', 'u_bks.id', '=', 'dso.id_user_bks')
            ->where('dso.id_stock_opname', $id)
            ->select(
                'dso.id_product',
                'dso.id as detail_opname_id',
                'dso.stock_bdg as fisik_bdg',
                'dso.id_user_bdg',
                'u_bdg.name as user_bdg_name',
                'u_bdg.image as user_bdg_image',
                'dso.stock_bks as fisik_bks',
                'dso.id_user_bks',
                'u_bks.name as user_bks_name',
                'u_bks.image as user_bks_image',
                'dso.stock_gudang as fisik_total',
                'dso.selisih as selisih_total',
                'dso.note',
                'dso.updated_at',
                DB::raw('CASE WHEN dso.stock_bdg IS NOT NULL THEN (dso.stock_bdg - COALESCE(dp.stock, 0)) ELSE (0 - COALESCE(dp.stock, 0)) END as selisih_bdg'),
                DB::raw('CASE WHEN dso.stock_bks IS NOT NULL THEN (dso.stock_bks - COALESCE(dp.warehouse_stock, 0)) ELSE (0 - COALESCE(dp.warehouse_stock, 0)) END as selisih_bks')
            );

        if ($since) {
            $query->where('dso.updated_at', '>=', $since->copy()->subSeconds(10));
        }

        $updates = $query->get();

        $stats = null;
        if ($updates->count() > 0 || !$since) {
            $totalSku = DetailProduct::where('is_opname', 1)->count();
            $countedSku = DetailStockOpname::where('id_stock_opname', $id)->count();

            $selisihStats = DB::table('detail_stock_opname as dso')
                ->join('detail_product as dp', 'dp.id', '=', 'dso.id_product')
                ->where('dso.id_stock_opname', $id)
                ->select(
                    DB::raw('COUNT(CASE WHEN dso.selisih != 0 THEN 1 END) as total_selisih_count'),
                    DB::raw('COUNT(CASE WHEN (COALESCE(dso.stock_bdg, 0) - COALESCE(dp.stock, 0)) != 0 THEN 1 END) as selisih_bdg_count'),
                    DB::raw('COUNT(CASE WHEN (COALESCE(dso.stock_bks, 0) - COALESCE(dp.warehouse_stock, 0)) != 0 THEN 1 END) as selisih_bks_count'),
                    DB::raw('SUM(dso.selisih) as sum_total_selisih'),
                    DB::raw('SUM(COALESCE(dso.stock_bdg, 0) - COALESCE(dp.stock, 0)) as sum_selisih_bdg'),
                    DB::raw('SUM(COALESCE(dso.stock_bks, 0) - COALESCE(dp.warehouse_stock, 0)) as sum_selisih_bks')
                )
                ->first();

            $stats = [
                'total_sku' => $totalSku,
                'counted_sku' => $countedSku,
                'uncounted_sku' => max(0, $totalSku - $countedSku),
                'progress_percent' => $totalSku > 0 ? round(($countedSku / $totalSku) * 100, 1) : 0,
                'total_selisih_count' => $selisihStats->total_selisih_count ?? 0,
                'selisih_bdg_count' => $selisihStats->selisih_bdg_count ?? 0,
                'selisih_bks_count' => $selisihStats->selisih_bks_count ?? 0,
                'sum_total_selisih' => $selisihStats->sum_total_selisih ?? 0,
                'sum_selisih_bdg' => $selisihStats->sum_selisih_bdg ?? 0,
                'sum_selisih_bks' => $selisihStats->sum_selisih_bks ?? 0,
            ];
        }

        return response()->json([
            'has_updates' => $updates->count() > 0,
            'updates_count' => $updates->count(),
            'current_user_id' => $currentUserId,
            'is_admin' => $isAdmin,
            'server_time' => Carbon::now()->toDateTimeString(),
            'updates' => $updates,
            'stats' => $stats
        ]);
    }

    public function finalize(Request $request, $id)
    {
        $opname = StockOpname::findOrFail($id);

        if ($opname->isLocked()) {
            return response()->json([
                'success' => true,
                'message' => 'Sesi Stock Opname sudah dalam status Terkunci / Selesai.'
            ]);
        }

        $totalSku = DetailProduct::where('is_opname', 1)->count();
        $countedSku = DetailStockOpname::where('id_stock_opname', $id)->count();

        if ($totalSku > 0 && $countedSku < $totalSku) {
            $remaining = $totalSku - $countedSku;
            return response()->json([
                'success' => false,
                'message' => "Belum semua item terhitung ({$remaining} item SKU tersisa). Mohon selesaikan penghitungan fisik seluruh item sebelum memfinalisasi."
            ], 422);
        }

        $opname->is_locked = 1;
        $opname->status = 'completed';
        $opname->completed_at = Carbon::now();
        $opname->id_user_completed = Auth::id();
        $opname->save();

        return response()->json([
            'success' => true,
            'message' => 'Data Stock Opname berhasil disimpan dan dikunci permanen! Seluruh input fisik telah dinonaktifkan.',
            'completed_at' => Carbon::now()->translatedFormat('d F Y H:i'),
            'user_completed_name' => Auth::user()->name ?? '-'
        ]);
    }

    public function save_item_inline(Request $request, $id)
    {
        $opname = StockOpname::findOrFail($id);
        if ($opname->isLocked()) {
            return response()->json([
                'success' => false,
                'message' => 'Sesi Stock Opname ini telah difinalisasi dan dikunci permanen. Perubahan tidak diizinkan.'
            ], 403);
        }

        $request->validate([
            'id_product' => 'required|exists:detail_product,id',
        ]);

        $replacement = DetailProduct::findOrFail($request->id_product);
        $dso = DetailStockOpname::firstOrNew([
            'id_stock_opname' => $id,
            'id_product' => $replacement->id,
        ]);

        $currentUserId = Auth::id();
        $currentUser = Auth::user();
        $userRole = $currentUser->role ?? '';
        $isAdmin = in_array($userRole, ['Admin', 'Super Admin', 'Director', 'Warehouse Manager', 'Operational Manager']);

        $sistem_bdg = (int)$replacement->stock;
        $sistem_bks = (int)$replacement->warehouse_stock;
        $sistem_total = $sistem_bdg + $sistem_bks;

        $targetField = $request->target_field; // 'bdg', 'bks', 'note', or null

        if ($targetField === 'bdg' || ($targetField === null && $request->has('stock_bdg'))) {
            if ($dso->id_user_bdg && $dso->id_user_bdg != $currentUserId && !$isAdmin) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kolom Fisik BDG terkunci karena sudah diinput oleh ' . ($dso->userBdg->name ?? 'akun lain') . '.'
                ], 403);
            }
            if ($request->stock_bdg !== null && $request->stock_bdg !== '') {
                $dso->stock_bdg = (int)$request->stock_bdg;
                $dso->id_user_bdg = $currentUserId;
            } else {
                $dso->stock_bdg = null;
                $dso->id_user_bdg = null;
            }
        }

        if ($targetField === 'bks' || ($targetField === null && $request->has('stock_bks'))) {
            if ($dso->id_user_bks && $dso->id_user_bks != $currentUserId && !$isAdmin) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kolom Fisik BKS terkunci karena sudah diinput oleh ' . ($dso->userBks->name ?? 'akun lain') . '.'
                ], 403);
            }
            if ($request->stock_bks !== null && $request->stock_bks !== '') {
                $dso->stock_bks = (int)$request->stock_bks;
                $dso->id_user_bks = $currentUserId;
            } else {
                $dso->stock_bks = null;
                $dso->id_user_bks = null;
            }
        }

        if ($targetField === 'note' || ($targetField === null && $request->has('note'))) {
            $dso->note = $request->note ?? '';
        }

        $fisik_bdg = $dso->stock_bdg;
        $fisik_bks = $dso->stock_bks;
        $hasCount = ($fisik_bdg !== null || $fisik_bks !== null);

        $val_bdg = $fisik_bdg ?? 0;
        $val_bks = $fisik_bks ?? 0;
        $fisik_total = $hasCount ? ($val_bdg + $val_bks) : null;
        $selisih_total = $hasCount ? (($val_bdg + $val_bks) - $sistem_total) : null;

        $dso->stock_sistem = $sistem_total;
        $dso->stock_gudang = $fisik_total;
        $dso->selisih = $selisih_total ?? 0;
        $dso->save();

        $dso->load(['userBdg', 'userBks']);

        $selisih_bdg = ($fisik_bdg !== null) ? ($fisik_bdg - $sistem_bdg) : 0;
        $selisih_bks = ($fisik_bks !== null) ? ($fisik_bks - $sistem_bks) : 0;

        return response()->json([
            'success' => true,
            'message' => 'Stok fisik berhasil disimpan.',
            'item' => [
                'detail_opname_id' => $dso->id,
                'id_product' => $replacement->id,
                'sistem_bdg' => $sistem_bdg,
                'sistem_bks' => $sistem_bks,
                'sistem_total' => $sistem_total,
                'fisik_bdg' => $dso->stock_bdg,
                'id_user_bdg' => $dso->id_user_bdg,
                'user_bdg_name' => $dso->userBdg->name ?? null,
                'user_bdg_image' => $dso->userBdg->image ?? null,
                'fisik_bks' => $dso->stock_bks,
                'id_user_bks' => $dso->id_user_bks,
                'user_bks_name' => $dso->userBks->name ?? null,
                'user_bks_image' => $dso->userBks->image ?? null,
                'fisik_total' => $fisik_total,
                'selisih_bdg' => $selisih_bdg,
                'selisih_bks' => $selisih_bks,
                'selisih_total' => $selisih_total,
                'note' => $dso->note,
                'updated_at' => $dso->updated_at ? $dso->updated_at->toDateTimeString() : Carbon::now()->toDateTimeString(),
                'is_counted' => $hasCount ? 1 : 0,
            ]
        ]);
    }

    public function bulk_fill_system($id)
    {
        $opname = StockOpname::findOrFail($id);
        if ($opname->isLocked()) {
            return response()->json([
                'success' => false,
                'message' => 'Sesi Stock Opname ini telah difinalisasi dan dikunci permanen. Perubahan tidak diizinkan.'
            ], 403);
        }

        $products = DetailProduct::where('is_opname', 1)->get();
        $currentUserId = Auth::id();

        DB::transaction(function () use ($id, $products, $currentUserId) {
            foreach ($products as $p) {
                $sistem_bdg = (int)$p->stock;
                $sistem_bks = (int)$p->warehouse_stock;
                $sistem_total = $sistem_bdg + $sistem_bks;

                DetailStockOpname::updateOrCreate(
                    [
                        'id_stock_opname' => $id,
                        'id_product' => $p->id,
                    ],
                    [
                        'stock_sistem' => $sistem_total,
                        'stock_bdg' => $sistem_bdg,
                        'id_user_bdg' => $currentUserId,
                        'stock_bks' => $sistem_bks,
                        'id_user_bks' => $currentUserId,
                        'stock_gudang' => $sistem_total,
                        'selisih' => 0,
                    ]
                );
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Semua produk berhasil diset sama dengan stok sistem (0 selisih).'
        ]);
    }

    public function bulk_reset($id)
    {
        $opname = StockOpname::findOrFail($id);
        if ($opname->isLocked()) {
            return response()->json([
                'success' => false,
                'message' => 'Sesi Stock Opname ini telah difinalisasi dan dikunci permanen. Perubahan tidak diizinkan.'
            ], 403);
        }

        DetailStockOpname::where('id_stock_opname', $id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Seluruh data hitungan fisik pada sesi ini berhasil di-reset.'
        ]);
    }

    public function show_print($id)
    {
        $opname = StockOpname::with(['user', 'userCompleted'])->findOrFail($id);

        $detailOpname = DB::table('detail_product as dp')
            ->leftJoin('product as p', 'p.id', '=', 'dp.id_product')
            ->leftJoin('detail_stock_opname as dso', function ($join) use ($id) {
                $join->on('dso.id_product', '=', 'dp.id')
                    ->where('dso.id_stock_opname', '=', $id);
            })
            ->where(function ($q) {
                $q->where('dp.is_opname', 1)
                  ->orWhereNotNull('dso.id');
            })
            ->select(
                'dp.id as product_id',
                'dp.replacement',
                'p.description',
                'p.unit',
                'p.go',
                'dp.stock as sistem_bdg',
                'dp.warehouse_stock as sistem_bks',
                DB::raw('(COALESCE(dp.stock, 0) + COALESCE(dp.warehouse_stock, 0)) as sistem_total'),
                'dso.stock_bdg as fisik_bdg',
                'dso.stock_bks as fisik_bks',
                'dso.stock_gudang as fisik_total',
                'dso.selisih as selisih_total',
                'dso.note',
                DB::raw('CASE WHEN dso.stock_bdg IS NOT NULL THEN (dso.stock_bdg - COALESCE(dp.stock, 0)) ELSE 0 END as selisih_bdg'),
                DB::raw('CASE WHEN dso.stock_bks IS NOT NULL THEN (dso.stock_bks - COALESCE(dp.warehouse_stock, 0)) ELSE 0 END as selisih_bks')
            )
            ->orderBy('dp.replacement', 'asc')
            ->get();

        return view('pages.warehouse.opname.detail-print', compact('opname', 'detailOpname'));
    }

    public function store_product(Request $request, $id)
    {
        $opnameMain = StockOpname::findOrFail($id);
        if ($opnameMain->isLocked()) {
            return redirect()->back()->with('error', 'Sesi Stock Opname ini telah difinalisasi dan dikunci permanen.');
        }

        $replacement = DetailProduct::find($request->replacement);
        $stock_gudang = (int)$request->stock_bdg + (int)$request->stock_bks;
        $opname = DetailStockOpname::firstOrNew([
            'id_stock_opname' => $id,
            'id_product' => $request->replacement,
        ]);
        $sistem_total = (int)$replacement->stock + (int)$replacement->warehouse_stock;
        $opname->stock_sistem = $sistem_total;
        $opname->stock_bdg = (int)$request->stock_bdg;
        $opname->stock_bks = (int)$request->stock_bks;
        $opname->stock_gudang = $stock_gudang;
        $opname->selisih = $stock_gudang - $sistem_total;
        $opname->note = $request->note ?? '';
        $opname->save();

        return redirect()->back()->with('success', 'Stock Opname Product berhasil disimpan!');
    }

    public function update_product(Request $request, $id)
    {
        $opname = DetailStockOpname::find($id);
        if (!$opname) {
            return redirect()->back()->with('error', 'Data tidak ditemukan');
        }

        $opnameMain = StockOpname::find($opname->id_stock_opname);
        if ($opnameMain && $opnameMain->isLocked()) {
            return redirect()->back()->with('error', 'Sesi Stock Opname ini telah difinalisasi dan dikunci permanen.');
        }

        $stock_gudang = (int)$request->stock_bdg + (int)$request->stock_bks;
        $replacement = DetailProduct::find($opname->id_product);
        $sistem_total = (int)$replacement->stock + (int)$replacement->warehouse_stock;
        $opname->stock_sistem = $sistem_total;
        $opname->stock_bdg = (int)$request->stock_bdg;
        $opname->stock_bks = (int)$request->stock_bks;
        $opname->stock_gudang = $stock_gudang;
        $opname->selisih = $stock_gudang - $sistem_total;
        $opname->note = $request->note ?? '';
        $opname->save();

        return redirect()->back()->with('success', 'Stock Opname Product berhasil diperbarui!');
    }

    public function stock_replacement($id)
    {
        $product = DetailProduct::find($id);

        return response()->json([
            'stock_sistem_bdg' => $product ? (int)$product->stock : 0,
            'stock_sistem_bks' => $product ? (int)$product->warehouse_stock : 0,
            'stock_sistem' => $product ? (int)($product->stock + $product->warehouse_stock) : 0
        ]);
    }

    public function show_replacement($id)
    {
        $opname = DetailStockOpname::find($id);

        if (!$opname) {
            return response()->json([
                'message' => 'Data stock opname tidak ditemukan'
            ], 404);
        }

        $product = DetailProduct::find($opname->id_product);

        return response()->json([
            'product' => optional($product)->replacement,
            'web' => $opname->stock_sistem ?? 0,
            'gudang' => $opname->stock_gudang ?? 0,
            'bdg' => $opname->stock_bdg ?? 0,
            'bks' => $opname->stock_bks ?? 0,
            'selisih' => $opname->selisih ?? 0,
            'note' => $opname->note ?? '',
        ]);
    }
}
