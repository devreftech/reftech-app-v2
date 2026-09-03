<?php

namespace App\Http\Controllers;

use App\Models\UnitQuotation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ManagementFeeController extends Controller
{
    /**
     * Tampilkan daftar Management Fee di bawah modul Finance.
     */
    public function index(Request $request)
    {
        $query = UnitQuotation::with(['client', 'sales', 'feePaidBy', 'invoices', 'details'])
            ->where('fee', '>', 0)
            ->where('status', 'po_received');

        // Filter Status Pencairan Fee
        if ($request->filled('fee_status') && $request->fee_status !== 'all') {
            $query->where('fee_payment_status', $request->fee_status);
        }

        // Filter Sales Person
        if ($request->filled('sales_id') && $request->sales_id !== 'all') {
            $query->where('id_sales', $request->sales_id);
        }

        // Filter Pencarian Keyword
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('no_quote', 'like', "%{$search}%")
                  ->orWhere('po_number', 'like', "%{$search}%")
                  ->orWhere('fee_bank_holder', 'like', "%{$search}%")
                  ->orWhere('fee_bank_account', 'like', "%{$search}%")
                  ->orWhereHas('client', function ($cq) use ($search) {
                      $cq->where('company', 'like', "%{$search}%")
                         ->orWhere('name', 'like', "%{$search}%");
                  });
            });
        }

        // Filter Rentang Tanggal
        if ($request->filled('start_date')) {
            $query->whereDate('date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('date', '<=', $request->end_date);
        }

        $items = $query->orderBy('date', 'desc')->paginate(15)->withQueryString();

        // Hitung Statistik / Summary Dashboard Fee (Hanya yang sudah resmi PO Received)
        $allFees = UnitQuotation::where('fee', '>', 0)
            ->where('status', 'po_received')
            ->get();
        $totalGrossFee = $allFees->sum('fee');
        
        $totalTaxDeduction = 0;
        $totalNetFee = 0;
        $totalPaidFee = 0;
        $totalPendingFee = 0;

        foreach ($allFees as $fq) {
            $taxData = $fq->fee_tax_data;
            $totalTaxDeduction += $taxData->tax_amount;
            $totalNetFee += $taxData->net_fee;

            if ($fq->fee_payment_status === 'paid') {
                $totalPaidFee += $taxData->net_fee;
            } else {
                $totalPendingFee += $taxData->net_fee;
            }
        }

        $salesList = User::whereIn('role', ['Sales', 'Admin', 'Sales Manager'])
            ->orderBy('name')
            ->get();

        return view('pages.finance.management-fee.index', compact(
            'items',
            'totalGrossFee',
            'totalTaxDeduction',
            'totalNetFee',
            'totalPaidFee',
            'totalPendingFee',
            'salesList'
        ));
    }

    /**
     * Update data rekening & proses pencairan/transfer fee oleh tim Finance.
     */
    public function updateDisbursement(Request $request, $id)
    {
        $quote = UnitQuotation::findOrFail($id);

        $request->validate([
            'fee_bank_name'       => 'nullable|string|max:100',
            'fee_bank_account'    => 'nullable|string|max:100',
            'fee_bank_holder'     => 'nullable|string|max:150',
            'fee_payment_status'  => 'required|in:unpaid,pending_transfer,paid',
            'fee_transfer_date'   => 'nullable|date',
            'fee_transfer_note'   => 'nullable|string|max:1000',
            'fee_transfer_proof'  => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
        ]);

        $quote->fee_bank_name      = $request->fee_bank_name;
        $quote->fee_bank_account   = $request->fee_bank_account;
        $quote->fee_bank_holder    = $request->fee_bank_holder;
        $quote->fee_payment_status = $request->fee_payment_status;
        $quote->fee_transfer_note  = $request->fee_transfer_note;

        if ($request->fee_payment_status === 'paid') {
            $quote->fee_transfer_date = $request->fee_transfer_date ?: now();
            $quote->fee_paid_by       = Auth::id();
        } elseif ($request->fee_payment_status === 'unpaid') {
            $quote->fee_transfer_date = null;
            $quote->fee_paid_by       = null;
        }

        // Upload bukti transfer jika ada
        if ($request->hasFile('fee_transfer_proof')) {
            if ($quote->fee_transfer_proof && Storage::exists($quote->fee_transfer_proof)) {
                Storage::delete($quote->fee_transfer_proof);
            }
            $path = $request->file('fee_transfer_proof')->store('fee-transfer-proofs', 'public');
            $quote->fee_transfer_proof = $path;
        }

        $quote->save();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Status pencairan Management Fee berhasil diperbarui.',
                'status'  => $quote->fee_payment_status,
            ]);
        }

        return redirect()->back()->with('success', 'Status pencairan Management Fee berhasil diperbarui.');
    }
}
