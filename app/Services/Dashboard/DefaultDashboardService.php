<?php

namespace App\Services\Dashboard;

use App\Models\Product;
use App\Models\DetailProduct;
use App\Models\SerialProduct;
use App\Models\User;
use App\Models\MonitoringActivities;
use App\Models\PendingPO;
use App\Models\ReqVisit;
use Carbon\Carbon;

class DefaultDashboardService
{
    /**
     * Get dashboard data payload for fallback default role
     */
    public function getDashboardData($notulens)
    {
        $today = Carbon::now()->toDateString();
        $commodity = Product::count();
        $dproduct = DetailProduct::count();
        $sproduct = SerialProduct::count();
        $user = User::find('25');
        $monitoring = MonitoringActivities::whereDate('date', $today)->first();

        $newCount = PendingPO::where('status', 0)
            ->where('type', 'Non Project')
            ->count();
        $listCount = PendingPO::whereIn('pending_po.status', [1, 2, 3, 4])
            ->where('type', 'Non Project')
            ->count();
        $deliveryCount = PendingPO::where('pending_po.status', 5)
            ->where('type', 'Non Project')
            ->count();
        $doneCount = PendingPO::where('pending_po.status', 6)
            ->where('type', 'Non Project')
            ->count();

        $visits = ReqVisit::whereNull('date')->get();
        $visited = ReqVisit::whereNotNull('date')->whereNull('visit_date')->get();

        return compact(
            'user',
            'newCount',
            'listCount',
            'deliveryCount',
            'notulens',
            'commodity',
            'dproduct',
            'sproduct',
            'visits',
            'visited'
        );
    }
}
