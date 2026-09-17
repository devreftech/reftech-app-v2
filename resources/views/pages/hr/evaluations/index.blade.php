@extends('layouts.sales.app')
@section('title', 'Evaluasi Kinerja & Probation - HRM')

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
                <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">Evaluasi Kinerja</li>
            </ol>
        </nav>
        <h4 class="fw-bold mb-0 text-heading">
            Evaluasi Kinerja &amp; Masa Percobaan (Probation)
        </h4>
    </div>

    <div class="d-flex align-items-center gap-2 flex-wrap">
        <button type="button" class="btn btn-primary shadow-xs" data-bs-toggle="modal" data-bs-target="#modalNewEvaluation">
            <i class="mdi mdi-plus me-1"></i> Buat Evaluasi Kinerja
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
    <div class="col-12 col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold d-block mb-1">Evaluasi Probation 3 Bulan</span>
                    <h3 class="fw-bold text-warning mb-0">{{ $stats['probation'] }} Karyawan</h3>
                    <span class="small text-muted">Penilaian masa percobaan</span>
                </div>
                <div class="avatar avatar-md rounded bg-label-warning d-flex align-items-center justify-content-center">
                    <i class="mdi mdi-account-clock fs-3"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold d-block mb-1">Review Tahunan (Annual)</span>
                    <h3 class="fw-bold text-info mb-0">{{ $stats['annual'] }} Karyawan</h3>
                    <span class="small text-muted">Penilaian performa tahunan</span>
                </div>
                <div class="avatar avatar-md rounded bg-label-info d-flex align-items-center justify-content-center">
                    <i class="mdi mdi-chart-line fs-3"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold d-block mb-1">Rekomendasi Lolos Tetap</span>
                    <h3 class="fw-bold text-success mb-0">{{ $stats['passed_permanent'] }} Karyawan</h3>
                    <span class="small text-muted">Diangkat menjadi PKWTT</span>
                </div>
                <div class="avatar avatar-md rounded bg-label-success d-flex align-items-center justify-content-center">
                    <i class="mdi mdi-shield-check fs-3"></i>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Evaluations Table --}}
<div class="card border-0 shadow-sm">
    <div class="card-header border-bottom py-3 bg-light d-flex justify-content-between align-items-center">
        <h6 class="card-title mb-0 fw-bold">Daftar Rekam Evaluasi Kinerja Karyawan</h6>
        <div class="d-flex align-items-center gap-2">
            <form action="{{ route('hr.evaluations.index') }}" method="GET">
                <select name="evaluation_type" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">Semua Tipe Evaluasi</option>
                    <option value="Probation 3 Bulan" @selected($type === 'Probation 3 Bulan')>Probation 3 Bulan</option>
                    <option value="Tahunan (Annual)" @selected($type === 'Tahunan (Annual)')>Tahunan (Annual)</option>
                    <option value="Kenaikan Jabatan" @selected($type === 'Kenaikan Jabatan')>Kenaikan Jabatan</option>
                    <option value="Evaluasi Khusus" @selected($type === 'Evaluasi Khusus')>Evaluasi Khusus</option>
                </select>
            </form>
        </div>
    </div>
    <div class="table-responsive text-nowrap">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Karyawan</th>
                    <th>Tipe Evaluasi</th>
                    <th>Skor Nilai</th>
                    <th>Rekomendasi Keputusan</th>
                    <th>Kekuatan &amp; Area Peningkatan</th>
                    <th>Penilai (Evaluator)</th>
                    <th>Tanggal Review</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($evaluations as $ev)
                    @php
                        $empName = $ev->employee->user?->name ?? ($ev->employee->nik ? 'Karyawan ' . $ev->employee->nik : 'Karyawan #' . $ev->employee->id);
                        $scoreBadge = $ev->score >= 85 ? 'success' : ($ev->score >= 70 ? 'info' : 'warning');
                        $recBadge = match ($ev->recommendation) {
                            'Lolos Pegawai Tetap' => 'success',
                            'Perpanjang Kontrak' => 'info',
                            'Peringatan / Evaluasi Ulang' => 'warning',
                            default => 'danger',
                        };
                    @endphp
                    <tr>
                        <td>
                            <div class="fw-bold text-heading">{{ $empName }}</div>
                            <span class="text-muted small">{{ $ev->employee->department?->name ?? '-' }} &bull; {{ $ev->employee->position?->name ?? '-' }}</span>
                        </td>
                        <td>
                            <span class="badge bg-label-primary">{{ $ev->evaluation_type }}</span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-label-{{ $scoreBadge }} font-monospace fs-7">{{ $ev->score }} / 100</span>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-label-{{ $recBadge }}">{{ $ev->recommendation }}</span>
                        </td>
                        <td>
                            <div class="small text-truncate" style="max-width: 220px;" title="Kekuatan: {{ $ev->strengths }} | Perlu Ditingkatkan: {{ $ev->improvements }}">
                                <span class="text-success fw-semibold">Poin Positif:</span> {{ $ev->strengths ?: '-' }}
                            </div>
                        </td>
                        <td>
                            <span class="small text-muted">{{ $ev->evaluator?->name ?? 'HR Team' }}</span>
                        </td>
                        <td>
                            <span class="small">{{ \Carbon\Carbon::parse($ev->evaluation_date)->translatedFormat('d M Y') }}</span>
                        </td>
                        <td class="text-end">
                            <form action="{{ route('hr.evaluations.destroy', $ev->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus rekam evaluasi ini?');">
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
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="mdi mdi-star-half fs-1 d-block mb-2 text-secondary"></i>
                            Belum ada catatan evaluasi kinerja karyawan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($evaluations->hasPages())
        <div class="card-footer border-top py-3 d-flex justify-content-end">
            {{ $evaluations->links() }}
        </div>
    @endif
