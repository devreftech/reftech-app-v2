@extends('layouts.sales.app')
@section('title', 'Warehouse Transfer - Mutasi Stok Antar Cabang')

@push('after-style')
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.css" />
    <style>
        /* Modern Typography & Polish */
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif !important;
        }

        /* Hero Banner Card */
        .transfer-hero-card {
            background: linear-gradient(135deg, #1e2640 0%, #2a3558 100%);
            border-radius: 16px;
            color: #ffffff;
            box-shadow: 0 10px 30px rgba(30, 38, 64, 0.15);
            position: relative;
            overflow: hidden;
        }
        .transfer-hero-card::after {
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

        /* Route Badges */
        .route-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            border-radius: 30px;
            font-weight: 600;
            font-size: 0.82rem;
            letter-spacing: 0.3px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.03);
            transition: transform 0.2s;
        }
        .route-pill:hover {
            transform: scale(1.03);
        }
        .route-bks-bdg {
            background: linear-gradient(135deg, rgba(105, 108, 255, 0.12) 0%, rgba(105, 108, 255, 0.05) 100%);
            color: #696cff;
            border: 1px solid rgba(105, 108, 255, 0.3);
        }
        .route-bdg-bks {
            background: linear-gradient(135deg, rgba(3, 195, 236, 0.12) 0%, rgba(3, 195, 236, 0.05) 100%);
            color: #03c3ec;
            border: 1px solid rgba(3, 195, 236, 0.3);
        }

        /* Status Badges */
        .badge-pulse {
            display: inline-block;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background-color: currentColor;
            box-shadow: 0 0 0 0 rgba(255, 171, 0, 0.7);
            animation: pulse-ring 1.8s infinite;
        }
        @keyframes pulse-ring {
            0% { box-shadow: 0 0 0 0 rgba(255, 171, 0, 0.7); }
            70% { box-shadow: 0 0 0 8px rgba(255, 171, 0, 0); }
            100% { box-shadow: 0 0 0 0 rgba(255, 171, 0, 0); }
        }

        /* Nav Filter Tabs */
        .custom-filter-nav {
            background: #f8f9fc;
            border-radius: 12px;
            padding: 5px;
            display: inline-flex;
            flex-wrap: wrap;
            gap: 4px;
        }
        .custom-filter-nav .nav-link {
            border-radius: 9px;
            font-weight: 500;
            font-size: 0.85rem;
            color: #566a7f;
            padding: 8px 16px;
            border: none;
            transition: all 0.2s ease;
        }
        .custom-filter-nav .nav-link:hover {
            background: rgba(105, 108, 255, 0.08);
            color: #696cff;
        }
        .custom-filter-nav .nav-link.active {
            background: #ffffff;
            color: #696cff;
            font-weight: 600;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.06);
        }

        /* Quick Item Preview Tag */
        .btn-item-preview {
            background: #f4f5f9;
            border: 1px solid #e2e5ec;
            color: #435971;
            padding: 4px 10px;
            border-radius: 8px;
            font-size: 0.78rem;
            font-weight: 600;
            transition: all 0.2s;
            cursor: pointer;
        }
        .btn-item-preview:hover {
            background: #696cff;
            border-color: #696cff;
            color: #ffffff;
            box-shadow: 0 4px 10px rgba(105, 108, 255, 0.25);
        }

        /* Copy Button */
        .btn-copy-id {
            background: none;
            border: none;
            color: #a1acb8;
            padding: 0 4px;
            transition: color 0.2s;
        }
        .btn-copy-id:hover {
            color: #696cff;
        }

        /* Slide-over Offcanvas Backdrop with Glassmorphism Blur Effect */
        .offcanvas-backdrop {
            transition: opacity 0.3s cubic-bezier(0.16, 1, 0.3, 1), backdrop-filter 0.3s cubic-bezier(0.16, 1, 0.3, 1) !important;
        }
        .offcanvas-backdrop.show {
            backdrop-filter: blur(8px) saturate(160%) !important;
            -webkit-backdrop-filter: blur(8px) saturate(160%) !important;
            background-color: rgba(15, 23, 42, 0.5) !important;
            opacity: 1 !important;
        }

        /* Slide-over Drawer Modern Elevation & Polish */
        #transferDetailOffcanvas {
            box-shadow: -15px 0 45px rgba(15, 23, 42, 0.3) !important;
            border-left: 1px solid rgba(0, 0, 0, 0.08) !important;
            transition: transform 0.35s cubic-bezier(0.16, 1, 0.3, 1) !important;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid flex-grow-1 container-p-y px-4">
        {{-- Flash Notification --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show d-flex align-items-center mb-4 shadow-sm border-0" role="alert">
                <i class="mdi mdi-check-circle-outline me-2 mdi-24px"></i>
                <div class="fw-semibold">{{ session('success') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Hero Header Banner --}}
        <div class="transfer-hero-card p-4 p-md-5 mb-4 position-relative">
            <div class="row align-items-center g-3">
                <div class="col-lg-7 col-md-12">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge bg-label-primary text-white border-0 px-2 py-1 font-11 rounded-pill">
                            <i class="mdi mdi-warehouse me-1"></i> LOGISTIK &amp; INVENTORI
                        </span>
                        <span class="badge bg-label-success text-white border-0 px-2 py-1 font-11 rounded-pill">
                            <span class="badge-pulse me-1 text-success"></span> Sistem Terintegrasi
                        </span>
                    </div>
                    <h3 class="fw-bold text-white mb-2 d-flex align-items-center gap-2">
                        Warehouse Transfer (Mutasi Stok Antar Cabang)
                    </h3>
                    <p class="text-white-50 mb-0 font-14" style="max-width: 600px;">
                        Monitoring alur pengiriman stok sparepart secara real-time antara <strong>Gudang Utama Bekasi (BKS)</strong> dan <strong>Gudang Cabang Bandung (BDG)</strong> untuk akurasi persediaan fisik.
                    </p>
                </div>
                <div class="col-lg-5 col-md-12 text-lg-end">
                    <div class="d-flex flex-wrap align-items-center justify-content-lg-end gap-2">
                        <div class="dropdown">
                            <button class="btn btn-outline-light dropdown-toggle d-flex align-items-center gap-1 waves-effect" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="mdi mdi-export-variant me-1"></i>
                                <span>Export Data</span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow">
                                <li>
                                    <a class="dropdown-item d-flex align-items-center gap-2" href="javascript:void(0)" onclick="window.print()">
                                        <i class="mdi mdi-printer-outline text-muted"></i>
                                        <span>Cetak Dokumen (Print)</span>
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item d-flex align-items-center gap-2" href="javascript:void(0)" id="exportCsvBtn">
                                        <i class="mdi mdi-file-delimited-outline text-muted"></i>
                                        <span>Download CSV / Excel</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <a href="{{ route('change-warehouse.create') }}" class="btn btn-primary btn-lg shadow d-flex align-items-center gap-2 px-4 waves-effect waves-light">
                            <i class="mdi mdi-plus-circle-outline font-20"></i>
                            <span>Buat Transfer Baru</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- 4 Modern Metric KPI Cards --}}
        <div class="row g-3 mb-4">
            <div class="col-xl-3 col-sm-6 col-12">
                <div class="kpi-card shadow-sm h-100 p-3">
                    <div class="kpi-accent-bar bg-primary"></div>
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted font-12 fw-semibold text-uppercase letter-spacing-1">TOTAL MUTASI STOK</span>
                        <div class="p-2 rounded-3 bg-label-primary text-primary">
                            <i class="mdi mdi-swap-horizontal-bold font-20"></i>
                        </div>
                    </div>
                    <div class="d-flex align-items-baseline gap-2">
                        <h2 class="mb-0 fw-bold text-heading">{{ number_format($totalTransfer ?? 0) }}</h2>
                        <span class="text-muted font-12">transaksi</span>
                    </div>
                    <small class="text-muted font-11 d-block mt-2">
                        <i class="mdi mdi-history me-1 text-primary"></i>Histori transfer antar cabang
                    </small>
                </div>
            </div>

            <div class="col-xl-3 col-sm-6 col-12">
                <div class="kpi-card shadow-sm h-100 p-3">
                    <div class="kpi-accent-bar bg-warning"></div>
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted font-12 fw-semibold text-uppercase letter-spacing-1">DALAM PENGIRIMAN</span>
                        <div class="p-2 rounded-3 bg-label-warning text-warning">
                            <i class="mdi mdi-truck-delivery-outline font-20"></i>
                        </div>
                    </div>
                    <div class="d-flex align-items-baseline gap-2">
                        <h2 class="mb-0 fw-bold text-warning">{{ number_format($inTransitCount ?? 0) }}</h2>
                        <span class="text-warning font-12 fw-medium">in-transit</span>
                    </div>
                    <small class="text-warning font-11 d-block mt-2">
                        @if(($inTransitCount ?? 0) > 0)
                            <span class="badge-pulse text-warning me-1"></span><strong>{{ $inTransitCount }} barang</strong> sedang di jalan
                        @else
                            <i class="mdi mdi-check-all me-1"></i>Seluruh transfer telah diterima
                        @endif
                    </small>
                </div>
            </div>

            <div class="col-xl-3 col-sm-6 col-12">
                <div class="kpi-card shadow-sm h-100 p-3">
                    <div class="kpi-accent-bar bg-success"></div>
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted font-12 fw-semibold text-uppercase letter-spacing-1">SELESAI DITERIMA</span>
                        <div class="p-2 rounded-3 bg-label-success text-success">
                            <i class="mdi mdi-shield-check-outline font-20"></i>
                        </div>
                    </div>
                    <div class="d-flex align-items-baseline gap-2">
                        <h2 class="mb-0 fw-bold text-success">{{ number_format($receivedCount ?? 0) }}</h2>
                        <span class="text-success font-12 fw-medium">received</span>
                    </div>
                    <small class="text-success font-11 d-block mt-2">
                        <i class="mdi mdi-check-decagram me-1"></i>Stok diverifikasi di gudang tujuan
                    </small>
                </div>
            </div>

            <div class="col-xl-3 col-sm-6 col-12">
                <div class="kpi-card shadow-sm h-100 p-3">
                    <div class="kpi-accent-bar bg-info"></div>
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted font-12 fw-semibold text-uppercase letter-spacing-1">TOTAL SPAREPART FISIK</span>
                        <div class="p-2 rounded-3 bg-label-info text-info">
                            <i class="mdi mdi-package-variant-closed-check font-20"></i>
                        </div>
                    </div>
                    <div class="d-flex align-items-baseline gap-2">
                        <h2 class="mb-0 fw-bold text-info">{{ number_format($totalQty ?? 0) }}</h2>
                        <span class="text-muted font-12 fw-medium">pcs</span>
                    </div>
                    <small class="text-muted font-11 d-block mt-2">
                        <i class="mdi mdi-layers-outline me-1 text-info"></i>Akumulasi fisik sparepart termutasi
                    </small>
                </div>
            </div>
        </div>

        {{-- Main Table Card with Custom Filters --}}
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header border-bottom py-3 px-3 px-md-4 d-flex flex-wrap align-items-center justify-content-between gap-3">
                {{-- Filter Tabs --}}
                <div class="custom-filter-nav" id="transferTab">
                    <button class="nav-link active" data-filter="all" type="button">
                        <i class="mdi mdi-view-grid-outline me-1"></i> Semua
                        <span class="badge bg-secondary ms-1 rounded-pill font-11">{{ $totalTransfer ?? 0 }}</span>
                    </button>
                    <button class="nav-link" data-filter="transit" type="button">
                        <i class="mdi mdi-truck-fast-outline me-1 text-warning"></i> Dalam Pengiriman
                        @if(($inTransitCount ?? 0) > 0)
                            <span class="badge bg-warning text-white ms-1 rounded-pill font-11">{{ $inTransitCount }}</span>
                        @endif
                    </button>
                    <button class="nav-link" data-filter="received" type="button">
                        <i class="mdi mdi-check-circle-outline me-1 text-success"></i> Selesai
                        <span class="badge bg-label-success ms-1 rounded-pill font-11">{{ $receivedCount ?? 0 }}</span>
                    </button>
                    <button class="nav-link" data-filter="bks-bdg" type="button">
                        <i class="mdi mdi-arrow-right-thin me-1 text-primary"></i> Bekasi ➔ Bandung
                        <span class="badge bg-label-primary ms-1 rounded-pill font-11">{{ $bksToBdgCount ?? 0 }}</span>
                    </button>
                    <button class="nav-link" data-filter="bdg-bks" type="button">
                        <i class="mdi mdi-arrow-left-thin me-1 text-info"></i> Bandung ➔ Bekasi
                        <span class="badge bg-label-info ms-1 rounded-pill font-11">{{ $bdgToBksCount ?? 0 }}</span>
                    </button>
                </div>

                {{-- Live Search Input --}}
                <div class="d-flex align-items-center gap-2">
                    <div class="input-group input-group-merge" style="max-width: 280px;">
                        <span class="input-group-text bg-white border-end-0"><i class="mdi mdi-magnify text-muted"></i></span>
                        <input type="text" id="customSearchInput" class="form-control border-start-0" placeholder="Cari transfer, barang, kurir..." />
                    </div>
                </div>
            </div>

            {{-- Table View --}}
            <div class="card-datatable table-responsive">
                <table class="table table-hover align-middle mb-0" id="warehouseTransferTable" style="width: 100%;">
                    <thead class="table-light">
                        <tr class="font-12 text-uppercase text-muted letter-spacing-1">
                            <th class="text-center" style="width: 40px;">No</th>
                            <th style="min-width: 150px;">Dokumen &amp; Tanggal</th>
                            <th style="min-width: 230px;">Judul / Keperluan</th>
                            <th class="text-center" style="min-width: 150px;">Rute Gudang</th>
                            <th style="min-width: 140px;">Kurir / Ekspedisi</th>
                            <th style="min-width: 160px;">PIC Pengirim &amp; Penerima</th>
                            <th class="text-center" style="min-width: 120px;">Daftar Barang</th>
                            <th class="text-center" style="min-width: 130px;">Status</th>
                            <th class="text-center" style="width: 90px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($transfers as $index => $item)
                            @php
                                $isReceived = ($item->status == 2);
                                $totalItemQty = $item->details->sum('qty');
                                $itemVariantsCount = $item->details->count();
                                $fromCode = strtoupper(trim($item->from ?? 'BDG'));
                                $toCode = strtoupper(trim($item->to ?? 'BKS'));
                                $routeClass = ($fromCode == 'BKS') ? 'route-bks-bdg' : 'route-bdg-bks';
                                $filterType = $isReceived ? 'received' : 'transit';
                                $routeFilter = strtolower($fromCode) . '-' . strtolower($toCode);

                                // JSON serialize items for offcanvas instant preview
                                $itemsJson = $item->details->map(function($d) {
                                    return [
                                        'qty' => $d->qty,
                                        'commodity' => optional(optional($d->replacement)->product)->commodity ?? 'Sparepart #' . $d->id_replacement,
                                        'desc' => optional(optional($d->replacement)->product)->detail_desc ?? '-',
                                        'part_no' => optional($d->replacement)->replacement ?? '-',
                                        'type' => (optional(optional($d->replacement)->product)->go == 'Genuine') ? 'Genuine (G)' : 'Replacement (R)'
                                    ];
                                })->toJson();
                            @endphp
                            <tr data-status-filter="{{ $filterType }}" data-route-filter="{{ $routeFilter }}">
                                <td class="text-center text-muted font-12">
                                    {{ $index + 1 }}
                                </td>
                                <td data-order="{{ strtotime($item->date ?? $item->created_at) }}">
                                    <div class="d-flex flex-column">
                                        <div class="d-flex align-items-center gap-1">
                                            <a href="{{ route('change-warehouse.show', $item->id) }}" class="fw-bold text-primary font-13 text-decoration-none">
                                                #TRF-{{ str_pad($item->id, 4, '0', STR_PAD_LEFT) }}
                                            </a>
                                            <button type="button" class="btn-copy-id" data-copy="#TRF-{{ str_pad($item->id, 4, '0', STR_PAD_LEFT) }}" title="Salin ID">
                                                <i class="mdi mdi-content-copy font-13"></i>
                                            </button>
                                        </div>
                                        <small class="text-muted font-11 d-flex align-items-center gap-1 mt-1">
                                            <i class="mdi mdi-calendar-blank-outline mdi-12px"></i>
                                            Kirim: {{ $item->date ? \Carbon\Carbon::parse($item->date)->translatedFormat('d M Y') : '-' }}
                                        </small>
                                        @if($isReceived && $item->date_recieve)
                                            <small class="text-success font-11 d-flex align-items-center gap-1" title="Tanggal Diterima di Gudang Tujuan">
                                                <i class="mdi mdi-check-all mdi-12px"></i>
                                                Tiba: {{ \Carbon\Carbon::parse($item->date_recieve)->translatedFormat('d M Y') }}
                                            </small>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <a href="{{ route('change-warehouse.show', $item->id) }}" class="text-heading fw-semibold font-13 text-truncate text-decoration-none" style="max-width: 280px;" title="{{ $item->title }}">
                                            {{ $item->title ?? 'Perpindahan Stok Gudang' }}
                                        </a>
                                        @if($item->note)
                                            <small class="text-muted font-11 text-truncate mt-1" style="max-width: 280px;" title="{{ $item->note }}">
                                                <i class="mdi mdi-text-box-outline me-1"></i>{{ $item->note }}
                                            </small>
                                        @endif
                                        @if($isReceived && $item->note_recieve)
                                            <small class="text-success font-10 text-truncate mt-1" style="max-width: 280px;" title="Catatan Penerimaan: {{ $item->note_recieve }}">
                                                <i class="mdi mdi-message-check-outline me-1"></i>{{ $item->note_recieve }}
                                            </small>
                                        @endif
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="route-pill {{ $routeClass }}" title="Dari Gudang {{ $fromCode == 'BKS' ? 'Bekasi' : 'Bandung' }} ke Gudang {{ $toCode == 'BKS' ? 'Bekasi' : 'Bandung' }}">
                                        <i class="mdi mdi-home-variant-outline"></i>
                                        <span>{{ $fromCode }}</span>
                                        <i class="mdi mdi-arrow-right-thin font-16"></i>
                                        <span>{{ $toCode }}</span>
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="p-1 rounded-2 bg-light text-muted">
                                            <i class="mdi mdi-truck-fast-outline font-14"></i>
                                        </div>
                                        <span class="font-12 fw-medium text-heading">
                                            {{ $item->kurir ?? 'Internal Staff' }}
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex flex-column font-12 gap-1">
                                        <div class="d-flex align-items-center gap-1 text-heading">
                                            <span class="badge bg-label-primary rounded-pill p-1 font-10" title="Gudang Pengirim">OUT</span>
                                            <span class="fw-medium text-truncate" style="max-width: 140px;">{{ optional($item->sender)->name ?? 'Gudang Pengirim' }}</span>
                                        </div>
                                        <div class="d-flex align-items-center gap-1 text-muted font-11">
                                            <span class="badge bg-label-success rounded-pill p-1 font-10" title="Gudang Penerima">IN</span>
                                            <span class="text-truncate" style="max-width: 140px;">{{ optional($item->reciever)->name ?? ($isReceived ? 'Penerima' : 'Menunggu...') }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <script type="application/json" id="transfer-items-{{ $item->id }}">{!! $itemsJson !!}</script>
                                    <button type="button" class="btn-item-preview d-inline-flex align-items-center gap-1 btn-open-drawer"
                                        data-id="{{ $item->id }}"
                                        data-trf="#TRF-{{ str_pad($item->id, 4, '0', STR_PAD_LEFT) }}"
                                        data-title="{{ $item->title }}"
                                        data-from="{{ $fromCode }}"
                                        data-to="{{ $toCode }}"
                                        data-sender="{{ optional($item->sender)->name ?? '-' }}"
                                        data-receiver="{{ optional($item->reciever)->name ?? ($isReceived ? 'Penerima' : 'Menunggu konfirmasi') }}"
                                        data-kurir="{{ $item->kurir ?? 'Internal' }}"
                                        data-date="{{ $item->date ? \Carbon\Carbon::parse($item->date)->translatedFormat('d M Y') : '-' }}"
                                        data-date-receive="{{ $item->date_recieve ? \Carbon\Carbon::parse($item->date_recieve)->translatedFormat('d M Y') : '-' }}"
                                        data-status="{{ $item->status }}"
                                        data-note="{{ $item->note ?? '-' }}"
                                        data-note-receive="{{ $item->note_recieve ?? '-' }}"
                                        data-items="{{ $itemsJson }}"
                                        title="Klik untuk melihat rincian barang">
                                        <i class="mdi mdi-package-variant font-13"></i>
                                        <span>{{ number_format($totalItemQty) }} pcs</span>
                                        <span class="badge bg-primary rounded-pill text-white font-10 ms-1">{{ $itemVariantsCount }}</span>
                                    </button>
                                </td>
                                <td class="text-center">
                                    @if($isReceived)
                                        <span class="badge bg-label-success rounded-pill font-11 px-3 py-2 d-inline-flex align-items-center gap-1 shadow-xs">
                                            <i class="mdi mdi-check-circle-outline font-13"></i> Selesai
                                        </span>
                                    @else
                                        <span class="badge bg-label-warning rounded-pill font-11 px-3 py-2 d-inline-flex align-items-center gap-1 shadow-xs">
                                            <span class="badge-pulse text-warning"></span> In-Transit
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="d-flex align-items-center justify-content-center gap-1">
                                        <a href="{{ route('change-warehouse.show', $item->id) }}" class="btn btn-sm btn-icon btn-label-primary waves-effect" data-bs-toggle="tooltip" title="Lihat Surat Jalan &amp; Detail">
                                            <i class="mdi mdi-file-document-outline font-15"></i>
                                        </a>

                                        @if(!$isReceived)
                                            <button type="button" class="btn btn-sm btn-icon btn-label-success btn-accept-transfer waves-effect" 
                                                data-id="{{ $item->id }}" 
                                                data-title="{{ $item->title }}"
                                                data-route="{{ $fromCode }} ➔ {{ $toCode }}"
                                                data-bs-toggle="tooltip" 
                                                title="Konfirmasi Terima Barang">
                                                <i class="mdi mdi-check font-16"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center justify-content-center">
                                        <div class="bg-label-primary p-3 rounded-circle mb-3">
                                            <i class="mdi mdi-swap-horizontal mdi-36px text-primary"></i>
                                        </div>
                                        <h6 class="fw-bold mb-1">Belum Ada Riwayat Transfer Antar Gudang</h6>
                                        <p class="text-muted font-12 mb-3">Mulai pengiriman mutasi stok pertama antara cabang Bekasi dan Bandung.</p>
                                        <a href="{{ route('change-warehouse.create') }}" class="btn btn-sm btn-primary">
                                            <i class="mdi mdi-plus me-1"></i> Buat Transfer Baru
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Sliding Quick Item Preview Drawer (Offcanvas) --}}
    <div class="offcanvas offcanvas-end shadow-lg" tabindex="-1" id="transferDetailOffcanvas" aria-labelledby="transferDetailOffcanvasLabel" style="width: 500px; max-width: 90vw;">
        <div class="offcanvas-header bg-label-primary border-bottom py-3">
            <div>
                <span class="badge bg-primary text-white mb-1 font-11 rounded-pill" id="drawerTrfId">#TRF-0000</span>
                <h5 class="offcanvas-title fw-bold text-heading" id="transferDetailOffcanvasLabel">Rincian Dokumen Transfer</h5>
            </div>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body p-4">
            {{-- Status & Route Card --}}
            <div class="card border mb-3 bg-light">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted font-11 fw-bold text-uppercase">Rute Perpindahan</span>
                        <div id="drawerStatusBadge"></div>
                    </div>
                    <div class="d-flex align-items-center justify-content-center py-2 bg-white rounded-3 border mb-3">
                        <div class="text-center px-3">
                            <span class="font-11 text-muted d-block">Gudang Asal</span>
                            <span class="fw-bold font-16 text-primary" id="drawerFrom">-</span>
                        </div>
                        <i class="mdi mdi-arrow-right-bold text-muted font-20 px-2"></i>
                        <div class="text-center px-3">
                            <span class="font-11 text-muted d-block">Gudang Tujuan</span>
                            <span class="fw-bold font-16 text-success" id="drawerTo">-</span>
                        </div>
                    </div>

                    <div class="row g-2 font-12">
                        <div class="col-6">
                            <span class="text-muted d-block font-11">Pengirim:</span>
                            <strong class="text-heading" id="drawerSender">-</strong>
                        </div>
                        <div class="col-6">
                            <span class="text-muted d-block font-11">Penerima:</span>
                            <strong class="text-heading" id="drawerReceiver">-</strong>
                        </div>
                        <div class="col-6">
                            <span class="text-muted d-block font-11">Ekspedisi / Kurir:</span>
                            <strong class="text-heading" id="drawerKurir">-</strong>
                        </div>
                        <div class="col-6">
                            <span class="text-muted d-block font-11">Tgl Kirim:</span>
                            <strong class="text-heading" id="drawerDate">-</strong>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Notes --}}
            <div class="mb-3">
                <label class="font-11 text-muted text-uppercase fw-bold d-block mb-1">Judul / Keperluan</label>
                <div class="p-2 rounded-2 bg-white border font-12 text-heading" id="drawerTitle">-</div>
            </div>

            {{-- Sparepart Items Table --}}
            <div class="mb-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <label class="font-11 text-muted text-uppercase fw-bold mb-0">Daftar Sparepart (<span id="drawerItemCount">0</span> jenis)</label>
                    <span class="badge bg-label-info font-11" id="drawerTotalPcs">0 pcs</span>
                </div>
                <div class="table-responsive border rounded-3 bg-white">
                    <table class="table table-sm table-hover mb-0">
                        <thead class="table-light">
                            <tr class="font-11">
                                <th style="width: 30px;">#</th>
                                <th>Deskripsi Sparepart</th>
                                <th class="text-end" style="width: 80px;">Kuantitas</th>
                            </tr>
                        </thead>
                        <tbody id="drawerItemsTbody" class="font-12">
                            {{-- Loaded dynamically --}}
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Note Receive --}}
            <div class="mb-3" id="drawerReceiveNoteBox" style="display: none;">
                <label class="font-11 text-success text-uppercase fw-bold d-block mb-1">Catatan Penerimaan Gudang</label>
                <div class="p-2 rounded-2 bg-label-success border font-12" id="drawerReceiveNote">-</div>
            </div>
        </div>
        <div class="offcanvas-footer border-top p-3 d-flex align-items-center justify-content-between gap-2">
            <a href="#" id="drawerFullDetailBtn" class="btn btn-outline-primary d-flex align-items-center gap-1">
                <i class="mdi mdi-open-in-new me-1"></i> Buka Surat Jalan
            </a>
            <div id="drawerActionSlot"></div>
        </div>
    </div>

    {{-- Modal Terima Transfer Gudang --}}
    <div class="modal fade" id="acceptTransferModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <form id="acceptTransferForm" method="POST" action="">
                    @csrf
                    <div class="modal-header bg-label-success pb-3 border-bottom">
                        <h5 class="modal-title fw-bold text-success d-flex align-items-center gap-2">
                            <i class="mdi mdi-check-decagram-outline font-22"></i>
                            Konfirmasi Penerimaan Transfer Gudang
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body pt-4">
                        <div class="alert alert-warning py-2 mb-3 font-12 d-flex align-items-center gap-2">
                            <i class="mdi mdi-information-outline font-18 text-warning"></i>
                            <div>Stok barang akan otomatis dipindahkan ke inventori gudang tujuan begitu konfirmasi disetujui.</div>
                        </div>

                        <div class="mb-3 p-3 bg-light rounded-3 border">
                            <label class="form-label font-11 text-muted text-uppercase fw-bold d-block mb-1">Dokumen Transfer</label>
                            <h6 class="fw-bold text-heading font-14 mb-1" id="modalTransferTitle">-</h6>
                            <span class="badge bg-label-primary font-12" id="modalTransferRoute">-</span>
                        </div>

                        <div class="mb-3">
                            <label for="modalAcceptNote" class="form-label fw-semibold font-13">Catatan Penerimaan &amp; Kondisi Fisik</label>
                            <textarea class="form-control" id="modalAcceptNote" name="note" rows="3" placeholder="Contoh: Barang telah tiba lengkap, fisik tersegel baik, tidak ada cacat..."></textarea>
                            <small class="text-muted font-11">Opsional: Tambahkan catatan jika ada selisih atau kondisi khusus.</small>
                        </div>

                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="confirmCheck" required checked>
                            <label class="form-check-label font-12 text-muted" for="confirmCheck">
                                Saya menyatakan barang telah diverifikasi secara fisik di gudang tujuan.
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer border-top pt-3">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success d-flex align-items-center gap-1 px-4">
                            <i class="mdi mdi-check me-1"></i> Konfirmasi Terima Barang
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('after-script')
    <script src="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.js"></script>
