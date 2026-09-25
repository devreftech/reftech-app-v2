<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\DetailExpense;
use App\Models\DetailProduct;
use App\Models\Expense;
use App\Models\FixedAsset;
use App\Models\FixedAssetConstructionCost;
use App\Models\FixedAssetService;
use App\Models\Machine;
use App\Models\Product;
use App\Models\SerialProduct;
use App\Models\Supplier;
use App\Models\Unit;
use App\Models\VehicleMaintenanceLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FixedController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $allAssets = FixedAsset::all();
        $totalNilaiPerolehan = $allAssets->sum('total');
        $totalPenyusutan = 0;
        $totalNilaiBuku = 0;

        foreach ($allAssets as $a) {
            $nb = $a->hitungNilaiBuku();
            $totalPenyusutan += $nb['total_penyusutan'];
            $totalNilaiBuku += $nb['nilai_buku'];
        }

        $totalCount = $allAssets->count();
        $assetCounts = FixedAsset::select('type', DB::raw('COUNT(*) as count'), DB::raw('SUM(total) as total_val'))
            ->groupBy('type')
            ->get()
            ->keyBy('type');

        return view('pages.finance.fixed.index', compact(
            'totalNilaiPerolehan',
            'totalPenyusutan',
            'totalNilaiBuku',
            'totalCount',
            'assetCounts'
        ));
    }

    /**
     * Data DataTable Fixed Asset, difilter per kategori (tab). Kategori Tools
     * dapat join tambahan (nama tools dari master, nama teknisi pemegang)
     * karena kolomnya beda dari kategori lain.
     */
    public function data(Request $request)
    {
        $type = $request->get('type');

        $query = FixedAsset::where('fixed_asset.type', $type)
            ->select(
                'fixed_asset.*',
                DB::raw("DATE_FORMAT(fixed_asset.beli, '%d-%m-%Y') as tanggal_beli"),
                DB::raw("DATE_FORMAT(fixed_asset.pakai, '%d-%m-%Y') as tanggal_pakai"),
                DB::raw("DATE_FORMAT(fixed_asset.tanggal_serah_terima, '%d-%m-%Y') as tanggal_serah_terima_fmt")
            );

        if ($type == 'Tools') {
            $query->leftJoin('tool_master', 'tool_master.id', '=', 'fixed_asset.id_tools_master')
                ->leftJoin('users', 'users.id', '=', 'fixed_asset.id_pic')
                ->addSelect('tool_master.nama_tools as nama_tools', 'users.name as teknisi_name');
        }

        if ($type == 'Mesin') {
            $query->leftJoin('unit', 'unit.id', '=', 'fixed_asset.id_unit')
                ->addSelect('unit.brand as unit_brand', 'unit.model as unit_model');
        }

        $data = $query->orderByDesc('fixed_asset.id')->get();

        if ($type == 'Tools') {
            $data->transform(function ($row) {
                $row->status_finance = $row->id_aktiva ? 'Lengkap' : 'Belum Lengkap';
                return $row;
            });
        }

        return response()->json(['data' => $data]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (Auth::user()?->role == 'Sales') {
            abort(403, 'Role Sales tidak memiliki izin untuk menambah Unit Acquisition baru.');
        }

        $account = Account::all();
        $suppliers = Supplier::all();
        $units = Unit::where('type', 'global')->orderBy('brand')->get();
        return view('pages.finance.fixed.form', compact('account', 'suppliers', 'units'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    /**
     * Prefix kode aset per kategori (type). Dipakai konsisten oleh generateAssetCode()
     * dan endpoint nextCode() supaya penomoran seragam: PREFIX-TAHUN-URUT (mis. KDR-2026-001).
     */
    private const ASSET_CODE_PREFIXES = [
        'Tanah' => 'TNH',
        'Bangunan' => 'BGN',
        'Kendaraan' => 'KDR',
        'Mesin' => 'MSN',
        'Peralatan Kantor' => 'AKT',
        'Tools' => 'TLS',
    ];

    public function generateAssetCode($type)
    {
        $prefix = self::ASSET_CODE_PREFIXES[$type] ?? 'AST';
        $year = date('Y');
        $pattern = "{$prefix}-{$year}-";

        $lastNumber = FixedAsset::where('type', $type)
            ->where('code', 'like', $pattern . '%')
            ->selectRaw('MAX(CAST(SUBSTRING(code, ?) AS UNSIGNED)) as max_num', [strlen($pattern) + 1])
            ->value('max_num');

        $next = ($lastNumber ?? 0) + 1;

        return $pattern . str_pad($next, 3, '0', STR_PAD_LEFT);
    }

    /**
     * AJAX: kasih preview kode aset berikutnya buat kategori terpilih, dipanggil
     * dari form.blade.php saat dropdown kategori berubah. Kode ini tetap editable
     * di form — ini cuma nilai default/saran.
     */
    public function nextCode(Request $request)
    {
        $type = $request->query('type');
        if (! $type) {
            return response()->json(['code' => '']);
        }

        return response()->json(['code' => $this->generateAssetCode($type)]);
    }

    public function store(Request $request)
    {
        if (Auth::user()?->role == 'Sales') {
            abort(403, 'Role Sales tidak memiliki izin untuk menambah Unit Acquisition baru.');
        }

        if ($request->type == 'Mesin') {
            return redirect('/unit-product-in/create')->with('error', 'Unit (kategori Mesin) sekarang diinput lewat Barang Masuk Unit, bukan lewat form Fixed Asset.');
        }

        if ($request->type == 'Kendaraan') {
            $request->validate([
                'plat_nomor' => 'required',
            ], [
                'plat_nomor.required' => 'Plat nomor kendaraan wajib diisi.',
            ]);
        }

        $tglBeli = $request->beli ?: ($request->pay ?: ($request->date ?: now()->toDateString()));
        $tglBayar = $request->bayar ?: ($request->date ?: ($request->pay ?: $tglBeli));
        $tglPakai = $request->pakai ?: $tglBeli;

        $totalInput = $request->total;
        if ((!$totalInput || $totalInput == 0) && $request->harga) {
            $totalInput = (float) str_replace(['.', ','], ['', '.'], $request->harga);
        }

        $fixed = new FixedAsset;
        $fixed->id_aktiva = $request->aktiva;
        $fixed->id_penyusutan = $request->penyusutan;
        $fixed->id_beban = $request->beban;
        $fixed->id_supplier = $request->supplier;
        $fixed->id_pengeluaran = $request->bank;
        $fixed->type = $request->type;
        $fixed->code = $request->code ?: $this->generateAssetCode($request->type);
        $fixed->no_invoice = $request->no_invoice;
        $fixed->beli = $tglBeli;
        $fixed->pakai = $tglPakai;
        $fixed->bayar = $tglBayar;
        $fixed->metode = $request->metode ?: 'Metode Garis Lurus';
        $fixed->desc = $request->desc;
        $fixed->umur = $request->umur ?: ($request->type === 'Bangunan' ? 240 : 48);
        $fixed->qty = $request->qty ?: 1;
        $fixed->total = $totalInput ?: 0;
        $fixed->status = $request->status ?? 0;
        $fixed->mulai_penyusutan = $tglBeli;

        if ($request->type == 'Kendaraan') {
            $fixed->jenis_kendaraan = $request->jenis_kendaraan;
            $fixed->merk_model = $request->merk_model;
            $fixed->bahan_bakar = $request->bahan_bakar;
            $fixed->plat_nomor = $request->plat_nomor;
            $fixed->atas_nama = $request->atas_nama;
        }

        if ($request->type == 'Bangunan') {
            $fixed->status_bangunan = $request->status_bangunan ?: 'operational';
            $fixed->tipe_pengadaan = $request->tipe_pengadaan ?: 'beli_jadi';
            $fixed->lokasi_bangunan = $request->lokasi_bangunan;
            $fixed->luas_bangunan = $request->luas_bangunan;
            $fixed->luas_tanah = $request->luas_tanah;
            $fixed->nomor_dokumen_legalitas = $request->nomor_dokumen_legalitas;
            if ($request->status_bangunan === 'construction') {
                $fixed->mulai_penyusutan = null;
            }
        }

        $fixedSave = $fixed->save();
        if ($fixedSave) {
            return redirect('fixed')->with('success', 'Data aset tetap berhasil ditambahkan');
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
        $fixed = FixedAsset::with(['unit', 'aktiva', 'penyusutan', 'beban', 'pengeluaran', 'supplier', 'toolsMaster', 'pic'])->findOrFail($id);
        $services = FixedAssetService::where('id_fixed_asset', $id)->with('detailProduct.product')->orderByDesc('date')->orderByDesc('id')->get();
        $workOrders = \App\Models\WorkOrder::where('id_fixed_asset', $id)
            ->with([
                'creator',
                'technician',
                'items.detailProduct.product.serial',
                'items.product.serial',
                'items.equivalent',
                'productOut'
            ])
            ->orderByDesc('id')
            ->get();
        $suppliers = Supplier::orderBy('supplier')->get();
        $maintenanceLogs = $fixed->type === 'Kendaraan' ? $fixed->maintenanceLogs()->get() : collect();

        // Riwayat & Ringkasan Biaya Konstruksi / Proyek (Khusus Kategori Bangunan)
        $constructionCosts = $fixed->type === 'Bangunan'
            ? $fixed->constructionCosts()->with(['supplier', 'pengeluaran', 'beban', 'expense', 'creator'])->get()
            : collect();
        $totalMaterialCost = $constructionCosts->where('kategori_biaya', 'material')->sum('total_biaya');
        $totalJasaCost = $constructionCosts->whereIn('kategori_biaya', ['jasa', 'operasional'])->sum('total_biaya');
        $totalProjectCost = $totalMaterialCost + $totalJasaCost;
        $accounts = Account::all();

        $hitung = $fixed->hitungNilaiBuku();
        $totalPenyusutan = $hitung['total_penyusutan'];
        $nilaiBuku = $hitung['nilai_buku'];

        // Penawaran Deal (Smart Quote / Rental / Penjualan yang statusnya po_received dan merujuk unit ini)
        $confirmedOffers = \App\Models\UnitQuotation::where('status', 'po_received')
            ->whereHas('details', fn ($q) => $q->where('id_fixed_asset', $fixed->id))
            ->with(['client', 'details' => fn ($q) => $q->where('id_fixed_asset', $fixed->id)])
            ->orderByDesc('id')
            ->get();

        // Ringkasan Finansial Keseluruhan (Unit Economics)
        $totalRevenue = $confirmedOffers->flatMap(fn ($q) => $q->details->where('id_fixed_asset', $fixed->id))->sum('amount');
        $totalWoCost = $workOrders->sum('total_cost');
        $totalVehicleCost = $maintenanceLogs->sum('biaya');
        $totalMaintenanceCost = $totalWoCost + $totalVehicleCost;
        $netAssetProfit = $totalRevenue - ($totalMaintenanceCost + $totalPenyusutan);

        // Perhitungan Breakdown Kinerja Finansial Per Tahun (Annual Breakdown)
        $startDate = \Carbon\Carbon::parse($fixed->mulai_penyusutan ?? ($fixed->beli ?? $fixed->created_at));
        $startYear = (int) $startDate->format('Y');
        $currentYear = (int) \Carbon\Carbon::now()->format('Y');

        $penyusutanPerBulan = $fixed->umur > 0 ? ($fixed->total / $fixed->umur) : (($fixed->total * 0.25) / 12);
        $annualBreakdown = [];
        $accumulatedDepr = 0;
        $maxDepr = (float) $fixed->total;

        for ($y = $startYear; $y <= $currentYear; $y++) {
            $yearStart = \Carbon\Carbon::createFromDate($y, 1, 1)->startOfDay();
            $yearEnd = \Carbon\Carbon::createFromDate($y, 12, 31)->endOfDay();

            $activeStart = $startDate->greaterThan($yearStart) ? $startDate->copy() : $yearStart->copy();
            $activeEnd = $y === $currentYear ? \Carbon\Carbon::now() : $yearEnd->copy();

            $monthsInYear = 0;
            if ($activeStart->lessThanOrEqualTo($activeEnd)) {
                $monthsInYear = $activeStart->diffInMonths($activeEnd);
            }

            $deprThisYear = ($fixed->qc_status === 'checking' || $fixed->status_bangunan === 'construction') ? 0 : ($penyusutanPerBulan * $monthsInYear);
            if ($accumulatedDepr + $deprThisYear > $maxDepr) {
                $deprThisYear = max(0, $maxDepr - $accumulatedDepr);
            }
            $accumulatedDepr += $deprThisYear;

            $woCostYear = $workOrders->filter(function ($wo) use ($y) {
                $d = $wo->date ? \Carbon\Carbon::parse($wo->date) : $wo->created_at;
                return $d && (int) $d->format('Y') === $y;
            })->sum('total_cost');

            $vehCostYear = $maintenanceLogs->filter(function ($log) use ($y) {
                $d = $log->tanggal ? \Carbon\Carbon::parse($log->tanggal) : $log->created_at;
                return $d && (int) $d->format('Y') === $y;
            })->sum('biaya');

            $maintThisYear = $woCostYear + $vehCostYear;

            $revThisYear = $confirmedOffers->filter(function ($quote) use ($y) {
                $d = $quote->date ? \Carbon\Carbon::parse($quote->date) : $quote->created_at;
                return $d && (int) $d->format('Y') === $y;
            })->flatMap(function ($q) use ($fixed) {
                return $q->details->where('id_fixed_asset', $fixed->id);
            })->sum('amount');

            $netMarginYear = $revThisYear - ($maintThisYear + $deprThisYear);

            $annualBreakdown[] = [
                'year' => $y,
                'depreciation' => $deprThisYear,
                'maintenance' => $maintThisYear,
                'revenue' => $revThisYear,
                'net_profit' => $netMarginYear,
            ];
        }

        $allUsers = \App\Models\User::orderBy('name')->get();

        return view('pages.finance.fixed.detail', compact(
            'fixed',
            'totalPenyusutan',
            'nilaiBuku',
            'services',
            'workOrders',
            'maintenanceLogs',
            'suppliers',
            'confirmedOffers',
            'totalRevenue',
            'totalMaintenanceCost',
            'netAssetProfit',
            'annualBreakdown',
            'constructionCosts',
            'totalMaterialCost',
            'totalJasaCost',
            'totalProjectCost',
            'accounts',
            'allUsers'
        ));
    }

    /**
     * Catat Biaya Proyek Pembangunan / Konstruksi Bangunan (Material vs Jasa).
     */
    public function storeConstructionCost(Request $request, $id)
    {
        $fixed = FixedAsset::findOrFail($id);

        $request->validate([
            'tanggal' => 'required|date',
            'kategori_biaya' => 'required|in:material,jasa,operasional',
            'nama_item' => 'required|string|max:255',
            'total_biaya' => 'required',
            'id_pengeluaran' => 'nullable|exists:account,id',
            'id_beban' => 'nullable|exists:account,id',
            'foto_bukti' => 'nullable|file|mimes:jpeg,png,jpg,pdf,webp|max:5120',
        ]);

        $totalBiaya = (float) str_replace(['.', ','], ['', '.'], $request->total_biaya);
        $qty = $request->qty ? (float) $request->qty : 1;
        $hargaSatuan = $request->harga_satuan ? (float) str_replace(['.', ','], ['', '.'], $request->harga_satuan) : ($totalBiaya / max(1, $qty));

        $fotoPath = null;
        if ($request->hasFile('foto_bukti')) {
            $file = $request->file('foto_bukti');
            $filename = 'cost_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/construction_costs'), $filename);
            $fotoPath = 'uploads/construction_costs/' . $filename;
        }

        $expenseId = null;

        // Jika Jasa / Operasional -> Catat ke modul Expense agar masuk beban keuangan
        if (in_array($request->kategori_biaya, ['jasa', 'operasional'])) {
            $bankId = null;
            if ($request->id_pengeluaran) {
                $bankObj = \App\Models\Bank::where('id_account', $request->id_pengeluaran)->first();
                $bankId = $bankObj?->id;
            }

            $expense = new Expense();
            $expense->id_bank = $bankId;
            $expense->no_invoice = $request->no_bukti ?: ('EXP-BGN-' . date('Ymd') . '-' . rand(100, 999));
            $expense->date = $request->tanggal;
            $expense->memo = "[Proyek Bangunan: {$fixed->desc}] " . $request->nama_item;
            $expense->payee = $request->payee ?: ($request->supplier_id ? Supplier::find($request->supplier_id)?->supplier : 'Upah / Jasa Konstruksi');
            $expense->amount = $totalBiaya;
            $expense->save();

            $dExpense = new DetailExpense();
            $dExpense->id_expense = $expense->id;
            $dExpense->id_account = $request->id_beban ?: ($fixed->id_beban ?: Account::where('name', 'like', '%Beban%')->value('id'));
            $dExpense->amount = $totalBiaya;
            $dExpense->memo = $request->nama_item;
            $dExpense->save();

            $expenseId = $expense->id;
        }

        // Jika Material -> Masuk Aktiva Tetap (Kapitalisasi nilai bangunan)
        if ($request->kategori_biaya === 'material') {
            $fixed->total = (float) $fixed->total + $totalBiaya;
            $fixed->save();
        }

        FixedAssetConstructionCost::create([
            'fixed_asset_id' => $fixed->id,
            'tanggal' => $request->tanggal,
            'kategori_biaya' => $request->kategori_biaya,
            'nama_item' => $request->nama_item,
            'supplier_id' => $request->supplier_id ?: null,
            'payee' => $request->payee,
            'qty' => $qty,
            'satuan' => $request->satuan,
            'harga_satuan' => $hargaSatuan,
            'total_biaya' => $totalBiaya,
            'no_bukti' => $request->no_bukti,
            'foto_bukti' => $fotoPath,
            'id_pengeluaran' => $request->id_pengeluaran ?: null,
            'id_beban' => $request->id_beban ?: null,
            'expense_id' => $expenseId,
            'catatan' => $request->catatan,
            'created_by' => Auth::id(),
        ]);

        $categoryLabel = $request->kategori_biaya === 'material' ? 'Material (dikapitalisasi ke Aset Tetap)' : 'Jasa/Operasional (dicatat ke Expense)';

        return redirect()->back()->with('success', "Biaya {$categoryLabel} sebesar Rp " . number_format($totalBiaya, 0, ',', '.') . " berhasil dicatat.");
    }

    /**
     * Hapus Catatan Biaya Proyek Bangunan.
     */
    public function destroyConstructionCost($id, $costId)
    {
        $fixed = FixedAsset::findOrFail($id);
        $cost = FixedAssetConstructionCost::where('fixed_asset_id', $id)->findOrFail($costId);

        // Jika kategori material, kembalikan nilai perolehan aktiva
        if ($cost->kategori_biaya === 'material') {
            $fixed->total = max(0, (float) $fixed->total - (float) $cost->total_biaya);
            $fixed->save();
        }

        // Jika ada relasi expense, hapus expense-nya
        if ($cost->expense_id) {
            DetailExpense::where('id_expense', $cost->expense_id)->delete();
            Expense::where('id', $cost->expense_id)->delete();
        }

        // Hapus file bukti jika ada
        if ($cost->foto_bukti && file_exists(public_path($cost->foto_bukti))) {
            @unlink(public_path($cost->foto_bukti));
        }

        $cost->delete();

        return redirect()->back()->with('success', 'Catatan biaya proyek berhasil dihapus.');
    }

    /**
     * Selesai Pembangunan & Aktifkan Aset (Handover).
     */
    public function finishConstruction(Request $request, $id)
    {
        $request->validate([
            'tanggal_selesai' => 'required|date',
        ]);

        $fixed = FixedAsset::findOrFail($id);
        $fixed->status_bangunan = 'operational';
        $fixed->pakai = $request->tanggal_selesai;
        $fixed->mulai_penyusutan = $request->tanggal_selesai;
        $fixed->save();

        return redirect()->back()->with('success', 'Pembangunan selesai! Status aset bangunan berhasil diubah menjadi Siap Operasional dan jadwal penyusutan telah diaktifkan.');
    }

    /**
     * Update Akun / PIC yang diizinkan untuk input biaya proyek pembangunan.
     */
    public function updateConstructionPics(Request $request, $id)
    {
        $fixed = FixedAsset::findOrFail($id);

        $picIds = $request->input('pic_construction_ids', []);
        $picIds = is_array($picIds) ? array_map('intval', array_filter($picIds)) : [];

        $fixed->pic_construction_ids = !empty($picIds) ? array_values(array_unique($picIds)) : null;
        $fixed->save();

        return redirect()->back()->with('success', 'Petugas (PIC) penginput biaya proyek pembangunan berhasil disimpan.');
    }

    /**
     * Update Supplier aset tetap via modal.
     */
    public function updateSupplier(Request $request, $id)
    {
        $request->validate([
            'id_supplier' => 'nullable|exists:supplier,id',
        ]);

        $fixed = FixedAsset::findOrFail($id);
        $fixed->id_supplier = $request->id_supplier ?: null;
        $fixed->save();

        return redirect()->back()->with('success', 'Data supplier aset tetap berhasil diperbarui.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $fixed = FixedAsset::findOrFail($id);
        $account = Account::all();
        $suppliers = Supplier::all();
        $units = Unit::where('type', 'global')->orderBy('brand')->get();
        return view('pages.finance.fixed.form', compact('fixed', 'account', 'suppliers', 'units'));
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
        $fixed = FixedAsset::findOrFail($id);

        $tglBeli = $request->beli ?: ($request->pay ?: ($request->date ?: $fixed->beli));
        $tglBayar = $request->bayar ?: ($request->date ?: ($request->pay ?: ($fixed->bayar ?: $tglBeli)));
        $tglPakai = $request->pakai ?: ($fixed->pakai ?: $tglBeli);

        $totalInput = $request->total;
        if ((!$totalInput || $totalInput == 0) && $request->harga) {
            $totalInput = (float) str_replace(['.', ','], ['', '.'], $request->harga);
        }

        $fixed->id_aktiva = $request->aktiva ?: $fixed->id_aktiva;
        $fixed->id_penyusutan = $request->penyusutan ?: $fixed->id_penyusutan;
        $fixed->id_beban = $request->beban ?: $fixed->id_beban;
        $fixed->id_supplier = $request->supplier ?: $fixed->id_supplier;
        $fixed->id_pengeluaran = $request->bank ?: $fixed->id_pengeluaran;
        if ($request->code) $fixed->code = $request->code;
        $fixed->no_invoice = $request->no_invoice ?: $fixed->no_invoice;
        $fixed->beli = $tglBeli;
        $fixed->pakai = $tglPakai;
        $fixed->bayar = $tglBayar;
        if ($request->metode) $fixed->metode = $request->metode;
        if ($request->desc) $fixed->desc = $request->desc;
        if ($request->umur) $fixed->umur = $request->umur;
        if ($request->qty) $fixed->qty = $request->qty;
        if ($totalInput !== null && $totalInput !== '') $fixed->total = $totalInput;
        $fixed->status = $request->status ?? $fixed->status;
        $fixed->mulai_penyusutan = $fixed->mulai_penyusutan ?: $tglBeli;

        if ($fixed->type == 'Mesin') {
            $fixed->id_unit = $request->id_unit ?: $fixed->id_unit;
            $fixed->serial_number = $request->serial_number ?: $fixed->serial_number;
        }

        if ($fixed->type == 'Kendaraan') {
            $fixed->jenis_kendaraan = $request->jenis_kendaraan ?: $fixed->jenis_kendaraan;
            $fixed->merk_model = $request->merk_model ?: $fixed->merk_model;
            $fixed->bahan_bakar = $request->bahan_bakar ?: $fixed->bahan_bakar;
            $fixed->plat_nomor = $request->plat_nomor ?: $fixed->plat_nomor;
            $fixed->atas_nama = $request->atas_nama ?: $fixed->atas_nama;
        }

        if ($fixed->type == 'Bangunan') {
            $fixed->status_bangunan = $request->status_bangunan ?: $fixed->status_bangunan;
            $fixed->tipe_pengadaan = $request->tipe_pengadaan ?: $fixed->tipe_pengadaan;
            $fixed->lokasi_bangunan = $request->lokasi_bangunan ?: $fixed->lokasi_bangunan;
            $fixed->luas_bangunan = $request->luas_bangunan ?: $fixed->luas_bangunan;
            $fixed->luas_tanah = $request->luas_tanah ?: $fixed->luas_tanah;
            $fixed->nomor_dokumen_legalitas = $request->nomor_dokumen_legalitas ?: $fixed->nomor_dokumen_legalitas;
            if ($request->status_bangunan === 'construction') {
                $fixed->mulai_penyusutan = null;
            }
        }

        if ($fixed->type == 'Tools') {
            if ($request->has('id_tools_master') && $request->id_tools_master) $fixed->id_tools_master = $request->id_tools_master;
            if ($request->has('id_pic') && $request->id_pic) $fixed->id_pic = $request->id_pic;
            if ($request->has('tanggal_serah_terima') && $request->tanggal_serah_terima) $fixed->tanggal_serah_terima = $request->tanggal_serah_terima;
        }

        $fixed->save();

        $redirectTo = $request->get('redirect_to');
        if ($redirectTo) {
            return redirect($redirectTo)->with('success', 'Data aset berhasil diupdate');
        }

        return redirect('/fixed/' . $id)->with('success', 'Data aset berhasil diupdate');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $fixed = FixedAsset::find($id);
        if (!$fixed) {
            return 0;
        }

        try {
            $fixedDel = $fixed->delete();
            return $fixedDel ? 1 : 0;
        } catch (\Throwable $e) {
            return 0;
        }
    }

    /**
     * Konfirmasi unit lolos QC (OK) atau ditolak (Reject) — khusus Admin.
     * Penyusutan baru mulai dihitung sejak titik konfirmasi OK, bukan dari tanggal beli.
     */
    public function confirm(Request $request, $id)
    {
        if (Auth::user()->role != 'Admin') {
            abort(403, 'Hanya Admin yang bisa konfirmasi unit ini.');
        }

        $fixed = FixedAsset::find($id);
        if (!$fixed || $fixed->qc_status !== 'checking') {
            return response()->json(['error' => 'Unit tidak ditemukan atau sudah dikonfirmasi'], 404);
        }

        $decision = $request->decision == 'ok' ? 'ok' : 'reject';
        $fixed->qc_status = $decision;
        $fixed->confirmed_by = Auth::user()->id;
        $fixed->confirmed_at = now();
        if ($decision == 'ok') {
            $fixed->mulai_penyusutan = now();
            $fixed->status_unit = 'OK';
        }
        $fixed->save();

        if ($decision == 'ok') {
            $this->linkMachine($fixed);
        }

        return redirect('/unit-acquisition/' . $id)->with('success', 'Unit berhasil dikonfirmasi: ' . strtoupper($decision));
    }

    /**
     * Bikin (atau pakai yang sudah ada) Machine yang mewakili unit ini, supaya
     * bisa nyambung ke modul Service Report yang sudah ada (Reports.id_machine).
     * SerialProduct di-reuse per Unit (1 SerialProduct = 1 spek Unit Global,
     * dipakai bersama banyak Machine) — pola yang sama seperti UnitController.
     * Machine LAMA tidak pernah disentuh; ini cuma nambah baris baru.
     */
    private function linkMachine(FixedAsset $fixed): void
    {
        if ($fixed->id_machine || !$fixed->id_unit) {
            return;
        }

        $unit = Unit::find($fixed->id_unit);
        if (!$unit) {
            return;
        }

        $serialProduct = SerialProduct::firstOrCreate(
            ['id_product' => $unit->id],
            [
                'fxp_parts' => '-',
                'brand' => $unit->brand ?: ($unit->unit ?: '-'),
                'pn' => $unit->sku ?: '-',
                'image' => '-',
                'air_cap' => $unit->air_cap,
                'bar' => $unit->bar,
            ]
        );

        $machine = Machine::create([
            'id_client' => 5387,
            'id_unit' => $serialProduct->id,
            'serial' => $fixed->serial_number ?: $fixed->code,
            'desc' => $fixed->desc ?: '-',
            'status' => 'Ready',
        ]);

        $fixed->id_machine = $machine->id;
        $fixed->save();
    }

    /**
     * Ubah status ketersediaan unit (OK/Service/Rental/Sold) — khusus Admin.
     * Ini terpisah dari qc_status (yang soal lolos-tidaknya QC awal).
     */
    public function updateStatusUnit(Request $request, $id)
    {
        if (Auth::user()->role != 'Admin') {
            abort(403, 'Hanya Admin yang bisa mengubah status unit ini.');
        }

        $fixed = FixedAsset::find($id);
        if (!$fixed) {
            return response()->json(['error' => 'Unit tidak ditemukan'], 404);
        }

        $allowed = ['OK', 'Service', 'Rental', 'Breakdown', 'Reserved', 'Sold'];
        if (!in_array($request->status_unit, $allowed)) {
            return redirect()->back()->with('error', 'Status tidak valid');
        }

        $fixed->status_unit = $request->status_unit;
        $fixed->save();

        $message = 'Status unit diperbarui menjadi ' . $request->status_unit;
        if ($request->status_unit == 'Rental') {
            $message .= ' — jangan lupa buat Service Report (pengecekan sebelum disewakan).';
        }

        return redirect('/unit-acquisition/' . $id)->with('success', $message);
    }

    /**
     * Set/ubah harga unit (harga jual, rental per hari, rental per bulan) — khusus Admin.
     */
    public function updatePricing(Request $request, $id)
    {
        if (Auth::user()->role != 'Admin') {
            abort(403, 'Hanya Admin yang bisa mengubah pengaturan harga unit ini.');
        }

        $request->validate([
            'harga_jual'         => 'nullable|numeric|min:0',
            'harga_rental_hari'  => 'nullable|numeric|min:0',
            'harga_rental_bulan' => 'nullable|numeric|min:0',
        ]);

        $fixed = FixedAsset::find($id);
        if (!$fixed) {
            return response()->json(['error' => 'Unit tidak ditemukan'], 404);
        }

        if ($request->has('harga_jual')) {
            $fixed->harga_jual = $request->harga_jual ?: null;
        }
        if ($request->has('harga_rental_hari')) {
            $fixed->harga_rental_hari = $request->harga_rental_hari ?: null;
        }
        if ($request->has('harga_rental_bulan')) {
            $fixed->harga_rental_bulan = $request->harga_rental_bulan ?: null;
        }

        $fixed->save();

        return redirect('/unit-acquisition/' . $id)->with('success', 'Pengaturan harga jual & rental unit berhasil diperbarui.');
    }

    public function updateHargaJual(Request $request, $id)
    {
        return $this->updatePricing($request, $id);
    }

    /**
     * Form input pemakaian spare part untuk servis unit (hanya selama masih Dalam Pengecekan —
     * biayanya numpuk ke harga pokok unit / total. Servis setelah lolos QC sebaiknya
     * dicatat sebagai Beban Pemeliharaan lewat menu Expense, bukan di sini.
     */
    /**
     * Unit Second cuma boleh nambah biaya (numpuk ke harga pokok) selama masih
     * "Dalam Pengecekan". Unit Baru tidak pernah lewat status checking sama sekali
     * (langsung ok), jadi pintu biayanya sengaja dibiarkan terbuka terus.
     */
    private function canCapitalizeService(FixedAsset $fixed): bool
    {
        if ($fixed->kondisi == 'Baru') {
            return true;
        }
        return $fixed->qc_status === 'checking';
    }

    public function createService($id)
    {
        $fixed = FixedAsset::find($id);
        if (!$fixed || !$this->canCapitalizeService($fixed)) {
            return redirect('/unit-acquisition/' . $id)->with('error', 'Unit ini sudah lolos QC — servis lanjutan dicatat sebagai Beban Pemeliharaan lewat menu Expense, bukan di sini.');
        }
        $product = SerialProduct::join('product', 'serial_product.id_product', '=', 'product.id')->get('serial_product.*');
        return view('pages.warehouse.unit-acquisition.form-service', compact('fixed', 'product'));
    }

    public function storeService(Request $request, $id)
    {
        $fixed = FixedAsset::find($id);
        if (!$fixed || !$this->canCapitalizeService($fixed)) {
            return redirect('/unit-acquisition/' . $id)->with('error', 'Unit ini sudah lolos QC — servis lanjutan dicatat sebagai Beban Pemeliharaan lewat menu Expense, bukan di sini.');
        }

        foreach ($request->equivalent as $item => $value) {
            $productD = DetailProduct::findOrFail($request->replacement[$item]);

            if ($request->warehouse[$item] == 'BDG') {
                $productD->stock -= $request->qty[$item];
            } else {
                $productD->warehouse_stock -= $request->qty[$item];
            }
            $productD->save();

            $product = Product::find($productD->id_product);
            if ($product) {
                if ($request->warehouse[$item] == 'BDG') {
                    $product->stock -= $request->qty[$item];
                } else {
                    $product->warehouse_stock -= $request->qty[$item];
                }
                $product->save();
            }

            $service = new FixedAssetService();
            $service->id_fixed_asset = $id;
            $service->id_detail_product = $request->replacement[$item];
            $service->warehouse = $request->warehouse[$item];
            $service->qty = $request->qty[$item];
            $service->price = $request->price[$item];
            $service->amount = $request->amount[$item];
            $service->note = $request->note;
            $service->date = $request->date;
            $service->created_by = Auth::user()->id;
            $service->save();

            $fixed->total = $fixed->total + $request->amount[$item];
        }
        $fixed->save();

        return redirect('/unit-acquisition/' . $id)->with('success', 'Biaya servis berhasil ditambahkan ke harga pokok unit');
    }

    /**
     * Form catat riwayat perawatan kendaraan (Servis / STNK & Pajak / Ganti Kaleng) — khusus type "Kendaraan".
     */
    public function createMaintenanceLog($id)
    {
        $fixed = FixedAsset::find($id);
        if (!$fixed || $fixed->type !== 'Kendaraan') {
            return redirect('/fixed/' . $id)->with('error', 'Riwayat perawatan hanya berlaku untuk kategori Kendaraan.');
        }
        return view('pages.finance.fixed.form-maintenance', compact('fixed'));
    }

    public function storeMaintenanceLog(Request $request, $id)
    {
        $fixed = FixedAsset::find($id);
        if (!$fixed || $fixed->type !== 'Kendaraan') {
            return redirect('/fixed/' . $id)->with('error', 'Riwayat perawatan hanya berlaku untuk kategori Kendaraan.');
        }

        $request->validate([
            'jenis' => 'required',
            'tanggal' => 'required|date',
        ]);

        $log = new VehicleMaintenanceLog();
        $log->id_fixed_asset = $id;
        $log->jenis = $request->jenis;
        $log->tanggal = $request->tanggal;
        $log->tanggal_jatuh_tempo = $request->tanggal_jatuh_tempo ?: null;
        $log->biaya = $request->biaya ?: null;
        $log->catatan = $request->catatan;
        $log->created_by = Auth::user()->id;
        $log->save();

        return redirect('/fixed/' . $id)->with('success', 'Riwayat perawatan kendaraan berhasil ditambahkan');
    }

    /**
     * Unit Acquisition (E-Stock) — daftar semua akuisisi unit second (kategori "Mesin"),
     * apapun status QC-nya.
     */
    public function indexUnitAcquisition()
    {
        return view('pages.warehouse.unit-acquisition.index');
    }

    public function showUnitAcquisition($id)
    {
        $fixed = FixedAsset::with('unit', 'rentalScans.client', 'rentalScans.picInternal', 'rentalScans.scannedBy')
            ->find($id);
        if (!$fixed) {
            return redirect()->route('unit-acquisition.index')->with('error', 'Unit Acquisition tidak ditemukan');
        }
        $services = FixedAssetService::where('id_fixed_asset', $id)->with('detailProduct.product')->get();

        $hitung = $fixed->hitungNilaiBuku();
        $totalPenyusutan = $hitung['total_penyusutan'];
        $nilaiBuku = $hitung['nilai_buku'];

        // Data buat aksi scan Rental (Jadikan Rental / Terima Kembali) — halaman ini
        // JUGA yang dibuka pas QR barcode unit di-scan, lihat barcodeImage().
        $lastOutScan = $fixed->rentalScans->firstWhere('action', 'out');
        // 'details' di-eager-load KHUSUS baris yang nyebut unit ini aja (bukan semua
        // item quotation) — dipakai buat nampilin nilai item unitnya doang, bukan
        // total keseluruhan quotation (yang bisa kecampur item lain kayak dryer dst).
        // Ambil semua quotation (Smart Quote) yang merujuk unit fisik ini
        $confirmedOffers = \App\Models\UnitQuotation::whereHas('details', fn ($q) => $q->where('id_fixed_asset', $fixed->id))
            ->with(['client', 'details' => fn ($q) => $q->where('id_fixed_asset', $fixed->id)])
            ->orderByDesc('id')
            ->get();

        $workOrders = \App\Models\WorkOrder::where('id_fixed_asset', $id)
            ->with(['creator', 'technician', 'items.detailProduct.product', 'productOut'])
            ->orderByDesc('id')
            ->get();

        return view('pages.warehouse.unit-acquisition.show', compact('fixed', 'totalPenyusutan', 'nilaiBuku', 'services', 'lastOutScan', 'confirmedOffers', 'workOrders'));
    }

    /**
     * Aksi scan barcode/QR unit Fixed Asset (Jadikan Rental) — QR-nya encode URL
     * ke halaman detail unit (unit-acquisition.show, lihat barcodeImage()), formnya
     * nempel di situ juga, jadi setelah submit balik lagi ke halaman yang sama.
     */
    public function scanStoreOut(Request $request, $id)
    {
        $fixed = FixedAsset::where('type', 'Mesin')->findOrFail($id);

        if ($fixed->status_unit !== 'OK') {
            return redirect()->back()->with('error', 'Unit ini statusnya bukan OK, gak bisa dijadikan Rental dari sini.');
        }

        $this->validate($request, [
            'id_client' => 'required|exists:client,id',
            'note' => 'nullable|string|max:255',
        ]);

        \App\Models\FixedAssetRentalScan::create([
            'id_fixed_asset' => $fixed->id,
            'action' => 'out',
            'id_client' => $request->id_client,
            // PIC Internal = akun yang lagi login pas scan, bukan dropdown pilihan.
            'id_pic_internal' => Auth::id(),
            'scanned_by' => Auth::id(),
            'note' => $request->note,
        ]);

        $fixed->status_unit = 'Rental';
        $fixed->save();

        return redirect()->route('unit-acquisition.show', $fixed->id)->with('success', 'Unit berhasil dijadikan Rental.');
    }

    public function scanStoreIn(Request $request, $id)
    {
        $fixed = FixedAsset::where('type', 'Mesin')->findOrFail($id);

        if ($fixed->status_unit !== 'Rental') {
            return redirect()->back()->with('error', 'Unit ini statusnya bukan Rental, gak bisa diterima kembali dari sini.');
        }

        // Client/PIC internal-nya nyalin dari baris "out" terakhir — gak perlu
        // dipilih ulang, cuma konfirmasi unit fisiknya udah balik.
        $lastOutScan = \App\Models\FixedAssetRentalScan::where('id_fixed_asset', $fixed->id)
            ->where('action', 'out')
            ->orderByDesc('id')
            ->first();

        \App\Models\FixedAssetRentalScan::create([
            'id_fixed_asset' => $fixed->id,
            'action' => 'in',
            'id_client' => $lastOutScan->id_client ?? null,
            'id_pic_internal' => $lastOutScan->id_pic_internal ?? null,
            'scanned_by' => Auth::id(),
            'note' => $request->note,
        ]);

        $fixed->status_unit = 'OK';
        $fixed->save();

        return redirect()->route('unit-acquisition.show', $fixed->id)->with('success', 'Unit berhasil diterima kembali, status jadi OK.');
    }

    /**
     * Generate QR code (PNG) yang encode URL ke scanForm() — ini yang di-print
     * jadi label barcode fisik nempel di unit-nya.
     */
    public function barcodeImage($id)
    {
        $fixed = FixedAsset::where('type', 'Mesin')->findOrFail($id);
        $url = route('unit-acquisition.show', $fixed->id);

        $result = (new \Endroid\QrCode\Builder\Builder())->build(
            data: $url,
            size: 320,
            margin: 10,
        );

        return response($result->getString(), 200)->header('Content-Type', $result->getMimeType());
    }
}
