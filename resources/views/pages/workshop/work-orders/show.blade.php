@extends('layouts.sales.app')
@section('title', 'Detail Work Order ' . $wo->no_wo . ' - Reftech')

@section('content')
<div class="container-fluid px-3 px-md-4 py-3 flex-grow-1">
    @php
        $badge = $wo->status_badge;
        $userRole = Auth::user()?->role;
        $isWarehouse = in_array($userRole, ['Developer', 'Admin', 'Super Admin', 'Logistic', 'Warehouse']);
        $isAccounting = in_array($userRole, ['Developer', 'Admin', 'Super Admin', 'Accounting', 'Finance', 'Finance Manager']);
        $canSeePrice = in_array($userRole, ['Developer', 'Admin', 'Super Admin', 'Accounting', 'Finance', 'Finance Manager']);
    @endphp

    {{-- Header Action Bar --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center py-3 mb-3 gap-3">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 fs-6">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('work-orders.index') }}">Work Order</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $wo->no_wo }}</li>
                </ol>
            </nav>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <h4 class="fw-bold mb-0 text-dark">{{ $wo->no_wo }}</h4>
                <span class="badge {{ $badge['class'] }} fs-6 d-inline-flex align-items-center gap-1">
                    <i class="mdi {{ $badge['icon'] }}" style="font-size: 14px;"></i>
                    {{ $badge['text'] }}
                </span>
                @if ($wo->accounting_treatment)
                    <span class="badge {{ $wo->accounting_treatment === 'capitalize' ? 'bg-label-success' : 'bg-label-info' }} fs-6">
                        <i class="mdi {{ $wo->accounting_treatment === 'capitalize' ? 'mdi-cash-plus' : 'mdi-calculator' }} me-1"></i>
                        {{ $wo->accounting_treatment === 'capitalize' ? 'Kapitalisasi Nilai Aset' : 'Beban Pemeliharaan (Expense)' }}
                    </span>
                @endif
            </div>
        </div>

        {{-- Dynamic Action Buttons --}}
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <a href="{{ route('work-orders.index') }}" class="btn btn-outline-secondary btn-sm shadow-xs">
                <i class="mdi mdi-arrow-left me-1"></i> Kembali
            </a>

            {{-- Aksi Gudang: Saat pending_warehouse atau waiting_pr --}}
            @if ($isWarehouse && in_array($wo->status, ['pending_warehouse', 'waiting_pr']))
                <button type="button" class="btn btn-warning btn-sm shadow-xs" data-bs-toggle="modal" data-bs-target="#modalGeneratePr">
                    <i class="mdi mdi-cart-plus me-1"></i> Buat Purchase Request (PR)
                </button>
                <button type="button" class="btn btn-primary btn-sm shadow-xs" data-bs-toggle="modal" data-bs-target="#modalVerifyWarehouse">
                    <i class="mdi mdi-check-bold me-1"></i> Verifikasi &amp; Teruskan ke Accounting
                </button>
            @endif

            {{-- Aksi Accounting: Saat pending_accounting --}}
            @if ($isAccounting && $wo->status === 'pending_accounting')
                <button type="button" class="btn btn-danger btn-sm shadow-xs" data-bs-toggle="modal" data-bs-target="#modalReject">
                    <i class="mdi mdi-close-circle-outline me-1"></i> Tolak WO
                </button>
                <button type="button" class="btn btn-success btn-sm shadow-xs" data-bs-toggle="modal" data-bs-target="#modalApproveAccounting">
                    <i class="mdi mdi-shield-check me-1"></i> Setujui Work Order (Approve)
                </button>
            @endif

            {{-- Aksi Gudang: Saat approved (Siap Keluar) --}}
            @if ($isWarehouse && $wo->status === 'approved')
                <button type="button" class="btn btn-success btn-sm shadow-xs px-3" data-bs-toggle="modal" data-bs-target="#modalIssueItems">
                    <i class="mdi mdi-truck-fast-outline me-1"></i> Buat Surat Jalan &amp; Keluarkan Barang
                </button>
            @endif

            {{-- Jika sudah issued / selesai --}}
            @if ($wo->status === 'issued' && $wo->productOut)
                <a href="{{ route('work-orders.print-sj', $wo->id) }}" target="_blank" class="btn btn-primary btn-sm shadow-xs">
                    <i class="mdi mdi-printer-outline me-1"></i> Cetak Surat Jalan (A4)
                </a>
                <a href="{{ route('product-out.show', $wo->id_product_out) }}" class="btn btn-label-info btn-sm shadow-xs">
                    <i class="mdi mdi-file-document-box-outline me-1"></i> Bukti Keluar ({{ $wo->productOut->no_product_out }})
                </a>
            @endif
        </div>
    </div>

    {{-- Alert Messages --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="mdi mdi-check-circle me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="mdi mdi-alert-circle me-1"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Interactive Stepper (Lifecycle) --}}
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body p-3 p-md-4">
            <div class="d-flex justify-content-between align-items-center position-relative flex-wrap gap-3">
                @php
                    $step1 = true;
                    $step2 = in_array($wo->status, ['waiting_pr', 'pending_accounting', 'approved', 'issued']);
                    $step3 = in_array($wo->status, ['approved', 'issued']);
                    $step4 = $wo->status === 'issued';
                    $isRejected = $wo->status === 'rejected';
                @endphp

                {{-- Step 1 --}}
                <div class="d-flex align-items-center gap-2">
                    <div class="avatar avatar-sm {{ $step1 ? 'bg-primary text-white' : 'bg-label-secondary' }} rounded-circle d-flex align-items-center justify-content-center fw-bold">
                        1
                    </div>
                    <div>
                        <span class="fw-bold d-block text-dark small">Pengajuan ServiceM</span>
                        <small class="text-muted" style="font-size: 11px;">{{ $wo->creator->name ?? 'Pemohon' }} ({{ $wo->date ? $wo->date->format('d/m/Y') : '-' }})</small>
                    </div>
                </div>

                <i class="mdi mdi-arrow-right text-muted d-none d-md-inline"></i>

                {{-- Step 2 --}}
                <div class="d-flex align-items-center gap-2">
                    <div class="avatar avatar-sm {{ $step2 ? 'bg-primary text-white' : ($wo->status === 'pending_warehouse' ? 'bg-warning text-white' : 'bg-label-secondary') }} rounded-circle d-flex align-items-center justify-content-center fw-bold">
                        2
                    </div>
                    <div>
                        <span class="fw-bold d-block text-dark small">Verifikasi Gudang / PR</span>
                        <small class="text-muted" style="font-size: 11px;">
                            @if ($wo->status === 'waiting_pr')
                                <span class="text-info fw-semibold">PR Aktif</span>
                            @elseif ($wo->warehouseUser)
                                {{ $wo->warehouseUser->name }} (OK)
                            @else
                                Menunggu Pengecekan
                            @endif
                        </small>
                    </div>
                </div>

                <i class="mdi mdi-arrow-right text-muted d-none d-md-inline"></i>

                {{-- Step 3 --}}
                <div class="d-flex align-items-center gap-2">
                    <div class="avatar avatar-sm {{ $step3 ? 'bg-primary text-white' : ($wo->status === 'pending_accounting' ? 'bg-warning text-white' : 'bg-label-secondary') }} rounded-circle d-flex align-items-center justify-content-center fw-bold">
                        3
                    </div>
                    <div>
                        <span class="fw-bold d-block text-dark small">Approval Accounting</span>
                        <small class="text-muted" style="font-size: 11px;">
                            @if ($wo->accountingUser)
                                {{ $wo->accountingUser->name }} (Approved)
                            @elseif ($wo->status === 'pending_accounting')
                                <span class="text-primary fw-semibold">Menunggu Persetujuan</span>
                            @else
                                Otorisasi Biaya
                            @endif
                        </small>
                    </div>
                </div>

                <i class="mdi mdi-arrow-right text-muted d-none d-md-inline"></i>

                {{-- Step 4 --}}
                <div class="d-flex align-items-center gap-2">
                    <div class="avatar avatar-sm {{ $step4 ? 'bg-success text-white' : ($wo->status === 'approved' ? 'bg-warning text-white' : 'bg-label-secondary') }} rounded-circle d-flex align-items-center justify-content-center fw-bold">
                        4
                    </div>
                    <div>
                        <span class="fw-bold d-block text-dark small">Pengeluaran Barang (Issued)</span>
                        <small class="text-muted" style="font-size: 11px;">
                            @if ($wo->status === 'issued')
                                <span class="text-success fw-semibold">Selesai &amp; Terpotong</span>
                            @elseif ($wo->status === 'approved')
                                <span class="text-warning fw-semibold">Siap Diambil</span>
                            @else
                                Pemotongan Stok
                            @endif
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Rejection Box if Rejected --}}
    @if ($wo->status === 'rejected')
        <div class="alert alert-danger d-flex align-items-center mb-4" role="alert">
            <i class="mdi mdi-close-circle-outline fs-3 me-3"></i>
            <div>
                <h6 class="alert-heading fw-bold mb-1">Work Order Ditolak</h6>
                <p class="mb-0">Alasan Penolakan: <strong>{{ $wo->rejected_reason ?: 'Tidak ada catatan alasan.' }}</strong></p>
            </div>
        </div>
    @endif

    {{-- Linked PR Banner if available --}}
    @if ($wo->purchaseRequest)
        <div class="alert alert-info d-flex justify-content-between align-items-center mb-4" role="alert">
            <div class="d-flex align-items-center gap-2">
                <i class="mdi mdi-cart-clock fs-3"></i>
                <div>
                    <h6 class="alert-heading fw-bold mb-0">Terhubung dengan Purchase Request (PR)</h6>
                    <small>No. PR: <strong>{{ $wo->purchaseRequest->no_pr }}</strong> — Status PR: {{ $wo->purchaseRequest->status == '1' ? 'Disetujui (Siap PO/GR)' : 'Menunggu' }}</small>
                </div>
            </div>
        </div>
    @endif

    {{-- Surat Jalan Terbit Banner --}}
    @if ($wo->status === 'issued' && $wo->productOut)
        <div class="card border-0 shadow-sm mb-4" style="background: linear-gradient(135deg, #1e293b 0%, #334155 100%); color: #fff; border-radius: 14px;">
            <div class="card-body p-4">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="avatar avatar-lg rounded-3 bg-primary text-white d-flex align-items-center justify-content-center shadow">
                            <i class="mdi mdi-truck-fast-outline fs-2"></i>
                        </div>
                        <div>
                            <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                                <h5 class="fw-bold text-white mb-0">Surat Jalan Terbit: {{ $wo->productOut->no_product_out }}</h5>
                                <span class="badge bg-success text-white">Stok Gudang Berhasil Dipotong</span>
                            </div>
                            <p class="text-white-50 small mb-0">
                                Dikeluarkan pada <strong>{{ \Carbon\Carbon::parse($wo->productOut->date)->isoFormat('DD MMMM YYYY') }}</strong> oleh <strong>{{ $wo->warehouseUser->name ?? ($wo->productOut->user->name ?? 'Gudang') }}</strong> &bull; Penerima: <strong>{{ $wo->technician->name ?? ($wo->creator->name ?? 'Teknisi Workshop') }}</strong>
                            </p>
                        </div>
                    </div>
                    <div class="d-flex gap-2 flex-wrap">
                        <a href="{{ route('work-orders.print-sj', $wo->id) }}" target="_blank" class="btn btn-primary btn-sm px-3 shadow-sm">
                            <i class="mdi mdi-printer me-1"></i> Cetak Surat Jalan (A4)
                        </a>
                        <a href="{{ route('product-out.show', $wo->id_product_out) }}" class="btn btn-outline-light btn-sm px-3 shadow-sm">
                            <i class="mdi mdi-open-in-new me-1"></i> Lihat Dokumen Barang Keluar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="row g-4 mb-4">
        {{-- Card 1: Data Unit Mesin --}}
        <div class="col-lg-4 col-md-6 col-12">
            <div class="card border-0 shadow-sm rounded-3 h-100 mb-0">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                    <h6 class="card-title mb-0 fw-bold d-flex align-items-center gap-2">
                        <i class="mdi mdi-engine text-primary"></i> 1. Informasi Unit Mesin
                    </h6>
                    @if ($wo->fixedAsset)
                        <a href="{{ route('unit-acquisition.show', $wo->fixedAsset->id) }}" class="small" target="_blank">
                            Lihat Unit <i class="mdi mdi-open-in-new"></i>
                        </a>
                    @endif
                </div>
                <div class="card-body pt-3">
                    @if ($wo->fixedAsset)
                        <div class="mb-3 pb-3 border-bottom">
                            <span class="text-muted small d-block">Kode Aset:</span>
                            <h5 class="fw-bolder text-primary mb-1">{{ $wo->fixedAsset->code }}</h5>
                            <span class="text-dark fw-semibold">{{ $wo->fixedAsset->unit->brand ?? ($wo->fixedAsset->unit_brand ?: ($wo->fixedAsset->desc ?: 'Mesin Kompresor')) }}</span>
                        </div>
                        <div class="row g-2 small">
                            <div class="col-6 mb-2">
                                <span class="text-muted d-block" style="font-size: 11px;">Model / Type:</span>
                                <span class="fw-semibold text-dark">{{ $wo->fixedAsset->unit->model ?? ($wo->fixedAsset->unit->formatted_type ?: '-') }}</span>
                            </div>
                            <div class="col-6 mb-2">
                                <span class="text-muted d-block" style="font-size: 11px;">Serial Number:</span>
                                <span class="fw-semibold text-dark">{{ $wo->fixedAsset->serial_number ?: ($wo->machine->serial ?? '-') }}</span>
                            </div>
                            <div class="col-6">
                                <span class="text-muted d-block" style="font-size: 11px;">Kondisi Saat Ini:</span>
                                <span class="badge bg-label-info">{{ $wo->fixedAsset->kondisi ?: ($wo->fixedAsset->status_unit ?? 'OK') }}</span>
                            </div>
                            @if ($canSeePrice)
                                <div class="col-6">
                                    <span class="text-muted d-block" style="font-size: 11px;">Nilai Pokok Aset:</span>
                                    <span class="fw-semibold text-dark">Rp {{ number_format($wo->fixedAsset->total, 0, ',', '.') }}</span>
                                </div>
                            @endif
                        </div>
                    @else
                        <p class="text-muted small mb-0">Mesin tidak terhubung ke data Fixed Asset.</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Card 2 (Top): Info Pengajuan & Otorisasi --}}
        <div class="col-lg-4 col-md-6 col-12">
            <div class="card border-0 shadow-sm rounded-3 h-100 mb-0">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="card-title mb-0 fw-bold d-flex align-items-center gap-2">
                        <i class="mdi mdi-clipboard-text-clock-outline text-primary"></i> Info Pengajuan &amp; Otorisasi
                    </h6>
                </div>
                <div class="card-body pt-3 small">
                    <div class="mb-2 pb-2 border-bottom">
                        <span class="text-muted d-block" style="font-size: 11px;">Pemohon (ServiceM):</span>
                        <span class="fw-semibold text-dark">{{ $wo->creator->name ?? '-' }}</span>
                        <span class="text-muted">pada {{ $wo->date ? $wo->date->format('d M Y') : '-' }}</span>
                    </div>
                    @if ($wo->target_date)
                        <div class="mb-2 pb-2 border-bottom">
                            <span class="text-muted d-block" style="font-size: 11px;">Est. Pekerjaan:</span>
                            <span class="fw-semibold text-primary"><i class="mdi mdi-calendar-clock me-1"></i>{{ $wo->target_date->format('d M Y') }}</span>
                        </div>
                    @endif
                    <div class="mb-2 pb-2 border-bottom">
                        <span class="text-muted d-block" style="font-size: 11px;">Teknisi Pelaksana:</span>
                        <span class="fw-semibold text-dark">{{ $wo->technician->name ?? 'Belum ditentukan' }}</span>
                    </div>
                    <div class="mb-2 pb-2 border-bottom">
                        <span class="text-muted d-block" style="font-size: 11px;">Verifikator Gudang:</span>
                        <span class="fw-semibold text-dark">{{ $wo->warehouseUser->name ?? 'Menunggu verifikasi' }}</span>
                        @if ($wo->warehouse_note)
                            <div class="p-2 rounded bg-light mt-1 text-muted fst-italic">"{{ $wo->warehouse_note }}"</div>
                        @endif
                    </div>
                    <div class="mb-0">
                        <span class="text-muted d-block" style="font-size: 11px;">Approver Accounting:</span>
                        <span class="fw-semibold text-dark">{{ $wo->accountingUser->name ?? 'Menunggu approval' }}</span>
                        @if ($wo->accounting_note)
                            <div class="p-2 rounded bg-light mt-1 text-muted fst-italic">"{{ $wo->accounting_note }}"</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 3 (Top): Deskripsi Pekerjaan / Keluhan --}}
        <div class="col-lg-4 col-12">
            <div class="card border-0 shadow-sm rounded-3 h-100 mb-0">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="card-title mb-0 fw-bold d-flex align-items-center gap-2">
                        <i class="mdi mdi-file-document-edit-outline text-primary"></i> Deskripsi Pekerjaan / Keluhan
                    </h6>
                </div>
                <div class="card-body pt-3">
                    <p class="mb-0 text-dark small" style="white-space: pre-line;">{{ $wo->description }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Section: Card 2 Fullwidth --}}
    <div class="row">
        <div class="col-12">
            @if ($isWarehouse && in_array($wo->status, ['pending_warehouse', 'waiting_pr']))
                {{-- MODE GUDANG: INTERACTIVE SPARE PART ALLOCATION FORM (Fullwidth) --}}
                <form action="{{ route('work-orders.verify-warehouse', $wo->id) }}" method="POST" id="formWarehouseVerify">
                    @csrf
                    <div class="card border-0 shadow-sm rounded-3 mb-4">
                        <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <div>
                                <h6 class="card-title mb-0 fw-bold d-flex align-items-center gap-2">
                                    <i class="mdi mdi-tools text-primary"></i> 2. Alokasi Suku Cadang &amp; Verifikasi Stok Gudang
                                </h6>
                                <small class="text-muted">Pilih suku cadang dari katalog Product &amp; Replacement gudang, cek ketersediaan stok BDG &amp; BKS.</small>
                            </div>
                            <button type="button" class="btn btn-outline-primary btn-sm px-3 shadow-xs" id="btnAddWarehouseItem">
                                <i class="mdi mdi-plus me-1"></i> Tambah Part Ekstra
                            </button>
                        </div>

                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table align-middle mb-0" id="warehouseItemsTable">
                                    <thead class="table-light text-uppercase" style="font-size: 0.78rem;">
                                        <tr>
                                            <th style="min-width: 210px;">Item Request (ServiceM)</th>
                                            <th style="min-width: 320px;">Part Selection (Katalog &amp; Replacement) <span class="text-danger">*</span></th>
                                            <th style="width: 155px;" class="text-center">Gudang &amp; Stok</th>
                                            <th style="width: 100px;">Qty Setuju</th>
                                            <th style="min-width: 140px;" class="text-center">Status Stok</th>
                                            @if ($canSeePrice)
                                                <th style="width: 155px;" class="text-end">Harga Satuan (HPP)</th>
                                                <th style="width: 150px;" class="text-end">Subtotal</th>
                                            @endif
                                            <th style="width: 45px;" class="text-center"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="warehouseItemsBody">
                                        @foreach ($wo->items as $idx => $item)
                                            @php
                                                $dp = $item->detailProduct;
                                                $p = $dp?->product ?? $item->product;
                                                $ser = $p?->serial?->first();
                                                $brand = $ser?->brand ?? ($p?->brand ?? '');
                                                $pn = $ser?->pn ?? ($p?->pn ?? ($dp?->replacement ?: ''));
                                                $desc = $p?->detail_desc ?: ($p?->description ?: ($p?->commodity ?: ''));
                                                $goRaw = $p?->go ?: 'Genuine';
                                                $goCode = strtoupper(substr(trim($goRaw), 0, 1));
                                                $goText = $goCode === 'R' ? 'Replacement' : 'Genuine';
                                                $price = $item->unit_price > 0 ? (float)$item->unit_price : ($dp?->hpp > 0 ? (float)$dp->hpp : (float)($dp?->modal ?? 0));
                                                $stockBdg = (float) ($dp?->stock ?? 0);
                                                $stockBks = (float) ($dp?->warehouse_stock ?? 0);
                                                $currentStock = $item->warehouse === 'BKS' ? $stockBks : $stockBdg;
                                                $qtyAppr = $item->qty_approved > 0 ? $item->qty_approved : $item->qty_requested;
                                                $isStockOk = $dp && ($currentStock >= $qtyAppr);
                                                $replacementsText = $p?->serial ? $p->serial->pluck('pn')->filter(fn($v) => !empty($v) && $v !== '-')->unique()->take(3)->implode(', ') : '';
                                            @endphp
                                            <tr id="item-row-{{ $item->id }}" class="item-calc-row" data-item-id="{{ $item->id }}">
                                                <td>
                                                    <div class="p-2 rounded bg-light border-start border-3 border-primary">
                                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                                            <small class="text-muted" style="font-size: 11px;">Request Item:</small>
                                                            <span class="badge bg-label-secondary">{{ (float)$item->qty_requested }} {{ $item->unit }}</span>
                                                        </div>
                                                        <span class="fw-bold text-dark d-block">{{ $item->item_name }}</span>
                                                        @if ($item->note)
                                                             <div class="small text-secondary mt-1"><i class="mdi mdi-note-outline me-1"></i>{{ $item->note }}</div>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td>
                                                    <select class="form-select form-select-sm select-part-ajax" name="items[{{ $item->id }}][id_detail_product]" data-item-id="{{ $item->id }}" required>
                                                        <option value="">-- Cari Part Katalog (Ketik PN/Brand/Deskripsi/G-R) --</option>
                                                        @if ($dp)
                                                            <option value="{{ $dp->id }}" selected
                                                                data-go-code="{{ $goCode }}"
                                                                data-go-text="{{ $goText }}"
                                                                data-brand="{{ $brand }}"
                                                                data-pn="{{ $pn }}"
                                                                data-desc="{{ $desc }}"
                                                                data-replacement-text="{{ $replacementsText }}"
                                                                data-price="{{ $price }}"
                                                                data-stock-bdg="{{ $stockBdg }}"
                                                                data-stock-bks="{{ $stockBks }}">
                                                                {{ ($brand ? $brand . ' ' : '') . $pn }} | {{ $goText }}
                                                            </option>
                                                        @endif
                                                    </select>
                                                </td>
                                                <td>
                                                    <div class="warehouse-toggle-wrapper" id="warehouseToggle_{{ $item->id }}">
                                                        <input type="radio" class="btn-check input-warehouse-toggle" name="items[{{ $item->id }}][warehouse]" id="wh_{{ $item->id }}_bdg" value="BDG" {{ ($item->warehouse ?? 'BDG') === 'BDG' ? 'checked' : '' }} data-item-id="{{ $item->id }}" autocomplete="off">
                                                        <label class="btn btn-sm" for="wh_{{ $item->id }}_bdg" title="Gudang Bandung">
                                                            <span>BDG</span>
                                                            <span class="badge rounded-pill stock-bdg-val">{{ (float)$stockBdg }}</span>
                                                        </label>

                                                        <input type="radio" class="btn-check input-warehouse-toggle" name="items[{{ $item->id }}][warehouse]" id="wh_{{ $item->id }}_bks" value="BKS" {{ ($item->warehouse ?? '') === 'BKS' ? 'checked' : '' }} data-item-id="{{ $item->id }}" autocomplete="off">
                                                        <label class="btn btn-sm" for="wh_{{ $item->id }}_bks" title="Gudang Bekasi">
                                                            <span>BKS</span>
                                                            <span class="badge rounded-pill stock-bks-val">{{ (float)$stockBks }}</span>
                                                        </label>
                                                    </div>
                                                </td>
                                                <td>
                                                    <input type="number" step="1" min="1" class="form-control form-control-sm text-center input-approved-qty" name="items[{{ $item->id }}][qty_approved]" value="{{ (float)$qtyAppr }}" data-item-id="{{ $item->id }}" required>
                                                    <small class="text-muted d-block text-center mt-1" style="font-size: 11px;">{{ $item->unit }}</small>
                                                </td>
                                                <td class="text-center">
                                                    <div class="stock-status-box" id="stockStatus_{{ $item->id }}">
                                                        @if ($dp)
                                                            @if ($isStockOk)
                                                                <span class="badge bg-label-success px-2 py-1">
                                                                    <i class="mdi mdi-check-circle me-1"></i> Tersedia ({{ $currentStock }})
                                                                </span>
                                                            @else
                                                                <span class="badge bg-label-danger px-2 py-1">
                                                                    <i class="mdi mdi-alert-circle me-1"></i> Kurang (Stok: {{ $currentStock }})
                                                                </span>
                                                            @endif
                                                        @else
                                                            <span class="badge bg-label-warning px-2 py-1">
                                                                <i class="mdi mdi-help-circle-outline me-1"></i> Belum Dipilih
                                                            </span>
                                                        @endif
                                                    </div>
                                                </td>
                                                @if ($canSeePrice)
                                                    <td>
                                                        <div class="input-group input-group-sm">
                                                            <span class="input-group-text">Rp</span>
                                                            <input type="number" step="0.01" class="form-control form-control-sm text-end input-unit-price" name="items[{{ $item->id }}][unit_price]" value="{{ (float)$price }}" data-item-id="{{ $item->id }}">
                                                        </div>
                                                    </td>
                                                    <td class="text-end">
                                                        <span class="fw-bold text-dark span-subtotal" id="subtotal_{{ $item->id }}">
                                                            Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                                        </span>
                                                    </td>
                                                @else
                                                    <input type="hidden" class="input-unit-price" name="items[{{ $item->id }}][unit_price]" value="{{ (float)$price }}" data-item-id="{{ $item->id }}">
                                                @endif
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-sm btn-icon btn-outline-danger btn-remove-row rounded-pill shadow-none" title="Hapus Item">
                                                        <i class="mdi mdi-delete-outline"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            {{-- Summary & Notes Box --}}
                            <div class="p-3 bg-light border-top">
                                <div class="row align-items-center mb-3">
                                    <div class="col-md-6 mb-2 mb-md-0">
                                        <label class="form-label fw-semibold small text-uppercase mb-1">Catatan Verifikasi Gudang</label>
                                        <textarea class="form-control form-control-sm" name="warehouse_note" rows="2" placeholder="Tuliskan catatan ketersediaan part, kesiapan fisik, atau estimasi pengambilan...">{{ old('warehouse_note', $wo->warehouse_note) }}</textarea>
                                    </div>
                                    <div class="col-md-6 text-md-end">
                                        @if ($canSeePrice)
                                            <span class="text-muted small d-block">Total Estimasi Nilai Suku Cadang:</span>
                                            <h4 class="fw-bolder text-primary mb-0" id="grandTotalCostLabel">Rp {{ number_format($wo->total_cost, 0, ',', '.') }}</h4>
                                        @endif
                                        <small class="text-muted" id="totalShortageNotice"></small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-footer bg-white border-top py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <div>
                                <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#modalReject">
                                    <i class="mdi mdi-close-circle-outline me-1"></i> Tolak Pengajuan
                                </button>
                                <button type="button" class="btn btn-outline-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalGeneratePr">
                                    <i class="mdi mdi-cart-plus me-1"></i> Buat PR Otomatis
                                </button>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="submit" name="action_type" value="save_draft" class="btn btn-outline-primary">
                                    <i class="mdi mdi-content-save-outline me-1"></i> Simpan Pilihan Part
                                </button>
                                <button type="submit" name="action_type" value="verify_accounting" class="btn btn-primary px-4 shadow-sm" id="btnSubmitVerify">
                                    <i class="mdi mdi-check-decagram me-1"></i> Verifikasi &amp; Teruskan ke Accounting
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            @else
                {{-- MODE VIEW (Setelah Diverifikasi / Disetujui / Selesai) --}}
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-0 fw-bold d-flex align-items-center gap-2">
                                <i class="mdi mdi-tools text-primary"></i> 2. Daftar Suku Cadang &amp; Ketersediaan Stok Gudang
                            </h6>
                            <small class="text-muted">{{ $wo->items->count() }} item spare part dalam pengajuan ini.</small>
                        </div>
                        @if ($canSeePrice)
                            <span class="badge bg-label-primary px-3 py-2 fs-6">
                                Total Biaya: Rp {{ number_format($wo->total_cost, 0, ',', '.') }}
                            </span>
                        @endif
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light text-uppercase" style="font-size: 0.78rem;">
                                <tr>
                                    <th style="width: 40px;">#</th>
                                    <th>Part Selection (Product &amp; Replacement)</th>
                                    <th style="width: 170px;">Gudang Terpilih</th>
                                    <th class="text-center" style="width: 120px;">Stok Gudang</th>
                                    <th class="text-center" style="width: 120px;">Qty Diminta</th>
                                    @if ($canSeePrice)
                                        <th class="text-end" style="width: 150px;">Harga Satuan</th>
                                        <th class="text-end" style="width: 150px;">Subtotal</th>
                                    @endif
                                    <th class="text-center" style="width: 140px;">Status Stok</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($wo->items as $idx => $item)
                                    @php
                                        $dp = $item->detailProduct;
                                        $p = $item->product ?? $dp?->product;
                                        $ser = $p?->serial?->first();
                                        $brand = $ser?->brand ?? ($p?->brand ?? '');
                                        $pn = $ser?->pn ?? ($p?->pn ?? ($dp?->replacement ?: ''));
                                        $desc = $p?->detail_desc ?: ($p?->description ?: ($p?->commodity ?: ''));
                                        $goRaw = $p?->go ?: 'Genuine';
                                        $goCode = strtoupper(substr(trim($goRaw), 0, 1));
                                        $goText = $goCode === 'R' ? 'Replacement' : 'Genuine';
                                        $isStockSufficient = $item->current_stock >= ($item->qty_approved > 0 ? $item->qty_approved : $item->qty_requested);
                                        $stockBdg = (float) ($dp?->stock ?? 0);
                                        $stockBks = (float) ($dp?->warehouse_stock ?? 0);
                                    @endphp
                                    <tr>
                                        <td>{{ $idx + 1 }}</td>
                                        <td>
                                            <div class="d-flex align-items-center gap-1 mb-1">
                                                <span class="badge {{ $goCode === 'R' ? 'bg-label-info' : 'bg-label-warning text-dark' }} fw-bold" style="font-size: 10px;">
                                                    [{{ $goCode }}] {{ $goText }}
                                                </span>
                                                <span class="fw-bold text-dark font-monospace" style="font-size: 0.88rem;">
                                                    {{ $brand ? $brand . ' — ' : '' }}{{ $pn }}
                                                </span>
                                            </div>
                                            <div class="text-dark small fw-semibold">
                                                {{ $desc ?: ($item->item_name ?: 'Part') }}
                                            </div>
                                            @if ($item->item_name && $item->item_name !== $desc)
                                                <small class="text-muted d-block" style="font-size: 11px;">Request Awal: <em>{{ $item->item_name }}</em></small>
                                            @endif
                                            @if ($item->note)
                                                <div class="small text-secondary mt-0.5"><i class="mdi mdi-note-text-outline me-1"></i>{{ $item->note }}</div>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($item->warehouse === 'BKS')
                                                <span class="badge bg-label-info"><i class="mdi mdi-warehouse me-1"></i>Gudang Bekasi (BKS)</span>
                                            @else
                                                <span class="badge bg-label-primary"><i class="mdi mdi-warehouse me-1"></i>Gudang Bandung (BDG)</span>
                                            @endif
                                            <div class="mt-1 font-monospace text-muted" style="font-size: 10px;">
                                                BDG: {{ $stockBdg }} | BKS: {{ $stockBks }}
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span class="fw-bold {{ $isStockSufficient ? 'text-success' : 'text-danger' }}">
                                                {{ (float) $item->current_stock }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-label-primary rounded-pill fs-6 px-2 py-1">
                                                {{ (float) ($item->qty_approved > 0 ? $item->qty_approved : $item->qty_requested) }}
                                            </span>
                                        </td>
                                        @if ($canSeePrice)
                                            <td class="text-end text-muted">
                                                Rp {{ number_format($item->unit_price, 0, ',', '.') }}
                                            </td>
                                            <td class="text-end fw-semibold text-dark">
                                                Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                            </td>
                                        @endif
                                        <td class="text-center">
                                            @if ($item->qty_issued > 0)
                                                <span class="badge bg-label-success">
                                                    <i class="mdi mdi-check-circle me-1"></i> Sudah Keluar
                                                </span>
                                            @elseif ($isStockSufficient)
                                                <span class="badge bg-label-success">
                                                    <i class="mdi mdi-check-circle me-1"></i> Tersedia
                                                </span>
                                            @else
                                                <span class="badge bg-label-danger">
                                                    <i class="mdi mdi-alert-circle me-1"></i> Kurang Stok
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            @if ($canSeePrice)
                                <tfoot class="table-light">
                                    <tr>
                                        <th colspan="6" class="text-end fw-bold text-uppercase">Total Estimasi Nilai Part:</th>
                                        <th class="text-end fw-bolder text-primary fs-6">
                                            Rp {{ number_format($wo->total_cost, 0, ',', '.') }}
                                        </th>
                                        <th></th>
                                    </tr>
                                </tfoot>
                            @endif
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- MODAL 1: VERIFIKASI GUDANG (Simple modal jika dipanggil dari tombol header) --}}
@if ($isWarehouse && in_array($wo->status, ['pending_warehouse', 'waiting_pr']))
    <div class="modal fade" id="modalVerifyWarehouse" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('work-orders.verify-warehouse', $wo->id) }}" method="POST">
                    @csrf
                    <input type="hidden" name="action_type" value="verify_accounting">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">
                            <i class="mdi mdi-check-decagram text-primary me-1"></i> Verifikasi Stok Gudang
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p class="small text-muted mb-3">
                            Dengan memverifikasi, Anda mengonfirmasi bahwa ketersediaan suku cadang telah dicek dan Work Order ini akan diteruskan ke tim <strong>Accounting</strong> untuk otorisasi biaya.
                        </p>
                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-uppercase">Catatan Verifikasi Gudang</label>
                            <textarea class="form-control" name="warehouse_note" rows="3" placeholder="Contoh: Stok di gudang Bandung/Bekasi sudah disiapkan dan siap diambil setelah disetujui Accounting...">{{ $wo->warehouse_note }}</textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="mdi mdi-send me-1"></i> Teruskan ke Accounting
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL 2: BUAT PURCHASE REQUEST DARI WO --}}
    <div class="modal fade" id="modalGeneratePr" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('work-orders.generate-pr', $wo->id) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold text-warning">
                            <i class="mdi mdi-cart-plus me-1"></i> Buat Purchase Request (PR)
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-warning small mb-3">
                            <i class="mdi mdi-information me-1"></i> Sistem akan secara otomatis membuat dokumen <strong>Purchase Request (PR)</strong> untuk suku cadang yang kurang dan memasukkannya ke menu <strong>Monitoring PR</strong> untuk diproses oleh Purchasing.
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-uppercase">Catatan Kebutuhan Pengadaan</label>
                            <textarea class="form-control" name="warehouse_note" rows="3" placeholder="Catatan untuk purchasing / estimasi kebutuhan..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-warning">
                            <i class="mdi mdi-check me-1"></i> Generate Purchase Request
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif

