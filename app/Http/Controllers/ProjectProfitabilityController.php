<?php

namespace App\Http\Controllers;

use App\Models\Client;
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
        $selectedClientId = $request->get('client_id');
        $selectedStatus = $request->get('status');

        $clients = Client::orderBy('company')->get();

        $query = PendingPO::with([
            'quote.pic.client',
            'quote.sales',
            'quote.detail_quotation',
            'unitQuotation.client',
            'unitQuotation.sales',
            'projectExpenses'
        ])
        ->whereBetween('date', [$startDate, $endDate]);

        if ($selectedClientId) {
            $query->where(function ($q) use ($selectedClientId) {
                $q->whereHas('quote.pic', function ($q2) use ($selectedClientId) {
                    $q2->where('id_client', $selectedClientId);
                })->orWhereHas('unitQuotation', function ($q3) use ($selectedClientId) {
                    $q3->where('id_client', $selectedClientId);
                });
            });
        }

        if ($selectedStatus !== null && $selectedStatus !== '') {
            $query->where('status', $selectedStatus);
        }

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

            return [
                'id' => $project->id,
                'po_number' => $project->no_pending ?: ('PO #' . $project->id),
                'no_pending' => $project->no_pending ?: ('PO #' . $project->id),
                'project_name' => $project->title ?: ($project->quote?->title ?: ($project->unitQuotation?->title ?: 'Project #' . $project->id)),
                'title' => $project->title ?: ($project->quote?->title ?: ($project->unitQuotation?->title ?: 'Project #' . $project->id)),
                'po_date' => $project->date,
                'date' => $project->date,
                'client_name' => $clientName,
                'sales_name' => $salesName,
                'revenue' => $revenue,
                'hpp' => $hpp,
                'expenses' => $expenses,
                'total_cost' => $totalCost,
                'net_profit' => $netProfit,
                'margin' => $margin,
                'margin_percent' => $margin,
                'health' => $health,
                'health_status' => $health,
                'health_badge' => $healthBadge,
                'health_label' => $healthLabel,
                'expense_count' => $project->projectExpenses->count(),
            ];
        });

        if ($healthFilter && in_array($healthFilter, ['healthy', 'moderate', 'critical'])) {
            $projects = $projects->where('health_status', $healthFilter)->values();
        }

        $totalRevenue = $projects->sum('revenue');
        $totalCost = $projects->sum('total_cost');
        $totalHpp = $projects->sum('hpp');
        $totalExpenses = $projects->sum('expenses');
        $totalNetProfit = $totalRevenue - $totalCost;
        $overallMargin = $totalRevenue > 0 ? round(($totalNetProfit / $totalRevenue) * 100, 1) : 0;

        $healthyCount = $projects->where('health_status', 'healthy')->count();
        $moderateCount = $projects->where('health_status', 'moderate')->count();
        $criticalCount = $projects->where('health_status', 'critical')->count();

        return view('pages.report.project-profitability', compact(
            'projects',
            'clients',
            'selectedClientId',
            'selectedStatus',
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
