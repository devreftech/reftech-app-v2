<?php

namespace App\Services;

use App\Models\FixedAsset;
use App\Models\ToolAudit;
use App\Models\ToolAuditItem;
use App\Models\ToolAuditPeriod;
use App\Models\User;
use Carbon\Carbon;

class ToolAuditPeriodGenerator
{
    /**
     * Window audit: 10 hari terakhir pada bulan Maret (Q1), Juni (Q2), September (Q3), & Desember (Q4).
     * Return null kalau tanggal hari ini di luar keempat window itu DAN gak ada
     * triwulan lewat yang butuh catch-up (lihat missedWindowCatchUp()).
     */
    public function activeWindow(?Carbon $date = null)
    {
        $date = $date ?? Carbon::today();
        $tahun = $date->year;

        $windows = [
            1 => Carbon::create($tahun, 3, 1)->endOfMonth(),  // Q1: Akhir Maret
            2 => Carbon::create($tahun, 6, 1)->endOfMonth(),  // Q2: Akhir Juni
            3 => Carbon::create($tahun, 9, 1)->endOfMonth(),  // Q3: Akhir September
            4 => Carbon::create($tahun, 12, 1)->endOfMonth(), // Q4: Akhir Desember
        ];

        foreach ($windows as $semester => $endOfMonth) {
            $start = $endOfMonth->copy()->subDays(9)->startOfDay();
            $end = $endOfMonth->copy()->endOfDay();
            if ($date->between($start, $end)) {
                return [
                    'tahun' => $tahun,
                    'semester' => $semester,
                    'tanggal_mulai' => $start->toDateString(),
                    'tanggal_selesai' => $end->toDateString(),
                ];
            }
        }

        return $this->missedWindowCatchUp($date);
    }

    /**
     * Fallback sementara: kalau window resmi sudah kelewat (mis. cron/lazy-trigger
     * gak sempat jalan pas H-9 s/d akhir bulan), tetap anggap triwulan yang baru
     * lewat itu "terbuka utk catch-up" — SELALU dicek ulang tiap kali dipanggil
     * (bukan cuma sekali), soalnya generateIfNeeded() pakai firstOrCreate yang
     * idempotent, jadi sweep berulang ini aman & perlu supaya teknisi yang baru
     * dapat tools SETELAH catch-up pertama tetap ke-sweep juga (bukan cuma
     * technician yang aktif pas trigger pertama). Cuma lihat ke belakang
     * (triwulan yang sudah lewat), gak pernah buka triwulan yang belum waktunya.
     */
    protected function missedWindowCatchUp(Carbon $date)
    {
        $tahun = $date->year;

        $candidates = [
            ['tahun' => $tahun, 'semester' => 1, 'end' => Carbon::create($tahun, 3, 1)->endOfMonth()],
            ['tahun' => $tahun, 'semester' => 2, 'end' => Carbon::create($tahun, 6, 1)->endOfMonth()],
            ['tahun' => $tahun, 'semester' => 3, 'end' => Carbon::create($tahun, 9, 1)->endOfMonth()],
            ['tahun' => $tahun, 'semester' => 4, 'end' => Carbon::create($tahun, 12, 1)->endOfMonth()],
            ['tahun' => $tahun - 1, 'semester' => 4, 'end' => Carbon::create($tahun - 1, 12, 1)->endOfMonth()],
        ];

        $candidates = array_filter($candidates, fn ($c) => $c['end']->lt($date));
        usort($candidates, fn ($a, $b) => $b['end'] <=> $a['end']);
        $nearest = $candidates[0] ?? null;

        if (!$nearest) {
            return null;
        }

        $end = $nearest['end'];
        $start = $end->copy()->subDays(9)->startOfDay();

        return [
            'tahun' => $nearest['tahun'],
            'semester' => $nearest['semester'],
            'tanggal_mulai' => $start->toDateString(),
            'tanggal_selesai' => $end->copy()->endOfDay()->toDateString(),
        ];
    }

    /**
     * Generate tool_audit (Draft) + tool_audit_item untuk semua teknisi yang memegang tools aktif
     * pada periode tertentu. Idempotent.
     */
    public function generateForPeriod(ToolAuditPeriod $period): array
    {
        $romawiMap = [1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV'];
        $romawi = $romawiMap[$period->semester] ?? (string) $period->semester;

        // Ambil semua user/teknisi yang memegang tools aktif
        $picIds = FixedAsset::where('type', 'Tools')
            ->where('status_tools', 'Aktif')
            ->whereNotNull('id_pic')
            ->pluck('id_pic')
            ->unique()
            ->toArray();

        $technicians = User::whereIn('id', $picIds)->get();

        $countTechnicians = 0;
        $countTools = 0;

        foreach ($technicians as $technician) {
            $activeTools = FixedAsset::where('type', 'Tools')
                ->where('id_pic', $technician->id)
                ->where('status_tools', 'Aktif')
                ->get();

            if ($activeTools->isEmpty()) {
                continue;
            }

            $audit = ToolAudit::firstOrCreate(
                ['id_audit_period' => $period->id, 'id_technician' => $technician->id],
                [
                    'no_audit' => ($technician->code ?? $technician->id) . '/' . $romawi . '/' . $period->tahun,
                    'status_submit' => 'Draft',
                    'total_tools' => $activeTools->count(),
                ]
            );

            // Update total_tools jika ada penambahan tools baru
            if ($audit->status_submit == 'Draft') {
                $audit->total_tools = $activeTools->count();
                $audit->save();
            }

            foreach ($activeTools as $tool) {
                ToolAuditItem::firstOrCreate(
                    ['id_tool_audit' => $audit->id, 'id_fixed_asset' => $tool->id],
                    ['qty_actual' => $tool->qty]
                );
                $countTools++;
            }

            $countTechnicians++;
        }

        return [
            'technicians' => $countTechnicians,
            'tools' => $countTools,
            'period' => $period,
        ];
    }

    /**
     * Idempotent — aman dipanggil berkali-kali (dari cron ataupun lazy trigger
     * saat teknisi/admin buka halaman Audit Tools).
     */
    public function generateIfNeeded(?Carbon $date = null): ?ToolAuditPeriod
    {
        $targetDate = $date ?? Carbon::today();
        $dateStr = $targetDate->toDateString();

        // 1. Prioritaskan periode aktif yang berstatus 'Open' di database
        $openPeriod = ToolAuditPeriod::where('status', 'Open')
            ->where('tanggal_mulai', '<=', $dateStr)
            ->where('tanggal_selesai', '>=', $dateStr)
            ->latest('id')
            ->first();

        if ($openPeriod) {
            $this->generateForPeriod($openPeriod);
            return $openPeriod;
        }

        // 2. Jika tidak ada periode custom aktif, cek window otomatis (Q1-Q4 / Catch-up)
        $window = $this->activeWindow($targetDate);
        if (!$window) {
            return null;
        }

        $period = ToolAuditPeriod::firstOrCreate(
            ['tahun' => $window['tahun'], 'semester' => $window['semester']],
            [
                'tanggal_mulai' => $window['tanggal_mulai'],
                'tanggal_selesai' => $window['tanggal_selesai'],
                'status' => 'Open',
            ]
        );

        $this->generateForPeriod($period);

        return $period;
    }
}
