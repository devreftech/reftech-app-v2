@extends('layouts.sales.app')
@section('title', 'Rekap & Akumulasi Denda Keterlambatan - HRM')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 text-muted small">
                <li class="breadcrumb-item">
                    <a href="{{ route('employees.index') }}" class="text-muted">
                        <i class="mdi mdi-account-group-outline me-1"></i>HR Management
                    </a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('hr.attendances.index') }}" class="text-muted">
                        Presensi &amp; Waktu Kerja
                    </a>
                </li>
                <li class="breadcrumb-item active text-danger fw-semibold" aria-current="page">Rekap Denda Keterlambatan</li>
            </ol>
        </nav>
        <h4 class="fw-bold mb-0 text-heading d-flex align-items-center gap-2">
            <i class="mdi mdi-cash-remove text-danger fs-3"></i>
            Rekap &amp; Akumulasi Denda Keterlambatan
        </h4>
        <small class="text-muted">
            Periode: <strong class="text-dark">{{ \Carbon\Carbon::createFromDate($year, $month, 1)->translatedFormat('F Y') }}</strong> &bull; 
            Skema: <span class="badge bg-label-danger font-11">{{ $latePenaltyType === 'per_minute' ? 'Per Menit (Rp '.number_format($latePenaltyRate, 0, ',', '.').'/mnt)' : 'Flat (Rp '.number_format($latePenaltyRate, 0, ',', '.').'/hari)' }}</span>
        </small>
    </div>

    <div class="d-flex align-items-center gap-2 flex-wrap">
        <a href="{{ route('hr.attendances.index') }}" class="btn btn-label-secondary shadow-xs d-flex align-items-center gap-1.5">
            <i class="mdi mdi-calendar-check-outline fs-5"></i>
            <span>Presensi Harian</span>
        </a>
        <a href="{{ route('hr.payrolls.index') }}" class="btn btn-outline-primary shadow-xs d-flex align-items-center gap-1.5">
            <i class="mdi mdi-calculator fs-5"></i>
            <span>Modul Payroll</span>
        </a>
        <button type="button" class="btn btn-outline-secondary shadow-xs d-flex align-items-center gap-1.5" onclick="window.print();">
            <i class="mdi mdi-printer-outline fs-5"></i>
            <span>Cetak Rekap</span>
        </button>
    </div>
</div>

{{-- KPI Summary Row --}}
<div class="row g-3 mb-4">
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm bg-label-danger h-100">
            <div class="card-body p-3.5">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="text-muted small fw-semibold text-uppercase font-11">Total Denda Terkumpul</span>
                    <div class="avatar avatar-xs bg-danger text-white rounded d-flex align-items-center justify-content-center">
                        <i class="mdi mdi-cash-multiple"></i>
                    </div>
                </div>
                <h3 class="fw-bold text-danger mb-0 font-22">Rp {{ number_format($totalPenaltyAccumulated, 0, ',', '.') }}</h3>
                <div class="text-muted small mt-1 font-11">Siap dipotongkan ke slip gaji Payroll</div>
            </div>
        </div>
    </div>
    
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm bg-label-warning h-100">
            <div class="card-body p-3.5">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="text-muted small fw-semibold text-uppercase font-11">Total Waktu Terlambat</span>
                    <div class="avatar avatar-xs bg-warning text-white rounded d-flex align-items-center justify-content-center">
                        <i class="mdi mdi-clock-alert-outline"></i>
                    </div>
                </div>
                <h3 class="fw-bold text-warning mb-0 font-22">{{ number_format($totalLateMinutes, 0, ',', '.') }} <small class="fs-6 fw-normal">Menit</small></h3>
                <div class="text-muted small mt-1 font-11">Setara {{ round($totalLateMinutes / 60, 1) }} Jam Jam Kerja Hilang</div>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm bg-label-info h-100">
            <div class="card-body p-3.5">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="text-muted small fw-semibold text-uppercase font-11">Karyawan Terlambat</span>
                    <div class="avatar avatar-xs bg-info text-white rounded d-flex align-items-center justify-content-center">
                        <i class="mdi mdi-account-clock-outline"></i>
                    </div>
                </div>
                <h3 class="fw-bold text-info mb-0 font-22">{{ $totalEmployeesLate }} <small class="fs-6 fw-normal">Orang</small></h3>
                <div class="text-muted small mt-1 font-11">Dari total {{ count($penaltyRecap) }} karyawan terdaftar</div>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm bg-label-primary h-100">
            <div class="card-body p-3.5">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="text-muted small fw-semibold text-uppercase font-11">Peringatan / Sanksi SP</span>
                    <div class="avatar avatar-xs bg-primary text-white rounded d-flex align-items-center justify-content-center">
                        <i class="mdi mdi-alert-octagon-outline"></i>
                    </div>
                </div>
                <h3 class="fw-bold text-primary mb-0 font-22">{{ $totalEmployeesWarning }} <small class="fs-6 fw-normal">Orang</small></h3>
                <div class="text-muted small mt-1 font-11">&ge; {{ $lateMultiplierThreshold }}x keterlambatan per bulan</div>
            </div>
        </div>
    </div>
