<?php

namespace App\Http\Controllers\Hvac;

use App\Http\Controllers\Controller;
use App\Models\Hvac\HvacActivityLoad;
use App\Models\Hvac\HvacGlassType;
use App\Models\Hvac\HvacMaterial;
use App\Models\Hvac\HvacProject;
use App\Models\Hvac\HvacRoom;
use App\Models\Hvac\HvacRoomEquipment;
use App\Models\Hvac\HvacRoomInfiltration;
use App\Models\Hvac\HvacRoomLighting;
use App\Models\Hvac\HvacRoomOccupant;
use App\Models\Hvac\HvacRoomRoof;
use App\Models\Hvac\HvacRoomVentilation;
use App\Models\Hvac\HvacRoomWall;
use App\Models\Hvac\HvacRoomWindow;
use App\Services\Hvac\HvacCoolingLoadCalculator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HvacRoomController extends Controller
{
    protected HvacCoolingLoadCalculator $calculator;

    public function __construct(HvacCoolingLoadCalculator $calculator)
    {
        $this->calculator = $calculator;
    }

    /**
     * Standalone Quick Cooling Load Calculator for instant Sales estimation
     */
    public function standaloneQuickCalculator()
    {
        $acUnits = \App\Models\Hvac\HvacAcCatalog::where('is_active', true)->orderBy('cooling_capacity_btuh', 'asc')->get();

        return view('pages.hvac.quick-calculator', compact('acUnits'));
    }

    /**
     * Save standalone quick estimation into an official HVAC Project & Room
     */
    public function saveStandaloneQuick(Request $request)
    {
        $validated = $request->validate([
            'project_name'          => 'required|string|max:200',
            'id_client'             => 'nullable|integer',
            'customer_name'         => 'nullable|string|max:200',
            'location'              => 'nullable|string|max:255',
            'room_name'             => 'required|string|max:150',
            'length'                => 'required|numeric|min:0.1',
            'width'                 => 'required|numeric|min:0.1',
            'height'                => 'required|numeric|min:0.1',
            'quick_occupants_count' => 'required|integer|min:0',
            'quick_room_condition'  => 'required|string',
            'quick_sun_exposure'    => 'required|string',
            'safety_factor_percent' => 'required|numeric|min:0|max:50',
        ]);

        $room = DB::transaction(function () use ($validated) {
            $project = HvacProject::create([
                'project_code'        => HvacProject::generateProjectCode(),
                'project_name'        => $validated['project_name'],
                'id_client'           => $validated['id_client'] ?: null,
                'customer_name'       => $validated['customer_name'] ?: null,
                'id_sales'            => auth()->id(),
                'location'            => $validated['location'] ?: null,
                'design_outdoor_temp' => 33.5,
                'design_outdoor_rh'   => 75.0,
                'status'              => 'draft',
            ]);

            $room = HvacRoom::create([
                'id_hvac_project'       => $project->id,
                'room_name'             => $validated['room_name'],
                'calculation_mode'      => 'quick',
                'room_type'             => 'Quick Estimate',
                'length'                => $validated['length'],
                'width'                 => $validated['width'],
                'height'                => $validated['height'],
                'indoor_temp'           => 24.0,
                'indoor_rh'             => 50.0,
                'outdoor_temp'          => 33.5,
                'outdoor_rh'            => 75.0,
                'quick_occupants_count' => $validated['quick_occupants_count'],
                'quick_room_condition'  => $validated['quick_room_condition'],
                'quick_sun_exposure'    => $validated['quick_sun_exposure'],
                'safety_factor_percent' => $validated['safety_factor_percent'],
            ]);

            $this->calculator->calculate($room);

            return $room;
        });

        return redirect()->route('hvac.room.result', $room->id)
            ->with('success', "Proyek {$room->project->project_code} & Ruangan {$room->room_name} berhasil disimpan.");
    }

    public function create($projectId)
    {
        $project = HvacProject::findOrFail($projectId);

        $materials = HvacMaterial::where('is_active', true)->orderBy('category')->orderBy('material_name')->get();
        $glassTypes = HvacGlassType::where('is_active', true)->orderBy('glass_name')->get();
        $activities = HvacActivityLoad::orderBy('activity_name')->get();

        return view('pages.hvac.room.create', compact('project', 'materials', 'glassTypes', 'activities'));
    }

    public function store(Request $request, $projectId)
    {
        $project = HvacProject::findOrFail($projectId);

        $validated = $request->validate([
            'room_name'             => 'required|string|max:150',
            'calculation_mode'      => 'required|in:quick,detailed',
            'room_type'             => 'nullable|string|max:100',
            'length'                => 'required|numeric|min:0.1',
            'width'                 => 'required|numeric|min:0.1',
            'height'                => 'required|numeric|min:0.1',
            'indoor_temp'           => 'required|numeric',
            'indoor_rh'             => 'required|numeric',
            'outdoor_temp'          => 'required|numeric',
            'outdoor_rh'            => 'required|numeric',
            'quick_occupants_count' => 'nullable|integer|min:0',
            'quick_room_condition'  => 'nullable|string',
            'quick_sun_exposure'    => 'nullable|string',
            'safety_factor_percent' => 'required|numeric|min:0|max:100',
            'notes'                 => 'nullable|string',
        ]);

        $validated['id_hvac_project'] = $project->id;

        $room = DB::transaction(function () use ($validated, $request) {
            $room = HvacRoom::create($validated);

            if ($room->calculation_mode === 'detailed') {
                $this->syncDetailedElements($room, $request);
            }

            // Execute cooling load calculation engine
            $this->calculator->calculate($room);

            return $room;
        });

        return redirect()->route('hvac.room.result', $room->id)
            ->with('success', "Kalkulasi Beban Pendinginan untuk {$room->room_name} berhasil dihitung.");
    }

    public function edit($id)
    {
        $room = HvacRoom::with([
            'project',
            'walls',
            'roofs',
            'windows',
            'occupants',
            'lightings',
            'equipments',
            'ventilations',
            'infiltrations',
            'result',
        ])->findOrFail($id);

        $materials = HvacMaterial::where('is_active', true)->orderBy('category')->orderBy('material_name')->get();
        $glassTypes = HvacGlassType::where('is_active', true)->orderBy('glass_name')->get();
        $activities = HvacActivityLoad::orderBy('activity_name')->get();

        return view('pages.hvac.room.edit', compact('room', 'materials', 'glassTypes', 'activities'));
    }

    public function update(Request $request, $id)
    {
        $room = HvacRoom::findOrFail($id);

        $validated = $request->validate([
            'room_name'             => 'required|string|max:150',
            'calculation_mode'      => 'required|in:quick,detailed',
            'room_type'             => 'nullable|string|max:100',
            'length'                => 'required|numeric|min:0.1',
            'width'                 => 'required|numeric|min:0.1',
            'height'                => 'required|numeric|min:0.1',
            'indoor_temp'           => 'required|numeric',
            'indoor_rh'             => 'required|numeric',
            'outdoor_temp'          => 'required|numeric',
            'outdoor_rh'            => 'required|numeric',
            'quick_occupants_count' => 'nullable|integer|min:0',
            'quick_room_condition'  => 'nullable|string',
            'quick_sun_exposure'    => 'nullable|string',
            'safety_factor_percent' => 'required|numeric|min:0|max:100',
            'notes'                 => 'nullable|string',
        ]);

        DB::transaction(function () use ($room, $validated, $request) {
            $room->update($validated);

            if ($room->calculation_mode === 'detailed') {
                $this->syncDetailedElements($room, $request);
            }

            // Recalculate
            $this->calculator->calculate($room);
        });

        return redirect()->route('hvac.room.result', $room->id)
            ->with('success', "Kalkulasi Beban Pendinginan untuk {$room->room_name} berhasil diperbarui.");
    }

    public function result($id)
    {
        $room = HvacRoom::with([
            'project.client',
            'result.recommendedAc',
            'walls.material',
            'roofs.material',
            'windows.glassType',
            'occupants.activity',
            'lightings',
            'equipments',
            'ventilations',
            'infiltrations',
        ])->findOrFail($id);

        if (!$room->result) {
            $this->calculator->calculate($room);
            $room->load('result.recommendedAc');
        }

        return view('pages.hvac.room.result', compact('room'));
    }

    public function printReport($id)
    {
        $room = HvacRoom::with([
            'project.client',
            'project.sales',
            'project.engineer',
            'result.recommendedAc',
            'walls.material',
            'roofs.material',
            'windows.glassType',
            'occupants.activity',
            'lightings',
            'equipments',
            'ventilations',
            'infiltrations',
        ])->findOrFail($id);

        return view('pages.hvac.room.print', compact('room'));
    }

    public function destroy($id)
    {
        $room = HvacRoom::findOrFail($id);
        $projectId = $room->id_hvac_project;
        $room->delete();

        return redirect()->route('hvac.project.show', $projectId)
            ->with('success', 'Ruangan berhasil dihapus.');
    }

    /**
     * Helper to sync detailed elements from form input arrays
     */
    protected function syncDetailedElements(HvacRoom $room, Request $request): void
    {
        // 1. Walls
        HvacRoomWall::where('id_hvac_room', $room->id)->delete();
        if ($request->has('walls') && is_array($request->input('walls'))) {
            foreach ($request->input('walls') as $w) {
                if (!empty($w['wall_name'])) {
                    $length = (float) ($w['length'] ?? 0);
                    $height = (float) ($w['height'] ?? $room->height);
                    $gross = (float) ($w['gross_area'] ?? ($length * $height));
                    $deduction = (float) ($w['window_deduction_area'] ?? 0);
                    $net = max(0.0, $gross - $deduction);

                    HvacRoomWall::create([
                        'id_hvac_room'          => $room->id,
                        'wall_name'             => $w['wall_name'],
                        'orientation'           => $w['orientation'] ?? 'N',
                        'is_external'           => isset($w['is_external']) ? (bool) $w['is_external'] : true,
                        'length'                => $length,
                        'height'                => $height,
                        'gross_area'            => $gross,
                        'window_deduction_area' => $deduction,
                        'net_area'              => $net,
                        'id_material'           => !empty($w['id_material']) ? $w['id_material'] : null,
                        'u_value'               => (float) ($w['u_value'] ?? 2.80),
                        'temp_difference'       => (float) ($w['temp_difference'] ?? 9.00),
                    ]);
                }
            }
        }

        // 2. Roofs
        HvacRoomRoof::where('id_hvac_room', $room->id)->delete();
        if ($request->has('roofs') && is_array($request->input('roofs'))) {
            foreach ($request->input('roofs') as $r) {
                if (!empty($r['roof_name'])) {
                    HvacRoomRoof::create([
                        'id_hvac_room'      => $room->id,
                        'roof_name'         => $r['roof_name'],
                        'area'              => (float) ($r['area'] ?? $room->floor_area),
                        'id_material'       => !empty($r['id_material']) ? $r['id_material'] : null,
                        'u_value'           => (float) ($r['u_value'] ?? 1.50),
                        'is_exposed_to_sun' => isset($r['is_exposed_to_sun']) ? (bool) $r['is_exposed_to_sun'] : true,
                        'temp_difference'   => (float) ($r['temp_difference'] ?? 15.00),
                    ]);
                }
            }
        }

        // 3. Windows / Glass
        HvacRoomWindow::where('id_hvac_room', $room->id)->delete();
        if ($request->has('windows') && is_array($request->input('windows'))) {
            foreach ($request->input('windows') as $win) {
                if (!empty($win['window_name'])) {
                    $wWidth = (float) ($win['width'] ?? 0);
                    $wHeight = (float) ($win['height'] ?? 0);
                    $wQty = max(1, (int) ($win['quantity'] ?? 1));
                    $wTotalArea = (float) ($win['total_area'] ?? ($wWidth * $wHeight * $wQty));

                    HvacRoomWindow::create([
                        'id_hvac_room'            => $room->id,
                        'window_name'             => $win['window_name'],
                        'width'                   => $wWidth,
                        'height'                  => $wHeight,
                        'quantity'                => $wQty,
                        'total_area'              => $wTotalArea,
                        'orientation'             => $win['orientation'] ?? 'N',
                        'id_glass_type'           => !empty($win['id_glass_type']) ? $win['id_glass_type'] : null,
                        'u_value'                 => (float) ($win['u_value'] ?? 5.80),
                        'shgc'                    => (float) ($win['shgc'] ?? 0.820),
                        'internal_shading_factor' => (float) ($win['internal_shading_factor'] ?? 1.00),
                        'solar_irradiance_w_m2'   => (float) ($win['solar_irradiance_w_m2'] ?? 300.00),
                    ]);
                }
            }
        }

        // 4. Occupants
        HvacRoomOccupant::where('id_hvac_room', $room->id)->delete();
        if ($request->has('occupants') && is_array($request->input('occupants'))) {
            foreach ($request->input('occupants') as $occ) {
                if (!empty($occ['activity_name']) || !empty($occ['id_activity'])) {
                    $activityName = $occ['activity_name'] ?? 'Penghuni';
                    if (!empty($occ['id_activity']) && $act = HvacActivityLoad::find($occ['id_activity'])) {
                        $activityName = $act->activity_name;
                    }

                    HvacRoomOccupant::create([
                        'id_hvac_room'             => $room->id,
                        'id_activity'              => !empty($occ['id_activity']) ? $occ['id_activity'] : null,
                        'activity_name'            => $activityName,
                        'quantity'                 => max(1, (int) ($occ['quantity'] ?? 1)),
                        'sensible_watt_per_person' => (float) ($occ['sensible_watt_per_person'] ?? 75.00),
                        'latent_watt_per_person'   => (float) ($occ['latent_watt_per_person'] ?? 55.00),
                    ]);
                }
            }
        }

        // 5. Lightings
        HvacRoomLighting::where('id_hvac_room', $room->id)->delete();
        if ($request->has('lightings') && is_array($request->input('lightings'))) {
            foreach ($request->input('lightings') as $l) {
                if (!empty($l['lighting_name'])) {
                    HvacRoomLighting::create([
                        'id_hvac_room'   => $room->id,
                        'lighting_name'  => $l['lighting_name'],
                        'quantity'       => max(1, (int) ($l['quantity'] ?? 1)),
                        'watt_per_unit'  => (float) ($l['watt_per_unit'] ?? 20.00),
                        'ballast_factor' => (float) ($l['ballast_factor'] ?? 1.00),
                        'usage_factor'   => (float) ($l['usage_factor'] ?? 1.00),
                    ]);
                }
            }
        }

        // 6. Equipments
        HvacRoomEquipment::where('id_hvac_room', $room->id)->delete();
        if ($request->has('equipments') && is_array($request->input('equipments'))) {
            foreach ($request->input('equipments') as $eq) {
                if (!empty($eq['equipment_name'])) {
                    HvacRoomEquipment::create([
                        'id_hvac_room'      => $room->id,
                        'equipment_name'    => $eq['equipment_name'],
                        'quantity'          => max(1, (int) ($eq['quantity'] ?? 1)),
                        'watt_per_unit'     => (float) ($eq['watt_per_unit'] ?? 100.00),
                        'usage_factor'      => (float) ($eq['usage_factor'] ?? 0.80),
                        'sensible_fraction' => (float) ($eq['sensible_fraction'] ?? 1.00),
                        'latent_fraction'   => (float) ($eq['latent_fraction'] ?? 0.00),
                    ]);
                }
            }
        }

        // 7. Ventilations
        HvacRoomVentilation::where('id_hvac_room', $room->id)->delete();
        if ($request->has('ventilations') && is_array($request->input('ventilations'))) {
            foreach ($request->input('ventilations') as $v) {
                if (isset($v['input_value']) && (float) $v['input_value'] > 0) {
                    HvacRoomVentilation::create([
                        'id_hvac_room' => $room->id,
                        'method'       => $v['method'] ?? 'per_person',
                        'input_value'  => (float) $v['input_value'],
                        'airflow_ls'   => 0,
                        'airflow_cfm'  => 0,
                    ]);
                }
            }
        }

        // 8. Infiltrations
        HvacRoomInfiltration::where('id_hvac_room', $room->id)->delete();
        if ($request->has('infiltrations') && is_array($request->input('infiltrations'))) {
            foreach ($request->input('infiltrations') as $inf) {
                if (isset($inf['ach_value']) && (float) $inf['ach_value'] > 0) {
                    HvacRoomInfiltration::create([
                        'id_hvac_room' => $room->id,
                        'method'       => $inf['method'] ?? 'ach',
                        'ach_value'    => (float) $inf['ach_value'],
                        'airflow_ls'   => 0,
                    ]);
                }
            }
        }
    }
}
