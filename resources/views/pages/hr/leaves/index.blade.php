@extends('layouts.sales.app')
@section('title', 'Manajemen Cuti & Izin - HRM')

@push('after-style')
<style>
    .leave-kpi-card {
        border-radius: 12px;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .leave-kpi-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(0,0,0,0.06);
    }
    .nav-tabs-leaves .nav-link {
        font-weight: 600;
        color: #64748b;
        border: none;
        border-bottom: 3px solid transparent;
        padding: 0.85rem 1.4rem;
        background: transparent;
        transition: all 0.2s ease;
        border-radius: 0;
        font-size: 0.95rem;
    }
    .nav-tabs-leaves .nav-link:hover {
        color: #4f46e5;
        background: rgba(79, 70, 229, 0.04);
    }
    .nav-tabs-leaves .nav-link.active {
        color: #4f46e5;
        border-bottom-color: #4f46e5;
        background: transparent;
        font-weight: 700;
    }
</style>
@endpush

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
                <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">Cuti &amp; Perizinan</li>
            </ol>
        </nav>
        <h4 class="fw-bold mb-0 text-heading">
            Manajemen Cuti &amp; Izin Karyawan
        </h4>
    </div>

    <div class="d-flex align-items-center gap-2 flex-wrap">
        <button type="button" class="btn btn-primary shadow-xs" data-bs-toggle="modal" data-bs-target="#modalNewLeaveRequest">
            <i class="mdi mdi-plus me-1"></i> Ajukan Cuti / Izin
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
@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show shadow-xs border-0 mb-4" role="alert">
        <i class="mdi mdi-alert-circle me-1"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

{{-- Tabs Navigasi: Permohonan Cuti vs Pengaturan Kuota Cuti --}}
<ul class="nav nav-tabs nav-tabs-leaves mb-4 border-bottom" id="leaveManagementTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link {{ $activeTab === 'requests' ? 'active' : '' }}" id="tab-requests-btn" data-bs-toggle="tab" data-bs-target="#tab-requests" type="button" role="tab" aria-controls="tab-requests" aria-selected="{{ $activeTab === 'requests' ? 'true' : 'false' }}">
            <i class="mdi mdi-calendar-clock-outline me-1"></i> Permohonan Cuti &amp; Izin
            @if ($stats['pending'] > 0)
                <span class="badge bg-warning rounded-pill ms-1">{{ $stats['pending'] }}</span>
            @endif
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link {{ $activeTab === 'settings' ? 'active' : '' }}" id="tab-settings-btn" data-bs-toggle="tab" data-bs-target="#tab-settings" type="button" role="tab" aria-controls="tab-settings" aria-selected="{{ $activeTab === 'settings' ? 'true' : 'false' }}">
            <i class="mdi mdi-cog-outline me-1"></i> Pengaturan Kuota Cuti Karyawan
        </button>
    </li>
</ul>

