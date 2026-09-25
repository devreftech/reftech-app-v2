@extends('layouts.sales.app')
@section('title', 'Detail Unit Acquisition - ' . $fixed->code)

@section('content')
    @php
        $statusUnitBadges = [
            'OK' => ['class' => 'bg-label-success', 'icon' => 'mdi-check-circle-outline', 'text' => 'Ready / OK'],
            'Rental' => ['class' => 'bg-label-primary', 'icon' => 'mdi-truck-delivery-outline', 'text' => 'Sedang Rental'],
            'Service' => ['class' => 'bg-label-warning', 'icon' => 'mdi-wrench-clock-outline', 'text' => 'Sedang Service'],
            'Breakdown' => ['class' => 'bg-label-danger', 'icon' => 'mdi-alert-octagon-outline', 'text' => 'Breakdown'],
            'Reserved' => ['class' => 'bg-label-info', 'icon' => 'mdi-bookmark-outline', 'text' => 'Reserved'],
            'Sold' => ['class' => 'bg-label-dark', 'icon' => 'mdi-cash-check', 'text' => 'Terjual (Sold)'],
        ];

        $currentStatusBadge = $statusUnitBadges[$fixed->status_unit] ?? [
            'class' => 'bg-label-secondary',
            'icon' => 'mdi-help-circle-outline',
            'text' => $fixed->status_unit ?: 'Tidak Diketahui',
        ];

        $isCompressor = $fixed->unit && in_array($fixed->unit->unit, ['PISTON COMPRESSOR', 'AIR COMPRESSOR SCREW']);
        $isDryer = $fixed->unit && in_array($fixed->unit->unit, ['REFRIGERANT AIR DRYER', 'DESICANT DRYER']);
    @endphp

    {{-- ── Hero Header ───────────────────────────────────────────── --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
                <div class="d-flex align-items-start gap-3">
                    <div class="avatar avatar-lg rounded bg-label-primary p-2 d-flex align-items-center justify-content-center flex-shrink-0">
                        <i class="mdi mdi-cube-outline fs-2"></i>
                    </div>
                    <div>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb breadcrumb-style1 mb-1">
                                <li class="breadcrumb-item"><a href="{{ route('unit-acquisition.index') }}">Unit Acquisition</a></li>
                                <li class="breadcrumb-item active">{{ $fixed->code }}</li>
                            </ol>
                        </nav>
                        <h4 class="fw-bold mb-1 d-flex align-items-center flex-wrap gap-2">
                            <span>{{ $fixed->code }}</span>
                            @if ($fixed->kondisi)
                                <span class="badge {{ $fixed->kondisi === 'Baru' ? 'bg-label-success' : 'bg-label-info' }} rounded-pill fs-7">
                                    <i class="mdi {{ $fixed->kondisi === 'Baru' ? 'mdi-sparkles' : 'mdi-sync' }} me-1"></i>
                                    {{ $fixed->kondisi === 'Baru' ? 'Unit Baru' : 'Unit Second' }}
                                </span>
                            @endif
                            <span class="badge {{ $currentStatusBadge['class'] }} rounded-pill fs-7">
                                <i class="mdi {{ $currentStatusBadge['icon'] }} me-1"></i>
                                {{ $currentStatusBadge['text'] }}
                            </span>
                            @if ($fixed->qc_status === 'checking')
                                <span class="badge bg-label-warning rounded-pill fs-7">
                                    <i class="mdi mdi-clock-outline me-1"></i> Dalam Pengecekan QC
                                </span>
                            @elseif ($fixed->qc_status === 'ok')
                                <span class="badge bg-label-success rounded-pill fs-7">
                                    <i class="mdi mdi-check-decagram-outline me-1"></i> Lolos QC
                                </span>
                            @elseif ($fixed->qc_status === 'reject')
                                <span class="badge bg-label-danger rounded-pill fs-7">
                                    <i class="mdi mdi-close-octagon-outline me-1"></i> QC Reject
                                </span>
                            @endif
                        </h4>
                        <p class="text-muted mb-0 small">
                            @if ($fixed->unit)
                                <strong class="text-dark">{{ $fixed->unit->brand }} {{ $fixed->unit->model }}</strong>
                                <span class="mx-1">&bull;</span>
                                <span class="font-monospace">{{ $fixed->unit->sku }}</span>
                                <span class="mx-1">&bull;</span>
                            @endif
                            <span>Serial Number: <strong>{{ $fixed->serial_number ?: '-' }}</strong></span>
                            @if ($fixed->no_invoice)
                                <span class="mx-1">&bull;</span>
                                <span>Inv: {{ $fixed->no_invoice }}</span>
                            @endif
                        </p>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <button type="button" class="btn btn-outline-secondary btn-sm shadow-none" onclick="window.printBarcodeModal()">
                        <i class="mdi mdi-qrcode-scan me-1"></i> QR Barcode
                    </button>
                    @if ($fixed->machine)
                        <a href="{{ route('service-reports.unit.machine', [$fixed->machine->id_unit, $fixed->id_machine]) }}"
                           class="btn btn-outline-primary btn-sm shadow-none">
                            <i class="mdi mdi-notebook-edit-outline me-1"></i> Service Report
                        </a>
                    @endif
                    <a href="{{ route('fixed.show', $fixed->id) }}" class="btn btn-outline-secondary btn-sm shadow-none" title="Lihat Buku Aset di Menu Finance">
                        <i class="mdi mdi-finance me-1"></i> Finance
                    </a>
                    <button class="btn btn-outline-secondary btn-sm shadow-none" id="backButton">
                        <i class="mdi mdi-arrow-left me-1"></i> Kembali
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Quick KPI Metric Cards ────────────────────────────────── --}}
    <div class="row g-3 mb-4">
        {{-- Card 1: Kondisi & Umur Unit --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100 kpi-card">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small fw-semibold text-uppercase">Kondisi &amp; Umur Unit</span>
                        <span class="avatar avatar-xs rounded bg-label-info p-1">
                            <i class="mdi mdi-history fs-6"></i>
                        </span>
                    </div>
                    @php
                        $tglBeli = $fixed->beli ? \Carbon\Carbon::parse($fixed->beli) : null;
                        $umurUnit = $tglBeli ? $tglBeli->diffForHumans(null, true, false, 2) : '-';
                    @endphp
                    <h5 class="fw-bold mb-1 {{ $fixed->kondisi === 'Baru' ? 'text-success' : 'text-info' }} d-flex align-items-center gap-1">
                        <i class="mdi {{ $fixed->kondisi === 'Baru' ? 'mdi-sparkles' : 'mdi-sync' }}"></i>
                        <span>{{ $fixed->kondisi === 'Baru' ? 'Unit Baru' : 'Unit Second' }}</span>
                    </h5>
                    <div class="d-flex justify-content-between text-muted small" style="font-size: 0.76rem;">
                        <span>Umur: <strong>{{ $umurUnit }}</strong></span>
                        <span>Beli: <strong>{{ $tglBeli ? $tglBeli->format('d M Y') : '-' }}</strong></span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 2: Harga Jual Unit Second --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100 kpi-card position-relative">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small fw-semibold text-uppercase">Harga Jual Unit</span>
                        <div class="d-flex align-items-center gap-1">
                            @if (auth()->user()?->role === 'Admin')
                                <button type="button" class="btn btn-xs btn-label-success rounded-pill px-2 py-0.5 d-flex align-items-center gap-1"
                                    data-bs-toggle="modal" data-bs-target="#modalEditHargaJual" title="Atur Harga Jual">
                                    <i class="mdi mdi-pencil-outline" style="font-size: 11px;"></i>
                                    <span style="font-size: 11px;">Edit</span>
                                </button>
                            @endif
                            <span class="avatar avatar-xs rounded bg-label-success p-1">
                                <i class="mdi mdi-tag-outline fs-6"></i>
                            </span>
                        </div>
                    </div>
                    <h5 class="fw-bold mb-1 {{ $fixed->harga_jual ? 'text-success' : 'text-muted' }}">
                        {{ $fixed->harga_jual ? 'Rp ' . number_format($fixed->harga_jual, 0, ',', '.') : 'Belum Diset' }}
                    </h5>
                    <p class="text-muted small mb-0" style="font-size: 0.76rem;">
                        <i class="mdi mdi-information-outline me-1"></i>Patokan harga di penawaran Unit Second
                    </p>
                </div>
            </div>
        </div>

        {{-- Card 3: Tarif Rental (Per Hari & Per Bulan) --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100 kpi-card position-relative">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small fw-semibold text-uppercase">Tarif Rental</span>
                        <div class="d-flex align-items-center gap-1">
                            @if (auth()->user()?->role === 'Admin')
                                <button type="button" class="btn btn-xs btn-label-primary rounded-pill px-2 py-0.5 d-flex align-items-center gap-1"
                                    data-bs-toggle="modal" data-bs-target="#modalEditTarifRental" title="Atur Tarif Rental">
                                    <i class="mdi mdi-pencil-outline" style="font-size: 11px;"></i>
                                    <span style="font-size: 11px;">Edit</span>
                                </button>
                            @endif
                            <span class="avatar avatar-xs rounded bg-label-primary p-1">
                                <i class="mdi mdi-clock-time-four-outline fs-6"></i>
                            </span>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-baseline mb-1">
                        <span class="small text-muted fw-semibold">Per Hari:</span>
                        <span class="fw-bold {{ $fixed->harga_rental_hari ? 'text-primary' : 'text-muted' }}">
                            {{ $fixed->harga_rental_hari ? 'Rp ' . number_format($fixed->harga_rental_hari, 0, ',', '.') : '-' }}
                        </span>
                    </div>
                    <div class="d-flex justify-content-between align-items-baseline" style="font-size: 0.78rem;">
                        <span class="small text-muted fw-semibold">Per Bulan:</span>
                        <span class="fw-bold {{ $fixed->harga_rental_bulan ? 'text-primary' : 'text-muted' }}">
                            {{ $fixed->harga_rental_bulan ? 'Rp ' . number_format($fixed->harga_rental_bulan, 0, ',', '.') : '-' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 4: Status Ketersediaan Fisik --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100 kpi-card position-relative">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small fw-semibold text-uppercase">Ketersediaan Fisik</span>
                        <div class="d-flex align-items-center gap-1">
                            @if (auth()->user()?->role === 'Admin')
                                <button type="button" class="btn btn-xs btn-label-warning rounded-pill px-2 py-0.5 d-flex align-items-center gap-1"
                                    data-bs-toggle="modal" data-bs-target="#modalEditStatusUnit" title="Ubah Status Ketersediaan">
                                    <i class="mdi mdi-pencil-outline" style="font-size: 11px;"></i>
                                    <span style="font-size: 11px;">Edit</span>
                                </button>
                            @endif
                            <span class="avatar avatar-xs rounded bg-label-warning p-1">
                                <i class="mdi mdi-tune-vertical fs-6"></i>
                            </span>
                        </div>
                    </div>
                    @php
                        $statusColors = [
                            'OK' => 'text-success',
                            'Rental' => 'text-primary',
                            'Service' => 'text-warning',
                            'Breakdown' => 'text-danger',
                            'Reserved' => 'text-info',
                            'Sold' => 'text-dark',
                        ];
                        $statusNames = [
                            'OK' => 'Ready / OK',
                            'Rental' => 'Sedang Rental',
                            'Service' => 'Sedang Service',
                            'Breakdown' => 'Breakdown',
                            'Reserved' => 'Reserved',
                            'Sold' => 'Terjual (Sold)',
                        ];
                    @endphp
                    <h6 class="fw-bold mb-1 {{ $statusColors[$fixed->status_unit] ?? 'text-dark' }} d-flex align-items-center gap-1">
                        <i class="mdi {{ $currentStatusBadge['icon'] }}"></i>
                        <span>{{ $statusNames[$fixed->status_unit] ?? ($fixed->status_unit ?: 'Tidak Diketahui') }}</span>
                    </h6>
                    @if ($fixed->status_unit === 'Rental' && $lastOutScan)
                        <div class="text-muted small text-truncate" style="font-size: 0.76rem;" title="{{ optional($lastOutScan->client)->company }}">
                            <i class="mdi mdi-account-arrow-right-outline text-primary me-1"></i>{{ optional($lastOutScan->client)->company ?? '-' }}
                        </div>
                        <div class="text-muted small" style="font-size: 0.72rem;">
                            Sejak: {{ $lastOutScan->created_at->format('d M Y') }}
                        </div>
                    @elseif ($fixed->status_unit === 'OK')
                        <p class="text-muted small mb-0" style="font-size: 0.76rem;">
                            <i class="mdi mdi-check-circle-outline text-success me-1"></i>Siap sewa / jual di gudang
                        </p>
                    @elseif ($fixed->status_unit === 'Service')
                        <p class="text-muted small mb-0" style="font-size: 0.76rem;">
                            <i class="mdi mdi-wrench-clock-outline text-warning me-1"></i>Dalam perbaikan / rekondisi
                        </p>
                    @elseif ($fixed->status_unit === 'Breakdown')
                        <p class="text-muted small mb-0" style="font-size: 0.76rem;">
                            <i class="mdi mdi-alert-circle-outline text-danger me-1"></i>Unit rusak / tidak siap pakai
                        </p>
                    @elseif ($fixed->status_unit === 'Reserved')
                        <p class="text-muted small mb-0" style="font-size: 0.76rem;">
                            <i class="mdi mdi-bookmark-check-outline text-info me-1"></i>Telah dipesan customer
                        </p>
                    @elseif ($fixed->status_unit === 'Sold')
                        <p class="text-muted small mb-0" style="font-size: 0.76rem;">
                            <i class="mdi mdi-cash-check text-dark me-1"></i>Unit telah terjual
                        </p>
                    @else
                        <div class="text-muted small" style="font-size: 0.76rem;">
                            Tgl Beli: {{ \Carbon\Carbon::parse($fixed->beli)->format('d M Y') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ── Main Layout: Fullwidth Content Tabs ────────────────────────────────── --}}
    <div class="row g-3 mb-3">
        <div class="col-12">
            <div class="card border-0 shadow-sm mb-0">
                {{-- Card Navigation Tabs --}}
                <div class="card-header border-bottom p-0 bg-transparent">
                    <ul class="nav nav-tabs card-header-tabs border-0 m-0 px-3" id="unitDetailTabs" role="tablist">
                        @if ($fixed->unit)
                            <li class="nav-item" role="presentation">
                                <button type="button" class="nav-link active py-3 fw-semibold d-flex align-items-center gap-1"
                                    data-bs-toggle="tab" data-bs-target="#tab-spesifikasi" aria-selected="true">
                                    <i class="mdi mdi-format-list-bulleted-square me-1"></i>Spesifikasi Unit
                                </button>
                            </li>
                        @endif
                        @if ($fixed->qc_status === 'ok')
                            <li class="nav-item" role="presentation">
                                <button type="button" class="nav-link py-3 fw-semibold d-flex align-items-center gap-1 {{ !$fixed->unit ? 'active' : '' }}"
                                    data-bs-toggle="tab" data-bs-target="#tab-rental" aria-selected="false">
                                    <i class="mdi mdi-truck-delivery-outline me-1"></i>Rental &amp; QR
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button type="button" class="nav-link py-3 fw-semibold d-flex align-items-center gap-1"
                                    data-bs-toggle="tab" data-bs-target="#tab-quotation" aria-selected="false">
                                    <i class="mdi mdi-file-document-outline me-1"></i>Quotation
                                    @if ($confirmedOffers->isNotEmpty())
                                        <span class="badge bg-label-primary rounded-pill ms-1">{{ $confirmedOffers->count() }}</span>
                                    @endif
                                </button>
                            </li>
                        @endif
                        @if ($fixed->machine)
                            <li class="nav-item" role="presentation">
                                <button type="button" class="nav-link py-3 fw-semibold d-flex align-items-center gap-1 {{ $fixed->qc_status !== 'ok' && !$fixed->unit ? 'active' : '' }}"
                                    data-bs-toggle="tab" data-bs-target="#tab-service-report" aria-selected="false">
                                    <i class="mdi mdi-notebook-edit-outline me-1"></i>Service Reports
                                </button>
                            </li>
                        @endif
                        <li class="nav-item" role="presentation">
                            <button type="button" class="nav-link py-3 fw-semibold d-flex align-items-center gap-1 {{ $fixed->qc_status !== 'ok' && !$fixed->machine && !$fixed->unit ? 'active' : '' }}"
                                data-bs-toggle="tab" data-bs-target="#tab-servis-part" aria-selected="false">
                                <i class="mdi mdi-wrench-outline me-1"></i>Biaya Servis / Spare Part
                                @if ($services->isNotEmpty())
                                    <span class="badge bg-label-secondary rounded-pill ms-1">{{ $services->count() }}</span>
                                @endif
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button type="button" class="nav-link py-3 fw-semibold d-flex align-items-center gap-1"
                                data-bs-toggle="tab" data-bs-target="#tab-work-orders" aria-selected="false">
                                <i class="mdi mdi-wrench-cog-outline me-1"></i>Work Order Spare Part
                                @if (isset($workOrders) && $workOrders->isNotEmpty())
                                    <span class="badge bg-label-primary rounded-pill ms-1">{{ $workOrders->count() }}</span>
                                @endif
                            </button>
                        </li>
                    </ul>
                </div>

                {{-- Tab Contents --}}
                <div class="tab-content p-4">
                    {{-- ── TAB 1: Spesifikasi Mesin ───────────────────── --}}
                    @if ($fixed->unit)
                        <div class="tab-pane fade show active" id="tab-spesifikasi" role="tabpanel">
                            <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                                <h6 class="fw-bold mb-0 text-dark">
                                    <i class="mdi mdi-information-outline text-primary me-2"></i>Spesifikasi Lengkap Unit
                                </h6>
                                <span class="badge bg-label-secondary font-monospace">{{ $fixed->unit->unit }}</span>
                            </div>

                            <div class="row g-3">
                                <div class="col-12 col-md-6">
                                    <div class="p-3 border rounded-3 bg-light-subtle h-100">
                                        <h6 class="fw-bold small text-muted text-uppercase mb-3">Identitas Utama</h6>
                                        @include('components.detail-row', ['label' => 'Kategori', 'value' => $fixed->unit->unit])
                                        @include('components.detail-row', ['label' => 'SKU', 'value' => $fixed->unit->sku])
                                        @include('components.detail-row', ['label' => 'Brand', 'value' => $fixed->unit->brand])
                                        @include('components.detail-row', ['label' => 'Model', 'value' => $fixed->unit->model])
                                        @include('components.detail-row', ['label' => 'Generasi', 'value' => $fixed->unit->generation])
                                        @include('components.detail-row', ['label' => 'Serial Number', 'value' => $fixed->serial_number ?: '-'])
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="p-3 border rounded-3 bg-light-subtle h-100">
                                        <h6 class="fw-bold small text-muted text-uppercase mb-3">Parameter Teknis</h6>
                                        @if ($isCompressor)
                                            @include('components.detail-row', ['label' => 'Tipe Mesin', 'value' => $fixed->unit->formatted_type ?: $fixed->unit->type_unit])
                                            @include('components.detail-row', ['label' => 'Motor Power', 'value' => $fixed->unit->power ? $fixed->unit->power . ' kW' : null])
                                            @include('components.detail-row', ['label' => 'Kapasitas Udara', 'value' => $fixed->unit->air_cap ? $fixed->unit->air_cap . ' m³/min' : null])
                                            @include('components.detail-row', ['label' => 'Tekanan Maks.', 'value' => $fixed->unit->bar ? $fixed->unit->bar . ' Bar' : null])
                                            @include('components.detail-row', ['label' => 'Tegangan / Voltase', 'value' => $fixed->unit->voltage])
                                            @include('components.detail-row', ['label' => 'Sistem Penggerak', 'value' => $fixed->unit->connect])
                                            @include('components.detail-row', ['label' => 'Pendingin', 'value' => $fixed->unit->cooling])
                                            @include('components.detail-row', ['label' => 'Exhaust / Discharge', 'value' => $fixed->unit->exhaust])
                                        @elseif ($isDryer)
                                            @include('components.detail-row', ['label' => 'Kapasitas Udara', 'value' => $fixed->unit->air_cap ? $fixed->unit->air_cap . ' m³/min' : null])
                                            @include('components.detail-row', ['label' => 'Tipe Refrigerant', 'value' => $fixed->unit->refrigerant_type])
                                            @include('components.detail-row', ['label' => 'Pressure Dew Point (PDP)', 'value' => $fixed->unit->pdp])
                                        @endif
                                        @include('components.detail-row', ['label' => 'Dimensi (P x L x T)', 'value' => $fixed->unit->dimension])
                                        @include('components.detail-row', ['label' => 'Berat Fisik', 'value' => $fixed->unit->weight ? $fixed->unit->weight . ' Kg' : null])
                                    </div>
                                </div>
                            </div>

                            @if (!$isCompressor && !$isDryer && $fixed->unit->desc)
                                <div class="mt-3 p-3 border rounded-3 bg-light-subtle">
                                    <h6 class="fw-bold small text-muted text-uppercase mb-2">Deskripsi Tambahan</h6>
                                    <p class="mb-0 small text-dark">{{ $fixed->unit->desc }}</p>
                                </div>
                            @endif
                        </div>
                    @endif

                    {{-- ── TAB 2: Rental & QR Barcode ─────────────────── --}}
                    @if ($fixed->qc_status === 'ok')
                        <div class="tab-pane fade {{ !$fixed->unit ? 'show active' : '' }}" id="tab-rental" role="tabpanel">
                            <div class="row g-4">
                                {{-- Kolom Kiri: Form Scan Rental --}}
                                <div class="col-12 col-lg-7">
                                    @if ($fixed->status_unit === 'OK')
                                        {{-- Jadikan Rental --}}
                                        <div class="card border border-primary border-opacity-25 bg-primary bg-opacity-10 shadow-none mb-3">
                                            <div class="card-body p-3">
                                                <h6 class="fw-bold mb-1 text-primary d-flex align-items-center">
                                                    <i class="mdi mdi-truck-delivery-outline me-2 fs-5"></i>Form Pengeluaran Rental
                                                </h6>
                                                <p class="text-muted small mb-3">Unit siap dikirim ke customer. Pilih penawaran PO atau tentukan nama client penyewa.</p>

                                                @php
                                                    $dealOffers = $confirmedOffers->filter(fn($o) => $o->status === 'po_received' || $o->po_number);
                                                @endphp
                                                @if ($dealOffers->isNotEmpty())
                                                    <div class="mb-3">
                                                        <label class="form-label small fw-semibold text-dark mb-1">
                                                            <i class="mdi mdi-lightning-bolt-outline text-warning me-1"></i>Pilih Cepat dari Penawaran Deal (PO):
                                                        </label>
                                                        <div class="d-flex flex-column gap-2">
                                                            @foreach ($dealOffers as $offer)
                                                                <button type="button" class="btn btn-white bg-white border text-start btn-sm offer-pick shadow-sm d-flex justify-content-between align-items-center"
                                                                    data-client-id="{{ $offer->client->id ?? '' }}"
                                                                    data-client-company="{{ $offer->client->company ?? '-' }}">
                                                                    <span>
                                                                        <strong class="text-primary">{{ $offer->client->company ?? '-' }}</strong>
                                                                        <span class="text-muted small ms-1">— {{ $offer->no_quote }}</span>
                                                                    </span>
                                                                    <span class="badge bg-label-success fs-8">PO: {{ $offer->po_number ?: '-' }}</span>
                                                                </button>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endif

                                                <form action="{{ route('fixed-asset.scan.out', $fixed->id) }}" method="post">
                                                    @csrf
                                                    <div class="mb-3">
                                                        <label class="form-label small fw-semibold text-muted mb-1">Client / Customer Penyewa <span class="text-danger">*</span></label>
                                                        <select class="form-select select2-scan-client" name="id_client" id="scanClient" required>
                                                            <option value="" selected disabled>-- Cari Perusahaan Client --</option>
                                                        </select>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label small fw-semibold text-muted mb-1">PIC Internal yang Menangani</label>
                                                        <input type="text" class="form-control form-control-sm bg-white" value="{{ Auth::user()?->name ?? '-' }}" disabled>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label small fw-semibold text-muted mb-1">Catatan Sewa (Opsional)</label>
                                                        <input type="text" class="form-control form-control-sm" name="note" placeholder="Contoh: Rental 1 bulan, lokasi project Cikarang">
                                                    </div>
                                                    <button type="submit" class="btn btn-primary btn-sm shadow-sm w-100">
                                                        <i class="mdi mdi-truck-delivery-outline me-1"></i> Konfirmasi Jadikan Rental
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    @elseif ($fixed->status_unit === 'Rental')
                                        {{-- Terima Kembali --}}
                                        <div class="card border border-warning border-opacity-25 bg-warning bg-opacity-10 shadow-none mb-3">
                                            <div class="card-body p-3">
                                                <h6 class="fw-bold mb-1 text-warning d-flex align-items-center">
                                                    <i class="mdi mdi-package-down me-2 fs-5"></i>Unit Sedang Disewa
                                                </h6>
                                                <p class="text-muted small mb-3">Konfirmasi pengembalian unit fisik kembali ke gudang.</p>

                                                @if ($lastOutScan)
                                                    <div class="border rounded-3 p-3 mb-3 bg-white shadow-sm">
                                                        <div class="d-flex justify-content-between align-items-center pb-2 mb-2 border-bottom">
                                                            <span class="text-muted small">Penyewa Aktif:</span>
                                                            <span class="fw-bold text-dark">{{ optional($lastOutScan->client)->company ?? '-' }}</span>
                                                        </div>
                                                        <div class="d-flex justify-content-between align-items-center pb-2 mb-2 border-bottom">
                                                            <span class="text-muted small">PIC Internal:</span>
                                                            <span class="fw-semibold text-dark">{{ optional($lastOutScan->picInternal)->name ?? '-' }}</span>
                                                        </div>
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <span class="text-muted small">Tgl Keluar:</span>
                                                            <span class="text-dark">{{ $lastOutScan->created_at->format('d-m-Y H:i') }}</span>
                                                        </div>
                                                    </div>
                                                @endif

                                                <form action="{{ route('fixed-asset.scan.in', $fixed->id) }}" method="post">
                                                    @csrf
                                                    <div class="mb-3">
                                                        <label class="form-label small fw-semibold text-muted mb-1">Catatan Penerimaan Kembali (Opsional)</label>
                                                        <input type="text" class="form-control form-control-sm" name="note" placeholder="Kondisi mesin saat diterima kembali, jam kerja / running hours, dll.">
                                                    </div>
                                                    <button type="submit" class="btn btn-success btn-sm shadow-sm w-100">
                                                        <i class="mdi mdi-check-circle-outline me-1"></i> Konfirmasi Terima Kembali ke Gudang
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    @else
                                        <div class="alert alert-secondary d-flex align-items-center gap-2 mb-3" role="alert">
                                            <i class="mdi mdi-information-outline fs-5"></i>
                                            <div>Status unit sekarang <strong>{{ $fixed->status_unit }}</strong>. Unit hanya bisa dijadikan rental jika berstatus <strong>OK</strong>.</div>
                                        </div>
                                    @endif
                                </div>

                                {{-- Kolom Kanan: Barcode QR Card --}}
                                <div class="col-12 col-lg-5">
                                    <div class="card border rounded-3 text-center p-3 h-100 bg-light-subtle d-flex flex-column justify-content-center align-items-center">
                                        <h6 class="fw-bold text-dark mb-1">QR Barcode Unit</h6>
                                        <p class="text-muted small mb-3">Scan QR code ini untuk membuka langsung detail unit ini di gudang.</p>
                                        <div class="bg-white p-3 rounded border shadow-sm mb-3">
                                            <img src="{{ route('fixed-asset.barcode', $fixed->id) }}" alt="Barcode {{ $fixed->code }}" class="img-fluid" style="max-width: 180px;">
                                        </div>
                                        <button type="button" class="btn btn-outline-primary btn-sm shadow-none" onclick="window.printBarcodeModal()">
                                            <i class="mdi mdi-printer me-1"></i> Cetak / Print Label QR
                                        </button>
                                    </div>
                                </div>
                            </div>

                            {{-- Riwayat Rental Table --}}
                            <div class="mt-4 pt-3 border-top">
                                <h6 class="fw-bold mb-3 text-dark d-flex align-items-center">
                                    <i class="mdi mdi-history text-primary me-2"></i>Riwayat Pergerakan Rental
                                </h6>
                                <div class="table-responsive">
                                    <table class="table table-hover table-sm align-middle mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Tanggal &amp; Waktu</th>
                                                <th>Aksi</th>
                                                <th>Client / Penyewa</th>
                                                <th>PIC Internal</th>
                                                <th>Petugas Scan</th>
                                                <th>Catatan</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($fixed->rentalScans as $scan)
                                                <tr>
                                                    <td class="small">{{ $scan->created_at->format('d-m-Y H:i') }}</td>
                                                    <td>
                                                        @if ($scan->action === 'out')
                                                            <span class="badge bg-label-primary px-2 py-1">
                                                                <i class="mdi mdi-arrow-up-right me-1"></i>Keluar (Rental)
                                                            </span>
                                                        @else
                                                            <span class="badge bg-label-success px-2 py-1">
                                                                <i class="mdi mdi-arrow-down-left me-1"></i>Kembali (OK)
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td class="fw-semibold text-dark">{{ optional($scan->client)->company ?? '-' }}</td>
                                                    <td class="small">{{ optional($scan->picInternal)->name ?? '-' }}</td>
                                                    <td class="small text-muted">{{ optional($scan->scannedBy)->name ?? '-' }}</td>
                                                    <td class="small text-muted">{{ $scan->note ?: '-' }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="6" class="text-center text-muted py-4">
                                                        <i class="mdi mdi-calendar-blank-outline d-block fs-3 mb-1"></i>
                                                        Belum ada riwayat pergerakan rental untuk unit ini.
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- ── TAB 3: Daftar Quotation (Smart Quote) ────────── --}}
                    @if ($fixed->qc_status === 'ok')
                        <div class="tab-pane fade" id="tab-quotation" role="tabpanel">
                            <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                                <div>
                                    <h6 class="fw-bold mb-0 text-dark">
                                        <i class="mdi mdi-file-document-outline text-primary me-2"></i>Daftar Quotation Unit Ini
                                    </h6>
                                    <p class="text-muted small mb-0">Daftar semua penawaran Smart Quote yang dibuat dan merujuk unit fisik ini.</p>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover table-sm align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>No. Quotation</th>
                                            <th>Status</th>
                                            <th>Nomor PO</th>
                                            <th>Customer / Client</th>
                                            <th>Tanggal Quote</th>
                                            <th class="text-end">Nilai Unit Terkait</th>
                                            <th class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $quoteStatusMap = [
                                                'draft'        => ['label' => 'Draft',        'class' => 'bg-label-secondary'],
                                                'sent'         => ['label' => 'Sent',         'class' => 'bg-label-info'],
                                                'negotiation'  => ['label' => 'Negotiation',  'class' => 'bg-label-warning'],
                                                'revision'     => ['label' => 'Revisi',       'class' => 'bg-label-primary'],
                                                'hot_prospect' => ['label' => 'Hot Prospect', 'class' => 'bg-label-danger'],
                                                'po_received'  => ['label' => 'PO Received',  'class' => 'bg-label-success'],
                                                'loss'         => ['label' => 'Loss',         'class' => 'bg-label-dark'],
                                            ];
                                        @endphp
                                        @forelse ($confirmedOffers as $offer)
                                            @php
                                                $stInfo = $quoteStatusMap[$offer->status] ?? ['label' => ucfirst(str_replace('_', ' ', $offer->status)), 'class' => 'bg-label-secondary'];
                                            @endphp
                                            <tr>
                                                <td class="fw-bold text-primary font-monospace">{{ $offer->no_quote }}</td>
                                                <td>
                                                    <span class="badge {{ $stInfo['class'] }} rounded-pill" style="font-size: 11px;">
                                                        {{ $stInfo['label'] }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @if ($offer->po_number)
                                                        <span class="badge bg-label-success">{{ $offer->po_number }}</span>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td class="fw-semibold text-dark">{{ optional($offer->client)->company ?? '-' }}</td>
                                                <td class="small">{{ $offer->date ? $offer->date->format('d-m-Y') : '-' }}</td>
                                                <td class="text-end fw-bold text-dark">
                                                    Rp {{ number_format($offer->details->sum('amount'), 0, ',', '.') }}
                                                </td>
                                                <td class="text-center">
                                                    <a href="{{ route('unit-quotation.show', $offer->id) }}" class="btn btn-xs btn-outline-primary shadow-none" target="_blank" title="Buka Detail Smart Quote">
                                                        <i class="mdi mdi-open-in-new me-1"></i>Buka
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center text-muted py-4">
                                                    <i class="mdi mdi-file-document-remove-outline d-block fs-3 mb-1"></i>
                                                    Belum ada penawaran (Quotation) yang menyebut unit fisik ini.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif

                    {{-- ── TAB 4: Service Reports ─────────────────────── --}}
                    @if ($fixed->machine)
                        <div class="tab-pane fade {{ $fixed->qc_status !== 'ok' && !$fixed->unit ? 'show active' : '' }}" id="tab-service-report" role="tabpanel">
                            <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                                <div>
                                    <h6 class="fw-bold mb-0 text-dark">
                                        <i class="mdi mdi-notebook-edit-outline text-primary me-2"></i>Riwayat Service Report Teknisi
                                    </h6>
                                    <p class="text-muted small mb-0">Laporan servis dan pekerjaan teknisi pada mesin unit ini.</p>
                                </div>
                                <a href="{{ route('service-reports.unit.machine', [$fixed->machine->id_unit, $fixed->id_machine]) }}"
                                   class="btn btn-primary btn-sm shadow-none">
                                    <i class="mdi mdi-plus me-1"></i>Buat Service Report
                                </a>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered m-0 datatable-service-report-history w-100" id="tableServiceReportHistory">
                                    <thead class="table-light">
                                        <tr>
                                            <th>No. Service</th>
                                            <th>Tipe Servis</th>
                                            <th>Deskripsi Pekerjaan</th>
                                            <th>Tanggal</th>
                                            <th>Teknisi</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    @endif

                    {{-- ── TAB 5: Biaya Servis & Spare Part ───────────── --}}
                    <div class="tab-pane fade {{ $fixed->qc_status !== 'ok' && !$fixed->machine && !$fixed->unit ? 'show active' : '' }}" id="tab-servis-part" role="tabpanel">
                        <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                            <div>
                                <h6 class="fw-bold mb-0 text-dark">
                                    <i class="mdi mdi-wrench text-warning me-2"></i>Pemakaian Spare Part / Servis Rekondisi
                                </h6>
                                <p class="text-muted small mb-0">Spare part yang dipakai selama reconditioning unit (dikapitalisasi ke harga perolehan unit).</p>
                            </div>
                            @if ($fixed->qc_status === 'checking' || $fixed->kondisi === 'Baru')
                                <a href="{{ route('unit-acquisition.service.create', $fixed->id) }}" class="btn btn-outline-primary btn-sm shadow-none">
                                    <i class="mdi mdi-plus me-1"></i>Tambah Part / Servis
                                </a>
                            @endif
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover table-sm align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Spare Part</th>
                                        <th>Gudang</th>
                                        <th class="text-center">Qty</th>
                                        <th class="text-end">Amount</th>
                                        <th>Catatan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($services as $service)
                                        <tr>
                                            <td class="small">{{ \Carbon\Carbon::parse($service->date)->format('d-m-Y') }}</td>
                                            <td class="fw-semibold text-dark">{{ $service->detailProduct?->product?->commodity ?? '-' }}</td>
                                            <td><span class="badge bg-label-secondary">{{ $service->warehouse }}</span></td>
                                            <td class="text-center fw-bold">{{ $service->qty }}</td>
                                            <td class="text-end fw-bold text-dark">Rp {{ number_format($service->amount, 0, ',', '.') }}</td>
                                            <td class="small text-muted">{{ $service->note ?: '-' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted py-4">
                                                <i class="mdi mdi-wrench-outline d-block fs-3 mb-1"></i>
                                                Belum ada pemakaian spare part / biaya servis yang tercatat untuk unit ini.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- ── TAB 6: Riwayat Work Order Internal ────────── --}}
                    <div class="tab-pane fade" id="tab-work-orders" role="tabpanel">
                        <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                            <div>
                                <h6 class="fw-bold mb-0 text-dark">
                                    <i class="mdi mdi-wrench-cog text-primary me-2"></i>Riwayat Work Order (Pergantian Spare Part)
                                </h6>
                                <p class="text-muted small mb-0">Pengajuan dan pengeluaran suku cadang internal untuk pemeliharaan mesin unit ini.</p>
                            </div>
                            <a href="{{ route('work-orders.create') }}" class="btn btn-primary btn-sm shadow-none">
                                <i class="mdi mdi-plus me-1"></i>Buat Work Order
                            </a>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover table-sm align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>No. WO</th>
                                        <th>Tgl Pengajuan</th>
                                        <th>Pemohon</th>
                                        <th>Part Diminta</th>
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
                                                <a href="{{ route('work-orders.show', $woItem->id) }}" class="fw-bold text-primary">
                                                    {{ $woItem->no_wo }}
                                                </a>
                                            </td>
                                            <td class="small">{{ $woItem->date ? $woItem->date->format('d-m-Y') : '-' }}</td>
                                            <td class="small">{{ $woItem->creator->name ?? '-' }}</td>
                                            <td>
                                                <span class="badge bg-label-secondary rounded-pill">
                                                    {{ $woItem->items->count() }} Part
                                                </span>
                                            </td>
                                            <td class="text-end fw-bold text-dark">
                                                Rp {{ number_format($woItem->total_cost, 0, ',', '.') }}
                                            </td>
                                            <td class="text-center">
                                                @if ($woItem->accounting_treatment === 'capitalize')
                                                    <span class="badge bg-label-success" style="font-size: 10px;">Kapitalisasi</span>
                                                @elseif ($woItem->accounting_treatment === 'expense')
                                                    <span class="badge bg-label-info" style="font-size: 10px;">Beban Biaya</span>
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
                                                <a href="{{ route('work-orders.show', $woItem->id) }}" class="btn btn-xs btn-outline-primary shadow-none">
                                                    Detail
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center text-muted py-4">
                                                <i class="mdi mdi-clipboard-text-outline d-block fs-3 mb-1"></i>
                                                Belum ada riwayat Work Order yang tercatat untuk mesin ini.
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

    {{-- ── Bottom Control & Navigasi Terkait ─────────────────────────────── --}}
    <div class="row g-3 mb-4">
        {{-- Card Menu Cepat & Navigasi Terkait --}}
        <div class="col-12">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header py-3 border-bottom bg-transparent d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold text-dark d-flex align-items-center fs-6">
                        <i class="mdi mdi-link-variant text-primary me-2"></i>Aksi Terkait &amp; Navigasi
                    </h6>
                    @if ($fixed->qc_status === 'checking')
                        <span class="badge bg-label-warning"><i class="mdi mdi-clock-outline me-1"></i>Menunggu Keputusan QC</span>
                    @endif
                </div>
                <div class="card-body p-3">
                    @if ($fixed->qc_status === 'checking' && auth()->user()?->role === 'Admin')
                        <div class="border border-warning border-opacity-50 rounded p-3 bg-warning bg-opacity-10 mb-3">
                            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                                <div>
                                    <h6 class="mb-1 fw-bold text-warning d-flex align-items-center" style="font-size: 0.9rem;">
                                        <i class="mdi mdi-clipboard-check-outline me-1.5"></i>Keputusan QC Masuk Unit
                                    </h6>
                                    <p class="small text-muted mb-0" style="font-size: 0.78rem;">Unit baru/masuk ini sedang dalam pengecekan inspeksi awal teknisi.</p>
                                </div>
                                <div class="d-flex gap-2">
                                    <form action="{{ route('unit-acquisition.confirm', $fixed->id) }}" method="post">
                                        @csrf
                                        <input type="hidden" name="decision" value="ok">
                                        <button type="submit" class="btn btn-success btn-sm shadow-sm px-3">
                                            <i class="mdi mdi-check me-1"></i> Lolos QC (Siap Operasional)
                                        </button>
                                    </form>
                                    <form action="{{ route('unit-acquisition.confirm', $fixed->id) }}" method="post">
                                        @csrf
                                        <input type="hidden" name="decision" value="reject">
                                        <button type="submit" class="btn btn-outline-danger btn-sm shadow-none px-3">
                                            <i class="mdi mdi-close me-1"></i> Reject Unit
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="d-flex flex-wrap gap-2">
                        @if ($fixed->qc_status === 'checking' || $fixed->kondisi === 'Baru')
                            <a href="{{ route('unit-acquisition.service.create', $fixed->id) }}" class="btn btn-outline-primary btn-sm shadow-none">
                                <i class="mdi mdi-wrench-outline me-1"></i>Tambah Pemakaian Part Servis
                            </a>
                        @endif

                        @if ($fixed->machine)
                            <a href="{{ route('service-reports.unit.machine', [$fixed->machine->id_unit, $fixed->id_machine]) }}" class="btn btn-outline-secondary btn-sm shadow-none">
                                <i class="mdi mdi-notebook-edit-outline me-1"></i>Buat Service Report Mesin
                            </a>
                        @endif

                        <a href="{{ route('fixed.edit', $fixed->id) }}" class="btn btn-outline-secondary btn-sm shadow-none">
                            <i class="mdi mdi-pencil-box-outline me-1"></i>Edit Data Pokok Fixed Asset
                        </a>

                        <a href="{{ route('fixed.show', $fixed->id) }}" class="btn btn-outline-secondary btn-sm shadow-none">
                            <i class="mdi mdi-finance me-1"></i>Buka di Menu Finance
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if (auth()->user()?->role === 'Admin')
        <!-- Modal Edit Harga Jual Unit -->
        <div class="modal fade" id="modalEditHargaJual" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-sm">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header py-3 bg-light border-bottom">
                        <h6 class="modal-title fw-bold text-dark d-flex align-items-center mb-0">
                            <i class="mdi mdi-tag-outline text-success me-2 fs-5"></i>Edit Harga Jual Unit
                        </h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('unit-acquisition.pricing', $fixed->id) }}" method="post">
                        @csrf
                        <div class="modal-body p-3">
                            <p class="text-muted small mb-3">
                                Tentukan patokan harga jual putus untuk unit second ini saat ditarik ke <strong>Smart Quote</strong>.
                            </p>
                            <div class="mb-2">
                                <label class="form-label small fw-semibold text-dark mb-1">Harga Jual Unit Second</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light fw-bold text-muted">Rp</span>
                                    <input type="text" class="form-control rupiah-mask fw-bold text-success" id="modalHargaJualInput"
                                        placeholder="Contoh: 50.000.000" autocomplete="off"
                                        value="{{ $fixed->harga_jual ? number_format(old('harga_jual', $fixed->harga_jual), 0, ',', '.') : '' }}">
                                    <input type="hidden" name="harga_jual" id="modalHargaJualRaw"
                                        value="{{ old('harga_jual', $fixed->harga_jual) }}">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer py-2 px-3 bg-light border-top d-flex justify-content-between">
                            <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-success btn-sm shadow-sm">
                                <i class="mdi mdi-check me-1"></i>Simpan Harga
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal Edit Tarif Rental -->
        <div class="modal fade" id="modalEditTarifRental" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header py-3 bg-light border-bottom">
                        <h6 class="modal-title fw-bold text-dark d-flex align-items-center mb-0">
                            <i class="mdi mdi-clock-time-four-outline text-primary me-2 fs-5"></i>Edit Tarif Rental Unit
                        </h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('unit-acquisition.pricing', $fixed->id) }}" method="post">
                        @csrf
                        <div class="modal-body p-3">
                            <p class="text-muted small mb-3">
                                Tentukan patokan tarif rental (sewa harian &amp; bulanan). Nilai ini otomatis terisi saat unit dipilih pada penawaran <strong>Smart Quote Rental</strong>.
                            </p>
                            <div class="mb-3">
                                <label class="form-label small fw-semibold text-dark mb-1">Tarif Rental / Hari (Daily Rate)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light fw-bold text-muted">Rp</span>
                                    <input type="text" class="form-control rupiah-mask fw-bold text-primary" id="modalRentalHariInput"
                                        placeholder="Contoh: 1.500.000" autocomplete="off"
                                        value="{{ $fixed->harga_rental_hari ? number_format(old('harga_rental_hari', $fixed->harga_rental_hari), 0, ',', '.') : '' }}">
                                    <input type="hidden" name="harga_rental_hari" id="modalRentalHariRaw"
                                        value="{{ old('harga_rental_hari', $fixed->harga_rental_hari) }}">
                                </div>
                            </div>
                            <div class="mb-2">
                                <label class="form-label small fw-semibold text-dark mb-1">Tarif Rental / Bulan (Monthly Rate)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light fw-bold text-muted">Rp</span>
                                    <input type="text" class="form-control rupiah-mask fw-bold text-primary" id="modalRentalBulanInput"
                                        placeholder="Contoh: 25.000.000" autocomplete="off"
                                        value="{{ $fixed->harga_rental_bulan ? number_format(old('harga_rental_bulan', $fixed->harga_rental_bulan), 0, ',', '.') : '' }}">
                                    <input type="hidden" name="harga_rental_bulan" id="modalRentalBulanRaw"
                                        value="{{ old('harga_rental_bulan', $fixed->harga_rental_bulan) }}">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer py-2 px-3 bg-light border-top d-flex justify-content-between">
                            <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary btn-sm shadow-sm">
                                <i class="mdi mdi-check me-1"></i>Simpan Tarif
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal Edit Status Ketersediaan Fisik Unit -->
        <div class="modal fade" id="modalEditStatusUnit" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-sm">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header py-3 bg-light border-bottom">
                        <h6 class="modal-title fw-bold text-dark d-flex align-items-center mb-0">
                            <i class="mdi mdi-tune-vertical text-warning me-2 fs-5"></i>Ubah Status Unit
                        </h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('unit-acquisition.status', $fixed->id) }}" method="post">
                        @csrf
                        <div class="modal-body p-3">
                            <p class="text-muted small mb-3">
                                Pilih status ketersediaan operasional fisik untuk unit ini.
                            </p>
                            <div class="mb-2">
                                <label class="form-label small fw-semibold text-dark mb-1">Status Ketersediaan</label>
                                <select class="form-select form-select-sm" name="status_unit">
                                    <option value="OK" {{ $fixed->status_unit === 'OK' ? 'selected' : '' }}>Ready / OK (Siap Sewa / Jual)</option>
                                    <option value="Rental" {{ $fixed->status_unit === 'Rental' ? 'selected' : '' }}>Sedang Rental (Di Customer)</option>
                                    <option value="Service" {{ $fixed->status_unit === 'Service' ? 'selected' : '' }}>Sedang Service / Rekondisi</option>
                                    <option value="Breakdown" {{ $fixed->status_unit === 'Breakdown' ? 'selected' : '' }}>Breakdown (Rusak)</option>
                                    <option value="Reserved" {{ $fixed->status_unit === 'Reserved' ? 'selected' : '' }}>Reserved (Dipesan Customer)</option>
                                    <option value="Sold" {{ $fixed->status_unit === 'Sold' ? 'selected' : '' }}>Terjual / Sold</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer py-2 px-3 bg-light border-top d-flex justify-content-between">
                            <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-warning btn-sm shadow-sm text-dark fw-semibold">
                                <i class="mdi mdi-check me-1"></i>Simpan Status
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    {{-- ── Modal Print Barcode ────────────────────────────────────── --}}
    <div class="modal fade" id="modalPrintBarcode" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header border-bottom py-2">
                    <h6 class="modal-title fw-bold" id="modalPrintBarcodeTitle">Label Barcode Unit</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center p-4" id="printableBarcodeArea">
                    <div class="fw-bold text-dark mb-1">{{ $fixed->code }}</div>
                    <div class="small text-muted mb-2">
                        {{ $fixed->unit ? $fixed->unit->brand . ' ' . $fixed->unit->model : '' }}
                        @if ($fixed->serial_number) (SN: {{ $fixed->serial_number }}) @endif
                    </div>
                    <div class="p-2 border rounded bg-white d-inline-block shadow-sm mb-2">
                        <img src="{{ route('fixed-asset.barcode', $fixed->id) }}" alt="QR {{ $fixed->code }}" style="max-width: 190px; height: auto;">
                    </div>
                    <div class="small text-muted" style="font-size: 0.7rem;">PT. REFTECH GLOBAL MANDIRI</div>
                </div>
                <div class="modal-footer border-top py-2">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-primary btn-sm" onclick="window.printBarcodeDirect()">
                        <i class="mdi mdi-printer me-1"></i> Cetak Label
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('after-style')
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/select2/select2.css" />
    <style>
        .kpi-card {
            transition: transform 0.18s ease, box-shadow 0.18s ease;
        }
        .kpi-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 0.5rem 1rem rgba(67, 89, 113, 0.1) !important;
        }
        #unitDetailTabs .nav-link {
            border-bottom: 2px solid transparent !important;
            color: #697a8d;
            transition: all 0.18s ease;
        }
        #unitDetailTabs .nav-link:hover {
            color: #666cff;
        }
        #unitDetailTabs .nav-link.active {
            color: #666cff !important;
            border-bottom-color: #666cff !important;
            background: transparent !important;
        }
        @media print {
            body * {
                visibility: hidden;
            }
            #printableBarcodeArea, #printableBarcodeArea * {
                visibility: visible;
            }
            #printableBarcodeArea {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }
        }
    </style>
