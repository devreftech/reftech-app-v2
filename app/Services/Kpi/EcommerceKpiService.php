<?php

namespace App\Services\Kpi;

use App\Models\EcommerceKpiAssignment;
use App\Models\EcommerceKpiAssignmentItem;
use App\Models\EcommerceKpiPeriod;
use App\Models\EcommerceKpiTemplate;
use App\Models\Quotation;
use App\Models\SalesOnline;
use App\Models\SalesTargetHistory;
use App\Models\Target;
use App\Models\UnitQuotation;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class EcommerceKpiService
{
    /**
     * Dapatkan daftar user yang bertipe E-Commerce / Online secara dinamis.
     * Mengambil dari:
     * 1. SalesTargetHistory dengan sales_type = 'ecommerce' atau 'online' yang aktif dalam roster
     * 2. User aktif dengan area 'Online' / 'Ecommerce' pada detail_users
     * 3. User yang memiliki data aktivitas di tabel sales_online
     */
    public function getEcommerceUsers(int $year): array
    {
        // 1. Dari Roster Sales Target
        $rosterUserIds = SalesTargetHistory::where('year', $year)
            ->whereIn('sales_type', ['ecommerce', 'online'])
            ->where('is_active_roster', 1)
            ->pluck('user_id')
            ->toArray();

        // 2. Dari User dengan area 'Online' / 'Ecommerce' di DetailUser
        $areaUserIds = User::where('active', '1')
            ->whereHas('detailUser', function ($q) {
                $q->where('area', 'LIKE', '%online%')
                  ->orWhere('area', 'LIKE', '%ecommerce%');
            })
            ->pluck('id')
            ->toArray();

        // 3. Dari User yang memiliki aktivitas di sales_online pada tahun tersebut
        $salesOnlineUserIds = SalesOnline::whereYear('date', $year)
            ->distinct()
            ->pluck('id_sales')
            ->toArray();

        // Gabungkan seluruh ID yang unik
        $combinedIds = array_unique(array_merge($rosterUserIds, $areaUserIds, $salesOnlineUserIds));

        if (empty($combinedIds)) {
            // Fallback ke semua user dengan role E-Commerce jika ada
            $combinedIds = User::whereIn('role', ['E-Commerce', 'Ecommerce'])
                ->where('active', '1')
                ->pluck('id')
                ->toArray();
        }

        return User::whereIn('id', $combinedIds)
            ->where('active', '1')
            ->orderBy('name')
            ->get()
            ->all();
    }

    /**
     * Inisialisasi atau buat assignment KPI untuk seluruh tim E-Commerce pada periode tertentu.
     */
    public function initializePeriodAssignments(EcommerceKpiPeriod $period, ?int $evaluatorId = null): array
    {
        $users = $this->getEcommerceUsers($period->year);
        $templates = EcommerceKpiTemplate::where('is_active', true)->orderBy('sort_order')->get();
        $assignments = [];

        foreach ($users as $user) {
            $assignment = EcommerceKpiAssignment::firstOrCreate(
                [
                    'period_id' => $period->id,
                    'user_id' => $user->id,
                ],
                [
                    'evaluator_id' => $evaluatorId,
                    'status' => 'draft',
                ]
            );

            // Jika item belum pernah dibuat, clone dari template
            if ($assignment->items()->count() === 0) {
                // Cari custom target revenue bulanan dari tabel target jika ada
                $monthlyTarget = $this->resolveMonthlyRevenueTarget($user->id, $period->year, $period->month);

                foreach ($templates as $tmpl) {
                    $target = $tmpl->default_target;

                    // Override target revenue jika ada target di sistem
                    if ($tmpl->calculation_handler === 'revenue' && $monthlyTarget > 0) {
                        $target = $monthlyTarget;
                    }

                    EcommerceKpiAssignmentItem::create([
                        'assignment_id' => $assignment->id,
                        'template_id' => $tmpl->id,
                        'kpi_code' => $tmpl->code,
                        'kpi_name' => $tmpl->name,
                        'kpi_type' => $tmpl->type,
                        'unit' => $tmpl->unit,
                        'calculation_handler' => $tmpl->calculation_handler,
                        'target' => $target,
                        'actual_system' => null,
                        'actual_final' => 0,
                        'achievement_rate' => 0,
                        'weight' => $tmpl->default_weight,
                        'score' => 0,
                        'sort_order' => $tmpl->sort_order,
                    ]);
                }
            }

            // Sync actual data dari database
            $this->syncSystemActuals($assignment);
            $assignments[] = $assignment->fresh(['items', 'user', 'period']);
        }

        return $assignments;
    }

    /**
     * Cari target revenue bulanan dari model Target atau SalesTargetHistory.
     */
    protected function resolveMonthlyRevenueTarget(int $userId, int $year, int $month): float
    {
        // 1. Coba dari SalesTargetHistory tahun terkait (annual / 12)
        $annualRow = SalesTargetHistory::where('user_id', $userId)->where('year', $year)->first();
        if ($annualRow && $annualRow->target_annual > 0) {
            return round($annualRow->target_annual / 12, 2);
        }

        // 2. Coba dari tabel target kolom total
        $targetRow = Target::where('id_sales', $userId)->first();
        if ($targetRow && $targetRow->total > 0) {
            return (float) $targetRow->total;
        }

        return 100000000.00;
    }

    /**
     * Hitung dan sinkronkan seluruh metrik sistem untuk assignment KPI.
     */
    public function syncSystemActuals(EcommerceKpiAssignment $assignment): void
    {
        $period = $assignment->period;
        $year = $period->year;
        $month = $period->month;
        $userId = $assignment->user_id;

        $items = $assignment->items;

        foreach ($items as $item) {
            $systemValue = null;

            if ($item->calculation_handler) {
                $systemValue = $this->calculateHandlerValue($item->calculation_handler, $userId, $year, $month);
            }

            $item->actual_system = $systemValue;

            // Jika status masih draft atau nilai final belum diubah evaluator, samakan aktual final dengan sistem
            if ($assignment->status === 'draft' || $item->kpi_type === 'automatic') {
                $item->actual_final = $systemValue ?? $item->actual_final;
            }

            // Hitung pencapaian & skor
            $item->achievement_rate = $this->calculateAchievementRate($item->target, $item->actual_final);
            $item->score = $this->calculateScore($item->achievement_rate, $item->weight);
            $item->save();
        }

        $this->recalculateAssignmentTotals($assignment);
    }

    /**
     * Eksekusi query penghitungan sesuai handler metrik.
     */
    public function calculateHandlerValue(string $handler, int $userId, int $year, int $month): ?float
    {
        switch ($handler) {
            case 'revenue':
                // Revenue dari Quotation (sparepart/service)
                $poNett = Quotation::where('id_sales', $userId)
                    ->whereYear('po_date', $year)
                    ->whereMonth('po_date', $month)
                    ->where('status', '100')
                    ->where('is_primary', '1')
                    ->sum('nett');

                // Revenue dari Unit Quotation (mesin/unit)
                $unitNett = UnitQuotation::where('id_sales', $userId)
                    ->where('status', 'po_received')
                    ->where('is_latest', 1)
                    ->whereYear('po_received', $year)
                    ->whereMonth('po_received', $month)
                    ->sum(DB::raw('total - IFNULL(tax_amount, 0) - IFNULL(fee, 0)'));

                return (float) ($poNett + $unitNett);

            case 'product_upload':
                return (float) SalesOnline::where('id_sales', $userId)
                    ->where('type', 'Product')
                    ->whereYear('date', $year)
                    ->whereMonth('date', $month)
                    ->count();

            case 'po_count':
                $quoteCount = Quotation::where('id_sales', $userId)
                    ->whereYear('po_date', $year)
                    ->whereMonth('po_date', $month)
                    ->where('status', '100')
                    ->where('is_primary', '1')
                    ->count();

                $unitCount = UnitQuotation::where('id_sales', $userId)
                    ->where('status', 'po_received')
                    ->where('is_latest', 1)
                    ->whereYear('po_received', $year)
                    ->whereMonth('po_received', $month)
                    ->count();

                return (float) ($quoteCount + $unitCount);

            case 'sw_update':
                $records = SalesOnline::where('id_sales', $userId)
                    ->where('type', 'SW')
                    ->whereYear('date', $year)
                    ->whereMonth('date', $month)
                    ->get();

                $total = 0;
                foreach ($records as $r) {
                    $total += (float) ($r->airend ?? 0) + (float) ($r->kojisha ?? 0);
                }
                return (float) $total;

            case 'akurasi':
                $avg = SalesOnline::where('id_sales', $userId)
                    ->where('type', 'Akurasi')
                    ->whereYear('date', $year)
                    ->whereMonth('date', $month)
                    ->avg('average');

                return $avg ? round(((float) $avg / 5) * 100, 2) : 0.00;

            case 'response':
                $avg = SalesOnline::where('id_sales', $userId)
                    ->where('type', 'Response')
                    ->whereYear('date', $year)
                    ->whereMonth('date', $month)
                    ->avg('average');

                return $avg ? round((float) $avg, 2) : 0.00;

            case 'delivery':
                $avg = SalesOnline::where('id_sales', $userId)
                    ->where('type', 'Delivery')
                    ->whereYear('date', $year)
                    ->whereMonth('date', $month)
                    ->avg('average');

                return $avg ? round(((float) $avg / 5) * 100, 2) : 0.00;

            case 'rating':
                $avg = SalesOnline::where('id_sales', $userId)
                    ->where('type', 'Rating')
                    ->whereYear('date', $year)
                    ->whereMonth('date', $month)
                    ->avg('average');

                return $avg ? round((float) $avg, 2) : 0.00;

            case 'customer':
                $avg = SalesOnline::where('id_sales', $userId)
                    ->where('type', 'Customer')
                    ->whereYear('date', $year)
                    ->whereMonth('date', $month)
                    ->avg('average');

                return $avg ? round(((float) $avg / 5) * 100, 2) : 0.00;

            case 'video':
                $videos = SalesOnline::where('id_sales', $userId)
                    ->where('type', 'Video')
                    ->whereYear('date', $year)
                    ->whereMonth('date', $month)
                    ->get();

                if ($videos->isEmpty()) {
                    return 0.00;
                }

                $scoreSum = 0;
                foreach ($videos as $v) {
                    if (!empty($v->ig)) $scoreSum += 30;
                    if (!empty($v->tiktok)) $scoreSum += 30;
                    if (!empty($v->tokped)) $scoreSum += 30;
                }

                return round($scoreSum / $videos->count(), 2);

            case 'manual':
            default:
                return null;
        }
    }

    /**
     * Hitung persentase pencapaian (Actual / Target * 100).
     * Diberikan capping 100% secara default agar kontribusi skor tidak melebihi bobot.
     */
    public function calculateAchievementRate(float $target, float $actual, bool $capAt100 = true): float
    {
        if ($target <= 0) {
            return 0.00;
        }

        $pct = ($actual / $target) * 100;
        if ($capAt100 && $pct > 100.00) {
            $pct = 100.00;
        }

        return round(max(0.00, $pct), 2);
    }

    /**
     * Hitung kontribusi skor (Achievement Rate * Weight / 100).
     */
    public function calculateScore(float $achievementRate, float $weight): float
    {
        return round(($achievementRate * $weight) / 100, 2);
    }

    /**
     * Hitung ulang total skor assignment dan tentukan predikat grade.
     */
    public function recalculateAssignmentTotals(EcommerceKpiAssignment $assignment): void
    {
        $totalScore = $assignment->items()->sum('score');
        $totalScore = round($totalScore, 2);

        $grade = $this->determineGrade($totalScore);

        $assignment->total_score = $totalScore;
        $assignment->grade = $grade;
        $assignment->save();
    }

    /**
     * Konversi total skor menjadi Grade (A/B/C/D).
     */
    public function determineGrade(float $score): string
    {
        if ($score >= 90) return 'A';
        if ($score >= 80) return 'B';
        if ($score >= 70) return 'C';
        return 'D';
    }
}
