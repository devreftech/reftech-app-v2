@extends('layouts.sales.app')
@section('title', 'My Prospect')
@section('content')
    @if (Auth::user()->role != 'Sales')
        <div class="card mb-4">
            <div class="card-widget-separator-wrapper">
                <div class="card-body card-widget-separator">
                    <div class="row gy-4 gy-sm-1">
                        <div class="col-sm-6 col-lg-3">
                            <div
                                class="d-flex justify-content-between align-items-start card-widget-1 border-end pb-3 pb-sm-0">
                                <div>
                                    <p class="mb-2">Quotation</p>
                                    <h4 class="mb-2">Rp
                                        {{ number_format(Auth::user()->role == 'Admin' ? $forecastAdmin : $forecast, 2, ',', '.') }}
                                    </h4>
                                    <p class="mb-0"><span
                                            class="badge rounded-pill bg-label-success">{{ (Auth::user()->role == 'Admin' ? $quotationAdmin : $quotation)->whereIn('status', ['20', '30', '40', '60', '80'])->count() }}</span>
                                    </p>
                                </div>
                                <div class="avatar me-sm-4">
                                    <span class="avatar-initial rounded bg-label-secondary">
                                        <i class="mdi mdi-home-outline mdi-24px"></i>
                                    </span>
                                </div>
                            </div>
                            <hr class="d-none d-sm-block d-lg-none me-4">
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <div
                                class="d-flex justify-content-between align-items-start card-widget-2 border-end pb-3 pb-sm-0">
                                <div>
                                    <p class="mb-2">Hot Prospect</p>
                                    <h4 class="mb-2">Rp
                                        {{ number_format(Auth::user()->role == 'Admin' ? $prospectAdmin : $prospect, 2, ',', '.') }}
                                    </h4>
                                    <p class="mb-0"><span
                                            class="badge rounded-pill bg-label-success">{{ (Auth::user()->role == 'Admin' ? $quotationAdmin : $quotation)->where('status', '80')->count() }}</span>
                                    </p>
                                </div>
                                <div class="avatar me-lg-4">
                                    <span class="avatar-initial rounded bg-label-secondary">
                                        <i class="mdi mdi-laptop mdi-24px"></i>
                                    </span>
                                </div>
                            </div>
                            <hr class="d-none d-sm-block d-lg-none">
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <div
                                class="d-flex justify-content-between align-items-start border-end pb-3 pb-sm-0 card-widget-3">
                                <div>
                                    <p class="mb-2">Purchase Order</p>
                                    <h4 class="mb-2">Rp
                                        {{ number_format(Auth::user()->role == 'Admin' ? $poAdmin : $po, 2, ',', '.') }}
                                    </h4>
                                    <p class="mb-0"><span
                                            class="badge rounded-pill bg-label-success">{{ (Auth::user()->role == 'Admin' ? $quotationAdmin : $quotation)->where('status', '100')->count() }}</span>
                                    </p>
                                </div>
                                <div class="avatar me-sm-4">
                                    <span class="avatar-initial rounded bg-label-secondary">
                                        <i class="mdi mdi-wallet-giftcard mdi-24px"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <p class="mb-2">Loss Order</p>
                                    <h4 class="mb-2">Rp
                                        {{ number_format(Auth::user()->role == 'Admin' ? $lossAdmin : $loss, 2, ',', '.') }}
                                    </h4>
                                    <p class="mb-0"><span
                                            class="badge rounded-pill bg-label-danger">{{ (Auth::user()->role == 'Admin' ? $quotationAdmin : $quotation)->where('status', '0')->count() }}</span>
                                    </p>
                                </div>
                                <div class="avatar">
                                    <span class="avatar-initial rounded bg-label-secondary">
                                        <i class="mdi mdi-currency-usd mdi-24px"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if (Auth::user()->role != 'Sales')
            <div class="card mb-4 shadow-sm border-0">
                <div class="card-body px-4 py-4">

                    <!-- Header -->
                    <div class="mb-4 d-flex justify-content-between align-items-center">
                        <div class="header-content">
                            <h5 class="fw-semibold mb-1">Monthly Leads Distribution</h5>
                            <p class="text-muted">Akumulasi leads bulan berjalan · detail per minggu via dropdown</p>
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                Week {{ $selectedWeekNum }}
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                @foreach ($availableWeeks as $week)
                                    <li>
                                        <a class="dropdown-item {{ $week['week'] == $selectedWeekNum ? 'active' : '' }}"
                                           href="{{ route('prospect.index', ['week' => $week['week']]) }}">
                                            {{ $week['label'] }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>


                    <div class="row g-4 row-cols-1 row-cols-sm-2 row-cols-lg-5">

                        @foreach ($salesLeads as $sales)
                            @php
                                $count = $sales->monthly_leads;
                                $weekCount = $sales->weekly_leads;

                                if ($count <= 20) {
                                    $color = 'success';
                                    $bg = 'bg-success-subtle';
                                } elseif ($count <= 40) {
                                    $color = 'warning';
                                    $bg = 'bg-warning-subtle';
                                } else {
                                    $color = 'danger';
                                    $bg = 'bg-danger-subtle';
                                }
                            @endphp

                            <div class="col">
                                <div
                                    class="d-flex align-items-center justify-content-between p-3 rounded-3 border h-100 transition-hover">

                                    <!-- Left -->
                                    <div class="d-flex align-items-center">
                                        <div class="avatar me-3" data-bs-toggle="tooltip" data-bs-placement="top"
                                            title="{{ $sales->name }}" style="cursor: default;">

                                            @if ($sales->image)
                                                <img src="{{ url('') . '/' . $sales->image }}" class="rounded-circle"
                                                    width="46" height="46" style="object-fit:cover;">
                                            @else
                                                <div class="rounded-circle bg-label-primary d-flex align-items-center justify-content-center"
                                                    style="width:46px;height:46px;">
                                                    <span class="fw-bold text-primary">
                                                        {{ strtoupper(substr($sales->name, 0, 1)) }}
                                                    </span>
                                                </div>
                                            @endif

                                        </div>

                                        <div>
                                            <p class="mb-1 text-dark medium fw-medium">
                                                {{ $sales->name }}
                                            </p>

                                            <h4 class="mb-0 fw-bold text-{{ $color }}">
                                                {{ $count }}
                                                <span class="fs-6 fw-normal text-muted">Leads/Bulan</span>
                                            </h4>

                                            <p class="mb-0 text-muted" style="font-size:0.78rem;">
                                                Week {{ $selectedWeekNum }}: {{ $weekCount }} leads
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Right indicator dot -->
                                    <div class="rounded-circle {{ $bg }}" style="width:12px;height:12px;"></div>



                                </div>
                            </div>
                        @endforeach

                    </div>
                </div>
            </div>
        @endif

        <div class="card mb-3">
            <div class="card-datatable table-responsive pt-0">
                <table
                    class="datatable{{ Auth::user()->role == 'Admin' ? '-prospect-admin' : '-prospect' }} table table-striped">
                    <thead>
                        <tr>
                            <th></th>
                            <th></th>
                            <th>ID</th>
                            <th>Company</th>
                            <th>Pic</th>
                            <th>category</th>
                            <th>Kebutuhan</th>
                            <th>Date </th>
                            <th>Value </th>
                            <th>Status</th>
                            <th>Info</th>
                            <th>Sales</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    @elseif (Auth::user()->role == 'Sales')
        <div class="card mb-3">
            <div class="card-datatable table-responsive pt-0">
                <table class="datatable-prospect-sales table table-striped">
                    <thead>
                        <tr>
                            <th></th>
                            <th></th>
                            <th>ID</th>
                            <th>Company</th>
                            <th>Prospect</th>
                            <th>Date</th>
                            <th>Support</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
        <div class="card mb-3">
            <div class="card-datatable table-responsive pt-0">
                <table class="datatable-prospect-fu-sales table table-striped">
                    <thead>
                        <tr>
                            <th></th>
                            <th></th>
                            <th>ID</th>
                            <th>Company</th>
                            <th>Prospect</th>
                            <th>Date</th>
                            <th>Support</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
        @foreach ($prospects as $prospect)
            @include('components.modal.prospect.confirm')
        @endforeach
    @endif
    </div>
    @include('components.modal.client.support.form')
@endsection()

@push('after-style')
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/select2/select2.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
    <link rel="stylesheet"
        href="{{ asset('assets') }}/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/animate-css/animate.css">
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-rowgroup-bs5/rowgroup.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/formvalidation/dist/css/formValidation.min.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.css" />
@endpush

@push('after-script')
    <script src="{{ asset('assets') }}/vendor/libs/select2/select2.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/moment/moment.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/flatpickr/flatpickr.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/formvalidation/dist/js/FormValidation.min.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.js"></script>
@endpush

@push('page-script')
    <script src="{{ asset('assets') }}/js/extended-ui-sweetalert2.js"></script>
    <script src="{{ asset('assets') }}/js/tables-datatables-basic.js"></script>
    <script src="{{ asset('assets') }}/includes/table-prospect-support.js"></script>
    <script src="{{ asset('assets') }}/includes/table-prospect-support-admin.js"></script>
    <script src="{{ asset('assets') }}/includes/table-prospect-support-sales.js"></script>
    <script src="{{ asset('assets') }}/includes/table-prospect-support-fu-sales.js"></script>
@endpush

@push('script')
    <script>
        // Initialize Bootstrap tooltips using jQuery
        $(document).ready(function() {
            $('[data-bs-toggle="tooltip"]').tooltip();

            $('#selectArea').select2({
                placeholder: 'Area',
                dropdownParent: $('#createProspect'),
                width: '100%',
                minimumInputLength: 2,
                language: {
                    inputTooShort: function() { return 'Ketik minimal 2 karakter...'; },
                    searching: function() { return 'Mencari...'; },
                    noResults: function() { return 'Kota/Kabupaten tidak ditemukan'; }
                },
                ajax: {
                    url: '{{ route("kota.search") }}',
                    dataType: 'json',
                    delay: 300,
                    data: function(params) { return { q: params.term }; },
                    processResults: function(data) { return { results: data }; },
                    cache: true
                }
            });

            $('#selectArea').on('select2:open', function() {
                $('.select2-search__field').attr('placeholder', 'Masukkan Kota/Kabupaten');
            });
        });

        $(document).on('click', '#withQuote', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: "Are you sure With Quotation?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, With Quotation!",
                customClass: {
                    confirmButton: "btn btn-primary me-3 waves-effect waves-light",
                    cancelButton: "btn btn-label-secondary waves-effect",
                },
                buttonsStyling: false,
            }).then(function(result) {
                if (result.value) {
                    $.ajax({
                        'url': '{{ url('prospect') }}/' + 'with_quotation/' + id,
                        'type': 'POST',
                        'data': {
                            '_method': 'POST',
                            '_token': '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response == 1) {
                                Swal.fire({
                                    icon: "success",
                                    title: "Converted!",
                                    text: "Your file has been converted.",
                                    customClass: {
                                        confirmButton: "btn btn-success waves-effect",
                                    },
                                })
                                window.setTimeout(function() {
                                    window.location.href =
                                        '/prospect/create_quotation/' + id;
                                }, 2000);
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: 'Data Failed With Quotation!'
                                });
                            }
                        }
                    });
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    Swal.fire({
                        title: "Cancelled",
                        text: "You cancelled :)",
                        icon: "error",
                        customClass: {
                            confirmButton: "btn btn-success waves-effect",
                        },
                    });
                }
            });
        });
        $(document).on('click', '#withoutQuote', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: "Are you sure without Quotation?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, without Quotation!",
                customClass: {
                    confirmButton: "btn btn-primary me-3 waves-effect waves-light",
                    cancelButton: "btn btn-label-secondary waves-effect",
                },
                buttonsStyling: false,
            }).then(function(result) {
                if (result.value) {
                    $.ajax({
                        'url': '{{ url('prospect') }}/' + 'without_quotation/' + id,
                        'type': 'POST',
                        'data': {
                            '_method': 'POST',
                            '_token': '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response == 1) {
                                Swal.fire({
                                    icon: "success",
                                    title: "Converted!",
                                    text: "Your file has been converted.",
                                    customClass: {
                                        confirmButton: "btn btn-success waves-effect",
                                    },
                                })
                                window.setTimeout(function() {
                                    window.location.href = '/leads/detail/' + id;
                                }, 2000);
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: 'Data Failed With Quotation!'
                                });
                            }
                        }
                    });
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    Swal.fire({
                        title: "Cancelled",
                        text: "You cancelled :)",
                        icon: "error",
                        customClass: {
                            confirmButton: "btn btn-success waves-effect",
                        },
                    });
                }
            });
        });
    </script>
@endpush
