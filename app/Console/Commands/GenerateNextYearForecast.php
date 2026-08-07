<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Machine;
use App\Models\ForecastHistory;
use Carbon\Carbon;

class GenerateNextYearForecast extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'forecast:generate-next-year {year?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate default PM1-PM1-PM2 forecast schedules for the next calendar year';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $year = $this->argument('year');
        if (!$year) {
            $year = date('Y') + 1; // default to next year
        }

        $this->info("Generating default forecast schedules for year: {$year}...");

        $machines = Machine::where('is_forecasted', true)
            ->whereHas('unit.unit', function($q) {
                $q->where('unit', 'AIR COMPRESSOR SCREW');
            })
            ->get();

        $count = 0;

        foreach ($machines as $machine) {
            $visits = $this->generateDefaultSchedule($machine, $year);

            // Structure visits parameters
            $visit1_type = $visits[0]['type'] ?? null;
            $visit1_date = $visits[0]['date'] ?? null;
            $visit2_type = $visits[1]['type'] ?? null;
            $visit2_date = $visits[1]['date'] ?? null;
            $visit3_type = $visits[2]['type'] ?? null;
            $visit3_date = $visits[2]['date'] ?? null;
            $visit4_type = $visits[3]['type'] ?? null;
            $visit4_date = $visits[3]['date'] ?? null;

            // 1. Save to history table
            ForecastHistory::updateOrCreate(
                ['id_machine' => $machine->id, 'year' => $year],
                [
                    'forecast_type' => $machine->forecast_type ?: 'parts',
                    'is_forecasted' => true,
                    'visit_1_type' => $visit1_type,
                    'visit_1_date' => $visit1_date,
                    'visit_2_type' => $visit2_type,
                    'visit_2_date' => $visit2_date,
                    'visit_3_type' => $visit3_type,
                    'visit_3_date' => $visit3_date,
                    'visit_4_type' => $visit4_type,
                    'visit_4_date' => $visit4_date,
                ]
            );

            // 2. If generating for next year, we can also update the active machine schedule
            if ($year == date('Y') || $year == (date('Y') + 1)) {
                $machine->update([
                    'visit_1_type' => $visit1_type,
                    'visit_1_date' => $visit1_date,
                    'visit_2_type' => $visit2_type,
                    'visit_2_date' => $visit2_date,
                    'visit_3_type' => $visit3_type,
                    'visit_3_date' => $visit3_date,
                    'visit_4_type' => $visit4_type,
                    'visit_4_date' => $visit4_date,
                ]);
            }

            $count++;
        }

        $this->info("Successfully generated default forecasts for {$count} machines in year {$year}!");
        return Command::SUCCESS;
    }

    /**
     * Generate 4-month interval PM schedule
     */
    private function generateDefaultSchedule($machine, $year)
    {
        $visits = [];
        
        // 1. Check if there is a forecast history for the previous year to carry over the PM cycle
        $prevHistory = ForecastHistory::where('id_machine', $machine->id)
            ->where('year', $year - 1)
            ->first();
            
        $prevVisits = [];
        if ($prevHistory) {
            for ($i = 1; $i <= 4; $i++) {
                $dateKey = "visit_{$i}_date";
                $typeKey = "visit_{$i}_type";
                if (!empty($prevHistory->$dateKey) && !empty($prevHistory->$typeKey)) {
                    $prevVisits[] = [
                        'date' => $prevHistory->$dateKey,
                        'type' => $prevHistory->$typeKey
                    ];
                }
            }
        }
        
        if (!empty($prevVisits)) {
            // Sort chronologically
            usort($prevVisits, function($a, $b) {
                return strcmp($a['date'], $b['date']);
            });
            
            $lastPrevVisit = end($prevVisits);
            $lastPrevDate = Carbon::parse($lastPrevVisit['date']);
            
            // Count consecutive PM1 at the end of the previous year
            $consecutivePm1 = 0;
            for ($i = count($prevVisits) - 1; $i >= 0; $i--) {
                if ($prevVisits[$i]['type'] == 'PM2') {
                    break;
                }
                if ($prevVisits[$i]['type'] == 'PM1') {
                    $consecutivePm1++;
                }
            }
            
            // Start projecting from $lastPrevDate
            $currentDate = clone $lastPrevDate;
            $projectedIndex = $consecutivePm1; 
            
            while ($currentDate->year <= $year && count($visits) < 4) {
                $currentDate->addMonths(4);
                $projectedIndex++;
                
                $type = 'PM1';
                if ($projectedIndex % 3 == 0) {
                    $type = 'PM2';
                }
                
                if ($currentDate->year == $year) {
                    $visits[] = [
                        'type' => $type,
                        'date' => $currentDate->format('Y-m-d')
                    ];
                }
            }
        }
        
        // 2. If no previous year forecast history exists, inspect actual Service Reports (reports table)
        if (empty($visits)) {
            // Get recent service reports for this machine with non-null pm_level or type 'Service'
            $recentReports = \App\Models\Reports::where('id_machine', $machine->id)
                ->where('type', 'Service')
                ->whereNotNull('date')
                ->orderBy('date', 'asc')
                ->get();

            if ($recentReports->isNotEmpty()) {
                $lastReport = $recentReports->last();
                $lastServiceDate = Carbon::parse($lastReport->date);

                // Calculate consecutive PM1 before/ending at lastReport
                $consecutivePm1 = 0;
                foreach ($recentReports as $rep) {
                    if ($rep->pm_level == 'PM2' || $rep->pm_level == 'PM3' || $rep->pm_level == 'PM4') {
                        $consecutivePm1 = 0;
                    } elseif ($rep->pm_level == 'PM1') {
                        $consecutivePm1++;
                    }
                }

                $currentDate = clone $lastServiceDate;
                $projectedIndex = $consecutivePm1;

                while ($currentDate->year <= $year && count($visits) < 4) {
                    $currentDate->addMonths(4);
                    $projectedIndex++;

                    $type = 'PM1';
                    // Every 3rd visit in the cycle is PM2 (PM1-PM1-PM2)
                    if ($projectedIndex % 3 == 0) {
                        $type = 'PM2';
                    }

                    if ($currentDate->year == $year) {
                        $visits[] = [
                            'type' => $type,
                            'date' => $currentDate->format('Y-m-d')
                        ];
                    }
                }
            }
        }

        // 3. Fallback to last_service_date column if no reports exist
        if (empty($visits) && $machine->last_service_date) {
            $lastService = Carbon::parse($machine->last_service_date);
            $currentDate = clone $lastService;
            $projectedIndex = 0;

            while ($currentDate->year <= $year && count($visits) < 4) {
                $currentDate->addMonths(4);
                $projectedIndex++;

                $type = 'PM1';
                if ($projectedIndex % 3 == 0) {
                    $type = 'PM2';
                }

                if ($currentDate->year == $year) {
                    $visits[] = [
                        'type' => $type,
                        'date' => $currentDate->format('Y-m-d')
                    ];
                }
            }
        }

        // 4. Default Fallback if no history or reports exist at all (PM1-PM1-PM2)
        if (empty($visits)) {
            $visits = [
                ['type' => 'PM1', 'date' => $year . '-03-15'],
                ['type' => 'PM1', 'date' => $year . '-07-15'],
                ['type' => 'PM2', 'date' => $year . '-11-15'],
            ];
        }
        
        return $visits;
    }
}
