@extends('layouts.sales.app')
@section('title', 'Sales Order & Project Monitoring')
@section('no-container') @endsection
@section('content')
    @php
        $activeTab = request()->get('tab', 'sales-order');
    @endphp

    <div class="container-fluid flex-grow-1 container-p-y">
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center py-3 mb-4 gap-2">
            <h4 class="fw-bold m-0">
                <span class="text-muted fw-normal">Operations /</span> Sales Order & Project Monitoring
            </h4>
            <div class="d-flex align-items-center">
                <form action="{{ route('pending-po.sales-order') }}" method="GET" class="d-flex align-items-center">
                    <input type="hidden" name="tab" id="active-tab-param" value="{{ $activeTab }}">
                    <label for="filter-year" class="me-2 fw-semibold text-muted text-nowrap">Tahun:</label>
                    <select name="year" id="filter-year" class="form-select form-select-sm" onchange="this.form.submit()" style="min-width: 130px;">
                        <option value="all" {{ $selectedYear == 'all' ? 'selected' : '' }}>Semua Tahun</option>
                        @foreach($availableYears as $yr)
                            <option value="{{ $yr }}" {{ $selectedYear == $yr ? 'selected' : '' }}>{{ $yr }}</option>
                        @endforeach
                    </select>
                </form>
            </div>
        </div>

        <!-- Top-Level Tab Switcher (No Page Reload) -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="nav-align-top">
                    <ul class="nav nav-pills flex-column flex-sm-row mb-0 gap-1" role="tablist">
                        <li class="nav-item flex-sm-grow-0" role="presentation">
                            <button class="nav-link {{ $activeTab !== 'project-monitoring' ? 'active' : '' }}"
                                    data-bs-toggle="tab"
                                    data-bs-target="#tab-content-sorder"
                                    type="button"
                                    role="tab"
                                    aria-controls="tab-content-sorder"
                                    aria-selected="{{ $activeTab !== 'project-monitoring' ? 'true' : 'false' }}">
                                <i class="mdi mdi-cart-outline me-2"></i>Sales Order (Spare Parts)
                            </button>
                        </li>
                        <li class="nav-item flex-sm-grow-0" role="presentation">
                            <button class="nav-link {{ $activeTab === 'project-monitoring' ? 'active' : '' }}"
                                    data-bs-toggle="tab"
                                    data-bs-target="#tab-content-project"
                                    type="button"
                                    role="tab"
                                    aria-controls="tab-content-project"
                                    aria-selected="{{ $activeTab === 'project-monitoring' ? 'true' : 'false' }}">
                                <i class="mdi mdi-briefcase-outline me-2"></i>Project Monitoring
                            </button>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Top-Level Tab Content -->
        <div class="tab-content p-0 border-0 shadow-none bg-transparent">
            <!-- 1. SALES ORDER TAB -->
            <div class="tab-pane fade {{ $activeTab !== 'project-monitoring' ? 'show active' : '' }}" id="tab-content-sorder" role="tabpanel">
                <!-- Sales Order KPI Cards Grid -->
                <div class="row gy-4 mb-4">
                    <div class="col-12">
                        <div class="card card-border-shadow-primary h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-2 pb-1">
                                    <div class="avatar me-2">
                                        <span class="avatar-initial rounded bg-label-primary">
                                            <i class="mdi mdi-cart-outline mdi-24px"></i>
                                        </span>
                                    </div>
                                    <h4 class="ms-1 mb-0 fw-bold text-primary">{{ $totalOrdersCount }}</h4>
                                </div>
                                <p class="mb-0 text-primary-900 fw-semibold">Total Sales Order</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sales Orders Tabs Card -->
                <div class="card">
                    <div class="card-header p-0">
                        <div class="nav-align-top">
                            <ul class="nav nav-tabs" role="tablist">
                                <li class="nav-item">
                                    <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#tab-sorder-new" aria-selected="true">
                                        New
                                        <span class="badge rounded-pill bg-danger ms-1">{{ $newOrders->count() }}</span>
                                    </button>
                                </li>
                                <li class="nav-item">
                                    <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#tab-sorder-check">
                                        Check Parts
                                        <span class="badge rounded-pill bg-warning ms-1">{{ $checkPartsOrders->count() }}</span>
                                    </button>
                                </li>
                                <li class="nav-item">
                                    <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#tab-sorder-delivery">
                                        Delivery Process
                                        <span class="badge rounded-pill bg-info ms-1">{{ $deliveryOrders->count() }}</span>
                                    </button>
                                </li>
                                <li class="nav-item">
                                    <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#tab-sorder-completed">
                                        Selesai
                                        <span class="badge rounded-pill bg-success ms-1">{{ $completedOrders->count() }}</span>
                                    </button>
                                </li>
                                <li class="nav-item">
                                    <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#tab-sorder-delayed">
                                        Delayed
                                        <span class="badge rounded-pill bg-danger ms-1">{{ $delayedOrders->count() }}</span>
                                    </button>
                                </li>
                                <li class="nav-item">
                                    <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#tab-sorder-return">
                                        Return
                                        <span class="badge rounded-pill bg-warning ms-1">{{ $returnOrders->count() }}</span>
                                    </button>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="tab-content p-0 border-0 shadow-none">
                            <div class="tab-pane fade show active" id="tab-sorder-new" role="tabpanel">
                                @include('pages.sorder._table', ['orderList' => $newOrders, 'tableId' => 'table-sorder-new'])
                            </div>
                            <div class="tab-pane fade" id="tab-sorder-check" role="tabpanel">
                                @include('pages.sorder._table', ['orderList' => $checkPartsOrders, 'tableId' => 'table-sorder-check'])
                            </div>
                            <div class="tab-pane fade" id="tab-sorder-delivery" role="tabpanel">
                                @include('pages.sorder._table', ['orderList' => $deliveryOrders, 'tableId' => 'table-sorder-delivery'])
                            </div>
                            <div class="tab-pane fade" id="tab-sorder-completed" role="tabpanel">
                                @include('pages.sorder._table', ['orderList' => $completedOrders, 'tableId' => 'table-sorder-completed'])
                            </div>
                            <div class="tab-pane fade" id="tab-sorder-delayed" role="tabpanel">
                                @include('pages.sorder._table', ['orderList' => $delayedOrders, 'tableId' => 'table-sorder-delayed'])
                            </div>
                            <div class="tab-pane fade" id="tab-sorder-return" role="tabpanel">
                                @include('pages.sorder._table', ['orderList' => $returnOrders, 'tableId' => 'table-sorder-return'])
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. PROJECT MONITORING TAB -->
            <div class="tab-pane fade {{ $activeTab === 'project-monitoring' ? 'show active' : '' }}" id="tab-content-project" role="tabpanel">
                <!-- Project KPI Cards Grid -->
                <div class="row gy-4 mb-4">
                    <div class="col-12">
                        <div class="card card-border-shadow-primary h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-2 pb-1">
                                    <div class="avatar me-2">
                                        <span class="avatar-initial rounded bg-label-primary">
                                            <i class="mdi mdi-briefcase-outline mdi-24px"></i>
                                        </span>
                                    </div>
                                    <h4 class="ms-1 mb-0">{{ $totalProjectsCount }}</h4>
                                </div>
                                <p class="mb-0 text-muted">Total Projects</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Projects Tabs Card -->
                <div class="card">
                    <div class="card-header p-0">
                        <div class="nav-align-top">
                            <ul class="nav nav-tabs" role="tablist">
                                <li class="nav-item">
                                    <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#tab-project-new" aria-selected="true">
                                        New
                                        <span class="badge rounded-pill bg-danger ms-1">{{ $newProjects->count() }}</span>
                                    </button>
                                </li>
                                <li class="nav-item">
                                    <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#tab-project-check">
                                        Check Parts / Unit / Material
                                        <span class="badge rounded-pill bg-warning ms-1">{{ $checkPartsProjects->count() }}</span>
                                    </button>
                                </li>
                                <li class="nav-item">
                                    <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#tab-project-sched">
                                        Scheduling / Shipment
                                        <span class="badge rounded-pill bg-info ms-1">{{ $schedulingProjects->count() }}</span>
                                    </button>
                                </li>
                                <li class="nav-item">
                                    <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#tab-project-progress">
                                        In Progress / Execution
                                        <span class="badge rounded-pill bg-primary ms-1">{{ $inProgressProjects->count() }}</span>
                                    </button>
                                </li>
                                <li class="nav-item">
                                    <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#tab-project-completed">
                                        Selesai
                                        <span class="badge rounded-pill bg-success ms-1">{{ $completedProjects->count() }}</span>
                                    </button>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="tab-content p-0 border-0 shadow-none">
                            <div class="tab-pane fade show active" id="tab-project-new" role="tabpanel">
                                @include('pages.project-monitoring._table', ['projectList' => $newProjects, 'tableId' => 'table-project-new'])
                            </div>
                            <div class="tab-pane fade" id="tab-project-check" role="tabpanel">
                                @include('pages.project-monitoring._table', ['projectList' => $checkPartsProjects, 'tableId' => 'table-project-check'])
                            </div>
                            <div class="tab-pane fade" id="tab-project-sched" role="tabpanel">
                                @include('pages.project-monitoring._table', ['projectList' => $schedulingProjects, 'tableId' => 'table-project-sched'])
                            </div>
                            <div class="tab-pane fade" id="tab-project-progress" role="tabpanel">
                                @include('pages.project-monitoring._table', ['projectList' => $inProgressProjects, 'tableId' => 'table-project-progress'])
                            </div>
                            <div class="tab-pane fade" id="tab-project-completed" role="tabpanel">
                                @include('pages.project-monitoring._table', ['projectList' => $completedProjects, 'tableId' => 'table-project-completed'])
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @foreach ($orders as $order)
        @include('components.modal.pending.jadwal.schedule')
    @endforeach
    @foreach ($schedules as $schedule)
        @include('components.modal.pending.jadwal.reschedule')
        @include('components.modal.pending.jadwal.dokumentasi')
    @endforeach