@endpush

@push('page-script')
    <script>
        $(document).ready(function() {
            // Inisialisasi Tooltip
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Inisialisasi DataTable Modern
            var table = $('#warehouseTransferTable').DataTable({
                order: [[1, 'desc']],
                pageLength: 15,
                lengthMenu: [10, 15, 25, 50, 100],
                language: {
                    search: "",
                    searchPlaceholder: "Cari nomor, judul, ekspedisi...",
                    lengthMenu: "Tampilkan _MENU_ data",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ transfer",
                    infoEmpty: "Tidak ada data transfer gudang",
                    paginate: {
                        first: '<i class="mdi mdi-chevron-double-left"></i>',
                        previous: '<i class="mdi mdi-chevron-left"></i>',
                        next: '<i class="mdi mdi-chevron-right"></i>',
                        last: '<i class="mdi mdi-chevron-double-right"></i>'
                    },
                    emptyTable: "Belum ada transaksi transfer antar gudang."
                },
                dom: 't<"card-footer d-flex flex-wrap justify-content-between align-items-center p-3 gap-2 border-top"<"text-muted font-12"i><"pagination-wrapper"p>>'
            });

            // Hubungkan custom search input
            $('#customSearchInput').on('keyup', function() {
                table.search(this.value).draw();
            });

            // Custom Filter Tabs
            $('#transferTab button').on('click', function(e) {
                e.preventDefault();
                $('#transferTab button').removeClass('active');
                $(this).addClass('active');

                var filter = $(this).data('filter');

                $.fn.dataTable.ext.search = [];

                if (filter === 'transit') {
                    $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                        var rowNode = table.row(dataIndex).node();
                        return $(rowNode).data('status-filter') === 'transit';
                    });
                } else if (filter === 'received') {
                    $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                        var rowNode = table.row(dataIndex).node();
                        return $(rowNode).data('status-filter') === 'received';
                    });
                } else if (filter === 'bks-bdg') {
                    $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                        var rowNode = table.row(dataIndex).node();
                        return $(rowNode).data('route-filter') === 'bks-bdg';
                    });
                } else if (filter === 'bdg-bks') {
                    $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                        var rowNode = table.row(dataIndex).node();
                        return $(rowNode).data('route-filter') === 'bdg-bks';
                    });
                }

                table.draw();
            });

            // Modal Konfirmasi Terima Barang
            $(document).on('click', '.btn-accept-transfer', function() {
                var id = $(this).data('id');
                var title = $(this).data('title');
                var route = $(this).data('route');

                var actionUrl = "{{ url('change-warehouse/accept') }}/" + id;
                $('#acceptTransferForm').attr('action', actionUrl);
                $('#modalTransferTitle').text(title);
                $('#modalTransferRoute').text(route);
                $('#modalAcceptNote').val('');

                var modal = new bootstrap.Modal(document.getElementById('acceptTransferModal'));
                modal.show();
            });

            // Salin Nomor Transfer ke Clipboard
            $(document).on('click', '.btn-copy-id', function() {
                var text = $(this).data('copy');
                navigator.clipboard.writeText(text).then(function() {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: 'Tersalin: ' + text,
                        showConfirmButton: false,
                        timer: 1500
                    });
                });
            });

            // Buka Drawer Quick Item Preview
            $(document).on('click', '.btn-open-drawer', function() {
                var data = $(this).data();
                $('#drawerTrfId').text(data.trf);
                $('#drawerTitle').text(data.title);
                $('#drawerFrom').text(data.from == 'BKS' ? 'Bekasi (BKS)' : 'Bandung (BDG)');
                $('#drawerTo').text(data.to == 'BKS' ? 'Bekasi (BKS)' : 'Bandung (BDG)');
                $('#drawerSender').text(data.sender);
                $('#drawerReceiver').text(data.receiver);
                $('#drawerKurir').text(data.kurir);
                $('#drawerDate').text(data.date);
                $('#drawerFullDetailBtn').attr('href', "{{ url('change-warehouse') }}/" + data.id);

                if (data.status == 2) {
                    $('#drawerStatusBadge').html('<span class="badge bg-label-success rounded-pill px-3 py-1 font-11"><i class="mdi mdi-check-circle-outline me-1"></i>Selesai Diterima</span>');
                    if (data.noteReceive && data.noteReceive !== '-') {
                        $('#drawerReceiveNote').text(data.noteReceive);
                        $('#drawerReceiveNoteBox').show();
                    } else {
                        $('#drawerReceiveNoteBox').hide();
                    }
                    $('#drawerActionSlot').empty();
                } else {
                    $('#drawerStatusBadge').html('<span class="badge bg-label-warning rounded-pill px-3 py-1 font-11"><span class="badge-pulse text-warning me-1"></span>Dalam Pengiriman</span>');
                    $('#drawerReceiveNoteBox').hide();
                    $('#drawerActionSlot').html('<button type="button" class="btn btn-success btn-accept-transfer d-flex align-items-center gap-1" data-id="' + data.id + '" data-title="' + data.title + '" data-route="' + data.from + ' ➔ ' + data.to + '"><i class="mdi mdi-check me-1"></i> Terima Barang</button>');
                }

                // Render Item Table with multi-source fallback
                var items = [];
                var itemsScript = document.getElementById('transfer-items-' + data.id);
                if (itemsScript && itemsScript.textContent) {
                    try {
                        items = JSON.parse(itemsScript.textContent);
                    } catch(e1) {
                        items = [];
                    }
                }

                if ((!items || items.length === 0) && data.items) {
                    if (Array.isArray(data.items)) {
                        items = data.items;
                    } else if (typeof data.items === 'object') {
                        items = Object.values(data.items);
                    } else if (typeof data.items === 'string') {
                        try {
                            items = JSON.parse(data.items);
                        } catch(e2) {
                            try {
                                var decoded = $('<div>').html(data.items).text();
                                items = JSON.parse(decoded);
                            } catch(e3) {
                                items = [];
                            }
                        }
                    }
                }

                var tbodyHtml = '';
                var totalPcs = 0;
                if (items && items.length > 0) {
                    items.forEach(function(item, idx) {
                        var qtyNum = parseInt(item.qty || 0);
                        totalPcs += qtyNum;
                        tbodyHtml += '<tr>' +
                            '<td class="text-muted font-11 text-center">' + (idx + 1) + '</td>' +
                            '<td>' +
                                '<div class="fw-semibold text-heading font-12">' + (item.commodity || 'Item Sparepart') + '</div>' +
                                '<small class="text-muted font-10 d-block">Part: <span class="font-monospace text-dark fw-semibold">' + (item.part_no || '-') + '</span> (' + (item.desc || '-') + ')</small>' +
                                '<span class="badge bg-label-secondary font-10 mt-1">' + (item.type || 'Sparepart') + '</span>' +
                            '</td>' +
                            '<td class="text-end align-top">' +
                                '<span class="fw-bold font-13 text-primary">' + qtyNum.toLocaleString() + '</span> <span class="font-11 text-muted">pcs</span>' +
                            '</td>' +
                        '</tr>';
                    });
                } else {
                    tbodyHtml = '<tr><td colspan="3" class="text-center py-4 text-muted font-12"><i class="mdi mdi-alert-circle-outline me-1"></i> Tidak ada rincian item tercatat dalam transfer ini.</td></tr>';
                }

                $('#drawerItemCount').text(items ? items.length : 0);
                $('#drawerTotalPcs').text(totalPcs.toLocaleString() + ' pcs');
                $('#drawerItemsTbody').html(tbodyHtml);

                var offcanvasEl = document.getElementById('transferDetailOffcanvas');
                var bsOffcanvas = bootstrap.Offcanvas.getOrCreateInstance(offcanvasEl);
                bsOffcanvas.show();
            });

            // Export CSV button handler
            $('#exportCsvBtn').on('click', function() {
                var csv = [];
                var rows = document.querySelectorAll("#warehouseTransferTable tr");
                
                for (var i = 0; i < rows.length; i++) {
                    var row = [], cols = rows[i].querySelectorAll("td, th");
                    for (var j = 0; j < cols.length - 1; j++) {
                        var text = cols[j].innerText.replace(/(\r\n|\n|\r)/gm, " ").trim();
                        text = text.replace(/"/g, '""');
                        row.push('"' + text + '"');
                    }
                    csv.push(row.join(","));
                }
                
                var csvFile = new Blob([csv.join("\n")], {type: "text/csv;charset=utf-8;"});
                var downloadLink = document.createElement("a");
                downloadLink.download = "warehouse_transfers_" + new Date().toISOString().slice(0,10) + ".csv";
                downloadLink.href = window.URL.createObjectURL(csvFile);
                downloadLink.style.display = "none";
                document.body.appendChild(downloadLink);
                downloadLink.click();
                document.body.removeChild(downloadLink);
            });
        });
    </script>
@endpush
