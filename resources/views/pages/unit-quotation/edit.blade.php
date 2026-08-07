@extends('layouts.sales.app')
@section('title', 'Edit Unit Quotation')
@section('content')
    {{-- Hero Page Header & Top Bar --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center py-3 mb-3 gap-3">
        <div>
            <h4 class="fw-bold mb-1">
                <span class="text-muted fw-light">Sales / <a href="{{ route('unit-quotation.show', $quote->id) }}" class="text-muted">Unit Quotation</a> /</span> Edit
            </h4>
            <p class="text-muted mb-0 small"><i class="mdi mdi-file-document-edit-outline me-1"></i> Edit quotation document {{ $quote->no_quote }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('unit-quotation.show', $quote->id) }}" class="btn btn-label-secondary">
                <i class="mdi mdi-arrow-left me-1"></i> Back
            </a>
            <button type="submit" form="form-unit-quotation" class="btn btn-primary shadow-sm">
                <i class="mdi mdi-content-save me-1"></i> Update Quotation
            </button>
        </div>
    </div>

    <form action="{{ route('unit-quotation.update', $quote->id) }}" method="POST" id="form-unit-quotation">
        @csrf
        @method('PUT')

        {{-- Hero Quotation Header Card --}}
        <div class="card mb-4 border-0 shadow-sm" style="background: linear-gradient(135deg, #f8f9ff 0%, #f0f2ff 100%); border-left: 5px solid #696cff !important;">
            <div class="card-body py-3">
                <div class="row align-items-center g-3">
                    <div class="col-md-8 col-12">
                        <label class="form-label text-uppercase fw-bold text-primary small mb-1" style="letter-spacing: .5px;">
                            <i class="mdi mdi-pound me-1"></i> Quotation Number
                        </label>
                        <input type="text" class="form-control form-control-lg fw-bold bg-white text-primary border-primary-subtle shadow-sm"
                            name="no_quote" placeholder="Quotation Number" value="{{ old('no_quote', $quote->no_quote) }}" style="font-size: 1.35rem;">
                    </div>
                    <div class="col-md-4 col-12 text-md-end">
                        <span class="badge bg-label-info px-3 py-2 fs-6 rounded-pill">
                            <i class="mdi mdi-file-document-edit-outline me-1"></i> STATUS: EDIT MODE
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
                    <div class="col-md-4">
                        <div class="form-floating form-floating-outline">
                            <select class="select2 form-select" name="id_client" id="client-select">
                                <option value="">-- Select Client --</option>
                                @foreach ($clients as $c)
                                    <option value="{{ $c->id }}"
                                        data-role="{{ $c->role }}"
                                        {{ $quote->id_client == $c->id ? 'selected' : '' }}>
                                        {{ $c->company }}
                                    </option>
                                @endforeach
                            </select>
                            <label>Client *</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating form-floating-outline">
                            <select class="form-select" name="id_pic" id="pic-select">
                                <option value="">-- Select PIC --</option>
                                @if ($quote->pic)
                                    <option value="{{ $quote->pic->id }}" selected>
                                        {{ $quote->pic->name_pic }}{{ $quote->pic->position ? ' (' . $quote->pic->position . ')' : '' }}
                                    </option>
                                @endif
                            </select>
                            <label>PIC / Contact *</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating form-floating-outline">
                            <select class="form-select" id="address-select">
                                <option value="">-- Select Address --</option>
                            </select>
                            <label>Address / Plant</label>
                        </div>
                        <input type="hidden" name="id_plant" id="input-id-plant" value="{{ $quote->id_plant }}">
                        <input type="hidden" name="address" id="input-address-hidden" value="{{ $quote->address }}">
                    </div>
                    <div class="col-md-12" id="manual-address-wrapper" style="display: none;">
                        <div class="form-floating form-floating-outline">
                            <textarea class="form-control" id="input-address-manual" rows="2" style="height: 60px;" placeholder="Enter custom address...">{{ $quote->address }}</textarea>
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
                            <input type="date" class="form-control" id="input-date" name="date"
                                value="{{ old('date', $quote->date?->format('Y-m-d')) }}">
                            <label>Date</label>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-floating form-floating-outline">
                            <input type="date" class="form-control bg-light-subtle" id="input-expired-date" name="expired_date"
                                value="{{ old('expired_date', $quote->expired_date ? \Carbon\Carbon::parse($quote->expired_date)->format('Y-m-d') : \Carbon\Carbon::parse($quote->date)->addMonth()->format('Y-m-d')) }}"
                                readonly title="Auto-calculated: 1 month from quotation date">
                            <label class="text-muted">Expired Quotation</label>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-floating form-floating-outline">
                            <input type="text" class="form-control" name="no_pr" placeholder="No PR (optional)"
                                value="{{ old('no_pr', $quote->no_pr) }}">
                            <label>No PR <span class="text-muted small">(optional)</span></label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating form-floating-outline">
                            <input type="text" class="form-control" name="title" placeholder="Title"
                                value="{{ old('title', $quote->title) }}">
                            <label>Title / Description</label>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-floating form-floating-outline">
                            <select class="form-select" id="select-type" name="type">
                                <option value="" disabled>-- Type --</option>
                                @foreach (['Unit', 'Rental', 'Project', 'Parts', 'Service', 'Piping', 'Air Audit'] as $t)
                                    <option value="{{ $t }}" {{ $quote->type === $t ? 'selected' : '' }}>{{ $t }}</option>
                                @endforeach
                            </select>
                            <label>Type</label>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-floating form-floating-outline">
                            <select class="form-select" id="select-week" name="week">
                                <option value="" disabled>-- Week --</option>
                                @foreach ([1,2,3,4,5] as $w)
                                    <option value="{{ $w }}" {{ $quote->week == $w ? 'selected' : '' }}>Week {{ $w }}</option>
                                @endforeach
                            </select>
                            <label>Week</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- LINE ITEMS --}}
        <div class="card mb-4 border-0 shadow-sm">
            <div class="card-header bg-transparent border-bottom py-3 d-flex align-items-center justify-content-between">
                <h6 class="card-title mb-0 fw-bold text-dark">
                    <i class="mdi mdi-cube-outline me-2 text-primary fs-5"></i> Quotation Line Items
                </h6>
                <span class="badge bg-label-secondary" id="items-count-badge">0 Items</span>
            </div>
            <div class="card-body p-0">
                <div id="line-items-container">
                    {{-- rows injected by JS --}}
                </div>
                <div id="empty-state" class="text-center text-muted py-5 my-2">
                    <div class="avatar avatar-md bg-label-primary mx-auto mb-3" style="width: 54px; height: 54px;">
                        <i class="mdi mdi-package-variant-closed fs-3" style="line-height: 54px;"></i>
                    </div>
                    <h6 class="fw-bold mb-1">No Line Items Added Yet</h6>
                    <p class="text-muted small mb-0">Click the buttons below to add Spare Parts/Units from catalog, Custom Items, or Head Titles.</p>
                </div>
                <div class="d-flex flex-wrap gap-2 p-3 border-top bg-light-subtle">
                    <button type="button" class="btn btn-sm btn-primary shadow-sm" id="btn-add-unit">
                        <i class="mdi mdi-plus me-1"></i> Add Item
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="btn-add-custom">
                        <i class="mdi mdi-format-list-bulleted me-1"></i> Add Custom Item
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-info" id="btn-add-header">
                        <i class="mdi mdi-format-header-1 me-1"></i> Add Head Title
                    </button>
                </div>
            </div>
        </div>

        {{-- SUMMARY + TERMS --}}
        <div class="card mb-4 border-0 shadow-sm">
            <div class="card-body">
                <div class="row g-4">
                    {{-- Note + Terms & Conditions (Kiri) --}}
                    <div class="col-lg-7">
                        {{-- Note --}}
                        <div class="mb-4 pb-3 border-bottom">
                            <h6 class="fw-bold mb-2 text-dark">
                                <i class="mdi mdi-notebook-edit-outline me-1 text-primary"></i> Note / Quotation Remarks
                            </h6>
                            <textarea class="form-control" name="note" id="note"
                                rows="3" placeholder="• Write your quotation note/remarks here..."
                                style="overflow-y: hidden; resize: none;">{{ old('note', $quote->note) }}</textarea>
                            <div class="form-text text-muted mt-1"><i class="mdi mdi-information-outline me-1"></i>Tekan <kbd>Enter</kbd> untuk baris baru otomatis ber-bullet.</div>
                        </div>

                        {{-- Terms & Conditions --}}
                        <div>
                            <h6 class="fw-bold mb-3 text-dark">
                                <i class="mdi mdi-shield-check-outline me-1 text-primary"></i> Terms & Conditions
                            </h6>
                            <div class="row mb-3 align-items-center">
                                <label class="col-sm-4 col-form-label text-muted small fw-semibold" for="validity">Validity of Quotation</label>
                                <div class="col-sm-8">
                                    <input type="text" id="validity" class="form-control form-control-sm" name="validity"
                                        value="{{ old('validity', $quote->validity) }}">
                                </div>
                            </div>
                            <div class="row mb-3 align-items-center">
                                <label class="col-sm-4 col-form-label text-muted small fw-semibold" for="pricing">Price</label>
                                <div class="col-sm-8">
                                    <input type="text" id="pricing" class="form-control form-control-sm" name="pricing"
                                        value="{{ old('pricing', $quote->pricing) }}">
                                </div>
                            </div>
                            <div class="row mb-3 align-items-center">
                                <label class="col-sm-4 col-form-label text-muted small fw-semibold" for="payment-select">Payment</label>
                                <div class="col-sm-8">
                                    <select class="form-select form-select-sm" id="payment-select">
                                        <option value="Cash Before Delivery">Cash Before Delivery</option>
                                        <option value="DP 50% & BP 50%">DP 50% & BP 50%</option>
                                        <option value="DP 30% & BP 70%">DP 30% & BP 70%</option>
                                        <option value="14 Days after invoice release">14 Days after invoice release</option>
                                        <option value="30 Days after invoice release">30 Days after invoice release</option>
                                        <option value="manual">-- Custom (Isi Sendiri) --</option>
                                    </select>
                                    <input type="hidden" name="payment" id="input-payment-hidden" value="{{ old('payment', $quote->payment) }}">
                                </div>
                            </div>
                            <div class="row mb-3 align-items-center" id="manual-payment-wrapper" style="display: none;">
                                <div class="col-sm-8 offset-sm-4">
                                    <input type="text" class="form-control form-control-sm" id="input-payment-manual" placeholder="Ketik custom payment term...">
                                </div>
                            </div>
                            <div class="row mb-3 align-items-center" id="warranty-wrapper" style="{{ empty(old('warranty', $quote->warranty)) ? 'display: none;' : '' }}">
                                <label class="col-sm-4 col-form-label text-muted small fw-semibold" for="warranty">Warranty</label>
                                <div class="col-sm-8">
                                    <input type="text" id="warranty" class="form-control form-control-sm" name="warranty"
                                        value="{{ old('warranty', $quote->warranty) }}">
                                </div>
                            </div>
                            <div class="row mb-3 align-items-center">
                                <label class="col-sm-4 col-form-label text-muted small fw-semibold" for="delivery">Delivery Process</label>
                                <div class="col-sm-8">
                                    <textarea id="delivery" class="form-control form-control-sm" name="delivery_process" rows="1" style="resize: vertical;">{{ old('delivery_process', $quote->delivery_process) }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Summary Card (Kanan) --}}
                    <div class="col-lg-5">
                        <div class="card border-0 shadow-sm overflow-hidden" style="background: #ffffff; border: 1px solid #e0e0ff !important; border-radius: 12px;">
                            {{-- Header --}}
                            <div class="card-header py-3 px-4 bg-light border-bottom d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-xs bg-label-primary rounded me-2 d-flex align-items-center justify-content-center" style="width:28px; height:28px;">
                                        <i class="mdi mdi-calculator text-primary fs-6"></i>
                                    </div>
                                    <h6 class="fw-bold mb-0 text-dark">Total Summary</h6>
                                </div>
                                <span class="badge bg-label-primary px-2 py-1" style="font-size:10px;">IDR SUMMARY</span>
                            </div>

                            <div class="card-body p-4">
                                {{-- Subtotal --}}
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="text-muted small">Subtotal</span>
                                    <span class="fw-bold text-dark fs-6" id="display-subtotal">Rp 0</span>
                                </div>

                                {{-- Discount --}}
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="text-muted small">Discount</span>
                                    <div style="width: 160px;">
                                        <div class="input-group input-group-sm">
                                            <select class="form-select flex-grow-0" id="select-diskon-type" name="diskon_type" style="max-width:65px; font-size:11px;">
                                                <option value="percent" {{ $quote->diskon_type === 'percent' ? 'selected' : '' }}>%</option>
                                                <option value="amount" {{ $quote->diskon_type === 'amount' ? 'selected' : '' }}>Rp</option>
                                            </select>
                                            <input type="text" class="form-control text-end fw-semibold" name="diskon" id="input-diskon" value="{{ old('diskon', $quote->diskon) }}" autocomplete="off">
                                        </div>
                                    </div>
                                </div>

                                {{-- PPN 12% --}}
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="text-muted small">PPN 12%</span>
                                        <div class="form-check form-switch mb-0">
                                            <input class="form-check-input" type="checkbox" name="tax" id="toggle-tax" value="1"
                                                {{ $quote->tax ? 'checked' : '' }}>
                                        </div>
                                    </div>
                                    <span id="display-tax" class="fw-semibold text-muted small">Rp 0</span>
                                </div>

                                {{-- Shipping Cost (Optional, Non-taxable) --}}
                                <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                                    <div>
                                        <span class="text-muted small d-block">Shipping Cost</span>
                                        <span class="text-muted" style="font-size: 10px;">( Non-taxable )</span>
                                    </div>
                                    <div style="width: 160px;">
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text fw-semibold" style="font-size:11px;">Rp</span>
                                            <input type="text" class="form-control text-end fw-semibold rupiah-input" name="shipping" id="input-shipping" value="{{ old('shipping', $quote->shipping > 0 ? number_format($quote->shipping, 0, '', '.') : '0') }}" placeholder="0" autocomplete="off">
                                        </div>
                                    </div>
                                </div>

                                {{-- Total Penawaran Hero Box --}}
                                <div class="p-3 rounded-3 d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #f0f2ff 0%, #e8ebff 100%); border: 1px dashed #696cff;">
                                    <div>
                                        <div class="text-uppercase fw-bold text-primary" style="font-size: 10px; letter-spacing: 0.8px;">Total Amount</div>
                                        <div class="text-muted" style="font-size: 10px;">( Inclusive of Tax &amp; Discount )</div>
                                    </div>
                                    <div class="fw-bolder text-primary fs-3" id="display-total" style="letter-spacing: -0.5px;">Rp 0</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mb-4">
            <a href="{{ route('unit-quotation.show', $quote->id) }}" class="btn btn-label-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary shadow-sm px-4">
                <i class="mdi mdi-content-save me-1"></i> Save Changes
            </button>
        </div>
    </form>

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
@endsection

