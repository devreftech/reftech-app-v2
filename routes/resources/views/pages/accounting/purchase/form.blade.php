    @extends('layouts.sales.app')
    @section('title', 'Create Purchase Order')
    @section('content')
        <form id="formAuthentication" class="mb-3 fv-plugins-bootstrap5 fv-plugins-framework"
            action="{{ @$purchase ? route('purchase.update', $purchase->id) : route('purchase.store') }}" method="post"
            enctype="multipart/form-data">
            @csrf
            @if (@$purchase)
                @method('patch')
            @endif
            <div class="form-floating mb-3">
                <input type="text" class="form-control fw-bold fs-3" id="floatingInputFilled"
                    aria-describedby="floatingInputFilledHelp" name="no_po" value="{{ old('no_po', @$purchase->no_po) }}">
                <label for="floatingInputFilled">No PO</label>
                <span class="form-floating-focused"></span>
            </div>
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <div class="card">
                <div class="card-body">
                    <div class="form-invoice-repeater source-item">
                        <div class="row mb-3">
                            <div class="col-12 col-lg-6">
                                <div class="form-floating form-floating-outline mb-2">
                                    <select id="supplier-dropdown" class="select2 form-select invoice-item-supplier"
                                        data-allow-clear="true" name="supplier" data-id="1"
                                        {{ Auth::user()->role == 'Logistic' ? 'disabled' : '' }}>
                                        <option>Pilih Supplier...</option>
                                        @foreach ($suppliers as $supp)
                                            <option value="{{ $supp->id }}" data-info="{{ $supp->info }}"
                                                {{ @$purchase->id_supplier == $supp->id ? 'selected' : '' }}>
                                                {{ $supp->supplier }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <label for="supplier-dropdown">Supplier</label>
                                </div>
                            </div>
                            <div class="col-12 col-lg-2">
                                <div class="form-floating form-floating-outline">
                                    <input class="form-control" type="text" placeholder="Put Mobile Here ...."
                                        id="mobile" name="mobile" value="{{ old('mobile', @$purchase->mobile ?? '') }}">
                                    <label for="mobile">Mobile</label>
                                </div>
                            </div>
                            <div class="col-6 col-lg-2">
                                <div class="form-floating form-floating-outline">
                                    <input class="form-control" type="text" placeholder="Put Delivery Time Here ...."
                                        id="delivery" name="delivery"
                                        value="{{ old('delivery', @$purchase->delivery ?? '') }}">
                                    <label for="delivery">Delivery Time</label>
                                </div>
                            </div>
                            <div class="col-6 col-lg-2">
                                <div class="form-floating form-floating-outline">
                                    <input class="form-control" type="date" id="date" name="date"
                                        value="{{ old('date', @$quotation->date ?? \Carbon\Carbon::today()->format('Y-m-d')) }}">
                                    <label for="date">Date</label>
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <button type="button" class="btn btn-primary waves-effect waves-light"
                                    data-bs-toggle="modal" data-bs-target="#createSupplier"
                                    {{ Auth::user()->role == 'Logistic' ? 'disabled' : '' }}>
                                    + Supplier
                                </button>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-12 col-lg-6">
                                <div class="form-floating form-floating-outline mb-2">
                                    <input class="form-control" type="text" placeholder="Put ATTN Quotation Here ...."
                                        id="attn" name="attn" value="{{ old('attn', @$purchase->attn ?? '') }}">
                                    <label for="pic-dropdown">ATTN</label>
                                </div>
                            </div>
                            <div class="col-12 col-lg-6">
                                <div class="form-floating form-floating-outline">
                                    <input class="form-control" type="text" placeholder="text Payment Here ...."
                                        id="payment" name="payment"
                                        value="{{ old('payment', @$purchase->payment ?? '') }}">
                                    <label for="select2Basic">Payment</label>
                                </div>
                            </div>
                        </div>
                        @if (@$purchase)
                            <div class="mb-2" data-repeater-list="group-a">
                                @php
                                    $no = 1;
                                @endphp
                                @foreach ($dPurchase as $item)
                                    <div class="repeater-wrapper pt-0 pt-md-4" data-repeater-item="">
                                        <div class="d-flex border rounded position-relative pe-0">
                                            <div class="row w-100 p-3">
                                                <div class="col-md col-12 mb-md-0">
                                                    <label for="product" class="mb-2">Product</label>
                                                    <textarea class="form-control invoice-item-detail-product" rows="{{ $no }}"
                                                        id="detailProduct-{{ $no }}" placeholder="Detail Product. Example: Kaeser ASD" name="product[]">{{ $item->product }}</textarea>
                                                </div>
                                                <div class="col-md-3 col-12 mb-md-0 mb-3">
                                                    <p class="mb-2 repeater-title">Price</p>
                                                    <div class="input-group mb-3" data-price="{{ $no }}">
                                                        <span class="input-group-text">Rp. </span>
                                                        <input type="text" class="form-control invoice-item-price-label"
                                                            id="priceLabel-{{ $no }}"
                                                            data-id="{{ $no }}" name="harga"
                                                            placeholder="Put Price Here" data-type="currency"
                                                            min="0" pattern="^[0-9]\d{0,2}(\.\d{3})*$"
                                                            @focus="focused = true" @blur="focused = false"
                                                            value="{{ number_format($item->price, '0', ',', '.') }}">
                                                        <input class="form-control invoice-item-price" type="number"
                                                            name="price[]" id="price-{{ $no }}"
                                                            value="{{ old('price[]', $item->price) }}" hidden>
                                                    </div>
                                                </div>
                                                <div class="col-md-1 col-12 mb-md-0 mb-3">
                                                    <p class="mb-2 repeater-title">Qty</p>
                                                    <input type="number" class="form-control mb-4 invoice-item-qty"
                                                        placeholder="Min 1" name="qty[]" id="qty-{{ $no }}"
                                                        data-id="{{ $no }}" min="1"
                                                        value="{{ $item->qty }}">
                                                </div>
                                                <div class="col-md-1 col-12 mb-md-0 mb-3">
                                                    <p class="mb-2 repeater-title">Info Qty</p>
                                                    <div class="form-floating form-floating-outline mb-4">
                                                        <select class="form-select invoice-item-info"
                                                            id="info-qty-{{ $no }}"
                                                            data-id="{{ $no }}"
                                                            aria-label="Default select example" name="info_qty[]">
                                                            <option disabled>---Info---</option>
                                                            <option value="Pcs"
                                                                {{ $item->info_qty == 'Pcs' ? 'selected' : '' }}>Pcs
                                                            </option>
                                                            <option value="Set"
                                                                {{ $item->info_qty == 'Set' ? 'selected' : '' }}>Set
                                                            </option>
                                                            <option value="Pail"
                                                                {{ $item->info_qty == 'Pail' ? 'selected' : '' }}>Pail
                                                            </option>
                                                            <option value="Unit"
                                                                {{ $item->info_qty == 'Unit' ? 'selected' : '' }}>Unit
                                                            </option>
                                                            <option value="Lot"
                                                                {{ $item->info_qty == 'Lot' ? 'selected' : '' }}>Lot
                                                            </option>
                                                            <option value="Meter"
                                                                {{ $item->info_qty == 'Meter' ? 'selected' : '' }}>Meter
                                                            </option>
                                                            <option value="Can"
                                                                {{ $item->info_qty == 'Can' ? 'selected' : '' }}>Can
                                                            </option>
                                                            <option value="Hari"
                                                                {{ $item->info_qty == 'Hari' ? 'selected' : '' }}>Hari
                                                            </option>
                                                            <option value="Bulan"
                                                                {{ $item->info_qty == 'Bulan' ? 'selected' : '' }}>Bulan
                                                            </option>
                                                            <option value="Kg"
                                                                {{ $item->info_qty == 'Kg' ? 'selected' : '' }}>Kg</option>
                                                            <option value="Tube"
                                                                {{ $item->info_qty == 'Tube' ? 'selected' : '' }}>Tube
                                                            </option>
                                                            <option value="Titik"
                                                                {{ $item->info_qty == 'Titik' ? 'selected' : '' }}>Titik
                                                            </option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-1 col-12 mb-md-0 mb-3">
                                                    <p class="mb-2 repeater-title">Disc (%)</p>
                                                    <div class="input-group mb-3" data-disc="{{$no}}">
                                                        <input type="text" class="form-control invoice-item-disc"
                                                            id="disc-{{ $no }}" data-id="{{ $no }}"
                                                            name="disc[]" placeholder="%"
                                                            value="{{ old('disc[]', $item->disc) }}">
                                                    </div>
                                                </div>
                                                <div class="col-md-1 col-12 pe-0">
                                                    <p class="mb-2 repeater-title">Amount</p>
                                                    <p class="mb-0 amount-label" id="amount-label-{{$no}}" data-id="{{$no}}">
                                                        {{ number_format($item->amount, 0, ',', '.') }}</p>
                                                    <input type="number" class="form-control invoice-item-amount"
                                                        name="amount[]" id="amount-{{ $no }}"
                                                        data-id="{{ $no }}"
                                                        value="{{ old('amount[]', $item->amount) }}" hidden>
                                                </div>
                                            </div>
                                            <div
                                                class="d-flex flex-column align-items-center justify-content-between border-start p-2">
                                                <i class="mdi mdi-close cursor-pointer bg-danger text-white btn-del"
                                                    data-repeater-delete=""></i>
                                            </div>
                                        </div>
                                    </div>
                                    @php
                                        $no++;
                                    @endphp
                                @endforeach
                            </div>
                        @else
                            <div class="mb-2" data-repeater-list="group-a">
                                <div class="repeater-wrapper pt-0 pt-md-4" data-repeater-item="">
                                    <div class="d-flex border rounded position-relative pe-0">
                                        <div class="row w-100 p-3">
                                            <div class="col-md col-12 mb-md-0">
                                                <label for="product" class="mb-2">Product</label>
                                                <textarea class="form-control invoice-item-detail-product" rows="2" id="detailProduct-1"
                                                    placeholder="Detail Product. Example: Kaeser ASD" name="product[]"></textarea>
                                            </div>
                                            <div class="col-md-3 col-12 mb-md-0 mb-3">
                                                <p class="mb-2 repeater-title">Price</p>
                                                <div class="input-group mb-3" data-price="1">
                                                    <span class="input-group-text">Rp. </span>
                                                    <input type="text" class="form-control invoice-item-price-label"
                                                        id="priceLabel-1" data-id="1" name="harga"
                                                        placeholder="Put Price Here" data-type="currency" min="0"
                                                        pattern="^[0-9]\d{0,2}(\.\d{3})*$" @focus="focused = true"
                                                        @blur="focused = false" value="{{ old('price[]') }}">
                                                    <input class="form-control invoice-item-price" type="number"
                                                        name="price[]" id="price-1" value="{{ old('price[]') }}"
                                                        hidden>
                                                </div>
                                            </div>
                                            <div class="col-md-1 col-12 mb-md-0 mb-3">
                                                <p class="mb-2 repeater-title">Qty</p>
                                                <input type="number" class="form-control mb-4 invoice-item-qty"
                                                    placeholder="Min 1" name="qty[]" id="qty-1" data-id="1"
                                                    min="1" value="{{ old('qty[]') }}">
                                            </div>
                                            <div class="col-md-1 col-12 mb-md-0 mb-3">
                                                <p class="mb-2 repeater-title">Info Qty</p>
                                                <div class="form-floating form-floating-outline mb-4">
                                                    <select class="form-select invoice-item-info" id="info-qty-1"
                                                        data-id="1" aria-label="Default select example"
                                                        name="info_qty[]">
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
                                            </div>
                                            <div class="col-md-1 col-12 mb-md-0 mb-3">
                                                <p class="mb-2 repeater-title">Disc (%)</p>
                                                <div class="input-group mb-3" data-disc="1">
                                                    <input type="text" class="form-control invoice-item-disc"
                                                        id="disc-1" data-id="1" name="disc[]" placeholder="%"
                                                        value="{{ old('disc[]', 0) }}">
                                                </div>
                                            </div>
                                            <div class="col-md-1 col-12 pe-0">
                                                <p class="mb-2 repeater-title">Amount</p>
                                                <p class="mb-0 amount-label" id="amount-label-1" data-id="1">
                                                    {{ old(strval('amount[]')) }}</p>
                                                <input type="number" class="form-control invoice-item-amount"
                                                    name="amount[]" id="amount-1" data-id="1"
                                                    value="{{ old('amount[]') }}" hidden>
                                            </div>
                                        </div>
                                        <div
                                            class="d-flex flex-column align-items-center justify-content-between border-start p-2">
                                            <i class="mdi mdi-close cursor-pointer bg-danger text-white btn-del"
                                                data-repeater-delete=""></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                        <div class="row">
                            <div class="col-12 mb-2">
                                <button type="button" class="btn btn-sm btn-primary waves-effect waves-light btn-add"
                                    data-repeater-create="">
                                    <i class="mdi mdi-plus me-1"></i> Add Item
                                </button>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-6 col-12">
                                <h5 class="my-4">
                                    Note :
                                </h5>
                                <div class="row mb-3">
                                    <label class="col-sm-4 col-form-label" for="note">Note</label>
                                    <div class="col-sm-8">
                                        <textarea class="form-control h-px-100" rows="2" placeholder="Write your note here...." name="note">{{ @$purchase->note }}</textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-2"></div>
                            <div class="col-lg-4">
                                <div class="card shadow-none bg-light text-secondary border border-secondary mt-5 mb-3">
                                    <div class="card-body ">
                                        <div class="row">
                                            <label class="col-sm-4 col-form-label text-sm-start"
                                                for="collapsible-pincode">Sub
                                                Total :</label>
                                            <div class="col-sm-8">
                                                {{-- @if (@$dquotation)
                                                    <input type="number" id="subtotal" class="form-control"
                                                        name="subtotal"
                                                        value="{{ old('subtotal', @$quotation->subtotal ?? '') }}">
                                                @else --}}
                                                <p class="mb-0 subtotal-label" id="subtotal-label" data-id="1">
                                                    {{ old('subtotal', @$purchase->subtotal ? 'RP ' . number_format(@$purchase->subtotal, 0, '', '.') : '') }}
                                                </p>
                                                <input type="number" id="subtotal" class="form-control"
                                                    name="subtotal"
                                                    value="{{ old('subtotal', @$purchase->subtotal ?? '') }}" hidden>
                                                {{-- @endif --}}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card shadow-none bg-light text-secondary border border-secondary mb-3">
                                    <div class="card-body">
                                        <div class="row">
                                            <label class="col-sm-4 col-form-label text-sm-start" for="collapsible-tax">Tax
                                                :</label>
                                            <div class="col-sm-8">
                                                <select id="tax" class="form-select form-select-lg"
                                                    style="background: none; border: none;" name="tax">
                                                    <option disabled>-----Select Tax-----</option>
                                                    <option value="0" {{ @$purchase->vat == '0' ? 'selected' : '' }}>
                                                        Without Tax</option>
                                                    <option value="11"
                                                        {{ @$purchase->vat == '11' ? 'selected' : '' }}>
                                                        <span> With PPN <small class="text-muted"> 12%</small> </span>
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card shadow-none bg-light text-secondary border border-secondary mb-3">
                                    <div class="card-body ">
                                        <div class="row">
                                            <label class="col-sm-4 col-form-label text-sm-start"
                                                for="collapsible-pincode">Discount :</label>
                                            <div class="col-sm-8">
                                                <div class="input-group">
                                                    <span class="input-group-text"
                                                        style="background: none; border: none;">Rp.
                                                    </span>
                                                    <input type="text" id="diskon-label" class="form-control"
                                                        placeholder="Discount Here....." data-type="currency"
                                                        style="background: none; border: none;"
                                                        pattern="^[0-9]\d{0,2}(\.\d{3})*$"
                                                        value="{{ old('diskon', @$purchase->diskon ? number_format(@$purchase->diskon, 0, '', '.') : '0') }}">
                                                    <input type="number" name="diskon" id="diskon"
                                                        value="{{ old('diskon', @$purchase->diskon ?? '0') }}" hidden>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card shadow-none bg-light text-secondary border border-secondary mb-3">
                                    <div class="card-body ">
                                        <div class="row">
                                            <label class="col-sm-4 col-form-label text-sm-start"
                                                for="collapsible-pincode">Total
                                                :</label>
                                            <div class="col-sm-8">
                                                <p class="mb-0 harga-total-label" id="hargaTotalLabel" data-id="1">
                                                    {{ old('harga_total', @$purchase->total ? 'RP ' . number_format(@$purchase->total, 0, '', '.') : '') }}
                                                </p>
                                                <input type="number" id="hargaTotal" class="form-control"
                                                    name="harga_total"
                                                    value="{{ old('harga_total', @$purchase->total ?? '') }}" hidden>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="float-end">
                                    <a href="{{ route('quotation.index') }}" type="button"
                                        class="btn btn-lg btn-outline-secondary">
                                        Back
                                    </a>
                                    <button :disabled="focused" type="submit" class="btn btn-lg btn-primary">
                                        Save
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        @include('components.modal.warehouse.supplier.form')
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
        <script src="{{ asset('assets') }}/includes/validator/quotation-validation.js"></script>
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

                function initializeSelect2Product() {
                    $('.invoice-item-product').select2({
                        placeholder: ' ---- Choose Part Number Here ---- ',
                        allowClear: true,
                        width: '100%',
                    });
                }
                $(document).ready(function() {
                    // Panggil fungsi inisialisasi saat halaman dimuat
                    initializeSelect2Product();

                    // Jika ada elemen dinamis yang ditambahkan, gunakan event listener
                    $(document).on('repeater:added', function() {
                        initializeSelect2Product();
                    });
                });

                function initFormValidation() {
                    const fv = FormValidation.formValidation(formAuthentication, {
                        fields: {
                            title: {
                                validators: {
                                    notEmpty: {
                                        message: "Please enter title",
                                    },
                                    stringLength: {
                                        min: 6,
                                        message: "Name must be more than 6 characters",
                                    },
                                },
                            },
                            "detail_product[]": {
                                selector: '[name="detail_product[]"]',
                                validators: {
                                    notEmpty: {
                                        message: "Please enter detail product",
                                    },
                                    stringLength: {
                                        min: 3,
                                        message: "Area must be more than 3 characters (detail product)",
                                    },
                                },
                            },
                            harga: {
                                validators: {
                                    notEmpty: {
                                        message: "Please enter price",
                                    },
                                    numericInput: {
                                        number: "Please enter a valid number.",
                                    },
                                },
                            },
                            "qty[]": {
                                validators: {
                                    notEmpty: {
                                        message: "Please enter Quantity",
                                    },
                                    numericInput: {
                                        number: "Please enter a valid number.",
                                    },
                                },
                            },
                        },
                        plugins: {
                            trigger: new FormValidation.plugins.Trigger(),
                            bootstrap5: new FormValidation.plugins.Bootstrap5({
                                eleValidClass: "",
                                rowSelector: ".mb-3",
                            }),
                            submitButton: new FormValidation.plugins.SubmitButton(),

                            defaultSubmit: new FormValidation.plugins.DefaultSubmit(),
                            autoFocus: new FormValidation.plugins.AutoFocus(),
                        },
                        init: (instance) => {
                            instance.on("plugins.message.placed", function(e) {
                                if (
                                    e.element.parentElement.classList.contains(
                                        "input-group"
                                    )
                                ) {
                                    e.element.parentElement.insertAdjacentElement(
                                        "afterend",
                                        e.messageElement
                                    );
                                }
                            });
                        },
                    });
                }

                // Jquery Dependency
                // formatting  shipping
                $("#shipping-label").on({
                    keyup: function() {
                        formatCurrencyShipping($(this));
                    }
                });
                // Formatting Discount Quotation
                $("#diskon-label").on({
                    keyup: function() {
                        formatCurrencyDiscount($(this));
                    }
                });

                function initializeSelect2Address() {
                    $('.invoice-item-destination').select2({
                        placeholder: ' ---- Choose Destination Here ---- ',
                        allowClear: true,
                        width: '100%',
                    });
                }

                function initializeSelect2PIC() {
                    $('.invoice-item-pic').select2({
                        placeholder: ' ---- Choose PIC Here ---- ',
                        allowClear: true,
                        width: '100%',
                    });
                }

                function formatNumber(n) {
                    return n.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ".")
                }

                function formatCurrencyShipping(input) {
                    var input_val = input.val();

                    // don't validate empty input
                    if (input_val === "") {
                        return;
                    }

                    // original length
                    var original_len = input_val.length;

                    // add commas to number
                    // remove all non-digits
                    input_val = formatNumber(input_val);
                    input_val = input_val;

                    // send updated string to input
                    input.val(input_val);
                    var nomorInt = parseFloat(input_val.replace(/[.,]/g, ''));
                    $('#shipping').val(nomorInt);
                }

                function formatCurrencyDiscount(input) {
                    var input_val = input.val();

                    // don't validate empty input
                    if (input_val === "") {
                        return;
                    }

                    // original length
                    var original_len = input_val.length;

                    // add commas to number
                    // remove all non-digits
                    input_val = formatNumber(input_val);
                    input_val = input_val;

                    // send updated string to input
                    input.val(input_val);
                    var nomorInt = parseFloat(input_val.replace(/[.,]/g, ''));
                    $('#diskon').val(nomorInt);
                }

                function formatPrice(num) {
                    return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                }

                $(`.invoice-item-client`).on('change', function(ev) {
                    var clientId = $(this).val();
                    console.log(clientId);

                    $.ajax({
                        url: '/quotation/client/' + clientId,
                        type: 'GET',
                        success: function(response) {
                            console.log(response);

                            // Mengosongkan dropdown detail produk
                            $(`.invoice-item-destination`).empty();
                            // Mengisi dropdown detail produk dengan hasil yang diterima
                            // $.each(response, function(key, value) {
                            $(`.invoice-item-destination`).append(
                                '<option value="' +
                                1 + '">' + response.address +
                                '</option>' +
                                '<option value="' +
                                2 + '">' + response.subAddress +
                                '</option>'
                            );
                            // Mengaktifkan dropdown detail produk
                            $(`.invoice-item-destination`).prop('disabled', false);
                        }
                    });
                    $.ajax({
                        url: '/quotation/pic/' + clientId,
                        type: 'GET',
                        success: function(response) {
                            console.log(response);

                            // Mengosongkan dropdown detail produk
                            $(`.invoice-item-pic`).empty();
                            // Mengisi dropdown detail produk dengan hasil yang diterima
                            $.each(response, function(key, value) {
                                $(`.invoice-item-pic`).append(
                                    '<option value="' +
                                    value.id + '">' + value.name_pic +
                                    '</option>'
                                );
                            });
                            // Mengaktifkan dropdown detail produk
                            $(`.invoice-item-pic`).prop('disabled', false);
                        }
                    });
                });

                $('.invoice-item-product').on('change', function(ev) {
                    var replacementId = $(this).find(':selected').data('replacement');
                    var Url = '/quotation/sparepart/' + replacementId;
                    var commodity = $(this).find(':selected').data('commodity');
                    var id = $(this).data('id');
                    // console.log('Replacement ID:', replacementId);
                    // console.log('URL:', Url);
                    // console.log('Textarea ID:', id);

                    $.ajax({
                        url: '/product-in/replacement/' + commodity,
                        type: 'GET',
                        success: function(response) {
                            // console.log('AJAX Response:', response);
                            $(`#info-stock-${id}`).text(response[0].stock);
                            $(`#info-weight-${id}`).text(response[0].weight);

                            var weightTotal = 0;

                            $('.info-weight-label').each(() => {
                                weightTotal += parseInt($(`#info-weight-${id}`).text());
                            });
                            console.log('Weight Total : ', weightTotal);
                            $(`.info-weight-total-label`).val(weightTotal + ' g');
                        }
                    });

                    $.ajax({
                        url: Url,
                        type: 'GET',
                        success: function(response) {
                            console.log('Replacement Id : ', replacementId);
                            console.log('URL: ', Url);

                            console.log('AJAX Response:', response);
                            $(`#detailProduct-${id}`).val(response[0].detail);
                            $(`#priceLabel-${id}`).val(formatPrice(response[0].price));
                            $(`#price-${id}`).val(response[0].price);
                            var sTotal = 0,
                                row = 0,
                                amount = 0,
                                hasil = 0,
                                harga = response[0].price,
                                disc = isNaN(parseInt($(`#disc-${id}`).val())) ? 0 : parseInt($(
                                    `#disc-${id}`).val()),
                                diskon = isNaN(parseInt($(`#diskon`).val())) ? 0 : parseInt($(
                                    `#diskon`).val());
                            $(`#qty-${id}`).val(1);
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

                            var noTax = 0;
                            var hTotal = 0;
                            var tax = isNaN(parseInt($('#tax').val())) ? 0 : parseInt($('#tax')
                                .val());
                            hTotal = parseInt(sTotal - diskon + (sTotal * tax / 100));
                            noTax = parseInt(sTotal - diskon)
                            $('#hargaTotalLabel').html(`${formatter.format(hTotal)}`);
                            $('#hargaTotal').val(hTotal);
                            $('#totalNoTax').val(noTax);
                        },
                        error: function(xhr, status, error) {
                            console.error('Error:', error);
                        }
                    });
                });


                $(".invoice-item-price-label").on('keyup', function() {
                    var input = $(this)
                    var id = input.data('id');
                    var input_val = input.val();

                    // original length
                    var original_len = input_val.length;

                    // add commas to number
                    // remove all non-digits
                    input_val = formatNumber(input_val);
                    input_val = input_val;

                    // send updated string to input
                    input.val(input_val);
                    var nomorInt = parseFloat(input_val.replace(/[.,]/g, ''));
                    console.log(id);
                    $(`#price-${id}`).val(nomorInt);
                });

                // Logic amount + Subtotal
                $('.invoice-item-price-label, .invoice-item-qty, .invoice-item-disc').on('keyup change click', function(
                    ev) {
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
                    console.log(valHarga);
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
                $('#subtotal, #tax, #shipping-label, #diskon-label, .invoice-item-price-label, .invoice-item-qty, .invoice-item-disc')
                    .on('keyup change',
                        () => {
                            var noTax = 0;
                            var hTotal = 0;
                            var sTotal = isNaN(parseInt($('#subtotal').val())) ? 0 : parseInt($('#subtotal').val());
                            var shipping = isNaN(parseInt($('#shipping').val())) ? 0 : parseInt($('#shipping').val());
                            var discount = isNaN(parseInt($('#diskon').val())) ? 0 : parseInt($('#diskon').val());
                            var dTotal = sTotal - discount,
                                diskon = isNaN(parseInt($(`#diskon`).val())) ? 0 : parseInt($(
                                    `#diskon`).val());
                            var tax = isNaN(parseInt($('#tax').val())) ? 0 : parseInt($('#tax').val());
                            hTotal = parseInt(dTotal + (dTotal * tax / 100) + shipping);
                            noTax = parseInt(dTotal + shipping);
                            console.log(hTotal);
                            $('#hargaTotalLabel').html(`${formatter.format(hTotal)}`);
                            $('#hargaTotal').val(hTotal);
                            $('#totalNoTax').val(noTax);
                        });
                // Logic Subtotal dan Amount Setelah Tambah Product
                $('.btn-add').on('click', () => {

                    $('.invoice-item-product').on('change', function(ev) {
                        var replacementId = $(this).find(':selected').data('replacement');
                        var Url = '/quotation/sparepart/' + replacementId;
                        var commodity = $(this).find(':selected').data('commodity');
                        var id = $(this).data('id');
                        // console.log('Replacement ID:', replacementId);
                        // console.log('URL:', Url);
                        console.log('Textarea ID:', id);

                        $.ajax({
                            url: '/product-in/replacement/' + commodity,
                            type: 'GET',
                            success: function(response) {
                                console.log('AJAX Response:', response);
                                $(`#info-stock-${id}`).text(response[0].stock);
                                $(`#info-weight-${id}`).text(response[0].weight);

                                var row = 0,
                                    weightTotal = 0;

                                $('.info-weight-label').each(() => {
                                    row++;
                                    weightTotal += parseInt($(`#info-weight-${row}`)
                                        .text());
                                });
                                console.log('Weight Total : ', weightTotal);
                                $(`.info-weight-total-label`).val(weightTotal + ' g');
                            }
                        });
                        $.ajax({
                            url: Url,
                            type: 'GET',

                            success: function(response) {
                                console.log('AJAX Response:', response);
                                $(`#detailProduct-${id}`).val(response[0].detail);
                                $(`#priceLabel-${id}`).val(formatPrice(response[0].price));
                                $(`#price-${id}`).val(response[0].price);
                                var sTotal = 0,
                                    row = 0,
                                    amount = 0,
                                    hasil = 0,
                                    harga = response[0].price,
                                    disc = isNaN(parseInt($(`#disc-${id}`).val())) ? 0 :
                                    parseInt($(
                                        `#disc-${id}`).val()),
                                    diskon = isNaN(parseInt($(`#diskon`).val())) ? 0 :
                                    parseInt($(
                                        `#diskon`).val());
                                $(`#qty-${id}`).val(1);
                                // menghitung hasil
                                hasil = harga * $(`#qty-${id}`).val();
                                // menghitung amount
                                amount = (hasil - (hasil * disc / 100));
                                // memasukan data amount dan subtotal
                                $(`#amount-${id}`).val(amount);
                                $(`#amount-label-${id}`).html(
                                    `${formatter.format(amount)}`);
                                $('.amount-label').each(() => {
                                    row++;
                                    sTotal += parseInt($(`#amount-${row}`).val())
                                });
                                $('#subtotal-label').html(`${formatter.format(sTotal)}`);
                                $('#subtotal').val(sTotal);

                                var noTax = 0;
                                var hTotal = 0;
                                var tax = isNaN(parseInt($('#tax').val())) ? 0 : parseInt($(
                                        '#tax')
                                    .val());
                                hTotal = parseInt(sTotal - diskon + (sTotal * tax / 100));
                                noTax = parseInt(sTotal - diskon)
                                $('#hargaTotalLabel').html(`${formatter.format(hTotal)}`);
                                $('#hargaTotal').val(hTotal);
                                $('#totalNoTax').val(noTax);
                            },
                            error: function(xhr, status, error) {
                                console.error('Error:', error);
                            }
                        });
                    });
                    $(".invoice-item-price-label").on('keyup', function() {
                        var input = $(this)
                        var id = input.data('id');
                        var input_val = input.val();

                        // don't validate empty input
                        if (input_val === "") {
                            return;
                        }

                        // original length
                        var original_len = input_val.length;

                        // initial caret position 
                        var caret_pos = input.prop("selectionStart");

                        // add commas to number
                        // remove all non-digits
                        input_val = formatNumber(input_val);
                        input_val = input_val;

                        // send updated string to input
                        input.val(input_val);
                        var nomorInt = parseFloat(input_val.replace(/[.,]/g, ''));
                        console.log(id);
                        $(`#price-${id}`).val(nomorInt);

                        // put caret back in the right position
                        var updated_len = input_val.length;
                        caret_pos = updated_len - original_len + caret_pos;
                        input[0].setSelectionRange(caret_pos, caret_pos);
                    });
                    $('.invoice-item-price-label, .invoice-item-qty, .invoice-item-disc').on(
                        'keyup change click',
                        function(ev) {
                            var id = $(this).data('id');
                            var sTotal = 0,
                                row = 0;
                            var amount = 0,
                                hasil = 0,
                                disc = isNaN(parseInt($(`#disc-${id}`).val())) ? 0 : parseInt($(
                                    `#disc-${id}`).val());
                            hasil = $(`#price-${id}`).val() * $(`#qty-${id}`).val();
                            amount = (hasil - (hasil * disc / 100));
                            $(`#amount-${id}`).val(amount);
                            $(`#amount-label-${id}`).html(`${formatter.format(amount)}`);
                            $('.amount-label').each(() => {
                                row++;
                                sTotal += parseInt($(`#amount-${row}`).val())
                            })
                            console.log(sTotal + "<total row>" + row);
                            console.log("Anda Sedang berada di Id : " + id);
                            $('#subtotal-label').html(`${formatter.format(sTotal)}`);
                            $('#subtotal').val(sTotal);
                        });

                    // Logic Harga Total
                    $('#subtotal, #tax, #shipping-label, #diskon-label, .invoice-item-price-label, .invoice-item-qty, .invoice-item-disc')
                        .on('keyup change',
                            () => {
                                var noTax = 0;
                                var hTotal = 0;
                                var sTotal = isNaN(parseInt($('#subtotal').val())) ? 0 : parseInt($('#subtotal')
                                    .val());
                                var shipping = isNaN(parseInt($('#shipping').val())) ? 0 : parseInt($(
                                    '#shipping').val());
                                var discount = isNaN(parseInt($('#diskon').val())) ? 0 : parseInt($('#diskon')
                                    .val());
                                var dTotal = sTotal - discount,
                                    diskon = isNaN(parseInt($(`#diskon`).val())) ? 0 : parseInt($(
                                        `#diskon`).val());
                                var tax = isNaN(parseInt($('#tax').val())) ? 0 : parseInt($('#tax').val());
                                hTotal = parseInt(dTotal + (dTotal * tax / 100) + shipping);
                                noTax = parseInt(dTotal + shipping);
                                console.log(hTotal);
                                $('#hargaTotalLabel').html(`${formatter.format(hTotal)}`);
                                $('#hargaTotal').val(hTotal);
                                $('#totalNoTax').val(noTax);
                            });
                    initializeSelect2Product();
                });

            })
        </script>
    @endpush
