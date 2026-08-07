@extends('layouts.sales.app')
@section('title', 'Product In')
@section('content')
    <div class="container-fluid flex-grow-1 container-p-y p-0">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1 text-dark" style="font-family: 'Inter', sans-serif;">
                    {{ Auth::user()->role == 'Logistic' ? 'Good Receipt (Penerimaan Barang)' : 'Penerimaan Barang Masuk (Product In)' }}
                </h4>
                <p class="text-muted mb-0 small">
                    {{ Auth::user()->role == 'Logistic' ? 'Kelola dan verifikasi penerimaan fisik barang berdasarkan Purchase Request.' : 'Kelola dan verifikasi surat jalan (DO) serta invoice barang masuk.' }}
                </p>
            </div>
        </div>
    @if (Auth::user()->role == 'Admin' || Auth::user()->role == 'Accounting')
        <div class="row">
            <div class="col-12 col-md-6">
                <div class="card mb-3">
                    <div class="card-datatable table-responsive pt-0">
                        <table class="datatable-product-in-req-lokal table table-striped">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>ID</th>
                                    <th>No. Product In</th>
                                    <th>No DO</th>
                                    <th>Date</th>
                                    <th>VAT</th>
                                    <th>Qty</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="card mb-3">
                    <div class="card-datatable table-responsive pt-0">
                        <table class="datatable-product-in-req-import table table-striped">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>ID</th>
                                    <th>No. Product In</th>
                                    <th>No DO</th>
                                    <th>Date</th>
                                    <th>VAT</th>
                                    <th>Qty</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endif
    @if (Auth::user()->role == 'Logistic')
        <div class="row">
            <div class="col-12 col-md-6">
                <div class="card mb-3">
                    <div class="card-datatable table-responsive pt-0">
                        <table class="datatable-product-in-req-logistic table table-striped">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>ID</th>
                                    <th>No. Product In</th>
                                    <th>Supplier</th>
                                    <th>Category</th>
                                    <th>Qty</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th>No. Product In</th>
                                    <th>Supplier</th>
                                    <th>Category</th>
                                    <th>Qty</th>
                                    <th>Date</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="card mb-3">
                    <div class="card-datatable table-responsive pt-0">
                        <table class="datatable-product-in-logistic table table-striped">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>ID</th>
                                    <th>Invoice</th>
                                    <th>Supplier</th>
                                    <th>Category</th>
                                    <th>Qty</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endif
    @if (Auth::user()->role != 'Logistic')
        <div class="card mb-3">
            <div class="card-datatable table-responsive pt-0">
                <table class="datatable-product-in-lokal table table-striped">
                    <thead>
                        <tr>
                            <th></th>
                            <th></th>
                            <th>ID</th>
                            <th>Invoice</th>
                            <th>Supplier</th>
                            <th>Product</th>
                            <th>Qty</th>
                            <th>Price</th>
                            <th>VAT</th>
                            <th>Date</th>
                            <th></th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
        <div class="card mb-3">
            <div class="card-datatable table-responsive pt-0">
                <table class="datatable-product-in-import table table-striped">
                    <thead>
                        <tr>
                            <th></th>
                            <th></th>
                            <th>ID</th>
                            <th>Invoice</th>
                            <th>Supplier</th>
                            <th>Product</th>
                            <th>Qty</th>
                            <th>Price</th>
                            <th>VAT</th>
                            <th>Date</th>
                            <th></th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
        </div>
    @endif
    </div>
@endsection

@push('after-style')
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
    <link rel="stylesheet"
        href="{{ asset('assets') }}/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/animate-css/animate.css">
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-rowgroup-bs5/rowgroup.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/formvalidation/dist/css/formValidation.min.css" />
    
    <style>
        /* Premium Inter Font Styling */
        body, h4, h5, table, .card-title, .dataTables_wrapper, .modal-title, .form-label {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif !important;
        }
        
        /* Modern Flat Card Blocks (No Hover Animations) */
        .card {
            border: 1px solid rgba(24, 28, 33, 0.08) !important;
            box-shadow: 0 4px 18px 0 rgba(24, 28, 33, 0.03) !important;
            border-radius: 10px !important;
            overflow: hidden;
            background: #ffffff !important;
        }

        /* Datatable wrapper cleanups */
        .card-header {
            background-color: transparent !important;
            border-bottom: 1px solid rgba(24, 28, 33, 0.08) !important;
            padding: 1.2rem 1.5rem !important;
        }
        
        .card-title, .head-label-delay h5, .head-label h5 {
            font-weight: 700 !important;
            color: #2F3349 !important;
            font-size: 1.05rem !important;
            letter-spacing: -0.01em;
            margin: 0 !important;
        }

        /* Table styling details */
        .table {
            margin-top: 0 !important;
            margin-bottom: 0 !important;
        }
        
        .table thead th {
            background-color: #fcfcfd !important;
            color: #5D596C !important;
            font-weight: 600 !important;
            text-transform: uppercase !important;
            font-size: 0.76rem !important;
            letter-spacing: 0.04em;
            border-bottom: 1px solid rgba(24, 28, 33, 0.08) !important;
            padding: 12px 16px !important;
        }
        
        .table tbody td {
            padding: 12px 16px !important;
            color: #333333 !important;
            font-size: 0.86rem !important;
            border-bottom: 1px solid rgba(24, 28, 33, 0.05) !important;
            vertical-align: middle !important;
        }
        
        .table tbody tr:last-child td {
            border-bottom: 0 !important;
        }
        
        /* Custom link styling inside tables */
        .table tbody td a {
            color: #7367F0 !important;
            font-weight: 500;
            text-decoration: none;
            transition: color 0.15s ease-in-out;
        }
        .table tbody td a:hover {
            color: #5d51e8 !important;
            text-decoration: underline;
        }
        
        /* Custom badge styling */
        .badge.bg-label-warning {
            background-color: #FFF2E0 !important;
            color: #FF9F43 !important;
        }
        .badge.bg-label-info {
            background-color: #E0F7FC !important;
            color: #00CFE8 !important;
        }
        
        /* Custom search box styles */
        .dataTables_filter input {
            border: 1px solid rgba(24, 28, 33, 0.12) !important;
            border-radius: 6px !important;
            padding: 5px 12px !important;
            font-size: 0.84rem !important;
            outline: none !important;
            box-shadow: none !important;
        }
        .dataTables_filter input:focus {
            border-color: #7367F0 !important;
        }
        
        /* Export buttons cleanups */
        .btn-label-primary {
            background-color: #ECEAFE !important;
            color: #7367F0 !important;
            border: none !important;
            padding: 6px 12px !important;
            font-size: 0.82rem !important;
            font-weight: 500 !important;
            border-radius: 6px !important;
            box-shadow: none !important;
        }

        .btn-new {
            background-color: #7367F0 !important;
            color: #FFFFFF !important;
            border: none !important;
            padding: 6px 12px !important;
            font-size: 0.82rem !important;
            font-weight: 500 !important;
            border-radius: 6px !important;
            box-shadow: none !important;
        }
    </style>
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
    <script src="{{ asset('assets') }}/includes/table-product-in-lokal.js"></script>
    <script src="{{ asset('assets') }}/includes/table-product-in-import.js"></script>
    <script src="{{ asset('assets') }}/includes/table-product-in-req-lokal.js"></script>
    <script src="{{ asset('assets') }}/includes/table-product-in-req-import.js"></script>
    <script src="{{ asset('assets') }}/includes/table-product-in-req-logistic.js"></script>
    <script src="{{ asset('assets') }}/includes/table-product-in-logistic.js"></script>
@endpush
