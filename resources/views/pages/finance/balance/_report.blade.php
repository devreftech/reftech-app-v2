{{-- Konten laporan Balance Statement, dipakai bareng oleh print.blade.php & detail.blade.php --}}

@php
    $totalKasBank = $totalKasBank ?? (($bank->saldo ?? 0) + ($capPalembang ?? 425000000) + ($modPalembang ?? 575000000));
    $totalLancar = $totalKasBank + $piutang + $asset + $ppnMas;
    $totalTetap = $totalFixed - $grandTotalPenyusutan;
    $totalAktiva = $totalLancar + $totalTetap;

    if (@$month) {
        $ekuitas = 250000000 + $labaTahunTahun - $prive - $labaBulanIni;
        $totalekuitas = $ekuitas + $labaBulanIni;
        $sebelumnya = $labaTahunTahun - $labaBulanIni;
        $labaPeriodeIni = $labaBulanIni;
        $periodeLabel = 'Bulan Ini';
    } else {
        $ekuitas = 250000000 + $labaTahunTahun - $prive - $labaTahunIni;
        $totalekuitas = $ekuitas + $labaTahunIni;
        $sebelumnya = $labaTahunTahun - $labaTahunIni;
        $labaPeriodeIni = $labaTahunIni;
        $periodeLabel = 'Tahun Ini';
    }
    $totalKewajiban = $ppnKel ?? 0;
    $ekujiban = $totalekuitas + $totalKewajiban;
    $selisih = $totalAktiva - $ekujiban;
    $isBalanced = round($totalAktiva) === round($ekujiban);
    $isPrint = $isPrintMode ?? false;
@endphp

