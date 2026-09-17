@extends('layouts.sales.app')
@section('title', 'Inventaris Alat Kerja Karyawan - HRM')

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
                <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">Alat Kerja Karyawan</li>
            </ol>
        </nav>
        <h4 class="fw-bold mb-0 text-heading">
            Inventaris &amp; Alat Kerja Karyawan
        </h4>
    </div>

    <div class="d-flex align-items-center gap-2 flex-wrap">
        <button type="button" class="btn btn-primary shadow-xs" data-bs-toggle="modal" data-bs-target="#modalNewAsset">
            <i class="mdi mdi-plus me-1"></i> Serah Terima Alat Kerja
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
                    <span class="text-muted small fw-semibold d-block mb-1">Sedang Digunakan (Aktif)</span>
                    <h3 class="fw-bold text-primary mb-0">{{ $stats['total_active'] }} Unit</h3>
                    <span class="small text-muted">Di tangan teknisi / staf</span>
                </div>
                <div class="avatar avatar-md rounded bg-label-primary d-flex align-items-center justify-content-center">
                    <i class="mdi mdi-laptop fs-3"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold d-block mb-1">Sudah Dikembalikan</span>
                    <h3 class="fw-bold text-success mb-0">{{ $stats['total_returned'] }} Unit</h3>
                    <span class="small text-muted">Tersimpan di gudang kantor</span>
                </div>
                <div class="avatar avatar-md rounded bg-label-success d-flex align-items-center justify-content-center">
                    <i class="mdi mdi-check-all fs-3"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold d-block mb-1">Rusak / Perlu Servis</span>
                    <h3 class="fw-bold text-warning mb-0">{{ $stats['total_damaged'] }} Unit</h3>
                    <span class="small text-muted">Perlu perbaikan maintenance</span>
                </div>
                <div class="avatar avatar-md rounded bg-label-warning d-flex align-items-center justify-content-center">
                    <i class="mdi mdi-wrench fs-3"></i>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Assets Table --}}
