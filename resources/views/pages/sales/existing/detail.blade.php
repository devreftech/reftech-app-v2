@extends('layouts.sales.app')
@section('title', 'Detail Existing - ' . $existing->company)
@section('content')

    {{-- Breadcrumbs & Top Action Bar --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-3 gap-2">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 text-muted small">
                <li class="breadcrumb-item">
                    <a href="{{ url('/') }}" class="text-muted"><i class="mdi mdi-home-outline me-1"></i>Dashboard</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('existing.index') }}" class="text-muted">CRM Existing</a>
                </li>
                <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">{{ $existing->company }}</li>
            </ol>
        </nav>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('existing.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="mdi mdi-arrow-left me-1"></i>Kembali
            </a>
            <button type="button" class="btn btn-sm btn-label-primary shadow-xs" data-bs-toggle="modal" data-bs-target="#updateExisting{{ $existing->id }}">
                <i class="mdi mdi-pencil-outline me-1"></i>Edit Data
            </button>
            <button type="button" class="btn btn-sm btn-label-danger delete-existing shadow-xs" data-id="{{ $existing->id }}">
                <i class="mdi mdi-trash-can-outline me-1"></i>Hapus
            </button>
        </div>
    </div>

    {{-- Hero Company Profile Header Card --}}
    <div class="card mb-4 border-0 shadow-sm overflow-hidden" style="border-radius: 16px;">
        {{-- Banner Gradient Background --}}
        <div style="background: linear-gradient(135deg, #4f46e5 0%, #6366f1 50%, #818cf8 100%); height: 100px; position: relative;">
            <div class="position-absolute" style="right: 20px; top: 10px; opacity: 0.15;">
                <i class="mdi mdi-domain" style="font-size: 90px; color: #fff; line-height: 1;"></i>
            </div>
        </div>

        <div class="card-body pt-0 pb-4 px-4 position-relative">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
                {{-- Left: Logo/Avatar & Company Title --}}
                <div class="d-flex align-items-start gap-3 flex-wrap" style="margin-top: -40px;">
                    <div class="avatar avatar-xl rounded-4 shadow-sm bg-white border border-4 border-white d-flex align-items-center justify-content-center text-primary fw-bold flex-shrink-0" style="width: 80px; height: 80px; font-size: 28px; background: linear-gradient(135deg, #eef2ff 0%, #e0e7ff 100%) !important;">
                        <span style="letter-spacing: -1px;">{{ strtoupper(substr($existing->company, 0, 2)) }}</span>
                    </div>
                    <div class="pt-3 pt-md-4 mt-1">
                        <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                            <h4 class="fw-bold mb-0 text-dark" style="color: #23272e !important; font-size: 1.35rem;">{{ $existing->company }}</h4>
                            <span id="customerStatusBadge" class="badge bg-label-{{ ($currentCrmStatus ?? '2') == '2' ? 'success' : (($currentCrmStatus ?? '2') == '3' ? 'warning' : 'danger') }} rounded-pill px-2.5">
                                <i class="mdi {{ ($currentCrmStatus ?? '2') == '2' ? 'mdi-check-circle-outline' : (($currentCrmStatus ?? '2') == '3' ? 'mdi-pause-circle-outline' : 'mdi-close-octagon-outline') }} me-1"></i>{{ ($currentCrmStatus ?? '2') == '2' ? 'Aktif' : (($currentCrmStatus ?? '2') == '3' ? 'Non-Aktif' : 'Bangkrupt') }}
                            </span>
                            @if ($existing->ru)
                                <span class="badge bg-label-{{ strtolower($existing->ru) == 'repeat' ? 'success' : 'info' }} rounded-pill px-2.5">
                                    {{ $existing->ru }}
                                </span>
                            @endif
                            @if ($existing->area)
                                <span class="badge bg-label-secondary rounded-pill px-2.5">
                                    <i class="mdi mdi-map-marker-outline me-1"></i>{{ $existing->area }}
                                </span>
                            @endif
                        </div>
                        <div class="d-flex align-items-center gap-3 text-muted small flex-wrap">
                            @if($existing->phone)
                                <span><i class="mdi mdi-phone-outline me-1 text-primary"></i><a href="tel:{{ $existing->phone }}" class="text-muted">{{ $existing->phone }}</a></span>
                            @endif
                            @if($existing->email)
                                <span><i class="mdi mdi-email-outline me-1 text-primary"></i><a href="mailto:{{ $existing->email }}" class="text-muted">{{ $existing->email }}</a></span>
                            @endif
                            @if($existing->sales)
                                <span class="badge bg-label-primary rounded-pill">
                                    <i class="mdi mdi-account-tie-outline me-1"></i>Sales: {{ $existing->sales->name }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Right: Status Switcher & Primary Action --}}
                <div class="d-flex align-items-center gap-3 flex-wrap ms-auto pt-2 pt-md-4">
                    {{-- Modern Capsule Segmented Status Switcher --}}
                    <div class="customer-status-segment-bar shadow-2xs" id="customerStatusButtonGroup">
                        <button type="button" class="btn-status-switch {{ ($currentCrmStatus ?? '2') == '2' ? 'active-status is-active' : '' }}" data-status="2" title="Customer Aktif">
                            <span class="status-indicator-dot dot-active"></span>
                            <span>Active</span>
                        </button>
                        <button type="button" class="btn-status-switch {{ ($currentCrmStatus ?? '2') == '3' ? 'active-status is-nonactive' : '' }}" data-status="3" title="Customer Non-Aktif">
                            <span class="status-indicator-dot dot-nonactive"></span>
                            <span>Non-Active</span>
                        </button>
                        <button type="button" class="btn-status-switch {{ ($currentCrmStatus ?? '2') == '1' ? 'active-status is-bangkrupt' : '' }}" data-status="1" title="Customer Bangkrupt / Pailit">
                            <span class="status-indicator-dot dot-bangkrupt"></span>
                            <span>Bangkrupt</span>
                        </button>
                    </div>

                    @if (Auth::user()->role == 'Sales')
                        @php
                            $emailPic = 0;
                            foreach ($charge as $pic) {
                                if ($pic->email_pic != null && $pic->email_pic != '-') {
                                    $emailPic++;
                                }
                            }
                        @endphp
                        <button type="button" class="btn btn-primary shadow-xs px-3 rounded-pill" data-bs-toggle="modal" data-bs-target="#createAction{{ $existing->id }}"
                            @if ($emailPic <= 0 || $existing->unit == null || $existing->unit == '-') disabled title="Pastikan PIC memiliki email & unit terdaftar" @endif>
                            <i class="mdi mdi-plus-circle-outline me-1"></i> New CRM Action
                        </button>
                    @endif
                </div>
            </div>

            {{-- Quick Summary Metric Cards (Bootstrap / Sneat Component Standard) --}}
            <div class="row g-3 mt-2 pt-3 border-top">
                {{-- 1. Total PIC --}}
                <div class="col-6 col-sm-4 col-xl">
                    <div class="d-flex align-items-center p-2.5 rounded-3 bg-white border shadow-2xs h-100">
                        <div class="avatar avatar-sm me-2.5 flex-shrink-0">
                            <span class="avatar-initial rounded bg-label-primary">
                                <i class="mdi mdi-account-group-outline fs-5"></i>
                            </span>
                        </div>
                        <div class="overflow-hidden">
                            <small class="text-muted d-block text-truncate fw-semibold" style="font-size: 0.72rem;">Total PIC</small>
                            <span class="fw-bold text-dark fs-6 text-truncate d-block">{{ $charge->count() }} Kontak</span>
                        </div>
                    </div>
                </div>

                {{-- 2. Total Unit / Mesin --}}
                <div class="col-6 col-sm-4 col-xl">
                    <div class="d-flex align-items-center p-2.5 rounded-3 bg-white border shadow-2xs h-100">
                        <div class="avatar avatar-sm me-2.5 flex-shrink-0">
                            <span class="avatar-initial rounded bg-label-info">
                                <i class="mdi mdi-cog-sync-outline fs-5"></i>
                            </span>
                        </div>
                        <div class="overflow-hidden">
                            <small class="text-muted d-block text-truncate fw-semibold" style="font-size: 0.72rem;">Total Mesin</small>
                            <span class="fw-bold text-dark fs-6 text-truncate d-block">{{ $machines->count() }} Unit</span>
                        </div>
                    </div>
                </div>

                {{-- 3. Plant Cabang (Hidden if 0) --}}
                @if ($plants->count() > 0)
                <div class="col-6 col-sm-4 col-xl">
                    <div class="d-flex align-items-center p-2.5 rounded-3 bg-white border shadow-2xs h-100">
                        <div class="avatar avatar-sm me-2.5 flex-shrink-0">
                            <span class="avatar-initial rounded bg-label-warning">
                                <i class="mdi mdi-office-building-marker-outline fs-5"></i>
                            </span>
                        </div>
                        <div class="overflow-hidden">
                            <small class="text-muted d-block text-truncate fw-semibold" style="font-size: 0.72rem;">Plant Cabang</small>
                            <span class="fw-bold text-dark fs-6 text-truncate d-block">{{ $plants->count() }} Lokasi</span>
                        </div>
                    </div>
                </div>
                @endif

                {{-- 4. Total PO --}}
                <div class="col-6 col-sm-6 col-xl">
                    <div class="d-flex align-items-center p-2.5 rounded-3 bg-white border shadow-2xs h-100">
                        <div class="avatar avatar-sm me-2.5 flex-shrink-0">
                            <span class="avatar-initial rounded bg-label-success">
                                <i class="mdi mdi-cart-check fs-5"></i>
                            </span>
                        </div>
                        <div class="overflow-hidden flex-grow-1">
                            <div class="d-flex align-items-center justify-content-between gap-1">
                                <small class="text-muted text-truncate fw-semibold" style="font-size: 0.72rem;">Total PO ({{ $yearsNow }})</small>
                                @if (($poGrowthDirection ?? 'neutral') == 'up')
                                    <span class="badge bg-label-success rounded-pill px-1.5 py-0 d-inline-flex align-items-center fw-bold" style="font-size: 0.65rem;" title="Naik {{ abs($poGrowthPercentage ?? 0) }}% dibanding tahun {{ $yearsNow - 1 }} (Rp {{ number_format($poPreviousYearTotal ?? 0, 0, ',', '.') }})">
                                        <i class="mdi mdi-arrow-top-right me-0.5"></i>+{{ $poGrowthPercentage }}%
                                    </span>
                                @elseif (($poGrowthDirection ?? 'neutral') == 'down')
                                    <span class="badge bg-label-danger rounded-pill px-1.5 py-0 d-inline-flex align-items-center fw-bold" style="font-size: 0.65rem;" title="Turun {{ abs($poGrowthPercentage ?? 0) }}% dibanding tahun {{ $yearsNow - 1 }} (Rp {{ number_format($poPreviousYearTotal ?? 0, 0, ',', '.') }})">
                                        <i class="mdi mdi-arrow-bottom-right me-0.5"></i>{{ $poGrowthPercentage }}%
                                    </span>
                                @else
                                    <span class="badge bg-label-secondary rounded-pill px-1.5 py-0 d-inline-flex align-items-center" style="font-size: 0.65rem;" title="0% dibanding tahun {{ $yearsNow - 1 }}">
                                        <i class="mdi mdi-minus me-0.5"></i>0%
                                    </span>
                                @endif
                            </div>
                            <span class="fw-bold text-success fs-6 text-truncate d-block">Rp {{ number_format($poCurrentYearTotal, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                {{-- 5. Quotation Aktif --}}
                <div class="col-6 col-sm-6 col-xl">
                    <div class="d-flex align-items-center p-2.5 rounded-3 bg-white border shadow-2xs h-100">
                        <div class="avatar avatar-sm me-2.5 flex-shrink-0">
                            <span class="avatar-initial rounded bg-label-primary">
                                <i class="mdi mdi-file-document-edit-outline fs-5"></i>
                            </span>
                        </div>
                        <div class="overflow-hidden">
                            <small class="text-muted d-block text-truncate fw-semibold" style="font-size: 0.72rem;">Quotation Aktif</small>
                            <span class="fw-bold text-primary fs-6 text-truncate d-block">{{ $activeQuoteCount ?? 0 }} Penawaran</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Tab Navigation & Content Container --}}
    <div class="card border-0 shadow-sm overflow-hidden" style="border-radius: 16px;">
        <div class="card-header border-bottom bg-white p-0 px-3">
            <ul class="nav nav-tabs nav-tabs-modern border-0" id="existing-detail-tab-nav" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-existing-detail" type="button" role="tab">
                        <i class="mdi mdi-domain me-1.5"></i>Informasi &amp; PIC
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-existing-tax" type="button" role="tab">
                        <i class="mdi mdi-file-certificate-outline me-1.5"></i>TAX
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-existing-crm" type="button" role="tab">
                        <i class="mdi mdi-phone-in-talk-outline me-1.5"></i>CRM Activity
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-existing-quotation" type="button" role="tab">
                        <i class="mdi mdi-file-document-outline me-1.5"></i>Quotations
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-existing-po" type="button" role="tab">
                        <i class="mdi mdi-cart-outline me-1.5"></i>Purchase Order
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-existing-service" type="button" role="tab">
                        <i class="mdi mdi-wrench-outline me-1.5"></i>Mesin &amp; Servis
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-existing-forecast" type="button" role="tab">
                        <i class="mdi mdi-chart-timeline-variant-shimmer me-1.5"></i>Forecast PM
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-existing-history" type="button" role="tab">
                        <i class="mdi mdi-history me-1.5"></i>History
                    </button>
                </li>
            </ul>
        </div>

        <div class="card-body p-4">
            <div class="tab-content p-0">

                {{-- ══════════════════════════════════════════════════════════════════ --}}
                {{-- ── TAB 1: INFORMASI PERUSAHAAN, PIC & PLANT ─────────────────────── --}}
                {{-- ══════════════════════════════════════════════════════════════════ --}}
                <div class="tab-pane fade show active" id="tab-existing-detail" role="tabpanel">
                    <div class="row g-4">
                        {{-- Profil Perusahaan Card --}}
                        <div class="col-lg-6">
                            <div class="card border border-light-subtle shadow-none h-100" style="border-radius: 12px; background: #fafafd;">
                                <div class="card-header bg-transparent border-bottom py-3 d-flex justify-content-between align-items-center">
                                    <h6 class="fw-bold mb-0 text-dark">
                                        <i class="mdi mdi-card-account-details-outline me-1.5 text-primary"></i> Data Perusahaan
                                    </h6>
                                    <div class="d-flex gap-2">
                                        <button type="button" class="btn btn-xs btn-label-secondary" data-bs-toggle="modal" data-bs-target="#createPlant" title="Tambah Lokasi Plant / Cabang">
                                            <i class="mdi mdi-factory me-1"></i> + Plant
                                        </button>
                                        <button type="button" class="btn btn-xs btn-label-primary" data-bs-toggle="modal" data-bs-target="#updateExisting{{ $existing->id }}">
                                            <i class="mdi mdi-pencil me-1"></i> Edit
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body py-3">
                                    <div class="table-responsive">
                                        <table class="table table-borderless table-sm mb-0">
                                            <tbody>
                                                <tr>
                                                    <td class="text-muted" style="width: 38%;"><i class="mdi mdi-account-tie me-1 text-secondary"></i> Sales Penanggung Jawab</td>
                                                    <td class="fw-bold text-primary">{{ $existing->sales?->name ?: '-' }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-muted"><i class="mdi mdi-cog-box me-1 text-secondary"></i> Unit Bisnis / Mesin Utama</td>
                                                    <td class="fw-semibold text-dark"><span class="badge bg-label-secondary">{{ $existing->unit ?: '-' }}</span></td>
                                                </tr>
                                                <tr>
                                                    <td class="text-muted"><i class="mdi mdi-repeat me-1 text-secondary"></i> Status R/U</td>
                                                    <td class="fw-semibold text-dark">{{ $existing->ru ?: '-' }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-muted"><i class="mdi mdi-source-branch me-1 text-secondary"></i> Lead Source</td>
                                                    <td class="fw-semibold text-dark">{{ $existing->source ?: '-' }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-muted"><i class="mdi mdi-phone-outline me-1 text-secondary"></i> No. Telepon Kantor</td>
                                                    <td class="fw-semibold text-dark">
                                                        @if($existing->phone)
                                                             <a href="tel:{{ $existing->phone }}" class="text-dark">{{ $existing->phone }}</a>
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-muted"><i class="mdi mdi-cellphone me-1 text-secondary"></i> Mobile Phone</td>
                                                    <td class="fw-semibold text-dark">{{ $existing->mobile ?: '-' }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-muted"><i class="mdi mdi-email-outline me-1 text-secondary"></i> Email Kantor</td>
                                                    <td class="fw-semibold text-dark">
                                                        @if($existing->email)
                                                            <a href="mailto:{{ $existing->email }}" class="text-primary">{{ $existing->email }}</a>
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-muted"><i class="mdi mdi-crosshairs-gps me-1 text-secondary"></i> Wilayah / Area</td>
                                                    <td class="fw-semibold text-dark">{{ $existing->area ?: '-' }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-muted align-top"><i class="mdi mdi-map-marker-outline me-1 text-secondary"></i> Alamat Pabrik / Kantor</td>
                                                    <td class="fw-semibold text-dark" style="line-height: 1.5;">{{ $existing->address ?: '-' }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- PIC (Person In Charge) Card --}}
                        <div class="col-lg-6">
                            <div class="card border border-light-subtle shadow-none h-100" style="border-radius: 12px; background: #ffffff;">
                                <div class="card-header bg-transparent border-bottom py-3 d-flex justify-content-between align-items-center">
                                    <h6 class="fw-bold mb-0 text-dark">
                                        <i class="mdi mdi-account-multiple-outline me-1.5 text-primary"></i> Person in Charge (PIC)
                                    </h6>
                                    <button type="button" class="btn btn-sm btn-primary shadow-xs" data-bs-toggle="modal" data-bs-target="#createPic">
                                        <i class="mdi mdi-plus me-1"></i> Tambah PIC
                                    </button>
                                </div>
                                <div class="card-body p-2 pt-3">
                                    <div class="card-datatable table-responsive pt-0">
                                        <table class="datatable-pic-client{{ Auth::user()->role == 'Sales' ? '-sales' : '' }} table table-hover table-bordered mb-0" style="width: 100%;">
                                            <thead class="table-light">
                                                <tr>
                                                    <th></th>
                                                    <th></th>
                                                    <th>ID</th>
                                                    <th>Nama PIC</th>
                                                    <th>Jabatan</th>
                                                    <th>Telepon</th>
                                                    <th>Email</th>
                                                    <th>Aksi</th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Plant / Cabang Pabrik (Hidden jika belum ada plant) --}}
                        @if ($plants->count() > 0)
                        <div class="col-12">
                            <div class="card border border-light-subtle shadow-none" style="border-radius: 12px;">
                                <div class="card-header bg-transparent border-bottom py-3 d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="fw-bold mb-0 text-dark">
                                            <i class="mdi mdi-factory me-1.5 text-primary"></i> Daftar Plant &amp; Lokasi Pabrik
                                        </h6>
                                        <small class="text-muted">Cabang pabrik / unit operasional pelanggan ({{ $plants->count() }} lokasi)</small>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-primary shadow-xs" data-bs-toggle="modal" data-bs-target="#createPlant">
                                        <i class="mdi mdi-plus me-1"></i> Tambah Plant
                                    </button>
                                </div>
                                <div class="card-body p-3">
                                    <div class="row g-3">
                                        @foreach ($plants as $plant)
                                            <div class="col-md-6 col-lg-4">
                                                <div class="p-3 border rounded-3 h-100 d-flex flex-column justify-content-between bg-white shadow-xs">
                                                    <div>
                                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                                            <div class="d-flex align-items-center gap-2">
                                                                <div class="avatar avatar-xs rounded bg-label-primary d-flex align-items-center justify-content-center">
                                                                    <i class="mdi mdi-office-building-marker"></i>
                                                                </div>
                                                                <h6 class="fw-bold mb-0 text-dark">{{ $plant->name }}</h6>
                                                            </div>
                                                        </div>
                                                        <p class="text-muted small mb-3"><i class="mdi mdi-map-marker-outline me-1"></i>{{ $plant->address ?: 'Alamat belum diatur' }}</p>
                                                    </div>
                                                    <div class="d-flex justify-content-end gap-2 border-top pt-2">
                                                        <button type="button" class="btn btn-xs btn-label-primary" data-bs-toggle="modal" data-bs-target="#updatePlant-{{ $plant->id }}">
                                                            <i class="mdi mdi-pencil me-1"></i>Edit
                                                        </button>
                                                        <button type="button" class="btn btn-xs btn-label-danger delete-plant" data-id="{{ $plant->id }}">
                                                            <i class="mdi mdi-trash-can-outline me-1"></i>Hapus
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- ══════════════════════════════════════════════════════════════════ --}}
                {{-- ── TAB 2: TAX (DATA NPWP & FAKTUR PAJAK) ────────────────────────── --}}
                {{-- ══════════════════════════════════════════════════════════════════ --}}
                <div class="tab-pane fade" id="tab-existing-tax" role="tabpanel">
                    <div class="row g-4">
                        <div class="col-12">
                            <div class="card border border-light-subtle shadow-none" style="border-radius: 14px; background: #ffffff;">
                                <div class="card-header bg-transparent border-bottom py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="avatar avatar-sm rounded bg-label-primary d-flex align-items-center justify-content-center">
                                            <i class="mdi mdi-file-certificate-outline fs-4"></i>
                                        </div>
                                        <div>
                                            <h6 class="fw-bold mb-0 text-dark">Data Perpajakan &amp; Faktur Pajak</h6>
                                            <small class="text-muted">Informasi administrasi Faktur Pajak dan Nomor Pokok Wajib Pajak Customer</small>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-primary shadow-xs rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#editNpwpDetails">
                                        <i class="mdi mdi-pencil-outline me-1"></i> Edit Data Pajak
                                    </button>
                                </div>
                                <div class="card-body p-4">
                                    <div class="row g-4">
                                        {{-- NPWP Card --}}
                                        <div class="col-md-5 col-lg-4">
                                            <div class="p-3 rounded-3 bg-light border border-light-subtle h-100 d-flex flex-column justify-content-between">
                                                <div>
                                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                                        <small class="text-muted font-size-11 fw-bold text-uppercase">Nomor NPWP (16 Digit)</small>
                                                        @if($existing->npwp)
                                                            <span class="badge bg-label-success rounded-pill px-2 py-0" style="font-size: 0.7rem;">Terdaftar</span>
                                                        @else
                                                            <span class="badge bg-label-secondary rounded-pill px-2 py-0" style="font-size: 0.7rem;">Belum Diisi</span>
                                                        @endif
                                                    </div>
                                                    <div class="d-flex align-items-center gap-2 my-2">
                                                        <i class="mdi mdi-numeric text-primary fs-3"></i>
                                                        <span class="fw-bold text-dark font-monospace fs-5">{{ $existing->npwp ?: 'Belum diatur' }}</span>
                                                    </div>
                                                </div>
                                                <div class="pt-2 border-top border-light-subtle mt-2">
                                                    <small class="text-muted" style="font-size: 0.75rem;">
                                                        <i class="mdi mdi-information-outline me-1"></i> Digunakan untuk penerbitan Faktur Pajak &amp; e-Faktur
                                                    </small>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Alamat Faktur Pajak Card --}}
                                        <div class="col-md-7 col-lg-8">
                                            <div class="p-3 rounded-3 bg-light border border-light-subtle h-100 d-flex flex-column justify-content-between">
                                                <div>
                                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                                        <small class="text-muted font-size-11 fw-bold text-uppercase">Alamat Terdaftar Faktur Pajak</small>
                                                        <i class="mdi mdi-map-marker-radius-outline text-secondary fs-5"></i>
                                                    </div>
                                                    <p class="text-dark fw-medium mb-0" style="font-size: 0.92rem; line-height: 1.6;">
                                                        {{ $existing->subAddress ?: 'Belum ada alamat faktur pajak terdaftar.' }}
                                                    </p>
                                                </div>
                                                <div class="pt-2 border-top border-light-subtle mt-3">
                                                    <small class="text-muted" style="font-size: 0.75rem;">
                                                        <i class="mdi mdi-office-building-marker-outline me-1"></i> Pastikan alamat sesuai dengan Surat Pengukuhan Pengusaha Kena Pajak (SPPKP)
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ══════════════════════════════════════════════════════════════════ --}}
                {{-- ── TAB 3: CRM ACTIVITY & TIMELINE ───────────────────────────────── --}}
                {{-- ══════════════════════════════════════════════════════════════════ --}}
                <div class="tab-pane fade" id="tab-existing-crm" role="tabpanel">
                    {{-- Weekly CRM Matrix --}}
                    <div class="card border border-light-subtle shadow-none mb-4" style="border-radius: 12px;">
                        <div class="card-header bg-transparent border-bottom py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <div>
                                <h6 class="fw-bold mb-0 text-dark">
                                    <i class="mdi mdi-calendar-month-outline me-1.5 text-primary"></i> Matriks Jadwal CRM Mingguan ({{ $yearsNow }})
                                </h6>
                                <small class="text-muted">Status aktivitas sales berkala per minggu pada semester berjalan</small>
                            </div>
                            @if (Auth::user()->role == 'Sales')
                                <button type="button" class="btn btn-sm btn-primary shadow-xs" data-bs-toggle="modal"
                                    data-bs-target="#createAction{{ $existing->id }}"
                                    @if ($emailPic <= 0 || $existing->unit == null || $existing->unit == '-') disabled @endif>
                                    <i class="mdi mdi-plus me-1"></i> Buat Aksi Baru
                                </button>
                            @endif
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive text-nowrap">
                                <table class="table table-bordered table-hover align-middle mb-0 text-center" style="font-size: 0.85rem;">
                                    <thead class="table-light">
                                        <tr>
                                            @php
                                                if ($monthNow <= 6) {
                                                    $bulan = array_keys($crmhis);
                                                    $mon1 = count($crmhis['January ' . $yearsNow]);
                                                    $mon2 = count($crmhis['February ' . $yearsNow]);
                                                    $mon3 = count($crmhis['March ' . $yearsNow]);
                                                    $mon4 = count($crmhis['April ' . $yearsNow]);
                                                    $mon5 = count($crmhis['May ' . $yearsNow]);
                                                    $mon6 = count($crmhis['June ' . $yearsNow]);
                                                } else {
                                                    $bulan = array_keys($crmhis);
                                                    $mon1 = count($crmhis['July ' . $yearsNow]);
                                                    $mon3 = count($crmhis['August ' . $yearsNow]);
                                                    $mon4 = count($crmhis['September ' . $yearsNow]);
                                                    $mon5 = count($crmhis['October ' . $yearsNow]);
                                                    $mon2 = count($crmhis['November ' . $yearsNow]);
                                                    $mon6 = count($crmhis['December ' . $yearsNow]);
                                                }
                                            @endphp
                                            @foreach ($bulan as $data => $data_bulan)
                                                <th class="fw-bold text-primary"
                                                    colspan="{{ $data_bulan == 'January ' . $yearsNow || $data_bulan == 'July ' . $yearsNow ? $mon1 : '' }}{{ $data_bulan == 'February ' . $yearsNow || $data_bulan == 'August ' . $yearsNow ? $mon2 : '' }}{{ $data_bulan == 'March ' . $yearsNow || $data_bulan == 'September ' . $yearsNow ? $mon3 : '' }}{{ $data_bulan == 'April ' . $yearsNow || $data_bulan == 'October ' . $yearsNow ? $mon4 : '' }}{{ $data_bulan == 'May ' . $yearsNow || $data_bulan == 'November ' . $yearsNow ? $mon5 : '' }}{{ $data_bulan == 'June ' . $yearsNow || $data_bulan == 'December ' . $yearsNow ? $mon6 : '' }}">
                                                    {{ $data_bulan }}
                                                </th>
                                            @endforeach
                                        </tr>
                                        @if ($monthNow <= 6)
                                            <tr class="bg-lighter">
                                                @php $weeks = 0; @endphp
                                                @foreach ($crmhis['January ' . $yearsNow] as $data) @php $weeks += 1; @endphp <th class="text-muted small">W{{ $weeks }}</th> @endforeach
                                                @php $weeks = 0; @endphp
                                                @foreach ($crmhis['February ' . $yearsNow] as $data) @php $weeks += 1; @endphp <th class="text-muted small">W{{ $weeks }}</th> @endforeach
                                                @php $weeks = 0; @endphp
                                                @foreach ($crmhis['March ' . $yearsNow] as $data) @php $weeks += 1; @endphp <th class="text-muted small">W{{ $weeks }}</th> @endforeach
                                                @php $weeks = 0; @endphp
                                                @foreach ($crmhis['April ' . $yearsNow] as $data) @php $weeks += 1; @endphp <th class="text-muted small">W{{ $weeks }}</th> @endforeach
                                                @php $weeks = 0; @endphp
                                                @foreach ($crmhis['May ' . $yearsNow] as $data) @php $weeks += 1; @endphp <th class="text-muted small">W{{ $weeks }}</th> @endforeach
                                                @php $weeks = 0; @endphp
                                                @foreach ($crmhis['June ' . $yearsNow] as $data) @php $weeks += 1; @endphp <th class="text-muted small">W{{ $weeks }}</th> @endforeach
                                            </tr>
                                        @else
                                            <tr class="bg-lighter">
                                                @php $weeks = 0; @endphp
                                                @foreach ($crmhis['July ' . $yearsNow] as $data) @php $weeks += 1; @endphp <th class="text-muted small">W{{ $weeks }}</th> @endforeach
                                                @php $weeks = 0; @endphp
                                                @foreach ($crmhis['August ' . $yearsNow] as $data) @php $weeks += 1; @endphp <th class="text-muted small">W{{ $weeks }}</th> @endforeach
                                                @php $weeks = 0; @endphp
                                                @foreach ($crmhis['September ' . $yearsNow] as $data) @php $weeks += 1; @endphp <th class="text-muted small">W{{ $weeks }}</th> @endforeach
                                                @php $weeks = 0; @endphp
                                                @foreach ($crmhis['October ' . $yearsNow] as $data) @php $weeks += 1; @endphp <th class="text-muted small">W{{ $weeks }}</th> @endforeach
                                                @php $weeks = 0; @endphp
                                                @foreach ($crmhis['November ' . $yearsNow] as $data) @php $weeks += 1; @endphp <th class="text-muted small">W{{ $weeks }}</th> @endforeach
                                                @php $weeks = 0; @endphp
                                                @foreach ($crmhis['December ' . $yearsNow] as $data) @php $weeks += 1; @endphp <th class="text-muted small">W{{ $weeks }}</th> @endforeach
                                            </tr>
                                        @endif
                                    </thead>
                                    <tbody>
                                        <tr>
                                            @foreach ($crmhis as $item)
                                                @foreach ($item as $minggu)
                                                    <td data-bs-toggle="tooltip" data-bs-placement="top"
                                                        data-bs-custom-class="tooltip-primary"
                                                        data-bs-original-title="{{ $minggu['note'][0] }}"
                                                        class="{{ !empty($minggu['data'][0]) ? 'fw-bold text-primary bg-label-primary' : '' }}">
                                                        {{ $minggu['data'][0] ?: '-' }}
                                                    </td>
                                                @endforeach
                                            @endforeach
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="row g-4">
                        {{-- Daily Call & Follow Up History Table --}}
                        <div class="col-lg-6">
                            <div class="card border border-light-subtle shadow-none h-100" style="border-radius: 12px; background: #ffffff;">
                                <div class="card-header bg-transparent border-bottom py-3 d-flex justify-content-between align-items-center">
                                    <h6 class="fw-bold mb-0 text-dark">
                                        <i class="mdi mdi-phone-in-talk-outline me-1.5 text-primary"></i> Log Daily Call &amp; Follow Up
                                    </h6>
                                    <span class="badge bg-label-info">{{ $callhis->where('name', '!=', 'Visit')->count() }} Log</span>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive text-nowrap">
                                        <table class="table table-hover align-middle mb-0" style="font-size: 0.85rem;">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Tanggal</th>
                                                    <th>Aksi</th>
                                                    <th>Status</th>
                                                    <th>Catatan</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($callhis->where('name', '!=', 'Visit') as $call)
                                                    <tr>
                                                        <td class="fw-semibold text-dark">{{ \Carbon\Carbon::parse($call->date)->format('d-m-Y') }}</td>
                                                        <td><span class="badge bg-label-primary">{{ $call->action ?: $call->name }}</span></td>
                                                        <td>
                                                            <span class="badge bg-label-{{ $call->status == 'Responded' ? 'success' : 'secondary' }} rounded-pill">
                                                                {{ $call->status }}
                                                            </span>
                                                        </td>
                                                        <td><small class="text-muted text-truncate d-inline-block" style="max-width: 180px;" title="{{ $call->note }}">{{ $call->note ?: '-' }}</small></td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="4" class="text-center py-4 text-muted">Belum ada log call.</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Visit History Table --}}
                        <div class="col-lg-6">
                            <div class="card border border-light-subtle shadow-none h-100" style="border-radius: 12px; background: #ffffff;">
                                <div class="card-header bg-transparent border-bottom py-3 d-flex justify-content-between align-items-center">
                                    <h6 class="fw-bold mb-0 text-dark">
                                        <i class="mdi mdi-map-marker-path me-1.5 text-primary"></i> Riwayat Visit Kunjungan
                                    </h6>
                                    <span class="badge bg-label-primary">{{ $visit->count() }} Kunjungan</span>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive text-nowrap">
                                        <table class="table table-hover align-middle mb-0" style="font-size: 0.85rem;">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Tanggal</th>
                                                    <th>Aksi</th>
                                                    <th>Status</th>
                                                    <th>Catatan</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($visit as $visits)
                                                    <tr>
                                                        <td class="fw-semibold text-dark">{{ \Carbon\Carbon::parse($visits->date)->format('d-m-Y') }}</td>
                                                        <td><span class="badge bg-label-danger">{{ $visits->action }}</span></td>
                                                        <td>
                                                            <span class="badge bg-label-{{ $visits->status == 'Responded' ? 'success' : 'secondary' }} rounded-pill">
                                                                {{ $visits->status }}
                                                            </span>
                                                        </td>
                                                        <td><small class="text-muted text-truncate d-inline-block" style="max-width: 180px;" title="{{ $visits->note }}">{{ $visits->note ?: '-' }}</small></td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="4" class="text-center py-4 text-muted">Belum ada riwayat Visit.</td>
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

                {{-- ══════════════════════════════════════════════════════════════════ --}}
                {{-- ── TAB 4: QUOTATION ─────────────────────────────────────────────── --}}
                {{-- ══════════════════════════════════════════════════════════════════ --}}
                <div class="tab-pane fade" id="tab-existing-quotation" role="tabpanel">
                    <div class="card border border-light-subtle shadow-none" style="border-radius: 14px; background: #ffffff;">
                        <div class="card-header bg-transparent border-bottom py-3">
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar avatar-sm rounded bg-label-primary d-flex align-items-center justify-content-center">
                                        <i class="mdi mdi-file-document-multiple-outline fs-4"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-0 text-dark">Daftar Penawaran Harga (Quotations)</h6>
                                        <small class="text-muted">Kelola seluruh status penawaran quotation dan unit penawaran pelanggan</small>
                                    </div>
                                </div>
                            </div>

                            {{-- Sub-Nav Pills Status Quotation --}}
                            <ul class="nav nav-pills nav-fill border-bottom pb-2 gap-2" id="quotation-subtabs-existing" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active fw-semibold py-2 d-flex align-items-center justify-content-center gap-1.5" id="subtab-quote-active-btn" data-bs-toggle="pill" data-bs-target="#subtab-quote-active" type="button" role="tab" aria-controls="subtab-quote-active" aria-selected="true">
                                        <i class="mdi mdi-file-document-edit-outline text-primary"></i>
                                        <span>Aktif / Sedang Berjalan</span>
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link fw-semibold py-2 d-flex align-items-center justify-content-center gap-1.5" id="subtab-quote-archive-btn" data-bs-toggle="pill" data-bs-target="#subtab-quote-archive" type="button" role="tab" aria-controls="subtab-quote-archive" aria-selected="false">
                                        <i class="mdi mdi-file-check-outline text-success"></i>
                                        <span>Selesai (Done PO)</span>
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link fw-semibold py-2 d-flex align-items-center justify-content-center gap-1.5" id="subtab-quote-loss-btn" data-bs-toggle="pill" data-bs-target="#subtab-quote-loss" type="button" role="tab" aria-controls="subtab-quote-loss" aria-selected="false">
                                        <i class="mdi mdi-file-cancel-outline text-danger"></i>
                                        <span>Loss / Batal</span>
                                    </button>
                                </li>
                            </ul>
                        </div>

                        <div class="card-body p-2 pt-3">
                            <div class="tab-content p-0">
                                {{-- Subtab 1: Active Quotations --}}
                                <div class="tab-pane fade show active" id="subtab-quote-active" role="tabpanel" aria-labelledby="subtab-quote-active-btn">
                                    <div class="card-datatable table-responsive pt-0">
                                        <table class="datatable-quotation-active table table-hover table-bordered mb-0" style="width: 100%;">
                                            <thead class="table-light">
                                                <tr>
                                                    <th></th>
                                                    <th></th>
                                                    <th>ID</th>
                                                    <th>Quote No.</th>
                                                    <th>Total Price</th>
                                                    <th>Deskripsi</th>
                                                    <th>Tanggal Quote</th>
                                                    <th>Status</th>
                                                    <th>Expired</th>
                                                    <th>Stats</th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>

                                {{-- Subtab 2: Done PO Quotations --}}
                                <div class="tab-pane fade" id="subtab-quote-archive" role="tabpanel" aria-labelledby="subtab-quote-archive-btn">
                                    <div class="card-datatable table-responsive pt-0">
                                        <table class="datatable-quotation-archive table table-hover table-bordered mb-0" style="width: 100%;">
                                            <thead class="table-light">
                                                <tr>
                                                    <th></th>
                                                    <th></th>
                                                    <th>ID</th>
                                                    <th>Quote No.</th>
                                                    <th>Total Price</th>
                                                    <th>Deskripsi</th>
                                                    <th>Tanggal Quote</th>
                                                    <th>Status</th>
                                                    <th>Expired</th>
                                                    <th>Stats</th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>

                                {{-- Subtab 3: Loss Quotations --}}
                                <div class="tab-pane fade" id="subtab-quote-loss" role="tabpanel" aria-labelledby="subtab-quote-loss-btn">
                                    <div class="card-datatable table-responsive pt-0">
                                        <table class="datatable-quotation-loss table table-hover table-bordered mb-0" style="width: 100%;">
                                            <thead class="table-light">
                                                <tr>
                                                    <th></th>
                                                    <th></th>
                                                    <th>ID</th>
                                                    <th>Quote No.</th>
                                                    <th>Total Price</th>
                                                    <th>Deskripsi</th>
                                                    <th>Tanggal Quote</th>
                                                    <th>Status</th>
                                                    <th>Expired</th>
                                                    <th>Stats</th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ══════════════════════════════════════════════════════════════════ --}}
                {{-- ── TAB 4: PURCHASE ORDER (KEY ACCOUNT SUMMARY) ──────────────────── --}}
                {{-- ══════════════════════════════════════════════════════════════════ --}}
                <div class="tab-pane fade" id="tab-existing-po" role="tabpanel">
                    {{-- Key Account Summary Header & Cards --}}
                    <div class="card border border-light-subtle shadow-none p-4 mb-4" style="background: #ffffff; border-radius: 14px;">
                        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                            <div class="d-flex align-items-center gap-3">
                                <div class="avatar avatar-md rounded-3 bg-label-primary d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                                    <i class="mdi mdi-chart-box-outline fs-3"></i>
                                </div>
                                <div>
                                    <h5 class="fw-bold mb-0 text-dark">Key Account Summary</h5>
                                    <small class="text-muted">Analisis performa transaksi &amp; akumulasi nilai Purchase Order</small>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <div class="form-floating form-floating-outline" style="min-width: 180px">
                                    <select id="poYearFilter" class="form-select border-primary shadow-xs">
                                        <option value="">Semua Tahun (All Time)</option>
                                        @foreach ($poYears as $year)
                                            <option value="{{ $year }}" @selected($year == $yearsNow)>Tahun {{ $year }}</option>
                                        @endforeach
                                    </select>
                                    <label for="poYearFilter" class="fw-semibold text-primary"><i class="mdi mdi-filter-variant me-1"></i>Filter Tahun</label>
                                </div>
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="card po-kpi-card border shadow-none h-100 p-3" style="border-top: 4px solid #696cff !important; border-radius: 10px; background: #fafafd;">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-md rounded-circle bg-label-primary me-3 d-flex align-items-center justify-content-center">
                                            <i class="mdi mdi-cash-multiple fs-3"></i>
                                        </div>
                                        <div class="overflow-hidden">
                                            <span class="text-uppercase fw-bold text-muted small" style="letter-spacing: 0.5px; font-size: 11px;">Total Revenue</span>
                                            <h4 class="fw-extrabold mb-0 text-primary text-truncate" id="poTotalRevenue">Rp 0</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card po-kpi-card border shadow-none h-100 p-3" style="border-top: 4px solid #28c76f !important; border-radius: 10px; background: #fafdfb;">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-md rounded-circle bg-label-success me-3 d-flex align-items-center justify-content-center">
                                            <i class="mdi mdi-file-document-check-outline fs-3"></i>
                                        </div>
                                        <div class="overflow-hidden">
                                            <span class="text-uppercase fw-bold text-muted small" style="letter-spacing: 0.5px; font-size: 11px;">Total Purchase Order</span>
                                            <h4 class="fw-extrabold mb-0 text-success text-truncate" id="poTotalCount">0</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card po-kpi-card border shadow-none h-100 p-3" style="border-top: 4px solid #00cfe8 !important; border-radius: 10px; background: #f7fcfd;">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-md rounded-circle bg-label-info me-3 d-flex align-items-center justify-content-center">
                                            <i class="mdi mdi-calculator-variant-outline fs-3"></i>
                                        </div>
                                        <div class="overflow-hidden">
                                            <span class="text-uppercase fw-bold text-muted small" style="letter-spacing: 0.5px; font-size: 11px;">Avg. Deal Size</span>
                                            <h4 class="fw-extrabold mb-0 text-info text-truncate" id="poAvgDeal">Rp 0</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Total Order Chart Section --}}
                    <div class="card border border-light-subtle shadow-none p-4 mb-4" style="background: #ffffff; border-radius: 14px;">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <div class="avatar avatar-xs rounded bg-label-primary p-1 d-flex align-items-center justify-content-center">
                                <i class="mdi mdi-chart-timeline-variant fs-5"></i>
                            </div>
                            <h6 class="fw-bold mb-0 text-dark">Grafik Tren Nilai Order (PO) Per Tahun</h6>
                        </div>
                        <div class="row g-4 align-items-center">
                            <div class="col-12 col-lg-4 col-md-5">
                                <div class="p-3.5 rounded-3 position-relative overflow-hidden" style="background: linear-gradient(135deg, rgba(105, 108, 255, 0.08) 0%, rgba(105, 108, 255, 0.02) 100%); border: 1px solid rgba(105, 108, 255, 0.15);">
                                    <span class="badge bg-label-primary px-3 py-1 rounded-pill mb-2 fw-bold" style="font-size: 11px;">
                                        <i class="mdi mdi-calendar-check me-1"></i>Tahun {{ $yearsNow }}
                                    </span>
                                    <div class="text-muted small mb-1 fw-medium">Total Nilai Order (PO)</div>
                                    <div class="fw-extrabold text-primary mb-2" style="font-size: 1.75rem; letter-spacing: -0.5px; line-height: 1.2;">
                                        Rp {{ number_format($poCurrentYearTotal, 0, ',', '.') }}
                                    </div>
                                    <p class="text-muted mb-0" style="font-size: 11.5px; line-height: 1.5;">
                                        Akumulasi dari seluruh penawaran yang sudah terbit PO (quotation &amp; smart quote) pada tahun berjalan.
                                    </p>
                                </div>
                            </div>
                            <div class="col-12 col-lg-8 col-md-7">
                                <div id="clientYearlyOrderChart" style="min-height: 230px;"></div>
                            </div>
                        </div>
                    </div>

                    {{-- Riwayat Purchase Order Table --}}
                    <div class="card border border-light-subtle shadow-none overflow-hidden" style="border-radius: 14px;">
                        <div class="card-header bg-transparent d-flex justify-content-between align-items-center py-3 px-4 border-bottom">
                            <div class="d-flex align-items-center gap-2">
                                <div class="avatar avatar-xs rounded bg-label-secondary p-1 d-flex align-items-center justify-content-center">
                                    <i class="mdi mdi-history fs-5 text-secondary"></i>
                                </div>
                                <h6 class="fw-bold mb-0 text-dark">Riwayat Transaksi Purchase Order</h6>
                            </div>
                            <span class="badge bg-label-primary rounded-pill px-3 py-1">
                                <i class="mdi mdi-text-box-search-outline me-1"></i>Log Transaksi PO
                            </span>
                        </div>
                        <div class="card-datatable table-responsive p-3">
                            <table class="datatable-po-history table table-hover border-top mb-0" id="dataTablePo" style="width:100%;">
                                <thead class="table-light">
                                    <tr>
                                        <th></th>
                                        <th></th>
                                        <th>ID</th>
                                        <th class="fw-bold text-uppercase" style="font-size: 11px; letter-spacing: 0.5px;">No Quote</th>
                                        <th class="fw-bold text-uppercase" style="font-size: 11px; letter-spacing: 0.5px;">Deskripsi</th>
                                        <th class="fw-bold text-uppercase" style="font-size: 11px; letter-spacing: 0.5px;">Tanggal PO</th>
                                        <th class="fw-bold text-uppercase" style="font-size: 11px; letter-spacing: 0.5px;">Status</th>
                                        <th class="fw-bold text-uppercase" style="font-size: 11px; letter-spacing: 0.5px;">Total Price</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- ══════════════════════════════════════════════════════════════════ --}}
                {{-- ── TAB 5: MESIN & SERVIS ─────────────────────────────────────────── --}}
                {{-- ══════════════════════════════════════════════════════════════════ --}}
                <div class="tab-pane fade" id="tab-existing-service" role="tabpanel">
                    {{-- Machine List Card --}}
                    <div class="card border border-light-subtle shadow-none mb-4" style="border-radius: 12px;">
                        <div class="card-header bg-transparent border-bottom py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <div>
                                <h6 class="fw-bold mb-0 text-dark">
                                    <i class="mdi mdi-cog-sync-outline me-1.5 text-primary"></i> Daftar Unit Mesin Pelanggan
                                </h6>
                                <small class="text-muted">Database unit kompresor &amp; mesin operasional yang terpasang di lokasi customer</small>
                            </div>
                            <button type="button" class="btn btn-sm btn-primary shadow-xs" data-bs-toggle="modal" data-bs-target="#createMachine">
                                <i class="mdi mdi-plus me-1"></i> Tambah Mesin Baru
                            </button>
                        </div>
                        <div class="card-body p-2 pt-3">
                            <div class="card-datatable table-responsive pt-0">
                                <table class="datatable-machine-client table table-hover table-bordered mb-0" style="width: 100%;">
                                    <thead class="table-light">
                                        <tr>
                                            <th></th>
                                            <th></th>
                                            <th>ID</th>
                                            <th>Kategori</th>
                                            <th>Brand</th>
                                            <th>Tipe</th>
                                            <th>Serial Number (S/N)</th>
                                            <th>Tag</th>
                                            <th>Lokasi Plant</th>
                                            <th>Laporan Servis</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- Service History Reports Card --}}
                    <div class="card border border-light-subtle shadow-none" style="border-radius: 12px;">
                        <div class="card-header bg-transparent border-bottom py-3">
                            <div class="mb-3">
                                <h6 class="fw-bold mb-1 text-dark">
                                    <i class="mdi mdi-clipboard-text-clock-outline me-1.5 text-primary"></i> Riwayat Laporan Servis &amp; Kunjungan Teknisi
                                </h6>
                                <p class="text-muted small mb-0">Pilih kategori riwayat laporan di bawah untuk melihat rincian servis teknisi.</p>
                            </div>

                            {{-- Sub-Nav Pills Navigation --}}
                            <ul class="nav nav-pills nav-fill border-bottom pb-2 gap-1" id="service-subtabs-existing" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active fw-semibold py-2" id="subtab-existing-service-btn" data-bs-toggle="pill" data-bs-target="#subtab-existing-service" type="button" role="tab" aria-controls="subtab-existing-service" aria-selected="true">
                                        <i class="mdi mdi-wrench-outline me-1"></i> Service Report
                                        <span class="badge bg-label-primary rounded-pill ms-1 d-none" id="service-history-count-badge">0</span>
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link fw-semibold py-2" id="subtab-existing-visit-btn" data-bs-toggle="pill" data-bs-target="#subtab-existing-visit" type="button" role="tab" aria-controls="subtab-existing-visit" aria-selected="false">
                                        <i class="mdi mdi-map-marker-path me-1"></i> Visit Report
                                        <span class="badge bg-label-primary rounded-pill ms-1 d-none" id="visit-history-count-badge">0</span>
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link fw-semibold py-2" id="subtab-existing-general-btn" data-bs-toggle="pill" data-bs-target="#subtab-existing-general" type="button" role="tab" aria-controls="subtab-existing-general" aria-selected="false">
                                        <i class="mdi mdi-clipboard-check-outline me-1"></i> General Check
                                        <span class="badge bg-label-primary rounded-pill ms-1 d-none" id="general-history-count-badge">0</span>
                                    </button>
                                </li>
                            </ul>
                        </div>

                        {{-- Sub-Tab Content Panes --}}
                        <div class="card-body p-2 pt-3">
                            <div class="tab-content p-0" id="service-subtabs-existing-content">
                                {{-- Sub-tab 1: Service History --}}
                                <div class="tab-pane fade show active" id="subtab-existing-service" role="tabpanel" aria-labelledby="subtab-existing-service-btn">
                                    <div class="card-datatable table-responsive pt-0">
                                        <table class="datatable-service-history table table-hover table-bordered w-100 mb-0" id="dataTableServiceHistory">
                                            <thead class="table-light">
                                                <tr>
                                                    <th></th>
                                                    <th></th>
                                                    <th>ID</th>
                                                    <th>No. Service</th>
                                                    <th>Unit Mesin</th>
                                                    <th>Teknisi</th>
                                                    <th>Tanggal</th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>

                                {{-- Sub-tab 2: Service Visit History --}}
                                <div class="tab-pane fade" id="subtab-existing-visit" role="tabpanel" aria-labelledby="subtab-existing-visit-btn">
                                    <div class="card-datatable table-responsive pt-0">
                                        <table class="datatable-visit-history table table-hover table-bordered w-100 mb-0" id="dataTableServiceVisitHistory">
                                            <thead class="table-light">
                                                <tr>
                                                    <th></th>
                                                    <th></th>
                                                    <th>ID</th>
                                                    <th>No. Service</th>
                                                    <th>Unit Mesin</th>
                                                    <th>Teknisi</th>
                                                    <th>Tanggal</th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>

                                {{-- Sub-tab 3: General Checkup History --}}
                                <div class="tab-pane fade" id="subtab-existing-general" role="tabpanel" aria-labelledby="subtab-existing-general-btn">
                                    <div class="card-datatable table-responsive pt-0">
                                        <table class="datatable-general-history table table-hover table-bordered w-100 mb-0" id="dataTableGeneralHistory">
                                            <thead class="table-light">
                                                <tr>
                                                    <th></th>
                                                    <th></th>
                                                    <th>ID</th>
                                                    <th>No. Service</th>
                                                    <th>Unit Mesin</th>
                                                    <th>Teknisi</th>
                                                    <th>Tanggal</th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ══════════════════════════════════════════════════════════════════ --}}
                {{-- ── TAB 6: FORECAST PM ────────────────────────────────────────────── --}}
                {{-- ══════════════════════════════════════════════════════════════════ --}}
                <div class="tab-pane fade" id="tab-existing-forecast" role="tabpanel">
                    <form action="{{ route('forecast.setup.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="redirect_back" value="1">
                        
                        <!-- Forecast Settings Section -->
                        <div class="card border border-light-subtle shadow-none mb-4" style="border-radius: 12px;">
                            <div class="card-header bg-transparent border-bottom py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                                <div>
                                    <h6 class="fw-bold mb-0 text-dark"><i class="mdi mdi-cog-outline me-1.5 text-primary"></i> Pengaturan Jadwal Forecast Unit</h6>
                                    <small class="text-muted">Atur jadwal rencana servis dan tipe PM untuk unit kompresor angin customer ini</small>
                                </div>
                                @if(in_array(Auth::user()->role, ['Admin', 'Sales Manager']))
                                <button type="submit" class="btn btn-sm btn-primary px-3 shadow-xs" style="border-radius: 8px; font-weight: 600;">
                                    <i class="mdi mdi-content-save-outline me-1"></i> Simpan Jadwal Forecast
                                </button>
                                @endif
                            </div>
                            
                            @php
                                $compressorMachines = $machines->filter(function($m) {
                                    return $m->unit && $m->unit->unit && strcasecmp($m->unit->unit->unit, 'AIR COMPRESSOR SCREW') === 0;
                                });
                                $isSales = !in_array(Auth::user()->role, ['Admin', 'Sales Manager']);
                            @endphp

                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover table-bordered align-middle mb-0" style="font-size: 0.85rem;">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Unit Kompresor</th>
                                                <th>Status &amp; Tipe</th>
                                                <th>Visit 1 (PM &amp; Tanggal)</th>
                                                <th>Visit 2 (PM &amp; Tanggal)</th>
                                                <th>Visit 3 (PM &amp; Tanggal)</th>
                                                <th>Visit 4 (PM &amp; Tanggal - Opsional)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($compressorMachines as $machine)
                                            <tr>
                                                <td>
                                                    <span class="fw-bold text-dark d-block" style="font-size: 0.9rem;">{{ $machine->unit->brand ?? '-' }} {{ $machine->unit->unit->model ?? '-' }}</span>
                                                    <small class="text-muted d-block">{{ $machine->desc }}</small>
                                                    <small class="text-secondary font-mono d-block">S/N: {{ $machine->serial }} | kW: {{ $machine->unit->unit->power ?? '-' }}</small>
                                                </td>
                                                <td>
                                                    <select class="form-select form-select-sm mb-1" name="machines[{{ $machine->id }}][is_forecasted]" style="width: 130px; border-radius: 6px;" {{ $isSales ? 'disabled' : '' }}>
                                                        <option value="1" {{ $machine->is_forecasted ? 'selected' : '' }}>Forecast Aktif</option>
                                                        <option value="0" {{ !$machine->is_forecasted ? 'selected' : '' }}>Non-Aktif</option>
                                                    </select>
                                                    <select class="form-select form-select-sm" name="machines[{{ $machine->id }}][forecast_type]" style="width: 130px; border-radius: 6px;" {{ $isSales ? 'disabled' : '' }}>
                                                        <option value="parts" {{ $machine->forecast_type == 'parts' ? 'selected' : '' }}>Parts Only</option>
                                                        <option value="regular_service" {{ $machine->forecast_type == 'regular_service' ? 'selected' : '' }}>Regular Service</option>
                                                        <option value="contract" {{ $machine->forecast_type == 'contract' ? 'selected' : '' }}>Service Contract</option>
                                                    </select>
                                                    <input type="hidden" name="machines[{{ $machine->id }}][last_service_date]" value="{{ $machine->last_service_date }}">
                                                    @if($isSales)
                                                        <input type="hidden" name="machines[{{ $machine->id }}][is_forecasted]" value="{{ $machine->is_forecasted ? '1' : '0' }}">
                                                        <input type="hidden" name="machines[{{ $machine->id }}][forecast_type]" value="{{ $machine->forecast_type }}">
                                                    @endif
                                                </td>
                                                <!-- Visit 1 -->
                                                <td>
                                                    <select class="form-select form-select-sm mb-1" name="machines[{{ $machine->id }}][visit_1_type]" style="width: 125px; border-radius: 6px;" {{ $isSales ? 'disabled' : '' }}>
                                                        <option value="" {{ is_null($machine->visit_1_type) ? 'selected' : '' }}>-- Jenis PM --</option>
                                                        <option value="PM1" {{ $machine->visit_1_type == 'PM1' ? 'selected' : '' }}>PM1 (Minor)</option>
                                                        <option value="PM2" {{ $machine->visit_1_type == 'PM2' ? 'selected' : '' }}>PM2 (Major)</option>
                                                    </select>
                                                    <input type="date" class="form-control form-control-sm" 
                                                           name="machines[{{ $machine->id }}][visit_1_date]" 
                                                           value="{{ $machine->visit_1_date ? \Carbon\Carbon::parse($machine->visit_1_date)->format('Y-m-d') : '' }}"
                                                           style="width: 125px; border-radius: 6px;"
                                                           {{ $isSales ? 'disabled' : '' }}>
                                                    @if($isSales)
                                                        <input type="hidden" name="machines[{{ $machine->id }}][visit_1_type]" value="{{ $machine->visit_1_type }}">
                                                        <input type="hidden" name="machines[{{ $machine->id }}][visit_1_date]" value="{{ $machine->visit_1_date }}">
                                                    @endif
                                                </td>
                                                <!-- Visit 2 -->
                                                <td>
                                                    <select class="form-select form-select-sm mb-1" name="machines[{{ $machine->id }}][visit_2_type]" style="width: 125px; border-radius: 6px;" {{ $isSales ? 'disabled' : '' }}>
                                                        <option value="" {{ is_null($machine->visit_2_type) ? 'selected' : '' }}>-- Jenis PM --</option>
                                                        <option value="PM1" {{ $machine->visit_2_type == 'PM1' ? 'selected' : '' }}>PM1 (Minor)</option>
                                                        <option value="PM2" {{ $machine->visit_2_type == 'PM2' ? 'selected' : '' }}>PM2 (Major)</option>
                                                    </select>
                                                    <input type="date" class="form-control form-control-sm" 
                                                           name="machines[{{ $machine->id }}][visit_2_date]" 
                                                           value="{{ $machine->visit_2_date ? \Carbon\Carbon::parse($machine->visit_2_date)->format('Y-m-d') : '' }}"
                                                           style="width: 125px; border-radius: 6px;"
                                                           {{ $isSales ? 'disabled' : '' }}>
                                                    @if($isSales)
                                                        <input type="hidden" name="machines[{{ $machine->id }}][visit_2_type]" value="{{ $machine->visit_2_type }}">
                                                        <input type="hidden" name="machines[{{ $machine->id }}][visit_2_date]" value="{{ $machine->visit_2_date }}">
                                                    @endif
                                                </td>
                                                <!-- Visit 3 -->
                                                <td>
                                                    <select class="form-select form-select-sm mb-1" name="machines[{{ $machine->id }}][visit_3_type]" style="width: 125px; border-radius: 6px;" {{ $isSales ? 'disabled' : '' }}>
                                                        <option value="" {{ is_null($machine->visit_3_type) ? 'selected' : '' }}>-- Jenis PM --</option>
                                                        <option value="PM1" {{ $machine->visit_3_type == 'PM1' ? 'selected' : '' }}>PM1 (Minor)</option>
                                                        <option value="PM2" {{ $machine->visit_3_type == 'PM2' ? 'selected' : '' }}>PM2 (Major)</option>
                                                    </select>
                                                    <input type="date" class="form-control form-control-sm" 
                                                           name="machines[{{ $machine->id }}][visit_3_date]" 
                                                           value="{{ $machine->visit_3_date ? \Carbon\Carbon::parse($machine->visit_3_date)->format('Y-m-d') : '' }}"
                                                           style="width: 125px; border-radius: 6px;"
                                                           {{ $isSales ? 'disabled' : '' }}>
                                                    @if($isSales)
                                                        <input type="hidden" name="machines[{{ $machine->id }}][visit_3_type]" value="{{ $machine->visit_3_type }}">
                                                        <input type="hidden" name="machines[{{ $machine->id }}][visit_3_date]" value="{{ $machine->visit_3_date }}">
                                                    @endif
                                                </td>
                                                <!-- Visit 4 -->
                                                <td>
                                                    <select class="form-select form-select-sm mb-1" name="machines[{{ $machine->id }}][visit_4_type]" style="width: 125px; border-radius: 6px;" {{ $isSales ? 'disabled' : '' }}>
                                                        <option value="" {{ is_null($machine->visit_4_type) ? 'selected' : '' }}>-- Jenis PM --</option>
                                                        <option value="PM1" {{ $machine->visit_4_type == 'PM1' ? 'selected' : '' }}>PM1 (Minor)</option>
                                                        <option value="PM2" {{ $machine->visit_4_type == 'PM2' ? 'selected' : '' }}>PM2 (Major)</option>
                                                    </select>
                                                    <input type="date" class="form-control form-control-sm" 
                                                           name="machines[{{ $machine->id }}][visit_4_date]" 
                                                           value="{{ $machine->visit_4_date ? \Carbon\Carbon::parse($machine->visit_4_date)->format('Y-m-d') : '' }}"
                                                           style="width: 125px; border-radius: 6px;"
                                                           {{ $isSales ? 'disabled' : '' }}>
                                                    @if($isSales)
                                                        <input type="hidden" name="machines[{{ $machine->id }}][visit_4_type]" value="{{ $machine->visit_4_type }}">
                                                        <input type="hidden" name="machines[{{ $machine->id }}][visit_4_date]" value="{{ $machine->visit_4_date }}">
                                                    @endif
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="6" class="text-center py-4 text-muted">Client ini belum memiliki unit kompresor terdaftar untuk di-forecast.</td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </form>
                    
                    <!-- Breakdown Rencana Forecast Client Section -->
                    <div class="card border border-light-subtle shadow-none mb-4" style="border-radius: 12px;">
                        <div class="card-header bg-transparent border-bottom py-3">
                            <h6 class="fw-bold mb-1 text-dark"><i class="mdi mdi-calculator-variant-outline me-1.5 text-primary"></i> Breakdown Rencana Forecast Nilai Servis</h6>
                            <small class="text-muted">Detail nominal estimasi part &amp; jasa berdasarkan jadwal yang tersimpan untuk tahun berjalan</small>
                        </div>
                        
                        @php
                            $clientDetails = [];
                            foreach($compressorMachines as $machine) {
                                if (!$machine->is_forecasted) continue;
                                
                                // Load rates
                                $power = $machine->unit->unit->power ?? null;
                                $servicePrices = null;
                                if ($power) {
                                    $normalizedPower = \App\Http\Controllers\ForecastController::normalizePower($power);
                                    $servicePrices = \App\Models\PowerServicePrice::where('power', $normalizedPower)->first();
                                }
                                
                                // Load PM template items (manually-curated per unit + level)
                                $spareparts = collect();
                                if ($machine->unit && $machine->unit->unit) {
                                    $spareparts = \App\Models\UnitPmTemplateItem::where('id_unit', $machine->unit->unit->id)
                                        ->where('type', 'part')
                                        ->with('equivalent')
                                        ->get();
                                }
                                
                                $visits = [
                                    ['num' => 1, 'date' => $machine->visit_1_date, 'type' => $machine->visit_1_type],
                                    ['num' => 2, 'date' => $machine->visit_2_date, 'type' => $machine->visit_2_type],
                                    ['num' => 3, 'date' => $machine->visit_3_date, 'type' => $machine->visit_3_type],
                                    ['num' => 4, 'date' => $machine->visit_4_date, 'type' => $machine->visit_4_type],
                                ];
                                
                                foreach($visits as $v) {
                                    if (empty($v['date']) || empty($v['type'])) continue;
                                    
                                    $pmLevel = $v['type'];
                                    
                                    // Calculate parts
                                    $partsTotal = 0;
                                    $includedParts = [];
                                    foreach($spareparts as $sp) {
                                        if ($sp->level != $pmLevel) continue;
                                        $sub = ($sp->qty * $sp->price);
                                        $partsTotal += $sub;
                                        $includedParts[] = [
                                            'pn' => $sp->equivalent->pn ?? '-',
                                            'brand' => $sp->equivalent->brand ?? '-',
                                            'description' => $sp->description ?: $sp->label,
                                            'qty' => $sp->qty,
                                            'price' => $sp->price,
                                            'subtotal' => $sub,
                                            'pm_level' => $sp->level
                                        ];
                                    }
                                    
                                    // Calculate service
                                    $serviceFee = 0;
                                    if ($machine->forecast_type == 'regular_service' && $servicePrices) {
                                        switch ($pmLevel) {
                                            case 'PM1': $serviceFee = $servicePrices->price_pm1; break;
                                            case 'PM2': $serviceFee = $servicePrices->price_pm2; break;
                                        }
                                    }
                                    
                                    $clientDetails[] = [
                                        'brand' => $machine->unit->brand ?? '-',
                                        'model' => $machine->unit->unit->model ?? '-',
                                        'serial' => $machine->serial,
                                        'visit' => 'Visit ' . $v['num'] . ' (' . $pmLevel . ')',
                                        'date' => \Carbon\Carbon::parse($v['date'])->format('d-m-Y'),
                                        'raw_date' => $v['date'],
                                        'forecast_type' => $machine->forecast_type,
                                        'parts_cost' => $partsTotal,
                                        'service_fee' => $serviceFee,
                                        'total' => $partsTotal + $serviceFee,
                                        'parts_detail' => $includedParts
                                    ];
                                }
                            }
                            
                            // Sort clientDetails chronologically
                            usort($clientDetails, function($a, $b) {
                                return strtotime($a['raw_date']) - strtotime($b['raw_date']);
                            });
                        @endphp
                        
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover table-striped align-middle mb-0" style="font-size: 0.85rem;">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Unit Kompresor</th>
                                            <th>Kunjungan</th>
                                            <th>Tanggal Rencana</th>
                                            <th>Tipe Forecast</th>
                                            <th class="text-end">Estimasi Part</th>
                                            <th class="text-end">Estimasi Jasa</th>
                                            <th class="text-end fw-bold">Total Forecast</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($clientDetails as $index => $detail)
                                        @php
                                            $badgeType = 'bg-label-primary';
                                            if($detail['forecast_type'] == 'parts') $badgeType = 'bg-label-warning';
                                            if($detail['forecast_type'] == 'contract') $badgeType = 'bg-label-success';
                                        @endphp
                                        <tr>
                                            <td>
                                                <strong>{{ $detail['brand'] }} {{ $detail['model'] }}</strong>
                                                <span class="text-muted d-block small">S/N: {{ $detail['serial'] }}</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-label-secondary font-mono">{{ $detail['visit'] }}</span>
                                            </td>
                                            <td>
                                                <span class="fw-semibold text-secondary">{{ $detail['date'] }}</span>
                                            </td>
                                            <td>
                                                <span class="badge {{ $badgeType }}" style="font-size: 0.7rem;">
                                                    {{ $detail['forecast_type'] == 'regular_service' ? 'Regular Service' : ($detail['forecast_type'] == 'parts' ? 'Parts Only' : 'Contract') }}
                                                </span>
                                            </td>
                                            <td class="text-end text-muted">
                                                @if($detail['parts_cost'] > 0)
                                                <a href="javascript:void(0)" class="text-decoration-underline text-primary fw-semibold" data-bs-toggle="modal" data-bs-target="#partsDetailModal-{{ $index }}">
                                                    Rp {{ number_format((float)($detail['parts_cost'] ?? 0), 0, ',', '.') }}
                                                </a>
                                                @else
                                                Rp 0
                                                @endif
                                            </td>
                                            <td class="text-end text-muted">Rp {{ number_format((float)($detail['service_fee'] ?? 0), 0, ',', '.') }}</td>
                                            <td class="text-end text-primary fw-bold">Rp {{ number_format((float)($detail['total'] ?? 0), 0, ',', '.') }}</td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="7" class="text-center py-4 text-muted">Belum ada kunjungan forecast yang terjadwal.</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Column for History Archives -->
                    <div class="card border border-light-subtle shadow-none" style="border-radius: 12px;">
                        <div class="card-header bg-transparent border-bottom py-3 d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="m-0 fw-bold text-dark"><i class="mdi mdi-archive-clock-outline me-1.5 text-primary"></i> Arsip Rencana Forecast Tahunan</h6>
                                <small class="text-muted">Histori log jadwal forecast dari tahun-tahun sebelumnya</small>
                            </div>
                            <span class="badge bg-label-info">History Log</span>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover table-striped align-middle mb-0" style="font-size: 0.85rem;">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Unit Kompresor</th>
                                            <th class="text-center">Tahun</th>
                                            <th>Tipe Forecast</th>
                                            <th>Rencana Kunjungan (PM &amp; Tanggal)</th>
                                            <th class="text-center">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $hasHistory = false; @endphp
                                        @foreach($compressorMachines as $mac)
                                            @if($mac->forecastHistories && $mac->forecastHistories->count() > 0)
                                                @foreach($mac->forecastHistories->sortByDesc('year') as $hist)
                                                    @php $hasHistory = true; @endphp
                                                    <tr>
                                                        <td>
                                                            <strong>{{ $mac->unit->brand ?? '-' }} {{ $mac->unit->unit->model ?? '-' }}</strong>
                                                            <span class="text-muted d-block small">S/N: {{ $mac->serial }}</span>
                                                        </td>
                                                        <td class="text-center">
                                                            <span class="badge bg-label-secondary fw-bold">{{ $hist->year }}</span>
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-label-primary" style="font-size: 0.7rem;">
                                                                {{ $hist->forecast_type == 'regular_service' ? 'Regular Service' : ($hist->forecast_type == 'parts' ? 'Parts Only' : 'Contract') }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <div class="d-flex flex-wrap gap-2">
                                                                @for($v = 1; $v <= 4; $v++)
                                                                    @php
                                                                        $vType = $hist->{"visit_{$v}_type"};
                                                                        $vDate = $hist->{"visit_{$v}_date"};
                                                                    @endphp
                                                                    @if(!empty($vDate))
                                                                        <span class="badge bg-light text-dark border p-1.5">
                                                                            <strong class="text-info">V{{ $v }} ({{ $vType }})</strong>: 
                                                                            {{ \Carbon\Carbon::parse($vDate)->format('d-m-Y') }}
                                                                        </span>
                                                                    @endif
                                                                @endfor
                                                            </div>
                                                        </td>
                                                        <td class="text-center">
                                                            @if($hist->is_forecasted)
                                                                <span class="badge bg-label-success rounded-pill">Aktif</span>
                                                            @else
                                                                <span class="badge bg-label-danger rounded-pill">Nonaktif</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                        @endforeach
                                        
                                        @if(!$hasHistory)
                                        <tr>
                                            <td colspan="5" class="text-center py-4 text-muted">Belum ada arsip riwayat forecast untuk customer ini.</td>
                                        </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ══════════════════════════════════════════════════════════════════ --}}
                {{-- ── TAB 8: CUSTOMER COMPLETE HISTORY & AUDIT TRAIL ───────────────── --}}
                {{-- ══════════════════════════════════════════════════════════════════ --}}
                <div class="tab-pane fade" id="tab-existing-history" role="tabpanel">
                    <div class="card border border-light-subtle shadow-none" style="border-radius: 14px; background: #ffffff;">
                        <div class="card-header bg-transparent border-bottom py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <div class="d-flex align-items-center gap-2">
                                <div class="avatar avatar-sm rounded-3 bg-label-primary d-flex align-items-center justify-content-center">
                                    <i class="mdi mdi-history fs-4"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-0 text-dark">Histori Lengkap &amp; Audit Trail Customer</h6>
                                    <small class="text-muted">Rekam jejak registrasi, perubahan profil/NPWP, PIC, Plant, CRM, penawaran, PO, dan servis</small>
                                </div>
                            </div>
                            <span class="badge bg-label-primary rounded-pill px-3 py-1 font-size-12 fw-bold">{{ $activityTimeline->count() }} Total Log Riwayat</span>
                        </div>

                        {{-- Quick Category Filter Pills --}}
                        <div class="px-4 py-2.5 border-bottom bg-lighter">
                            <div class="d-flex align-items-center gap-2 flex-wrap" id="timelineFilterButtons">
                                <span class="small text-muted fw-semibold me-1"><i class="mdi mdi-filter-variant me-1"></i>Filter Kategori:</span>
                                <button type="button" class="btn btn-xs btn-primary rounded-pill timeline-filter-btn active" data-filter="all">
                                    <i class="mdi mdi-apps me-1"></i> Semua ({{ $activityTimeline->count() }})
                                </button>
                                <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill timeline-filter-btn" data-filter="data,tax">
                                    <i class="mdi mdi-pencil-box-outline me-1"></i> Perubahan Data &amp; NPWP
                                </button>
                                <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill timeline-filter-btn" data-filter="crm,visit">
                                    <i class="mdi mdi-phone-in-talk-outline me-1"></i> CRM &amp; Visit
                                </button>
                                <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill timeline-filter-btn" data-filter="quotation,po">
                                    <i class="mdi mdi-file-document-check-outline me-1"></i> Penawaran &amp; PO
                                </button>
                                <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill timeline-filter-btn" data-filter="service">
                                    <i class="mdi mdi-wrench-outline me-1"></i> Servis Mesin
                                </button>
                            </div>
                        </div>

                        <div class="card-body p-4">
                            @if ($activityTimeline->count())
                                <ul class="timeline mb-0 ms-1" id="crmHistoryTimeline">
                                    @foreach ($activityTimeline as $index => $history)
                                        <li class="timeline-item timeline-item-transparent clearfix crm-history-item" data-type="{{ $history['type'] ?? 'data' }}" style="{{ $index >= 15 ? 'display: none;' : '' }}">
                                            <span class="timeline-point timeline-point-{{ $history['color'] }}"></span>
                                            <div class="timeline-event pb-3">
                                                <div class="timeline-header mb-1 d-flex justify-content-between align-items-start gap-2">
                                                    <div>
                                                        <h6 class="mb-0 fw-bold text-dark">
                                                            <i class="mdi {{ $history['icon'] ?? 'mdi-information-outline' }} text-{{ $history['color'] }} me-1"></i>
                                                            {{ $history['title'] }}
                                                            @if (!empty($history['no_quote']))
                                                                <a href="{{ $history['url'] }}" class="ms-1 font-monospace text-primary fw-bold text-decoration-underline">{{ $history['no_quote'] }}</a>
                                                            @endif
                                                        </h6>
                                                    </div>
                                                    <small class="text-muted text-nowrap" title="{{ $history['date']->format('d M Y H:i:s') }}">
                                                        <i class="mdi mdi-clock-outline me-0.5"></i>{{ $history['date']->diffInDays(\Carbon\Carbon::now()) > 7 ? $history['date']->format('d M Y') : $history['date']->diffForHumans() }}
                                                    </small>
                                                </div>

                                                <div class="d-flex align-items-center gap-1.5 flex-wrap mb-2">
                                                    <span class="badge bg-label-{{ $history['color'] }} rounded-pill font-size-11">{{ $history['category'] }}</span>
                                                    <span class="badge bg-light text-dark border font-size-11">{{ $history['status'] }}</span>
                                                    @if(!empty($history['user_name']))
                                                        <span class="badge bg-lighter text-muted border font-size-11">
                                                            <i class="mdi mdi-account-circle-outline me-0.5"></i>{{ $history['user_name'] }}
                                                        </span>
                                                    @endif
                                                </div>

                                                {{-- Diffs Table jika ada perubahan field spesifik (seperti NPWP, Alamat, dll) --}}
                                                @if (!empty($history['diffs']))
                                                    <div class="p-2.5 rounded-3 bg-light border border-light-subtle mb-2">
                                                        <small class="fw-bold text-muted d-block mb-1 font-size-11 text-uppercase">Rincian Perubahan:</small>
                                                        @foreach ($history['diffs'] as $diff)
                                                            <div class="d-flex align-items-center justify-content-between py-1 border-bottom border-light-subtle last-border-0 gap-2 small flex-wrap">
                                                                <span class="text-secondary fw-medium">{{ $diff['field'] }}:</span>
                                                                <div class="d-flex align-items-center gap-1.5">
                                                                    <span class="text-muted text-decoration-line-through small bg-white px-1.5 py-0.5 rounded border">{{ $diff['old'] }}</span>
                                                                    <i class="mdi mdi-arrow-right text-primary font-size-12"></i>
                                                                    <span class="text-dark fw-bold bg-white px-1.5 py-0.5 rounded border border-primary-subtle text-primary">{{ $diff['new'] }}</span>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @endif

                                                @if (!empty($history['note']))
                                                    <p class="mb-0 text-muted small bg-light p-2 rounded border border-light-subtle">
                                                        {{ $history['note'] }}
                                                    </p>
                                                @endif
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                                <div class="text-center mt-3" id="timelineLoadMoreWrapper" style="{{ $activityTimeline->count() <= 15 ? 'display: none;' : '' }}">
                                    <button type="button" class="btn btn-label-primary btn-sm px-4 shadow-xs" id="crmHistoryLoadMore">
                                        <i class="mdi mdi-chevron-down me-1"></i> Muat Lebih Banyak (Load More)
                                    </button>
                                </div>
                            @else
                                <div class="text-center py-5 text-muted">
                                    <i class="mdi mdi-history fs-1 d-block mb-1 opacity-50"></i>
                                    Belum ada log aktivitas dan histori tercatat.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- Modals for Parts Breakdown --}}
    @if(!empty($clientDetails))
        @foreach($clientDetails as $index => $detail)
            @if($detail['parts_cost'] > 0)
            <!-- Parts Detail Modal -->
            <div class="modal fade" id="partsDetailModal-{{ $index }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-light py-3">
                            <h5 class="modal-title fw-bold text-dark mb-0">
                                <i class="mdi mdi-package-variant-closed me-1 text-primary"></i> Detail Estimasi Part: {{ $detail['brand'] }} {{ $detail['model'] }}
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-0">
                            <div class="p-3 bg-lighter border-bottom">
                                <span class="badge bg-label-info me-1">{{ $detail['visit'] }}</span>
                                <span class="text-secondary small">S/N: {{ $detail['serial'] }} | Rencana Tanggal: {{ $detail['date'] }}</span>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover table-striped align-middle m-0" style="font-size: 0.85rem;">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Part Number (PN)</th>
                                            <th>Description</th>
                                            <th>Level</th>
                                            <th class="text-center">Qty</th>
                                            <th class="text-end">Harga Satuan</th>
                                            <th class="text-end fw-bold">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($detail['parts_detail'] as $part)
                                        <tr>
                                            <td>
                                                <span class="font-mono fw-bold text-dark">{{ $part['pn'] }}</span>
                                                <small class="text-muted d-block">{{ $part['brand'] }}</small>
                                            </td>
                                            <td>{{ $part['description'] }}</td>
                                            <td>
                                                <span class="badge bg-label-secondary" style="font-size: 0.7rem;">{{ $part['pm_level'] }}</span>
                                            </td>
                                            <td class="text-center fw-semibold">{{ $part['qty'] }}</td>
                                            <td class="text-end text-muted">Rp {{ number_format((float)($part['price'] ?? 0), 0, ',', '.') }}</td>
                                            <td class="text-end text-primary fw-bold">Rp {{ number_format((float)($part['subtotal'] ?? 0), 0, ',', '.') }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="table-light">
                                        <tr>
                                            <td colspan="5" class="text-end fw-bold text-dark">Total Estimasi Part:</td>
                                            <td class="text-end fw-bold text-primary" style="font-size: 0.95rem;">Rp {{ number_format((float)($detail['parts_cost'] ?? 0), 0, ',', '.') }}</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        <div class="modal-footer bg-light py-2">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        @endforeach
    @endif

    {{-- Project Modals Integration --}}
    @include('pages.sales.existing.form')
    @include('components.modal.pic.existing.form-create')
    @include('components.modal.machine.form')
    @include('components.modal.req-visit.form-create')
    @include('components.modal.plant.form-create')
    @include('pages.sales.activities.form-existing')
    @include('pages.sales.activities.form-visit')
    @foreach ($charge as $pic)
        @include('components.modal.pic.existing.form-update')
    @endforeach
    @foreach ($machines as $machine)
        @include('components.modal.machine.form-edit')
    @endforeach
    @foreach ($plants as $plant)
        @include('components.modal.plant.form-update')
    @endforeach

    <div class="modal fade" id="machineReportsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="machineReportsModalTitle">Service Report</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="machineReportsList">
                        <p class="text-center text-muted mb-0">Memuat...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Edit NPWP Modal --}}
    <form action="{{ route('customers.update', $existing->id) }}" method="post">
        @csrf
        @method('patch')
        <input type="hidden" name="company" value="{{ $existing->company }}">
        <input type="hidden" name="email" value="{{ $existing->email }}">
        <input type="hidden" name="phone" value="{{ $existing->phone }}">
        <input type="hidden" name="ru" value="{{ $existing->ru }}">
        <input type="hidden" name="unit" value="{{ $existing->unit }}">
        <input type="hidden" name="source" value="{{ $existing->source }}">
        <input type="hidden" name="mobile" value="{{ $existing->mobile }}">
        <input type="hidden" name="address" value="{{ $existing->address }}">
        <input type="hidden" name="area" value="{{ $existing->area }}">
        <input type="hidden" name="web" value="{{ $existing->web }}">
        @if (Auth::user()->id == 1 || Auth::user()->id == 16)
            <input type="hidden" name="info" value="{{ $existing->info }}">
        @endif

        <div class="modal fade" id="editNpwpDetails" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-md" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Edit NPWP &amp; Tax Details</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-2 mb-3">
                            <div class="col-12 mb-2">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" id="npwpInput" class="form-control npwp-number-only" name="npwp"
                                        placeholder="16 Digit No. NPWP" value="{{ old('npwp', $existing->npwp) }}" 
                                        inputmode="numeric" pattern="\d{16}" minlength="16" maxlength="16"
                                        title="No. NPWP harus persis 16 digit angka" required>
                                    <label for="npwpInput">No. NPWP (16 Digit)</label>
                                </div>
                            </div>
                            <div class="col-12 mb-2">
                                <div class="form-floating form-floating-outline">
                                    <textarea class="form-control h-px-100" name="subAddress" id="subAddressInput"
                                        placeholder="Alamat NPWP">{{ old('subAddress', $existing->subAddress) }}</textarea>
                                    <label for="subAddressInput">Alamat NPWP</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary waves-effect"
                            data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary waves-effect waves-light">Simpan Perubahan</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection()

