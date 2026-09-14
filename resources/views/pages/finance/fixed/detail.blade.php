@extends('layouts.sales.app')
@section('title', 'Detail Fixed Asset - ' . ($fixed->code ?? $fixed->type))

@push('after-style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.css') }}" />
    <style>
        .asset-kpi-card {
            border-radius: 12px;
            border: 1px solid rgba(0, 0, 0, 0.06);
            transition: all 0.2s ease-in-out;
        }
        .asset-kpi-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
        }
        .stat-icon {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .detail-label {
            font-size: 0.8125rem;
            color: #8592a3;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
            margin-bottom: 2px;
        }
        .detail-value {
            font-size: 0.95rem;
            color: #384551;
            font-weight: 500;
        }
        .coa-box {
            border-radius: 8px;
            border-left: 4px solid var(--bs-primary);
            background: #f8f9fa;
            padding: 10px 14px;
        }
    </style>
@endpush

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Breadcrumb & Top Bar -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">
                <span class="text-muted fw-light">Finance / <a href="{{ route('fixed.index', ['type' => $fixed->type]) }}" class="text-muted">Fixed Asset</a> /</span> Detail Aset
            </h4>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <span class="badge bg-primary px-3 py-1 font-monospace fs-6">{{ $fixed->code ?: 'FA-' . $fixed->id }}</span>
                <span class="badge bg-label-info px-2 py-1">{{ $fixed->type }}</span>
                @if ($fixed->status == 1)
                    <span class="badge bg-label-success"><i class="mdi mdi-check-circle-outline me-1"></i>Sudah Dibayar</span>
                @else
                    <span class="badge bg-label-warning"><i class="mdi mdi-clock-outline me-1"></i>Belum Dibayar</span>
                @endif
                @if ($fixed->qc_status === 'checking')
                    <span class="badge bg-label-warning">QC: Dalam Pengecekan</span>
                @elseif ($fixed->qc_status === 'ok')
                    <span class="badge bg-label-success">QC: OK (Siap Ditawarkan)</span>
                @elseif ($fixed->qc_status === 'reject')
                    <span class="badge bg-label-danger">QC: Reject</span>
                @endif
                @if ($fixed->is_disposed)
                    <span class="badge bg-danger">Sudah Dihapus / Disposed</span>
                @endif
            </div>
        </div>
        <div class="mt-3 mt-md-0 d-flex gap-2">
            <a href="{{ route('fixed.index', ['type' => $fixed->type]) }}" class="btn btn-outline-secondary waves-effect">
                <i class="mdi mdi-arrow-left me-1"></i> Kembali
            </a>
            <a href="{{ route('fixed.edit', $fixed->id) }}" class="btn btn-primary waves-effect">
                <i class="mdi mdi-pencil-outline me-1"></i> Edit Aset
            </a>
        </div>
    </div>

    <!-- 4 KPI Summary Cards -->
    <div class="row g-3 mb-4">
        <!-- Nilai Perolehan -->
        <div class="col-sm-6 col-xl-3">
            <div class="card asset-kpi-card shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <span class="text-muted fw-medium d-block mb-1">Nilai Perolehan Awal</span>
                            <h4 class="card-title mb-1 text-dark fw-bold">Rp {{ number_format($fixed->total ?: 0, 0, ',', '.') }}</h4>
                            <small class="text-muted">Qty: <strong>{{ $fixed->qty ?: 1 }}</strong> unit</small>
                        </div>
                        <div class="stat-icon bg-label-primary">
                            <i class="mdi mdi-cash-multiple mdi-24px text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Masa Manfaat -->
        <div class="col-sm-6 col-xl-3">
            <div class="card asset-kpi-card shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <span class="text-muted fw-medium d-block mb-1">Masa Manfaat</span>
                            <h4 class="card-title mb-1 text-dark fw-bold">{{ $fixed->umur ?: '-' }} <span class="fs-6 fw-normal text-muted">Bulan</span></h4>
                            <small class="text-muted">{{ $fixed->umur ? round($fixed->umur / 12, 1) . ' Tahun • ' . ($fixed->metode ?: 'Garis Lurus') : 'Tanpa Penyusutan' }}</small>
                        </div>
                        <div class="stat-icon bg-label-info">
                            <i class="mdi mdi-calendar-clock mdi-24px text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Akumulasi Penyusutan -->
        <div class="col-sm-6 col-xl-3">
            <div class="card asset-kpi-card shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <span class="text-muted fw-medium d-block mb-1">Akumulasi Penyusutan</span>
                            <h4 class="card-title mb-1 text-danger fw-bold">Rp {{ number_format($totalPenyusutan ?: 0, 0, ',', '.') }}</h4>
                            <small class="text-danger fw-semibold">
                                @if($fixed->total > 0 && $totalPenyusutan > 0)
                                    {{ round(($totalPenyusutan / $fixed->total) * 100, 1) }}% dari perolehan
                                @else
                                    0% disusutkan
                                @endif
                            </small>
                        </div>
                        <div class="stat-icon bg-label-danger">
                            <i class="mdi mdi-trending-down mdi-24px text-danger"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Nilai Buku Bersih -->
        <div class="col-sm-6 col-xl-3">
            <div class="card asset-kpi-card shadow-sm h-100 border-start border-4 border-success">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <span class="text-muted fw-medium d-block mb-1">Nilai Buku Bersih (NBV)</span>
                            <h4 class="card-title mb-1 text-success fw-bold">Rp {{ number_format($nilaiBuku ?: 0, 0, ',', '.') }}</h4>
                            <small class="text-success fw-semibold">
                                <i class="mdi mdi-check-circle me-1"></i>Nilai buku saat ini
                            </small>
                        </div>
                        <div class="stat-icon bg-label-success">
                            <i class="mdi mdi-book-open-outline mdi-24px text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Main Asset Details -->
        <div class="col-xl-8 col-lg-7 col-12 mb-4">
            <!-- Informasi Utama -->
            <div class="card shadow-sm mb-4">
                <div class="card-header border-bottom py-3 d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0 fw-bold">
                        <i class="mdi mdi-card-bulleted-outline text-primary me-2"></i>Informasi & Spesifikasi Aset
                    </h5>
                    <span class="text-muted small">No. Invoice: <strong>{{ $fixed->no_invoice ?: '-' }}</strong></span>
                </div>
                <div class="card-body pt-4">
                    <div class="mb-4">
                        <div class="detail-label">Keterangan / Nama Aset</div>
                        <h5 class="fw-bold text-dark mb-0">{{ $fixed->desc ?: '-' }}</h5>
                    </div>

                    <!-- Spesifikasi Kategori Spesifik -->
                    @if ($fixed->type === 'Kendaraan')
                        <div class="alert alert-light border p-3 mb-4">
                            <h6 class="fw-bold text-primary mb-3"><i class="mdi mdi-car me-1"></i>Spesifikasi Armada Kendaraan</h6>
                            <div class="row g-3">
                                <div class="col-sm-6 col-md-4">
                                    <div class="detail-label">Plat Nomor</div>
                                    <div class="detail-value fw-bold text-dark font-monospace">{{ $fixed->plat_nomor ?: '-' }}</div>
                                </div>
                                <div class="col-sm-6 col-md-4">
                                    <div class="detail-label">Jenis Kendaraan</div>
                                    <div class="detail-value">{{ $fixed->jenis_kendaraan ?: '-' }}</div>
                                </div>
                                <div class="col-sm-6 col-md-4">
                                    <div class="detail-label">Merk / Model</div>
                                    <div class="detail-value">{{ $fixed->merk_model ?: '-' }}</div>
                                </div>
                                <div class="col-sm-6 col-md-4">
                                    <div class="detail-label">Bahan Bakar</div>
                                    <div class="detail-value">
                                        @if($fixed->bahan_bakar)
                                            <span class="badge bg-label-info">{{ $fixed->bahan_bakar }}</span>
                                        @else
                                            -
                                        @endif
                                    </div>
                                </div>
                                <div class="col-sm-6 col-md-8">
                                    <div class="detail-label">Atas Nama (STNK)</div>
                                    <div class="detail-value">{{ $fixed->atas_nama ?: 'Perusahaan' }}</div>
                                </div>
                            </div>
                        </div>
                    @elseif ($fixed->type === 'Mesin')
                        <div class="alert alert-light border p-3 mb-4">
                            <h6 class="fw-bold text-primary mb-3"><i class="mdi mdi-cog-outline me-1"></i>Identitas Unit Mesin (E-Stock)</h6>
                            <div class="row g-3">
                                <div class="col-sm-6 col-md-4">
                                    <div class="detail-label">Brand & Model</div>
                                    <div class="detail-value fw-bold text-dark">{{ $fixed->unit?->brand ?: '-' }} {{ $fixed->unit?->model ?: '' }}</div>
                                </div>
                                <div class="col-sm-6 col-md-4">
                                    <div class="detail-label">SKU Unit</div>
                                    <div class="detail-value font-monospace">{{ $fixed->unit?->sku ?: '-' }}</div>
                                </div>
                                <div class="col-sm-6 col-md-4">
                                    <div class="detail-label">Serial Number</div>
                                    <div class="detail-value font-monospace">{{ $fixed->serial_number ?: '-' }}</div>
                                </div>
                                <div class="col-sm-6 col-md-4">
                                    <div class="detail-label">Kondisi</div>
                                    <div class="detail-value">
                                        <span class="badge {{ ($fixed->kondisi ?? '') === 'Baru' ? 'bg-label-success' : 'bg-label-warning' }}">
                                            {{ $fixed->kondisi ?: '-' }}
                                        </span>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-md-4">
                                    <div class="detail-label">QC Status</div>
                                    <div class="detail-value">{{ $fixed->qc_status ?: '-' }}</div>
                                </div>
                            </div>
                        </div>
                    @elseif ($fixed->type === 'Tools')
                        <div class="alert alert-light border p-3 mb-4">
                            <h6 class="fw-bold text-primary mb-3"><i class="mdi mdi-tools me-1"></i>Data Operasional Tools</h6>
                            <div class="row g-3">
                                <div class="col-sm-6 col-md-4">
                                    <div class="detail-label">Nama Tools Master</div>
                                    <div class="detail-value fw-bold text-dark">{{ $fixed->toolsMaster?->nama_tools ?: '-' }}</div>
                                </div>
                                <div class="col-sm-6 col-md-4">
                                    <div class="detail-label">Teknisi (PIC)</div>
                                    <div class="detail-value">
                                        @if($fixed->pic)
                                            <span class="badge bg-label-primary">{{ $fixed->pic->name }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-sm-6 col-md-4">
                                    <div class="detail-label">Tgl Serah Terima</div>
                                    <div class="detail-value">
                                        {{ $fixed->tanggal_serah_terima ? \Carbon\Carbon::parse($fixed->tanggal_serah_terima)->format('d M Y') : '-' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Tanggal-tanggal penting -->
                    <div class="row g-3 pt-2 border-top">
                        <div class="col-sm-6 col-md-3">
                            <div class="detail-label">Tanggal Beli / Perolehan</div>
                            <div class="detail-value">
                                {{ $fixed->beli ? \Carbon\Carbon::parse($fixed->beli)->format('d M Y') : ($fixed->date ? \Carbon\Carbon::parse($fixed->date)->format('d M Y') : '-') }}
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-3">
                            <div class="detail-label">Tanggal Mulai Pakai</div>
                            <div class="detail-value">
                                {{ $fixed->pakai ? \Carbon\Carbon::parse($fixed->pakai)->format('d M Y') : '-' }}
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-3">
                            <div class="detail-label">Mulai Penyusutan</div>
                            <div class="detail-value">
                                {{ $fixed->mulai_penyusutan ? \Carbon\Carbon::parse($fixed->mulai_penyusutan)->format('d M Y') : ($fixed->beli ? \Carbon\Carbon::parse($fixed->beli)->format('d M Y') : '-') }}
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-3">
                            <div class="detail-label">Tanggal Pembayaran</div>
                            <div class="detail-value">
                                {{ $fixed->bayar ? \Carbon\Carbon::parse($fixed->bayar)->format('d M Y') : '-' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Struktur Akun Akuntansi (COA) -->
            <div class="card shadow-sm mb-4">
                <div class="card-header border-bottom py-3">
                    <h5 class="card-title mb-0 fw-bold">
                        <i class="mdi mdi-book-open-page-variant-outline text-primary me-2"></i>Pemetaan Akun Akuntansi (COA)
                    </h5>
                </div>
                <div class="card-body pt-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="coa-box">
                                <div class="detail-label text-primary">Akun Aktiva (Aset Tetap)</div>
                                <div class="fw-semibold text-dark">
                                    {{ $fixed->aktiva ? $fixed->aktiva->code . ' - ' . $fixed->aktiva->name : 'Belum Ditentukan' }}
                                </div>
                                <small class="text-muted">{{ $fixed->aktiva?->category ?: '-' }}</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="coa-box" style="border-left-color: #ff3e1d;">
                                <div class="detail-label text-danger">Akun Akumulasi Penyusutan</div>
                                <div class="fw-semibold text-dark">
                                    {{ $fixed->penyusutan ? $fixed->penyusutan->code . ' - ' . $fixed->penyusutan->name : 'Belum Ditentukan' }}
                                </div>
                                <small class="text-muted">{{ $fixed->penyusutan?->category ?: '-' }}</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="coa-box" style="border-left-color: #ffab00;">
                                <div class="detail-label text-warning">Akun Beban Penyusutan</div>
                                <div class="fw-semibold text-dark">
                                    {{ $fixed->beban ? $fixed->beban->code . ' - ' . $fixed->beban->name : 'Belum Ditentukan' }}
                                </div>
                                <small class="text-muted">{{ $fixed->beban?->category ?: '-' }}</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="coa-box" style="border-left-color: #03c3ec;">
                                <div class="detail-label text-info">Akun Kas / Bank Pengeluaran</div>
                                <div class="fw-semibold text-dark">
                                    {{ $fixed->pengeluaran ? $fixed->pengeluaran->code . ' - ' . $fixed->pengeluaran->name : 'Belum Ditentukan' }}
                                </div>
                                <small class="text-muted">{{ $fixed->pengeluaran?->category ?: '-' }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Conditional Table: Riwayat Servis (Mesin) -->
            @if ($fixed->type === 'Mesin')
                <div class="card shadow-sm mb-4">
                    <div class="card-header border-bottom py-3 d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0 fw-bold">
                            <i class="mdi mdi-wrench-outline text-primary me-2"></i>Riwayat Servis & Spare Part
                        </h5>
                        <span class="badge bg-label-primary">{{ count($services) }} Servis</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Spare Part</th>
                                    <th>Warehouse</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-end">Biaya Servis</th>
                                    <th>Catatan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($services as $service)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($service->date)->format('d M Y') }}</td>
                                        <td class="fw-semibold">{{ $service->detailProduct?->product?->commodity ?? '-' }}</td>
                                        <td><span class="badge bg-label-secondary">{{ $service->warehouse }}</span></td>
                                        <td class="text-center">{{ $service->qty }}</td>
                                        <td class="text-end fw-bold text-dark">Rp {{ number_format($service->amount, 0, ',', '.') }}</td>
                                        <td class="text-muted small">{{ $service->note ?: '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">
                                            Belum ada riwayat servis atau pemakaian spare part untuk mesin ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            <!-- Conditional Table: Riwayat Perawatan (Kendaraan) -->
            @if ($fixed->type === 'Kendaraan')
                <div class="card shadow-sm mb-4">
                    <div class="card-header border-bottom py-3 d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0 fw-bold">
                            <i class="mdi mdi-car-wrench text-primary me-2"></i>Riwayat Perawatan Kendaraan
                        </h5>
                        <a href="{{ route('fixed.maintenance.create', $fixed->id) }}" class="btn btn-sm btn-primary">
                            <i class="mdi mdi-plus me-1"></i> Tambah Perawatan
                        </a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Jenis Perawatan</th>
                                    <th>Jatuh Tempo Berikutnya</th>
                                    <th class="text-end">Biaya</th>
                                    <th>Catatan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($maintenanceLogs as $log)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($log->tanggal)->format('d M Y') }}</td>
                                        <td><span class="badge bg-label-info">{{ $log->jenis }}</span></td>
                                        <td>
                                            @if($log->tanggal_jatuh_tempo)
                                                <span class="text-dark fw-medium">{{ \Carbon\Carbon::parse($log->tanggal_jatuh_tempo)->format('d M Y') }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="text-end fw-bold text-dark">
                                            {{ $log->biaya ? 'Rp ' . number_format($log->biaya, 0, ',', '.') : '-' }}
                                        </td>
                                        <td class="text-muted small">{{ $log->catatan ?: '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">
                                            Belum ada riwayat perawatan rutin atau servis kendaraan ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar Actions & Meta -->
        <div class="col-xl-4 col-lg-5 col-12">
            <!-- Action Card -->
            <div class="card shadow-sm mb-4">
                <div class="card-header border-bottom py-3">
                    <h6 class="card-title mb-0 fw-bold">
                        <i class="mdi mdi-cogs text-primary me-2"></i>Aksi Aset
                    </h6>
                </div>
                <div class="card-body pt-3 d-flex flex-column gap-2">
                    @if ($fixed->type === 'Mesin')
                        <a href="{{ route('unit-acquisition.show', $fixed->id) }}"
                            class="btn btn-outline-primary w-100 waves-effect text-start d-flex align-items-center">
                            <i class="mdi mdi-cube-send me-2"></i> Kelola di Unit Acquisition (E-Stock)
                        </a>
                    @endif
                    @if ($fixed->type === 'Kendaraan')
                        <a href="{{ route('fixed.maintenance.create', $fixed->id) }}"
                            class="btn btn-outline-primary w-100 waves-effect text-start d-flex align-items-center">
                            <i class="mdi mdi-car-wrench me-2"></i> Tambah Perawatan Kendaraan
                        </a>
                    @endif
                    <a href="{{ route('fixed.edit', $fixed->id) }}"
                        class="btn btn-outline-secondary w-100 waves-effect text-start d-flex align-items-center">
                        <i class="mdi mdi-square-edit-outline me-2"></i> Edit Data Aset
                    </a>
                    <a class="btn btn-outline-info w-100 waves-effect text-start d-flex align-items-center" target="_blank"
                        href="{{ route('expense.print', $fixed->id) }}">
                        <i class="mdi mdi-printer me-2"></i> Cetak Dokumen / Voucher
                    </a>
                    <button type="button" class="btn btn-outline-danger w-100 waves-effect text-start d-flex align-items-center delete-fixed"
                        data-id="{{ $fixed->id }}">
                        <i class="mdi mdi-trash-can-outline me-2"></i> Hapus Aset Tetap
                    </button>
                </div>
            </div>

            <!-- Supplier Info Card -->
            @if ($fixed->supplier)
                <div class="card shadow-sm mb-4">
                    <div class="card-header border-bottom py-3">
                        <h6 class="card-title mb-0 fw-bold">
                            <i class="mdi mdi-truck-delivery-outline text-primary me-2"></i>Informasi Supplier
                        </h6>
                    </div>
                    <div class="card-body pt-3">
                        <h6 class="fw-bold text-dark mb-1">{{ $fixed->supplier->supplier }}</h6>
                        @if ($fixed->supplier->address)
                            <p class="text-muted small mb-2"><i class="mdi mdi-map-marker-outline me-1"></i>{{ $fixed->supplier->address }}</p>
                        @endif
                        @if ($fixed->supplier->phone)
                            <p class="text-muted small mb-2"><i class="mdi mdi-phone-outline me-1"></i>{{ $fixed->supplier->phone }}</p>
                        @endif
                        @if ($fixed->supplier->info)
                            <div class="alert alert-light border p-2 mb-0 small text-muted">
                                {{ $fixed->supplier->info }}
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Disposal Card (If Disposed) -->
            @if ($fixed->is_disposed)
                <div class="card shadow-sm mb-4 border-danger">
                    <div class="card-header bg-label-danger py-3">
                        <h6 class="card-title mb-0 fw-bold text-danger">
                            <i class="mdi mdi-alert-octagon-outline me-2"></i>Aset Telah Dihapus / Disposed
                        </h6>
                    </div>
                    <div class="card-body pt-3">
                        <div class="mb-2">
                            <span class="detail-label">Tanggal Disposal</span>
                            <div class="detail-value">{{ $fixed->tanggal_disposal ? \Carbon\Carbon::parse($fixed->tanggal_disposal)->format('d M Y') : '-' }}</div>
                        </div>
                        <div class="mb-2">
                            <span class="detail-label">Nilai Buku Saat Disposal</span>
                            <div class="detail-value fw-bold text-dark">Rp {{ number_format($fixed->nilai_buku_disposal ?: 0, 0, ',', '.') }}</div>
                        </div>
                        <div>
                            <span class="detail-label">Harga Jual Final</span>
                            <div class="detail-value fw-bold text-success">Rp {{ number_format($fixed->harga_jual_final ?: 0, 0, ',', '.') }}</div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('after-script')
    <script src="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.js') }}"></script>
    <script>
        $(document).on('click', '.delete-fixed', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: "Hapus Aset Tetap?",
                text: "Data aset tetap dan riwayat terkait akan dihapus secara permanen!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Ya, Hapus!",
                cancelButtonText: "Batal",
                customClass: {
                    confirmButton: "btn btn-danger me-3 waves-effect waves-light",
                    cancelButton: "btn btn-label-secondary waves-effect",
                },
                buttonsStyling: false,
            }).then(function(result) {
                if (result.value) {
                    $.ajax({
                        url: '{{ url('fixed') }}/' + id,
                        type: 'POST',
                        data: {
                            '_method': 'DELETE',
                            '_token': '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response == 1) {
                                Swal.fire({
                                    icon: "success",
                                    title: "Berhasil Dihapus!",
                                    text: "Data aset tetap telah dihapus.",
                                    customClass: {
                                        confirmButton: "btn btn-success waves-effect",
                                    },
                                });
                                window.setTimeout(function() {
                                    window.location.href = '{{ route('fixed.index', ['type' => $fixed->type]) }}';
                                }, 1500);
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: 'Gagal menghapus aset tetap.'
                                });
                            }
                        },
                        error: function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: 'Terjadi kesalahan sistem saat menghapus aset.'
                            });
                        }
                    });
                }
            });
        });
    </script>
@endpush
