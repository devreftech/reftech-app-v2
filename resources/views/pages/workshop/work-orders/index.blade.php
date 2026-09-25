@extends('layouts.sales.app')
@section('title', 'Work Order (Pergantian Spare Part Mesin) - Reftech ERP')

@push('after-style')
    <style>
        /* Modern Typography & Polish */
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif !important;
        }

        /* Hero Banner Card */
        .wo-hero-card {
            background: linear-gradient(135deg, #1e2640 0%, #2a3558 100%);
            border-radius: 16px;
            color: #ffffff;
            box-shadow: 0 10px 30px rgba(30, 38, 64, 0.15);
            position: relative;
            overflow: hidden;
        }
        .wo-hero-card::after {
            content: '';
            position: absolute;
            right: -20px;
            bottom: -30px;
            width: 260px;
            height: 260px;
            background: radial-gradient(circle, rgba(105, 108, 255, 0.25) 0%, rgba(255,255,255,0) 70%);
            border-radius: 50%;
            pointer-events: none;
        }

        /* KPI Metric Cards */
        .kpi-card {
            border-radius: 14px;
            background: #ffffff;
            border: 1px solid rgba(0, 0, 0, 0.05);
            transition: all 0.25s cubic-bezier(0.165, 0.84, 0.44, 1);
            position: relative;
            overflow: hidden;
        }
        .kpi-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 28px rgba(0, 0, 0, 0.08);
            border-color: rgba(105, 108, 255, 0.3);
        }
        .kpi-accent-bar {
            height: 4px;
            width: 100%;
            position: absolute;
            top: 0;
            left: 0;
        }

        /* Nav Tabs Pill Redesign (Sama Persis seperti Purchase Request) */
        .pr-nav-pills {
            background: #f4f5f9;
            padding: 5px;
            border-radius: 12px;
            display: inline-flex;
            gap: 4px;
            border: 1px solid rgba(0, 0, 0, 0.05);
            flex-wrap: wrap;
        }
        .pr-nav-pills .nav-link {
            border-radius: 8px !important;
            padding: 8px 16px;
            color: #566a7f;
            font-weight: 600;
            font-size: 0.84rem;
            transition: all 0.2s ease;
            border: none !important;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            text-decoration: none;
        }
        .pr-nav-pills .nav-link:hover {
            color: #696cff;
            background: rgba(105, 108, 255, 0.08);
        }
        .pr-nav-pills .nav-link.active {
            color: #ffffff !important;
            background: #696cff !important;
            box-shadow: 0 4px 12px rgba(105, 108, 255, 0.35);
        }
        .pr-nav-pills .badge {
            font-size: 0.72rem !important;
            padding: 3px 8px !important;
            font-weight: 700 !important;
            line-height: 1.2 !important;
        }

        /* Table Styling */
        .table thead th {
            font-size: 0.75rem !important;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #566a7f;
            background-color: #f8f9fa;
            border-bottom: 2px solid #e7eaf0 !important;
            vertical-align: middle;
        }
        .table td {
            font-size: 0.85rem;
            vertical-align: middle;
        }

        /* High Contrast Badge Overrides */
        .badge.bg-label-warning {
            background-color: #fff2d6 !important;
            color: #8f5200 !important;
            font-weight: 600 !important;
            border: 1px solid rgba(255, 171, 0, 0.3) !important;
        }
        .badge.bg-label-info {
            background-color: #e1f7fc !important;
            color: #036b82 !important;
            font-weight: 600 !important;
            border: 1px solid rgba(38, 198, 249, 0.3) !important;
        }
        .badge.bg-label-success {
            background-color: #e8fadf !important;
            color: #2e6d0e !important;
            font-weight: 600 !important;
            border: 1px solid rgba(113, 221, 55, 0.3) !important;
        }
        .badge.bg-label-primary {
            background-color: #eae8fd !important;
            color: #4b4ec7 !important;
            font-weight: 600 !important;
            border: 1px solid rgba(105, 108, 255, 0.3) !important;
        }
        .badge.bg-label-secondary {
            background-color: #ebeef0 !important;
            color: #3b4d61 !important;
            font-weight: 600 !important;
            border: 1px solid rgba(133, 146, 163, 0.3) !important;
        }
        .badge.bg-label-danger {
            background-color: #ffe5e5 !important;
            color: #b31d1d !important;
            font-weight: 600 !important;
            border: 1px solid rgba(255, 77, 73, 0.3) !important;
        }
    </style>
