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
        <h4 class="fw-bold mb-0 text-heading">
            Presensi &amp; Waktu Kerja
        </h4>
    </div>

    <div class="d-flex align-items-center gap-2 flex-wrap">
        <button type="button" class="btn btn-outline-info shadow-xs" data-bs-toggle="modal" data-bs-target="#modalOfficeWifiSettings">
            <i class="mdi mdi-wifi-cog me-1"></i> Pengaturan WiFi Kantor
            @if (isset($isWifiRestrictionEnabled) && $isWifiRestrictionEnabled)
                <span class="badge bg-success ms-1">Aktif</span>
            @else
                <span class="badge bg-secondary ms-1">Nonaktif</span>
            @endif
        </button>
        <button type="button" class="btn btn-primary shadow-xs" data-bs-toggle="modal" data-bs-target="#modalManualAttendance">
            <i class="mdi mdi-plus me-1"></i> Input Presensi Manual
        </button>
        <a href="{{ route('employees.index') }}" class="btn btn-label-secondary shadow-xs">
            <i class="mdi mdi-arrow-left me-1"></i> Hub Karyawan
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
                <div class="text-muted small mt-1" style="font-size:0.75rem;">Status hadir normal</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-3">
                <span class="text-muted small fw-semibold d-block mb-1">Terlambat Masuk</span>
                <h4 class="fw-bold text-warning mb-0">{{ $stats['total_late'] }}</h4>
                <div class="text-muted small mt-1" style="font-size:0.75rem;">Lewat jam toleransi</div>
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
            <div class="col-12 col-sm-6 col-md-3">
                <label class="form-label small fw-semibold mb-1">Pilih Tanggal</label>
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light"><i class="mdi mdi-calendar"></i></span>
                    <input type="date" name="date" class="form-control" value="{{ $selectedDate }}">
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
            <div class="col-12 col-sm-6 col-md-3">
                <label class="form-label small fw-semibold mb-1">Cari Karyawan / NIK</label>
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Nama atau NIK..." value="{{ $search }}">
            </div>
            <div class="col-12 col-md-1 d-flex align-items-end pt-md-3">
                <button type="submit" class="btn btn-sm btn-primary w-100">
                    <i class="mdi mdi-filter-variant"></i>
                </button>
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
                    <th>Jam Masuk</th>
                    <th>Jam Pulang</th>
                    <th>Tipe Kerja</th>
                    <th>Keterlambatan / Lembur</th>
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
                        <td>
                            @if ($att->clock_in)
                                <span class="badge bg-label-dark font-monospace">
                                    <i class="mdi mdi-clock-in me-1"></i>{{ substr($att->clock_in, 0, 5) }}
                                </span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
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
                            <span class="badge bg-label-secondary small">{{ $att->work_type }}</span>
                        </td>
                        <td>
                            @if ($att->late_minutes > 0)
                                <span class="badge bg-label-warning me-1">Terlambat {{ $att->late_minutes }}m</span>
                            @endif
                            @if ($att->overtime_minutes > 0)
                                <span class="badge bg-label-info">Lembur {{ round($att->overtime_minutes / 60, 1) }}j</span>
                            @endif
                            @if ($att->late_minutes == 0 && $att->overtime_minutes == 0)
                                <span class="text-muted small">-</span>
                            @endif
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
                        <td colspan="9" class="text-center py-5 text-muted">
                            <i class="mdi mdi-calendar-blank-outline fs-1 d-block mb-2 text-secondary"></i>
                            Tidak ada data presensi pada tanggal <strong>{{ \Carbon\Carbon::parse($selectedDate)->translatedFormat('d F Y') }}</strong>.
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

