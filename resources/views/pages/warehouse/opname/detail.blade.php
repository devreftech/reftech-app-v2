@extends('layouts.sales.app')
@section('title', 'Audit Fisik Stock Opname Q' . $opname->periode . ' ' . ($opname->year ?? date('Y', strtotime($opname->date))))

@section('content')
    {{-- Header & Breadcrumbs --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-3 gap-3">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('opname.index') }}">Stock Opname</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Sesi #{{ $opname->id }}</li>
                </ol>
            </nav>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <h4 class="fw-bold mb-0 text-primary">
                    <i class="mdi mdi-clipboard-text-search-outline me-2"></i>Stock Opname Fisik Q{{ $opname->periode }} - {{ $opname->year ?? date('Y', strtotime($opname->date)) }}
                </h4>
                <span class="badge bg-label-primary px-3 py-1 fs-6">Sesi #{{ $opname->id }}</span>
                <span class="badge bg-label-success live-indicator-badge py-1 px-2">
                    <span class="pulse-dot"></span> Live Auto-Sync Aktif
                </span>
            </div>
            <div class="d-flex align-items-center gap-3 text-muted small mt-1 flex-wrap">
                <span><i class="mdi mdi-account-circle-outline me-1"></i>Petugas: <strong>{{ $opname->user->name ?? '-' }}</strong></span>
                <span><i class="mdi mdi-calendar-blank-outline me-1"></i>Tanggal: <strong>{{ \Carbon\Carbon::parse($opname->date)->translatedFormat('d F Y') }}</strong></span>
                @if($opname->note && $opname->note !== '-')
                    <span><i class="mdi mdi-information-outline me-1"></i>Catatan: <em>{{ $opname->note }}</em></span>
                @endif
            </div>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <a href="{{ route('opname.index') }}" class="btn btn-outline-secondary waves-effect">
                <i class="mdi mdi-arrow-left me-1"></i> Kembali
            </a>
            <a href="{{ route('opname.show_print', $opname->id) }}" target="_blank" class="btn btn-outline-primary waves-effect">
                <i class="mdi mdi-printer-outline me-1"></i> Print / PDF
            </a>

            @if($stats['is_locked'])
                <button type="button" class="btn btn-success waves-effect" disabled>
                    <i class="mdi mdi-lock-check-outline me-1"></i> Selesai &amp; Terkunci
                </button>
            @else
                @if($stats['is_all_counted'])
                    <button type="button" class="btn btn-primary btn-finalize-opname waves-effect waves-light shadow-sm">
                        <i class="mdi mdi-content-save-check-outline me-1"></i> Simpan Data &amp; Kunci Sesi
                    </button>
                @endif
                <button type="button" class="btn btn-label-success waves-effect" id="btnBulkFillSystem">
                    <i class="mdi mdi-check-all me-1"></i> Set Semua = Sistem
                </button>
                <button type="button" class="btn btn-label-warning waves-effect" id="btnBulkReset">
                    <i class="mdi mdi-restart me-1"></i> Reset Hitungan
                </button>
                <button type="button" class="btn btn-label-danger waves-effect" id="btnDeleteSession">
                    <i class="mdi mdi-trash-can-outline me-1"></i> Hapus Sesi
                </button>
            @endif
            <form id="formDeleteSession" action="{{ route('opname.destroy', $opname->id) }}" method="POST" style="display: none;">
                @csrf
                @method('DELETE')
            </form>
        </div>
    </div>

    {{-- Lock Notification Banner if finalized --}}
    @if($stats['is_locked'])
        <div class="alert alert-success d-flex align-items-center mb-3 shadow-sm border-0" role="alert">
            <i class="mdi mdi-lock-check fs-3 me-3 text-success"></i>
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center w-100 gap-2">
                <div>
                    <h6 class="alert-heading fw-bold mb-1">Stock Opname Telah Difinalisasi &amp; Dikunci</h6>
                    <span class="small">
                        Data fisik telah diverifikasi 100% dan dikunci permanen oleh <strong>{{ $opname->userCompleted->name ?? 'Petugas' }}</strong> 
                        pada {{ $opname->completed_at ? \Carbon\Carbon::parse($opname->completed_at)->translatedFormat('d F Y, H:i') . ' WIB' : '-' }}.
                    </span>
                </div>
                <span class="badge bg-success fs-6 py-2 px-3 text-nowrap"><i class="mdi mdi-check-all me-1"></i> Final Completed</span>
            </div>
        </div>
    @endif

    {{-- Milestone & Progress Timeline Card --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header border-bottom py-3 d-flex flex-wrap justify-content-between align-items-center gap-2 bg-light-subtle">
            <div class="d-flex align-items-center gap-2">
                <div class="avatar avatar-sm bg-label-primary rounded p-1 d-flex align-items-center justify-content-center">
                    <i class="mdi mdi-flag-checkered fs-4"></i>
                </div>
                <div>
                    <h6 class="fw-bold mb-0 text-dark">Milestone &amp; Progres Pelaksanaan Stock Opname</h6>
                    <small class="text-muted">Jejak tanggal mulai, aktivitas audit berkala, dan status finalisasi</small>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
                @if($stats['is_locked'])
                    <span class="badge bg-label-success py-1 px-3 fs-6">
                        <i class="mdi mdi-lock-outline me-1"></i> Status: Selesai &amp; Dikunci
                    </span>
                @elseif($stats['is_all_counted'])
                    <button type="button" class="btn btn-sm btn-primary btn-finalize-opname waves-effect waves-light shadow-sm">
                        <i class="mdi mdi-content-save-check-outline me-1"></i> Simpan Data &amp; Kunci
                    </button>
                @else
                    <span class="badge bg-label-warning py-1 px-3 fs-6">
                        <i class="mdi mdi-clock-outline me-1"></i> Proses Audit: {{ $stats['progress_percent'] }}%
                    </span>
                @endif
            </div>
        </div>
        <div class="card-body py-3">
            {{-- Horizontal Milestone Steps --}}
            <div class="row g-3">
                {{-- Step 1: Tanggal Mulai Sesi --}}
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="p-3 rounded border h-100 bg-white d-flex flex-column justify-content-between position-relative">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="badge bg-label-primary text-uppercase fw-semibold" style="font-size: 11px;">Milestone 1</span>
                            <i class="mdi mdi-check-circle text-success fs-5"></i>
                        </div>
                        <div>
                            <div class="text-muted small fw-medium">Tanggal Mulai Sesi</div>
                            <h6 class="fw-bold mb-1 text-dark mt-1">
                                {{ \Carbon\Carbon::parse($opname->date)->translatedFormat('d F Y') }}
                            </h6>
                            <div class="text-muted small">
                                <i class="mdi mdi-account-circle-outline me-1"></i>Oleh: <strong>{{ $opname->user->name ?? 'Admin' }}</strong>
                            </div>
                        </div>
                        <div class="mt-2 pt-2 border-top text-muted small d-flex align-items-center">
                            <span class="badge bg-label-success rounded-pill px-2 py-0 me-1">Selesai</span> Inisiasi Sesi
                        </div>
                    </div>
                </div>

                {{-- Step 2: Progres Hitung SKU Fisik --}}
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="p-3 rounded border h-100 bg-white d-flex flex-column justify-content-between position-relative">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="badge bg-label-info text-uppercase fw-semibold" style="font-size: 11px;">Milestone 2</span>
                            @if($stats['is_all_counted'])
                                <i id="m2StatusIcon" class="mdi mdi-check-circle text-success fs-5"></i>
                            @else
                                <div id="m2StatusIcon" class="spinner-border spinner-border-sm text-primary" role="status"></div>
                            @endif
                        </div>
                        <div>
                            <div class="text-muted small fw-medium">Pencatatan Fisik SKU</div>
                            <h6 class="fw-bold mb-1 {{ $stats['is_all_counted'] ? 'text-success' : 'text-primary' }} mt-1" id="m2SkuCount">
                                {{ number_format($stats['counted_sku']) }} / {{ number_format($stats['total_sku']) }} SKU
                            </h6>
                            <div class="text-muted small">
                                Input Pertama: <strong>{{ $stats['first_input_at'] ? \Carbon\Carbon::parse($stats['first_input_at'])->translatedFormat('d M, H:i') : '-' }}</strong>
                            </div>
                        </div>
                        <div class="mt-2 pt-2 border-top text-muted small">
                            <div class="d-flex justify-content-between mb-1">
                                <span>Progress:</span>
                                <strong class="text-dark" id="m2ProgressText">{{ $stats['progress_percent'] }}%</strong>
                            </div>
                            <div class="progress" style="height: 4px;">
                                <div class="progress-bar {{ $stats['is_all_counted'] ? 'bg-success' : 'bg-primary' }}" id="m2ProgressBar" style="width: {{ $stats['progress_percent'] }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Step 3: Review & Rekonsiliasi Selisih --}}
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="p-3 rounded border h-100 bg-white d-flex flex-column justify-content-between position-relative">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="badge bg-label-warning text-uppercase fw-semibold" style="font-size: 11px;">Milestone 3</span>
                            <i class="mdi {{ $stats['total_selisih_count'] > 0 ? 'mdi-alert-circle text-warning' : 'mdi-check-circle text-success' }} fs-5"></i>
                        </div>
                        <div>
                            <div class="text-muted small fw-medium">Review Selisih Stok</div>
                            <h6 class="fw-bold mb-1 {{ $stats['total_selisih_count'] > 0 ? 'text-warning' : 'text-success' }} mt-1">
                                {{ number_format($stats['total_selisih_count']) }} SKU Selisih
                            </h6>
                            <div class="text-muted small">
                                Net: <strong>{{ $stats['sum_total_selisih'] > 0 ? '+' : '' }}{{ number_format($stats['sum_total_selisih']) }} pcs</strong>
                                (BDG: {{ $stats['selisih_bdg_count'] }}, BKS: {{ $stats['selisih_bks_count'] }})
                            </div>
                        </div>
                        <div class="mt-2 pt-2 border-top text-muted small d-flex align-items-center">
                            @if($stats['is_all_counted'])
                                <span class="badge bg-label-success rounded-pill px-2 py-0 me-1">Terverifikasi</span> Siap Finalisasi
                            @else
                                <span class="badge bg-label-secondary rounded-pill px-2 py-0 me-1">Proses</span> Audit Berjalan
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Step 4: Finalisasi & Penguncian Data --}}
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="p-3 rounded border h-100 {{ $stats['is_locked'] ? 'bg-label-success border-success' : 'bg-white' }} d-flex flex-column justify-content-between position-relative">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="badge {{ $stats['is_locked'] ? 'bg-success' : 'bg-label-primary' }} text-uppercase fw-semibold" style="font-size: 11px;">Milestone 4</span>
                            @if($stats['is_locked'])
                                <i class="mdi mdi-lock-check text-success fs-5"></i>
                            @elseif($stats['is_all_counted'])
                                <i class="mdi mdi-lock-open-variant text-warning fs-5"></i>
                            @else
                                <i class="mdi mdi-lock text-muted fs-5"></i>
                            @endif
                        </div>
                        <div>
                            <div class="text-muted small fw-medium">Finalisasi &amp; Kunci Data</div>
                            @if($stats['is_locked'])
                                <h6 class="fw-bold mb-1 text-success mt-1">
                                    <i class="mdi mdi-lock me-1"></i>Terkunci Permanen
                                </h6>
                                <div class="text-muted small">
                                    Oleh: <strong>{{ $opname->userCompleted->name ?? 'Petugas' }}</strong><br>
                                    Tgl: <strong>{{ $opname->completed_at ? \Carbon\Carbon::parse($opname->completed_at)->translatedFormat('d M Y, H:i') : '-' }}</strong>
                                </div>
                            @elseif($stats['is_all_counted'])
                                <h6 class="fw-bold mb-1 text-primary mt-1">100% SKU Terhitung</h6>
                                <div class="text-muted small mb-1">
                                    Klik tombol untuk mengunci data secara permanen.
                                </div>
                                <button type="button" class="btn btn-sm btn-primary w-100 btn-finalize-opname waves-effect waves-light shadow-sm mt-1">
                                    <i class="mdi mdi-content-save-check me-1"></i> Simpan Data
                                </button>
                            @else
                                <h6 class="fw-bold mb-1 text-muted mt-1">Menunggu Hitungan 100%</h6>
                                <div class="text-muted small">
                                    Sisa {{ number_format($stats['uncounted_sku']) }} SKU belum dihitung.
                                </div>
                            @endif
                        </div>
                        <div class="mt-2 pt-2 border-top text-muted small d-flex align-items-center">
                            @if($stats['is_locked'])
                                <span class="badge bg-success rounded-pill px-2 py-0 me-1">Final</span> Input Dikunci
                            @elseif($stats['is_all_counted'])
                                <span class="badge bg-label-warning rounded-pill px-2 py-0 me-1">Siap</span> Butuh Finalisasi
                            @else
                                <span class="badge bg-label-secondary rounded-pill px-2 py-0 me-1">Menunggu</span> 100% SKU
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Daily Activity Breakdown per Tanggal Mulai / Berjalan --}}
            @if(isset($dailyTimeline) && count($dailyTimeline) > 0)
                <div class="mt-3 pt-3 border-top">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="small fw-bold text-dark text-uppercase">
                            <i class="mdi mdi-timeline-clock-outline me-1 text-primary"></i>Aktivitas Audit Per Tanggal:
                        </span>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($dailyTimeline as $timeline)
                            <div class="badge bg-label-secondary p-2 d-flex align-items-center gap-2 border">
                                <i class="mdi mdi-calendar-check text-primary fs-6"></i>
                                <div class="text-start">
                                    <div class="fw-bold text-dark">
                                        {{ \Carbon\Carbon::parse($timeline->log_date)->translatedFormat('d M Y') }}
                                    </div>
                                    <div class="text-muted" style="font-size: 10px;">
                                        {{ $timeline->total_items }} SKU (BDG: {{ $timeline->bdg_counted }}, BKS: {{ $timeline->bks_counted }})
                                        @if($timeline->selisih_items > 0)
                                            <span class="text-danger fw-bold ms-1">({{ $timeline->selisih_items }} selisih)</span>
                                        @else
                                            <span class="text-success fw-bold ms-1">(0 selisih)</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- KPI Metric Summary Cards --}}
    <div class="row g-3 mb-4">
        {{-- Card 1: Total SKU & Progress --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div>
                            <span class="text-muted small fw-medium text-uppercase">Total Item / SKU</span>
                            <h4 class="fw-bold mb-0 text-primary mt-1" id="statTotalSku">{{ number_format($stats['total_sku']) }}</h4>
                        </div>
                        <div class="avatar avatar-md bg-label-primary rounded p-2 d-flex align-items-center justify-content-center">
                            <i class="mdi mdi-package-variant-closed fs-3"></i>
                        </div>
                    </div>
                    <div class="d-flex align-items-center justify-content-between text-muted small">
                        <span>Terhitung: <strong id="statCountedSku" class="text-dark">{{ number_format($stats['counted_sku']) }}</strong></span>
                        <span id="statProgressPercent" class="badge bg-label-primary">{{ $stats['progress_percent'] }}%</span>
                    </div>
                    <div class="progress mt-2" style="height: 6px;">
                        <div class="progress-bar bg-primary" id="statProgressBar" role="progressbar" style="width: {{ $stats['progress_percent'] }}%" aria-valuenow="{{ $stats['progress_percent'] }}" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 2: Selisih Gudang Bandung (BDG) --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div>
                            <span class="text-muted small fw-medium text-uppercase">Gudang Bandung (BDG)</span>
                            <h4 class="fw-bold mb-0 text-info mt-1" id="statSelisihBdgPcs">
                                {{ $stats['sum_selisih_bdg'] > 0 ? '+' . number_format($stats['sum_selisih_bdg']) : number_format($stats['sum_selisih_bdg']) }} <small class="fs-6 fw-normal text-muted">pcs</small>
                            </h4>
                        </div>
                        <div class="avatar avatar-md bg-label-info rounded p-2 d-flex align-items-center justify-content-center">
                            <i class="mdi mdi-warehouse fs-3"></i>
                        </div>
                    </div>
                    <div class="d-flex align-items-center justify-content-between text-muted small">
                        <span>Item Selisih:</span>
                        <span id="statSelisihBdgCount" class="badge {{ $stats['selisih_bdg_count'] > 0 ? 'bg-label-danger' : 'bg-label-success' }}">
                            {{ number_format($stats['selisih_bdg_count']) }} SKU
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 3: Selisih Gudang Bekasi (BKS) --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div>
                            <span class="text-muted small fw-medium text-uppercase">Gudang Bekasi (BKS)</span>
                            <h4 class="fw-bold mb-0 text-warning mt-1" id="statSelisihBksPcs">
                                {{ $stats['sum_selisih_bks'] > 0 ? '+' . number_format($stats['sum_selisih_bks']) : number_format($stats['sum_selisih_bks']) }} <small class="fs-6 fw-normal text-muted">pcs</small>
                            </h4>
                        </div>
                        <div class="avatar avatar-md bg-label-warning rounded p-2 d-flex align-items-center justify-content-center">
                            <i class="mdi mdi-home-city-outline fs-3"></i>
                        </div>
                    </div>
                    <div class="d-flex align-items-center justify-content-between text-muted small">
                        <span>Item Selisih:</span>
                        <span id="statSelisihBksCount" class="badge {{ $stats['selisih_bks_count'] > 0 ? 'bg-label-danger' : 'bg-label-success' }}">
                            {{ number_format($stats['selisih_bks_count']) }} SKU
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 4: Total Selisih Konsolidasi --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div>
                            <span class="text-muted small fw-medium text-uppercase">Total Selisih Bersih</span>
                            <h4 class="fw-bold mb-0 {{ $stats['sum_total_selisih'] != 0 ? 'text-danger' : 'text-success' }} mt-1" id="statSumTotalSelisih">
                                {{ $stats['sum_total_selisih'] > 0 ? '+' . number_format($stats['sum_total_selisih']) : number_format($stats['sum_total_selisih']) }} <small class="fs-6 fw-normal text-muted">pcs</small>
                            </h4>
                        </div>
                        <div class="avatar avatar-md {{ $stats['total_selisih_count'] > 0 ? 'bg-label-danger' : 'bg-label-success' }} rounded p-2 d-flex align-items-center justify-content-center">
                            <i class="mdi mdi-scale-balance fs-3"></i>
                        </div>
                    </div>
                    <div class="d-flex align-items-center justify-content-between text-muted small">
                        <span>Total SKU Selisih:</span>
                        <span id="statTotalSelisihCount" class="badge {{ $stats['total_selisih_count'] > 0 ? 'bg-label-danger' : 'bg-label-success' }}">
                            {{ number_format($stats['total_selisih_count']) }} SKU
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main DataTable Card --}}
    <div class="card border-0 shadow-sm">
        {{-- Card Header & Filter Tabs --}}
        <div class="card-header border-bottom py-3">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
                <div class="d-flex align-items-center gap-2 flex-wrap" id="filterButtonGroup">
                    <span class="fw-semibold text-dark me-1 small text-uppercase"><i class="mdi mdi-filter-variant me-1"></i>Filter:</span>
                    <button type="button" class="btn btn-sm btn-primary filter-btn active" data-filter="all">
                        Semua SKU
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-danger filter-btn" data-filter="selisih">
                        <i class="mdi mdi-alert-circle-outline me-1"></i>Ada Selisih
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-info filter-btn" data-filter="selisih_bdg">
                        Selisih BDG
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-warning filter-btn" data-filter="selisih_bks">
                        Selisih BKS
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-success filter-btn" data-filter="match">
                        <i class="mdi mdi-check-circle-outline me-1"></i>Sesuai (0)
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary filter-btn" data-filter="uncounted">
                        Belum Dihitung
                    </button>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-label-info"><i class="mdi mdi-account-lock-outline me-1"></i>Avatar PIC mencatat penginput &amp; mengunci data untuk akun lain</span>
                </div>
            </div>
        </div>

        {{-- Table --}}
        <div class="card-datatable table-responsive p-0">
            <table class="table table-hover table-bordered mb-0 align-middle w-100" id="tableOpnameInline">
                <thead class="table-light">
                    <tr>
                        <th class="text-center" style="width: 40px;">#</th>
                        <th style="min-width: 220px;">SKU &amp; Sparepart</th>
                        {{-- Group Gudang Bandung --}}
                        <th class="text-center bg-label-info text-info border-start border-end" style="min-width: 210px;" colspan="3">
                            <i class="mdi mdi-warehouse me-1"></i>GUDANG BANDUNG (BDG)
                        </th>
                        {{-- Group Gudang Bekasi --}}
                        <th class="text-center bg-label-warning text-warning border-start border-end" style="min-width: 210px;" colspan="3">
                            <i class="mdi mdi-home-city-outline me-1"></i>GUDANG BEKASI (BKS)
                        </th>
                        {{-- Total Selisih --}}
                        <th class="text-center" style="min-width: 110px;">Total Selisih</th>
                        <th style="min-width: 160px;">Catatan</th>
                        <th class="text-center" style="width: 60px;">Status</th>
                    </tr>
                    <tr class="table-light small text-muted">
                        <th></th>
                        <th>Replacement / Nama Part</th>
                        {{-- BDG sub-headers --}}
                        <th class="text-center bg-label-info text-info border-start" style="width: 55px;" title="Stok Sistem Bandung">Sistem</th>
                        <th class="text-center bg-label-info text-info" style="width: 100px;" title="Fisik Bandung & PIC">Fisik BDG</th>
                        <th class="text-center bg-label-info text-info border-end" style="width: 60px;" title="Selisih Bandung">Selisih</th>
                        {{-- BKS sub-headers --}}
                        <th class="text-center bg-label-warning text-warning border-start" style="width: 55px;" title="Stok Sistem Bekasi">Sistem</th>
                        <th class="text-center bg-label-warning text-warning" style="width: 100px;" title="Fisik Bekasi & PIC">Fisik BKS</th>
                        <th class="text-center bg-label-warning text-warning border-end" style="width: 60px;" title="Selisih Bekasi">Selisih</th>
                        {{-- Summary --}}
                        <th class="text-center">Net (Fisik - Sis)</th>
                        <th>Keterangan</th>
                        <th class="text-center">Simpan</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- Populated via DataTables AJAX --}}
                </tbody>
            </table>
        </div>
    </div>

    {{-- Modal Info SKU --}}
    <div class="modal fade" id="modalSkuInfo" tabindex="-1" aria-labelledby="modalSkuInfoLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 440px;">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-label-primary border-0 pb-2">
                    <div class="d-flex align-items-center gap-2">
                        <div class="avatar avatar-sm bg-primary rounded p-1 d-flex align-items-center justify-content-center">
                            <i class="mdi mdi-package-variant-closed text-white fs-5"></i>
                        </div>
                        <div>
                            <h6 class="modal-title fw-bold mb-0" id="modalSkuInfoLabel">Info SKU</h6>
                            <small class="text-muted" id="modalSkuSubtitle">Detail & Pengaturan Opname</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pt-3">
                    <div class="mb-3 p-3 rounded bg-light border">
                        <div class="small text-muted text-uppercase fw-semibold mb-1">Kode SKU / Replacement</div>
                        <div class="fw-bold text-dark fs-6" id="modalSkuCode">-</div>
                    </div>

                    {{-- Status Opname Toggle --}}
                    <div class="d-flex align-items-center justify-content-between p-3 rounded border mb-3">
                        <div>
                            <div class="fw-semibold text-dark">Status Opname</div>
                            <small class="text-muted">Nonaktifkan untuk menyembunyikan dari listing opname</small>
                        </div>
                        <div class="form-check form-switch mb-0" style="transform: scale(1.3); transform-origin: right center;">
                            <input class="form-check-input" type="checkbox" id="modalOpnameToggle" role="switch">
                        </div>
                    </div>

                    <div id="modalOpnameStatus" class="mb-3"></div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <a href="#" id="modalBtnDetailSku" target="_blank" class="btn btn-primary waves-effect waves-light">
                        <i class="mdi mdi-open-in-new me-1"></i> Detail SKU
                    </a>
                    <button type="button" class="btn btn-label-secondary waves-effect" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('after-style')
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.css" />
    <style>
        .opname-input-number {
            width: 65px;
            text-align: center;
            font-weight: 600;
            padding: 0.25rem 0.35rem;
            font-size: 0.875rem;
        }
        .opname-input-number:focus {
            background-color: #fffde7;
            border-color: #7367f0;
            box-shadow: 0 0 0 0.2rem rgba(115, 103, 240, 0.25);
        }
        .opname-input-note {
            font-size: 0.8125rem;
            padding: 0.25rem 0.5rem;
        }
        .table-hover tbody tr:hover {
            background-color: rgba(115, 103, 240, 0.04);
        }
        .filter-btn.active {
            box-shadow: 0 2px 4px rgba(115, 103, 240, 0.4);
        }
        .badge-selisih {
            min-width: 45px;
            font-size: 0.75rem;
            font-weight: 700;
        }
        .avatar-pic-badge {
            width: 24px;
            height: 24px;
            font-size: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            border-radius: 50%;
            cursor: pointer;
        }
        .avatar-pic-img {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            object-fit: cover;
            cursor: pointer;
        }
        .saving-spinner {
            display: inline-block;
            width: 1rem;
            height: 1rem;
            vertical-align: middle;
            border: 2px solid currentColor;
            border-right-color: transparent;
            border-radius: 50%;
            animation: spinner-border .75s linear infinite;
        }
        /* Live Auto-Sync Styles */
        .live-indicator-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .pulse-dot {
            width: 8px;
            height: 8px;
            background-color: #28c76f;
            border-radius: 50%;
            animation: pulse-dot-anim 1.5s infinite;
        }
        @keyframes pulse-dot-anim {
            0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(40, 199, 111, 0.7); }
            70% { transform: scale(1.1); box-shadow: 0 0 0 6px rgba(40, 199, 111, 0); }
            100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(40, 199, 111, 0); }
        }
        @keyframes live-update-flash {
            0% { background-color: rgba(40, 199, 111, 0.3); }
            100% { background-color: transparent; }
        }
        .live-updated-row {
            animation: live-update-flash 2.5s ease-out;
        }
    </style>
