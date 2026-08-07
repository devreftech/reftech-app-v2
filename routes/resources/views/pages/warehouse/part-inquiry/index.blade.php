@extends('layouts.sales.app')
@section('title', 'Part Inquiry')
@section('content')
    <h4 class="fw-bold py-3 mb-4">
        Part Inquiry
    </h4>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card mb-3">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Daftar Part Inquiry</h5>
            <a href="{{ route('part-inquiry.create') }}" class="btn btn-primary btn-sm">
                <i class="mdi mdi-plus me-1"></i> Add New Part
            </a>
        </div>
        <div class="card-datatable table-responsive pt-0">
            <table class="datatable-part-inquiry table table-bordered">
                <thead>
                    <tr>
                        <th>SKU</th>
                        <th>Brand</th>
                        <th>Part Number</th>
                        <th>Harga Jual</th>
                        <th>Vendor</th>
                        <th>Harga USD</th>
                        <th>Last Inquiry</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
@endsection

@push('after-style')
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css" />
@endpush

@push('after-script')
    <script src="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
@endpush

@push('page-script')
    <script src="{{ asset('assets') }}/includes/table-part-inquiry.js"></script>
@endpush
