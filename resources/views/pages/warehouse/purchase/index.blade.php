@extends('layouts.sales.app')
@section('title', 'Req Purchase')
@section('content')
    <div class="container-fluid p-0" style="width: calc(100% - 10px); margin-right:5px;margin-left:5px;">
        <div class="d-flex align-items-center justify-content-between py-3 mb-2">
            <div>
                <h4 class="fw-bold m-0 text-dark">Request Purchase</h4>
                <p class="text-muted small mb-0">Kelola dan pantau seluruh pengajuan pembelian barang</p>
            </div>
        </div>

        {{-- Stat summary cards matching Quotation page design --}}
        <div class="row row-cols-1 row-cols-sm-2 row-cols-xl-5 g-3 mb-4">
            <div class="col">
                <div class="card h-100 border-0 custom-stat-card">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-muted fw-semibold small text-uppercase tracking-wider">New PR</span>
                            <div class="avatar avatar-sm">
                                <span class="avatar-initial rounded-3 bg-label-primary shadow-xs">
                                    <i class="mdi mdi-file-plus-outline mdi-20px"></i>
                                </span>
                            </div>
                        </div>
                        <h4 class="mb-2 fw-bold text-dark">{{ $newCount }}</h4>
                        <div class="d-flex align-items-center gap-1">
                            <span class="badge bg-label-primary rounded-pill fw-semibold">{{ $newCount }}</span>
                            <span class="text-muted small">Menunggu ACC</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card h-100 border-0 custom-stat-card">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-muted fw-semibold small text-uppercase tracking-wider">Approved</span>
                            <div class="avatar avatar-sm">
                                <span class="avatar-initial rounded-3 bg-label-warning shadow-xs">
                                    <i class="mdi mdi-clipboard-check-outline mdi-20px"></i>
                                </span>
                            </div>
                        </div>
                        <h4 class="mb-2 fw-bold text-dark">{{ $accCount }}</h4>
                        <div class="d-flex align-items-center gap-1">
                            <span class="badge bg-label-warning rounded-pill fw-semibold">{{ $accCount }}</span>
                            <span class="text-muted small">Telah Disetujui</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card h-100 border-0 custom-stat-card">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-muted fw-semibold small text-uppercase tracking-wider">Purchase Order</span>
                            <div class="avatar avatar-sm">
                                <span class="avatar-initial rounded-3 bg-label-dark shadow-xs">
                                    <i class="mdi mdi-file-document-outline mdi-20px"></i>
                                </span>
                            </div>
                        </div>
                        <h4 class="mb-2 fw-bold text-dark">{{ $poCount }}</h4>
                        <div class="d-flex align-items-center gap-1">
                            <span class="badge bg-label-dark rounded-pill fw-semibold">{{ $poCount }}</span>
                            <span class="text-muted small">PO Terbit</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card h-100 border-0 custom-stat-card">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-muted fw-semibold small text-uppercase tracking-wider">On Delivery</span>
                            <div class="avatar avatar-sm">
                                <span class="avatar-initial rounded-3 bg-label-info shadow-xs">
                                    <i class="mdi mdi-truck-delivery-outline mdi-20px"></i>
                                </span>
                            </div>
                        </div>
                        <h4 class="mb-2 fw-bold text-dark">{{ $deliveryCount }}</h4>
                        <div class="d-flex align-items-center gap-1">
                            <span class="badge bg-label-info rounded-pill fw-semibold">{{ $deliveryCount }}</span>
                            <span class="text-muted small">Dalam Pengiriman</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card h-100 border-0 custom-stat-card">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-muted fw-semibold small text-uppercase tracking-wider">Done</span>
                            <div class="avatar avatar-sm">
                                <span class="avatar-initial rounded-3 bg-label-success shadow-xs">
                                    <i class="mdi mdi-check-all mdi-20px"></i>
                                </span>
                            </div>
                        </div>
                        <h4 class="mb-2 fw-bold text-dark">{{ $doneCount }}</h4>
                        <div class="d-flex align-items-center gap-1">
                            <span class="badge bg-label-success rounded-pill fw-semibold">{{ $doneCount }}</span>
                            <span class="text-muted small">Selesai</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card custom-main-card mb-4">
            {{-- ── Top-level status tabs ─────────────────────────────────── --}}
            <div class="card-header py-2 bg-transparent border-bottom">
                <ul class="nav nav-tabs card-header-tabs border-0 m-0" id="purchaseRequestTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button type="button" class="nav-link active px-3 py-2 fw-semibold" role="tab" data-bs-toggle="tab"
                            data-bs-target="#navs-pills-top-new" aria-controls="navs-pills-top-new"
                            aria-selected="true">
                            <i class="mdi mdi-file-plus-outline me-1"></i>New Purchase
                            @if (@$newCount >= 1)
                                <span class="badge bg-danger rounded-pill ms-1">{{ $newCount }}</span>
                            @endif
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button type="button" class="nav-link px-3 py-2 fw-semibold" role="tab" data-bs-toggle="tab"
                            data-bs-target="#navs-pills-top-acc" aria-controls="navs-pills-top-acc"
                            aria-selected="false">
                            <i class="mdi mdi-clipboard-check-outline me-1"></i>Approved
                            @if (@$accCount >= 1)
                                <span class="badge bg-warning rounded-pill ms-1">{{ $accCount }}</span>
                            @endif
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button type="button" class="nav-link px-3 py-2 fw-semibold" role="tab" data-bs-toggle="tab"
                            data-bs-target="#navs-pills-top-po" aria-controls="navs-pills-top-po"
                            aria-selected="false">
                            <i class="mdi mdi-file-document-outline me-1"></i>Purchase Order
                            @if (@$poCount >= 1)
                                <span class="badge bg-dark rounded-pill ms-1">{{ $poCount }}</span>
                            @endif
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button type="button" class="nav-link px-3 py-2 fw-semibold" role="tab" data-bs-toggle="tab"
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
                            class="nav-link px-3 py-2 fw-semibold {{ auth::user()->role == 'ServiceM' ? 'active' : '' }}"
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

            <div class="tab-content p-0">
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
                <div class="tab-pane fade p-3" id="navs-pills-top-po" role="tabpanel">
                    <div class="table-responsive">
                        <table class="datatable-purchase-order table table-bordered">
                            <thead>
                                <tr>
                                    <th>No PO</th>
                                    <th>No PR</th>
                                    <th>Customer</th>
                                    <th>Item</th>
                                    <th>Date</th>
                                    <th>Status</th>
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
                                    <th>No PO (Client)</th>
                                    <th>No SO</th>
                                    <th>Customer</th>
                                    <th>Item</th>
                                    <th>Qty</th>
                                    <th>Date</th>
                                    <th>Pengiriman</th>
                                    <th>Tgl Pembelian</th>
                                    <th class="text-center">Sign</th>
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

    <style>
        /* Modern Clean Card Styles matching Quotation page */
        .custom-stat-card,
        .custom-main-card,
        .card,
        .modern-card {
            border: 1px solid rgba(226, 232, 240, 0.8) !important;
            box-shadow: 0 2px 6px 0 rgba(67, 89, 113, 0.06), 0 0 1px 0 rgba(67, 89, 113, 0.15) !important;
            border-radius: 0.75rem !important;
            background-color: #ffffff;
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        }

        .custom-stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 14px 0 rgba(67, 89, 113, 0.1) !important;
        }

        /* Refined tab header */
        .card-header-tabs .nav-link {
            color: #64748b;
            border: none !important;
            border-bottom: 2px solid transparent !important;
            border-radius: 0 !important;
            transition: all 0.2s ease;
        }

        .card-header-tabs .nav-link:hover {
            color: #3b82f6;
        }

        .card-header-tabs .nav-link.active {
            color: #2563eb !important;
            background-color: transparent !important;
            border-bottom: 2px solid #2563eb !important;
        }

        /* Clean datatables headers & rows */
        .table thead th {
            background-color: #f8fafc !important;
            color: #475569 !important;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-weight: 600;
            border-bottom: 1px solid #e2e8f0 !important;
        }

        .table td {
            font-size: 0.875rem;
            vertical-align: middle;
        }
    </style>
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
