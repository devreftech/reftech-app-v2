<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use App\Models\BankAdjustment;
use App\Models\BankReconciliation;
use App\Models\BankTransfer;
use App\Models\Expense;
use App\Models\Payment;
use App\Models\PurchasePayment;
use Auth;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;

class BankReconciliationController extends Controller
{
    /**
     * Display the Bank Reconciliation interface.
     */
    public function index(Request $request)
    {
        $banks = Bank::where('is_active', 1)->orderBy('bank')->get();
        $selectedBankId = $request->get('bank_id', $banks->first()?->id);
        $year = (int) $request->get('year', Carbon::now()->year);
        $month = (int) $request->get('month', Carbon::now()->month);

        $selectedBank = null;
        $reconciliation = null;
        $mutations = collect();
        $totalDebit = 0;
        $totalCredit = 0;
        $reconciledKeys = [];
        $statementBalance = 0;
        $notes = '';
        $reconciliationStatus = 'draft';

        if ($selectedBankId) {
            $selectedBank = Bank::findOrFail($selectedBankId);

            // Fetch existing reconciliation record if any
            $reconciliation = BankReconciliation::where('id_bank', $selectedBankId)
                ->where('period_year', $year)
                ->where('period_month', $month)
                ->first();

            if ($reconciliation) {
                $reconciledKeys = (array) ($reconciliation->reconciled_items ?? []);
                $statementBalance = (float) $reconciliation->statement_balance;
                $notes = $reconciliation->notes ?? '';
                $reconciliationStatus = $reconciliation->status ?? 'draft';
            }

            $startDate = Carbon::create($year, $month, 1)->startOfMonth()->toDateString();
            $endDate = Carbon::create($year, $month, 1)->endOfMonth()->toDateString();

            // 1. INFLOWS (DEBIT BANK / UANG MASUK)
            // A. Customer Payments (Payment Receipts AR)
            $customerPayments = Payment::where('id_bank', $selectedBankId)
                ->where('level', 1)
                ->whereBetween('date', [$startDate, $endDate])
                ->with(['quotation.pic.client', 'unitQuotation.client'])
                ->get()
                ->map(function ($p) use ($reconciledKeys) {
                    $client = $p->unitQuotation?->client?->company 
                        ?? ($p->quotation?->pic?->client?->company ?? 'Pelanggan');
                    $ref = $p->unitQuotation?->no_quote 
                        ?? ($p->quotation?->no_quote ?? '#RCPT-' . $p->id);
                    $key = 'cust_pay_' . $p->id;

                    return (object) [
                        'key' => $key,
                        'date' => $p->date,
                        'type' => 'INFLOW',
                        'category' => 'Penerimaan AR',
                        'badge_class' => 'bg-label-success',
                        'ref' => $ref,
                        'party' => $client,
                        'description' => ($p->method ?: 'Payment') . ($p->note ? ' - ' . $p->note : ''),
                        'debit' => (float) $p->amount,
                        'credit' => 0,
                        'is_reconciled' => in_array($key, $reconciledKeys),
                    ];
                });

            // B. Bank Transfer In
            $transfersIn = BankTransfer::where('id_to_bank', $selectedBankId)
                ->whereBetween('date', [$startDate, $endDate])
                ->with('fromBank')
                ->get()
                ->map(function ($t) use ($reconciledKeys) {
                    $key = 'transfer_in_' . $t->id;
                    return (object) [
                        'key' => $key,
                        'date' => $t->date,
                        'type' => 'INFLOW',
                        'category' => 'Transfer Masuk',
                        'badge_class' => 'bg-label-info',
                        'ref' => $t->transfer_number ?: ('#TRF-' . $t->id),
                        'party' => 'Dari ' . ($t->fromBank?->bank ?? 'Bank'),
                        'description' => $t->note ?: 'Transfer Antar Bank',
                        'debit' => (float) $t->amount,
                        'credit' => 0,
                        'is_reconciled' => in_array($key, $reconciledKeys),
                    ];
                });

            // C. Bank Adjustment In
            $adjustmentsIn = BankAdjustment::where('id_bank', $selectedBankId)
                ->where('type', 'in')
                ->whereBetween('date', [$startDate, $endDate])
                ->get()
                ->map(function ($adj) use ($reconciledKeys) {
                    $key = 'adj_in_' . $adj->id;
                    return (object) [
                        'key' => $key,
                        'date' => $adj->date,
                        'type' => 'INFLOW',
                        'category' => 'Penyesuaian Masuk',
                        'badge_class' => 'bg-label-primary',
                        'ref' => $adj->adjustment_number,
                        'party' => 'Internal Reftech',
                        'description' => $adj->reason,
                        'debit' => (float) $adj->difference,
                        'credit' => 0,
                        'is_reconciled' => in_array($key, $reconciledKeys),
                    ];
                });

            // 2. OUTFLOWS (CREDIT BANK / UANG KELUAR)
            // A. Purchase Payments (Supplier AP)
            $supplierPayments = PurchasePayment::where('id_bank', $selectedBankId)
                ->whereBetween('date', [$startDate, $endDate])
                ->with('supplier')
                ->get()
                ->map(function ($pay) use ($reconciledKeys) {
                    $key = 'supp_pay_' . $pay->id;
                    return (object) [
                        'key' => $key,
                        'date' => $pay->date,
                        'type' => 'OUTFLOW',
                        'category' => 'Pembayaran AP',
                        'badge_class' => 'bg-label-danger',
                        'ref' => $pay->payment_number,
                        'party' => $pay->supplier?->supplier ?: 'Supplier',
                        'description' => $pay->note ?: 'Pembayaran Hutang Pembelian',
                        'debit' => 0,
                        'credit' => (float) $pay->amount,
                        'is_reconciled' => in_array($key, $reconciledKeys),
                    ];
                });

            // B. Operational Expenses
            $expenses = Expense::where('id_bank', $selectedBankId)
                ->whereBetween('date', [$startDate, $endDate])
                ->get()
                ->map(function ($exp) use ($reconciledKeys) {
                    $key = 'expense_' . $exp->id;
                    return (object) [
                        'key' => $key,
                        'date' => $exp->date,
                        'type' => 'OUTFLOW',
                        'category' => 'Beban Operasional',
                        'badge_class' => 'bg-label-warning',
                        'ref' => $exp->no_expense ?: ('#EXP-' . $exp->id),
                        'party' => $exp->payee ?: 'Vendor / Karyawan',
                        'description' => $exp->memo ?: 'Beban Operasional Perusahaan',
                        'debit' => 0,
                        'credit' => (float) $exp->amount,
                        'is_reconciled' => in_array($key, $reconciledKeys),
                    ];
                });

            // C. Bank Transfer Out
            $transfersOut = BankTransfer::where('id_from_bank', $selectedBankId)
                ->whereBetween('date', [$startDate, $endDate])
                ->with('toBank')
                ->get()
                ->map(function ($t) use ($reconciledKeys) {
                    $key = 'transfer_out_' . $t->id;
                    return (object) [
                        'key' => $key,
                        'date' => $t->date,
                        'type' => 'OUTFLOW',
                        'category' => 'Transfer Keluar',
                        'badge_class' => 'bg-label-info',
                        'ref' => $t->transfer_number ?: ('#TRF-' . $t->id),
                        'party' => 'Ke ' . ($t->toBank?->bank ?? 'Bank'),
                        'description' => $t->note ?: 'Transfer Antar Bank',
                        'debit' => 0,
                        'credit' => (float) $t->amount,
                        'is_reconciled' => in_array($key, $reconciledKeys),
                    ];
                });

            // D. Bank Adjustment Out
            $adjustmentsOut = BankAdjustment::where('id_bank', $selectedBankId)
                ->where('type', 'out')
                ->whereBetween('date', [$startDate, $endDate])
                ->get()
                ->map(function ($adj) use ($reconciledKeys) {
                    $key = 'adj_out_' . $adj->id;
                    return (object) [
                        'key' => $key,
                        'date' => $adj->date,
                        'type' => 'OUTFLOW',
                        'category' => 'Penyesuaian Keluar',
                        'badge_class' => 'bg-label-secondary',
                        'ref' => $adj->adjustment_number,
                        'party' => 'Internal Reftech',
                        'description' => $adj->reason,
                        'debit' => 0,
                        'credit' => (float) $adj->difference,
                        'is_reconciled' => in_array($key, $reconciledKeys),
                    ];
                });

            // Combine mutations
            $mutations = $customerPayments->concat($transfersIn)
                ->concat($adjustmentsIn)
                ->concat($supplierPayments)
                ->concat($expenses)
                ->concat($transfersOut)
                ->concat($adjustmentsOut)
                ->sortBy(fn($m) => $m->date)
                ->values();

            $totalDebit = $mutations->sum('debit');
            $totalCredit = $mutations->sum('credit');
        }

        $years = range(Carbon::now()->year + 1, 2022);
        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        return view('pages.finance.reconciliation.index', compact(
            'banks',
            'selectedBankId',
            'selectedBank',
            'year',
            'month',
            'years',
            'months',
            'reconciliation',
            'mutations',
            'totalDebit',
            'totalCredit',
            'reconciledKeys',
            'statementBalance',
            'notes',
            'reconciliationStatus'
        ));
    }

