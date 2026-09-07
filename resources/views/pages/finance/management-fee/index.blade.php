@extends('layouts.sales.app')
@section('title', 'Management Fee - Finance')
@section('content')

<div class="container-fluid flex-grow-1 container-p-y px-4">
    {{-- Breadcrumb & Title --}}
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h4 class="fw-bold py-1 mb-1 text-dark">
                <i class="mdi mdi-cash-refund text-primary me-2"></i>Management Fee
            </h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb m-0" style="font-size: 12.5px;">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}">Dashboard</a></li>
                    <li class="breadcrumb-item">Finance</li>
                    <li class="breadcrumb-item active">Management Fee</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            {{-- Segmented Year Navigator Pill Group (Style Report Monthly) --}}
            @php
                $currentYearNum = ($selectedYear && $selectedYear !== 'all') ? (int)$selectedYear : (int)date('Y');
                $prevYearOption = $currentYearNum - 1;
                $nextYearOption = $currentYearNum + 1;
            @endphp
            <div class="d-inline-flex align-items-center bg-body-tertiary border rounded-pill p-1 shadow-sm">
                {{-- Prev Year --}}
                <a href="{{ route('finance.management-fee.index', ['year' => $prevYearOption, 'tab' => request('tab', 'quotation')]) }}"
                   class="btn btn-icon btn-sm btn-label-secondary rounded-circle shadow-none"
                   style="width: 28px; height: 28px;"
                   title="Tahun Sebelumnya ({{ $prevYearOption }})">
                    <i class="mdi mdi-chevron-left fs-5"></i>
                </a>

                {{-- Dropdown Tahun --}}
                <div class="dropdown">
                    <button type="button"
                        class="btn btn-sm border-0 bg-transparent fw-semibold text-dark dropdown-toggle py-0 px-2 shadow-none d-flex align-items-center gap-1.5"
                        style="font-size: 0.82rem; box-shadow: none !important; background: transparent !important;"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="mdi mdi-calendar-range text-primary"></i>
                        <span>{{ ($selectedYear && $selectedYear !== 'all') ? 'Tahun ' . $selectedYear : 'Semua Tahun' }}</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="z-index: 1055;">
                        <li>
                            <a class="dropdown-item waves-effect d-flex align-items-center justify-content-between {{ (!$selectedYear || $selectedYear === 'all') ? 'active' : '' }}"
                               href="{{ route('finance.management-fee.index', ['year' => 'all', 'tab' => request('tab', 'quotation')]) }}">
                                <span>Semua Tahun</span>
                                @if (!$selectedYear || $selectedYear === 'all')
                                    <i class="mdi mdi-check ms-2"></i>
                                @endif
                            </a>
                        </li>
                        <div class="dropdown-divider my-1"></div>
                        @foreach ($years as $yr)
                            <li>
                                <a class="dropdown-item waves-effect d-flex align-items-center justify-content-between {{ $selectedYear == $yr ? 'active' : '' }}"
                                   href="{{ route('finance.management-fee.index', ['year' => $yr, 'tab' => request('tab', 'quotation')]) }}">
                                    <span>Tahun {{ $yr }}</span>
                                    @if ($selectedYear == $yr)
                                        <i class="mdi mdi-check ms-2"></i>
                                    @endif
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                {{-- Next Year --}}
                <a href="{{ route('finance.management-fee.index', ['year' => $nextYearOption, 'tab' => request('tab', 'quotation')]) }}"
                   class="btn btn-icon btn-sm btn-label-secondary rounded-circle shadow-none"
                   style="width: 28px; height: 28px;"
                   title="Tahun Berikutnya ({{ $nextYearOption }})">
                    <i class="mdi mdi-chevron-right fs-5"></i>
                </a>
            </div>

            {{-- Shortcut Kembali ke Tahun Ini jika sedang memilih tahun lain / semua tahun --}}
            @if ($selectedYear != now()->year)
                <a href="{{ route('finance.management-fee.index', ['year' => now()->year, 'tab' => request('tab', 'quotation')]) }}" 
                   class="btn btn-sm btn-label-primary rounded-pill px-3 fw-semibold shadow-none" 
                   title="Kembali ke Tahun Sekarang">
                    <i class="mdi mdi-calendar-today me-1"></i> Tahun Ini
                </a>
            @endif

            <span class="badge bg-label-primary px-3 py-2 fw-semibold d-none d-md-inline-block rounded-pill" style="font-size: 12px;">
                <i class="mdi mdi-shield-check-outline me-1"></i> Kebijakan Pajak Fee 2026
            </span>

            @if (!$isSales)
            <button type="button" class="btn btn-primary btn-sm px-3 shadow-sm rounded-pill" id="btn-add-manual-fee">
                <i class="mdi mdi-plus-circle-outline me-1"></i> Tambah Fee Manual
            </button>
            @endif
        </div>
    </div>

    {{-- Alert Banner Info Kebijakan 2026 --}}
    <div class="alert alert-primary py-2.5 px-3 mb-4 rounded-3 border-0 shadow-sm" role="alert">
        <div class="d-flex align-items-start gap-2">
            <i class="mdi mdi-information-outline fs-4 flex-shrink-0 mt-0.5"></i>
            <div class="small w-100">
                <div class="fw-bold mb-1">Panduan Kebijakan Pajak Management Fee 2026:</div>
                <div class="d-flex flex-wrap gap-2 align-items-center">
                    <span class="badge bg-white text-dark border fw-medium px-2 py-1">&lt; Rp 1,5 Juta : <strong>Pajak 0%</strong> (Bebas Pajak)</span>
                    <span class="badge bg-white text-dark border fw-medium px-2 py-1">Rp 1,5 Juta - Rp 5 Juta : <strong>Pajak 3.68%</strong></span>
                    <span class="badge bg-white text-dark border fw-medium px-2 py-1">&gt; Rp 5 Juta : <strong>Pajak 10%</strong></span>
                    <span class="badge bg-warning text-dark border fw-semibold px-2 py-1"><i class="mdi mdi-lock-outline me-1"></i>Maksimal Total Fee Quotation: <strong>10% Pre-PPN</strong></span>
                </div>
            </div>
        </div>
    </div>

    {{-- KPI / Summary Cards (Dipengaruhi Filter Tahun Global) --}}
    @php
        $yearContextText = ($selectedYear && $selectedYear !== 'all') ? 'Tahun ' . $selectedYear : 'Semua Periode';
    @endphp
    <div class="row g-3 mb-4">
        {{-- Card 1: Total Gross Fee --}}
        <div class="col-xl-3 col-sm-6 col-12">
            <div class="card border-0 shadow-sm overflow-hidden h-100" style="border-left: 4px solid #696cff !important;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted fw-semibold" style="font-size: 12px;">Total Gross Fee</span>
                        <div class="avatar avatar-sm bg-label-primary rounded-circle d-flex align-items-center justify-content-center">
                            <i class="mdi mdi-cash-minus fs-5"></i>
                        </div>
                    </div>
                    <h5 class="fw-bold mb-1 text-dark">Rp {{ number_format($totalGrossFee, 0, ',', '.') }}</h5>
                    <span class="text-muted small" style="font-size: 11px;">Akumulasi gross fee ({{ $yearContextText }})</span>
                </div>
            </div>
        </div>

        {{-- Card 2: Total Potongan Pajak PPh --}}
        <div class="col-xl-3 col-sm-6 col-12">
            <div class="card border-0 shadow-sm overflow-hidden h-100" style="border-left: 4px solid #ff3e1d !important;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted fw-semibold" style="font-size: 12px;">Potongan Pajak (2026)</span>
                        <div class="avatar avatar-sm bg-label-danger rounded-circle d-flex align-items-center justify-content-center">
                            <i class="mdi mdi-percent-outline fs-5"></i>
                        </div>
                    </div>
                    <h5 class="fw-bold mb-1 text-danger">Rp {{ number_format($totalTaxDeduction, 0, ',', '.') }}</h5>
                    <span class="text-muted small" style="font-size: 11px;">Total tax withholding ({{ $yearContextText }})</span>
                </div>
            </div>
        </div>

        {{-- Card 3: Nett Fee Pending / Siap Ditransfer --}}
        <div class="col-xl-3 col-sm-6 col-12">
            <div class="card border-0 shadow-sm overflow-hidden h-100" style="border-left: 4px solid #ffab00 !important;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted fw-semibold" style="font-size: 12px;">Nett Fee Pending / Unpaid</span>
                        <div class="avatar avatar-sm bg-label-warning rounded-circle d-flex align-items-center justify-content-center">
                            <i class="mdi mdi-clock-sand fs-5"></i>
                        </div>
                    </div>
                    <h5 class="fw-bold mb-1 text-warning">Rp {{ number_format($totalPendingFee, 0, ',', '.') }}</h5>
                    <span class="text-muted small" style="font-size: 11px;">Menunggu transfer ({{ $yearContextText }})</span>
                </div>
            </div>
        </div>

        {{-- Card 4: Nett Fee Telah Ditransfer (Paid) --}}
        <div class="col-xl-3 col-sm-6 col-12">
            <div class="card border-0 shadow-sm overflow-hidden h-100" style="border-left: 4px solid #71dd37 !important;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted fw-semibold" style="font-size: 12px;">Nett Fee Telah Ditransfer</span>
                        <div class="avatar avatar-sm bg-label-success rounded-circle d-flex align-items-center justify-content-center">
                            <i class="mdi mdi-check-decagram-outline fs-5"></i>
                        </div>
                    </div>
                    <h5 class="fw-bold mb-1 text-success">Rp {{ number_format($totalPaidFee, 0, ',', '.') }}</h5>
                    <span class="text-muted small" style="font-size: 11px;">Telah dicairkan ({{ $yearContextText }})</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Card with Tabs --}}
    <div class="card border-0 shadow-sm overflow-hidden">
        {{-- Nav Tabs --}}
        <div class="card-header bg-white border-bottom p-0">
            <ul class="nav nav-tabs nav-fill" id="feeTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link py-3 px-4 fw-semibold text-start d-flex align-items-center gap-2 {{ $activeTab === 'quotation' ? 'active border-bottom border-primary border-3' : 'text-muted' }}"
                        id="tab-quotation-link" data-bs-toggle="tab" data-bs-target="#tab-quotation-pane" type="button" role="tab">
                        <i class="mdi mdi-file-document-edit-outline fs-5 text-primary"></i>
                        <div>
                            <div class="fw-bold">Fee dari Penawaran (Quotation)</div>
                            <small class="text-muted fw-normal" style="font-size: 11px;">Terkoneksi ke PO &amp; Smart Quotation</small>
                        </div>
                        <span class="badge rounded-pill bg-label-primary ms-auto">{{ $allQuoteFees->count() }} Data</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link py-3 px-4 fw-semibold text-start d-flex align-items-center gap-2 {{ $activeTab === 'manual' ? 'active border-bottom border-primary border-3' : 'text-muted' }}"
                        id="tab-manual-link" data-bs-toggle="tab" data-bs-target="#tab-manual-pane" type="button" role="tab">
                        <i class="mdi mdi-calculator-variant-outline fs-5 text-success"></i>
                        <div>
                            <div class="fw-bold">Fee Manual / Khusus</div>
                            <small class="text-muted fw-normal" style="font-size: 11px;">Input langsung berbasis Company / Customer</small>
                        </div>
                        <span class="badge rounded-pill bg-label-success ms-auto">{{ $allManualFees->count() }} Data</span>
                    </button>
                </li>
            </ul>
        </div>

        <div class="tab-content p-0" id="feeTabsContent">
            {{-- ========================================================= --}}
            {{-- TAB 1: FEE DARI QUOTATION / PENAWARAN                     --}}
            {{-- ========================================================= --}}
            <div class="tab-pane fade {{ $activeTab === 'quotation' ? 'show active' : '' }}" id="tab-quotation-pane" role="tabpanel">
                {{-- Filter Quotation Header --}}
                <div class="bg-light border-bottom py-3 px-4">
                    <form action="{{ route('finance.management-fee.index') }}" method="GET" id="form-filter-quote">
                        <input type="hidden" name="tab" value="quotation">
                        <input type="hidden" name="year" value="{{ request('year', 'all') }}">
                        <div class="row g-2 align-items-center">
                            {{-- Search Keyword --}}
                            <div class="{{ $isSales ? 'col-lg-5 col-md-6 col-12' : 'col-lg-4 col-md-6 col-12' }}">
                                <div class="input-group input-group-merge input-group-sm">
                                    <span class="input-group-text"><i class="mdi mdi-magnify"></i></span>
                                    <input type="text" class="form-control" name="search" value="{{ request('search') }}"
                                        placeholder="Cari No Quote, PO, Customer, Rekening...">
                                </div>
                            </div>

                            {{-- Status Fee --}}
                            <div class="col-lg-2 col-md-3 col-6">
                                <select class="form-select form-select-sm" name="fee_status" onchange="this.form.submit()">
                                    <option value="all" {{ request('fee_status') == 'all' ? 'selected' : '' }}>Semua Status Fee</option>
                                    <option value="unpaid" {{ request('fee_status') == 'unpaid' ? 'selected' : '' }}>🔴 Unpaid (Belum)</option>
                                    <option value="pending_transfer" {{ request('fee_status') == 'pending_transfer' ? 'selected' : '' }}>🟡 Siap Ditransfer</option>
                                    <option value="paid" {{ request('fee_status') == 'paid' ? 'selected' : '' }}>🟢 Paid (Sudah Ditransfer)</option>
                                </select>
                            </div>

                            {{-- Status Pembayaran Customer --}}
                            <div class="{{ $isSales ? 'col-lg-2 col-md-3 col-6' : 'col-lg-2 col-md-3 col-6' }}">
                                <select class="form-select form-select-sm" name="cust_payment_status" onchange="this.form.submit()">
                                    <option value="all" {{ request('cust_payment_status') == 'all' ? 'selected' : '' }}>Semua Status Bayar Customer</option>
                                    <option value="paid" {{ request('cust_payment_status') == 'paid' ? 'selected' : '' }}>🟢 Full Payment (Lunas)</option>
                                    <option value="dp" {{ request('cust_payment_status') == 'dp' ? 'selected' : '' }}>🟡 DP (Sebagian)</option>
                                    <option value="tempo" {{ request('cust_payment_status') == 'tempo' ? 'selected' : '' }}>🟠 Credit / Tempo</option>
                                    <option value="unpaid" {{ request('cust_payment_status') == 'unpaid' ? 'selected' : '' }}>🔴 Belum Dibayar</option>
                                </select>
                            </div>

                            {{-- Sales Person (Hanya untuk Admin / Finance / Accounting) --}}
                            @if (!$isSales)
                            <div class="col-lg-2 col-md-3 col-6">
                                <select class="form-select form-select-sm" name="sales_id" onchange="this.form.submit()">
                                    <option value="all">Semua Sales</option>
                                    @foreach ($salesList as $s)
                                        <option value="{{ $s->id }}" {{ request('sales_id') == $s->id ? 'selected' : '' }}>
                                            {{ $s->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @endif

                            {{-- Date Filter & Actions --}}
                            <div class="{{ $isSales ? 'col-lg-3 col-md-12 col-12' : 'col-lg-2 col-md-12 col-12' }} d-flex gap-1">
                                <input type="date" class="form-control form-control-sm" name="start_date" value="{{ request('start_date') }}" title="Dari Tanggal">
                                <input type="date" class="form-control form-control-sm" name="end_date" value="{{ request('end_date') }}" title="Sampai Tanggal">
                                <button type="submit" class="btn btn-sm btn-primary px-2" title="Terapkan Filter">
                                    <i class="mdi mdi-filter-variant"></i>
                                </button>
                                <a href="{{ route('finance.management-fee.index', ['tab' => 'quotation', 'year' => request('year')]) }}" class="btn btn-sm btn-label-secondary px-2" title="Reset Filter">
                                    <i class="mdi mdi-refresh"></i>
                                </a>
                            </div>
                        </div>
                    </form>
                </div>

                {{-- Quotation Table Data --}}
                <div class="table-responsive text-nowrap">
                    <table class="table table-hover align-middle mb-0" style="font-size: 12.5px;">
                        <thead class="table-light" style="font-size: 11.5px;">
                            <tr>
                                <th class="text-center" style="width: 4%;">No</th>
                                <th style="width: 17%;">Quotation &amp; PO</th>
                                <th style="width: 17%;">Customer &amp; Sales</th>
                                <th class="text-end" style="width: 12%;">Nilai Quote</th>
                                <th class="text-end" style="width: 11%;">Gross Fee</th>
                                <th class="text-center" style="width: 12%;">Pajak (2026)</th>
                                <th class="text-end fw-bold" style="width: 12%;">Nett Transfer</th>
                                <th style="width: 15%;">Info Rekening</th>
                                <th class="text-center" style="width: 10%;">Status Pencairan</th>
                                <th class="text-center" style="width: 8%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $noQuote = $items->firstItem() ?: 1; @endphp
                            @forelse ($items as $item)
                                @php
                                    $taxData = $item->fee_tax_data;
                                    $custPay = $item->customer_payment_summary;
                                    $preTax = floatval($item->subtotal ?? 0) - floatval($item->diskon ?? 0);
                                    if ($preTax <= 0) {
                                        $preTax = floatval($item->total ?? 0) - floatval($item->tax_amount ?? 0);
                                    }
                                    $clientName = $item->client?->company ?: ($item->client?->name ?? 'Customer');
                                @endphp
                                <tr>
                                    <td class="text-center text-muted fw-semibold">{{ $noQuote++ }}</td>
                                    
                                    {{-- Quotation & PO Info + Status Pembayaran Customer --}}
                                    <td>
                                        <a href="{{ route('unit-quotation.show', $item->id) }}" class="fw-bold text-primary text-decoration-none d-block">
                                            #{{ $item->no_quote }}
                                        </a>
                                        <div class="d-flex flex-wrap gap-1 align-items-center mt-0.5">
                                            @if ($item->po_number)
                                                <span class="badge bg-label-dark px-1.5 py-0.5" style="font-size: 9.5px;">
                                                    <i class="mdi mdi-clipboard-text-outline me-0.5"></i>PO: {{ $item->po_number }}
                                                </span>
                                            @endif
                                            {{-- Status Bayar Customer --}}
                                            <span class="badge {{ $custPay->badge_class }} px-1.5 py-0.5 fw-semibold" style="font-size: 9.5px;" title="{{ $custPay->detail_text }}">
                                                <i class="mdi {{ $custPay->icon }} me-0.5"></i>{{ $custPay->label }}
                                            </span>
                                        </div>
                                        <div class="text-muted small mt-0.5" style="font-size: 10px;">
                                            Tgl Quote: {{ $item->date?->format('d/m/Y') }}
                                        </div>
                                    </td>

                                    {{-- Customer & Sales --}}
                                    <td>
                                        <div class="fw-semibold text-dark text-truncate" style="max-width: 180px;" title="{{ $clientName }}">
                                            {{ $clientName }}
                                        </div>
                                        <div class="text-muted small" style="font-size: 11px;">
                                            <i class="mdi mdi-account-outline me-1"></i>{{ $item->sales?->name ?? '-' }}
                                        </div>
                                    </td>

                                    {{-- Nilai Penawaran Pre-PPN & Inc PPN --}}
                                    <td class="text-end">
                                        <div class="fw-semibold text-dark">Rp {{ number_format($preTax, 0, ',', '.') }}</div>
                                        <div class="text-muted small" style="font-size: 10px;" title="Total Tagihan Termasuk PPN">
                                            Total: Rp {{ number_format($item->total, 0, ',', '.') }}
                                        </div>
                                    </td>

                                    {{-- Gross Fee --}}
                                    <td class="text-end text-danger fw-bold">
                                        Rp {{ number_format($item->fee, 0, ',', '.') }}
                                    </td>

                                    {{-- Pajak Fee 2026 --}}
                                    <td class="text-center">
                                        @if ($taxData->tax_amount > 0)
                                            <span class="badge bg-label-danger px-2 py-1 fw-bold" style="font-size: 10.5px;">
                                                {{ $taxData->tax_rate_label }} (-Rp {{ number_format($taxData->tax_amount, 0, ',', '.') }})
                                            </span>
                                        @else
                                            <span class="badge bg-label-success px-2 py-1" style="font-size: 10.5px;">
                                                0% (Bebas Pajak)
                                            </span>
                                        @endif
                                    </td>

                                    {{-- Nett Transfer (Yang Diterima) --}}
                                    <td class="text-end fw-bolder text-primary fs-6">
                                        Rp {{ number_format($taxData->net_fee, 0, ',', '.') }}
                                    </td>

                                    {{-- Info Rekening --}}
                                    <td>
                                        @if ($item->fee_bank_account)
                                            <div class="fw-semibold text-dark" style="font-size: 11.5px;">
                                                <span class="badge bg-label-info px-1.5 py-0.5 me-1" style="font-size: 9.5px;">{{ $item->fee_bank_name ?: 'Bank' }}</span>
                                                {{ $item->fee_bank_account }}
                                            </div>
                                            <div class="text-muted small text-truncate" style="font-size: 11px; max-width: 160px;" title="{{ $item->fee_bank_holder }}">
                                                a.n {{ $item->fee_bank_holder ?: '-' }}
                                            </div>
                                            @if ($item->fee_bank_branch)
                                                <div class="text-muted small text-truncate" style="font-size: 10px; max-width: 160px;" title="Cabang: {{ $item->fee_bank_branch }}">
                                                    <i class="mdi mdi-map-marker-outline me-0.5"></i>{{ $item->fee_bank_branch }}
                                                </div>
                                            @endif
                                        @else
                                            <span class="text-muted small fst-italic" style="font-size: 11px;">
                                                <i class="mdi mdi-alert-circle-outline text-warning me-0.5"></i>Rekening belum diisi
                                            </span>
                                        @endif
                                    </td>

                                    {{-- Status Pencairan Fee --}}
                                    <td class="text-center">
                                        @if ($item->fee_payment_status === 'paid')
                                            <span class="badge bg-success px-2.5 py-1 text-uppercase fw-bold" style="font-size: 10.5px;">
                                                <i class="mdi mdi-check me-0.5"></i> Paid
                                            </span>
                                            @if ($item->sourceBank)
                                                <div class="mt-1">
                                                    <span class="badge bg-label-info px-1.5 py-0.5" style="font-size: 9.5px;" title="Rekening Sumber: {{ $item->sourceBank->no_rek }} ({{ $item->sourceBank->nama_rek }})">
                                                        <i class="mdi mdi-bank me-0.5"></i>{{ $item->sourceBank->bank }}
                                                    </span>
                                                </div>
                                            @endif
                                            @if ($item->fee_transfer_date)
                                                <div class="text-muted small" style="font-size: 10px;">
                                                    {{ $item->fee_transfer_date->format('d/m/Y') }}
                                                </div>
                                            @endif
                                        @elseif ($item->fee_payment_status === 'pending_transfer')
                                            <span class="badge bg-warning text-dark px-2.5 py-1 text-uppercase fw-bold" style="font-size: 10.5px;">
                                                <i class="mdi mdi-clock-outline me-0.5"></i> Siap Transfer
                                            </span>
                                        @else
                                            <span class="badge bg-label-secondary px-2.5 py-1 text-uppercase fw-semibold" style="font-size: 10.5px;">
                                                Unpaid
                                            </span>
                                        @endif
                                    </td>

                                    {{-- Action Buttons --}}
                                    <td class="text-center">
                                        <div class="d-flex align-items-center justify-content-center gap-1">
                                            @if (!$isSales)
                                                {{-- Tombol Proses Transfer / Rekening Modal --}}
                                                <button type="button" class="btn btn-icon btn-sm btn-label-primary btn-open-disbursement"
                                                    title="Kelola Rekening & Status Transfer"
                                                    data-id="{{ $item->id }}"
                                                    data-no-quote="{{ $item->no_quote }}"
                                                    data-client="{{ $clientName }}"
                                                    data-sales="{{ $item->sales?->name ?? '-' }}"
                                                    data-pretax="Rp {{ number_format($preTax, 0, ',', '.') }}"
                                                    data-gross="Rp {{ number_format($item->fee, 0, ',', '.') }}"
                                                    data-tax-label="{{ $taxData->tax_rate_label }}"
                                                    data-tax-amount-num="{{ $taxData->tax_amount }}"
                                                    data-tax-amount-formatted="Rp {{ number_format($taxData->tax_amount, 0, ',', '.') }}"
                                                    data-net="Rp {{ number_format($taxData->net_fee, 0, ',', '.') }}"
                                                    data-source-bank-id="{{ $item->id_source_bank }}"
                                                    data-bank-name="{{ $item->fee_bank_name }}"
                                                    data-bank-branch="{{ $item->fee_bank_branch }}"
                                                    data-bank-account="{{ $item->fee_bank_account }}"
                                                    data-bank-holder="{{ $item->fee_bank_holder }}"
                                                    data-payment-status="{{ $item->fee_payment_status ?: 'unpaid' }}"
                                                    data-transfer-date="{{ $item->fee_transfer_date ? $item->fee_transfer_date->format('Y-m-d') : '' }}"
                                                    data-transfer-note="{{ $item->fee_transfer_note }}"
                                                    data-proof-url="{{ $item->fee_transfer_proof ? \Illuminate\Support\Facades\Storage::url($item->fee_transfer_proof) : '' }}"
                                                    data-cust-pay-status="{{ $custPay->status }}"
                                                    data-cust-pay-label="{{ $custPay->label }}"
                                                    data-cust-pay-badge="{{ $custPay->badge_class }}"
                                                    data-cust-pay-icon="{{ $custPay->icon }}"
                                                    data-cust-pay-detail="{{ $custPay->detail_text }}"
                                                    data-cust-pay-lunas="{{ $custPay->is_lunas ? '1' : '0' }}"
                                                    data-action-url="{{ route('finance.management-fee.update-disbursement', $item->id) }}">
                                                    <i class="mdi mdi-cash-fast"></i>
                                                </button>
                                            @endif

                                            {{-- Lihat Bukti Transfer --}}
                                            @if ($item->fee_transfer_proof)
                                                <a href="{{ \Illuminate\Support\Facades\Storage::url($item->fee_transfer_proof) }}" target="_blank"
                                                    class="btn btn-icon btn-sm btn-label-success" title="Lihat Bukti Transfer">
                                                    <i class="mdi mdi-file-document-check-outline"></i>
                                                </a>
                                            @endif

                                            {{-- Detail Smart Quote --}}
                                            <a href="{{ route('unit-quotation.show', $item->id) }}" target="_blank"
                                                class="btn btn-icon btn-sm btn-label-secondary" title="Buka Detail Smart Quote">
                                                <i class="mdi mdi-eye-outline"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center py-5 text-muted">
                                        <div class="d-flex flex-column align-items-center justify-content-center">
                                            <i class="mdi mdi-cash-remove text-muted opacity-50 mb-2" style="font-size: 48px;"></i>
                                            <h6 class="fw-bold mb-1 text-dark">Tidak Ada Data Fee dari Penawaran</h6>
                                            <p class="small text-muted mb-0">Belum ada penawaran PO received dengan catatan fee yang sesuai filter.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Quotation Pagination Footer --}}
                @if ($items->hasPages())
                    <div class="card-footer bg-light border-top py-2 px-4 d-flex justify-content-between align-items-center">
                        <span class="text-muted small">
                            Menampilkan {{ $items->firstItem() }} s/d {{ $items->lastItem() }} dari total {{ $items->total() }} data
                        </span>
                        <div>
                            {{ $items->appends(['tab' => 'quotation'])->links() }}
                        </div>
                    </div>
                @endif
            </div>

            {{-- ========================================================= --}}
            {{-- TAB 2: FEE MANUAL / KHUSUS                                --}}
            {{-- ========================================================= --}}
            <div class="tab-pane fade {{ $activeTab === 'manual' ? 'show active' : '' }}" id="tab-manual-pane" role="tabpanel">
                {{-- Filter Manual Header --}}
                <div class="bg-light border-bottom py-3 px-4">
                    <form action="{{ route('finance.management-fee.index') }}" method="GET" id="form-filter-manual">
                        <input type="hidden" name="tab" value="manual">
                        <input type="hidden" name="year" value="{{ request('year', 'all') }}">
                        <div class="row g-2 align-items-center">
                            {{-- Search Keyword --}}
                            <div class="col-lg-4 col-md-6 col-12">
                                <div class="input-group input-group-merge input-group-sm">
                                    <span class="input-group-text"><i class="mdi mdi-magnify"></i></span>
                                    <input type="text" class="form-control" name="manual_search" value="{{ request('manual_search') }}"
                                        placeholder="Cari Judul, No Ref, Customer, Rekening...">
                                </div>
                            </div>

                            {{-- Status Fee --}}
                            <div class="col-lg-2 col-md-3 col-6">
                                <select class="form-select form-select-sm" name="manual_fee_status" onchange="this.form.submit()">
                                    <option value="all" {{ request('manual_fee_status') == 'all' ? 'selected' : '' }}>Semua Status Fee</option>
                                    <option value="unpaid" {{ request('manual_fee_status') == 'unpaid' ? 'selected' : '' }}>🔴 Unpaid (Belum)</option>
                                    <option value="pending_transfer" {{ request('manual_fee_status') == 'pending_transfer' ? 'selected' : '' }}>🟡 Siap Ditransfer</option>
                                    <option value="paid" {{ request('manual_fee_status') == 'paid' ? 'selected' : '' }}>🟢 Paid (Sudah Ditransfer)</option>
                                </select>
                            </div>

                            {{-- Filter Customer / Company --}}
                            <div class="col-lg-3 col-md-3 col-6">
                                <select class="form-select form-select-sm" name="manual_client_id" onchange="this.form.submit()">
                                    <option value="all">Semua Company / Client</option>
                                    @foreach ($clientList as $cl)
                                        <option value="{{ $cl->id }}" {{ request('manual_client_id') == $cl->id ? 'selected' : '' }}>
                                            {{ $cl->company }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Date Filter & Actions --}}
                            <div class="col-lg-3 col-md-12 col-12 d-flex gap-1">
                                <input type="date" class="form-control form-control-sm" name="manual_start_date" value="{{ request('manual_start_date') }}" title="Dari Tanggal">
                                <input type="date" class="form-control form-control-sm" name="manual_end_date" value="{{ request('manual_end_date') }}" title="Sampai Tanggal">
                                <button type="submit" class="btn btn-sm btn-primary px-2" title="Terapkan Filter">
                                    <i class="mdi mdi-filter-variant"></i>
                                </button>
                                <a href="{{ route('finance.management-fee.index', ['tab' => 'manual', 'year' => request('year')]) }}" class="btn btn-sm btn-label-secondary px-2" title="Reset Filter">
                                    <i class="mdi mdi-refresh"></i>
                                </a>
                            </div>
                        </div>
                    </form>
                </div>

                {{-- Manual Table Data --}}
                <div class="table-responsive text-nowrap">
                    <table class="table table-hover align-middle mb-0" style="font-size: 12.5px;">
                        <thead class="table-light" style="font-size: 11.5px;">
                            <tr>
                                <th class="text-center" style="width: 4%;">No</th>
                                <th style="width: 18%;">Tanggal &amp; Referensi</th>
                                <th style="width: 18%;">Company / Customer</th>
                                <th class="text-end" style="width: 12%;">Gross Fee</th>
                                <th class="text-center" style="width: 12%;">Pajak (2026)</th>
                                <th class="text-end fw-bold" style="width: 12%;">Nett Transfer</th>
                                <th style="width: 15%;">Info Rekening</th>
                                <th class="text-center" style="width: 10%;">Status Pencairan</th>
                                <th class="text-center" style="width: 9%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $noManual = $manualItems->firstItem() ?: 1; @endphp
                            @forelse ($manualItems as $mItem)
                                @php
                                    $mTaxData = $mItem->fee_tax_data;
                                    $compName = $mItem->company_name;
                                @endphp
                                <tr>
                                    <td class="text-center text-muted fw-semibold">{{ $noManual++ }}</td>

                                    {{-- Tanggal & Referensi / Judul Fee --}}
                                    <td>
                                        <div class="fw-bold text-dark">{{ $mItem->title }}</div>
                                        <div class="d-flex flex-wrap gap-1 align-items-center mt-0.5">
                                            @if ($mItem->reference_no)
                                                <span class="badge bg-label-dark px-1.5 py-0.5" style="font-size: 9.5px;">
                                                    <i class="mdi mdi-pound me-0.5"></i>Ref: {{ $mItem->reference_no }}
                                                </span>
                                            @endif
                                            <span class="badge bg-label-secondary px-1.5 py-0.5" style="font-size: 9.5px;">
                                                <i class="mdi mdi-calendar-blank-outline me-0.5"></i>{{ $mItem->date?->format('d/m/Y') }}
                                            </span>
                                        </div>
                                    </td>

                                    {{-- Company / Customer & Creator --}}
                                    <td>
                                        <div class="fw-semibold text-dark text-truncate" style="max-width: 180px;" title="{{ $compName }}">
                                            <i class="mdi mdi-domain text-primary me-1"></i>{{ $compName }}
                                        </div>
                                        <div class="text-muted small" style="font-size: 10.5px;">
                                            Dibuat: {{ $mItem->creator?->name ?? 'User' }}
                                        </div>
                                    </td>

                                    {{-- Gross Fee --}}
                                    <td class="text-end text-danger fw-bold">
                                        Rp {{ number_format($mItem->gross_fee, 0, ',', '.') }}
                                    </td>

                                    {{-- Pajak Fee 2026 --}}
                                    <td class="text-center">
                                        @if ($mTaxData->tax_amount > 0)
                                            <span class="badge bg-label-danger px-2 py-1 fw-bold" style="font-size: 10.5px;">
                                                {{ $mTaxData->tax_rate_label }} (-Rp {{ number_format($mTaxData->tax_amount, 0, ',', '.') }})
                                            </span>
                                        @else
                                            <span class="badge bg-label-success px-2 py-1" style="font-size: 10.5px;">
                                                0% (Bebas Pajak)
                                            </span>
                                        @endif
                                    </td>

                                    {{-- Nett Transfer --}}
                                    <td class="text-end fw-bolder text-primary fs-6">
                                        Rp {{ number_format($mTaxData->net_fee, 0, ',', '.') }}
                                    </td>

                                    {{-- Info Rekening --}}
                                    <td>
                                        @if ($mItem->fee_bank_account)
                                            <div class="fw-semibold text-dark" style="font-size: 11.5px;">
                                                <span class="badge bg-label-info px-1.5 py-0.5 me-1" style="font-size: 9.5px;">{{ $mItem->fee_bank_name ?: 'Bank' }}</span>
                                                {{ $mItem->fee_bank_account }}
                                            </div>
                                            <div class="text-muted small text-truncate" style="font-size: 11px; max-width: 160px;" title="{{ $mItem->fee_bank_holder }}">
                                                a.n {{ $mItem->fee_bank_holder ?: '-' }}
                                            </div>
                                            @if ($mItem->fee_bank_branch)
                                                <div class="text-muted small text-truncate" style="font-size: 10px; max-width: 160px;" title="Cabang: {{ $mItem->fee_bank_branch }}">
                                                    <i class="mdi mdi-map-marker-outline me-0.5"></i>{{ $mItem->fee_bank_branch }}
                                                </div>
                                            @endif
                                        @else
                                            <span class="text-muted small fst-italic" style="font-size: 11px;">
                                                <i class="mdi mdi-alert-circle-outline text-warning me-0.5"></i>Rekening belum diisi
                                            </span>
                                        @endif
                                    </td>

                                    {{-- Status Pencairan --}}
                                    <td class="text-center">
                                        @if ($mItem->fee_payment_status === 'paid')
                                            <span class="badge bg-success px-2.5 py-1 text-uppercase fw-bold" style="font-size: 10.5px;">
                                                <i class="mdi mdi-check me-0.5"></i> Paid
                                            </span>
                                            @if ($mItem->sourceBank)
                                                <div class="mt-1">
                                                    <span class="badge bg-label-info px-1.5 py-0.5" style="font-size: 9.5px;" title="Rekening Sumber: {{ $mItem->sourceBank->no_rek }} ({{ $mItem->sourceBank->nama_rek }})">
                                                        <i class="mdi mdi-bank me-0.5"></i>{{ $mItem->sourceBank->bank }}
                                                    </span>
                                                </div>
                                            @endif
                                            @if ($mItem->fee_transfer_date)
                                                <div class="text-muted small" style="font-size: 10px;">
                                                    {{ $mItem->fee_transfer_date->format('d/m/Y') }}
                                                </div>
                                            @endif
                                        @elseif ($mItem->fee_payment_status === 'pending_transfer')
                                            <span class="badge bg-warning text-dark px-2.5 py-1 text-uppercase fw-bold" style="font-size: 10.5px;">
                                                <i class="mdi mdi-clock-outline me-0.5"></i> Siap Transfer
                                            </span>
                                        @else
                                            <span class="badge bg-label-secondary px-2.5 py-1 text-uppercase fw-semibold" style="font-size: 10.5px;">
                                                Unpaid
                                            </span>
                                        @endif
                                    </td>

                                    {{-- Action Buttons --}}
                                    <td class="text-center">
                                        <div class="d-flex align-items-center justify-content-center gap-1">
                                            @if (!$isSales)
                                                {{-- Tombol Pencairan Manual --}}
                                                <button type="button" class="btn btn-icon btn-sm btn-label-primary btn-open-manual-disbursement"
                                                    title="Proses Pencairan & Bukti Transfer"
                                                    data-id="{{ $mItem->id }}"
                                                    data-title="{{ $mItem->title }}"
                                                    data-company="{{ $compName }}"
                                                    data-gross="Rp {{ number_format($mItem->gross_fee, 0, ',', '.') }}"
                                                    data-tax-label="{{ $mTaxData->tax_rate_label }}"
                                                    data-tax-amount-num="{{ $mTaxData->tax_amount }}"
                                                    data-tax-amount-formatted="Rp {{ number_format($mTaxData->tax_amount, 0, ',', '.') }}"
                                                    data-net="Rp {{ number_format($mTaxData->net_fee, 0, ',', '.') }}"
                                                    data-source-bank-id="{{ $mItem->id_source_bank }}"
                                                    data-bank-name="{{ $mItem->fee_bank_name }}"
                                                    data-bank-branch="{{ $mItem->fee_bank_branch }}"
                                                    data-bank-account="{{ $mItem->fee_bank_account }}"
                                                    data-bank-holder="{{ $mItem->fee_bank_holder }}"
                                                    data-payment-status="{{ $mItem->fee_payment_status ?: 'unpaid' }}"
                                                    data-transfer-date="{{ $mItem->fee_transfer_date ? $mItem->fee_transfer_date->format('Y-m-d') : '' }}"
                                                    data-transfer-note="{{ $mItem->fee_transfer_note }}"
                                                    data-proof-url="{{ $mItem->fee_transfer_proof ? \Illuminate\Support\Facades\Storage::url($mItem->fee_transfer_proof) : '' }}"
                                                    data-action-url="{{ route('finance.management-fee.update-manual-disbursement', $mItem->id) }}">
                                                    <i class="mdi mdi-cash-fast"></i>
                                                </button>
                                            @endif

                                            {{-- Lihat Bukti Transfer --}}
                                            @if ($mItem->fee_transfer_proof)
                                                <a href="{{ \Illuminate\Support\Facades\Storage::url($mItem->fee_transfer_proof) }}" target="_blank"
                                                    class="btn btn-icon btn-sm btn-label-success" title="Lihat Bukti Transfer">
                                                    <i class="mdi mdi-file-document-check-outline"></i>
                                                </a>
                                            @endif

                                            @if (!$isSales)
                                                {{-- Edit Fee Manual --}}
                                                <button type="button" class="btn btn-icon btn-sm btn-label-warning btn-edit-manual-fee"
                                                    title="Edit Data Fee Manual"
                                                    data-id="{{ $mItem->id }}"
                                                    data-client-id="{{ $mItem->client_id }}"
                                                    data-custom-company="{{ $mItem->custom_company_name }}"
                                                    data-date="{{ $mItem->date ? $mItem->date->format('Y-m-d') : '' }}"
                                                    data-title="{{ $mItem->title }}"
                                                    data-ref="{{ $mItem->reference_no }}"
                                                    data-gross="{{ $mItem->gross_fee }}"
                                                    data-source-bank-id="{{ $mItem->id_source_bank }}"
                                                    data-bank-name="{{ $mItem->fee_bank_name }}"
                                                    data-bank-branch="{{ $mItem->fee_bank_branch }}"
                                                    data-bank-account="{{ $mItem->fee_bank_account }}"
                                                    data-bank-holder="{{ $mItem->fee_bank_holder }}"
                                                    data-payment-status="{{ $mItem->fee_payment_status ?: 'unpaid' }}"
                                                    data-transfer-date="{{ $mItem->fee_transfer_date ? $mItem->fee_transfer_date->format('Y-m-d') : '' }}"
                                                    data-transfer-note="{{ $mItem->fee_transfer_note }}"
                                                    data-proof-url="{{ $mItem->fee_transfer_proof ? \Illuminate\Support\Facades\Storage::url($mItem->fee_transfer_proof) : '' }}"
                                                    data-update-url="{{ route('finance.management-fee.update-manual', $mItem->id) }}">
                                                    <i class="mdi mdi-pencil-outline"></i>
                                                </button>

                                                {{-- Hapus Fee Manual --}}
                                                <form action="{{ route('finance.management-fee.destroy-manual', $mItem->id) }}" method="POST" class="d-inline form-delete-manual">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-icon btn-sm btn-label-danger" title="Hapus Fee Manual" onclick="return confirm('Apakah Anda yakin ingin menghapus data Fee Manual ini?')">
                                                        <i class="mdi mdi-trash-can-outline"></i>
                                                    </button>
                                                </form>
                                            @else
                                                @if (!$mItem->fee_transfer_proof)
                                                    <span class="text-muted small">-</span>
                                                @endif
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-5 text-muted">
                                        <div class="d-flex flex-column align-items-center justify-content-center">
                                            <i class="mdi mdi-file-plus-outline text-muted opacity-50 mb-2" style="font-size: 48px;"></i>
                                            <h6 class="fw-bold mb-1 text-dark">Belum Ada Data Fee Manual</h6>
                                            <p class="small text-muted mb-2">Belum ada data fee manual yang tercatat.</p>
                                            @if (!$isSales)
                                                <button type="button" class="btn btn-primary btn-sm" id="btn-add-manual-fee-empty">
                                                    <i class="mdi mdi-plus-circle-outline me-1"></i> Tambah Fee Manual Sekarang
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Manual Pagination Footer --}}
                @if ($manualItems->hasPages())
                    <div class="card-footer bg-light border-top py-2 px-4 d-flex justify-content-between align-items-center">
                        <span class="text-muted small">
                            Menampilkan {{ $manualItems->firstItem() }} s/d {{ $manualItems->lastItem() }} dari total {{ $manualItems->total() }} data
                        </span>
                        <div>
                            {{ $manualItems->appends(['tab' => 'manual'])->links() }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- ========================================================================= --}}
{{-- MODAL 1: PROSES PENCAIRAN FEE DARI QUOTATION                              --}}
{{-- ========================================================================= --}}
<div class="modal fade" id="modalDisbursement" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header border-bottom py-3">
                <div class="d-flex align-items-center gap-2">
                    <div class="avatar avatar-sm bg-label-primary rounded-circle d-flex align-items-center justify-content-center">
                        <i class="mdi mdi-cash-fast fs-4"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0">Proses Pencairan Management Fee</h5>
                        <span class="text-muted small" style="font-size: 11px;" id="modal-disburse-quote-title">#Quote</span>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="POST" id="form-disbursement" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-3 p-md-4">
                    {{-- Status Pembayaran Customer Alert Banner --}}
                    <div id="modal-cust-pay-banner" class="alert py-2.5 px-3 mb-3 rounded-3 border-0 d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <div class="d-flex align-items-center gap-2">
                            <i class="mdi mdi-cash-check fs-4" id="modal-cust-pay-icon"></i>
                            <div>
                                <span class="text-muted small d-block" style="font-size: 10.5px;">Status Pembayaran Customer:</span>
                                <strong class="text-dark small" id="modal-cust-pay-title">-</strong>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge" id="modal-cust-pay-badge" style="font-size: 11px;">-</span>
                        </div>
                    </div>

                    {{-- Detail Nominal & Pajak Fee 2026 Summary --}}
                    <div class="p-3 rounded-3 bg-light border mb-4">
                        <div class="row g-2 text-center align-items-center">
                            <div class="col-sm-3 col-6">
                                <span class="text-muted small d-block" style="font-size: 10px;">Nilai Quote (Pre-PPN)</span>
                                <span class="fw-bold text-dark" id="modal-d-pretax" style="font-size: 12.5px;">-</span>
                            </div>
                            <div class="col-sm-3 col-6">
                                <span class="text-muted small d-block" style="font-size: 10px;">Gross Fee (Diputuskan)</span>
                                <span class="fw-bold text-danger" id="modal-d-gross" style="font-size: 12.5px;">-</span>
                            </div>
                            <div class="col-sm-3 col-6">
                                <span class="text-muted small d-block" style="font-size: 10px;" id="modal-d-tax-label">Pajak Fee (2026)</span>
                                <span class="fw-bold text-danger" id="modal-d-tax-amount" style="font-size: 12.5px;">-</span>
                            </div>
                            <div class="col-sm-3 col-6">
                                <span class="text-muted small d-block" style="font-size: 10px;">Nett Fee yang Ditransfer</span>
                                <span class="fw-bolder text-primary fs-6" id="modal-d-net">-</span>
                            </div>
                        </div>
                    </div>

                    {{-- Form Data Rekening --}}
                    <h6 class="fw-bold text-dark mb-2" style="font-size: 12.5px;">
                        <i class="mdi mdi-bank-outline me-1 text-primary"></i> Data Rekening Tujuan Penerima Fee
                    </h6>
                    <div class="row g-3 mb-3">
                        <div class="col-md-3 col-6">
                            <label class="form-label small fw-semibold" for="input_fee_bank_name">Nama Bank</label>
                            <input type="text" class="form-control form-control-sm" id="input_fee_bank_name" name="fee_bank_name"
                                placeholder="Contoh: BCA, Mandiri">
                        </div>
                        <div class="col-md-3 col-6">
                            <label class="form-label small fw-semibold" for="input_fee_bank_branch">Cabang Bank <span class="text-muted fw-normal">(Opsional)</span></label>
                            <input type="text" class="form-control form-control-sm" id="input_fee_bank_branch" name="fee_bank_branch"
                                placeholder="Contoh: KCP Dago / BDG">
                        </div>
                        <div class="col-md-3 col-6">
                            <label class="form-label small fw-semibold" for="input_fee_bank_account">Nomor Rekening</label>
                            <input type="text" class="form-control form-control-sm" id="input_fee_bank_account" name="fee_bank_account"
                                placeholder="Contoh: 1234567890">
                        </div>
                        <div class="col-md-3 col-6">
                            <label class="form-label small fw-semibold" for="input_fee_bank_holder">Nama Pemilik (A/N)</label>
                            <input type="text" class="form-control form-control-sm" id="input_fee_bank_holder" name="fee_bank_holder"
                                placeholder="Contoh: John Doe">
                        </div>
                    </div>

                    <div class="divider my-3">
                        <div class="divider-text text-muted small" style="font-size: 11px;">Status &amp; Eksekusi Transfer</div>
                    </div>

                    {{-- Form Status Transfer & Upload Bukti --}}
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold" for="input_fee_payment_status">Status Pencairan Fee <span class="text-danger">*</span></label>
                            <select class="form-select form-select-sm" id="input_fee_payment_status" name="fee_payment_status" required>
                                <option value="unpaid">🔴 Belum Ditransfer (Unpaid)</option>
                                <option value="pending_transfer">🟡 Siap Ditransfer (Pending Action)</option>
                                <option value="paid">🟢 Sudah Ditransfer (Paid)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold" for="input_fee_transfer_date">Tanggal Transfer</label>
                            <input type="date" class="form-control form-control-sm" id="input_fee_transfer_date" name="fee_transfer_date"
                                value="{{ date('Y-m-d') }}">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label small fw-semibold" for="input_fee_source_bank">
                                <i class="mdi mdi-bank text-primary me-1"></i> Rekening Bank Kantor (Sumber Dana Transfer) <span class="text-danger">*</span>
                            </label>
                            <select class="form-select form-select-sm" id="input_fee_source_bank" name="id_source_bank">
                                <option value="">-- Pilih Rekening Kantor Asal Transfer --</option>
                                @foreach ($banks as $b)
                                    <option value="{{ $b->id }}">{{ $b->bank }} - {{ $b->no_rek }} (a.n {{ $b->nama_rek }}) [Saldo: Rp {{ number_format($b->saldo, 0, ',', '.') }}]</option>
                                @endforeach
                            </select>
                            <small class="text-muted" style="font-size: 10.5px;">Wajib dipilih jika status "Sudah Ditransfer (Paid)". Saldo bank akan otomatis berkurang &amp; tercatat sebagai Debet di Rekening Koran.</small>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-12">
                            <label class="form-label small fw-semibold d-flex justify-content-between align-items-center mb-1.5">
                                <span>Upload Bukti Transfer <span class="text-muted">(JPG, PNG, PDF maks 5MB)</span></span>
                                <span class="badge bg-label-info fw-normal" style="font-size: 10.5px;">
                                    <i class="mdi mdi-content-paste me-1"></i>Support Paste (Ctrl + V)
                                </span>
                            </label>

                            {{-- Drop & Paste Area --}}
                            <label for="input_fee_transfer_proof" id="paste-drop-zone" class="border border-2 border-dashed rounded-3 p-3 text-center bg-light position-relative d-block mb-0" style="cursor: pointer; transition: all 0.2s ease;">
                                <input type="file" class="d-none" id="input_fee_transfer_proof" name="fee_transfer_proof" accept="image/*,.pdf">
                                
                                <div id="paste-prompt" class="py-1">
                                    <div class="avatar avatar-md bg-label-primary rounded-circle mx-auto mb-2 d-flex align-items-center justify-content-center">
                                        <i class="mdi mdi-cloud-upload-outline fs-3"></i>
                                    </div>
                                    <p class="mb-1 fw-semibold text-dark small">
                                        Klik untuk pilih file, drag &amp; drop, atau tekan <kbd class="bg-dark text-white px-1.5 py-0.5 rounded" style="font-size: 11px;">Ctrl + V</kbd> untuk Paste Screenshot
                                    </p>
                                    <span class="text-muted small" style="font-size: 11px;">Mendukung format JPG, PNG, PDF (Maks. 5 MB)</span>
                                </div>

                                {{-- Live Preview File Baru --}}
                                <div id="new-file-preview" class="d-none">
                                    <div class="d-flex align-items-center justify-content-between p-2 rounded bg-white border shadow-sm">
                                        <div class="d-flex align-items-center gap-2 text-start overflow-hidden">
                                            <img id="pasted-image-thumb" src="" alt="Preview" class="rounded object-fit-cover border d-none" style="width: 50px; height: 50px;">
                                            <div id="pasted-pdf-icon" class="avatar avatar-sm bg-label-danger rounded d-flex align-items-center justify-content-center flex-shrink-0 d-none" style="width: 50px; height: 50px;">
                                                <i class="mdi mdi-file-pdf-box fs-3"></i>
                                            </div>
                                            <div class="text-truncate">
                                                <div class="fw-bold text-dark small text-truncate" id="new-file-name">bukti.png</div>
                                                <div class="d-flex gap-1 mt-0.5">
                                                    <span class="badge bg-label-secondary px-1.5 py-0.5" id="new-file-size" style="font-size: 10px;">-</span>
                                                    <span class="badge bg-label-success px-1.5 py-0.5" id="new-file-source" style="font-size: 10px;">Siap Diupload</span>
                                                </div>
                                            </div>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-icon btn-label-danger flex-shrink-0 ms-2" id="btn-remove-new-file" title="Hapus / Pilih Ulang">
                                            <i class="mdi mdi-close"></i>
                                        </button>
                                    </div>
                                </div>
                            </label>

                            {{-- Bukti Transfer Tersimpan Sebelumnya --}}
                            <input type="hidden" name="delete_fee_transfer_proof" id="delete_fee_transfer_proof" value="0">

                            <div id="proof-preview-wrap" class="mt-2 d-none">
                                <div class="alert alert-secondary py-1.5 px-2.5 mb-0 rounded d-flex align-items-center justify-content-between flex-wrap gap-2" style="font-size: 11.5px;">
                                    <span class="text-muted">
                                        <i class="mdi mdi-file-check-outline text-success me-1"></i> Bukti transfer sebelumnya tersimpan
                                    </span>
                                    <div class="d-flex align-items-center gap-1.5">
                                        <a href="#" target="_blank" id="proof-preview-link" class="btn btn-xs btn-label-primary px-2 py-0.5 fw-semibold">
                                            <i class="mdi mdi-open-in-new me-0.5"></i> Lihat File
                                        </a>
                                        <button type="button" class="btn btn-xs btn-icon btn-label-danger" id="btn-delete-existing-proof" title="Hapus Bukti Transfer">
                                            <i class="mdi mdi-trash-can-outline"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div id="proof-deleted-notice" class="mt-2 d-none">
                                <div class="alert alert-warning py-1.5 px-2.5 mb-0 rounded d-flex align-items-center justify-content-between flex-wrap gap-2" style="font-size: 11.5px;">
                                    <span class="text-dark">
                                        <i class="mdi mdi-alert-circle-outline text-warning me-1"></i> Bukti transfer sebelumnya akan dihapus saat disimpan.
                                    </span>
                                    <button type="button" class="btn btn-xs btn-label-secondary px-2 py-0.5" id="btn-undo-delete-proof">
                                        <i class="mdi mdi-undo me-0.5"></i> Batal Hapus
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-0">
                        <label class="form-label small fw-semibold" for="input_fee_transfer_note">Catatan / Nomor Referensi Transfer <span class="text-muted">(Opsional)</span></label>
                        <textarea class="form-control form-control-sm" id="input_fee_transfer_note" name="fee_transfer_note" rows="2"
                            placeholder="Contoh: No ref transfer bank #TRX123456789 atau catatan transfer..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top py-2 px-3">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="mdi mdi-content-save-outline me-1"></i> Simpan Status Pencairan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ========================================================================= --}}
{{-- MODAL 2: TAMBAH / EDIT MANUAL MANAGEMENT FEE                              --}}
{{-- ========================================================================= --}}
<div class="modal fade" id="modalManualFee" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header border-bottom py-3">
                <div class="d-flex align-items-center gap-2">
                    <div class="avatar avatar-sm bg-label-success rounded-circle d-flex align-items-center justify-content-center">
                        <i class="mdi mdi-calculator-variant fs-4"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0" id="manual-fee-modal-title">Tambah Fee Manual</h5>
                        <span class="text-muted small" style="font-size: 11px;">Input management fee mandiri tanpa penawaran quote</span>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('finance.management-fee.store-manual') }}" method="POST" id="form-manual-fee" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-3 p-md-4">
                    {{-- Company / Customer Selection --}}
                    <div class="row g-3 mb-3">
                        <div class="col-md-7">
                            <label class="form-label small fw-semibold" for="manual_client_id">Pilih Customer / Company <span class="text-danger">*</span></label>
                            <select class="form-select form-select-sm" id="manual_client_id" name="client_id">
                                <option value="">-- Pilih dari Daftar Customer --</option>
                                @foreach ($clientList as $cl)
                                    <option value="{{ $cl->id }}">{{ $cl->company }}</option>
                                @endforeach
                            </select>
                            <small class="text-muted" style="font-size: 10.5px;">Atau isi nama company manual di bawah jika tidak ada di list.</small>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label small fw-semibold" for="manual_custom_company_name">Company Manual <span class="text-muted fw-normal">(Opsional)</span></label>
                            <input type="text" class="form-control form-control-sm" id="manual_custom_company_name" name="custom_company_name"
                                placeholder="Ketik nama PT / Perusahaan...">
                        </div>
                    </div>

                    {{-- Tanggal, Judul / Keterangan & No Referensi --}}
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold" for="manual_date">Tanggal Fee <span class="text-danger">*</span></label>
                            <input type="date" class="form-control form-control-sm" id="manual_date" name="date" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold" for="manual_reference_no">No Referensi / Dokumen <span class="text-muted fw-normal">(Opsional)</span></label>
                            <input type="text" class="form-control form-control-sm" id="manual_reference_no" name="reference_no" placeholder="Contoh: REF/2026/001">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold" for="manual_title">Keterangan / Judul Fee <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-sm" id="manual_title" name="title" placeholder="Contoh: Fee Project Maintenance" required>
                        </div>
                    </div>

                    {{-- Gross Fee & Live Tax Policy 2026 Display --}}
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold" for="manual_gross_fee">Nominal Gross Fee (Rp) <span class="text-danger">*</span></label>
                            <div class="input-group input-group-merge input-group-sm">
                                <span class="input-group-text fw-bold">Rp</span>
                                <input type="number" class="form-control" id="manual_gross_fee" name="gross_fee" min="1" step="any" placeholder="0" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            {{-- Live Tax Preview Box --}}
                            <div class="p-2.5 rounded-3 bg-light border h-100 d-flex flex-column justify-content-center">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="text-muted small" style="font-size: 11px;">Tarif Pajak (2026):</span>
                                    <span class="badge bg-label-info fw-bold" id="manual_calc_tax_rate">0% (Bebas Pajak)</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="text-muted small" style="font-size: 11px;">Potongan PPh:</span>
                                    <span class="fw-bold text-danger small" id="manual_calc_tax_amount">Rp 0</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center border-top pt-1 mt-1">
                                    <span class="text-dark fw-bold small">Nett Diterima:</span>
                                    <span class="fw-bolder text-primary" id="manual_calc_net_fee">Rp 0</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Data Rekening Tujuan --}}
                    <h6 class="fw-bold text-dark mb-2 mt-4" style="font-size: 12.5px;">
                        <i class="mdi mdi-bank-outline me-1 text-primary"></i> Data Rekening Tujuan Penerima Fee
                    </h6>
                    <div class="row g-3 mb-3">
                        <div class="col-md-3 col-6">
                            <label class="form-label small fw-semibold" for="manual_bank_name">Nama Bank</label>
                            <input type="text" class="form-control form-control-sm" id="manual_bank_name" name="fee_bank_name" placeholder="Contoh: BCA, Mandiri">
                        </div>
                        <div class="col-md-3 col-6">
                            <label class="form-label small fw-semibold" for="manual_bank_branch">Cabang Bank <span class="text-muted fw-normal">(Opsional)</span></label>
                            <input type="text" class="form-control form-control-sm" id="manual_bank_branch" name="fee_bank_branch" placeholder="Contoh: KCP Dago / BDG">
                        </div>
                        <div class="col-md-3 col-6">
                            <label class="form-label small fw-semibold" for="manual_bank_account">Nomor Rekening</label>
                            <input type="text" class="form-control form-control-sm" id="manual_bank_account" name="fee_bank_account" placeholder="Contoh: 1234567890">
                        </div>
                        <div class="col-md-3 col-6">
                            <label class="form-label small fw-semibold" for="manual_bank_holder">Nama Pemilik (A/N)</label>
                            <input type="text" class="form-control form-control-sm" id="manual_bank_holder" name="fee_bank_holder" placeholder="Contoh: John Doe">
                        </div>
                    </div>

                    <div class="divider my-3">
                        <div class="divider-text text-muted small" style="font-size: 11px;">Status &amp; Eksekusi Transfer</div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold" for="manual_fee_payment_status">Status Pencairan Fee <span class="text-danger">*</span></label>
                            <select class="form-select form-select-sm" id="manual_fee_payment_status" name="fee_payment_status" required>
                                <option value="unpaid">🔴 Belum Ditransfer (Unpaid)</option>
                                <option value="pending_transfer">🟡 Siap Ditransfer (Pending Action)</option>
                                <option value="paid">🟢 Sudah Ditransfer (Paid)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold" for="manual_fee_transfer_date">Tanggal Transfer</label>
                            <input type="date" class="form-control form-control-sm" id="manual_fee_transfer_date" name="fee_transfer_date" value="{{ date('Y-m-d') }}">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label small fw-semibold" for="manual_source_bank">
                                <i class="mdi mdi-bank text-success me-1"></i> Rekening Bank Kantor (Sumber Dana Transfer) <span class="text-danger">*</span>
                            </label>
                            <select class="form-select form-select-sm" id="manual_source_bank" name="id_source_bank">
                                <option value="">-- Pilih Rekening Kantor Asal Transfer --</option>
                                @foreach ($banks as $b)
                                    <option value="{{ $b->id }}">{{ $b->bank }} - {{ $b->no_rek }} (a.n {{ $b->nama_rek }}) [Saldo: Rp {{ number_format($b->saldo, 0, ',', '.') }}]</option>
                                @endforeach
                            </select>
                            <small class="text-muted" style="font-size: 10.5px;">Wajib dipilih jika status "Sudah Ditransfer (Paid)". Saldo bank akan otomatis berkurang &amp; tercatat sebagai Debet di Rekening Koran.</small>
                        </div>
                    </div>

                    {{-- Upload Bukti Transfer Manual --}}
                    <div class="row g-3 mb-3">
                        <div class="col-md-12">
                            <label class="form-label small fw-semibold d-flex justify-content-between align-items-center mb-1.5">
                                <span>Upload Bukti Transfer <span class="text-muted">(JPG, PNG, PDF maks 5MB)</span></span>
                                <span class="badge bg-label-info fw-normal" style="font-size: 10.5px;">
                                    <i class="mdi mdi-content-paste me-1"></i>Support Paste (Ctrl + V)
                                </span>
                            </label>

                            <label for="manual_fee_transfer_proof" id="manual-paste-drop-zone" class="border border-2 border-dashed rounded-3 p-3 text-center bg-light position-relative d-block mb-0" style="cursor: pointer; transition: all 0.2s ease;">
                                <input type="file" class="d-none" id="manual_fee_transfer_proof" name="fee_transfer_proof" accept="image/*,.pdf">
                                
                                <div id="manual-paste-prompt" class="py-1">
                                    <div class="avatar avatar-md bg-label-success rounded-circle mx-auto mb-2 d-flex align-items-center justify-content-center">
                                        <i class="mdi mdi-cloud-upload-outline fs-3"></i>
                                    </div>
                                    <p class="mb-1 fw-semibold text-dark small">
                                        Klik untuk pilih file, drag &amp; drop, atau tekan <kbd class="bg-dark text-white px-1.5 py-0.5 rounded" style="font-size: 11px;">Ctrl + V</kbd> untuk Paste Screenshot
                                    </p>
                                    <span class="text-muted small" style="font-size: 11px;">Mendukung format JPG, PNG, PDF (Maks. 5 MB)</span>
                                </div>

                                {{-- Live Preview File Baru Manual --}}
                                <div id="manual-new-file-preview" class="d-none">
                                    <div class="d-flex align-items-center justify-content-between p-2 rounded bg-white border shadow-sm">
                                        <div class="d-flex align-items-center gap-2 text-start overflow-hidden">
                                            <img id="manual-pasted-image-thumb" src="" alt="Preview" class="rounded object-fit-cover border d-none" style="width: 50px; height: 50px;">
                                            <div id="manual-pasted-pdf-icon" class="avatar avatar-sm bg-label-danger rounded d-flex align-items-center justify-content-center flex-shrink-0 d-none" style="width: 50px; height: 50px;">
                                                <i class="mdi mdi-file-pdf-box fs-3"></i>
                                            </div>
                                            <div class="text-truncate">
                                                <div class="fw-bold text-dark small text-truncate" id="manual-new-file-name">bukti.png</div>
                                                <div class="d-flex gap-1 mt-0.5">
                                                    <span class="badge bg-label-secondary px-1.5 py-0.5" id="manual-new-file-size" style="font-size: 10px;">-</span>
                                                    <span class="badge bg-label-success px-1.5 py-0.5" id="manual-new-file-source" style="font-size: 10px;">Siap Diupload</span>
                                                </div>
                                            </div>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-icon btn-label-danger flex-shrink-0 ms-2" id="btn-manual-remove-new-file" title="Hapus / Pilih Ulang">
                                            <i class="mdi mdi-close"></i>
                                        </button>
                                    </div>
                                </div>
                            </label>

                            {{-- Bukti Transfer Tersimpan Sebelumnya (Mode Edit) --}}
                            <input type="hidden" name="delete_fee_transfer_proof" id="manual_delete_fee_transfer_proof" value="0">

                            <div id="manual-proof-preview-wrap" class="mt-2 d-none">
                                <div class="alert alert-secondary py-1.5 px-2.5 mb-0 rounded d-flex align-items-center justify-content-between flex-wrap gap-2" style="font-size: 11.5px;">
                                    <span class="text-muted">
                                        <i class="mdi mdi-file-check-outline text-success me-1"></i> Bukti transfer tersimpan
                                    </span>
                                    <div class="d-flex align-items-center gap-1.5">
                                        <a href="#" target="_blank" id="manual-proof-preview-link" class="btn btn-xs btn-label-primary px-2 py-0.5 fw-semibold">
                                            <i class="mdi mdi-open-in-new me-0.5"></i> Lihat File
                                        </a>
                                        <button type="button" class="btn btn-xs btn-icon btn-label-danger" id="btn-manual-delete-existing-proof" title="Hapus Bukti Transfer">
                                            <i class="mdi mdi-trash-can-outline"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div id="manual-proof-deleted-notice" class="mt-2 d-none">
                                <div class="alert alert-warning py-1.5 px-2.5 mb-0 rounded d-flex align-items-center justify-content-between flex-wrap gap-2" style="font-size: 11.5px;">
                                    <span class="text-dark">
                                        <i class="mdi mdi-alert-circle-outline text-warning me-1"></i> Bukti transfer sebelumnya akan dihapus saat disimpan.
                                    </span>
                                    <button type="button" class="btn btn-xs btn-label-secondary px-2 py-0.5" id="btn-manual-undo-delete-proof">
                                        <i class="mdi mdi-undo me-0.5"></i> Batal Hapus
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-0">
                        <label class="form-label small fw-semibold" for="manual_fee_transfer_note">Catatan / Keterangan Tambahan <span class="text-muted">(Opsional)</span></label>
                        <textarea class="form-control form-control-sm" id="manual_fee_transfer_note" name="fee_transfer_note" rows="2"
                            placeholder="Catatan transfer, no invoice, atau rincian tambahan..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top py-2 px-3">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary" id="btn-submit-manual-fee">
                        <i class="mdi mdi-content-save-outline me-1"></i> Simpan Data Fee
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ========================================================================= --}}
{{-- MODAL 3: PENCAIRAN FEE MANUAL CEPAT                                       --}}
{{-- ========================================================================= --}}
<div class="modal fade" id="modalManualDisbursement" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header border-bottom py-3">
                <div class="d-flex align-items-center gap-2">
                    <div class="avatar avatar-sm bg-label-success rounded-circle d-flex align-items-center justify-content-center">
                        <i class="mdi mdi-cash-fast fs-4"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0">Pencairan Fee Manual</h5>
                        <span class="text-muted small" style="font-size: 11px;" id="modal-mdisburse-title">-</span>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="POST" id="form-manual-disbursement" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-3 p-md-4">
                    {{-- Detail Nominal & Pajak Summary --}}
                    <div class="p-3 rounded-3 bg-light border mb-4">
                        <div class="row g-2 text-center align-items-center">
                            <div class="col-sm-4 col-12">
                                <span class="text-muted small d-block" style="font-size: 10px;">Company / Customer</span>
                                <span class="fw-bold text-dark" id="modal-md-comp" style="font-size: 12.5px;">-</span>
                            </div>
                            <div class="col-sm-2 col-6">
                                <span class="text-muted small d-block" style="font-size: 10px;">Gross Fee</span>
                                <span class="fw-bold text-danger" id="modal-md-gross" style="font-size: 12.5px;">-</span>
                            </div>
                            <div class="col-sm-3 col-6">
                                <span class="text-muted small d-block" style="font-size: 10px;" id="modal-md-tax-label">Pajak Fee (2026)</span>
                                <span class="fw-bold text-danger" id="modal-md-tax-amount" style="font-size: 12.5px;">-</span>
                            </div>
                            <div class="col-sm-3 col-12">
                                <span class="text-muted small d-block" style="font-size: 10px;">Nett Fee yang Ditransfer</span>
                                <span class="fw-bolder text-primary fs-6" id="modal-md-net">-</span>
                            </div>
                        </div>
                    </div>

                    {{-- Form Data Rekening --}}
                    <h6 class="fw-bold text-dark mb-2" style="font-size: 12.5px;">
                        <i class="mdi mdi-bank-outline me-1 text-primary"></i> Data Rekening Tujuan Penerima Fee
                    </h6>
                    <div class="row g-3 mb-3">
                        <div class="col-md-3 col-6">
                            <label class="form-label small fw-semibold" for="input_md_bank_name">Nama Bank</label>
                            <input type="text" class="form-control form-control-sm" id="input_md_bank_name" name="fee_bank_name" placeholder="Contoh: BCA">
                        </div>
                        <div class="col-md-3 col-6">
                            <label class="form-label small fw-semibold" for="input_md_bank_branch">Cabang Bank <span class="text-muted fw-normal">(Opsional)</span></label>
                            <input type="text" class="form-control form-control-sm" id="input_md_bank_branch" name="fee_bank_branch" placeholder="Contoh: KCP Dago / BDG">
                        </div>
                        <div class="col-md-3 col-6">
                            <label class="form-label small fw-semibold" for="input_md_bank_account">Nomor Rekening</label>
                            <input type="text" class="form-control form-control-sm" id="input_md_bank_account" name="fee_bank_account" placeholder="Contoh: 1234567890">
                        </div>
                        <div class="col-md-3 col-6">
                            <label class="form-label small fw-semibold" for="input_md_bank_holder">Nama Pemilik (A/N)</label>
                            <input type="text" class="form-control form-control-sm" id="input_md_bank_holder" name="fee_bank_holder" placeholder="Contoh: John Doe">
                        </div>
                    </div>

                    <div class="divider my-3">
                        <div class="divider-text text-muted small" style="font-size: 11px;">Status &amp; Eksekusi Transfer</div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold" for="input_md_payment_status">Status Pencairan Fee <span class="text-danger">*</span></label>
                            <select class="form-select form-select-sm" id="input_md_payment_status" name="fee_payment_status" required>
                                <option value="unpaid">🔴 Belum Ditransfer (Unpaid)</option>
                                <option value="pending_transfer">🟡 Siap Ditransfer (Pending Action)</option>
                                <option value="paid">🟢 Sudah Ditransfer (Paid)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold" for="input_md_transfer_date">Tanggal Transfer</label>
                            <input type="date" class="form-control form-control-sm" id="input_md_transfer_date" name="fee_transfer_date" value="{{ date('Y-m-d') }}">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label small fw-semibold" for="input_md_source_bank">
                                <i class="mdi mdi-bank text-success me-1"></i> Rekening Bank Kantor (Sumber Dana Transfer) <span class="text-danger">*</span>
                            </label>
                            <select class="form-select form-select-sm" id="input_md_source_bank" name="id_source_bank">
                                <option value="">-- Pilih Rekening Kantor Asal Transfer --</option>
                                @foreach ($banks as $b)
                                    <option value="{{ $b->id }}">{{ $b->bank }} - {{ $b->no_rek }} (a.n {{ $b->nama_rek }}) [Saldo: Rp {{ number_format($b->saldo, 0, ',', '.') }}]</option>
                                @endforeach
                            </select>
                            <small class="text-muted" style="font-size: 10.5px;">Wajib dipilih jika status "Sudah Ditransfer (Paid)". Saldo bank akan otomatis berkurang &amp; tercatat sebagai Debet di Rekening Koran.</small>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-12">
                            <label class="form-label small fw-semibold d-flex justify-content-between align-items-center mb-1.5">
                                <span>Upload Bukti Transfer <span class="text-muted">(JPG, PNG, PDF maks 5MB)</span></span>
                                <span class="badge bg-label-info fw-normal" style="font-size: 10.5px;">
                                    <i class="mdi mdi-content-paste me-1"></i>Support Paste (Ctrl + V)
                                </span>
                            </label>

                            <label for="input_md_transfer_proof" id="md-paste-drop-zone" class="border border-2 border-dashed rounded-3 p-3 text-center bg-light position-relative d-block mb-0" style="cursor: pointer; transition: all 0.2s ease;">
                                <input type="file" class="d-none" id="input_md_transfer_proof" name="fee_transfer_proof" accept="image/*,.pdf">
                                
                                <div id="md-paste-prompt" class="py-1">
                                    <div class="avatar avatar-md bg-label-success rounded-circle mx-auto mb-2 d-flex align-items-center justify-content-center">
                                        <i class="mdi mdi-cloud-upload-outline fs-3"></i>
                                    </div>
                                    <p class="mb-1 fw-semibold text-dark small">
                                        Klik untuk pilih file, drag &amp; drop, atau tekan <kbd class="bg-dark text-white px-1.5 py-0.5 rounded" style="font-size: 11px;">Ctrl + V</kbd> untuk Paste Screenshot
                                    </p>
                                    <span class="text-muted small" style="font-size: 11px;">Mendukung format JPG, PNG, PDF (Maks. 5 MB)</span>
                                </div>

                                <div id="md-new-file-preview" class="d-none">
                                    <div class="d-flex align-items-center justify-content-between p-2 rounded bg-white border shadow-sm">
                                        <div class="d-flex align-items-center gap-2 text-start overflow-hidden">
                                            <img id="md-pasted-image-thumb" src="" alt="Preview" class="rounded object-fit-cover border d-none" style="width: 50px; height: 50px;">
                                            <div id="md-pasted-pdf-icon" class="avatar avatar-sm bg-label-danger rounded d-flex align-items-center justify-content-center flex-shrink-0 d-none" style="width: 50px; height: 50px;">
                                                <i class="mdi mdi-file-pdf-box fs-3"></i>
                                            </div>
                                            <div class="text-truncate">
                                                <div class="fw-bold text-dark small text-truncate" id="md-new-file-name">bukti.png</div>
                                                <div class="d-flex gap-1 mt-0.5">
                                                    <span class="badge bg-label-secondary px-1.5 py-0.5" id="md-new-file-size" style="font-size: 10px;">-</span>
                                                    <span class="badge bg-label-success px-1.5 py-0.5" id="md-new-file-source" style="font-size: 10px;">Siap Diupload</span>
                                                </div>
                                            </div>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-icon btn-label-danger flex-shrink-0 ms-2" id="btn-md-remove-new-file" title="Hapus / Pilih Ulang">
                                            <i class="mdi mdi-close"></i>
                                        </button>
                                    </div>
                                </div>
                            </label>

                            <input type="hidden" name="delete_fee_transfer_proof" id="md_delete_fee_transfer_proof" value="0">

                            <div id="md-proof-preview-wrap" class="mt-2 d-none">
                                <div class="alert alert-secondary py-1.5 px-2.5 mb-0 rounded d-flex align-items-center justify-content-between flex-wrap gap-2" style="font-size: 11.5px;">
                                    <span class="text-muted">
                                        <i class="mdi mdi-file-check-outline text-success me-1"></i> Bukti transfer tersimpan
                                    </span>
                                    <div class="d-flex align-items-center gap-1.5">
                                        <a href="#" target="_blank" id="md-proof-preview-link" class="btn btn-xs btn-label-primary px-2 py-0.5 fw-semibold">
                                            <i class="mdi mdi-open-in-new me-0.5"></i> Lihat File
                                        </a>
                                        <button type="button" class="btn btn-xs btn-icon btn-label-danger" id="btn-md-delete-existing-proof" title="Hapus Bukti Transfer">
                                            <i class="mdi mdi-trash-can-outline"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div id="md-proof-deleted-notice" class="mt-2 d-none">
                                <div class="alert alert-warning py-1.5 px-2.5 mb-0 rounded d-flex align-items-center justify-content-between flex-wrap gap-2" style="font-size: 11.5px;">
                                    <span class="text-dark">
                                        <i class="mdi mdi-alert-circle-outline text-warning me-1"></i> Bukti transfer sebelumnya akan dihapus saat disimpan.
                                    </span>
                                    <button type="button" class="btn btn-xs btn-label-secondary px-2 py-0.5" id="btn-md-undo-delete-proof">
                                        <i class="mdi mdi-undo me-0.5"></i> Batal Hapus
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-0">
                        <label class="form-label small fw-semibold" for="input_md_transfer_note">Catatan / Nomor Referensi Transfer <span class="text-muted">(Opsional)</span></label>
                        <textarea class="form-control form-control-sm" id="input_md_transfer_note" name="fee_transfer_note" rows="2"
                            placeholder="Contoh: No ref transfer bank #TRX123456789 atau catatan transfer..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top py-2 px-3">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="mdi mdi-content-save-outline me-1"></i> Simpan Status Pencairan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('script')