@push('after-style')
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/animate-css/animate.css">
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-rowgroup-bs5/rowgroup.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/formvalidation/dist/css/formValidation.min.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/select2/select2.css" />
    <style>
        .po-kpi-card {
            transition: transform 0.25s ease, box-shadow 0.25s ease;
        }
        .po-kpi-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 24px rgba(105, 108, 255, 0.12) !important;
        }

        /* Modern Segmented Status Switcher */
        .customer-status-segment-bar {
            display: inline-flex;
            align-items: center;
            background: #f1f5f9;
            padding: 3px;
            border-radius: 50rem;
            border: 1px solid #e2e8f0;
            gap: 2px;
        }
        .customer-status-segment-bar .btn-status-switch {
            border: none;
            background: transparent;
            color: #64748b;
            font-size: 0.78rem;
            font-weight: 600;
            padding: 5px 12px;
            border-radius: 50rem;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            cursor: pointer;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            user-select: none;
            line-height: 1.2;
        }
        .customer-status-segment-bar .btn-status-switch:hover:not(.active-status) {
            color: #334155;
            background: rgba(255, 255, 255, 0.6);
        }
        .customer-status-segment-bar .btn-status-switch.active-status {
            background: #ffffff !important;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.08), 0 1px 2px rgba(0, 0, 0, 0.04);
            font-weight: 700;
        }
        .customer-status-segment-bar .btn-status-switch.is-active {
            color: #16a34a !important;
        }
        .customer-status-segment-bar .btn-status-switch.is-nonactive {
            color: #d97706 !important;
        }
        .customer-status-segment-bar .btn-status-switch.is-bangkrupt {
            color: #dc2626 !important;
        }
        .status-indicator-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            display: inline-block;
        }
        .dot-active {
            background-color: #22c55e;
            box-shadow: 0 0 0 2px rgba(34, 197, 94, 0.25);
        }
        .dot-nonactive {
            background-color: #f59e0b;
            box-shadow: 0 0 0 2px rgba(245, 158, 11, 0.25);
        }
        .dot-bangkrupt {
            background-color: #ef4444;
            box-shadow: 0 0 0 2px rgba(239, 68, 68, 0.25);
        }

        /* Modern Tabs Navigation */
        .nav-tabs-modern {
            display: flex;
            flex-wrap: wrap;
            gap: 4px;
            padding: 0 4px;
        }
        .nav-tabs-modern .nav-link {
            color: #64748b;
            font-weight: 600;
            font-size: 0.9rem;
            padding: 14px 18px;
            border: none;
            border-bottom: 3px solid transparent;
            background: transparent !important;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
        }
        .nav-tabs-modern .nav-link:hover {
            color: #4f46e5;
            border-bottom-color: #cbd5e1;
        }
        .nav-tabs-modern .nav-link.active {
            color: #4f46e5 !important;
            border-bottom-color: #4f46e5 !important;
            font-weight: 700;
        }
    </style>
