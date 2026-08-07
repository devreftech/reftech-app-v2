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

    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Clients /</span> Leads
    </h4>

    @if (Auth::user()->role == 'Sales')
        <div class="d-flex justify-content-end">
            <button class="btn btn-secondary btn-primary" tabindex="0" type="button" data-bs-target="#createLeads"
                data-bs-toggle="modal">
                <span>
                    <i class="mdi mdi-plus me-sm-1"></i>
                    <span class="d-none d-sm-inline-block">Add New
                        Leads</span>
                </span>
            </button>
        </div>
        <div class="nav-align-top mb-4">
            <ul class="nav nav-pills mb-3" role="tablist">
                <li class="nav-item" role="presentation">
                    <button type="button" class="nav-link waves-effect waves-light active" role="tab"
                        data-bs-toggle="tab" data-bs-target="#navs-pills-top-leads" aria-controls="navs-pills-top-leads"
                        aria-selected="true">
                        Leads
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button type="button" class="nav-link waves-effect waves-light" role="tab" data-bs-toggle="tab"
                        data-bs-target="#navs-pills-top-cust" aria-controls="navs-pills-top-cust" aria-selected="true">
                        customer
                        {{-- @if (@$accCount >= 1)
                            <div class="badge bg-danger rounded-pill ms-auto">{{ $accCount }}</div>
                        @endif --}}
                    </button>
                </li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane fade active show" id="navs-pills-top-leads" role="tabpanel">
                    <div class="card-datatable pt-0">
                        <table class="datatable-leads-search table table-striped">
                            <thead>
                                <tr>
                                    <th>Company</th>
                                    <th>R/U</th>
                                    <th>Status</th>
                                    <th>Address</th>
                                    <th>Lass Contact</th>
                                    <th>Next FU</th>
                                    <th>Flag</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
                <div class="tab-pane fade" id="navs-pills-top-cust" role="tabpanel">
                    <div class="card-datatable pt-0">
                        <table class="datatable-customer-search table table-striped" id="dataTableCrm">
                            <thead>
                                <tr>
                                    <th>Company</th>
                                    <th>R/U</th>
                                    <th>Status</th>
                                    <th>Address</th>
                                    <th>Note</th>
                                    <th>Lass Contact</th>
                                    <th>Next FU</th>
                                    <th>Flag</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        {{-- @if (Auth::user()->id == '1' || Auth::user()->id == '16' || Auth::user()->id == '23')
            <div class="card">
                <div class="card-datatable table-responsive pt-0">
                    <table class="datatable-leads-info table table-striped">
                        <thead>
                            <tr>
                                <th></th>
                                <th></th>
                                <th>ID</th>
                                <th>Company</th>
                                <th>Address</th>
                                <th>Status</th>
                                <th>Last Contact</th>
                                <th>Next Follow Up</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        @else
            <div class="card">
                <div class="card-datatable table-responsive pt-0">
                    <table class="datatable-leads table table-striped">
                        <thead>
                            <tr>
                                <th></th>
                                <th></th>
                                <th>ID</th>
                                <th>Company</th>
                                <th>Address</th>
                                <th>Status</th>
                                <th>Last Contact</th>
                                <th>Next Follow Up</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        @endif --}}
    @elseif(Auth::user()->role == 'Admin' || Auth::user()->role == 'Technician')
        <div class="card">
            <div class="card-datatable table-responsive pt-0">
                <table class="datatable-leads-admin table table-striped">
                    <thead>
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
    @endif
    @include('pages.sales.clients.leads.form')
    {{-- @foreach ($client as $clients)
        @include('pages.sales.activities.form')
    @endforeach --}}
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
