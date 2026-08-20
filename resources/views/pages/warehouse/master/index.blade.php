@extends('layouts.sales.app')
@section('title', 'Data Product')
@section('content')
    <h4 class="fw-bold py-3 mb-4">
        Product
    </h4>
    <div class="card mb-4">
        <div class="card-widget-separator-wrapper">
            <div class="card-body card-widget-separator">
                <div class="row gy-4 gy-sm-1">
                    <div class="col-sm-6 col-lg-3">
                        <div class="d-flex justify-content-between align-items-start card-widget-1 border-end pb-3 pb-sm-0">
                            <div>
                                <p class="mb-1 text-muted">Fast Moving</p>
                                <h4 class="mb-2 text-success fw-bold" id="kpi-fast-moving">0</h4>
                                <p class="mb-0"><span class="badge rounded-pill bg-label-success">Keluar &le; 60 Hari</span></p>
                            </div>
                            <div class="avatar me-sm-4">
                                <span class="avatar-initial rounded bg-label-success">
                                    <i class="mdi mdi-flash mdi-24px"></i>
                                </span>
                            </div>
                        </div>
                        <hr class="d-none d-sm-block d-lg-none me-4">
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <div class="d-flex justify-content-between align-items-start card-widget-2 border-end pb-3 pb-sm-0">
                            <div>
                                <p class="mb-1 text-muted">Slow Moving</p>
                                <h4 class="mb-2 text-warning fw-bold" id="kpi-slow-moving">0</h4>
                                <p class="mb-0"><span class="badge rounded-pill bg-label-warning">61 &ndash; 180 Hari</span></p>
                            </div>
                            <div class="avatar me-lg-4">
                                <span class="avatar-initial rounded bg-label-warning">
                                    <i class="mdi mdi-clock-outline mdi-24px"></i>
                                </span>
                            </div>
                        </div>
                        <hr class="d-none d-sm-block d-lg-none">
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <div class="d-flex justify-content-between align-items-start border-end pb-3 pb-sm-0 card-widget-3">
                            <div>
                                <p class="mb-1 text-muted">Dead Stock</p>
                                <h4 class="mb-2 text-danger fw-bold" id="kpi-dead-stock">0</h4>
                                <p class="mb-0"><span class="badge rounded-pill bg-label-danger">&gt; 180 Hari (Stok &gt; 0)</span></p>
                            </div>
                            <div class="avatar me-sm-4">
                                <span class="avatar-initial rounded bg-label-danger">
                                    <i class="mdi mdi-alert-circle-outline mdi-24px"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="mb-1 text-muted">By Order / Indent</p>
                                <h4 class="mb-2 text-info fw-bold" id="kpi-by-order">0</h4>
                                <p class="mb-0"><span class="badge rounded-pill bg-label-info">Sesuai Kebutuhan</span></p>
                            </div>
                            <div class="avatar">
                                <span class="avatar-initial rounded bg-label-info">
                                    <i class="mdi mdi-cart-arrow-down mdi-24px"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card mb-3">
        <div class="card-header pb-2">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                <div class="d-flex align-items-center gap-2">
                    <span class="fw-semibold text-muted"><i class="mdi mdi-filter-variant me-1"></i>Filter FSN:</span>
                    <div class="btn-group btn-group-sm" role="group" id="fsn-filter-group">
                        <button type="button" class="btn btn-outline-primary fsn-filter-btn active" data-filter="all">Semua</button>
                        <button type="button" class="btn btn-outline-success fsn-filter-btn" data-filter="fast_moving">🟢 Fast Moving</button>
                        <button type="button" class="btn btn-outline-warning fsn-filter-btn" data-filter="slow_moving">🟡 Slow Moving</button>
                        <button type="button" class="btn btn-outline-danger fsn-filter-btn" data-filter="dead_stock">🔴 Dead Stock</button>
                        <button type="button" class="btn btn-outline-info fsn-filter-btn" data-filter="by_order">🔵 By Order</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-datatable table-responsive pt-0">
            <table class="datatable-master table table-bordered">
                <thead>
                    <tr>
                        <th rowspan="2"></th>
                        <th rowspan="2">ID</th>
                        <th rowspan="2">SKU</th>
                        <th rowspan="2">DESC</th>
                        <th rowspan="2">Dimension</th>
                        <th rowspan="2">Genuine / Replacement</th>
                        <th rowspan="2">Status FSN</th>
                        <th colspan="3" class="text-center border-bottom">Stock</th>
                    </tr>
                    <tr>
                        <th>BDG</th>
                        <th>BKS</th>
                        <th>Pend</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    @include('components.modal.warehouse.product.form')
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
    <script src="{{ asset('assets') }}/includes/table-master.js"></script>
@endpush
