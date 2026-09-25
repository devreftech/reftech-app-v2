@extends('layouts.sales.app')
@section('title', 'Pengaturan Presensi & Kebijakan HRMS - Reftech')

@section('content')
<div class="container-fluid px-0">
    {{-- ── 1. HEADER & BREADCRUMB ───────────────────────────────────────────── --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1.5 text-muted small">
                    <li class="breadcrumb-item">
                        <a href="{{ route('employees.index') }}" class="text-muted d-inline-flex align-items-center">
                            <i class="mdi mdi-account-group-outline me-1"></i>HR Management
                        </a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('hr.attendances.index') }}" class="text-muted">Presensi &amp; Kehadiran</a>
                    </li>
                    <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">Pengaturan &amp; Kebijakan</li>
                </ol>
            </nav>
            <div class="d-flex align-items-center gap-2.5 flex-wrap">
                <h4 class="fw-bold mb-0 text-heading">Pengaturan Presensi &amp; Kebijakan HRMS</h4>
                <span class="badge bg-label-primary rounded-pill px-2.5 py-1 font-11 fw-bold">
                    <i class="mdi mdi-shield-crown-outline me-1"></i>Security &amp; Policy Hub
                </span>
            </div>
            <p class="text-muted small mb-0 mt-1">Kelola jam kerja operasional, denda keterlambatan berjenjang, anti-fraud device lock, dan whitelist WiFi secara modular.</p>
        </div>

        <div class="d-flex align-items-center gap-2 flex-wrap">
            <a href="{{ route('hr.attendances.index') }}" class="btn btn-outline-secondary shadow-xs d-flex align-items-center gap-1.5 px-3 rounded-3">
                <i class="mdi mdi-arrow-left fs-5"></i>
                <span class="fw-semibold">Kembali</span>
            </a>
            <a href="{{ route('hr.attendances.penalties') }}" class="btn btn-outline-danger shadow-xs d-flex align-items-center gap-1.5 px-3 rounded-3" title="Buka Rekap Akumulasi Denda">
                <i class="mdi mdi-cash-remove fs-5"></i>
                <span class="fw-semibold">Rekap Denda</span>
            </a>
            <button type="button" class="btn btn-primary shadow-xs d-flex align-items-center gap-1.5 px-3.5 rounded-3 fw-bold" onclick="document.getElementById('formMasterSettings').submit();">
                <i class="mdi mdi-content-save-check fs-5"></i>
                <span>Simpan Semua Perubahan</span>
            </button>
        </div>
    </div>

    {{-- ── 2. ALERT NOTIFICATIONS ────────────────────────────────────────────── --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2.5 shadow-xs mb-4 rounded-3 border-0 bg-success-subtle text-success" role="alert">
            <i class="mdi mdi-check-circle-outline fs-4"></i>
            <div class="flex-grow-1 fw-medium font-13">{{ session('success') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2.5 shadow-xs mb-4 rounded-3 border-0 bg-danger-subtle text-danger" role="alert">
            <i class="mdi mdi-alert-circle-outline fs-4"></i>
            <div class="flex-grow-1 fw-medium font-13">{{ session('error') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- ── 3. TOP QUICK SUMMARY METRICS ──────────────────────────────────────── --}}
    <div class="row g-3 mb-4">
        {{-- Metric 1 --}}
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-xs h-100 rounded-3 bg-white top-stat-card">
                <div class="card-body p-3.5 d-flex align-items-center gap-3">
                    <div class="avatar avatar-md bg-label-primary rounded-3 d-flex align-items-center justify-content-center flex-shrink-0">
                        <i class="mdi mdi-clock-outline fs-3 text-primary"></i>
                    </div>
                    <div class="overflow-hidden">
                        <span class="text-muted font-11 fw-semibold text-uppercase d-block mb-0.5">Jam Masuk Standar</span>
                        <div class="fw-bold text-dark font-16">{{ $workStartTime ?? '08:00' }} <span class="font-11 text-muted fw-normal">WIB</span></div>
                        <small class="text-success font-11 d-block text-truncate">Buka Jam {{ $earliestClockInTime ?? '07:00' }} WIB</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- Metric 2 --}}
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-xs h-100 rounded-3 bg-white top-stat-card">
                <div class="card-body p-3.5 d-flex align-items-center gap-3">
                    <div class="avatar avatar-md bg-label-danger rounded-3 d-flex align-items-center justify-content-center flex-shrink-0">
                        <i class="mdi mdi-cash-clock fs-3 text-danger"></i>
                    </div>
                    <div class="overflow-hidden">
                        <span class="text-muted font-11 fw-semibold text-uppercase d-block mb-0.5">Denda Keterlambatan</span>
                        <div class="fw-bold font-15">
                            @if(isset($isLatePenaltyEnabled) && $isLatePenaltyEnabled)
                                <span class="text-danger">Aktif Berjenjang</span>
                            @else
                                <span class="text-muted">Dinonaktifkan</span>
                            @endif
                        </div>
                        <small class="text-muted font-11 d-block text-truncate">Max: Rp {{ number_format($lateTier3Rate ?? 100000, 0, ',', '.') }}</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- Metric 3 --}}
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-xs h-100 rounded-3 bg-white top-stat-card">
                <div class="card-body p-3.5 d-flex align-items-center gap-3">
                    <div class="avatar avatar-md bg-label-success rounded-3 d-flex align-items-center justify-content-center flex-shrink-0">
                        <i class="mdi mdi-clock-fast fs-3 text-success"></i>
                    </div>
                    <div class="overflow-hidden">
                        <span class="text-muted font-11 fw-semibold text-uppercase d-block mb-0.5">Auto Clock-Out</span>
                        <div class="fw-bold text-dark font-16">{{ $autoClockOutTime ?? '17:00' }} <span class="font-11 text-muted fw-normal">WIB</span></div>
                        <small class="text-muted font-11 d-block text-truncate">
                            @if(isset($isAutoClockOutEnabled) && $isAutoClockOutEnabled)
                                <span class="text-success"><i class="mdi mdi-check-circle-outline me-0.5"></i>Auto-Out Aktif</span>
                            @else
                                <span class="text-secondary">Manual Only</span>
                            @endif
                        </small>
                    </div>
                </div>
            </div>
        </div>

        {{-- Metric 4 --}}
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-xs h-100 rounded-3 bg-white top-stat-card">
                <div class="card-body p-3.5 d-flex align-items-center gap-3">
                    <div class="avatar avatar-md bg-label-info rounded-3 d-flex align-items-center justify-content-center flex-shrink-0">
                        <i class="mdi mdi-shield-check-outline fs-3 text-info"></i>
                    </div>
                    <div class="overflow-hidden">
                        <span class="text-muted font-11 fw-semibold text-uppercase d-block mb-0.5">Proteksi Anti-Fraud</span>
                        <div class="fw-bold text-dark font-15">
                            @if(isset($isDeviceLockEnabled) && $isDeviceLockEnabled)
                                <span class="text-info">Device Lock Aktif</span>
                            @else
                                <span class="text-muted">Standard</span>
                            @endif
                        </div>
                        <small class="text-info font-11 d-block text-truncate">{{ $officeWifis->count() }} WiFi Terdaftar</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── 4. NAVIGATION TABS (MODERN SEGMENTED PILLS) ───────────────────────── --}}
    <div class="card border-0 shadow-xs rounded-4 bg-white mb-4 p-2 custom-settings-nav-card">
        <ul class="nav nav-pills custom-modern-tabs gap-2" id="settingsTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active fw-semibold d-flex align-items-center gap-2 py-2 px-3 rounded-3 font-13" id="tab-penalty-tab" data-bs-toggle="tab" data-bs-target="#tab-penalty" type="button" role="tab" aria-controls="tab-penalty" aria-selected="true">
                    <span class="tab-icon-pill bg-danger-subtle text-danger rounded-2 d-inline-flex align-items-center justify-content-center">
                        <i class="mdi mdi-cash-clock font-15"></i>
                    </span>
                    <span>Jam &amp; Denda</span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-semibold d-flex align-items-center gap-2 py-2 px-3 rounded-3 font-13" id="tab-security-tab" data-bs-toggle="tab" data-bs-target="#tab-security" type="button" role="tab" aria-controls="tab-security" aria-selected="false">
                    <span class="tab-icon-pill bg-primary-subtle text-primary rounded-2 d-inline-flex align-items-center justify-content-center">
                        <i class="mdi mdi-shield-account-outline font-15"></i>
                    </span>
                    <span>Anti-Fraud</span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-semibold d-flex align-items-center gap-2 py-2 px-3 rounded-3 font-13" id="tab-autoclockout-tab" data-bs-toggle="tab" data-bs-target="#tab-autoclockout" type="button" role="tab" aria-controls="tab-autoclockout" aria-selected="false">
                    <span class="tab-icon-pill bg-success-subtle text-success rounded-2 d-inline-flex align-items-center justify-content-center">
                        <i class="mdi mdi-clock-fast font-15"></i>
                    </span>
                    <span>Auto Clock-Out</span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-semibold d-flex align-items-center gap-2 py-2 px-3 rounded-3 font-13" id="tab-wifi-tab" data-bs-toggle="tab" data-bs-target="#tab-wifi" type="button" role="tab" aria-controls="tab-wifi" aria-selected="false">
                    <span class="tab-icon-pill bg-info-subtle text-info rounded-2 d-inline-flex align-items-center justify-content-center">
                        <i class="mdi mdi-wifi-check font-15"></i>
                    </span>
                    <span>WiFi Kantor</span>
                    <span class="badge bg-info-subtle text-info rounded-pill px-2 font-11 ms-0.5 fw-bold">{{ $officeWifis->count() }}</span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-semibold d-flex align-items-center gap-2 py-2 px-3 rounded-3 font-13" id="tab-holiday-tab" data-bs-toggle="tab" data-bs-target="#tab-holiday" type="button" role="tab" aria-controls="tab-holiday" aria-selected="false">
                    <span class="tab-icon-pill bg-danger-subtle text-danger rounded-2 d-inline-flex align-items-center justify-content-center">
                        <i class="mdi mdi-calendar-heart font-15"></i>
                    </span>
                    <span>Kalender Merah</span>
                    <span class="badge bg-danger-subtle text-danger rounded-pill px-2 font-11 ms-0.5 fw-bold">{{ $holidays->count() }}</span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-semibold d-flex align-items-center gap-2 py-2 px-3 rounded-3 font-13" id="tab-payroll-tab" data-bs-toggle="tab" data-bs-target="#tab-payroll" type="button" role="tab" aria-controls="tab-payroll" aria-selected="false">
                    <span class="tab-icon-pill bg-warning-subtle text-warning rounded-2 d-inline-flex align-items-center justify-content-center">
                        <i class="mdi mdi-calendar-sync font-15"></i>
                    </span>
                    <span>Cutoff &amp; Payroll</span>
                </button>
            </li>
        </ul>
    </div>

    {{-- ── 5. TAB CONTENTS: MODULAR INDEPENDENT CARDS ────────────────────────── --}}
    <form action="{{ route('hr.attendances.settings.update') }}" method="POST" id="formMasterSettings">
        @csrf

        <div class="tab-content p-0" id="settingsTabContent">
            
            {{-- ══════════════════════════════════════════════════════════════════════════ --}}
            {{-- TAB 1: JAM KERJA & SKEMA DENDA BERTINGKAT                                  --}}
            {{-- ══════════════════════════════════════════════════════════════════════════ --}}
            <div class="tab-pane fade show active" id="tab-penalty" role="tabpanel" aria-labelledby="tab-penalty-tab">
                
                {{-- CARD 1.1: Master Switch Kebijakan Sanksi Keterlambatan --}}
                <div class="card border-0 shadow-xs mb-4 rounded-4 bg-white border-start border-4 border-danger modular-card">
                    <div class="card-body p-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="avatar avatar-lg bg-danger-subtle text-danger rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 52px; height: 52px;">
                                <i class="mdi mdi-cash-clock fs-2"></i>
                            </div>
                            <div>
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <h5 class="fw-bold mb-0 text-heading font-16">Kebijakan Sanksi &amp; Denda Keterlambatan Presensi</h5>
                                    @if(isset($isLatePenaltyEnabled) && $isLatePenaltyEnabled)
                                        <span class="badge bg-success text-white font-10 px-2 py-0.5 rounded-pill"><i class="mdi mdi-check-circle me-1"></i>Sistem Aktif</span>
                                    @else
                                        <span class="badge bg-secondary text-white font-10 px-2 py-0.5 rounded-pill">Dinonaktifkan</span>
                                    @endif
                                </div>
                                <p class="text-muted font-12 mb-0 mt-0.5">Jika saklar diaktifkan, potongan denda keterlambatan berjenjang dan alpa otomatis dihitung dan dimasukkan ke rekapan payroll bulanan.</p>
                            </div>
                        </div>
                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input cursor-pointer" type="checkbox" name="is_late_penalty_enabled" value="1"
                                   id="switchLatePenalty" style="width: 3.6rem; height: 1.85rem;" @checked(isset($isLatePenaltyEnabled) && $isLatePenaltyEnabled)>
                        </div>
                    </div>
                </div>

                {{-- CARD 1.2: Konfigurasi Jam Masuk & Toleransi --}}
                <div class="card border-0 shadow-xs mb-4 rounded-4 bg-white modular-card">
                    <div class="card-header py-3 px-4 bg-white border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <div class="d-flex align-items-center gap-2">
                            <div class="avatar avatar-xs bg-label-primary rounded d-flex align-items-center justify-content-center">
                                <i class="mdi mdi-clock-outline font-14"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold font-14 text-dark text-uppercase">Konfigurasi Jam Operasional &amp; Toleransi Masuk</h6>
                                <small class="text-muted font-11">Atur jam buka presensi, jam masuk standar, batas toleransi, dan status libur akhir pekan</small>
                            </div>
                        </div>
                        <span class="badge bg-label-primary rounded-pill font-11 px-2.5 py-1">Senin s/d Jumat</span>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            {{-- Jam Buka Presensi (Minimal) --}}
                            <div class="col-12 col-sm-6 col-lg-3">
                                <div class="p-3.5 rounded-3 border-0 bg-body-tertiary h-100 d-flex flex-column justify-content-between modular-subcard">
                                    <div>
                                        <label class="form-label fw-bold text-dark mb-1 font-12 d-flex align-items-center justify-content-between">
                                            <span><i class="mdi mdi-door-open text-success me-1"></i>Jam Buka Presensi</span>
                                            <span class="badge bg-success-subtle text-success font-10">Minimal</span>
                                        </label>
                                        <div class="input-group input-group-merge my-2">
                                            <span class="input-group-text bg-white border-0 text-success"><i class="mdi mdi-clock-start"></i></span>
                                            <input type="time" name="earliest_clock_in_time" class="form-control bg-white border-0 fw-bold text-success font-15" value="{{ $earliestClockInTime ?? '07:00' }}" required>
                                            <span class="input-group-text bg-white border-0 fw-semibold text-muted font-12">WIB</span>
                                        </div>
                                    </div>
                                    <small class="text-muted font-11 d-block"><i class="mdi mdi-information-outline me-1"></i>Karyawan belum dapat clock-in sebelum jam ini.</small>
                                </div>
                            </div>

                            {{-- Jam Masuk Standar Kantor --}}
                            <div class="col-12 col-sm-6 col-lg-3">
                                <div class="p-3.5 rounded-3 border-0 bg-body-tertiary h-100 d-flex flex-column justify-content-between modular-subcard">
                                    <div>
                                        <label class="form-label fw-bold text-dark mb-1 font-12 d-flex align-items-center justify-content-between">
                                            <span><i class="mdi mdi-clock-in text-primary me-1"></i>Jam Masuk Kantor</span>
                                            <span class="badge bg-primary text-white font-10">Standar</span>
                                        </label>
                                        <div class="input-group input-group-merge my-2">
                                            <span class="input-group-text bg-white border-0 text-primary"><i class="mdi mdi-clock-outline"></i></span>
                                            <input type="time" name="work_start_time" class="form-control bg-white border-0 fw-bold text-primary font-15" value="{{ $workStartTime ?? '08:00' }}" required>
                                            <span class="input-group-text bg-white border-0 fw-semibold text-muted font-12">WIB</span>
                                        </div>
                                    </div>
                                    <small class="text-muted font-11 d-block"><i class="mdi mdi-information-outline me-1"></i>Batas waktu presensi tepat waktu (on-time).</small>
                                </div>
                            </div>

                            {{-- Toleransi Keterlambatan --}}
                            <div class="col-12 col-sm-6 col-lg-3">
                                <div class="p-3.5 rounded-3 border-0 bg-body-tertiary h-100 d-flex flex-column justify-content-between modular-subcard">
                                    <div>
                                        <label class="form-label fw-bold text-dark mb-1 font-12 d-flex align-items-center justify-content-between">
                                            <span><i class="mdi mdi-timer-sand text-warning me-1"></i>Toleransi Keterlambatan</span>
                                            <span class="badge bg-warning-subtle text-warning font-10">Grace Period</span>
                                        </label>
                                        <div class="input-group my-2">
                                            <span class="input-group-text bg-white border-0 text-warning"><i class="mdi mdi-timer-outline"></i></span>
                                            <input type="number" name="late_tolerance_minutes" class="form-control fw-bold bg-white border-0 font-15" value="{{ $lateToleranceMinutes ?? 0 }}" min="0" required>
                                            <span class="input-group-text bg-white border-0 fw-semibold text-muted font-12">Menit</span>
                                        </div>
                                    </div>
                                    <small class="text-muted font-11 d-block"><i class="mdi mdi-information-outline me-1"></i>0 = Lewat jam masuk langsung dihitung denda.</small>
                                </div>
                            </div>

                            {{-- Libur Akhir Pekan --}}
                            <div class="col-12 col-sm-6 col-lg-3">
                                <div class="p-3.5 rounded-3 border-0 bg-body-tertiary h-100 d-flex flex-column justify-content-between modular-subcard">
                                    <div>
                                        <label class="form-label fw-bold text-dark mb-1 font-12 d-flex align-items-center justify-content-between">
                                            <span><i class="mdi mdi-calendar-weekend text-success me-1"></i>Libur Akhir Pekan</span>
                                            <span class="badge bg-success-subtle text-success font-10">Weekend</span>
                                        </label>
                                        <div class="d-flex align-items-center justify-content-between pt-2">
                                            <div>
                                                <div class="fw-bold text-dark font-13">Sabtu &amp; Minggu</div>
                                                <small class="text-muted font-11">Bebas absensi &amp; denda</small>
                                            </div>
                                            <div class="form-check form-switch mb-0">
                                                <input class="form-check-input cursor-pointer" type="checkbox" name="is_weekend_off_enabled" value="1"
                                                       style="width: 2.8rem; height: 1.5rem;" @checked(!isset($isWeekendOffEnabled) || $isWeekendOffEnabled)>
                                            </div>
                                        </div>
                                    </div>
                                    <small class="text-muted font-11 d-block mt-2"><i class="mdi mdi-information-outline me-1"></i>Tanpa sanksi alpa otomatis pada akhir pekan.</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- CARD 1.3: Skema Denda Keterlambatan Bertingkat (Bulan Berjalan) --}}
                <div class="card border-0 shadow-xs mb-4 rounded-4 bg-white modular-card">
                    <div class="card-header py-3 px-4 bg-white border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <div class="d-flex align-items-center gap-2">
                            <div class="avatar avatar-xs bg-label-danger rounded d-flex align-items-center justify-content-center">
                                <i class="mdi mdi-stairs-up font-14"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold font-14 text-dark text-uppercase">Skema Denda Keterlambatan Berjenjang (Bulan Berjalan)</h6>
                                <small class="text-muted font-11">Nominal denda flat per kejadian keterlambatan dalam periode 1 bulan aktif</small>
                            </div>
                        </div>
                        <span class="badge bg-label-secondary font-11"><i class="mdi mdi-refresh me-1"></i>Reset Otomatis Setiap Tgl 1 Awal Bulan</span>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            {{-- Tier 1 --}}
                            <div class="col-12 col-md-4">
                                <div class="card border-0 h-100 shadow-none rounded-3 bg-body-tertiary overflow-hidden">
                                    <div style="height: 3.5px; background: #ffab00;"></div>
                                    <div class="card-body p-3.5">
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <span class="fw-bold text-dark font-13">Terlambat Ke-1</span>
                                            <span class="badge bg-warning text-dark font-10 fw-bold">Strike 1</span>
                                        </div>
                                        <div class="input-group my-2">
                                            <span class="input-group-text bg-white border-0 fw-semibold text-muted font-13">Rp</span>
                                            <input type="number" name="late_tier_1_rate" class="form-control fw-bold font-14 text-dark bg-white border-0" value="{{ $lateTier1Rate ?? 50000 }}" min="0" required>
                                        </div>
                                        <small class="text-muted font-11 d-flex align-items-center gap-1">
                                            <i class="mdi mdi-information-outline text-warning"></i>
                                            <span>Denda flat kejadian pertama dalam 1 bulan.</span>
                                        </small>
                                    </div>
                                </div>
                            </div>

                            {{-- Tier 2 --}}
                            <div class="col-12 col-md-4">
                                <div class="card border-0 h-100 shadow-none rounded-3 bg-body-tertiary overflow-hidden">
                                    <div style="height: 3.5px; background: #fd7e14;"></div>
                                    <div class="card-body p-3.5">
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <span class="fw-bold text-dark font-13">Terlambat Ke-2</span>
                                            <span class="badge bg-warning text-dark font-10 fw-bold">Strike 2</span>
                                        </div>
                                        <div class="input-group my-2">
                                            <span class="input-group-text bg-white border-0 fw-semibold text-muted font-13">Rp</span>
                                            <input type="number" name="late_tier_2_rate" class="form-control fw-bold font-14 text-dark bg-white border-0" value="{{ $lateTier2Rate ?? 75000 }}" min="0" required>
                                        </div>
                                        <small class="text-muted font-11 d-flex align-items-center gap-1">
                                            <i class="mdi mdi-information-outline text-warning"></i>
                                            <span>Denda flat kejadian kedua dalam 1 bulan.</span>
                                        </small>
                                    </div>
                                </div>
                            </div>

                            {{-- Tier 3 & Seterusnya --}}
                            <div class="col-12 col-md-4">
                                <div class="card border-0 h-100 shadow-none rounded-3 bg-body-tertiary overflow-hidden">
                                    <div style="height: 3.5px; background: #ff3e1d;"></div>
                                    <div class="card-body p-3.5">
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <span class="fw-bold text-danger font-13">Terlambat Ke-3 &amp; Seterusnya (&ge; 3x)</span>
                                            <span class="badge bg-danger text-white font-10 fw-bold">Strike &ge; 3</span>
                                        </div>
                                        <div class="input-group my-2">
                                            <span class="input-group-text bg-white border-0 fw-semibold text-danger font-13">Rp</span>
                                            <input type="number" name="late_tier_3_rate" class="form-control fw-bold font-14 text-danger bg-white border-0" value="{{ $lateTier3Rate ?? 100000 }}" min="0" required>
                                        </div>
                                        <small class="text-muted font-11 d-flex align-items-center gap-1">
                                            <i class="mdi mdi-alert-circle-outline text-danger"></i>
                                            <span>Denda flat untuk keterlambatan ke-3, 4, 5, dst.</span>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- CARD 1.4: Sanksi Alpa / Mangkir Kerja --}}
                <div class="card border-0 shadow-xs mb-4 rounded-4 bg-white modular-card">
                    <div class="card-body p-4">
                        <div class="row align-items-center g-3">
                            <div class="col-12 col-md-7 col-lg-8">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="avatar avatar-md bg-label-danger rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 44px; height: 44px;">
                                        <i class="mdi mdi-account-remove-outline fs-3"></i>
                                    </div>
                                    <div>
                                        <div class="d-flex align-items-center gap-2">
                                            <h6 class="fw-bold mb-0 text-dark font-14">Sanksi Alpa / Mangkir Seharian Penuh</h6>
                                            <span class="badge bg-danger-subtle text-danger font-10">Unexcused Absence</span>
                                        </div>
                                        <p class="text-muted mb-0 font-11 mt-0.5">Dikenakan kepada karyawan yang tidak hadir tanpa permohonan izin/cuti/sakit resmi di hari kerja (Senin s/d Jumat).</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-5 col-lg-4">
                                <div class="p-3 rounded-3 border-0 bg-body-tertiary">
                                    <label class="form-label font-11 fw-bold mb-1 text-dark">Nominal Potongan Alpa / Hari Kerja</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-0 fw-semibold text-danger">Rp</span>
                                        <input type="number" name="alpha_penalty_rate" class="form-control fw-bold text-danger bg-white border-0 font-14" value="{{ $alphaPenaltyRate ?? 50000 }}" min="0" required>
                                        <span class="input-group-text bg-white border-0 text-muted font-11">/ hari</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- CARD 1.5: Simulasi & Live Preview Modal Clock In --}}
                <div class="card border-0 shadow-xs rounded-4 bg-success-subtle text-success modular-card">
                    <div class="card-body p-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="avatar avatar-md bg-success text-white rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 46px; height: 46px;">
                                <i class="mdi mdi-play-circle-outline fs-3"></i>
                            </div>
                            <div>
                                <div class="d-flex align-items-center gap-2">
                                    <h6 class="fw-bold mb-0 text-dark font-14">Simulasi &amp; Live Test Modal Clock In</h6>
                                    <span class="badge bg-success text-white font-10">Live Interactive</span>
                                </div>
                                <p class="text-muted font-11 mb-0 mt-0.5">Uji coba langsung pop-up modal absensi masuk yang tampil di akun seluruh karyawan, termasuk validasi jam buka dan kamera live.</p>
                            </div>
                        </div>
                        <button type="button" class="btn btn-success shadow-xs px-3.5 py-2 fw-bold d-flex align-items-center gap-1.5 rounded-3 border-0" onclick="testClockInModalNow()">
                            <i class="mdi mdi-clock-in fs-5"></i>
                            <span>Test Buka Modal Clock In</span>
                        </button>
                    </div>
                </div>

            </div>

            {{-- ══════════════════════════════════════════════════════════════════════════ --}}
            {{-- TAB 2: PROTEKSI MULTI-LAYER ANTI-FRAUD                                     --}}
            {{-- ══════════════════════════════════════════════════════════════════════════ --}}
            <div class="tab-pane fade" id="tab-security" role="tabpanel" aria-labelledby="tab-security-tab">
                
                {{-- CARD 2.1: Overview Anti-Fraud Header --}}
                <div class="card border-0 shadow-xs mb-4 rounded-4 bg-white border-start border-4 border-primary modular-card">
                    <div class="card-body p-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="avatar avatar-lg bg-primary-subtle text-primary rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 52px; height: 52px;">
                                <i class="mdi mdi-shield-crown-outline fs-2"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-0 text-heading font-16">Proteksi Multi-Layer Keamanan &amp; Anti-Kecurangan Presensi</h5>
                                <p class="text-muted font-12 mb-0 mt-0.5">Aktifkan layer proteksi untuk mencegah titip absen rekan kerja, manipulasi lokasi (Fake GPS), dan multi-akun pada 1 perangkat.</p>
                            </div>
                        </div>
                        <span class="badge bg-label-primary rounded-pill font-11 px-2.5 py-1">3 Layer Proteksi</span>
                    </div>
                </div>

                {{-- CARD 2.2: Layer 1 - WiFi Restriction --}}
                <div class="card border-0 shadow-xs mb-3 rounded-4 bg-white feature-box-card modular-card">
                    <div class="card-body p-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
                        <div class="d-flex align-items-start gap-3 me-3">
                            <div class="avatar avatar-md bg-label-primary rounded-3 d-flex align-items-center justify-content-center mt-1 flex-shrink-0">
                                <i class="mdi mdi-wifi-check fs-3"></i>
                            </div>
                            <div>
                                <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                                    <h6 class="fw-bold text-dark mb-0 font-14">Layer 1: Pembatasan Jaringan WiFi Resmi Kantor (IP Whitelist)</h6>
                                    <span class="badge bg-primary-subtle text-primary font-10">Network Whitelist</span>
                                </div>
                                <p class="text-muted mb-0 font-12">Karyawan hanya dapat melakukan Clock In / Out jika perangkat mereka terhubung ke IP Publik WiFi kantor yang telah terdaftar di whitelist (Tab 4).</p>
                            </div>
                        </div>
                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input cursor-pointer" type="checkbox" name="is_wifi_restriction_enabled" value="1"
                                   style="width: 3.4rem; height: 1.8rem;" @checked(isset($isWifiRestrictionEnabled) && $isWifiRestrictionEnabled)>
                        </div>
                    </div>
                </div>

                {{-- CARD 2.3: Layer 2 - Device Lock (Anti-Titip Absen) --}}
                <div class="card border-0 shadow-xs mb-3 rounded-4 bg-white feature-box-card modular-card">
                    <div class="card-body p-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
                        <div class="d-flex align-items-start gap-3 me-3">
                            <div class="avatar avatar-md bg-label-info rounded-3 d-flex align-items-center justify-content-center mt-1 flex-shrink-0">
                                <i class="mdi mdi-cellphone-lock fs-3"></i>
                            </div>
                            <div>
                                <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                                    <h6 class="fw-bold text-dark mb-0 font-14">Layer 2: Anti-Titip Absen (Kunci 1 Perangkat per Karyawan)</h6>
                                    <span class="badge bg-info-subtle text-info font-10">Hardware Fingerprint</span>
                                </div>
                                <p class="text-muted mb-0 font-12">Mengunci identitas unik perangkat (Device Fingerprint). Mencegah 1 HP/Laptop dipakai untuk mengabsenkan akun rekan kerja pada hari yang sama meskipun berada di kantor.</p>
                            </div>
                        </div>
                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input cursor-pointer" type="checkbox" name="is_device_lock_enabled" value="1"
                                   style="width: 3.4rem; height: 1.8rem;" @checked(isset($isDeviceLockEnabled) && $isDeviceLockEnabled)>
                        </div>
                    </div>
                </div>

                {{-- CARD 2.4: Layer 3 - Live Camera Selfie --}}
                <div class="card border-0 shadow-xs mb-4 rounded-4 bg-white feature-box-card modular-card">
                    <div class="card-body p-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
                        <div class="d-flex align-items-start gap-3 me-3">
                            <div class="avatar avatar-md bg-label-warning rounded-3 d-flex align-items-center justify-content-center mt-1 flex-shrink-0">
                                <i class="mdi mdi-camera-iris fs-3"></i>
                            </div>
                            <div>
                                <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                                    <h6 class="fw-bold text-dark mb-0 font-14">Layer 3: Wajibkan Foto Selfie Kamera Live saat Clock In</h6>
                                    <span class="badge bg-warning-subtle text-warning font-10">Biometric Snapshot</span>
                                </div>
                                <p class="text-muted mb-0 font-12">Membuka dialog kamera live saat tombol Clock In diklik dan mengambil bukti snapshot foto wajah karyawan secara instan sebagai bukti kehadiran fisik.</p>
                            </div>
                        </div>
                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input cursor-pointer" type="checkbox" name="is_selfie_required" value="1"
                                   style="width: 3.4rem; height: 1.8rem;" @checked(isset($isSelfieRequired) && $isSelfieRequired)>
                        </div>
                    </div>
                </div>

            </div>

            {{-- ══════════════════════════════════════════════════════════════════════════ --}}
            {{-- TAB 3: AUTO CLOCK-OUT & UCAPAN APRESIASI JAM PULANG                        --}}
            {{-- ══════════════════════════════════════════════════════════════════════════ --}}
            <div class="tab-pane fade" id="tab-autoclockout" role="tabpanel" aria-labelledby="tab-autoclockout-tab">
                
                {{-- CARD 3.1: Konfigurasi Auto Clock-Out --}}
                <div class="card border-0 shadow-xs mb-4 rounded-4 bg-white border-start border-4 border-success modular-card">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between pb-3 mb-4 border-bottom flex-wrap gap-3">
                            <div class="d-flex align-items-center gap-3">
                                <div class="avatar avatar-lg bg-success-subtle text-success rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 52px; height: 52px;">
                                    <i class="mdi mdi-clock-fast fs-2"></i>
                                </div>
                                <div>
                                    <div class="d-flex align-items-center gap-2 flex-wrap">
                                        <h5 class="fw-bold mb-0 text-heading font-16">Auto Clock-Out Otomatis (Jam Pulang Default)</h5>
                                        @if(isset($isAutoClockOutEnabled) && $isAutoClockOutEnabled)
                                            <span class="badge bg-success text-white font-10 rounded-pill"><i class="mdi mdi-check-circle me-1"></i>Auto-Out Aktif</span>
                                        @else
                                            <span class="badge bg-secondary text-white font-10 rounded-pill">Nonaktif</span>
                                        @endif
                                    </div>
                                    <small class="text-muted">Otomatis menandai jam pulang bagi karyawan yang lupa atau tidak melakukan Clock Out secara mandiri.</small>
                                </div>
                            </div>
                            <div class="form-check form-switch mb-0">
                                <input class="form-check-input cursor-pointer" type="checkbox" name="is_auto_clock_out_enabled" value="1"
                                       id="switchAutoClockOut" style="width: 3.6rem; height: 1.85rem;" @checked(isset($isAutoClockOutEnabled) && $isAutoClockOutEnabled)>
                            </div>
                        </div>

                        <div class="row g-4 align-items-stretch">
                            <div class="col-12 col-md-6">
                                <div class="p-4 rounded-3 border-0 bg-body-tertiary h-100 d-flex flex-column justify-content-between modular-subcard">
                                    <div>
                                        <label class="form-label fw-bold text-dark mb-1 font-13 d-flex align-items-center gap-1.5">
                                            <i class="mdi mdi-clock-out text-success fs-5"></i> Jam Pulang Standar Kantor
                                        </label>
                                        <div class="input-group input-group-merge my-2">
                                            <span class="input-group-text bg-white border-0 text-success"><i class="mdi mdi-clock-outline"></i></span>
                                            <input type="time" name="auto_clock_out_time" class="form-control bg-white border-0 fw-bold text-success font-16" value="{{ $autoClockOutTime ?? '17:00' }}" required>
                                            <span class="input-group-text bg-white border-0 fw-semibold text-muted font-12">WIB</span>
                                        </div>
                                        <small class="text-muted font-11 d-block">Jam ini yang akan otomatis diisikan ke data absensi karyawan yang belum Clock Out.</small>
                                    </div>
                                    
                                    <div class="mt-3 pt-3 border-top d-flex align-items-center gap-2 text-muted font-12">
                                        <i class="mdi mdi-check-circle text-success fs-5"></i>
                                        <span>Status presensi ditandai sebagai: <strong class="text-dark">Hadir (Auto Clock-Out)</strong></span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <div class="p-4 rounded-3 border-0 bg-body-tertiary h-100 d-flex flex-column justify-content-between modular-subcard">
                                    <div>
                                        <div class="d-flex align-items-center gap-2 mb-2">
                                            <div class="avatar avatar-xs bg-label-info rounded d-flex align-items-center justify-content-center">
                                                <i class="mdi mdi-robot-outline font-14"></i>
                                            </div>
                                            <span class="fw-bold text-dark font-13">Jadwal Evaluasi Otomatis (Cron Job)</span>
                                        </div>
                                        <p class="text-muted font-12 mb-3">Sistem background scheduler otomatis menjalankan auto clock-out pada pukul <strong class="text-dark">17:05 WIB</strong> dan sapu bersih harian pada <strong class="text-dark">23:55 WIB</strong>.</p>
                                    </div>
                                    
                                    <button type="button" class="btn btn-outline-success w-100 py-2.5 d-flex align-items-center justify-content-center gap-1.5 fw-semibold rounded-3" onclick="document.getElementById('formAutoClockOutNow').submit();">
                                        <i class="mdi mdi-lightning-bolt fs-5"></i>
                                        <span>Jalankan Auto Clock-Out Hari Ini Sekarang</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- CARD 3.2: Modul Ucapan Apresiasi Jam Pulang Karyawan --}}
                <div class="card border-0 shadow-xs mb-4 rounded-4 bg-white modular-card">
                    <div class="card-header py-3 px-4 bg-white border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <div class="d-flex align-items-center gap-2.5">
                            <div class="avatar avatar-xs bg-label-warning rounded d-flex align-items-center justify-content-center">
                                <i class="mdi mdi-message-star-outline font-14 text-warning"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold font-14 text-dark text-uppercase">Modul Pop-Up Ucapan Apresiasi Jam Pulang</h6>
                                <small class="text-muted font-11">Apresiasi otomatis saat jam pulang tiba dan mengingatkan karyawan untuk Clock Out</small>
                            </div>
                        </div>
                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input cursor-pointer" type="checkbox" name="is_clock_out_greeting_enabled" value="1"
                                   id="switchClockOutGreeting" style="width: 3.4rem; height: 1.8rem;" @checked(!isset($isClockOutGreetingEnabled) || $isClockOutGreetingEnabled)>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <div class="col-12 col-lg-7">
                                <div class="p-3.5 rounded-3 border-0 bg-body-tertiary h-100 modular-subcard">
                                    <div class="mb-3">
                                        <label class="form-label font-11 fw-bold text-dark mb-1">Judul Pop-Up Ucapan <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-white border-0"><i class="mdi mdi-format-title text-primary"></i></span>
                                            <input type="text" name="clock_out_greeting_title" id="inputGreetingTitle" class="form-control bg-white font-13 fw-semibold border-0" 
                                                   value="{{ $clockOutGreetingTitle ?? 'Terima Kasih Atas Kerja Keras Hari Ini! 🎉' }}" 
                                                   placeholder="Contoh: Terima Kasih Atas Dedikasi &amp; Kerja Keras Hari Ini! 🎉" required>
                                        </div>
                                    </div>

                                    <div>
                                        <label class="form-label font-11 fw-bold text-dark mb-1">Isi Pesan / Ucapan Pulang <span class="text-danger">*</span></label>
                                        <textarea name="clock_out_greeting_message" id="inputGreetingMessage" class="form-control bg-white font-13 border-0" rows="4" 
                                                  placeholder="Tuliskan pesan ucapan jam pulang..." required>{{ $clockOutGreetingMessage ?? 'Jam kerja operasional kantor hari ini telah selesai. Selamat beristirahat, nikmati waktu berkualitas bersama keluarga, dan sampai jumpa besok!' }}</textarea>
                                        <small class="text-muted font-11 mt-1 d-block"><i class="mdi mdi-information-outline me-1"></i>Pesan ini menyambut karyawan dengan hangat saat jam pulang tiba dan mengingatkan untuk clock out.</small>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-lg-5">
                                {{-- CARD Preview Mini Card --}}
                                <div class="p-3.5 rounded-3 border-0 bg-white shadow-xs h-100 d-flex flex-column justify-content-between modular-subcard">
                                    <div>
                                        <div class="d-flex align-items-center justify-content-between mb-2 pb-1 border-bottom">
                                            <span class="fw-bold text-muted font-10 text-uppercase"><i class="mdi mdi-eye-outline me-1"></i> Live Preview Pop-up</span>
                                            <span class="badge bg-warning-subtle text-warning font-10">Prospect Style</span>
                                        </div>
                                        
                                        <div class="rounded-3 border-0 overflow-hidden shadow-xs my-2" style="background: #ffffff;">
                                            <!-- Mini Top Stripe -->
                                            <div style="height: 4px; background: linear-gradient(90deg, #f59e0b 0%, #ec4899 50%, #6366f1 100%);"></div>
                                            <div class="p-3 text-center">
                                                <span class="badge rounded-pill px-2 py-0.5 mb-2 fw-bold" style="background: rgba(245, 158, 11, 0.12); color: #d97706; font-size: 0.68rem;">
                                                    <i class="mdi mdi-weather-sunset-down me-1"></i> JAM PULANG KANTOR
                                                </span>
                                                
                                                <div class="mx-auto mb-2 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; border-radius: 50%; background: radial-gradient(circle, rgba(245, 158, 11, 0.22) 0%, transparent 70%);">
                                                    <div style="width: 32px; height: 32px; border-radius: 50%; background: linear-gradient(135deg, #f59e0b, #d97706); color: white; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 10px rgba(245, 158, 11, 0.35);">
                                                        <i class="mdi mdi-party-popper font-14"></i>
                                                    </div>
                                                </div>

                                                <h6 class="fw-bold text-dark font-12 mb-1" id="previewGreetingTitle">
                                                    {{ $clockOutGreetingTitle ?? 'Terima Kasih Atas Kerja Keras Hari Ini! 🎉' }}
                                                </h6>
                                                <div class="text-muted font-10 px-2 mb-2" style="line-height: 1.45;" id="previewGreetingMessage">
                                                    {{ $clockOutGreetingMessage ?? 'Jam kerja operasional kantor hari ini telah selesai. Selamat beristirahat, nikmati waktu berkualitas bersama keluarga, dan sampai jumpa besok!' }}
                                                </div>
                                                
                                                <div class="d-flex justify-content-center gap-1.5 pt-1">
                                                    <span class="badge bg-light text-muted border-0 font-10 py-1 px-2.5">Tutup</span>
                                                    <span class="badge bg-warning text-dark font-10 py-1 px-2.5 fw-bold"><i class="mdi mdi-clock-out me-1"></i>Clock Out Sekarang</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="pt-2">
                                        <button type="button" class="btn btn-sm btn-outline-warning w-100 py-2 fw-semibold shadow-xs d-flex align-items-center justify-content-center gap-1.5 rounded-3" onclick="testGreetingModalNow()">
                                            <i class="mdi mdi-play-circle-outline fs-5"></i>
                                            <span>Test Buka Modal Jam Pulang (Live Pop-up)</span>
                                        </button>
                                        <small class="text-muted font-10 text-center d-block mt-1"><i class="mdi mdi-information-outline text-primary me-1"></i>Klik tombol untuk mensimulasikan pop-up yang muncul di akun karyawan.</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            {{-- ══════════════════════════════════════════════════════════════════════════ --}}
            {{-- TAB 4: WHITELIST JARINGAN WIFI KANTOR                                      --}}
            {{-- ══════════════════════════════════════════════════════════════════════════ --}}
            <div class="tab-pane fade" id="tab-wifi" role="tabpanel" aria-labelledby="tab-wifi-tab">
                
                {{-- CARD 4.1: Banner Status Whitelist --}}
                <div class="card border-0 shadow-xs mb-4 rounded-4 bg-white border-start border-4 border-info modular-card">
                    <div class="card-body p-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="avatar avatar-lg bg-info-subtle text-info rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 52px; height: 52px;">
                                <i class="mdi mdi-ip-network-outline fs-2"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-0 text-heading font-16">Manajemen Whitelist IP Jaringan WiFi Kantor</h5>
                                <p class="text-muted font-12 mb-0 mt-0.5">Daftarkan seluruh IP Publik ISP/Router kantor agar karyawan hanya dapat clock-in saat terhubung ke jaringan resmi.</p>
                            </div>
                        </div>
                        <span class="badge bg-label-info rounded-pill font-11 px-2.5 py-1">{{ $officeWifis->count() }} WiFi Terdaftar</span>
                    </div>
                </div>

                <div class="row g-4">
                    {{-- Kolom Kiri: Deteksi IP & Form Tambah WiFi --}}
                    <div class="col-12 col-lg-5">
                        
                        {{-- CARD 4.2: Deteksi IP Live --}}
                        <div class="card border-0 shadow-xs mb-3 bg-white rounded-4 modular-card">
                            <div class="card-body p-3.5 d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="avatar avatar-md bg-label-primary rounded-3 d-flex align-items-center justify-content-center">
                                        <i class="mdi mdi-ip-network fs-3"></i>
                                    </div>
                                    <div>
                                        <div class="text-muted font-10 text-uppercase fw-bold">IP Jaringan Anda Saat Ini</div>
                                        <span class="fw-bold font-monospace text-primary font-15">{{ $currentClientIp }}</span>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-sm btn-label-primary fw-semibold rounded-pill px-3 border-0" onclick="autofillMyIp('{{ $currentClientIp }}')">
                                    <i class="mdi mdi-plus-circle-outline me-1"></i> Gunakan IP
                                </button>
                            </div>
                        </div>

                        {{-- CARD 4.3: Form Tambah WiFi Baru --}}
                        <div class="card border-0 shadow-xs bg-white rounded-4 modular-card">
                            <div class="card-header py-3 px-4 bg-white border-bottom">
                                <h6 class="mb-0 fw-bold font-13 text-uppercase text-dark d-flex align-items-center gap-2">
                                    <i class="mdi mdi-plus-circle text-primary fs-5"></i> Tambah Jaringan WiFi Kantor
                                </h6>
                            </div>
                            <div class="card-body p-4">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label font-11 fw-bold mb-1 text-dark">Nama WiFi / Lokasi <span class="text-danger">*</span></label>
                                        <input type="text" name="name" id="wifiNameInput" class="form-control font-13 rounded-3" placeholder="Contoh: WiFi Kantor Pusat Lantai 1">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label font-11 fw-bold mb-1 text-dark">IP Address (Public IP) <span class="text-danger">*</span></label>
                                        <input type="text" name="ip_address" id="wifiIpInput" class="form-control font-monospace font-13 rounded-3" placeholder="Contoh: 180.252.xxx.xxx">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label font-11 fw-bold mb-1 text-dark">Catatan / Keterangan</label>
                                        <input type="text" name="notes" id="wifiNotesInput" class="form-control font-13 rounded-3" placeholder="Opsional (misal: ISP Biznet, Ruang HR, dll)">
                                    </div>
                                    <div class="col-12 pt-2">
                                        <button type="button" class="btn btn-primary w-100 py-2.5 fw-semibold shadow-xs d-flex align-items-center justify-content-center gap-1.5 rounded-3 border-0" onclick="submitStandaloneWifiForm()">
                                            <i class="mdi mdi-plus-box-outline fs-5"></i>
                                            <span>Daftarkan IP WiFi ke Whitelist</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Kolom Kanan: Tabel Daftar WiFi Whitelist --}}
                    <div class="col-12 col-lg-7">
                        {{-- CARD 4.4: Tabel Whitelist WiFi --}}
                        <div class="card border-0 shadow-xs h-100 bg-white rounded-4 modular-card">
                            <div class="card-header py-3 px-4 bg-white border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
                                <div>
                                    <h6 class="mb-0 fw-bold font-13 text-uppercase text-dark d-flex align-items-center gap-2">
                                        <i class="mdi mdi-wifi-check text-success fs-5"></i> Daftar Jaringan WiFi Terdaftar
                                    </h6>
                                    <small class="text-muted font-11">Whitelist IP publik yang diizinkan untuk presensi karyawan</small>
                                </div>
                                <span class="badge bg-label-primary rounded-pill font-11 px-2.5 py-1">{{ $officeWifis->count() }} WiFi Terdaftar</span>
                            </div>
                            <div class="table-responsive" style="max-height: 480px; overflow-y: auto;">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="bg-light sticky-top">
                                        <tr>
                                            <th class="ps-4">Nama Jaringan</th>
                                            <th>IP Address</th>
                                            <th>Catatan</th>
                                            <th class="text-center pe-4" style="width: 80px;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($officeWifis as $wifi)
                                            <tr>
                                                <td class="ps-4">
                                                    <div class="fw-bold text-heading font-13">{{ $wifi->name }}</div>
                                                    <span class="badge bg-success-subtle text-success font-10"><i class="mdi mdi-check-circle me-1"></i>Whitelist Aktif</span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-light text-dark font-monospace border-0 font-12 px-2.5 py-1 rounded-2">{{ $wifi->ip_address }}</span>
                                                </td>
                                                <td class="text-muted font-11">{{ $wifi->notes ?: '-' }}</td>
                                                <td class="text-center pe-4">
                                                    <button type="button" class="btn btn-sm btn-label-danger btn-icon rounded-2 border-0" title="Hapus dari Whitelist" onclick="deleteWifiRow('{{ route('hr.attendances.wifis.destroy', $wifi->id) }}', '{{ $wifi->name }}', '{{ $wifi->ip_address }}')">
                                                        <i class="mdi mdi-trash-can-outline font-15"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center py-5 text-muted font-12">
                                                    <div class="avatar avatar-lg bg-light rounded-circle mx-auto mb-2 d-flex align-items-center justify-content-center">
                                                        <i class="mdi mdi-wifi-off fs-1 opacity-50"></i>
                                                    </div>
                                                    <div class="fw-semibold text-dark font-13">Belum ada IP WiFi kantor yang didaftarkan</div>
                                                    <small class="text-muted">Tambahkan IP publik kantor pada form di sebelah kiri untuk mengaktifkan whitelist.</small>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            {{-- ══════════════════════════════════════════════════════════════════════════ --}}
            {{-- TAB 5: PENGATURAN KALENDER MERAH & HARI LIBUR PERUSAHAAN                   --}}
            {{-- ══════════════════════════════════════════════════════════════════════════ --}}
            <div class="tab-pane fade" id="tab-holiday" role="tabpanel" aria-labelledby="tab-holiday-tab">
                
                {{-- CARD 5.1: Master Switch Kebijakan Kalender Merah & Libur Bebas Denda --}}
                <div class="card border-0 shadow-xs mb-4 rounded-4 bg-white modular-card">
                    <div class="card-body p-4">
                        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3">
                            <div class="d-flex align-items-start gap-3">
                                <div class="avatar avatar-md bg-danger-subtle text-danger rounded-3 d-flex align-items-center justify-content-center flex-shrink-0 mt-0.5">
                                    <i class="mdi mdi-calendar-star fs-3"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1 fw-bold text-dark font-14">Kebijakan Libur Kalender Merah &amp; Bebas Denda Presensi</h6>
                                    <p class="text-muted font-12 mb-0">Pada tanggal kalender merah (Hari Libur Nasional, Cuti Bersama, &amp; Libur Perusahaan), karyawan dibebaskan dari kewajiban presensi dan <strong>tidak akan dikenakan sanksi denda keterlambatan maupun potongan alpa</strong>.</p>
                                </div>
                            </div>
                            <div class="form-check form-switch form-switch-lg mb-0 flex-shrink-0">
                                <input type="hidden" name="is_holiday_penalty_free" value="0">
                                <input class="form-check-input" type="checkbox" role="switch" id="switchHolidayPenaltyFree" name="is_holiday_penalty_free" value="1" {{ (isset($isHolidayPenaltyFree) && $isHolidayPenaltyFree) ? 'checked' : '' }}>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-4">
                    {{-- Kolom Kiri: Form Tambah & Sinkronisasi Preset --}}
                    <div class="col-12 col-lg-5">
                        {{-- CARD 5.2: Form Tambah Hari Libur Baru --}}
                        <div class="card border-0 shadow-xs bg-white rounded-4 mb-4 modular-card">
                            <div class="card-header py-3 px-4 bg-white border-bottom">
                                <h6 class="mb-0 fw-bold font-13 text-uppercase text-dark d-flex align-items-center gap-2">
                                    <i class="mdi mdi-calendar-plus text-danger fs-5"></i> Tambah Kalender Merah / Libur
                                </h6>
                            </div>
                            <div class="card-body p-4">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label font-11 fw-bold mb-1 text-dark">Nama Hari Libur / Keterangan Acara <span class="text-danger">*</span></label>
                                        <input type="text" name="name" id="holidayNameInput" class="form-control font-13 rounded-3" placeholder="Contoh: Hari Kemerdekaan RI ke-81">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label font-11 fw-bold mb-1 text-dark">Tanggal Libur <span class="text-danger">*</span></label>
                                        <input type="date" name="holiday_date" id="holidayDateInput" class="form-control font-13 rounded-3" value="{{ date('Y-m-d') }}">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label font-11 fw-bold mb-1 text-dark">Jenis / Kategori Libur <span class="text-danger">*</span></label>
                                        <select name="type" id="holidayTypeInput" class="form-select font-13 rounded-3">
                                            <option value="National">🔴 Hari Libur Nasional (Pemerintah)</option>
                                            <option value="Joint_Leave">🟠 Cuti Bersama Nasional</option>
                                            <option value="Company">🔵 Libur Khusus Internal Perusahaan</option>
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label font-11 fw-bold mb-1 text-dark">Catatan / Deskripsi Tambahan</label>
                                        <textarea name="description" id="holidayDescInput" class="form-control font-13 rounded-3" rows="2" placeholder="Opsional (contoh: Keputusan Direksi / SKB 3 Menteri)"></textarea>
                                    </div>
                                    <div class="col-12 pt-2">
                                        <button type="button" class="btn btn-danger w-100 py-2.5 fw-semibold shadow-xs d-flex align-items-center justify-content-center gap-1.5 rounded-3 border-0" onclick="submitStandaloneHolidayForm()">
                                            <i class="mdi mdi-plus-box-outline fs-5"></i>
                                            <span>Tambahkan ke Kalender Merah</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- CARD 5.3: Preset Generator 1-Klik Libur Nasional Indonesia --}}
                        <div class="card border-0 shadow-xs bg-white rounded-4 modular-card">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-start gap-3">
                                    <div class="avatar avatar-sm bg-label-warning rounded-2 d-flex align-items-center justify-content-center flex-shrink-0">
                                        <i class="mdi mdi-lightning-bolt text-warning fs-5"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-1 fw-bold text-dark font-13">Sinkronisasi Preset Otomatis</h6>
                                        <p class="text-muted font-11 mb-3">Impor daftar Hari Libur Nasional &amp; Cuti Bersama resmi Indonesia untuk tahun {{ $holidayYear }} dengan satu kali klik.</p>
                                        <button type="button" class="btn btn-sm btn-outline-warning text-dark fw-semibold rounded-pill px-3 shadow-xs d-inline-flex align-items-center gap-1.5" onclick="importPresetHolidays('{{ $holidayYear }}')">
                                            <i class="mdi mdi-download-box-outline fs-5"></i>
                                            <span>Impor Default SKB Libur Nasional {{ $holidayYear }}</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Kolom Kanan: Tabel Daftar Hari Libur Terdaftar --}}
                    <div class="col-12 col-lg-7">
                        {{-- CARD 5.4: Tabel Daftar Kalender Merah --}}
                        <div class="card border-0 shadow-xs h-100 bg-white rounded-4 modular-card">
                            <div class="card-header py-3 px-4 bg-white border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
                                <div>
                                    <h6 class="mb-0 fw-bold font-13 text-uppercase text-dark d-flex align-items-center gap-2">
                                        <i class="mdi mdi-calendar-multiselect text-danger fs-5"></i> Daftar Tanggal Merah &amp; Libur
                                    </h6>
                                    <small class="text-muted font-11">Presensi tidak wajib dan denda otomatis dinonaktifkan</small>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <select class="form-select form-select-sm font-12 rounded-3" style="width: 110px;" onchange="window.location.href='{{ route('hr.attendances.settings') }}?holiday_year=' + this.value + '#tab-holiday'">
                                        @foreach($availableHolidayYears as $yr)
                                            <option value="{{ $yr }}" {{ (int)$holidayYear === (int)$yr ? 'selected' : '' }}>Tahun {{ $yr }}</option>
                                        @endforeach
                                    </select>
                                    <span class="badge bg-danger-subtle text-danger rounded-pill font-11 px-2.5 py-1">{{ $holidays->count() }} Hari Libur</span>
                                </div>
                            </div>
                            <div class="table-responsive" style="max-height: 540px; overflow-y: auto;">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="bg-light sticky-top">
                                        <tr>
                                            <th class="ps-4" style="width: 140px;">Tanggal</th>
                                            <th>Nama Hari Libur</th>
                                            <th>Kategori</th>
                                            <th class="text-center pe-4" style="width: 70px;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($holidays as $holiday)
                                            <tr>
                                                <td class="ps-4">
                                                    <div class="fw-bold text-dark font-12 font-monospace">{{ $holiday->holiday_date->isoFormat('D MMM Y') }}</div>
                                                    <small class="text-muted font-11">{{ $holiday->holiday_date->isoFormat('dddd') }}</small>
                                                </td>
                                                <td>
                                                    <div class="fw-bold text-heading font-13">{{ $holiday->name }}</div>
                                                    @if($holiday->description)
                                                        <small class="text-muted font-11 d-block text-truncate" style="max-width: 260px;">{{ $holiday->description }}</small>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($holiday->type === 'National')
                                                        <span class="badge bg-danger-subtle text-danger font-11 px-2 py-0.5 rounded-pill"><i class="mdi mdi-flag-outline me-1"></i>Libur Nasional</span>
                                                    @elseif($holiday->type === 'Joint_Leave')
                                                        <span class="badge bg-warning-subtle text-warning font-11 px-2 py-0.5 rounded-pill"><i class="mdi mdi-calendar-clock me-1"></i>Cuti Bersama</span>
                                                    @else
                                                        <span class="badge bg-primary-subtle text-primary font-11 px-2 py-0.5 rounded-pill"><i class="mdi mdi-office-building me-1"></i>Libur Perusahaan</span>
                                                    @endif
                                                </td>
                                                <td class="text-center pe-4">
                                                    <button type="button" class="btn btn-sm btn-label-danger btn-icon rounded-2 border-0" title="Hapus dari Kalender" onclick="deleteHolidayRow('{{ route('hr.attendances.holidays.destroy', $holiday->id) }}', '{{ $holiday->name }}', '{{ $holiday->holiday_date->isoFormat('D MMMM Y') }}')">
                                                        <i class="mdi mdi-trash-can-outline font-15"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center py-5 text-muted font-12">
                                                    <div class="avatar avatar-lg bg-light rounded-circle mx-auto mb-2 d-flex align-items-center justify-content-center">
                                                        <i class="mdi mdi-calendar-blank-outline fs-1 opacity-50"></i>
                                                    </div>
                                                    <div class="fw-semibold text-dark font-13">Belum ada hari libur terdaftar untuk tahun {{ $holidayYear }}</div>
                                                    <small class="text-muted">Gunakan tombol <strong>Impor Default SKB</strong> atau tambahkan hari libur manual.</small>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            {{-- ══════════════════════════════════════════════════════════════════════════ --}}
            {{-- TAB 6: SIKLUS CUT-OFF PRESENSI & INTEGRASI PAYROLL                        --}}
            {{-- ══════════════════════════════════════════════════════════════════════════ --}}
            <div class="tab-pane fade" id="tab-payroll" role="tabpanel" aria-labelledby="tab-payroll-tab">
                
                {{-- CARD 6.1: Konfigurasi Tanggal Gajian & Waktu Rilis Rekap --}}
                <div class="card border-0 shadow-xs mb-4 rounded-4 bg-white border-start border-4 border-warning modular-card">
                    <div class="card-header py-3.5 px-4 bg-white border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <div class="d-flex align-items-center gap-2.5">
                            <div class="avatar avatar-sm bg-warning-subtle text-warning rounded-3 d-flex align-items-center justify-content-center">
                                <i class="mdi mdi-calendar-sync fs-4"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold font-15 text-dark text-uppercase">Konfigurasi Siklus Cut-Off &amp; Tanggal Penggajian</h6>
                                <small class="text-muted font-11">Atur patokan tanggal gajian, jam rilis rekap denda, dan aturan step-back hari kerja otomatis</small>
                            </div>
                        </div>
                        <span class="badge bg-label-warning rounded-pill font-11 px-2.5 py-1">
                            <i class="mdi mdi-calculator-variant me-1"></i>Payroll Automation
                        </span>
                    </div>

                    <div class="card-body p-4">
                        <div class="row g-3.5">
                            {{-- Tanggal Standar Gajian --}}
                            <div class="col-12 col-md-6">
                                <div class="p-3.5 rounded-3 bg-body-tertiary h-100 modular-subcard">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <label class="form-label fw-bold text-dark font-13 mb-0 d-flex align-items-center gap-1.5">
                                            <i class="mdi mdi-calendar-check text-primary fs-5"></i> Tanggal Standar Gajian (Payment Date)
                                        </label>
                                        <span class="badge bg-primary font-11">Default: Tgl 28</span>
                                    </div>
                                    <p class="text-muted font-11 mb-2.5">Tanggal pembayaran gaji rutin bulanan karyawan Reftech.</p>
                                    <select name="payroll_pay_date" class="form-select bg-white fw-bold font-14 border-0 shadow-xs">
                                        @for ($d = 1; $d <= 31; $d++)
                                            <option value="{{ $d }}" @selected(($payrollPayDate ?? 28) == $d)>
                                                Setiap Tanggal {{ $d }} {{ $d == 28 ? '(Standar Reftech)' : '' }}
                                            </option>
                                        @endfor
                                    </select>
                                </div>
                            </div>

                            {{-- Jam Rilis Rekap Denda --}}
                            <div class="col-12 col-md-6">
                                <div class="p-3.5 rounded-3 bg-body-tertiary h-100 modular-subcard">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <label class="form-label fw-bold text-dark font-13 mb-0 d-flex align-items-center gap-1.5">
                                            <i class="mdi mdi-clock-lock-outline text-danger fs-5"></i> Jam Kunci &amp; Rilis Rekap Denda
                                        </label>
                                        <span class="badge bg-danger font-11">Default: 09:00 WIB</span>
                                    </div>
                                    <p class="text-muted font-11 mb-2.5">Jam saat sistem mengunci denda periode dan memunculkan notifikasi pop-up ke HR.</p>
                                    <div class="input-group input-group-merge">
                                        <span class="input-group-text bg-white border-0 text-danger"><i class="mdi mdi-clock-outline"></i></span>
                                        <input type="time" name="payroll_cutoff_release_time" class="form-control bg-white border-0 fw-bold text-danger font-15 shadow-xs" value="{{ $payrollCutoffReleaseTime ?? '09:00' }}" required>
                                    </div>
                                </div>
                            </div>

                            {{-- Switch 1: Auto Step-back Weekend --}}
                            <div class="col-12 col-md-6">
                                <div class="p-3.5 rounded-3 bg-body-tertiary h-100 d-flex align-items-center justify-content-between gap-3 modular-subcard">
                                    <div>
                                        <label class="form-label fw-bold text-dark font-13 mb-1 d-flex align-items-center gap-1.5">
                                            <i class="mdi mdi-calendar-weekend text-warning fs-5"></i> Auto Step-Back Akhir Pekan (Weekend Rule)
                                        </label>
                                        <p class="text-muted font-11 mb-0">
                                            Jika tgl gajian jatuh hari <strong>Minggu</strong>, rekap maju ke hari <strong>Jumat</strong>. Jika tgl gajian hari <strong>Senin</strong>, rekap siap di hari <strong>Jumat</strong> minggu sebelumnya.
                                        </p>
                                    </div>
                                    <div class="form-check form-switch mb-0 flex-shrink-0">
                                        <input class="form-check-input cursor-pointer" type="checkbox" name="payroll_auto_stepback_weekend" value="1"
                                               id="switchStepbackWeekend" style="width: 3.2rem; height: 1.65rem;" @checked($payrollAutoStepbackWeekend ?? true)>
                                    </div>
                                </div>
                            </div>

                            {{-- Switch 2: Auto Step-back Hari Libur --}}
                            <div class="col-12 col-md-6">
                                <div class="p-3.5 rounded-3 bg-body-tertiary h-100 d-flex align-items-center justify-content-between gap-3 modular-subcard">
                                    <div>
                                        <label class="form-label fw-bold text-dark font-13 mb-1 d-flex align-items-center gap-1.5">
                                            <i class="mdi mdi-calendar-remove text-danger fs-5"></i> Lewati Kalender Merah / Libur Nasional
                                        </label>
                                        <p class="text-muted font-11 mb-0">
                                            Jika hari cut-off hasil mundur bertepatan dengan <strong>Kalender Merah Reftech</strong>, sistem otomatis melompati libur ke <strong>Hari Kerja Efektif Sebelumnya (Kamis, dst)</strong>.
                                        </p>
                                    </div>
                                    <div class="form-check form-switch mb-0 flex-shrink-0">
                                        <input class="form-check-input cursor-pointer" type="checkbox" name="payroll_auto_stepback_holiday" value="1"
                                               id="switchStepbackHoliday" style="width: 3.2rem; height: 1.65rem;" @checked($payrollAutoStepbackHoliday ?? true)>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- CARD 6.2: Live Simulator Cut-off Presensi Reftech --}}
                <div class="card border-0 shadow-xs mb-4 rounded-4 bg-white modular-card">
                    <div class="card-header py-3.5 px-4 bg-white border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <div class="d-flex align-items-center gap-2">
                            <div class="avatar avatar-xs bg-label-info rounded d-flex align-items-center justify-content-center">
                                <i class="mdi mdi-timeline-clock-outline font-14"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold font-14 text-dark text-uppercase">Simulasi Real-Time Siklus Cut-Off Penggajian</h6>
                                <small class="text-muted font-11">Preview rentang tanggal presensi terhitung dan jadwal rilis rekap denda berdasarkan kalender aktif</small>
                            </div>
                        </div>
                        <span class="badge bg-success font-11 rounded-pill px-2.5 py-1">
                            <i class="mdi mdi-check-circle me-1"></i>Live Calculator
                        </span>
                    </div>

                    <div class="card-body p-4">
                        <div class="row g-3">
                            {{-- Bulan Berjalan --}}
                            @if(isset($currentCutoffPeriod))
                            <div class="col-12 col-md-6">
                                <div class="border border-primary border-opacity-25 bg-label-primary p-3.5 rounded-3 h-100">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <span class="badge bg-primary rounded-pill font-11">
                                            Periode Bulan Ini: {{ $currentCutoffPeriod['month_name'] }} {{ $currentCutoffPeriod['year'] }}
                                        </span>
                                        @if($currentCutoffPeriod['is_recap_ready'])
                                            <span class="badge bg-success font-10"><i class="mdi mdi-lock-check me-1"></i>Terkunci 09:00</span>
                                        @else
                                            <span class="badge bg-warning font-10"><i class="mdi mdi-progress-clock me-1"></i>Berjalan</span>
                                        @endif
                                    </div>
                                    <div class="fw-bold font-15 text-dark mb-1">
                                        {{ $currentCutoffPeriod['start_date']->translatedFormat('d M Y') }} s/d {{ $currentCutoffPeriod['end_date']->translatedFormat('d M Y') }}
                                    </div>
                                    <div class="font-11 text-muted d-flex align-items-center gap-1 mt-1">
                                        <i class="mdi mdi-clock-check-outline text-primary"></i>
                                        <span>Rilis Notifikasi Modal &amp; Rekap: <strong>{{ $currentCutoffPeriod['end_date']->translatedFormat('d M Y') }} ({{ $currentCutoffPeriod['release_time'] }} WIB)</strong></span>
                                    </div>
                                </div>
                            </div>
                            @endif

                            {{-- Bulan Depan --}}
                            @if(isset($nextCutoffPeriod))
                            <div class="col-12 col-md-6">
                                <div class="border border-secondary border-opacity-25 bg-body-tertiary p-3.5 rounded-3 h-100">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <span class="badge bg-label-secondary rounded-pill font-11">
                                            Periode Bulan Depan: {{ $nextCutoffPeriod['month_name'] }} {{ $nextCutoffPeriod['year'] }}
                                        </span>
                                        <span class="badge bg-label-secondary font-10">Berikutnya</span>
                                    </div>
                                    <div class="fw-bold font-15 text-dark mb-1">
                                        {{ $nextCutoffPeriod['start_date']->translatedFormat('d M Y') }} s/d {{ $nextCutoffPeriod['end_date']->translatedFormat('d M Y') }}
                                    </div>
                                    <div class="font-11 text-muted d-flex align-items-center gap-1 mt-1">
                                        <i class="mdi mdi-clock-check-outline text-secondary"></i>
                                        <span>Rilis Notifikasi Modal &amp; Rekap: <strong>{{ $nextCutoffPeriod['end_date']->translatedFormat('d M Y') }} ({{ $nextCutoffPeriod['release_time'] }} WIB)</strong></span>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- CARD 6.3: Integrasi Cepat ke Rekap Denda & Payroll --}}
                <div class="card border-0 shadow-xs mb-4 rounded-4 bg-white modular-card">
                    <div class="card-body p-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="avatar avatar-md bg-label-success rounded-3 d-flex align-items-center justify-content-center flex-shrink-0">
                                <i class="mdi mdi-file-document-edit-outline fs-3 text-success"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-1 text-dark font-14">Akses Cepat Modul Penggajian &amp; Rekap Denda</h6>
                                <p class="text-muted font-11 mb-0">Gunakan link pintas berikut untuk meninjau data akumulasi denda presensi atau langsung memproses batch payroll.</p>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <a href="{{ route('hr.attendances.penalties') }}" class="btn btn-outline-danger font-12 fw-bold px-3 py-1.5 rounded-3 shadow-xs">
                                <i class="mdi mdi-table-eye me-1"></i> Buka Rekap Denda
                            </a>
                            <a href="{{ route('hr.payrolls.index') }}" class="btn btn-primary font-12 fw-bold px-3 py-1.5 rounded-3 shadow-xs">
                                <i class="mdi mdi-calculator me-1"></i> Buka Hub Payroll
                            </a>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </form>

    {{-- ── 6. FLOATING / STICKY BOTTOM SAVE BAR ──────────────────────────────── --}}
    <div class="card border-0 shadow-sm rounded-4 bg-white mt-4 sticky-save-bar">
        <div class="card-body py-3.5 px-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div class="text-muted font-11 d-flex align-items-center gap-1.5">
                <i class="mdi mdi-information-outline text-primary fs-5"></i>
                <span>Seluruh konfigurasi yang disimpan akan langsung diterapkan secara realtime untuk seluruh akun karyawan.</span>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('hr.attendances.index') }}" class="btn btn-label-secondary px-3.5 rounded-3 fw-semibold border-0">Batal</a>
                <button type="button" class="btn btn-primary px-4 shadow-xs fw-bold d-flex align-items-center gap-1.5 rounded-3 border-0" onclick="document.getElementById('formMasterSettings').submit();">
                    <i class="mdi mdi-content-save-check fs-5"></i>
                    <span>Simpan Semua Pengaturan</span>
                </button>
            </div>
        </div>
    </div>

    {{-- ── HIDDEN UTILITY FORMS ──────────────────────────────────────────────── --}}
    {{-- Form Auto Clock Out --}}
    <form id="formAutoClockOutNow" action="{{ route('hr.attendances.auto-clockout-now') }}" method="POST" class="d-none" onsubmit="return confirm('Jalankan Auto Clock-Out untuk semua karyawan yang belum Clock Out pada hari ini?');">
        @csrf
        <input type="hidden" name="date" value="{{ \Carbon\Carbon::today('Asia/Jakarta')->toDateString() }}">
    </form>

    {{-- Form Wifi Store Standalone --}}
    <form id="formStandaloneWifiStore" action="{{ route('hr.attendances.wifis.store') }}" method="POST" class="d-none">
        @csrf
        <input type="hidden" name="name" id="hiddenWifiName">
        <input type="hidden" name="ip_address" id="hiddenWifiIp">
        <input type="hidden" name="notes" id="hiddenWifiNotes">
    </form>

    {{-- Form Wifi Delete Standalone --}}
    <form id="formStandaloneWifiDelete" action="" method="POST" class="d-none">
        @csrf
        @method('DELETE')
    </form>

    {{-- Form Holiday Store Standalone --}}
    <form id="formStandaloneHolidayStore" action="{{ route('hr.attendances.holidays.store') }}" method="POST" class="d-none">
        @csrf
        <input type="hidden" name="name" id="hiddenHolidayName">
        <input type="hidden" name="holiday_date" id="hiddenHolidayDate">
        <input type="hidden" name="type" id="hiddenHolidayType">
        <input type="hidden" name="description" id="hiddenHolidayDesc">
    </form>

    {{-- Form Holiday Delete Standalone --}}
    <form id="formStandaloneHolidayDelete" action="" method="POST" class="d-none">
        @csrf
        @method('DELETE')
    </form>

    {{-- Form Holiday Import Standalone --}}
    <form id="formStandaloneHolidayImport" action="{{ route('hr.attendances.holidays.import-defaults') }}" method="POST" class="d-none">
        @csrf
        <input type="hidden" name="year" id="hiddenHolidayImportYear" value="{{ $holidayYear }}">
    </form>
