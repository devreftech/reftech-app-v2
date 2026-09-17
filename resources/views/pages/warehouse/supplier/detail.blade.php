@extends('layouts.sales.app')
@section('title', 'Detail Supplier - ' . $supplier->supplier)

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    {{-- Breadcrumb Navigation --}}
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ url('/') }}"><i class="mdi mdi-home-outline me-1"></i>Dashboard</a></li>
            <li class="breadcrumb-item">Pengadaan & Logistik</li>
            <li class="breadcrumb-item"><a href="{{ route('supplier.index') }}">Mitra Supplier</a></li>
            <li class="breadcrumb-item active text-truncate" style="max-width: 280px;" aria-current="page">{{ $supplier->supplier }}</li>
        </ol>
    </nav>

    {{-- Hero Profile Banner & Header --}}
    <div class="card border-0 shadow-sm rounded-3 mb-4 overflow-hidden supplier-hero-card">
        <div class="supplier-hero-bg"></div>
        <div class="card-body p-4 pt-3 position-relative">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
                {{-- Supplier Identity --}}
                <div class="d-flex align-items-center gap-3 flex-grow-1">
                    @php
                        $cleanName = trim($supplier->supplier);
                        $words = preg_split('/\s+/', $cleanName);
                        $initials = '';
                        foreach (array_slice($words, 0, 2) as $w) {
                            $initials .= strtoupper(substr($w, 0, 1));
                        }
                        $initials = $initials ?: 'SP';

                        // Avatar color palette based on supplier id
                        $colors = ['primary', 'success', 'info', 'warning', 'danger'];
                        $accentColor = $colors[$supplier->id % count($colors)];
                    @endphp

                    <div class="avatar avatar-xl flex-shrink-0">
                        <span class="avatar-initial rounded-3 bg-label-{{ $accentColor }} shadow-sm fw-bold fs-3 border border-2 border-white">
                            {{ $initials }}
                        </span>
                    </div>

                    <div>
                        <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                            <h4 class="fw-bold text-dark mb-0 supplier-title">{{ $supplier->supplier }}</h4>
                            @if ($supplier->info == 'Import')
                                <span class="badge bg-label-info rounded-pill px-2.5 py-1">
                                    <i class="mdi mdi-airplane-takeoff me-1"></i>Pemasok Import
                                </span>
                            @else
                                <span class="badge bg-label-success rounded-pill px-2.5 py-1">
                                    <i class="mdi mdi-map-marker-radius-outline me-1"></i>Pemasok Lokal
                                </span>
                            @endif

                            @if (!empty($supplier->code) && $supplier->code !== '-')
                                <span class="badge bg-label-secondary rounded-pill px-2.5 py-1 font-monospace">
                                    <i class="mdi mdi-barcode-scan me-1"></i>{{ $supplier->code }}
                                </span>
                            @endif

                            @if (!empty($supplier->npwp))
                                <span class="badge bg-label-primary rounded-pill px-2.5 py-1">
                                    <i class="mdi mdi-check-decagram-outline me-1"></i>NPWP Terdaftar
                                </span>
                            @else
                                <span class="badge bg-label-warning rounded-pill px-2.5 py-1">
                                    <i class="mdi mdi-alert-circle-outline me-1"></i>Non-PKP
                                </span>
                            @endif
                        </div>

                        <div class="d-flex flex-wrap align-items-center text-muted small gap-3 mt-1">
                            <span>
                                <i class="mdi mdi-map-marker-outline text-primary me-1"></i>
                                {{ $supplier->area ?: ($supplier->address ?: 'Indonesia') }}
                            </span>
                            @if (!empty($supplier->phone))
                                <span>
                                    <i class="mdi mdi-phone-outline text-success me-1"></i>
                                    {{ $supplier->phone }}
                                </span>
                            @endif
                            @if (!empty($supplier->email))
                                <span>
                                    <i class="mdi mdi-email-outline text-info me-1"></i>
                                    {{ $supplier->email }}
                                </span>
                            @endif
                            <span>
                                <i class="mdi mdi-calendar-check-outline text-secondary me-1"></i>
                                Terdaftar: {{ $supplier->created_at ? $supplier->created_at->format('d M Y') : '-' }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="d-flex flex-wrap align-items-center gap-2 flex-shrink-0 mt-2 mt-md-0">
                    <a href="{{ route('supplier.index') }}" class="btn btn-outline-secondary waves-effect btn-sm">
                        <i class="mdi mdi-arrow-left me-1"></i> Daftar Supplier
                    </a>
                    <a href="{{ url('/payable/statement') }}" class="btn btn-outline-info waves-effect btn-sm">
                        <i class="mdi mdi-file-document-outline me-1"></i> Kartu Hutang
                    </a>
                    <button type="button" class="btn btn-primary waves-effect waves-light btn-sm" data-bs-toggle="modal" data-bs-target="#updateSupplier-{{ $supplier->id }}">
                        <i class="mdi mdi-pencil-outline me-1"></i> Edit Supplier
                    </button>
                    <button type="button" data-id="{{ $supplier->id }}" class="btn btn-outline-danger waves-effect btn-sm delete-supplier">
                        <i class="mdi mdi-delete-outline me-1"></i> Hapus
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Top KPI Metric Cards --}}
    <div class="row g-3 mb-4">
        {{-- Total Order Tahun Berjalan --}}
        {{-- Total Order PO --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card h-100 border-0 shadow-sm kpi-metric-card">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted fw-semibold mb-1 small text-uppercase tracking-wider">Total Nilai PO</p>
                            <h4 class="fw-bold mb-1 text-primary">Rp {{ number_format($poTotalAmount, 0, ',', '.') }}</h4>
                            <span class="badge bg-label-primary rounded-pill small">
                                <i class="mdi mdi-file-document-outline me-1"></i>{{ $poTotalCount }} Dokumen PO
                            </span>
                        </div>
                        <div class="avatar avatar-md bg-label-primary rounded-3 p-2">
                            <i class="mdi mdi-file-document-multiple-outline fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Total Transaksi Invoice --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card h-100 border-0 shadow-sm kpi-metric-card">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted fw-semibold mb-1 small text-uppercase tracking-wider">Faktur Masuk</p>
                            <h4 class="fw-bold mb-1 text-success">{{ number_format($supplier->productIn()->count(), 0, ',', '.') }}</h4>
                            <span class="badge bg-label-success rounded-pill small">
                                <i class="mdi mdi-receipt-text-check-outline me-1"></i>Total Transaksi
                            </span>
                        </div>
                        <div class="avatar avatar-md bg-label-success rounded-3 p-2">
                            <i class="mdi mdi-truck-delivery-outline fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Kontak PIC --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card h-100 border-0 shadow-sm kpi-metric-card">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted fw-semibold mb-1 small text-uppercase tracking-wider">Narahubung (PIC)</p>
                            <h4 class="fw-bold mb-1 text-info">{{ $pics->count() }} Orang</h4>
                            <span class="badge bg-label-info rounded-pill small">
                                <i class="mdi mdi-account-tie-outline me-1"></i>Kontak Person
                            </span>
                        </div>
                        <div class="avatar avatar-md bg-label-info rounded-3 p-2">
                            <i class="mdi mdi-card-account-phone-outline fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Klasifikasi & Wilayah --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card h-100 border-0 shadow-sm kpi-metric-card">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted fw-semibold mb-1 small text-uppercase tracking-wider">Kategori & Area</p>
                            <h5 class="fw-bold mb-1 text-warning text-truncate" style="max-width: 150px;">{{ $supplier->area ?: '-' }}</h5>
                            <span class="badge bg-label-warning rounded-pill small">
                                <i class="mdi mdi-tag-outline me-1"></i>{{ $supplier->info ?: 'Lokal' }}
                            </span>
                        </div>
                        <div class="avatar avatar-md bg-label-warning rounded-3 p-2">
                            <i class="mdi mdi-domain fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Grid: Company Details (65%) & PIC Contacts (35%) --}}
    <div class="row g-4 mb-4 supplier-company-pic-row">
        {{-- Company Details & Order Chart (Left 65%) --}}
        <div class="col-12 supplier-col-company">
            {{-- Company Profile Card --}}
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-transparent border-bottom py-3 d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <div class="avatar avatar-xs bg-label-primary rounded">
                            <i class="mdi mdi-office-building-outline"></i>
                        </div>
                        <h5 class="card-title mb-0 fw-bold text-dark font-16">Informasi & Profil Perusahaan</h5>
                    </div>
                    <button type="button" class="btn btn-sm btn-label-primary waves-effect" data-bs-toggle="modal" data-bs-target="#updateSupplier-{{ $supplier->id }}">
                        <i class="mdi mdi-pencil-outline me-1"></i>Edit Profil
                    </button>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        {{-- Address Tile --}}
                        <div class="col-12 col-md-6">
                            <div class="p-3 rounded-3 bg-light-soft border border-light-subtle h-100 info-tile">
                                <div class="d-flex align-items-center text-muted small fw-semibold mb-2">
                                    <i class="mdi mdi-map-marker-radius text-primary me-2 fs-5"></i>
                                    ALAMAT LENGKAP
                                </div>
                                <div class="fw-medium text-dark font-14">
                                    {{ $supplier->address ?: 'Belum diisi' }}
                                </div>
                            </div>
                        </div>

                        {{-- Area / City Tile --}}
                        <div class="col-12 col-md-6">
                            <div class="p-3 rounded-3 bg-light-soft border border-light-subtle h-100 info-tile">
                                <div class="d-flex align-items-center text-muted small fw-semibold mb-2">
                                    <i class="mdi mdi-city-variant-outline text-info me-2 fs-5"></i>
                                    WILAYAH / KOTA OPERASIONAL
                                </div>
                                <div class="fw-medium text-dark font-14">
                                    {{ $supplier->area ?: '-' }}
                                </div>
                            </div>
                        </div>

                        {{-- Phone Tile --}}
                        <div class="col-12 col-md-6">
                            <div class="p-3 rounded-3 bg-light-soft border border-light-subtle h-100 info-tile">
                                <div class="d-flex align-items-center text-muted small fw-semibold mb-2">
                                    <i class="mdi mdi-phone-outline text-success me-2 fs-5"></i>
                                    NOMOR TELEPON / KANTOR
                                </div>
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="fw-medium text-dark font-14">
                                        {{ $supplier->phone ?: '-' }}
                                    </div>
                                    @if (!empty($supplier->phone))
                                        @php
                                            $cleanPhone = preg_replace('/[^0-9]/', '', $supplier->phone);
                                            if (str_starts_with($cleanPhone, '0')) {
                                                $waPhone = '62' . substr($cleanPhone, 1);
                                            } else {
                                                $waPhone = $cleanPhone;
                                            }
                                        @endphp
                                        <div class="d-flex gap-1">
                                            <a href="tel:{{ $supplier->phone }}" class="btn btn-xs btn-label-secondary" title="Hubungi">
                                                <i class="mdi mdi-phone"></i>
                                            </a>
                                            <a href="https://wa.me/{{ $waPhone }}" target="_blank" class="btn btn-xs btn-label-success" title="Chat WhatsApp">
                                                <i class="mdi mdi-whatsapp"></i>
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Email Tile --}}
                        <div class="col-12 col-md-6">
                            <div class="p-3 rounded-3 bg-light-soft border border-light-subtle h-100 info-tile">
                                <div class="d-flex align-items-center text-muted small fw-semibold mb-2">
                                    <i class="mdi mdi-email-outline text-danger me-2 fs-5"></i>
                                    EMAIL PERUSAHAAN
                                </div>
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="fw-medium text-dark font-14 text-truncate" style="max-width: 220px;">
                                        {{ $supplier->email ?: '-' }}
                                    </div>
                                    @if (!empty($supplier->email))
                                        <a href="mailto:{{ $supplier->email }}" class="btn btn-xs btn-label-danger" title="Kirim Email">
                                            <i class="mdi mdi-email-fast-outline"></i>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- NPWP Tile --}}
                        <div class="col-12 col-md-6">
                            <div class="p-3 rounded-3 bg-light-soft border border-light-subtle h-100 info-tile">
                                <div class="d-flex align-items-center text-muted small fw-semibold mb-2">
                                    <i class="mdi mdi-file-certificate-outline text-warning me-2 fs-5"></i>
                                    NOMOR POKOK WAJIB PAJAK (NPWP)
                                </div>
                                <div class="fw-semibold text-dark font-monospace font-14">
                                    {{ $supplier->npwp ?: 'Belum terdaftar (Non-PKP)' }}
                                </div>
                            </div>
                        </div>

                        {{-- Info / Notes Tile --}}
                        <div class="col-12 col-md-6">
                            <div class="p-3 rounded-3 bg-light-soft border border-light-subtle h-100 info-tile">
                                <div class="d-flex align-items-center text-muted small fw-semibold mb-2">
                                    <i class="mdi mdi-information-outline text-secondary me-2 fs-5"></i>
                                    CATATAN / INFORMASI KHUSUS
                                </div>
                                <div class="fw-medium text-dark font-14">
                                    {{ $supplier->info ?: 'Tidak ada catatan tambahan' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Supplier Addresses Card --}}
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-transparent border-bottom py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <div class="avatar avatar-xs bg-label-primary rounded">
                            <i class="mdi mdi-map-marker-multiple-outline"></i>
                        </div>
                        <div>
                            <h5 class="card-title mb-0 fw-bold text-dark font-16">Daftar Alamat Supplier</h5>
                            <small class="text-muted font-12">Alamat operasional, kantor pusat, dan gudang untuk pemilihan PO</small>
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary btn-sm waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#createSupplierAddress">
                        <i class="mdi mdi-plus me-1"></i> Tambah Alamat
                    </button>
                </div>
                <div class="card-body p-3">
                    <div class="row g-3">
                        @forelse ($addresses as $addr)
                            <div class="col-12 col-md-6">
                                <div class="p-3 rounded-3 border {{ $addr->is_primary ? 'border-primary bg-label-primary-subtle' : 'border-light-subtle bg-white' }} h-100 position-relative transition-all hover-shadow-sm d-flex flex-column justify-content-between">
                                    <div>
                                        <div class="d-flex justify-content-between align-items-start mb-2 gap-2">
                                            <div class="d-flex align-items-center gap-1.5 flex-wrap">
                                                <span class="fw-bold text-dark font-14">{{ $addr->name ?: 'Alamat Operasional' }}</span>
                                                @if ($addr->is_primary)
                                                    <span class="badge bg-primary rounded-pill font-11">
                                                        <i class="mdi mdi-star me-0.5"></i>Utama
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="d-flex gap-1 flex-shrink-0">
                                                @if (!$addr->is_primary)
                                                    <button type="button" class="btn btn-icon btn-xs btn-label-secondary set-primary-supplier-address" data-id="{{ $addr->id }}" title="Jadikan Alamat Utama">
                                                        <i class="mdi mdi-star-outline font-14"></i>
                                                    </button>
                                                @endif
                                                <button type="button" class="btn btn-icon btn-xs btn-label-primary" data-bs-toggle="modal" data-bs-target="#updateSupplierAddress-{{ $addr->id }}" title="Edit Alamat">
                                                    <i class="mdi mdi-pencil-outline font-14"></i>
                                                </button>
                                                <button type="button" class="btn btn-icon btn-xs btn-label-danger delete-supplier-address" data-id="{{ $addr->id }}" title="Hapus Alamat">
                                                    <i class="mdi mdi-delete-outline font-14"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="text-muted font-13" style="line-height: 1.5;">
                                            <i class="mdi mdi-map-marker-outline text-primary me-1"></i>{{ $addr->address }}
                                        </div>
                                    </div>
                                    @if (!$addr->is_primary)
                                        <div class="pt-2 mt-2 border-top border-light-subtle text-end">
                                            <button type="button" class="btn btn-xs btn-outline-primary set-primary-supplier-address" data-id="{{ $addr->id }}">
                                                <i class="mdi mdi-check-circle-outline me-1"></i>Jadikan Utama
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            @push('modals')
                                @include('components.modal.warehouse.supplier.address-update', ['addr' => $addr])
                            @endpush
                        @empty
                            <div class="col-12 text-center py-4">
                                <div class="avatar avatar-md bg-label-secondary rounded-circle mx-auto mb-2">
                                    <i class="mdi mdi-map-marker-off-outline fs-4"></i>
                                </div>
                                <h6 class="fw-bold text-dark mb-1 font-15">Belum Ada Alamat Tersimpan</h6>
                                <p class="text-muted small mb-3">Tambahkan alamat operasional atau cabang untuk memudahkan pemilihan saat membuat Purchase Order (PO).</p>
                                <button type="button" class="btn btn-sm btn-primary waves-effect" data-bs-toggle="modal" data-bs-target="#createSupplierAddress">
                                    <i class="mdi mdi-plus me-1"></i> Tambah Alamat Sekarang
                                </button>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Total Order Chart Card --}}
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-transparent border-bottom py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <div class="avatar avatar-xs bg-label-primary rounded">
                            <i class="mdi mdi-chart-box-outline"></i>
                        </div>
                        <h5 class="card-title mb-0 fw-bold text-dark font-16">Statistik Tren Pengadaan Tahunan</h5>
                    </div>
                    <span class="badge bg-label-secondary rounded-pill small">5 Tahun Terakhir</span>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4 align-items-center">
                        <div class="col-12 col-lg-4 border-end-lg">
                            <div class="p-3 rounded-3 bg-label-primary mb-3">
                                <div class="text-primary small fw-semibold mb-1 text-uppercase tracking-wider">Total Order {{ $currentYear }}</div>
                                <div class="fw-bold text-dark" style="font-size: 1.6rem; line-height: 1.2;">
                                    Rp {{ number_format($currentYearTotal, 0, ',', '.') }}
                                </div>
                                <div class="text-muted small mt-2">
                                    Akumulasi nilai transaksi invoice barang masuk sepanjang tahun {{ $currentYear }}.
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-2 text-muted small">
                                <i class="mdi mdi-information-outline text-primary"></i>
                                Data dihitung otomatis dari riwayat faktur masuk barang.
                            </div>
                        </div>
                        <div class="col-12 col-lg-8">
                            <div id="supplierYearlyOrderChart" style="min-height: 220px;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- PIC Card (Right 35%) --}}
        <div class="col-12 supplier-col-pic">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-transparent border-bottom py-3 d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <div class="avatar avatar-xs bg-label-primary rounded">
                            <i class="mdi mdi-account-multiple-outline"></i>
                        </div>
                        <div>
                            <h5 class="card-title mb-0 fw-bold text-dark font-16">Narahubung PIC</h5>
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary btn-sm waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#createSupplierPic">
                        <i class="mdi mdi-plus me-1"></i> Tambah
                    </button>
                </div>
                <div class="card-body p-3">
                    @forelse ($pics as $pic)
                        @php
                            $picWords = preg_split('/\s+/', trim($pic->name_pic));
                            $picInitials = '';
                            foreach (array_slice($picWords, 0, 2) as $pw) {
                                $picInitials .= strtoupper(substr($pw, 0, 1));
                            }
                            $picInitials = $picInitials ?: 'P';
                            $picColor = $colors[$loop->index % count($colors)];
                        @endphp
                        <div class="pic-contact-card p-3 rounded-3 mb-3 border border-light-subtle bg-white transition-all shadow-none hover-shadow-sm">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div class="d-flex align-items-center gap-2.5">
                                    <div class="avatar avatar-sm flex-shrink-0">
                                        <span class="avatar-initial rounded-circle bg-label-{{ $picColor }} fw-bold font-13">
                                            {{ $picInitials }}
                                        </span>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold text-dark mb-0 font-14">{{ $pic->name_pic }}</h6>
                                        <span class="badge bg-label-secondary rounded-pill font-11 mt-0.5">
                                            {{ $pic->position ?: 'Person In Charge' }}
                                        </span>
                                    </div>
                                </div>

                                <div class="d-flex gap-1 flex-shrink-0">
                                    <button type="button" class="btn btn-icon btn-xs btn-label-primary waves-effect"
                                        data-bs-toggle="modal" data-bs-target="#updateSupplierPic-{{ $pic->id }}"
                                        title="Edit Kontak">
                                        <i class="mdi mdi-pencil-outline font-14"></i>
                                    </button>
                                    <button type="button" data-id="{{ $pic->id }}"
                                        class="btn btn-icon btn-xs btn-label-danger waves-effect delete-supplier-pic"
                                        title="Hapus Kontak">
                                        <i class="mdi mdi-delete-outline font-14"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="pt-2 border-top border-light-subtle mt-2 d-flex flex-column gap-1.5 font-12">
                                @if (!empty($pic->phone_pic))
                                    @php
                                        $cleanPicPhone = preg_replace('/[^0-9]/', '', $pic->phone_pic);
                                        if (str_starts_with($cleanPicPhone, '0')) {
                                            $waPicPhone = '62' . substr($cleanPicPhone, 1);
                                        } else {
                                            $waPicPhone = $cleanPicPhone;
                                        }
                                    @endphp
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center text-muted">
                                            <i class="mdi mdi-phone-outline text-success me-1.5 font-14"></i>
                                            <span class="text-dark">{{ $pic->phone_pic }}</span>
                                        </div>
                                        <div class="d-flex gap-1">
                                            <a href="tel:{{ $pic->phone_pic }}" class="text-secondary hover-primary" title="Telepon">
                                                <i class="mdi mdi-phone font-14"></i>
                                            </a>
                                            <a href="https://wa.me/{{ $waPicPhone }}" target="_blank" class="text-success hover-opacity" title="WhatsApp">
                                                <i class="mdi mdi-whatsapp font-14"></i>
                                            </a>
                                        </div>
                                    </div>
                                @else
                                    <div class="text-muted small">
                                        <i class="mdi mdi-phone-outline me-1.5 font-14 text-muted"></i>-
                                    </div>
                                @endif

                                @if (!empty($pic->email_pic))
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center text-muted text-truncate" style="max-width: 200px;">
                                            <i class="mdi mdi-email-outline text-danger me-1.5 font-14"></i>
                                            <span class="text-dark text-truncate">{{ $pic->email_pic }}</span>
                                        </div>
                                        <a href="mailto:{{ $pic->email_pic }}" class="text-danger hover-opacity" title="Kirim Email">
                                            <i class="mdi mdi-email font-14"></i>
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                        @push('modals')
                            @include('components.modal.warehouse.supplier.pic-update')
                        @endpush
                    @empty
                        <div class="text-center py-5 px-3">
                            <div class="avatar avatar-lg bg-label-secondary rounded-circle mx-auto mb-3">
                                <i class="mdi mdi-account-off-outline fs-3"></i>
                            </div>
                            <h6 class="fw-bold text-dark mb-1 font-15">Belum Ada Narahubung</h6>
                            <p class="text-muted small mb-3">
                                Belum ada data PIC yang didaftarkan untuk supplier ini.
                            </p>
                            <button type="button" class="btn btn-sm btn-primary waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#createSupplierPic">
                                <i class="mdi mdi-plus me-1"></i> Tambah PIC Sekarang
                            </button>
                        </div>
                    @endforelse

                    {{-- Quick Action Card --}}
                    <div class="p-3 rounded-3 bg-label-info mt-3 border border-info-subtle">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <i class="mdi mdi-lightbulb-on-outline text-info fs-5"></i>
                            <span class="fw-bold text-dark font-13">Pintasan Operasional</span>
                        </div>
                        <p class="text-muted small mb-2">Akses cepat menu terkait pengadaan dengan supplier ini:</p>
                        <div class="d-flex flex-column gap-1.5">
                            <a href="{{ url('/purchase/create?supplier_id=' . $supplier->id) }}" class="btn btn-xs btn-white border shadow-none text-start text-dark d-flex align-items-center justify-content-between">
                                <span><i class="mdi mdi-file-document-plus-outline text-primary me-1.5"></i>Buat Purchase Order (PO)</span>
                                <i class="mdi mdi-chevron-right text-muted"></i>
                            </a>
                            <a href="{{ url('/productIn') }}" class="btn btn-xs btn-white border shadow-none text-start text-dark d-flex align-items-center justify-content-between">
                                <span><i class="mdi mdi-plus-box-outline text-success me-1.5"></i>Catat Barang Masuk</span>
                                <i class="mdi mdi-chevron-right text-muted"></i>
                            </a>
                            <a href="{{ route('payable.statement', ['supplier_id' => $supplier->id]) }}" class="btn btn-xs btn-white border shadow-none text-start text-dark d-flex align-items-center justify-content-between">
                                <span><i class="mdi mdi-book-open-page-variant-outline text-info me-1.5"></i>Kartu Hutang Dagang</span>
                                <i class="mdi mdi-chevron-right text-muted"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Procurement & Transaction Records Card with Tabs (PO & Product In) --}}
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-header bg-transparent border-bottom py-3">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 pb-2">
                <div class="d-flex align-items-center gap-2">
                    <div class="avatar avatar-xs bg-label-primary rounded">
                        <i class="mdi mdi-clipboard-text-clock-outline"></i>
                    </div>
                    <div>
                        <h5 class="card-title mb-0 fw-bold text-dark font-16">Rekap Transaksi & Pengadaan</h5>
                        <p class="text-muted small mb-0">Riwayat Purchase Order (PO) dan Penerimaan Barang / Faktur Mitra</p>
                    </div>
                </div>
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <a href="{{ url('/purchase/create?supplier_id=' . $supplier->id) }}" class="btn btn-sm btn-primary waves-effect waves-light shadow-xs">
                        <i class="mdi mdi-plus me-1"></i> Buat PO Baru
                    </a>
                    <a href="{{ url('/productIn') }}" class="btn btn-sm btn-outline-primary waves-effect">
                        <i class="mdi mdi-package-down me-1"></i> Penerimaan Barang
                    </a>
                    <a href="{{ route('payable.statement', ['supplier_id' => $supplier->id]) }}" class="btn btn-sm btn-outline-info waves-effect">
                        <i class="mdi mdi-book-open-outline me-1"></i> Kartu Hutang
                    </a>
                </div>
            </div>

            {{-- Nav Tabs --}}
            <ul class="nav nav-tabs card-header-tabs" id="supplierTxTabs" role="tablist">
                <li class="nav-item">
                    <button class="nav-link active fw-bold py-2.5 d-flex align-items-center gap-2" 
                            id="tab-po" 
                            data-bs-toggle="tab" 
                            data-bs-target="#content-po" 
                            type="button" 
                            role="tab" 
                            aria-controls="content-po" 
                            aria-selected="true">
                        <i class="mdi mdi-file-document-outline fs-5"></i>
                        <span>Purchase Order (PO)</span>
                        <span class="badge bg-primary rounded-pill ms-1">{{ $poTotalCount }}</span>
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link fw-bold py-2.5 d-flex align-items-center gap-2" 
                            id="tab-in" 
                            data-bs-toggle="tab" 
                            data-bs-target="#content-in" 
                            type="button" 
                            role="tab" 
                            aria-controls="content-in" 
                            aria-selected="false">
                        <i class="mdi mdi-package-down fs-5"></i>
                        <span>Barang Masuk & Invoice</span>
                        <span class="badge bg-success rounded-pill ms-1">{{ $supplier->productIn()->count() }}</span>
                    </button>
                </li>
            </ul>
        </div>

        <div class="tab-content p-0">
            {{-- TAB 1: REKAP PURCHASE ORDER (PO) --}}
            <div class="tab-pane fade show active" id="content-po" role="tabpanel" aria-labelledby="tab-po">
                <div class="table-responsive p-3">
                    <table class="table table-hover align-middle border-top" id="tableSupplierPo">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 40px;" class="text-center">#</th>
                                <th>No. PO & Tanggal</th>
                                <th>Item & Komoditas Pengadaan</th>
                                <th>Termin Pembayaran</th>
                                <th>Status TTD &amp; GR</th>
                                <th class="text-end">Total Nilai PO</th>
                                <th class="text-center" style="width: 120px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($purchaseOrders as $index => $po)
                                <tr>
                                    <td class="text-center text-muted fw-semibold">{{ $index + 1 }}</td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <a href="{{ route('purchase.show', $po->id) }}" class="fw-bold text-primary font-monospace" style="font-size: 0.95rem;">
                                                <i class="mdi mdi-file-document-outline me-1"></i>{{ $po->no_po }}
                                            </a>
                                            <div class="text-muted small mt-0.5">
                                                <i class="mdi mdi-calendar-blank-outline text-secondary me-1"></i>
                                                {{ $po->date ? \Carbon\Carbon::parse($po->date)->format('d M Y') : ($po->created_at ? $po->created_at->format('d M Y') : '-') }}
                                            </div>
                                            @if ($po->purchaseRequest)
                                                <div class="mt-1">
                                                    <a href="{{ route('purchase-request.show', $po->id_purchase_request) }}" class="badge bg-label-secondary rounded-pill font-11 text-decoration-none" title="Lihat Purchase Request">
                                                        <i class="mdi mdi-link-variant me-1"></i>PR: {{ $po->purchaseRequest->no_pr ?? 'Lihat PR' }}
                                                    </a>
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column gap-1" style="max-width: 320px;">
                                            @php
                                                $poDetails = $po->detail;
                                                $firstItems = $poDetails->take(2);
                                                $remainingCount = $poDetails->count() - 2;
                                            @endphp
                                            @forelse ($firstItems as $d)
                                                @php
                                                    $rawProd = $d->getAttribute('product');
                                                    $itemName = (is_string($rawProd) && !empty($rawProd)) 
                                                        ? $rawProd 
                                                        : ($d->product?->commodity ?? ($d->product?->description ?? 'Item PO'));
                                                    $itemUnit = !empty($d->info_qty) ? $d->info_qty : ($d->unit?->unit ?? ($d->product?->unit ?? ''));
                                                @endphp
                                                <div class="d-flex align-items-center justify-content-between small">
                                                    <span class="text-dark text-truncate me-2" title="{{ $itemName }}">
                                                        • {{ $itemName }}
                                                    </span>
                                                    <span class="badge bg-label-primary px-1.5 py-0 font-11 flex-shrink-0">
                                                        {{ number_format($d->qty ?? 1, 0, ',', '.') }} {{ $itemUnit ?: 'pcs' }}
                                                    </span>
                                                </div>
                                            @empty
                                                <span class="text-muted small">Tidak ada rincian item</span>
                                            @endforelse
                                            @if ($remainingCount > 0)
                                                <span class="text-muted font-11 fw-semibold">+{{ $remainingCount }} item lainnya...</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column small">
                                            @if ($po->isTempo())
                                                <span class="badge bg-label-warning rounded-pill px-2.5 py-1 mb-1 d-inline-block" style="width: fit-content;">
                                                    <i class="mdi mdi-clock-outline me-1"></i>Tempo {{ $po->top_days ?: 30 }} Hari
                                                </span>
                                            @else
                                                <span class="badge bg-label-success rounded-pill px-2.5 py-1 mb-1 d-inline-block" style="width: fit-content;">
                                                    <i class="mdi mdi-cash-check me-1"></i>{{ ucfirst($po->payment_type ?? 'Lunas') }}
                                                </span>
                                            @endif
                                            @if (!empty($po->payment))
                                                <span class="text-muted font-11 text-truncate" style="max-width: 160px;" title="{{ $po->payment }}">
                                                    {{ $po->payment }}
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column gap-1">
                                            @if ($po->isSignedByVendor())
                                                <span class="badge bg-label-success rounded-pill font-11" title="Ditandatangani oleh: {{ $po->vendor_signer_name }}">
                                                    <i class="mdi mdi-draw me-1"></i>Sudah TTD
                                                </span>
                                                <small class="text-muted font-11">
                                                    {{ $po->vendor_signed_at ? \Carbon\Carbon::parse($po->vendor_signed_at)->format('d/m/Y H:i') : '' }}
                                                </small>
                                            @else
                                                <span class="badge bg-label-secondary rounded-pill font-11">
                                                    <i class="mdi mdi-pencil-outline me-1"></i>Belum TTD
                                                </span>
                                            @endif

                                            @if (!empty($po->no_gr))
                                                <span class="badge bg-label-info rounded-pill font-11 mt-0.5">
                                                    <i class="mdi mdi-truck-check-outline me-1"></i>GR: {{ $po->no_gr }}
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="text-end">
                                        <div class="d-flex flex-column align-items-end">
                                            <span class="fw-bold text-dark font-15 text-primary">
                                                Rp {{ number_format($po->total, 0, ',', '.') }}
                                            </span>
                                            @if ($po->subtotal > 0 && $po->subtotal != $po->total)
                                                <span class="text-muted font-11">
                                                    DPP: Rp {{ number_format($po->subtotal, 0, ',', '.') }}
                                                </span>
                                            @endif
                                            @if ($po->vat > 0)
                                                <span class="text-muted font-11">
                                                    PPN: Rp {{ number_format($po->vat, 0, ',', '.') }}
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-inline-flex gap-1">
                                            {{-- Detail PO --}}
                                            <a href="{{ route('purchase.show', $po->id) }}" 
                                               class="btn btn-sm btn-icon btn-outline-primary waves-effect" 
                                               data-bs-toggle="tooltip" 
                                               data-bs-placement="top" 
                                               title="Buka Detail PO">
                                                <i class="mdi mdi-eye-outline"></i>
                                            </a>

                                            {{-- Cetak PO --}}
                                            <a href="{{ route('purchase.show_print', $po->id) }}" 
                                               target="_blank" 
                                               class="btn btn-sm btn-icon btn-outline-secondary waves-effect" 
                                               data-bs-toggle="tooltip" 
                                               data-bs-placement="top" 
                                               title="Cetak Dokumen PO">
                                                <i class="mdi mdi-printer-outline"></i>
                                            </a>

                                            {{-- Edit PO --}}
                                            <a href="{{ route('purchase.edit', $po->id) }}" 
                                               class="btn btn-sm btn-icon btn-outline-warning waves-effect" 
                                               data-bs-toggle="tooltip" 
                                               data-bs-placement="top" 
                                               title="Edit PO">
                                                <i class="mdi mdi-pencil-outline"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <div class="d-flex flex-column align-items-center justify-content-center">
                                            <div class="avatar avatar-xl bg-label-secondary mb-3 rounded-circle p-3">
                                                <i class="mdi mdi-file-document-outline fs-1"></i>
                                            </div>
                                            <h6 class="fw-bold text-secondary mb-1">Belum Ada Purchase Order (PO)</h6>
                                            <p class="text-muted small mb-3">Belum ada dokumen PO yang diterbitkan untuk supplier {{ $supplier->supplier }}.</p>
                                            <a href="{{ url('/purchase/create?supplier_id=' . $supplier->id) }}" class="btn btn-sm btn-primary shadow-xs">
                                                <i class="mdi mdi-plus me-1"></i> Buat PO Sekarang
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- TAB 2: RIWAYAT BARANG MASUK & INVOICE --}}
            <div class="tab-pane fade" id="content-in" role="tabpanel" aria-labelledby="tab-in">
                <div class="p-3">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h6 class="fw-bold text-dark mb-0 font-14">Riwayat Invoice & Penerimaan Barang</h6>
                            <small class="text-muted">Data surat jalan & faktur masuk yang telah dicatat dari supplier ini</small>
                        </div>
                        <a href="{{ url('/productIn') }}" class="btn btn-sm btn-outline-primary waves-effect">
                            <i class="mdi mdi-plus me-1"></i> Penerimaan Baru
                        </a>
                    </div>
                    <div class="card-datatable table-responsive pt-0">
                        <table class="datatable-product-in-supplier table table-hover align-middle border-top w-100">
                            <thead class="table-light">
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th>No. Invoice</th>
                                    <th>Produk &amp; Kuantitas</th>
                                    <th class="text-end">Subtotal</th>
                                    <th class="text-end">PPN / Pajak</th>
                                    <th class="text-center">Tanggal Masuk</th>
                                    <th class="text-center" style="width: 80px;">Aksi</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Sliding Quick Preview Drawer (Offcanvas) for Invoice & Goods Receipt with Blur Effect (Same as PR) --}}