@endpush

@section('content')
{{-- FULLWIDTH CONTAINER --}}
@php
    $userRole = Auth::user()?->role;
    $canSeePrice = in_array($userRole, ['Developer', 'Admin', 'Super Admin', 'Accounting', 'Finance', 'Finance Manager']);
@endphp
<div class="container-fluid px-3 px-md-4 py-3 flex-grow-1">
    {{-- Hero Banner Card --}}
    <div class="card border-0 shadow-sm wo-hero-card mb-4">
        <div class="card-body p-4 p-md-4 position-relative" style="z-index: 1;">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-1 text-white-50 small">
                            <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-white-50">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('fixed.index', ['type' => 'Mesin']) }}" class="text-white-50">Fixed Asset</a></li>
                            <li class="breadcrumb-item active text-white" aria-current="page">Work Order</li>
                        </ol>
                    </nav>
                    <h3 class="fw-bolder mb-1 text-white d-flex align-items-center gap-2">
                        <i class="mdi mdi-wrench-cog text-primary-light"></i> Work Order Spare Part Mesin
                    </h3>
                    <p class="text-white-50 mb-0 small">
                        Modul terpadu pengajuan, verifikasi gudang, pengadaan (PR), otorisasi akuntansi, dan pengeluaran suku cadang pemeliharaan mesin unit internal.
                    </p>
                </div>

                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <a href="{{ route('purchase-request.index') }}" class="btn btn-outline-light btn-sm shadow-sm">
                        <i class="mdi mdi-file-document-outline me-1"></i> Monitoring PR
                    </a>
                    @if (in_array(Auth::user()?->role, ['ServiceM', 'Support', 'Technician', 'Admin', 'Super Admin', 'Developer']))
                        <a href="{{ route('work-orders.create') }}" class="btn btn-primary btn-sm shadow-sm px-3">
                            <i class="mdi mdi-plus-thick me-1"></i> Buat Work Order Baru
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- 5 KPI Metric Cards --}}
    <div class="row g-3 mb-4">
        {{-- Total WO --}}
        <div class="col-sm-6 col-xl">
            <a href="{{ route('work-orders.index', ['status' => 'all']) }}" class="text-decoration-none">
                <div class="card kpi-card h-100 shadow-sm">
                    <div class="kpi-accent-bar bg-primary"></div>
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-muted fw-semibold small text-uppercase" style="font-size: 0.72rem;">Total Semua WO</span>
                            <div class="avatar avatar-sm bg-label-primary rounded">
                                <i class="mdi mdi-clipboard-list-outline fs-5 text-primary"></i>
                            </div>
                        </div>
                        <h4 class="fw-bolder text-primary mb-0">{{ $counts['all'] }}</h4>
                        <small class="text-muted" style="font-size: 11px;">Seluruh pengajuan tercatat</small>
                    </div>
                </div>
            </a>
        </div>

        {{-- Butuh Cek Gudang --}}
        <div class="col-sm-6 col-xl">
            <a href="{{ route('work-orders.index', ['status' => 'pending_warehouse']) }}" class="text-decoration-none">
                <div class="card kpi-card h-100 shadow-sm">
                    <div class="kpi-accent-bar bg-warning"></div>
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-muted fw-semibold small text-uppercase" style="font-size: 0.72rem;">Verifikasi Gudang</span>
                            <div class="avatar avatar-sm bg-label-warning rounded">
                                <i class="mdi mdi-warehouse fs-5 text-warning"></i>
                            </div>
                        </div>
                        <h4 class="fw-bolder text-warning mb-0">{{ $counts['pending_warehouse'] }}</h4>
                        <small class="text-muted" style="font-size: 11px;">Perlu pengecekan fisik</small>
                    </div>
                </div>
            </a>
        </div>

        {{-- Menunggu PR --}}
        <div class="col-sm-6 col-xl">
            <a href="{{ route('work-orders.index', ['status' => 'waiting_pr']) }}" class="text-decoration-none">
                <div class="card kpi-card h-100 shadow-sm">
                    <div class="kpi-accent-bar bg-info"></div>
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-muted fw-semibold small text-uppercase" style="font-size: 0.72rem;">Menunggu PR</span>
                            <div class="avatar avatar-sm bg-label-info rounded">
                                <i class="mdi mdi-cart-clock fs-5 text-info"></i>
                            </div>
                        </div>
                        <h4 class="fw-bolder text-info mb-0">{{ $counts['waiting_pr'] }}</h4>
                        <small class="text-muted" style="font-size: 11px;">Stok kosong / proses beli</small>
                    </div>
                </div>
            </a>
        </div>

        {{-- Persetujuan Accounting --}}
        <div class="col-sm-6 col-xl">
            <a href="{{ route('work-orders.index', ['status' => 'pending_accounting']) }}" class="text-decoration-none">
                <div class="card kpi-card h-100 shadow-sm">
                    <div class="kpi-accent-bar bg-primary"></div>
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-muted fw-semibold small text-uppercase" style="font-size: 0.72rem;">Acc Accounting</span>
                            <div class="avatar avatar-sm bg-label-primary rounded">
                                <i class="mdi mdi-shield-account-outline fs-5 text-primary"></i>
                            </div>
                        </div>
                        <h4 class="fw-bolder text-primary mb-0">{{ $counts['pending_accounting'] }}</h4>
                        <small class="text-muted" style="font-size: 11px;">Otorisasi biaya</small>
                    </div>
                </div>
            </a>
        </div>

        {{-- Siap Keluar / Approved --}}
        <div class="col-sm-6 col-xl">
            <a href="{{ route('work-orders.index', ['status' => 'approved']) }}" class="text-decoration-none">
                <div class="card kpi-card h-100 shadow-sm">
                    <div class="kpi-accent-bar bg-success"></div>
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-muted fw-semibold small text-uppercase" style="font-size: 0.72rem;">Siap Dikeluarkan</span>
                            <div class="avatar avatar-sm bg-label-success rounded">
                                <i class="mdi mdi-check-decagram-outline fs-5 text-success"></i>
                            </div>
                        </div>
                        <h4 class="fw-bolder text-success mb-0">{{ $counts['approved'] }}</h4>
                        <small class="text-muted" style="font-size: 11px;">Disetujui siap serah terima</small>
                    </div>
                </div>
            </a>
        </div>
    </div>

    {{-- Main Content Card (Desain Persis seperti Purchase Request) --}}
    <div class="card shadow-sm border-0 mb-4">
        {{-- Top Navigation Pill Tabs di dalam Card Header --}}
        <div class="card-header border-bottom py-3 px-4 bg-white d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div class="pr-nav-pills" id="workOrderTabs">
                <a class="nav-link {{ $status === 'all' ? 'active' : '' }}" href="{{ route('work-orders.index', ['status' => 'all']) }}">
                    <i class="mdi mdi-view-grid-outline"></i>
                    <span>Semua</span>
                    <span class="badge {{ $status === 'all' ? 'bg-white text-primary' : 'bg-label-secondary' }} rounded-pill font-11">{{ $counts['all'] }}</span>
                </a>

                <a class="nav-link {{ $status === 'pending_warehouse' ? 'active' : '' }}" href="{{ route('work-orders.index', ['status' => 'pending_warehouse']) }}">
                    <i class="mdi mdi-warehouse"></i>
                    <span>Verifikasi Gudang</span>
                    <span class="badge {{ $status === 'pending_warehouse' ? 'bg-white text-warning' : 'bg-warning text-dark' }} rounded-pill font-11" style="{{ @$counts['pending_warehouse'] >= 1 ? '' : 'display:none;' }}">{{ $counts['pending_warehouse'] }}</span>
                </a>

                <a class="nav-link {{ $status === 'waiting_pr' ? 'active' : '' }}" href="{{ route('work-orders.index', ['status' => 'waiting_pr']) }}">
                    <i class="mdi mdi-cart-clock"></i>
                    <span>Menunggu PR</span>
                    <span class="badge {{ $status === 'waiting_pr' ? 'bg-white text-info' : 'bg-info' }} rounded-pill font-11" style="{{ @$counts['waiting_pr'] >= 1 ? '' : 'display:none;' }}">{{ $counts['waiting_pr'] }}</span>
                </a>

                <a class="nav-link {{ $status === 'pending_accounting' ? 'active' : '' }}" href="{{ route('work-orders.index', ['status' => 'pending_accounting']) }}">
                    <i class="mdi mdi-shield-check-outline"></i>
                    <span>Acc Accounting</span>
                    <span class="badge {{ $status === 'pending_accounting' ? 'bg-white text-primary' : 'bg-primary' }} rounded-pill font-11" style="{{ @$counts['pending_accounting'] >= 1 ? '' : 'display:none;' }}">{{ $counts['pending_accounting'] }}</span>
                </a>

                <a class="nav-link {{ $status === 'approved' ? 'active' : '' }}" href="{{ route('work-orders.index', ['status' => 'approved']) }}">
                    <i class="mdi mdi-package-variant-closed-check"></i>
                    <span>Siap Keluar</span>
                    <span class="badge {{ $status === 'approved' ? 'bg-white text-success' : 'bg-success' }} rounded-pill font-11" style="{{ @$counts['approved'] >= 1 ? '' : 'display:none;' }}">{{ $counts['approved'] }}</span>
                </a>

                <a class="nav-link {{ $status === 'issued' ? 'active' : '' }}" href="{{ route('work-orders.index', ['status' => 'issued']) }}">
                    <i class="mdi mdi-check-all"></i>
                    <span>Good Receipt / Selesai</span>
                    <span class="badge {{ $status === 'issued' ? 'bg-white text-secondary' : 'bg-label-secondary' }} rounded-pill font-11" style="{{ @$counts['issued'] >= 1 ? '' : 'display:none;' }}">{{ $counts['issued'] }}</span>
                </a>
            </div>

            <div class="d-flex align-items-center gap-2">
                {{-- Search Bar --}}
                <form action="{{ route('work-orders.index') }}" method="GET" class="d-flex align-items-center">
                    <input type="hidden" name="status" value="{{ $status }}">
                    <div class="input-group input-group-merge input-group-sm" style="min-width: 250px;">
                        <span class="input-group-text bg-light border-end-0"><i class="mdi mdi-magnify text-muted"></i></span>
                        <input type="text" name="search" class="form-control bg-light border-start-0" placeholder="Cari No. WO, Mesin..." value="{{ request('search') }}">
                        @if (request('search'))
                            <a href="{{ route('work-orders.index', ['status' => $status]) }}" class="btn btn-light btn-sm border" title="Reset Search">
                                <i class="mdi mdi-close"></i>
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        {{-- Main Table Body --}}
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">No. Work Order</th>
                        <th>Mesin (Fixed Asset)</th>
                        <th>Tgl Pengajuan</th>
                        <th>Pemohon (ServiceM)</th>
                        <th class="text-center">Total Part</th>
                        @if ($canSeePrice)
                            <th class="text-end">Estimasi Biaya</th>
                        @endif
                        <th class="text-center">Status</th>
                        <th class="text-center pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($workOrders as $wo)
                        @php $badge = $wo->status_badge; @endphp
                        <tr>
                            <td class="ps-4">
                                <a href="{{ route('work-orders.show', $wo->id) }}" class="fw-bold text-primary font-monospace fs-6">
                                    {{ $wo->no_wo }}
                                </a>
                                @if ($wo->productOut)
                                    <div class="mt-0.5">
                                        <span class="badge bg-label-success" style="font-size: 10px;" title="Surat Jalan Terbit">
                                            <i class="mdi mdi-truck-fast-outline me-0.5"></i>{{ $wo->productOut->no_product_out }}
                                        </span>
                                    </div>
                                @endif
                                @if ($wo->accounting_treatment)
                                    <div class="mt-0.5">
                                        @if ($wo->accounting_treatment === 'capitalize')
                                            <span class="badge bg-label-success" style="font-size: 10px;">
                                                <i class="mdi mdi-cash-plus me-0.5"></i>Kapitalisasi
                                            </span>
                                        @else
                                            <span class="badge bg-label-info" style="font-size: 10px;">
                                                <i class="mdi mdi-calculator me-0.5"></i>Beban Operasional
                                            </span>
                                        @endif
                                    </div>
                                @endif
                            </td>
                            <td>
                                @if ($wo->fixedAsset)
                                    <div class="fw-semibold text-dark">{{ $wo->fixedAsset->code }}</div>
                                    <small class="text-muted d-block text-truncate" style="max-width: 220px;">
                                        {{ $wo->fixedAsset->unit->brand ?? ($wo->fixedAsset->desc ?: 'Mesin Kompresor') }}
                                        @if ($wo->fixedAsset->unit && $wo->fixedAsset->unit->model)
                                            ({{ $wo->fixedAsset->unit->model }})
                                        @endif
                                    </small>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <span class="fw-semibold">{{ $wo->date ? $wo->date->format('d M Y') : '-' }}</span>
                                @if ($wo->target_date)
                                    <small class="text-muted d-block" style="font-size: 11px;">Est. Pekerjaan: {{ $wo->target_date->format('d/m/Y') }}</small>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-1.5">
                                    <i class="mdi mdi-account-circle-outline text-secondary"></i>
                                    <span class="fw-semibold">{{ $wo->creator->name ?? 'ServiceM' }}</span>
                                </div>
                                @if ($wo->technician)
                                    <small class="text-muted d-block" style="font-size: 11px;">Teknisi: {{ $wo->technician->name }}</small>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge bg-label-secondary rounded-pill fw-bold">
                                    {{ $wo->items->count() }} Part
                                </span>
                            </td>
                            @if ($canSeePrice)
                                <td class="text-end fw-bold text-dark">
                                    Rp {{ number_format($wo->total_cost, 0, ',', '.') }}
                                </td>
                            @endif
                            <td class="text-center">
                                <span class="badge {{ $badge['class'] }} d-inline-flex align-items-center gap-1">
                                    <i class="mdi {{ $badge['icon'] }}" style="font-size: 13px;"></i>
                                    {{ $badge['text'] }}
                                </span>
                            </td>
                            <td class="text-center pe-4">
                                <div class="d-inline-flex align-items-center gap-1">
                                    @if ($wo->status === 'issued' && $wo->productOut)
                                        <a href="{{ route('work-orders.print-sj', $wo->id) }}" target="_blank" class="btn btn-sm btn-icon btn-label-primary rounded-pill shadow-xs" title="Cetak Surat Jalan (A4)">
                                            <i class="mdi mdi-printer-outline"></i>
                                        </a>
                                    @endif
                                    <a href="{{ route('work-orders.show', $wo->id) }}" class="btn btn-sm {{ $wo->status === 'approved' ? 'btn-success' : 'btn-label-primary' }} rounded-pill shadow-xs" title="Lihat Detail &amp; Aksi">
                                        @if ($wo->status === 'approved')
                                            <i class="mdi mdi-truck-fast-outline me-1"></i> Keluarkan Part
                                        @else
                                            Detail <i class="mdi mdi-arrow-right ms-1"></i>
                                        @endif
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $canSeePrice ? 8 : 7 }}" class="text-center py-5 text-muted">
                                <i class="mdi mdi-clipboard-text-search-outline fs-1 d-block mb-2 text-secondary"></i>
                                <strong>Tidak ada data Work Order ditemukan.</strong>
                                <p class="small text-muted mb-0">Silakan ubah filter tab atau buat pengajuan Work Order baru.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($workOrders->hasPages())
            <div class="card-footer bg-white border-top py-3 d-flex justify-content-end">
                {{ $workOrders->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
