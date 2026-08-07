@extends('layouts.sales.app')
@section('title', 'Create Inventory Adjustment')
@section('content')
    {{-- Hero Page Header & Top Bar --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center py-3 mb-3 gap-3">
        <div>
            <h4 class="fw-bold mb-1">
                <span class="text-muted fw-light">Finance / <a href="{{ route('expense-inventory.index') }}" class="text-muted">Inventory Adjustment</a> /</span> Create
            </h4>
            <p class="text-muted mb-0 small"><i class="mdi mdi-clipboard-text-outline me-1"></i> Koreksi nilai persediaan &amp; alokasi akun terkait</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('expense-inventory.index') }}" class="btn btn-label-secondary">
                <i class="mdi mdi-arrow-left me-1"></i> Back
            </a>
            <button :disabled="focused" type="submit" form="formAuthentication" class="btn btn-primary shadow-sm">
                <i class="mdi mdi-content-save me-1"></i> Save Adjustment
            </button>
        </div>
    </div>

    <form id="formAuthentication" class="fv-plugins-bootstrap5 fv-plugins-framework"
        action="{{ route('expense-inventory.store') }}" method="post" enctype="multipart/form-data">
        @csrf

        <div class="form-invoice-repeater source-item">
            {{-- ADJUSTMENT DETAILS --}}
            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-header bg-transparent border-bottom py-3 d-flex align-items-center">
                    <h6 class="card-title mb-0 fw-bold text-dark">
                        <i class="mdi mdi-file-document-outline me-2 text-primary fs-5"></i> Adjustment Details
                    </h6>
                </div>
                <div class="card-body pt-4">
                    <div class="row g-3">
                        <div class="col-12 col-md-4">
                            <div class="form-floating form-floating-outline">
                                <input class="form-control" type="text" placeholder="Put No Voucher Here ...."
                                    id="no-voucher-input" name="no_invoice" value="{{ old('no_invoice') }}">
                                <label for="no-voucher-input">No Invoice</label>
                            </div>
                        </div>
                        <div class="col-6 col-md-4">
                            <div class="form-floating form-floating-outline">
                                <input class="form-control" type="text" placeholder="Put Memo Here ...." id="memo-input"
                                    name="detail" value="{{ old('detail') }}">
                                <label for="memo-input">Memo</label>
                            </div>
                        </div>
                        <div class="col-6 col-md-4">
                            <div class="form-floating form-floating-outline">
                                <input class="form-control" type="date" id="Date" name="date">
                                <label for="Date">Date</label>
                            </div>
                        </div>

                        <div class="col-12">
                            <hr class="my-2">
                            <div class="d-flex align-items-center text-muted small fw-bold text-uppercase mb-1" style="letter-spacing:.5px;">
                                <i class="mdi mdi-format-list-bulleted-type me-1"></i> Account
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating form-floating-outline">
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
                </div>
            </div>

            {{-- ADJUSTMENT ITEMS --}}
            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-header bg-transparent border-bottom py-3 d-flex align-items-center justify-content-between">
                    <h6 class="card-title mb-0 fw-bold text-dark">
                        <i class="mdi mdi-package-variant-closed me-2 text-primary fs-5"></i> Adjustment Items
                    </h6>
                    <span class="badge bg-label-secondary" id="items-count-badge">1 Item</span>
                </div>
                <div class="card-body p-0">
                    <div class="mb-0" data-repeater-list="group-a">
                        <div class="repeater-wrapper" data-repeater-item="">
                            <div class="position-relative border-bottom p-3">
                                <div class="row w-100">
                                    <div class="col-md-6 col-12 mb-md-0 mb-3">
                                        <label for="product" class="mb-2 small text-muted">Product</label>
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
                                        <p class="mb-2 repeater-title small text-muted">Qty</p>
                                        <input type="number" class="form-control invoice-item-qty" placeholder="Min 1"
                                            name="qty[]" id="qty-1" data-id="1" min="1"
                                            value="{{ old('qty[]') }}">
                                        <p class="info-max-label small text-muted mb-0 mt-1" id="info-max-1"></p>
                                    </div>
                                    <div class="col-md-1 col-12 mb-md-0 mb-3">
                                        <p class="mb-2 repeater-title small text-muted">Warehouse</p>
                                        <select class="form-select invoice-item-warehouse" id="warehouse-1"
                                            data-id="1" aria-label="Default select example" name="warehouse[]">
                                            <option>---Info---</option>
                                            <option value="BDG">BDG</option>
                                            <option value="BKS">BKS</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2 col-12 mb-md-0 mb-3">
                                        <p class="mb-2 repeater-title small text-muted">Price</p>
                                        <div class="input-group input-group-sm" data-price="1">
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
                                    <div class="col-md-2 col-12 pe-4 text-md-end">
                                        <p class="mb-2 repeater-title small text-muted">Amount</p>
                                        <p class="mb-0 amount-label fw-semibold text-primary" id="amount-label-1" data-id="1">
                                            {{ old(strval('amount[]')) }}</p>
                                        <input type="number" class="form-control invoice-item-amount" name="amount[]"
                                            id="amount-1" data-id="1" min="12" value="{{ old('amount[]') }}"
                                            hidden>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-sm btn-icon btn-label-danger btn-del position-absolute top-0 end-0 m-2"
                                    data-repeater-delete="">
                                    <i class="mdi mdi-delete-outline"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex flex-wrap gap-2 p-3 border-top bg-light-subtle">
                        <button type="button" class="btn btn-sm btn-primary shadow-sm btn-add" data-repeater-create="">
                            <i class="mdi mdi-plus me-1"></i> Add Item
                        </button>
                    </div>
                </div>
            </div>

            {{-- SUMMARY --}}
            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-lg-7">
                            <h6 class="fw-bold mb-2 text-dark">
                                <i class="mdi mdi-text-long me-1 text-primary"></i> Say Amount
                            </h6>
                            <p class="fs-6 fw-medium mb-0 p-3 rounded-3 invoice-item-say-total bg-light-subtle border">
                                Say amount: # Rupiah
                            </p>
                        </div>
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
                                    <div class="p-3 rounded-3 d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #f0f2ff 0%, #e8ebff 100%); border: 1px dashed #696cff;">
                                        <div>
                                            <div class="text-uppercase fw-bold text-primary" style="font-size: 10px; letter-spacing: 0.8px;">Total Amount</div>
                                            <div class="text-muted" style="font-size: 10px;">( Total seluruh item )</div>
                                        </div>
                                        <input type="text" class="form-control invoice-item-total-label border-0 bg-transparent text-end fw-bolder text-primary fs-4 p-0"
                                            name="harga" placeholder="Total Here" id="total-label" value="{{ old('total[]') }}" disabled style="max-width: 160px; letter-spacing: -0.5px;">
                                        <input class="form-control invoice-item-total" type="number" name="total"
                                            id="total" value="{{ old('total[]') }}" hidden>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mb-4">
            <a href="{{ route('expense-inventory.index') }}" class="btn btn-label-secondary">Cancel</a>
            <button :disabled="focused" type="submit" class="btn btn-primary shadow-sm px-4">
                <i class="mdi mdi-content-save me-1"></i> Save Adjustment
            </button>
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

            function updateItemsCountBadge() {
                var count = $('.repeater-wrapper').length;
                $('#items-count-badge').text(count + (count === 1 ? ' Item' : ' Items'));
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
                updateItemsCountBadge();

                // Jika ada elemen dinamis yang ditambahkan, gunakan event listener
                $(document).on('repeater:added', function() {
                    initializeSelect2Account();
                    updateItemsCountBadge();
                });
                $(document).on('repeater:deleted', updateItemsCountBadge);
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