<div class="offcanvas offcanvas-end shadow-lg" tabindex="-1" id="invoiceProductInOffcanvas" aria-labelledby="invoiceProductInOffcanvasLabel" style="width: 540px; max-width: 92vw;">
    <div class="offcanvas-header bg-label-primary border-bottom py-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-primary text-white font-11 rounded-pill" id="invDrawerInvoiceBadge">INV-0000</span>
                <div id="invDrawerStatusBadge"></div>
            </div>
            <h5 class="offcanvas-title fw-bold text-heading mb-0" id="invoiceProductInOffcanvasLabel">Rincian Faktur &amp; Barang Masuk</h5>
        </div>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body p-4">
        {{-- Document Summary Card --}}
        <div class="card border mb-3 bg-light">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between pb-2 mb-2 border-bottom">
                    <div>
                        <span class="text-muted font-11 d-block text-uppercase fw-bold">Mitra Supplier:</span>
                        <h6 class="fw-bold text-heading font-14 mb-0">{{ $supplier->supplier }}</h6>
                    </div>
                    <div class="text-end">
                        <span class="badge bg-label-primary rounded-pill font-11">
                            <i class="mdi mdi-domain me-1"></i>{{ $supplier->area ?: 'Supplier' }}
                        </span>
                    </div>
                </div>

                <div class="row g-2 font-12">
                    <div class="col-6">
                        <span class="text-muted d-block font-11">No. Invoice:</span>
                        <strong class="text-primary font-monospace" id="invDrawerInvoiceNo">-</strong>
                    </div>
                    <div class="col-6">
                        <span class="text-muted d-block font-11">No. Surat Jalan (DO):</span>
                        <strong class="text-heading font-monospace" id="invDrawerNoDo">-</strong>
                    </div>
                    <div class="col-6">
                        <span class="text-muted d-block font-11">No. Dokumen Masuk:</span>
                        <strong class="text-heading font-monospace" id="invDrawerNoProductIn">-</strong>
                    </div>
                    <div class="col-6">
                        <span class="text-muted d-block font-11">Tgl Penerimaan:</span>
                        <strong class="text-heading" id="invDrawerDate">-</strong>
                    </div>
                    <div class="col-12">
                        <span class="text-muted d-block font-11">Tgl Jatuh Tempo / Bayar:</span>
                        <strong class="text-heading" id="invDrawerPaymentDate">-</strong>
                    </div>
                </div>
            </div>
        </div>

        {{-- Financial Info Card --}}
        <div class="card border mb-3 bg-white">
            <div class="card-body p-3">
                <span class="text-muted font-11 d-block text-uppercase fw-bold mb-2">
                    <i class="mdi mdi-cash-multiple text-success me-1"></i> Rincian Nilai Faktur
                </span>
                <div class="d-flex justify-content-between align-items-center mb-1 font-13">
                    <span class="text-muted">Subtotal (DPP):</span>
                    <span class="fw-semibold text-dark" id="invDrawerSubtotal">-</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-2 font-13">
                    <span class="text-muted">PPN / Pajak:</span>
                    <span class="fw-semibold text-dark" id="invDrawerTax">-</span>
                </div>
                <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                    <span class="fw-bold text-dark font-14">Total Tagihan:</span>
                    <span class="fw-bold text-primary font-16" id="invDrawerTotal">-</span>
                </div>
            </div>
        </div>

        {{-- Items Table --}}
        <div class="mb-3">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <label class="font-11 text-muted text-uppercase fw-bold mb-0">Daftar Komoditas / Barang (<span id="invDrawerItemCount">0</span> item)</label>
            </div>
            <div class="table-responsive border rounded-3 bg-white">
                <table class="table table-sm table-hover mb-0">
                    <thead class="table-light">
                        <tr class="font-11 text-muted text-uppercase">
                            <th style="width: 35px;" class="text-center">#</th>
                            <th>Nama Komoditas / Produk</th>
                            <th class="text-end" style="width: 120px;">Kuantitas</th>
                        </tr>
                    </thead>
                    <tbody id="invDrawerItemsTbody" class="font-12">
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Note Card --}}
        <div class="card border mb-3 bg-white" id="invDrawerNoteCard" style="display: none;">
            <div class="card-body p-3">
                <span class="text-muted font-11 d-block text-uppercase fw-bold mb-1">
                    <i class="mdi mdi-note-text-outline text-info me-1"></i> Catatan Transaksi
                </span>
                <div class="text-dark font-13" id="invDrawerNote">-</div>
            </div>
        </div>
    </div>
    <div class="offcanvas-footer border-top p-3 d-flex align-items-center justify-content-between gap-2">
        <a href="#" id="invDrawerDetailBtn" class="btn btn-primary d-flex align-items-center gap-1 shadow-xs">
            <i class="mdi mdi-open-in-new me-1"></i> Buka Halaman Lengkap
        </a>
        <a href="#" id="invDrawerPrintBtn" target="_blank" class="btn btn-outline-secondary d-flex align-items-center gap-1 shadow-xs">
            <i class="mdi mdi-printer-outline me-1"></i> Cetak Faktur
        </a>
    </div>