</div>

<script>
    function autofillMyIp(ip) {
        var ipInput = document.getElementById('wifiIpInput');
        var nameInput = document.getElementById('wifiNameInput');
        if (ipInput) ipInput.value = ip;
        if (nameInput && !nameInput.value) nameInput.value = 'WiFi Kantor Pusat';
        ipInput?.focus();
    }

    function submitStandaloneHolidayForm() {
        var name = document.getElementById('holidayNameInput')?.value;
        var date = document.getElementById('holidayDateInput')?.value;
        var type = document.getElementById('holidayTypeInput')?.value;
        var desc = document.getElementById('holidayDescInput')?.value;

        if (!name || !date) {
            alert('Silakan isi Nama Hari Libur dan Tanggal terlebih dahulu.');
            return;
        }

        document.getElementById('hiddenHolidayName').value = name;
        document.getElementById('hiddenHolidayDate').value = date;
        document.getElementById('hiddenHolidayType').value = type || 'National';
        document.getElementById('hiddenHolidayDesc').value = desc || '';
        document.getElementById('formStandaloneHolidayStore').submit();
    }

    function deleteHolidayRow(actionUrl, name, date) {
        if (confirm('Hapus hari libur "' + name + '" (' + date + ') dari kalender merah?')) {
            var form = document.getElementById('formStandaloneHolidayDelete');
            form.action = actionUrl;
            form.submit();
        }
    }

    function importPresetHolidays(year) {
        if (confirm('Sinkronkan dan impor daftar default Hari Libur Nasional & Cuti Bersama untuk tahun ' + year + '?')) {
            document.getElementById('hiddenHolidayImportYear').value = year;
            document.getElementById('formStandaloneHolidayImport').submit();
        }
    }

    function submitStandaloneWifiForm() {
        var name = document.getElementById('wifiNameInput')?.value;
        var ip = document.getElementById('wifiIpInput')?.value;
        var notes = document.getElementById('wifiNotesInput')?.value;

        if (!name || !ip) {
            alert('Silakan isi Nama WiFi dan IP Address terlebih dahulu.');
            return;
        }

        document.getElementById('hiddenWifiName').value = name;
        document.getElementById('hiddenWifiIp').value = ip;
        document.getElementById('hiddenWifiNotes').value = notes || '';
        document.getElementById('formStandaloneWifiStore').submit();
    }

    function deleteWifiRow(actionUrl, name, ip) {
        if (confirm('Hapus WiFi "' + name + '" (' + ip + ') dari daftar whitelist?')) {
            var form = document.getElementById('formStandaloneWifiDelete');
            form.action = actionUrl;
            form.submit();
        }
    }

    // Function to test the Clock In Modal live from settings
    function testClockInModalNow() {
        var clockInModalEl = document.getElementById('navClockInModal');
        if (clockInModalEl && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            var inInstance = bootstrap.Modal.getInstance(clockInModalEl) || new bootstrap.Modal(clockInModalEl);
            inInstance.show();
        } else {
            alert('Modal Clock In siap di navbar.');
        }
    }

    // Function to test the Greeting Modal live from settings
    function testGreetingModalNow() {
        var titleIn = document.getElementById('inputGreetingTitle');
        var msgIn = document.getElementById('inputGreetingMessage');
        var modalTitle = document.getElementById('modalClockOutGreetingTitle');
        var modalMsg = document.getElementById('modalClockOutGreetingMessage');

        if (titleIn && modalTitle) {
            modalTitle.textContent = titleIn.value || 'Terima Kasih Atas Kerja Keras Hari Ini! 🎉';
        }
        if (msgIn && modalMsg) {
            modalMsg.textContent = msgIn.value || 'Jam kerja operasional kantor hari ini telah selesai. Selamat beristirahat, nikmati waktu berkualitas bersama keluarga, dan sampai jumpa besok!';
        }

        var greetingEl = document.getElementById('modalClockOutGreeting');
        if (greetingEl && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            var modalInstance = bootstrap.Modal.getInstance(greetingEl) || new bootstrap.Modal(greetingEl);
            modalInstance.show();
        }
    }

    // Live preview sync for Clock Out Greeting Pop-up
    document.addEventListener('DOMContentLoaded', function() {
        var titleIn = document.getElementById('inputGreetingTitle');
        var msgIn = document.getElementById('inputGreetingMessage');
        var titlePrev = document.getElementById('previewGreetingTitle');
        var msgPrev = document.getElementById('previewGreetingMessage');

        if (titleIn && titlePrev) {
            titleIn.addEventListener('input', function() {
                titlePrev.textContent = this.value || 'Terima Kasih Atas Kerja Keras Hari Ini! 🎉';
            });
        }

        if (msgIn && msgPrev) {
            msgIn.addEventListener('input', function() {
                msgPrev.textContent = this.value || 'Jam kerja operasional kantor hari ini telah selesai. Selamat beristirahat, nikmati waktu berkualitas bersama keluarga, dan sampai jumpa besok!';
            });
        }

        // Support URL hash for direct tab navigation (e.g. #tab-holiday)
        var currentHash = window.location.hash;
        if (currentHash) {
            var targetBtn = document.querySelector('button[data-bs-target="' + currentHash + '"]');
            if (targetBtn && typeof bootstrap !== 'undefined' && bootstrap.Tab) {
                var tabTrigger = new bootstrap.Tab(targetBtn);
                tabTrigger.show();
            }
        }
    });
