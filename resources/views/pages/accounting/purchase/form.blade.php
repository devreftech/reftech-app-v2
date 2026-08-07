@extends('layouts.sales.app')
@section('title', @$purchase ? 'Edit Purchase Order' : 'Create Purchase Order')
@section('content')
    {{-- Hero Page Header & Top Bar --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center py-3 mb-3 gap-3">
        <div>
            <h4 class="fw-bold mb-1">
                <span class="text-muted fw-light">Procurement / <a href="{{ route('purchase.index') }}" class="text-muted">Purchase Order</a> /</span>
                {{ @$purchase ? 'Edit' : 'Create' }}
            </h4>
            <p class="text-muted mb-0 small"><i class="mdi mdi-cart-outline me-1"></i> Buat dokumen Purchase Order ke Supplier</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('purchase.index') }}" class="btn btn-label-secondary">
                <i class="mdi mdi-arrow-left me-1"></i> Back
            </a>
            <button type="submit" form="formAuthentication" class="btn btn-primary shadow-sm">
                <i class="mdi mdi-content-save me-1"></i> Save PO
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
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Hero No PO Card --}}
        <div class="card mb-4 border-0 shadow-sm" style="background: linear-gradient(135deg, #f8f9ff 0%, #f0f2ff 100%); border-left: 5px solid #696cff !important;">
            <div class="card-body py-3">
                <div class="row align-items-center g-3">
                    <div class="col-md-8 col-12">
                        <label class="form-label text-uppercase fw-bold text-primary small mb-1" style="letter-spacing: .5px;">
                            <i class="mdi mdi-pound me-1"></i> PO Number
                        </label>
                        <input type="text" class="form-control form-control-lg fw-bold bg-white text-primary border-primary-subtle shadow-sm"
                            id="no_po_display" name="no_po" required style="font-size: 1.35rem;"
                            value="{{ old('no_po', @$purchase->no_po ?? $previewNoPo ?? '') }}">
                    </div>
                    <div class="col-md-4 col-12 text-md-end">
                        @if (@$purchase)
                            <span class="badge bg-label-primary px-3 py-2 fs-6 rounded-pill">
                                <i class="mdi mdi-shape-outline me-1"></i> {{ $purchase->category }}
                            </span>
                        @else
                            <span class="badge bg-label-secondary px-3 py-2 fs-6 rounded-pill">
                                <i class="mdi mdi-clock-outline me-1"></i> NEW PO
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- SUPPLIER & PO DETAILS --}}
        <div class="card mb-4 border-0 shadow-sm">
            <div class="card-header bg-transparent border-bottom py-3 d-flex align-items-center">
                <h6 class="card-title mb-0 fw-bold text-dark">
                    <i class="mdi mdi-domain me-2 text-primary fs-5"></i> Supplier & Purchase Order Details
                </h6>
            </div>
            <div class="card-body pt-4">
                <div class="row g-3">
                    <div class="col-12">
                        <div class="d-flex align-items-center text-muted small fw-bold text-uppercase mb-1" style="letter-spacing:.5px;">
                            <i class="mdi mdi-account-group-outline me-1"></i> Supplier
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="d-flex align-items-center gap-2">
                            <div class="form-floating form-floating-outline flex-grow-1">
                                <select id="supplier-dropdown" class="select2 form-select invoice-item-supplier"
                                    data-allow-clear="true" name="supplier" data-id="1" required
                                    {{ Auth::user()->role == 'Logistic' ? 'disabled' : '' }}>
                                    <option value="">Pilih Supplier...</option>
                                    @foreach ($suppliers as $supp)
                                        <option value="{{ $supp->id }}" data-info="{{ $supp->info }}"
                                            data-code="{{ $supp->code }}" data-phone="{{ $supp->phone }}"
                                            {{ @$purchase->id_supplier == $supp->id ? 'selected' : '' }}>
                                            {{ $supp->supplier }}
                                        </option>
                                    @endforeach
                                </select>
                                <label for="supplier-dropdown">Supplier</label>
                            </div>
                            <button type="button" class="btn btn-label-secondary"
                                data-bs-toggle="modal" data-bs-target="#quickAddSupplierModal"
                                {{ Auth::user()->role == 'Logistic' ? 'disabled' : '' }}>
                                <i class="mdi mdi-domain me-1"></i>Supplier Baru
                            </button>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating form-floating-outline">
                            <input class="form-control" type="date" id="date" name="date" required
                                value="{{ old('date', @$purchase->date ?? \Carbon\Carbon::today()->format('Y-m-d')) }}">
                            <label for="date">Date</label>
                        </div>
                    </div>

                    <div class="col-12">
                        <hr class="my-2">
                        <div class="d-flex align-items-center text-muted small fw-bold text-uppercase mb-1" style="letter-spacing:.5px;">
                            <i class="mdi mdi-file-document-outline me-1"></i> PO Parameters
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-floating form-floating-outline">
                            <input class="form-control" type="text" placeholder="Put Delivery Time Here ...."
                                id="delivery" name="delivery"
                                value="{{ old('delivery', @$purchase->delivery ?? '') }}">
                            <label for="delivery">Delivery Time</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-floating form-floating-outline">
                            <input class="form-control" type="text" placeholder="Put ATTN Quotation Here ...."
                                id="attn" name="attn" value="{{ old('attn', @$purchase->attn ?? '') }}">
                            <label for="attn">ATTN</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-floating form-floating-outline">
                            <input class="form-control" type="text" placeholder="Put Mobile Here ...."
                                id="mobile" name="mobile" value="{{ old('mobile', @$purchase->mobile ?? '') }}">
                            <label for="mobile">Mobile</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-floating form-floating-outline">
                            <input class="form-control" type="text" placeholder="text Payment Here ...."
                                id="payment" name="payment"
                                value="{{ old('payment', @$purchase->payment ?? '') }}">
                            <label for="payment">Payment</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- LINE ITEMS --}}
        <div class="card mb-4 border-0 shadow-sm">
            <div class="card-header bg-transparent border-bottom py-3 d-flex align-items-center justify-content-between">
                <h6 class="card-title mb-0 fw-bold text-dark">
                    <i class="mdi mdi-cube-outline me-2 text-primary fs-5"></i> Purchase Order Items
                </h6>
                <span class="badge bg-label-secondary" id="items-count-badge">0 Items</span>
            </div>
            <div class="card-body p-0">
                <div class="form-invoice-repeater source-item">
                    @if (@$purchase)
                        <div class="mb-0" data-repeater-list="group-a">
                            @php
                                $no = 1;
                            @endphp
                            @foreach ($dPurchase as $item)
                                <div class="repeater-wrapper" data-repeater-item="">
                                    <div class="position-relative border-bottom p-3">
                                        <div class="row w-100">
                                            <input type="hidden" class="invoice-item-detail-id" name="detail_id[]"
                                                value="{{ $item->id }}">
                                            <div class="col-md col-12 mb-md-0 item-fields">
                                                <div class="item-category-toggle mb-2">
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input item-category-radio" type="radio"
                                                            value="Sparepart" {{ ($item->category ?? 'Sparepart') != 'Unit' && ($item->category ?? '') != 'Custom' ? 'checked' : '' }}>
                                                        <label class="form-check-label small">Sparepart</label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input item-category-radio" type="radio"
                                                            value="Unit" {{ ($item->category ?? '') == 'Unit' ? 'checked' : '' }}>
                                                        <label class="form-check-label small">Unit Global</label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input item-category-radio" type="radio"
                                                            value="Custom" {{ ($item->category ?? '') == 'Custom' ? 'checked' : '' }}>
                                                        <label class="form-check-label small">Custom Item</label>
                                                    </div>
                                                </div>
                                                <div class="field-product-sparepart">
                                                    <select class="form-select form-select-sm select2-product-po" name="id_product[]">
                                                        <option value="">Cari SKU / Product...</option>
                                                        @foreach ($products ?? [] as $p)
                                                            <option value="{{ $p->id }}"
                                                                data-label="{{ $p->commodity }} — {{ $p->description }}"
                                                                data-unit="{{ $p->unit }}"
                                                                {{ $item->id_product == $p->id ? 'selected' : '' }}>
                                                                {{ $p->commodity }} — {{ $p->description }}
                                                            </option>
                                                        @endforeach
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
                                                </div>
                                                <div class="field-product-custom" style="display:none;">
                                                    <textarea class="form-control form-control-sm invoice-item-detail-product"
                                                        name="product[]" rows="2"
                                                        placeholder="Nama/Deskripsi Item Custom...">{{ $item->product }}</textarea>
                                                </div>
                                            </div>
                                            <div class="col-md-3 col-12 mb-md-0 mb-3">
                                                <p class="mb-2 repeater-title small text-muted">Price</p>
                                                <div class="input-group input-group-sm" data-price="{{ $no }}">
                                                    <span class="input-group-text">Rp. </span>
                                                    <input type="text" class="form-control invoice-item-price-label"
                                                        id="priceLabel-{{ $no }}"
                                                        data-id="{{ $no }}" name="harga"
                                                        placeholder="Put Price Here" data-type="currency"
                                                        min="0" pattern="^[0-9]\d{0,2}(\.\d{3})*$"
                                                        value="{{ number_format($item->price, '0', ',', '.') }}">
                                                    <input class="form-control invoice-item-price" type="number"
                                                        name="price[]" id="price-{{ $no }}"
                                                        value="{{ old('price[]', $item->price) }}" hidden>
                                                </div>
                                            </div>
                                            <div class="col-md-1 col-12 mb-md-0 mb-3">
                                                <p class="mb-2 repeater-title small text-muted">Qty</p>
                                                <input type="number" class="form-control form-control-sm invoice-item-qty"
                                                    placeholder="Min 1" name="qty[]" id="qty-{{ $no }}"
                                                    data-id="{{ $no }}" min="1"
                                                    value="{{ $item->qty }}">
                                            </div>
                                            <div class="col-md-1 col-12 mb-md-0 mb-3">
                                                <p class="mb-2 repeater-title small text-muted">Info Qty</p>
                                                <select class="form-select form-select-sm invoice-item-info"
                                                    id="info-qty-{{ $no }}"
                                                    data-id="{{ $no }}"
                                                    aria-label="Default select example" name="info_qty[]">
                                                    <option disabled>---Info---</option>
                                                    <option value="Pcs" {{ $item->info_qty == 'Pcs' ? 'selected' : '' }}>Pcs</option>
                                                    <option value="Set" {{ $item->info_qty == 'Set' ? 'selected' : '' }}>Set</option>
                                                    <option value="Pail" {{ $item->info_qty == 'Pail' ? 'selected' : '' }}>Pail</option>
                                                    <option value="Unit" {{ $item->info_qty == 'Unit' ? 'selected' : '' }}>Unit</option>
                                                    <option value="Lot" {{ $item->info_qty == 'Lot' ? 'selected' : '' }}>Lot</option>
                                                    <option value="Meter" {{ $item->info_qty == 'Meter' ? 'selected' : '' }}>Meter</option>
                                                    <option value="Can" {{ $item->info_qty == 'Can' ? 'selected' : '' }}>Can</option>
                                                    <option value="Hari" {{ $item->info_qty == 'Hari' ? 'selected' : '' }}>Hari</option>
                                                    <option value="Bulan" {{ $item->info_qty == 'Bulan' ? 'selected' : '' }}>Bulan</option>
                                                    <option value="Kg" {{ $item->info_qty == 'Kg' ? 'selected' : '' }}>Kg</option>
                                                    <option value="Tube" {{ $item->info_qty == 'Tube' ? 'selected' : '' }}>Tube</option>
                                                    <option value="Titik" {{ $item->info_qty == 'Titik' ? 'selected' : '' }}>Titik</option>
                                                </select>
                                            </div>
                                            <div class="col-md-1 col-12 mb-md-0 mb-3">
                                                <p class="mb-2 repeater-title small text-muted">Disc (%)</p>
                                                <div class="input-group input-group-sm" data-disc="{{$no}}">
                                                    <input type="text" class="form-control invoice-item-disc"
                                                        id="disc-{{ $no }}" data-id="{{ $no }}"
                                                        name="disc[]" placeholder="%"
                                                        value="{{ old('disc[]', $item->disc) }}">
                                                </div>
                                            </div>
                                            <div class="col-md-2 col-12 pe-4 text-md-end">
                                                <p class="mb-2 repeater-title small text-muted">Amount</p>
                                                <p class="mb-0 amount-label fw-semibold text-primary" id="amount-label-{{$no}}" data-id="{{$no}}">
                                                    {{ number_format($item->amount, 0, ',', '.') }}</p>
                                                <input type="number" class="form-control invoice-item-amount"
                                                    name="amount[]" id="amount-{{ $no }}"
                                                    data-id="{{ $no }}"
                                                    value="{{ old('amount[]', $item->amount) }}" hidden>
                                            </div>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-icon btn-label-danger btn-del position-absolute top-0 end-0 m-2"
                                            data-repeater-delete="">
                                            <i class="mdi mdi-delete-outline"></i>
                                        </button>
                                    </div>
                                </div>
                                @php
                                    $no++;
                                @endphp
                            @endforeach
                        </div>
                    @else
                        <div class="mb-0" data-repeater-list="group-a">
                            <div class="repeater-wrapper" data-repeater-item="">
                                <div class="position-relative border-bottom p-3">
                                    <div class="row w-100">
                                        <input type="hidden" class="invoice-item-detail-id" name="detail_id[]" value="">
                                        <div class="col-md col-12 mb-md-0 item-fields">
                                            <div class="item-category-toggle mb-2">
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input item-category-radio" type="radio"
                                                        value="Sparepart" checked>
                                                    <label class="form-check-label small">Sparepart</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input item-category-radio" type="radio"
                                                        value="Unit">
                                                    <label class="form-check-label small">Unit Global</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input item-category-radio" type="radio"
                                                        value="Custom">
                                                    <label class="form-check-label small">Custom Item</label>
                                                </div>
                                            </div>
                                            <div class="field-product-sparepart">
                                                <select class="form-select form-select-sm select2-product-po" name="id_product[]">
                                                    <option value="">Cari SKU / Product...</option>
                                                    @foreach ($products ?? [] as $p)
                                                        <option value="{{ $p->id }}"
                                                            data-label="{{ $p->commodity }} — {{ $p->description }}"
                                                            data-unit="{{ $p->unit }}">
                                                            {{ $p->commodity }} — {{ $p->description }}
                                                        </option>
                                                    @endforeach
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
                                            </div>
                                            <div class="field-product-custom" style="display:none;">
                                                <textarea class="form-control form-control-sm invoice-item-detail-product"
                                                    name="product[]" rows="2"
                                                    placeholder="Nama/Deskripsi Item Custom..."></textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-12 mb-md-0 mb-3">
                                            <p class="mb-2 repeater-title small text-muted">Price</p>
                                            <div class="input-group input-group-sm" data-price="1">
                                                <span class="input-group-text">Rp. </span>
                                                <input type="text" class="form-control invoice-item-price-label"
                                                    id="priceLabel-1" data-id="1" name="harga"
                                                    placeholder="Put Price Here" data-type="currency" min="0"
                                                    pattern="^[0-9]\d{0,2}(\.\d{3})*$"
                                                    value="{{ old('price[]') }}">
                                                <input class="form-control invoice-item-price" type="number"
                                                    name="price[]" id="price-1" value="{{ old('price[]') }}" hidden>
                                            </div>
                                        </div>
                                        <div class="col-md-1 col-12 mb-md-0 mb-3">
                                            <p class="mb-2 repeater-title small text-muted">Qty</p>
                                            <input type="number" class="form-control form-control-sm invoice-item-qty"
                                                placeholder="Min 1" name="qty[]" id="qty-1" data-id="1"
                                                min="1" value="{{ old('qty[]') }}">
                                        </div>
                                        <div class="col-md-1 col-12 mb-md-0 mb-3">
                                            <p class="mb-2 repeater-title small text-muted">Info Qty</p>
                                            <select class="form-select form-select-sm invoice-item-info" id="info-qty-1"
                                                data-id="1" aria-label="Default select example" name="info_qty[]">
                                                <option disabled>---Info---</option>
                                                <option value="Pcs">Pcs</option>
                                                <option value="Set">Set</option>
                                                <option value="Pail">Pail</option>
                                                <option value="Unit">Unit</option>
                                                <option value="Lot">Lot</option>
                                                <option value="Meter">Meter</option>
                                                <option value="Can">Can</option>
                                                <option value="Hari">Hari</option>
                                                <option value="Bulan">Bulan</option>
                                                <option value="Kg">Kg</option>
                                                <option value="Tube">Tube</option>
                                                <option value="Titik">Titik</option>
                                            </select>
                                        </div>
                                        <div class="col-md-1 col-12 mb-md-0 mb-3">
                                            <p class="mb-2 repeater-title small text-muted">Disc (%)</p>
                                            <div class="input-group input-group-sm" data-disc="1">
                                                <input type="text" class="form-control invoice-item-disc"
                                                    id="disc-1" data-id="1" name="disc[]" placeholder="%"
                                                    value="{{ old('disc[]', 0) }}">
                                            </div>
                                        </div>
                                        <div class="col-md-2 col-12 pe-4 text-md-end">
                                            <p class="mb-2 repeater-title small text-muted">Amount</p>
                                            <p class="mb-0 amount-label fw-semibold text-primary" id="amount-label-1" data-id="1">
                                                {{ old(strval('amount[]')) }}</p>
                                            <input type="number" class="form-control invoice-item-amount"
                                                name="amount[]" id="amount-1" data-id="1"
                                                value="{{ old('amount[]') }}" hidden>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-icon btn-label-danger btn-del position-absolute top-0 end-0 m-2"
                                        data-repeater-delete="">
                                        <i class="mdi mdi-delete-outline"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endif
                    <div class="d-flex flex-wrap gap-2 p-3 border-top bg-light-subtle">
                        <button type="button" class="btn btn-sm btn-primary shadow-sm btn-add" data-repeater-create="">
                            <i class="mdi mdi-plus me-1"></i> Add Item
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="btn-add-custom-item">
                            <i class="mdi mdi-format-list-bulleted me-1"></i> Add Custom Item
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- SUMMARY + NOTE --}}
        <div class="card mb-4 border-0 shadow-sm">
            <div class="card-body">
                <div class="row g-4">
                    {{-- Note (Kiri) --}}
                    <div class="col-lg-7">
                        <h6 class="fw-bold mb-2 text-dark">
                            <i class="mdi mdi-notebook-edit-outline me-1 text-primary"></i> Note / PO Remarks
                        </h6>
                        <textarea class="form-control h-px-100" rows="3" placeholder="Write your note here...."
                            name="note">{{ @$purchase->note }}</textarea>
                    </div>

                    {{-- Summary Card (Kanan) --}}
                    <div class="col-lg-5">
                        <div class="card border-0 shadow-sm overflow-hidden" style="background: #ffffff; border: 1px solid #e0e0ff !important; border-radius: 12px;">
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
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="text-muted small">Subtotal</span>
                                    <div class="text-end">
                                        <span class="fw-bold text-dark fs-6 subtotal-label" id="subtotal-label" data-id="1">
                                            {{ old('subtotal', @$purchase->subtotal ? 'RP ' . number_format(@$purchase->subtotal, 0, '', '.') : 'RP 0') }}
                                        </span>
                                        <input type="number" id="subtotal" name="subtotal"
                                            value="{{ old('subtotal', @$purchase->subtotal ?? '') }}" hidden>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="text-muted small">Discount</span>
                                    <div style="width: 160px;">
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text">Rp</span>
                                            <input type="text" id="diskon-label" class="form-control text-end fw-semibold"
                                                placeholder="0" data-type="currency"
                                                pattern="^[0-9]\d{0,2}(\.\d{3})*$"
                                                value="{{ old('diskon', @$purchase->diskon ? number_format(@$purchase->diskon, 0, '', '.') : '0') }}">
                                            <input type="number" name="diskon" id="diskon"
                                                value="{{ old('diskon', @$purchase->diskon ?? '0') }}" hidden>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="text-muted small">Tax (PPN 12%)</span>
                                        <div class="form-check form-switch mb-0">
                                            <input class="form-check-input" type="checkbox" role="switch"
                                                id="taxSwitch" {{ @$purchase->vat == '11' ? 'checked' : '' }}>
                                        </div>
                                    </div>
                                    <span class="fw-semibold tax-amount-label text-muted small" id="taxAmountLabel">
                                        @if (@$purchase && $purchase->vat == '11')
                                            {{ 'RP ' . number_format(($purchase->subtotal - $purchase->diskon) * $purchase->vat / 100, 0, '', '.') }}
                                        @endif
                                    </span>
                                    <input type="hidden" id="tax" name="tax" value="{{ old('tax', @$purchase->vat ?? '0') }}">
                                </div>

                                {{-- Total Hero Box --}}
                                <div class="p-3 rounded-3 d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #f0f2ff 0%, #e8ebff 100%); border: 1px dashed #696cff;">
                                    <div>
                                        <div class="text-uppercase fw-bold text-primary" style="font-size: 10px; letter-spacing: 0.8px;">Total Amount</div>
                                        <div class="text-muted" style="font-size: 10px;">( Inclusive of Tax &amp; Discount )</div>
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
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mb-4">
            <a href="{{ route('purchase.index') }}" class="btn btn-label-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary shadow-sm px-4">
                <i class="mdi mdi-content-save me-1"></i> Save PO
            </button>
        </div>
    </form>

    <div class="modal fade" id="quickAddSupplierModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Supplier Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Code <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="qsCode" placeholder="SUP001">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Supplier Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="qsName" placeholder="PT Contoh Jaya">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Info <span class="text-danger">*</span></label>
                        <select class="form-select" id="qsInfo">
                            <option value="" disabled selected>-- Pilih --</option>
                            <option value="Lokal">Lokal</option>
                            <option value="Import">Import</option>
                        </select>
                    </div>
                    <div id="qsError" class="alert alert-danger d-none"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="saveQuickAddSupplier">
                        <i class="mdi mdi-content-save-outline me-1"></i> Simpan
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
    <script src="{{ asset('assets') }}/includes/repeater/jquery-repeater-invoice.js"></script>
    <script src="{{ asset('assets') }}/js/app-invoice-add.js"></script>
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
                currency: 'IDR'
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
            var $supplierDropdown = $('#supplier-dropdown');
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

            // Auto-fill Mobile dari nomor telepon Supplier terpilih
            $supplierDropdown.on('change', function() {
                var phone = $(this).find(':selected').data('phone');
                if (phone) {
                    $('#mobile').val(phone);
                }
            });

            // Quick Add Supplier (AJAX, tanpa reload)
            $('#saveQuickAddSupplier').on('click', function() {
                var code = $('#qsCode').val().trim();
                var name = $('#qsName').val().trim();
                var info = $('#qsInfo').val();

                if (!code || !name || !info) {
                    $('#qsError').removeClass('d-none').text('Semua field wajib diisi.');
                    return;
                }

                $.ajax({
                    url: '{{ route('supplier.quick-store') }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        code: code,
                        supplier: name,
                        info: info
                    },
                    success: function(res) {
                        if (res.success) {
                            var newSupplier = res.data;
                            var option = new Option(newSupplier.supplier, newSupplier.id, true, true);
                            $(option).attr('data-info', newSupplier.info);
                            $(option).attr('data-code', newSupplier.code);
                            $supplierDropdown.append(option).trigger('change');

                            $('#qsCode').val('');
                            $('#qsName').val('');
                            $('#qsInfo').val('');
                            $('#qsError').addClass('d-none').text('');
                            $('#quickAddSupplierModal').modal('hide');
                        }
                    },
                    error: function(xhr) {
                        var msg = xhr.responseJSON && xhr.responseJSON.message ?
                            xhr.responseJSON.message : 'Gagal menyimpan, coba lagi.';
                        $('#qsError').removeClass('d-none').text(msg);
                    }
                });
            });

            function formatNumber(n) {
                return n.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ".")
            }

            function formatCurrencyDiscount(input) {
                var input_val = input.val();

                // don't validate empty input
                if (input_val === "") {
                    return;
                }

                // add commas to number
                // remove all non-digits
                input_val = formatNumber(input_val);

                // send updated string to input
                input.val(input_val);
                var nomorInt = parseFloat(input_val.replace(/[.,]/g, ''));
                $('#diskon').val(nomorInt);
            }

            // Formatting Discount Quotation (delegated: works for existing + future rows)
            $(document).on('keyup', '#diskon-label', function() {
                formatCurrencyDiscount($(this));
            });

            // Toggle Tax (PPN 12%) on/off
            $(document).on('change', '#taxSwitch', function() {
                $('#tax').val($(this).is(':checked') ? 11 : 0).trigger('change');
            });

            $(document).on('keyup', '.invoice-item-price-label', function() {
                var input = $(this)
                var id = input.data('id');
                var input_val = input.val();

                // original length
                var original_len = input_val.length;

                // initial caret position
                var caret_pos = input.prop("selectionStart");

                // add commas to number
                // remove all non-digits
                input_val = formatNumber(input_val);

                // send updated string to input
                input.val(input_val);
                var nomorInt = parseFloat(input_val.replace(/[.,]/g, ''));
                $(`#price-${id}`).val(nomorInt);

                // put caret back in the right position
                var updated_len = input_val.length;
                caret_pos = updated_len - original_len + caret_pos;
                input[0].setSelectionRange(caret_pos, caret_pos);
            });

            // Logic amount + Subtotal
            $(document).on('keyup change click', '.invoice-item-price-label, .invoice-item-qty, .invoice-item-disc',
                function(ev) {
                    // mengambil ID
                    var id = $(this).data('id');
                    // prepare data
                    var sTotal = 0,
                        row = 0,
                        amount = 0,
                        hasil = 0,
                        valHarga = $(`#price-${id}`).val(),
                        harga = Number(valHarga),
                        disc = isNaN(parseInt($(`#disc-${id}`).val())) ? 0 : parseInt($(`#disc-${id}`).val());
                    // menghitung hasil
                    hasil = harga * $(`#qty-${id}`).val();
                    // menghitung amount
                    amount = (hasil - (hasil * disc / 100));
                    // memasukan data amount dan subtotal
                    $(`#amount-${id}`).val(amount);
                    $(`#amount-label-${id}`).html(`${formatter.format(amount)}`);
                    $('.amount-label').each(() => {
                        row++;
                        sTotal += parseInt($(`#amount-${row}`).val())
                    });
                    $('#subtotal-label').html(`${formatter.format(sTotal)}`);
                    $('#subtotal').val(sTotal);
                });

            // Logic Harga Total
            $(document).on('keyup change',
                '#subtotal, #tax, #diskon-label, .invoice-item-price-label, .invoice-item-qty, .invoice-item-disc',
                () => {
                    var noTax = 0;
                    var hTotal = 0;
                    var sTotal = isNaN(parseInt($('#subtotal').val())) ? 0 : parseInt($('#subtotal').val());
                    var discount = isNaN(parseInt($('#diskon').val())) ? 0 : parseInt($('#diskon').val());
                    var dTotal = sTotal - discount;
                    var tax = isNaN(parseInt($('#tax').val())) ? 0 : parseInt($('#tax').val());
                    var taxAmount = parseInt(dTotal * tax / 100);
                    hTotal = parseInt(dTotal + taxAmount);
                    noTax = parseInt(dTotal);
                    $('#taxAmountLabel').html(tax > 0 ? formatter.format(taxAmount) : '');
                    $('#hargaTotalLabel').html(`${formatter.format(hTotal)}`);
                    $('#hargaTotal').val(hTotal);
                    $('#totalNoTax').val(noTax);
                });

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
                            dropdownParent: $el.closest('.field-product-sparepart')
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
            }

            // Info Qty ikut otomatis dari master: Sparepart -> unit di tabel Product, Unit Global -> selalu "Unit"
            function lockInfoQty($fields, forcedValue) {
                var $info = $fields.closest('.row').find('.invoice-item-info');
                $info.addClass('pe-none bg-light').attr('tabindex', '-1').attr('aria-readonly', 'true');
                if (forcedValue) {
                    var matched = $info.find('option').filter(function() {
                        return $(this).val().toLowerCase() === String(forcedValue).toLowerCase();
                    });
                    if (matched.length) {
                        $info.val(matched.val());
                    }
                }
            }

            function unlockInfoQty($fields) {
                $fields.closest('.row').find('.invoice-item-info')
                    .removeClass('pe-none bg-light').removeAttr('tabindex').removeAttr('aria-readonly');
            }

            function applyRowCategory($fields) {
                var category = $fields.find('.item-category-radio:checked').val() || 'Sparepart';
                var $sparepart = $fields.find('.field-product-sparepart');
                var $unit = $fields.find('.field-product-unit');
                var $custom = $fields.find('.field-product-custom');
                var $product = $fields.find('.select2-product-po');
                var $unitSelect = $fields.find('.select2-unit-po');
                var $customText = $fields.find('.invoice-item-detail-product');

                $sparepart.hide();
                $unit.hide();
                $custom.hide();
                $product.removeAttr('required');
                $unitSelect.removeAttr('required');
                $customText.removeAttr('required');

                if (category === 'Unit') {
                    $unit.show();
                    $unitSelect.attr('required', true);
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
                    pendingCustomAdd = false;
                }
                applyRowCategory($newFields);
                updateItemsCountBadge();
            });

            $(document).on('repeater:deleted', updateItemsCountBadge);

            // Add Custom Item: pakai mekanisme "Add Item" yang sama, lalu langsung set ke mode Custom
            $('#btn-add-custom-item').on('click', function() {
                pendingCustomAdd = true;
                $('.btn-add[data-repeater-create]').trigger('click');
            });

            // Sinkronkan label Product/Unit terpilih ke field product[] tersembunyi (dipakai halaman detail/print)
            $(document).on('change', '.select2-product-po, .select2-unit-po', function() {
                var $fields = $(this).closest('.item-fields');
                var label = $(this).find(':selected').data('label') || '';
                $fields.find('.invoice-item-detail-product').val(label);
                applyRowCategory($fields);
            });
        })
    </script>
@endpush
