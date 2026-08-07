@extends('layouts.sales.app')
@section('title', 'Return')
@section('content')
    <h4 class="fw-bold py-3 mb-4">
        Return
    </h4>
    <div class="card mb-3">
        <div class="card-datatable table-responsive pt-0">
            <table
                class="datatable-sales-completed-retur table table-striped">
                <thead>
                    <tr>
                        <th>No Return</th>
                        {{-- <th>No PO</th> --}}
                        <th>Company</th>
                        <th>Status</th>
                        <th>Sales</th>
                        <th>Date PO</th>
                        <th>Date Return</th>
                    </tr>   
                </thead>
            </table>
        </div>
    </div>
    {{-- <div class="card mb-3">
        <div class="card-datatable table-responsive pt-0">
            <table
                class="datatable-request-return table table-striped">
                <thead>
                    <tr>
                        <th></th>
                        <th></th>
                        <th>ID</th>
                        <th>No Return</th>
                        <th>Company</th>
                        <th>Note</th>
                        <th>Total</th>
                        <th>Sales</th>
                        <th>Date</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    
    <div class="card mb-3">
        <div class="card-datatable table-responsive pt-0">
            <table
                class="datatable-return table table-striped">
                <thead>
                    <tr>
                        <th></th>
                        <th></th>
                        <th>ID</th>
                        <th>No Return</th>
                        <th>Company</th>
                        <th>Note</th>
                        <th>Total</th>
                        <th>Sales</th>
                        <th>Date</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div> --}}
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
    <script src="{{ asset('assets') }}/includes/table-search-retur.js"></script>
    {{-- <script src="{{ asset('assets') }}/includes/table-return.js"></script> --}}
@endpush
