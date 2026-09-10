<?php

namespace App\Http\Controllers\Hvac;

use App\Http\Controllers\Controller;
use App\Models\Hvac\HvacAcCatalog;
use App\Models\Hvac\HvacActivityLoad;
use App\Models\Hvac\HvacGlassType;
use App\Models\Hvac\HvacMaterial;
use Illuminate\Http\Request;

class HvacMasterCatalogController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->query('tab', 'ac_catalog');
        $selectedAcType = $request->query('ac_type', 'all');

        $acUnits = HvacAcCatalog::orderBy('cooling_capacity_btuh', 'asc')->get();
        $allAcTypes = HvacAcCatalog::select('ac_type')->distinct()->pluck('ac_type');
        $acTypeCounts = HvacAcCatalog::select('ac_type', \Illuminate\Support\Facades\DB::raw('count(*) as count'))
            ->groupBy('ac_type')
            ->pluck('count', 'ac_type');
        $totalAcCount = $acUnits->count();

        $materials = HvacMaterial::orderBy('category')->orderBy('material_name')->get();
        $glassTypes = HvacGlassType::orderBy('glass_name')->get();
        $activities = HvacActivityLoad::orderBy('activity_name')->get();

        return view('pages.hvac.master.index', compact(
            'tab',
            'acUnits',
            'materials',
            'glassTypes',
            'activities',
            'allAcTypes',
            'acTypeCounts',
            'totalAcCount',
            'selectedAcType'
        ));
    }

    public function storeAc(Request $request)
    {
        $validated = $request->validate([
            'brand'                 => 'required|string|max:100',
            'model_name'            => 'required|string|max:150',
            'ac_type'               => 'required|string',
            'nominal_pk'            => 'required|numeric',
            'cooling_capacity_btuh' => 'required|numeric',
            'cooling_capacity_kw'   => 'required|numeric',
            'power_input_watt'      => 'nullable|numeric',
            'refrigerant'           => 'required|string|max:20',
            'energy_rating'         => 'nullable|string|max:50',
        ]);

        HvacAcCatalog::create($validated);

        return redirect()->route('hvac.master.index', ['tab' => 'ac_catalog'])
            ->with('success', 'Unit AC baru berhasil ditambahkan ke katalog.');
    }

    public function storeMaterial(Request $request)
    {
        $validated = $request->validate([
            'category'      => 'required|string',
            'material_name' => 'required|string|max:150',
            'u_value'       => 'required|numeric',
            'default_cltd'  => 'nullable|numeric',
            'description'   => 'nullable|string',
        ]);

        HvacMaterial::create($validated);

        return redirect()->route('hvac.master.index', ['tab' => 'materials'])
            ->with('success', 'Material konstruksi baru berhasil ditambahkan.');
    }

    public function storeGlass(Request $request)
    {
        $validated = $request->validate([
            'glass_name'          => 'required|string|max:150',
            'u_value'             => 'required|numeric',
            'shgc'                => 'required|numeric|between:0,1',
            'shading_coefficient' => 'nullable|numeric',
            'description'         => 'nullable|string',
        ]);

        HvacGlassType::create($validated);

        return redirect()->route('hvac.master.index', ['tab' => 'glass'])
            ->with('success', 'Tipe kaca baru berhasil ditambahkan.');
    }
}
