@extends('layouts.sales.app')
@section('title', 'Detail Fixed Asset - ' . ($fixed->code ?? $fixed->type))

@push('after-style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.css') }}" />
    <style>
        .asset-kpi-card {
            border-radius: 10px;
            border: 1px solid rgba(67, 89, 113, 0.12);
            box-shadow: 0 2px 6px 0 rgba(67, 89, 113, 0.05);
            transition: all 0.2s ease-in-out;
            background: #ffffff;
        }
        .asset-kpi-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 14px rgba(67, 89, 113, 0.1);
        }
        .stat-icon {
            width: 44px;
            height: 44px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .detail-card {
            border-radius: 10px;
            border: 1px solid rgba(67, 89, 113, 0.12);
            box-shadow: 0 2px 6px 0 rgba(67, 89, 113, 0.05);
            background: #ffffff;
        }
        .detail-label {
            font-size: 0.72rem;
            color: #8592a3;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
            margin-bottom: 2px;
        }
        .detail-value {
            font-size: 0.9rem;
            color: #384551;
            font-weight: 600;
        }
        .coa-box {
            border-radius: 8px;
            border: 1px solid #e7ebee;
            border-left: 4px solid var(--bs-primary);
            background: #fbfcfd;
            padding: 12px 14px;
            min-height: 88px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .info-spec-box {
            border-radius: 8px;
            background: #fbfcfd;
            border: 1px solid #e7ebee;
            padding: 14px;
        }
        .supplier-box {
            border-radius: 8px;
            background: #fbfcfd;
            border: 1px solid #e7ebee;
            padding: 14px;
        }
        .table-custom thead th {
            font-size: 0.76rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
            color: #697a8d;
            padding: 0.75rem 1rem;
            white-space: nowrap;
        }
        .table-custom tbody td {
            padding: 0.75rem 1rem;
            vertical-align: middle;
        }
    </style>
@endpush

@section('content')
<div class="container-fluid px-3 px-md-4 py-3 flex-grow-1">
    <!-- Breadcrumb & Top Action Bar -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-3">
        <div>
            <h4 class="fw-bold mb-1">
                <span class="text-muted fw-light">Finance / <a href="{{ route('fixed.index', ['type' => $fixed->type]) }}" class="text-muted">Fixed Asset</a> /</span> Detail Aset
            </h4>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <span class="badge bg-primary px-2.5 py-1 font-monospace fs-6">{{ $fixed->code ?: 'FA-' . $fixed->id }}</span>
                <span class="badge bg-label-info px-2 py-1">{{ $fixed->type }}</span>
                @if (!($fixed->type === 'Bangunan' && $fixed->status_bangunan === 'construction'))
                    @if ($fixed->status == 1)
                        <span class="badge bg-label-success"><i class="mdi mdi-check-circle-outline me-1"></i>Sudah Dibayar</span>
                    @else
                        <span class="badge bg-label-warning"><i class="mdi mdi-clock-outline me-1"></i>Belum Dibayar</span>
                    @endif
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
                @if ($fixed->type === 'Bangunan')
                    @if ($fixed->status_bangunan === 'construction')
                        <span class="badge bg-warning"><i class="mdi mdi-hammer-wrench me-1"></i>Dalam Pembangunan</span>
                    @else
                        <span class="badge bg-success"><i class="mdi mdi-check-circle-outline me-1"></i>Siap Operasional</span>
                    @endif
                    @if ($fixed->tipe_pengadaan === 'swakelola')
                        <span class="badge bg-label-primary">Pembangunan Swakelola</span>
                    @elseif ($fixed->tipe_pengadaan === 'kontraktor')
                        <span class="badge bg-label-info">Kontraktor</span>
                    @elseif ($fixed->tipe_pengadaan === 'beli_jadi')
                        <span class="badge bg-label-secondary">Beli Jadi</span>
                    @endif
                @endif
            </div>
        </div>
        <div class="d-flex flex-wrap gap-2 align-items-center">
            <a href="{{ route('fixed.index', ['type' => $fixed->type]) }}" class="btn btn-outline-secondary btn-sm waves-effect">
                <i class="mdi mdi-arrow-left me-1"></i> Kembali
            </a>
            @if ($fixed->type === 'Mesin')
                <a href="{{ route('unit-acquisition.show', $fixed->id) }}" class="btn btn-outline-primary btn-sm waves-effect">
                    <i class="mdi mdi-cube-send me-1"></i> Kelola di E-Stock
                </a>
            @endif
            @if ($fixed->type === 'Kendaraan')
                <a href="{{ route('fixed.maintenance.create', $fixed->id) }}" class="btn btn-outline-primary btn-sm waves-effect">
                    <i class="mdi mdi-car-wrench me-1"></i> Tambah Perawatan
                </a>
            @endif
            <a href="{{ route('expense.print', $fixed->id) }}" target="_blank" class="btn btn-outline-info btn-sm waves-effect">
                <i class="mdi mdi-printer me-1"></i> Cetak Voucher
            </a>
            <a href="{{ route('fixed.edit', $fixed->id) }}" class="btn btn-primary btn-sm waves-effect">
                <i class="mdi mdi-pencil-outline me-1"></i> Edit Aset
            </a>
            <button type="button" class="btn btn-outline-danger btn-sm waves-effect delete-fixed" data-id="{{ $fixed->id }}">
                <i class="mdi mdi-trash-can-outline me-1"></i> Hapus Aset
            </button>
        </div>
    </div>

    <!-- 4 KPI Summary Cards -->
    <div class="row g-3 mb-3">
        <!-- Nilai Perolehan -->
        <div class="col-sm-6 col-xl-3">
            <div class="card asset-kpi-card h-100">
                <div class="card-body p-3">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <span class="detail-label d-block mb-1">Nilai Perolehan Awal</span>
                            <h4 class="card-title mb-1 text-dark fw-bold" style="font-size: 1.25rem;">Rp {{ number_format($fixed->total ?: 0, 0, ',', '.') }}</h4>
                            <small class="text-muted">Qty: <strong>{{ $fixed->qty ?: 1 }}</strong> unit</small>
                        </div>
                        <div class="stat-icon bg-label-primary">
                            <i class="mdi mdi-cash-multiple mdi-20px text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Masa Manfaat -->
        <div class="col-sm-6 col-xl-3">
            <div class="card asset-kpi-card h-100">
                <div class="card-body p-3">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <span class="detail-label d-block mb-1">Masa Manfaat</span>
                            <h4 class="card-title mb-1 text-dark fw-bold" style="font-size: 1.25rem;">{{ $fixed->umur ?: '-' }} <span class="fs-6 fw-normal text-muted">Bulan</span></h4>
                            <small class="text-muted">{{ $fixed->umur ? round($fixed->umur / 12, 1) . ' Thn • ' . ($fixed->metode ?: 'Garis Lurus') : 'Tanpa Penyusutan' }}</small>
                        </div>
                        <div class="stat-icon bg-label-info">
                            <i class="mdi mdi-calendar-clock mdi-20px text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Akumulasi Penyusutan -->
        <div class="col-sm-6 col-xl-3">
            <div class="card asset-kpi-card h-100">
                <div class="card-body p-3">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <span class="detail-label d-block mb-1">Akumulasi Penyusutan</span>
                            <h4 class="card-title mb-1 text-danger fw-bold" style="font-size: 1.25rem;">Rp {{ number_format($totalPenyusutan ?: 0, 0, ',', '.') }}</h4>
                            <small class="text-danger fw-semibold">
                                @if($fixed->total > 0 && $totalPenyusutan > 0)
                                    {{ round(($totalPenyusutan / $fixed->total) * 100, 1) }}% dari perolehan
                                @else
                                    0% disusutkan
                                @endif
                            </small>
                        </div>
                        <div class="stat-icon bg-label-danger">
                            <i class="mdi mdi-trending-down mdi-20px text-danger"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Nilai Buku Bersih -->
        <div class="col-sm-6 col-xl-3">
            <div class="card asset-kpi-card h-100 border-start border-4 border-success">
                <div class="card-body p-3">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <span class="detail-label d-block mb-1">Nilai Buku Bersih (NBV)</span>
                            <h4 class="card-title mb-1 text-success fw-bold" style="font-size: 1.25rem;">Rp {{ number_format($nilaiBuku ?: 0, 0, ',', '.') }}</h4>
                            <small class="text-success fw-semibold">
                                <i class="mdi mdi-check-circle me-1"></i>Nilai buku saat ini
                            </small>
                        </div>
                        <div class="stat-icon bg-label-success">
                            <i class="mdi mdi-book-open-outline mdi-20px text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Section: Analisis Kinerja Finansial & Pendapatan Aset (Unit Economics - Khusus Mesin / Aset Komersial Rental) -->
    @if ($fixed->type === 'Mesin' || $confirmedOffers->isNotEmpty())
        <div class="card detail-card mb-3">
            <div class="card-header border-bottom py-3 px-3 px-md-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 bg-transparent">
                <div>
                    <h5 class="card-title mb-0 fw-bold fs-6 d-flex align-items-center">
                        <i class="mdi mdi-finance text-primary me-2"></i>Kinerja Finansial & Pendapatan Aset (Unit Economics)
                    </h5>
                    <small class="text-muted">Analisis laba rugi riil per unit aset: perbandingan pendapatan sewa/PO, biaya perawatan & suku cadang, dan beban penyusutan.</small>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-label-primary px-2.5 py-1">
                        <i class="mdi mdi-chart-timeline-variant me-1"></i>Financial Health
                    </span>
                </div>
            </div>
            <div class="card-body p-3 p-md-4">
                <!-- 4 Quick Yield KPI Metrics with Harmonious Pantone Palette -->
                <div class="row g-3 mb-4">
                    <div class="col-6 col-lg-3">
                        <div class="p-3 rounded border h-100 bg-white" style="border-left: 4px solid #10b981 !important;">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <span class="detail-label d-block mb-0 text-muted">Total Pendapatan (PO)</span>
                                <span class="badge bg-label-success" style="font-size: 10px; padding: 2px 6px;">
                                    <i class="mdi mdi-arrow-down-left me-1"></i>Inflow
                                </span>
                            </div>
                            <h5 class="fw-bold mb-1 text-dark" style="font-size: 1.15rem;">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h5>
                            <small class="text-muted d-block" style="font-size: 0.74rem;">Dari {{ $confirmedOffers->count() }} penawaran PO deal</small>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3">
                        <div class="p-3 rounded border h-100 bg-white" style="border-left: 4px solid #f59e0b !important;">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <span class="detail-label d-block mb-0 text-muted">Total Biaya Perawatan</span>
                                <span class="badge bg-label-warning" style="font-size: 10px; padding: 2px 6px;">
                                    <i class="mdi mdi-wrench me-1"></i>Maintenance
                                </span>
                            </div>
                            <h5 class="fw-bold mb-1 text-dark" style="font-size: 1.15rem;">Rp {{ number_format($totalMaintenanceCost, 0, ',', '.') }}</h5>
                            <small class="text-muted d-block" style="font-size: 0.74rem;">Work Orders &amp; Log Servis</small>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3">
                        <div class="p-3 rounded border h-100 bg-white" style="border-left: 4px solid #ef4444 !important;">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <span class="detail-label d-block mb-0 text-muted">Akumulasi Penyusutan</span>
                                <span class="badge bg-label-danger" style="font-size: 10px; padding: 2px 6px;">
                                    <i class="mdi mdi-trending-down me-1"></i>Depresiasi
                                </span>
                            </div>
                            <h5 class="fw-bold mb-1 text-dark" style="font-size: 1.15rem;">Rp {{ number_format($totalPenyusutan, 0, ',', '.') }}</h5>
                            <small class="text-muted d-block" style="font-size: 0.74rem;">Amortisasi nilai perolehan</small>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3">
                        <div class="p-3 rounded border h-100 bg-white" style="border-left: 4px solid {{ $netAssetProfit >= 0 ? '#059669' : '#dc2626' }} !important; box-shadow: 0 2px 6px rgba(0,0,0,0.03);">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <span class="detail-label d-block mb-0" style="color: #475569;">Net Kontribusi Laba</span>
                                <span class="badge {{ $netAssetProfit >= 0 ? 'bg-label-success' : 'bg-label-danger' }}" style="font-size: 10px; padding: 3px 7px;">
                                    <i class="mdi {{ $netAssetProfit >= 0 ? 'mdi-trending-up' : 'mdi-trending-down' }} me-1"></i>{{ $netAssetProfit >= 0 ? 'Surplus' : 'Defisit' }}
                                </span>
                            </div>
                            <h5 class="fw-bold mb-1" style="font-size: 1.2rem; color: {{ $netAssetProfit >= 0 ? '#047857' : '#b91c1c' }};">
                                {{ $netAssetProfit >= 0 ? '+' : '' }}Rp {{ number_format($netAssetProfit, 0, ',', '.') }}
                            </h5>
                            <small class="{{ $netAssetProfit >= 0 ? 'text-success' : 'text-danger' }} fw-medium" style="font-size: 0.74rem;">
                                <i class="mdi {{ $netAssetProfit >= 0 ? 'mdi-check-circle-outline' : 'mdi-alert-circle-outline' }} me-1"></i>{{ $netAssetProfit >= 0 ? 'Laba operasional aset positif' : 'Biaya & penyusutan > pendapatan' }}
                            </small>
                        </div>
                    </div>
                </div>

                <div class="row g-3">
                    <!-- Tabel 1: Analisis Breakdown Tahunan (Annual Yield) -->
                    <div class="col-lg-6 col-12">
                        <div class="border rounded p-3 bg-white h-100 d-flex flex-column justify-content-between">
                            <div>
                                <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                                    <h6 class="fw-bold text-dark mb-0 fs-6">
                                        <i class="mdi mdi-calendar-range text-primary me-1"></i>Breakdown Finansial Per Tahun
                                    </h6>
                                    <span class="badge bg-label-secondary">Tahunan</span>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-sm table-hover align-middle mb-0" style="font-size: 0.84rem;">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Tahun</th>
                                                <th class="text-end">Penyusutan</th>
                                                <th class="text-end">Biaya Servis</th>
                                                <th class="text-end">Pendapatan</th>
                                                <th class="text-end">Net Margin</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($annualBreakdown ?? [] as $rowAnnual)
                                                <tr>
                                                    <td class="fw-bold text-dark">{{ $rowAnnual['year'] }}</td>
                                                    <td class="text-end text-danger text-nowrap">
                                                        Rp {{ number_format($rowAnnual['depreciation'], 0, ',', '.') }}
                                                    </td>
                                                    <td class="text-end text-warning text-nowrap">
                                                        Rp {{ number_format($rowAnnual['maintenance'], 0, ',', '.') }}
                                                    </td>
                                                    <td class="text-end text-success fw-semibold text-nowrap">
                                                        Rp {{ number_format($rowAnnual['revenue'], 0, ',', '.') }}
                                                    </td>
                                                    <td class="text-end fw-bold {{ $rowAnnual['net_profit'] >= 0 ? 'text-success' : 'text-danger' }} text-nowrap">
                                                        {{ $rowAnnual['net_profit'] >= 0 ? '+' : '' }}Rp {{ number_format($rowAnnual['net_profit'], 0, ',', '.') }}
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="5" class="text-center text-muted py-3">Belum ada data rekapan tahunan.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <small class="text-muted d-block mt-2 pt-2 border-top" style="font-size: 0.72rem;">
                                * Net Margin = Pendapatan PO - (Biaya Servis &amp; Suku Cadang + Beban Penyusutan).
                            </small>
                        </div>
                    </div>

                    <!-- Tabel 2: Riwayat Penawaran PO (Quotation Deal / Rental) -->
                    <div class="col-lg-6 col-12">
                        <div class="border rounded p-3 bg-white h-100 d-flex flex-column justify-content-between">
                            <div>
                                <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                                    <h6 class="fw-bold text-dark mb-0 fs-6">
                                        <i class="mdi mdi-file-check-outline text-success me-1"></i>Penawaran PO (Deal / Rental) Terkait
                                    </h6>
                                    <span class="badge bg-label-success">PO Received</span>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-sm table-hover align-middle mb-0" style="font-size: 0.84rem;">
                                        <thead class="table-light">
                                            <tr>
                                                <th>No. Quote / PO</th>
                                                <th>Customer</th>
                                                <th>Tanggal</th>
                                                <th class="text-end">Nilai Unit</th>
                                                <th class="text-center">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($confirmedOffers ?? [] as $offer)
                                                @php
                                                    $itemVal = $offer->details->where('id_fixed_asset', $fixed->id)->sum('amount');
                                                @endphp
                                                <tr>
                                                    <td>
                                                        <div class="fw-bold text-primary font-monospace">{{ $offer->no_quote }}</div>
                                                        @if ($offer->po_number)
                                                            <span class="badge bg-label-success" style="font-size: 10px;">PO: {{ $offer->po_number }}</span>
                                                        @endif
                                                    </td>
                                                    <td class="small">{{ optional($offer->client)->company ?? '-' }}</td>
                                                    <td class="small text-nowrap">{{ $offer->date ? \Carbon\Carbon::parse($offer->date)->format('d M Y') : '-' }}</td>
                                                    <td class="text-end fw-bold text-dark text-nowrap">
                                                        Rp {{ number_format($itemVal, 0, ',', '.') }}
                                                    </td>
                                                    <td class="text-center">
                                                        <a href="{{ route('unit-quotation.show', $offer->id) }}" class="btn btn-xs btn-outline-primary" target="_blank" title="Buka Smart Quote">
                                                            <i class="mdi mdi-open-in-new"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="5" class="text-center text-muted py-3">
                                                        Belum ada penawaran deal (PO Received) yang menggunakan unit ini.
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <small class="text-muted d-block mt-2 pt-2 border-top" style="font-size: 0.72rem;">
                                * Nilai unit dihitung khusus untuk item unit fisik ini pada Smart Quotation.
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Row: Informasi & Spesifikasi (Left) and Pemetaan COA (Right) Side-by-Side -->
    <div class="row g-3 mb-3">
        <!-- Informasi & Spesifikasi Aset (Termasuk Data Supplier) -->
        <div class="col-lg-6 col-12 d-flex">
            <div class="card detail-card w-100 h-100 mb-0 d-flex flex-column">
                <div class="card-header border-bottom py-3 px-3 px-md-4 d-flex justify-content-between align-items-center bg-transparent">
                    <h5 class="card-title mb-0 fw-bold fs-6">
                        <i class="mdi mdi-card-bulleted-outline text-primary me-2"></i>Informasi & Spesifikasi Aset
                    </h5>
                    @if ($fixed->type === 'Bangunan')
                        <span class="text-muted small">Tipe Pengadaan: <strong class="text-dark">{{ $fixed->procurement_type ? ucfirst($fixed->procurement_type) : ($fixed->status_bangunan === 'construction' ? 'Swakelola' : 'Beli / Bangun') }}</strong></span>
                    @else
                        <span class="text-muted small">No. Invoice: <strong class="text-dark">{{ $fixed->no_invoice ?: '-' }}</strong></span>
                    @endif
                </div>
                <div class="card-body p-3 p-md-4 d-flex flex-column justify-content-between flex-grow-1">
                    <div class="d-flex flex-column gap-3">
                        <div>
                            <div class="detail-label">Keterangan / Nama Aset</div>
                            <h5 class="fw-bold text-dark mb-0">{{ $fixed->desc ?: '-' }}</h5>
                        </div>

                        <!-- Spesifikasi Kategori Spesifik -->
                        @if ($fixed->type === 'Kendaraan')
                            <div class="info-spec-box">
                                <h6 class="fw-bold text-primary mb-2.5 fs-6"><i class="mdi mdi-car me-1"></i>Spesifikasi Armada Kendaraan</h6>
                                <div class="row g-2.5">
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
                            <div class="info-spec-box">
                                <h6 class="fw-bold text-primary mb-2.5 fs-6"><i class="mdi mdi-cog-outline me-1"></i>Identitas Unit Mesin (E-Stock)</h6>
                                <div class="row g-2.5">
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
                            <div class="info-spec-box">
                                <h6 class="fw-bold text-primary mb-2.5 fs-6"><i class="mdi mdi-tools me-1"></i>Data Operasional Tools</h6>
                                <div class="row g-2.5">
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

                        <!-- Informasi Spesifik Bangunan -->
                        @if ($fixed->type === 'Bangunan')
                            <div class="info-spec-box mb-3">
                                <div class="d-flex align-items-center justify-content-between mb-2 pb-2 border-bottom">
                                    <h6 class="fw-bold text-dark mb-0 fs-6">
                                        <i class="mdi mdi-office-building text-primary me-1"></i>Spesifikasi Properti & Bangunan
                                    </h6>
                                    <div>
                                        @if ($fixed->status_bangunan === 'construction')
                                            <span class="badge bg-warning text-dark"><i class="mdi mdi-hammer-wrench me-1"></i>Proses Konstruksi</span>
                                        @else
                                            <span class="badge bg-success"><i class="mdi mdi-check-circle-outline me-1"></i>Siap Digunakan</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="row g-2.5">
                                    <div class="col-12">
                                        <div class="detail-label">Alamat / Lokasi Bangunan</div>
                                        <div class="detail-value fw-semibold text-dark">
                                            <i class="mdi mdi-map-marker text-danger me-1"></i>{{ $fixed->lokasi_bangunan ?: 'Belum Diisi' }}
                                        </div>
                                    </div>
                                    <div class="col-sm-4 col-6">
                                        <div class="detail-label">Luas Bangunan</div>
                                        <div class="detail-value text-dark">
                                            {{ $fixed->luas_bangunan ? number_format($fixed->luas_bangunan, 0, ',', '.') . ' m²' : '-' }}
                                        </div>
                                    </div>
                                    <div class="col-sm-4 col-6">
                                        <div class="detail-label">Luas Tanah</div>
                                        <div class="detail-value text-dark">
                                            {{ $fixed->luas_tanah ? number_format($fixed->luas_tanah, 0, ',', '.') . ' m²' : '-' }}
                                        </div>
                                    </div>
                                    <div class="col-sm-4 col-12">
                                        <div class="detail-label">Legalitas / PBG / IMB</div>
                                        <div class="detail-value text-dark font-monospace">
                                            {{ $fixed->nomor_dokumen_legalitas ?: '-' }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Tanggal-tanggal penting -->
                        <div class="row g-2.5 pt-2 border-top">
                            @if ($fixed->type === 'Bangunan' && $fixed->status_bangunan === 'construction')
                                <div class="col-4">
                                    <div class="detail-label">Mulai Konstruksi</div>
                                    <div class="detail-value">
                                        {{ $fixed->beli ? \Carbon\Carbon::parse($fixed->beli)->format('d M Y') : ($fixed->date ? \Carbon\Carbon::parse($fixed->date)->format('d M Y') : '-') }}
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="detail-label">Mulai Pakai</div>
                                    <div class="detail-value">
                                        <span class="badge bg-label-warning"><i class="mdi mdi-clock-outline me-1"></i>Menunggu Selesai</span>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="detail-label">Mulai Susut</div>
                                    <div class="detail-value">
                                        <span class="badge bg-label-secondary" title="Penyusutan baru akan aktif saat tombol Selesai Pembangunan & Aktifkan Aset diklik">
                                            <i class="mdi mdi-pause-circle-outline me-1"></i>Belum Aktif (Konstruksi)
                                        </span>
                                    </div>
                                </div>
                            @else
                                <div class="col-6 col-md-3">
                                    <div class="detail-label">{{ $fixed->type === 'Bangunan' ? 'Mulai Konstruksi / Beli' : 'Tanggal Beli' }}</div>
                                    <div class="detail-value">
                                        {{ $fixed->beli ? \Carbon\Carbon::parse($fixed->beli)->format('d M Y') : ($fixed->date ? \Carbon\Carbon::parse($fixed->date)->format('d M Y') : '-') }}
                                    </div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="detail-label">Mulai Pakai</div>
                                    <div class="detail-value">
                                        {{ $fixed->pakai ? \Carbon\Carbon::parse($fixed->pakai)->format('d M Y') : '-' }}
                                    </div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="detail-label">Mulai Susut</div>
                                    <div class="detail-value">
                                        @if ($fixed->mulai_penyusutan)
                                            <span class="fw-semibold text-dark">{{ \Carbon\Carbon::parse($fixed->mulai_penyusutan)->format('d M Y') }}</span>
                                        @else
                                            {{ $fixed->beli ? \Carbon\Carbon::parse($fixed->beli)->format('d M Y') : '-' }}
                                        @endif
                                    </div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="detail-label">Tanggal Bayar</div>
                                    <div class="detail-value">
                                        {{ $fixed->bayar ? \Carbon\Carbon::parse($fixed->bayar)->format('d M Y') : '-' }}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    @if ($fixed->type !== 'Bangunan')
                        <!-- Informasi Supplier Box -->
                        <div class="supplier-box mt-3">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="mdi mdi-truck-delivery-outline text-primary fs-5"></i>
                                    <span class="fw-bold text-dark">{{ $fixed->supplier?->supplier ?? 'Belum Ada Supplier' }}</span>
                                    @if ($fixed->supplier)
                                        <span class="badge bg-label-primary">Supplier</span>
                                    @endif
                                </div>
                                <button type="button" class="btn btn-xs btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalEditSupplier">
                                    <i class="mdi mdi-pencil-outline me-1"></i> {{ $fixed->supplier ? 'Ubah Supplier' : 'Pilih Supplier' }}
                                </button>
                            </div>
                            @if ($fixed->supplier)
                                <div class="row g-2 small text-muted">
                                    @if ($fixed->supplier->address)
                                        <div class="col-12"><i class="mdi mdi-map-marker-outline me-1"></i>{{ $fixed->supplier->address }}</div>
                                    @endif
                                    @if ($fixed->supplier->phone)
                                        <div class="col-sm-6"><i class="mdi mdi-phone-outline me-1"></i>{{ $fixed->supplier->phone }}</div>
                                    @endif
                                    @if ($fixed->supplier->info)
                                        <div class="col-12 mt-1"><em>{{ $fixed->supplier->info }}</em></div>
                                    @endif
                                </div>
                            @else
                                <p class="text-muted small mb-0">Belum ada data supplier yang dipilih untuk aset tetap ini.</p>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Pemetaan Akun Akuntansi (COA) -->
        <div class="col-lg-6 col-12 d-flex">
            <div class="card detail-card w-100 h-100 mb-0 d-flex flex-column">
                <div class="card-header border-bottom py-3 px-3 px-md-4 bg-transparent">
                    <h5 class="card-title mb-0 fw-bold fs-6">
                        <i class="mdi mdi-book-open-page-variant-outline text-primary me-2"></i>Pemetaan Akun Akuntansi (COA)
                    </h5>
                </div>
                <div class="card-body p-3 p-md-4 d-flex flex-column justify-content-between flex-grow-1">
                    <div class="row g-3">
                        <div class="col-12 col-sm-6">
                            <div class="coa-box h-100">
                                <div class="detail-label text-primary">Akun Aktiva (Aset Tetap)</div>
                                <div class="fw-semibold text-dark">
                                    {{ $fixed->aktiva ? $fixed->aktiva->code . ' - ' . $fixed->aktiva->name : 'Belum Ditentukan' }}
                                </div>
                                <small class="text-muted">{{ $fixed->aktiva?->category ?: '-' }}</small>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6">
                            <div class="coa-box h-100" style="border-left-color: #ff3e1d;">
                                <div class="detail-label text-danger">Akun Akumulasi Penyusutan</div>
                                <div class="fw-semibold text-dark">
                                    {{ $fixed->penyusutan ? $fixed->penyusutan->code . ' - ' . $fixed->penyusutan->name : 'Belum Ditentukan' }}
                                </div>
                                <small class="text-muted">{{ $fixed->penyusutan?->category ?: '-' }}</small>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6">
                            <div class="coa-box h-100" style="border-left-color: #ffab00;">
                                <div class="detail-label text-warning">Akun Beban Penyusutan</div>
                                <div class="fw-semibold text-dark">
                                    {{ $fixed->beban ? $fixed->beban->code . ' - ' . $fixed->beban->name : 'Belum Ditentukan' }}
                                </div>
                                <small class="text-muted">{{ $fixed->beban?->category ?: '-' }}</small>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6">
                            <div class="coa-box h-100" style="border-left-color: #03c3ec;">
                                <div class="detail-label text-info">Akun Kas / Bank Pengeluaran</div>
                                <div class="fw-semibold text-dark">
                                    {{ $fixed->pengeluaran ? $fixed->pengeluaran->code . ' - ' . $fixed->pengeluaran->name : 'Belum Ditentukan' }}
                                </div>
                                <small class="text-muted">{{ $fixed->pengeluaran?->category ?: '-' }}</small>
                            </div>
                        </div>
                    </div>

                    @if ($fixed->is_disposed)
                        <div class="card border-danger mt-3 mb-0">
                            <div class="card-header bg-label-danger py-2 px-3">
                                <h6 class="card-title mb-0 fw-bold text-danger fs-6">
                                    <i class="mdi mdi-alert-octagon-outline me-1"></i>Aset Telah Dihapus / Disposed
                                </h6>
                            </div>
                            <div class="card-body p-3">
                                <div class="row g-2 small">
                                    <div class="col-4">
                                        <span class="detail-label">Tanggal Disposal</span>
                                        <div>{{ $fixed->tanggal_disposal ? \Carbon\Carbon::parse($fixed->tanggal_disposal)->format('d M Y') : '-' }}</div>
                                    </div>
                                    <div class="col-4">
                                        <span class="detail-label">Nilai Buku Saat Disposal</span>
                                        <div class="fw-bold text-dark">Rp {{ number_format($fixed->nilai_buku_disposal ?: 0, 0, ',', '.') }}</div>
                                    </div>
                                    <div class="col-4">
                                        <span class="detail-label">Harga Jual Final</span>
                                        <div class="fw-bold text-success">Rp {{ number_format($fixed->harga_jual_final ?: 0, 0, ',', '.') }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Fullwidth Riwayat Tables (Mesin / Kendaraan) -->
    <div class="row g-3 mb-3">
        <div class="col-12">
            <!-- Card: Riwayat Servis & Work Order (Mesin) -->
            @if ($fixed->type === 'Mesin')
                <div class="card detail-card mb-0">
                    <div class="card-header border-bottom py-3 px-3 px-md-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 bg-transparent">
                        <div>
                            <h5 class="card-title mb-0 fw-bold fs-6">
                                <i class="mdi mdi-wrench-outline text-primary me-2"></i>Riwayat Servis & Work Order
                            </h5>
                            <small class="text-muted">Riwayat perbaikan internal, pengeluaran suku cadang, dan Surat Jalan untuk mesin ini.</small>
                        </div>
                        <a href="{{ route('work-orders.create') }}" class="btn btn-sm btn-primary">
                            <i class="mdi mdi-plus me-1"></i> Buat Work Order
                        </a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-custom table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>No. WO</th>
                                    <th>Tanggal</th>
                                    <th>Pemohon</th>
                                    <th>Parts</th>
                                    <th class="text-end">Total Biaya</th>
                                    <th class="text-center">Perlakuan</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($workOrders ?? [] as $woItem)
                                    @php $woBadge = $woItem->status_badge; @endphp
                                    <tr>
                                        <td>
                                            <a href="{{ route('work-orders.show', $woItem->id) }}" class="fw-bold text-primary font-monospace">
                                                {{ $woItem->no_wo }}
                                            </a>
                                        </td>
                                        <td class="small text-nowrap">{{ $woItem->date ? \Carbon\Carbon::parse($woItem->date)->format('d M Y') : '-' }}</td>
                                        <td class="small">{{ $woItem->creator->name ?? '-' }}</td>
                                        <td>
                                            @if($woItem->items->isNotEmpty())
                                                <div class="d-flex flex-column gap-1">
                                                    @foreach($woItem->items as $wItem)
                                                        @php
                                                            $dp = $wItem->detailProduct;
                                                            $p = $wItem->product ?? $dp?->product;
                                                            $ser = $p?->serial?->first();
                                                            $brand = $ser?->brand ?? ($p?->brand ?? '');
                                                            $pn = $ser?->pn ?? ($p?->pn ?? ($dp?->replacement ?: ''));
                                                            $desc = $p?->detail_desc ?: ($p?->description ?: ($p?->commodity ?: ''));
                                                            $goRaw = $p?->go ?: 'Genuine';
                                                            $goCode = strtoupper(substr(trim($goRaw), 0, 1));
                                                            $qty = (float)($wItem->qty_issued ?: ($wItem->qty_approved ?: $wItem->qty_requested));
                                                        @endphp
                                                        <div class="d-flex flex-wrap align-items-center gap-1">
                                                            <span class="badge {{ $goCode === 'R' ? 'bg-label-info' : 'bg-label-warning text-dark' }} fw-bold" style="font-size: 10px; padding: 2px 5px;">
                                                                {{ $goCode }}
                                                            </span>
                                                            @if($pn)
                                                                <span class="font-monospace fw-bold text-dark" style="font-size: 0.82rem;">
                                                                    {{ $brand ? $brand . ' ' : '' }}{{ $pn }}
                                                                </span>
                                                            @endif
                                                            <span class="text-secondary small">
                                                                {{ $desc ?: ($wItem->item_name ?: 'Part') }}
                                                            </span>
                                                            <span class="badge bg-label-secondary rounded-pill" style="font-size: 10px;">
                                                                {{ $qty }} {{ $wItem->unit }}
                                                            </span>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @else
                                                <span class="text-muted small">-</span>
                                            @endif
                                        </td>
                                        <td class="text-end fw-bold text-dark text-nowrap">
                                            Rp {{ number_format($woItem->total_cost, 0, ',', '.') }}
                                        </td>
                                        <td class="text-center">
                                            @if ($woItem->accounting_treatment === 'capitalize')
                                                <span class="badge bg-label-success">Kapitalisasi</span>
                                            @elseif ($woItem->accounting_treatment === 'expense')
                                                <span class="badge bg-label-info">Beban Biaya</span>
                                            @else
                                                <span class="text-muted small">-</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <span class="badge {{ $woBadge['class'] }} d-inline-flex align-items-center gap-1">
                                                <i class="mdi {{ $woBadge['icon'] }}" style="font-size: 11px;"></i>
                                                {{ $woBadge['text'] }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('work-orders.show', $woItem->id) }}" class="btn btn-sm btn-outline-primary py-1 px-2">
                                                Detail
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4 text-muted">
                                            Belum ada riwayat perbaikan atau pemakaian spare part (Work Order) untuk mesin ini.
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
                <div class="card detail-card mb-0">
                    <div class="card-header border-bottom py-3 px-3 px-md-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 bg-transparent">
                        <h5 class="card-title mb-0 fw-bold fs-6">
                            <i class="mdi mdi-car-wrench text-primary me-2"></i>Riwayat Perawatan Kendaraan
                        </h5>
                        <a href="{{ route('fixed.maintenance.create', $fixed->id) }}" class="btn btn-sm btn-primary">
                            <i class="mdi mdi-plus me-1"></i> Tambah Perawatan
                        </a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-custom table-hover align-middle mb-0">
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

            <!-- Card: Proyek Pembangunan & Pencatatan Biaya (Bangunan) -->
            @if ($fixed->type === 'Bangunan')
                <div class="card detail-card mb-0">
                    <div class="card-header border-bottom py-3 px-3 px-md-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 bg-transparent">
                        <div>
                            <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                                <h5 class="card-title mb-0 fw-bold fs-6">
                                    <i class="mdi mdi-hammer-wrench text-primary me-2"></i>Proyek Pembangunan &amp; Realisasi Biaya
                                </h5>
                                @php
                                    $picUsers = $fixed->pic_construction_users;
                                @endphp
                                @if ($picUsers->isNotEmpty())
                                    <span class="badge bg-label-info d-inline-flex align-items-center gap-1" title="Petugas Lapangan yang Ditugaskan Input">
                                        <i class="mdi mdi-account-check-outline"></i> PIC: {{ $picUsers->pluck('name')->implode(', ') }}
                                    </span>
                                @else
                                    <span class="badge bg-label-secondary" title="Belum ada petugas khusus yang di-setting">
                                        <i class="mdi mdi-account-off-outline me-1"></i>Belum Ada PIC Khusus
                                    </span>
                                @endif
                            </div>
                            <small class="text-muted d-block">
                                Pencatatan biaya swakelola/konstruksi: <strong>Material</strong> (dikapitalisasi ke Aset Tetap) vs <strong>Jasa/Upah</strong> (dicatat ke Expense).
                            </small>
                        </div>
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#modalSettingPics" title="Atur Akun/User yang diizinkan untuk input pengeluaran proyek">
                                <i class="mdi mdi-account-cog-outline me-1"></i> Setting PIC
                            </button>
                            @if ($fixed->status_bangunan === 'construction')
                                <button type="button" class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#modalFinishConstruction" title="Selesaikan Masa Pembangunan & Mulai Aktifkan Penyusutan Aset">
                                    <i class="mdi mdi-check-decagram me-1"></i> Selesai Proyek
                                </button>
                            @endif
                            <a href="{{ route('proyek-konstruksi.index', ['building_id' => $fixed->id]) }}" class="btn btn-sm btn-primary" title="Buka Halaman Pencatatan Pengeluaran Proyek Pembangunan">
                                <i class="mdi mdi-plus me-1"></i> Catat Biaya
                            </a>
                        </div>
                    </div>

                    <!-- 3 KPI Cards for Building Project -->
                    <div class="card-body p-3 p-md-4 border-bottom bg-light">
                        <div class="row g-3">
                            <div class="col-md-4 col-12">
                                <div class="p-3 rounded border bg-white shadow-xs" style="border-left: 4px solid #6366f1 !important;">
                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                        <span class="detail-label text-primary">Total Biaya Material</span>
                                        <span class="badge bg-label-primary" style="font-size: 10px;">Kapitalisasi Aset</span>
                                    </div>
                                    <h5 class="fw-bold mb-1 text-primary">Rp {{ number_format($totalMaterialCost, 0, ',', '.') }}</h5>
                                    <small class="text-muted" style="font-size: 11px;">Masuk ke Nilai Perolehan Aktiva Tetap</small>
                                </div>
                            </div>
                            <div class="col-md-4 col-12">
                                <div class="p-3 rounded border bg-white shadow-xs" style="border-left: 4px solid #f59e0b !important;">
                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                        <span class="detail-label text-warning">Total Jasa &amp; Upah Tukang</span>
                                        <span class="badge bg-label-warning" style="font-size: 10px;">Expense / Beban</span>
                                    </div>
                                    <h5 class="fw-bold mb-1 text-warning">Rp {{ number_format($totalJasaCost, 0, ',', '.') }}</h5>
                                    <small class="text-muted" style="font-size: 11px;">Dicatat langsung ke modul Expense operasional</small>
                                </div>
                            </div>
                            <div class="col-md-4 col-12">
                                <div class="p-3 rounded border bg-white shadow-xs" style="border-left: 4px solid #10b981 !important;">
                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                        <span class="detail-label text-success">Total Realisasi Biaya Proyek</span>
                                        <span class="badge bg-label-success" style="font-size: 10px;">Grand Total</span>
                                    </div>
                                    <h5 class="fw-bold mb-1 text-success">Rp {{ number_format($totalProjectCost, 0, ',', '.') }}</h5>
                                    <small class="text-muted" style="font-size: 11px;">Akumulasi Material + Jasa/Upah Keseluruhan</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-custom table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Kategori Perlakuan</th>
                                    <th>Item / Keterangan</th>
                                    <th>Toko / Penerima</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-end">Harga Satuan</th>
                                    <th class="text-end">Total Biaya</th>
                                    <th class="text-center">Bukti Nota</th>
                                    <th>Pemetaan COA</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($constructionCosts ?? [] as $cost)
                                    <tr>
                                        <td class="small text-nowrap fw-semibold">
                                            {{ $cost->tanggal ? $cost->tanggal->format('d M Y') : '-' }}
                                            @if ($cost->creator)
                                                <div class="text-muted" style="font-size: 11px;">Oleh: {{ $cost->creator->name }}</div>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($cost->kategori_biaya === 'material')
                                                <span class="badge bg-label-primary d-inline-flex align-items-center gap-1">
                                                    <i class="mdi mdi-cube-outline"></i> Material (Aktiva)
                                                </span>
                                            @elseif ($cost->kategori_biaya === 'jasa')
                                                <span class="badge bg-label-warning d-inline-flex align-items-center gap-1">
                                                    <i class="mdi mdi-account-hard-hat"></i> Jasa/Upah (Expense)
                                                </span>
                                            @elseif ($cost->kategori_biaya === 'sewa')
                                                <span class="badge bg-label-info d-inline-flex align-items-center gap-1">
                                                    <i class="mdi mdi-cog-outline"></i> Sewa Alat (Expense)
                                                </span>
                                            @else
                                                <span class="badge bg-label-secondary d-inline-flex align-items-center gap-1">
                                                    <i class="mdi mdi-cash-multiple"></i> Operasional
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="fw-bold text-dark">{{ $cost->nama_item }}</div>
                                            <div class="d-flex flex-wrap gap-1 align-items-center mt-1">
                                                @if ($cost->purchase_order_id)
                                                    <a href="{{ route('purchase.show', $cost->purchase_order_id) }}" target="_blank" class="badge bg-label-info text-decoration-none d-inline-flex align-items-center gap-1" title="Buka Detail Purchase Order">
                                                        <i class="mdi mdi-link-variant"></i> PO: {{ $cost->purchaseOrder?->no_po ?: ($cost->no_bukti ?: 'Lihat PO') }}
                                                    </a>
                                                @elseif ($cost->no_bukti)
                                                    <span class="badge bg-label-secondary font-monospace" style="font-size: 10.5px;">Ref: {{ $cost->no_bukti }}</span>
                                                @endif
                                            </div>
                                            @if ($cost->catatan)
                                                <div class="small text-muted fst-italic mt-0.5">{{ $cost->catatan }}</div>
                                            @endif
                                        </td>
                                        <td class="small">
                                            {{ $cost->supplier?->supplier ?: ($cost->payee ?: '-') }}
                                        </td>
                                        <td class="text-center small">
                                            {{ $cost->qty }} {{ $cost->satuan ?: '' }}
                                        </td>
                                        <td class="text-end small">
                                            Rp {{ number_format($cost->harga_satuan, 0, ',', '.') }}
                                        </td>
                                        <td class="text-end fw-bold {{ $cost->kategori_biaya === 'material' ? 'text-primary' : 'text-warning' }} text-nowrap">
                                            Rp {{ number_format($cost->total_biaya, 0, ',', '.') }}
                                        </td>
                                        <td class="text-center">
                                            @if ($cost->foto_bukti)
                                                <a href="{{ asset($cost->foto_bukti) }}" target="_blank" class="btn btn-xs btn-label-secondary">
                                                    <i class="mdi mdi-file-document-outline me-1"></i> Lihat
                                                </a>
                                            @else
                                                <span class="text-muted small">-</span>
                                            @endif
                                        </td>
                                        <td class="small">
                                            @if ($cost->pengeluaran)
                                                <div class="d-flex flex-column gap-0.5">
                                                    <span class="badge bg-label-success" style="font-size: 10px;" title="Akun Kas / Bank">
                                                        <i class="mdi mdi-bank-outline me-1"></i>{{ $cost->pengeluaran->code }}
                                                    </span>
                                                    @if ($cost->beban)
                                                        <span class="badge bg-label-info" style="font-size: 10px;" title="Akun Beban">
                                                            <i class="mdi mdi-book-outline me-1"></i>{{ $cost->beban->code }}
                                                        </span>
                                                    @endif
                                                </div>
                                            @else
                                                <button type="button" class="btn btn-xs btn-label-warning btn-map-coa" 
                                                    data-id="{{ $cost->id }}"
                                                    data-item="{{ $cost->nama_item }}"
                                                    data-total="Rp {{ number_format($cost->total_biaya, 0, ',', '.') }}"
                                                    data-cat="{{ $cost->kategori_biaya }}"
                                                    data-pengeluaran="{{ $cost->id_pengeluaran }}"
                                                    data-beban="{{ $cost->id_beban }}"
                                                    data-nobukti="{{ $cost->no_bukti }}"
                                                    data-bs-toggle="modal" data-bs-target="#modalDetailMapCoa">
                                                    <i class="mdi mdi-plus-box-outline me-1"></i> Petakan COA
                                                </button>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex align-items-center justify-content-center gap-1">
                                                <button type="button" class="btn btn-xs btn-outline-primary btn-map-coa" 
                                                    data-id="{{ $cost->id }}"
                                                    data-item="{{ $cost->nama_item }}"
                                                    data-total="Rp {{ number_format($cost->total_biaya, 0, ',', '.') }}"
                                                    data-cat="{{ $cost->kategori_biaya }}"
                                                    data-pengeluaran="{{ $cost->id_pengeluaran }}"
                                                    data-beban="{{ $cost->id_beban }}"
                                                    data-nobukti="{{ $cost->no_bukti }}"
                                                    data-bs-toggle="modal" data-bs-target="#modalDetailMapCoa"
                                                    title="Edit Pemetaan Akun">
                                                    <i class="mdi mdi-book-edit-outline"></i>
                                                </button>
                                                <form action="{{ route('fixed.construction-cost.destroy', ['id' => $fixed->id, 'costId' => $cost->id]) }}" method="POST" class="d-inline form-delete-cost">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-xs btn-outline-danger btn-delete-cost" title="Hapus Biaya">
                                                        <i class="mdi mdi-trash-can-outline"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center py-4 text-muted">
                                            <i class="mdi mdi-clipboard-text-outline fs-3 d-block mb-1 text-secondary"></i>
                                            Belum ada pencatatan biaya material maupun jasa pembangunan untuk gedung ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Modal Catat Biaya Pembangunan (Material vs Jasa) -->
    @if ($fixed->type === 'Bangunan')
        <div class="modal fade" id="modalAddConstructionCost" tabindex="-1" aria-labelledby="modalAddConstructionCostLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <form action="{{ route('fixed.construction-cost.store', $fixed->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-header border-bottom py-3">
                            <h5 class="modal-title fw-bold" id="modalAddConstructionCostLabel">
                                <i class="mdi mdi-plus-circle-outline text-primary me-2"></i>Catat Biaya Pembangunan - {{ $fixed->desc }}
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body py-3">
                            <div class="row g-3">
                                <!-- Tanggal -->
                                <div class="col-md-6 col-12">
                                    <label class="form-label fw-semibold">Tanggal Transaksi <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" name="tanggal" value="{{ date('Y-m-d') }}" required>
                                </div>

                                <!-- Kategori Biaya -->
                                <div class="col-md-6 col-12">
                                    <label class="form-label fw-semibold">Kategori Biaya <span class="text-danger">*</span></label>
                                    <select class="form-select" name="kategori_biaya" id="cost_kategori_biaya" required>
                                        <option value="material">🏗️ Material Fisik (Kapitalisasi ke Aset Tetap)</option>
                                        <option value="jasa">👷 Jasa / Upah Tukang / Mandor (Dicatat ke Expense)</option>
                                        <option value="operasional">⚙️ Sewa Alat / Perizinan / Operasional Proyek (Expense)</option>
                                    </select>
                                    <small class="text-muted" id="kategori_biaya_notice">
                                        Material akan otomatis menambah Nilai Perolehan Aset Bangunan.
                                    </small>
                                </div>

                                <!-- Nama Item / Uraian -->
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Nama Item / Uraian Pengeluaran <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="nama_item" placeholder="Contoh: Semen Tiga Roda 50 Sak / Upah Tukang Minggu ke-3 / Baja Ringan CNP" required>
                                </div>

                                <!-- Supplier / Toko Bangunan -->
                                <div class="col-md-6 col-12">
                                    <label class="form-label fw-semibold">Supplier / Toko Bangunan (Master)</label>
                                    <select name="supplier_id" class="form-select select2-cost-supplier">
                                        <option value="">-- Pilih Supplier Master (Opsional) --</option>
                                        @foreach ($suppliers ?? [] as $supp)
                                            <option value="{{ $supp->id }}">{{ $supp->supplier }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Penerima / Toko Bebas -->
                                <div class="col-md-6 col-12">
                                    <label class="form-label fw-semibold">Nama Toko / Penerima Upah (Payee)</label>
                                    <input type="text" class="form-control" name="payee" placeholder="Contoh: TB Makmur Abadi / Mandor Pak Joko">
                                </div>

                                <!-- Qty & Satuan -->
                                <div class="col-md-4 col-6">
                                    <label class="form-label fw-semibold">Qty</label>
                                    <input type="number" step="0.01" class="form-control" name="qty" id="cost_qty" value="1">
                                </div>
                                <div class="col-md-4 col-6">
                                    <label class="form-label fw-semibold">Satuan</label>
                                    <input type="text" class="form-control" name="satuan" placeholder="Contoh: sak, btg, m2, hari, ls">
                                </div>

                                <!-- Total Biaya -->
                                <div class="col-md-4 col-12">
                                    <label class="form-label fw-semibold">Total Biaya (Rp) <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control fw-bold text-primary currency-input" name="total_biaya" id="cost_total_biaya" placeholder="0" required>
                                </div>

                                <!-- Akun Kas / Bank -->
                                <div class="col-md-6 col-12">
                                    <label class="form-label fw-semibold">Sumber Dana (Akun Kas / Bank)</label>
                                    <select name="id_pengeluaran" class="form-select select2-cost-account">
                                        <option value="">-- Pilih Akun Kas/Bank --</option>
                                        @foreach ($accounts ?? [] as $acc)
                                            <option value="{{ $acc->id }}" {{ $fixed->id_pengeluaran == $acc->id ? 'selected' : '' }}>
                                                {{ $acc->code }} - {{ $acc->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Akun Beban (Untuk Jasa / Expense) -->
                                <div class="col-md-6 col-12" id="cost_beban_wrapper" style="display: none;">
                                    <label class="form-label fw-semibold">Akun Beban (Laba Rugi)</label>
                                    <select name="id_beban" class="form-select select2-cost-account">
                                        <option value="">-- Pilih Akun Beban --</option>
                                        @foreach ($accounts ?? [] as $acc)
                                            <option value="{{ $acc->id }}" {{ $fixed->id_beban == $acc->id ? 'selected' : '' }}>
                                                {{ $acc->code }} - {{ $acc->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- No. Bukti & Foto Bukti Nota -->
                                <div class="col-md-6 col-12">
                                    <label class="form-label fw-semibold">No. Nota / Kwitansi / Struk</label>
                                    <input type="text" class="form-control font-monospace" name="no_bukti" placeholder="Contoh: NOTA/2026/09/101">
                                </div>
                                <div class="col-md-6 col-12">
                                    <label class="form-label fw-semibold">Upload Foto Struk / Nota Fisik</label>
                                    <input type="file" class="form-control" name="foto_bukti" accept="image/*,application/pdf">
                                    <small class="text-muted">JPG, PNG, atau PDF (Maks. 5 MB).</small>
                                </div>

                                <!-- Catatan -->
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Catatan Tambahan</label>
                                    <textarea class="form-control" rows="2" name="catatan" placeholder="Catatan spesifikasi material / keterangan pekerjaan..."></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-top py-2">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">
                                <i class="mdi mdi-content-save-outline me-1"></i> Simpan Biaya
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal Selesai Pembangunan (Handover & Aktivasi Penyusutan) -->
        <div class="modal fade" id="modalFinishConstruction" tabindex="-1" aria-labelledby="modalFinishConstructionLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form action="{{ route('fixed.finish-construction', $fixed->id) }}" method="POST">
                        @csrf
                        <div class="modal-header border-bottom py-3">
                            <h5 class="modal-title fw-bold" id="modalFinishConstructionLabel">
                                <i class="mdi mdi-check-decagram text-success me-2"></i>Selesai Pembangunan &amp; Aktifkan Aset
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body py-4">
                            <div class="alert alert-success d-flex align-items-start mb-3">
                                <i class="mdi mdi-office-building-check fs-4 me-2 mt-0.5"></i>
                                <div class="small">
                                    <strong>Konfirmasi Serah Terima Bangunan:</strong>
                                    <div class="mt-1">
                                        Bangunan <strong>{{ $fixed->desc }}</strong> akan diubah statusnya menjadi <strong>Siap Operasional</strong>. Total akumulasi belanja material sebesar <strong>Rp {{ number_format($fixed->total, 0, ',', '.') }}</strong> akan dikunci sebagai Nilai Perolehan Aktiva Tetap dan jadwal penyusutan (20 tahun) mulai dihitung.
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold text-dark">Tanggal Serah Terima / Mulai Operasional <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="tanggal_selesai" value="{{ date('Y-m-d') }}" required>
                                <small class="text-muted">Tanggal ini akan menjadi patokan awal perhitungan depresiasi bangunan.</small>
                            </div>
                        </div>
                        <div class="modal-footer border-top py-2">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-success">
                                <i class="mdi mdi-check-bold me-1"></i> Ya, Aktifkan Aset Sekarang
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- Modal Setting PIC / Akun Penginput Pengeluaran Proyek -->
        <div class="modal fade" id="modalSettingPics" tabindex="-1" aria-labelledby="modalSettingPicsLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form action="{{ route('fixed.update-construction-pics', $fixed->id) }}" method="POST">
                        @csrf
                        <div class="modal-header border-bottom py-3">
                            <h5 class="modal-title fw-bold" id="modalSettingPicsLabel">
                                <i class="mdi mdi-account-cog-outline text-primary me-2"></i>Setting Akun Penginput Biaya Proyek
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body py-4">
                            <div class="alert alert-info py-2 px-3 mb-3 small d-flex align-items-start gap-2">
                                <i class="mdi mdi-information-outline fs-5 mt-0.5"></i>
                                <div>
                                    Pilih akun user/staff (misal: Teknisi Lapangan, GA, Logistik, Mandor) yang diberikan akses untuk menginput pengeluaran harian pembangunan gedung <strong>{{ $fixed->desc }}</strong>.
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <label class="form-label fw-semibold text-dark mb-0">Pilih Akun User / Staff Lapangan (PIC)</label>
                                    <div class="d-flex gap-1">
                                        <button type="button" class="btn btn-xs btn-label-primary" id="btn-select-all-pics">
                                            <i class="mdi mdi-checkbox-multiple-marked-outline me-1"></i>Pilih Semua User
                                        </button>
                                        <button type="button" class="btn btn-xs btn-label-secondary" id="btn-deselect-all-pics">
                                            <i class="mdi mdi-close me-1"></i>Hapus Pilihan
                                        </button>
                                    </div>
                                </div>
                                <select name="pic_construction_ids[]" id="pic_construction_select" class="form-select select2-pics" multiple="multiple" data-placeholder="Ketik nama atau pilih akun user...">
                                    @php
                                        $selectedPics = $fixed->pic_construction_ids ?? [];
                                    @endphp
                                    @foreach ($allUsers ?? [] as $user)
                                        <option value="{{ $user->id }}" {{ in_array($user->id, $selectedPics) ? 'selected' : '' }}>
                                            {{ $user->name }} ({{ $user->role }}) - {{ $user->email }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted d-block mt-1">Total <strong>{{ count($allUsers ?? []) }} user</strong> terdaftar. Anda bisa memilih satu, beberapa, atau seluruh user menggunakan tombol di atas.</small>
                            </div>
                        </div>
                        <div class="modal-footer border-top py-2">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">
                                <i class="mdi mdi-content-save-outline me-1"></i> Simpan Pengaturan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Modal Ubah Supplier -->
    <div class="modal fade" id="modalEditSupplier" tabindex="-1" aria-labelledby="modalEditSupplierLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('fixed.update-supplier', $fixed->id) }}" method="POST">
                    @csrf
                    <div class="modal-header border-bottom py-3">
                        <h5 class="modal-title fw-bold" id="modalEditSupplierLabel">
                            <i class="mdi mdi-truck-delivery-outline text-primary me-2"></i>Pilih / Ubah Supplier Aset
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body py-4">
                        <div class="mb-3">
                            <label for="id_supplier_select" class="form-label fw-semibold text-dark">Supplier / Vendor</label>
                            <select name="id_supplier" id="id_supplier_select" class="form-select select2-supplier" data-placeholder="-- Pilih Supplier --">
                                <option value="">-- Tanpa Supplier (Kosongkan) --</option>
                                @foreach ($suppliers ?? [] as $supp)
                                    <option value="{{ $supp->id }}" {{ $fixed->id_supplier == $supp->id ? 'selected' : '' }}>
                                        {{ $supp->supplier }} {{ $supp->phone ? '('.$supp->phone.')' : '' }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted d-block mt-1">Pilih supplier tempat aset ini dibeli atau diperoleh.</small>
                        </div>
                    </div>
                    <div class="modal-footer border-top py-2">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="mdi mdi-content-save-outline me-1"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Modal Petakan COA (Finance / Accounting / Admin) -->
    <div class="modal fade" id="modalDetailMapCoa" tabindex="-1" aria-labelledby="modalDetailMapCoaLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="formDetailMapCoa" method="POST">
                    @csrf
                    <div class="modal-header border-bottom py-3">
                        <h5 class="modal-title fw-bold" id="modalDetailMapCoaLabel">
                            <i class="mdi mdi-book-open-page-variant-outline text-primary me-2"></i>Pemetaan Akun Akuntansi (COA)
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body py-3">
                        <div class="p-2.5 rounded bg-light mb-3">
                            <div class="small text-muted">Item Pengeluaran:</div>
                            <div class="fw-bold text-dark fs-6" id="detail_map_item_title">-</div>
                            <div class="d-flex justify-content-between align-items-center mt-1">
                                <span class="badge bg-label-primary" id="detail_map_item_cat">-</span>
                                <span class="fw-bold text-primary" id="detail_map_item_total">Rp 0</span>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Sumber Dana (Akun Kas / Bank) <span class="text-danger">*</span></label>
                            <select name="id_pengeluaran" id="detail_map_id_pengeluaran" class="form-select select2-detail-map-coa" required>
                                <option value="">-- Pilih Akun Kas/Bank --</option>
                                @foreach ($accounts as $acc)
                                    <option value="{{ $acc->id }}">{{ $acc->code }} - {{ $acc->name }} ({{ $acc->category }})</option>
                                @endforeach
                            </select>
                            <small class="text-muted">Akun kas/bank yang berkurang untuk pembayaran ini.</small>
                        </div>

                        <div class="mb-3" id="detail_map_beban_wrapper">
                            <label class="form-label fw-semibold">Akun Beban (Laba Rugi)</label>
                            <select name="id_beban" id="detail_map_id_beban" class="form-select select2-detail-map-coa">
                                <option value="">-- Pilih Akun Beban --</option>
                                @foreach ($accounts as $acc)
                                    <option value="{{ $acc->id }}">{{ $acc->code }} - {{ $acc->name }} ({{ $acc->category }})</option>
                                @endforeach
                            </select>
                            <small class="text-muted">Untuk biaya Jasa / Sewa / Operasional (dicatat ke laporan beban laba rugi).</small>
                        </div>

                        <div class="mb-2">
                            <label class="form-label fw-semibold">No. Bukti / Ref Voucher Finance</label>
                            <input type="text" class="form-control font-monospace" name="no_bukti" id="detail_map_no_bukti" placeholder="Contoh: BKK-2026/09/012">
                        </div>
                    </div>

                    <div class="modal-footer border-top py-2">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="mdi mdi-check-bold me-1"></i> Simpan Pemetaan COA
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('after-script')
    <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('#modalDetailMapCoa').on('shown.bs.modal', function () {
                $('.select2-detail-map-coa').select2({
                    dropdownParent: $('#modalDetailMapCoa'),
                    width: '100%'
                });
            });

            $('#modalSettingPics').on('shown.bs.modal', function () {
                $('.select2-pics').select2({
                    dropdownParent: $('#modalSettingPics'),
                    width: '100%'
                });
            });

            $('#btn-select-all-pics').on('click', function () {
                $('#pic_construction_select > option').prop('selected', true);
                $('#pic_construction_select').trigger('change');
            });

            $('#btn-deselect-all-pics').on('click', function () {
                $('#pic_construction_select').val(null).trigger('change');
            });

            $('.btn-map-coa').on('click', function() {
                var id = $(this).data('id');
                var url = '{{ url("proyek-konstruksi") }}/' + id + '/map-coa';
                $('#formDetailMapCoa').attr('action', url);

                $('#detail_map_item_title').text($(this).data('item'));
                $('#detail_map_item_total').text($(this).data('total'));
                $('#detail_map_item_cat').text(($(this).data('cat') || '').toUpperCase());
                $('#detail_map_id_pengeluaran').val($(this).data('pengeluaran')).trigger('change');
                $('#detail_map_id_beban').val($(this).data('beban')).trigger('change');
                $('#detail_map_no_bukti').val($(this).data('nobukti'));
            });

            $('#modalEditSupplier').on('shown.bs.modal', function () {
                $('.select2-supplier').select2({
                    dropdownParent: $('#modalEditSupplier'),
                    width: '100%'
                });
            });

            $('#modalAddConstructionCost').on('shown.bs.modal', function () {
                $('.select2-cost-supplier, .select2-cost-account').select2({
                    dropdownParent: $('#modalAddConstructionCost'),
                    width: '100%'
                });
            });

            function formatNumber(n) {
                return n.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            }

            $('.currency-input').on('input keyup change', function() {
                var val = $(this).val();
                var cleaned = val.replace(/\D/g, "");
                $(this).val(formatNumber(cleaned));
            });

            $('#cost_kategori_biaya').on('change', function() {
                var cat = $(this).val();
                if (cat === 'material') {
                    $('#cost_beban_wrapper').slideUp();
                    $('#kategori_biaya_notice').text('Material akan otomatis menambah Nilai Perolehan Aset Bangunan.');
                } else if (cat === 'jasa') {
                    $('#cost_beban_wrapper').slideDown();
                    $('#kategori_biaya_notice').text('Jasa/Upah akan otomatis dicatat sebagai transaksi di modul Expense (Laporan Beban).');
                } else {
                    $('#cost_beban_wrapper').slideDown();
                    $('#kategori_biaya_notice').text('Operasional proyek akan dicatat sebagai transaksi di modul Expense.');
                }
            });

            $('.btn-delete-cost').on('click', function(e) {
                e.preventDefault();
                var form = $(this).closest('form');
                Swal.fire({
                    title: "Hapus Catatan Biaya?",
                    text: "Biaya ini akan dihapus dari riwayat proyek. Jika kategori material, nilai perolehan aktiva akan dikurangi.",
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
                        form.submit();
                    }
                });
            });
        });

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

