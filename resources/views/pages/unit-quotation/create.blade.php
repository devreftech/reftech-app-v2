@extends('layouts.sales.app')
@section('title', 'Create Smart Quote')
@section('hide-chat', true)
@section('content')
    {{-- Hero Page Header & Top Bar --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center py-3 mb-3 gap-3">
        <div>
            <h4 class="fw-bold mb-1">
                <span class="text-muted fw-light">Sales / <a href="{{ route('unit-quotation.index') }}" class="text-muted">Smart Quote</a> /</span> Create
            </h4>
            <p class="text-muted mb-0 small"><i class="mdi mdi-file-document-edit-outline me-1"></i> Create official B2B smart quote document</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('unit-quotation.index') }}" class="btn btn-label-secondary">
                <i class="mdi mdi-arrow-left me-1"></i> Back
            </a>
            <button type="submit" form="form-unit-quotation" class="btn btn-primary shadow-sm">
                <i class="mdi mdi-content-save me-1"></i> Save Quotation
            </button>
        </div>
    </div>

    <form action="{{ route('unit-quotation.store') }}" method="POST" id="form-unit-quotation">
        @csrf
        <input type="hidden" name="id_prospect" value="{{ $selectedProspect ?? '' }}">

        {{-- Hero Quotation Header Card --}}
        <div class="card mb-4 border-0 shadow-sm" style="background: linear-gradient(135deg, #f8f9ff 0%, #f0f2ff 100%); border-left: 5px solid #696cff !important;">
            <div class="card-body py-3">
                <div class="row align-items-center g-3">
                    <div class="col-md-8 col-12">
                        <label class="form-label text-uppercase fw-bold text-primary small mb-1" style="letter-spacing: .5px;">
                            <i class="mdi mdi-pound me-1"></i> Quotation Number
                        </label>
                        <input type="text" class="form-control form-control-lg fw-bold bg-white text-primary border-primary-subtle shadow-sm"
                            name="no_quote" placeholder="Quotation Number" value="{{ old('no_quote', $defaultNoQuote) }}" style="font-size: 1.35rem;">
                    </div>
                    <div class="col-md-4 col-12 text-md-end">
                        <span class="badge bg-label-primary px-3 py-2 fs-6 rounded-pill">
                            <i class="mdi mdi-clock-outline me-1"></i> STATUS: DRAFT
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- HEADER CLIENT & DETAILS --}}
        <div class="card mb-4 border-0 shadow-sm">
            <div class="card-header bg-transparent border-bottom py-3 d-flex align-items-center">
                <h6 class="card-title mb-0 fw-bold text-dark">
                    <i class="mdi mdi-domain me-2 text-primary fs-5"></i> Customer Information & Quotation Details
                </h6>
            </div>
            <div class="card-body pt-4">
                <div class="row g-3">
                    {{-- Sub Header: Customer & Alamat --}}
                    <div class="col-12">
                        <div class="d-flex align-items-center text-muted small fw-bold text-uppercase mb-1" style="letter-spacing:.5px;">
                            <i class="mdi mdi-account-group-outline me-1"></i> Customer & Delivery Address
                        </div>
                    </div>
                    @if ($isManager ?? false)
                        <div class="col-md-4">
                            <div class="p-2.5 mb-2 rounded-3 border bg-white shadow-none">
                                <div class="d-flex align-items-center justify-content-between mb-1.5">
                                    <span class="text-muted small fw-bold text-uppercase" style="font-size: 10.5px; letter-spacing: 0.5px;">
                                        <i class="mdi mdi-filter-variant text-primary me-1"></i> Mode Sumber Client
                                    </span>
                                </div>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="form-check form-check-inline mb-0">
                                        <input class="form-check-input" type="radio" name="client_source_type" id="src_by_sales" value="sales" checked>
                                        <label class="form-check-label fw-semibold text-dark" for="src_by_sales" style="font-size: 12px; cursor: pointer;">
                                            <i class="mdi mdi-account-tie text-primary me-0.5"></i> By Sales
                                        </label>
                                    </div>
                                    <div class="form-check form-check-inline mb-0">
                                        <input class="form-check-input" type="radio" name="client_source_type" id="src_self_leads" value="self_leads">
                                        <label class="form-check-label fw-semibold text-dark" for="src_self_leads" style="font-size: 12px; cursor: pointer;">
                                            <i class="mdi mdi-account-arrow-right text-success me-0.5"></i> Leads Sendiri
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div id="sales-select-container">
                                <div class="form-floating form-floating-outline">
                                    <select class="select2 form-select" name="id_sales" id="sales-select">
                                        <option value="">-- Semua Sales (Default) --</option>
                                        @foreach ($salesUsers as $s)
                                            <option value="{{ $s->id }}">{{ $s->name }}</option>
                                        @endforeach
                                    </select>
                                    <label>Filter by Sales</label>
                                </div>
                                <div class="form-text small text-muted" style="font-size: 11px;">
                                    Pilih sales untuk memuat client miliknya, atau pilih opsi <strong>Leads Sendiri</strong> di atas.
                                </div>
                            </div>

                            <div id="self-leads-banner" class="alert alert-subtle-success py-2 px-3 mb-0 rounded-3 d-none align-items-center gap-2" style="font-size: 12px; background-color: #e8fadf; border: 1px solid #71dd37; color: #2e6815;">
                                <i class="mdi mdi-check-circle-outline fs-5 text-success"></i>
                                <div>
                                    <strong class="d-block" style="font-size: 12px;">Mode Leads Sendiri Aktif</strong>
                                    <span style="font-size: 11px; color: #43762b;">Memuat data leads yang diinput sendiri oleh Anda.</span>
                                </div>
                            </div>
                        </div>
                    @endif
                    <div class="col-md-4">
                        <div class="form-floating form-floating-outline">
                            <select class="select2 form-select" name="id_client" id="client-select">
                                <option value="">-- Select Client --</option>
                                @foreach ($clients as $c)
                                    <option value="{{ $c->id }}" data-role="{{ $c->role }}" {{ (old('id_client', $selectedClient ?? '') == $c->id) ? 'selected' : '' }}>{{ $c->company }}</option>
                                @endforeach
                            </select>
                            <label>Client *</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating form-floating-outline">
                            <select class="form-select" name="id_pic" id="pic-select" disabled>
                                <option value="">-- Select Client first --</option>
                            </select>
                            <label>PIC / Contact *</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating form-floating-outline">
                            <select class="form-select" id="address-select" disabled>
                                <option value="">-- Select Client first --</option>
                            </select>
                            <label>Address / Plant</label>
                        </div>
                        <input type="hidden" name="id_plant" id="input-id-plant" value="">
                        <input type="hidden" name="address" id="input-address-hidden" value="">
                    </div>
                    <div class="col-md-12" id="manual-address-wrapper" style="display: none;">
                        <div class="form-floating form-floating-outline">
                            <textarea class="form-control" id="input-address-manual" rows="2" style="height: 60px;" placeholder="Enter custom address..."></textarea>
                            <label>Custom Address</label>
                        </div>
                    </div>

                    <div class="col-12">
                        <hr class="my-2">
                        <div class="d-flex align-items-center text-muted small fw-bold text-uppercase mb-1" style="letter-spacing:.5px;">
                            <i class="mdi mdi-file-document-outline me-1"></i> Quotation Parameters & Specifications
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="form-floating form-floating-outline">
                            <input type="date" class="form-control" id="input-date" name="date" value="{{ date('Y-m-d') }}">
                            <label>Date</label>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-floating form-floating-outline">
                            <input type="date" class="form-control bg-light-subtle" id="input-expired-date" name="expired_date"
                                value="{{ date('Y-m-d', strtotime('+1 month')) }}" readonly
                                title="Auto-calculated: 1 month from quotation date">
                            <label class="text-muted">Expired Quotation</label>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-floating form-floating-outline">
                            <input type="text" class="form-control" name="no_pr" placeholder="No PR (optional)">
                            <label>No PR <span class="text-muted small">(optional)</span></label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating form-floating-outline">
                            <input type="text" class="form-control" name="title" placeholder="Title">
                            <label>Title / Description</label>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-floating form-floating-outline">
                            <select class="form-select" id="select-type" name="type">
                                <option value="" disabled>-- Type --</option>
                                <option value="Unit">Unit</option>
                                <option value="Rental">Rental</option>
                                <option value="Project" selected>Project</option>
                                <option value="Parts">Parts</option>
                                <option value="Service">Service</option>
                                <option value="Piping">Piping</option>
                                <option value="Air Audit">Air Audit</option>
                                <option value="General Check / Visit">General Check / Visit</option>
                                <option value="HVAC">HVAC</option>
                                <option value="Fire System">Fire System</option>
                            </select>
                            <label>Type</label>
                        </div>
                    </div>
                    <div class="col-md-2" id="unit-condition-wrapper" style="display:none;">
                        <div class="form-floating form-floating-outline">
                            <select class="form-select" id="select-unit-condition" name="unit_condition">
                                <option value="" disabled selected>-- Kondisi --</option>
                                <option value="Baru">Unit Baru</option>
                                <option value="Second">Unit Second</option>
                            </select>
                            <label>Kondisi Unit</label>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-floating form-floating-outline">
                            <select class="form-select" id="select-week" name="week">
                                <option value="" disabled selected>-- Week --</option>
                                <option value="1">Week 1</option>
                                <option value="2">Week 2</option>
                                <option value="3">Week 3</option>
                                <option value="4">Week 4</option>
                                <option value="5">Week 5</option>
                            </select>
                            <label>Week</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- QUOTATION OPTIONS (Opsi 1, Opsi 2, dst — buat perbandingan harga) --}}
        <div class="card mb-4 border-0 shadow-sm">
            <div class="card-header bg-transparent border-bottom py-2 d-flex align-items-center justify-content-between flex-wrap gap-2">
                <ul class="nav nav-pills flex-wrap gap-2 mb-0" id="options-tab-nav" role="tablist"></ul>
                <button type="button" class="btn btn-sm btn-outline-primary" id="btn-add-option">
                    <i class="mdi mdi-plus me-1"></i> Tambah Opsi
                </button>
            </div>
            <div class="tab-content" id="options-tab-content">
                {{-- option panes injected by JS --}}
            </div>
        </div>

        {{-- MODAL: Load Template PM (Unit Global) --}}
        <div class="modal fade" id="modal-load-pm-template" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">Load Template PM dari Unit Global</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p class="text-muted small">Pilih unit dan level PM — item template yang sudah disusun di Unit Global akan ditambahkan ke line items quotation ini.</p>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Unit</label>
                            <select class="select2 form-select" id="pm-load-unit-select" style="width:100%">
                                <option value="">Cari unit (SKU / Brand / Model)...</option>
                            </select>
                        </div>
                        <div class="mb-2">
                            <label class="form-label small fw-semibold">Level PM</label>
                            <div class="d-flex flex-wrap gap-2" id="pm-load-level-group">
                                <button type="button" class="btn btn-sm rounded-pill px-3 pm-load-level-btn" data-level="PM1">PM1</button>
                                <button type="button" class="btn btn-sm rounded-pill px-3 pm-load-level-btn" data-level="PM2">PM2</button>
                                <button type="button" class="btn btn-sm rounded-pill px-3 pm-load-level-btn" data-level="PM3">PM3</button>
                                <button type="button" class="btn btn-sm rounded-pill px-3 pm-load-level-btn" data-level="PM4">PM4</button>
                            </div>
                        </div>
                        <div id="pm-load-preview" class="small text-muted mt-3" style="display:none;"></div>
                        <div class="small text-muted mt-3">
                            <i class="mdi mdi-information-outline me-1"></i>Baris <strong>Transportation</strong> akan otomatis ditambahkan — Tipe & Kota tinggal dipilih setelah item masuk ke quotation.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn btn-primary" id="btn-pm-load-confirm" disabled>
                            <i class="mdi mdi-plus me-1"></i> Tambahkan ke Quotation
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- NOTE + TERMS & CONDITIONS (shared, gak per-opsi) --}}
        <div class="card mb-4 border-0 shadow-sm">
            <div class="card-body">
                <div class="row g-4">
                    <div class="col-lg-10 col-xl-9 mx-auto">
                        <div class="row g-4">
                        {{-- Note --}}
                        <div class="col-md-6 mb-4 mb-md-0">
                            <h6 class="fw-bold mb-2 text-dark">
                                <i class="mdi mdi-notebook-edit-outline me-1 text-primary"></i> Note / Quotation Remarks
                            </h6>
                            <textarea class="form-control" name="note" id="note"
                                rows="3" placeholder="• Write your quotation note/remarks here..."
                                style="overflow-y: hidden; resize: none;">{{ old('note') }}</textarea>
                            <div class="form-text text-muted mt-1"><i class="mdi mdi-information-outline me-1"></i>Tekan <kbd>Enter</kbd> untuk baris baru otomatis ber-bullet.</div>
                        </div>

                        {{-- Terms & Conditions --}}
                        <div class="col-md-6">
                            <h6 class="fw-bold mb-3 text-dark">
                                <i class="mdi mdi-shield-check-outline me-1 text-primary"></i> Terms & Conditions
                            </h6>
                            <div class="row mb-3 align-items-center">
                                <label class="col-sm-4 col-form-label text-muted small fw-semibold" for="validity">Validity of Quotation</label>
                                <div class="col-sm-8">
                                    <input type="text" id="validity" class="form-control form-control-sm" name="validity"
                                        value="{{ old('validity', '1(one) Month After this Quotation Created') }}">
                                </div>
                            </div>
                            <div class="row mb-3 align-items-center">
                                <label class="col-sm-4 col-form-label text-muted small fw-semibold" for="pricing">Price</label>
                                <div class="col-sm-8">
                                    <input type="text" id="pricing" class="form-control form-control-sm" name="pricing"
                                        value="{{ old('pricing', 'Franco Factory') }}">
                                </div>
                            </div>
                            <div class="row mb-3 align-items-center">
                                <label class="col-sm-4 col-form-label text-muted small fw-semibold" for="payment-select">Payment</label>
                                <div class="col-sm-8">
                                    <select class="form-select form-select-sm" id="payment-select">
                                        @if(isset($paymentTemplates) && count($paymentTemplates) > 0)
                                            <optgroup label="Template Sales">
                                                @foreach($paymentTemplates as $pt)
                                                    <option value="{{ $pt->payment_term }}" {{ $pt->is_default ? 'selected' : '' }}>
                                                        {{ $pt->name }} {{ $pt->client ? '('.$pt->client->company.')' : '' }}
                                                    </option>
                                                @endforeach
                                            </optgroup>
                                        @endif
                                        <optgroup label="Standar System">
                                            <option value="Cash Before Delivery">Cash Before Delivery</option>
                                            <option value="DP 50% & BP 50%">DP 50% & BP 50%</option>
                                            <option value="DP 30% & BP 70%">DP 30% & BP 70%</option>
                                            <option value="14 Days after invoice release">14 Days after invoice release</option>
                                            <option value="30 Days after invoice release">30 Days after invoice release</option>
                                        </optgroup>
                                        <option value="manual">-- Custom (Isi Sendiri) --</option>
                                    </select>
                                    <input type="hidden" name="payment" id="input-payment-hidden" value="{{ old('payment', 'Cash Before Delivery') }}">
                                </div>
                            </div>
                            <div class="row mb-3 align-items-center" id="manual-payment-wrapper" style="display: none;">
                                <div class="col-sm-8 offset-sm-4">
                                    <input type="text" class="form-control form-control-sm" id="input-payment-manual" placeholder="Ketik custom payment term...">
                                </div>
                            </div>
                            <div class="row mb-3 align-items-center" id="warranty-wrapper">
                                <label class="col-sm-4 col-form-label text-muted small fw-semibold" for="warranty">Warranty</label>
                                <div class="col-sm-8">
                                    <input type="text" id="warranty" class="form-control form-control-sm" name="warranty"
                                        value="{{ old('warranty', '-') }}">
                                </div>
                            </div>
                            <div class="row mb-3 align-items-center">
                                <label class="col-sm-4 col-form-label text-muted small fw-semibold" for="delivery">Delivery Process</label>
                                <div class="col-sm-8">
                                    <textarea id="delivery" class="form-control form-control-sm" name="delivery_process" rows="1" style="resize: vertical;">{{ old('delivery_process', 'Ready stock') }}</textarea>
                                </div>
                            </div>
                        </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mb-4">
            <a href="{{ route('unit-quotation.index') }}" class="btn btn-label-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary shadow-sm px-4">
                <i class="mdi mdi-content-save me-1"></i> Save Quotation
            </button>
        </div>
    </form>

    <!-- Floating Fixed Bottom Summary Bar (Blue Concept) -->
    <div class="sticky-summary-bar p-3 no-print">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div class="d-flex align-items-center gap-3 flex-wrap">
                <div>
                    <span class="text-white-50 small d-block" style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;">
                        Total Penawaran (<span id="bubble-active-option-name" class="text-white fw-bold">Opsi 1</span>)
                    </span>
                    <span class="fw-bold fs-4 text-warning" id="bubble-grand-total">Rp 0</span>
                </div>
                <div class="border-start border-white-50 ps-3 d-none d-md-block" style="font-size: 11.5px;">
                    <span class="text-white-50">Subtotal: <strong class="text-white" id="bubble-subtotal">Rp 0</strong></span>
                    <span class="mx-2 text-white-50">&bull;</span>
                    <span class="text-white-50">PPN: <strong class="text-white" id="bubble-tax">Rp 0</strong></span>
                </div>
            </div>

            <div class="d-flex align-items-center gap-2">
                <span class="text-white-50 small d-none d-sm-inline-flex align-items-center me-1" id="smartQuoteLastSavedTime" style="font-size: 11.5px;">
                    <i class="mdi mdi-cloud-check-outline me-1 text-white"></i> AutoSave Aktif
                </span>
                <button type="button" class="btn btn-sm btn-outline-light" onclick="resetSmartQuoteDraft()" title="Reset dan hapus draft tersimpan">
                    <i class="mdi mdi-refresh me-1"></i> Reset
                </button>
                <button type="submit" form="form-unit-quotation" class="btn btn-light text-primary btn-sm px-4 fw-bold shadow-sm">
                    <i class="mdi mdi-content-save me-1"></i> Save Quotation
                </button>
            </div>
        </div>
    </div>
    {{-- TEMPLATES (hidden, cloned by JS) --}}
    <template id="tmpl-unit-row">
        <div class="unit-row border-bottom p-3" data-type="unit">
            <input type="hidden" name="items[__IDX__][type]" value="unit">
            <input type="hidden" name="items[__IDX__][id_unit]" class="field-id-unit">
            <input type="hidden" name="items[__IDX__][id_fixed_asset]" class="field-id-fixed-asset">
            <input type="hidden" name="items[__IDX__][id_equivalent]" class="field-id-equivalent">
            <input type="hidden" name="items[__IDX__][spec_visible]" class="field-spec-visible">

            <div class="d-flex align-items-center mb-2">
                <div class="btn-drag-handle text-muted me-2" title="Geser (drag & drop) untuk memindahkan posisi">
                    <i class="mdi mdi-drag-vertical fs-4"></i>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input unit-source-radio" type="radio" name="unit_source___IDX__"
                        value="sparepart" checked>
                    <label class="form-check-label small">Spare Part</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input unit-source-radio" type="radio" name="unit_source___IDX__"
                        value="catalog">
                    <label class="form-check-label small">Catalog Unit</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input unit-source-radio" type="radio" name="unit_source___IDX__"
                        value="fixed_asset">
                    <label class="form-check-label small">Unit Second</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input unit-source-radio" type="radio" name="unit_source___IDX__"
                        value="rental">
                    <label class="form-check-label small">Rental</label>
                </div>
            </div>

            <div class="row g-2 align-items-start">
                <div class="col-md-4">
                    <div class="unit-source-catalog" style="display:none;">
                        <select class="select2-unit-search form-select form-select-sm" style="width:100%">
                            <option value="">Search unit (SKU / Brand / Model)...</option>
                        </select>
                    </div>
                    <div class="unit-source-fixed-asset" style="display:none;">
                        <select class="select2-fixed-asset-search form-select form-select-sm" style="width:100%">
                            <option value="">Search Unit Second (SKU / Brand / Serial Number)...</option>
                        </select>
                    </div>
                    <div class="unit-source-equivalent">
                        <select class="select2-equivalent-search form-select form-select-sm" style="width:100%">
                            <option value="">Search Spare Part / Equivalent (PN / Brand / Name)...</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-1">
                    <input type="number" class="form-control form-control-sm text-center field-qty"
                        name="items[__IDX__][qty]" value="1" min="1" placeholder="Qty">
                </div>
                <div class="col-md-1">
                    <input type="text" class="form-control form-control-sm text-center field-info-qty"
                        name="items[__IDX__][info_qty]" value="Unit" readonly>
                </div>
                <div class="col-md-2">
                    <input type="text" class="form-control form-control-sm text-end field-price rupiah-input"
                        name="items[__IDX__][price]" placeholder="Price" autocomplete="off">
                </div>
                <div class="col-md-1">
                    <div class="input-group input-group-sm">
                        <input type="number" class="form-control text-center field-disc"
                            name="items[__IDX__][disc]" value="0" min="0" max="100" placeholder="Disc">
                        <span class="input-group-text">%</span>
                    </div>
                </div>
                <div class="col-md-2 text-end">
                    <span class="field-amount fw-semibold text-primary">Rp 0</span>
                </div>
                <div class="col-md-1 text-end">
                    <div class="d-inline-flex align-items-center gap-1">
                        <div class="btn-group btn-group-sm" role="group">
                            <button type="button" class="btn btn-xs btn-outline-secondary btn-move-up" title="Geser ke atas"><i class="mdi mdi-arrow-up"></i></button>
                            <button type="button" class="btn btn-xs btn-outline-secondary btn-move-down" title="Geser ke bawah"><i class="mdi mdi-arrow-down"></i></button>
                        </div>
                        <button type="button" class="btn btn-sm btn-icon btn-label-danger btn-remove-row" title="Hapus Baris">
                            <i class="mdi mdi-delete-outline"></i>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Custom Title --}}
            <div class="mt-2">
                <input type="text" class="form-control form-control-sm field-label"
                    name="items[__IDX__][label]"
                    placeholder="Item title (auto-filled, editable)">
            </div>

            {{-- Spec Preview --}}
            <div class="spec-preview mt-2 ms-1 ps-3 border-start border-2" style="display:none;">
                <div class="spec-rows"></div>
                <p class="text-muted small mb-0 mt-1">
                    <i class="mdi mdi-information-outline me-1"></i>
                    Click <kbd>×</kbd> on a spec to hide it from the quotation.
                </p>
            </div>

            {{-- Spare Part Stock Preview --}}
            <div class="equivalent-stock-preview mt-2" style="display:none;">
                <span class="badge bg-label-info me-1">BDG Stock: <span class="stock-bdg">0</span></span>
                <span class="badge bg-label-info me-1">BKS Stock: <span class="stock-bks">0</span></span>
                <span class="badge bg-label-warning">Pending Stock: <span class="stock-pending">0</span></span>
            </div>
        </div>
    </template>

    <template id="tmpl-custom-row">
        <div class="unit-row border-bottom p-3" data-type="custom">
            <input type="hidden" name="items[__IDX__][type]" value="custom">

            <div class="row g-3 align-items-start">
                {{-- Left: Drag Handle + Item Title & Description (30%) --}}
                <div class="col-md-4 d-flex align-items-start" style="flex: 0 0 30%; max-width: 30%;">
                    <div class="btn-drag-handle text-muted me-2 mt-1" title="Geser (drag & drop) untuk memindahkan posisi">
                        <i class="mdi mdi-drag-vertical fs-4"></i>
                    </div>
                    <div class="w-100">
                        <div class="mb-2">
                            <input type="text" class="form-control form-control-sm fw-bold field-label"
                                name="items[__IDX__][label]" placeholder="Item Title *" required>
                        </div>
                        <div>
                            <textarea class="form-control form-control-sm field-description"
                                name="items[__IDX__][description]" rows="2" placeholder="Description (optional)"></textarea>
                        </div>
                    </div>
                </div>

                {{-- Right: Qty, Price, Disc, Nominal, Move Up/Down, Remove (70%) --}}
                <div class="col-md-8" style="flex: 0 0 70%; max-width: 70%;">
                    <div class="row g-2 align-items-center h-100 pt-1">
                        <div class="col-md-3">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">Qty</span>
                                <input type="number" class="form-control text-center field-qty"
                                    name="items[__IDX__][qty]" value="1" min="1">
                                <select class="form-select field-info-qty" name="items[__IDX__][info_qty]" style="max-width:70px;">
                                    <option value="Lot">Lot</option>
                                    <option value="Set">Set</option>
                                    <option value="Unit">Unit</option>
                                    <option value="Pcs">Pcs</option>
                                    <option value="Ls">Ls</option>
                                    <option value="Btg">Btg</option>
                                    <option value="Mtr">Mtr</option>
                                    <option value="Days">Days</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">Price</span>
                                <input type="text" class="form-control form-control-sm text-end field-price rupiah-input"
                                    name="items[__IDX__][price]" placeholder="Price" autocomplete="off">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">Disc</span>
                                <input type="number" class="form-control text-center field-disc"
                                    name="items[__IDX__][disc]" value="0" min="0" max="100">
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                        <div class="col-md-3 text-end">
                            <span class="text-muted small me-1">Nominal:</span>
                            <span class="field-amount fw-semibold text-primary">Rp 0</span>
                        </div>
                        <div class="col-md-1 text-end">
                            <div class="d-inline-flex align-items-center gap-1">
                                <div class="btn-group btn-group-sm" role="group">
                                    <button type="button" class="btn btn-xs btn-outline-secondary btn-move-up" title="Geser ke atas"><i class="mdi mdi-arrow-up"></i></button>
                                    <button type="button" class="btn btn-xs btn-outline-secondary btn-move-down" title="Geser ke bawah"><i class="mdi mdi-arrow-down"></i></button>
                                </div>
                                <button type="button" class="btn btn-sm btn-icon btn-label-danger btn-remove-row" title="Hapus Baris">
                                    <i class="mdi mdi-delete-outline"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </template>

    <template id="tmpl-header-row">
        <div class="unit-row border-bottom p-3 bg-light" data-type="header">
            <input type="hidden" name="items[__IDX__][type]" value="header">
            <div class="d-flex align-items-center gap-2">
                <div class="btn-drag-handle text-muted" title="Geser (drag & drop) untuk memindahkan posisi">
                    <i class="mdi mdi-drag-vertical fs-4"></i>
                </div>
                <span class="badge bg-primary text-uppercase" style="font-size:10px;">Head Title</span>
                <div class="flex-grow-1">
                    <input type="text" class="form-control form-control-sm fw-bold text-primary field-label"
                        name="items[__IDX__][label]" placeholder="Head Title (e.g. A. SCOPE OF WORK, B. PIPING SYSTEM) *" required>
                </div>
                <span class="badge bg-label-primary section-subtotal-badge text-nowrap" style="font-size:11px;"></span>
                <div class="d-flex align-items-center gap-1">
                    <div class="btn-group btn-group-sm" role="group">
                        <button type="button" class="btn btn-xs btn-outline-secondary btn-move-up" title="Geser ke atas"><i class="mdi mdi-arrow-up"></i></button>
                        <button type="button" class="btn btn-xs btn-outline-secondary btn-move-down" title="Geser ke bawah"><i class="mdi mdi-arrow-down"></i></button>
                    </div>
                    <button type="button" class="btn btn-sm btn-icon btn-label-danger btn-remove-row" title="Hapus Baris">
                        <i class="mdi mdi-delete-outline"></i>
                    </button>
                </div>
            </div>
        </div>
    </template>

    {{-- Tab nav-item untuk 1 Opsi --}}
    <template id="tmpl-option-tab">
        <li class="nav-item" data-option-idx="__OPT__">
            <a class="nav-link" data-bs-toggle="pill" href="#option-pane-__OPT__" role="tab">
                <i class="mdi mdi-file-document-outline me-1"></i>
                <span class="tab-title-display">Opsi</span>
            </a>
        </li>
    </template>

    {{-- Pane untuk 1 Opsi: judul, line items sendiri, summary sendiri --}}
    <template id="tmpl-option-pane">
        <div class="option-pane tab-pane fade" id="option-pane-__OPT__" data-option-idx="__OPT__">
            <div class="p-3 border-bottom bg-light-subtle d-flex align-items-center gap-2 flex-wrap option-header-toolbar" style="display: none;">
                <div class="option-title-wrapper align-items-center gap-2" style="display: none;">
                    <label class="fw-semibold small text-muted mb-0">Judul Opsi:</label>
                    <input type="text" class="form-control form-control-sm fw-bold option-title-input" style="max-width:320px;"
                        name="options[__OPT__][title]" placeholder="Judul Opsi (mis. Unit Baru, Unit Second)">
                </div>
                <span class="badge bg-label-secondary items-count-badge">0 Items</span>
                <button type="button" class="btn btn-sm btn-outline-danger ms-auto btn-remove-option" style="display: none;">
                    <i class="mdi mdi-delete-outline me-1"></i> Hapus Opsi Ini
                </button>
            </div>

            <div class="line-items-container">
                {{-- rows injected by JS --}}
            </div>
            <div class="empty-state text-center text-muted py-5 my-2">
                <div class="avatar avatar-md bg-label-primary mx-auto mb-3" style="width: 54px; height: 54px;">
                    <i class="mdi mdi-package-variant-closed fs-3" style="line-height: 54px;"></i>
                </div>
                <h6 class="fw-bold mb-1">No Line Items Added Yet</h6>
                <p class="text-muted small mb-0">Click the buttons below to add Spare Parts/Units from catalog, Custom Items, or Head Titles.</p>
            </div>
            <div class="d-flex flex-wrap gap-2 p-3 border-top border-bottom bg-light-subtle">
                <button type="button" class="btn btn-sm btn-primary shadow-sm btn-add-unit">
                    <i class="mdi mdi-plus me-1"></i> Add Item
                </button>
                <button type="button" class="btn btn-sm btn-outline-secondary btn-add-custom">
                    <i class="mdi mdi-format-list-bulleted me-1"></i> Add Custom Item
                </button>
                <button type="button" class="btn btn-sm btn-outline-info btn-add-header">
                    <i class="mdi mdi-format-header-1 me-1"></i> Add Head Title
                </button>
                <button type="button" class="btn btn-sm btn-outline-primary btn-add-transport">
                    <i class="mdi mdi-truck-outline me-1"></i> Add Transport
                </button>
                <button type="button" class="btn btn-sm btn-outline-success ms-md-auto" data-bs-toggle="modal" data-bs-target="#modal-load-pm-template">
                    <i class="mdi mdi-file-document-multiple-outline me-1"></i> Load Template PM
                </button>
            </div>

            {{-- Summary (subtotal/diskon/tax/shipping/total) khusus opsi ini --}}
            <div class="p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted small">Subtotal</span>
                    <span class="fw-bold text-dark fs-6 display-subtotal">Rp 0</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted small">Discount</span>
                    <div style="width: 160px;">
                        <div class="input-group input-group-sm">
                            <select class="form-select flex-grow-0 select-diskon-type" name="options[__OPT__][diskon_type]" style="max-width:65px; font-size:11px;">
                                <option value="percent" selected>%</option>
                                <option value="amount">Rp</option>
                            </select>
                            <input type="text" class="form-control text-end fw-semibold input-diskon" name="options[__OPT__][diskon]" value="0" autocomplete="off">
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="d-flex align-items-center gap-2">
                        <span class="text-muted small">PPN 12%</span>
                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input toggle-tax" type="checkbox" name="options[__OPT__][tax]" value="1" checked>
                        </div>
                    </div>
                    <span class="display-tax fw-semibold text-muted small">Rp 0</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                    <div>
                        <span class="text-muted small d-block">Shipping Cost</span>
                        <span class="text-muted" style="font-size: 10px;">( Non-taxable )</span>
                    </div>
                    <div style="width: 160px;">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text fw-semibold" style="font-size:11px;">Rp</span>
                            <input type="text" class="form-control text-end fw-semibold rupiah-input input-shipping" name="options[__OPT__][shipping]" value="0" placeholder="0" autocomplete="off">
                        </div>
                    </div>
                </div>
                <div class="p-3 rounded-3 d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #f0f2ff 0%, #e8ebff 100%); border: 1px dashed #696cff;">
                    <div>
                        <div class="text-uppercase fw-bold text-primary" style="font-size: 10px; letter-spacing: 0.8px;">Total Amount</div>
                        <div class="text-muted" style="font-size: 10px;">( Inclusive of Tax &amp; Discount )</div>
                    </div>
                    <div class="fw-bolder text-primary fs-3 display-total" style="letter-spacing: -0.5px;">Rp 0</div>
                </div>
            </div>
        </div>
    </template>