{{-- MODAL 3: APPROVAL ACCOUNTING --}}
@if ($isAccounting && $wo->status === 'pending_accounting')
    <div class="modal fade" id="modalApproveAccounting" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('work-orders.approve-accounting', $wo->id) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold text-success">
                            <i class="mdi mdi-shield-check me-1"></i> Persetujuan Biaya (Accounting)
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="p-3 bg-light rounded-3 border mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="text-muted small">Total Nilai Suku Cadang:</span>
                                <h5 class="fw-bolder text-primary mb-0">Rp {{ number_format($wo->total_cost, 0, ',', '.') }}</h5>
                            </div>
                            <small class="text-muted">Untuk Mesin: <strong>{{ $wo->fixedAsset->code ?? '-' }}</strong> ({{ $wo->items->count() }} part)</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-uppercase required">Perlakuan Akuntansi (Accounting Treatment)</label>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="accounting_treatment" id="treatmentExpense" value="expense" checked>
                                <label class="form-check-label fw-semibold" for="treatmentExpense">
                                    Beban Pemeliharaan (Maintenance Expense)
                                    <small class="text-muted d-block fw-normal">Dicatat sebagai biaya operasional/pemeliharaan mesin rutin.</small>
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="accounting_treatment" id="treatmentCapitalize" value="capitalize">
                                <label class="form-check-label fw-semibold" for="treatmentCapitalize">
                                    Kapitalisasi Nilai Aset (Capitalize to Asset)
                                    <small class="text-muted d-block fw-normal">Menambahkan biaya spare part ke harga perolehan / nilai buku mesin Fixed Asset.</small>
                                </label>
                            </div>
                        </div>

                        <div class="mb-0">
                            <label class="form-label fw-semibold small text-uppercase">Catatan Accounting</label>
                            <textarea class="form-control" name="accounting_note" rows="2" placeholder="Catatan persetujuan / referensi pos anggaran..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success">
                            <i class="mdi mdi-check-circle me-1"></i> Setujui Work Order
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL 4: REJECT WORK ORDER --}}
    <div class="modal fade" id="modalReject" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('work-orders.reject', $wo->id) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold text-danger">
                            <i class="mdi mdi-close-circle-outline me-1"></i> Tolak Work Order
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-uppercase required">Alasan Penolakan</label>
                            <textarea class="form-control" name="rejected_reason" rows="3" placeholder="Tuliskan alasan penolakan Work Order..." required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">
                            <i class="mdi mdi-close-thick me-1"></i> Konfirmasi Penolakan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif

