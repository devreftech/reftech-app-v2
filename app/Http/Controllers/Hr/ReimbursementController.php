<?php

namespace App\Http\Controllers\Hr;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Bank;
use App\Models\DetailExpense;
use App\Models\Employee;
use App\Models\Expense;
use App\Models\HrReimbursement;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ReimbursementController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->input('status');
        $claimType = $request->input('claim_type');

        $query = HrReimbursement::with(['employee.user', 'employee.department', 'approver', 'expense.bank']);

        if ($status) {
            $query->where('status', $status);
        }

        if ($claimType) {
            $query->where('claim_type', $claimType);
        }

        $reimbursements = $query->orderByDesc('created_at')->paginate(20)->withQueryString();

        $allClaims = HrReimbursement::all();
        $stats = [
            'pending' => $allClaims->where('status', 'Pending')->count(),
            'approved_count' => $allClaims->where('status', 'Approved')->count(),
            'paid_amount' => (float) $allClaims->where('status', 'Paid')->sum('amount'),
            'total_claims' => $allClaims->count(),
        ];

        $employees = Employee::with('user')->where('employment_status', '!=', 'Resign')->get();
        $banks = Bank::orderBy('bank')->get();
        $accounts = Account::where('category', 'Expense')->orderBy('code')->get();

        return view('pages.hr.reimbursements.index', compact('reimbursements', 'stats', 'employees', 'status', 'claimType', 'banks', 'accounts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'claim_type' => 'required|in:BBM / Bensin,Tol / Parkir,Akomodasi / Hotel,Konsumsi Lapangan,Medis / Pengobatan,Lainnya',
            'amount' => 'required|numeric|min:1000',
            'event_date' => 'required|date',
            'description' => 'required|string|max:1000',
            'receipt_image' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:4096',
        ]);

        $receiptPath = null;
        if ($request->hasFile('receipt_image')) {
            $file = $request->file('receipt_image');
            $filename = 'claim_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('asset/hr/claims'), $filename);
            $receiptPath = 'asset/hr/claims/' . $filename;
        }

        $claimNumber = 'CLM-' . date('Ym') . '-' . str_pad(HrReimbursement::count() + 1, 4, '0', STR_PAD_LEFT);

        HrReimbursement::create([
            'employee_id' => $validated['employee_id'],
            'claim_number' => $claimNumber,
            'claim_type' => $validated['claim_type'],
            'amount' => $validated['amount'],
            'event_date' => $validated['event_date'],
            'description' => $validated['description'],
            'receipt_image' => $receiptPath,
            'status' => 'Pending',
        ]);

        return redirect()->back()->with('success', 'Klaim reimbursement berhasil diajukan dengan nomor: ' . $claimNumber);
    }

    public function update(Request $request, HrReimbursement $reimbursement)
    {
        if ($reimbursement->status !== 'Pending') {
            return redirect()->back()->with('error', 'Hanya pengajuan klaim berstatus Pending yang dapat diedit.');
        }

        // Authorization check: only owner or HR access
        $user = Auth::user();
        $isOwner = $reimbursement->employee?->user_id === $user->id || $reimbursement->employee_id === $user->employee?->id;
        $isHrAdmin = in_array($user->role, ['Admin', 'Developer', 'Finance', 'Finance Manager']) || (method_exists($user, 'isDeveloper') && $user->isDeveloper());

        if (!$isOwner && !$isHrAdmin) {
            return redirect()->back()->with('error', 'Anda tidak memiliki hak untuk mengedit pengajuan reimbursement ini.');
        }

        $validated = $request->validate([
            'claim_type' => 'required|in:BBM / Bensin,Tol / Parkir,Akomodasi / Hotel,Konsumsi Lapangan,Medis / Pengobatan,Lainnya',
            'amount' => 'required|numeric|min:1000',
            'event_date' => 'required|date',
            'description' => 'required|string|max:1000',
            'receipt_image' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:4096',
        ]);

        $data = [
            'claim_type' => $validated['claim_type'],
            'amount' => $validated['amount'],
            'event_date' => $validated['event_date'],
            'description' => $validated['description'],
        ];

        if ($request->hasFile('receipt_image')) {
            // Delete old file if exists
            if ($reimbursement->receipt_image && File::exists(public_path($reimbursement->receipt_image))) {
                File::delete(public_path($reimbursement->receipt_image));
            }

            $file = $request->file('receipt_image');
            $filename = 'claim_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('asset/hr/claims'), $filename);
            $data['receipt_image'] = 'asset/hr/claims/' . $filename;
        }

        $reimbursement->update($data);

        return redirect()->back()->with('success', "Pengajuan reimbursement {$reimbursement->claim_number} berhasil diperbarui.");
    }

    public function destroy(HrReimbursement $reimbursement)
    {
        if ($reimbursement->status !== 'Pending') {
            return redirect()->back()->with('error', 'Hanya pengajuan klaim berstatus Pending yang dapat dihapus.');
        }

        $user = Auth::user();
        $isOwner = $reimbursement->employee?->user_id === $user->id || $reimbursement->employee_id === $user->employee?->id;
        $isHrAdmin = in_array($user->role, ['Admin', 'Developer', 'Finance', 'Finance Manager']) || (method_exists($user, 'isDeveloper') && $user->isDeveloper());

        if (!$isOwner && !$isHrAdmin) {
            return redirect()->back()->with('error', 'Anda tidak memiliki hak untuk menghapus pengajuan reimbursement ini.');
        }

        if ($reimbursement->receipt_image && File::exists(public_path($reimbursement->receipt_image))) {
            File::delete(public_path($reimbursement->receipt_image));
        }

        $claimNum = $reimbursement->claim_number;
        $reimbursement->delete();

        return redirect()->back()->with('success', "Pengajuan reimbursement {$claimNum} berhasil dihapus.");
    }

    public function approve(HrReimbursement $reimbursement)
    {
        $reimbursement->update([
            'status' => 'Approved',
            'approved_by' => Auth::id(),
            'approved_at' => Carbon::now(),
        ]);

        return redirect()->back()->with('success', "Klaim {$reimbursement->claim_number} berhasil disetujui (Approved).");
    }

    public function reject(Request $request, HrReimbursement $reimbursement)
    {
        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $reimbursement->update([
            'status' => 'Rejected',
            'approved_by' => Auth::id(),
            'approved_at' => Carbon::now(),
            'rejection_reason' => $validated['rejection_reason'],
        ]);

        return redirect()->back()->with('success', "Klaim {$reimbursement->claim_number} telah ditolak.");
    }

    public function markPaid(HrReimbursement $reimbursement)
    {
        $reimbursement->update([
            'status' => 'Paid',
            'paid_at' => Carbon::now(),
        ]);

        return redirect()->back()->with('success', "Klaim {$reimbursement->claim_number} telah dicairkan (Paid).");
    }

    /**
     * Posting Pembayaran Klaim Reimbursement ke Finance Expense
     */
    public function postToExpense(Request $request, HrReimbursement $reimbursement)
    {
        if ($reimbursement->expense_id) {
            return redirect()->back()->with('error', 'Klaim reimbursement ini sudah pernah dibukukan ke Finance Expense (Voucher #' . $reimbursement->expense?->no_expense . ').');
        }

        $validated = $request->validate([
            'id_bank' => 'required|exists:bank,id',
            'id_account' => 'required|exists:account,id',
            'payment_date' => 'required|date',
            'memo' => 'nullable|string|max:500',
        ]);

        $bank = Bank::findOrFail($validated['id_bank']);
        $totalAmount = (float) $reimbursement->amount;

        if ($bank->saldo < $totalAmount) {
            return redirect()->back()->with('error', "Saldo bank {$bank->bank} tidak mencukupi (Tersedia: Rp " . number_format($bank->saldo, 0, ',', '.') . ", Dibutuhkan: Rp " . number_format($totalAmount, 0, ',', '.') . ").");
        }

        DB::beginTransaction();
        try {
            $date = Carbon::parse($validated['payment_date']);
            $year = $date->year;
            $month = $date->month;
            $romanMonths = ['', 'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];
            $roman = $romanMonths[$month] ?? 'I';

            $count = Expense::whereYear('date', $year)->count();
            $noExpense = 'EXP' . str_pad($count + 1, 3, '0', STR_PAD_LEFT) . '-' . $roman . '-' . $year;

            // Potong saldo bank
            $bank->saldo -= $totalAmount;
            $bank->save();

            $empName = $reimbursement->employee?->user?->name ?? 'Karyawan';
            $memo = $validated['memo'] ?: "Reimbursement {$reimbursement->claim_type} ({$reimbursement->claim_number}) - {$empName}";

            // Simpan Expense Header
            $expense = new Expense();
            $expense->id_bank = $bank->id;
            $expense->no_expense = $noExpense;
            $expense->no_invoice = $reimbursement->claim_number;
            $expense->memo = $memo;
            $expense->date = $validated['payment_date'];
            $expense->amount = $totalAmount;
            $expense->save();

            // Simpan Detail Expense Jurnal COA
            $detailExpense = new DetailExpense();
            $detailExpense->id_expense = $expense->id;
            $detailExpense->id_account = $validated['id_account'];
            $detailExpense->memo = "Reimbursement {$reimbursement->claim_type} - {$reimbursement->description}";
            $detailExpense->amount = $totalAmount;
            $detailExpense->save();

            // Update Reimbursement status
            $reimbursement->update([
                'status' => 'Paid',
                'paid_at' => Carbon::now(),
                'expense_id' => $expense->id,
            ]);

            DB::commit();

            return redirect()->back()->with('success', "Klaim {$reimbursement->claim_number} berhasil dicairkan & dibukukan ke Finance Expense dengan voucher #{$noExpense}! Saldo bank {$bank->bank} dipotong sebesar Rp " . number_format($totalAmount, 0, ',', '.'));
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal memposting reimbursement ke Finance Expense: ' . $e->getMessage());
        }
    }

    /**
     * Endpoint API untuk polling background status klaim pending & cuti (tanpa reload halaman)
     */
    public function pendingCheck()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['reimbursements' => 0, 'leaves' => 0, 'total' => 0]);
        }

        $isFinanceOrHr = in_array($user->role, ['Admin', 'Developer', 'Finance', 'Finance Manager', 'Accounting', 'Super Admin']) || (method_exists($user, 'isDeveloper') && $user->isDeveloper());
        
        $reimbursementCount = 0;
        if ($isFinanceOrHr) {
            $reimbursementCount = HrReimbursement::where('status', 'Pending')->count();
        }

        $isLeaveEligible = \App\Services\Hr\LeaveAlertService::isUserEligible($user);
        $leaveCount = 0;
        if ($isLeaveEligible) {
            $leaveCount = \App\Services\Hr\LeaveAlertService::getPendingCount();
        }

        return response()->json([
            'reimbursements' => $reimbursementCount,
            'leaves' => $leaveCount,
            'total' => $reimbursementCount + $leaveCount,
        ]);
    }
}
