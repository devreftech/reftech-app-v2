@extends('layouts.sales.app')
@section('title', 'Sales Order')
@section('no-container') @endsection
@section('content')
    <div class="container-fluid p-0" style="width: calc(100% - 10px); margin-right:5px;margin-left:5px;">
        <h4 class="fw-bold py-3 mb-4"> <span class="text-muted fw-normal">Sales Order</h4>
        <div class="nav-align-top mb-4">
            <ul class="nav nav-pills mb-3" role="tablist">
                <li class="nav-item" role="presentation">
                    <button type="button"
                        class="nav-link {{ auth::user()->role == 'Logistic' || auth::user()->role == 'Sales' ? 'active' : '' }} waves-effect waves-light"
                        role="tab" data-bs-toggle="tab" data-bs-target="#navs-pills-top-new"
                        aria-controls="navs-pills-top-new" aria-selected="true">
                        New order
                        @if (@$newCount >= 1)
                            <div class="badge bg-danger rounded-pill ms-auto">{{ $newCount }}</div>
                        @endif
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button type="button" class="nav-link waves-effect waves-light" role="tab" data-bs-toggle="tab"
                        data-bs-target="#navs-pills-top-list" aria-controls="navs-pills-top-list" aria-selected="true">
                        List
                        @if (@$listCount >= 1)
                            <div class="badge bg-danger rounded-pill ms-auto">{{ $listCount }}</div>
                        @endif
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button type="button" class="nav-link waves-effect waves-light" role="tab" data-bs-toggle="tab"
                        data-bs-target="#navs-pills-top-ready" aria-controls="navs-pills-top-ready" aria-selected="false"
                        tabindex="-1">
                        Ready Stock
                        @if (@$readyCount >= 1)
                            <div class="badge bg-danger rounded-pill ms-auto">{{ $readyCount }}</div>
                        @endif
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button type="button"
                        class="nav-link {{ auth::user()->role == 'ServiceM' ? 'active' : '' }} waves-effect waves-light"
                        role="tab" data-bs-toggle="tab" data-bs-target="#navs-pills-top-penjadwalan"
                        aria-controls="navs-pills-top-penjadwalan" aria-selected="false" tabindex="-1">
                        Penjadwalan Service
                        @if (@$jadwalCount >= 1)
                            <div class="badge bg-danger rounded-pill ms-auto">{{ $jadwalCount }}</div>
                        @endif
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button type="button" class="nav-link waves-effect waves-light" role="tab" data-bs-toggle="tab"
                        data-bs-target="#navs-pills-top-delivery" aria-controls="navs-pills-top-delivery"
                        aria-selected="false" tabindex="-1">
                        Delivery Proccess
                        @if (@$deliveryCount >= 1)
                            <div class="badge bg-danger rounded-pill ms-auto">{{ $deliveryCount }}</div>
                        @endif
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button type="button" class="nav-link waves-effect waves-light" role="tab" data-bs-toggle="tab"
                        data-bs-target="#navs-pills-top-retur" aria-controls="navs-pills-top-retur" aria-selected="false"
                        tabindex="-1">
                        Return
                        {{-- @if (@$noInvoiceCountNP >= 1)
                            <div class="badge bg-danger rounded-pill ms-auto">{{ $noInvoiceCountNP }}</div>
                        @endif --}}
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button type="button" class="nav-link waves-effect waves-light" role="tab" data-bs-toggle="tab"
                        data-bs-target="#navs-pills-top-delay" aria-controls="navs-pills-top-delay" aria-selected="false"
                        tabindex="-1">
                        Delayed Done
                        {{-- @if (@$noInvoiceCountNP >= 1)
                            <div class="badge bg-danger rounded-pill ms-auto">{{ $noInvoiceCountNP }}</div>
                        @endif --}}
                        @if (@$delayedCount >= 1)
                            <div class="badge bg-danger rounded-pill ms-auto">{{ $delayedCount }}</div>
                        @endif
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button type="button"
                        class="nav-link {{ auth::user()->role == 'Admin' ? 'active' : '' }} waves-effect waves-light"
                        role="tab" data-bs-toggle="tab" data-bs-target="#navs-pills-top-donenp"
                        aria-controls="navs-pills-top-donenp" aria-selected="false" tabindex="-1">
                        Done Non Project
                        @if (@$noInvoiceCountNP >= 1)
                            <div class="badge bg-danger rounded-pill ms-auto">{{ $noInvoiceCountNP }}</div>
                        @endif
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button type="button" class="nav-link waves-effect waves-light" role="tab" data-bs-toggle="tab"
                        data-bs-target="#navs-pills-top-donep" aria-controls="navs-pills-top-donep" aria-selected="false"
                        tabindex="-1">
                        Done Project
                        @if (@$noInvoiceCountP >= 1)
                            <div class="badge bg-danger rounded-pill ms-auto">{{ $noInvoiceCountP }}</div>
                        @endif
                    </button>
                </li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane fade show {{ auth::user()->role == 'Logistic' || auth::user()->role == 'Sales' ? 'active show' : '' }}"
                    id="navs-pills-top-new" role="tabpanel">
                    <div class="card-datatable pt-0">
                        <table
                            class="datatable-new-order-search{{ auth::user()->role == 'Sales' ? '' : '-admin' }} table table-bordered">
                            <thead>
                                @if (Auth::user()->role == 'Admin' || Auth::user()->role == 'Logistic')
                                    <tr>
                                        <th>No SO</th>
                                        <th>No PO</th>
                                        <th>Date</th>
                                        <th>Category</th>
                                        <th>Customer</th>
                                        <th>Description</th>
                                        <th>Status</th>
                                        <th>Payment</th>
                                        <th>area</th>
                                        <th>Delivery</th>
                                        <th>Sales</th>
                                        <th>Team</th>
                                    </tr>
                                @endif
                                @if (Auth::user()->role == 'Sales')
                                    <tr>
                                        <th>No SO</th>
                                        <th>Date</th>
                                        <th>PO No.</th>
                                        <th>Customer</th>
                                        <th>Part Desc</th>
                                        <th>Status</th>
                                        <th>Payment</th>
                                        <th>Delivery</th>
                                    </tr>
                                @endif
                            </thead>
                        </table>
                    </div>
                </div>
                <div class="tab-pane fade" id="navs-pills-top-list" role="tabpanel">
                    <div class="card-datatable pt-0">
                        <table
                            class="datatable-sales-list-search{{ auth::user()->role == 'Sales' ? '' : '-admin' }} table table-bordered">
                            <thead>
                                @if (Auth::user()->role == 'Admin' || Auth::user()->role == 'Logistic')
                                    <tr>
                                        <th>No SO</th>
                                        <th>No PO</th>
                                        <th>Date</th>
                                        <th>Category</th>
                                        <th>Customer</th>
                                        <th>Description</th>
                                        <th>Status</th>
                                        <th>Payment</th>
                                        <th>area</th>
                                        <th>Delivery</th>
                                        <th>Sales</th>
                                        <th>Team</th>
                                    </tr>
                                @endif
                                @if (Auth::user()->role == 'Sales')
                                    <tr>
                                        <th>Date</th>
                                        <th>PO No.</th>
                                        <th>Customer</th>
                                        <th>Part Desc</th>
                                        <th>Status</th>
                                        <th>Payment</th>
                                        <th>Delivery</th>
                                    </tr>
                                @endif
                            </thead>
                        </table>
                    </div>
                </div>
                <div class="tab-pane fade" id="navs-pills-top-ready" role="tabpanel">
                    <div class="card-datatable pt-0">
                        <table class="datatable-sales-list-ready table table-bordered">
                            <thead>
                                <tr>
                                    <th>No SO</th>
                                    <th>No PO</th>
                                    <th>Date</th>
                                    <th>Category</th>
                                    <th>Customer</th>
                                    <th>Description</th>
                                    <th>Status</th>
                                    <th>Payment</th>
                                    <th>area</th>
                                    <th>Delivery</th>
                                    <th>Sales</th>
                                    <th>Team</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
                <div class="tab-pane fade {{ auth::user()->role == 'ServiceM' ? 'active show' : '' }}"
                    id="navs-pills-top-penjadwalan" role="tabpanel">
                    <div class="card-datatable pt-0">
                        <table class="datatable-sales-list-jadwal table table-bordered">
                            <thead>
                                <tr>
                                    <th>No SO</th>
                                    <th>No PO</th>
                                    <th>Date</th>
                                    <th>Schedule</th>
                                    <th>Customer</th>
                                    <th>Description</th>
                                    <th>Status</th>
                                    <th>area</th>
                                    <th>Delivery</th>
                                    <th>Sales</th>
                                    <th>Team</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
                <div class="tab-pane fade" id="navs-pills-top-delivery" role="tabpanel">
                    <div class="card-datatable pt-0">
                        <table
                            class="datatable-sales-delivery-search{{ auth::user()->role == 'Sales' ? '' : '-admin' }} table table-bordered">
                            <thead>
                                <tr>
                                    <th>PO Date</th>
                                    @if (Auth::user()->role == 'Sales')
                                        <th>PO No.</th>
                                    @endif
                                    <th>Customer</th>
                                    <th>Part Desc</th>
                                    <th>Status</th>
                                    <th>Payment</th>
                                    <th>Delivery</th>
                                    @if (Auth::user()->role == 'Admin' || Auth::user()->role == 'Logistic')
                                        <th>Sales</th>
                                        <th>Team</th>
                                    @endif
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
                <div class="tab-pane fade" id="navs-pills-top-retur" role="tabpanel">
                    <div class="card-datatable pt-0">
                        <table class="datatable-sales-completed-retur table table-bordered">
                            <thead>
                                <tr>
                                    <th>No PO</th>
                                    <th>No Invoice</th>
                                    <th>PO Date</th>
                                    <th>Customer</th>
                                    <th>Part Desc</th>
                                    <th>Status</th>
                                    <th>Payment</th>
                                    <th>Delivery</th>
                                    <th>Sales</th>
                                    <th>Team</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
                <div class="tab-pane fade" id="navs-pills-top-delay" role="tabpanel">
                    <div class="card-datatable pt-0">
                        <table class="datatable-sales-search-delay table table-bordered">
                            <thead>
                                <tr>
                                    <th>No PO</th>
                                    <th>No Invoice</th>
                                    <th>PO Date</th>
                                    <th>Customer</th>
                                    <th>Part Desc</th>
                                    <th>Status</th>
                                    <th>Payment</th>
                                    <th>Delivery</th>
                                    <th>Sales</th>
                                    <th>Team</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
                <div class="tab-pane fade {{ auth::user()->role == 'Admin' ? 'active show' : '' }}"
                    id="navs-pills-top-donenp" role="tabpanel">
                    <div class="card-datatable pt-0">
                        <table
                            class="datatable-sales-completed-search{{ auth::user()->role == 'Sales' ? '' : '-admin' }}-non table table-bordered">
                            <thead>
                                <tr>
                                    @if (Auth::user()->role != 'Sales')
                                        <th>No PO</th>
                                        <th>No Invoice</th>
                                    @endif
                                    <th>PO Date</th>
                                    @if (Auth::user()->role == 'Sales')
                                        <th>PO No.</th>
                                    @endif
                                    <th>Customer</th>
                                    <th>Part Desc</th>
                                    <th>Status</th>
                                    <th>Payment</th>
                                    <th>Delivery</th>
                                    @if (Auth::user()->role != 'Sales')
                                        <th>Sales</th>
                                        <th>Team</th>
                                    @endif
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
                <div class="tab-pane fade" id="navs-pills-top-donep" role="tabpanel">
                    <div class="card-datatable pt-0">
                        <table
                            class="datatable-sales-completed-search{{ auth::user()->role == 'Sales' ? '' : '-admin' }}-project table table-bordered">
                            <thead>
                                <tr>
                                    @if (Auth::user()->role != 'Sales')
                                        <th>No PO</th>
                                        <th>No Invoice</th>
                                    @endif
                                    <th>PO Date</th>
                                    @if (Auth::user()->role == 'Sales')
                                        <th>PO No.</th>
                                    @endif
                                    <th>Customer</th>
                                    <th>Part Desc</th>
                                    <th>Status</th>
                                    <th>Payment</th>
                                    <th>Delivery</th>
                                    @if (Auth::user()->role != 'Sales')
                                        <th>Sales</th>
                                        <th>Team</th>
                                    @endif
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        {{-- Bataasss --}}
    </div>
    @foreach ($orders as $order)
        @include('components.modal.pending.jadwal.schedule')
    @endforeach
    @foreach ($schedules as $schedule)
        @include('components.modal.pending.jadwal.reschedule')
        @include('components.modal.pending.jadwal.dokumentasi')
    @endforeach
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
    {{-- <script src="{{ asset('assets') }}/js/tables-datatables-basic.js"></script> --}}
    <script src="{{ asset('assets') }}/js/tables-datatables-advanced.js"></script>
    <script src="{{ asset('assets') }}/js/forms-selects.js"></script>
    <script src="{{ asset('assets') }}/js/extended-ui-sweetalert2.js"></script>e
    {{-- datatable --}}
    <script src="{{ asset('assets') }}/includes/table-search-new-order.js"></script>
    <script src="{{ asset('assets') }}/includes/table-search-new-order-admin.js"></script>

    <script src="{{ asset('assets') }}/includes/table-search-sales-list.js"></script>
    <script src="{{ asset('assets') }}/includes/table-search-sales-list-admin.js"></script>

    <script src="{{ asset('assets') }}/includes/table-search-sales-ready.js"></script>
    <script src="{{ asset('assets') }}/includes/table-search-sales-retur.js"></script>
    <script src="{{ asset('assets') }}/includes/table-search-sales-jadwal.js"></script>
    <script src="{{ asset('assets') }}/includes/table-search-sales-delay.js"></script>

    <script src="{{ asset('assets') }}/includes/table-search-sales-delivery.js"></script>
    <script src="{{ asset('assets') }}/includes/table-search-sales-delivery-admin.js"></script>

    <script src="{{ asset('assets') }}/includes/table-search-sales-completed-non.js"></script>
    <script src="{{ asset('assets') }}/includes/table-search-sales-completed-non-admin.js"></script>
    <script src="{{ asset('assets') }}/includes/table-search-sales-completed-project.js"></script>
    <script src="{{ asset('assets') }}/includes/table-search-sales-completed-project-admin.js"></script>
@endpush

@push('script')
@endpush
