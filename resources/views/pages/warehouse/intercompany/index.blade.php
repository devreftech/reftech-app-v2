@extends('layouts.sales.app')
@section('title', 'Rekap Barang Keluar Kojisha (Intercompany PO ke Reftech)')

@section('content')
<div class="container-fluid flex-grow-1 p-0">
    {{-- Breadcrumb & Title --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-style1 mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('product-out.index') }}" class="text-muted">Warehouse</a></li>
                    <li class="breadcrumb-item active text-primary fw-semibold">Rekap BK Kojisha (Intercompany)</li>
                </ol>
            </nav>
            <div class="d-flex align-items-center gap-2">
                <h4 class="fw-bold mb-0 text-dark">
                    <i class="mdi mdi-swap-horizontal text-danger me-2"></i>Rekap Barang Keluar Kojisha
                </h4>
                <span class="badge bg-danger text-white px-3 py-1 fw-semibold">PT. Kojisha Innotiv Indonesia</span>
            </div>
            <p class="text-muted small mb-0 mt-1">
                Catatan pengeluaran barang spare part penjualan Kojisha dari stok Reftech untuk pembuatan PO Intercompany bulanan.
            </p>
        </div>

        {{-- Clean & Modern Filter Toolbar matching /reports --}}
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <form action="{{ route('intercompany.index') }}" method="GET" class="d-flex align-items-center flex-wrap mb-0 gap-2" id="filterPeriodForm">
                <input type="hidden" name="status" id="formStatusInput" value="{{ $selectedStatus }}">

                <!-- Segmented Date Navigator Pill Group -->
                <div class="d-inline-flex align-items-center bg-body-tertiary border rounded-pill p-1">
                    <!-- Prev Month Button -->
                    <a href="{{ route('intercompany.index', ['month' => $prevMonth, 'year' => $prevYear, 'status' => $selectedStatus]) }}" 
                       class="btn btn-icon btn-sm btn-label-secondary rounded-circle" 
                       style="width: 28px; height: 28px;"
                       data-bs-toggle="tooltip" title="Bulan Sebelumnya ({{ $monthNames[$prevMonth] ?? $prevMonth }} {{ $prevYear }})">
                        <i class="mdi mdi-chevron-left fs-5"></i>
                    </a>

                    <!-- Month Select -->
                    <select name="month" class="form-select form-select-sm border-0 bg-transparent fw-semibold text-dark text-center px-1" style="min-width: 110px; font-size: 0.82rem; cursor: pointer;" onchange="document.getElementById('filterPeriodForm').submit()">
                        @foreach ($monthNames as $mNum => $mLabel)
                            <option value="{{ $mNum }}" {{ $mNum == $selectedMonth ? 'selected' : '' }}>
                                {{ $mLabel }}
                            </option>
                        @endforeach
                    </select>

                    <span class="text-muted opacity-25">|</span>

                    <!-- Year Select -->
                    <select name="year" class="form-select form-select-sm border-0 bg-transparent fw-semibold text-dark text-center px-1" style="min-width: 78px; font-size: 0.82rem; cursor: pointer;" onchange="document.getElementById('filterPeriodForm').submit()">
                        @foreach ($availableYears as $y)
                            <option value="{{ $y }}" {{ $y == $selectedYear ? 'selected' : '' }}>
                                {{ $y }}
                            </option>
                        @endforeach
                    </select>

                    <!-- Next Month Button -->
                    <a href="{{ route('intercompany.index', ['month' => $nextMonth, 'year' => $nextYear, 'status' => $selectedStatus]) }}" 
                       class="btn btn-icon btn-sm btn-label-secondary rounded-circle" 
                       style="width: 28px; height: 28px;"
                       data-bs-toggle="tooltip" title="Bulan Berikutnya ({{ $monthNames[$nextMonth] ?? $nextMonth }} {{ $nextYear }})">
                        <i class="mdi mdi-chevron-right fs-5"></i>
                    </a>
                </div>

                <!-- Return to Current Month Button -->
                @if ($selectedMonth != now()->month || $selectedYear != now()->year)
                    <a href="{{ route('intercompany.index', ['month' => now()->month, 'year' => now()->year, 'status' => $selectedStatus]) }}" 
                       class="btn btn-sm btn-label-primary rounded-pill px-3 fw-semibold" 
                       data-bs-toggle="tooltip" title="Kembali ke Bulan Sekarang">
                        <i class="mdi mdi-calendar-today me-1"></i> Bulan Ini
                    </a>
                @endif

                <a href="{{ route('intercompany.print', ['month' => $selectedMonth, 'year' => $selectedYear]) }}" target="_blank" class="btn btn-sm btn-label-secondary rounded-pill px-3 fw-semibold">
                    <i class="mdi mdi-printer-outline me-1"></i> Cetak Rekap
                </a>
            </form>
        </div>
    </div>

    {{-- Flash Message Alerts --}}
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center mb-4" role="alert">
            <i class="mdi mdi-alert-circle-outline fs-4 me-2"></i>
            <div>{{ session('error') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('success') || session('message'))
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center mb-4" role="alert">
            <i class="mdi mdi-check-circle-outline fs-4 me-2"></i>
            <div>{{ session('success') ?: session('message') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Metrics KPI Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted d-block small mb-1">Total Transaksi BK Kojisha</span>
                        <h5 class="mb-0 fw-bold text-dark">{{ $totalTransactions }} Dokumen BK</h5>
                        <span class="badge bg-label-danger rounded-pill mt-1" style="font-size: 10.5px;">
                            Periode {{ $monthNames[$selectedMonth] }} {{ $selectedYear }}
                        </span>
                    </div>
                    <div class="avatar avatar-md bg-label-danger rounded-3 d-flex align-items-center justify-content-center">
                        <i class="mdi mdi-file-document-multiple-outline fs-3"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted d-block small mb-1">Total Qty Spare Part Keluar</span>
                        <h5 class="mb-0 fw-bold text-dark">{{ number_format($totalQty, 0, '', '.') }} <span class="text-muted fs-6 fw-normal">Item Pcs</span></h5>
                        <span class="badge bg-label-info rounded-pill mt-1" style="font-size: 10.5px;">
                            Dari Stok Reftech (BDG/BKS)
                        </span>
                    </div>
                    <div class="avatar avatar-md bg-label-info rounded-3 d-flex align-items-center justify-content-center">
                        <i class="mdi mdi-package-variant-closed fs-3"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-12 col-xl-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted d-block small mb-1">Total Estimasi Nilai Modal (HPP)</span>
                        <h5 class="mb-0 fw-bold text-success">Rp {{ number_format($totalEstimatedHpp, 0, '', '.') }}</h5>
                        <span class="badge bg-label-success rounded-pill mt-1" style="font-size: 10.5px;">
                            Estimasi Nilai PO ke Reftech
                        </span>
                    </div>
                    <div class="avatar avatar-md bg-label-success rounded-3 d-flex align-items-center justify-content-center">
                        <i class="mdi mdi-cash-multiple fs-3"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Status Navigation Tabs (Instant Switch without Reload) --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-2 d-flex flex-wrap align-items-center justify-content-between gap-2">
            <ul class="nav nav-pills gap-1" role="tablist" id="intercompanyStatusTabs">
                <li class="nav-item">
                    <button type="button" class="nav-link tab-filter-btn {{ $selectedStatus == 'pending' ? 'active' : '' }} d-flex align-items-center gap-2 py-2 px-3 fw-semibold" 
                            data-status="pending">
                        <i class="mdi mdi-clock-outline"></i>
                        <span>Belum Dibuat PO</span>
                        <span class="badge {{ $selectedStatus == 'pending' ? 'bg-white text-primary' : 'bg-label-warning' }} rounded-pill tab-badge" id="badgePendingCount">{{ $pendingCount }}</span>
                    </button>
                </li>
                <li class="nav-item">
                    <button type="button" class="nav-link tab-filter-btn {{ $selectedStatus == 'done' ? 'active' : '' }} d-flex align-items-center gap-2 py-2 px-3 fw-semibold" 
                            data-status="done">
                        <i class="mdi mdi-check-circle-outline"></i>
                        <span>Sudah Dibuat PO</span>
                        <span class="badge {{ $selectedStatus == 'done' ? 'bg-white text-success' : 'bg-label-success' }} rounded-pill tab-badge" id="badgeDoneCount">{{ $doneCount }}</span>
                    </button>
                </li>
                <li class="nav-item">
                    <button type="button" class="nav-link tab-filter-btn {{ $selectedStatus == 'all' ? 'active' : '' }} d-flex align-items-center gap-2 py-2 px-3 fw-semibold" 
                            data-status="all">
                        <i class="mdi mdi-format-list-bulleted"></i>
                        <span>Semua Item</span>
                        <span class="badge {{ $selectedStatus == 'all' ? 'bg-white text-secondary' : 'bg-label-secondary' }} rounded-pill tab-badge" id="badgeAllCount">{{ $allCount }}</span>
                    </button>
                </li>
                <li class="nav-item">
                    <button type="button" class="nav-link tab-filter-btn {{ $selectedStatus == 'release' ? 'active' : '' }} d-flex align-items-center gap-2 py-2 px-3 fw-semibold" 
                            data-status="release">
                        <i class="mdi mdi-file-document-check-outline"></i>
                        <span>PO Release</span>
                        <span class="badge {{ $selectedStatus == 'release' ? 'bg-white text-info' : 'bg-label-info' }} rounded-pill tab-badge" id="badgeReleaseCount">{{ $poReleaseCount }}</span>
                    </button>
                </li>
            </ul>

            <div class="text-muted small px-2" id="tabDescription">
                @if($selectedStatus == 'pending')
                    <i class="mdi mdi-information-outline text-primary me-1"></i>
                    Menampilkan item barang keluar Kojisha yang <b>belum diproses ke Draft PO</b>.
                @elseif($selectedStatus == 'done')
                    <i class="mdi mdi-check-all text-success me-1"></i>
                    Menampilkan item yang <b>sudah diterbitkan PO</b> ke Reftech. Anda dapat membatalkan PO jika diperlukan.
                @elseif($selectedStatus == 'release')
                    <i class="mdi mdi-file-document-check-outline text-info me-1"></i>
                    Menampilkan rekapan dokumen <b>Purchase Order (PO) Kojisha ke Reftech</b> yang telah diterbitkan pada periode ini.
                @else
                    <i class="mdi mdi-layers-outline text-secondary me-1"></i>
                    Menampilkan <b>seluruh</b> riwayat pengeluaran barang spare part Kojisha.
                @endif
            </div>
        </div>
    </div>

    {{-- Main Table & Draft PO Builder Form (Items View) --}}
    <form action="{{ route('intercompany.draft-po') }}" method="POST" id="draftPoForm" style="display: {{ $selectedStatus == 'release' ? 'none' : '' }};">
        @csrf
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-bottom py-3 d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div class="d-flex align-items-center gap-3">
                    <h6 class="mb-0 fw-bold text-dark" id="tableHeaderTitle">
                        <i class="mdi mdi-format-list-bulleted me-1 text-primary"></i> 
                        <span id="tableHeaderTitleText">Item Siap Dibuatkan PO Intercompany</span>
                    </h6>
                    <span class="badge bg-label-primary rounded-pill px-2.5 py-1" id="visibleRowCountBadge">{{ $pendingCount }} Baris Item</span>
                </div>
                <div class="d-flex align-items-center gap-2" id="headerActionButtons">
                    <button type="button" class="btn btn-sm btn-outline-primary waves-effect" id="btnSelectAll">
                        <i class="mdi mdi-checkbox-multiple-marked-outline me-1"></i> Pilih Semua
                    </button>
                    <button type="button" class="btn btn-sm btn-danger waves-effect fw-semibold" id="btnOpenModalPo">
                        <i class="mdi mdi-cart-arrow-right me-1"></i> Buat Draft PO ke Reftech
                    </button>
                </div>
            </div>

            <div class="table-responsive text-nowrap">
                <table class="table table-hover align-middle mb-0" id="intercompanyTable">
                    <thead class="table-light">
                        <tr class="text-uppercase" style="font-size: 11px; letter-spacing: 0.5px;">
                            <th class="text-center py-3" style="width: 40px;" id="thCheckAllCol">
                                <input type="checkbox" class="form-check-input" id="checkAllHeader" checked>
                            </th>
                            <th class="py-3" style="min-width: 160px;">No. BK &amp; Tanggal</th>
                            <th class="py-3" style="min-width: 130px;">Ref. Invoice / PO</th>
                            <th class="py-3" style="min-width: 180px;">Customer Penerima (Tujuan)</th>
                            <th class="text-center py-3" style="width: 70px;">Gudang</th>
                            <th class="py-3" style="min-width: 200px;">Deskripsi Spare Part</th>
                            <th class="text-center py-3" style="width: 60px;">Qty</th>
                            <th class="text-end py-3" style="min-width: 140px;">Harga Satuan (HPP/Manual)</th>
                            <th class="text-end py-3" style="min-width: 140px;">Harga Jual (Rp)</th>
                            <th class="text-center py-3 pe-3" style="min-width: 160px;">Status PO Intercompany</th>
                        </tr>
                    </thead>
                    <tbody id="intercompanyTableBody">
                        @forelse($items as $idx => $item)
                            @php
                                $bk = $item->productOut;
                                $hasPo = !empty($item->id_purchase_order) && !empty($item->purchaseOrder);
                                $po = $item->purchaseOrder;
                                $partNumber = $item->serialProduct?->pn ?? ($item->detailProduct?->product?->part_number ?? ($item->detailProduct?->replacement ?? '-'));
                                $brand = $item->serialProduct?->brand ?? ($item->detailProduct?->product?->brand ?? '');
                                $desc = $item->detailProduct?->product?->description ?? ($item->detailProduct?->replacement ?? '-');
                                $unitHpp = (float) ($item->detailProduct?->modal ?? ($item->detailProduct?->hpp ?? 0));
                                $qty = (int) ($item->qty ?? 1);
                                $rowSubtotal = $unitHpp * $qty;
                                $unitSellingPrice = (float) ($item->price ?? 0);
                                $totalSellingPrice = (float) ($item->amount ?? ($unitSellingPrice * $qty));
                            @endphp
                            <tr class="item-row {{ $hasPo ? 'table-light-subtle is-done' : 'is-pending' }}" 
                                data-id="{{ $item->id }}" 
                                data-has-po="{{ $hasPo ? '1' : '0' }}"
                                data-bk="{{ $bk->no_product_out ?: 'BK #' . $bk->id }}" 
                                data-part="{{ ($brand ? $brand . ' - ' : '') . $partNumber }}" 
                                data-desc="{{ $desc }}" 
                                data-client="{{ trim($bk->detail_client) ?: '-' }}">
                                <td class="text-center row-check-cell">
                                    @if($hasPo)
                                        <input type="checkbox" class="form-check-input" disabled title="Item ini sudah dibuatkan PO: {{ $po->no_po }}">
                                    @else
                                        <input type="checkbox" name="selected_items[]" value="{{ $item->id }}" class="form-check-input row-checkbox" checked>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('product-out.show', $bk->id) }}" class="fw-bold text-primary d-block" target="_blank">
                                        {{ $bk->no_product_out ?: 'BK #' . $bk->id }}
                                    </a>
                                    <small class="text-muted">
                                        <i class="mdi mdi-calendar-blank-outline me-1"></i>{{ \Carbon\Carbon::parse($bk->date)->format('d M Y') }}
                                    </small>
                                </td>
                                <td>
                                    @if($bk->invoice)
                                        <span class="badge bg-label-info d-block mb-1 text-truncate" style="max-width: 130px;" title="{{ $bk->invoice }}">
                                            {{ $bk->invoice }}
                                        </span>
                                    @endif
                                    @if($bk->po)
                                        <small class="text-muted d-block text-truncate" style="max-width: 130px;" title="{{ $bk->po }}">
                                            PO: {{ $bk->po }}
                                        </small>
                                    @endif
                                </td>
                                <td>
                                    <div class="text-dark fw-semibold text-wrap" style="max-width: 180px; font-size: 12px;">
                                        {{ trim($bk->detail_client) ?: '-' }}
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="badge {{ $item->warehouse == 'BKS' ? 'bg-label-warning' : 'bg-label-primary' }} fw-bold px-2 py-1">
                                        {{ $item->warehouse ?: 'BDG' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $brand ? $brand . ' - ' : '' }}{{ $partNumber }}</div>
                                    <small class="text-muted d-block text-wrap" style="max-width: 220px;">
                                        {{ $desc }}
                                    </small>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-label-dark fs-7 px-2.5 py-1 qty-val" data-qty="{{ $qty }}">
                                        {{ $qty }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    @if($hasPo)
                                        <span class="fw-semibold text-muted">Rp {{ number_format($unitHpp, 0, '', '.') }}</span>
                                    @else
                                        <div class="input-group input-group-sm justify-content-end">
                                            <span class="input-group-text px-1.5" style="font-size: 11px;">Rp</span>
                                            <input type="text" name="unit_price[{{ $item->id }}]" 
                                                   class="form-control form-control-sm text-end unit-price-input fw-semibold" 
                                                   style="max-width: 110px;" 
                                                   value="{{ number_format($unitHpp, 0, '', '.') }}"
                                                   data-raw="{{ $unitHpp }}">
                                        </div>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <span class="fw-bold text-dark">
                                        Rp {{ number_format($unitSellingPrice, 0, '', '.') }}
                                    </span>
                                    @if($qty > 1 && $totalSellingPrice > 0)
                                        <small class="text-muted d-block" style="font-size: 10.5px;">Total: Rp {{ number_format($totalSellingPrice, 0, '', '.') }}</small>
                                    @endif
                                    <input type="hidden" class="row-subtotal-raw" value="{{ $rowSubtotal }}">
                                </td>
                                <td class="text-center pe-3">
                                    @if($hasPo)
                                        <div class="d-inline-flex align-items-center gap-1">
                                            <a href="{{ route('purchase.show', $item->id_purchase_order) }}" class="badge bg-success text-white py-1.5 px-2 text-decoration-none fw-semibold shadow-xs" target="_blank" title="Klik untuk melihat dokumen PO">
                                                <i class="mdi mdi-file-document-outline me-1"></i>{{ $po->no_po }}
                                            </a>
                                            <button type="button" class="btn btn-xs btn-outline-danger p-1 waves-effect btn-quick-cancel-po" 
                                                    data-po-id="{{ $po->id }}" 
                                                    data-po-no="{{ $po->no_po }}"
                                                    title="Batalkan PO ini &amp; kembalikan item ke listing belum PO">
                                                <i class="mdi mdi-close-circle-outline font-12"></i>
                                            </button>
                                        </div>
                                    @else
                                        <span class="badge bg-label-warning px-2.5 py-1 fw-semibold">
                                            <i class="mdi mdi-clock-outline me-1"></i>Belum Dibuat PO
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                        @endforelse

                        {{-- Client-Side Empty State Row --}}
                        <tr id="emptyStateRow" style="display: {{ $items->isEmpty() ? '' : 'none' }};">
                            <td colspan="10" class="text-center py-5">
                                <div class="d-flex flex-column align-items-center justify-content-center">
                                    <div class="avatar avatar-xl bg-label-secondary rounded-circle mb-3 d-flex align-items-center justify-content-center">
                                        <i class="mdi mdi-package-variant fs-1 text-muted"></i>
                                    </div>
                                    <h6 class="fw-bold text-muted mb-1" id="emptyStateTitle">
                                        Tidak Ada Data Barang Keluar Kojisha
                                    </h6>
                                    <p class="text-muted small mb-0" id="emptyStateDesc">
                                        Tidak ditemukan catatan pengeluaran spare part dengan entitas Kojisha pada bulan {{ $monthNames[$selectedMonth] }} {{ $selectedYear }}.
                                    </p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- Table Footer Summary --}}
            <div class="card-footer bg-light-subtle border-top py-3 d-flex flex-wrap align-items-center justify-content-between gap-3" id="tableFooterSummary">
                <div class="d-flex align-items-center gap-2">
                    <span class="text-muted small">Item Terpilih:</span>
                    <span class="badge bg-primary rounded-pill px-3 py-1" id="selectedCountBadge">0 Item</span>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <span class="text-muted fw-semibold">Total Nilai Draft PO:</span>
                    <h5 class="mb-0 fw-bolder text-primary" id="selectedTotalText">
                        Rp 0
                    </h5>
                    <button type="button" class="btn btn-sm btn-danger waves-effect fw-semibold ms-2" id="btnOpenModalPoFooter">
                        <i class="mdi mdi-cart-arrow-right me-1"></i> Buat Draft PO ke Reftech
                    </button>
                </div>
            </div>
        </div>

        {{-- Modal Form Konfirmasi & PPN PO Intercompany --}}
        <div class="modal fade" id="modalCreatePo" tabindex="-1" aria-labelledby="modalCreatePoLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header bg-danger text-white py-3">
                        <div>
                            <h5 class="modal-title fw-bold text-white mb-0" id="modalCreatePoLabel">
                                <i class="mdi mdi-cart-arrow-right me-2"></i>Konfirmasi Pembuatan PO Intercompany
                            </h5>
                            <small class="text-white-50">Dari PT Kojisha Innotiv Indonesia ke PT Reftech Jaya Optima</small>
                        </div>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        {{-- Entity Route Banner --}}
                        <div class="d-flex align-items-center justify-content-between p-3 rounded-3 bg-light border mb-3 flex-wrap gap-2">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-danger px-2.5 py-1 fw-semibold">PEMBELI (ISSUER)</span>
                                <span class="fw-bold text-dark" style="font-size: 13px;">PT. KOJISHA INNOTIV INDONESIA</span>
                            </div>
                            <i class="mdi mdi-arrow-right-bold text-danger fs-4 d-none d-sm-inline"></i>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-primary px-2.5 py-1 fw-semibold">VENDOR (SUPPLIER)</span>
                                <span class="fw-bold text-dark" style="font-size: 13px;">PT. REFTECH JAYA OPTIMA</span>
                            </div>
                        </div>

                        {{-- Items Preview Table --}}
                        <div class="mb-3">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <label class="form-label fw-bold text-dark mb-0">
                                    <i class="mdi mdi-format-list-bulleted me-1 text-primary"></i> Daftar Item yang Dipilih (<span id="modalItemCountText">0</span> item)
                                </label>
                                <span class="text-muted small">Spare Part dari Stok Reftech</span>
                            </div>
                            <div class="table-responsive border rounded-3" style="max-height: 220px;">
                                <table class="table table-sm table-hover align-middle mb-0" id="modalItemsTable">
                                    <thead class="table-light sticky-top">
                                        <tr class="text-uppercase" style="font-size: 11px;">
                                            <th style="width: 35px;" class="text-center py-2">No</th>
                                            <th class="py-2">Deskripsi Spare Part &amp; BK</th>
                                            <th class="text-center py-2" style="width: 60px;">Qty</th>
                                            <th class="text-end py-2" style="width: 130px;">Harga Satuan</th>
                                            <th class="text-end py-2 pe-3" style="width: 140px;">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody id="modalItemsTableBody" style="font-size: 12px;">
                                        <!-- Populated via JS -->
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- PPN & Tax Settings --}}
                        <div class="card border border-dashed rounded-3 p-3 mb-3 bg-light-subtle">
                            <div class="row align-items-center g-3">
                                <div class="col-md-7">
                                    <div class="form-check form-switch mb-1">
                                        <input class="form-check-input" type="checkbox" id="modalPpnToggle" name="is_ppn" value="1" style="cursor: pointer; transform: scale(1.15);">
                                        <label class="form-check-label fw-bold text-dark ms-2" for="modalPpnToggle" style="cursor: pointer;">
                                            Kenakan PPN (Pajak Pertambahan Nilai)
                                        </label>
                                    </div>
                                    <small class="text-muted d-block ms-4 ps-1">
                                        Aktifkan jika PO Intercompany ini diterbitkan dengan faktur pajak PPN.
                                    </small>
                                </div>
                                <div class="col-md-5 text-md-end" id="modalPpnRateContainer" style="display: none;">
                                    <div class="d-inline-flex align-items-center gap-2">
                                        <label class="form-label small fw-semibold text-muted mb-0">Tarif PPN:</label>
                                        <select name="ppn_rate" id="modalPpnRate" class="form-select form-select-sm" style="width: 100px;">
                                            <option value="11" selected>11%</option>
                                            <option value="12">12%</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Custom PO Note --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark small mb-1">
                                <i class="mdi mdi-note-text-outline me-1 text-primary"></i> Catatan Dokumen PO (Opsional)
                            </label>
                            <textarea name="note" id="modalPoNote" class="form-control form-control-sm" rows="2" placeholder="Catatan internal PO penggantian stok spare part periode {{ $monthNames[$selectedMonth] }} {{ $selectedYear }}..."></textarea>
                        </div>

                        {{-- Calculations Summary Box --}}
                        <div class="p-3 rounded-3" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                            <div class="d-flex justify-content-between align-items-center mb-1.5" style="font-size: 13px;">
                                <span class="text-muted">Subtotal DPP ({{ $monthNames[$selectedMonth] }} {{ $selectedYear }}):</span>
                                <span class="fw-semibold text-dark" id="modalSubtotalText">Rp 0</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom" style="font-size: 13px;">
                                <span class="text-muted d-flex align-items-center gap-1">
                                    <span>PPN:</span>
                                    <span class="badge bg-label-secondary rounded-pill" id="modalPpnBadge">Non-PPN (0%)</span>
                                </span>
                                <span class="fw-semibold text-danger" id="modalPpnText">Rp 0</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center pt-1">
                                <div>
                                    <span class="fw-bold text-dark fs-6 d-block">Grand Total Nilai PO:</span>
                                    <small class="text-muted">Total tagihan PO dari Reftech ke Kojisha</small>
                                </div>
                                <h4 class="mb-0 fw-bolder text-danger" id="modalGrandTotalText">Rp 0</h4>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light py-2.5 d-flex justify-content-between">
                        <button type="button" class="btn btn-sm btn-outline-secondary px-3" data-bs-dismiss="modal">
                            <i class="mdi mdi-close me-1"></i> Batal
                        </button>
                        <button type="submit" class="btn btn-danger waves-effect fw-bold px-4" id="btnSubmitCreatePo">
                            <i class="mdi mdi-check-circle-outline me-1"></i> Create PO
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    {{-- PO Release Card (Documents View) --}}
    <div class="card border-0 shadow-sm" id="poReleaseCard" style="display: {{ $selectedStatus == 'release' ? '' : 'none' }};">
        <div class="card-header bg-transparent border-bottom py-3 d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-3">
                <h6 class="mb-0 fw-bold text-dark">
                    <i class="mdi mdi-file-document-multiple-outline me-1 text-info"></i> Daftar Dokumen PO Intercompany (Kojisha &rarr; Reftech)
                </h6>
                <span class="badge bg-label-info rounded-pill px-2.5 py-1">{{ $releasedPos->count() }} Dokumen PO</span>
            </div>
            <div>
                <span class="text-muted small">Periode: <b>{{ $monthNames[$selectedMonth] }} {{ $selectedYear }}</b></span>
            </div>
        </div>
        <div class="table-responsive text-nowrap">
            <table class="table table-hover align-middle mb-0" id="tablePoRelease">
                <thead class="table-light">
                    <tr class="text-uppercase" style="font-size: 11px; letter-spacing: 0.5px;">
                        <th class="py-3" style="min-width: 170px;">No. Dokumen PO</th>
                        <th class="py-3" style="min-width: 110px;">Tanggal Terbit</th>
                        <th class="py-3" style="min-width: 170px;">Vendor (Tujuan)</th>
                        <th class="text-center py-3" style="width: 100px;">Item Sparepart</th>
                        <th class="text-end py-3" style="min-width: 130px;">Subtotal DPP</th>
                        <th class="text-end py-3" style="min-width: 120px;">PPN</th>
                        <th class="text-end py-3" style="min-width: 140px;">Grand Total</th>
                        <th class="py-3" style="min-width: 200px;">Catatan / Keterangan</th>
                        <th class="text-center py-3 pe-3" style="min-width: 130px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($releasedPos as $po)
                        <tr>
                            <td>
                                <a href="{{ route('purchase.show', $po->id) }}" class="fw-bold text-primary d-block" target="_blank">
                                    <i class="mdi mdi-file-document-outline me-1"></i>{{ $po->no_po }}
                                </a>
                                <small class="text-muted">{{ $po->company ?: 'PT. REFTECH JAYA OPTIMA' }}</small>
                            </td>
                            <td>
                                <span class="fw-semibold text-dark">{{ \Carbon\Carbon::parse($po->date)->format('d M Y') }}</span>
                            </td>
                            <td>
                                <div class="fw-bold text-dark" style="font-size: 12.5px;">{{ $po->company ?: 'PT. REFTECH JAYA OPTIMA' }}</div>
                                <small class="text-muted">{{ $po->attn ?: 'Central Logistics Reftech' }}</small>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-label-dark px-2.5 py-1 fw-bold">
                                    {{ $po->detail_purchase_order_count ?? ($po->detail ? $po->detail->count() : 0) }} Pcs Item
                                </span>
                            </td>
                            <td class="text-end fw-semibold text-dark">
                                Rp {{ number_format((float)($po->subtotal ?? 0), 0, '', '.') }}
                            </td>
                            <td class="text-end">
                                @if((float)($po->vat ?? 0) > 0)
                                    <span class="badge bg-label-danger fw-bold">
                                        Rp {{ number_format((float)$po->vat, 0, '', '.') }}
                                    </span>
                                @else
                                    <span class="badge bg-label-secondary">Non-PPN (Rp 0)</span>
                                @endif
                            </td>
                            <td class="text-end fw-bolder text-primary pe-2" style="font-size: 13.5px;">
                                Rp {{ number_format((float)($po->total ?? 0), 0, '', '.') }}
                            </td>
                            <td>
                                <small class="text-muted d-block text-wrap" style="max-width: 250px;">
                                    {{ $po->note ?: 'PO Intercompany penggantian stok spare part' }}
                                </small>
                            </td>
                            <td class="text-center pe-3">
                                <div class="d-inline-flex align-items-center gap-1">
                                    <a href="{{ route('purchase.show', $po->id) }}" class="btn btn-xs btn-outline-primary p-1.5 waves-effect" target="_blank" title="Lihat Dokumen PO">
                                        <i class="mdi mdi-eye-outline font-13"></i>
                                    </a>
                                    <a href="{{ route('purchase.show_print', $po->id) }}" class="btn btn-xs btn-outline-secondary p-1.5 waves-effect" target="_blank" title="Download / Cetak PO">
                                        <i class="mdi mdi-printer-outline font-13"></i>
                                    </a>
                                    <button type="button" class="btn btn-xs btn-outline-danger p-1.5 waves-effect btn-quick-cancel-po" 
                                            data-po-id="{{ $po->id }}" 
                                            data-po-no="{{ $po->no_po }}" 
                                            title="Batalkan Dokumen PO ini &amp; kembalikan item ke listing">
                                        <i class="mdi mdi-close-circle-outline font-13"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5">
                                <div class="d-flex flex-column align-items-center justify-content-center">
                                    <div class="avatar avatar-xl bg-label-secondary rounded-circle mb-3 d-flex align-items-center justify-content-center">
                                        <i class="mdi mdi-file-document-outline fs-1 text-muted"></i>
                                    </div>
                                    <h6 class="fw-bold text-muted mb-1">Belum Ada Dokumen PO Release</h6>
                                    <p class="text-muted small mb-0">
                                        Belum ada Purchase Order (PO) Kojisha ke Reftech yang diterbitkan untuk periode {{ $monthNames[$selectedMonth] }} {{ $selectedYear }}.
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Hidden Form for Quick Cancel PO --}}
    <form id="globalCancelPoForm" method="POST" action="" style="display: none;">
        @csrf
    </form>
