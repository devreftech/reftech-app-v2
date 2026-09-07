<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Bank;
use App\Models\DetailPayable;
use App\Models\DetailProductIn;
use App\Models\Payable;
use App\Models\ProductIn;
use App\Models\PurchasePayment;
use App\Models\ProjectExpense;
use App\Models\Supplier;
use App\Models\Retur;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PayableController extends Controller
{
    public function index_invoice()
    {
        $base = ProductIn::whereNotNull('invoice');
        $totalCount = $base->count();
        $totalAmount = (float) $base->sum('total');

        $paidCount = $base->clone()->where('accept', '1')->count();
        $paidAmount = (float) $base->clone()->where('accept', '1')->sum('total');

        $partialCount = $base->clone()->where('accept', '2')->count();
        $partialAmount = (float) $base->clone()->where('accept', '2')->sum('total');

        $unpaidCount = $base->clone()->where('accept', '0')->count();
        $unpaidAmount = (float) $base->clone()->where('accept', '0')->sum('total');

        // Due alerts (overdue & due soon <= 7 days)
        $today = Carbon::today();
        $unpaidOrPartial = ProductIn::whereNotNull('invoice')
            ->whereIn('accept', ['0', '2'])
            ->get();

        $overdueCount = 0;
        $overdueAmount = 0;
        $dueSoonCount = 0;
        $dueSoonAmount = 0;

        foreach ($unpaidOrPartial as $item) {
            $dueDateStr = $item->due_date;
            $remaining = $item->remaining_payable;
            if ($dueDateStr) {
                $due = Carbon::parse($dueDateStr)->startOfDay();
                if ($today->gt($due)) {
                    $overdueCount++;
                    $overdueAmount += $remaining;
                } elseif ($today->diffInDays($due, false) <= 7) {
                    $dueSoonCount++;
                    $dueSoonAmount += $remaining;
                }
            }
        }

        return view('pages.finance.payable.index-invoice', compact(
            'totalCount',
            'totalAmount',
            'paidCount',
            'paidAmount',
            'partialCount',
            'partialAmount',
            'unpaidCount',
            'unpaidAmount',
            'overdueCount',
            'overdueAmount',
            'dueSoonCount',
            'dueSoonAmount'
        ));
    }

    public function show_invoice($id)
    {
        $product = ProductIn::with(['supp', 'purchaseOrder', 'creator'])->findOrFail($id);
        $detProduct = DetailProductIn::where('id_product_in', $id)->get();
        $return = Retur::where('id_product_in', $id)->get();
        $banks = Bank::orderBy('bank')->get();
        $payments = $product->payments()->with(['bank', 'creator'])->orderBy('date', 'desc')->get();

        return view('pages.finance.payable.detail-invoice', compact('product', 'detProduct', 'return', 'banks', 'payments'));
    }

    public function index_aging()
    {
        $base = ProductIn::whereIn('accept', ['0', '2'])->whereNotNull('invoice');

        $unpaid = $base->clone()->get();
        $bucketCurrent = $base->clone()->whereRaw('DATEDIFF(CURDATE(), COALESCE(date_invoice, date)) BETWEEN 0 AND 30')->get();
        $bucket31to60 = $base->clone()->whereRaw('DATEDIFF(CURDATE(), COALESCE(date_invoice, date)) BETWEEN 31 AND 60')->get();
        $bucket61to90 = $base->clone()->whereRaw('DATEDIFF(CURDATE(), COALESCE(date_invoice, date)) BETWEEN 61 AND 90')->get();
        $bucket90plus = $base->clone()->whereRaw('DATEDIFF(CURDATE(), COALESCE(date_invoice, date)) > 90')->get();

        return view('pages.finance.payable.index-aging', compact(
            'unpaid',
            'bucketCurrent',
            'bucket31to60',
            'bucket61to90',
            'bucket90plus'
        ));
    }

    public function show_aging($id)
    {
        $product = ProductIn::findOrFail($id);
        $detProduct = DetailProductIn::where('id_product_in', $id)->get();
        $today = Carbon::today();
        $baseDate = $product->date_invoice ?: $product->date;
        $diffDue = $baseDate ? $today->diffInDays(Carbon::parse($baseDate), false) : 0;
        $banks = Bank::orderBy('bank')->get();
        $payments = $product->payments()->with(['bank', 'creator'])->orderBy('date', 'desc')->get();

        return view('pages.finance.payable.detail-aging', compact('product', 'detProduct', 'diffDue', 'banks', 'payments'));
    }

    public function index_receipt()
    {
        $base = ProductIn::whereNotNull('invoice');
        $totalCount = $base->count();
        $receipt = (float) $base->sum('total');

        $paidCount = $base->clone()->where('accept', '1')->count();
        $paid = (float) $base->clone()->where('accept', '1')->sum('total');

        $partialCount = $base->clone()->where('accept', '2')->count();
        $partial = (float) $base->clone()->where('accept', '2')->sum('total');

        $unpaidCount = $base->clone()->where('accept', '0')->count();
        $unpaid = (float) $base->clone()->where('accept', '0')->sum('total');

        return view('pages.finance.payable.index-receipt', compact(
            'totalCount',
            'receipt',
            'paidCount',
            'paid',
            'partialCount',
            'partial',
            'unpaidCount',
            'unpaid'
        ));
    }

    public function show_receipt($id)
    {
        $product = ProductIn::with(['supp', 'purchaseOrder'])->findOrFail($id);

        $receipt = ProductIn::where('id', $id)
            ->selectRaw("
            CONCAT(
                '#PAY-',
                LPAD(
                    (
                        SELECT COUNT(*)
                        FROM product_in pi2
                        WHERE YEAR(pi2.date) = YEAR(product_in.date)
                          AND pi2.id <= product_in.id
                    ),
                    3,
                    '0'
                ),
                '-',
                RIGHT(YEAR(product_in.date), 2)
            ) as no_receipt
        ")
            ->value('no_receipt');

        $detProduct = DetailProductIn::where('id_product_in', $id)->get();
        $banks = Bank::orderBy('bank')->get();
        $payments = $product->payments()->with(['bank', 'creator'])->orderBy('date', 'desc')->get();

        return view(
            'pages.finance.payable.detail-receipt',
            compact('receipt', 'product', 'detProduct', 'banks', 'payments')
        );
    }

    /**
     * Point 1, 2, 3: Record payment (full or installment), upload proof of transfer, and deduct bank balance
     */
    public function storePayment(Request $request, $id)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'date' => 'required|date',
            'id_bank' => 'required|exists:bank,id',
            'payment_method' => 'nullable|string',
            'note' => 'nullable|string',
            'proof_file' => 'nullable|file|mimes:jpeg,png,jpg,pdf,webp|max:5120',
        ]);

        $product = ProductIn::findOrFail($id);
        $bank = Bank::findOrFail($request->id_bank);

        $proofPath = null;
        if ($request->hasFile('proof_file')) {
            $file = $request->file('proof_file');
            $filename = 'proof_ap_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $proofPath = $file->storeAs('payable_proofs', $filename, 'public');
        }

        $countToday = PurchasePayment::whereDate('created_at', Carbon::today())->count() + 1;
        $paymentNumber = 'PAY/AP/' . date('Ymd') . '/' . str_pad($countToday, 4, '0', STR_PAD_LEFT);

        $payment = PurchasePayment::create([
            'id_product_in' => $product->id,
            'id_supplier' => $product->id_supplier,
            'id_bank' => $bank->id,
            'payment_number' => $paymentNumber,
            'date' => $request->date,
            'amount' => $request->amount,
            'payment_method' => $request->payment_method ?? 'Bank Transfer',
            'proof_file' => $proofPath,
            'note' => $request->note,
            'created_by' => Auth::id() ?? 1,
        ]);

        // Auto deduct bank balance
        $bank->decrement('saldo', $request->amount);

        // Recalculate ProductIn accept status
        $totalPaid = (float) $product->payments()->sum('amount');
        if ($totalPaid >= (float) $product->total) {
            $product->accept = '1'; // Fully Paid
            $product->date_payment = $request->date;
        } elseif ($totalPaid > 0) {
            $product->accept = '2'; // Partially Paid
            $product->date_payment = $request->date;
        } else {
            $product->accept = '0'; // Unpaid
        }
        $product->save();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Pembayaran berhasil dicatat & saldo bank berhasil dipotong.',
                'payment' => $payment,
                'total_paid' => $totalPaid,
                'remaining' => $product->remaining_payable,
                'accept' => $product->accept,
            ]);
        }

        return redirect()->back()->with('success', 'Pembayaran sebesar Rp ' . number_format($request->amount, 0, ',', '.') . ' berhasil dicatat!');
    }

    /**
     * Delete payment installment & restore bank balance
     */
    public function destroyPayment($id)
    {
        $payment = PurchasePayment::findOrFail($id);
        $bank = Bank::find($payment->id_bank);

        if ($bank) {
            $bank->increment('saldo', $payment->amount);
        }

        if ($payment->proof_file && Storage::disk('public')->exists($payment->proof_file)) {
            Storage::disk('public')->delete($payment->proof_file);
        }

        $productId = $payment->id_product_in;
        $payment->delete();

        if ($productId) {
            $product = ProductIn::find($productId);
            if ($product) {
                $totalPaid = (float) $product->payments()->sum('amount');
                if ($totalPaid >= (float) $product->total) {
                    $product->accept = '1';
                } elseif ($totalPaid > 0) {
                    $product->accept = '2';
                } else {
                    $product->accept = '0';
                }
                $product->save();
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Data pembayaran berhasil dihapus & saldo bank telah dikembalikan.',
        ]);
    }

    /**
     * Point 5: Statement of Account / Kartu Hutang Supplier
     */
    public function supplierStatement(Request $request)
    {
        $suppliers = Supplier::orderBy('supplier')->get();
        $selectedSupplierId = $request->get('supplier_id');
        $startDate = $request->get('start_date', Carbon::now()->subMonths(3)->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', Carbon::now()->toDateString());

        $selectedSupplier = null;
        $openingBalance = 0;
        $transactions = collect();
        $totalDebit = 0;
        $totalCredit = 0;
        $endingBalance = 0;

        if ($selectedSupplierId) {
            $selectedSupplier = Supplier::findOrFail($selectedSupplierId);

            // Calculate opening balance before start_date
            $prevPurchases = (float) ProductIn::where('id_supplier', $selectedSupplierId)
                ->where('date', '<', $startDate)
                ->sum('total');

            $prevPayments = (float) PurchasePayment::where('id_supplier', $selectedSupplierId)
                ->where('date', '<', $startDate)
                ->sum('amount');

            $prevLegacyPaid = (float) ProductIn::where('id_supplier', $selectedSupplierId)
                ->where('date', '<', $startDate)
                ->where('accept', '1')
                ->whereDoesntHave('payments')
                ->sum('total');

            $openingBalance = max(0, $prevPurchases - ($prevPayments + $prevLegacyPaid));

            // Current transactions in range
            $purchases = ProductIn::where('id_supplier', $selectedSupplierId)
                ->whereBetween('date', [$startDate, $endDate])
                ->get()
                ->map(function ($p) {
                    return [
                        'date' => $p->date,
                        'type' => 'PURCHASE',
                        'badge_class' => 'bg-label-primary',
                        'ref' => $p->invoice ?: $p->no_product_in,
                        'description' => 'Pembelian Barang (DO: ' . ($p->no_do ?: '-') . ')',
                        'debit' => (float) $p->total,
                        'credit' => 0,
                        'link' => route('payable.show_invoice', $p->id),
                        'proof_file' => null,
                    ];
                });

            $payments = PurchasePayment::where('id_supplier', $selectedSupplierId)
                ->whereBetween('date', [$startDate, $endDate])
                ->with('bank')
                ->get()
                ->map(function ($pay) {
                    $bankName = $pay->bank ? $pay->bank->bank : 'Kas/Bank';
                    return [
                        'date' => $pay->date,
                        'type' => 'PAYMENT',
                        'badge_class' => 'bg-label-success',
                        'ref' => $pay->payment_number,
                        'description' => 'Pembayaran via ' . $bankName . ($pay->note ? ' - ' . $pay->note : ''),
                        'debit' => 0,
                        'credit' => (float) $pay->amount,
                        'link' => $pay->id_product_in ? route('payable.show_receipt', $pay->id_product_in) : null,
                        'proof_file' => $pay->proof_file,
                    ];
                });

            // Legacy fully paid without individual payment records in date range
            $legacyPaid = ProductIn::where('id_supplier', $selectedSupplierId)
                ->whereBetween('date_payment', [$startDate, $endDate])
                ->where('accept', '1')
                ->whereDoesntHave('payments')
                ->get()
                ->map(function ($lp) {
                    return [
                        'date' => $lp->date_payment ?: $lp->date,
                        'type' => 'PAYMENT',
                        'badge_class' => 'bg-label-success',
                        'ref' => '#PAY-LEGACY-' . $lp->id,
                        'description' => 'Pelunasan Faktur ' . ($lp->invoice ?: $lp->no_product_in),
                        'debit' => 0,
                        'credit' => (float) $lp->total,
                        'link' => route('payable.show_receipt', $lp->id),
                        'proof_file' => null,
                    ];
                });

            $all = $purchases->concat($payments)->concat($legacyPaid)->sortBy('date')->values();

            $running = $openingBalance;
            $transactions = $all->map(function ($item) use (&$running, &$totalDebit, &$totalCredit) {
                $totalDebit += $item['debit'];
                $totalCredit += $item['credit'];
                $running = $running + $item['debit'] - $item['credit'];
                $item['balance'] = $running;
                return (object) $item;
            });

            $endingBalance = $running;
        }

        return view('pages.finance.payable.supplier-statement', compact(
            'suppliers',
            'selectedSupplierId',
            'selectedSupplier',
            'startDate',
            'endDate',
            'openingBalance',
            'transactions',
            'totalDebit',
            'totalCredit',
            'endingBalance'
        ));
    }

    /**
     * Print Statement of Account
     */
    public function supplierStatementPrint(Request $request, $id)
    {
        $selectedSupplier = Supplier::findOrFail($id);
        $startDate = $request->get('start_date', Carbon::now()->subMonths(3)->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', Carbon::now()->toDateString());

        $prevPurchases = (float) ProductIn::where('id_supplier', $id)
            ->where('date', '<', $startDate)
            ->sum('total');

        $prevPayments = (float) PurchasePayment::where('id_supplier', $id)
            ->where('date', '<', $startDate)
            ->sum('amount');

        $prevLegacyPaid = (float) ProductIn::where('id_supplier', $id)
            ->where('date', '<', $startDate)
            ->where('accept', '1')
            ->whereDoesntHave('payments')
            ->sum('total');

        $openingBalance = max(0, $prevPurchases - ($prevPayments + $prevLegacyPaid));

        $purchases = ProductIn::where('id_supplier', $id)
            ->whereBetween('date', [$startDate, $endDate])
            ->get()
            ->map(function ($p) {
                return [
                    'date' => $p->date,
                    'type' => 'PURCHASE',
                    'ref' => $p->invoice ?: $p->no_product_in,
                    'description' => 'Pembelian Barang (DO: ' . ($p->no_do ?: '-') . ')',
                    'debit' => (float) $p->total,
                    'credit' => 0,
                ];
            });

        $payments = PurchasePayment::where('id_supplier', $id)
            ->whereBetween('date', [$startDate, $endDate])
            ->with('bank')
            ->get()
            ->map(function ($pay) {
                $bankName = $pay->bank ? $pay->bank->bank : 'Kas/Bank';
                return [
                    'date' => $pay->date,
                    'type' => 'PAYMENT',
                    'ref' => $pay->payment_number,
                    'description' => 'Pembayaran via ' . $bankName . ($pay->note ? ' - ' . $pay->note : ''),
                    'debit' => 0,
                    'credit' => (float) $pay->amount,
                ];
            });

        $legacyPaid = ProductIn::where('id_supplier', $id)
            ->whereBetween('date_payment', [$startDate, $endDate])
            ->where('accept', '1')
            ->whereDoesntHave('payments')
            ->get()
            ->map(function ($lp) {
                return [
                    'date' => $lp->date_payment ?: $lp->date,
                    'type' => 'PAYMENT',
                    'ref' => '#PAY-LEGACY-' . $lp->id,
                    'description' => 'Pelunasan Faktur ' . ($lp->invoice ?: $lp->no_product_in),
                    'debit' => 0,
                    'credit' => (float) $lp->total,
                ];
            });

        $all = $purchases->concat($payments)->concat($legacyPaid)->sortBy('date')->values();

        $totalDebit = 0;
        $totalCredit = 0;
        $running = $openingBalance;
        $transactions = $all->map(function ($item) use (&$running, &$totalDebit, &$totalCredit) {
            $totalDebit += $item['debit'];
            $totalCredit += $item['credit'];
            $running = $running + $item['debit'] - $item['credit'];
            $item['balance'] = $running;
            return (object) $item;
        });

        $endingBalance = $running;

        return view('pages.finance.payable.supplier-statement-print', compact(
            'selectedSupplier',
            'startDate',
            'endDate',
            'openingBalance',
            'transactions',
            'totalDebit',
            'totalCredit',
            'endingBalance'
        ));
    }

    /**
     * Export AP Aging Report to Excel/CSV.
     */
    public function exportAgingExcel(Request $request)
    {
        $base = ProductIn::whereIn('accept', ['0', '2'])->whereNotNull('invoice')->with(['supplier', 'purchaseOrder'])->get();
        $filename = 'AP_Aging_Report_' . date('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($base) {
            $output = fopen('php://output', 'w');
            fputs($output, "\xEF\xBB\xBF");

            fputcsv($output, [
                'No. Faktur / Invoice',
                'No. PO',
                'Supplier',
                'Tgl Invoice',
                'Jatuh Tempo',
                'Total Faktur (Rp)',
                'Sudah Dibayar (Rp)',
                'Sisa Hutang (Rp)',
                'Umur Hutang (Hari)',
                'Kategori Umur (Bracket)',
                'Status Jatuh Tempo',
            ]);

            $today = Carbon::today();
            foreach ($base as $row) {
                $baseDate = $row->date_invoice ?: $row->date;
                $days = $baseDate ? $today->diffInDays(Carbon::parse($baseDate)) : 0;
                $bracket = '0 - 30 Hari';
                if ($days > 90) {
                    $bracket = '> 90 Hari';
                } elseif ($days >= 61) {
                    $bracket = '61 - 90 Hari';
                } elseif ($days >= 31) {
                    $bracket = '31 - 60 Hari';
                }

                $total = (float) $row->total;
                $paid = (float) $row->total_paid;
                $remaining = (float) $row->remaining_payable;
                $supplierName = $row->supplier?->supplier ?? $row->supplier ?? '-';

                fputcsv($output, [
                    $row->invoice ?: $row->no_product_in,
                    $row->purchaseOrder?->no_po ?? '-',
                    $supplierName,
                    $row->date_invoice ? Carbon::parse($row->date_invoice)->format('d/m/Y') : ($row->date ? Carbon::parse($row->date)->format('d/m/Y') : '-'),
                    $row->due_date ? Carbon::parse($row->due_date)->format('d/m/Y') : '-',
                    $total,
                    $paid,
                    $remaining,
                    $days,
                    $bracket,
                    strtoupper($row->due_status),
                ]);
            }
            fclose($output);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export Supplier SOA to Excel/CSV.
     */
    public function exportStatementExcel(Request $request, $id)
    {
        $selectedSupplier = Supplier::findOrFail($id);
        $startDate = $request->get('start_date', Carbon::now()->subMonths(3)->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', Carbon::now()->toDateString());

        $prevPurchases = (float) ProductIn::where('id_supplier', $id)->where('date', '<', $startDate)->sum('total');
        $prevPayments = (float) PurchasePayment::where('id_supplier', $id)->where('date', '<', $startDate)->sum('amount');
        $prevLegacyPaid = (float) ProductIn::where('id_supplier', $id)->where('date', '<', $startDate)->where('accept', '1')->whereDoesntHave('payments')->sum('total');
        $openingBalance = max(0, $prevPurchases - ($prevPayments + $prevLegacyPaid));

        $purchases = ProductIn::where('id_supplier', $id)->whereBetween('date', [$startDate, $endDate])->get()->map(fn($p) => [
            'date' => $p->date,
            'type' => 'PURCHASE',
            'ref' => $p->invoice ?: $p->no_product_in,
            'description' => 'Pembelian Barang (DO: ' . ($p->no_do ?: '-') . ')',
            'debit' => (float) $p->total,
            'credit' => 0,
        ]);

        $payments = PurchasePayment::where('id_supplier', $id)->whereBetween('date', [$startDate, $endDate])->with('bank')->get()->map(fn($pay) => [
            'date' => $pay->date,
            'type' => 'PAYMENT',
            'ref' => $pay->payment_number,
            'description' => 'Pembayaran via ' . ($pay->bank?->bank ?: 'Kas/Bank') . ($pay->note ? ' - ' . $pay->note : ''),
            'debit' => 0,
            'credit' => (float) $pay->amount,
        ]);

        $legacyPaid = ProductIn::where('id_supplier', $id)->whereBetween('date_payment', [$startDate, $endDate])->where('accept', '1')->whereDoesntHave('payments')->get()->map(fn($lp) => [
            'date' => $lp->date_payment ?: $lp->date,
            'type' => 'PAYMENT',
            'ref' => '#PAY-LEGACY-' . $lp->id,
            'description' => 'Pelunasan Faktur ' . ($lp->invoice ?: $lp->no_product_in),
            'debit' => 0,
            'credit' => (float) $lp->total,
        ]);

        $all = $purchases->concat($payments)->concat($legacyPaid)->sortBy('date')->values();
        $cleanSupplierName = preg_replace('/[^A-Za-z0-9_\-]/', '_', $selectedSupplier->supplier);
        $filename = 'Kartu_Hutang_' . $cleanSupplierName . '_' . date('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($selectedSupplier, $startDate, $endDate, $openingBalance, $all) {
            $output = fopen('php://output', 'w');
            fputs($output, "\xEF\xBB\xBF");

            fputcsv($output, ['KARTU HUTANG SUPPLIER (STATEMENT OF ACCOUNT)']);
            fputcsv($output, ['Supplier', $selectedSupplier->supplier]);
            fputcsv($output, ['Periode', Carbon::parse($startDate)->format('d/m/Y') . ' s/d ' . Carbon::parse($endDate)->format('d/m/Y')]);
            fputcsv($output, ['Saldo Awal', $openingBalance]);
            fputcsv($output, []);

            fputcsv($output, ['Tanggal', 'Tipe', 'No. Referensi / Invoice', 'Keterangan Transaksi', 'Debit / Pembelian (Rp)', 'Kredit / Pembayaran (Rp)', 'Saldo Hutang (Rp)']);

            $running = $openingBalance;
            $totalDebit = 0;
            $totalCredit = 0;

            foreach ($all as $item) {
                $totalDebit += $item['debit'];
                $totalCredit += $item['credit'];
                $running = $running + $item['debit'] - $item['credit'];

                fputcsv($output, [
                    Carbon::parse($item['date'])->format('d/m/Y'),
                    $item['type'],
                    $item['ref'],
                    $item['description'],
                    $item['debit'] > 0 ? $item['debit'] : 0,
                    $item['credit'] > 0 ? $item['credit'] : 0,
                    $running,
                ]);
            }

            fputcsv($output, ['TOTAL', '', '', '', $totalDebit, $totalCredit, $running]);
            fclose($output);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Point 6: Non-Inventory Project Expenses in AP
     */
    public function index_expenses(Request $request)
    {
        $query = ProjectExpense::with(['pending', 'kanbanTask', 'user', 'payments.bank']);

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('date', [$request->start_date, $request->end_date]);
        }

        $expenses = $query->orderByDesc('date')->get();

        $totalAmount = $expenses->sum('amount');
        $totalPaid = $expenses->sum(function ($e) {
            return $e->payments->sum('amount');
        });
        $totalUnpaid = max(0, $totalAmount - $totalPaid);
        $banks = Bank::orderBy('bank')->get();

        return view('pages.finance.payable.expenses', compact(
            'expenses',
            'totalAmount',
            'totalPaid',
            'totalUnpaid',
            'banks'
        ));
    }

    /**
     * Pay Non-Inventory Project Expense
     */
    public function payExpense(Request $request, $id)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'date' => 'required|date',
            'id_bank' => 'required|exists:bank,id',
            'payment_method' => 'nullable|string',
            'note' => 'nullable|string',
            'proof_file' => 'nullable|file|mimes:jpeg,png,jpg,pdf,webp|max:5120',
        ]);

        $expense = ProjectExpense::findOrFail($id);
        $bank = Bank::findOrFail($request->id_bank);

        $proofPath = null;
        if ($request->hasFile('proof_file')) {
            $file = $request->file('proof_file');
            $filename = 'proof_exp_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $proofPath = $file->storeAs('payable_proofs', $filename, 'public');
        }

        $countToday = PurchasePayment::whereDate('created_at', Carbon::today())->count() + 1;
        $paymentNumber = 'PAY/EXP/' . date('Ymd') . '/' . str_pad($countToday, 4, '0', STR_PAD_LEFT);

        $payment = PurchasePayment::create([
            'id_project_expense' => $expense->id,
            'id_bank' => $bank->id,
            'payment_number' => $paymentNumber,
            'date' => $request->date,
            'amount' => $request->amount,
            'payment_method' => $request->payment_method ?? 'Bank Transfer',
            'proof_file' => $proofPath,
            'note' => $request->note,
            'created_by' => Auth::id() ?? 1,
        ]);

        $bank->decrement('saldo', $request->amount);

        return redirect()->back()->with('success', 'Biaya project berhasil dibayar sebesar Rp ' . number_format($request->amount, 0, ',', '.'));
    }

    public function storePayable(Request $request)
    {
        $bank = Bank::find($request->bank);
        $payable = new Payable;
        $payable->id_bank = $request->bank;
        $payable->no_voucher = $request->no_voucher;
        $payable->no_cheque = $request->no_cheque;
        $payable->memo = $request->detail;
        $payable->payee = $request->payee;
        $payable->date = $request->date;
        $payable->amount = $request->total;
        $payableSave = $payable->save();
        $dpayableSave = true;
        if ($payableSave && $request->has('account')) {
            foreach ($request->account as $item => $value) {
                $dpayable = new DetailPayable();
                $dpayable->id_payable = $payable->id;
                $dpayable->id_account = $request->account[$item];
                $dpayable->memo = $request->memo[$item] ?? '';
                $dpayable->amount = $request->amount[$item] ?? 0;
                $dpayableSave = $dpayable->save();
            }
        }
        if ($bank) {
            $bank->saldo -= $request->total;
            $bank->save();
        }
        if ($payableSave && $dpayableSave) {
            return redirect('payable')->with('success', 'Data berhasil disimpan');
        }
        return redirect()->back()->with('error', 'Gagal menyimpan data');
    }

    public function showPayable($id)
    {
        $payable = Payable::find($id);
        $detailPayable = DetailPayable::where('id_payable', $id)->get();
        $terbilang = $this->capitalizeWords(
            trim($this->terbilang($payable->amount ?? 0))
        );
        return view('pages.finance.payable.detail', compact('detailPayable', 'payable', 'terbilang'));
    }

    public function showPayablePrint($id)
    {
        $payable = Payable::find($id);
        $detailPayable = DetailPayable::where('id_payable', $id)->get();
        $terbilang = $this->capitalizeWords(
            trim($this->terbilang($payable->amount ?? 0))
        );
        return view('pages.finance.payable.detail-print', compact('detailPayable', 'payable', 'terbilang'));
    }

    public function deletePayable($id)
    {
        $payable = Payable::find($id);
        if (!$payable) {
            return 0;
        }
        $bank = Bank::find($payable->id_bank);
        $detailPayable = DetailPayable::where('id_payable', $id)->get();
        foreach ($detailPayable as $key) {
            $key->delete();
        }
        if ($bank) {
            $bank->saldo += $payable->amount;
            $bank->save();
        }
        $payableDel = $payable->delete();
        return $payableDel ? 1 : 0;
    }

    public function addPph(Request $request, $id)
    {
        $payment = ProductIn::find($id);
        $payment->pph = $request->pph;
        $paymentSave = $payment->save();
        if ($paymentSave) {
            return redirect('/payable/receipt/' . $id)->with('success', 'PPH berhasil ditambahkan!');
        }
    }

    public function editDate(Request $request, $id)
    {
        $payment = ProductIn::find($id);
        $payment->date_payment = $request->date;
        $paymentSave = $payment->save();
        if ($paymentSave) {
            return redirect('/payable/receipt/' . $id)->with('success', 'Date Telah Diubah!');
        }
    }

    public function confirmReceipt(Request $request, $id)
    {
        $product = ProductIn::findOrFail($id);
        $product->accept = '1';
        if ($request->filled('date_payment')) {
            $product->date_payment = $request->date_payment;
        } elseif (!$product->date_payment) {
            $product->date_payment = Carbon::now()->toDateString();
        }
        $product->save();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Pembayaran berhasil dikonfirmasi (PAID).'
            ]);
        }

        return redirect()->route('payable.show_receipt', $id)->with('success', 'Pembayaran berhasil dikonfirmasi (PAID).');
    }

    public function unconfirmReceipt(Request $request, $id)
    {
        $product = ProductIn::findOrFail($id);
        $product->accept = '0';
        $product->save();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Konfirmasi pembayaran berhasil dibatalkan (UNPAID).'
            ]);
        }

        return redirect()->route('payable.show_receipt', $id)->with('success', 'Konfirmasi pembayaran berhasil dibatalkan (UNPAID).');
    }

    private function terbilang($number)
    {
        $number = abs($number);
        $words = ["", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas"];

        if ($number < 12)
            return " " . $words[$number];
        if ($number < 20)
            return $this->terbilang($number - 10) . " belas";
        if ($number < 100)
            return $this->terbilang(floor($number / 10)) . " puluh" . $this->terbilang($number % 10);
        if ($number < 200)
            return " seratus" . $this->terbilang($number - 100);
        if ($number < 1000)
            return $this->terbilang(floor($number / 100)) . " ratus" . $this->terbilang($number % 100);
        if ($number < 2000)
            return " seribu" . $this->terbilang($number - 1000);
        if ($number < 1000000)
            return $this->terbilang(floor($number / 1000)) . " ribu" . $this->terbilang($number % 1000);
        if ($number < 1000000000)
            return $this->terbilang(floor($number / 1000000)) . " juta" . $this->terbilang($number % 1000000);
        if ($number < 1000000000000)
            return $this->terbilang(floor($number / 1000000000)) . " miliar" . $this->terbilang($number % 1000000000);

        return "";
    }

    private function capitalizeWords($str)
    {
        return ucwords($str);
    }
}