</div>

{{-- Filter Card --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-3">
        <form action="{{ route('hr.attendances.penalties') }}" method="GET" class="row g-2.5 align-items-end">
            
            {{-- Switcher Bulan & Tahun --}}
            <div class="col-12 col-sm-6 col-md-3">
                <label class="form-label small fw-semibold mb-1"><i class="mdi mdi-calendar-month me-1 text-primary"></i>Pilih Periode Bulan</label>
                <select name="month_year_select" class="form-select form-select-sm" onchange="var p = this.value.split('-'); this.form.month.value = p[0]; this.form.year.value = p[1]; this.form.submit();">
                    @foreach ($availableMonths as $mOpt)
                        <option value="{{ $mOpt['month'] }}-{{ $mOpt['year'] }}" @selected($mOpt['month'] == $month && $mOpt['year'] == $year)>
                            {{ $mOpt['label'] }} {{ $mOpt['is_current'] ? '(Bulan Ini)' : '' }}
                        </option>
                    @endforeach
                </select>
                <input type="hidden" name="month" value="{{ $month }}">
                <input type="hidden" name="year" value="{{ $year }}">
            </div>

            {{-- Filter Departemen --}}
            <div class="col-12 col-sm-6 col-md-3">
                <label class="form-label small fw-semibold mb-1">Departemen</label>
                <select name="department_id" class="form-select form-select-sm">
                    <option value="">Semua Departemen</option>
                    @foreach ($departments as $dept)
                        <option value="{{ $dept->id }}" @selected($departmentId == $dept->id)>{{ $dept->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Filter Status Sanksi --}}
            <div class="col-12 col-sm-6 col-md-2">
                <label class="form-label small fw-semibold mb-1">Status Sanksi</label>
                <select name="strike_status" class="form-select form-select-sm">
                    <option value="all">Semua Status</option>
                    <option value="penalized" @selected($strikeFilter === 'penalized')>Kena Denda Aktif</option>
                    <option value="warning_sp" @selected($strikeFilter === 'warning_sp')>Peringatan SP (&ge;{{ $lateMultiplierThreshold }}x)</option>
                    <option value="tolerance" @selected($strikeFilter === 'tolerance')>Bebas Toleransi</option>
                    <option value="disciplined" @selected($strikeFilter === 'disciplined')>Disiplin (0x)</option>
                </select>
            </div>

            {{-- Search Input --}}
            <div class="col-12 col-sm-6 col-md-3">
                <label class="form-label small fw-semibold mb-1">Cari Karyawan</label>
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Nama atau NIK..." value="{{ $search }}">
            </div>

            {{-- Buttons --}}
            <div class="col-12 col-md-1 d-flex gap-1">
                <button type="submit" class="btn btn-sm btn-primary w-100" title="Terapkan Filter">
                    <i class="mdi mdi-filter-variant"></i>
                </button>
                <a href="{{ route('hr.attendances.penalties') }}" class="btn btn-sm btn-label-secondary" title="Reset Filter">
                    <i class="mdi mdi-refresh"></i>
                </a>
            </div>
        </form>
    </div>
</div>

{{-- Main Recap Table Card --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header border-bottom py-3 bg-white d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2">
        <div>
            <h6 class="mb-0 fw-bold text-heading font-15"><i class="mdi mdi-table-large me-1 text-primary"></i> Tabel Rekapitulasi Denda &amp; Pelanggaran Karyawan</h6>
            <small class="text-muted font-12">Menampilkan akumulasi seluruh keterlambatan karyawan pada periode {{ \Carbon\Carbon::createFromDate($year, $month, 1)->translatedFormat('F Y') }}</small>
        </div>
        <span class="badge bg-label-primary font-11 fw-semibold">{{ count($penaltyRecap) }} Karyawan Tercatat</span>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light">
                <tr>
                    <th style="width: 50px;">#</th>
                    <th>Karyawan</th>
                    <th>Departemen &amp; Jabatan</th>
                    <th class="text-center">Kehadiran</th>
                    <th class="text-center">Frekuensi Terlambat</th>
                    <th class="text-center">Total Keterlambatan</th>
                    <th class="text-center">Status Evaluasi / Sanksi</th>
                    <th class="text-end">Total Denda (Rp)</th>
                    <th class="text-center" style="width: 80px;">Rincian</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($penaltyRecap as $idx => $row)
                    @php
                        $emp = $row['employee'];
                        $empName = $emp->user?->name ?? 'Karyawan #' . $emp->id;
                        $initials = strtoupper(substr($empName, 0, 2));
                    @endphp
                    <tr>
                        <td class="text-muted small">{{ $idx + 1 }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2.5">
                                <div class="avatar avatar-sm bg-label-primary rounded-circle d-flex align-items-center justify-content-center fw-bold font-12">
                                    {{ $initials }}
                                </div>
                                <div>
                                    <div class="fw-bold text-heading font-13">{{ $empName }}</div>
                                    <div class="text-muted font-11 font-monospace">{{ $emp->nik }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="font-12 text-dark fw-semibold">{{ $emp->department?->name ?? 'Umum' }}</div>
                            <small class="text-muted font-11">{{ $emp->position?->name ?? '-' }}</small>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-label-secondary font-11">{{ $row['present_days'] }} Hari</span>
                        </td>
                        <td class="text-center">
                            @if ($row['late_days_count'] > 0)
                                <span class="badge bg-label-warning font-11 fw-bold">{{ $row['late_days_count'] }} Kali</span>
                            @else
                                <span class="badge bg-label-success font-11">0 Kali (Tepat Waktu)</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if ($row['late_minutes'] > 0)
                                <span class="fw-bold text-danger font-monospace font-13">{{ $row['late_minutes'] }} <small class="fw-normal">mnt</small></span>
                            @else
                                <span class="text-muted font-12">-</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <span class="badge {{ $row['strike_badge'] }} font-11 px-2.5 py-1">{{ $row['strike_status'] }}</span>
                        </td>
                        <td class="text-end font-monospace">
                            @if ($row['penalty_total'] > 0)
                                <span class="fw-bold text-danger font-14">Rp {{ number_format($row['penalty_total'], 0, ',', '.') }}</span>
                            @else
                                <span class="text-success fw-semibold font-12">Rp 0</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if ($row['late_days_count'] > 0)
                                <button class="btn btn-xs btn-label-primary btn-icon" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePenaltyDetail{{ $emp->id }}" aria-expanded="false" title="Buka Detail Keterlambatan">
                                    <i class="mdi mdi-chevron-down"></i>
                                </button>
                            @else
                                <span class="text-muted font-11">-</span>
                            @endif
                        </td>
                    </tr>
                    @if ($row['late_days_count'] > 0)
                        <tr class="collapse" id="collapsePenaltyDetail{{ $emp->id }}">
                            <td colspan="9" class="p-0 bg-light">
                                <div class="p-3.5 border-top border-bottom">
                                    <div class="d-flex align-items-center justify-content-between mb-2.5">
                                        <div class="small fw-bold text-dark font-12">
                                            <i class="mdi mdi-history me-1 text-danger"></i> Log Tanggal &amp; Rincian Denda Keterlambatan: <strong>{{ $empName }}</strong>
                                        </div>
                                        <span class="badge bg-white text-danger border font-11 fw-semibold">Total: Rp {{ number_format($row['penalty_total'], 0, ',', '.') }}</span>
                                    </div>
                                    <div class="d-flex flex-wrap gap-2.5">
                                        @foreach ($row['late_records'] as $latRec)
                                            <div class="card p-2.5 border bg-white shadow-none font-11" style="min-width: 200px; border-radius: 8px;">
                                                <div class="d-flex justify-content-between text-muted mb-1.5 pb-1 border-bottom">
                                                    <span class="fw-semibold"><i class="mdi mdi-calendar-blank me-1 text-primary"></i>{{ \Carbon\Carbon::parse($latRec->date)->translatedFormat('d M Y') }}</span>
                                                    <span class="font-monospace text-dark fw-bold">{{ substr($latRec->clock_in, 0, 5) }} WIB</span>
                                                </div>
                                                <div class="d-flex justify-content-between align-items-center pt-0.5">
                                                    <span class="badge bg-label-warning font-10">Terlambat {{ $latRec->late_minutes }}m</span>
                                                    <span class="font-monospace text-danger fw-bold font-12">Rp {{ number_format($latRec->penalty_amount ?? 0, 0, ',', '.') }}</span>
                                                </div>
                                                @if ($latRec->notes)
                                                    <div class="text-muted font-10 mt-1 fst-italic text-truncate">{{ $latRec->notes }}</div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endif
                @empty
                    <tr>
                        <td colspan="9" class="text-center py-5 text-muted">
                            <i class="mdi mdi-account-search-outline fs-1 d-block mb-2 opacity-50"></i>
                            Tidak ada data keterlambatan atau karyawan yang sesuai filter pada periode ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Footer Info Policy Card --}}
<div class="card border-0 shadow-sm bg-light mb-4">
    <div class="card-body p-3.5 d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
        <div class="d-flex align-items-center gap-2.5">
            <div class="avatar avatar-xs bg-label-info rounded d-flex align-items-center justify-content-center">
                <i class="mdi mdi-information-outline fs-5"></i>
            </div>
            <div class="font-12 text-muted">
                <strong>Kebijakan Presensi Aktif:</strong> Jam Masuk: <strong>{{ $workStartTime }} WIB</strong> &bull; Toleransi Harian: <strong>{{ $lateToleranceMinutes }} mnt</strong> &bull; Kuota Bebas Bulanan: <strong>{{ $lateFreeCountPerMonth }}x</strong> &bull; Batas Sanksi SP: <strong>&ge;{{ $lateMultiplierThreshold }}x</strong> (Pengali {{ $lateMultiplierRate }}x)
            </div>
        </div>
        <a href="{{ route('hr.attendances.index') }}" class="btn btn-xs btn-outline-primary text-nowrap">
            <i class="mdi mdi-cog-outline me-1"></i> Ubah Pengaturan Kebijakan
        </a>
    </div>
</div>
@endsection

@push('after-style')
<style>
    .font-10 { font-size: 10px !important; }
    .font-11 { font-size: 11px !important; }
    .font-12 { font-size: 12px !important; }
    .font-13 { font-size: 13px !important; }
    .font-14 { font-size: 14px !important; }
    .font-15 { font-size: 15px !important; }
    .font-22 { font-size: 22px !important; }
    @media print {
        .navbar, .layout-navbar, .footer, .btn, form {
            display: none !important;
        }
        .card {
            border: 1px solid #ccc !important;
            box-shadow: none !important;
        }
        .collapse {
            display: table-row !important;
        }
    }
</style>
@endpush
