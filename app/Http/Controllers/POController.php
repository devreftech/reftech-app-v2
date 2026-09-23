<?php

namespace App\Http\Controllers;

use App\Models\DetailPurchaseOrder;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\PurchaseRequest;
use App\Models\PurchaseRequestDetail;
use App\Models\PurchaseRequestDetailAllocation;
use App\Models\PurchaseOrderType;
use App\Models\RentalAccessory;
use App\Models\Supplier;
use App\Models\Unit;
use App\Services\PurchaseRequestService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        // Optimized: Products are loaded on-demand via Select2 AJAX (searchProducts) to eliminate 9,200+ <option> tags bottleneck
        $products = collect();
        $poTypes = PurchaseOrderType::orderBy('name')->get();

        $sourcePr = null;
        $sourceProductSet = null;
        $prefillItems = [];
        if ($request->query('from_pr')) {
            $sourcePr = PurchaseRequest::with('details.equivalent.product', 'details.allocations')->find($request->query('from_pr'));
            if ($sourcePr) {
                $selectedItems = $request->query('items', []);

                $detailsToPrefill = $sourcePr->details->filter(function ($detail) use ($selectedItems) {
                    if ($detail->is_rejected) {
                        return false;
                    }
                    if (!empty($selectedItems)) {
                        return array_key_exists((string) $detail->id, $selectedItems) || in_array((string) $detail->id, $selectedItems) || in_array($detail->id, $selectedItems);
                    }
                    // Fallback (akses langsung tanpa selection dari halaman PR): tampilkan
                    // hanya item yang masih ada sisa qty belum teralokasi ke PO manapun.
                    return $detail->remainingQty > 0;
                });

                foreach ($detailsToPrefill as $detail) {
                    $product = $detail->equivalent->product ?? null;
                    if ($product) {
                        $requestedQty = 0;
                        if (!empty($selectedItems)) {
                            if (array_key_exists((string) $detail->id, $selectedItems) && is_numeric($selectedItems[(string) $detail->id])) {
                                $requestedQty = (int) $selectedItems[(string) $detail->id];
                            }
                        }
                        $qty = $requestedQty > 0 ? $requestedQty : $detail->remainingQty;
                        if ($qty <= 0) {
                            continue;
                        }
                        $prefillItems[] = [
                            'id_product' => $product->id,
                            'label' => $product->commodity . ' — ' . $product->description,
                            'qty' => $qty,
                            'unit' => ($product->unit && $product->unit !== '-') ? $product->unit : 'Pcs',
                            'pr_detail_id' => $detail->id,
                            'pr_remaining' => $detail->remainingQty,
                        ];
                    }
                }
            }
        } elseif ($request->query('from_product_set') || $request->has('product_ids') || ($request->has('items') && !$request->has('from_pr'))) {
            if ($request->query('from_product_set')) {
                $sourceProductSet = \App\Models\ProductSet::with('product')->find($request->query('from_product_set'));
            }

            $productMap = []; // [product_id => qty]
            if ($request->has('product_ids')) {
                foreach ((array) $request->query('product_ids') as $pid) {
                    if ($pid) {
                        $productMap[$pid] = 1;
                    }
                }
            }
            if ($request->has('items')) {
                foreach ((array) $request->query('items') as $pid => $qty) {
                    if ($pid) {
                        $productMap[$pid] = max(1, (int) $qty);
                    }
                }
            }

            if (!empty($productMap)) {
                $foundProducts = Product::whereIn('id', array_keys($productMap))->get();
                foreach ($foundProducts as $product) {
                    $qty = $productMap[$product->id] ?? 1;
                    $prefillItems[] = [
                        'id_product' => $product->id,
                        'label' => $product->commodity . ' — ' . $product->description,
                        'qty' => $qty,
                        'unit' => ($product->unit && $product->unit !== '-') ? $product->unit : 'Pcs',
                        'pr_detail_id' => null,
                        'pr_remaining' => null,
                    ];
                }
            }
        }

        $accessories = RentalAccessory::orderBy('name')->get();

        return view('pages.accounting.purchase.form', compact('suppliers', 'previewNoPo', 'units', 'products', 'accessories', 'sourcePr', 'sourceProductSet', 'prefillItems', 'poTypes'));
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

    /**
     * AJAX endpoint for paginated Select2 product search on PO create form.
     */
    public function searchProducts(Request $request)
    {
        // If single ID requested for Select2 pre-fill/hydration
        if ($request->filled('id')) {
            $product = Product::select('id', 'commodity', 'description', 'unit')->find($request->input('id'));
            if ($product) {
                $desc = !empty($product->description) && $product->description !== '-' ? ' — ' . $product->description : '';
                return response()->json([
                    'results' => [[
                        'id' => $product->id,
                        'text' => $product->commodity . $desc,
                        'commodity' => $product->commodity,
                        'description' => $product->description,
                        'unit' => ($product->unit && $product->unit !== '-') ? $product->unit : 'Pcs',
                    ]],
                    'pagination' => ['more' => false],
                ]);
            }
            return response()->json(['results' => [], 'pagination' => ['more' => false]]);
        }

        $q = trim($request->input('q', ''));
        $page = max(1, (int) $request->input('page', 1));
        $limit = 30;

        $query = Product::select('id', 'commodity', 'description', 'unit');

        if (!empty($q)) {
            $query->where(function ($sub) use ($q) {
                $sub->where('commodity', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%");
            });
        }

        $totalCount = $query->count();
        $products = $query->orderBy('commodity')
            ->skip(($page - 1) * $limit)
            ->take($limit)
            ->get();

        $results = $products->map(function ($p) {
            $desc = !empty($p->description) && $p->description !== '-' ? ' — ' . $p->description : '';
            return [
                'id' => $p->id,
                'text' => $p->commodity . $desc,
                'commodity' => $p->commodity,
                'description' => $p->description,
                'unit' => ($p->unit && $p->unit !== '-') ? $p->unit : 'Pcs',
            ];
        });

        return response()->json([
            'results' => $results,
            'pagination' => [
                'more' => ($page * $limit) < $totalCount,
            ],
        ]);
    }

    private function cleanNumber($val): float
    {
        if (is_null($val) || $val === '') {
            return 0.0;
        }
        if (is_int($val) || is_float($val)) {
            return (float) $val;
        }
        $str = trim((string) $val);
        $str = preg_replace('/[^\d.,\-]/', '', $str);
        if ($str === '' || $str === '-') {
            return 0.0;
        }

        $hasComma = strpos($str, ',') !== false;
        $hasDot = strpos($str, '.') !== false;

        if ($hasComma && $hasDot) {
            if (strrpos($str, ',') > strrpos($str, '.')) {
                // Format: 4.311.880,18 -> dot = thousands, comma = decimal
                $str = str_replace('.', '', $str);
                $str = str_replace(',', '.', $str);
            } else {
                // Format: 4,311,880.18 -> comma = thousands, dot = decimal
                $str = str_replace(',', '', $str);
            }
        } elseif ($hasComma) {
            // Only comma e.g. 4311880,18
            $str = str_replace('.', '', $str);
            $str = str_replace(',', '.', $str);
        } elseif ($hasDot) {
            if (substr_count($str, '.') > 1) {
                // Multiple thousands dots: 4.311.880
                $str = str_replace('.', '', $str);
            } else {
                // Exactly one dot: e.g. 500.000 vs 4311880.18
                $parts = explode('.', $str);
                $beforeDot = $parts[0];
                $afterDot = $parts[1] ?? '';
                if (strlen($afterDot) === 3 && strlen($beforeDot) >= 1 && strlen($beforeDot) <= 3) {
                    $str = $beforeDot . $afterDot;
                } else {
                    $str = $beforeDot . '.' . $afterDot;
                }
            }
        }
        return (float) $str;
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

    public function generateNoDirectPurchase(): string
    {
        $year = now()->format('Y');
        $romanMonths = ['I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];
        $roman = $romanMonths[(int) now()->format('n') - 1];
        $suffix = "-DP/RJO/{$roman}/{$year}";

        $last = PurchaseOrder::where('no_po', 'like', '%' . $suffix)
            ->orderByDesc('no_po')
            ->value('no_po');

        $lastSeq = $last ? (int) substr($last, 0, 3) : 0;
        $nextSeq = str_pad($lastSeq + 1, 3, '0', STR_PAD_LEFT);

        return $nextSeq . $suffix;
    }

    /**
     * Show form for creating a simplified Direct Purchase (Non-PO Formal).
     */
    public function createDirect(Request $request)
    {
        $suppliers = Supplier::orderBy('supplier')->get();
        $previewNoDp = $this->generateNoDirectPurchase();

        $sourcePr = null;
        $prefillItems = [];
        if ($request->query('from_pr')) {
            $sourcePr = PurchaseRequest::with('details.equivalent.product', 'details.allocations', 'pending')->find($request->query('from_pr'));
            if ($sourcePr) {
                $selectedItems = $request->query('items', []);

                $detailsToPrefill = $sourcePr->details->filter(function ($detail) use ($selectedItems) {
                    if ($detail->is_rejected) {
                        return false;
                    }
                    if (!empty($selectedItems)) {
                        return array_key_exists((string) $detail->id, $selectedItems) || in_array((string) $detail->id, $selectedItems) || in_array($detail->id, $selectedItems);
                    }
                    return $detail->remainingQty > 0;
                });

                foreach ($detailsToPrefill as $detail) {
                    $product = $detail->equivalent->product ?? null;
                    if ($product) {
                        $requestedQty = 0;
                        if (!empty($selectedItems)) {
                            if (array_key_exists((string) $detail->id, $selectedItems) && is_numeric($selectedItems[(string) $detail->id])) {
                                $requestedQty = (int) $selectedItems[(string) $detail->id];
                            }
                        }
                        $qty = $requestedQty > 0 ? $requestedQty : $detail->remainingQty;
                        if ($qty <= 0) {
                            continue;
                        }
                        $prefillItems[] = [
                            'id_product' => $product->id,
                            'name' => $product->commodity . ($product->description && $product->description != '-' ? ' — ' . $product->description : ''),
                            'brand_pn' => trim(($detail->equivalent->brand ?? '') . ' ' . ($detail->equivalent->pn ?? '')),
                            'qty' => $qty,
                            'unit' => ($product->unit && $product->unit !== '-') ? $product->unit : 'Pcs',
                            'price' => $detail->equivalent->price ?? ($product->price ?? 0),
                            'pr_detail_id' => $detail->id,
                            'pr_remaining' => $detail->remainingQty,
                        ];
                    }
                }
            }
        }

        return view('pages.accounting.purchase.direct-create', compact('suppliers', 'previewNoDp', 'sourcePr', 'prefillItems'));
    }

    /**
     * Store a newly created Direct Purchase in storage.
     */
    public function storeDirect(Request $request)
    {
        $request->validate([
            'no_po' => 'required|string|unique:purchase_order,no_po',
            'date' => 'required|date',
            'supplier' => 'required|integer|exists:supplier,id',
            'product' => 'required|array|min:1',
            'product.*' => 'required|string',
            'qty' => 'required|array',
            'qty.*' => 'required|numeric|min:0.01',
            'price' => 'required|array',
        ], [
            'no_po.required' => 'Nomor transaksi Direct Purchase wajib diisi.',
            'no_po.unique' => 'Nomor transaksi sudah digunakan.',
            'date.required' => 'Tanggal pembelian wajib diisi.',
            'supplier.required' => 'Supplier wajib dipilih dari daftar master supplier.',
            'supplier.exists' => 'Supplier yang dipilih tidak valid atau belum terdaftar.',
            'product.required' => 'Minimal 1 item produk harus ditambahkan.',
            'product.min' => 'Minimal 1 item produk harus ditambahkan.',
            'product.*.required' => 'Nama produk tidak boleh kosong.',
            'qty.*.required' => 'Kuantiti barang wajib diisi.',
            'qty.*.min' => 'Kuantiti barang minimal 0.01.',
        ]);

        return DB::transaction(function () use ($request) {
            $supplierId = $request->supplier;
            $company = $request->supplier_name;
            if ($supplierId) {
                $sup = Supplier::find($supplierId);
                if ($sup) {
                    $company = $sup->supplier;
                }
            }

            $purchase = new PurchaseOrder();
            $purchase->no_po = $request->no_po;
            $purchase->no_reference = $request->no_reference ?? null;
            $purchase->id_supplier = $supplierId;
            $purchase->company = $company ?: 'Direct Purchase';
            $purchase->category = $request->category ?: 'Sparepart';
            $purchase->is_direct_purchase = 1;
            $purchase->id_purchase_request = $request->id_purchase_request ?: null;
            $purchase->date = $request->date;
            $purchase->payment = $request->payment ?: 'Cash';
            $purchase->payment_type = 'cash';
            $purchase->receipt_status = 'Open';
            $purchase->delivery = $request->cargo ?? '';
            $purchase->note = $request->note ?? '';

            $subtotal = 0;
            foreach ($request->product as $key => $prodName) {
                $qty = (float) ($request->qty[$key] ?? 0);
                $price = $this->cleanNumber($request->price[$key] ?? 0);
                $subtotal += ($qty * $price);
            }
            $deliveryCost = $this->cleanNumber($request->delivery_cost ?? 0);
            $diskon = 0;
            if ($request->filled('discount_type') && $request->filled('discount_value')) {
                $discVal = $this->cleanNumber($request->discount_value);
                if ($request->discount_type === 'percent') {
                    $diskon = ($subtotal * $discVal) / 100;
                } else {
                    $diskon = $discVal;
                }
            } elseif ($request->filled('diskon')) {
                $diskon = $this->cleanNumber($request->diskon);
            }
            $diskon = min($diskon, $subtotal);

            $purchase->subtotal = $subtotal;
            $purchase->diskon = $diskon;
            $purchase->delivery_cost = $deliveryCost;
            $purchase->total = max(0, $subtotal - $diskon + $deliveryCost);
            $purchase->on_delivery_cargo = $request->cargo ?? null;
            $purchase->on_delivery_no_resi = $request->no_resi ?? null;
            $purchase->save();

            $affectedPrIds = [];
            if ($purchase->id_purchase_request) {
                $affectedPrIds[] = (int) $purchase->id_purchase_request;
            }

            foreach ($request->product as $key => $prodName) {
                $qty = (float) ($request->qty[$key] ?? 0);
                $price = $this->cleanNumber($request->price[$key] ?? 0);
                $amount = $qty * $price;
                $unit = $request->unit[$key] ?? 'Pcs';
                $productId = $request->id_product[$key] ?? null;

                $dPurchase = new DetailPurchaseOrder();
                $dPurchase->id_purchase_order = $purchase->id;
                $dPurchase->product = $prodName;
                $dPurchase->category = $purchase->category;
                $dPurchase->id_product = $productId;
                $dPurchase->qty = $qty;
                $dPurchase->info_qty = $unit;
                $dPurchase->price = $price;
                $dPurchase->amount = $amount;
                $dPurchase->save();

                // Link to PR detail allocation if created from PR
                $prDetailId = $request->pr_detail_id[$key] ?? null;
                if ($prDetailId) {
                    $prDetail = PurchaseRequestDetail::find($prDetailId);
                    if ($prDetail && $prDetail->remainingQty > 0) {
                        $allocQty = min((int) $qty, $prDetail->remainingQty);
                        PurchaseRequestDetailAllocation::create([
                            'id_purchase_request_detail' => $prDetail->id,
                            'id_purchase_order' => $purchase->id,
                            'qty' => $allocQty > 0 ? $allocQty : 1,
                            'purchase_type' => $request->purchase_type ?? 'Lokal',
                            'cargo' => $request->cargo ?? null,
                            'no_resi' => $request->no_resi ?? null,
                            'purchase_date' => $request->date,
                        ]);
                        $affectedPrIds[] = (int) $prDetail->id_purchase_request;
                    }
                }
            }

            $affectedPrIds = array_values(array_unique(array_filter($affectedPrIds)));
            if (!empty($affectedPrIds) && !$purchase->id_purchase_request) {
                $purchase->id_purchase_request = $affectedPrIds[0];
                $purchase->save();
            }

            // Cek update status PR ke On Delivery (status = 2) jika seluruh kebutuhan PR sudah dialokasi dan ada info kirim
            foreach ($affectedPrIds as $prId) {
                $pr = PurchaseRequest::with(['details.allocations'])->find($prId);
                if ($pr && $pr->status == '1') {
                    if ($this->prService->allDeliveriesSubmitted($pr) && $this->prService->isFullyAllocated($pr)) {
                        $pr->status = '2';
                        $pr->save();
                    }
                }
            }

            if ($request->filled('id_purchase_request')) {
                return redirect()->route('purchase-request.show', $purchase->id_purchase_request)
                    ->with('success', "Direct Purchase {$purchase->no_po} berhasil dibuat dan dialokasikan ke Purchase Request.");
            }

            return redirect()->route('purchase.show', $purchase->id)
                ->with('success', "Direct Purchase {$purchase->no_po} berhasil dibuat.");
        });
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
            'supplier' => 'required|integer|exists:supplier,id',
            'date' => 'required|date',
            'product' => 'required|array|min:1',
        ];
        $messages = [
            'no_po.required' => 'Nomor PO wajib diisi.',
            'no_po.unique' => 'Nomor PO sudah pernah digunakan.',
            'supplier.required' => 'Supplier wajib dipilih dari daftar master supplier.',
            'supplier.exists' => 'Supplier yang dipilih tidak ditemukan.',
            'date.required' => 'Tanggal PO wajib diisi.',
            'product.required' => 'Minimal 1 item produk harus ditambahkan pada PO.',
            'product.min' => 'Minimal 1 item produk harus ditambahkan pada PO.',
        ];
        $this->validate($request, $rule, $messages);

        return DB::transaction(function () use ($request) {
            $supplier = Supplier::find($request->supplier);
            $itemCategories = $request->item_category ?? [];
            $purchase = new PurchaseOrder();
            $purchase->id_supplier = $request->supplier;
            $purchase->id_purchase_request = $request->id_purchase_request ?: null;
            $purchase->no_po = $request->no_po;
            $purchase->no_reference = $request->no_reference ?? null;
            $purchase->category = $request->category ?: (in_array('Accessories', $itemCategories) ? 'Accessories' : (in_array('Unit', $itemCategories) ? 'Unit' : 'Sparepart'));
            $purchase->company = $supplier ? $supplier->supplier : 'Supplier';
            $purchase->attn = $request->attn ?? '';
            $purchase->mobile = $request->mobile ?? '';
            $purchase->delivery = $request->delivery ?? '';
            $purchase->ship_to = $request->ship_to ?? null;
            $purchase->date = $request->date;
            $purchase->email = $supplier->email ?? '-';
            $purchase->phone = $supplier->phone ?? '-';
            $purchase->address = $request->address ?? $supplier->address ?? '-';
            $purchase->payment = $request->payment ?? '';
            $this->applyPaymentTerms($purchase, $request);
            $purchase->note = $request->note ?? '';
            $purchase->subtotal = $this->cleanNumber($request->subtotal);
            $purchase->vat = $this->cleanNumber($request->tax);
            $purchase->diskon = $this->cleanNumber($request->diskon);
            $purchase->delivery_cost = $this->cleanNumber($request->delivery_cost);
            $purchase->total = $this->cleanNumber($request->harga_total);
            $purchase->save();

            foreach ($request->product as $key => $value) {
                $itemCategory = $itemCategories[$key] ?? 'Sparepart';
                $dPurchase = new DetailPurchaseOrder();
                $dPurchase->id_purchase_order = $purchase->id;
                $dPurchase->product = $value;
                $dPurchase->category = $itemCategory;
                $dPurchase->id_unit = $itemCategory == 'Unit' ? ($request->id_unit[$key] ?? null) : null;
                $dPurchase->id_rental_accessory = $itemCategory == 'Accessories' ? ($request->id_rental_accessory[$key] ?? null) : null;
                $dPurchase->kondisi = $itemCategory == 'Unit' ? ($request->kondisi[$key] ?? 'Baru') : null;
                $dPurchase->id_product = $itemCategory == 'Sparepart' ? ($request->id_product[$key] ?? null) : null;
                $dPurchase->qty = $itemCategory == 'Header' ? 0 : (float) ($request->qty[$key] ?? 0);
                $dPurchase->info_qty = $itemCategory == 'Header' ? '' : ($request->info_qty[$key] ?? '');
                $dPurchase->price = $itemCategory == 'Header' ? 0 : $this->cleanNumber($request->price[$key] ?? 0);
                $dPurchase->disc = $itemCategory == 'Header' ? 0 : $this->cleanNumber($request->disc[$key] ?? 0);
                $dPurchase->amount = $itemCategory == 'Header' ? 0 : $this->cleanNumber($request->amount[$key] ?? 0);
                $dPurchase->save();

                if ($itemCategory !== 'Header') {
                    $prDetailId = $request->pr_detail_id[$key] ?? null;
                    if ($prDetailId) {
                        $prDetail = PurchaseRequestDetail::find($prDetailId);
                        if ($prDetail && $prDetail->remainingQty > 0) {
                            PurchaseRequestDetailAllocation::create([
                                'id_purchase_request_detail' => $prDetail->id,
                                'id_purchase_order' => $purchase->id,
                                'qty' => min((int) ($request->qty[$key] ?? 0), $prDetail->remainingQty),
                            ]);
                        }
                    }
                }
            }

            // Jika id_purchase_request belum diset tapi ada item dari PR, set ke PR pertama
            if (!$purchase->id_purchase_request) {
                $firstAlloc = PurchaseRequestDetailAllocation::where('id_purchase_order', $purchase->id)->first();
                if ($firstAlloc && $firstAlloc->purchaseRequestDetail) {
                    $purchase->id_purchase_request = $firstAlloc->purchaseRequestDetail->id_purchase_request;
                    $purchase->save();
                }
            }

            return redirect('purchase/' . $purchase->id)->with('success', 'data berhasil ditambahkan');
        });
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
        if (!$purchase) {
            return redirect()->route('purchase.index')->with('error', 'Purchase Order tidak ditemukan');
        }
        $dPurchase = DetailPurchaseOrder::where('id_purchase_order', $id)->get();
        $hargaSebelumPpn = ($purchase->subtotal ?? 0) - ($purchase->diskon ?? 0);
        $dpp = ($purchase->vat ?? 0) > 0 ? round(($hargaSebelumPpn * 11) / 12) : 0;
        $tax = ($purchase->vat ?? 0) > 0 ? round(($dpp * 12) / 100) : 0;
        $totalPph = 0;
        foreach ($dPurchase as $product) {
            $pph = ($product->amount * $product->pph) / 100;
            $totalPph += $pph;
        }

        $sourcePr = null;
        $prDeliveryDone = false;
        $prDeliveryType = null;
        $linkedPrs = $purchase->linkedPurchaseRequests;
        if ($linkedPrs->isNotEmpty()) {
            $sourcePr = $linkedPrs->first();
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

        if ($purchase->is_direct_purchase) {
            return view('pages.accounting.purchase.direct-detail', compact('purchase', 'dPurchase', 'dpp', 'tax', 'totalPph', 'sourcePr', 'linkedPrs', 'prDeliveryDone', 'prDeliveryType', 'productIns'));
        }

        return view('pages.accounting.purchase.detail', compact('purchase', 'dPurchase', 'dpp', 'tax', 'totalPph', 'sourcePr', 'linkedPrs', 'prDeliveryDone', 'prDeliveryType', 'productIns'));
    }

    /**
     * Update info pengiriman & resi secara fleksibel (untuk Direct Purchase maupun PO).
     */
    public function updateDeliveryInfo(Request $request, $id)
    {
        $request->validate([
            'cargo' => 'nullable|string|max:255',
            'no_resi' => 'nullable|string|max:255',
            'purchase_type' => 'nullable|string|max:50',
            'purchase_date' => 'nullable|date',
            'delivery_cost' => 'nullable',
        ]);

        return DB::transaction(function () use ($request, $id) {
            $purchase = PurchaseOrder::findOrFail($id);

            $cargo = $request->cargo ?: null;
            $noResi = $request->no_resi ?: null;
            $purchaseType = $request->purchase_type ?: 'Lokal';
            $purchaseDate = $request->purchase_date ?: ($purchase->date ?: date('Y-m-d'));

            if ($request->has('delivery_cost')) {
                $deliveryCost = $this->cleanNumber($request->delivery_cost);
                $purchase->delivery_cost = $deliveryCost;
                $purchase->total = max(0, ($purchase->subtotal ?? 0) - ($purchase->diskon ?? 0) + $deliveryCost);
            }

            $purchase->delivery = $cargo ?? ($purchase->delivery ?? '');
            $purchase->on_delivery_cargo = $cargo;
            $purchase->on_delivery_no_resi = $noResi;
            $purchase->save();

            // Update seluruh alokasi PR terkait PO/Direct Purchase ini
            $allocations = PurchaseRequestDetailAllocation::where('id_purchase_order', $purchase->id)->get();
            if ($allocations->isNotEmpty()) {
                PurchaseRequestDetailAllocation::where('id_purchase_order', $purchase->id)->update([
                    'purchase_type' => $purchaseType,
                    'cargo' => $cargo,
                    'no_resi' => $noResi,
                    'purchase_date' => $purchaseDate,
                ]);

                // Cek dan update status PR yang terhubung jika seluruh alokasi sudah memiliki info pengiriman
                $linkedPrs = $purchase->linkedPurchaseRequests;
                foreach ($linkedPrs as $pr) {
                    if ((int) $pr->status === 1 && $this->prService->allDeliveriesSubmitted($pr)) {
                        $pr->status = '2';
                        $pr->save();
                    }
                }
            }

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Informasi pengiriman & no resi berhasil diperbarui.',
                    'cargo' => $cargo,
                    'no_resi' => $noResi,
                    'purchase_type' => $purchaseType,
                    'delivery_cost' => $purchase->delivery_cost,
                    'total' => $purchase->total,
                ]);
            }

            return redirect()->back()->with('success', 'Informasi pengiriman & resi berhasil diperbarui.');
        });
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

        $purchase = PurchaseOrder::find($id);
        if (!$purchase || !$purchase->id_purchase_request) {
            return response()->json(['message' => 'Purchase Order ini tidak terkait dengan Purchase Request.'], 422);
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
            'date_invoice' => 'required|date',
            // Hanya relevan untuk PO tempo — override estimasi jatuh tempo.
            'due_date' => 'nullable|date',
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
        $purchase->invoice_date = $request->date_invoice;
        $purchase->save();

        // ── Titik lahirnya Account Payable ──────────────────────────────────────
        // Baris AP (Purchase Invoice) baru muncul di modul Finance ketika invoice
        // supplier diisi di sini. Jatuh tempo dihitung dari tanggal invoice + termin
        // PO; PO non-tempo (cash/transfer) tidak punya jatuh tempo.
        $dueDate = $purchase->resolveDueDate($request->date_invoice, $request->due_date);

        \App\Models\ProductIn::where('id_purchase_order', $purchase->id)
            ->get()
            ->each(function ($pi) use ($purchase, $request, $dueDate) {
                $pi->invoice = $purchase->no_invoice_supplier;
                $pi->date_invoice = $request->date_invoice;
                $pi->date_payment = $dueDate; // null utk PO non-tempo → AP tanpa due date
                $pi->save();
            });

        return redirect()->route('purchase.show', $purchase->id)
            ->with('success', 'Invoice supplier berhasil disimpan.');
    }

    /**
     * Set kolom termin pembayaran PO dari request form.
     * top_days & due_date_estimate hanya disimpan untuk tipe 'tempo'.
     */
    private function applyPaymentTerms(PurchaseOrder $purchase, Request $request): void
    {
        $type = in_array($request->payment_type, ['cash', 'transfer', 'tempo'], true)
            ? $request->payment_type
            : 'cash';

        $purchase->payment_type = $type;

        if ($type === 'tempo') {
            $purchase->top_days = $request->filled('top_days') ? max(0, (int) $request->top_days) : 30;
            $purchase->due_date_estimate = $request->due_date_estimate
                ?: ($request->date ? \Carbon\Carbon::parse($request->date)->addDays($purchase->top_days)->toDateString() : null);
        } else {
            $purchase->top_days = null;
            $purchase->due_date_estimate = null;
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $suppliers = Supplier::all();
        $purchase = PurchaseOrder::with('supplier')->find($id);
        $dPurchase = DetailPurchaseOrder::where('id_purchase_order', $id)->get();
        $units = Unit::where('type', 'global')->orderBy('brand')->get();
        $products = Product::orderBy('commodity')->get();
        $accessories = RentalAccessory::orderBy('name')->get();
        $poTypes = PurchaseOrderType::orderBy('name')->get();
        return view('pages.accounting.purchase.form', compact('suppliers', 'purchase', 'dPurchase', 'units', 'products', 'accessories', 'poTypes'));
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
        $purchase->no_reference = $request->no_reference ?? null;
        $purchase->category = $request->category ?: (in_array('Accessories', $itemCategories) ? 'Accessories' : (in_array('Unit', $itemCategories) ? 'Unit' : 'Sparepart'));
        $purchase->company = $supplier->supplier;
        $purchase->attn = $request->attn ?? '';
        $purchase->mobile = $request->mobile ?? '';
        $purchase->delivery = $request->delivery ?? '';
        $purchase->ship_to = $request->ship_to ?? null;
        $purchase->date = $request->date;
        $purchase->email = $supplier->email ?? '-';
        $purchase->phone = $supplier->phone ?? '-';
        $purchase->address = $request->address ?? $supplier->address ?? '-';
        $purchase->payment = $request->payment ?? '';
        $this->applyPaymentTerms($purchase, $request);
        $purchase->note = $request->note ?? '';
        $purchase->subtotal = $this->cleanNumber($request->subtotal);
        $purchase->vat = $this->cleanNumber($request->tax);
        $purchase->diskon = $this->cleanNumber($request->diskon);
        $purchase->delivery_cost = $this->cleanNumber($request->delivery_cost);
        $purchase->total = $this->cleanNumber($request->harga_total);
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
                $dPurchase->id_rental_accessory = $itemCategory == 'Accessories' ? ($request->id_rental_accessory[$key] ?? null) : null;
                $dPurchase->kondisi = $itemCategory == 'Unit' ? ($request->kondisi[$key] ?? 'Baru') : null;
                $dPurchase->id_product = $itemCategory == 'Sparepart' ? ($request->id_product[$key] ?? null) : null;
                $dPurchase->qty = $itemCategory == 'Header' ? 0 : (float) ($request->qty[$key] ?? 0);
                $dPurchase->info_qty = $itemCategory == 'Header' ? '' : ($request->info_qty[$key] ?? '');
                $dPurchase->price = $itemCategory == 'Header' ? 0 : $this->cleanNumber($request->price[$key] ?? 0);
                $dPurchase->disc = $itemCategory == 'Header' ? 0 : $this->cleanNumber($request->disc[$key] ?? 0);
                $dPurchase->amount = $itemCategory == 'Header' ? 0 : $this->cleanNumber($request->amount[$key] ?? 0);
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
        $hargaSebelumPpn = ($purchase->subtotal ?? 0) - ($purchase->diskon ?? 0);
        $dpp = ($purchase->vat ?? 0) > 0 ? round(($hargaSebelumPpn * 11) / 12) : 0;
        $tax = ($purchase->vat ?? 0) > 0 ? round(($dpp * 12) / 100) : 0;
        $totalPph = 0;
        foreach ($dPurchase as $item) {
            $pph = ($item->amount * $item->pph) / 100;
            $totalPph += $pph;
        }
        return view('pages.accounting.purchase.detail-print', compact('purchase', 'dPurchase', 'dpp', 'tax', 'totalPph'));
    }

    public function add_pph(Request $request, $id)
    {
        if ($request->has('pph_by_id')) {
            foreach ($request->pph_by_id as $dId => $pphVal) {
                DetailPurchaseOrder::where('id', $dId)->update(['pph' => $pphVal ?? 0]);
            }
        } else {
            $DPO = DetailPurchaseOrder::where('id_purchase_order', $id)->get();
            foreach ($DPO as $item => $value) {
                $value->pph = $value->category === 'Header' ? 0 : ($request->pph[$item] ?? 0);
                $value->save();
            }
        }
        return redirect('/purchase/' . $id)->with('massage', 'Data telah terkirim');
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