<div class="tab-content p-0" id="leaveManagementTabContent">
    {{-- ──────────────────────────────────────────────────────────────────────── --}}
    {{-- ── TAB 1: PERMOHONAN CUTI & IZIN ─────────────────────────────────────── --}}
    {{-- ──────────────────────────────────────────────────────────────────────── --}}
    <div class="tab-pane fade {{ $activeTab === 'requests' ? 'show active' : '' }}" id="tab-requests" role="tabpanel" aria-labelledby="tab-requests-btn">
        {{-- Stats Row --}}
        <div class="row g-3 mb-4">
            <div class="col-12 col-md-4">
                <div class="card border-0 shadow-sm leave-kpi-card">
                    <div class="card-body p-3 d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small fw-semibold d-block mb-1">Menunggu Persetujuan</span>
                            <h3 class="fw-bold text-warning mb-0">{{ $stats['pending'] }}</h3>
                            <span class="small text-muted">Perlu tindakan HR / Atasan</span>
                        </div>
                        <div class="avatar avatar-md rounded bg-label-warning d-flex align-items-center justify-content-center">
                            <i class="mdi mdi-clock-outline fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="card border-0 shadow-sm leave-kpi-card">
                    <div class="card-body p-3 d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small fw-semibold d-block mb-1">Disetujui Tahun {{ $year }}</span>
                            <h3 class="fw-bold text-success mb-0">{{ $stats['approved'] }}</h3>
                            <span class="small text-muted">Kuota cuti telah terpotong</span>
                        </div>
                        <div class="avatar avatar-md rounded bg-label-success d-flex align-items-center justify-content-center">
                            <i class="mdi mdi-check-circle-outline fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="card border-0 shadow-sm leave-kpi-card">
                    <div class="card-body p-3 d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small fw-semibold d-block mb-1">Pengajuan Ditolak</span>
                            <h3 class="fw-bold text-danger mb-0">{{ $stats['rejected'] }}</h3>
                            <span class="small text-muted">Tidak disetujui</span>
                        </div>
                        <div class="avatar avatar-md rounded bg-label-danger d-flex align-items-center justify-content-center">
                            <i class="mdi mdi-close-circle-outline fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Leave Requests List Card --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header border-bottom py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h6 class="card-title mb-0 fw-bold d-flex align-items-center">
                    <i class="mdi mdi-format-list-bulleted me-2 text-primary"></i> Daftar Permohonan Cuti &amp; Izin
                </h6>
                <div class="d-flex align-items-center gap-2">
                    <form action="{{ route('hr.leaves.index') }}" method="GET" class="d-flex align-items-center gap-2">
                        <input type="hidden" name="tab" value="requests">
                        <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="">Semua Status</option>
                            <option value="Pending" @selected($status === 'Pending')>Pending</option>
                            <option value="Approved" @selected($status === 'Approved')>Approved</option>
                            <option value="Rejected" @selected($status === 'Rejected')>Rejected</option>
                        </select>
                        <select name="leave_type_id" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="">Semua Jenis Cuti</option>
                            @foreach ($leaveTypes as $lt)
                                <option value="{{ $lt->id }}" @selected($typeId == $lt->id)>{{ $lt->name }}</option>
                            @endforeach
                        </select>
                        <select name="year" class="form-select form-select-sm" onchange="this.form.submit()">
                            @foreach ($availableYears as $yr)
                                <option value="{{ $yr }}" @selected($year == $yr)>Tahun {{ $yr }}</option>
                            @endforeach
                        </select>
                    </form>
                </div>
            </div>

            <div class="table-responsive text-nowrap">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Karyawan</th>
                            <th>Jenis Cuti</th>
                            <th>Tanggal Pelaksanaan</th>
                            <th>Durasi</th>
                            <th>Alasan &amp; Bukti</th>
                            <th>Status</th>
                            <th>Disetujui Oleh</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($leaveRequests as $req)
                            @php
                                $empName = $req->employee->user?->name ?? ($req->employee->nik ? 'Karyawan ' . $req->employee->nik : 'Karyawan #' . $req->employee->id);
                                $badge = match ($req->status) {
                                    'Approved' => 'success',
                                    'Rejected' => 'danger',
                                    default => 'warning',
                                };
                            @endphp
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="avatar avatar-sm rounded-circle bg-label-primary d-flex align-items-center justify-content-center fw-bold">
                                            {{ strtoupper(substr($empName, 0, 2)) }}
                                        </div>
                                        <div>
                                            <div class="fw-semibold text-heading">{{ $empName }}</div>
                                            <span class="text-muted small">{{ $req->employee->department?->name ?? '-' }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-label-info">{{ $req->leaveType->name }}</span>
                                </td>
                                <td>
                                    <div class="small fw-semibold text-heading">
                                        {{ \Carbon\Carbon::parse($req->start_date)->translatedFormat('d M Y') }}
                                        @if ($req->start_date != $req->end_date)
                                            s/d {{ \Carbon\Carbon::parse($req->end_date)->translatedFormat('d M Y') }}
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <span class="fw-bold text-heading">{{ $req->total_days }} Hari</span>
                                </td>
                                <td>
                                    <div class="small text-truncate" style="max-width: 200px;" title="{{ $req->reason }}">
                                        {{ $req->reason }}
                                    </div>
                                    @if ($req->attachment)
                                        <a href="{{ asset($req->attachment) }}" target="_blank" class="small text-primary mt-1 d-inline-block">
                                            <i class="mdi mdi-paperclip me-1"></i>Lampiran Bukti
                                        </a>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-label-{{ $badge }}">{{ $req->status }}</span>
                                    @if ($req->rejection_note)
                                        <div class="small text-danger mt-1 text-truncate" style="max-width: 140px;" title="{{ $req->rejection_note }}">
                                            Ket: {{ $req->rejection_note }}
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    @if ($req->approver)
                                        <span class="small text-muted">{{ $req->approver->name }}</span>
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    @if ($req->status === 'Pending')
                                        <div class="d-inline-flex gap-1">
                                            <form action="{{ route('hr.leaves.approve', $req->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Setujui permohonan cuti ini?');">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-icon btn-label-success" title="Setujui (Approve)">
                                                    <i class="mdi mdi-check"></i>
                                                </button>
                                            </form>
                                            <button type="button" class="btn btn-sm btn-icon btn-label-danger" title="Tolak (Reject)"
                                                    data-bs-toggle="modal" data-bs-target="#modalRejectLeave-{{ $req->id }}">
                                                <i class="mdi mdi-close"></i>
                                            </button>
                                        </div>

                                        {{-- Modal Reject --}}
                                        <div class="modal fade" id="modalRejectLeave-{{ $req->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <form action="{{ route('hr.leaves.reject', $req->id) }}" method="POST" class="modal-content border-0 shadow">
                                                    @csrf
                                                    <div class="modal-header border-bottom bg-light">
                                                        <h6 class="modal-title fw-bold text-danger">Tolak Pengajuan Cuti</h6>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body p-4 text-start">
                                                        <p class="small text-muted mb-2">
                                                            Berikan alasan penolakan permohonan cuti <strong>{{ $empName }}</strong>:
                                                        </p>
                                                        <textarea name="rejection_note" class="form-control" rows="3" required placeholder="Contoh: Jadwal bertabrakan dengan maintenance mesin penting..."></textarea>
                                                    </div>
                                                    <div class="modal-footer border-top bg-light">
                                                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                                                        <button type="submit" class="btn btn-danger">Tolak Permohonan</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-muted small">Selesai</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <i class="mdi mdi-calendar-blank-outline fs-1 d-block mb-2 text-secondary"></i>
                                    Belum ada pengajuan cuti atau perizinan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($leaveRequests->hasPages())
                <div class="card-footer border-top py-3 d-flex justify-content-end">
                    {{ $leaveRequests->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- ──────────────────────────────────────────────────────────────────────── --}}
    {{-- ── TAB 2: PENGATURAN KUOTA CUTI KARYAWAN ─────────────────────────────── --}}
    {{-- ──────────────────────────────────────────────────────────────────────── --}}
    <div class="tab-pane fade {{ $activeTab === 'settings' ? 'show active' : '' }}" id="tab-settings" role="tabpanel" aria-labelledby="tab-settings-btn">
        {{-- Toolbar / Filter Tahun --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-3 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div class="d-flex align-items-center gap-2">
                    <div class="avatar avatar-sm rounded bg-label-primary d-flex align-items-center justify-content-center">
                        <i class="mdi mdi-calendar-star fs-4 text-primary"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 fw-bold">Konfigurasi Kuota Cuti Karyawan</h6>
                        <small class="text-muted">Kelola alokasi cuti tahunan, kuota khusus individu, dan status tampil di portal karyawan.</small>
                    </div>
                </div>
                <form action="{{ route('hr.leaves.index') }}" method="GET" class="d-flex align-items-center gap-2">
                    <input type="hidden" name="tab" value="settings">
                    <label class="form-label mb-0 small fw-semibold text-nowrap">Pilih Tahun:</label>
                    <select name="year" class="form-select form-select-sm" onchange="this.form.submit()" style="min-width: 120px;">
                        @foreach ($availableYears as $yr)
                            <option value="{{ $yr }}" @selected($year == $yr)>Tahun {{ $yr }}</option>
                        @endforeach
                    </select>
                </form>
            </div>
        </div>

        {{-- Form Actions Row --}}
        <div class="row g-3 mb-4">
            {{-- Form 1: Penetapan Kuota Masal (Semua Karyawan) --}}
            <div class="col-12 col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header border-bottom py-3 bg-light">
                        <h6 class="card-title mb-0 fw-bold text-primary d-flex align-items-center">
                            <i class="mdi mdi-account-multiple-check-outline me-2"></i> 1. Tetapkan Kuota Masal (Semua Karyawan)
                        </h6>
                    </div>
                    <div class="card-body p-3 p-md-4">
                        <form action="{{ route('hr.leaves.balances.bulk') }}" method="POST" onsubmit="return confirm('Terapkan kuota ini ke seluruh karyawan aktif untuk tahun {{ $year }}?');">
                            @csrf
                            <input type="hidden" name="year" value="{{ $year }}">
                            <div class="mb-3">
                                <label class="form-label fw-semibold small">Jumlah Kuota Cuti Tahunan (Hari)</label>
                                <div class="input-group">
                                    <input type="number" name="total_quota" class="form-control" value="12" min="0" max="365" required>
                                    <span class="input-group-text">Hari / Tahun</span>
                                </div>
                                <div class="form-text small">Kuota ini akan diterapkan ke seluruh {{ $employees->count() }} karyawan aktif pada Tahun {{ $year }}.</div>
                            </div>
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="is_active" value="1" id="bulkActiveSwitch" checked>
                                    <label class="form-check-label fw-semibold small" for="bulkActiveSwitch">
                                        Aktifkan Kuota Cuti (Muncul di My Portal Karyawan)
                                    </label>
                                </div>
                                <small class="text-muted d-block mt-1">
                                    Jika dinonaktifkan, info sisa kuota cuti tidak akan ditampilkan pada halaman My Portal karyawan.
                                </small>
                            </div>
                            <button type="submit" class="btn btn-primary w-100 shadow-xs">
                                <i class="mdi mdi-check-all me-1"></i> Terapkan ke Semua Karyawan (Tahun {{ $year }})
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Form 2: Penetapan Kuota Spesifik (Individu Karyawan) --}}
            <div class="col-12 col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header border-bottom py-3 bg-light">
                        <h6 class="card-title mb-0 fw-bold text-success d-flex align-items-center">
                            <i class="mdi mdi-account-cog-outline me-2"></i> 2. Tetapkan Kuota Spesifik per Karyawan
                        </h6>
                    </div>
                    <div class="card-body p-3 p-md-4">
                        <form action="{{ route('hr.leaves.balances.individual') }}" method="POST">
                            @csrf
                            <input type="hidden" name="year" value="{{ $year }}">
                            <div class="mb-3">
                                <label class="form-label fw-semibold small">Pilih Karyawan</label>
                                <select name="employee_id" class="form-select" required>
                                    <option value="">- Pilih Karyawan Spesifik -</option>
                                    @foreach ($employees as $emp)
                                        @php
                                            $currBal = $leaveBalances->get($emp->id);
                                            $currQuotaText = $currBal ? "({$currBal->total_quota} Hari, " . ($currBal->is_active ? 'Aktif' : 'Non-Aktif') . ")" : "(Belum diset)";
                                        @endphp
                                        <option value="{{ $emp->id }}">
                                            {{ $emp->user?->name ?? $emp->nik }} - {{ $emp->department?->name ?? 'HR' }} {{ $currQuotaText }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold small">Jumlah Kuota Khusus (Hari)</label>
                                <div class="input-group">
                                    <input type="number" name="total_quota" class="form-control" value="12" min="0" max="365" required>
                                    <span class="input-group-text">Hari</span>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="is_active" value="1" id="individualActiveSwitch" checked>
                                    <label class="form-check-label fw-semibold small" for="individualActiveSwitch">
                                        Status Kuota Aktif (Tampil di Portal Karyawan Terpilih)
                                    </label>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-success w-100 shadow-xs">
                                <i class="mdi mdi-content-save-outline me-1"></i> Simpan Kuota Karyawan
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tabel Daftar Kuota Cuti Karyawan Tahun Terpilih --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header border-bottom py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h6 class="card-title mb-0 fw-bold d-flex align-items-center">
                        <i class="mdi mdi-table-account me-2 text-primary"></i> Daftar Kuota Cuti Karyawan (Tahun {{ $year }})
                    </h6>
                    <small class="text-muted">Total: {{ $employees->count() }} Karyawan Aktif</small>
                </div>
            </div>

            <div class="table-responsive text-nowrap">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 50px;">No</th>
                            <th>Karyawan</th>
                            <th>Departemen</th>
                            <th class="text-center">Total Kuota</th>
                            <th class="text-center">Terpakai</th>
                            <th class="text-center">Sisa Kuota</th>
                            <th class="text-center">Status Portal</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($employees as $idx => $emp)
                            @php
                                $bal = $leaveBalances->get($emp->id);
                                $totalQ = $bal ? $bal->total_quota : 12;
                                $usedQ = $bal ? $bal->used_quota : 0;
                                $remQ = $bal ? $bal->remaining_quota : ($totalQ - $usedQ);
                                $isActive = $bal ? (bool)$bal->is_active : true;
                                $empName = $emp->user?->name ?? ($emp->nik ? 'Karyawan ' . $emp->nik : 'Karyawan #' . $emp->id);
                            @endphp
                            <tr>
                                <td>{{ $idx + 1 }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="avatar avatar-sm rounded-circle bg-label-primary d-flex align-items-center justify-content-center fw-bold">
                                            {{ strtoupper(substr($empName, 0, 2)) }}
                                        </div>
                                        <div>
                                            <div class="fw-semibold text-heading">{{ $empName }}</div>
                                            <span class="text-muted small">NIK: {{ $emp->nik ?? '-' }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-label-secondary">{{ $emp->department?->name ?? '-' }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="fw-bold text-dark fs-6">{{ $totalQ }} Hari</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-label-warning px-2 py-1">{{ $usedQ }} Hari</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-label-{{ $remQ > 0 ? 'primary' : 'danger' }} px-2.5 py-1 fw-bold fs-6">
                                        {{ $remQ }} Hari
                                    </span>
                                </td>
                                <td class="text-center">
                                    @if ($isActive)
                                        <span class="badge bg-label-success d-inline-flex align-items-center gap-1">
                                            <i class="mdi mdi-check-circle-outline"></i> Aktif (Muncul di Portal)
                                        </span>
                                    @else
                                        <span class="badge bg-label-secondary d-inline-flex align-items-center gap-1">
                                            <i class="mdi mdi-eye-off-outline"></i> Non-Aktif (Disembunyikan)
                                        </span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="d-inline-flex align-items-center gap-1">
                                        @if ($bal)
                                            <form action="{{ route('hr.leaves.balances.toggle', $bal->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-icon {{ $isActive ? 'btn-label-secondary' : 'btn-label-success' }}"
                                                        title="{{ $isActive ? 'Nonaktifkan dari Portal' : 'Aktifkan ke Portal' }}">
                                                    <i class="mdi {{ $isActive ? 'mdi-eye-off' : 'mdi-eye' }}"></i>
                                                </button>
                                            </form>
                                        @endif
                                        <button type="button" class="btn btn-sm btn-icon btn-label-primary" title="Ubah Kuota"
                                                data-bs-toggle="modal" data-bs-target="#modalEditBalance-{{ $emp->id }}">
                                            <i class="mdi mdi-pencil"></i>
                                        </button>
                                    </div>

                                    {{-- Modal Edit Kuota Individual --}}
                                    <div class="modal fade" id="modalEditBalance-{{ $emp->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <form action="{{ route('hr.leaves.balances.individual') }}" method="POST" class="modal-content border-0 shadow">
                                                @csrf
                                                <input type="hidden" name="employee_id" value="{{ $emp->id }}">
                                                <input type="hidden" name="year" value="{{ $year }}">
                                                <div class="modal-header border-bottom bg-light">
                                                    <h6 class="modal-title fw-bold text-dark">
                                                        Ubah Kuota Cuti - {{ $empName }}
                                                    </h6>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body p-4 text-start">
                                                    <div class="mb-3">
                                                        <label class="form-label small fw-semibold">Tahun Cuti</label>
                                                        <input type="text" class="form-control" value="{{ $year }}" disabled>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label small fw-semibold">Total Kuota Cuti (Hari)</label>
                                                        <div class="input-group">
                                                            <input type="number" name="total_quota" class="form-control" value="{{ $totalQ }}" min="0" max="365" required>
                                                            <span class="input-group-text">Hari</span>
                                                        </div>
                                                        <div class="form-text small">Saat ini sudah terpakai: <strong>{{ $usedQ }} Hari</strong>.</div>
                                                    </div>
                                                    <div class="mb-2">
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="switchEditActive-{{ $emp->id }}" @checked($isActive)>
                                                            <label class="form-check-label fw-semibold small" for="switchEditActive-{{ $emp->id }}">
                                                                Status Aktif (Tampilkan Kuota di My Portal)
                                                            </label>
                                                        </div>
                                                        <small class="text-muted d-block mt-1">Jika dinonaktifkan, kuota tidak akan muncul di portal karyawan.</small>
                                                    </div>
                                                </div>
                                                <div class="modal-footer border-top bg-light">
                                                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">
                                    Tidak ada data karyawan aktif.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Modal Ajukan Cuti Baru --}}
<div class="modal fade" id="modalNewLeaveRequest" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('hr.leaves.store') }}" method="POST" enctype="multipart/form-data" class="modal-content border-0 shadow">
            @csrf
            <div class="modal-header border-bottom bg-light">
                <h5 class="modal-title fw-bold">Ajukan Permohonan Cuti / Izin</h5>
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
                    <div class="col-12">
                        <label class="form-label fw-semibold">Jenis Cuti / Izin</label>
                        <select name="leave_type_id" class="form-select" required>
                            @foreach ($leaveTypes as $lt)
                                <option value="{{ $lt->id }}">
                                    {{ $lt->name }} (Maks. {{ $lt->default_days }} Hari {{ $lt->is_paid ? 'Berbayar' : 'Unpaid' }})
                                </option>
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
                        <label class="form-label fw-semibold">Alasan Permohonan</label>
                        <textarea name="reason" class="form-control" rows="3" required placeholder="Jelaskan alasan pengajuan cuti atau izin..."></textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Lampiran Bukti (Opsional / Surat Dokter)</label>
                        <input type="file" name="attachment" class="form-control" accept=".jpg,.jpeg,.png,.pdf">
                        <div class="form-text small">Format JPG, PNG, atau PDF (maks. 4MB).</div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top bg-light">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Kirim Pengajuan Cuti</button>
            </div>
        </form>
    </div>
</div>
@endsection
