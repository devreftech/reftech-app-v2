@extends('layouts.sales.app')
@section('title', 'Klaim Reimbursement Operasional - HRM')

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
                <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">Reimbursement</li>
            </ol>
        </nav>
        <h4 class="fw-bold mb-0 text-heading">
            Klaim Biaya Operasional (Reimbursement)
        </h4>
    </div>

    <div class="d-flex align-items-center gap-2 flex-wrap">
        <button type="button" class="btn btn-primary shadow-xs" data-bs-toggle="modal" data-bs-target="#modalNewClaim">
            <i class="mdi mdi-plus me-1"></i> Ajukan Klaim Biaya
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

{{-- Stats Row --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-3">
                <span class="text-muted small fw-semibold d-block mb-1">Menunggu Persetujuan</span>
                <h3 class="fw-bold text-warning mb-0">{{ $stats['pending'] }}</h3>
                <span class="small text-muted">Klaim baru diajukan</span>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-3">
                <span class="text-muted small fw-semibold d-block mb-1">Disetujui (Approved)</span>
                <h3 class="fw-bold text-info mb-0">{{ $stats['approved_count'] }}</h3>
                <span class="small text-muted">Siap dicairkan finance</span>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-3">
                <span class="text-muted small fw-semibold d-block mb-1">Total Sudah Dicairkan</span>
                <h4 class="fw-bold text-success mb-0">Rp {{ number_format($stats['paid_amount'], 0, ',', '.') }}</h4>
                <span class="small text-muted">Status: Paid</span>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-3">
                <span class="text-muted small fw-semibold d-block mb-1">Total Riwayat Klaim</span>
                <h3 class="fw-bold text-primary mb-0">{{ $stats['total_claims'] }}</h3>
                <span class="small text-muted">Keseluruhan entri</span>
            </div>
        </div>
    </div>
</div>

{{-- Filter Card --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-3">
        <form action="{{ route('hr.reimbursements.index') }}" method="GET" class="row g-2 align-items-center">
            <div class="col-12 col-sm-6 col-md-4">
                <label class="form-label small fw-semibold mb-1">Status Klaim</label>
                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    <option value="Pending" @selected($status === 'Pending')>Pending</option>
                    <option value="Approved" @selected($status === 'Approved')>Approved</option>
                    <option value="Paid" @selected($status === 'Paid')>Paid (Sudah Dicairkan)</option>
                    <option value="Rejected" @selected($status === 'Rejected')>Rejected (Ditolak)</option>
                </select>
            </div>
            <div class="col-12 col-sm-6 col-md-4">
                <label class="form-label small fw-semibold mb-1">Kategori Biaya</label>
                <select name="claim_type" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">Semua Kategori</option>
                    <option value="BBM / Bensin" @selected($claimType === 'BBM / Bensin')>BBM / Bensin</option>
                    <option value="Tol / Parkir" @selected($claimType === 'Tol / Parkir')>Tol / Parkir</option>
                    <option value="Akomodasi / Hotel" @selected($claimType === 'Akomodasi / Hotel')>Akomodasi / Hotel</option>
                    <option value="Konsumsi Lapangan" @selected($claimType === 'Konsumsi Lapangan')>Konsumsi Lapangan</option>
                    <option value="Medis / Pengobatan" @selected($claimType === 'Medis / Pengobatan')>Medis / Pengobatan</option>
                    <option value="Lainnya" @selected($claimType === 'Lainnya')>Lainnya</option>
                </select>
            </div>
            <div class="col-12 col-md-4 d-flex align-items-end pt-md-3">
                <a href="{{ route('hr.reimbursements.index') }}" class="btn btn-sm btn-outline-secondary w-100">
                    <i class="mdi mdi-refresh me-1"></i> Reset Filter
                </a>
            </div>
        </form>
    </div>
</div>

{{-- Reimbursement Table --}}
<div class="card border-0 shadow-sm">
    <div class="table-responsive text-nowrap">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>No. Klaim</th>
                    <th>Karyawan</th>
                    <th>Kategori Biaya</th>
                    <th>Nominal Pengajuan</th>
                    <th>Tanggal Transaksi</th>
                    <th>Deskripsi &amp; Bukti</th>
                    <th>Status</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($reimbursements as $claim)
                    @php
                        $empName = $claim->employee->user?->name ?? ($claim->employee->nik ? 'Karyawan ' . $claim->employee->nik : 'Karyawan #' . $claim->employee->id);
                        $badge = match ($claim->status) {
                            'Paid' => 'success',
                            'Approved' => 'info',
                            'Rejected' => 'danger',
                            default => 'warning',
                        };
                    @endphp
                    <tr>
                        <td class="font-monospace fw-bold text-primary">{{ $claim->claim_number }}</td>
                        <td>
                            <div class="fw-semibold text-heading">{{ $empName }}</div>
                            <span class="text-muted small">{{ $claim->employee->department?->name ?? '-' }}</span>
                        </td>
                        <td>
                            <span class="badge bg-label-secondary">{{ $claim->claim_type }}</span>
                        </td>
                        <td class="font-monospace fw-bold text-heading">
                            Rp {{ number_format($claim->amount, 0, ',', '.') }}
                        </td>
                        <td>
                            {{ \Carbon\Carbon::parse($claim->event_date)->translatedFormat('d M Y') }}
                        </td>
                        <td>
                            <div class="small text-truncate" style="max-width: 180px;" title="{{ $claim->description }}">
                                {{ $claim->description }}
                            </div>
                            @if ($claim->receipt_image)
                                <a href="{{ asset($claim->receipt_image) }}" target="_blank" class="small text-primary mt-1 d-inline-block">
                                    <i class="mdi mdi-receipt me-1"></i>Lihat Struk/Nota
                                </a>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-label-{{ $badge }}">{{ $claim->status }}</span>
                            @if ($claim->rejection_reason)
                                <div class="small text-danger mt-1 text-truncate" style="max-width: 140px;" title="{{ $claim->rejection_reason }}">
                                    Ket: {{ $claim->rejection_reason }}
                                </div>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-1">
                                @if ($claim->status === 'Pending')
                                    <form action="{{ route('hr.reimbursements.approve', $claim->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Setujui klaim ini?');">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-icon btn-label-success" title="Approve">
                                            <i class="mdi mdi-check"></i>
                                        </button>
                                    </form>
                                    <button type="button" class="btn btn-sm btn-icon btn-label-danger" data-bs-toggle="modal" data-bs-target="#modalRejectClaim-{{ $claim->id }}" title="Tolak">
                                        <i class="mdi mdi-close"></i>
                                    </button>
                                @elseif ($claim->status === 'Approved')
                                    <form action="{{ route('hr.reimbursements.paid', $claim->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Tandai klaim ini telah ditransfer/dicairkan?');">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success">
                                            <i class="mdi mdi-cash me-1"></i> Cairkan (Paid)
                                        </button>
                                    </form>
                                @else
                                    <span class="text-muted small">Selesai</span>
                                @endif
                            </div>

                            {{-- Modal Reject Klaim --}}
                            @if ($claim->status === 'Pending')
                                <div class="modal fade" id="modalRejectClaim-{{ $claim->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <form action="{{ route('hr.reimbursements.reject', $claim->id) }}" method="POST" class="modal-content border-0 shadow text-start">
                                            @csrf
                                            <div class="modal-header border-bottom bg-light">
                                                <h6 class="modal-title fw-bold text-danger">Tolak Klaim: {{ $claim->claim_number }}</h6>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body p-4">
                                                <p class="small text-muted mb-2">Alasan penolakan reimbursement:</p>
                                                <textarea name="rejection_reason" class="form-control" rows="3" required placeholder="Contoh: Lampiran struk tidak jelas atau tidak sesuai SOP perjalanan dinas..."></textarea>
                                            </div>
                                            <div class="modal-footer border-top bg-light">
                                                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-danger">Tolak Klaim</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="mdi mdi-receipt-text-outline fs-1 d-block mb-2 text-secondary"></i>
                            Belum ada entri klaim reimbursement biaya.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($reimbursements->hasPages())
        <div class="card-footer border-top py-3 d-flex justify-content-end">
            {{ $reimbursements->links() }}
        </div>
    @endif
</div>

{{-- Modal Ajukan Klaim Baru --}}
<div class="modal fade" id="modalNewClaim" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('hr.reimbursements.store') }}" method="POST" enctype="multipart/form-data" class="modal-content border-0 shadow">
            @csrf
            <div class="modal-header border-bottom bg-light">
                <h5 class="modal-title fw-bold">Ajukan Klaim Reimbursement</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label fw-semibold">Pilih Karyawan</label>
                        <select name="employee_id" class="form-select" required>
                            <option value="">- Pilih Karyawan -</option>
                            @foreach ($employees as $emp)
                                <option value="{{ $emp->id }}">{{ $emp->user?->name ?? $emp->nik }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-semibold">Kategori Klaim</label>
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
                        <label class="form-label fw-semibold">Tanggal Transaksi / Nota</label>
                        <input type="date" name="event_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Nominal Klaim (IDR)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">Rp</span>
                            <input type="number" name="amount" class="form-control" placeholder="100000" min="1000" required>
                        </div>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Keterangan / Keperluan Operasional</label>
                        <textarea name="description" class="form-control" rows="2" required placeholder="Contoh: Pengisian BBM mobil operasional saat kunjungan servis PT ABC..."></textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Foto Struk / Nota Pembayaran</label>
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
@endsection