@endsection

@push('after-style')
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/select2/select2.css" />
    <style>
        .bg-light-primary { background-color: #f0f0ff !important; border: 2px dashed #696cff !important; }
        .btn-drag-handle { cursor: grab; padding: 2px 4px; border-radius: 4px; transition: background 0.15s; }
        .btn-drag-handle:hover { background: #e8e8ff; color: #696cff !important; }
        .btn-drag-handle:active { cursor: grabbing; }

        /* Floating Fixed Bottom Summary Bar - Blue Concept */
        .sticky-summary-bar {
            position: fixed;
            bottom: 20px;
            left: calc(16.25rem + 1.5rem);
            right: 1.5rem;
            z-index: 1040;
            background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);
            border: 1px solid rgba(255, 255, 255, 0.25);
            box-shadow: 0 10px 25px -5px rgba(37, 99, 235, 0.4), 0 8px 10px -6px rgba(30, 58, 138, 0.3);
            border-radius: 12px;
            color: #fff;
            transition: left 0.3s ease, right 0.3s ease;
        }
        .layout-menu-collapsed .sticky-summary-bar {
            left: calc(4.5rem + 1.5rem);
        }
        @media (max-width: 1199.98px) {
            .sticky-summary-bar {
                left: 1rem;
                right: 1rem;
                bottom: 12px;
            }
        }
        #form-unit-quotation {
            padding-bottom: 90px;
        }
        #chat-bubble-container, .chat-bubble-container, #chat-widget {
            display: none !important;
        }
        /* Sembunyikan toolbar header opsi (badge items, judul opsi, tombol hapus) jika opsi cuma 1 */
        .tab-content:not(.has-multi-options) .option-header-toolbar {
            display: none !important;
        }
    </style>
