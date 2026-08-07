@extends('layouts.sales.app')
@section('title', 'Sales Urgent Order')
@section('content')
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Sales /</span> Urgent Order (SUO)
    </h4>
    <div class="card">
        <div class="card-datatable table-responsive pt-0">
            <table class="datatable-suo-sales table table-striped">
                <thead>
                    <tr>
                        <th>No. SUO</th>
                        <th>Company</th>
                        <th>PIC</th>
                        <th>Status</th>
                        <th>No. Invoice Booking</th>
                        <th>Tanggal</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
@endsection

@push('after-style')
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables.bootstrap5.css"/>
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css"/>
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css"/>
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.css"/>
@endpush

@push('after-script')
    <script src="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.js"></script>
@endpush

@push('page-script')
    <script src="{{ asset('assets') }}/includes/table-suo-sales.js"></script>
@endpush
