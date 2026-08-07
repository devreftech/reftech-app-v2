@extends('layouts.sales.app')
@section('title', 'Expense')
@section('content')
    <form id="formAuthentication" class="mb-3 fv-plugins-bootstrap5 fv-plugins-framework"
        action="{{ route('expense-inventory.store') }}" method="post" enctype="multipart/form-data">
        @csrf
        <div class="card mb-3">
            <div class="card-body">
                <div class="form-invoice-repeater source-item">
                    <div class="row">
                        <div class="col-12 col-md-6">
                            <div class="form-floating form-floating-outline mb-2">
                                <input class="form-control" type="text" placeholder="Put No Voucher Here ...."
                                    id="no-voucher-input" name="no_invoice" value="{{ old('no_invoice') }}">
                                <label for="no-voucher-input">No Invoice</label>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="form-floating form-floating-outline mb-2">
                                <input class="form-control" type="text" placeholder="Put Memo Here ...." id="memo-input"
                                    name="detail" value="{{ old('detail') }}">
                                <label for="detail-input">Memo</label>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="form-floating form-floating-outline mb-4">
                                <input class="form-control" type="date" id="Date" name="date">
                                <label for="Date">Date</label>
                            </div>
                        </div>
                    </div>

                    <div class="row w-100">
                        <div class="col-md-6 col-12 mb-md-0">
                            <label for="account" class="mb-2">Account</label>
                            <div class="form-floating form-floating-outline mb-2">
                                <select id="account-1" class="select2 form-select invoice-item-account"
                                    data-allow-clear="true" name="account" data-id="1">
                                    <option> ---- Choose Account Here ---- </option>
                                    @foreach ($account as $accounts)
                                        <option value="{{ $accounts->id }}" data-memo="{{ $accounts->category }}">
                                            {{ $accounts->code }} - {{ $accounts->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="mb-2" data-repeater-list="group-a">
                        <div class="repeater-wrapper pt-0 pt-md-4" data-repeater-item="">
                            <div class="d-flex border rounded position-relative pe-0">
                                <div class="row w-100 p-3">
                                    <div class="col-md-6 col-12 mb-md-0 mb-3">
                                        <label for="product" class="mb-2">Product</label>
                                        <div class="form-floating form-floating-outline mb-2">
                                            <select id="equivalent-dropdown"
                                                class="select2 form-select invoice-item-equivalent" data-allow-clear="true"
                                                name="equivalent[]" data-id="1">
                                                <option> ---- Choose Equivalent || Commodity Here ---- </option>
                                                @foreach ($product as $products)
                                                    <option value="{{ $products->id }}"
                                                        data-commodity="{{ $products->id_product }}">
                                                        {{ $products->pn }} ||
                                                        {{ $products->product->commodity }} -
                                                        {{ $products->product->go == 'Genuine' ? 'G' : 'R' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <label for="equivalent-dropdown">Equivalent || Commodity</label>
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
                                    <div class="col-md-1 col-12 mb-md-0 mb-3">
                                        <p class="mb-2 repeater-title">Warehouse</p>
                                        <div class="form-floating form-floating-outline">
                                            <select class="form-select invoice-item-warehouse" id="warehouse-1"
                                                data-id="1" aria-label="Default select example" name="warehouse[]">
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
                                            id="amount-1" data-id="1" min="12" value="{{ old('amount[]') }}"
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
                                        placeholder="Total Here" id="total-label"value="{{ old('total[]') }}" disabled>
                                    <input class="form-control invoice-item-total" type="number" name="total"
                                        id="total" value="{{ old('total[]') }}" hidden>
                                    <label for="total">Total</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <p class="fs-5 fw-medium mt-2 p-2 invoice-item-say-total w-100"
                        style="background-color: rgb(248, 248, 248); width:70%;"> Say
                        amount: # Rupiah</p>
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
    {{-- <script src="{{ asset('assets') }}/js/app-invoice-add.js"></script> --}}
@endpush

@push('page-script')
    <script src="{{ asset('assets') }}/includes/repeater/repeater-invoice-inventory.js"></script>
    {{-- <script src="{{ asset('assets') }}/includes/validator/quotation-validation.js"></script> --}}
    <script src="{{ asset('assets') }}/js/forms-selects.js"></script>
@endpush

@push('script')
    <script>
        $(() => {
            function capitalizeWords(str) {
                return str.replace(/\b\w/g, function(c) {
                    return c.toUpperCase();
                });
            }

            function terbilang(n) {
                const angka = ["", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan",
                    "sepuluh", "sebelas"
                ];

                n = parseInt(n);

                if (n < 12) return angka[n];
                if (n < 20) return terbilang(n - 10) + " belas";
                if (n < 100) return terbilang(Math.floor(n / 10)) + " puluh " + terbilang(n % 10);
                if (n < 200) return "seratus " + terbilang(n - 100);
                if (n < 1000) return terbilang(Math.floor(n / 100)) + " ratus " + terbilang(n % 100);
                if (n < 2000) return "seribu " + terbilang(n - 1000);
                if (n < 1000000) return terbilang(Math.floor(n / 1000)) + " ribu " + terbilang(n % 1000);
                if (n < 1000000000) return terbilang(Math.floor(n / 1000000)) + " juta " + terbilang(n % 1000000);
                if (n < 1000000000000) return terbilang(Math.floor(n / 1000000000)) + " miliar " + terbilang(n %
                    1000000000);
                if (n < 1000000000000000) return terbilang(Math.floor(n / 1000000000000)) + " triliun " + terbilang(
                    n % 1000000000000);

                return "";
            }
            let formatter = new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR'
            });
            const numberFormatter = new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 0,
                maximumFractionDigits: 2
            });

            function formatNumber(n) {
                return n.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ".")
            }

            function formatRupiah(angka) {
                return new Intl.NumberFormat('id-ID').format(angka);
            }

            function initializeSelect2Commodity() {
                $('.invoice-item-commodity').select2({
                    placeholder: ' ---- Choose Commodity Here ---- ',
                    allowClear: true,
                    width: '100%',
                });
            }

            function initializeSelect2Account() {
                $('.invoice-item-account').select2({
                    placeholder: ' ---- Choose Account Here ---- ',
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

            function initializeSelect2Equivalent() {
                $('.invoice-item-equivalent').select2({
                    placeholder: ' ---- Choose Equivalent Here ---- ',
                    allowClear: true,
                    width: '100%',
                });
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

            // Initialize Bootstrap tooltips using jQuery
            $(document).ready(function() {
                $('[data-bs-toggle="tooltip"]').tooltip();

                // Panggil fungsi inisialisasi saat halaman dimuat
                initializeSelect2Account();

                // Jika ada elemen dinamis yang ditambahkan, gunakan event listener
                $(document).on('repeater:added', function() {
                    initializeSelect2Account();
                });
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
                        // console.log(response[0]);

                        var modal = response[0].modal;
                        var priceLabel = $(`#price-label-${comId}`);
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
                            priceLabel.val(formatRupiah(modal));
                            $(`#price-${comId}`).val(modal);
                            $(`#amount-label-${comId}`).text('Rp ' + formatRupiah(modal));
                            $(`#amount-${comId}`).val(modal);
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

                $.ajax({
                    url: Url,
                    type: 'GET',
                    success: function(response) {
                        console.log(response);
                        console.log('ini com id', comId);
                        var allStock = response.stock + response.warehouse_stock;
                        var modal = response.modal;

                        var priceLabel = $(`#price-label-${comId}`);

                        if (allStock > 0) {
                            $(`#info-max-${comId}`).text(
                                `Max : ${response.stock} - ${response.warehouse_stock}`
                            );
                            $(`#qty-${comId}`).prop('disabled', false);
                            $(`#qty-${comId}`).attr('max', allStock);

                            priceLabel.val(formatRupiah(modal));
                            $(`#price-${comId}`).val(modal);
                            $(`#amount-label-${comId}`).text('Rp ' + formatRupiah(modal));
                            $(`#amount-${comId}`).val(modal);
                        } else {
                            $(`#info-max-${comId}`).text('Max : 0');
                            $(`#qty-${comId}`).attr('max', 0);
                            $(`#qty-${comId}`).prop('disabled', true);
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
                $(`#amount-label-${id}`).html(`${formatRupiah(amount)}`);
            });

            // Logic Harga Total
            $('.invoice-item-price-label, .invoice-item-qty')
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
                        $('#total-label').val(`${formatRupiah(hTotal)}`);
                        $('#total').val(hTotal);
                        let hasilTerbilang = capitalizeWords(terbilang(hTotal).trim());
                        if (hasilTerbilang === "") hasilTerbilang = "-";

                        $('.invoice-item-say-total').text("Say amount: " + hasilTerbilang + " Rupiah");
                        console.log('Harga total: ' + hTotal);
                    });
            // Logic Subtotal dan Amount Setelah Tambah Product
            $('.btn-add').on('click', () => {
                $(`.invoice-item-equivalent`).on('change', function(ev) {
                    var replacementId = $('invoice-item-replacement').val();
                    var productId = $(this).val();
                    var comId = $(this).data('id');
                    var commodity = $(this).find(':selected').data('commodity');
                    $.ajax({
                        url: '/product-in/replacement/' + commodity,
                        type: 'GET',
                        success: function(response) {
                            // console.log(response[0]);

                            var modal = response[0].modal;
                            var priceLabel = $(`#price-label-${comId}`);
                            // Mengosongkan dropdown detail produk
                            $(`#replacement-dropdown-${comId}`).empty();
                            // Mengisi dropdown detail produk dengan hasil yang diterima
                            $.each(response, function(key, value) {
                                $(`#replacement-dropdown-${comId}`).append(
                                    '<option value="' +
                                    value.id + '">' + value.replacement +
                                    '</option>');
                            });
                            var allStock = response[0].stock + response[0]
                                .warehouse_stock;
                            if (response[0].stock >= 1 || response[0].warehouse_stock >=
                                1) {
                                $(`#info-max-${comId}`).text('Max : ' + response[0]
                                    .stock + ' - ' +
                                    response[0].warehouse_stock);
                                $(`#qty-${comId}`).prop('disabled', false);
                                $(`#qty-${comId}`).attr('max', allStock);
                                priceLabel.val(formatRupiah(modal));
                                $(`#price-${comId}`).val(modal);
                                $(`#amount-label-${comId}`).text('Rp ' + formatRupiah(
                                    modal));
                                $(`#amount-${comId}`).val(modal);
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

                    $.ajax({
                        url: Url,
                        type: 'GET',
                        success: function(response) {
                            console.log(response);
                            console.log('ini com id', comId);
                            var allStock = response.stock + response.warehouse_stock;
                            var modal = response.modal;

                            var priceLabel = $(`#price-label-${comId}`);

                            if (allStock > 0) {
                                $(`#info-max-${comId}`).text(
                                    `Max : ${response.stock} - ${response.warehouse_stock}`
                                );
                                $(`#qty-${comId}`).prop('disabled', false);
                                $(`#qty-${comId}`).attr('max', allStock);

                                priceLabel.val(formatRupiah(modal));
                                $(`#price-${comId}`).val(modal);
                                $(`#amount-label-${comId}`).text('Rp ' + formatRupiah(
                                    modal));
                                $(`#amount-${comId}`).val(modal);
                            } else {
                                $(`#info-max-${comId}`).text('Max : 0');
                                $(`#qty-${comId}`).attr('max', 0);
                                $(`#qty-${comId}`).prop('disabled', true);
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
                    $(`#amount-label-${id}`).html(`${formatRupiah(amount)}`);
                });

                // Logic Harga Total
                $('.invoice-item-price-label, .invoice-item-qty')
                    .on('keyup change',
                        () => {
                            var row = 0,
                                total = 0,
                                hTotal = 0,
                                shipping = isNaN(parseInt($('#shipping').val())) ? 0 : parseInt($(
                                    '#shipping').val());
                            $('.amount-label').each(() => {
                                row++;
                                total += parseInt($(`#amount-${row}`).val())
                            });
                            hTotal = parseInt(total + shipping);
                            $('#total-label').val(`${formatRupiah(hTotal)}`);
                            $('#total').val(hTotal);
                            let hasilTerbilang = capitalizeWords(terbilang(hTotal).trim());
                            if (hasilTerbilang === "") hasilTerbilang = "-";

                            $('.invoice-item-say-total').text("Say amount: " + hasilTerbilang + " Rupiah");
                            console.log('Harga total: ' + hTotal);
                        });
                initializeSelect2Commodity();
                initializeSelect2Replacement();
                initializeSelect2Equivalent();
            })
        });
    </script>
@endpush