</div>

{{-- Modal Buat Evaluasi Baru --}}
<div class="modal fade" id="modalNewEvaluation" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <form action="{{ route('hr.evaluations.store') }}" method="POST" class="modal-content border-0 shadow">
            @csrf
            <div class="modal-header border-bottom bg-light">
                <h5 class="modal-title fw-bold">Formulir Evaluasi Kinerja Karyawan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-3">
                    <div class="col-12 col-sm-6">
                        <label class="form-label fw-semibold">Pilih Karyawan</label>
                        <select name="employee_id" class="form-select" required>
                            <option value="">- Pilih Karyawan -</option>
                            @foreach ($employees as $emp)
                                <option value="{{ $emp->id }}">
                                    {{ $emp->user?->name ?? $emp->nik }} ({{ $emp->department?->name ?? 'HR' }} &bull; {{ $emp->employment_status }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label class="form-label fw-semibold">Tipe Evaluasi</label>
                        <select name="evaluation_type" class="form-select" required>
                            <option value="Probation 3 Bulan">Probation 3 Bulan</option>
                            <option value="Tahunan (Annual)">Tahunan (Annual Review)</option>
                            <option value="Kenaikan Jabatan">Promosi / Kenaikan Jabatan</option>
                            <option value="Evaluasi Khusus">Evaluasi Khusus</option>
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-semibold">Tanggal Evaluasi</label>
                        <input type="date" name="evaluation_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-semibold">Skor Performa Akhir (0 - 100)</label>
                        <input type="number" name="score" class="form-control" value="85" min="0" max="100" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Rekomendasi Keputusan HR &amp; Manajemen</label>
                        <select name="recommendation" class="form-select" required>
                            <option value="Lolos Pegawai Tetap">Lolos Menjadi Pegawai Tetap (PKWTT)</option>
                            <option value="Perpanjang Kontrak">Perpanjang Kontrak PKWT</option>
                            <option value="Peringatan / Evaluasi Ulang">Peringatan / Evaluasi Ulang 1 Bulan</option>
                            <option value="Tidak Dilanjutkan / PHK">Tidak Dilanjutkan Masa Kerja</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Kekuatan &amp; Nilai Positif (Strengths)</label>
                        <textarea name="strengths" class="form-control" rows="2" placeholder="Kemampuan teknis, kedisiplinan, inisiatif kerja..."></textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Area Yang Perlu Ditingkatkan (Improvements)</label>
                        <textarea name="improvements" class="form-control" rows="2" placeholder="Komunikasi tim, ketelitian administrasi, manajemen waktu..."></textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Catatan Tambahan Manajerial</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="Rencana pengembangan atau target pelatihan selanjutnya..."></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top bg-light">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Hasil Penilaian</button>
            </div>
        </form>
    </div>
</div>
@endsection
