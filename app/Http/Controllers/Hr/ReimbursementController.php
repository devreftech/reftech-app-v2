<?php

namespace App\Http\Controllers\Hr;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\HrReimbursement;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReimbursementController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->input('status');
        $claimType = $request->input('claim_type');

        $query = HrReimbursement::with(['employee.user', 'employee.department', 'approver']);

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
            'paid_amount' => $allClaims->where('status', 'Paid')->sum('amount'),
            'total_claims' => $allClaims->count(),
        ];

        $employees = Employee::with('user')->where('employment_status', '!=', 'Resign')->get();

        return view('pages.hr.reimbursements.index', compact('reimbursements', 'stats', 'employees', 'status', 'claimType'));
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
}
