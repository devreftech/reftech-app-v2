<?php

namespace App\Services\Hvac;

use App\Models\Hvac\HvacAcCatalog;

class AcRecommendationService
{
    /**
     * Recommend AC unit(s) based on design cooling load (BTU/h)
     */
    public function recommend(float $designLoadBtuh, ?string $preferredType = null, ?string $preferredBrand = null): array
    {
        if ($designLoadBtuh <= 0) {
            return [
                'model'          => null,
                'ac_id'          => null,
                'capacity_btuh'  => 0,
                'capacity_pk'    => 0,
                'unit_qty'       => 1,
                'notes'          => 'Beban pendinginan 0 BTU/h',
                'multi_options'  => [],
            ];
        }

        $query = HvacAcCatalog::where('is_active', true);

        if ($preferredType) {
            $query->where('ac_type', $preferredType);
        }
        if ($preferredBrand) {
            $query->where('brand', $preferredBrand);
        }

        $units = $query->orderBy('cooling_capacity_btuh', 'asc')->get();

        // 1. Single Unit Recommendation: find first unit >= designLoadBtuh * 0.95
        $matchedUnit = $units->first(function ($unit) use ($designLoadBtuh) {
            return (float) $unit->cooling_capacity_btuh >= ($designLoadBtuh * 0.95);
        });

        // If load is very large and exceeds largest single unit in DB, pick largest and scale qty
        if (!$matchedUnit && $units->isNotEmpty()) {
            $largest = $units->last();
            $qty = (int) ceil($designLoadBtuh / (float) $largest->cooling_capacity_btuh);
            return [
                'model'          => $largest->model_name,
                'ac_id'          => $largest->id,
                'capacity_btuh'  => (float) $largest->cooling_capacity_btuh,
                'capacity_pk'    => (float) $largest->nominal_pk,
                'unit_qty'       => max(1, $qty),
                'notes'          => "Beban {$designLoadBtuh} BTU/h melebihi kapasitas 1 unit terbesar. Direkomendasikan {$qty}x unit {$largest->brand} {$largest->model_name} ({$largest->nominal_pk} PK).",
                'multi_options'  => [],
            ];
        }

        if (!$matchedUnit) {
            // Fallback nominal estimation: 9000 BTU/h per PK
            $estPk = round($designLoadBtuh / 9000.0, 1);
            return [
                'model'          => "AC Commercial {$estPk} PK",
                'ac_id'          => null,
                'capacity_btuh'  => round($estPk * 9000.0),
                'capacity_pk'    => $estPk,
                'unit_qty'       => 1,
                'notes'          => "Estimasi kapasitas {$estPk} PK (" . number_format($designLoadBtuh) . " BTU/h)",
                'multi_options'  => [],
            ];
        }

        // Multi-split / multi-unit alternative (useful for rooms > 24,000 BTU/h)
        $multiOptions = [];
        if ($designLoadBtuh >= 24000.0) {
            // Option with 2 units
            $targetPerUnit = $designLoadBtuh / 2.0;
            $halfUnit = $units->first(function ($u) use ($targetPerUnit) {
                return (float) $u->cooling_capacity_btuh >= ($targetPerUnit * 0.95);
            });
            if ($halfUnit) {
                $multiOptions[] = [
                    'label'         => "2 Unit x {$halfUnit->brand} {$halfUnit->model_name} ({$halfUnit->nominal_pk} PK)",
                    'unit_qty'      => 2,
                    'unit_capacity' => (float) $halfUnit->cooling_capacity_btuh,
                    'total_capacity'=> (float) $halfUnit->cooling_capacity_btuh * 2,
                ];
            }
        }

        $marginPercent = round((((float) $matchedUnit->cooling_capacity_btuh - $designLoadBtuh) / $designLoadBtuh) * 100, 1);

        return [
            'model'          => "{$matchedUnit->brand} {$matchedUnit->model_name}",
            'ac_id'          => $matchedUnit->id,
            'capacity_btuh'  => (float) $matchedUnit->cooling_capacity_btuh,
            'capacity_pk'    => (float) $matchedUnit->nominal_pk,
            'unit_qty'       => 1,
            'notes'          => "Direkomendasikan 1 unit {$matchedUnit->brand} {$matchedUnit->model_name} ({$matchedUnit->nominal_pk} PK, " . number_format($matchedUnit->cooling_capacity_btuh) . " BTU/h) dengan cadangan kapasitas {$marginPercent}%.",
            'multi_options'  => $multiOptions,
        ];
    }
}
