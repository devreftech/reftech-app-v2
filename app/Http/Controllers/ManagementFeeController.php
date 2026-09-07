<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use App\Models\Client;
use App\Models\ManualManagementFee;
use App\Models\UnitQuotation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ManagementFeeController extends Controller
{
    /**
     * Tampilkan daftar Management Fee di bawah modul Finance (Tab Penawaran & Tab Manual).
     */
    public function index(Request $request)
    {
        $activeTab = $request->get('tab', 'quotation'); // 'quotation' or 'manual'
        $selectedYear = $request->get('year', 'all');
        $isSales = Auth::user()?->role === 'Sales';
        $currentUserId = Auth::id();

        // --------------------------------------------------------------------
        // 1. Query Data Management Fee dari UnitQuotation (Tab 1)
        // --------------------------------------------------------------------
        $quoteQuery = UnitQuotation::with(['client', 'sales', 'feePaidBy', 'invoices', 'details', 'payments', 'sourceBank'])
            ->where('fee', '>', 0)
            ->where('status', 'po_received');

        // Jika user adalah Sales, kunci data hanya untuk penawaran / customer miliknya
        if ($isSales) {
            $quoteQuery->where('id_sales', $currentUserId);
        } elseif ($request->filled('sales_id') && $request->sales_id !== 'all') {
            $quoteQuery->where('id_sales', $request->sales_id);
        }

        // Filter Tahun Global
        if ($selectedYear !== 'all' && !empty($selectedYear)) {
            $quoteQuery->whereYear('date', $selectedYear);
        }

        // Filter Status Pencairan Fee
        if ($request->filled('fee_status') && $request->fee_status !== 'all') {
            $quoteQuery->where('fee_payment_status', $request->fee_status);
        }

        // Filter Status Pembayaran Customer (Lunas, DP, Tempo, Belum Bayar)
        if ($request->filled('cust_payment_status') && $request->cust_payment_status !== 'all') {
            $cps = $request->cust_payment_status;
            if ($cps === 'paid') {
                $quoteQuery->whereHas('payments', function ($q) {
                    $q->where('level', 1);
                });
            } elseif ($cps === 'dp') {
                $quoteQuery->whereHas('payments', function ($q) {
                    $q->where('type', 'like', '%dp%');
                });
            } elseif ($cps === 'tempo') {
                $quoteQuery->where(function ($q) {
                    $q->where('payment_method', 'like', '%tempo%')
                      ->orWhere('payment_method', 'like', '%credit%')
                      ->orWhereHas('payments', fn($pq) => $pq->where('type', 'like', '%tempo%'))
                      ->orWhereHas('invoices', fn($iq) => $iq->where('type', 'like', '%tempo%'));
                });
            } elseif ($cps === 'unpaid') {
                $quoteQuery->whereDoesntHave('payments', function ($q) {
                    $q->where('level', 1);
                });
            }
        }

        // Filter Pencarian Keyword (Tab Quotation)
        if ($request->filled('search')) {
            $search = $request->search;
            $quoteQuery->where(function ($q) use ($search) {
                $q->where('no_quote', 'like', "%{$search}%")
                  ->orWhere('po_number', 'like', "%{$search}%")
                  ->orWhere('fee_bank_holder', 'like', "%{$search}%")
                  ->orWhere('fee_bank_account', 'like', "%{$search}%")
                  ->orWhere('fee_bank_branch', 'like', "%{$search}%")
                  ->orWhereHas('client', function ($cq) use ($search) {
                      $cq->where('company', 'like', "%{$search}%");
                  });
            });
        }

        // Filter Rentang Tanggal
        if ($request->filled('start_date')) {
            $quoteQuery->whereDate('date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $quoteQuery->whereDate('date', '<=', $request->end_date);
        }

        $items = $quoteQuery->orderBy('date', 'desc')->paginate(15, ['*'], 'quote_page')->withQueryString();

        // --------------------------------------------------------------------
        // 2. Query Data Management Fee Manual (Tab 2)
        // --------------------------------------------------------------------
        $manualQuery = ManualManagementFee::with(['client', 'feePaidBy', 'creator', 'sourceBank']);

        // Jika user adalah Sales, kunci data hanya untuk record buatan sales tsb atau customernya
        if ($isSales) {
            $manualQuery->where(function ($q) use ($currentUserId) {
                $q->where('created_by', $currentUserId)
                  ->orWhereHas('client', fn($cq) => $cq->where('id_sales', $currentUserId));
            });
        }

        // Filter Tahun Global
        if ($selectedYear !== 'all' && !empty($selectedYear)) {
            $manualQuery->whereYear('date', $selectedYear);
        }

        if ($request->filled('manual_search')) {
            $ms = $request->manual_search;
            $manualQuery->where(function ($q) use ($ms) {
                $q->where('title', 'like', "%{$ms}%")
                  ->orWhere('reference_no', 'like', "%{$ms}%")
                  ->orWhere('custom_company_name', 'like', "%{$ms}%")
                  ->orWhere('fee_bank_holder', 'like', "%{$ms}%")
                  ->orWhere('fee_bank_account', 'like', "%{$ms}%")
                  ->orWhere('fee_bank_branch', 'like', "%{$ms}%")
                  ->orWhereHas('client', function ($cq) use ($ms) {
                      $cq->where('company', 'like', "%{$ms}%");
                  });
            });
        }

        if ($request->filled('manual_fee_status') && $request->manual_fee_status !== 'all') {
            $manualQuery->where('fee_payment_status', $request->manual_fee_status);
        }

        if ($request->filled('manual_client_id') && $request->manual_client_id !== 'all') {
            $manualQuery->where('client_id', $request->manual_client_id);
        }

        if ($request->filled('manual_start_date')) {
            $manualQuery->whereDate('date', '>=', $request->manual_start_date);
        }
        if ($request->filled('manual_end_date')) {
            $manualQuery->whereDate('date', '<=', $request->manual_end_date);
        }

        $manualItems = $manualQuery->orderBy('date', 'desc')->paginate(15, ['*'], 'manual_page')->withQueryString();

        // --------------------------------------------------------------------
        // 3. Hitung Statistik / Summary Dashboard Fee (Dipengaruhi Filter Tahun & Role)
        // --------------------------------------------------------------------
        $quoteSummaryQuery = UnitQuotation::where('fee', '>', 0)->where('status', 'po_received');
        $manualSummaryQuery = ManualManagementFee::query();

        if ($isSales) {
            $quoteSummaryQuery->where('id_sales', $currentUserId);
            $manualSummaryQuery->where(function ($q) use ($currentUserId) {
                $q->where('created_by', $currentUserId)
                  ->orWhereHas('client', fn($cq) => $cq->where('id_sales', $currentUserId));
            });
        }

        if ($selectedYear !== 'all' && !empty($selectedYear)) {
            $quoteSummaryQuery->whereYear('date', $selectedYear);
            $manualSummaryQuery->whereYear('date', $selectedYear);
        }

        $allQuoteFees = $quoteSummaryQuery->get();
        $allManualFees = $manualSummaryQuery->get();

        $totalGrossFee = $allQuoteFees->sum('fee') + $allManualFees->sum('gross_fee');
        $totalTaxDeduction = 0;
        $totalNetFee = 0;
        $totalPaidFee = 0;
        $totalPendingFee = 0;

        foreach ($allQuoteFees as $fq) {
            $taxData = $fq->fee_tax_data;
            $totalTaxDeduction += $taxData->tax_amount;
            $totalNetFee += $taxData->net_fee;

            if ($fq->fee_payment_status === 'paid') {
                $totalPaidFee += $taxData->net_fee;
            } else {
                $totalPendingFee += $taxData->net_fee;
            }
        }

        foreach ($allManualFees as $fm) {
            $taxData = $fm->fee_tax_data;
            $totalTaxDeduction += $taxData->tax_amount;
            $totalNetFee += $taxData->net_fee;

            if ($fm->fee_payment_status === 'paid') {
                $totalPaidFee += $taxData->net_fee;
            } else {
                $totalPendingFee += $taxData->net_fee;
            }
        }

        if ($isSales) {
            $salesList = User::where('id', $currentUserId)->get();
        } else {
            $salesList = User::whereIn('role', ['Sales', 'Admin', 'Sales Manager'])
                ->orderBy('name')
                ->get();
        }

        $clientQuery = Client::whereNotNull('company')->where('company', '!=', '');
        if ($isSales) {
            $clientQuery->where('id_sales', $currentUserId);
        }
        $clientList = $clientQuery->orderBy('company')->get(['id', 'company']);

        // Daftar tahun untuk filter (disesuaikan dengan data sales jika role sales)
        $yearsQuoteQuery = UnitQuotation::whereNotNull('date')->where('fee', '>', 0)->where('status', 'po_received');
        $yearsManualQuery = ManualManagementFee::whereNotNull('date');
        if ($isSales) {
            $yearsQuoteQuery->where('id_sales', $currentUserId);
            $yearsManualQuery->where(function ($q) use ($currentUserId) {
                $q->where('created_by', $currentUserId)
                  ->orWhereHas('client', fn($cq) => $cq->where('id_sales', $currentUserId));
            });
        }
        $yearsQuote = $yearsQuoteQuery->selectRaw('YEAR(date) as yr')->distinct()->pluck('yr');
        $yearsManual = $yearsManualQuery->selectRaw('YEAR(date) as yr')->distinct()->pluck('yr');
        $years = $yearsQuote->merge($yearsManual)->push((int) date('Y'))->unique()->filter()->sortDesc()->values();

        // Bank Aktif untuk Sumber Dana Pencairan Fee
        $banks = Bank::where('is_active', 1)->orderBy('bank')->get();

        return view('pages.finance.management-fee.index', compact(
            'items',
            'manualItems',
            'activeTab',
            'selectedYear',
            'totalGrossFee',
            'totalTaxDeduction',
            'totalNetFee',
            'totalPaidFee',
            'totalPendingFee',
            'salesList',
            'clientList',
            'years',
            'allQuoteFees',
            'allManualFees',
            'isSales',
            'banks'
        ));
    }

    /**
     * Update data rekening & proses pencairan/transfer fee Quotation oleh tim Finance.
     */
    public function updateDisbursement(Request $request, $id)
    {
        if (Auth::user()?->role === 'Sales') {
            abort(403, 'Akses ditolak. Role Sales hanya memiliki hak akses read-only pada Management Fee.');
        }

        $quote = UnitQuotation::findOrFail($id);

        $request->validate([
            'id_source_bank'      => 'nullable|exists:bank,id',
            'fee_bank_name'       => 'nullable|string|max:100',
            'fee_bank_account'    => 'nullable|string|max:100',
            'fee_bank_holder'     => 'nullable|string|max:150',
            'fee_bank_branch'     => 'nullable|string|max:100',
            'fee_payment_status'  => 'required|in:unpaid,pending_transfer,paid',
            'fee_transfer_date'   => 'nullable|date',
            'fee_transfer_note'   => 'nullable|string|max:1000',
            'fee_transfer_proof'  => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
        ]);

        if ($request->fee_payment_status === 'paid' && empty($request->id_source_bank)) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pilih rekening Bank Kantor asal transfer untuk pencairan dana.',
                ], 422);
            }
            return back()->withErrors(['id_source_bank' => 'Pilih rekening Bank Kantor asal transfer untuk pencairan dana.'])->withInput();
        }

        $oldStatus = $quote->fee_payment_status;
        $oldBankId = $quote->id_source_bank;
        $netFee = (float) ($quote->fee_tax_data->net_fee ?: $quote->fee);

        $newStatus = $request->fee_payment_status;
        $newBankId = $request->id_source_bank ? (int) $request->id_source_bank : null;

        DB::transaction(function () use ($quote, $request, $oldStatus, $oldBankId, $netFee, $newStatus, $newBankId) {
            // Revert old bank balance if it was paid
            if ($oldStatus === 'paid' && $oldBankId) {
                Bank::where('id', $oldBankId)->increment('saldo', $netFee);
            }

            // Deduct new bank balance if new status is paid
            if ($newStatus === 'paid' && $newBankId) {
                Bank::where('id', $newBankId)->decrement('saldo', $netFee);
            }

            $quote->id_source_bank     = $newStatus === 'paid' ? $newBankId : ($request->id_source_bank ?: null);
            $quote->fee_bank_name      = $request->fee_bank_name;
            $quote->fee_bank_account   = $request->fee_bank_account;
            $quote->fee_bank_holder    = $request->fee_bank_holder;
            $quote->fee_bank_branch    = $request->fee_bank_branch;
            $quote->fee_payment_status = $newStatus;
            $quote->fee_transfer_note  = $request->fee_transfer_note;

            if ($newStatus === 'paid') {
                $quote->fee_transfer_date = $request->fee_transfer_date ?: ($quote->fee_transfer_date ?: now());
                $quote->fee_paid_by       = $quote->fee_paid_by ?: Auth::id();
            } elseif ($newStatus === 'unpaid') {
                $quote->fee_transfer_date = null;
                $quote->fee_paid_by       = null;
                $quote->id_source_bank    = null;
            }

            // Upload bukti transfer jika ada, atau hapus jika diminta
            if ($request->hasFile('fee_transfer_proof')) {
                if ($quote->fee_transfer_proof && (Storage::disk('public')->exists($quote->fee_transfer_proof) || Storage::exists($quote->fee_transfer_proof))) {
                    Storage::disk('public')->delete($quote->fee_transfer_proof);
                }
                $path = $request->file('fee_transfer_proof')->store('fee-transfer-proofs', 'public');
                $quote->fee_transfer_proof = $path;
            } elseif ($request->boolean('delete_fee_transfer_proof') || $request->delete_fee_transfer_proof == '1') {
                if ($quote->fee_transfer_proof && (Storage::disk('public')->exists($quote->fee_transfer_proof) || Storage::exists($quote->fee_transfer_proof))) {
                    Storage::disk('public')->delete($quote->fee_transfer_proof);
                }
                $quote->fee_transfer_proof = null;
            }

            $quote->save();
        });

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Status pencairan Management Fee berhasil diperbarui.',
                'status'  => $quote->fee_payment_status,
            ]);
        }

        return redirect()->route('finance.management-fee.index', ['tab' => 'quotation'])->with('success', 'Status pencairan Management Fee berhasil diperbarui.');
    }

    /**
     * Simpan data Manual Management Fee baru.
     */
    public function storeManual(Request $request)
    {
        if (Auth::user()?->role === 'Sales') {
            abort(403, 'Akses ditolak. Role Sales hanya memiliki hak akses read-only pada Management Fee.');
        }

        $request->validate([
            'id_source_bank'      => 'nullable|exists:bank,id',
            'client_id'           => 'nullable|exists:client,id',
            'custom_company_name' => 'nullable|string|max:200',
            'date'                => 'required|date',
            'title'               => 'required|string|max:255',
            'reference_no'        => 'nullable|string|max:100',
            'gross_fee'           => 'required|numeric|min:1',
            'fee_bank_name'       => 'nullable|string|max:100',
            'fee_bank_branch'     => 'nullable|string|max:100',
            'fee_bank_account'    => 'nullable|string|max:100',
            'fee_bank_holder'     => 'nullable|string|max:150',
            'fee_payment_status'  => 'required|in:unpaid,pending_transfer,paid',
            'fee_transfer_date'   => 'nullable|date',
            'fee_transfer_note'   => 'nullable|string|max:1000',
            'fee_transfer_proof'  => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
        ]);

        $isPaid = $request->fee_payment_status === 'paid';
        $newBankId = $request->id_source_bank ? (int) $request->id_source_bank : null;

        if ($isPaid && empty($newBankId)) {
            return back()->withErrors(['id_source_bank' => 'Pilih rekening Bank Kantor asal transfer untuk pencairan dana.'])->withInput();
        }

        $proofPath = null;
        if ($request->hasFile('fee_transfer_proof')) {
            $proofPath = $request->file('fee_transfer_proof')->store('fee-transfer-proofs', 'public');
        }

        DB::transaction(function () use ($request, $proofPath, $isPaid, $newBankId) {
            $fee = ManualManagementFee::create([
                'client_id'           => $request->client_id,
                'custom_company_name' => $request->custom_company_name,
                'date'                => $request->date,
                'title'               => $request->title,
                'reference_no'        => $request->reference_no,
                'gross_fee'           => $request->gross_fee,
                'fee_bank_name'       => $request->fee_bank_name,
                'fee_bank_branch'     => $request->fee_bank_branch,
                'fee_bank_account'    => $request->fee_bank_account,
                'fee_bank_holder'     => $request->fee_bank_holder,
                'fee_payment_status'  => $request->fee_payment_status,
                'id_source_bank'      => $isPaid ? $newBankId : ($request->id_source_bank ?: null),
                'fee_transfer_date'   => $isPaid ? ($request->fee_transfer_date ?: now()) : null,
                'fee_transfer_proof'  => $proofPath,
                'fee_transfer_note'   => $request->fee_transfer_note,
                'fee_paid_by'         => $isPaid ? Auth::id() : null,
                'created_by'          => Auth::id(),
            ]);

            if ($isPaid && $newBankId) {
                $netFee = (float) ($fee->fee_tax_data->net_fee ?: $fee->gross_fee);
                Bank::where('id', $newBankId)->decrement('saldo', $netFee);
            }
        });

        return redirect()->route('finance.management-fee.index', ['tab' => 'manual'])->with('success', 'Data Manual Management Fee berhasil ditambahkan.');
    }

    /**
     * Update data Manual Management Fee.
     */
    public function updateManual(Request $request, $id)
    {
        if (Auth::user()?->role === 'Sales') {
            abort(403, 'Akses ditolak. Role Sales hanya memiliki hak akses read-only pada Management Fee.');
        }

        $fee = ManualManagementFee::findOrFail($id);

        $request->validate([
            'id_source_bank'      => 'nullable|exists:bank,id',
            'client_id'           => 'nullable|exists:client,id',
            'custom_company_name' => 'nullable|string|max:200',
            'date'                => 'required|date',
            'title'               => 'required|string|max:255',
            'reference_no'        => 'nullable|string|max:100',
            'gross_fee'           => 'required|numeric|min:1',
            'fee_bank_name'       => 'nullable|string|max:100',
            'fee_bank_branch'     => 'nullable|string|max:100',
            'fee_bank_account'    => 'nullable|string|max:100',
            'fee_bank_holder'     => 'nullable|string|max:150',
            'fee_payment_status'  => 'required|in:unpaid,pending_transfer,paid',
            'fee_transfer_date'   => 'nullable|date',
            'fee_transfer_note'   => 'nullable|string|max:1000',
            'fee_transfer_proof'  => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
        ]);

        $oldStatus = $fee->fee_payment_status;
        $oldBankId = $fee->id_source_bank;
        $oldNetFee = (float) ($fee->fee_tax_data->net_fee ?: $fee->gross_fee);

        $newStatus = $request->fee_payment_status;
        $newBankId = $request->id_source_bank ? (int) $request->id_source_bank : null;

        if ($newStatus === 'paid' && empty($newBankId)) {
            return back()->withErrors(['id_source_bank' => 'Pilih rekening Bank Kantor asal transfer untuk pencairan dana.'])->withInput();
        }

        DB::transaction(function () use ($fee, $request, $oldStatus, $oldBankId, $oldNetFee, $newStatus, $newBankId) {
            // Revert old bank balance if previously paid
            if ($oldStatus === 'paid' && $oldBankId) {
                Bank::where('id', $oldBankId)->increment('saldo', $oldNetFee);
            }

            $fee->client_id           = $request->client_id;
            $fee->custom_company_name = $request->custom_company_name;
            $fee->date                = $request->date;
            $fee->title               = $request->title;
            $fee->reference_no        = $request->reference_no;
            $fee->gross_fee           = $request->gross_fee;
            $fee->fee_bank_name       = $request->fee_bank_name;
            $fee->fee_bank_branch     = $request->fee_bank_branch;
            $fee->fee_bank_account    = $request->fee_bank_account;
            $fee->fee_bank_holder     = $request->fee_bank_holder;
            $fee->fee_payment_status  = $newStatus;
            $fee->id_source_bank      = $newStatus === 'paid' ? $newBankId : ($request->id_source_bank ?: null);
            $fee->fee_transfer_note   = $request->fee_transfer_note;

            if ($newStatus === 'paid') {
                $fee->fee_transfer_date = $request->fee_transfer_date ?: ($fee->fee_transfer_date ?: now());
                $fee->fee_paid_by       = $fee->fee_paid_by ?: Auth::id();
            } elseif ($newStatus === 'unpaid') {
                $fee->fee_transfer_date = null;
                $fee->fee_paid_by       = null;
                $fee->id_source_bank    = null;
            }

            if ($request->hasFile('fee_transfer_proof')) {
                if ($fee->fee_transfer_proof && (Storage::disk('public')->exists($fee->fee_transfer_proof) || Storage::exists($fee->fee_transfer_proof))) {
                    Storage::disk('public')->delete($fee->fee_transfer_proof);
                }
                $fee->fee_transfer_proof = $request->file('fee_transfer_proof')->store('fee-transfer-proofs', 'public');
            } elseif ($request->boolean('delete_fee_transfer_proof') || $request->delete_fee_transfer_proof == '1') {
                if ($fee->fee_transfer_proof && (Storage::disk('public')->exists($fee->fee_transfer_proof) || Storage::exists($fee->fee_transfer_proof))) {
                    Storage::disk('public')->delete($fee->fee_transfer_proof);
                }
                $fee->fee_transfer_proof = null;
            }

            $fee->save();

            // Deduct new bank balance if paid
            if ($newStatus === 'paid' && $newBankId) {
                $newNetFee = (float) ($fee->fee_tax_data->net_fee ?: $fee->gross_fee);
                Bank::where('id', $newBankId)->decrement('saldo', $newNetFee);
            }
        });

        return redirect()->route('finance.management-fee.index', ['tab' => 'manual'])->with('success', 'Data Manual Management Fee berhasil diperbarui.');
    }

    /**
     * Hapus data Manual Management Fee.
     */
    public function destroyManual($id)
    {
        if (Auth::user()?->role === 'Sales') {
            abort(403, 'Akses ditolak. Role Sales hanya memiliki hak akses read-only pada Management Fee.');
        }

        $fee = ManualManagementFee::findOrFail($id);

        DB::transaction(function () use ($fee) {
            if ($fee->fee_payment_status === 'paid' && $fee->id_source_bank) {
                $netFee = (float) ($fee->fee_tax_data->net_fee ?: $fee->gross_fee);
                Bank::where('id', $fee->id_source_bank)->increment('saldo', $netFee);
            }

            if ($fee->fee_transfer_proof && (Storage::disk('public')->exists($fee->fee_transfer_proof) || Storage::exists($fee->fee_transfer_proof))) {
                Storage::disk('public')->delete($fee->fee_transfer_proof);
            }

            $fee->delete();
        });

        return redirect()->route('finance.management-fee.index', ['tab' => 'manual'])->with('success', 'Data Manual Management Fee berhasil dihapus.');
    }

    /**
     * Update data status pencairan & transfer untuk Manual Management Fee.
     */
    public function updateManualDisbursement(Request $request, $id)
    {
        if (Auth::user()?->role === 'Sales') {
            abort(403, 'Akses ditolak. Role Sales hanya memiliki hak akses read-only pada Management Fee.');
        }

        $fee = ManualManagementFee::findOrFail($id);

        $request->validate([
            'id_source_bank'      => 'nullable|exists:bank,id',
            'fee_bank_name'       => 'nullable|string|max:100',
            'fee_bank_branch'     => 'nullable|string|max:100',
            'fee_bank_account'    => 'nullable|string|max:100',
            'fee_bank_holder'     => 'nullable|string|max:150',
            'fee_payment_status'  => 'required|in:unpaid,pending_transfer,paid',
            'fee_transfer_date'   => 'nullable|date',
            'fee_transfer_note'   => 'nullable|string|max:1000',
            'fee_transfer_proof'  => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
        ]);

        if ($request->fee_payment_status === 'paid' && empty($request->id_source_bank)) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pilih rekening Bank Kantor asal transfer untuk pencairan dana.',
                ], 422);
            }
            return back()->withErrors(['id_source_bank' => 'Pilih rekening Bank Kantor asal transfer untuk pencairan dana.'])->withInput();
        }

        $oldStatus = $fee->fee_payment_status;
        $oldBankId = $fee->id_source_bank;
        $netFee = (float) ($fee->fee_tax_data->net_fee ?: $fee->gross_fee);

        $newStatus = $request->fee_payment_status;
        $newBankId = $request->id_source_bank ? (int) $request->id_source_bank : null;

        DB::transaction(function () use ($fee, $request, $oldStatus, $oldBankId, $netFee, $newStatus, $newBankId) {
            if ($oldStatus === 'paid' && $oldBankId) {
                Bank::where('id', $oldBankId)->increment('saldo', $netFee);
            }

            if ($newStatus === 'paid' && $newBankId) {
                Bank::where('id', $newBankId)->decrement('saldo', $netFee);
            }

            $fee->id_source_bank      = $newStatus === 'paid' ? $newBankId : ($request->id_source_bank ?: null);
            $fee->fee_bank_name       = $request->fee_bank_name;
            $fee->fee_bank_branch     = $request->fee_bank_branch;
            $fee->fee_bank_account    = $request->fee_bank_account;
            $fee->fee_bank_holder     = $request->fee_bank_holder;
            $fee->fee_payment_status  = $newStatus;
            $fee->fee_transfer_note   = $request->fee_transfer_note;

            if ($newStatus === 'paid') {
                $fee->fee_transfer_date = $request->fee_transfer_date ?: ($fee->fee_transfer_date ?: now());
                $fee->fee_paid_by       = $fee->fee_paid_by ?: Auth::id();
            } elseif ($newStatus === 'unpaid') {
                $fee->fee_transfer_date = null;
                $fee->fee_paid_by       = null;
                $fee->id_source_bank    = null;
            }

            if ($request->hasFile('fee_transfer_proof')) {
                if ($fee->fee_transfer_proof && (Storage::disk('public')->exists($fee->fee_transfer_proof) || Storage::exists($fee->fee_transfer_proof))) {
                    Storage::disk('public')->delete($fee->fee_transfer_proof);
                }
                $fee->fee_transfer_proof = $request->file('fee_transfer_proof')->store('fee-transfer-proofs', 'public');
            } elseif ($request->boolean('delete_fee_transfer_proof') || $request->delete_fee_transfer_proof == '1') {
                if ($fee->fee_transfer_proof && (Storage::disk('public')->exists($fee->fee_transfer_proof) || Storage::exists($fee->fee_transfer_proof))) {
                    Storage::disk('public')->delete($fee->fee_transfer_proof);
                }
                $fee->fee_transfer_proof = null;
            }

            $fee->save();
        });

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Status pencairan Manual Fee berhasil diperbarui.',
                'status'  => $fee->fee_payment_status,
            ]);
        }

        return redirect()->route('finance.management-fee.index', ['tab' => 'manual'])->with('success', 'Status pencairan Manual Fee berhasil diperbarui.');
    }
}
