@extends('layouts.sales.app')
@section('title', 'Profil Karyawan & Data Diri - ' . $user->name)

@section('content')
    @php
        $userAvatar = $user->image ? url('/') . '/' . $user->image : asset('assets/img/avatars/1.png');
        $employee = $employee ?? $user->employee;

        // Tenure / Masa Kerja Calculation
        $joinDate = $employee?->join_date ?? ($user->date_in ? \Carbon\Carbon::parse($user->date_in) : null);
        $tenureStr = '—';
        if ($joinDate) {
            $endDate = $employee?->resign_date ?? \Carbon\Carbon::now();
            $years = $joinDate->diffInYears($endDate);
            $months = $joinDate->copy()->addYears($years)->diffInMonths($endDate);
            $tenureStr = "{$years} thn {$months} bln";
        }

        // WhatsApp clean phone
        $rawPhone = $user->phone ?? $employee?->phone;
        $cleanPhone = $rawPhone ? preg_replace('/[^0-9]/', '', $rawPhone) : null;
        if ($cleanPhone && str_starts_with($cleanPhone, '0')) {
            $cleanPhone = '62' . substr($cleanPhone, 1);
        }
    @endphp

    {{-- Breadcrumb --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-3 gap-2">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 text-muted small">
                <li class="breadcrumb-item">
                    <a href="{{ url('/') }}" class="text-muted"><i class="mdi mdi-home-outline me-1"></i>Dashboard</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('employees.index') }}" class="text-muted">HR Management</a>
                </li>
                <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">Profil Karyawan</li>
            </ol>
        </nav>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════════ --}}
    {{-- ── HERO PROFILE CARD (MODERN GLASSMORPHISM & GRADIENT HEADER) ──────── --}}
    {{-- ══════════════════════════════════════════════════════════════════════ --}}
    <div class="card mb-4 border-0 shadow-sm overflow-hidden profile-hero-card" style="border-radius: 18px;">
        {{-- Banner Container with Gradient & Ambient Backdrop --}}
        <div class="profile-banner-wrapper position-relative" style="height: 180px; background: {{ $user->banner ? 'url(' . asset($user->banner) . ') center/cover no-repeat' : 'linear-gradient(135deg, #4f46e5 0%, #7c3aed 50%, #6366f1 100%)' }};">
            <div class="profile-banner-overlay position-absolute top-0 start-0 w-100 h-100" style="background: linear-gradient(180deg, rgba(0,0,0,0.15) 0%, rgba(0,0,0,0.65) 100%);"></div>

            @if(!$user->banner)
                <div class="position-absolute end-0 bottom-0 opacity-15 p-3 user-select-none pointer-events-none">
                    <i class="mdi mdi-shield-account text-white" style="font-size: 160px; margin-right: -25px; margin-bottom: -45px;"></i>
                </div>
            @endif

            {{-- Floating Banner Edit Button --}}
            @if(Auth::id() == $user->id || in_array(Auth::user()->role, ['Admin', 'HRD', 'Super Admin']))
                <button type="button" class="btn btn-sm btn-glass text-white position-absolute end-0 top-0 m-3 shadow-xs d-flex align-items-center gap-1.5" data-bs-toggle="modal" data-bs-target="#changeBannerModal">
                    <i class="mdi mdi-camera-outline fs-5"></i>
                    <span class="d-none d-sm-inline fw-medium">Ubah Banner</span>
                </button>
            @endif
        </div>

        {{-- Profile Details Bar --}}
        <div class="card-body pt-0 pb-4 px-3 px-md-4">
            <div class="d-flex flex-column flex-md-row align-items-center align-items-md-end gap-3 gap-md-4" style="margin-top: -65px;">
                {{-- Avatar with Glowing Status Ring --}}
                <div class="position-relative flex-shrink-0">
                    <div class="profile-avatar-container position-relative">
                        <img src="{{ $userAvatar }}" alt="{{ $user->name }}" class="rounded-circle shadow-lg profile-avatar-img" style="width: 124px; height: 124px; object-fit: cover; border: 4px solid #ffffff;">
                        <span class="position-absolute bottom-0 end-0 p-1.5 bg-{{ $user->active == '1' ? 'success' : 'danger' }} border border-3 border-white rounded-circle status-glow-{{ $user->active == '1' ? 'active' : 'inactive' }}" title="{{ $user->active == '1' ? 'Akun Aktif' : 'Non-Aktif' }}" style="width: 22px; height: 22px;"></span>
                    </div>
                </div>

                {{-- Bio & Title Information --}}
                <div class="flex-grow-1 text-center text-md-start">
                    <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-md-start gap-2 mb-1.5">
                        <h3 class="fw-bold mb-0 text-dark" style="letter-spacing: -0.3px;">{{ $user->name }}</h3>
                        <span class="badge bg-label-primary px-2.5 py-1 rounded-pill fw-bold" style="font-size: 0.75rem;">
                            <i class="mdi mdi-shield-account-outline me-1"></i>{{ $user->role }}
                        </span>
                        @if ($user->active == '1')
                            <span class="badge bg-label-success px-2 py-0.5 rounded-pill" style="font-size: 0.72rem;">
                                <i class="mdi mdi-check-circle-outline me-1"></i>Aktif
                            </span>
                        @else
                            <span class="badge bg-label-danger px-2 py-0.5 rounded-pill" style="font-size: 0.72rem;">
                                Non-Aktif
                            </span>
                        @endif
                    </div>

                    {{-- Badges Metadata Row --}}
                    <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-md-start gap-1.5 mb-2.5">
                        @if ($employee && $employee->department)
                            <span class="badge bg-label-info px-2.5 py-1 rounded-pill fw-semibold">
                                <i class="mdi mdi-domain me-1"></i>{{ $employee->department->name }}
                            </span>
                        @endif
                        @if ($employee && $employee->position)
                            <span class="badge bg-label-secondary px-2.5 py-1 rounded-pill fw-semibold">
                                <i class="mdi mdi-briefcase-outline me-1"></i>{{ $employee->position->name }}
                            </span>
                        @endif
                        @if ($employee && $employee->nik)
                            <span class="badge bg-label-dark px-2.5 py-1 rounded-pill font-monospace cursor-pointer btn-copy-meta" data-copy="{{ $employee->nik }}" title="Klik untuk Salin NIK">
                                <i class="mdi mdi-barcode me-1"></i>NIK: {{ $employee->nik }} <i class="mdi mdi-content-copy ms-1 font-size-10 opacity-75"></i>
                            </span>
                        @elseif ($user->code)
                            <span class="badge bg-label-warning px-2.5 py-1 rounded-pill fw-semibold">
                                <i class="mdi mdi-ticket-confirmation-outline me-1"></i>Code: {{ $user->code }}
                            </span>
                        @endif
                        @if ($user->area)
                            <span class="badge bg-label-warning px-2.5 py-1 rounded-pill fw-semibold">
                                <i class="mdi mdi-map-marker-outline me-1"></i>{{ $user->area }}
                            </span>
                        @endif
                    </div>

                    {{-- Quick Contact Strip --}}
                    <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-md-start gap-3 small text-secondary">
                        <span class="cursor-pointer btn-copy-meta d-inline-flex align-items-center" data-copy="{{ $user->email }}" title="Salin Email">
                            <i class="mdi mdi-email-outline me-1.5 text-primary fs-6"></i>{{ $user->email }}
                        </span>
                        @if ($user->phone)
                            <span class="d-inline-flex align-items-center">
                                <i class="mdi mdi-phone-outline me-1.5 text-success fs-6"></i>{{ $user->phone }}
                            </span>
                        @endif
                        @if ($joinDate)
                            <span class="d-inline-flex align-items-center">
                                <i class="mdi mdi-calendar-check-outline me-1.5 text-info fs-6"></i>Bergabung: {{ $joinDate->format('d M Y') }} ({{ $tenureStr }})
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="d-flex flex-wrap gap-2 align-items-center justify-content-center mt-3 mt-md-0">
                    @if ($cleanPhone)
                        <a href="https://wa.me/{{ $cleanPhone }}" target="_blank" class="btn btn-outline-success fw-semibold shadow-xs">
                            <i class="mdi mdi-whatsapp me-1 fs-5"></i> Chat WA
                        </a>
                    @endif
                    @if ($isOwnProfile || in_array(Auth::user()->role, ['Admin', 'HRD', 'Super Admin']))
                        <a href="{{ route('profile.edit', $user->id) }}" class="btn btn-primary fw-semibold shadow-xs">
                            <i class="mdi mdi-account-edit-outline me-1.5 fs-5"></i> Edit Profil
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════════ --}}
    {{-- ── QUICK KPI STAT CARDS ───────────────────────────────────────────── --}}
    {{-- ══════════════════════════════════════════════════════════════════════ --}}
    <div class="row g-3 mb-4">
        {{-- Card 1: Status Presensi Hari Ini --}}
        <div class="col-6 col-lg-3">
            <div class="card profile-stat-card border-0 shadow-sm h-100">
                <div class="card-body p-3 d-flex flex-column justify-content-between">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted fw-semibold small text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.5px;">Presensi Hari Ini</span>
                        <div class="stat-icon-box bg-label-{{ ($todayAttendance && $todayAttendance->clock_in) ? 'success' : 'warning' }}">
                            <i class="mdi mdi-clock-check-outline"></i>
                        </div>
                    </div>
                    <div>
                        @if ($todayAttendance && $todayAttendance->clock_out)
                            <h4 class="mb-0 fw-bold text-success">{{ substr($todayAttendance->clock_in, 0, 5) }} - {{ substr($todayAttendance->clock_out, 0, 5) }}</h4>
                            <span class="text-muted small d-flex align-items-center gap-1 mt-1">
                                <i class="mdi mdi-check-all text-success"></i> Selesai Pulang
                            </span>
                        @elseif ($todayAttendance && $todayAttendance->clock_in)
                            <h4 class="mb-0 fw-bold text-primary">{{ substr($todayAttendance->clock_in, 0, 5) }} WIB</h4>
                            <span class="text-muted small d-flex align-items-center gap-1 mt-1">
                                <i class="mdi mdi-clock-in text-primary"></i> Sedang Masuk
                            </span>
                        @else
                            <h4 class="mb-0 fw-bold text-secondary">Belum Absen</h4>
                            <span class="text-muted small d-flex align-items-center gap-1 mt-1">
                                <i class="mdi mdi-alert-circle-outline text-warning"></i> Hari Ini
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 2: Sisa Kuota Cuti --}}
        <div class="col-6 col-lg-3">
            <div class="card profile-stat-card border-0 shadow-sm h-100">
                <div class="card-body p-3 d-flex flex-column justify-content-between">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted fw-semibold small text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.5px;">Sisa Cuti {{ date('Y') }}</span>
                        <div class="stat-icon-box bg-label-primary">
                            <i class="mdi mdi-beach"></i>
                        </div>
                    </div>
                    <div>
                        <h4 class="mb-0 fw-bold text-primary">{{ $leaveBalance?->remaining_quota ?? 12 }} Hari</h4>
                        <span class="text-muted small d-flex align-items-center gap-1 mt-1">
                            <i class="mdi mdi-calendar-blank-outline"></i> Kuota: {{ $leaveBalance?->total_quota ?? 12 }} hari
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 3: Alat Kerja / Fasilitas --}}
        <div class="col-6 col-lg-3">
            <div class="card profile-stat-card border-0 shadow-sm h-100">
                <div class="card-body p-3 d-flex flex-column justify-content-between">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted fw-semibold small text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.5px;">Alat Kerja &amp; Fasilitas</span>
                        <div class="stat-icon-box bg-label-warning">
                            <i class="mdi mdi-laptop"></i>
                        </div>
                    </div>
                    <div>
                        <h4 class="mb-0 fw-bold text-warning">{{ $myAssets->count() }} Unit</h4>
                        <span class="text-muted small d-flex align-items-center gap-1 mt-1">
                            <i class="mdi mdi-check-circle-outline"></i> Fasilitas dipegang
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 4: Status Kepegawaian --}}
        <div class="col-6 col-lg-3">
            <div class="card profile-stat-card border-0 shadow-sm h-100">
                <div class="card-body p-3 d-flex flex-column justify-content-between">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted fw-semibold small text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.5px;">Status Karyawan</span>
                        <div class="stat-icon-box bg-label-info">
                            <i class="mdi mdi-account-check-outline"></i>
                        </div>
                    </div>
                    <div>
                        <h4 class="mb-0 fw-bold text-info">{{ $employee?->employment_status ?? 'Aktif' }}</h4>
                        <span class="text-muted small d-flex align-items-center gap-1 mt-1 text-truncate" title="{{ $employee?->position?->name ?? $user->role }}">
                            <i class="mdi mdi-briefcase-outline"></i> {{ $employee?->position?->name ?? $user->role }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════════ --}}
    {{-- ── SALES REPORT TAB STYLE NAVIGATION (NAV-PILLS-CUSTOM) ────────────── --}}
    {{-- ══════════════════════════════════════════════════════════════════════ --}}
    @php
        $activeTab = request('tab', (request()->has('month') || request()->has('year') || request()->has('att_month')) ? 'tabAttendance' : 'tabProfileData');
    @endphp
    <div class="mb-4">
        <ul class="nav nav-pills nav-pills-custom flex-wrap gap-2 mb-3" id="profileTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link {{ $activeTab === 'tabProfileData' ? 'active' : '' }} d-flex align-items-center gap-2" id="tab-profile-data-btn" data-bs-toggle="tab" data-bs-target="#tabProfileData" type="button" role="tab" aria-controls="tabProfileData" aria-selected="{{ $activeTab === 'tabProfileData' ? 'true' : 'false' }}">
                    <i class="mdi mdi-card-account-details-outline"></i>
                    <span>Data Karyawan</span>
                </button>
            </li>
            @if ($employee)
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ $activeTab === 'tabAttendance' ? 'active' : '' }} d-flex align-items-center gap-2" id="tab-attendance-btn" data-bs-toggle="tab" data-bs-target="#tabAttendance" type="button" role="tab" aria-controls="tabAttendance" aria-selected="{{ $activeTab === 'tabAttendance' ? 'true' : 'false' }}">
                        <i class="mdi mdi-calendar-clock-outline"></i>
                        <span>Presensi</span>
                        <span class="badge rounded-pill bg-label-info">{{ $monthAttendances->count() }}</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ $activeTab === 'tabLeave' ? 'active' : '' }} d-flex align-items-center gap-2" id="tab-leave-btn" data-bs-toggle="tab" data-bs-target="#tabLeave" type="button" role="tab" aria-controls="tabLeave" aria-selected="{{ $activeTab === 'tabLeave' ? 'true' : 'false' }}">
                        <i class="mdi mdi-calendar-remove-outline"></i>
                        <span>Cuti &amp; Izin</span>
                        <span class="badge rounded-pill bg-label-primary">{{ $leaveBalance?->remaining_quota ?? 12 }} Hari</span>
                    </button>
                </li>
                @if ($isOwnProfile || $isAdminOrHr)
                    <li class="nav-item" role="presentation">
                        <button class="nav-link d-flex align-items-center gap-2" id="tab-payslip-btn" data-bs-toggle="tab" data-bs-target="#tabPayslip" type="button" role="tab" aria-controls="tabPayslip" aria-selected="false">
                            <i class="mdi mdi-cash-multiple"></i>
                            <span>Slip Gaji</span>
                            <span class="badge rounded-pill bg-label-success">{{ $myPayslips->count() }}</span>
                        </button>
                    </li>
                @endif
                <li class="nav-item" role="presentation">
                    <button class="nav-link d-flex align-items-center gap-2" id="tab-assets-btn" data-bs-toggle="tab" data-bs-target="#tabAssets" type="button" role="tab" aria-controls="tabAssets" aria-selected="false">
                        <i class="mdi mdi-laptop"></i>
                        <span>Alat Kerja</span>
                        <span class="badge rounded-pill bg-label-warning">{{ $myAssets->count() }}</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link d-flex align-items-center gap-2" id="tab-claims-btn" data-bs-toggle="tab" data-bs-target="#tabClaims" type="button" role="tab" aria-controls="tabClaims" aria-selected="false">
                        <i class="mdi mdi-receipt-text-outline"></i>
                        <span>Klaim Biaya</span>
                        <span class="badge rounded-pill bg-label-secondary">{{ $myReimbursements->count() }}</span>
                    </button>
                </li>
            @endif
            @if ($isSales)
                <li class="nav-item" role="presentation">
                    <button class="nav-link d-flex align-items-center gap-2" id="tab-sales-btn" data-bs-toggle="tab" data-bs-target="#tabSales" type="button" role="tab" aria-controls="tabSales" aria-selected="false">
                        <i class="mdi mdi-chart-areaspline"></i>
                        <span>Kinerja Sales</span>
                        <span class="badge rounded-pill bg-label-primary">{{ $salesMetrics['totalQuotations'] ?? 0 }}</span>
                    </button>
                </li>
            @endif
        </ul>

        {{-- ══════════════════════════════════════════════════════════════════ --}}
        {{-- ── TAB CONTENTS ─────────────────────────────────────────────────── --}}
        {{-- ══════════════════════════════════════════════════════════════════ --}}
        <div class="tab-content p-0 bg-transparent border-0 mt-3">
            {{-- ────────────────────────────────────────────────────────────────── --}}
            {{-- ── TAB 1: DATA PRIBADI & KEPEGAWAIAN ─────────────────────────── --}}
            {{-- ────────────────────────────────────────────────────────────────── --}}
            <div class="tab-pane fade {{ $activeTab === 'tabProfileData' ? 'show active' : '' }}" id="tabProfileData" role="tabpanel">
                <div class="row g-3">
                    {{-- Section A: Informasi Pribadi & Kontak --}}
                    <div class="col-12 col-md-6">
                        <div class="card border-0 shadow-sm h-100 rounded-4">
                            <div class="card-header bg-transparent py-3 border-bottom d-flex align-items-center gap-2">
                                <div class="avatar avatar-xs bg-label-primary rounded p-1 d-flex align-items-center justify-content-center">
                                    <i class="mdi mdi-account-circle-outline fs-5"></i>
                                </div>
                                <h6 class="card-title mb-0 fw-bold text-dark">Data Pribadi &amp; Kontak</h6>
                            </div>
                            <div class="card-body p-3">
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2.5 border-bottom">
                                        <span class="text-muted small">Nama Lengkap</span>
                                        <span class="fw-semibold text-dark">{{ $user->name }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2.5 border-bottom">
                                        <span class="text-muted small">Nomor Induk Karyawan (NIK)</span>
                                        <div class="d-flex align-items-center gap-1.5">
                                            <span class="font-monospace fw-bold text-primary">{{ $employee?->nik ?: ($user->nip ?: '—') }}</span>
                                            @if ($employee?->nik || $user->nip)
                                                <button class="btn btn-xs btn-outline-secondary p-1 rounded btn-copy-meta" data-copy="{{ $employee?->nik ?: $user->nip }}" title="Salin NIK">
                                                    <i class="mdi mdi-content-copy font-size-11"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2.5 border-bottom">
                                        <span class="text-muted small">Email Login</span>
                                        <div class="d-flex align-items-center gap-1.5">
                                            <span class="fw-semibold text-dark">{{ $user->email }}</span>
                                            <button class="btn btn-xs btn-outline-secondary p-1 rounded btn-copy-meta" data-copy="{{ $user->email }}" title="Salin Email">
                                                <i class="mdi mdi-content-copy font-size-11"></i>
                                            </button>
                                        </div>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2.5 border-bottom">
                                        <span class="text-muted small">Nomor Telepon / WA</span>
                                        <div class="d-flex align-items-center gap-1.5">
                                            @if ($rawPhone)
                                                <span class="fw-semibold text-dark">{{ $rawPhone }}</span>
                                                @if ($cleanPhone)
                                                    <a href="https://wa.me/{{ $cleanPhone }}" target="_blank" class="btn btn-xs btn-success py-0.5 px-2 rounded-pill shadow-xs" title="Chat WA">
                                                        <i class="mdi mdi-whatsapp"></i>
                                                    </a>
                                                @endif
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </div>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2.5 border-bottom">
                                        <span class="text-muted small">Tanggal Lahir</span>
                                        <span class="fw-semibold text-dark">
                                            {{ $employee?->birthday ? $employee->birthday->translatedFormat('d F Y') : ($user->birthday ? \Carbon\Carbon::parse($user->birthday)->translatedFormat('d F Y') : '—') }}
                                        </span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-start px-0 py-2.5 border-0">
                                        <span class="text-muted small">Alamat Domisili</span>
                                        <span class="fw-semibold text-end text-dark" style="max-width: 260px;">
                                            {{ $employee?->address ?: ($user->address ?: '—') }}
                                        </span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    {{-- Section B: Informasi Kepegawaian & Jabatan --}}
                    <div class="col-12 col-md-6">
                        <div class="card border-0 shadow-sm h-100 rounded-4">
                            <div class="card-header bg-transparent py-3 border-bottom d-flex align-items-center gap-2">
                                <div class="avatar avatar-xs bg-label-info rounded p-1 d-flex align-items-center justify-content-center">
                                    <i class="mdi mdi-briefcase-outline fs-5"></i>
                                </div>
                                <h6 class="card-title mb-0 fw-bold text-dark">Data Kepegawaian &amp; Posisi</h6>
                            </div>
                            <div class="card-body p-3">
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2.5 border-bottom">
                                        <span class="text-muted small">Departemen / Divisi</span>
                                        <span class="badge bg-label-info fw-semibold">{{ $employee?->department?->name ?: '—' }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2.5 border-bottom">
                                        <span class="text-muted small">Jabatan / Posisi</span>
                                        <span class="badge bg-label-primary fw-semibold">{{ $employee?->position?->name ?: ($user->role ?: '—') }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2.5 border-bottom">
                                        <span class="text-muted small">Status Kepegawaian</span>
                                        <span class="badge bg-label-success">{{ $employee?->employment_status ?: 'Tetap' }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2.5 border-bottom">
                                        <span class="text-muted small">Tanggal Masuk (Join Date)</span>
                                        <span class="fw-semibold text-dark">{{ $joinDate ? $joinDate->translatedFormat('d F Y') : '—' }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2.5 border-bottom">
                                        <span class="text-muted small">Masa Kerja (Tenure)</span>
                                        <span class="badge bg-label-secondary font-monospace">{{ $tenureStr }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2.5 border-0">
                                        <span class="text-muted small">Hak Akses Presensi Online</span>
                                        <span class="badge bg-label-{{ ($employee?->can_online_attendance ?? true) ? 'success' : 'warning' }}">
                                            <i class="mdi mdi-{{ ($employee?->can_online_attendance ?? true) ? 'check' : 'close' }} me-1"></i>
                                            {{ ($employee?->can_online_attendance ?? true) ? 'Diizinkan Online' : 'Khusus Mesin Kantor' }}
                                        </span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    {{-- Section C: Info Rekening Payroll & BPJS --}}
                    @if ($employee && $employee->salary && ($isOwnProfile || $isAdminOrHr))
                        <div class="col-12">
                            <div class="card border-0 shadow-sm rounded-4">
                                <div class="card-header bg-transparent py-3 border-bottom d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="avatar avatar-xs bg-label-success rounded p-1 d-flex align-items-center justify-content-center">
                                            <i class="mdi mdi-bank fs-5"></i>
                                        </div>
                                        <h6 class="card-title mb-0 fw-bold text-dark">Informasi Rekening &amp; Finansial</h6>
                                    </div>
                                    <span class="badge bg-label-success rounded-pill px-3 py-1">
                                        <i class="mdi mdi-shield-check-outline me-1"></i>Rekening Payroll Aktif
                                    </span>
                                </div>
                                <div class="card-body p-3">
                                    <div class="row g-3">
                                        <div class="col-12 col-md-4">
                                            <div class="p-3 bg-light rounded-3 border h-100">
                                                <span class="text-muted small d-block mb-1">Nama Bank &amp; No. Rekening</span>
                                                <div class="fw-bold text-dark fs-6 d-flex align-items-center justify-content-between">
                                                    <span>{{ $employee->salary->bank_name ?: 'BCA' }} — {{ $employee->salary->bank_account ?: '-' }}</span>
                                                    @if ($employee->salary->bank_account)
                                                        <button class="btn btn-xs btn-outline-secondary p-1 rounded btn-copy-meta" data-copy="{{ $employee->salary->bank_account }}" title="Salin Rekening">
                                                            <i class="mdi mdi-content-copy font-size-11"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                                <small class="text-muted">a.n {{ $employee->salary->bank_holder ?: $user->name }}</small>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-4">
                                            <div class="p-3 bg-light rounded-3 border h-100">
                                                <span class="text-muted small d-block mb-1">Nomor BPJS Kesehatan</span>
                                                <div class="font-monospace fw-bold text-primary fs-6 d-flex align-items-center justify-content-between">
                                                    <span>{{ $employee->salary->bpjs_kesehatan_number ?: '—' }}</span>
                                                    @if ($employee->salary->bpjs_kesehatan_number)
                                                        <button class="btn btn-xs btn-outline-secondary p-1 rounded btn-copy-meta" data-copy="{{ $employee->salary->bpjs_kesehatan_number }}" title="Salin BPJS">
                                                            <i class="mdi mdi-content-copy font-size-11"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                                <small class="text-muted">Status: {{ $employee->salary->bpjs_kesehatan_number ? 'Terdaftar' : 'Belum Didaftarkan' }}</small>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-4">
                                            <div class="p-3 bg-light rounded-3 border h-100">
                                                <span class="text-muted small d-block mb-1">Nomor BPJS Ketenagakerjaan</span>
                                                <div class="font-monospace fw-bold text-primary fs-6 d-flex align-items-center justify-content-between">
                                                    <span>{{ $employee->salary->bpjs_ketenagakerjaan_number ?: '—' }}</span>
                                                    @if ($employee->salary->bpjs_ketenagakerjaan_number)
                                                        <button class="btn btn-xs btn-outline-secondary p-1 rounded btn-copy-meta" data-copy="{{ $employee->salary->bpjs_ketenagakerjaan_number }}" title="Salin BPJS TK">
                                                            <i class="mdi mdi-content-copy font-size-11"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                                <small class="text-muted">Status: {{ $employee->salary->bpjs_ketenagakerjaan_number ? 'Terdaftar' : 'Belum Didaftarkan' }}</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            @if ($employee)
                {{-- ────────────────────────────────────────────────────────────── --}}
                {{-- ── TAB 2: PRESENSI & HISTORI BULANAN ───────────────────────── --}}
                {{-- ────────────────────────────────────────────────────────────── --}}
                <div class="tab-pane fade {{ $activeTab === 'tabAttendance' ? 'show active' : '' }}" id="tabAttendance" role="tabpanel">
                    {{-- Clock In / Clock Out Action Widget --}}
                    @if ($isOwnProfile)
                        <div class="card border-0 shadow-sm mb-4 attendance-action-banner text-white rounded-4 overflow-hidden">
                            <div class="card-body p-4 d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                                <div>
                                    <div class="d-flex align-items-center gap-2 mb-1.5 flex-wrap">
                                        <span class="badge bg-white text-primary fw-bold px-2.5 py-1 rounded-pill">
                                            <i class="mdi mdi-calendar-today me-1"></i>{{ \Carbon\Carbon::now('Asia/Jakarta')->translatedFormat('l, d F Y') }}
                                        </span>
                                        <span class="badge bg-black bg-opacity-25 text-white font-monospace px-3 py-1 rounded-pill" id="liveTimeClock">--:--:-- WIB</span>
                                    </div>
                                    <h4 class="fw-bold text-white mb-1">Presensi Kerja Online</h4>
                                    <p class="mb-0 text-white opacity-85 small">Catat waktu kehadiran masuk dan kepulangan kerja Anda secara digital.</p>
                                </div>
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    @if (!$employee->can_online_attendance)
                                        <span class="badge bg-white text-secondary py-2.5 px-3.5 fs-7 rounded-pill shadow-sm">
                                            <i class="mdi mdi-shield-account-outline me-1 text-primary"></i> Bebas Presensi Online
                                        </span>
                                    @elseif (!$todayAttendance || !$todayAttendance->clock_in)
                                        <button type="button" class="btn btn-success btn-lg shadow fw-bold px-4 rounded-pill waves-effect" data-bs-toggle="modal" data-bs-target="#navClockInModal">
                                            <i class="mdi mdi-clock-in me-1.5"></i> Presensi Masuk (Clock In)
                                        </button>
                                    @elseif ($todayAttendance && !$todayAttendance->clock_out)
                                        <button type="button" class="btn btn-warning btn-lg shadow fw-bold px-4 rounded-pill waves-effect" data-bs-toggle="modal" data-bs-target="#navClockOutModal">
                                            <i class="mdi mdi-clock-out me-1.5"></i> Presensi Pulang (Clock Out)
                                        </button>
                                    @else
                                        <span class="badge bg-success py-2.5 px-3.5 fs-7 rounded-pill shadow-sm">
                                            <i class="mdi mdi-check-all me-1"></i> Presensi Hari Ini Selesai ({{ substr($todayAttendance->clock_in, 0, 5) }} - {{ substr($todayAttendance->clock_out, 0, 5) }})
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- ── MONTH FILTER & PERIOD CONTROLLER CARD ─────────────────── --}}
                    <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
                        <div class="card-body p-3 p-md-4">
                            <div class="d-flex flex-column flex-lg-row align-items-start align-items-lg-center justify-content-between gap-3 pb-3 border-bottom">
                                {{-- Current Selected Month Title & Badges --}}
                                <div>
                                    <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                                        <h5 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                                            <i class="mdi mdi-calendar-month-outline text-primary"></i>
                                            Periode: {{ $selectedPeriod->locale('id')->translatedFormat('F Y') }}
                                        </h5>
                                        @if ($isCurrentRunningMonth)
                                            <span class="badge bg-label-primary rounded-pill px-2.5 py-1 fw-bold">
                                                <i class="mdi mdi-clock-check-outline me-1"></i>Bulan Berjalan
                                            </span>
                                        @else
                                            <span class="badge bg-label-secondary rounded-pill px-2.5 py-1">
                                                <i class="mdi mdi-history me-1"></i>Arsip Histori Bulan
                                            </span>
                                        @endif
                                    </div>
                                    <small class="text-muted">Menampilkan seluruh data kehadiran, waktu masuk, pulang, telat, dan lemur pada periode ini.</small>
                                </div>

                                {{-- Period Filter & Navigator Toolbar --}}
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    {{-- Prev Month Button --}}
                                    <a href="{{ route('profile.show', ['profile' => $user->id, 'month' => $prevPeriod->month, 'year' => $prevPeriod->year]) }}#tabAttendance" 
                                       class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1 shadow-xs" title="Bulan Sebelumnya ({{ $prevPeriod->locale('id')->translatedFormat('M Y') }})">
                                        <i class="mdi mdi-chevron-left"></i>
                                        <span class="d-none d-sm-inline">{{ $prevPeriod->locale('id')->translatedFormat('M Y') }}</span>
                                    </a>

                                    {{-- Select Dropdown Form --}}
                                    <form method="GET" action="{{ route('profile.show', $user->id) }}#tabAttendance" class="d-flex align-items-center gap-1.5 m-0" id="filterAttMonthForm">
                                        <select name="month" class="form-select form-select-sm fw-semibold" onchange="document.getElementById('filterAttMonthForm').submit()" style="min-width: 130px;">
                                            @for($m = 1; $m <= 12; $m++)
                                                @php
                                                    $mName = \Carbon\Carbon::createFromDate($selectedYear, $m, 1)->locale('id')->translatedFormat('F');
                                                @endphp
                                                <option value="{{ $m }}" {{ $selectedMonth == $m ? 'selected' : '' }}>
                                                    {{ $mName }}
                                                </option>
                                            @endfor
                                        </select>
                                        <select name="year" class="form-select form-select-sm fw-semibold" onchange="document.getElementById('filterAttMonthForm').submit()" style="min-width: 90px;">
                                            @for($y = \Carbon\Carbon::now('Asia/Jakarta')->year + 1; $y >= 2022; $y--)
                                                <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                                            @endfor
                                        </select>
                                    </form>

                                    {{-- Reset to Current Running Month if viewing past --}}
                                    @if (!$isCurrentRunningMonth)
                                        <a href="{{ route('profile.show', $user->id) }}#tabAttendance" class="btn btn-sm btn-primary d-flex align-items-center gap-1 shadow-xs" title="Kembali ke Bulan Berjalan">
                                            <i class="mdi mdi-calendar-today"></i>
                                            <span class="d-none d-md-inline">Bulan Berjalan</span>
                                        </a>
                                    @endif

                                    {{-- Next Month Button --}}
                                    <a href="{{ route('profile.show', ['profile' => $user->id, 'month' => $nextPeriod->month, 'year' => $nextPeriod->year]) }}#tabAttendance" 
                                       class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1 shadow-xs" title="Bulan Berikutnya ({{ $nextPeriod->locale('id')->translatedFormat('M Y') }})">
                                        <span class="d-none d-sm-inline">{{ $nextPeriod->locale('id')->translatedFormat('M Y') }}</span>
                                        <i class="mdi mdi-chevron-right"></i>
                                    </a>
                                </div>
                            </div>

                            {{-- Available Historical Period Quick Pills (1-Click Switch) --}}
                            @if(isset($availableAttendancePeriods) && $availableAttendancePeriods->isNotEmpty())
                                <div class="d-flex align-items-center gap-2 flex-wrap pt-3">
                                    <small class="text-muted fw-bold d-flex align-items-center gap-1">
                                        <i class="mdi mdi-history text-secondary"></i> Arsip Bulan Tersedia:
                                    </small>
                                    @foreach($availableAttendancePeriods as $p)
                                        @php
                                            $pDate = \Carbon\Carbon::createFromDate($p->year, $p->month, 1);
                                            $isActiveP = ($selectedMonth == $p->month && $selectedYear == $p->year);
                                        @endphp
                                        <a href="{{ route('profile.show', ['profile' => $user->id, 'month' => $p->month, 'year' => $p->year]) }}#tabAttendance" 
                                           class="badge {{ $isActiveP ? 'bg-primary text-white shadow-sm' : 'bg-label-secondary' }} text-decoration-none py-1.5 px-2.5 rounded-pill d-inline-flex align-items-center gap-1.5 transition-all">
                                            <span>{{ $pDate->locale('id')->translatedFormat('M Y') }}</span>
                                            <span class="badge rounded-pill {{ $isActiveP ? 'bg-white text-primary' : 'bg-secondary text-white' }}" style="font-size: 10px;">{{ $p->total_days }}h</span>
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- ── MONTHLY SUMMARY STAT CARDS (4 METRICS) ───────────────── --}}
                    <div class="row g-3 mb-4">
                        {{-- Metric 1: Total Hari Hadir --}}
                        <div class="col-6 col-md-3">
                            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                                <div class="card-body p-3">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <span class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem;">Total Hadir</span>
                                        <span class="badge bg-label-success p-2 rounded-circle">
                                            <i class="mdi mdi-account-check fs-5"></i>
                                        </span>
                                    </div>
                                    <h4 class="mb-0 fw-bold text-dark">{{ $attStats['totalHadir'] ?? 0 }} <small class="text-muted fs-6">Hari</small></h4>
                                    <small class="text-muted d-block mt-1">Presensi status hadir</small>
                                </div>
                            </div>
                        </div>

                        {{-- Metric 2: Tepat Waktu --}}
                        <div class="col-6 col-md-3">
                            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                                <div class="card-body p-3">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <span class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem;">Tepat Waktu</span>
                                        <span class="badge bg-label-primary p-2 rounded-circle">
                                            <i class="mdi mdi-timer-check-outline fs-5"></i>
                                        </span>
                                    </div>
                                    <h4 class="mb-0 fw-bold text-primary">{{ $attStats['totalOnTime'] ?? 0 }} <small class="text-muted fs-6">Hari</small></h4>
                                    <small class="text-muted d-block mt-1">Masuk sebelum toleransi</small>
                                </div>
                            </div>
                        </div>

                        {{-- Metric 3: Terlambat --}}
                        <div class="col-6 col-md-3">
                            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                                <div class="card-body p-3">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <span class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem;">Terlambat</span>
                                        <span class="badge bg-label-warning p-2 rounded-circle">
                                            <i class="mdi mdi-clock-alert-outline fs-5"></i>
                                        </span>
                                    </div>
                                    <h4 class="mb-0 fw-bold text-warning">{{ $attStats['totalLate'] ?? 0 }} <small class="text-muted fs-6">Hari</small></h4>
                                    <small class="text-muted d-block mt-1">Akumulasi: {{ $attStats['totalLateMins'] ?? 0 }} Menit</small>
                                </div>
                            </div>
                        </div>

                        {{-- Metric 4: Lembur --}}
                        <div class="col-6 col-md-3">
                            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                                <div class="card-body p-3">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <span class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem;">Lembur</span>
                                        <span class="badge bg-label-info p-2 rounded-circle">
                                            <i class="mdi mdi-briefcase-clock-outline fs-5"></i>
                                        </span>
                                    </div>
                                    <h4 class="mb-0 fw-bold text-info">{{ round(($attStats['totalOvertimeMins'] ?? 0) / 60, 1) }} <small class="text-muted fs-6">Jam</small></h4>
                                    <small class="text-muted d-block mt-1">Total: {{ $attStats['totalOvertimeMins'] ?? 0 }} Menit</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ── TABEL RIWAYAT PRESENSI LENGKAP ───────────────────────── --}}
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                        <div class="card-header border-bottom py-3 bg-white d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <div>
                                <h6 class="card-title mb-0 fw-bold text-dark">
                                    <i class="mdi mdi-format-list-bulleted text-primary me-1"></i>
                                    Rincian Presensi: {{ $selectedPeriod->locale('id')->translatedFormat('F Y') }}
                                </h6>
                                <small class="text-muted">Daftar rekaman waktu masuk, pulang, dan durasi per tanggal.</small>
                            </div>
                            <span class="badge bg-label-primary px-3 py-1.5 rounded-pill fw-semibold">{{ $monthAttendances->count() }} Hari Tercatat</span>
                        </div>
                        <div class="table-responsive text-nowrap">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-3">Tanggal &amp; Hari</th>
                                        <th>Jam Masuk</th>
                                        <th>Jam Pulang</th>
                                        <th>Durasi Kerja</th>
                                        <th>Tipe Kerja</th>
                                        <th>Status Kehadiran</th>
                                        <th>Keterlambatan / Lembur</th>
                                        <th class="pe-3">Catatan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($monthAttendances as $att)
                                        @php
                                            $workDurationStr = '-';
                                            if ($att->clock_in && $att->clock_out) {
                                                $cin = \Carbon\Carbon::parse($att->clock_in);
                                                $cout = \Carbon\Carbon::parse($att->clock_out);
                                                $durHours = $cin->diffInHours($cout);
                                                $durMins = $cin->diffInMinutes($cout) % 60;
                                                $workDurationStr = "{$durHours}j {$durMins}m";
                                            }
                                        @endphp
                                        <tr>
                                            <td class="ps-3">
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="avatar avatar-xs bg-label-secondary rounded p-1 d-flex align-items-center justify-content-center">
                                                        <i class="mdi mdi-calendar-text"></i>
                                                    </div>
                                                    <div>
                                                        <span class="fw-semibold text-dark d-block">{{ \Carbon\Carbon::parse($att->date)->translatedFormat('l, d M Y') }}</span>
                                                        <small class="text-muted font-monospace">{{ \Carbon\Carbon::parse($att->date)->format('d/m/Y') }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @if ($att->clock_in)
                                                    <span class="badge bg-label-dark font-monospace px-2.5 py-1.5 fs-7">
                                                        <i class="mdi mdi-clock-in text-primary me-1"></i>{{ substr($att->clock_in, 0, 5) }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($att->clock_out)
                                                    <span class="badge bg-label-dark font-monospace px-2.5 py-1.5 fs-7">
                                                        <i class="mdi mdi-clock-out text-danger me-1"></i>{{ substr($att->clock_out, 0, 5) }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($workDurationStr !== '-')
                                                    <span class="badge bg-label-primary rounded-pill font-monospace">{{ $workDurationStr }}</span>
                                                @else
                                                    <span class="text-muted small">—</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-label-secondary rounded-pill small">
                                                    {{ $att->work_type ?: 'WFO' }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-label-{{ $att->status === 'Hadir' ? 'success' : ($att->status === 'Izin' ? 'info' : ($att->status === 'Sakit' ? 'warning' : 'danger')) }} rounded-pill px-2.5 py-1">
                                                    {{ $att->status }}
                                                </span>
                                            </td>
                                            <td>
                                                @if ($att->late_minutes > 0)
                                                    <span class="badge bg-label-warning me-1 rounded-pill" title="Terlambat {{ $att->late_minutes }} menit">
                                                        <i class="mdi mdi-clock-alert-outline me-0.5"></i> Telat {{ $att->late_minutes }}m
                                                    </span>
                                                @endif
                                                @if ($att->overtime_minutes > 0)
                                                    <span class="badge bg-label-info rounded-pill" title="Lembur {{ $att->overtime_minutes }} menit">
                                                        <i class="mdi mdi-plus-circle-outline me-0.5"></i> Lembur {{ round($att->overtime_minutes / 60, 1) }}j
                                                    </span>
                                                @endif
                                                @if ($att->late_minutes == 0 && $att->overtime_minutes == 0)
                                                    <span class="badge bg-label-success rounded-pill small">
                                                        <i class="mdi mdi-check me-0.5"></i> Tepat Waktu
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="pe-3">
                                                <small class="text-muted text-truncate d-inline-block" style="max-width: 160px;" title="{{ $att->notes }}">
                                                    {{ $att->notes ?: '—' }}
                                                </small>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center py-5">
                                                <div class="d-flex flex-column align-items-center justify-content-center">
                                                    <div class="avatar avatar-lg bg-label-secondary rounded-circle mb-3 d-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
                                                        <i class="mdi mdi-calendar-blank-outline fs-3 text-muted"></i>
                                                    </div>
                                                    <h6 class="fw-bold text-dark mb-1">Belum Ada Riwayat Presensi</h6>
                                                    <p class="text-muted small mb-0">Tidak ditemukan rekaman presensi pada periode <strong>{{ $selectedPeriod->locale('id')->translatedFormat('F Y') }}</strong>.</p>
                                                    @if (!$isCurrentRunningMonth)
                                                        <a href="{{ route('profile.show', $user->id) }}#tabAttendance" class="btn btn-sm btn-primary mt-3 shadow-xs">
                                                            <i class="mdi mdi-calendar-today me-1"></i> Kembali ke Bulan Berjalan
                                                        </a>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- ────────────────────────────────────────────────────────────── --}}
                {{-- ── TAB 3: CUTI & IZIN ───────────────────────────────────────── --}}
                {{-- ────────────────────────────────────────────────────────────── --}}
                <div class="tab-pane fade" id="tabLeave" role="tabpanel">
                    <div class="card border-0 shadow-sm mb-4 rounded-4 overflow-hidden">
                        <div class="card-header border-bottom py-3 bg-white d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title mb-0 fw-bold text-dark">Pengajuan &amp; Riwayat Cuti</h6>
                                <small class="text-muted">Sisa Kuota: <strong>{{ $leaveBalance?->remaining_quota ?? 12 }} Hari</strong></small>
                            </div>
                            @if ($isOwnProfile)
                                <button type="button" class="btn btn-sm btn-primary fw-semibold shadow-xs rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalEssNewLeave">
                                    <i class="mdi mdi-plus me-1"></i> Ajukan Cuti / Izin
                                </button>
                            @endif
                        </div>
                        <div class="table-responsive text-nowrap">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Jenis Cuti</th>
                                        <th>Tanggal Mulai - Selesai</th>
                                        <th>Durasi</th>
                                        <th>Alasan / Keperluan</th>
                                        <th>Status Pengajuan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($myLeaves as $leave)
                                        <tr>
                                            <td>
                                                <span class="fw-semibold text-dark">{{ $leave->leaveType?->name ?? 'Cuti Tahunan' }}</span>
                                            </td>
                                            <td>
                                                {{ \Carbon\Carbon::parse($leave->start_date)->format('d M Y') }} - {{ \Carbon\Carbon::parse($leave->end_date)->format('d M Y') }}
                                            </td>
                                            <td>
                                                <span class="badge bg-label-info rounded-pill px-2.5 py-1">{{ $leave->total_days }} Hari</span>
                                            </td>
                                            <td>
                                                <span class="small text-muted text-truncate d-block" style="max-width: 260px;">{{ $leave->reason }}</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-label-{{ $leave->status === 'Approved' ? 'success' : ($leave->status === 'Pending' ? 'warning' : 'danger') }} rounded-pill px-2.5 py-1">
                                                    {{ $leave->status }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-4 text-muted">Belum ada riwayat pengajuan cuti.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- ────────────────────────────────────────────────────────────── --}}
                {{-- ── TAB 4: SLIP GAJI ─────────────────────────────────────────── --}}
                {{-- ────────────────────────────────────────────────────────────── --}}
                @if ($isOwnProfile || $isAdminOrHr)
                    <div class="tab-pane fade" id="tabPayslip" role="tabpanel">
                        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                            <div class="card-header border-bottom py-3 bg-white d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-title mb-0 fw-bold text-dark">Arsip Slip Gaji Digital</h6>
                                    <small class="text-muted">Dokumen resmi slip gaji bulanan</small>
                                </div>
                                <span class="badge bg-label-success rounded-pill px-3 py-1.5">{{ $myPayslips->count() }} Slip Tersedia</span>
                            </div>
                            <div class="table-responsive text-nowrap">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Periode Gaji</th>
                                            <th>Gaji Pokok &amp; Tunjangan</th>
                                            <th>Total Potongan</th>
                                            <th>Gaji Bersih (THP)</th>
                                            <th class="text-end">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($myPayslips as $item)
                                            <tr>
                                                <td>
                                                    <div class="fw-bold text-dark">{{ $item->payroll?->period_name ?? 'Periode Gaji' }}</div>
                                                    <small class="text-muted">{{ $item->payroll ? \Carbon\Carbon::parse($item->payroll->payment_date)->format('d M Y') : '-' }}</small>
                                                </td>
                                                <td>
                                                    <span class="fw-semibold text-dark">Rp {{ number_format((float)($item->basic_salary ?? 0) + (float)($item->allowances_total ?? 0), 0, ',', '.') }}</span>
                                                </td>
                                                <td>
                                                    <span class="text-danger fw-semibold">Rp {{ number_format((float)($item->deductions_total ?? 0), 0, ',', '.') }}</span>
                                                </td>
                                                <td>
                                                    <span class="fw-bold text-success fs-6">Rp {{ number_format((float)($item->net_salary ?? 0), 0, ',', '.') }}</span>
                                                </td>
                                                <td class="text-end">
                                                    <a href="{{ route('hr.payrolls.slip', $item->id) }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill shadow-xs">
                                                        <i class="mdi mdi-printer me-1"></i> Cetak Slip
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-4 text-muted">Belum ada slip gaji yang digenerate untuk akun ini.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- ────────────────────────────────────────────────────────────── --}}
                {{-- ── TAB 5: ALAT KERJA & FASILITAS (FIXED ASSET) ──────────────── --}}
                {{-- ────────────────────────────────────────────────────────────── --}}
                <div class="tab-pane fade" id="tabAssets" role="tabpanel">
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                        <div class="card-header border-bottom py-3 bg-white d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title mb-0 fw-bold text-dark">Inventaris Alat Kerja &amp; Fasilitas Terdaftar</h6>
                                <small class="text-muted">Aset &amp; alat kerja operasional yang sedang dipegang oleh karyawan</small>
                            </div>
                            <span class="badge bg-label-warning rounded-pill px-3 py-1.5">{{ $myAssets->count() }} Unit Dipegang</span>
                        </div>
                        <div class="table-responsive text-nowrap">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Nama Alat / Aset</th>
                                        <th>Kode Aset &amp; S/N</th>
                                        <th>Kondisi Fisik</th>
                                        <th>Tanggal Penyerahan</th>
                                        <th>Catatan Kelengkapan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($myAssets as $asset)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center gap-1.5 mb-0.5">
                                                    @if ($asset->fixedAsset)
                                                        <span class="badge bg-label-info py-0.5 px-2 rounded-pill font-size-11">
                                                            <i class="mdi mdi-link-variant me-1"></i>{{ $asset->fixedAsset->type }}
                                                        </span>
                                                    @endif
                                                    <div class="fw-bold text-dark">{{ $asset->asset_name }}</div>
                                                </div>
                                            </td>
                                            <td>
                                                @if ($asset->fixed_asset_id)
                                                    <a href="{{ route('fixed.show', $asset->fixed_asset_id) }}" target="_blank" class="font-monospace text-primary fw-semibold d-inline-flex align-items-center gap-1">
                                                        {{ $asset->asset_code ?: ($asset->fixedAsset->code ?? '-') }}
                                                        <i class="mdi mdi-open-in-new font-size-11"></i>
                                                    </a>
                                                @else
                                                    <span class="font-monospace text-primary">{{ $asset->asset_code ?: '-' }}</span>
                                                @endif
                                                <div class="font-monospace text-muted small">S/N: {{ $asset->serial_number ?: '-' }}</div>
                                            </td>
                                            <td>
                                                <span class="badge bg-label-{{ $asset->condition === 'Baik' ? 'success' : ($asset->condition === 'Normal' ? 'info' : 'warning') }} rounded-pill px-2.5 py-1">
                                                    {{ $asset->condition }}
                                                </span>
                                            </td>
                                            <td>{{ \Carbon\Carbon::parse($asset->handover_date)->format('d M Y') }}</td>
                                            <td><span class="small text-muted">{{ $asset->notes ?: 'Lengkap' }}</span></td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-4 text-muted">Tidak ada catatan peminjaman alat kerja saat ini.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- ────────────────────────────────────────────────────────────── --}}
                {{-- ── TAB 6: REIMBURSEMENT (KLAIM BIAYA) ───────────────────────── --}}
                {{-- ────────────────────────────────────────────────────────────── --}}
                <div class="tab-pane fade" id="tabClaims" role="tabpanel">
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                        <div class="card-header border-bottom py-3 bg-white d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title mb-0 fw-bold text-dark">Riwayat Klaim Reimbursement Biaya</h6>
                                <small class="text-muted">Klaim operasional lapangan yang diajukan</small>
                            </div>
                            @if ($isOwnProfile)
                                <button type="button" class="btn btn-sm btn-primary fw-semibold shadow-xs rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalEssNewClaim">
                                    <i class="mdi mdi-plus me-1"></i> Ajukan Klaim Baru
                                </button>
                            @endif
                        </div>
                        <div class="table-responsive text-nowrap">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Kategori Biaya</th>
                                        <th>Tanggal Nota</th>
                                        <th>Nominal Biaya</th>
                                        <th>Keperluan</th>
                                        <th>Status Klaim</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($myReimbursements as $claim)
                                        <tr>
                                            <td>
                                                <span class="badge bg-label-primary rounded-pill px-2.5 py-1">{{ $claim->claim_type }}</span>
                                            </td>
                                            <td>{{ \Carbon\Carbon::parse($claim->event_date)->format('d M Y') }}</td>
                                            <td>
                                                <span class="fw-bold text-dark">Rp {{ number_format((float)($claim->amount ?? 0), 0, ',', '.') }}</span>
                                            </td>
                                            <td>
                                                <span class="small text-muted text-truncate d-block" style="max-width: 260px;">{{ $claim->description }}</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-label-{{ $claim->status === 'Paid' ? 'success' : ($claim->status === 'Approved' ? 'info' : ($claim->status === 'Pending' ? 'warning' : 'danger')) }} rounded-pill px-2.5 py-1">
                                                    {{ $claim->status }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-4 text-muted">Belum ada riwayat pengajuan reimbursement.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

            {{-- ────────────────────────────────────────────────────────────── --}}
            {{-- ── TAB 7: KINERJA SALES (KHUSUS SALES) ──────────────────────── --}}
            {{-- ────────────────────────────────────────────────────────────── --}}
            @if ($isSales)
                <div class="tab-pane fade" id="tabSales" role="tabpanel">
                    {{-- Sales KPI Metrics Grid --}}
                    <div class="row g-3 mb-4">
                        <div class="col-6 col-md-3">
                            <div class="card border-0 shadow-sm p-3 rounded-4">
                                <span class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.5px;">Total Quotation</span>
                                <h3 class="fw-bold text-primary mb-0 mt-1">{{ number_format($salesMetrics['totalQuotations'] ?? 0) }}</h3>
                                <small class="text-muted">Smart &amp; Legacy Quote</small>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="card border-0 shadow-sm p-3 rounded-4">
                                <span class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.5px;">Done PO</span>
                                <h3 class="fw-bold text-success mb-0 mt-1">{{ number_format($salesMetrics['countPoReceived'] ?? 0) }}</h3>
                                <small class="text-muted">Completed Orders</small>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="card border-0 shadow-sm p-3 rounded-4">
                                <span class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.5px;">Total Client</span>
                                <h3 class="fw-bold text-info mb-0 mt-1">{{ number_format($salesMetrics['totalClients'] ?? 0) }}</h3>
                                <small class="text-muted">Assigned Companies</small>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="card border-0 shadow-sm p-3 rounded-4">
                                <span class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.5px;">Active Customers</span>
                                <h3 class="fw-bold text-warning mb-0 mt-1">{{ number_format($salesMetrics['totalCustomers'] ?? 0) }}</h3>
                                <small class="text-muted">Repeat Buyers</small>
                            </div>
                        </div>
                    </div>

                    {{-- Payment Templates & Client List for Sales --}}
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <div class="card border-0 shadow-sm h-100 rounded-4 overflow-hidden">
                                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                                    <h6 class="card-title mb-0 fw-bold text-dark">Template Syarat Pembayaran</h6>
                                    @if ($isOwnProfile)
                                        <button type="button" class="btn btn-sm btn-primary rounded-pill shadow-xs px-3" data-bs-toggle="modal" data-bs-target="#createTemplateModal">
                                            <i class="mdi mdi-plus me-1"></i> Tambah Template
                                        </button>
                                    @endif
                                </div>
                                <div class="card-body p-3">
                                    @forelse ($paymentTemplates as $tpl)
                                        <div class="d-flex justify-content-between align-items-center p-2.5 mb-2 bg-light rounded-3 border">
                                            <div>
                                                <div class="fw-semibold text-dark">{{ $tpl->name }}</div>
                                                <small class="text-muted">{{ $tpl->client?->company ?: 'General Template' }}</small>
                                            </div>
                                            <div>
                                                @if ($tpl->is_default)
                                                    <span class="badge bg-label-success rounded-pill">Default</span>
                                                @endif
                                            </div>
                                        </div>
                                    @empty
                                        <div class="text-center py-4 text-muted small">Belum ada template syarat pembayaran tersimpan.</div>
                                    @endforelse
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-md-6">
                            <div class="card border-0 shadow-sm h-100 rounded-4 overflow-hidden">
                                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                                    <h6 class="card-title mb-0 fw-bold text-dark">Klien &amp; Customer Terdaftar</h6>
                                    <span class="badge bg-label-primary rounded-pill px-2.5 py-1">{{ $salesClients->count() }} Perusahaan</span>
                                </div>
                                <div class="card-body p-3">
                                    <div class="table-responsive" style="max-height: 280px; overflow-y: auto;">
                                        <table class="table table-sm align-middle mb-0">
                                            <tbody>
                                                @forelse ($salesClients as $cl)
                                                    <tr>
                                                        <td>
                                                            <div class="fw-semibold text-dark">{{ $cl->company }}</div>
                                                            <small class="text-muted">{{ $cl->address ?: 'Bandung / Jabar' }}</small>
                                                        </td>
                                                        <td class="text-end">
                                                            <span class="badge bg-label-{{ $cl->role == 'Customers' ? 'success' : 'info' }} rounded-pill">{{ $cl->role }}</span>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="2" class="text-center py-4 text-muted small">Belum ada klien yang ditugaskan.</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════════ --}}
    {{-- ── MODALS ─────────────────────────────────────────────────────────── --}}
    {{-- ══════════════════════════════════════════════════════════════════════ --}}

    {{-- Modal: Ubah Banner --}}
    <div class="modal fade" id="changeBannerModal" tabindex="-1" aria-labelledby="changeBannerModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <form action="{{ route('profile.banner.update', $user->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header bg-primary text-white border-0 py-3">
                        <h5 class="modal-title fw-bold text-white" id="changeBannerModalLabel">
                            <i class="mdi mdi-image-edit-outline me-2"></i>Ubah Banner Profil
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark">Preview Banner Baru</label>
                            <div id="bannerPreviewContainer" class="w-100 rounded-3 border d-flex align-items-center justify-content-center text-muted position-relative overflow-hidden shadow-xs" style="height: 150px; background: {{ $user->banner ? 'url(' . asset($user->banner) . ') center/cover no-repeat' : 'linear-gradient(135deg, #4f46e5 0%, #7c3aed 50%, #6366f1 100%)' }};">
                                <span class="bg-dark bg-opacity-60 text-white px-3 py-1 rounded-pill small fw-medium">Pratinjau Banner</span>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="bannerInput" class="form-label fw-semibold text-dark">Pilih Gambar Banner (JPG, PNG, WebP — Maks. 5MB)</label>
                            <input class="form-control" type="file" id="bannerInput" name="banner" accept="image/*" required>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-0 py-3">
                        <button type="button" class="btn btn-outline-secondary rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold"><i class="mdi mdi-cloud-upload-outline me-1"></i> Simpan Banner</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @if ($employee && $isOwnProfile)
        {{-- Modal: Ajukan Cuti Mandiri --}}
        <div class="modal fade" id="modalEssNewLeave" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <form action="{{ route('hr.leaves.store') }}" method="POST" enctype="multipart/form-data" class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                    @csrf
                    <input type="hidden" name="employee_id" value="{{ $employee->id }}">
                    <div class="modal-header bg-primary text-white border-0 py-3">
                        <h5 class="modal-title fw-bold text-white"><i class="mdi mdi-calendar-plus me-1.5"></i>Ajukan Cuti / Izin Mandiri</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-semibold text-dark">Jenis Cuti</label>
                                <select name="leave_type_id" class="form-select" required>
                                    @foreach ($leaveTypes as $lt)
                                        <option value="{{ $lt->id }}">{{ $lt->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-semibold text-dark">Tanggal Mulai</label>
                                <input type="date" name="start_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-semibold text-dark">Tanggal Berakhir</label>
                                <input type="date" name="end_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold text-dark">Alasan / Keperluan</label>
                                <textarea name="reason" class="form-control" rows="3" placeholder="Jelaskan alasan pengajuan cuti/izin..." required></textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold text-dark">Lampiran (Opsional, Surat Dokter jika sakit)</label>
                                <input type="file" name="attachment" class="form-control" accept=".jpg,.jpeg,.png,.pdf">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-0 py-3">
                        <button type="button" class="btn btn-outline-secondary rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold">Kirim Permohonan Cuti</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Modal: Ajukan Klaim Reimbursement --}}
        <div class="modal fade" id="modalEssNewClaim" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <form action="{{ route('hr.reimbursements.store') }}" method="POST" enctype="multipart/form-data" class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                    @csrf
                    <input type="hidden" name="employee_id" value="{{ $employee->id }}">
                    <div class="modal-header bg-primary text-white border-0 py-3">
                        <h5 class="modal-title fw-bold text-white"><i class="mdi mdi-receipt-text-plus me-1.5"></i>Ajukan Klaim Reimbursement</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-6">
                                <label class="form-label fw-semibold text-dark">Kategori Biaya</label>
                                <select name="claim_type" class="form-select" required>
                                    <option value="BBM / Bensin">BBM / Bensin</option>
                                    <option value="Tol / Parkir">Tol / Parkir</option>
                                    <option value="Akomodasi / Hotel">Akomodasi / Hotel</option>
                                    <option value="Konsumsi Lapangan">Konsumsi Lapangan</option>
                                    <option value="Medis / Pengobatan">Medis / Pengobatan</option>
                                    <option value="Lainnya">Lainnya</option>
                                </select>
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-semibold text-dark">Tanggal Nota</label>
                                <input type="date" name="event_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold text-dark">Nominal Biaya (IDR)</label>
                                <input type="number" name="amount" class="form-control" placeholder="100000" min="1000" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold text-dark">Keperluan / Keterangan</label>
                                <textarea name="description" class="form-control" rows="2" placeholder="Tujuan pengeluaran..." required></textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold text-dark">Upload Foto Struk / Nota</label>
                                <input type="file" name="receipt_image" class="form-control" accept=".jpg,.jpeg,.png,.pdf">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-0 py-3">
                        <button type="button" class="btn btn-outline-secondary rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold">Kirim Klaim Biaya</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
@endsection

@push('after-style')
<style>
    /* ── Profile Hero & Banner ─────────────────────────────────── */
    .profile-hero-card {
        background: #ffffff;
        border-radius: 18px;
        transition: box-shadow 0.25s ease;
    }
    .profile-avatar-container {
        width: 124px;
        height: 124px;
    }
    .profile-avatar-img {
        transition: transform 0.25s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    .profile-avatar-img:hover {
        transform: scale(1.04);
    }
    .status-glow-active {
        box-shadow: 0 0 0 3px rgba(40, 199, 111, 0.35);
    }
    .status-glow-inactive {
        box-shadow: 0 0 0 3px rgba(234, 84, 85, 0.35);
    }
    .btn-glass {
        background: rgba(0, 0, 0, 0.35);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 50rem;
        padding: 0.35rem 0.9rem;
        transition: all 0.2s ease;
    }
    .btn-glass:hover {
        background: rgba(0, 0, 0, 0.55);
        color: #ffffff;
        transform: translateY(-1px);
    }

    /* ── KPI Stat Cards ───────────────────────────────────────── */
    .profile-stat-card {
        border-radius: 14px;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .profile-stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(67, 89, 113, 0.08) !important;
    }
    .stat-icon-box {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.35rem;
    }

    /* ── Nav Pills Custom (Style Tab Report Sales) ────────────── */
    .nav-pills-custom .nav-link {
        border-radius: 8px;
        padding: 10px 18px;
        font-weight: 500;
        color: #566a7f;
        transition: all 0.2s ease;
        background: #ffffff;
        border: 1px solid #e0e4e8;
    }
    .nav-pills-custom .nav-link:hover {
        background: rgba(105, 108, 255, 0.08);
        color: #696cff;
        border-color: #696cff;
    }
    .nav-pills-custom .nav-link.active {
        background: #696cff;
        color: #ffffff;
        border-color: #696cff;
        box-shadow: 0 4px 12px rgba(105, 108, 255, 0.35);
    }
    .nav-pills-custom .nav-link.active .badge {
        background: rgba(255, 255, 255, 0.25) !important;
        color: #ffffff !important;
    }

    /* ── Attendance Action Banner ─────────────────────────────── */
    .attendance-action-banner {
        background: linear-gradient(135deg, #696cff 0%, #4f46e5 100%);
    }

    /* ── Utilities ────────────────────────────────────────────── */
    .font-size-11 { font-size: 11px; }
    .font-size-10 { font-size: 10px; }
</style>
@endpush

@push('after-script')
<script>
    $(document).ready(function () {
        // Live time clock ticker
        function updateLiveClock() {
            var now = new Date();
            var hours = String(now.getHours()).padStart(2, '0');
            var minutes = String(now.getMinutes()).padStart(2, '0');
            var seconds = String(now.getSeconds()).padStart(2, '0');
            var el = document.getElementById('liveTimeClock');
            if (el) el.textContent = hours + ':' + minutes + ':' + seconds + ' WIB';
        }
        setInterval(updateLiveClock, 1000);
        updateLiveClock();

        // Banner image preview
        $('#bannerInput').on('change', function(e) {
            const [file] = e.target.files;
            if (file) {
                $('#bannerPreviewContainer').css('background-image', `url('${URL.createObjectURL(file)}')`);
            }
        });

        // Handle URL Hash for Tab Switching (e.g. #tabAttendance)
        var hash = window.location.hash;
        if (hash) {
            var $tabBtn = $('button[data-bs-target="' + hash + '"], a[data-bs-target="' + hash + '"]');
            if ($tabBtn.length) {
                var tabInstance = bootstrap.Tab.getOrCreateInstance($tabBtn[0]);
                tabInstance.show();
            }
        }
        $('button[data-bs-toggle="tab"], a[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
            var target = $(e.target).attr('data-bs-target');
            if (target && history.replaceState) {
                history.replaceState(null, null, target);
            }
        });

        // Copy-to-clipboard functionality
        $(document).on('click', '.btn-copy-meta', function (e) {
            e.preventDefault();
            var text = $(this).data('copy');
            if (!text || text === '—' || text === '-') return;

            navigator.clipboard.writeText(text).then(function () {
                var origTitle = $(this).attr('data-bs-original-title') || 'Disalin!';
                $(this).tooltip('hide').attr('data-bs-original-title', 'Tersalin: ' + text).tooltip('show');
                var $btn = $(this);
                setTimeout(function () {
                    $btn.attr('data-bs-original-title', origTitle).tooltip('hide');
                }, 2000);
            }.bind(this));
        });
    });
</script>
@endpush
