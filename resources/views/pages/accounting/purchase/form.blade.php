@extends('layouts.sales.app')
@section('title', @$purchase ? 'Edit Purchase Order' : 'Create Purchase Order')
@section('content')
    <style>
        /* Nonaktifkan efek hover geser-ke-atas (translateY) khusus di halaman ini */
        #formAuthentication .card:hover {
            transform: none !important;
            box-shadow: 0 4px 18px 0 rgba(24, 28, 33, 0.03) !important;
        }
    </style>

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
        <input type="hidden" name="id_purchase_request" value="{{ old('id_purchase_request', $sourcePr->id ?? '') }}">
        @if ($sourcePr ?? null)
            <div class="alert alert-info d-flex align-items-center gap-2 mb-3">
                <i class="mdi mdi-file-document-outline fs-5"></i>
                <div>Item Sparepart di bawah otomatis dari <strong>{{ $sourcePr->no_pr }}</strong>. Silakan pilih supplier & lengkapi harga.</div>
            </div>
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
                            <i class="mdi mdi-account-group-outline me-1"></i> Supplier & Kontak
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="d-flex align-items-center gap-2">
                            <div class="form-floating form-floating-outline flex-grow-1">
                                <select id="supplier-dropdown" class="select2 form-select invoice-item-supplier"
                                    data-allow-clear="true" name="supplier" data-id="1" required
                                    {{ Auth::user()->role == 'Logistic' ? 'disabled' : '' }}>
                                    <option value="">Pilih Supplier...</option>
                                    @foreach ($suppliers as $supp)
                                        <option value="{{ $supp->id }}" data-info="{{ $supp->info }}"
                                            data-code="{{ $supp->code }}" data-phone="{{ $supp->phone }}"
                                            data-address="{{ $supp->address }}"
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
                            <button type="button" id="btn-edit-supplier" class="btn btn-label-primary d-none"
                                data-bs-toggle="modal" data-bs-target="#editSupplierModal"
                                {{ Auth::user()->role == 'Logistic' ? 'disabled' : '' }}>
                                <i class="mdi mdi-pencil-outline me-1"></i>Edit Supplier
                            </button>
                        </div>
                    </div>
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
                                <label for="attn">ATTN (PIC)</label>
                            </div>
                            <button type="button" class="btn btn-label-primary btn-sm d-none" id="btn-edit-attn-pic" title="Edit PIC">
                                <i class="mdi mdi-pencil-outline"></i>
                            </button>
                            <button type="button" class="btn btn-label-secondary btn-sm" id="btn-add-attn-pic" title="Tambah PIC">
                                <i class="mdi mdi-plus"></i>
                            </button>
                        </div>
                        <div class="form-text small text-muted" id="attn-empty-hint" style="display:none;">
                            Belum ada PIC untuk supplier ini.
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating form-floating-outline">
                            <input class="form-control" type="text" placeholder="Put Mobile Here ...."
                                id="mobile" name="mobile" value="{{ old('mobile', @$purchase->mobile ?? '') }}">
                            <label for="mobile">Mobile</label>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-floating form-floating-outline">
                            <textarea class="form-control" id="address" name="address" rows="2"
                                placeholder="Alamat supplier akan ter-load otomatis saat supplier dipilih">{{ old('address', @$purchase->address ?? '') }}</textarea>
                            <label for="address">Address (Supplier)</label>
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
                            <input class="form-control" type="date" id="date" name="date" required
                                value="{{ old('date', @$purchase->date ?? \Carbon\Carbon::today()->format('Y-m-d')) }}">
                            <label for="date">Date</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-floating form-floating-outline">
                            <input class="form-control" type="text" placeholder="Put Delivery Time Here ...."
                                id="delivery" name="delivery"
                                value="{{ old('delivery', @$purchase->delivery ?? 'ASAP') }}">
                            <label for="delivery">Delivery Time</label>
                        </div>
                    </div>
                    <div class="col-md-3">
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
                            <label for="payment-select">Payment</label>
                            <input type="hidden" name="payment" id="input-payment-hidden"
                                value="{{ old('payment', @$purchase->payment ?? 'Cash Before Delivery') }}">
                        </div>
                        <div class="mt-2" id="manual-payment-wrapper" style="display:none;">
                            <input type="text" class="form-control" id="input-payment-manual"
                                placeholder="Ketik custom payment term...">
                        </div>
                    </div>
                    <div class="col-md-3">
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
                            <button type="button" class="btn btn-label-secondary" data-bs-toggle="modal"
                                data-bs-target="#quickAddPoTypeModal" title="Tambah Tipe PO">
                                <i class="mdi mdi-plus"></i>
                            </button>
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
                    @php
                        $unitList = ['Pcs', 'Set', 'Pail', 'Drum', 'Unit', 'Lot', 'Meter', 'Can', 'Hari', 'Bulan', 'Kg', 'Tube', 'Titik', 'Box', 'Roll', 'Liter', 'Lembar', 'Paket', 'Karton', 'Pallet', 'Botol', 'Batang'];
                    @endphp
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
                                            <input type="hidden" name="pr_detail_id[]" value="">
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
                                                {{-- Radio di atas gak punya name (per-baris, exclusivity dihandle JS) supaya
                                                     gak nabrak grup radio baris lain — nilainya disinkronkan ke sini lewat
                                                     applyRowCategory() tiap kali berubah, ini yang beneran ke-submit. --}}
                                                <input type="hidden" class="item-category-value" name="item_category[]"
                                                    value="{{ ($item->category ?? '') == 'Unit' ? 'Unit' : (($item->category ?? '') == 'Custom' ? 'Custom' : 'Sparepart') }}">
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
                                                    <select class="form-select form-select-sm mt-1 select-kondisi-unit" name="kondisi[]">
                                                        <option value="Baru" {{ ($item->kondisi ?? 'Baru') == 'Baru' ? 'selected' : '' }}>Unit Baru (masuk stok jual)</option>
                                                        <option value="Second" {{ ($item->kondisi ?? '') == 'Second' ? 'selected' : '' }}>Unit Second (jadi Fixed Asset, QC dulu)</option>
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
                                                <div class="price-tax-hint mt-1 small d-none" style="font-size: 11px; line-height: 1.35; background: #f0f2ff; padding: 5px 8px; border-radius: 6px; border-left: 3px solid #696cff;">
                                                    <div class="hint-calc-wrapper">
                                                        <div class="text-secondary mb-1">
                                                            <i class="mdi mdi-calculator-variant-outline text-primary me-1"></i>Inc. PPN (11%):<br>
                                                            Harga Exc. PPN: <strong class="text-primary exc-ppn-val">Rp 0</strong> <span class="ppn-val text-muted" style="font-size: 10px;">(PPN: Rp 0)</span><br>
                                                            DPP (11/12): <strong class="text-dark dpp-val" style="font-size: 10.5px;">Rp 0</strong>
                                                        </div>
                                                        <button type="button" class="btn btn-xs btn-primary py-0 px-2 btn-apply-dpp" style="font-size: 10px; height: 22px;">
                                                            <i class="mdi mdi-check me-1"></i> Gunakan Harga Exc. PPN
                                                        </button>
                                                    </div>
                                                    <div class="hint-applied-wrapper d-none">
                                                        <div class="d-flex align-items-center justify-content-between text-success">
                                                            <span style="font-size: 10px;">
                                                                <i class="mdi mdi-check-circle-outline me-1"></i>Exc. PPN: <strong class="applied-dpp-text">Rp 0</strong> <span class="text-muted applied-dpp-calc" style="font-size: 9.5px;">(DPP: Rp 0)</span>
                                                            </span>
                                                            <button type="button" class="btn btn-xs btn-link text-danger p-0 ms-1 btn-reset-dpp" style="font-size: 10px; text-decoration: underline; line-height: 1;">
                                                                Batal
                                                            </button>
                                                        </div>
                                                    </div>
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
                                                <select class="form-select form-select-sm invoice-item-info select2-info-qty"
                                                    id="info-qty-{{ $no }}"
                                                    data-id="{{ $no }}"
                                                    aria-label="Default select example" name="info_qty[]">
                                                    <option disabled value="">---Info---</option>
                                                    @foreach ($unitList as $uOpt)
                                                        <option value="{{ $uOpt }}" {{ strcasecmp($item->info_qty, $uOpt) === 0 ? 'selected' : '' }}>{{ $uOpt }}</option>
                                                    @endforeach
                                                    @if (!empty($item->info_qty) && !collect($unitList)->contains(fn($u) => strcasecmp($u, $item->info_qty) === 0))
                                                        <option value="{{ $item->info_qty }}" selected>{{ $item->info_qty }}</option>
                                                    @endif
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
                    @elseif (!empty($prefillItems))
                        <div class="mb-0" data-repeater-list="group-a">
                            @foreach ($prefillItems as $i => $pi)
                                @php $rno = $i + 1; @endphp
                                <div class="repeater-wrapper" data-repeater-item="">
                                    <div class="position-relative border-bottom p-3">
                                        <div class="row w-100">
                                            <input type="hidden" class="invoice-item-detail-id" name="detail_id[]" value="">
                                            <input type="hidden" name="pr_detail_id[]" value="{{ $pi['pr_detail_id'] ?? '' }}">
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
                                                <input type="hidden" class="item-category-value" name="item_category[]" value="Sparepart">
                                                <div class="field-product-sparepart">
                                                    <select class="form-select form-select-sm select2-product-po" name="id_product[]">
                                                        <option value="">Cari SKU / Product...</option>
                                                        @foreach ($products ?? [] as $p)
                                                            <option value="{{ $p->id }}"
                                                                data-label="{{ $p->commodity }} — {{ $p->description }}"
                                                                data-unit="{{ $p->unit }}"
                                                                {{ $pi['id_product'] == $p->id ? 'selected' : '' }}>
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
                                                    <select class="form-select form-select-sm mt-1 select-kondisi-unit" name="kondisi[]">
                                                        <option value="Baru" selected>Unit Baru (masuk stok jual)</option>
                                                        <option value="Second">Unit Second (jadi Fixed Asset, QC dulu)</option>
                                                    </select>
                                                </div>
                                                <div class="field-product-custom" style="display:none;">
                                                    <textarea class="form-control form-control-sm invoice-item-detail-product"
                                                        name="product[]" rows="2"
                                                        placeholder="Nama/Deskripsi Item Custom...">{{ $pi['label'] }}</textarea>
                                                </div>
                                            </div>
                                            <div class="col-md-3 col-12 mb-md-0 mb-3">
                                                <p class="mb-2 repeater-title small text-muted">Price</p>
                                                <div class="input-group input-group-sm" data-price="{{ $rno }}">
                                                    <span class="input-group-text">Rp. </span>
                                                    <input type="text" class="form-control invoice-item-price-label"
                                                        id="priceLabel-{{ $rno }}" data-id="{{ $rno }}" name="harga"
                                                        placeholder="Put Price Here" data-type="currency" min="0"
                                                        pattern="^[0-9]\d{0,2}(\.\d{3})*$" value="">
                                                    <input class="form-control invoice-item-price" type="number"
                                                        name="price[]" id="price-{{ $rno }}" value="" hidden>
                                                </div>
                                                <div class="price-tax-hint mt-1 small d-none" style="font-size: 11px; line-height: 1.35; background: #f0f2ff; padding: 5px 8px; border-radius: 6px; border-left: 3px solid #696cff;">
                                                    <div class="hint-calc-wrapper">
                                                        <div class="text-secondary mb-1">
                                                            <i class="mdi mdi-calculator-variant-outline text-primary me-1"></i>Inc. PPN (11%):<br>
                                                            Harga Exc. PPN: <strong class="text-primary exc-ppn-val">Rp 0</strong> <span class="ppn-val text-muted" style="font-size: 10px;">(PPN: Rp 0)</span><br>
                                                            DPP (11/12): <strong class="text-dark dpp-val" style="font-size: 10.5px;">Rp 0</strong>
                                                        </div>
                                                        <button type="button" class="btn btn-xs btn-primary py-0 px-2 btn-apply-dpp" style="font-size: 10px; height: 22px;">
                                                            <i class="mdi mdi-check me-1"></i> Gunakan Harga Exc. PPN
                                                        </button>
                                                    </div>
                                                    <div class="hint-applied-wrapper d-none">
                                                        <div class="d-flex align-items-center justify-content-between text-success">
                                                            <span style="font-size: 10px;">
                                                                <i class="mdi mdi-check-circle-outline me-1"></i>Exc. PPN: <strong class="applied-dpp-text">Rp 0</strong> <span class="text-muted applied-dpp-calc" style="font-size: 9.5px;">(DPP: Rp 0)</span>
                                                            </span>
                                                            <button type="button" class="btn btn-xs btn-link text-danger p-0 ms-1 btn-reset-dpp" style="font-size: 10px; text-decoration: underline; line-height: 1;">
                                                                Batal
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-1 col-12 mb-md-0 mb-3">
                                                <p class="mb-2 repeater-title small text-muted">Qty</p>
                                                <input type="number" class="form-control form-control-sm invoice-item-qty"
                                                    placeholder="Min 1" name="qty[]" id="qty-{{ $rno }}" data-id="{{ $rno }}"
                                                    data-pr-remaining="{{ $pi['pr_remaining'] ?? '' }}"
                                                    min="1" value="{{ $pi['qty'] }}">
                                                <small class="text-info d-block mt-1 qty-stock-hint {{ ($pi['pr_remaining'] ?? null) !== null && $pi['qty'] > $pi['pr_remaining'] ? '' : 'd-none' }}"
                                                    id="qty-stock-hint-{{ $rno }}">
                                                    <i class="mdi mdi-information-outline"></i>
                                                    {{ $pi['pr_remaining'] ?? 0 }} pcs utk PR, +{{ max(0, $pi['qty'] - ($pi['pr_remaining'] ?? 0)) }} stok
                                                </small>
                                            </div>
                                            <div class="col-md-1 col-12 mb-md-0 mb-3">
                                                <p class="mb-2 repeater-title small text-muted">Info Qty</p>
                                                <select class="form-select form-select-sm invoice-item-info select2-info-qty" id="info-qty-{{ $rno }}"
                                                    data-id="{{ $rno }}" aria-label="Default select example" name="info_qty[]">
                                                    <option disabled value="">---Info---</option>
                                                    @foreach ($unitList as $uOpt)
                                                        <option value="{{ $uOpt }}" {{ $uOpt === 'Pcs' ? 'selected' : '' }}>{{ $uOpt }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-1 col-12 mb-md-0 mb-3">
                                                <p class="mb-2 repeater-title small text-muted">Disc (%)</p>
                                                <div class="input-group input-group-sm" data-disc="{{ $rno }}">
                                                    <input type="text" class="form-control invoice-item-disc"
                                                        id="disc-{{ $rno }}" data-id="{{ $rno }}" name="disc[]" placeholder="%"
                                                        value="0">
                                                </div>
                                            </div>
                                            <div class="col-md-2 col-12 pe-4 text-md-end">
                                                <p class="mb-2 repeater-title small text-muted">Amount</p>
                                                <p class="mb-0 amount-label fw-semibold text-primary" id="amount-label-{{ $rno }}" data-id="{{ $rno }}"></p>
                                                <input type="number" class="form-control invoice-item-amount"
                                                    name="amount[]" id="amount-{{ $rno }}" data-id="{{ $rno }}"
                                                    value="" hidden>
                                            </div>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-icon btn-label-danger btn-del position-absolute top-0 end-0 m-2"
                                            data-repeater-delete="">
                                            <i class="mdi mdi-delete-outline"></i>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="mb-0" data-repeater-list="group-a">
                            <div class="repeater-wrapper" data-repeater-item="">
                                <div class="position-relative border-bottom p-3">
                                    <div class="row w-100">
                                        <input type="hidden" class="invoice-item-detail-id" name="detail_id[]" value="">
                                        <input type="hidden" name="pr_detail_id[]" value="">
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
                                            <input type="hidden" class="item-category-value" name="item_category[]" value="Sparepart">
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
                                                <select class="form-select form-select-sm mt-1 select-kondisi-unit" name="kondisi[]">
                                                    <option value="Baru" selected>Unit Baru (masuk stok jual)</option>
                                                    <option value="Second">Unit Second (jadi Fixed Asset, QC dulu)</option>
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
                                            <div class="price-tax-hint mt-1 small d-none" style="font-size: 11px; line-height: 1.35; background: #f0f2ff; padding: 5px 8px; border-radius: 6px; border-left: 3px solid #696cff;">
                                                <div class="hint-calc-wrapper">
                                                    <div class="text-secondary mb-1">
                                                        <i class="mdi mdi-calculator-variant-outline text-primary me-1"></i>Inc. PPN (11%):<br>
                                                        Harga Exc. PPN: <strong class="text-primary exc-ppn-val">Rp 0</strong> <span class="ppn-val text-muted" style="font-size: 10px;">(PPN: Rp 0)</span><br>
                                                        DPP (11/12): <strong class="text-dark dpp-val" style="font-size: 10.5px;">Rp 0</strong>
                                                    </div>
                                                    <button type="button" class="btn btn-xs btn-primary py-0 px-2 btn-apply-dpp" style="font-size: 10px; height: 22px;">
                                                        <i class="mdi mdi-check me-1"></i> Gunakan Harga Exc. PPN
                                                    </button>
                                                </div>
                                                <div class="hint-applied-wrapper d-none">
                                                    <div class="d-flex align-items-center justify-content-between text-success">
                                                        <span style="font-size: 10px;">
                                                            <i class="mdi mdi-check-circle-outline me-1"></i>Exc. PPN: <strong class="applied-dpp-text">Rp 0</strong> <span class="text-muted applied-dpp-calc" style="font-size: 9.5px;">(DPP: Rp 0)</span>
                                                        </span>
                                                        <button type="button" class="btn btn-xs btn-link text-danger p-0 ms-1 btn-reset-dpp" style="font-size: 10px; text-decoration: underline; line-height: 1;">
                                                            Batal
                                                        </button>
                                                    </div>
                                                </div>
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
                                            <select class="form-select form-select-sm invoice-item-info select2-info-qty" id="info-qty-1"
                                                data-id="1" aria-label="Default select example" name="info_qty[]">
                                                <option disabled value="">---Info---</option>
                                                @foreach ($unitList as $uOpt)
                                                    <option value="{{ $uOpt }}" {{ $uOpt === 'Pcs' ? 'selected' : '' }}>{{ $uOpt }}</option>
                                                @endforeach
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
                                        <span class="text-muted small">Tax (PPN 11%)</span>
                                        <div class="form-check form-switch mb-0">
                                            <input class="form-check-input" type="checkbox" role="switch"
                                                id="taxSwitch" {{ (@$purchase->vat == '12' || @$purchase->vat == '11') ? 'checked' : '' }}>
                                        </div>
                                    </div>
                                    <span class="fw-semibold tax-amount-label text-muted small" id="taxAmountLabel">
                                        @if (@$purchase && ($purchase->vat == '12' || $purchase->vat == '11'))
                                            {{ 'RP ' . number_format(($purchase->subtotal - $purchase->diskon) * $purchase->vat / 100, 0, '', '.') }}
                                        @endif
                                    </span>
                                    <input type="hidden" id="tax" name="tax" value="{{ old('tax', @$purchase->vat ?? '0') }}">
                                </div>

                                <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                                    <span class="text-muted small">Delivery Cost</span>
                                    <div style="width: 160px;">
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text">Rp</span>
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

    <div class="modal fade" id="editSupplierModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Supplier</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="editSupplierAlert" class="alert alert-danger d-none"></div>
                    <input type="hidden" id="esId">
                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Supplier</label>
                            <input type="text" class="form-control" id="esSupplier">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Info</label>
                            <select class="form-select" id="esInfo">
                                <option value="Lokal">Lokal</option>
                                <option value="Import">Import</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Code</label>
                            <input type="text" class="form-control" id="esCode">
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="text" class="form-control" id="esEmail">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phone</label>
                            <input type="text" class="form-control" id="esPhone">
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">NPWP</label>
                            <input type="text" class="form-control" id="esNpwp">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Area</label>
                            <input type="text" class="form-control" id="esArea">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Address</label>
                        <textarea class="form-control" id="esAddress" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-primary" id="esSaveBtn">
                        <i class="mdi mdi-content-save-outline me-1"></i> Simpan Supplier
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

    <div class="modal fade" id="quickAddPicModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="quickAddPicModalTitle">Tambah PIC Supplier</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="qpId">
                    <div id="qpError" class="alert alert-danger d-none"></div>
                    <div class="mb-3">
                        <label class="form-label">Nama PIC <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="qpName" placeholder="Nama PIC">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Position</label>
                        <input type="text" class="form-control" id="qpPosition" placeholder="Contoh: Sales">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phone</label>
                        <input type="text" class="form-control" id="qpPhone" placeholder="08xxxxxxxxxx">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="text" class="form-control" id="qpEmail" placeholder="xxxx@xxx.xx">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="saveQuickAddPic">
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

            // Auto-fill Mobile & Address dari data Supplier terpilih
            $supplierDropdown.on('change', function() {
                var $selected = $(this).find(':selected');
                var phone = realValue($selected.data('phone'));
                var address = realValue($selected.data('address'));
                // Selalu di-set (bukan cuma kalau ada isinya) — sama seperti address di
                // bawah. Kalau pakai "if (phone)", giliran supplier baru gak punya nomor
                // telepon tercatat, Mobile-nya nyangkut ke punya supplier sebelumnya.
                $('#mobile').val(phone);
                $('#address').val(address);
                toggleEditSupplierBtn();
                // keepValue dipaksa kosong ('') di sini — kalau dibiarkan undefined,
                // loadAttnOptions() bakal balik baca $attnDropdown.val() yang isinya
                // masih PIC supplier LAMA (belum ke-reset), jadi nyangkut kepilih terus
                // walau supplier sudah ganti dan PIC itu bukan miliknya. Dengan '' di
                // sini, PIC pertama supplier BARU yang otomatis kepilih (autoSelectFirst).
                loadAttnOptions($(this).val(), '', true);
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

            function loadAttnOptions(supplierId, keepValue, autoSelectFirst) {
                var currentValue = keepValue !== undefined ? keepValue : $attnDropdown.val();
                $attnDropdown.empty();
                // Opsi kosong di awal, supaya kalau tidak ada PIC yang cocok dgn currentValue,
                // browser tidak otomatis memilih PIC pertama di list (yang bisa menimpa Mobile secara tidak sengaja)
                $attnDropdown.append(new Option('', '', false, false));
                if (!supplierId) {
                    $('#attn-empty-hint').hide();
                    $attnDropdown.trigger('change');
                    return;
                }
                $.ajax({
                    url: '/supplier/' + supplierId + '/edit-data',
                    type: 'GET',
                    success: function(res) {
                        // Kalau supplier sudah diganti lagi sebelum response ini datang (klik cepat
                        // ganti-ganti supplier), response yang telat ini basi — jangan ditimpakan ke
                        // dropdown ATTN supplier yang sekarang aktif (race condition, bukan cache).
                        if (String($supplierDropdown.val() || '') !== String(supplierId)) {
                            return;
                        }
                        var pics = res.pics || [];
                        // Kalau belum ada PIC yang dipertahankan (mis. supplier baru dipilih), otomatis
                        // pilih PIC pertama (terbaru) supaya user tidak perlu pilih manual tiap kali.
                        var autoPickName = (!currentValue && autoSelectFirst && pics.length) ? pics[0].name_pic : null;
                        pics.forEach(function(pic) {
                            $attnDropdown.append(makePicOption(pic, pic.name_pic === currentValue || pic.name_pic === autoPickName));
                        });
                        if (currentValue && !pics.some(function(p) { return p.name_pic === currentValue; })) {
                            var keepOption = new Option(currentValue, currentValue, true, true);
                            $attnDropdown.append(keepOption);
                        }
                        $('#attn-empty-hint').toggle(!pics.length);
                        $attnDropdown.trigger('change');
                    }
                });
            }

            // Auto-fill Mobile & toggle tombol Edit PIC saat memilih PIC dari dropdown ATTN.
            // Cuma timpa Mobile kalau PIC yang kepilih itu punya nomor telepon sendiri yang
            // beneran keisi — kalau PIC-nya gak punya nomor tercatat (banyak data lama yang
            // kosong), biarkan nomor telepon umum milik supplier (di-set oleh handler
            // ganti-supplier barusan) yang tetap tampil, jangan malah ditimpa jadi kosong.
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

            // Muat ulang ATTN saat halaman dibuka dengan supplier sudah terpilih (mode Edit PO)
            if ($supplierDropdown.val()) {
                loadAttnOptions($supplierDropdown.val(), '{{ old('attn', @$purchase->attn ?? '') }}');
            }

            function resetPicModal() {
                $('#qpError').addClass('d-none').text('');
                $('#qpId').val('');
                $('#qpName').val('');
                $('#qpPosition').val('');
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
                $('#qpPosition').val($selected.data('position') || '');
                $('#qpPhone').val($selected.data('phone') || '');
                $('#qpEmail').val($selected.data('email') || '');
                $('#quickAddPicModalTitle').text('Edit PIC Supplier');
                $('#quickAddPicModal').modal('show');
            });

            $('#saveQuickAddPic').on('click', function() {
                var supplierId = $supplierDropdown.val();
                var picId = $('#qpId').val();
                var name = $('#qpName').val().trim();
                if (!name) {
                    $('#qpError').removeClass('d-none').text('Nama PIC wajib diisi.');
                    return;
                }

                var payload = {
                    _token: '{{ csrf_token() }}',
                    namePic: name,
                    position: $('#qpPosition').val(),
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
                            $('#qpError').removeClass('d-none').text('Gagal menyimpan PIC.');
                        }
                    },
                    error: function(xhr) {
                        var msg = xhr.responseJSON && xhr.responseJSON.message ?
                            xhr.responseJSON.message : 'Gagal menyimpan, coba lagi.';
                        $('#qpError').removeClass('d-none').text(msg);
                    }
                });
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

            // Edit Supplier (AJAX, tanpa reload halaman)
            $('#editSupplierModal').on('show.bs.modal', function() {
                var id = $supplierDropdown.val();
                $('#editSupplierAlert').addClass('d-none').text('');
                if (!id) {
                    $('#editSupplierAlert').removeClass('d-none').text('Pilih supplier terlebih dahulu.');
                    return;
                }
                $('#esId').val(id);

                $.ajax({
                    url: '/supplier/' + id + '/edit-data',
                    type: 'GET',
                    success: function(res) {
                        var s = res.supplier;
                        $('#esSupplier').val(s.supplier);
                        $('#esInfo').val(s.info);
                        $('#esCode').val(s.code);
                        $('#esEmail').val(s.email);
                        $('#esPhone').val(s.phone);
                        $('#esNpwp').val(s.npwp);
                        $('#esArea').val(s.area);
                        $('#esAddress').val(s.address);
                    },
                    error: function() {
                        $('#editSupplierAlert').removeClass('d-none').text('Gagal memuat data supplier.');
                    }
                });
            });

            // Simpan perubahan info Supplier
            $('#esSaveBtn').on('click', function() {
                var id = $('#esId').val();
                if (!id) return;
                var $btn = $(this).prop('disabled', true);

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
                            $('#editSupplierAlert').removeClass('d-none').text('Gagal menyimpan perubahan.');
                        }
                    },
                    error: function() {
                        $('#editSupplierAlert').removeClass('d-none').text('Gagal menyimpan perubahan.');
                    },
                    complete: function() {
                        $btn.prop('disabled', false);
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

            // Live calculation helper: Hitung Harga Exc. PPN & DPP Nilai Lain jika harga yang diinput adalah include PPN (11%)
            function updatePriceTaxHint($input) {
                var $col = $input.closest('.col-md-3');
                var $hint = $col.find('.price-tax-hint');
                if (!$hint.length) return;

                // Jika baris ini sudah dalam state "DPP diterapkan", jangan hitung ulang dari nilai DPP
                if ($col.data('dpp-applied')) {
                    return;
                }

                var inputVal = $input.val();
                var nomorInt = parseFloat((inputVal || '').replace(/[.,]/g, '')) || 0;

                if (nomorInt > 0) {
                    var hargaExcPpn = Math.round((nomorInt * 100) / 111);
                    var ppn = nomorInt - hargaExcPpn;
                    var dpp = Math.round((hargaExcPpn * 11) / 12);

                    $hint.find('.exc-ppn-val').text('Rp ' + formatNumber(String(hargaExcPpn)));
                    $hint.find('.ppn-val').text('(PPN: Rp ' + formatNumber(String(ppn)) + ')');
                    $hint.find('.dpp-val').text('Rp ' + formatNumber(String(dpp)));
                    $hint.find('.btn-apply-dpp').data('exc-ppn', hargaExcPpn).data('dpp', dpp).data('orig-price', nomorInt);
                    $hint.find('.hint-calc-wrapper').removeClass('d-none');
                    $hint.find('.hint-applied-wrapper').addClass('d-none');
                    $hint.removeClass('d-none');
                } else {
                    $hint.addClass('d-none');
                }
            }

            $(document).on('keyup input', '.invoice-item-price-label', function() {
                var input = $(this);
                var $col = input.closest('.col-md-3');
                var $row = input.closest('.repeater-wrapper');

                // User mengetik manual -> reset state DPP applied pada baris ini
                $col.data('dpp-applied', false);

                var input_val = input.val();
                var original_len = input_val.length;
                var caret_pos = input.prop("selectionStart") || 0;

                input_val = formatNumber(input_val);
                input.val(input_val);

                var nomorInt = parseFloat(input_val.replace(/[.,]/g, '')) || 0;
                $row.find('.invoice-item-price').val(nomorInt);

                var updated_len = input_val.length;
                caret_pos = updated_len - original_len + caret_pos;
                if (input[0] && input[0].setSelectionRange) {
                    input[0].setSelectionRange(caret_pos, caret_pos);
                }

                updatePriceTaxHint(input);
                recalculateTotals();
            });

            // Handler tombol "Gunakan Harga Exc. PPN"
            $(document).on('click', '.btn-apply-dpp', function(e) {
                e.preventDefault();
                e.stopPropagation();

                var $col = $(this).closest('.col-md-3');
                var $row = $(this).closest('.repeater-wrapper');
                var excPpn = $(this).data('exc-ppn');
                var dpp = $(this).data('dpp');
                var origPrice = $(this).data('orig-price');
                var $priceLabel = $col.find('.invoice-item-price-label');
                var $priceHidden = $row.find('.invoice-item-price');
                var $hint = $col.find('.price-tax-hint');

                if (excPpn && excPpn > 0) {
                    $col.data('dpp-applied', true);
                    $col.data('orig-price', origPrice);
                    $col.data('exc-ppn-val', excPpn);
                    $col.data('dpp-val', dpp);

                    $priceLabel.val(formatNumber(String(excPpn)));
                    $priceHidden.val(excPpn);

                    // Switch ke status diterapkan
                    $hint.find('.applied-dpp-text').text('Rp ' + formatNumber(String(excPpn)));
                    $hint.find('.applied-dpp-calc').text('(DPP: Rp ' + formatNumber(String(dpp)) + ')');
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

                var $col = $(this).closest('.col-md-3');
                var $row = $(this).closest('.repeater-wrapper');
                var origPrice = $col.data('orig-price');
                var $priceLabel = $col.find('.invoice-item-price-label');
                var $priceHidden = $row.find('.invoice-item-price');

                if (origPrice && origPrice > 0) {
                    $col.data('dpp-applied', false);
                    $priceLabel.val(formatNumber(String(origPrice)));
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

            // Unified calculation function for row amounts, subtotal, discount, tax, delivery, and grand total
            function recalculateTotals() {
                var sTotal = 0;
                $('.repeater-wrapper').each(function() {
                    var $row = $(this);
                    var harga = parseFloat($row.find('.invoice-item-price').val()) || 0;
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
                var taxAmount = Math.round(dTotal * taxPercent / 100);
                var hTotal = Math.round(dTotal + taxAmount + deliveryCost);

                $('#taxAmountLabel').html(taxPercent > 0 ? formatter.format(taxAmount) : '');
                $('#hargaTotal').val(hTotal);
                $('#hargaTotalLabel').html(formatter.format(hTotal));
                $('#totalNoTax').val(dTotal);
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
                recalculateTotals();
            });

            $(document).on('repeater:deleted', function() {
                updateItemsCountBadge();
                recalculateTotals();
            });

            $(document).on('click', '[data-repeater-delete]', function() {
                setTimeout(function() {
                    updateItemsCountBadge();
                    recalculateTotals();
                }, 50);
            });

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
