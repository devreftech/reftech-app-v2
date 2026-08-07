<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Machine;
use App\Models\Unit;
use App\Models\UnitPmTemplateItem;
use App\Models\PowerServicePrice;
use App\Models\Contract;
use App\Models\ContractVisitSchedule;
use App\Models\Quotation;
use App\Models\Reports;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ForecastController extends Controller
{
    /**
     * Dashboard Forecast (Tampilan Utama)
     */
    public function index(Request $request)
    {
        $year = $request->input('year', Carbon::now()->year);
        $salesId = Auth::user()->role == 'Admin' || Auth::user()->role == 'Sales Manager' 
            ? $request->input('id_sales') 
            : Auth::id();
        $semester = $request->input('semester', 'all');

        // Get sales users for filter
        $salesUsers = \App\Models\User::whereIn('id', [1, 2, 3, 4, 32])->get();

        $forecastData = $this->getForecastDataArray($salesId, $year, $semester);

        return view('pages.sales.forecast.dashboard', array_merge([
            'year' => $year,
            'salesId' => $salesId,
            'salesUsers' => $salesUsers,
            'semester' => $semester,
        ], $forecastData));
    }

    /**
     * Public method to compute forecast metrics and data breakdown (reusable on Sales Dashboard)
     */
    public function getForecastDataArray($salesId, $year, $semester = 'all')
    {
        $cacheKey = "forecast_data_{$salesId}_{$year}_{$semester}";
        return \Illuminate\Support\Facades\Cache::remember($cacheKey, 600, function() use ($salesId, $year, $semester) {
            // Query machines (AIR COMPRESSOR SCREW only)
            $query = Machine::with(['client.sales', 'unit.unit', 'forecastHistories'])
                ->whereHas('unit.unit', function($q) {
                    $q->where('unit', 'AIR COMPRESSOR SCREW');
                })
                ->where(function($q) use ($year) {
                    $q->where('is_forecasted', true)
                      ->orWhereHas('forecastHistories', function($sub) use ($year) {
                          $sub->where('year', $year)->where('is_forecasted', true);
                      });
                });

            if ($salesId) {
                $query->whereHas('client', function($q) use ($salesId) {
                    $q->where('id_sales', $salesId);
                });
            }
            $machines = $query->get();

            // Initialize months for calendar year (Jan - Dec) based on semester
            $startMonth = 1;
            $endMonth = 12;
            if ($semester == '1') {
                $endMonth = 6;
            } elseif ($semester == '2') {
                $startMonth = 7;
            }

            $months = [];
            for ($m = $startMonth; $m <= $endMonth; $m++) {
                $date = Carbon::create($year, $m, 1);
                $months[$date->format('Y-m')] = [
                    'name' => $date->format('F'),
                    'target' => 0,       // Original Forecast Target
                    'projection' => 0,   // Realistic Projection
                    'actual' => 0,       // Realized PO
                    'forecast_details' => [],
                    'actual_details' => [],
                ];
            }

            // --- OPTIMIZATION: Pre-fetch resources to avoid N+1 query problem ---
            // 1. Bulk load all service prices
            $servicePricesMap = PowerServicePrice::all()->keyBy(function($item) {
                return self::normalizePower($item->power);
            });

            // 2. Bulk load Template Penawaran PM items (unit_pm_template_items) for unique units in one query.
            // Parts cost is driven by the manually-curated PM template per unit+level, not a static
            // sparepart.pm_level attribute.
            $unitIds = $machines->pluck('unit.unit.id')->filter()->unique();
            $sparepartsGrouped = collect();
            if ($unitIds->isNotEmpty()) {
                $sparepartsGrouped = UnitPmTemplateItem::whereIn('id_unit', $unitIds)
                    ->get()
                    ->groupBy('id_unit');
            }

            // 3. Bulk load contract visit schedules for the target year and map in memory
            $allContractSchedules = ContractVisitSchedule::with(['contract.quotation.pic'])
                ->whereYear('planned_date', $year)
                ->where('status', 'Pending')
                ->get();

            $contractSchedulesByClient = collect();
            foreach ($allContractSchedules as $sched) {
                $cId = null;
                if ($sched->contract) {
                    if ($sched->contract->id_client) {
                        $cId = $sched->contract->id_client;
                    } elseif ($sched->contract->quotation && $sched->contract->quotation->pic) {
                        $cId = $sched->contract->quotation->pic->id_client;
                    }
                }
                if ($cId) {
                    if (!$contractSchedulesByClient->has($cId)) {
                        $contractSchedulesByClient->put($cId, collect());
                    }
                    $contractSchedulesByClient->get($cId)->push($sched);
                }
            }
            // --------------------------------------------------------------------

            // 1. Calculate Dynamically: Target Awal Tahun & Proyeksi Aktual
            $forecastDetails = [];
            $forecastedClientIds = [];
            foreach ($machines as $machine) {
                // Apply historical forecast snapshot if it exists for the requested year
                $history = $machine->forecastHistories->where('year', $year)->first();
                if ($history) {
                    $machine->is_forecasted = $history->is_forecasted;
                    $machine->forecast_type = $history->forecast_type;
                    $machine->visit_1_type = $history->visit_1_type;
                    $machine->visit_1_date = $history->visit_1_date;
                    $machine->visit_2_type = $history->visit_2_type;
                    $machine->visit_2_date = $history->visit_2_date;
                    $machine->visit_3_type = $history->visit_3_type;
                    $machine->visit_3_date = $history->visit_3_date;
                    $machine->visit_4_type = $history->visit_4_type;
                    $machine->visit_4_date = $history->visit_4_date;
                }

                $power = $machine->unit->unit->power ?? null;
                
                // Get prices for this machine's power capacity from memory map
                $servicePrices = null;
                if ($power) {
                    $normalizedPower = self::normalizePower($power);
                    $servicePrices = $servicePricesMap->get($normalizedPower);
                }

                // Get consumable spare parts from memory map
                $spareparts = collect();
                if ($machine->unit && $machine->unit->unit) {
                    $spareparts = $sparepartsGrouped->get($machine->unit->unit->id, collect());
                }

                // A. Target Awal Tahun & Proyeksi Berjalan (Calculated from manual schedules)
                $this->calculateCalendarForecast($machine, $year, $spareparts, $servicePrices, $months, 'target', $contractSchedulesByClient);
                $this->calculateCalendarForecast($machine, $year, $spareparts, $servicePrices, $months, 'projection', $contractSchedulesByClient);

                // Check if this machine has any active visits/schedules in this year
                $hasActiveForecast = false;
                if ($machine->forecast_type == 'contract') {
                    $schedulesCount = $contractSchedulesByClient->has($machine->id_client)
                        ? $contractSchedulesByClient->get($machine->id_client)->count()
                        : 0;
                    if ($schedulesCount > 0) {
                        $hasActiveForecast = true;
                    }
                } else {
                    foreach ([$machine->visit_1_date, $machine->visit_2_date, $machine->visit_3_date, $machine->visit_4_date] as $vDate) {
                        if (!empty($vDate)) {
                            try {
                                if (Carbon::parse($vDate)->year == $year) {
                                    $hasActiveForecast = true;
                                    break;
                                }
                            } catch (\Exception $e) {}
                        }
                    }
                }

                if ($hasActiveForecast) {
                    $forecastedClientIds[] = $machine->id_client;
                }

                // B. Collect breakdown details for this machine's scheduled visits
                if ($machine->forecast_type == 'contract') {
                    // For Contract type, load visit schedules from memory map
                    $schedules = $contractSchedulesByClient->get($machine->id_client, collect());

                    foreach ($schedules as $sched) {
                        $schedMonth = Carbon::parse($sched->planned_date)->month;
                        if ($semester == '1' && $schedMonth > 6) continue;
                        if ($semester == '2' && $schedMonth < 7) continue;

                        $forecastDetails[] = [
                            'company' => $machine->client->company ?? 'No Company',
                            'brand' => $machine->unit->brand ?? '-',
                            'model' => $machine->unit->unit->model ?? '-',
                            'serial' => $machine->serial,
                            'forecast_type' => 'contract',
                            'visit_name' => 'Visit Contract',
                            'date' => Carbon::parse($sched->planned_date)->format('d-m-Y'),
                            'raw_date' => $sched->planned_date,
                            'type' => 'Contract',
                            'parts_cost' => 0,
                            'service_fee' => $sched->estimated_revenue,
                            'total_revenue' => $sched->estimated_revenue
                        ];
                    }
                } else {
                    // For regular/parts
                    $visits = [
                        ['date' => $machine->visit_1_date, 'type' => $machine->visit_1_type, 'num' => 1],
                        ['date' => $machine->visit_2_date, 'type' => $machine->visit_2_type, 'num' => 2],
                        ['date' => $machine->visit_3_date, 'type' => $machine->visit_3_type, 'num' => 3],
                        ['date' => $machine->visit_4_date, 'type' => $machine->visit_4_type, 'num' => 4],
                    ];

                    foreach ($visits as $v) {
                        if (empty($v['date']) || empty($v['type'])) continue;
                        
                        $vDate = Carbon::parse($v['date']);
                        if ($vDate->year != $year) continue;

                        $vMonth = $vDate->month;
                        if ($semester == '1' && $vMonth > 6) continue;
                        if ($semester == '2' && $vMonth < 7) continue;

                        $pmLevel = $v['type'];
                        $partsTotal = 0;
                        foreach ($spareparts as $sp) {
                            if ($sp->level == $pmLevel) {
                                $partsTotal += ($sp->qty * $sp->price);
                            }
                        }

                        // Calculate Service Fee
                        $serviceFee = 0;
                        if ($machine->forecast_type == 'regular_service' && $servicePrices) {
                            switch ($pmLevel) {
                                case 'PM1': $serviceFee = $servicePrices->price_pm1; break;
                                case 'PM2': $serviceFee = $servicePrices->price_pm2; break;
                            }
                        }

                        $forecastDetails[] = [
                            'company' => $machine->client->company ?? 'No Company',
                            'brand' => $machine->unit->brand ?? '-',
                            'model' => $machine->unit->unit->model ?? '-',
                            'serial' => $machine->serial,
                            'forecast_type' => $machine->forecast_type,
                            'visit_name' => 'Visit ' . $v['num'],
                            'date' => $vDate->format('d-m-Y'),
                            'raw_date' => $v['date'],
                            'type' => $pmLevel,
                            'parts_cost' => $partsTotal,
                            'service_fee' => $serviceFee,
                            'total_revenue' => $partsTotal + $serviceFee
                        ];
                    }
                }
            }

            // Sort details by date ascending
            usort($forecastDetails, function($a, $b) {
                return strtotime($a['raw_date']) - strtotime($b['raw_date']);
            });

            // 2. Fetch Actual Won POs (Status 100)
            $clientIds = array_unique($forecastedClientIds);
            $picIds = \App\Models\Pic::whereIn('id_client', $clientIds)->pluck('id');

            $quotationQuery = Quotation::where('status', '100')
                ->where('is_primary', '1')
                ->where('level', '1')
                ->whereIn('id_pic', $picIds)
                ->where(function($q) use ($year) {
                    $q->whereYear('po_date', $year)
                      ->orWhere(function($sub) use ($year) {
                          $sub->whereNull('po_date')
                              ->whereYear('status_date', $year);
                      });
                });

            if ($salesId) {
                $quotationQuery->where('id_sales', $salesId);
            }

            $actualPOS = $quotationQuery->get();
            $actualPoDetails = [];
            foreach ($actualPOS as $po) {
                $date = $po->po_date ?: $po->status_date ?: $po->created_at;
                $cDate = Carbon::parse($date);
                $monthKey = $cDate->format('Y-m');

                $poItem = [
                    'id' => $po->id,
                    'no_quotation' => $po->no_quotation ?? '-',
                    'no_po' => $po->no_po ?? '-',
                    'company' => $po->pic->client->company ?? 'No Company',
                    'date' => $cDate->format('d-m-Y'),
                    'raw_date' => $cDate->format('Y-m-d'),
                    'total' => $po->nett ?? 0,
                ];

                $actualPoDetails[] = $poItem;

                if (isset($months[$monthKey])) {
                    $months[$monthKey]['actual'] += $po->nett ?? 0;
                    $months[$monthKey]['actual_details'][] = $poItem;
                }
            }

            // Group forecastDetails into months array as well
            foreach ($forecastDetails as $detail) {
                $mKey = Carbon::parse($detail['raw_date'])->format('Y-m');
                if (isset($months[$mKey])) {
                    $months[$mKey]['forecast_details'][] = $detail;
                }
            }

            // Summary cards data
            $totalTarget = 0;
            $totalProjection = 0;
            $totalActual = 0;
            foreach ($months as $m => $data) {
                $totalTarget += $data['target'];
                $totalProjection += $data['projection'];
                $totalActual += $data['actual'];
            }

            $realizationRate = $totalTarget > 0 ? round(($totalActual / $totalTarget) * 100, 1) : 0;

            return compact(
                'months', 'totalTarget', 'totalProjection', 'totalActual', 'realizationRate', 'machines', 'forecastDetails'
            );
        });
    }

    /**
     * Helper to get last service date before start of selected year (for Target baseline)
     */
    private function getBaselineDate($machine, $year)
    {
        $startOfYear = Carbon::create($year, 1, 1)->startOfDay();
        
        // Find last report of type Service before start of the year
        $lastReport = $machine->reports()
            ->where('type', 'Service')
            ->where('date', '<', $startOfYear)
            ->orderBy('date', 'desc')
            ->first();

        if ($lastReport && $lastReport->date) {
            return Carbon::parse($lastReport->date);
        }

        if ($machine->last_service_date) {
            $lsd = Carbon::parse($machine->last_service_date);
            if ($lsd->lt($startOfYear)) {
                return $lsd;
            }
        }

        $createdAt = Carbon::parse($machine->created_at);
        if ($createdAt->lt($startOfYear)) {
            return $createdAt;
        }

        return null;
    }

    /**
     * Core Algorithm: Manual PM scheduling projection (Visit 1 to 4)
     */
    private function calculateCalendarForecast($machine, $year, $spareparts, $servicePrices, &$months, $keyType, $contractSchedulesByClient = null)
    {
        if ($machine->forecast_type == 'contract') {
            // For Contract type, pull from visit schedules map in memory instead of database
            $schedules = $contractSchedulesByClient ? $contractSchedulesByClient->get($machine->id_client, collect()) : collect();

            foreach ($schedules as $sched) {
                $mKey = Carbon::parse($sched->planned_date)->format('Y-m');
                if (isset($months[$mKey])) {
                    $months[$mKey][$keyType] += $sched->estimated_revenue;
                }
            }
            return;
        }

        // For Parts and Regular Service types, use manual visits
        $visits = [
            ['date' => $machine->visit_1_date, 'type' => $machine->visit_1_type],
            ['date' => $machine->visit_2_date, 'type' => $machine->visit_2_type],
            ['date' => $machine->visit_3_date, 'type' => $machine->visit_3_type],
            ['date' => $machine->visit_4_date, 'type' => $machine->visit_4_type],
        ];

        foreach ($visits as $visit) {
            if (empty($visit['date']) || empty($visit['type'])) {
                continue;
            }

            $projectedDate = Carbon::parse($visit['date']);
            
            // We only care about projections landing inside the selected year
            if ($projectedDate->year != $year) {
                continue;
            }

            $mKey = $projectedDate->format('Y-m');
            if (!isset($months[$mKey])) {
                continue;
            }

            $pmLevel = $visit['type']; // 'PM1' or 'PM2'

            // 1. Calculate Spareparts Cost — sourced from the manually-curated PM template
            // for this unit + level (unit_pm_template_items), exact match per level (no cumulation).
            $partsTotal = 0;
            foreach ($spareparts as $sp) {
                if ($sp->level == $pmLevel) {
                    $partsTotal += ($sp->qty * $sp->price);
                }
            }

            // 2. Calculate Service Fee
            $serviceFee = 0;
            if ($machine->forecast_type == 'regular_service' && $servicePrices) {
                switch ($pmLevel) {
                    case 'PM1': $serviceFee = $servicePrices->price_pm1; break;
                    case 'PM2': $serviceFee = $servicePrices->price_pm2; break;
                }
            }

            $months[$mKey][$keyType] += ($partsTotal + $serviceFee);
        }
    }

    /**
     * Bulk Setup View for Sales Machine Mapping
     */
    public function bulkSetup(Request $request)
    {
        $user = Auth::user();
        $isAdmin = $user->role == 'Admin' || $user->role == 'Sales Manager';

        // Get sales users (ID 1, 2, 3, 4, 32)
        $salesUsers = \App\Models\User::whereIn('id', [1, 2, 3, 4, 32])->get();

        $query = Machine::with(['client.sales', 'unit.unit', 'forecastHistories'])
            ->whereHas('unit.unit', function($q) {
                $q->where('unit', 'AIR COMPRESSOR SCREW');
            });

        if ($isAdmin) {
            // Load all machines for these sales reps
            $query->whereHas('client', function($q) {
                $q->whereIn('id_sales', [1, 2, 3, 4, 32]);
            });
        } else {
            // Only load machines for this sales rep
            $query->whereHas('client', function($q) use ($user) {
                $q->where('id_sales', $user->id);
            });
            // Limit salesUsers to only themselves for tab view
            $salesUsers = \App\Models\User::where('id', $user->id)->get();
        }

        $machines = $query->get();

        // Fetch won POs (status 100) for these clients to show realization flags on setup page
        $clientIds = $machines->pluck('id_client')->unique();
        $picIds = \App\Models\Pic::whereIn('id_client', $clientIds)->pluck('id');

        $wonQuotations = \App\Models\Quotation::with('pic')
            ->where('status', '100')
            ->where('is_primary', '1')
            ->where('level', '1')
            ->whereIn('id_pic', $picIds)
            ->get();

        $clientWonMonths = [];
        foreach ($wonQuotations as $po) {
            if ($po->pic) {
                $cId = $po->pic->id_client;
                $poDate = $po->po_date ?: $po->status_date ?: $po->created_at;
                if ($poDate) {
                    $monthKey = \Carbon\Carbon::parse($poDate)->format('Y-m');
                    $clientWonMonths[$cId][$monthKey] = true;
                }
            }
        }

        // Group machines by sales rep ID
        $groupedMachines = $machines->groupBy(function($machine) {
            return $machine->client->id_sales ?? 0;
        });

        return view('pages.sales.forecast.setup', compact('groupedMachines', 'salesUsers', 'isAdmin', 'clientWonMonths'));
    }

    /**
     * Store Bulk Setup Settings
     */
    public function storeBulkSetup(Request $request)
    {
        $rules = [
            'machines' => 'required|array',
            'machines.*.forecast_type' => 'required|in:parts,regular_service,contract',
            'machines.*.is_forecasted' => 'required|boolean',
            'machines.*.last_service_date' => 'nullable|date',
            'machines.*.visit_1_type' => 'nullable|in:PM1,PM2',
            'machines.*.visit_1_date' => 'nullable|date',
            'machines.*.visit_2_type' => 'nullable|in:PM1,PM2',
            'machines.*.visit_2_date' => 'nullable|date',
            'machines.*.visit_3_type' => 'nullable|in:PM1,PM2',
            'machines.*.visit_3_date' => 'nullable|date',
            'machines.*.visit_4_type' => 'nullable|in:PM1,PM2',
            'machines.*.visit_4_date' => 'nullable|date',
        ];

        $this->validate($request, $rules);

        foreach ($request->machines as $id => $data) {
            $machine = Machine::find($id);
            if ($machine) {
                $machine->update([
                    'forecast_type' => $data['forecast_type'],
                    'is_forecasted' => $data['is_forecasted'],
                    'last_service_date' => $data['last_service_date'] ?? null,
                    'visit_1_type' => $data['visit_1_type'] ?? null,
                    'visit_1_date' => $data['visit_1_date'] ?? null,
                    'visit_2_type' => $data['visit_2_type'] ?? null,
                    'visit_2_date' => $data['visit_2_date'] ?? null,
                    'visit_3_type' => $data['visit_3_type'] ?? null,
                    'visit_3_date' => $data['visit_3_date'] ?? null,
                    'visit_4_type' => $data['visit_4_type'] ?? null,
                    'visit_4_date' => $data['visit_4_date'] ?? null,
                ]);

                // Determine the target forecast year from input dates
                $targetYear = null;
                foreach (['visit_1_date', 'visit_2_date', 'visit_3_date', 'visit_4_date'] as $field) {
                    if (!empty($data[$field])) {
                        try {
                            $targetYear = \Carbon\Carbon::parse($data[$field])->year;
                            break;
                        } catch (\Exception $e) {
                            // Ignore invalid date strings
                        }
                    }
                }
                if (!$targetYear) {
                    $targetYear = date('Y');
                }

                // Auto-upsert into forecast history for that year
                \App\Models\ForecastHistory::updateOrCreate(
                    ['id_machine' => $id, 'year' => $targetYear],
                    [
                        'forecast_type' => $data['forecast_type'],
                        'is_forecasted' => $data['is_forecasted'],
                        'visit_1_type' => $data['visit_1_type'] ?? null,
                        'visit_1_date' => $data['visit_1_date'] ?? null,
                        'visit_2_type' => $data['visit_2_type'] ?? null,
                        'visit_2_date' => $data['visit_2_date'] ?? null,
                        'visit_3_type' => $data['visit_3_type'] ?? null,
                        'visit_3_date' => $data['visit_3_date'] ?? null,
                        'visit_4_type' => $data['visit_4_type'] ?? null,
                        'visit_4_date' => $data['visit_4_date'] ?? null,
                    ]
                );
            }
        }

        self::clearForecastCache();

        if ($request->has('redirect_back')) {
            return redirect()->back()->with('message', 'Pengaturan forecast mesin berhasil disimpan.');
        }

        return redirect()->route('forecast.setup')->with('message', 'Pengaturan forecast mesin berhasil disimpan.');
    }

    /**
     * Trigger Default Forecast Generation for Next Year
     */
    public function generateDefaultForecast(Request $request)
    {
        $year = $request->get('year');
        if (!$year) {
            $year = date('Y') + 1; // default to next year
        }

        try {
            \Illuminate\Support\Facades\Artisan::call('forecast:generate-next-year', ['year' => $year]);
            self::clearForecastCache();
            return redirect()->back()->with('message', "Berhasil menjadwalkan otomatis rencana forecast untuk tahun {$year} dengan kalkulasi default (PM1-PM1-PM2)!");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', "Gagal melakukan generate otomatis: " . $e->getMessage());
        }
    }

    /**
     * Master Harga Jasa PM (CRUD View)
     */
    public function managePrices()
    {
        // Get all pricing rules (exclude system standard template row)
        $prices = PowerServicePrice::where('power', '!=', 'STANDARD_TEMPLATE')->orderBy('power')->get();

        // Get standard template (record with power 'STANDARD_TEMPLATE' or fallback)
        $defaultTemplate = PowerServicePrice::where('power', 'STANDARD_TEMPLATE')->first()
            ?? PowerServicePrice::whereNotNull('desc_pm1')->latest()->first();

        // Get unique powers currently available in unit table (AIR COMPRESSOR SCREW only) to make adding simple
        $rawPowers = Unit::where('unit', 'AIR COMPRESSOR SCREW')
            ->whereNotNull('power')
            ->where('power', '!=', '')
            ->where('power', '!=', '-')
            ->pluck('power');

        $availablePowers = $rawPowers->map(function($p) {
            return self::normalizePower($p);
        })->filter()->unique()->sortBy(function($p) {
            return floatval(str_replace(',', '.', $p));
        })->values();

        return view('pages.sales.forecast.prices', compact('prices', 'availablePowers', 'defaultTemplate'));
    }

    /**
     * Update/Store Jasa PM Pricing (Prices only)
     */
    public function updatePrices(Request $request)
    {
        // Clean currency inputs if formatted with dots/commas
        $request->merge([
            'price_pm1' => intval(str_replace(['.', ','], '', $request->input('price_pm1', 0))),
            'price_pm2' => intval(str_replace(['.', ','], '', $request->input('price_pm2', 0))),
            'price_pm3' => intval(str_replace(['.', ','], '', $request->input('price_pm3', 0))),
            'price_pm4' => intval(str_replace(['.', ','], '', $request->input('price_pm4', 0))),
        ]);

        $powerInput = $request->filled('custom_power') ? $request->input('custom_power') : $request->input('power');
        $request->merge(['power' => self::normalizePower($powerInput)]);

        $rules = [
            'power' => 'required|string',
            'price_pm1' => 'required|integer|min:0',
            'price_pm2' => 'required|integer|min:0',
            'price_pm3' => 'required|integer|min:0',
            'price_pm4' => 'required|integer|min:0',
        ];

        $this->validate($request, $rules);

        PowerServicePrice::updateOrCreate(
            ['power' => $request->power],
            [
                'price_pm1' => $request->price_pm1,
                'price_pm2' => $request->price_pm2,
                'price_pm3' => $request->price_pm3,
                'price_pm4' => $request->price_pm4,
            ]
        );

        self::clearForecastCache();

        return redirect()->route('forecast.prices')->with('message', 'Master harga jasa PM berhasil disimpan.');
    }

    /**
     * Update Global Standard Template for Scope & Remarks
     */
    public function updateStandardTemplate(Request $request)
    {
        $rules = [
            'desc_pm1' => 'nullable|string',
            'desc_pm2' => 'nullable|string',
            'desc_pm3' => 'nullable|string',
            'desc_pm4' => 'nullable|string',
            'note_pm1' => 'nullable|string',
            'note_pm2' => 'nullable|string',
            'note_pm3' => 'nullable|string',
            'note_pm4' => 'nullable|string',
        ];

        $this->validate($request, $rules);

        PowerServicePrice::updateOrCreate(
            ['power' => 'STANDARD_TEMPLATE'],
            [
                'price_pm1' => 0,
                'price_pm2' => 0,
                'price_pm3' => 0,
                'price_pm4' => 0,
                'desc_pm1' => $request->desc_pm1,
                'desc_pm2' => $request->desc_pm2,
                'desc_pm3' => $request->desc_pm3,
                'desc_pm4' => $request->desc_pm4,
                'note_pm1' => $request->note_pm1,
                'note_pm2' => $request->note_pm2,
                'note_pm3' => $request->note_pm3,
                'note_pm4' => $request->note_pm4,
            ]
        );

        // Sync global template scope & notes to all power records in database
        PowerServicePrice::where('power', '!=', 'STANDARD_TEMPLATE')->update([
            'desc_pm1' => $request->desc_pm1,
            'desc_pm2' => $request->desc_pm2,
            'desc_pm3' => $request->desc_pm3,
            'desc_pm4' => $request->desc_pm4,
            'note_pm1' => $request->note_pm1,
            'note_pm2' => $request->note_pm2,
            'note_pm3' => $request->note_pm3,
            'note_pm4' => $request->note_pm4,
        ]);

        return redirect()->route('forecast.prices')->with('message', 'Template standar scope & remarks quotation berhasil diperbarui.');
    }

    /**
     * Delete Jasa PM Pricing record
     */
    public function deletePrices($id)
    {
        $price = PowerServicePrice::findOrFail($id);
        $price->delete();

        self::clearForecastCache();

        return redirect()->route('forecast.prices')->with('message', 'Data harga jasa servis PM berhasil dihapus.');
    }

    /**
     * Manage Contract visit schedules
     */
    public function manageContracts(Request $request)
    {
        $user = Auth::user();
        $isAdmin = $user->role == 'Admin' || $user->role == 'Sales Manager';

        $contractsQuery = Contract::with(['quotation.pic.client', 'client', 'visitSchedules']);
        $clientsQuery = \App\Models\Client::where('role', 'Customers')->orderBy('company');

        if (!$isAdmin) {
            $contractsQuery->where(function($q) use ($user) {
                $q->whereHas('client', function($sub) use ($user) {
                    $sub->where('id_sales', $user->id);
                })->orWhereHas('quotation', function($sub) use ($user) {
                    $sub->where('id_sales', $user->id);
                });
            });
            $clientsQuery->where('id_sales', $user->id);
        }

        $contracts = $contractsQuery->get();
        $clients = $clientsQuery->get();

        return view('pages.sales.forecast.contracts', compact('contracts', 'clients'));
    }

    /**
     * Store manual contract
     */
    public function storeContract(Request $request)
    {
        $rules = [
            'no_contract' => 'required|string|unique:contract,no_contract',
            'id_client' => 'required|exists:client,id',
            'date' => 'required|date',
            'type' => 'nullable|string',
        ];

        $this->validate($request, $rules);

        Contract::create([
            'no_contract' => $request->no_contract,
            'id_client' => $request->id_client,
            'date' => $request->date,
            'type' => $request->type ?? 'Order',
            'level' => '1',
        ]);

        self::clearForecastCache();

        return redirect()->route('forecast.contracts')->with('message', 'Contract successfully created.');
    }

    /**
     * Delete a manual or existing contract
     */
    public function deleteContract($id)
    {
        $contract = Contract::findOrFail($id);
        $contract->visitSchedules()->delete();
        $contract->delete();

        self::clearForecastCache();

        return redirect()->route('forecast.contracts')->with('message', 'Contract and its scheduled visits successfully deleted.');
    }

    /**
     * Store Visit Schedule for a specific contract
     */
    public function storeContractSchedule(Request $request, $id)
    {
        $rules = [
            'visits' => 'required|array',
            'visits.*.planned_date' => 'required|date',
            'visits.*.estimated_revenue' => 'required|integer|min:0',
            'visits.*.description' => 'nullable|string',
        ];

        $this->validate($request, $rules);

        $contract = Contract::findOrFail($id);

        // Delete existing pending visit schedules to recreate
        ContractVisitSchedule::where('id_contract', $id)->where('status', 'Pending')->delete();

        foreach ($request->visits as $index => $visitData) {
            ContractVisitSchedule::create([
                'id_contract' => $contract->id,
                'visit_number' => $index + 1,
                'planned_date' => $visitData['planned_date'],
                'estimated_revenue' => $visitData['estimated_revenue'],
                'status' => 'Pending',
                'description' => $visitData['description'] ?? 'Kunjungan Kontrak Servis',
            ]);
        }

        self::clearForecastCache();

        return redirect()->route('forecast.contracts')->with('message', 'Jadwal kunjungan kontrak berhasil disimpan.');
    }

    /**
     * Normalize dirty power strings to standard 'X.Y kW'
     */
    public static function normalizePower($power)
    {
        if (empty($power) || $power == '-') {
            return '';
        }
        
        $power = strtolower($power);
        // Replace commas with dots and clean spacing
        $power = str_replace(',', '.', $power);
        $power = str_replace(' ', '', $power);
        
        // 1. Check if contains "kw"
        if (preg_match('/(\d+[\.]?\d*)kw/', $power, $matches)) {
            $val = floatval($matches[1]);
            return $val . ' kW';
        }
        
        // 2. Check for HP and calculate kW (1 HP = 0.745 kW, standard round to 1 decimal)
        if (preg_match('/(\d+[\.]?\d*)(hp|pk)/', $power, $matches)) {
            $hp = floatval($matches[1]);
            $kw = round($hp * 0.745, 1);
            return $kw . ' kW';
        }
        
        // 3. Fallback: if it's just a number
        if (preg_match('/(\d+[\.]?\d*)/', $power, $matches)) {
            return floatval($matches[1]) . ' kW';
        }
        
        return '';
    }

    /**
     * Clear all cached forecast data keys across common combinations
     */
    public static function clearForecastCache()
    {
        foreach ([null, 1, 2, 3, 4, 32] as $id) {
            foreach ([Carbon::now()->year - 1, Carbon::now()->year, Carbon::now()->year + 1] as $y) {
                foreach (['all', '1', '2'] as $s) {
                    \Illuminate\Support\Facades\Cache::forget("forecast_data_{$id}_{$y}_{$s}");
                }
            }
        }
    }
}
