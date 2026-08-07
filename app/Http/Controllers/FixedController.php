<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\DetailProduct;
use App\Models\FixedAsset;
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
        return view('pages.finance.fixed.index');
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

        // dd($request->all());
        $fixed = new FixedAsset;
        $fixed->id_aktiva = $request->aktiva;
        $fixed->id_penyusutan = $request->penyusutan;
        $fixed->id_beban = $request->beban;
        $fixed->id_supplier = $request->supplier;
        $fixed->id_pengeluaran = $request->bank;
        $fixed->type = $request->type;
        $fixed->code = $request->code ?: $this->generateAssetCode($request->type);
        $fixed->no_invoice = $request->no_invoice;
        $fixed->beli = $request->pay;
        $fixed->pakai = $request->pakai;
        $fixed->bayar = $request->date;
        $fixed->metode = $request->metode;
        $fixed->desc = $request->desc;
        $fixed->umur = $request->umur;
        $fixed->qty = $request->qty;
        $fixed->total = $request->total;
        $fixed->status = $request->status ?? 0;

        if ($request->type == 'Kendaraan') {
            $fixed->jenis_kendaraan = $request->jenis_kendaraan;
            $fixed->merk_model = $request->merk_model;
            $fixed->bahan_bakar = $request->bahan_bakar;
            $fixed->plat_nomor = $request->plat_nomor;
            $fixed->atas_nama = $request->atas_nama;
            $fixed->mulai_penyusutan = $fixed->beli;
        } else {
            $fixed->mulai_penyusutan = $fixed->beli;
        }

        $fixedSave = $fixed->save();
        if ($fixedSave) {
            return redirect('fixed')->with('success', 'data telah di tambahkan');
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
        $fixed = FixedAsset::find($id);
        $services = FixedAssetService::where('id_fixed_asset', $id)->with('detailProduct.product')->get();
        $maintenanceLogs = $fixed->type === 'Kendaraan' ? $fixed->maintenanceLogs()->get() : collect();

        $hitung = $fixed->hitungNilaiBuku();
        $totalPenyusutan = $hitung['total_penyusutan'];
        $nilaiBuku = $hitung['nilai_buku'];

        return view('pages.finance.fixed.detail', compact('fixed', 'totalPenyusutan', 'nilaiBuku', 'services', 'maintenanceLogs'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $fixed = FixedAsset::find($id);
        $account = Account::all();
        $suppliers = Supplier::all();
        $units = Unit::where('type', 'global')->orderBy('brand')->get();
        return view('pages.finance.fixed.form', compact('fixed', 'account', 'suppliers', 'units'));
    }

    /**
     * Update the specified resource in storage.
     *
     * Kategori (type) dan kondisi (Second/Baru) sengaja tidak diubah di sini —
     * dua field itu menentukan alur QC/penyusutan dan dikirim sebagai hidden
     * input read-only dari form. Edit ini cuma untuk membetulkan data yang
     * salah input (kode, tanggal, harga, akun, dst), bukan mengubah alur.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $fixed = FixedAsset::find($id);
        $fixed->id_aktiva = $request->aktiva;
        $fixed->id_penyusutan = $request->penyusutan;
        $fixed->id_beban = $request->beban;
        $fixed->id_supplier = $request->supplier;
        $fixed->id_pengeluaran = $request->bank;
        $fixed->code = $request->code;
        $fixed->no_invoice = $request->no_invoice;
        $fixed->beli = $request->pay;
        $fixed->pakai = $request->pakai;
        $fixed->bayar = $request->date;
        $fixed->metode = $request->metode;
        $fixed->desc = $request->desc;
        $fixed->umur = $request->umur;
        $fixed->qty = $request->qty;
        $fixed->total = $request->total;
        $fixed->status = $request->status ?? 0;

        if ($fixed->type == 'Mesin') {
            $fixed->id_unit = $request->id_unit;
            $fixed->serial_number = $request->serial_number;
        }

        if ($fixed->type == 'Kendaraan') {
            $fixed->jenis_kendaraan = $request->jenis_kendaraan;
            $fixed->merk_model = $request->merk_model;
            $fixed->bahan_bakar = $request->bahan_bakar;
            $fixed->plat_nomor = $request->plat_nomor;
            $fixed->atas_nama = $request->atas_nama;
        }

        $fixed->save();

        return redirect('/fixed/' . $id)->with('success', 'Data berhasil diupdate');
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
        $fixedDel = $fixed->delete();
        if ($fixedDel) {
            return 1;
        } else {
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
     * Set/ubah harga jual unit second — khusus Admin. Harga ini yang dipakai
     * sebagai sumber utama harga saat unit ditawarkan di /unit maupun dipilih
     * di form Quotation Unit (lihat /db/fixed-asset/search), menggantikan
     * fallback ke Catalog Unit yang sifatnya per-spek (bukan per-unit fisik).
     */
    public function updateHargaJual(Request $request, $id)
    {
        if (Auth::user()->role != 'Admin') {
            abort(403, 'Hanya Admin yang bisa mengubah harga jual unit ini.');
        }

        $request->validate([
            'harga_jual' => 'required|numeric|min:0',
        ]);

        $fixed = FixedAsset::find($id);
        if (!$fixed) {
            return response()->json(['error' => 'Unit tidak ditemukan'], 404);
        }

        $fixed->harga_jual = $request->harga_jual;
        $fixed->save();

        return redirect('/unit-acquisition/' . $id)->with('success', 'Harga jual unit berhasil diperbarui');
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
        $fixed = FixedAsset::find($id);
        $services = FixedAssetService::where('id_fixed_asset', $id)->with('detailProduct.product')->get();

        $hitung = $fixed->hitungNilaiBuku();
        $totalPenyusutan = $hitung['total_penyusutan'];
        $nilaiBuku = $hitung['nilai_buku'];

        return view('pages.warehouse.unit-acquisition.show', compact('fixed', 'totalPenyusutan', 'nilaiBuku', 'services'));
    }
}