@endsection

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
    <script src="{{ asset('assets') }}/js/forms-selects.js"></script>
    <script src="{{ asset('assets') }}/js/extended-ui-sweetalert2.js"></script>
@endpush

@push('script')
    <script>
        $(document).ready(function() {
            // Handle Top-Level Tab Switching
            $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
                var targetId = $(e.target).data('bs-target');
                if (targetId === '#tab-content-sorder' || targetId === '#tab-content-project') {
                    var tabName = targetId === '#tab-content-project' ? 'project-monitoring' : 'sales-order';
                    $('#active-tab-param').val(tabName);

                    // Update URL without reloading page
                    var newUrl = window.location.protocol + "//" + window.location.host + window.location.pathname + '?tab=' + tabName + '&year=' + $('#filter-year').val();
                    window.history.pushState({path: newUrl}, '', newUrl);

                    // Adjust DataTables columns on tab switch to prevent header layout compression
                    $.fn.dataTable.tables({ visible: true, api: true }).columns.adjust();
                }
            });

            // Initialize Datatable for Sales Order
            $('.datatable-sorder').each(function() {
                var $table = $(this);

                // Clone header for search row
                $table.find('thead tr')
                    .clone(true)
                    .appendTo($table.find('thead'));

                var table = $table.DataTable({
                    orderCellsTop: true,
                    order: [[2, 'desc']], // Sort by Date descending
                    pageLength: 10,
                    language: {
                        search: "Cari Sales Order:",
                        lengthMenu: "Tampilkan _MENU_",
                        info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ sales order",
                        paginate: {
                            first: "Pertama",
                            last: "Terakhir",
                            next: "Berikutnya",
                            previous: "Sebelumnya"
                        }
                    }
                });

                // Replace cloned headers with input fields
                $table.find('thead tr:eq(1) th').each(function(i) {
                    var title = $(this).text();
                    if (i === 6) { // Skip Sales avatar column
                        $(this).html('');
                        return;
                    }
                    $(this).html('<input type="text" class="form-control form-control-sm" placeholder="Cari ' + title + '..." />');

                    $('input', this).on('keyup change', function() {
                        if (table.column(i).search() !== this.value) {
                            table.column(i).search(this.value).draw();
                        }
                    });
                });
            });

            // Initialize Datatable for Projects
            $('.datatable-project').each(function() {
                var $table = $(this);

                // Clone header for search row
                $table.find('thead tr')
                    .clone(true)
                    .appendTo($table.find('thead'));

                var table = $table.DataTable({
                    orderCellsTop: true,
                    order: [[0, 'desc']],
                    pageLength: 10,
                    language: {
                        search: "Cari Proyek:",
                        lengthMenu: "Tampilkan _MENU_",
                        info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ proyek",
                        paginate: {
                            first: "Pertama",
                            last: "Terakhir",
                            next: "Berikutnya",
                            previous: "Sebelumnya"
                        }
                    }
                });

                // Replace cloned headers with input fields
                $table.find('thead tr:eq(1) th').each(function(i) {
                    var title = $(this).text();
                    if (i === 6) { // Skip Sales avatar column
                        $(this).html('');
                        return;
                    }
                    $(this).html('<input type="text" class="form-control form-control-sm" placeholder="Cari ' + title + '..." />');

                    $('input', this).on('keyup change', function() {
                        if (table.column(i).search() !== this.value) {
                            table.column(i).search(this.value).draw();
                        }
                    });
                });
            });
        });
    </script>
@endpush
