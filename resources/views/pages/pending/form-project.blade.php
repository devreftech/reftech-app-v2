@extends('layouts.sales.app')
@section('title', 'Product Out - Project Delivery')

@php
    $isLogistic = Auth::check() && in_array(strtolower(Auth::user()->role ?? ''), ['logistic']);
@endphp

@push('after-style')
<link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/select2/select2.css" />
<style>
    /* Card & Container Polish */
    .po-project-card {
        border-radius: 12px;
        border: 1px solid rgba(0, 0, 0, 0.08);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    html.dark-style .po-project-card {
        border-color: rgba(255, 255, 255, 0.08);
        background-color: #2b2c40;
    }

    .hero-banner {
        background: linear-gradient(135deg, rgba(105, 108, 255, 0.08) 0%, rgba(105, 108, 255, 0.02) 100%);
        border: 1px solid rgba(105, 108, 255, 0.2);
        border-radius: 12px;
    }
    html.dark-style .hero-banner {
        background: linear-gradient(135deg, rgba(105, 108, 255, 0.15) 0%, rgba(43, 44, 64, 0.6) 100%);
        border-color: rgba(105, 108, 255, 0.3);
    }

    /* Section Headers */
    .section-header-title {
        font-size: 1.05rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    /* Custom Badges & Tags */
    .badge-pill-channel {
        cursor: pointer;
        padding: 8px 16px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.85rem;
        transition: all 0.2s ease;
        border: 1px solid rgba(0,0,0,0.1);
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .badge-pill-channel.active {
        background-color: #696cff !important;
        color: #fff !important;
        border-color: #696cff !important;
        box-shadow: 0 4px 10px rgba(105, 108, 255, 0.35);
    }

    /* Item Table Styling */
    .table-items th {
        background-color: #f8f9fa;
        font-weight: 600;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 12px 14px;
    }
    html.dark-style .table-items th {
        background-color: #232333;
        color: #cfd3ec;
    }
    .table-items td {
        padding: 14px 14px;
        vertical-align: middle;
    }

    /* Summary Financial Box */
    .financial-summary-card {
        background: #fdfdfd;
        border: 1px solid rgba(0, 0, 0, 0.08);
        border-radius: 12px;
    }
    html.dark-style .financial-summary-card {
        background: #232333;
        border-color: rgba(255, 255, 255, 0.08);
    }

    /* Sticky Bottom Action Bar */
    .sticky-action-bar {
        position: sticky;
        bottom: 1rem;
        z-index: 1020;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(8px);
        border: 1px solid rgba(0, 0, 0, 0.12);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        border-radius: 14px;
        padding: 14px 24px;
        margin-top: 1.5rem;
    }
    html.dark-style .sticky-action-bar {
        background: rgba(43, 44, 64, 0.95);
        border-color: rgba(255, 255, 255, 0.12);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.4);
    }

    .quick-template-badge {
        cursor: pointer;
        user-select: none;
        transition: all 0.15s ease;
    }
    .quick-template-badge:hover {
        transform: translateY(-1px);
        opacity: 0.85;
    }
</style>
@endpush

@section('content')
<div class="container-fluid flex-grow-1 container-p-y px-3 px-md-4">
    
    {{-- Breadcrumb Navigation --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb m-0 fs-6">
                <li class="breadcrumb-item">
                    <a href="{{ route('pending-po.sales-order') }}" class="text-muted">
                        <i class="mdi mdi-clipboard-text-outline me-1"></i>Sales Order
                    </a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('pending-po.show', $pending->id) }}" class="text-muted">
                        {{ $pending->no_pending ?: 'Pending PO #' . $pending->id }}
                    </a>
                </li>
                <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">
                    Product Out (Project)
                </li>
            </ol>
        </nav>
        <a href="{{ url()->previous() != url()->current() ? url()->previous() : route('pending-po.show', $pending->id) }}"
           class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1">
            <i class="mdi mdi-arrow-left"></i> Kembali
        </a>
    </div>

    {{-- Hero Context Banner --}}
    <div class="hero-banner p-3 p-md-4 mb-4">
        <div class="row align-items-center g-3">
            <div class="col-lg-8 col-md-7 col-12">
                <div class="d-flex align-items-center gap-2 flex-wrap mb-2">
                    <span class="badge bg-primary fs-6 px-3 py-1 fw-bold">
                        <i class="mdi mdi-dolly me-1"></i> FORM BARANG KELUAR (PROJECT)
                    </span>
                    <span class="badge bg-label-info">
                        <i class="mdi mdi-folder-star-outline me-1"></i> Tipe: {{ $pending->type ?? 'Project' }}
                    </span>
                    @if($pending->project_category)
                        <span class="badge bg-label-secondary">
                            Kategori: {{ $pending->project_category }}
                        </span>
                    @endif
                    @php
                        $isKojisha = ($pending->quote && (method_exists($pending->quote, 'isKojisha') ? $pending->quote->isKojisha() : ($pending->quote->flag === 'Kojisha')))
                            || ($pending->unitQuotation && (method_exists($pending->unitQuotation, 'isKojisha') ? $pending->unitQuotation->isKojisha() : false));
                    @endphp
                    <span class="badge {{ $isKojisha ? 'bg-label-warning' : 'bg-label-success' }}">
                        {{ $isKojisha ? 'Kojisha' : 'Reftech' }}
                    </span>
                </div>
                <h4 class="mb-1 fw-bold text-dark">
                    {{ $pending->title ?: ($pending->unitQuotation?->title ?? 'Pengeluaran Barang Project') }}
                </h4>
                <div class="text-muted small d-flex flex-wrap gap-3 mt-2">
                    <span><strong>No. Pending / SO:</strong> {{ $pending->no_pending ?: '-' }}</span>
                    <span><strong>Ref. Quotation:</strong> {{ $pending->unitQuotation?->no_quote ?? $pending->quote?->no_quote ?? '-' }}</span>
                    <span><strong>No. BK:</strong> <span class="badge bg-label-primary font-monospace">{{ $nextNoProductOut ?? '-' }}</span></span>
                    <span><strong>Tanggal PO:</strong> {{ $pending->date ? \Carbon\Carbon::parse($pending->date)->format('d/m/Y') : '-' }}</span>
                </div>
            </div>
            <div class="col-lg-4 col-md-5 col-12 text-md-end text-start">
                <div class="d-inline-flex flex-column align-items-md-end align-items-start">
                    <span class="text-muted small mb-1">Status Alur Dokumen</span>
                    <span class="badge bg-label-primary px-3 py-2 fs-6 rounded-pill d-flex align-items-center gap-1">
                        <i class="mdi mdi-truck-delivery-outline fs-5"></i>
                        Proses Pengiriman Logistik
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- Error Alert if validation fails --}}
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible shadow-sm mb-4" role="alert">
            <div class="d-flex align-items-start gap-2">
                <i class="mdi mdi-alert-circle-outline fs-4 mt-n1"></i>
                <div>
                    <h6 class="alert-heading fw-bold mb-1">Harap periksa kembali input formulir:</h6>
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Delivery History Alert if partial deliveries exist --}}
    @if(!empty($allProductOuts) && count($allProductOuts) > 0)
        <div class="alert bg-label-info border border-info alert-dismissible mb-4 shadow-sm" role="alert">
            <div class="d-flex align-items-start gap-2">
                <i class="mdi mdi-truck-check-outline fs-4 text-info mt-n1"></i>
                <div class="w-100">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-1">
                        <h6 class="fw-bold mb-0 text-info">Riwayat Pengiriman Sebelumnya (Partial Delivery)</h6>
                        <span class="badge bg-info text-white">{{ count($allProductOuts) }} Surat Jalan Telah Dibuat</span>
                    </div>
                    <p class="small mb-2">Project Sales Order ini telah memiliki pengeluaran barang sebelumnya. Input kuantitas di bawah ini telah disesuaikan dengan <strong>sisa barang</strong> yang belum terkirim.</p>
                    <div class="table-responsive bg-white rounded-2 border">
                        <table class="table table-sm table-borderless font-12 mb-0">
                            <thead class="table-light font-11 text-uppercase text-muted">
                                <tr>
                                    <th class="ps-2">No. Surat Jalan (BK)</th>
                                    <th>Tanggal</th>
                                    <th>Driver / Kendaraan</th>
                                    <th class="text-center">Jml Item</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($allProductOuts as $pastPo)
                                    <tr class="border-bottom">
                                        <td class="ps-2 fw-bold font-monospace text-primary">{{ $pastPo->code_product_out }}</td>
                                        <td>{{ $pastPo->date_out ? \Carbon\Carbon::parse($pastPo->date_out)->format('d/m/Y') : '-' }}</td>
                                        <td>{{ $pastPo->driver ?? '-' }} {{ $pastPo->vehicle_number ? '(' . $pastPo->vehicle_number . ')' : '' }}</td>
                                        <td class="text-center"><span class="badge bg-label-primary">{{ $pastPo->detailProductOut->count() }} item</span></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Main Submission Form --}}
    <form id="formProductOutProject" action="{{ route('pending-po.product_out-post', $id) }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- Row 1: Informasi Dokumen & Referensi Klien --}}
        <div class="row g-4 mb-4">
            {{-- Card Left: Dokumen & Transaksi --}}
            <div class="col-12 col-lg-6">
                <div class="card po-project-card h-100 shadow-sm">
                    <div class="card-header border-bottom py-3 d-flex align-items-center justify-content-between">
                        <div class="section-header-title text-primary">
                            <i class="mdi mdi-receipt-text-outline fs-5"></i>
                            <span>Dokumen Referensi & Transaksi</span>
                        </div>
                        <span class="badge bg-label-primary font-monospace">Langkah 1</span>
                    </div>
                    <div class="card-body pt-4">
                        <div class="row g-3">
                            {{-- No Barang Keluar (BK) --}}
                            <div class="col-12">
                                <label class="form-label fw-bold small text-uppercase text-muted d-flex justify-content-between align-items-center" for="inputNoProductOut">
                                    <span>No. Dokumen Barang Keluar (BK) <span class="text-danger">*</span></span>
                                    <span class="badge bg-label-primary font-monospace" style="font-size: 10.5px;">Auto Generated</span>
                                </label>
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text bg-light-subtle text-primary"><i class="mdi mdi-truck-delivery-outline"></i></span>
                                    <input type="text" class="form-control fw-bold fs-6 text-primary font-monospace @error('no_product_out') is-invalid @enderror"
                                           id="inputNoProductOut" name="no_product_out"
                                           value="{{ old('no_product_out', $nextNoProductOut ?? '') }}"
                                           placeholder="Contoh: 032-P/BK/IX/2026" required>
                                </div>
                                <div class="form-text small">Nomor registrasi bukti barang keluar (disamakan dengan format penomoran barang keluar manual).</div>
                            </div>

                            {{-- No Invoice --}}
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-bold small text-uppercase text-muted" for="inputInvoice">
                                    No. Invoice <span class="text-danger">*</span>
                                </label>
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text bg-light-subtle"><i class="mdi mdi-file-certificate-outline"></i></span>
                                    <input type="text" class="form-control fw-bold fs-6 @error('invoice') is-invalid @enderror"
                                           id="inputInvoice" name="invoice"
                                           value="{{ old('invoice', $invoiceNo ?? '') }}"
                                           placeholder="Contoh: 661/SJ-P/RJO/IX/2026" required>
                                </div>
                                <div class="form-text small">Nomor invoice resmi accounting / penagihan.</div>
                            </div>

                            {{-- No PO --}}
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-bold small text-uppercase text-muted" for="inputPo">
                                    No. Purchase Order (PO)
                                </label>
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text bg-light-subtle"><i class="mdi mdi-tag-text-outline"></i></span>
                                    <input type="text" class="form-control fw-bold fs-6"
                                           id="inputPo" name="po"
                                           value="{{ old('po', $poNo ?? '') }}"
                                           placeholder="Contoh: 12236484">
                                </div>
                                <div class="form-text small">Nomor PO yang diterbitkan klien.</div>
                            </div>

                            {{-- Tanggal Transaksi --}}
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-bold small text-uppercase text-muted" for="inputDate">
                                    Tanggal Transaksi <span class="text-danger">*</span>
                                </label>
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text bg-light-subtle"><i class="mdi mdi-calendar-blank-outline"></i></span>
                                    <input type="date" class="form-control"
                                           id="inputDate" name="date"
                                           value="{{ old('date', date('Y-m-d')) }}" required>
                                </div>
                            </div>

                            {{-- Kanal Pengiriman (Offline / Online) --}}
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-bold small text-uppercase text-muted d-block">
                                    Kanal Distribusi <span class="text-danger">*</span>
                                </label>
                                <div class="d-flex align-items-center gap-2 mt-1">
                                    <input type="hidden" name="vers" id="inputChannelVers" value="{{ old('vers', 'Offline') }}">
                                    <span class="badge-pill-channel {{ old('vers', 'Offline') === 'Offline' ? 'active' : 'bg-light text-muted' }}"
                                          data-channel="Offline" onclick="selectChannel('Offline')">
                                        <i class="mdi mdi-store-marker-outline"></i> Offline
                                    </span>
                                    <span class="badge-pill-channel {{ old('vers') === 'Online' ? 'active' : 'bg-light text-muted' }}"
                                          data-channel="Online" onclick="selectChannel('Online')">
                                        <i class="mdi mdi-web"></i> Online
                                    </span>
                                </div>
                                <div class="form-text small">Pilih apakah pengiriman ditangani langsung via offline / online.</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Card Right: Informasi Klien & Tujuan Pengiriman --}}
            <div class="col-12 col-lg-6">
                <div class="card po-project-card h-100 shadow-sm">
                    <div class="card-header border-bottom py-3 d-flex align-items-center justify-content-between">
                        <div class="section-header-title text-primary">
                            <i class="mdi mdi-office-building-marker-outline fs-5"></i>
                            <span>Tujuan Pengiriman &amp; Penerima</span>
                        </div>
                        <span class="badge bg-label-info font-monospace">Klien</span>
                    </div>
                    <div class="card-body pt-4">
                        {{-- Quick Info Badges --}}
                        <div class="p-2 mb-3 rounded bg-light-subtle border d-flex flex-wrap gap-3 align-items-center small">
                            <div>
                                <span class="text-muted d-block" style="font-size: 11px;">Perusahaan Klien:</span>
                                <strong class="text-dark">{{ $clientName ?: '-' }}</strong>
                            </div>
                            @if(!empty($clientPic))
                            <div class="border-start ps-3">
                                <span class="text-muted d-block" style="font-size: 11px;">PIC Penerima:</span>
                                <strong>{{ $clientPic }}</strong> {{ $clientPhone ? "($clientPhone)" : "" }}
                            </div>
                            @endif
                        </div>

                        {{-- Editable Textarea Detail Customers --}}
                        <label class="form-label fw-bold small text-uppercase text-muted" for="detailClient">
                            Detail Klien &amp; Alamat Tujuan Pengiriman <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control @error('detail_client') is-invalid @enderror"
                                  id="detailClient" name="detail_client" rows="4"
                                  placeholder="Tuliskan nama perusahaan klien, PIC, kontak telepon, dan alamat lengkap tujuan pengiriman..."
                                  required>{{ old('detail_client', $detailClientFormatted ?? '') }}</textarea>
                        <div class="form-text small text-muted mt-1">
                            <i class="mdi mdi-information-outline me-1"></i>Informasi di atas dicetak langsung pada lembar Bukti Barang Keluar &amp; Surat Jalan.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Row 2: Tabel Item Pengeluaran Barang Project --}}
        <div class="card po-project-card shadow-sm mb-4">
            <div class="card-header border-bottom py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div class="section-header-title text-primary">
                    <i class="mdi mdi-package-variant-closed fs-5"></i>
                    <span>Daftar Item &amp; Alokasi Barang Keluar</span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-label-primary rounded-pill px-3 py-1" id="itemCountBadge">
                        {{ count($itemsData) }} Item Tercatat
                    </span>
                </div>
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover table-items m-0 align-middle">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 45px;">#</th>
                            <th style="min-width: 300px;">Deskripsi Item &amp; Spesifikasi</th>
                            <th style="min-width: 200px;">Part / Replacement</th>
                            <th style="width: 120px;">Gudang</th>
                            <th class="text-center" style="width: 90px;">Qty</th>
                            @if(!$isLogistic)
                                <th class="text-end" style="min-width: 160px;">Harga Satuan</th>
                                <th class="text-end" style="min-width: 160px;">Subtotal</th>
                            @endif
                            <th class="text-center" style="width: 60px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="itemsTableBody">
                        @forelse($itemsData as $index => $item)
                            @php
                                $rowId = $index + 1;
                                $isNonInv = $item['is_non_inventory'];
                            @endphp
                            <tr class="item-row" data-row="{{ $rowId }}">
                                {{-- Index --}}
                                <td class="text-center fw-bold text-muted row-index-display">
                                    {{ $rowId }}
                                </td>

                                {{-- Description & Item Info --}}
                                <td>
                                    <div class="d-flex flex-column">
                                        <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                                            <span class="fw-bold text-dark fs-6 item-title-name">{{ $item['name'] }}</span>
                                            @if($isNonInv)
                                                <span class="badge bg-label-info py-0 px-2" style="font-size: 11px;">
                                                    <i class="mdi mdi-certificate-outline me-1"></i>Unit / Project
                                                </span>
                                            @else
                                                <span class="badge bg-label-primary py-0 px-2" style="font-size: 11px;">
                                                    PN: {{ $item['equiv']?->pn ?? '-' }}
                                                </span>
                                            @endif
                                            <span class="badge bg-label-secondary font-11 py-0 px-1.5">Order: {{ $item['qty_ordered'] ?? $item['qty'] }}</span>
                                            @if(($item['qty_shipped'] ?? 0) > 0)
                                                <span class="badge bg-label-info font-11 py-0 px-1.5">Terkirim: {{ $item['qty_shipped'] }}</span>
                                                <span class="badge bg-label-warning font-11 py-0 px-1.5">Sisa: {{ $item['qty_remaining'] ?? $item['qty'] }}</span>
                                            @endif
                                        </div>

                                        @if(!empty($item['description']))
                                            <div class="text-muted small lh-sm p-2 rounded bg-light-subtle border"
                                                 style="white-space: pre-wrap; font-size: 12px; max-width: 480px;">{{ $item['description'] }}</div>
                                        @endif
                                        
                                        @if(!empty($item['dPending']->note) && $item['dPending']->note !== $item['name'])
                                            <span class="text-muted small mt-1">
                                                <i class="mdi mdi-note-outline me-1"></i>Catatan: {{ $item['dPending']->note }}
                                            </span>
                                        @endif
                                    </div>
                                </td>

                                {{-- Replacement / Part Dropdown with All SKU Listing --}}
                                <td>
                                    <div class="replacement-select-container">
                                        <input type="hidden" name="equivalent[]" class="item-equivalent-input" id="equivalent-input-{{ $rowId }}"
                                               value="{{ $item['equiv']?->id ?? $item['default_serial_id'] ?? 0 }}">
                                        
                                        <select class="form-select form-select-sm select2-replacement-ajax"
                                                name="replacement[]" data-row="{{ $rowId }}" style="width: 100%;">
                                            @if($item['default_rep_id'])
                                                <option value="{{ $item['default_rep_id'] }}"
                                                        data-serial="{{ $item['default_serial_id'] }}"
                                                        data-stock="{{ $item['default_stock'] }}"
                                                        data-warehouse-stock="{{ $item['default_wh_stock'] }}"
                                                        selected>
                                                    {{ $item['default_rep_text'] }}
                                                </option>
                                                <option value="0">-- Non-Inventory / Ready Stock (Tanpa SKU Fisik) --</option>
                                            @else
                                                <option value="0" selected>-- Non-Inventory / Ready Stock (Tanpa SKU Fisik) --</option>
                                            @endif
                                        </select>

                                        <div class="stock-info-label small mt-1" id="stock-info-{{ $rowId }}">
                                            @if($item['default_rep_id'])
                                                <span class="badge bg-label-info py-0">Stok: BDG {{ $item['default_stock'] }} | BKS {{ $item['default_wh_stock'] }}</span>
                                            @else
                                                <span class="badge bg-label-secondary py-0">Non-Inventory / Ready Stock</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                {{-- Warehouse BDG / BKS --}}
                                <td>
                                    <select class="form-select form-select-sm fw-semibold" name="warehouse[]">
                                        <option value="BDG" {{ ($item['dPending']->bdg ?? 0) >= ($item['dPending']->bks ?? 0) ? 'selected' : '' }}>BDG (Bandung)</option>
                                        <option value="BKS" {{ ($item['dPending']->bks ?? 0) > ($item['dPending']->bdg ?? 0) ? 'selected' : '' }}>BKS (Bekasi)</option>
                                    </select>
                                </td>

                                {{-- Qty --}}
                                <td class="text-center">
                                    <input type="number" step="any" min="1" max="{{ $item['qty_remaining'] ?? $item['qty'] }}"
                                           class="form-control form-control-sm text-center fw-bold item-qty-input"
                                           name="qty[]" data-row="{{ $rowId }}"
                                           value="{{ (int)$item['qty'] ?: 1 }}"
                                           style="width: 85px; margin: 0 auto;" required>
                                    @if($isLogistic)
                                        {{-- Hidden inputs for Logistic so price & amount are submitted to backend and saved --}}
                                        <input type="hidden" name="price[]" class="item-price-raw" id="price-raw-{{ $rowId }}"
                                               data-row="{{ $rowId }}" value="{{ (int)$item['price'] }}">
                                        <input type="hidden" name="amount[]" class="item-amount-raw" id="amount-raw-{{ $rowId }}"
                                               value="{{ (int)$item['amount'] }}">
                                    @endif
                                </td>

                                @if(!$isLogistic)
                                {{-- Price --}}
                                <td class="text-end">
                                    <div class="input-group input-group-sm justify-content-end">
                                        <span class="input-group-text bg-light-subtle">Rp</span>
                                        <input type="text" class="form-control form-control-sm text-end fw-semibold item-price-display"
                                               data-row="{{ $rowId }}"
                                               value="{{ number_format($item['price'], 0, ',', '.') }}">
                                        <input type="hidden" name="price[]" class="item-price-raw" id="price-raw-{{ $rowId }}"
                                               data-row="{{ $rowId }}" value="{{ (int)$item['price'] }}">
                                    </div>
                                </td>

                                {{-- Amount --}}
                                <td class="text-end fw-bold fs-6 text-dark">
                                    <span class="item-amount-display" id="amount-display-{{ $rowId }}">
                                        Rp {{ number_format($item['amount'], 0, ',', '.') }}
                                    </span>
                                    <input type="hidden" name="amount[]" class="item-amount-raw" id="amount-raw-{{ $rowId }}"
                                           value="{{ (int)$item['amount'] }}">
                                </td>
                                @endif

                                {{-- Action Delete --}}
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-icon btn-outline-danger btn-delete-item"
                                            data-bs-toggle="tooltip" data-bs-placement="top" title="Hapus Item dari Pengeluaran Ini">
                                        <i class="mdi mdi-trash-can-outline fs-5"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr id="emptyInitialRow">
                                <td colspan="{{ $isLogistic ? 6 : 8 }}" class="text-center py-4 text-muted">
                                    <i class="mdi mdi-alert-circle-outline fs-3 d-block mb-1 text-warning"></i>
                                    Tidak ada item aktif pada Pending PO ini.
                                </td>
                            </tr>
                        @endforelse
                        <tr id="emptyItemsRow" style="display: none;">
                            <td colspan="{{ $isLogistic ? 6 : 8 }}" class="text-center py-4 text-muted">
                                <i class="mdi mdi-alert-circle-outline fs-3 d-block mb-1 text-warning"></i>
                                <span>Semua item telah dihapus dari formulir ini.</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Row 3: Biaya Logistik, Ringkasan Finansial, dan Catatan --}}
        <div class="row g-4 mb-4">
            {{-- Kolom Catatan Logistik Khusus --}}
            <div class="col-12 {{ $isLogistic ? 'col-lg-12' : 'col-lg-7' }}">
                <div class="card po-project-card h-100 shadow-sm">
                    <div class="card-header border-bottom py-3 d-flex align-items-center justify-content-between">
                        <div class="section-header-title text-primary">
                            <i class="mdi mdi-note-edit-outline fs-5"></i>
                            <span>Catatan Tambahan Pengeluaran Barang</span>
                        </div>
                        <span class="badge bg-label-secondary font-monospace">Opsional</span>
                    </div>
                    <div class="card-body pt-3">
                        <div class="mb-3">
                            <label class="form-label text-muted small fw-bold text-uppercase" for="noteTextarea">
                                Instruksi Kurir / Catatan Logistik <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control @error('note') is-invalid @enderror"
                                      id="noteTextarea" name="note" rows="4"
                                      placeholder="Contoh: Pengiriman menggunakan armada Reftech / Disertakan sertifikat uji / Kontak supir: Pak Joko...">{{ old('note', '-') }}</textarea>
                            <div class="form-text small">Catatan ini akan tercetak pada laporan pengeluaran barang.</div>
                        </div>

                        {{-- Quick Fill Chips --}}
                        <div class="d-flex align-items-center gap-2 flex-wrap small">
                            <span class="text-muted me-1" style="font-size: 11px;">Template Cepat:</span>
                            <span class="badge bg-label-secondary quick-template-badge" onclick="appendNote('Pengiriman menggunakan armada internal Reftech.')">
                                + Armada Internal
                            </span>
                            <span class="badge bg-label-secondary quick-template-badge" onclick="appendNote('Unit rental siap dan telah lolos uji inspeksi fungsi (PDI).')">
                                + Lolos PDI / QC
                            </span>
                            <span class="badge bg-label-secondary quick-template-badge" onclick="appendNote('Pengiriman barang via ekspedisi rekanan.')">
                                + Ekspedisi Rekanan
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            @if(!$isLogistic)
            {{-- Kolom Kanan: Ringkasan Finansial --}}
            <div class="col-12 col-lg-5">
                <div class="card financial-summary-card shadow-sm h-100">
                    <div class="card-header border-bottom py-3 d-flex align-items-center justify-content-between">
                        <div class="section-header-title text-primary">
                            <i class="mdi mdi-calculator fs-5"></i>
                            <span>Ringkasan Nilai Transaksi</span>
                        </div>
                        <span class="badge bg-label-success">Kalkulasi Otomatis</span>
                    </div>
                    <div class="card-body pt-3">
                        {{-- Subtotal Items --}}
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="text-muted">Subtotal Barang / Jasa:</span>
                            <span class="fw-bold text-dark fs-6" id="summarySubtotalDisplay">
                                Rp {{ number_format($subtotal, 0, ',', '.') }}
                            </span>
                        </div>

                        {{-- Biaya Pengiriman (Shipping) --}}
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="form-label text-muted mb-0 small fw-semibold" for="shippingInput">
                                    Ongkos Kirim (Shipping):
                                </label>
                                <span class="badge bg-label-info py-0 px-2" style="font-size: 10px;">Dapat diedit</span>
                            </div>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text bg-light-subtle">Rp</span>
                                <input type="text" class="form-control text-end fw-bold @error('shipping') is-invalid @enderror"
                                       id="shippingDisplayInput"
                                       value="{{ number_format(old('shipping', $shipping ?? 0), 0, ',', '.') }}"
                                       placeholder="0">
                                <input type="hidden" name="shipping" id="shippingRawInput"
                                       value="{{ (int)old('shipping', $shipping ?? 0) }}">
                            </div>
                        </div>

                        {{-- Tax Info if available --}}
                        @if(!empty($pending->unitQuotation?->tax_amount) && $pending->unitQuotation->tax_amount > 0)
                            <div class="d-flex justify-content-between align-items-center mb-2 small text-muted">
                                <span>PPN Terhitung (Quotation):</span>
                                <span>Rp {{ number_format($pending->unitQuotation->tax_amount, 0, ',', '.') }}</span>
                            </div>
                        @endif

                        <hr class="my-3">

                        {{-- Grand Total --}}
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <div>
                                <span class="fw-bold text-dark fs-6 d-block">Grand Total Transaksi</span>
                                <small class="text-muted" style="font-size: 11px;">Termasuk biaya barang &amp; ongkir</small>
                            </div>
                            <div class="text-end">
                                <span class="fw-extrabold text-primary fs-4" id="summaryGrandTotalDisplay">
                                    Rp {{ number_format($grandTotal, 0, ',', '.') }}
                                </span>
                                <input type="hidden" name="total" id="grandTotalRawInput" value="{{ (int)$grandTotal }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @else
                {{-- Hidden inputs for Logistic so shipping and total are still passed to backend and recorded --}}
                <input type="hidden" name="shipping" id="shippingRawInput" value="{{ (int)old('shipping', $shipping ?? 0) }}">
                <input type="hidden" name="total" id="grandTotalRawInput" value="{{ (int)$grandTotal }}">
            @endif
        </div>

        {{-- Sticky Bottom Action Bar --}}
        <div class="sticky-action-bar d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="avatar avatar-md bg-label-primary rounded-3 d-flex align-items-center justify-content-center">
                    <i class="mdi mdi-cube-send fs-3"></i>
                </div>
                <div>
                    @if(!$isLogistic)
                        <span class="text-muted small d-block">Total Nilai Pengeluaran Barang:</span>
                        <strong class="fs-5 text-dark" id="stickyTotalPreview">
                            Rp {{ number_format($grandTotal, 0, ',', '.') }}
                        </strong>
                        <span class="text-muted small ms-1" id="stickyItemCount">({{ count($itemsData) }} Item)</span>
                    @else
                        <span class="text-muted small d-block">Alokasi Pengeluaran Barang:</span>
                        <strong class="fs-5 text-primary" id="stickyLogisticItemCount">
                            {{ count($itemsData) }} Item Siap Kirim
                        </strong>
                    @endif
                </div>
            </div>
            
            <div class="d-flex align-items-center gap-2">
                <a href="{{ url()->previous() != url()->current() ? url()->previous() : route('pending-po.show', $pending->id) }}"
                   class="btn btn-outline-secondary px-3">
                    <i class="mdi mdi-close me-1"></i> Batal
                </a>
                <button type="button" id="btnSubmitForm" class="btn btn-primary px-4 d-flex align-items-center gap-2 shadow-sm">
                    <i class="mdi mdi-check-circle-outline fs-5"></i>
                    <span class="fw-bold">Simpan &amp; Terbitkan Barang Keluar</span>
                </button>
            </div>
        </div>

    </form>
