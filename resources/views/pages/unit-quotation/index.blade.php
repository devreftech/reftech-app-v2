@extends('layouts.sales.app')
@section('title', 'Penawaran Unit')
@section('content')
    <div class="d-flex justify-content-between align-items-center py-3 mb-4">
        <h4 class="fw-bold mb-0">
            <span class="text-muted fw-light">Sales /</span> Penawaran Unit
        </h4>
        <a href="{{ route('unit-quotation.create') }}">
            <button class="btn btn-primary btn-sm">
                <i class="mdi mdi-plus me-1"></i> Buat Penawaran
            </button>
        </a>
    </div>

    <div class="card">
        <div class="card-datatable table-responsive">
            <table class="datatable-unit-quotation table table-bordered">
                <thead>
                    <tr>
                        <th class="text-center">No. Quotation</th>
                        <th>Client</th>
                        <th>Description</th>
                        <th class="text-center">Date</th>
                        <th class="text-center">Total</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
@endsection

@push('after-style')
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
@endpush

@push('after-script')
    <script src="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
@endpush

@push('page-script')
    <script src="{{ asset('assets') }}/includes/table-unit-quotation.js"></script>
@endpush
