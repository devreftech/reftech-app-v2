@extends('layouts.sales.app')
@section('title', 'Profil Karyawan - ' . ($employee->user?->name ?? $employee->nik ?? 'HRM'))

@section('content')
@php
    $statusConfig = [
        'Tetap' => ['color' => 'success', 'icon' => 'mdi-shield-check-outline', 'label' => 'Pegawai Tetap'],
        'Kontrak' => ['color' => 'info', 'icon' => 'mdi-file-document-outline', 'label' => 'PKWT (Kontrak)'],
        'Probation' => ['color' => 'warning', 'icon' => 'mdi-clock-outline', 'label' => 'Probation'],
        'Resign' => ['color' => 'secondary', 'icon' => 'mdi-account-arrow-right-outline', 'label' => 'Resign / Alumni'],
        'Perlu Verifikasi' => ['color' => 'danger', 'icon' => 'mdi-alert-circle-outline', 'label' => 'Perlu Verifikasi'],
    ];
    $status = $statusConfig[$employee->employment_status] ?? [
        'color' => 'secondary',
        'icon' => 'mdi-account',
        'label' => $employee->employment_status ?: 'Tidak Diketahui',
    ];

    $empName = $employee->user?->name ?? ($employee->nik ? 'Karyawan ' . $employee->nik : 'Karyawan #' . $employee->id);
    $words = array_filter(explode(' ', trim($empName)));
    $inits = '';
    foreach (array_slice($words, 0, 2) as $w) {
        $inits .= strtoupper(substr($w, 0, 1));
    }
    if (empty($inits)) $inits = 'KR';

    // Masa kerja / Tenure calculation
    $tenure = null;
    $tenureDetail = null;
    if ($employee->join_date) {
        try {
            $join = \Illuminate\Support\Carbon::parse($employee->join_date);
            $diff = $join->diff(\Illuminate\Support\Carbon::now());
            $parts = [];
            if ($diff->y > 0) $parts[] = $diff->y . ' thn';
            if ($diff->m > 0) $parts[] = $diff->m . ' bln';
            if (empty($parts)) $parts[] = max($diff->d, 1) . ' hr';
            $tenure = implode(' ', $parts);
            $tenureDetail = $join->translatedFormat('d M Y');
        } catch (\Exception $e) {
            $tenure = null;
        }
    }

    // Contract deadline calculation
    $contractDaysLeft = null;
    $contractIsExpired = false;
    if ($employee->contract_end_date) {
        try {
            $end = \Illuminate\Support\Carbon::parse($employee->contract_end_date);
            $today = \Illuminate\Support\Carbon::today();
            if ($today->gt($end)) {
                $contractIsExpired = true;
                $contractDaysLeft = 'Berakhir ' . $end->diffForHumans();
            } else {
                $days = $today->diffInDays($end);
                $contractDaysLeft = $days == 0 ? 'Hari ini berakhir' : $days . ' hari tersisa';
            }
        } catch (\Exception $e) {
            $contractDaysLeft = null;
        }
    }

    // Age calculation
    $age = null;
    if ($employee->birthday) {
        try {
            $age = \Illuminate\Support\Carbon::parse($employee->birthday)->age;
        } catch (\Exception $e) {
            $age = null;
        }
    }

    // WhatsApp clean number
    $rawPhone = $employee->phone ?? $employee->user?->phone;
    $cleanPhone = $rawPhone ? preg_replace('/[^0-9]/', '', $rawPhone) : null;
    if ($cleanPhone && str_starts_with($cleanPhone, '0')) {
        $cleanPhone = '62' . substr($cleanPhone, 1);
    }
@endphp

<style>
    @media print {
        .no-print, .layout-navbar, .layout-menu, .content-footer, .btn, .breadcrumb {
            display: none !important;
        }
        .content-wrapper {
            padding: 0 !important;
        }
        .card {
            box-shadow: none !important;
            border: 1px solid #ddd !important;
        }
    }
</style>

{{-- Flash Messages --}}
@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show shadow-xs border-0 mb-4" role="alert">
        <div class="d-flex align-items-center">
            <i class="mdi mdi-check-circle-outline fs-4 me-2 text-success"></i>
            <div>{{ session('success') }}</div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

{{-- Top Action Bar & Breadcrumbs --}}
<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3 no-print">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 text-muted small">
                <li class="breadcrumb-item">
                    <a href="{{ route('employees.index') }}" class="text-muted">
                        <i class="mdi mdi-account-group-outline me-1"></i>HR Management
                    </a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('employees.index') }}" class="text-muted">Data Karyawan</a>
                </li>
                <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">Profil Karyawan</li>
            </ol>
        </nav>
        <h4 class="fw-bold mb-0 text-heading">
            Detail Profil Karyawan
        </h4>
    </div>

    <div class="d-flex align-items-center gap-2 flex-wrap">
        <button type="button" onclick="window.print()" class="btn btn-outline-secondary shadow-xs">
            <i class="mdi mdi-printer-outline me-1"></i> Cetak Profil
        </button>
        <a href="{{ route('employees.edit', $employee->id) }}" class="btn btn-primary shadow-xs">
            <i class="mdi mdi-pencil-outline me-1"></i> Edit Data
        </a>
        <a href="{{ route('employees.index') }}" class="btn btn-label-secondary shadow-xs">
            <i class="mdi mdi-arrow-left me-1"></i> Hub Karyawan
        </a>
    </div>
