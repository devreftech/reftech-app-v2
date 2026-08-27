<?php

namespace App\Http\Controllers;

use App\Models\DetailPurchaseOrder;
use App\Models\DetailUnitProductIn;
use App\Models\FixedAsset;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\Unit;
use App\Models\UnitInventory;
use App\Models\UnitProductIn;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UnitProductInController extends Controller
{
    public function index()
    {
        $unitProductIns = UnitProductIn::with('supplier', 'po')->orderByDesc('id')->get();
        return view('pages.warehouse.unit-product-in.index', compact('unitProductIns'));
    }

    public function create()
    {
        $units = Unit::where('type', 'global')->orderBy('brand')->get();
        $suppliers = Supplier::all();
        $nextNoTransaksi = $this->generateNoTransaksi();
        return view('pages.warehouse.unit-product-in.form', compact('units', 'suppliers', 'nextNoTransaksi'));
    }

    // Format: 001-U/BM/VIII/2026 — U (Unit) beda sama P (Parts/Sparepart) yang
    // dipakai ProductInController::generateNoProductIn(), biar sekilas kebaca dari
    // nomornya kategori barangnya apa. Nomor urut per bulan.
    private function generateNoTransaksi(): string
    {
        $now = now();
        $year = $now->format('Y');
        $romanMonths = ['I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];
        $roman = $romanMonths[(int) $now->format('n') - 1];
        $suffix = "-U/BM/{$roman}/{$year}";

        $last = UnitProductIn::where('no_transaksi', 'like', '%' . $suffix)
            ->orderByDesc('no_transaksi')
            ->value('no_transaksi');

        $lastSeq = $last ? (int) substr($last, 0, 3) : 0;
        $nextSeq = str_pad($lastSeq + 1, 3, '0', STR_PAD_LEFT);

        return $nextSeq . $suffix;
    }

    public function store(Request $request)
    {
        if ($request->transaction_type == 'purchase_new') {
            return redirect('/unit-product-in/create')->with('error', 'Unit baru (beli dari supplier) sekarang diinput lewat Purchase Order, lalu diterima via Goods Receipt.');
        }

        $rule = [
            'transaction_type' => 'required|in:purchase_used,trade_in',
            'date' => 'required|date',
            'id_unit' => 'required|array',
            'id_unit.*' => 'required|integer|exists:unit,id',
            'serial_number' => 'required|array',
            'harga' => 'required|array',
        ];
        $this->validate($request, $rule);

        $unitProductIn = DB::transaction(function () use ($request) {
            $unitProductIn = new UnitProductIn();
            $unitProductIn->no_transaksi = $this->generateNoTransaksi();
            $unitProductIn->transaction_type = $request->transaction_type;
            $unitProductIn->id_supplier = $request->transaction_type == 'purchase_new' ? $request->id_supplier : null;
            $unitProductIn->id_customer = $request->transaction_type != 'purchase_new' ? $request->id_customer : null;
            $unitProductIn->date = $request->date;
            $unitProductIn->note = $request->note;
            $unitProductIn->created_by = Auth::id();
            $unitProductIn->save();

            $this->prosesDetail($unitProductIn, $request);

            return $unitProductIn;
        });

        return redirect('/unit-product-in')->with('success', 'Barang masuk unit berhasil disimpan: ' . $unitProductIn->no_transaksi);
    }

    /**
     * Routing per baris: purchase_new -> unit_inventory (merchandise, bukan aset),
     * purchase_used/trade_in -> fixed_asset (bisa dipakai rental sebelum laku dijual).
     */
    private function prosesDetail(UnitProductIn $unitProductIn, Request $request): void
    {
        foreach ($request->id_unit as $key => $idUnit) {
            $harga = $request->harga[$key] ?? 0;
            $biayaTambahan = $request->biaya_tambahan[$key] ?? 0;
            $serial = $request->serial_number[$key] ?? null;

            $detail = new DetailUnitProductIn();
            $detail->id_unit_product_in = $unitProductIn->id;
            $detail->id_unit = $idUnit;
            $detail->serial_number = $serial;
            $detail->harga = $harga;
            $detail->biaya_tambahan = $biayaTambahan;

            if ($unitProductIn->transaction_type == 'purchase_new') {
                $detail->kondisi = 'Baru';

                // Biaya rebranding TIDAK diisi di sini — selalu 0 dulu pas GR, rinciannya
                // (bisa >1 baris: cat, stiker, ongkos kerja, dst) diisi belakangan lewat
                // halaman detail unit (lihat storeRebrandingCost) begitu HPP-nya jelas.
                $inventory = new UnitInventory();
                $inventory->id_unit = $idUnit;
                $inventory->serial_number = $serial;
                $inventory->harga_modal = $harga;
                $inventory->biaya_rebranding = 0;
                $inventory->total_modal = $harga;
                $inventory->status = 'available';
                $inventory->id_unit_product_in = $unitProductIn->id;
                $inventory->created_by = Auth::id();
                $inventory->save();

                $detail->id_unit_inventory = $inventory->id;
            } else {
                $detail->kondisi = 'Second';

                $fixed = new FixedAsset();
                $fixed->type = 'Mesin';
                $fixed->code = app(FixedController::class)->generateAssetCode('Mesin');
                $fixed->id_unit = $idUnit;
                $fixed->serial_number = $serial;
                $fixed->kondisi = 'Second';
                $fixed->qc_status = 'checking';
                $fixed->id_supplier = $unitProductIn->id_supplier;
                $fixed->beli = $unitProductIn->date;
                $fixed->umur = 48;
                $fixed->total = $harga + $biayaTambahan;
                $fixed->qty = 1;
                $fixed->status = 0;
                $fixed->save();

                $detail->id_fixed_asset = $fixed->id;
            }

            $detail->save();
        }
    }

    /**
     * GR khusus PO kategori Unit — form tampilkan baris PO yang belum diterima,
     * Logistik tinggal isi serial number per unit (terima utuh sekaligus).
     */
    public function goodsReceiptForm($po)
    {
        $purchase = PurchaseOrder::where('category', 'Unit')->findOrFail($po);
        $detail = DetailPurchaseOrder::where('id_purchase_order', $po)->where('category', 'Unit')->with('unit')->get();
        // Preview nomor transaksi yang bakal dipakai kalau penerimaan ini disimpan —
        // sama polanya kayak previewNoGr di form GR Parts, biar user tahu nomornya
        // dari awal walau baru beneran "dikunci" pas submit.
        $previewNoTransaksi = $this->generateNoTransaksi();
        return view('pages.warehouse.unit-product-in.goods-receipt', compact('purchase', 'detail', 'previewNoTransaksi'));
    }

    public function storeGoodsReceipt(Request $request, $po)
    {
        $purchase = PurchaseOrder::where('category', 'Unit')->findOrFail($po);

        $rule = [
            'date' => 'required|date',
            'detail_id' => 'required|array',
            'serial_number' => 'required|array',
        ];
        $this->validate($request, $rule);

        DB::transaction(function () use ($request, $purchase) {
            $unitProductIn = new UnitProductIn();
            $unitProductIn->no_transaksi = $this->generateNoTransaksi();
            $unitProductIn->transaction_type = 'purchase_new';
            $unitProductIn->id_po = $purchase->id;
            $unitProductIn->id_supplier = $purchase->id_supplier;
            $unitProductIn->date = $request->date;
            $unitProductIn->note = 'Otomatis dibuat via Goods Receipt PO ' . $purchase->no_po;
            $unitProductIn->created_by = Auth::id();
            $unitProductIn->save();

            foreach ($request->detail_id as $key => $detailId) {
                $poDetail = DetailPurchaseOrder::findOrFail($detailId);
                $serial = $request->serial_number[$key] ?? null;
                // Kondisi ditentukan pas PO dibuat (lihat POController::store/update) —
                // Baru masuk stok jual (unit_inventory), Second didaftarkan sebagai
                // aset dulu buat di-QC (fixed_asset, sama kayak alur trade_in/purchase_used
                // manual di prosesDetail()) sebelum bisa dijual/direntalkan.
                $kondisi = $poDetail->kondisi === 'Second' ? 'Second' : 'Baru';

                $detail = new DetailUnitProductIn();
                $detail->id_unit_product_in = $unitProductIn->id;
                $detail->id_unit = $poDetail->id_unit;
                $detail->serial_number = $serial;
                $detail->harga = $poDetail->price;
                $detail->biaya_tambahan = 0;
                $detail->kondisi = $kondisi;

                if ($kondisi === 'Baru') {
                    $inventory = new UnitInventory();
                    $inventory->id_unit = $poDetail->id_unit;
                    $inventory->serial_number = $serial;
                    $inventory->harga_modal = $poDetail->price;
                    $inventory->biaya_rebranding = 0;
                    $inventory->total_modal = $poDetail->price;
                    $inventory->status = 'available';
                    $inventory->id_unit_product_in = $unitProductIn->id;
                    $inventory->created_by = Auth::id();
                    $inventory->save();

                    $detail->id_unit_inventory = $inventory->id;
                } else {
                    $fixed = new FixedAsset();
                    $fixed->type = 'Mesin';
                    $fixed->code = app(FixedController::class)->generateAssetCode('Mesin');
                    $fixed->id_unit = $poDetail->id_unit;
                    $fixed->serial_number = $serial;
                    $fixed->kondisi = 'Second';
                    $fixed->qc_status = 'checking';
                    $fixed->id_supplier = $purchase->id_supplier;
                    $fixed->beli = $request->date;
                    $fixed->umur = 48;
                    $fixed->total = $poDetail->price;
                    $fixed->qty = 1;
                    $fixed->status = 0;
                    $fixed->save();

                    $detail->id_fixed_asset = $fixed->id;
                }

                $detail->save();
            }

            $purchase->receipt_status = 'Received';
            $purchase->save();
        });

        return redirect('/purchase/' . $purchase->id)->with('success', 'Goods Receipt unit berhasil disimpan.');
    }

    /**
     * Detail per MODEL unit (Unit Global id), bukan per serial number — spesifikasi
     * Unit-nya, breakdown stok tiap serial number yang lagi available, riwayat masuk
     * (semua transaksi yang bawa model ini masuk), dan riwayat keluar (semua yang
     * udah terjual). Serial number beda-beda per unit fisik, tapi satu halaman ini
     * mewakili modelnya secara keseluruhan.
     */
    public function showInventory($id)
    {
        $unit = Unit::findOrFail($id);
        $inventories = UnitInventory::where('id_unit', $id)->with('rebrandingCosts')->orderByDesc('id')->get();

        return view('pages.warehouse.unit-inventory.show', compact('unit', 'inventories'));
    }

    /**
     * Tambah satu baris rincian biaya rebranding buat satu unit fisik (unit_inventory) —
     * bisa berkali-kali (cat, stiker, ongkos kerja, dst). biaya_rebranding & total_modal
     * di-sync ulang dari SUM semua baris tiap kali ada perubahan, bukan diisi manual
     * lagi pas GR — lihat UnitProductInController::prosesDetail/storeGoodsReceipt yang
     * selalu set biaya_rebranding awal ke 0.
     */
    public function storeRebrandingCost(Request $request, $inventoryId)
    {
        $this->validate($request, [
            'date' => 'required|date',
            'item' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'note' => 'nullable|string|max:255',
        ]);

        $inventory = UnitInventory::findOrFail($inventoryId);

        $inventory->rebrandingCosts()->create([
            'date' => $request->date,
            'item' => $request->item,
            'amount' => $request->amount,
            'note' => $request->note,
            'created_by' => Auth::id(),
        ]);

        $this->syncRebrandingTotal($inventory);

        return redirect()->back()->with('success', 'Rincian biaya rebranding berhasil ditambahkan.');
    }

    public function destroyRebrandingCost($costId)
    {
        $cost = \App\Models\UnitInventoryRebrandingCost::findOrFail($costId);
        $inventory = $cost->unitInventory;
        $cost->delete();

        if ($inventory) {
            $this->syncRebrandingTotal($inventory);
        }

        return redirect()->back()->with('success', 'Rincian biaya rebranding berhasil dihapus.');
    }

    private function syncRebrandingTotal(UnitInventory $inventory): void
    {
        $inventory->biaya_rebranding = $inventory->rebrandingCosts()->sum('amount');
        $inventory->total_modal = $inventory->harga_modal + $inventory->biaya_rebranding;
        $inventory->save();
    }

    /**
     * Set/ubah harga jual (listing) satu MODEL unit — satu harga berlaku buat
     * semua serial number-nya, bukan per unit fisik. Beda dari harga_modal/
     * total_modal (harga pokok/beli per unit) yang tetap per serial.
     */
    public function updateHargaJualUnit(Request $request, $id)
    {
        $this->validate($request, [
            'harga_jual' => 'required|numeric|min:0',
        ]);

        $unit = Unit::findOrFail($id);
        $unit->harga_jual = $request->harga_jual;
        $unit->save();

        return redirect()->back()->with('success', 'Harga jual unit berhasil disimpan.');
    }
}
