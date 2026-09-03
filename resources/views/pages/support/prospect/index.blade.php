@extends('layouts.sales.app')
@section('title', 'Marketing Prospects & Pipeline')
@section('content')
    @if (Auth::user()->role != 'Sales')
        {{-- ===== ADMIN / SUPPORT VIEW (MODERN & CLEAN REDESIGN) ===== --}}

        {{-- Hero Card Banner --}}
        <div class="card border-0 shadow-sm mb-4 prospect-hero-card">
            <div class="card-body p-4">
                <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="badge bg-label-primary px-3 py-1 rounded-pill fw-semibold">
                                <i class="mdi mdi-shield-crown-outline me-1"></i> Admin & Support Overview
                            </span>
                            <span class="text-muted small">&bull;</span>
                            <span class="text-muted small">Marketing Leads & Pipeline Allocation</span>
                        </div>
                        <h3 class="fw-bold text-heading mb-1">Prospect & Lead Pipeline</h3>
                        <p class="text-muted mb-0">
                            Monitoring seluruh leads dari tim marketing, distribusi penugasan sales, dan tracking pipeline penawaran (quotation).
                        </p>
                    </div>

                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <span class="badge bg-label-primary fs-6 px-3 py-2 rounded-pill">
                            <i class="mdi mdi-account-group-outline me-1"></i> {{ count($salesList ?? []) }} Sales Team
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Executive KPI Summary Cards --}}
        <div class="row g-3 mb-4">
            {{-- Quotation Pipeline --}}
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card h-100 border-0 shadow-sm prospect-kpi-card border-start-primary">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div class="d-flex align-items-start justify-content-between mb-2">
                            <div class="avatar avatar-md">
                                <div class="avatar-initial bg-label-primary rounded-3 shadow-xs">
                                    <i class="mdi mdi-file-document-outline fs-4"></i>
                                </div>
                            </div>
                            <span class="badge bg-label-primary rounded-pill small px-2 py-1">Pipeline</span>
                        </div>
                        <div>
                            <span class="text-muted small text-uppercase fw-semibold">Quotation Forecast</span>
                            <h4 class="fw-bold mb-1 text-primary">Rp {{ number_format(Auth::user()->role == 'Admin' ? $forecastAdmin : $forecast, 0, ',', '.') }}</h4>
                            <small class="text-muted">{{ (Auth::user()->role == 'Admin' ? $quotationAdmin : $quotation)->whereIn('status', ['20', '30', '40', '60', '80'])->count() + (Auth::user()->role == 'Admin' ? $sqDocForecastAdmin : $sqDocForecast) }} Dokumen Penawaran</small>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Hot Prospects --}}
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card h-100 border-0 shadow-sm prospect-kpi-card border-start-warning">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div class="d-flex align-items-start justify-content-between mb-2">
                            <div class="avatar avatar-md">
                                <div class="avatar-initial bg-label-warning rounded-3 shadow-xs">
                                    <i class="mdi mdi-fire fs-4"></i>
                                </div>
                            </div>
                            <span class="badge bg-label-warning rounded-pill small px-2 py-1">Hot 80%</span>
                        </div>
                        <div>
                            <span class="text-muted small text-uppercase fw-semibold">Hot Prospects</span>
                            <h4 class="fw-bold mb-1 text-warning">Rp {{ number_format(Auth::user()->role == 'Admin' ? $prospectAdmin : $prospect, 0, ',', '.') }}</h4>
                            <small class="text-muted">{{ (Auth::user()->role == 'Admin' ? $quotationAdmin : $quotation)->where('status', '80')->count() + (Auth::user()->role == 'Admin' ? $sqDocProspectAdmin : $sqDocProspect) }} Prospek Probabilitas Tinggi</small>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Purchase Orders (PO) --}}
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card h-100 border-0 shadow-sm prospect-kpi-card border-start-success">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div class="d-flex align-items-start justify-content-between mb-2">
                            <div class="avatar avatar-md">
                                <div class="avatar-initial bg-label-success rounded-3 shadow-xs">
                                    <i class="mdi mdi-cart-check fs-4"></i>
                                </div>
                            </div>
                            <span class="badge bg-label-success rounded-pill small px-2 py-1">Closed Win</span>
                        </div>
                        <div>
                            <span class="text-muted small text-uppercase fw-semibold">Purchase Order (PO)</span>
                            <h4 class="fw-bold mb-1 text-success">Rp {{ number_format(Auth::user()->role == 'Admin' ? $poAdmin : $po, 0, ',', '.') }}</h4>
                            <small class="text-muted">{{ (Auth::user()->role == 'Admin' ? $quotationAdmin : $quotation)->where('status', '100')->count() + (Auth::user()->role == 'Admin' ? $sqDocPoAdmin : $sqDocPo) }} Transaksi Sukses PO</small>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Loss Orders --}}
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card h-100 border-0 shadow-sm prospect-kpi-card border-start-danger">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div class="d-flex align-items-start justify-content-between mb-2">
                            <div class="avatar avatar-md">
                                <div class="avatar-initial bg-label-danger rounded-3 shadow-xs">
                                    <i class="mdi mdi-close-circle-outline fs-4"></i>
                                </div>
                            </div>
                            <span class="badge bg-label-danger rounded-pill small px-2 py-1">Loss</span>
                        </div>
                        <div>
                            <span class="text-muted small text-uppercase fw-semibold">Loss Orders</span>
                            <h4 class="fw-bold mb-1 text-danger">Rp {{ number_format(Auth::user()->role == 'Admin' ? $lossAdmin : $loss, 0, ',', '.') }}</h4>
                            <small class="text-muted">{{ (Auth::user()->role == 'Admin' ? $quotationAdmin : $quotation)->where('status', '0')->count() + (Auth::user()->role == 'Admin' ? $sqDocLossAdmin : $sqDocLoss) }} Penawaran Tidak Deal</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Monthly Leads Distribution Section (Sales Workload) --}}
        <div class="card mb-4 shadow-sm border-0">
            <div class="card-body p-4">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-4 pb-2 border-bottom">
                    <div>
                        <h6 class="fw-bold mb-1 text-heading d-flex align-items-center">
                            <i class="mdi mdi-account-switch-outline me-2 text-primary fs-5"></i> Distribusi Beban Leads Sales (Workload)
                        </h6>
                        <small class="text-muted">Akumulasi leads masuk bulan berjalan & per minggu. Klik kartu sales untuk melihat rincian.</small>
                    </div>
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary btn-sm dropdown-toggle rounded-pill shadow-xs" type="button"
                                data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="mdi mdi-calendar-week-outline me-1"></i> Week {{ $selectedWeekNum }}
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                            @foreach ($availableWeeks as $week)
                                <li>
                                    <a class="dropdown-item {{ $week['week'] == $selectedWeekNum ? 'active' : '' }}"
                                       href="{{ route('prospect.index', ['week' => $week['week']]) }}">
                                        {{ $week['label'] }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                <div class="row g-3 row-cols-1 row-cols-sm-2 row-cols-lg-5">
                    @foreach ($salesLeads as $sales)
                        @php
                            $count = $sales->monthly_leads;
                            $weekCount = $sales->weekly_leads;

                            if ($count <= 20) {
                                $color = 'success';
                            } elseif ($count <= 40) {
                                $color = 'warning';
                            } else {
                                $color = 'danger';
                            }
                            $workloadLabel = $count <= 20 ? 'Aman' : ($count <= 40 ? 'Waspada' : 'Tinggi');
                        @endphp

                        <div class="col">
                            <div class="p-3 rounded-3 border h-100 transition-hover sales-lead-card position-relative bg-white shadow-xs"
                                data-sales-id="{{ $sales->id }}" data-sales-name="{{ $sales->name }}"
                                style="cursor: pointer; border-left: 4px solid var(--bs-{{ $color }}) !important;">

                                <span class="badge rounded-pill bg-label-{{ $color }} position-absolute top-0 end-0 m-2"
                                    style="font-size:0.68rem;">
                                    {{ $workloadLabel }}
                                </span>

                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-md me-3 flex-shrink-0" data-bs-toggle="tooltip" data-bs-placement="top"
                                        title="{{ $sales->name }}">
                                        @if ($sales->image)
                                            <img src="{{ url('') . '/' . $sales->image }}" class="rounded-circle shadow-xs"
                                                width="42" height="42" style="object-fit:cover;"
                                                onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                            <div class="rounded-circle bg-label-primary align-items-center justify-content-center"
                                                style="width:42px;height:42px;display:none;">
                                                <span class="fw-bold text-primary">
                                                    {{ strtoupper(substr($sales->name, 0, 1)) }}
                                                </span>
                                            </div>
                                        @else
                                            <div class="rounded-circle bg-label-primary d-flex align-items-center justify-content-center shadow-xs"
                                                style="width:42px;height:42px;">
                                                <span class="fw-bold text-primary">
                                                    {{ strtoupper(substr($sales->name, 0, 1)) }}
                                                </span>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="overflow-hidden">
                                        <p class="mb-1 text-dark fw-semibold small text-truncate">
                                            {{ $sales->name }}
                                        </p>
                                        <h4 class="mb-0 fw-bold text-{{ $color }} fs-5">
                                            {{ $count }}
                                            <span class="fs-6 fw-normal text-muted" style="font-size: 0.75rem !important;">Leads</span>
                                        </h4>
                                        <small class="text-muted" style="font-size:0.75rem;">
                                            Wk {{ $selectedWeekNum }}: {{ $weekCount }} leads
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Sales Leads Modal --}}
        <div class="modal animate__animated animate__fadeIn" id="salesLeadsModal" tabindex="-1" style="display: none;"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-xl" role="document" style="max-width: 90%;">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header border-bottom py-3">
                        <h5 class="modal-title fw-bold text-dark" id="salesLeadsModalTitle">Prospect List</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Company</th>
                                        <th>Category</th>
                                        <th>Kebutuhan</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                        <th>Value</th>
                                    </tr>
                                </thead>
                                <tbody id="salesLeadsModalBody">
                                    <tr>
                                        <td colspan="6" class="text-center py-4">Memuat data prospek...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filter Toolbar & Admin DataTable --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent border-bottom py-3">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                    <div class="d-flex align-items-center gap-2">
                        <div class="avatar avatar-sm bg-label-primary rounded p-1">
                            <i class="mdi mdi-table-account fs-4"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-0 text-heading">Data Master Prospek Marketing</h6>
                            <small class="text-muted">Daftar seluruh prospek dan alokasi tim sales</small>
                        </div>
                    </div>

                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        @if (Auth::user()->role == 'Admin')
                            <div class="d-flex align-items-center gap-1">
                                <label class="form-label mb-0 text-muted small text-nowrap"><i class="mdi mdi-filter-variant me-1"></i>Sales:</label>
                                <select class="form-select form-select-sm" id="prospect-sales-filter" style="min-width: 180px;">
                                    <option value="">Semua Sales</option>
                                    @foreach ($salesList as $s)
                                        <option value="{{ $s->id }}">{{ $s->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                        <div class="d-flex align-items-center gap-1">
                            <label class="form-label mb-0 text-muted small text-nowrap"><i class="mdi mdi-calendar-outline me-1"></i>Tahun:</label>
                            <select class="form-select form-select-sm" id="prospect-year-filter" style="min-width: 120px;">
                                <option value="">Semua Tahun</option>
                                @foreach ($availableYears as $year)
                                    <option value="{{ $year }}" {{ $year == now()->year ? 'selected' : '' }}>{{ $year }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <script>
                window.prospectSalesFilter = '';
                window.prospectYearFilter = '{{ now()->year }}';
            </script>

            <div class="card-datatable table-responsive pt-0">
                <table class="datatable{{ Auth::user()->role == 'Admin' ? '-prospect-admin' : '-prospect' }} table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="min-width: 220px;">Company & PIC</th>
                            <th style="min-width: 220px;">Category & Kebutuhan</th>
                            <th style="min-width: 140px;">Date & Source</th>
                            <th style="min-width: 140px;">Marketing Support</th>
                            <th style="min-width: 140px;">Assigned Sales</th>
                            <th style="min-width: 160px;">Quotation Status</th>
                            <th class="text-center" style="min-width: 110px;">Status & Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    @elseif (Auth::user()->role == 'Sales')
        {{-- ===== SALES ROLE VIEW (MODERN & CLEAN REDESIGN) ===== --}}

        {{-- Hero Card Banner --}}
        <div class="card border-0 shadow-sm mb-4 prospect-hero-card">
            <div class="card-body p-4">
                <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="badge bg-label-primary px-3 py-1 rounded-pill fw-semibold">
                                <i class="mdi mdi-account-tie-outline me-1"></i> Sales Dashboard
                            </span>
                            <span class="text-muted small">&bull;</span>
                            <span class="text-muted small">Marketing Prospect Allocation</span>
                        </div>
                        <h3 class="fw-bold text-heading mb-1">My Prospect Pipeline</h3>
                        <p class="text-muted mb-0">
                            Kelola prospek dari tim marketing, proses penawaran (quotation), dan tindak lanjuti prospek calon klien.
                        </p>
                    </div>

                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <span class="badge bg-label-primary fs-6 px-3 py-2 rounded-pill">
                            <i class="mdi mdi-inbox-arrow-down me-1"></i> {{ $salesNewProspectCount ?? 0 }} New
                        </span>
                        <span class="badge bg-label-warning fs-6 px-3 py-2 rounded-pill">
                            <i class="mdi mdi-progress-clock me-1"></i> {{ $salesFuProspectCount ?? 0 }} Follow-Up
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Executive KPI Summary Cards for Sales --}}
        <div class="row g-3 mb-4">
            {{-- Total Assigned --}}
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card h-100 border-0 shadow-sm prospect-kpi-card border-start-primary">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div class="d-flex align-items-start justify-content-between mb-2">
                            <div class="avatar avatar-md">
                                <div class="avatar-initial bg-label-primary rounded-3 shadow-xs">
                                    <i class="mdi mdi-inbox-multiple-outline fs-4"></i>
                                </div>
                            </div>
                            <span class="badge bg-label-primary rounded-pill small px-2 py-1">Assigned</span>
                        </div>
                        <div>
                            <span class="text-muted small text-uppercase fw-semibold">Total Prospects</span>
                            <h4 class="fw-bold mb-1 text-primary">{{ $salesTotalAssigned ?? 0 }}</h4>
                            <small class="text-muted">{{ $salesNewProspectCount ?? 0 }} Baru &bull; {{ $salesFuProspectCount ?? 0 }} Follow-Up</small>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Quotation Pipeline --}}
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card h-100 border-0 shadow-sm prospect-kpi-card border-start-info">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div class="d-flex align-items-start justify-content-between mb-2">
                            <div class="avatar avatar-md">
                                <div class="avatar-initial bg-label-info rounded-3 shadow-xs">
                                    <i class="mdi mdi-file-document-outline fs-4"></i>
                                </div>
                            </div>
                            <span class="badge bg-label-info rounded-pill small px-2 py-1">Pipeline</span>
                        </div>
                        <div>
                            <span class="text-muted small text-uppercase fw-semibold">Quotation Value</span>
                            <h4 class="fw-bold mb-1 text-info">Rp {{ number_format($salesQuoteForecast ?? 0, 0, ',', '.') }}</h4>
                            <small class="text-muted">{{ $salesQuoteForecastCount ?? 0 }} Transaksi Quote Aktif</small>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Hot Prospects --}}
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card h-100 border-0 shadow-sm prospect-kpi-card border-start-warning">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div class="d-flex align-items-start justify-content-between mb-2">
                            <div class="avatar avatar-md">
                                <div class="avatar-initial bg-label-warning rounded-3 shadow-xs">
                                    <i class="mdi mdi-fire fs-4"></i>
                                </div>
                            </div>
                            <span class="badge bg-label-warning rounded-pill small px-2 py-1">Hot 80%</span>
                        </div>
                        <div>
                            <span class="text-muted small text-uppercase fw-semibold">Hot Prospects</span>
                            <h4 class="fw-bold mb-1 text-warning">Rp {{ number_format($salesHotProspect ?? 0, 0, ',', '.') }}</h4>
                            <small class="text-muted">{{ $salesHotProspectCount ?? 0 }} Hot Prospect (80%)</small>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Purchase Orders --}}
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card h-100 border-0 shadow-sm prospect-kpi-card border-start-success">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div class="d-flex align-items-start justify-content-between mb-2">
                            <div class="avatar avatar-md">
                                <div class="avatar-initial bg-label-success rounded-3 shadow-xs">
                                    <i class="mdi mdi-cart-check fs-4"></i>
                                </div>
                            </div>
                            <span class="badge bg-label-success rounded-pill small px-2 py-1">Closed Win</span>
                        </div>
                        <div>
                            <span class="text-muted small text-uppercase fw-semibold">Done PO</span>
                            <h4 class="fw-bold mb-1 text-success">Rp {{ number_format($salesPo ?? 0, 0, ',', '.') }}</h4>
                            <small class="text-muted">{{ $salesPoCount ?? 0 }} Transaksi Closed PO</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Table 1: New Assigned Prospects --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-datatable table-responsive pt-0">
                <table class="datatable-prospect-sales table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 25px;"></th>
                            <th style="display:none;">ID</th>
                            <th style="min-width: 220px;">Company & PIC</th>
                            <th style="min-width: 220px;">Category & Kebutuhan</th>
                            <th style="min-width: 140px;">Date & Source</th>
                            <th style="min-width: 140px;">Marketing Support</th>
                            <th style="min-width: 150px;">Quotation Status</th>
                            <th class="text-center" style="min-width: 120px;">Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>

        {{-- Table 2: Follow Up Prospects In Progress --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-datatable table-responsive pt-0">
                <table class="datatable-prospect-fu-sales table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 25px;"></th>
                            <th style="display:none;">ID</th>
                            <th style="min-width: 220px;">Company & PIC</th>
                            <th style="min-width: 220px;">Category & Kebutuhan</th>
                            <th style="min-width: 140px;">Date & Source</th>
                            <th style="min-width: 140px;">Marketing Support</th>
                            <th style="min-width: 150px;">Quotation Status</th>
                            <th class="text-center" style="min-width: 120px;">Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>

        {{-- Confirmation Modals for Prospects --}}
        @foreach ($prospects as $prospect)
            @include('components.modal.prospect.confirm')
        @endforeach
    @endif

    @include('components.modal.client.support.form')
@endsection

@push('after-style')
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/select2/select2.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/animate-css/animate.css">
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-rowgroup-bs5/rowgroup.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/formvalidation/dist/css/formValidation.min.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.css" />
    <style>
        .prospect-hero-card {
            background: linear-gradient(135deg, rgba(var(--bs-primary-rgb), 0.04) 0%, rgba(var(--bs-primary-rgb), 0.01) 100%);
            border: 1px solid rgba(var(--bs-primary-rgb), 0.12) !important;
            border-radius: 14px;
        }

        .prospect-kpi-card {
            border-radius: 12px;
            transition: transform 0.2s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.2s ease;
            border: 1px solid rgba(0, 0, 0, 0.06);
        }
        .prospect-kpi-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08) !important;
        }
        .border-start-primary   { border-left: 4px solid var(--bs-primary) !important; }
        .border-start-success   { border-left: 4px solid var(--bs-success) !important; }
        .border-start-warning   { border-left: 4px solid var(--bs-warning) !important; }
        .border-start-danger    { border-left: 4px solid var(--bs-danger) !important; }
        .border-start-info      { border-left: 4px solid var(--bs-info) !important; }

        .text-primary-hover:hover {
            color: var(--bs-primary) !important;
            text-decoration: underline;
        }

        .transition-hover {
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }
        .transition-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 0.25rem 0.75rem rgba(0, 0, 0, 0.08);
        }

        .kpi-tile {
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }
        .kpi-tile:hover {
            transform: translateY(-2px);
        }

        /* Dark Mode */
        .dark-style .prospect-hero-card {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.04) 0%, rgba(255, 255, 255, 0.01) 100%);
            border: 1px solid rgba(255, 255, 255, 0.08) !important;
        }
        .dark-style .prospect-kpi-card {
            border-color: rgba(255, 255, 255, 0.08);
        }
    </style>