</div>

{{-- ── HERO PROFILE HEADER CARD ──────────────────────────────────────── --}}
<div class="card border-0 shadow-sm mb-4 overflow-hidden">
    {{-- Banner Top Strip --}}
    <div style="height: 100px; background: linear-gradient(135deg, #5b54d6 0%, #3d35af 50%, #20187a 100%);"></div>

    <div class="card-body px-4 pb-4 pt-0">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-end gap-3" style="margin-top: -50px;">
            {{-- Avatar & Identity --}}
            <div class="d-flex flex-column flex-sm-row align-items-center align-items-sm-end gap-3 text-center text-sm-start">
                <div class="position-relative">
                    @if ($employee->user?->image && file_exists(public_path($employee->user->image)))
                        <img src="{{ asset($employee->user->image) }}" alt="{{ $empName }}"
                             class="rounded-circle shadow"
                             style="width: 105px; height: 105px; object-fit: cover; border: 4px solid #fff; background: #fff;">
                    @else
                        <div class="rounded-circle shadow d-inline-flex align-items-center justify-content-center bg-label-primary text-primary"
                             style="width: 105px; height: 105px; font-size: 2.2rem; font-weight: 700; border: 4px solid #fff; background: #fff;">
                            {{ $inits }}
                        </div>
                    @endif
                    <span class="position-absolute bottom-0 end-0 p-1 bg-{{ $status['color'] }} border border-white rounded-circle"
                          title="Status: {{ $status['label'] }}"
                          style="width: 20px; height: 20px;"></span>
                </div>

                <div class="mb-1">
                    <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-sm-start gap-2">
                        <h4 class="fw-bold mb-0 text-heading">{{ $empName }}</h4>
                        <span class="badge bg-label-{{ $status['color'] }} px-2 py-1">
                            <i class="mdi {{ $status['icon'] }} me-1"></i>{{ $status['label'] }}
                        </span>
                        @if ($employee->nik)
                            <span class="badge bg-label-secondary font-monospace">
                                NIK: {{ $employee->nik }}
                            </span>
                        @endif
                    </div>
                    <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-sm-start gap-3 mt-1 text-muted small">
                        <span>
                            <i class="mdi mdi-briefcase-outline text-primary me-1"></i>
                            <strong class="text-heading">{{ $employee->position?->name ?? 'Jabatan Belum Diatur' }}</strong>
                        </span>
                        <span>&bull;</span>
                        <span>
                            <i class="mdi mdi-domain text-info me-1"></i>
                            <strong class="text-heading">{{ $employee->department?->name ?? 'Tanpa Departemen' }}</strong>
                        </span>
                        @if ($employee->user_id)
                            <span>&bull;</span>
                            <span class="text-success">
                                <i class="mdi mdi-shield-check-outline me-1"></i>Akun Portal: {{ $employee->user?->role ?? 'User' }}
                            </span>
                        @endif
                        <span>&bull;</span>
                        @if ($employee->can_online_attendance)
                            <span class="badge bg-label-success">
                                <i class="mdi mdi-clock-check-outline me-1"></i>Absensi Online Aktif
                            </span>
                        @else
                            <span class="badge bg-label-secondary">
                                <i class="mdi mdi-clock-remove-outline me-1"></i>Bebas Absensi Online
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Quick Action Contacts --}}
            <div class="d-flex flex-wrap align-items-center gap-2 w-100 w-md-auto justify-content-center justify-content-md-end">
                @if ($cleanPhone)
                    <a href="https://wa.me/{{ $cleanPhone }}" target="_blank" class="btn btn-sm btn-label-success shadow-xs">
                        <i class="mdi mdi-whatsapp me-1"></i> WhatsApp
                    </a>
                @endif
                @if ($employee->user?->email)
                    <a href="mailto:{{ $employee->user->email }}" class="btn btn-sm btn-label-primary shadow-xs">
                        <i class="mdi mdi-email-outline me-1"></i> Email
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- ── STATS ROW ──────────────────────────────────────────────────────── --}}
<div class="row g-3 mb-4">
    {{-- Card 1: Masa Kerja --}}
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="text-muted small fw-semibold">Masa Kerja</span>
                    <div class="avatar avatar-xs rounded bg-label-primary d-flex align-items-center justify-content-center">
                        <i class="mdi mdi-timer-sand fs-6"></i>
                    </div>
                </div>
                <h5 class="fw-bold text-heading mb-0">{{ $tenure ?? '-' }}</h5>
                <div class="text-muted small mt-1" style="font-size: 0.75rem;">
                    {{ $tenureDetail ? 'Mulai: ' . $tenureDetail : 'Tanggal masuk kosong' }}
                </div>
            </div>
        </div>
    </div>

    {{-- Card 2: Status Kontrak --}}
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="text-muted small fw-semibold">Status Ikatan</span>
                    <div class="avatar avatar-xs rounded bg-label-{{ $status['color'] }} d-flex align-items-center justify-content-center">
                        <i class="mdi {{ $status['icon'] }} fs-6"></i>
                    </div>
                </div>
                <h5 class="fw-bold text-heading mb-0">{{ $employee->employment_status }}</h5>
                <div class="text-muted small mt-1" style="font-size: 0.75rem;">
                    @if ($employee->employment_status === 'Kontrak')
                        <span class="{{ $contractIsExpired ? 'text-danger fw-semibold' : 'text-info' }}">
                            {{ $contractDaysLeft ?? 'Batas kontrak belum diatur' }}
                        </span>
                    @elseif ($employee->employment_status === 'Tetap')
                        <span class="text-success">Pegawai Permanen</span>
                    @elseif ($employee->employment_status === 'Resign')
                        <span class="text-secondary">Keluar: {{ $employee->resign_date?->translatedFormat('d M Y') ?? '-' }}</span>
                    @else
                        <span>Status HR aktif</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Card 3: Departemen --}}
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="text-muted small fw-semibold">Departemen</span>
                    <div class="avatar avatar-xs rounded bg-label-info d-flex align-items-center justify-content-center">
                        <i class="mdi mdi-office-building fs-6"></i>
                    </div>
                </div>
                <h5 class="fw-bold text-heading mb-0 text-truncate" title="{{ $employee->department?->name ?? '-' }}">
                    {{ $employee->department?->name ?? 'Belum Diatur' }}
                </h5>
                <div class="text-muted small mt-1" style="font-size: 0.75rem;">
                    {{ $employee->position?->name ?? 'Jabatan belum diatur' }}
                </div>
            </div>
        </div>
    </div>

    {{-- Card 4: Status Akun --}}
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="text-muted small fw-semibold">Akun Login</span>
                    <div class="avatar avatar-xs rounded bg-label-{{ $employee->user_id ? 'success' : 'secondary' }} d-flex align-items-center justify-content-center">
                        <i class="mdi {{ $employee->user_id ? 'mdi-account-check' : 'mdi-account-off' }} fs-6"></i>
                    </div>
                </div>
                <h5 class="fw-bold text-heading mb-0">
                    {{ $employee->user_id ? 'Terhubung' : 'Non-User' }}
                </h5>
                <div class="text-muted small mt-1" style="font-size: 0.75rem;">
                    @if ($employee->user)
                        <span>Role: {{ $employee->user->role ?? 'User' }} ({{ $employee->user->active ? 'Aktif' : 'Non-Aktif' }})</span>
                    @else
                        <span>Pegawai tanpa akses login portal</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ── MAIN DETAILS (2 COLUMNS) ──────────────────────────────────────── --}}
