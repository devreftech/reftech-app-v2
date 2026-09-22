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
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    protected PurchaseRequestService $prService;

    public function __construct(PurchaseRequestService $prService)
    {
        $this->prService = $prService;
    }

    public function index()
    {
        // Badge harus konsisten dengan isi tabel: query tabel (lihat
        // $purchaseRequestListQuery di routes/web.php) nge-INNER JOIN ke
        // purchase_request_detail, jadi PR yang cuma header kosong tanpa item
        // (mis. draft yang semua detailnya sudah dihapus) tidak pernah muncul di
        // tabel.
        $badgeBase = fn () => PurchaseRequest::whereNotNull('id_pending')->has('details');

        $newCount = $badgeBase()->where('status', '0')->count();
        $accCount = $badgeBase()->where('status', '1')->count();
        $deliveryCount = $badgeBase()->where('status', '2')->count();

        // Sama seperti isi tab-nya: PO cuma dihitung selama PR-nya masih status Acc(1).
        $poCount = PurchaseOrder::whereNotNull('id_purchase_request')
            ->whereIn('id_purchase_request', PurchaseRequest::where('status', '1')->pluck('id'))
            ->count();

        // Tab Good Receipt di-root dari PO yang PR-nya sudah berstatus Done(3).
        $doneCount = PurchaseOrder::whereNotNull('id_purchase_request')
            ->whereIn('id_purchase_request', PurchaseRequest::where('status', '3')->pluck('id'))
            ->count();

        return view('pages.warehouse.purchase.index', compact('newCount', 'accCount', 'deliveryCount', 'doneCount', 'poCount'));
    }

    public function storeManual(Request $request)
    {
        if (!in_array(Auth::user()->role, ['Developer', 'Admin', 'Super Admin', 'Logistic'])) {
            abort(403, 'Akses tidak diizinkan.');
        }

        $request->validate([
            'id_pending' => 'nullable|exists:pending_po,id',
            'date' => 'required|date',
            'title' => 'nullable|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.id_equivalent' => 'required',
            'items.*.qty' => 'required|numeric|min:0.01',
            'items.*.note' => 'nullable|string|max:255',
            'status' => 'nullable|in:0,1',
        ]);

        return DB::transaction(function () use ($request) {
            if ($request->filled('id_pending')) {
                $pending = PendingPO::findOrFail($request->id_pending);
            } else {
                $year = now()->format('Y');
                $month = now()->format('m');
                $prefixSo = "SO-MANUAL/{$year}/{$month}/";
                $lastSo = PendingPO::where('no_pending', 'like', $prefixSo . '%')->orderByDesc('id')->value('no_pending');
                $lastSeq = $lastSo ? (int) substr($lastSo, -3) : 0;
                $noPending = $prefixSo . str_pad($lastSeq + 1, 3, '0', STR_PAD_LEFT);

                $pending = new PendingPO();
                $pending->no_pending = $noPending;
                $pending->title = $request->title ?: 'Pengadaan Internal / Manual';
                $pending->type = 'Manual';
                $pending->status = '1';
                $pending->date = $request->date;
                $pending->save();
            }

            $pr = new PurchaseRequest();
            $pr->no_pr = $this->prService->generateNoPr();
            $pr->id_pending = $pending->id;
            $pr->id_user = Auth::id();
            $pr->status = $request->status !== null ? $request->status : '1'; // Default: langsung status ACC (1) siap PO
            $pr->date = $request->date;
            $pr->save();

            $createdCount = 0;
            foreach ($request->items as $item) {
                $qty = (float) ($item['qty'] ?? 0);
                $idEquiv = $item['id_equivalent'] ?? null;
                if ($qty > 0 && $idEquiv) {
                    $pr->details()->create([
                        'id_equivalent' => $idEquiv,
                        'qty' => $qty,
                        'note' => $item['note'] ?? null,
                    ]);
                    $createdCount++;
                }
            }

            if ($createdCount === 0) {
                throw new \Exception('Minimal 1 item sparepart dengan Qty > 0 harus dipilih.');
            }

            $targetSoMsg = $pending->no_pending ? " untuk SO {$pending->no_pending}" : "";
            return redirect()->route('purchase-request.index')
                ->with('success', "Purchase Request {$pr->no_pr} ({$createdCount} item){$targetSoMsg} berhasil dibuat.");
        });
    }

    /**
     * Cari Sales Order (PendingPO) yang terdaftar untuk dipilih pada pembuatan PR Manual.
     */
    public function searchSalesOrder(Request $request)
    {
        $q = trim($request->get('q', ''));

        $query = PendingPO::with([
            'quote.pic.client',
            'unitQuotation.pic.client',
        ])->whereNotNull('no_pending');

        if (!empty($q)) {
            $query->where(function ($sub) use ($q) {
                $sub->where('no_pending', 'like', "%{$q}%")
                    ->orWhere('title', 'like', "%{$q}%")
                    ->orWhereHas('quote.pic.client', function ($cq) use ($q) {
                        $cq->where('company', 'like', "%{$q}%")
                           ->orWhere('name', 'like', "%{$q}%");
                    })
                    ->orWhereHas('unitQuotation.pic.client', function ($uq) use ($q) {
                        $uq->where('company', 'like', "%{$q}%")
                           ->orWhere('name', 'like', "%{$q}%");
                    });
            });
        }

        $results = $query->orderByDesc('id')->take(35)->get();

        $data = $results->map(function ($so) {
            $company = $so->quote?->pic?->client?->company 
                ?: $so->unitQuotation?->pic?->client?->company 
                ?: $so->quote?->pic?->client?->name 
                ?: $so->unitQuotation?->pic?->client?->name 
                ?: ($so->title ?: 'Customer Non-Quotation');

            $dateStr = $so->date ? Carbon::parse($so->date)->format('d/m/Y') : ($so->created_at ? $so->created_at->format('d/m/Y') : '-');
            $typeStr = $so->type ?: ($so->id_unit_quotation ? 'Unit' : 'Part');
            $text = "{$so->no_pending} — {$company} [{$typeStr}, {$dateStr}]";

            return [
                'id' => $so->id,
                'no_pending' => $so->no_pending,
                'company' => $company,
                'type' => $typeStr,
                'date' => $dateStr,
                'title' => $so->title,
                'text' => $text,
            ];
        });

        return response()->json(['data' => $data]);
    }

    public function store(Request $request, $id)
    {
        $pending = PendingPO::findOrFail($id);
        $header = null;
        $createdCount = 0;

        // 1. If explicit array of item rows passed (items[][id_equivalent], items[][qty], etc.)
        if ($request->has('items') && is_array($request->items)) {
            foreach ($request->items as $row) {
                $qty = (float) ($row['qty'] ?? 0);
                $idEquiv = $row['id_equivalent'] ?? null;
                if ($qty > 0 && $idEquiv && $idEquiv != '0') {
                    if (!$header) {
                        $header = $this->prService->findOrCreateDraftHeader($id, Auth::id());
                    }
                    $header->details()->create([
                        'id_equivalent' => $idEquiv,
                        'qty' => $qty,
                        'note' => $row['note'] ?? null,
                    ]);
                    $createdCount++;
                }
            }
        }
        // 2. If parallel arrays passed (qty[], id_equivalent[], note[])
        elseif ($request->has('qty') && is_array($request->qty)) {
            $idEquivs = $request->id_equivalent ?? [];

            // Fallback equivalent mapping if not explicitly passed in request
            if (empty($idEquivs)) {
                if ($pending->id_quotation) {
                    $dQuote = DetailQuotation::where('id_quotation', $pending->id_quotation)->get();
                    $idEquivs = $dQuote->pluck('id_equivalent')->toArray();
                } elseif ($pending->id_unit_quotation) {
                    $dPending = DetailPendingPO::where('id_pending', $id)->get();
                    $idEquivs = $dPending->pluck('id_equivalent')->toArray();
                }
            }

            foreach ($request->qty as $key => $value) {
                $qty = (float) $value;
                $idEquiv = $idEquivs[$key] ?? null;
                if ($qty > 0 && $idEquiv && $idEquiv != '0') {
                    if (!$header) {
                        $header = $this->prService->findOrCreateDraftHeader($id, Auth::id());
                    }
                    $note = is_array($request->note) ? ($request->note[$key] ?? null) : $request->note;
                    $header->details()->create([
                        'id_equivalent' => $idEquiv,
                        'qty' => $qty,
                        'note' => $note,
                    ]);
                    $createdCount++;
                }
            }
        }
        // 3. If single item passed (id_equivalent, qty, note)
        elseif ($request->has('id_equivalent') && $request->has('qty')) {
            $qty = (float) $request->qty;
            if ($qty > 0 && $request->id_equivalent && $request->id_equivalent != '0') {
                $header = $this->prService->findOrCreateDraftHeader($id, Auth::id());
                $header->details()->create([
                    'id_equivalent' => $request->id_equivalent,
                    'qty' => $qty,
                    'note' => $request->note,
                ]);
                $createdCount++;
            }
        }

        // Also check if extra manual equivalent items were submitted (array or scalar)
        if ($request->has('manual_id_equivalent')) {
            $manualEquivs = is_array($request->manual_id_equivalent) ? $request->manual_id_equivalent : [$request->manual_id_equivalent];
            $manualQtys = is_array($request->manual_qty) ? $request->manual_qty : [$request->manual_qty];
            $manualNotes = is_array($request->manual_note) ? $request->manual_note : [$request->manual_note];

            foreach ($manualEquivs as $k => $manualEquiv) {
                $manualQty = (float) ($manualQtys[$k] ?? 0);
                if ($manualQty > 0 && $manualEquiv && $manualEquiv != '0') {
                    if (!$header) {
                        $header = $this->prService->findOrCreateDraftHeader($id, Auth::id());
                    }
                    $header->details()->create([
                        'id_equivalent' => $manualEquiv,
                        'qty' => $manualQty,
                        'note' => $manualNotes[$k] ?? null,
                    ]);
                    $createdCount++;
                }
            }
        }

        if ($createdCount > 0) {
            return redirect('pending-po/' . $id)->with('success', "Purchase Request berhasil dibuat ({$createdCount} item ditambahkan).");
        }

        return redirect('pending-po/' . $id)->with('warning', 'Tidak ada item yang dipilih atau Qty bernilai 0.');
    }

    public function store_project(Request $request, $id)
    {
        return $this->store($request, $id);
    }
    public function show($id)
    {
        // 1. Prioritaskan pencarian langsung berdasarkan ID PurchaseRequest
        $purchase = PurchaseRequest::with(['details.equivalent.product', 'details.allocations.purchaseOrder', 'purchaseOrders.detail', 'rejector'])->find($id);

        if ($purchase) {
            $pending = PendingPO::find($purchase->id_pending);
        } else {
            // 2. Fallback jika parameter $id adalah id_pending (PendingPO)
            $purchase = PurchaseRequest::where('id_pending', $id)
                ->with(['details.equivalent.product', 'details.allocations.purchaseOrder', 'purchaseOrders.detail', 'rejector'])
                ->orderByDesc('id')
                ->first();
            $pending = PendingPO::find($id);
        }

        if (!$pending && !$purchase) {
            abort(404, 'Purchase Request atau Sales Order tidak ditemukan.');
        }

        $id = $pending ? $pending->id : ($purchase ? $purchase->id_pending : null);
        $isUnitQuotation = $pending && (bool) $pending->id_unit_quotation;

        if ($isUnitQuotation) {
            // Unit Quotation punya field/relasi setara buat semua yang dibutuhkan view ini
            // (pic.client, sales, type) — cukup di-alias di titik yang beda nama kolomnya,
            // sisanya kompatibel langsung tanpa perlu view terpisah.
            $quotation = UnitQuotation::with(['sales', 'pic.client'])->find($pending->id_unit_quotation);
            if ($quotation) {
                $quotation->po_date = $quotation->po_received;
                $detQuotation = $quotation->details; // UnitQuotationDetail: sudah punya id_equivalent + price
                $subQuote = collect();
                $invoice = Invoice::where('id_unit_quotation', $quotation->id)->first();
            } else {
                $detQuotation = collect();
                $subQuote = collect();
                $invoice = null;
            }
        } else {
            $quotationId = $pending ? $pending->id_quotation : ($purchase ? $purchase->id_quotation : null);
            $quotation = $quotationId ? Quotation::with(['sales', 'pic.client'])->find($quotationId) : null;
            $detQuotation = $quotationId ? DetailQuotation::where('id_quotation', $quotationId)->get() : collect();
            $subQuote = $quotationId ? SubtitleQuotation::with('detail')->where('id_quotation', $quotationId)->get() : collect();
            $invoice = $quotation ? Invoice::where('id_quotation', $quotation->id)->first() : null;
        }

        $activity = ChangeStatus::where('id_pending', $id)->with('comment')->get();

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

        // Fetch last purchase info per product for PR items (Impor/Lokal and previous purchase price)
        $productIds = $purchase ? $purchase->details->pluck('equivalent.id_product')->filter()->unique()->values() : collect();
        $lastPurchaseHistory = [];

        if ($productIds->isNotEmpty()) {
            // 1. Check from DetailPurchaseOrder
            $poHistory = DB::table('detail_purchase_order as dpo')
                ->join('purchase_order as po', 'po.id', '=', 'dpo.id_purchase_order')
                ->leftJoin('supplier as s', 's.id', '=', 'po.id_supplier')
                ->whereIn('dpo.id_product', $productIds)
                ->where('dpo.price', '>', 0)
                ->select([
                    'dpo.id_product',
                    'dpo.price',
                    'po.date as purchase_date',
                    'po.no_po',
                    's.supplier as supplier_name',
                    's.info as supplier_info',
                    's.area as supplier_area',
                ])
                ->orderByDesc('po.date')
                ->orderByDesc('dpo.id')
                ->get()
                ->groupBy('id_product');

            // 2. Check from DetailProductIn (Barang Masuk)
            $productInHistory = DB::table('detail_product_in as dpi')
                ->join('detail_product as dp', 'dp.id', '=', 'dpi.id_detail_product')
                ->join('product_in as pi', 'pi.id', '=', 'dpi.id_product_in')
                ->leftJoin('supplier as s', 's.id', '=', 'pi.id_supplier')
                ->whereIn('dp.id_product', $productIds)
                ->where('dpi.modal', '>', 0)
                ->select([
                    'dp.id_product',
                    'dpi.modal as price',
                    'pi.date as purchase_date',
                    'pi.no_product_in as no_po',
                    's.supplier as supplier_name',
                    's.info as supplier_info',
                    's.area as supplier_area',
                ])
                ->orderByDesc('pi.date')
                ->orderByDesc('dpi.id')
                ->get()
                ->groupBy('id_product');

            // 3. Check DetailProduct fallback (HPP / modal)
            $detailProductPrices = DB::table('detail_product')
                ->whereIn('id_product', $productIds)
                ->select('id_product', 'modal', 'hpp')
                ->orderByDesc('id')
                ->get()
                ->groupBy('id_product');

            foreach ($productIds as $pId) {
                $latestPo = $poHistory->get($pId)?->first();
                $latestIn = $productInHistory->get($pId)?->first();
                $dpPrice = $detailProductPrices->get($pId)?->first();

                $picked = null;
                if ($latestPo && $latestIn) {
                    $picked = ($latestPo->purchase_date >= $latestIn->purchase_date) ? $latestPo : $latestIn;
                } elseif ($latestPo) {
                    $picked = $latestPo;
                } elseif ($latestIn) {
                    $picked = $latestIn;
                }

                if ($picked) {
                    $suppInfo = strtolower((string) ($picked->supplier_info ?? ''));
                    $suppArea = strtolower((string) ($picked->supplier_area ?? ''));
                    $isImpor = str_contains($suppInfo, 'import') 
                            || str_contains($suppInfo, 'impor') 
                            || str_contains($suppArea, 'china') 
                            || str_contains($suppArea, 'shanghai') 
                            || str_contains($suppArea, 'taiwan') 
                            || str_contains($suppArea, 'japan') 
                            || str_contains($suppArea, 'overseas');

                    $lastPurchaseHistory[$pId] = [
                        'has_history' => true,
                        'price' => (float) $picked->price,
                        'purchase_type' => $isImpor ? 'Impor' : 'Lokal',
                        'purchase_date' => $picked->purchase_date,
                        'supplier_name' => $picked->supplier_name,
                        'ref_doc' => $picked->no_po,
                    ];
                } elseif ($dpPrice && ($dpPrice->modal > 0 || $dpPrice->hpp > 0)) {
                    $lastPurchaseHistory[$pId] = [
                        'has_history' => true,
                        'price' => (float) ($dpPrice->modal ?: $dpPrice->hpp),
                        'purchase_type' => 'Lokal',
                        'purchase_date' => null,
                        'supplier_name' => 'Master HPP',
                        'ref_doc' => null,
                    ];
                } else {
                    $lastPurchaseHistory[$pId] = [
                        'has_history' => false,
                        'price' => 0,
                        'purchase_type' => null,
                        'purchase_date' => null,
                        'supplier_name' => null,
                        'ref_doc' => null,
                    ];
                }
            }
        }

        return view('pages.warehouse.purchase.detail', compact(
            'purchase', 'activity', 'subQuote', 'pending', 'quotation', 'invoice', 'detQuotation', 'isUnitQuotation',
            'discussions', 'allUsers', 'lastPurchaseHistory',
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

        $mentionedIds = [];
        if ($request->mentions) {
            foreach ($request->mentions as $userId) {
                $valInt = (int) $userId;
                if ($valInt && $valInt !== Auth::id()) {
                    $mentionedIds[$valInt] = $valInt;
                }
            }
        }

        // Auto detect @Username in message text
        $activeUsers = \App\Models\User::where('active', '1')
            ->where('id', '!=', Auth::id())
            ->get();
        foreach ($activeUsers as $user) {
            if (stripos($request->message, '@' . $user->name) !== false) {
                $mentionedIds[$user->id] = $user->id;
            }
        }

        foreach ($mentionedIds as $userId) {
            $mention = new PrDiscussionMention();
            $mention->id_discussion = $discussion->id;
            $mention->id_user_mention = $userId;
            $mention->level = '0';
            $mention->save();
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
        if (!$pending) {
            return redirect()->route('purchase.index')->with('error', 'Pending PO tidak ditemukan');
        }
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

        return DB::transaction(function () use ($request, $id) {
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
            $productIn->info = $supplier ? $supplier->info : null;
            $productIn->date = $request->date;
            $productIn->date_invoice = $request->date_invoice;
            $productIn->subtotal = $request->subtotal;
            $productIn->total_no_tax = $request->total_no_tax;
            $productIn->tax = $request->tax;
            $productIn->note = $request->note;
            $productIn->shipping = $request->shipping;
            $productIn->total = $request->total;
            $productIn->save();

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
                $dProductIn->save();
            }

            return redirect('/product-in')->with('message', 'data telah di tambahkan');
        });
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

        return DB::transaction(function () use ($request) {
            $supplier = Supplier::find($request->supplier);
            $productIn = new ProductIn();
            $productIn->no_product_in = $this->generateNoProductIn($request->warehouse[0] ?? 'BDG');
            $productIn->no_do = $request->no_do;
            $productIn->invoice = null;
            $productIn->id_supplier = $request->supplier;
            $productIn->supplier = null;
            $productIn->info = $supplier ? $supplier->info : null;
            $productIn->date = $request->date;
            $productIn->date_invoice = null;
            $productIn->subtotal = null;
            $productIn->total_no_tax = null;
            $productIn->tax = null;
            $productIn->note = null;
            $productIn->shipping = null;
            $productIn->total = null;
            $productIn->save();

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
                $dProductIn->save();
            }

            return redirect('/product-in')->with('message', 'data telah di tambahkan');
        });
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

        return DB::transaction(function () use ($request, $id) {
            $po = PurchaseOrder::findOrFail($id);
            $supplier = Supplier::findOrFail($request->supplier);

            // 1. Create the ProductIn (Barang Masuk) record, satu per PO
            $productIn = new ProductIn();
            $productIn->no_product_in = $this->generateNoProductIn($request->warehouse[0] ?? 'BDG');
            $productIn->no_do = $request->no_do;
            $productIn->invoice = $po->no_invoice_supplier;
            $productIn->id_supplier = $request->supplier;
            $productIn->id_purchase_order = $po->id;
            $productIn->info = $supplier->info;
            $productIn->date = $request->gr_date;
            $productIn->date_invoice = $po->no_invoice_supplier ? ($po->invoice_date ?: $request->gr_date) : null;
            $productIn->date_payment = $po->no_invoice_supplier
                ? $po->resolveDueDate($productIn->date_invoice)
                : null;
            $productIn->subtotal = null;
            $productIn->total_no_tax = null;
            $productIn->tax = null;
            $productIn->note = 'Otomatis dibuat via Goods Receipt PO ' . $po->no_po;
            $productIn->shipping = null;
            $productIn->total = null;
            $productIn->created_by = Auth::id();
            $productIn->save();

            $dProductSave = false;
            $damagedLines = [];

            // 2. Loop through each allocation (item PR x PO ini) untuk simpan Goods Receipt
            foreach ($request->alloc_id as $key => $allocId) {
                $allocation = PurchaseRequestDetailAllocation::find($allocId);
                if ($allocation) {
                    $status = $request->gr_status[$key];
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

            // 3. Kalau ada item yang rusak, catat sebagai Retur ke supplier
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
                if (!$po->no_gr) {
                    $po->no_gr = $this->prService->generateNoGr();
                    $po->gr_sent_at = now();
                }
                $po->receipt_status = 'Received';
                $po->save();

                // PR baru "done" kalau SEMUA PO-nya sudah diterima
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
        });
    }

    /**
     * GR buat PO Parts yang dibeli langsung tanpa Purchase Request (id_purchase_request
     * null) — sumber itemnya langsung detail_purchase_order, bukan
     * purchase_request_detail_allocation kayak goodsReceiptForm() di atas, karena
     * emang gak ada PR yang dialokasikan ke PO ini.
     */
    public function goodsReceiptFormDirect($id)
    {
        $po = PurchaseOrder::findOrFail($id);
        $details = DetailPurchaseOrder::where('id_purchase_order', $id)
            ->where('category', 'Sparepart')
            ->orderBy('id')
            ->get();

        $fullRep = [];
        foreach ($details as $key => $detail) {
            $fullRep[$key] = $detail->id_product
                ? DetailProduct::where('id_product', $detail->id_product)->with('product')->get()
                : collect([]);
        }

        $suppliers = Supplier::all();
        $previewNoGr = $po->no_gr ?: $this->prService->generateNoGr();

        return view('pages.warehouse.purchase.goods_receipt_direct', compact('po', 'details', 'fullRep', 'suppliers', 'previewNoGr'));
    }

    public function storeGoodsReceiptDirect(Request $request, $id)
    {
        $rule = [
            'no_do' => 'required|string|max:255',
            'gr_date' => 'required|date',
            'supplier' => 'required|integer|exists:supplier,id',
            'detail_id' => 'required|array',
            'detail_id.*' => 'required|integer|exists:detail_purchase_order,id',
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

        return DB::transaction(function () use ($request, $id) {
            $po = PurchaseOrder::findOrFail($id);
            $supplier = Supplier::findOrFail($request->supplier);

            $productIn = new ProductIn();
            $productIn->no_product_in = $this->generateNoProductIn($request->warehouse[0] ?? 'BDG');
            $productIn->no_do = $request->no_do;
            $productIn->invoice = $po->no_invoice_supplier;
            $productIn->id_supplier = $request->supplier;
            $productIn->id_purchase_order = $po->id;
            $productIn->info = $supplier->info;
            $productIn->date = $request->gr_date;
            $productIn->date_invoice = $po->no_invoice_supplier ? ($po->invoice_date ?: $request->gr_date) : null;
            $productIn->date_payment = $po->no_invoice_supplier
                ? $po->resolveDueDate($productIn->date_invoice)
                : null;
            $productIn->subtotal = null;
            $productIn->total_no_tax = null;
            $productIn->tax = null;
            $productIn->note = 'Otomatis dibuat via Goods Receipt PO ' . $po->no_po . ' (tanpa PR)';
            $productIn->shipping = null;
            $productIn->total = null;
            $productIn->created_by = Auth::id();
            $productIn->save();

            $dProductSave = false;
            $damagedLines = [];

            foreach ($request->detail_id as $key => $detailId) {
                $poDetail = DetailPurchaseOrder::find($detailId);
                if (!$poDetail) {
                    continue;
                }

                $status = $request->gr_status[$key];
                $poQtyCap = $poDetail->qty;

                $qtyRec = $request->qty_received[$key];
                $qtyDamaged = min((int) ($request->qty_damaged[$key] ?? 0), $poQtyCap);
                $qtyGood = $qtyRec;
                $note = $request->gr_note[$key] ?? null;
                $replId = $request->replacement[$key];
                $wh = $request->warehouse[$key];

                if ($qtyGood > 0) {
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
                if (!$po->no_gr) {
                    $po->no_gr = $this->prService->generateNoGr();
                    $po->gr_sent_at = now();
                }
                $po->receipt_status = 'Received';
                $po->save();

                return redirect()->route('purchase.show', $po->id)->with('success', 'Verifikasi Goods Receipt berhasil disimpan.');
            }

            return redirect()->back()->with('error', 'Gagal memproses Goods Receipt.');
        });
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

        // qty (kebutuhan SO) cuma boleh diubah role Logistic/Admin — sama seperti
        // qty_stock di bawah, biar nggak sembarang role bisa geser angka kebutuhan
        // pengadaan setelah PR terbit (termasuk PR yang auto-generate dari shortage).
        if ((int) $request->qty !== (int) $detail->qty) {
            if (!in_array(Auth::user()->role, ['Logistic', 'Admin'])) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'qty' => 'Hanya role Logistic/Admin yang boleh mengubah qty Purchase Request.',
                ]);
            }
            if ((int) $request->qty < (int) $detail->allocatedQty) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'qty' => "Qty tidak boleh diturunkan di bawah jumlah yang sudah dialokasikan ke PO ({$detail->allocatedQty}).",
                ]);
            }
        }

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

    // Format: 001-P/BM/VIII/2026 — sama persis polanya kayak ProductInController::
    // generateNoProductIn(), disamain biar No. Product In konsisten dari manapun
    // dia dibuat (manual atau via Goods Receipt PO).
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

    /**
     * Selesaikan Purchase Request yang barang fisiknya sudah diinput secara manual (GR Manual).
     * Dapat diakses oleh: Logistic, Admin, Super Admin, Developer.
     */
    public function completeManualGr(Request $request, $id)
    {
        $user = Auth::user();
        $userRole = $user ? $user->getRawOriginal('role') : '';
        $allowedRoles = ['Logistic', 'Admin', 'Super Admin', 'Developer'];

        if (!in_array($userRole, $allowedRoles) && !$user?->isDeveloper()) {
            return response()->json(['message' => 'Anda tidak memiliki hak akses untuk aksi ini.'], 403);
        }

        $purchase = PurchaseRequest::find($id);
        if (!$purchase) {
            return response()->json(['message' => 'Data Purchase Request tidak ditemukan.'], 404);
        }

        $idProductIn = $request->input('id_product_in');
        $noGr = trim($request->input('no_gr', ''));
        $note = trim($request->input('note', ''));
        $grDate = $request->input('gr_date', now()->format('Y-m-d'));

        $productIn = null;
        if (!empty($idProductIn)) {
            $productIn = ProductIn::find($idProductIn);
            if ($productIn && empty($noGr)) {
                $noGr = $productIn->no_product_in;
            }
        }

        // Ubah status PR ke Done (3)
        $purchase->status = '3';
        $purchase->save();

        // Update seluruh PO terkait menjadi Received & hubungkan ke Product In jika ada
        $pos = $purchase->purchaseOrders;
        foreach ($pos as $po) {
            $po->receipt_status = 'Received';
            if (!empty($noGr)) {
                $po->no_gr = $noGr;
            } elseif (empty($po->no_gr)) {
                $po->no_gr = 'GR-MANUAL';
            }
            if (empty($po->gr_sent_at)) {
                $po->gr_sent_at = $grDate ? \Carbon\Carbon::parse($grDate) : now();
            }
            $po->save();

            // Hubungkan dokumen Barang Masuk (product_in) ke PO ini jika belum tertaut
            if ($productIn && empty($productIn->id_purchase_order)) {
                $productIn->id_purchase_order = $po->id;
                $productIn->save();
            }
        }

        $linkedText = $productIn ? ' dan berhasil ditautkan ke Barang Masuk (' . $productIn->no_product_in . ').' : '.';

        return response()->json([
            'success' => true,
            'message' => 'Purchase Request ' . ($purchase->no_pr ?: '#' . $purchase->id) . ' berhasil diselesaikan dengan GR Manual' . $linkedText
        ]);
    }

    /**
     * Quick Action khusus role Developer untuk troubleshooting status PR (Bypass / Rollback).
     */
    public function devAction(Request $request, $id)
    {
        $user = Auth::user();
        $userRole = $user ? $user->getRawOriginal('role') : '';

        if ($userRole !== 'Developer' && !$user?->isDeveloper()) {
            return response()->json(['message' => 'Aksi ini hanya dapat dilakukan oleh role Developer.'], 403);
        }

        return DB::transaction(function () use ($request, $id) {
            $purchase = PurchaseRequest::find($id);
            if (!$purchase) {
                return response()->json(['message' => 'Data Purchase Request tidak ditemukan.'], 404);
            }

            $action = $request->input('action');

            if ($action === 'force_done') {
                $purchase->status = '3';
                $purchase->save();

                // Update seluruh PO terkait (jika ada) ke Received
                foreach ($purchase->purchaseOrders as $po) {
                    $po->receipt_status = 'Received';
                    if (empty($po->no_gr)) {
                        $po->no_gr = 'GR-DEV-CLOSE';
                    }
                    $po->save();
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Developer Action: PR ' . ($purchase->no_pr ?: '#' . $purchase->id) . ' berhasil dipaksa selesai (Done / status 3).'
                ]);
            } elseif ($action === 'rollback_approved') {
                $purchase->status = '1';
                $purchase->save();

                // Bersihkan info delivery pada details alokasi
                $purchase->details()->update([
                    'purchase_type' => null,
                    'cargo' => null,
                    'no_resi' => null,
                    'purchase_date' => null,
                ]);

                \App\Models\PurchaseRequestDetailAllocation::whereIn('id_purchase_request_detail', $purchase->details()->pluck('id'))
                    ->update([
                        'purchase_type' => null,
                        'cargo' => null,
                        'no_resi' => null,
                        'purchase_date' => null,
                    ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Developer Action: PR ' . ($purchase->no_pr ?: '#' . $purchase->id) . ' berhasil di-rollback ke Approved (status 1).'
                ]);
            } elseif ($action === 'rollback_new') {
                // Putuskan relasi PO jika ada
                foreach ($purchase->purchaseOrders as $po) {
                    $po->id_purchase_request = null;
                    $po->save();
                }

                $purchase->status = '0';
                $purchase->save();

                return response()->json([
                    'success' => true,
                    'message' => 'Developer Action: PR ' . ($purchase->no_pr ?: '#' . $purchase->id) . ' berhasil dikembalikan ke New PR (status 0).'
                ]);
            }

            return response()->json(['message' => 'Aksi developer tidak valid.'], 422);
        });
    }

    /**
     * Kembalikan Purchase Request dari status Approved (1) ke New PR (0).
     */
    public function rollbackToNew(Request $request, $id)
    {
        $user = Auth::user();
        $userRole = $user ? $user->getRawOriginal('role') : '';

        if (!in_array($userRole, ['Developer', 'Admin', 'Super Admin', 'Logistic']) && !$user?->isDeveloper()) {
            return response()->json(['message' => 'Akses tidak diizinkan.'], 403);
        }

        return DB::transaction(function () use ($id) {
            $purchase = PurchaseRequest::find($id);
            if (!$purchase) {
                return response()->json(['message' => 'Data Purchase Request tidak ditemukan.'], 404);
            }

            // Putuskan relasi PO yang terhubung jika ada
            foreach ($purchase->purchaseOrders as $po) {
                $po->id_purchase_request = null;
                $po->save();
            }

            // Bersihkan info delivery pada details alokasi
            $purchase->details()->update([
                'purchase_type' => null,
                'cargo' => null,
                'no_resi' => null,
                'purchase_date' => null,
            ]);

            \App\Models\PurchaseRequestDetailAllocation::whereIn('id_purchase_request_detail', $purchase->details()->pluck('id'))
                ->update([
                    'purchase_type' => null,
                    'cargo' => null,
                    'no_resi' => null,
                    'purchase_date' => null,
                ]);

            $purchase->status = '0';
            $purchase->save();

            $poMsg = $poCount > 0 ? " ({$poCount} PO terkait berhasil dilepas)" : "";
            return response()->json([
                'success' => true,
                'message' => 'Purchase Request ' . ($purchase->no_pr ?: '#' . $purchase->id) . " berhasil dikembalikan ke New PR (Draft){$poMsg}."
            ]);
        });
    }

    /**
     * Cari PO yang tersedia untuk dihubungkan ke PR.
     */
    public function searchPoToLink(Request $request)
    {
        $q = trim($request->get('q', ''));
        $currentPrId = $request->get('pr_id');

        $query = PurchaseOrder::query();

        if (!empty($q)) {
            $query->where(function ($sub) use ($q) {
                $sub->where('no_po', 'like', "%{$q}%")
                    ->orWhere('company', 'like', "%{$q}%")
                    ->orWhere('no_reference', 'like', "%{$q}%");
            });
        }

        // Jangan tampilkan PO yang sudah tertaut ke PR ini
        if ($currentPrId) {
            $query->where(function ($sub) use ($currentPrId) {
                $sub->whereNull('id_purchase_request')
                    ->orWhere('id_purchase_request', '!=', $currentPrId);
            });
        }

        $results = $query->orderByDesc('id')->take(30)->get();

        $data = $results->map(function ($po) {
            $dateStr = $po->date ? Carbon::parse($po->date)->format('d/m/Y') : '-';
            $companyStr = $po->company ?: 'Tanpa Vendor';
            $statusStr = $po->receipt_status ?: 'Open';
            $totalStr = $po->total ? 'Rp ' . number_format($po->total, 0, ',', '.') : '';

            $text = "{$po->no_po} — {$companyStr} [Tgl: {$dateStr}]";
            if ($totalStr) {
                $text .= " ({$totalStr})";
            }
            if ($po->id_purchase_request) {
                $text .= " (Tertaut ke PR #{$po->id_purchase_request})";
            }

            return [
                'id' => $po->id,
                'no_po' => $po->no_po,
                'company' => $companyStr,
                'date' => $dateStr,
                'total' => $totalStr,
                'status' => $statusStr,
                'text' => $text,
                'is_linked_other' => (bool) $po->id_purchase_request,
            ];
        });

        return response()->json([
            'results' => $data,
            'data' => $data
        ]);
    }

    /**
     * Hubungkan Purchase Request ke satu atau beberapa PO yang sudah terbit.
     */
    public function linkPurchaseOrder(Request $request, $id)
    {
        $purchase = PurchaseRequest::where('id_pending', $id)->orWhere('id', $id)->first();
        if (!$purchase) {
            return response()->json(['success' => false, 'message' => 'Purchase Request tidak ditemukan.'], 404);
        }

        $poIds = (array) $request->input('id_purchase_order', []);
        if (empty($poIds)) {
            return response()->json(['success' => false, 'message' => 'Silakan pilih minimal satu Purchase Order (PO).'], 422);
        }

        $linkedPoNames = [];
        foreach ($poIds as $poId) {
            $po = PurchaseOrder::with('detail')->find($poId);
            if (!$po) continue;

            $po->id_purchase_request = $purchase->id;
            $po->save();
            $linkedPoNames[] = $po->no_po ?: ('PO #' . $po->id);

            // Alokasikan item PR ke PO ini jika belum teralokasi
            foreach ($purchase->details as $prDetail) {
                $existingAlloc = PurchaseRequestDetailAllocation::where('id_purchase_request_detail', $prDetail->id)
                    ->where('id_purchase_order', $po->id)
                    ->first();

                if (!$existingAlloc) {
                    $matchingPoDetail = null;
                    $prProductId = $prDetail->equivalent->id_product ?? null;
                    if ($prProductId) {
                        $matchingPoDetail = $po->detail->firstWhere('id_product', $prProductId);
                    }

                    $allocQty = $matchingPoDetail ? min($matchingPoDetail->qty, $prDetail->qty) : ($prDetail->remainingQty > 0 ? $prDetail->remainingQty : $prDetail->qty);

                    PurchaseRequestDetailAllocation::create([
                        'id_purchase_request_detail' => $prDetail->id,
                        'id_purchase_order' => $po->id,
                        'qty' => $allocQty > 0 ? $allocQty : 1,
                        'purchase_type' => $po->category == 'Unit' ? 'Lokal' : null,
                        'cargo' => $po->on_delivery_cargo,
                        'no_resi' => $po->on_delivery_no_resi,
                        'purchase_date' => $po->date,
                    ]);
                }
            }
        }

        $poListStr = implode(', ', $linkedPoNames);
        return response()->json([
            'success' => true,
            'message' => "Purchase Order ({$poListStr}) berhasil dihubungkan ke Purchase Request ini."
        ]);
    }

    /**
     * Lepaskan tautan PO dari Purchase Request.
     */
    public function unlinkPurchaseOrder(Request $request, $id)
    {
        $purchase = PurchaseRequest::where('id_pending', $id)->orWhere('id', $id)->first();
        if (!$purchase) {
            return response()->json(['success' => false, 'message' => 'Purchase Request tidak ditemukan.'], 404);
        }

        $poId = $request->input('id_purchase_order');
        $po = PurchaseOrder::find($poId);
        if ($po && $po->id_purchase_request == $purchase->id) {
            $po->id_purchase_request = null;
            $po->save();

            // Hapus alokasi detail PR untuk PO ini
            PurchaseRequestDetailAllocation::whereIn('id_purchase_request_detail', $purchase->details->pluck('id'))
                ->where('id_purchase_order', $po->id)
                ->delete();

            return response()->json([
                'success' => true,
                'message' => "Tautan Purchase Order {$po->no_po} berhasil dilepas dari PR ini."
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Purchase Order tidak tertaut ke PR ini.'], 400);
    }

    /**
     * Ambil daftar item PR yang sudah di-approve (status == 1) dan masih memiliki sisa kebutuhan (remainingQty > 0).
     * Digunakan oleh halaman pembuatan PO untuk konsolidasi item dari beberapa PR.
     */
    public function getAvailablePrItems(Request $request)
    {
        $q = trim($request->get('q', ''));

        // Cari PR yang statusnya 1 (Approved)
        $prs = PurchaseRequest::where('status', 1)
            ->with([
                'pending',
                'details.equivalent.product',
                'details.allocations'
            ])
            ->orderByDesc('id')
            ->get();

        $items = [];
        foreach ($prs as $pr) {
            $noPr = $pr->no_pr ?: ('PR #' . $pr->id);
            $noSo = $pr->pending->no_pending ?? '-';
            $prDate = $pr->date ? Carbon::parse($pr->date)->format('d/m/Y') : ($pr->created_at ? $pr->created_at->format('d/m/Y') : '-');

            foreach ($pr->details as $detail) {
                $rem = $detail->remainingQty;
                if ($rem <= 0) continue;

                $product = $detail->equivalent->product ?? null;
                $brandPn = trim(($detail->equivalent->brand ?? '') . ' ' . ($detail->equivalent->pn ?? ''));
                $commodityDesc = $product ? trim(($product->commodity ?? '') . ' — ' . ($product->description ?? '')) : '';
                $productName = $commodityDesc ?: ($brandPn ?: 'Item #' . $detail->id);
                $productId = $product ? $product->id : null;
                $unit = ($product && $product->unit && $product->unit !== '-') ? $product->unit : 'Pcs';

                // Filter pencarian jika q diisi
                if (!empty($q)) {
                    $searchPool = strtolower("{$noPr} {$noSo} {$brandPn} {$commodityDesc} {$detail->note}");
                    if (!str_contains($searchPool, strtolower($q))) {
                        continue;
                    }
                }

                $items[] = [
                    'pr_detail_id' => $detail->id,
                    'pr_id' => $pr->id,
                    'no_pr' => $noPr,
                    'pr_date' => $prDate,
                    'no_so' => $noSo,
                    'id_product' => $productId,
                    'brand_pn' => $brandPn,
                    'product_name' => $productName,
                    'unit' => $unit,
                    'total_qty' => $detail->totalQty,
                    'remaining_qty' => $rem,
                    'qty_to_take' => $rem,
                    'price' => $detail->equivalent->price ?? ($product->price ?? 0),
                    'note' => $detail->note ?: '-',
                ];
            }
        }

        return response()->json([
            'success' => true,
            'total' => count($items),
            'items' => $items
        ]);
    }
}