{{-- Modal Pengaturan Jaringan WiFi Kantor (IP Whitelist) --}}
<div class="modal fade" id="modalOfficeWifiSettings" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom bg-light">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded p-2 bg-label-info text-info">
                        <i class="mdi mdi-wifi-cog fs-4"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0">Pengaturan Jaringan WiFi Kantor</h5>
                        <small class="text-muted">Batasi presensi online hanya dari jaringan internet / WiFi resmi kantor</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">

                {{-- 1. Master Toggle Restriction --}}
                <div class="card border mb-4 {{ isset($isWifiRestrictionEnabled) && $isWifiRestrictionEnabled ? 'bg-label-success border-success' : 'bg-light border-secondary' }}">
                    <div class="card-body p-3">
                        <form action="{{ route('hr.attendances.wifis.toggle-restriction') }}" method="POST" class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3">
                            @csrf
                            <div>
                                <h6 class="fw-bold mb-1 text-heading d-flex align-items-center">
                                    <i class="mdi mdi-shield-lock-outline me-1"></i>
                                    Status Pembatasan Jaringan WiFi:
                                    @if (isset($isWifiRestrictionEnabled) && $isWifiRestrictionEnabled)
                                        <span class="badge bg-success ms-2">AKTIF</span>
                                    @else
                                        <span class="badge bg-secondary ms-2">NONAKTIF</span>
                                    @endif
                                </h6>
                                <p class="small text-muted mb-0">
                                    @if (isset($isWifiRestrictionEnabled) && $isWifiRestrictionEnabled)
                                        Karyawan <strong>hanya dapat melakukan Clock In / Clock Out</strong> saat perangkat terhubung ke salah satu WiFi kantor yang terdaftar di bawah.
                                    @else
                                        Pembatasan dinonaktifkan. Karyawan dapat melakukan Clock In / Clock Out dari jaringan internet manapun.
                                    @endif
                                </p>
                            </div>
                            <div class="form-check form-switch form-switch-lg mb-0 align-self-start align-self-sm-center">
                                <input type="hidden" name="is_wifi_restriction_enabled" value="0">
                                <input class="form-check-input cursor-pointer" type="checkbox" name="is_wifi_restriction_enabled" value="1"
                                       id="toggleWifiMaster" onchange="this.form.submit()"
                                       style="width: 3.2rem; height: 1.8rem;"
                                       @checked(isset($isWifiRestrictionEnabled) && $isWifiRestrictionEnabled)>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- 2. IP Admin Saat Ini & Fast 1-Click Add --}}
                <div class="p-3 rounded border mb-4 bg-light d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3">
                    <div class="d-flex align-items-center gap-2">
                        <i class="mdi mdi-ip-network-outline fs-3 text-primary"></i>
                        <div>
                            <div class="small text-muted">IP Jaringan Anda Saat Ini:</div>
                            <span class="fw-bold font-monospace text-dark fs-6">{{ $currentClientIp }}</span>
                        </div>
                    </div>
                    <button type="button" class="btn btn-sm btn-label-primary" onclick="autofillMyIp('{{ $currentClientIp }}')">
                        <i class="mdi mdi-plus-circle-outline me-1"></i> Gunakan IP Ini
                    </button>
                </div>

                {{-- 3. Form Tambah WiFi Baru --}}
                <div class="card border mb-4">
                    <div class="card-header py-2 bg-light border-bottom">
                        <h6 class="mb-0 fw-bold small text-uppercase text-muted"><i class="mdi mdi-plus me-1"></i> Tambah Jaringan WiFi Kantor</h6>
                    </div>
                    <div class="card-body p-3">
                        <form action="{{ route('hr.attendances.wifis.store') }}" method="POST">
                            @csrf
                            <div class="row g-2">
                                <div class="col-12 col-md-4">
                                    <label class="form-label small fw-semibold">Nama WiFi / Lokasi <span class="text-danger">*</span></label>
                                    <input type="text" name="name" id="wifiNameInput" class="form-control form-control-sm" placeholder="Contoh: WiFi Kantor Pusat" required>
                                </div>
                                <div class="col-12 col-md-4">
                                    <label class="form-label small fw-semibold">IP Address (Public IP) <span class="text-danger">*</span></label>
                                    <input type="text" name="ip_address" id="wifiIpInput" class="form-control form-control-sm font-monospace" placeholder="Contoh: 180.252.xxx.xxx" required>
                                </div>
                                <div class="col-12 col-md-4">
                                    <label class="form-label small fw-semibold">Catatan / Keterangan</label>
                                    <div class="input-group input-group-sm">
                                        <input type="text" name="notes" class="form-control" placeholder="Opsional (ISP Biznet, dll)">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="mdi mdi-plus"></i> Tambah
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- 4. Daftar Jaringan WiFi Terdaftar --}}
                <h6 class="fw-bold mb-2 small text-uppercase text-muted"><i class="mdi mdi-format-list-bulleted me-1"></i> Daftar Jaringan WiFi Terdaftar ({{ $officeWifis->count() }})</h6>
                <div class="table-responsive border rounded">
                    <table class="table table-sm table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>Nama Jaringan</th>
                                <th>IP Address</th>
                                <th>Status</th>
                                <th>Catatan</th>
                                <th class="text-center" style="width: 100px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($officeWifis as $wifi)
                                <tr>
                                    <td class="fw-semibold text-heading">{{ $wifi->name }}</td>
                                    <td>
                                        <span class="badge bg-light text-dark font-monospace border">{{ $wifi->ip_address }}</span>
                                    </td>
                                    <td>
                                        @if ($wifi->is_active)
                                            <span class="badge bg-label-success">Aktif</span>
                                        @else
                                            <span class="badge bg-label-secondary">Nonaktif</span>
                                        @endif
                                    </td>
                                    <td class="text-muted small">{{ $wifi->notes ?? '-' }}</td>
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
                                    <td colspan="5" class="text-center py-3 text-muted small">
                                        <i class="mdi mdi-wifi-off fs-4 d-block mb-1 opacity-50"></i>
                                        Belum ada IP WiFi kantor yang didaftarkan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
            <div class="modal-footer border-top bg-light">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
    function autofillMyIp(ip) {
        var ipInput = document.getElementById('wifiIpInput');
        var nameInput = document.getElementById('wifiNameInput');
        if (ipInput) ipInput.value = ip;
        if (nameInput && !nameInput.value) nameInput.value = 'WiFi Kantor';
        ipInput?.focus();
    }
</script>
@endsection