<div class="row g-4">
    {{-- Left Column: Personal Identity & System Account --}}
    <div class="col-12 col-lg-5 col-xl-4">
        <div class="d-flex flex-column gap-4">

            {{-- Personal Profile Info --}}
            <div class="card shadow-sm border-0">
                <div class="card-header border-bottom py-3 bg-light">
                    <h6 class="card-title mb-0 fw-bold d-flex align-items-center text-heading">
                        <i class="mdi mdi-card-account-details-outline text-primary fs-5 me-2"></i>
                        Identitas Personal
                    </h6>
                </div>
                <div class="card-body p-3">
                    <ul class="list-unstyled mb-0 d-flex flex-column gap-3">
                        <li class="d-flex justify-content-between align-items-start">
                            <span class="text-muted small"><i class="mdi mdi-barcode me-1"></i> NIK</span>
                            <span class="fw-semibold font-monospace text-heading">{{ $employee->nik ?? '-' }}</span>
                        </li>
                        <li class="d-flex justify-content-between align-items-start">
                            <span class="text-muted small"><i class="mdi mdi-cake-variant-outline me-1"></i> Tgl Lahir</span>
                            <span class="fw-semibold text-heading text-end">
                                {{ $employee->birthday ? \Illuminate\Support\Carbon::parse($employee->birthday)->translatedFormat('d F Y') : '-' }}
                                @if ($age)
                                    <span class="badge bg-label-info ms-1">{{ $age }} thn</span>
                                @endif
                            </span>
                        </li>
                        <li class="d-flex justify-content-between align-items-start">
                            <span class="text-muted small"><i class="mdi mdi-phone-outline me-1"></i> No. Telepon</span>
                            <span class="fw-semibold text-heading">{{ $employee->phone ?? '-' }}</span>
                        </li>
                        <li class="d-flex justify-content-between align-items-start">
                            <span class="text-muted small"><i class="mdi mdi-email-outline me-1"></i> Email</span>
                            <span class="fw-semibold text-heading text-truncate ms-2" style="max-width: 180px;" title="{{ $employee->user?->email ?? '-' }}">
                                {{ $employee->user?->email ?? '-' }}
                            </span>
                        </li>
                        <li class="pt-2 border-top">
                            <span class="text-muted small d-block mb-1"><i class="mdi mdi-map-marker-outline me-1"></i> Alamat Domisili</span>
                            <p class="mb-0 small text-heading" style="line-height: 1.5;">
                                {{ $employee->address ?: 'Alamat belum diinputkan.' }}
                            </p>
                        </li>
                    </ul>
                </div>
            </div>

            {{-- System Account Integration --}}
            <div class="card shadow-sm border-0">
                <div class="card-header border-bottom py-3 bg-light">
                    <h6 class="card-title mb-0 fw-bold d-flex align-items-center text-heading">
                        <i class="mdi mdi-shield-account-outline text-success fs-5 me-2"></i>
                        Akun Pengguna Sistem
                    </h6>
                </div>
                <div class="card-body p-3">
                    @if ($employee->user)
                        <div class="d-flex align-items-center gap-3 mb-3 p-2 rounded bg-label-primary">
                            <div class="avatar avatar-sm rounded bg-primary text-white d-flex align-items-center justify-content-center">
                                <i class="mdi mdi-account-key"></i>
                            </div>
                            <div>
                                <div class="fw-bold text-heading">{{ $employee->user->name }}</div>
                                <div class="text-muted small">{{ $employee->user->email }}</div>
                            </div>
                        </div>

                        <ul class="list-unstyled mb-0 d-flex flex-column gap-2 small">
                            <li class="d-flex justify-content-between">
                                <span class="text-muted">Role / Hak Akses:</span>
                                <span class="badge bg-label-primary">{{ $employee->user->role ?? 'User' }}</span>
                            </li>
                            <li class="d-flex justify-content-between">
                                <span class="text-muted">Status Akun Login:</span>
                                @if ($employee->user->active)
                                    <span class="badge bg-label-success"><i class="mdi mdi-check-circle-outline me-1"></i>Aktif</span>
                                @else
                                    <span class="badge bg-label-danger"><i class="mdi mdi-close-circle-outline me-1"></i>Dinonaktifkan</span>
                                @endif
                            </li>
                            @if ($employee->user->code)
                                <li class="d-flex justify-content-between">
                                    <span class="text-muted">Kode Inisial:</span>
                                    <span class="fw-semibold font-monospace">{{ $employee->user->code }}</span>
                                </li>
                            @endif
                            <li class="d-flex justify-content-between">
                                <span class="text-muted">Terdaftar Sejak:</span>
                                <span>{{ $employee->user->created_at ? $employee->user->created_at->translatedFormat('d M Y') : '-' }}</span>
                            </li>
                        </ul>
                    @else
                        <div class="text-center py-3">
                            <div class="rounded-circle d-inline-flex align-items-center justify-content-center bg-label-secondary mb-2" style="width: 50px; height: 50px;">
                                <i class="mdi mdi-account-off-outline fs-3 text-secondary"></i>
                            </div>
                            <h6 class="fw-semibold mb-1">Belum Terhubung ke Akun User</h6>
                            <p class="text-muted small mb-3">Karyawan ini tidak memiliki hak akses login ke sistem ERP.</p>
                            <a href="{{ route('employees.edit', $employee->id) }}" class="btn btn-sm btn-outline-primary">
                                <i class="mdi mdi-link-variant me-1"></i> Hubungkan Sekarang
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Danger Zone Card --}}
            <div class="card shadow-sm border-0 border-top border-danger border-2 no-print">
                <div class="card-body p-3">
                    <h6 class="fw-bold text-danger mb-1 d-flex align-items-center">
                        <i class="mdi mdi-alert-outline me-1"></i> Manajemen Data
                    </h6>
                    <p class="text-muted small mb-2">
                        Menghapus data karyawan ini tidak akan menghapus akun user login yang bersangkutan.
                    </p>
                    <button type="button" class="btn btn-sm btn-label-danger w-100" data-bs-toggle="modal" data-bs-target="#deleteEmployeeModal">
                        <i class="mdi mdi-trash-can-outline me-1"></i> Hapus Data Karyawan
                    </button>
                </div>
            </div>

        </div>
    </div>

    {{-- Right Column: Employment Details, Contract Timeline & Career --}}
    <div class="col-12 col-lg-7 col-xl-8">
        <div class="d-flex flex-column gap-4">

            {{-- Employment & Organization Details --}}
            <div class="card shadow-sm border-0">
                <div class="card-header border-bottom py-3 bg-light d-flex justify-content-between align-items-center">
                    <h6 class="card-title mb-0 fw-bold d-flex align-items-center text-heading">
                        <i class="mdi mdi-domain text-info fs-5 me-2"></i>
                        Penempatan & Struktur Organisasi
                    </h6>
                    <span class="badge bg-label-primary">Struktur HR</span>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-12 col-sm-6">
                            <div class="p-3 rounded bg-light border">
                                <div class="text-muted small mb-1 fw-semibold">
                                    <i class="mdi mdi-domain text-info me-1"></i> Departemen / Divisi
                                </div>
                                <h5 class="fw-bold text-heading mb-1">
                                    {{ $employee->department?->name ?? 'Belum Ditugaskan' }}
                                </h5>
                                <div class="text-muted small">
                                    {{ $employee->department?->description ?? 'Tidak ada deskripsi departemen.' }}
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-sm-6">
                            <div class="p-3 rounded bg-light border">
                                <div class="text-muted small mb-1 fw-semibold">
                                    <i class="mdi mdi-briefcase-outline text-primary me-1"></i> Posisi Jabatan
                                </div>
                                <h5 class="fw-bold text-heading mb-1">
                                    {{ $employee->position?->name ?? 'Belum Ditugaskan' }}
                                </h5>
                                <div class="text-muted small">
                                    {{ $employee->position?->description ?? 'Tidak ada deskripsi jabatan.' }}
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-sm-6">
                            <div class="d-flex align-items-start gap-3">
                                <div class="avatar avatar-sm rounded bg-label-success d-flex align-items-center justify-content-center">
                                    <i class="mdi mdi-calendar-check"></i>
                                </div>
                                <div>
                                    <span class="text-muted small d-block">Tanggal Mulai Bergabung</span>
                                    <strong class="text-heading">
                                        {{ $employee->join_date ? \Illuminate\Support\Carbon::parse($employee->join_date)->translatedFormat('d F Y') : '-' }}
                                    </strong>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-sm-6">
                            <div class="d-flex align-items-start gap-3">
                                <div class="avatar avatar-sm rounded bg-label-primary d-flex align-items-center justify-content-center">
                                    <i class="mdi mdi-timer-sand"></i>
                                </div>
                                <div>
                                    <span class="text-muted small d-block">Total Lama Masa Kerja</span>
                                    <strong class="text-heading">{{ $tenure ?? '-' }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Contract & Employment Agreement --}}
            <div class="card shadow-sm border-0">
                <div class="card-header border-bottom py-3 bg-light d-flex justify-content-between align-items-center">
                    <h6 class="card-title mb-0 fw-bold d-flex align-items-center text-heading">
                        <i class="mdi mdi-file-sign text-warning fs-5 me-2"></i>
                        Perjanjian Kerja & Periode Kontrak
                    </h6>
                    <span class="badge bg-label-{{ $status['color'] }}">{{ $employee->employment_status }}</span>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-12 col-md-4">
                            <div class="p-3 rounded border text-center h-100">
                                <div class="text-muted small mb-1">Mulai Kontrak</div>
                                <h6 class="fw-bold mb-0 text-heading">
                                    {{ $employee->contract_start_date ? \Illuminate\Support\Carbon::parse($employee->contract_start_date)->translatedFormat('d M Y') : '-' }}
                                </h6>
                            </div>
                        </div>

                        <div class="col-12 col-md-4">
                            <div class="p-3 rounded border text-center h-100 {{ $contractIsExpired ? 'border-danger bg-label-danger' : '' }}">
                                <div class="text-muted small mb-1">Berakhir Kontrak</div>
                                <h6 class="fw-bold mb-0 text-heading">
                                    {{ $employee->contract_end_date ? \Illuminate\Support\Carbon::parse($employee->contract_end_date)->translatedFormat('d M Y') : '-' }}
                                </h6>
                                @if ($contractDaysLeft)
                                    <div class="small mt-1 {{ $contractIsExpired ? 'text-danger fw-bold' : 'text-primary' }}">
                                        {{ $contractDaysLeft }}
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="col-12 col-md-4">
                            <div class="p-3 rounded border text-center h-100 {{ $employee->resign_date ? 'border-secondary bg-light' : '' }}">
                                <div class="text-muted small mb-1">Tanggal Resign</div>
                                <h6 class="fw-bold mb-0 text-heading">
                                    {{ $employee->resign_date ? \Illuminate\Support\Carbon::parse($employee->resign_date)->translatedFormat('d M Y') : '-' }}
                                </h6>
                                @if ($employee->resign_date)
                                    <div class="small text-muted mt-1">Status Non-Aktif</div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Timeline / Status Explanatory Box --}}
                    <div class="mt-4 p-3 rounded bg-light border">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <i class="mdi mdi-information-outline text-primary"></i>
                            <strong class="small text-heading">Keterangan Status Hubungan Kerja:</strong>
                        </div>
                        <p class="small text-muted mb-0">
                            @if ($employee->employment_status === 'Tetap')
                                Pegawai telah diangkat menjadi Karyawan Tetap (PKWTT) dan berhak atas seluruh fasilitas reguler perusahaan.
                            @elseif ($employee->employment_status === 'Kontrak')
                                Karyawan terikat Perjanjian Kerja Waktu Tertentu (PKWT). Harap perhatikan tanggal berakhir kontrak untuk penjadwalan evaluasi perpanjangan atau pengangkatan pegawai tetap.
                            @elseif ($employee->employment_status === 'Probation')
                                Karyawan sedang dalam masa percobaan kerja (evaluasi berkala kinerja 3 bulan).
                            @elseif ($employee->employment_status === 'Resign')
                                Karyawan telah resmi mengakhiri masa kerja di perusahaan. Hak akses sistem login telah dicabut atau dinonaktifkan.
                            @else
                                Data karyawan memerlukan verifikasi ulang oleh tim HR untuk melengkapi parameter kontrak & status aktif.
                            @endif
                        </p>
                    </div>
                </div>
            {{-- ── KOMPENSASI GAJI & RIWAYAT KENAIKAN (SALARY HISTORY) ── --}}
            @php
                $sal = $employee->salary;
                $bpjs = ($sal?->bpjs_kesehatan ?? 0) + ($sal?->bpjs_ketenagakerjaan ?? 0);
            @endphp
            <div class="card shadow-sm border-0">
                <div class="card-header border-bottom py-3 bg-light d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2">
                    <div>
                        <h6 class="card-title mb-0 fw-bold d-flex align-items-center text-heading">
                            <i class="mdi mdi-cash-multiple text-success fs-5 me-2"></i>
                            Struktur Kompensasi &amp; Riwayat Kenaikan Gaji
                        </h6>
                        <small class="text-muted">Master komponen kompensasi pokok, tunjangan, dan log kenaikan per tahun/bulan</small>
                    </div>
                    <button type="button" class="btn btn-sm btn-primary shadow-xs" data-bs-toggle="modal" data-bs-target="#modalEditSalaryProfile">
                        <i class="mdi mdi-trending-up me-1"></i> Atur / Naikkan Gaji
                    </button>
                </div>

                <div class="card-body p-4">
                    {{-- Current Salary Component Summary Grid --}}
                    <div class="row g-3 mb-4">
                        <div class="col-12 col-sm-6 col-md-3">
                            <div class="p-3 rounded bg-light border text-center h-100">
                                <span class="text-muted small d-block mb-1">Gaji Pokok</span>
                                <h5 class="fw-bold text-primary mb-0 font-monospace">
                                    Rp {{ number_format($sal?->basic_salary ?? 0, 0, ',', '.') }}
                                </h5>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-md-3">
                            <div class="p-3 rounded bg-light border text-center h-100">
                                <span class="text-muted small d-block mb-1">Total Tunjangan</span>
                                <h5 class="fw-bold text-info mb-0 font-monospace">
                                    Rp {{ number_format($sal?->total_allowance ?? 0, 0, ',', '.') }}
                                </h5>
                                <small class="text-muted font-monospace" style="font-size: 0.72rem;">Trf: {{ number_format($sal?->transport_allowance ?? 0, 0, ',', '.') }} &bull; Mkn: {{ number_format($sal?->meal_allowance ?? 0, 0, ',', '.') }}</small>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-md-3">
                            <div class="p-3 rounded bg-light border text-center h-100">
                                <span class="text-muted small d-block mb-1">Potongan BPJS Total</span>
                                <h5 class="fw-bold text-danger mb-0 font-monospace">
                                    -Rp {{ number_format($bpjs, 0, ',', '.') }}
                                </h5>
                                <small class="text-muted font-monospace" style="font-size: 0.72rem;">Kes: {{ number_format($sal?->bpjs_kesehatan ?? 0, 0, ',', '.') }} &bull; TK: {{ number_format($sal?->bpjs_ketenagakerjaan ?? 0, 0, ',', '.') }}</small>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-md-3">
                            <div class="p-3 rounded bg-label-success border border-success text-center h-100">
                                <span class="text-muted small d-block mb-1">Total Gaji Bruto</span>
                                <h5 class="fw-bold text-success mb-0 font-monospace">
                                    Rp {{ number_format($sal?->total_gross ?? 0, 0, ',', '.') }}
                                </h5>
                                <small class="text-muted font-monospace" style="font-size: 0.72rem;">{{ $sal?->bank_name ?? 'Bank' }} {{ $sal?->bank_account_number ? '(' . $sal->bank_account_number . ')' : '' }}</small>
                            </div>
                        </div>
                    </div>

                    {{-- Salary Increment History Table --}}
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <h6 class="fw-bold mb-0 text-heading d-flex align-items-center">
                            <i class="mdi mdi-timeline-text-outline text-info me-1"></i> Log Riwayat Kenaikan &amp; Perubahan Gaji
                        </h6>
                        <span class="badge bg-label-info">{{ $employee->salaryHistories->count() }} Catatan Riwayat</span>
                    </div>

                    @if ($employee->salaryHistories->count() > 0)
                        <div class="table-responsive border rounded">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Tanggal Berlaku</th>
                                        <th>Tipe / Alasan</th>
                                        <th>Gaji Pokok Baru</th>
                                        <th>Total Bruto</th>
                                        <th>Kenaikan (+/-)</th>
                                        <th>Diupdate Oleh &amp; Catatan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($employee->salaryHistories as $hist)
                                        @php
                                            $typeBadge = match ($hist->change_type) {
                                                'Kenaikan Tahunan' => 'primary',
                                                'Promosi Jabatan' => 'success',
                                                'Evaluasi Kinerja' => 'info',
                                                'Penyesuaian UMR' => 'warning',
                                                'Penetapan Awal' => 'secondary',
                                                default => 'dark',
                                            };
                                        @endphp
                                        <tr>
                                            <td>
                                                <div class="fw-bold text-heading">
                                                    {{ \Carbon\Carbon::parse($hist->effective_date)->translatedFormat('d M Y') }}
                                                </div>
                                                <small class="text-muted font-monospace">{{ \Carbon\Carbon::parse($hist->effective_date)->diffForHumans() }}</small>
                                            </td>
                                            <td>
                                                <span class="badge bg-label-{{ $typeBadge }}">{{ $hist->change_type }}</span>
                                            </td>
                                            <td>
                                                <div class="font-monospace fw-semibold text-heading">
                                                    Rp {{ number_format($hist->basic_salary, 0, ',', '.') }}
                                                </div>
                                                @if ($hist->previous_basic_salary > 0)
                                                    <small class="text-muted font-monospace d-block">
                                                        Sebelumnya: Rp {{ number_format($hist->previous_basic_salary, 0, ',', '.') }}
                                                    </small>
                                                @endif
                                            </td>
                                            <td class="font-monospace fw-bold text-success">
                                                Rp {{ number_format($hist->total_gross, 0, ',', '.') }}
                                            </td>
                                            <td>
                                                @if ($hist->increment_amount > 0)
                                                    <span class="badge bg-label-success font-monospace">
                                                        +Rp {{ number_format($hist->increment_amount, 0, ',', '.') }}
                                                        @if ($hist->increment_percentage > 0)
                                                            (+{{ $hist->increment_percentage }}%)
                                                        @endif
                                                    </span>
                                                @elseif ($hist->increment_amount < 0)
                                                    <span class="badge bg-label-danger font-monospace">
                                                        -Rp {{ number_format(abs($hist->increment_amount), 0, ',', '.') }}
                                                    </span>
                                                @else
                                                    <span class="badge bg-label-secondary font-monospace">0</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="small fw-semibold text-heading">{{ $hist->creator?->name ?? 'HR System' }}</div>
                                                @if ($hist->notes)
                                                    <div class="small text-muted fst-italic mt-1" style="max-width: 250px;">
                                                        "{{ $hist->notes }}"
                                                    </div>
                                                @else
                                                    <span class="text-muted small">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4 border rounded bg-light">
                            <div class="avatar avatar-md rounded-circle bg-label-secondary mx-auto mb-2 d-flex align-items-center justify-content-center">
                                <i class="mdi mdi-history text-secondary fs-4"></i>
                            </div>
                            <h6 class="fw-bold mb-1">Belum Ada Riwayat Perubahan Gaji</h6>
                            <p class="text-muted small mb-0">Klik tombol <strong>Atur / Naikkan Gaji</strong> di atas untuk menetapkan atau memperbarui riwayat kompensasi karyawan.</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Audit & Record Timestamps --}}
            <div class="card shadow-sm border-0 bg-white">
                <div class="card-body p-3 text-muted small d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <div>
                        <i class="mdi mdi-clock-check-outline me-1"></i>
                        Dibuat pada: <strong>{{ $employee->created_at ? $employee->created_at->translatedFormat('d F Y, H:i') : '-' }}</strong>
                    </div>
                    <div>
                        <i class="mdi mdi-update me-1"></i>
                        Pembaruan terakhir: <strong>{{ $employee->updated_at ? $employee->updated_at->translatedFormat('d F Y, H:i') : '-' }}</strong>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

