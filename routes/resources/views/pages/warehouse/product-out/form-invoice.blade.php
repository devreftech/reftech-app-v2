@extends('layouts.sales.app')
@section('title', 'Product Out')
@section('content')
    <form action="{{ route('product-out.store') }}" method="post" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <div class="col-12 col-md-6">
                <div class="form-floating mb-3">
                    <input type="text" class="form-control form-control-lg fw-bold fs-3" id="floatingInputFilled"
                        placeholder="xxx/xx/xx/xxxx xxxx" aria-describedby="floatingInputFilledHelp" name="invoice"
                        value="{{ $invoice->no_invoice }}">
                    <label for="floatingInputFilled">No Invoice</label>
                    <span class="form-floating-focused"></span>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="form-floating mb-3">
                    <input type="text" class="form-control form-control-lg fw-bold fs-3" id="floatingInputFilled"
                        placeholder="xxx/xx/xx/xxxx xxxx" aria-describedby="floatingInputFilledHelp" name="po"
                        value="{{ $invoice->no_po }}">
                    <label for="floatingInputFilled">No Po</label>
                    <span class="form-floating-focused"></span>
                </div>
            </div>
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
                    <div class="row">
                        <div class="col-12 col-lg-6 mb-3 mb-lg-0">
                            <textarea class="form-control" rows="2" placeholder="Detail Customers" name="detail_client"
                                style="min-height: 100px">{{ $invoice->quote->pic->client->company }}</textarea>
                        </div>
                        <div class="col-6 col-lg-3">
                            <div class="form-floating form-floating-outline">
                                <select class="form-select" id="vers" aria-label="Default select example"
                                    name="vers">
                                    <option disabled>----- Select Offline / Online ------</option>
                                    <option value="Online">Online</option>
                                    <option value="Offline">Offline</option>
                                </select>
                                <label for="vers">Offline / Online</label>
                            </div>
                        </div>
                        <div class="col-6 col-lg-3">
                            <div class="form-floating form-floating-outline mb-4">
                                <input class="form-control" type="date" id="Date" name="date"
                                    {{-- {{ @$productIn->date ? '' : '_label' }}  naikin nanti --}}
                                    value="{{ old('date', @$productIn->date ?? now()->format('Y-m-d')) }}"
                                    {{-- {{ @$productIn->date ? '' : 'disabled' }} --}}>
                                {{-- @if (empty($productIn->date))
                                    <input type="date" name="estimated_date" id=""
                                        value="{{ now()->format('Y-m-d') }}" hidden>
                                @endif --}}
                                <label for="Date">Date Transaction</label>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3" data-repeater-list="group-a">
                        @php
                            $row = 0;
                        @endphp
                        @foreach ($detailQ as $detail)
                            @php
                                $row++;
                            @endphp
                            <div class="repeater-wrapper pt-0 pt-md-4" data-repeater-item="">
                                <div class="d-flex border rounded position-relative pe-0">
                                    <div class="row w-100 p-3">
                                        <div class="col-md-6 col-12 mb-md-0 mb-3">
                                            <label for="product" class="mb-2">Product</label>
                                            <div class="form-floating form-floating-outline mb-2">
                                                <select id="equivalent-dropdown"
                                                    class="select2 form-select invoice-item-equivalent"
                                                    data-allow-clear="true" name="equivalent[]" data-id="1" disabled>
                                                    <option value="{{ $detail->equivalent->id }}"
                                                        data-commodity="{{ $detail->equivalent->id_product }}" selected>
                                                        {{ $detail->equivalent->pn }} ||
                                                        {{ $detail->equivalent->product->commodity }} -
                                                        {{ $detail->equivalent->product->go == 'Genuine' ? 'G' : 'R' }}
                                                    </option>
                                                </select>
                                                <label for="equivalent-dropdown">Equivalent || Commodity</label>
                                            </div>
                                            <input type="text" class="form-control invoice-item-equivalent"
                                                name="equivalent[]" id="equivalent-1" data-id="1" min="12"
                                                value="{{ $detail->id_equivalent }}" hidden>
                                            <div class="form-floating form-floating-outline mb-2">
                                                <select id="replacement-dropdown-1"
                                                    class="select2 form-select invoice-item-replacement" data-id="{{$row}}"
                                                    data-allow-clear="true" name="replacement[]">
                                                    <option> ---- Choose Replacement Here ---- </option>
                                                    @foreach ($dProduct as $product)
                                                        @if ($product->id_product == $detail->equivalent->product->id)
                                                            <option value="{{ $product->id }}">
                                                                {{ $product->replacement }} </option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                                <label for="replacement-dropdown">Replacement</label>
                                            </div>
                                        </div>
                                        <div class="col-md-1 col-12 mb-md-0 mb-3">
                                            <p class="mb-2 repeater-title">Qty</p>
                                            <input type="number" class="form-control mb-3 invoice-item-qty"
                                                placeholder="Min 1" id="qty-1" data-id="1" min="1"
                                                value="{{ $detail->qty }}" disabled>
                                            <p class="info-max-label" id="info-max-{{$row}}"></p>
                                            <input type="number" class="form-control invoice-item-qty" name="qty[]"
                                                id="qty-{{$row}}" data-id="{{$row}}" value="{{ $detail->qty }}" hidden>
                                        </div>
                                        <div class="col-md-1 col-12 mb-md-0 mb-3">
                                            <p class="mb-2 repeater-title">Warehouse</p>
                                            <div class="form-floating form-floating-outline">
                                                <select class="form-select invoice-item-warehouse" id="warehouse-1"
                                                    data-id="1" aria-label="Default select example"
                                                    name="warehouse[]">
                                                    <option>---Info---</option>
                                                    <option value="BDG">BDG
                                                    </option>
                                                    <option value="BKS">BKS
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2 col-12 mb-md-0 mb-3">
                                            <p class="mb-2 repeater-title">Price</p>
                                            <div class="input-group" data-price="1">
                                                <span class="input-group-text">Rp. </span>
                                                <input type="text" class="form-control invoice-item-price-label"
                                                    id="price-label" data-id="1" min="0"
                                                    placeholder="Put Price Here" data-type="currency"
                                                    pattern="^[0-9]\d{0,2}(\.\d{3})*$" @focus="focused = true"
                                                    @blur="focused = false"
                                                    value="{{ number_format($detail->price, 2, ',', '.') }}" disabled>
                                                <input class="form-control invoice-item-price" type="number"
                                                    name="price[]" id="price-1" value="{{ $detail->price }}" hidden>
                                            </div>
                                        </div>
                                        <div class="col-md-2 col-12 pe-0">
                                            <p class="mb-2 repeater-title">Amount</p>
                                            <p class="mb-0 amount-label" id="amount-label-1" data-id="1">
                                                Rp {{ number_format($detail->amount, 2, ',', '.') }}</p>
                                            <input type="number" class="form-control invoice-item-amount"
                                                name="amount[]" id="amount-{{$row}}" data-id="1" min="12"
                                                value="{{ $detail->amount }}" hidden>
                                        </div>
                                    </div>
                                    <div
                                        class="d-flex flex-column align-items-center justify-content-between border-start p-2">
                                        <i class="mdi mdi-close cursor-pointer bg-danger text-white btn-del"
                                            data-repeater-delete=""></i>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="row mb-1">
                        <div class="col-lg-8"></div>
                        <div class="col-lg-4 col-12">
                            <h5 class="my-2">
                                Shipping
                            </h5>
                            <div class="input-group" data-shipping="1">
                                <span class="input-group-text">Rp. </span>
                                <input type="text" class="form-control invoice-item-shipping-label"
                                    id="shipping-label" data-id="1" min="0" placeholder="Put shipping Here"
                                    data-type="currency" pattern="^[0-9]\d{0,2}(\.\d{3})*$" @focus="focused = true"
                                    @blur="focused = false"
                                    value="{{ old('shipping', @$pOut->shipping ? number_format(@$pOut->shipping, 0, '', '.') : '0') }}">
                                <input class="form-control invoice-item-shipping" type="number" name="shipping"
                                    id="shipping" value="{{ old('shipping', @$pOut->shipping ?? '0') }}" hidden>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-lg-8"></div>
                        <div class="col-lg-4 col-12">
                            <h5 class="my-2">
                                Total Price
                            </h5>
                            <div class="input-group" data-total="1">
                                <span class="input-group-text">Rp. </span>
                                <p class="form-control invoice-item-total-label h-px-25 mb-0" id="total-label">
                                    {{number_format( $invoice->quote->subtotal, 0 , ',', '.') }} </p>
                                <input class="form-control invoice-item-total" type="number" name="total"
                                    id="total" value="{{ $invoice->quote->subtotal }}" hidden>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-lg-8"></div>
                        <div class="col-lg-4 col-12">
                            <h5 class="my-2">
                                Note
                            </h5>
                            <textarea class="form-control h-px-100" rows="2" placeholder="Write your note here...." name="note">-</textarea>
                        </div>
                    </div>
                    <div class="float-end">
                        <a href="{{ route('product-out.index') }}" type="button"
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
    <script src="{{ asset('assets') }}/includes/repeater/repeater-invoice-productOut.js"></script>
    <script src="{{ asset('assets') }}/js/app-invoice-add.js"></script>
