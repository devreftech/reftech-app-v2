@extends('layouts.sales.app')
@section('title', @$purchase ? 'Edit Purchase Order' : 'Create Purchase Order')
@section('content')
    <style>
        /* Card design refinements */
        #formAuthentication .card {
            border-radius: 12px;
            border: 1px solid #e7e7ee !important;
            box-shadow: 0 2px 8px 0 rgba(34, 41, 47, 0.04) !important;
            transition: box-shadow 0.2s ease, border-color 0.2s ease;
        }
        #formAuthentication .card:hover {
            transform: none !important;
            box-shadow: 0 4px 16px 0 rgba(34, 41, 47, 0.07) !important;
        }
        .po-section-header {
            background-color: #fbfbfe;
            border-bottom: 1px solid #eef0f6;
            padding: 0.95rem 1.25rem;
            border-radius: 12px 12px 0 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .po-section-title {
            font-weight: 700;
            font-size: 0.95rem;
            color: #2b3445;
            display: flex;
            align-items: center;
            margin: 0;
        }
        .po-section-step {
            width: 26px;
            height: 26px;
            border-radius: 7px;
            background: #eef0fd;
            color: #696cff;
            font-weight: 700;
            font-size: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-right: 10px;
        }
        
        /* Repeater row styling */
        .repeater-wrapper {
            transition: background-color 0.15s ease;
        }
        .repeater-wrapper:hover {
            background-color: #fafbfe;
        }
        .repeater-wrapper:not(:last-child) .repeater-row-inner {
            border-bottom: 1px dashed #e7e7ee;
        }
        .repeater-row-inner {
            padding: 1.15rem 1.25rem;
        }
        
        /* Category bullet radios & aligned column headers */
        .item-col-label {
            height: 24px;
            display: flex;
            align-items: center;
            margin-bottom: 4px;
        }
        .item-category-radios {
            height: 24px;
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 4px;
        }
        .item-category-radios .form-check {
            margin-bottom: 0;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding-left: 0;
        }
        .item-category-radios .form-check-input {
            cursor: pointer;
            margin: 0;
            float: none;
        }
        .item-category-radios .form-check-label {
            cursor: pointer;
            user-select: none;
            font-size: 12px;
            font-weight: 500;
            color: #566a7f;
            line-height: 1;
            margin-bottom: 0;
        }
        .item-category-radios .form-check-input:checked + .form-check-label {
            color: #696cff;
            font-weight: 700;
        }

        /* Uniform input heights & rounded styling across line item row (standardizing with Satuan Select2) */
        .repeater-wrapper .form-control:not(textarea),
        .repeater-wrapper .form-select,
        .repeater-wrapper .input-group:not(.has-validation) > .form-control,
        .repeater-wrapper .input-group:not(.has-validation) > .input-group-text,
        .repeater-wrapper .input-group-sm > .form-control,
        .repeater-wrapper .input-group-sm > .input-group-text {
            height: 38px !important;
            min-height: 38px !important;
            font-size: 0.85rem !important;
        }
        .repeater-wrapper .select2-container .select2-selection--single {
            height: 38px !important;
            min-height: 38px !important;
            border-radius: 6px !important;
        }
        .repeater-wrapper .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 36px !important;
            font-size: 0.85rem !important;
        }
        .repeater-wrapper .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px !important;
        }
        .repeater-wrapper .amount-label {
            height: 38px !important;
            min-height: 38px !important;
            line-height: 38px !important;
            font-size: 0.925rem !important;
        }
        .repeater-wrapper .btn-del {
            height: 38px !important;
            width: 38px !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            padding: 0 !important;
            border-radius: 6px !important;
        }

        /* Rounded styling for Harga Satuan input group & line item form controls */
        .repeater-wrapper .form-control,
        .repeater-wrapper .form-select {
            border-radius: 6px !important;
        }
        .repeater-wrapper .input-group {
            border-radius: 6px !important;
        }
        .repeater-wrapper .input-group > .input-group-text:first-child {
            border-top-left-radius: 6px !important;
            border-bottom-left-radius: 6px !important;
            border-top-right-radius: 0 !important;
            border-bottom-right-radius: 0 !important;
        }
        .repeater-wrapper .input-group > .invoice-item-price-label,
        .repeater-wrapper .input-group > .form-control:not(:first-child) {
            border-top-right-radius: 6px !important;
            border-bottom-right-radius: 6px !important;
            border-top-left-radius: 0 !important;
            border-bottom-left-radius: 0 !important;
        }

        /* Sleek Modern Segmented Tab Buttons for Ship To */
        .ship-to-tabs-container {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 8px;
            background: #f5f6fa;
            padding: 5px;
            border-radius: 10px;
            border: 1px solid #e7e7ee;
        }
        @media (max-width: 768px) {
            .ship-to-tabs-container {
                grid-template-columns: 1fr;
            }
        }
        .ship-to-tab-btn {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 12px;
            background: transparent;
            border: 1.5px solid transparent;
            border-radius: 8px;
            color: #566a7f;
            cursor: pointer;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
            text-align: left;
            user-select: none;
            width: 100%;
        }
        .ship-to-tab-btn .tab-icon {
            width: 34px;
            height: 34px;
            border-radius: 7px;
            background: #e9ecef;
            color: #697a8d;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 17px;
            transition: all 0.2s ease;
        }
        .ship-to-tab-btn .tab-content {
            display: flex;
            flex-direction: column;
            line-height: 1.25;
            min-width: 0;
            flex-grow: 1;
        }
        .ship-to-tab-btn .tab-title {
            font-size: 12.5px;
            font-weight: 600;
            color: #435971;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .ship-to-tab-btn .tab-desc {
            font-size: 11px;
            color: #a1acb8;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .ship-to-tab-btn .tab-check {
            display: none;
            color: #696cff;
            font-size: 16px;
            flex-shrink: 0;
        }
        .ship-to-tab-btn:hover:not(.active) {
            background: rgba(255, 255, 255, 0.75);
            border-color: #d9dee3;
        }
        .ship-to-tab-btn.active {
            background: #ffffff !important;
            border-color: #696cff !important;
            box-shadow: 0 2px 8px rgba(105, 108, 255, 0.16) !important;
        }
        .ship-to-tab-btn.active .tab-icon {
            background: #ebeefd;
            color: #696cff;
        }
        .ship-to-tab-btn.active .tab-title {
            color: #696cff;
            font-weight: 700;
        }
        .ship-to-tab-btn.active .tab-desc {
            color: #697a8d;
        }
        .ship-to-tab-btn.active .tab-check {
            display: block;
        }
        
        /* Financial Summary Box */
        .summary-card-inner {
            background: #ffffff;
            border: 1px solid #e6e8f0;
            border-radius: 12px;
            overflow: hidden;
        }
        .summary-grand-total {
            background: linear-gradient(135deg, #f0f2ff 0%, #e6e9ff 100%);
            border: 1.5px dashed #696cff;
            border-radius: 10px;
            padding: 1.15rem 1.25rem;
        }

        /* Drag handle */
        .btn-drag-handle {
            cursor: grab;
            color: #a1acb8;
            padding: 4px 6px;
            border-radius: 6px;
            transition: all 0.15s;
        }
        .btn-drag-handle:hover {
            color: #566a7f;
            background-color: #eceef1;
        }
        .btn-drag-handle:active {
            cursor: grabbing;
        }

        /* Sticky bottom action bar */
        .po-sticky-footer {
            position: sticky;
            bottom: 15px;
            z-index: 1010;
            background: rgba(255, 255, 255, 0.96);
            backdrop-filter: blur(10px);
            border: 1px solid #e2e4ed;
            border-radius: 12px;
            box-shadow: 0 6px 24px rgba(34, 41, 47, 0.1);
            padding: 0.85rem 1.25rem;
            margin-top: 1.5rem;
        }
    </style>

    {{-- Hero Page Header & Top Bar --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center py-3 mb-3 gap-3">
        <div>
            <h4 class="fw-bold mb-1 d-flex align-items-center gap-2">
                <span class="text-muted fw-light">Procurement / <a href="{{ route('purchase.index') }}" class="text-muted">Purchase Order</a> /</span>
                <span class="text-primary">{{ @$purchase ? 'Edit #' . $purchase->no_po : 'Create New PO' }}</span>
            </h4>
            <p class="text-muted mb-0 small">
                <i class="mdi mdi-file-document-edit-outline me-1 text-primary"></i>
                Lengkapi rincian pemesanan, rekanan supplier, termin pembayaran, dan daftar item belanja.
            </p>
        </div>
        <div class="d-flex gap-2 align-items-center flex-wrap">
            @if (!@$purchase)
                <span id="poDraftStatus" class="badge bg-label-secondary d-inline-flex align-items-center py-2 px-3 align-self-center shadow-xs" title="Status autosave draf lokal">
                    <i class="mdi mdi-cloud-check-outline me-1"></i><span class="txt">Autosave Aktif</span>
                </span>
                <button type="button" id="poDraftReset" class="btn btn-outline-danger btn-sm d-none" title="Hapus draf tersimpan & mulai ulang">
                    <i class="mdi mdi-trash-can-outline me-1"></i> Reset Draf
                </button>
            @endif
            <a href="{{ route('purchase.index') }}" class="btn btn-label-secondary">
                <i class="mdi mdi-arrow-left me-1"></i> Kembali
            </a>
            <button type="submit" form="formAuthentication" class="btn btn-primary shadow-sm px-3">
                <i class="mdi mdi-content-save-outline me-1"></i> Simpan Dokumen PO
            </button>
        </div>
    </div>

    <form id="formAuthentication" class="fv-plugins-bootstrap5 fv-plugins-framework"
        action="{{ @$purchase ? route('purchase.update', $purchase->id) : route('purchase.store') }}" method="post"
        enctype="multipart/form-data">
        @csrf
        @if (@$purchase)
            @method('patch')
        @endif
        <input type="hidden" name="id_purchase_request" value="{{ old('id_purchase_request', $sourcePr->id ?? '') }}">

        @if (!@$purchase)
            {{-- Auto-Save Draft Recovery Alert Banner --}}
            <div id="poDraftRecoveryAlert" class="alert alert-warning border-warning shadow-sm d-none align-items-center justify-content-between flex-wrap gap-3 mb-4" style="border-radius: 12px; background: #fffdf5; border-left: 5px solid #f59e0b !important;">
                <div class="d-flex align-items-center gap-3">
                    <div class="badge rounded-circle p-2 bg-label-warning flex-shrink-0" style="width: 42px; height: 42px; display: inline-flex; align-items: center; justify-content: center;">
                        <i class="mdi mdi-history fs-4"></i>
                    </div>
                    <div>
                        <div class="fw-bold text-dark" style="font-size: 0.95rem;">
                            Ditemukan draf PO sebelumnya yang belum disimpan!
                        </div>
                        <div class="text-muted small" id="poDraftInfoText">
                            Tersimpan pada <span class="fw-semibold text-dark" id="poDraftTimeText">-</span> (<span id="poDraftItemCount" class="fw-semibold text-primary">0</span> item terdata).
                        </div>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-warning text-dark btn-sm fw-semibold shadow-xs" id="btnApplyDraft">
                        <i class="mdi mdi-restore me-1"></i> Pulihkan Draf
                    </button>
                    <button type="button" class="btn btn-label-secondary btn-sm" id="btnDismissDraft">
                        Abaikan & Buat Baru
                    </button>
                </div>
            </div>
        @endif

        @if ($sourcePr ?? null)
            <div class="alert alert-info d-flex align-items-center gap-2 mb-3 shadow-xs border-0" style="border-radius: 10px; background: #f0f4ff; color: #35508c;">
                <i class="mdi mdi-file-document-outline fs-5 text-primary"></i>
                <div>Item Sparepart di bawah otomatis ditarik dari Purchase Request <strong>{{ $sourcePr->no_pr }}</strong>. Silakan tentukan supplier & lengkapi penawaran harga.</div>
            </div>
        @elseif ($sourceProductSet ?? null)
            <div class="alert alert-info d-flex align-items-center gap-2 mb-3 shadow-xs border-0" style="border-radius: 10px; background: #f0f4ff; color: #35508c;">
                <i class="mdi mdi-package-variant-closed fs-5 text-primary"></i>
                <div>Item Sparepart di bawah otomatis dari Bundle <strong>{{ $sourceProductSet->product->commodity ?? 'Product Set' }}</strong>. Silakan tentukan supplier & lengkapi harga.</div>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger shadow-xs border-0" style="border-radius: 10px;">
                <div class="fw-bold mb-1"><i class="mdi mdi-alert-circle-outline me-1"></i> Periksa kembali data input:</div>
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Hero Document No PO Card --}}
        <div class="card mb-4 border-0 shadow-sm" style="background: linear-gradient(135deg, #f8f9ff 0%, #f1f3fd 100%); border-left: 5px solid #696cff !important;">
            <div class="card-body py-3 px-4">
                <div class="row align-items-center g-3">
                    <div class="col-md-8 col-12">
                        <label class="form-label text-uppercase fw-bold text-primary small mb-1" style="letter-spacing: .5px;">
                            <i class="mdi mdi-pound me-1"></i> Purchase Order Number
                        </label>
                        <div class="input-group input-group-merge">
                            <span class="input-group-text bg-white border-primary-subtle fw-bold text-primary px-3">
                                <i class="mdi mdi-file-document-outline fs-5"></i>
                            </span>
                            <input type="text" class="form-control form-control-lg fw-bold bg-white text-primary border-primary-subtle shadow-xs"
                                id="no_po_display" name="no_po" required style="font-size: 1.25rem; letter-spacing: 0.5px;"
                                value="{{ old('no_po', @$purchase->no_po ?? $previewNoPo ?? '') }}">
                        </div>
                    </div>
                    <div class="col-md-4 col-12 text-md-end">
                        @if (@$purchase)
                            <span class="badge bg-label-primary px-3 py-2 fs-6 rounded-pill">
                                <i class="mdi mdi-shape-outline me-1"></i> {{ $purchase->category }}
                            </span>
                        @else
                            <div class="d-inline-flex align-items-center gap-2">
                                <span class="badge bg-label-success px-3 py-2 fs-6 rounded-pill">
                                    <i class="mdi mdi-plus-circle-outline me-1"></i> DOKUMEN BARU
                                </span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- 1. SUPPLIER & CONTACT DETAILS --}}
        <div class="card mb-4 border-0 shadow-sm">
            <div class="po-section-header">
                <div class="po-section-title">
                    <span class="po-section-step">1</span>
                    <span>Informasi Supplier &amp; Rekanan</span>
                </div>
                <span class="badge bg-label-primary px-2 py-1" style="font-size: 11px;">Data Supplier &amp; PIC</span>
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    {{-- Supplier Dropdown & Quick Add/Edit Buttons --}}
                    <div class="col-12">
                        <div class="d-flex align-items-center gap-2">
                            <div class="form-floating form-floating-outline flex-grow-1">
                                <select id="supplier-dropdown" class="select2 form-select invoice-item-supplier"
                                    data-allow-clear="true" name="supplier" data-id="1" required
                                    {{ Auth::user()->role == 'Logistic' ? 'disabled' : '' }}>
                                    <option value="">Pilih Rekanan Supplier...</option>
                                    @foreach ($suppliers as $supp)
                                        <option value="{{ $supp->id }}" data-info="{{ $supp->info }}"
                                            data-code="{{ $supp->code }}" data-phone="{{ $supp->phone }}"
                                            data-address="{{ $supp->address }}"
                                            {{ @$purchase->id_supplier == $supp->id ? 'selected' : '' }}>
                                            {{ $supp->supplier }}
                                        </option>
                                    @endforeach
                                </select>
                                <label for="supplier-dropdown">Nama Supplier <span class="text-danger">*</span></label>
                            </div>
                            <button type="button" class="btn btn-outline-primary"
                                data-bs-toggle="modal" data-bs-target="#quickAddSupplierModal"
                                {{ Auth::user()->role == 'Logistic' ? 'disabled' : '' }} title="Tambah Supplier Baru">
                                <i class="mdi mdi-domain-plus me-1"></i>Supplier Baru
                            </button>
                            <button type="button" id="btn-edit-supplier" class="btn btn-label-primary d-none"
                                data-bs-toggle="modal" data-bs-target="#editSupplierModal"
                                {{ Auth::user()->role == 'Logistic' ? 'disabled' : '' }} title="Edit Informasi Supplier">
                                <i class="mdi mdi-pencil-outline me-1"></i>Edit Supplier
                            </button>
                        </div>
                    </div>

                    {{-- ATTN (PIC) & Mobile Phone --}}
                    <div class="col-md-6">
                        <div class="d-flex align-items-center gap-2">
                            <div class="form-floating form-floating-outline flex-grow-1">
                                <select id="attn" name="attn" class="form-select" data-allow-clear="true">
                                    @if (old('attn', @$purchase->attn ?? ''))
                                        <option value="{{ old('attn', @$purchase->attn) }}" selected>
                                            {{ old('attn', @$purchase->attn) }}
                                        </option>
                                    @endif
                                </select>
                                <label for="attn">ATTN (PIC Supplier)</label>
                            </div>
                            <button type="button" class="btn btn-outline-secondary btn-sm" id="btn-add-attn-pic" title="Tambah PIC Baru">
                                <i class="mdi mdi-account-plus"></i>
                            </button>
                            <button type="button" class="btn btn-label-primary btn-sm d-none" id="btn-edit-attn-pic" title="Edit PIC">
                                <i class="mdi mdi-pencil-outline"></i>
                            </button>
                        </div>
                        <div class="form-text small text-muted" id="attn-empty-hint" style="display:none;">
                            <i class="mdi mdi-information-outline me-1"></i>Belum ada kontak PIC tercatat untuk supplier ini.
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-floating form-floating-outline">
                            <input class="form-control" type="text" placeholder="Nomor HP / Telepon PIC..."
                                id="mobile" name="mobile" value="{{ old('mobile', @$purchase->mobile ?? '') }}">
                            <label for="mobile">No. HP / Kontak Telepon</label>
                        </div>
                    </div>

                    {{-- Supplier Address Selector & Textarea --}}
                    <div class="col-12">
                        <div class="card bg-light-soft border border-light-subtle p-3 mb-0" style="border-radius: 10px;">
                            <div class="d-flex align-items-center justify-content-between mb-2 flex-wrap gap-1">
                                <label class="form-label fw-bold text-dark font-13 mb-0" for="address-select">
                                    <i class="mdi mdi-map-marker-outline text-primary me-1"></i>Pilih Alamat Tersimpan
                                </label>
                                <button type="button" class="btn btn-outline-primary btn-xs" id="btn-add-supplier-address" title="Tambah Alamat Baru untuk Supplier Ini">
                                    <i class="mdi mdi-plus me-1"></i>Tambah Alamat Baru
                                </button>
                            </div>
                            <div class="mb-2">
                                <select id="address-select" class="form-select form-select-sm">
                                    <option value="">-- Pilih Alamat Operasional / Cabang / Gudang --</option>
                                </select>
                            </div>
                            <div class="form-floating form-floating-outline">
                                <textarea class="form-control bg-white" id="address" name="address" rows="2" style="height: 68px;"
                                    placeholder="Alamat supplier akan terisi otomatis saat alamat dipilih">{{ old('address', @$purchase->address ?? '') }}</textarea>
                                <label for="address">Alamat Supplier Terpilih</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 2. TRANSACTION PARAMETERS & DELIVERY --}}
        <div class="card mb-4 border-0 shadow-sm">
            <div class="po-section-header">
                <div class="po-section-title">
                    <span class="po-section-step">2</span>
                    <span>Ketentuan Transaksi &amp; Pengiriman</span>
                </div>
                <span class="badge bg-label-info px-2 py-1" style="font-size: 11px;">Termin &amp; Destinasi</span>
            </div>
            <div class="card-body p-4">
                <div class="row g-3 mb-4">
                    <div class="col-md-3 col-sm-6">
                        <div class="form-floating form-floating-outline">
                            <input class="form-control" type="date" id="date" name="date" required
                                value="{{ old('date', !empty($purchase->date) ? \Carbon\Carbon::parse($purchase->date)->format('Y-m-d') : \Carbon\Carbon::today()->format('Y-m-d')) }}">
                            <label for="date">Tanggal PO <span class="text-danger">*</span></label>
                        </div>
                    </div>

                    <div class="col-md-3 col-sm-6">
                        <div class="form-floating form-floating-outline">
                            <input class="form-control" type="text" placeholder="No. Reference..."
                                id="no_reference" name="no_reference"
                                value="{{ old('no_reference', @$purchase->no_reference ?? '') }}">
                            <label for="no_reference">No. Reference <span class="text-muted small">(opsional)</span></label>
                        </div>
                    </div>

                    <div class="col-md-3 col-sm-6">
                        <div class="form-floating form-floating-outline">
                            <input class="form-control" type="text" placeholder="Delivery Time..."
                                id="delivery" name="delivery"
                                value="{{ old('delivery', @$purchase->delivery ?? 'ASAP') }}">
                            <label for="delivery">Delivery Time</label>
                        </div>
                    </div>

                    <div class="col-md-3 col-sm-6">
                        <div class="d-flex align-items-center gap-2">
                            <div class="form-floating form-floating-outline flex-grow-1">
                                @php
                                    $selectedPoType = old('category', @$purchase->category ?? 'Sparepart');
                                @endphp
                                <select class="form-select" id="po-type-select" name="category">
                                    @foreach ($poTypes as $poType)
                                        <option value="{{ $poType->name }}" {{ $selectedPoType == $poType->name ? 'selected' : '' }}>
                                            {{ $poType->name }}
                                        </option>
                                    @endforeach
                                    @if ($selectedPoType && $poTypes->doesntContain('name', $selectedPoType))
                                        <option value="{{ $selectedPoType }}" selected>{{ $selectedPoType }}</option>
                                    @endif
                                </select>
                                <label for="po-type-select">PO Type</label>
                            </div>
                            <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal"
                                data-bs-target="#quickAddPoTypeModal" title="Tambah Tipe PO Baru">
                                <i class="mdi mdi-plus"></i>
                            </button>
                        </div>
                    </div>

                    <div class="col-md-4 col-sm-6">
                        <div class="form-floating form-floating-outline">
                            <select class="form-select" id="payment-select">
                                <optgroup label="Standar System">
                                    <option value="Cash Before Delivery">Cash Before Delivery</option>
                                    <option value="DP 50% & BP 50%">DP 50% & BP 50%</option>
                                    <option value="DP 30% & BP 70%">DP 30% & BP 70%</option>
                                    <option value="14 Days after invoice release">14 Days after invoice release</option>
                                    <option value="30 Days after invoice release">30 Days after invoice release</option>
                                </optgroup>
                                <option value="manual">-- Custom (Isi Sendiri) --</option>
                            </select>
                            <label for="payment-select">Payment Terms</label>
                            <input type="hidden" name="payment" id="input-payment-hidden"
                                value="{{ old('payment', @$purchase->payment ?? 'Cash Before Delivery') }}">
                        </div>
                        <div class="mt-2" id="manual-payment-wrapper" style="display:none;">
                            <input type="text" class="form-control" id="input-payment-manual"
                                placeholder="Ketik custom payment term...">
                        </div>
                    </div>

                    @php
                        $pmtType = old('payment_type', @$purchase->payment_type ?? 'cash');
                        $topDays = old('top_days', @$purchase->top_days);
                        $dueEst = old('due_date_estimate', !empty($purchase->due_date_estimate) ? \Carbon\Carbon::parse($purchase->due_date_estimate)->format('Y-m-d') : '');
                    @endphp
                    <div class="col-md-3 col-sm-6">
                        <div class="form-floating form-floating-outline">
                            <select class="form-select" id="payment-type-select" name="payment_type">
                                <option value="cash" {{ $pmtType == 'cash' ? 'selected' : '' }}>Cash</option>
                                <option value="transfer" {{ $pmtType == 'transfer' ? 'selected' : '' }}>Transfer</option>
                                <option value="tempo" {{ $pmtType == 'tempo' ? 'selected' : '' }}>Tempo (Credit)</option>
                            </select>
                            <label for="payment-type-select">Metode Pembayaran</label>
                        </div>
                    </div>

                    <div class="col-md-2 col-sm-6 tempo-field" style="{{ $pmtType == 'tempo' ? '' : 'display:none;' }}">
                        <div class="form-floating form-floating-outline">
                            <input class="form-control" type="number" min="0" id="top-days-input" name="top_days"
                                placeholder="30" value="{{ $topDays !== null && $topDays !== '' ? $topDays : 30 }}">
                            <label for="top-days-input">Termin (hari)</label>
                        </div>
                    </div>

                    <div class="col-md-3 col-sm-6 tempo-field" style="{{ $pmtType == 'tempo' ? '' : 'display:none;' }}">
                        <div class="form-floating form-floating-outline">
                            <input class="form-control" type="date" id="due-date-estimate-input" name="due_date_estimate"
                                value="{{ $dueEst }}">
                            <label for="due-date-estimate-input">Estimasi Jatuh Tempo</label>
                        </div>
                    </div>
                </div>

                {{-- Alamat Pengiriman (Ship To) - Simple & Minimalist --}}
                @php
                    $addrBdg = 'Taman Kopo Indah V, Ruko Soho Sommerville No. 31 Bandung - Jawabarat 40218';
                    $addrBks = 'Jl. Nancep No.45A, Cibening, Kec. Setu, Kabupaten Bekasi, Jawa Barat 17320';
                    $currentShipTo = old('ship_to', @$purchase->ship_to ?? $addrBdg);
                    $isBdg = trim($currentShipTo) == trim($addrBdg);
                    $isBks = trim($currentShipTo) == trim($addrBks);
                    $isCustom = !$isBdg && !$isBks && !empty($currentShipTo);
                    if (!$isBdg && !$isBks && !$isCustom) {
                        $isBdg = true;
                        $currentShipTo = $addrBdg;
                    }
                @endphp
                <div class="col-12 mt-2 pt-3 border-top">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <label class="form-label fw-bold text-dark font-13 mb-0">
                            <i class="mdi mdi-truck-delivery-outline text-primary me-1"></i>Alamat Pengiriman (Ship To)
                        </label>
                        <span class="badge bg-label-primary font-11">Pilihan Cepat / Manual</span>
                    </div>

                    {{-- Modern Segmented Tab Buttons --}}
                    <div class="ship-to-tabs-container mb-2">
                        <button type="button" class="ship-to-tab-btn {{ $isBdg ? 'active' : '' }}" data-preset="BDG" data-address="{{ $addrBdg }}">
                            <div class="tab-icon">
                                <i class="mdi mdi-office-building-marker"></i>
                            </div>
                            <div class="tab-content text-start">
                                <span class="tab-title">Gudang Bandung (BDG)</span>
                                <span class="tab-desc">Kopo Indah V, Bandung</span>
                            </div>
                            <i class="mdi mdi-check-circle tab-check ms-auto"></i>
                        </button>

                        <button type="button" class="ship-to-tab-btn {{ $isBks ? 'active' : '' }}" data-preset="BKS" data-address="{{ $addrBks }}">
                            <div class="tab-icon">
                                <i class="mdi mdi-warehouse"></i>
                            </div>
                            <div class="tab-content text-start">
                                <span class="tab-title">Gudang Bekasi (BKS)</span>
                                <span class="tab-desc">Setu, Bekasi</span>
                            </div>
                            <i class="mdi mdi-check-circle tab-check ms-auto"></i>
                        </button>

                        <button type="button" class="ship-to-tab-btn {{ $isCustom ? 'active' : '' }}" data-preset="CUSTOM">
                            <div class="tab-icon">
                                <i class="mdi mdi-map-marker-plus-outline"></i>
                            </div>
                            <div class="tab-content text-start">
                                <span class="tab-title">Alamat Lain / Custom</span>
                                <span class="tab-desc">Input manual alamat baru</span>
                            </div>
                            <i class="mdi mdi-check-circle tab-check ms-auto"></i>
                        </button>
                    </div>

                    <div class="form-floating form-floating-outline">
                        <textarea class="form-control bg-white" id="ship_to_input" name="ship_to" rows="2" style="height: 68px;"
                            placeholder="Tuliskan alamat lengkap pengiriman..." required>{{ $currentShipTo }}</textarea>
                        <label for="ship_to_input">Detail Alamat Pengiriman (Ship To) <span class="text-danger">*</span></label>
                    </div>
                </div>
            </div>
        </div>

        {{-- 3. LINE ITEMS --}}
        <div class="card mb-4 border-0 shadow-sm">
            <div class="po-section-header flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                    <span class="po-section-step">3</span>
                    <h6 class="po-section-title mb-0">Daftar Item Purchase Order</h6>
                    <span class="badge bg-label-primary" id="items-count-badge">0 Items</span>
                </div>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <button type="button" class="btn btn-sm btn-primary shadow-xs btn-add-item-action" data-repeater-create="">
                        <i class="mdi mdi-plus me-1"></i> Add Item
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-primary shadow-xs btn-pull-pr-action" data-bs-toggle="modal" data-bs-target="#modalPullPrItems">
                        <i class="mdi mdi-file-import-outline me-1"></i> Tarik Item dari PR
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-info btn-add-header-title-action" id="btn-add-header-title-top">
                        <i class="mdi mdi-format-header-1 me-1"></i> Head Title
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary btn-add-custom-item-action" id="btn-add-custom-item-top">
                        <i class="mdi mdi-format-list-bulleted me-1"></i> Custom Item
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="form-invoice-repeater source-item">
                    @php
                        $unitList = ['Pcs', 'Set', 'Pail', 'Drum', 'Unit', 'Lot', 'Meter', 'Can', 'Hari', 'Bulan', 'Kg', 'Tube', 'Titik', 'Box', 'Roll', 'Liter', 'Lembar', 'Paket', 'Karton', 'Pallet', 'Botol', 'Batang'];
                    @endphp
                    @if (@$purchase)
                        <div class="mb-0" data-repeater-list="group-a">
                            @php $no = 1; @endphp
                            @foreach ($dPurchase as $item)
                                @if (($item->category ?? '') === 'Header')
                                    <div class="repeater-wrapper header-row-wrapper" data-repeater-item="" data-category="Header">
                                        <div class="position-relative border-bottom p-3" style="background:#f8f9ff !important; border-left: 4px solid #696cff !important;">
                                            <input type="hidden" class="invoice-item-detail-id" name="detail_id[]" value="{{ $item->id }}">
                                            <input type="hidden" name="pr_detail_id[]" value="">
                                            <input type="hidden" class="item-category-value" name="item_category[]" value="Header">
                                            <input type="hidden" name="id_product[]" value="">
                                            <input type="hidden" name="id_unit[]" value="">
                                            <input type="hidden" name="id_rental_accessory[]" value="">
                                            <input type="hidden" name="kondisi[]" value="">
                                            <input type="hidden" class="invoice-item-price" name="price[]" value="0">
                                            <input type="hidden" class="invoice-item-qty" name="qty[]" value="0">
                                            <input type="hidden" class="invoice-item-info" name="info_qty[]" value="">
                                            <input type="hidden" class="invoice-item-disc" name="disc[]" value="0">
                                            <input type="hidden" class="invoice-item-amount" name="amount[]" value="0">

                                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                                <div class="btn btn-sm btn-icon btn-label-secondary btn-drag-handle cursor-move flex-shrink-0" title="Geser (drag & drop) untuk memindahkan posisi" style="cursor: grab;">
                                                    <i class="mdi mdi-drag-vertical fs-5"></i>
                                                </div>
                                                <span class="badge bg-primary text-uppercase" style="font-size:10.5px; letter-spacing:0.5px;">
                                                    <i class="mdi mdi-bookmark-outline me-1"></i>Head Title
                                                </span>
                                                <div class="flex-grow-1" style="min-width: 250px;">
                                                    <input type="text" class="form-control form-control-sm fw-bold text-primary header-title-input invoice-item-detail-product"
                                                        name="product[]" placeholder="Head Title (e.g. A. SCOPE OF WORK, B. SPAREPART) *"
                                                        value="{{ $item->product }}" required>
                                                </div>
                                                <span class="badge bg-label-primary section-subtotal-badge text-nowrap" style="font-size:11px;">Subtotal: Rp 0</span>
                                                <button type="button" class="btn btn-sm btn-icon btn-label-danger btn-del ms-auto" data-repeater-delete="" title="Hapus Head Title">
                                                    <i class="mdi mdi-delete-outline"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="repeater-wrapper" data-repeater-item="">
                                        <div class="position-relative repeater-row-inner">
                                            <div class="d-flex align-items-start gap-2">
                                                <div class="btn-drag-handle mt-1 flex-shrink-0" title="Geser urutan baris">
                                                    <i class="mdi mdi-drag-vertical fs-5"></i>
                                                </div>
                                                <div class="row w-100 g-2">
                                                    <input type="hidden" class="invoice-item-detail-id" name="detail_id[]" value="{{ $item->id }}">
                                                    <input type="hidden" name="pr_detail_id[]" value="">

                                                    {{-- Item Selection & Category Selector --}}
                                                    <div class="col-lg-5 col-12 mb-2 mb-lg-0 item-fields">
                                                        <div class="item-category-radios">
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input item-category-radio" type="radio" value="Sparepart"
                                                                    {{ ($item->category ?? 'Sparepart') != 'Unit' && ($item->category ?? '') != 'Accessories' && ($item->category ?? '') != 'Custom' ? 'checked' : '' }}>
                                                                <label class="form-check-label">Sparepart</label>
                                                            </div>
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input item-category-radio" type="radio" value="Unit"
                                                                    {{ ($item->category ?? '') == 'Unit' ? 'checked' : '' }}>
                                                                <label class="form-check-label">Unit Global</label>
                                                            </div>
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input item-category-radio" type="radio" value="Accessories"
                                                                    {{ ($item->category ?? '') == 'Accessories' ? 'checked' : '' }}>
                                                                <label class="form-check-label">Aksesoris</label>
                                                            </div>
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input item-category-radio" type="radio" value="Custom"
                                                                    {{ ($item->category ?? '') == 'Custom' ? 'checked' : '' }}>
                                                                <label class="form-check-label">Custom</label>
                                                            </div>
                                                        </div>
                                                        <input type="hidden" class="item-category-value" name="item_category[]"
                                                            value="{{ ($item->category ?? '') == 'Unit' ? 'Unit' : (($item->category ?? '') == 'Accessories' ? 'Accessories' : (($item->category ?? '') == 'Custom' ? 'Custom' : 'Sparepart')) }}">

                                                        <div class="field-product-sparepart">
                                                            <select class="form-select form-select-sm select2-product-po" name="id_product[]">
                                                                <option value="">Cari SKU / Product...</option>
                                                                @if (!empty($item->id_product))
                                                                    <option value="{{ $item->id_product }}"
                                                                        data-label="{{ $item->product ?? '' }}"
                                                                        data-unit="{{ $item->info_qty ?? 'Pcs' }}"
                                                                        selected>
                                                                        {{ $item->product ?? 'Product #' . $item->id_product }}
                                                                    </option>
                                                                @endif
                                                            </select>
                                                        </div>
                                                        <div class="field-product-unit" style="display:none;">
                                                            <select class="form-select form-select-sm select2-unit-po" name="id_unit[]">
                                                                <option value="">Cari Unit...</option>
                                                                @foreach ($units ?? [] as $u)
                                                                    <option value="{{ $u->id }}"
                                                                        data-sku="{{ $u->sku }}"
                                                                        data-name="{{ $u->brand }} {{ $u->model }}"
                                                                        data-label="{{ $u->sku }} - {{ $u->brand }} {{ $u->model }}"
                                                                        {{ $item->id_unit == $u->id ? 'selected' : '' }}>
                                                                        {{ $u->sku }} {{ $u->brand }} {{ $u->model }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                            <select class="form-select form-select-sm mt-1 select-kondisi-unit" name="kondisi[]">
                                                                <option value="Baru" {{ ($item->kondisi ?? 'Baru') == 'Baru' ? 'selected' : '' }}>Unit Baru (masuk stok jual)</option>
                                                                <option value="Second" {{ ($item->kondisi ?? '') == 'Second' ? 'selected' : '' }}>Unit Second (jadi Fixed Asset, QC dulu)</option>
                                                            </select>
                                                        </div>
                                                        <div class="field-product-accessory" style="display:none;">
                                                            <select class="form-select form-select-sm select2-accessory-po" name="id_rental_accessory[]">
                                                                <option value="">Pilih Aksesoris Rental...</option>
                                                                @foreach ($accessories ?? [] as $acc)
                                                                    <option value="{{ $acc->id }}"
                                                                        data-code="{{ $acc->code }}"
                                                                        data-name="{{ $acc->name }}"
                                                                        data-category="{{ $acc->category }}"
                                                                        data-label="{{ $acc->name }} ({{ $acc->code ?? '-' }})"
                                                                        {{ ($item->id_rental_accessory ?? '') == $acc->id ? 'selected' : '' }}>
                                                                        {{ $acc->name }} [{{ strtoupper($acc->category) }}] {{ $acc->code ? "({$acc->code})" : '' }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="field-product-custom" style="display:none;">
                                                            <textarea class="form-control form-control-sm invoice-item-detail-product"
                                                                name="product[]" rows="2"
                                                                placeholder="Nama/Deskripsi Item Custom...">{{ $item->product }}</textarea>
                                                        </div>
                                                    </div>

                                                    {{-- Price & Interactive Tax Calculation --}}
                                                    <div class="col-lg-3 col-md-4 col-12 mb-2 mb-md-0">
                                                        <div class="item-col-label">
                                                            <label class="form-label text-muted small mb-0 fw-semibold">Harga Satuan</label>
                                                        </div>
                                                        <div class="input-group input-group-sm" data-price="{{ $no }}">
                                                            <span class="input-group-text bg-light text-muted fw-semibold">Rp</span>
                                                            <input type="text" class="form-control invoice-item-price-label text-end fw-semibold"
                                                                id="priceLabel-{{ $no }}" data-id="{{ $no }}" name="harga"
                                                                placeholder="0" data-type="currency" min="0"
                                                                value="{{ fmod((float)$item->price, 1) != 0 ? number_format((float)$item->price, 2, ',', '.') : number_format((float)$item->price, 0, ',', '.') }}">
                                                            <input class="form-control invoice-item-price" type="number" step="any"
                                                                name="price[]" id="price-{{ $no }}"
                                                                value="{{ (float) $item->price }}" hidden>
                                                        </div>
                                                        <div class="price-tax-hint mt-1 small d-none" style="font-size: 11px; line-height: 1.35; background: #f0f2ff; padding: 5px 8px; border-radius: 6px; border-left: 3px solid #696cff;">
                                                            <div class="hint-calc-wrapper">
                                                                <div class="text-secondary mb-1">
                                                                    <i class="mdi mdi-calculator-variant-outline text-primary me-1"></i>Inc. PPN (11%):<br>
                                                                    DPP (Exc. PPN): <strong class="text-primary exc-ppn-val">Rp 0</strong><br>
                                                                    <span class="ppn-val text-muted" style="font-size: 10px;">PPN (11%): Rp 0</span>
                                                                </div>
                                                                <button type="button" class="btn btn-xs btn-primary py-0 px-2 btn-apply-dpp" style="font-size: 10px; height: 22px;">
                                                                    <i class="mdi mdi-check me-1"></i> Gunakan Harga DPP
                                                                </button>
                                                            </div>
                                                            <div class="hint-applied-wrapper d-none">
                                                                <div class="d-flex align-items-center justify-content-between text-success">
                                                                    <span style="font-size: 10px;">
                                                                        <i class="mdi mdi-check-circle-outline me-1"></i>DPP: <strong class="applied-dpp-text">Rp 0</strong>
                                                                    </span>
                                                                    <button type="button" class="btn btn-xs btn-link text-danger p-0 ms-1 btn-reset-dpp" style="font-size: 10px; text-decoration: underline; line-height: 1;">
                                                                        Batal
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    {{-- Qty & Live Stock Hint --}}
                                                    <div class="col-lg-1 col-md-2 col-4">
                                                        <div class="item-col-label">
                                                            <label class="form-label text-muted small mb-0 fw-semibold">Qty</label>
                                                        </div>
                                                        <input type="number" class="form-control form-control-sm invoice-item-qty text-center"
                                                            placeholder="1" name="qty[]" id="qty-{{ $no }}"
                                                            data-id="{{ $no }}" min="1" value="{{ $item->qty }}">
                                                    </div>

                                                    {{-- Info Qty (Unit Satuan) --}}
                                                    <div class="col-lg-1 col-md-2 col-4">
                                                        <div class="item-col-label">
                                                            <label class="form-label text-muted small mb-0 fw-semibold">Satuan</label>
                                                        </div>
                                                        <select class="form-select form-select-sm invoice-item-info select2-info-qty"
                                                            id="info-qty-{{ $no }}" data-id="{{ $no }}" name="info_qty[]">
                                                            <option disabled value="">-Pilih-</option>
                                                            @foreach ($unitList as $uOpt)
                                                                <option value="{{ $uOpt }}" {{ strcasecmp($item->info_qty, $uOpt) === 0 ? 'selected' : '' }}>{{ $uOpt }}</option>
                                                            @endforeach
                                                            @if (!empty($item->info_qty) && !collect($unitList)->contains(fn($u) => strcasecmp($u, $item->info_qty) === 0))
                                                                <option value="{{ $item->info_qty }}" selected>{{ $item->info_qty }}</option>
                                                            @endif
                                                        </select>
                                                    </div>

                                                    {{-- Disc (%) --}}
                                                    <div class="col-lg-1 col-md-2 col-4">
                                                        <div class="item-col-label">
                                                            <label class="form-label text-muted small mb-0 fw-semibold">Disc (%)</label>
                                                        </div>
                                                        <div class="input-group input-group-sm" data-disc="{{ $no }}">
                                                            <input type="text" class="form-control invoice-item-disc text-center"
                                                                id="disc-{{ $no }}" data-id="{{ $no }}" name="disc[]" placeholder="0"
                                                                value="{{ old('disc[]', $item->disc) }}">
                                                        </div>
                                                    </div>

                                                    {{-- Amount & Delete --}}
                                                    <div class="col-lg-1 col-md-2 col-12 text-md-end">
                                                        <div class="item-col-label justify-content-end">
                                                            <label class="form-label text-muted small mb-0 fw-semibold">Amount</label>
                                                        </div>
                                                        <div class="amount-label fw-bold text-primary text-end d-flex align-items-center justify-content-md-end justify-content-start" id="amount-label-{{ $no }}" data-id="{{ $no }}">
                                                            {{ number_format($item->amount, 0, ',', '.') }}
                                                        </div>
                                                        <input type="number" class="form-control invoice-item-amount"
                                                            name="amount[]" id="amount-{{ $no }}" data-id="{{ $no }}"
                                                            value="{{ old('amount[]', $item->amount) }}" hidden>
                                                    </div>
                                                </div>
                                                <div class="ms-1 flex-shrink-0 d-flex flex-column align-items-center">
                                                    <div class="item-col-label d-none d-md-block" style="height: 24px;"></div>
                                                    <button type="button" class="btn btn-sm btn-icon btn-label-danger btn-del"
                                                        data-repeater-delete="" title="Hapus Baris Item">
                                                        <i class="mdi mdi-delete-outline"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                @php $no++; @endphp
                            @endforeach
                        </div>
                    @elseif (!empty($prefillItems))
                        <div class="mb-0" data-repeater-list="group-a">
                            @foreach ($prefillItems as $i => $pi)
                                @php $rno = $i + 1; @endphp
                                <div class="repeater-wrapper" data-repeater-item="">
                                    <div class="position-relative repeater-row-inner">
                                        <div class="d-flex align-items-start gap-2">
                                            <div class="btn-drag-handle mt-1 flex-shrink-0" title="Geser urutan baris">
                                                <i class="mdi mdi-drag-vertical fs-5"></i>
                                            </div>
                                            <div class="row w-100 g-2">
                                                <input type="hidden" class="invoice-item-detail-id" name="detail_id[]" value="">
                                                <input type="hidden" name="pr_detail_id[]" value="{{ $pi['pr_detail_id'] ?? '' }}">

                                                {{-- Item Selection & Category Selector --}}
                                                <div class="col-lg-5 col-12 mb-2 mb-lg-0 item-fields">
                                                    <div class="item-category-radios">
                                                        <div class="form-check form-check-inline">
                                                            <input class="item-category-radio" type="radio" value="Sparepart" checked>
                                                            <label class="form-check-label">Sparepart</label>
                                                        </div>
                                                        <div class="form-check form-check-inline">
                                                            <input class="item-category-radio" type="radio" value="Unit">
                                                            <label class="form-check-label">Unit Global</label>
                                                        </div>
                                                        <div class="form-check form-check-inline">
                                                            <input class="item-category-radio" type="radio" value="Accessories">
                                                            <label class="form-check-label">Aksesoris</label>
                                                        </div>
                                                        <div class="form-check form-check-inline">
                                                            <input class="item-category-radio" type="radio" value="Custom">
                                                            <label class="form-check-label">Custom</label>
                                                        </div>
                                                    </div>
                                                    <input type="hidden" class="item-category-value" name="item_category[]" value="Sparepart">

                                                    <div class="field-product-sparepart">
                                                        <select class="form-select form-select-sm select2-product-po" name="id_product[]">
                                                            <option value="">Cari SKU / Product...</option>
                                                            @if (!empty($pi['id_product']))
                                                                <option value="{{ $pi['id_product'] }}"
                                                                    data-label="{{ $pi['label'] ?? '' }}"
                                                                    data-unit="{{ $pi['unit'] ?? 'Pcs' }}"
                                                                    selected>
                                                                    {{ $pi['label'] ?? 'Product #' . $pi['id_product'] }}
                                                                </option>
                                                            @endif
                                                        </select>
                                                    </div>
                                                    <div class="field-product-unit" style="display:none;">
                                                        <select class="form-select form-select-sm select2-unit-po" name="id_unit[]">
                                                            <option value="">Cari Unit...</option>
                                                            @foreach ($units ?? [] as $u)
                                                                <option value="{{ $u->id }}"
                                                                    data-sku="{{ $u->sku }}"
                                                                    data-name="{{ $u->brand }} {{ $u->model }}"
                                                                    data-label="{{ $u->sku }} - {{ $u->brand }} {{ $u->model }}">
                                                                    {{ $u->sku }} {{ $u->brand }} {{ $u->model }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        <select class="form-select form-select-sm mt-1 select-kondisi-unit" name="kondisi[]">
                                                            <option value="Baru" selected>Unit Baru (masuk stok jual)</option>
                                                            <option value="Second">Unit Second (jadi Fixed Asset, QC dulu)</option>
                                                        </select>
                                                    </div>
                                                    <div class="field-product-accessory" style="display:none;">
                                                        <select class="form-select form-select-sm select2-accessory-po" name="id_rental_accessory[]">
                                                            <option value="">Pilih Aksesoris Rental...</option>
                                                            @foreach ($accessories ?? [] as $acc)
                                                                <option value="{{ $acc->id }}"
                                                                    data-code="{{ $acc->code }}"
                                                                    data-name="{{ $acc->name }}"
                                                                    data-category="{{ $acc->category }}"
                                                                    data-label="{{ $acc->name }} ({{ $acc->code ?? '-' }})">
                                                                    {{ $acc->name }} [{{ strtoupper($acc->category) }}] {{ $acc->code ? "({$acc->code})" : '' }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="field-product-custom" style="display:none;">
                                                        <textarea class="form-control form-control-sm invoice-item-detail-product"
                                                            name="product[]" rows="2"
                                                            placeholder="Nama/Deskripsi Item Custom..."></textarea>
                                                    </div>
                                                </div>

                                                {{-- Price & Tax Calc --}}
                                                <div class="col-lg-3 col-md-4 col-12 mb-2 mb-md-0">
                                                    <div class="item-col-label">
                                                        <label class="form-label text-muted small mb-0 fw-semibold">Harga Satuan</label>
                                                    </div>
                                                    <div class="input-group input-group-sm" data-price="{{ $rno }}">
                                                        <span class="input-group-text bg-light text-muted fw-semibold">Rp</span>
                                                        <input type="text" class="form-control invoice-item-price-label text-end fw-semibold"
                                                            id="priceLabel-{{ $rno }}" data-id="{{ $rno }}" name="harga"
                                                            placeholder="0" data-type="currency" min="0" value="">
                                                        <input class="form-control invoice-item-price" type="number" step="any"
                                                            name="price[]" id="price-{{ $rno }}" value="" hidden>
                                                    </div>
                                                    <div class="price-tax-hint mt-1 small d-none" style="font-size: 11px; line-height: 1.35; background: #f0f2ff; padding: 5px 8px; border-radius: 6px; border-left: 3px solid #696cff;">
                                                        <div class="hint-calc-wrapper">
                                                            <div class="text-secondary mb-1">
                                                                <i class="mdi mdi-calculator-variant-outline text-primary me-1"></i>Inc. PPN (11%):<br>
                                                                DPP (Exc. PPN): <strong class="text-primary exc-ppn-val">Rp 0</strong><br>
                                                                <span class="ppn-val text-muted" style="font-size: 10px;">PPN (11%): Rp 0</span>
                                                            </div>
                                                            <button type="button" class="btn btn-xs btn-primary py-0 px-2 btn-apply-dpp" style="font-size: 10px; height: 22px;">
                                                                <i class="mdi mdi-check me-1"></i> Gunakan Harga DPP
                                                            </button>
                                                        </div>
                                                        <div class="hint-applied-wrapper d-none">
                                                            <div class="d-flex align-items-center justify-content-between text-success">
                                                                <span style="font-size: 10px;">
                                                                    <i class="mdi mdi-check-circle-outline me-1"></i>DPP: <strong class="applied-dpp-text">Rp 0</strong>
                                                                </span>
                                                                <button type="button" class="btn btn-xs btn-link text-danger p-0 ms-1 btn-reset-dpp" style="font-size: 10px; text-decoration: underline; line-height: 1;">
                                                                    Batal
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- Qty & Live Stock Hint --}}
                                                <div class="col-lg-1 col-md-2 col-4">
                                                    <div class="item-col-label">
                                                        <label class="form-label text-muted small mb-0 fw-semibold">Qty</label>
                                                    </div>
                                                    <input type="number" class="form-control form-control-sm invoice-item-qty text-center"
                                                        placeholder="1" name="qty[]" id="qty-{{ $rno }}" data-id="{{ $rno }}"
                                                        data-pr-remaining="{{ $pi['qty'] ?? '' }}"
                                                        min="1" value="{{ $pi['qty'] ?? 1 }}">
                                                    <div class="qty-stock-hint text-info small d-none" id="qty-stock-hint-{{ $rno }}" style="font-size: 10px; line-height: 1.2;"></div>
                                                </div>

                                                {{-- Info Qty (Satuan) --}}
                                                <div class="col-lg-1 col-md-2 col-4">
                                                    <div class="item-col-label">
                                                        <label class="form-label text-muted small mb-0 fw-semibold">Satuan</label>
                                                    </div>
                                                    <select class="form-select form-select-sm invoice-item-info select2-info-qty" id="info-qty-{{ $rno }}"
                                                        data-id="{{ $rno }}" name="info_qty[]">
                                                        <option disabled value="">-Pilih-</option>
                                                        @php $defUnit = !empty($pi['unit']) ? $pi['unit'] : 'Pcs'; @endphp
                                                        @foreach ($unitList as $uOpt)
                                                            <option value="{{ $uOpt }}" {{ strcasecmp($defUnit, $uOpt) === 0 ? 'selected' : '' }}>{{ $uOpt }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                {{-- Disc (%) --}}
                                                <div class="col-lg-1 col-md-2 col-4">
                                                    <div class="item-col-label">
                                                        <label class="form-label text-muted small mb-0 fw-semibold">Disc (%)</label>
                                                    </div>
                                                    <div class="input-group input-group-sm" data-disc="{{ $rno }}">
                                                        <input type="text" class="form-control invoice-item-disc text-center"
                                                            id="disc-{{ $rno }}" data-id="{{ $rno }}" name="disc[]" placeholder="0"
                                                            value="0">
                                                    </div>
                                                </div>

                                                {{-- Amount & Delete --}}
                                                <div class="col-lg-1 col-md-2 col-12 text-md-end">
                                                    <div class="item-col-label justify-content-end">
                                                        <label class="form-label text-muted small mb-0 fw-semibold">Amount</label>
                                                    </div>
                                                    <div class="amount-label fw-bold text-primary text-end d-flex align-items-center justify-content-md-end justify-content-start" id="amount-label-{{ $rno }}" data-id="{{ $rno }}">0</div>
                                                    <input type="number" class="form-control invoice-item-amount"
                                                        name="amount[]" id="amount-{{ $rno }}" data-id="{{ $rno }}"
                                                        value="" hidden>
                                                </div>
                                            </div>
                                            <div class="ms-1 flex-shrink-0 d-flex flex-column align-items-center">
                                                <div class="item-col-label d-none d-md-block" style="height: 24px;"></div>
                                                <button type="button" class="btn btn-sm btn-icon btn-label-danger btn-del"
                                                    data-repeater-delete="" title="Hapus Baris Item">
                                                    <i class="mdi mdi-delete-outline"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="mb-0" data-repeater-list="group-a">
                            <div class="repeater-wrapper" data-repeater-item="">
                                <div class="position-relative repeater-row-inner">
                                    <div class="d-flex align-items-start gap-2">
                                        <div class="btn-drag-handle mt-1 flex-shrink-0" title="Geser urutan baris">
                                            <i class="mdi mdi-drag-vertical fs-5"></i>
                                        </div>
                                        <div class="row w-100 g-2">
                                            <input type="hidden" class="invoice-item-detail-id" name="detail_id[]" value="">
                                            <input type="hidden" name="pr_detail_id[]" value="">

                                            {{-- Item Selection & Category Selector --}}
                                            <div class="col-lg-5 col-12 mb-2 mb-lg-0 item-fields">
                                                <div class="item-category-radios">
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input item-category-radio" type="radio" value="Sparepart" checked>
                                                        <label class="form-check-label">Sparepart</label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input item-category-radio" type="radio" value="Unit">
                                                        <label class="form-check-label">Unit Global</label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input item-category-radio" type="radio" value="Accessories">
                                                        <label class="form-check-label">Aksesoris</label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input item-category-radio" type="radio" value="Custom">
                                                        <label class="form-check-label">Custom</label>
                                                    </div>
                                                </div>
                                                <input type="hidden" class="item-category-value" name="item_category[]" value="Sparepart">

                                                <div class="field-product-sparepart">
                                                    <select class="form-select form-select-sm select2-product-po" name="id_product[]">
                                                        <option value="">Cari SKU / Product...</option>
                                                    </select>
                                                </div>
                                                <div class="field-product-unit" style="display:none;">
                                                    <select class="form-select form-select-sm select2-unit-po" name="id_unit[]">
                                                        <option value="">Cari Unit...</option>
                                                        @foreach ($units ?? [] as $u)
                                                            <option value="{{ $u->id }}"
                                                                data-sku="{{ $u->sku }}"
                                                                data-name="{{ $u->brand }} {{ $u->model }}"
                                                                data-label="{{ $u->sku }} - {{ $u->brand }} {{ $u->model }}">
                                                                {{ $u->sku }} {{ $u->brand }} {{ $u->model }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <select class="form-select form-select-sm mt-1 select-kondisi-unit" name="kondisi[]">
                                                        <option value="Baru" selected>Unit Baru (masuk stok jual)</option>
                                                        <option value="Second">Unit Second (jadi Fixed Asset, QC dulu)</option>
                                                    </select>
                                                </div>
                                                <div class="field-product-accessory" style="display:none;">
                                                    <select class="form-select form-select-sm select2-accessory-po" name="id_rental_accessory[]">
                                                        <option value="">Pilih Aksesoris Rental...</option>
                                                        @foreach ($accessories ?? [] as $acc)
                                                            <option value="{{ $acc->id }}"
                                                                data-code="{{ $acc->code }}"
                                                                data-name="{{ $acc->name }}"
                                                                data-category="{{ $acc->category }}"
                                                                data-label="{{ $acc->name }} ({{ $acc->code ?? '-' }})">
                                                                {{ $acc->name }} [{{ strtoupper($acc->category) }}] {{ $acc->code ? "({$acc->code})" : '' }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="field-product-custom" style="display:none;">
                                                    <textarea class="form-control form-control-sm invoice-item-detail-product"
                                                        name="product[]" rows="2"
                                                        placeholder="Nama/Deskripsi Item Custom..."></textarea>
                                                </div>
                                            </div>

                                            {{-- Price & Tax Calc --}}
                                            <div class="col-lg-3 col-md-4 col-12 mb-2 mb-md-0">
                                                <div class="item-col-label">
                                                    <label class="form-label text-muted small mb-0 fw-semibold">Harga Satuan</label>
                                                </div>
                                                <div class="input-group input-group-sm" data-price="1">
                                                    <span class="input-group-text bg-light text-muted fw-semibold">Rp</span>
                                                    <input type="text" class="form-control invoice-item-price-label text-end fw-semibold"
                                                        id="priceLabel-1" data-id="1" name="harga"
                                                        placeholder="0" data-type="currency" min="0"
                                                        value="{{ old('price[]') }}">
                                                    <input class="form-control invoice-item-price" type="number" step="any"
                                                        name="price[]" id="price-1" value="{{ old('price[]') }}" hidden>
                                                </div>
                                                <div class="price-tax-hint mt-1 small d-none" style="font-size: 11px; line-height: 1.35; background: #f0f2ff; padding: 5px 8px; border-radius: 6px; border-left: 3px solid #696cff;">
                                                    <div class="hint-calc-wrapper">
                                                        <div class="text-secondary mb-1">
                                                            <i class="mdi mdi-calculator-variant-outline text-primary me-1"></i>Inc. PPN (11%):<br>
                                                            DPP (Exc. PPN): <strong class="text-primary exc-ppn-val">Rp 0</strong><br>
                                                            <span class="ppn-val text-muted" style="font-size: 10px;">PPN (11%): Rp 0</span>
                                                        </div>
                                                        <button type="button" class="btn btn-xs btn-primary py-0 px-2 btn-apply-dpp" style="font-size: 10px; height: 22px;">
                                                            <i class="mdi mdi-check me-1"></i> Gunakan Harga DPP
                                                        </button>
                                                    </div>
                                                    <div class="hint-applied-wrapper d-none">
                                                        <div class="d-flex align-items-center justify-content-between text-success">
                                                            <span style="font-size: 10px;">
                                                                <i class="mdi mdi-check-circle-outline me-1"></i>DPP: <strong class="applied-dpp-text">Rp 0</strong>
                                                            </span>
                                                            <button type="button" class="btn btn-xs btn-link text-danger p-0 ms-1 btn-reset-dpp" style="font-size: 10px; text-decoration: underline; line-height: 1;">
                                                                Batal
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Qty & Live Stock Hint --}}
                                            <div class="col-lg-1 col-md-2 col-4">
                                                <div class="item-col-label">
                                                    <label class="form-label text-muted small mb-0 fw-semibold">Qty</label>
                                                </div>
                                                <input type="number" class="form-control form-control-sm invoice-item-qty text-center"
                                                    placeholder="1" name="qty[]" id="qty-1" data-id="1"
                                                    min="1" value="{{ old('qty[]') }}">
                                            </div>

                                            {{-- Info Qty (Satuan) --}}
                                            <div class="col-lg-1 col-md-2 col-4">
                                                <div class="item-col-label">
                                                    <label class="form-label text-muted small mb-0 fw-semibold">Satuan</label>
                                                </div>
                                                <select class="form-select form-select-sm invoice-item-info select2-info-qty" id="info-qty-1"
                                                    data-id="1" name="info_qty[]">
                                                    <option disabled value="">-Pilih-</option>
                                                    @foreach ($unitList as $uOpt)
                                                        <option value="{{ $uOpt }}" {{ $uOpt === 'Pcs' ? 'selected' : '' }}>{{ $uOpt }}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            {{-- Disc (%) --}}
                                            <div class="col-lg-1 col-md-2 col-4">
                                                <div class="item-col-label">
                                                    <label class="form-label text-muted small mb-0 fw-semibold">Disc (%)</label>
                                                </div>
                                                <div class="input-group input-group-sm" data-disc="1">
                                                    <input type="text" class="form-control invoice-item-disc text-center"
                                                        id="disc-1" data-id="1" name="disc[]" placeholder="0"
                                                        value="{{ old('disc[]', 0) }}">
                                                </div>
                                            </div>

                                            {{-- Amount & Delete --}}
                                            <div class="col-lg-1 col-md-2 col-12 text-md-end">
                                                <div class="item-col-label justify-content-end">
                                                    <label class="form-label text-muted small mb-0 fw-semibold">Amount</label>
                                                </div>
                                                <div class="amount-label fw-bold text-primary text-end d-flex align-items-center justify-content-md-end justify-content-start" id="amount-label-1" data-id="1">
                                                    {{ old(strval('amount[]')) ?? '0' }}
                                                </div>
                                                <input type="number" class="form-control invoice-item-amount"
                                                    name="amount[]" id="amount-1" data-id="1"
                                                    value="{{ old('amount[]') }}" hidden>
                                            </div>
                                        </div>
                                        <div class="ms-1 flex-shrink-0 d-flex flex-column align-items-center">
                                            <div class="item-col-label d-none d-md-block" style="height: 24px;"></div>
                                            <button type="button" class="btn btn-sm btn-icon btn-label-danger btn-del"
                                                data-repeater-delete="" title="Hapus Baris Item">
                                                <i class="mdi mdi-delete-outline"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                    <div class="d-flex flex-wrap gap-2 p-3 border-top bg-light-subtle align-items-center">
                        <button type="button" class="btn btn-sm btn-primary shadow-xs btn-add-item-action" data-repeater-create="">
                            <i class="mdi mdi-plus me-1"></i> Add Item
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-primary shadow-xs btn-pull-pr-action" data-bs-toggle="modal" data-bs-target="#modalPullPrItems">
                            <i class="mdi mdi-file-import-outline me-1"></i> Tarik Item dari PR
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-info btn-add-header-title-action" id="btn-add-header-title-bottom">
                            <i class="mdi mdi-format-header-1 me-1"></i> Add Head Title
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary btn-add-custom-item-action" id="btn-add-custom-item-bottom">
                            <i class="mdi mdi-format-list-bulleted me-1"></i> Add Custom Item
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- 4. REMARKS & FINANCIAL SUMMARY (2-COLUMN LAYOUT) --}}
        <div class="row g-4 mb-4">
            {{-- Left Column: Catatan / Remarks PO --}}
            <div class="col-lg-6 col-12">
                <div class="card border-0 shadow-sm h-100">
                    <div class="po-section-header">
                        <div class="po-section-title">
                            <span class="po-section-step">4</span>
                            <span>Catatan &amp; Instruksi PO</span>
                        </div>
                        <span class="badge bg-label-secondary px-2 py-1" style="font-size: 10.5px;">PO Remarks</span>
                    </div>
                    <div class="card-body p-4 d-flex flex-column">
                        <div class="form-floating form-floating-outline mb-3 flex-grow-1">
                            <textarea class="form-control" rows="5" style="height: 140px;" placeholder="Tuliskan catatan khusus atau instruksi pengiriman PO di sini..."
                                id="po_note" name="note">{{ @$purchase->note }}</textarea>
                            <label for="po_note">Catatan Tambahan (dicetak di dokumen PO)</label>
                        </div>
                        <div class="p-3 rounded-3" style="background: #f8f9fc; border: 1px dashed #dbe0eb;">
                            <div class="d-flex align-items-center gap-2 text-primary fw-bold small mb-1">
                                <i class="mdi mdi-lightbulb-on-outline"></i> Petunjuk Singkat:
                            </div>
                            <ul class="text-muted small mb-0 ps-3" style="font-size: 11.5px; line-height: 1.5;">
                                <li>Gunakan fitur <strong>Tarik Item dari PR</strong> untuk menggabungkan beberapa Purchase Request yang disetujui.</li>
                                <li>Centang opsi <strong>Tax (PPN 12%)</strong> jika pembelian dikenakan pajak pertambahan nilai.</li>
                                <li>Draf PO disimpan otomatis di browser dan dapat dipulihkan sewaktu-waktu jika halaman tertutup.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Column: Total Summary (Finance Summary) --}}
            <div class="col-lg-6 col-12">
                <div class="summary-card-inner shadow-sm">
                    <div class="po-section-header">
                        <div class="po-section-title">
                            <span class="avatar avatar-xs bg-label-primary rounded me-2 d-flex align-items-center justify-content-center" style="width:24px; height:24px;">
                                <i class="mdi mdi-calculator text-primary" style="font-size: 14px;"></i>
                            </span>
                            <span>Rincian Pembayaran</span>
                        </div>
                        <span class="badge bg-label-primary px-2 py-1" style="font-size:10px;">IDR SUMMARY</span>
                    </div>
                    <div class="p-4">
                        {{-- Subtotal --}}
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="text-muted fw-semibold">Subtotal Item</span>
                            <div class="text-end">
                                <span class="fw-bold text-dark fs-6 subtotal-label" id="subtotal-label" data-id="1">
                                    {{ old('subtotal', @$purchase->subtotal ? 'RP ' . number_format(@$purchase->subtotal, 0, '', '.') : 'RP 0') }}
                                </span>
                                <input type="number" id="subtotal" name="subtotal"
                                    value="{{ old('subtotal', @$purchase->subtotal ?? '') }}" hidden>
                            </div>
                        </div>

                        {{-- Global Discount --}}
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="text-muted fw-semibold">Diskon Global</span>
                            <div style="width: 170px;">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-light text-muted fw-semibold">Rp</span>
                                    <input type="text" id="diskon-label" class="form-control text-end fw-semibold"
                                        placeholder="0" data-type="currency"
                                        pattern="^[0-9]\d{0,2}(\.\d{3})*$"
                                        value="{{ old('diskon', @$purchase->diskon ? number_format(@$purchase->diskon, 0, '', '.') : '0') }}">
                                    <input type="number" name="diskon" id="diskon"
                                        value="{{ old('diskon', @$purchase->diskon ?? '0') }}" hidden>
                                </div>
                            </div>
                        </div>

                        {{-- Subtotal After Discount (hanya muncul jika ada nominal Diskon Global) --}}
                        <div class="d-flex justify-content-between align-items-center mb-3 subtotal-after-discount-row d-none">
                            <span class="text-muted fw-semibold">Subtotal After Discount</span>
                            <span class="fw-bold text-dark" id="subtotalAfterDiscountLabel">RP 0</span>
                        </div>

                        @php
                            $initHargaSebelumPpn = (@$purchase->subtotal ?? 0) - (@$purchase->diskon ?? 0);
                            $initVatActive = (@$purchase && ($purchase->vat == '12' || $purchase->vat == '11'));
                            $initDppNilaiLain = $initVatActive ? round($initHargaSebelumPpn * 11 / 12) : 0;
                        @endphp
                        {{-- DPP Nilai Lain (hanya muncul jika Tax PPN aktif) --}}
                        <div class="d-flex justify-content-between align-items-center mb-3 dpp-nilai-lain-row {{ $initVatActive ? '' : 'd-none' }}">
                            <span class="text-muted fw-semibold">DPP Nilai Lain</span>
                            <span class="fw-bold text-dark" id="dppNilaiLainLabel">{{ $initVatActive ? 'RP ' . number_format($initDppNilaiLain, 0, '', '.') : 'RP 0' }}</span>
                        </div>

                        {{-- Tax (PPN 12%) --}}
                        <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                            <div class="d-flex align-items-center gap-2">
                                <span class="text-muted fw-semibold">Tax (PPN 12%)</span>
                                <div class="form-check form-switch mb-0">
                                    <input class="form-check-input" type="checkbox" role="switch"
                                        id="taxSwitch" {{ (@$purchase->vat == '12' || @$purchase->vat == '11') ? 'checked' : '' }}>
                                </div>
                            </div>
                            <span class="fw-bold text-dark tax-amount-label" id="taxAmountLabel">
                                @if (@$purchase && ($purchase->vat == '12' || $purchase->vat == '11'))
                                    {{ 'RP ' . number_format(($purchase->subtotal - $purchase->diskon) * $purchase->vat / 100, 0, '', '.') }}
                                @endif
                            </span>
                            <input type="hidden" id="tax" name="tax" value="{{ old('tax', @$purchase->vat ?? '0') }}">
                        </div>

                        {{-- Delivery Cost --}}
                        <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                            <span class="text-muted fw-semibold">Biaya Pengiriman</span>
                            <div style="width: 170px;">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-light text-muted fw-semibold">Rp</span>
                                    <input type="text" id="delivery-cost-label" class="form-control text-end fw-semibold"
                                        placeholder="0" data-type="currency"
                                        pattern="^[0-9]\d{0,2}(\.\d{3})*$"
                                        value="{{ old('delivery_cost', @$purchase->delivery_cost ? number_format(@$purchase->delivery_cost, 0, '', '.') : '0') }}">
                                    <input type="number" name="delivery_cost" id="delivery-cost"
                                        value="{{ old('delivery_cost', @$purchase->delivery_cost ?? '0') }}" hidden>
                                </div>
                            </div>
                        </div>

                        {{-- Total Hero Box --}}
                        <div class="summary-grand-total d-flex justify-content-between align-items-center">
                            <div>
                                <div class="text-uppercase fw-bold text-primary" style="font-size: 11px; letter-spacing: 0.8px;">Total Tagihan PO</div>
                                <div class="text-muted small" style="font-size: 11px;">( Termasuk Pajak, Diskon &amp; Ongkir )</div>
                            </div>
                            <div class="fw-bolder text-primary fs-3 harga-total-label" id="hargaTotalLabel" data-id="1" style="letter-spacing: -0.5px;">
                                {{ old('harga_total', @$purchase->total ? 'RP ' . number_format(@$purchase->total, 0, '', '.') : 'RP 0') }}
                            </div>
                            <input type="number" id="hargaTotal" name="harga_total"
                                value="{{ old('harga_total', @$purchase->total ?? '') }}" hidden>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sticky Floating Bottom Action Bar --}}
        <div class="po-sticky-footer d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('purchase.index') }}" class="btn btn-label-secondary">
                    <i class="mdi mdi-arrow-left me-1"></i> Batal / Kembali
                </a>
                <span class="text-muted small d-none d-md-inline">
                    Pastikan rincian harga dan supplier sudah sesuai sebelum disimpan.
                </span>
            </div>
            <div class="d-flex align-items-center gap-2">
                <button type="submit" class="btn btn-primary shadow-sm px-4">
                    <i class="mdi mdi-content-save-outline me-1"></i> Simpan Dokumen PO
                </button>
            </div>
        </div>
    </form>

    <!-- Modal: Tambah Supplier Baru -->
    <div class="modal fade" id="quickAddSupplierModal" tabindex="-1" aria-labelledby="quickAddSupplierModalTitle" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 520px;">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 14px; overflow: hidden;">
                <div class="modal-header border-bottom py-3 px-4 bg-white d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-3">
                        <div class="avatar avatar-md flex-shrink-0">
                            <span class="avatar-initial rounded-3 bg-label-primary shadow-xs">
                                <i class="mdi mdi-domain-plus font-22"></i>
                            </span>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold text-dark mb-0" id="quickAddSupplierModalTitle">Supplier Baru</h5>
                            <small class="text-muted font-12">Daftarkan data master vendor / supplier baru</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 bg-white">
                    <div id="qsError" class="alert alert-danger d-none d-flex align-items-center py-2 px-3 mb-3 font-13" role="alert">
                        <i class="mdi mdi-alert-circle-outline me-2 font-16 flex-shrink-0"></i>
                        <span id="qsErrorText"></span>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold text-dark font-13" for="qsCode">
                                Kode Supplier <span class="text-danger">*</span>
                            </label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text bg-light border-end-0 text-muted">
                                    <i class="mdi mdi-barcode"></i>
                                </span>
                                <input type="text" class="form-control border-start-0 ps-1" id="qsCode" placeholder="Contoh: SUP-001" autocomplete="off">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold text-dark font-13" for="qsInfo">
                                Kategori Asal <span class="text-danger">*</span>
                            </label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text bg-light border-end-0 text-muted">
                                    <i class="mdi mdi-earth"></i>
                                </span>
                                <select class="form-select border-start-0 ps-1" id="qsInfo">
                                    <option value="" disabled selected>-- Pilih Kategori --</option>
                                    <option value="Lokal">Lokal (Domestik)</option>
                                    <option value="Import">Import (Luar Negeri)</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark font-13" for="qsName">
                            Nama Supplier / Perusahaan <span class="text-danger">*</span>
                        </label>
                        <div class="input-group input-group-merge">
                            <span class="input-group-text bg-light border-end-0 text-muted">
                                <i class="mdi mdi-domain"></i>
                            </span>
                            <input type="text" class="form-control border-start-0 ps-1" id="qsName" placeholder="Contoh: PT Sumber Rejeki Abadi" autocomplete="off">
                        </div>
                    </div>

                    <div class="row g-3 mb-1">
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold text-dark font-13" for="qsPhone">
                                No. Telepon Kantor
                            </label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text bg-light border-end-0 text-muted">
                                    <i class="mdi mdi-phone-outline"></i>
                                </span>
                                <input type="text" class="form-control border-start-0 ps-1" id="qsPhone" placeholder="021-xxxxxxx / 08xx" autocomplete="off">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold text-dark font-13" for="qsEmail">
                                Email Perusahaan
                            </label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text bg-light border-end-0 text-muted">
                                    <i class="mdi mdi-email-outline"></i>
                                </span>
                                <input type="email" class="form-control border-start-0 ps-1" id="qsEmail" placeholder="info@supplier.com" autocomplete="off">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top py-3 px-4 bg-light d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-label-secondary px-3" data-bs-dismiss="modal">
                        <i class="mdi mdi-close me-1"></i> Batal
                    </button>
                    <button type="button" class="btn btn-primary px-4 shadow-sm" id="saveQuickAddSupplier">
                        <i class="mdi mdi-content-save-outline me-1"></i> Simpan Supplier
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal: Edit Informasi Supplier -->
    <div class="modal fade" id="editSupplierModal" tabindex="-1" aria-labelledby="editSupplierModalTitle" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered modal-lg" style="max-width: 720px;">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 14px; overflow: hidden;">
                <div class="modal-header border-bottom py-3 px-4 bg-white d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-3">
                        <div class="avatar avatar-md flex-shrink-0">
                            <span class="avatar-initial rounded-3 bg-label-primary shadow-xs">
                                <i class="mdi mdi-domain-edit font-22"></i>
                            </span>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold text-dark mb-0" id="editSupplierModalTitle">Edit Informasi Supplier</h5>
                            <small class="text-muted font-12">Perbarui data profil, legalitas, dan kontak vendor terpilih</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 bg-white">
                    <div id="editSupplierAlert" class="alert alert-danger d-none d-flex align-items-center py-2 px-3 mb-3 font-13" role="alert">
                        <i class="mdi mdi-alert-circle-outline me-2 font-16 flex-shrink-0"></i>
                        <span id="editSupplierAlertText"></span>
                    </div>
                    <input type="hidden" id="esId">

                    <!-- Bagian 1: Informasi Utama -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-7">
                            <label class="form-label fw-semibold text-dark font-13" for="esSupplier">
                                Nama Supplier / Perusahaan <span class="text-danger">*</span>
                            </label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text bg-light border-end-0 text-muted">
                                    <i class="mdi mdi-domain"></i>
                                </span>
                                <input type="text" class="form-control border-start-0 ps-1" id="esSupplier" placeholder="Nama Supplier" required autocomplete="off">
                            </div>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label fw-semibold text-dark font-13" for="esCode">
                                Kode Supplier <span class="text-danger">*</span>
                            </label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text bg-light border-end-0 text-muted">
                                    <i class="mdi mdi-barcode"></i>
                                </span>
                                <input type="text" class="form-control border-start-0 ps-1" id="esCode" placeholder="Kode Supplier" required autocomplete="off">
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark font-13" for="esInfo">
                                Kategori Asal <span class="text-danger">*</span>
                            </label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text bg-light border-end-0 text-muted">
                                    <i class="mdi mdi-earth"></i>
                                </span>
                                <select class="form-select border-start-0 ps-1" id="esInfo">
                                    <option value="Lokal">Lokal (Domestik)</option>
                                    <option value="Import">Import (Luar Negeri)</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark font-13" for="esArea">
                                Area / Kota Operasional
                            </label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text bg-light border-end-0 text-muted">
                                    <i class="mdi mdi-map-marker-outline"></i>
                                </span>
                                <input type="text" class="form-control border-start-0 ps-1" id="esArea" placeholder="Contoh: Jakarta / Bandung / Shanghai" autocomplete="off">
                            </div>
                        </div>
                    </div>

                    <!-- Bagian 2: Kontak & Legalitas -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark font-13" for="esPhone">
                                No. Telepon Kantor
                            </label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text bg-light border-end-0 text-muted">
                                    <i class="mdi mdi-phone-outline"></i>
                                </span>
                                <input type="text" class="form-control border-start-0 ps-1" id="esPhone" placeholder="021-xxxxxxx / 08xx" autocomplete="off">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark font-13" for="esEmail">
                                Email Perusahaan
                            </label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text bg-light border-end-0 text-muted">
                                    <i class="mdi mdi-email-outline"></i>
                                </span>
                                <input type="email" class="form-control border-start-0 ps-1" id="esEmail" placeholder="info@perusahaan.com" autocomplete="off">
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold text-dark font-13" for="esNpwp">
                                Nomor Pokok Wajib Pajak (NPWP)
                            </label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text bg-light border-end-0 text-muted">
                                    <i class="mdi mdi-card-account-details-outline"></i>
                                </span>
                                <input type="text" class="form-control border-start-0 ps-1" id="esNpwp" placeholder="00.000.000.0-000.000" autocomplete="off">
                            </div>
                        </div>
                    </div>

                    <!-- Bagian 3: Alamat -->
                    <div class="mb-1">
                        <label class="form-label fw-semibold text-dark font-13" for="esAddress">
                            Alamat Lengkap
                        </label>
                        <textarea class="form-control" id="esAddress" rows="3" placeholder="Alamat lengkap kantor pusat, gudang, atau workshop supplier..." style="resize: vertical;"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top py-3 px-4 bg-light d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-label-secondary px-3" data-bs-dismiss="modal">
                        <i class="mdi mdi-close me-1"></i> Batal
                    </button>
                    <button type="button" class="btn btn-primary px-4 shadow-sm" id="esSaveBtn">
                        <i class="mdi mdi-content-save-outline me-1"></i> Simpan Perubahan
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="quickAddPoTypeModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Tipe PO</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="qtError" class="alert alert-danger d-none"></div>
                    <div class="mb-3">
                        <label class="form-label">Nama Tipe PO <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="qtName" placeholder="Contoh: Jasa">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="saveQuickAddPoType">
                        <i class="mdi mdi-content-save-outline me-1"></i> Simpan
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal: Tambah/Edit PIC Supplier -->
    <div class="modal fade" id="quickAddPicModal" tabindex="-1" aria-labelledby="quickAddPicModalTitle" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 480px;">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 14px; overflow: hidden;">
                <div class="modal-header border-bottom py-3 px-4 bg-white d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-3">
                        <div class="avatar avatar-md flex-shrink-0">
                            <span class="avatar-initial rounded-3 bg-label-primary shadow-xs" id="quickAddPicAvatar">
                                <i class="mdi mdi-account-plus-outline font-22"></i>
                            </span>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold text-dark mb-0" id="quickAddPicModalTitle">Tambah PIC Supplier</h5>
                            <small class="text-muted font-12" id="quickAddPicModalSubtitle">Kontak person (ATTN) untuk supplier terpilih</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 bg-white">
                    <input type="hidden" id="qpId">
                    <div id="qpError" class="alert alert-danger d-none d-flex align-items-center py-2 px-3 mb-3 font-13" role="alert">
                        <i class="mdi mdi-alert-circle-outline me-2 font-16 flex-shrink-0"></i>
                        <span id="qpErrorText"></span>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark font-13" for="qpName">
                            Nama PIC <span class="text-danger">*</span>
                        </label>
                        <div class="input-group input-group-merge">
                            <span class="input-group-text bg-light border-end-0 text-muted">
                                <i class="mdi mdi-account-outline"></i>
                            </span>
                            <input type="text" class="form-control border-start-0 ps-1" id="qpName" placeholder="Contoh: Bpk. Hendra / Ibu Sarah" autofocus autocomplete="off">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark font-13" for="qpPhone">
                            No. Telepon / WhatsApp
                        </label>
                        <div class="input-group input-group-merge">
                            <span class="input-group-text bg-light border-end-0 text-muted">
                                <i class="mdi mdi-phone-outline"></i>
                            </span>
                            <input type="text" class="form-control border-start-0 ps-1" id="qpPhone" placeholder="08xxxxxxxxxx" autocomplete="off">
                        </div>
                        <div class="form-text font-11 text-muted">Nomor ini otomatis mengisi kolom Mobile saat PIC dipilih.</div>
                    </div>

                    <div class="mb-1">
                        <label class="form-label fw-semibold text-dark font-13" for="qpEmail">
                            Email PIC
                        </label>
                        <div class="input-group input-group-merge">
                            <span class="input-group-text bg-light border-end-0 text-muted">
                                <i class="mdi mdi-email-outline"></i>
                            </span>
                            <input type="email" class="form-control border-start-0 ps-1" id="qpEmail" placeholder="nama@perusahaan.com" autocomplete="off">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top py-3 px-4 bg-light d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-label-secondary px-3" data-bs-dismiss="modal">
                        <i class="mdi mdi-close me-1"></i> Batal
                    </button>
                    <button type="button" class="btn btn-primary px-4 shadow-sm" id="saveQuickAddPic">
                        <i class="mdi mdi-content-save-outline me-1"></i> Simpan PIC
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal: Tambah Alamat Supplier Baru Langsung di Form PO -->
    <div class="modal fade" id="quickAddAddressModal" tabindex="-1" aria-labelledby="quickAddAddressModalTitle" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 480px;">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 14px; overflow: hidden;">
                <div class="modal-header border-bottom py-3 px-4 bg-white d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-3">
                        <div class="avatar avatar-md flex-shrink-0">
                            <span class="avatar-initial rounded-3 bg-label-primary shadow-xs">
                                <i class="mdi mdi-map-marker-plus-outline font-22"></i>
                            </span>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold text-dark mb-0" id="quickAddAddressModalTitle">Tambah Alamat Supplier</h5>
                            <small class="text-muted font-12">Alamat operasional/gudang untuk supplier terpilih</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 bg-white">
                    <div id="qaError" class="alert alert-danger d-none d-flex align-items-center py-2 px-3 mb-3 font-13" role="alert">
                        <i class="mdi mdi-alert-circle-outline me-2 font-16 flex-shrink-0"></i>
                        <span id="qaErrorText"></span>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark font-13" for="qaName">
                            Label / Nama Alamat
                        </label>
                        <div class="input-group input-group-merge">
                            <span class="input-group-text bg-light border-end-0 text-muted">
                                <i class="mdi mdi-tag-outline"></i>
                            </span>
                            <input type="text" class="form-control border-start-0 ps-1" id="qaName" placeholder="Contoh: Kantor Pusat / Gudang Bintaro" autocomplete="off">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark font-13" for="qaAddress">
                            Alamat Lengkap <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control" id="qaAddress" rows="3" placeholder="Jl. Raya No. 123..." required></textarea>
                    </div>

                    <div class="form-check form-switch mt-2">
                        <input class="form-check-input" type="checkbox" id="qaIsPrimary" value="1">
                        <label class="form-check-label fw-semibold text-dark font-13" for="qaIsPrimary">
                            Jadikan sebagai Alamat Utama
                        </label>
                    </div>
                </div>
                <div class="modal-footer border-top py-3 px-4 bg-light d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-label-secondary px-3" data-bs-dismiss="modal">
                        <i class="mdi mdi-close me-1"></i> Batal
                    </button>
                    <button type="button" class="btn btn-primary px-4 shadow-sm" id="saveQuickAddAddress">
                        <i class="mdi mdi-content-save-outline me-1"></i> Simpan Alamat
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal: Tarik Item dari PR Disetujui (Multi-PR Consolidation) --}}
    <div class="modal fade" id="modalPullPrItems" tabindex="-1" aria-labelledby="modalPullPrItemsLabel" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 14px; overflow: hidden;">
                <div class="modal-header border-bottom py-3 px-4 bg-white d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-3">
                        <div class="avatar avatar-md flex-shrink-0">
                            <span class="avatar-initial rounded-3 bg-label-primary shadow-xs">
                                <i class="mdi mdi-file-import-outline font-22"></i>
                            </span>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold text-dark mb-0" id="modalPullPrItemsLabel">Tarik Item dari Purchase Request (PR)</h5>
                            <small class="text-muted font-12">
                                Pilih item dari PR yang disetujui (lintas PR/SO) untuk digabung ke dalam dokumen Purchase Order ini
                            </small>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-2 mb-3 align-items-center justify-content-between">
                        <div class="col-md-7 col-12">
                            <div class="input-group input-group-merge">
                                <span class="input-group-text bg-white border-end-0"><i class="mdi mdi-magnify text-muted"></i></span>
                                <input type="text" id="inputSearchPrItems" class="form-control border-start-0 ps-0" placeholder="Ketik nomor PR, nomor SO, part number, atau nama barang...">
                            </div>
                        </div>
                        <div class="col-md-5 col-12 text-md-end d-flex align-items-center justify-content-md-end gap-2">
                            <span class="badge bg-label-primary py-2 px-3">
                                <span id="countSelectedPrItems">0</span> item dipilih
                            </span>
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="btnReloadPrItems" title="Muat ulang data item PR">
                                <i class="mdi mdi-refresh me-1"></i> Refresh
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive border rounded" style="max-height: 440px; overflow-y: auto;">
                        <table class="table table-hover align-middle mb-0" id="tablePullPrItems">
                            <thead class="table-light sticky-top" style="z-index: 2;">
                                <tr>
                                    <th class="text-center" style="width: 40px;">
                                        <input type="checkbox" class="form-check-input" id="checkAllModalPrItems">
                                    </th>
                                    <th style="width: 170px;">Dokumen PR</th>
                                    <th style="width: 150px;">SO / Proyek</th>
                                    <th>Item / Sparepart</th>
                                    <th class="text-center" style="width: 90px;">Sisa PR</th>
                                    <th class="text-center" style="width: 110px;">Qty Ambil</th>
                                    <th style="width: 130px;">Catatan</th>
                                </tr>
                            </thead>
                            <tbody id="tbodyPullPrItems">
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">
                                        <span class="spinner-border spinner-border-sm me-2 text-primary" role="status"></span>
                                        Memuat daftar item Purchase Request yang tersedia...
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-2 text-muted small d-flex align-items-center gap-1">
                        <i class="mdi mdi-information-outline text-primary"></i>
                        <span>Anda dapat mencentang beberapa item dari PR berbeda. Qty Ambil dapat diubah sebelum dimasukkan ke PO.</span>
                    </div>
                </div>
                <div class="modal-footer border-top bg-light bg-opacity-25 px-4 py-3 d-flex justify-content-between">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-primary d-flex align-items-center gap-1 shadow-xs" id="btnApplyPullPrItems" disabled>
                        <i class="mdi mdi-check-bold me-1"></i>
                        <span>Tambahkan ke PO</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('after-style')
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/select2/select2.css" />
@endpush
@push('after-script')
    <script src="{{ asset('assets') }}/vendor/libs/formvalidation/dist/js/FormValidation.min.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/select2/select2.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/sortablejs/sortable.js"></script>
    <script src="{{ asset('assets') }}/includes/repeater/jquery-repeater-invoice.js"></script>
    {{-- <script src="{{ asset('assets') }}/js/app-invoice-add.js"></script> --}}
