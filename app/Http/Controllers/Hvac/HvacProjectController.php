<?php

namespace App\Http\Controllers\Hvac;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Hvac\HvacCityDesignTemp;
use App\Models\Hvac\HvacProject;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HvacProjectController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status');
        $search = $request->query('search');

        $query = HvacProject::with(['client', 'sales', 'engineer', 'rooms.result'])
            ->orderBy('id', 'desc');

        if ($status) {
            $query->where('status', $status);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('project_code', 'like', "%{$search}%")
                  ->orWhere('project_name', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%")
                  ->orWhereHas('client', function ($cq) use ($search) {
                      $cq->where('company', 'like', "%{$search}%");
                  });
            });
        }

        $projects = $query->paginate(15)->withQueryString();

        $stats = [
            'total'      => HvacProject::count(),
            'draft'      => HvacProject::where('status', 'draft')->count(),
            'calculated' => HvacProject::where('status', 'calculated')->count(),
            'approved'   => HvacProject::where('status', 'approved')->count(),
        ];

        return view('pages.hvac.project.index', compact('projects', 'stats', 'status', 'search'));
    }

    public function create()
    {
        $projectCode = HvacProject::generateProjectCode();
        $salesList = User::where('active', '1')->orderBy('name', 'asc')->get(['id', 'name', 'role']);
        $cities = HvacCityDesignTemp::orderBy('city_name', 'asc')->get();

        return view('pages.hvac.project.create', compact('projectCode', 'salesList', 'cities'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_name'        => 'required|string|max:200',
            'id_client'           => 'nullable|integer',
            'customer_name'       => 'nullable|string|max:200',
            'id_sales'            => 'nullable|integer',
            'id_engineer'         => 'nullable|integer',
            'location'            => 'nullable|string|max:255',
            'design_outdoor_temp' => 'required|numeric|min:20|max:50',
            'design_outdoor_rh'   => 'required|numeric|min:10|max:100',
            'notes'               => 'nullable|string',
        ]);

        $validated['project_code'] = HvacProject::generateProjectCode();
        $validated['id_sales'] = $validated['id_sales'] ?: Auth::id();
        $validated['status'] = 'draft';

        $project = HvacProject::create($validated);

        return redirect()->route('hvac.project.show', $project->id)
            ->with('success', "Proyek HVAC {$project->project_code} berhasil dibuat.");
    }

    public function show($id)
    {
        $project = HvacProject::with([
            'client',
            'sales',
            'engineer',
            'rooms.result.recommendedAc',
            'rooms.walls',
            'rooms.windows',
            'rooms.occupants',
        ])->findOrFail($id);

        // Aggregate total cooling capacity across all rooms in this project
        $totalProjectLoadBtuh = $project->rooms->sum(fn($r) => $r->result?->design_load_btuh ?? 0);
        $totalProjectLoadKw   = $project->rooms->sum(fn($r) => $r->result?->design_load_kw ?? 0);
        $totalProjectLoadTr   = $project->rooms->sum(fn($r) => $r->result?->design_load_tr ?? 0);
        $totalProjectLoadPk   = $project->rooms->sum(fn($r) => $r->result?->design_load_pk ?? 0);

        return view('pages.hvac.project.show', compact(
            'project',
            'totalProjectLoadBtuh',
            'totalProjectLoadKw',
            'totalProjectLoadTr',
            'totalProjectLoadPk'
        ));
    }

    public function edit($id)
    {
        $project = HvacProject::findOrFail($id);
        $salesList = User::where('active', '1')->orderBy('name', 'asc')->get(['id', 'name', 'role']);
        $cities = HvacCityDesignTemp::orderBy('city_name', 'asc')->get();

        return view('pages.hvac.project.edit', compact('project', 'salesList', 'cities'));
    }

    public function update(Request $request, $id)
    {
        $project = HvacProject::findOrFail($id);

        $validated = $request->validate([
            'project_name'        => 'required|string|max:200',
            'id_client'           => 'nullable|integer',
            'customer_name'       => 'nullable|string|max:200',
            'id_sales'            => 'nullable|integer',
            'id_engineer'         => 'nullable|integer',
            'location'            => 'nullable|string|max:255',
            'design_outdoor_temp' => 'required|numeric|min:20|max:50',
            'design_outdoor_rh'   => 'required|numeric|min:10|max:100',
            'status'              => 'required|in:draft,calculated,approved,quoted',
            'notes'               => 'nullable|string',
        ]);

        $project->update($validated);

        return redirect()->route('hvac.project.show', $project->id)
            ->with('success', 'Data Proyek berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $project = HvacProject::findOrFail($id);
        $project->delete();

        return redirect()->route('hvac.project.index')
            ->with('success', 'Proyek HVAC berhasil dihapus.');
    }
}
