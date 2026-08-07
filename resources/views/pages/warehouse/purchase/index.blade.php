@extends('layouts.sales.app')
@section('title', 'Req Purchase')
@section('content')
    <div class="container-fluid p-0" style="width: calc(100% - 10px); margin-right:5px;margin-left:5px;">
        <h4 class="fw-bold py-3 mb-4"> <span class="text-muted fw-normal">Request Purchase</h4>

        <div class="card mb-4">
            {{-- ── Top-level status tabs ─────────────────────────────────── --}}
            <div class="card-header p-0 border-bottom">
                <ul class="nav nav-tabs px-3 pt-2" id="purchaseRequestTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button type="button" class="nav-link active px-3 py-3" role="tab" data-bs-toggle="tab"
                            data-bs-target="#navs-pills-top-new" aria-controls="navs-pills-top-new"
                            aria-selected="true">
                            <i class="mdi mdi-file-plus-outline me-1"></i>New Purchase
                            @if (@$newCount >= 1)
                                <span class="badge bg-danger rounded-pill ms-1">{{ $newCount }}</span>
                            @endif
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button type="button" class="nav-link px-3 py-3" role="tab" data-bs-toggle="tab"
                            data-bs-target="#navs-pills-top-acc" aria-controls="navs-pills-top-acc"
                            aria-selected="false">
                            <i class="mdi mdi-clipboard-check-outline me-1"></i>Approved
                            @if (@$accCount >= 1)
                                <span class="badge bg-warning rounded-pill ms-1">{{ $accCount }}</span>
                            @endif
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button type="button" class="nav-link px-3 py-3" role="tab" data-bs-toggle="tab"
                            data-bs-target="#navs-pills-top-delivery" aria-controls="navs-pills-top-delivery"
                            aria-selected="false">
                            <i class="mdi mdi-truck-delivery-outline me-1"></i>On Delivery
                            @if (@$deliveryCount >= 1)
                                <span class="badge bg-info rounded-pill ms-1">{{ $deliveryCount }}</span>
                            @endif
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button type="button"
                            class="nav-link px-3 py-3 {{ auth::user()->role == 'ServiceM' ? 'active' : '' }}"
                            role="tab" data-bs-toggle="tab" data-bs-target="#navs-pills-top-done"
                            aria-controls="navs-pills-top-done" aria-selected="false">
                            <i class="mdi mdi-check-all me-1"></i>Done Purchase
                            @if (@$doneCount >= 1)
                                <span class="badge bg-success rounded-pill ms-1">{{ $doneCount }}</span>
                            @endif
                        </button>
                    </li>
                </ul>
            </div>

            <div class="tab-content">
                <div class="tab-pane fade active show p-3" id="navs-pills-top-new" role="tabpanel">
                    <div class="table-responsive">
                        <table class="datatable-purchase-request-new table table-bordered">
                            <thead>
                                <tr>
                                    <th>No PR</th>
                                    <th>No PO</th>
                                    <th>No SO</th>
                                    <th>Customer</th>
                                    <th>Item</th>
                                    <th>Qty</th>
                                    <th>Date</th>
                                    <th class="text-center">Sign</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
                <div class="tab-pane fade p-3" id="navs-pills-top-acc" role="tabpanel">
                    <div class="table-responsive">
                        <table class="datatable-purchase-request-acc table table-bordered">
                            <thead>
                                <tr>
                                    <th>No PR</th>
                                    <th>No PO</th>
                                    <th>No SO</th>
                                    <th>Customer</th>
                                    <th>Item</th>
                                    <th>Qty</th>
                                    <th>Date</th>
                                    <th class="text-center">Sign</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
                <div class="tab-pane fade p-3" id="navs-pills-top-delivery" role="tabpanel">
                    <div class="table-responsive">
                        <table class="datatable-purchase-request-delivery table table-bordered">
                            <thead>
                                <tr>
                                    <th>No PR</th>
                                    <th>No PO</th>
                                    <th>No SO</th>
                                    <th>Customer</th>
                                    <th>Item</th>
                                    <th>Qty</th>
                                    <th>Date</th>
                                    <th class="text-center">Sign</th>
                                    <th>Tipe Pembelian</th>
                                    <th>Cargo</th>
                                    <th>No Resi</th>
                                    <th>Tgl Pembelian</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
                <div class="tab-pane fade p-3" id="navs-pills-top-done" role="tabpanel">
                    <div class="table-responsive">
                        <table class="datatable-purchase-request-done table table-bordered">
                            <thead>
                                <tr>
                                    <th>No PR</th>
                                    <th>No PO</th>
                                    <th>No SO</th>
                                    <th>Customer</th>
                                    <th>Item</th>
                                    <th>Qty</th>
                                    <th>Date</th>
                                    <th class="text-center">Sign</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
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
    <script src="{{ asset('assets') }}/includes/table-purchase-request.js"></script>
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
