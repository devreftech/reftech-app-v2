@extends('layouts.sales.app')
@section('title', 'Servis Unit')
@section('content')
    <form id="formAuthentication" class="mb-3 fv-plugins-bootstrap5 fv-plugins-framework"
        action="{{ route('unit-acquisition.service.store', $fixed->id) }}" method="post" enctype="multipart/form-data">
        @csrf
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="mb-3">Servis Unit — {{ $fixed->code }}</h5>
                <div class="form-invoice-repeater source-item">
                    <div class="row">
                        <div class="col-6 col-md-3">
                            <div class="form-floating form-floating-outline mb-2">
                                <input class="form-control" type="text" placeholder="Put Note Here ...." id="note-input"
                                    name="note" value="{{ old('note') }}">
                                <label for="note-input">Catatan Servis</label>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="form-floating form-floating-outline mb-4">
                                <input class="form-control" type="date" id="Date" name="date"
                                    value="{{ now()->format('Y-m-d') }}">
                                <label for="Date">Tanggal</label>
                            </div>
                        </div>
                    </div>

                    <div class="mb-2" data-repeater-list="group-a">
                        <div class="repeater-wrapper pt-0 pt-md-4" data-repeater-item="">
                            <div class="d-flex border rounded position-relative pe-0">
                                <div class="row w-100 p-3">
                                    <div class="col-md-4 col-12 mb-md-0 mb-3">
                                        <label for="product" class="mb-2">Spare Part</label>
                                        <div class="form-floating form-floating-outline mb-2">
                                            <select id="equivalent-dropdown"
                                                class="select2 form-select invoice-item-equivalent" data-allow-clear="true"
                                                name="equivalent[]" data-id="1">
                                                <option> ---- Choose Equivalent || Commodity Here ---- </option>
                                                @foreach ($product as $products)
                                                    <option value="{{ $products->id }}"
                                                        data-commodity="{{ $products->id_product }}">
                                                        {{ $products->pn }} ||
                                                        {{ $products->product->commodity ?? '' }} -
                                                        {{ $products->product->go == 'Genuine' ? 'G' : 'R' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <label for="equivalent-dropdown">Spare Part</label>
                                        </div>
                                        <div class="form-floating form-floating-outline mb-2">
                                            <select id="replacement-dropdown-1"
                                                class="select2 form-select invoice-item-replacement" data-id="1"
                                                data-allow-clear="true" name="replacement[]" disabled>
                                                <option> ---- Choose Replacement Here ---- </option>
                                            </select>
                                            <label for="replacement-dropdown">Replacement</label>
                                        </div>
                                    </div>
                                    <div class="col-md-1 col-12 mb-md-0 mb-3">
                                        <p class="mb-2 repeater-title">Qty</p>
                                        <input type="number" class="form-control mb-3 invoice-item-qty" placeholder="Min 1"
                                            name="qty[]" id="qty-1" data-id="1" min="1"
                                            value="{{ old('qty[]') }}">
                                        <p class="info-max-label" id="info-max-1"></p>
                                    </div>
                                    <div class="col-md-2 col-12 mb-md-0 mb-3">
                                        <p class="mb-2 repeater-title">Warehouse</p>
                                        <div class="form-floating form-floating-outline">
                                            <select class="form-select invoice-item-warehouse" id="warehouse-1"
                                                data-id="1" aria-label="Default select example" name="warehouse[]">
                                                <option>---Info---</option>
                                                <option value="BDG">BDG</option>
                                                <option value="BKS">BKS</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2 col-12 mb-md-0 mb-3">
                                        <p class="mb-2 repeater-title">Price</p>
                                        <div class="input-group" data-price="1">
                                            <span class="input-group-text">Rp. </span>
                                            <input type="text" class="form-control invoice-item-price-label"
                                                id="price-label-1" data-id="1" min="0"
                                                placeholder="Put Price Here" data-type="currency"
                                                pattern="^[0-9]\d{0,2}(\.\d{3})*$" @focus="focused = true"
                                                @blur="focused = false" value="{{ old('price[]') }}">
                                            <input class="form-control invoice-item-price" type="number" name="price[]"
                                                id="price-1" value="{{ old('price[]') }}" hidden>
                                        </div>
                                    </div>
                                    <div class="col-md-2 col-12 pe-0">
                                        <p class="mb-2 repeater-title">Amount</p>
                                        <p class="mb-0 amount-label" id="amount-label-1" data-id="1">
                                            {{ old(strval('amount[]')) }}</p>
                                        <input type="number" class="form-control invoice-item-amount" name="amount[]"
                                            id="amount-1" data-id="1" min="0" value="{{ old('amount[]') }}"
                                            hidden>
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
                    <div class="row">
                        <div class="col-8 mb-2">
                            <button type="button" class="btn btn-sm btn-primary waves-effect waves-light btn-add"
                                data-repeater-create="">
                                <i class="mdi mdi-plus me-1"></i> Add Item
                            </button>
                        </div>
                        <div class="col-4 mb-2">
                            <div class="input-group input-group-merge mb-3" data-amount="1">
                                <span class="input-group-text">Rp. </span>
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control invoice-item-total-label" name="harga"
                                        placeholder="Total Here" id="total-label" value="{{ old('total[]') }}" disabled>
                                    <label for="total-label">Total Servis</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="float-end">
                        <a href="{{ route('unit-acquisition.show', $fixed->id) }}" type="button"
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
    </form>
@endsection()

@push('after-style')
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/select2/select2.css" />
@endpush

@push('after-script')
    <script src="{{ asset('assets') }}/vendor/libs/formvalidation/dist/js/FormValidation.min.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/select2/select2.js"></script>
    <script src="{{ asset('assets') }}/includes/repeater/jquery-repeater-invoice.js"></script>
@endpush

@push('page-script')
    <script src="{{ asset('assets') }}/includes/repeater/repeater-invoice-inventory.js"></script>
    <script src="{{ asset('assets') }}/js/forms-selects.js"></script>
@endpush

@push('script')
    <script>
        $(() => {
            function formatRupiah(angka) {
                return new Intl.NumberFormat('id-ID').format(angka);
            }

            function initializeSelect2Equivalent() {
                $('.invoice-item-equivalent').select2({
                    placeholder: ' ---- Choose Equivalent Here ---- ',
                    allowClear: true,
                    width: '100%',
                });
            }

            function initializeSelect2Replacement() {
                $('.invoice-item-replacement').select2({
                    placeholder: ' ---- Choose Replacement Here ---- ',
                    allowClear: true,
                    width: '100%',
                });
            }

            function formatNumber(n) {
                return n.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ".")
            }

            function bindEquivalentChange() {
                $('.invoice-item-equivalent').off('change').on('change', function() {
                    var comId = $(this).data('id');
                    var commodity = $(this).find(':selected').data('commodity');
                    $.ajax({
                        url: '/product-in/replacement/' + commodity,
                        type: 'GET',
                        success: function(response) {
                            var priceLabel = $(`#price-label-${comId}`);
                            $(`#replacement-dropdown-${comId}`).empty();
                            $.each(response, function(key, value) {
                                $(`#replacement-dropdown-${comId}`).append(
                                    '<option value="' + value.id + '">' + value.replacement + '</option>'
                                );
                            });
                            var allStock = response[0].stock + response[0].warehouse_stock;
                            if (response[0].stock >= 1 || response[0].warehouse_stock >= 1) {
                                $(`#info-max-${comId}`).text('Max : ' + response[0].stock + ' - ' + response[0]
                                    .warehouse_stock);
                                $(`#qty-${comId}`).prop('disabled', false);
                                $(`#qty-${comId}`).attr('max', allStock);
                                priceLabel.val(formatRupiah(response[0].modal));
                                $(`#price-${comId}`).val(response[0].modal);
                                $(`#amount-label-${comId}`).text('Rp ' + formatRupiah(response[0].modal));
                                $(`#amount-${comId}`).val(response[0].modal);
                            } else {
                                $(`#info-max-${comId}`).text('Max : 0');
                                $(`#qty-${comId}`).attr('max', 0);
                                $(`#qty-${comId}`).prop('disabled', true);
                            }
                            $(`#replacement-dropdown-${comId}`).prop('disabled', false);
                        }
                    });
                });
            }

            function bindReplacementChange() {
                $('.invoice-item-replacement').off('change').on('change', function() {
                    var replacementId = $(this).val();
                    var comId = $(this).data('id');
                    $.ajax({
                        url: '/product-out/replacement/' + replacementId,
                        type: 'GET',
                        success: function(response) {
                            var allStock = response.stock + response.warehouse_stock;
                            var priceLabel = $(`#price-label-${comId}`);
                            if (allStock > 0) {
                                $(`#info-max-${comId}`).text(`Max : ${response.stock} - ${response.warehouse_stock}`);
                                $(`#qty-${comId}`).prop('disabled', false);
                                $(`#qty-${comId}`).attr('max', allStock);
                                priceLabel.val(formatRupiah(response.modal));
                                $(`#price-${comId}`).val(response.modal);
                                $(`#amount-label-${comId}`).text('Rp ' + formatRupiah(response.modal));
                                $(`#amount-${comId}`).val(response.modal);
                            } else {
                                $(`#info-max-${comId}`).text('Max : 0');
                                $(`#qty-${comId}`).attr('max', 0);
                                $(`#qty-${comId}`).prop('disabled', true);
                            }
                        }
                    });
                });
            }

            function bindQtyChange() {
                $('.invoice-item-qty').off('keyup change click').on('keyup change click', function() {
                    var id = $(this).data('id');
                    var harga = Number($(`#price-${id}`).val());
                    var amount = harga * $(`#qty-${id}`).val();
                    $(`#amount-${id}`).val(amount);
                    $(`#amount-label-${id}`).html(`Rp ${formatRupiah(amount)}`);
                    recomputeTotal();
                });
            }

            function recomputeTotal() {
                var row = 0,
                    total = 0;
                $('.amount-label').each(() => {
                    row++;
                    total += parseInt($(`#amount-${row}`).val()) || 0;
                });
                $('#total-label').val(formatRupiah(total));
            }

            $(document).ready(function() {
                initializeSelect2Equivalent();
                initializeSelect2Replacement();
                bindEquivalentChange();
                bindReplacementChange();
                bindQtyChange();

                $(document).on('repeater:added', function() {
                    initializeSelect2Equivalent();
                    initializeSelect2Replacement();
                    bindEquivalentChange();
                    bindReplacementChange();
                    bindQtyChange();
                });
            });
        });
    </script>
@endpush