{{-- ── MODAL ATUR / NAIKKAN GAJI (DI HALAMAN PROFIL KARYAWAN) ────────── --}}
<div class="modal fade" id="modalEditSalaryProfile" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('hr.employees.salary', $employee->id) }}" method="POST" class="modal-content border-0 shadow text-start">
            @csrf
            <div class="modal-header border-bottom bg-light">
                <div>
                    <h5 class="modal-title fw-bold mb-0">Atur &amp; Naikkan Komponen Gaji</h5>
                    <small class="text-muted">{{ $empName }} &bull; {{ $employee->department?->name ?? '-' }}</small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label fw-semibold">Gaji Pokok (IDR) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="text" name="basic_salary" class="form-control font-monospace format-rupiah" value="{{ number_format($sal?->basic_salary ?? 5500000, 0, ',', '.') }}" placeholder="0" required>
                        </div>
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-semibold">Tunjangan Transport</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="text" name="transport_allowance" class="form-control font-monospace format-rupiah" value="{{ number_format($sal?->transport_allowance ?? 500000, 0, ',', '.') }}" placeholder="0">
                        </div>
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-semibold">Tunjangan Makan</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="text" name="meal_allowance" class="form-control font-monospace format-rupiah" value="{{ number_format($sal?->meal_allowance ?? 650000, 0, ',', '.') }}" placeholder="0">
                        </div>
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-semibold">Tunjangan Jabatan</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="text" name="position_allowance" class="form-control font-monospace format-rupiah" value="{{ number_format($sal?->position_allowance ?? 750000, 0, ',', '.') }}" placeholder="0">
                        </div>
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-semibold">Tunjangan Lainnya</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="text" name="other_allowance" class="form-control font-monospace format-rupiah" value="{{ number_format($sal?->other_allowance ?? 0, 0, ',', '.') }}" placeholder="0">
                        </div>
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-semibold text-danger">Potongan BPJS Kesehatan</label>
                        <div class="input-group">
                            <span class="input-group-text text-danger">Rp</span>
                            <input type="text" name="bpjs_kesehatan" class="form-control font-monospace format-rupiah" value="{{ number_format($sal?->bpjs_kesehatan ?? 55000, 0, ',', '.') }}" placeholder="0">
                        </div>
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-semibold text-danger">Potongan BPJS Ketenagakerjaan</label>
                        <div class="input-group">
                            <span class="input-group-text text-danger">Rp</span>
                            <input type="text" name="bpjs_ketenagakerjaan" class="form-control font-monospace format-rupiah" value="{{ number_format($sal?->bpjs_ketenagakerjaan ?? 110000, 0, ',', '.') }}" placeholder="0">
                        </div>
                    </div>
                    <div class="col-4">
                        <label class="form-label fw-semibold">Nama Bank</label>
                        <input type="text" name="bank_name" class="form-control" value="{{ $sal?->bank_name ?? 'BCA' }}" placeholder="BCA / Mandiri">
                    </div>
                    <div class="col-8">
                        <label class="form-label fw-semibold">No. Rekening</label>
                        <input type="text" name="bank_account_number" class="form-control font-monospace" value="{{ $sal?->bank_account_number ?? '' }}">
                    </div>

                    {{-- Parameter Riwayat Kenaikan --}}
                    <div class="col-12 border-top pt-3 mt-2">
                        <span class="text-muted small fw-bold text-uppercase d-block mb-2">Parameter Riwayat &amp; Kenaikan Gaji</span>
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-semibold">Tanggal Berlaku <span class="text-danger">*</span></label>
                        <input type="date" name="effective_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-semibold">Tipe Perubahan</label>
                        <select name="change_type" class="form-select">
                            <option value="Kenaikan Tahunan" selected>Kenaikan Tahunan</option>
                            <option value="Promosi Jabatan">Promosi Jabatan</option>
                            <option value="Evaluasi Kinerja">Evaluasi Kinerja</option>
                            <option value="Penyesuaian UMR">Penyesuaian UMR</option>
                            <option value="Kenaikan Berkala">Kenaikan Berkala</option>
                            <option value="Penetapan Awal">Penetapan Awal</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Catatan / Alasan Kenaikan</label>
                        <input type="text" name="notes" class="form-control" placeholder="Contoh: Kenaikan gaji tahunan per evaluasi KPI">
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top bg-light">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Komponen Gaji</button>
            </div>
        </form>
    </div>
