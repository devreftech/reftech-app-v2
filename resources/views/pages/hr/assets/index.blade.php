@extends('layouts.sales.app')
@section('title', 'Inventaris Alat Kerja Karyawan - HRM')

@push('after-style')
<link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/select2/select2.css" />
<style>
    .select2-container--default .select2-selection--single {
        height: 38px !important;
        border-color: #d9dee3 !important;
        display: flex;
        align-items: center;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 38px !important;
        padding-left: 12px;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px !important;
    }
    .asset-preview-card {
        background: #f8f9fa;
        border: 1px dashed #cbd5e1;
        border-radius: 8px;
        transition: all 0.2s ease;
    }
    .asset-preview-card:hover {
        background: #f1f5f9;
        border-color: #94a3b8;
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
        <a href="{{ route('fixed.index') }}" class="btn btn-label-info shadow-xs" target="_blank" title="Buka Daftar Fixed Asset Perusahaan">
            <i class="mdi mdi-office-building-cog-outline me-1"></i> Master Fixed Asset
        </a>
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
    <div class="card-header border-bottom py-3 bg-light d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div class="d-flex align-items-center gap-2">
            <h6 class="card-title mb-0 fw-bold">Daftar Aset &amp; Peminjaman Fasilitas Kerja</h6>
            <span class="badge bg-label-primary rounded-pill">{{ $assets->total() }} Data</span>
        </div>
        <div class="d-flex align-items-center gap-2">
            <form action="{{ route('hr.assets.index') }}" method="GET" class="d-flex gap-2 align-items-center">
                @if (request('employee_id'))
                    <input type="hidden" name="employee_id" value="{{ request('employee_id') }}">
                @endif
                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    <option value="Digunakan" @selected($status === 'Digunakan')>Digunakan (Aktif)</option>
                    <option value="Dikembalikan" @selected($status === 'Dikembalikan')>Dikembalikan</option>
                    <option value="Hilang/Rusak" @selected($status === 'Hilang/Rusak')>Hilang / Rusak</option>
                </select>
                @if ($status || request('employee_id'))
                    <a href="{{ route('hr.assets.index') }}" class="btn btn-sm btn-outline-secondary" title="Reset Filter">
                        <i class="mdi mdi-refresh"></i>
                    </a>
                @endif
            </form>
        </div>
    </div>
    <div class="table-responsive text-nowrap">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Nama Alat / Fasilitas</th>
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
                        $isFixed = !empty($asset->fixed_asset_id) && $asset->fixedAsset;
                        $faType = $isFixed ? $asset->fixedAsset->type : null;
                        $faTypeBadge = match($faType) {
                            'Peralatan Kantor' => 'primary',
                            'Tools' => 'info',
                            'Kendaraan' => 'warning',
                            'Mesin' => 'secondary',
                            default => 'dark',
                        };
                    @endphp
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-1 mb-1">
                                @if ($isFixed)
                                    <span class="badge bg-label-{{ $faTypeBadge }} py-0 px-1" style="font-size: 0.68rem;">
                                        <i class="mdi mdi-link-variant me-1"></i>{{ $faType }}
                                    </span>
                                @else
                                    <span class="badge bg-label-secondary py-0 px-1" style="font-size: 0.68rem;">Manual</span>
                                @endif
                                <span class="fw-bold text-heading">{{ $asset->asset_name }}</span>
                            </div>
                            @if ($asset->notes)
                                <span class="text-muted small text-truncate d-block" style="max-width: 260px;" title="{{ $asset->notes }}">
                                    <i class="mdi mdi-information-outline me-1"></i>{{ $asset->notes }}
                                </span>
                            @endif
                        </td>
                        <td>
                            @if ($isFixed)
                                <a href="{{ route('fixed.show', $asset->fixed_asset_id) }}" target="_blank"
                                    class="font-monospace small text-primary fw-semibold d-inline-flex align-items-center gap-1"
                                    title="Lihat Buku Aset di Menu Finance">
                                    {{ $asset->asset_code ?: ($asset->fixedAsset->code ?: 'FA-' . $asset->fixed_asset_id) }}
                                    <i class="mdi mdi-open-in-new font-size-11"></i>
                                </a>
                            @else
                                <div class="font-monospace small text-primary">{{ $asset->asset_code ?: '-' }}</div>
                            @endif
                            <div class="font-monospace small text-muted">S/N: {{ $asset->serial_number ?: '-' }}</div>
                        </td>
                        <td>
                            <div class="fw-semibold text-heading">{{ $empName }}</div>
                            <span class="text-muted small">
                                {{ $asset->employee->department?->name ?? '-' }}
                                @if($asset->employee->position) &bull; {{ $asset->employee->position->name }} @endif
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-label-{{ $asset->condition === 'Baik' ? 'success' : ($asset->condition === 'Normal' ? 'info' : ($asset->condition === 'Rusak Ringan' ? 'warning' : 'danger')) }}">
                                {{ $asset->condition }}
                            </span>
                        </td>
                        <td>
                            <span class="small">{{ \Carbon\Carbon::parse($asset->handover_date)->format('d M Y') }}</span>
                        </td>
                        <td>
                            <span class="small text-muted">{{ $asset->returned_date ? \Carbon\Carbon::parse($asset->returned_date)->format('d M Y') : '—' }}</span>
                        </td>
                        <td>
                            <span class="badge bg-label-{{ $badge }}">{{ $asset->status }}</span>
                        </td>
                        <td class="text-end">
                            <button type="button" class="btn btn-sm btn-icon btn-label-primary" data-bs-toggle="modal" data-bs-target="#modalEditAsset-{{ $asset->id }}" title="Perbarui Status">
                                <i class="mdi mdi-pencil-outline"></i>
                            </button>
                            <form action="{{ route('hr.assets.destroy', $asset->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus data serah terima alat kerja ini?');">
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
                                            <div>
                                                <h6 class="modal-title fw-bold mb-0">Update Status: {{ $asset->asset_name }}</h6>
                                                <small class="text-muted">Penerima: {{ $empName }}</small>
                                            </div>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body p-4">
                                            @if ($isFixed)
                                                <div class="alert alert-primary d-flex align-items-center py-2 px-3 mb-3 small" role="alert">
                                                    <i class="mdi mdi-link-variant me-2 fs-5"></i>
                                                    <div>
                                                        <strong>Terhubung ke Fixed Asset:</strong>
                                                        <a href="{{ route('fixed.show', $asset->fixed_asset_id) }}" target="_blank" class="fw-semibold text-primary text-decoration-underline ms-1">
                                                            {{ $asset->fixedAsset->code ?? $asset->asset_code }} ({{ $asset->fixedAsset->type }})
                                                        </a>
                                                        <div class="text-muted mt-1 font-size-11">
                                                            Mengubah status menjadi <em>Dikembalikan</em> akan otomatis melepaskan PIC dan mengembalikan status alat ke inventaris perusahaan.
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif

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
                                                    <textarea name="notes" class="form-control" rows="2" placeholder="Catatan kondisi saat pengembalian atau update status...">{{ $asset->notes }}</textarea>
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
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <form action="{{ route('hr.assets.store') }}" method="POST" id="form-new-asset" class="modal-content border-0 shadow">
            @csrf
            <div class="modal-header border-bottom bg-light py-3">
                <div class="d-flex align-items-center gap-2">
                    <div class="avatar avatar-sm bg-label-primary rounded d-flex align-items-center justify-content-center">
                        <i class="mdi mdi-handshake-outline fs-4"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0">Serah Terima Alat Kerja / Fasilitas</h5>
                        <small class="text-muted">Integrasi pencatatan inventaris alat kerja karyawan dengan Fixed Asset Reftech</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-3">
                    {{-- Karyawan Penerima --}}
                    <div class="col-12">
                        <label class="form-label fw-semibold">Pilih Karyawan Penerima <span class="text-danger">*</span></label>
                        <select name="employee_id" id="select-employee-id" class="form-select" required>
                            <option value="">-- Pilih Karyawan Penerima --</option>
                            @foreach ($employees as $emp)
                                <option value="{{ $emp->id }}" @selected(request('employee_id') == $emp->id)>
                                    {{ $emp->user?->name ?? ($emp->nik ? 'NIK: ' . $emp->nik : 'Karyawan #' . $emp->id) }}
                                    @if ($emp->department) &bull; {{ $emp->department->name }} @endif
                                    @if ($emp->position) — {{ $emp->position->name }} @endif
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Mode Sumber Aset: Fixed Asset vs Manual --}}
                    <div class="col-12">
                        <label class="form-label fw-semibold d-block">Sumber Data Aset</label>
                        <div class="btn-group w-100" role="group" aria-label="Sumber Aset">
                            <input type="radio" class="btn-check" name="asset_source_mode" id="mode-fixed" value="fixed" checked autocomplete="off">
                            <label class="btn btn-outline-primary" for="mode-fixed">
                                <i class="mdi mdi-office-building-cog me-1"></i> Pilih dari Fixed Asset Perusahaan (Terintegrasi)
                            </label>

                            <input type="radio" class="btn-check" name="asset_source_mode" id="mode-manual" value="manual" autocomplete="off">
                            <label class="btn btn-outline-secondary" for="mode-manual">
                                <i class="mdi mdi-pencil me-1"></i> Input Manual (Non-Fixed Asset / APD)
                            </label>
                        </div>
                    </div>

                    {{-- Section 1: Dropdown Pilih Fixed Asset --}}
                    <div class="col-12" id="section-fixed-asset">
                        <label class="form-label fw-semibold">
                            Cari &amp; Pilih Fixed Asset <span class="text-danger">*</span>
                        </label>
                        <select name="fixed_asset_id" id="select-fixed-asset" class="form-select" style="width: 100%;">
                            <option value="">-- Ketik / Pilih Aset (Peralatan Kantor, Tools, Kendaraan, Mesin) --</option>
                            @foreach ($fixedAssets->groupBy('type') as $type => $groupAssets)
                                <optgroup label="📂 Kategori: {{ $type }} ({{ count($groupAssets) }} unit)">
                                    @foreach ($groupAssets as $fa)
                                        @php
                                            $faName = match($fa->type) {
                                                'Tools' => $fa->toolsMaster->nama_tools ?? ($fa->desc !== '-' ? $fa->desc : 'Tools #' . $fa->id),
                                                'Mesin' => $fa->unit ? ($fa->unit->brand . ' ' . $fa->unit->model) : $fa->desc,
                                                'Kendaraan' => $fa->desc . ($fa->plat_nomor ? ' [' . $fa->plat_nomor . ']' : ''),
                                                default => $fa->desc,
                                            };
                                            $faCode = $fa->code ?: ('FA-' . str_pad($fa->id, 4, '0', STR_PAD_LEFT));
                                            $faSn = $fa->serial_number ?: '';
                                            $faCondition = in_array($fa->kondisi, ['Baik', 'Normal', 'Rusak Ringan', 'Perlu Servis']) ? $fa->kondisi : 'Baik';
                                            $isUsed = !empty($fa->id_pic);
                                            $picName = $fa->pic?->name;
                                        @endphp
                                        <option value="{{ $fa->id }}"
                                            data-name="{{ $faName }}"
                                            data-code="{{ $faCode }}"
                                            data-sn="{{ $faSn }}"
                                            data-condition="{{ $faCondition }}"
                                            data-type="{{ $fa->type }}"
                                            data-pic="{{ $picName }}"
                                            data-price="{{ $fa->total ? 'Rp ' . number_format($fa->total, 0, ',', '.') : '' }}"
                                            data-url="{{ route('fixed.show', $fa->id) }}"
                                        >
                                            [{{ $fa->type }}] {{ $faCode }} — {{ $faName }} @if($faSn) (S/N: {{ $faSn }}) @endif {{ $isUsed ? '⚠️ Sedang dipegang: ' . $picName : '✅ Tersedia' }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>

                        {{-- Preview Box Fixed Asset --}}
                        <div id="fixed-asset-preview" class="asset-preview-card p-3 mt-2 d-none">
                            <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar avatar-sm bg-label-info rounded d-flex align-items-center justify-content-center">
                                        <i class="mdi mdi-laptop" id="preview-icon"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-heading" id="preview-name">-</div>
                                        <div class="small text-muted font-monospace">
                                            <span id="preview-code" class="text-primary fw-semibold">-</span> | 
                                            S/N: <span id="preview-sn">-</span>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <span class="badge bg-label-primary" id="preview-type">-</span>
                                </div>
                            </div>
                            <div id="preview-pic-warning" class="alert alert-warning py-1 px-2 mt-2 mb-0 small d-none">
                                <i class="mdi mdi-alert-circle-outline me-1"></i>
                                Catatan: Aset ini sebelumnya tercatat dipegang oleh <strong id="preview-pic-name">-</strong>. Serah terima ini akan otomatis mengalihkan PIC aset ke karyawan penerima.
                            </div>
                        </div>
                    </div>

                    {{-- Asset Fields (Auto-filled or manual) --}}
                    <div class="col-12">
                        <label class="form-label fw-semibold">Nama Alat / Aset <span class="text-danger">*</span></label>
                        <input type="text" name="asset_name" id="input-asset-name" class="form-control" placeholder="Contoh: Laptop HP Pavilion 14 / Toolset Bosch 108 pcs" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-semibold">Kode Aset / Tag</label>
                        <input type="text" name="asset_code" id="input-asset-code" class="form-control" placeholder="Contoh: AKT-2026-001">
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-semibold">Serial Number (S/N)</label>
                        <input type="text" name="serial_number" id="input-asset-sn" class="form-control" placeholder="Contoh: 5CD23489XX">
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-semibold">Kondisi Fisik Saat Serah Terima <span class="text-danger">*</span></label>
                        <select name="condition" id="input-asset-condition" class="form-select" required>
                            <option value="Baik">Baik (Prima)</option>
                            <option value="Normal">Normal</option>
                            <option value="Rusak Ringan">Rusak Ringan</option>
                            <option value="Perlu Servis">Perlu Servis</option>
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-semibold">Tanggal Penyerahan <span class="text-danger">*</span></label>
                        <input type="date" name="handover_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Catatan / Kelengkapan Aksesoris</label>
                        <textarea name="notes" id="input-asset-notes" class="form-control" rows="2" placeholder="Contoh: Termasuk charger adapter original, tas ransel laptop, dan mouse wireless..."></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top bg-light py-3">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">
                    <i class="mdi mdi-check-circle-outline me-1"></i> Simpan Serah Terima
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('after-script')
<script src="{{ asset('assets') }}/vendor/libs/select2/select2.js"></script>
<script>
$(document).ready(function() {
    // Inisialisasi Select2 untuk pilihan Fixed Asset di dalam modal
    $('#select-fixed-asset').select2({
        dropdownParent: $('#modalNewAsset'),
        placeholder: '-- Ketik / Pilih Aset (Peralatan Kantor, Tools, Kendaraan, Mesin) --',
        allowClear: true
    });

    $('#select-employee-id').select2({
        dropdownParent: $('#modalNewAsset'),
        placeholder: '-- Pilih Karyawan Penerima --',
        allowClear: true
    });

    // Handle Switch Mode: Fixed Asset vs Manual
    $('input[name="asset_source_mode"]').on('change', function() {
        const isFixed = $(this).val() === 'fixed';
        if (isFixed) {
            $('#section-fixed-asset').slideDown(200);
            $('#select-fixed-asset').trigger('change');
        } else {
            $('#section-fixed-asset').slideUp(200);
            $('#select-fixed-asset').val('').trigger('change');
            $('#fixed-asset-preview').addClass('d-none');
            $('#input-asset-name').val('').focus();
            $('#input-asset-code').val('');
            $('#input-asset-sn').val('');
            $('#input-asset-condition').val('Baik');
        }
    });

    // Handle Selection of Fixed Asset
    $('#select-fixed-asset').on('change', function() {
        const selected = $(this).find('option:selected');
        const assetId = $(this).val();

        if (!assetId || assetId === '') {
            $('#fixed-asset-preview').addClass('d-none');
            return;
        }

        const name = selected.data('name') || '';
        const code = selected.data('code') || '';
        const sn = selected.data('sn') || '';
        const condition = selected.data('condition') || 'Baik';
        const type = selected.data('type') || '';
        const pic = selected.data('pic') || '';

        // Auto-fill form inputs
        $('#input-asset-name').val(name);
        $('#input-asset-code').val(code);
        $('#input-asset-sn').val(sn);
        $('#input-asset-condition').val(condition);

        // Update preview card
        $('#preview-name').text(name);
        $('#preview-code').text(code || 'No Code');
        $('#preview-sn').text(sn || '—');
        $('#preview-type').text(type || 'Fixed Asset');

        // Choose appropriate icon
        let iconClass = 'mdi-office-building-cog';
        if (type === 'Peralatan Kantor') iconClass = 'mdi-laptop';
        else if (type === 'Tools') iconClass = 'mdi-wrench';
        else if (type === 'Kendaraan') iconClass = 'mdi-car';
        else if (type === 'Mesin') iconClass = 'mdi-cog';

        $('#preview-icon').attr('class', 'mdi ' + iconClass);

        // PIC warning if already assigned
        if (pic && pic.trim() !== '') {
            $('#preview-pic-name').text(pic);
            $('#preview-pic-warning').removeClass('d-none');
        } else {
            $('#preview-pic-warning').addClass('d-none');
        }

        $('#fixed-asset-preview').removeClass('d-none');
    });

    // Re-adjust select2 when modal opens
    $('#modalNewAsset').on('shown.bs.modal', function () {
        $('#select-fixed-asset').select2({
            dropdownParent: $('#modalNewAsset'),
            placeholder: '-- Ketik / Pilih Aset (Peralatan Kantor, Tools, Kendaraan, Mesin) --',
            allowClear: true
        });
    });
});
</script>
@endpush
