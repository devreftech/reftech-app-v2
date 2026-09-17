@extends('layouts.sales.app')
@section('title', 'Manajemen Cuti & Izin - HRM')

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

{{-- Stats Row --}}
<div class="row g-3 mb-4">
    <div class="col-12 col-md-4">
        <div class="card border-0 shadow-sm">
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
        <div class="card border-0 shadow-sm">
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
        <div class="card border-0 shadow-sm">
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
                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    <option value="Pending" @selected($status === 'Pending')>Pending</option>
                    <option value="Approved" @selected($status === 'Approved')>Approved</option>
                    <option value="Rejected" @selected($status === 'Rejected')>Rejected</option>
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
