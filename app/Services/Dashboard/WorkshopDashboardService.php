<?php

namespace App\Services\Dashboard;

use App\Models\FixedAsset;
use Carbon\Carbon;

class WorkshopDashboardService
{
    /**
     * Get dashboard data payload for Workshop
     */
    public function getDashboardData($notulens)
    {
        $workshopWidgets = $this->getWorkshopDashboardData();

        return array_merge(compact('notulens'), $workshopWidgets);
    }

    public function getWorkshopDashboardData(): array
    {
        // Scope: unit Fixed Asset kategori "Mesin" (unit workshop/heavy equipment).
        // "Kendaraan Kantor" sengaja tidak dihitung di sini — beda konteks (fleet kantor, bukan unit workshop).
        $workshopUnits = FixedAsset::where('type', 'Mesin')->with('unit')->get();
        $workshopTotalUnit = $workshopUnits->count();

        $statusLabels = ['OK', 'Rental', 'Service', 'Breakdown', 'Reserved'];
        $workshopStatusCounts = [];
        foreach ($statusLabels as $label) {
            $workshopStatusCounts[$label] = $workshopUnits->where('status_unit', $label)->count();
        }
        // Unit yang statusnya belum diklasifikasi Admin (null) atau sudah Sold, dikelompokkan terpisah
        // supaya tidak "hilang" dari total tapi juga tidak mengotori 5 kategori utama mockup.
        $workshopStatusOther = $workshopUnits->whereNotIn('status_unit', $statusLabels)->count();

        $workshopKondisiBaru = $workshopUnits->where('kondisi', 'Baru')->count();
        $workshopKondisiSecond = $workshopUnits->where('kondisi', 'Second')->count();

        $workshopQcChecking = $workshopUnits->where('qc_status', 'checking')->count();
        $workshopQcOk = $workshopUnits->where('qc_status', 'ok')->count();
        $workshopQcReject = $workshopUnits->where('qc_status', 'reject')->count();

        $workshopTotalNilaiAset = $workshopUnits->sum('total');

        // Unit yang perlu perhatian: sedang Service atau Breakdown
        $workshopAttentionUnits = $workshopUnits
            ->whereIn('status_unit', ['Service', 'Breakdown'])
            ->sortByDesc('updated_at')
            ->take(10)
            ->values();

        // Unit yang baru masuk (acquisition terbaru)
        $workshopRecentUnits = $workshopUnits
            ->sortByDesc('created_at')
            ->take(6)
            ->values();

        // --- Vehicle Maintenance ---
        // Data real dari fixed_asset (type='Kendaraan') + vehicle_maintenance_log.
        // Due-date per kategori diambil dari riwayat TERBARU (tanggal paling akhir) per jenis
        // (Servis / STNK & Pajak / Ganti Kaleng); kalau belum pernah dicatat, statusnya '-' (secondary).
        $today = Carbon::now();
        $statusFromDays = function (?int $days) {
            if ($days === null) {
                return ['label' => '-', 'color' => 'secondary'];
            }
            if ($days < 0) {
                return ['label' => 'Overdue', 'color' => 'danger'];
            }
            if ($days <= 14) {
                return ['label' => 'Due Soon', 'color' => 'warning'];
            }
            return ['label' => 'OK', 'color' => 'success'];
        };

        $vehicleAssets = FixedAsset::where('type', 'Kendaraan')->with('maintenanceLogs')->get();

        $daysUntil = function ($log) use ($today) {
            if (!$log || !$log->tanggal_jatuh_tempo) {
                return null;
            }
            return (int) $today->diffInDays(Carbon::parse($log->tanggal_jatuh_tempo), false);
        };

        $workshopVehicles = $vehicleAssets->map(function ($fixed) use ($daysUntil, $statusFromDays) {
            $servisLog = $fixed->maintenanceLogs->where('jenis', 'Servis')->sortByDesc('tanggal')->first();
            $pajakLog = $fixed->maintenanceLogs->where('jenis', 'STNK & Pajak')->sortByDesc('tanggal')->first();
            $kalengLog = $fixed->maintenanceLogs->where('jenis', 'Ganti Kaleng')->sortByDesc('tanggal')->first();

            $servisStatus = $statusFromDays($daysUntil($servisLog));
            $pajakStatus = $statusFromDays($daysUntil($pajakLog));
            $kalengStatus = $statusFromDays($daysUntil($kalengLog));

            // Status keseluruhan = yang paling genting di antara ketiganya
            $rank = ['danger' => 3, 'warning' => 2, 'success' => 1, 'secondary' => 0];
            $overall = collect([$servisStatus, $pajakStatus, $kalengStatus])->sortByDesc(fn ($s) => $rank[$s['color']])->first();

            return (object) [
                'plat' => $fixed->plat_nomor ?: '-',
                'jenis' => trim(($fixed->jenis_kendaraan ?: '') . ' ' . ($fixed->merk_model ?: '')) ?: $fixed->code,
                'servis_terakhir' => $servisLog ? Carbon::parse($servisLog->tanggal) : null,
                'servis_berikutnya' => $servisLog && $servisLog->tanggal_jatuh_tempo ? Carbon::parse($servisLog->tanggal_jatuh_tempo) : null,
                'servis_status' => $servisStatus,
                'pajak_due' => $pajakLog && $pajakLog->tanggal_jatuh_tempo ? Carbon::parse($pajakLog->tanggal_jatuh_tempo) : null,
                'pajak_status' => $pajakStatus,
                'ganti_kaleng_due' => $kalengLog && $kalengLog->tanggal_jatuh_tempo ? Carbon::parse($kalengLog->tanggal_jatuh_tempo) : null,
                'ganti_kaleng_status' => $kalengStatus,
                'overall_status' => $overall,
            ];
        });

        $workshopVehicleTotal = $workshopVehicles->count();
        $workshopVehicleServisDue = $workshopVehicles->filter(fn ($v) => $v->servis_status['color'] !== 'success')->count();
        $workshopVehiclePajakDue = $workshopVehicles->filter(fn ($v) => $v->pajak_status['color'] !== 'success')->count();
        $workshopVehicleKalengDue = $workshopVehicles->filter(fn ($v) => $v->ganti_kaleng_status['color'] !== 'success')->count();
        $workshopVehicleOverdueCount = $workshopVehicles->filter(fn ($v) => $v->overall_status['color'] === 'danger')->count();

        // Klasifikasi 1 kendaraan = 1 kategori paling genting, buat donut Overview (total harus pas = $workshopVehicleTotal)
        $rank = ['danger' => 3, 'warning' => 2, 'success' => 1, 'secondary' => 0];
        $workshopVehicleOverviewCounts = ['Ready' => 0, 'Servis Due' => 0, 'STNK/Pajak Due' => 0, 'Ganti Kaleng Due' => 0];
        foreach ($workshopVehicles as $v) {
            $candidates = [
                'Servis Due' => $v->servis_status,
                'STNK/Pajak Due' => $v->pajak_status,
                'Ganti Kaleng Due' => $v->ganti_kaleng_status,
            ];
            $mostUrgentKey = collect($candidates)->sortByDesc(fn ($s) => $rank[$s['color']])->keys()->first();
            $mostUrgent = $candidates[$mostUrgentKey];
            $workshopVehicleOverviewCounts[$mostUrgent['color'] === 'success' ? 'Ready' : $mostUrgentKey]++;
        }

        return compact(
            'workshopTotalUnit',
            'workshopStatusCounts',
            'workshopStatusOther',
            'workshopKondisiBaru',
            'workshopKondisiSecond',
            'workshopQcChecking',
            'workshopQcOk',
            'workshopQcReject',
            'workshopTotalNilaiAset',
            'workshopAttentionUnits',
            'workshopVehicles',
            'workshopVehicleTotal',
            'workshopVehicleServisDue',
            'workshopVehiclePajakDue',
            'workshopVehicleKalengDue',
            'workshopVehicleOverdueCount',
            'workshopVehicleOverviewCounts',
            'workshopRecentUnits',
        );
    }
}
