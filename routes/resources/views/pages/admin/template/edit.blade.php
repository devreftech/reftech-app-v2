@extends('layouts.sales.app')
@section('title', 'Create Service Quotation')
@section('content')
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <form id="formAuthentication" class="mb-3 fv-plugins-bootstrap5 fv-plugins-framework"
        action="{{ route('template.update', $machine->id) }}" method="post" enctype="multipart/form-data">
        @method('PATCH')
        @csrf
        <div class="card">
            <div class="card-header">
                <h3>Create Template of {{ $machine->brand }} {{ $machine->sku }}</h3>
            </div>
            <div class="card-body">
                {{-- <div class="float-end">
                    <a class="btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#scopeModal">
                        <span class="text-white menu-icon tf-icons mdi mdi-content-copy"></span>
                    </a>
                </div> --}}
                @if (@$machine)
                    @php
                        $id = 1;
                        $dataDelete = 1;
                    @endphp
                    @foreach ($subTemplate as $title)
                        @php
                            $dataDetail = 1;
                        @endphp
                        <div class="service mb-3">
                            <h5 class="mb-0">Title</h5>
                            <input type="text" id="title" class="subtitle form-control form-control-lg mb-2"
                                name="subTitle[]" placeholder="Title Quotation" value="{{ $title->subtitle }}">
                            <div class="border rounded">
                                <div class="form-invoice-repeater-{{ $id }} source-item">
                                    <div class="col-repeater mb-2" data-repeater-list="{{ $id }}">
                                        @foreach ($title->detail as $item)
                                            <div class="repeater-wrapper pt-0" data-repeater-item="{{ $dataDetail }}">
                                                <div class="d-flex position-relative pe-0">
                                                    <div class="row w-100 p-3 pb-0">
                                                        <div class="col-md-6 col-12 mb-md-0">
                                                            <label for="product" class="mb-2">Product</label>
                                                            <div class="form-floating form-floating-outline mb-2">
                                                                <input type="text"
                                                                    id="product-{{ $id }}-{{ $dataDetail }}"
                                                                    class="form-control form-control-lg mb-2"
                                                                    name="product[{{ $id }}][]"
                                                                    placeholder="Input Product Here"
                                                                    value="{{ $item->product }}">
                                                                <label
                                                                    for="product-{{ $id }}-{{ $dataDetail }}">Product</label>
                                                            </div>
                                                            <textarea class="form-control invoice-item-detail-product" rows="2"
                                                                id="detailProduct-{{ $id }}-{{ $dataDetail }}" placeholder="Detail Product. Example: Kaeser ASD"
                                                                name="detail_product[{{ $id }}][]">{{ $item->detail }}</textarea>
                                                        </div>
                                                        <div class="col-md-3 col-12 mb-md-0 mb-3">
                                                            <p class="mb-2 repeater-title">Price</p>
                                                            <div class="input-group mb-3" data-price="{{ $id }}">
                                                                <span class="input-group-text">Rp. </span>
                                                                <input type="text"
                                                                    class="form-control invoice-item-price-label"
                                                                    id="priceLabel-{{ $id }}-{{ $dataDetail }}"
                                                                    data-main="{{ $id }}"
                                                                    data-id="{{ $dataDetail }}" name="harga"
                                                                    placeholder="Put Price Here" data-type="currency"
                                                                    min="0" pattern="^[0-9]\d{0,2}(\.\d{3})*$"
                                                                    @focus="focused = true" @blur="focused = false"
                                                                    value="{{ old('price[' . $id . '][]', @$item->price ? number_format(@$item->price, 0, '', '.') : '') }}">
                                                                <input class="form-control invoice-item-price"
                                                                    type="number" name="price[{{ $id }}][]"
                                                                    id="price-{{ $id }}-{{ $dataDetail }}"
                                                                    value="{{ old('price[' . $id . '][]', @$item->price ?? '') }}"
                                                                    hidden>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-1 col-12 mb-md-0 mb-3">
                                                            <p class="mb-2 repeater-title">Qty</p>
                                                            <input type="number" class="form-control mb-4 invoice-item-qty"
                                                                placeholder="Min 1" name="qty[{{ $id }}][]"
                                                                id="qty-{{ $id }}-{{ $dataDetail }}"
                                                                data-main="{{ $id }}"
                                                                data-id="{{ $dataDetail }}" min="1"
                                                                value="{{$item->qty}}">
                                                            <div class="form-floating form-floating-outline">
                                                                <select class="form-select invoice-item-info"
                                                                    id="info-qty-{{ $id }}-{{ $dataDetail }}"
                                                                    data-main="{{ $id }}"
                                                                    data-id="{{ $dataDetail }}"
                                                                    aria-label="Default select example"
                                                                    name="info_qty[{{ $id }}][]">
                                                                    <option disabled>---Info---</option>
                                                                    <option value="Pcs" {{$item->info_qty == 'Pcs' ? 'Selected' : ''}}>Pcs</option>
                                                                    <option value="Set" {{$item->info_qty == 'Set' ? 'Selected' : ''}}>Set</option>
                                                                    <option value="Pail" {{$item->info_qty == 'Pail' ? 'Selected' : ''}}>Pail</option>
                                                                    <option value="Unit" {{$item->info_qty == 'Unit' ? 'Selected' : ''}}>Unit</option>
                                                                    <option value="Lot" {{$item->info_qty == 'Lot' ? 'Selected' : ''}}>Lot</option>
                                                                    <option value="Meter" {{$item->info_qty == 'Meter' ? 'Selected' : ''}}>Meter</option>
                                                                    <option value="Hari" {{$item->info_qty == 'Hari' ? 'Selected' : ''}}>Hari</option>
                                                                    <option value="Can" {{$item->info_qty == 'Can' ? 'Selected' : ''}}>Can</option>
                                                                </select>
                                                                <label for="exampleFormControlSelect1">Info</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-1 col-12 mb-md-0 mb-3">
                                                            <p class="mb-2 repeater-title">Discount</p>
                                                            <input type="number" class="form-control invoice-item-disc"
                                                                placeholder="%" name="disc[{{ $id }}][]"
                                                                id="disc-{{ $id }}-{{ $dataDetail }}"
                                                                data-main="{{ $id }}"
                                                                data-id="{{ $dataDetail }}" min="0"
                                                                value="{{ $item->disc }}">
                                                        </div>
                                                        <div class="col-md-1 col-12 pe-0">
                                                            <p class="mb-2 repeater-title">Amount</p>
                                                            <p class="mb-0 amount-label"
                                                                id="amount-label-{{ $id }}-{{ $dataDetail }}"
                                                                data-main="{{ $id }}"
                                                                data-id="{{ $dataDetail }}">
                                                                Rp
                                                                {{ old("amount[$id][]", number_format(optional($item)->amount, 2, ',', '.')) }}
                                                            </p>
                                                            <input type="number" class="form-control invoice-item-amount"
                                                                name="amount[{{ $id }}][]"
                                                                id="amount-{{ $id }}-{{ $dataDetail }}"
                                                                data-main="{{ $id }}"
                                                                data-id="{{ $dataDetail }}"
                                                                value="{{ old('amount[' . $id . '][]', @$item->amount ?? '') }}"
                                                                hidden>
                                                        </div>
                                                    </div>
                                                    <div
                                                        class="d-flex flex-column align-items-center justify-content-between border-start p-2">
                                                        <i class="mdi mdi-close cursor-pointer bg-danger text-white btn-del"
                                                            data-delete="{{ $dataDelete }}"
                                                            data-repeater-delete=""></i>
                                                    </div>
                                                </div>
                                            </div>
                                            @php
                                                $dataDetail++;
                                                $dataDelete++;
                                            @endphp
                                        @endforeach
                                    </div>
                                    <div class="row p-3 pt-0">
                                        <div class="col-12">
                                            <button type="button"
                                                class="btn btn-sm btn-primary waves-effect waves-light btn-add"
                                                data-repeater-create="{{ $id }}">
                                                <i class="mdi mdi-plus me-1"></i> Add Product
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @php
                            $id++;
                        @endphp
                    @endforeach
                @endif
                <div class="row p-3">
                    <div class="col-12">
                        <button type="button" class="btn btn-sm btn-success waves-effect waves-light btn-add-title">
                            <i class="mdi mdi-plus me-1"></i> Add Title
                        </button>
                    </div>
                </div>

                <div class="row">
                    <div class="float-end">
                        <a href="{{ route('template.index') }}" type="button" class="btn btn-lg btn-outline-secondary">
                            Back
                        </a>
                        <button :disabled="focused" type="submit" class="btn btn-lg btn-primary">
                            Save
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </form>

    <div class="service-copy mb-3" hidden disable>
        <h5 class="mb-0">Title</h5>
        <input type="text" id="title" class="form-control form-control-lg mb-2" name="subTitle[]"
            placeholder="Title Quotation">
        <div class="border rounded">
            <div class="form-invoice-repeater-1 source-item">
                <div class="col-repeater mb-2" data-repeater-list="1">
                    <div class="repeater-wrapper pt-0" data-repeater-item="1">
                        <div class="d-flex position-relative pe-0">
                            <div class="row w-100 p-3 pb-0">
                                <div class="col-md-6 col-12 mb-md-0">
                                    <label for="product" class="mb-2">Product</label>
                                    <div class="form-floating form-floating-outline mb-2">
                                        <input type="text" id="product-1-1" class="form-control form-control-lg mb-2"
                                            name="product[][]" placeholder="Input Product Here">
                                        <label for="product-1-1">Product</label>
                                    </div>
                                    <textarea class="form-control invoice-item-detail-product" rows="2" id="detailProduct-1-1"
                                        placeholder="Detail Product. Example: Kaeser ASD" name="detail_product[][]"></textarea>
                                </div>
                                <div class="col-md-3 col-12 mb-md-0 mb-3">
                                    <p class="mb-2 repeater-title">Price</p>
                                    <div class="input-group mb-3" data-price="1">
                                        <span class="input-group-text">Rp. </span>
                                        <input type="text" class="form-control invoice-item-price-label"
                                            id="priceLabel-1-1" data-main="1" data-id="1"
                                            placeholder="Put Price Here" data-type="currency" min="0"
                                            pattern="^[0-9]\d{0,2}(\.\d{3})*$" @focus="focused = true"
                                            @blur="focused = false" value="{{ old('price[1][]') }}">
                                        <input class="form-control invoice-item-price" type="number" name="price[][]"
                                            id="price-1-1" value="{{ old('price[1][]') }}" hidden>
                                    </div>
                                </div>
                                <div class="col-md-1 col-12 mb-md-0 mb-3">
                                    <p class="mb-2 repeater-title">Qty</p>
                                    <input type="number" class="form-control mb-4 invoice-item-qty" placeholder="Min 1"
                                        name="qty[][]" id="qty-1-1" data-main="1" data-id="1" min="1"
                                        value="1">
                                    <div class="form-floating form-floating-outline">
                                        <select class="form-select invoice-item-info" id="info-qty-1-1" data-main="1"
                                            data-id="1" aria-label="Default select example" name="info_qty[][]">
                                            <option disabled>---Info---</option>
                                            <option value="Pcs">Pcs</option>
                                            <option value="Set">Set</option>
                                            <option value="Pail">Pail</option>
                                            <option value="Unit">Unit</option>
                                            <option value="Lot">Lot</option>
                                            <option value="Meter">Meter</option>
                                            <option value="Hari">Hari</option>
                                            <option value="Can">Can</option>
                                        </select>
                                        <label for="exampleFormControlSelect1">Info</label>
                                    </div>
                                </div>
                                <div class="col-md-1 col-12 mb-md-0 mb-3">
                                    <p class="mb-2 repeater-title">Discount</p>
                                    <input type="number" class="form-control invoice-item-disc" placeholder="%"
                                        name="disc[][]" id="disc-1-1" data-main="1" data-id="1" min="0"
                                        value="{{ old('disc[1][]', '0') }}">
                                </div>
                                <div class="col-md-1 col-12 pe-0">
                                    <p class="mb-2 repeater-title">Amount</p>
                                    <p class="mb-0 amount-label" id="amount-label-1-1" data-main="1" data-id="1">
                                        {{ old(strval('amount[1][]')) }}</p>
                                    <input type="number" class="form-control invoice-item-amount" name="amount[][]"
                                        id="amount-1-1" data-main="1" data-id="1" value="{{ old('amount[1][]') }}"
                                        hidden>
                                </div>
                            </div>
                            <div class="d-flex flex-column align-items-center justify-content-between border-start p-2">
                                <i class="mdi mdi-close cursor-pointer bg-danger text-white btn-del"
                                    data-repeater-delete=""></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row p-3 pt-0">
                    <div class="col-12">
                        <button type="button" class="btn btn-sm btn-primary waves-effect waves-light btn-add"
                            data-repeater-create="1">
                            <i class="mdi mdi-plus me-1"></i> Add Product
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- @include('components.modal.quotation.service.scope') --}}
@endsection