{{-- Scoped Styles for Modern Financial Balance Sheet --}}
<style>
    .bs-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 12.5px;
    }
    .bs-table td {
        padding: 5.5px 12px;
        vertical-align: middle;
        line-height: 1.4;
    }
    .bs-sec-header td {
        background-color: #f1f5f9;
        font-weight: 700;
        color: #1e293b;
        font-size: 11.5px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding-top: 8px;
        padding-bottom: 8px;
        border-top: 1px solid #e2e8f0;
        border-bottom: 1px solid #cbd5e1;
    }
    .bs-group-header td {
        font-weight: 600;
        color: #334155;
        font-size: 12px;
        background-color: #fafbfd;
        padding-top: 6px;
        padding-bottom: 5px;
        border-top: 1px dashed #edf2f7;
    }
    .bs-row-item td {
        color: #475569;
        border-bottom: 1px solid #f8fafc;
    }
    .bs-row-item:hover td {
        background-color: #f8faff;
    }
    .bs-row-subtotal td {
        background-color: #f8fafc;
        border-top: 1px dashed #cbd5e1;
        border-bottom: 1px solid #e2e8f0;
        color: #1e293b;
        font-size: 12px;
        padding-top: 5.5px;
        padding-bottom: 5.5px;
    }
    .bs-row-total-cat td {
        background-color: #edf2f7;
        border-top: 2px solid #cbd5e1;
        border-bottom: 2px solid #cbd5e1;
        font-size: 12.5px;
        padding-top: 7px;
        padding-bottom: 7px;
    .bs-row-grand-total td {
        background-color: #f1f5f9 !important;
        color: #0f172a !important;
        font-weight: 800 !important;
        border-top: 2px solid #0f172a !important;
        border-bottom: 4px double #0f172a !important;
        padding-top: 9px !important;
        padding-bottom: 9px !important;
    }
    .bs-row-grand-total td * {
        color: #0f172a !important;
    }
    .bs-amount {
        font-variant-numeric: tabular-nums;
        white-space: nowrap;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, monospace;
    }
    .balance-card {
        border-radius: 8px;
        overflow: hidden;
        font-family: 'Inter', sans-serif;
    }

@if(!$isPrint)
    /* Fallback Print Styles (Hanya saat cetak langsung dari layar detail balance) */
    @media print {
        @page {
            size: A4 portrait;
            margin: 8mm 10mm;
        }
        body {
            background: #fff !important;
            color: #000 !important;
            font-size: 11px !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        .no-print {
            display: none !important;
        }
        .balance-card {
            border: 1px solid #64748b !important;
            box-shadow: none !important;
            page-break-inside: auto !important;
        }
        .bs-table {
            font-size: 10px !important;
            table-layout: fixed !important;
        }
        .bs-table td {
            padding: 2.5px 5px !important;
        }
        .bs-row-grand-total td {
            background-color: #f1f5f9 !important;
            color: #000000 !important;
            border-top: 2px solid #000000 !important;
            border-bottom: 4px double #000000 !important;
        }
        .bs-sec-header td {
            background-color: #e2e8f0 !important;
            color: #000000 !important;
        }
        .bs-row-total-cat td {
            background-color: #edf2f7 !important;
            color: #000000 !important;
            border-top: 1px solid #000000 !important;
            border-bottom: 1px solid #000000 !important;
        }
        .badge {
            border: 1px solid #94a3b8 !important;
            color: #000000 !important;
            background: transparent !important;
        }
    }
@endif
</style>

{{-- Letterhead Perusahaan (Simple & Clean) --}}
<div class="balance-letterhead text-center mb-4 pb-3 border-bottom">
    <h4 class="fw-bold text-dark mb-1" style="letter-spacing: 0.5px;">PT. REFTECH JAYA OPTIMA</h4>
    <h5 class="fw-bold text-primary mb-1">Neraca (Standar)</h5>
    <p class="text-muted mb-0 small">Dari <strong>{{ $startString }}</strong> ke <strong>{{ $endString }}</strong></p>
</div>

{{-- Main Balance Sheet Grid (Skontro 2-Kolom / Stafel 1-Kolom) --}}
<div class="row g-4 balance-sheet-grid" id="balanceSheetGrid">

    {{-- ==================== KOLOM KIRI: AKTIVA (ASSETS) ==================== --}}
    <div class="col-md-6 balance-col" id="colAktiva">
        <div class="card border shadow-none balance-card h-100">
            <div class="card-header bg-label-primary py-2.5 px-3 d-flex justify-content-between align-items-center">
                <span class="fw-bold text-primary d-flex align-items-center gap-2">
                    <i class="mdi mdi-wallet-plus-outline fs-5"></i> AKTIVA (ASET / ASSETS)
                </span>
                <span class="badge bg-primary rounded-pill">Rp {{ number_format($totalAktiva, 0, ',', '.') }}</span>
            </div>
            <div class="card-body p-0">
                <table class="table table-sm bs-table mb-0">
                    <colgroup>
                        <col class="col-bs-label" style="width: 63%;">
                        <col class="col-bs-amount" style="width: 37%;">
                    </colgroup>
                    <tbody>
                        {{-- 1. AKTIVA LANCAR --}}
                        <tr class="bs-sec-header">
                            <td colspan="2">
                                <i class="mdi mdi-lightning-bolt-outline me-1"></i> A. AKTIVA LANCAR (Current Assets)
                            </td>
                        </tr>

                        {{-- 1.1 Kas dan Setara Kas --}}
                        <tr class="bs-group-header">
                            <td colspan="2">1. Kas dan Setara Kas (Kas &amp; Bank)</td>
                        </tr>
                        <tr class="bs-row-item">
                            <td class="ps-4">
                                <span class="d-inline-flex align-items-center gap-1">
                                    <i class="mdi mdi-bank-outline text-muted small"></i> BCA IDR (Pusat Bandung)
                                </span>
                            </td>
                            <td class="text-end bs-amount">{{ number_format($bank->saldo ?? 0, 0, ',', '.') }}</td>
                        </tr>
                        @if(isset($palembangBanks) && $palembangBanks->count() > 0)
                            @foreach ($palembangBanks as $pBank)
                                <tr class="bs-row-item">
                                    <td class="ps-4">
                                        <span class="d-inline-flex align-items-center gap-1">
                                            <i class="mdi mdi-map-marker text-success small"></i> {{ $pBank->bank }}
                                        </span>
                                    </td>
                                    <td class="text-end bs-amount">{{ number_format($pBank->saldo, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        @else
                            <tr class="bs-row-item">
                                <td class="ps-4">
                                    <span class="d-inline-flex align-items-center gap-1">
                                        <i class="mdi mdi-map-marker text-success small"></i> BCA (Capital Palembang)
                                    </span>
                                </td>
                                <td class="text-end bs-amount">{{ number_format($capPalembang ?? 425000000, 0, ',', '.') }}</td>
                            </tr>
                            <tr class="bs-row-item">
                                <td class="ps-4">
                                    <span class="d-inline-flex align-items-center gap-1">
                                        <i class="mdi mdi-map-marker text-success small"></i> BCA (Modal Palembang)
                                    </span>
                                </td>
                                <td class="text-end bs-amount">{{ number_format($modPalembang ?? 575000000, 0, ',', '.') }}</td>
                            </tr>
                        @endif
                        <tr class="bs-row-subtotal">
                            <td class="ps-3 fw-semibold">Jumlah Kas dan Setara Kas</td>
                            <td class="text-end fw-semibold bs-amount">{{ number_format($totalKasBank, 0, ',', '.') }}</td>
                        </tr>

                        {{-- 1.2 Piutang Dagang --}}
                        <tr class="bs-group-header">
                            <td colspan="2">2. Piutang Dagang (Accounts Receivable)</td>
                        </tr>
                        <tr class="bs-row-item">
                            <td class="ps-4">Piutang Usaha (Jatuh Tempo)</td>
                            <td class="text-end bs-amount">{{ number_format($piutang, 0, ',', '.') }}</td>
                        </tr>
                        <tr class="bs-row-subtotal">
                            <td class="ps-3 fw-semibold">Jumlah Piutang Dagang</td>
                            <td class="text-end fw-semibold bs-amount">{{ number_format($piutang, 0, ',', '.') }}</td>
                        </tr>

                        {{-- 1.3 Persediaan --}}
                        <tr class="bs-group-header">
                            <td colspan="2">3. Persediaan (Inventories)</td>
                        </tr>
                        <tr class="bs-row-item">
                            <td class="ps-4">Persediaan Barang Dagang</td>
                            <td class="text-end bs-amount">{{ number_format($asset, 0, ',', '.') }}</td>
                        </tr>
                        <tr class="bs-row-subtotal">
                            <td class="ps-3 fw-semibold">Jumlah Persediaan</td>
                            <td class="text-end fw-semibold bs-amount">{{ number_format($asset, 0, ',', '.') }}</td>
                        </tr>

                        {{-- 1.4 Aktiva Lancar Lainnya --}}
                        <tr class="bs-group-header">
                            <td colspan="2">4. Aktiva Lancar Lainnya</td>
                        </tr>
                        <tr class="bs-row-item">
                            <td class="ps-4">PPN Masukan (11%)</td>
                            <td class="text-end bs-amount">{{ number_format($ppnMas, 0, ',', '.') }}</td>
                        </tr>
                        <tr class="bs-row-subtotal">
                            <td class="ps-3 fw-semibold">Jumlah Aktiva Lancar Lainnya</td>
                            <td class="text-end fw-semibold bs-amount">{{ number_format($ppnMas, 0, ',', '.') }}</td>
                        </tr>

                        {{-- TOTAL AKTIVA LANCAR --}}
                        <tr class="bs-row-total-cat">
                            <td class="fw-bold text-dark">
                                <i class="mdi mdi-sigma me-1 text-primary"></i> TOTAL AKTIVA LANCAR
                            </td>
                            <td class="text-end fw-bold text-primary bs-amount">{{ number_format($totalLancar, 0, ',', '.') }}</td>
                        </tr>

                        {{-- 2. AKTIVA TETAP --}}
                        <tr class="bs-sec-header">
                            <td colspan="2">
                                <i class="mdi mdi-office-building me-1"></i> B. AKTIVA TETAP (Fixed Assets)
                            </td>
                        </tr>

                        {{-- 2.1 Nilai Perolehan / Histori --}}
                        <tr class="bs-group-header">
                            <td colspan="2">1. Nilai Perolehan / Histori Aset Tetap</td>
                        </tr>
                        @forelse ($fixedAsset as $item)
                            <tr class="bs-row-item">
                                <td class="ps-4">{{ $item->type }}</td>
                                <td class="text-end bs-amount">{{ number_format($item->total_amount, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr class="bs-row-item">
                                <td class="ps-4 text-muted">- Tidak ada aset tetap tercatat -</td>
                                <td class="text-end bs-amount">0</td>
                            </tr>
                        @endforelse
                        <tr class="bs-row-subtotal">
                            <td class="ps-3 fw-semibold">Jumlah Nilai Perolehan Aset Tetap</td>
                            <td class="text-end fw-semibold bs-amount">{{ number_format($totalFixed ?? 0, 0, ',', '.') }}</td>
                        </tr>

                        {{-- 2.2 Akumulasi Penyusutan --}}
                        <tr class="bs-group-header">
                            <td colspan="2">2. Akumulasi Penyusutan (Accumulated Depreciation)</td>
                        </tr>
                        @forelse ($penyusutan as $item)
                            <tr class="bs-row-item">
                                <td class="ps-4 text-danger">Akum. Penys. {{ $item['type'] }}</td>
                                <td class="text-end text-danger bs-amount">- {{ number_format($item['total_penyusutan'], 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr class="bs-row-item">
                                <td class="ps-4 text-muted">-</td>
                                <td class="text-end bs-amount">0</td>
                            </tr>
                        @endforelse
                        <tr class="bs-row-subtotal">
                            <td class="ps-3 fw-semibold text-danger">Jumlah Akumulasi Penyusutan</td>
                            <td class="text-end fw-semibold text-danger bs-amount">- {{ number_format($grandTotalPenyusutan ?? 0, 0, ',', '.') }}</td>
                        </tr>

                        {{-- TOTAL AKTIVA TETAP NET --}}
                        <tr class="bs-row-total-cat">
                            <td class="fw-bold text-dark">
                                <i class="mdi mdi-sigma me-1 text-info"></i> TOTAL AKTIVA TETAP (NILAI BUKU NET)
                            </td>
                            <td class="text-end fw-bold text-info bs-amount">{{ number_format($totalTetap ?? 0, 0, ',', '.') }}</td>
                        </tr>

                        {{-- 3. OTHER ASSETS --}}
                        <tr class="bs-sec-header">
                            <td colspan="2">
                                <i class="mdi mdi-dots-horizontal me-1"></i> C. AKTIVA LAINNYA (Other Assets)
                            </td>
                        </tr>
                        <tr class="bs-row-subtotal">
                            <td class="ps-3 fw-semibold">Jumlah Aktiva Lainnya</td>
                            <td class="text-end fw-semibold bs-amount">0</td>
                        </tr>

                        {{-- GRAND TOTAL AKTIVA --}}
                        <tr class="bs-row-grand-total">
                            <td class="fw-bolder fs-6">
                                <i class="mdi mdi-check-circle-outline me-1"></i> JUMLAH TOTAL AKTIVA
                            </td>
                            <td class="text-end fw-bolder fs-6 bs-amount">{{ number_format($totalAktiva, 0, ',', '.') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ==================== KOLOM KANAN: PASIVA (KEWAJIBAN & EKUITAS) ==================== --}}
    <div class="col-md-6 balance-col" id="colPasiva">
        <div class="card border shadow-none balance-card h-100">
            <div class="card-header bg-label-info py-2.5 px-3 d-flex justify-content-between align-items-center">
                <span class="fw-bold text-info d-flex align-items-center gap-2">
                    <i class="mdi mdi-scale-balance fs-5"></i> PASIVA (KEWAJIBAN &amp; EKUITAS)
                </span>
                <span class="badge bg-info rounded-pill">Rp {{ number_format($ekujiban ?? 0, 0, ',', '.') }}</span>
            </div>
            <div class="card-body p-0">
                <table class="table table-sm bs-table mb-0">
                    <colgroup>
                        <col class="col-bs-label" style="width: 63%;">
                        <col class="col-bs-amount" style="width: 37%;">
                    </colgroup>
                    <tbody>
                        {{-- 1. KEWAJIBAN --}}
                        <tr class="bs-sec-header">
                            <td colspan="2">
                                <i class="mdi mdi-credit-card-clock-outline me-1"></i> A. KEWAJIBAN (Liabilities)
                            </td>
                        </tr>

                        {{-- 1.1 Kewajiban Lancar --}}
                        <tr class="bs-group-header">
                            <td colspan="2">1. Kewajiban Lancar (Current Liabilities)</td>
                        </tr>
                        <tr class="bs-row-item">
                            <td class="ps-4">Hutang Dagang / Usaha (Accounts Payable)</td>
                            <td class="text-end bs-amount">0</td>
                        </tr>
                        <tr class="bs-row-item">
                            <td class="ps-4">PPN Keluaran (11%)</td>
                            <td class="text-end bs-amount">{{ number_format($ppnKel, 0, ',', '.') }}</td>
                        </tr>
                        <tr class="bs-row-subtotal">
                            <td class="ps-3 fw-semibold">Jumlah Kewajiban Lancar</td>
                            <td class="text-end fw-semibold bs-amount">{{ number_format($ppnKel ?? 0, 0, ',', '.') }}</td>
                        </tr>

                        {{-- 1.2 Kewajiban Jangka Panjang --}}
                        <tr class="bs-group-header">
                            <td colspan="2">2. Kewajiban Jangka Panjang (Long-Term Liabilities)</td>
                        </tr>
                        <tr class="bs-row-item">
                            <td class="ps-4">Hutang Bank Jangka Panjang</td>
                            <td class="text-end bs-amount">0</td>
                        </tr>
                        <tr class="bs-row-subtotal">
                            <td class="ps-3 fw-semibold">Jumlah Kewajiban Jangka Panjang</td>
                            <td class="text-end fw-semibold bs-amount">0</td>
                        </tr>

                        {{-- TOTAL KEWAJIBAN --}}
                        <tr class="bs-row-total-cat">
                            <td class="fw-bold text-dark">
                                <i class="mdi mdi-sigma me-1 text-warning"></i> TOTAL KEWAJIBAN (LIABILITIES)
                            </td>
                            <td class="text-end fw-bold text-warning bs-amount">{{ number_format($ppnKel ?? 0, 0, ',', '.') }}</td>
                        </tr>

                        {{-- 2. EKUITAS --}}
                        <tr class="bs-sec-header">
                            <td colspan="2">
                                <i class="mdi mdi-account-cash-outline me-1"></i> B. EKUITAS (Equity)
                            </td>
                        </tr>

                        {{-- 2.1 Modal Saham & Laba Akumulasi --}}
                        <tr class="bs-group-header">
                            <td colspan="2">1. Modal Saham &amp; Laba Akumulasi</td>
                        </tr>
                        <tr class="bs-row-item">
                            <td class="ps-4">
                                <span class="d-inline-flex align-items-center gap-1">
                                    <i class="mdi mdi-cash-multiple text-primary small"></i> Modal Disetor (Capital Stock)
                                </span>
                            </td>
                            <td class="text-end bs-amount">{{ number_format(250000000, 0, ',', '.') }}</td>
                        </tr>
                        <tr class="bs-row-item">
                            <td class="ps-4">
                                <span class="d-inline-flex align-items-center gap-1">
                                    <i class="mdi mdi-history text-muted small"></i> Laba Ditahan (Retained Earnings Tahun Lalu)
                                </span>
                            </td>
                            <td class="text-end bs-amount">{{ number_format($labaTahunLalu, 0, ',', '.') }}</td>
                        </tr>
                        <tr class="bs-row-item">
                            <td class="ps-4">
                                <span class="d-inline-flex align-items-center gap-1 text-danger">
                                    <i class="mdi mdi-cash-minus small"></i> Prive (Pengambilan Pribadi Pemilik)
                                </span>
                            </td>
                            <td class="text-end text-danger bs-amount">- {{ number_format($prive, 0, ',', '.') }}</td>
                        </tr>
                        <tr class="bs-row-item">
                            <td class="ps-4">
                                <span class="d-inline-flex align-items-center gap-1">
                                    <i class="mdi mdi-calendar-clock text-muted small"></i> Laba Tahun Sebelumnya
                                </span>
                            </td>
                            <td class="text-end bs-amount">{{ number_format($sebelumnya, 0, ',', '.') }}</td>
                        </tr>
                        <tr class="bs-row-item">
                            <td class="ps-4">
                                <span class="d-inline-flex align-items-center gap-1 text-muted">
                                    <i class="mdi mdi-circle-outline small"></i> Opening Balance Equity
                                </span>
                            </td>
                            <td class="text-end text-muted bs-amount">0</td>
                        </tr>
                        <tr class="bs-row-item">
                            <td class="ps-4">
                                <span class="d-inline-flex align-items-center gap-1 text-success fw-semibold">
                                    <i class="mdi mdi-trending-up small"></i> Laba Bersih {{ $periodeLabel }}
                                </span>
                            </td>
                            <td class="text-end fw-semibold text-success bs-amount">{{ number_format($labaPeriodeIni, 0, ',', '.') }}</td>
                        </tr>

                        {{-- TOTAL EKUITAS --}}
                        <tr class="bs-row-total-cat">
                            <td class="fw-bold text-dark">
                                <i class="mdi mdi-sigma me-1 text-info"></i> TOTAL EKUITAS (EQUITY)
                            </td>
                            <td class="text-end fw-bold text-info bs-amount">{{ number_format($totalekuitas ?? 0, 0, ',', '.') }}</td>
                        </tr>

                        {{-- Spacer to balance height with Aktiva if in side-by-side mode on screen --}}
                        @if(!$isPrint)
                        <tr class="bs-spacer-row d-none d-lg-table-row">
                            <td colspan="2" style="height: 48px;">&nbsp;</td>
                        </tr>
                        @endif

                        {{-- GRAND TOTAL KEWAJIBAN & EKUITAS --}}
                        <tr class="bs-row-grand-total">
                            <td class="fw-bolder fs-6">
                                <i class="mdi mdi-check-circle-outline me-1"></i> JUMLAH TOTAL PASIVA
                            </td>
                            <td class="text-end fw-bolder fs-6 bs-amount">{{ number_format($ekujiban ?? 0, 0, ',', '.') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@if(!$isPrint)
{{-- Balance Reconciliation Card (Hanya Tampil di Mode Layar) --}}
<div class="card border {{ $isBalanced ? 'border-success' : 'border-warning' }} shadow-none mt-4 no-print" style="background-color: {{ $isBalanced ? '#f0fdf4' : '#fffbeb' }};">
    <div class="card-body p-3">
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="avatar avatar-md rounded-circle {{ $isBalanced ? 'bg-success text-white' : 'bg-warning text-dark' }} d-flex align-items-center justify-content-center flex-shrink-0">
                    <i class="mdi {{ $isBalanced ? 'mdi-check-decagram' : 'mdi-scale-unbalanced' }} fs-3"></i>
                </div>
                <div>
                    <h6 class="fw-bold mb-1 {{ $isBalanced ? 'text-success' : 'text-dark' }}">
                        {{ $isBalanced ? 'Status Neraca: Seimbang (Balanced)' : 'Status Neraca: Memerlukan Rekonsiliasi Saldo Awal' }}
                    </h6>
                    <p class="mb-0 text-muted small">
                        Persamaan Akuntansi: <strong>Aktiva (Rp {{ number_format($totalAktiva, 0, ',', '.') }})</strong> 
                        {{ $isBalanced ? '=' : '≠' }} 
                        <strong>Kewajiban &amp; Ekuitas (Rp {{ number_format($ekujiban, 0, ',', '.') }})</strong>
                    </p>
                </div>
            </div>
            <div class="text-sm-end ps-sm-3 border-sm-start">
                <span class="text-muted small d-block">Selisih Buku</span>
                <span class="fw-bold fs-5 {{ $isBalanced ? 'text-success' : 'text-danger' }} bs-amount">
                    Rp {{ number_format($selisih, 0, ',', '.') }}
                </span>
            </div>
        </div>
    </div>
</div>
@endif
