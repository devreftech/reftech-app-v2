@extends('layouts.sales.app')
@section('title', 'Data Unit Global')
@section('content')
    <div class="d-flex justify-content-between align-items-center py-3 mb-4">
        <h4 class="fw-bold mb-0">
            <span class="text-muted fw-light">Warehouse /</span> Unit Global
        </h4>
        @if (in_array(Auth::user()->role, ['Admin', 'Logistic']))
            <a data-bs-toggle="modal" data-bs-target="#createProduct">
                <button class="btn btn-primary btn-sm">
                    <i class="mdi mdi-plus me-1"></i> Add Unit
                </button>
            </a>
        @endif
    </div>

    <div class="card">
        <div class="card-header p-0 border-bottom">
            <ul class="nav nav-tabs" id="unitGlobalTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active px-4 py-3" data-bs-toggle="tab"
                        data-bs-target="#tab-compressor" type="button" role="tab">
                        Air Compressor Screw
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link px-4 py-3" id="btn-tab-piston"
                        data-bs-toggle="tab" data-bs-target="#tab-piston"
                        type="button" role="tab">
                        Piston
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link px-4 py-3" id="btn-tab-dryer"
                        data-bs-toggle="tab" data-bs-target="#tab-dryer"
                        type="button" role="tab">
                        Refrigerant Dryer
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link px-4 py-3" id="btn-tab-desiccant"
                        data-bs-toggle="tab" data-bs-target="#tab-desiccant"
                        type="button" role="tab">
                        Desiccant Dryer
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link px-4 py-3" id="btn-tab-filtration"
                        data-bs-toggle="tab" data-bs-target="#tab-filtration"
                        type="button" role="tab">
                        Filtration System
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link px-4 py-3" id="btn-tab-tank"
                        data-bs-toggle="tab" data-bs-target="#tab-tank"
                        type="button" role="tab">
                        Air Receiver Tank
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link px-4 py-3" id="btn-tab-booster"
                        data-bs-toggle="tab" data-bs-target="#tab-booster"
                        type="button" role="tab">
                        Booster Compressor
                    </button>
                </li>
            </ul>
        </div>

        <div class="tab-content">
            <div class="tab-pane fade show active" id="tab-compressor" role="tabpanel">
                <div class="card-datatable table-responsive pt-0">
                    <table class="datatable-unit-compressor table table-bordered">
                        <thead>
                            <tr>
                                <th class="text-center">SKU</th>
                                <th class="text-center">Brand</th>
                                <th class="text-center">Type</th>
                                <th class="text-center">Motor Power</th>
                                <th class="text-center">Max Pressure</th>
                                <th class="text-center">Air Capacity</th>
                                <th class="text-center">Connection</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>

            <div class="tab-pane fade" id="tab-piston" role="tabpanel">
                <div class="card-datatable table-responsive pt-0">
                    <table class="datatable-unit-piston table table-bordered">
                        <thead>
                            <tr>
                                <th class="text-center">SKU</th>
                                <th class="text-center">Brand</th>
                                <th class="text-center">Type</th>
                                <th class="text-center">Motor Power</th>
                                <th class="text-center">Max Pressure</th>
                                <th class="text-center">Air Capacity</th>
                                <th class="text-center">Connection</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>

            <div class="tab-pane fade" id="tab-dryer" role="tabpanel">
                <div class="card-datatable table-responsive pt-0">
                    <table class="datatable-unit-dryer table table-bordered">
                        <thead>
                            <tr>
                                <th class="text-center">SKU</th>
                                <th class="text-center">Brand</th>
                                <th class="text-center">Model</th>
                                <th class="text-center">FAD / Air Cap</th>
                                <th class="text-center">Refrigerant Type</th>
                                <th class="text-center">Voltage</th>
                                <th class="text-center">Connection</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>

            <div class="tab-pane fade" id="tab-desiccant" role="tabpanel">
                <div class="card-datatable table-responsive pt-0">
                    <table class="datatable-unit-desiccant table table-bordered">
                        <thead>
                            <tr>
                                <th class="text-center">SKU</th>
                                <th class="text-center">Brand</th>
                                <th class="text-center">FAD / Air Cap</th>
                                <th class="text-center">Refrigerant Type</th>
                                <th class="text-center">PDP</th>
                                <th class="text-center">Voltage</th>
                                <th class="text-center">Connection</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
            <div class="tab-pane fade" id="tab-filtration" role="tabpanel">
                <div class="card-datatable table-responsive pt-0">
                    <table class="datatable-unit-filtration table table-bordered">
                        <thead>
                            <tr>
                                <th class="text-center">SKU</th>
                                <th class="text-center">Brand</th>
                                <th class="text-center">Model</th>
                                <th class="text-center">FAD</th>
                                <th class="text-center">Connection</th>
                                <th class="text-center">Filtration</th>
                                <th class="text-center">Oil Content</th>
                                <th class="text-center">Grade</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
            <div class="tab-pane fade" id="tab-tank" role="tabpanel">
                <div class="card-datatable table-responsive pt-0">
                    <table class="datatable-unit-tank table table-bordered">
                        <thead>
                            <tr>
                                <th class="text-center">SKU</th>
                                <th class="text-center">Capacity</th>
                                <th class="text-center">Material</th>
                                <th class="text-center">Dimension</th>
                                <th class="text-center">Working Pressure</th>
                                <th class="text-center">Test Pressure</th>
                                <th class="text-center">Tipe</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
            <div class="tab-pane fade" id="tab-booster" role="tabpanel">
                <div class="card-datatable table-responsive pt-0">
                    <table class="datatable-unit-booster table table-bordered">
                        <thead>
                            <tr>
                                <th class="text-center">SKU</th>
                                <th class="text-center">Brand</th>
                                <th class="text-center">Model / Bare</th>
                                <th class="text-center">Inlet Pressure</th>
                                <th class="text-center">Outlet Pressure</th>
                                <th class="text-center">Inlet Capacity</th>
                                <th class="text-center">Outlet Capacity</th>
                                <th class="text-center">Motor Power</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @include('components.modal.warehouse.unit.form-global')
@endsection()

@push('after-style')
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/animate-css/animate.css">
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/formvalidation/dist/css/formValidation.min.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/select2/select2.css" />
@endpush

@push('after-script')
    <script src="{{ asset('assets') }}/vendor/libs/moment/moment.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/flatpickr/flatpickr.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/formvalidation/dist/js/FormValidation.min.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/select2/select2.js"></script>
@endpush

@push('page-script')
    <script src="{{ asset('assets') }}/js/tables-datatables-basic.js"></script>
    <script src="{{ asset('assets') }}/includes/table-unit-global.js"></script>
    <script src="{{ asset('assets') }}/includes/table-unit-piston-global.js"></script>
    <script src="{{ asset('assets') }}/includes/table-unit-dryer-global.js"></script>
    <script src="{{ asset('assets') }}/includes/table-unit-desiccant-global.js"></script>
    <script src="{{ asset('assets') }}/includes/table-unit-filtration-global.js"></script>
    <script src="{{ asset('assets') }}/includes/table-unit-tank-global.js"></script>
    <script src="{{ asset('assets') }}/includes/table-unit-booster-global.js"></script>
    <script src="{{ asset('assets') }}/js/forms-selects.js"></script>
@endpush

