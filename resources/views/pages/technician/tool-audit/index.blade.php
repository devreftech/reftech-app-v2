@extends('layouts.sales.app')
@section('title', 'Self-Audit Tools Teknisi')
@section('hide-chat', true)
@section('content')
    <style>
        .card-stat-widget {
            border: 1px solid rgba(0, 0, 0, 0.06);
            border-radius: 14px;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            background: #ffffff;
        }

        .card-stat-widget:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 22px rgba(0, 0, 0, 0.08);
        }

        .stat-icon-wrapper {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }

        .hero-audit-banner {
            border-radius: 16px;
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .hero-audit-banner.urgent {
            background: linear-gradient(135deg, #fff8f0 0%, #fff1e0 100%);
            border-left: 5px solid #ff9f43;
        }

        .hero-audit-banner.clean {
            background: linear-gradient(135deg, #f3fdf6 0%, #e8f9ee 100%);
            border-left: 5px solid #28c76f;
        }

        /* Mobile-first Nav Pills & Responsive Tabs */
        .nav-tabs-scroll-wrapper {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: none;
            -ms-overflow-style: none;
            padding-bottom: 2px;
        }
        .nav-tabs-scroll-wrapper::-webkit-scrollbar {
            display: none;
        }

        .nav-pills-custom {
            display: flex;
            flex-wrap: nowrap;
            white-space: nowrap;
            margin-bottom: 0;
        }

        .nav-pills-custom .nav-link {
            border-radius: 10px;
            padding: 8px 14px;
            font-weight: 600;
            color: #566a7f;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            flex-shrink: 0;
            font-size: 13px;
        }

        @media (max-width: 575.98px) {
            .nav-pills-custom .nav-link {
                padding: 7px 11px;
                font-size: 12px;
            }
        }

        .nav-pills-custom .nav-link.active {
            background-color: #696cff;
            color: #ffffff !important;
            box-shadow: 0 4px 14px rgba(105, 108, 255, 0.35);
        }

        .nav-pills-custom .nav-link.active .badge.bg-label-secondary {
            background-color: rgba(255, 255, 255, 0.25) !important;
            color: #ffffff !important;
        }

        /* Filter status horizontal scroll on mobile */
        .status-filter-scroll-wrapper {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: none;
            -ms-overflow-style: none;
            padding-bottom: 4px;
        }
        .status-filter-scroll-wrapper::-webkit-scrollbar {
            display: none;
        }

        .tool-grid-card {
            border-radius: 12px;
            border: 1px solid rgba(0, 0, 0, 0.07);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            background: #ffffff;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .tool-grid-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.08);
        }

        .pulse-badge {
            animation: pulse-animation 2s infinite;
        }

        @keyframes pulse-animation {
            0% { box-shadow: 0 0 0 0 rgba(255, 159, 67, 0.5); }
            70% { box-shadow: 0 0 0 8px rgba(255, 159, 67, 0); }
            100% { box-shadow: 0 0 0 0 rgba(255, 159, 67, 0); }
        }

        .badge-cond-ada {
            background-color: rgba(40, 199, 111, 0.12);
            color: #28c76f;
            border: 1px solid rgba(40, 199, 111, 0.25);
        }

        .badge-cond-rusak {
            background-color: rgba(255, 159, 67, 0.12);
            color: #ff9f43;
            border: 1px solid rgba(255, 159, 67, 0.25);
        }

        .badge-cond-hilang {
            background-color: rgba(234, 84, 85, 0.12);
            color: #ea5455;
            border: 1px solid rgba(234, 84, 85, 0.25);
        }

        .incoming-transfer-card {
            border-left: 4px solid #ffab00;
            background: #fffcf5;
            border-radius: 12px;
        }
    </style>

    {{-- Alert Messages --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4 shadow-sm" role="alert">
            <div class="d-flex align-items-center">
                <i class="mdi mdi-check-circle-outline fs-4 me-2"></i>
                <div class="fw-medium">{{ session('success') }}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-4 shadow-sm" role="alert">
            <div class="d-flex align-items-center">
                <i class="mdi mdi-alert-circle-outline fs-4 me-2"></i>
                <div class="fw-medium">{{ session('error') }}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-4 shadow-sm" role="alert">
            <div class="fw-bold mb-1"><i class="mdi mdi-alert-circle me-1"></i>Terjadi kesalahan pengisian data:</div>
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Header Section --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <div class="d-flex align-items-center gap-2">
                <h4 class="fw-bold mb-0 text-dark">
                    <i class="mdi mdi-tools text-primary me-2"></i>Self-Audit Tools Teknisi
                </h4>
                <span class="badge bg-label-primary font-12 rounded-pill px-3 py-1">Role: Teknisi</span>
            </div>
            <p class="text-muted mb-0 small mt-1">
                Lakukan pengecekan fisik berkala, kelola kepemilikan alat, dan laporkan kondisi terkini alat kerja Anda.
            </p>
        </div>
        <div class="d-flex align-items-center gap-2">
            @if ($activeAudit)
                <span class="badge bg-label-warning px-3 py-2 rounded-pill font-12 fw-semibold">
                    <i class="mdi mdi-clock-alert-outline me-1"></i>
                    Periode {{ $activeAudit->period?->period_title ?? 'Audit' }} Aktif
                </span>
            @else
                <span class="badge bg-label-success px-3 py-2 rounded-pill font-12 fw-semibold">
                    <i class="mdi mdi-check-all me-1"></i> Tidak Ada Audit Tertunda
                </span>
            @endif
        </div>
    </div>

    {{-- Hero Callout Banner --}}
    @if ($activeAudit)
        @php
            $period = $activeAudit->period;
            $dueDate = $period ? \Carbon\Carbon::parse($period->tanggal_selesai) : null;
            $daysLeft = $dueDate ? ceil(now()->floatDiffInDays($dueDate, false)) : 0;
            $isRejected = $activeAudit->status_submit === 'Rejected';
            
            // Progress calculation
            $totalItems = $activeAudit->items->count();
            $filledItems = $activeAudit->items->filter(function($it) {
                return !empty($it->kondisi);
            })->count();
            $percent = $totalItems > 0 ? round(($filledItems / $totalItems) * 100) : 0;
        @endphp
        <div class="card hero-audit-banner urgent shadow-sm mb-4">
            <div class="card-body p-4">
                <div class="row align-items-center g-3">
                    <div class="col-lg-8 col-md-7">
                        <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                            <span class="badge {{ $isRejected ? 'bg-danger' : 'bg-warning text-dark' }} px-3 py-1 font-12 fw-bold text-uppercase rounded-pill">
                                <i class="mdi {{ $isRejected ? 'mdi-alert-circle' : 'mdi-clock-alert-outline' }} me-1"></i>
                                {{ $isRejected ? 'Perlu Revisi Teknisi' : 'Periode Audit Sedang Berlangsung' }}
                            </span>
                            @if ($dueDate)
                                <span class="badge bg-label-danger font-12 fw-semibold rounded-pill px-3 py-1">
                                    <i class="mdi mdi-calendar-clock me-1"></i>
                                    @if ($daysLeft > 0)
                                        Batas Waktu: Sisa {{ $daysLeft }} Hari Lagi ({{ $dueDate->translatedFormat('d M Y') }})
                                    @elseif ($daysLeft === 0)
                                        Batas Waktu: Hari Ini ({{ $dueDate->translatedFormat('d M Y') }})!
                                    @else
                                        Terlewat {{ abs($daysLeft) }} Hari dari Jadwal!
                                    @endif
                                </span>
                            @endif
                        </div>
                        <h5 class="fw-bold text-dark mb-1">
                            {{ $period ? $period->period_title : 'Periode Audit Tools' }} — No. Audit: <span class="text-primary">{{ $activeAudit->no_audit }}</span>
                        </h5>
                        <p class="text-muted small mb-3">
                            @if ($isRejected)
                                <span class="text-danger fw-bold"><i class="mdi mdi-alert-circle-outline me-1"></i>Catatan Admin:</span>
                                <em>"{{ $activeAudit->catatan_admin ?? 'Silakan lengkapi/perbaiki foto kondisi tools sesuai arahan admin.' }}"</em>
                            @else
                                Anda memiliki <strong>{{ $totalItems }} unit tools</strong> yang wajib dilaporkan kondisi fisiknya (Ada, Rusak, atau Hilang).
                                Sistem dilengkapi <strong>Auto-Save</strong> sehingga progres tersimpan otomatis.
                            @endif
                        </p>
                        
                        {{-- Mini Progress --}}
                        <div class="d-flex align-items-center gap-3">
                            <div class="progress flex-grow-1" style="height: 8px; border-radius: 6px; background-color: rgba(0,0,0,0.08);">
                                <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $percent }}%;" aria-valuenow="{{ $percent }}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <span class="small fw-bold text-dark font-12 text-nowrap">
                                {{ $filledItems }} / {{ $totalItems }} Tools Diperiksa ({{ $percent }}%)
                            </span>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-5 text-md-end">
                        <div class="d-flex flex-column flex-sm-row flex-md-column gap-2 justify-content-md-end align-items-stretch align-items-md-end">
                            <a href="{{ route('tool-audit.show', $activeAudit->id) }}" class="btn btn-primary btn-lg px-4 py-2 shadow-sm font-14 fw-bold">
                                <i class="mdi mdi-clipboard-edit-outline me-1"></i>
                                {{ $isRejected ? 'Perbaiki Form Audit' : ($filledItems > 0 ? 'Lanjutkan Pengisian' : 'Mulai Isi Audit') }}
                            </a>
                            <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-3 py-1 font-12" data-bs-toggle="modal" data-bs-target="#modalPanduanAudit">
                                <i class="mdi mdi-book-open-page-variant-outline me-1"></i> Baca Panduan &amp; Ketentuan
                            </button>
                        </div>
                        <div class="text-muted small mt-2">
                            <i class="mdi mdi-shield-check-outline text-success me-1"></i> Perubahan tersimpan otomatis
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="card hero-audit-banner clean shadow-sm mb-4">
            <div class="card-body p-4">
                <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="stat-icon-wrapper bg-label-success text-success" style="width: 54px; height: 54px;">
                            <i class="mdi mdi-check-decagram fs-2"></i>
                        </div>
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <span class="badge bg-success font-11 rounded-pill px-3 py-1">STATUS AMAN</span>
                                <span class="text-muted small">Semua Laporan Selesai</span>
                            </div>
                            <h6 class="fw-bold text-dark mb-1">Tidak Ada Audit Tools yang Tertunda</h6>
                            <p class="text-muted small mb-0">
                                Seluruh laporan audit Anda telah disubmit atau terverifikasi. Periode audit berikutnya akan dibuka otomatis di akhir kuartal berikutnya (Maret, Juni, September, Desember).
                            </p>
                        </div>
                    </div>
                    <div>
                        <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3 py-2 font-12 text-nowrap" data-bs-toggle="modal" data-bs-target="#modalPanduanAudit">
                            <i class="mdi mdi-book-open-page-variant-outline me-1"></i> Panduan &amp; Ketentuan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- KPI Stat Cards Grid --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="card card-stat-widget h-100 shadow-xs">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small fw-semibold">Tools Dipegang</span>
                        <div class="stat-icon-wrapper bg-label-primary text-primary" style="width: 40px; height: 40px;">
                            <i class="mdi mdi-tools fs-5"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold mb-1 text-dark">{{ $assignedTools->count() }} <span class="fs-6 fw-normal text-muted">unit</span></h3>
                    <div class="small text-muted d-flex align-items-center">
                        <i class="mdi mdi-account-check-outline text-success me-1"></i> Aktif atas nama Anda
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="card card-stat-widget h-100 shadow-xs">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small fw-semibold">Audit Aktif</span>
                        <div class="stat-icon-wrapper bg-label-warning text-warning" style="width: 40px; height: 40px;">
                            <i class="mdi mdi-calendar-clock fs-5"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold mb-1 text-dark">
                        {{ $activeAudit ? '1 Periode' : '0' }}
                    </h3>
                    <div class="small text-muted d-flex align-items-center">
                        @if ($activeAudit)
                            <span class="text-warning fw-semibold"><i class="mdi mdi-circle-medium"></i> Sedang berjalan</span>
                        @else
                            <span class="text-success"><i class="mdi mdi-check-circle-outline"></i> Sesuai jadwal</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="card card-stat-widget h-100 shadow-xs">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small fw-semibold">Butuh Tindakan</span>
                        <div class="stat-icon-wrapper {{ $actionRequiredCount > 0 ? 'bg-label-danger text-danger' : 'bg-label-secondary text-secondary' }}" style="width: 40px; height: 40px;">
                            <i class="mdi mdi-alert-circle-outline fs-5"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold mb-1 {{ $actionRequiredCount > 0 ? 'text-danger' : 'text-dark' }}">
                        {{ $actionRequiredCount }} <span class="fs-6 fw-normal text-muted">audit</span>
                    </h3>
                    <div class="small text-muted d-flex align-items-center">
                        @if ($rejectedAuditsCount > 0)
                            <span class="text-danger fw-semibold">{{ $rejectedAuditsCount }} revisi</span> &bull; {{ $draftAuditsCount }} draft
                        @else
                            <span>{{ $draftAuditsCount }} draft tersimpan</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="card card-stat-widget h-100 shadow-xs">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small fw-semibold">Transfer Masuk</span>
                        <div class="stat-icon-wrapper {{ $incomingTransfers->count() > 0 ? 'bg-label-warning text-warning' : 'bg-label-info text-info' }}" style="width: 40px; height: 40px;">
                            <i class="mdi mdi-account-switch-outline fs-5"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold mb-1 {{ $incomingTransfers->count() > 0 ? 'text-warning' : 'text-dark' }}">
                        {{ $incomingTransfers->count() }} <span class="fs-6 fw-normal text-muted">permintaan</span>
                    </h3>
                    <div class="small text-muted d-flex align-items-center">
                        @if ($incomingTransfers->count() > 0)
                            <span class="text-warning fw-semibold"><i class="mdi mdi-clock-alert-outline"></i> Menunggu persetujuan</span>
                        @else
                            <span class="text-muted"><i class="mdi mdi-check-circle-outline"></i> Tidak ada antrean</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Tab Navigation --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header border-bottom p-3">
            <div class="d-flex flex-column flex-lg-row align-items-stretch align-items-lg-center justify-content-between gap-2 gap-lg-3">
                <div class="nav-tabs-scroll-wrapper">
                    <ul class="nav nav-pills nav-pills-custom gap-2" id="toolAuditTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="tab-audits-btn" data-bs-toggle="pill" data-bs-target="#tab-audits" type="button" role="tab">
                                <i class="mdi mdi-clipboard-text-clock-outline me-1"></i>
                                <span class="d-none d-sm-inline">Daftar Riwayat Audit</span>
                                <span class="d-sm-none">Riwayat Audit</span>
                                <span class="badge bg-white text-primary rounded-pill ms-1 font-11">{{ $audits->count() }}</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="tab-tools-btn" data-bs-toggle="pill" data-bs-target="#tab-tools" type="button" role="tab">
                                <i class="mdi mdi-wrench-outline me-1"></i>
                                <span class="d-none d-sm-inline">Tools Saya Saat Ini</span>
                                <span class="d-sm-none">Tools Saya</span>
                                <span class="badge bg-label-secondary rounded-pill ms-1 font-11">{{ $assignedTools->count() }}</span>
                                @if ($incomingTransfers->count() > 0)
                                    <span class="badge bg-danger rounded-pill ms-1 font-10 pulse-badge">
                                        <span class="d-none d-sm-inline">{{ $incomingTransfers->count() }} Transfer Masuk</span>
                                        <span class="d-sm-none">{{ $incomingTransfers->count() }} Baru</span>
                                    </span>
                                @endif
                            </button>
                        </li>
                    </ul>
                </div>

                {{-- Live Search Filter --}}
                <div class="position-relative w-100 mt-2 mt-lg-0" style="max-width: 320px;">
                    <i class="mdi mdi-magnify position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                    <input type="text" id="liveSearchInput" class="form-control form-control-sm ps-5 rounded-pill" placeholder="Cari audit atau tools...">
                </div>
            </div>
        </div>

        <div class="card-body p-3 p-md-4">
            <div class="tab-content p-0" id="toolAuditTabsContent">
                
                {{-- TAB 1: DAFTAR AUDIT --}}
                <div class="tab-pane fade show active" id="tab-audits" role="tabpanel">
                    
                    {{-- Status Filter Badges --}}
                    <div class="status-filter-scroll-wrapper mb-3">
                        <div class="d-flex align-items-center gap-1 gap-sm-2 flex-nowrap flex-md-wrap">
                            <span class="small text-muted fw-semibold me-1 text-nowrap d-none d-sm-inline">Filter Status:</span>
                            <button type="button" class="btn btn-xs btn-outline-primary active btn-audit-filter rounded-pill text-nowrap" data-filter="all">Semua ({{ $audits->count() }})</button>
                            <button type="button" class="btn btn-xs btn-outline-secondary btn-audit-filter rounded-pill text-nowrap" data-filter="Draft">Draft ({{ $draftAuditsCount }})</button>
                            <button type="button" class="btn btn-xs btn-outline-warning btn-audit-filter rounded-pill text-nowrap" data-filter="Submitted">Menunggu ({{ $submittedAuditsCount }})</button>
                            <button type="button" class="btn btn-xs btn-outline-success btn-audit-filter rounded-pill text-nowrap" data-filter="Verified">Terverifikasi ({{ $verifiedAuditsCount }})</button>
                            <button type="button" class="btn btn-xs btn-outline-danger btn-audit-filter rounded-pill text-nowrap" data-filter="Rejected">Revisi ({{ $rejectedAuditsCount }})</button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="tableAudits">
                            <thead class="table-light">
                                <tr>
                                    <th style="min-width: 140px;">No. Audit</th>
                                    <th style="min-width: 180px;">Periode &amp; Window</th>
                                    <th style="min-width: 170px;">Rincian Kondisi Tools</th>
                                    <th style="min-width: 140px;">Status Submit</th>
                                    <th style="min-width: 140px;">Waktu Submit / Review</th>
                                    <th class="text-center" style="min-width: 130px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($audits as $audit)
                                    @php
                                        $period = $audit->period;
                                        $statusClass = [
                                            'Draft' => 'bg-label-secondary',
                                            'Submitted' => 'bg-label-warning',
                                            'Verified' => 'bg-label-success',
                                            'Rejected' => 'bg-label-danger',
                                        ][$audit->status_submit] ?? 'bg-label-secondary';

                                        $items = $audit->items;
                                        $countAda = $items->where('kondisi', 'Ada')->count();
                                        $countRusak = $items->where('kondisi', 'Rusak')->count();
                                        $countHilang = $items->where('kondisi', 'Hilang')->count();
                                        $countBelum = $items->whereNull('kondisi')->count();
                                        $total = $audit->total_tools ?: $items->count();

                                        $searchText = strtolower($audit->no_audit . ' ' . ($period ? $period->period_title : '') . ' ' . $audit->status_submit);
                                    @endphp
                                    <tr class="audit-item-row" data-status="{{ $audit->status_submit }}" data-search="{{ $searchText }}">
                                        <td>
                                            <div class="fw-bold text-dark font-13">{{ $audit->no_audit }}</div>
                                            <div class="small text-muted font-11">ID: #{{ $audit->id }}</div>
                                        </td>
                                        <td>
                                            <div class="fw-semibold text-heading font-13">
                                                {{ $period ? $period->period_title : 'Periode Audit' }}
                                            </div>
                                            @if ($period)
                                                <div class="small text-muted font-11">
                                                    <i class="mdi mdi-calendar-range me-1"></i>
                                                    {{ \Carbon\Carbon::parse($period->tanggal_mulai)->translatedFormat('d M') }} s/d {{ \Carbon\Carbon::parse($period->tanggal_selesai)->translatedFormat('d M Y') }}
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex flex-wrap gap-1 align-items-center mb-1">
                                                <span class="badge badge-cond-ada font-11 px-2 py-0" title="Kondisi Ada">
                                                    <i class="mdi mdi-check-circle-outline me-1"></i>{{ $countAda }} Ada
                                                </span>
                                                @if ($countRusak > 0)
                                                    <span class="badge badge-cond-rusak font-11 px-2 py-0" title="Kondisi Rusak">
                                                        <i class="mdi mdi-alert-circle-outline me-1"></i>{{ $countRusak }} Rusak
                                                    </span>
                                                @endif
                                                @if ($countHilang > 0)
                                                    <span class="badge badge-cond-hilang font-11 px-2 py-0" title="Kondisi Hilang">
                                                        <i class="mdi mdi-close-circle-outline me-1"></i>{{ $countHilang }} Hilang
                                                    </span>
                                                @endif
                                                @if ($countBelum > 0 && in_array($audit->status_submit, ['Draft', 'Rejected']))
                                                    <span class="badge bg-label-secondary font-11 px-2 py-0" title="Belum Dicek">
                                                        {{ $countBelum }} Belum Dicek
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="small text-muted font-11">
                                                Total terdaftar: <strong>{{ $total }} tools</strong>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge {{ $statusClass }} font-12 px-3 py-1 rounded-pill">
                                                @if ($audit->status_submit === 'Draft')
                                                    <i class="mdi mdi-file-document-edit-outline me-1"></i>Draft
                                                @elseif ($audit->status_submit === 'Submitted')
                                                    <i class="mdi mdi-clock-outline me-1"></i>Menunggu Verifikasi
                                                @elseif ($audit->status_submit === 'Verified')
                                                    <i class="mdi mdi-check-decagram me-1"></i>Terverifikasi
                                                @elseif ($audit->status_submit === 'Rejected')
                                                    <i class="mdi mdi-alert-circle me-1"></i>Butuh Revisi
                                                @else
                                                    {{ $audit->status_submit }}
                                                @endif
                                            </span>
                                            @if ($audit->status_submit === 'Rejected' && $audit->catatan_admin)
                                                <div class="small text-danger mt-1 font-11" title="{{ $audit->catatan_admin }}">
                                                    <i class="mdi mdi-message-alert-outline me-1"></i>{{ \Illuminate\Support\Str::limit($audit->catatan_admin, 35) }}
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($audit->submitted_at)
                                                <div class="small text-dark font-12 fw-medium">
                                                    <i class="mdi mdi-send-check-outline text-success me-1"></i>
                                                    {{ \Carbon\Carbon::parse($audit->submitted_at)->translatedFormat('d M Y, H:i') }}
                                                </div>
                                            @else
                                                <div class="small text-muted font-12 fst-italic">Belum disubmit</div>
                                            @endif
                                            @if ($audit->verified_at)
                                                <div class="small text-muted font-11 mt-1">
                                                    Verifikasi: {{ \Carbon\Carbon::parse($audit->verified_at)->translatedFormat('d M Y') }}
                                                </div>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if ($audit->status_submit === 'Rejected')
                                                <a href="{{ route('tool-audit.show', $audit->id) }}" class="btn btn-sm btn-outline-danger px-3 py-1 font-12">
                                                    <i class="mdi mdi-alert-circle-outline me-1"></i> Perbaiki
                                                </a>
                                            @elseif ($audit->status_submit === 'Draft' && $period && $period->status === 'Open')
                                                <a href="{{ route('tool-audit.show', $audit->id) }}" class="btn btn-sm btn-outline-primary px-3 py-1 font-12">
                                                    <i class="mdi mdi-pencil-outline me-1"></i> Lanjutkan
                                                </a>
                                            @else
                                                <a href="{{ route('tool-audit.show', $audit->id) }}" class="btn btn-sm btn-outline-secondary px-3 py-1 font-12">
                                                    <i class="mdi mdi-eye-outline me-1"></i> Detail
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-5 text-muted">
                                            <div class="avatar avatar-lg mx-auto mb-3 bg-label-secondary rounded-circle d-flex align-items-center justify-content-center" style="width: 54px; height: 54px;">
                                                <i class="mdi mdi-clipboard-text-clock-outline fs-2 text-secondary"></i>
                                            </div>
                                            <h6 class="fw-bold mb-1 text-secondary">Belum Ada Periode Audit</h6>
                                            <p class="small text-muted mb-0 max-w-500 mx-auto">
                                                Audit tools akan terbuka otomatis pada akhir kuartal (Maret, Juni, September, Desember) atau ketika admin warehouse membuka jadwal audit baru.
                                            </p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- TAB 2: DAFTAR TOOLS SAYA & FITUR TRANSFER KEPEMILIKAN --}}
                <div class="tab-pane fade" id="tab-tools" role="tabpanel">

                    {{-- INCOMING TRANSFER REQUESTS NOTIFICATION SECTION --}}
                    @if ($incomingTransfers->count() > 0)
                        <div class="card incoming-transfer-card shadow-sm border mb-4">
                            <div class="card-body p-3 p-md-4">
                                <div class="d-flex align-items-center gap-2 mb-3">
                                    <div class="avatar avatar-sm bg-warning text-white rounded-circle d-flex align-items-center justify-content-center">
                                        <i class="mdi mdi-account-arrow-right fs-5"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold text-dark mb-0">Permintaan Transfer Kepemilikan Masuk ({{ $incomingTransfers->count() }})</h6>
                                        <div class="small text-muted font-11">Teknisi lain mengajukan pemindahan alat ke akun Anda. Harap tinjau dan konfirmasi penerimaan.</div>
                                    </div>
                                </div>

                                <div class="row g-3">
                                    @foreach ($incomingTransfers as $inc)
                                        @php
                                            $incAsset = $inc->fixedAsset;
                                            $incMaster = $incAsset->toolsMaster ?? null;
                                            $incFoto = $inc->foto_kondisi ? asset($inc->foto_kondisi) : ($incAsset->foto_awal ? asset($incAsset->foto_awal) : asset('assets/img/illustrations/tool-placeholder.png'));
                                        @endphp
                                        <div class="col-12 col-lg-6">
                                            <div class="card border p-3 bg-white shadow-xs">
                                                <div class="d-flex gap-3 align-items-start">
                                                    <a href="javascript:void(0);" onclick="previewPhoto('{{ $incFoto }}', '{{ addslashes($incMaster->nama_tools ?? $incAsset->code) }}')">
                                                        <img src="{{ $incFoto }}" alt="Foto Tool" class="rounded border shadow-xs" style="width: 72px; height: 72px; object-fit: cover;">
                                                    </a>
                                                    <div class="flex-grow-1 min-w-0">
                                                        <div class="d-flex align-items-center justify-content-between gap-1 mb-1">
                                                            <h6 class="fw-bold text-dark mb-0 font-14 text-truncate">{{ $incMaster->nama_tools ?? $incAsset->desc }}</h6>
                                                            <span class="badge bg-label-warning font-10">Pending</span>
                                                        </div>
                                                        <div class="small text-muted font-11 mb-1">
                                                            Pengirim: <strong class="text-primary">{{ $inc->fromUser?->name ?? 'Teknisi' }}</strong> &bull; {{ \Carbon\Carbon::parse($inc->requested_at)->diffForHumans() }}
                                                        </div>
                                                        <div class="small text-muted font-11 mb-2">
                                                            Kode: <code>{{ $incAsset->code }}</code> | Qty: <strong>{{ $incAsset->qty }}</strong>
                                                        </div>
                                                        @if ($inc->catatan_pengirim)
                                                            <div class="alert alert-secondary py-1 px-2 mb-2 font-11 rounded">
                                                                <i class="mdi mdi-message-text-outline me-1"></i><em>"{{ $inc->catatan_pengirim }}"</em>
                                                            </div>
                                                        @endif
                                                        <div class="d-flex gap-2 mt-2">
                                                            <button type="button" class="btn btn-sm btn-success px-3 py-1 font-12 rounded-pill shadow-xs"
                                                                onclick="openAcceptTransferModal({{ $inc->id }}, '{{ addslashes($incMaster->nama_tools ?? $incAsset->code) }}', '{{ addslashes($inc->fromUser?->name ?? 'Teknisi') }}')">
                                                                <i class="mdi mdi-check me-1"></i> Terima Alat
                                                            </button>
                                                            <button type="button" class="btn btn-sm btn-outline-danger px-3 py-1 font-12 rounded-pill"
                                                                onclick="openRejectTransferModal({{ $inc->id }}, '{{ addslashes($incMaster->nama_tools ?? $incAsset->code) }}', '{{ addslashes($inc->fromUser?->name ?? 'Teknisi') }}')">
                                                                <i class="mdi mdi-close me-1"></i> Tolak
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- OUTGOING PENDING TRANSFERS ALERT --}}
                    @php
                        $myPendingOutgoing = $outgoingTransfers->where('status', 'Pending');
                    @endphp
                    @if ($myPendingOutgoing->count() > 0)
                        <div class="alert alert-info py-2 px-3 mb-3 rounded-3 shadow-xs">
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                                <div class="font-12">
                                    <i class="mdi mdi-clock-outline me-1"></i>
                                    Anda memiliki <strong>{{ $myPendingOutgoing->count() }} pengajuan transfer keluar</strong> yang sedang menunggu persetujuan teknisi penerima.
                                </div>
                                <button type="button" class="btn btn-xs btn-outline-info rounded-pill" data-bs-toggle="collapse" data-bs-target="#collapseOutgoingTransfers">
                                    Lihat Detail Pengajuan
                                </button>
                            </div>
                            <div class="collapse mt-2 pt-2 border-top" id="collapseOutgoingTransfers">
                                <div class="row g-2">
                                    @foreach ($myPendingOutgoing as $outg)
                                        @php
                                            $outAsset = $outg->fixedAsset;
                                            $outMaster = $outAsset->toolsMaster ?? null;
                                        @endphp
                                        <div class="col-md-6">
                                            <div class="p-2 border rounded bg-white d-flex justify-content-between align-items-center gap-2">
                                                <div class="font-11 min-w-0">
                                                    <strong class="text-dark d-block text-truncate">{{ $outMaster->nama_tools ?? $outAsset->code }}</strong>
                                                    <span class="text-muted">Tujuan: <strong>{{ $outg->toUser?->name }}</strong> ({{ \Carbon\Carbon::parse($outg->requested_at)->format('d/m/y H:i') }})</span>
                                                </div>
                                                <form action="{{ route('tool-audit.transfer.cancel', $outg->id) }}" method="post" onsubmit="return confirm('Batalkan pengajuan transfer alat ini?');">
                                                    @csrf
                                                    <button type="submit" class="btn btn-xs btn-outline-danger rounded-pill px-2">
                                                        <i class="mdi mdi-close"></i> Batalkan
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- TOOLS INVENTORY HEADER --}}
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h6 class="fw-bold text-dark mb-0">Daftar Peralatan / Tools yang Ditugaskan</h6>
                            <p class="text-muted small mb-0">Alat kerja aktif yang berada dalam tanggung jawab Anda. Anda dapat mentransfer kepemilikan alat ke rekan teknisi lain.</p>
                        </div>
                        <span class="badge bg-label-primary px-3 py-1 rounded-pill font-12">
                            Total: {{ $assignedTools->count() }} Tools
                        </span>
                    </div>

                    {{-- TOOLS GRID --}}
                    @if ($assignedTools->count() > 0)
                        <div class="row g-3" id="myToolsGrid">
                            @foreach ($assignedTools as $tool)
                                @php
                                    $master = $tool->toolsMaster;
                                    $fotoAwal = $tool->foto_awal ? asset($tool->foto_awal) : asset('assets/img/illustrations/tool-placeholder.png');
                                    $toolName = $master->nama_tools ?? ($tool->desc ?? 'Alat Kerja');
                                    $searchTool = strtolower($toolName . ' ' . $tool->code . ' ' . $tool->serial_number);
                                    
                                    // Check if this tool is currently in pending outgoing transfer
                                    $isPendingTransfer = $outgoingTransfers->where('id_fixed_asset', $tool->id)->where('status', 'Pending')->first();
                                @endphp
                                <div class="col-md-6 col-lg-4 tool-card-col" data-search="{{ $searchTool }}">
                                    <div class="card tool-grid-card shadow-xs p-3">
                                        <div class="d-flex gap-3 align-items-start flex-grow-1">
                                            <div class="position-relative">
                                                @if ($tool->foto_awal)
                                                    <a href="javascript:void(0);" onclick="previewPhoto('{{ $fotoAwal }}', '{{ addslashes($toolName) }}')">
                                                        <img src="{{ $fotoAwal }}" alt="Foto Tool" loading="lazy" decoding="async"
                                                            style="width: 74px; height: 74px; object-fit: cover; border-radius: 10px;" class="border shadow-xs">
                                                    </a>
                                                @else
                                                    <div class="bg-label-secondary border rounded-3 d-flex align-items-center justify-content-center" style="width: 74px; height: 74px;">
                                                        <i class="mdi mdi-tools fs-2 text-secondary"></i>
                                                    </div>
                                                @endif
                                                <span class="badge bg-primary position-absolute top-0 start-0 translate-middle-y font-10 px-1 py-0 rounded" style="margin-left: 10px;">
                                                    Qty: {{ $tool->qty }}
                                                </span>
                                            </div>
                                            <div class="flex-grow-1 min-w-0">
                                                <h6 class="fw-bold text-dark mb-1 text-truncate" title="{{ $toolName }}">
                                                    {{ $toolName }}
                                                </h6>
                                                <div class="small text-muted font-11 mb-1">
                                                    Kode: <span class="fw-semibold text-heading">{{ $tool->code ?? '-' }}</span>
                                                </div>
                                                @if ($tool->serial_number)
                                                    <div class="small text-muted font-11 mb-1">
                                                        S/N: <code>{{ $tool->serial_number }}</code>
                                                    </div>
                                                @endif
                                                <div class="d-flex align-items-center gap-1 mt-1 flex-wrap">
                                                    <span class="badge bg-label-info font-10 px-2 py-0">
                                                        <i class="mdi mdi-calendar-check me-1"></i>
                                                        {{ $tool->tanggal_serah_terima ? \Carbon\Carbon::parse($tool->tanggal_serah_terima)->format('d/m/Y') : 'Serah Terima' }}
                                                    </span>
                                                    @if ($tool->kondisi)
                                                        <span class="badge {{ $tool->kondisi === 'Ada' ? 'bg-label-success' : 'bg-label-warning' }} font-10 px-2 py-0">
                                                            {{ $tool->kondisi }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Transfer Action / Status Footer on Card --}}
                                        <div class="mt-3 pt-2 border-top">
                                            @if ($isPendingTransfer)
                                                <div class="d-flex align-items-center justify-content-between bg-label-warning p-2 rounded">
                                                    <div class="font-10 text-dark">
                                                        <i class="mdi mdi-clock-outline me-1"></i>
                                                        Transfer ke: <strong>{{ $isPendingTransfer->toUser?->name }}</strong>
                                                    </div>
                                                    <form action="{{ route('tool-audit.transfer.cancel', $isPendingTransfer->id) }}" method="post" onsubmit="return confirm('Batalkan pengajuan transfer alat ini?');">
                                                        @csrf
                                                        <button type="submit" class="btn btn-xs btn-danger rounded-pill px-2 py-0 font-10" title="Batalkan">
                                                            Batal
                                                        </button>
                                                    </form>
                                                </div>
                                            @else
                                                <button type="button" class="btn btn-sm btn-outline-primary w-100 rounded-pill font-11 py-1 d-flex align-items-center justify-content-center gap-1"
                                                    onclick="openTransferModal({{ $tool->id }}, '{{ addslashes($toolName) }}', '{{ $tool->code }}', '{{ $tool->serial_number }}', '{{ $tool->qty }}', '{{ $fotoAwal }}')">
                                                    <i class="mdi mdi-account-switch-outline fs-6"></i>
                                                    <span>Transfer Kepemilikan</span>
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5 text-muted">
                            <div class="avatar avatar-lg mx-auto mb-3 bg-label-secondary rounded-circle d-flex align-items-center justify-content-center" style="width: 54px; height: 54px;">
                                <i class="mdi mdi-wrench-outline fs-2 text-secondary"></i>
                            </div>
                            <h6 class="fw-bold mb-1 text-secondary">Belum Ada Tools yang Ditugaskan</h6>
                            <p class="small text-muted mb-0">Hubungi tim warehouse atau admin jika Anda sudah menerima serah terima tools.</p>
                        </div>
                    @endif

                    {{-- RIWAYAT TRANSFER KEPEMILIKAN COLLAPSIBLE --}}
                    @if ($transferHistories->count() > 0)
                        <div class="mt-4 pt-3 border-top">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="fw-bold text-dark mb-0 font-13">
                                    <i class="mdi mdi-history me-1 text-primary"></i>Riwayat Transfer Kepemilikan Terakhir
                                </h6>
                                <button class="btn btn-xs btn-outline-secondary rounded-pill" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTransferHistory">
                                    Buka / Tutup Riwayat ({{ $transferHistories->count() }})
                                </button>
                            </div>
                            <div class="collapse show" id="collapseTransferHistory">
                                <div class="table-responsive border rounded bg-white">
                                    <table class="table table-sm table-hover mb-0 align-middle font-12">
                                        <thead class="table-light">
                                            <tr>
                                                <th>No. Transfer</th>
                                                <th>Alat / Tools</th>
                                                <th>Dari (Pengirim)</th>
                                                <th>Ke (Penerima)</th>
                                                <th>Waktu Permintaan</th>
                                                <th>Status</th>
                                                <th>Catatan</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($transferHistories as $th)
                                                @php
                                                    $thAsset = $th->fixedAsset;
                                                    $thMaster = $thAsset?->toolsMaster;
                                                    $statusBadge = [
                                                        'Pending' => 'bg-label-warning',
                                                        'Approved' => 'bg-label-success',
                                                        'Rejected' => 'bg-label-danger',
                                                        'Cancelled' => 'bg-label-secondary',
                                                    ][$th->status] ?? 'bg-label-secondary';
                                                @endphp
                                                <tr>
                                                    <td><code>{{ $th->transfer_number }}</code></td>
                                                    <td>
                                                        <strong class="text-dark">{{ $thMaster->nama_tools ?? ($thAsset->desc ?? '-') }}</strong>
                                                        <div class="font-10 text-muted">{{ $thAsset?->code }}</div>
                                                    </td>
                                                    <td>
                                                        <span class="{{ $th->id_from_user == Auth::id() ? 'fw-bold text-primary' : 'text-dark' }}">
                                                            {{ $th->id_from_user == Auth::id() ? 'Saya (' . $th->fromUser?->name . ')' : $th->fromUser?->name }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <span class="{{ $th->id_to_user == Auth::id() ? 'fw-bold text-success' : 'text-dark' }}">
                                                            {{ $th->id_to_user == Auth::id() ? 'Saya (' . $th->toUser?->name . ')' : $th->toUser?->name }}
                                                        </span>
                                                    </td>
                                                    <td class="text-muted">{{ \Carbon\Carbon::parse($th->requested_at)->format('d/m/Y H:i') }}</td>
                                                    <td>
                                                        <span class="badge {{ $statusBadge }} rounded-pill font-10">
                                                            {{ $th->status === 'Approved' ? 'Diterima' : ($th->status === 'Rejected' ? 'Ditolak' : ($th->status === 'Cancelled' ? 'Dibatalkan' : 'Menunggu')) }}
                                                        </span>
                                                    </td>
                                                    <td class="small text-muted" style="max-width: 200px;">
                                                        @if ($th->catatan_pengirim)
                                                            <div><span class="fw-semibold">Kirim:</span> {{ $th->catatan_pengirim }}</div>
                                                        @endif
                                                        @if ($th->catatan_penerima)
                                                            <div><span class="fw-semibold">Respon:</span> {{ $th->catatan_penerima }}</div>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </div>

    {{-- MODAL PANDUAN & KETENTUAN AUDIT TOOLS --}}
    @include('components.modal-panduan-tool-audit')

    {{-- MODAL AJUKAN TRANSFER KEPEMILIKAN TOOLS --}}
    <div class="modal fade" id="transferToolModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('tool-audit.transfer.store') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id_fixed_asset" id="modalTransferAssetId">

                    <div class="modal-header border-bottom py-2 px-3 bg-label-primary">
                        <h6 class="modal-title fw-bold text-primary mb-0">
                            <i class="mdi mdi-account-switch-outline me-1"></i> Ajukan Transfer Kepemilikan Alat
                        </h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body p-3">
                        {{-- Tool Summary Box --}}
                        <div class="d-flex gap-3 align-items-center p-2 border rounded bg-light mb-3">
                            <img id="modalTransferToolImg" src="" alt="Foto Tool" class="rounded border shadow-xs" style="width: 60px; height: 60px; object-fit: cover;">
                            <div class="min-w-0">
                                <h6 class="fw-bold text-dark mb-0 font-14 text-truncate" id="modalTransferToolName">-</h6>
                                <div class="small text-muted font-11">
                                    Kode: <span id="modalTransferToolCode" class="fw-semibold text-heading">-</span> | S/N: <code id="modalTransferToolSn">-</code>
                                </div>
                                <span class="badge bg-label-primary font-10 px-2 py-0" id="modalTransferToolQty">Qty: -</span>
                            </div>
                        </div>

                        {{-- Destination Technician Dropdown --}}
                        <div class="mb-3">
                            <label class="form-label font-12 fw-bold text-dark">
                                Pilih Teknisi Penerima (Tujuan): <span class="text-danger">*</span>
                            </label>
                            <select class="form-select form-select-sm" name="id_to_user" required>
                                <option value="">-- Pilih Rekan Teknisi --</option>
                                @foreach ($otherTechnicians as $tech)
                                    <option value="{{ $tech->id }}">{{ $tech->name }} ({{ $tech->email }})</option>
                                @endforeach
                            </select>
                            <div class="form-text font-11 text-muted">
                                Alat akan berpindah kepemilikan setelah teknisi yang dituju menyetujui permintaan ini.
                            </div>
                        </div>

                        {{-- Catatan / Alasan --}}
                        <div class="mb-3">
                            <label class="form-label font-12 fw-bold text-dark">
                                Alasan / Catatan Serah Terima:
                            </label>
                            <textarea class="form-control form-control-sm" name="catatan_pengirim" rows="2" placeholder="Contoh: Pemindahan unit kerja, tukar shift, perbantuan proyek..."></textarea>
                        </div>

                        {{-- Foto Kondisi Fisik saat Serah Terima --}}
                        <div class="mb-2">
                            <label class="form-label font-12 fw-bold text-dark">
                                Foto Kondisi Fisik saat Transfer (Opsional):
                            </label>
                            <input type="file" class="form-control form-control-sm" name="foto_kondisi" accept="image/*" capture="environment">
                            <div class="form-text font-10 text-muted">Ambil/lampirkan foto kondisi terkini alat saat diserahkan.</div>
                        </div>
                    </div>

                    <div class="modal-footer border-top py-2 px-3">
                        <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-sm btn-primary px-3 shadow-sm">
                            <i class="mdi mdi-send me-1"></i> Kirim Permintaan Transfer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL TERIMA TRANSFER KEPEMILIKAN --}}
    <div class="modal fade" id="acceptTransferModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="formAcceptTransfer" action="" method="post">
                    @csrf
                    <div class="modal-header border-bottom py-2 px-3 bg-label-success">
                        <h6 class="modal-title fw-bold text-success mb-0">
                            <i class="mdi mdi-check-circle-outline me-1"></i> Konfirmasi Terima Transfer Alat
                        </h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body p-3">
                        <p class="small text-muted mb-2">
                            Anda akan menerima kepemilikan alat <strong class="text-dark" id="acceptModalToolName">-</strong> dari rekan teknisi <strong class="text-primary" id="acceptModalSenderName">-</strong>.
                        </p>
                        <div class="alert alert-info py-2 px-3 font-11 mb-3 rounded">
                            <i class="mdi mdi-information-outline me-1"></i>
                            Setelah disetujui, alat ini resmi terdaftar atas nama Anda dan wajib Anda laporkan pada setiap periode audit.
                        </div>

                        <div class="mb-2">
                            <label class="form-label font-12 fw-bold text-dark">Catatan Penerimaan (Opsional):</label>
                            <textarea class="form-control form-control-sm" name="catatan_penerima" rows="2" placeholder="Contoh: Alat diterima dalam kondisi lengkap dan baik..."></textarea>
                        </div>
                    </div>

                    <div class="modal-footer border-top py-2 px-3">
                        <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-sm btn-success px-4 shadow-sm">
                            <i class="mdi mdi-check me-1"></i> Ya, Terima Kepemilikan Alat
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL TOLAK TRANSFER KEPEMILIKAN --}}
    <div class="modal fade" id="rejectTransferModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="formRejectTransfer" action="" method="post">
                    @csrf
                    <div class="modal-header border-bottom py-2 px-3 bg-label-danger">
                        <h6 class="modal-title fw-bold text-danger mb-0">
                            <i class="mdi mdi-close-circle-outline me-1"></i> Tolak Permintaan Transfer Alat
                        </h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body p-3">
                        <p class="small text-muted mb-2">
                            Apakah Anda yakin ingin menolak pengajuan transfer alat <strong class="text-dark" id="rejectModalToolName">-</strong> dari <strong class="text-primary" id="rejectModalSenderName">-</strong>?
                        </p>
                        <div class="mb-2">
                            <label class="form-label font-12 fw-bold text-dark">Alasan Penolakan (Opsional):</label>
                            <textarea class="form-control form-control-sm" name="catatan_penerima" rows="2" placeholder="Contoh: Fisik alat belum saya terima, salah kirim, dll..."></textarea>
                        </div>
                    </div>

                    <div class="modal-footer border-top py-2 px-3">
                        <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-sm btn-danger px-3 shadow-sm">
                            <i class="mdi mdi-close me-1"></i> Tolak Transfer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Lightbox Photo Modal --}}
    <div class="modal fade" id="photoPreviewModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-bottom py-2 px-3">
                    <h6 class="modal-title fw-bold" id="photoPreviewTitle">Foto Tool</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center p-3 bg-light">
                    <img id="photoPreviewImg" src="" alt="Preview" class="img-fluid rounded border shadow-sm" style="max-height: 420px; object-fit: contain;">
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL NOTIFIKASI OTOMATIS: PERMINTAAN TRANSFER TOOLS MASUK --}}
    @if ($incomingTransfers->count() > 0)
        <div class="modal fade" id="autoIncomingTransferModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
                    <div class="modal-header py-3 px-4 bg-label-warning border-bottom">
                        <div class="d-flex align-items-center gap-2">
                            <div class="avatar avatar-sm bg-warning text-white rounded-circle d-flex align-items-center justify-content-center">
                                <i class="mdi mdi-bell-ring-outline fs-5"></i>
                            </div>
                            <div>
                                <h6 class="modal-title fw-bold text-dark mb-0">Pemberitahuan: Ada Transfer Tools Masuk!</h6>
                                <div class="font-11 text-muted">Terdapat <strong>{{ $incomingTransfers->count() }} unit alat kerja</strong> yang dialihkan ke akun Anda oleh rekan teknisi.</div>
                            </div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body p-3 p-md-4">
                        <div class="row g-3">
                            @foreach ($incomingTransfers as $inc)
                                @php
                                    $incAsset = $inc->fixedAsset;
                                    $incMaster = $incAsset?->toolsMaster;
                                    $incFoto = $inc->foto_kondisi ? asset($inc->foto_kondisi) : ($incAsset?->foto_awal ? asset($incAsset->foto_awal) : asset('assets/img/illustrations/tool-placeholder.png'));
                                    $incName = $incMaster?->nama_tools ?? ($incAsset?->desc ?? 'Alat Kerja');
                                @endphp
                                <div class="col-12 col-md-6">
                                    <div class="card border rounded-3 p-3 bg-white h-100 shadow-xs">
                                        <div class="d-flex gap-3 align-items-start mb-2">
                                            <img src="{{ $incFoto }}" alt="Foto Tool" class="rounded border shadow-xs" style="width: 68px; height: 68px; object-fit: cover;">
                                            <div class="min-w-0 flex-grow-1">
                                                <h6 class="fw-bold text-dark mb-1 font-13 text-truncate" title="{{ $incName }}">{{ $incName }}</h6>
                                                <div class="font-11 text-muted mb-1">
                                                    Kode: <code>{{ $incAsset?->code }}</code> | Qty: <strong>{{ $incAsset?->qty }}</strong>
                                                </div>
                                                <div class="font-11 text-dark">
                                                    Dari: <strong class="text-primary">{{ $inc->fromUser?->name }}</strong>
                                                    <span class="text-muted">({{ \Carbon\Carbon::parse($inc->requested_at)->diffForHumans() }})</span>
                                                </div>
                                            </div>
                                        </div>

                                        @if ($inc->catatan_pengirim)
                                            <div class="alert alert-secondary py-1 px-2 mb-2 font-11 rounded">
                                                <i class="mdi mdi-message-text-outline me-1"></i><em>"{{ $inc->catatan_pengirim }}"</em>
                                            </div>
                                        @endif

                                        <div class="d-flex gap-2 mt-auto pt-2 border-top">
                                            <button type="button" class="btn btn-sm btn-success flex-grow-1 rounded-pill py-1 font-12 shadow-xs"
                                                onclick="openAcceptFromAutoModal({{ $inc->id }}, '{{ addslashes($incName) }}', '{{ addslashes($inc->fromUser?->name ?? 'Teknisi') }}')">
                                                <i class="mdi mdi-check me-1"></i> Terima
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-danger flex-grow-1 rounded-pill py-1 font-12"
                                                onclick="openRejectFromAutoModal({{ $inc->id }}, '{{ addslashes($incName) }}', '{{ addslashes($inc->fromUser?->name ?? 'Teknisi') }}')">
                                                <i class="mdi mdi-close me-1"></i> Tolak
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="modal-footer border-top py-2 px-4 justify-content-between">
                        <span class="small text-muted font-11">
                            <i class="mdi mdi-information-outline me-1"></i> Kepemilikan alat resmi beralih ke Anda setelah disetujui.
                        </span>
                        <button type="button" class="btn btn-sm btn-primary rounded-pill px-3" data-bs-dismiss="modal" onclick="switchToToolsTab()">
                            <i class="mdi mdi-wrench-outline me-1"></i> Buka Tab Tools Saya
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Client-side Filter & Modal Script --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Live Search
            const searchInput = document.getElementById('liveSearchInput');
            const auditRows = document.querySelectorAll('.audit-item-row');
            const toolCols = document.querySelectorAll('.tool-card-col');

            function applySearch() {
                const q = (searchInput?.value || '').trim().toLowerCase();

                // Filter audit table
                auditRows.forEach(row => {
                    const searchData = row.getAttribute('data-search') || '';
                    const currentFilter = document.querySelector('.btn-audit-filter.active')?.getAttribute('data-filter') || 'all';
                    const status = row.getAttribute('data-status');

                    const matchesSearch = !q || searchData.includes(q);
                    const matchesFilter = (currentFilter === 'all' || status === currentFilter);

                    row.style.display = (matchesSearch && matchesFilter) ? '' : 'none';
                });

                // Filter tools grid
                toolCols.forEach(col => {
                    const searchData = col.getAttribute('data-search') || '';
                    const matchesSearch = !q || searchData.includes(q);
                    col.style.display = matchesSearch ? '' : 'none';
                });
            }

            if (searchInput) {
                searchInput.addEventListener('input', applySearch);
            }

            // Status Filter Buttons
            const filterBtns = document.querySelectorAll('.btn-audit-filter');
            filterBtns.forEach(btn => {
                btn.addEventListener('click', function () {
                    filterBtns.forEach(b => {
                        b.classList.remove('active', 'btn-primary', 'btn-secondary', 'btn-warning', 'btn-success', 'btn-danger');
                        b.classList.add('btn-outline-' + (b.getAttribute('data-filter') === 'all' ? 'primary' : (b.getAttribute('data-filter') === 'Draft' ? 'secondary' : (b.getAttribute('data-filter') === 'Submitted' ? 'warning' : (b.getAttribute('data-filter') === 'Verified' ? 'success' : 'danger')))));
                    });

                    this.classList.add('active');
                    applySearch();
                });
            });

            // Auto show incoming transfer modal on page load if any requests exist
            @if ($incomingTransfers->count() > 0)
                const autoModalEl = document.getElementById('autoIncomingTransferModal');
                if (autoModalEl) {
                    const autoModal = new bootstrap.Modal(autoModalEl);
                    autoModal.show();
                }
            @endif

            // Auto switch tab if URL has hash
            if (window.location.hash === '#tab-tools') {
                switchToToolsTab();
            }
        });

        function switchToToolsTab() {
            const tabBtn = document.getElementById('tab-tools-btn');
            if (tabBtn) {
                const bsTab = new bootstrap.Tab(tabBtn);
                bsTab.show();
            }
        }

        function openAcceptFromAutoModal(transferId, toolName, senderName) {
            const autoModalEl = document.getElementById('autoIncomingTransferModal');
            if (autoModalEl) {
                const autoModal = bootstrap.Modal.getInstance(autoModalEl);
                if (autoModal) autoModal.hide();
            }
            openAcceptTransferModal(transferId, toolName, senderName);
        }

        function openRejectFromAutoModal(transferId, toolName, senderName) {
            const autoModalEl = document.getElementById('autoIncomingTransferModal');
            if (autoModalEl) {
                const autoModal = bootstrap.Modal.getInstance(autoModalEl);
                if (autoModal) autoModal.hide();
            }
            openRejectTransferModal(transferId, toolName, senderName);
        }

        // Photo Lightbox
        function previewPhoto(url, title) {
            document.getElementById('photoPreviewImg').src = url;
            document.getElementById('photoPreviewTitle').innerText = title || 'Foto Tool';
            const modal = new bootstrap.Modal(document.getElementById('photoPreviewModal'));
            modal.show();
        }

        // Open Transfer Request Modal
        function openTransferModal(assetId, name, code, sn, qty, photoUrl) {
            document.getElementById('modalTransferAssetId').value = assetId;
            document.getElementById('modalTransferToolName').innerText = name || 'Alat Kerja';
            document.getElementById('modalTransferToolCode').innerText = code || '-';
            document.getElementById('modalTransferToolSn').innerText = sn || '-';
            document.getElementById('modalTransferToolQty').innerText = 'Qty: ' + (qty || 1);
            document.getElementById('modalTransferToolImg').src = photoUrl || '/assets/img/illustrations/tool-placeholder.png';

            const modal = new bootstrap.Modal(document.getElementById('transferToolModal'));
            modal.show();
        }

        // Open Accept Transfer Modal
        function openAcceptTransferModal(transferId, toolName, senderName) {
            document.getElementById('formAcceptTransfer').action = '/tool-audit/transfer/' + transferId + '/accept';
            document.getElementById('acceptModalToolName').innerText = toolName || 'Alat';
            document.getElementById('acceptModalSenderName').innerText = senderName || 'Teknisi';

            const modal = new bootstrap.Modal(document.getElementById('acceptTransferModal'));
            modal.show();
        }

        // Open Reject Transfer Modal
        function openRejectTransferModal(transferId, toolName, senderName) {
            document.getElementById('formRejectTransfer').action = '/tool-audit/transfer/' + transferId + '/reject';
            document.getElementById('rejectModalToolName').innerText = toolName || 'Alat';
            document.getElementById('rejectModalSenderName').innerText = senderName || 'Teknisi';

            const modal = new bootstrap.Modal(document.getElementById('rejectTransferModal'));
            modal.show();
        }
    </script>
@endsection
