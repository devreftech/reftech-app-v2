@extends('layouts.sales.app')
@section('title', 'Presensi & Kehadiran Karyawan - HRM')

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
                <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">Presensi &amp; Kehadiran</li>
            </ol>
        </nav>
        <h4 class="fw-bold mb-0 text-heading d-flex align-items-center flex-wrap gap-2">
            <span>Presensi &amp; Waktu Kerja</span>
            @if ($filterType === 'monthly')
                <span class="badge bg-label-primary fs-6 fw-semibold">
                    <i class="mdi mdi-calendar-month me-1"></i>{{ \Carbon\Carbon::create($selectedYear, $selectedMonthNum, 1)->translatedFormat('F Y') }}
                </span>
            @else
                <span class="badge bg-label-primary fs-6 fw-semibold">
                    <i class="mdi mdi-calendar-today me-1"></i>{{ \Carbon\Carbon::parse($selectedDate)->translatedFormat('d F Y') }}
                </span>
            @endif
        </h4>
    </div>

    <div class="d-flex align-items-center gap-2 flex-wrap">
        {{-- Tombol Menuju Halaman Rekap Denda Keterlambatan --}}
        <a href="{{ route('hr.attendances.penalties') }}" class="btn btn-outline-danger shadow-xs d-flex align-items-center gap-1.5" title="Buka Halaman Rekap &amp; Akumulasi Denda Keterlambatan">
            <i class="mdi mdi-cash-remove fs-5"></i>
            <span>Rekap Denda</span>
            @if ($totalMonthlyPenaltyAccumulated > 0)
                <span class="badge bg-danger ms-1">Rp {{ number_format($totalMonthlyPenaltyAccumulated, 0, ',', '.') }}</span>
            @endif
        </a>

        {{-- Tombol Pengaturan Presensi Simpel (Icon Setting) --}}
        <button type="button" class="btn btn-outline-secondary shadow-xs d-flex align-items-center gap-1.5" data-bs-toggle="modal" data-bs-target="#modalOfficeWifiSettings" title="Pengaturan Presensi, Anti-Fraud &amp; Kebijakan Denda">
            <i class="mdi mdi-cog-outline fs-5"></i>
            <span>Pengaturan</span>
        </button>

        {{-- Tombol Input Manual --}}
        <button type="button" class="btn btn-primary shadow-xs d-flex align-items-center gap-1.5" data-bs-toggle="modal" data-bs-target="#modalManualAttendance">
            <i class="mdi mdi-plus fs-5"></i>
            <span>Input Presensi</span>
        </button>

        <a href="{{ route('employees.index') }}" class="btn btn-label-secondary shadow-xs d-flex align-items-center gap-1.5">
            <i class="mdi mdi-arrow-left fs-5"></i>
            <span>Hub Karyawan</span>
        </a>
    </div>
</div>

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show shadow-xs border-0 mb-4" role="alert">
        <i class="mdi mdi-check-circle-outline me-1"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