@push('after-style')
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/select2/select2.css" />
@endpush
@push('after-script')
    <script src="{{ asset('assets') }}/vendor/libs/formvalidation/dist/js/FormValidation.min.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/select2/select2.js"></script>
    {{-- <script src="{{ asset('assets') }}/includes/repeater/jquery-repeater-invoice.js"></script> --}}
@endpush
@push('page-script')
    {{-- <script src="{{ asset('assets') }}/includes/repeater/repeater-service.js"></script> --}}
    {{-- <script src="{{ asset('assets') }}/includes/validator/quotation-validation.js"></script> --}}
    <script src="{{ asset('assets') }}/js/forms-selects.js"></script>
@endpush
@push('script')
    <script>
        $(() => {

            document.addEventListener('DOMContentLoaded', function() {
                const modal = document.getElementById('scopeModal');
                modal.addEventListener('shown.bs.modal', function() {
                    const el = document.getElementById("autoResizeTA");
                    el.style.height = "auto";
                    el.style.height = el.scrollHeight + "px";
                });
            });
            // Format Integer menjadi Currency ID Rupiah
            let formatter = new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR'
            });

            // function initializeSelect2Product() {
            //     $('.invoice-item-product').select2({
            //         placeholder: ' ---- Choose Part Number Here ---- ',
            //         allowClear: true,
            //         width: '100%',
            //     });
            // }
            // $(document).ready(function() {
            //     // Panggil fungsi inisialisasi saat halaman dimuat
            //     initializeSelect2Product();

            //     // Jika ada elemen dinamis yang ditambahkan, gunakan event listener
            //     $(document).on('repeater:added', function() {
            //         initializeSelect2Product();
            //     });
            // });

            // function initFormValidation() {
            //     const fv = FormValidation.formValidation(formAuthentication, {
            //         fields: {
            //             title: {
            //                 validators: {
            //                     notEmpty: {
            //                         message: "Please enter title",
            //                     },
            //                     stringLength: {
            //                         min: 6,
            //                         message: "Name must be more than 6 characters",
            //                     },
            //                 },
            //             },
            //             "detail_product[]": {
            //                 selector: '[name="detail_product[]"]',
            //                 validators: {
            //                     notEmpty: {
            //                         message: "Please enter detail product",
            //                     },
            //                     stringLength: {
            //                         min: 3,
            //                         message: "Area must be more than 3 characters (detail product)",
            //                     },
            //                 },
            //             },
            //             harga: {
            //                 validators: {
            //                     notEmpty: {
            //                         message: "Please enter price",
            //                     },
            //                     numericInput: {
            //                         number: "Please enter a valid number.",
            //                     },
            //                 },
            //             },
            //             "qty[]": {
            //                 validators: {
            //                     notEmpty: {
            //                         message: "Please enter Quantity",
            //                     },
            //                     numericInput: {
            //                         number: "Please enter a valid number.",
            //                     },
            //                 },
            //             },
            //         },
            //         plugins: {
            //             trigger: new FormValidation.plugins.Trigger(),
            //             bootstrap5: new FormValidation.plugins.Bootstrap5({
            //                 eleValidClass: "",
            //                 rowSelector: ".mb-3",
            //             }),
            //             submitButton: new FormValidation.plugins.SubmitButton(),

            //             defaultSubmit: new FormValidation.plugins.DefaultSubmit(),
            //             autoFocus: new FormValidation.plugins.AutoFocus(),
            //         },
            //         init: (instance) => {
            //             instance.on("plugins.message.placed", function(e) {
            //                 if (
            //                     e.element.parentElement.classList.contains(
            //                         "input-group"
            //                     )
            //                 ) {
            //                     e.element.parentElement.insertAdjacentElement(
            //                         "afterend",
            //                         e.messageElement
            //                     );
            //                 }
            //             });
            //         },
            //     });
            // }

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
                $.ajax({
                    url: '/quotation/client/' + clientId,
                    type: 'GET',
                    success: function(response) {
                        $(`.invoice-item-destination`).empty();
                        $.each(response, function(key, value) {
                            $(`.invoice-item-destination`).append(
                                '<option value="' +
                                1 + '">' + value.address +
                                '</option>' +
                                '<option value="' +
                                2 + '">' + value.subAddress +
                                '</option>'
                            );
                        });
                        $(`.invoice-item-destination`).prop('disabled', false);
                    }
                });
            });

            $(".invoice-item-price-label").on('keyup', function() {
                var input = $(this)
                var main = $(this).data('main');
                var id = input.data('id');
                var input_val = input.val();

                var original_len = input_val.length;

                input_val = formatNumber(input_val);
                input_val = input_val;

                input.val(input_val);
                var nomorInt = parseFloat(input_val.replace(/[.,]/g, ''));
                console.log(id);
                $(`#price-${main}-${id}`).val(nomorInt);
            });

            // Logic amount + Subtotal
            $('.invoice-item-price-label, .invoice-item-qty, .invoice-item-disc').on('keyup change click', function(
                ev) {
                var main = $(this).data('main');
                var id = $(this).data('id');
                var sTotal = 0,
                    row = 0,
                    amount = 0,
                    hasil = 0,
                    valHarga = $(`#price-${main}-${id}`).val(),
                    harga = Number(valHarga),
                    disc = isNaN(parseInt($(`#disc-${main}-${id}`).val())) ? 0 : parseInt($(
                        `#disc-${main}-${id}`).val());
                console.log(harga);
                hasil = harga * $(`#qty-${main}-${id}`).val();
                amount = (hasil - (hasil * disc / 100));
                $(`#amount-${main}-${id}`).val(amount);
                $(`#amount-label-${main}-${id}`).html(`${formatter.format(amount)}`);
                let total = 0;
                $('[id^="amount-"]').each(function() {
                    const value = parseInt($(this).val());
                    if (!isNaN(value)) {
                        sTotal += value; // Tambahkan nilai jika valid (angka)
                    }
                });

                console.log("Total:", total);
                console.log("jumlah amount : " + $('.amount-label:visible').length);
                console.log(sTotal + "<total main>" + main);
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
                        var sTotal = isNaN(parseInt($('#subtotal').val())) ? 0 : parseInt($('#subtotal').val());
                        var shipping = isNaN(parseInt($('#shipping').val())) ? 0 : parseInt($('#shipping').val());
                        var discount = isNaN(parseInt($('#diskon').val())) ? 0 : parseInt($('#diskon').val());
                        var dTotal = sTotal - discount;
                        var tax = isNaN(parseInt($('#tax').val())) ? 0 : parseInt($('#tax').val());
                        hTotal = parseInt(dTotal + (dTotal * tax / 100) + shipping);
                        noTax = parseInt(dTotal + shipping);
                        console.log(hTotal);
                        $('#hargaTotalLabel').html(`${formatter.format(hTotal)}`);
                        $('#hargaTotal').val(hTotal);
                        $('#totalNoTax').val(noTax);
                    });
            // Logic Subtotal dan Amount Setelah Tambah Product
            $('.btn-add-title').on('click', () => {
                console.log('btn Add');
                $(".invoice-item-price-label").on('keyup', function() {
                    var input = $(this)
                    var main = input.data('main');
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
                    $(`#price-${main}-${id}`).val(nomorInt);

                    // put caret back in the right position
                    var updated_len = input_val.length;
                    caret_pos = updated_len - original_len + caret_pos;
                    input[0].setSelectionRange(caret_pos, caret_pos);
                });
                $('.invoice-item-price-label, .invoice-item-qty, .invoice-item-disc').on(
                    'keyup change click',
                    function(ev) {
                        console.log('btn price , qty , diisc');
                        var main = $(this).data('main');
                        var id = $(this).data('id');
                        var sTotal = 0,
                            row = 0;
                        var amount = 0,
                            hasil = 0,
                            disc = isNaN(parseInt($(`#disc-${main}-${id}`).val())) ? 0 : parseInt($(
                                `#disc-${main}-${id}`).val());
                        hasil = $(`#price-${main}-${id}`).val() * $(`#qty-${main}-${id}`).val();
                        amount = (hasil - (hasil * disc / 100));
                        $(`#amount-${main}-${id}`).val(amount);
                        $(`#amount-label-${main}-${id}`).html(`${formatter.format(amount)}`);
                        $('.amount-label').each(() => {
                            row++;
                            sTotal += parseInt($(`#amount-${main}-${id}`).val())
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
                            var dTotal = sTotal - discount;
                            var tax = isNaN(parseInt($('#tax').val())) ? 0 : parseInt($('#tax').val());
                            hTotal = parseInt(dTotal + (dTotal * tax / 100) + shipping);
                            noTax = parseInt(dTotal + shipping);
                            console.log(hTotal);
                            $('#hargaTotalLabel').html(`${formatter.format(hTotal)}`);
                            $('#hargaTotal').val(hTotal);
                            $('#totalNoTax').val(noTax);
                        });
            });

            document.querySelectorAll("[data-repeater-create]").forEach((button) => {
                button.addEventListener("click", function() {
                    const repeaterIndex = this.getAttribute(
                        "data-repeater-create"); // Dapatkan indeks repeater

                    const repeaterContainer = document.querySelector(
                        `.form-invoice-repeater-${repeaterIndex}`);

                    if (repeaterContainer) {
                        const template = repeaterContainer.querySelector(
                            ".repeater-wrapper[data-repeater-item]");
                        if (template) {
                            // Kloning elemen template
                            const newItem = template.cloneNode(true);
                            const wrapper = repeaterContainer.querySelector(
                                `[data-repeater-list="${repeaterIndex}"]`);

                            // Hitung jumlah elemen saat ini
                            const currentItems = wrapper.querySelectorAll(
                                ".repeater-wrapper");
                            const newIndex = currentItems.length + 1;
                            console.log(template);
                            console.log(currentItems);

                            // Perbarui atribut elemen kloningan
                            newItem.setAttribute("data-repeater-item", newIndex);
                            newItem.querySelectorAll("input, textarea, select, p").forEach(
                                (input) => {
                                    const name = input.getAttribute("name") || "";
                                    const id = input.getAttribute("id") || "";
                                    const dataMain = input.getAttribute("data-main") || "";
                                    const dataId = input.getAttribute("data-id") || "";

                                    if (name) {
                                        input.setAttribute(
                                            "name",
                                            name.replace(/\[\d+]/,
                                                `[${repeaterIndex}]`).replace(
                                                /\[\d+\]$/, `[${newIndex}]`)
                                        );
                                    }
                                    if (id) {
                                        input.setAttribute("id", id.replace(
                                            /-\d+-\d+$/,
                                            `-${repeaterIndex}-${newIndex}`
                                        ));
                                    }
                                    if (dataId) {
                                        input.setAttribute("data-id",
                                            newIndex);
                                    }
                                    if (dataMain) {
                                        input.setAttribute("data-main",
                                            repeaterIndex);
                                    }
                                    // Reset nilai elemen input
                                    if (input.tagName === "INPUT" || input
                                        .tagName === "TEXTAREA") {
                                        input.value = "";
                                    } else if (input.tagName === "SELECT") {
                                        input.selectedIndex = 0;
                                    }
                                });

                            // Tambahkan elemen baru ke wrapper
                            wrapper.appendChild(newItem);

                            // Perlihatkan elemen baru
                            newItem.style.display = "block";

                            // Event listener untuk tombol hapus
                            // const deleteButton = newItem.querySelector(
                            //     "[data-repeater-delete]");
                            // if (deleteButton) {
                            //     deleteButton.addEventListener("click", function() {
                            //         const dataDelete = this.getAttribute('data-delete');
                            //         console.log("Data-delete yang diklik:", dataDelete);

                            //         if (confirm(
                            //                 "Are you sure you want to delete this element?"
                            //             )) {
                            //             newItem.remove();
                            //         }
                            //     });
                            // }
                        } else {
                            console.error(
                                "Template untuk elemen repeater tidak ditemukan.");
                        }
                    } else {
                        console.error(
                            `Repeater dengan data-repeater-create="${repeaterIndex}" tidak ditemukan.`
                        );
                    }

                    $(".invoice-item-price-label").on('keyup', function() {
                        var input = $(this)
                        var main = input.data('main');
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
                        $(`#price-${main}-${id}`).val(nomorInt);

                        // put caret back in the right position
                        var updated_len = input_val.length;
                        caret_pos = updated_len - original_len + caret_pos;
                        input[0].setSelectionRange(caret_pos, caret_pos);
                    });

                    $('.invoice-item-price-label, .invoice-item-qty, .invoice-item-disc').on(
                        'keyup change click',
                        function(
                            ev) {
                            // mengambil ID
                            var main = $(this).data('main');
                            var id = $(this).data('id');
                            // prepare data
                            var sTotal = 0,
                                row = 0,
                                amount = 0,
                                hasil = 0,
                                valHarga = $(`#price-${main}-${id}`).val(),
                                harga = Number(valHarga),
                                disc = isNaN(parseInt($(`#disc-${main}-${id}`).val())) ? 0 :
                                parseInt($(
                                    `#disc-${main}-${id}`).val());
                            // menghitung hasil
                            console.log(harga);
                            hasil = harga * $(`#qty-${main}-${id}`).val();
                            // menghitung amount
                            amount = (hasil - (hasil * disc / 100));
                            // memasukan data amount dan subtotal
                            $(`#amount-${main}-${id}`).val(amount);
                            $(`#amount-label-${main}-${id}`).html(
                                `${formatter.format(amount)}`);
                            let total = 0;
                            // Iterasi semua elemen input yang sesuai dengan pola ID "amount-{main}-{id}"
                            $('[id^="amount-"]').each(function() {
                                const value = parseInt($(this).val());
                                if (!isNaN(value)) {
                                    sTotal +=
                                        value; // Tambahkan nilai jika valid (angka)
                                }
                            });

                            console.log("Total:", total);
                            console.log("jumlah amount : " + $('.amount-label:visible').length);
                            console.log(sTotal + "<total main>" + main);
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
                                var sTotal = isNaN(parseInt($('#subtotal').val())) ? 0 : parseInt($(
                                        '#subtotal')
                                    .val());
                                var shipping = isNaN(parseInt($('#shipping').val())) ? 0 : parseInt(
                                    $(
                                        '#shipping').val());
                                var discount = isNaN(parseInt($('#diskon').val())) ? 0 : parseInt($(
                                        '#diskon')
                                    .val());
                                var dTotal = sTotal - discount;
                                var tax = isNaN(parseInt($('#tax').val())) ? 0 : parseInt($('#tax')
                                    .val());
                                hTotal = parseInt(dTotal + (dTotal * tax / 100) + shipping);
                                noTax = parseInt(dTotal + shipping);
                                console.log(hTotal);
                                $('#hargaTotalLabel').html(`${formatter.format(hTotal)}`);
                                $('#hargaTotal').val(hTotal);
                                $('#totalNoTax').val(noTax);
                            });
                });
            });
            document.addEventListener("click", function(e) {
                const deleteButton = e.target.closest("[data-repeater-delete]");
                if (deleteButton) {
                    const dataDelete = deleteButton.getAttribute("data-delete");
                    console.log("Data-delete yang diklik:", dataDelete);

                    const repeaterItem = deleteButton.closest("[data-repeater-item]");
                    if (repeaterItem) {
                        if (confirm("Are you sure you want to delete this element?")) {
                            repeaterItem.remove();
                            var sTotal = 0,
                                dTotal = 0,
                                tax = 0,
                                shipping = 0,
                                discount = 0,
                                noTax = 0;

                            $('[id^="amount-"]').each(function() {
                                const value = parseInt($(this).val());
                                if (!isNaN(value)) {
                                    sTotal +=
                                        value; // Tambahkan nilai jika valid (angka)
                                }
                            });

                            shipping = isNaN(parseInt($('#shipping').val())) ? 0 : parseInt(
                                $(
                                    '#shipping').val());
                            discount = isNaN(parseInt($('#diskon').val())) ? 0 : parseInt($(
                                    '#diskon')
                                .val());
                            dTotal = sTotal - discount;
                            tax = isNaN(parseInt($('#tax').val())) ? 0 : parseInt($('#tax')
                                .val());
                            hTotal = parseInt(dTotal + (dTotal * tax / 100) + shipping);
                            noTax = parseInt(dTotal + shipping);

                            $('#subtotal-label').html(`${formatter.format(sTotal)}`);
                            $('#subtotal').val(sTotal);
                            $('#hargaTotalLabel').html(`${formatter.format(hTotal)}`);
                            $('#hargaTotal').val(hTotal);
                            $('#totalNoTax').val(noTax);
                            console.log('sTotal : ' + sTotal + ', dTotal : ' + dTotal + ', tax : ' + tax +
                                ', shipping : ' + shipping + ', discount : ' + discount + ', noTax : ' +
                                noTax);
                        }
                    }
                }
            });

            $('.btn-add-title').on('click', function() {
                const $serviceDiv = $('.service-copy').first(); // Ambil elemen 'service-copy' pertama
                const $clonedDiv = $serviceDiv.clone(); // Salin elemen

                // Ubah class menjadi 'service'
                $clonedDiv.removeClass('service-copy').addClass('service');

                // Hapus hidden dan tampilkan elemen
                $clonedDiv.removeAttr('hidden').show();


                // Sisipkan elemen clone sebelum elemen row
                $clonedDiv.insertBefore($(this).closest('.row'));

                // Hitung elemen dengan class 'service'
                const serviceCount = $('.service').length;
                $clonedDiv.find('#qty-' + serviceCount + '-1').val('1');
                $clonedDiv.find('#qty-' + serviceCount + '-1').html('1');
                // Reset nilai input dan sesuaikan ID
                $clonedDiv.find('.form-invoice-repeater-1').removeClass('form-invoice-repeater-1').addClass(
                    'form-invoice-repeater-' + serviceCount);
                // $clonedDiv.find('.subtitle').removeName('subTitle[]').addClass(
                //     'subTitle[' + serviceCount + ']');
                $clonedDiv.find('.col-repeater').data('repeater-list', serviceCount).attr(
                    'data-repeater-list', serviceCount);
                $clonedDiv.find('.btn-add').data('repeater-create', serviceCount).attr(
                    'data-repeater-create', serviceCount);

                $clonedDiv.find('input, select, textarea, p').each(function() {
                    $(this).val(''); // Kosongkan nilai

                    // Update ID dengan format "id-serviceCount-1"
                    if ($(this).attr('id')) {
                        const originalId = $(this).attr('id'); // Ambil ID asli
                        const newId = originalId.replace(/(\w+)-\d+-\d+$/, `$1-${serviceCount}-1`);
                        $(this).attr('id', newId); // Set ID baru
                    }
                    if ($(this).attr('name')) {
                        const originalName = $(this).attr('name');
                        if (/\[\]$/.test(originalName) && !/\[\]\[\]$/.test(
                                originalName)) {} else if (/\[\]\[\]$/.test(originalName)) {
                            // Logic untuk 2 array (contoh: product[][])
                            const newName = originalName.replace(/\[\]\[\]$/,
                                `[${serviceCount}][]`);
                            $(this).attr('name', newName);
                        }
                    }
                    if ($(this).attr('data-main')) {
                        const originalMain = $(this).attr('data-main');
                        const newMain = originalMain.replace(/\d+$/, serviceCount);
                        $(this).attr('data-main', newMain);
                    }

                });
                console.log('Jumlah elemen dengan class service:', serviceCount);


                $(".invoice-item-price-label").on('keyup', function() {
                    var input = $(this)
                    var main = $(this).data('main');
                    var id = input.data('id');
                    var input_val = input.val();

                    var original_len = input_val.length;

                    input_val = formatNumber(input_val);
                    input_val = input_val;

                    input.val(input_val);
                    var nomorInt = parseFloat(input_val.replace(/[.,]/g, ''));
                    console.log(id);
                    $(`#price-${main}-${id}`).val(nomorInt);
                });

                // Logic amount + Subtotal
                $('.invoice-item-price-label, .invoice-item-qty, .invoice-item-disc').on(
                    'keyup change click',
                    function(
                        ev) {
                        var main = $(this).data('main');
                        var id = $(this).data('id');
                        var sTotal = 0,
                            row = 0,
                            amount = 0,
                            hasil = 0,
                            valHarga = $(`#price-${main}-${id}`).val(),
                            harga = Number(valHarga),
                            disc = isNaN(parseInt($(`#disc-${main}-${id}`).val())) ? 0 : parseInt($(
                                `#disc-${main}-${id}`).val());
                        console.log(harga);
                        hasil = harga * $(`#qty-${main}-${id}`).val();
                        amount = (hasil - (hasil * disc / 100));
                        $(`#amount-${main}-${id}`).val(amount);
                        $(`#amount-label-${main}-${id}`).html(`${formatter.format(amount)}`);
                        let total = 0;
                        $('[id^="amount-"]').each(function() {
                            const value = parseInt($(this).val());
                            if (!isNaN(value)) {
                                sTotal += value; // Tambahkan nilai jika valid (angka)
                            }
                        });

                        console.log("Total:", total);
                        console.log("jumlah amount : " + $('.amount-label:visible').length);
                        console.log(sTotal + "<total main>" + main);
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
                            var dTotal = sTotal - discount;
                            var tax = isNaN(parseInt($('#tax').val())) ? 0 : parseInt($('#tax').val());
                            hTotal = parseInt(dTotal + (dTotal * tax / 100) + shipping);
                            noTax = parseInt(dTotal + shipping);
                            console.log(hTotal);
                            $('#hargaTotalLabel').html(`${formatter.format(hTotal)}`);
                            $('#hargaTotal').val(hTotal);
                            $('#totalNoTax').val(noTax);
                        });

                document.querySelectorAll("[data-repeater-create]").forEach((button) => {
                    button.addEventListener("click", function() {
                        const repeaterIndex = this.getAttribute(
                            "data-repeater-create"); // Dapatkan indeks repeater


                        const repeaterContainer = document.querySelector(
                            `.form-invoice-repeater-${serviceCount}`);

                        if (repeaterContainer && serviceCount == repeaterIndex) {
                            const template = repeaterContainer.querySelector(
                                ".repeater-wrapper[data-repeater-item]");
                            if (template) {
                                // Kloning elemen template
                                const newItem = template.cloneNode(true);

                                console.log(newItem);
                                const wrapper = repeaterContainer.querySelector(
                                    `[data-repeater-list="${serviceCount}"]`);

                                // Hitung jumlah elemen saat ini
                                const currentItems = wrapper.querySelectorAll(
                                    ".repeater-wrapper");
                                const newIndex = currentItems.length + 1;

                                // Perbarui atribut elemen kloningan
                                newItem.setAttribute("data-repeater-item", newIndex);
                                newItem.querySelectorAll("input, textarea, select, p")
                                    .forEach(
                                        (input) => {
                                            const name = input.getAttribute("name") || "";
                                            const id = input.getAttribute("id") || "";
                                            const dataMain = input.getAttribute(
                                                "data-main") || "";
                                            const dataId = input.getAttribute("data-id") ||
                                                "";

                                            if (name) {
                                                input.setAttribute(
                                                    "name",
                                                    name.replace(/\[\d+]/,
                                                        `[${serviceCount}]`).replace(
                                                        /\[\d+\]$/, `[${newIndex}]`)
                                                );
                                            }
                                            if (id) {
                                                input.setAttribute("id", id.replace(
                                                    /-\d+-\d+$/,
                                                    `-${serviceCount}-${newIndex}`
                                                ));
                                            }
                                            if (dataId) {
                                                input.setAttribute("data-id",
                                                    newIndex);
                                            }
                                            if (dataMain) {
                                                input.setAttribute("data-main",
                                                    serviceCount);
                                            }
                                            // Reset nilai elemen input
                                            if (input.tagName === "INPUT" || input
                                                .tagName === "TEXTAREA") {
                                                input.value = "";
                                            } else if (input.tagName === "SELECT") {
                                                input.selectedIndex = 0;
                                            }
                                        });

                                // Tambahkan elemen baru ke wrapper
                                wrapper.appendChild(newItem);

                                // Perlihatkan elemen baru
                                newItem.style.display = "block";

                                // Event listener untuk tombol hapus
                                // const deleteButton = newItem.querySelector(
                                //     "[data-repeater-delete]");
                                // if (deleteButton) {
                                //     deleteButton.addEventListener("click", function() {
                                //         const dataDelete = this.getAttribute(
                                //             'data-delete');
                                //         console.log("Data-delete yang diklik:",
                                //             dataDelete);

                                //         if (confirm(
                                //                 "Are you sure you want to delete this element?"
                                //                 )) {
                                //             newItem.remove();
                                //         }
                                //     });
                                // }
                            } else {
                                console.error(
                                    "Template untuk elemen repeater tidak ditemukan.");
                            }
                        } else {
                            console.error(
                                `Repeater dengan data-repeater-create="${serviceCount}" tidak ditemukan.`
                            );
                        }

                        $(".invoice-item-price-label").on('keyup', function() {
                            var input = $(this)
                            var main = input.data('main');
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
                            var nomorInt = parseFloat(input_val.replace(/[.,]/g,
                                ''));
                            console.log(id);
                            $(`#price-${main}-${id}`).val(nomorInt);

                            // put caret back in the right position
                            var updated_len = input_val.length;
                            caret_pos = updated_len - original_len + caret_pos;
                            input[0].setSelectionRange(caret_pos, caret_pos);
                        });

                        $('.invoice-item-price-label, .invoice-item-qty, .invoice-item-disc')
                            .on(
                                'keyup change click',
                                function(
                                    ev) {
                                    // mengambil ID
                                    var main = $(this).data('main');
                                    var id = $(this).data('id');
                                    // prepare data
                                    var sTotal = 0,
                                        row = 0,
                                        amount = 0,
                                        hasil = 0,
                                        valHarga = $(`#price-${main}-${id}`).val(),
                                        harga = Number(valHarga),
                                        disc = isNaN(parseInt($(`#disc-${main}-${id}`)
                                            .val())) ? 0 :
                                        parseInt($(
                                            `#disc-${main}-${id}`).val());
                                    // menghitung hasil
                                    console.log(harga);
                                    hasil = harga * $(`#qty-${main}-${id}`).val();
                                    // menghitung amount
                                    amount = (hasil - (hasil * disc / 100));
                                    // memasukan data amount dan subtotal
                                    $(`#amount-${main}-${id}`).val(amount);
                                    $(`#amount-label-${main}-${id}`).html(
                                        `${formatter.format(amount)}`);
                                    let total = 0;
                                    // Iterasi semua elemen input yang sesuai dengan pola ID "amount-{main}-{id}"
                                    $('[id^="amount-"]').each(function() {
                                        const value = parseInt($(this).val());
                                        if (!isNaN(value)) {
                                            sTotal +=
                                                value; // Tambahkan nilai jika valid (angka)
                                        }
                                    });

                                    console.log("Total:", total);
                                    console.log("jumlah amount : " + $(
                                        '.amount-label:visible').length);
                                    console.log(sTotal + "<total main>" + main);
                                    console.log("Anda Sedang berada di Id : " + id);
                                    $('#subtotal-label').html(
                                        `${formatter.format(sTotal)}`);
                                    $('#subtotal').val(sTotal);
                                });

                        // Logic Harga Total
                        $('#subtotal, #tax, #shipping-label, #diskon-label, .invoice-item-price-label, .invoice-item-qty, .invoice-item-disc')
                            .on('keyup change',
                                () => {
                                    var noTax = 0;
                                    var hTotal = 0;
                                    var sTotal = isNaN(parseInt($('#subtotal').val())) ? 0 :
                                        parseInt($(
                                                '#subtotal')
                                            .val());
                                    var shipping = isNaN(parseInt($('#shipping').val())) ?
                                        0 : parseInt(
                                            $(
                                                '#shipping').val());
                                    var discount = isNaN(parseInt($('#diskon').val())) ? 0 :
                                        parseInt($(
                                                '#diskon')
                                            .val());
                                    var dTotal = sTotal - discount;
                                    var tax = isNaN(parseInt($('#tax').val())) ? 0 :
                                        parseInt($('#tax')
                                            .val());
                                    hTotal = parseInt(dTotal + (dTotal * tax / 100) +
                                        shipping);
                                    noTax = parseInt(dTotal + shipping);
                                    console.log(hTotal);
                                    $('#hargaTotalLabel').html(
                                        `${formatter.format(hTotal)}`);
                                    $('#hargaTotal').val(hTotal);
                                    $('#totalNoTax').val(noTax);
                                });
                    });
                });
                // document.addEventListener("click", function(e) {
                //     const deleteButton = e.target.closest("[data-repeater-delete]");
                //     if (deleteButton) {
                //         const dataDelete = deleteButton.getAttribute("data-delete");
                //         console.log("Data-delete yang diklik:", dataDelete);

                //         const repeaterItem = deleteButton.closest("[data-repeater-item]");
                //         if (repeaterItem) {
                //             if (confirm("Are you sure you want to delete this element?")) {
                //                 repeaterItem.remove();
                //             }
                //         }
                //     }
                // });
            });

        })
    </script>
@endpush
