<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use App\Models\PettyCashTransaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PettyCashController extends Controller
{
    /**
     * Display Petty Cash Dashboard & Transaction Ledger.
     */
    public function index(Request $request)
    {
        $pettyCashBanks = Bank::where('is_petty_cash', 1)
            ->with('pic')
            ->orderBy('is_active', 'desc')
            ->orderBy('bank', 'asc')
            ->get();

        // Selected active petty cash bank (default to first active petty cash account if exists)
        $selectedBankId = $request->get('id_bank');
        $selectedBank = null;

        if ($selectedBankId) {
            $selectedBank = Bank::with('pic')->find($selectedBankId);
        } elseif ($pettyCashBanks->isNotEmpty()) {
            $selectedBank = $pettyCashBanks->first();
            $selectedBankId = $selectedBank->id;
        }

        // Available source banks for topup reimbursement (non petty cash or all active banks)
        $sourceBanks = Bank::where('is_active', 1)
            ->where(function($q) use ($selectedBankId) {
                if ($selectedBankId) {
                    $q->where('id', '!=', $selectedBankId);
                }
            })
            ->orderBy('entity', 'asc')
            ->orderBy('bank', 'asc')
            ->get();

        // Date filters (default current month)
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', Carbon::now()->toDateString());
        $categoryFilter = $request->get('category');
        $typeFilter = $request->get('type');

        // Query transactions
        $query = PettyCashTransaction::with(['bank.pic', 'sourceBank', 'creator'])
            ->whereBetween('date', [$startDate, $endDate]);

        if ($selectedBankId) {
            $query->where('id_bank', $selectedBankId);
        }

        if ($categoryFilter) {
            $query->where('category', $categoryFilter);
        }

        if ($typeFilter) {
            $query->where('type', $typeFilter);
        }

        $transactions = $query->orderBy('date', 'desc')->orderBy('id', 'desc')->paginate(25)->withQueryString();

        // Summary metrics
        $metricsQuery = PettyCashTransaction::whereBetween('date', [$startDate, $endDate]);
        if ($selectedBankId) {
            $metricsQuery->where('id_bank', $selectedBankId);
        }

        $totalDisbursement = (float) (clone $metricsQuery)->where('type', 'disbursement')->sum('amount');
        $totalTopup = (float) (clone $metricsQuery)->where('type', 'topup')->sum('amount');
        $disbursementCount = (clone $metricsQuery)->where('type', 'disbursement')->count();

        // Active bank metrics
        $currentBalance = $selectedBank ? (float)$selectedBank->saldo : (float)$pettyCashBanks->sum('saldo');
        $plafond = $selectedBank ? (float)$selectedBank->plafond : (float)$pettyCashBanks->sum('plafond');
        $usedPercentage = $plafond > 0 ? min(100, round((($plafond - $currentBalance) / $plafond) * 100, 1)) : 0;
        $remainingPlafond = max(0, $plafond - $currentBalance);

        // Predefined categories for petty cash expenses
        $categories = [
            'Konsumsi & Dapur',
            'ATK & Percetakan',
            'Transport / Bensin / Tol / Parkir',
            'Kurir / Ekspedisi / Ongkir',
            'Kebersihan & Sanitasi',
            'Maintenance & Perbaikan Ringan',
            'Perlengkapan Operasional Kantor',
            'Biaya Pos & Materai',
            'Keperluan Proyek / Lapangan Kas',
            'Lain-lain',
        ];

        return view('pages.finance.petty-cash.index', compact(
            'pettyCashBanks',
            'selectedBank',
            'selectedBankId',
            'sourceBanks',
            'transactions',
            'startDate',
            'endDate',
            'categoryFilter',
            'typeFilter',
            'totalDisbursement',
            'totalTopup',
            'disbursementCount',
            'currentBalance',
            'plafond',
            'usedPercentage',
            'remainingPlafond',
            'categories'
        ));
    }

    /**
     * Store a newly created Petty Cash Expense (BKK - Bukti Kas Keluar).
     */
    public function storeDisbursement(Request $request)
    {
        $request->validate([
            'id_bank' => 'required|exists:bank,id',
            'date' => 'required|date',
            'category' => 'required|string|max:100',
            'recipient' => 'nullable|string|max:150',
            'amount' => 'required|numeric|min:1',
            'description' => 'required|string|max:500',
            'proof_attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        return DB::transaction(function () use ($request) {
            $bank = Bank::lockForUpdate()->findOrFail($request->id_bank);
            $amount = (float) $request->amount;

            // Generate Voucher Number: BKK-YYMM-XXXX
            $prefix = 'BKK-' . date('ym', strtotime($request->date)) . '-';
            $lastTx = PettyCashTransaction::where('voucher_number', 'LIKE', $prefix . '%')
                ->orderBy('id', 'desc')
                ->first();
            $nextSeq = 1;
            if ($lastTx && preg_match('/-(\d+)$/', $lastTx->voucher_number, $matches)) {
                $nextSeq = (int)$matches[1] + 1;
            }
            $voucherNumber = $prefix . str_pad($nextSeq, 4, '0', STR_PAD_LEFT);

            // Handle file upload
            $proofPath = null;
            if ($request->hasFile('proof_attachment')) {
                $file = $request->file('proof_attachment');
                $filename = 'bkk_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $proofPath = $file->storeAs('petty_cash', $filename, 'public');
            }

            // Create transaction record
            $tx = PettyCashTransaction::create([
                'id_bank' => $bank->id,
                'voucher_number' => $voucherNumber,
                'type' => 'disbursement',
                'date' => $request->date,
                'category' => $request->category,
                'recipient' => $request->recipient ?: 'Kasir / Operasional',
                'amount' => $amount,
                'description' => $request->description,
                'proof_attachment' => $proofPath,
                'id_source_bank' => null,
                'created_by' => Auth::id(),
            ]);

            // Deduct petty cash balance atomically
            $bank->saldo -= $amount;
            $bank->save();

            return redirect()->route('petty_cash.index', ['id_bank' => $bank->id])
                ->with('success', "Pengeluaran Kas Kecil [{$tx->voucher_number}] sebesar Rp " . number_format($amount, 0, ',', '.') . " berhasil dicatat.");
        });
    }

    /**
     * Store a Petty Cash Top-Up / Reimbursement (BKM - Bukti Kas Masuk).
     */
    public function storeTopup(Request $request)
    {
        $request->validate([
            'id_bank' => 'required|exists:bank,id|different:id_source_bank',
            'id_source_bank' => 'required|exists:bank,id',
            'date' => 'required|date',
            'amount' => 'required|numeric|min:1',
            'description' => 'nullable|string|max:500',
            'proof_attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ], [
            'id_bank.different' => 'Rekening Kas Kecil dan Rekening Sumber Utama tidak boleh sama.',
            'amount.min' => 'Nominal pengisian minimal Rp 1.',
        ]);

        return DB::transaction(function () use ($request) {
            $targetBank = Bank::lockForUpdate()->findOrFail($request->id_bank);
            $sourceBank = Bank::lockForUpdate()->findOrFail($request->id_source_bank);
            $amount = (float) $request->amount;

            // Generate Voucher Number: BKM-YYMM-XXXX
            $prefix = 'BKM-' . date('ym', strtotime($request->date)) . '-';
            $lastTx = PettyCashTransaction::where('voucher_number', 'LIKE', $prefix . '%')
                ->orderBy('id', 'desc')
                ->first();
            $nextSeq = 1;
            if ($lastTx && preg_match('/-(\d+)$/', $lastTx->voucher_number, $matches)) {
                $nextSeq = (int)$matches[1] + 1;
            }
            $voucherNumber = $prefix . str_pad($nextSeq, 4, '0', STR_PAD_LEFT);

            // Handle file upload
            $proofPath = null;
            if ($request->hasFile('proof_attachment')) {
                $file = $request->file('proof_attachment');
                $filename = 'bkm_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $proofPath = $file->storeAs('petty_cash', $filename, 'public');
            }

            // Create transaction record
            $tx = PettyCashTransaction::create([
                'id_bank' => $targetBank->id,
                'voucher_number' => $voucherNumber,
                'type' => 'topup',
                'date' => $request->date,
                'category' => 'Reimbursement / Top-Up Kas',
                'recipient' => $targetBank->pic?->name ?: $targetBank->atas_nama,
                'amount' => $amount,
                'description' => $request->description ?: "Pengisian Kas Kecil dari {$sourceBank->bank} ({$sourceBank->no_rek})",
                'proof_attachment' => $proofPath,
                'id_source_bank' => $sourceBank->id,
                'created_by' => Auth::id(),
            ]);

            // Deduct source bank & increment petty cash balance
            $sourceBank->saldo -= $amount;
            $sourceBank->save();

            $targetBank->saldo += $amount;
            $targetBank->save();

            return redirect()->route('petty_cash.index', ['id_bank' => $targetBank->id])
                ->with('success', "Pengisian Kas Kecil [{$tx->voucher_number}] sebesar Rp " . number_format($amount, 0, ',', '.') . " dari {$sourceBank->bank} berhasil diproses.");
        });
    }

    /**
     * Delete a petty cash transaction and safely restore balances.
     */
    public function destroy($id)
    {
        return DB::transaction(function () use ($id) {
            $tx = PettyCashTransaction::lockForUpdate()->findOrFail($id);
            $bank = Bank::lockForUpdate()->find($tx->id_bank);
            $amount = (float) $tx->amount;
            $bankId = $tx->id_bank;

            if ($tx->type === 'disbursement') {
                if ($bank) {
                    $bank->saldo += $amount;
                    $bank->save();
                }
            } elseif ($tx->type === 'topup') {
                if ($bank) {
                    $bank->saldo -= $amount;
                    $bank->save();
                }
                if ($tx->id_source_bank) {
                    $sourceBank = Bank::lockForUpdate()->find($tx->id_source_bank);
                    if ($sourceBank) {
                        $sourceBank->saldo += $amount;
                        $sourceBank->save();
                    }
                }
            }

            // Delete proof file if exists
            if ($tx->proof_attachment && Storage::disk('public')->exists($tx->proof_attachment)) {
                Storage::disk('public')->delete($tx->proof_attachment);
            }

            $voucherNo = $tx->voucher_number;
            $tx->delete();

            return redirect()->route('petty_cash.index', ['id_bank' => $bankId])
                ->with('success', "Transaksi Kas Kecil [{$voucherNo}] berhasil dibatalkan dan saldo telah dipulihkan.");
        });
    }

    /**
     * Print single transaction voucher (BKK / BKM).
     */
    public function printVoucher($id)
    {
        $tx = PettyCashTransaction::with(['bank.pic', 'sourceBank', 'creator'])->findOrFail($id);
        return view('pages.finance.petty-cash.print-voucher', compact('tx'));
    }

    /**
     * Printable view of Petty Cash Ledger Statement.
     */
    public function printStatement(Request $request)
    {
        $selectedBankId = $request->get('id_bank');
        $bank = Bank::with('pic')->findOrFail($selectedBankId);

        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', Carbon::now()->toDateString());

        // Calculate opening balance before start_date
        $prevTopup = (float) PettyCashTransaction::where('id_bank', $bank->id)
            ->where('type', 'topup')
            ->whereDate('date', '<', $startDate)
            ->sum('amount');

        $prevDisburse = (float) PettyCashTransaction::where('id_bank', $bank->id)
            ->where('type', 'disbursement')
            ->whereDate('date', '<', $startDate)
            ->sum('amount');

        $initialBalance = (float) ($bank->initial_balance ?: 0);
        $openingBalance = $initialBalance + $prevTopup - $prevDisburse;

        // Transactions in date range
        $transactions = PettyCashTransaction::where('id_bank', $bank->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->with(['creator', 'sourceBank'])
            ->orderBy('date', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        $totalIn = 0;
        $totalOut = 0;
        $running = $openingBalance;

        $ledger = $transactions->map(function ($tx) use (&$running, &$totalIn, &$totalOut) {
            $in = $tx->type === 'topup' ? (float)$tx->amount : 0;
            $out = $tx->type === 'disbursement' ? (float)$tx->amount : 0;
            $totalIn += $in;
            $totalOut += $out;
            $running = $running + $in - $out;

            return (object) [
                'date' => $tx->date->toDateString(),
                'voucher_number' => $tx->voucher_number,
                'type' => $tx->type,
                'category' => $tx->category,
                'recipient' => $tx->recipient,
                'description' => $tx->description,
                'source_bank' => $tx->sourceBank ? "{$tx->sourceBank->bank} ({$tx->sourceBank->no_rek})" : null,
                'in' => $in,
                'out' => $out,
                'running_balance' => $running,
                'creator' => $tx->creator?->name ?? 'Kasir',
            ];
        });

        $closingBalance = $running;

        return view('pages.finance.petty-cash.print-statement', compact(
            'bank',
            'startDate',
            'endDate',
            'openingBalance',
            'ledger',
            'totalIn',
            'totalOut',
            'closingBalance'
        ));
    }
}