@endpush

@push('after-script')
    <script src="{{ asset('assets') }}/vendor/libs/select2/select2.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/sortablejs/sortable.js"></script>

    <script>
        const SMART_QUOTE_DRAFT_KEY = 'smart_quote_create_draft';
        let savedDraftData = null;
        try {
            const rawDraft = localStorage.getItem(SMART_QUOTE_DRAFT_KEY);
            if (rawDraft) {
                savedDraftData = JSON.parse(rawDraft);
            }
        } catch(e) { console.error('Draft load error', e); }

        if (savedDraftData && savedDraftData.options && savedDraftData.options.length > 0) {
            window.EDIT_OPTIONS = savedDraftData.options;
            if (savedDraftData.id_client) window.EDIT_CLIENT_ID = savedDraftData.id_client;
            if (savedDraftData.id_pic) window.EDIT_PIC_ID = savedDraftData.id_pic;
            if (savedDraftData.id_plant) window.EDIT_PLANT_ID = savedDraftData.id_plant;
            if (savedDraftData.address_hidden || savedDraftData.address_manual) {
                window.EDIT_ADDRESS = savedDraftData.address_hidden || savedDraftData.address_manual;
            }
            if (savedDraftData.payment_hidden || savedDraftData.payment_manual) {
                window.EDIT_PAYMENT = savedDraftData.payment_hidden || savedDraftData.payment_manual;
            }
        } else {
            @if(old('payment'))
                window.EDIT_PAYMENT = @json(old('payment'));
            @endif
            @if(isset($selectedPic) && $selectedPic)
                window.EDIT_PIC_ID = @json($selectedPic);
            @endif
        }
        window.TRANSPORT_PRICES = @json($transportationPrices);
    </script>
    <script src="{{ asset('assets') }}/includes/form-unit-quotation.js?v={{ filemtime(public_path('assets/includes/form-unit-quotation.js')) }}"></script>
    
    <script>
        $(document).ready(function() {
            if (savedDraftData && savedDraftData.id_client) {
                setTimeout(function() {
                    $('#client-select').val(savedDraftData.id_client).trigger('change');
                }, 150);
            } else if (@json(isset($selectedClient) && $selectedClient)) {
                setTimeout(function() {
                    $('#client-select').val(@json($selectedClient ?? '')).trigger('change');
                }, 150);
            }
        });
    </script>
