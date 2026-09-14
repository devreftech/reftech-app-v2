@extends('layouts.sales.app')
@section('title', 'Marketplace & Rekonsiliasi Escrow - Finance')
@section('no-container') @endsection

@php
if (!function_exists('getMarketplaceBrand')) {
    function getMarketplaceBrand($name) {
        $lower = strtolower($name);
        if (str_contains($lower, 'shopee')) {
            return [
                'bg' => '#ff5722',
                'bg_light' => '#fff0ec',
                'color' => '#d03b0d',
                'icon' => 'mdi-shopping-outline',
            ];
        } elseif (str_contains($lower, 'tokopedia')) {
            return [
                'bg' => '#03ac0e',
                'bg_light' => '#ebfbee',
                'color' => '#02850b',
                'icon' => 'mdi-storefront-outline',
            ];
        } elseif (str_contains($lower, 'tiktok')) {
            return [
                'bg' => '#111827',
                'bg_light' => '#f3f4f6',
                'color' => '#1f2937',
                'icon' => 'mdi-video-outline',
            ];
        } elseif (str_contains($lower, 'lazada')) {
            return [
                'bg' => '#0f146d',
                'bg_light' => '#edf0ff',
                'color' => '#0f146d',
                'icon' => 'mdi-heart-outline',
            ];
        } elseif (str_contains($lower, 'blibli')) {
            return [
                'bg' => '#0095da',
                'bg_light' => '#e6f6fd',
                'color' => '#0077ae',
                'icon' => 'mdi-cart-outline',
            ];
        } elseif (str_contains($lower, 'airend')) {
            return [
                'bg' => '#6366f1',
                'bg_light' => '#eef2ff',
                'color' => '#4f46e5',
                'icon' => 'mdi-fan',
            ];
        } elseif (str_contains($lower, 'compressor') || str_contains($lower, 'parts')) {
            return [
                'bg' => '#0284c7',
                'bg_light' => '#f0f9ff',
                'color' => '#0369a1',
                'icon' => 'mdi-cog-outline',
            ];
        } elseif (str_contains($lower, 'kojisha') || str_contains($lower, 'filter')) {
            return [
                'bg' => '#0d9488',
                'bg_light' => '#f0fdfa',
                'color' => '#0f766e',
                'icon' => 'mdi-air-filter',
            ];
        } else {
            return [
                'bg' => '#696cff',
                'bg_light' => '#f2f2ff',
                'color' => '#5657d4',
                'icon' => 'mdi-store-outline',
            ];
        }
    }
}
@endphp

