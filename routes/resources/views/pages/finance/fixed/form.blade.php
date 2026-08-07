@extends('layouts.sales.app')
@section('title', 'Create Fixed Asset')
@section('content')
    <form id="formAuthentication" class="mb-3 fv-plugins-bootstrap5 fv-plugins-framework" action="{{ route('fixed.store') }}"
        method="post" enctype="multipart/form-data">
        @csrf
        <div class="card mb-3">
            <div class="card-body">
                <div class="form-invoice-repeater source-item">
                    <div class="row">
                        <div class="col-4">
                            <div class="form-floating form-floating-outline mb-2">
                                <input class="form-control" type="text" placeholder="Put Code Here ...."
                                    id="no-code-input" name="code" value="{{ old('code') }}">
                                <label for="no-code-input">Code</label>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-floating form-floating-outline">
                                <select class="form-select invoice-item-type" id="type" data-id="1"
                                    aria-label="Default select example" name="type">
                                    <option>---Type Penyusutan---</option>
                                    <option value="Tanah">Tanah
                                    </option>
                                    <option value="Bangunan">Bangunan
                                    </option>
                                    <option value="Kendaraan">Kendaraan
                                    </option>
                                    <option value="Mesin">Mesin
                                    </option>
                                    <option value="Peralatan Kantor">Peralatan Kantor
                                    </option>
                                    <option value="Tools">Tools
                                    </option>
                                </select>
                                <label for="type">Code</label>
                            </div>
                        </div>
                        <div class="col-2">
                            <div class="form-floating form-floating-outline mb-4">
                                <input class="form-control" type="date" id="Date" name="date">
                                <label for="Date">Tanggal Beli</label>
                            </div>
                        </div>
                        <div class="col-2">
                            <div class="form-floating form-floating-outline mb-4">
                                <input class="form-control" type="date" id="pakai" name="pakai">
                                <label for="pakai">Tanggal Pakai</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-md-6">
                        <div class="form-floating form-floating-outline mb-4">
                            <select id="supplier-dropdown" class="select2 form-select invoice-item-supplier"
                                data-allow-clear="true" name="supplier" data-id="1">
                                <option selected>Pilih Supplier...</option>
                                @foreach ($suppliers as $supp)
                                    <option value="{{ $supp->id }}" data-info="{{ $supp->info }}">
                                        {{ $supp->supplier }}
                                    </option>
                                @endforeach
                            </select>
                            {{-- <label for="supplier-dropdown">Supplier</label> --}}
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="form-floating form-floating-outline mb-2">
                            <input class="form-control" type="text" placeholder="Put No Voucher Here ...."
                                id="no-voucher-input" name="no_invoice" value="{{ old('no_invoice') }}">
                            <label for="no-voucher-input">No Invoice</label>
                        </div>
                    </div>
                </div>
                <div class="d-flex border rounded position-relative pe-0 mb-3">
                    <div class="row w-100 p-3">
                        <div class="col-md-6 col-12 mb-md-0">
                            <label for="Keterangan" class="mb-2">Keterangan</label>
                            <div class="form-floating form-floating-outline mb-2">
                                <input class="form-control" type="text" placeholder="Put Keterangan Here ...."
                                    id="desc-input" name="desc" value="{{ old('desc') }}">
                            </div>
                        </div>
                        <div class="col-md-2 col-12 mb-md-0 mb-3">
                            <p class="mb-2 repeater-title">Qty</p>
                            <div class="form-floating form-floating-outline mb-2">
                                <input type="number" class="form-control mb-3 invoice-item-qty" placeholder="Min 1"
                                    name="qty" id="qty-1" data-id="1" min="1" value="{{ old('qty') }}">
                            </div>
                        </div>
                        <div class="col-md-4 col-12 mb-md-0 mb-3">
                            <p class="mb-2 repeater-title">Total</p>
                            <div class="input-group input-group-merge mb-3" data-total="1">
                                <span class="input-group-text">Rp. </span>
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control invoice-item-amount-label" id="totalLabel-1"
                                        data-id="1" name="harga" placeholder="Put total Here" data-type="currency"
                                        min="0" pattern="^[0-9]\d{0,2}(\.\d{3})*$" @focus="focused = true"
                                        @blur="focused = false" value="{{ old('total') }}">
                                </div>
                            </div>
                            <input class="form-control invoice-item-amount" type="number" name="total"
                                id="amount-1" value="{{ old('total') }}" hidden>
                        </div>
                    </div>
                </div>
                <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button type="button" class="nav-link waves-effect active" data-bs-toggle="tab"
                            data-bs-target="#form-tabs-personal" role="tab" aria-selected="true">
                            Umum
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button type="button" class="nav-link waves-effect" data-bs-toggle="tab"
                            data-bs-target="#form-tabs-account" role="tab" aria-selected="false" tabindex="-1">
                            Pengeluaran
                        </button>
                    </li>
                    <span class="tab-slider" style="left: 0px; width: 165.812px; bottom: 0px;"></span>
                </ul>

                <div class="tab-content">
                    <div class="tab-pane fade active show" id="form-tabs-personal" role="tabpanel">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline">
                                    <input type="number" class="form-control invoice-item-umur" placeholder="Min 1"
                                        name="umur" id="umur" data-id="1" min="1"
                                        value="{{ old('umur') }}">
                                    <label for="umur">Umur Bulan Aktiva</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline">
                                    <select class="form-select invoice-item-metode" id="metode-1" data-id="1"
                                        aria-label="Default select example" name="metode">
                                        <option>---Metode Penyusutan---</option>
                                        <option value="Metode Garis Lurus">Metode Garis Lurus
                                        </option>
                                        <option value="Metode Saldo Menurun">Metode Saldo Menurun
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating form-floating-outline mb-2">
                                    <select id="aktiva-1" class="select2 form-select invoice-item-aktiva"
                                        data-allow-clear="true" name="aktiva" data-id="1">
                                        <option> ---- Choose Account Here ---- </option>
                                        @foreach ($account as $accounts)
                                            <option value="{{ $accounts->id }}" data-memo="{{ $accounts->category }}">
                                                {{ $accounts->code }} - {{ $accounts->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <label for="aktiva">Akun Aktiva</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating form-floating-outline mb-2">
                                    <select id="penyusutan-1" class="select2 form-select invoice-item-penyusutan"
                                        data-allow-clear="true" name="penyusutan" data-id="1">
                                        <option> ---- Choose Account Here ---- </option>
                                        @foreach ($account as $accounts)
                                            <option value="{{ $accounts->id }}" data-memo="{{ $accounts->category }}">
                                                {{ $accounts->code }} - {{ $accounts->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <label for="penyusutan">Akun Akun Penyusutan</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating form-floating-outline mb-2">
                                    <select id="beban-1" class="select2 form-select invoice-item-beban"
                                        data-allow-clear="true" name="beban" data-id="1">
                                        <option> ---- Choose Account Here ---- </option>
                                        @foreach ($account as $accounts)
                                            <option value="{{ $accounts->id }}" data-memo="{{ $accounts->category }}">
                                                {{ $accounts->code }} - {{ $accounts->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <label for="beban">Akun Beban Penyusutan</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="form-tabs-account" role="tabpanel">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-floating form-floating-outline mb-2">
                                    <select id="bank-1" class="select2 form-select invoice-item-bank"
                                        data-allow-clear="true" name="bank" data-id="1">
                                        <option> ---- Choose Account Here ---- </option>
                                        @foreach ($account as $accounts)
                                            <option value="{{ $accounts->id }}" data-memo="{{ $accounts->category }}">
                                                {{ $accounts->code }} - {{ $accounts->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <label for="bank">Akun Bank</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating form-floating-outline mb-4">
                                    <input class="form-control" type="date" id="pay" name="pay">
                                    <label for="pay">Tanggal Pay</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating form-floating-outline">
                                    <select class="form-select invoice-item-status" id="status-1" data-id="1"
                                        aria-label="Default select example" name="status">
                                        <option>---Status Payment---</option>
                                        <option value="1">Sudah dibayar
                                        </option>
                                        <option value="0">Belum Dibayar
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="float-end">
                    <a href="{{ route('quotation.index') }}" type="button" class="btn btn-lg btn-outline-secondary">
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
    <script src="{{ asset('assets') }}/includes/repeater/repeater-invoice-expense.js"></script>
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
            $(".invoice-item-bank").on('change', function() {
                var saldo = $('option:selected', this).data('saldo');
                console.log(saldo);

                $('.invoice-item-saldo-label').val(numberFormatter.format(saldo));
            });
            $(".invoice-item-amount-label").on('keyup', function() {
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
                console.log(nomorInt);
                $(`#amount-${id}`).val(nomorInt);
            });
            $('.invoice-item-amount-label').on('keyup change click', function() {

                var total = 0;

                $('.invoice-item-amount').each(function() {
                    total += parseInt($(this).val()) || 0;
                });

                $('#total-label').val(numberFormatter.format(total));
                $('#total').val(total);
                let hasilTerbilang = capitalizeWords(terbilang(total).trim());
                if (hasilTerbilang === "") hasilTerbilang = "-";

                $('.invoice-item-say-total').text("Say amount: " + hasilTerbilang + " Rupiah");
            });

            function initializeSelect2Account() {
                $('.invoice-item-account').select2({
                    placeholder: ' ---- Choose Account Here ---- ',
                    allowClear: true,
                    width: '100%',
                });
            }
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
            $('.invoice-item-account').on('change', function() {
                var id = $(this).data('id');
                var memo = $('option:selected', this).data('memo');
                console.log(memo);

                $(`#memo-label-${id}`).val(memo);
                $(`#memo-${id}`).val(memo);
            });

            $('.btn-add').on('click', () => {
                initializeSelect2Account();

                $('.invoice-item-account').on('change', function() {
                    var id = $(this).data('id');
                    var memo = $('option:selected', this).data('memo');
                    console.log(memo);

                    $(`#memo-label-${id}`).val(memo);
                    $(`#memo-${id}`).val(memo);
                });
                $(".invoice-item-amount-label").on('keyup', function() {
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
                    console.log(nomorInt);
                    $(`#amount-${id}`).val(nomorInt);
                });
                $('.invoice-item-amount-label').on('keyup change click', function() {

                    var total = 0;

                    $('.invoice-item-amount').each(function() {
                        total += parseInt($(this).val()) || 0;
                    });

                    $('#total-label').val(numberFormatter.format(total));
                    $('#total').val(total);
                    let hasilTerbilang = capitalizeWords(terbilang(total).trim());
                    if (hasilTerbilang === "") hasilTerbilang = "-";

                    $('.invoice-item-say-total').text("Say amount: " + hasilTerbilang + " Rupiah");
                });


            });
            $(document).on('click', '.delete-expense', function() {
                var id = $(this).data('id');
                Swal.fire({
                    title: "Are you sure?",
                    text: "You won't be able to revert this!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Yes, delete it!",
                    customClass: {
                        confirmButton: "btn btn-primary me-3 waves-effect waves-light",
                        cancelButton: "btn btn-label-secondary waves-effect",
                    },
                    buttonsStyling: false,
                }).then(function(result) {
                    if (result.value) {
                        $.ajax({
                            'url': '{{ url('expense-acount') }}/' + id,
                            'type': 'POST',
                            'data': {
                                '_method': 'DELETE',
                                '_token': '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response == 1) {
                                    Swal.fire({
                                        icon: "success",
                                        title: "Deleted!",
                                        text: "Your file has been deleted.",
                                        customClass: {
                                            confirmButton: "btn btn-success waves-effect",
                                        },
                                    })
                                    window.setTimeout(function() {
                                        window.location.href =
                                            '/expense-acount';
                                    }, 2000);
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Oops...',
                                        text: 'Data Failed to Delete!'
                                    });
                                }
                            }
                        });
                    } else if (result.dismiss === Swal.DismissReason.cancel) {
                        Swal.fire({
                            title: "Cancelled",
                            text: "Your imaginary file is safe :)",
                            icon: "error",
                            customClass: {
                                confirmButton: "btn btn-success waves-effect",
                            },
                        });
                    }
                });
            });
        });
    </script>
@endpush