<div class="card border-0 shadow-sm">
    <div class="card-header border-bottom py-3 bg-light d-flex justify-content-between align-items-center">
        <h6 class="card-title mb-0 fw-bold">Daftar Aset &amp; Peminjaman Fasilitas Kerja</h6>
        <div class="d-flex align-items-center gap-2">
            <form action="{{ route('hr.assets.index') }}" method="GET">
                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    <option value="Digunakan" @selected($status === 'Digunakan')>Digunakan</option>
                    <option value="Dikembalikan" @selected($status === 'Dikembalikan')>Dikembalikan</option>
                    <option value="Hilang/Rusak" @selected($status === 'Hilang/Rusak')>Hilang / Rusak</option>
                </select>
            </form>
        </div>
    </div>
    <div class="table-responsive text-nowrap">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Nama Alat / Aset</th>
                    <th>Kode &amp; Serial Number</th>
                    <th>Karyawan Pemegang</th>
                    <th>Kondisi Fisik</th>
                    <th>Tgl Penyerahan</th>
                    <th>Tgl Pengembalian</th>
                    <th>Status</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($assets as $asset)
                    @php
                        $empName = $asset->employee->user?->name ?? ($asset->employee->nik ? 'Karyawan ' . $asset->employee->nik : 'Karyawan #' . $asset->employee->id);
                        $badge = match ($asset->status) {
                            'Dikembalikan' => 'success',
                            'Hilang/Rusak' => 'danger',
                            default => 'primary',
                        };
                    @endphp
                    <tr>
                        <td>
                            <div class="fw-bold text-heading">{{ $asset->asset_name }}</div>
                            @if ($asset->notes)
                                <span class="text-muted small text-truncate d-block" style="max-width: 180px;">{{ $asset->notes }}</span>
                            @endif
                        </td>
                        <td>
                            <div class="font-monospace small text-primary">{{ $asset->asset_code ?: '-' }}</div>
                            <div class="font-monospace small text-muted">S/N: {{ $asset->serial_number ?: '-' }}</div>
                        </td>
                        <td>
                            <div class="fw-semibold text-heading">{{ $empName }}</div>
                            <span class="text-muted small">{{ $asset->employee->department?->name ?? '-' }}</span>
                        </td>
                        <td>
                            <span class="badge bg-label-{{ $asset->condition === 'Baik' ? 'success' : ($asset->condition === 'Normal' ? 'info' : 'warning') }}">
                                {{ $asset->condition }}
                            </span>
                        </td>
                        <td>
                            {{ \Carbon\Carbon::parse($asset->handover_date)->format('d M Y') }}
                        </td>
                        <td>
                            {{ $asset->returned_date ? \Carbon\Carbon::parse($asset->returned_date)->format('d M Y') : '-' }}
                        </td>
                        <td>
                            <span class="badge bg-label-{{ $badge }}">{{ $asset->status }}</span>
                        </td>
                        <td class="text-end">
                            <button type="button" class="btn btn-sm btn-icon btn-label-primary" data-bs-toggle="modal" data-bs-target="#modalEditAsset-{{ $asset->id }}" title="Perbarui Status">
                                <i class="mdi mdi-pencil-outline"></i>
                            </button>
                            <form action="{{ route('hr.assets.destroy', $asset->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus data aset ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-icon btn-label-danger" title="Hapus">
                                    <i class="mdi mdi-trash-can-outline"></i>
                                </button>
                            </form>

                            {{-- Modal Edit Aset --}}
                            <div class="modal fade" id="modalEditAsset-{{ $asset->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <form action="{{ route('hr.assets.update', $asset->id) }}" method="POST" class="modal-content border-0 shadow text-start">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-header border-bottom bg-light">
                                            <h6 class="modal-title fw-bold">Update Status: {{ $asset->asset_name }}</h6>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body p-4">
                                            <div class="row g-3">
                                                <div class="col-6">
                                                    <label class="form-label fw-semibold">Status Aset</label>
                                                    <select name="status" class="form-select" required>
                                                        <option value="Digunakan" @selected($asset->status === 'Digunakan')>Digunakan (Aktif)</option>
                                                        <option value="Dikembalikan" @selected($asset->status === 'Dikembalikan')>Dikembalikan ke Kantor</option>
                                                        <option value="Hilang/Rusak" @selected($asset->status === 'Hilang/Rusak')>Hilang / Rusak Berat</option>
                                                    </select>
                                                </div>
                                                <div class="col-6">
                                                    <label class="form-label fw-semibold">Kondisi Fisik</label>
                                                    <select name="condition" class="form-select" required>
                                                        <option value="Baik" @selected($asset->condition === 'Baik')>Baik (Prima)</option>
                                                        <option value="Normal" @selected($asset->condition === 'Normal')>Normal</option>
                                                        <option value="Rusak Ringan" @selected($asset->condition === 'Rusak Ringan')>Rusak Ringan</option>
                                                        <option value="Perlu Servis" @selected($asset->condition === 'Perlu Servis')>Perlu Servis</option>
                                                    </select>
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label fw-semibold">Tanggal Dikembalikan (Opsional)</label>
                                                    <input type="date" name="returned_date" class="form-control" value="{{ $asset->returned_date ? \Carbon\Carbon::parse($asset->returned_date)->format('Y-m-d') : date('Y-m-d') }}">
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label fw-semibold">Catatan Kondisi</label>
                                                    <textarea name="notes" class="form-control" rows="2">{{ $asset->notes }}</textarea>
                                                </div>
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
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="mdi mdi-laptop-off fs-1 d-block mb-2 text-secondary"></i>
                            Belum ada riwayat serah terima alat kerja karyawan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($assets->hasPages())
        <div class="card-footer border-top py-3 d-flex justify-content-end">
            {{ $assets->links() }}
        </div>
    @endif
</div>

{{-- Modal Serah Terima Aset Baru --}}
<div class="modal fade" id="modalNewAsset" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('hr.assets.store') }}" method="POST" class="modal-content border-0 shadow">
            @csrf
            <div class="modal-header border-bottom bg-light">
                <h5 class="modal-title fw-bold">Serah Terima Alat Kerja / Fasilitas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label fw-semibold">Pilih Karyawan Penerima</label>
                        <select name="employee_id" class="form-select" required>
                            <option value="">- Pilih Karyawan -</option>
                            @foreach ($employees as $emp)
                                <option value="{{ $emp->id }}">{{ $emp->user?->name ?? $emp->nik }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Nama Alat / Aset</label>
                        <input type="text" name="asset_name" class="form-control" placeholder="Contoh: Laptop HP Pavilion 14 / Toolset Bosch 108 pcs" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-semibold">Kode Aset / Tag</label>
                        <input type="text" name="asset_code" class="form-control" placeholder="Contoh: AST-LP-012">
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-semibold">Serial Number (S/N)</label>
                        <input type="text" name="serial_number" class="form-control" placeholder="Contoh: 5CD23489XX">
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-semibold">Kondisi Awal</label>
                        <select name="condition" class="form-select" required>
                            <option value="Baik">Baik (Prima)</option>
                            <option value="Normal">Normal</option>
                            <option value="Rusak Ringan">Rusak Ringan</option>
                            <option value="Perlu Servis">Perlu Servis</option>
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-semibold">Tanggal Penyerahan</label>
                        <input type="date" name="handover_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Catatan / Kelengkapan Aksesoris</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="Contoh: Termasuk charger adapter, tas ransel, dan mouse wireless..."></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top bg-light">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Serah Terima</button>
            </div>
        </form>
    </div>
</div>
@endsection
