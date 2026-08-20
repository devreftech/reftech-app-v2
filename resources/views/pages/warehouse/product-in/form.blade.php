@extends('layouts.sales.app')
@section('title', 'Product In')
@section('content')
    @if (Auth::user()->role == 'Logistic')
        <div class="mb-3">
            <h4 class="fw-bold mb-1 text-dark">GR Manual &mdash; Barang Masuk Tanpa PO</h4>
            <p class="text-muted mb-0 small">
                Dipakai untuk barang yang tiba-tiba masuk stok tanpa melalui Purchase Order (mis. titipan supplier,
                koreksi stok opname). Harga bisa diisi belakangan lewat Konfirmasi Invoice / Isi Invoice.
            </p>
        </div>
    @endif
    <form action="{{ route(Auth::user()->role == 'Logistic' ? 'product-in.logistic-store' : 'product-in.store') }}"
        method="post" enctype="multipart/form-data">
        @csrf
        <div class="card mb-3">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="text" class="form-control form-control-lg fw-bold fs-4 text-primary"
                                id="noProductInPreview" value="{{ $nextNoProductIn }}" disabled>
                            <label for="noProductInPreview">No. Product In <span class="badge bg-label-secondary ms-1">Auto</span></label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        @if (Auth::user()->role == 'Admin' || Auth::user()->role == 'Accounting')
                            <div class="form-floating">
                                <input type="text" class="form-control form-control-lg fw-bold fs-4" id="floatingInputFilled"
                                    placeholder="xxx/xx/xx/xxxx xxxx" aria-describedby="floatingInputFilledHelp" name="invoice">
                                <label for="floatingInputFilled">No Invoice</label>
                            </div>
                        @else
                            <div class="form-floating">
                                <input type="text" class="form-control form-control-lg fw-bold fs-4" id="floatingInputFilled"
                                    placeholder="xxx/xx/xx/xxxx xxxx" aria-describedby="floatingInputFilledHelp" name="no_do">
                                <label for="floatingInputFilled">No Delivery Order</label>
                            </div>
                        @endif
                    </div>
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
                    <h6 class="text-uppercase text-muted small fw-bold mb-3">Informasi Supplier &amp; Tanggal</h6>
                    <div class="row g-3 mb-4 align-items-end">
                        <div class="col-12 {{ Auth::user()->role == 'Logistic' ? 'col-lg-5' : 'col-lg-4' }}">
                            <div class="form-floating form-floating-outline">
                                <select id="supplier-dropdown" class="select2 form-select invoice-item-supplier"
                                    data-allow-clear="true" name="supplier" data-id="1"
                                    {{ Auth::user()->role == 'Logistic' ? 'disabled' : '' }}>
                                    <option selected>Pilih Supplier...</option>
                                    @foreach ($suppliers as $supp)
                                        <option value="{{ $supp->id }}" data-info="{{ $supp->info }}">
                                            {{ $supp->supplier }}
                                        </option>
                                    @endforeach
                                </select>
                                <label for="supplier-dropdown">Supplier</label>
                            </div>
                        </div>
                        <div class="col-6 {{ Auth::user()->role == 'Logistic' ? 'col-lg-3' : 'col-lg-2' }}">
                            <label class="small text-muted mb-1 d-block">Supplier Info</label>
                            <div class="btn-group w-100" role="group" aria-label="Supplier Info">
                                <button type="button"
                                    class="btn btn-outline-primary info-toggle-btn {{ Auth::user()->role == 'Logistic' ? 'active' : '' }}"
                                    data-value="Lokal" {{ Auth::user()->role == 'Logistic' ? '' : 'disabled' }}>Lokal</button>
                                <button type="button" class="btn btn-outline-primary info-toggle-btn"
                                    data-value="Import" {{ Auth::user()->role == 'Logistic' ? '' : 'disabled' }}>Import</button>
                            </div>
                            <input type="hidden" class="invoice-item-info" id="info-dropdown" name="info"
                                value="{{ Auth::user()->role == 'Logistic' ? 'Lokal' : '' }}">
                        </div>
                        <div class="col-6 {{ Auth::user()->role == 'Logistic' ? 'col-lg-4' : 'col-lg-2' }}">
                            <div class="form-floating form-floating-outline">
                                <input class="form-control" type="date" id="Date" name="date"
                                    value="{{ old('date', @$productIn->date ?? now()->format('Y-m-d')) }}">
                                @if (empty($productIn->date))
                                    <input type="date" name="estimated_date" id=""
                                        value="{{ now()->format('Y-m-d') }}" hidden>
                                @endif
                                <label for="Date">Date Product In</label>
                            </div>
                        </div>
                        @if (Auth::user()->role == 'Admin' || Auth::user()->role == 'Accounting')
                            <div class="col-6 col-lg-2">
                                <div class="form-floating form-floating-outline">
                                    <input class="form-control" type="date" id="DateInvoice" name="date_invoice"
                                        value="{{ old('date_invoice', @$productIn->date_invoice ?? now()->format('Y-m-d')) }}">
                                    <label for="DateInvoice">Date Invoice</label>
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <button type="button" class="btn btn-primary waves-effect waves-light w-100" data-bs-toggle="modal"
                                    data-bs-target="#createSupplier">
                                    + Supplier
                                </button>
                            </div>
                        @endif
                    </div>
                    <h6 class="text-uppercase text-muted small fw-bold mb-3">Item Barang</h6>
                    <div class="mb-3" data-repeater-list="group-a">
                        <div class="repeater-wrapper" data-repeater-item="">
                            <div class="d-flex border rounded position-relative pe-0 mb-3">
                                <div class="row w-100 p-3">
                                    <div
                                        class="{{ Auth::user()->role == 'Admin' || Auth::user()->role == 'Accounting' ? 'col-md-3' : 'col-md-6' }} col-12 mb-md-0 mb-3">
                                        <label for="product" class="mb-2">Product</label>
                                        <div class="form-floating form-floating-outline mb-2">
                                            <select id="replacement-dropdown-1"
                                                class="form-select invoice-item-replacement" data-allow-clear="true"
                                                name="replacement[]" data-id="1">
                                                <option value=""> ---- Choose Commodity || Replacement Here ---- </option>
                                            </select>
                                            <label for="replacement-dropdown">Commodity || Replacement</label>
                                        </div>
                                    </div>
                                    <div class="{{ Auth::user()->role == 'Admin' || Auth::user()->role == 'Accounting' ? 'col-md-1' : 'col-md-3' }} col-12 mb-md-0 mb-3">
                                        <p class="mb-2 repeater-title">Qty</p>
                                        <input type="number" class="form-control invoice-item-qty" placeholder="Min 1"
                                            name="qty[]" id="qty-1" data-id="1" min="1"
                                            value="{{ old('qty[]') }}">
                                    </div>
                                    <div class="{{ Auth::user()->role == 'Admin' || Auth::user()->role == 'Accounting' ? 'col-md-2' : 'col-md-3' }} col-12 mb-md-0 mb-3 warehouse-toggle-wrap">
                                        <p class="mb-2 repeater-title">Warehouse</p>
                                        <div class="btn-group w-100" role="group" aria-label="Warehouse">
                                            <button type="button" class="btn btn-outline-primary btn-sm warehouse-toggle-btn"
                                                data-value="BDG">BDG</button>
                                            <button type="button" class="btn btn-outline-primary btn-sm warehouse-toggle-btn"
                                                data-value="BKS">BKS</button>
                                        </div>
                                        <input type="hidden" class="invoice-item-warehouse" id="warehouse-1"
                                            data-id="1" name="warehouse[]" value="">
                                    </div>
                                    @if (Auth::user()->role == 'Admin' || Auth::user()->role == 'Accounting')
                                        <div class="col-md-2 col-12 mb-md-0 mb-3">
                                            <p class="mb-2 repeater-title">Price</p>
                                            <div class="input-group" data-price="1">
                                                <span class="input-group-text">Rp. </span>
                                                <input type="text" class="form-control invoice-item-price-label"
                                                    id="price-label" data-id="1" min="0"
                                                    placeholder="Put Price Here" data-type="currency"
                                                    pattern="^[0-9]\d{0,2}(\.\d{3})*$" @focus="focused = true"
                                                    @blur="focused = false" value="{{ old('price[]') }}"
                                                    {{ Auth::user()->role == 'Logistic' ? 'Disabled' : '' }}>
                                                <input class="form-control invoice-item-price" type="number"
                                                    name="price[]" id="price-1" value="{{ old('price[]') }}" hidden>
                                            </div>
                                        </div>
                                        <div class="col-md-2 col-12 mb-md-0 mb-3">
                                            <p class="mb-2 repeater-title">Discount</p>
                                            <div class="input-group" data-disc="1">
                                                <span class="input-group-text">Rp. </span>
                                                <input type="text" class="form-control invoice-item-disc-label"
                                                    id="disc-label" data-id="1" min="0"
                                                    placeholder="Put Discount Here" data-type="currency"
                                                    pattern="^[0-9]\d{0,2}(\.\d{3})*$" @focus="focused = true"
                                                    @blur="focused = false" value="{{ old('disc[]') }}"
                                                    {{ Auth::user()->role == 'Logistic' ? 'Disabled' : '' }}>
                                                <input class="form-control invoice-item-disc" type="number"
                                                    name="disc[]" id="disc-1" value="{{ old('disc[]') }}" hidden>
                                            </div>
                                        </div>
                                        <div class="col-md-2 col-12 pe-0">
                                            <p class="mb-2 repeater-title">Amount</p>
                                            <p class="mb-0 amount-label" id="amount-label-1" data-id="1">
                                                {{ old(strval('amount[]')) }}</p>
                                            <input type="number" class="form-control invoice-item-amount"
                                                name="amount[]" id="amount-1" data-id="1" min="0"
                                                value="{{ old('amount[]') }}" hidden>
                                        </div>
                                    @endif
                                </div>
                                <div
                                    class="d-flex flex-column align-items-center justify-content-center border-start p-2">
                                    <i class="mdi mdi-close cursor-pointer bg-danger text-white btn-del"
                                        data-repeater-delete=""></i>
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
                    @if (Auth::user()->role == 'Admin' || Auth::user()->role == 'Accounting')
                        <div class="row mb-1">
                            <div class="col-lg-8"></div>
                            <div class="col-lg-4 col-12">
                                <h5 class="my-2">
                                    Subtotal
                                </h5>
                                <div class="input-group" data-subtotal="1">
                                    <span class="input-group-text">Rp. </span>
                                    <p class="form-control invoice-item-subtotal-label h-px-25 mb-0" id="subtotal-label">
                                        Subtotal
                                        Here </p>
                                    <input class="form-control invoice-item-subtotal" type="number" name="subtotal"
                                        id="subtotal" value="{{ old('subtotal') }}" hidden>
                                    <input class="form-control invoice-item-total-no-tax" type="number"
                                        name="total_no_tax" id="totalNoTax" value="{{ old('total_no_tax') }}" hidden>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-1">
                            <div class="col-lg-8"></div>
                            <div class="col-lg-4 col-12">
                                <h5 class="my-2">
                                    Discount
                                </h5>
                                <div class="input-group" data-total-disc="1">
                                    <span class="input-group-text">Rp. </span>
                                    <p class="form-control invoice-item-total-disc-label h-px-25 mb-0"
                                        id="total-disc-label">
                                        Total Discount Here </p>
                                    <input class="form-control invoice-item-total-disc" type="number" name="total-disc"
                                        id="total-disc" value="{{ old('total-disc') }}" hidden>
                                </div>
                            </div>
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
                                        id="shipping-label" data-id="1" min="0"
                                        placeholder="Put shipping Here" data-type="currency"
                                        pattern="^[0-9]\d{0,2}(\.\d{3})*$" @focus="focused = true"
                                        @blur="focused = false" value="{{ old('shipping') }}"
                                        {{ Auth::user()->role == 'Logistic' ? 'Disabled' : '' }}>
                                    <input class="form-control invoice-item-shipping" type="number" name="shipping"
                                        id="shipping" value="{{ old('shipping') }}" hidden>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-1">
                            <div class="col-lg-8"></div>
                            <div class="col-lg-4 col-12">
                                <h5 class="my-2">
                                    Tax
                                </h5>
                                <div class="form-floating form-floating-outline mb-4">
                                    <select class="form-select invoice-item-tax" id="tax" name="tax"
                                        aria-label="Default select example"
                                        {{ Auth::user()->role == 'Logistic' ? 'Disabled' : '' }}>
                                        <option selected disabled>----- Choose Tax Here -----</option>
                                        <option value="11">11%</option>
                                        <option value="0">0%</option>
                                    </select>
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
                                    <p class="form-control invoice-item-total-label h-px-25 mb-0" id="total-label"> Total
                                        Price Here </p>
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
                                <textarea class="form-control h-px-100" rows="2" placeholder="Write your note here...." name="note"
                                    {{ Auth::user()->role == 'Logistic' ? 'Disabled' : '' }}>-</textarea>
                            </div>
                        </div>
                    @endif
                    @if (Auth::user()->role == 'Logistic')
                        <div class="row mb-3">
                            <div class="col-12">
                                <label for="manual-gr-note" class="mb-2">Catatan / Alasan <span class="text-danger">*</span></label>
                                <textarea class="form-control h-px-100" rows="2" id="manual-gr-note"
                                    placeholder="Kenapa barang ini masuk tanpa PO? (mis. titipan supplier, koreksi stok opname)"
                                    name="note" required>{{ old('note') }}</textarea>
                            </div>
                        </div>
                    @endif
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
    @include('components.modal.warehouse.supplier.form')
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

            // Render tiap hasil pencarian dengan badge G/R (Genuine/Replacement) alih-alih
            // digabung jadi teks polos " - G" / " - R".
            function formatReplacementOption(item) {
                // Placeholder & pesan loading/searching-nya select2 gak punya field custom
                // (commodity/detail_desc/replacement/go) — cuma hasil AJAX asli yang punya.
                if (!item.commodity) return item.text;

                var badgeClass = item.go === 'G' ? 'bg-label-success' : 'bg-label-warning';
                var $result = $('<span></span>');
                $result.text(item.commodity + ' (' + item.detail_desc + ') || ' + item.replacement + ' ');
                $result.append($('<span class="badge ' + badgeClass + '"></span>').text(item.go));
                return $result;
            }

            // Dulu semua 3000+ produk di-render sekaligus jadi <option>, bikin dropdown ini
            // lemot dibuka. Sekarang di-search on-demand ke server (baru mulai nyari kalau
            // ketik minimal 2 karakter), jadi buka form-nya instan.
            function initializeSelect2Replacement() {
                $(`#replacement-dropdown-${rep}`).not('.select2-hidden-accessible').select2({
                    placeholder: ' ---- Choose Commodity || Replacement Here ---- ',
                    allowClear: true,
                    width: '100%',
                    minimumInputLength: 2,
                    templateResult: formatReplacementOption,
                    templateSelection: formatReplacementOption,
                    ajax: {
                        url: '{{ route('product-in.replacements.search') }}',
                        dataType: 'json',
                        delay: 250,
                        data: function(params) {
                            return { q: params.term };
                        },
                        processResults: function(data) {
                            return { results: data };
                        },
                        cache: true,
                    },
                    language: {
                        inputTooShort: function() {
                            return 'Ketik minimal 2 huruf untuk mencari...';
                        },
                        searching: function() {
                            return 'Mencari...';
                        },
                        noResults: function() {
                            return 'Produk tidak ditemukan';
                        },
                    },
                });
            }
            initializeSelect2Replacement();

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
            $('#supplier-dropdown').on('change', function() {
                let info = $(this).find(':selected').data('info');
                $('.info-toggle-btn').removeClass('active');
                $(`.info-toggle-btn[data-value="${info}"]`).addClass('active');
                $('#info-dropdown').val(info);
            });

            // Toggle "Supplier Info" (Lokal/Import) — cuma aktif kalau tombolnya gak disabled
            // (buat Admin/Accounting, nilainya otomatis ngikut supplier yang dipilih di atas).
            $(document).on('click', '.info-toggle-btn:not(:disabled)', function() {
                $('.info-toggle-btn').removeClass('active');
                $(this).addClass('active');
                $('#info-dropdown').val($(this).data('value')).trigger('change');
            });

            // Toggle "Warehouse" (BDG/BKS) per baris item — delegated biar otomatis kepasang
            // ke baris baru yang ditambah lewat "Add Item" juga.
            $(document).on('click', '.warehouse-toggle-btn', function() {
                var $wrap = $(this).closest('.warehouse-toggle-wrap');
                $wrap.find('.warehouse-toggle-btn').removeClass('active');
                $(this).addClass('active');
                $wrap.find('.invoice-item-warehouse').val($(this).data('value')).trigger('change');
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