@endpush

@push('after-script')
    <script src="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.js"></script>
@endpush

@push('script')
    <script>
        $(document).ready(function() {
            const opnameId = {{ $opname->id }};
            let currentFilter = 'all';
            let currentUserId = {{ Auth::id() }};
            let isAdmin = {{ in_array(Auth::user()->role ?? '', ['Admin', 'Super Admin', 'Director', 'Warehouse Manager', 'Operational Manager']) ? 'true' : 'false' }};
            let isLockedSession = {{ $stats['is_locked'] ? 'true' : 'false' }};
            let lastServerTime = null;
            let isSyncing = false;

            // Setup CSRF header for all AJAX requests
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Helper to format tooltip text (e.g. Fajar Uzumaki | 08-10-2026)
            function formatUserTooltip(name, updatedAt) {
                if (!name) return '';
                let dateStr = '';
                if (updatedAt) {
                    const datePart = updatedAt.split(' ')[0].split('T')[0];
                    const parts = datePart.split('-');
                    if (parts.length === 3) {
                        dateStr = ` | ${parts[2]}-${parts[1]}-${parts[0]}`;
                    }
                }
                return `${name}${dateStr}`;
            }

            // Initialize DataTable
            const table = $('#tableOpnameInline').DataTable({
                ajax: {
                    url: '/db/stock/opname/' + opnameId,
                    type: 'GET',
                    dataSrc: function(json) {
                        if (json.current_user_id) currentUserId = json.current_user_id;
                        if (json.is_admin !== undefined) isAdmin = json.is_admin;
                        if (json.is_locked !== undefined) isLockedSession = json.is_locked;
                        if (json.server_time) lastServerTime = json.server_time;
                        return json.data;
                    }
                },
                processing: true,
                deferRender: true,
                pageLength: 25,
                lengthMenu: [15, 25, 50, 100, 250, 500],
                order: [],
                dom: '<"card-header d-flex flex-wrap justify-content-between align-items-center py-2"<"d-flex align-items-center gap-2"l><"dt-action-search"f>>t<"card-footer d-flex flex-wrap justify-content-between align-items-center py-2"<"text-muted small"i><"pagination-wrapper"p>>',
                columns: [
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        className: 'text-center text-muted small',
                        render: function(data, type, row, meta) {
                            return meta.row + 1;
                        }
                    },
                    {
                        data: 'replacement',
                        render: function(data, type, row) {
                            const goBadge = row.go ? `<span class="badge bg-label-secondary ms-1 small">${row.go.substring(0,1)}</span>` : '';
                            const desc = row.description ? `<div class="text-muted small text-truncate" style="max-width: 250px;">${row.description}</div>` : '';
                            const unit = row.unit ? `<span class="badge bg-label-dark small ms-1">${row.unit}</span>` : '';
                            const isOpname = (row.is_opname === 1 || row.is_opname === true || row.is_opname === '1');
                            const opnameDot = isOpname
                                ? `<span class="badge bg-label-success rounded-pill p-1 ms-1" title="Opname: ON" style="width:10px;height:10px;display:inline-block;"></span>`
                                : `<span class="badge bg-label-secondary rounded-pill p-1 ms-1" title="Opname: OFF" style="width:10px;height:10px;display:inline-block;"></span>`;

                            return `<div class="d-flex flex-column" style="min-width: 230px;">
                                <div class="d-flex align-items-center gap-1 mb-1">
                                    <span class="fw-bold text-primary btn-sku-info-trigger" style="cursor:pointer;"
                                        data-id="${row.product_id}"
                                        data-sku="${data || '-'}"
                                        data-product-url="${row.parent_product_id ? '/product/' + row.parent_product_id : '#'}"
                                        data-is-opname="${isOpname ? '1' : '0'}"
                                        title="Klik untuk lihat info SKU">
                                        ${data || '-'}
                                    </span>
                                    ${goBadge}${unit}${opnameDot}
                                </div>
                                ${desc}
                            </div>`;
                        }
                    },
                    // BDG: Sistem
                    {
                        data: 'sistem_bdg',
                        className: 'text-center bg-light text-dark fw-bold border-start',
                        render: function(data) {
                            return data !== null ? data : 0;
                        }
                    },
                    // BDG: Fisik Input + Lock
                    {
                        data: 'fisik_bdg',
                        orderable: false,
                        className: 'text-center p-1',
                        render: function(data, type, row) {
                            const val = (data !== null && data !== undefined) ? data : '';
                            const hasUser = !!row.id_user_bdg;
                            const isOwnerOrAdmin = (!hasUser) || (row.id_user_bdg == currentUserId) || isAdmin;
                            const tooltipText = formatUserTooltip(row.user_bdg_name, row.updated_at);

                            if (isLockedSession) {
                                return `<div class="d-flex align-items-center justify-content-center gap-1 input-container-bdg-${row.product_id}">
                                    <input type="number" class="form-control form-control-sm opname-input-number bg-light border-0 text-dark fw-bold input-bdg" 
                                        data-id="${row.product_id}" 
                                        value="${val}" disabled data-bs-toggle="tooltip" data-bs-placement="top" title="${tooltipText || 'Sesi Terkunci'}">
                                    <i class="mdi mdi-lock text-success" title="Sesi Terkunci"></i>
                                </div>`;
                            }

                            if (!isOwnerOrAdmin && hasUser) {
                                return `<div class="d-flex align-items-center justify-content-center gap-1 input-container-bdg-${row.product_id}">
                                    <input type="number" class="form-control form-control-sm opname-input-number bg-light border-0 text-muted input-bdg" 
                                        data-id="${row.product_id}" 
                                        value="${val}" disabled data-bs-toggle="tooltip" data-bs-placement="top" title="${tooltipText}">
                                    <i class="mdi mdi-lock text-warning lock-icon-bdg-${row.product_id}" data-bs-toggle="tooltip" data-bs-placement="top" title="${tooltipText}"></i>
                                </div>`;
                            }

                            const ownerTooltip = (hasUser && tooltipText) ? ` data-bs-toggle="tooltip" data-bs-placement="top" title="${tooltipText}"` : '';
                            return `<div class="d-flex align-items-center justify-content-center gap-1 input-container-bdg-${row.product_id}">
                                <input type="number" min="0" class="form-control form-control-sm opname-input-number input-bdg" 
                                    data-id="${row.product_id}" 
                                    value="${val}" 
                                    placeholder="-"${ownerTooltip}>
                            </div>`;
                        }
                    },
                    // BDG: Selisih
                    {
                        data: 'selisih_bdg',
                        className: 'text-center border-end p-1',
                        render: function(data, type, row) {
                            return renderSelisihBadge(data, row.is_counted, 'bdg', row.product_id);
                        }
                    },
                    // BKS: Sistem
                    {
                        data: 'sistem_bks',
                        className: 'text-center bg-light text-dark fw-bold border-start',
                        render: function(data) {
                            return data !== null ? data : 0;
                        }
                    },
                    // BKS: Fisik Input + Lock
                    {
                        data: 'fisik_bks',
                        orderable: false,
                        className: 'text-center p-1',
                        render: function(data, type, row) {
                            const val = (data !== null && data !== undefined) ? data : '';
                            const hasUser = !!row.id_user_bks;
                            const isOwnerOrAdmin = (!hasUser) || (row.id_user_bks == currentUserId) || isAdmin;
                            const tooltipText = formatUserTooltip(row.user_bks_name, row.updated_at);

                            if (isLockedSession) {
                                return `<div class="d-flex align-items-center justify-content-center gap-1 input-container-bks-${row.product_id}">
                                    <input type="number" class="form-control form-control-sm opname-input-number bg-light border-0 text-dark fw-bold input-bks" 
                                        data-id="${row.product_id}" 
                                        value="${val}" disabled data-bs-toggle="tooltip" data-bs-placement="top" title="${tooltipText || 'Sesi Terkunci'}">
                                    <i class="mdi mdi-lock text-success" title="Sesi Terkunci"></i>
                                </div>`;
                            }

                            if (!isOwnerOrAdmin && hasUser) {
                                return `<div class="d-flex align-items-center justify-content-center gap-1 input-container-bks-${row.product_id}">
                                    <input type="number" class="form-control form-control-sm opname-input-number bg-light border-0 text-muted input-bks" 
                                        data-id="${row.product_id}" 
                                        value="${val}" disabled data-bs-toggle="tooltip" data-bs-placement="top" title="${tooltipText}">
                                    <i class="mdi mdi-lock text-warning lock-icon-bks-${row.product_id}" data-bs-toggle="tooltip" data-bs-placement="top" title="${tooltipText}"></i>
                                </div>`;
                            }

                            const ownerTooltip = (hasUser && tooltipText) ? ` data-bs-toggle="tooltip" data-bs-placement="top" title="${tooltipText}"` : '';
                            return `<div class="d-flex align-items-center justify-content-center gap-1 input-container-bks-${row.product_id}">
                                <input type="number" min="0" class="form-control form-control-sm opname-input-number input-bks" 
                                    data-id="${row.product_id}" 
                                    value="${val}" 
                                    placeholder="-"${ownerTooltip}>
                            </div>`;
                        }
                    },
                    // BKS: Selisih
                    {
                        data: 'selisih_bks',
                        className: 'text-center border-end p-1',
                        render: function(data, type, row) {
                            return renderSelisihBadge(data, row.is_counted, 'bks', row.product_id);
                        }
                    },
                    // Total Selisih
                    {
                        data: 'selisih_total',
                        className: 'text-center p-1',
                        render: function(data, type, row) {
                            return renderTotalSelisihBadge(data, row.is_counted, row.product_id);
                        }
                    },
                    // Catatan
                    {
                        data: 'note',
                        orderable: false,
                        className: 'p-1',
                        render: function(data, type, row) {
                            const val = data || '';
                            const disabledAttr = isLockedSession ? 'disabled class="form-control form-control-sm opname-input-note input-note bg-light border-0 text-dark"' : 'class="form-control form-control-sm opname-input-note input-note"';
                            return `<input type="text" ${disabledAttr} 
                                data-id="${row.product_id}" 
                                value="${val}" 
                                placeholder="${isLockedSession ? '-' : 'Catatan...'}">`;
                        }
                    },
                    // Status
                    {
                        data: 'is_counted',
                        orderable: false,
                        searchable: false,
                        className: 'text-center p-1',
                        render: function(data, type, row) {
                            if (data == 1) {
                                return `<span id="statusIcon-${row.product_id}" class="text-success"><i class="mdi mdi-check-circle fs-5" title="Tersimpan"></i></span>`;
                            }
                            return `<span id="statusIcon-${row.product_id}" class="text-muted"><i class="mdi mdi-minus-circle-outline fs-5" title="Belum dihitung"></i></span>`;
                        }
                    }
                ],
                drawCallback: function() {
                    // Initialize Bootstrap tooltips on newly rendered rows
                    $('[data-bs-toggle="tooltip"]').tooltip();
                },
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Cari SKU / Sparepart...",
                    lengthMenu: "Tampil _MENU_ baris",
                    info: "Menampilkan _START_ s/d _END_ dari _TOTAL_ item",
                    infoEmpty: "Tidak ada data produk",
                    zeroRecords: "Tidak ada produk yang cocok dengan pencarian / filter",
                    paginate: {
                        next: '<i class="mdi mdi-chevron-right"></i>',
                        previous: '<i class="mdi mdi-chevron-left"></i>'
                    }
                }
            });

            // Helper function to render badges
            function renderSelisihBadge(val, isCounted, type, productId) {
                if (!isCounted && (val === null || val === undefined || val === 0)) {
                    return `<span class="badge bg-label-secondary badge-selisih badge-${type}-${productId}">-</span>`;
                }
                const num = parseInt(val) || 0;
                let badgeClass = 'bg-label-success';
                let text = '0';
                if (num > 0) {
                    badgeClass = 'bg-label-info';
                    text = '+' + num;
                } else if (num < 0) {
                    badgeClass = 'bg-label-danger';
                    text = num;
                }
                return `<span class="badge ${badgeClass} badge-selisih badge-${type}-${productId}">${text}</span>`;
            }

            function renderTotalSelisihBadge(val, isCounted, productId) {
                if (!isCounted || val === null || val === undefined) {
                    return `<span class="badge bg-label-secondary badge-selisih badge-total-${productId}">-</span>`;
                }
                const num = parseInt(val) || 0;
                let badgeClass = 'bg-success';
                let text = '0';
                if (num > 0) {
                    badgeClass = 'bg-info';
                    text = '+' + num;
                } else if (num < 0) {
                    badgeClass = 'bg-danger';
                    text = num;
                }
                return `<span class="badge ${badgeClass} badge-selisih badge-total-${productId}">${text}</span>`;
            }

            // Custom DataTable Filtering for Filter Tabs
            $.fn.dataTable.ext.search.push(function(settings, data, dataIndex, rowData) {
                if (currentFilter === 'all') return true;

                const isCounted = rowData.is_counted == 1;
                const selisihTotal = parseInt(rowData.selisih_total) || 0;
                const selisihBdg = parseInt(rowData.selisih_bdg) || 0;
                const selisihBks = parseInt(rowData.selisih_bks) || 0;

                if (currentFilter === 'selisih') {
                    return isCounted && selisihTotal !== 0;
                }
                if (currentFilter === 'selisih_bdg') {
                    return isCounted && selisihBdg !== 0;
                }
                if (currentFilter === 'selisih_bks') {
                    return isCounted && selisihBks !== 0;
                }
                if (currentFilter === 'match') {
                    return isCounted && selisihTotal === 0;
                }
                if (currentFilter === 'uncounted') {
                    return !isCounted;
                }
                return true;
            });

            // Filter button click handler
            $('.filter-btn').on('click', function() {
                $('.filter-btn').removeClass('active btn-primary btn-danger btn-info btn-warning btn-success btn-secondary');
                
                // Add outline to all
                $('.filter-btn[data-filter="all"]').addClass('btn-outline-primary');
                $('.filter-btn[data-filter="selisih"]').addClass('btn-outline-danger');
                $('.filter-btn[data-filter="selisih_bdg"]').addClass('btn-outline-info');
                $('.filter-btn[data-filter="selisih_bks"]').addClass('btn-outline-warning');
                $('.filter-btn[data-filter="match"]').addClass('btn-outline-success');
                $('.filter-btn[data-filter="uncounted"]').addClass('btn-outline-secondary');

                const filter = $(this).data('filter');
                currentFilter = filter;
                $(this).addClass('active');

                if (filter === 'all') $(this).removeClass('btn-outline-primary').addClass('btn-primary');
                if (filter === 'selisih') $(this).removeClass('btn-outline-danger').addClass('btn-danger');
                if (filter === 'selisih_bdg') $(this).removeClass('btn-outline-info').addClass('btn-info');
                if (filter === 'selisih_bks') $(this).removeClass('btn-outline-warning').addClass('btn-warning');
                if (filter === 'match') $(this).removeClass('btn-outline-success').addClass('btn-success');
                if (filter === 'uncounted') $(this).removeClass('btn-outline-secondary').addClass('btn-secondary');

                table.draw();
            });

            // Inline Save function per field
            function saveSingleField(productId, field, value, $row) {
                if (isLockedSession) {
                    Swal.fire({
                        icon: 'info',
                        title: 'Sesi Telah Dikunci',
                        text: 'Stock opname ini telah difinalisasi dan dikunci, perubahan data tidak diizinkan.'
                    });
                    return;
                }

                const $status = $(`#statusIcon-${productId}`);
                $status.html('<span class="saving-spinner text-primary"></span>');

                let payload = {
                    id_product: productId,
                    target_field: field
                };
                if (field === 'bdg') payload.stock_bdg = value;
                if (field === 'bks') payload.stock_bks = value;
                if (field === 'note') payload.note = value;

                $.ajax({
                    url: `/stock-opname/${opnameId}/save-item`,
                    type: 'POST',
                    data: payload,
                    success: function(res) {
                        if (res.success) {
                            $status.html('<i class="mdi mdi-check-circle text-success fs-5" title="Tersimpan"></i>');

                            // Update tooltip on inputs
                            if (field === 'bdg') {
                                const tipBdg = formatUserTooltip(res.item.user_bdg_name, res.item.updated_at);
                                const $bdg = $(`.input-bdg[data-id="${productId}"]`);
                                if (tipBdg) {
                                    $bdg.attr('title', tipBdg).attr('data-bs-original-title', tipBdg).attr('data-bs-toggle', 'tooltip');
                                } else {
                                    $bdg.removeAttr('title').removeAttr('data-bs-original-title').removeAttr('data-bs-toggle');
                                }
                            }
                            if (field === 'bks') {
                                const tipBks = formatUserTooltip(res.item.user_bks_name, res.item.updated_at);
                                const $bks = $(`.input-bks[data-id="${productId}"]`);
                                if (tipBks) {
                                    $bks.attr('title', tipBks).attr('data-bs-original-title', tipBks).attr('data-bs-toggle', 'tooltip');
                                } else {
                                    $bks.removeAttr('title').removeAttr('data-bs-original-title').removeAttr('data-bs-toggle');
                                }
                            }

                            // Update badge selisih BDG
                            const numBdg = parseInt(res.item.selisih_bdg) || 0;
                            let bdgClass = numBdg === 0 ? 'bg-label-success' : (numBdg > 0 ? 'bg-label-info' : 'bg-label-danger');
                            let bdgText = numBdg > 0 ? '+' + numBdg : numBdg;
                            if (res.item.fisik_bdg === null) {
                                $(`.badge-bdg-${productId}`).attr('class', `badge bg-label-secondary badge-selisih badge-bdg-${productId}`).text('-');
                            } else {
                                $(`.badge-bdg-${productId}`).attr('class', `badge ${bdgClass} badge-selisih badge-bdg-${productId}`).text(bdgText);
                            }

                            // Update badge selisih BKS
                            const numBks = parseInt(res.item.selisih_bks) || 0;
                            let bksClass = numBks === 0 ? 'bg-label-success' : (numBks > 0 ? 'bg-label-info' : 'bg-label-danger');
                            let bksText = numBks > 0 ? '+' + numBks : numBks;
                            if (res.item.fisik_bks === null) {
                                $(`.badge-bks-${productId}`).attr('class', `badge bg-label-secondary badge-selisih badge-bks-${productId}`).text('-');
                            } else {
                                $(`.badge-bks-${productId}`).attr('class', `badge ${bksClass} badge-selisih badge-bks-${productId}`).text(bksText);
                            }

                            // Update badge selisih Total
                            const numTotal = parseInt(res.item.selisih_total) || 0;
                            let totalClass = numTotal === 0 ? 'bg-success' : (numTotal > 0 ? 'bg-info' : 'bg-danger');
                            let totalText = numTotal > 0 ? '+' + numTotal : numTotal;
                            if (res.item.fisik_bdg === null && res.item.fisik_bks === null) {
                                $(`.badge-total-${productId}`).attr('class', `badge bg-label-secondary badge-selisih badge-total-${productId}`).text('-');
                            } else {
                                $(`.badge-total-${productId}`).attr('class', `badge ${totalClass} badge-selisih badge-total-${productId}`).text(totalText);
                            }

                            // Update row data cache in DataTable so filter works accurately
                            const dtRow = table.row($row);
                            if (dtRow && dtRow.data()) {
                                const rowData = dtRow.data();
                                rowData.fisik_bdg = res.item.fisik_bdg;
                                rowData.id_user_bdg = res.item.id_user_bdg;
                                rowData.user_bdg_name = res.item.user_bdg_name;
                                rowData.user_bdg_image = res.item.user_bdg_image;
                                rowData.fisik_bks = res.item.fisik_bks;
                                rowData.id_user_bks = res.item.id_user_bks;
                                rowData.user_bks_name = res.item.user_bks_name;
                                rowData.user_bks_image = res.item.user_bks_image;
                                rowData.fisik_total = res.item.fisik_total;
                                rowData.selisih_bdg = res.item.selisih_bdg;
                                rowData.selisih_bks = res.item.selisih_bks;
                                rowData.selisih_total = res.item.selisih_total;
                                rowData.note = res.item.note;
                                rowData.updated_at = res.item.updated_at;
                                rowData.is_counted = res.item.is_counted;
                            }

                            $('[data-bs-toggle="tooltip"]').tooltip();
                        }
                    },
                    error: function(xhr) {
                        $status.html('<i class="mdi mdi-alert-circle text-danger fs-5" title="Gagal simpan"></i>');
                        const msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Gagal menyimpan data.';
                        Swal.fire({
                            icon: 'error',
                            title: 'Tidak Diizinkan',
                            text: msg,
                            customClass: { confirmButton: 'btn btn-primary' }
                        });
                    }
                });
            }

            // Debounced auto-save on typing (input event - 500ms debounce)
            $(document).on('input', '.input-bdg', function() {
                if (isLockedSession) return;
                const $this = $(this);
                const productId = $this.data('id');
                const val = $this.val();
                const $row = $this.closest('tr');
                clearTimeout($this.data('saveTimeout'));
                $this.data('saveTimeout', setTimeout(function() {
                    saveSingleField(productId, 'bdg', val, $row);
                }, 500));
            });

            $(document).on('input', '.input-bks', function() {
                if (isLockedSession) return;
                const $this = $(this);
                const productId = $this.data('id');
                const val = $this.val();
                const $row = $this.closest('tr');
                clearTimeout($this.data('saveTimeout'));
                $this.data('saveTimeout', setTimeout(function() {
                    saveSingleField(productId, 'bks', val, $row);
                }, 500));
            });

            $(document).on('input', '.input-note', function() {
                if (isLockedSession) return;
                const $this = $(this);
                const productId = $this.data('id');
                const val = $this.val();
                const $row = $this.closest('tr');
                clearTimeout($this.data('saveTimeout'));
                $this.data('saveTimeout', setTimeout(function() {
                    saveSingleField(productId, 'note', val, $row);
                }, 500));
            });

            // Immediate save on change (blur) or Enter key
            $(document).on('change', '.input-bdg', function() {
                if (isLockedSession) return;
                const $this = $(this);
                clearTimeout($this.data('saveTimeout'));
                saveSingleField($this.data('id'), 'bdg', $this.val(), $this.closest('tr'));
            });

            $(document).on('change', '.input-bks', function() {
                if (isLockedSession) return;
                const $this = $(this);
                clearTimeout($this.data('saveTimeout'));
                saveSingleField($this.data('id'), 'bks', $this.val(), $this.closest('tr'));
            });

            $(document).on('change', '.input-note', function() {
                if (isLockedSession) return;
                const $this = $(this);
                clearTimeout($this.data('saveTimeout'));
                saveSingleField($this.data('id'), 'note', $this.val(), $this.closest('tr'));
            });

            $(document).on('keyup', '.input-bdg, .input-bks, .input-note', function(e) {
                if (e.key === 'Enter') {
                    $(this).trigger('change');
                }
            });

            // Buka Modal Info SKU saat nama SKU diklik
            let currentModalProductId = null;

            $(document).on('click', '.btn-sku-info-trigger', function(e) {
                e.stopPropagation();
                const $btn = $(this);
                currentModalProductId = $btn.data('id');
                const skuCode = $btn.data('sku');
                const productUrl = $btn.data('product-url');
                const isOpname = $btn.data('is-opname') == '1';

                // Populate modal
                $('#modalSkuCode').text(skuCode);
                $('#modalSkuSubtitle').text(skuCode);
                $('#modalBtnDetailSku').attr('href', productUrl);
                $('#modalOpnameToggle').prop('checked', isOpname);
                $('#modalOpnameStatus').html('');

                // Disable toggle jika sesi terkunci
                $('#modalOpnameToggle').prop('disabled', !!isLockedSession);

                const modal = new bootstrap.Modal(document.getElementById('modalSkuInfo'));
                modal.show();
            });

            // Toggle Opname ON/OFF di dalam modal
            $('#modalOpnameToggle').on('change', function() {
                if (isLockedSession) return;
                if (!currentModalProductId) return;

                const $toggle = $(this);
                const newState = $toggle.is(':checked');
                $toggle.prop('disabled', true);
                $('#modalOpnameStatus').html('<div class="alert alert-info py-2 small mb-0"><span class="spinner-border spinner-border-sm me-2"></span>Menyimpan perubahan...</div>');

                $.ajax({
                    url: `/product/replacement/${currentModalProductId}/toggle-opname`,
                    type: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(res) {
                        $toggle.prop('disabled', false);
                        if (res.success) {
                            $toggle.prop('checked', res.is_opname);

                            if (!res.is_opname) {
                                // Jika OFF → hapus row dari DataTable
                                $('#modalOpnameStatus').html(`<div class="alert alert-warning py-2 small mb-0"><i class="mdi mdi-eye-off me-1"></i>${res.message} SKU ini akan hilang dari listing opname.</div>`);
                                setTimeout(function() {
                                    // Hapus baris dari datatable
                                    table.rows().every(function() {
                                        const d = this.data();
                                        if (d && d.product_id == currentModalProductId) {
                                            this.remove();
                                        }
                                    });
                                    table.draw(false);
                                    bootstrap.Modal.getInstance(document.getElementById('modalSkuInfo')).hide();
                                }, 1500);
                            } else {
                                // Jika ON → update dot indicator di row
                                $('#modalOpnameStatus').html(`<div class="alert alert-success py-2 small mb-0"><i class="mdi mdi-check-circle me-1"></i>${res.message}</div>`);
                                table.rows().every(function() {
                                    const d = this.data();
                                    if (d && d.product_id == currentModalProductId) {
                                        d.is_opname = 1;
                                    }
                                });
                                setTimeout(() => table.draw(false), 1500);
                            }

                            const Toast = Swal.mixin({
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 2000
                            });
                            Toast.fire({ icon: 'success', title: res.message });
                        }
                    },
                    error: function() {
                        $toggle.prop('disabled', false);
                        $toggle.prop('checked', !newState); // revert
                        $('#modalOpnameStatus').html('<div class="alert alert-danger py-2 small mb-0"><i class="mdi mdi-alert me-1"></i>Gagal mengubah status opname SKU.</div>');
                    }
                });
            });

            // Handler for Finalize / Simpan & Kunci Stock Opname
            $(document).on('click', '.btn-finalize-opname', function() {
                Swal.fire({
                    title: 'Simpan & Kunci Stock Opname?',
                    text: 'Pastikan seluruh perhitungan fisik telah diverifikasi. Setelah disimpan, seluruh data fisik dan catatan pada sesi Stock Opname ini akan dikunci permanen dan tidak dapat diubah lagi.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Simpan & Kunci Data!',
                    cancelButtonText: 'Periksa Kembali',
                    customClass: {
                        confirmButton: 'btn btn-primary me-3 waves-effect waves-light',
                        cancelButton: 'btn btn-label-secondary waves-effect'
                    },
                    buttonsStyling: false
                }).then(function(result) {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Memfinalisasi Data...',
                            text: 'Sedang mengunci seluruh data stock opname',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        $.ajax({
                            url: `/stock-opname/${opnameId}/finalize`,
                            type: 'POST',
                            data: {
                                _token: $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(res) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: res.message || 'Data Stock Opname berhasil disimpan dan dikunci permanen.',
                                    customClass: {
                                        confirmButton: 'btn btn-primary'
                                    }
                                }).then(() => {
                                    window.location.reload();
                                });
                            },
                            error: function(xhr) {
                                const msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Gagal memfinalisasi data stock opname.';
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal Finalisasi',
                                    text: msg,
                                    customClass: {
                                        confirmButton: 'btn btn-primary'
                                    }
                                });
                            }
                        });
                    }
                });
            });

            // Real-Time Background Live Sync Function
            function checkLiveUpdates() {
                if (isSyncing || !lastServerTime) return;
                isSyncing = true;

                $.ajax({
                    url: `/stock-opname/${opnameId}/sync-updates?since=${encodeURIComponent(lastServerTime)}`,
                    type: 'GET',
                    success: function(res) {
                        if (res.server_time) {
                            lastServerTime = res.server_time;
                        }
                        if (res.current_user_id) currentUserId = res.current_user_id;
                        if (res.is_admin !== undefined) isAdmin = res.is_admin;
                        if (res.is_locked !== undefined && res.is_locked !== isLockedSession) {
                            isLockedSession = res.is_locked;
                            if (isLockedSession) {
                                window.location.reload();
                                return;
                            }
                        }

                        if (res.has_updates && res.updates && res.updates.length > 0) {
                            res.updates.forEach(function(item) {
                                const productId = item.id_product;
                                const $bdgInput = $(`.input-bdg[data-id="${productId}"]`);
                                const $bksInput = $(`.input-bks[data-id="${productId}"]`);
                                const $noteInput = $(`.input-note[data-id="${productId}"]`);
                                const $row = $bdgInput.closest('tr');

                                // 1. Update BDG (if not actively being typed by this user)
                                if (!$bdgInput.is(':focus') && !isLockedSession) {
                                    const isBdgOwner = (!item.id_user_bdg) || (item.id_user_bdg == currentUserId) || isAdmin;
                                    const valBdg = (item.fisik_bdg !== null && item.fisik_bdg !== undefined) ? item.fisik_bdg : '';
                                    const tooltipBdg = formatUserTooltip(item.user_bdg_name, item.updated_at);
                                    $bdgInput.val(valBdg);

                                    if (!isBdgOwner && item.id_user_bdg) {
                                        $bdgInput.prop('disabled', true).addClass('bg-light border-0 text-muted').attr('title', tooltipBdg).attr('data-bs-original-title', tooltipBdg).attr('data-bs-toggle', 'tooltip');
                                        if (!$row.find(`.lock-icon-bdg-${productId}`).length) {
                                            $bdgInput.parent().append(`<i class="mdi mdi-lock text-warning lock-icon-bdg-${productId}" data-bs-toggle="tooltip" data-bs-placement="top" title="${tooltipBdg}"></i>`);
                                        } else {
                                            $row.find(`.lock-icon-bdg-${productId}`).attr('title', tooltipBdg).attr('data-bs-original-title', tooltipBdg);
                                        }
                                    } else {
                                        $bdgInput.prop('disabled', false).removeClass('bg-light border-0 text-muted');
                                        if (tooltipBdg) {
                                            $bdgInput.attr('title', tooltipBdg).attr('data-bs-original-title', tooltipBdg).attr('data-bs-toggle', 'tooltip');
                                        } else {
                                            $bdgInput.removeAttr('title').removeAttr('data-bs-original-title').removeAttr('data-bs-toggle');
                                        }
                                        $row.find(`.lock-icon-bdg-${productId}`).remove();
                                    }
                                }

                                // 2. Update BKS (if not actively being typed by this user)
                                if (!$bksInput.is(':focus') && !isLockedSession) {
                                    const isBksOwner = (!item.id_user_bks) || (item.id_user_bks == currentUserId) || isAdmin;
                                    const valBks = (item.fisik_bks !== null && item.fisik_bks !== undefined) ? item.fisik_bks : '';
                                    const tooltipBks = formatUserTooltip(item.user_bks_name, item.updated_at);
                                    $bksInput.val(valBks);

                                    if (!isBksOwner && item.id_user_bks) {
                                        $bksInput.prop('disabled', true).addClass('bg-light border-0 text-muted').attr('title', tooltipBks).attr('data-bs-original-title', tooltipBks).attr('data-bs-toggle', 'tooltip');
                                        if (!$row.find(`.lock-icon-bks-${productId}`).length) {
                                            $bksInput.parent().append(`<i class="mdi mdi-lock text-warning lock-icon-bks-${productId}" data-bs-toggle="tooltip" data-bs-placement="top" title="${tooltipBks}"></i>`);
                                        } else {
                                            $row.find(`.lock-icon-bks-${productId}`).attr('title', tooltipBks).attr('data-bs-original-title', tooltipBks);
                                        }
                                    } else {
                                        $bksInput.prop('disabled', false).removeClass('bg-light border-0 text-muted');
                                        if (tooltipBks) {
                                            $bksInput.attr('title', tooltipBks).attr('data-bs-original-title', tooltipBks).attr('data-bs-toggle', 'tooltip');
                                        } else {
                                            $bksInput.removeAttr('title').removeAttr('data-bs-original-title').removeAttr('data-bs-toggle');
                                        }
                                        $row.find(`.lock-icon-bks-${productId}`).remove();
                                    }
                                }

                                // 3. Update Note
                                if (!$noteInput.is(':focus') && !isLockedSession) {
                                    $noteInput.val(item.note || '');
                                }

                                // 4. Update Badges
                                const numBdg = parseInt(item.selisih_bdg) || 0;
                                let bdgClass = numBdg === 0 ? 'bg-label-success' : (numBdg > 0 ? 'bg-label-info' : 'bg-label-danger');
                                let bdgText = numBdg > 0 ? '+' + numBdg : numBdg;
                                if (item.fisik_bdg === null) {
                                    $(`.badge-bdg-${productId}`).attr('class', `badge bg-label-secondary badge-selisih badge-bdg-${productId}`).text('-');
                                } else {
                                    $(`.badge-bdg-${productId}`).attr('class', `badge ${bdgClass} badge-selisih badge-bdg-${productId}`).text(bdgText);
                                }

                                const numBks = parseInt(item.selisih_bks) || 0;
                                let bksClass = numBks === 0 ? 'bg-label-success' : (numBks > 0 ? 'bg-label-info' : 'bg-label-danger');
                                let bksText = numBks > 0 ? '+' + numBks : numBks;
                                if (item.fisik_bks === null) {
                                    $(`.badge-bks-${productId}`).attr('class', `badge bg-label-secondary badge-selisih badge-bks-${productId}`).text('-');
                                } else {
                                    $(`.badge-bks-${productId}`).attr('class', `badge ${bksClass} badge-selisih badge-bks-${productId}`).text(bksText);
                                }

                                const numTotal = parseInt(item.selisih_total) || 0;
                                let totalClass = numTotal === 0 ? 'bg-success' : (numTotal > 0 ? 'bg-info' : 'bg-danger');
                                let totalText = numTotal > 0 ? '+' + numTotal : numTotal;
                                if (item.fisik_bdg === null && item.fisik_bks === null) {
                                    $(`.badge-total-${productId}`).attr('class', `badge bg-label-secondary badge-selisih badge-total-${productId}`).text('-');
                                    $(`#statusIcon-${productId}`).html('<i class="mdi mdi-minus-circle-outline text-muted fs-5" title="Belum dihitung"></i>');
                                } else {
                                    $(`.badge-total-${productId}`).attr('class', `badge ${totalClass} badge-selisih badge-total-${productId}`).text(totalText);
                                    $(`#statusIcon-${productId}`).html('<i class="mdi mdi-check-circle text-success fs-5" title="Tersimpan"></i>');
                                }

                                // Visual flash effect on the updated row
                                if ($row.length) {
                                    $row.addClass('live-updated-row');
                                    setTimeout(() => $row.removeClass('live-updated-row'), 2500);
                                }

                                // Update DataTable row memory
                                table.rows().every(function() {
                                    const d = this.data();
                                    if (d && d.product_id == productId) {
                                        d.fisik_bdg = item.fisik_bdg;
                                        d.id_user_bdg = item.id_user_bdg;
                                        d.user_bdg_name = item.user_bdg_name;
                                        d.user_bdg_image = item.user_bdg_image;
                                        d.fisik_bks = item.fisik_bks;
                                        d.id_user_bks = item.id_user_bks;
                                        d.user_bks_name = item.user_bks_name;
                                        d.user_bks_image = item.user_bks_image;
                                        d.fisik_total = item.fisik_total;
                                        d.selisih_bdg = item.selisih_bdg;
                                        d.selisih_bks = item.selisih_bks;
                                        d.selisih_total = item.selisih_total;
                                        d.note = item.note;
                                        d.updated_at = item.updated_at;
                                        d.is_counted = (item.fisik_bdg !== null || item.fisik_bks !== null) ? 1 : 0;
                                    }
                                });
                            });

                            $('[data-bs-toggle="tooltip"]').tooltip();
                        }

                        // Update KPI cards in real-time
                        if (res.stats) {
                            $('#statTotalSku').text(new Intl.NumberFormat().format(res.stats.total_sku));
                            $('#statCountedSku').text(new Intl.NumberFormat().format(res.stats.counted_sku));
                            $('#statProgressPercent').text(res.stats.progress_percent + '%');
                            $('#statProgressBar').css('width', res.stats.progress_percent + '%').attr('aria-valuenow', res.stats.progress_percent);
                            
                            const sumBdg = res.stats.sum_selisih_bdg;
                            $('#statSelisihBdgPcs').html((sumBdg > 0 ? '+' : '') + new Intl.NumberFormat().format(sumBdg) + ' <small class="fs-6 fw-normal text-muted">pcs</small>');
                            $('#statSelisihBdgCount').attr('class', 'badge ' + (res.stats.selisih_bdg_count > 0 ? 'bg-label-danger' : 'bg-label-success')).text(new Intl.NumberFormat().format(res.stats.selisih_bdg_count) + ' SKU');

                            const sumBks = res.stats.sum_selisih_bks;
                            $('#statSelisihBksPcs').html((sumBks > 0 ? '+' : '') + new Intl.NumberFormat().format(sumBks) + ' <small class="fs-6 fw-normal text-muted">pcs</small>');
                            $('#statSelisihBksCount').attr('class', 'badge ' + (res.stats.selisih_bks_count > 0 ? 'bg-label-danger' : 'bg-label-success')).text(new Intl.NumberFormat().format(res.stats.selisih_bks_count) + ' SKU');

                            const sumTotal = res.stats.sum_total_selisih;
                            $('#statSumTotalSelisih').attr('class', 'fw-bold mb-0 mt-1 ' + (sumTotal != 0 ? 'text-danger' : 'text-success')).html((sumTotal > 0 ? '+' : '') + new Intl.NumberFormat().format(sumTotal) + ' <small class="fs-6 fw-normal text-muted">pcs</small>');
                            $('#statTotalSelisihCount').attr('class', 'badge ' + (res.stats.total_selisih_count > 0 ? 'bg-label-danger' : 'bg-label-success')).text(new Intl.NumberFormat().format(res.stats.total_selisih_count) + ' SKU');

                            // Update Milestone 2 card realtime
                            const isAllCounted = res.stats.progress_percent >= 100;
                            const fmtCounted = new Intl.NumberFormat().format(res.stats.counted_sku);
                            const fmtTotal = new Intl.NumberFormat().format(res.stats.total_sku);
                            $('#m2SkuCount')
                                .attr('class', 'fw-bold mb-1 mt-1 ' + (isAllCounted ? 'text-success' : 'text-primary'))
                                .text(fmtCounted + ' / ' + fmtTotal + ' SKU');
                            $('#m2ProgressText').text(res.stats.progress_percent + '%');
                            $('#m2ProgressBar')
                                .css('width', res.stats.progress_percent + '%')
                                .attr('class', 'progress-bar ' + (isAllCounted ? 'bg-success' : 'bg-primary'));
                            if (isAllCounted) {
                                $('#m2StatusIcon').replaceWith('<i id="m2StatusIcon" class="mdi mdi-check-circle text-success fs-5"></i>');
                            } else {
                                if ($('#m2StatusIcon').is('i')) {
                                    $('#m2StatusIcon').replaceWith('<div id="m2StatusIcon" class="spinner-border spinner-border-sm text-primary" role="status"></div>');
                                }
                            }
                        }
                    },
                    complete: function() {
                        isSyncing = false;
                    }
                });
            }

            // Run Live Sync check every 2.5 seconds
            setInterval(checkLiveUpdates, 2500);

            // Bulk Fill All = System
            $('#btnBulkFillSystem').on('click', function() {
                if (isLockedSession) return;
                Swal.fire({
                    title: 'Set Semua Fisik = Stok Sistem?',
                    text: 'Semua produk yang terdaftar akan otomatis diisi fisik BDG & BKS sesuai stok sistem saat ini (0 selisih). Anda cukup mencari dan mengedit yang terjadi selisih.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Terapkan Otomatis!',
                    cancelButtonText: 'Batal',
                    customClass: {
                        confirmButton: 'btn btn-success me-3 waves-effect waves-light',
                        cancelButton: 'btn btn-label-secondary waves-effect'
                    },
                    buttonsStyling: false
                }).then(function(result) {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Memproses Pengisian...',
                            text: 'Mohon tunggu sebentar',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        $.ajax({
                            url: `/stock-opname/${opnameId}/bulk-fill-system`,
                            type: 'POST',
                            success: function(res) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: res.message || 'Semua produk telah diset sesuai stok sistem.',
                                    customClass: {
                                        confirmButton: 'btn btn-primary'
                                    }
                                }).then(() => {
                                    window.location.reload();
                                });
                            },
                            error: function() {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: 'Terjadi kesalahan saat memproses data.',
                                    customClass: {
                                        confirmButton: 'btn btn-primary'
                                    }
                                });
                            }
                        });
                    }
                });
            });

            // Bulk Reset Hitungan
            $('#btnBulkReset').on('click', function() {
                if (isLockedSession) return;
                Swal.fire({
                    title: 'Reset Semua Hitungan Fisik?',
                    text: 'Seluruh angka fisik yang sudah diinput pada sesi ini akan dikosongkan kembali.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Reset Hitungan!',
                    cancelButtonText: 'Batal',
                    customClass: {
                        confirmButton: 'btn btn-warning me-3 waves-effect waves-light',
                        cancelButton: 'btn btn-label-secondary waves-effect'
                    },
                    buttonsStyling: false
                }).then(function(result) {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Mereset Data...',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        $.ajax({
                            url: `/stock-opname/${opnameId}/bulk-reset`,
                            type: 'POST',
                            success: function(res) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Selesai!',
                                    text: res.message,
                                    customClass: {
                                        confirmButton: 'btn btn-primary'
                                    }
                                }).then(() => {
                                    window.location.reload();
                                });
                            },
                            error: function() {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: 'Gagal mereset data hitungan.',
                                    customClass: {
                                        confirmButton: 'btn btn-primary'
                                    }
                                });
                            }
                        });
                    }
                });
            });

            // Delete Session
            $('#btnDeleteSession').on('click', function() {
                if (isLockedSession) return;
                Swal.fire({
                    title: 'Hapus Sesi Stock Opname Ini?',
                    text: 'Sesi ini beserta seluruh data hitungan fisik di dalamnya akan dihapus permanen.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Hapus Sesi!',
                    cancelButtonText: 'Batal',
                    customClass: {
                        confirmButton: 'btn btn-danger me-3 waves-effect waves-light',
                        cancelButton: 'btn btn-label-secondary waves-effect'
                    },
                    buttonsStyling: false
                }).then(function(result) {
                    if (result.isConfirmed) {
                        $('#formDeleteSession').submit();
                    }
                });
            });
        });
    </script>
@endpush
