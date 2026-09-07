<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use App\Models\BankTransfer;
use App\Models\Expense;
use App\Models\ManualManagementFee;
use App\Models\Payable;
use App\Models\Payment;
use App\Models\PettyCashTransaction;
use App\Models\ProjectExpense;
use App\Models\PurchasePayment;
use App\Models\UnitQuotation;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class BankController extends Controller
{
    /**
     * Display a listing of bank accounts and summary metrics.
     */
    public function index()
    {
        $banks = Bank::with('pic')->orderBy('is_active', 'desc')->orderBy('bank', 'asc')->get()->map(function ($bank) {
            $arCount = Payment::where('id_bank', $bank->id)->where('level', 1)->count();
            $apCount = PurchasePayment::where('id_bank', $bank->id)->count();
            $expenseCount = Expense::where('id_bank', $bank->id)->count();
            $pettyCount = PettyCashTransaction::where('id_bank', $bank->id)->count();
            
            $bank->total_tx_count = $arCount + $apCount + $expenseCount + $pettyCount;
            $bank->total_in = (float) Payment::where('id_bank', $bank->id)->where('level', 1)->sum('amount')
                            + (float) PettyCashTransaction::where('id_bank', $bank->id)->where('type', 'topup')->sum('amount');
            $bank->total_out = (float) PurchasePayment::where('id_bank', $bank->id)->sum('amount')
                             + (float) Expense::where('id_bank', $bank->id)->sum('amount')
                             + (float) PettyCashTransaction::where('id_bank', $bank->id)->where('type', 'disbursement')->sum('amount')
                             + (float) PettyCashTransaction::where('id_source_bank', $bank->id)->where('type', 'topup')->sum('amount');
            return $bank;
        });

        $totalLiquidBalance = $banks->where('is_active', 1)->sum('saldo');
        $totalInitialBalance = $banks->where('is_active', 1)->sum('initial_balance');
        $totalIn = $banks->sum('total_in');
        $totalOut = $banks->sum('total_out');
        $activeBankCount = $banks->where('is_active', 1)->count();

        $reftechBanks = $banks->filter(function($b) {
            return strtolower($b->entity ?? 'reftech') === 'reftech';
        });

        $kojishaBanks = $banks->filter(function($b) {
            return strtolower($b->entity ?? '') === 'kojisha';
        });

        $users = User::orderBy('name', 'asc')->get();

        return view('pages.finance.bank.index', compact(
            'banks',
            'reftechBanks',
            'kojishaBanks',
            'totalLiquidBalance',
            'totalInitialBalance',
            'totalIn',
            'totalOut',
            'activeBankCount',
            'users'
        ));
    }

    /**
     * Store a newly created bank account.
     */
    public function store(Request $request)
    {
        $request->validate([
            'bank' => 'required|string|max:100',
            'no_rek' => 'required|string|max:100',
            'atas_nama' => 'nullable|string|max:150',
            'entity' => 'nullable|string|in:Reftech,Kojisha',
            'branch' => 'nullable|string|max:150',
            'initial_balance' => 'nullable|numeric|min:0',
            'description' => 'nullable|string|max:500',
            'is_petty_cash' => 'nullable|in:0,1',
            'pic_id' => 'nullable|exists:users,id',
            'plafond' => 'nullable|numeric|min:0',
        ]);

        $initialBalance = $request->input('initial_balance', 0) ?: 0;

        $bank = Bank::create([
            'bank' => $request->bank,
            'no_rek' => $request->no_rek,
            'atas_nama' => $request->atas_nama ?: 'PT. Refrigerasi Teknik Indonesia',
            'entity' => $request->entity ?: 'Reftech',
            'branch' => $request->branch,
            'initial_balance' => $initialBalance,
            'saldo' => $initialBalance,
            'is_active' => 1,
            'description' => $request->description,
            'is_petty_cash' => $request->has('is_petty_cash') ? (int)$request->is_petty_cash : 0,
            'pic_id' => $request->pic_id,
            'plafond' => $request->plafond ?: 0,
        ]);

        return redirect()->route('bank.index')->with('success', "Rekening {$bank->bank} ({$bank->no_rek}) untuk {$bank->entity} berhasil ditambahkan.");
    }

    /**
     * Update the specified bank account.
     */
    public function update(Request $request, $id)
    {
        $bank = Bank::findOrFail($id);

        $request->validate([
            'bank' => 'required|string|max:100',
            'no_rek' => 'required|string|max:100',
            'atas_nama' => 'nullable|string|max:150',
            'entity' => 'nullable|string|in:Reftech,Kojisha',
            'branch' => 'nullable|string|max:150',
            'initial_balance' => 'nullable|numeric|min:0',
            'adjust_saldo' => 'nullable|numeric',
            'is_active' => 'nullable|in:0,1',
            'description' => 'nullable|string|max:500',
            'is_petty_cash' => 'nullable|in:0,1',
            'pic_id' => 'nullable|exists:users,id',
            'plafond' => 'nullable|numeric|min:0',
        ]);

        $bank->bank = $request->bank;
        $bank->no_rek = $request->no_rek;
        $bank->atas_nama = $request->atas_nama ?: $bank->atas_nama;
        $bank->entity = $request->entity ?: ($bank->entity ?: 'Reftech');
        $bank->branch = $request->branch;
        if ($request->has('initial_balance') && $request->initial_balance !== null) {
            $bank->initial_balance = $request->initial_balance;
        }
        if ($request->has('adjust_saldo') && $request->adjust_saldo !== null) {
            $bank->saldo = $request->adjust_saldo;
        }
        if ($request->has('is_active')) {
            $bank->is_active = (int) $request->is_active;
        }
        $bank->description = $request->description;
        $bank->is_petty_cash = (int) $request->input('is_petty_cash', 0);
        $bank->pic_id = $request->pic_id;
        $bank->plafond = $request->plafond ?: 0;
        $bank->save();

        return redirect()->route('bank.index')->with('success', "Data rekening {$bank->bank} ({$bank->entity}) berhasil diperbarui.");
    }

    /**
     * Toggle active status of a bank account.
     */
    public function toggleStatus($id)
    {
        $bank = Bank::findOrFail($id);
        $bank->is_active = $bank->is_active ? 0 : 1;
        $bank->save();

        $statusLabel = $bank->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->back()->with('success', "Rekening {$bank->bank} berhasil {$statusLabel}.");
    }

    /**
     * Remove the specified bank account from storage.
     */
    /**
     * Handle internal transfer between bank accounts.
     */
    public function transfer(Request $request)
    {
        $request->validate([
            'id_from_bank' => 'required|exists:bank,id|different:id_to_bank',
            'id_to_bank' => 'required|exists:bank,id',
            'amount' => 'required|numeric|min:1',
            'fee' => 'nullable|numeric|min:0',
            'date' => 'required|date',
            'note' => 'nullable|string|max:500',
            'proof_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ], [
            'id_from_bank.different' => 'Rekening sumber dan rekening tujuan tidak boleh sama.',
            'amount.min' => 'Nominal transfer minimal Rp 1.',
        ]);

        $fromBank = Bank::findOrFail($request->id_from_bank);
        $toBank = Bank::findOrFail($request->id_to_bank);
        $amount = (float) $request->amount;
        $fee = (float) ($request->fee ?: 0);
        $totalDeduction = $amount + $fee;

        $proofPath = null;
        if ($request->hasFile('proof_file')) {
            $file = $request->file('proof_file');
            $filename = 'trf_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $proofPath = $file->storeAs('bank_transfers', $filename, 'public');
        }

        // Generate unique transfer number
        $prefix = 'TRF-' . Carbon::parse($request->date)->format('Ym') . '-';
        $lastTrf = BankTransfer::where('transfer_number', 'like', $prefix . '%')->latest('id')->first();
        $seq = 1;
        if ($lastTrf) {
            $lastSeq = (int) substr($lastTrf->transfer_number, -4);
            $seq = $lastSeq + 1;
        }
        $transferNumber = $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);

        DB::transaction(function () use ($fromBank, $toBank, $transferNumber, $request, $amount, $fee, $totalDeduction, $proofPath) {
            // Deduct from source bank (amount + admin fee)
            $fromBank->decrement('saldo', $totalDeduction);

            // Increment destination bank (amount)
            $toBank->increment('saldo', $amount);

            // Record Transfer Transaction
            BankTransfer::create([
                'transfer_number' => $transferNumber,
                'id_from_bank' => $fromBank->id,
                'id_to_bank' => $toBank->id,
                'amount' => $amount,
                'fee' => $fee,
                'date' => $request->date,
                'note' => $request->note,
                'proof_file' => $proofPath,
                'created_by' => Auth::id(),
            ]);
        });

        $formattedAmount = number_format($amount, 0, ',', '.');
        return redirect()->route('bank.index')->with('success', "Transfer dana sebesar Rp {$formattedAmount} dari {$fromBank->bank} ke {$toBank->bank} berhasil dicatat (No. {$transferNumber}).");
    }

    /**
     * Remove the specified bank account from storage.
     */
    public function destroy($id)
    {
        $bank = Bank::findOrFail($id);

        $arCount = Payment::where('id_bank', $bank->id)->count();
        $apCount = PurchasePayment::where('id_bank', $bank->id)->count();
        $expenseCount = Expense::where('id_bank', $bank->id)->count();
        $payableCount = Payable::where('id_bank', $bank->id)->count();
        $projectExpenseCount = ProjectExpense::where('id_bank', $bank->id)->count();
        $transferCount = BankTransfer::where('id_from_bank', $bank->id)->orWhere('id_to_bank', $bank->id)->count();
        $feeCount = UnitQuotation::where('id_source_bank', $bank->id)->where('fee_payment_status', 'paid')->count()
                  + ManualManagementFee::where('id_source_bank', $bank->id)->where('fee_payment_status', 'paid')->count();
        $totalTx = $arCount + $apCount + $expenseCount + $payableCount + $projectExpenseCount + $transferCount + $feeCount;

        if ($totalTx > 0) {
            return redirect()->back()->with('error', "Rekening {$bank->bank} ({$bank->no_rek}) TIDAK BISA DIHAPUS karena telah memiliki {$totalTx} riwayat transaksi terkait (AR/AP/Expense/Voucher/Transfer/Management Fee). Silakan gunakan opsi 'Nonaktifkan' jika rekening ini sudah tidak digunakan agar integritas laporan keuangan tetap aman.");
        }

        $bank->delete();
        return redirect()->route('bank.index')->with('success', "Rekening {$bank->bank} berhasil dihapus.");
    }

    /**
     * Display Bank Statement / Rekening Koran mutasi.
     */
    public function statement(Request $request, $id)
    {
        $bank = Bank::findOrFail($id);
        $startDate = $request->get('start_date', Carbon::now()->subMonths(3)->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', Carbon::now()->toDateString());

        // Previous transactions before start_date
        $prevAr = (float) Payment::where('id_bank', $id)
            ->where('level', 1)
            ->where(function($q) use ($startDate) {
                $q->whereDate('date', '<', $startDate)
                  ->orWhere(function($sub) use ($startDate) {
                      $sub->whereNull('date')->whereDate('created_at', '<', $startDate);
                  });
            })->sum('amount');

        $prevAp = (float) PurchasePayment::where('id_bank', $id)
            ->where(function($q) use ($startDate) {
                $q->whereDate('date', '<', $startDate)
                  ->orWhere(function($sub) use ($startDate) {
                      $sub->whereNull('date')->whereDate('created_at', '<', $startDate);
                  });
            })->sum('amount');

        $prevExpense = (float) Expense::where('id_bank', $id)
            ->where(function($q) use ($startDate) {
                $q->whereDate('date', '<', $startDate)
                  ->orWhere(function($sub) use ($startDate) {
                      $sub->whereNull('date')->whereDate('created_at', '<', $startDate);
                  });
            })->sum('amount');

        $prevTrfIn = (float) BankTransfer::where('id_to_bank', $id)
            ->whereDate('date', '<', $startDate)
            ->sum('amount');

        $prevTrfOut = (float) BankTransfer::where('id_from_bank', $id)
            ->whereDate('date', '<', $startDate)
            ->sum(DB::raw('amount + fee'));

        $prevUqFees = (float) UnitQuotation::where('id_source_bank', $id)
            ->where('fee_payment_status', 'paid')
            ->where(function($q) use ($startDate) {
                $q->whereDate('fee_transfer_date', '<', $startDate)
                  ->orWhere(function($sub) use ($startDate) {
                      $sub->whereNull('fee_transfer_date')->whereDate('updated_at', '<', $startDate);
                  });
            })->get()->sum(fn($q) => $q->fee_tax_data?->net_fee ?: $q->fee);

        $prevManualFees = (float) ManualManagementFee::where('id_source_bank', $id)
            ->where('fee_payment_status', 'paid')
            ->where(function($q) use ($startDate) {
                $q->whereDate('fee_transfer_date', '<', $startDate)
                  ->orWhere(function($sub) use ($startDate) {
                      $sub->whereNull('fee_transfer_date')->whereDate('updated_at', '<', $startDate);
                  });
            })->get()->sum(fn($mf) => $mf->fee_tax_data->net_fee ?: $mf->gross_fee);

        // Petty Cash previous mutations
        $prevPctTopupOut = (float) PettyCashTransaction::where('id_source_bank', $id)
            ->where('type', 'topup')
            ->whereDate('date', '<', $startDate)
            ->sum('amount');

        $prevPctTopupIn = (float) PettyCashTransaction::where('id_bank', $id)
            ->where('type', 'topup')
            ->whereDate('date', '<', $startDate)
            ->sum('amount');

        $prevPctDisburseOut = (float) PettyCashTransaction::where('id_bank', $id)
            ->where('type', 'disbursement')
            ->whereDate('date', '<', $startDate)
            ->sum('amount');

        $initialBalance = (float) ($bank->initial_balance ?: 0);
        $openingBalance = $initialBalance + $prevAr + $prevTrfIn + $prevPctTopupIn - ($prevAp + $prevExpense + $prevTrfOut + $prevUqFees + $prevManualFees + $prevPctTopupOut + $prevPctDisburseOut);

        // Transactions in date range
        // 1. AR Customer Payments (Penerimaan / Masuk / Kredit)
        $arPayments = Payment::where('id_bank', $id)
            ->where('level', 1)
            ->where(function($q) use ($startDate, $endDate) {
                $q->whereBetween('date', [$startDate, $endDate])
                  ->orWhere(function($sub) use ($startDate, $endDate) {
                      $sub->whereNull('date')->whereBetween('created_at', [$startDate, $endDate]);
                  });
            })
            ->with(['quotation.pic.client', 'unitQuotation.client'])
            ->get()->map(function ($pay) {
                $clientName = '-';
                $refNo = '#RCPT-' . $pay->id;
                if ($pay->unitQuotation) {
                    $clientName = $pay->unitQuotation->client?->company ?? '-';
                    $refNo = $pay->unitQuotation->no_quote ?: $refNo;
                } elseif ($pay->quotation) {
                    $clientName = $pay->quotation->pic?->client?->company ?? '-';
                    $refNo = $pay->quotation->no_quote ?: $refNo;
                }
                return [
                    'date' => $pay->date ?: ($pay->created_at ? $pay->created_at->toDateString() : Carbon::now()->toDateString()),
                    'module' => 'AR (Customer Receipt)',
                    'badge_class' => 'bg-label-success',
                    'ref_no' => $refNo,
                    'description' => "Penerimaan Pembayaran dari {$clientName}" . ($pay->note ? " ({$pay->note})" : ''),
                    'in' => (float) $pay->amount,
                    'out' => 0,
                    'type' => 'IN',
                ];
            });

        // 2. AP Purchase Payments (Pengeluaran / Keluar / Debet)
        $apPayments = PurchasePayment::where('id_bank', $id)
            ->where(function($q) use ($startDate, $endDate) {
                $q->whereBetween('date', [$startDate, $endDate])
                  ->orWhere(function($sub) use ($startDate, $endDate) {
                      $sub->whereNull('date')->whereBetween('created_at', [$startDate, $endDate]);
                  });
            })
            ->with('supplier')
            ->get()->map(function ($pay) {
                $supplierName = $pay->supplier?->name ?? 'Supplier / Vendor';
                return [
                    'date' => $pay->date ?: ($pay->created_at ? $pay->created_at->toDateString() : Carbon::now()->toDateString()),
                    'module' => 'AP (Supplier Payment)',
                    'badge_class' => 'bg-label-danger',
                    'ref_no' => $pay->payment_number ?: ('#PAY-AP-' . $pay->id),
                    'description' => "Pembayaran Tagihan Pembelian ke {$supplierName}" . ($pay->note ? " ({$pay->note})" : ''),
                    'in' => 0,
                    'out' => (float) $pay->amount,
                    'type' => 'OUT',
                ];
            });

        // 3. Operational Expenses (Pengeluaran / Keluar / Debet)
        $expenses = Expense::where('id_bank', $id)
            ->where(function($q) use ($startDate, $endDate) {
                $q->whereBetween('date', [$startDate, $endDate])
                  ->orWhere(function($sub) use ($startDate, $endDate) {
                      $sub->whereNull('date')->whereBetween('created_at', [$startDate, $endDate]);
                  });
            })
            ->get()->map(function ($exp) {
                return [
                    'date' => $exp->date ?: ($exp->created_at ? $exp->created_at->toDateString() : Carbon::now()->toDateString()),
                    'module' => 'Expense Operasional',
                    'badge_class' => 'bg-label-warning',
                    'ref_no' => $exp->no_expense ?: ('#EXP-' . $exp->id),
                    'description' => "Pengeluaran Operasional / Kas: " . ($exp->memo ?: 'Beban Operasional'),
                    'in' => 0,
                    'out' => (float) $exp->amount,
                    'type' => 'OUT',
                ];
            });

        // 4. Internal Transfers - Masuk (Inflow)
        $transfersIn = BankTransfer::where('id_to_bank', $id)
            ->whereBetween('date', [$startDate, $endDate])
            ->with('fromBank')
            ->get()->map(function ($trf) {
                $fromBankName = $trf->fromBank ? "{$trf->fromBank->bank} ({$trf->fromBank->no_rek})" : 'Rekening Lain';
                return [
                    'date' => $trf->date->toDateString(),
                    'module' => 'Transfer Masuk',
                    'badge_class' => 'bg-label-info',
                    'ref_no' => $trf->transfer_number,
                    'description' => "Transfer Dana Masuk dari {$fromBankName}" . ($trf->note ? " - {$trf->note}" : ''),
                    'in' => (float) $trf->amount,
                    'out' => 0,
                    'type' => 'IN',
                ];
            });

        // 5. Internal Transfers - Keluar (Outflow)
        $transfersOut = BankTransfer::where('id_from_bank', $id)
            ->whereBetween('date', [$startDate, $endDate])
            ->with('toBank')
            ->get()->map(function ($trf) {
                $toBankName = $trf->toBank ? "{$trf->toBank->bank} ({$trf->toBank->no_rek})" : 'Rekening Lain';
                $feeNote = $trf->fee > 0 ? " (Termasuk Biaya Admin: Rp " . number_format($trf->fee, 0, ',', '.') . ")" : "";
                return [
                    'date' => $trf->date->toDateString(),
                    'module' => 'Transfer Keluar',
                    'badge_class' => 'bg-label-dark',
                    'ref_no' => $trf->transfer_number,
                    'description' => "Transfer Dana Keluar ke {$toBankName}{$feeNote}" . ($trf->note ? " - {$trf->note}" : ''),
                    'in' => 0,
                    'out' => (float) ($trf->amount + $trf->fee),
                    'type' => 'OUT',
                ];
            });

        // 6. Management Fee Disbursements (Pengeluaran / Keluar / Debet)
        $uqFees = UnitQuotation::where('id_source_bank', $id)
            ->where('fee_payment_status', 'paid')
            ->where(function($q) use ($startDate, $endDate) {
                $q->whereBetween('fee_transfer_date', [$startDate, $endDate])
                  ->orWhere(function($sub) use ($startDate, $endDate) {
                      $sub->whereNull('fee_transfer_date')->whereBetween('updated_at', [$startDate, $endDate]);
                  });
            })
            ->with('client')
            ->get()->map(function ($uq) {
                $netFee = (float) ($uq->fee_tax_data?->net_fee ?: $uq->fee);
                $clientName = $uq->client?->company ?? '-';
                $recipient = $uq->fee_bank_holder ? " ke {$uq->fee_bank_holder} ({$uq->fee_bank_name})" : "";
                return [
                    'date' => $uq->fee_transfer_date ? Carbon::parse($uq->fee_transfer_date)->toDateString() : ($uq->updated_at ? $uq->updated_at->toDateString() : Carbon::now()->toDateString()),
                    'module' => 'Management Fee',
                    'badge_class' => 'bg-label-danger',
                    'ref_no' => $uq->no_quote ?: ('#FEE-UQ-' . $uq->id),
                    'description' => "Pencairan Management Fee Quote: {$uq->no_quote} ({$clientName}){$recipient}" . ($uq->fee_transfer_note ? " - {$uq->fee_transfer_note}" : ''),
                    'in' => 0,
                    'out' => $netFee,
                    'type' => 'OUT',
                ];
            });

        $manualFees = ManualManagementFee::where('id_source_bank', $id)
            ->where('fee_payment_status', 'paid')
            ->where(function($q) use ($startDate, $endDate) {
                $q->whereBetween('fee_transfer_date', [$startDate, $endDate])
                  ->orWhere(function($sub) use ($startDate, $endDate) {
                      $sub->whereNull('fee_transfer_date')->whereBetween('updated_at', [$startDate, $endDate]);
                  });
            })
            ->with('client')
            ->get()->map(function ($mf) {
                $amount = (float) ($mf->fee_tax_data->net_fee ?: $mf->gross_fee);
                $clientName = $mf->company_name;
                $recipient = $mf->fee_bank_holder ? " ke {$mf->fee_bank_holder} ({$mf->fee_bank_name})" : "";
                return [
                    'date' => $mf->fee_transfer_date ? Carbon::parse($mf->fee_transfer_date)->toDateString() : ($mf->updated_at ? $mf->updated_at->toDateString() : Carbon::now()->toDateString()),
                    'module' => 'Management Fee (Manual)',
                    'badge_class' => 'bg-label-danger',
                    'ref_no' => $mf->reference_no ?: ('#FEE-MAN-' . $mf->id),
                    'description' => "Pencairan Management Fee: {$mf->title} ({$clientName}){$recipient}" . ($mf->fee_transfer_note ? " - {$mf->fee_transfer_note}" : ''),
                    'in' => 0,
                    'out' => $amount,
                    'type' => 'OUT',
                ];
            });

        // 7. Petty Cash Outflow (Pengisian ke Kas Kecil dari Bank ini)
        $pettyCashTopupsOut = PettyCashTransaction::where('id_source_bank', $id)
            ->where('type', 'topup')
            ->whereBetween('date', [$startDate, $endDate])
            ->with('bank')
            ->get()->map(function ($pct) {
                $targetName = $pct->bank ? "{$pct->bank->bank} ({$pct->bank->no_rek})" : 'Kas Kecil';
                return [
                    'date' => $pct->date->toDateString(),
                    'module' => 'Petty Cash Top-Up',
                    'badge_class' => 'bg-label-dark',
                    'ref_no' => $pct->voucher_number ?: ('#BKM-' . $pct->id),
                    'description' => "Pengisian Dana Kas Kecil ke {$targetName}" . ($pct->description ? " - {$pct->description}" : ''),
                    'in' => 0,
                    'out' => (float) $pct->amount,
                    'type' => 'OUT',
                ];
            });

        // 8. Petty Cash Inflow (Penerimaan Top-up jika ini akun Kas Kecil)
        $pettyCashTopupsIn = PettyCashTransaction::where('id_bank', $id)
            ->where('type', 'topup')
            ->whereBetween('date', [$startDate, $endDate])
            ->with('sourceBank')
            ->get()->map(function ($pct) {
                $srcName = $pct->sourceBank ? "{$pct->sourceBank->bank} ({$pct->sourceBank->no_rek})" : 'Bank Kantor';
                return [
                    'date' => $pct->date->toDateString(),
                    'module' => 'Petty Cash Reimbursement',
                    'badge_class' => 'bg-label-info',
                    'ref_no' => $pct->voucher_number ?: ('#BKM-' . $pct->id),
                    'description' => "Penerimaan Dana Kas Kecil dari {$srcName}" . ($pct->description ? " - {$pct->description}" : ''),
                    'in' => (float) $pct->amount,
                    'out' => 0,
                    'type' => 'IN',
                ];
            });

        // 9. Petty Cash Disbursements (Pengeluaran dari akun Kas Kecil ini)
        $pettyCashDisbursements = PettyCashTransaction::where('id_bank', $id)
            ->where('type', 'disbursement')
            ->whereBetween('date', [$startDate, $endDate])
            ->get()->map(function ($pct) {
                $recipientStr = $pct->recipient ? " [Penerima: {$pct->recipient}]" : '';
                return [
                    'date' => $pct->date->toDateString(),
                    'module' => 'Petty Cash Expense',
                    'badge_class' => 'bg-label-warning',
                    'ref_no' => $pct->voucher_number ?: ('#BKK-' . $pct->id),
                    'description' => "Kas Kecil ({$pct->category}): {$pct->description}{$recipientStr}",
                    'in' => 0,
                    'out' => (float) $pct->amount,
                    'type' => 'OUT',
                ];
            });

        $all = $arPayments->concat($apPayments)
            ->concat($expenses)
            ->concat($transfersIn)
            ->concat($transfersOut)
            ->concat($uqFees)
            ->concat($manualFees)
            ->concat($pettyCashTopupsOut)
            ->concat($pettyCashTopupsIn)
            ->concat($pettyCashDisbursements)
            ->sortBy('date')
            ->values();

        $totalIn = 0;
        $totalOut = 0;
        $running = $openingBalance;
        $ledger = $all->map(function ($item) use (&$running, &$totalIn, &$totalOut) {
            $totalIn += $item['in'];
            $totalOut += $item['out'];
            $running = $running + $item['in'] - $item['out'];
            $item['running_balance'] = $running;
            return (object) $item;
        });

        $closingBalance = $running;

        return view('pages.finance.bank.statement', compact(
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

    /**
     * Printable view of Bank Statement.
     */
    public function statementPrint(Request $request, $id)
    {
        $bank = Bank::findOrFail($id);
        $startDate = $request->get('start_date', Carbon::now()->subMonths(3)->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', Carbon::now()->toDateString());

        // Previous transactions before start_date
        $prevAr = (float) Payment::where('id_bank', $id)
            ->where('level', 1)
            ->where(function($q) use ($startDate) {
                $q->whereDate('date', '<', $startDate)
                  ->orWhere(function($sub) use ($startDate) {
                      $sub->whereNull('date')->whereDate('created_at', '<', $startDate);
                  });
            })->sum('amount');

        $prevAp = (float) PurchasePayment::where('id_bank', $id)
            ->where(function($q) use ($startDate) {
                $q->whereDate('date', '<', $startDate)
                  ->orWhere(function($sub) use ($startDate) {
                      $sub->whereNull('date')->whereDate('created_at', '<', $startDate);
                  });
            })->sum('amount');

        $prevExpense = (float) Expense::where('id_bank', $id)
            ->where(function($q) use ($startDate) {
                $q->whereDate('date', '<', $startDate)
                  ->orWhere(function($sub) use ($startDate) {
                      $sub->whereNull('date')->whereDate('created_at', '<', $startDate);
                  });
            })->sum('amount');

        $prevTrfIn = (float) BankTransfer::where('id_to_bank', $id)
            ->whereDate('date', '<', $startDate)
            ->sum('amount');

        $prevTrfOut = (float) BankTransfer::where('id_from_bank', $id)
            ->whereDate('date', '<', $startDate)
            ->sum(DB::raw('amount + fee'));

        $prevUqFees = (float) UnitQuotation::where('id_source_bank', $id)
            ->where('fee_payment_status', 'paid')
            ->where(function($q) use ($startDate) {
                $q->whereDate('fee_transfer_date', '<', $startDate)
                  ->orWhere(function($sub) use ($startDate) {
                      $sub->whereNull('fee_transfer_date')->whereDate('updated_at', '<', $startDate);
                  });
            })->get()->sum(fn($q) => $q->fee_tax_data?->net_fee ?: $q->fee);

        $prevManualFees = (float) ManualManagementFee::where('id_source_bank', $id)
            ->where('fee_payment_status', 'paid')
            ->where(function($q) use ($startDate) {
                $q->whereDate('fee_transfer_date', '<', $startDate)
                  ->orWhere(function($sub) use ($startDate) {
                      $sub->whereNull('fee_transfer_date')->whereDate('updated_at', '<', $startDate);
                  });
            })->get()->sum(fn($mf) => $mf->fee_tax_data->net_fee ?: $mf->gross_fee);

        // Petty cash previous
        $prevPctTopupOut = (float) PettyCashTransaction::where('id_source_bank', $id)
            ->where('type', 'topup')
            ->whereDate('date', '<', $startDate)
            ->sum('amount');

        $prevPctTopupIn = (float) PettyCashTransaction::where('id_bank', $id)
            ->where('type', 'topup')
            ->whereDate('date', '<', $startDate)
            ->sum('amount');

        $prevPctDisburseOut = (float) PettyCashTransaction::where('id_bank', $id)
            ->where('type', 'disbursement')
            ->whereDate('date', '<', $startDate)
            ->sum('amount');

        $initialBalance = (float) ($bank->initial_balance ?: 0);
        $openingBalance = $initialBalance + $prevAr + $prevTrfIn + $prevPctTopupIn - ($prevAp + $prevExpense + $prevTrfOut + $prevUqFees + $prevManualFees + $prevPctTopupOut + $prevPctDisburseOut);

        // Transactions in date range
        $arPayments = Payment::where('id_bank', $id)
            ->where('level', 1)
            ->where(function($q) use ($startDate, $endDate) {
                $q->whereBetween('date', [$startDate, $endDate])
                  ->orWhere(function($sub) use ($startDate, $endDate) {
                      $sub->whereNull('date')->whereBetween('created_at', [$startDate, $endDate]);
                  });
            })
            ->with(['quotation.pic.client', 'unitQuotation.client'])
            ->get()->map(function ($pay) {
                $clientName = '-';
                $refNo = '#RCPT-' . $pay->id;
                if ($pay->unitQuotation) {
                    $clientName = $pay->unitQuotation->client?->company ?? '-';
                    $refNo = $pay->unitQuotation->no_quote ?: $refNo;
                } elseif ($pay->quotation) {
                    $clientName = $pay->quotation->pic?->client?->company ?? '-';
                    $refNo = $pay->quotation->no_quote ?: $refNo;
                }
                return [
                    'date' => $pay->date ?: ($pay->created_at ? $pay->created_at->toDateString() : Carbon::now()->toDateString()),
                    'module' => 'AR (Customer Receipt)',
                    'ref_no' => $refNo,
                    'description' => "Penerimaan Pembayaran dari {$clientName}" . ($pay->note ? " ({$pay->note})" : ''),
                    'in' => (float) $pay->amount,
                    'out' => 0,
                    'type' => 'IN',
                ];
            });

        $apPayments = PurchasePayment::where('id_bank', $id)
            ->where(function($q) use ($startDate, $endDate) {
                $q->whereBetween('date', [$startDate, $endDate])
                  ->orWhere(function($sub) use ($startDate, $endDate) {
                      $sub->whereNull('date')->whereBetween('created_at', [$startDate, $endDate]);
                  });
            })
            ->with('supplier')
            ->get()->map(function ($pay) {
                $supplierName = $pay->supplier?->name ?? 'Supplier / Vendor';
                return [
                    'date' => $pay->date ?: ($pay->created_at ? $pay->created_at->toDateString() : Carbon::now()->toDateString()),
                    'module' => 'AP (Supplier Payment)',
                    'ref_no' => $pay->payment_number ?: ('#PAY-AP-' . $pay->id),
                    'description' => "Pembayaran Tagihan ke {$supplierName}" . ($pay->note ? " ({$pay->note})" : ''),
                    'in' => 0,
                    'out' => (float) $pay->amount,
                    'type' => 'OUT',
                ];
            });

        $expenses = Expense::where('id_bank', $id)
            ->where(function($q) use ($startDate, $endDate) {
                $q->whereBetween('date', [$startDate, $endDate])
                  ->orWhere(function($sub) use ($startDate, $endDate) {
                      $sub->whereNull('date')->whereBetween('created_at', [$startDate, $endDate]);
                  });
            })
            ->get()->map(function ($exp) {
                return [
                    'date' => $exp->date ?: ($exp->created_at ? $exp->created_at->toDateString() : Carbon::now()->toDateString()),
                    'module' => 'Expense Operasional',
                    'ref_no' => $exp->no_expense ?: ('#EXP-' . $exp->id),
                    'description' => "Pengeluaran Kas: " . ($exp->memo ?: 'Beban Operasional'),
                    'in' => 0,
                    'out' => (float) $exp->amount,
                    'type' => 'OUT',
                ];
            });

        $transfersIn = BankTransfer::where('id_to_bank', $id)
            ->whereBetween('date', [$startDate, $endDate])
            ->with('fromBank')
            ->get()->map(function ($trf) {
                $fromBankName = $trf->fromBank ? "{$trf->fromBank->bank} ({$trf->fromBank->no_rek})" : 'Rekening Lain';
                return [
                    'date' => $trf->date->toDateString(),
                    'module' => 'Transfer Masuk',
                    'ref_no' => $trf->transfer_number,
                    'description' => "Transfer Dana Masuk dari {$fromBankName}" . ($trf->note ? " - {$trf->note}" : ''),
                    'in' => (float) $trf->amount,
                    'out' => 0,
                    'type' => 'IN',
                ];
            });

        $transfersOut = BankTransfer::where('id_from_bank', $id)
            ->whereBetween('date', [$startDate, $endDate])
            ->with('toBank')
            ->get()->map(function ($trf) {
                $toBankName = $trf->toBank ? "{$trf->toBank->bank} ({$trf->toBank->no_rek})" : 'Rekening Lain';
                $feeNote = $trf->fee > 0 ? " (Termasuk Biaya Admin: Rp " . number_format($trf->fee, 0, ',', '.') . ")" : "";
                return [
                    'date' => $trf->date->toDateString(),
                    'module' => 'Transfer Keluar',
                    'ref_no' => $trf->transfer_number,
                    'description' => "Transfer Dana Keluar ke {$toBankName}{$feeNote}" . ($trf->note ? " - {$trf->note}" : ''),
                    'in' => 0,
                    'out' => (float) ($trf->amount + $trf->fee),
                    'type' => 'OUT',
                ];
            });

        $uqFees = UnitQuotation::where('id_source_bank', $id)
            ->where('fee_payment_status', 'paid')
            ->where(function($q) use ($startDate, $endDate) {
                $q->whereBetween('fee_transfer_date', [$startDate, $endDate])
                  ->orWhere(function($sub) use ($startDate, $endDate) {
                      $sub->whereNull('fee_transfer_date')->whereBetween('updated_at', [$startDate, $endDate]);
                  });
            })
            ->with('client')
            ->get()->map(function ($uq) {
                $netFee = (float) ($uq->fee_tax_data?->net_fee ?: $uq->fee);
                $clientName = $uq->client?->company ?? '-';
                $recipient = $uq->fee_bank_holder ? " ke {$uq->fee_bank_holder} ({$uq->fee_bank_name})" : "";
                return [
                    'date' => $uq->fee_transfer_date ? Carbon::parse($uq->fee_transfer_date)->toDateString() : ($uq->updated_at ? $uq->updated_at->toDateString() : Carbon::now()->toDateString()),
                    'module' => 'Management Fee',
                    'ref_no' => $uq->no_quote ?: ('#FEE-UQ-' . $uq->id),
                    'description' => "Pencairan Management Fee Quote: {$uq->no_quote} ({$clientName}){$recipient}" . ($uq->fee_transfer_note ? " - {$uq->fee_transfer_note}" : ''),
                    'in' => 0,
                    'out' => $netFee,
                    'type' => 'OUT',
                ];
            });

        $manualFees = ManualManagementFee::where('id_source_bank', $id)
            ->where('fee_payment_status', 'paid')
            ->where(function($q) use ($startDate, $endDate) {
                $q->whereBetween('fee_transfer_date', [$startDate, $endDate])
                  ->orWhere(function($sub) use ($startDate, $endDate) {
                      $sub->whereNull('fee_transfer_date')->whereBetween('updated_at', [$startDate, $endDate]);
                  });
            })
            ->with('client')
            ->get()->map(function ($mf) {
                $amount = (float) ($mf->fee_tax_data->net_fee ?: $mf->gross_fee);
                $clientName = $mf->company_name;
                $recipient = $mf->fee_bank_holder ? " ke {$mf->fee_bank_holder} ({$mf->fee_bank_name})" : "";
                return [
                    'date' => $mf->fee_transfer_date ? Carbon::parse($mf->fee_transfer_date)->toDateString() : ($mf->updated_at ? $mf->updated_at->toDateString() : Carbon::now()->toDateString()),
                    'module' => 'Management Fee (Manual)',
                    'ref_no' => $mf->reference_no ?: ('#FEE-MAN-' . $mf->id),
                    'description' => "Pencairan Management Fee: {$mf->title} ({$clientName}){$recipient}" . ($mf->fee_transfer_note ? " - {$mf->fee_transfer_note}" : ''),
                    'in' => 0,
                    'out' => $amount,
                    'type' => 'OUT',
                ];
            });

        // Petty Cash Outflow (Pengisian ke Kas Kecil dari Bank ini)
        $pettyCashTopupsOut = PettyCashTransaction::where('id_source_bank', $id)
            ->where('type', 'topup')
            ->whereBetween('date', [$startDate, $endDate])
            ->with('bank')
            ->get()->map(function ($pct) {
                $targetName = $pct->bank ? "{$pct->bank->bank} ({$pct->bank->no_rek})" : 'Kas Kecil';
                return [
                    'date' => $pct->date->toDateString(),
                    'module' => 'Petty Cash Top-Up',
                    'ref_no' => $pct->voucher_number ?: ('#BKM-' . $pct->id),
                    'description' => "Pengisian Dana Kas Kecil ke {$targetName}" . ($pct->description ? " - {$pct->description}" : ''),
                    'in' => 0,
                    'out' => (float) $pct->amount,
                    'type' => 'OUT',
                ];
            });

        // Petty Cash Inflow (Penerimaan Top-up jika ini akun Kas Kecil)
        $pettyCashTopupsIn = PettyCashTransaction::where('id_bank', $id)
            ->where('type', 'topup')
            ->whereBetween('date', [$startDate, $endDate])
            ->with('sourceBank')
            ->get()->map(function ($pct) {
                $srcName = $pct->sourceBank ? "{$pct->sourceBank->bank} ({$pct->sourceBank->no_rek})" : 'Bank Kantor';
                return [
                    'date' => $pct->date->toDateString(),
                    'module' => 'Petty Cash Reimbursement',
                    'ref_no' => $pct->voucher_number ?: ('#BKM-' . $pct->id),
                    'description' => "Penerimaan Dana Kas Kecil dari {$srcName}" . ($pct->description ? " - {$pct->description}" : ''),
                    'in' => (float) $pct->amount,
                    'out' => 0,
                    'type' => 'IN',
                ];
            });

        // Petty Cash Disbursements (Pengeluaran dari akun Kas Kecil ini)
        $pettyCashDisbursements = PettyCashTransaction::where('id_bank', $id)
            ->where('type', 'disbursement')
            ->whereBetween('date', [$startDate, $endDate])
            ->get()->map(function ($pct) {
                $recipientStr = $pct->recipient ? " [Penerima: {$pct->recipient}]" : '';
                return [
                    'date' => $pct->date->toDateString(),
                    'module' => 'Petty Cash Expense',
                    'ref_no' => $pct->voucher_number ?: ('#BKK-' . $pct->id),
                    'description' => "Kas Kecil ({$pct->category}): {$pct->description}{$recipientStr}",
                    'in' => 0,
                    'out' => (float) $pct->amount,
                    'type' => 'OUT',
                ];
            });

        $all = $arPayments->concat($apPayments)
            ->concat($expenses)
            ->concat($transfersIn)
            ->concat($transfersOut)
            ->concat($uqFees)
            ->concat($manualFees)
            ->concat($pettyCashTopupsOut)
            ->concat($pettyCashTopupsIn)
            ->concat($pettyCashDisbursements)
            ->sortBy('date')
            ->values();

        $totalIn = 0;
        $totalOut = 0;
        $running = $openingBalance;
        $ledger = $all->map(function ($item) use (&$running, &$totalIn, &$totalOut) {
            $totalIn += $item['in'];
            $totalOut += $item['out'];
            $running = $running + $item['in'] - $item['out'];
            $item['running_balance'] = $running;
            return (object) $item;
        });

        $closingBalance = $running;

        return view('pages.finance.bank.statement-print', compact(
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
