<?php

namespace App\Http\Controllers;

use App\Models\ChangeStatus;
use App\Models\Comment;
use App\Models\DetailProduct;
use App\Models\DetailProductIn;
use App\Models\DetailPurchaseOrder;
use App\Models\DetailQuotation;
use App\Models\Invoice;
use App\Models\PendingPO;
use App\Models\PrDiscussion;
use App\Models\PrDiscussionMention;
use App\Models\Product;
use App\Models\ProductIn;
use App\Models\Prospect;
use App\Models\PurchaseOrder;
use App\Models\PurchaseRequest;
use App\Models\PurchaseRequestDetail;
use App\Models\PurchaseRequestDetailAllocation;
use App\Models\Quotation;
use App\Models\SerialProduct;
use App\Models\SubtitleQuotation;
use App\Models\Supplier;
use App\Models\UnitQuotation;
use App\Models\User;
use App\Services\PurchaseRequestService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PurchaseController extends Controller
{
    protected PurchaseRequestService $prService;

    public function __construct(PurchaseRequestService $prService)
    {
        $this->prService = $prService;
    }

    public function index()
    {
        // Badge hanya menghitung PR yang pending_po-nya masih ada, biar konsisten
        // dengan isi tabel (PR dengan id_pending yatim tidak pernah bisa ditampilkan).
        $validPendingIds = PendingPO::pluck('id');

        $newCount = PurchaseRequest::where('status', '0')->whereIn('id_pending', $validPendingIds)->count();
        $accCount = PurchaseRequest::where('status', '1')->whereIn('id_pending', $validPendingIds)->count();
        $deliveryCount = PurchaseRequest::where('status', '2')->whereIn('id_pending', $validPendingIds)->count();
        $doneCount = PurchaseRequest::where('status', '3')->whereIn('id_pending', $validPendingIds)->count();
        // Sama seperti isi tab-nya: PO cuma dihitung selama PR-nya masih status Acc(1).
        $poCount = PurchaseOrder::whereNotNull('id_purchase_request')
            ->whereIn('id_purchase_request', PurchaseRequest::where('status', '1')->pluck('id'))
            ->count();

        return view('pages.warehouse.purchase.index', compact('newCount', 'accCount', 'deliveryCount', 'doneCount', 'poCount'));
    }
    public function store(Request $request, $id)
    {
        $pending = PendingPO::find($id);
        $quotation = Quotation::find($pending->id_quotation);

        $dQuote = DetailQuotation::where('id_quotation', $quotation->id)->get();

        $success = false;
        $header = null;

        foreach ($request->qty as $key => $value) {
            if ($value != 0) {
                if (!$header) {
                    $header = $this->prService->findOrCreateDraftHeader($id, Auth::id());
                }

                $header->details()->create([
                    'id_equivalent' => $dQuote[$key]->id_equivalent,
                    'qty' => $request->qty[$key],
                    'note' => $request->note[$key],
                ]);

                $success = true;
            }
        }

        if ($success) {
            return redirect('pending-po/' . $id)->with('success', 'Purchase Request telah dibuat');
        }
    }
    public function store_project(Request $request, $id)
    {
        $header = $this->prService->findOrCreateDraftHeader($id, Auth::id());

        $header->details()->create([
            'id_equivalent' => $request->id_equivalent,
            'qty' => $request->qty,
            'note' => $request->note,
        ]);

        return redirect('pending-po/' . $id)->with('success', 'Purchase Request telah dibuat');
    }
    public function show($id)
    {
        $pending = PendingPO::find($id);
        $isUnitQuotation = (bool) $pending->id_unit_quotation;

        if ($isUnitQuotation) {
            // Unit Quotation punya field/relasi setara buat semua yang dibutuhkan view ini
            // (pic.client, sales, type) — cukup di-alias di titik yang beda nama kolomnya,
            // sisanya kompatibel langsung tanpa perlu view terpisah.
            $quotation = UnitQuotation::with(['sales', 'pic.client'])->findOrFail($pending->id_unit_quotation);
            $quotation->po_date = $quotation->po_received;
            $detQuotation = $quotation->details; // UnitQuotationDetail: sudah punya id_equivalent + price
            $subQuote = collect();
            $invoice = Invoice::where('id_unit_quotation', $quotation->id)->first();
        } else {
            $quotation = Quotation::find($pending->id_quotation);
            $detQuotation = DetailQuotation::where('id_quotation', $pending->id_quotation)->get();
            $subQuote = SubtitleQuotation::with('detail')->where('id_quotation', $pending->id_quotation)->get();
            $invoice = Invoice::where('id_quotation', $quotation->id)->first();
        }

        $activity = ChangeStatus::where('id_pending', $id)->with('comment')->get();
        $purchase = PurchaseRequest::where('id_pending', $id)->with('details.equivalent.product', 'details.allocations.purchaseOrder', 'purchaseOrders.detail')->first();

        // Data diskusi PR
        $discussions = PrDiscussion::where('id_pending', $id)
            ->with(['user', 'mentions.user'])
            ->orderBy('created_at', 'asc')
            ->get();

        // Semua user aktif untuk dropdown mention
        $allUsers = User::where('active', '1')->orderBy('name')->get(['id', 'name', 'role', 'image']);

        // Variabel notifikasi navbar (pola standar)
        $firstComments = Comment::where('id_user', Auth::id())
            ->groupBy('id_status')
            ->get();
        $statusIds = $firstComments->pluck('id_status')->toArray();
        $dates = $firstComments->pluck('created_at', 'id_status');

        $commentsQuery = Comment::join('change_status as c', 'c.id', '=', 'comment.id_status')
            ->join('quotation as q', 'q.id', '=', 'c.id_quotation')
            ->join('users as u', 'u.id', '=', 'comment.id_user')
            ->whereIn('comment.id_status', count($statusIds) ? $statusIds : [0])
            ->where(function ($query) use ($dates) {
                foreach ($dates as $statusId => $createdAt) {
                    $query->orWhere(function ($subQuery) use ($statusId, $createdAt) {
                        $subQuery->where('comment.id_status', $statusId)
                            ->whereRaw('TIMESTAMPDIFF(SECOND, ?, comment.created_at) > 0', [$createdAt]);
                    });
                }
            })
            ->where('comment.id_user', '!=', Auth::id());

        $commentAdmin = $commentsQuery->orderBy('comment.id_status')
            ->orderByDesc('comment.created_at')
            ->get(['q.id as idQ', 'comment.id as idC', 'comment.id_user', 'comment.level', 'comment.comment', 'comment.date', 'q.no_quote', 'u.name', 'u.image']);

        $unreadCommentAdmin = $commentsQuery->where('comment.level', '1')
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

        $comment = $quotationComment->union($prospectComment)->orderBy('date', 'DESC')->take(5)->get();
        $unreadComment = $quotationComment->union($prospectComment)->orderBy('date', 'DESC')->where('o.level', '1')->take(5)->get();

        $noSaleProspect = Prospect::whereNull('id_sales')->whereNull('provide')->count();
        $leveledProspect = Prospect::whereNull('level')->where('id_sales', Auth::id())->count();

        return view('pages.warehouse.purchase.detail', compact(
            'purchase', 'activity', 'subQuote', 'pending', 'quotation', 'invoice', 'detQuotation', 'isUnitQuotation',
            'discussions', 'allUsers',
            'comment', 'unreadComment', 'commentAdmin', 'unreadCommentAdmin', 'noSaleProspect', 'leveledProspect'
        ));
    }

    public function addDiscussion(Request $request, $id)
    {
        $request->validate(['message' => 'required|string|max:2000']);

        $discussion = new PrDiscussion();
        $discussion->id_pending = $id;
        $discussion->id_user = Auth::id();
        $discussion->message = $request->message;
        $discussion->save();

        if ($request->mentions) {
            foreach ($request->mentions as $userId) {
                $mention = new PrDiscussionMention();
                $mention->id_discussion = $discussion->id;
                $mention->id_user_mention = $userId;
                $mention->level = '0';
                $mention->save();
            }
        }

        return redirect()->route('purchase-request.show', $id)->with('success', 'Pesan berhasil dikirim')->withFragment('diskusi');
    }

    public function readPrMention($id)
    {
        $mention = PrDiscussionMention::find($id);
        if ($mention && $mention->id_user_mention == Auth::id()) {
            $mention->level = '1';
            $mention->save();
            return response()->json(['message' => 'ok']);
        }
        return response()->json(['message' => 'not found'], 404);
    }
    public function delete($id)
    {
        $detail = PurchaseRequestDetail::find($id);
        if (!$detail) {
            return 0;
        }
        return $detail->delete() ? 1 : 0;
    }
    public function acc($id)
    {
        $purchase = PurchaseRequest::find($id);
        if (!$purchase) {
            return 0;
        }
        $purchase->status = '1';
        return $purchase->save() ? 1 : 0;
    }
    public function reject(Request $request, $id)
    {
        $rule = ['reason' => 'required|string|max:1000'];
        $this->validate($request, $rule);

        $purchase = PurchaseRequest::find($id);
        if (!$purchase) {
            return response()->json(['error' => 'Purchase Request tidak ditemukan.'], 404);
        }
        if ($purchase->status != '0') {
            return response()->json(['error' => 'Purchase Request ini sudah diproses, tidak bisa ditolak lagi.'], 422);
        }

        $purchase->status = '4';
        $purchase->rejected_at = now();
        $purchase->rejected_reason = $request->reason;
        $purchase->rejected_by = Auth::id();
        $purchase->save();

        return response()->json(1);
    }
    public function delivery(Request $request, $id)
    {
        $rule = [
            'purchase_type' => 'required|in:Lokal,Impor',
            'cargo' => 'required|string|max:255',
            'no_resi' => 'nullable|string|max:255',
            'purchase_date' => 'required|date',
        ];
        $this->validate($request, $rule);

        $purchase = PurchaseRequest::find($id);
        if (!$purchase) {
            return 0;
        }
        if (!$purchase->purchaseOrders()->exists()) {
            return response()->json(['message' => 'Buat Purchase Order terlebih dahulu sebelum lanjut ke On Delivery.'], 422);
        }
        $purchase->load('details.allocations');
        if (!$this->prService->isFullyAllocated($purchase)) {
            return response()->json(['message' => 'Masih ada item yang belum dialokasikan sepenuhnya ke Purchase Order.'], 422);
        }

        $purchase->status = '2';
        $purchaseSave = $purchase->save();

        // Isi info pengiriman ke semua item (default sama untuk semua),
        // item tertentu bisa di-override belakangan lewat updateDeliveryInfo().
        $purchase->details()->update([
            'purchase_type' => $request->purchase_type,
            'cargo' => $request->cargo,
            'no_resi' => $request->no_resi,
            'purchase_date' => $request->purchase_date,
        ]);

        return $purchaseSave ? 1 : 0;
    }
    public function updateDeliveryInfo(Request $request, $id)
    {
        $rule = [
            'purchase_type' => 'required|in:Lokal,Impor',
            'cargo' => 'required|string|max:255',
            'no_resi' => 'nullable|string|max:255',
            'purchase_date' => 'required|date',
        ];
        $this->validate($request, $rule);

        // $id = id baris alokasi (purchase_request_detail_allocation), bukan id item PR —
        // satu item PR bisa split qty ke beberapa PO, jadi info pengiriman melekat per alokasi.
        $allocation = \App\Models\PurchaseRequestDetailAllocation::find($id);
        if (!$allocation) {
            return 0;
        }

        $allocation->purchase_type = $request->purchase_type;
        $allocation->cargo = $request->cargo;
        $allocation->no_resi = $request->no_resi;
        $allocation->purchase_date = $request->purchase_date;

        return $allocation->save() ? 1 : 0;
    }

    public function done_all($id)
    {
        $pending = PendingPO::find($id);
        $header = PurchaseRequest::where('id_pending', $id)->first();
        $purchases = $header ? $header->details()->orderBy('id')->get() : collect();

        $fullRep = [];
        foreach ($purchases as $key => $purchase) {
            $equivalent = SerialProduct::where('id', $purchase->id_equivalent)->first();

            if ($equivalent) {
                $fullRep[$key] = DetailProduct::where('id_product', $equivalent->id_product)->get();
            }
        }
        $suppliers = Supplier::all();
        $detProduct = DetailProduct::join('product', 'detail_product.id_product', '=', 'product.id')->get('detail_product.*');
        return view('pages.warehouse.purchase.form', compact('detProduct', 'suppliers', 'fullRep', 'purchases', 'pending'));
    }
    public function store_done_all(Request $request, $id)
    {

        $rule = [
            'invoice' => 'required',
            'date' => 'required',
            'note' => 'required',
            'replacement' => 'required|array',
            'replacement.*' => 'required|integer|exists:detail_product,id',
        ];
        $message = [
            'invoice.required' => 'Field No Invoice Wajib Diisi',
            'date.required' => 'Field Date Wajib Diisi',
            'note.required' => 'Field Note Wajib Diisi',
            'replacement.required' => 'Commodity || Replacement Wajib Dipilih',
            'replacement.*.required' => 'Commodity || Replacement Wajib Dipilih',
            'replacement.*.exists' => 'Commodity || Replacement tidak valid',
        ];
        $this->validate($request, $rule, $message);

        $header = PurchaseRequest::where('id_pending', $id)->first();
        $purchases = $header ? $header->details()->orderBy('id')->get() : collect();

        foreach ($purchases as $key => $purchase) {
            if (isset($request->price[$key])) {
                $purchase->price = $request->price[$key];
            }
            if (isset($request->amount[$key])) {
                $purchase->amount = $request->amount[$key];
            }
            $purchase->save();
        }

        if ($header) {
            $header->status = '3';
            $header->save();
        }

        $supplier = Supplier::find($request->supplier);
        // Masukan Data ke Tabel Quotataion
        $productIn = new ProductIn();
        $productIn->no_product_in = $this->generateNoProductIn($request->warehouse[0] ?? 'BDG');
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
                $dProductIn = new DetailProductIn();
                $dProductIn->id_product_in = $productIn->id;
                $dProductIn->id_detail_product = $request->replacement[$item];
                $dProductIn->qty = $request->qty[$item];
                $dProductIn->modal = $request->price[$item];
                $dProductIn->disc = !empty($request->disc[$item]) ? $request->disc[$item] : 0;
                $dProductIn->amount = $request->amount[$item];
                $dProductIn->warehouse = $request->warehouse[$item];
                $productD = DetailProduct::find($request->replacement[$item]);
                if (!$productD) {
                    continue;
                }
                $productD->modal = ((($productD->stock + $productD->warehouse_stock) * $productD->modal) + ($request->qty[$item] * $request->price[$item])) / (($productD->stock + $productD->warehouse_stock) + $request->qty[$item]);
                if ($request->warehouse[$item] == 'BDG') {
                    $productD->stock = $productD->stock + $request->qty[$item];
                } else {
                    $productD->warehouse_stock = $productD->warehouse_stock + $request->qty[$item];
                }
                $productD->save();
                $product = Product::find($productD->id_product);
                if ($product) {
                    if ($request->warehouse[$item] == 'BDG') {
                        $product->stock = $product->stock + $request->qty[$item];
                    } else {
                        $product->warehouse_stock = $product->warehouse_stock + $request->qty[$item];
                    }
                    $product->save();
                }
                $dProductSave = $dProductIn->save();
            }
        }
        if ($dProductSave) {
            return redirect('/product-in')->with('message', 'data telah di tambahkan');
        }
    }
    public function store_done_all_logistic(Request $request, $id)
    {

        $rule = [
            'no_do' => 'required',
            'date' => 'required',
            'replacement' => 'required|array',
            'replacement.*' => 'required|integer|exists:detail_product,id',
        ];
        $message = [
            'no_do.required' => 'Field No DO Wajib Diisi',
            'date.required' => 'Field Date Wajib Diisi',
            'replacement.required' => 'Commodity || Replacement Wajib Dipilih',
            'replacement.*.required' => 'Commodity || Replacement Wajib Dipilih',
            'replacement.*.exists' => 'Commodity || Replacement tidak valid',
        ];
        $this->validate($request, $rule, $message);
        // dd($request->all());
        $supplier = Supplier::find($request->supplier);
        // Masukan Data ke Tabel Quotataion
        $productIn = new ProductIn();
        $productIn->no_product_in = $this->generateNoProductIn($request->warehouse[0] ?? 'BDG');
        $productIn->no_do = $request->no_do;
        $productIn->invoice = null;
        // $productIn->id_supplier = null;
        $productIn->id_supplier = $request->supplier;
        $productIn->supplier = null;
        $productIn->info = $supplier->info;
        // $productIn->info = $request->info;
        $productIn->date = $request->date;
        $productIn->date_invoice = null;
        $productIn->subtotal = null;
        $productIn->total_no_tax = null;
        $productIn->tax = null;
        $productIn->note = null;
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
                $productD = DetailProduct::find($request->replacement[$item]);
                if (!$productD) {
                    continue;
                }
                if ($request->warehouse[$item] == 'BDG') {
                    $productD->stock = $productD->stock + $request->qty[$item];
                } else {
                    $productD->warehouse_stock = $productD->warehouse_stock + $request->qty[$item];
                }
                $productD->save();
                $product = Product::find($productD->id_product);
                if ($product) {
                    if ($request->warehouse[$item] == 'BDG') {
                        $product->stock = $product->stock + $request->qty[$item];
                    } else {
                        $product->warehouse_stock = $product->warehouse_stock + $request->qty[$item];
                    }
                    $product->save();
                }
                $dProductSave = $dProductIn->save();
            }
        }
        if ($dProductSave) {
            return redirect('/product-in')->with('message', 'data telah di tambahkan');
        }
    }

    // $id = id Purchase Order. Satu PO = satu pengiriman, jadi verifikasi
    // penerimaannya juga per PO lewat alokasi (purchase_request_detail_allocation),
    // bukan per PR (satu PR bisa pecah ke beberapa PO yang datang terpisah).
    public function goodsReceiptForm($id)
    {
        $po = PurchaseOrder::with('purchaseRequest.pending')->findOrFail($id);
        $pending = $po->purchaseRequest->pending;
        $allocations = PurchaseRequestDetailAllocation::where('id_purchase_order', $id)
            ->with('detail.equivalent.product')
            ->orderBy('id')
            ->get();

        $fullRep = [];
        foreach ($allocations as $key => $allocation) {
            $equivalent = $allocation->detail->equivalent ?? null;
            if ($equivalent) {
                $fullRep[$key] = DetailProduct::where('id_product', $equivalent->id_product)->get();
            } else {
                $fullRep[$key] = collect([]);
            }

            // allocation->qty di-clamp ke kebutuhan PR — kalau Logistic sengaja beli
            // lebih banyak buat nambah stok, qty asli yang beneran dikirim/diterima
            // dari supplier ada di DetailPurchaseOrder (id_product), bukan di alokasi
            // PR ini. Dipakai sebagai qty order/default qty diterima di form GR biar
            // kelebihannya nggak hilang pas checklist penerimaan.
            $poQty = $equivalent
                ? (DetailPurchaseOrder::where('id_purchase_order', $id)->where('id_product', $equivalent->id_product)->value('qty') ?? $allocation->qty)
                : $allocation->qty;
            $allocation->po_qty = max($poQty, $allocation->qty);
        }

        $suppliers = Supplier::all();

        // Preview nomor GR yang bakal dipakai kalau verifikasi ini disimpan — nomor
        // aslinya baru benar-benar "dikunci" pas submit (lihat storeGoodsReceipt),
        // ini cuma gambaran biar user tahu nomornya dari awal.
        $previewNoGr = $po->no_gr ?: $this->prService->generateNoGr();

        return view('pages.warehouse.purchase.goods_receipt', compact('pending', 'po', 'allocations', 'fullRep', 'suppliers', 'previewNoGr'));
    }

    public function storeGoodsReceipt(Request $request, $id)
    {
        $rule = [
            'no_do' => 'required|string|max:255',
            'gr_date' => 'required|date',
            'supplier' => 'required|integer|exists:supplier,id',
            'alloc_id' => 'required|array',
            'alloc_id.*' => 'required|integer|exists:purchase_request_detail_allocation,id',
            'gr_status' => 'required|array',
            'gr_status.*' => 'required|in:Sesuai,Tidak Sesuai,Rusak',
            'replacement' => 'required|array',
            'replacement.*' => 'required|integer|exists:detail_product,id',
            'qty_received' => 'required|array',
            'qty_received.*' => 'required|integer|min:0',
            'qty_damaged' => 'nullable|array',
            'qty_damaged.*' => 'nullable|integer|min:0',
            'warehouse' => 'required|array',
            'warehouse.*' => 'required|in:BDG,BKS',
            'gr_note' => 'nullable|array',
        ];

        $message = [
            'no_do.required' => 'Nomor Delivery Order (DO) Wajib Diisi',
            'gr_date.required' => 'Tanggal Penerimaan Wajib Diisi',
            'supplier.required' => 'Supplier Wajib Dipilih',
            'replacement.*.required' => 'Commodity || Replacement Wajib Dipilih',
        ];

        $this->validate($request, $rule, $message);

        $po = PurchaseOrder::findOrFail($id);
        $supplier = Supplier::findOrFail($request->supplier);

        // 1. Create the ProductIn (Barang Masuk) record, satu per PO
        $productIn = new ProductIn();
        $productIn->no_product_in = $this->generateNoProductIn($request->warehouse[0] ?? 'BDG');
        $productIn->no_do = $request->no_do;
        // Ikut invoice supplier yang sudah diupload di PO (kalau ada) — supaya GR ini
        // langsung kebaca di tabel "Product In — Lokal/Import" tanpa nunggu upload
        // invoice terpisah. Kalau belum ada, tetap null seperti sebelumnya (nanti ikut
        // ke-sync begitu invoice-nya diupload lewat POController::uploadInvoice()).
        $productIn->invoice = $po->no_invoice_supplier;
        $productIn->id_supplier = $request->supplier;
        $productIn->id_purchase_order = $po->id;
        $productIn->info = $supplier->info;
        $productIn->date = $request->gr_date;
        $productIn->date_invoice = null;
        $productIn->subtotal = null;
        $productIn->total_no_tax = null;
        $productIn->tax = null;
        $productIn->note = 'Otomatis dibuat via Goods Receipt PO ' . $po->no_po;
        $productIn->shipping = null;
        $productIn->total = null;
        $productIn->created_by = Auth::id(); // tracking who received it
        $productIn->save();

        $dProductSave = false;
        $damagedLines = [];

        // 2. Loop through each allocation (item PR x PO ini) untuk simpan Goods Receipt
        foreach ($request->alloc_id as $key => $allocId) {
            $allocation = PurchaseRequestDetailAllocation::find($allocId);
            if ($allocation) {
                $status = $request->gr_status[$key];
                // Qty Diterima yang dikirim dari form sudah otomatis dihitung di sisi
                // client sebagai (qty order - qty rusak) untuk status Rusak, jadi di sini
                // dia SUDAH mewakili qty yang kondisinya baik / siap masuk stok — jangan
                // dikurangi qty_damaged lagi (dobel kurang).
                // Qty rusak dibatasi ke qty PO asli (bisa lebih besar dari allocation->qty
                // kalau PO-nya sengaja dilebihin buat nambah stok), bukan ke qty PR-nya —
                // biar barang rusak dari porsi tambahan stok itu juga kecatat benar.
                $equivalent = $allocation->detail->equivalent ?? null;
                $poQtyCap = $equivalent
                    ? (DetailPurchaseOrder::where('id_purchase_order', $po->id)->where('id_product', $equivalent->id_product)->value('qty') ?? $allocation->qty)
                    : $allocation->qty;
                $poQtyCap = max($poQtyCap, $allocation->qty);

                $qtyRec = $request->qty_received[$key];
                $qtyDamaged = min((int) ($request->qty_damaged[$key] ?? 0), $poQtyCap);
                $qtyGood = $qtyRec;
                $note = $request->gr_note[$key] ?? null;
                $replId = $request->replacement[$key];
                $wh = $request->warehouse[$key];

                // Update baris alokasi
                $allocation->no_do = $request->no_do;
                $allocation->gr_date = $request->gr_date;
                $allocation->gr_status = $status;
                $allocation->qty_received = $qtyRec;
                $allocation->gr_note = $note;
                $allocation->warehouse = $wh;
                $allocation->save();

                if ($qtyGood > 0) {
                    // Save Detail Product In (cuma qty yang kondisinya baik)
                    $dProductIn = new DetailProductIn();
                    $dProductIn->id_product_in = $productIn->id;
                    $dProductIn->id_detail_product = $replId;
                    $dProductIn->qty = $qtyGood;
                    $dProductIn->modal = null;
                    $dProductIn->amount = null;
                    $dProductIn->warehouse = $wh;
                    $dProductIn->save();

                    // Update physical inventory stock
                    $productD = DetailProduct::find($replId);
                    if ($productD) {
                        if ($wh == 'BDG') {
                            $productD->stock += $qtyGood;
                        } else {
                            $productD->warehouse_stock += $qtyGood;
                        }
                        $productD->save();

                        $product = Product::find($productD->id_product);
                        if ($product) {
                            if ($wh == 'BDG') {
                                $product->stock += $qtyGood;
                            } else {
                                $product->warehouse_stock += $qtyGood;
                            }
                            $product->save();
                        }
                    }
                }

                if ($qtyDamaged > 0) {
                    $damagedLines[] = [
                        'id_replacement' => $replId,
                        'qty' => $qtyDamaged,
                        'note' => $note ?: 'Rusak saat diterima (GR PO ' . $po->no_po . ')',
                    ];
                }

                $dProductSave = true;
            }
        }

        // 3. Kalau ada item yang rusak, catat sebagai Retur ke supplier — terpisah
        // dari stok, biar barang cacat nggak ikut kehitung available stock.
        if (!empty($damagedLines)) {
            $retur = new \App\Models\Retur();
            $retur->id_product_in = $productIn->id;
            $retur->no_return = $this->generateNoReturn();
            $retur->status = 0;
            $retur->date = $request->gr_date;
            $retur->save();

            foreach ($damagedLines as $line) {
                $detailReturn = new \App\Models\DetailReturn();
                $detailReturn->id_retur = $retur->id;
                $detailReturn->id_replacement = $line['id_replacement'];
                $detailReturn->qty = $line['qty'];
                $detailReturn->note = $line['note'];
                $detailReturn->date = $request->gr_date;
                $detailReturn->status = 0;
                $detailReturn->save();
            }
        }

        if ($dProductSave) {
            // No. GR baru dibuat di sini, pas barang benar-benar diverifikasi diterima —
            // bukan lagi di langkah "Send to GR" terpisah (dihapus, alur sekarang langsung
            // dari Incoming Goods begitu PO on delivery ke form Goods Receipt ini).
            if (!$po->no_gr) {
                $po->no_gr = $this->prService->generateNoGr();
                $po->gr_sent_at = now();
            }
            $po->receipt_status = 'Received';
            $po->save();

            // PR baru "done" kalau SEMUA PO-nya sudah diterima (PR bisa pecah ke beberapa PO).
            $header = PurchaseRequest::find($po->id_purchase_request);
            if ($header) {
                $allPosReceived = $header->purchaseOrders()->where('receipt_status', '!=', 'Received')->doesntExist();
                if ($allPosReceived) {
                    $header->status = '3';
                    $header->save();
                }
            }

            return redirect()->route('purchase.show', $po->id)->with('success', 'Verifikasi Goods Receipt berhasil disimpan.');
        }

        return redirect()->back()->with('error', 'Gagal memproses Goods Receipt.');
    }

    public function update(Request $request, $id)
    {
        $rule = [
            'qty' => 'required|integer|min:1',
            'qty_stock' => 'nullable|integer|min:0',
            'note' => 'nullable|string|max:1000',
        ];
        $this->validate($request, $rule);

        $detail = PurchaseRequestDetail::with('header')->findOrFail($id);

        // qty_stock = qty tambahan buat buffer stok gudang (di luar kebutuhan SO),
        // sengaja dibatasi role Logistic/Admin dan cuma sebelum PR di-ACC — biar nggak
        // bikin alokasi/PO yang sudah jalan jadi nggak sinkron.
        if ($request->filled('qty_stock') && (int) $request->qty_stock !== (int) ($detail->qty_stock ?? 0)) {
            if (!in_array(Auth::user()->role, ['Logistic', 'Admin'])) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'qty_stock' => 'Hanya role Logistic/Admin yang boleh mengisi qty tambahan stok.',
                ]);
            }
            if (!$detail->header || $detail->header->status != '0') {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'qty_stock' => 'Qty tambahan stok hanya bisa diubah sebelum Purchase Request di-ACC.',
                ]);
            }
            $detail->qty_stock = $request->qty_stock;
        }

        $detail->qty = $request->qty;
        $detail->note = $request->note;
        $detail->save();

        return redirect()->back()->with('success', 'Purchase Request berhasil diperbarui.');
    }

    // Format: 001-BM/VIII/BDG/2026 — nomor urut per gudang (BDG/BKS) tiap bulan,
    // biar keliatan langsung barangnya masuk ke gudang mana dari nomornya.
    private function generateNoProductIn(string $warehouse = 'BDG'): string
    {
        $now = now();
        $year = $now->format('Y');
        $romanMonths = ['I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];
        $roman = $romanMonths[(int) $now->format('n') - 1];
        $wh = in_array($warehouse, ['BDG', 'BKS']) ? $warehouse : 'BDG';
        $suffix = "-BM/{$roman}/{$wh}/{$year}";

        $last = ProductIn::where('no_product_in', 'like', '%' . $suffix)
            ->orderByDesc('no_product_in')
            ->value('no_product_in');

        $lastSeq = $last ? (int) substr($last, 0, 3) : 0;
        $nextSeq = str_pad($lastSeq + 1, 3, '0', STR_PAD_LEFT);

        return $nextSeq . $suffix;
    }

    private function generateNoReturn(): string
    {
        $year = now()->format('Y');
        $month = now()->format('m');
        $prefix = "RET/{$year}/{$month}/";

        $last = \App\Models\Retur::where('no_return', 'like', $prefix . '%')
            ->orderByDesc('no_return')
            ->value('no_return');

        $lastSeq = $last ? (int) substr($last, -3) : 0;
        $nextSeq = str_pad($lastSeq + 1, 3, '0', STR_PAD_LEFT);

        return $prefix . $nextSeq;
    }
}