@endpush

@push('after-script')
    <script src="{{ asset('assets') }}/vendor/libs/moment/moment.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/flatpickr/flatpickr.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/formvalidation/dist/js/FormValidation.min.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/select2/select2.js"></script>
@endpush

@push('page-script')
    <script src="{{ asset('assets') }}/vendor/libs/apex-charts/apexcharts.js"></script>
    <script src="{{ asset('assets') }}/js/tables-datatables-basic.js"></script>
    <script src="{{ asset('assets') }}/includes/table-quotation-client.js"></script>
    <script src="{{ asset('assets') }}/includes/table-po-history.js"></script>
    <script src="{{ asset('assets') }}/includes/table-machine-client.js"></script>
    <script src="{{ asset('assets') }}/includes/table-pic-client.js"></script>
    <script src="{{ asset('assets') }}/includes/table-pic-client-sales.js"></script>
    <script src="{{ asset('assets') }}/includes/table-service-history.js"></script>
    <script src="{{ asset('assets') }}/includes/table-general-history.js"></script>
    <script src="{{ asset('assets') }}/includes/table-visit-history.js"></script>
    <script src="{{ asset('assets') }}/js/extended-ui-sweetalert2.js"></script>
    <script src="{{ asset('assets') }}/js/forms-selects.js?v={{ filemtime(public_path('assets/js/forms-selects.js')) }}"></script>
    <script>
        (function () {
            const isDark = document.documentElement.classList.contains('dark-style');
            const labelColor = isDark ? '#a8aaae' : '#6d6b77';
            const borderColor = isDark ? '#404152' : '#dbdade';

            const formatRp = val => {
                if (val >= 1_000_000_000) return 'Rp ' + (val / 1_000_000_000).toFixed(1) + 'B';
                if (val >= 1_000_000) return 'Rp ' + (val / 1_000_000).toFixed(1) + 'M';
                return 'Rp ' + val.toLocaleString('id-ID');
            };

            const yearlyLabels = @json($poYearlyLabels);
            const yearlyTotals = @json($poYearlyTotals);

            const chartEl = document.querySelector('#clientYearlyOrderChart');
            if (chartEl) {
                new ApexCharts(chartEl, {
                    chart: { type: 'bar', height: 230, toolbar: { show: false } },
                    series: [{ name: 'Total Order', data: yearlyTotals }],
                    colors: ['#696cff'],
                    plotOptions: { bar: { borderRadius: 6, columnWidth: '38%' } },
                    fill: {
                        type: 'gradient',
                        gradient: {
                            shade: 'light',
                            type: 'vertical',
                            shadeIntensity: 0.25,
                            gradientToColors: ['#8c8eff'],
                            inverseColors: false,
                            opacityFrom: 0.95,
                            opacityTo: 0.85
                        }
                    },
                    dataLabels: { enabled: false },
                    xaxis: {
                        categories: yearlyLabels,
                        labels: { style: { colors: labelColor, fontSize: '12px', fontWeight: 600 } },
                        axisBorder: { show: false },
                        axisTicks: { show: false },
                    },
                    yaxis: { labels: { formatter: formatRp, style: { colors: labelColor, fontSize: '11px' } } },
                    grid: { borderColor, strokeDashArray: 4, padding: { top: -10, bottom: -5 } },
                    tooltip: { y: { formatter: formatRp } },
                }).render();
            }
        })();
    </script>
