@extends('layouts.sales.app')
@section('title', 'Monitoring machine')
@section('content')
<div class="card mb-3">
    <div class="card-body">
        <h5> Reports Monthly </h5>
        <div class="card-datatable table-responsive pt-0">
            <table class="datatable-monitoring-semester table table-bordered">
                <thead>
                    <tr>
                        <th></th>
                        <th>ID</th>
                        <th>Semester</th>
                        <th>Year</th>
                        <th>MMonth</th>
                    </tr>
                </thead>
            </table>
        </div>
        <a class="btn btn-primary btn-outline-secondary d-grid mb-3 waves-effect float-end" target="_blank"
            href="{{ route('monitoring.fajarPaper-summary-print', $month) }}">
            Download
        </a>
    </div>
</div>
    <div class="card mb-3">
        <div class="card-body">
            <h5> Summary Pekerjaan Service </h5>
            <div class="card-datatable table-responsive pt-0">
                <table class="datatable-monitoring-summary table table-bordered">
                    <thead>
                        <tr>
                            <th></th>
                            <th>ID</th>
                            <th>Date</th>
                            <th>Location</th>
                            <th>Tag</th>
                            <th>Machine</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                </table>
            </div>
            <a class="btn btn-primary btn-outline-secondary d-grid mb-3 waves-effect float-end"
                href="{{ route('summary.mainlog', \Carbon\Carbon::today()->month) }}">
                Visitor
            </a>
        </div>
    </div>
    <div class="card mb-3">
        <div class="card-body">
            <h5> On Process Inquiry </h5>
            <div class="card-datatable table-responsive pt-0">
                <table class="datatable-monitoring-quote table table-bordered">
                    <thead>
                        <tr>
                            <th></th>
                            <th>ID</th>
                            <th>Date</th>
                            <th>Location</th>
                            <th>Tag</th>
                            <th>Machine</th>
                            <th>Keterangan</th>
                            <th>No.Inquiry</th>
                        </tr>
                    </thead>
                </table>
            </div>
            <a class="btn btn-primary btn-outline-secondary d-grid mb-3 waves-effect float-end" target="_blank"
                href="{{ route('monitoring.fajarPaper-quote-print', $month) }}">
                Download
            </a>
        </div>
    </div>
    <div class="card mb-3">
        <div class="card-body">
            <h5> Pending Confirm By User </h5>
            <div class="card-datatable table-responsive pt-0">
                <table class="datatable-monitoring-reports table table-bordered">
                    <thead>
                        <tr>
                            <th></th>
                            <th>ID</th>
                            <th>Date</th>
                            <th>Location</th>
                            <th>Tag</th>
                            <th>Machine</th>
                            <th>Keterangan</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                </table>
            </div>
            <a class="btn btn-primary btn-outline-secondary d-grid mb-3 waves-effect float-end" target="_blank"
                href="{{ route('monitoring.fajarPaper-hold-print', $month) }}">
                Download
            </a>
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
    <script src="{{ asset('assets') }}/includes/table-monitoring-summary.js"></script>
    <script src="{{ asset('assets') }}/includes/table-monitoring-reports.js"></script>
    <script src="{{ asset('assets') }}/includes/table-monitoring-quote.js"></script>
    <script src="{{ asset('assets') }}/includes/table-monitoring-semester.js"></script>
@endpush

@push('script')
@endpush
