@extends('layouts.sales.app')
@section('title', 'My Quotation')
@section('content')
    <div class="card mb-4">
        <div class="card-widget-separator-wrapper">
            <div class="card-body card-widget-separator">
                <div class="row gy-4 gy-sm-1">
                    <div class="col-sm-6 col-lg-3">
                        <div class="d-flex justify-content-between align-items-start card-widget-1 border-end pb-3 pb-sm-0">
                            <div>
                                <p class="mb-2">Quotation</p>
                                <h4 class="mb-2">Rp {{ number_format(Auth::user()->role == "Admin" ? $forecastAdmin : $forecast, 2, ',', '.') }}</h4>
                                <p class="mb-0"><span
                                        class="badge rounded-pill bg-label-success">{{ (Auth::user()->role == "Admin" ? $quotationAdmin : $quotation)->whereIn('status', ['20', '30', '40', '60', '80'])->count() }}</span>
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
                        <div class="d-flex justify-content-between align-items-start card-widget-2 border-end pb-3 pb-sm-0">
                            <div>
                                <p class="mb-2">Hot Prospect</p>
                                <h4 class="mb-2">Rp {{ number_format(Auth::user()->role == "Admin" ? $prospectAdmin : $prospect, 2, ',', '.') }}</h4>
                                <p class="mb-0"><span
                                        class="badge rounded-pill bg-label-success">{{ (Auth::user()->role == "Admin" ? $quotationAdmin : $quotation)->where('status', '80')->count() }}</span>
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
                        <div class="d-flex justify-content-between align-items-start border-end pb-3 pb-sm-0 card-widget-3">
                            <div>
                                <p class="mb-2">Purchase Order</p>
                                <h4 class="mb-2">Rp {{ number_format(Auth::user()->role == "Admin" ? $poAdmin : $po, 2, ',', '.') }}</h4>
                                <p class="mb-0"><span
                                        class="badge rounded-pill bg-label-success">{{ (Auth::user()->role == "Admin" ? $quotationAdmin : $quotation)->where('status', '100')->count() }}</span>
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
                                <h4 class="mb-2">Rp {{ number_format(Auth::user()->role == "Admin" ? $lossAdmin : $loss, 2, ',', '.') }}</h4>
                                <p class="mb-0"><span
                                        class="badge rounded-pill bg-label-danger">{{ (Auth::user()->role == "Admin" ? $quotationAdmin : $quotation)->where('status', '0')->count() }}</span>
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
    <div class="card mb-3">
        <div class="card-datatable table-responsive pt-0">
            <table class="datatable{{Auth::user()->role == "Admin" ? '-quotation-admin-unit' : '-quotation-unit'}} table table-striped">
                <thead>
                    <tr>
                        <th></th>
                        <th></th>
                        <th>ID</th>
                        <th>Quote No.</th>
                        <th>Company</th>
                        <th>Total Price</th>
                        <th>Description</th>
                        <th>Date Quotation</th>
                        {{-- <th>Date Expired</th> --}}
                        @if (Auth::user()->role == 'Sales')
                        <th>Status</th>
                        @endif
                        <th>Stats</th>
                        @if (Auth::user()->role == 'Admin')
                            <th>Assign</th>
                        @endif
                    </tr>
                </thead>
            </table>
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
@endpush

@push('after-script')
    <script src="{{ asset('assets') }}/vendor/libs/moment/moment.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/flatpickr/flatpickr.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/formvalidation/dist/js/FormValidation.min.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
@endpush

@push('page-script')
    <script src="{{ asset('assets') }}/js/tables-datatables-basic.js"></script>
    <script src="{{ asset('assets') }}/includes/table-quotation-unit.js"></script>
    <script src="{{ asset('assets') }}/includes/table-quotation-admin-unit.js"></script>
@endpush

@push('script')
    <script>
        // Initialize Bootstrap tooltips using jQuery
        $(document).ready(function() {
            $('[data-bs-toggle="tooltip"]').tooltip();
        });
    </script>
@endpush
