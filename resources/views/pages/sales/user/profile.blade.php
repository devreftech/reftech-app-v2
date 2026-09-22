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
                <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">Profil &amp; Data Karyawan</li>
            </ol>
        </nav>
    </div>

    {{-- Hero Profile Banner Card --}}
    <div class="card mb-4 border-0 shadow-sm overflow-hidden" style="border-radius: 16px;">
        <div style="background: {{ $user->banner ? 'url(' . asset($user->banner) . ') center/cover no-repeat' : 'linear-gradient(135deg, #666cff 0%, #4f46e5 100%)' }}; height: 160px; position: relative;">
            @if(!$user->banner)
                <div class="position-absolute end-0 bottom-0 opacity-25 p-3">
                    <i class="mdi mdi-account-circle-outline text-white" style="font-size: 140px; margin-right: -20px; margin-bottom: -40px;"></i>
                </div>
            @endif
            @if(Auth::id() == $user->id || in_array(Auth::user()->role, ['Admin', 'HRD', 'Super Admin']))
                <button type="button" class="btn btn-sm btn-white bg-white text-dark shadow-sm position-absolute end-0 top-0 m-3 d-flex align-items-center gap-1 border-0" data-bs-toggle="modal" data-bs-target="#changeBannerModal">
                    <i class="mdi mdi-camera-outline fs-5 text-primary"></i> <span class="d-none d-sm-inline fw-semibold">Ubah Banner</span>
                </button>
            @endif
        </div>
        <div class="card-body pt-0 pb-4">
            <div class="d-flex flex-column flex-md-row align-items-center align-items-md-end gap-3" style="margin-top: -50px;">
                <div class="position-relative">
                    <img src="{{ $userAvatar }}" alt="{{ $user->name }}" class="rounded-circle border border-4 border-white shadow" style="width: 110px; height: 110px; object-fit: cover;">
                    <span class="position-absolute bottom-0 end-0 p-1 bg-{{ $user->active == '1' ? 'success' : 'danger' }} border border-2 border-white rounded-circle" title="{{ $user->active == '1' ? 'Akun Aktif' : 'Non-Aktif' }}" style="width: 16px; height: 16px;"></span>
                </div>
                <div class="flex-grow-1 text-center text-md-start">
                    <h4 class="fw-bold mb-1 text-dark">{{ $user->name }}</h4>
                    <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-md-start gap-2 mb-2">
                        <span class="badge bg-label-primary px-2.5 py-1 fw-semibold"><i class="mdi mdi-shield-account-outline me-1"></i>{{ $user->role }}</span>
                        @if ($employee && $employee->department)
                            <span class="badge bg-label-info px-2.5 py-1 fw-semibold"><i class="mdi mdi-domain me-1"></i>{{ $employee->department->name }}</span>
                        @endif
                        @if ($employee && $employee->position)
                            <span class="badge bg-label-secondary px-2.5 py-1 fw-semibold"><i class="mdi mdi-briefcase-outline me-1"></i>{{ $employee->position->name }}</span>
                        @endif
                        @if ($employee && $employee->nik)
                            <span class="badge bg-label-dark px-2.5 py-1 fw-semibold"><i class="mdi mdi-barcode me-1"></i>NIK: {{ $employee->nik }}</span>
                        @elseif ($user->code)
                            <span class="badge bg-label-warning px-2.5 py-1 fw-semibold"><i class="mdi mdi-ticket-confirmation-outline me-1"></i>Code: {{ $user->code }}</span>
                        @endif
                        @if ($user->area)
                            <span class="badge bg-label-warning px-2.5 py-1 fw-semibold"><i class="mdi mdi-map-marker-outline me-1"></i>{{ $user->area }}</span>
                        @endif
                    </div>
                    <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-md-start gap-3 small text-muted">
                        <span><i class="mdi mdi-email-outline me-1 text-primary"></i>{{ $user->email }}</span>
                        @if ($user->phone)
                            <span><i class="mdi mdi-phone-outline me-1 text-success"></i>{{ $user->phone }}</span>
                        @endif
                        @if ($joinDate)
                            <span><i class="mdi mdi-calendar-check me-1 text-info"></i>Bergabung: {{ $joinDate->format('d M Y') }} ({{ $tenureStr }})</span>
                        @endif
                    </div>
                </div>
                <div class="d-flex gap-2 align-items-center">
                    @if ($isOwnProfile || in_array(Auth::user()->role, ['Admin', 'HRD']))
                        <a href="{{ route('profile.edit', $user->id) }}" class="btn btn-primary shadow-xs">
                            <i class="mdi mdi-account-edit-outline me-1"></i> Edit Data Akun
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Quick KPI Summary Cards --}}
    <div class="row g-3 mb-4">
        {{-- Card 1: Status Presensi Hari Ini --}}
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <span class="text-muted fw-semibold small text-uppercase">Presensi Hari Ini</span>
                        <div class="avatar avatar-sm">
                            <span class="avatar-initial rounded bg-label-{{ ($todayAttendance && $todayAttendance->clock_in) ? 'success' : 'warning' }}">
                                <i class="mdi mdi-clock-check-outline mdi-20px"></i>
                            </span>
                        </div>
                    </div>
                    @if ($todayAttendance && $todayAttendance->clock_out)
                        <h4 class="mb-0 fw-bold text-success">{{ substr($todayAttendance->clock_in, 0, 5) }} - {{ substr($todayAttendance->clock_out, 0, 5) }}</h4>
                        <span class="text-muted small"><i class="mdi mdi-check-all text-success me-1"></i>Selesai Pulang</span>
                    @elseif ($todayAttendance && $todayAttendance->clock_in)
                        <h4 class="mb-0 fw-bold text-primary">{{ substr($todayAttendance->clock_in, 0, 5) }} WIB</h4>
                        <span class="text-muted small"><i class="mdi mdi-clock-in text-primary me-1"></i>Sedang Masuk</span>
                    @else
                        <h4 class="mb-0 fw-bold text-secondary">Belum Absen</h4>
                        <span class="text-muted small"><i class="mdi mdi-alert-circle-outline text-warning me-1"></i>Hari Ini</span>
                    @endif
                </div>
            </div>
        </div>

        {{-- Card 2: Sisa Kuota Cuti --}}
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <span class="text-muted fw-semibold small text-uppercase">Sisa Cuti {{ date('Y') }}</span>
                        <div class="avatar avatar-sm">
                            <span class="avatar-initial rounded bg-label-primary">
                                <i class="mdi mdi-beach mdi-20px"></i>
                            </span>
                        </div>
                    </div>
                    <h4 class="mb-0 fw-bold text-primary">{{ $leaveBalance?->remaining_quota ?? 12 }} Hari</h4>
                    <span class="text-muted small"><i class="mdi mdi-calendar-blank-outline me-1"></i>Dari kuota {{ $leaveBalance?->total_quota ?? 12 }} hari</span>
                </div>
            </div>
        </div>

        {{-- Card 3: Alat Kerja / Fasilitas --}}
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <span class="text-muted fw-semibold small text-uppercase">Alat Kerja &amp; Fasilitas</span>
                        <div class="avatar avatar-sm">
                            <span class="avatar-initial rounded bg-label-warning">
                                <i class="mdi mdi-laptop mdi-20px"></i>
                            </span>
                        </div>
                    </div>
                    <h4 class="mb-0 fw-bold text-warning">{{ $myAssets->count() }} Unit</h4>
                    <span class="text-muted small"><i class="mdi mdi-check-circle-outline me-1"></i>Fasilitas aktif dipegang</span>
                </div>
            </div>
        </div>

        {{-- Card 4: Status Kepegawaian --}}
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <span class="text-muted fw-semibold small text-uppercase">Status Karyawan</span>
                        <div class="avatar avatar-sm">
                            <span class="avatar-initial rounded bg-label-info">
                                <i class="mdi mdi-account-check-outline mdi-20px"></i>
                            </span>
                        </div>
                    </div>
                    <h4 class="mb-0 fw-bold text-info">{{ $employee?->employment_status ?? 'Aktif' }}</h4>
                    <span class="text-muted small"><i class="mdi mdi-briefcase-outline me-1"></i>{{ $employee?->position?->name ?? $user->role }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Tabbed Navigation --}}
    <div class="nav-align-top mb-4">
        <ul class="nav nav-tabs nav-fill shadow-xs" role="tablist">
            <li class="nav-item">
                <button type="button" class="nav-link active py-2.5" role="tab" data-bs-toggle="tab" data-bs-target="#tabProfileData">
                    <i class="mdi mdi-card-account-details-outline me-2"></i> Data Karyawan
                </button>
            </li>
            @if ($employee)
                <li class="nav-item">
                    <button type="button" class="nav-link py-2.5" role="tab" data-bs-toggle="tab" data-bs-target="#tabAttendance">
                        <i class="mdi mdi-calendar-clock-outline me-2"></i> Presensi &amp; Kehadiran
                    </button>
                </li>
                <li class="nav-item">
                    <button type="button" class="nav-link py-2.5" role="tab" data-bs-toggle="tab" data-bs-target="#tabLeave">
                        <i class="mdi mdi-calendar-remove-outline me-2"></i> Cuti &amp; Izin
                    </button>
                </li>
                @if ($isOwnProfile || $isAdminOrHr)
                    <li class="nav-item">
                        <button type="button" class="nav-link py-2.5" role="tab" data-bs-toggle="tab" data-bs-target="#tabPayslip">
                            <i class="mdi mdi-cash-multiple me-2"></i> Slip Gaji
                        </button>
                    </li>
                @endif
                <li class="nav-item">
                    <button type="button" class="nav-link py-2.5" role="tab" data-bs-toggle="tab" data-bs-target="#tabAssets">
                        <i class="mdi mdi-laptop me-2"></i> Alat Kerja
                    </button>
                </li>
                <li class="nav-item">
                    <button type="button" class="nav-link py-2.5" role="tab" data-bs-toggle="tab" data-bs-target="#tabClaims">
                        <i class="mdi mdi-receipt-text-outline me-2"></i> Klaim Biaya
                    </button>
                </li>
            @endif
            @if ($isSales)
                <li class="nav-item">
                    <button type="button" class="nav-link py-2.5" role="tab" data-bs-toggle="tab" data-bs-target="#tabSales">
                        <i class="mdi mdi-chart-areaspline me-2"></i> Kinerja Sales
                    </button>
                </li>
            @endif
        </ul>

        <div class="tab-content p-0 bg-transparent border-0 mt-3">
            {{-- ══════════════════════════════════════════════════════════════════════ --}}
            {{-- ── TAB 1: DATA PRIBADI & KEPEGAWAIAN ─────────────────────────────── --}}
            {{-- ══════════════════════════════════════════════════════════════════════ --}}
            <div class="tab-pane fade show active" id="tabProfileData" role="tabpanel">
                <div class="row g-3">
                    {{-- Section A: Informasi Pribadi & Kontak --}}
                    <div class="col-12 col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-light py-3 border-bottom d-flex align-items-center gap-2">
                                <i class="mdi mdi-account-circle-outline fs-5 text-primary"></i>
                                <h6 class="card-title mb-0 fw-bold">Data Pribadi &amp; Kontak</h6>
                            </div>
                            <div class="card-body p-3">
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2 border-0">
                                        <span class="text-muted small">Nama Lengkap</span>
                                        <span class="fw-semibold text-heading">{{ $user->name }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2 border-0">
                                        <span class="text-muted small">Nomor Induk Karyawan (NIK)</span>
                                        <span class="font-monospace fw-bold text-primary">{{ $employee?->nik ?: ($user->nip ?: '—') }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2 border-0">
                                        <span class="text-muted small">Email Login</span>
                                        <span class="fw-semibold">{{ $user->email }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2 border-0">
                                        <span class="text-muted small">Nomor Telepon / WA</span>
                                        <div>
                                            @if ($rawPhone)
                                                <span class="fw-semibold">{{ $rawPhone }}</span>
                                                @if ($cleanPhone)
                                                    <a href="https://wa.me/{{ $cleanPhone }}" target="_blank" class="btn btn-xs btn-outline-success ms-1 py-0 px-1.5" title="Kirim Pesan WhatsApp">
                                                        <i class="mdi mdi-whatsapp"></i> WA
                                                    </a>
                                                @endif
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </div>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2 border-0">
                                        <span class="text-muted small">Tanggal Lahir</span>
                                        <span class="fw-semibold">
                                            {{ $employee?->birthday ? $employee->birthday->translatedFormat('d F Y') : ($user->birthday ? \Carbon\Carbon::parse($user->birthday)->translatedFormat('d F Y') : '—') }}
                                        </span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-start px-0 py-2 border-0">
                                        <span class="text-muted small">Alamat Domisili</span>
                                        <span class="fw-semibold text-end text-truncate" style="max-width: 250px;">
                                            {{ $employee?->address ?: ($user->address ?: '—') }}
                                        </span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    {{-- Section B: Informasi Kepegawaian & Jabatan --}}
                    <div class="col-12 col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-light py-3 border-bottom d-flex align-items-center gap-2">
                                <i class="mdi mdi-briefcase-outline fs-5 text-primary"></i>
                                <h6 class="card-title mb-0 fw-bold">Data Kepegawaian &amp; Posisi</h6>
                            </div>
                            <div class="card-body p-3">
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2 border-0">
                                        <span class="text-muted small">Departemen / Divisi</span>
                                        <span class="badge bg-label-info fw-semibold">{{ $employee?->department?->name ?: '—' }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2 border-0">
                                        <span class="text-muted small">Jabatan / Posisi</span>
                                        <span class="badge bg-label-primary fw-semibold">{{ $employee?->position?->name ?: ($user->role ?: '—') }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2 border-0">
                                        <span class="text-muted small">Status Kepegawaian</span>
                                        <span class="badge bg-label-success">{{ $employee?->employment_status ?: 'Tetap' }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2 border-0">
                                        <span class="text-muted small">Tanggal Masuk (Join Date)</span>
                                        <span class="fw-semibold">{{ $joinDate ? $joinDate->translatedFormat('d F Y') : '—' }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2 border-0">
                                        <span class="text-muted small">Masa Kerja (Tenure)</span>
                                        <span class="badge bg-label-secondary font-monospace">{{ $tenureStr }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2 border-0">
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

                    {{-- Section C: Info Gaji & Fasilitas (jika ada data salary) --}}
                    @if ($employee && $employee->salary && ($isOwnProfile || $isAdminOrHr))
                        <div class="col-12">
                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-light py-3 border-bottom d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="mdi mdi-bank fs-5 text-primary"></i>
                                        <h6 class="card-title mb-0 fw-bold">Informasi Rekening &amp; Finansial</h6>
                                    </div>
                                    <span class="badge bg-label-success">Rekening Payroll Aktif</span>
                                </div>
                                <div class="card-body p-3">
                                    <div class="row g-3">
                                        <div class="col-12 col-md-4">
                                            <div class="p-3 bg-light rounded">
                                                <span class="text-muted small d-block mb-1">Nama Bank &amp; No. Rekening</span>
                                                <div class="fw-bold text-heading fs-6">{{ $employee->salary->bank_name ?: 'BCA' }} — {{ $employee->salary->bank_account ?: '-' }}</div>
                                                <small class="text-muted">a.n {{ $employee->salary->bank_holder ?: $user->name }}</small>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-4">
                                            <div class="p-3 bg-light rounded">
                                                <span class="text-muted small d-block mb-1">Nomor BPJS Kesehatan</span>
                                                <div class="font-monospace fw-bold text-primary">{{ $employee->salary->bpjs_kesehatan_number ?: '—' }}</div>
                                                <small class="text-muted">Status: {{ $employee->salary->bpjs_kesehatan_number ? 'Terdaftar' : 'Belum Didaftarkan' }}</small>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-4">
                                            <div class="p-3 bg-light rounded">
                                                <span class="text-muted small d-block mb-1">Nomor BPJS Ketenagakerjaan</span>
                                                <div class="font-monospace fw-bold text-primary">{{ $employee->salary->bpjs_ketenagakerjaan_number ?: '—' }}</div>
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
                {{-- ══════════════════════════════════════════════════════════════════ --}}
                {{-- ── TAB 2: PRESENSI & ABSENSI ─────────────────────────────────────── --}}
                {{-- ══════════════════════════════════════════════════════════════════ --}}
                <div class="tab-pane fade" id="tabAttendance" role="tabpanel">
                    {{-- Clock In / Clock Out Action Widget --}}
                    @if ($isOwnProfile)
                        <div class="card border-0 shadow-sm mb-4 bg-primary text-white">
                            <div class="card-body p-4 d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                                <div>
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <span class="badge bg-white text-primary fw-semibold"><i class="mdi mdi-calendar-today me-1"></i>{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</span>
                                        <span class="badge bg-label-light text-white font-monospace" id="liveTimeClock"></span>
                                    </div>
                                    <h4 class="fw-bold text-white mb-1">Presensi Kerja Online</h4>
                                    <p class="mb-0 opacity-75 small">Catat waktu kehadiran masuk dan pulang kerja secara digital.</p>
                                </div>
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    @if (!$employee->can_online_attendance)
                                        <span class="badge bg-white text-secondary py-2 px-3 fs-7">
                                            <i class="mdi mdi-shield-account-outline me-1"></i> Bebas Presensi Online
                                        </span>
                                    @elseif (!$todayAttendance || !$todayAttendance->clock_in)
                                        <button type="button" class="btn btn-success btn-lg shadow-sm px-4 waves-effect" data-bs-toggle="modal" data-bs-target="#navClockInModal">
                                            <i class="mdi mdi-clock-in me-1"></i> Presensi Masuk (Clock In)
                                        </button>
                                    @elseif ($todayAttendance && !$todayAttendance->clock_out)
                                        <button type="button" class="btn btn-warning btn-lg shadow-sm px-4 waves-effect" data-bs-toggle="modal" data-bs-target="#navClockOutModal">
                                            <i class="mdi mdi-clock-out me-1"></i> Presensi Pulang (Clock Out)
                                        </button>
                                    @else
                                        <span class="badge bg-success py-2 px-3 fs-7 shadow-xs">
                                            <i class="mdi mdi-check-all me-1"></i> Presensi Hari Ini Lengkap ({{ substr($todayAttendance->clock_in, 0, 5) }} - {{ substr($todayAttendance->clock_out, 0, 5) }})
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Table Riwayat Presensi Bulan Ini --}}
                    <div class="card border-0 shadow-sm">
                        <div class="card-header border-bottom py-3 bg-light d-flex justify-content-between align-items-center">
                            <h6 class="card-title mb-0 fw-bold">Riwayat Kehadiran Bulan Ini ({{ \Carbon\Carbon::now()->translatedFormat('F Y') }})</h6>
                            <span class="badge bg-label-primary">{{ $monthAttendances->count() }} Hari Tercatat</span>
                        </div>
                        <div class="table-responsive text-nowrap">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Jam Masuk</th>
                                        <th>Jam Pulang</th>
                                        <th>Tipe Kerja</th>
                                        <th>Status</th>
                                        <th>Keterlambatan / Lembur</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($monthAttendances as $att)
                                        <tr>
                                            <td>
                                                <span class="fw-semibold text-heading">{{ \Carbon\Carbon::parse($att->date)->translatedFormat('l, d M Y') }}</span>
                                            </td>
                                            <td>
                                                @if ($att->clock_in)
                                                    <span class="badge bg-label-dark font-monospace">{{ substr($att->clock_in, 0, 5) }}</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($att->clock_out)
                                                    <span class="badge bg-label-dark font-monospace">{{ substr($att->clock_out, 0, 5) }}</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td><span class="badge bg-label-secondary small">{{ $att->work_type }}</span></td>
                                            <td>
                                                <span class="badge bg-label-{{ $att->status === 'Hadir' ? 'success' : ($att->status === 'Izin' ? 'info' : 'warning') }}">
                                                    {{ $att->status }}
                                                </span>
                                            </td>
                                            <td>
                                                @if ($att->late_minutes > 0)
                                                    <span class="badge bg-label-warning me-1">Telat {{ $att->late_minutes }}m</span>
                                                @endif
                                                @if ($att->overtime_minutes > 0)
                                                    <span class="badge bg-label-info">Lembur {{ round($att->overtime_minutes / 60, 1) }}j</span>
                                                @endif
                                                @if ($att->late_minutes == 0 && $att->overtime_minutes == 0)
                                                    <span class="text-muted small">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-4 text-muted">Belum ada riwayat presensi bulan ini.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- ══════════════════════════════════════════════════════════════════ --}}
                {{-- ── TAB 3: CUTI & IZIN ────────────────────────────────────────────── --}}
                {{-- ══════════════════════════════════════════════════════════════════ --}}
                <div class="tab-pane fade" id="tabLeave" role="tabpanel">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header border-bottom py-3 bg-light d-flex justify-content-between align-items-center">
                            <h6 class="card-title mb-0 fw-bold">Pengajuan &amp; Saldo Cuti Karyawan</h6>
                            @if ($isOwnProfile)
                                <button type="button" class="btn btn-sm btn-primary shadow-xs" data-bs-toggle="modal" data-bs-target="#modalEssNewLeave">
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
                                                <span class="fw-semibold text-heading">{{ $leave->leaveType?->name ?? 'Cuti Tahunan' }}</span>
                                            </td>
                                            <td>
                                                {{ \Carbon\Carbon::parse($leave->start_date)->format('d M Y') }} - {{ \Carbon\Carbon::parse($leave->end_date)->format('d M Y') }}
                                            </td>
                                            <td>
                                                <span class="badge bg-label-info">{{ $leave->total_days }} Hari</span>
                                            </td>
                                            <td>
                                                <span class="small text-muted text-truncate d-block" style="max-width: 250px;">{{ $leave->reason }}</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-label-{{ $leave->status === 'Approved' ? 'success' : ($leave->status === 'Pending' ? 'warning' : 'danger') }}">
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

                {{-- ══════════════════════════════════════════════════════════════════ --}}
                {{-- ── TAB 4: SLIP GAJI ──────────────────────────────────────────────── --}}
                {{-- ══════════════════════════════════════════════════════════════════ --}}
                @if ($isOwnProfile || $isAdminOrHr)
                    <div class="tab-pane fade" id="tabPayslip" role="tabpanel">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header border-bottom py-3 bg-light d-flex justify-content-between align-items-center">
                                <h6 class="card-title mb-0 fw-bold">Arsip Slip Gaji Digital</h6>
                                <span class="badge bg-label-success">{{ $myPayslips->count() }} Slip Tersedia</span>
                            </div>
                            <div class="table-responsive text-nowrap">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Periode Gaji</th>
                                            <th>Gaji Pokok &amp; Tunjangan</th>
                                            <th>Total Potongan</th>
                                            <th>Gaji Bersih (Take Home Pay)</th>
                                            <th class="text-end">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($myPayslips as $item)
                                            <tr>
                                                <td>
                                                    <div class="fw-bold text-heading">{{ $item->payroll?->period_name ?? 'Periode Gaji' }}</div>
                                                    <small class="text-muted">{{ $item->payroll ? \Carbon\Carbon::parse($item->payroll->payment_date)->format('d M Y') : '-' }}</small>
                                                </td>
                                                <td>
                                                    <span class="fw-semibold text-dark">Rp {{ number_format((float)($item->basic_salary ?? 0) + (float)($item->allowances_total ?? 0), 0, ',', '.') }}</span>
                                                </td>
                                                <td>
                                                    <span class="text-danger">Rp {{ number_format((float)($item->deductions_total ?? 0), 0, ',', '.') }}</span>
                                                </td>
                                                <td>
                                                    <span class="fw-bold text-success fs-6">Rp {{ number_format((float)($item->net_salary ?? 0), 0, ',', '.') }}</span>
                                                </td>
                                                <td class="text-end">
                                                    <a href="{{ route('hr.payrolls.slip', $item->id) }}" target="_blank" class="btn btn-sm btn-outline-primary shadow-xs">
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

                {{-- ══════════════════════════════════════════════════════════════════ --}}
                {{-- ── TAB 5: ALAT KERJA & FASILITAS (FIXED ASSET INTEGRATION) ───────── --}}
                {{-- ══════════════════════════════════════════════════════════════════ --}}
                <div class="tab-pane fade" id="tabAssets" role="tabpanel">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header border-bottom py-3 bg-light d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title mb-0 fw-bold">Inventaris Alat Kerja &amp; Fasilitas Terdaftar</h6>
                                <small class="text-muted">Aset &amp; alat kerja operasional yang sedang dipegang oleh karyawan</small>
                            </div>
                            <span class="badge bg-label-warning">{{ $myAssets->count() }} Unit Dipegang</span>
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
                                                <div class="d-flex align-items-center gap-1 mb-1">
                                                    @if ($asset->fixedAsset)
                                                        <span class="badge bg-label-info py-0 px-1 font-size-11">
                                                            <i class="mdi mdi-link-variant me-1"></i>{{ $asset->fixedAsset->type }}
                                                        </span>
                                                    @endif
                                                    <div class="fw-bold text-heading">{{ $asset->asset_name }}</div>
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
                                                <span class="badge bg-label-{{ $asset->condition === 'Baik' ? 'success' : ($asset->condition === 'Normal' ? 'info' : 'warning') }}">
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

                {{-- ══════════════════════════════════════════════════════════════════ --}}
                {{-- ── TAB 6: REIMBURSEMENT (KLAIM BIAYA) ─────────────────────────────── --}}
                {{-- ══════════════════════════════════════════════════════════════════ --}}
                <div class="tab-pane fade" id="tabClaims" role="tabpanel">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header border-bottom py-3 bg-light d-flex justify-content-between align-items-center">
                            <h6 class="card-title mb-0 fw-bold">Riwayat Klaim Reimbursement Biaya</h6>
                            @if ($isOwnProfile)
                                <button type="button" class="btn btn-sm btn-primary shadow-xs" data-bs-toggle="modal" data-bs-target="#modalEssNewClaim">
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
                                                <span class="badge bg-label-primary">{{ $claim->claim_type }}</span>
                                            </td>
                                            <td>{{ \Carbon\Carbon::parse($claim->event_date)->format('d M Y') }}</td>
                                            <td>
                                                <span class="fw-bold text-dark">Rp {{ number_format((float)($claim->amount ?? 0), 0, ',', '.') }}</span>
                                            </td>
                                            <td>
                                                <span class="small text-muted text-truncate d-block" style="max-width: 250px;">{{ $claim->description }}</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-label-{{ $claim->status === 'Paid' ? 'success' : ($claim->status === 'Approved' ? 'info' : ($claim->status === 'Pending' ? 'warning' : 'danger')) }}">
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

            {{-- ══════════════════════════════════════════════════════════════════ --}}
            {{-- ── TAB 7: KINERJA SALES (KHUSUS SALES) ────────────────────────────── --}}
            {{-- ══════════════════════════════════════════════════════════════════ --}}
            @if ($isSales)
                <div class="tab-pane fade" id="tabSales" role="tabpanel">
                    {{-- Sales KPI Metrics Grid --}}
                    <div class="row g-3 mb-4">
                        <div class="col-6 col-md-3">
                            <div class="card border-0 shadow-sm p-3">
                                <span class="text-muted small fw-semibold text-uppercase">Total Quotation</span>
                                <h3 class="fw-bold text-primary mb-0 mt-1">{{ number_format($salesMetrics['totalQuotations'] ?? 0) }}</h3>
                                <small class="text-muted">Smart &amp; Legacy Quote</small>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="card border-0 shadow-sm p-3">
                                <span class="text-muted small fw-semibold text-uppercase">Done PO</span>
                                <h3 class="fw-bold text-success mb-0 mt-1">{{ number_format($salesMetrics['countPoReceived'] ?? 0) }}</h3>
                                <small class="text-muted">Completed Orders</small>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="card border-0 shadow-sm p-3">
                                <span class="text-muted small fw-semibold text-uppercase">Total Client</span>
                                <h3 class="fw-bold text-info mb-0 mt-1">{{ number_format($salesMetrics['totalClients'] ?? 0) }}</h3>
                                <small class="text-muted">Assigned Companies</small>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="card border-0 shadow-sm p-3">
                                <span class="text-muted small fw-semibold text-uppercase">Active Customers</span>
                                <h3 class="fw-bold text-warning mb-0 mt-1">{{ number_format($salesMetrics['totalCustomers'] ?? 0) }}</h3>
                                <small class="text-muted">Repeat Buyers</small>
                            </div>
                        </div>
                    </div>

                    {{-- Payment Templates & Client List for Sales --}}
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-header bg-light py-3 border-bottom d-flex justify-content-between align-items-center">
                                    <h6 class="card-title mb-0 fw-bold">Template Syarat Pembayaran</h6>
                                    @if ($isOwnProfile)
                                        <button type="button" class="btn btn-sm btn-primary shadow-xs" data-bs-toggle="modal" data-bs-target="#createTemplateModal">
                                            <i class="mdi mdi-plus me-1"></i> Tambah Template
                                        </button>
                                    @endif
                                </div>
                                <div class="card-body p-3">
                                    @forelse ($paymentTemplates as $tpl)
                                        <div class="d-flex justify-content-between align-items-center p-2 mb-2 bg-light rounded border">
                                            <div>
                                                <div class="fw-semibold text-heading">{{ $tpl->name }}</div>
                                                <small class="text-muted">{{ $tpl->client?->company ?: 'General Template' }}</small>
                                            </div>
                                            <div>
                                                @if ($tpl->is_default)
                                                    <span class="badge bg-label-success">Default</span>
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
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-header bg-light py-3 border-bottom d-flex justify-content-between align-items-center">
                                    <h6 class="card-title mb-0 fw-bold">Klien &amp; Customer Terdaftar</h6>
                                    <span class="badge bg-label-primary">{{ $salesClients->count() }} Perusahaan</span>
                                </div>
                                <div class="card-body p-3">
                                    <div class="table-responsive" style="max-height: 280px; overflow-y: auto;">
                                        <table class="table table-sm align-middle mb-0">
                                            <tbody>
                                                @forelse ($salesClients as $cl)
                                                    <tr>
                                                        <td>
                                                            <div class="fw-semibold text-heading">{{ $cl->company }}</div>
                                                            <small class="text-muted">{{ $cl->address ?: 'Bandung / Jabar' }}</small>
                                                        </td>
                                                        <td class="text-end">
                                                            <span class="badge bg-label-{{ $cl->role == 'Customers' ? 'success' : 'info' }}">{{ $cl->role }}</span>
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
    {{-- ── MODAL: UBAH BANNER ──────────────────────────────────────────────── --}}
    {{-- ══════════════════════════════════════════════════════════════════════ --}}
    <div class="modal fade" id="changeBannerModal" tabindex="-1" aria-labelledby="changeBannerModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
                <form action="{{ route('profile.banner.update', $user->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header border-bottom py-3">
                        <h5 class="modal-title fw-bold" id="changeBannerModalLabel"><i class="mdi mdi-image-edit-outline me-2 text-primary"></i>Ubah Banner Profil</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark">Preview Banner Baru</label>
                            <div id="bannerPreviewContainer" class="w-100 rounded border d-flex align-items-center justify-content-center text-muted position-relative overflow-hidden" style="height: 140px; background: {{ $user->banner ? 'url(' . asset($user->banner) . ') center/cover no-repeat' : 'linear-gradient(135deg, #666cff 0%, #4f46e5 100%)' }};">
                                <span class="bg-dark bg-opacity-50 text-white px-3 py-1 rounded small">Pratinjau Banner</span>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="bannerInput" class="form-label fw-semibold text-dark">Pilih Gambar Banner (JPG, PNG, WebP — Maks. 5MB)</label>
                            <input class="form-control" type="file" id="bannerInput" name="banner" accept="image/*" required>
                        </div>
                    </div>
                    <div class="modal-footer border-top py-3">
                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary"><i class="mdi mdi-cloud-upload-outline me-1"></i> Simpan Banner</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @if ($employee && $isOwnProfile)
        {{-- ══════════════════════════════════════════════════════════════════════ --}}
        {{-- ── MODAL: AJUKAN CUTI MANDIRI ──────────────────────────────────────── --}}
        {{-- ══════════════════════════════════════════════════════════════════════ --}}
        <div class="modal fade" id="modalEssNewLeave" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <form action="{{ route('hr.leaves.store') }}" method="POST" enctype="multipart/form-data" class="modal-content border-0 shadow">
                    @csrf
                    <input type="hidden" name="employee_id" value="{{ $employee->id }}">
                    <div class="modal-header border-bottom bg-light">
                        <h5 class="modal-title fw-bold"><i class="mdi mdi-calendar-plus me-1 text-primary"></i>Ajukan Cuti / Izin Mandiri</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-semibold">Jenis Cuti</label>
                                <select name="leave_type_id" class="form-select" required>
                                    @foreach ($leaveTypes as $lt)
                                        <option value="{{ $lt->id }}">{{ $lt->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-semibold">Tanggal Mulai</label>
                                <input type="date" name="start_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-semibold">Tanggal Berakhir</label>
                                <input type="date" name="end_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Alasan / Keperluan</label>
                                <textarea name="reason" class="form-control" rows="3" placeholder="Jelaskan alasan pengajuan cuti/izin..." required></textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Lampiran (Opsional, Surat Dokter jika sakit)</label>
                                <input type="file" name="attachment" class="form-control" accept=".jpg,.jpeg,.png,.pdf">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top bg-light">
                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Kirim Permohonan Cuti</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════════════════════════ --}}
        {{-- ── MODAL: AJUKAN REIMBURSEMENT MANDIRI ─────────────────────────────── --}}
        {{-- ══════════════════════════════════════════════════════════════════════ --}}
        <div class="modal fade" id="modalEssNewClaim" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <form action="{{ route('hr.reimbursements.store') }}" method="POST" enctype="multipart/form-data" class="modal-content border-0 shadow">
                    @csrf
                    <input type="hidden" name="employee_id" value="{{ $employee->id }}">
                    <div class="modal-header border-bottom bg-light">
                        <h5 class="modal-title fw-bold"><i class="mdi mdi-receipt-text-plus me-1 text-primary"></i>Ajukan Klaim Reimbursement</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-6">
                                <label class="form-label fw-semibold">Kategori Biaya</label>
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
                                <label class="form-label fw-semibold">Tanggal Nota</label>
                                <input type="date" name="event_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Nominal Biaya (IDR)</label>
                                <input type="number" name="amount" class="form-control" placeholder="100000" min="1000" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Keperluan / Keterangan</label>
                                <textarea name="description" class="form-control" rows="2" placeholder="Tujuan pengeluaran..." required></textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Upload Foto Struk / Nota</label>
                                <input type="file" name="receipt_image" class="form-control" accept=".jpg,.jpeg,.png,.pdf">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top bg-light">
                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Kirim Klaim Biaya</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
@endsection

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
    });
</script>
@endpush