</div>

@include('components.modal.warehouse.supplier.pic-create')
@include('components.modal.warehouse.supplier.address-create')
@include('components.modal.warehouse.supplier.form')
@endsection

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
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/apex-charts/apex-charts.css" />
    <style>
        .supplier-hero-card {
            background: linear-gradient(135deg, rgba(105, 108, 255, 0.05) 0%, rgba(255, 255, 255, 1) 100%);
            border: 1px solid rgba(105, 108, 255, 0.12) !important;
        }

        .bg-light-soft {
            background-color: #f8f9fa;
        }

        .dark-style .bg-light-soft {
            background-color: #2b2c40 !important;
        }

        .info-tile {
            transition: all 0.2s ease-in-out;
        }

        .info-tile:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
        }

        .kpi-metric-card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .kpi-metric-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.06) !important;
        }

        .pic-contact-card {
            transition: all 0.2s ease-in-out;
        }

        .pic-contact-card:hover {
            border-color: #696cff !important;
            box-shadow: 0 4px 14px rgba(105, 108, 255, 0.08) !important;
        }

        .btn-xs {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
            line-height: 1.2;
            border-radius: 0.375rem;
        }

        .btn-icon.btn-xs {
            width: 26px;
            height: 26px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .font-11 { font-size: 0.6875rem !important; }
        .font-12 { font-size: 0.75rem !important; }
        .font-13 { font-size: 0.8125rem !important; }
        .font-14 { font-size: 0.875rem !important; }
        .font-15 { font-size: 0.9375rem !important; }
        .font-16 { font-size: 1rem !important; }

        @media (min-width: 992px) {
            .supplier-company-pic-row {
                flex-wrap: nowrap !important;
            }

            .supplier-col-company {
                flex: 0 0 65% !important;
                max-width: 65% !important;
                width: 65% !important;
            }

            .supplier-col-pic {
                flex: 0 0 35% !important;
                max-width: 35% !important;
                width: 35% !important;
            }

            .border-end-lg {
                border-right: 1px solid var(--bs-border-color) !important;
            }
        }

        /* Glassmorphism Backdrop Blur for Offcanvas Drawer (Like PR) */
        .offcanvas-backdrop {
            transition: opacity 0.3s cubic-bezier(0.16, 1, 0.3, 1), backdrop-filter 0.3s cubic-bezier(0.16, 1, 0.3, 1) !important;
        }
        .offcanvas-backdrop.show {
            backdrop-filter: blur(8px) saturate(160%) !important;
            -webkit-backdrop-filter: blur(8px) saturate(160%) !important;
            background-color: rgba(15, 23, 42, 0.5) !important;
            opacity: 1 !important;
        }

        /* Slide-over Drawer Elevation */
        #invoiceProductInOffcanvas {
            box-shadow: -15px 0 45px rgba(15, 23, 42, 0.3) !important;
            border-left: 1px solid rgba(0, 0, 0, 0.08) !important;
            transition: transform 0.35s cubic-bezier(0.16, 1, 0.3, 1) !important;
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
    <script src="{{ asset('assets') }}/includes/table-product-in-supplier.js?v={{ filemtime(public_path('assets/includes/table-product-in-supplier.js')) }}"></script>
    <script src="{{ asset('assets') }}/js/extended-ui-sweetalert2.js"></script>
    <script src="{{ asset('assets') }}/js/forms-selects.js"></script>
    <script>
        (function () {
            const isDark = document.documentElement.classList.contains('dark-style');
            const labelColor = isDark ? '#a8aaae' : '#6d6b77';
            const borderColor = isDark ? '#404152' : '#e7e7e8';

            const formatRp = val => {
                if (val >= 1_000_000_000) return 'Rp ' + (val / 1_000_000_000).toFixed(1) + ' M';
                if (val >= 1_000_000) return 'Rp ' + (val / 1_000_000).toFixed(1) + ' Jt';
                return 'Rp ' + (val || 0).toLocaleString('id-ID');
            };

            const yearlyLabels = @json($yearlyLabels);
            const yearlyTotals = @json($yearlyTotals);

            const chartEl = document.querySelector('#supplierYearlyOrderChart');
            if (chartEl) {
                new ApexCharts(chartEl, {
                    chart: {
                        type: 'bar',
                        height: 230,
                        toolbar: { show: false },
                        parentHeightOffset: 0
                    },
                    series: [{ name: 'Total Pengadaan', data: yearlyTotals }],
                    colors: ['#696cff'],
                    plotOptions: {
                        bar: {
                            borderRadius: 6,
                            columnWidth: '38%',
                            distributed: false,
                            dataLabels: { position: 'top' }
                        }
                    },
                    dataLabels: {
                        enabled: false
                    },
                    legend: { show: false },
                    xaxis: {
                        categories: yearlyLabels,
                        labels: {
                            style: {
                                colors: labelColor,
                                fontSize: '13px',
                                fontFamily: 'inherit',
                                fontWeight: 500
                            }
                        },
                        axisBorder: { show: false },
                        axisTicks: { show: false },
                    },
                    yaxis: {
                        labels: {
                            formatter: formatRp,
                            style: {
                                colors: labelColor,
                                fontSize: '11px',
                                fontFamily: 'inherit'
                            }
                        }
                    },
                    grid: {
                        borderColor: borderColor,
                        strokeDashArray: 4,
                        padding: {
                            top: -10,
                            bottom: -5,
                            left: 10,
                            right: 10
                        }
                    },
                    tooltip: {
                        theme: isDark ? 'dark' : 'light',
                        y: {
                            formatter: function(val) {
                                return 'Rp ' + (val || 0).toLocaleString('id-ID');
                            }
                        }
                    },
                }).render();
            }

            // Inisialisasi DataTable untuk Rekap PO Supplier
            $(document).ready(function() {
                if ($('#tableSupplierPo tbody tr').length > 0 && !$('#tableSupplierPo tbody tr td[colspan]').length) {
                    $('#tableSupplierPo').DataTable({
                        order: [[1, 'desc']],
                        pageLength: 10,
                        language: {
                            search: "_INPUT_",
                            searchPlaceholder: "Cari nomor PO, komoditas, item...",
                            lengthMenu: "Tampilkan _MENU_ data",
                            info: "Menampilkan _START_ - _END_ dari _TOTAL_ PO",
                            infoEmpty: "Menampilkan 0 PO",
                            zeroRecords: "Tidak ada data PO yang sesuai kriteria pencarian",
                            paginate: { first: "Awal", last: "Akhir", next: "Lanjut", previous: "Kembali" }
                        },
                        dom: '<"row align-items-center mb-2"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 text-md-end"f>>t<"row align-items-center mt-2"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6 text-md-end"p>>'
                    });
                }

                // Adjust table column widths when switching tabs
                $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
                    $($.fn.dataTable.tables(true)).DataTable().columns.adjust();
                });
            });
        })();
    </script>