</div>

{{-- ── MODAL DELETE CONFIRMATION ───────────────────────────────────────── --}}
<div class="modal fade" id="deleteEmployeeModal" tabindex="-1" aria-labelledby="deleteEmployeeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom bg-light">
                <h5 class="modal-title fw-bold text-danger d-flex align-items-center" id="deleteEmployeeModalLabel">
                    <i class="mdi mdi-alert-circle-outline me-2 fs-4"></i> Konfirmasi Hapus Data
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <div class="avatar avatar-xl rounded-circle bg-label-danger mx-auto mb-3 d-flex align-items-center justify-content-center">
                    <i class="mdi mdi-trash-can-outline fs-1 text-danger"></i>
                </div>
                <h5 class="fw-bold text-heading mb-2">Hapus Data Karyawan?</h5>
                <p class="text-muted small mb-3">
                    Anda akan menghapus data kepegawaian untuk <strong>{{ $empName }}</strong> (NIK: {{ $employee->nik ?? '-' }}).
                    Tindakan ini tidak dapat dibatalkan. Akun login user sistem (jika ada) tidak akan terhapus.
                </p>
                <div class="d-flex justify-content-center gap-2">
                    <button type="button" class="btn btn-label-secondary px-4" data-bs-dismiss="modal">Batal</button>
                    <form action="{{ route('employees.destroy', $employee->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger px-4">
                            <i class="mdi mdi-trash-can-outline me-1"></i> Ya, Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('after-script')
<script>
    $(document).on('input keyup', '.format-rupiah', function () {
        var raw = $(this).val().replace(/[^0-9]/g, '');
        if (raw === '') {
            $(this).val('');
        } else {
            $(this).val(parseInt(raw, 10).toLocaleString('id-ID'));
        }
    });
</script>
@endpush
