@extends('layouts.sales.app')
@section('title', 'Aging Report')
@section('no-container') @endsection
@section('content')
    <div class="container-fluid p-0" style="width: calc(100% - 10px); margin-right:5px;margin-left:5px;">
        <h4 class="fw-bold py-3 mb-4"> <span class="text-muted fw-normal">Account Receivable /</span> Aging Report</h4>
        {{-- <div class="row mb-3">
            <div class="col-3">
                <div class="card">
                    <div class="card-body">
                        <h5>INVOICE</h5>
                        <p>Total Penerimaan</p>
                        <h3>Rp {{ number_format(@$receipt, 0, ',', '.') }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-3">
                <div class="card">
                    <div class="card-body">
                        <h5>PAID</h5>
                        <p>Sudah DiBayar</p>
                        <h3>Rp {{ number_format(@$confirm, 0, ',', '.') }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-3">
                <div class="card">
                    <div class="card-body">
                        <h5>UNPAID</h5>
                        <p>Belum DiBayar</p>
                        <h3>Rp {{ number_format(@$unconfirm, 0, ',', '.') }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-3">
                <div class="card">
                    <div class="card-body">
                        <h5>DUE DATE</h5>
                        <p>Jatuh Tempo</p>
                        <h3>Rp {{ number_format(@$overdue, 0, ',', '.') }}</h3>
                    </div>
                </div>
            </div>
        </div> --}}
        {{-- Bartasss --}}


        <div class="nav-align-top mb-4">
            <ul class="nav nav-pills mb-3" role="tablist">
                <li class="nav-item" role="presentation">
                    <button type="button" class="nav-link active waves-effect waves-light" role="tab"
                        data-bs-toggle="tab" data-bs-target="#navs-pills-top-general" aria-controls="navs-pills-top-general"
                        aria-selected="true">
                        General
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button type="button" class="nav-link waves-effect waves-light" role="tab" data-bs-toggle="tab"
                        data-bs-target="#navs-pills-top-reftech" aria-controls="navs-pills-top-reftech"
                        aria-selected="false" tabindex="-1">
                        Reftech
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button type="button" class="nav-link waves-effect waves-light" role="tab" data-bs-toggle="tab"
                        data-bs-target="#navs-pills-top-kojisha" aria-controls="navs-pills-top-kojisha"
                        aria-selected="false" tabindex="-1">
                        Kojisha
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button type="button" class="nav-link waves-effect waves-light" role="tab" data-bs-toggle="tab"
                        data-bs-target="#navs-pills-top-ahmad" aria-controls="navs-pills-top-ahmad" aria-selected="false"
                        tabindex="-1">
                        Ahmad
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button type="button" class="nav-link waves-effect waves-light" role="tab" data-bs-toggle="tab"
                        data-bs-target="#navs-pills-top-rayi" aria-controls="navs-pills-top-rayi" aria-selected="false"
                        tabindex="-1">
                        Rayi
                    </button>
                </li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane fade show active" id="navs-pills-top-general" role="tabpanel">
                    <div class="row mb-3">
                        <div class="col-4">
                            <div class="card shadow-none bg-transparent border border-primary mb-3" data-bs-toggle="modal" data-bs-target="#detailOutstanding">
                                <div class="card-body">
                                    <h5 class="card-title">Total Outstanding</h5>
                                    <h2 class="card-text text-primary">Rp.
                                        {{ number_format($invoice->sum('amount', 0, '.', ',')) }}
                                    </h2>
                                    <div class="table-responsive text-nowrap border-top">
                                        <table class="table">
                                            <tbody class="table-border-bottom-0">
                                                <tr>
                                                    <td class="pe-5"><span class="text-heading">VAT</span></td>
                                                    <td class="ps-5 d-flex justify-content-end">
                                                        <span class="text-heading fw-semibold">Rp. 500.000.000</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="pe-5"><span class="text-heading">Non-VAT</span></td>
                                                    <td class="ps-5 d-flex justify-content-end">
                                                        <span class="text-heading fw-semibold">Rp. 200.000.000</span>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="card shadow-none bg-transparent border border-danger mb-3" data-bs-toggle="modal" data-bs-target="#detailOverdue">
                                <div class="card-body">
                                    <h5 class="card-title">Total Overdue</h5>
                                    <h2 class="card-text text-danger">Rp.
                                        {{ number_format($overdue->sum('amount', 0, '.', ',')) }}
                                    </h2>
                                    <div class="table-responsive text-nowrap border-top">
                                        <table class="table">
                                            <tbody class="table-border-bottom-0">
                                                <tr>
                                                    <td class="pe-5"><span class="text-heading">VAT</span></td>
                                                    <td class="ps-5 d-flex justify-content-end">
                                                        <span class="text-heading fw-semibold">Rp. 500.000.000</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="pe-5"><span class="text-heading">Non-VAT</span></td>
                                                    <td class="ps-5 d-flex justify-content-end">
                                                        <span class="text-heading fw-semibold">Rp. 200.000.000</span>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="card shadow-none bg-transparent border border-success mb-3" data-bs-toggle="modal" data-bs-target="#detailOnDue">
                                <div class="card-body">
                                    <h5 class="card-title">Total On Due</h5>
                                    <h2 class="card-text text-success">Rp.
                                        {{ number_format($ondue->sum('amount', 0, '.', ',')) }}
                                    </h2>
                                    <div class="table-responsive text-nowrap border-top">
                                        <table class="table">
                                            <tbody class="table-border-bottom-0">
                                                <tr>
                                                    <td class="pe-5"><span class="text-heading">VAT</span></td>
                                                    <td class="ps-5 d-flex justify-content-end">
                                                        <span class="text-heading fw-semibold">Rp. 500.000.000</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="pe-5"><span class="text-heading">Non-VAT</span></td>
                                                    <td class="ps-5 d-flex justify-content-end">
                                                        <span class="text-heading fw-semibold">Rp. 200.000.000</span>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-datatable table-responsive pt-0">
                        <table class="datatable-aging-report-ar table table-bordered">
                            <thead>
                                <tr>
                                    <th>Invoice</th>
                                    <th>Date</th>
                                    <th>No. PO</th>
                                    <th>Customer</th>
                                    <th>Total</th>
                                    <th>Due Date</th>
                                    <th>overdue</th>
                                    <th>VAT</th>
                                    <th>name</th>
                                    <th>reminder</th>
                                    <th>flag</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
                <div class="tab-pane fade" id="navs-pills-top-reftech" role="tabpanel">
                    <div class="row mb-3">
                        <div class="col-4">
                            <div class="card shadow-none bg-transparent border border-primary mb-3">
                                <div class="card-body">
                                    <h5 class="card-title">Total Outstanding</h5>
                                    <h2 class="card-text text-primary">Rp.
                                        {{ number_format($invoice->where('info', 'Reftech')->sum('amount', 0, '.', ',')) }}
                                    </h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="card shadow-none bg-transparent border border-danger mb-3">
                                <div class="card-body">
                                    <h5 class="card-title">Total Overdue</h5>
                                    <h2 class="card-text text-danger">Rp.
                                        {{ number_format($overdue->where('info', 'Reftech')->sum('amount', 0, '.', ',')) }}
                                    </h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="card shadow-none bg-transparent border border-success mb-3">
                                <div class="card-body">
                                    <h5 class="card-title">Total On Due</h5>
                                    <h2 class="card-text text-success">Rp.
                                        {{ number_format($ondue->where('info', 'Reftech')->sum('amount', 0, '.', ',')) }}
                                    </h2>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-datatable table-responsive pt-0">
                        <table class="datatable-aging-report-reftech table table-bordered">
                            <thead>
                                <tr>
                                    <th>Invoice</th>
                                    <th>Date</th>
                                    <th>No. PO</th>
                                    <th>Customer</th>
                                    <th>Total</th>
                                    <th>Due Date</th>
                                    <th>overdue</th>
                                    <th>VAT</th>
                                    <th>name</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
                <div class="tab-pane fade" id="navs-pills-top-kojisha" role="tabpanel">
                    <div class="row mb-3">
                        <div class="col-4">
                            <div class="card shadow-none bg-transparent border border-primary mb-3">
                                <div class="card-body">
                                    <h5 class="card-title">Total Outstanding</h5>
                                    <h2 class="card-text text-primary">Rp.
                                        {{ number_format($invoice->where('info', 'Kojisha')->sum('amount', 0, '.', ',')) }}
                                    </h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="card shadow-none bg-transparent border border-danger mb-3">
                                <div class="card-body">
                                    <h5 class="card-title">Total Overdue</h5>
                                    <h2 class="card-text text-danger">Rp.
                                        {{ number_format($overdue->where('info', 'Kojisha')->sum('amount', 0, '.', ',')) }}
                                    </h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="card shadow-none bg-transparent border border-success mb-3">
                                <div class="card-body">
                                    <h5 class="card-title">Total On Due</h5>
                                    <h2 class="card-text text-success">Rp.
                                        {{ number_format($ondue->where('info', 'Kojisha')->sum('amount', 0, '.', ',')) }}
                                    </h2>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-datatable table-responsive pt-0">
                        <table class="datatable-aging-report-kojisha table table-bordered">
                            <thead>
                                <tr>
                                    <th>Invoice</th>
                                    <th>Date</th>
                                    <th>No. PO</th>
                                    <th>Customer</th>
                                    <th>Total</th>
                                    <th>Due Date</th>
                                    <th>overdue</th>
                                    <th>VAT</th>
                                    <th>name</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
                <div class="tab-pane fade" id="navs-pills-top-ahmad" role="tabpanel">
                    <div class="row mb-3">
                        <div class="col-4">
                            <div class="card shadow-none bg-transparent border border-primary mb-3">
                                <div class="card-body">
                                    <h5 class="card-title">Total Outstanding</h5>
                                    <h2 class="card-text text-primary">Rp.
                                        {{ number_format($invoice->whereIn('id_sales', [2, 3, 4, 32])->sum('amount', 0, '.', ',')) }}
                                    </h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="card shadow-none bg-transparent border border-danger mb-3">
                                <div class="card-body">
                                    <h5 class="card-title">Total Overdue</h5>
                                    <h2 class="card-text text-danger">Rp.
                                        {{ number_format($overdue->whereIn('id_sales', [2, 3, 4, 32])->sum('amount', 0, '.', ',')) }}
                                    </h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="card shadow-none bg-transparent border border-warning mb-3">
                                <div class="card-body">
                                    <h5 class="card-title">Total On Due</h5>
                                    <h2 class="card-text text-success">Rp.
                                        {{ number_format($ondue->whereIn('id_sales', [2, 3, 4, 32])->sum('amount', 0, '.', ',')) }}
                                    </h2>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-datatable table-responsive pt-0">
                        <table class="datatable-aging-report-ahmad table table-bordered">
                            <thead>
                                <tr>
                                    <th>Invoice</th>
                                    <th>Date</th>
                                    <th>No. PO</th>
                                    <th>Customer</th>
                                    <th>Total</th>
                                    <th>Due Date</th>
                                    <th>overdue</th>
                                    <th>VAT</th>
                                    <th>name</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
                <div class="tab-pane fade" id="navs-pills-top-rayi" role="tabpanel">
                    <div class="row mb-3">
                        <div class="col-4">
                            <div class="card shadow-none bg-transparent border border-primary mb-3">
                                <div class="card-body">
                                    <h5 class="card-title">Total Outstanding</h5>
                                    <h2 class="card-text text-primary">Rp.
                                        {{ number_format($invoice->whereIn('id_sales', [1, 16, 23])->sum('amount', 0, '.', ',')) }}
                                    </h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="card shadow-none bg-transparent border border-danger mb-3">
                                <div class="card-body">
                                    <h5 class="card-title">Total Overdue</h5>
                                    <h2 class="card-text text-danger">Rp.
                                        {{ number_format($overdue->whereIn('id_sales', [1, 16, 23])->sum('amount', 0, '.', ',')) }}
                                    </h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="card shadow-none bg-transparent border border-warning mb-3">
                                <div class="card-body">
                                    <h5 class="card-title">Total On Due</h5>
                                    <h2 class="card-text text-success">Rp.
                                        {{ number_format($ondue->whereIn('id_sales', [1, 16, 23])->sum('amount', 0, '.', ',')) }}
                                    </h2>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-datatable table-responsive pt-0">
                        <table class="datatable-aging-report-rayi table table-bordered">
                            <thead>
                                <tr>
                                    <th>Invoice</th>
                                    <th>Date</th>
                                    <th>No. PO</th>
                                    <th>Customer</th>
                                    <th>Total</th>
                                    <th>Due Date</th>
                                    <th>overdue</th>
                                    <th>VAT</th>
                                    <th>name</th>
                                    <th>flag</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Batasss --}}
    </div>
    @include('components.modal.payment.outstanding')
    @include('components.modal.payment.overdue')
    @include('components.modal.payment.ondue')
@endsection()

@push('after-style')
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
    <link rel="stylesheet"
        href="{{ asset('assets') }}/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
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
    <script src="{{ asset('assets') }}/includes/table-ar-aging-report.js"></script>
    <script src="{{ asset('assets') }}/includes/table-ar-aging-report-reftech.js"></script>
    <script src="{{ asset('assets') }}/includes/table-ar-aging-report-kojisha.js"></script>
    <script src="{{ asset('assets') }}/includes/table-ar-aging-report-ahmad.js"></script>
    <script src="{{ asset('assets') }}/includes/table-ar-aging-report-rayi.js"></script>
    <script src="{{ asset('assets') }}/js/extended-ui-sweetalert2.js"></script>
@endpush
@push('script')
    <script>
        // Initialize Bootstrap tooltips using jQuery
        $(document).ready(function() {
            const initTooltips = () => {
                const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
                const tooltipList = [...tooltipTriggerList].map(el => new bootstrap.Tooltip(el));
            };
            initTooltips();
        });
    </script>
@endpush
