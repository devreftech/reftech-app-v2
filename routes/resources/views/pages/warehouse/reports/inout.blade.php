@extends('layouts.sales.app')
@section('title', 'Year Reports')
@section('content')
    <div class="card mb-3">
        <div class="card-header">
            <div class="d-flex justify-content-between">
                <div class="title">
                    <h4 class="fw-bold p-0 m-0">
                        Tahun {{ $year }}
                    </h4>
                    <p class="m-0">Report Product In - Out</p>
                </div>

                <div class="btn-group">
                    <button type="button" class="btn btn-outline-primary dropdown-toggle waves-effect waves-light"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        Select Year
                    </button>
                    <ul class="dropdown-menu" style="">
                        <li>
                            <a class="dropdown-item waves-effect"
                                href="{{ route('reports.yearly', 2024) }}">Year
                                {{ 2024 }}</a>
                        </li>
                        <li>
                            <a class="dropdown-item waves-effect"
                                href="{{ route('reports.yearly', 2025) }}">Year
                                {{ 2025 }}</a>
                        </li>
                        <li>
                            <a class="dropdown-item waves-effect"
                                href="{{ route('reports.yearly', 2026) }}">Year
                                {{ 2026 }}</a>
                        </li>
                        <li>
                            <a class="dropdown-item waves-effect"
                                href="{{ route('reports.yearly', 2027) }}">Year
                                {{ 2027 }}</a>
                        </li>
                        <li>
                            <a class="dropdown-item waves-effect"
                                href="{{ route('reports.yearly', 2028) }}">Year
                                {{ 2028 }}</a>
                        </li>
                        <li>
                            <a class="dropdown-item waves-effect"
                                href="{{ route('reports.yearly', 2029) }}">Year
                                {{ 2029 }}</a>
                        </li>
                        <li>
                            <a class="dropdown-item waves-effect"
                                href="{{ route('reports.yearly', 2030) }}">Year
                                {{ 2030 }}</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="card-datatable table-responsive pt-0">
            <table class="datatable-sales-reports-year table table-bordered">
                <thead class="text-center bg-label-secondary" style="color:white !important;">
                    <tr class="border-white">
                        <th rowspan="2"></th>
                        <th class="text-center border-white" style="border-color:white !important;" rowspan="2">SKU</th>
                        <th class="text-center border-white" style="border-color:white !important;" rowspan="2">G/r</th>
                        <th class="text-center border-white" style="border-color:white !important;" colspan="2">Semester 1</th>
                        <th class="text-center border-white" style="border-color:white !important;" colspan="2">Semester 2</th>
                        <th class="text-center border-white" style="border-color:white !important;" rowspan="2">Current Stock</th>
                    </tr>
                    <tr class="border-white">
                        <th class="text-center border-white" style="border-color:white !important;">Product In</th>
                        <th class="text-center border-white" style="border-color:white !important;">Product Out</th>
                        <th class="text-center border-white" style="border-color:white !important;">Product In</th>
                        <th class="text-center border-white" style="border-color:white !important;">Product Out</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    {{-- @include('pages.warehouse.reports.form') --}}
@endsection()

@push('style')
    
@endpush

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
    <script src="{{ asset('assets') }}/includes/table-sales-reports-year.js"></script>
@endpush