@endpush
@push('page-script')
    <script src="{{ asset('assets') }}/includes/repeater/repeater-invoice.js"></script>
    <script src="{{ asset('assets') }}/js/forms-selects.js"></script>
@endpush
@push('script')
    <script>
        $(() => {
            // Format Integer menjadi Currency ID Rupiah
            let formatter = new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            });

            // Tampilkan Badge - Code - Nama Supplier
            function renderSupplierOption(option) {
                if (!option.id) {
                    return option.text;
                }
                var info = $(option.element).data('info');
                var code = $(option.element).data('code');
                var $wrapper = $('<span></span>');
                if (info) {
                    var badgeClass = info === 'Lokal' ? 'bg-label-info' : 'bg-label-primary';
                    $wrapper.append($('<span></span>').addClass('badge ' + badgeClass).text(info));
                    $wrapper.append(' ');
                }
                if (code) {
                    $wrapper.append($('<span></span>').addClass('text-muted').text(code));
                    $wrapper.append(' ');
                }
                $wrapper.append(document.createTextNode(option.text));
                return $wrapper;
            }
            // Payment dropdown dengan opsi custom "Isi Sendiri"
            var $paymentSelect = $('#payment-select');
            if ($paymentSelect.length) {
                var presetPaymentValues = $paymentSelect.find('option').map(function() {
                    return $(this).val();
                }).get();
                var currentPayment = $('#input-payment-hidden').val();

                if (currentPayment && presetPaymentValues.indexOf(currentPayment) === -1) {
                    $paymentSelect.val('manual');
                    $('#manual-payment-wrapper').show();
                    $('#input-payment-manual').val(currentPayment);
                } else if (currentPayment) {
                    $paymentSelect.val(currentPayment);
                }
                $('#input-payment-hidden').val($paymentSelect.val() === 'manual' ?
                    $('#input-payment-manual').val() : $paymentSelect.val());

                $paymentSelect.on('change', function() {
                    var val = $(this).val();
                    if (val === 'manual') {
                        $('#manual-payment-wrapper').show();
                        $('#input-payment-hidden').val($('#input-payment-manual').val());
                    } else {
                        $('#manual-payment-wrapper').hide();
                        $('#input-payment-hidden').val(val);
                    }
                });

                $('#input-payment-manual').on('input', function() {
                    $('#input-payment-hidden').val($(this).val());
                });
            }

            // Tipe pembayaran: field termin & estimasi jatuh tempo hanya tampil untuk "tempo"
            var $pmtType = $('#payment-type-select');
            if ($pmtType.length) {
                var addDaysISO = function(dateStr, days) {
                    var p = dateStr.split('-');
                    var d = new Date(Date.UTC(+p[0], +p[1] - 1, +p[2]));
                    d.setUTCDate(d.getUTCDate() + days);
                    return d.toISOString().slice(0, 10);
                };
                var recalcDueEstimate = function() {
                    var baseDate = $('#date').val();
                    var days = parseInt($('#top-days-input').val(), 10);
                    if ($pmtType.val() === 'tempo' && baseDate && !isNaN(days)) {
                        $('#due-date-estimate-input').val(addDaysISO(baseDate, days));
                    }
                };
                var toggleTempoFields = function() {
                    var isTempo = $pmtType.val() === 'tempo';
                    $('.tempo-field').toggle(isTempo);
                    if (isTempo && !$('#top-days-input').val()) $('#top-days-input').val(30);
                    if (isTempo) recalcDueEstimate();
                };
                $pmtType.on('change', toggleTempoFields);
                $('#top-days-input, #date').on('change input', recalcDueEstimate);
                toggleTempoFields();
            }

            // Quick Add PO Type (AJAX, tanpa reload) — tipe baru langsung tersimpan di master
            // data jadi ke depan tinggal muncul di dropdown, tidak perlu diketik ulang.
            $('#saveQuickAddPoType').on('click', function() {
                var name = $('#qtName').val().trim();
                if (!name) {
                    $('#qtError').removeClass('d-none').text('Nama tipe PO wajib diisi.');
                    return;
                }

                $.ajax({
                    url: '{{ route('purchase-order-type.quick-store') }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        name: name
                    },
                    success: function(res) {
                        if (res.success) {
                            var newType = res.data;
                            var option = new Option(newType.name, newType.name, true, true);
                            $('#po-type-select').append(option).trigger('change');

                            $('#qtName').val('');
                            $('#qtError').addClass('d-none').text('');
                            $('#quickAddPoTypeModal').modal('hide');
                        }
                    },
                    error: function(xhr) {
                        var msg = xhr.responseJSON && xhr.responseJSON.message ?
                            xhr.responseJSON.message : 'Gagal menyimpan, coba lagi.';
                        $('#qtError').removeClass('d-none').text(msg);
                    }
                });
            });

            // ── Handler Tab Button Ship To (BDG / BKS / Custom) ──
            $(document).on('click', '.ship-to-tab-btn', function(e) {
                e.preventDefault();
                var $btn = $(this);
                var preset = $btn.attr('data-preset');
                var address = $btn.attr('data-address');

                $('.ship-to-tab-btn').removeClass('active');
                $btn.addClass('active');

                if (preset === 'BDG' || preset === 'BKS') {
                    if (address) {
                        $('#ship_to_input').val(address).trigger('change');
                    }
                } else if (preset === 'CUSTOM') {
                    var cur = ($('#ship_to_input').val() || '').trim();
                    var bdg = ($('.ship-to-tab-btn[data-preset="BDG"]').attr('data-address') || '').trim();
                    var bks = ($('.ship-to-tab-btn[data-preset="BKS"]').attr('data-address') || '').trim();
                    if (cur === bdg || cur === bks) {
                        $('#ship_to_input').val('').trigger('change');
                    }
                    $('#ship_to_input').focus();
                }
            });

            $(document).on('input', '#ship_to_input', function() {
                var val = ($(this).val() || '').trim();
                var bdg = ($('.ship-to-tab-btn[data-preset="BDG"]').attr('data-address') || '').trim();
                var bks = ($('.ship-to-tab-btn[data-preset="BKS"]').attr('data-address') || '').trim();

                $('.ship-to-tab-btn').removeClass('active');
                if (val && val === bdg) {
                    $('.ship-to-tab-btn[data-preset="BDG"]').addClass('active');
                } else if (val && val === bks) {
                    $('.ship-to-tab-btn[data-preset="BKS"]').addClass('active');
                } else {
                    $('.ship-to-tab-btn[data-preset="CUSTOM"]').addClass('active');
                }
            });

            var $supplierDropdown = $('#supplier-dropdown');

            function initSupplierSelect2() {
                if ($supplierDropdown.data('select2')) {
                    $supplierDropdown.select2('destroy');
                }
                $supplierDropdown.select2({
                    placeholder: 'Pilih Supplier...',
                    allowClear: true,
                    width: '100%',
                    dropdownParent: $supplierDropdown.parent(),
                    templateResult: renderSupplierOption,
                    templateSelection: renderSupplierOption,
                    escapeMarkup: function(m) {
                        return m;
                    }
                });
            }
            initSupplierSelect2();

            // Tampilkan/sembunyikan tombol "Edit Supplier" sesuai ada/tidaknya supplier terpilih
            function toggleEditSupplierBtn() {
                $('#btn-edit-supplier').toggleClass('d-none', !$supplierDropdown.val());
            }
            toggleEditSupplierBtn();

            // Address selector helper & event handling
            var $addressSelect = $('#address-select');

            function populateAddressDropdown(addresses, currentAddress, autoSelectFirst) {
                $addressSelect.empty();
                $addressSelect.append(new Option('-- Pilih Alamat Operasional / Cabang / Gudang --', '', false, false));

                if (!addresses || !addresses.length) {
                    return;
                }

                var matched = false;
                var primaryAddr = null;

                addresses.forEach(function(addr) {
                    var isPrimary = Boolean(addr.is_primary);
                    if (isPrimary && !primaryAddr) primaryAddr = addr;

                    var cleanAddrText = (addr.address || '').trim();
                    var label = (addr.name ? addr.name : 'Alamat') + 
                                (isPrimary ? ' [Utama]' : '') + ' : ' + 
                                (cleanAddrText.length > 70 ? cleanAddrText.substring(0, 70) + '...' : cleanAddrText);

                    var isSelected = false;
                    if (currentAddress && (cleanAddrText === currentAddress.trim() || currentAddress.trim().indexOf(cleanAddrText) !== -1)) {
                        isSelected = true;
                        matched = true;
                    }

                    var opt = new Option(label, cleanAddrText, isSelected, isSelected);
                    $(opt).attr('data-id', addr.id);
                    $(opt).attr('data-name', addr.name || '');
                    $(opt).attr('data-address', cleanAddrText);
                    $(opt).attr('data-primary', isPrimary ? '1' : '0');
                    $addressSelect.append(opt);
                });

                if (!matched && autoSelectFirst) {
                    var pick = primaryAddr || addresses[0];
                    if (pick && pick.address) {
                        $addressSelect.val(pick.address.trim());
                        $('#address').val(pick.address.trim());
                    }
                }
            }

            $addressSelect.on('change', function() {
                var selectedAddr = $(this).val();
                if (selectedAddr) {
                    $('#address').val(selectedAddr);
                }
            });

            // Quick Add Address modal trigger & AJAX handler
            $('#btn-add-supplier-address').on('click', function() {
                var supplierId = $supplierDropdown.val();
                if (!supplierId) {
                    alert('Pilih supplier terlebih dahulu.');
                    return;
                }
                $('#qaError').addClass('d-none');
                $('#qaErrorText').text('');
                $('#qaName').val('');
                $('#qaAddress').val('');
                $('#qaIsPrimary').prop('checked', false);
                $('#quickAddAddressModal').modal('show');
            });

            $('#saveQuickAddAddress').on('click', function() {
                var supplierId = $supplierDropdown.val();
                var name = $('#qaName').val().trim();
                var address = $('#qaAddress').val().trim();
                var isPrimary = $('#qaIsPrimary').is(':checked');

                if (!address) {
                    $('#qaError').removeClass('d-none');
                    $('#qaErrorText').text('Alamat lengkap wajib diisi.');
                    return;
                }

                var $btn = $(this);
                var origText = $btn.html();
                $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...');

                $.ajax({
                    url: '/supplier/' + supplierId + '/address',
                    type: 'POST',
                    headers: { 'Accept': 'application/json' },
                    data: {
                        _token: '{{ csrf_token() }}',
                        name: name,
                        address: address,
                        is_primary: isPrimary ? 1 : 0
                    },
                    success: function(res) {
                        $btn.prop('disabled', false).html(origText);
                        if (res.success) {
                            var newAddr = res.data;
                            var cleanAddrText = (newAddr.address || '').trim();
                            var label = (newAddr.name ? newAddr.name : 'Alamat') + 
                                        (newAddr.is_primary ? ' [Utama]' : '') + ' : ' + 
                                        (cleanAddrText.length > 70 ? cleanAddrText.substring(0, 70) + '...' : cleanAddrText);
                            var opt = new Option(label, cleanAddrText, true, true);
                            $(opt).attr('data-id', newAddr.id);
                            $(opt).attr('data-name', newAddr.name || '');
                            $(opt).attr('data-address', cleanAddrText);
                            $(opt).attr('data-primary', newAddr.is_primary ? '1' : '0');

                            $addressSelect.append(opt);
                            $addressSelect.val(cleanAddrText).trigger('change');
                            $('#address').val(cleanAddrText);

                            $('#quickAddAddressModal').modal('hide');
                        } else {
                            $('#qaError').removeClass('d-none');
                            $('#qaErrorText').text('Gagal menyimpan alamat.');
                        }
                    },
                    error: function(xhr) {
                        $btn.prop('disabled', false).html(origText);
                        var msg = xhr.responseJSON && xhr.responseJSON.message ?
                            xhr.responseJSON.message : 'Gagal menyimpan, coba lagi.';
                        $('#qaError').removeClass('d-none');
                        $('#qaErrorText').text(msg);
                    }
                });
            });

            // Auto-fill Mobile & Address dari data Supplier terpilih
            $supplierDropdown.on('change', function() {
                var $selected = $(this).find(':selected');
                var phone = realValue($selected.data('phone'));
                var address = realValue($selected.data('address'));
                $('#mobile').val(phone);
                $('#address').val(address);
                toggleEditSupplierBtn();
                loadAttnOptions($(this).val(), '', '', true);
            });

            // ATTN (PIC) select2 + tombol Tambah PIC
            var $attnDropdown = $('#attn');
            $attnDropdown.select2({
                placeholder: 'Pilih / ketik PIC...',
                allowClear: true,
                width: '100%',
                tags: true,
                dropdownParent: $attnDropdown.closest('.d-flex')
            });

            // Beberapa data lama menyimpan "-" sebagai placeholder utk field kosong (bukan data asli)
            function realValue(v) {
                v = (v || '').toString().trim();
                return (v && v !== '-') ? v : '';
            }

            function makePicOption(pic, selected) {
                var label = pic.name_pic + (pic.position ? ' (' + pic.position + ')' : '');
                var option = new Option(label, pic.name_pic, !!selected, !!selected);
                $(option).attr('data-id', pic.id || '');
                $(option).attr('data-position', realValue(pic.position));
                $(option).attr('data-phone', realValue(pic.phone_pic));
                $(option).attr('data-email', realValue(pic.email_pic));
                return option;
            }

            function loadAttnOptions(supplierId, keepAttnValue, keepAddressValue, autoSelectFirst) {
                var currentAttn = keepAttnValue !== undefined ? keepAttnValue : $attnDropdown.val();
                var currentAddr = keepAddressValue !== undefined ? keepAddressValue : $('#address').val();

                $attnDropdown.empty();
                $attnDropdown.append(new Option('', '', false, false));
                $addressSelect.empty().append(new Option('-- Pilih Alamat Operasional / Cabang / Gudang --', '', false, false));

                if (!supplierId) {
                    $('#attn-empty-hint').hide();
                    $attnDropdown.trigger('change');
                    return;
                }
                $.ajax({
                    url: '/supplier/' + supplierId + '/edit-data',
                    type: 'GET',
                    success: function(res) {
                        if (String($supplierDropdown.val() || '') !== String(supplierId)) {
                            return;
                        }
                        // 1. PICs
                        var pics = res.pics || [];
                        var autoPickName = (!currentAttn && autoSelectFirst && pics.length) ? pics[0].name_pic : null;
                        pics.forEach(function(pic) {
                            $attnDropdown.append(makePicOption(pic, pic.name_pic === currentAttn || pic.name_pic === autoPickName));
                        });
                        if (currentAttn && !pics.some(function(p) { return p.name_pic === currentAttn; })) {
                            var keepOption = new Option(currentAttn, currentAttn, true, true);
                            $attnDropdown.append(keepOption);
                        }
                        $('#attn-empty-hint').toggle(!pics.length);
                        $attnDropdown.trigger('change');

                        // 2. Addresses
                        var addresses = res.addresses || [];
                        populateAddressDropdown(addresses, currentAddr, autoSelectFirst);
                    }
                });
            }

            // Auto-fill Mobile & toggle tombol Edit PIC saat memilih PIC dari dropdown ATTN.
            $attnDropdown.on('change', function() {
                var $selected = $(this).find(':selected');
                if ($selected.data('id')) {
                    var picPhone = realValue($selected.data('phone'));
                    if (picPhone) {
                        $('#mobile').val(picPhone);
                    }
                }
                $('#btn-edit-attn-pic').toggleClass('d-none', !$selected.data('id'));
            });

            // Muat ulang ATTN & Alamat saat halaman dibuka dengan supplier sudah terpilih (mode Edit PO)
            if ($supplierDropdown.val()) {
                loadAttnOptions(
                    $supplierDropdown.val(), 
                    '{{ old('attn', @$purchase->attn ?? '') }}',
                    '{{ old('address', @$purchase->address ?? '') }}',
                    {{ @$purchase ? 'false' : 'true' }}
                );
            }

            function resetPicModal() {
                $('#qpError').addClass('d-none');
                $('#qpErrorText').text('');
                $('#qpId').val('');
                $('#qpName').val('');
                $('#qpPhone').val('');
                $('#qpEmail').val('');
            }

            // Tambah PIC baru langsung dari form PO (AJAX, tanpa reload)
            $('#btn-add-attn-pic').on('click', function() {
                if (!$supplierDropdown.val()) {
                    alert('Pilih supplier terlebih dahulu.');
                    return;
                }
                resetPicModal();
                $('#quickAddPicModalTitle').text('Tambah PIC Supplier');
                $('#quickAddPicModalSubtitle').text('Kontak person (ATTN) untuk supplier terpilih');
                $('#quickAddPicAvatar').html('<i class="mdi mdi-account-plus-outline font-22"></i>');
                $('#saveQuickAddPic').html('<i class="mdi mdi-content-save-outline me-1"></i> Simpan PIC');
                $('#quickAddPicModal').modal('show');
            });

            // Edit PIC yang sedang terpilih di ATTN
            $('#btn-edit-attn-pic').on('click', function() {
                var $selected = $attnDropdown.find(':selected');
                var picId = $selected.data('id');
                if (!picId) return;

                resetPicModal();
                $('#qpId').val(picId);
                $('#qpName').val($selected.val());
                $('#qpPhone').val($selected.data('phone') || '');
                $('#qpEmail').val($selected.data('email') || '');
                $('#quickAddPicModalTitle').text('Edit PIC Supplier');
                $('#quickAddPicModalSubtitle').text('Perbarui kontak person (ATTN) untuk supplier terpilih');
                $('#quickAddPicAvatar').html('<i class="mdi mdi-account-edit-outline font-22"></i>');
                $('#saveQuickAddPic').html('<i class="mdi mdi-content-save-outline me-1"></i> Perbarui PIC');
                $('#quickAddPicModal').modal('show');
            });

            $('#saveQuickAddPic').on('click', function() {
                var supplierId = $supplierDropdown.val();
                var picId = $('#qpId').val();
                var name = $('#qpName').val().trim();
                if (!name) {
                    $('#qpError').removeClass('d-none');
                    $('#qpErrorText').text('Nama PIC wajib diisi.');
                    return;
                }

                var $btn = $(this);
                var origText = $btn.html();
                $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...');

                var payload = {
                    _token: '{{ csrf_token() }}',
                    namePic: name,
                    position: '',
                    phonePic: $('#qpPhone').val(),
                    emailPic: $('#qpEmail').val(),
                };
                var url = picId ? '/supplier/pic/' + picId : '/supplier/' + supplierId + '/pic';
                if (picId) {
                    payload._method = 'PATCH';
                }

                $.ajax({
                    url: url,
                    type: 'POST',
                    headers: { 'Accept': 'application/json' },
                    data: payload,
                    success: function(res) {
                        $btn.prop('disabled', false).html(origText);
                        if (res.success) {
                            var pic = res.data;
                            if (picId) {
                                $attnDropdown.find('option[data-id="' + picId + '"]').remove();
                            }
                            $attnDropdown.append(makePicOption(pic, true)).trigger('change');
                            if (pic.phone_pic) {
                                $('#mobile').val(pic.phone_pic);
                            }
                            $('#attn-empty-hint').hide();
                            $('#quickAddPicModal').modal('hide');
                        } else {
                            $('#qpError').removeClass('d-none');
                            $('#qpErrorText').text('Gagal menyimpan PIC.');
                        }
                    },
                    error: function(xhr) {
                        $btn.prop('disabled', false).html(origText);
                        var msg = xhr.responseJSON && xhr.responseJSON.message ?
                            xhr.responseJSON.message : 'Gagal menyimpan, coba lagi.';
                        $('#qpError').removeClass('d-none');
                        $('#qpErrorText').text(msg);
                    }
                });
            });

            $('#quickAddSupplierModal').on('show.bs.modal', function() {
                $('#qsError').addClass('d-none');
                $('#qsErrorText').text('');
            });

            // Quick Add Supplier (AJAX, tanpa reload)
            $('#saveQuickAddSupplier').on('click', function() {
                var code = $('#qsCode').val().trim();
                var name = $('#qsName').val().trim();
                var info = $('#qsInfo').val();
                var phone = $('#qsPhone').length ? $('#qsPhone').val().trim() : '';
                var email = $('#qsEmail').length ? $('#qsEmail').val().trim() : '';

                if (!code || !name || !info) {
                    $('#qsError').removeClass('d-none');
                    $('#qsErrorText').text('Kode Supplier, Nama Supplier, dan Kategori Asal wajib diisi.');
                    return;
                }

                var $btn = $(this);
                var origText = $btn.html();
                $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...');

                $.ajax({
                    url: '{{ route('supplier.quick-store') }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        code: code,
                        supplier: name,
                        info: info,
                        phone: phone,
                        email: email
                    },
                    success: function(res) {
                        $btn.prop('disabled', false).html(origText);
                        if (res.success) {
                            var newSupplier = res.data;
                            var option = new Option(newSupplier.supplier, newSupplier.id, true, true);
                            $(option).attr('data-info', newSupplier.info);
                            $(option).attr('data-code', newSupplier.code);
                            $supplierDropdown.append(option).trigger('change');

                            $('#qsCode').val('');
                            $('#qsName').val('');
                            $('#qsInfo').val('');
                            if ($('#qsPhone').length) $('#qsPhone').val('');
                            if ($('#qsEmail').length) $('#qsEmail').val('');
                            $('#qsError').addClass('d-none');
                            $('#qsErrorText').text('');
                            $('#quickAddSupplierModal').modal('hide');
                        }
                    },
                    error: function(xhr) {
                        $btn.prop('disabled', false).html(origText);
                        var msg = xhr.responseJSON && xhr.responseJSON.message ?
                            xhr.responseJSON.message : 'Gagal menyimpan, coba lagi.';
                        $('#qsError').removeClass('d-none');
                        $('#qsErrorText').text(msg);
                    }
                });
            });

            // Edit Supplier (AJAX, tanpa reload halaman)
            $('#editSupplierModal').on('show.bs.modal', function() {
                var id = $supplierDropdown.val();
                $('#editSupplierAlert').addClass('d-none');
                $('#editSupplierAlertText').text('');
                if (!id) {
                    $('#editSupplierAlert').removeClass('d-none');
                    $('#editSupplierAlertText').text('Pilih supplier terlebih dahulu.');
                    return;
                }
                $('#esId').val(id);

                $.ajax({
                    url: '/supplier/' + id + '/edit-data',
                    type: 'GET',
                    success: function(res) {
                        var s = res.supplier;
                        $('#esSupplier').val(s.supplier || '');
                        $('#esInfo').val(s.info || 'Lokal');
                        $('#esCode').val(s.code || '');
                        $('#esEmail').val(s.email || '');
                        $('#esPhone').val(s.phone || '');
                        $('#esNpwp').val(s.npwp || '');
                        $('#esArea').val(s.area || '');
                        $('#esAddress').val(s.address || '');
                    },
                    error: function() {
                        $('#editSupplierAlert').removeClass('d-none');
                        $('#editSupplierAlertText').text('Gagal memuat data supplier.');
                    }
                });
            });

            // Simpan perubahan info Supplier
            $('#esSaveBtn').on('click', function() {
                var id = $('#esId').val();
                if (!id) return;
                var $btn = $(this);
                var origHtml = $btn.html();
                $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...');

                $.ajax({
                    url: '/supplier/' + id,
                    type: 'POST',
                    headers: { 'Accept': 'application/json' },
                    data: {
                        _method: 'PATCH',
                        _token: '{{ csrf_token() }}',
                        supplier: $('#esSupplier').val(),
                        info: $('#esInfo').val(),
                        code: $('#esCode').val(),
                        email: $('#esEmail').val(),
                        phone: $('#esPhone').val(),
                        npwp: $('#esNpwp').val(),
                        area: $('#esArea').val(),
                        address: $('#esAddress').val(),
                    },
                    success: function(res) {
                        if (res.success) {
                            var $opt = $supplierDropdown.find('option[value="' + id + '"]');
                            $opt.text(res.data.supplier);
                            $opt.attr('data-info', res.data.info);
                            $opt.attr('data-code', res.data.code);
                            $opt.attr('data-phone', res.data.phone);
                            $opt.attr('data-address', res.data.address);
                            initSupplierSelect2();
                            $('#mobile').val(realValue(res.data.phone));
                            $('#address').val(realValue(res.data.address));
                            $('#editSupplierModal').modal('hide');
                        } else {
                            $('#editSupplierAlert').removeClass('d-none');
                            $('#editSupplierAlertText').text('Gagal menyimpan perubahan.');
                        }
                    },
                    error: function(xhr) {
                        var msg = xhr.responseJSON && xhr.responseJSON.message ?
                            xhr.responseJSON.message : 'Gagal menyimpan perubahan.';
                        $('#editSupplierAlert').removeClass('d-none');
                        $('#editSupplierAlertText').text(msg);
                    },
                    complete: function() {
                        $btn.prop('disabled', false).html(origHtml);
                    }
                });
            });

            function formatNumber(n) {
                return String(n || '').replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            }

            function formatDecimalDisplay(num) {
                if (num === null || num === undefined || isNaN(num)) return '0';
                var n = Number(num);
                var isFractional = Math.abs(n - Math.round(n)) > 0.0001;
                var fixed = isFractional ? n.toFixed(2) : Math.round(n).toString();
                var parts = fixed.split('.');
                var intPart = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                if (parts.length > 1 && parts[1] !== '00') {
                    return intPart + ',' + parts[1];
                }
                return intPart;
            }

            function formatCurrencyInput(val) {
                if (val === null || val === undefined || val === '') return '';
                var str = String(val);
                var hasComma = str.indexOf(',') !== -1;
                if (hasComma) {
                    var parts = str.split(',');
                    var intPart = parts[0].replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                    var decPart = parts.slice(1).join('').replace(/\D/g, "").slice(0, 2);
                    return intPart + ',' + decPart;
                }
                return str.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            }

            function parseCurrency(val) {
                if (!val) return 0;
                var str = String(val).trim();
                if (str.indexOf(',') !== -1) {
                    var clean = str.replace(/\./g, '').replace(',', '.');
                    return parseFloat(clean) || 0;
                }
                var clean = str.replace(/\./g, '');
                return parseFloat(clean) || 0;
            }

            function formatCurrencyDiscount(input) {
                var input_val = input.val();

                // don't validate empty input
                if (input_val === "") {
                    return;
                }

                input_val = formatCurrencyInput(input_val);
                input.val(input_val);
                var nomorFloat = parseCurrency(input_val);
                $('#diskon').val(nomorFloat);
            }

            // Formatting Discount Quotation (delegated: works for existing + future rows)
            $(document).on('keyup', '#diskon-label', function() {
                formatCurrencyDiscount($(this));
            });

            function formatCurrencyDeliveryCost(input) {
                var input_val = input.val();

                if (input_val === "") {
                    return;
                }

                input_val = formatNumber(input_val);
                input.val(input_val);
                var nomorInt = parseFloat(input_val.replace(/[.,]/g, '')) || 0;
                $('#delivery-cost').val(nomorInt);
            }

            $(document).on('keyup input', '#delivery-cost-label', function() {
                formatCurrencyDeliveryCost($(this));
                recalculateTotals();
            });

            $(document).on('keyup input', '#diskon-label', function() {
                formatCurrencyDiscount($(this));
                recalculateTotals();
            });

            // Toggle Tax (PPN 11%) on/off
            $(document).on('change', '#taxSwitch', function() {
                $('#tax').val($(this).is(':checked') ? 11 : 0);
                recalculateTotals();
            });

            // Live calculation helper: Hitung Harga DPP (Exc. PPN) dan PPN jika harga yang diinput adalah include PPN (11%)
            function updatePriceTaxHint($input) {
                var $col = $input.closest('.col-lg-3');
                var $hint = $col.find('.price-tax-hint');
                if (!$hint.length) return;

                // Jika baris ini sudah dalam state "DPP diterapkan", jangan hitung ulang dari nilai DPP
                if ($col.data('dpp-applied')) {
                    return;
                }

                var inputVal = $input.val();
                var nomorFloat = parseCurrency(inputVal);

                if (nomorFloat > 0) {
                    var dppPrecise = nomorFloat / 1.11;
                    var ppnPrecise = nomorFloat - dppPrecise;

                    var dppFormatted = formatDecimalDisplay(dppPrecise);
                    var ppnFormatted = formatDecimalDisplay(ppnPrecise);

                    $hint.find('.exc-ppn-val').text('Rp ' + dppFormatted);
                    $hint.find('.ppn-val').text('(PPN: Rp ' + ppnFormatted + ')');
                    $hint.find('.btn-apply-dpp')
                        .data('exc-ppn', dppPrecise)
                        .data('dpp-formatted', dppFormatted)
                        .data('orig-price', nomorFloat);
                    $hint.find('.hint-calc-wrapper').removeClass('d-none');
                    $hint.find('.hint-applied-wrapper').addClass('d-none');
                    $hint.removeClass('d-none');
                } else {
                    $hint.addClass('d-none');
                }
            }

            $(document).on('keyup input', '.invoice-item-price-label', function(e) {
                var input = $(this);
                var $col = input.closest('.col-lg-3');
                var $row = input.closest('.repeater-wrapper');

                if (e.type === 'keyup' && [37, 38, 39, 40, 9, 16, 17, 18, 27].includes(e.which)) {
                    return;
                }

                // User mengetik manual -> reset state DPP applied pada baris ini
                $col.data('dpp-applied', false);

                var input_val = input.val();
                var original_len = input_val.length;
                var caret_pos = input.prop("selectionStart") || 0;

                input_val = formatCurrencyInput(input_val);
                input.val(input_val);

                var nomorFloat = parseCurrency(input_val);
                $row.find('.invoice-item-price').val(nomorFloat);

                // Otomatis isi Qty jadi 1 jika Price diisi dan Qty masih kosong / 0
                if (nomorFloat > 0) {
                    var $qtyInput = $row.find('.invoice-item-qty');
                    var currentQty = parseFloat($qtyInput.val()) || 0;
                    if (currentQty <= 0 || !$qtyInput.val().trim()) {
                        $qtyInput.val(1);
                    }
                }

                var updated_len = input_val.length;
                caret_pos = updated_len - original_len + caret_pos;
                if (input[0] && input[0].setSelectionRange) {
                    input[0].setSelectionRange(caret_pos, caret_pos);
                }

                updatePriceTaxHint(input);
                recalculateTotals();
            });

            // Handler tombol "Gunakan Harga DPP (Exc. PPN)"
            $(document).on('click', '.btn-apply-dpp', function(e) {
                e.preventDefault();
                e.stopPropagation();

                var $col = $(this).closest('.col-lg-3');
                var $row = $(this).closest('.repeater-wrapper');
                var excPpn = parseFloat($(this).data('exc-ppn')) || 0;
                var dppFormatted = $(this).data('dpp-formatted') || formatDecimalDisplay(excPpn);
                var origPrice = $(this).data('orig-price');
                var $priceLabel = $col.find('.invoice-item-price-label');
                var $priceHidden = $row.find('.invoice-item-price');
                var $hint = $col.find('.price-tax-hint');

                if (excPpn && excPpn > 0) {
                    $col.data('dpp-applied', true);
                    $col.data('orig-price', origPrice);
                    $col.data('exc-ppn-val', excPpn);

                    $priceLabel.val(dppFormatted);
                    $priceHidden.val(parseFloat(excPpn.toFixed(4)));

                    // Otomatis isi Qty jadi 1 jika Qty masih kosong / 0
                    var $qtyInput = $row.find('.invoice-item-qty');
                    var currentQty = parseFloat($qtyInput.val()) || 0;
                    if (currentQty <= 0 || !$qtyInput.val().trim()) {
                        $qtyInput.val(1);
                    }

                    // Switch ke status diterapkan
                    $hint.find('.applied-dpp-text').text('Rp ' + dppFormatted);
                    $hint.find('.hint-calc-wrapper').addClass('d-none');
                    $hint.find('.hint-applied-wrapper').removeClass('d-none');
                    $hint.removeClass('d-none');

                    // Auto-aktifkan toggle Tax PPN 11% di summary jika belum aktif
                    if (!$('#taxSwitch').is(':checked')) {
                        $('#taxSwitch').prop('checked', true);
                        $('#tax').val(11);
                    }

                    recalculateTotals();
                }
            });

            // Handler tombol "Batal / Reset DPP"
            $(document).on('click', '.btn-reset-dpp', function(e) {
                e.preventDefault();
                e.stopPropagation();

                var $col = $(this).closest('.col-lg-3');
                var $row = $(this).closest('.repeater-wrapper');
                var origPrice = $col.data('orig-price');
                var $priceLabel = $col.find('.invoice-item-price-label');
                var $priceHidden = $row.find('.invoice-item-price');

                if (origPrice && origPrice > 0) {
                    $col.data('dpp-applied', false);
                    $priceLabel.val(formatDecimalDisplay(origPrice));
                    $priceHidden.val(origPrice);

                    updatePriceTaxHint($priceLabel);
                    recalculateTotals();
                }
            });

            // Info "qty lebih dari kebutuhan PR = tambahan stok" — live update pas qty diubah
            $(document).on('keyup change input', '.invoice-item-qty', function () {
                var id = $(this).data('id');
                var prRemaining = parseInt($(this).data('pr-remaining'), 10);
                var qty = parseInt($(this).val(), 10) || 0;
                var $hint = $('#qty-stock-hint-' + id);
                if ($hint.length && !isNaN(prRemaining)) {
                    if (qty > prRemaining) {
                        $hint.removeClass('d-none').html(
                            '<i class="mdi mdi-information-outline"></i> ' + prRemaining + ' pcs utk PR, +' + (qty - prRemaining) + ' stok'
                        );
                    } else {
                        $hint.addClass('d-none');
                    }
                }
                recalculateTotals();
            });

            $(document).on('keyup change input', '.invoice-item-disc', function() {
                recalculateTotals();
            });

            // Helper untuk prefix huruf Head Title (A., B., C., dst)
            function getNextHeaderPrefix() {
                var headerCount = $('[data-repeater-list="group-a"] .repeater-wrapper').filter(function() {
                    return $(this).hasClass('header-row-wrapper') || $(this).find('.item-category-value').val() === 'Header';
                }).length;
                var letter = String.fromCharCode(65 + (headerCount % 26));
                return letter + '. ';
            }

            function recalcHeaderPrefixes() {
                var headerCount = 0;
                $('[data-repeater-list="group-a"] .repeater-wrapper').each(function () {
                    var isHeader = $(this).hasClass('header-row-wrapper') || $(this).find('.item-category-value').val() === 'Header';
                    if (isHeader) {
                        var $input = $(this).find('.header-title-input');
                        var val = $input.val() || '';
                        var prefix = String.fromCharCode(65 + (headerCount % 26)) + '. ';
                        var cleanVal = val.replace(/^[A-Z]\.\s*/i, '');
                        $input.val(prefix + cleanVal);
                        headerCount++;
                    }
                });
            }

            // Hitung subtotal tiap section di bawah Head Title sampai Head Title berikutnya
            function recalcSectionSubtotals() {
                $('[data-repeater-list="group-a"] .repeater-wrapper').each(function () {
                    var $row = $(this);
                    var isHeader = $row.hasClass('header-row-wrapper') || $row.find('.item-category-value').val() === 'Header';
                    if (!isHeader) return;

                    var subtotal = 0;
                    var $next = $row.next('.repeater-wrapper');
                    while ($next.length && !$next.hasClass('header-row-wrapper') && $next.find('.item-category-value').val() !== 'Header') {
                        var harga = parseFloat($next.find('.invoice-item-price').val()) || 0;
                        var qty = parseFloat($next.find('.invoice-item-qty').val()) || 0;
                        var disc = parseFloat($next.find('.invoice-item-disc').val()) || 0;
                        var hasil = harga * qty;
                        var amount = Math.round(hasil - (hasil * disc / 100));
                        subtotal += amount;
                        $next = $next.next('.repeater-wrapper');
                    }
                    $row.find('.section-subtotal-badge').text('Subtotal: ' + formatter.format(subtotal));
                });
            }

            // Unified calculation function for row amounts, subtotal, discount, tax, delivery, and grand total
            function recalculateTotals() {
                var sTotal = 0;
                $('.repeater-wrapper').each(function() {
                    var $row = $(this);
                    var isHeader = $row.hasClass('header-row-wrapper') || $row.find('.item-category-value').val() === 'Header';
                    if (isHeader) return;

                    var $priceLabel = $row.find('.invoice-item-price-label');
                    var $priceHidden = $row.find('.invoice-item-price');
                    var harga = 0;
                    if ($priceLabel.length && $priceLabel.val() !== '') {
                        harga = parseCurrency($priceLabel.val());
                        $priceHidden.val(harga);
                    } else {
                        harga = parseFloat($priceHidden.val()) || 0;
                    }

                    var qty = parseFloat($row.find('.invoice-item-qty').val()) || 0;
                    var disc = parseFloat($row.find('.invoice-item-disc').val()) || 0;
                    var hasil = harga * qty;
                    var amount = Math.round(hasil - (hasil * disc / 100));

                    $row.find('.invoice-item-amount').val(amount);
                    $row.find('.amount-label').html(formatter.format(amount));
                    sTotal += amount;
                });

                $('#subtotal').val(sTotal);
                $('#subtotal-label').html(formatter.format(sTotal));

                var discount = parseFloat($('#diskon').val()) || 0;
                var deliveryCost = parseFloat($('#delivery-cost').val()) || 0;
                var dTotal = Math.max(0, sTotal - discount);
                var taxPercent = parseFloat($('#tax').val()) || 0;

                if (discount > 0) {
                    $('.subtotal-after-discount-row').removeClass('d-none');
                    $('#subtotalAfterDiscountLabel').html(formatter.format(dTotal));
                } else {
                    $('.subtotal-after-discount-row').addClass('d-none');
                }
                var dppNilaiLain = taxPercent > 0 ? Math.round(dTotal * 11 / 12) : 0;
                var taxAmount = taxPercent > 0 ? Math.round(dTotal * taxPercent / 100) : 0;
                var hTotal = Math.round(dTotal + taxAmount + deliveryCost);

                if (taxPercent > 0) {
                    $('.dpp-nilai-lain-row').removeClass('d-none');
                    $('#dppNilaiLainLabel').html(formatter.format(dppNilaiLain));
                } else {
                    $('.dpp-nilai-lain-row').addClass('d-none');
                }

                $('#taxAmountLabel').html(taxPercent > 0 ? formatter.format(taxAmount) : '');
                $('#hargaTotal').val(hTotal);
                $('#hargaTotalLabel').html(formatter.format(hTotal));
                $('#totalNoTax').val(dTotal);

                recalcSectionSubtotals();
            }

            // Jalankan recalculate saat pertama load
            recalculateTotals();

            // Kategori per-item: Sparepart (select2 dari tabel Product) vs Unit Global (select2 dari tabel Unit)

            // Tampilkan SKU di depan (bold) untuk opsi Unit Global
            function renderUnitOption(option) {
                if (!option.id) {
                    return option.text;
                }
                var sku = $(option.element).data('sku');
                var name = $(option.element).data('name') || option.text;
                var $wrapper = $('<span></span>');
                if (sku) {
                    $wrapper.append($('<strong></strong>').text(sku));
                    $wrapper.append(document.createTextNode(' - ' + name));
                } else {
                    $wrapper.append(document.createTextNode(name));
                }
                return $wrapper;
            }

            function initItemSelect2($scope) {
                $scope.find('.select2-product-po').each(function() {
                    var $el = $(this);
                    if (!$el.data('select2')) {
                        $el.select2({
                            placeholder: 'Cari SKU / Product...',
                            width: '100%',
                            dropdownParent: $el.closest('.field-product-sparepart'),
                            allowClear: true,
                            ajax: {
                                url: '{{ route("purchase.products.search") }}',
                                dataType: 'json',
                                delay: 250,
                                data: function(params) {
                                    return {
                                        q: params.term || '',
                                        page: params.page || 1
                                    };
                                },
                                processResults: function(data, params) {
                                    params.page = params.page || 1;
                                    return {
                                        results: data.results,
                                        pagination: data.pagination
                                    };
                                },
                                cache: true
                            },
                            minimumInputLength: 0,
                            templateResult: function(item) {
                                if (!item.id) return item.text;
                                var $box = $('<div></div>');
                                var $title = $('<div class="fw-bold text-dark"></div>').text(item.commodity || item.text);
                                $box.append($title);
                                if (item.description && item.description !== '-') {
                                    $box.append($('<div class="text-muted small" style="font-size: 0.78rem;"></div>').text(item.description));
                                }
                                return $box;
                            },
                            templateSelection: function(item) {
                                return item.text || item.commodity || 'Cari SKU / Product...';
                            },
                            escapeMarkup: function(m) { return m; }
                        });
                    }
                });
                $scope.find('.select2-unit-po').each(function() {
                    var $el = $(this);
                    if (!$el.data('select2')) {
                        $el.select2({
                            placeholder: 'Cari Unit...',
                            width: '100%',
                            dropdownParent: $el.closest('.field-product-unit'),
                            templateResult: renderUnitOption,
                            templateSelection: renderUnitOption,
                            escapeMarkup: function(m) {
                                return m;
                            }
                        });
                    }
                });
                $scope.find('.select2-accessory-po').each(function() {
                    var $el = $(this);
                    if (!$el.data('select2')) {
                        $el.select2({
                            placeholder: 'Pilih Aksesoris Rental...',
                            width: '100%',
                            dropdownParent: $el.closest('.field-product-accessory')
                        });
                    }
                });
                $scope.find('.select2-info-qty').each(function() {
                    var $el = $(this);
                    if (!$el.data('select2')) {
                        $el.select2({
                            placeholder: 'Satuan...',
                            tags: true,
                            width: '100%',
                            dropdownParent: $el.parent()
                        });
                    }
                });
            }

            // Info Qty ikut otomatis dari master: Sparepart -> unit di tabel Product, Unit Global -> selalu "Unit"
            function lockInfoQty($fields, forcedValue) {
                var $info = $fields.closest('.row').find('.invoice-item-info');
                var $container = $info.next('.select2-container');
                $info.addClass('pe-none bg-light').attr('tabindex', '-1').attr('aria-readonly', 'true');
                $container.addClass('pe-none opacity-75');
                if (forcedValue) {
                    var matched = $info.find('option').filter(function() {
                        return $(this).val().toLowerCase() === String(forcedValue).toLowerCase();
                    });
                    if (matched.length) {
                        $info.val(matched.val()).trigger('change.select2');
                    } else {
                        var newOpt = new Option(forcedValue, forcedValue, true, true);
                        $info.append(newOpt).trigger('change.select2');
                    }
                }
            }

            function unlockInfoQty($fields) {
                var $info = $fields.closest('.row').find('.invoice-item-info');
                var $container = $info.next('.select2-container');
                $info.removeClass('pe-none bg-light').removeAttr('tabindex').removeAttr('aria-readonly');
                $container.removeClass('pe-none opacity-75');
            }

            function applyRowCategory($fields) {
                var category = $fields.find('.item-category-radio:checked').val() || 'Sparepart';
                // Radio-nya sendiri gak punya name (lihat komentar di template), jadi nilai
                // yang beneran ke-submit ke server disinkronkan lewat hidden input ini.
                $fields.find('.item-category-value').val(category);
                var $sparepart = $fields.find('.field-product-sparepart');
                var $unit = $fields.find('.field-product-unit');
                var $accessory = $fields.find('.field-product-accessory');
                var $custom = $fields.find('.field-product-custom');
                var $product = $fields.find('.select2-product-po');
                var $unitSelect = $fields.find('.select2-unit-po');
                var $accessorySelect = $fields.find('.select2-accessory-po');
                var $customText = $fields.find('.invoice-item-detail-product');

                $sparepart.hide();
                $unit.hide();
                $accessory.hide();
                $custom.hide();
                $product.removeAttr('required');
                $unitSelect.removeAttr('required');
                $accessorySelect.removeAttr('required');
                $customText.removeAttr('required');

                if (category === 'Unit') {
                    $unit.show();
                    $unitSelect.attr('required', true);
                    lockInfoQty($fields, 'Unit');
                } else if (category === 'Accessories') {
                    $accessory.show();
                    $accessorySelect.attr('required', true);
                    lockInfoQty($fields, 'Unit');
                } else if (category === 'Custom') {
                    $custom.show();
                    $customText.attr('required', true);
                    unlockInfoQty($fields);
                } else {
                    $sparepart.show();
                    $product.attr('required', true);
                    lockInfoQty($fields, $product.find(':selected').data('unit'));
                }
            }

            function updateItemsCountBadge() {
                var count = $('.repeater-wrapper').length;
                $('#items-count-badge').text(count + (count === 1 ? ' Item' : ' Items'));
            }

            initItemSelect2($(document));
            $('.item-fields').each(function() {
                applyRowCategory($(this));
            });
            updateItemsCountBadge();

            // Radio per-baris (tanpa name global) supaya tiap item punya grup sendiri
            $(document).on('change', '.item-category-radio', function() {
                var $fields = $(this).closest('.item-fields');
                $fields.find('.item-category-radio').not(this).prop('checked', false);
                applyRowCategory($fields);
            });

            var pendingCustomAdd = false;
            $(document).on('repeater:added', function() {
                var $newFields = $('.repeater-wrapper').last().find('.item-fields');
                initItemSelect2($newFields);
                if (pendingCustomAdd) {
                    $newFields.find('.item-category-radio[value="Custom"]').prop('checked', true);
                    $newFields.find('.item-category-radio').not('[value="Custom"]').prop('checked', false);
                    $newFields.find('.item-category-value').val('Custom');
                    pendingCustomAdd = false;
                }
                applyRowCategory($newFields);
                updateItemsCountBadge();
                recalculateTotals();
            });

            $(document).on('repeater:deleted', function() {
                updateItemsCountBadge();
                recalcHeaderPrefixes();
                recalculateTotals();
            });

            $(document).on('click', '[data-repeater-delete]', function() {
                var $row = $(this).closest('.repeater-wrapper');
                $row.remove();
                setTimeout(function() {
                    updateItemsCountBadge();
                    recalcHeaderPrefixes();
                    recalculateTotals();
                }, 50);
            });

            // Helper membuat baris item baru di repeater secara bersih, andal, dan sinkron
            function createItemRow(isCustom) {
                if (isCustom) {
                    pendingCustomAdd = true;
                }
                var $repeaterBtn = $('.form-invoice-repeater [data-repeater-create]').first();
                if (!$repeaterBtn.length) {
                    $repeaterBtn = $('[data-repeater-create]').last();
                }
                if ($repeaterBtn.length) {
                    $repeaterBtn.trigger('click');
                }

                setTimeout(function() {
                    var $row = $('[data-repeater-list="group-a"] .repeater-wrapper').not('.header-row-wrapper').last();
                    if ($row.length) {
                        $row.stop(true, true).show();

                        // Bersihkan artefak DOM Select2 lama dari hasil clone template repeater
                        var $fields = $row.find('.item-fields');
                        $fields.find('.select2-container').remove();
                        $fields.find('.select2-hidden-accessible')
                            .removeClass('select2-hidden-accessible')
                            .removeAttr('data-select2-id')
                            .removeAttr('aria-hidden')
                            .removeAttr('tabindex');

                        initItemSelect2($fields);

                        if (isCustom) {
                            $fields.find('.item-category-radio[value="Custom"]').prop('checked', true);
                            $fields.find('.item-category-radio').not('[value="Custom"]').prop('checked', false);
                            $fields.find('.item-category-value').val('Custom');
                        }
                        applyRowCategory($fields);
                        if (isCustom) {
                            $fields.find('.field-product-custom textarea').focus();
                        }
                        updateItemsCountBadge();
                        recalculateTotals();
                    }
                }, 40);
            }

            // Delegated click listeners untuk tombol Add Item (Header & Bottom)
            $(document).on('click', '.btn-add-item-action, .po-section-header .btn-add[data-repeater-create]', function(e) {
                if ($(this).closest('.po-section-header').length) {
                    e.preventDefault();
                    createItemRow(false);
                }
            });

            // Delegated click listeners untuk tombol Add Custom Item (Header & Bottom)
            $(document).on('click', '.btn-add-custom-item-action, #btn-add-custom-item, #btn-add-custom-item-top, #btn-add-custom-item-bottom', function(e) {
                e.preventDefault();
                createItemRow(true);
            });

            // Add Head Title: tambah baris grup header custom
            function addHeaderTitleRow(titleText) {
                var prefix = getNextHeaderPrefix();
                var val = titleText !== undefined ? titleText : prefix;
                var html = `
                    <div class="repeater-wrapper header-row-wrapper" data-repeater-item="" data-category="Header">
                        <div class="position-relative border-bottom p-3" style="background:#f8f9ff !important; border-left: 4px solid #696cff !important;">
                            <input type="hidden" class="invoice-item-detail-id" name="detail_id[]" value="">
                            <input type="hidden" name="pr_detail_id[]" value="">
                            <input type="hidden" class="item-category-value" name="item_category[]" value="Header">
                            <input type="hidden" name="id_product[]" value="">
                            <input type="hidden" name="id_unit[]" value="">
                            <input type="hidden" name="id_rental_accessory[]" value="">
                            <input type="hidden" name="kondisi[]" value="">
                            <input type="hidden" class="invoice-item-price" name="price[]" value="0">
                            <input type="hidden" class="invoice-item-qty" name="qty[]" value="0">
                            <input type="hidden" class="invoice-item-info" name="info_qty[]" value="">
                            <input type="hidden" class="invoice-item-disc" name="disc[]" value="0">
                            <input type="hidden" class="invoice-item-amount" name="amount[]" value="0">

                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <div class="btn btn-sm btn-icon btn-label-secondary btn-drag-handle cursor-move flex-shrink-0" title="Geser (drag & drop) untuk memindahkan posisi" style="cursor: grab;">
                                    <i class="mdi mdi-drag-vertical fs-5"></i>
                                </div>
                                <span class="badge bg-primary text-uppercase" style="font-size:10.5px; letter-spacing:0.5px;">
                                    <i class="mdi mdi-bookmark-outline me-1"></i>Head Title
                                </span>
                                <div class="flex-grow-1" style="min-width: 250px;">
                                    <input type="text" class="form-control form-control-sm fw-bold text-primary header-title-input invoice-item-detail-product"
                                        name="product[]" placeholder="Head Title (e.g. A. SCOPE OF WORK, B. SPAREPART) *"
                                        value="${val}" required>
                                </div>
                                <span class="badge bg-label-primary section-subtotal-badge text-nowrap" style="font-size:11px;">Subtotal: Rp 0</span>
                                <button type="button" class="btn btn-sm btn-icon btn-label-danger btn-del ms-auto" data-repeater-delete="" title="Hapus Head Title">
                                    <i class="mdi mdi-delete-outline"></i>
                                </button>
                            </div>
                        </div>
                    </div>`;
                var $newHead = $(html);
                $('[data-repeater-list="group-a"]').append($newHead);
                $newHead.find('.header-title-input').focus();
                updateItemsCountBadge();
                recalcHeaderPrefixes();
                recalculateTotals();
            }

            // Delegated click listeners untuk tombol Add Head Title (Header & Bottom)
            $(document).on('click', '.btn-add-header-title-action, #btn-add-header-title, #btn-add-header-title-top, #btn-add-header-title-bottom', function(e) {
                e.preventDefault();
                addHeaderTitleRow();
            });

            // Inisialisasi Sortable Drag & Drop untuk baris item & Head Title
            function initSortableItems() {
                var container = document.querySelector('[data-repeater-list="group-a"]');
                if (container && typeof Sortable !== 'undefined') {
                    Sortable.create(container, {
                        handle: '.btn-drag-handle',
                        animation: 150,
                        ghostClass: 'bg-light-primary',
                        onEnd: function () {
                            recalcHeaderPrefixes();
                            recalculateTotals();
                        }
                    });
                }
            }
            initSortableItems();

            // Sinkronkan label Product/Unit terpilih ke field product[] tersembunyi (dipakai halaman detail/print)
            $(document).on('change', '.select2-product-po, .select2-unit-po, .select2-accessory-po', function() {
                var $fields = $(this).closest('.item-fields');
                var label = $(this).find(':selected').data('label') || $(this).find(':selected').text().trim() || '';
                $fields.find('.invoice-item-detail-product').val(label);
                applyRowCategory($fields);
            });

            // Handler khusus ketika item dipilih lewat Select2 AJAX
            $(document).on('select2:select', '.select2-product-po', function(e) {
                var data = e.params.data;
                var $fields = $(this).closest('.item-fields');
                if (data) {
                    if (data.unit) {
                        lockInfoQty($fields, data.unit);
                    }
                    var fullLabel = data.text || (data.commodity + (data.description && data.description !== '-' ? ' — ' + data.description : ''));
                    $fields.find('.invoice-item-detail-product').val(fullLabel);
                    applyRowCategory($fields);
                }
            });

            // ── Handler Modal Tarik Item PR (Multi-PR Consolidation) ──
            var cachedPrItems = [];

            function loadAvailablePrItems() {
                var $tbody = $('#tbodyPullPrItems');
                $tbody.html('<tr><td colspan="7" class="text-center py-4 text-muted">' +
                    '<span class="spinner-border spinner-border-sm me-2 text-primary" role="status"></span>' +
                    'Memuat daftar item Purchase Request yang tersedia...</td></tr>');
                $('#checkAllModalPrItems').prop('checked', false);
                $('#btnApplyPullPrItems').prop('disabled', true);
                $('#countSelectedPrItems').text('0');

                $.ajax({
                    url: '{{ route("purchase-request.available-items") }}',
                    type: 'GET',
                    dataType: 'json',
                    success: function (res) {
                        cachedPrItems = res.items || [];
                        renderPrItemsTable(cachedPrItems);
                    },
                    error: function () {
                        $tbody.html('<tr><td colspan="7" class="text-center py-4 text-danger">' +
                            '<i class="mdi mdi-alert-circle-outline me-1"></i> Gagal memuat data item PR. Silakan coba lagi.</td></tr>');
                    }
                });
            }

            function renderPrItemsTable(items) {
                var $tbody = $('#tbodyPullPrItems');
                $tbody.empty();

                var query = ($('#inputSearchPrItems').val() || '').trim().toLowerCase();
                var filtered = items;
                if (query) {
                    filtered = items.filter(function (it) {
                        var pool = (it.no_pr + ' ' + it.no_so + ' ' + it.brand_pn + ' ' + it.product_name + ' ' + it.note).toLowerCase();
                        return pool.indexOf(query) !== -1;
                    });
                }

                if (!filtered.length) {
                    $tbody.html('<tr><td colspan="7" class="text-center py-4 text-muted">' +
                        '<i class="mdi mdi-information-outline me-1"></i> Tidak ada item PR yang cocok / tersedia.</td></tr>');
                    return;
                }

                filtered.forEach(function (it) {
                    var $tr = $('<tr>' +
                        '<td class="text-center">' +
                            '<input type="checkbox" class="form-check-input check-pr-item" data-id="' + it.pr_detail_id + '">' +
                        '</td>' +
                        '<td>' +
                            '<span class="fw-bold text-primary font-monospace font-12">' + it.no_pr + '</span>' +
                            '<div class="text-muted font-11"><i class="mdi mdi-calendar-outline me-1"></i>' + it.pr_date + '</div>' +
                        '</td>' +
                        '<td>' +
                            '<span class="badge bg-label-dark font-11">' + it.no_so + '</span>' +
                        '</td>' +
                        '<td>' +
                            '<div class="fw-semibold text-dark font-13">' + it.product_name + '</div>' +
                            (it.brand_pn && it.brand_pn !== it.product_name ? '<div class="text-muted font-11">' + it.brand_pn + '</div>' : '') +
                        '</td>' +
                        '<td class="text-center">' +
                            '<span class="badge bg-label-info font-12">' + it.remaining_qty + ' ' + it.unit + '</span>' +
                        '</td>' +
                        '<td class="text-center">' +
                            '<input type="number" class="form-control form-control-sm text-center input-qty-take mx-auto" ' +
                                'data-id="' + it.pr_detail_id + '" min="1" max="' + it.remaining_qty + '" value="' + it.remaining_qty + '" style="width: 80px;">' +
                        '</td>' +
                        '<td>' +
                            '<span class="text-muted small">' + it.note + '</span>' +
                        '</td>' +
                    '</tr>');
                    $tbody.append($tr);
                });

                updateModalSelectionCount();
            }

            function updateModalSelectionCount() {
                var checkedCount = $('#tablePullPrItems .check-pr-item:checked').length;
                $('#countSelectedPrItems').text(checkedCount);
                $('#btnApplyPullPrItems').prop('disabled', checkedCount === 0);
                $('#btnApplyPullPrItems span').text(checkedCount > 0 ? 'Tambahkan (' + checkedCount + ' item) ke PO' : 'Tambahkan ke PO');
            }

            $('#modalPullPrItems').on('shown.bs.modal', function () {
                if (!cachedPrItems.length) {
                    loadAvailablePrItems();
                }
                $('#inputSearchPrItems').focus();
            });

            $('#btnReloadPrItems').on('click', function () {
                loadAvailablePrItems();
            });

            $('#inputSearchPrItems').on('input', function () {
                renderPrItemsTable(cachedPrItems);
            });

            $('#checkAllModalPrItems').on('change', function () {
                var isChecked = $(this).is(':checked');
                $('#tablePullPrItems .check-pr-item').prop('checked', isChecked);
                updateModalSelectionCount();
            });

            $(document).on('change', '.check-pr-item', function () {
                updateModalSelectionCount();
            });

            // Action: Tambahkan Item Terpilih ke Repeater Form PO
            $('#btnApplyPullPrItems').on('click', function () {
                var selected = [];
                $('#tablePullPrItems .check-pr-item:checked').each(function () {
                    var detailId = $(this).data('id');
                    var it = cachedPrItems.find(function (x) { return String(x.pr_detail_id) === String(detailId); });
                    if (it) {
                        var $qtyInput = $('#tablePullPrItems .input-qty-take[data-id="' + detailId + '"]');
                        var takeQty = parseInt($qtyInput.val(), 10) || it.remaining_qty || 1;
                        selected.push({
                            item: it,
                            qty: takeQty
                        });
                    }
                });

                if (!selected.length) return;

                // Cek apakah baris pertama repeater kosong (bisa ditimpa)
                var $existingRows = $('[data-repeater-list="group-a"] .repeater-wrapper').not('.header-row-wrapper');
                var canReuseFirstRow = false;
                if ($existingRows.length === 1) {
                    var $first = $existingRows.first();
                    var hasVal = ($first.find('.select2-product-po').val() || $first.find('.select2-unit-po').val() ||
                        $first.find('.select2-accessory-po').val() || ($first.find('textarea[name="product[]"]').val() || '').trim() ||
                        parseFloat($first.find('.invoice-item-qty').val()) > 0);
                    if (!hasVal) {
                        canReuseFirstRow = true;
                    }
                }

                selected.forEach(function (entry, idx) {
                    var it = entry.item;
                    var takeQty = entry.qty;

                    var $row;
                    if (idx === 0 && canReuseFirstRow) {
                        $row = $existingRows.first();
                        var $fields = $row.find('.item-fields');
                        $fields.find('.select2-container').remove();
                        $fields.find('.select2-hidden-accessible')
                            .removeClass('select2-hidden-accessible')
                            .removeAttr('data-select2-id')
                            .removeAttr('aria-hidden')
                            .removeAttr('tabindex');
                        initItemSelect2($fields);
                    } else {
                        $row = createItemRow();
                    }

                    // Isi data ke baris repeater
                    $row.find('input[name="pr_detail_id[]"]').val(it.pr_detail_id);

                    if (it.id_product) {
                        $row.find('.item-category-radio[value="Sparepart"]').prop('checked', true).trigger('change');
                        var $productSelect = $row.find('.select2-product-po');
                        if (!$productSelect.find('option[value="' + it.id_product + '"]').length) {
                            $productSelect.append(new Option(it.product_name, it.id_product, true, true));
                        }
                        $productSelect.val(String(it.id_product)).trigger('change');
                        $row.find('.invoice-item-detail-product').val(it.product_name);
                    } else {
                        $row.find('.item-category-radio[value="Custom"]').prop('checked', true).trigger('change');
                        $row.find('.field-product-custom textarea[name="product[]"]').val(it.product_name);
                        $row.find('.invoice-item-detail-product').val(it.product_name);
                    }

                    // Set Qty & Satuan
                    $row.find('.invoice-item-qty').val(takeQty).attr('data-pr-remaining', it.remaining_qty).trigger('input');
                    if (it.unit) {
                        var $infoSelect = $row.find('.invoice-item-info');
                        if (!$infoSelect.find('option[value="' + it.unit + '"]').length) {
                            $infoSelect.append(new Option(it.unit, it.unit, true, true));
                        }
                        $infoSelect.val(it.unit).trigger('change');
                    }

                    // Badge asal PR & SO
                    $row.find('.badge-pr-origin').remove();
                    var $badge = $('<div class="mt-1 badge-pr-origin">' +
                        '<span class="badge bg-label-primary font-11 d-inline-flex align-items-center gap-1">' +
                            '<i class="mdi mdi-clipboard-text-outline"></i> ' + it.no_pr + ' &bull; SO: ' + it.no_so +
                        '</span>' +
                    '</div>');
                    $row.find('.item-fields').append($badge);
                });

                // Update hidden id_purchase_request jika belum ada
                var $mainPrInput = $('input[name="id_purchase_request"]');
                if (!$mainPrInput.val() && selected[0] && selected[0].item.pr_id) {
                    $mainPrInput.val(selected[0].item.pr_id);
                }

                $('#modalPullPrItems').modal('hide');
                recalculateTotals();
                updateItemsCountBadge();

                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Item Ditambahkan',
                        text: selected.length + ' item dari Purchase Request berhasil dimasukkan ke dalam PO.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
            });
        })
    </script>