@push('before-style')
<style>
    .kpi-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        border-radius: 12px;
    }
    .kpi-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 24px rgba(67, 89, 113, 0.12) !important;
    }
    .brand-icon-box {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        flex-shrink: 0;
    }
    .custom-nav-pills .nav-link {
        border-radius: 10px;
        padding: 11px 20px;
        font-weight: 600;
        color: #566a7f;
        background-color: transparent;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        gap: 8px;
        border: none;
    }
    .custom-nav-pills .nav-link:hover {
        background-color: rgba(105, 108, 255, 0.08);
        color: #696cff;
    }
    .custom-nav-pills .nav-link.active {
        background: linear-gradient(135deg, #696cff 0%, #5657d4 100%);
        color: #ffffff !important;
        box-shadow: 0 4px 12px rgba(105, 108, 255, 0.35);
    }
    .custom-nav-pills .nav-link.active .badge {
        background-color: rgba(255, 255, 255, 0.25) !important;
        color: #ffffff !important;
    }
    .table-custom-row {
        transition: background-color 0.15s ease;
    }
    .table-custom-row:hover {
        background-color: #f8f9fc !important;
    }
    .held-payment-item {
        border-radius: 8px;
        transition: all 0.15s ease;
        border: 1px solid #e7e7e7;
    }
    .held-payment-item:hover {
        background-color: #f8faff;
        border-color: #c7d2fe;
    }
    .held-payment-item.is-selected {
        background-color: #eef2ff;
        border-color: #6366f1;
    }
    .status-pulse {
        display: inline-block;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        margin-right: 6px;
    }
    .status-pulse.active {
        background-color: #10b981;
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.25);
    }
    .status-pulse.inactive {
        background-color: #9ca3af;
    }
    .progress-bar-escrow {
        height: 8px;
        border-radius: 4px;
        overflow: hidden;
        background-color: #e5e7eb;
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-3">

    {{-- Page Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center py-2 mb-3 gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <div class="brand-icon-box bg-label-primary">
                    <i class="mdi mdi-storefront-outline text-primary"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-0 text-dark">Marketplace &amp; Rekonsiliasi Escrow</h4>
                    <span class="text-muted small">
                        Finance &middot; Kelola channel penjualan, pemantauan saldo escrow held, pencairan bank, dan rekonsiliasi.
                    </span>
                </div>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <button type="button" class="btn btn-label-secondary btn-sm px-3 shadow-sm rounded-pill" data-bs-toggle="modal" data-bs-target="#modalCreateMarketplace">
                <i class="mdi mdi-plus-circle-outline me-1"></i> Tambah Marketplace
            </button>
            <button type="button" class="btn btn-primary btn-sm px-3 shadow-sm rounded-pill" data-bs-toggle="modal" data-bs-target="#modalCreateSettlement">
                <i class="mdi mdi-cash-fast me-1"></i> Catat Pencairan Baru
            </button>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm d-flex align-items-center" role="alert">
            <i class="mdi mdi-check-circle-outline fs-5 me-2"></i>
            <div>{{ session('success') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm d-flex align-items-center" role="alert">
            <i class="mdi mdi-alert-circle-outline fs-5 me-2"></i>
            <div>{{ session('error') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (isset($errors) && $errors->any())
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
            <div class="fw-semibold mb-1"><i class="mdi mdi-alert-circle-outline me-1"></i> Terjadi kesalahan input:</div>
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Executive Summary KPI Cards --}}
    <div class="row g-3 mb-4">
        {{-- Saldo Held --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100 kpi-card" style="background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%); border-left: 4px solid #f59e0b !important;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-uppercase fw-bold text-warning small" style="font-size: 11px; letter-spacing: 0.5px;">
                            <i class="mdi mdi-wallet-clock-outline me-1"></i> Saldo Held (Escrow)
                        </span>
                        <span class="badge bg-label-warning rounded-pill">{{ $totalHeldCount }} Transaksi</span>
                    </div>
                    <h4 class="fw-bolder text-dark mb-1">Rp {{ number_format($totalHeldAmount, 0, ',', '.') }}</h4>
                    <small class="text-muted" style="font-size: 11.5px;">Dana transaksi tertahan di marketplace</small>
                </div>
            </div>
        </div>

        {{-- Total Pencairan Masuk --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100 kpi-card" style="background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); border-left: 4px solid #10b981 !important;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-uppercase fw-bold text-success small" style="font-size: 11px; letter-spacing: 0.5px;">
                            <i class="mdi mdi-cash-check me-1"></i> Total Pencairan (Net)
                        </span>
                        <span class="badge bg-label-success rounded-pill">{{ $totalSettlementsCount }} Batch</span>
                    </div>
                    <h4 class="fw-bolder text-success mb-1">Rp {{ number_format($totalSettledNet, 0, ',', '.') }}</h4>
                    <small class="text-muted" style="font-size: 11.5px;">Dana bersih sukses masuk ke rekening</small>
                </div>
            </div>
        </div>

        {{-- Total Potongan Fee --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100 kpi-card" style="background: linear-gradient(135deg, #fff1f2 0%, #ffe4e6 100%); border-left: 4px solid #f43f5e !important;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-uppercase fw-bold text-danger small" style="font-size: 11px; letter-spacing: 0.5px;">
                            <i class="mdi mdi-tag-percent-outline me-1"></i> Biaya / Fee Komisi
                        </span>
                        <span class="badge bg-label-danger rounded-pill">Fee Kanal</span>
                    </div>
                    <h4 class="fw-bolder text-danger mb-1">Rp {{ number_format($totalFees, 0, ',', '.') }}</h4>
                    <small class="text-muted" style="font-size: 11.5px;">Total potongan biaya platform</small>
                </div>
            </div>
        </div>

        {{-- Channel Marketplace Aktif --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100 kpi-card" style="background: linear-gradient(135deg, #f8f9ff 0%, #edf0ff 100%); border-left: 4px solid #6366f1 !important;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-uppercase fw-bold text-primary small" style="font-size: 11px; letter-spacing: 0.5px;">
                            <i class="mdi mdi-storefront-outline me-1"></i> Channel Marketplace
                        </span>
                        <span class="badge bg-label-primary rounded-pill">{{ $activeMarketplaceCount }} Aktif</span>
                    </div>
                    <h4 class="fw-bolder text-primary mb-1">{{ $activeMarketplaceCount }} <span class="fs-6 fw-normal text-muted">/ {{ $marketplaces->count() }} Channel</span></h4>
                    <small class="text-muted" style="font-size: 11.5px;">Toko resmi terdaftar &amp; terhubung</small>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Container with Modern Tabs (Client-side switching without page reload) --}}
    <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
        {{-- Nav Pill Tabs Header --}}
        <div class="card-header bg-white border-bottom p-3">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <ul class="nav nav-pills custom-nav-pills gap-2 m-0" id="marketplaceTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button type="button" class="nav-link {{ $activeTab === 'master' ? 'active' : '' }}" id="btn-tab-master"
                            data-bs-toggle="tab" data-bs-target="#tab-master" role="tab" aria-controls="tab-master" aria-selected="{{ $activeTab === 'master' ? 'true' : 'false' }}">
                            <i class="mdi mdi-store-cog-outline fs-5"></i>
                            <span>Master Data Marketplace</span>
                            <span class="badge rounded-pill bg-label-primary ms-1">{{ $marketplaces->count() }}</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button type="button" class="nav-link {{ $activeTab === 'settlement' ? 'active' : '' }}" id="btn-tab-settlement"
                            data-bs-toggle="tab" data-bs-target="#tab-settlement" role="tab" aria-controls="tab-settlement" aria-selected="{{ $activeTab === 'settlement' ? 'true' : 'false' }}">
                            <i class="mdi mdi-cash-sync fs-5"></i>
                            <span>Riwayat Pencairan (Settlement)</span>
                            <span class="badge rounded-pill bg-label-success ms-1">{{ $totalSettlementsCount }}</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button type="button" class="nav-link {{ $activeTab === 'reconciliation' ? 'active' : '' }}" id="btn-tab-reconciliation"
                            data-bs-toggle="tab" data-bs-target="#tab-reconciliation" role="tab" aria-controls="tab-reconciliation" aria-selected="{{ $activeTab === 'reconciliation' ? 'true' : 'false' }}">
                            <i class="mdi mdi-scale-balance fs-5"></i>
                            <span>Rekonsiliasi Escrow</span>
                            @if ($legacyUnmatchedCount > 0)
                                <span class="badge rounded-pill bg-label-danger ms-1">{{ $legacyUnmatchedCount }} legacy</span>
                            @endif
                        </button>
                    </li>
                </ul>
            </div>
        </div>

        <div class="tab-content p-0">
            {{-- ========================================================================= --}}
            {{-- TAB 1: MASTER DATA MARKETPLACE                                            --}}
            {{-- ========================================================================= --}}
            <div class="tab-pane fade {{ $activeTab === 'master' ? 'show active' : '' }}" id="tab-master" role="tabpanel" aria-labelledby="btn-tab-master">
                {{-- Master Data Filter & Search Toolbar --}}
                <div class="p-3 bg-light border-bottom">
                    <div class="row g-2 align-items-center justify-content-between">
                        <div class="col-12 col-md-5">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-white border-end-0"><i class="mdi mdi-magnify text-muted"></i></span>
                                <input type="text" id="searchMarketplaceTable" class="form-control border-start-0 ps-0" placeholder="Cari nama marketplace, entity, atau bank..." autocomplete="off">
                            </div>
                        </div>
                        <div class="col-12 col-md-7 d-flex justify-content-md-end align-items-center gap-2 flex-wrap">
                            <span class="text-muted small">Filter Entity:</span>
                            <div class="btn-group btn-group-sm" role="group" id="entityFilterGroup">
                                <button type="button" class="btn btn-outline-secondary active filter-entity-btn" data-entity="all">Semua</button>
                                @php
                                    $entities = $marketplaces->pluck('entity')->filter()->unique();
                                @endphp
                                @foreach ($entities as $ent)
                                    <button type="button" class="btn btn-outline-secondary filter-entity-btn" data-entity="{{ strtolower($ent) }}">{{ $ent }}</button>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Master Data Table --}}
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="marketplaceMasterTable">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 28%;">Nama &amp; Kanal Marketplace</th>
                                <th style="width: 14%;">Entity Perusahaan</th>
                                <th style="width: 20%;">Rekening Bank Default</th>
                                <th class="text-center" style="width: 10%;">Fee Platform</th>
                                <th class="text-end" style="width: 16%;">Saldo Held (Escrow)</th>
                                <th class="text-center" style="width: 12%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($marketplaces as $m)
                                @php
                                    $brand = getMarketplaceBrand($m->name);
                                @endphp
                                <tr class="table-custom-row master-data-row" 
                                    data-name="{{ strtolower($m->name) }}" 
                                    data-entity="{{ strtolower($m->entity ?? '') }}" 
                                    data-bank="{{ strtolower($m->defaultBank->bank ?? '') }}">
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="brand-icon-box me-3" style="background-color: {{ $brand['bg_light'] }}; color: {{ $brand['color'] }};">
                                                <i class="mdi {{ $brand['icon'] }}"></i>
                                            </div>
                                            <div>
                                                <div class="d-flex align-items-center gap-2">
                                                    <span class="fw-bold text-dark fs-6">{{ $m->name }}</span>
                                                    @if ($m->is_active)
                                                        <span class="badge bg-label-success rounded-pill px-2 py-0" style="font-size: 10px;">
                                                            <span class="status-pulse active"></span> Aktif
                                                        </span>
                                                    @else
                                                        <span class="badge bg-label-secondary rounded-pill px-2 py-0" style="font-size: 10px;">
                                                            <span class="status-pulse inactive"></span> Nonaktif
                                                        </span>
                                                    @endif
                                                </div>
                                                <small class="text-muted" style="font-size: 11px;">ID #{{ $m->id }} &middot; Dibuat {{ $m->created_at ? $m->created_at->format('d/m/Y') : '-' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if ($m->entity)
                                            <span class="badge bg-label-info rounded-pill px-2.5 py-1">
                                                <i class="mdi mdi-domain me-1"></i>{{ $m->entity }}
                                            </span>
                                        @else
                                            <span class="text-muted small">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($m->defaultBank)
                                            <div class="d-flex align-items-center">
                                                <i class="mdi mdi-bank text-primary me-2 fs-5"></i>
                                                <div>
                                                    <div class="fw-semibold text-dark small">{{ $m->defaultBank->bank }}</div>
                                                    <div class="text-muted font-monospace" style="font-size: 11.5px;">{{ $m->defaultBank->no_rek }}</div>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-muted small fst-italic">Belum diset</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if ($m->fee_percent !== null)
                                            <span class="badge bg-label-danger rounded-pill px-2.5 py-1 fw-semibold">
                                                <i class="mdi mdi-percent me-1"></i>{{ number_format($m->fee_percent, 2) }}%
                                            </span>
                                        @else
                                            <span class="text-muted small">-</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if ($m->held_amount > 0)
                                            <div class="fw-bold text-warning" style="font-size: 14.5px;">
                                                Rp {{ number_format($m->held_amount, 0, ',', '.') }}
                                            </div>
                                            <button type="button" class="btn btn-xs btn-label-warning rounded-pill mt-1 px-2 btn-preview-held" 
                                                data-marketplace-id="{{ $m->id }}" 
                                                data-marketplace-name="{{ $m->name }}">
                                                <i class="mdi mdi-clock-outline me-1"></i>{{ $m->held_count }} payment held
                                            </button>
                                        @else
                                            <span class="badge bg-label-secondary rounded-pill px-2.5 py-1">
                                                <i class="mdi mdi-check-all me-1"></i>Nihil / Rp 0
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="d-inline-flex gap-1 align-items-center">
                                            @if ($m->held_amount > 0 && $m->is_active)
                                                <button type="button" class="btn btn-icon btn-sm btn-label-success rounded-circle btn-settle-shortcut" 
                                                    title="Catat Pencairan Marketplace Ini"
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#modalCreateSettlement"
                                                    data-marketplace-id="{{ $m->id }}">
                                                    <i class="mdi mdi-cash-fast"></i>
                                                </button>
                                            @endif
                                            <button type="button" class="btn btn-icon btn-sm btn-label-primary rounded-circle btn-edit-marketplace" 
                                                title="Edit Marketplace"
                                                data-id="{{ $m->id }}"
                                                data-name="{{ $m->name }}"
                                                data-entity="{{ $m->entity }}"
                                                data-bank-id="{{ $m->id_default_bank }}"
                                                data-fee="{{ $m->fee_percent }}">
                                                <i class="mdi mdi-pencil-outline"></i>
                                            </button>
                                            <form action="{{ route('finance.marketplace.toggle', $m->id) }}" method="post" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-icon btn-sm {{ $m->is_active ? 'btn-label-secondary' : 'btn-label-success' }} rounded-circle" 
                                                    title="{{ $m->is_active ? 'Nonaktifkan Kanal' : 'Aktifkan Kanal' }}"
                                                    onclick="return confirm('{{ $m->is_active ? 'Nonaktifkan marketplace ' . $m->name . '?' : 'Aktifkan marketplace ' . $m->name . '?' }}')">
                                                    <i class="mdi {{ $m->is_active ? 'mdi-eye-off-outline' : 'mdi-eye-outline' }}"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-5">
                                        <div class="my-3">
                                            <i class="mdi mdi-store-remove-outline text-secondary" style="font-size: 48px;"></i>
                                            <p class="mt-2 mb-2 fw-semibold">Belum ada channel marketplace terdaftar.</p>
                                            <button type="button" class="btn btn-primary btn-sm rounded-pill" data-bs-toggle="modal" data-bs-target="#modalCreateMarketplace">
                                                <i class="mdi mdi-plus-circle-outline me-1"></i> Tambah Marketplace Sekarang
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- ========================================================================= --}}
            {{-- TAB 2: RIWAYAT PENCAIRAN (SETTLEMENT)                                     --}}
            {{-- ========================================================================= --}}
            <div class="tab-pane fade {{ $activeTab === 'settlement' ? 'show active' : '' }}" id="tab-settlement" role="tabpanel" aria-labelledby="btn-tab-settlement">
                {{-- Settlement Live Filter Toolbar (Instant without reload) --}}
                <div class="p-3 bg-light border-bottom">
                    <div class="row g-2 align-items-center">
                        <div class="col-12 col-md-4">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-white border-end-0"><i class="mdi mdi-magnify text-muted"></i></span>
                                <input type="text" id="searchSettlementTable" class="form-control border-start-0 ps-0" placeholder="Cari No. Pencairan, Ref, Catatan..." autocomplete="off">
                            </div>
                        </div>
                        <div class="col-12 col-md-3">
                            <select id="filterSettlementMarketplace" class="form-select form-select-sm">
                                <option value="">Semua Marketplace</option>
                                @foreach ($marketplaces as $m)
                                    <option value="{{ $m->id }}">{{ $m->name }} ({{ $m->entity ?: '-' }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-3">
                            <select id="filterSettlementBank" class="form-select form-select-sm">
                                <option value="">Semua Bank Penerima</option>
                                @foreach ($banks as $b)
                                    <option value="{{ $b->id }}">{{ $b->bank }} - {{ $b->no_rek }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-2 d-flex gap-2">
                            <button type="button" id="btnResetSettlementFilter" class="btn btn-label-secondary btn-sm w-100">
                                <i class="mdi mdi-refresh me-1"></i> Reset
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Settlement Table --}}
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="settlementTable">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 16%;">No. Pencairan</th>
                                <th style="width: 15%;">Marketplace</th>
                                <th style="width: 16%;">Bank Tujuan</th>
                                <th style="width: 11%;">Tanggal</th>
                                <th class="text-end" style="width: 12%;">Gross</th>
                                <th class="text-end" style="width: 10%;">Fee</th>
                                <th class="text-end" style="width: 12%;">Net Masuk Bank</th>
                                <th class="text-center" style="width: 8%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($settlements as $s)
                                @php
                                    $brand = getMarketplaceBrand($s->marketplace->name ?? '');
                                    $searchBlob = strtolower(($s->settlement_number ?? '') . ' ' . ($s->reference_no ?? '') . ' ' . ($s->note ?? '') . ' ' . ($s->marketplace->name ?? '') . ' ' . ($s->bank->bank ?? ''));
                                @endphp
                                <tr class="table-custom-row settlement-data-row" 
                                    data-search="{{ $searchBlob }}"
                                    data-marketplace-id="{{ $s->id_marketplace }}"
                                    data-bank-id="{{ $s->id_bank }}">
                                    <td>
                                        <div class="fw-bold font-monospace text-primary fs-6">{{ $s->settlement_number }}</div>
                                        @if ($s->reference_no)
                                            <div class="text-muted" style="font-size: 11px;">Ref: {{ $s->reference_no }}</div>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="brand-icon-box me-2" style="background-color: {{ $brand['bg_light'] }}; color: {{ $brand['color'] }}; width: 30px; height: 30px; font-size: 16px;">
                                                <i class="mdi {{ $brand['icon'] }}"></i>
                                            </div>
                                            <div>
                                                <span class="fw-semibold text-dark">{{ $s->marketplace->name ?? '-' }}</span>
                                                @if ($s->marketplace && $s->marketplace->entity)
                                                    <div class="badge bg-label-info rounded-pill px-1.5 py-0" style="font-size: 10px;">{{ $s->marketplace->entity }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-dark small">{{ $s->bank->bank ?? '-' }}</div>
                                        <div class="text-muted font-monospace" style="font-size: 11px;">{{ $s->bank->no_rek ?? '-' }}</div>
                                    </td>
                                    <td>
                                        <div class="text-dark fw-medium small">{{ optional($s->settlement_date)->format('d M Y') }}</div>
                                        <small class="text-muted" style="font-size: 10.5px;">{{ optional($s->settlement_date)->diffForHumans() }}</small>
                                    </td>
                                    <td class="text-end">
                                        <div class="fw-medium text-dark">Rp {{ number_format($s->gross_amount, 0, ',', '.') }}</div>
                                        <small class="text-muted" style="font-size: 11px;">{{ $s->items->count() }} payments</small>
                                    </td>
                                    <td class="text-end">
                                        @if ($s->fee_amount > 0)
                                            <div class="text-danger fw-medium">- Rp {{ number_format($s->fee_amount, 0, ',', '.') }}</div>
                                        @else
                                            <span class="text-muted small">Rp 0</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <div class="fw-bolder text-success fs-6">Rp {{ number_format($s->net_amount, 0, ',', '.') }}</div>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-icon btn-sm btn-label-primary rounded-circle" data-bs-toggle="modal" data-bs-target="#modalDetailSettlement{{ $s->id }}" title="Detail Pencairan">
                                            <i class="mdi mdi-eye-outline"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr id="emptySettlementRow">
                                    <td colspan="8" class="text-center text-muted py-5">
                                        <div class="my-3">
                                            <i class="mdi mdi-cash-remove text-secondary" style="font-size: 48px;"></i>
                                            <p class="mt-2 mb-2 fw-semibold">Belum ada riwayat pencairan settlement.</p>
                                            <button type="button" class="btn btn-primary btn-sm rounded-pill" data-bs-toggle="modal" data-bs-target="#modalCreateSettlement">
                                                <i class="mdi mdi-cash-fast me-1"></i> Catat Pencairan Baru
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- ========================================================================= --}}
            {{-- TAB 3: REKONSILIASI ESCROW                                                --}}
            {{-- ========================================================================= --}}
            <div class="tab-pane fade {{ $activeTab === 'reconciliation' ? 'show active' : '' }}" id="tab-reconciliation" role="tabpanel" aria-labelledby="btn-tab-reconciliation">
                <div class="p-4">
                    {{-- Reconciliation Overview Card --}}
                    <div class="card border-0 bg-light rounded-3 mb-4">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                                <div>
                                    <h6 class="fw-bold mb-1"><i class="mdi mdi-scale-balance text-warning me-1"></i> Ringkasan Rekonsiliasi Escrow Per Channel</h6>
                                    <p class="text-muted small mb-0">Perbandingan antara saldo penjualan yang masih tertahan (held) vs dana yang telah sukses dicairkan ke bank.</p>
                                </div>
                                <div>
                                    <span class="badge bg-label-primary px-3 py-2 rounded-pill">
                                        <i class="mdi mdi-check-circle me-1"></i> Data Real-Time
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Reconciliation Table --}}
                    <div class="table-responsive border rounded-3 overflow-hidden mb-4">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 14%;">Entity</th>
                                    <th style="width: 20%;">Marketplace</th>
                                    <th class="text-end" style="width: 16%;">Held (Belum Cair)</th>
                                    <th class="text-end" style="width: 16%;">Disbursed (Sudah Cair)</th>
                                    <th style="width: 18%;">Rasio Pencairan</th>
                                    <th class="text-end" style="width: 16%;">Total Net Settlement</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($reconciliation as $row)
                                    @php
                                        $brand = getMarketplaceBrand($row->marketplace->name);
                                    @endphp
                                    <tr class="table-custom-row">
                                        <td>
                                            @if ($row->marketplace->entity)
                                                <span class="badge bg-label-info rounded-pill px-2.5 py-1">{{ $row->marketplace->entity }}</span>
                                            @else
                                                <span class="text-muted small">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="brand-icon-box me-2" style="background-color: {{ $brand['bg_light'] }}; color: {{ $brand['color'] }}; width: 32px; height: 32px; font-size: 16px;">
                                                    <i class="mdi {{ $brand['icon'] }}"></i>
                                                </div>
                                                <span class="fw-bold text-dark">{{ $row->marketplace->name }}</span>
                                            </div>
                                        </td>
                                        <td class="text-end">
                                            @if ($row->held > 0)
                                                <span class="fw-bold text-warning fs-6">Rp {{ number_format($row->held, 0, ',', '.') }}</span>
                                            @else
                                                <span class="text-muted small">Rp 0</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            @if ($row->disbursed > 0)
                                                <span class="fw-semibold text-success">Rp {{ number_format($row->disbursed, 0, ',', '.') }}</span>
                                            @else
                                                <span class="text-muted small">Rp 0</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center justify-content-between mb-1" style="font-size: 11px;">
                                                <span class="text-muted">Cair: {{ $row->disbursed_percent }}%</span>
                                                <span class="text-muted">Total: Rp {{ number_format($row->total_escrow, 0, ',', '.') }}</span>
                                            </div>
                                            <div class="progress-bar-escrow">
                                                <div style="height: 100%; width: {{ $row->disbursed_percent }}%; background-color: #10b981; border-radius: 4px;"></div>
                                            </div>
                                        </td>
                                        <td class="text-end">
                                            <span class="fw-bold text-dark">Rp {{ number_format($row->settled_net, 0, ',', '.') }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">Belum ada data rekonsiliasi channel.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Legacy Unmatched Section --}}
                    @if ($legacyUnmatchedCount > 0)
                        <div class="card border border-warning shadow-none rounded-3 mt-4">
                            <div class="card-header bg-label-warning py-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
                                <div>
                                    <h6 class="fw-bold mb-0 text-dark">
                                        <i class="mdi mdi-history text-warning me-1"></i> Payment Escrow Legacy / Belum Ditandai Channel
                                    </h6>
                                    <span class="text-muted small">
                                        Total {{ $legacyUnmatchedCount }} pembayaran Escrow historis yang belum diasosiasikan ke channel marketplace tertentu.
                                    </span>
                                </div>
                                <div>
                                    <input type="text" id="filterLegacyInput" class="form-control form-control-sm bg-white" placeholder="Cari client / ref no..." style="min-width: 220px;">
                                </div>
                            </div>
                            <div class="table-responsive" style="max-height: 380px; overflow-y: auto;">
                                <table class="table table-sm table-hover align-middle mb-0" id="legacyTable">
                                    <thead class="table-light sticky-top">
                                        <tr>
                                            <th style="width: 14%;">Tanggal</th>
                                            <th style="width: 22%;">No. Referensi</th>
                                            <th style="width: 24%;">Client</th>
                                            <th class="text-end" style="width: 18%;">Nominal</th>
                                            <th style="width: 22%;">Tandai ke Marketplace</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($legacyUnmatched as $p)
                                            <tr class="legacy-row" data-client="{{ strtolower($p->client) }}" data-ref="{{ strtolower($p->ref) }}">
                                                <td>
                                                    <span class="text-dark small">{{ $p->date ? \Carbon\Carbon::parse($p->date)->format('d/m/Y') : '-' }}</span>
                                                </td>
                                                <td>
                                                    <span class="fw-semibold text-primary font-monospace small">{{ $p->ref }}</span>
                                                </td>
                                                <td>
                                                    <span class="text-dark small">{{ $p->client }}</span>
                                                </td>
                                                <td class="text-end fw-semibold text-dark">
                                                    Rp {{ number_format($p->amount, 0, ',', '.') }}
                                                </td>
                                                <td>
                                                    <form action="{{ route('finance.marketplace.payments.assign', $p->id) }}" method="post" class="d-flex gap-1 align-items-center">
                                                        @csrf
                                                        <select name="id_marketplace" class="form-select form-select-sm" required>
                                                            <option value="" disabled selected>-- Pilih Channel --</option>
                                                            @foreach ($marketplaces as $m)
                                                                <option value="{{ $m->id }}">{{ $m->name }}</option>
                                                            @endforeach
                                                        </select>
                                                        <button type="submit" class="btn btn-sm btn-label-primary px-2.5" title="Simpan asosiasi">
                                                            <i class="mdi mdi-check"></i>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ========================================================================= --}}
{{-- MODALS SECTION (All placed outside .card to prevent overflow trapping)    --}}
{{-- ========================================================================= --}}

{{-- MODAL: TAMBAH MARKETPLACE --}}
<div class="modal fade" id="modalCreateMarketplace" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow">
            <form action="{{ route('finance.marketplace.store') }}" method="post">
                @csrf
                <div class="modal-header border-bottom">
                    <div class="d-flex align-items-center gap-2">
                        <div class="brand-icon-box bg-label-primary" style="width: 36px; height: 36px; font-size: 18px;">
                            <i class="mdi mdi-store-plus-outline text-primary"></i>
                        </div>
                        <h5 class="modal-title fw-bold mb-0">Tambah Channel Marketplace</h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Marketplace / Toko <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="mdi mdi-store-outline"></i></span>
                            <input type="text" name="name" class="form-control" placeholder="mis. Tokopedia Reftech Official, Shopee Parts" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Entity / Perusahaan</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="mdi mdi-domain"></i></span>
                            <input type="text" name="entity" class="form-control" placeholder="mis. Reftech / Kojisha">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Rekening Bank Default (Tujuan Pencairan)</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="mdi mdi-bank"></i></span>
                            <select name="id_default_bank" class="form-select">
                                <option value="">-- Pilih Rekening Default (Opsional) --</option>
                                @foreach ($banks as $b)
                                    <option value="{{ $b->id }}">{{ $b->bank }} - {{ $b->no_rek }} ({{ $b->entity }})</option>
                                @endforeach
                            </select>
                        </div>
                        <small class="text-muted">Rekening bank yang biasa digunakan untuk menerima dana pencairan dari kanal ini.</small>
                    </div>
                    <div class="mb-2">
                        <label class="form-label fw-semibold">Estimasi Fee Platform (%)</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="mdi mdi-percent"></i></span>
                            <input type="number" step="0.01" min="0" max="100" name="fee_percent" class="form-control" placeholder="mis. 2.50">
                            <span class="input-group-text">%</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top bg-light">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4">Simpan Marketplace</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL: EDIT MARKETPLACE (Global Outside Table) --}}
<div class="modal fade" id="modalEditMarketplace" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow">
            <form id="formEditMarketplace" method="post" action="">
                @csrf
                <div class="modal-header border-bottom">
                    <div class="d-flex align-items-center gap-2">
                        <div class="brand-icon-box bg-label-primary" style="width: 36px; height: 36px; font-size: 18px;">
                            <i class="mdi mdi-pencil-outline text-primary"></i>
                        </div>
                        <h5 class="modal-title fw-bold mb-0" id="modalEditMarketplaceTitle">Edit Marketplace</h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Marketplace <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="mdi mdi-store-outline"></i></span>
                            <input type="text" name="name" id="editMarketplaceName" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Entity / Nama Perusahaan</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="mdi mdi-domain"></i></span>
                            <input type="text" name="entity" id="editMarketplaceEntity" class="form-control" placeholder="mis. Reftech / Kojisha">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Rekening Bank Default (Pencairan)</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="mdi mdi-bank"></i></span>
                            <select name="id_default_bank" id="editMarketplaceBank" class="form-select">
                                <option value="">-- Tidak diset --</option>
                                @foreach ($banks as $b)
                                    <option value="{{ $b->id }}">{{ $b->bank }} - {{ $b->no_rek }} ({{ $b->entity }})</option>
                                @endforeach
                            </select>
                        </div>
                        <small class="text-muted">Rekening bank perusahaan yang akan otomatis terpilih saat mencatat pencairan.</small>
                    </div>
                    <div class="mb-2">
                        <label class="form-label fw-semibold">Fee Marketplace (%)</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="mdi mdi-percent"></i></span>
                            <input type="number" step="0.01" min="0" max="100" name="fee_percent" id="editMarketplaceFee" class="form-control" placeholder="0.00">
                            <span class="input-group-text">%</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top bg-light">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL: CATAT PENCAIRAN (SETTLEMENT) --}}
<div class="modal fade" id="modalCreateSettlement" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content border-0 shadow">
            <form action="{{ route('finance.marketplace.settlements.store') }}" method="post" enctype="multipart/form-data" id="formCreateSettlement">
                @csrf
                <div class="modal-header border-bottom">
                    <div class="d-flex align-items-center gap-2">
                        <div class="brand-icon-box bg-label-primary" style="width: 38px; height: 38px; font-size: 20px;">
                            <i class="mdi mdi-cash-fast text-primary"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold mb-0">Catat Pencairan (Settlement)</h5>
                            <span class="text-muted small">Pencairan saldo escrow marketplace masuk ke rekening kas bank perusahaan.</span>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        {{-- Step 1: Channel & Bank --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Kanal Marketplace <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="mdi mdi-store-outline"></i></span>
                                <select name="id_marketplace" id="settlementMarketplace" class="form-select" required>
                                    <option value="" disabled selected>-- Pilih Marketplace --</option>
                                    @foreach ($marketplaces->where('is_active', true) as $m)
                                        <option value="{{ $m->id }}" 
                                            data-bank-id="{{ $m->id_default_bank }}" 
                                            data-fee="{{ $m->fee_percent }}">
                                            {{ $m->name }} ({{ $m->entity ?: '-' }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Rekening Bank Penerima <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="mdi mdi-bank"></i></span>
                                <select name="id_bank" id="settlementBank" class="form-select" required>
                                    <option value="" disabled selected>-- Pilih Bank --</option>
                                    @foreach ($banks as $b)
                                        <option value="{{ $b->id }}">{{ $b->bank }} - {{ $b->no_rek }} ({{ $b->entity }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Step 2: Date & Ref --}}
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Tanggal Pencairan <span class="text-danger">*</span></label>
                            <input type="date" name="settlement_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">No. Ref Marketplace / Batch ID</label>
                            <input type="text" name="reference_no" class="form-control" placeholder="mis. WDR-202609-012">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Bukti Transfer (File)</label>
                            <input type="file" name="proof_file" class="form-control" accept=".jpg,.jpeg,.png,.pdf">
                        </div>

                        {{-- Step 3: Held Payments Selection Box --}}
                        <div class="col-12">
                            <div class="card border rounded-3 p-3 bg-light">
                                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
                                    <div>
                                        <span class="fw-bold text-dark">
                                            <i class="mdi mdi-checkbox-multiple-marked-outline text-primary me-1"></i>
                                            Pilih Payment Escrow yang Dicairkan (Held) <span class="text-danger">*</span>
                                        </span>
                                        <div class="text-muted small">Pilih payment order yang termasuk dalam batch transfer ini.</div>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <button type="button" class="btn btn-xs btn-outline-primary" id="btnToggleSelectAll" style="display: none;">
                                            Pilih Semua
                                        </button>
                                        <button type="button" class="btn btn-xs btn-label-success" id="btnApplyTotalToGross" style="display: none;">
                                            <i class="mdi mdi-arrow-down-bold me-1"></i> Terapkan ke Gross
                                        </button>
                                    </div>
                                </div>

                                {{-- Search within held payments --}}
                                <div class="mb-2" id="heldPaymentsSearchContainer" style="display: none;">
                                    <input type="text" id="searchHeldInput" class="form-control form-control-sm bg-white" placeholder="Cari client, no. quote di list...">
                                </div>

                                <div id="settlementHeldPaymentsBox" class="border rounded-2 bg-white p-2" style="max-height: 220px; overflow-y: auto;">
                                    <div class="text-center text-muted py-4" id="settlementHeldPlaceholder">
                                        <i class="mdi mdi-cursor-default-click-outline fs-3 text-secondary"></i>
                                        <p class="mb-0 mt-1 small">Pilih marketplace di atas terlebih dahulu untuk menampilkan daftar order held.</p>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between align-items-center mt-2 px-1">
                                    <small class="text-muted"><span id="settlementSelectedCount">0</span> payment terpilih</small>
                                    <span class="fw-bold text-dark small">Total Terpilih: <span class="text-primary fs-6">Rp <span id="settlementRunningTotal">0</span></span></span>
                                </div>
                            </div>
                        </div>

                        {{-- Step 4: Amounts --}}
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Gross Amount (Total Escrow) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" step="1" min="0" name="gross_amount" id="settlementGross" class="form-control" placeholder="0" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Potongan Fee Marketplace</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" step="1" min="0" name="fee_amount" id="settlementFee" class="form-control" value="0">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Net Masuk Rekening <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" step="1" min="0" name="net_amount" id="settlementNet" class="form-control fw-bold text-success" placeholder="0" required>
                            </div>
                        </div>

                        {{-- Note --}}
                        <div class="col-12">
                            <label class="form-label fw-semibold">Catatan / Keterangan</label>
                            <textarea name="note" class="form-control" rows="2" placeholder="Catatan tambahan batch pencairan..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top bg-light">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="mdi mdi-check-circle-outline me-1"></i> Simpan Pencairan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODALS: DETAIL SETTLEMENT (Rendered outside table to prevent overflow trapping) --}}
@foreach ($settlements as $s)
    <div class="modal fade" id="modalDetailSettlement{{ $s->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-bottom">
                    <div class="d-flex align-items-center gap-2">
                        <div class="brand-icon-box bg-label-success" style="width: 36px; height: 36px; font-size: 18px;">
                            <i class="mdi mdi-cash-check text-success"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold mb-0">Rincian Pencairan #{{ $s->settlement_number }}</h5>
                            <span class="text-muted small">{{ optional($s->settlement_date)->format('d F Y') }}</span>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    {{-- Financial Summary Box --}}
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded-3 text-center">
                                <div class="text-muted small mb-1">Gross Amount</div>
                                <h5 class="fw-bold mb-0 text-dark">Rp {{ number_format($s->gross_amount, 0, ',', '.') }}</h5>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded-3 text-center">
                                <div class="text-muted small mb-1">Fee Marketplace</div>
                                <h5 class="fw-bold mb-0 text-danger">- Rp {{ number_format($s->fee_amount, 0, ',', '.') }}</h5>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 bg-label-success rounded-3 text-center">
                                <div class="text-success small mb-1">Net Diterima di Bank</div>
                                <h5 class="fw-bold mb-0 text-success">Rp {{ number_format($s->net_amount, 0, ',', '.') }}</h5>
                            </div>
                        </div>
                    </div>

                    {{-- Info Grid --}}
                    <div class="row g-3 mb-4">
                        <div class="col-sm-6">
                            <div class="d-flex align-items-center mb-2">
                                <span class="text-muted me-2" style="width: 120px;">Marketplace:</span>
                                <span class="fw-semibold text-dark">{{ $s->marketplace->name ?? '-' }} ({{ $s->marketplace->entity ?? '-' }})</span>
                            </div>
                            <div class="d-flex align-items-center mb-2">
                                <span class="text-muted me-2" style="width: 120px;">Bank Tujuan:</span>
                                <span class="fw-semibold text-dark">{{ $s->bank->bank ?? '-' }} - {{ $s->bank->no_rek ?? '-' }}</span>
                            </div>
                            <div class="d-flex align-items-center">
                                <span class="text-muted me-2" style="width: 120px;">Ref. Eksternal:</span>
                                <span class="text-dark">{{ $s->reference_no ?: '-' }}</span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="d-flex align-items-center mb-2">
                                <span class="text-muted me-2" style="width: 120px;">Dicatat Oleh:</span>
                                <span class="fw-semibold text-dark">{{ $s->creator->name ?? 'Sistem' }}</span>
                            </div>
                            <div class="d-flex align-items-center mb-2">
                                <span class="text-muted me-2" style="width: 120px;">Waktu Input:</span>
                                <span class="text-dark">{{ $s->created_at ? $s->created_at->format('d/m/Y H:i') : '-' }}</span>
                            </div>
                            <div class="d-flex align-items-center">
                                <span class="text-muted me-2" style="width: 120px;">Bukti Transfer:</span>
                                @if ($s->proof_file)
                                    <a href="{{ asset('storage/' . $s->proof_file) }}" target="_blank" class="btn btn-xs btn-label-primary">
                                        <i class="mdi mdi-open-in-new me-1"></i> Buka Bukti File
                                    </a>
                                @else
                                    <span class="text-muted small">Tidak ada file</span>
                                @endif
                            </div>
                        </div>
                        @if ($s->note)
                            <div class="col-12">
                                <div class="p-2.5 bg-light rounded text-dark small">
                                    <i class="mdi mdi-note-text-outline me-1 text-primary"></i> <strong>Catatan:</strong> {{ $s->note }}
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- Bundled Escrow Payments Table --}}
                    <h6 class="fw-bold mb-2">
                        <i class="mdi mdi-format-list-checks text-primary me-1"></i>
                        Daftar Payment Escrow dalam Batch Ini ({{ $s->items->count() }} item)
                    </h6>
                    <div class="table-responsive border rounded" style="max-height: 240px; overflow-y: auto;">
                        <table class="table table-sm table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>No. Ref</th>
                                    <th>Tanggal</th>
                                    <th class="text-end">Nominal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($s->items as $item)
                                    <tr>
                                        <td>
                                            <span class="fw-semibold text-dark">Payment #{{ $item->id_payment }}</span>
                                        </td>
                                        <td>{{ $item->payment?->date ? \Carbon\Carbon::parse($item->payment->date)->format('d/m/Y') : '-' }}</td>
                                        <td class="text-end fw-semibold">Rp {{ number_format($item->amount, 0, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-3">Tidak ada data item payment terlampir.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer border-top bg-light">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endforeach

{{-- MODAL: PREVIEW HELD PAYMENTS --}}
<div class="modal fade" id="modalPreviewHeldPayments" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom">
                <div class="d-flex align-items-center gap-2">
                    <div class="brand-icon-box bg-label-warning" style="width: 36px; height: 36px; font-size: 18px;">
                        <i class="mdi mdi-wallet-clock-outline text-warning"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0" id="previewHeldTitle">Daftar Payment Held</h5>
                        <span class="text-muted small">Transaksi yang belum dicairkan ke kas bank</span>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3">
                <div class="table-responsive" style="max-height: 360px; overflow-y: auto;">
                    <table class="table table-sm table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Tanggal</th>
                                <th>No. Ref / Quote</th>
                                <th>Client</th>
                                <th class="text-end">Nominal</th>
                            </tr>
                        </thead>
                        <tbody id="previewHeldTableBody">
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4"><span class="spinner-border spinner-border-sm me-1"></span> Memuat data...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer border-top bg-light">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" id="btnPreviewGoSettle">
                    <i class="mdi mdi-cash-fast me-1"></i> Catat Pencairan Sekarang
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('after-script')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var heldPaymentsUrlBase = "{{ url('finance/marketplace') }}";

        function formatRupiah(num) {
            return new Intl.NumberFormat('id-ID').format(Math.round(num));
        }

        // =========================================================================
        // 1. Tab Switching Without Reload & Hash Persistence
        // =========================================================================
        var hash = window.location.hash;
        var urlParams = new URLSearchParams(window.location.search);
        var tabParam = urlParams.get('tab');
        var targetTab = hash ? hash.replace('#', '') : tabParam;

        if (targetTab) {
            var triggerBtn = document.querySelector('#marketplaceTabs button[data-bs-target="#tab-' + targetTab + '"]');
            if (triggerBtn) {
                var tabInstance = bootstrap.Tab.getOrCreateInstance(triggerBtn);
                tabInstance.show();
            }
        }

        document.querySelectorAll('#marketplaceTabs button[data-bs-toggle="tab"]').forEach(function (btn) {
            btn.addEventListener('shown.bs.tab', function (e) {
                var targetId = e.target.getAttribute('data-bs-target').replace('#tab-', '');
                if (window.history.replaceState) {
                    window.history.replaceState(null, null, '#' + targetId);
                } else {
                    window.location.hash = targetId;
                }
            });
        });

        // =========================================================================
        // 2. Live Filter Master Data Table (Instant client-side)
        // =========================================================================
        var searchMasterInput = document.getElementById('searchMarketplaceTable');
        var masterRows = document.querySelectorAll('.master-data-row');
        var currentEntityFilter = 'all';

        function applyMasterFilters() {
            var query = (searchMasterInput?.value || '').toLowerCase().trim();
            masterRows.forEach(function (row) {
                var name = row.getAttribute('data-name') || '';
                var entity = row.getAttribute('data-entity') || '';
                var bank = row.getAttribute('data-bank') || '';

                var matchesQuery = !query || name.includes(query) || entity.includes(query) || bank.includes(query);
                var matchesEntity = (currentEntityFilter === 'all') || (entity === currentEntityFilter);

                if (matchesQuery && matchesEntity) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        searchMasterInput?.addEventListener('input', applyMasterFilters);

        document.querySelectorAll('.filter-entity-btn').forEach(function (btn) {
            btn.addEventListener('click', function () {
                document.querySelectorAll('.filter-entity-btn').forEach(function (b) { b.classList.remove('active'); });
                this.classList.add('active');
                currentEntityFilter = this.getAttribute('data-entity');
                applyMasterFilters();
            });
        });

        // =========================================================================
        // 3. Edit Marketplace Modal Handler (Outside table to prevent overflow clip)
        // =========================================================================
        var editModalEl = document.getElementById('modalEditMarketplace');
        var editModal = editModalEl ? new bootstrap.Modal(editModalEl) : null;
        var formEditMarketplace = document.getElementById('formEditMarketplace');
        var editMarketplaceTitle = document.getElementById('modalEditMarketplaceTitle');
        var editMarketplaceName = document.getElementById('editMarketplaceName');
        var editMarketplaceEntity = document.getElementById('editMarketplaceEntity');
        var editMarketplaceBank = document.getElementById('editMarketplaceBank');
        var editMarketplaceFee = document.getElementById('editMarketplaceFee');
        var updateUrlBase = "{{ url('finance/marketplace') }}";

        document.querySelectorAll('.btn-edit-marketplace').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var id = this.getAttribute('data-id');
                var name = this.getAttribute('data-name') || '';
                var entity = this.getAttribute('data-entity') || '';
                var bankId = this.getAttribute('data-bank-id') || '';
                var fee = this.getAttribute('data-fee') || '';

                if (formEditMarketplace) formEditMarketplace.action = updateUrlBase + '/' + id + '/update';
                if (editMarketplaceTitle) editMarketplaceTitle.textContent = 'Edit Marketplace: ' + name;
                if (editMarketplaceName) editMarketplaceName.value = name;
                if (editMarketplaceEntity) editMarketplaceEntity.value = entity;
                if (editMarketplaceBank) editMarketplaceBank.value = bankId;
                if (editMarketplaceFee) editMarketplaceFee.value = fee;

                if (editModal) editModal.show();
            });
        });

        // =========================================================================
        // 4. Live Filter Settlement Table (Instant client-side)
        // =========================================================================
        var searchSettlementInput = document.getElementById('searchSettlementTable');
        var filterSettlementMkt = document.getElementById('filterSettlementMarketplace');
        var filterSettlementBank = document.getElementById('filterSettlementBank');
        var btnResetSettlement = document.getElementById('btnResetSettlementFilter');
        var settlementRows = document.querySelectorAll('.settlement-data-row');

        function applySettlementFilters() {
            var q = (searchSettlementInput?.value || '').toLowerCase().trim();
            var mktId = filterSettlementMkt?.value || '';
            var bankId = filterSettlementBank?.value || '';

            settlementRows.forEach(function (row) {
                var searchData = row.getAttribute('data-search') || '';
                var rowMkt = row.getAttribute('data-marketplace-id') || '';
                var rowBank = row.getAttribute('data-bank-id') || '';

                var matchQuery = !q || searchData.includes(q);
                var matchMkt = !mktId || rowMkt === mktId;
                var matchBank = !bankId || rowBank === bankId;

                row.style.display = (matchQuery && matchMkt && matchBank) ? '' : 'none';
            });
        }

        searchSettlementInput?.addEventListener('input', applySettlementFilters);
        filterSettlementMkt?.addEventListener('change', applySettlementFilters);
        filterSettlementBank?.addEventListener('change', applySettlementFilters);
        btnResetSettlement?.addEventListener('click', function () {
            if (searchSettlementInput) searchSettlementInput.value = '';
            if (filterSettlementMkt) filterSettlementMkt.value = '';
            if (filterSettlementBank) filterSettlementBank.value = '';
            applySettlementFilters();
        });

        // =========================================================================
        // 5. Settlement Modal Form: Gross - Fee = Net
        // =========================================================================
        var grossInput = document.getElementById('settlementGross');
        var feeInput = document.getElementById('settlementFee');
        var netInput = document.getElementById('settlementNet');

        function recalcNet() {
            var gross = parseFloat(grossInput?.value || 0);
            var fee = parseFloat(feeInput?.value || 0);
            if (netInput) {
                netInput.value = Math.max(gross - fee, 0);
            }
        }
        grossInput?.addEventListener('input', recalcNet);
        feeInput?.addEventListener('input', recalcNet);

        // =========================================================================
        // 6. Settlement Modal Form: Held Payments Fetcher
        // =========================================================================
        var marketplaceSelect = document.getElementById('settlementMarketplace');
        var bankSelect = document.getElementById('settlementBank');
        var box = document.getElementById('settlementHeldPaymentsBox');
        var runningTotalEl = document.getElementById('settlementRunningTotal');
        var selectedCountEl = document.getElementById('settlementSelectedCount');
        var btnToggleSelectAll = document.getElementById('btnToggleSelectAll');
        var btnApplyTotalToGross = document.getElementById('btnApplyTotalToGross');
        var searchHeldContainer = document.getElementById('heldPaymentsSearchContainer');
        var searchHeldInput = document.getElementById('searchHeldInput');

        var currentSelectedTotal = 0;

        function updateRunningTotal() {
            if (!box) return;
            var checked = box.querySelectorAll('input[type=checkbox]:checked');
            var total = 0;
            checked.forEach(function (cb) {
                total += parseFloat(cb.dataset.amount || 0);
                var parentItem = cb.closest('.held-payment-item');
                if (parentItem) parentItem.classList.add('is-selected');
            });
            box.querySelectorAll('input[type=checkbox]:not(:checked)').forEach(function (cb) {
                var parentItem = cb.closest('.held-payment-item');
                if (parentItem) parentItem.classList.remove('is-selected');
            });

            currentSelectedTotal = total;
            if (runningTotalEl) runningTotalEl.textContent = formatRupiah(total);
            if (selectedCountEl) selectedCountEl.textContent = checked.length;

            if (btnApplyTotalToGross) {
                btnApplyTotalToGross.style.display = total > 0 ? 'inline-block' : 'none';
            }
        }

        btnApplyTotalToGross?.addEventListener('click', function () {
            if (grossInput) {
                grossInput.value = Math.round(currentSelectedTotal);
                recalcNet();
            }
        });

        var allSelectedState = false;
        btnToggleSelectAll?.addEventListener('click', function () {
            if (!box) return;
            var visibleCheckboxes = box.querySelectorAll('.held-payment-item:not([style*="display: none"]) input[type=checkbox]');
            allSelectedState = !allSelectedState;
            visibleCheckboxes.forEach(function (cb) {
                cb.checked = allSelectedState;
            });
            this.textContent = allSelectedState ? 'Batal Pilih Semua' : 'Pilih Semua';
            updateRunningTotal();
        });

        searchHeldInput?.addEventListener('input', function () {
            var q = (this.value || '').toLowerCase().trim();
            var items = box.querySelectorAll('.held-payment-item');
            items.forEach(function (item) {
                var text = item.textContent.toLowerCase();
                item.style.display = (!q || text.includes(q)) ? '' : 'none';
            });
        });

        function loadHeldPaymentsForMarketplace(id) {
            if (!box) return;
            box.innerHTML = '<div class="text-center text-muted py-3"><span class="spinner-border spinner-border-sm me-1"></span> Memuat daftar payment escrow held...</div>';
            if (btnToggleSelectAll) btnToggleSelectAll.style.display = 'none';
            if (btnApplyTotalToGross) btnApplyTotalToGross.style.display = 'none';
            if (searchHeldContainer) searchHeldContainer.style.display = 'none';

            fetch(heldPaymentsUrlBase + '/' + id + '/held-payments')
                .then(function (res) { return res.json(); })
                .then(function (json) {
                    var data = json.data || [];
                    if (data.length === 0) {
                        box.innerHTML = '<div class="text-center text-muted py-4"><i class="mdi mdi-check-circle-outline text-success fs-3"></i><p class="mb-0 mt-1 small">Tidak ada payment escrow yang masih held (semua telah dicairkan).</p></div>';
                        updateRunningTotal();
                        return;
                    }
                    var html = '';
                    data.forEach(function (p) {
                        html += '<div class="held-payment-item p-2 mb-1">' +
                            '<div class="form-check m-0 d-flex align-items-center w-100">' +
                                '<input class="form-check-input me-2" type="checkbox" name="payment_ids[]" value="' + p.id + '" data-amount="' + p.amount + '" id="pay-' + p.id + '">' +
                                '<label class="form-check-label d-flex justify-content-between align-items-center w-100 cursor-pointer" for="pay-' + p.id + '">' +
                                    '<div>' +
                                        '<div class="fw-semibold text-dark small">' + p.client + '</div>' +
                                        '<small class="text-muted">' + p.ref + ' &middot; ' + p.date + '</small>' +
                                    '</div>' +
                                    '<span class="fw-bold text-primary small">Rp ' + formatRupiah(p.amount) + '</span>' +
                                '</label>' +
                            '</div>' +
                        '</div>';
                    });
                    box.innerHTML = html;
                    if (btnToggleSelectAll) btnToggleSelectAll.style.display = 'inline-block';
                    if (searchHeldContainer) searchHeldContainer.style.display = 'block';

                    box.querySelectorAll('input[type=checkbox]').forEach(function (cb) {
                        cb.addEventListener('change', updateRunningTotal);
                    });
                    updateRunningTotal();
                })
                .catch(function () {
                    box.innerHTML = '<p class="text-danger small mb-0 p-2">Gagal memuat data payment.</p>';
                });
        }

        marketplaceSelect?.addEventListener('change', function () {
            var selectedOption = this.options[this.selectedIndex];
            var bankId = selectedOption.getAttribute('data-bank-id');
            var feePercent = parseFloat(selectedOption.getAttribute('data-fee') || 0);

            if (bankId && bankSelect) {
                bankSelect.value = bankId;
            }

            loadHeldPaymentsForMarketplace(this.value);
        });

        // 7. Shortcut from Master Data to Settlement Modal
        document.querySelectorAll('.btn-settle-shortcut').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var mId = this.getAttribute('data-marketplace-id');
                if (marketplaceSelect && mId) {
                    marketplaceSelect.value = mId;
                    marketplaceSelect.dispatchEvent(new Event('change'));
                }
            });
        });

        // 8. Preview Held Payments Modal
        var previewModalEl = document.getElementById('modalPreviewHeldPayments');
        var previewModal = previewModalEl ? new bootstrap.Modal(previewModalEl) : null;
        var previewBody = document.getElementById('previewHeldTableBody');
        var previewTitle = document.getElementById('previewHeldTitle');
        var btnPreviewGoSettle = document.getElementById('btnPreviewGoSettle');
        var currentPreviewMarketplaceId = null;

        document.querySelectorAll('.btn-preview-held').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var mId = this.getAttribute('data-marketplace-id');
                var mName = this.getAttribute('data-marketplace-name');
                currentPreviewMarketplaceId = mId;

                if (previewTitle) previewTitle.textContent = 'Daftar Saldo Held: ' + mName;
                if (previewBody) previewBody.innerHTML = '<tr><td colspan="4" class="text-center text-muted py-4"><span class="spinner-border spinner-border-sm me-1"></span> Memuat data...</td></tr>';
                if (previewModal) previewModal.show();

                fetch(heldPaymentsUrlBase + '/' + mId + '/held-payments')
                    .then(function (res) { return res.json(); })
                    .then(function (json) {
                        var data = json.data || [];
                        if (data.length === 0) {
                            previewBody.innerHTML = '<tr><td colspan="4" class="text-center text-muted py-4">Semua payment telah dicairkan.</td></tr>';
                            return;
                        }
                        var html = '';
                        var total = 0;
                        data.forEach(function (p) {
                            total += p.amount;
                            html += '<tr>' +
                                '<td>' + p.date + '</td>' +
                                '<td><span class="fw-semibold text-primary font-monospace">' + p.ref + '</span></td>' +
                                '<td>' + p.client + '</td>' +
                                '<td class="text-end fw-semibold text-dark">Rp ' + formatRupiah(p.amount) + '</td>' +
                            '</tr>';
                        });
                        html += '<tr class="table-light fw-bold">' +
                            '<td colspan="3" class="text-end">Total Saldo Held:</td>' +
                            '<td class="text-end text-warning">Rp ' + formatRupiah(total) + '</td>' +
                        '</tr>';
                        previewBody.innerHTML = html;
                    })
                    .catch(function () {
                        previewBody.innerHTML = '<tr><td colspan="4" class="text-center text-danger py-4">Gagal memuat data payment.</td></tr>';
                    });
            });
        });

        btnPreviewGoSettle?.addEventListener('click', function () {
            if (previewModal) previewModal.hide();
            var settleModalEl = document.getElementById('modalCreateSettlement');
            if (settleModalEl) {
                var settleModal = new bootstrap.Modal(settleModalEl);
                settleModal.show();
                if (marketplaceSelect && currentPreviewMarketplaceId) {
                    marketplaceSelect.value = currentPreviewMarketplaceId;
                    marketplaceSelect.dispatchEvent(new Event('change'));
                }
            }
        });

        // 9. Filter Legacy Escrow Payments Table
        var filterLegacyInput = document.getElementById('filterLegacyInput');
        filterLegacyInput?.addEventListener('input', function () {
            var q = (this.value || '').toLowerCase().trim();
            var rows = document.querySelectorAll('.legacy-row');
            rows.forEach(function (row) {
                var client = row.getAttribute('data-client') || '';
                var ref = row.getAttribute('data-ref') || '';
                row.style.display = (!q || client.includes(q) || ref.includes(q)) ? '' : 'none';
            });
        });
    });
</script>
@endpush
