@extends('layouts.sales.app')
@section('title', 'Create Expense')
@section('content')
    {{-- Hero Page Header & Top Bar --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center py-3 mb-3 gap-3">
        <div>
            <h4 class="fw-bold mb-1">
                <span class="text-muted fw-light">Finance / <a href="{{ route('expense.index') }}" class="text-muted">Expense</a> /</span> Create
            </h4>
            <p class="text-muted mb-0 small"><i class="mdi mdi-cash-multiple me-1"></i> Catat pengeluaran &amp; alokasi akun</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('expense.index') }}" class="btn btn-label-secondary">
                <i class="mdi mdi-arrow-left me-1"></i> Back
            </a>
            <button :disabled="focused" type="submit" form="formAuthentication" class="btn btn-primary shadow-sm">
                <i class="mdi mdi-content-save me-1"></i> Save Expense
            </button>
        </div>
    </div>

    <form id="formAuthentication" class="fv-plugins-bootstrap5 fv-plugins-framework" action="{{ route('expense.store') }}"
        method="post" enctype="multipart/form-data">
        @csrf

        {{-- Hero No Expense Card --}}
        <div class="card mb-4 border-0 shadow-sm" style="background: linear-gradient(135deg, #f8f9ff 0%, #f0f2ff 100%); border-left: 5px solid #696cff !important;">
            <div class="card-body py-3">
                <div class="row align-items-center g-3">
                    <div class="col-md-8 col-12">
                        <label class="form-label text-uppercase fw-bold text-primary small mb-1" style="letter-spacing: .5px;">
                            <i class="mdi mdi-pound me-1"></i> No. Expense
                        </label>
                        <input class="form-control form-control-lg fw-bold bg-white text-primary border-primary-subtle shadow-sm"
                            type="text" id="no-expense-input" name="no_expense" value="{{ $noExpense }}" readonly
                            style="font-size: 1.35rem;">
                    </div>
                    <div class="col-md-4 col-12 text-md-end">
                        <span class="badge bg-label-secondary px-3 py-2 fs-6 rounded-pill">
                            <i class="mdi mdi-clock-outline me-1"></i> NEW EXPENSE
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-invoice-repeater source-item">
            {{-- BANK & EXPENSE DETAILS --}}
            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-header bg-transparent border-bottom py-3 d-flex align-items-center">
                    <h6 class="card-title mb-0 fw-bold text-dark">
                        <i class="mdi mdi-bank-outline me-2 text-primary fs-5"></i> Bank &amp; Expense Details
                    </h6>
                </div>
                <div class="card-body pt-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <div class="d-flex align-items-center text-muted small fw-bold text-uppercase mb-1" style="letter-spacing:.5px;">
                                <i class="mdi mdi-bank-transfer me-1"></i> Sumber Dana
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating form-floating-outline">
                                <select id="bank-dropdown" class="select2 form-select invoice-item-bank"
                                    data-allow-clear="true" name="bank" data-id="1">
                                    <option selected>Pilih bank...</option>
                                    @foreach ($bank as $item)
                                        <option value="{{ $item->id }}" data-saldo="{{ $item->saldo }}">
                                            {{ $item->bank }} - {{ $item->no_rek }}
                                        </option>
                                    @endforeach
                                </select>
                                <label for="bank-dropdown">Bank</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="input-group input-group-merge" data-amount="1">
                                <span class="input-group-text">Rp. </span>
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control invoice-item-saldo-label"
                                        placeholder="Pilih Bank Dahulu" id="saldo-label" name="saldo"
                                        value="{{ old('saldo[]', 'Pilih Bank Dahulu') }}" disabled>
                                    <label for="saldo-label">Bank Saldo</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating form-floating-outline">
                                <input class="form-control" type="date" id="Date" name="date">
                                <label for="Date">Date</label>
                            </div>
                        </div>

                        <div class="col-12">
                            <hr class="my-2">
                            <div class="d-flex align-items-center text-muted small fw-bold text-uppercase mb-1" style="letter-spacing:.5px;">
                                <i class="mdi mdi-file-document-outline me-1"></i> Detail Dokumen
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="form-floating form-floating-outline">
                                <input class="form-control" type="text" placeholder="Put No Voucher Here ...."
                                    id="no-voucher-input" name="no_invoice" value="{{ old('no_invoice') }}">
                                <label for="no-voucher-input">No Invoice</label>
                            </div>
                        </div>
                        <div class="col-6 col-md-4">
                            <div class="form-floating form-floating-outline">
                                <input class="form-control" type="text" placeholder="Put No Cheque Here ...."
                                    id="no-cheque-input" name="no_cheque" value="{{ old('no_cheque') }}">
                                <label for="no-cheque-input">No Cheque</label>
                            </div>
                        </div>
                        <div class="col-6 col-md-4">
                            <div class="form-floating form-floating-outline">
                                <input class="form-control" type="text" placeholder="Put Memo Here ...." id="memo-input"
                                    name="detail" value="{{ old('detail') }}">
                                <label for="memo-input">Memo</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- EXPENSE ITEMS --}}
            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-header bg-transparent border-bottom py-3 d-flex align-items-center justify-content-between">
                    <h6 class="card-title mb-0 fw-bold text-dark">
                        <i class="mdi mdi-format-list-bulleted me-2 text-primary fs-5"></i> Expense Items
                    </h6>
                    <span class="badge bg-label-secondary" id="items-count-badge">1 Item</span>
                </div>
                <div class="card-body p-0">
                    <div class="mb-0" data-repeater-list="group-a">
                        <div class="repeater-wrapper" data-repeater-item="">
                            <div class="position-relative border-bottom p-3">
                                <div class="row w-100">
                                    <div class="col-md-6 col-12 mb-md-0">
                                        <label for="account" class="mb-2 small text-muted">Account</label>
                                        <div class="form-floating form-floating-outline mb-2">
                                            <select id="account-1" class="select2 form-select invoice-item-account"
                                                data-allow-clear="true" name="account[]" data-id="1">
                                                <option> ---- Choose Account Here ---- </option>
                                                @foreach ($account as $accounts)
                                                    <option value="{{ $accounts->id }}"
                                                        data-memo="{{ $accounts->category }}">
                                                        {{ $accounts->code }} - {{ $accounts->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-12 mb-md-0 mb-3">
                                        <p class="mb-2 repeater-title small text-muted">Memo</p>
                                        <div class="form-floating form-floating-outline mb-2">
                                            <input type="text" class="form-control invoice-item-memo-label"
                                                placeholder="Choose Account first" id="memo-label-1"
                                                value="{{ old('memo[]', 'Choose Account first') }}" disabled>
                                            <input type="text" class="form-control invoice-item-memo"
                                                placeholder="Choose Account first" name="memo[]" id="memo-1"
                                                value="{{ old('memo[]', 'Choose Account first') }}" hidden>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-12 mb-md-0 mb-3">
                                        <p class="mb-2 repeater-title small text-muted">Amount</p>
                                        <div class="input-group input-group-merge" data-amount="1">
                                            <span class="input-group-text">Rp. </span>
                                            <div class="form-floating form-floating-outline">
                                                <input type="text" class="form-control invoice-item-amount-label"
                                                    id="amountLabel-1" data-id="1" name="harga"
                                                    placeholder="Put amount Here" data-type="currency" min="0"
                                                    pattern="^[0-9]\d{0,2}(\.\d{3})*$" @focus="focused = true"
                                                    @blur="focused = false" value="{{ old('amount[]') }}">
                                            </div>
                                            <input class="form-control invoice-item-amount" type="number"
                                                name="amount[]" id="amount-1" value="{{ old('amount[]') }}" hidden>
                                        </div>
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
            <a href="{{ route('expense.index') }}" class="btn btn-label-secondary">Cancel</a>
            <button :disabled="focused" type="submit" class="btn btn-primary shadow-sm px-4">
                <i class="mdi mdi-content-save me-1"></i> Save Expense
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

            function updateItemsCountBadge() {
                var count = $('.repeater-wrapper').length;
                $('#items-count-badge').text(count + (count === 1 ? ' Item' : ' Items'));
            }

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
