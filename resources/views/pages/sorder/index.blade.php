@extends('layouts.sales.app')
@section('title', 'Sales Order & Project Operations')
@section('no-container') @endsection
@section('content')
    @php
        $activeTab = request()->get('tab', 'sales-order');
    @endphp

    <div class="container-fluid flex-grow-1 container-p-y">
        <!-- Hero Header & Filter Bar -->
        <div class="card bg-white border-0 shadow-sm mb-4 overflow-hidden" style="border-radius: 14px;">
            <div class="card-body p-4">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="badge bg-label-primary rounded-pill px-2 py-1 fs-tiny text-uppercase fw-bold">
                                <i class="mdi mdi-cube-send me-1"></i>Operations Hub
                            </span>
                            <span class="text-muted small">|</span>
                            <span class="text-muted small">Periode Tahun <strong>{{ $selectedYear === 'all' ? 'Semua Tahun' : $selectedYear }}</strong></span>
                        </div>
                        <h3 class="fw-bold text-dark m-0 mb-1" style="letter-spacing: -0.5px;">
                            Sales Order & Project Operations
                        </h3>
                        <p class="text-muted mb-0 small">
                            Monitoring terintegrasi alur pesanan Purchase Order (PO), operasional Non-Project, dan eksekusi Proyek.
                        </p>
                    </div>
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <form action="{{ route('pending-po.sales-order') }}" method="GET" class="d-flex align-items-center gap-2">
                            <input type="hidden" name="tab" id="active-tab-param" value="{{ $activeTab }}">
                            <div class="input-group input-group-sm shadow-xs" style="min-width: 200px;">
                                <span class="input-group-text bg-light border-end-0 text-muted">
                                    <i class="mdi mdi-calendar-range-outline text-primary"></i>
                                </span>
                                <select name="year" id="filter-year" class="form-select border-start-0 ps-0 fw-semibold" onchange="this.form.submit()">
                                    <option value="all" {{ $selectedYear == 'all' ? 'selected' : '' }}>Semua Periode Tahun</option>
                                    @foreach($availableYears as $yr)
                                        <option value="{{ $yr }}" {{ $selectedYear == $yr ? 'selected' : '' }}>Tahun {{ $yr }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top-Level Segmented Control Navigation -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-start">
                    <ul class="nav nav-pills-custom shadow-xs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link {{ !in_array($activeTab, ['non-project', 'project', 'project-monitoring']) ? 'active' : '' }}"
                                    data-bs-toggle="tab"
                                    data-bs-target="#tab-content-master"
                                    type="button"
                                    role="tab"
                                    aria-controls="tab-content-master"
                                    aria-selected="{{ !in_array($activeTab, ['non-project', 'project', 'project-monitoring']) ? 'true' : 'false' }}">
                                <i class="mdi mdi-clipboard-text-clock-outline me-2"></i>Sales Order
                                <span class="badge rounded-pill bg-label-primary ms-2">{{ $totalMasterOrdersCount ?? 0 }}</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link {{ $activeTab === 'non-project' ? 'active' : '' }}"
                                    data-bs-toggle="tab"
                                    data-bs-target="#tab-content-sorder"
                                    type="button"
                                    role="tab"
                                    aria-controls="tab-content-sorder"
                                    aria-selected="{{ $activeTab === 'non-project' ? 'true' : 'false' }}">
                                <i class="mdi mdi-cube-outline me-2"></i>Non-Project
                                <span class="badge rounded-pill bg-label-info ms-2">{{ $totalOrdersCount ?? 0 }}</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link {{ in_array($activeTab, ['project', 'project-monitoring']) ? 'active' : '' }}"
                                    data-bs-toggle="tab"
                                    data-bs-target="#tab-content-project"
                                    type="button"
                                    role="tab"
                                    aria-controls="tab-content-project"
                                    aria-selected="{{ in_array($activeTab, ['project', 'project-monitoring']) ? 'true' : 'false' }}">
                                <i class="mdi mdi-briefcase-check-outline me-2"></i>Project
                                <span class="badge rounded-pill bg-label-warning ms-2">{{ $totalProjectsCount ?? 0 }}</span>
                            </button>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Top-Level Tab Content -->
        <div class="tab-content p-0 border-0 shadow-none bg-transparent" id="main-tab-content">

            <!-- ========================================================= -->
            <!-- 1. MASTER SALES ORDER TAB (ALL POS) -->
            <!-- ========================================================= -->
            <div class="tab-pane fade {{ !in_array($activeTab, ['non-project', 'project', 'project-monitoring']) ? 'show active' : '' }}" id="tab-content-master" role="tabpanel">
                
                <!-- Master KPI Metric Cards Grid -->
                <div class="row g-3 mb-4">
                    <div class="col-12 col-sm-6 col-xl-3">
                        <div class="card kpi-card h-100 shadow-xs">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.5px;">Total Sales Order</span>
                                    <div class="kpi-icon-container bg-label-primary text-primary">
                                        <i class="mdi mdi-clipboard-list-outline"></i>
                                    </div>
                                </div>
                                <h3 class="fw-bold text-dark mb-1">{{ number_format($totalMasterOrdersCount ?? 0, 0, ',', '.') }}</h3>
                                <div class="d-flex align-items-center gap-1 text-muted small" style="font-size: 0.78rem;">
                                    <i class="mdi mdi-check-circle-outline text-success"></i>
                                    <span>Semua record PO masuk</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-xl-3">
                        <div class="card kpi-card h-100 shadow-xs">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.5px;">Orders In Progress</span>
                                    <div class="kpi-icon-container bg-label-warning text-warning">
                                        <i class="mdi mdi-progress-clock"></i>
                                    </div>
                                </div>
                                <h3 class="fw-bold text-dark mb-1">{{ number_format($totalMasterInProgressCount ?? 0, 0, ',', '.') }}</h3>
                                <div class="d-flex align-items-center gap-1 text-muted small" style="font-size: 0.78rem;">
                                    <i class="mdi mdi-clock-outline text-warning"></i>
                                    <span>Sedang aktif (Belum Selesai)</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-xl-3">
                        <div class="card kpi-card h-100 shadow-xs">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.5px;">Non-Project Orders</span>
                                    <div class="kpi-icon-container bg-label-info text-info">
                                        <i class="mdi mdi-cube-outline"></i>
                                    </div>
                                </div>
                                <h3 class="fw-bold text-dark mb-1">{{ number_format($totalOrdersCount ?? 0, 0, ',', '.') }}</h3>
                                <div class="d-flex align-items-center gap-1 text-muted small" style="font-size: 0.78rem;">
                                    <i class="mdi mdi-cart-arrow-right text-info"></i>
                                    <span>Unit / Parts / Chemical</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-xl-3">
                        <div class="card kpi-card h-100 shadow-xs">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.5px;">Project Orders</span>
                                    <div class="kpi-icon-container bg-label-purple">
                                        <i class="mdi mdi-briefcase-outline"></i>
                                    </div>
                                </div>
                                <h3 class="fw-bold text-dark mb-1">{{ number_format($totalProjectsCount ?? 0, 0, ',', '.') }}</h3>
                                <div class="d-flex align-items-center gap-1 text-muted small" style="font-size: 0.78rem;">
                                    <i class="mdi mdi-hard-hat" style="color: #7367f0;"></i>
                                    <span>Instalasi & Service Proyek</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Master Sales Orders Table Card -->
                <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                    <div class="card-header bg-white border-bottom py-3 px-4">
                        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2">
                            <div>
                                <h5 class="card-title fw-bold text-dark mb-0">Daftar Lengkap Sales Order (Master PO)</h5>
                                <p class="text-muted small mb-0">Seluruh pesanan PO dari unit quotation maupun quotation standar dengan status real-time.</p>
                            </div>
                            <span class="badge bg-label-primary rounded-pill px-3 py-2 fw-semibold">
                                <i class="mdi mdi-database-outline me-1"></i>{{ number_format($totalMasterOrdersCount ?? 0, 0, ',', '.') }} Pesanan
                            </span>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        @include('pages.sorder._table_master', ['orderList' => $allMasterOrders, 'tableId' => 'table-sales-order-master'])
                    </div>
                </div>
            </div>

            <!-- ========================================================= -->
            <!-- 2. NON-PROJECT TAB -->
            <!-- ========================================================= -->
            <div class="tab-pane fade {{ $activeTab === 'non-project' ? 'show active' : '' }}" id="tab-content-sorder" role="tabpanel">
                
                <!-- Non-Project Financial KPI Cards Grid -->
                <div class="row g-3 mb-4">
                    <div class="col-12 col-sm-6 col-xl-3">
                        <div class="card kpi-card h-100 shadow-xs">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.5px;">Total Non-Project</span>
                                    <div class="kpi-icon-container bg-label-primary text-primary">
                                        <i class="mdi mdi-cube-send"></i>
                                    </div>
                                </div>
                                <h3 class="fw-bold text-dark mb-1">{{ number_format($totalOrdersCount ?? 0, 0, ',', '.') }}</h3>
                                <div class="d-flex align-items-center gap-1 text-muted small" style="font-size: 0.78rem;">
                                    <i class="mdi mdi-information-outline text-primary"></i>
                                    <span>Volume pesanan reguler</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-xl-3">
                        <div class="card kpi-card h-100 shadow-xs">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.5px;">Sedang Diproses</span>
                                    <div class="kpi-icon-container bg-label-warning text-warning">
                                        <i class="mdi mdi-progress-clock"></i>
                                    </div>
                                </div>
                                <h3 class="fw-bold text-dark mb-1">{{ number_format(($newOrders->count() + $checkPartsOrders->count()) ?? 0, 0, ',', '.') }}</h3>
                                <div class="d-flex align-items-center gap-1 text-muted small" style="font-size: 0.78rem;">
                                    <i class="mdi mdi-clock-outline text-warning"></i>
                                    <span>Persiapan & check parts</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-xl-3">
                        <div class="card kpi-card h-100 shadow-xs">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.5px;">Proses Pengiriman</span>
                                    <div class="kpi-icon-container bg-label-info text-info">
                                        <i class="mdi mdi-truck-delivery-outline"></i>
                                    </div>
                                </div>
                                <h3 class="fw-bold text-dark mb-1">{{ number_format($deliveryOrders->count() ?? 0, 0, ',', '.') }}</h3>
                                <div class="d-flex align-items-center gap-1 text-muted small" style="font-size: 0.78rem;">
                                    <i class="mdi mdi-truck-fast-outline text-info"></i>
                                    <span>Pesanan dalam ekspedisi</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-xl-3">
                        <div class="card kpi-card h-100 shadow-xs">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.5px;">Pesanan Selesai</span>
                                    <div class="kpi-icon-container bg-label-success text-success">
                                        <i class="mdi mdi-check-circle-outline"></i>
                                    </div>
                                </div>
                                <h3 class="fw-bold text-dark mb-1">{{ number_format($completedOrders->count() ?? 0, 0, ',', '.') }}</h3>
                                <div class="d-flex align-items-center gap-1 text-muted small" style="font-size: 0.78rem;">
                                    <i class="mdi mdi-check-all text-success"></i>
                                    <span>Barang berhasil diterima</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sales Orders Sub-Tabs Card -->
                <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                    <div class="card-header bg-white border-bottom p-3">
                        <div class="d-flex flex-wrap gap-2" role="tablist">
                            <button type="button" class="subtab-pill active" role="tab" data-bs-toggle="tab" data-bs-target="#tab-sorder-new" aria-selected="true">
                                <span class="badge bg-danger rounded-pill me-1" style="width: 8px; height: 8px; padding: 0;"></span>
                                New
                                <span class="badge rounded-pill bg-label-danger ms-1">{{ $newOrders->count() }}</span>
                            </button>
                            <button type="button" class="subtab-pill" role="tab" data-bs-toggle="tab" data-bs-target="#tab-sorder-check">
                                <span class="badge bg-warning rounded-pill me-1" style="width: 8px; height: 8px; padding: 0;"></span>
                                Check Parts
                                <span class="badge rounded-pill bg-label-warning ms-1">{{ $checkPartsOrders->count() }}</span>
                            </button>
                            <button type="button" class="subtab-pill" role="tab" data-bs-toggle="tab" data-bs-target="#tab-sorder-delivery">
                                <span class="badge bg-info rounded-pill me-1" style="width: 8px; height: 8px; padding: 0;"></span>
                                Delivery Process
                                <span class="badge rounded-pill bg-label-info ms-1">{{ $deliveryOrders->count() }}</span>
                            </button>
                            <button type="button" class="subtab-pill" role="tab" data-bs-toggle="tab" data-bs-target="#tab-sorder-completed">
                                <span class="badge bg-success rounded-pill me-1" style="width: 8px; height: 8px; padding: 0;"></span>
                                Selesai
                                <span class="badge rounded-pill bg-label-success ms-1">{{ $completedOrders->count() }}</span>
                            </button>
                            <button type="button" class="subtab-pill" role="tab" data-bs-toggle="tab" data-bs-target="#tab-sorder-delayed">
                                <span class="badge bg-danger rounded-pill me-1" style="width: 8px; height: 8px; padding: 0;"></span>
                                Delayed
                                <span class="badge rounded-pill bg-label-danger ms-1">{{ $delayedOrders->count() }}</span>
                            </button>
                            <button type="button" class="subtab-pill" role="tab" data-bs-toggle="tab" data-bs-target="#tab-sorder-return">
                                <span class="badge bg-warning rounded-pill me-1" style="width: 8px; height: 8px; padding: 0;"></span>
                                Return
                                <span class="badge rounded-pill bg-label-warning ms-1">{{ $returnOrders->count() }}</span>
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-0">
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

            <!-- ========================================================= -->
            <!-- 3. PROJECT TAB -->
            <!-- ========================================================= -->
            <div class="tab-pane fade {{ in_array($activeTab, ['project', 'project-monitoring']) ? 'show active' : '' }}" id="tab-content-project" role="tabpanel">
                
                <!-- Project Financial KPI Cards Grid -->
                <div class="row g-3 mb-4">
                    <div class="col-12 col-sm-6 col-xl-3">
                        <div class="card kpi-card h-100 shadow-xs">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.5px;">Total Proyek</span>
                                    <div class="kpi-icon-container bg-label-primary text-primary">
                                        <i class="mdi mdi-briefcase-outline"></i>
                                    </div>
                                </div>
                                <h3 class="fw-bold text-dark mb-1">{{ number_format($totalProjectsCount ?? 0, 0, ',', '.') }}</h3>
                                <div class="d-flex align-items-center gap-1 text-muted small" style="font-size: 0.78rem;">
                                    <i class="mdi mdi-wrench-clock-outline text-primary"></i>
                                    <span>Volume project aktif & selesai</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-xl-3">
                        <div class="card kpi-card h-100 shadow-xs">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.5px;">Tahap Persiapan</span>
                                    <div class="kpi-icon-container bg-label-warning text-warning">
                                        <i class="mdi mdi-clipboard-check-outline"></i>
                                    </div>
                                </div>
                                <h3 class="fw-bold text-dark mb-1">{{ number_format(($newProjects->count() + $checkPartsProjects->count()) ?? 0, 0, ',', '.') }}</h3>
                                <div class="d-flex align-items-center gap-1 text-muted small" style="font-size: 0.78rem;">
                                    <i class="mdi mdi-clock-outline text-warning"></i>
                                    <span>Check parts, unit & material</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-xl-3">
                        <div class="card kpi-card h-100 shadow-xs">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.5px;">Jadwal & Eksekusi</span>
                                    <div class="kpi-icon-container bg-label-info text-info">
                                        <i class="mdi mdi-calendar-clock-outline"></i>
                                    </div>
                                </div>
                                <h3 class="fw-bold text-dark mb-1">{{ number_format(($schedulingProjects->count() + $inProgressProjects->count()) ?? 0, 0, ',', '.') }}</h3>
                                <div class="d-flex align-items-center gap-1 text-muted small" style="font-size: 0.78rem;">
                                    <i class="mdi mdi-tools text-info"></i>
                                    <span>Jadwal pickup & eksekusi</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-xl-3">
                        <div class="card kpi-card h-100 shadow-xs">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.5px;">Proyek Selesai</span>
                                    <div class="kpi-icon-container bg-label-success text-success">
                                        <i class="mdi mdi-check-decagram-outline"></i>
                                    </div>
                                </div>
                                <h3 class="fw-bold text-dark mb-1">{{ number_format($completedProjects->count() ?? 0, 0, ',', '.') }}</h3>
                                <div class="d-flex align-items-center gap-1 text-muted small" style="font-size: 0.78rem;">
                                    <i class="mdi mdi-check-all text-success"></i>
                                    <span>Pekerjaan telah tuntas</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Projects Sub-Tabs Card -->
                <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                    <div class="card-header bg-white border-bottom p-3">
                        <div class="d-flex flex-wrap gap-2" role="tablist">
                            <button type="button" class="subtab-pill active" role="tab" data-bs-toggle="tab" data-bs-target="#tab-project-new" aria-selected="true">
                                <span class="badge bg-danger rounded-pill me-1" style="width: 8px; height: 8px; padding: 0;"></span>
                                New
                                <span class="badge rounded-pill bg-label-danger ms-1">{{ $newProjects->count() }}</span>
                            </button>
                            <button type="button" class="subtab-pill" role="tab" data-bs-toggle="tab" data-bs-target="#tab-project-check">
                                <span class="badge bg-warning rounded-pill me-1" style="width: 8px; height: 8px; padding: 0;"></span>
                                Check Parts / Unit / Material
                                <span class="badge rounded-pill bg-label-warning ms-1">{{ $checkPartsProjects->count() }}</span>
                            </button>
                            <button type="button" class="subtab-pill" role="tab" data-bs-toggle="tab" data-bs-target="#tab-project-sched">
                                <span class="badge bg-info rounded-pill me-1" style="width: 8px; height: 8px; padding: 0;"></span>
                                Scheduling / Shipment
                                <span class="badge rounded-pill bg-label-info ms-1">{{ $schedulingProjects->count() }}</span>
                            </button>
                            <button type="button" class="subtab-pill" role="tab" data-bs-toggle="tab" data-bs-target="#tab-project-progress">
                                <span class="badge bg-primary rounded-pill me-1" style="width: 8px; height: 8px; padding: 0;"></span>
                                In Progress / Execution
                                <span class="badge rounded-pill bg-label-primary ms-1">{{ $inProgressProjects->count() }}</span>
                            </button>
                            <button type="button" class="subtab-pill" role="tab" data-bs-toggle="tab" data-bs-target="#tab-project-completed">
                                <span class="badge bg-success rounded-pill me-1" style="width: 8px; height: 8px; padding: 0;"></span>
                                Selesai
                                <span class="badge rounded-pill bg-label-success ms-1">{{ $completedProjects->count() }}</span>
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-0">
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

@endsection

@push('after-style')
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/animate-css/animate.css">
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-rowgroup-bs5/rowgroup.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/formvalidation/dist/css/formValidation.min.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.css" />

    <style>
        /* Modern Segmented Control Pills */
        .nav-pills-custom {
            background-color: #f0f2f6;
            padding: 5px;
            border-radius: 50rem;
            display: inline-flex;
            border: 1px solid #e4e6eb;
            gap: 4px;
        }
        .nav-pills-custom .nav-link {
            border-radius: 50rem;
            font-weight: 600;
            color: #566a7f;
            padding: 0.65rem 1.4rem;
            transition: all 0.22s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid transparent;
            display: flex;
            align-items: center;
            user-select: none;
            cursor: pointer;
        }
        .nav-pills-custom .nav-link:hover {
            color: #696cff;
            background-color: rgba(255, 255, 255, 0.7);
        }
        .nav-pills-custom .nav-link:active {
            transform: scale(0.97);
        }
        .nav-pills-custom .nav-link.active {
            background: linear-gradient(135deg, #696cff 0%, #5457ff 100%) !important;
            color: #fff !important;
            box-shadow: 0 4px 14px rgba(105, 108, 255, 0.38) !important;
            transform: translateY(0);
        }
        .nav-pills-custom .nav-link.active .badge {
            background-color: rgba(255, 255, 255, 0.28) !important;
            color: #ffffff !important;
        }

        /* KPI Cards */
        .kpi-card {
            border: 1px solid rgba(67, 89, 113, 0.08);
            border-radius: 12px;
            background: #ffffff;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .kpi-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(67, 89, 113, 0.09) !important;
        }
        .kpi-icon-container {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
        }

        /* Operational Sub-tabs */
        .subtab-pill {
            background: transparent;
            border: 1px solid transparent;
            font-weight: 600;
            font-size: 0.85rem;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            color: #566a7f;
            display: inline-flex;
            align-items: center;
            transition: all 0.22s cubic-bezier(0.4, 0, 0.2, 1);
            user-select: none;
        }
        .subtab-pill:hover {
            background-color: #f5f6f9;
            color: #696cff;
            transform: translateY(-1px);
        }
        .subtab-pill:active {
            transform: scale(0.96);
        }
        .subtab-pill.active {
            background-color: #f0f2ff !important;
            color: #696cff !important;
            border-color: rgba(105, 108, 255, 0.3) !important;
            box-shadow: 0 2px 6px rgba(105, 108, 255, 0.12);
        }

        /* Smooth Fluid Main Tab & Subtab Transitions */
        #main-tab-content {
            min-height: 520px;
            position: relative;
        }
        #main-tab-content > .tab-pane {
            opacity: 0;
            transform: translateY(5px);
            transition: opacity 0.22s cubic-bezier(0.16, 1, 0.3, 1), transform 0.22s cubic-bezier(0.16, 1, 0.3, 1);
            will-change: opacity, transform;
        }
        #main-tab-content > .tab-pane.active.show {
            opacity: 1;
            transform: translateY(0);
        }

        /* Inner Sub-tab Panes */
        .card-body > .tab-content > .tab-pane {
            opacity: 0;
            transition: opacity 0.18s ease;
        }
        .card-body > .tab-content > .tab-pane.active.show {
            opacity: 1;
        }

        /* Table & Row Hover */
        .table-row-hover:hover {
            background-color: rgba(105, 108, 255, 0.035) !important;
            transition: background-color 0.15s ease;
        }
        .hover-elevate {
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }
        .hover-elevate:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 6px rgba(105, 108, 255, 0.3);
        }
        .status-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            display: inline-block;
            background-color: currentColor;
        }

        /* Column Search Inputs */
        .column-search-input {
            border-radius: 6px;
            font-size: 0.78rem;
            padding: 0.28rem 0.6rem;
            background-color: #ffffff;
            border: 1px solid #dce0e6;
            transition: all 0.2s ease;
        }
        .column-search-input:focus {
            border-color: #696cff;
            box-shadow: 0 0 0 0.15rem rgba(105, 108, 255, 0.18);
            background-color: #ffffff;
        }

        /* DataTables Controls Integration */
        .dataTables_wrapper .dataTables_filter input {
            border-radius: 8px;
            padding: 0.35rem 0.75rem;
            border: 1px solid #dce0e6;
        }
        .dataTables_wrapper .dataTables_filter input:focus {
            border-color: #696cff;
            box-shadow: 0 0 0 0.15rem rgba(105, 108, 255, 0.18);
        }
        .dataTables_wrapper .dataTables_length select {
            border-radius: 8px;
            border: 1px solid #dce0e6;
        }

        /* Balanced, Airy Table Layout */
        .datatable-master, .datatable-sorder, .datatable-project {
            width: 100% !important;
            table-layout: auto;
        }
        .datatable-master th, .datatable-master td,
        .datatable-sorder th, .datatable-sorder td,
        .datatable-project th, .datatable-project td {
            padding: 0.72rem 0.85rem !important;
            font-size: 0.84rem;
            vertical-align: middle;
        }
        .column-search-input {
            font-size: 0.75rem !important;
            padding: 0.25rem 0.5rem !important;
            height: 30px !important;
            border-radius: 6px !important;
        }
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        /* Proportional, Comfortable Column Widths */
        .col-so-po {
            min-width: 155px;
            max-width: 170px;
            width: 160px;
        }
        .col-date {
            min-width: 110px;
            width: 115px;
        }
        .col-type {
            min-width: 105px;
            width: 110px;
        }
        .col-area {
            min-width: 110px;
            width: 120px;
        }
        .col-flag {
            min-width: 75px;
            width: 80px;
        }
        .col-sales {
            min-width: 70px;
            width: 75px;
        }
        .col-status, .col-progress {
            min-width: 135px;
            width: 145px;
        }
        .col-payment {
            min-width: 105px;
            width: 115px;
        }
        .col-customer {
            min-width: 180px;
        }
        .col-desc {
            min-width: 220px;
        }

        /* Custom Purple Badge for Credit Paid */
        .bg-label-purple {
            background-color: #f2eefa !important;
            color: #7367f0 !important;
            border: 1px solid rgba(115, 103, 240, 0.25) !important;
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
    <script src="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.js"></script>
@endpush

@push('page-script')
    <script src="{{ asset('assets') }}/js/forms-selects.js"></script>
    <script src="{{ asset('assets') }}/js/extended-ui-sweetalert2.js"></script>
@endpush

@push('script')
    <script>
        $(document).ready(function() {
            // Initialize Tooltips
            $('[data-bs-toggle="tooltip"]').tooltip();

            // Handle Smooth Tab Switching without layout stuttering
            $('button[data-bs-toggle="tab"]').on('show.bs.tab', function(e) {
                var targetId = $(e.target).data('bs-target');
                var tabMap = {
                    '#tab-content-master': 'sales-order',
                    '#tab-content-sorder': 'non-project',
                    '#tab-content-project': 'project'
                };

                if (tabMap[targetId]) {
                    var tabName = tabMap[targetId];
                    $('#active-tab-param').val(tabName);

                    // Update URL without reloading page
                    var newUrl = window.location.protocol + "//" + window.location.host + window.location.pathname + '?tab=' + tabName + '&year=' + $('#filter-year').val();
                    window.history.pushState({path: newUrl}, '', newUrl);
                }
            });

            $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
                // Adjust DataTables columns on next animation frame for instantaneous, stutter-free display
                requestAnimationFrame(function() {
                    $.fn.dataTable.tables({ visible: true, api: true }).columns.adjust();
                });
            });

            // Initialize Datatable for Master Sales Order (All POs)
            $('.datatable-master').each(function() {
                var $table = $(this);

                // Clone header for search row
                $table.find('thead tr')
                    .clone(true)
                    .appendTo($table.find('thead'));

                var table = $table.DataTable({
                    orderCellsTop: true,
                    order: [[1, 'desc']], // Sort by Date descending (column index 1)
                    pageLength: 10,
                    drawCallback: function() {
                        $('[data-bs-toggle="tooltip"]').tooltip();
                    },
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

                // Replace cloned headers with input fields / selection
                $table.find('thead tr:eq(1) th').each(function(i) {
                    var title = $(this).text().trim();
                    if (i === 7) { // Skip Sales avatar column (index 7)
                        $(this).html('');
                        return;
                    }
                    if (i === 3) { // Selection filter for Type & Description
                        $(this).html(
                            '<select class="form-select form-select-sm column-search-input" style="height: 30px; font-size: 0.75rem; padding-top: 2px; padding-bottom: 2px;">' +
                                '<option value="">Semua Tipe</option>' +
                                '<option value="Project">Project</option>' +
                                '<option value="Non-Project">Non-Project</option>' +
                            '</select>'
                        );

                        $('select', this).on('change', function() {
                            var val = $(this).val();
                            if (!val) {
                                table.column(i).search('').draw();
                            } else if (val === 'Project') {
                                table.column(i).search('(^|[^A-Za-z0-9-])Project', true, false).draw();
                            } else {
                                table.column(i).search('Non-Project', false, false).draw();
                            }
                        });
                        return;
                    }
                    if (i === 5) { // Selection filter for Payment
                        $(this).html(
                            '<select class="form-select form-select-sm column-search-input" style="height: 30px; font-size: 0.75rem; padding-top: 2px; padding-bottom: 2px;">' +
                                '<option value="">Semua</option>' +
                                '<option value="PAID">PAID</option>' +
                                '<option value="Credit Paid">Credit Paid</option>' +
                                '<option value="UNPAID">UNPAID</option>' +
                            '</select>'
                        );

                        $('select', this).on('change', function() {
                            var val = $(this).val();
                            if (!val) {
                                table.column(i).search('').draw();
                            } else if (val === 'PAID') {
                                table.column(i).search('(^|[^A-Za-z0-9-])PAID', true, false).draw();
                            } else if (val === 'Credit Paid') {
                                table.column(i).search('Credit Paid', false, false).draw();
                            } else {
                                table.column(i).search('UNPAID', false, false).draw();
                            }
                        });
                        return;
                    }
                    if (i === 6) { // Selection filter for Flag
                        $(this).html(
                            '<select class="form-select form-select-sm column-search-input" style="height: 30px; font-size: 0.75rem; padding-top: 2px; padding-bottom: 2px;">' +
                                '<option value="">Semua</option>' +
                                '<option value="RJO">RJO</option>' +
                                '<option value="KII">KII</option>' +
                            '</select>'
                        );

                        $('select', this).on('change', function() {
                            var val = $(this).val();
                            table.column(i).search(val ? val : '', false, false).draw();
                        });
                        return;
                    }
                    $(this).html('<input type="text" class="form-control form-control-sm column-search-input" placeholder="Cari ' + title + '..." />');

                    $('input', this).on('keyup change', function() {
                        if (table.column(i).search() !== this.value) {
                            table.column(i).search(this.value).draw();
                        }
                    });
                });
            });

            // Initialize Datatable for Sales Order (Non-Project)
            $('.datatable-sorder').each(function() {
                var $table = $(this);

                // Clone header for search row
                $table.find('thead tr')
                    .clone(true)
                    .appendTo($table.find('thead'));

                var table = $table.DataTable({
                    orderCellsTop: true,
                    order: [[1, 'desc']], // Sort by Date descending (column index 1)
                    pageLength: 10,
                    drawCallback: function() {
                        $('[data-bs-toggle="tooltip"]').tooltip();
                    },
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
                    var title = $(this).text().trim();
                    if (i === 5) { // Skip Sales avatar column (index 5)
                        $(this).html('');
                        return;
                    }
                    $(this).html('<input type="text" class="form-control form-control-sm column-search-input" placeholder="Cari ' + title + '..." />');

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
                    order: [[1, 'desc']], // Sort by Date descending (column index 1)
                    pageLength: 10,
                    drawCallback: function() {
                        $('[data-bs-toggle="tooltip"]').tooltip();
                    },
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
                    var title = $(this).text().trim();
                    if (i === 6) { // Skip Sales avatar column (index 6 in project table)
                        $(this).html('');
                        return;
                    }
                    $(this).html('<input type="text" class="form-control form-control-sm column-search-input" placeholder="Cari ' + title + '..." />');

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
