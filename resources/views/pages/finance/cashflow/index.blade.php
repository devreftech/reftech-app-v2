@extends('layouts.sales.app')
@section('title', 'Cashflow Statement (Laporan Arus Kas) - Finance ERP')

@push('after-style')
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.css" />
    <style>
        .finance-kpi-card {
            transition: all 0.25s ease;
            border: 1px solid rgba(0, 0, 0, 0.05);
            box-shadow: 0 2px 6px 0 rgba(67, 89, 113, 0.07);
        }
        .finance-kpi-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 14px 0 rgba(67, 89, 113, 0.12);
        }
        .nav-tabs-finance .nav-link {
            font-weight: 600;
            color: #64748b;
            border: none;
            border-bottom: 3px solid transparent;
            padding: 0.85rem 1.4rem;
            background: transparent;
            transition: all 0.2s ease;
            border-radius: 0;
            font-size: 0.92rem;
        }
        .nav-tabs-finance .nav-link:hover {
            color: #4f46e5;
            background: rgba(79, 70, 229, 0.04);
        }
        .nav-tabs-finance .nav-link.active {
            color: #4f46e5;
            border-bottom-color: #4f46e5;
            background: transparent;
            font-weight: 700;
        }
        .cashflow-table th {
            font-size: 0.78rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .cashflow-table td {
            vertical-align: middle;
            font-size: 0.88rem;
        }
    </style>
@endpush

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    {{-- Page Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center py-3 mb-3 gap-3">
        <div>
            <h4 class="fw-bold mb-1">
                <span class="text-muted fw-light">Finance / Statement /</span> Cashflow Statement
            </h4>
            <p class="text-muted mb-0 small">
                <i class="mdi mdi-cash-sync me-1 text-primary"></i> Laporan Arus Kas (Metode Langsung: Aktivitas Operasional, Investasi &amp; Pendanaan)
            </p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-label-info px-3 py-2 fs-7">
                <i class="mdi mdi-calendar-check me-1"></i> Periode Aktif: {{ $ringkasanTahunLabel }}
            </span>
        </div>
    </div>

    {{-- Unified Statement Navigation Tabs --}}
    <ul class="nav nav-tabs nav-tabs-finance mb-4 border-bottom" role="tablist">
        <li class="nav-item">
            <a class="nav-link" href="{{ route('expense-income.index') }}">
                <i class="mdi mdi-book-open-outline me-1"></i> Income Statement (Laba Rugi)
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('expense-balance.index') }}">
                <i class="mdi mdi-scale-balance me-1"></i> Balance Statement (Neraca)
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('expense-equity.index') }}">
                <i class="mdi mdi-chart-donut me-1"></i> Equity Statement (Perubahan Modal)
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link active" href="{{ route('expense-cashflow.index') }}">
                <i class="mdi mdi-cash-sync me-1"></i> Cashflow Statement (Arus Kas)
            </a>
        </li>
    </ul>

    @php
        $netOperasiBulan = ($ringkasanBulan['kasMasuk'] ?? 0) - ($ringkasanBulan['kasKeluar'] ?? 0);
        $totalNetBulan = $netOperasiBulan + ($ringkasanBulan['netInvestasi'] ?? 0) + ($ringkasanBulan['netPendanaan'] ?? 0);

        $netOperasiTahun = ($ringkasanTahun['kasMasuk'] ?? 0) - ($ringkasanTahun['kasKeluar'] ?? 0);
        $totalNetTahun = $netOperasiTahun + ($ringkasanTahun['netInvestasi'] ?? 0) + ($ringkasanTahun['netPendanaan'] ?? 0);
    @endphp

    {{-- 4 Executive Cashflow KPI Cards (YTD) --}}
    <div class="row g-3 mb-4">
        {{-- Kas Masuk Operasi --}}
        <div class="col-sm-6 col-xl-3">
            <div class="card finance-kpi-card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="fw-semibold text-muted small">Kas Masuk Operasi (YTD)</span>
                        <span class="avatar avatar-sm rounded bg-label-success">
                            <i class="mdi mdi-cash-plus fs-5"></i>
                        </span>
                    </div>
                    <h4 class="mb-1 text-success fw-bold">Rp {{ number_format($ringkasanTahun['kasMasuk'], 0, ',', '.') }}</h4>
                    <div class="d-flex align-items-center text-muted small">
                        <span>Bulan ini: <strong class="text-dark">Rp {{ number_format($ringkasanBulan['kasMasuk'], 0, ',', '.') }}</strong></span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Kas Keluar Operasi --}}
        <div class="col-sm-6 col-xl-3">
            <div class="card finance-kpi-card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="fw-semibold text-muted small">Kas Keluar Operasi (YTD)</span>
                        <span class="avatar avatar-sm rounded bg-label-danger">
                            <i class="mdi mdi-cash-minus fs-5"></i>
                        </span>
                    </div>
                    <h4 class="mb-1 text-danger fw-bold">Rp {{ number_format($ringkasanTahun['kasKeluar'], 0, ',', '.') }}</h4>
                    <div class="d-flex align-items-center text-muted small">
                        <span>Bulan ini: <strong class="text-dark">Rp {{ number_format($ringkasanBulan['kasKeluar'], 0, ',', '.') }}</strong></span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Net Kas Operasi --}}
        <div class="col-sm-6 col-xl-3">
            <div class="card finance-kpi-card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="fw-semibold text-muted small">Net Arus Kas Operasi (OCF)</span>
                        <span class="avatar avatar-sm rounded bg-label-primary">
                            <i class="mdi mdi-swap-horizontal-bold fs-5"></i>
                        </span>
                    </div>
                    <h4 class="mb-1 {{ $netOperasiTahun >= 0 ? 'text-primary' : 'text-danger' }} fw-bold">
                        Rp {{ number_format($netOperasiTahun, 0, ',', '.') }}
                    </h4>
                    <div class="d-flex align-items-center text-muted small">
                        <span class="badge {{ $netOperasiTahun >= 0 ? 'bg-label-success' : 'bg-label-danger' }} me-1">
                            {{ $netOperasiTahun >= 0 ? 'Surplus Operasi' : 'Defisit Operasi' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Net Investasi & Pendanaan --}}
        <div class="col-sm-6 col-xl-3">
            <div class="card finance-kpi-card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="fw-semibold text-muted small">Net Investasi &amp; Modal</span>
                        <span class="avatar avatar-sm rounded bg-label-info">
                            <i class="mdi mdi-bank-transfer fs-5"></i>
                        </span>
                    </div>
                    @php $netInvFinTahun = ($ringkasanTahun['netInvestasi'] ?? 0) + ($ringkasanTahun['netPendanaan'] ?? 0); @endphp
                    <h4 class="mb-1 {{ $netInvFinTahun >= 0 ? 'text-dark' : 'text-danger' }} fw-bold">
                        Rp {{ number_format($netInvFinTahun, 0, ',', '.') }}
                    </h4>
                    <div class="d-flex align-items-center text-muted small">
                        <span>Inv: <strong>Rp {{ number_format($ringkasanTahun['netInvestasi'], 0, ',', '.') }}</strong></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Cashflow Period Comparison: Bulan Berjalan vs Tahun Berjalan --}}
    <div class="row g-4 mb-4">
        {{-- Bulan Berjalan --}}
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-bottom py-3 d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <div class="avatar avatar-sm rounded bg-label-primary">
                            <i class="mdi mdi-calendar-month-outline fs-5"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-bold">Ringkasan Bulan Ini</h6>
                            <small class="text-muted">{{ $ringkasanBulanLabel }}</small>
                        </div>
                    </div>
                    <span class="badge bg-label-secondary">Bulan Berjalan</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 cashflow-table">
                            <tbody>
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-semibold text-dark"><i class="mdi mdi-cash-plus text-success me-2"></i>Kas Masuk Operasi</div>
                                        <small class="text-muted">Pelunasan Penjualan PO &amp; Pendapatan Lain</small>
                                    </td>
                                    <td class="text-end pe-4 fw-bold text-success fs-6">
                                        Rp {{ number_format($ringkasanBulan['kasMasuk'], 0, ',', '.') }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-semibold text-dark"><i class="mdi mdi-cash-minus text-danger me-2"></i>Kas Keluar Operasi</div>
                                        <small class="text-muted">Beban Operasional Kas &amp; Pengeluaran Umum</small>
                                    </td>
                                    <td class="text-end pe-4 fw-bold text-danger fs-6">
                                        (Rp {{ number_format($ringkasanBulan['kasKeluar'], 0, ',', '.') }})
                                    </td>
                                </tr>
                                <tr class="table-light">
                                    <td class="ps-4 fw-bold text-primary">
                                        <i class="mdi mdi-sigma me-2"></i>Net Arus Kas Aktivitas Operasi
                                    </td>
                                    <td class="text-end pe-4 fw-bold {{ $netOperasiBulan >= 0 ? 'text-primary' : 'text-danger' }} fs-6">
                                        Rp {{ number_format($netOperasiBulan, 0, ',', '.') }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-semibold text-dark"><i class="mdi mdi-domain text-info me-2"></i>Net Aktivitas Investasi</div>
                                        <small class="text-muted">Pembelian / Penjualan Aset Tetap</small>
                                    </td>
                                    <td class="text-end pe-4 fw-semibold {{ $ringkasanBulan['netInvestasi'] < 0 ? 'text-danger' : 'text-dark' }}">
                                        Rp {{ number_format($ringkasanBulan['netInvestasi'], 0, ',', '.') }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-semibold text-dark"><i class="mdi mdi-account-cash text-warning me-2"></i>Net Aktivitas Pendanaan</div>
                                        <small class="text-muted">Penarikan Prive Pemilik</small>
                                    </td>
                                    <td class="text-end pe-4 fw-semibold {{ $ringkasanBulan['netPendanaan'] < 0 ? 'text-danger' : 'text-dark' }}">
                                        Rp {{ number_format($ringkasanBulan['netPendanaan'], 0, ',', '.') }}
                                    </td>
                                </tr>
                                <tr class="border-top border-2">
                                    <td class="ps-4 py-3 fw-bold text-dark">
                                        Total Estimasi Perubahan Kas Bulan Ini
                                    </td>
                                    <td class="text-end pe-4 py-3 fw-bold {{ $totalNetBulan >= 0 ? 'text-success' : 'text-danger' }} fs-6">
                                        Rp {{ number_format($totalNetBulan, 0, ',', '.') }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-top py-3 d-flex justify-content-end gap-2">
                    <a href="{{ url('/cashflow-detail/' . $currentYear . '/' . str_pad($currentMonth, 2, '0', STR_PAD_LEFT)) }}" class="btn btn-sm btn-primary">
                        <i class="mdi mdi-eye-outline me-1"></i> Buka Detail Bulan Ini
                    </a>
                    <a href="{{ url('/cashflow-print/' . $currentYear . '/' . str_pad($currentMonth, 2, '0', STR_PAD_LEFT)) }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                        <i class="mdi mdi-printer-outline me-1"></i> Cetak PDF
                    </a>
                </div>
            </div>
        </div>

        {{-- Tahun Berjalan --}}
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-bottom py-3 d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <div class="avatar avatar-sm rounded bg-label-info">
                            <i class="mdi mdi-calendar-range fs-5"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-bold">Ringkasan Tahun Ini</h6>
                            <small class="text-muted">Tahun {{ $ringkasanTahunLabel }}</small>
                        </div>
                    </div>
                    <span class="badge bg-label-secondary">Tahun Berjalan</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 cashflow-table">
                            <tbody>
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-semibold text-dark"><i class="mdi mdi-cash-plus text-success me-2"></i>Kas Masuk Operasi</div>
                                        <small class="text-muted">Total Pelunasan Penjualan PO YTD</small>
                                    </td>
                                    <td class="text-end pe-4 fw-bold text-success fs-6">
                                        Rp {{ number_format($ringkasanTahun['kasMasuk'], 0, ',', '.') }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-semibold text-dark"><i class="mdi mdi-cash-minus text-danger me-2"></i>Kas Keluar Operasi</div>
                                        <small class="text-muted">Total Beban Operasional Kas YTD</small>
                                    </td>
                                    <td class="text-end pe-4 fw-bold text-danger fs-6">
                                        (Rp {{ number_format($ringkasanTahun['kasKeluar'], 0, ',', '.') }})
                                    </td>
                                </tr>
                                <tr class="table-light">
                                    <td class="ps-4 fw-bold text-primary">
                                        <i class="mdi mdi-sigma me-2"></i>Net Arus Kas Aktivitas Operasi
                                    </td>
                                    <td class="text-end pe-4 fw-bold {{ $netOperasiTahun >= 0 ? 'text-primary' : 'text-danger' }} fs-6">
                                        Rp {{ number_format($netOperasiTahun, 0, ',', '.') }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-semibold text-dark"><i class="mdi mdi-domain text-info me-2"></i>Net Aktivitas Investasi</div>
                                        <small class="text-muted">Total Pembelian/Penjualan Aset Tetap YTD</small>
                                    </td>
                                    <td class="text-end pe-4 fw-semibold {{ $ringkasanTahun['netInvestasi'] < 0 ? 'text-danger' : 'text-dark' }}">
                                        Rp {{ number_format($ringkasanTahun['netInvestasi'], 0, ',', '.') }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-semibold text-dark"><i class="mdi mdi-account-cash text-warning me-2"></i>Net Aktivitas Pendanaan</div>
                                        <small class="text-muted">Total Penarikan Prive Pemilik YTD</small>
                                    </td>
                                    <td class="text-end pe-4 fw-semibold {{ $ringkasanTahun['netPendanaan'] < 0 ? 'text-danger' : 'text-dark' }}">
                                        Rp {{ number_format($ringkasanTahun['netPendanaan'], 0, ',', '.') }}
                                    </td>
                                </tr>
                                <tr class="border-top border-2">
                                    <td class="ps-4 py-3 fw-bold text-dark">
                                        Total Estimasi Perubahan Kas Tahun Ini
                                    </td>
                                    <td class="text-end pe-4 py-3 fw-bold {{ $totalNetTahun >= 0 ? 'text-success' : 'text-danger' }} fs-6">
                                        Rp {{ number_format($totalNetTahun, 0, ',', '.') }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-top py-3 d-flex justify-content-end gap-2">
                    <a href="{{ url('/cashflow-detail/' . $currentYear) }}" class="btn btn-sm btn-primary">
                        <i class="mdi mdi-eye-outline me-1"></i> Buka Detail Tahun Ini
                    </a>
                    <a href="{{ url('/cashflow-print/' . $currentYear) }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                        <i class="mdi mdi-printer-outline me-1"></i> Cetak PDF
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Period Selector Card --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-transparent border-bottom py-3">
            <h6 class="card-title mb-0 fw-bold text-dark">
                <i class="mdi mdi-calendar-search-outline me-2 text-primary fs-5"></i>
                Pilih Periode Laporan Cashflow (Detail &amp; Cetak)
            </h6>
        </div>
        <div class="card-body py-4">
            <div class="row g-4">
                {{-- Periode Bulanan --}}
                <div class="col-md-6 border-end-md">
                    <div class="p-3 bg-light rounded-3 h-100">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <span class="avatar avatar-xs rounded bg-label-primary">
                                <i class="mdi mdi-calendar-month"></i>
                            </span>
                            <h6 class="mb-0 fw-bold">Laporan Arus Kas Bulanan</h6>
                        </div>
                        <p class="text-muted small mb-3">Pilih bulan dan tahun untuk melihat detail arus kas keluar masuk operasional.</p>
                        <div class="form-floating form-floating-outline mb-3">
                            <input class="form-control" type="month" id="cashflowMonthInput" value="{{ $currentYear }}-{{ str_pad($currentMonth, 2, '0', STR_PAD_LEFT) }}">
                            <label for="cashflowMonthInput">Bulan &amp; Tahun</label>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="button" id="btnViewMonth" class="btn btn-primary flex-fill">
                                <i class="mdi mdi-eye-outline me-1"></i> Buka Laporan
                            </button>
                            <button type="button" id="btnPrintMonth" class="btn btn-outline-secondary flex-fill">
                                <i class="mdi mdi-printer-outline me-1"></i> Cetak PDF
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Periode Tahunan --}}
                <div class="col-md-6">
                    <div class="p-3 bg-light rounded-3 h-100">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <span class="avatar avatar-xs rounded bg-label-info">
                                <i class="mdi mdi-calendar-range"></i>
                            </span>
                            <h6 class="mb-0 fw-bold">Laporan Arus Kas Tahunan (Annual)</h6>
                        </div>
                        <p class="text-muted small mb-3">Pilih tahun buku fiskal untuk audit tahunan aktivitas operasi, investasi &amp; modal.</p>
                        <div class="form-floating form-floating-outline mb-3">
                            <select id="cashflowYearSelect" class="form-select">
                                @for ($y = $currentYear + 2; $y >= $currentYear - 5; $y--)
                                    <option value="{{ $y }}" {{ $y == $currentYear ? 'selected' : '' }}>Tahun {{ $y }}</option>
                                @endfor
                            </select>
                            <label for="cashflowYearSelect">Tahun Fiskal</label>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="button" id="btnViewYear" class="btn btn-primary flex-fill">
                                <i class="mdi mdi-eye-outline me-1"></i> Buka Laporan
                            </button>
                            <button type="button" id="btnPrintYear" class="btn btn-outline-secondary flex-fill">
                                <i class="mdi mdi-printer-outline me-1"></i> Cetak PDF
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- PSAK Standard Accounting Note --}}
    <div class="alert alert-primary alert-dismissible d-flex align-items-start gap-3 border-0 shadow-sm" role="alert">
        <span class="avatar avatar-sm rounded bg-primary text-white flex-shrink-0 mt-1">
            <i class="mdi mdi-information-variant fs-5"></i>
        </span>
        <div class="d-flex flex-column flex-grow-1">
            <h6 class="alert-heading fw-bold mb-1">Standar Pelaporan Arus Kas (PSAK 2 / SAK ETAP)</h6>
            <div class="small text-muted">
                Laporan Arus Kas mengklasifikasikan mutasi kas ke dalam 3 pilar: 
                <strong>Aktivitas Operasi</strong> (penerimaan kas dari pelanggan &amp; pengeluaran beban operasional),
                <strong>Aktivitas Investasi</strong> (perolehan dan pelepasan aset tetap), serta 
                <strong>Aktivitas Pendanaan</strong> (transaksi modal &amp; prive).
                Data kas tersinkronisasi secara otomatis dengan modul Purchase Order, Expense Kas Umum, dan Jurnal Aset.
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>

</div>
@endsection

@push('after-script')
    <script src="{{ asset('assets') }}/vendor/libs/moment/moment.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.js"></script>
@endpush

@push('script')
    <script>
        $(document).ready(function() {
            // Button View Monthly Cashflow
            $('#btnViewMonth').on('click', function() {
                var monthVal = $('#cashflowMonthInput').val();
                if (!monthVal) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Pilih Periode',
                        text: 'Silakan pilih bulan dan tahun terlebih dahulu.',
                        customClass: { confirmButton: 'btn btn-primary' },
                        buttonsStyling: false
                    });
                    return;
                }
                var parts = monthVal.split('-');
                var year = parts[0];
                var month = parts[1];
                window.location.href = `/cashflow-detail/${year}/${month}`;
            });

            // Button Print Monthly Cashflow
            $('#btnPrintMonth').on('click', function() {
                var monthVal = $('#cashflowMonthInput').val();
                if (!monthVal) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Pilih Periode',
                        text: 'Silakan pilih bulan dan tahun terlebih dahulu.',
                        customClass: { confirmButton: 'btn btn-primary' },
                        buttonsStyling: false
                    });
                    return;
                }
                var parts = monthVal.split('-');
                var year = parts[0];
                var month = parts[1];
                window.open(`/cashflow-print/${year}/${month}`, '_blank');
            });

            // Button View Yearly Cashflow
            $('#btnViewYear').on('click', function() {
                var yearVal = $('#cashflowYearSelect').val();
                if (!yearVal) return;
                window.location.href = `/cashflow-detail/${yearVal}`;
            });

            // Button Print Yearly Cashflow
            $('#btnPrintYear').on('click', function() {
                var yearVal = $('#cashflowYearSelect').val();
                if (!yearVal) return;
                window.open(`/cashflow-print/${yearVal}`, '_blank');
            });
        });
    </script>
@endpush
