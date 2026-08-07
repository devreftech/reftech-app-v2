@extends('layouts.sales.app')
@section('title', 'Create Unit Quotation')
@section('content')
    <div class="d-flex justify-content-between align-items-center py-3 mb-4">
        <h4 class="fw-bold mb-0">
            <span class="text-muted fw-light">Sales / <a href="{{ route('unit-quotation.index') }}">Unit Quotation</a> /</span> Create
        </h4>
    </div>

    <form action="{{ route('unit-quotation.store') }}" method="POST" id="form-unit-quotation">
        @csrf

        {{-- No. Quotation --}}
        <div class="form-floating mb-3">
            <input type="text" class="form-control fw-bold fs-3" name="no_quote"
                placeholder="Quotation Number"
                value="{{ old('no_quote', $defaultNoQuote) }}">
            <label>Quotation Number</label>
        </div>

        {{-- HEADER --}}
        <div class="card mb-4">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="form-floating form-floating-outline">
                            <select class="select2 form-select" name="id_client" id="client-select">
                                <option value="">-- Select Client --</option>
                                @foreach ($clients as $c)
                                    <option value="{{ $c->id }}" data-role="{{ $c->role }}">{{ $c->company }}</option>
                                @endforeach
                            </select>
                            <label>Client</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating form-floating-outline">
                            <select class="form-select" name="id_pic" id="pic-select" disabled>
                                <option value="">-- Select Client first --</option>
                            </select>
                            <label>PIC / Contact</label>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-floating form-floating-outline">
                            <input type="date" class="form-control" name="date" value="{{ date('Y-m-d') }}">
                            <label>Date</label>
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
                            <select class="form-select" name="type">
                                <option value="" disabled selected>-- Type --</option>
                                <option value="Unit">Unit</option>
                                <option value="Rental">Rental</option>
                                <option value="Project">Project</option>
                            </select>
                            <label>Type</label>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-floating form-floating-outline">
                            <select class="form-select" name="week">
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

        {{-- LINE ITEMS --}}
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center py-3">
                <h6 class="mb-0 fw-bold">Quotation Items</h6>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-sm btn-primary" id="btn-add-unit">
                        <i class="mdi mdi-plus me-1"></i> Add Unit
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="btn-add-custom">
                        <i class="mdi mdi-format-list-bulleted me-1"></i> Add Custom Item
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                <div id="line-items-container">
                    {{-- rows injected by JS --}}
                </div>
                <div id="empty-state" class="text-center text-muted py-5">
                    <i class="mdi mdi-package-variant-closed mdi-48px d-block mb-2"></i>
                    No items yet. Click "Add Unit" or "Add Custom Item".
                </div>
            </div>
        </div>

        {{-- SUMMARY + TERMS --}}
        <div class="card mb-4">
            <div class="card-body">
                <div class="row">
                    {{-- Terms & Conditions --}}
                    <div class="col-lg-6">
                        <h5 class="mb-4">Terms & Conditions</h5>
                        <div class="row mb-3">
                            <label class="col-sm-4 col-form-label" for="validity">Validity of Quotation</label>
                            <div class="col-sm-8">
                                <input type="text" id="validity" class="form-control" name="validity"
                                    value="{{ old('validity', '1(one) Month After this Quotation Created') }}">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-4 col-form-label" for="pricing">Price</label>
                            <div class="col-sm-8">
                                <input type="text" id="pricing" class="form-control" name="pricing"
                                    value="{{ old('pricing', 'Franco FACTORY ( BEKASI )') }}">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-4 col-form-label" for="delivery">Delivery Process</label>
                            <div class="col-sm-8">
                                <input type="text" id="delivery" class="form-control" name="delivery_process"
                                    value="{{ old('delivery_process', 'Ready stock') }}">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-4 col-form-label" for="payment">Payment</label>
                            <div class="col-sm-8">
                                <input type="text" id="payment" class="form-control" name="payment"
                                    value="{{ old('payment', 'Cash Before Delivery') }}">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-4 col-form-label" for="note">Note</label>
                            <div class="col-sm-8">
                                <textarea class="form-control" name="note" id="note"
                                    rows="2" placeholder="Write your note here...">{{ old('note') }}</textarea>
                            </div>
                        </div>
                    </div>

                    {{-- Summary --}}
                    <div class="col-lg-2"></div>
                    <div class="col-lg-4">
                        <div class="row mb-2 align-items-center mt-4">
                            <div class="col-5 text-muted">Subtotal</div>
                            <div class="col-7 fw-semibold text-end" id="display-subtotal">Rp 0</div>
                        </div>
                        <div class="row mb-2 align-items-center">
                            <div class="col-5 text-muted">Discount</div>
                            <div class="col-7">
                                <div class="input-group input-group-sm">
                                    <input type="number" class="form-control text-end" name="diskon" id="input-diskon"
                                        value="0" min="0" max="100" step="0.01">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-2 align-items-center">
                            <div class="col-5 text-muted">VAT 11%</div>
                            <div class="col-7 d-flex align-items-center gap-2">
                                <div class="form-check form-switch mb-0">
                                    <input class="form-check-input" type="checkbox" name="tax" id="toggle-tax" value="1">
                                </div>
                                <span id="display-tax" class="text-muted small">Rp 0</span>
                            </div>
                        </div>
                        <hr>
                        <div class="row align-items-center">
                            <div class="col-5 fw-bold">Total</div>
                            <div class="col-7 fw-bold text-end fs-5" id="display-total">Rp 0</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mb-4">
            <a href="{{ route('unit-quotation.index') }}" class="btn btn-label-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">
                <i class="mdi mdi-content-save me-1"></i> Save Quotation
            </button>
        </div>
    </form>

    {{-- TEMPLATES (hidden, cloned by JS) --}}
    <template id="tmpl-unit-row">
        <div class="unit-row border-bottom p-3" data-type="unit">
            <input type="hidden" name="items[__IDX__][type]" value="unit">
            <input type="hidden" name="items[__IDX__][id_unit]" class="field-id-unit">
            <input type="hidden" name="items[__IDX__][spec_visible]" class="field-spec-visible">

            <div class="row g-2 align-items-start">
                <div class="col-md-4">
                    <select class="select2-unit-search form-select form-select-sm" style="width:100%">
                        <option value="">Search unit (SKU / Brand / Model)...</option>
                    </select>
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
                    <button type="button" class="btn btn-sm btn-icon btn-label-danger btn-remove-row">
                        <i class="mdi mdi-delete-outline"></i>
                    </button>
                </div>
            </div>

            {{-- Custom Title --}}
            <div class="mt-2">
                <input type="text" class="form-control form-control-sm field-label"
                    name="items[__IDX__][label]"
                    placeholder="Judul item (otomatis terisi, bisa diubah)">
            </div>

            {{-- Spec Preview --}}
            <div class="spec-preview mt-2 ms-1 ps-3 border-start border-2" style="display:none;">
                <div class="spec-rows"></div>
                <p class="text-muted small mb-0 mt-1">
                    <i class="mdi mdi-information-outline me-1"></i>
                    Click <kbd>×</kbd> on a spec to hide it from the quotation.
                </p>
            </div>
        </div>
    </template>

    <template id="tmpl-custom-row">
        <div class="unit-row border-bottom p-3" data-type="custom">
            <input type="hidden" name="items[__IDX__][type]" value="custom">
            <div class="row g-2">
                <div class="col-md-6">
                    <input type="text" class="form-control form-control-sm"
                        name="items[__IDX__][label]" placeholder="Title *" required>
                </div>
                <div class="col-md-6">
                    <input type="text" class="form-control form-control-sm"
                        name="items[__IDX__][description]" placeholder="Description (optional)">
                </div>
                <div class="col-md-2">
                    <div class="input-group input-group-sm">
                        <input type="number" class="form-control text-center field-qty"
                            name="items[__IDX__][qty]" value="1" min="1">
                        <select class="form-select" name="items[__IDX__][info_qty]" style="max-width:70px;">
                            <option value="Lot">Lot</option>
                            <option value="Set">Set</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <input type="text" class="form-control form-control-sm text-end field-price rupiah-input"
                        name="items[__IDX__][price]" placeholder="Price" autocomplete="off">
                </div>
                <div class="col-md-1">
                    <div class="input-group input-group-sm">
                        <input type="number" class="form-control text-center field-disc"
                            name="items[__IDX__][disc]" value="0" min="0" max="100">
                        <span class="input-group-text">%</span>
                    </div>
                </div>
                <div class="col-md-1 text-end">
                    <span class="field-amount fw-semibold text-primary">Rp 0</span>
                </div>
                <div class="col-md-1 text-end">
                    <button type="button" class="btn btn-sm btn-icon btn-label-danger btn-remove-row">
                        <i class="mdi mdi-delete-outline"></i>
                    </button>
                </div>
            </div>
        </div>
    </template>
@endsection

@push('after-style')
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/select2/select2.css" />
@endpush

@push('after-script')
    <script src="{{ asset('assets') }}/vendor/libs/select2/select2.js"></script>
@endpush

@push('page-script')
    <script src="{{ asset('assets') }}/includes/form-unit-quotation.js"></script>
@endpush