{{-- MODAL 5: BUAT SURAT JALAN & PENGELUARAN SUKU CADANG --}}
@if ($isWarehouse && $wo->status === 'approved')
    <div class="modal fade" id="modalIssueItems" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('work-orders.issue-items', $wo->id) }}" method="POST">
                    @csrf
                    <div class="modal-header bg-success text-white py-3">
                        <h5 class="modal-title fw-bold text-white mb-0">
                            <i class="mdi mdi-truck-fast-outline me-1"></i> Buat Surat Jalan &amp; Pengeluaran Suku Cadang
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="alert alert-success d-flex align-items-center mb-3 p-3" role="alert">
                            <i class="mdi mdi-information-outline fs-3 me-2"></i>
                            <div class="small">
                                Menyetujui pengeluaran ini akan <strong>menerbitkan Surat Jalan (Bukti Barang Keluar)</strong> resmi dan <strong>memotong stok fisik gudang (Bandung / Bekasi)</strong> secara realtime sesuai part yang dipilih.
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-uppercase required">Tanggal Surat Jalan / Pengeluaran</label>
                                <input type="date" name="date" class="form-control" value="{{ now()->toDateString() }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-uppercase required">Nama Penerima / Teknisi</label>
                                <input type="text" name="recipient" class="form-control" value="{{ $wo->technician->name ?? ($wo->creator->name ?? 'Teknisi Workshop') }}" placeholder="Nama PIC Penerima Barang" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold small text-uppercase">Catatan Surat Jalan / Serah Terima</label>
                                <textarea class="form-control" name="note" rows="2" placeholder="Catatan pengeluaran barang, kondisi serah terima, atau no referensi khusus...">Surat Jalan Work Order {{ $wo->no_wo }} (Pemeliharaan Mesin {{ $wo->fixedAsset->code ?? 'Internal' }})</textarea>
                            </div>
                        </div>

                        {{-- Rincian Suku Cadang yang Dikeluarkan & Gudang Asal --}}
                        <div class="card border rounded-3 mb-0 overflow-hidden">
                            <div class="card-header bg-light py-2 px-3">
                                <span class="fw-bold small text-dark"><i class="mdi mdi-format-list-checks me-1"></i> Rincian Part &amp; Gudang yang Akan Dipotong Stoknya:</span>
                            </div>
                            <div class="table-responsive" style="max-height: 220px;">
                                <table class="table table-sm table-hover align-middle mb-0" style="font-size: 0.82rem;">
                                    <thead class="table-light text-uppercase">
                                        <tr>
                                            <th style="width: 30px;">#</th>
                                            <th>Suku Cadang</th>
                                            <th class="text-center" style="width: 130px;">Gudang Potong</th>
                                            <th class="text-center" style="width: 90px;">Qty Keluar</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($wo->items as $idx => $item)
                                            @php
                                                $dp = $item->detailProduct;
                                                $p = $item->product ?? $dp?->product;
                                                $ser = $p?->serial?->first();
                                                $pn = $ser?->pn ?? ($p?->pn ?? ($dp?->replacement ?: '-'));
                                                $desc = $p?->detail_desc ?: ($p?->description ?: ($item->item_name ?: 'Part'));
                                                $qty = (float) ($item->qty_approved > 0 ? $item->qty_approved : $item->qty_requested);
                                            @endphp
                                            <tr>
                                                <td>{{ $idx + 1 }}</td>
                                                <td>
                                                    <span class="fw-bold text-dark font-monospace">{{ $pn }}</span>
                                                    <small class="text-muted d-block">{{ $desc }}</small>
                                                </td>
                                                <td class="text-center">
                                                    @if ($item->warehouse === 'BKS')
                                                        <span class="badge bg-label-info fw-bold"><i class="mdi mdi-warehouse me-0.5"></i>Bekasi (BKS)</span>
                                                    @else
                                                        <span class="badge bg-label-primary fw-bold"><i class="mdi mdi-warehouse me-0.5"></i>Bandung (BDG)</span>
                                                    @endif
                                                </td>
                                                <td class="text-center fw-bold text-dark fs-6">
                                                    {{ $qty }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light py-2">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success px-4">
                            <i class="mdi mdi-truck-check me-1"></i> Terbitkan Surat Jalan &amp; Keluarkan Barang
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif
@endsection

@push('after-style')
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
<style>
.select2-container .select2-selection--single {
    height: 38px !important;
    border: 1px solid #d9dee3 !important;
    display: flex !important;
    align-items: center !important;
}
.select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: normal !important;
    padding-left: 10px !important;
    padding-right: 25px !important;
    color: #566a7f;
    font-size: 0.85rem;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
}
.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 36px !important;
    right: 6px !important;
}
.select2-dropdown {
    z-index: 9999 !important;
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15) !important;
    border: 1px solid #d9dee3 !important;
}
.select2-results__option {
    padding: 8px 12px !important;
}
.select2-results__option--highlighted {
    background-color: #f2f4f8 !important;
    color: #333 !important;
}

