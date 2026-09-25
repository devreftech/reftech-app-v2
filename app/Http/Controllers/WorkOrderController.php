<?php

namespace App\Http\Controllers;

use App\Models\DetailProduct;
use App\Models\DetailProductOut;
use App\Models\FixedAsset;
use App\Models\FixedAssetService;
use App\Models\Machine;
use App\Models\PendingPO;
use App\Models\Product;
use App\Models\ProductOut;
use App\Models\PurchaseRequest;
use App\Models\PurchaseRequestDetail;
use App\Models\SerialProduct;
use App\Models\User;
use App\Models\WorkOrder;
use App\Models\WorkOrderItem;
use App\Services\PurchaseRequestService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WorkOrderController extends Controller
{
    protected $prService;

    public function __construct(PurchaseRequestService $prService)
    {
        $this->prService = $prService;
    }

    /**
     * Generate Nomor Work Order: 001-WO/IX/2026
     */
    public function generateNoWo(): string
    {
        $now = now();
        $year = $now->format('Y');
        $romanMonths = ['I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];
        $roman = $romanMonths[(int) $now->format('n') - 1];
        $suffix = "-WO/{$roman}/{$year}";

        $last = WorkOrder::where('no_wo', 'like', '%' . $suffix)
            ->orderByDesc('id')
            ->value('no_wo');

        $lastSeq = $last ? (int) substr($last, 0, 3) : 0;
        $nextSeq = str_pad($lastSeq + 1, 3, '0', STR_PAD_LEFT);

        return $nextSeq . $suffix;
    }

    /**
     * Tampilan List Work Order dengan KPI Counters & Filter
     */
    public function index(Request $request)
    {
        $status = $request->get('status', 'all');

        $query = WorkOrder::with([
            'fixedAsset',
            'machine',
            'creator',
            'technician',
            'warehouseUser',
            'accountingUser',
            'productOut',
            'items'
        ]);

        if ($status !== 'all' && in_array($status, ['pending_warehouse', 'waiting_pr', 'pending_accounting', 'approved', 'issued', 'rejected'])) {
            $query->where('status', $status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('no_wo', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('fixedAsset', function ($fa) use ($search) {
                        $fa->where('code', 'like', "%{$search}%")
                            ->orWhere('desc', 'like', "%{$search}%");
                    })
                    ->orWhereHas('creator', function ($u) use ($search) {
                        $u->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $workOrders = $query->orderByDesc('id')->paginate(15)->withQueryString();

        // KPI Counters
        $counts = [
            'all' => WorkOrder::count(),
            'pending_warehouse' => WorkOrder::where('status', 'pending_warehouse')->count(),
            'waiting_pr' => WorkOrder::where('status', 'waiting_pr')->count(),
            'pending_accounting' => WorkOrder::where('status', 'pending_accounting')->count(),
            'approved' => WorkOrder::where('status', 'approved')->count(),
            'issued' => WorkOrder::where('status', 'issued')->count(),
            'rejected' => WorkOrder::where('status', 'rejected')->count(),
        ];

        return view('pages.workshop.work-orders.index', compact('workOrders', 'counts', 'status'));
    }

    /**
     * Form Buat Work Order Baru (Role ServiceM / Admin / Support)
     */
    public function create()
    {
        // Ambil daftar Fixed Asset kategori Mesin
        $fixedAssets = FixedAsset::where('fixed_asset.type', 'Mesin')
            ->leftJoin('unit', 'unit.id', '=', 'fixed_asset.id_unit')
            ->select('fixed_asset.*', 'unit.brand as unit_brand', 'unit.model as unit_model')
            ->orderBy('fixed_asset.code')
            ->get();

        // Ambil daftar Teknisi
        $technicians = User::whereIn('role', ['Technician', 'ServiceM', 'Support', 'Admin', 'Developer'])
            ->orderBy('name')
            ->get();

        $suggestedNoWo = $this->generateNoWo();

        return view('pages.workshop.work-orders.create', compact('fixedAssets', 'technicians', 'suggestedNoWo'));
    }

    /**
     * Simpan Work Order Baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_fixed_asset' => 'required|exists:fixed_asset,id',
            'date' => 'required|date',
            'target_date' => 'nullable|date',
            'id_user_technician' => 'nullable|exists:users,id',
            'description' => 'required|string',
            'items' => 'required|array|min:1',
            'items.*.item_name' => 'required|string',
            'items.*.qty_requested' => 'required|numeric|min:0.01',
            'items.*.unit' => 'nullable|string',
            'items.*.note' => 'nullable|string',
        ]);

        return DB::transaction(function () use ($request) {
            $fixedAsset = FixedAsset::findOrFail($request->id_fixed_asset);

            $wo = new WorkOrder();
            $wo->no_wo = $this->generateNoWo();
            $wo->id_fixed_asset = $fixedAsset->id;
            $wo->id_machine = $fixedAsset->id_machine;
            $wo->id_user_created = Auth::id();
            $wo->id_user_technician = $request->id_user_technician ?: null;
            $wo->date = $request->date;
            $wo->target_date = $request->target_date ?: null;
            $wo->status = 'pending_warehouse';
            $wo->description = $request->description;
            $wo->total_cost = 0;
            $wo->save();

            foreach ($request->items as $itemData) {
                if (empty($itemData['item_name'])) continue;

                $qty = (float) ($itemData['qty_requested'] ?? 1);
                $unit = $itemData['unit'] ?? 'Pcs';

                $item = new WorkOrderItem();
                $item->id_work_order = $wo->id;
                $item->item_name = trim($itemData['item_name']);
                $item->qty_requested = $qty;
                $item->qty_approved = $qty;
                $item->unit = $unit;
                $item->warehouse = 'BDG';
                $item->qty_issued = 0;
                $item->stock_at_check = 0;
                $item->unit_price = 0;
                $item->subtotal = 0;
                $item->needs_pr = false;
                $item->note = $itemData['note'] ?? null;
                $item->save();
            }

            return redirect()->route('work-orders.show', $wo->id)
                ->with('success', "Work Order {$wo->no_wo} berhasil dibuat dan diteruskan ke Gudang untuk pengecekan ketersediaan spare part.");
        });
    }

    /**
     * Detail Work Order & Tracking Status
     */
    public function show($id)
    {
        $wo = WorkOrder::with([
            'fixedAsset.unit',
            'machine',
            'creator',
            'technician',
            'warehouseUser',
            'accountingUser',
            'purchaseRequest.activeDetails',
            'productOut.detail',
            'items.detailProduct.product.serial',
            'items.product.serial',
            'items.equivalent',
        ])->findOrFail($id);

        // Update real-time stock check for display
        foreach ($wo->items as $item) {
            if ($item->detailProduct) {
                $dp = $item->detailProduct;
                $item->current_stock = $item->warehouse === 'BKS' ? (float) $dp->warehouse_stock : (float) $dp->stock;
            } else {
                $item->current_stock = 0;
            }
        }

        return view('pages.workshop.work-orders.show', compact('wo'));
    }

    /**
     * Verifikasi Gudang: Cek ketersediaan stok, mapping spare part katalog & teruskan ke Accounting
     */
    public function verifyWarehouse(Request $request, $id)
    {
        $allowedRoles = ['Developer', 'Admin', 'Super Admin', 'Logistic', 'Warehouse'];
        if (!in_array(Auth::user()->role, $allowedRoles)) {
            abort(403, 'Hanya Gudang / Admin yang dapat memverifikasi stok Work Order.');
        }

        $wo = WorkOrder::with('items')->findOrFail($id);

        if (!in_array($wo->status, ['pending_warehouse', 'waiting_pr'])) {
            return redirect()->back()->with('error', 'Status Work Order tidak dalam tahap verifikasi gudang.');
        }

        $request->validate([
            'warehouse_note' => 'nullable|string',
            'action_type' => 'nullable|in:save_draft,verify_accounting',
            'items' => 'nullable|array',
        ]);

        return DB::transaction(function () use ($request, $wo) {
            $totalCost = 0;

            if ($request->has('items')) {
                foreach ($request->items as $itemKey => $itemData) {
                    $item = null;
                    if (is_numeric($itemKey)) {
                        $item = WorkOrderItem::where('id_work_order', $wo->id)->find($itemKey);
                    }
                    if (!$item) {
                        $item = new WorkOrderItem();
                        $item->id_work_order = $wo->id;
                        $item->item_name = $itemData['item_name'] ?? 'Spare Part Tambahan';
                        $item->qty_requested = (float) ($itemData['qty_approved'] ?? 1);
                        $item->unit = $itemData['unit'] ?? 'Pcs';
                    }

                    $idDetailProduct = $itemData['id_detail_product'] ?? null;
                    $warehouse = in_array($itemData['warehouse'] ?? 'BDG', ['BDG', 'BKS']) ? $itemData['warehouse'] : 'BDG';
                    $qtyApproved = (float) ($itemData['qty_approved'] ?? $item->qty_requested);
                    if ($qtyApproved <= 0) $qtyApproved = (float) $item->qty_requested;

                    $item->warehouse = $warehouse;
                    $item->qty_approved = $qtyApproved;
                    if (isset($itemData['unit'])) {
                        $item->unit = $itemData['unit'];
                    }
                    if (isset($itemData['note'])) {
                        $item->note = $itemData['note'];
                    }

                    if ($idDetailProduct) {
                        $dp = DetailProduct::with('product')->find($idDetailProduct);
                        if ($dp) {
                            $item->id_detail_product = $dp->id;
                            $item->id_product = $dp->id_product;
                            $currentStock = $warehouse === 'BKS' ? (float) $dp->warehouse_stock : (float) $dp->stock;
                            $item->stock_at_check = $currentStock;
                            
                            $unitPrice = isset($itemData['unit_price']) && (float)$itemData['unit_price'] > 0
                                ? (float)$itemData['unit_price']
                                : ($dp->hpp > 0 ? (float)$dp->hpp : (float)($dp->modal ?? 0));
                            
                            $item->unit_price = $unitPrice;
                            $item->subtotal = $qtyApproved * $unitPrice;
                            $item->needs_pr = $currentStock < $qtyApproved;
                        }
                    } else {
                        $item->stock_at_check = 0;
                        $item->unit_price = (float) ($itemData['unit_price'] ?? 0);
                        $item->subtotal = $qtyApproved * $item->unit_price;
                        $item->needs_pr = true;
                    }

                    $item->save();
                    $totalCost += $item->subtotal;
                }
            }

            // Hapus item yang dihapus oleh gudang jika ada list ID
            if ($request->has('deleted_item_ids') && is_array($request->deleted_item_ids)) {
                WorkOrderItem::where('id_work_order', $wo->id)
                    ->whereIn('id', $request->deleted_item_ids)
                    ->delete();
            }

            // Recalculate total cost
            $totalCost = $wo->items()->sum('subtotal');
            $wo->total_cost = $totalCost;
            $wo->id_user_warehouse = Auth::id();
            if ($request->filled('warehouse_note')) {
                $wo->warehouse_note = $request->warehouse_note;
            }

            $actionType = $request->get('action_type', 'verify_accounting');
            if ($actionType === 'verify_accounting') {
                $wo->status = 'pending_accounting';
                $wo->save();

                return redirect()->route('work-orders.show', $wo->id)
                    ->with('success', "Stok dan pemilihan spare part Work Order {$wo->no_wo} telah diverifikasi dan diteruskan ke Accounting untuk persetujuan biaya.");
            } else {
                $wo->save();

                return redirect()->route('work-orders.show', $wo->id)
                    ->with('success', "Pilihan spare part dan alokasi stok untuk Work Order {$wo->no_wo} berhasil disimpan.");
            }
        });
    }

    /**
     * Gudang Membuat Purchase Request (PR) otomatis dari Work Order untuk item yang kurang stok
     */
    public function generatePr(Request $request, $id)
    {
        if (!in_array(Auth::user()->role, ['Developer', 'Admin', 'Super Admin', 'Logistic', 'Warehouse'])) {
            abort(403, 'Akses tidak diizinkan.');
        }

        $wo = WorkOrder::with(['fixedAsset', 'items.detailProduct.product'])->findOrFail($id);

        return DB::transaction(function () use ($request, $wo) {
            // 1. Buat Header PendingPO
            $year = now()->format('Y');
            $month = now()->format('m');
            $prefixSo = "SO-WO/{$year}/{$month}/";
            $lastSo = PendingPO::where('no_pending', 'like', $prefixSo . '%')->orderByDesc('id')->value('no_pending');
            $lastSeq = $lastSo ? (int) substr($lastSo, -3) : 0;
            $noPending = $prefixSo . str_pad($lastSeq + 1, 3, '0', STR_PAD_LEFT);

            $machineCode = $wo->fixedAsset->code ?? $wo->no_wo;
            $pending = new PendingPO();
            $pending->no_pending = $noPending;
            $pending->title = "Pengadaan Spare Part Work Order {$wo->no_wo} - Mesin {$machineCode}";
            $pending->type = 'Manual';
            $pending->status = '1';
            $pending->date = now()->toDateString();
            $pending->save();

            // 2. Buat Purchase Request
            $pr = new PurchaseRequest();
            $pr->no_pr = $this->prService->generateNoPr();
            $pr->id_pending = $pending->id;
            $pr->id_user = Auth::id();
            $pr->status = '1'; // Langsung status ACC agar Purchasing bisa langsung buatkan PO
            $pr->date = now()->toDateString();
            $pr->save();

            // 3. Masukkan item yang butuh PR
            $prItemCount = 0;
            foreach ($wo->items as $item) {
                // Cek apakah item ini diminta PR (atau default semua item yang stoknya kurang)
                $currentStock = $item->warehouse === 'BDG' 
                    ? (float) ($item->detailProduct->stock ?? 0) 
                    : (float) ($item->detailProduct->warehouse_stock ?? 0);

                $neededQty = $item->qty_approved > 0 ? $item->qty_approved : $item->qty_requested;
                $shortage = max(0, $neededQty - $currentStock);

                // Buat PR detail untuk kuantiti yang dibutuhkan
                $qtyToOrder = $shortage > 0 ? $shortage : $neededQty;

                $pr->details()->create([
                    'id_equivalent' => $item->id_product ?: ($item->detailProduct->id_product ?? 1),
                    'qty' => $qtyToOrder,
                    'qty_stock' => $currentStock,
                    'price' => $item->unit_price,
                    'amount' => $qtyToOrder * $item->unit_price,
                    'note' => "Untuk Work Order {$wo->no_wo} (Mesin {$machineCode})",
                ]);

                $item->needs_pr = true;
                $item->save();
                $prItemCount++;
            }

            // 4. Update Work Order
            $wo->id_purchase_request = $pr->id;
            $wo->id_user_warehouse = Auth::id();
            $wo->status = 'waiting_pr';
            if ($request->filled('warehouse_note')) {
                $wo->warehouse_note = $request->warehouse_note;
            }
            $wo->save();

            return redirect()->route('work-orders.show', $wo->id)
                ->with('success', "Purchase Request {$pr->no_pr} berhasil dibuat dan otomatis masuk ke antrian Monitoring Purchase Request.");
        });
    }

    /**
     * Approval Accounting: Menentukan perlakuan akuntansi (Beban vs Kapitalisasi) & Approve
     */
    public function approveAccounting(Request $request, $id)
    {
        if (!in_array(Auth::user()->role, ['Developer', 'Admin', 'Super Admin', 'Accounting', 'Finance', 'Finance Manager'])) {
            abort(403, 'Hanya tim Accounting / Finance yang berwenang menyetujui biaya Work Order.');
        }

        $wo = WorkOrder::findOrFail($id);

        if (!in_array($wo->status, ['pending_accounting', 'waiting_pr'])) {
            return redirect()->back()->with('error', 'Status Work Order tidak dalam tahap persetujuan Accounting.');
        }

        $request->validate([
            'accounting_treatment' => 'required|in:expense,capitalize',
            'accounting_note' => 'nullable|string',
        ]);

        $wo->id_user_accounting = Auth::id();
        $wo->accounting_treatment = $request->accounting_treatment;
        $wo->accounting_note = $request->accounting_note;
        $wo->status = 'approved';
        $wo->save();

        $treatmentLabel = $wo->accounting_treatment === 'capitalize' ? 'Kapitalisasi Nilai Aset' : 'Beban Pemeliharaan (Expense)';

        return redirect()->route('work-orders.show', $wo->id)
            ->with('success', "Work Order {$wo->no_wo} telah disetujui sebagai {$treatmentLabel}. Gudang siap mengeluarkan barang.");
    }

    /**
     * Reject Work Order oleh Gudang atau Accounting
     */
    public function reject(Request $request, $id)
    {
        $allowedRoles = ['Developer', 'Admin', 'Super Admin', 'Accounting', 'Finance', 'Finance Manager', 'Logistic', 'Warehouse'];
        if (!in_array(Auth::user()->role, $allowedRoles)) {
            abort(403, 'Akses tidak diizinkan.');
        }

        $wo = WorkOrder::findOrFail($id);

        $request->validate([
            'rejected_reason' => 'required|string|max:500',
        ]);

        $wo->rejected_reason = $request->rejected_reason;
        $wo->status = 'rejected';
        $wo->save();

        return redirect()->route('work-orders.show', $wo->id)
            ->with('success', "Work Order {$wo->no_wo} telah ditolak dengan alasan: {$request->rejected_reason}");
    }

    /**
     * Pengeluaran Barang oleh Gudang (Goods Issue & Terbitkan Surat Jalan):
     * Menerbitkan Surat Jalan (ProductOut), memotong stok gudang (BDG/BKS), dan update status Work Order.
     */
    public function issueItems(Request $request, $id)
    {
        if (!in_array(Auth::user()->role, ['Developer', 'Admin', 'Super Admin', 'Logistic', 'Warehouse'])) {
            abort(403, 'Hanya Gudang / Logistic yang berwenang mengeluarkan barang.');
        }

        $wo = WorkOrder::with(['fixedAsset.unit', 'creator', 'technician', 'items.detailProduct.product'])->findOrFail($id);

        if ($wo->status !== 'approved') {
            return redirect()->back()->with('error', 'Hanya Work Order yang sudah disetujui Accounting yang dapat dikeluarkan barangnya.');
        }

        $request->validate([
            'date' => 'nullable|date',
            'recipient' => 'nullable|string|max:255',
            'note' => 'nullable|string|max:500',
        ]);

        return DB::transaction(function () use ($request, $wo) {
            $machineCode = $wo->fixedAsset->code ?? ($wo->fixedAsset->desc ?? 'Mesin');
            $recipientName = $request->filled('recipient') 
                ? $request->recipient 
                : ($wo->technician->name ?? ($wo->creator->name ?? 'Teknisi Workshop'));

            // 1. Generate No Surat Jalan (Product Out)
            $now = $request->filled('date') ? \Carbon\Carbon::parse($request->date) : now();
            $year = $now->format('Y');
            $romanMonths = ['I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];
            $roman = $romanMonths[(int) $now->format('n') - 1];
            $suffix = "-P/BK-WO/{$roman}/{$year}";

            $last = ProductOut::where('no_product_out', 'like', '%' . $suffix)
                ->orderByDesc('no_product_out')
                ->value('no_product_out');

            $lastSeq = $last ? (int) substr($last, 0, 3) : 0;
            $nextSeq = str_pad($lastSeq + 1, 3, '0', STR_PAD_LEFT);
            $noProductOut = $nextSeq . $suffix;

            // 2. Buat Record Dokumen Surat Jalan (ProductOut)
            $productOut = new ProductOut();
            $productOut->no_product_out = $noProductOut;
            $productOut->id_user = Auth::id();
            $productOut->detail_client = "Penerima: {$recipientName} | Internal Pemeliharaan Aset - Mesin {$machineCode}";
            $productOut->invoice = "-";
            $productOut->po = $wo->no_wo;
            $productOut->no_type = "1";
            $productOut->note = $request->filled('note') 
                ? $request->note 
                : "Surat Jalan Work Order {$wo->no_wo} (Pergantian Part Mesin {$machineCode})";
            $productOut->vers = 'WO';
            $productOut->flag = 'Work Order';
            $productOut->total = $wo->total_cost;
            $productOut->date = $now->toDateString();
            $productOut->save();

            // 3. Potong Stok Gudang dan Buat Detail Product Out
            $totalCapitalized = 0;
            $deductedItems = [];

            foreach ($wo->items as $item) {
                $qtyToIssue = $item->qty_approved > 0 ? $item->qty_approved : $item->qty_requested;
                if (!$item->id_detail_product) {
                    continue; // Skip jika tidak dimapping ke produk fisik gudang
                }

                $dp = DetailProduct::find($item->id_detail_product);
                if ($dp) {
                    // Potong stok gudang yang dipilih (BDG / BKS)
                    if ($item->warehouse === 'BKS') {
                        $dp->warehouse_stock = max(0, (float)$dp->warehouse_stock - $qtyToIssue);
                    } else {
                        $dp->stock = max(0, (float)$dp->stock - $qtyToIssue);
                    }
                    $dp->save();

                    $product = Product::find($dp->id_product);
                    if ($product) {
                        if ($item->warehouse === 'BKS') {
                            $product->warehouse_stock = max(0, (float)$product->warehouse_stock - $qtyToIssue);
                        } else {
                            $product->stock = max(0, (float)$product->stock - $qtyToIssue);
                        }
                        $product->save();
                    }

                    // Cari serial_product yang tepat (agar relasi serialProduct memuat Brand & Part Number yang presisi)
                    $serialProduct = null;
                    if ($item->id_equivalent) {
                        $serialProduct = SerialProduct::find($item->id_equivalent);
                    }
                    if (!$serialProduct && $dp->id_product) {
                        $serialProduct = SerialProduct::where('id_product', $dp->id_product)
                            ->where('pn', $dp->replacement)
                            ->first() 
                            ?? SerialProduct::where('id_product', $dp->id_product)->first();
                    }

                    // Record DetailProductOut (Rincian Surat Jalan)
                    DetailProductOut::create([
                        'id_product_out' => $productOut->id,
                        'id_detail_product' => $dp->id,
                        'id_serial_product' => $serialProduct ? $serialProduct->id : null,
                        'warehouse' => $item->warehouse ?: 'BDG',
                        'qty' => $qtyToIssue,
                        'price' => $item->unit_price,
                        'amount' => $qtyToIssue * $item->unit_price,
                    ]);

                    $deductedItems[] = "{$qtyToIssue}x {$dp->replacement} ({$item->warehouse})";
                }

                // Update item WO
                $item->qty_issued = $qtyToIssue;
                $item->save();

                // 4. Catat riwayat pemakaian spare part ke FixedAssetService
                if ($wo->id_fixed_asset) {
                    $service = new FixedAssetService();
                    $service->id_fixed_asset = $wo->id_fixed_asset;
                    $service->id_detail_product = $dp ? $dp->id : null;
                    $service->warehouse = $item->warehouse ?: 'BDG';
                    $service->qty = $qtyToIssue;
                    $service->price = $item->unit_price;
                    $service->amount = $qtyToIssue * $item->unit_price;
                    $treatmentLabel = $wo->accounting_treatment === 'capitalize' ? 'Kapitalisasi' : 'Beban';
                    $service->note = "Work Order {$wo->no_wo} ({$treatmentLabel}): " . ($item->note ?: ($item->item_name ?: 'Pergantian part internal'));
                    $service->date = $now->toDateString();
                    $service->created_by = Auth::id();
                    $service->save();

                    if ($wo->accounting_treatment === 'capitalize') {
                        $totalCapitalized += ($qtyToIssue * $item->unit_price);
                    }
                }
            }

            // Tambahkan nilai kapitalisasi ke total aset mesin jika capitalize
            if ($wo->accounting_treatment === 'capitalize' && $wo->id_fixed_asset && $totalCapitalized > 0) {
                $fa = FixedAsset::find($wo->id_fixed_asset);
                if ($fa) {
                    $fa->total += $totalCapitalized;
                    $fa->save();
                }
            }

            // 5. Update Status Work Order
            $wo->id_product_out = $productOut->id;
            $wo->id_user_warehouse = Auth::id();
            $wo->status = 'issued';
            $wo->save();

            return redirect()->route('work-orders.show', $wo->id)
                ->with('success', "Surat Jalan {$noProductOut} berhasil diterbitkan! Stok suku cadang pada gudang yang dipilih telah otomatis dipotong dan Work Order selesai.");
        });
    }

    /**
     * Cetak Surat Jalan Work Order (A4 Document Format)
     */
    public function printSuratJalan($id)
    {
        $wo = WorkOrder::with([
            'fixedAsset.unit',
            'creator',
            'technician',
            'warehouseUser',
            'accountingUser',
            'productOut.detail.detailProduct.product.serial',
            'items.detailProduct.product.serial'
        ])->findOrFail($id);

        if (!$wo->id_product_out && $wo->status !== 'issued') {
            return redirect()->route('work-orders.show', $wo->id)
                ->with('error', 'Surat Jalan belum diterbitkan untuk Work Order ini.');
        }

        return view('pages.workshop.work-orders.sj-print', compact('wo'));
    }

    /**
     * API: Cari mesin Fixed Asset (untuk Select2 AJAX search dengan template kaya Smart Quote)
     */
    public function searchMachines(Request $request)
    {
        $term = $request->get('q', '');
        $machines = FixedAsset::where('fixed_asset.type', 'Mesin')
            ->leftJoin('unit', 'unit.id', '=', 'fixed_asset.id_unit')
            ->where(function ($q) use ($term) {
                $q->where('fixed_asset.code', 'like', "%{$term}%")
                    ->orWhere('fixed_asset.desc', 'like', "%{$term}%")
                    ->orWhere('fixed_asset.serial_number', 'like', "%{$term}%")
                    ->orWhere('unit.brand', 'like', "%{$term}%")
                    ->orWhere('unit.model', 'like', "%{$term}%");
            })
            ->select(
                'fixed_asset.id',
                'fixed_asset.code',
                'fixed_asset.desc',
                'fixed_asset.serial_number',
                'fixed_asset.kondisi',
                'fixed_asset.status_unit',
                'fixed_asset.lokasi',
                'unit.brand as unit_brand',
                'unit.model as unit_model',
                'unit.type_unit'
            )
            ->limit(30)
            ->get();

        $results = $machines->map(function ($m) {
            $brand = $m->unit_brand ?: ($m->desc ?: 'Mesin');
            $model = $m->unit_model ?: '-';
            $sn = $m->serial_number ?: '-';
            $kondisi = $m->kondisi ?: ($m->status_unit ?? 'OK');

            return [
                'id' => $m->id,
                'code' => $m->code,
                'brand' => $brand,
                'model' => $model,
                'sn' => $sn,
                'desc' => $m->desc,
                'kondisi' => $kondisi,
                'lokasi' => $m->lokasi ?: 'Gudang Workshop',
                'text' => "{$m->code} — {$brand} {$model} (SN: {$sn})",
            ];
        });

        return response()->json($results);
    }

    /**
     * API: Cari spare part untuk dropdown repeater form & mapping gudang
     * Load dari Product, Serial/Equivalent Replacement, Keterangan G/R, dan Stok BDG & BKS
     */
    public function searchParts(Request $request)
    {
        $term = trim($request->get('q', ''));
        if ($term === '') {
            return response()->json([]);
        }
        
        $query = DetailProduct::join('product', 'detail_product.id_product', '=', 'product.id')
            ->with(['product.serial']);

        if (!empty($term)) {
            $query->where(function ($q) use ($term) {
                $q->where('product.detail_desc', 'like', "%{$term}%")
                    ->orWhere('product.description', 'like', "%{$term}%")
                    ->orWhere('product.commodity', 'like', "%{$term}%")
                    ->orWhere('product.go', 'like', "%{$term}%")
                    ->orWhere('detail_product.replacement', 'like', "%{$term}%")
                    ->orWhereHas('product.serial', function ($sq) use ($term) {
                        $sq->where('pn', 'like', "%{$term}%")
                           ->orWhere('brand', 'like', "%{$term}%");
                    });
            });
        }

        $detailProducts = $query->select('detail_product.*')->limit(50)->get();

        $results = $detailProducts->map(function ($dp) {
            $product = $dp->product;
            $serials = $product?->serial ?? collect();
            $primarySerial = $serials->first();

            $goRaw = $product?->go ?: 'Genuine';
            $goCode = strtoupper(substr(trim($goRaw), 0, 1)); // 'G' atau 'R'
            $goBadge = $goCode === 'R' ? 'R' : 'G';
            $goText = $goCode === 'R' ? 'Replacement' : 'Genuine';

            $brand = $primarySerial?->brand ?: '';
            $pn = $primarySerial?->pn ?: ($dp->replacement ?: '-');
            $desc = $product?->detail_desc ?: ($product?->description ?: ($product?->commodity ?: 'Spare Part'));
            $commodity = $product?->commodity ?: '';
            $unit = $product?->unit ?: 'Pcs';

            // Ambil daftar replacement PN
            $replacementPns = $serials->pluck('pn')->filter(fn($p) => !empty($p) && $p !== '-')->unique()->values();
            $replacementText = $replacementPns->take(4)->implode(', ');

            $stockBdg = (float) ($dp->stock ?? 0);
            $stockBks = (float) ($dp->warehouse_stock ?? 0);
            
            // HPP / Modal Rata-rata dari DetailProduct (bukan harga jual katalog)
            $avgHpp = (float) ($dp->hpp > 0 ? $dp->hpp : ($dp->modal > 0 ? $dp->modal : 0));
            if ($avgHpp <= 0 && method_exists($dp, 'detailProductIn')) {
                $lastIn = $dp->detailProductIn()
                    ->where(function ($q) {
                        $q->where('modal', '>', 0)->orWhere('hpp', '>', 0);
                    })
                    ->latest('id')
                    ->first();
                if ($lastIn) {
                    $avgHpp = (float) ($lastIn->modal ?: $lastIn->hpp);
                }
            }
            $price = $avgHpp;

            // Suggested warehouse berdasarkan stok ketersediaan
            $suggestedWarehouse = 'BDG';
            if ($stockBdg <= 0 && $stockBks > 0) {
                $suggestedWarehouse = 'BKS';
            } elseif ($stockBdg > 0) {
                $suggestedWarehouse = 'BDG';
            }

            $displayText = ($brand ? "{$brand} " : "") . "{$pn} | {$goText}";

            return [
                'id' => $dp->id,
                'id_product' => $dp->id_product,
                'id_equivalent' => $primarySerial?->id,
                'go' => $goRaw,
                'go_code' => $goBadge,
                'go_text' => $goText,
                'commodity' => $commodity,
                'brand' => $brand,
                'pn' => $pn,
                'desc' => $desc,
                'unit' => $unit,
                'replacement_code' => $dp->replacement ?: '',
                'replacements' => $replacementPns->toArray(),
                'replacement_text' => $replacementText,
                'stock_bdg' => $stockBdg,
                'stock_bks' => $stockBks,
                'suggested_warehouse' => $suggestedWarehouse,
                'price' => $price,
                'text' => $displayText,
            ];
        });

        return response()->json($results);
    }
}