@push('after-style')
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/select2/select2.css" />
    <style>
        .bg-light-primary { background-color: #f0f0ff !important; border: 2px dashed #696cff !important; }
        .btn-drag-handle { cursor: grab; padding: 2px 4px; border-radius: 4px; transition: background 0.15s; }
        .btn-drag-handle:hover { background: #e8e8ff; color: #696cff !important; }
        .btn-drag-handle:active { cursor: grabbing; }
    </style>
@endpush

@push('after-script')
    <script src="{{ asset('assets') }}/vendor/libs/select2/select2.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/sortablejs/sortable.js"></script>
@endpush

@push('page-script')
    <script>
        window.EDIT_ITEMS = @json($editItems);
        window.EDIT_CLIENT_ID = {{ $quote->id_client ?? 'null' }};
        window.EDIT_PIC_ID = {{ $quote->id_pic ?? 'null' }};
        window.EDIT_PLANT_ID = {{ $quote->id_plant ?? 'null' }};
        window.EDIT_ADDRESS = @json($quote->address ?? '');
        window.EDIT_PAYMENT = @json($quote->payment ?? '');
    </script>
    <script src="{{ asset('assets') }}/includes/form-unit-quotation.js"></script>
    <script>
        (function () {
            const inputDate   = document.getElementById('input-date');
            const expiredDate = document.getElementById('input-expired-date');

            function addOneMonth(dateStr) {
                if (!dateStr) return '';
                const d = new Date(dateStr);
                const day = d.getDate();
                d.setMonth(d.getMonth() + 1);
                if (d.getDate() !== day) d.setDate(0);
                return d.toISOString().slice(0, 10);
            }

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

            // Auto-resize height to fit content (including existing content on load)
            function autoResize() {
                ta.style.height = 'auto';
                ta.style.height = ta.scrollHeight + 'px';
            }
            ta.addEventListener('input', autoResize);
            ta.addEventListener('keydown', function () {
                setTimeout(autoResize, 0);
            });
            autoResize(); // run immediately to fit existing content
        })();
    </script>
@endpush
