<?php

namespace App\Http\Controllers;

use App\Models\DetailProductOut;
use App\Models\DetailPurchaseOrder;
use App\Models\ProductOut;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class IntercompanyReportController extends Controller
{
    /**
     * Display the monthly Kojisha to Reftech intercompany outgoing report and draft PO builder.
     */
    public function index(Request $request)
    {
        $selectedMonth = (int) $request->get('month', now()->month);
        $selectedYear = (int) $request->get('year', now()->year);
        $selectedStatus = $request->get('status', 'pending'); // 'pending', 'done', 'all'

        // Base Query
        $baseQuery = DetailProductOut::query()
            ->select('detail_product_out.*')
            ->join('product_out', 'detail_product_out.id_product_out', '=', 'product_out.id')
            ->where(function ($q) {
                $q->where('product_out.flag', 'Kojisha')
                    ->orWhere('product_out.no_product_out', 'like', '%BK-KII%')
                    ->orWhere('product_out.invoice', 'like', '%/KII/%')
                    ->orWhere('product_out.po', 'like', '%KII%');
            })
            ->whereYear('product_out.date', $selectedYear)
            ->whereMonth('product_out.date', $selectedMonth);

        if ($request->filled('search')) {
            $search = trim($request->search);
            $baseQuery->where(function ($q) use ($search) {
                $q->where('product_out.no_product_out', 'like', "%{$search}%")
                    ->orWhere('product_out.detail_client', 'like', "%{$search}%")
                    ->orWhere('product_out.invoice', 'like', "%{$search}%")
                    ->orWhere('product_out.po', 'like', "%{$search}%");
            });
        }

        $items = $baseQuery->with([
                'productOut',
                'detailProduct.product',
                'serialProduct',
                'purchaseOrder',
            ])
            ->orderBy('product_out.date', 'asc')
            ->orderBy('detail_product_out.id', 'asc')
            ->get();

        // Counts for tabs calculated from all loaded items
        $pendingCount = $items->whereNull('id_purchase_order')->count();
        $doneCount = $items->whereNotNull('id_purchase_order')->count();
        $allCount = $items->count();

        // Calculate KPI summaries
        $totalTransactions = $items->pluck('id_product_out')->unique()->count();
        $totalQty = (int) $items->sum('qty');
        $totalEstimatedHpp = (float) $items->sum(function ($item) {
            $hpp = (float) ($item->detailProduct->modal ?? ($item->detailProduct->hpp ?? 0));
            return $hpp * (int) ($item->qty ?? 1);
        });

        // Query released intercompany POs (Kojisha -> Reftech)
        $poQuery = PurchaseOrder::query()
            ->where(function ($q) {
                $q->where('no_po', 'like', '%KII%')
                    ->orWhere('no_po', 'like', '%KOJISHA%')
                    ->orWhere('note', 'like', '%Kojisha%')
                    ->orWhereHas('supplier', function ($sq) {
                        $sq->where('category', 'Intercompany')
                           ->orWhere('supplier', 'like', '%Reftech%');
                    });
            })
            ->whereYear('date', $selectedYear)
            ->whereMonth('date', $selectedMonth);

        if ($request->filled('search')) {
            $search = trim($request->search);
            $poQuery->where(function ($q) use ($search) {
                $q->where('no_po', 'like', "%{$search}%")
                    ->orWhere('note', 'like', "%{$search}%")
                    ->orWhere('company', 'like', "%{$search}%");
            });
        }

        $releasedPos = $poQuery->with(['detailPurchaseOrder', 'supplier'])
            ->withCount('detailPurchaseOrder')
            ->orderBy('date', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        $poReleaseCount = $releasedPos->count();

        // Available years for filter dropdown
        $availableYears = DB::table('product_out')
            ->whereNotNull('date')
            ->selectRaw('DISTINCT YEAR(date) as yr')
            ->pluck('yr')
            ->merge([(int) date('Y')])
            ->unique()
            ->sortDesc()
            ->values();

        $monthNames = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        $currentCarbon = Carbon::createFromDate($selectedYear, $selectedMonth, 1);
        $prevCarbon = (clone $currentCarbon)->subMonth();
        $nextCarbon = (clone $currentCarbon)->addMonth();

        $prevMonth = $prevCarbon->month;
        $prevYear = $prevCarbon->year;
        $nextMonth = $nextCarbon->month;
        $nextYear = $nextCarbon->year;

        return view('pages.warehouse.intercompany.index', compact(
            'items',
            'releasedPos',
            'selectedMonth',
            'selectedYear',
            'selectedStatus',
            'prevMonth',
            'prevYear',
            'nextMonth',
            'nextYear',
            'pendingCount',
            'doneCount',
            'allCount',
            'poReleaseCount',
            'totalTransactions',
            'totalQty',
            'totalEstimatedHpp',
            'availableYears',
            'monthNames'
        ));
    }

    /**
     * Store draft PO from Kojisha to Reftech.
     */
    public function storeDraftPo(Request $request)
    {
        $request->validate([
            'selected_items' => 'required|array|min:1',
            'unit_price' => 'required|array',
            'selected_items.*' => 'required|integer',
        ]);

        $selectedIds = $request->input('selected_items', []);
        $prices = $request->input('unit_price', []);

        $items = DetailProductOut::whereIn('id', $selectedIds)
            ->with(['productOut', 'detailProduct.product', 'serialProduct'])
            ->get();

        if ($items->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada item yang dipilih untuk membuat Draft PO.');
        }

        DB::beginTransaction();
        try {
            // Find or create Reftech supplier
            $reftechSupplier = Supplier::where('supplier', 'like', '%Reftech%')->first();
            if (!$reftechSupplier) {
                $reftechSupplier = Supplier::create([
                    'supplier' => 'PT. REFTECH JAYA OPTIMA',
                    'address' => 'Bandung, Jawa Barat',
                    'contact' => 'Reftech Central Logistics',
                    'email' => 'sales@reftech.id',
                    'category' => 'Intercompany',
                ]);
            }

            // Generate PO number for Kojisha -> Reftech
            $year = date('Y');
            $romanMonths = ['I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];
            $roman = $romanMonths[(int) date('n') - 1];
            $prefix = "PO-KII/RJO/{$roman}/{$year}";
            
            $lastPo = PurchaseOrder::where('no_po', 'like', "%{$prefix}")->orderByDesc('id')->first();
            $seq = 1;
            if ($lastPo && preg_match('/^(\d+)/', $lastPo->no_po, $m)) {
                $seq = ((int) $m[1]) + 1;
            }
            $noPo = str_pad($seq, 3, '0', STR_PAD_LEFT) . "/{$prefix}";

            $total = 0;
            $poDetails = [];

            foreach ($items as $item) {
                $rawPrice = $prices[$item->id] ?? null;
                if ($rawPrice !== null) {
                    $cleaned = preg_replace('/[^0-9]/', '', (string)$rawPrice);
                    $unitPrice = (float) $cleaned;
                } else {
                    $unitPrice = (float) ($item->detailProduct->modal ?? ($item->detailProduct->hpp ?? 0));
                }

                $qty = (int) ($item->qty ?? 1);
                $subtotal = $unitPrice * $qty;
                $total += $subtotal;

                $brand = $item->serialProduct?->brand ?? ($item->detailProduct?->product?->brand ?? '');
                $pn = $item->serialProduct?->pn ?? ($item->detailProduct?->product?->part_number ?? ($item->detailProduct?->replacement ?? '-'));
                $desc = $item->detailProduct?->product?->description ?? '';
                $productName = trim(($brand ? $brand . ' - ' : '') . $pn . ($desc ? ' (' . substr($desc, 0, 80) . ')' : ''));

                $poDetails[] = [
                    'id_product' => $item->detailProduct?->id_product,
                    'id_detail_product' => $item->id_detail_product,
                    'product_name' => $productName ?: 'Spare Part',
                    'qty' => $qty,
                    'price' => $unitPrice,
                    'amount' => $subtotal,
                ];
            }

            $isPpn = $request->boolean('is_ppn') || $request->input('is_ppn') === '1' || $request->input('ppn_mode') === 'ppn';
            $ppnRate = (float) $request->input('ppn_rate', 11);
            $vat = $isPpn ? round(($total * $ppnRate) / 100) : 0;
            $grandTotal = $total + $vat;

            $customNote = trim($request->input('note', ''));
            $defaultNote = "PO Intercompany penggantian stok Spare Part penjualan Kojisha periode " . Carbon::now()->isoFormat('MMMM YYYY') . " (" . count($poDetails) . " items)" . ($isPpn ? " [PPN {$ppnRate}%]" : " [Non-PPN]");

            $po = new PurchaseOrder();
            $po->no_po = $noPo;
            $po->id_supplier = $reftechSupplier->id;
            $po->category = 'Sparepart';
            $po->date = now();
            $po->company = 'PT. REFTECH JAYA OPTIMA';
            $po->attn = 'Central Logistics & Sales Reftech';
            $po->address = 'Taman Kopo Indah V, Soho Sommerville No. 31, Bandung – Jawa Barat 40218';
            $po->phone = '022 54417653';
            $po->email = 'sales@reftech.id';
            $po->payment = 'Intercompany Settlement';
            $po->delivery = 'Direct Customer Delivery / Warehouse Takeout';
            $po->note = $customNote ?: $defaultNote;
            $po->subtotal = $total;
            $po->vat = $vat;
            $po->total = $grandTotal;
            $po->save();

            foreach ($poDetails as $detail) {
                $dPo = new DetailPurchaseOrder();
                $dPo->id_purchase_order = $po->id;
                $dPo->id_product = $detail['id_product'];
                $dPo->product = $detail['product_name'];
                $dPo->category = 'Sparepart';
                $dPo->info_qty = 'Pcs';
                $dPo->qty = $detail['qty'];
                $dPo->price = $detail['price'];
                $dPo->amount = $detail['amount'];
                $dPo->disc = 0;
                $dPo->save();
            }

            // Link the selected detail_product_out items to this PO
            DetailProductOut::whereIn('id', $selectedIds)->update([
                'id_purchase_order' => $po->id
            ]);

            DB::commit();

            return redirect()->route('purchase.show', $po->id)
                ->with('message', "Draft PO Intercompany {$noPo} berhasil dibuat dengan total Rp " . number_format($grandTotal, 0, '', '.'));
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal membuat Draft PO Intercompany: ' . $e->getMessage());
        }
    }

    /**
     * Cancel an intercompany purchase order and return all associated items to pending pool.
     */
    public function cancelPo(Request $request, $id)
    {
        $po = PurchaseOrder::findOrFail($id);

        DB::beginTransaction();
        try {
            $noPo = $po->no_po;

            // 1. Release all detail_product_out items
            DetailProductOut::where('id_purchase_order', $po->id)
                ->update(['id_purchase_order' => null]);

            // 2. Delete PO details
            DetailPurchaseOrder::where('id_purchase_order', $po->id)->delete();

            // 3. Delete the PO
            $po->delete();

            DB::commit();

            return redirect()->route('intercompany.index', ['status' => 'pending'])
                ->with('success', "Purchase Order Intercompany {$noPo} berhasil dibatalkan. Seluruh item barang keluar telah dikembalikan ke daftar belum dibuatkan PO.");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal membatalkan PO Intercompany: ' . $e->getMessage());
        }
    }

    /**
     * Printable view of monthly intercompany outgoing report.
     */
    public function print(Request $request)
    {
        $selectedMonth = (int) $request->get('month', now()->month);
        $selectedYear = (int) $request->get('year', now()->year);

        $items = DetailProductOut::query()
            ->select('detail_product_out.*')
            ->join('product_out', 'detail_product_out.id_product_out', '=', 'product_out.id')
            ->where(function ($q) {
                $q->where('product_out.flag', 'Kojisha')
                    ->orWhere('product_out.no_product_out', 'like', '%BK-KII%')
                    ->orWhere('product_out.invoice', 'like', '%/KII/%')
                    ->orWhere('product_out.po', 'like', '%KII%');
            })
            ->whereYear('product_out.date', $selectedYear)
            ->whereMonth('product_out.date', $selectedMonth)
            ->with([
                'productOut',
                'detailProduct.product',
                'serialProduct'
            ])
            ->orderBy('product_out.date', 'asc')
            ->get();

        $monthNames = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        return view('pages.warehouse.intercompany.print', compact(
            'items',
            'selectedMonth',
            'selectedYear',
            'monthNames'
        ));
    }
}
