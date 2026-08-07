@extends('layouts.sales.app')
@section('title', 'Monitoring machine')
@section('content')
    @if (Auth::user()->role != 'Client')
        <h4>Machine {{ $machine->unit->brand }} {{ $machine->unit->unit->sku }}</h4>
        <div class="row">
            <div class="col-12 col-md-6">
                <div class="card mb-3">
                    <div class="card-body">
                        <h5> Daily </h5>
                        <div class="card-datatable table-responsive pt-0">
                            <table class="datatable-daily-month table table-striped">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th></th>
                                        <th>ID</th>
                                        <th>Month</th>
                                        <th>Button</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 mb-3">
                <div class="card mb-3">
                    <div class="card-body">
                        <h5> Weekly </h5>
                        <div class="card-datatable table-responsive pt-0">
                            <table class="datatable-monitoring-weekly table table-striped">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th></th>
                                        <th>ID</th>
                                        <th>Week</th>
                                        <th>First Date</th>
                                        <th>Last Date</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="card mb-3">
                    <div class="card-body">
                        <h5> Monthly </h5>
                        <div class="card-datatable table-responsive pt-0">
                            <table class="datatable-monitoring-monthly table table-striped">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th></th>
                                        <th>ID</th>
                                        <th>Month</th>
                                        <th>First Date</th>
                                        <th>Last Date</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <h4>Machine {{ $machine->unit->brand }} {{ $machine->unit->unit->sku }}</h4>
        <div class="row">
            <div class="col-12">
                <div class="card mb-3">
                    <div class="card-body">
                        <h5> Daily </h5>
                        <div class="card-datatable table-responsive pt-0">
                            <table class="datatable-daily-month table table-striped">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th></th>
                                        <th>ID</th>
                                        <th>Month</th>
                                        <th>Button</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
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
    <script src="{{ asset('assets') }}/includes/table-coordinator-compressor.js"></script>
    <script src="{{ asset('assets') }}/includes/table-coordinator-dryer.js"></script>
    <script src="{{ asset('assets') }}/includes/table-coordinator-month.js"></script>
    <script src="{{ asset('assets') }}/includes/table-coordinator-monthly.js"></script>
    <script src="{{ asset('assets') }}/includes/table-coordinator-weekly.js"></script>
@endpush
