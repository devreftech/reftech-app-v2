<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Contract;
use App\Models\Delivery;
use App\Models\DetailQuotation;
use App\Models\Expense;
use App\Models\ExpenseInvoice;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\ProductOut;
use App\Models\Prospect;
use App\Models\Quotation;
use App\Models\SubtitleQuotation;
use App\Models\Suo;
use App\Models\UnitQuotation;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $requestContract = Contract::join('quotation as q', 'q.id', '=', 'contract.id_quotation')
            ->join('pic as p', 'p.id', '=', 'q.id_pic')
            ->join('client as c', 'c.id', '=', 'p.id_client')
            ->join('users as u', 'u.id', '=', 'q.id_sales')
            ->where('contract.level', '0')
            ->count();
        $requestInvoice = Quotation::join('pic', 'pic.id', '=', 'quotation.id_pic')
            ->join('client', 'client.id', '=', 'pic.id_client')
            ->join('invoice', 'invoice.id_quotation', '=', 'quotation.id')
            ->join('users', 'users.id', '=', 'quotation.id_sales')
            ->where('status', '100')
            ->whereNotNull('client.npwp')
            ->whereNotNull('quotation.po_file')
            ->whereNull('invoice.no_invoice')
            ->count()
            + Invoice::pendingUnitRequest()->count();
        $noSaleProspect = Prospect::whereNULL('id_sales')->whereNull('provide')->count();
        $activeTab = request('tab', 'reftech');
        return view('pages.accounting.invoice.index', compact('requestContract', 'requestInvoice', 'noSaleProspect', 'activeTab'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $requestContract = Contract::join('quotation as q', 'q.id', '=', 'contract.id_quotation')
            ->join('pic as p', 'p.id', '=', 'q.id_pic')
            ->join('client as c', 'c.id', '=', 'p.id_client')
            ->join('users as u', 'u.id', '=', 'q.id_sales')
            ->where('contract.level', '0')
            ->count();
        $requestInvoice = Quotation::join('pic', 'pic.id', '=', 'quotation.id_pic')
            ->join('client', 'client.id', '=', 'pic.id_client')
            ->join('invoice', 'invoice.id_quotation', '=', 'quotation.id')
            ->join('users', 'users.id', '=', 'quotation.id_sales')
            ->where('status', '100')
            ->whereNotNull('client.npwp')
            ->whereNotNull('quotation.po_file')
            ->whereNull('invoice.no_invoice')
            ->count()
            + Invoice::pendingUnitRequest()->count();
        $invoice = Invoice::find($id);

        // Unit quotation invoice — arahkan ke halaman yang benar
        if ($invoice && $invoice->id_unit_quotation) {
            return redirect()->route('invoice.show_unit', $id);
        }

        $totalAmount = 0;
        $invoiceOrder = Invoice::where('id_quotation', $invoice->id_quotation)
            ->orderBy('id')
            ->pluck('id')
            ->search($id) + 1;
        $quote = Quotation::where('id', $invoice->id_quotation)->first();
        if ($quote->type != 'Sparepart') {
            $subQuote = SubtitleQuotation::with('detail')->where('id_quotation', $quote->id)->get();
        }
        // $return = ReturnQ::where('id_quotation', $invoice->id_quotation)->first();
        $dquote = DetailQuotation::where('id_quotation', $quote->id)->get();
        $payments = Payment::where('id_quotation', $invoice->id_quotation)
        ->orderBy('id')
        ->take($invoiceOrder)
        ->get();
        // dd($payments);
        $expense = ExpenseInvoice::where('id_invoice', $id)->get();
        $totalExpense = ExpenseInvoice::where('id_invoice', $id)->sum('total');

        $totalPph23 = 0;
        $totalPph = 0;
        if ($quote->type == 'Sparepart') {
            foreach ($dquote as $product) {
                $pph = ($product->amount * $product->pph) / 100;
                $totalPph23 += $pph;
            }
        } else {
            foreach ($subQuote as $subtitle) {
                foreach ($subtitle->detail as $detail) {

                    $pph = ($detail->amount * $detail->pph) / 100;
                    $totalPph23 += $pph;
                }
            }
        }
        foreach ($payments as $payment) {
            $totalAmount += $payment->amount;
        }
        $totalPph = $totalPph23 + $invoice->pph;
        $hargaAfterExpanse = $quote->harga_total - $totalExpense;
        $remaining = $quote->harga_total - $totalAmount;
        $harga = Payment::where('id_quotation', $quote->id)->get();
        $priceDp = $this->terbilang($quote->harga_total * @$harga[0]->percent / 100 - $totalPph);
        $priceBp = $this->terbilang($quote->harga_total * @$harga[1]->percent / 100 - $totalPph);
        // $price = $this->terbilang(@$harga->amount - $totalPph);
        $fullPrice = $this->terbilang($quote->harga_total - $totalPph);
        $tax = ($quote->subtotal - $quote->diskon) * $quote->tax / 100;
        // $pph = $quote->subtotal * $invoice->pph / 100;
        // dd($quote->harga_total - $totalpph);
        $afterDisc = $quote->subtotal - $quote->diskon;

        $doTek = Delivery::where('id_invoice', $id)->where('type', 'teknisi')->whereNot('code', 'Manual')->get();
        $doEks = Delivery::where('id_invoice', $id)->where('type', 'ekspedisi')->whereNot('code', 'Manual')->get();
        $doTekMan = Delivery::where('id_invoice', $id)->where('type', 'teknisi')->where('code', 'Manual')->get();
        $doEksMan = Delivery::where('id_invoice', $id)->where('type', 'ekspedisi')->where('code', 'Manual')->get();
        $noSaleProspect = Prospect::whereNULL('id_sales')->whereNull('provide')->count();
        $pOut = ProductOut::where('invoice', $invoice->no_invoice)->first();
        $lastPayment = Payment::where('id_quotation', $quote->id)->orderByDesc('id')->first();
        // dd($doTekMan);
        if ($quote->type == 'Sparepart') {
            return view('pages.accounting.invoice.detail', compact('totalPph23', 'totalPph', 'lastPayment', 'requestContract', 'requestInvoice', 'hargaAfterExpanse', 'totalExpense', 'expense', 'noSaleProspect', 'pOut', 'quote', 'harga', 'dquote', 'priceDp', 'priceBp', 'fullPrice', 'tax', 'invoice', 'payments', 'remaining', 'afterDisc', 'doTek', 'doEks', 'doTekMan', 'doEksMan'));
        } else {
            return view('pages.accounting.invoice.detail', compact('totalPph23', 'totalPph', 'lastPayment', 'requestContract', 'requestInvoice', 'subQuote', 'hargaAfterExpanse', 'totalExpense', 'expense', 'noSaleProspect', 'pOut', 'quote', 'harga', 'dquote', 'priceDp', 'priceBp', 'fullPrice', 'tax', 'invoice', 'payments', 'remaining', 'afterDisc', 'doTek', 'doEks', 'doTekMan', 'doEksMan'));
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
        //
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
        $rule = [
            'invoice' => 'required',
            'payment' => 'required',
        ];
        $message = [
            'invoice.required' => 'Field invoice Wajib Diisi',
            'payment.required' => 'Field payment Wajib Diisi',
        ];
        $this->validate($request, $rule, $message);
        $invoice = Invoice::findOrFail($id);
        
        $invoice->no_invoice = $request->invoice;
        $invoice->term = $request->payment;

        if ($invoice->id_unit_quotation) {
            $invoice->invoiceTo = '1';
            $invoiceSave = $invoice->save();
            if ($invoiceSave) {
                $quote = UnitQuotation::find($invoice->id_unit_quotation);
                if ($quote) {
                    $this->syncMonitoringDocumentCardUnit($quote);
                }
                return redirect()->route('invoice.show_unit', $id)->with('success', 'Invoice has been updated');
            }
        } else {
            $quote = Quotation::find($invoice->id_quotation);
            $invoice->invoiceTo = $quote->destination;
            $invoiceSave = $invoice->save();
            if ($invoiceSave) {
                $this->syncMonitoringDocumentCard($quote);
                return redirect('/invoice/' . $id)->with('message', 'Invoice has been accepted');
            }
        }
    }

    /**
     * Trigger auto Kanban card creation for Monitoring Document board
     * once an invoice for this quotation has been accepted (no_invoice filled).
     */
    private function syncMonitoringDocumentCard(Quotation $quote)
    {
        $hasNonEscrowPayment = Payment::where('id_quotation', $quote->id)
            ->where('method', '!=', 'Escrow')
            ->exists();

        if (!$hasNonEscrowPayment) {
            return;
        }

        $pendingPO = \App\Models\PendingPO::where('id_quotation', $quote->id)
            ->where('status', '<', 6)
            ->where('created_at', '>=', '2026-07-17 00:00:00')
            ->first();

        if (!$pendingPO) {
            return;
        }

        $board = \App\Models\KanbanBoard::where('type', 'monitoring')->first();
        if (!$board) {
            $board = \App\Models\KanbanBoard::create([
                'title' => 'Monitoring Document',
                'description' => 'Papan Kanban khusus untuk memantau dokumen PO/Sales Order.',
                'type' => 'monitoring',
                'created_by' => 1,
            ]);

            $defaultColumns = ['PO REFTECH', 'PO E-COMMERCE', 'Draft / SPK', 'Invoice Sent', 'Paid / Completed'];
            foreach ($defaultColumns as $index => $colTitle) {
                \App\Models\KanbanColumn::create([
                    'board_id' => $board->id,
                    'title' => $colTitle,
                    'position' => $index,
                ]);
            }
        }

        $cardExists = \App\Models\KanbanTask::where('board_id', $board->id)
            ->where('pending_po_id', $pendingPO->id)
            ->exists();

        if ($cardExists) {
            return;
        }

        $idSales = $quote->id_sales;
        $targetColTitle = ($idSales == 16) ? 'PO E-COMMERCE' : 'PO REFTECH';

        $column = \App\Models\KanbanColumn::where('board_id', $board->id)
            ->where('title', $targetColTitle)
            ->first();

        if (!$column) {
            return;
        }

        $pos = \App\Models\KanbanTask::where('column_id', $column->id)->count();

        $invoicePo = Invoice::where('id_quotation', $quote->id)->whereNotNull('no_po')->where('no_po', '!=', '')->value('no_po');
        $poNumber = $invoicePo ?: ($pendingPO->no_pending ?? 'No PO');
        $companyName = 'Unknown Client';
        if ($quote->pic && $quote->pic->client) {
            $companyName = $quote->pic->client->company;
        }

        $taskTitle = "[$poNumber] - $companyName";

        \App\Models\KanbanTask::create([
            'board_id' => $board->id,
            'column_id' => $column->id,
            'pending_po_id' => $pendingPO->id,
            'title' => $taskTitle,
            'description' => null,
            'position' => $pos,
        ]);
    }

    /**
     * Sama seperti syncMonitoringDocumentCard, tapi untuk invoice yang berasal
     * dari Unit Quotation (id_unit_quotation), yang sebelumnya tidak pernah
     * memicu pembuatan card Monitoring Document sama sekali.
     */
    private function syncMonitoringDocumentCardUnit(UnitQuotation $quote)
    {
        $hasNonEscrowPayment = Payment::where('id_unit_quotation', $quote->id)
            ->where('method', '!=', 'Escrow')
            ->exists();

        if (!$hasNonEscrowPayment) {
            return;
        }

        $pendingPO = \App\Models\PendingPO::where('id_unit_quotation', $quote->id)
            ->where('status', '<', 6)
            ->first();

        if (!$pendingPO) {
            return;
        }

        $board = \App\Models\KanbanBoard::where('type', 'monitoring')->first();
        if (!$board) {
            $board = \App\Models\KanbanBoard::create([
                'title' => 'Monitoring Document',
                'description' => 'Papan Kanban khusus untuk memantau dokumen PO/Sales Order.',
                'type' => 'monitoring',
                'created_by' => 1,
            ]);

            $defaultColumns = ['PO REFTECH', 'PO E-COMMERCE', 'Draft / SPK', 'Invoice Sent', 'Paid / Completed'];
            foreach ($defaultColumns as $index => $colTitle) {
                \App\Models\KanbanColumn::create([
                    'board_id' => $board->id,
                    'title' => $colTitle,
                    'position' => $index,
                ]);
            }
        }

        $cardExists = \App\Models\KanbanTask::where('board_id', $board->id)
            ->where('pending_po_id', $pendingPO->id)
            ->exists();

        if ($cardExists) {
            return;
        }

        $idSales = $quote->id_sales;
        $targetColTitle = ($idSales == 16) ? 'PO E-COMMERCE' : 'PO REFTECH';

        $column = \App\Models\KanbanColumn::where('board_id', $board->id)
            ->where('title', $targetColTitle)
            ->first();

        if (!$column) {
            return;
        }

        $pos = \App\Models\KanbanTask::where('column_id', $column->id)->count();

        $invoicePo = Invoice::where('id_unit_quotation', $quote->id)->whereNotNull('no_po')->where('no_po', '!=', '')->value('no_po');
        $poNumber = $invoicePo ?: ($pendingPO->no_pending ?? 'No PO');
        $companyName = $quote->client->company ?? 'Unknown Client';

        $taskTitle = "[$poNumber] - $companyName";

        \App\Models\KanbanTask::create([
            'board_id' => $board->id,
            'column_id' => $column->id,
            'pending_po_id' => $pendingPO->id,
            'title' => $taskTitle,
            'description' => null,
            'position' => $pos,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $invoice = Invoice::find($id);
        if ($invoice->type == "DP") {
            $allInvoice = Invoice::where('id_quotation', $invoice->id_quotation)->get();
            foreach ($allInvoice as $item) {
                $invoiceDel = $item->delete();
            }
        } else {
            $invoiceDel = $invoice->delete();
        }

        if ($invoiceDel) {
            return 1;
        } else {
            return 0;
        }
    }
    public function index_kojisha()
    {
        return redirect()->route('invoice.index', ['tab' => 'kojisha']);
    }
    public function request()
    {
        return redirect()->route('invoice.index', ['tab' => 'request']);
    }
    public function before_accept($id)
    {
        $requestContract = Contract::join('quotation as q', 'q.id', '=', 'contract.id_quotation')
            ->join('pic as p', 'p.id', '=', 'q.id_pic')
            ->join('client as c', 'c.id', '=', 'p.id_client')
            ->join('users as u', 'u.id', '=', 'q.id_sales')
            ->where('contract.level', '0')
            ->count();
        $requestInvoice = Quotation::join('pic', 'pic.id', '=', 'quotation.id_pic')
            ->join('client', 'client.id', '=', 'pic.id_client')
            ->join('invoice', 'invoice.id_quotation', '=', 'quotation.id')
            ->join('users', 'users.id', '=', 'quotation.id_sales')
            ->where('status', '100')
            ->whereNotNull('quotation.po_file')
            ->whereNotNull('client.npwp')
            ->whereNull('invoice.no_invoice')
            ->count()
            + Invoice::pendingUnitRequest()->count();
        $dateNow = Carbon::now();
        $year = $dateNow->year;
        $month = $dateNow->month;
        $monthCode = $this->convertToRoman($month);
        $lastInvoicePRef = Invoice::join('quotation', 'quotation.id', '=', 'invoice.id_quotation')
            ->leftJoin('payment as p', 'p.id_quotation', '=', 'quotation.id')
            ->where('quotation.tax', '11')
            ->where('invoice.flag', 'Reftech')
            ->whereNotNull('no_invoice')
            ->whereYear('invoice.created_at', $year)
            ->whereNot('p.method', 'Escrow')
            ->orderBy('invoice.no_invoice', 'desc')
            ->first(['invoice.*', 'quotation.tax']);
        $lastInvoiceNPRef = Invoice::join('quotation', 'quotation.id', '=', 'invoice.id_quotation')
            ->leftJoin('payment as p', 'p.id_quotation', '=', 'quotation.id')
            ->where('quotation.tax', '0')
            ->where('invoice.flag', 'Reftech')
            ->whereNotNull('no_invoice')
            ->whereYear('invoice.created_at', $year)
            ->whereNot('p.method', 'Escrow')
            ->orderBy('invoice.no_invoice', 'desc')
            ->first(['invoice.*', 'quotation.tax']);
        $lastInvoicePKoj = Invoice::join('quotation', 'quotation.id', '=', 'invoice.id_quotation')
            ->leftJoin('payment as p', 'p.id_quotation', '=', 'quotation.id')
            ->where('quotation.tax', '11')
            ->where('invoice.flag', 'Kojisha')
            ->whereNotNull('no_invoice')
            ->whereNotIn('quotation.id_sales', [16, 23])
            ->whereNot('p.method', 'Escrow')
            ->whereYear('invoice.created_at', $year)
            ->orderBy('invoice.no_invoice', 'desc')
            ->first(['invoice.*', 'quotation.tax']);
        $lastInvoiceNPKoj = Invoice::join('quotation', 'quotation.id', '=', 'invoice.id_quotation')
            ->leftJoin('payment as p', 'p.id_quotation', '=', 'quotation.id')
            ->where('quotation.tax', '0')
            ->where('invoice.flag', 'Kojisha')
            ->whereNotNull('no_invoice')
            ->whereNotIn('quotation.id_sales', [16, 23])
            ->whereNot('p.method', 'Escrow')
            ->whereYear('invoice.created_at', $year)
            ->orderBy('invoice.no_invoice', 'desc')
            ->first(['invoice.*', 'quotation.tax']);
        // Ambil sequence terakhir dari SUO booking agar urutan invoice global konsisten
        $lastSuoBookingNo = Suo::whereNotNull('no_invoice_booking')->orderByDesc('id')->value('no_invoice_booking');
        $lastSuoSeq = 0;
        if ($lastSuoBookingNo) {
            preg_match('/^(\d+)\//', $lastSuoBookingNo, $m);
            $lastSuoSeq = isset($m[1]) ? (int) $m[1] : 0;
        }

        // Juga pertimbangkan invoice unit quotation yg sudah ada (satu namespace nomor dgn invoice biasa)
        $lastUnitInvoice = Invoice::whereNotNull('id_unit_quotation')
            ->whereNotNull('no_invoice')
            ->whereYear('created_at', $year)
            ->orderBy('no_invoice', 'desc')
            ->first();
        $lastUnitSeq = 0;
        if ($lastUnitInvoice) {
            preg_match('/^(\d+)\//', $lastUnitInvoice->no_invoice, $m);
            $lastUnitSeq = isset($m[1]) ? (int) $m[1] : 0;
        }

        function generateNextInvoiceNumber($lastInvoice, $defaultCode, $lastSuoSeq = 0, $lastUnitSeq = 0)
        {
            $lastSeqFromInvoice = 0;
            if ($lastInvoice) {
                preg_match('/^(\d+)\//', $lastInvoice->no_invoice, $matches);
                if (!empty($matches)) {
                    $lastSeqFromInvoice = (int) $matches[1];
                }
            }

            $effectiveLast = max($lastSeqFromInvoice, $lastSuoSeq, $lastUnitSeq);

            if ($effectiveLast > 0) {
                return str_pad($effectiveLast + 1, 3, '0', STR_PAD_LEFT);
            }

            return $defaultCode;
        }
        // Generate next invoice numbers — mempertimbangkan sequence SUO booking & invoice unit quotation
        $nextCodePR  = generateNextInvoiceNumber($lastInvoicePRef,  '001', $lastSuoSeq, $lastUnitSeq);
        $nextCodeNPR = generateNextInvoiceNumber($lastInvoiceNPRef, '001', $lastSuoSeq, $lastUnitSeq);
        $nextCodePK  = generateNextInvoiceNumber($lastInvoicePKoj,  '001', $lastSuoSeq, $lastUnitSeq);
        $nextCodeNPK = generateNextInvoiceNumber($lastInvoiceNPKoj, '001', $lastSuoSeq, $lastUnitSeq);

        // "Last No" display — tampilkan nomor terakhir yg benar-benar dipakai (invoice, SUO booking, atau invoice unit quotation)
        $getEffectiveLastDisplay = function ($lastInvoice) use ($lastSuoBookingNo, $lastSuoSeq, $lastUnitInvoice, $lastUnitSeq) {
            $best = 0;
            if ($lastInvoice) {
                preg_match('/^(\d+)\//', $lastInvoice->no_invoice, $m);
                $best = isset($m[1]) ? (int) $m[1] : 0;
            }
            $bestDisplay = $lastInvoice->no_invoice ?? null;

            if ($lastUnitSeq > $best) {
                $best = $lastUnitSeq;
                $bestDisplay = $lastUnitInvoice->no_invoice;
            }
            if ($lastSuoSeq > $best) {
                $bestDisplay = $lastSuoBookingNo;
            }

            return $bestDisplay;
        };
        $displayLastPR  = $getEffectiveLastDisplay($lastInvoicePRef);
        $displayLastNPR = $getEffectiveLastDisplay($lastInvoiceNPRef);
        $displayLastPK  = $getEffectiveLastDisplay($lastInvoicePKoj);
        $displayLastNPK = $getEffectiveLastDisplay($lastInvoiceNPKoj);

        $totalAmount = 0;
        $invoice = Invoice::find($id);
        $quote = Quotation::find($invoice->id_quotation);
        if ($quote->type != 'Sparepart') {
            $subQuote = SubtitleQuotation::with('detail')->where('id_quotation', $invoice->id_quotation)->get();
        }
        $dquote = DetailQuotation::where('id_quotation', $quote->id)->get();
        $payments = Payment::where('id_quotation', $quote->id)->get();
        $lastPayment = Payment::where('id_quotation', $quote->id)->orderByDesc('id')->first();

        foreach ($payments as $payment) {
            $totalAmount += $payment->amount;
        }

        $remaining = $quote->harga_total - $totalAmount;
        $price = $this->terbilang($remaining);
        $tax = ($quote->subtotal - $quote->diskon) * $quote->tax / 100;
        // dd($price);
        if ($quote->type != 'Sparepart') {
            return view('pages.accounting.invoice.before-accept', compact('lastPayment', 'requestContract', 'requestInvoice', 'quote', 'subQuote', 'dquote', 'price', 'tax', 'invoice', 'payments', 'remaining', 'lastInvoicePRef', 'lastInvoiceNPRef', 'lastInvoicePKoj', 'lastInvoiceNPKoj', 'nextCodePR', 'nextCodeNPR', 'nextCodePK', 'nextCodeNPK', 'displayLastPR', 'displayLastNPR', 'displayLastPK', 'displayLastNPK', 'year', 'monthCode'));
        } else {
            return view('pages.accounting.invoice.before-accept', compact('lastPayment', 'requestContract', 'requestInvoice', 'quote', 'dquote', 'price', 'tax', 'invoice', 'payments', 'remaining', 'lastInvoicePRef', 'lastInvoiceNPRef', 'lastInvoicePKoj', 'lastInvoiceNPKoj', 'nextCodePR', 'nextCodeNPR', 'nextCodePK', 'nextCodeNPK', 'displayLastPR', 'displayLastNPR', 'displayLastPK', 'displayLastNPK', 'year', 'monthCode'));
        }
    }

    public function print_invoice($id)
    {
        $totalAmount = 0;
        $invoice = Invoice::find($id);
        $quote = Quotation::where('id', $invoice->id_quotation)->first();
        $dquote = DetailQuotation::where('id_quotation', $quote->id)->get();
        $payments = Payment::where('id_quotation', $quote->id)->get();

        if ($quote->type != 'Sparepart') {
            $subQuote = SubtitleQuotation::with('detail')->where('id_quotation', $quote->id)->get();
            // dd($subQuote);
        }
        $totalPph = 0;

        if ($quote->type == 'Sparepart') {
            foreach ($dquote as $product) {
                $pph = ($product->amount * $product->pph) / 100;
                $totalPph += $pph;
            }
        } else {
            foreach ($subQuote as $subtitle) {
                foreach ($subtitle->detail as $detail) {

                    $pph = ($detail->amount * $detail->pph) / 100;
                    $totalPph += $pph;
                }
            }
        }
        $totalPph += $invoice->pph ?? 0;
        foreach ($payments as $payment) {
            $totalAmount += $payment->amount;
        }

        $remaining = $quote->harga_total - $totalAmount;
        $harga = Payment::where('id_quotation', $quote->id)->get();
        $price = $this->terbilang(@$harga[0]->amount - $totalPph);
        $fullPrice = $this->terbilang($quote->harga_total - $totalPph);
        $priceDp = $this->terbilang($quote->harga_total * @$harga[0]->percent / 100 - $totalPph);
        $priceBp = $this->terbilang($quote->harga_total * @$harga[1]->percent / 100 - $totalPph);
        $tax = ($quote->subtotal - $quote->diskon) * $quote->tax / 100;
        // $pph = $quote->subtotal * $invoice->pph / 100;
        $afterDisc = $quote->subtotal - $quote->diskon;
        // dd($termncon);
        if ($quote->type == 'Sparepart') {
            return view("pages.accounting.invoice.detail-print", compact('harga', 'quote', 'priceDp', 'priceBp', 'dquote', 'tax', 'invoice', 'price', 'fullPrice', 'payments', 'remaining', 'afterDisc'));
        } else {
            return view("pages.accounting.invoice.detail-print", compact('subQuote', 'harga', 'priceDp', 'priceBp', 'quote', 'dquote', 'tax', 'invoice', 'price', 'fullPrice', 'payments', 'remaining', 'afterDisc'));
        }

    }

    public function hand_sign(Request $request, $id)
    {
        $photo = Invoice::find($id);
        $quote = Quotation::where('id', $photo->id_quotation)->first();
        $harga = Payment::where('id_quotation', $quote->id)->orderBy('created_at', 'DESC')->first();
        $jumlah = isset($harga) ? $harga->amount : $quote->harga_total;

        if ($photo->flag == "Reftech") {
            $photo->sign = $jumlah >= 5000000 ? 'asset/sign/reftech-m.jpeg' : 'asset/sign/reftech-nm.jpeg';
        } elseif ($photo->flag == "Kojisha") {
            $photo->sign = $jumlah >= 5000000 ? 'asset/sign/kojisha-m.jpeg' : 'asset/sign/kojisha-nm.jpeg';
        }

        // if ($request->hasFile('sign')) {
        //     $foto = $request->file('sign'); // Akses file sesuai dengan iterasi saat ini
        //     // Proses setiap file gambar
        //     $image_ext = $foto->getClientOriginalExtension();
        //     $image_name = Str::random(8);

        //     $upload_path = 'asset/invoice';
        //     $imagename = $upload_path . '/' . $image_name . '.' . $image_ext;

        //     // Pemrosesan gambar
        //     $img = Image::make($foto->path());
        //     $img->fit(800, 500, function ($constraint) {
        //         $constraint->aspectRatio();
        //     });
        //     $img->save($imagename);

        //     $photo['sign'] = $imagename;
        // }
        // dd($photo);

        $status = $photo->save();

        if ($status) {
            return 1;
        } else {
            0;
        }
    }
    public function delete_hand_sign($id)
    {
        $invoice = Invoice::find($id);

        // $delsign = File::delete($invoice->sign);
        // if ($delsign) {
        $invoice->sign = NULL;
        // }
        // dd($photo);
        $status = $invoice->save();

        if ($status) {
            return 1;
        } else {
            return 0;
        }
    }
    public function change_date(Request $request, $id)
    {
        $invoice = Invoice::findOrFail($id);

        $invoice->date = $request->date;
        $invoice->invoiceTo = $request->destination;
        $invoiceSave = $invoice->save();

        if ($invoiceSave) {
            if ($invoice->id_unit_quotation) {
                return redirect()->route('invoice.show_unit', $id)->with('success', 'Tanggal invoice berhasil diubah.');
            }
            return redirect('/invoice/' . $id)->with('massage', 'Data telah terkirim');
        }
    }

    public function inputExpense(Request $request, $id)
    {
        // dd($request->all());
        $expense = new ExpenseInvoice();
        $expense->id_invoice = $id;
        $expense->desc = $request->desc;
        $expense->qty = $request->qty;
        $expense->price = $request->price;
        $expense->total = $request->price * $request->qty;
        $expenseSave = $expense->save();
        if ($expenseSave) {
            return redirect('/invoice/' . $id)->with('massage', 'Data telah terkirim');
        }
    }

    public function deleteExpense($id)
    {
        $expense = ExpenseInvoice::find($id);
        $expenseDel = $expense->delete();
        if ($expenseDel) {
            return 1;
        } else {
            return 0;
        }

    }

    public function do_ekspedisi($id)
    {
        $invoice = Invoice::find($id);
        $quote = Quotation::find($invoice->id_quotation);
        $dQuote = DetailQuotation::where('id_quotation', $invoice->id_quotation)->get();
        $client = Client::where('id', $quote->pic->id_client)->first();
        // dd($client);

        return view("pages.accounting.delivery.ekspedisi", compact('quote', 'invoice', 'dQuote', 'client'));
    }
    public function print_ekspedisi($id)
    {
        $invoice = Invoice::find($id);
        $quote = Quotation::find($invoice->id_quotation);
        $dQuote = DetailQuotation::where('id_quotation', $invoice->id_quotation)->get();

        return view("pages.accounting.delivery.ekspedisi-print", compact('quote', 'invoice', 'dQuote'));
    }

    public function do_teknisi($id)
    {
        $invoice = Invoice::find($id);
        $quote = Quotation::find($invoice->id_quotation);
        $dQuote = DetailQuotation::where('id_quotation', $invoice->id_quotation)->get();

        return view("pages.accounting.delivery.teknisi", compact('quote', 'invoice', 'dQuote'));
    }
    public function print_teknisi($id)
    {
        $invoice = Invoice::find($id);
        $quote = Quotation::find($invoice->id_quotation);
        $dQuote = DetailQuotation::where('id_quotation', $invoice->id_quotation)->get();

        return view("pages.accounting.delivery.teknisi-print", compact('quote', 'invoice', 'dQuote'));
    }
    public function form_ekspedisi(Request $request, $id)
    {
        $invoice = Invoice::find($id);

        $invoice->dateDo = $request->date;
        $invoice->doTo = $request->destination;
        $status = $invoice->save();

        if ($status) {
            return redirect('/invoice/do_ekspedisi/' . $id)->with('massage', 'Data telah terkirim');
        }
    }
    public function form_teknisi(Request $request, $id)
    {
        $invoice = Invoice::find($id);

        if ($request->check == '1') {
            $invoice->dateDo = NULL;
        } else {
            $invoice->dateDo = $request->date;
        }

        $invoice->doTo = $request->destination;
        $status = $invoice->save();

        if ($status) {
            return redirect('/invoice/do_teknisi/' . $id)->with('massage', 'Data telah terkirim');
        }
    }

    public function label_detail($id)
    {
        $invoice = Invoice::find($id);
        $quote = Quotation::find($invoice->id_quotation);

        return view("pages.accounting.label.detail", compact('quote', 'invoice'));
    }
    public function label_print($id)
    {
        $invoice = Invoice::find($id);
        $quote = Quotation::find($invoice->id_quotation);

        return view("pages.accounting.label.detail-print", compact('quote', 'invoice'));
    }

    public function add_pph(Request $request, $id)
    {
        $invoice = Invoice::find($id);
        $dQuote = DetailQuotation::where('id_quotation', $invoice->id_quotation)->get();
        foreach ($dQuote as $item => $value) {
            $value->pph = $request->pph[$item];
            $status = $value->save();
        }
        if ($status) {
            return redirect('/invoice/' . $id)->with('massage', 'Data telah terkirim');
        }
    }
    public function add_pph_manual(Request $request, $id)
    {
        $invoice = Invoice::find($id);
        $invoice->pph = $request->pph;
        $status = $invoice->save();
        if ($status) {
            return redirect('/invoice/' . $id)->with('massage', 'Data telah terkirim');
        }
    }
    public function add_pph_service(Request $request, $id)
    {
        $invoice = Invoice::find($id);
        $subQuotes = SubtitleQuotation::with('detail')->where('id_quotation', $invoice->id_quotation)->get();
        $row = 0;
        foreach ($subQuotes as $subQuote) {
            foreach ($subQuote->detail as $index => $value) {
                $row++;
                $value->pph = $request->pph[$row]; // pastikan $request->pph ada dan indexnya sesuai
                $status = $value->save();
            }
        }
        if ($status) {
            return redirect('/invoice/' . $id)->with('massage', 'Data telah terkirim');
        }
    }

    public function delete_pph($id)
    {
        $invoice = Invoice::find($id);
        $dQuote = DetailQuotation::where('id_quotation', $invoice->id_quotation)->get();
        foreach ($dQuote as $item => $value) {
            $value->pph = 0;
            $status = $value->save();
        }
        if ($status) {
            return 1;
        } else {
            return 0;
        }

    }
    public function delete_pph_manual($id)
    {
        $invoice = Invoice::find($id);
        $invoice->pph = 0;
        $status = $invoice->save();
        if ($status) {
            return 1;
        } else {
            return 0;
        }

    }
    public function delete_pph_service($id)
    {
        $invoice = Invoice::find($id);
        $subQuotes = SubtitleQuotation::with('detail')->where('id_quotation', $invoice->id_quotation)->get();

        foreach ($subQuotes as $subQuote) {
            foreach ($subQuote->detail as $index => $value) {
                $value->pph = 0;
                $status = $value->save();
            }
        }
        if ($status) {
            return 1;
        } else {
            return 0;
        }

    }
    public function change_desc(Request $request, $id)
    {
        $invoice = Invoice::find($id);
        $dQuote = DetailQuotation::where('id_quotation', $invoice->id_quotation)->get();

        $checkedIds = (array) $request->input('checker', []);
        // dd($checkedIds);

        foreach ($dQuote as $value) {
            $value->view = in_array($value->id, $checkedIds) ? '1' : '0';
            $status = $value->save();
        }

        if ($status) {
            return redirect('/invoice/' . $id)->with('message', 'Data telah terkirim');
        } else {
            return redirect('/invoice/' . $id)->with('error', 'Terjadi kesalahan saat mengirim data');
        }
    }
    public function due_date(Request $request, $id)
    {
        $invoice = Invoice::findOrFail($id);

        $rawDueDate = $request->input('due_date');
        $baseDate = $request->input('date') ? Carbon::parse($request->input('date')) : ($invoice->date ? Carbon::parse($invoice->date) : Carbon::today());

        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $rawDueDate)) {
            $dueDate = Carbon::parse($rawDueDate);
        } elseif (is_numeric($rawDueDate)) {
            $dueDate = (clone $baseDate)->addDays((int) $rawDueDate);
        } else {
            $dueDate = Carbon::parse($rawDueDate);
        }

        $daysCount = (int) (clone $baseDate)->diffInDays($dueDate, false);

        if ($invoice->id_unit_quotation) {
            $payment = Payment::where('id_unit_quotation', $invoice->id_unit_quotation)
                ->where('type', 'Tempo')
                ->orderByDesc('id')
                ->first();

            if (!$payment) {
                $payment = Payment::where('id_unit_quotation', $invoice->id_unit_quotation)->orderByDesc('id')->first();
            }

            if (!$payment) {
                $quote = UnitQuotation::find($invoice->id_unit_quotation);
                $payment = new Payment();
                $payment->id_unit_quotation = $invoice->id_unit_quotation;
                $payment->type              = 'Tempo';
                $payment->amount            = $invoice->harga_total ?? ($quote->total ?? 0);
                $payment->level             = 0;
                $payment->date              = $invoice->date ? Carbon::parse($invoice->date)->toDateString() : now()->toDateString();
            }

            $payment->due_date = $dueDate->toDateString();
            $payment->overdue  = $daysCount;
            $payment->tempo    = $daysCount > 0 ? $daysCount : ($payment->tempo ?? 30);
            if ($request->filled('note')) {
                $payment->note = $request->note;
            }
            $payment->save();

            return redirect()->route('invoice.show_unit', $id)
                ->with('success', 'Tanggal jatuh tempo berhasil disimpan: ' . $dueDate->format('d M Y'));
        } else {
            $quote = Quotation::find($invoice->id_quotation);
            $payment = Payment::where('id_quotation', $quote->id)
                ->where('type', 'Tempo')
                ->orderByDesc('id')
                ->first();

            if (!$payment) {
                $payment = Payment::where('id_quotation', $quote->id)->orderByDesc('id')->first();
            }

            if (!$payment) {
                $payment = new Payment();
                $payment->id_quotation = $quote->id;
                $payment->type         = 'Tempo';
                $payment->amount       = $quote->harga_total ?? 0;
                $payment->level        = 0;
                $payment->date         = $invoice->date ? Carbon::parse($invoice->date)->toDateString() : now()->toDateString();
            }

            $payment->due_date = $dueDate->toDateString();
            $payment->overdue  = $daysCount;
            $payment->tempo    = $daysCount > 0 ? $daysCount : ($payment->tempo ?? 30);
            if ($request->filled('note')) {
                $payment->note = $request->note;
            }
            $payment->save();

            return redirect()->route('invoice.show', $id)
                ->with('message', 'Tanggal jatuh tempo berhasil disimpan: ' . $dueDate->format('d M Y'));
        }
    }
    public function confirm_payment(Request $request, $id)
    {
        $invoice = Invoice::find($id);
        $invoice->status_p = 1;
        $invoice->note_p = $request->note;
        $invoiceSave = $invoice->save();
        if ($invoiceSave) {
            return redirect('/invoice/' . $id)->with('message', 'Data telah terkirim');
        }
    }
    public function undo_confirm_payment($id)
    {
        $invoice = Invoice::find($id);
        $invoice->status_p = 0;
        $invoice->note_p = null;
        $invoiceSave = $invoice->save();
        if ($invoiceSave) {
            return 1;
        } else {
            return 0;
        }
    }
    public function add_pph_unit(Request $request, $id)
    {
        $invoice = Invoice::findOrFail($id);
        $details = \App\Models\UnitQuotationDetail::where('id_unit_quotation', $invoice->id_unit_quotation)->get();
        foreach ($details as $i => $detail) {
            $detail->pph = $request->pph[$i] ?? 0;
            $detail->save();
        }
        return redirect()->route('invoice.show_unit', $id)->with('success', 'PPH 23 berhasil disimpan.');
    }

    public function delete_pph_unit($id)
    {
        $invoice = Invoice::findOrFail($id);
        \App\Models\UnitQuotationDetail::where('id_unit_quotation', $invoice->id_unit_quotation)
            ->update(['pph' => 0]);
        return 1;
    }

    public function add_pph_manual_unit(Request $request, $id)
    {
        $invoice      = Invoice::findOrFail($id);
        $invoice->pph = $request->pph;
        $invoice->save();
        return redirect()->route('invoice.show_unit', $id)->with('success', 'PPH Manual berhasil disimpan.');
    }

    public function delete_pph_manual_unit($id)
    {
        $invoice      = Invoice::findOrFail($id);
        $invoice->pph = 0;
        $invoice->save();
        return 1;
    }

    public function confirm_payment_unit(Request $request, $id)
    {
        $invoice           = Invoice::findOrFail($id);
        $invoice->status_p = 1;
        $invoice->save();

        Payment::where('id_unit_quotation', $invoice->id_unit_quotation)
            ->where('level', 0)
            ->update(['level' => 1, 'date_confirm' => now()]);

        return redirect()->route('invoice.show_unit', $id)->with('success', 'Pembayaran telah dikonfirmasi.');
    }

    public function toggleSpec($id)
    {
        $invoice           = Invoice::findOrFail($id);
        $invoice->show_spec = $invoice->show_spec ? 0 : 1;
        $invoice->save();
        return response()->json(['show_spec' => $invoice->show_spec]);
    }

    public function undo_payment_unit($id)
    {
        $invoice           = Invoice::findOrFail($id);
        $invoice->status_p = 0;
        $invoice->save();

        Payment::where('id_unit_quotation', $invoice->id_unit_quotation)
            ->where('level', 1)
            ->update(['level' => 0, 'date_confirm' => null]);

        return 1;
    }

    public function hand_sign_unit($id)
    {
        $invoice = Invoice::findOrFail($id);
        $quote   = UnitQuotation::findOrFail($invoice->id_unit_quotation);

        $amount = $quote->total;
        if ($invoice->flag === 'Reftech') {
            $invoice->sign = $amount >= 5000000
                ? 'asset/sign/reftech-m.jpeg'
                : 'asset/sign/reftech-nm.jpeg';
        } else {
            $invoice->sign = $amount >= 5000000
                ? 'asset/sign/kojisha-m.jpeg'
                : 'asset/sign/kojisha-nm.jpeg';
        }

        return $invoice->save() ? 1 : 0;
    }

    public function delete_hand_sign_unit($id)
    {
        $invoice       = Invoice::findOrFail($id);
        $invoice->sign = null;
        return $invoice->save() ? 1 : 0;
    }

    public function label_detail_unit($id)
    {
        $invoice = Invoice::findOrFail($id);
        $quote   = UnitQuotation::with(['client', 'pic'])->findOrFail($invoice->id_unit_quotation);
        return view('pages.accounting.label.detail-unit', compact('invoice', 'quote'));
    }

    public function label_print_unit($id)
    {
        $invoice = Invoice::findOrFail($id);
        $quote   = UnitQuotation::with(['client', 'pic'])->findOrFail($invoice->id_unit_quotation);
        return view('pages.accounting.label.detail-print-unit', compact('invoice', 'quote'));
    }

    public function show_unit($id)
    {
        $invoice = Invoice::findOrFail($id);
        $quote   = UnitQuotation::with(['client', 'pic', 'sales', 'details.unit', 'details.equivalent.product', 'deliveries.detail'])->findOrFail($invoice->id_unit_quotation);

        $allInvoices = Invoice::where('id_unit_quotation', $quote->id)
            ->orderByRaw("FIELD(type,'DP','BP','CT')")
            ->get();

        $payments = Payment::where('id_unit_quotation', $quote->id)->orderBy('id')->get();

        $percent       = floatval($invoice->percent ?? 100);
        $invoiceAmount = round($quote->total * $percent / 100);

        $afterDiskon   = $quote->subtotal - $quote->discount_amount;
        $totalPph      = $quote->details->sum(fn($d) => ($d->amount * $d->pph) / 100);
        $totalPph     += $invoice->pph ?? 0;
        $totalAfterPph = $invoiceAmount - $totalPph;
        $terbilang     = $this->terbilang($totalAfterPph);
        $terbilangEn   = $this->terbilangEn($totalAfterPph);

        $requestContract = Contract::join('quotation as q', 'q.id', '=', 'contract.id_quotation')
            ->join('pic as p', 'p.id', '=', 'q.id_pic')->join('client as c', 'c.id', '=', 'p.id_client')
            ->join('users as u', 'u.id', '=', 'q.id_sales')->where('contract.level', '0')->count();
        $requestInvoice = Quotation::join('pic', 'pic.id', '=', 'quotation.id_pic')
            ->join('client', 'client.id', '=', 'pic.id_client')
            ->join('invoice', 'invoice.id_quotation', '=', 'quotation.id')
            ->join('users', 'users.id', '=', 'quotation.id_sales')
            ->where('status', '100')->whereNotNull('client.npwp')
            ->whereNotNull('quotation.po_file')->whereNull('invoice.no_invoice')->count()
            + Invoice::pendingUnitRequest()->count();
        $noSaleProspect = Prospect::whereNull('id_sales')->whereNull('provide')->count();
        $pendingPO = \App\Models\PendingPO::with(['doc_recipient', 'shipping_recipient'])->where('id_unit_quotation', $quote->id)->first();
        $monitoringTask = null;
        $bast = null;
        if ($pendingPO) {
            $monitoringTask = \App\Models\KanbanTask::where('pending_po_id', $pendingPO->id)->first();
        }
        if ($monitoringTask) {
            $bast = \App\Models\Bast::where('id_kanban_task', $monitoringTask->id)->with('units')->first();
        }
        if (!$bast && isset($quote->id)) {
            $bast = \App\Models\Bast::where('id_quotation', $quote->id)->with('units')->first();
        }

        return view('pages.accounting.invoice.detail-unit', compact(
            'invoice', 'quote', 'allInvoices', 'payments', 'afterDiskon', 'terbilang', 'terbilangEn',
            'totalPph', 'totalAfterPph', 'invoiceAmount',
            'requestContract', 'requestInvoice', 'noSaleProspect',
            'pendingPO', 'monitoringTask', 'bast'
        ));
    }

    public function print_unit($id)
    {
        $invoice       = Invoice::findOrFail($id);
        $quote         = UnitQuotation::with(['client', 'pic', 'details.unit', 'details.equivalent.product'])->findOrFail($invoice->id_unit_quotation);

        $percent       = floatval($invoice->percent ?? 100);
        $invoiceAmount = round($quote->total * $percent / 100);

        $totalPph      = $quote->details->sum(fn($d) => ($d->amount * $d->pph) / 100);
        $totalPph     += $invoice->pph ?? 0;
        $totalAfterPph = $invoiceAmount - $totalPph;
        $terbilang     = $this->terbilang($totalAfterPph);
        $terbilangEn   = $this->terbilangEn($totalAfterPph);

        return view('pages.accounting.invoice.detail-print-unit', compact(
            'invoice', 'quote', 'terbilang', 'terbilangEn', 'totalPph', 'totalAfterPph', 'invoiceAmount'
        ));
    }

    public function before_accept_unit($id)
    {
        $invoice = Invoice::findOrFail($id);
        $quote   = UnitQuotation::with(['client', 'pic', 'details.unit', 'details.equivalent.product', 'sales'])->findOrFail($invoice->id_unit_quotation);

        // Kumpulkan semua invoice untuk unit quotation ini (DP & BP → 2 record)
        $allInvoices = Invoice::where('id_unit_quotation', $quote->id)
            ->whereNull('no_invoice')
            ->orderByRaw("FIELD(type,'DP','BP','CT')")
            ->get();

        // Auto-generate nomor invoice — ikuti urutan global (sama dengan before_accept biasa)
        $year       = Carbon::now()->year;
        $monthCode  = $this->convertToRoman(Carbon::now()->month);

        $lastSuoBookingNo = Suo::whereNotNull('no_invoice_booking')->orderByDesc('id')->value('no_invoice_booking');
        $lastSuoSeq = 0;
        if ($lastSuoBookingNo) {
            preg_match('/^(\d+)\//', $lastSuoBookingNo, $m);
            $lastSuoSeq = isset($m[1]) ? (int) $m[1] : 0;
        }

        $lastInvoicePRef = Invoice::join('quotation', 'quotation.id', '=', 'invoice.id_quotation')
            ->leftJoin('payment as p', 'p.id_quotation', '=', 'quotation.id')
            ->where('quotation.tax', '11')->where('invoice.flag', 'Reftech')
            ->whereNotNull('no_invoice')->whereYear('invoice.created_at', $year)
            ->whereNot('p.method', 'Escrow')
            ->orderBy('invoice.no_invoice', 'desc')
            ->first(['invoice.*', 'quotation.tax']);

        // Juga pertimbangkan invoice unit quotation yg sudah ada
        $lastUnitInvoice = Invoice::whereNotNull('id_unit_quotation')
            ->whereNotNull('no_invoice')
            ->whereYear('created_at', $year)
            ->orderBy('no_invoice', 'desc')
            ->first();

        $lastSeqFromService = 0;
        if ($lastInvoicePRef) {
            preg_match('/^(\d+)\//', $lastInvoicePRef->no_invoice, $m);
            $lastSeqFromService = isset($m[1]) ? (int) $m[1] : 0;
        }
        $lastSeqFromUnit = 0;
        if ($lastUnitInvoice) {
            preg_match('/^(\d+)\//', $lastUnitInvoice->no_invoice, $m);
            $lastSeqFromUnit = isset($m[1]) ? (int) $m[1] : 0;
        }

        $effectiveLast = max($lastSeqFromService, $lastSeqFromUnit, $lastSuoSeq);
        $nextSeq       = $effectiveLast > 0 ? $effectiveLast + 1 : 1;

        // Format: {seq}/SJ-P/RJO/{month}/{year} (PPN) atau SJ-NP (non-PPN)
        $sjCode = $quote->tax ? 'SJ-P' : 'SJ-NP';

        // Untuk DP & BP: siapkan 2 nomor berurutan
        $nextNumbers = [];
        foreach ($allInvoices as $i => $inv) {
            $nextNumbers[$inv->id] = str_pad($nextSeq + $i, 3, '0', STR_PAD_LEFT) . '/' . $sjCode . '/RJO/' . $monthCode . '/' . $year;
        }

        $requestContract = Contract::join('quotation as q', 'q.id', '=', 'contract.id_quotation')
            ->join('pic as p', 'p.id', '=', 'q.id_pic')->join('client as c', 'c.id', '=', 'p.id_client')
            ->join('users as u', 'u.id', '=', 'q.id_sales')->where('contract.level', '0')->count();
        $requestInvoice = Quotation::join('pic', 'pic.id', '=', 'quotation.id_pic')
            ->join('client', 'client.id', '=', 'pic.id_client')
            ->join('invoice', 'invoice.id_quotation', '=', 'quotation.id')
            ->join('users', 'users.id', '=', 'quotation.id_sales')
            ->where('status', '100')->whereNotNull('client.npwp')
            ->whereNotNull('quotation.po_file')->whereNull('invoice.no_invoice')->count()
            + Invoice::pendingUnitRequest()->count();
        $noSaleProspect = Prospect::whereNull('id_sales')->whereNull('provide')->count();

        return view('pages.accounting.invoice.before-accept-unit', compact(
            'invoice', 'quote', 'allInvoices', 'nextNumbers',
            'requestContract', 'requestInvoice', 'noSaleProspect',
            'year', 'monthCode'
        ));
    }

    public function accept_unit(Request $request, $id)
    {
        $invoice = Invoice::findOrFail($id);
        $quote   = UnitQuotation::findOrFail($invoice->id_unit_quotation);

        $pendingInvoices = Invoice::where('id_unit_quotation', $quote->id)
            ->whereNull('no_invoice')
            ->orderByRaw("FIELD(type,'DP','BP','CT')")
            ->get();

        $invoiceDate = $request->input('invoice_date', now()->toDateString());
        $term        = $request->input('term');

        foreach ($pendingInvoices as $inv) {
            $inv->no_invoice = $request->input('no_invoice_' . $inv->id);
            $inv->date       = $invoiceDate;
            $inv->invoiceTo  = '1';
            $inv->term       = $term;
            $inv->save();
        }

        $justIssued = $pendingInvoices->first();
        return redirect()->route('invoice.show_unit', $justIssued->id)
            ->with('success', 'Invoice berhasil diterbitkan.');
    }

    /**
     * Reject pengajuan invoice unit quotation — semua invoice yang masih pending
     * (belum ada no_invoice) untuk unit quotation yang sama ikut di-reject sekaligus,
     * sama seperti accept_unit() memproses semuanya bersamaan (DP & BP).
     * Setelah di-reject, baris ini otomatis hilang dari listing Request Invoice
     * (lihat Invoice::scopePendingUnitRequest()).
     */
    public function reject_unit(Request $request, $id)
    {
        $invoice = Invoice::findOrFail($id);
        $quote   = UnitQuotation::findOrFail($invoice->id_unit_quotation);

        $pendingInvoices = Invoice::where('id_unit_quotation', $quote->id)
            ->whereNull('no_invoice')
            ->whereNull('rejected_at')
            ->get();

        foreach ($pendingInvoices as $inv) {
            $inv->rejected_at     = now();
            $inv->rejected_reason = $request->input('reason');
            $inv->rejected_by     = Auth::id();
            $inv->save();
        }

        return redirect()->route('invoice.request')
            ->with('success', 'Pengajuan invoice unit berhasil di-reject.');
    }

    private function terbilang($number)
    {
        $number = abs($number);
        $words = array("", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");
        $result = "";

        if ($number < 12) {
            $result = " " . $words[$number];
        } elseif ($number < 20) {
            $result = $this->terbilang($number - 10) . " belas";
        } elseif ($number < 100) {
            $result = $this->terbilang((int) ($number / 10)) . " puluh " . $this->terbilang($number % 10);
        } elseif ($number < 200) {
            $result = " seratus " . $this->terbilang($number - 100);
        } elseif ($number < 1000) {
            $result = $this->terbilang((int) ($number / 100)) . " ratus " . $this->terbilang($number % 100);
        } elseif ($number < 2000) {
            $result = " seribu" . $this->terbilang($number - 1000);
        } elseif ($number < 1000000) {
            $result = $this->terbilang((int) ($number / 1000)) . " ribu " . $this->terbilang($number % 1000);
        } elseif ($number < 1000000000) {
            $result = $this->terbilang((int) ($number / 1000000)) . " juta " . $this->terbilang($number % 1000000);
        } elseif ($number < 1000000000000) {
            $result = $this->terbilang((int) ($number / 1000000000)) . " milyar " . $this->terbilang($number % 1000000000);
        } elseif ($number < 1000000000000000) {
            $result = $this->terbilang((int) ($number / 1000000000000)) . " trilyun " . $this->terbilang($number % 1000000000000);
        }

        return ucwords(trim($result));
    }

    private function terbilangEn($number)
    {
        $number = (int) abs($number);
        if ($number === 0) {
            return 'Zero';
        }

        return trim($this->terbilangEnPart($number));
    }

    private function terbilangEnPart($number)
    {
        $ones = ["", "One", "Two", "Three", "Four", "Five", "Six", "Seven", "Eight", "Nine", "Ten",
            "Eleven", "Twelve", "Thirteen", "Fourteen", "Fifteen", "Sixteen", "Seventeen", "Eighteen", "Nineteen"];
        $tens = ["", "", "Twenty", "Thirty", "Forty", "Fifty", "Sixty", "Seventy", "Eighty", "Ninety"];

        if ($number < 20) {
            return $ones[$number];
        } elseif ($number < 100) {
            $result = $tens[(int) ($number / 10)];
            if ($number % 10) {
                $result .= '-' . $ones[$number % 10];
            }
            return $result;
        } elseif ($number < 1000) {
            $result = $ones[(int) ($number / 100)] . ' Hundred';
            if ($number % 100) {
                $result .= ' ' . $this->terbilangEnPart($number % 100);
            }
            return $result;
        } elseif ($number < 1000000) {
            $result = $this->terbilangEnPart((int) ($number / 1000)) . ' Thousand';
            if ($number % 1000) {
                $result .= ' ' . $this->terbilangEnPart($number % 1000);
            }
            return $result;
        } elseif ($number < 1000000000) {
            $result = $this->terbilangEnPart((int) ($number / 1000000)) . ' Million';
            if ($number % 1000000) {
                $result .= ' ' . $this->terbilangEnPart($number % 1000000);
            }
            return $result;
        } elseif ($number < 1000000000000) {
            $result = $this->terbilangEnPart((int) ($number / 1000000000)) . ' Billion';
            if ($number % 1000000000) {
                $result .= ' ' . $this->terbilangEnPart($number % 1000000000);
            }
            return $result;
        }

        $result = $this->terbilangEnPart((int) ($number / 1000000000000)) . ' Trillion';
        if ($number % 1000000000000) {
            $result .= ' ' . $this->terbilangEnPart($number % 1000000000000);
        }
        return $result;
    }

    protected function convertToRoman($month)
    {
        $romanMonth = [
            1 => 'I',
            2 => 'II',
            3 => 'III',
            4 => 'IV',
            5 => 'V',
            6 => 'VI',
            7 => 'VII',
            8 => 'VIII',
            9 => 'IX',
            10 => 'X',
            11 => 'XI',
            12 => 'XII',
        ];

        return $romanMonth[$month];
    }
}
