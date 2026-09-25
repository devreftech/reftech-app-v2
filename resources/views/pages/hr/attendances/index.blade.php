@extends('layouts.sales.app')
@section('title', 'Presensi & Kehadiran Karyawan - HRM')
@section('hide-chat', 'true')

@push('after-style')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<style>
    .att-modern-root {
        font-family: 'Inter', system-ui, -apple-system, sans-serif !important;
        letter-spacing: -0.01em;
    }
    .att-modern-root h1, .att-modern-root h2, .att-modern-root h3, .att-modern-root h4, .att-modern-root h5, .att-modern-root h6 {
        font-family: 'Inter', system-ui, -apple-system, sans-serif !important;
        letter-spacing: -0.02em;
    }

    /* ── KPI Stat Cards ─────────────────────────────────────── */
    .att-kpi-card {
        border-radius: 16px;
        transition: transform 0.2s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.2s ease;
        border: 1px solid rgba(226, 232, 240, 0.8) !important;
        background: #ffffff;
    }
    .att-kpi-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.03) !important;
    }
    .att-icon-box {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    /* ── Podium Cards ───────────────────────────────────────── */
    .podium-card {
        border-radius: 16px;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        background: #ffffff;
    }
    .podium-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 28px rgba(0,0,0,0.06);
    }

    /* ── Table Styling ──────────────────────────────────────── */
    .att-table-wrapper {
        border-radius: 16px;
        overflow: hidden;
        border: 1px solid rgba(226, 232, 240, 0.8);
        background: #ffffff;
    }
    .att-table thead th {
        background-color: #f8fafc;
        color: #475569;
        font-weight: 700;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        padding: 0.95rem 1rem;
        border-bottom: 1px solid #e2e8f0;
    }
    .att-table tbody td {
        padding: 0.9rem 1rem;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.85rem;
    }
    .att-table tbody tr:hover td {
        background-color: #f8fafc !important;
    }

    /* ── Filter Bar ─────────────────────────────────────────── */
    .att-filter-card {
        border-radius: 16px;
        border: 1px solid rgba(226, 232, 240, 0.8);
        background: #ffffff;
    }

    .font-10 { font-size: 10px !important; }
    .font-11 { font-size: 11px !important; }
    .font-12 { font-size: 12px !important; }
    .font-13 { font-size: 13px !important; }
    .font-14 { font-size: 14px !important; }
    .font-15 { font-size: 15px !important; }
</style>
@endpush

