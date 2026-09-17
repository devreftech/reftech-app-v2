@extends('layouts.sales.app')
@section('title', 'HR Management')

@push('after-style')
<style>
    /* Lightweight styles for high performance */
    .hr-main-tabs .nav-link {
        font-size: 0.9375rem;
        font-weight: 600;
        padding: 0.6rem 1.25rem;
        border-radius: 8px;
        color: #697a8d;
        transition: all 0.15s ease-in-out;
    }
    .hr-main-tabs .nav-link:hover {
        color: #696cff;
        background-color: rgba(105, 108, 255, 0.08);
    }
    .hr-main-tabs .nav-link.active {
        color: #ffffff !important;
        background-color: #696cff !important;
        box-shadow: 0 3px 8px rgba(105, 108, 255, 0.35);
    }
    .hr-main-tabs .nav-link .badge {
        font-size: 0.75rem;
        font-weight: 600;
        padding: 0.25rem 0.55rem;
    }
    .hr-main-tabs .nav-link.active .badge {
        background-color: rgba(255, 255, 255, 0.28) !important;
        color: #ffffff !important;
    }

    .kpi-card {
        transition: transform 0.15s ease, box-shadow 0.15s ease;
        border: 1px solid rgba(0, 0, 0, 0.06);
        cursor: pointer;
    }
    .kpi-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.07) !important;
    }
    .kpi-card.active-kpi {
        border-color: #696cff !important;
        box-shadow: 0 0 0 2px rgba(105, 108, 255, 0.25) !important;
        background: #fdfdff !important;
    }

    .employee-table th, .hr-table th {
        font-size: 0.75rem;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        font-weight: 700;
        color: #566a7f;
        background-color: #f8f9fa;
        border-top: 1px solid #e7e7e8;
        border-bottom: 2px solid #e7e7e8;
        padding-top: 0.75rem;
        padding-bottom: 0.75rem;
    }
    .employee-table td, .hr-table td {
        vertical-align: middle;
        padding-top: 0.75rem;
        padding-bottom: 0.75rem;
    }
    .employee-row:hover, .hr-table tbody tr:hover {
        background-color: rgba(105, 108, 255, 0.025) !important;
    }

    .avatar-user-img {
        width: 40px;
        height: 40px;
        object-fit: cover;
        border-radius: 50%;
        border: 2px solid #fff;
        box-shadow: 0 2px 4px rgba(0,0,0,0.08);
    }
    .status-dot {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        display: inline-block;
        margin-right: 5px;
    }

    .status-nav-tabs .nav-link {
        font-size: 0.8125rem;
        font-weight: 500;
        border-radius: 20px;
        padding: 0.35rem 0.85rem;
        color: #697a8d;
        background-color: #f5f5f9;
        transition: all 0.15s ease;
    }
    .status-nav-tabs .nav-link:hover {
        color: #696cff;
        background-color: rgba(105, 108, 255, 0.08);
    }
    .status-nav-tabs .nav-link.active {
        color: #ffffff !important;
        background-color: #696cff !important;
    }
    .status-nav-tabs .nav-link.active .badge {
        background-color: rgba(255, 255, 255, 0.25) !important;
        color: #ffffff !important;
    }

    /* AJAX Table Loading Overlay */
    #employeeTableWrapper {
        position: relative;
        min-height: 200px;
    }
    .table-loading-overlay {
        position: absolute;
        inset: 0;
        background: rgba(255, 255, 255, 0.7);
        backdrop-filter: blur(1px);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 10;
        border-radius: 0.375rem;
    }
    .table-loading-overlay.active {
        display: flex;
    }
</style>
@endpush

