<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\ProductIn;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;

class TaxReportController extends Controller
{
    /**
     * Display Tax Report (Rekapitulasi PPN Keluaran, PPN Masukan, dan PPh).
     */
    public function index(Request $request)
    {
        $year = (int) $request->get('year', Carbon::now()->year);
        $month = $request->get('month', Carbon::now()->month); // 1-12 or 'all'
        $flag = $request->get('flag', 'all'); // 'all', 'reftech', 'kojisha'

        // 1. PPN Keluaran (Output VAT / Sales Invoices with Tax)
        // Sparepart Quotation Invoices
        $spOutputQuery = Invoice::join('quotation as q', 'q.id', '=', 'invoice.id_quotation')
            ->join('pic', 'pic.id', '=', 'q.id_pic')
            ->join('client as c', 'c.id', '=', 'pic.id_client')
            ->whereNotNull('invoice.no_invoice')
            ->where('q.tax', '11')
            ->when($year, fn($q2) => $q2->whereYear('invoice.date', $year))
            ->when($month && $month !== 'all', fn($q2) => $q2->whereMonth('invoice.date', (int)$month))
            ->when($flag && $flag !== 'all', function ($q2) use ($flag) {
                $q2->where('c.info', 'like', '%' . $flag . '%');
            });

        $spOutput = $spOutputQuery->select([
            'invoice.id',
            'invoice.no_invoice',
            'invoice.no_po',
            'invoice.date',
            'c.company',
            'c.npwp',
            'c.info as bendera',
            'q.no_quote',
            'q.harga_total as gross',
            DB::raw("CASE WHEN q.total_no_tax > 0 THEN q.total_no_tax ELSE ROUND(q.harga_total / 1.11) END as dpp"),
            DB::raw("CASE WHEN q.total_no_tax > 0 THEN (q.harga_total - q.total_no_tax) ELSE (q.harga_total - ROUND(q.harga_total / 1.11)) END as ppn"),
        ])->get()->map(function ($row) {
            $row->source = 'Sparepart';
            return $row;
        });

        // Unit Quotation Invoices
        $uqOutputQuery = Invoice::join('unit_quotation as uq', 'uq.id', '=', 'invoice.id_unit_quotation')
            ->join('client as c', 'c.id', '=', 'uq.id_client')
            ->whereNotNull('invoice.no_invoice')
            ->where('uq.tax', 1)
            ->when($year, fn($q2) => $q2->whereYear('invoice.date', $year))
            ->when($month && $month !== 'all', fn($q2) => $q2->whereMonth('invoice.date', (int)$month))
            ->when($flag && $flag !== 'all', function ($q2) use ($flag) {
                $q2->where('c.info', 'like', '%' . $flag . '%');
            });

        $uqOutput = $uqOutputQuery->select([
            'invoice.id',
            'invoice.no_invoice',
            'invoice.no_po',
            'invoice.date',
            'c.company',
            'c.npwp',
            'c.info as bendera',
            'uq.no_quote',
            'uq.total as gross',
            DB::raw("CASE WHEN uq.tax_amount > 0 THEN (uq.total - uq.tax_amount) ELSE ROUND(uq.total / 1.11) END as dpp"),
            DB::raw("CASE WHEN uq.tax_amount > 0 THEN uq.tax_amount ELSE (uq.total - ROUND(uq.total / 1.11)) END as ppn"),
        ])->get()->map(function ($row) {
            $row->source = 'Unit';
            return $row;
        });

        $outputVatList = $spOutput->concat($uqOutput)->sortByDesc('date')->values();
        $totalOutputDpp = $outputVatList->sum('dpp');
        $totalOutputPpn = $outputVatList->sum('ppn');
        $totalOutputGross = $outputVatList->sum('gross');

        // 2. PPN Masukan (Input VAT / Purchase Invoices with Tax)
        $inputVatQuery = ProductIn::leftJoin('supplier as s', 's.id', '=', 'product_in.id_supplier')
            ->where(function ($q) {
                $q->where('product_in.tax', '11')
                  ->orWhere('product_in.tax', 1);
            })
            ->when($year, fn($q2) => $q2->whereYear('product_in.date', $year))
            ->when($month && $month !== 'all', fn($q2) => $q2->whereMonth('product_in.date', (int)$month));

        $inputVatList = $inputVatQuery->select([
            'product_in.id',
            'product_in.invoice as no_invoice',
            'product_in.no_do',
            'product_in.no_product_in',
            'product_in.date',
            DB::raw("COALESCE(s.supplier, product_in.supplier, 'Vendor') as supplier_name"),
            's.npwp',
            'product_in.total as gross',
            DB::raw("CASE WHEN product_in.total_no_tax > 0 THEN product_in.total_no_tax ELSE ROUND(product_in.total / 1.11) END as dpp"),
            DB::raw("CASE WHEN product_in.total_no_tax > 0 THEN (product_in.total - product_in.total_no_tax) ELSE (product_in.total - ROUND(product_in.total / 1.11)) END as ppn"),
        ])->orderByDesc('product_in.date')->get();

        $totalInputDpp = $inputVatList->sum('dpp');
        $totalInputPpn = $inputVatList->sum('ppn');
        $totalInputGross = $inputVatList->sum('gross');

        // 3. Net PPN
        $netPpn = $totalOutputPpn - $totalInputPpn; // Positive = Kurang Bayar (wajib disetor ke negara), Negative = Lebih Bayar

        // 4. PPh Withholding (Pemotongan Pajak PPh 23 dari Payment)
        $pphPayments = Payment::where('pph', '>', 0)
            ->when($year, fn($q2) => $q2->whereYear('date', $year))
            ->when($month && $month !== 'all', fn($q2) => $q2->whereMonth('date', (int)$month))
            ->with(['quotation.pic.client', 'unitQuotation.client', 'bank'])
            ->orderByDesc('date')
            ->get()
            ->map(function ($p) {
                $clientName = $p->unitQuotation?->client?->company 
                    ?? ($p->quotation?->pic?->client?->company ?? 'Pelanggan');
                return [
                    'id' => $p->id,
                    'date' => $p->date,
                    'client' => $clientName,
                    'bank' => $p->bank?->bank ?: '-',
                    'amount' => (float)$p->amount,
                    'pph' => (float)$p->pph,
                    'note' => $p->note ?: 'Pemotongan PPh Penerimaan',
                ];
            });

        $totalPph = $pphPayments->sum('pph');

        $years = range(Carbon::now()->year + 1, 2022);

        return view('pages.finance.tax.index', compact(
            'year',
            'month',
            'flag',
            'years',
            'outputVatList',
            'totalOutputDpp',
            'totalOutputPpn',
            'totalOutputGross',
            'inputVatList',
            'totalInputDpp',
            'totalInputPpn',
            'totalInputGross',
            'netPpn',
            'pphPayments',
            'totalPph'
        ));
    }
}
