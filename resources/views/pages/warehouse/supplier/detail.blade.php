@extends('layouts.sales.app')
@section('title', 'Detail Suppliers')
@section('content')
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Supplier Details {{ $supplier->supplier }}
    </h4>
    <div class="row mb-4">
        <div class="col-md-12">
            <h5 class="fw-bold pb-1 mb-3">
                Details
            </h5>
            <div class="card">
                <div class="card-header pb-0">
                    <div class="text-end text-muted">
                        <a type="button" data-bs-toggle="modal" data-bs-target="#updateSupplier-{{ $supplier->id }}">
                            <button type="button" class="btn btn-sm btn-label-primary">Edit</button>
                        </a>
                        <a href="#" data-id="{{ $supplier->id }}"
                            class="btn btn-sm btn-label-danger delete-supplier">Delete</a>
                    </div>
                </div>
                <div class="card-body">
                    <p class="card-text">
                    <div class="row mb-1">
                        <div class="col-3">
                            Address
                        </div>
                        <div class="col-9">
                            : {{ $supplier->address }}
                        </div>
                    </div>
                    <div class="row mb-1">
                        <div class="col-3">
                            Area
                        </div>
                        <div class="col-9">
                            : {{ $supplier->area }}
                        </div>
                    </div>
                    <div class="row mb-1">
                        <div class="col-3">
                            Phone
                        </div>
                        <div class="col-9">
                            : {{ $supplier->phone }}
                        </div>
                    </div>
                    <div class="row mb-1">
                        <div class="col-3">
                            Email
                        </div>
                        <div class="col-9">
                            : {{ $supplier->email }}
                        </div>
                    </div>
                    <div class="row mb-1">
                        <div class="col-3">
                            Npwp
                        </div>
                        <div class="col-9">
                            : {{ $supplier->npwp }}
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-3">
                            Info
                        </div>
                        <div class="col-9">
                            : {{ $supplier->info }}
                        </div>
                    </div>
                    </p>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        
        <div class="card mb-3">
            <div class="card-datatable table-responsive pt-0">
                <table class="datatable-product-in-supplier table table-striped">
                    <thead>
                        <tr>
                            <th></th>
                            <th></th>
                            <th>Invoice</th>
                            <th>Product</th>
                            <th>Qty</th>
                            <th>Price</th>
                            <th>VAT</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
    @include('components.modal.warehouse.supplier.form')
@endsection()
@push('after-style')
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
    <link rel="stylesheet"
        href="{{ asset('assets') }}/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/animate-css/animate.css">
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-rowgroup-bs5/rowgroup.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/formvalidation/dist/css/formValidation.min.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/select2/select2.css" />
@endpush
@push('after-script')
    <script src="{{ asset('assets') }}/vendor/libs/moment/moment.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/flatpickr/flatpickr.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/formvalidation/dist/js/FormValidation.min.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/select2/select2.js"></script>
@endpush
@push('page-script')
    <script src="{{ asset('assets') }}/js/tables-datatables-basic.js"></script>
    <script src="{{ asset('assets') }}/includes/table-product-in-supplier.js"></script>
    <script src="{{ asset('assets') }}/js/extended-ui-sweetalert2.js"></script>
    <script src="{{ asset('assets') }}/js/forms-selects.js"></script>
@endpush
@push('script')
    <script>
        $(document).on('click', '.delete-supplier', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, Delete it!",
                customClass: {
                    confirmButton: "btn btn-primary me-3 waves-effect waves-light",
                    cancelButton: "btn btn-label-secondary waves-effect",
                },
                buttonsStyling: false,
            }).then(function(result) {
                if (result.value) {
                    $.ajax({
                        'url': '{{ url('supplier') }}/' + id,
                        'type': 'POST',
                        'data': {
                            '_method': 'DELETE',
                            '_token': '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response == 1) {
                                Swal.fire({
                                    icon: "success",
                                    title: "Deleteed!",
                                    text: "Your file has been Deleted.",
                                    customClass: {
                                        confirmButton: "btn btn-success waves-effect",
                                    },
                                })
                                window.setTimeout(function() {
                                    window.location.href = '/supplier/';
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
                        text: "Your Delete is cancelled :)",
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