</div>
@endsection

@push('after-script')
<script src="{{ asset('assets') }}/vendor/libs/select2/select2.js"></script>
<script>
    // Channel selector pill
    function selectChannel(channel) {
        $('#inputChannelVers').val(channel);
        $('.badge-pill-channel').removeClass('active').addClass('bg-light text-muted');
        $(`.badge-pill-channel[data-channel="${channel}"]`).addClass('active').removeClass('bg-light text-muted');
    }

    // Quick fill for notes textarea
    function appendNote(text) {
        var current = $('#noteTextarea').val().trim();
        if (current === '' || current === '-') {
            $('#noteTextarea').val(text);
        } else {
            $('#noteTextarea').val(current + ' ' + text);
        }
    }

    $(document).ready(function() {
        // Initialize tooltips
        if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        }

        // Initialize Select2 with AJAX for All SKU / Replacements
        function initSelect2Replacement($select) {
            $select.select2({
                width: '100%',
                placeholder: "Ketik untuk cari semua SKU / Replacement...",
                allowClear: false,
                ajax: {
                    url: '{{ route("pending-po.replacements.search") }}',
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            q: params.term || ''
                        };
                    },
                    processResults: function (data) {
                        var results = [];
                        // Always include Non-Inventory option at top
                        results.push({
                            id: 0,
                            text: '-- Non-Inventory / Ready Stock (Tanpa SKU Fisik) --',
                            replacement: 'Non-Inventory / Ready Stock',
                            commodity: 'Tanpa SKU Fisik',
                            stock: 0,
                            warehouse_stock: 0,
                            serial_id: 0,
                            go: 'Unit/Service'
                        });
                        $.each(data, function(idx, item) {
                            results.push({
                                id: item.id,
                                text: item.text,
                                replacement: item.replacement,
                                commodity: item.commodity,
                                stock: item.stock,
                                warehouse_stock: item.warehouse_stock,
                                serial_id: item.serial_id,
                                go: item.go
                            });
                        });
                        return { results: results };
                    },
                    cache: true
                },
                templateResult: function (item) {
                    if (!item.id && item.id !== 0) return item.text;
                    if (item.id == 0 || item.id == '0') {
                        return $('<div class="py-1 text-muted"><i class="mdi mdi-check-circle-outline text-success me-1"></i><strong>Non-Inventory / Ready Stock</strong> (Tanpa SKU Fisik)</div>');
                    }
                    return $(
                        '<div class="py-1">' +
                            '<div class="fw-bold text-dark d-flex align-items-center justify-content-between">' +
                                '<span>' + (item.replacement || item.text) + '</span>' +
                                '<span class="badge bg-label-' + (item.go === 'G' ? 'success' : 'primary') + ' ms-2">' + (item.go || '') + '</span>' +
                            '</div>' +
                            '<div class="text-muted small" style="font-size: 11px;">' + (item.commodity || '') + ' &bull; <strong class="text-primary">BDG: ' + (item.stock || 0) + ' | BKS: ' + (item.warehouse_stock || 0) + '</strong></div>' +
                        '</div>'
                    );
                },
                templateSelection: function (item) {
                    if (item.id == 0 || item.id == '0') {
                        return 'Non-Inventory / Ready Stock (Tanpa SKU Fisik)';
                    }
                    return item.replacement ? (item.replacement + ' (' + item.commodity + ')') : item.text;
                }
            }).on('select2:select', function(e) {
                var data = e.params.data;
                var row = $(this).data('row');
                if (data.id == 0 || data.id == '0') {
                    $(`#equivalent-input-${row}`).val(0);
                    $(`#stock-info-${row}`).html('<span class="badge bg-label-secondary py-0">Non-Inventory / Ready Stock</span>');
                } else {
                    $(`#equivalent-input-${row}`).val(data.serial_id || 0);
                    $(`#stock-info-${row}`).html('<span class="badge bg-label-info py-0">Stok: BDG ' + (data.stock || 0) + ' | BKS ' + (data.warehouse_stock || 0) + '</span>');
                }
            });
        }

        // Initialize all replacement select2 on page load
        $('.select2-replacement-ajax').each(function() {
            initSelect2Replacement($(this));
        });

        // Helper format number to Indonesian thousand separator
        function formatRupiah(num) {
            return new Intl.NumberFormat('id-ID').format(Math.round(num));
        }

        function parseCurrency(str) {
            if (!str) return 0;
            var clean = str.toString().replace(/[^0-9]/g, '');
            return parseInt(clean, 10) || 0;
        }

        // Reindex row numbers and item count badges
        function reindexRows() {
            var totalRows = $('.item-row').length;
            $('#itemCountBadge').text(totalRows + ' Item Tercatat');
            $('#stickyItemCount').text('(' + totalRows + ' Item)');
            $('#stickyLogisticItemCount').text(totalRows + ' Item Siap Kirim');
            
            if (totalRows === 0) {
                $('#emptyItemsRow').show();
            } else {
                $('#emptyItemsRow').hide();
                $('.item-row').each(function(idx) {
                    var newIndex = idx + 1;
                    $(this).attr('data-row', newIndex);
                    $(this).find('.row-index-display').text(newIndex);
                    $(this).find('.select2-replacement-ajax').attr('data-row', newIndex);
                    $(this).find('.item-equivalent-input').attr('id', 'equivalent-input-' + newIndex);
                    $(this).find('.item-qty-input').attr('data-row', newIndex);
                    $(this).find('.item-price-display').attr('data-row', newIndex);
                    $(this).find('.item-price-raw').attr('id', 'price-raw-' + newIndex).attr('data-row', newIndex);
                    $(this).find('.item-amount-display').attr('id', 'amount-display-' + newIndex);
                    $(this).find('.item-amount-raw').attr('id', 'amount-raw-' + newIndex);
                    $(this).find('.stock-info-label').attr('id', 'stock-info-' + newIndex);
                });
            }
        }

        // Live calculation across rows
        function recalculateAll() {
            var subtotal = 0;

            $('.item-row').each(function() {
                var row = $(this).data('row');
                var qty = parseFloat($(this).find('.item-qty-input').val()) || 0;
                var price = parseFloat($(this).find('.item-price-raw').val()) || 0;
                var amount = Math.round(qty * price);

                $(this).find('.item-amount-raw').val(amount);
                $(`#amount-display-${row}`).text('Rp ' + formatRupiah(amount));
                subtotal += amount;
            });

            var shipping = parseCurrency($('#shippingRawInput').val());
            var grandTotal = subtotal + shipping;

            // Update displays
            $('#summarySubtotalDisplay').text('Rp ' + formatRupiah(subtotal));
            $('#summaryGrandTotalDisplay').text('Rp ' + formatRupiah(grandTotal));
            $('#stickyTotalPreview').text('Rp ' + formatRupiah(grandTotal));
            $('#grandTotalRawInput').val(grandTotal);
        }

        // Event listener for deleting item rows
        $(document).on('click', '.btn-delete-item', function() {
            var row = $(this).closest('tr.item-row');
            var itemName = row.find('.item-title-name').text().trim() || 'item ini';

            Swal.fire({
                title: 'Hapus Item?',
                html: `Apakah Anda yakin ingin menghapus <strong class="text-danger">${itemName}</strong> dari daftar pengeluaran barang?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: '<i class="mdi mdi-trash-can-outline me-1"></i> Ya, Hapus',
                cancelButtonText: 'Batal',
                customClass: {
                    confirmButton: 'btn btn-danger me-2',
                    cancelButton: 'btn btn-outline-secondary'
                },
                buttonsStyling: false
            }).then(function(result) {
                if (result.isConfirmed) {
                    row.fadeOut(200, function() {
                        $(this).remove();
                        reindexRows();
                        recalculateAll();
                    });
                }
            });
        });

        // Event listener for Qty change
        $(document).on('input change', '.item-qty-input', function() {
            recalculateAll();
        });

        // Event listener for Price change (if user edits formatted price)
        $(document).on('input blur', '.item-price-display', function() {
            var row = $(this).data('row');
            var rawVal = parseCurrency($(this).val());
            $(this).val(formatRupiah(rawVal));
            $(`#price-raw-${row}`).val(rawVal);
            recalculateAll();
        });

        // Event listener for Shipping change
        $('#shippingDisplayInput').on('input blur', function() {
            var rawVal = parseCurrency($(this).val());
            $(this).val(formatRupiah(rawVal));
            $('#shippingRawInput').val(rawVal);
            recalculateAll();
        });

        // Confirmation before submit using SweetAlert2
        $('#btnSubmitForm').on('click', function(e) {
            e.preventDefault();

            // Validate that at least 1 item is present
            if ($('.item-row').length === 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Tidak Ada Item',
                    text: 'Minimal harus ada 1 item pada daftar untuk menerbitkan barang keluar.',
                    customClass: {
                        confirmButton: 'btn btn-primary'
                    },
                    buttonsStyling: false
                });
                return;
            }

            // Check form HTML5 validity
            var form = document.getElementById('formProductOutProject');
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            var isLogistic = {{ $isLogistic ? 'true' : 'false' }};
            var invoice = $('#inputInvoice').val().trim();
            var noProductOut = $('#inputNoProductOut').val() ? $('#inputNoProductOut').val().trim() : '';
            var totalStr = $('#summaryGrandTotalDisplay').text();
            var itemCount = $('.item-row').length;

            var confirmHtml = isLogistic
                ? `Konfirmasi pengeluaran barang project:<br>No. BK: <strong class="text-primary font-monospace fs-6">${noProductOut}</strong><br>Invoice: <strong>${invoice}</strong><br>Jumlah: <strong>${itemCount} Item Siap Dikirim</strong>`
                : `Konfirmasi pengeluaran barang project:<br>No. BK: <strong class="text-primary font-monospace fs-6">${noProductOut}</strong><br>Invoice: <strong>${invoice}</strong><br>Jumlah: <strong>${itemCount} Item</strong> • Total: <strong>${totalStr}</strong>`;

            Swal.fire({
                title: 'Terbitkan Barang Keluar?',
                html: confirmHtml,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: '<i class="mdi mdi-check me-1"></i> Ya, Simpan Sekarang',
                cancelButtonText: 'Periksa Kembali',
                customClass: {
                    confirmButton: 'btn btn-primary me-2',
                    cancelButton: 'btn btn-outline-secondary'
                },
                buttonsStyling: false
            }).then(function(result) {
                if (result.isConfirmed) {
                    $('#btnSubmitForm').prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Menyimpan...');
                    form.submit();
                }
            });
        });
    });
</script>
@endpush