@endpush

@push('script')
    <script>
        $(document).on('click', '.delete-supplier-pic', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: "Hapus PIC Supplier?",
                text: "Data narahubung ini akan dihapus permanen.",
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
                        'url': '{{ url('supplier/pic') }}/' + id,
                        'type': 'POST',
                        'data': {
                            '_method': 'DELETE',
                            '_token': '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response == 1) {
                                Swal.fire({
                                    icon: "success",
                                    title: "Berhasil Dihapus!",
                                    text: "Data PIC supplier berhasil dihapus.",
                                    customClass: {
                                        confirmButton: "btn btn-success waves-effect",
                                    },
                                });
                                window.setTimeout(function() {
                                    location.reload();
                                }, 1200);
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal Menghapus',
                                    text: 'Terjadi kendala saat menghapus data PIC.'
                                });
                            }
                        }
                    });
                }
            });
        });

        $(document).on('click', '.delete-supplier', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: "Hapus Mitra Supplier?",
                text: "Apakah Anda yakin ingin menghapus data supplier ini? Tindakan ini tidak dapat dibatalkan.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Ya, Hapus Supplier!",
                cancelButtonText: "Batal",
                customClass: {
                    confirmButton: "btn btn-danger me-3 waves-effect waves-light",
                    cancelButton: "btn btn-label-secondary waves-effect",
                },
                buttonsStyling: false,
            }).then(function(result) {
                if (result.value) {
                    $.ajax({
                        'url': '{{ url('supplier') }}/' + id,
                        'type': 'POST',
                        'data': {
                            '_method': 'DELETE',
                            '_token': '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response == 1) {
                                Swal.fire({
                                    icon: "success",
                                    title: "Supplier Dihapus!",
                                    text: "Data supplier telah berhasil dihapus dari sistem.",
                                    customClass: {
                                        confirmButton: "btn btn-success waves-effect",
                                    },
                                });
                                window.setTimeout(function() {
                                    window.location.href = '{{ route('supplier.index') }}';
                                }, 1500);
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal Menghapus',
                                    text: 'Data supplier tidak dapat dihapus.'
                                });
                            }
                        }
                    });
                }
            });
        $(document).on('click', '.delete-supplier-address', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: "Hapus Alamat Supplier?",
                text: "Alamat supplier ini akan dihapus dari daftar.",
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
                        url: '{{ url('supplier/address') }}/' + id,
                        type: 'POST',
                        data: {
                            _method: 'DELETE',
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.success || response == 1) {
                                Swal.fire({
                                    icon: "success",
                                    title: "Berhasil Dihapus!",
                                    text: "Alamat supplier berhasil dihapus.",
                                    customClass: {
                                        confirmButton: "btn btn-success waves-effect",
                                    },
                                });
                                window.setTimeout(function() {
                                    location.reload();
                                }, 1000);
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal Menghapus',
                                    text: 'Terjadi kendala saat menghapus alamat supplier.'
                                });
                            }
                        }
                    });
                }
            });
        });

        $(document).on('click', '.set-primary-supplier-address', function() {
            var id = $(this).data('id');
            $.ajax({
                url: '{{ url('supplier/address') }}/' + id + '/set-primary',
                type: 'POST',
                data: {
                    _method: 'PATCH',
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success || response == 1) {
                        Swal.fire({
                            icon: "success",
                            title: "Alamat Utama Diperbarui!",
                            text: "Alamat ini telah dijadikan sebagai alamat utama supplier.",
                            showConfirmButton: false,
                            timer: 1200
                        });
                        window.setTimeout(function() {
                            location.reload();
                        }, 1000);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: 'Gagal mengubah status alamat utama.'
                        });
                    }
                }
            });
        });
    </script>
@endpush
