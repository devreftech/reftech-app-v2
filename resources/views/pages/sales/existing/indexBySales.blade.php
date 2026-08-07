@extends('layouts.sales.app')
@section('title', 'My Leads')
@section('content')

    @if (Session::has('message'))
        <div class="bs-toast toast toast-placement-ex m-2 fade top-0 end-0 hide" role="alert" aria-live="assertive"
            aria-atomic="true" data-bs-delay="2000">
            <div class="toast-header">
                <i class="mdi mdi-home me-2 text-success"></i>
                <div class="me-auto fw-semibold">Successfully</div>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">{{ Session::get('message') }}</div>
        </div>
    @endif

    <!-- Header Banner Card -->
    <div class="card clean-card mb-4">
        <div class="card-body p-4">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div>
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <span class="badge bg-label-success fs-6 px-3 py-2">
                            <i class="mdi mdi-account-check-outline me-1"></i> Directory Customer
                        </span>
                    </div>
                    <h4 class="fw-bold mb-0 text-dark">Customer by Sales</h4>
                    <small class="text-muted">
                        Pantau data customer tiap sales secara terpusat.
                    </small>
                </div>
            </div>
        </div>
    </div>

    <div class="card card-minimalist mb-4">
        <div class="card-header card-minimalist-header py-2">
            <ul class="nav nav-pills card-header-pills border-0 m-0 flex-nowrap overflow-auto" id="cbs-sales-tab-nav"
                data-default-sales-id="{{ $sales->first()->id ?? '' }}" role="tablist">
                @foreach ($sales as $sale)
                    <li class="nav-item" role="presentation">
                        <button type="button"
                            class="nav-link fw-semibold waves-effect waves-light select-sales {{ $loop->first ? 'active' : '' }}"
                            aria-selected="true" data-id="{{ $sale->id }}">
                            {{ $sale->name }}
                            <span class="badge rounded-pill bg-label-success ms-1">{{ $customersCountBySales[$sale->id] ?? 0 }}</span>
                        </button>
                    </li>
                @endforeach
            </ul>
        </div>

        <div class="card-body p-0">
            <div class="tab-content border-0 p-0 m-0">
                <div class="tab-pane fade active show p-3" id="navs-pills-top-customers" role="tabpanel">

                    <ul class="nav nav-tabs mb-3" id="cbs-status-tab-nav" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#cbs-tab-active" type="button">
                                <i class="mdi mdi-check-circle-outline me-1"></i>Active
                                <span class="badge rounded-pill bg-success ms-1" id="cbs-badge-active">-</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#cbs-tab-non-active" type="button">
                                <i class="mdi mdi-close-circle-outline me-1"></i>Non Active
                                <span class="badge rounded-pill bg-warning ms-1" id="cbs-badge-non-active">-</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#cbs-tab-bangkrupt" type="button">
                                <i class="mdi mdi-alert-circle-outline me-1"></i>Bangkrupt
                                <span class="badge rounded-pill bg-danger ms-1" id="cbs-badge-bangkrupt">-</span>
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content border-0 p-0 m-0">
                        {{-- Sub-tab: Active --}}
                        <div class="tab-pane fade show active" id="cbs-tab-active">
                            <div class="card-datatable table-responsive pt-0">
                                <table class="datatable-customers-by-sales-active table table-bordered" data-badge="cbs-badge-active">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Company</th>
                                            <th>R/U</th>
                                            <th>Status</th>
                                            <th>Address</th>
                                            <th>Note</th>
                                            <th>Last Contact</th>
                                            <th>Next FU</th>
                                            <th>Flag</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>

                        {{-- Sub-tab: Non Active --}}
                        <div class="tab-pane fade" id="cbs-tab-non-active">
                            <div class="card-datatable table-responsive pt-0">
                                <table class="datatable-customers-by-sales-non-active table table-bordered" data-badge="cbs-badge-non-active">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Company</th>
                                            <th>R/U</th>
                                            <th>Status</th>
                                            <th>Address</th>
                                            <th>Note</th>
                                            <th>Last Contact</th>
                                            <th>Next FU</th>
                                            <th>Flag</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>

                        {{-- Sub-tab: Bangkrupt --}}
                        <div class="tab-pane fade" id="cbs-tab-bangkrupt">
                            <div class="card-datatable table-responsive pt-0">
                                <table class="datatable-customers-by-sales-bangkrupt table table-bordered" data-badge="cbs-badge-bangkrupt">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Company</th>
                                            <th>R/U</th>
                                            <th>Status</th>
                                            <th>Address</th>
                                            <th>Note</th>
                                            <th>Last Contact</th>
                                            <th>Next FU</th>
                                            <th>Flag</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection

@push('after-style')
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
    <link rel="stylesheet"
        href="{{ asset('assets') }}/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/flatpickr/flatpickr.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/animate-css/animate.css">
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-rowgroup-bs5/rowgroup.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/formvalidation/dist/css/formValidation.min.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.css" />
    <style>
        .card-minimalist {
            border: 1px solid #e0e2e8 !important;
            box-shadow: none !important;
            border-radius: 12px;
        }
        .card-minimalist-header {
            border-bottom: 1px solid #e0e2e8 !important;
            background-color: #fafbfe;
            border-top-left-radius: 12px !important;
            border-top-right-radius: 12px !important;
        }
        .card-header-pills .nav-link {
            border-radius: 20px;
            font-weight: 500;
            padding: 0.4rem 1rem;
            margin-right: 0.35rem;
            color: #697a8d;
        }
        .card-header-pills .nav-link.active {
            background-color: #696cff;
            color: #fff;
        }
        .card-header-pills .nav-link.active .badge {
            background-color: rgba(255, 255, 255, 0.25) !important;
            color: #fff !important;
        }
    </style>
@endpush

@push('after-script')
    <script src="{{ asset('assets') }}/vendor/libs/moment/moment.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/flatpickr/flatpickr.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/formvalidation/dist/js/FormValidation.min.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.js"></script>
@endpush

@push('page-script')
    <script src="{{ asset('assets') }}/js/extended-ui-sweetalert2.js"></script>
    <script src="{{ asset('assets') }}/js/tables-datatables-advanced.js"></script>
    <script src="{{ asset('assets') }}/includes/table-customer-by-sales.js"></script>
@endpush

@push('script')
    <script>
        // Initialize Bootstrap tooltips using jQuery
        $(document).ready(function() {
            $('[data-toggle="tooltip"]').tooltip();

            $('.select-sales').on('click', function() {
                $('.select-sales').removeClass('active');
                $(this).addClass('active');
                let id = $(this).data('id');

                reloadCustomersBySales(id);
            });
        });

        $(document).on('click', '.delete-data-leads', function() {
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
                        'url': '{{ url('leads') }}/' + id,
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
                                    location.reload();
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
            // Swal.fire({
            //     title: "Are you sure?",
            //     text: "You won't be able to revert this!",
            //     icon: "warning",
            //     showCancelButton: true,
            //     confirmButtonColor: "#3085d6",
            //     cancelButtonColor: "#d33",
            //     confirmButtonText: "Yes, delete it!"
            // }).then((result) => {
            //     if (result.isConfirmed) {
            //         $.ajax({
            //             'url': '{{ url('leads') }}/' + id,
            //             'type': 'POST',
            //             'data': {
            //                 '_method': 'DELETE',
            //                 '_token': '{{ csrf_token() }}'
            //             },
            //             success: function(response) {
            //                 if (response == 1) {
            //                     Swal.fire({
            //                         title: "Deleted!",
            //                         text: "Your file has been deleted.",
            //                         icon: "success"
            //                     })
            //                     window.setTimeout(function() {
            //                         location.reload();
            //                     }, 2000);
            //                 } else {
            //                     Swal.fire({
            //                         icon: 'error',
            //                         title: 'Oops...',
            //                         text: 'Data Failed to Delete!'
            //                     });
            //                 }
            //             }
            //         });
            //     }
            // });
        });
    </script>
@endpush