<script>
    $(document).ready(function () {
        // --------------------------------------------------------------------
        // Format Currency Helper
        // --------------------------------------------------------------------
        function formatRupiah(num) {
            return 'Rp ' + Math.round(num).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }

        // --------------------------------------------------------------------
        // Live Tax Policy 2026 Calculator for Manual Fee Modal
        // --------------------------------------------------------------------
        function calcTax2026(fee) {
            var val = parseFloat(fee) || 0;
            var taxRate = 0;
            var taxLabel = '0% (Bebas Pajak)';
            if (val < 1500000) {
                taxRate = 0;
                taxLabel = '0% (Bebas Pajak)';
            } else if (val <= 5000000) {
                taxRate = 0.0368;
                taxLabel = '3.68%';
            } else {
                taxRate = 0.10;
                taxLabel = '10%';
            }
            var taxAmount = Math.round(val * taxRate);
            var netFee = val - taxAmount;

            return {
                taxRate: taxRate,
                taxLabel: taxLabel,
                taxAmount: taxAmount,
                netFee: netFee
            };
        }

        $('#manual_gross_fee').on('input', function () {
            var val = $(this).val();
            var res = calcTax2026(val);
            $('#manual_calc_tax_rate').text(res.taxLabel);
            $('#manual_calc_tax_amount').text(res.taxAmount > 0 ? '- ' + formatRupiah(res.taxAmount) : 'Rp 0');
            $('#manual_calc_net_fee').text(formatRupiah(res.netFee));
        });

        // --------------------------------------------------------------------
        // Generic File Upload / Paste / Dropzone Handler Factory
        // --------------------------------------------------------------------
        function setupUploadHandler(opts) {
            var inputId = opts.inputId;
            var pasteDropZoneId = opts.pasteDropZoneId;
            var pastePromptId = opts.pastePromptId;
            var newFilePreviewId = opts.newFilePreviewId;
            var pastedImageThumbId = opts.pastedImageThumbId;
            var pastedPdfIconId = opts.pastedPdfIconId;
            var newFileNameId = opts.newFileNameId;
            var newFileSizeId = opts.newFileSizeId;
            var newFileSourceId = opts.newFileSourceId;
            var btnRemoveNewFileId = opts.btnRemoveNewFileId;
            var modalId = opts.modalId;

            function reset() {
                var input = document.getElementById(inputId);
                if (input) input.value = '';
                $('#' + pastedImageThumbId).attr('src', '').addClass('d-none');
                $('#' + pastedPdfIconId).addClass('d-none');
                $('#' + newFilePreviewId).addClass('d-none');
                $('#' + pastePromptId).removeClass('d-none');
            }

            function handleFile(file, sourceLabel) {
                if (!file) return;

                if (file.size > 5 * 1024 * 1024) {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Ukuran Terlalu Besar',
                            text: 'Ukuran file maksimal adalah 5MB.',
                            customClass: { confirmButton: 'btn btn-danger' }
                        });
                    } else {
                        alert('Ukuran file maksimal adalah 5MB.');
                    }
                    return;
                }

                var input = document.getElementById(inputId);
                if (input && typeof DataTransfer !== 'undefined') {
                    var dt = new DataTransfer();
                    dt.items.add(file);
                    input.files = dt.files;
                }

                $('#' + newFileNameId).text(file.name);
                $('#' + newFileSizeId).text((file.size / 1024 >= 1024 ? (file.size / (1024 * 1024)).toFixed(2) + ' MB' : (file.size / 1024).toFixed(1) + ' KB'));
                $('#' + newFileSourceId).text(sourceLabel || 'Siap Diupload');

                if (file.type.startsWith('image/')) {
                    var reader = new FileReader();
                    reader.onload = function (e) {
                        $('#' + pastedImageThumbId).attr('src', e.target.result).removeClass('d-none');
                        $('#' + pastedPdfIconId).addClass('d-none');
                    };
                    reader.readAsDataURL(file);
                } else {
                    $('#' + pastedImageThumbId).addClass('d-none');
                    $('#' + pastedPdfIconId).removeClass('d-none');
                }

                $('#' + pastePromptId).addClass('d-none');
                $('#' + newFilePreviewId).removeClass('d-none');
            }

            $('#' + inputId).on('change', function () {
                if (this.files && this.files[0]) {
                    handleFile(this.files[0], '📁 Dipilih dari Komputer');
                }
            });

            $('#' + btnRemoveNewFileId).on('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                reset();
            });

            var dropZone = document.getElementById(pasteDropZoneId);
            if (dropZone) {
                ['dragenter', 'dragover'].forEach(function (eventName) {
                    dropZone.addEventListener(eventName, function (e) {
                        e.preventDefault();
                        e.stopPropagation();
                        $('#' + pasteDropZoneId).addClass('border-primary bg-label-primary');
                    }, false);
                });

                ['dragleave', 'drop'].forEach(function (eventName) {
                    dropZone.addEventListener(eventName, function (e) {
                        e.preventDefault();
                        e.stopPropagation();
                        $('#' + pasteDropZoneId).removeClass('border-primary bg-label-primary');
                    }, false);
                });

                dropZone.addEventListener('drop', function (e) {
                    var dt = e.dataTransfer;
                    if (dt && dt.files && dt.files[0]) {
                        handleFile(dt.files[0], '📥 Drag & Drop');
                    }
                }, false);
            }

            return {
                reset: reset,
                handleFile: handleFile
            };
        }

        var uploaderDisburse = setupUploadHandler({
            inputId: 'input_fee_transfer_proof',
            pasteDropZoneId: 'paste-drop-zone',
            pastePromptId: 'paste-prompt',
            newFilePreviewId: 'new-file-preview',
            pastedImageThumbId: 'pasted-image-thumb',
            pastedPdfIconId: 'pasted-pdf-icon',
            newFileNameId: 'new-file-name',
            newFileSizeId: 'new-file-size',
            newFileSourceId: 'new-file-source',
            btnRemoveNewFileId: 'btn-remove-new-file',
            modalId: 'modalDisbursement'
        });

        var uploaderManual = setupUploadHandler({
            inputId: 'manual_fee_transfer_proof',
            pasteDropZoneId: 'manual-paste-drop-zone',
            pastePromptId: 'manual-paste-prompt',
            newFilePreviewId: 'manual-new-file-preview',
            pastedImageThumbId: 'manual-pasted-image-thumb',
            pastedPdfIconId: 'manual-pasted-pdf-icon',
            newFileNameId: 'manual-new-file-name',
            newFileSizeId: 'manual-new-file-size',
            newFileSourceId: 'manual-new-file-source',
            btnRemoveNewFileId: 'btn-manual-remove-new-file',
            modalId: 'modalManualFee'
        });

        var uploaderMDisburse = setupUploadHandler({
            inputId: 'input_md_transfer_proof',
            pasteDropZoneId: 'md-paste-drop-zone',
            pastePromptId: 'md-paste-prompt',
            newFilePreviewId: 'md-new-file-preview',
            pastedImageThumbId: 'md-pasted-image-thumb',
            pastedPdfIconId: 'md-pasted-pdf-icon',
            newFileNameId: 'md-new-file-name',
            newFileSizeId: 'md-new-file-size',
            newFileSourceId: 'md-new-file-source',
            btnRemoveNewFileId: 'btn-md-remove-new-file',
            modalId: 'modalManualDisbursement'
        });

        // --------------------------------------------------------------------
        // Global Paste (Ctrl + V) Handler for whichever modal is active
        // --------------------------------------------------------------------
        $(document).on('paste', function (e) {
            var activeModal = $('.modal.show');
            if (!activeModal.length) return;

            var clipboardData = e.originalEvent.clipboardData || window.clipboardData;
            if (!clipboardData || !clipboardData.items) return;

            for (var i = 0; i < clipboardData.items.length; i++) {
                if (clipboardData.items[i].type.indexOf('image') !== -1) {
                    var blob = clipboardData.items[i].getAsFile();
                    if (blob) {
                        e.preventDefault();
                        var timestamp = new Date().toISOString().replace(/[-:T.]/g, '').slice(0, 14);
                        var ext = blob.type.split('/')[1] || 'png';
                        if (ext === 'jpeg') ext = 'jpg';
                        var file = new File([blob], 'bukti_transfer_' + timestamp + '.' + ext, { type: blob.type });

                        if (activeModal.attr('id') === 'modalDisbursement') {
                            uploaderDisburse.handleFile(file, '📋 Di-paste (Ctrl + V)');
                        } else if (activeModal.attr('id') === 'modalManualFee') {
                            uploaderManual.handleFile(file, '📋 Di-paste (Ctrl + V)');
                        } else if (activeModal.attr('id') === 'modalManualDisbursement') {
                            uploaderMDisburse.handleFile(file, '📋 Di-paste (Ctrl + V)');
                        }
                        break;
                    }
                }
            }
        });

        // --------------------------------------------------------------------
        // Delete & Undo Proof Handlers
        // --------------------------------------------------------------------
        $('#btn-delete-existing-proof').on('click', function () {
            $('#delete_fee_transfer_proof').val('1');
            $('#proof-preview-wrap').addClass('d-none');
            $('#proof-deleted-notice').removeClass('d-none');
        });
        $('#btn-undo-delete-proof').on('click', function () {
            $('#delete_fee_transfer_proof').val('0');
            $('#proof-deleted-notice').addClass('d-none');
            $('#proof-preview-wrap').removeClass('d-none');
        });

        $('#btn-manual-delete-existing-proof').on('click', function () {
            $('#manual_delete_fee_transfer_proof').val('1');
            $('#manual-proof-preview-wrap').addClass('d-none');
            $('#manual-proof-deleted-notice').removeClass('d-none');
        });
        $('#btn-manual-undo-delete-proof').on('click', function () {
            $('#manual_delete_fee_transfer_proof').val('0');
            $('#manual-proof-deleted-notice').addClass('d-none');
            $('#manual-proof-preview-wrap').removeClass('d-none');
        });

        $('#btn-md-delete-existing-proof').on('click', function () {
            $('#md_delete_fee_transfer_proof').val('1');
            $('#md-proof-preview-wrap').addClass('d-none');
            $('#md-proof-deleted-notice').removeClass('d-none');
        });
        $('#btn-md-undo-delete-proof').on('click', function () {
            $('#md_delete_fee_transfer_proof').val('0');
            $('#md-proof-deleted-notice').addClass('d-none');
            $('#md-proof-preview-wrap').removeClass('d-none');
        });

        // --------------------------------------------------------------------
        // Open Modal Pencairan Quotation
        // --------------------------------------------------------------------
        $(document).on('click', '.btn-open-disbursement', function () {
            var btn = $(this);
            uploaderDisburse.reset();
            $('#delete_fee_transfer_proof').val('0');
            $('#proof-deleted-notice').addClass('d-none');

            var custPayStatus = btn.data('cust-pay-status');
            var custPayLabel = btn.data('cust-pay-label');
            var custPayDetail = btn.data('cust-pay-detail');
            var custPayLunas = btn.data('cust-pay-lunas');

            var banner = $('#modal-cust-pay-banner');
            banner.removeClass('alert-success alert-warning alert-danger alert-info alert-secondary bg-label-success bg-label-warning bg-label-danger bg-label-info bg-label-secondary');
            
            if (custPayLunas == '1') {
                banner.addClass('alert-success bg-label-success');
                $('#modal-cust-pay-icon').attr('class', 'mdi mdi-check-decagram fs-4 text-success');
                $('#modal-cust-pay-title').text(custPayLabel + ' — ' + custPayDetail);
                $('#modal-cust-pay-badge').attr('class', 'badge bg-success').text('Aman Dicairkan');
            } else if (custPayStatus === 'dp' || custPayStatus === 'partial_tempo') {
                banner.addClass('alert-warning bg-label-warning');
                $('#modal-cust-pay-icon').attr('class', 'mdi mdi-cash-clock fs-4 text-warning');
                $('#modal-cust-pay-title').text(custPayLabel + ' — ' + custPayDetail);
                $('#modal-cust-pay-badge').attr('class', 'badge bg-warning text-dark').text('Belum Lunas');
            } else if (custPayStatus === 'tempo') {
                banner.addClass('alert-secondary bg-label-secondary');
                $('#modal-cust-pay-icon').attr('class', 'mdi mdi-calendar-clock fs-4 text-secondary');
                $('#modal-cust-pay-title').text(custPayLabel + ' — ' + custPayDetail);
                $('#modal-cust-pay-badge').attr('class', 'badge bg-secondary').text('Termin Kredit');
            } else {
                banner.addClass('alert-danger bg-label-danger');
                $('#modal-cust-pay-icon').attr('class', 'mdi mdi-alert-circle-outline fs-4 text-danger');
                $('#modal-cust-pay-title').text(custPayLabel + ' — ' + custPayDetail);
                $('#modal-cust-pay-badge').attr('class', 'badge bg-danger').text('Belum Ada Bayar');
            }

            $('#form-disbursement').attr('action', btn.data('action-url'));
            $('#modal-disburse-quote-title').text('#' + btn.data('no-quote') + ' — ' + btn.data('client') + ' (Sales: ' + btn.data('sales') + ')');
            $('#modal-d-pretax').text(btn.data('pretax'));
            $('#modal-d-gross').text(btn.data('gross'));
            $('#modal-d-tax-label').text('Pajak Fee (' + (btn.data('tax-label') || '0%') + ')');
            
            var taxAmountNum = parseFloat(btn.data('tax-amount-num') || 0);
            if (taxAmountNum > 0) {
                $('#modal-d-tax-amount').text('- ' + btn.data('tax-amount-formatted')).removeClass('text-success').addClass('text-danger');
            } else {
                $('#modal-d-tax-amount').text('Rp 0 (Bebas Pajak)').removeClass('text-danger').addClass('text-success');
            }

            $('#modal-d-net').text(btn.data('net'));

            $('#input_fee_bank_name').val(btn.data('bank-name'));
            $('#input_fee_bank_branch').val(btn.data('bank-branch') || '');
            $('#input_fee_bank_account').val(btn.data('bank-account'));
            $('#input_fee_bank_holder').val(btn.data('bank-holder'));
            $('#input_fee_payment_status').val(btn.data('payment-status') || 'unpaid');
            $('#input_fee_source_bank').val(btn.data('source-bank-id') || '');
            $('#input_fee_transfer_date').val(btn.data('transfer-date') || '{{ date("Y-m-d") }}');
            $('#input_fee_transfer_note').val(btn.data('transfer-note'));

            var proofUrl = btn.data('proof-url');
            if (proofUrl) {
                $('#proof-preview-wrap').removeClass('d-none');
                $('#proof-preview-link').attr('href', proofUrl);
            } else {
                $('#proof-preview-wrap').addClass('d-none');
            }

            var modalEl = document.getElementById('modalDisbursement');
            if (modalEl) {
                bootstrap.Modal.getOrCreateInstance(modalEl).show();
            }
        });

        // --------------------------------------------------------------------
        // Open Modal Tambah Fee Manual
        // --------------------------------------------------------------------
        function openAddManualFeeModal() {
            uploaderManual.reset();
            $('#manual_delete_fee_transfer_proof').val('0');
            $('#manual-proof-preview-wrap').addClass('d-none');
            $('#manual-proof-deleted-notice').addClass('d-none');

            $('#manual-fee-modal-title').text('Tambah Fee Manual');
            $('#form-manual-fee').attr('action', '{{ route("finance.management-fee.store-manual") }}');

            $('#manual_client_id').val('');
            $('#manual_custom_company_name').val('');
            $('#manual_date').val('{{ date("Y-m-d") }}');
            $('#manual_reference_no').val('');
            $('#manual_title').val('');
            $('#manual_gross_fee').val('').trigger('input');
            $('#manual_bank_name').val('');
            $('#manual_bank_branch').val('');
            $('#manual_bank_account').val('');
            $('#manual_bank_holder').val('');
            $('#manual_fee_payment_status').val('unpaid');
            $('#manual_source_bank').val('');
            $('#manual_fee_transfer_date').val('{{ date("Y-m-d") }}');
            $('#manual_fee_transfer_note').val('');

            var modalEl = document.getElementById('modalManualFee');
            if (modalEl) {
                bootstrap.Modal.getOrCreateInstance(modalEl).show();
            }
        }

        $('#btn-add-manual-fee, #btn-add-manual-fee-empty').on('click', function () {
            openAddManualFeeModal();
        });

        // --------------------------------------------------------------------
        // Open Modal Edit Fee Manual
        // --------------------------------------------------------------------
        $(document).on('click', '.btn-edit-manual-fee', function () {
            var btn = $(this);
            uploaderManual.reset();
            $('#manual_delete_fee_transfer_proof').val('0');
            $('#manual-proof-deleted-notice').addClass('d-none');

            $('#manual-fee-modal-title').text('Edit Fee Manual: ' + btn.data('title'));
            $('#form-manual-fee').attr('action', btn.data('update-url'));

            $('#manual_client_id').val(btn.data('client-id') || '');
            $('#manual_custom_company_name').val(btn.data('custom-company') || '');
            $('#manual_date').val(btn.data('date') || '{{ date("Y-m-d") }}');
            $('#manual_reference_no').val(btn.data('ref') || '');
            $('#manual_title').val(btn.data('title') || '');
            $('#manual_gross_fee').val(btn.data('gross') || 0).trigger('input');
            $('#manual_bank_name').val(btn.data('bank-name') || '');
            $('#manual_bank_branch').val(btn.data('bank-branch') || '');
            $('#manual_bank_account').val(btn.data('bank-account') || '');
            $('#manual_bank_holder').val(btn.data('bank-holder') || '');
            $('#manual_fee_payment_status').val(btn.data('payment-status') || 'unpaid');
            $('#manual_source_bank').val(btn.data('source-bank-id') || '');
            $('#manual_fee_transfer_date').val(btn.data('transfer-date') || '{{ date("Y-m-d") }}');
            $('#manual_fee_transfer_note').val(btn.data('transfer-note') || '');

            var proofUrl = btn.data('proof-url');
            if (proofUrl) {
                $('#manual-proof-preview-wrap').removeClass('d-none');
                $('#manual-proof-preview-link').attr('href', proofUrl);
            } else {
                $('#manual-proof-preview-wrap').addClass('d-none');
            }

            var modalEl = document.getElementById('modalManualFee');
            if (modalEl) {
                bootstrap.Modal.getOrCreateInstance(modalEl).show();
            }
        });

        // --------------------------------------------------------------------
        // Open Modal Quick Pencairan Fee Manual
        // --------------------------------------------------------------------
        $(document).on('click', '.btn-open-manual-disbursement', function () {
            var btn = $(this);
            uploaderMDisburse.reset();
            $('#md_delete_fee_transfer_proof').val('0');
            $('#md-proof-deleted-notice').addClass('d-none');

            $('#form-manual-disbursement').attr('action', btn.data('action-url'));
            $('#modal-mdisburse-title').text(btn.data('title') + ' (' + btn.data('company') + ')');
            $('#modal-md-comp').text(btn.data('company'));
            $('#modal-md-gross').text(btn.data('gross'));
            $('#modal-md-tax-label').text('Pajak Fee (' + (btn.data('tax-label') || '0%') + ')');

            var taxAmountNum = parseFloat(btn.data('tax-amount-num') || 0);
            if (taxAmountNum > 0) {
                $('#modal-md-tax-amount').text('- ' + btn.data('tax-amount-formatted')).removeClass('text-success').addClass('text-danger');
            } else {
                $('#modal-md-tax-amount').text('Rp 0 (Bebas Pajak)').removeClass('text-danger').addClass('text-success');
            }

            $('#modal-md-net').text(btn.data('net'));

            $('#input_md_bank_name').val(btn.data('bank-name') || '');
            $('#input_md_bank_branch').val(btn.data('bank-branch') || '');
            $('#input_md_bank_account').val(btn.data('bank-account') || '');
            $('#input_md_bank_holder').val(btn.data('bank-holder') || '');
            $('#input_md_payment_status').val(btn.data('payment-status') || 'unpaid');
            $('#input_md_source_bank').val(btn.data('source-bank-id') || '');
            $('#input_md_transfer_date').val(btn.data('transfer-date') || '{{ date("Y-m-d") }}');
            $('#input_md_transfer_note').val(btn.data('transfer-note') || '');

            var proofUrl = btn.data('proof-url');
            if (proofUrl) {
                $('#md-proof-preview-wrap').removeClass('d-none');
                $('#md-proof-preview-link').attr('href', proofUrl);
            } else {
                $('#md-proof-preview-wrap').addClass('d-none');
            }

            var modalEl = document.getElementById('modalManualDisbursement');
            if (modalEl) {
                bootstrap.Modal.getOrCreateInstance(modalEl).show();
            }
        });
    });
</script>
@endpush

@endsection
