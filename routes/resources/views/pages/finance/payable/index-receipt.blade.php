@extends('layouts.sales.app')
@section('title', 'Payable')
@section('content')
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-normal">Account Payable /</span> Purchase Invoice
    </h4>
    <div class="container-fluid p-0" style="width: calc(100% - 10px); margin-right:5px;margin-left:5px;">
        <div class="row mb-3">
            <div class="col-4">
                <div class="card">
                    <div class="card-body">
                        <h5>PAYMENT RECEIPT</h5>
                        <p>Total Pembelian</p>
                        <h3>Rp {{ number_format(@$receipt, 0, ',', '.') }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-4">
                <div class="card">
                    <div class="card-body">
                        <h5>PAID</h5>
                        <p>Sudah Dibayar</p>
                        <h3>Rp {{ number_format(@$paid, 0, ',', '.') }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-4">
                <div class="card">
                    <div class="card-body">
                        <h5>UN-PAID</h5>
                        <p>Belum Dibayar</p>
                        <h3>Rp {{ number_format(@$unpaid, 0, ',', '.') }}</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="card mb-3">
            <div class="card-datatable table-responsive pt-0">
                <table class="datatable-sales-receipt-ap table table-bordered">
                    <thead>
                        <tr>
                            {{-- <th></th>
                        <th>ID</th> --}}
                            <th>No. Receipt</th>
                            <th>Date</th>
                            <th>Invoice</th>
                            <th>Supplier</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Info</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
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
@endpush

@push('after-script')
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
    <script src="{{ asset('assets') }}/js/tables-datatables-advanced.js"></script>
    <script src="{{ asset('assets') }}/includes/table-ap-receipt.js"></script>
@endpush

@push('script')
    <script>
        // Initialize Bootstrap tooltips using jQuery
        $(document).ready(function() {
            $('[data-bs-toggle="tooltip"]').tooltip();
        });

        $(document).on('click', '.delete-payable', function() {
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
                        'url': '{{ url('payable-acount') }}/' + id,
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
                                    window.location.href = '/payable-acount';
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
    </script>
@endpush
