@extends('layouts.sales.app')
@section('title', 'Detail RAB: ' . $rab->no_rab)

@push('before-style')
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

    /* Section Cards & Tables (Kept as loved by user) */
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
    <!-- 1. Executive Top Header & Actions -->
    <div class="rab-header-box no-print">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                    <a href="{{ route('piping-rab.index') }}" class="btn btn-sm btn-outline-secondary p-1 px-2 me-1" title="Kembali ke Daftar RAB">
                        <i class="mdi mdi-arrow-left fs-6"></i>
                    </a>
                    <h4 class="fw-bold mb-0 text-dark">{{ $rab->no_rab }}</h4>
                    @if($rab->revision_number > 0)
                        <span class="badge bg-label-info fw-semibold">Revisi {{ $rab->revision_number }}</span>
                    @else
                        <span class="badge bg-label-secondary fw-semibold">Original (Rev 0)</span>
                    @endif
                    @php
                        $statusBadgeClass = match($rab->status) {
                            'Converted' => 'bg-success',
                            'Approved'  => 'bg-info',
                            'Cancelled' => 'bg-danger',
                            default     => 'bg-warning'
                        };
                    @endphp
                    <span class="badge {{ $statusBadgeClass }} fw-semibold px-2 py-1">
                        {{ $rab->status }}
                    </span>
                </div>
                <div class="d-flex align-items-center gap-2 text-muted small">
                    <span class="fw-semibold text-dark">{{ $rab->project_name }}</span>
                    <span>&bull;</span>
                    <span><i class="mdi mdi-domain me-1"></i>{{ $rab->client ? $rab->client->company : 'Klien Umum' }}</span>
                    <span>&bull;</span>
                    <span><i class="mdi mdi-calendar-outline me-1"></i>{{ $rab->rab_date ? $rab->rab_date->format('d M Y') : '-' }}</span>
                </div>
            </div>

            <!-- Header Action Buttons -->
            <div class="d-flex flex-wrap gap-2">
                <button type="button" class="btn btn-outline-secondary btn-sm px-3" onclick="window.print()">
                    <i class="mdi mdi-printer me-1"></i> Cetak RAB
                </button>
                <a href="{{ route('piping-rab.edit', $rab->id) }}" class="btn btn-outline-primary btn-sm px-3">
                    <i class="mdi mdi-pencil me-1"></i> Edit RAB
                </a>
                <form action="{{ route('piping-rab.revise', $rab->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Buat revisi baru dari RAB ini?')">
                    @csrf
                    <button type="submit" class="btn btn-outline-info btn-sm px-3">
                        <i class="mdi mdi-source-branch me-1"></i> Buat Revisi Baru
                    </button>
                </form>
                @if($rab->status !== 'Converted')
                    <button type="button" class="btn btn-success btn-sm px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalConvertQuote">
                        <i class="mdi mdi-file-export-outline me-1"></i> Convert to Smart Quote
                    </button>
                @else
                    @if($rab->convertedQuotation)
                        <a href="{{ route('unit-quotation.show', $rab->converted_quotation_id) }}" class="btn btn-success btn-sm px-3">
                            <i class="mdi mdi-open-in-new me-1"></i> Buka Smart Quote ({{ $rab->convertedQuotation->no_quote }})
                        </a>
                    @endif
                    <button type="button" class="btn btn-outline-success btn-sm px-3" data-bs-toggle="modal" data-bs-target="#modalConvertQuote" title="Convert ulang ke Smart Quote dengan opsi atau mode baru">
                        <i class="mdi mdi-refresh me-1"></i> Convert Ulang
                    </button>
                @endif
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show no-print" role="alert">
            <i class="mdi mdi-check-circle-outline me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show no-print" role="alert">
            <i class="mdi mdi-alert-circle-outline me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Revision History Pill Bar (If exists) -->
    @if($revisions->count() > 1)
        <div class="alert alert-info py-2 px-3 mb-4 d-flex flex-wrap align-items-center justify-content-between gap-2 no-print" style="border-radius: 10px;">
            <div class="d-flex align-items-center gap-2">
                <i class="mdi mdi-history fs-5"></i>
                <span class="small fw-bold">Riwayat Revisi Dokumen:</span>
                @foreach($revisions as $rev)
                    <a href="{{ route('piping-rab.show', $rev->id) }}" class="badge {{ $rev->id === $rab->id ? 'bg-primary' : 'bg-white text-dark border' }} text-decoration-none px-2 py-1">
                        {{ $rev->revision_number === 0 ? 'Rev 0' : 'Rev ' . $rev->revision_number }}
                        @if($rev->is_latest) <span class="badge bg-success ms-1" style="font-size: 8px;">Latest</span> @endif
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    <!-- 2. Financial KPI Metric Widgets (4 Stat Cards) -->
    @php
        $marginPct = $rab->total_hpp > 0 ? round(($rab->total_margin / $rab->total_hpp) * 100, 1) : 0;
        $totalItemsCount = $rab->sections->sum(fn($s) => $s->items->count());
    @endphp
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
                <div class="fs-4 fw-bold text-dark mb-1 font-monospace">Rp {{ number_format($rab->total_hpp, 0, ',', '.') }}</div>
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
                <div class="fs-4 fw-bold text-success mb-1 font-monospace">Rp {{ number_format($rab->total_margin, 0, ',', '.') }}</div>
                <div class="d-flex align-items-center gap-1">
                    <span class="badge bg-label-success" style="font-size: 10px;">+{{ $marginPct }}% Margin</span>
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
                <div class="fs-4 fw-bold stat-value mb-1 font-monospace">Rp {{ number_format($rab->total_selling_price, 0, ',', '.') }}</div>
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
                <div class="fs-4 fw-bold text-dark mb-1">{{ $rab->sections->count() }} <span class="fs-6 fw-normal text-muted">Area</span></div>
                <small class="text-muted" style="font-size: 11px;">Total {{ $totalItemsCount }} item pipa, fitting & jasa</small>
            </div>
        </div>
    </div>

    <!-- 3. Detail Informasi Proyek & Klien Card (Modern Clean Info Grid) -->
    <div class="rab-show-card mb-4">
        <div class="section-header-clean d-flex justify-content-between align-items-center">
            <h6 class="fw-bold text-dark mb-0"><i class="mdi mdi-information-outline me-2 text-primary"></i>Informasi Lengkap Proyek & Klien</h6>
            <span class="badge bg-label-secondary" style="font-size: 11px;">ID Dokumen #{{ $rab->id }}</span>
        </div>
        <div class="p-4">
            <div class="row g-3">
                <div class="col-md-4 col-sm-6">
                    <div class="project-info-tile">
                        <div class="tile-label"><i class="mdi mdi-domain text-primary"></i> Customer / Perusahaan</div>
                        <div class="tile-value">{{ $rab->client ? $rab->client->company : '-' }}</div>
                        <small class="text-muted" style="font-size: 11px;">{{ $rab->client ? $rab->client->name : '' }}</small>
                    </div>
                </div>

                <div class="col-md-4 col-sm-6">
                    <div class="project-info-tile">
                        <div class="tile-label"><i class="mdi mdi-account-tie text-primary"></i> PIC Customer</div>
                        <div class="tile-value">{{ $rab->pic ? $rab->pic->name : '-' }}</div>
                        <small class="text-muted" style="font-size: 11px;">{{ $rab->pic && $rab->pic->phone ? $rab->pic->phone : ($rab->client ? $rab->client->phone : '-') }}</small>
                    </div>
                </div>

                <div class="col-md-4 col-sm-6">
                    <div class="project-info-tile">
                        <div class="tile-label"><i class="mdi mdi-map-marker-radius-outline text-primary"></i> Lokasi / Plant Area</div>
                        <div class="tile-value">{{ $rab->location_plant ?: '-' }}</div>
                        <small class="text-muted" style="font-size: 11px;">Area instalasi sistem piping</small>
                    </div>
                </div>

                <div class="col-md-4 col-sm-6">
                    <div class="project-info-tile">
                        <div class="tile-label"><i class="mdi mdi-calendar-check text-primary"></i> Tanggal RAB</div>
                        <div class="tile-value">{{ $rab->rab_date ? $rab->rab_date->format('d F Y') : '-' }}</div>
                        <small class="text-muted" style="font-size: 11px;">Dibuat: {{ $rab->created_at ? $rab->created_at->format('d/m/Y H:i') : '-' }}</small>
                    </div>
                </div>

                <div class="col-md-4 col-sm-6">
                    <div class="project-info-tile">
                        <div class="tile-label"><i class="mdi mdi-badge-account-outline text-primary"></i> Sales Person</div>
                        <div class="tile-value">{{ $rab->sales ? $rab->sales->name : '-' }}</div>
                        <small class="text-muted" style="font-size: 11px;">PIC Sales Reftech</small>
                    </div>
                </div>

                <div class="col-md-4 col-sm-6">
                    <div class="project-info-tile">
                        <div class="tile-label"><i class="mdi mdi-account-edit-outline text-primary"></i> Dibuat Oleh (Admin/Estimator)</div>
                        <div class="tile-value">{{ $rab->admin ? $rab->admin->name : '-' }}</div>
                        <small class="text-muted" style="font-size: 11px;">Estimator RAB Internal</small>
                    </div>
                </div>

                @if($rab->notes)
                    <div class="col-12">
                        <div class="p-3 bg-light rounded border" style="font-size: 12.5px;">
                            <strong class="text-dark"><i class="mdi mdi-note-text-outline me-1 text-primary"></i>Catatan Teknis Internal:</strong>
                            <span class="text-muted ms-1">{{ $rab->notes }}</span>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Section Details -->
    @foreach($rab->sections as $sIdx => $sec)
        <div class="rab-show-card mb-4">
            <div class="section-header-clean d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-primary rounded p-1"><i class="mdi mdi-folder-open-outline fs-6"></i></span>
                    <h6 class="mb-0 fw-bold text-dark">{{ $sec->section_name }}</h6>
                </div>
                <div class="d-flex gap-3 align-items-center">
                    <small class="text-muted">Subtotal HPP: <strong class="text-dark">Rp {{ number_format($sec->subtotal_hpp, 0, ',', '.') }}</strong></small>
                    <span class="badge bg-label-primary fs-6 px-3 py-2">Subtotal Jual: Rp {{ number_format($sec->subtotal_selling_price, 0, ',', '.') }}</span>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table rab-show-table align-middle">
                    <thead>
                        <tr>
                            <th style="width: 35px;" class="text-center">#</th>
                            <th style="width: 28%;">Uraian Item / Spesifikasi</th>
                            <th style="width: 14%;" class="text-center">Kalkulasi Meter</th>
                            <th style="width: 10%;" class="text-center">Qty / Satuan</th>
                            <th style="width: 14%;" class="text-end">HPP / Unit</th>
                            <th style="width: 15%;">Supplier</th>
                            <th style="width: 7%;" class="text-center">Margin</th>
                            <th style="width: 12%;" class="text-end">Total Jual (Rp)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sec->items as $iIdx => $item)
                            <tr>
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
                                    <span class="fw-bold text-primary fs-6">{{ (float)$item->calculated_qty }}</span>
                                    <small class="text-muted d-block" style="font-size: 11px;">{{ $item->unit }}</small>
                                </td>
                                <td class="text-end text-muted">
                                    <span>Rp {{ number_format($item->unit_price_hpp, 0, ',', '.') }}</span>
                                </td>
                                <td>
                                    @if($item->supplier)
                                        <span class="text-truncate d-inline-block fw-semibold text-dark" style="max-width: 150px;" title="{{ $item->supplier->supplier }}">
                                            {{ $item->supplier->supplier }}
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($item->margin_type === 'percent')
                                        <span class="badge bg-label-success">+{{ (float)$item->margin_value }}%</span>
                                    @else
                                        <span class="badge bg-label-success">+Rp {{ number_format($item->margin_value, 0, ',', '.') }}</span>
                                    @endif
                                    <small class="text-muted d-block" style="font-size: 9.5px;">@ Rp {{ number_format($item->unit_selling_price, 0, ',', '.') }}</small>
                                </td>
                                <td class="text-end fw-bold text-primary fs-6">
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

<!-- Modal Convert to Smart Quote -->
<div class="modal fade" id="modalConvertQuote" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form action="{{ route('piping-rab.convert', $rab->id) }}" method="POST" class="modal-content">
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
