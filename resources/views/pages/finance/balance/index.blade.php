@extends('layouts.sales.app')
@section('title', 'Balance Statement (Neraca) - Finance ERP')

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
        .balance-item-row {
            padding: 0.65rem 0.85rem;
            border-bottom: 1px solid #f1f5f9;
            transition: background 0.15s ease;
        }
        .balance-item-row:hover {
            background-color: #f8fafc;
        }
        .dark-style .balance-item-row:hover {
            background-color: rgba(255, 255, 255, 0.03);
        }
    </style>
@endpush

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    {{-- Page Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center py-3 mb-3 gap-3">
        <div>
            <h4 class="fw-bold mb-1">
                <span class="text-muted fw-light">Finance / Statement /</span> Balance Statement
            </h4>
            <p class="text-muted mb-0 small">
                <i class="mdi mdi-scale-balance me-1 text-primary"></i> Laporan Neraca Keuangan (Posisi Aset Aktiva Lancar &amp; Tetap vs Kewajiban &amp; Ekuitas Modal)
            </p>
        </div>
        <div class="d-flex flex-wrap align-items-center gap-2">
            <a href="{{ route('fixed.index') }}" class="btn btn-outline-primary btn-sm px-3 shadow-sm">
                <i class="mdi mdi-domain me-1"></i> Data Fixed Asset
            </a>
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
            <a class="nav-link active" href="{{ route('expense-balance.index') }}">
                <i class="mdi mdi-scale-balance me-1"></i> Balance Statement (Neraca)
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('expense-equity.index') }}">
                <i class="mdi mdi-chart-donut me-1"></i> Equity Statement (Perubahan Modal)
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('expense-cashflow.index') }}">
                <i class="mdi mdi-cash-sync me-1"></i> Cashflow Statement (Arus Kas)
            </a>
        </li>
    </ul>

    {{-- 4 Executive Finance KPI Cards (Aktiva vs Pasiva) --}}
    <div class="row g-3 mb-4">
        {{-- KPI 1: Total Aset (Aktiva Keseluruhan) --}}
        <div class="col-xl-3 col-sm-6 col-12">
            <div class="card finance-kpi-card h-100 rounded-3">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small fw-semibold">Total Aset (Aktiva)</span>
                        <div class="avatar avatar-sm bg-label-primary rounded-3 d-flex align-items-center justify-content-center">
                            <i class="mdi mdi-shield-check-outline fs-4"></i>
                        </div>
                    </div>
                    <h4 class="fw-bold text-primary mb-1">
                        Rp {{ number_format($totalAset, 0, ',', '.') }}
                    </h4>
                    <div class="d-flex align-items-center justify-content-between small">
                        <span class="text-muted">Aset Lancar + Tetap</span>
                        <span class="badge bg-label-primary fw-bold rounded-pill">Total Aktiva</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- KPI 2: Total Aset Lancar --}}
        <div class="col-xl-3 col-sm-6 col-12">
            <div class="card finance-kpi-card h-100 rounded-3">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small fw-semibold">Aset Lancar (Current Asset)</span>
                        <div class="avatar avatar-sm bg-label-info rounded-3 d-flex align-items-center justify-content-center">
                            <i class="mdi mdi-cash-multiple fs-4"></i>
                        </div>
                    </div>
                    <h4 class="fw-bold text-info mb-1">
                        Rp {{ number_format($asetLancar, 0, ',', '.') }}
                    </h4>
                    <div class="d-flex align-items-center justify-content-between small">
                        <span class="text-muted">Kas + Piutang + Persediaan</span>
                        <span class="badge bg-label-info fw-bold rounded-pill">Likuid</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- KPI 3: Nilai Buku Aset Tetap Bersih --}}
        <div class="col-xl-3 col-sm-6 col-12">
            <div class="card finance-kpi-card h-100 rounded-3">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small fw-semibold">Aset Tetap Bersih (Net Fixed)</span>
                        <div class="avatar avatar-sm bg-label-warning rounded-3 d-flex align-items-center justify-content-center">
                            <i class="mdi mdi-domain fs-4"></i>
                        </div>
                    </div>
                    <h4 class="fw-bold text-dark mb-1">
                        Rp {{ number_format($asetTetapBersih, 0, ',', '.') }}
                    </h4>
                    <div class="d-flex align-items-center justify-content-between small">
                        <span class="text-muted">Setelah depresiasi penyusutan</span>
                        <span class="badge bg-label-warning text-dark fw-bold rounded-pill">Net Book</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- KPI 4: Total Ekuitas Modal --}}
        <div class="col-xl-3 col-sm-6 col-12">
            <div class="card finance-kpi-card h-100 rounded-3">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small fw-semibold">Ekuitas &amp; Modal Bersih</span>
                        <div class="avatar avatar-sm bg-label-success rounded-3 d-flex align-items-center justify-content-center">
                            <i class="mdi mdi-scale-balance fs-4"></i>
                        </div>
                    </div>
                    <h4 class="fw-bold text-success mb-1">
                        Rp {{ number_format($totalEkuitas, 0, ',', '.') }}
                    </h4>
                    <div class="d-flex align-items-center justify-content-between small">
                        <span class="text-muted">Modal + Laba - Prive</span>
                        <span class="badge bg-label-success fw-bold rounded-pill">Equity</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Period Selector & Report Actions Toolbar (Single Row with Selections) --}}
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body py-3 px-4">
            <div class="d-flex flex-column flex-xl-row justify-content-between align-items-xl-center gap-3">
                {{-- Left: Icon & Title in 1 clean line --}}
                <div class="d-flex align-items-center gap-2">
                    <i class="mdi mdi-file-chart-outline text-primary fs-4"></i>
                    <div>
                        <h6 class="card-title mb-0 fw-bold text-dark">
                            Generator Laporan Balance Statement (Neraca)
                        </h6>
                        <small class="text-muted d-none d-sm-inline">Pilih periode untuk melihat rincian neraca komprehensif atau mencetak dokumen resmi</small>
                    </div>
                </div>

                {{-- Right: All filters & actions in 1 neat row --}}
                <div class="d-flex flex-wrap align-items-center gap-2">
                    {{-- Dropdown Bulan --}}
                    <div class="input-group input-group-sm" style="width: auto;">
                        <span class="input-group-text bg-light text-muted">
                            <i class="mdi mdi-calendar-month-outline"></i>
                        </span>
                        <select id="balance-month-select" class="form-select form-select-sm" style="min-width: 140px;">
                            <option value="all">Semua Bulan (1 Thn)</option>
                            @php
                                $bulanOptions = [
                                    '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
                                    '04' => 'April', '05' => 'Mei', '06' => 'Juni',
                                    '07' => 'Juli', '08' => 'Agustus', '09' => 'September',
                                    '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
                                ];
                            @endphp
                            @foreach($bulanOptions as $mNum => $mName)
                                <option value="{{ $mNum }}" {{ (int)$mNum == (int)date('m') ? 'selected' : '' }}>
                                    {{ $mName }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Dropdown Tahun --}}
                    <div class="input-group input-group-sm" style="width: auto;">
                        <span class="input-group-text bg-light text-muted">
                            <i class="mdi mdi-calendar-range"></i>
                        </span>
                        <select id="balance-year-select" class="form-select form-select-sm" style="min-width: 105px;">
                            @foreach($years as $yr)
                                <option value="{{ $yr }}" {{ $yr == $currentYear ? 'selected' : '' }}>
                                    Tahun {{ $yr }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Tombol Buka Neraca --}}
                    <button type="button" id="btn-view-balance" class="btn btn-primary btn-sm px-3 shadow-xs">
                        <i class="mdi mdi-eye-outline me-1"></i> Buka Neraca
                    </button>

                    {{-- Tombol Cetak PDF --}}
                    <button type="button" id="btn-print-balance" class="btn btn-outline-primary btn-sm px-3 shadow-xs">
                        <i class="mdi mdi-printer me-1"></i> Cetak PDF
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Ringkasan Komposisi Neraca (Aktiva vs Pasiva / Ekuitas) --}}
    <div class="row g-3">
        {{-- Sisi Kiri: Rincian Aset (Aktiva) --}}
        <div class="col-lg-6 col-12">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
                    <h6 class="card-title mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                        <i class="mdi mdi-wallet-outline text-primary fs-5"></i>
                        Rincian Komponen Aset (Aktiva)
                    </h6>
                    <span class="badge bg-label-primary fw-bold">Posisi {{ Carbon\Carbon::now()->translatedFormat('F Y') }}</span>
                </div>
                <div class="card-body p-0">
                    <div class="balance-item-row d-flex justify-content-between align-items-center">
                        <div>
                            <span class="fw-semibold text-dark">Kas &amp; Saldo Rekening Bank (BCA)</span>
                            <small class="d-block text-muted">Saldo kas cair di rekening bank operasional</small>
                        </div>
                        <span class="fw-bold text-dark">Rp {{ number_format($bankSaldo, 0, ',', '.') }}</span>
                    </div>

                    <div class="balance-item-row d-flex justify-content-between align-items-center">
                        <div>
                            <span class="fw-semibold text-dark">Piutang Usaha (Account Receivable)</span>
                            <small class="d-block text-muted">Tagihan invoice tempo yang belum lunas</small>
                        </div>
                        <span class="fw-bold text-dark">Rp {{ number_format($piutang, 0, ',', '.') }}</span>
                    </div>

                    <div class="balance-item-row d-flex justify-content-between align-items-center">
                        <div>
                            <span class="fw-semibold text-dark">Persediaan Barang Dagang (Inventory Stock)</span>
                            <small class="d-block text-muted">Total nilai modal persediaan sparepart &amp; unit fisik</small>
                        </div>
                        <span class="fw-bold text-dark">Rp {{ number_format($persediaan, 0, ',', '.') }}</span>
                    </div>

                    <div class="balance-item-row bg-light d-flex justify-content-between align-items-center">
                        <span class="fw-bold text-primary">Subtotal Aset Lancar</span>
                        <span class="fw-bold text-primary fs-6">Rp {{ number_format($asetLancar, 0, ',', '.') }}</span>
                    </div>

                    <div class="balance-item-row d-flex justify-content-between align-items-center">
                        <div>
                            <span class="fw-semibold text-dark">Aset Tetap (Nilai Perolehan / Gross Fixed Asset)</span>
                            <small class="d-block text-muted">Mesin, kendaraan, inventaris kantor</small>
                        </div>
                        <span class="fw-bold text-dark">Rp {{ number_format($totalFixed, 0, ',', '.') }}</span>
                    </div>

                    <div class="balance-item-row d-flex justify-content-between align-items-center">
                        <div>
                            <span class="fw-semibold text-danger">Akumulasi Penyusutan (Depreciation)</span>
                            <small class="d-block text-muted">Alokasi beban depresiasi aset tetap</small>
                        </div>
                        <span class="fw-bold text-danger">- Rp {{ number_format($penyusutan, 0, ',', '.') }}</span>
                    </div>

                    <div class="balance-item-row bg-light d-flex justify-content-between align-items-center">
                        <span class="fw-bold text-dark">Subtotal Aset Tetap Bersih</span>
                        <span class="fw-bold text-dark fs-6">Rp {{ number_format($asetTetapBersih, 0, ',', '.') }}</span>
                    </div>

                    <div class="p-3 bg-primary-subtle d-flex justify-content-between align-items-center rounded-bottom">
                        <span class="fw-bold text-primary fs-6">TOTAL KESELURUHAN AKTIVA</span>
                        <span class="fw-bold text-primary fs-5">Rp {{ number_format($totalAset, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sisi Kanan: Rincian Ekuitas Modal (Pasiva) --}}
        <div class="col-lg-6 col-12">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
                    <h6 class="card-title mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                        <i class="mdi mdi-chart-donut text-success fs-5"></i>
                        Rincian Komponen Ekuitas (Modal Pemilik)
                    </h6>
                    <span class="badge bg-label-success fw-bold">Posisi {{ Carbon\Carbon::now()->translatedFormat('F Y') }}</span>
                </div>
                <div class="card-body p-0">
                    <div class="balance-item-row d-flex justify-content-between align-items-center">
                        <div>
                            <span class="fw-semibold text-dark">Saldo Laba Tahun Lalu (Retained Earnings)</span>
                            <small class="d-block text-muted">Akumulasi laba ditahan dari tahun-tahun sebelumnya</small>
                        </div>
                        <span class="fw-bold text-dark">Rp {{ number_format($balanceData['labaTahunLalu'] ?? 0, 0, ',', '.') }}</span>
                    </div>

                    <div class="balance-item-row d-flex justify-content-between align-items-center">
                        <div>
                            <span class="fw-semibold text-success">Laba Bersih Periode Berjalan</span>
                            <small class="d-block text-muted">Kontribusi profit periode bulan berjalan</small>
                        </div>
                        <span class="fw-bold text-success">+ Rp {{ number_format($balanceData['labaBulanIni'] ?? 0, 0, ',', '.') }}</span>
                    </div>

                    <div class="balance-item-row d-flex justify-content-between align-items-center">
                        <div>
                            <span class="fw-semibold text-danger">Pengambilan Prive Pemilik (Drawings)</span>
                            <small class="d-block text-muted">Penarikan modal/dividen oleh pemilik perusahaan</small>
                        </div>
                        <span class="fw-bold text-danger">- Rp {{ number_format($balanceData['prive'] ?? 0, 0, ',', '.') }}</span>
                    </div>

                    <div class="p-3 bg-success-subtle d-flex justify-content-between align-items-center rounded-bottom mt-auto">
                        <span class="fw-bold text-success fs-6">TOTAL EKUITAS &amp; MODAL BERSIH</span>
                        <span class="fw-bold text-success fs-5">Rp {{ number_format($totalEkuitas, 0, ',', '.') }}</span>
                    </div>

                    <div class="p-3">
                        <div class="alert alert-info d-flex align-items-center gap-2 mb-0 py-2 small" role="alert">
                            <i class="mdi mdi-information-outline fs-5"></i>
                            <span>Klik tombol <strong>"Buka Neraca"</strong> di toolbar atas untuk melihat lembar pembukuan detail per akun akun neraca.</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('after-script')
    <script src="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.js"></script>
@endpush

@push('page-script')
    <script>
        $(function() {
            $('#btn-view-balance').on('click', function() {
                var m = $('#balance-month-select').val();
                var y = $('#balance-year-select').val();
                if (m && m !== 'all') {
                    window.location.href = '/balance-detail/' + y + '/' + m;
                } else {
                    window.location.href = '/balance-detail/' + y;
                }
            });

            $('#btn-print-balance').on('click', function() {
                var m = $('#balance-month-select').val();
                var y = $('#balance-year-select').val();
                if (m && m !== 'all') {
                    window.open('/balance-print/' + m + '/' + y, '_blank');
                } else {
                    window.open('/balance-print/' + y, '_blank');
                }
            });
        });
    </script>
@endpush
