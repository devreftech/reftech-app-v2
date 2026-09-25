<?php

namespace App\Services\Dashboard;

use App\Models\PendingPO;
use App\Models\PurchaseOrder;
use App\Models\PurchaseRequest;
use App\Models\Suo;
use App\Models\ProductIn;
use App\Models\ProductOut;
use App\Models\DetailProduct;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class LogisticDashboardService
{
    /**
     * Get dashboard data payload for Logistic role
     */
    public function getDashboardData($notulens)
    {
        $logWidgets = $this->getLogisticDashboardData();

        return array_merge(compact('notulens'), $logWidgets);
    }

    public function getLogisticDashboardData(): array
    {
        $lowStockThreshold = 5;

        // KPI cards
        $logSoBaruCount = PendingPO::where('status', 0)->where('type', 'Non Project')->count();
        $logPrPendingCount = PurchaseRequest::where('status', '0')
            ->whereNull('rejected_at')
            ->whereHas('pending')
            ->whereHas('activeDetails')
            ->count();
        $logSuoPendingCount = Suo::where('status', 'submitted')->count();
        // PO yang udah "On Delivery" (semua alokasinya ada info pengiriman) tapi GR-nya
        // belum diverifikasi — gantiin flag ProductIn.accept yang udah gak relevan sejak
        // alur GR baru: ProductIn sekarang dibuat OTOMATIS pas GR diverifikasi
        // (lihat PurchaseController::storeGoodsReceipt()), jadi begitu ada baris ProductIn
        // artinya barang itu udah pasti diterima, gak butuh langkah "Accept" manual lagi.
        $logIncomingPendingCount = $this->onDeliveryPendingReceiptQuery()->count();
        $logLowStockCount = DetailProduct::whereRaw('(stock + warehouse_stock) > 0 AND (stock + warehouse_stock) < ?', [$lowStockThreshold])->count();

        // Status Sales Order (Non Project) breakdown
        $logSoNewCount = $logSoBaruCount;
        $logSoListCount = PendingPO::whereIn('pending_po.status', [1, 2, 3, 4])->where('type', 'Non Project')->count();
        $logSoDeliveryCount = PendingPO::where('pending_po.status', 5)->where('type', 'Non Project')->count();
        $logSoDoneCount = PendingPO::where('pending_po.status', 6)->where('type', 'Non Project')->count();
        $logSoStatusSeries = [$logSoNewCount, $logSoListCount, $logSoDeliveryCount, $logSoDoneCount];

        // Status Purchase Order (Supplier) breakdown
        $logPoReceivedCount = PurchaseOrder::where('receipt_status', 'Received')->count();
        $logPoOnDeliveryCount = PurchaseOrder::where(function ($q) {
                $q->whereNull('receipt_status')->orWhere('receipt_status', '!=', 'Received');
            })
            ->where(function ($q) {
                $q->whereNotNull('on_delivery_at')
                  ->orWhere(function ($sq) {
                      $sq->whereHas('prAllocations')
                         ->whereDoesntHave('prAllocations', fn ($aq) => $aq->whereNull('purchase_type'));
                  });
            })->count();
        $logPoPendingCount = PurchaseOrder::where(function ($q) {
                $q->whereNull('receipt_status')->orWhere('receipt_status', '!=', 'Received');
            })
            ->where(function ($q) {
                $q->whereNull('on_delivery_at')
                  ->where(function ($sq) {
                      $sq->doesntHave('prAllocations')
                         ->orWhereHas('prAllocations', fn ($aq) => $aq->whereNull('purchase_type'));
                  });
            })->count();
        $logPoStatusSeries = [$logPoPendingCount, $logPoOnDeliveryCount, $logPoReceivedCount];

        // PR otomatis dari Sales Order (stok tidak cukup) yang valid & belum di-approve (status = 0)
        $logPrFromSo = PurchaseRequest::whereNotNull('id_pending')
            ->where('status', '0')
            ->whereNull('rejected_at')
            ->whereHas('pending')
            ->whereHas('activeDetails')
            ->with(['pending.quote.pic.client', 'user', 'activeDetails.equivalent.product'])
            ->orderByDesc('date')
            ->take(6)
            ->get();

        // Incoming Goods - Pending Receipt
        $logIncomingPending = $this->onDeliveryPendingReceiptQuery()
            ->with(['supplier', 'details.product', 'details.unit', 'prAllocations.detail.equivalent.product'])
            ->orderByDesc('date')
            ->take(6)
            ->get();

        // Stok Hampir Habis (masih ada stok, tapi di bawah ambang batas)
        $logLowStock = DetailProduct::selectRaw('detail_product.*, (stock + warehouse_stock) as total_stock')
            ->with('product')
            ->havingRaw('total_stock > 0 AND total_stock < ?', [$lowStockThreshold])
            ->orderBy('total_stock')
            ->take(6)
            ->get();

        // Penerimaan Barang 7 hari terakhir
        $logReceivingLabels = [];
        $logReceivingSeries = [];
        for ($i = 6; $i >= 0; $i--) {
            $day = Carbon::now()->subDays($i);
            $logReceivingLabels[] = $day->format('d/m');
            // Setiap baris ProductIn = barang yang udah diterima (dibuat otomatis pas GR
            // diverifikasi) — pakai kolom `date` (tanggal GR aktual), bukan `updated_at`,
            // biar gak ikut kegeser kalau record-nya diedit belakangan (mis. konfirmasi invoice).
            $logReceivingSeries[] = ProductIn::whereDate('date', $day->toDateString())->count();
        }

        // Aktivitas terbaru: gabungan PR dibuat, barang diterima, barang dikirim
        $logRecentPr = PurchaseRequest::orderByDesc('created_at')
            ->withCount('details')
            ->limit(5)
            ->get(['id', 'no_pr as ref', 'created_at as tanggal'])
            ->map(function ($pr) {
                $pr->ket = $pr->details_count . ' item';
                $pr->tipe = 'PR Dibuat';
                return $pr;
            });
        $logRecentIncoming = ProductIn::orderByDesc('date')
            ->limit(5)
            ->get(['no_do as ref', 'date as tanggal', 'supplier as ket', DB::raw("'Barang Diterima' as tipe")]);
        $logRecentOutgoing = ProductOut::orderByDesc('created_at')
            ->limit(5)
            ->get([DB::raw("CONCAT('DO-', id) as ref"), 'created_at as tanggal', 'detail_client as ket', DB::raw("'Barang Dikirim' as tipe")]);
        $logRecentActivity = $logRecentPr
            ->concat($logRecentIncoming)
            ->concat($logRecentOutgoing)
            ->sortByDesc('tanggal')
            ->take(8)
            ->values();

        return compact(
            'logSoBaruCount',
            'logPrPendingCount',
            'logSuoPendingCount',
            'logIncomingPendingCount',
            'logLowStockCount',
            'logSoNewCount',
            'logSoListCount',
            'logSoDeliveryCount',
            'logSoDoneCount',
            'logSoStatusSeries',
            'logPoPendingCount',
            'logPoOnDeliveryCount',
            'logPoReceivedCount',
            'logPoStatusSeries',
            'logPrFromSo',
            'logIncomingPending',
            'logLowStock',
            'logReceivingLabels',
            'logReceivingSeries',
            'logRecentActivity',
        );
    }

    // PO yang statusnya "Sedang Dikirim" (semua item alokasinya udah keisi info
    // pengiriman lewat "On Delivery"/"Info Pengiriman") tapi belum "Diterima" (GR-nya
    // belum diverifikasi lewat PurchaseController::storeGoodsReceipt()). Ini pengganti
    // langsung dari query lama `ProductIn::where('accept', '0')` — sebelum ada alur GR,
    // ProductIn dibuat manual duluan lalu di-"Accept" belakangan; sekarang ProductIn
    // BARU ada setelah GR diverifikasi, jadi titik "pending"-nya pindah ke level PO.
    private function onDeliveryPendingReceiptQuery()
    {
        return PurchaseOrder::where('receipt_status', '!=', 'Received')
            ->whereHas('prAllocations')
            ->whereDoesntHave('prAllocations', fn ($q) => $q->whereNull('purchase_type'));
    }
}