@endpush

@push('after-script')
    <script src="{{ asset('assets') }}/vendor/libs/select2/select2.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/moment/moment.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/flatpickr/flatpickr.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/formvalidation/dist/js/FormValidation.min.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.js"></script>
@endpush

@push('page-script')
    <script src="{{ asset('assets') }}/js/extended-ui-sweetalert2.js"></script>
    <script src="{{ asset('assets') }}/js/tables-datatables-basic.js"></script>
    <script src="{{ asset('assets') }}/includes/table-prospect-support.js"></script>
    <script src="{{ asset('assets') }}/includes/table-prospect-support-admin.js"></script>
    <script src="{{ asset('assets') }}/includes/table-prospect-support-sales.js"></script>
    <script src="{{ asset('assets') }}/includes/table-prospect-support-fu-sales.js"></script>
@endpush

@push('script')
    <script>
        $(document).ready(function() {
            $('[data-bs-toggle="tooltip"]').tooltip();

            $('#selectArea').select2({
                placeholder: 'Area',
                dropdownParent: $('#createProspect'),
                width: '100%',
                minimumInputLength: 2,
                language: {
                    inputTooShort: function() { return 'Ketik minimal 2 karakter...'; },
                    searching: function() { return 'Mencari...'; },
                    noResults: function() { return 'Kota/Kabupaten tidak ditemukan'; }
                },
                ajax: {
                    url: '{{ route("kota.search") }}',
                    dataType: 'json',
                    delay: 300,
                    data: function(params) { return { q: params.term }; },
                    processResults: function(data) { return { results: data }; },
                    cache: true
                }
            });

            $('#selectArea').on('select2:open', function() {
                $('.select2-search__field').attr('placeholder', 'Masukkan Kota/Kabupaten');
            });

            function toggleDomainField() {
                var isWebsite = $('#selectSource').val() === 'Website';
                $('#domainWrapper').toggle(isWebsite);
                if (!isWebsite) {
                    $('#domainInput').val('');
                }
            }
            toggleDomainField();
            $('#selectSource').on('change', toggleDomainField);
        });

        $('#prospect-sales-filter').on('change', function() {
            window.prospectSalesFilter = $(this).val();
            $('.datatable-prospect-admin').DataTable().ajax.reload();
        });

        $('#prospect-year-filter').on('change', function() {
            window.prospectYearFilter = $(this).val();
            if ($.fn.DataTable.isDataTable('.datatable-prospect-admin')) {
                $('.datatable-prospect-admin').DataTable().ajax.reload();
            }
            if ($.fn.DataTable.isDataTable('.datatable-prospect')) {
                $('.datatable-prospect').DataTable().ajax.reload();
            }
        });

        $(document).on('click', '.sales-lead-card', function() {
            var salesId = $(this).data('sales-id');
            var salesName = $(this).data('sales-name');
            var modalEl = document.getElementById('salesLeadsModal');
            var modal = bootstrap.Modal.getOrCreateInstance(modalEl);
            var statusLabel = {
                20: { title: 'Send WA / Email', class: 'bg-label-secondary' },
                30: { title: 'Inquiry Accepted', class: 'bg-label-dark' },
                40: { title: 'Progress Follow Up', class: 'bg-label-info' },
                60: { title: 'Negotiation / Revisi', class: 'bg-label-primary' },
                80: { title: 'Hot Prospect', class: 'bg-label-warning' },
                100: { title: 'Done PO', class: 'bg-label-success' },
                0: { title: 'Loss', class: 'bg-label-danger' },
            };

            $('#salesLeadsModalTitle').text('Prospect Bulan Ini - ' + salesName);
            $('#salesLeadsModalBody').html('<tr><td colspan="6" class="text-center">Loading...</td></tr>');
            modal.show();

            $.ajax({
                url: '{{ url('prospect/monthly-leads') }}/' + salesId,
                type: 'GET',
                success: function(response) {
                    var rows = '';
                    if (response.data && response.data.length > 0) {
                        $.each(response.data, function(i, item) {
                            var status = statusLabel[item.status];
                            var statusHtml = status ?
                                '<span class="badge rounded-pill ' + status.class + '">' + status.title + '</span>' :
                                '<span class="badge rounded-pill bg-label-secondary">New / Unquoted</span>';
                            var value = item.nett ? '<span class="fw-bold text-success">Rp ' + Number(item.nett).toLocaleString('id-ID') + '</span>' : '<span class="text-muted">—</span>';
                            var dateFmt = item.date ? moment(item.date).format('DD MMM YYYY') : '—';
                            rows += '<tr>' +
                                '<td><a href="/prospect/' + item.id + '" class="fw-bold text-primary">' + (item.company ?? '—') + '</a></td>' +
                                '<td><span class="badge bg-label-primary rounded-pill small">' + (item.category ?? 'General') + '</span></td>' +
                                '<td class="small text-truncate" style="max-width:200px;">' + (item.kebutuhan ?? '—') + '</td>' +
                                '<td class="small">' + dateFmt + '</td>' +
                                '<td>' + statusHtml + '</td>' +
                                '<td>' + value + '</td>' +
                                '</tr>';
                        });
                    } else {
                        rows = '<tr><td colspan="6" class="text-center py-4 text-muted">Belum ada prospek untuk sales ini pada bulan berjalan.</td></tr>';
                    }
                    $('#salesLeadsModalBody').html(rows);
                },
                error: function() {
                    $('#salesLeadsModalBody').html('<tr><td colspan="6" class="text-center">Gagal memuat data</td></tr>');
                }
            });
        });

        $(document).on('click', '#withQuote', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: "Create Smart Quote?",
                text: "Konversi prospek ini menjadi Smart Quotation resmi?",
                icon: "question",
                showCancelButton: true,
                confirmButtonText: "Ya, Buat Smart Quote!",
                cancelButtonText: "Batal",
                customClass: {
                    confirmButton: "btn btn-primary me-3 waves-effect waves-light",
                    cancelButton: "btn btn-label-secondary waves-effect",
                },
                buttonsStyling: false,
            }).then(function(result) {
                if (result.value) {
                    $.ajax({
                        url: '{{ url('prospect') }}/' + 'with_quotation/' + id,
                        type: 'POST',
                        data: {
                            '_method': 'POST',
                            '_token': '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response == 1) {
                                Swal.fire({
                                    icon: "success",
                                    title: "Berhasil!",
                                    text: "Prospek berhasil dialihkan ke pembuatan Smart Quote.",
                                    customClass: {
                                        confirmButton: "btn btn-success waves-effect",
                                    },
                                    showConfirmButton: false,
                                    timer: 1000
                                });
                                window.setTimeout(function() {
                                    window.location.href = '/smart-quote/create?prospect_id=' + id;
                                }, 1000);
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: 'Gagal membuat penawaran!'
                                });
                            }
                        }
                    });
                }
            });
        });

        $(document).on('click', '#withoutQuote', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: "Without Quotation?",
                text: "Lanjutkan prospek tanpa membuat quotation?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Ya, Lanjutkan!",
                cancelButtonText: "Batal",
                customClass: {
                    confirmButton: "btn btn-primary me-3 waves-effect waves-light",
                    cancelButton: "btn btn-label-secondary waves-effect",
                },
                buttonsStyling: false,
            }).then(function(result) {
                if (result.value) {
                    $.ajax({
                        url: '{{ url('prospect') }}/' + 'without_quotation/' + id,
                        type: 'POST',
                        data: {
                            '_method': 'POST',
                            '_token': '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response == 1) {
                                Swal.fire({
                                    icon: "success",
                                    title: "Berhasil!",
                                    text: "Status prospek berhasil diperbarui.",
                                    customClass: {
                                        confirmButton: "btn btn-success waves-effect",
                                    },
                                });
                                window.setTimeout(function() {
                                    window.location.href = '/leads/detail/' + id;
                                }, 1200);
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: 'Gagal memperbarui status prospek!'
                                });
                            }
                        }
                    });
                }
            });
        });
    </script>
@endpush
