@extends('layouts.sales.app')
@section('title', 'Form Change Warehous')
@section('content')
    <form action="{{ route('change-warehouse.store') }}" method="post" enctype="multipart/form-data">
        @csrf
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
                <div class="row mb-1">
                    <div class="col-12 col-md-6">
                        <div class="form-floating form-floating-outline mb-2">
                            <input class="form-control" type="text" placeholder="Put Title Here ...." id="title-input"
                                name="title" value="{{ old('title') }}">
                            <label for="title-input">Title</label>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="form-floating form-floating-outline mb-2">
                            <input class="form-control" type="text" placeholder="Put Kurir Here ...." id="kurir-input"
                                name="kurir" value="{{ old('kurir') }}">
                            <label for="kurir-input">Kurir</label>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="form-floating form-floating-outline">
                            <select class="form-select invoice-item-info" id="info-dropdown" name="info"
                                aria-label="Default select example">
                                <option selected disabled>----- Choose Warehouse where to -----</option>
                                <option value="BDG">Bandung</option>
                                <option value="BKS">Bekasi</option>
                            </select>
                            <label for="info-dropdown">Choose Warehouse</label>
                        </div>
                    </div>
                </div>
                <div class="form-invoice-repeater source-item">
                    <div class="mb-3" data-repeater-list="group-a">
                        <div class="repeater-wrapper pt-0 pt-md-4" data-repeater-item="">
                            <div class="d-flex border rounded position-relative pe-0">
                                <div class="row w-100 p-3">
                                    <div class="col-md-8 col-12 mb-md-0 mb-3">
                                        <label for="product" class="mb-2">Product</label>
                                        <div class="form-floating form-floating-outline mb-2">
                                            <select id="replacement-dropdown-1"
                                                class="select2 form-select invoice-item-replacement" data-allow-clear="true"
                                                name="replacement[]" data-id="1">
                                                <option> ---- Choose Commodity || Replacement Here ---- </option>
                                                @foreach ($detProduct as $products)
                                                    <option value="{{ $products->id }}">
                                                        {{ $products->product->commodity }}
                                                        ({{ $products->product->detail_desc }})
                                                        ||
                                                        {{ $products->replacement }} -
                                                        {{ $products->product->go == 'Genuine' ? 'G' : 'R' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <label for="replacement-dropdown">Commodity || Replacement</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-12 mb-md-0 mb-3">
                                        <div class="form-floating form-floating-outline mb-2">
                                            <p class="mb-2 repeater-title">Qty</p>
                                            <input type="number" class="form-control mb-3 invoice-item-qty"
                                                placeholder="Min 1" name="qty[]" id="qty-1" data-id="1"
                                                min="1" value="{{ old('qty[]') }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-12 mb-2">
                            <button type="button" class="btn btn-sm btn-primary waves-effect waves-light btn-add"
                                data-repeater-create="">
                                <i class="mdi mdi-plus me-1"></i> Add Item
                            </button>
                        </div>
                    </div>
                    {{-- <div class="row mb-1">
                        <div class="col-lg-8"></div>
                        <div class="col-lg-4 col-12">
                            <h5 class="my-2">
                                To Warehouse
                            </h5>
                            <div class="form-floating form-floating-outline">
                                <select class="form-select invoice-item-info" id="info-dropdown" name="info"
                                    aria-label="Default select example">
                                    <option selected disabled>----- Choose Warehouse where to -----</option>
                                    <option value="BDG">Bandung</option>
                                    <option value="BKS">Bekasi</option>
                                </select>
                            </div>
                        </div>
                    </div> --}}
                    {{-- <div class="row mb-1">
                        <div class="col-lg-8"></div>
                        <div class="col-lg-4 col-12">
                            <h5 class="my-2">
                                Kurir
                            </h5>
                            <input class="form-control" type="text" placeholder="Put Kurir Here ...." id="kurir-input"
                                name="kurir" value="{{ old('kurir') }}">
                        </div>
                    </div> --}}
                    <div class="row mb-1">
                        <div class="col-lg-8"></div>
                        <div class="col-lg-4 col-12">
                            <h5 class="my-2">
                                Note
                            </h5>
                            <textarea class="form-control h-px-100" rows="2" placeholder="Write your note here...." name="note">-</textarea>
                        </div>
                    </div>
                    <div class="float-end">
                        <a href="{{ route('quotation.index') }}" type="button"
                            class="btn btn-lg btn-outline-secondary w-px-120">
                            Back
                        </a>
                        <button :disabled="focused" type="submit" class="btn btn-lg btn-primary w-px-120">
                            Save
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection
@push('after-style')
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/select2/select2.css" />
@endpush
@push('after-script')
    <script src="{{ asset('assets') }}/vendor/libs/select2/select2.js"></script>
    <script src="{{ asset('assets') }}/includes/repeater/jquery-repeater-invoice.js"></script>
    <script src="{{ asset('assets') }}/includes/repeater/repeater-invoice-productIn.js"></script>
    <script src="{{ asset('assets') }}/js/app-invoice-add.js"></script>
@endpush
@push('page-script')
    <script src="{{ asset('assets') }}/js/forms-selects.js"></script>
@endpush
@push('script')
    <script>
        $(() => {
            var rep = 1;
            let formatter = new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR'
            });

            function formatNumber(n) {
                return n.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ".")
            }

            function initializeSelect2Replacement() {
                $(`#replacement-dropdown-${rep}`).select2({
                    placeholder: ' ---- Choose Commodity || Replacement Here ---- ',
                    allowClear: true,
                    width: '100%',
                });
            }

            $(".invoice-item-shipping-label").on('keyup', function() {
                var input = $(this)
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
                // console.log(id);
                $(`#shipping`).val(nomorInt);
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
                // console.log(id);
                $(`#price-${id}`).val(nomorInt);
            })
            $(".invoice-item-disc-label").on('keyup', function() {
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
                // console.log(id);
                $(`#disc-${id}`).val(nomorInt);
            })

            $('.invoice-item-price-label, .invoice-item-qty, .invoice-item-disc-label').on('keyup change click',
                function(
                    ev) {
                    var id = $(this).data('id');
                    var sTotal = 0,
                        totalDisc = 0,
                        row = 0,
                        rowD = 0;
                    var amount = 0,
                        valDiscount = $(`#disc-${id}`).val(),
                        valHarga = $(`#price-${id}`).val(),
                        disc = Number(valDiscount),
                        harga = Number(valHarga);

                    amount = harga * $(`#qty-${id}`).val() - disc;
                    $(`#amount-${id}`).val(amount);
                    $(`#amount-label-${id}`).html(`${formatter.format(amount)}`);
                    $('.amount-label').each(() => {
                        row++;
                        sTotal += parseInt($(`#amount-${row}`).val())
                    });
                    $('.invoice-item-disc-label').each(() => {
                        rowD++;
                        let val = Number($(`#disc-${rowD}`).val());
                        totalDisc += isNaN(val) ? 0 : val;
                    });
                    console.log('discount : ' + totalDisc);
                    $('#subtotal-label').html(`${formatter.format(sTotal)}`);
                    $('#total-disc-label').html(`${formatter.format(totalDisc)}`);
                    $('#subtotal').val(sTotal);
                    $('#total-disc').val(totalDisc);
                });

            // Logic Harga Total
            $('#shipping-label, .invoice-item-price-label, .invoice-item-qty, .invoice-item-tax, .invoice-item-disc-label')
                .on('keyup change',
                    () => {
                        var row = 0,
                            total = 0,
                            hTotal = 0,
                            totalNoTax = 0,
                            tax = isNaN(parseInt($('#tax').val())) ? 0 : parseInt($('#tax').val()),
                            subtotal = isNaN(parseInt($('#subtotal').val())) ? 0 : parseInt($('#subtotal').val()),
                            shipping = isNaN(parseInt($('#shipping').val())) ? 0 : parseInt($('#shipping').val());
                        hTotal = parseInt(subtotal + (subtotal * tax / 100) + shipping);
                        totalNoTax = parseInt(subtotal + shipping);
                        $('#total-label').html(`${formatter.format(hTotal)}`);
                        $('#total').val(hTotal);
                        $('#totalNoTax').val(totalNoTax);
                        console.log('Harga total: ' + hTotal);
                    });
            // Logic Subtotal dan Amount Setelah Tambah Product
            $('.btn-add').on('click', () => {
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
                    // console.log(id);
                    $(`#price-${id}`).val(nomorInt);
                });


                $(".invoice-item-disc-label").on('keyup', function() {
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
                    // console.log(id);
                    $(`#disc-${id}`).val(nomorInt);
                })

                $('.invoice-item-price-label, .invoice-item-qty, .invoice-item-disc-label').on(
                    'keyup change click',
                    function(
                        ev) {
                        var id = $(this).data('id');
                        var sTotal = 0,
                            totalDisc = 0,
                            row = 0,
                            rowD = 0;
                        var amount = 0,
                            valDiscount = $(`#disc-${id}`).val(),
                            valHarga = $(`#price-${id}`).val(),
                            disc = Number(valDiscount),
                            harga = Number(valHarga);

                        amount = harga * $(`#qty-${id}`).val() - disc;
                        $(`#amount-${id}`).val(amount);
                        $(`#amount-label-${id}`).html(`${formatter.format(amount)}`);
                        $('.amount-label').each(() => {
                            row++;
                            sTotal += parseInt($(`#amount-${row}`).val())
                        });
                        $('.invoice-item-disc-label').each(() => {
                            rowD++;
                            let val = Number($(`#disc-${rowD}`).val());
                            totalDisc += isNaN(val) ? 0 : val;
                        });
                        $('#subtotal-label').html(`${formatter.format(sTotal)}`);
                        $('#total-disc-label').html(`${formatter.format(totalDisc)}`);
                        $('#subtotal').val(sTotal);
                        $('#total-disc').val(totalDisc);
                    });
                // Logic Harga Total
                $('#shipping-label, .invoice-item-price-label, .invoice-item-qty, .invoice-item-tax, .invoice-item-disc-label')
                    .on('keyup change',
                        () => {
                            var row = 0,
                                total = 0,
                                hTotal = 0,
                                totalNoTax = 0,
                                tax = isNaN(parseInt($('#tax').val())) ? 0 : parseInt($('#tax').val()),
                                subtotal = isNaN(parseInt($('#subtotal').val())) ? 0 : parseInt($(
                                    '#subtotal').val()),
                                shipping = isNaN(parseInt($('#shipping').val())) ? 0 : parseInt($(
                                    '#shipping').val());
                            hTotal = parseInt(subtotal + (subtotal * tax / 100) + shipping);
                            totalNoTax = parseInt(subtotal + shipping);
                            $('#total-label').html(`${formatter.format(hTotal)}`);
                            $('#total').val(hTotal);
                            $('#totalNoTax').val(totalNoTax);
                            console.log('Harga total: ' + hTotal);
                        });
                rep++;
                initializeSelect2Replacement();
            })
        });
    </script>
@endpush
