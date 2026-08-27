<?php

namespace App\Http\Controllers;

use App\Models\DetailPurchaseOrder;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\PurchaseRequest;
use App\Models\PurchaseRequestDetail;
use App\Models\PurchaseRequestDetailAllocation;
use App\Models\PurchaseOrderType;
use App\Models\Supplier;
use App\Services\PurchaseRequestService;
use Illuminate\Http\Request;

class POController extends Controller
{
    protected PurchaseRequestService $prService;

    public function __construct(PurchaseRequestService $prService)
    {
        $this->prService = $prService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('pages.accounting.purchase.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $suppliers = Supplier::all();
        $previewNoPo = $this->generateNoPo();
        $units = \App\Models\Unit::where('type', 'global')->orderBy('brand')->get();
        $products = Product::orderBy('commodity')->get();
        $poTypes = PurchaseOrderType::orderBy('name')->get();

        $sourcePr = null;
        $prefillItems = [];
        if ($request->query('from_pr')) {
            $sourcePr = PurchaseRequest::with('details.equivalent.product', 'details.allocations')->find($request->query('from_pr'));
            if ($sourcePr) {
                $selectedItems = $request->query('items', []);

                $detailsToPrefill = $sourcePr->details->filter(function ($detail) use ($selectedItems) {
                    if (!empty($selectedItems)) {
                        return array_key_exists((string) $detail->id, $selectedItems);
                    }
                    // Fallback (akses langsung tanpa selection dari halaman PR): tampilkan
                    // hanya item yang masih ada sisa qty belum teralokasi ke PO manapun.
                    return $detail->remainingQty > 0;
                });

                foreach ($detailsToPrefill as $detail) {
                    $product = $detail->equivalent->product ?? null;
                    if ($product) {
                        $requestedQty = !empty($selectedItems)
                            ? (int) ($selectedItems[(string) $detail->id] ?? 0)
                            : $detail->remainingQty;
                        // Qty PO boleh lebih dari sisa kebutuhan PR — kelebihannya jadi
                        // tambahan stok, bukan dibatasi ke remainingQty. Alokasi ke PR
                        // (yang menentukan status "Lunas") tetap di-clamp terpisah saat
                        // store(), lihat PurchaseRequestDetailAllocation di bawah.
                        $qty = $requestedQty > 0 ? $requestedQty : $detail->remainingQty;
                        if ($qty <= 0) {
                            continue;
                        }
                        $prefillItems[] = [
                            'id_product' => $product->id,
                            'label' => $product->commodity . ' — ' . $product->description,
                            'qty' => $qty,
                            'pr_detail_id' => $detail->id,
                            'pr_remaining' => $detail->remainingQty,
                        ];
                    }
                }
            }
        }

        return view('pages.accounting.purchase.form', compact('suppliers', 'previewNoPo', 'units', 'products', 'sourcePr', 'prefillItems', 'poTypes'));
    }

    public function quickStoreType(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:purchase_order_types,name',
        ]);

        $type = PurchaseOrderType::create([
            'name' => $request->name,
        ]);

