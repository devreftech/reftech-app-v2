<?php

namespace App\Http\Controllers;

use App\Models\PendingPO;
use App\Models\PurchaseRequest;
use App\Models\ProjectExpense;
use App\Models\Quotation;
use App\Models\DetailQuotation;
use App\Models\SubtitleQuotation;
use App\Models\Expanse;
use App\Models\Invoice;
use App\Models\SerialProduct;
use App\Models\DetailPendingPO;
use App\Models\UnitQuotation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProjectMonitoringController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the projects.
     */
    public function index(Request $request)
    {
        $role = Auth::user()->role;
        $selectedYear = $request->get('year', date('Y'));

        // Query available years for filter dropdown
        $availableYears = PendingPO::where('type', 'Project')
            ->get()
            ->map(function ($project) {
                $date = $project->date ?? null;
                return $date ? Carbon::parse($date)->year : null;
            })
            ->filter()
            ->unique()
            ->sortDesc()
            ->values()
            ->all();

        $currentYear = intval(date('Y'));
        if (!in_array($currentYear, $availableYears)) {
            $availableYears[] = $currentYear;
            rsort($availableYears);
        }

        $projects = PendingPO::where('type', 'Project')
            ->with([
                'quote.pic.client',
                'quote.sales',
                'unitQuotation.client',
                'unitQuotation.sales',
            ])
            ->get();

        // Apply year filter unless 'all' is selected
        if ($selectedYear !== 'all') {
            $projects = $projects->filter(function ($project) use ($selectedYear) {
                $date = $project->date ?? null;
                return $date && Carbon::parse($date)->year == $selectedYear;
            });
        }

        // If user is sales, filter by their own sales records
        if ($role === 'Sales') {
            $projects = $projects->filter(function ($project) {
                $quoteSales = $project->quote?->id_sales;
                $unitSales = $project->unitQuotation?->id_sales;
                return ($quoteSales == Auth::id()) || ($unitSales == Auth::id());
            });
        }

        // Batch cost sums per project (sebelumnya 3 query per project di dalam map = N+1)
        $projectIds = $projects->pluck('id');
        $materialCostByProject = PurchaseRequest::whereIn('id_pending', $projectIds)
            ->where('status', '3')
            ->groupBy('id_pending')->selectRaw('id_pending, SUM(amount) as total')
            ->pluck('total', 'id_pending');
        $generalCostByProject = ProjectExpense::whereIn('id_pending', $projectIds)
            ->groupBy('id_pending')->selectRaw('id_pending, SUM(amount) as total')
            ->pluck('total', 'id_pending');
        $shippingCostByProject = Expanse::whereIn('id_pending', $projectIds)
            ->where('type', 'Resi')
            ->groupBy('id_pending')->selectRaw('id_pending, SUM(cost) as total')
            ->pluck('total', 'id_pending');

        // Calculate profitability metrics for each project
        $projects = $projects->map(function ($project) use ($materialCostByProject, $generalCostByProject, $shippingCostByProject) {
            $project->order_date = $project->date;
            $project->company = $project->unitQuotation?->client?->company
                ?? $project->quote?->pic?->client?->company
                ?? '-';
            $project->area = $project->unitQuotation?->client?->area
                ?? $project->quote?->pic?->client?->area
                ?? '-';
            $project->sales_name = $project->unitQuotation?->sales?->name
                ?? $project->quote?->sales?->name
                ?? '-';
            $project->sales_image = $project->unitQuotation?->sales?->image
                ?? $project->quote?->sales?->image
                ?? null;
            $project->revenue = $project->unitQuotation ? ($project->unitQuotation->total ?? 0) : ($project->quote?->nett ?? 0);
            $project->no_quote = $project->unitQuotation?->no_quote ?? $project->quote?->no_quote ?? '-';
            $project->no_po = $project->unitQuotation ? ($project->unitQuotation->po_number ?? '-') : ($project->quote?->invoice->first()?->no_po ?? '-');
            $project->detail_route = $project->id_unit_quotation
                ? route('unit-quotation.show', $project->id_unit_quotation)
                : route('project-monitoring.show', $project->id);
            $project->material_cost = (float) $materialCostByProject->get($project->id, 0);
            $project->general_cost = (float) $generalCostByProject->get($project->id, 0);
            $project->shipping_cost = (float) $shippingCostByProject->get($project->id, 0);

            $project->total_cost = $project->material_cost + $project->general_cost + $project->shipping_cost;
            $project->profit = $project->revenue - $project->total_cost;
            $project->margin = $project->revenue > 0 ? ($project->profit / $project->revenue) * 100 : 0;

            return $project;
        });

        // Group projects for visual dashboard tabs
        $newProjects = $projects->filter(fn($p) => $p->status == 0);
        $checkPartsProjects = $projects->filter(fn($p) => $p->status != 0 && $p->status != 6 && ($p->project_status_step ?? 1) == 1);
        $schedulingProjects = $projects->filter(fn($p) => $p->status != 0 && $p->status != 6 && ($p->project_status_step ?? 1) == 2);
        $inProgressProjects = $projects->filter(fn($p) => $p->status != 0 && $p->status != 6 && ($p->project_status_step ?? 1) >= 3);
        $completedProjects = $projects->filter(fn($p) => $p->status == 6);

        // Dashboard overall KPIs
        $totalProjects = $projects->count();
        $totalRevenue = $projects->sum('revenue');
        $totalMaterial = $projects->sum('material_cost');
        $totalGeneral = $projects->sum('general_cost');
        $totalShipping = $projects->sum('shipping_cost');
        $totalCost = $totalMaterial + $totalGeneral + $totalShipping;
        $totalProfit = $totalRevenue - $totalCost;
        $overallMargin = $totalRevenue > 0 ? ($totalProfit / $totalRevenue) * 100 : 0;

        return view('pages.project-monitoring.index', compact(
            'projects',
            'newProjects',
            'checkPartsProjects',
            'schedulingProjects',
            'inProgressProjects',
            'completedProjects',
            'totalProjects',
            'totalRevenue',
            'totalMaterial',
            'totalGeneral',
            'totalShipping',
            'totalProfit',
            'overallMargin',
            'selectedYear',
            'availableYears'
        ));
    }

    /**
     * Show the detailed profitability dashboard of a project.
     */
    public function show($id)
    {
        $project = PendingPO::join('quotation as q', 'pending_po.id_quotation', '=', 'q.id')
            ->join('pic as p', 'q.id_pic', '=', 'p.id')
            ->join('client as c', 'p.id_client', '=', 'c.id')
            ->join('users as u', 'q.id_sales', '=', 'u.id')
            ->where('pending_po.id', $id)
            ->select(
                'pending_po.*',
                'c.company',
                'p.name_pic as pic_name',
                'u.name as sales_name',
                'q.nett as revenue',
                'q.no_quote',
                'q.type as quote_type'
            )
            ->firstOrFail();

        // Quotation Items (Revenue Details)
        $quoteItems = collect();
        if ($project->quote_type === 'Service') {
            $quoteItems = SubtitleQuotation::with('detail')
                ->where('id_quotation', $project->id_quotation)
                ->get()
                ->flatMap(fn($subtitle) => $subtitle->detail)
                ->map(fn($item) => (object) [
                    'item_name' => $item->product ?: $item->detail,
                    'qty' => $item->qty,
                    'unit' => $item->info_qty,
                    'price' => $item->price,
                    'amount' => $item->qty * $item->price
                ]);
        } else {
            $quoteItems = DetailQuotation::where('id_quotation', $project->id_quotation)->get()
                ->map(fn($item) => (object) [
                    'item_name' => $item->detail_product,
                    'qty' => $item->qty,
                    'unit' => $item->info_qty,
                    'price' => $item->modal, // selling price is modal in this schema
                    'amount' => $item->amount
                ]);
        }

        // Purchases (PR & Costs)
        $purchases = PurchaseRequest::where('id_pending', $project->id)
            ->with('equivalent.product')
            ->get();

        // General Expenses
        $expenses = ProjectExpense::where('id_pending', $project->id)
            ->with('user')
            ->orderBy('date', 'desc')
            ->get();

        // Shipping Costs
        $shippingCosts = Expanse::where('id_pending', $project->id)
            ->where('type', 'Resi')
            ->get();

        // Financial Math
        $materialCost = $purchases->where('status', '3')->sum('amount');
        $generalCost = $expenses->sum('amount');
        $shippingCost = $shippingCosts->sum('cost');
        $totalCost = $materialCost + $generalCost + $shippingCost;
        $profit = $project->revenue - $totalCost;
        $margin = $project->revenue > 0 ? ($profit / $project->revenue) * 100 : 0;

        // Load relationships for logistic check tab
        $subQuote = SubtitleQuotation::with('detail.pending')
            ->where('id_quotation', $project->id_quotation)
            ->get();
        $serial = SerialProduct::all();

        return view('pages.project-monitoring.show', compact(
            'project',
            'quoteItems',
            'purchases',
            'expenses',
            'shippingCosts',
            'materialCost',
            'generalCost',
            'shippingCost',
            'totalCost',
            'profit',
            'margin',
            'subQuote',
            'serial'
        ));
    }

    /**
     * Store a general expense entry for the project.
     */
    public function storeExpense(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|in:Transport,Akomodasi,Konsumsi,Material,Alat,Lain-lain',
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
            'receipt' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:4096',
        ]);

        $project = PendingPO::findOrFail($id);

        $expense = new ProjectExpense();
        $expense->id_pending = $project->id;
        $expense->id_user = Auth::id();
        $expense->name = $request->name;
        $expense->category = $request->category;
        $expense->amount = $request->amount;
        $expense->date = $request->date;

        if ($request->hasFile('receipt')) {
            $file = $request->file('receipt');
            $ext = $file->getClientOriginalExtension();
            $filename = 'expense_' . Str::random(10) . '_' . time() . '.' . $ext;

            $uploadPath = public_path('asset/expenses');
            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }
            $file->move($uploadPath, $filename);

            $expense->receipt = 'asset/expenses/' . $filename;
        }

        $expense->save();

        return redirect()->route('project-monitoring.show', $id)
            ->with('success', 'Biaya operasional berhasil dicatat.');
    }

    /**
     * Remove a general expense entry.
     */
    public function destroyExpense($id)
    {
        $expense = ProjectExpense::findOrFail($id);
        $projectId = $expense->id_pending;

        // Delete receipt file if exists
        if ($expense->receipt && file_exists(public_path($expense->receipt))) {
            @unlink(public_path($expense->receipt));
        }

        $expense->delete();

        return redirect()->route('project-monitoring.show', $projectId)
            ->with('success', 'Biaya operasional berhasil dihapus.');
    }

    /**
     * Update the progress category and current status step of the project.
     */
    public function updateStatusStep(Request $request, $id)
    {
        $request->validate([
            'project_category'    => 'required|string|in:Service PM,Overhaul,Rental,Unit,Piping',
            'project_status_step' => 'required|integer|min:1|max:5',
        ]);

        $project = PendingPO::findOrFail($id);
        $project->project_category = $request->project_category;
        $project->project_status_step = $request->project_status_step;

        // Auto-complete project status if it reaches the final step
        $isCompleted = false;
        if ($request->project_category === 'Service PM' && $request->project_status_step == 4) $isCompleted = true;
        if ($request->project_category === 'Overhaul' && $request->project_status_step == 4) $isCompleted = true;
        if ($request->project_category === 'Rental' && $request->project_status_step == 5) $isCompleted = true;
        if ($request->project_category === 'Unit' && $request->project_status_step == 4) $isCompleted = true;
        if ($request->project_category === 'Piping' && $request->project_status_step == 5) $isCompleted = true;

        if ($isCompleted) {
            $project->status = 6; // Done
        } else {
            // Transition from New (status 0) to In Progress (status 2) since progress has started
            if ($project->status == 0) {
                $project->status = 2;
            }
            // Revert status to In Progress if moved back from completed step
            elseif ($project->status == 6) {
                $project->status = 2;
            }
        }

        $project->save();

        return redirect()->route('project-monitoring.show', $id)
            ->with('success', 'Progress langkah status proyek berhasil diperbarui.');
    }
}
