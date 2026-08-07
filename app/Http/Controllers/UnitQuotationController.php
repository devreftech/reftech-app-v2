<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Contract;
use App\Models\Delivery;
use App\Models\DetailDelivery;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\PendingPO;
use App\Models\Pic;
use App\Models\Unit;
use App\Models\UnitQuotation;
use App\Models\UnitQuotationDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class UnitQuotationController extends Controller
{
    public function index()
    {
        return view('pages.unit-quotation.index');
    }

    public function create()
    {
        $defaultNoQuote = $this->generateNoQuote();

        $clients = Client::where('id_sales', Auth::id())->orderBy('company')->get();
        $paymentTemplates = \App\Models\SalesPaymentTemplate::with('client')
            ->where('id_sales', Auth::id())
            ->orderBy('is_default', 'desc')
            ->orderBy('name')
            ->get();

        return view('pages.unit-quotation.create', compact('clients', 'defaultNoQuote', 'paymentTemplates'));
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
        $subtotal = 0;
        $items    = $request->input('items', []);

        foreach ($items as $item) {
            if (($item['type'] ?? '') === 'header' || ($item['type'] ?? '') === 'heading') {
                continue;
            }
            $qty    = floatval($item['qty']   ?? 1);
            $price  = floatval($item['price'] ?? 0);
            $disc   = floatval($item['disc']  ?? 0);
            $subtotal += $qty * $price * (1 - $disc / 100);
        }

        $diskonType  = $request->diskon_type === 'amount' ? 'amount' : 'percent';
        $diskon      = floatval($request->diskon ?? 0);
        $afterDiskon = $diskonType === 'amount' ? ($subtotal - $diskon) : ($subtotal - ($subtotal * $diskon / 100));
        $tax         = $request->boolean('tax');
        $tax_amount  = $tax ? round($afterDiskon * 0.11) : 0;
        $shipping    = floatval(str_replace('.', '', $request->shipping ?? 0));
        $total       = $afterDiskon + $tax_amount + $shipping;

        $client = $request->id_client ? Client::find($request->id_client) : null;

        $quote = UnitQuotation::create([
            'id_client'        => $request->id_client ?: null,
            'id_pic'           => $request->id_pic ?: null,
            'id_plant'         => $request->id_plant ?: null,
            'address'          => $request->address ?: null,
            'id_sales'         => Auth::id(),
            'id_support'       => $client->id_support ?? null,
            'no_quote'         => $request->no_quote ?: $this->generateNoQuote($request->type),
            'attn'             => $request->attn,
            'no_pr'            => $request->no_pr ?: null,
            'date'             => $request->date,
            'expired_date'     => $request->expired_date ?: \Carbon\Carbon::parse($request->date)->addMonth()->format('Y-m-d'),
            'title'            => $request->title,
            'type'             => $request->type,
            'week'             => $request->week,
            'subtotal'         => $subtotal,
            'diskon'           => $diskon,
            'diskon_type'      => $diskonType,
            'tax'              => $tax,
            'tax_amount'       => $tax_amount,
            'shipping'         => $shipping,
            'total'            => $total,
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

        $this->saveDetails($quote->id, $items);

        $quote->statusHistory()->create(['status' => 'draft', 'note' => null]);

        return redirect()->route('unit-quotation.show', $quote->id)
            ->with('success', 'Quotation created successfully.');
    }

    public function show($id)
    {
        $quote       = UnitQuotation::with(['client', 'pic', 'plant', 'sales', 'details.unit', 'details.equivalent.product', 'statusHistory', 'comments.user'])->findOrFail($id);
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

        $payments  = Payment::where('id_unit_quotation', $quote->id)->orderBy('id')->get();
        $pendingPo = PendingPO::where('id_unit_quotation', $quote->id)->first();

        return view('pages.unit-quotation.detail', compact('quote', 'allVersions', 'invoices', 'contracts', 'payments', 'thisYear', 'formattedNumberSC', 'pendingPo'));
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
        $quote   = UnitQuotation::with(['client', 'pic', 'plant', 'details.unit', 'details.fixedAsset', 'details.equivalent.product'])->findOrFail($id);
        $clients = Client::orderBy('company')->get();

        $editItems = $quote->details->map(function ($d) {
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
        })->values();

        return view('pages.unit-quotation.edit', compact('quote', 'clients', 'editItems'));
    }

    public function update(Request $request, $id)
    {
        $quote    = UnitQuotation::findOrFail($id);
        $subtotal = 0;
        $items    = $request->input('items', []);

        foreach ($items as $item) {
            if (($item['type'] ?? '') === 'header' || ($item['type'] ?? '') === 'heading') {
                continue;
            }
            $qty    = floatval($item['qty']   ?? 1);
            $price  = floatval($item['price'] ?? 0);
            $disc   = floatval($item['disc']  ?? 0);
            $subtotal += $qty * $price * (1 - $disc / 100);
        }

        $diskonType  = $request->diskon_type === 'amount' ? 'amount' : 'percent';
        $diskon      = floatval($request->diskon ?? 0);
        $afterDiskon = $diskonType === 'amount' ? ($subtotal - $diskon) : ($subtotal - ($subtotal * $diskon / 100));
        $tax         = $request->boolean('tax');
        $tax_amount  = $tax ? round($afterDiskon * 0.11) : 0;
        $shipping    = floatval(str_replace('.', '', $request->shipping ?? 0));
        $total       = $afterDiskon + $tax_amount + $shipping;

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
            'week'             => $request->week,
            'subtotal'         => $subtotal,
            'diskon'           => $diskon,
            'diskon_type'      => $diskonType,
            'tax'              => $tax,
            'tax_amount'       => $tax_amount,
            'shipping'         => $shipping,
            'total'            => $total,
            'note'             => $request->note,
            'validity'         => $request->validity,
            'pricing'          => $request->pricing,
            'warranty'         => $request->warranty,
            'delivery_process' => $request->delivery_process,
            'payment'          => $request->payment,
        ]);

        $quote->details()->delete();
        $this->saveDetails($quote->id, $items);

        return redirect()->route('unit-quotation.show', $quote->id)
            ->with('success', 'Quotation updated successfully.');
    }

    public function revise($id)
    {
        $source = UnitQuotation::with('details')->findOrFail($id);

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
            'week'             => $source->week,
            'subtotal'         => $source->subtotal,
            'diskon'           => $source->diskon,
            'diskon_type'      => $source->diskon_type,
            'tax'              => $source->tax,
            'tax_amount'       => $source->tax_amount,
            'total'            => $source->total,
            'note'             => $source->note,
            'validity'         => $source->validity,
            'pricing'          => $source->pricing,
            'warranty'         => $source->warranty,
            'delivery_process' => $source->delivery_process,
            'payment'          => $source->payment,
            'status'           => 'revision',
        ]);

        foreach ($source->details as $d) {
            UnitQuotationDetail::create([
                'id_unit_quotation' => $newQuote->id,
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
        $delivery->date              = $request->date ?? Carbon::today()->toDateString();
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
            'po_file'        => 'required|file|mimes:pdf|max:5120',
            'invoice_type'   => 'required|in:DP,CT',
            'dp_percent'     => 'nullable|numeric|min:1|max:99',
            'payment_method' => 'required|string|max:100',
        ]);

        $quote = UnitQuotation::findOrFail($id);

        $client = $quote->client;
        if (!$client) {
            return redirect()->back()->with('error', 'Data client tidak ditemukan.');
        }

        $npwpClean = preg_replace('/[^a-zA-Z0-9]/', '', $client->npwp ?? '');
        if (strlen($npwpClean) < 14) {
            return redirect()->route('unit-quotation.show', $id)
                ->with('error', 'NPWP client belum diisi or kurang dari 14 karakter. Pengajuan PO tidak dapat diproses.');
        }

        $year = now()->year;
        $path = $request->file('po_file')->store("unit-quotation/po/{$year}", 'public');

        $quote->update([
            'po_number'      => $request->po_number,
            'po_file'        => $path,
            'payment_method' => $request->payment_method,
            'status'         => 'po_received',
            'po_received'    => now()->toDateString(),
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
        $this->createInvoiceRecords($quote, $request->invoice_type, $request->dp_percent);

        // After upload PO, show convert-to-SalesOrder modal on the unit quotation detail page
        $flashData = [
            'success' => 'PO berhasil diupload. Status diubah ke PO Received.',
            'open_convert_po' => true,
            'noPending' => $pending->no_pending ?? ($quote->po_number ?? $quote->no_quote),
            'ekspidisi' => $pending->delivery ?? $this->resolvePendingDeliveryValue($quote->delivery_process),
            'pending_id' => $pending->id ?? null,
        ];

        return redirect()->route('unit-quotation.show', $id)->with($flashData);
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

        Invoice::create([
            'id_unit_quotation' => $quote->id,
            'no_po'             => $quote->po_number,
            'flag'              => 'Reftech',
            'pph'               => 0,
            'type'              => $request->label,
            'percent'           => $percentOfTotal,
        ]);

        return redirect()->route('unit-quotation.show', $id)
            ->with('success', 'Invoice selanjutnya berhasil diajukan.');
    }

    public function destroy($id)
    {
        $quote = UnitQuotation::findOrFail($id);
        $quote->delete();
        return response()->json(1);
    }

    public function addPayment(Request $request, $id)
    {
        UnitQuotation::findOrFail($id);

        $payment                    = new Payment();
        $payment->id_unit_quotation = $id;
        $payment->amount            = $request->amount;
        $payment->percent           = $request->percent;
        $payment->note              = $request->note;
        $payment->type              = $request->type;
        $payment->method            = $request->method;
        $isEscrow                   = ($request->method === 'Escrow');
        $payment->level             = $isEscrow ? 1 : 0;
        $payment->date              = now()->toDateString();
        if ($isEscrow) {
            $payment->date_confirm = now()->toDateString();
        }
        if ($request->type === 'Tempo') {
            $payment->tempo = $request->tempo;
        }
        $payment->save();

        if ($isEscrow) {
            Invoice::where('id_unit_quotation', $id)
                ->whereNotNull('no_invoice')
                ->update(['status_p' => 1]);
        }

        return redirect()->route('unit-quotation.show', $id)->with('success', 'Payment berhasil ditambahkan.');
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

    public function deletePayment($id)
    {
        $payment = Payment::findOrFail($id);
        $payment->delete();
        return response()->json(1);
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

        // 5. Reset data PO & status kembali ke Negotiation
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

    private function createInvoiceRecords(UnitQuotation $quote, string $invoiceType = 'CT', $dpPercent = null): void
    {
        Invoice::create([
            'id_unit_quotation' => $quote->id,
            'no_po'             => $quote->po_number,
            'flag'              => 'Reftech',
            'pph'               => 0,
            'type'              => $invoiceType,
            'percent'           => $invoiceType === 'DP' ? floatval($dpPercent ?? 50) : 100,
        ]);
    }

    private function saveDetails(int $quoteId, array $items): void
    {
        foreach ($items as $i => $item) {
            $isHeader = (($item['type'] ?? '') === 'header' || ($item['type'] ?? '') === 'heading');
            $qty      = $isHeader ? 0 : floatval($item['qty']   ?? 1);
            $price    = $isHeader ? 0 : floatval($item['price'] ?? 0);
            $disc     = $isHeader ? 0 : floatval($item['disc']  ?? 0);
            $amount   = $isHeader ? 0 : ($qty * $price * (1 - $disc / 100));

            UnitQuotationDetail::create([
                'id_unit_quotation' => $quoteId,
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
                'sort_order'        => $i,
            ]);
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
        $pending->no_pending = $quote->po_number ?: $quote->no_quote;
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

                // Auto create PurchaseRequest for missing quantity
                $missingQty = $item->qty - $totalStock;
                $pr = new \App\Models\PurchaseRequest();
                $pr->no_pr = $this->generateNoPr();
                $pr->id_pending = $pending->id;
                $pr->id_user = Auth::id() ?? $quote->id_sales;
                $pr->id_equivalent = $item->id_equivalent;
                $pr->qty = $missingQty;
                $pr->status = '0';
                $pr->date = Carbon::now();
                $pr->note = 'Otomatis dibuat oleh sistem karena stok kurang (Butuh: ' . $item->qty . ', Tersedia: ' . $totalStock . ')';
                $pr->save();
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
            $dPending->save();
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

    private function generateNoPr(): string
    {
        $year = now()->format('Y');
        $month = now()->format('m');
        $prefix = "PR/{$year}/{$month}/";

        $last = \App\Models\PurchaseRequest::where('no_pr', 'like', $prefix . '%')
            ->orderByDesc('no_pr')
            ->value('no_pr');

        $lastSeq = $last ? (int) substr($last, -3) : 0;
        $nextSeq = str_pad($lastSeq + 1, 3, '0', STR_PAD_LEFT);

        return $prefix . $nextSeq;
    }

    private function getTypePrefix(?string $type): string
    {
        return match ($type) {
            'Unit'      => 'U',
            'Rental'    => 'R',
            'Project'   => 'PR',
            'Parts'     => 'P',
            'Service'   => 'S',
            'Piping'    => 'PIP',
            'Air Audit' => 'AA',
            default     => 'PU',
        };
    }

    private function getNextSequenceNumber(): int
    {
        $dateNow = Carbon::now();
        $salesId = Auth::id();

        $legacyQuotes = \App\Models\Quotation::whereYear('created_at', $dateNow)
            ->where('id_sales', $salesId)
            ->pluck('no_quote');

        $unitQuotes = UnitQuotation::whereYear('created_at', $dateNow)
            ->where('id_sales', $salesId)
            ->pluck('no_quote');

        $maxSeq = 0;

        foreach ($legacyQuotes->concat($unitQuotes) as $noQuote) {
            if ($noQuote && preg_match('/^(\d+)-/i', $noQuote, $matches)) {
                $seq = (int) $matches[1];
                if ($seq > $maxSeq) {
                    $maxSeq = $seq;
                }
            }
        }

        if ($maxSeq === 0) {
            $totalCount = \App\Models\Quotation::whereYear('created_at', $dateNow)->where('id_sales', $salesId)->count()
                + UnitQuotation::whereYear('created_at', $dateNow)->where('id_sales', $salesId)->count();
            $maxSeq = $totalCount;
        }

        return $maxSeq + 1;
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
