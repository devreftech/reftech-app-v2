<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Contract;
use App\Models\Delivery;
use App\Models\DetailDelivery;
use App\Models\DetailPendingPO;
use App\Models\Invoice;
use App\Models\KanbanBoard;
use App\Models\KanbanTask;
use App\Models\Payment;
use App\Models\PendingPO;
use App\Models\Pic;
use App\Models\Product;
use App\Models\PurchaseRequest;
use App\Models\Unit;
use App\Models\UnitQuotation;
use App\Models\UnitQuotationDetail;
use App\Models\UnitQuotationOption;
use App\Models\User;
use App\Services\PurchaseRequestService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class UnitQuotationController extends Controller
{
    protected PurchaseRequestService $prService;

    public function __construct(PurchaseRequestService $prService)
    {
        $this->prService = $prService;
    }

    public function index()
    {
        return view('pages.unit-quotation.index');
    }

    public function create(Request $request)
    {
        $defaultNoQuote = $this->generateNoQuote();
        $isManager = in_array(Auth::user()->role, ['Admin', 'Sales Manager']);

        $salesUsers = $isManager
            ? User::where('role', 'Sales')->where('active', '1')->where('id', '!=', 23)->orderBy('name')->get(['id', 'name'])
            : collect();

        $clients = $isManager
            ? Client::orderBy('company')->get()
            : Client::where('id_sales', Auth::id())->orderBy('company')->get();

        $selectedClient   = $request->get('client_id');
        $selectedPic      = $request->get('pic_id');
        $selectedProspect = $request->get('prospect_id');

        if ($selectedProspect && !$selectedClient) {
            $prospect = \App\Models\Prospect::with('pic.client')->find($selectedProspect);
            if ($prospect && $prospect->pic) {
                $selectedClient = $prospect->pic->id_client;
                $selectedPic    = $prospect->id_pic;
            }
        }

        $paymentTemplates = $isManager
            ? collect()
            : \App\Models\SalesPaymentTemplate::with('client')
                ->where('id_sales', Auth::id())
                ->orderBy('is_default', 'desc')
                ->orderBy('name')
                ->get();

        $transportationPrices = \App\Models\TransportationPrice::orderBy('city')->get(['id', 'city', 'price']);

        return view('pages.unit-quotation.create', compact('clients', 'defaultNoQuote', 'paymentTemplates', 'isManager', 'salesUsers', 'transportationPrices', 'selectedClient', 'selectedPic', 'selectedProspect'));
    }

    public function getClientsBySales(Request $request, $salesId)
    {
        $query = Client::query();

        if ($salesId === 'self_leads' || $salesId === 'self') {
            // Data leads / client yang diinput sendiri oleh user yang sedang login
            $query->where('id_sales', Auth::id());
        } elseif ($salesId === 'all' || empty($salesId) || $salesId === '0') {
            // Semua client
        } else {
            $query->where('id_sales', $salesId);
        }

        $clients = $query->orderBy('company')->get(['id', 'company', 'role']);

        return response()->json(['clients' => $clients]);
    }

    public function getPics($clientId)
    {
        $pics   = Pic::where('id_client', $clientId)->get(['id', 'name_pic', 'position']);
        $client = Client::with('plants')->find($clientId);

        $clientTemplate = \App\Models\SalesPaymentTemplate::where('id_sales', Auth::id())
            ->where(function ($q) use ($clientId) {
                $q->where('id_client', $clientId)
                  ->orWhereJsonContains('client_ids', (int) $clientId)
                  ->orWhereRaw('JSON_CONTAINS(client_ids, ?)', [json_encode((int) $clientId)]);
            })
            ->first();

        $defaultTemplate = \App\Models\SalesPaymentTemplate::where('id_sales', Auth::id())
            ->where('is_default', true)
            ->first();

        return response()->json([
            'pics'           => $pics,
            'address'        => $client->address ?? '',
            'subAddress'     => $client->subAddress ?? '',
            'plants'         => $client->plants ?? [],
            'clientPayment'  => $clientTemplate ? $clientTemplate->payment_term : null,
            'defaultPayment' => $defaultTemplate ? $defaultTemplate->payment_term : null,
        ]);
    }

    public function store(Request $request)
    {
        $processedOptions = $this->processOptionsInput($request->input('options', []));
        $first = $processedOptions[0] ?? $this->emptyOptionTotals();

        $client   = $request->id_client ? Client::find($request->id_client) : null;
        $prospect = $request->id_prospect ? \App\Models\Prospect::find($request->id_prospect) : null;
        $idSupport = $prospect ? ($prospect->id_support ?: ($client->id_support ?? null)) : ($client->id_support ?? null);

        $isManager = in_array(Auth::user()->role, ['Admin', 'Sales Manager']);
        $idSales = Auth::id();
        if ($isManager) {
            if ($request->input('client_source_type') === 'self_leads') {
                $idSales = Auth::id();
            } elseif ($request->filled('id_sales')) {
                $idSales = $request->id_sales;
            } elseif ($client && $client->id_sales) {
                $idSales = $client->id_sales;
            }
        }

        $quote = UnitQuotation::create([
            'id_client'        => $request->id_client ?: null,
            'id_pic'           => $request->id_pic ?: null,
            'id_plant'         => $request->id_plant ?: null,
            'address'          => $request->address ?: null,
            'id_sales'         => $idSales,
            'id_support'       => $idSupport,
            'no_quote'         => $request->no_quote ?: $this->generateNoQuote($request->type),
            'attn'             => $request->attn,
            'no_pr'            => $request->no_pr ?: null,
            'date'             => $request->date,
            'expired_date'     => $request->expired_date ?: \Carbon\Carbon::parse($request->date)->addMonth()->format('Y-m-d'),
            'title'            => $request->title,
            'type'             => $request->type,
            'unit_condition'   => $request->type === 'Unit' ? $request->unit_condition : null,
            'week'             => $request->week,
            'subtotal'         => $first['subtotal'],
            'diskon'           => $first['diskon'],
            'diskon_type'      => $first['diskon_type'],
            'tax'              => $first['tax'],
            'tax_amount'       => $first['tax_amount'],
            'shipping'         => $first['shipping'],
            'total'            => $first['total'],
            'note'             => $request->note,
            'validity'         => $request->validity,
            'pricing'          => $request->pricing,
            'warranty'         => $request->warranty,
            'delivery_process' => $request->delivery_process,
            'payment'          => $request->payment,
            'status'           => 'draft',
            'revision_number'  => 0,
            'is_latest'        => 1,
        ]);

        $this->saveOptions($quote->id, $processedOptions);

        if ($prospect) {
            $prospect->id_quotation = $quote->id;
            $prospect->level = '1';
            $prospect->save();
        }

        $quote->statusHistory()->create(['status' => 'draft', 'note' => null]);

        return redirect()->route('unit-quotation.show', $quote->id)
            ->with('success', 'Quotation created successfully.');
    }

    public function show($id)
    {
        $quote       = UnitQuotation::with([
            'client', 'pic', 'plant', 'sales', 'statusHistory', 'comments.user',
            'details.unit', 'details.equivalent.product',
            'options.details.unit', 'options.details.equivalent.product',
        ])->findOrFail($id);
        $allVersions = $quote->allVersions();
        $invoices    = Invoice::where('id_unit_quotation', $quote->id)->orderByRaw("FIELD(type,'DP','BP','CT')")->get();
        $contracts   = Contract::where('id_unit_quotation', $quote->id)->get();

        $thisYear      = \Carbon\Carbon::now()->year;
        $numberLastSC  = Contract::where('type', 'Selling')->where('level', '1')
            ->whereYear('date', \Carbon\Carbon::now())->orderByDesc('id')->first('no_contract');
        if ($numberLastSC && preg_match('/^\d{3}/', $numberLastSC->no_contract, $m)) {
            $formattedNumberSC = str_pad((int) $m[0] + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $formattedNumberSC = '001';
        }
        // Nomor kontrak unit dipisah per (Selling/Order x PPN/Non-PPN) — lihat Contract::unitContractNumbers().
        $unitNumbers = Contract::unitContractNumbers($thisYear);

        $payments  = Payment::where('id_unit_quotation', $quote->id)->orderBy('id')->get();
        $pendingPo = PendingPO::where('id_unit_quotation', $quote->id)->first();

        $user = Auth::user();
        $kanbanBoards = $user->role === 'Admin'
            ? KanbanBoard::where('type', '!=', 'monitoring')->orderBy('title')->get()
            : $user->kanbanBoards()->where('type', '!=', 'monitoring')->orderBy('title')->get();
        // Kalau quotation ini udah pernah di-post ke Kanban, tombol Action-nya ganti
        // jadi link "Monitoring Project" langsung ke kartunya, bukan modal Post lagi.
        $kanbanTask = KanbanTask::where('id_unit_quotation', $quote->id)->latest()->first();

        return view('pages.unit-quotation.detail', compact('quote', 'allVersions', 'invoices', 'contracts', 'payments', 'thisYear', 'formattedNumberSC', 'unitNumbers', 'pendingPo', 'kanbanBoards', 'kanbanTask'));
    }

    /**
     * "Post to Kanban" — bikin kartu baru di board & kolom pilihan user, ditautkan
     * balik ke quotation ini lewat id_unit_quotation (beda dari pending_po_id yang
     * cuma keisi kalau quotation-nya udah punya PO/SO — post-to-kanban bisa dipakai
     * dari status manapun).
     */
    public function postToKanban(Request $request, $id)
    {
        $quote = UnitQuotation::with('client')->findOrFail($id);

        $request->validate([
            'board_id' => 'required|exists:kanban_boards,id',
            'mode' => 'nullable|in:new,link',
            'column_id' => 'required_without:task_id|nullable|string',
            'task_id' => 'required_if:mode,link|nullable|exists:kanban_tasks,id',
        ]);

        $board = KanbanBoard::findOrFail($request->board_id);
        if (Auth::user()->role !== 'Admin' && !$board->members->contains(Auth::id())) {
            abort(403, 'Anda bukan anggota board ini.');
        }

        // Satu quotation cuma boleh nempel ke satu kartu aktif.
        if (KanbanTask::where('id_unit_quotation', $quote->id)->exists()) {
            return redirect()->route('unit-quotation.show', $quote->id)
                ->with('error', 'Quotation ini sudah terhubung ke sebuah kartu Kanban.');
        }

        if ($request->mode === 'link' && $request->task_id) {
            $task = KanbanTask::where('id', $request->task_id)
                ->where('board_id', $board->id)
                ->firstOrFail();

            if ($task->id_unit_quotation || $task->pending_po_id) {
                return redirect()->route('unit-quotation.show', $quote->id)
                    ->with('error', 'Kartu yang dipilih sudah terhubung ke quotation/PO lain.');
            }

            $pendingPo = PendingPO::where('id_unit_quotation', $quote->id)->first();
            $task->id_unit_quotation = $quote->id;
            if ($pendingPo) {
                $task->pending_po_id = $pendingPo->id;
                \App\Models\ProjectExpense::where('id_kanban_task', $task->id)
                    ->whereNull('id_pending')
                    ->update(['id_pending' => $pendingPo->id]);
            }
            $task->save();

            return redirect()->route('unit-quotation.show', $quote->id)
                ->with('success', 'Quotation berhasil dihubungkan ke kartu "' . $task->title . '".');
        }

        $columnId = (int) str_replace('column_', '', $request->column_id);
        $maxPos = KanbanTask::where('column_id', $columnId)->max('position');
        $position = is_null($maxPos) ? 0 : $maxPos + 1;

        KanbanTask::create([
            'board_id' => $board->id,
            'column_id' => $columnId,
            'title' => $quote->no_quote . ' — ' . ($quote->client->company ?? '-'),
            'description' => $quote->title,
            'position' => $position,
            'priority' => 'medium',
            'id_unit_quotation' => $quote->id,
        ]);

        return redirect()->route('unit-quotation.show', $quote->id)
            ->with('success', 'Quotation berhasil di-post ke board "' . $board->title . '".');
    }

    /**
     * Daftar kartu di sebuah board yang masih bisa dihubungkan (belum nempel ke
     * quotation/PO manapun) — dipakai dropdown "Hubungkan ke kartu" di modal Post to Kanban.
     */
    public function getLinkableKanbanTasks($boardId)
    {
        $board = KanbanBoard::with('members')->findOrFail($boardId);
        if (Auth::user()->role !== 'Admin' && !$board->members->contains(Auth::id())) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $tasks = KanbanTask::with('column')
            ->where('board_id', $board->id)
            ->whereNull('id_unit_quotation')
            ->whereNull('pending_po_id')
            ->orderBy('column_id')
            ->orderBy('position')
            ->get()
            ->map(function ($t) {
                return [
                    'id' => $t->id,
                    'title' => $t->title,
                    'column' => $t->column ? $t->column->title : '-',
                ];
            });

        return response()->json(['success' => true, 'tasks' => $tasks]);
    }

    public function storeComment(Request $request, $id)
    {
        $request->validate([
            'comment' => 'required|string',
        ]);

        $quote = UnitQuotation::findOrFail($id);

        $comment = $quote->comments()->create([
            'user_id' => Auth::id(),
            'comment' => $request->comment,
        ]);

        return response()->json(['success' => true, 'comment' => $comment->load('user')]);
    }

    public function updateComment(Request $request, $id)
    {
        $comment = \App\Models\UnitQuotationComment::findOrFail($id);

        if ($comment->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'comment' => 'required|string',
        ]);

        $comment->update(['comment' => $request->comment]);

        return response()->json(['success' => true]);
    }

    public function destroyComment($id)
    {
        $comment = \App\Models\UnitQuotationComment::findOrFail($id);

        if ($comment->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $comment->delete();

        return response()->json(['success' => true]);
    }

    public function edit($id)
    {
        $quote   = UnitQuotation::with([
            'client', 'pic', 'plant',
            'options.details.unit', 'options.details.fixedAsset', 'options.details.equivalent.product',
            'details.unit', 'details.fixedAsset', 'details.equivalent.product',
        ])->findOrFail($id);
        $clients = Client::orderBy('company')->get();
        $paymentTemplates = \App\Models\SalesPaymentTemplate::with('client')
            ->where('id_sales', Auth::id())
            ->orderBy('is_default', 'desc')
            ->orderBy('name')
            ->get();

        $mapItem = function ($d) {
            return [
                'type'          => $d->type,
                'id_unit'       => $d->id_unit,
                'id_fixed_asset'=> $d->id_fixed_asset,
                'id_equivalent' => $d->id_equivalent,
                'unit'          => $d->unit ? $d->unit->toArray() : null,
                'fixed_asset'   => $d->fixedAsset ? [
                    'id'            => $d->fixedAsset->id,
                    'code'          => $d->fixedAsset->code,
                    'serial_number' => $d->fixedAsset->serial_number,
                ] : null,
                'equivalent'    => $d->equivalent ? [
                    'id'           => $d->equivalent->id,
                    'pn'           => $d->equivalent->pn,
                    'brand'        => $d->equivalent->brand,
                    'product_name' => optional($d->equivalent->product)->name,
                    'product_sku'  => optional($d->equivalent->product)->sku,
                ] : null,
                'spec_visible'=> $d->getSpecVisibleArray(),
                'label'       => $d->label,
                'description' => $d->description,
                'qty'         => (float) $d->qty,
                'info_qty'    => $d->info_qty,
                'price'       => (float) $d->price,
                'disc'        => (float) $d->disc,
            ];
        };

        if ($quote->options->isEmpty()) {
            // Quotation lama (dibuat sebelum fitur multi-opsi ada) — bungkus detail
            // yang sudah ada jadi 1 opsi virtual, biar form edit tetap konsisten.
            $editOptions = [[
                'title'       => 'Opsi 1',
                'diskon'      => (float) $quote->diskon,
                'diskon_type' => $quote->diskon_type ?? 'percent',
                'tax'         => (bool) $quote->tax,
                'shipping'    => (float) $quote->shipping,
                'items'       => $quote->details->map($mapItem)->values(),
            ]];
        } else {
            $editOptions = $quote->options->map(function ($opt) use ($mapItem) {
                return [
                    'title'       => $opt->title,
                    'diskon'      => (float) $opt->diskon,
                    'diskon_type' => $opt->diskon_type,
                    'tax'         => (bool) $opt->tax,
                    'shipping'    => (float) $opt->shipping,
                    'items'       => $opt->details->map($mapItem)->values(),
                ];
            })->values();
        }

        $transportationPrices = \App\Models\TransportationPrice::orderBy('city')->get(['id', 'city', 'price']);

        return view('pages.unit-quotation.edit', compact('quote', 'clients', 'editOptions', 'paymentTemplates', 'transportationPrices'));
    }

    public function update(Request $request, $id)
    {
        $quote = UnitQuotation::findOrFail($id);

        $processedOptions = $this->processOptionsInput($request->input('options', []));
        $first = $processedOptions[0] ?? $this->emptyOptionTotals();

        $quote->update([
            'id_client'        => $request->id_client ?: null,
            'id_pic'           => $request->id_pic ?: null,
            'id_plant'         => $request->id_plant ?: null,
            'address'          => $request->address ?: null,
            'no_quote'         => $request->no_quote ?: $quote->no_quote,
            'attn'             => $request->attn,
            'no_pr'            => $request->no_pr ?: null,
            'date'             => $request->date,
            'expired_date'     => $request->expired_date ?: \Carbon\Carbon::parse($request->date)->addMonth()->format('Y-m-d'),
            'title'            => $request->title,
            'type'             => $request->type,
            'unit_condition'   => $request->type === 'Unit' ? $request->unit_condition : null,
            'week'             => $request->week,
            'subtotal'         => $first['subtotal'],
            'diskon'           => $first['diskon'],
            'diskon_type'      => $first['diskon_type'],
            'tax'              => $first['tax'],
            'tax_amount'       => $first['tax_amount'],
            'shipping'         => $first['shipping'],
            'total'            => $first['total'],
            'note'             => $request->note,
            'validity'         => $request->validity,
            'pricing'          => $request->pricing,
            'warranty'         => $request->warranty,
            'delivery_process' => $request->delivery_process,
            'payment'          => $request->payment,
        ]);

        // Hapus semua detail & opsi lama, baru dibuat ulang dari input —
        // sama seperti cara detail biasa disimpan ulang sebelum fitur opsi ada.
        UnitQuotationDetail::where('id_unit_quotation', $quote->id)->delete();
        UnitQuotationOption::where('id_unit_quotation', $quote->id)->delete();
        $this->saveOptions($quote->id, $processedOptions);

        return redirect()->route('unit-quotation.show', $quote->id)
            ->with('success', 'Quotation updated successfully.');
    }

    public function revise($id)
    {
        $source = UnitQuotation::with(['details', 'options.details'])->findOrFail($id);

        $rootId  = $source->root_id ?? $source->id;
        $nextRev = UnitQuotation::where(function ($q) use ($rootId) {
            $q->where('id', $rootId)->orWhere('root_id', $rootId);
        })->max('revision_number') + 1;

        $baseNo      = preg_replace('/-R\d+$/', '', $source->no_quote);
        $newNoQuote  = $baseNo . '-R' . $nextRev;

        // Mark all versions as not latest, and set status to 'revision'
        UnitQuotation::where(function ($q) use ($rootId) {
            $q->where('id', $rootId)->orWhere('root_id', $rootId);
        })->update(['is_latest' => 0, 'status' => 'revision']);

        $newQuote = UnitQuotation::create([
            'root_id'          => $rootId,
            'revision_number'  => $nextRev,
            'is_latest'        => 1,
            'id_client'        => $source->id_client,
            'id_pic'           => $source->id_pic,
            'id_plant'         => $source->id_plant,
            'address'          => $source->address,
            'id_sales'         => $source->id_sales,
            'id_support'       => $source->id_support,
            'no_quote'         => $newNoQuote,
            'attn'             => $source->attn,
            'no_pr'            => $source->no_pr,
            'date'             => now()->toDateString(),
            'expired_date'     => $source->expired_date ? $source->expired_date->format('Y-m-d') : \Carbon\Carbon::now()->addMonth()->format('Y-m-d'),
            'title'            => $source->title,
            'type'             => $source->type,
            'unit_condition'   => $source->unit_condition,
            'week'             => $source->week,
            'subtotal'         => $source->subtotal,
            'diskon'           => $source->diskon,
            'diskon_type'      => $source->diskon_type,
            'tax'              => $source->tax,
            'tax_amount'       => $source->tax_amount,
            'shipping'         => $source->shipping,
            'total'            => $source->total,
            'note'             => $source->note,
            'validity'         => $source->validity,
            'pricing'          => $source->pricing,
            'warranty'         => $source->warranty,
            'delivery_process' => $source->delivery_process,
            'payment'          => $source->payment,
            'status'           => 'revision',
        ]);

        $duplicateDetail = function ($d, $newQuoteId, $newOptionId = null) {
            UnitQuotationDetail::create([
                'id_unit_quotation' => $newQuoteId,
                'id_option'         => $newOptionId,
                'type'              => $d->type,
                'id_unit'           => $d->id_unit,
                'id_fixed_asset'    => $d->id_fixed_asset,
                'id_equivalent'     => $d->id_equivalent,
                'spec_visible'      => $d->spec_visible,
                'label'             => $d->label,
                'description'       => $d->description,
                'qty'               => $d->qty,
                'info_qty'          => $d->info_qty,
                'price'             => $d->price,
                'disc'              => $d->disc,
                'amount'            => $d->amount,
                'sort_order'        => $d->sort_order,
            ]);
        };

        if ($source->options->isEmpty()) {
            foreach ($source->details as $d) {
                $duplicateDetail($d, $newQuote->id);
            }
        } else {
            foreach ($source->options as $opt) {
                $newOption = UnitQuotationOption::create([
                    'id_unit_quotation' => $newQuote->id,
                    'title'             => $opt->title,
                    'sort_order'        => $opt->sort_order,
                    'subtotal'          => $opt->subtotal,
                    'diskon'            => $opt->diskon,
                    'diskon_type'       => $opt->diskon_type,
                    'tax'               => $opt->tax,
                    'tax_amount'        => $opt->tax_amount,
                    'shipping'          => $opt->shipping,
                    'total'             => $opt->total,
                ]);
                foreach ($opt->details as $d) {
                    $duplicateDetail($d, $newQuote->id, $newOption->id);
                }
            }
        }

        return redirect()->route('unit-quotation.show', $newQuote->id)
            ->with('success', 'Revisi berhasil dibuat: ' . $newNoQuote);
    }

    public function print($id)
    {
        $quote = UnitQuotation::with(['client', 'pic', 'plant', 'sales', 'details.unit', 'details.equivalent.product'])->findOrFail($id);
        return view('pages.unit-quotation.print', compact('quote'));
    }

    public function storeDelivery(Request $request, $id)
    {
        $quote = UnitQuotation::with('details.unit', 'details.equivalent.product')->findOrFail($id);

        $selectedIds  = (array) $request->input('item_ids', []);
        $requestedQty = (array) $request->input('qty', []);

        $delivery = new Delivery();
        $delivery->id_unit_quotation = $quote->id;
        $delivery->id_invoice        = $request->id_invoice ?: null;
        $delivery->date              = $request->filled('date') ? $request->date : null;
        $delivery->destination       = $request->destination;
        $delivery->type              = $request->type ?? 'Ekspedisi';
        $delivery->code              = 'Unit';
        $delivery->save();

        $pendingHeader = null;
        $itemCount     = 0;

        foreach ($quote->details as $item) {
            if ($item->type === 'header') {
                $pendingHeader = $item->label;
                continue;
            }

            if (!in_array($item->id, $selectedIds)) {
                continue;
            }

            $qtyToSend = min((float) ($requestedQty[$item->id] ?? 0), $item->remaining_qty);
            if ($qtyToSend <= 0) {
                continue;
            }

            if ($pendingHeader !== null) {
                $headerRow = new DetailDelivery();
                $headerRow->id_delivery = $delivery->id;
                $headerRow->type        = 'header';
                $headerRow->desc        = $pendingHeader;
                $headerRow->qty         = 0;
                $headerRow->info_qty    = '';
                $headerRow->view        = '0';
                $headerRow->save();
                $pendingHeader = null;
            }

            if ($item->equivalent) {
                $spParts = array_filter([
                    $item->equivalent->brand ?? '',
                    $item->equivalent->pn ?? '',
                    $item->label ?: optional($item->equivalent->product)->description ?: $item->description
                ]);
                $desc = implode(' — ', $spParts);
            } elseif ($item->unit) {
                $desc = $item->label ?: trim($item->unit->brand . ' ' . $item->unit->sku . ($item->unit->model ? ' — ' . $item->unit->model : ''));
            } else {
                $desc = $item->label ?: $item->description;
            }

            $dDelivery = new DetailDelivery();
            $dDelivery->id_delivery              = $delivery->id;
            $dDelivery->id_unit_quotation_detail = $item->id;
            $dDelivery->id_pn                    = null;
            $dDelivery->desc                     = $desc;
            $dDelivery->qty                      = $qtyToSend;
            $dDelivery->info_qty                 = $item->info_qty ?? 'Unit';
            $dDelivery->view                     = '0';
            $dDelivery->save();
            $itemCount++;
        }

        if ($itemCount === 0) {
            $delivery->delete();
            return redirect()->back()->with('error', 'Pilih minimal 1 item dengan qty > 0 untuk dikirim.');
        }

        if ($request->id_invoice) {
            return redirect()->to(route('invoice.show_unit', $request->id_invoice) . '#tab-delivery')
                ->with('success', 'Surat Jalan berhasil dibuat.');
        }

        return redirect()->to(route('unit-quotation.show', $quote->id) . '#tab-delivery')
            ->with('success', 'Surat Jalan berhasil dibuat.');
    }

    public function changeStatus(Request $request, $id)
    {
        $quote      = UnitQuotation::findOrFail($id);
        $newStatus  = $request->status;
        $updateData = ['status' => $newStatus];

        // Quotation dengan >1 opsi belum "final" — customer belum memutuskan mana
        // yang dipilih, jadi belum boleh lanjut ke PO Received (hitung PO dari
        // opsi yang mana?). Sales harus hapus opsi yang kalah dulu lewat edit.
        if ($newStatus === 'po_received' && $quote->has_multiple_options) {
            return redirect()->back()->with('error', 'Quotation ini masih punya lebih dari 1 opsi. Hapus opsi yang tidak dipilih customer dulu (lewat Edit) sebelum menandai PO Received.');
        }

        // Reset expired_date +1 month from today on every active status update
        // Exception: 'loss' and 'cancel' are final — do not extend
        $finalStatuses = ['loss', 'cancel'];
        if (!in_array($newStatus, $finalStatuses)) {
            $updateData['expired_date'] = \Carbon\Carbon::now()->addMonth()->format('Y-m-d');
        }

        $quote->update($updateData);

        $quote->statusHistory()->create([
            'status' => $newStatus,
            'note'   => $request->note,
        ]);

        if ($newStatus === 'po_received') {
            $this->createPendingPoForUnitQuotation($quote);

            $client = $quote->client;
            if ($client && ($client->id_issues != "5" || $client->role !== 'Customers')) {
                $client->id_issues = '5';
                $client->role = 'Customers';
                $client->save();

                \App\Models\CrmStatus::create([
                    'id_client' => $client->id,
                    'status'    => 2,
                ]);
            }
        }

        return redirect()->route('unit-quotation.show', $id)
            ->with('success', 'Status updated.');
    }

    public function uploadPO(Request $request, $id)
    {
        $request->validate([
            'po_number'      => 'required|string|max:100',
            'po_date'        => 'nullable|date',
            'po_file'        => 'required|file|mimes:pdf|max:5120',
            'invoice_type'   => 'required|in:DP,CT',
            'dp_percent'     => 'nullable|numeric|min:1|max:99',
            'payment_method' => 'required|string|max:100',
        ]);

        $quote = UnitQuotation::findOrFail($id);

        if ($quote->has_multiple_options) {
            $message = 'Quotation ini masih punya lebih dari 1 opsi. Hapus opsi yang tidak dipilih customer dulu (lewat Edit) sebelum upload PO.';
            return $request->expectsJson()
                ? response()->json(['error' => $message], 422)
                : redirect()->back()->with('error', $message);
        }

        $client = $quote->client;
        if (!$client) {
            $message = 'Data client tidak ditemukan.';
            return $request->expectsJson()
                ? response()->json(['error' => $message], 422)
                : redirect()->back()->with('error', $message);
        }

        $npwpClean = preg_replace('/[^a-zA-Z0-9]/', '', $client->npwp ?? '');
        if (strlen($npwpClean) < 14) {
            $message = 'NPWP client belum diisi or kurang dari 14 karakter. Pengajuan PO tidak dapat diproses.';
            return $request->expectsJson()
                ? response()->json(['error' => $message], 422)
                : redirect()->route('unit-quotation.show', $id)->with('error', $message);
        }

        $year = now()->year;
        $path = $request->file('po_file')->store("unit-quotation/po/{$year}", 'public');

        $quote->update([
            'po_number'      => $request->po_number,
            'po_file'        => $path,
            'payment_method' => $request->payment_method,
            'status'         => 'po_received',
            'po_received'    => $request->po_date ?: now()->toDateString(),
            'type'           => 'Project',
        ]);

        $quote->statusHistory()->create([
            'status' => 'po_received',
            'note'   => 'PO No. ' . $request->po_number,
        ]);

        if ($client->id_issues != "5" || $client->role !== 'Customers') {
            $client->id_issues = '5';
            $client->role = 'Customers';
            $client->save();

            \App\Models\CrmStatus::create([
                'id_client' => $client->id,
                'status'    => 2,
            ]);
        }

        $pending = $this->createPendingPoForUnitQuotation($quote);
        $invoice = $this->createInvoiceRecords($quote, $request->invoice_type, $request->dp_percent);
        $this->notifyInvoiceRequested($quote, $invoice);

        if ($request->expectsJson()) {
            return response()->json([
                'success'     => 'PO berhasil diupload. Status diubah ke PO Received.',
                'po_file_url' => Storage::url($path),
                'quote'       => [
                    'no_quote'  => $quote->no_quote,
                    'po_number' => $quote->po_number,
                    'total'     => $quote->total,
                ],
                'pendingPo'   => $pending ? [
                    'id'                         => $pending->id,
                    'no_pending'                 => $pending->no_pending,
                    'title'                      => $pending->title,
                    'delivery'                   => $pending->delivery,
                    'combine_shipping_and_parts' => (bool) $pending->combine_shipping_and_parts,
                    'shipping_address_manual'    => $pending->shipping_address_manual,
                    'doc_address_manual'         => $pending->doc_address_manual,
                    'shipping_recipient_id'      => $pending->shipping_recipient_id,
                    'doc_recipient_id'           => $pending->doc_recipient_id,
                ] : null,
            ]);
        }

        // Fallback (non-AJAX): perilaku lama, modal dibuka via flash sekali pakai.
        $flashData = [
            'success' => 'PO berhasil diupload. Status diubah ke PO Received.',
            'open_convert_po' => true,
            'noPending' => $pending->no_pending ?? ($quote->po_number ?? $quote->no_quote),
            'ekspidisi' => $pending->delivery ?? $this->resolvePendingDeliveryValue($quote->delivery_process),
            'pending_id' => $pending->id ?? null,
        ];

        return redirect()->route('unit-quotation.show', $id)->with($flashData);
    }

    /**
     * Edit No PO dari sisi Sales — hanya boleh selama BELUM ada invoice yang
     * diterbitkan (no_invoice terisi). Setelah invoice terbit, perubahan No PO
     * dilakukan Accounting lewat halaman detail invoice (barengan Edit No
     * Invoice & Term).
     */
    public function updatePoNumber(Request $request, $id)
    {
        $request->validate([
            'po_number' => 'required|string|max:100',
        ]);

        $quote = UnitQuotation::findOrFail($id);

        if ($quote->status !== 'po_received' || !$quote->po_number) {
            return back()->with('error', 'PO belum diupload untuk quotation ini.');
        }

        if ($quote->cancel_request) {
            return back()->with('error', 'Ada pengajuan pembatalan PO yang masih diproses. Selesaikan dulu sebelum mengubah No PO.');
        }

        if (Invoice::where('id_unit_quotation', $quote->id)->whereNotNull('no_invoice')->exists()) {
            return back()->with('error', 'Invoice sudah diterbitkan. Perubahan No PO harus dilakukan lewat halaman Invoice (Accounting).');
        }

        $old = $quote->po_number;
        $new = trim($request->po_number);

        if ($old === $new) {
            return back()->with('success', 'No PO tidak berubah.');
        }

        $quote->update(['po_number' => $new]);

        // Sinkronkan ke shell invoice yang masih pending (belum diterbitkan).
        Invoice::where('id_unit_quotation', $quote->id)
            ->whereNull('no_invoice')
            ->update(['no_po' => $new]);

        $quote->statusHistory()->create([
            'status' => 'po_received',
            'note'   => 'No PO diubah: ' . ($old ?: '-') . ' -> ' . $new,
        ]);

        return redirect()->route('unit-quotation.show', $id)
            ->with('success', 'No PO berhasil diperbarui.');
    }

    public function requestNextInvoice(Request $request, $id)
    {
        $request->validate([
            'percent' => 'required|numeric|min:1|max:100',
            'label'   => 'required|string|max:50',
        ]);

        $quote = UnitQuotation::findOrFail($id);

        if (Invoice::where('id_unit_quotation', $quote->id)->whereNull('no_invoice')->whereNull('rejected_at')->exists()) {
            return back()->with('error', 'Masih ada invoice yang belum diterbitkan.');
        }

        $issuedPercent = Invoice::where('id_unit_quotation', $quote->id)
            ->whereNotNull('no_invoice')
            ->sum('percent');

        if ($issuedPercent >= 100) {
            return back()->with('error', 'Semua tagihan sudah 100% diterbitkan.');
        }

        $remainingPercent = 100 - $issuedPercent;
        $percentOfTotal   = round($remainingPercent * floatval($request->percent) / 100, 2);

        $invoice = Invoice::create([
            'id_unit_quotation' => $quote->id,
            'no_po'             => $quote->po_number,
            'flag'              => $this->invoiceFlagFor($quote),
            'pph'               => 0,
            'type'              => $request->label,
            'percent'           => $percentOfTotal,
        ]);
        $this->notifyInvoiceRequested($quote, $invoice);

        return redirect()->route('unit-quotation.show', $id)
            ->with('success', 'Invoice selanjutnya berhasil diajukan.');
    }

    public function destroy($id)
    {
        $quote = UnitQuotation::findOrFail($id);
        $quote->delete();
        return response()->json(1);
    }

    /**
     * Update / input Management Fee for Smart Quote.
     * Nominal Fee mengurangi pencatatan achievement / omset sales, namun
     * nominal penawaran/invoice customer tetap utuh normal (sebelum PPN).
     */
    public function updateFee(Request $request, $id)
    {
        $quote = UnitQuotation::with('details')->findOrFail($id);

        $request->validate([
            'fee'               => 'nullable',
            'fee_note'          => 'nullable|string|max:1000',
            'fee_bank_name'     => 'nullable|string|max:100',
            'fee_bank_account'  => 'nullable|string|max:100',
            'fee_bank_holder'   => 'nullable|string|max:150',
            'item_fee'          => 'nullable|array',
        ]);

        $rawFee = $request->fee;
        if (is_string($rawFee)) {
            $rawFee = (float) preg_replace('/[^\d]/', '', $rawFee);
        } else {
            $rawFee = (float) ($rawFee ?? 0);
        }

        // Simpan fee per-item jika ada input alokasi fee per item
        $totalItemFee = 0;
        if ($request->has('item_fee') && is_array($request->item_fee)) {
            foreach ($request->item_fee as $detailId => $itemFeeVal) {
                $detail = UnitQuotationDetail::where('id_unit_quotation', $quote->id)->where('id', $detailId)->first();
                if ($detail) {
                    $cleanedVal = is_string($itemFeeVal) ? (float) preg_replace('/[^\d]/', '', $itemFeeVal) : (float) ($itemFeeVal ?? 0);
                    $totalItemFee += $cleanedVal;
                }
            }
            $rawFee = $totalItemFee;
        }

        // Batas maksimal fee 10% dari nilai penawaran sebelum PPN
        $preTax = floatval($quote->subtotal ?? 0) - floatval($quote->diskon ?? 0);
        if ($preTax <= 0) {
            $preTax = floatval($quote->total ?? 0) - floatval($quote->tax_amount ?? 0);
        }
        $maxFeeAllowed = round($preTax * 0.10, 2);

        if ($rawFee > ($maxFeeAllowed + 1) && $maxFeeAllowed > 0) {
            $errMessage = 'Total Management Fee (Rp ' . number_format($rawFee, 0, ',', '.') . ') melebihi batas maksimal 10% dari nilai penawaran (Maks. Rp ' . number_format($maxFeeAllowed, 0, ',', '.') . ').';
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $errMessage,
                ], 422);
            }
            return redirect()->route('unit-quotation.show', $id)
                ->with('error', $errMessage);
        }

        // Simpan detail fee setelah lolos validasi
        if ($request->has('item_fee') && is_array($request->item_fee)) {
            foreach ($request->item_fee as $detailId => $itemFeeVal) {
                $detail = UnitQuotationDetail::where('id_unit_quotation', $quote->id)->where('id', $detailId)->first();
                if ($detail) {
                    $cleanedVal = is_string($itemFeeVal) ? (float) preg_replace('/[^\d]/', '', $itemFeeVal) : (float) ($itemFeeVal ?? 0);
                    $detail->fee = $cleanedVal;
                    $detail->save();
                }
            }
        }

        $quote->fee = $rawFee;
        $quote->fee_note = $request->fee_note;
        if ($request->has('fee_bank_name')) {
            $quote->fee_bank_name = $request->fee_bank_name;
        }
        if ($request->has('fee_bank_account')) {
            $quote->fee_bank_account = $request->fee_bank_account;
        }
        if ($request->has('fee_bank_holder')) {
            $quote->fee_bank_holder = $request->fee_bank_holder;
        }
        $quote->save();

        foreach ($quote->options as $opt) {
            $opt->fee = $rawFee;
            $opt->save();
        }

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Management Fee berhasil disimpan.',
                'fee'     => $quote->fee,
                'fee_note'=> $quote->fee_note,
            ]);
        }

        return redirect()->route('unit-quotation.show', $id)
            ->with('success', 'Management Fee berhasil diperbarui.');
    }

    /**
     * Delete / reset Management Fee for Smart Quote.
     */
    public function deleteFee(Request $request, $id)
    {
        $quote = UnitQuotation::with('details')->findOrFail($id);

        UnitQuotationDetail::where('id_unit_quotation', $quote->id)->update(['fee' => 0]);
        $quote->fee = 0;
        $quote->fee_note = null;
        $quote->save();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Management Fee berhasil dihapus.',
            ]);
        }

        return redirect()->route('unit-quotation.show', $id)
            ->with('success', 'Management Fee berhasil dihapus.');
    }

    public function addPayment(Request $request, $id)
    {
        $quote = UnitQuotation::findOrFail($id);

        $payment                    = new Payment();
        $payment->id_unit_quotation = $id;
        $payment->amount            = $request->amount;
        $payment->percent           = $request->percent;
        $payment->note              = $request->note;
        $payment->type              = $request->type;
        $payment->method            = $request->method;
        $isEscrow                   = ($request->method === 'Escrow');
        $payment->escrow_channel    = $isEscrow ? $request->escrow_channel : null;
        $payment->level             = $isEscrow ? 1 : 0;
        $payment->date              = now()->toDateString();
        if ($isEscrow) {
            $payment->date_confirm = now()->toDateString();
        }
        if ($request->type === 'Tempo') {
            $payment->tempo = $request->tempo;
        }
        $payment->save();

        $this->prService->evaluatePaymentGate($payment, Auth::id());

        $targetInvoice = null;

        if ($isEscrow) {
            // Mark any invoice already issued through the normal flow as paid.
            Invoice::where('id_unit_quotation', $id)
                ->whereNotNull('no_invoice')
                ->update(['status_p' => 1]);

            // Escrow payments bypass the Request Invoice → Accounting approval flow:
            // the invoice is issued immediately, using the quotation number as the
            // invoice number so it never consumes a slot in the normal invoice
            // numbering sequence.
            $targetInvoice = Invoice::create([
                'id_unit_quotation' => $id,
                'no_po'             => $quote->po_number,
                'flag'              => $this->invoiceFlagFor($quote),
                'pph'               => 0,
                'type'              => 'Escrow',
                'percent'           => $payment->percent,
                'no_invoice'        => $quote->no_quote,
                'date'              => $payment->date,
                'invoiceTo'         => '1',
                'status_p'          => 1,
            ]);

            // Tutup otomatis invoice shell yang masih pending (dibuat waktu PO
            // diproses, belum sempat di-nomori) — supaya tidak nyangkut minta
            // di-approve manual lewat halaman before.accept.unit padahal invoice
            // Escrow-nya (di atas) sudah terbit.
            Invoice::where('id_unit_quotation', $id)
                ->whereNull('no_invoice')
                ->whereNull('rejected_at')
                ->where('id', '!=', $targetInvoice->id)
                ->update([
                    'rejected_at'     => now(),
                    'rejected_reason' => 'Auto-closed: dibayar via Escrow',
                ]);
        } else {
            // Payment biasa dicatat terhadap invoice yang sudah diterbitkan paling akhir
            // (mis. DP/BP sebelumnya) — itu yang relevan buat Accounting cek/follow up.
            $targetInvoice = Invoice::where('id_unit_quotation', $id)
                ->whereNotNull('no_invoice')
                ->latest('id')
                ->first();
        }

        // Notifikasi Accounting: ada payment baru masuk, perlu di-follow up (mis. terbitkan invoice).
        $accountingUsers = User::where('role', 'Accounting')->where('active', '1')->get(['id']);
        foreach ($accountingUsers as $accUser) {
            \App\Models\UnitQuotationPaymentNotification::create([
                'id_payment' => $payment->id,
                'id_invoice' => $targetInvoice->id ?? null,
                'id_unit_quotation' => $id,
                'id_user' => $accUser->id,
                'type' => 'payment',
                'is_read' => false,
            ]);
        }

        return redirect()->route('unit-quotation.show', $id)->with('success', 'Payment berhasil ditambahkan.');
    }

    // Dipanggil polling navbar (role Accounting & Admin, plus Sales buat notifikasi
    // invoice yang sudah di-acc) supaya notifikasi payment baru, PO menunggu invoice,
    // dan invoice yang sudah terbit muncul tanpa reload halaman.
    public function unreadPaymentNotifications()
    {
        if (!in_array(Auth::user()->role, ['Accounting', 'Admin', 'Sales'])) {
            return response()->json(['count' => 0, 'items' => []]);
        }

        // Ambil notifikasi terbaru terlepas dari status baca — "tandai dibaca" cuma
        // menghilangkan penanda merahnya, bukan menghapusnya dari daftar. Badge merah
        // (count) tetap dihitung dari yang unread saja.
        $notifs = \App\Models\UnitQuotationPaymentNotification::where('id_user', Auth::id())
            ->with(['unitQuotation.client', 'unitQuotation.sales', 'payment', 'invoice'])
            ->orderByDesc('created_at')
            ->take(15)
            ->get();

        $items = $notifs->map(function ($n) {
            $quote = $n->unitQuotation;
            $inv = $n->invoice;
            $poUrl = null;
            if ($quote && !empty($quote->po_file)) {
                $poUrl = \Illuminate\Support\Facades\Storage::disk('public')->exists($quote->po_file)
                    ? \Illuminate\Support\Facades\Storage::url($quote->po_file)
                    : asset('storage/' . $quote->po_file);
            }

            return [
                'id' => $n->id,
                'type' => $n->type,
                'is_read' => (bool) $n->is_read,
                'no_quote' => $quote->no_quote ?? '-',
                'quote_id' => $quote->id ?? null,
                'company' => $quote->client->company ?? '-',
                'sales_name' => $quote->sales->name ?? null,
                'po_number' => $quote->po_number ?? null,
                'po_url' => $poUrl,
                'invoice_id' => $inv->id ?? null,
                'invoice_type' => $inv->type ?? null,
                'invoice_percent' => $inv->percent ?? null,
                'amount' => $n->type === 'payment'
                    ? (float) ($n->payment->amount ?? 0)
                    : (float) ($quote->total ?? 0) * (float) ($inv->percent ?? 100) / 100,
                'url' => $this->resolveInvoiceNotificationUrl($n),
                'quote_url' => $n->id_unit_quotation ? route('unit-quotation.show', $n->id_unit_quotation) : null,
                'created_at' => $n->created_at->diffForHumans(),
            ];
        });

        return response()->json(['count' => $notifs->where('is_read', false)->count(), 'items' => $items]);
    }

    public function markPaymentNotificationRead($id)
    {
        \App\Models\UnitQuotationPaymentNotification::where('id', $id)
            ->where('id_user', Auth::id())
            ->update(['is_read' => true]);

        return response()->json(['ok' => true]);
    }

    public function proofPayment(Request $request, $id)
    {
        $payment = Payment::findOrFail($id);
        $quote   = UnitQuotation::findOrFail($payment->id_unit_quotation);

        $request->validate(['file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120']);

        $foto       = $request->file('file');
        $ext        = $foto->getClientOriginalExtension();
        $safeName   = preg_replace('/[^A-Za-z0-9\-]/', '_', $quote->no_quote);
        $payCount   = Payment::where('id_unit_quotation', $quote->id)->count();
        $fileName   = $safeName . '-' . $payCount . '.' . $ext;
        $subDir     = 'asset/payment/' . now()->format('Y/m');
        $uploadPath = public_path($subDir);
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }
        $foto->move($uploadPath, $fileName);

        $payment->file = $subDir . '/' . $fileName;
        $payment->save();

        return response()->json([
            'success'    => true,
            'file_url'   => asset($payment->file),
            'payment_id' => $payment->id,
        ]);
    }

    public function deleteProof($id)
    {
        $payment = Payment::findOrFail($id);

        if ($payment->file && file_exists(public_path($payment->file))) {
            unlink(public_path($payment->file));
        }

        $payment->file = null;
        $payment->save();

        return response()->json(['success' => true]);
    }

    public function updatePayment(Request $request, $id)
    {
        $payment = Payment::findOrFail($id);

        if ($payment->level == 1) {
            return response()->json(['error' => 'Payment sudah dikonfirmasi Accounting, tidak bisa diubah lagi.'], 422);
        }

        $payment->amount  = $request->amount;
        $payment->percent = $request->percent;
        $payment->note    = $request->note;
        $payment->type    = $request->type;
        $payment->method  = $request->method;
        $payment->tempo   = $request->type === 'Tempo' ? $request->tempo : null;
        $payment->save();

        return response()->json(1);
    }

    public function deletePayment($id)
    {
        $payment = Payment::findOrFail($id);

        if ($payment->level == 1) {
            return response()->json(['error' => 'Payment sudah dikonfirmasi Accounting, tidak bisa dihapus.'], 422);
        }

        $payment->delete();
        return response()->json(1);
    }

    public function toggleHideTitle(Request $request, $id)
    {
        $quote = UnitQuotation::findOrFail($id);
        $quote->hide_title = !$quote->hide_title;
        $quote->save();

        return back()->with('success', $quote->hide_title ? 'Title disembunyikan di halaman print.' : 'Title ditampilkan di halaman print.');
    }

    public function cancelPO(Request $request, $id)
    {
        $quote = UnitQuotation::findOrFail($id);

        $hasIssuedInvoice = Invoice::where('id_unit_quotation', $id)
            ->whereNotNull('no_invoice')
            ->exists();

        if ($hasIssuedInvoice) {
            // Needs Accounting approval
            $quote->cancel_request = 1;
            $quote->save();
            return redirect()->route('unit-quotation.show', $id)
                ->with('info', 'Permintaan cancel PO dikirim ke Accounting untuk persetujuan.');
        }

        // No issued invoice yet — cancel directly
        $this->performCancelPO($quote);

        $quote->statusHistory()->create([
            'status' => 'negotiation',
            'note'   => 'PO dibatalkan oleh ' . Auth::user()->name,
        ]);

        return redirect()->route('unit-quotation.show', $id)
            ->with('success', 'PO berhasil dibatalkan.');
    }

    public function approveCancelPO($id)
    {
        $quote = UnitQuotation::findOrFail($id);
        $this->performCancelPO($quote);

        $quote->statusHistory()->create([
            'status' => 'negotiation',
            'note'   => 'Cancel PO disetujui Accounting oleh ' . Auth::user()->name,
        ]);

        return redirect()->route('unit-quotation.show', $id)
            ->with('success', 'Cancel PO disetujui. Status kembali ke Negotiation.');
    }

    private function performCancelPO(UnitQuotation $quote): void
    {
        // 1. Hapus file PO dari storage jika ada
        if ($quote->po_file) {
            if (Storage::disk('public')->exists($quote->po_file)) {
                Storage::disk('public')->delete($quote->po_file);
            }
            if (File::exists(public_path('storage/' . $quote->po_file))) {
                File::delete(public_path('storage/' . $quote->po_file));
            }
        }

        // 2. Hapus data Payment & file bukti transfer jika ada
        $payments = Payment::where('id_unit_quotation', $quote->id)->get();
        foreach ($payments as $payment) {
            if ($payment->file && File::exists(public_path($payment->file))) {
                File::delete(public_path($payment->file));
            }
            if ($payment->file && Storage::disk('public')->exists($payment->file)) {
                Storage::disk('public')->delete($payment->file);
            }
            $payment->delete();
        }

        // 3. Hapus semua data Invoice terkait
        Invoice::where('id_unit_quotation', $quote->id)->delete();

        // 4. Hapus Selling Contract terkait jika ada
        Contract::where('id_unit_quotation', $quote->id)->delete();

        // 5. Reset Sales Order (Pending PO) kalau belum diproses logistik sama sekali
        // (status masih 0 = draft/baru). Kalau sudah diproses (status > 0), jangan
        // disentuh — menghapusnya bisa merusak data fulfillment yang sudah nyata terjadi.
        // Dibuat ulang otomatis dari qty quotation terbaru saat PO di-upload lagi
        // (lihat createPendingPoForUnitQuotation()).
        $pending = PendingPO::where('id_unit_quotation', $quote->id)->first();
        if ($pending && (int) $pending->status === 0) {
            $details = DetailPendingPO::where('id_pending', $pending->id)->get();
            foreach ($details as $detail) {
                if ($detail->id_equivalent) {
                    $sp = $detail->equivalent;
                    $product = $sp ? $sp->product : null;
                    if ($product) {
                        $product->stock += $detail->bdg;
                        $product->warehouse_stock += $detail->bks;
                        $product->pending_stock -= ($detail->bdg + $detail->bks);
                        $product->save();
                    }
                }
                $detail->delete();
            }

            PurchaseRequest::where('id_pending', $pending->id)->delete();
            $pending->delete();
        }

        // 6. Reset data PO & status kembali ke Negotiation
        $quote->status         = 'negotiation';
        $quote->cancel_request = 0;
        $quote->po_number      = null;
        $quote->po_file        = null;
        $quote->po_received    = null;
        $quote->save();
    }

    public function rejectCancelPO($id)
    {
        $quote = UnitQuotation::findOrFail($id);
        $quote->cancel_request = 0;
        $quote->save();

        return redirect()->route('unit-quotation.show', $id)
            ->with('warning', 'Permintaan cancel PO ditolak.');
    }

    /**
     * Update detail Sales Order (PendingPO) untuk Unit Quotation.
     * PendingPO sudah dibuat saat uploadPO(); method ini mengupdate
     * field alamat, ekspedisi, dan penerima yang tidak bisa diisi saat upload.
     */
    public function updatePendingPo(Request $request, $id)
    {
        $request->validate([
            'note' => 'nullable|string|max:1000',
        ]);

        $quote   = UnitQuotation::findOrFail($id);
        $pending = PendingPO::where('id_unit_quotation', $id)->first();

        if (!$pending) {
            return redirect()->route('unit-quotation.show', $id)
                ->with('error', 'Sales Order belum dibuat. Upload PO terlebih dahulu.');
        }

        // Update field dasar
        if ($request->filled('no_pending'))  $pending->no_pending = $request->no_pending;
        if ($request->filled('title'))       $pending->title      = $request->title;
        if ($request->filled('ekspidisi'))   $pending->delivery   = (int) $request->ekspidisi;
        if ($request->filled('type'))        $pending->type       = $request->type;
        if ($pending->type === 'Project') {
            $pending->project_category    = $pending->project_category ?? 'Unit';
            $pending->project_status_step = $pending->project_status_step ?? 1;
        }

        // Update alamat pengiriman
        $combine = $request->has('combine_shipping_and_parts') || $request->combine_shipping_and_parts == 1;
        $pending->combine_shipping_and_parts = $combine;

        $shipType   = $request->input('shipping_address_type', 'customer');
        $shipManual = $shipType === 'manual' ? $request->input('shipping_address_manual') : ($shipType !== 'customer' ? $shipType : null);

        $pending->shipping_address_type   = ($shipType === 'customer') ? 'customer' : 'manual';
        $pending->shipping_address_manual = $shipManual;

        if ($combine) {
            $pending->doc_address_type   = $pending->shipping_address_type;
            $pending->doc_address_manual = $pending->shipping_address_manual;
            $pending->charged            = $request->input('charged');
            $pending->doc_charged        = null;
            $pending->shipping_charged   = null;
            $pending->doc_recipient_id      = $request->input('shipping_recipient_id');
            $pending->shipping_recipient_id = $request->input('shipping_recipient_id');
        } else {
            $docType   = $request->input('doc_address_type', 'customer');
            $docManual = $docType === 'manual' ? $request->input('doc_address_manual') : ($docType !== 'customer' ? $docType : null);

            $pending->doc_address_type      = ($docType === 'customer') ? 'customer' : 'manual';
            $pending->doc_address_manual    = $docManual;
            $pending->charged               = null;
            $pending->doc_charged           = $request->input('doc_charged');
            $pending->shipping_charged      = $request->input('shipping_charged');
            $pending->doc_recipient_id      = $request->input('doc_recipient_id');
            $pending->shipping_recipient_id = $request->input('shipping_recipient_id');
        }

        $pending->save();

        return redirect()->route('unit-quotation.show', $id)
            ->with('success', 'Detail Sales Order berhasil diperbarui.');
    }

    // Tujuan klik notifikasi: 'invoice_requested' (menunggu diterbitkan) ke halaman
    // terbitkan invoice, 'payment' (invoice sudah terbit, tinggal di-follow up) ke
    // halaman detail invoice-nya. Fallback ke detail quotation kalau invoice-nya
    // tidak ada/sudah terhapus (mis. quote-nya sempat di-cancel).
    private function resolveInvoiceNotificationUrl(\App\Models\UnitQuotationPaymentNotification $n): string
    {
        if ($n->id_invoice) {
            if ($n->type === 'invoice_requested') {
                return route('before.accept.unit', $n->id_invoice);
            }
            if ($n->type === 'payment' || $n->type === 'invoice_approved') {
                return route('invoice.show_unit', $n->id_invoice);
            }
        }

        return route('unit-quotation.show', $n->id_unit_quotation);
    }

    private function createInvoiceRecords(UnitQuotation $quote, string $invoiceType = 'CT', $dpPercent = null): Invoice
    {
        return Invoice::create([
            'id_unit_quotation' => $quote->id,
            'no_po'             => $quote->po_number,
            'flag'              => $this->invoiceFlagFor($quote),
            'pph'               => 0,
            'type'              => $invoiceType,
            'percent'           => $invoiceType === 'DP' ? floatval($dpPercent ?? 50) : 100,
        ]);
    }

    // Entitas penerbit invoice mengikuti client-nya (Reftech / Kojisha) supaya
    // invoice, surat jalan, & label pengiriman konsisten.
    private function invoiceFlagFor(UnitQuotation $quote): string
    {
        return optional($quote->client)->info === 'Kojisha' ? 'Kojisha' : 'Reftech';
    }

    // Notifikasi Accounting & Admin: ada invoice yang menunggu diterbitkan (muncul di
    // Invoice > tab Request), dipanggil setiap kali invoice baru dibuat lewat Upload PO
    // maupun "Ajukan Invoice Selanjutnya" — supaya tidak perlu bolak-balik cek tab itu manual.
    private function notifyInvoiceRequested(UnitQuotation $quote, Invoice $invoice): void
    {
        $notifyUsers = User::whereIn('role', ['Accounting', 'Admin'])->where('active', '1')->get(['id']);
        foreach ($notifyUsers as $notifyUser) {
            \App\Models\UnitQuotationPaymentNotification::create([
                'id_invoice' => $invoice->id,
                'id_unit_quotation' => $quote->id,
                'id_user' => $notifyUser->id,
                'type' => 'invoice_requested',
                'is_read' => false,
            ]);
        }
    }

    private function saveDetails(int $quoteId, array $items, ?int $optionId = null): void
    {
        $sortOrder = 0;
        foreach ($items as $item) {
            $isHeader = (($item['type'] ?? '') === 'header' || ($item['type'] ?? '') === 'heading');
            $rawPrice = $item['price'] ?? 0;
            if (is_string($rawPrice) && str_contains($rawPrice, '.')) {
                $rawPrice = preg_replace('/[^\d]/', '', $rawPrice);
            }
            $qty      = $isHeader ? 0 : floatval($item['qty']   ?? 1);
            $price    = $isHeader ? 0 : floatval($rawPrice);
            $disc     = $isHeader ? 0 : floatval($item['disc']  ?? 0);
            $amount   = $isHeader ? 0 : ($qty * $price * (1 - $disc / 100));

            UnitQuotationDetail::create([
                'id_unit_quotation' => $quoteId,
                'id_option'         => $optionId,
                'type'              => $item['type'],
                'id_unit'           => $item['id_unit'] ?? null,
                'id_fixed_asset'    => $item['id_fixed_asset'] ?? null,
                'id_equivalent'     => $item['id_equivalent'] ?? null,
                'spec_visible'      => ($item['type'] === 'unit') ? ($item['spec_visible'] ?? null) : null,
                'label'             => $item['label'] ?? null,
                'description'       => $item['description'] ?? null,
                'qty'               => $qty,
                'info_qty'          => $isHeader ? null : ($item['info_qty'] ?? null),
                'price'             => $price,
                'disc'              => $disc,
                'amount'            => $amount,
                'sort_order'        => $sortOrder++,
            ]);
        }
    }

    /**
     * Hitung subtotal/diskon/tax/shipping/total per opsi dari input mentah
     * request "options[]" (dari form create/edit Smart Quote).
     */
    private function processOptionsInput(array $optionsInput): array
    {
        $result = [];
        foreach (array_values($optionsInput) as $i => $opt) {
            $items    = $opt['items'] ?? [];
            $subtotal = 0;
            foreach ($items as $item) {
                if (in_array($item['type'] ?? '', ['header', 'heading'], true)) {
                    continue;
                }
                $rawPrice = $item['price'] ?? 0;
                if (is_string($rawPrice) && str_contains($rawPrice, '.')) {
                    // Thousand separators (e.g. 1.500.000)
                    $rawPrice = preg_replace('/[^\d]/', '', $rawPrice);
                }
                $qty   = floatval($item['qty']   ?? 1);
                $price = floatval($rawPrice);
                $disc  = floatval($item['disc']  ?? 0);
                $subtotal += $qty * $price * (1 - $disc / 100);
            }

            $diskonType  = ($opt['diskon_type'] ?? 'percent') === 'amount' ? 'amount' : 'percent';
            $rawDiskon   = $opt['diskon'] ?? 0;
            if ($diskonType === 'amount' && is_string($rawDiskon) && str_contains($rawDiskon, '.')) {
                $rawDiskon = preg_replace('/[^\d]/', '', $rawDiskon);
            }
            $diskon      = floatval($rawDiskon);
            $afterDiskon = $diskonType === 'amount' ? ($subtotal - $diskon) : ($subtotal - ($subtotal * $diskon / 100));
            $tax         = filter_var($opt['tax'] ?? false, FILTER_VALIDATE_BOOLEAN);
            $taxAmount   = $tax ? round($afterDiskon * 0.11) : 0;
            $rawShipping = $opt['shipping'] ?? 0;
            if (is_string($rawShipping)) {
                $rawShipping = preg_replace('/[^\d]/', '', $rawShipping);
            }
            $shipping    = floatval($rawShipping);
            $total       = $afterDiskon + $taxAmount + $shipping;

            $result[] = [
                'title'       => trim($opt['title'] ?? '') ?: ('Opsi ' . ($i + 1)),
                'items'       => $items,
                'subtotal'    => $subtotal,
                'diskon'      => $diskon,
                'diskon_type' => $diskonType,
                'tax'         => $tax,
                'tax_amount'  => $taxAmount,
                'shipping'    => $shipping,
                'total'       => $total,
            ];
        }

        return $result;
    }

    private function emptyOptionTotals(): array
    {
        return [
            'subtotal' => 0, 'diskon' => 0, 'diskon_type' => 'percent',
            'tax' => false, 'tax_amount' => 0, 'shipping' => 0, 'total' => 0,
        ];
    }

    /**
     * Simpan opsi-opsi (hasil processOptionsInput) + detail item masing-masing.
     * Kolom subtotal/diskon/dst di unit_quotation sendiri (parent) dibiarkan
     * mengikuti opsi pertama — supaya semua fitur lain yang baca $quote->total
     * langsung (PO, Invoice, Contract, Delivery) tetap jalan tanpa perlu tahu
     * soal opsi, asalkan quotation-nya cuma py 1 opsi saat itu dieksekusi.
     */
    private function saveOptions(int $quoteId, array $processedOptions): void
    {
        $sortOrder = 0;
        foreach ($processedOptions as $opt) {
            $option = UnitQuotationOption::create([
                'id_unit_quotation' => $quoteId,
                'title'             => $opt['title'],
                'sort_order'        => $sortOrder++,
                'subtotal'          => $opt['subtotal'],
                'diskon'            => $opt['diskon'],
                'diskon_type'       => $opt['diskon_type'],
                'tax'               => $opt['tax'],
                'tax_amount'        => $opt['tax_amount'],
                'shipping'          => $opt['shipping'],
                'total'             => $opt['total'],
            ]);

            $this->saveDetails($quoteId, $opt['items'], $option->id);
        }
    }

    protected function createPendingPoForUnitQuotation(UnitQuotation $quote): PendingPO
    {
        $existing = PendingPO::where('id_unit_quotation', $quote->id)->first();
        if ($existing) {
            return $existing;
        }

        $pending = new PendingPO();
        $pending->status = 0;
        $pending->id_quotation = null;
        $pending->id_unit_quotation = $quote->id;
        $pending->type = ($quote->type === 'Project' || $quote->type === 'Service') ? 'Project' : 'Non Project';
        if ($pending->type === 'Project') {
            $pending->project_category = 'Unit';
            $pending->project_status_step = 1;
        }
        $pending->title = $quote->title ?: 'Unit Project';
        $pending->no_pending = $this->generateNoSo();
        $pending->delivery = $this->resolvePendingDeliveryValue($quote->delivery_process);
        $pending->combine_shipping_and_parts = false;
        $pending->doc_address_type = 'customer';
        $pending->shipping_address_type = 'customer';
        $pending->doc_address_manual = null;
        $pending->shipping_address_manual = null;
        $pending->doc_recipient_id = null;
        $pending->shipping_recipient_id = null;
        $pending->date = Carbon::now();
        $pending->save();

        // Kalau quotation ini sudah nempel ke kartu Kanban (di-post/di-link sebelum jadi
        // PO), isi pending_po_id kartunya + tarik biaya kartu ke PO ini biar ke-rollup
        // di Project Monitoring.
        $linkedTasks = KanbanTask::where('id_unit_quotation', $quote->id)->whereNull('pending_po_id')->get();
        foreach ($linkedTasks as $linkedTask) {
            $linkedTask->pending_po_id = $pending->id;
            $linkedTask->save();
            \App\Models\ProjectExpense::where('id_kanban_task', $linkedTask->id)
                ->whereNull('id_pending')
                ->update(['id_pending' => $pending->id]);
        }

        // Create detail rows and apply stock allocation logic similar to QuotationController::convert_po
        foreach ($quote->details as $item) {
            // skip header rows
            if (($item->type ?? '') === 'header' || ($item->type ?? '') === 'heading') {
                continue;
            }

            // Only create DetailPendingPO for items that reference an equivalent (serial_product)
            if (empty($item->id_equivalent) || $item->id_equivalent == '0') {
                // For unit-type items without an equivalent, create a minimal detail row
                $dPending = new \App\Models\DetailPendingPO();
                $dPending->id_pending = $pending->id;
                $dPending->id_equivalent = null;
                $dPending->bdg = 0;
                $dPending->bks = 0;
                $dPending->status = 0;
                $dPending->note = $item->label ?: ($item->description ?? '');
                $dPending->save();
                continue;
            }

            // Find corresponding product record via serial_product
            $product = \App\Models\Product::join('serial_product as sp', 'sp.id_product', '=', 'product.id')
                ->where('sp.id', $item->id_equivalent)
                ->select('product.*')
                ->first();

            $bdgStock = $product->stock ?? 0;
            $bksStock = $product->warehouse_stock ?? 0;
            $totalStock = $bdgStock + $bksStock;

            $bdgAlloc = 0;
            $bksAlloc = 0;

            if ($totalStock >= $item->qty) {
                // Ready stock
                if ($bdgStock >= $item->qty) {
                    $bdgAlloc = $item->qty;
                    $bksAlloc = 0;
                } else {
                    $bdgAlloc = $bdgStock;
                    $bksAlloc = $item->qty - $bdgStock;
                }
                $status = 2; // Ready Stock
                $note = 'Auto Allocated & Reserved (Ready Stock)';
            } else {
                // Not enough stock
                $status = 3; // Kurang
                $bdgAlloc = $bdgStock;
                $bksAlloc = $bksStock;
                $note = 'Auto Allocated & Reserved (Kurang). Kept available stock: BDG ' . $bdgAlloc . ', BKS ' . $bksAlloc;
                // PR untuk kekurangan ini baru dibuat setelah DP dikonfirmasi,
                // lihat pemanggilan generateShortfallForUnitPending() di bawah.
                $missingQty = $item->qty - $totalStock;
            }

            if ($product) {
                // Update product stock/pending_stock
                $product->stock = max(0, ($product->stock ?? 0) - $bdgAlloc);
                $product->warehouse_stock = max(0, ($product->warehouse_stock ?? 0) - $bksAlloc);
                $product->pending_stock = ($product->pending_stock ?? 0) + ($bdgAlloc + $bksAlloc);
                $product->save();
            }

            $dPending = new \App\Models\DetailPendingPO();
            $dPending->id_pending = $pending->id;
            $dPending->id_equivalent = $item->id_equivalent;
            $dPending->bdg = $bdgAlloc;
            $dPending->bks = $bksAlloc;
            $dPending->status = $status ?? 0;
            $dPending->note = $note ?? '';
            $dPending->pr_qty_needed = $missingQty ?? null;
            $dPending->save();

            // reset per-item so a later "Ready Stock" item doesn't inherit a stale value
            $missingQty = null;
        }

        if ($this->prService->paymentGateSatisfied($pending)) {
            $this->prService->generateShortfallForUnitPending($pending, Auth::id() ?? $quote->id_sales);
        }

        // Create initial change status (Pending Created) similar to QuotationController
        try {
            $changeStatusClass = \App\Models\ChangeStatus::class;
            if (class_exists($changeStatusClass)) {
                $status = new $changeStatusClass();
                $status->id_pending = $pending->id;
                $status->note = 'Pending Created';
                $status->status = 0;
                $status->date = Carbon::now();
                $status->save();
            }
        } catch (\Exception $e) {
            // Non-fatal: continue
        }

        return $pending;
    }

    /**
     * Format: 001-SO/RJO/VIII/2026 — nomor urut reset tiap bulan, mengikuti pola
     * penomoran PR (lihat PurchaseRequestService::generateNoPr()).
     */
    protected function generateNoSo(): string
    {
        $year = now()->format('Y');
        $romanMonths = ['I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];
        $roman = $romanMonths[(int) now()->format('n') - 1];
        $suffix = "-SO/RJO/{$roman}/{$year}";

        $last = PendingPO::where('no_pending', 'like', '%' . $suffix)
            ->orderByDesc('no_pending')
            ->value('no_pending');

        $lastSeq = $last ? (int) substr($last, 0, 3) : 0;
        $nextSeq = str_pad($lastSeq + 1, 3, '0', STR_PAD_LEFT);

        return $nextSeq . $suffix;
    }

    protected function resolvePendingDeliveryValue($deliveryValue): int
    {
        if (is_int($deliveryValue) || ctype_digit((string) $deliveryValue)) {
            return (int) $deliveryValue;
        }

        $normalized = strtolower(trim((string) $deliveryValue));
        $map = [
            '1' => 1,
            'jne' => 1,
            'j&t' => 1,
            'jnt' => 1,
            'cargo' => 1,
            '2' => 2,
            'send by technician' => 2,
            'technician' => 2,
            'technian' => 2,
            '3' => 3,
            'taken directly' => 3,
            'taken' => 3,
            'directly' => 3,
            '4' => 4,
            'other' => 4,
            'others' => 4,
            'customer' => 4,
            'ready stock' => 4,
            'ready-stock' => 4,
            'ready stock' => 4,
        ];

        return $map[$normalized] ?? 4;
    }


    private function getTypePrefix(?string $type): string
    {
        return match ($type) {
            'Unit'                  => 'U',
            'Rental'                => 'R',
            'Project'               => 'PR',
            'Parts'                 => 'P',
            'Service'               => 'S',
            'Piping'                => 'PIP',
            'Air Audit'             => 'AA',
            'General Check / Visit' => 'GC',
            'HVAC'                  => 'HVAC',
            'Fire System'           => 'FS',
            default                 => 'PU',
        };
    }

    private function getNextSequenceNumber(): int
    {
        $dateNow  = Carbon::now();
        $salesId  = Auth::id();
        $userCode = Auth::user()->code ?? Auth::user()->name;

        // Unit Quotation adalah format yang dipakai sekarang & ke depannya — nomor urut per sales
        // basenya HANYA dari tabel unit_quotation ini, bukan lagi tabel Quotation lama. Riwayat
        // legacy Quotation (termasuk data lama yang no_quote-nya berantakan/salah ketik) sudah
        // tidak relevan sebagai acuan nomor quotation baru.
        $unitQuotes = UnitQuotation::whereYear('created_at', $dateNow)
            ->where('id_sales', $salesId)
            ->pluck('no_quote');

        $maxSeq = 0;

        foreach ($unitQuotes as $noQuote) {
            if (!$noQuote || !preg_match('/^(\d+)-/i', $noQuote, $matches)) {
                continue;
            }

            // Nomor lama hasil revisi/duplikasi quotation tahun lalu (tahun di teks nomor beda
            // dari tahun berjalan) atau hasil reassign dari sales lain (kode di teks nomor beda
            // dari kode sales ini) diabaikan — supaya nggak "meracuni" nomor urut sales ini terus
            // menerus ke depan walau `created_at` / `id_sales` sudah keburu ikut yang baru.
            if (!$this->noQuoteBelongsToCurrentTrack($noQuote, $dateNow->year, $userCode)) {
                continue;
            }

            $seq = (int) $matches[1];
            if ($seq > $maxSeq) {
                $maxSeq = $seq;
            }
        }

        if ($maxSeq === 0) {
            $maxSeq = UnitQuotation::whereYear('created_at', $dateNow)->where('id_sales', $salesId)->count();
        }

        return $maxSeq + 1;
    }

    private function noQuoteBelongsToCurrentTrack(string $noQuote, int $year, string $userCode): bool
    {
        // Ambil token 4-digit terakhir sebagai tahun (toleran terhadap suffix seperti " (REV-01)").
        if (!preg_match_all('/\/(\d{4})/', $noQuote, $yearMatches) || (int) end($yearMatches[1]) !== $year) {
            return false;
        }

        // Kode sales harus muncul persis setelah "RJO-" atau "RJO/", diapit batas kata.
        $codePattern = preg_quote($userCode, '#');
        if (!preg_match('#RJO[-/]' . $codePattern . '(?=[/-]|$)#i', $noQuote)) {
            return false;
        }

        return true;
    }

    private function generateNoQuote(?string $type = null): string
    {
        $dateNow  = Carbon::now();
        $month    = $this->convertToRoman($dateNow->month);
        $userCode = Auth::user()->code ?? Auth::user()->name;
        $nextSeq  = $this->getNextSequenceNumber();
        $counter  = str_pad($nextSeq, 3, '0', STR_PAD_LEFT);
        $prefix   = $this->getTypePrefix($type);
        return $counter . '-' . $prefix . '/BDG/RJO-' . $userCode . '/' . $month . '/' . $dateNow->year;
    }

    private function convertToRoman(int $month): string
    {
        $roman = [1=>'I',2=>'II',3=>'III',4=>'IV',5=>'V',6=>'VI',
                  7=>'VII',8=>'VIII',9=>'IX',10=>'X',11=>'XI',12=>'XII'];
        return $roman[$month];
    }
}