/* Warehouse Segmented Switch Toggle */
.warehouse-toggle-wrapper {
    display: inline-flex;
    background: #f1f3f6;
    border-radius: 50rem;
    padding: 2.5px;
    border: 1px solid #e0e4e8;
    width: 100%;
}
.warehouse-toggle-wrapper .btn-check + .btn {
    border: none !important;
    border-radius: 50rem !important;
    font-size: 0.74rem !important;
    font-weight: 600 !important;
    color: #697a8d !important;
    background: transparent !important;
    padding: 4px 6px !important;
    flex: 1 1 0;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 3px;
    box-shadow: none !important;
    cursor: pointer;
    line-height: 1.2;
}
.warehouse-toggle-wrapper .btn-check:checked + .btn {
    background: #696cff !important;
    color: #ffffff !important;
    box-shadow: 0 2px 6px rgba(105, 108, 255, 0.35) !important;
}
.warehouse-toggle-wrapper .btn-check:checked + .btn .badge {
    background: #ffffff !important;
    color: #696cff !important;
    font-weight: 700 !important;
}
.warehouse-toggle-wrapper .btn-check:not(:checked) + .btn .badge {
    background: #dfe3e8 !important;
    color: #566a7f !important;
    font-weight: 600 !important;
}
</style>
@endpush

