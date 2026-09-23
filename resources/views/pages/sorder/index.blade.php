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
                            <button type="button" class="subtab-pill" role="tab" data-bs-toggle="tab" data-bs-target="#tab-sorder-partial">
                                <span class="badge bg-info rounded-pill me-1" style="width: 8px; height: 8px; padding: 0;"></span>
                                Partial Delivery
                                <span class="badge rounded-pill bg-label-info ms-1">{{ $partialDeliveryOrders->count() }}</span>
                            </button>
                            <button type="button" class="subtab-pill" role="tab" data-bs-toggle="tab" data-bs-target="#tab-sorder-delivery">
                                <span class="badge bg-primary rounded-pill me-1" style="width: 8px; height: 8px; padding: 0;"></span>
                                Delivery Process
                                <span class="badge rounded-pill bg-label-primary ms-1">{{ $deliveryOrders->count() }}</span>
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
                            <div class="tab-pane fade" id="tab-sorder-partial" role="tabpanel">
                                @include('pages.sorder._table', ['orderList' => $partialDeliveryOrders, 'tableId' => 'table-sorder-partial'])
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

    {{-- =========================================================== --}}
    {{-- =========================================================== --}}
    {{-- BULK ACTION FLOATING BAR                                     --}}
    {{-- =========================================================== --}}
    <div id="bulk-action-bar" class="bulk-action-bar" role="toolbar" aria-label="Bulk Action Bar">
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <span class="badge bg-white text-dark fw-bold px-3 py-2 rounded-pill shadow-xs" id="bulk-count">0 PO dipilih</span>

            <select id="bulk-status-select" class="form-select form-select-sm" style="min-width: 170px; max-width: 200px;">
                <option value="">-- Ubah Status ke --</option>
                <option value="0">New PO</option>
                <option value="1">On Check</option>
                <option value="2">Ready Stock</option>
                <option value="3">Kurang</option>
                <option value="4">Pre-delivery</option>
                <option value="5">Delivery Process</option>
                <option value="6">✅ Done (tanpa BK otomatis)</option>
                <option value="9">Delayed</option>
            </select>

            <input type="text" id="bulk-note-input" class="form-control form-control-sm"
                style="min-width: 200px; max-width: 240px;"
                placeholder="Catatan opsional (mis: Part belum diinput BK)">

            <button id="btn-bulk-apply" class="btn btn-sm btn-success fw-semibold px-3">
                <i class="mdi mdi-check me-1"></i>Terapkan
            </button>

            <button id="btn-bulk-link" class="btn btn-sm btn-primary fw-semibold px-3" style="display: none;" title="Kaitkan SO yang dipilih ke 1 pekerjaan yang sama">
                <i class="mdi mdi-link-variant me-1"></i>Kaitkan SO
            </button>

            <button id="bulk-deselect" class="btn btn-sm btn-outline-light fw-semibold px-3">
                <i class="mdi mdi-close me-1"></i>Batal
            </button>
        </div>
    </div>

    {{-- Modal: Link Sales Orders --}}
    <div class="modal fade" id="modal-link-so" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold text-white">
                        <i class="mdi mdi-link-variant me-2"></i>Kaitkan Sales Order (1 Pekerjaan Sama)
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="alert alert-primary d-flex align-items-center mb-3 py-2 px-3 border-0 bg-label-primary" role="alert">
                        <i class="mdi mdi-information-outline me-2 fs-5"></i>
                        <small>Pilih salah satu Sales Order di bawah untuk dijadikan <strong>SO Utama (Parent)</strong>. SO lainnya akan dikaitkan sebagai turunan.</small>
                    </div>
                    <label class="form-label fw-bold text-dark mb-2">Pilih SO Utama (Parent):</label>
                    <div id="link-so-list" class="list-group list-group-flush border rounded-3 overflow-hidden">
                        <!-- Populated by JS -->
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" id="btn-confirm-link-so" class="btn btn-primary fw-semibold">
                        <i class="mdi mdi-link-variant me-1"></i>Kaitkan Sales Order
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal: View Linked Group / Unlink --}}
    <div class="modal fade" id="modal-view-linked-group" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title fw-bold text-white">
                        <i class="mdi mdi-link-variant me-2"></i>Grup Sales Order Terkait
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div id="linked-group-loading" class="text-center py-4">
                        <div class="spinner-border text-info" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="text-muted small mt-2">Memuat daftar SO terkait...</p>
                    </div>
                    <div id="linked-group-content" style="display: none;">
                        <div class="d-flex justify-content-between align-items-center mb-3 p-2 bg-light rounded">
                            <span class="badge bg-label-primary px-3 py-2 fs-6">
                                <i class="mdi mdi-star-outline me-1"></i>SO Utama: <strong id="linked-parent-no">-</strong>
                            </span>
                            <span class="text-muted small fw-semibold" id="linked-total-count">0 SO Terkait</span>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered table-sm align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>No. SO</th>
                                        <th>Customer</th>
                                        <th>Sales</th>
                                        <th>Tanggal</th>
                                        <th class="text-center">Peran</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="linked-group-table-body">
                                    <!-- Populated by JS -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Sliding Quick Preview Drawer (Offcanvas) for Sales Order Items --}}
    <div class="offcanvas offcanvas-end shadow-lg" tabindex="-1" id="salesOrderItemOffcanvas" aria-labelledby="salesOrderItemOffcanvasLabel" style="width: 580px; max-width: 92vw;">
        <div class="offcanvas-header bg-label-primary border-bottom py-3">
            <div>
                <div class="d-flex align-items-center gap-2 mb-1">
                    <span class="badge bg-primary text-white font-11 rounded-pill" id="soDrawerNoPending">SO-0000</span>
                    <div id="soDrawerStatusBadge"></div>
                </div>
                <h5 class="offcanvas-title fw-bold text-heading" id="salesOrderItemOffcanvasLabel">Rincian Item Sales Order</h5>
            </div>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body p-4">
            {{-- Document Summary Card --}}
            <div class="card border mb-3 bg-light">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between pb-2 mb-2 border-bottom">
                        <div>
                            <span class="text-muted font-11 d-block text-uppercase fw-bold">Customer / Klien:</span>
                            <h6 class="fw-bold text-heading font-14 mb-0" id="soDrawerCompany">-</h6>
                        </div>
                        <div class="text-end" id="soDrawerPaymentSlot">
                            {{-- Payment badge --}}
                        </div>
                    </div>

                    <div class="row g-2 font-12">
                        <div class="col-6">
                            <span class="text-muted d-block font-11">No. Sales Order (SO):</span>
                            <strong class="text-primary font-monospace" id="soDrawerNoPendingText">-</strong>
                        </div>
                        <div class="col-6">
                            <span class="text-muted d-block font-11">No. Purchase Order (PO):</span>
                            <strong class="text-heading font-monospace" id="soDrawerNoPo">-</strong>
                        </div>
                        <div class="col-6">
                            <span class="text-muted d-block font-11">Tanggal Order:</span>
                            <strong class="text-heading" id="soDrawerDate">-</strong>
                        </div>
                        <div class="col-6">
                            <span class="text-muted d-block font-11">Sales In Charge:</span>
                            <div class="d-flex align-items-center gap-1 mt-1" id="soDrawerSalesBox">
                                <span class="fw-semibold text-heading" id="soDrawerUserName">-</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Ordered Items Table --}}
            <div class="mb-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <label class="font-11 text-muted text-uppercase fw-bold mb-0">Daftar Barang / Sparepart (<span id="soDrawerItemCount">0</span> jenis)</label>
                    <span class="badge bg-label-primary font-11" id="soDrawerTotalQty">0 item</span>
                </div>
                <div class="table-responsive border rounded-3 bg-white">
                    <table class="table table-sm table-hover mb-0">
                        <thead class="table-light">
                            <tr class="font-11 text-muted text-uppercase">
                                <th style="width: 32px;" class="text-center">#</th>
                                <th>Nama Barang &amp; Spesifikasi</th>
                                <th class="text-center" style="width: 140px;">Kuantitas (Order / Sisa)</th>
                                <th class="text-center" style="width: 130px;">Status / Stok</th>
                            </tr>
                        </thead>
                        <tbody id="soDrawerItemsTbody" class="font-12">
                            {{-- Dynamically populated --}}
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Delivery Notes / Surat Jalan History Section --}}
            <div class="mb-3" id="soDrawerDeliveriesWrapper" style="display: none;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <label class="font-11 text-muted text-uppercase fw-bold mb-0">
                        <i class="mdi mdi-truck-delivery-outline me-1 text-primary"></i>Riwayat Pengeluaran Barang / Surat Jalan (<span id="soDrawerDeliveriesCount">0</span>)
                    </label>
                </div>
                <div class="table-responsive border rounded-3 bg-white">
                    <table class="table table-sm table-hover mb-0">
                        <thead class="table-light font-11 text-muted text-uppercase">
                            <tr>
                                <th style="width: 32px;" class="text-center">#</th>
                                <th>No. Surat Jalan / Tgl</th>
                                <th>Driver / Kendaraan</th>
                                <th class="text-center" style="width: 80px;">Jml Item</th>
                                <th class="text-end" style="width: 65px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="soDrawerDeliveriesTbody" class="font-12">
                            {{-- Dynamically populated --}}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="offcanvas-footer border-top p-3 d-flex align-items-center justify-content-between gap-2 flex-wrap">
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <a href="#" id="soDrawerDetailBtn" class="btn btn-primary d-flex align-items-center gap-1 shadow-xs">
                    <i class="mdi mdi-open-in-new me-1"></i> Buka SO
                </a>
                <a href="#" id="soDrawerCreateDeliveryBtn" class="btn btn-warning d-flex align-items-center gap-1 shadow-xs text-dark fw-semibold" style="display: none;">
                    <i class="mdi mdi-truck-fast-outline me-1"></i> Buat Surat Jalan
                </a>
            </div>
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="offcanvas">
                Tutup
            </button>
        </div>
    </div>

    {{-- Toast notification --}}
    <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1100;">
        <div id="bulk-toast" class="toast text-white" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header bg-transparent text-white border-0">
                <i class="mdi mdi-information-outline me-2"></i>
                <strong class="me-auto">Info</strong>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
            </div>
            <div class="toast-body fw-semibold"></div>
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

        /* ── Bulk Action Floating Bar ─────────────────────────────── */
        .bulk-action-bar {
            position: fixed;
            bottom: -90px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 1050;
            background: linear-gradient(135deg, #1e1e2e 0%, #2d2d44 100%);
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: 16px;
            padding: 12px 20px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.35), 0 2px 8px rgba(0,0,0,0.2);
            backdrop-filter: blur(12px);
            transition: bottom 0.35s cubic-bezier(0.34, 1.56, 0.64, 1);
            white-space: nowrap;
        }
        .bulk-action-bar.show {
            bottom: 24px;
        }
        .bulk-action-bar .form-select,
        .bulk-action-bar .form-control {
            background: rgba(255,255,255,0.08);
            border-color: rgba(255,255,255,0.15);
            color: #fff;
            font-size: 0.82rem;
        }
        .bulk-action-bar .form-select option {
            background: #2d2d44;
            color: #fff;
        }
        .bulk-action-bar .form-control::placeholder {
            color: rgba(255,255,255,0.4);
        }
        .bulk-action-bar .form-select:focus,
        .bulk-action-bar .form-control:focus {
            border-color: rgba(115,103,240,0.6);
            box-shadow: 0 0 0 2px rgba(115,103,240,0.2);
        }

        /* ── SO Item Quick Preview Button & Drawer (Matching Purchase Request) ─── */
        .btn-item-preview, .btn-so-item-preview {
            background: #f4f5f9;
            border: 1px solid #e2e5ec;
            color: #435971;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            transition: all 0.2s;
            cursor: pointer;
        }
        .btn-item-preview:hover, .btn-so-item-preview:hover {
            background: #696cff;
            border-color: #696cff;
            color: #ffffff !important;
            box-shadow: 0 4px 10px rgba(105, 108, 255, 0.25);
        }
        .btn-item-preview:hover i,
        .btn-item-preview:hover span,
        .btn-so-item-preview:hover i,
        .btn-so-item-preview:hover span {
            color: #ffffff !important;
        }
        #salesOrderItemOffcanvas {
            background: #ffffff;
        }

        /* Glassmorphism Backdrop Blur for Offcanvas Drawer (Like PR & Modal) */
        .offcanvas-backdrop {
            transition: opacity 0.3s cubic-bezier(0.16, 1, 0.3, 1), backdrop-filter 0.3s cubic-bezier(0.16, 1, 0.3, 1) !important;
        }
        .offcanvas-backdrop.show {
            backdrop-filter: blur(8px) saturate(160%) !important;
            -webkit-backdrop-filter: blur(8px) saturate(160%) !important;
            background-color: rgba(15, 23, 42, 0.5) !important;
            opacity: 1 !important;
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
                    order: [[2, 'desc']], // Sort by Date descending (index 2, shifted by checkbox)
                    pageLength: 10,
                    columnDefs: [{
                        targets: 0,
                        orderable: false,
                        searchable: false,
                        className: 'no-sort'
                    }],
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
                    if (i === 0) { // Skip checkbox column
                        $(this).html('');
                        return;
                    }
                    if (i === 8) { // Skip Sales avatar column (index 8, shifted by checkbox)
                        $(this).html('');
                        return;
                    }
                    if (i === 4) { // Selection filter for Type & Description (shifted)
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
                    if (i === 6) { // Selection filter for Payment (shifted)
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
                    if (i === 7) { // Selection filter for Flag (shifted)
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
                    order: [[2, 'desc']], // Sort by Date (index 2, shifted by checkbox)
                    pageLength: 10,
                    columnDefs: [{
                        targets: 0,
                        orderable: false,
                        searchable: false,
                        className: 'no-sort'
                    }],
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
                    if (i === 0) { // Skip checkbox column
                        $(this).html('');
                        return;
                    }
                    if (i === 6) { // Skip Sales avatar column (index 6, shifted by checkbox)
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
                    order: [[2, 'desc']], // Sort by Date (index 2, shifted by checkbox)
                    pageLength: 10,
                    columnDefs: [{
                        targets: 0,
                        orderable: false,
                        searchable: false,
                        className: 'no-sort'
                    }],
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
                    if (i === 0) { // Skip checkbox column
                        $(this).html('');
                        return;
                    }
                    if (i === 7) { // Skip Sales avatar column (index 7, shifted by checkbox)
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

    {{-- ============================================================ --}}
    {{-- BULK ACTION & LINK SO FEATURE JS                             --}}
    {{-- ============================================================ --}}
    <script>
    $(function () {
        var CSRF = $('meta[name="csrf-token"]').attr('content');
        var BULK_URL = '{{ route("sales-order.bulk-status") }}';
        var LINK_URL = '{{ route("sales-order.link") }}';
        var UNLINK_BASE_URL = '{{ url("/sales-order/unlink") }}';
        var LINKED_GROUP_BASE_URL = '{{ url("/sales-order/linked-group") }}';

        // ─── Floating Bulk Bar ────────────────────────────────────────
        var $bar = $('#bulk-action-bar');
        var $linkBtn = $('#btn-bulk-link');

        function getChecked() {
            return $('.bulk-row-check:checked');
        }

        function updateBar() {
            var $checked = getChecked();
            var n = $checked.length;
            if (n > 0) {
                $('#bulk-count').text(n + ' PO dipilih');
                $bar.addClass('show');

                // Show Link SO button only if >= 2 POs are selected
                if (n >= 2) {
                    $linkBtn.show();
                } else {
                    $linkBtn.hide();
                }
            } else {
                $bar.removeClass('show');
                $linkBtn.hide();
            }
        }

        // ─── Select all in same table ─────────────────────────────────
        $(document).on('change', '.bulk-check-all', function () {
            var tableId = '#' + $(this).data('table');
            $(tableId).find('.bulk-row-check').prop('checked', this.checked);
            updateBar();
        });

        // ─── Individual row checkbox ──────────────────────────────────
        $(document).on('change', '.bulk-row-check', function () {
            var $table = $(this).closest('table');
            var totalInTable = $table.find('.bulk-row-check').length;
            var checkedInTable = $table.find('.bulk-row-check:checked').length;
            $table.find('.bulk-check-all').prop('indeterminate', checkedInTable > 0 && checkedInTable < totalInTable);
            $table.find('.bulk-check-all').prop('checked', checkedInTable === totalInTable && totalInTable > 0);
            updateBar();
        });

        // ─── Deselect all ─────────────────────────────────────────────
        $('#bulk-deselect').on('click', function () {
            $('.bulk-row-check, .bulk-check-all').prop('checked', false).prop('indeterminate', false);
            updateBar();
        });

        // ─── Apply bulk status ────────────────────────────────────────
        $('#btn-bulk-apply').on('click', function () {
            var status = $('#bulk-status-select').val();
            var note   = $('#bulk-note-input').val().trim();
            var ids    = getChecked().map(function () { return parseInt($(this).val()); }).get();

            if (!status) { alert('Pilih status terlebih dahulu.'); return; }
            if (ids.length === 0) { alert('Tidak ada PO yang dipilih.'); return; }

            var label = $('#bulk-status-select option:selected').text();
            if (!confirm('Update ' + ids.length + ' PO ke status "' + label + '"?\n\n' + (status == 6 ? 'Catatan: Barang Keluar tidak otomatis dibuat. Akan ditandai dengan note.' : ''))) return;

            var $btn = $(this);
            $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Memproses...');

            $.ajax({
                url: BULK_URL,
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': CSRF },
                data: { ids: ids, status: status, note: note },
                success: function (res) {
                    showToast(res.message, 'success');
                    $('.bulk-row-check, .bulk-check-all').prop('checked', false).prop('indeterminate', false);
                    updateBar();
                    setTimeout(function () { location.reload(); }, 1500);
                },
                error: function (xhr) {
                    var msg = xhr.responseJSON?.message || 'Terjadi kesalahan.';
                    showToast(msg, 'danger');
                },
                complete: function () {
                    $btn.prop('disabled', false).html('<i class="mdi mdi-check me-1"></i>Terapkan');
                }
            });
        });

        // ─── Open Link SO Modal ───────────────────────────────────────
        $('#btn-bulk-link').on('click', function () {
            var items = [];
            var clients = new Set();
            getChecked().each(function () {
                var id = $(this).val();
                var $row = $(this).closest('tr');
                var no = $(this).data('no') || $row.find('.col-so-po a').text().trim() || ('ID #' + id);
                var customer = $(this).data('client') || $row.find('.col-customer span.text-truncate').text().trim() || '-';
                items.push({ id: id, no: no, customer: customer });
                if (customer && customer !== '-') {
                    clients.add(customer.trim().toLowerCase());
                }
            });

            if (items.length < 2) {
                alert('Pilih minimal 2 Sales Order untuk dikaitkan.');
                return;
            }

            if (clients.size > 1) {
                var clientList = Array.from(new Set(items.map(function(i) { return i.customer; }))).join('\n• ');
                alert('PERINGATAN: Sales Order yang dipilih berasal dari Client yang berbeda:\n• ' + clientList + '\n\nSales Order yang dikaitkan WAJIB berasal dari 1 Client yang sama.');
                return;
            }

            var html = '';
            items.forEach(function (item, index) {
                var checked = index === 0 ? 'checked' : '';
                html += '<label class="list-group-item list-group-item-action d-flex align-items-center justify-content-between p-3 cursor-pointer border-bottom">' +
                    '<div class="d-flex align-items-center">' +
                        '<input class="form-check-input me-3 link-parent-radio" type="radio" name="selected_parent_id" value="' + item.id + '" id="parent-radio-' + item.id + '" ' + checked + '>' +
                        '<div>' +
                            '<strong class="d-block text-dark">' + item.no + '</strong>' +
                            '<small class="text-muted"><i class="mdi mdi-office-building me-1"></i>' + item.customer + '</small>' +
                        '</div>' +
                    '</div>' +
                    '<span class="badge ' + (index === 0 ? 'bg-label-primary' : 'bg-label-secondary') + ' rounded-pill radio-badge">' +
                        (index === 0 ? 'SO Utama' : 'SO Turunan') +
                    '</span>' +
                '</label>';
            });

            $('#link-so-list').html(html);

            // Update badge dynamically when user switches parent radio
            $(document).on('change', '.link-parent-radio', function () {
                $('.link-parent-radio').each(function () {
                    var $badge = $(this).closest('.list-group-item').find('.radio-badge');
                    if (this.checked) {
                        $badge.removeClass('bg-label-secondary').addClass('bg-label-primary').text('SO Utama');
                    } else {
                        $badge.removeClass('bg-label-primary').addClass('bg-label-secondary').text('SO Turunan');
                    }
                });
            });

            var modal = new bootstrap.Modal(document.getElementById('modal-link-so'));
            modal.show();
        });

        // ─── Confirm Link SO ──────────────────────────────────────────
        $('#btn-confirm-link-so').on('click', function () {
            var parentId = $('input[name="selected_parent_id"]:checked').val();
            if (!parentId) {
                alert('Pilih salah satu SO sebagai SO Utama.');
                return;
            }

            var childIds = [];
            getChecked().each(function () {
                var id = $(this).val();
                if (id != parentId) {
                    childIds.push(id);
                }
            });

            var $btn = $(this);
            $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Menyimpan...');

            $.ajax({
                url: LINK_URL,
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': CSRF },
                data: { parent_id: parentId, child_ids: childIds },
                success: function (res) {
                    bootstrap.Modal.getInstance(document.getElementById('modal-link-so')).hide();
                    showToast(res.message, 'success');
                    $('.bulk-row-check, .bulk-check-all').prop('checked', false).prop('indeterminate', false);
                    updateBar();
                    setTimeout(function () { location.reload(); }, 1200);
                },
                error: function (xhr) {
                    var msg = xhr.responseJSON?.message || 'Gagal mengaitkan Sales Order.';
                    alert(msg);
                },
                complete: function () {
                    $btn.prop('disabled', false).html('<i class="mdi mdi-link-variant me-1"></i>Kaitkan Sales Order');
                }
            });
        });

        // ─── View Linked Group Modal & Unlink ─────────────────────────
        $(document).on('click', '.view-linked-group', function (e) {
            e.preventDefault();
            var id = $(this).data('id');
            var modalEl = document.getElementById('modal-view-linked-group');
            var modal = new bootstrap.Modal(modalEl);
            modal.show();

            $('#linked-group-loading').show();
            $('#linked-group-content').hide();

            $.ajax({
                url: LINKED_GROUP_BASE_URL + '/' + id,
                method: 'GET',
                success: function (res) {
                    if (!res.success) {
                        alert('Gagal mengambil data kaitan.');
                        modal.hide();
                        return;
                    }

                    $('#linked-parent-no').text(res.parent_no_pending);
                    $('#linked-total-count').text(res.total_linked + ' SO dalam grup');

                    var tbodyHtml = '';
                    res.members.forEach(function (m) {
                        var isParent = m.is_parent;
                        tbodyHtml += '<tr>' +
                            '<td>' +
                                '<a href="' + m.detail_url + '" target="_blank" class="fw-bold text-primary text-decoration-none">' +
                                    m.no_pending + ' <i class="mdi mdi-open-in-new" style="font-size: 0.75rem;"></i>' +
                                '</a>' +
                                '<small class="text-muted d-block">' + (m.title || '-') + '</small>' +
                            '</td>' +
                            '<td>' + m.company + '</td>' +
                            '<td>' + m.sales + '</td>' +
                            '<td>' + m.date + '</td>' +
                            '<td class="text-center">' +
                                (isParent
                                    ? '<span class="badge bg-label-primary px-2 py-1"><i class="mdi mdi-star me-1"></i>SO Utama</span>'
                                    : '<span class="badge bg-label-secondary px-2 py-1">Turunan</span>') +
                            '</td>' +
                            '<td class="text-center text-nowrap">' +
                                (isParent
                                    ? '<button class="btn btn-xs btn-outline-danger btn-unlink" data-id="' + m.id + '" data-all="1" title="Lepas semua kaitan">' +
                                          '<i class="mdi mdi-link-variant-off me-1"></i>Lepas Semua' +
                                      '</button>'
                                    : '<button class="btn btn-xs btn-outline-danger btn-unlink" data-id="' + m.id + '" data-all="0" title="Lepas dari SO Utama">' +
                                          '<i class="mdi mdi-link-variant-off me-1"></i>Lepas' +
                                      '</button>') +
                            '</td>' +
                        '</tr>';
                    });

                    $('#linked-group-table-body').html(tbodyHtml);
                    $('#linked-group-loading').hide();
                    $('#linked-group-content').show();
                },
                error: function () {
                    alert('Terjadi kesalahan saat memuat data kaitan.');
                    modal.hide();
                }
            });
        });

        // ─── Execute Unlink ───────────────────────────────────────────
        $(document).on('click', '.btn-unlink', function () {
            var id = $(this).data('id');
            var unlinkAll = $(this).data('all') == 1;
            var promptMsg = unlinkAll
                ? 'Lepas semua kaitan untuk grup Sales Order ini?'
                : 'Lepas kaitan Sales Order ini dari SO Utama?';

            if (!confirm(promptMsg)) return;

            var $btn = $(this);
            $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');

            $.ajax({
                url: UNLINK_BASE_URL + '/' + id,
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': CSRF },
                data: { unlink_all: unlinkAll ? 1 : 0 },
                success: function (res) {
                    bootstrap.Modal.getInstance(document.getElementById('modal-view-linked-group')).hide();
                    showToast(res.message, 'success');
                    setTimeout(function () { location.reload(); }, 1200);
                },
                error: function () {
                    alert('Gagal melepas kaitan.');
                    $btn.prop('disabled', false).html('<i class="mdi mdi-link-variant-off me-1"></i>Lepas');
                }
            });
        });

        // ─── Toast helper ─────────────────────────────────────────────
        function showToast(msg, type) {
            var $t = $('#bulk-toast');
            $t.removeClass('bg-success bg-danger bg-warning').addClass('bg-' + type);
            $t.find('.toast-body').text(msg);
            var toast = new bootstrap.Toast($t[0], { delay: 4000 });
            toast.show();
        }

        // ─── Quick Preview Items Drawer ──────────────────────────────
        window.soItemsData = {
            @foreach($allMasterOrders as $o)
                @if(!empty($o->drawer_data))
                    {{ $o->id }}: @json($o->drawer_data),
                @endif
            @endforeach
        };

        function openSalesOrderItemDrawer(data) {
            if (!data) return;

            $('#soDrawerNoPending').text(data.no_pending || '-');
            $('#soDrawerNoPendingText').text(data.no_pending || '-');
            $('#soDrawerCompany').text(data.company || '-');
            $('#soDrawerNoPo').text(data.no_po || '-');
            $('#soDrawerDate').text(data.formatted_date || '-');

            // Status Badge
            var statusBadgeHtml = '<span class="badge ' + (data.progress_badge || 'bg-label-primary') + ' rounded-pill px-3 py-1 font-11 fw-semibold">' + (data.progress_label || 'New') + '</span>';
            $('#soDrawerStatusBadge').html(statusBadgeHtml);

            // Payment Badge
            var paymentBadgeHtml = '<span class="badge ' + (data.payment_badge || 'bg-label-secondary') + ' rounded-pill px-2.5 py-1 font-11 fw-semibold" title="' + (data.payment_detail || '') + '">' + (data.payment_label || 'UNPAID') + '</span>';
            $('#soDrawerPaymentSlot').html(paymentBadgeHtml);

            // Sales box
            var salesName = data.sales_name || '-';
            var salesAv = data.sales_avatar
                ? '<img src="' + data.sales_avatar + '" class="rounded-circle me-1" style="width:24px;height:24px;object-fit:cover;" alt="' + salesName + '">'
                : '<span class="avatar-initial rounded-circle bg-label-primary font-10 fw-bold me-1" style="width:24px;height:24px;display:inline-flex;align-items:center;justify-content:center;">' + (salesName.substring(0, 2).toUpperCase()) + '</span>';
            $('#soDrawerSalesBox').html(salesAv + '<span class="fw-semibold text-heading font-12">' + salesName + '</span>');

            // Items table
            var tbodyHtml = '';
            var items = data.items || [];
            var totalQty = 0;
            var totalShipped = 0;

            if (items.length > 0) {
                items.forEach(function(item, idx) {
                    var qty = parseFloat(item.qty) || 0;
                    var qtyShipped = parseFloat(item.qty_shipped) || 0;
                    var qtyRemaining = item.qty_remaining !== undefined ? parseFloat(item.qty_remaining) : Math.max(0, qty - qtyShipped);

                    totalQty += qty;
                    totalShipped += qtyShipped;

                    var goBadge = '';
                    if (item.go) {
                        var isG = item.go.toLowerCase() === 'genuine';
                        goBadge = isG
                            ? '<span class="badge bg-label-primary font-10 ms-1">Genuine (G)</span>'
                            : '<span class="badge bg-label-info font-10 ms-1">Replacement (R)</span>';
                    }

                    var stockInfoHtml = '';
                    if (item.total_stock > 0 || item.bdg > 0 || item.bks > 0) {
                        var stockSuffClass = item.is_enough ? 'text-success' : 'text-danger';
                        var stockIcon = item.is_enough ? 'mdi-check-circle-outline' : 'mdi-alert-circle-outline';
                        stockInfoHtml = '<div class="mt-1" style="font-size: 10px;">' +
                            '<span class="' + stockSuffClass + ' fw-semibold d-block"><i class="mdi ' + stockIcon + ' me-1"></i>Stok: ' + item.total_stock + ' ' + (item.unit || '') + '</span>' +
                            '<span class="text-muted" style="font-size: 9.5px;">(BDG: ' + (item.bdg || 0) + ' | BKS: ' + (item.bks || 0) + ')</span>' +
                        '</div>';
                    }

                    var itemStatusBadge = item.status
                        ? '<span class="badge ' + (item.status_badge || 'bg-label-secondary') + ' rounded-pill px-2 py-0.5" style="font-size: 10px;">' + item.status + '</span>'
                        : '';

                    // Fulfillment badge & quantity display
                    var fulfillBadge = '';
                    if (qtyShipped >= qty && qty > 0) {
                        fulfillBadge = '<span class="badge bg-label-success font-10 py-0.5 px-1.5 mt-1 d-inline-block"><i class="mdi mdi-check-circle-outline me-0.5"></i>Tuntas</span>';
                    } else if (qtyShipped > 0) {
                        fulfillBadge = '<span class="badge bg-label-warning font-10 py-0.5 px-1.5 mt-1 d-inline-block"><i class="mdi mdi-truck-fast-outline me-0.5"></i>Kirim: ' + qtyShipped + '/' + qty + '</span>';
                    } else {
                        fulfillBadge = '<span class="badge bg-label-secondary font-10 py-0.5 px-1.5 mt-1 d-inline-block">Belum Kirim</span>';
                    }

                    tbodyHtml += '<tr>' +
                        '<td class="text-muted font-11 text-center align-middle">' + (idx + 1) + '</td>' +
                        '<td>' +
                            '<div class="fw-semibold text-heading font-12 d-flex align-items-center flex-wrap">' +
                                item.name +
                                goBadge +
                            '</div>' +
                            (item.description && item.description !== item.name ? '<div class="text-muted font-11 mt-0.5 text-truncate" style="max-width: 260px;" title="' + item.description + '">' + item.description + '</div>' : '') +
                            (item.note ? '<div class="text-muted font-10 mt-0.5 fst-italic"><i class="mdi mdi-note-text-outline me-1"></i>' + item.note + '</div>' : '') +
                        '</td>' +
                        '<td class="text-center align-middle">' +
                            '<div class="font-12 fw-bold text-heading">' + qty + ' <span class="font-11 text-muted fw-normal">' + (item.unit || 'pcs') + '</span></div>' +
                            '<div class="font-11 text-muted mt-0.5">Kirim: <strong class="text-success">' + qtyShipped + '</strong> | Sisa: <strong class="' + (qtyRemaining > 0 ? 'text-danger' : 'text-muted') + '">' + qtyRemaining + '</strong></div>' +
                            fulfillBadge +
                        '</td>' +
                        '<td class="text-center align-middle">' +
                            itemStatusBadge +
                            stockInfoHtml +
                        '</td>' +
                    '</tr>';
                });
                $('#soDrawerItemCount').text(items.length);
                $('#soDrawerTotalQty').text(totalQty + ' total qty (Kirim: ' + totalShipped + ')');
            } else {
                tbodyHtml = '<tr><td colspan="4" class="text-center py-4 text-muted font-12">Tidak ada rincian item.</td></tr>';
                $('#soDrawerItemCount').text('0');
                $('#soDrawerTotalQty').text('0 item');
            }

            $('#soDrawerItemsTbody').html(tbodyHtml);

            // Riwayat Surat Jalan (Delivery Notes)
            var deliveries = data.product_outs || [];
            if (deliveries.length > 0) {
                var delivHtml = '';
                deliveries.forEach(function(deliv, didx) {
                    var actBtn = '';
                    if (deliv.detail_route) {
                        actBtn = '<a href="' + deliv.detail_route + '" class="btn btn-xs btn-outline-primary" title="Lihat Surat Jalan"><i class="mdi mdi-eye-outline"></i></a>';
                    }
                    delivHtml += '<tr>' +
                        '<td class="text-muted font-11 text-center align-middle">' + (didx + 1) + '</td>' +
                        '<td class="align-middle">' +
                            '<strong class="text-primary font-monospace font-11 d-block">' + (deliv.code_product_out || '-') + '</strong>' +
                            '<span class="text-muted font-10">' + (deliv.formatted_date_out || deliv.date_out || '-') + '</span>' +
                        '</td>' +
                        '<td class="align-middle font-11">' +
                            '<div class="text-heading fw-semibold text-truncate" style="max-width: 140px;">' + (deliv.driver || '-') + '</div>' +
                            '<span class="text-muted font-10">' + (deliv.vehicle_number || '-') + '</span>' +
                        '</td>' +
                        '<td class="text-center align-middle">' +
                            '<span class="badge bg-label-info font-11">' + (deliv.item_count || 0) + ' item</span>' +
                        '</td>' +
                        '<td class="text-end align-middle">' +
                            actBtn +
                        '</td>' +
                    '</tr>';
                });
                $('#soDrawerDeliveriesTbody').html(delivHtml);
                $('#soDrawerDeliveriesCount').text(deliveries.length);
                $('#soDrawerDeliveriesWrapper').show();
            } else {
                $('#soDrawerDeliveriesTbody').html('');
                $('#soDrawerDeliveriesCount').text('0');
                $('#soDrawerDeliveriesWrapper').hide();
            }

            // Create Surat Jalan Button
            if (data.create_product_out_route) {
                $('#soDrawerCreateDeliveryBtn').attr('href', data.create_product_out_route).show();
            } else {
                $('#soDrawerCreateDeliveryBtn').hide();
            }

            // Detail Route
            if (data.detail_route) {
                $('#soDrawerDetailBtn').attr('href', data.detail_route).show();
            } else {
                $('#soDrawerDetailBtn').hide();
            }

            // Show offcanvas
            var offcanvasEl = document.getElementById('salesOrderItemOffcanvas');
            if (offcanvasEl) {
                var bsOffcanvas = bootstrap.Offcanvas.getOrCreateInstance(offcanvasEl);
                bsOffcanvas.show();
            }
        }

        $(document).on('click', '.btn-so-item-preview', function(e) {
            e.preventDefault();
            var id = $(this).data('id');
            var data = window.soItemsData ? window.soItemsData[id] : null;
            if (data) {
                openSalesOrderItemDrawer(data);
            }
        });
    });
    </script>
@endpush
