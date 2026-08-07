@extends('layouts.sales.app')
@section('title', 'Return Quotation')
@section('content')
    <form action="{{ route('return.update', $invoice->id) }}" method="post" enctype="multipart/form-data">
        @method('PATCH')
        @csrf
        <h4 class="fw-bold py-3 mb-4">
            Return Of {{ $invoice->no_invoice }}
        </h4>
        <div class="form-floating mb-3">
            <input type="text" class="form-control form-control-lg fw-bold fs-3" id="floatingInputFilled"
                placeholder="xxx/xx/xx/xxxx xxxx" aria-describedby="floatingInputFilledHelp" name="no_return">
            <label for="floatingInputFilled">No Return</label>
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
                    <div class="row">
                        <div class="col-12 col-lg-6 mb-3 mb-lg-0">
                            <div class="form-floating form-floating-outline">
                                <input class="form-control" type="text" name="client" id="client"
                                    value="{{ $quote->pic->client->company }} - {{ $quote->pic->name_pic }}" disabled>
                            </div>
                        </div>
                        <div class="col-6 col-lg-3">
                            <div class="form-floating form-floating-outline">
                                <input class="form-control" type="text" name="sales" id="sales"
                                    value="{{ $quote->sales->name }}" disabled>
                            </div>
                        </div>
                        <div class="col-6 col-lg-3">
                            <div class="form-floating form-floating-outline mb-4">
                                <input class="form-control" type="date" id="Date" name="date"
                                    {{-- {{ @$productIn->date ? '' : '_label' }}  naikin nanti --}}
                                    value="{{ old('date', now()->format('Y-m-d')) }}"
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
                            $row = 1;
                        @endphp
                        @foreach ($dQuote as $product)
                            <div class="repeater-wrapper pt-0 pt-md-4" data-repeater-item="">
                                <div class="d-flex border rounded position-relative pe-0">
                                    <div class="row w-100 p-3">
                                        <div class="col-md-6 col-12 mb-md-0 mb-3">
                                            <label for="p" class="mb-2">Product</label>
                                            <div class="form-floating form-floating-outline mb-2">
                                                <input class="form-control" type="text" name="product" id="product"
                                                    value="{{ $product->equivalent->brand }} {{ $product->equivalent->pn }}"
                                                    disabled>
                                                <input type="text" name="equivalent[]" id="equivalent"
                                                    value="{{ $product->id_equivalent }}" hidden>
                                                <label for="product">product</label>
                                            </div>
                                            <div class="form-floating form-floating-outline mb-2">
                                                <input class="form-control" type="text" name="detail_product"
                                                    id="detail_product" value="{{ $product->detail_product }}" disabled>
                                                <input type="text" name="detail_equivalent[]" id="detail_equivalent"
                                                    value="{{ $product->detail_product }}" hidden>
                                                <label for="detail_product">detail product</label>
                                            </div>
                                        </div>
                                        <div class="col-md-1 col-2 mb-md-0 mb-3">
                                            <p class="mb-2 repeater-title">Qty</p>
                                            <input type="number" class="form-control mb-3 invoice-item-qty" name="qty[]"
                                                id="qty-{{ $row }}" data-id="{{ $row }}" min="0"
                                                value="0" max="{{ $product->qty }}">
                                            <input type="text" name="info_qty[]" id="info_qty"
                                                value="{{ $product->info_qty }}" hidden>
                                            <p class="info-max-label" id="info-max-{{ $row }}">Max :
                                                {{ $product->qty }}</p>
                                        </div>
                                        <div class="col-md-3 col-5 mb-md-0 mb-3">
                                            <p class="mb-2 repeater-title">Price</p>
                                            <div class="input-group" data-price="{{$row}}">
                                                <span class="input-group-text">Rp. </span>
                                                <p class="form-control invoice-item-price-label h-px-25 mb-0" id="price-label"> {{ $product->price }}
                                                </p>
                                            </div>
                                            {{-- <div class="input-group" data-price="{{ $row }}">
                                                <span class="input-group-text">Rp. </span>
                                                <input type="text" class="form-control invoice-item-price-label"
                                                    id="price-label" data-id="{{ $row }}"
                                                    placeholder="Put Price Here" data-type="currency"
                                                    pattern="^[0-9]\d{0,2}(\.\d{3})*$" @focus="focused = true"
                                                    @blur="focused = false" value="{{ $product->price }}">
                                            </div> --}}
                                            <input class="form-control invoice-item-price" type="number" name="price[]"
                                                id="price-{{ $row }}" value="{{ $product->price }}" hidden>
                                        </div>
                                        <div class="col-md-2 col-5 pe-0">
                                            <p class="mb-2 repeater-title">Amount</p>
                                            <p class="mb-0 amount-label" id="amount-label-{{ $row }}"
                                                data-id="{{ $row }}">
                                                Rp
                                                {{ old('amount', @$product->amount ? number_format(@$product->amount, 0, '', '.') : '0') }}
                                            </p>
                                            <input type="number" class="invoice-item-amount" name="amount[]"
                                                id="amount-{{ $row }}" data-id="{{ $row }}"
                                                value="{{ old('amount', @$quote->amount ?? '0') }}" hidden>
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
                                $row++;
                            @endphp
                        @endforeach
                    </div>
                    {{-- <div class="row mb-3">
                        <div class="col-12 mb-2">
                            <button type="button" class="btn btn-sm btn-primary waves-effect waves-light btn-add"
                                data-repeater-create="">
                                <i class="mdi mdi-plus me-1"></i> Add Item
                            </button>
                        </div>
                    </div> --}}
                    <div class="row mb-1">
                        <div class="col-lg-8"></div>
                        <div class="col-lg-4 col-12">
                            <h5 class="my-2">
                                Subtotal
                            </h5>
                            <div class="input-group" data-shipping="1">
                                <span class="input-group-text">Rp. </span>
                                <p class="form-control invoice-item-subtotal-label h-px-25 mb-0" id="subtotal-label"> 0
                                </p>
                            </div>
                            <input class="form-control invoice-item-subtotal" type="number" name="subtotal"
                                id="subtotal" value="0" hidden>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-lg-8"></div>
                        <div class="col-lg-4 col-12">
                            <h5 class="my-2">
                                Tax
                            </h5>
                            <p class="form-control invoice-item-tax-label h-px-25 mb-0" id="tax-label">
                                {{ $quote->tax }} </p>
                            <input class="form-control invoice-item-tax" type="number" name="tax" id="tax"
                                value="{{ $quote->tax }}" hidden>
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
                                <p class="form-control invoice-item-total-label h-px-25 mb-0" id="total-label"> 0 </p>
                                <input class="form-control invoice-item-total" type="number" name="total"
                                    id="total" value="{{ old('total') }}" hidden>
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
                        <a href="{{ route('quotation.index') }}" type="button"
                            class="btn btn-lg btn-outline-secondary w-px-120">
                            Back
                        </a>
                        <button type="submit" class="btn btn-lg btn-primary w-px-120">
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
                $('.amount-label').each(() => {
                    row++;
                    sTotal += parseInt($(`#amount-${row}`).val())
                });
                console.log(sTotal);
                $('#subtotal-label').html(`${formatter.format(sTotal)}`);
                $('#subtotal').val(sTotal);
            });

            // Logic Harga Total
            $('.invoice-item-price-label, .invoice-item-qty')
                .on('keyup change',
                    () => {
                        var row = 0,
                            total = 0,
                            hTotal = 0,
                            tax = isNaN(parseInt($('#tax').val())) ? 0 : parseInt($('#tax').val());
                        $('.amount-label').each(() => {
                            row++;
                            total += parseInt($(`#amount-${row}`).val())
                        });
                        hTotal = parseInt(total + (total * tax / 100));
                        $('#total-label').html(`${formatter.format(hTotal)}`);
                        $('#total').val(hTotal);
                        console.log('Harga total: ' + hTotal);
                    });
        });
    </script>
@endpush
