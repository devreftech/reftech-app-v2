<?php

namespace App\Services\Dashboard;

use App\Models\PendingPO;
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
        $logPrPendingCount = PurchaseRequest::where('status', '0')->count();
        $logSuoPendingCount = Suo::where('status', 'submitted')->count();
        $logIncomingPendingCount = ProductIn::where('accept', '0')->count();
        $logLowStockCount = DetailProduct::whereRaw('(stock + warehouse_stock) > 0 AND (stock + warehouse_stock) < ?', [$lowStockThreshold])->count();

        // Status Sales Order (Non Project) breakdown
        $logSoNewCount = $logSoBaruCount;
        $logSoListCount = PendingPO::whereIn('pending_po.status', [1, 2, 3, 4])->where('type', 'Non Project')->count();
        $logSoDeliveryCount = PendingPO::where('pending_po.status', 5)->where('type', 'Non Project')->count();
        $logSoDoneCount = PendingPO::where('pending_po.status', 6)->where('type', 'Non Project')->count();
        $logSoStatusSeries = [$logSoNewCount, $logSoListCount, $logSoDeliveryCount, $logSoDoneCount];

        // PR otomatis dari Sales Order (stok tidak cukup)
        $logPrFromSo = PurchaseRequest::whereNotNull('id_pending')
            ->where('status', '0')
            ->with(['pending', 'equivalent.product'])
            ->orderByDesc('date')
            ->take(6)
            ->get();

        // Incoming Goods - Pending Receipt
        $logIncomingPending = ProductIn::where('accept', '0')
            ->with('supp')
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
            $logReceivingSeries[] = ProductIn::where('accept', '1')->whereDate('updated_at', $day->toDateString())->count();
        }

        // Aktivitas terbaru: gabungan PR dibuat, barang diterima, barang dikirim
        $logRecentPr = PurchaseRequest::orderByDesc('created_at')
            ->limit(5)
            ->get(['no_pr as ref', 'created_at as tanggal', 'qty as ket', DB::raw("'PR Dibuat' as tipe")]);
        $logRecentIncoming = ProductIn::where('accept', '1')
            ->orderByDesc('updated_at')
            ->limit(5)
            ->get(['no_do as ref', 'updated_at as tanggal', 'supplier as ket', DB::raw("'Barang Diterima' as tipe")]);
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
            'logPrFromSo',
            'logIncomingPending',
            'logLowStock',
            'logReceivingLabels',
            'logReceivingSeries',
            'logRecentActivity',
        );
    }
}