@endpush

@push('script')
    <script>
        // Re-adjust DataTables column widths when switching tabs, since tables
        // initialized inside a hidden tab-pane render with collapsed widths.
        $('#existing-detail-tab-nav button[data-bs-toggle="tab"]').on('shown.bs.tab', function() {
            $.fn.dataTable.tables({
                visible: true,
                api: true
            }).columns.adjust().responsive.recalc();
        });

        $(document).on('click', '#crmHistoryLoadMore', function() {
            $('#crmHistoryTimeline .crm-history-item.d-none').removeClass('d-none');
            $(this).parent().remove();
        });

        $(document).on('click', '.delete-pic', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, delete it!",
                customClass: {
                    confirmButton: "btn btn-primary me-3 waves-effect waves-light",
                    cancelButton: "btn btn-label-secondary waves-effect",
                },
                buttonsStyling: false,
            }).then(function(result) {
                if (result.value) {
                    $.ajax({
                        'url': '{{ url('pic') }}/' + id,
                        'type': 'POST',
                        'data': {
                            '_method': 'DELETE',
                            '_token': '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response == 1) {
                                Swal.fire({
                                    icon: "success",
                                    title: "Deleted!",
                                    text: "Your file has been deleted.",
                                    customClass: {
                                        confirmButton: "btn btn-success waves-effect",
                                    },
                                })
                                window.setTimeout(function() {
                                    location.reload();
                                }, 2000);
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: 'Data Failed to Delete!'
                                });
                            }
                        }
                    });
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    Swal.fire({
                        title: "Cancelled",
                        text: "Your imaginary file is safe :)",
                        icon: "error",
                        customClass: {
                            confirmButton: "btn btn-success waves-effect",
                        },
                    });
                }
            });
        });
        $(document).on('click', '.delete-machine', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, delete it!",
                customClass: {
                    confirmButton: "btn btn-primary me-3 waves-effect waves-light",
                    cancelButton: "btn btn-label-secondary waves-effect",
                },
                buttonsStyling: false,
            }).then(function(result) {
                if (result.value) {
                    $.ajax({
                        'url': '{{ url('machine') }}/' + id,
                        'type': 'POST',
                        'data': {
                            '_method': 'DELETE',
                            '_token': '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response == 1) {
                                Swal.fire({
                                    icon: "success",
                                    title: "Deleted!",
                                    text: "Your file has been deleted.",
                                    customClass: {
                                        confirmButton: "btn btn-success waves-effect",
                                    },
                                })
                                window.setTimeout(function() {
                                    location.reload();
                                }, 2000);
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: 'Data Failed to Delete!'
                                });
                            }
                        }
                    });
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    Swal.fire({
                        title: "Cancelled",
                        text: "Your imaginary file is safe :)",
                        icon: "error",
                        customClass: {
                            confirmButton: "btn btn-success waves-effect",
                        },
                    });
                }
            });
        });
        $(document).on('click', '.delete-plant', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, delete it!",
                customClass: {
                    confirmButton: "btn btn-primary me-3 waves-effect waves-light",
                    cancelButton: "btn btn-label-secondary waves-effect",
                },
                buttonsStyling: false,
            }).then(function(result) {
                if (result.value) {
                    $.ajax({
                        'url': '{{ url('plant') }}/' + id,
                        'type': 'POST',
                        'data': {
                            '_method': 'DELETE',
                            '_token': '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response == 1) {
                                Swal.fire({
                                    icon: "success",
                                    title: "Deleted!",
                                    text: "Your file has been deleted.",
                                    customClass: {
                                        confirmButton: "btn btn-success waves-effect",
                                    },
                                })
                                window.setTimeout(function() {
                                    location.reload();
                                }, 2000);
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: 'Data Failed to Delete!'
                                });
                            }
                        }
                    });
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    Swal.fire({
                        title: "Cancelled",
                        text: "Your imaginary file is safe :)",
                        icon: "error",
                        customClass: {
                            confirmButton: "btn btn-success waves-effect",
                        },
                    });
                }
            });
        });
        $(document).on('click', '.delete-existing', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, delete it!",
                customClass: {
                    confirmButton: "btn btn-primary me-3 waves-effect waves-light",
                    cancelButton: "btn btn-label-secondary waves-effect",
                },
                buttonsStyling: false,
            }).then(function(result) {
                if (result.value) {
                    $.ajax({
                        'url': '{{ url('existing') }}/' + id,
                        'type': 'POST',
                        'data': {
                            '_method': 'DELETE',
                            '_token': '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response == 1) {
                                Swal.fire({
                                    icon: "success",
                                    title: "Deleted!",
                                    text: "Your file has been deleted.",
                                    customClass: {
                                        confirmButton: "btn btn-success waves-effect",
                                    },
                                })
                                window.setTimeout(function() {
                                    window.location.href = '/existing';
                                }, 2000);
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: 'Data Failed to Delete!'
                                });
                            }
                        }
                    });
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    Swal.fire({
                        title: "Cancelled",
                        text: "Your imaginary file is safe :)",
                        icon: "error",
                        customClass: {
                            confirmButton: "btn btn-success waves-effect",
                        },
                    });
                }
            });
        });

        // Customer Status Switcher Handler (Active / Non-Active / Bangkrupt)
        $(document).on('click', '.btn-status-switch', function() {
            var $btn = $(this);
            var newStatus = $btn.data('status').toString();
            var clientId = "{{ $existing->id }}";
            var csrfToken = $('meta[name="csrf-token"]').attr('content') || '{{ csrf_token() }}';

            $btn.prop('disabled', true);

            $.ajax({
                url: '/existing/update-status/' + clientId,
                type: 'POST',
                data: {
                    status: newStatus,
                    _token: csrfToken
                },
                success: function(response) {
                    $btn.prop('disabled', false);

                    // Update Button Active States
                    $('.btn-status-switch').each(function() {
                        var btnSt = $(this).data('status').toString();
                        $(this).removeClass('active-status is-active is-nonactive is-bangkrupt');

                        if (btnSt === newStatus) {
                            $(this).addClass('active-status');
                            if (newStatus === '2') {
                                $(this).addClass('is-active');
                            } else if (newStatus === '3') {
                                $(this).addClass('is-nonactive');
                            } else if (newStatus === '1') {
                                $(this).addClass('is-bangkrupt');
                            }
                        }
                    });

                    // Update Header Badge
                    var badgeClass = newStatus === '2' ? 'bg-label-success' : (newStatus === '3' ? 'bg-label-warning' : 'bg-label-danger');
                    var iconClass = newStatus === '2' ? 'mdi-check-circle-outline' : (newStatus === '3' ? 'mdi-pause-circle-outline' : 'mdi-close-octagon-outline');
                    var labelText = newStatus === '2' ? 'Aktif' : (newStatus === '3' ? 'Non-Aktif' : 'Bangkrupt');

                    $('#customerStatusBadge')
                        .attr('class', 'badge ' + badgeClass + ' rounded-pill px-2.5')
                        .html('<i class="mdi ' + iconClass + ' me-1"></i>' + labelText);

                    // SweetAlert Full Center Modal with Backdrop
                    Swal.fire({
                        icon: 'success',
                        title: 'Status Customer Diperbarui',
                        html: '<div class="py-2"><p class="text-muted mb-1" style="font-size: 0.95rem;">Status customer berhasil diubah menjadi:</p><div class="badge fs-5 px-3 py-2 ' + badgeClass + ' rounded-pill mt-1"><i class="mdi ' + iconClass + ' me-1"></i> ' + labelText + '</div></div>',
                        showConfirmButton: true,
                        confirmButtonText: 'Tutup',
                        customClass: {
                            confirmButton: 'btn btn-primary rounded-pill px-4'
                        },
                        buttonsStyling: false,
                        timer: 2000,
                        timerProgressBar: true
                    });
                },
                error: function(err) {
                    $btn.prop('disabled', false);
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Mengubah Status',
                        text: 'Terjadi kesalahan saat memperbarui status customer.'
                    });
                }
            });
        });

        // Timeline Category Filter & Load More Handler
        var timelineLimit = 12;
        var currentTimelineFilter = 'all';

        function applyTimelineFilter() {
            var $items = $('.crm-history-item');
            var shownCount = 0;
            var totalMatching = 0;

            $items.each(function() {
                var itemType = $(this).data('type') ? $(this).data('type').toString() : '';
                var isMatch = false;

                if (currentTimelineFilter === 'all') {
                    isMatch = true;
                } else {
                    var allowedTypes = currentTimelineFilter.split(',');
                    isMatch = allowedTypes.includes(itemType);
                }

                if (isMatch) {
                    totalMatching++;
                    if (shownCount < timelineLimit) {
                        $(this).show();
                        shownCount++;
                    } else {
                        $(this).hide();
                    }
                } else {
                    $(this).hide();
                }
            });

            if (totalMatching > shownCount) {
                $('#timelineLoadMoreWrapper').show();
            } else {
                $('#timelineLoadMoreWrapper').hide();
            }
        }

        $(document).on('click', '.timeline-filter-btn', function() {
            $('.timeline-filter-btn').removeClass('btn-primary active').addClass('btn-outline-secondary');
            $(this).removeClass('btn-outline-secondary').addClass('btn-primary active');

            currentTimelineFilter = $(this).data('filter') ? $(this).data('filter').toString() : 'all';
            timelineLimit = 12;
            applyTimelineFilter();
        });

        $(document).on('click', '#crmHistoryLoadMore', function() {
            timelineLimit += 12;
            applyTimelineFilter();
        });

        // Re-adjust DataTables column widths when switching any tabs or subtabs
        $('button[data-bs-toggle="tab"], button[data-bs-toggle="pill"]').on('shown.bs.tab', function() {
            $.fn.dataTable.tables({
                visible: true,
                api: true
            }).columns.adjust().responsive.recalc();
        });
    </script>
@endpush
