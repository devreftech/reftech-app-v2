@extends('layouts.sales.app')
@section('title', 'Project Monitoring Dashboard')
@section('no-container') @endsection
@section('content')
    <div class="container-fluid flex-grow-1 container-p-y">
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center py-3 mb-4 gap-2">
            <h4 class="fw-bold m-0">
                <span class="text-muted fw-normal">Project /</span> Profitability Monitoring
            </h4>
            <div class="d-flex align-items-center">
                <form action="{{ route('project-monitoring.index') }}" method="GET" class="d-flex align-items-center">
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

        @include('components.dashboard.tab-navigation')

        <!-- KPI Cards Grid -->
        <div class="row gy-4 mb-4">
            <!-- Total Projects Card -->
            <div class="col-sm-6 col-lg-3">
                <div class="card card-border-shadow-primary h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2 pb-1">
                            <div class="avatar me-2">
                                <span class="avatar-initial rounded bg-label-primary">
                                    <i class="mdi mdi-briefcase-outline mdi-24px"></i>
                                </span>
                            </div>
                            <h4 class="ms-1 mb-0">{{ $totalProjects }}</h4>
                        </div>
                        <p class="mb-0 text-muted">Total Projects</p>
                    </div>
                </div>
            </div>

            <!-- Total Revenue Card -->
            <div class="col-sm-6 col-lg-3">
                <div class="card card-border-shadow-success h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2 pb-1">
                            <div class="avatar me-2">
                                <span class="avatar-initial rounded bg-label-success">
                                    <i class="mdi mdi-currency-usd mdi-24px"></i>
                                </span>
                            </div>
                            <h4 class="ms-1 mb-0 text-success">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h4>
                        </div>
                        <p class="mb-0 text-muted">Total Revenue (Quotation)</p>
                    </div>
                </div>
            </div>

            <!-- Total Cost Card -->
            <div class="col-sm-6 col-lg-3">
                <div class="card card-border-shadow-warning h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2 pb-1">
                            <div class="avatar me-2">
                                <span class="avatar-initial rounded bg-label-warning">
                                    <i class="mdi mdi-cart-outline mdi-24px"></i>
                                </span>
                            </div>
                            <h4 class="ms-1 mb-0 text-warning">Rp {{ number_format($totalMaterial + $totalGeneral + $totalShipping, 0, ',', '.') }}</h4>
                        </div>
                        <p class="mb-0 text-muted">Total Expenses & Purchases</p>
                    </div>
                </div>
            </div>

            <!-- Net Profit / Margin Card -->
            <div class="col-sm-6 col-lg-3">
                <div class="card card-border-shadow-info h-100" style="background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2 pb-1">
                            <div class="avatar me-2">
                                <span class="avatar-initial rounded bg-info text-white">
                                    <i class="mdi mdi-trending-up mdi-24px"></i>
                                </span>
                            </div>
                            <div class="d-flex flex-column">
                                <h5 class="ms-1 mb-0 text-primary fw-bold">Rp {{ number_format($totalProfit, 0, ',', '.') }}</h5>
                                <small class="ms-1 text-muted fw-bold">Margin: {{ number_format($overallMargin, 1) }}%</small>
                            </div>
                        </div>
                        <p class="mb-0 text-primary-900 fw-semibold">Net Profit</p>
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
                            <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#tab-new" aria-selected="true">
                                New
                                <span class="badge rounded-pill bg-danger ms-1">{{ $newProjects->count() }}</span>
                            </button>
                        </li>
                        <li class="nav-item">
                            <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#tab-check">
                                Check Parts / Unit / Material
                                <span class="badge rounded-pill bg-warning ms-1">{{ $checkPartsProjects->count() }}</span>
                            </button>
                        </li>
                        <li class="nav-item">
                            <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#tab-sched">
                                Scheduling / Shipment
                                <span class="badge rounded-pill bg-info ms-1">{{ $schedulingProjects->count() }}</span>
                            </button>
                        </li>
                        <li class="nav-item">
                            <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#tab-progress">
                                In Progress / Execution
                                <span class="badge rounded-pill bg-primary ms-1">{{ $inProgressProjects->count() }}</span>
                            </button>
                        </li>
                        <li class="nav-item">
                            <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#tab-completed">
                                Selesai
                                <span class="badge rounded-pill bg-success ms-1">{{ $completedProjects->count() }}</span>
                            </button>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="card-body">
                <div class="tab-content p-0 border-0 shadow-none">
                    <!-- Tab New -->
                    <div class="tab-pane fade show active" id="tab-new" role="tabpanel">
                        @include('pages.project-monitoring._table', ['projectList' => $newProjects, 'tableId' => 'table-new'])
                    </div>
                    <!-- Tab Check Parts -->
                    <div class="tab-pane fade" id="tab-check" role="tabpanel">
                        @include('pages.project-monitoring._table', ['projectList' => $checkPartsProjects, 'tableId' => 'table-check'])
                    </div>
                    <!-- Tab Sched -->
                    <div class="tab-pane fade" id="tab-sched" role="tabpanel">
                        @include('pages.project-monitoring._table', ['projectList' => $schedulingProjects, 'tableId' => 'table-sched'])
                    </div>
                    <!-- Tab In Progress -->
                    <div class="tab-pane fade" id="tab-progress" role="tabpanel">
                        @include('pages.project-monitoring._table', ['projectList' => $inProgressProjects, 'tableId' => 'table-progress'])
                    </div>
                    <!-- Tab Completed -->
                    <div class="tab-pane fade" id="tab-completed" role="tabpanel">
                        @include('pages.project-monitoring._table', ['projectList' => $completedProjects, 'tableId' => 'table-completed'])
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('after-style')
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
@endpush

@push('after-script')
    <script src="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
    <script>
        $(document).ready(function() {
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
                    if (i === 7) { // Skip Sales avatar column
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