@section('content')
<div class="att-modern-root">
    {{-- ── 1. HEADER & ACTION TOOLBAR ─────────────────────────────────────── --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 text-muted small">
                    <li class="breadcrumb-item">
                        <a href="{{ route('employees.index') }}" class="text-muted text-decoration-none">
                            <i class="mdi mdi-account-group-outline me-1"></i>HR Management
                        </a>
                    </li>
                    <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">Presensi &amp; Kehadiran</li>
                </ol>
            </nav>
            <div class="d-flex align-items-center flex-wrap gap-2">
                <h4 class="fw-bold mb-0 text-dark">Presensi &amp; Waktu Kerja</h4>
                @if ($filterType === 'monthly')
                    <span class="badge bg-label-primary rounded-pill px-3 py-1 font-12 fw-semibold">
                        <i class="mdi mdi-calendar-month me-1"></i>{{ \Carbon\Carbon::create($selectedYear, $selectedMonthNum, 1)->translatedFormat('F Y') }}
                    </span>
                @else
                    <span class="badge bg-label-primary rounded-pill px-3 py-1 font-12 fw-semibold">
                        <i class="mdi mdi-calendar-today me-1"></i>{{ \Carbon\Carbon::parse($selectedDate)->translatedFormat('d F Y') }}
                    </span>
                @endif
            </div>
        </div>

        <div class="d-flex align-items-center gap-2 flex-wrap">
            {{-- Tombol Menuju Halaman Rekap Denda Keterlambatan --}}
            <a href="{{ route('hr.attendances.penalties') }}" class="btn btn-sm btn-outline-danger rounded-pill px-3 py-2 shadow-xs d-flex align-items-center gap-1.5 fw-bold" title="Buka Halaman Rekap &amp; Akumulasi Denda Keterlambatan">
                <i class="mdi mdi-cash-minus fs-5 text-danger"></i>
                <span>Rekap Denda</span>
                @if ($totalMonthlyPenaltyAccumulated > 0)
                    <span class="badge bg-danger rounded-pill font-11 ms-1">Rp {{ number_format($totalMonthlyPenaltyAccumulated, 0, ',', '.') }}</span>
                @endif
            </a>

            {{-- Tombol Menuju Halaman Pengaturan Presensi --}}
            <a href="{{ route('hr.attendances.settings') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-2 shadow-xs d-flex align-items-center gap-1.5 fw-semibold" title="Pengaturan Presensi, Anti-Fraud &amp; Kebijakan Denda">
                <i class="mdi mdi-cog-outline fs-5"></i>
                <span>Pengaturan</span>
            </a>

            {{-- Tombol Input Manual --}}
            <button type="button" class="btn btn-sm btn-primary rounded-pill px-3.5 py-2 shadow-xs d-flex align-items-center gap-1.5 fw-bold" data-bs-toggle="modal" data-bs-target="#modalManualAttendance">
                <i class="mdi mdi-plus-circle-outline fs-5"></i>
                <span>Input Presensi</span>
            </button>

            <a href="{{ route('employees.index') }}" class="btn btn-sm btn-label-secondary rounded-pill px-3 py-2 shadow-xs d-flex align-items-center gap-1.5">
                <i class="mdi mdi-arrow-left fs-5"></i>
                <span>Hub Karyawan</span>
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-xs border-0 mb-4 rounded-3 d-flex align-items-center" role="alert">
            <i class="mdi mdi-check-circle-outline fs-5 me-2 text-success"></i>
            <div>{{ session('success') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- ── 2. KPI STAT CARDS ─────────────────────────────────────────────── --}}
    <div class="row g-3 mb-4">
        {{-- Card 1: Hadir Tepat Waktu --}}
        <div class="col-6 col-md">
            <div class="card att-kpi-card shadow-xs h-100 p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted font-11 text-uppercase fw-bold" style="letter-spacing: 0.5px;">Tepat Waktu</span>
                    <div class="att-icon-box bg-label-success">
                        <i class="mdi mdi-check-decagram-outline text-success fs-5"></i>
                    </div>
                </div>
                <div>
                    <h3 class="fw-bolder text-dark mb-0 font-monospace">{{ max(0, $stats['total_present'] - $stats['total_late']) }}</h3>
                    <span class="text-success font-11 fw-semibold d-flex align-items-center gap-1 mt-1">
                        <i class="mdi mdi-clock-check-outline"></i> Sesuai jam operasional
                    </span>
                </div>
            </div>
        </div>

        {{-- Card 2: Terlambat Masuk --}}
        <div class="col-6 col-md">
            <div class="card att-kpi-card shadow-xs h-100 p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted font-11 text-uppercase fw-bold" style="letter-spacing: 0.5px;">Terlambat</span>
                    <div class="att-icon-box bg-label-warning">
                        <i class="mdi mdi-clock-alert-outline text-warning fs-5"></i>
                    </div>
                </div>
                <div>
                    <h3 class="fw-bolder text-warning mb-0 font-monospace">{{ $stats['total_late'] }}</h3>
                    <span class="text-muted font-11 d-flex align-items-center gap-1 mt-1">
                        <i class="mdi mdi-clock-outline"></i> Lewat {{ $workStartTime ?? '08:00' }} WIB
                    </span>
                </div>
            </div>
        </div>

        {{-- Card 3: Cuti & Izin --}}
        <div class="col-6 col-md">
            <div class="card att-kpi-card shadow-xs h-100 p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted font-11 text-uppercase fw-bold" style="letter-spacing: 0.5px;">Cuti &amp; Izin</span>
                    <div class="att-icon-box bg-label-info">
                        <i class="mdi mdi-beach text-info fs-5"></i>
                    </div>
                </div>
                <div>
                    <h3 class="fw-bolder text-info mb-0 font-monospace">{{ $stats['total_leave'] }}</h3>
                    <span class="text-muted font-11 d-flex align-items-center gap-1 mt-1">
                        <i class="mdi mdi-calendar-check-outline"></i> Disetujui HR
                    </span>
                </div>
            </div>
        </div>

        {{-- Card 4: Izin Sakit --}}
        <div class="col-6 col-md">
            <div class="card att-kpi-card shadow-xs h-100 p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted font-11 text-uppercase fw-bold" style="letter-spacing: 0.5px;">Izin Sakit</span>
                    <div class="att-icon-box bg-label-primary">
                        <i class="mdi mdi-hospital-box-outline text-primary fs-5"></i>
                    </div>
                </div>
                <div>
                    <h3 class="fw-bolder text-primary mb-0 font-monospace">{{ $stats['total_sick'] }}</h3>
                    <span class="text-muted font-11 d-flex align-items-center gap-1 mt-1">
                        <i class="mdi mdi-file-document-check-outline"></i> Surat dokter
                    </span>
                </div>
            </div>
        </div>

        {{-- Card 5: Alpa --}}
        <div class="col-12 col-md">
            <div class="card att-kpi-card shadow-xs h-100 p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted font-11 text-uppercase fw-bold" style="letter-spacing: 0.5px;">Alpa / Absen</span>
                    <div class="att-icon-box bg-label-danger">
                        <i class="mdi mdi-account-remove-outline text-danger fs-5"></i>
                    </div>
                </div>
                <div>
                    <h3 class="fw-bolder text-danger mb-0 font-monospace">{{ $stats['total_alpha'] }}</h3>
                    <span class="text-danger font-11 fw-semibold d-flex align-items-center gap-1 mt-1">
                        <i class="mdi mdi-alert-circle-outline"></i> Potongan denda
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- ── 3. TOP 3 RANKING KETERLAMBATAN (LEADERBOARD TELAT) ─────────────── --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden bg-white">
        <div class="card-header bg-white py-3 px-4 border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2.5">
                <div class="avatar avatar-sm bg-label-danger rounded-circle d-flex align-items-center justify-content-center">
                    <i class="mdi mdi-podium-gold fs-4 text-danger"></i>
                </div>
                <div>
                    <h6 class="card-title mb-0 fw-bold text-dark d-flex align-items-center flex-wrap gap-2">
                        <span>Leaderboard Keterlambatan (Top 3 Terbanyak)</span>
                        <span class="badge bg-label-danger rounded-pill font-11 px-2.5 py-0.5">
                            <i class="mdi mdi-calendar-month me-0.5"></i>Periode: {{ \Carbon\Carbon::createFromDate($recapYear, $recapMonth, 1)->translatedFormat('F Y') }}
                        </span>
                    </h6>
                    <small class="text-muted font-11">Karyawan dengan akumulasi menit dan frekuensi terlambat tertinggi pada periode berjalan.</small>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('hr.attendances.penalties', ['month' => $recapMonth, 'year' => $recapYear]) }}" class="btn btn-sm btn-outline-danger shadow-xs rounded-pill px-3 font-12 fw-bold" title="Buka Detail Seluruh Denda Keterlambatan">
                    <i class="mdi mdi-chart-box-outline me-1"></i> Lihat Rekap Lengkap &amp; Denda
                </a>
            </div>
        </div>
        <div class="card-body p-3 p-md-4 bg-light bg-opacity-50">
            @if (isset($topLateRankings) && $topLateRankings->isNotEmpty())
                <div class="row g-3">
                    @foreach ($topLateRankings as $rankIdx => $lateItem)
                        @php
                            $rankNumber = $rankIdx + 1;
                            $empObj = $lateItem['employee'];
                            $empUser = $empObj->user;
                            $userName = $empUser?->name ?? 'Karyawan Reftech';
                            $deptName = $empObj->department?->name ?? '-';
                            $posName = $empObj->position?->name ?? 'Staff';
                            $nik = $empObj->nik ?? '-';

                            // Resolve asset foto / avatar dari project
                            $empAvatar = null;
                            if ($empUser && $empUser->image) {
                                if (str_starts_with($empUser->image, 'http://') || str_starts_with($empUser->image, 'https://')) {
                                    $empAvatar = $empUser->image;
                                } elseif (file_exists(public_path($empUser->image))) {
                                    $empAvatar = asset(ltrim($empUser->image, '/'));
                                } elseif (file_exists(public_path('storage/' . $empUser->image))) {
                                    $empAvatar = asset('storage/' . $empUser->image);
                                }
                            }
                            if (!$empAvatar) {
                                $defaultAvatarNum = (($empObj->user_id ?? $empObj->id) % 18) + 1;
                                $avatarPath = "assets/img/avatars/{$defaultAvatarNum}.png";
                                if (file_exists(public_path($avatarPath))) {
                                    $empAvatar = asset($avatarPath);
                                }
                            }

                            $rankStyles = [
                                1 => [
                                    'badge_bg' => 'bg-danger text-white',
                                    'border_color' => '#ef4444',
                                    'card_border' => 'border-danger border-opacity-25',
                                    'card_bg' => 'bg-white',
                                    'header_icon' => 'mdi mdi-crown text-danger',
                                    'rank_label' => 'Rank #1 Paling Telat',
                                    'ribbon_class' => 'bg-danger',
                                ],
                                2 => [
                                    'badge_bg' => 'bg-warning text-dark',
                                    'border_color' => '#f59e0b',
                                    'card_border' => 'border-warning border-opacity-25',
                                    'card_bg' => 'bg-white',
                                    'header_icon' => 'mdi mdi-medal-outline text-warning',
                                    'rank_label' => 'Rank #2',
                                    'ribbon_class' => 'bg-warning',
                                ],
                                3 => [
                                    'badge_bg' => 'bg-info text-white',
                                    'border_color' => '#06b6d4',
                                    'card_border' => 'border-info border-opacity-25',
                                    'card_bg' => 'bg-white',
                                    'header_icon' => 'mdi mdi-medal-outline text-info',
                                    'rank_label' => 'Rank #3',
                                    'ribbon_class' => 'bg-info',
                                ],
                            ];
                            $style = $rankStyles[$rankNumber] ?? $rankStyles[3];
                        @endphp
                        <div class="col-12 col-md-4">
                            <div class="card border {{ $style['card_border'] }} shadow-xs rounded-4 h-100 {{ $style['card_bg'] }} position-relative overflow-hidden podium-card">
                                {{-- Top Accent Bar --}}
                                <div style="height: 4px; width: 100%;" class="{{ $style['ribbon_class'] }}"></div>
                                
                                <div class="card-body p-3.5 d-flex flex-column justify-content-between">
                                    <div>
                                        <div class="d-flex align-items-center justify-content-between mb-3">
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="badge {{ $style['badge_bg'] }} rounded-pill px-2.5 py-1 fw-bold fs-7 shadow-xs">
                                                    <i class="{{ $style['header_icon'] }} me-1"></i>#{{ $rankNumber }}
                                                </span>
                                                <span class="text-muted fw-bold font-11 text-uppercase">{{ $style['rank_label'] }}</span>
                                            </div>
                                            <span class="badge {{ $lateItem['strike_badge'] ?? 'bg-label-warning' }} rounded-pill font-11">
                                                {{ $lateItem['strike_status'] ?? ($lateItem['late_days_count'] . 'x Terlambat') }}
                                            </span>
                                        </div>

                                        <div class="d-flex align-items-center gap-2.5 mb-3">
                                            <div class="position-relative flex-shrink-0">
                                                @if ($empAvatar)
                                                    <img src="{{ $empAvatar }}" alt="{{ $userName }}" class="rounded-circle shadow-xs" style="width: 46px; height: 46px; object-fit: cover; border: 2px solid {{ $style['border_color'] }};">
                                                @else
                                                    <div class="avatar avatar-md rounded-circle bg-label-primary d-flex align-items-center justify-content-center fw-bold fs-6 shadow-xs" style="width: 46px; height: 46px; border: 2px solid {{ $style['border_color'] }};">
                                                        {{ strtoupper(substr($userName, 0, 2)) }}
                                                    </div>
                                                @endif
                                                <span class="position-absolute bottom-0 end-0 badge {{ $style['badge_bg'] }} rounded-circle p-0 d-flex align-items-center justify-content-center shadow-xs" style="width: 18px; height: 18px; font-size: 10px; border: 2px solid #fff;">
                                                    {{ $rankNumber }}
                                                </span>
                                            </div>
                                            <div class="overflow-hidden">
                                                <a href="{{ route('profile.show', $empObj->user_id ?? $empObj->id) }}#tabAttendance" class="fw-bold text-dark d-block text-truncate text-decoration-none hover-primary mb-0 font-14" title="{{ $userName }}">
                                                    {{ $userName }}
                                                </a>
                                                <div class="text-muted font-11 text-truncate">
                                                    <span>{{ $deptName }}</span> &bull; <span>{{ $posName }}</span>
                                                </div>
                                                <div class="text-muted font-monospace font-11">NIK: {{ $nik }}</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="border-top pt-2.5 mt-2 bg-light bg-opacity-75 rounded-3 p-2.5">
                                        <div class="row g-2 text-center">
                                            <div class="col-4 border-end">
                                                <span class="text-muted small d-block font-10 text-uppercase fw-semibold">Frekuensi</span>
                                                <span class="fw-bold text-danger font-14 font-monospace">{{ $lateItem['late_days_count'] }}x</span>
                                                <span class="text-muted font-10 d-block">Hari</span>
                                            </div>
                                            <div class="col-4 border-end">
                                                <span class="text-muted small d-block font-10 text-uppercase fw-semibold">Durasi Telat</span>
                                                <span class="fw-bold text-warning font-14 font-monospace">{{ $lateItem['late_minutes'] }}m</span>
                                                <span class="text-muted font-10 d-block">{{ round($lateItem['late_minutes'] / 60, 1) }} Jam</span>
                                            </div>
                                            <div class="col-4">
                                                <span class="text-muted small d-block font-10 text-uppercase fw-semibold">Total Denda</span>
                                                <span class="fw-bold text-danger font-14 font-monospace">
                                                    @if ($lateItem['penalty_total'] > 0)
                                                        {{ number_format($lateItem['penalty_total'] / 1000, 0) }}k
                                                    @else
                                                        0
                                                    @endif
                                                </span>
                                                <span class="text-muted font-10 d-block">
                                                    @if ($lateItem['penalty_total'] > 0)
                                                        Rp {{ number_format($lateItem['penalty_total'], 0, ',', '.') }}
                                                    @else
                                                        Bebas Denda
                                                    @endif
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-4 bg-white rounded-3 border border-dashed p-4">
                    <div class="avatar avatar-md bg-label-success rounded-circle mx-auto mb-2 d-flex align-items-center justify-content-center">
                        <i class="mdi mdi-shield-check-outline fs-3 text-success"></i>
                    </div>
                    <h6 class="fw-bold text-success mb-1">100% Disiplin &amp; Tepat Waktu</h6>
                    <p class="text-muted small mb-0 font-12">Tidak ada rekaman keterlambatan pada periode <strong>{{ \Carbon\Carbon::createFromDate($recapYear, $recapMonth, 1)->translatedFormat('F Y') }}</strong>.</p>
                </div>
            @endif
        </div>
    </div>

    {{-- ── 4. MODERN FILTER TOOLBAR ───────────────────────────────────────── --}}
    <div class="card att-filter-card shadow-xs mb-4">
        <div class="card-body p-3 p-md-3.5">
            <form action="{{ route('hr.attendances.index') }}" method="GET" class="row g-2.5 align-items-end">
                <div class="col-12 col-sm-6 col-md-2">
                    <label class="form-label font-11 fw-bold text-dark mb-1">Tipe Periode</label>
                    <select name="filter_type" id="filterTypeSelect" class="form-select font-12 rounded-3" onchange="toggleFilterType(this.value)">
                        <option value="daily" @selected($filterType === 'daily')>📅 Harian (Tanggal)</option>
                        <option value="monthly" @selected($filterType === 'monthly')>🗓️ Bulanan (Bulan)</option>
                    </select>
                </div>
                <div class="col-12 col-sm-6 col-md-2">
                    <div id="containerDailyFilter" style="{{ $filterType === 'monthly' ? 'display: none;' : '' }}">
                        <label class="form-label font-11 fw-bold text-dark mb-1">Pilih Tanggal</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="mdi mdi-calendar text-primary"></i></span>
                            <input type="date" name="date" class="form-control font-12 rounded-end-3" value="{{ $selectedDate }}">
                        </div>
                    </div>
                    <div id="containerMonthlyFilter" style="{{ $filterType !== 'monthly' ? 'display: none;' : '' }}">
                        <label class="form-label font-11 fw-bold text-dark mb-1">Pilih Bulan</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="mdi mdi-calendar-month text-primary"></i></span>
                            <input type="month" name="month" class="form-control font-12 rounded-end-3" value="{{ $selectedMonth }}">
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <label class="form-label font-11 fw-bold text-dark mb-1">Departemen</label>
                    <select name="department_id" class="form-select font-12 rounded-3">
                        <option value="">Semua Departemen</option>
                        @foreach ($departments as $dept)
                            <option value="{{ $dept->id }}" @selected($departmentId == $dept->id)>{{ $dept->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-sm-6 col-md-2">
                    <label class="form-label font-11 fw-bold text-dark mb-1">Status Kehadiran</label>
                    <select name="status" class="form-select font-12 rounded-3">
                        <option value="">Semua Status</option>
                        <option value="Hadir" @selected($status === 'Hadir')>Hadir</option>
                        <option value="Izin" @selected($status === 'Izin')>Izin</option>
                        <option value="Sakit" @selected($status === 'Sakit')>Sakit</option>
                        <option value="Cuti" @selected($status === 'Cuti')>Cuti</option>
                        <option value="Alpa" @selected($status === 'Alpa')>Alpa</option>
                    </select>
                </div>
                <div class="col-12 col-sm-6 col-md-2">
                    <label class="form-label font-11 fw-bold text-dark mb-1">Cari Karyawan / NIK</label>
                    <input type="text" name="search" class="form-control font-12 rounded-3" placeholder="Nama atau NIK..." value="{{ $search }}">
                </div>
                <div class="col-12 col-md-1 d-flex gap-1.5">
                    <button type="submit" class="btn btn-primary rounded-3 w-100 shadow-xs font-12 fw-bold" title="Terapkan Filter">
                        <i class="mdi mdi-filter-variant me-1"></i>Filter
                    </button>
                    @if ($filterType === 'monthly' || $departmentId || $status || $search || $selectedDate !== \Carbon\Carbon::today('Asia/Jakarta')->toDateString())
                        <a href="{{ route('hr.attendances.index') }}" class="btn btn-outline-secondary rounded-3 shadow-xs" title="Reset Filter ke Hari Ini">
                            <i class="mdi mdi-refresh"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    {{-- ── 5. ATTENDANCE DATA TABLE ───────────────────────────────────────── --}}
    <div class="att-table-wrapper shadow-xs mb-4">
        <div class="p-3.5 px-4 bg-white border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <div class="avatar avatar-xs bg-label-primary rounded-circle d-flex align-items-center justify-content-center">
                    <i class="mdi mdi-table-account text-primary"></i>
                </div>
                <h6 class="fw-bold text-dark mb-0 font-15">Data Log Presensi &amp; Rekam Kehadiran</h6>
            </div>
            <span class="badge bg-label-secondary rounded-pill font-11 px-3 py-1">
                Total: {{ $attendances->total() }} Baris Data
            </span>
        </div>

        <div class="table-responsive text-nowrap">
            <table class="table att-table align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width: 48px;" class="text-center">#</th>
                        <th>Karyawan</th>
                        <th>Departemen &amp; Jabatan</th>
                        @if ($filterType === 'monthly')
                            <th>Tanggal</th>
                        @endif
                        <th>Jam Masuk &amp; Foto</th>
                        <th>Jam Pulang</th>
                        <th>Tipe &amp; Device</th>
                        <th>Keterlambatan &amp; Denda</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($attendances as $idx => $att)
                        @php
                            $empName = $att->employee->user?->name ?? ($att->employee->nik ? 'Karyawan ' . $att->employee->nik : 'Karyawan #' . $att->employee->id);
                            $statusColors = [
                                'Hadir' => 'success',
                                'Izin' => 'info',
                                'Sakit' => 'primary',
                                'Cuti' => 'warning',
                                'Alpa' => 'danger',
                            ];
                            $badgeColor = $statusColors[$att->status] ?? 'secondary';
                        @endphp
                        <tr>
                            <td class="text-center text-muted font-12 font-monospace">{{ $attendances->firstItem() + $idx }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2.5">
                                    <div class="avatar avatar-sm rounded-circle bg-label-primary d-flex align-items-center justify-content-center fw-bold shadow-xs">
                                        {{ strtoupper(substr($empName, 0, 2)) }}
                                    </div>
                                    <div>
                                        <a href="{{ route('employees.show', $att->employee_id) }}" class="fw-bold text-dark d-block text-truncate text-decoration-none hover-primary font-13" style="max-width: 180px;" title="{{ $empName }}">
                                            {{ $empName }}
                                        </a>
                                        <span class="text-muted font-11 font-monospace">NIK: {{ $att->employee->nik ?? '-' }}</span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="fw-semibold text-dark font-12">{{ $att->employee->department?->name ?? '-' }}</div>
                                <span class="text-muted font-11">{{ $att->employee->position?->name ?? '-' }}</span>
                            </td>
                            @if ($filterType === 'monthly')
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="fw-bold text-dark font-12 font-monospace">{{ \Carbon\Carbon::parse($att->date)->translatedFormat('d M Y') }}</span>
                                        <small class="text-muted font-11">{{ \Carbon\Carbon::parse($att->date)->translatedFormat('l') }}</small>
                                    </div>
                                </td>
                            @endif
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    @if ($att->clock_in)
                                        <span class="badge bg-label-dark font-monospace rounded-pill font-11 px-2.5 py-1">
                                            <i class="mdi mdi-clock-in text-success me-1"></i>{{ substr($att->clock_in, 0, 5) }}
                                        </span>
                                    @else
                                        <span class="text-muted font-12">-</span>
                                    @endif

                                    @if ($att->selfie_in)
                                        <button type="button" class="btn btn-xs btn-label-success btn-icon rounded-circle shadow-xs" data-bs-toggle="modal" data-bs-target="#modalSelfiePreview{{ $att->id }}" title="Lihat Foto Selfie Masuk">
                                            <i class="mdi mdi-camera-account"></i>
                                        </button>

                                        {{-- Modal Preview Selfie --}}
                                        <div class="modal fade" id="modalSelfiePreview{{ $att->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-sm modal-dialog-centered">
                                                <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                                                    <div class="modal-header py-2.5 px-3 bg-light border-bottom">
                                                        <h6 class="modal-title fw-bold font-12 mb-0"><i class="mdi mdi-camera-account me-1 text-success"></i> Selfie: {{ $empName }}</h6>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body p-2 text-center bg-dark">
                                                        <img src="{{ asset($att->selfie_in) }}" alt="Selfie Presensi" class="img-fluid rounded-3 shadow-sm" style="max-height: 360px; object-fit: cover;">
                                                        <div class="text-white-50 font-11 mt-2 font-monospace">
                                                            Waktu: {{ substr($att->clock_in, 0, 5) }} WIB &bull; IP: {{ $att->ip_address ?? '-' }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td>
                                @if ($att->clock_out)
                                    <span class="badge bg-label-dark font-monospace rounded-pill font-11 px-2.5 py-1">
                                        <i class="mdi mdi-clock-out text-danger me-1"></i>{{ substr($att->clock_out, 0, 5) }}
                                    </span>
                                @else
                                    <span class="text-muted font-12">-</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex flex-column gap-1 align-items-start">
                                    <span class="badge bg-label-secondary font-11 rounded-pill">{{ $att->work_type }}</span>
                                    @if ($att->device_id)
                                        <span class="badge bg-label-info font-10 rounded-pill" title="Device ID: {{ $att->device_id }}">
                                            <i class="mdi mdi-cellphone-check me-0.5"></i>{{ substr($att->device_id, 0, 10) }}...
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div class="d-flex flex-column gap-1 align-items-start">
                                    @if ($att->late_minutes > 0)
                                        <span class="badge bg-label-warning rounded-pill font-11">
                                            <i class="mdi mdi-clock-alert-outline me-0.5"></i>Telat {{ $att->late_minutes }}m
                                        </span>
                                        @if ($att->penalty_amount > 0)
                                            <span class="badge bg-danger rounded-pill font-10 font-monospace">
                                                <i class="mdi mdi-cash-minus me-0.5"></i>Rp {{ number_format($att->penalty_amount, 0, ',', '.') }}
                                            </span>
                                        @else
                                            <span class="badge bg-label-info rounded-pill font-10">Bebas Denda</span>
                                        @endif
                                    @endif
                                    @if ($att->overtime_minutes > 0)
                                        <span class="badge bg-label-info rounded-pill font-11">Lembur {{ round($att->overtime_minutes / 60, 1) }}j</span>
                                    @endif
                                    @if ($att->late_minutes == 0 && $att->overtime_minutes == 0)
                                        <span class="text-muted font-12">-</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-label-{{ $badgeColor }} rounded-pill font-11 px-2.5 py-1">{{ $att->status }}</span>
                            </td>
                            <td class="text-end pe-4">
                                <form action="{{ route('hr.attendances.destroy', $att->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus catatan presensi ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-icon btn-label-danger rounded-circle shadow-xs" title="Hapus Data Presensi">
                                        <i class="mdi mdi-trash-can-outline"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $filterType === 'monthly' ? 10 : 9 }}" class="text-center py-5 text-muted">
                                <div class="avatar avatar-lg bg-label-secondary rounded-circle mx-auto mb-2 d-flex align-items-center justify-content-center">
                                    <i class="mdi mdi-calendar-blank-outline fs-2 text-secondary"></i>
                                </div>
                                <h6 class="fw-bold text-dark mb-1 font-14">Tidak Ada Data Presensi</h6>
                                <p class="text-muted font-12 mb-0">
                                    @if ($filterType === 'monthly')
                                        Tidak ditemukan catatan kehadiran pada bulan <strong>{{ \Carbon\Carbon::create($selectedYear, $selectedMonthNum, 1)->translatedFormat('F Y') }}</strong>.
                                    @else
                                        Tidak ditemukan catatan kehadiran pada tanggal <strong>{{ \Carbon\Carbon::parse($selectedDate)->translatedFormat('d F Y') }}</strong>.
                                    @endif
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($attendances->hasPages())
            <div class="card-footer border-top py-3 px-4 bg-white d-flex justify-content-end">
                {{ $attendances->links() }}
            </div>
        @endif
    </div>
</div>

{{-- ── 6. MODAL INPUT PRESENSI MANUAL ─────────────────────────────────── --}}
<div class="modal fade" id="modalManualAttendance" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('hr.attendances.store') }}" method="POST" class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            @csrf
            <div style="height: 4px; background: linear-gradient(90deg, #6366f1 0%, #3b82f6 100%);"></div>
            <div class="modal-header border-bottom bg-light py-3 px-4">
                <div class="d-flex align-items-center gap-2">
                    <div class="avatar avatar-sm bg-label-primary rounded-circle d-flex align-items-center justify-content-center">
                        <i class="mdi mdi-plus-circle-outline fs-5 text-primary"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-dark font-15 mb-0">Input Catatan Presensi Manual</h5>
                        <small class="text-muted font-11">Pencatatan atau koreksi presensi oleh HR Admin</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label font-11 fw-bold text-dark mb-1">Pilih Karyawan <span class="text-danger">*</span></label>
                        <select name="employee_id" class="form-select font-12 rounded-3" required>
                            <option value="">- Pilih Karyawan -</option>
                            @foreach ($employees as $emp)
                                <option value="{{ $emp->id }}">
                                    {{ $emp->user?->name ?? $emp->nik }} ({{ $emp->department?->name ?? 'HR' }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label class="form-label font-11 fw-bold text-dark mb-1">Tanggal <span class="text-danger">*</span></label>
                        <input type="date" name="date" class="form-control font-12 rounded-3" value="{{ $selectedDate }}" required>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label class="form-label font-11 fw-bold text-dark mb-1">Status Kehadiran <span class="text-danger">*</span></label>
                        <select name="status" class="form-select font-12 rounded-3" required>
                            <option value="Hadir">Hadir</option>
                            <option value="Izin">Izin</option>
                            <option value="Sakit">Sakit</option>
                            <option value="Cuti">Cuti</option>
                            <option value="Alpa">Alpa</option>
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="form-label font-11 fw-bold text-dark mb-1">Jam Masuk (Clock In)</label>
                        <input type="time" name="clock_in" class="form-control font-12 rounded-3" value="08:00">
                    </div>
                    <div class="col-6">
                        <label class="form-label font-11 fw-bold text-dark mb-1">Jam Pulang (Clock Out)</label>
                        <input type="time" name="clock_out" class="form-control font-12 rounded-3" value="17:00">
                    </div>
                    <div class="col-6">
                        <label class="form-label font-11 fw-bold text-dark mb-1">Tipe Kerja</label>
                        <select name="work_type" class="form-select font-12 rounded-3">
                            <option value="WFO">WFO (Kantor)</option>
                            <option value="WFH">WFH (Rumah)</option>
                            <option value="Site/Lapangan">Site / Lapangan</option>
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="form-label font-11 fw-bold text-dark mb-1">Terlambat (Menit)</label>
                        <input type="number" name="late_minutes" class="form-control font-12 rounded-3" value="0" min="0">
                    </div>
                    <div class="col-12">
                        <label class="form-label font-11 fw-bold text-dark mb-1">Nominal Denda (Rp) <small class="text-muted fw-normal">(Opsional / Otomatis Dihitung)</small></label>
                        <input type="number" name="penalty_amount" class="form-control font-12 rounded-3" placeholder="Kosongkan jika ingin dihitung otomatis oleh sistem">
                    </div>
                    <div class="col-12">
                        <label class="form-label font-11 fw-bold text-dark mb-1">Catatan / Keterangan</label>
                        <textarea name="notes" class="form-control font-12 rounded-3" rows="2" placeholder="Keterangan tambahan..."></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top bg-light py-2.5 px-4 d-flex justify-content-between">
                <button type="button" class="btn btn-label-secondary font-12 rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary font-12 fw-bold rounded-pill px-4 shadow-xs">Simpan Presensi</button>
            </div>
        </form>
    </div>
</div>

<script>
    function toggleFilterType(val) {
        const dailyContainer = document.getElementById('containerDailyFilter');
        const monthlyContainer = document.getElementById('containerMonthlyFilter');
        if (val === 'monthly') {
            if (dailyContainer) dailyContainer.style.display = 'none';
            if (monthlyContainer) monthlyContainer.style.display = 'block';
        } else {
            if (dailyContainer) dailyContainer.style.display = 'block';
            if (monthlyContainer) monthlyContainer.style.display = 'none';
        }
    }
</script>
@endsection
