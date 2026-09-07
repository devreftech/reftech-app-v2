<?php

namespace App\Http\Controllers;

use App\Models\PendingPO;
use App\Models\ProjectExpense;
use App\Models\Quotation;
use App\Models\UnitQuotation;
use App\Models\DetailQuotation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProjectProfitabilityController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfYear()->toDateString());
        $endDate = $request->get('end_date', Carbon::now()->toDateString());
        $healthFilter = $request->get('health'); // all, healthy, moderate, critical

        $query = PendingPO::with([
            'quote.pic.client',
            'quote.sales',
            'quote.detail_quotation',
            'unitQuotation.client',
            'unitQuotation.sales',
            'projectExpenses'
        ])
        ->whereBetween('date', [$startDate, $endDate]);

        $projects = $query->orderByDesc('date')->get()->map(function ($project) {
            $revenue = (float) $project->revenue;
            $hpp = (float) $project->total_hpp;
            $expenses = (float) $project->total_expenses;
            $totalCost = $hpp + $expenses;
            $netProfit = $revenue - $totalCost;
            $margin = $revenue > 0 ? round(($netProfit / $revenue) * 100, 1) : 0;

            if ($margin >= 25) {
                $health = 'healthy';
                $healthBadge = 'bg-label-success';
                $healthLabel = 'Sehat (≥25%)';
            } elseif ($margin >= 10) {
                $health = 'moderate';
                $healthBadge = 'bg-label-warning';
                $healthLabel = 'Moderat (10-24%)';
            } else {
                $health = 'critical';
                $healthBadge = 'bg-label-danger';
                $healthLabel = 'Kritis / Rendah (<10%)';
            }

            $clientName = '-';
            $salesName = '-';
            if ($project->unitQuotation) {
                $clientName = $project->unitQuotation->client?->company ?? '-';
                $salesName = $project->unitQuotation->sales?->name ?? '-';
            } elseif ($project->quote) {
                $clientName = $project->quote->pic?->client?->company ?? '-';
                $salesName = $project->quote->sales?->name ?? '-';
            }

            return (object) [
                'id' => $project->id,
                'no_pending' => $project->no_pending,
                'title' => $project->title ?: ($project->quote?->title ?: 'Project #' . $project->id),
                'date' => $project->date,
                'client_name' => $clientName,
                'sales_name' => $salesName,
                'revenue' => $revenue,
                'hpp' => $hpp,
                'expenses' => $expenses,
                'total_cost' => $totalCost,
                'net_profit' => $netProfit,
                'margin' => $margin,
                'health' => $health,
                'health_badge' => $healthBadge,
                'health_label' => $healthLabel,
                'expense_count' => $project->projectExpenses->count(),
            ];
        });

        if ($healthFilter && in_array($healthFilter, ['healthy', 'moderate', 'critical'])) {
            $projects = $projects->where('health', $healthFilter)->values();
        }

        $totalRevenue = $projects->sum('revenue');
        $totalCost = $projects->sum('total_cost');
        $totalHpp = $projects->sum('hpp');
        $totalExpenses = $projects->sum('expenses');
        $totalNetProfit = $totalRevenue - $totalCost;
        $overallMargin = $totalRevenue > 0 ? round(($totalNetProfit / $totalRevenue) * 100, 1) : 0;

        $healthyCount = $projects->where('health', 'healthy')->count();
        $moderateCount = $projects->where('health', 'moderate')->count();
        $criticalCount = $projects->where('health', 'critical')->count();

        return view('pages.report.project-profitability', compact(
            'projects',
            'startDate',
            'endDate',
            'healthFilter',
            'totalRevenue',
            'totalCost',
            'totalHpp',
            'totalExpenses',
            'totalNetProfit',
            'overallMargin',
            'healthyCount',
            'moderateCount',
            'criticalCount'
        ));
    }
}