    /**
     * Store or update bank reconciliation checklist and statement balance.
     */
    public function save(Request $request)
    {
        $request->validate([
            'id_bank' => 'required|exists:bank,id',
            'period_year' => 'required|integer',
            'period_month' => 'required|integer',
            'statement_balance' => 'required|numeric',
        ]);

        $bankId = (int) $request->input('id_bank');
        $year = (int) $request->input('period_year');
        $month = (int) $request->input('period_month');
        $statementBalance = (float) $request->input('statement_balance');
        $reconciledItems = (array) $request->input('reconciled_items', []);
        $notes = $request->input('notes');
        $status = $request->input('status', 'draft');

        $bank = Bank::findOrFail($bankId);
        $bookBalance = (float) $bank->saldo;
        $difference = $statementBalance - $bookBalance;

        $lastDayOfMonth = Carbon::create($year, $month, 1)->endOfMonth()->toDateString();

        $reconciliation = BankReconciliation::updateOrCreate(
            [
                'id_bank' => $bankId,
                'period_year' => $year,
                'period_month' => $month,
            ],
            [
                'statement_date' => $lastDayOfMonth,
                'statement_balance' => $statementBalance,
                'book_balance' => $bookBalance,
                'difference' => $difference,
                'status' => $status,
                'reconciled_items' => $reconciledItems,
                'notes' => $notes,
                'created_by' => Auth::id(),
            ]
        );

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Rekonsiliasi bank berhasil disimpan.',
                'difference' => $difference,
                'is_balanced' => abs($difference) < 1,
            ]);
        }

        return redirect()->back()->with('success', 'Rekonsiliasi bank berhasil disimpan.');
    }
}
