@extends('layouts.sales.app')
@section('title', 'Aging Report AP')
@section('content')
    <h4 class="fw-bold py-3 mb-4"> <span class="text-muted">Account Payable / Aging Report/</span> Invoice #123123 </h4>
    <div class="card mb-3">
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-6 mb-3">
                    <h4 class="mb-3">Purchase Aging</h4>
                    <div class="row">
                        <div class="col-6 mb-3">
                            Invoice Number
                        </div>
                        <div class="col-6 mb-3">
                            : <a class="text-black"
                                href="{{ route('invoice.show', $product->id) }}">{{ $product->invoice }}</a>
                        </div>
                        <div class="col-6 mb-3">
                            Invoice Date
                        </div>
                        <div class="col-6 mb-3">
                            : {{ $product->date }}
                        </div>
                        <div class="col-6 mb-3">
                            Supplier
                        </div>
                        <div class="col-6 mb-3">
                            : {{ $product->supp->supplier ?? $product->supplier }}
                        </div>
                        <div class="col-6 mb-3">
                            Info
                        </div>
                        <div class="col-6 mb-3">
                            : {{ $product->info }}
                        </div>
                    </div>
                </div>
                <div class="col-6 mb-3">
                    <div class="info text-end">
                        <p class="badge bg-label-danger text-danger rounded">Overdue</p>
                        <p>Days Past Due : {{ $diffDue < 0 ? abs($diffDue) : 0 }}</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer border">
            <div class="row mt-3">
                <div class="col-4">
                    <div class="card">
                        <div class="card-body bg-label-secondary">
                            <p>product Total</p>
                            <h5>Rp {{ number_format($product->total, 0, ',', '.') }}</h5>
                        </div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="card">
                        <div class="card-body bg-label-secondary">
                            <p>Paid to Date</p>
                            <h5>-</h5>
                        </div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="card">
                        <div class="card-body bg-label-secondary">
                            <p>Outstanding</p>
                            <h5 class="text-danger">Rp {{ number_format($product->total, 0, ',', '.') }}</h5>
                        </div>
                    </div>
                </div>
            </div>
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
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.css" />
@endpush

@push('after-script')
    <script src="{{ asset('assets') }}/vendor/libs/moment/moment.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/flatpickr/flatpickr.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/formvalidation/dist/js/FormValidation.min.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.js"></script>
@endpush

@push('page-script')
    <script src="{{ asset('assets') }}/js/tables-datatables-basic.js"></script>
    <script src="{{ asset('assets') }}/includes/table-sales-product-ar.js"></script>
    <script src="{{ asset('assets') }}/js/extended-ui-sweetalert2.js"></script>
@endpush

@push('script')
@endpush