@push('after-scripts')
<script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
<script>
$(document).ready(function() {
    const CAN_SEE_PRICE = @json($canSeePrice);
    let newRowCounter = 1000;

    function formatPartOption(item) {
        if (item.loading) return item.text;
        if (!item.id) return item.text;

        let goCode = item.go_code || 'G';
        let goText = item.go_text || (goCode === 'R' ? 'Replacement' : 'Genuine');
        let badgeClass = goCode === 'R' ? 'bg-label-warning' : 'bg-label-success';
        let badge = '<span class="badge ' + badgeClass + ' me-1" style="font-size:10px;">' + goText + '</span> ';

        let brand = item.brand ? item.brand + ' — ' : '';
        let pn = item.pn || '';
        let desc = item.desc ? ' <span class="text-muted small">(' + item.desc + ')</span>' : '';

        return $('<span>' + badge + '<strong>' + brand + pn + '</strong>' + desc + '</span>');
    }

    function formatPartSelection(item) {
        if (!item.id) return item.text;
        let opt = item.element ? $(item.element) : null;
        let goCode = item.go_code || (opt ? opt.data('go-code') : '') || '';
        let goText = item.go_text || (opt ? opt.data('go-text') : '') || (goCode === 'R' ? 'Replacement' : 'Genuine');
        let brand = item.brand || (opt ? opt.data('brand') : '') || '';
        let pn = item.pn || (opt ? opt.data('pn') : '') || '';
        
        if (pn) {
            let partTitle = (brand ? brand + ' ' : '') + pn;
            return partTitle + ' | ' + goText;
        }
        return item.text || item.id;
    }

    function initPartSelect2($element) {
        $element.select2({
            width: '100%',
            placeholder: '-- Cari Part Katalog (Ketik PN/Brand/Deskripsi/G-R) --',
            allowClear: true,
            minimumInputLength: 1,
            language: {
                inputTooShort: function() {
                    return 'Ketik minimal 1 karakter untuk mencari...';
                },
                noResults: function() {
                    return 'Part tidak ditemukan';
                },
                searching: function() {
                    return 'Mencari katalog part...';
                }
            },
            ajax: {
                url: "{{ route('work-orders.search-parts') }}",
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return { q: params.term || '' };
                },
                processResults: function(data) {
                    var items = Array.isArray(data) ? data : (data.results || data.data || []);
                    return { results: items };
                },
                cache: true
            },
            templateResult: formatPartOption,
            templateSelection: formatPartSelection,
            escapeMarkup: function(m) { return m; }
        });

        // Event saat part dipilih
        $element.on('select2:select', function(e) {
            let data = e.params ? e.params.data : {};
            let row = $(this).closest('.item-calc-row');
            let opt = $(this).find('option:selected');
            
            $(this).data('selected-data', data);
            row.data('selected-data', data);

            let price = parseFloat(data.price || 0);
            let stockBdg = parseFloat(data.stock_bdg || 0);
            let stockBks = parseFloat(data.stock_bks || 0);

            opt.attr('data-stock-bdg', stockBdg)
               .attr('data-stock-bks', stockBks)
               .attr('data-price', price)
               .attr('data-go-code', data.go_code || 'G')
               .attr('data-brand', data.brand || '')
               .attr('data-pn', data.pn || '')
               .attr('data-desc', data.desc || '');

            opt.data('stock-bdg', stockBdg)
               .data('stock-bks', stockBks)
               .data('price', price)
               .data('go-code', data.go_code || 'G')
               .data('brand', data.brand || '')
               .data('pn', data.pn || '')
               .data('desc', data.desc || '');

            // Auto-detect & switch pilihan gudang
            if (stockBdg > 0 && stockBks <= 0) {
                row.find('.input-warehouse-toggle[value="BDG"]').prop('checked', true);
            } else if (stockBks > 0 && stockBdg <= 0) {
                row.find('.input-warehouse-toggle[value="BKS"]').prop('checked', true);
            }

            // Update realtime nilai stok pada toggle switch
            row.find('.stock-bdg-val').text(stockBdg);
            row.find('.stock-bks-val').text(stockBks);

            row.find('.input-unit-price').val(price);
            updateRowCalculations(row);
        });

        // Event saat part di-clear
        $element.on('select2:clear', function(e) {
            let row = $(this).closest('.item-calc-row');
            $(this).removeData('selected-data');
            row.removeData('selected-data');

            row.find('.stock-bdg-val').text('0');
            row.find('.stock-bks-val').text('0');
            row.find('.input-unit-price').val(0);
            updateRowCalculations(row);
        });
    }

    function updateRowCalculations(row) {
        let selectPart = row.find('.select-part-ajax');
        let hasPart = selectPart.val();
        let opt = selectPart.find('option:selected');
        let selectedData = selectPart.data('selected-data') || row.data('selected-data') || {};

        let stockBdg = selectedData.stock_bdg !== undefined 
            ? parseFloat(selectedData.stock_bdg) 
            : parseFloat(opt.attr('data-stock-bdg') || opt.data('stock-bdg') || 0);

        let stockBks = selectedData.stock_bks !== undefined 
            ? parseFloat(selectedData.stock_bks) 
            : parseFloat(opt.attr('data-stock-bks') || opt.data('stock-bks') || 0);

        let warehouse = row.find('.input-warehouse-toggle:checked').val() || 'BDG';
        let currentStock = warehouse === 'BKS' ? stockBks : stockBdg;

        let qty = parseFloat(row.find('.input-approved-qty').val()) || 0;
        let unitPrice = parseFloat(row.find('.input-unit-price').val()) || 0;
        let subtotal = qty * unitPrice;

        row.find('.span-subtotal').text('Rp ' + new Intl.NumberFormat('id-ID').format(subtotal));

        let statusBox = row.find('.stock-status-box');
        if (hasPart) {
            if (currentStock >= qty && qty > 0) {
                statusBox.html('<span class="badge bg-label-success px-2 py-1"><i class="mdi mdi-check-circle me-1"></i> Tersedia (' + currentStock + ')</span>');
            } else {
                statusBox.html('<span class="badge bg-label-danger px-2 py-1"><i class="mdi mdi-alert-circle me-1"></i> Kurang (Stok: ' + currentStock + ')</span>');
            }
        } else {
            statusBox.html('<span class="badge bg-label-warning px-2 py-1"><i class="mdi mdi-help-circle-outline me-1"></i> Belum Dipilih</span>');
        }

        updateGrandTotal();
    }

    function updateGrandTotal() {
        let grandTotal = 0;
        let shortageCount = 0;
        let unmappedCount = 0;

        $('.item-calc-row').each(function() {
            let row = $(this);
            let selectPart = row.find('.select-part-ajax');
            let hasPart = selectPart.val();
            let opt = selectPart.find('option:selected');
            let selectedData = selectPart.data('selected-data') || row.data('selected-data') || {};

            let stockBdg = selectedData.stock_bdg !== undefined 
                ? parseFloat(selectedData.stock_bdg) 
                : parseFloat(opt.attr('data-stock-bdg') || opt.data('stock-bdg') || 0);

            let stockBks = selectedData.stock_bks !== undefined 
                ? parseFloat(selectedData.stock_bks) 
                : parseFloat(opt.attr('data-stock-bks') || opt.data('stock-bks') || 0);

            let warehouse = row.find('.input-warehouse-toggle:checked').val() || 'BDG';
            let currentStock = warehouse === 'BKS' ? stockBks : stockBdg;

            let qty = parseFloat(row.find('.input-approved-qty').val()) || 0;
            let unitPrice = parseFloat(row.find('.input-unit-price').val()) || 0;
            
            grandTotal += (qty * unitPrice);

            if (!hasPart) {
                unmappedCount++;
            } else if (currentStock < qty) {
                shortageCount++;
            }
        });

        $('#grandTotalCostLabel').text('Rp ' + new Intl.NumberFormat('id-ID').format(grandTotal));

        let notice = '';
        if (unmappedCount > 0) {
            notice = '<span class="text-warning"><i class="mdi mdi-alert-outline me-1"></i>Ada ' + unmappedCount + ' item belum dipilih part katalognya.</span>';
        } else if (shortageCount > 0) {
            notice = '<span class="text-danger"><i class="mdi mdi-alert-circle me-1"></i>Ada ' + shortageCount + ' item kurang stok (Bisa buat PR).</span>';
        } else {
            notice = '<span class="text-success"><i class="mdi mdi-check-all me-1"></i>Semua stok tersedia di gudang terpilih.</span>';
        }
        $('#totalShortageNotice').html(notice);
    }

    // Inisialisasi Select2 untuk semua elemen part selection
    $('.select-part-ajax').each(function() {
        initPartSelect2($(this));
    });

    // Event listeners saat switch toggle gudang
    $(document).on('change', '.input-warehouse-toggle', function() {
        let row = $(this).closest('.item-calc-row');
        updateRowCalculations(row);
    });

    // Event listeners saat ganti qty atau harga
    $(document).on('input', '.input-approved-qty, .input-unit-price', function() {
        let row = $(this).closest('.item-calc-row');
        updateRowCalculations(row);
    });

    // Tambah baris baru oleh Gudang
    $(document).on('click', '#btnAddWarehouseItem', function(e) {
        e.preventDefault();
        newRowCounter++;
        let priceCells = CAN_SEE_PRICE ? `
            <td>
                <div class="input-group input-group-sm">
                    <span class="input-group-text">Rp</span>
                    <input type="number" step="0.01" class="form-control form-control-sm text-end input-unit-price" name="items[${newRowCounter}][unit_price]" value="0" data-item-id="${newRowCounter}">
                </div>
            </td>
            <td class="text-end">
                <span class="fw-bold text-dark span-subtotal" id="subtotal_${newRowCounter}">Rp 0</span>
            </td>
        ` : `
            <input type="hidden" class="input-unit-price" name="items[${newRowCounter}][unit_price]" value="0" data-item-id="${newRowCounter}">
        `;

        let tr = `
            <tr id="item-row-${newRowCounter}" class="item-calc-row" data-item-id="${newRowCounter}">
                <td>
                    <div class="p-2 rounded bg-light border-start border-3 border-warning">
                        <small class="text-muted d-block" style="font-size: 11px;">Part Tambahan Gudang:</small>
                        <input type="text" class="form-control form-control-sm mt-1" name="items[${newRowCounter}][item_name]" placeholder="Nama item tambahan..." required>
                    </div>
                </td>
                <td>
                    <select class="form-select form-select-sm select-part-ajax" name="items[${newRowCounter}][id_detail_product]" data-item-id="${newRowCounter}" required>
                        <option value="">-- Cari Part Katalog (Ketik PN/Brand/Deskripsi/G-R) --</option>
                    </select>
                </td>
                <td>
                    <div class="warehouse-toggle-wrapper" id="warehouseToggle_${newRowCounter}">
                        <input type="radio" class="btn-check input-warehouse-toggle" name="items[${newRowCounter}][warehouse]" id="wh_${newRowCounter}_bdg" value="BDG" checked data-item-id="${newRowCounter}" autocomplete="off">
                        <label class="btn btn-sm" for="wh_${newRowCounter}_bdg" title="Gudang Bandung">
                            <span>BDG</span>
                            <span class="badge rounded-pill stock-bdg-val">0</span>
                        </label>

                        <input type="radio" class="btn-check input-warehouse-toggle" name="items[${newRowCounter}][warehouse]" id="wh_${newRowCounter}_bks" value="BKS" data-item-id="${newRowCounter}" autocomplete="off">
                        <label class="btn btn-sm" for="wh_${newRowCounter}_bks" title="Gudang Bekasi">
                            <span>BKS</span>
                            <span class="badge rounded-pill stock-bks-val">0</span>
                        </label>
                    </div>
                </td>
                <td>
                    <input type="number" step="1" min="1" class="form-control form-control-sm text-center input-approved-qty" name="items[${newRowCounter}][qty_approved]" value="1" data-item-id="${newRowCounter}" required>
                    <select class="form-select form-select-sm mt-1" name="items[${newRowCounter}][unit]" style="font-size:11px;">
                        <option value="Pcs" selected>Pcs</option>
                        <option value="Set">Set</option>
                        <option value="Unit">Unit</option>
                        <option value="Liter">Liter</option>
                        <option value="Box">Box</option>
                    </select>
                </td>
                <td class="text-center">
                    <div class="stock-status-box" id="stockStatus_${newRowCounter}">
                        <span class="badge bg-label-warning px-2 py-1">Belum Dipilih</span>
                    </div>
                </td>
                ${priceCells}
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-icon btn-outline-danger btn-remove-row rounded-pill shadow-none" title="Hapus Item">
                        <i class="mdi mdi-delete-outline"></i>
                    </button>
                </td>
            </tr>
        `;
        $('#warehouseItemsBody').append(tr);
        let newSelect = $(`#item-row-${newRowCounter}`).find('.select-part-ajax');
        initPartSelect2(newSelect);
        updateGrandTotal();
    });

    // Remove row
    $(document).on('click', '.btn-remove-row', function(e) {
        e.preventDefault();
        let row = $(this).closest('.item-calc-row');
        let itemId = row.data('item-id');
        if (itemId && !isNaN(itemId) && itemId < 1000) {
            $('#formWarehouseVerify').append(`<input type="hidden" name="deleted_item_ids[]" value="${itemId}">`);
        }
        row.remove();
        updateGrandTotal();
    });

    // Initial calculation on load
    $('.item-calc-row').each(function() {
        updateRowCalculations($(this));
    });
});
</script>
@endpush

