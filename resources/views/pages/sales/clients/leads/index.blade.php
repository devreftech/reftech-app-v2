@extends('layouts.sales.app')
@section('title', 'My Leads & Customers')
@section('content')

    @if (Session::has('message'))
        <div class="bs-toast toast toast-placement-ex m-2 fade top-0 end-0 hide" role="alert" aria-live="assertive"
            aria-atomic="true" data-bs-delay="2000">
            <div class="toast-header">
                <i class="mdi mdi-check-circle me-2 text-success"></i>
                <div class="me-auto fw-semibold">Berhasil</div>
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
                        <span class="badge bg-label-primary fs-6 px-3 py-2">
                            <i class="mdi mdi-account-group-outline me-1"></i> Directory Client
                        </span>
                    </div>
                    <h4 class="fw-bold mb-0 text-dark">Leads & Customer Management</h4>
                    <small class="text-muted">
                        Kelola data calon pelanggan (Leads) dan pelanggan aktif (Customer) secara efisien.
                    </small>
                </div>

                @if (Auth::user()->role == 'Sales')
                    <button type="button" class="btn btn-primary waves-effect waves-light rounded-pill px-4"
                        data-bs-toggle="modal" data-bs-target="#createLeads">
                        <i class="mdi mdi-plus me-1"></i> Tambah Leads
                    </button>
                @endif
            </div>
        </div>
    </div>

    @if (Auth::user()->role == 'Sales')
        <!-- Tab Layout Concept like payment-index/invoice -->
        <div class="card card-minimalist mb-4">
            <div class="card-header card-minimalist-header py-2">
                <ul class="nav nav-tabs card-header-tabs border-0 m-0 flex-nowrap overflow-auto" id="crm-tab-nav" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active fw-semibold" data-bs-toggle="tab" data-bs-target="#navs-pills-top-leads" type="button" role="tab">
                            <i class="mdi mdi-account-group-outline me-1 text-primary"></i> Leads
                            <span class="badge rounded-pill bg-primary ms-1" id="badge-leads">-</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-semibold" data-bs-toggle="tab" data-bs-target="#navs-pills-top-cust" type="button" role="tab">
                            <i class="mdi mdi-account-check-outline me-1 text-success"></i> Customer
                            <span class="badge rounded-pill bg-success ms-1" id="badge-customers">-</span>
                        </button>
                    </li>
                </ul>
            </div>

            <div class="card-body p-0">
                <div class="tab-content border-0 p-0 m-0">
                    <div class="tab-pane fade show active p-3" id="navs-pills-top-leads" role="tabpanel">
                        <div class="card-datatable table-responsive pt-0">
                            <table class="datatable-leads-search table table-bordered" data-badge="badge-leads">
                                <thead class="table-light">
                                    <tr>
                                        <th>Company</th>
                                        <th>R/U</th>
                                        <th>Status</th>
                                        <th>Address</th>
                                        <th>Last Contact</th>
                                        <th>Next FU</th>
                                        <th>Flag</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane fade p-3" id="navs-pills-top-cust" role="tabpanel">
                        <div class="card-datatable table-responsive pt-0">
                            <table class="datatable-customer-search table table-bordered" id="dataTableCrm" data-badge="badge-customers">
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
    @elseif(Auth::user()->role == 'Admin' || Auth::user()->role == 'Technician')
        <div class="card card-minimalist">
            <div class="card-header card-minimalist-header py-3">
                <h5 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                    <i class="mdi mdi-shield-account-outline text-primary"></i> Data Leads All Sales
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="tab-pane fade show active p-3">
                    <div class="card-datatable table-responsive pt-0">
                        <table class="datatable-leads-admin table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th>ID</th>
                                    <th>Company</th>
                                    <th>Address</th>
                                    <th>Status</th>
                                    <th>Last Contact</th>
                                    <th>Next Follow Up</th>
                                    <th>Assigned</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endif
    @include('pages.sales.clients.leads.form')
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
        .nav-tabs .nav-link {
            border-radius: 6px 6px 0 0;
            font-weight: 500;
        }
        .nav-tabs .nav-link.active {
            border-color: #e0e2e8 #e0e2e8 #fff !important;
            background-color: #ffffff;
            font-weight: 600;
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
    <script src="{{ asset('assets') }}/includes/table-leads.js"></script>
    <script src="{{ asset('assets') }}/includes/table-leads-search.js"></script>
    <script src="{{ asset('assets') }}/includes/table-customer-search.js"></script>
    <script src="{{ asset('assets') }}/includes/table-leads-info.js"></script>
    <script src="{{ asset('assets') }}/includes/table-leads-admin.js"></script>
@endpush

@push('script')
    <script>
        // Initialize Bootstrap tooltips using jQuery
        $(document).ready(function() {
            $('[data-toggle="tooltip"]').tooltip();

            $('#dataTableCrm').on('change', '.status-dropdown', function() {
                var selectedValue = $(this).val();
                var rowId = $(this).data('id');
                var csrfToken = $('meta[name="csrf-token"]').attr('content');

                console.log('id = ' + rowId);


                $.ajax({
                    type: 'POST',
                    url: '/existing/update-status/' + rowId,
                    data: {
                        status: selectedValue,
                        _token: csrfToken
                    },
                    success: function(response) {
                        console.log('Perubahan status berhasil dikirim ke server');
                        // Handle response jika perlu
                    },
                    error: function(error) {
                        console.error('Gagal mengirim permintaan ke server:', error);
                        // Handle error jika perlu
                    }
                });
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