@endpush

@push('page-script')
    <script>
        // ── AutoSave & Draft Management Engine ──
        let autoSaveTimer = null;

        function triggerAutoSaveSmartQuote() {
            clearTimeout(autoSaveTimer);
            autoSaveTimer = setTimeout(function() {
                saveSmartQuoteDraft();
            }, 500);
        }

        function saveSmartQuoteDraft() {
            try {
                const draft = {
                    id_sales: $('#sales-select').val() || '',
                    id_client: $('#client-select').val() || '',
                    id_pic: $('#pic-select').val() || '',
                    id_plant: $('#input-id-plant').val() || '',
                    address_select: $('#address-select').val() || '',
                    address_manual: $('#input-address-manual').val() || '',
                    address_hidden: $('#input-address-hidden').val() || '',
                    date: $('#input-date').val() || '',
                    expired_date: $('#input-expired-date').val() || '',
                    no_pr: $('input[name="no_pr"]').val() || '',
                    title: $('input[name="title"]').val() || '',
                    type: $('#select-type').val() || 'Project',
                    unit_condition: $('#select-unit-condition').val() || '',
                    week: $('#select-week').val() || '',
                    note: $('#note').val() || '',
                    validity: $('#validity').val() || '',
                    pricing: $('#pricing').val() || '',
                    payment_select: $('#payment-select').val() || '',
                    payment_manual: $('#input-payment-manual').val() || '',
                    payment_hidden: $('#input-payment-hidden').val() || '',
                    warranty: $('#warranty').val() || '',
                    delivery_process: $('#delivery').val() || '',
                    options: []
                };

                $('.option-pane').each(function() {
                    const $pane = $(this);
                    const optIdx = $pane.data('option-idx');
                    const optTitle = $pane.find('.option-title-input').val() || '';
                    const diskonType = $pane.find('.select-diskon-type').val() || 'percent';
                    const diskon = $pane.find('.input-diskon').val() || '0';
                    const tax = $pane.find('.toggle-tax').is(':checked') ? 1 : 0;
                    const shipping = $pane.find('.input-shipping').val() || '0';

                    const optData = {
                        title: optTitle,
                        diskon_type: diskonType,
                        diskon: diskonType === 'amount' ? (parseFloat(diskon.replace(/\D/g, '')) || 0) : (parseFloat(diskon) || 0),
                        tax: tax,
                        shipping: parseFloat(shipping.replace(/\D/g, '')) || 0,
                        items: []
                    };

                    $pane.find('.line-items-container .unit-row').each(function() {
                        const $row = $(this);
                        const rowType = $row.attr('data-type') || 'custom';
                        const item = {
                            type: rowType,
                            id_unit: $row.find('.field-id-unit').val() || '',
                            id_fixed_asset: $row.find('.field-id-fixed-asset').val() || '',
                            id_equivalent: $row.find('.field-id-equivalent').val() || '',
                            spec_visible: $row.find('.field-spec-visible').val() || '',
                            label: $row.find('.field-label').val() || '',
                            description: $row.find('.field-description').val() || '',
                            qty: parseFloat($row.find('.field-qty').val()) || 1,
                            info_qty: $row.find('.field-info-qty').val() || 'Unit',
                            price: parseFloat(String($row.find('.field-price').val()).replace(/\D/g, '')) || 0,
                            disc: parseFloat($row.find('.field-disc').val()) || 0,
                        };

                        if (rowType === 'transport') {
                            item.label = $row.find('.field-transport-type').val() || item.label;
                        }

                        optData.items.push(item);
                    });

                    draft.options.push(optData);
                });

                localStorage.setItem(SMART_QUOTE_DRAFT_KEY, JSON.stringify(draft));

                const now = new Date();
                const timeStr = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
                $('#smartQuoteLastSavedTime').html('<i class="mdi mdi-cloud-check-outline me-1 text-white"></i> Autosaved pk ' + timeStr);
            } catch(e) {
                console.error('Failed to autosave smart quote draft', e);
            }
        }

        function resetSmartQuoteDraft() {
            if (confirm('Yakin ingin mereset draft penawaran ini dan mengulang dari awal?')) {
                localStorage.removeItem(SMART_QUOTE_DRAFT_KEY);
                window.location.reload();
            }
        }

        // Restore scalar fields if draft exists
        $(document).ready(function() {
            if (savedDraftData) {
                if (savedDraftData.date) $('#input-date').val(savedDraftData.date);
                if (savedDraftData.expired_date) $('#input-expired-date').val(savedDraftData.expired_date);
                if (savedDraftData.no_pr) $('input[name="no_pr"]').val(savedDraftData.no_pr);
                if (savedDraftData.title) $('input[name="title"]').val(savedDraftData.title);
                if (savedDraftData.type) $('#select-type').val(savedDraftData.type).trigger('change');
                if (savedDraftData.unit_condition) $('#select-unit-condition').val(savedDraftData.unit_condition);
                if (savedDraftData.week) $('#select-week').val(savedDraftData.week);
                if (savedDraftData.note) $('#note').val(savedDraftData.note);
                if (savedDraftData.validity) $('#validity').val(savedDraftData.validity);
                if (savedDraftData.pricing) $('#pricing').val(savedDraftData.pricing);
                if (savedDraftData.warranty) $('#warranty').val(savedDraftData.warranty);
                if (savedDraftData.delivery_process) $('#delivery').val(savedDraftData.delivery_process);

                $('#smartQuoteLastSavedTime').html('<i class="mdi mdi-cloud-check-outline me-1 text-white"></i> Draft dipulihkan');
            }

            // Bind input changes for auto-saving
            $('#form-unit-quotation').on('input change', 'input, select, textarea', function() {
                triggerAutoSaveSmartQuote();
            });

            // Clear draft upon successful form submission
            $('#form-unit-quotation').on('submit', function() {
                localStorage.removeItem(SMART_QUOTE_DRAFT_KEY);
            });
        });

        (function () {
            const inputDate    = document.getElementById('input-date');
            const expiredDate  = document.getElementById('input-expired-date');

            function addOneMonth(dateStr) {
                if (!dateStr) return '';
                const d = new Date(dateStr);
                const day = d.getDate();
                d.setMonth(d.getMonth() + 1);
                // Handle end-of-month overflow (e.g. Jan 31 → Feb 28)
                if (d.getDate() !== day) d.setDate(0);
                return d.toISOString().slice(0, 10);
            }

            // Auto-update expired date whenever date changes
            inputDate.addEventListener('change', function () {
                expiredDate.value = addOneMonth(this.value);
            });
        })();

        // ── Auto-bullet on Note textarea ──
        (function () {
            const BULLET = '\u2022 ';
            const ta = document.getElementById('note');
            if (!ta) return;

            // When user first focuses & textarea is empty, pre-fill bullet
            ta.addEventListener('focus', function () {
                if (this.value.trim() === '') {
                    this.value = BULLET;
                    this.setSelectionRange(BULLET.length, BULLET.length);
                }
            });

            ta.addEventListener('keydown', function (e) {
                if (e.key !== 'Enter') return;
                e.preventDefault();

                const start = this.selectionStart;
                const end   = this.selectionEnd;
                const val   = this.value;

                // Find the current line
                const lineStart = val.lastIndexOf('\n', start - 1) + 1;
                const currentLine = val.substring(lineStart, start);

                // If current line is only a bullet (empty item), remove bullet & exit list
                if (currentLine === BULLET || currentLine === '\u2022') {
                    this.value = val.substring(0, lineStart) + val.substring(end);
                    this.setSelectionRange(lineStart, lineStart);
                    return;
                }

                // Otherwise insert newline + bullet
                const insert = '\n' + BULLET;
                this.value = val.substring(0, start) + insert + val.substring(end);
                const newPos = start + insert.length;
                this.setSelectionRange(newPos, newPos);
            });

            // Ensure first line starts with bullet on blur if not empty
            ta.addEventListener('blur', function () {
                if (this.value && !this.value.startsWith(BULLET)) {
                    this.value = BULLET + this.value;
                }
            });

            // Auto-resize height to fit content
            function autoResize() {
                ta.style.height = 'auto';
                ta.style.height = ta.scrollHeight + 'px';
            }
            ta.addEventListener('input', autoResize);
            ta.addEventListener('keydown', function () {
                setTimeout(autoResize, 0);
            });
            autoResize();
        })();
    </script>
@endpush