{{-- Stat Cards --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-3">
                <span class="text-muted small fw-semibold d-block mb-1">Hadir Tepat Waktu</span>
                <h4 class="fw-bold text-success mb-0">{{ max(0, $stats['total_present'] - $stats['total_late']) }}</h4>
                <div class="text-muted small mt-1" style="font-size:0.75rem;">Status hadir tepat waktu</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-3">
                <span class="text-muted small fw-semibold d-block mb-1">Terlambat Masuk</span>
                <h4 class="fw-bold text-warning mb-0">{{ $stats['total_late'] }}</h4>
                <div class="text-muted small mt-1" style="font-size:0.75rem;">Lewat jam {{ $workStartTime ?? '08:30' }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-3">
                <span class="text-muted small fw-semibold d-block mb-1">Cuti &amp; Izin</span>
                <h4 class="fw-bold text-info mb-0">{{ $stats['total_leave'] }}</h4>
                <div class="text-muted small mt-1" style="font-size:0.75rem;">Disetujui HR</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-3">
                <span class="text-muted small fw-semibold d-block mb-1">Izin Sakit</span>
                <h4 class="fw-bold text-primary mb-0">{{ $stats['total_sick'] }}</h4>
                <div class="text-muted small mt-1" style="font-size:0.75rem;">Surat dokter</div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-3">
                <span class="text-muted small fw-semibold d-block mb-1">Alpa / Tanpa Kabar</span>
                <h4 class="fw-bold text-danger mb-0">{{ $stats['total_alpha'] }}</h4>
                <div class="text-muted small mt-1" style="font-size:0.75rem;">Potongan absensi</div>
            </div>
        </div>
    </div>
</div>

{{-- Filter Card --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-3">
        <form action="{{ route('hr.attendances.index') }}" method="GET" class="row g-2 align-items-center">
            <div class="col-12 col-sm-6 col-md-2">
                <label class="form-label small fw-semibold mb-1">Tipe Periode</label>
                <select name="filter_type" id="filterTypeSelect" class="form-select form-select-sm" onchange="toggleFilterType(this.value)">
                    <option value="daily" @selected($filterType === 'daily')>📅 Harian (Tanggal)</option>
                    <option value="monthly" @selected($filterType === 'monthly')>🗓️ Bulanan (Bulan)</option>
                </select>
            </div>
            <div class="col-12 col-sm-6 col-md-2">
                <div id="containerDailyFilter" style="{{ $filterType === 'monthly' ? 'display: none;' : '' }}">
                    <label class="form-label small fw-semibold mb-1">Pilih Tanggal</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light"><i class="mdi mdi-calendar"></i></span>
                        <input type="date" name="date" class="form-control form-control-sm" value="{{ $selectedDate }}">
                    </div>
                </div>
                <div id="containerMonthlyFilter" style="{{ $filterType !== 'monthly' ? 'display: none;' : '' }}">
                    <label class="form-label small fw-semibold mb-1">Pilih Bulan</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light"><i class="mdi mdi-calendar-month"></i></span>
                        <input type="month" name="month" class="form-control form-control-sm" value="{{ $selectedMonth }}">
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-3">
                <label class="form-label small fw-semibold mb-1">Departemen</label>
                <select name="department_id" class="form-select form-select-sm">
                    <option value="">Semua Departemen</option>
                    @foreach ($departments as $dept)
                        <option value="{{ $dept->id }}" @selected($departmentId == $dept->id)>{{ $dept->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-sm-6 col-md-2">
                <label class="form-label small fw-semibold mb-1">Status</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">Semua Status</option>
                    <option value="Hadir" @selected($status === 'Hadir')>Hadir</option>
                    <option value="Izin" @selected($status === 'Izin')>Izin</option>
                    <option value="Sakit" @selected($status === 'Sakit')>Sakit</option>
                    <option value="Cuti" @selected($status === 'Cuti')>Cuti</option>
                    <option value="Alpa" @selected($status === 'Alpa')>Alpa</option>
                </select>
            </div>
            <div class="col-12 col-sm-6 col-md-2">
                <label class="form-label small fw-semibold mb-1">Cari Karyawan / NIK</label>
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Nama atau NIK..." value="{{ $search }}">
            </div>
            <div class="col-12 col-md-1 d-flex align-items-end pt-md-3 gap-1">
                <button type="submit" class="btn btn-sm btn-primary w-100" title="Terapkan Filter">
                    <i class="mdi mdi-filter-variant"></i>
                </button>
                @if ($filterType === 'monthly' || $departmentId || $status || $search || $selectedDate !== \Carbon\Carbon::today('Asia/Jakarta')->toDateString())
                    <a href="{{ route('hr.attendances.index') }}" class="btn btn-sm btn-outline-secondary" title="Reset Filter ke Hari Ini">
                        <i class="mdi mdi-refresh"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

{{-- Attendance Table --}}
<div class="card border-0 shadow-sm">
    <div class="table-responsive text-nowrap">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th style="width: 50px;">#</th>
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
                    <th class="text-end">Aksi</th>
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
                        <td>{{ $attendances->firstItem() + $idx }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="avatar avatar-sm rounded-circle bg-label-primary d-flex align-items-center justify-content-center fw-bold">
                                    {{ strtoupper(substr($empName, 0, 2)) }}
                                </div>
                                <div>
                                    <a href="{{ route('employees.show', $att->employee_id) }}" class="fw-semibold text-heading d-block text-truncate" style="max-width: 180px;">
                                        {{ $empName }}
                                    </a>
                                    <span class="text-muted small font-monospace">NIK: {{ $att->employee->nik ?? '-' }}</span>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="fw-semibold text-heading small">{{ $att->employee->department?->name ?? '-' }}</div>
                            <span class="text-muted small">{{ $att->employee->position?->name ?? '-' }}</span>
                        </td>
                        @if ($filterType === 'monthly')
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="fw-semibold text-heading font-12 font-monospace">{{ \Carbon\Carbon::parse($att->date)->translatedFormat('d M Y') }}</span>
                                    <small class="text-muted font-11">{{ \Carbon\Carbon::parse($att->date)->translatedFormat('l') }}</small>
                                </div>
                            </td>
                        @endif
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                @if ($att->clock_in)
                                    <span class="badge bg-label-dark font-monospace">
                                        <i class="mdi mdi-clock-in me-1"></i>{{ substr($att->clock_in, 0, 5) }}
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif

                                @if ($att->selfie_in)
                                    <button type="button" class="btn btn-xs btn-label-success btn-icon rounded-circle" data-bs-toggle="modal" data-bs-target="#modalSelfiePreview{{ $att->id }}" title="Lihat Foto Selfie Masuk">
                                        <i class="mdi mdi-camera-account"></i>
                                    </button>

                                    {{-- Modal Preview Selfie --}}
                                    <div class="modal fade" id="modalSelfiePreview{{ $att->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-sm modal-dialog-centered">
                                            <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
                                                <div class="modal-header py-2 bg-light border-bottom">
                                                    <h6 class="modal-title fw-bold small mb-0"><i class="mdi mdi-camera-account me-1 text-success"></i> Selfie: {{ $empName }}</h6>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body p-2 text-center bg-dark">
                                                    <img src="{{ asset($att->selfie_in) }}" alt="Selfie Presensi" class="img-fluid rounded-2 shadow-sm" style="max-height: 360px; object-fit: cover;">
                                                    <div class="text-white-50 font-11 mt-2">
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
                                <span class="badge bg-label-dark font-monospace">
                                    <i class="mdi mdi-clock-out me-1"></i>{{ substr($att->clock_out, 0, 5) }}
                                </span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex flex-column gap-1 align-items-start">
                                <span class="badge bg-label-secondary small">{{ $att->work_type }}</span>
                                @if ($att->device_id)
                                    <span class="badge bg-label-info font-10" title="Device ID: {{ $att->device_id }}">
                                        <i class="mdi mdi-cellphone-check me-1"></i>{{ substr($att->device_id, 0, 11) }}...
                                    </span>
                                @endif
                            </div>
                        </td>
                        <td>
                            <div class="d-flex flex-column gap-1 align-items-start">
                                @if ($att->late_minutes > 0)
                                    <span class="badge bg-label-warning">
                                        <i class="mdi mdi-clock-alert-outline me-1"></i>Terlambat {{ $att->late_minutes }}m
                                    </span>
                                    @if ($att->penalty_amount > 0)
                                        <span class="badge bg-label-danger font-11">
                                            <i class="mdi mdi-cash-minus me-1"></i>Denda: Rp {{ number_format($att->penalty_amount, 0, ',', '.') }}
                                        </span>
                                    @else
                                        <span class="badge bg-label-info font-10">Bebas Denda</span>
                                    @endif
                                @endif
                                @if ($att->overtime_minutes > 0)
                                    <span class="badge bg-label-info">Lembur {{ round($att->overtime_minutes / 60, 1) }}j</span>
                                @endif
                                @if ($att->late_minutes == 0 && $att->overtime_minutes == 0)
                                    <span class="text-muted small">-</span>
                                @endif
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-label-{{ $badgeColor }}">{{ $att->status }}</span>
                        </td>
                        <td class="text-end">
                            <form action="{{ route('hr.attendances.destroy', $att->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus catatan presensi ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-icon btn-label-danger" title="Hapus">
                                    <i class="mdi mdi-trash-can-outline"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ $filterType === 'monthly' ? 10 : 9 }}" class="text-center py-5 text-muted">
                            <i class="mdi mdi-calendar-blank-outline fs-1 d-block mb-2 text-secondary"></i>
                            @if ($filterType === 'monthly')
                                Tidak ada data presensi pada bulan <strong>{{ \Carbon\Carbon::create($selectedYear, $selectedMonthNum, 1)->translatedFormat('F Y') }}</strong>.
                            @else
                                Tidak ada data presensi pada tanggal <strong>{{ \Carbon\Carbon::parse($selectedDate)->translatedFormat('d F Y') }}</strong>.
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($attendances->hasPages())
        <div class="card-footer border-top py-3 d-flex justify-content-end">
            {{ $attendances->links() }}
        </div>
    @endif
</div>

{{-- Modal Input Presensi Manual --}}
<div class="modal fade" id="modalManualAttendance" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('hr.attendances.store') }}" method="POST" class="modal-content border-0 shadow">
            @csrf
            <div class="modal-header border-bottom bg-light">
                <h5 class="modal-title fw-bold">Input Catatan Presensi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label fw-semibold">Pilih Karyawan</label>
                        <select name="employee_id" class="form-select" required>
                            <option value="">- Pilih Karyawan -</option>
                            @foreach ($employees as $emp)
                                <option value="{{ $emp->id }}">
                                    {{ $emp->user?->name ?? $emp->nik }} ({{ $emp->department?->name ?? 'HR' }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label class="form-label fw-semibold">Tanggal</label>
                        <input type="date" name="date" class="form-control" value="{{ $selectedDate }}" required>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label class="form-label fw-semibold">Status Kehadiran</label>
                        <select name="status" class="form-select" required>
                            <option value="Hadir">Hadir</option>
                            <option value="Izin">Izin</option>
                            <option value="Sakit">Sakit</option>
                            <option value="Cuti">Cuti</option>
                            <option value="Alpa">Alpa</option>
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-semibold">Jam Masuk (Clock In)</label>
                        <input type="time" name="clock_in" class="form-control" value="08:00">
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-semibold">Jam Pulang (Clock Out)</label>
                        <input type="time" name="clock_out" class="form-control" value="17:00">
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-semibold">Tipe Kerja</label>
                        <select name="work_type" class="form-select">
                            <option value="WFO">WFO (Kantor)</option>
                            <option value="WFH">WFH (Rumah)</option>
                            <option value="Site/Lapangan">Site / Lapangan</option>
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-semibold">Terlambat (Menit)</label>
                        <input type="number" name="late_minutes" class="form-control" value="0" min="0">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Nominal Denda (Rp) <small class="text-muted">(Opsional / Otomatis Dihitung)</small></label>
                        <input type="number" name="penalty_amount" class="form-control" placeholder="Kosongkan jika ingin dihitung otomatis oleh sistem">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Catatan / Keterangan</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="Keterangan tambahan..."></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top bg-light">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Presensi</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Pengaturan Keamanan Presensi, Anti-Fraud, & Kebijakan Denda (Sistem Tab Ala Task Kanban Modal) --}}
<div class="modal fade" id="modalOfficeWifiSettings" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
            <div style="height: 4px; background: linear-gradient(90deg, #696cff 0%, #03c3ec 50%, #71dd37 100%); width: 100%;"></div>
            
            {{-- Modal Header --}}
            <div class="modal-header border-bottom py-3 bg-light d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-3">
                    <div class="avatar avatar-md bg-label-primary rounded-3 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                        <i class="mdi mdi-shield-crown-outline fs-3 text-primary"></i>
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2">
                            <h5 class="modal-title fw-bold mb-0 text-heading">Pengaturan Presensi, Anti-Fraud &amp; Kebijakan Denda</h5>
                            <span class="badge bg-label-primary font-11 fw-semibold">HRMS Center</span>
                        </div>
                        <small class="text-muted">Konfigurasi jam operasional kantor, proteksi anti-kecurangan, auto clock-out, dan IP jaringan WiFi</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            {{-- Nav Tabs Header ala Kanban Modal --}}
            <div class="bg-white border-bottom px-4 pt-2">
                <ul class="nav nav-tabs border-0 gap-2" id="settingsTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active fw-semibold d-flex align-items-center gap-1.5 py-2.5 px-3" id="tab-penalty-tab" data-bs-toggle="tab" data-bs-target="#tab-penalty" type="button" role="tab" aria-controls="tab-penalty" aria-selected="true">
                            <i class="mdi mdi-cash-clock fs-5 text-danger"></i>
                            <span>Jam Masuk &amp; Denda Keterlambatan</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-semibold d-flex align-items-center gap-1.5 py-2.5 px-3" id="tab-security-tab" data-bs-toggle="tab" data-bs-target="#tab-security" type="button" role="tab" aria-controls="tab-security" aria-selected="false">
                            <i class="mdi mdi-shield-account-outline fs-5 text-primary"></i>
                            <span>Proteksi Anti-Fraud &amp; Selfie</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-semibold d-flex align-items-center gap-1.5 py-2.5 px-3" id="tab-autoclockout-tab" data-bs-toggle="tab" data-bs-target="#tab-autoclockout" type="button" role="tab" aria-controls="tab-autoclockout" aria-selected="false">
                            <i class="mdi mdi-clock-fast fs-5 text-success"></i>
                            <span>Jam Pulang &amp; Auto Clock-Out</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-semibold d-flex align-items-center gap-1.5 py-2.5 px-3" id="tab-wifi-tab" data-bs-toggle="tab" data-bs-target="#tab-wifi" type="button" role="tab" aria-controls="tab-wifi" aria-selected="false">
                            <i class="mdi mdi-wifi-check fs-5 text-info"></i>
                            <span>Jaringan WiFi Kantor ({{ $officeWifis->count() }})</span>
                        </button>
                    </li>
                </ul>
            </div>

            {{-- Modal Body: Form Utama --}}
            <div class="modal-body p-4 bg-light">
                <form action="{{ route('hr.attendances.settings.update') }}" method="POST" id="formMasterSettings">
                    @csrf
                    
                    <div class="tab-content p-0" id="settingsTabContent">
                        
                        {{-- TAB 1: Jam Masuk & Kebijakan Denda Keterlambatan --}}
                        <div class="tab-pane fade show active" id="tab-penalty" role="tabpanel" aria-labelledby="tab-penalty-tab">
                            <div class="card border-0 shadow-sm mb-0" style="border-radius: 12px;">
                                <div class="card-body p-4">
                                    <div class="d-flex align-items-center justify-content-between pb-3 mb-4 border-bottom">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="avatar avatar-md bg-label-danger rounded-3 d-flex align-items-center justify-content-center">
                                                <i class="mdi mdi-cash-clock fs-3"></i>
                                            </div>
                                            <div>
                                                <h5 class="fw-bold mb-0 text-heading">Kebijakan Denda &amp; Sanksi Keterlambatan</h5>
                                                <small class="text-muted">Aktifkan pemotongan denda otomatis per menit atau tarif flat bulanan</small>
                                            </div>
                                        </div>
                                        <div class="form-check form-switch mb-0">
                                            <input class="form-check-input cursor-pointer" type="checkbox" name="is_late_penalty_enabled" value="1"
                                                   id="switchLatePenalty" style="width: 3.2rem; height: 1.7rem;" @checked(isset($isLatePenaltyEnabled) && $isLatePenaltyEnabled)>
                                        </div>
                                    </div>

                                    <div class="row g-3">
                                        <div class="col-12 col-md-6 col-lg-3">
                                            <label class="form-label fw-semibold text-dark mb-1">Jam Masuk Standar Kantor</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light"><i class="mdi mdi-clock-in text-primary"></i></span>
                                                <input type="time" name="work_start_time" class="form-control" value="{{ $workStartTime ?? '08:30' }}" required>
                                                <span class="input-group-text bg-light">WIB</span>
                                            </div>
                                            <small class="text-muted font-11">Batas jam presensi normal</small>
                                        </div>

                                        <div class="col-12 col-md-6 col-lg-3">
                                            <label class="form-label fw-semibold text-dark mb-1">Toleransi Bebas Harian</label>
                                            <div class="input-group">
                                                <input type="number" name="late_tolerance_minutes" class="form-control" value="{{ $lateToleranceMinutes ?? 0 }}" min="0">
                                                <span class="input-group-text bg-light">Menit</span>
                                            </div>
                                            <small class="text-muted font-11">Contoh: 15 menit dispensasi</small>
                                        </div>

                                        <div class="col-12 col-md-6 col-lg-3">
                                            <label class="form-label fw-semibold text-dark mb-1">Skema Perhitungan Denda</label>
                                            <select name="late_penalty_type" class="form-select">
                                                <option value="per_minute" @selected(($latePenaltyType ?? 'per_minute') === 'per_minute')>Per Menit Keterlambatan</option>
                                                <option value="flat" @selected(($latePenaltyType ?? '') === 'flat')>Nominal Flat per Kejadian</option>
                                            </select>
                                            <small class="text-muted font-11">Dihitung dari selisih menit</small>
                                        </div>

                                        <div class="col-12 col-md-6 col-lg-3">
                                            <label class="form-label fw-semibold text-dark mb-1">Tarif Nominal Denda (Rp)</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light">Rp</span>
                                                <input type="number" name="late_penalty_rate" class="form-control" value="{{ $latePenaltyRate ?? 1000 }}" min="0" required>
                                            </div>
                                            <small class="text-muted font-11">Per menit / per kejadian</small>
                                        </div>

                                        <div class="col-12 col-md-4 mt-3">
                                            <div class="p-3 rounded border bg-light h-100">
                                                <label class="form-label fw-semibold text-dark mb-1"><i class="mdi mdi-shield-star-outline text-warning me-1"></i>Kuota Bebas Denda Bulanan</label>
                                                <div class="input-group input-group-sm mb-1">
                                                    <input type="number" name="late_free_count_per_month" class="form-control" value="{{ $lateFreeCountPerMonth ?? 2 }}" min="0" required>
                                                    <span class="input-group-text bg-white">Kali / Bulan</span>
                                                </div>
                                                <small class="text-muted font-11">Misal: 2x pertama terlambat dalam sebulan tidak dikenakan denda.</small>
                                            </div>
                                        </div>

                                        <div class="col-12 col-md-4 mt-3">
                                            <div class="p-3 rounded border bg-light h-100">
                                                <label class="form-label fw-semibold text-dark mb-1"><i class="mdi mdi-alert-octagon-outline text-danger me-1"></i>Batas Sanksi SP (Terlambat ke-)</label>
                                                <div class="input-group input-group-sm mb-1">
                                                    <input type="number" name="late_multiplier_threshold" class="form-control" value="{{ $lateMultiplierThreshold ?? 5 }}" min="1" required>
                                                    <span class="input-group-text bg-white">Kali</span>
                                                </div>
                                                <small class="text-muted font-11">Ambang batas akumulasi terlambat untuk memicu sanksi eskalasi.</small>
                                            </div>
                                        </div>

                                        <div class="col-12 col-md-4 mt-3">
                                            <div class="p-3 rounded border bg-light h-100">
                                                <label class="form-label fw-semibold text-dark mb-1"><i class="mdi mdi-multiplication text-danger me-1"></i>Pengali Denda Sanksi Eskalasi</label>
                                                <div class="input-group input-group-sm mb-1">
                                                    <input type="number" step="0.1" name="late_multiplier_rate" class="form-control" value="{{ $lateMultiplierRate ?? 2.0 }}" min="1" required>
                                                    <span class="input-group-text bg-white">x Lipat</span>
                                                </div>
                                                <small class="text-muted font-11">Contoh: 2.0 = denda menjadi 2x lipat jika melanggar batas SP.</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- TAB 2: Proteksi Multi-Layer Anti-Fraud & Selfie Live --}}
                        <div class="tab-pane fade" id="tab-security" role="tabpanel" aria-labelledby="tab-security-tab">
                            <div class="card border-0 shadow-sm mb-0" style="border-radius: 12px;">
                                <div class="card-body p-4">
                                    <div class="pb-3 mb-4 border-bottom">
                                        <h5 class="fw-bold mb-1 text-heading">Proteksi Keamanan Multi-Layer Anti-Fraud</h5>
                                        <small class="text-muted">Cegah segala bentuk manipulasi presensi: titip absen rekan kerja, pemalsuan lokasi, dan multi-akun.</small>
                                    </div>

                                    <div class="row g-3">
                                        {{-- 1. WiFi Restriction --}}
                                        <div class="col-12">
                                            <div class="d-flex align-items-center justify-content-between p-3.5 rounded border bg-white shadow-xs">
                                                <div class="d-flex align-items-start gap-3 me-3">
                                                    <div class="avatar avatar-md bg-label-primary rounded-3 d-flex align-items-center justify-content-center mt-1">
                                                        <i class="mdi mdi-wifi-check fs-3"></i>
                                                    </div>
                                                    <div>
                                                        <h6 class="fw-bold text-dark mb-1 font-15">Pembatasan Jaringan WiFi Resmi Kantor</h6>
                                                        <p class="text-muted mb-0 font-12">Karyawan hanya diperbolehkan melakukan Clock In / Out jika perangkat mereka terhubung ke IP Publik WiFi resmi kantor yang telah didaftarkan di whitelist.</p>
                                                    </div>
                                                </div>
                                                <div class="form-check form-switch mb-0">
                                                    <input class="form-check-input cursor-pointer" type="checkbox" name="is_wifi_restriction_enabled" value="1"
                                                           style="width: 3.2rem; height: 1.7rem;" @checked(isset($isWifiRestrictionEnabled) && $isWifiRestrictionEnabled)>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- 2. Anti-Titip Absen (Device Lock) --}}
                                        <div class="col-12">
                                            <div class="d-flex align-items-center justify-content-between p-3.5 rounded border bg-white shadow-xs">
                                                <div class="d-flex align-items-start gap-3 me-3">
                                                    <div class="avatar avatar-md bg-label-info rounded-3 d-flex align-items-center justify-content-center mt-1">
                                                        <i class="mdi mdi-cellphone-lock fs-3"></i>
                                                    </div>
                                                    <div>
                                                        <h6 class="fw-bold text-dark mb-1 font-15">Anti-Titip Absen (Kunci 1 Perangkat per Karyawan)</h6>
                                                        <p class="text-muted mb-0 font-12">Mengunci identitas perangkat (Device Fingerprint). Mencegah 1 HP/Laptop dipakai untuk mengabsenkan akun karyawan lain pada hari yang sama meskipun berada di kantor.</p>
                                                    </div>
                                                </div>
                                                <div class="form-check form-switch mb-0">
                                                    <input class="form-check-input cursor-pointer" type="checkbox" name="is_device_lock_enabled" value="1"
                                                           style="width: 3.2rem; height: 1.7rem;" @checked(isset($isDeviceLockEnabled) && $isDeviceLockEnabled)>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- 3. Live Camera Selfie --}}
                                        <div class="col-12">
                                            <div class="d-flex align-items-center justify-content-between p-3.5 rounded border bg-white shadow-xs">
                                                <div class="d-flex align-items-start gap-3 me-3">
                                                    <div class="avatar avatar-md bg-label-warning rounded-3 d-flex align-items-center justify-content-center mt-1">
                                                        <i class="mdi mdi-camera-iris fs-3"></i>
                                                    </div>
                                                    <div>
                                                        <h6 class="fw-bold text-dark mb-1 font-15">Wajibkan Foto Selfie Kamera Live saat Clock In</h6>
                                                        <p class="text-muted mb-0 font-12">Membuka modal kamera live interaktif saat tombol Clock In diklik dan mengambil bukti snapshot foto wajah karyawan secara instan.</p>
                                                    </div>
                                                </div>
                                                <div class="form-check form-switch mb-0">
                                                    <input class="form-check-input cursor-pointer" type="checkbox" name="is_selfie_required" value="1"
                                                           style="width: 3.2rem; height: 1.7rem;" @checked(isset($isSelfieRequired) && $isSelfieRequired)>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- TAB 3: Jam Pulang & Auto Clock-Out --}}
                        <div class="tab-pane fade" id="tab-autoclockout" role="tabpanel" aria-labelledby="tab-autoclockout-tab">
                            <div class="card border-0 shadow-sm mb-0" style="border-radius: 12px;">
                                <div class="card-body p-4">
                                    <div class="d-flex align-items-center justify-content-between pb-3 mb-4 border-bottom">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="avatar avatar-md bg-label-success rounded-3 d-flex align-items-center justify-content-center">
                                                <i class="mdi mdi-clock-fast fs-3"></i>
                                            </div>
                                            <div>
                                                <h5 class="fw-bold mb-0 text-heading">Auto Clock-Out Otomatis (Jam Pulang Default)</h5>
                                                <small class="text-muted">Otomatis mengisi jam pulang karyawan yang lupa atau belum melakukan Clock Out</small>
                                            </div>
                                        </div>
                                        <div class="form-check form-switch mb-0">
                                            <input class="form-check-input cursor-pointer" type="checkbox" name="is_auto_clock_out_enabled" value="1"
                                                   id="switchAutoClockOut" style="width: 3.2rem; height: 1.7rem;" @checked(isset($isAutoClockOutEnabled) && $isAutoClockOutEnabled)>
                                        </div>
                                    </div>

                                    <div class="row g-4 align-items-center">
                                        <div class="col-12 col-md-6">
                                            <label class="form-label fw-semibold text-dark mb-1">Jam Pulang Default Standar Kantor</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light"><i class="mdi mdi-clock-out text-success"></i></span>
                                                <input type="time" name="auto_clock_out_time" class="form-control" value="{{ $autoClockOutTime ?? '17:00' }}" required>
                                                <span class="input-group-text bg-light">WIB</span>
                                            </div>
                                            <small class="text-muted font-11">Jam ini yang akan diisikan ke absensi karyawan yang belum Clock Out</small>
                                        </div>

                                        <div class="col-12 col-md-6">
                                            <div class="p-3 rounded border bg-light">
                                                <div class="d-flex align-items-center gap-2 mb-2">
                                                    <i class="mdi mdi-robot-outline text-info fs-5"></i>
                                                    <span class="fw-bold text-dark font-13">Jadwal Eksekusi Otomatis (Cron Scheduler)</span>
                                                </div>
                                                <p class="text-muted font-11 mb-2">Sistem otomatis menjalankan evaluasi auto clock-out pada pukul <strong>17:05 WIB</strong> dan evaluasi sapu bersih harian pada <strong>23:55 WIB</strong>.</p>
                                                <button type="button" class="btn btn-sm btn-outline-success" onclick="document.getElementById('formAutoClockOutNow').submit();">
                                                    <i class="mdi mdi-lightning-bolt me-1"></i> Trigger Auto Clock-Out Hari Ini Sekarang
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </form>

                {{-- TAB 4: Jaringan WiFi Kantor (IP Whitelist) --}}
                <div class="tab-content p-0">
                    <div class="tab-pane fade" id="tab-wifi" role="tabpanel" aria-labelledby="tab-wifi-tab">
                        <div class="row g-3">
                            
                            {{-- Kolom Kiri: Deteksi IP & Form Tambah WiFi --}}
                            <div class="col-12 col-lg-5">
                                {{-- IP Admin Aktif --}}
                                <div class="p-3 rounded border mb-3 bg-white shadow-xs d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="avatar avatar-sm bg-label-primary rounded d-flex align-items-center justify-content-center">
                                            <i class="mdi mdi-ip-network-outline fs-4"></i>
                                        </div>
                                        <div>
                                            <div class="text-muted font-10 text-uppercase fw-semibold">IP Jaringan Anda Saat Ini</div>
                                            <span class="fw-bold font-monospace text-dark font-14">{{ $currentClientIp }}</span>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-xs btn-label-primary" onclick="autofillMyIp('{{ $currentClientIp }}')">
                                        <i class="mdi mdi-plus-circle-outline me-1"></i> Gunakan IP Ini
                                    </button>
                                </div>

                                {{-- Form Tambah WiFi Baru --}}
                                <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                                    <div class="card-header py-3 bg-white border-bottom">
                                        <h6 class="mb-0 fw-bold font-13 text-uppercase text-dark"><i class="mdi mdi-plus-circle text-primary me-1"></i> Tambah Jaringan WiFi Kantor</h6>
                                    </div>
                                    <div class="card-body p-3">
                                        <form action="{{ route('hr.attendances.wifis.store') }}" method="POST">
                                            @csrf
                                            <div class="row g-2.5">
                                                <div class="col-12">
                                                    <label class="form-label font-11 fw-semibold mb-1">Nama WiFi / Lokasi <span class="text-danger">*</span></label>
                                                    <input type="text" name="name" id="wifiNameInput" class="form-control form-control-sm" placeholder="Contoh: WiFi Kantor Pusat Lantai 1" required>
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label font-11 fw-semibold mb-1">IP Address (Public IP) <span class="text-danger">*</span></label>
                                                    <input type="text" name="ip_address" id="wifiIpInput" class="form-control form-control-sm font-monospace" placeholder="Contoh: 180.252.xxx.xxx" required>
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label font-11 fw-semibold mb-1">Catatan / Keterangan</label>
                                                    <input type="text" name="notes" class="form-control form-control-sm" placeholder="Opsional (ISP Biznet, Telkom, dsb)">
                                                </div>
                                                <div class="col-12 pt-2">
                                                    <button type="submit" class="btn btn-primary btn-sm w-100">
                                                        <i class="mdi mdi-plus-box-outline me-1"></i> Daftarkan IP WiFi ke Whitelist
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            {{-- Kolom Kanan: Tabel Daftar WiFi Whitelist --}}
                            <div class="col-12 col-lg-7">
                                <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
                                    <div class="card-header py-3 bg-white border-bottom d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-0 fw-bold font-13 text-uppercase text-dark"><i class="mdi mdi-wifi-check text-success me-1"></i> Daftar Jaringan WiFi Terdaftar</h6>
                                            <small class="text-muted font-11">Whitelist IP yang diizinkan untuk presensi karyawan</small>
                                        </div>
                                        <span class="badge bg-label-primary">{{ $officeWifis->count() }} WiFi Aktif</span>
                                    </div>
                                    <div class="table-responsive" style="max-height: 380px; overflow-y: auto;">
                                        <table class="table table-hover mb-0">
                                            <thead class="bg-light sticky-top">
                                                <tr>
                                                    <th>Nama Jaringan</th>
                                                    <th>IP Address</th>
                                                    <th>Catatan</th>
                                                    <th class="text-center" style="width: 70px;">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($officeWifis as $wifi)
                                                    <tr>
                                                        <td>
                                                            <div class="fw-semibold text-heading font-13">{{ $wifi->name }}</div>
                                                            <span class="badge bg-label-success font-10">Aktif</span>
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-light text-dark font-monospace border font-11">{{ $wifi->ip_address }}</span>
                                                        </td>
                                                        <td class="text-muted font-11">{{ $wifi->notes ?: '-' }}</td>
                                                        <td class="text-center">
                                                            <form action="{{ route('hr.attendances.wifis.destroy', $wifi->id) }}" method="POST" class="d-inline"
                                                                  onsubmit="return confirm('Hapus WiFi {{ $wifi->name }} dari daftar whitelist?')">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-xs btn-label-danger btn-icon" title="Hapus">
                                                                    <i class="mdi mdi-trash-can-outline"></i>
                                                                </button>
                                                            </form>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="4" class="text-center py-4 text-muted font-12">
                                                            <i class="mdi mdi-wifi-off fs-3 d-block mb-1 opacity-50"></i>
                                                            Belum ada IP WiFi kantor yang didaftarkan.
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
                </div>

                {{-- Hidden Form for Instant Auto Clock Out Trigger --}}
                <form id="formAutoClockOutNow" action="{{ route('hr.attendances.auto-clockout-now') }}" method="POST" class="d-none" onsubmit="return confirm('Jalankan Auto Clock-Out untuk semua karyawan yang belum Clock Out pada tanggal {{ $selectedDate }}?');">
                    @csrf
                    <input type="hidden" name="date" value="{{ $selectedDate }}">
                </form>
            </div>

            {{-- Modal Footer --}}
            <div class="modal-footer border-top bg-white py-3 px-4 d-flex justify-content-between align-items-center">
                <small class="text-muted font-11"><i class="mdi mdi-information-outline me-1"></i>Pengaturan yang disimpan akan langsung aktif untuk seluruh akun karyawan.</small>
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-primary px-4 shadow-sm" onclick="document.getElementById('formMasterSettings').submit();">
                        <i class="mdi mdi-content-save-outline me-1"></i> Simpan Semua Pengaturan
                    </button>
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

    function autofillMyIp(ip) {
        var ipInput = document.getElementById('wifiIpInput');
        var nameInput = document.getElementById('wifiNameInput');
        if (ipInput) ipInput.value = ip;
        if (nameInput && !nameInput.value) nameInput.value = 'WiFi Kantor';
        ipInput?.focus();
    }
</script>
@endsection

@push('after-style')
<style>
    @media (min-width: 1200px) {
        #modalOfficeWifiSettings .modal-xl {
            max-width: 1280px !important;
        }
        .border-end-lg {
            border-right: 1px solid #e7e7e8 !important;
        }
    }
    @media (max-width: 1199.98px) {
        .border-end-lg {
            border-bottom: 1px solid #e7e7e8 !important;
            padding-bottom: 1.5rem !important;
            margin-bottom: 1.5rem !important;
        }
    }
    .font-10 { font-size: 10px !important; }
    .font-11 { font-size: 11px !important; }
    .font-12 { font-size: 12px !important; }
    .font-13 { font-size: 13px !important; }
    .font-14 { font-size: 14px !important; }
</style>
@endpush