@endpush

@push('after-script')
    <script src="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/select2/select2.js"></script>
@endpush

@push('script')
    <script>
        $(function() {
            $('#backButton').on('click', function() {
                window.history.back();
            });

            // ── Rupiah Input Auto-Format ────────────────────────────────────
            function setupRupiahMask(displayId, rawId) {
                var disp = document.getElementById(displayId);
                var raw = document.getElementById(rawId);
                if (!disp || !raw) return;

                disp.addEventListener('input', function() {
                    var val = this.value.replace(/\D/g, '');
                    this.value = val ? String(parseInt(val, 10)).replace(/\B(?=(\d{3})+(?!\d))/g, '.') : '';
                    raw.value = val || '';
                });
            }

            setupRupiahMask('modalHargaJualInput', 'modalHargaJualRaw');
            setupRupiahMask('modalRentalHariInput', 'modalRentalHariRaw');
            setupRupiahMask('modalRentalBulanInput', 'modalRentalBulanRaw');

            // ── Form Jadikan Rental: Select2 Client ─────────────────────────
            if ($('#scanClient').length) {
                $('#scanClient').select2({
                    width: '100%',
                    placeholder: 'Cari nama customer / perusahaan...',
                    minimumInputLength: 2,
                    ajax: {
                        url: '/db/client/search',
                        dataType: 'json',
                        delay: 300,
                        data: function(params) { return { q: params.term }; },
                        processResults: function(data) {
                            return {
                                results: $.map(data, function(c) {
                                    return { id: c.id, text: c.company };
                                })
                            };
                        },
                    },
                });

                $('.offer-pick').on('click', function() {
                    var id = $(this).data('client-id');
                    var company = $(this).data('client-company');
                    if (!id) return;
                    var option = new Option(company, id, true, true);
                    $('#scanClient').empty().append(option).trigger('change');
                });
            }

            // ── Modal Print Barcode ─────────────────────────────────────────
            window.printBarcodeModal = function() {
                var modal = new bootstrap.Modal(document.getElementById('modalPrintBarcode'));
                modal.show();
            };

            window.printBarcodeDirect = function() {
                window.print();
            };

            // ── Service Reports Datatable ───────────────────────────────────
            @if ($fixed->machine)
                if (window.jQuery && $.fn.DataTable) {
                    $.fn.dataTable.ext.errMode = 'none';

                    if ($.fn.DataTable.isDataTable('#tableServiceReportHistory')) {
                        $('#tableServiceReportHistory').DataTable().destroy();
                    }

                    var dtServiceReport = $('#tableServiceReportHistory').DataTable({
                        destroy: true,
                        processing: true,
                        serverSide: false,
                        ajax: {
                            type: 'GET',
                            url: '/db/service-reports/machine/{{ $fixed->id_machine }}'
                        },
                        columns: [
                            { data: 'no_service' },
                            { data: 'type' },
                            { data: 'jobdesc' },
                            { data: 'date' },
                            { data: 'technician' },
                        ],
                        columnDefs: [
                            {
                                targets: 0,
                                render: function(data, type, full) {
                                    var url = '{{ url('service-reports') }}/' + (full.id || '');
                                    return '<a href="' + url + '" class="fw-semibold text-primary">' + (data ?? '-') + '</a>';
                                }
                            },
                            {
                                targets: 2,
                                render: function(data) {
                                    if (!data) return '-';
                                    return data.length > 55 ? ('<span title="' + data.replace(/"/g, '&quot;') + '">' + data.substring(0, 52) + '...</span>') : data;
                                }
                            },
                            {
                                targets: 3,
                                className: 'text-center',
                                render: function(data) {
                                    if (!data) return '-';
                                    var d = new Date(data);
                                    return ('0' + d.getDate()).slice(-2) + '-' + ('0' + (d.getMonth() + 1)).slice(-2) + '-' + d.getFullYear();
                                }
                            },
                        ],
                        order: [[3, 'desc']],
                        dom: '<"row px-3 pt-3 pb-2"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-end"f>>t<"row px-3 pt-2 pb-3"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6 d-flex justify-content-end"p>>',
                    });

                    $('button[data-bs-target="#tab-service-report"]').on('shown.bs.tab', function() {
                        if (dtServiceReport) {
                            dtServiceReport.columns.adjust().draw(false);
                        }
                    });
                }
            @endif
        });
    </script>
@endpush
