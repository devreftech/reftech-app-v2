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
     * Window audit: 10 hari terakhir bulan Juni (semester 1) & Desember (semester 2).
     * Return null kalau tanggal hari ini di luar kedua window itu DAN gak ada
     * semester lewat yang butuh catch-up (lihat missedWindowCatchUp()).
     */
    public function activeWindow(?Carbon $date = null)
    {
        $date = $date ?? Carbon::today();
        $tahun = $date->year;

        $windows = [
            1 => Carbon::create($tahun, 6, 1)->endOfMonth(),
            2 => Carbon::create($tahun, 12, 1)->endOfMonth(),
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
     * gak sempat jalan pas H-9 s/d akhir bulan), tetap anggap semester yang baru
     * lewat itu "terbuka utk catch-up" — SELALU dicek ulang tiap kali dipanggil
     * (bukan cuma sekali), soalnya generateIfNeeded() pakai firstOrCreate yang
     * idempotent, jadi sweep berulang ini aman & perlu supaya teknisi yang baru
     * dapat tools SETELAH catch-up pertama tetap ke-sweep juga (bukan cuma
     * technician yang aktif pas trigger pertama). Cuma lihat ke belakang
     * (semester yang sudah lewat), gak pernah buka semester yang belum waktunya.
     */
    protected function missedWindowCatchUp(Carbon $date)
    {
        $tahun = $date->year;

        $candidates = [
            ['tahun' => $tahun, 'semester' => 1, 'end' => Carbon::create($tahun, 6, 1)->endOfMonth()],
            ['tahun' => $tahun, 'semester' => 2, 'end' => Carbon::create($tahun, 12, 1)->endOfMonth()],
            ['tahun' => $tahun - 1, 'semester' => 2, 'end' => Carbon::create($tahun - 1, 12, 1)->endOfMonth()],
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
     * Idempotent — aman dipanggil berkali-kali (dari cron ataupun lazy trigger
     * saat teknisi/admin buka halaman Audit Tools). Bikin tool_audit_period +
     * tool_audit (Draft) + tool_audit_item kalau belum ada, untuk tiap teknisi
     * yang punya minimal 1 tools Aktif.
     */
    public function generateIfNeeded(?Carbon $date = null): ?ToolAuditPeriod
    {
        $window = $this->activeWindow($date);
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

        $romawi = $window['semester'] == 1 ? 'I' : 'II';
        $technicians = User::where('role', 'Technician')->get();

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
                    'no_audit' => ($technician->code ?? $technician->id) . '/' . $romawi . '/' . $window['tahun'],
                    'status_submit' => 'Draft',
                    'total_tools' => $activeTools->count(),
                ]
            );

            foreach ($activeTools as $tool) {
                ToolAuditItem::firstOrCreate(
                    ['id_tool_audit' => $audit->id, 'id_fixed_asset' => $tool->id],
                    ['qty_actual' => $tool->qty]
                );
            }
        }

        return $period;
    }
}
