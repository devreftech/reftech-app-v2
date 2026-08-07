@extends('layouts.sales.app')
@section('title', 'Monitoring machine')
@section('content')
    <h4 class="fw-bold py-3 mb-4">
        Monitoring Weekly Machine {{ $machine->unit->brand }} {{ $machine->unit->unit->sku }}
    </h4>
    <div class="card mb-3">
        <div class="card-datatable table-responsive pt-0">
            @if ($machine->unit->unit->unit != 'REFRIGERANT AIR DRYER')
                <table class="datatable-monitoring-weekly table table-striped">
                    <thead>
                        <tr>
                            <th></th>
                            <th></th>
                            <th>ID</th>
                            <th>Date</th>
                            <th>Voltage</th>
                            <th>A. Load</th>
                            <th>A. Idle</th>
                            <th>PIC</th>
                        </tr>
                    </thead>
                </table>
            @else
                <table class="datatable-monitoring-dryer-weekly table table-striped">
                    <thead>
                        <tr>
                            <th></th>
                            <th></th>
                            <th>ID</th>
                            <th>Date</th>
                            <th>Voltage</th>
                            <th>Ampere</th>
                            <th>Dew Point</th>
                            <th>Auto Drain</th>
                            <th>Pre</th>
                            <th>After</th>
                            <th>PIC</th>
                        </tr>
                    </thead>
                </table>
            @endif
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
    <script src="{{ asset('assets') }}/includes/table-monitoring-machine-weekly.js"></script>
    <script src="{{ asset('assets') }}/includes/table-monitoring-dryer-weekly.js"></script>
@endpush
