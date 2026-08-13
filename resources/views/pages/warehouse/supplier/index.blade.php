@extends('layouts.sales.app')
@section('title', 'Supplier')
@section('content')
    <div class="d-flex align-items-center justify-content-between py-3 mb-1">
        <h4 class="fw-bold m-0">Supplier</h4>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createSupplier">
            <i class="mdi mdi-plus me-1"></i> Supplier
        </button>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card mb-3">
                <div class="card-datatable table-responsive pt-0">
                    <table class="datatable-supplier table table-bordered">
                        <thead>
                            <tr>
                                <th></th>
                                <th></th>
                                {{-- <th>ID</th> --}}
                                <th>supplier</th>
                                <th>Phone</th>
                                <th>Email</th>
                                <th>Area</th>
                                <th>Info</th>
                                {{-- <th></th> --}}
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @include('components.modal.warehouse.supplier.form')
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
    <script src="{{ asset('assets') }}/includes/table-supplier.js"></script>
@endpush