@section('content')
    @php
        $activeTab = request('tab', 'employees');
        if (!in_array($activeTab, ['employees', 'departments', 'positions'])) {
            $activeTab = 'employees';
        }
    @endphp

    {{-- Header & Breadcrumb --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center py-2 mb-3 gap-2">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-style1 mb-1">
                    <li class="breadcrumb-item text-muted">
                        <i class="mdi mdi-account-tie-outline me-1"></i> HR Management
                    </li>
                    <li class="breadcrumb-item active fw-semibold text-primary" id="breadcrumbActiveText">
                        {{ $activeTab === 'departments' ? 'Departemen' : ($activeTab === 'positions' ? 'Posisi / Jabatan' : 'Data Karyawan') }}
                    </li>
                </ol>
            </nav>
            <h4 class="fw-bold mb-0 text-heading">
                <span>Manajemen HR & Organisasi</span>
            </h4>
        </div>
        <div class="d-flex align-items-center gap-2" id="headerActionButtons">
            {{-- Tab 1 Action --}}
            <a href="{{ route('employees.create') }}" class="btn btn-primary d-flex align-items-center shadow-sm tab-action-btn" id="btnCreateEmployee"
               style="{{ $activeTab === 'employees' ? '' : 'display: none !important;' }}">
                <i class="mdi mdi-account-plus-outline me-1 fs-5"></i> Tambah Karyawan
            </a>

            {{-- Tab 2 Action --}}
            <button type="button" class="btn btn-primary d-flex align-items-center shadow-sm tab-action-btn" id="btnCreateDepartment"
                    data-bs-toggle="modal" data-bs-target="#modalDepartment"
                    style="{{ $activeTab === 'departments' ? '' : 'display: none !important;' }}">
                <i class="mdi mdi-plus-circle-outline me-1 fs-5"></i> Tambah Departemen
            </button>

            {{-- Tab 3 Action --}}
            <button type="button" class="btn btn-primary d-flex align-items-center shadow-sm tab-action-btn" id="btnCreatePosition"
                    data-bs-toggle="modal" data-bs-target="#modalPosition"
                    style="{{ $activeTab === 'positions' ? '' : 'display: none !important;' }}">
                <i class="mdi mdi-plus-circle-outline me-1 fs-5"></i> Tambah Posisi
            </button>
        </div>
    </div>

    {{-- Notification Alerts --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-xs border-0 mb-3 d-flex align-items-center" role="alert">
            <i class="mdi mdi-check-circle fs-4 me-2 text-success"></i>
            <div class="flex-grow-1">
                <strong>Berhasil!</strong> {{ session('success') }}
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-xs border-0 mb-3 d-flex align-items-center" role="alert">
            <i class="mdi mdi-alert-circle fs-4 me-2 text-danger"></i>
            <div class="flex-grow-1">
                <strong>Terjadi Kesalahan!</strong> {{ session('error') }}
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('warning'))
        <div class="alert alert-warning alert-dismissible fade show shadow-xs border-0 mb-3 d-flex align-items-center" role="alert">
            <i class="mdi mdi-alert-outline fs-4 me-2 text-warning"></i>
            <div class="flex-grow-1">
                <strong>Perhatian!</strong> {{ session('warning') }}
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- 3 MAIN TABS NAV (Karyawan, Departemen, Posisi) --}}
    <div class="nav-align-top mb-4">
        <ul class="nav nav-pills hr-main-tabs gap-2 p-1 bg-white rounded shadow-sm border" id="hrMainTabs" role="tablist">
            <li class="nav-item">
                <button type="button" 
                        class="nav-link {{ $activeTab === 'employees' ? 'active' : '' }}" 
                        data-bs-toggle="tab" 
                        data-bs-target="#tab-employees" 
                        data-tab-name="employees"
                        data-title="Data Karyawan"
                        role="tab"
                        aria-selected="{{ $activeTab === 'employees' ? 'true' : 'false' }}">
                    <i class="mdi mdi-badge-account-horizontal-outline me-1"></i> Data Karyawan
                    <span class="badge rounded-pill bg-label-primary ms-1" id="mainTabBadgeEmployees">
                        {{ $stats['total'] ?? $employees->total() }}
                    </span>
                </button>
            </li>
            <li class="nav-item">
                <button type="button" 
                        class="nav-link {{ $activeTab === 'departments' ? 'active' : '' }}" 
                        data-bs-toggle="tab" 
                        data-bs-target="#tab-departments" 
                        data-tab-name="departments"
                        data-title="Departemen"
                        role="tab"
                        aria-selected="{{ $activeTab === 'departments' ? 'true' : 'false' }}">
                    <i class="mdi mdi-sitemap-outline me-1"></i> Departemen
                    <span class="badge rounded-pill bg-label-info ms-1">
                        {{ $departments->count() }}
                    </span>
                </button>
            </li>
            <li class="nav-item">
                <button type="button" 
                        class="nav-link {{ $activeTab === 'positions' ? 'active' : '' }}" 
                        data-bs-toggle="tab" 
                        data-bs-target="#tab-positions" 
                        data-tab-name="positions"
                        data-title="Posisi / Jabatan"
                        role="tab"
                        aria-selected="{{ $activeTab === 'positions' ? 'true' : 'false' }}">
                    <i class="mdi mdi-briefcase-outline me-1"></i> Posisi / Jabatan
                    <span class="badge rounded-pill bg-label-warning ms-1">
                        {{ $positions->count() }}
                    </span>
                </button>
            </li>
        </ul>

        {{-- TAB CONTENT --}}
        <div class="tab-content p-0 bg-transparent shadow-none border-0 mt-3">
            
            {{-- ============================================================== --}}
            {{-- TAB 1: DATA KARYAWAN                                            --}}
            {{-- ============================================================== --}}
            <div class="tab-pane fade {{ $activeTab === 'employees' ? 'show active' : '' }}" id="tab-employees" role="tabpanel">
                @php
                    $currentStatus = request('employment_status', '');
                    $probationReviewCount = ($stats['probation'] ?? 0) + ($stats['perlu_verifikasi'] ?? 0);
                @endphp

                {{-- KPI Metric Summary Cards --}}
                <div class="row g-3 mb-4">
                    <div class="col-12 col-sm-6 col-xl-3">
                        <div class="card kpi-card h-100 shadow-sm {{ empty($currentStatus) ? 'active-kpi' : '' }}" data-kpi-status="">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="text-muted fw-medium small">Semua Karyawan</span>
                                    <div class="avatar avatar-sm">
                                        <span class="avatar-initial rounded bg-label-primary">
                                            <i class="mdi mdi-account-group-outline mdi-20px"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="d-flex align-items-baseline gap-2">
                                    <h3 class="mb-0 fw-bold" id="kpiTotalNum">{{ $stats['total'] ?? 0 }}</h3>
                                    <span class="text-muted small">Orang</span>
                                </div>
                                <div class="text-muted small mt-2 d-flex align-items-center">
                                    <i class="mdi mdi-database-outline me-1 text-primary"></i> Seluruh data aktif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-sm-6 col-xl-3">
                        <div class="card kpi-card h-100 shadow-sm {{ $currentStatus === 'Tetap' ? 'active-kpi' : '' }}" data-kpi-status="Tetap">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="text-muted fw-medium small">Karyawan Tetap</span>
                                    <div class="avatar avatar-sm">
                                        <span class="avatar-initial rounded bg-label-success">
                                            <i class="mdi mdi-shield-check-outline mdi-20px"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="d-flex align-items-baseline gap-2">
                                    <h3 class="mb-0 fw-bold text-success" id="kpiTetapNum">{{ $stats['tetap'] ?? 0 }}</h3>
                                    <span class="text-muted small">Pegawai</span>
                                </div>
                                <div class="text-muted small mt-2 d-flex align-items-center">
                                    <span class="status-dot bg-success"></span> Status permanen
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-sm-6 col-xl-3">
                        <div class="card kpi-card h-100 shadow-sm {{ $currentStatus === 'Kontrak' ? 'active-kpi' : '' }}" data-kpi-status="Kontrak">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="text-muted fw-medium small">Karyawan Kontrak</span>
                                    <div class="avatar avatar-sm">
                                        <span class="avatar-initial rounded bg-label-info">
                                            <i class="mdi mdi-file-document-edit-outline mdi-20px"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="d-flex align-items-baseline gap-2">
                                    <h3 class="mb-0 fw-bold text-info" id="kpiKontrakNum">{{ $stats['kontrak'] ?? 0 }}</h3>
                                    <span class="text-muted small">Pegawai</span>
                                </div>
                                <div class="text-muted small mt-2 d-flex align-items-center">
                                    <span class="status-dot bg-info"></span> Kontrak PKWT
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-sm-6 col-xl-3">
                        <div class="card kpi-card h-100 shadow-sm {{ $currentStatus === 'Probation' ? 'active-kpi' : '' }}" data-kpi-status="Probation">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="text-muted fw-medium small">Probation & Review</span>
                                    <div class="avatar avatar-sm">
                                        <span class="avatar-initial rounded bg-label-warning">
                                            <i class="mdi mdi-account-clock-outline mdi-20px"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="d-flex align-items-baseline gap-2">
                                    <h3 class="mb-0 fw-bold text-warning" id="kpiProbationNum">{{ $probationReviewCount }}</h3>
                                    <span class="text-muted small">Pegawai</span>
                                </div>
                                <div class="text-muted small mt-2 d-flex align-items-center">
                                    <span class="status-dot bg-warning"></span> {{ $stats['probation'] ?? 0 }} Probation
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Filter Toolbar Card --}}
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body p-3">
                        <form method="GET" action="{{ route('employees.index') }}" id="filterForm">
                            <input type="hidden" name="employment_status" id="employmentStatusInput" value="{{ $currentStatus }}">
                            <input type="hidden" name="tab" value="employees">

                            <div class="row g-2 align-items-center mb-3">
                                <div class="col-12 col-md-5">
                                    <div class="input-group input-group-merge shadow-none">
                                        <span class="input-group-text bg-light border-end-0 text-muted">
                                            <i class="mdi mdi-magnify fs-5"></i>
                                        </span>
                                        <input type="text" name="search" id="searchInput" value="{{ request('search') }}" 
                                               class="form-control bg-light border-start-0 ps-0" 
                                               placeholder="Cari nama, NIK, atau email..." autocomplete="off">
                                        <button type="button" class="input-group-text bg-light text-muted border-start-0" id="clearSearchBtn" 
                                                style="{{ request('search') ? '' : 'display:none;' }}">
                                            <i class="mdi mdi-close-circle"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="col-12 col-md-4">
                                    <select name="id_department" id="departmentSelect" class="form-select bg-light">
                                        <option value="">Semua Departemen</option>
                                        @foreach ($departments as $dept)
                                            <option value="{{ $dept->id }}" @selected(request('id_department') == $dept->id)>
                                                {{ $dept->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-12 col-md-3 d-flex gap-2">
                                    <button type="submit" class="btn btn-primary flex-grow-1">
                                        <i class="mdi mdi-filter-variant me-1"></i> Filter
                                    </button>
                                    <button type="button" class="btn btn-label-secondary" id="resetFiltersBtn" title="Reset Filter">
                                        <i class="mdi mdi-refresh"></i>
                                    </button>
                                </div>
                            </div>

                            {{-- Status Cepat Tabs --}}
                            <div class="pt-2 border-top">
                                <div class="d-flex flex-wrap align-items-center gap-2">
                                    <span class="text-muted small fw-bold text-uppercase me-1">Status:</span>
                                    <ul class="nav nav-pills status-nav-tabs" id="statusPillsTab" role="tablist">
                                        <li class="nav-item">
                                            <button type="button" class="nav-link status-tab-btn {{ empty($currentStatus) ? 'active' : '' }}" data-status="">
                                                Semua <span class="badge rounded-pill bg-label-secondary ms-1" id="tabBadgeTotal">{{ $stats['total'] ?? 0 }}</span>
                                            </button>
                                        </li>
                                        <li class="nav-item">
                                            <button type="button" class="nav-link status-tab-btn {{ $currentStatus === 'Tetap' ? 'active' : '' }}" data-status="Tetap">
                                                <span class="status-dot bg-success"></span> Tetap <span class="badge rounded-pill bg-label-success ms-1" id="tabBadgeTetap">{{ $stats['tetap'] ?? 0 }}</span>
                                            </button>
                                        </li>
                                        <li class="nav-item">
                                            <button type="button" class="nav-link status-tab-btn {{ $currentStatus === 'Kontrak' ? 'active' : '' }}" data-status="Kontrak">
                                                <span class="status-dot bg-info"></span> Kontrak <span class="badge rounded-pill bg-label-info ms-1" id="tabBadgeKontrak">{{ $stats['kontrak'] ?? 0 }}</span>
                                            </button>
                                        </li>
                                        <li class="nav-item">
                                            <button type="button" class="nav-link status-tab-btn {{ $currentStatus === 'Probation' ? 'active' : '' }}" data-status="Probation">
                                                <span class="status-dot bg-warning"></span> Probation <span class="badge rounded-pill bg-label-warning ms-1" id="tabBadgeProbation">{{ $stats['probation'] ?? 0 }}</span>
                                            </button>
                                        </li>
                                        <li class="nav-item">
                                            <button type="button" class="nav-link status-tab-btn {{ $currentStatus === 'Perlu Verifikasi' ? 'active' : '' }}" data-status="Perlu Verifikasi">
                                                <span class="status-dot bg-danger"></span> Perlu Verifikasi <span class="badge rounded-pill bg-label-danger ms-1" id="tabBadgeVerifikasi">{{ $stats['perlu_verifikasi'] ?? 0 }}</span>
                                            </button>
                                        </li>
                                        <li class="nav-item">
                                            <button type="button" class="nav-link status-tab-btn {{ $currentStatus === 'Resign' ? 'active' : '' }}" data-status="Resign">
                                                Resign <span class="badge rounded-pill bg-label-secondary ms-1" id="tabBadgeResign">{{ $stats['resign'] ?? 0 }}</span>
                                            </button>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Table Wrapper with AJAX --}}
                <div class="card shadow-sm border-0" id="employeeTableWrapper">
                    <div class="table-loading-overlay" id="tableLoadingOverlay">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Memuat...</span>
                        </div>
                    </div>
                    <div id="tableContentContainer">
                        @include('pages.hr.employees._table', ['employees' => $employees])
                    </div>
                </div>
            </div>

            {{-- ============================================================== --}}
            {{-- TAB 2: DEPARTEMEN                                               --}}
            {{-- ============================================================== --}}
            <div class="tab-pane fade {{ $activeTab === 'departments' ? 'show active' : '' }}" id="tab-departments" role="tabpanel">
                <div class="card shadow-sm border-0">
                    <div class="card-header border-bottom py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
                        <div class="d-flex align-items-center gap-2">
                            <i class="mdi mdi-sitemap-outline text-primary fs-5"></i>
                            <h5 class="card-title mb-0 fw-bold">Daftar Departemen</h5>
                            <span class="badge bg-label-primary rounded-pill ms-1 font-monospace">{{ $departments->count() }} Departemen</span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <div id="bulkActionDepartments" class="d-none align-items-center gap-2">
                                <span class="badge bg-label-danger font-monospace fs-7" id="selectedDepartmentsCount">0 Terpilih</span>
                                <button type="button" class="btn btn-sm btn-danger shadow-xs" id="btnBulkDeleteDepartments">
                                    <i class="mdi mdi-trash-can-outline me-1"></i> Hapus Terpilih
                                </button>
                            </div>
                            <button type="button" class="btn btn-sm btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#modalDepartment" onclick="openAddDepartmentModal()">
                                <i class="mdi mdi-plus-circle-outline me-1"></i> Tambah Departemen
                            </button>
                        </div>
                    </div>
                    <div class="table-responsive text-nowrap">
                        <table class="table hr-table mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 40px;" class="text-center">
                                        <input type="checkbox" class="form-check-input" id="checkAllDepartments" title="Pilih Semua">
                                    </th>
                                    <th>Nama Departemen</th>
                                    <th>Kode</th>
                                    <th>Induk Departemen</th>
                                    <th class="text-center">Jml. Posisi</th>
                                    <th class="text-center">Jml. Karyawan</th>
                                    <th>Status</th>
                                    <th class="text-center" style="width: 100px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($departments as $dept)
                                    <tr>
                                        <td class="text-center">
                                            <input type="checkbox" class="form-check-input department-checkbox" value="{{ $dept->id }}" data-name="{{ $dept->name }}">
                                        </td>
                                        <td class="fw-semibold text-heading">{{ $dept->name }}</td>
                                        <td>
                                            @if ($dept->code)
                                                <span class="badge bg-light text-dark border font-monospace">{{ $dept->code }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>{{ $dept->parent?->name ?? '-' }}</td>
                                        <td class="text-center">
                                            <span class="badge bg-label-info font-monospace">{{ $dept->positions_count }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-label-primary font-monospace">{{ $dept->employees_count }}</span>
                                        </td>
                                        <td>
                                            <span class="badge rounded-pill bg-label-{{ $dept->is_active ? 'success' : 'secondary' }} px-2 py-1">
                                                <span class="status-dot bg-{{ $dept->is_active ? 'success' : 'secondary' }}"></span>
                                                {{ $dept->is_active ? 'Aktif' : 'Nonaktif' }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <div class="d-inline-flex gap-1">
                                                <button type="button" class="btn btn-sm btn-icon btn-label-primary rounded-pill btn-edit-department"
                                                        data-id="{{ $dept->id }}"
                                                        data-name="{{ $dept->name }}"
                                                        data-code="{{ $dept->code }}"
                                                        data-parent="{{ $dept->parent_id }}"
                                                        data-active="{{ $dept->is_active ? '1' : '0' }}"
                                                        title="Edit">
                                                    <i class="mdi mdi-pencil-outline"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-icon btn-label-danger rounded-pill btn-delete-item"
                                                        data-url="{{ route('departments.destroy', $dept->id) }}"
                                                        data-name="Departemen {{ $dept->name }}"
                                                        title="Hapus">
                                                    <i class="mdi mdi-trash-can-outline"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4 text-muted">Belum ada data departemen.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- ============================================================== --}}
            {{-- TAB 3: POSISI / JABATAN                                         --}}
            {{-- ============================================================== --}}
            <div class="tab-pane fade {{ $activeTab === 'positions' ? 'show active' : '' }}" id="tab-positions" role="tabpanel">
                <div class="card shadow-sm border-0">
                    <div class="card-header border-bottom py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
                        <div class="d-flex align-items-center gap-2">
                            <i class="mdi mdi-briefcase-outline text-primary fs-5"></i>
                            <h5 class="card-title mb-0 fw-bold">Daftar Posisi / Jabatan</h5>
                            <span class="badge bg-label-warning rounded-pill ms-1 font-monospace">{{ $positions->count() }} Posisi</span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <div id="bulkActionPositions" class="d-none align-items-center gap-2">
                                <span class="badge bg-label-danger font-monospace fs-7" id="selectedPositionsCount">0 Terpilih</span>
                                <button type="button" class="btn btn-sm btn-danger shadow-xs" id="btnBulkDeletePositions">
                                    <i class="mdi mdi-trash-can-outline me-1"></i> Hapus Terpilih
                                </button>
                            </div>
                            <button type="button" class="btn btn-sm btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#modalPosition" onclick="openAddPositionModal()">
                                <i class="mdi mdi-plus-circle-outline me-1"></i> Tambah Posisi
                            </button>
                        </div>
                    </div>
                    <div class="table-responsive text-nowrap">
                        <table class="table hr-table mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 40px;" class="text-center">
                                        <input type="checkbox" class="form-check-input" id="checkAllPositions" title="Pilih Semua">
                                    </th>
                                    <th>Nama Posisi</th>
                                    <th>Departemen</th>
                                    <th class="text-center">Level Wewenang</th>
                                    <th class="text-center">Jml. Karyawan</th>
                                    <th>Status</th>
                                    <th class="text-center" style="width: 100px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($positions as $pos)
                                    <tr>
                                        <td class="text-center">
                                            <input type="checkbox" class="form-check-input position-checkbox" value="{{ $pos->id }}" data-name="{{ $pos->name }}">
                                        </td>
                                        <td class="fw-semibold text-heading">{{ $pos->name }}</td>
                                        <td>
                                            @if ($pos->department)
                                                <span class="badge bg-label-info">{{ $pos->department->name }}</span>
                                            @else
                                                <span class="text-muted small">Tanpa Departemen</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-label-dark font-monospace">Level {{ $pos->level }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-label-primary font-monospace">{{ $pos->employees_count }}</span>
                                        </td>
                                        <td>
                                            <span class="badge rounded-pill bg-label-{{ $pos->is_active ? 'success' : 'secondary' }} px-2 py-1">
                                                <span class="status-dot bg-{{ $pos->is_active ? 'success' : 'secondary' }}"></span>
                                                {{ $pos->is_active ? 'Aktif' : 'Nonaktif' }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <div class="d-inline-flex gap-1">
                                                <button type="button" class="btn btn-sm btn-icon btn-label-primary rounded-pill btn-edit-position"
                                                        data-id="{{ $pos->id }}"
                                                        data-name="{{ $pos->name }}"
                                                        data-department="{{ $pos->id_department }}"
                                                        data-level="{{ $pos->level }}"
                                                        data-active="{{ $pos->is_active ? '1' : '0' }}"
                                                        title="Edit">
                                                    <i class="mdi mdi-pencil-outline"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-icon btn-label-danger rounded-pill btn-delete-item"
                                                        data-url="{{ route('positions.destroy', $pos->id) }}"
                                                        data-name="Posisi {{ $pos->name }}"
                                                        title="Hapus">
                                                    <i class="mdi mdi-trash-can-outline"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-muted">Belum ada data posisi.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- ============================================================== --}}
    {{-- MODAL CRUD: DEPARTEMEN                                         --}}
    {{-- ============================================================== --}}
    <div class="modal fade" id="modalDepartment" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form id="formDepartment" method="POST" action="{{ route('departments.store') }}" class="modal-content">
                @csrf
                <input type="hidden" name="_method" id="deptMethod" value="POST">
                <div class="modal-header border-bottom py-3">
                    <h5 class="modal-title fw-bold" id="modalDepartmentTitle">Tambah Departemen</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Departemen <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="deptNameInput" class="form-control" placeholder="Contoh: Human Resources" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kode Singkatan</label>
                        <input type="text" name="code" id="deptCodeInput" class="form-control text-uppercase" maxlength="20" placeholder="Contoh: HRD, FIN">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Induk Departemen (Opsional)</label>
                        <select name="parent_id" id="deptParentSelect" class="form-select">
                            <option value="">- Tanpa Induk (Departemen Utama) -</option>
                            @foreach ($departments as $parentDept)
                                <option value="{{ $parentDept->id }}">{{ $parentDept->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-2">
                        <label class="form-label d-block">Status Keaktifan</label>
                        <div class="form-check form-switch mt-1">
                            <input type="hidden" name="is_active" value="0">
                            <input class="form-check-input" type="checkbox" name="is_active" id="deptActiveSwitch" value="1" checked>
                            <label class="form-check-label" for="deptActiveSwitch">Departemen Aktif</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top py-2">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="mdi mdi-content-save-outline me-1"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ============================================================== --}}
    {{-- MODAL CRUD: POSISI / JABATAN                                   --}}
    {{-- ============================================================== --}}
    <div class="modal fade" id="modalPosition" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form id="formPosition" method="POST" action="{{ route('positions.store') }}" class="modal-content">
                @csrf
                <input type="hidden" name="_method" id="posMethod" value="POST">
                <div class="modal-header border-bottom py-3">
                    <h5 class="modal-title fw-bold" id="modalPositionTitle">Tambah Posisi / Jabatan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Posisi <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="posNameInput" class="form-control" placeholder="Contoh: Senior Technician" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Departemen</label>
                        <select name="id_department" id="posDeptSelect" class="form-select">
                            <option value="">- Belum Ditentukan -</option>
                            @foreach ($departments as $deptOption)
                                <option value="{{ $deptOption->id }}">{{ $deptOption->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Level Wewenang <span class="text-danger">*</span></label>
                        <input type="number" name="level" id="posLevelInput" class="form-control" min="1" max="20" value="1" required>
                        <div class="form-text small">Makin tinggi angka level, makin tinggi wewenang persetujuan (approval).</div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label d-block">Status Keaktifan</label>
                        <div class="form-check form-switch mt-1">
                            <input type="hidden" name="is_active" value="0">
                            <input class="form-check-input" type="checkbox" name="is_active" id="posActiveSwitch" value="1" checked>
                            <label class="form-check-label" for="posActiveSwitch">Posisi Aktif</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top py-2">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="mdi mdi-content-save-outline me-1"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Universal Delete Form --}}
    <form id="genericDeleteForm" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>

    {{-- Bulk Delete Forms --}}
    <form id="formBulkStatusEmployees" method="POST" action="{{ route('employees.bulk-status') }}" style="display: none;">
        @csrf
        <input type="hidden" name="status" id="bulkStatusInput" value="">
    </form>
    <form id="formBulkDeleteEmployees" method="POST" action="{{ route('employees.bulk-destroy') }}" style="display: none;">
        @csrf
    </form>
    <form id="formBulkDeleteDepartments" method="POST" action="{{ route('departments.bulk-destroy') }}" style="display: none;">
        @csrf
    </form>
    <form id="formBulkDeletePositions" method="POST" action="{{ route('positions.bulk-destroy') }}" style="display: none;">
        @csrf
    </form>
@endsection

@push('after-script')
<script>
    // Global functions for opening add modals
    function openAddDepartmentModal() {
        $('#formDepartment')[0].reset();
        $('#deptMethod').val('POST');
        $('#formDepartment').attr('action', "{{ route('departments.store') }}");
        $('#modalDepartmentTitle').text('Tambah Departemen');
        $('#deptActiveSwitch').prop('checked', true);
        $('#deptParentSelect').val('');
    }

    function openAddPositionModal() {
        $('#formPosition')[0].reset();
        $('#posMethod').val('POST');
        $('#formPosition').attr('action', "{{ route('positions.store') }}");
        $('#modalPositionTitle').text('Tambah Posisi / Jabatan');
        $('#posLevelInput').val(1);
        $('#posActiveSwitch').prop('checked', true);
        $('#posDeptSelect').val('');
    }

    document.addEventListener('DOMContentLoaded', function () {
        var $loadingOverlay = $('#tableLoadingOverlay');
        var $filterForm = $('#filterForm');
        var $searchInput = $('#searchInput');
        var $clearSearchBtn = $('#clearSearchBtn');
        var $deptSelect = $('#departmentSelect');
        var $statusInput = $('#employmentStatusInput');
        var searchTimer = null;

        // 1. MAIN TAB SWITCHING
        $('#hrMainTabs button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
            var tabName = $(e.target).data('tab-name');
            var tabTitle = $(e.target).data('title');
            $('#breadcrumbActiveText').text(tabTitle);

            // Toggle corresponding action button in header
            $('.tab-action-btn').attr('style', 'display: none !important;');
            if (tabName === 'employees') {
                $('#btnCreateEmployee').show();
            } else if (tabName === 'departments') {
                $('#btnCreateDepartment').show();
            } else if (tabName === 'positions') {
                $('#btnCreatePosition').show();
            }

            // Update browser URL without reload
            var newUrl = new URL(window.location.href);
            newUrl.searchParams.set('tab', tabName);
            window.history.replaceState(null, '', newUrl.toString());
        });

        // 2. FETCH EMPLOYEES VIA AJAX
        function fetchEmployees(url, pushState = true) {
            $loadingOverlay.addClass('active');

            $.ajax({
                url: url,
                type: 'GET',
                dataType: 'json',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                success: function (response) {
                    if (response.html) {
                        $('#tableContentContainer').html(response.html);
                    }
                    if (response.stats) {
                        var stats = response.stats;
                        if (stats.total !== undefined) {
                            $('#mainTabBadgeEmployees').text(stats.total);
                            $('#kpiTotalNum').text(stats.total);
                            $('#tabBadgeTotal').text(stats.total);
                        }
                        if (stats.tetap !== undefined) {
                            $('#kpiTetapNum').text(stats.tetap);
                            $('#tabBadgeTetap').text(stats.tetap);
                        }
                        if (stats.kontrak !== undefined) {
                            $('#kpiKontrakNum').text(stats.kontrak);
                            $('#tabBadgeKontrak').text(stats.kontrak);
                        }
                        if (stats.probation !== undefined || stats.perlu_verifikasi !== undefined) {
                            var p = parseInt(stats.probation || 0);
                            var v = parseInt(stats.perlu_verifikasi || 0);
                            $('#kpiProbationNum').text(p + v);
                            $('#tabBadgeProbation').text(p);
                            $('#tabBadgeVerifikasi').text(v);
                        }
                        if (stats.resign !== undefined) {
                            $('#tabBadgeResign').text(stats.resign);
                        }
                    }
                    if (pushState && window.history.pushState) {
                        window.history.pushState(null, '', url);
                    }
                },
                error: function (xhr, status, error) {
                    console.error('AJAX Load Error:', error);
                },
                complete: function () {
                    $loadingOverlay.removeClass('active');
                    updateEmployeesBulkState();
                }
            });
        }

        function getFilteredUrl(customParams = {}) {
            var actionUrl = "{{ route('employees.index') }}";
            var params = $filterForm.serializeArray();
            var paramObj = { tab: 'employees' };

            $.each(params, function (i, field) {
                if (field.value !== '') {
                    paramObj[field.name] = field.value;
                }
            });

            $.extend(paramObj, customParams);

            for (var key in paramObj) {
                if (paramObj[key] === '' || paramObj[key] === null || paramObj[key] === undefined) {
                    delete paramObj[key];
                }
            }

            var queryString = $.param(paramObj);
            return queryString ? actionUrl + '?' + queryString : actionUrl;
        }

        // Status Cepat Click
        $(document).on('click', '.status-tab-btn', function (e) {
            e.preventDefault();
            var status = $(this).data('status');
            $statusInput.val(status);
            $('.status-tab-btn').removeClass('active');
            $(this).addClass('active');

            $('.kpi-card').removeClass('active-kpi');
            $('.kpi-card[data-kpi-status="' + status + '"]').addClass('active-kpi');

            fetchEmployees(getFilteredUrl({ employment_status: status, page: 1 }));
        });

        // KPI Card Click
        $(document).on('click', '.kpi-card', function (e) {
            e.preventDefault();
            var status = $(this).data('kpi-status');
            $statusInput.val(status);
            $('.kpi-card').removeClass('active-kpi');
            $(this).addClass('active-kpi');

            $('.status-tab-btn').removeClass('active');
            $('.status-tab-btn[data-status="' + status + '"]').addClass('active');

            fetchEmployees(getFilteredUrl({ employment_status: status, page: 1 }));
        });

        // Pagination Click
        $(document).on('click', '#employeeTableWrapper .pagination a', function (e) {
            e.preventDefault();
            var url = $(this).attr('href');
            if (url && !$(this).parent().hasClass('disabled') && !$(this).parent().hasClass('active')) {
                fetchEmployees(url);
            }
        });

        // Search Input (Debounced)
        $searchInput.on('input', function () {
            var val = $(this).val();
            $clearSearchBtn.toggle(val.trim() !== '');

            clearTimeout(searchTimer);
            searchTimer = setTimeout(function () {
                fetchEmployees(getFilteredUrl({ page: 1 }));
            }, 350);
        });

        $clearSearchBtn.on('click', function () {
            $searchInput.val('');
            $(this).hide();
            fetchEmployees(getFilteredUrl({ search: '', page: 1 }));
        });

        $deptSelect.on('change', function () {
            fetchEmployees(getFilteredUrl({ page: 1 }));
        });

        $filterForm.on('submit', function (e) {
            e.preventDefault();
            clearTimeout(searchTimer);
            fetchEmployees(getFilteredUrl({ page: 1 }));
        });

        $(document).on('click', '#resetFiltersBtn, .btn-reset-ajax-filter', function (e) {
            e.preventDefault();
            $searchInput.val('');
            $clearSearchBtn.hide();
            $deptSelect.val('');
            $statusInput.val('');
            $('.status-tab-btn').removeClass('active');
            $('.status-tab-btn[data-status=""]').addClass('active');
            $('.kpi-card').removeClass('active-kpi');
            $('.kpi-card[data-kpi-status=""]').addClass('active-kpi');
            fetchEmployees("{{ route('employees.index', ['tab' => 'employees']) }}");
        });

        // 3. EDIT DEPARTMENT MODAL
        $(document).on('click', '.btn-edit-department', function () {
            var id = $(this).data('id');
            var name = $(this).data('name');
            var code = $(this).data('code') || '';
            var parentId = $(this).data('parent') || '';
            var isActive = $(this).data('active') == '1';

            $('#modalDepartmentTitle').text('Edit Departemen');
            $('#formDepartment').attr('action', "{{ url('departments') }}/" + id);
            $('#deptMethod').val('PUT');
            $('#deptNameInput').val(name);
            $('#deptCodeInput').val(code);
            $('#deptParentSelect').val(parentId);
            $('#deptActiveSwitch').prop('checked', isActive);

            var modal = new bootstrap.Modal(document.getElementById('modalDepartment'));
            modal.show();
        });

        // 4. EDIT POSITION MODAL
        $(document).on('click', '.btn-edit-position', function () {
            var id = $(this).data('id');
            var name = $(this).data('name');
            var deptId = $(this).data('department') || '';
            var level = $(this).data('level') || 1;
            var isActive = $(this).data('active') == '1';

            $('#modalPositionTitle').text('Edit Posisi / Jabatan');
            $('#formPosition').attr('action', "{{ url('positions') }}/" + id);
            $('#posMethod').val('PUT');
            $('#posNameInput').val(name);
            $('#posDeptSelect').val(deptId);
            $('#posLevelInput').val(level);
            $('#posActiveSwitch').prop('checked', isActive);

            var modal = new bootstrap.Modal(document.getElementById('modalPosition'));
            modal.show();
        });

        // 5. GENERIC DELETE WITH SWEETALERT2
        $(document).on('click', '.btn-delete-item, .btn-delete-employee', function (e) {
            e.preventDefault();
            var deleteUrl = $(this).data('url') || ("{{ url('employees') }}/" + $(this).data('id'));
            var itemName = $(this).data('name') || 'data ini';
            var form = document.getElementById('genericDeleteForm');
            form.action = deleteUrl;

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Hapus Data?',
                    text: 'Apakah Anda yakin ingin menghapus "' + itemName + '"? Tindakan ini tidak dapat dibatalkan.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: '<i class="mdi mdi-trash-can-outline me-1"></i> Ya, Hapus',
                    cancelButtonText: 'Batal',
                    customClass: {
                        confirmButton: 'btn btn-danger me-2',
                        cancelButton: 'btn btn-label-secondary'
                    },
                    buttonsStyling: false,
                    reverseButtons: true
                }).then(function (result) {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            } else {
                if (confirm('Hapus ' + itemName + '?')) {
                    form.submit();
                }
            }
        });

        // ── 6. BULK CHECKBOX & BULK DELETE HANDLERS ─────────────────────────
        // A. Employees Checkboxes
        function updateEmployeesBulkState() {
            var total = $('.employee-checkbox').length;
            var checked = $('.employee-checkbox:checked').length;
            $('#checkAllEmployees').prop('checked', total > 0 && total === checked);

            if (checked > 0) {
                $('#bulkActionEmployees').removeClass('d-none').addClass('d-flex');
                $('#selectedEmployeesCount').text(checked + ' Terpilih');
            } else {
                $('#bulkActionEmployees').removeClass('d-flex').addClass('d-none');
            }
        }

        $(document).on('change', '#checkAllEmployees', function () {
            $('.employee-checkbox').prop('checked', $(this).is(':checked'));
            updateEmployeesBulkState();
        });

        $(document).on('change', '.employee-checkbox', function () {
            updateEmployeesBulkState();
        });

        // Bulk Change Employment Status
        $(document).on('click', '.btn-bulk-status', function (e) {
            e.preventDefault();
            var targetStatus = $(this).data('status');
            var selected = [];
            $('.employee-checkbox:checked').each(function () {
                selected.push($(this).val());
            });

            if (selected.length === 0) return;

            var count = selected.length;
            var form = document.getElementById('formBulkStatusEmployees');
            $(form).find('input[name="ids[]"]').remove();
            selected.forEach(function (id) {
                $(form).append('<input type="hidden" name="ids[]" value="' + id + '">');
            });
            $('#bulkStatusInput').val(targetStatus);

            var badgeIcon = targetStatus === 'Resign' ? 'warning' : 'question';
            var confirmBtnClass = targetStatus === 'Resign' ? 'btn btn-danger me-2' : 'btn btn-primary me-2';

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Ubah Status ke "' + targetStatus + '"?',
                    text: 'Apakah Anda yakin ingin memindahkan status kepegawaian ' + count + ' karyawan terpilih menjadi ' + targetStatus + '?',
                    icon: badgeIcon,
                    showCancelButton: true,
                    confirmButtonText: '<i class="mdi mdi-check-circle-outline me-1"></i> Ya, Terapkan Status',
                    cancelButtonText: 'Batal',
                    customClass: {
                        confirmButton: confirmBtnClass,
                        cancelButton: 'btn btn-label-secondary'
                    },
                    buttonsStyling: false,
                    reverseButtons: true
                }).then(function (result) {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            } else {
                if (confirm('Ubah status ' + count + ' karyawan menjadi ' + targetStatus + '?')) {
                    form.submit();
                }
            }
        });

        $(document).on('click', '#btnBulkDeleteEmployees', function () {
            var selected = [];
            $('.employee-checkbox:checked').each(function () {
                selected.push($(this).val());
            });

            if (selected.length === 0) return;

            var count = selected.length;
            var form = document.getElementById('formBulkDeleteEmployees');
            $(form).find('input[name="ids[]"]').remove();
            selected.forEach(function (id) {
                $(form).append('<input type="hidden" name="ids[]" value="' + id + '">');
            });

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Hapus ' + count + ' Karyawan Terpilih?',
                    text: 'Apakah Anda yakin ingin menghapus ' + count + ' data karyawan yang dipilih? Tindakan ini tidak dapat dibatalkan.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: '<i class="mdi mdi-trash-can-outline me-1"></i> Ya, Hapus ' + count + ' Karyawan',
                    cancelButtonText: 'Batal',
                    customClass: {
                        confirmButton: 'btn btn-danger me-2',
                        cancelButton: 'btn btn-label-secondary'
                    },
                    buttonsStyling: false,
                    reverseButtons: true
                }).then(function (result) {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            } else {
                if (confirm('Hapus ' + count + ' karyawan terpilih?')) {
                    form.submit();
                }
            }
        });

        // B. Departments Checkboxes
        function updateDepartmentsBulkState() {
            var total = $('.department-checkbox').length;
            var checked = $('.department-checkbox:checked').length;
            $('#checkAllDepartments').prop('checked', total > 0 && total === checked);

            if (checked > 0) {
                $('#bulkActionDepartments').removeClass('d-none').addClass('d-flex');
                $('#selectedDepartmentsCount').text(checked + ' Terpilih');
            } else {
                $('#bulkActionDepartments').removeClass('d-flex').addClass('d-none');
            }
        }

        $(document).on('change', '#checkAllDepartments', function () {
            $('.department-checkbox').prop('checked', $(this).is(':checked'));
            updateDepartmentsBulkState();
        });

        $(document).on('change', '.department-checkbox', function () {
            updateDepartmentsBulkState();
        });

        $(document).on('click', '#btnBulkDeleteDepartments', function () {
            var selected = [];
            $('.department-checkbox:checked').each(function () {
                selected.push($(this).val());
            });

            if (selected.length === 0) return;

            var count = selected.length;
            var form = document.getElementById('formBulkDeleteDepartments');
            $(form).find('input[name="ids[]"]').remove();
            selected.forEach(function (id) {
                $(form).append('<input type="hidden" name="ids[]" value="' + id + '">');
            });

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Hapus ' + count + ' Departemen Terpilih?',
                    text: 'Departemen yang masih memiliki relasi karyawan, posisi, atau sub-departemen akan dilewati secara otomatis dan aman.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: '<i class="mdi mdi-trash-can-outline me-1"></i> Ya, Hapus Terpilih',
                    cancelButtonText: 'Batal',
                    customClass: {
                        confirmButton: 'btn btn-danger me-2',
                        cancelButton: 'btn btn-label-secondary'
                    },
                    buttonsStyling: false,
                    reverseButtons: true
                }).then(function (result) {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            } else {
                if (confirm('Hapus ' + count + ' departemen terpilih?')) {
                    form.submit();
                }
            }
        });

        // C. Positions Checkboxes
        function updatePositionsBulkState() {
            var total = $('.position-checkbox').length;
            var checked = $('.position-checkbox:checked').length;
            $('#checkAllPositions').prop('checked', total > 0 && total === checked);

            if (checked > 0) {
                $('#bulkActionPositions').removeClass('d-none').addClass('d-flex');
                $('#selectedPositionsCount').text(checked + ' Terpilih');
            } else {
                $('#bulkActionPositions').removeClass('d-flex').addClass('d-none');
            }
        }

        $(document).on('change', '#checkAllPositions', function () {
            $('.position-checkbox').prop('checked', $(this).is(':checked'));
            updatePositionsBulkState();
        });

        $(document).on('change', '.position-checkbox', function () {
            updatePositionsBulkState();
        });

        $(document).on('click', '#btnBulkDeletePositions', function () {
            var selected = [];
            $('.position-checkbox:checked').each(function () {
                selected.push($(this).val());
            });

            if (selected.length === 0) return;

            var count = selected.length;
            var form = document.getElementById('formBulkDeletePositions');
            $(form).find('input[name="ids[]"]').remove();
            selected.forEach(function (id) {
                $(form).append('<input type="hidden" name="ids[]" value="' + id + '">');
            });

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Hapus ' + count + ' Posisi Terpilih?',
                    text: 'Posisi yang masih digunakan oleh karyawan aktif akan dilewati secara otomatis dan aman.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: '<i class="mdi mdi-trash-can-outline me-1"></i> Ya, Hapus Terpilih',
                    cancelButtonText: 'Batal',
                    customClass: {
                        confirmButton: 'btn btn-danger me-2',
                        cancelButton: 'btn btn-label-secondary'
                    },
                    buttonsStyling: false,
                    reverseButtons: true
                }).then(function (result) {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            } else {
                if (confirm('Hapus ' + count + ' posisi terpilih?')) {
                    form.submit();
                }
            }
        });
    });
</script>
@endpush
