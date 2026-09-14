<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use App\Models\Marketplace;
use App\Models\MarketplaceSettlement;
use App\Models\MarketplaceSettlementItem;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MarketplaceController extends Controller
{
    /**
     * Marketplace Management module — one page, three tabs:
     *  - master        : marketplace channel master data (name, entity, default bank, fee %)
     *  - settlement    : record & review pencairan (settlement) batches from a marketplace to a bank
     *  - reconciliation: held vs disbursed escrow balance per marketplace / entity
     */
    public function index(Request $request)
    {
        $activeTab = $request->get('tab', 'master');

        $marketplaces = Marketplace::withCount([
            'payments as held_count' => fn ($q) => $q->where('disbursement_status', 'held'),
        ])->orderBy('name')->get();

        $marketplaces->each(function ($m) {
            $m->held_amount = (float) $m->payments()->where('disbursement_status', 'held')->sum('amount');
        });

        $banks = Bank::where('is_active', 1)->orderBy('bank')->get();

        // High-level KPI summaries
        $totalHeldAmount = (float) $marketplaces->sum('held_amount');
        $totalHeldCount = (int) $marketplaces->sum('held_count');
        $totalSettledNet = (float) MarketplaceSettlement::sum('net_amount');
        $totalSettledGross = (float) MarketplaceSettlement::sum('gross_amount');
        $totalFees = (float) MarketplaceSettlement::sum('fee_amount');
        $totalSettlementsCount = (int) MarketplaceSettlement::count();
        $activeMarketplaceCount = (int) $marketplaces->where('is_active', true)->count();

        // Settlements data (always loaded for instant client-side tab switching)
        $settlements = MarketplaceSettlement::with(['marketplace', 'bank', 'creator', 'items.payment'])
            ->orderByDesc('settlement_date')
            ->orderByDesc('id')
            ->limit(100)
            ->get();

        // Reconciliation data (always loaded for instant client-side tab switching)
        $reconciliation = Marketplace::query()
            ->orderBy('entity')
            ->orderBy('name')
            ->get()
            ->map(function ($m) {
                $held = (float) Payment::where('id_marketplace', $m->id)->where('disbursement_status', 'held')->sum('amount');
                $disbursed = (float) Payment::where('id_marketplace', $m->id)->where('disbursement_status', 'disbursed')->sum('amount');
                $settledNet = (float) MarketplaceSettlement::where('id_marketplace', $m->id)->sum('net_amount');
                $totalEscrow = $held + $disbursed;
                $disbursedPercent = $totalEscrow > 0 ? round(($disbursed / $totalEscrow) * 100, 1) : 0;
                return (object) [
                    'marketplace' => $m,
                    'held' => $held,
                    'disbursed' => $disbursed,
                    'total_escrow' => $totalEscrow,
                    'disbursed_percent' => $disbursedPercent,
                    'settled_net' => $settledNet,
                ];
            });

        // Legacy escrow payments predating the marketplace field
        $legacyUnmatched = Payment::where('method', 'Escrow')
            ->whereNull('id_marketplace')
            ->with(['quotation.pic.client', 'unitQuotation.client'])
            ->orderByDesc('date')
            ->limit(200)
            ->get()
            ->map(function ($p) {
                $client = $p->unitQuotation?->client?->company
                    ?? ($p->quotation?->pic?->client?->company ?? '-');
                $ref = $p->unitQuotation?->no_quote ?? ($p->quotation?->no_quote ?? '#' . $p->id);
                return (object) [
                    'id' => $p->id,
                    'date' => $p->date,
                    'ref' => $ref,
                    'client' => $client,
                    'amount' => (float) $p->amount,
                    'escrow_channel' => $p->escrow_channel,
                ];
            });

        $legacyUnmatchedCount = Payment::where('method', 'Escrow')->whereNull('id_marketplace')->count();

        return view('pages.finance.marketplace.index', compact(
            'activeTab',
            'marketplaces',
            'banks',
            'settlements',
            'reconciliation',
            'legacyUnmatched',
            'legacyUnmatchedCount',
            'totalHeldAmount',
            'totalHeldCount',
            'totalSettledNet',
            'totalSettledGross',
            'totalFees',
            'totalSettlementsCount',
            'activeMarketplaceCount'
        ));
    }

    /**
     * Create a new marketplace channel (master data).
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:150|unique:marketplaces,name',
            'entity' => 'nullable|string|max:100',
            'id_default_bank' => 'nullable|exists:bank,id',
            'fee_percent' => 'nullable|numeric|min:0|max:100',
        ], [
            'name.unique' => 'Nama marketplace ini sudah terdaftar.',
        ]);

        Marketplace::create([
            'name' => $request->name,
            'entity' => $request->entity,
            'id_default_bank' => $request->id_default_bank ?: null,
            'fee_percent' => $request->fee_percent ?: null,
            'is_active' => 1,
        ]);

        return redirect()->route('finance.marketplace.index', ['tab' => 'master'])
            ->with('success', 'Marketplace berhasil ditambahkan.');
    }

    /**
     * Update marketplace master data.
     */
    public function update(Request $request, $id)
    {
        $marketplace = Marketplace::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:150|unique:marketplaces,name,' . $marketplace->id,
            'entity' => 'nullable|string|max:100',
            'id_default_bank' => 'nullable|exists:bank,id',
            'fee_percent' => 'nullable|numeric|min:0|max:100',
        ], [
            'name.unique' => 'Nama marketplace ini sudah terdaftar.',
        ]);

        $marketplace->update([
            'name' => $request->name,
            'entity' => $request->entity,
            'id_default_bank' => $request->id_default_bank ?: null,
            'fee_percent' => $request->fee_percent ?: null,
        ]);

        return redirect()->route('finance.marketplace.index', ['tab' => 'master'])
            ->with('success', 'Marketplace berhasil diperbarui.');
    }

    /**
     * Toggle marketplace active/inactive — never delete, so history stays intact.
     */
    public function toggleActive($id)
    {
        $marketplace = Marketplace::findOrFail($id);
        $marketplace->is_active = !$marketplace->is_active;
        $marketplace->save();

        return redirect()->route('finance.marketplace.index', ['tab' => 'master'])
            ->with('success', 'Status marketplace berhasil diperbarui.');
    }

    /**
     * AJAX: list held (not-yet-disbursed) escrow payments for a marketplace,
     * used by the settlement form to pick which payments a pencairan batch covers.
     */
    public function heldPayments($id)
    {
        $payments = Payment::where('id_marketplace', $id)
            ->where('disbursement_status', 'held')
            ->with(['quotation.pic.client', 'unitQuotation.client'])
            ->orderBy('date')
            ->get()
            ->map(function ($p) {
                $client = $p->unitQuotation?->client?->company
                    ?? ($p->quotation?->pic?->client?->company ?? '-');
                $ref = $p->unitQuotation?->no_quote ?? ($p->quotation?->no_quote ?? '#' . $p->id);
                return [
                    'id' => $p->id,
                    'date' => $p->date ? Carbon::parse($p->date)->format('d/m/Y') : '-',
                    'ref' => $ref,
                    'client' => $client,
                    'amount' => (float) $p->amount,
                ];
            });

        return response()->json(['data' => $payments]);
    }

    /**
     * Record a pencairan (settlement) batch: marketplace paid out gross - fee = net
     * into a bank account, covering one or more held escrow payments.
     */
    public function storeSettlement(Request $request)
    {
        $request->validate([
            'id_marketplace' => 'required|exists:marketplaces,id',
            'id_bank' => 'required|exists:bank,id',
            'settlement_date' => 'required|date',
            'reference_no' => 'nullable|string|max:150',
            'gross_amount' => 'required|numeric|min:0',
            'fee_amount' => 'nullable|numeric|min:0',
            'net_amount' => 'required|numeric|min:0',
            'note' => 'nullable|string|max:500',
            'payment_ids' => 'required|array|min:1',
            'payment_ids.*' => 'exists:payment,id',
            'proof_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ], [
            'payment_ids.required' => 'Pilih minimal satu payment escrow yang dicairkan pada batch ini.',
        ]);

        $marketplaceId = (int) $request->id_marketplace;

        // Only allow attaching payments that actually belong to this marketplace
        // and are still held — protects against a stale form / double-settlement.
        $payments = Payment::where('id_marketplace', $marketplaceId)
            ->where('disbursement_status', 'held')
            ->whereIn('id', $request->payment_ids)
            ->get();

        if ($payments->isEmpty()) {
            return redirect()->back()->with('error', 'Payment yang dipilih tidak valid atau sudah tercairkan sebelumnya.');
        }

        $proofPath = null;
        if ($request->hasFile('proof_file')) {
            $file = $request->file('proof_file');
            $filename = 'mkt_settle_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $proofPath = $file->storeAs('marketplace_settlements', $filename, 'public');
        }

        $prefix = 'MKT-' . Carbon::parse($request->settlement_date)->format('Ym') . '-';
        $lastSettlement = MarketplaceSettlement::where('settlement_number', 'like', $prefix . '%')->latest('id')->first();
        $seq = $lastSettlement ? ((int) substr($lastSettlement->settlement_number, -4)) + 1 : 1;
        $settlementNumber = $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);

        DB::transaction(function () use ($request, $payments, $proofPath, $settlementNumber) {
            $settlement = MarketplaceSettlement::create([
                'settlement_number' => $settlementNumber,
                'id_marketplace' => $request->id_marketplace,
                'id_bank' => $request->id_bank,
                'settlement_date' => $request->settlement_date,
                'reference_no' => $request->reference_no,
                'gross_amount' => (float) $request->gross_amount,
                'fee_amount' => (float) ($request->fee_amount ?: 0),
                'net_amount' => (float) $request->net_amount,
                'proof_file' => $proofPath,
                'status' => 'reconciled',
                'note' => $request->note,
                'created_by' => Auth::id(),
            ]);

            foreach ($payments as $payment) {
                MarketplaceSettlementItem::create([
                    'id_marketplace_settlement' => $settlement->id,
                    'id_payment' => $payment->id,
                    'amount' => (float) $payment->amount,
                ]);

                $payment->disbursement_status = 'disbursed';
                $payment->save();
            }

            $bank = Bank::findOrFail($request->id_bank);
            $bank->increment('saldo', (float) $request->net_amount);
        });

        return redirect()->route('finance.marketplace.index', ['tab' => 'settlement'])
            ->with('success', "Pencairan {$settlementNumber} berhasil dicatat.");
    }

    /**
     * Tag a legacy escrow payment (predates the marketplace field) with a
     * marketplace, for finance to fill in manually when they want to.
     */
    public function assignPayment(Request $request, $paymentId)
    {
        $request->validate([
            'id_marketplace' => 'required|exists:marketplaces,id',
        ]);

        $payment = Payment::where('method', 'Escrow')->findOrFail($paymentId);
        $payment->id_marketplace = $request->id_marketplace;
        $payment->save();

        return redirect()->back()->with('success', 'Payment berhasil ditandai ke marketplace.');
    }
}