@endpush
@push('page-script')
    <script src="{{ asset('assets') }}/js/forms-selects.js"></script>
@endpush
@push('script')
    <script>
        $(() => {
            let formatter = new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR'
            });

            function formatNumber(n) {
                return n.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ".")
            }

            function initializeSelect2Replacement() {
                $('.invoice-item-replacement').select2({
                    placeholder: ' ---- Choose Replacement Here ---- ',
                    allowClear: true,
                    width: '100%',
                });
            }

            function initializeSelect2Equivalent() {
                $('.invoice-item-equivalent').select2({
                    placeholder: ' ---- Choose Equivalent Here ---- ',
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
            });

            $(`.invoice-item-equivalent`).on('change', function(ev) {
                var replacementId = $('invoice-item-replacement').val();
                var productId = $(this).val();
                var comId = $(this).data('id');
                var commodity = $(this).find(':selected').data('commodity');
                $.ajax({
                    url: '/product-in/replacement/' + commodity,
                    type: 'GET',
                    success: function(response) {
                        // Mengosongkan dropdown detail produk
                        $(`#replacement-dropdown-${comId}`).empty();
                        // Mengisi dropdown detail produk dengan hasil yang diterima
                        $.each(response, function(key, value) {
                            $(`#replacement-dropdown-${comId}`).append(
                                '<option value="' +
                                value.id + '">' + value.replacement +
                                '</option>');
                        });
                        var allStock = response[0].stock + response[0].warehouse_stock;
                        if (response[0].stock >= 1 || response[0].warehouse_stock >= 1) {
                            $(`#info-max-${comId}`).text('Max : ' + response[0].stock + ' - ' +
                                response[0].warehouse_stock);
                            $(`#qty-${comId}`).prop('disabled', false);
                            $(`#qty-${comId}`).attr('max', allStock);
                        } else {
                            $(`#info-max-${comId}`).text('Max : 0');
                            $(`#qty-${comId}`).attr('max', 0);
                            $(`#qty-${comId}`).prop('disabled', true);
                            $(`#qty-${comId}`).attr('value', 0);
                        }
                        // Mengaktifkan dropdown detail produk
                        $(`#replacement-dropdown-${comId}`).prop('disabled', false);
                    }
                });
            });

            $(`.invoice-item-replacement`).on('change', function(ev) {
                var replacementId = $(this).val();
                var Url = '/product-out/replacement/' + replacementId;
                var comId = $(this).data('id');
                console.log(Url);
                console.log(replacementId);
                $.ajax({
                    url: Url,
                    type: 'GET',
                    success: function(response) {
                        console.log(response);
                        var allStock = response.stock + response.warehouse_stock;
                        if (response.stock >= 1 || response.warehouse_stock >= 1) {
                            $(`#info-max-${comId}`).text('Max : ' + response.stock + ' - ' +
                                response.warehouse_stock);
                            $(`#qty-${comId}`).attr('max', allStock);
                        } else {
                            $(`#info-max-${comId}`).text('Max : 0');
                            $(`#qty-${comId}`).attr('max', 0);
                            $(`#qty-${comId}`).attr('value', 0);
                        }
                    }
                });
            });

            $('.invoice-item-price-label, .invoice-item-qty').on('keyup change click', function(
                ev) {
                var id = $(this).data('id');
                var sTotal = 0,
                    row = 0;
                var amount = 0,
                    valHarga = $(`#price-${id}`).val(),
                    harga = Number(valHarga);
                amount = harga * $(`#qty-${id}`).val();
                $(`#amount-${id}`).val(amount);
                $(`#amount-label-${id}`).html(`${formatter.format(amount)}`);
            });

            // Logic Harga Total
            $('#shipping-label, .invoice-item-price-label, .invoice-item-qty')
                .on('keyup change',
                    () => {
                        var row = 0,
                            total = 0,
                            hTotal = 0,
                            shipping = isNaN(parseInt($('#shipping').val())) ? 0 : parseInt($('#shipping').val());
                        $('.amount-label').each(() => {
                            row++;
                            total += parseInt($(`#amount-${row}`).val())
                        });
                        hTotal = parseInt(total + shipping);
                        $('#total-label').html(`${formatter.format(hTotal)}`);
                        $('#total').val(hTotal);
                        console.log('Harga total: ' + hTotal);
                    });
            // Logic Subtotal dan Amount Setelah Tambah Product
            // $('.btn-add').on('click', () => {
            //     $(".invoice-item-price-label").on('keyup', function() {
            //         var input = $(this)
            //         var id = input.data('id');
            //         var input_val = input.val();

            //         // original length
            //         var original_len = input_val.length;

            //         // add commas to number
            //         // remove all non-digits
            //         input_val = formatNumber(input_val);
            //         input_val = input_val;

            //         // send updated string to input
            //         input.val(input_val);
            //         var nomorInt = parseFloat(input_val.replace(/[.,]/g, ''));
            //         // console.log(id);
            //         $(`#price-${id}`).val(nomorInt);
            //     });

            //     $(`.invoice-item-equivalent`).on('change', function(ev) {
            //         var replacementId = $('invoice-item-replacement').val();
            //         var productId = $(this).val();
            //         var comId = $(this).data('id');
            //         var commodity = $(this).find(':selected').data('commodity');
            //         $.ajax({
            //             url: '/product-in/replacement/' + commodity,
            //             type: 'GET',
            //             success: function(response) {
            //                 // Mengosongkan dropdown detail produk
            //                 $(`#replacement-dropdown-${comId}`).empty();
            //                 // Mengisi dropdown detail produk dengan hasil yang diterima
            //                 $.each(response, function(key, value) {
            //                     $(`#replacement-dropdown-${comId}`).append(
            //                         '<option value="' +
            //                         value.id + '">' + value.replacement +
            //                         '</option>');
            //                 });

            //                 var allStock = response[0].stock + response[0]
            //                     .warehouse_stock;
            //                 if (response[0].stock >= 1 || response[0].warehouse_stock >=
            //                     1) {
            //                     $(`#info-max-${comId}`).text('Max : ' + response[0]
            //                         .stock + ' - ' +
            //                         response[0].warehouse_stock);
            //                     $(`#qty-${comId}`).prop('disabled', false);
            //                     $(`#qty-${comId}`).attr('max', allStock);
            //                 } else {
            //                     $(`#info-max-${comId}`).text('Max : 0');
            //                     $(`#qty-${comId}`).attr('max', 0);
            //                     $(`#qty-${comId}`).prop('disabled', true);
            //                     $(`#qty-${comId}`).attr('value', 0);
            //                 }
            //                 // Mengaktifkan dropdown detail produk
            //                 $(`#replacement-dropdown-${comId}`).prop('disabled', false);
            //             }
            //         });
            //     });

            //     $(`.invoice-item-replacement`).on('change', function(ev) {
            //         var replacementId = $(this).val();
            //         var Url = '/product-out/replacement/' + replacementId;
            //         var comId = $(this).data('id');
            //         console.log(Url);
            //         console.log(replacementId);
            //         $.ajax({
            //             url: Url,
            //             type: 'GET',
            //             success: function(response) {
            //                 console.log(response);
            //                 var allStock = response.stock + response.warehouse_stock;
            //                 if (response.stock >= 1 || response.warehouse_stock >= 1) {
            //                     $(`#info-max-${comId}`).text('Max : ' + response.stock +
            //                         ' - ' +
            //                         response.warehouse_stock);
            //                     $(`#qty-${comId}`).attr('max', allStock);
            //                 } else {
            //                     $(`#info-max-${comId}`).text('Max : 0');
            //                     $(`#qty-${comId}`).attr('max', 0);
            //                     $(`#qty-${comId}`).attr('value', 0);
            //                 }
            //             }
            //         });
            //     });

            //     $('.invoice-item-price-label, .invoice-item-qty').on('keyup change click', function(
            //         ev) {
            //         var id = $(this).data('id');
            //         var sTotal = 0,
            //             row = 0;
            //         var amount = 0,
            //             valHarga = $(`#price-${id}`).val(),
            //             harga = Number(valHarga);
            //         console.log("Harga nya adalah : " + harga);
            //         amount = harga * $(`#qty-${id}`).val();
            //         $(`#amount-${id}`).val(amount);
            //         $(`#amount-label-${id}`).html(`${formatter.format(amount)}`);
            //     });

            //     $('#shipping-label, .invoice-item-price-label, .invoice-item-qty')
            //         .on('keyup change',
            //             () => {
            //                 var row = 0,
            //                     total = 0,
            //                     hTotal = 0,
            //                     shipping = isNaN(parseInt($('#shipping').val())) ? 0 : parseInt($(
            //                         '#shipping').val());
            //                 $('.amount-label').each(() => {
            //                     row++;
            //                     total += parseInt($(`#amount-${row}`).val())
            //                 });
            //                 console.log('Ini Total: ' + total);
            //                 hTotal = parseInt(total + shipping);
            //                 $('#total-label').html(`${formatter.format(hTotal)}`);
            //                 $('#total').val(hTotal);
            //                 console.log('Harga total: ' + hTotal);
            //             });
            //     initializeSelect2Commodity();
            //     initializeSelect2Replacement();
            //     initializeSelect2Equivalent();
            // })
        });
    </script>
@endpush
