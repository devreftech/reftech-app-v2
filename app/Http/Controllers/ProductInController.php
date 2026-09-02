<?php

namespace App\Http\Controllers;

use App\Models\DetailProduct;
use App\Models\DetailProductIn;
use Illuminate\Support\Facades\Auth;
use App\Models\DetailReturn;
use App\Models\Product;
use App\Models\ProductIn;
use App\Models\ProductInCost;
use App\Models\Prospect;
use App\Models\Retur;
use App\Models\Supplier;
use App\Models\SupplierPic;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ProductInController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $product = ProductIn::all();
        // dd($product);
        $noSaleProspect = Prospect::whereNULL('id_sales')->whereNull('provide')->count();
        return view('pages.warehouse.product-in.index', compact('noSaleProspect'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $suppliers = Supplier::all();
        $nextNoProductIn = $this->generateNoProductIn();
        return view('pages.warehouse.product-in.form', compact('suppliers', 'nextNoProductIn'));
    }

    // Sumber data dropdown "Commodity || Replacement" di form Product In — dulu di-render
    // penuh sekaligus (3000+ <option>, bikin select2 lemot pas dibuka), sekarang di-search
    // on-demand kayak pola yang sama dipakai di QuotationController::searchProducts().
    public function searchReplacements(Request $request)
    {
        $search = $request->input('q');

        $query = DetailProduct::join('product', 'detail_product.id_product', '=', 'product.id')
            ->select('detail_product.id', 'detail_product.replacement', 'product.commodity', 'product.detail_desc', 'product.go')
            ->where(function ($q) {
                $q->whereNull('product.category')
                    ->orWhere('product.category', '!=', 'Unit');
            });

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('detail_product.replacement', 'like', "%{$search}%")
                    ->orWhere('product.commodity', 'like', "%{$search}%")
                    ->orWhere('product.detail_desc', 'like', "%{$search}%");
            });
        }

        $results = $query->limit(30)->get()->map(function ($p) {
            $goCode = $p->go == 'Genuine' ? 'G' : 'R';
            return [
                'id' => $p->id,
                // 'text' dipakai select2 buat fallback tampilan/screen reader — badge G/R
                // yang keliatan di dropdown-nya di-render terpisah lewat templateResult (JS).
                'text' => "{$p->commodity} ({$p->detail_desc}) || {$p->replacement} - {$goCode}",
                'commodity' => $p->commodity,
                'detail_desc' => $p->detail_desc,
                'replacement' => $p->replacement,
                'go' => $goCode,
            ];
        });

        return response()->json($results);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    // Format: 001-P/BM/VIII/2026 — P (Parts/Sparepart) beda sama U (Unit) yang
    // dipakai UnitProductInController, biar sekilas kebaca dari nomornya kategori
    // barangnya apa. Nomor urut per bulan, gak lagi dibedain per gudang (BDG/BKS).
    private function generateNoProductIn(string $warehouse = 'BDG'): string
    {
        $now = now();
        $year = $now->format('Y');
        $romanMonths = ['I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];
        $roman = $romanMonths[(int) $now->format('n') - 1];
        $suffix = "-P/BM/{$roman}/{$year}";

        $last = ProductIn::where('no_product_in', 'like', '%' . $suffix)
            ->orderByDesc('no_product_in')
            ->value('no_product_in');

        $lastSeq = $last ? (int) substr($last, 0, 3) : 0;
        $nextSeq = str_pad($lastSeq + 1, 3, '0', STR_PAD_LEFT);

        return $nextSeq . $suffix;
    }

    public function store(Request $request)
    {
        $rule = [
            'invoice' => 'required',
            'date' => 'required',
            'note' => 'required',
        ];
        $message = [
            'invoice.required' => 'Field No Invoice Wajib Diisi',
            'date.required' => 'Field Date Wajib Diisi',
            'note.required' => 'Field Note Wajib Diisi',
        ];
        $this->validate($request, $rule, $message);
        // dd($request->all());
        $supplier = Supplier::find($request->supplier);
        // Masukan Data ke Tabel Quotataion
        $productIn = new ProductIn();
        $productIn->no_product_in = $this->generateNoProductIn($request->warehouse[0] ?? 'BDG');
        $productIn->created_by = Auth::id();
        $productIn->no_do = NULL;
        $productIn->invoice = $request->invoice;
        $productIn->id_supplier = $request->supplier;
        // $productIn->supplier = $request->suplier;
        $productIn->info = $supplier->info;
        $productIn->date = $request->date;
        $productIn->date_invoice = $request->date_invoice;
        $productIn->subtotal = $request->subtotal;
        $productIn->total_no_tax = $request->total_no_tax;
        $productIn->tax = $request->tax;
        $productIn->note = $request->note;
        $productIn->shipping = $request->shipping;
        $productIn->total = $request->total;
        $productInSave = $productIn->save();
        if ($productInSave) {
            // Masukan Data Ke Tabel Detail Quotataion
            foreach ($request->replacement as $item => $value) {
                $dProductIn = new DetailProductIn;
                $dProductIn->id_product_in = $productIn->id;
                $dProductIn->id_detail_product = $request->replacement[$item];
                $dProductIn->qty = $request->qty[$item];
                $dProductIn->modal = $request->price[$item];
                $dProductIn->disc = !empty($request->disc[$item]) ? $request->disc[$item] : 0;
                $dProductIn->amount = $request->amount[$item];
                $dProductIn->warehouse = $request->warehouse[$item];
                $productD = DetailProduct::where('id', $request->replacement[$item])->first();
                // dd($productD);
                $productD->modal = ((($productD->stock + $productD->warehouse_stock) * $productD->modal) + ($request->qty[$item] * $request->price[$item])) / (($productD->stock + $productD->warehouse_stock) + $request->qty[$item]);
                if ($request->warehouse[$item] == 'BDG') {
                    $productD->stock = $productD->stock + $request->qty[$item];
                } else {
                    $productD->warehouse_stock = $productD->warehouse_stock + $request->qty[$item];
                }
                $productD->save();
                $product = Product::where('id', $productD->id_product)->first();
                if ($request->warehouse[$item] == 'BDG') {
                    $product->stock = $product->stock + $request->qty[$item];
                } else {
                    $product->warehouse_stock = $product->warehouse_stock + $request->qty[$item];
                }
                // dd($product);
                $product->save();
                $dProductSave = $dProductIn->save();
            }
        }
        if ($dProductSave) {
            return redirect('/product-in')->with('message', 'data telah di tambahkan');
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
        $product = ProductIn::with([
            'purchaseOrder.supplier',
            'creator',
            'detail.detailProduct.product',
            'return.detail.replacement.product',
            'costs.creator',
        ])->findOrFail($id);

        $tax = $product->subtotal * $product->tax / 100;
        $detail = $product->detail;
        $noSaleProspect = Prospect::whereNULL('id_sales')->whereNull('provide')->count();
        $return = $product->return;

        // Brand-nya nyimpen di serial_product (bukan di product/detail_product langsung)
        $productIds = $detail->pluck('detailProduct.id_product')->filter()->unique();
        $serials = \App\Models\SerialProduct::whereIn('id_product', $productIds)->get(['id_product', 'pn', 'brand']);
        foreach ($detail as $d) {
            $idProduct = $d->detailProduct->id_product ?? null;
            $match = $serials->first(fn ($s) => $s->id_product === $idProduct && $s->pn === $d->detailProduct->replacement);
            $fallback = $serials->first(fn ($s) => $s->id_product === $idProduct);
            $d->brand = $match->brand ?? $fallback->brand ?? null;
        }

        // Info tambahan buat mode edit inline — cuma relevan selama belum ada invoice
        // (lihat preview.blade.php lama), jadi query-nya di-skip kalau udah gak kepake.
        $suppliers = collect();
        $detProduct = collect();
        if (!$product->invoice) {
            $suppliers = Supplier::all();
            $detProduct = DetailProduct::join('product', 'detail_product.id_product', '=', 'product.id')->get('detail_product.*');
            $this->attachPoPriceReference($detail, $product->purchaseOrder);
        }

        // Preview alokasi Biaya Tambahan (tab "Biaya Tambahan") — dihitung tiap load,
        // baru ditulis ke DB kalau user klik "Simpan & Terapkan ke HPP" (lihat applyHpp()).
        $costAllocation = $this->calculateCostAllocation($detail, $product->costs);

        return view('pages.warehouse.product-in.detail', compact(
            'product', 'detail', 'noSaleProspect', 'tax', 'return', 'suppliers', 'detProduct', 'costAllocation'
        ));
    }

    // Hitung nilai barang per item (harga PO kalau belum invoice, harga invoice/modal
    // kalau udah) lalu distribusikan Biaya Tambahan secara proporsional ke nilai barang
    // (item lebih mahal dapat porsi lebih besar) — dipakai buat preview di halaman dan
    // buat nilai final yang ditimpakan ke HPP pas applyHpp().
    //
    // $unitPrices (opsional): map [detail_product_in.id => harga satuan]. Dipakai biar
    // fungsi ini gak perlu baca atribut dinamis `po_price` dari model — soalnya kalau
    // model itu nanti ikut di-save() (lihat applyHpp()), atribut dinamis semacam itu
    // ikut ke-dirty dan bikin Eloquent nyoba nulis ke kolom yang gak ada di DB.
    private function calculateCostAllocation($detail, $costs, ?array $unitPrices = null): array
    {
        $totalAdditionalCost = $costs->sum('amount');

        $itemValues = $detail->map(function ($d) use ($unitPrices) {
            $unitValue = $unitPrices !== null ? ($unitPrices[$d->id] ?? 0) : ($d->po_price ?? $d->modal ?? 0);
            return $unitValue * $d->qty;
        });
        $nominalPo = $itemValues->sum();

        $rows = [];
        foreach ($detail as $i => $d) {
            $itemValue = $itemValues[$i];
            $share = $nominalPo > 0 ? $itemValue / $nominalPo : 0;
            $allocatedCost = (int) round($share * $totalAdditionalCost);
            $hppBaru = $d->qty > 0 ? (int) round(($itemValue + $allocatedCost) / $d->qty) : null;

            $rows[] = [
                'id' => $d->id,
                'item_value' => $itemValue,
                'share' => $share,
                'allocated_cost' => $allocatedCost,
                'hpp_baru' => $hppBaru,
            ];
        }

        return [
            'nominal_po' => $nominalPo,
            'total_additional_cost' => $totalAdditionalCost,
            'rows' => collect($rows)->keyBy('id'),
        ];
    }

    public function addCost(Request $request, $id)
    {
        $request->validate([
            'label' => 'required|string|max:255',
            'amount' => 'required|integer|min:1',
        ]);

        ProductInCost::create([
            'id_product_in' => $id,
            'label' => $request->label,
            'amount' => $request->amount,
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('product-in.show', $id)->with('message', 'Biaya tambahan berhasil ditambahkan.');
    }

    public function deleteCost($costId)
    {
        $cost = ProductInCost::findOrFail($costId);
        $productInId = $cost->id_product_in;
        $cost->delete();

        return redirect()->route('product-in.show', $productInId)->with('message', 'Biaya tambahan berhasil dihapus.');
    }

    // Timpa (bukan rata-rata sama stok lama) detail_product.hpp & detail_product_in.hpp
    // per item sesuai hasil alokasi Biaya Tambahan — jadi HPP mencerminkan biaya batch
    // GR ini aja, sisa stok lama gak ikut ke-average.
    public function applyHpp($id)
    {
        $product = ProductIn::with(['detail.detailProduct', 'costs', 'purchaseOrder.detail'])->findOrFail($id);

        // Harga acuan per item belum tentu ke-set di sini — beda dari show(), request ini
        // gak lewat situ — jadi dicocokkan ulang ke harga PO/invoice biar alokasinya
        // konsisten sama yang ditampilkan di preview. Dibikin lookup array biasa (bukan
        // atribut dinamis di model) karena model-model ini di bawah bakal di-save().
        $unitPrices = null;
        if (!$product->invoice) {
            $poPriceByProduct = $product->purchaseOrder
                ? $product->purchaseOrder->detail->whereNotNull('id_product')->keyBy('id_product')
                : collect();

            $unitPrices = [];
            foreach ($product->detail as $d) {
                $idProduct = $d->detailProduct->id_product ?? null;
                $poDetail = $idProduct ? $poPriceByProduct->get($idProduct) : null;
                $unitPrices[$d->id] = $poDetail->price ?? 0;
            }
        }

        $allocation = $this->calculateCostAllocation($product->detail, $product->costs, $unitPrices);

        foreach ($product->detail as $d) {
            $row = $allocation['rows']->get($d->id);
            if (!$row || is_null($row['hpp_baru'])) {
                continue;
            }
            $d->hpp = $row['hpp_baru'];
            $d->save();

            if ($d->detailProduct) {
                $d->detailProduct->hpp = $row['hpp_baru'];
                $d->detailProduct->save();
            }
        }

        return redirect()->route('product-in.show', $id)->with('message', 'HPP berhasil diterapkan ke item-item barang masuk ini.');
    }

    // Cocokkan tiap item barang masuk ke harga di PO asalnya (per produk) — dipakai
    // buat nampilin kolom Price di tabel "Item Diterima" (show) dan buat auto-fill
    // form invoicing (edit), jadi Accounting/Logistic gak perlu ngetik ulang harga
    // yang sebenarnya udah disepakati pas PO dibuat.
    private function attachPoPriceReference($detail, $purchaseOrder): void
    {
        if (!$purchaseOrder) {
            foreach ($detail as $d) {
                $d->po_price = null;
                $d->po_disc = null;
            }
            return;
        }

        $poPriceByProduct = $purchaseOrder->detail
            ->whereNotNull('id_product')
            ->keyBy('id_product');

        foreach ($detail as $d) {
            $idProduct = $d->detailProduct->id_product ?? null;
            $poDetail = $idProduct ? $poPriceByProduct->get($idProduct) : null;
            $d->po_price = $poDetail->price ?? null;
            $d->po_disc = $poDetail->disc ?? null;
        }
    }

    // product-in.preview & product-in.gr-detail sudah digabung ke halaman detail
    // (product-in.show) — redirect biar link/bookmark lama tetap jalan.
    public function preview($id)
    {
        return redirect()->route('product-in.show', $id);
    }

    public function grDetail($id)
    {
        return redirect()->route('product-in.show', $id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $productIn = ProductIn::with('purchaseOrder.detail')->find($id);
        $dProductIn = DetailProductIn::where('id_product_in', $id)->with('detailProduct')->get();
        $suppliers = Supplier::all();

        // Auto-fill Price/Discount dari harga PO asal — masih bisa di-override
        // manual di form kalau perlu.
        $this->attachPoPriceReference($dProductIn, $productIn->purchaseOrder ?? null);

        return view('pages.warehouse.product-in.invoicing', compact('suppliers', 'productIn', 'dProductIn'));
    }

    public function edit_return($id)
    {
        $return = Retur::find($id);
        $dReturn = DetailReturn::where('id_retur', $id)->where('status', 1)->get();
        $suppliers = Supplier::all();
        // dd($dReturn[0]->return);
        return view('pages.warehouse.product-in.return', compact('suppliers', 'return', 'dReturn','id'));
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
        $product = ProductIn::find($id);
        $dProductIn = DetailProductIn::where('id_product_in', $id)->get();
        // dd($request->all());
        $total = 0;
        foreach ($dProductIn as $item) {
            $item->detailProduct->modal = $request->modal[$item->id];
            $item->modal = $request->modal[$item->id];
            // $item->disc = $request->disc[$item->id];
            $item->amount = $request->modal[$item->id] * $item->qty;
            $detailSave = $item->save();
            $item->detailProduct->save();
            $total += $item->amount;
        }
        if ($detailSave) {
            $taxed = $total * $product->tax;
            $product->subtotal = $total;
            $product->total_no_tax = $total + $product->shipping;
            $product->total = $total + $taxed + $product->shipping;
            $productSave = $product->save();
        }
        if ($productSave) {
            return redirect('/product-in/' . $id)->with('message', 'data telah di tambahkan');
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
        $product = ProductIn::find($id);
        $detail = DetailProductIn::where('id_product_in', $id)->get();

        $delProductIn = $product->delete();

        foreach ($detail as $dProductIn) {
            if ($dProductIn->warehouse == 'BDG') {
                $dProductIn->detailProduct->stock = $dProductIn->detailProduct->stock - $dProductIn->qty;
            } else {
                $dProductIn->detailProduct->warehouse_stock = $dProductIn->detailProduct->warehouse_stock - $dProductIn->qty;
            }
            $dProductIn->detailProduct->save();
            if ($dProductIn->warehouse == 'BDG') {
                $dProductIn->detailProduct->product->stock = $dProductIn->detailProduct->product->stock - $dProductIn->qty;
            } else {
                $dProductIn->detailProduct->product->warehouse_stock = $dProductIn->detailProduct->product->warehouse_stock - $dProductIn->qty;
            }
            $dProductIn->detailProduct->product->save();
            $delDetProductIn = $dProductIn->delete();
        }

        if ($delProductIn || $delDetProductIn) {
            return 1;
        } else {
            return 0;
        }
    }

    public function logistic_store(Request $request)
    {

        $rule = [
            'no_do' => 'required',
            'date' => 'required',
            // GR Manual (barang masuk tanpa PO) — wajib ada alasan/catatan biar ada jejak
            // audit kenapa stok ini muncul tanpa Purchase Order.
            'note' => 'required|string|max:1000',
        ];
        $message = [
            'no_do.required' => 'Field No DO Wajib Diisi',
            'date.required' => 'Field Date Wajib Diisi',
            'note.required' => 'Catatan/Alasan Wajib Diisi',
        ];
        $this->validate($request, $rule, $message);
        // dd($request->all());
        $supplier = Supplier::find($request->supplier);
        // Masukan Data ke Tabel Quotataion
        $productIn = new ProductIn();
        $productIn->no_product_in = $this->generateNoProductIn($request->warehouse[0] ?? 'BDG');
        $productIn->created_by = Auth::id();
        $productIn->no_do = $request->no_do;
        $productIn->invoice = null;
        // $productIn->id_supplier = null;
        $productIn->id_supplier = $request->supplier;
        $productIn->supplier = null;
        // $productIn->info = $supplier->info;
        $productIn->info = $request->info;
        $productIn->date = $request->date;
        $productIn->date_invoice = null;
        $productIn->subtotal = null;
        $productIn->total_no_tax = null;
        $productIn->tax = null;
        $productIn->note = $request->note;
        $productIn->shipping = null;
        $productIn->total = null;
        $productInSave = $productIn->save();
        if ($productInSave) {
            // Masukan Data Ke Tabel Detail Quotataion
            foreach ($request->replacement as $item => $value) {
                $dProductIn = new DetailProductIn;
                $dProductIn->id_product_in = $productIn->id;
                $dProductIn->id_detail_product = $request->replacement[$item];
                $dProductIn->qty = $request->qty[$item];
                $dProductIn->modal = null;
                $dProductIn->amount = null;
                $dProductIn->warehouse = $request->warehouse[$item];
                $productD = DetailProduct::where('id', $request->replacement[$item])->first();
                // $productD->modal = ((($productD->stock + $productD->warehouse_stock) * $productD->modal) + ($request->qty[$item] * $request->price[$item])) / (($productD->stock + $productD->warehouse_stock) + $request->qty[$item]);
                if ($request->warehouse[$item] == 'BDG') {
                    $productD->stock = $productD->stock + $request->qty[$item];
                } else {
                    $productD->warehouse_stock = $productD->warehouse_stock + $request->qty[$item];
                }
                $productD->save();
                $product = Product::where('id', $productD->id_product)->first();
                if ($request->warehouse[$item] == 'BDG') {
                    $product->stock = $product->stock + $request->qty[$item];
                } else {
                    $product->warehouse_stock = $product->warehouse_stock + $request->qty[$item];
                }
                // dd($product);
                $product->save();
                $dProductSave = $dProductIn->save();
            }
        }
        if ($dProductSave) {
            return redirect('/product-in')->with('message', 'data telah di tambahkan');
        }
    }
    public function invoicing(Request $request, $id)
    {
        $productIn = ProductIn::find($id);
        $dProductIn = DetailProductIn::where('id_product_in', $id)->get();
        // dd($request->all());
        $rule = [
            'invoice' => 'required',
            // 'suplier' => 'required',
            // 'date_invoice' => 'required',
            'note' => 'required',
        ];
        $message = [
            'invoice.required' => 'Field No Invoice Wajib Diisi',
            // 'suplier.required' => 'Field Suplier Wajib Diisi',
            // 'date_invoice.required' => 'Field Date Wajib Diisi',
            'note.required' => 'Field Note Wajib Diisi',
        ];
        $this->validate($request, $rule, $message);
        // dd($request->all());
        // Masukan Data ke Tabel Quotataion
        $productIn->invoice = $request->invoice;
        $productIn->id_supplier = $request->supplier;
        // $productIn->supplier = $request->suplier;
        $productIn->date_invoice = $request->date_invoice;
        $productIn->subtotal = $request->subtotal;
        $productIn->total_no_tax = $request->total_no_tax;
        $productIn->tax = $request->tax;
        $productIn->note = $request->note;
        $productIn->shipping = $request->shipping;
        $productIn->total = $request->total;
        $productInSave = $productIn->save();
        if ($productInSave) {
            // Masukan Data Ke Tabel Detail Quotataion
            foreach ($dProductIn as $item => $value) {
                $value->modal = $request->price[$item];
                $value->amount = $request->amount[$item];
                $value->disc = $request->disc[$item];
                $dProductSave = $value->save();
            }
        }
        if ($dProductSave) {
            return redirect('/product-in')->with('message', 'data telah di tambahkan');
        }
    }
    public function return_in(Request $request, $id)
    {
        $return = Retur::find($id);
        $dReturn = DetailReturn::where('id_retur', $id)->where('status', 1)->get();
        // dd($request->all());
        $rule = [
            'invoice' => 'required',
            // 'suplier' => 'required',
            // 'date_invoice' => 'required',
            'note' => 'required',
        ];
        $message = [
            'invoice.required' => 'Field No Invoice Wajib Diisi',
            // 'suplier.required' => 'Field Suplier Wajib Diisi',
            // 'date_invoice.required' => 'Field Date Wajib Diisi',
            'note.required' => 'Field Note Wajib Diisi',
        ];
        $this->validate($request, $rule, $message);
        // dd($request->all());
        // Masukan Data ke Tabel Quotataiona
        $productIn = new ProductIn();
        $productIn->invoice = $request->invoice;
        $productIn->id_supplier = $request->supplier;
        // $productIn->supplier = $request->suplier;
        $productIn->date_invoice = $request->date_invoice;
        $productIn->date = Carbon::now();
        $productIn->subtotal = $request->subtotal;
        $productIn->total_no_tax = $request->total_no_tax;
        $productIn->tax = $request->tax;
        $productIn->note = $request->note;
        $productIn->shipping = $request->shipping;
        $productIn->total = $request->total;
        $productInSave = $productIn->save();
        if ($productInSave) {
            // Masukan Data Ke Tabel Detail Quotataion
            foreach ($dReturn as $item => $value) {
                $dproductIn = new DetailProductIn();
                $dproductIn->id_product_in = $productIn->id;
                $dproductIn->id_detail_product = $value->id_replacement;
                $dproductIn->qty = $value->qty;
                $dproductIn->warehouse = $request->warehouse[$item];
                $dproductIn->modal = $request->price[$item];
                $dproductIn->amount = $request->amount[$item];
                $dproductIn->disc = !empty($request->disc[$item]) ? $request->disc[$item] : 0;
                $dProductSave = $dproductIn->save();
            }
        }
        $return->status = 1;
        $return->save();
        if ($dProductSave) {
            return redirect('/return/'. $id)->with('message', 'data telah di tambahkan');
        }
    }


    public function logistic_update(Request $request, $id)
    {
        $productIn = ProductIn::find($id);
        $productIn->no_do       = $request->no_do;
        $productIn->date        = $request->date;
        $productIn->id_supplier = $request->id_supplier ?: $productIn->id_supplier;
        $productIn->info        = $request->info;
        $productIn->save();

        foreach ($request->items ?? [] as $detailId => $values) {
            $detail = DetailProductIn::find($detailId);
            if (!$detail) continue;

            $oldDetailProductId = $detail->id_detail_product;
            $oldQty             = $detail->qty;
            $oldWarehouse       = $detail->warehouse;
            $newDetailProductId = $values['id_detail_product'] ?? $oldDetailProductId;
            $newQty             = (int) ($values['qty'] ?? $oldQty);
            $newWarehouse       = $values['warehouse'] ?? $oldWarehouse;

            // Revert stok lama
            $oldProductD = DetailProduct::find($oldDetailProductId);
            if ($oldProductD) {
                $oldProduct = $oldProductD->product;
                if ($oldWarehouse == 'BDG') {
                    $oldProductD->stock           -= $oldQty;
                    $oldProduct->stock            -= $oldQty;
                } else {
                    $oldProductD->warehouse_stock -= $oldQty;
                    $oldProduct->warehouse_stock  -= $oldQty;
                }
                $oldProductD->save();
                $oldProduct->save();
            }

            // Terapkan stok baru
            $newProductD = DetailProduct::find($newDetailProductId);
            if ($newProductD) {
                $newProduct = $newProductD->product;
                if ($newWarehouse == 'BDG') {
                    $newProductD->stock           += $newQty;
                    $newProduct->stock            += $newQty;
                } else {
                    $newProductD->warehouse_stock += $newQty;
                    $newProduct->warehouse_stock  += $newQty;
                }
                $newProductD->save();
                $newProduct->save();
            }

            $detail->id_detail_product = $newDetailProductId;
            $detail->qty               = $newQty;
            $detail->warehouse         = $newWarehouse;
            $detail->save();
        }

        // Tambah item baru
        foreach ($request->new_items ?? [] as $newItem) {
            if (empty($newItem['id_detail_product']) || empty($newItem['qty'])) continue;

            $dProductIn = new DetailProductIn();
            $dProductIn->id_product_in    = $id;
            $dProductIn->id_detail_product = $newItem['id_detail_product'];
            $dProductIn->qty              = (int) $newItem['qty'];
            $dProductIn->warehouse        = $newItem['warehouse'];
            $dProductIn->modal            = null;
            $dProductIn->amount           = null;
            $dProductIn->save();

            $productD = DetailProduct::find($newItem['id_detail_product']);
            if ($productD) {
                $product = $productD->product;
                if ($newItem['warehouse'] == 'BDG') {
                    $productD->stock          += $dProductIn->qty;
                    $product->stock           += $dProductIn->qty;
                } else {
                    $productD->warehouse_stock += $dProductIn->qty;
                    $product->warehouse_stock  += $dProductIn->qty;
                }
                $productD->save();
                $product->save();
            }
        }

        return response()->json(['success' => true, 'message' => 'Data berhasil diperbarui']);
    }

    public function destroyDetail($id)
    {
        $detail = DetailProductIn::find($id);
        if (!$detail) return response()->json(['success' => false, 'message' => 'Item tidak ditemukan'], 404);

        // Revert stok
        $productD = $detail->detailProduct;
        if ($productD) {
            $product = $productD->product;
            if ($detail->warehouse == 'BDG') {
                $productD->stock          -= $detail->qty;
                $product->stock           -= $detail->qty;
            } else {
                $productD->warehouse_stock -= $detail->qty;
                $product->warehouse_stock  -= $detail->qty;
            }
            $productD->save();
            $product->save();
        }

        $detail->delete();
        return response()->json(['success' => true, 'message' => 'Item berhasil dihapus']);
    }

    public function productIn_print($id)
    {
        $product = ProductIn::find($id);
        $detail = DetailProductIn::where('id_product_in', $product->id)->get();
        $tax = $product->total_no_tax * $product->tax / 100;
        return view('pages.warehouse.product-in.detail-print', compact('product', 'detail', 'tax'));
    }

    public function indexSupplier()
    {
        return view('pages.warehouse.supplier.index');
    }
    public function detailSupplier($id)
    {
        $supplier = Supplier::find($id);
        $pics = SupplierPic::where('id_supplier', $id)->orderByDesc('id')->get();

        $currentYear = now()->year;
        $startYear = $currentYear - 4;
        $yearlyTotalsByYear = ProductIn::selectRaw('YEAR(date) as year, SUM(total) as total')
            ->where('id_supplier', $id)
            ->whereNotNull('date')
            ->whereYear('date', '>=', $startYear)
            ->groupBy('year')
            ->pluck('total', 'year');

        $yearlyLabels = [];
        $yearlyTotals = [];
        for ($year = $startYear; $year <= $currentYear; $year++) {
            $yearlyLabels[] = (string) $year;
            $yearlyTotals[] = (int) ($yearlyTotalsByYear[$year] ?? 0);
        }
        $currentYearTotal = (int) ($yearlyTotalsByYear[$currentYear] ?? 0);

        return view('pages.warehouse.supplier.detail', compact(
            'supplier',
            'pics',
            'yearlyLabels',
            'yearlyTotals',
            'currentYearTotal',
            'currentYear'
        ));
    }

    public function storeSupplierPic(Request $request, $id)
    {
        $request->validate([
            'namePic'  => 'required|string',
            'position' => 'nullable|string',
            'phonePic' => 'nullable|string',
            'emailPic' => 'nullable|string',
        ]);

        $pic = new SupplierPic();
        $pic->id_supplier = $id;
        $pic->name_pic = $request->namePic;
        $pic->position = $request->position;
        $pic->phone_pic = $request->phonePic;
        $pic->email_pic = $request->emailPic;
        $pic->save();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'data' => $pic]);
        }

        return redirect('/supplier/' . $id)->with('success', 'PIC berhasil ditambahkan');
    }

    public function updateSupplierPic(Request $request, $id)
    {
        $request->validate([
            'namePic'  => 'required|string',
            'position' => 'nullable|string',
            'phonePic' => 'nullable|string',
            'emailPic' => 'nullable|string',
        ]);

        $pic = SupplierPic::find($id);
        $pic->name_pic = $request->namePic;
        $pic->position = $request->position;
        $pic->phone_pic = $request->phonePic;
        $pic->email_pic = $request->emailPic;
        $pic->save();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'data' => $pic]);
        }

        return redirect('/supplier/' . $pic->id_supplier)->with('success', 'PIC berhasil diubah');
    }

    public function destroySupplierPic($id)
    {
        $pic = SupplierPic::find($id);
        $deleted = $pic->delete();

        return $deleted ? 1 : 0;
    }

    public function storeSupplier(Request $request)
    {
        $supplier = new Supplier();
        $supplier->supplier = $request->supplier;
        $supplier->phone = $request->phone;
        $supplier->email = $request->email;
        $supplier->code = $request->code;
        $supplier->area = $request->area;
        $supplier->address = $request->address;
        $supplier->npwp = $request->npwp;
        $supplier->info = $request->info;
        $supplierSave = $supplier->save();
        if ($supplierSave) {
            return redirect()->back()->with('success', 'Supplier berhasil ditambahkan!');
        }
    }
    public function quickStoreSupplier(Request $request)
    {
        $request->validate([
            'code'     => 'required|string|max:255',
            'supplier' => 'required|string|max:255',
            'info'     => 'required|in:Lokal,Import',
        ]);

        $supplier = new Supplier();
        $supplier->code = $request->code;
        $supplier->supplier = $request->supplier;
        $supplier->info = $request->info;
        $supplier->save();

        return response()->json([
            'success' => true,
            'data'    => $supplier->only('id', 'code', 'supplier', 'info'),
        ]);
    }

    public function deleteSupplier($id)
    {
        $supplier = Supplier::find($id);
        $supplierDel = $supplier->delete();
        if ($supplierDel) {
            return 1;
        } else {
            return 0;
        }
    }
    public function updateSupplier(Request $request, $id)
    {
        $supplier = Supplier::find($id);
        $supplier->supplier = $request->supplier;
        $supplier->phone = $request->phone;
        $supplier->email = $request->email;
        $supplier->code = $request->code;
        $supplier->area = $request->area;
        $supplier->address = $request->address;
        $supplier->npwp = $request->npwp;
        $supplier->info = $request->info;
        $supplierSave = $supplier->save();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => (bool) $supplierSave, 'data' => $supplier]);
        }

        if ($supplierSave) {
            return redirect()->back()->with('success', 'Supplier berhasil ditambahkan!');
        }
    }

    public function editDataSupplier($id)
    {
        $supplier = Supplier::find($id);
        $pics = SupplierPic::where('id_supplier', $id)->orderByDesc('id')->get();

        return response()->json(['supplier' => $supplier, 'pics' => $pics]);
    }
    public function acceptIn($id)
    {
        $product = ProductIn::find($id);
        $product->accept = '1';
        $productSave = $product->save();
        if ($productSave) {
            return 1;
        } else {
            return 0;
        }
    }
    public function return(Request $request, $id)
    {
        $detProduct = DetailProductIn::where('id_product_in', $id)->get();
        foreach ($request->qty as $key => $value) {
            if ($value != 0) {
                // dd($key);
                $return = new Retur();
                $return->id_product_in = $id;
                $return->id_replacement = $detProduct[$key]->id_detail_product;
                $return->qty = $value;
                $return->note = $request->note[$key] ?? '-';
                $return->status = 0;
                $return->date = Carbon::today();
                $returnSave = $return->save();
            }
        }
        if ($returnSave) {
            return redirect()->back()->with('success', 'Data Return Telah Ditambahkan');
        }
    }
    public function clearReturn($id)
    {
        $return = Retur::find($id);
        $return->status = 1;
        $returnSave = $return->save();
        if ($returnSave) {
            return 1;
        } else {
            return 0;
        }
    }
}