</script>
@endsection

@push('after-style')
<style>
    #modalClockOutGreeting {
        z-index: 1085 !important;
    }
    .top-stat-card {
        border: 0 !important;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .top-stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 18px rgba(67, 89, 113, 0.08) !important;
    }
    .custom-modern-tabs .nav-link {
        color: #64748b;
        background: #f8fafc;
        border: 0 !important;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .custom-modern-tabs .nav-link:hover {
        color: #4f46e5;
        background: #ffffff;
        transform: translateY(-1px);
    }
    .custom-modern-tabs .nav-link.active {
        color: #4f46e5 !important;
        background: #eef2ff !important;
        box-shadow: 0 2px 8px rgba(99, 102, 241, 0.15);
    }
    .tab-icon-pill {
        width: 26px;
        height: 26px;
    }
    .modular-subcard {
        border: 0 !important;
        transition: box-shadow 0.2s ease, transform 0.2s ease;
    }
    .modular-subcard:hover {
        box-shadow: 0 4px 12px rgba(67, 89, 113, 0.06);
    }
    .feature-box-card {
        border: 0 !important;
        transition: all 0.2s ease;
    }
    .feature-box-card:hover {
        background-color: #ffffff !important;
        box-shadow: 0 4px 14px rgba(67, 89, 113, 0.07);
    }
    .card-header {
        border-bottom: 1px solid #f1f5f9 !important;
    }
    .card-footer {
        border-top: 1px solid #f1f5f9 !important;
    }
    .font-10 { font-size: 10px !important; }
    .font-11 { font-size: 11px !important; }
    .font-12 { font-size: 12px !important; }
    .font-13 { font-size: 13px !important; }
    .font-14 { font-size: 14px !important; }
    .font-15 { font-size: 15px !important; }
    .font-16 { font-size: 16px !important; }
</style>
@endpush
