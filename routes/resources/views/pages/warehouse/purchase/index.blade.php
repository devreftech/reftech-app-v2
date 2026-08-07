@extends('layouts.sales.app')
@section('title', 'Req Purchase')
@section('content')
    <div class="container-fluid p-0" style="width: calc(100% - 10px); margin-right:5px;margin-left:5px;">
        <h4 class="fw-bold py-3 mb-4"> <span class="text-muted fw-normal">Request Purchase</h4>
        <div class="nav-align-top mb-4">
            <ul class="nav nav-pills mb-3" role="tablist">
                <li class="nav-item" role="presentation">
                    <button type="button" class="nav-link waves-effect waves-light active" role="tab"
                        data-bs-toggle="tab" data-bs-target="#navs-pills-top-new" aria-controls="navs-pills-top-new"
                        aria-selected="true">
                        New Purchase
                        @if (@$newCount >= 1)
                            <div class="badge bg-danger rounded-pill ms-auto">{{ $newCount }}</div>
                        @endif
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button type="button" class="nav-link waves-effect waves-light" role="tab" data-bs-toggle="tab"
                        data-bs-target="#navs-pills-top-acc" aria-controls="navs-pills-top-acc" aria-selected="true">
                        Acc
                        @if (@$accCount >= 1)
                            <div class="badge bg-danger rounded-pill ms-auto">{{ $accCount }}</div>
                        @endif
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button type="button" class="nav-link waves-effect waves-light" role="tab" data-bs-toggle="tab"
                        data-bs-target="#navs-pills-top-delivery" aria-controls="navs-pills-top-delivery"
                        aria-selected="false" tabindex="-1">
                        On Delivery
                        @if (@$deliveryCount >= 1)
                            <div class="badge bg-danger rounded-pill ms-auto">{{ $deliveryCount }}</div>
                        @endif
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button type="button"
                        class="nav-link {{ auth::user()->role == 'ServiceM' ? 'active' : '' }} waves-effect waves-light"
                        role="tab" data-bs-toggle="tab" data-bs-target="#navs-pills-top-done"
                        aria-controls="navs-pills-top-done" aria-selected="false" tabindex="-1">
                        Done Purchase
                        @if (@$doneCount >= 1)
                            <div class="badge bg-danger rounded-pill ms-auto">{{ $doneCount }}</div>
                        @endif
                    </button>
                </li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane fade active show" id="navs-pills-top-new" role="tabpanel">
                    <div class="card-datatable pt-0">
                        <table class="datatable-purchase-request-new table table-striped">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>No PO</th>
                                    <th>No SO</th>
                                    <th>Item</th>
                                    <th>Customer</th>
                                    <th>Qty</th>
                                    <th>Note</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
                <div class="tab-pane fade" id="navs-pills-top-acc" role="tabpanel">
                    <div class="card-datatable pt-0">
                        <table class="datatable-purchase-request-acc table table-striped">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>No PO</th>
                                    <th>No SO</th>
                                    <th>Item</th>
                                    <th>Customer</th>
                                    <th>Qty</th>
                                    <th>Note</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
                <div class="tab-pane fade" id="navs-pills-top-delivery" role="tabpanel">
                    <div class="card-datatable pt-0">
                        <table class="datatable-purchase-request-delivery table table-striped">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>No PO</th>
                                    <th>No SO</th>
                                    <th>Item</th>
                                    <th>Customer</th>
                                    <th>Qty</th>
                                    <th>Note</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
                <div class="tab-pane fade" id="navs-pills-top-done" role="tabpanel">
                    <div class="card-datatable pt-0">
                        <table class="datatable-purchase-request-done table table-striped">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>No PO</th>
                                    <th>No SO</th>
                                    <th>Item</th>
                                    <th>Customer</th>
                                    <th>Qty</th>
                                    <th>Note</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        {{-- Bataasss --}}
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
    <script src="{{ asset('assets') }}/includes/table-purchase-request-new.js"></script>
    <script src="{{ asset('assets') }}/includes/table-purchase-request-acc.js"></script>
    <script src="{{ asset('assets') }}/includes/table-purchase-request-delivery.js"></script>
    <script src="{{ asset('assets') }}/includes/table-purchase-request-done.js"></script>
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
