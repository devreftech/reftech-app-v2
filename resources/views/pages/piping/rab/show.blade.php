@extends('layouts.sales.app')
@section('title', 'Detail RAB: ' . $rab->no_rab)

@push('before-style')
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
<style>
    html, body {
        max-width: 100vw !important;
        overflow-x: hidden !important;
    }
    .layout-wrapper, .layout-container, .layout-page, .content-wrapper {
        max-width: 100% !important;
        overflow-x: hidden !important;
    }
    .content-wrapper > .container-fluid {
        max-width: 100% !important;
        overflow-x: hidden !important;
        box-sizing: border-box !important;
    }

    /* Top Header Toolbar */
    .rab-header-box {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 18px 24px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.02);
        margin-bottom: 20px;
    }

    /* Executive KPI Stat Cards */
    .stat-kpi-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 16px 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.02);
        transition: all 0.2s ease;
        height: 100%;
        position: relative;
        overflow: hidden;
    }
    .stat-kpi-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(0,0,0,0.06);
    }
    .stat-kpi-card.highlight-primary {
        background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
        color: #ffffff;
        border-color: #0284c7;
    }
    .stat-kpi-card.highlight-primary .text-muted {
        color: rgba(255, 255, 255, 0.8) !important;
    }
    .stat-kpi-card.highlight-primary .stat-title {
        color: rgba(255, 255, 255, 0.9) !important;
    }
    .stat-kpi-card.highlight-primary .stat-value {
        color: #ffffff !important;
    }
    .stat-icon-circle {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        flex-shrink: 0;
    }

    /* Project Detail Metadata Tiles */
    .project-info-tile {
        background: #f8fafc;
        border: 1px solid #edf2f7;
        border-radius: 8px;
        padding: 12px 16px;
        height: 100%;
    }
    .project-info-tile .tile-label {
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #64748b;
        margin-bottom: 4px;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .project-info-tile .tile-value {
        font-size: 13.5px;
        font-weight: 600;
        color: #1e293b;
    }

    /* Section Cards & Tables */
    .rab-show-card {
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        background: #ffffff;
        box-shadow: 0 2px 8px rgba(0,0,0,0.03);
        margin-bottom: 24px;
        overflow: hidden;
    }
    .section-header-clean {
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        padding: 12px 20px;
    }
    .table-responsive {
        width: 100% !important;
        overflow-x: auto;
    }
    .rab-show-table {
        width: 100% !important;
        margin-bottom: 0 !important;
    }
    .rab-show-table th {
        background: #f1f5f9;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #475569;
        padding: 10px 12px;
        vertical-align: middle;
        border-bottom: 1px solid #cbd5e1;
    }
    .rab-show-table td {
        padding: 10px 12px;
        vertical-align: middle;
        border-color: #f1f5f9;
        font-size: 12.5px;
    }
    .rab-show-table tr:hover td {
        background: #f8fafc;
    }

    /* Smooth Revision Tab Animations */
    .revision-pane {
        will-change: opacity, transform;
    }
    .revision-pane.fade-in {
        animation: revFadeIn 0.28s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }
    .revision-pane.fade-out {
        animation: revFadeOut 0.16s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }
    @keyframes revFadeIn {
        0% {
            opacity: 0;
            transform: translateY(8px) scale(0.995);
        }
        100% {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }
    @keyframes revFadeOut {
        0% {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
        100% {
            opacity: 0;
            transform: translateY(-8px) scale(0.995);
        }
    }

    .rev-tab-btn {
        cursor: pointer;
        transition: all 0.2s ease;
        font-size: 12px;
        font-weight: 600;
        border: 1px solid #cbd5e1;
        outline: none;
    }
    .rev-tab-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 3px 8px rgba(0,0,0,0.08);
        border-color: #0284c7;
    }
    .rev-tab-btn.active {
        background-color: #0284c7 !important;
        color: #ffffff !important;
        border-color: #0284c7 !important;
        box-shadow: 0 2px 10px rgba(2, 132, 199, 0.35) !important;
    }

    /* Live Row Flash Effect on Vendor/HPP Change */
    @keyframes rowHighlightSuccess {
        0% { background-color: rgba(34, 197, 94, 0.28); }
        100% { background-color: transparent; }
    }
    .row-highlight-flash > td {
        animation: rowHighlightSuccess 2s ease-out;
    }

    /* Vendor Comparison Modal Cards */
    .vendor-compare-card {
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 14px 16px;
        background: #ffffff;
        transition: all 0.2s ease;
    }
    .vendor-compare-card:hover {
        border-color: #0284c7;
        box-shadow: 0 4px 14px rgba(2, 132, 199, 0.08);
    }
    .vendor-compare-card.active-vendor {
        border-color: #22c55e;
        background: #f0fdf4;
    }

    /* Select2 in Bootstrap Modal */
    .select2-container {
        width: 100% !important;
        z-index: 1060;
    }
    .select2-container--default .select2-selection--single {
        height: 38px;
        border: 1px solid #d9dee3;
        border-radius: 6px;
        display: flex;
        align-items: center;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 36px;
        padding-left: 12px;
        color: #566a7f;
        font-size: 13px;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px;
        right: 8px;
    }
    .select2-dropdown {
        border-color: #d9dee3;
        box-shadow: 0 4px 16px rgba(0,0,0,0.12);
        border-radius: 8px;
        z-index: 1070 !important;
    }
    .select2-search--dropdown .select2-search__field {
        border-radius: 6px;
        border: 1px solid #d9dee3;
        padding: 6px 10px;
        font-size: 13px;
    }

    @media print {
        .no-print {
            display: none !important;
        }
        .layout-menu, .layout-navbar, .content-footer {
            display: none !important;
        }
        .content-wrapper {
            padding: 0 !important;
            margin: 0 !important;
        }
    }
</style>
@endpush

@section('content')
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show no-print mb-3" role="alert">
            <i class="mdi mdi-check-circle-outline me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show no-print mb-3" role="alert">
            <i class="mdi mdi-alert-circle-outline me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Revision History Instant Tab Bar (Without Page Reload) -->
    @if($revisions->count() > 1)
        <div class="alert alert-info py-2 px-3 mb-4 d-flex flex-wrap align-items-center justify-content-between gap-2 no-print shadow-xs" style="border-radius: 10px; background-color: #f0f9ff; border-color: #bae6fd;">
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <i class="mdi mdi-history fs-5 text-primary"></i>
                <span class="small fw-bold text-dark me-1">Riwayat Revisi Dokumen:</span>
                @foreach($revisions as $rev)
                    <button type="button" 
                            onclick="switchRevisionTab({{ $rev->id }})" 
                            data-rev-id="{{ $rev->id }}" 
                            class="rev-tab-btn badge {{ $rev->id === $rab->id ? 'active' : 'bg-white text-dark border' }} text-decoration-none px-3 py-2">
                        <i class="mdi {{ $rev->revision_number === 0 ? 'mdi-file-star-outline' : 'mdi-source-branch' }} me-1"></i>
                        {{ $rev->revision_number === 0 ? 'Utama' : 'Rev ' . $rev->revision_number }}
                        @if($rev->is_latest) <span class="badge bg-success ms-1" style="font-size: 8.5px;">Latest</span> @endif
                    </button>
                @endforeach
            </div>
            <span class="small text-muted d-none d-md-inline" style="font-size: 11px;">
                <i class="mdi mdi-cursor-default-click-outline me-1"></i>Klik tab untuk beralih revisi secara instan
            </span>
        </div>
    @endif

    <!-- Revision Content Panes (Seamless Animated Switch) -->
    <div id="revisionContentPanes">
        @foreach($revisions as $currentRab)
            @php
                $isCurrent = $currentRab->id === $rab->id;
                $marginPct = $currentRab->total_hpp > 0 ? round(($currentRab->total_margin / $currentRab->total_hpp) * 100, 1) : 0;
                $totalItemsCount = $currentRab->sections->sum(fn($s) => $s->items->count());
                $statusBadgeClass = match($currentRab->status) {
                    'Converted' => 'bg-success',
                    'Approved'  => 'bg-info',
                    'Cancelled' => 'bg-danger',
                    default     => 'bg-warning'
                };
            @endphp
            <div id="rev_pane_{{ $currentRab->id }}" class="revision-pane {{ $isCurrent ? 'active' : 'd-none' }}">
                <!-- 1. Top Header Toolbar -->
                <div class="rab-header-box no-print">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                        <div>
                            <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                                <a href="{{ route('piping-rab.index') }}" class="btn btn-sm btn-outline-secondary p-1 px-2 me-1" title="Kembali ke Daftar RAB">
                                    <i class="mdi mdi-arrow-left fs-6"></i>
                                </a>
                                <h4 class="fw-bold mb-0 text-dark font-monospace">{{ $currentRab->no_rab }}</h4>
                                @if($currentRab->revision_number > 0)
                                    <span class="badge bg-label-info fw-semibold">Revisi {{ $currentRab->revision_number }}</span>
                                @else
                                    <span class="badge bg-label-primary fw-semibold">Versi Utama</span>
                                @endif
                                <span class="badge {{ $statusBadgeClass }} fw-semibold px-2 py-1">
                                    {{ $currentRab->status }}
                                </span>
                            </div>
                            <div class="d-flex align-items-center gap-2 text-muted small">
                                <span class="fw-semibold text-dark">{{ $currentRab->project_name }}</span>
                                <span>&bull;</span>
                                <span><i class="mdi mdi-domain me-1"></i>{{ $currentRab->client ? $currentRab->client->company : 'Klien Umum' }}</span>
                                <span>&bull;</span>
                                <span><i class="mdi mdi-calendar-outline me-1"></i>{{ $currentRab->rab_date ? $currentRab->rab_date->format('d M Y') : '-' }}</span>
                            </div>
                        </div>

                        <!-- Action Buttons for this specific revision -->
                        <div class="d-flex flex-wrap gap-2">
                            <button type="button" class="btn btn-outline-secondary btn-sm px-3" onclick="window.print()">
                                <i class="mdi mdi-printer me-1"></i> Cetak RAB
                            </button>
                            <a href="{{ route('piping-rab.edit', $currentRab->id) }}" class="btn btn-outline-primary btn-sm px-3">
                                <i class="mdi mdi-pencil me-1"></i> Edit RAB
                            </a>
                            <form action="{{ route('piping-rab.revise', $currentRab->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Buat revisi baru dari RAB ini?')">
                                @csrf
                                <button type="submit" class="btn btn-outline-info btn-sm px-3">
                                    <i class="mdi mdi-source-branch me-1"></i> Buat Revisi Baru
                                </button>
                            </form>
                            @if($currentRab->status !== 'Converted')
                                <button type="button" class="btn btn-success btn-sm px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalConvertQuote" onclick="prepareConvertModal({{ $currentRab->id }})">
                                    <i class="mdi mdi-file-export-outline me-1"></i> Convert to Smart Quote
                                </button>
                            @else
                                @if($currentRab->convertedQuotation)
                                    <a href="{{ route('quotation.show', $currentRab->convertedQuotation->id) }}" class="btn btn-success btn-sm px-3 shadow-sm">
                                        <i class="mdi mdi-open-in-new me-1"></i> Buka Smart Quote ({{ $currentRab->convertedQuotation->no_quote }})
                                    </a>
                                @endif
                                <button type="button" class="btn btn-outline-success btn-sm px-3" data-bs-toggle="modal" data-bs-target="#modalConvertQuote" onclick="prepareConvertModal({{ $currentRab->id }})" title="Convert ulang ke Smart Quote dengan opsi atau mode baru">
                                    <i class="mdi mdi-refresh me-1"></i> Convert Ulang
                                </button>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- 2. Financial KPI Metric Widgets (4 Stat Cards) -->
                <div class="row g-3 mb-4">
                    <!-- Stat 1: HPP Modal -->
                    <div class="col-sm-6 col-xl-3">
                        <div class="stat-kpi-card">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="text-muted fw-semibold" style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;">Total HPP Modal</span>
                                <div class="stat-icon-circle bg-label-secondary text-secondary">
                                    <i class="mdi mdi-tag-outline"></i>
                                </div>
                            </div>
                            <div class="fs-4 fw-bold text-dark mb-1 font-monospace" id="kpi_hpp_{{ $currentRab->id }}">Rp {{ number_format($currentRab->total_hpp, 0, ',', '.') }}</div>
                            <small class="text-muted" style="font-size: 11px;">Biaya modal material & jasa</small>
                        </div>
                    </div>

                    <!-- Stat 2: Gross Margin -->
                    <div class="col-sm-6 col-xl-3">
                        <div class="stat-kpi-card">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="text-muted fw-semibold" style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;">Gross Margin Proyek</span>
                                <div class="stat-icon-circle bg-label-success text-success">
                                    <i class="mdi mdi-trending-up"></i>
                                </div>
                            </div>
                            <div class="fs-4 fw-bold text-success mb-1 font-monospace" id="kpi_margin_{{ $currentRab->id }}">Rp {{ number_format($currentRab->total_margin, 0, ',', '.') }}</div>
                            <div class="d-flex align-items-center gap-1">
                                <span class="badge bg-label-success" id="kpi_margin_pct_{{ $currentRab->id }}" style="font-size: 10px;">+{{ $marginPct }}% Margin</span>
                                <small class="text-muted" style="font-size: 11px;">Keuntungan kotor</small>
                            </div>
                        </div>
                    </div>

                    <!-- Stat 3: Grand Total Jual (Highlight) -->
                    <div class="col-sm-6 col-xl-3">
                        <div class="stat-kpi-card highlight-primary shadow">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="stat-title fw-semibold" style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;">Grand Total Estimasi Jual</span>
                                <div class="stat-icon-circle bg-white text-primary">
                                    <i class="mdi mdi-cash-multiple"></i>
                                </div>
                            </div>
                            <div class="fs-4 fw-bold stat-value mb-1 font-monospace" id="kpi_sell_{{ $currentRab->id }}">Rp {{ number_format($currentRab->total_selling_price, 0, ',', '.') }}</div>
                            <small class="text-muted" style="font-size: 11px;">Nilai penawaran ke klien</small>
                        </div>
                    </div>

                    <!-- Stat 4: Scope & Sections -->
                    <div class="col-sm-6 col-xl-3">
                        <div class="stat-kpi-card">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="text-muted fw-semibold" style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;">Scope & Item Pekerjaan</span>
                                <div class="stat-icon-circle bg-label-primary text-primary">
                                    <i class="mdi mdi-pipe-wrench"></i>
                                </div>
                            </div>
                            <div class="fs-4 fw-bold text-dark mb-1">{{ $currentRab->sections->count() }} <span class="fs-6 fw-normal text-muted">Area</span></div>
                            <small class="text-muted" style="font-size: 11px;">Total {{ $totalItemsCount }} item pipa, fitting & jasa</small>
                        </div>
                    </div>
                </div>

                <!-- 3. Detail Informasi Proyek & Klien Card -->
                <div class="rab-show-card mb-4">
                    <div class="section-header-clean d-flex justify-content-between align-items-center">
                        <h6 class="fw-bold text-dark mb-0"><i class="mdi mdi-information-outline me-2 text-primary"></i>Informasi Lengkap Proyek & Klien</h6>
                        <span class="badge bg-label-secondary" style="font-size: 11px;">ID Dokumen #{{ $currentRab->id }}</span>
                    </div>
                    <div class="p-4">
                        <div class="row g-3">
                            <div class="col-md-4 col-sm-6">
                                <div class="project-info-tile">
                                    <div class="tile-label"><i class="mdi mdi-domain text-primary"></i> Customer / Perusahaan</div>
                                    <div class="tile-value">{{ $currentRab->client ? $currentRab->client->company : '-' }}</div>
                                    <small class="text-muted" style="font-size: 11px;">{{ $currentRab->client ? $currentRab->client->name : '' }}</small>
                                </div>
                            </div>

                            <div class="col-md-4 col-sm-6">
                                <div class="project-info-tile">
                                    <div class="tile-label"><i class="mdi mdi-account-tie text-primary"></i> PIC Customer</div>
                                    <div class="tile-value">{{ $currentRab->pic ? $currentRab->pic->name : '-' }}</div>
                                    <small class="text-muted" style="font-size: 11px;">{{ $currentRab->pic && $currentRab->pic->phone ? $currentRab->pic->phone : ($currentRab->client ? $currentRab->client->phone : '-') }}</small>
                                </div>
                            </div>

                            <div class="col-md-4 col-sm-6">
                                <div class="project-info-tile">
                                    <div class="tile-label"><i class="mdi mdi-map-marker-radius-outline text-primary"></i> Lokasi / Plant Area</div>
                                    <div class="tile-value">{{ $currentRab->location_plant ?: '-' }}</div>
                                    <small class="text-muted" style="font-size: 11px;">Area instalasi sistem piping</small>
                                </div>
                            </div>

                            <div class="col-md-4 col-sm-6">
                                <div class="project-info-tile">
                                    <div class="tile-label"><i class="mdi mdi-calendar-check text-primary"></i> Tanggal RAB</div>
                                    <div class="tile-value">{{ $currentRab->rab_date ? $currentRab->rab_date->format('d F Y') : '-' }}</div>
                                    <small class="text-muted" style="font-size: 11px;">Dibuat: {{ $currentRab->created_at ? $currentRab->created_at->format('d/m/Y H:i') : '-' }}</small>
                                </div>
                            </div>

                            <div class="col-md-4 col-sm-6">
                                <div class="project-info-tile">
                                    <div class="tile-label"><i class="mdi mdi-badge-account-outline text-primary"></i> Sales Person</div>
                                    <div class="tile-value">{{ $currentRab->sales ? $currentRab->sales->name : '-' }}</div>
                                    <small class="text-muted" style="font-size: 11px;">PIC Sales Reftech</small>
                                </div>
                            </div>

                            <div class="col-md-4 col-sm-6">
                                <div class="project-info-tile">
                                    <div class="tile-label"><i class="mdi mdi-account-edit-outline text-primary"></i> Dibuat Oleh (Admin/Estimator)</div>
                                    <div class="tile-value">{{ $currentRab->admin ? $currentRab->admin->name : '-' }}</div>
                                    <small class="text-muted" style="font-size: 11px;">Estimator RAB Internal</small>
                                </div>
                            </div>

                            @if($currentRab->notes)
                                <div class="col-12">
                                    <div class="p-3 bg-light rounded border" style="font-size: 12.5px;">
                                        <strong class="text-dark"><i class="mdi mdi-note-text-outline me-1 text-primary"></i>Catatan Teknis Internal:</strong>
                                        <span class="text-muted ms-1">{{ $currentRab->notes }}</span>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- 4. Section Details -->
                @foreach($currentRab->sections as $sIdx => $sec)
                    <div class="rab-show-card mb-4" id="section_card_{{ $sec->id }}">
                        <div class="section-header-clean d-flex flex-wrap justify-content-between align-items-center gap-2">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-primary rounded p-1"><i class="mdi mdi-folder-open-outline fs-6"></i></span>
                                <h6 class="mb-0 fw-bold text-dark">{{ $sec->section_name }}</h6>
                            </div>
                            <div class="d-flex gap-3 align-items-center">
                                <small class="text-muted">Subtotal HPP: <strong class="text-dark font-monospace" id="sec_hpp_{{ $sec->id }}">Rp {{ number_format($sec->subtotal_hpp, 0, ',', '.') }}</strong></small>
                                <span class="badge bg-label-primary fs-6 px-3 py-2 font-monospace" id="sec_sell_{{ $sec->id }}">Subtotal Jual: Rp {{ number_format($sec->subtotal_selling_price, 0, ',', '.') }}</span>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table rab-show-table align-middle">
                                <thead>
                                    <tr>
                                        <th style="width: 35px;" class="text-center">#</th>
                                        <th style="width: 27%;">Uraian Item / Spesifikasi</th>
                                        <th style="width: 13%;" class="text-center">Kalkulasi Meter</th>
                                        <th style="width: 10%;" class="text-center">Qty / Satuan</th>
                                        <th style="width: 13%;" class="text-end">HPP / Unit</th>
                                        <th style="width: 18%;">Supplier / Vendor</th>
                                        <th style="width: 7%;" class="text-center">Margin</th>
                                        <th style="width: 12%;" class="text-end">Total Jual (Rp)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($sec->items as $iIdx => $item)
                                        @php
                                            $vendorPricesData = [];
                                            if ($item->material && $item->material->vendorPrices) {
                                                foreach ($item->material->vendorPrices as $vp) {
                                                    $vendorPricesData[] = [
                                                        'id_supplier'    => $vp->id_supplier,
                                                        'supplier_name'  => $vp->supplier ? $vp->supplier->supplier : 'Supplier #' . $vp->id_supplier,
                                                        'supplier_phone' => $vp->supplier && $vp->supplier->phone ? $vp->supplier->phone : null,
                                                        'supplier_area'  => $vp->supplier && $vp->supplier->area ? $vp->supplier->area : null,
                                                        'price_idr'      => (float) $vp->price_idr,
                                                        'date'           => $vp->date ? $vp->date->format('d/m/Y') : null,
                                                        'notes'          => $vp->notes,
                                                        'is_primary'     => (bool) $vp->is_primary,
                                                    ];
                                                }
                                            }

                                            $itemPayload = [
                                                'id'                 => $item->id,
                                                'rab_id'             => $currentRab->id,
                                                'section_id'         => $sec->id,
                                                'item_name'          => $item->item_name,
                                                'size'               => $item->size,
                                                'calculated_qty'     => (float)$item->calculated_qty,
                                                'unit'               => $item->unit == 'Batang' ? 'Btg' : $item->unit,
                                                'id_supplier'        => $item->id_supplier,
                                                'supplier_name'      => $item->supplier ? $item->supplier->supplier : '-',
                                                'unit_price_hpp'     => (float)$item->unit_price_hpp,
                                                'margin_type'        => $item->margin_type,
                                                'margin_value'       => (float)$item->margin_value,
                                                'unit_selling_price' => (float)$item->unit_selling_price,
                                                'vendor_prices'      => $vendorPricesData,
                                            ];
                                        @endphp
                                        <tr id="show_item_row_{{ $item->id }}" data-item-id="{{ $item->id }}">
                                            <td class="text-center text-muted fw-semibold small">{{ $iIdx + 1 }}</td>
                                            <td>
                                                <div class="fw-bold text-dark">{{ $item->item_name }}</div>
                                                @if($item->size)
                                                    <span class="badge bg-label-secondary" style="font-size: 11px;">{{ $item->size }}</span>
                                                @endif
                                                @if($item->spec)
                                                    <small class="text-muted d-block">{{ $item->spec }}</small>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if($item->input_length_meter)
                                                    <span class="fw-semibold text-dark">{{ (float)$item->input_length_meter }} m</span>
                                                    @if($item->waste_percent > 0)
                                                        <small class="text-muted d-block" style="font-size: 10px;">(+{{ (float)$item->waste_percent }}% waste)</small>
                                                    @endif
                                                    @if($item->length_per_unit)
                                                        <small class="text-muted d-block" style="font-size: 9.5px;">(1 btg = {{ (float)$item->length_per_unit }}m)</small>
                                                    @endif
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <span class="fw-bold text-primary fs-6" id="cell_qty_{{ $item->id }}">{{ (float)$item->calculated_qty }}</span>
                                                <small class="text-muted d-block" style="font-size: 11px;">{{ $item->unit == 'Batang' ? 'Btg' : $item->unit }}</small>
                                            </td>
                                            <td class="text-end text-muted font-monospace" id="cell_hpp_{{ $item->id }}">
                                                <span>Rp {{ number_format($item->unit_price_hpp, 0, ',', '.') }}</span>
                                            </td>
                                            <td id="cell_supplier_{{ $item->id }}">
                                                <div class="d-flex flex-column gap-1">
                                                    <div class="fw-semibold text-dark text-truncate item-supplier-text" style="max-width: 170px;" title="{{ $item->supplier ? $item->supplier->supplier : 'Belum Ada Supplier' }}">
                                                        <i class="mdi mdi-storefront-outline text-muted me-1"></i>
                                                        <span class="supplier-label-name">{{ $item->supplier ? $item->supplier->supplier : '-' }}</span>
                                                    </div>
                                                    <button type="button" 
                                                            class="btn btn-xs btn-outline-primary py-0 px-2 d-inline-flex align-items-center gap-1 shadow-none no-print btn-vendor-modal" 
                                                            style="font-size: 11px; border-radius: 6px; width: fit-content;"
                                                            data-vendor-info="{{ json_encode($itemPayload, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) }}"
                                                            onclick="onVendorButtonClick(this)"
                                                            title="Klik untuk bandingkan vendor & ganti supplier">
                                                        <i class="mdi mdi-swap-horizontal text-primary"></i>
                                                        <span>{{ count($vendorPricesData) > 0 ? count($vendorPricesData) . ' Vendor' : 'Ganti Vendor' }}</span>
                                                    </button>
                                                </div>
                                            </td>
                                            <td class="text-center" id="cell_margin_{{ $item->id }}">
                                                @if($item->margin_type === 'percent')
                                                    <span class="badge bg-label-success margin-badge">+{{ (float)$item->margin_value }}%</span>
                                                @else
                                                    <span class="badge bg-label-success margin-badge">+Rp {{ number_format($item->margin_value, 0, ',', '.') }}</span>
                                                @endif
                                                <small class="text-muted d-block unit-sell-subtext font-monospace" style="font-size: 9.5px;">@ Rp {{ number_format($item->unit_selling_price, 0, ',', '.') }}</small>
                                            </td>
                                            <td class="text-end fw-bold text-primary fs-6 font-monospace" id="cell_total_sell_{{ $item->id }}">
                                                Rp {{ number_format($item->total_selling_price, 0, ',', '.') }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center py-4 text-muted">Tidak ada item di section ini.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endforeach
            </div>
        @endforeach
    </div>

    <!-- Modal 1: Vendor Comparison & Switcher (Live AJAX Update) -->
    <div class="modal fade" id="modalVendorComparison" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 12px;">
                <div class="modal-header bg-light pb-3">
                    <div class="d-flex align-items-center gap-2">
                        <div class="stat-icon-circle bg-primary text-white" style="width: 36px; height: 36px; font-size: 18px;">
                            <i class="mdi mdi-store-search-outline"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold text-dark mb-0">Bandingkan & Ganti Vendor / Supplier</h5>
                            <small class="text-muted">Pilih supplier penyedia material terbaik & sesuaikan HPP otomatis</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-4">
                    <!-- Item Header Context -->
                    <div class="p-3 mb-4 rounded border bg-light-subtle d-flex flex-wrap justify-content-between align-items-center gap-3" style="background-color: #f8fafc; border-color: #e2e8f0;">
                        <div>
                            <div class="fw-bold text-dark fs-6" id="m_item_name">-</div>
                            <div class="d-flex align-items-center gap-2 small text-muted">
                                <span id="m_item_size_badge" class="badge bg-label-secondary">-</span>
                                <span>Qty Kebutuhan: <strong class="text-dark" id="m_item_qty">-</strong></span>
                                <span>&bull;</span>
                                <span>Margin Aktif: <strong class="text-success" id="m_item_margin">-</strong></span>
                            </div>
                        </div>
                        <div class="text-end">
                            <div class="small text-muted">HPP Aktif Saat Ini:</div>
                            <div class="fs-5 fw-bold text-primary font-monospace" id="m_item_current_hpp">Rp 0</div>
                            <small class="text-muted" id="m_item_current_supplier">Supplier: -</small>
                        </div>
                    </div>

                    <!-- Vendor List Cards -->
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="fw-bold text-dark mb-0">
                                <i class="mdi mdi-format-list-checks me-1 text-primary"></i> Daftar Vendor Terdaftar di Master Pricelist:
                            </h6>
                            <span class="badge bg-label-info" id="m_vendor_count_badge">0 Vendor</span>
                        </div>

                        <div id="m_vendor_list_container" class="d-flex flex-column gap-2">
                            <!-- Populated via JavaScript -->
                        </div>
                    </div>

                    <!-- Option 2: Custom Supplier or Manual HPP Input -->
                    <div class="accordion" id="accordionCustomSupplier">
                        <div class="accordion-item border rounded" style="overflow: hidden;">
                            <h2 class="accordion-header" id="headingCustom">
                                <button class="accordion-button collapsed py-2 px-3 bg-light text-dark fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseCustom" aria-expanded="false" aria-controls="collapseCustom" style="font-size: 13px;">
                                    <i class="mdi mdi-pencil-ruler me-2 text-primary"></i> Atau Pilih Supplier Lain / Input Custom HPP Manual
                                </button>
                            </h2>
                            <div id="collapseCustom" class="accordion-collapse collapse" aria-labelledby="headingCustom" data-bs-parent="#accordionCustomSupplier">
                                <div class="accordion-body p-3 bg-white">
                                    <!-- Quick Add Supplier Inline Form -->
                                    <div id="quickAddSupplierBox" class="p-3 mb-3 bg-light rounded border border-primary border-opacity-25 shadow-xs d-none">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="fw-bold text-primary small"><i class="mdi mdi-domain-plus me-1"></i> Tambah Master Supplier Baru</span>
                                            <button type="button" class="btn-close btn-sm" style="font-size: 10px;" onclick="toggleQuickAddSupplier()"></button>
                                        </div>
                                        <div class="row g-2">
                                            <div class="col-md-6">
                                                <label class="form-label small required mb-1">Nama Perusahaan / Supplier</label>
                                                <input type="text" id="new_sup_name" class="form-control form-control-sm" placeholder="Contoh: PT Surya Baja Mandiri">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label small mb-1">No. Telp / WhatsApp</label>
                                                <input type="text" id="new_sup_phone" class="form-control form-control-sm" placeholder="Contoh: 08123456789">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label small mb-1">Email</label>
                                                <input type="email" id="new_sup_email" class="form-control form-control-sm" placeholder="supplier@email.com">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label small mb-1">Alamat / Kota</label>
                                                <input type="text" id="new_sup_address" class="form-control form-control-sm" placeholder="Jakarta Barat / Cikarang">
                                            </div>
                                            <div class="col-12 text-end mt-2">
                                                <button type="button" class="btn btn-xs btn-outline-secondary me-1" onclick="toggleQuickAddSupplier()">Batal</button>
                                                <button type="button" class="btn btn-xs btn-success px-3" id="btnSaveNewSupplier" onclick="saveNewSupplier()">
                                                    <i class="mdi mdi-content-save-outline me-1"></i> Simpan & Pilih Supplier
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <form id="formCustomVendor" onsubmit="applyCustomVendor(event)">
                                        <div class="row g-3 align-items-end">
                                            <div class="col-md-6">
                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                    <label class="form-label required small fw-bold mb-0">Pilih Supplier</label>
                                                    <button type="button" class="btn btn-link text-primary p-0" style="font-size: 11.5px; text-decoration: none;" onclick="toggleQuickAddSupplier()">
                                                        <i class="mdi mdi-plus-circle-outline me-1"></i>+ Tambah Supplier Baru
                                                    </button>
                                                </div>
                                                <select id="custom_supplier_select" class="form-select" style="width: 100%;">
                                                    <option value="">-- Cari / Pilih Supplier --</option>
                                                    @if(isset($allSuppliers))
                                                        @foreach($allSuppliers as $sup)
                                                            <option value="{{ $sup->id }}">{{ $sup->supplier }}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label required small fw-bold mb-1">Harga Modal HPP Satuan (Rp)</label>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text">Rp</span>
                                                    <input type="text" id="custom_hpp_input" class="form-control font-monospace" placeholder="0" oninput="formatRupiahInput(this); calculateCustomPreview();" required>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-check form-switch mb-2">
                                                    <input class="form-check-input" type="checkbox" id="check_save_to_master" checked>
                                                    <label class="form-check-label small text-muted" for="check_save_to_master">
                                                        <i class="mdi mdi-database-plus-outline text-primary me-1"></i> Simpan juga harga & supplier ini ke <strong>Master Pricelist Material</strong>
                                                    </label>
                                                </div>
                                                <div class="p-2 rounded bg-light border d-flex justify-content-between align-items-center" style="font-size: 12px;">
                                                    <div>
                                                        <span class="text-muted">Simulasi Penjualan:</span>
                                                        <span class="fw-semibold text-dark ms-1">Subtotal HPP: <span id="custom_prev_hpp" class="font-monospace">Rp 0</span></span>
                                                        <span class="mx-1">&bull;</span>
                                                        <span class="fw-bold text-success">Subtotal Jual: <span id="custom_prev_sell" class="font-monospace">Rp 0</span></span>
                                                    </div>
                                                    <button type="submit" class="btn btn-sm btn-primary px-3 shadow-sm" id="btn_apply_custom">
                                                        <i class="mdi mdi-check me-1"></i> Terapkan Supplier & HPP Ini
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-outline-secondary btn-sm px-4" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal 2: Convert to Smart Quote (Dynamic Target) -->
    <div class="modal fade" id="modalConvertQuote" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form id="formConvertQuote" action="{{ route('piping-rab.convert', $rab->id) }}" method="POST" class="modal-content">
                @csrf
                <div class="modal-header bg-light">
                    <div>
                        <h5 class="modal-title fw-bold text-success"><i class="mdi mdi-file-export-outline me-2"></i>Convert RAB ke Smart Quote</h5>
                        <small class="text-muted">Proses penawaran harga resmi untuk tim Sales</small>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning py-2 mb-3" style="font-size: 13px;">
                        <i class="mdi mdi-shield-check-outline me-1"></i> <strong>Kerahasiaan Terjamin:</strong> Data HPP modal supplier dan rincian margin internal tidak akan tampil di PDF penawaran klien.
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold required">Pilih Mode Tampilan Harga di Penawaran:</label>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-check custom-option custom-option-basic p-3 border rounded">
                                    <input class="form-check-input" type="radio" name="conversion_mode" id="modeLumpSum" value="lumpsum" checked>
                                    <label class="form-check-label" for="modeLumpSum">
                                        <span class="custom-option-header mb-1">
                                            <strong class="text-primary"><i class="mdi mdi-package-variant-closed me-1"></i>Mode LUMPSUM / PAKET (Direkomendasikan)</strong>
                                        </span>
                                        <small class="text-muted d-block">
                                            Harga disatukan per section (misal: "Pekerjaan Instalasi Piping Plant A: 1 Lot"). Menghindari customer membandingkan harga satuan item ke toko lain.
                                        </small>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check custom-option custom-option-basic p-3 border rounded">
                                    <input class="form-check-input" type="radio" name="conversion_mode" id="modeBreakdown" value="breakdown">
                                    <label class="form-check-label" for="modeBreakdown">
                                        <span class="custom-option-header mb-1">
                                            <strong class="text-dark"><i class="mdi mdi-format-list-bulleted me-1"></i>Mode BREAKDOWN (Rincian per Item)</strong>
                                        </span>
                                        <small class="text-muted d-block">
                                            Seluruh item pipa, fitting, dan jasa dimunculkan satu-per-satu beserta harga jual satuannya (cocok untuk tender formal BUMN).
                                        </small>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label required">Pajak (PPN)</label>
                            <select name="tax" class="form-select" required>
                                <option value="1">PPN 11% (Dikenakan Pajak)</option>
                                <option value="0">Non PPN (Tanpa Pajak)</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Masa Berlaku Penawaran</label>
                            <input type="text" name="validity" class="form-control" value="14 (empat belas) hari kalender">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Garansi Pekerjaan</label>
                            <input type="text" name="warranty" class="form-control" value="Garansi kebocoran & instalasi selama 6 bulan">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Syarat Pembayaran (Payment Term)</label>
                            <input type="text" name="payment" class="form-control" value="DP 30% saat PO, Pelunasan 70% setelah BAST">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Estimasi Waktu Pelaksanaan</label>
                            <input type="text" name="delivery_process" class="form-control" value="Estimasi 2-3 minggu setelah material siap di lokasi">
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success px-4">
                        <i class="mdi mdi-check-all me-1"></i> Buat Smart Quote Sekarang
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('after-script')
<script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
<script>
    let activeModalItem = null;

    $(document).ready(function() {
        $('#custom_supplier_select').select2({
            dropdownParent: $('#modalVendorComparison'),
            placeholder: '-- Cari / Pilih Supplier --',
            allowClear: true,
            width: '100%'
        });
    });

    function toggleQuickAddSupplier() {
        const box = document.getElementById('quickAddSupplierBox');
        if (box) {
            box.classList.toggle('d-none');
            if (!box.classList.contains('d-none')) {
                document.getElementById('new_sup_name').focus();
            }
        }
    }

    function saveNewSupplier() {
        const nameInput = document.getElementById('new_sup_name');
        const phoneInput = document.getElementById('new_sup_phone');
        const emailInput = document.getElementById('new_sup_email');
        const addressInput = document.getElementById('new_sup_address');
        const btnSave = document.getElementById('btnSaveNewSupplier');

        const supplierName = nameInput.value.trim();
        if (!supplierName) {
            alert('Nama Supplier / Perusahaan wajib diisi!');
            nameInput.focus();
            return;
        }

        const origHtml = btnSave.innerHTML;
        btnSave.disabled = true;
        btnSave.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...';

        fetch('{{ route("piping-rab.suppliers.quick-store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                supplier: supplierName,
                phone: phoneInput.value.trim(),
                email: emailInput.value.trim(),
                address: addressInput.value.trim()
            })
        })
        .then(res => res.json())
        .then(res => {
            btnSave.disabled = false;
            btnSave.innerHTML = origHtml;

            if (res.success && res.data) {
                const newOption = new Option(res.data.supplier, res.data.id, true, true);
                $('#custom_supplier_select').append(newOption).trigger('change');

                // Clear input
                nameInput.value = '';
                phoneInput.value = '';
                emailInput.value = '';
                addressInput.value = '';
                toggleQuickAddSupplier();

                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Supplier Ditambahkan!',
                        text: `Supplier "${res.data.supplier}" berhasil ditambahkan dan dipilih.`,
                        timer: 2000,
                        showConfirmButton: false,
                        toast: true,
                        position: 'top-end'
                    });
                }
            } else {
                alert(res.message || 'Gagal menyimpan supplier baru');
            }
        })
        .catch(err => {
            btnSave.disabled = false;
            btnSave.innerHTML = origHtml;
            console.error('Error saving supplier:', err);
            alert('Terjadi kesalahan koneksi saat menyimpan supplier.');
        });
    }

    function formatRupiahDisplay(number) {
        return 'Rp ' + new Intl.NumberFormat('id-ID').format(Math.round(number || 0));
    }

    function formatRupiahNumber(val) {
        if (val === null || val === undefined || val === '') return '0';
        let numStr = String(val).trim();
        if (/^\d+\.\d{1,2}$/.test(numStr)) {
            let parsed = parseFloat(numStr);
            return isNaN(parsed) ? '0' : new Intl.NumberFormat('id-ID').format(Math.round(parsed));
        }
        let clean = numStr.replace(/\D/g, '');
        let num = parseInt(clean, 10);
        return isNaN(num) ? '0' : new Intl.NumberFormat('id-ID').format(num);
    }

    function parseRupiahNumber(val) {
        if (!val) return 0;
        let str = String(val).trim();
        if (/^\d+\.\d{1,2}$/.test(str)) {
            return parseFloat(str) || 0;
        }
        let clean = str.replace(/\./g, '').replace(/,/g, '.').replace(/[^\d.]/g, '');
        return parseFloat(clean) || 0;
    }

    function formatRupiahInput(input) {
        const caretPos = input.selectionStart;
        const rawVal = input.value;
        const clean = rawVal.replace(/\D/g, '');
        if (!clean) {
            input.value = '';
            return;
        }
        const formatted = new Intl.NumberFormat('id-ID').format(parseInt(clean, 10));
        input.value = formatted;
    }

    function switchRevisionTab(revId) {
        const allBtns = document.querySelectorAll('.rev-tab-btn');
        const currentActive = document.querySelector('.revision-pane:not(.d-none)');
        const targetPane = document.getElementById('rev_pane_' + revId);

        if (!targetPane || currentActive === targetPane) return;

        allBtns.forEach(btn => {
            if (btn.getAttribute('data-rev-id') == revId) {
                btn.classList.add('active');
                btn.classList.remove('bg-white', 'text-dark', 'border');
            } else {
                btn.classList.remove('active');
                btn.classList.add('bg-white', 'text-dark', 'border');
            }
        });

        if (currentActive) {
            currentActive.classList.add('fade-out');
            setTimeout(() => {
                currentActive.classList.add('d-none');
                currentActive.classList.remove('fade-out', 'fade-in');

                targetPane.classList.remove('d-none');
                targetPane.classList.add('fade-in');

                window.history.pushState({ revId: revId }, '', '/piping-rab/' + revId);
                prepareConvertModal(revId);
            }, 150);
        } else {
            targetPane.classList.remove('d-none');
            targetPane.classList.add('fade-in');
        }
    }

    function prepareConvertModal(revId) {
        const form = document.getElementById('formConvertQuote');
        if (form) {
            form.action = '/piping-rab/' + revId + '/convert';
        }
    }

    function onVendorButtonClick(btn) {
        try {
            const dataStr = btn.getAttribute('data-vendor-info');
            if (dataStr) {
                const itemData = JSON.parse(dataStr);
                openVendorComparisonModal(itemData, btn);
            }
        } catch (err) {
            console.error('Failed to parse vendor info:', err);
        }
    }

    // Modal Vendor Comparison
    function openVendorComparisonModal(item, sourceBtn) {
        activeModalItem = item;
        activeModalItem.sourceBtn = sourceBtn;

        document.getElementById('m_item_name').innerText = item.item_name || 'Material Item';
        document.getElementById('m_item_size_badge').innerText = item.size ? item.size : '-';
        document.getElementById('m_item_qty').innerText = `${item.calculated_qty} ${item.unit}`;
        
        const marginStr = item.margin_type === 'percent' 
            ? `+${item.margin_value}%` 
            : `+${formatRupiahDisplay(item.margin_value)}`;
        document.getElementById('m_item_margin').innerText = marginStr;

        document.getElementById('m_item_current_hpp').innerText = formatRupiahDisplay(item.unit_price_hpp);
        document.getElementById('m_item_current_supplier').innerText = `Supplier: ${item.supplier_name || '-'}`;

        const container = document.getElementById('m_vendor_list_container');
        const badgeCount = document.getElementById('m_vendor_count_badge');
        container.innerHTML = '';

        const vps = item.vendor_prices || [];
        badgeCount.innerText = `${vps.length} Vendor Terdaftar`;

        if (vps.length === 0) {
            container.innerHTML = `
                <div class="alert alert-light border text-center py-3 text-muted">
                    <i class="mdi mdi-information-outline fs-5 d-block mb-1 text-secondary"></i>
                    Belum ada data pricelist vendor lain yang terdaftar pada master material ini.<br>
                    <span class="small">Gunakan menu di bawah untuk memilih supplier secara manual atau mengubah HPP.</span>
                </div>
            `;
        } else {
            // Sort vendors by price asc
            vps.sort((a, b) => parseFloat(a.price_idr) - parseFloat(b.price_idr));
            const lowestPrice = parseFloat(vps[0].price_idr);

            vps.forEach((vp, idx) => {
                const vpPrice = parseFloat(vp.price_idr);
                const isCurrent = (item.id_supplier && item.id_supplier == vp.id_supplier) && (Math.abs(item.unit_price_hpp - vpPrice) < 1);
                const isCheapest = Math.abs(vpPrice - lowestPrice) < 1;
                
                const diff = vpPrice - item.unit_price_hpp;
                let diffBadge = '';
                if (isCurrent) {
                    diffBadge = `<span class="badge bg-label-secondary font-monospace">Harga Saat Ini</span>`;
                } else if (diff < 0) {
                    diffBadge = `<span class="badge bg-label-success font-monospace"><i class="mdi mdi-arrow-down-bold"></i> Lebih Hemat ${formatRupiahDisplay(Math.abs(diff))}</span>`;
                } else if (diff > 0) {
                    diffBadge = `<span class="badge bg-label-warning font-monospace"><i class="mdi mdi-arrow-up-bold"></i> Lebih Mahal ${formatRupiahDisplay(diff)}</span>`;
                } else {
                    diffBadge = `<span class="badge bg-label-info font-monospace">Sama dengan HPP saat ini</span>`;
                }

                // Simulate selling price
                let simulatedUnitSell = 0;
                if (item.margin_type === 'percent') {
                    simulatedUnitSell = vpPrice + (vpPrice * (item.margin_value / 100));
                } else {
                    simulatedUnitSell = vpPrice + item.margin_value;
                }
                const simulatedTotalSell = simulatedUnitSell * item.calculated_qty;
                const simulatedTotalHpp = vpPrice * item.calculated_qty;

                const card = document.createElement('div');
                card.className = `vendor-compare-card ${isCurrent ? 'active-vendor' : ''}`;
                card.innerHTML = `
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                        <div class="d-flex align-items-start gap-3">
                            <div class="stat-icon-circle ${isCurrent ? 'bg-success text-white' : 'bg-label-primary text-primary'}" style="width: 40px; height: 40px; font-size: 20px;">
                                <i class="mdi ${isCurrent ? 'mdi-check-decagram' : 'mdi-domain'}"></i>
                            </div>
                            <div>
                                <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                                    <h6 class="fw-bold text-dark mb-0">${vp.supplier_name}</h6>
                                    ${isCurrent ? '<span class="badge bg-success" style="font-size: 10px;">✅ Sedang Digunakan</span>' : ''}
                                    ${isCheapest ? '<span class="badge bg-label-warning fw-semibold" style="font-size: 10px;">⭐ Termurah</span>' : ''}
                                    ${vp.is_primary ? '<span class="badge bg-label-primary" style="font-size: 10px;">🏢 Vendor Utama</span>' : ''}
                                </div>
                                <div class="d-flex align-items-center gap-2 text-muted small">
                                    ${vp.supplier_phone ? `<span><i class="mdi mdi-phone-outline me-1"></i>${vp.supplier_phone}</span><span>&bull;</span>` : ''}
                                    <span>Update: ${vp.date || '-'}</span>
                                    ${vp.notes ? `<span>&bull;</span><span>${vp.notes}</span>` : ''}
                                </div>
                                <div class="mt-2 text-muted small">
                                    Simulasi RAB: HPP Subtotal <strong class="text-dark font-monospace">${formatRupiahDisplay(simulatedTotalHpp)}</strong> &bull; Total Jual <strong class="text-primary font-monospace">${formatRupiahDisplay(simulatedTotalSell)}</strong>
                                </div>
                            </div>
                        </div>

                        <div class="text-end d-flex flex-column align-items-end gap-2">
                            <div>
                                <div class="fs-5 fw-bold text-dark font-monospace">${formatRupiahDisplay(vpPrice)} <small class="text-muted fs-6 fw-normal">/ ${item.unit}</small></div>
                                <div class="mt-1">${diffBadge}</div>
                            </div>
                            ${isCurrent ? `
                                <button type="button" class="btn btn-sm btn-outline-success px-3" disabled>
                                    <i class="mdi mdi-check me-1"></i> Terpasang
                                </button>
                            ` : `
                                <button type="button" class="btn btn-sm btn-primary px-3 shadow-sm btn-select-vendor">
                                    <i class="mdi mdi-swap-horizontal me-1"></i> Pilih Vendor Ini
                                </button>
                            `}
                        </div>
                    </div>
                `;

                if (!isCurrent) {
                    const selectBtn = card.querySelector('.btn-select-vendor');
                    if (selectBtn) {
                        selectBtn.addEventListener('click', () => {
                            applyVendor(item.id, vp.id_supplier, vpPrice, vp.supplier_name);
                        });
                    }
                }

                container.appendChild(card);
            });
        }

        // Reset custom form and select2
        $('#custom_supplier_select').val(item.id_supplier || '').trigger('change');
        const box = document.getElementById('quickAddSupplierBox');
        if (box) box.classList.add('d-none');

        const customHppInput = document.getElementById('custom_hpp_input');
        if (customHppInput) {
            customHppInput.value = formatRupiahNumber(item.unit_price_hpp);
        }
        calculateCustomPreview();

        const modalElem = new bootstrap.Modal(document.getElementById('modalVendorComparison'));
        modalElem.show();
    }

    function calculateCustomPreview() {
        if (!activeModalItem) return;
        const customHpp = parseRupiahNumber(document.getElementById('custom_hpp_input').value);
        const qty = activeModalItem.calculated_qty || 1;
        const totalHpp = customHpp * qty;

        let unitSell = 0;
        if (activeModalItem.margin_type === 'percent') {
            unitSell = customHpp + (customHpp * (activeModalItem.margin_value / 100));
        } else {
            unitSell = customHpp + activeModalItem.margin_value;
        }
        const totalSell = unitSell * qty;

        document.getElementById('custom_prev_hpp').innerText = formatRupiahDisplay(totalHpp);
        document.getElementById('custom_prev_sell').innerText = formatRupiahDisplay(totalSell);
    }

    function applyVendor(itemId, supplierId, hppPrice, supplierName, saveToMaster = false) {
        if (!confirm(`Terapkan supplier "${supplierName}" dengan HPP ${formatRupiahDisplay(hppPrice)}? Perhitungan HPP & harga jual RAB akan disesuaikan otomatis.`)) {
            return;
        }

        const modalEl = document.getElementById('modalVendorComparison');
        const modalInstance = bootstrap.Modal.getInstance(modalEl);

        fetch(`/piping-rab/items/${itemId}/update-supplier`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                id_supplier: supplierId,
                unit_price_hpp: hppPrice,
                save_to_master: saveToMaster
            })
        })
        .then(response => response.json())
        .then(res => {
            if (res.success) {
                if (modalInstance) modalInstance.hide();
                updateDomAfterVendorChange(res.data);
                
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Vendor Berhasil Diperbarui!',
                        text: `Supplier dan HPP item telah disesuaikan menjadi ${formatRupiahDisplay(hppPrice)}.` + (saveToMaster ? ' (Tersimpan ke Master Pricelist)' : ''),
                        timer: 2000,
                        showConfirmButton: false,
                        toast: true,
                        position: 'top-end'
                    });
                }
            } else {
                alert(res.message || 'Gagal memperbarui vendor');
            }
        })
        .catch(err => {
            console.error('Error updating vendor:', err);
            alert('Terjadi kesalahan koneksi saat memperbarui vendor.');
        });
    }

    function applyCustomVendor(e) {
        e.preventDefault();
        if (!activeModalItem) return;

        const supplierId = $('#custom_supplier_select').val();
        const selectElem = document.getElementById('custom_supplier_select');
        const supplierName = selectElem.selectedIndex > 0 ? selectElem.options[selectElem.selectedIndex].text : 'Supplier';
        const hppPrice = parseRupiahNumber(document.getElementById('custom_hpp_input').value);
        const saveToMaster = $('#check_save_to_master').is(':checked');

        if (hppPrice <= 0) {
            alert('Silakan masukkan nominal HPP yang valid.');
            return;
        }

        applyVendor(activeModalItem.id, supplierId, hppPrice, supplierName, saveToMaster);
    }

    function updateDomAfterVendorChange(data) {
        const item = data.item;
        const section = data.section;
        const rab = data.rab;

        // 1. Update Table Row
        const row = document.getElementById(`show_item_row_${item.id}`);
        if (row) {
            row.classList.remove('row-highlight-flash');
            void row.offsetWidth; // trigger reflow
            row.classList.add('row-highlight-flash');

            // Supplier Cell
            const supplierCell = document.getElementById(`cell_supplier_${item.id}`);
            if (supplierCell) {
                const labelElem = supplierCell.querySelector('.supplier-label-name');
                if (labelElem) labelElem.innerText = item.supplier_name;
                
                // Update button dataset and vendor count label
                const btn = supplierCell.querySelector('.btn-vendor-modal');
                if (btn) {
                    try {
                        const currentData = JSON.parse(btn.getAttribute('data-vendor-info') || '{}');
                        currentData.id_supplier = item.id_supplier;
                        currentData.supplier_name = item.supplier_name;
                        currentData.unit_price_hpp = item.unit_price_hpp;
                        currentData.unit_selling_price = item.unit_selling_price;
                        if (item.vendor_prices) {
                            currentData.vendor_prices = item.vendor_prices;
                            const spanCount = btn.querySelector('span');
                            if (spanCount) {
                                spanCount.innerText = item.vendor_prices.length > 0 ? `${item.vendor_prices.length} Vendor` : 'Ganti Vendor';
                            }
                        }
                        btn.setAttribute('data-vendor-info', JSON.stringify(currentData));
                    } catch(e){}
                }
            }

            // HPP Cell
            const hppCell = document.getElementById(`cell_hpp_${item.id}`);
            if (hppCell) {
                hppCell.innerHTML = `<span>${formatRupiahDisplay(item.unit_price_hpp)}</span>`;
            }

            // Margin Unit Sell Subtext
            const marginCell = document.getElementById(`cell_margin_${item.id}`);
            if (marginCell) {
                const subtext = marginCell.querySelector('.unit-sell-subtext');
                if (subtext) {
                    subtext.innerText = `@ ${formatRupiahDisplay(item.unit_selling_price)}`;
                }
            }

            // Total Jual Cell
            const sellCell = document.getElementById(`cell_total_sell_${item.id}`);
            if (sellCell) {
                sellCell.innerText = formatRupiahDisplay(item.total_selling_price);
            }
        }

        // 2. Update Section Subtotals
        const secHppElem = document.getElementById(`sec_hpp_${section.id}`);
        if (secHppElem) secHppElem.innerText = formatRupiahDisplay(section.subtotal_hpp);

        const secSellElem = document.getElementById(`sec_sell_${section.id}`);
        if (secSellElem) secSellElem.innerText = `Subtotal Jual: ${formatRupiahDisplay(section.subtotal_selling_price)}`;

        // 3. Update RAB KPI Stat Cards
        const kpiHpp = document.getElementById(`kpi_hpp_${rab.id}`);
        if (kpiHpp) kpiHpp.innerText = formatRupiahDisplay(rab.total_hpp);

        const kpiMargin = document.getElementById(`kpi_margin_${rab.id}`);
        if (kpiMargin) kpiMargin.innerText = formatRupiahDisplay(rab.total_margin);

        const kpiMarginPct = document.getElementById(`kpi_margin_pct_${rab.id}`);
        if (kpiMarginPct) kpiMarginPct.innerText = `+${rab.margin_percent}% Margin`;

        const kpiSell = document.getElementById(`kpi_sell_${rab.id}`);
        if (kpiSell) kpiSell.innerText = formatRupiahDisplay(rab.total_selling_price);
    }

    // Handle browser Back/Forward navigation
    window.addEventListener('popstate', function (event) {
        if (event.state && event.state.revId) {
            switchRevisionTab(event.state.revId);
        }
    });
</script>
@endpush