        return response()->json([
            'success' => true,
            'data' => $type->only('id', 'name'),
        ]);
    }

    private function generateNoPo(): string
    {
        $year = now()->format('Y');
        $romanMonths = ['I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];
        $roman = $romanMonths[(int) now()->format('n') - 1];
        $suffix = "-P/RJO/{$roman}/{$year}";

        $last = PurchaseOrder::where('no_po', 'like', '%' . $suffix)
            ->orderByDesc('no_po')
            ->value('no_po');

        $lastSeq = $last ? (int) substr($last, 0, 3) : 0;
        $nextSeq = str_pad($lastSeq + 1, 3, '0', STR_PAD_LEFT);

        return $nextSeq . $suffix;
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
            'no_po' => 'required|string|unique:purchase_order,no_po',
        ];
        $this->validate($request, $rule);

        $supplier = Supplier::find($request->supplier);
        $itemCategories = $request->item_category ?? [];
        $purchase = new PurchaseOrder();
        $purchase->id_supplier = $request->supplier;
        $purchase->id_purchase_request = $request->id_purchase_request ?: null;
        $purchase->no_po = $request->no_po;
        $purchase->category = $request->category ?: (in_array('Unit', $itemCategories) ? 'Unit' : 'Sparepart');
        $purchase->company = $supplier->supplier;
        $purchase->attn = $request->attn ?? '';
        $purchase->mobile = $request->mobile ?? '';
        $purchase->delivery = $request->delivery ?? '';
        $purchase->date = $request->date;
        $purchase->email = $supplier->email ?? '-';
        $purchase->phone = $supplier->phone ?? '-';
        $purchase->address = $request->address ?? $supplier->address ?? '-';
        $purchase->payment = $request->payment ?? '';
        $purchase->note = $request->note ?? '';
        $purchase->subtotal = $request->subtotal;
        $purchase->vat = $request->tax;
        $purchase->diskon = $request->diskon;
        $purchase->delivery_cost = $request->delivery_cost ?? 0;
        $purchase->total = $request->harga_total;
        $purchaseSave = $purchase->save();
        $dPurchaseSave = true;
        if ($purchaseSave) {
            foreach ($request->product as $key => $value) {
                $itemCategory = $itemCategories[$key] ?? 'Sparepart';
                $dPurchase = new DetailPurchaseOrder();
                $dPurchase->id_purchase_order = $purchase->id;
                $dPurchase->product = $value;
                $dPurchase->category = $itemCategory;
                $dPurchase->id_unit = $itemCategory == 'Unit' ? ($request->id_unit[$key] ?? null) : null;
                $dPurchase->kondisi = $itemCategory == 'Unit' ? ($request->kondisi[$key] ?? 'Baru') : null;
                $dPurchase->id_product = $itemCategory == 'Sparepart' ? ($request->id_product[$key] ?? null) : null;
                $dPurchase->qty = $request->qty[$key];
                $dPurchase->info_qty = $request->info_qty[$key];
                $dPurchase->price = $request->price[$key];
                $dPurchase->disc = $request->disc[$key];
                $dPurchase->amount = $request->amount[$key];
                $dPurchaseSave = $dPurchase->save();

                $prDetailId = $request->pr_detail_id[$key] ?? null;
                if ($prDetailId) {
                    $prDetail = PurchaseRequestDetail::find($prDetailId);
                    if ($prDetail && $prDetail->remainingQty > 0) {
                        PurchaseRequestDetailAllocation::create([
                            'id_purchase_request_detail' => $prDetail->id,
                            'id_purchase_order' => $purchase->id,
                            'qty' => min((int) $request->qty[$key], $prDetail->remainingQty),
                        ]);
                    }
                }
            }
        }
        if ($purchaseSave && $dPurchaseSave) {
            return redirect('purchase/' . $purchase->id)->with('success', 'data berhasil ditambahkan');
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
        $purchase = PurchaseOrder::with('supplier')->find($id);
        $dPurchase = DetailPurchaseOrder::where('id_purchase_order', $id)->get();
        $tax = $purchase->total * 11 / 100;
        $totalPph = 0;
        foreach ($dPurchase as $product) {
            $pph = ($product->amount * $product->pph) / 100;
            $totalPph += $pph;
        }

        $sourcePr = null;
        $prDeliveryDone = false;
        $prDeliveryType = null;
        if ($purchase->id_purchase_request) {
            $sourcePr = PurchaseRequest::find($purchase->id_purchase_request);
            $prDeliveryType = $this->resolvePurchaseType($purchase->supplier->info ?? null);

            $poAllocations = PurchaseRequestDetailAllocation::where('id_purchase_order', $purchase->id)->get();
            $prDeliveryDone = $poAllocations->isNotEmpty() && $poAllocations->every(fn ($a) => !is_null($a->purchase_type));
        }

        // Riwayat Goods Receipt (barang masuk) & Retur (item rusak/dikembalikan)
        // yang lahir dari GR PO ini, biar kelihatan di halaman detail PO — bukan cuma
        // nyangkut di halaman /product-in yang jarang dibuka dari sini.
        $productIns = \App\Models\ProductIn::where('id_purchase_order', $purchase->id)
            ->with(['detail.detailProduct.product', 'return.detail.replacement.product'])
            ->orderByDesc('id')
            ->get();

        return view('pages.accounting.purchase.detail', compact('purchase', 'dPurchase', 'tax', 'totalPph', 'sourcePr', 'prDeliveryDone', 'prDeliveryType', 'productIns'));
    }

    private function resolvePurchaseType(?string $supplierInfo): string
    {
        $normalized = strtolower((string) $supplierInfo);
        return str_contains($normalized, 'import') || str_contains($normalized, 'impor') ? 'Impor' : 'Lokal';
    }

    /**
     * Tandai item PR yang teralokasi ke PO ini sebagai "on delivery". Tipe (Lokal/Impor)
     * otomatis ikut info supplier PO, tanggal pembelian ikut tanggal PO dibuat — cuma
     * cargo & no resi yang perlu diisi manual. Hanya item yang teralokasi ke PO INI yang
     * kena update, bukan seluruh item PR (PR bisa dipecah ke beberapa PO/supplier).
     */
    public function delivery(Request $request, $id)
    {
        $rule = [
            'cargo' => 'required|string|max:255',
            'no_resi' => 'nullable|string|max:255',
        ];
        $this->validate($request, $rule);

        $purchase = PurchaseOrder::with('supplier')->find($id);
        if (!$purchase || !$purchase->id_purchase_request) {
            return response()->json(['message' => 'Purchase Order ini tidak terhubung ke Purchase Request.'], 422);
        }

        $purchaseType = $this->resolvePurchaseType($purchase->supplier->info ?? null);

        $allocations = PurchaseRequestDetailAllocation::where('id_purchase_order', $purchase->id)->get();

        if ($allocations->isEmpty()) {
            return response()->json(['message' => 'Tidak ada item Purchase Request yang teralokasi ke Purchase Order ini.'], 422);
        }

        // Update per baris alokasi (bukan per item PR) — satu item PR bisa split qty
        // ke beberapa PO, jadi info pengiriman harus melekat ke alokasi masing-masing PO.
        PurchaseRequestDetailAllocation::where('id_purchase_order', $purchase->id)->update([
            'purchase_type' => $purchaseType,
            'cargo' => $request->cargo,
            'no_resi' => $request->no_resi,
            'purchase_date' => $purchase->date,
        ]);

        $pr = PurchaseRequest::with('details.allocations')->find($purchase->id_purchase_request);
        if ($pr && (int) $pr->status === 1 && $this->prService->allDeliveriesSubmitted($pr)) {
            $pr->status = '2';
            $pr->save();
        }

        return 1;
    }

    /**
     * Tandai PO yang dibeli langsung tanpa Purchase Request sebagai "On Delivery"
     * (barang sudah dikirim supplier) — berlaku buat kategori Unit maupun Parts,
     * bedanya cuma di kolom on_delivery_* punya purchase_order (bukan lewat
     * PurchaseRequestDetailAllocation kayak delivery() di atas yang khusus PO hasil
     * Purchase Request). Setelah ini disubmit, tombol "Terima Barang" (GR) baru
     * muncul di halaman detail PO — Unit ke UnitProductInController, Parts ke
     * PurchaseController::goodsReceiptFormDirect().
     */
    public function deliveryUnit(Request $request, $id)
    {
        $rule = [
            'cargo' => 'required|string|max:255',
            'no_resi' => 'nullable|string|max:255',
        ];
        $this->validate($request, $rule);

        $purchase = PurchaseOrder::find($id);
        if (!$purchase || $purchase->id_purchase_request) {
            return response()->json(['message' => 'Purchase Order ini dibeli lewat Purchase Request, gunakan alur delivery biasa.'], 422);
        }

        $purchase->on_delivery_at = now();
        $purchase->on_delivery_cargo = $request->cargo;
        $purchase->on_delivery_no_resi = $request->no_resi;
        $purchase->save();

        return 1;
    }

    /**
     * Simpan No. Invoice & file invoice dari supplier untuk PO ini. Nomor invoice ikut
     * disinkronkan ke ProductIn (barang masuk) yang sudah lahir dari GR PO ini — supaya
     * GR-nya kebaca di tabel "Product In — Lokal/Import" (yang mensyaratkan invoice
     * terisi) tanpa harus diinput ulang manual.
     */
    public function uploadInvoice(Request $request, $id)
    {
        $rule = [
            'no_invoice_supplier' => 'required|string|max:255',
            'invoice_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ];
        $this->validate($request, $rule);

        $purchase = PurchaseOrder::findOrFail($id);

        if ($request->hasFile('invoice_file')) {
            if ($purchase->invoice_file && \Illuminate\Support\Facades\Storage::disk('public')->exists($purchase->invoice_file)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($purchase->invoice_file);
            }
            $year = now()->year;
            $purchase->invoice_file = $request->file('invoice_file')->store("purchase-order/invoice/{$year}", 'public');
        }

        $purchase->no_invoice_supplier = $request->no_invoice_supplier;
        $purchase->save();

        // Sinkron ke ProductIn yang sudah ada (kalau GR-nya sudah pernah diverifikasi
        // sebelum invoice-nya diupload) — biar langsung kebaca di tabel Invoice.
        \App\Models\ProductIn::where('id_purchase_order', $purchase->id)
            ->update(['invoice' => $purchase->no_invoice_supplier]);

        return redirect()->route('purchase.show', $purchase->id)
            ->with('success', 'Invoice supplier berhasil disimpan.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $purchase = PurchaseOrder::find($id);
        $dPurchase = DetailPurchaseOrder::where('id_purchase_order', $id)->get();
        $suppliers = Supplier::all();
        $units = \App\Models\Unit::where('type', 'global')->orderBy('brand')->get();
        $products = Product::orderBy('commodity')->get();
        $poTypes = PurchaseOrderType::orderBy('name')->get();
        return view('pages.accounting.purchase.form', compact('suppliers', 'purchase', 'dPurchase', 'units', 'products', 'poTypes'));
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
        $this->validate($request, [
            'no_po' => 'required|string|unique:purchase_order,no_po,' . $id,
        ]);
        $supplier = Supplier::find($request->supplier);
        $itemCategories = $request->item_category ?? [];
        $purchase = PurchaseOrder::find($id);
        $purchase->id_supplier = $request->supplier;
        $purchase->no_po = $request->no_po;
        $purchase->category = $request->category ?: (in_array('Unit', $itemCategories) ? 'Unit' : 'Sparepart');
        $purchase->company = $supplier->supplier;
        $purchase->attn = $request->attn ?? '';
        $purchase->mobile = $request->mobile ?? '';
        $purchase->delivery = $request->delivery ?? '';
        $purchase->date = $request->date;
        $purchase->email = $supplier->email ?? '-';
        $purchase->phone = $supplier->phone ?? '-';
        $purchase->address = $request->address ?? $supplier->address ?? '-';
        $purchase->payment = $request->payment ?? '';
        $purchase->note = $request->note ?? '';
        $purchase->subtotal = $request->subtotal;
        $purchase->vat = $request->tax;
        $purchase->diskon = $request->diskon;
        $purchase->delivery_cost = $request->delivery_cost ?? 0;
        $purchase->total = $request->harga_total;
        $purchaseSave = $purchase->save();
        $dPurchaseSave = true;
        if ($purchaseSave) {
            $submittedIds = [];
            foreach ($request->product as $key => $value) {
                $itemCategory = $itemCategories[$key] ?? 'Sparepart';
                $detailId = $request->detail_id[$key] ?? null;
                $dPurchase = $detailId ? DetailPurchaseOrder::find($detailId) : null;
                if (!$dPurchase) {
                    $dPurchase = new DetailPurchaseOrder();
                    $dPurchase->id_purchase_order = $purchase->id;
                }
                $dPurchase->product = $value;
                $dPurchase->category = $itemCategory;
                $dPurchase->id_unit = $itemCategory == 'Unit' ? ($request->id_unit[$key] ?? null) : null;
                $dPurchase->kondisi = $itemCategory == 'Unit' ? ($request->kondisi[$key] ?? 'Baru') : null;
                $dPurchase->id_product = $itemCategory == 'Sparepart' ? ($request->id_product[$key] ?? null) : null;
                $dPurchase->qty = $request->qty[$key];
                $dPurchase->info_qty = $request->info_qty[$key];
                $dPurchase->price = $request->price[$key];
                $dPurchase->disc = $request->disc[$key];
                $dPurchase->amount = $request->amount[$key];
                $dPurchaseSave = $dPurchase->save();
                $submittedIds[] = $dPurchase->id;
            }
            DetailPurchaseOrder::where('id_purchase_order', $purchase->id)
                ->whereNotIn('id', $submittedIds)
                ->delete();
        }
        if ($purchaseSave && $dPurchaseSave) {
            return redirect('purchase/'. $id)->with('success', 'data berhasil ditambahkan');
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
        $purchase = PurchaseOrder::find($id);
        $dPurchase = DetailPurchaseOrder::where('id_purchase_order', $id)->get();
        $purchaseDel = $purchase->delete();
        foreach ($dPurchase as $order) {
            $order->delete();
        }
        if ($purchaseDel) {
            return 1;
        } else {
            return 0;
        }

    }
    public function show_print($id)
    {
        $purchase = PurchaseOrder::find($id);
        $dPurchase = DetailPurchaseOrder::where('id_purchase_order', $id)->get();
        $tax = $purchase->total * 11 / 100;
        $totalPph = 0;
        foreach ($dPurchase as $item) {
            $pph = ($item->amount * $item->pph) / 100;
            $totalPph += $pph;
        }
        return view('pages.accounting.purchase.detail-print', compact('purchase', 'dPurchase', 'tax', 'totalPph'));
    }

    public function add_pph(Request $request, $id)
    {

        $PO = PurchaseOrder::find($id);
        $DPO = DetailPurchaseOrder::where('id_purchase_order', $id)->get();
        foreach ($DPO as $item => $value) {
            $value->pph = $request->pph[$item];
            $status = $value->save();
        }
        if ($status) {
            return redirect('/purchase/' . $id)->with('massage', 'Data telah terkirim');
        }
    }
    public function delete_pph($id)
    {
        $purchase = PurchaseOrder::find($id);
        $dPurchase = DetailPurchaseOrder::where('id_purchase_order', $id)->get();
        foreach ($dPurchase as $item => $value) {
            $value->pph = 0;
            $status = $value->save();
        }
        if ($status) {
            return 1;
        } else {
            return 0;
        }

    }
}