</div>
@endsection

@push('after-scripts')
<script>
$(document).ready(function() {
    let currentStatus = '{{ $selectedStatus ?: "pending" }}';
    const monthName = '{{ $monthNames[$selectedMonth] }}';
    const yearVal = '{{ $selectedYear }}';

    function parseRupiah(str) {
        if (!str) return 0;
        let clean = str.toString().replace(/[^0-9]/g, '');
        return parseFloat(clean) || 0;
    }

    function formatRupiah(num) {
        return 'Rp ' + Math.round(num).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    function calculateTotals() {
        let total = 0;
        let count = 0;

        $('.item-row:visible').each(function() {
            let row = $(this);
            let checkbox = row.find('.row-checkbox');
            let isChecked = checkbox.length > 0 && checkbox.is(':checked') && !checkbox.is(':disabled');
            let qty = parseFloat(row.find('.qty-val').data('qty')) || 1;
            let priceInput = row.find('.unit-price-input');
            let price = priceInput.length > 0 ? parseRupiah(priceInput.val()) : (row.find('.row-subtotal-raw').val() / qty);

            let rowSubtotal = qty * price;
            row.find('.row-subtotal-raw').val(rowSubtotal);

            if (isChecked) {
                total += rowSubtotal;
                count++;
            }
        });

        $('#selectedCountBadge').text(count + ' Item');
        $('#selectedTotalText').text(formatRupiah(total));

        if (count === 0 || currentStatus === 'done') {
            $('#btnOpenModalPo, #btnOpenModalPoFooter').prop('disabled', true).addClass('opacity-50');
        } else {
            $('#btnOpenModalPo, #btnOpenModalPoFooter').prop('disabled', false).removeClass('opacity-50');
        }
    }

    function applyTabFilter(status) {
        currentStatus = status;
        $('#formStatusInput').val(status);

        // Update active tab buttons & badges
        $('.tab-filter-btn').removeClass('active');
        $('.tab-badge').each(function() {
            $(this).removeClass('bg-white text-primary text-success text-secondary text-info');
        });

        let activeBtn = $(`.tab-filter-btn[data-status="${status}"]`);
        activeBtn.addClass('active');

        if (status === 'release') {
            // View PO Release Documents Card
            $('#draftPoForm').hide();
            $('#poReleaseCard').show();

            activeBtn.find('.tab-badge').addClass('bg-white text-info').removeClass('bg-label-info');
            $(`.tab-filter-btn[data-status="pending"] .tab-badge`).addClass('bg-label-warning');
            $(`.tab-filter-btn[data-status="done"] .tab-badge`).addClass('bg-label-success');
            $(`.tab-filter-btn[data-status="all"] .tab-badge`).addClass('bg-label-secondary');

            $('#tabDescription').html('<i class="mdi mdi-file-document-check-outline text-info me-1"></i>Menampilkan rekapan dokumen <b>Purchase Order (PO) Kojisha ke Reftech</b> yang telah diterbitkan pada periode ini.');
        } else {
            // View Items Table Card
            $('#draftPoForm').show();
            $('#poReleaseCard').hide();

            $(`.tab-filter-btn[data-status="release"] .tab-badge`).addClass('bg-label-info');

            if (status === 'pending') {
                activeBtn.find('.tab-badge').addClass('bg-white text-primary').removeClass('bg-label-warning');
                $(`.tab-filter-btn[data-status="done"] .tab-badge`).addClass('bg-label-success');
                $(`.tab-filter-btn[data-status="all"] .tab-badge`).addClass('bg-label-secondary');
                $('#tabDescription').html('<i class="mdi mdi-information-outline text-primary me-1"></i>Menampilkan item barang keluar Kojisha yang <b>belum diproses ke Draft PO</b>.');
                $('#tableHeaderTitleText').text('Item Siap Dibuatkan PO Intercompany');
                $('#headerActionButtons').show();
                $('#tableFooterSummary').show();
            } else if (status === 'done') {
                activeBtn.find('.tab-badge').addClass('bg-white text-success').removeClass('bg-label-success');
                $(`.tab-filter-btn[data-status="pending"] .tab-badge`).addClass('bg-label-warning');
                $(`.tab-filter-btn[data-status="all"] .tab-badge`).addClass('bg-label-secondary');
                $('#tabDescription').html('<i class="mdi mdi-check-all text-success me-1"></i>Menampilkan item yang <b>sudah diterbitkan PO</b> ke Reftech. Anda dapat membatalkan PO jika diperlukan.');
                $('#tableHeaderTitleText').text('Item Sudah Terbit PO Intercompany');
                $('#headerActionButtons').hide();
                $('#tableFooterSummary').hide();
            } else {
                activeBtn.find('.tab-badge').addClass('bg-white text-secondary').removeClass('bg-label-secondary');
                $(`.tab-filter-btn[data-status="pending"] .tab-badge`).addClass('bg-label-warning');
                $(`.tab-filter-btn[data-status="done"] .tab-badge`).addClass('bg-label-success');
                $('#tabDescription').html('<i class="mdi mdi-layers-outline text-secondary me-1"></i>Menampilkan <b>seluruh</b> riwayat pengeluaran barang spare part Kojisha.');
                $('#tableHeaderTitleText').text('Daftar Rincian Barang Keluar Kojisha');
                $('#headerActionButtons').show();
                $('#tableFooterSummary').show();
            }

            // Filter Item Rows
            let visibleCount = 0;

            $('.item-row').each(function() {
                let row = $(this);
                let hasPo = row.data('has-po') == '1';
                let shouldShow = false;

                if (status === 'pending') {
                    shouldShow = !hasPo;
                } else if (status === 'done') {
                    shouldShow = hasPo;
                } else {
                    shouldShow = true;
                }

                if (shouldShow) {
                    row.show();
                    visibleCount++;
                } else {
                    row.hide();
                }
            });

            $('#visibleRowCountBadge').text(visibleCount + ' Baris Item');

            // Check empty state
            if (visibleCount === 0) {
                $('#emptyStateRow').show();
                if (status === 'pending') {
                    $('#emptyStateTitle').text('Semua Item Barang Keluar Sudah Dibuatkan PO');
                    $('#emptyStateDesc').text(`Tidak ada pengeluaran spare part Kojisha yang pending untuk periode ${monthName} ${yearVal}.`);
                } else if (status === 'done') {
                    $('#emptyStateTitle').text('Belum Ada PO Intercompany Terbit');
                    $('#emptyStateDesc').text(`Belum ada Draft/PO Intercompany yang diterbitkan pada periode ${monthName} ${yearVal}.`);
                } else {
                    $('#emptyStateTitle').text('Tidak Ada Data Barang Keluar Kojisha');
                    $('#emptyStateDesc').text(`Tidak ditemukan catatan pengeluaran spare part dengan entitas Kojisha pada bulan ${monthName} ${yearVal}.`);
                }
            } else {
                $('#emptyStateRow').hide();
            }

            // Update header check-all
            let visibleActiveCheckboxes = $('.item-row:visible .row-checkbox:not(:disabled)');
            let checkedBoxes = $('.item-row:visible .row-checkbox:not(:disabled):checked');
            let allChecked = visibleActiveCheckboxes.length > 0 && checkedBoxes.length === visibleActiveCheckboxes.length;
            $('#checkAllHeader').prop('checked', allChecked);

            calculateTotals();
        }

        // Update browser URL without full reload
        if (window.history && window.history.replaceState) {
            let url = new URL(window.location.href);
            url.searchParams.set('status', status);
            window.history.replaceState({}, '', url.toString());
        }
    }

    // Tab button click handler (Instant switch without reload)
    $(document).on('click', '.tab-filter-btn', function(e) {
        e.preventDefault();
        let targetStatus = $(this).data('status');
        applyTabFilter(targetStatus);
    });

    // Input format on price change
    $(document).on('input', '.unit-price-input', function() {
        let val = parseRupiah($(this).val());
        $(this).val(val ? val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".") : '0');
        calculateTotals();
    });

    // Checkbox changes
    $(document).on('change', '.row-checkbox', function() {
        let activeCheckboxes = $('.item-row:visible .row-checkbox:not(:disabled)');
        let checkedBoxes = $('.item-row:visible .row-checkbox:not(:disabled):checked');
        let allChecked = activeCheckboxes.length > 0 && checkedBoxes.length === activeCheckboxes.length;
        $('#checkAllHeader').prop('checked', allChecked);
        calculateTotals();
    });

    $('#checkAllHeader').on('change', function() {
        let isChecked = $(this).is(':checked');
        $('.item-row:visible .row-checkbox:not(:disabled)').prop('checked', isChecked);
        calculateTotals();
    });

    $('#btnSelectAll').on('click', function() {
        let activeCheckboxes = $('.item-row:visible .row-checkbox:not(:disabled)');
        let checkedBoxes = $('.item-row:visible .row-checkbox:not(:disabled):checked');
        let allChecked = activeCheckboxes.length > 0 && checkedBoxes.length === activeCheckboxes.length;
        $('.item-row:visible .row-checkbox:not(:disabled), #checkAllHeader').prop('checked', !allChecked);
        calculateTotals();
    });

    function updateModalCalculations() {
        let subtotal = 0;
        let count = 0;

        $('.item-row:visible').each(function() {
            let row = $(this);
            let checkbox = row.find('.row-checkbox');
            if (checkbox.length > 0 && checkbox.is(':checked') && !checkbox.is(':disabled')) {
                let qty = parseFloat(row.find('.qty-val').data('qty')) || 1;
                let price = parseRupiah(row.find('.unit-price-input').val());
                subtotal += (qty * price);
                count++;
            }
        });

        let isPpn = $('#modalPpnToggle').is(':checked');
        let ppnRate = parseFloat($('#modalPpnRate').val()) || 11;
        let vat = 0;

        if (isPpn) {
            $('#modalPpnRateContainer').show();
            vat = Math.round((subtotal * ppnRate) / 100);
            $('#modalPpnBadge').text('PPN (' + ppnRate + '%)').removeClass('bg-label-secondary').addClass('bg-label-danger');
        } else {
            $('#modalPpnRateContainer').hide();
            vat = 0;
            $('#modalPpnBadge').text('Non-PPN (0%)').removeClass('bg-label-danger').addClass('bg-label-secondary');
        }

        let grandTotal = subtotal + vat;

        $('#modalSubtotalText').text(formatRupiah(subtotal));
        $('#modalPpnText').text(formatRupiah(vat));
        $('#modalGrandTotalText').text(formatRupiah(grandTotal));
    }

    function openPoModal() {
        let checkedRows = $('.item-row:visible .row-checkbox:not(:disabled):checked');
        if (checkedRows.length === 0) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Pilih Item',
                    text: 'Silakan centang minimal 1 item untuk membuat Draft Purchase Order (PO) ke Reftech.'
                });
            } else {
                alert('Silakan centang minimal 1 item untuk membuat Draft Purchase Order (PO) ke Reftech.');
            }
            return;
        }

        let tableBody = $('#modalItemsTableBody');
        tableBody.empty();

        let no = 1;
        checkedRows.each(function() {
            let row = $(this).closest('.item-row');
            let bkNo = row.data('bk') || '-';
            let partName = row.data('part') || '-';
            let desc = row.data('desc') || '';
            let client = row.data('client') || '';
            let qty = parseFloat(row.find('.qty-val').data('qty')) || 1;
            let price = parseRupiah(row.find('.unit-price-input').val());
            let rowSubtotal = qty * price;

            let html = `
                <tr>
                    <td class="text-center fw-semibold text-muted py-2">${no++}</td>
                    <td class="py-2">
                        <div class="fw-bold text-dark">${partName}</div>
                        <small class="text-muted d-block">${bkNo} ${client ? '• Cust: ' + client : ''}</small>
                    </td>
                    <td class="text-center py-2">
                        <span class="badge bg-label-dark">${qty}</span>
                    </td>
                    <td class="text-end py-2">${formatRupiah(price)}</td>
                    <td class="text-end pe-3 fw-bold text-dark py-2">${formatRupiah(rowSubtotal)}</td>
                </tr>
            `;
            tableBody.append(html);
        });

        $('#modalItemCountText').text(checkedRows.length);
        updateModalCalculations();

        let modalEl = document.getElementById('modalCreatePo');
        if (modalEl) {
            let bsModal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
            bsModal.show();
        }
    }

    $('#btnOpenModalPo, #btnOpenModalPoFooter').on('click', function(e) {
        e.preventDefault();
        openPoModal();
    });

    $('#modalPpnToggle, #modalPpnRate').on('change', function() {
        updateModalCalculations();
    });

    $('#draftPoForm').on('submit', function(e) {
        let checkedCount = $('.item-row:visible .row-checkbox:not(:disabled):checked').length;
        if (checkedCount === 0) {
            e.preventDefault();
            return false;
        }

        let submitBtn = $('#btnSubmitCreatePo');
        submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Creating PO...');
    });

    // Quick Cancel PO handler
    $(document).on('click', '.btn-quick-cancel-po', function(e) {
        e.preventDefault();
        let poId = $(this).data('po-id');
        let poNo = $(this).data('po-no');
        let cancelUrl = "{{ url('/warehouse/intercompany/po') }}/" + poId + "/cancel";

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Batalkan PO Intercompany?',
                html: `Apakah Anda yakin ingin membatalkan dokumen PO <b>${poNo}</b>?<br><br><small class="text-danger fw-semibold"><i class="mdi mdi-information-outline me-1"></i>Seluruh item barang keluar yang ada di dalam PO ini akan dikembalikan ke daftar 'Belum Dibuat PO'.</small>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#8592a3',
                confirmButtonText: '<i class="mdi mdi-check me-1"></i> Ya, Batalkan PO',
                cancelButtonText: 'Kembali'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Membatalkan...',
                        text: 'Sedang membatalkan PO dan mengembalikan item...',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        didOpen: () => { Swal.showLoading(); }
                    });
                    let form = $('#globalCancelPoForm');
                    form.attr('action', cancelUrl);
                    form.submit();
                }
            });
        } else {
            if (confirm(`Apakah Anda yakin ingin membatalkan dokumen PO ${poNo}? Seluruh item akan dikembalikan ke daftar rekap belum PO.`)) {
                let form = $('#globalCancelPoForm');
                form.attr('action', cancelUrl);
                form.submit();
            }
        }
    });

    // Initialize initial tab view
    applyTabFilter(currentStatus);
});
</script>
@endpush