@endpush

@if (!@$purchase)
@push('script')
    {{--
        ── Autosave Draft PO (client-side, sama pola dengan Smart Quote) ──
        Draft disimpan di localStorage per-browser: semua field header + line item.
        - Dibuka dari prefill (?from_pr= / ?from_product_set= / ?items= / ?product_ids=):
          draft TIDAK di-restore (biar tidak menimpa prefill), tapi autosave tetap jalan.
        - no_po TIDAK ikut di-restore (selalu pakai nomor baru yang di-generate server).
        - Draft dihapus otomatis saat form berhasil disubmit.
    --}}
    <script>
        (function () {
            const qs = new URLSearchParams(window.location.search);
            const USER_ID = @json(Auth::id() ?? 'guest');
            const SOURCE_PR = qs.get('from_pr') || 'new';
            const DRAFT_KEY = 'po_draft_' + USER_ID + '_' + SOURCE_PR;
            const HAS_PREFILL = qs.has('from_pr') || qs.has('from_product_set') || qs.has('items') || qs.has('product_ids');

            let draft = null;
            try {
                const raw = localStorage.getItem(DRAFT_KEY);
                if (raw) draft = JSON.parse(raw);
            } catch (e) { console.warn('PO draft load error', e); }

            const fmtId = (n) => (Number(n) || 0).toLocaleString('id-ID');
            const $status = $('#poDraftStatus');

            function markStatus(text, cls) {
                $status.removeClass('d-none bg-label-secondary bg-label-success bg-label-warning bg-label-info')
                    .addClass(cls || 'bg-label-success');
                $status.find('.txt').text(text);
                $('#poDraftReset').removeClass('d-none');
            }

            // ── Kumpulkan snapshot form ──
            function collectDraft() {
                const d = {
                    saved_at: Date.now(),
                    saved_at_formatted: new Date().toLocaleString('id-ID', { dateStyle: 'medium', timeStyle: 'short' }),
                    supplier: $('#supplier-dropdown').val() || '',
                    supplier_label: $('#supplier-dropdown option:selected').text().trim() || '',
                    attn: $('#attn').val() || '',
                    mobile: $('#mobile').val() || '',
                    address: $('#address').val() || '',
                    date: $('#date').val() || '',
                    no_reference: $('#no_reference').val() || '',
                    delivery: $('#delivery').val() || '',
                    payment_select: $('#payment-select').val() || '',
                    payment_manual: $('#input-payment-manual').val() || '',
                    payment_hidden: $('#input-payment-hidden').val() || '',
                    payment_type: $('#payment-type-select').val() || 'cash',
                    top_days: $('#top-days-input').val() || '',
                    due_date_estimate: $('#due-date-estimate-input').val() || '',
                    category: $('#po-type-select').val() || '',
                    ship_to: $('#ship_to_input').val() || '',
                    note: $('[name="note"]').val() || '',
                    items: []
                };
                $('[data-repeater-list="group-a"] .repeater-wrapper').each(function () {
                    const $r = $(this);
                    if ($r.is('.header-row-wrapper')) {
                        d.items.push({ category: 'Header', product: $r.find('input[name="product[]"]').val() || '' });
                        return;
                    }
                    const cat = $r.find('.item-category-value').val() || 'Sparepart';
                    d.items.push({
                        category: cat,
                        id_product: $r.find('.select2-product-po').val() || '',
                        id_product_label: $r.find('.select2-product-po option:selected').text().trim() || '',
                        id_unit: $r.find('.select2-unit-po').val() || '',
                        id_unit_label: $r.find('.select2-unit-po option:selected').text().trim() || '',
                        kondisi: $r.find('.select-kondisi-unit').val() || 'Baru',
                        id_rental_accessory: $r.find('.select2-accessory-po').val() || '',
                        id_rental_accessory_label: $r.find('.select2-accessory-po option:selected').text().trim() || '',
                        product: $r.find('.field-product-custom textarea[name="product[]"]').val() || '',
                        qty: $r.find('.invoice-item-qty').val() || '',
                        info_qty: $r.find('.invoice-item-info').val() || '',
                        disc: $r.find('.invoice-item-disc').val() || '',
                        price: $r.find('.invoice-item-price').val() || ''
                    });
                });
                return d;
            }

            let saveTimer = null;
            function scheduleSave() {
                markStatus('Menyimpan...', 'bg-label-warning');
                clearTimeout(saveTimer);
                saveTimer = setTimeout(function () {
                    try {
                        localStorage.setItem(DRAFT_KEY, JSON.stringify(collectDraft()));
                        const t = new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
                        markStatus('Tersimpan ' + t, 'bg-label-success');
                    } catch (e) { console.warn('PO draft save error', e); }
                }, 600);
            }

            // ── Restore helper: pastikan option ada di select lalu pilih ──
            function setSelect($sel, val, label) {
                if (!$sel.length || !val) return;
                if (!$sel.find('option[value="' + String(val).replace(/"/g, '\\"') + '"]').length) {
                    $sel.append(new Option(label || val, val, true, true));
                }
                $sel.val(String(val)).trigger('change');
            }

            function restoreItems(items) {
                if (!items || !items.length) return;
                // Buang baris starter kosong bawaan
                const $preexisting = $('[data-repeater-list="group-a"] .repeater-wrapper');

                items.forEach(function (it) {
                    if (it.category === 'Header') {
                        $('#btn-add-header-title').trigger('click');
                        $('[data-repeater-list="group-a"] .header-row-wrapper').last()
                            .find('input[name="product[]"]').val(it.product || '');
                        return;
                    }
                    var $r;
                    if (it.category === 'Custom') {
                        $r = createItemRow();
                        $r.find('.item-category-radio[value="Custom"]').prop('checked', true).trigger('change');
                    } else {
                        $r = createItemRow();
                    }

                    if (it.category && it.category !== 'Sparepart') {
                        $r.find('.item-category-radio[value="' + it.category + '"]').prop('checked', true).trigger('change');
                    }
                    if (it.category === 'Sparepart') setSelect($r.find('.select2-product-po'), it.id_product, it.id_product_label);
                    if (it.category === 'Unit') {
                        setSelect($r.find('.select2-unit-po'), it.id_unit, it.id_unit_label);
                        $r.find('.select-kondisi-unit').val(it.kondisi || 'Baru');
                    }
                    if (it.category === 'Accessories') setSelect($r.find('.select2-accessory-po'), it.id_rental_accessory, it.id_rental_accessory_label);
                    if (it.category === 'Custom') $r.find('.field-product-custom textarea[name="product[]"]').val(it.product || '');

                    if (it.info_qty) setSelect($r.find('.invoice-item-info'), it.info_qty, it.info_qty);
                    $r.find('.invoice-item-qty').val(it.qty || '').trigger('input');
                    $r.find('.invoice-item-disc').val(it.disc || '').trigger('input');
                    if (it.price) {
                        $r.find('.invoice-item-price').val(it.price);
                        $r.find('.invoice-item-price-label').val(fmtId(it.price)).trigger('input');
                    }
                });

                // Hapus baris starter yang masih kosong
                $preexisting.each(function () {
                    const $r = $(this);
                    const hasVal = ($r.find('.select2-product-po').val() || $r.find('.select2-unit-po').val() ||
                        $r.find('.select2-accessory-po').val() || ($r.find('textarea[name="product[]"]').val() || '').trim() ||
                        parseFloat($r.find('.invoice-item-qty').val()) > 0);
                    if (!hasVal) { $r.find('[data-repeater-delete]').trigger('click'); }
                });
            }

            function restoreDraft() {
                if (!draft) return;
                // Header
                if (draft.supplier) {
                    setSelect($('#supplier-dropdown'), draft.supplier, draft.supplier_label);
                }
                $('#date').val(draft.date || $('#date').val());
                $('#no_reference').val(draft.no_reference || '');
                $('#delivery').val(draft.delivery || $('#delivery').val());
                if (draft.category) $('#po-type-select').val(draft.category).trigger('change');
                if (draft.note) $('[name="note"]').val(draft.note);

                // Payment term
                if (draft.payment_select) {
                    $('#payment-select').val(draft.payment_select).trigger('change');
                    if (draft.payment_select === 'manual') $('#input-payment-manual').val(draft.payment_manual || '').trigger('input');
                }
                if (draft.payment_type) {
                    $('#payment-type-select').val(draft.payment_type).trigger('change');
                    if (draft.payment_type === 'tempo') {
                        if (draft.top_days) $('#top-days-input').val(draft.top_days).trigger('input');
                        if (draft.due_date_estimate) $('#due-date-estimate-input').val(draft.due_date_estimate);
                    }
                }

                // ship_to
                if (draft.ship_to) {
                    $('#ship_to_input').val(draft.ship_to).trigger('input');
                }

                // Line items
                restoreItems(draft.items);

                // Mobile/Address/ATTN di-set setelah supplier change selesai
                setTimeout(function () {
                    if (draft.mobile) $('#mobile').val(draft.mobile);
                    if (draft.address) $('#address').val(draft.address);
                    if (draft.attn) {
                        const $a = $('#attn');
                        if (!$a.find('option[value="' + String(draft.attn).replace(/"/g, '\\"') + '"]').length) {
                            $a.append(new Option(draft.attn, draft.attn, true, true));
                        }
                        $a.val(draft.attn).trigger('change');
                    }
                }, 1200);

                markStatus('Draf dipulihkan', 'bg-label-info');
            }

            $(function () {
                const hasContent = draft && (draft.supplier || (draft.items && draft.items.some(i =>
                    i.id_product || i.id_unit || i.id_rental_accessory || (i.product || '').trim())));

                if (hasContent && !HAS_PREFILL) {
                    const validCount = (draft.items || []).filter(i => i.id_product || i.id_unit || i.id_rental_accessory || (i.product || '').trim()).length;
                    $('#poDraftTimeText').text(draft.saved_at_formatted || 'sebelumnya');
                    $('#poDraftItemCount').text(validCount);
                    $('#poDraftRecoveryAlert').removeClass('d-none').addClass('d-flex');
                    $('#poDraftReset').removeClass('d-none');
                    markStatus('Draf tersimpan', 'bg-label-info');
                } else {
                    $('#poDraftReset').removeClass('d-none');
                    $status.removeClass('d-none').addClass('bg-label-secondary').find('.txt').text('Autosave Aktif');
                }

                $('#btnApplyDraft').on('click', function () {
                    $('#poDraftRecoveryAlert').removeClass('d-flex').addClass('d-none');
                    restoreDraft();
                });

                $('#btnDismissDraft').on('click', function () {
                    $('#poDraftRecoveryAlert').removeClass('d-flex').addClass('d-none');
                    try { localStorage.removeItem(DRAFT_KEY); } catch (e) {}
                    markStatus('Autosave Aktif', 'bg-label-secondary');
                });

                // Autosave saat ada perubahan di form
                $('#formAuthentication').on('input change', 'input, select, textarea', scheduleSave);
                $(document).on('repeater:added repeater:deleted', scheduleSave);

                // Bersihkan draft saat submit berhasil dan sinkronkan nilai numerik
                $('#formAuthentication').on('submit', function () {
                    $('.repeater-wrapper').each(function() {
                        var $row = $(this);
                        var isHeader = $row.hasClass('header-row-wrapper') || $row.find('.item-category-value').val() === 'Header';
                        if (!isHeader) {
                            var $label = $row.find('.invoice-item-price-label');
                            if ($label.length && $label.val() !== '') {
                                $row.find('.invoice-item-price').val(parseCurrency($label.val()));
                            }
                        }
                    });
                    try { localStorage.removeItem(DRAFT_KEY); } catch (e) {}
                });

                $('#poDraftReset').on('click', function () {
                    if (confirm('Hapus draf PO tersimpan dan mulai dari awal?')) {
                        try { localStorage.removeItem(DRAFT_KEY); } catch (e) {}
                        window.location = window.location.pathname;
                    }
                });
            });
        })();
    </script>
@endpush
@endif
