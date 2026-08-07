@extends('layouts.sales.app')
@section('title', 'Sales Invoice AR')
@section('no-container') @endsection
@section('content')
    <div class="container-fluid p-0" style="width: calc(100% - 10px); margin-right:5px;margin-left:5px;">
        <h4 class="fw-bold py-3 mb-4"> <span class="text-muted">Account Recieveable /</span> Sales Invoice</h4>
        {{-- <div class="row mb-3">
            <div class="col-4">
                <div class="card">
                    <div class="card-body">
                        <h5>Invoice</h5>
                        <p>Total Invoice</p>
                        <h3>Rp {{ number_format(@$fullInvoice, 0, ',', '.') }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-4">
                <div class="card">
                    <div class="card-body">
                        <h5>PAID</h5>
                        <p>Total Paid</p>
                        <h3>Rp {{ number_format(@$fullPayment, 0, ',', '.') }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-4">
                <div class="card">
                    <div class="card-body">
                        <h5>UNPAID</h5>
                        <p>Total Outstanding</p>
                        <h3>Rp {{ number_format(@$sisa, 0, ',', '.') }}</h3>
                    </div>
                </div>
            </div>
        </div> --}}
        <div class="card mb-3">
            <div class="card-datatable table-responsive pt-0">
                <table class="datatable-sales-invoice-rayi table table-striped">
                    <thead>
                        <tr>
                            {{-- <th></th> --}}
                            {{-- <th>ID</th> --}}
                            <th>Invoice No.</th>
                            <th>No PO.</th>
                            <th>Date</th>
                            <th>Company</th>
                            <th>Total Invoice</th>
                            <th>Advance Payment</th>
                            <th>Outstanding</th>
                            <th>Status</th>
                            <th>Sales</th>
                            <th>Flag</th>
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
    <script src="{{ asset('assets') }}/js/tables-datatables-basic.js"></script>
    <script src="{{ asset('assets') }}/includes/table-ar-sales-invoice-rayi.js"></script>
    <script src="{{ asset('assets') }}/js/extended-ui-sweetalert2.js"></script>
@endpush

@push('script')
@endpush
