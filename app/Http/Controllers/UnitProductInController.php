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
        $unitProductIns = UnitProductIn::with('supplier')->orderByDesc('id')->get();
        $pendingUnitPo = PurchaseOrder::where('category', 'Unit')
            ->where('receipt_status', 'Pending')
            ->orderByDesc('id')
            ->get();
        return view('pages.warehouse.unit-product-in.index', compact('unitProductIns', 'pendingUnitPo'));
    }

    public function create()
    {
        $units = Unit::where('type', 'global')->orderBy('brand')->get();
        $suppliers = Supplier::all();
        $nextNoTransaksi = $this->generateNoTransaksi();
        return view('pages.warehouse.unit-product-in.form', compact('units', 'suppliers', 'nextNoTransaksi'));
    }

    private function generateNoTransaksi(): string
    {
        $year = now()->format('Y');
        $month = now()->format('m');
        $prefix = "UIN/{$year}/{$month}/";

        $last = UnitProductIn::where('no_transaksi', 'like', $prefix . '%')
            ->orderByDesc('no_transaksi')
            ->value('no_transaksi');

        $lastSeq = $last ? (int) substr($last, -3) : 0;
        $nextSeq = str_pad($lastSeq + 1, 3, '0', STR_PAD_LEFT);

        return $prefix . $nextSeq;
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

                $inventory = new UnitInventory();
                $inventory->id_unit = $idUnit;
                $inventory->serial_number = $serial;
                $inventory->harga_modal = $harga;
                $inventory->biaya_rebranding = $biayaTambahan;
                $inventory->total_modal = $harga + $biayaTambahan;
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
        $detail = DetailPurchaseOrder::where('id_purchase_order', $po)->where('category', 'Unit')->get();
        return view('pages.warehouse.unit-product-in.goods-receipt', compact('purchase', 'detail'));
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

                $detail = new DetailUnitProductIn();
                $detail->id_unit_product_in = $unitProductIn->id;
                $detail->id_unit = $poDetail->id_unit;
                $detail->serial_number = $request->serial_number[$key] ?? null;
                $detail->harga = $poDetail->price;
                $detail->biaya_tambahan = 0;
                $detail->kondisi = 'Baru';

                $inventory = new UnitInventory();
                $inventory->id_unit = $poDetail->id_unit;
                $inventory->serial_number = $request->serial_number[$key] ?? null;
                $inventory->harga_modal = $poDetail->price;
                $inventory->biaya_rebranding = 0;
                $inventory->total_modal = $poDetail->price;
                $inventory->status = 'available';
                $inventory->id_unit_product_in = $unitProductIn->id;
                $inventory->created_by = Auth::id();
                $inventory->save();

                $detail->id_unit_inventory = $inventory->id;
                $detail->save();
            }

            $purchase->receipt_status = 'Received';
            $purchase->save();
        });

        return redirect('/purchase/' . $purchase->id)->with('success', 'Goods Receipt unit berhasil disimpan.');
    }
}
