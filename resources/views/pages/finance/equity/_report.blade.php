{{-- Konten laporan Equity Statement, dipakai bareng oleh print.blade.php & detail.blade.php --}}

@php
    $periodYear = $year ?? \Carbon\Carbon::parse($startDate)->year;
    $currentProfit = @$month ? ($labaBulanIni ?? 0) : ($labaTahunIni ?? 0);
    $priveVal = $prive ?? 0;
    
    if (@$month) {
        $ekuitas = 250000000 + ($labaTahunTahun ?? 0) - $priveVal - ($labaBulanIni ?? 0);
        $totalekuitas = $ekuitas + ($labaBulanIni ?? 0);
        $sebelumnya = ($labaTahunTahun ?? 0) - ($labaBulanIni ?? 0);
        $periodeLabel = 'Bulan ' . \Carbon\Carbon::create($periodYear, $month, 1)->translatedFormat('F Y');
    } else {
        $ekuitas = 250000000 + ($labaTahunTahun ?? 0) - $priveVal - ($labaTahunIni ?? 0);
        $totalekuitas = $ekuitas + ($labaTahunIni ?? 0);
        $sebelumnya = ($labaTahunTahun ?? 0) - ($labaTahunIni ?? 0);
        $periodeLabel = 'Tahun ' . $periodYear;
    }
    $netMovement = $currentProfit - $priveVal;
    $growthPct = $ekuitas > 0 ? (($totalekuitas - $ekuitas) / $ekuitas) * 100 : 0;
    $isPrint = $isPrintMode ?? false;
@endphp

<style>
    .eq-report-container {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        color: #1e293b;
    }
    .eq-header-box {
        border-bottom: 2px solid #696cff;
        padding-bottom: 14px;
        margin-bottom: 20px;
    }
    .eq-company-name {
        font-weight: 800;
        font-size: 1.15rem;
        letter-spacing: 0.5px;
        color: #0f172a;
    }
    .eq-doc-title {
        font-weight: 700;
        font-size: 1rem;
        color: #696cff;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .eq-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 12.5px;
    }
    .eq-table th {
        background-color: #f8fafc;
        color: #475569;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 11px;
        letter-spacing: 0.5px;
        padding: 8px 12px;
        border-top: 1px solid #cbd5e1;
        border-bottom: 2px solid #cbd5e1;
    }
    .eq-table td {
        padding: 7px 12px;
        vertical-align: middle;
    }
    .eq-sec-header td {
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
    .eq-row-item td {
        color: #334155;
        border-bottom: 1px solid #f8fafc;
    }
    .eq-row-item:hover td {
        background-color: #f8faff;
    }
    .eq-row-subtotal td {
        background-color: #f8fafc;
        border-top: 1px dashed #cbd5e1;
        border-bottom: 1px solid #e2e8f0;
        font-weight: 700;
        color: #0f172a;
    }
    .eq-row-grand-total td {
        background-color: #f8fafc !important;
        color: #0f172a !important;
        font-weight: 800 !important;
        border-top: 2px solid #0f172a !important;
        border-bottom: 3px double #0f172a !important;
        font-size: 13px;
        padding-top: 10px;
        padding-bottom: 10px;
    }
    .signature-section {
        margin-top: 30px;
    }
    .signature-box {
        text-align: center;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        padding: 10px 12px;
        background-color: #fafbfd;
    }
    .signature-space {
        height: 55px;
    }
</style>

<div class="eq-report-container">
    {{-- Letterhead / Corporate Header --}}
    <div class="eq-header-box d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
        <div class="d-flex align-items-center gap-3">
            <div class="avatar avatar-md bg-label-primary rounded-3 d-flex align-items-center justify-content-center fw-bold fs-4 text-primary">
                RT
            </div>
            <div>
                <div class="eq-company-name">PT. REFTECH JAYA OPTIMA</div>
                <div class="eq-doc-title">Laporan Perubahan Modal / Ekuitas</div>
                <div class="text-muted small">Periode: <strong>{{ $startString }}</strong> s/d <strong>{{ $endString }}</strong></div>
            </div>
        </div>
        <div class="text-md-end text-muted small">
            <div><strong>No. Dokumen:</strong> EQ-{{ $periodYear }}{{ @$month ? str_pad($month, 2, '0', STR_PAD_LEFT) : 'FY' }}/RJO</div>
            <div><strong>Standar:</strong> PSAK / SAK ETAP</div>
            <div><strong>Mata Uang:</strong> IDR (Rupiah)</div>
        </div>
    </div>

    {{-- Equity Statement Table --}}
    <div class="table-responsive mb-4">
        <table class="eq-table">
            <thead>
                <tr>
                    <th style="width: 55%;">Komponen Modal &amp; Ekuitas Pemilik</th>
                    <th style="width: 22%;" class="text-end">Rincian Pos (IDR)</th>
                    <th style="width: 23%;" class="text-end">Jumlah Kumulatif (IDR)</th>
                </tr>
            </thead>
            <tbody>
                {{-- BAGIAN I: MODAL AWAL --}}
                <tr class="eq-sec-header">
                    <td colspan="3">
                        <i class="mdi mdi-calendar-start me-1 text-primary"></i> I. Saldo Modal &amp; Ekuitas Awal Periode
                    </td>
                </tr>
                <tr class="eq-row-item">
                    <td class="ps-4">
                        <span class="fw-semibold text-dark">Modal Saham Disetor (Paid-in Capital)</span>
                        <small class="d-block text-muted">Modal awal penyertaan pendirian perseroan</small>
                    </td>
                    <td class="text-end text-muted">250.000.000</td>
                    <td></td>
                </tr>
                <tr class="eq-row-item">
                    <td class="ps-4">
                        <span class="fw-semibold text-dark">Saldo Laba Ditahan Tahun Lalu (Retained Earnings)</span>
                        <small class="d-block text-muted">Akumulasi laba ditahan tahun {{ $periodYear - 1 }}</small>
                    </td>
                    <td class="text-end text-muted">{{ number_format($labaTahunLalu, 0, ',', '.') }}</td>
                    <td></td>
                </tr>
                <tr class="eq-row-item">
                    <td class="ps-4">
                        <span class="fw-semibold text-dark">Akumulasi Laba Usaha Sebelumnya</span>
                        <small class="d-block text-muted">Total laba bersih periode-periode terdahulu</small>
                    </td>
                    <td class="text-end text-muted">{{ number_format($sebelumnya, 0, ',', '.') }}</td>
                    <td></td>
                </tr>
                <tr class="eq-row-subtotal">
                    <td class="ps-3 fw-bold text-dark">
                        Total Saldo Ekuitas Awal (Beginning Balance)
                    </td>
                    <td></td>
                    <td class="text-end text-primary fw-bold">Rp {{ number_format($ekuitas, 0, ',', '.') }}</td>
                </tr>

                {{-- BAGIAN II: PERUBAHAN PERIODE BERJALAN --}}
                <tr class="eq-sec-header">
                    <td colspan="3">
                        <i class="mdi mdi-swap-vertical me-1 text-primary"></i> II. Pergerakan Modal Periode Berjalan ({{ $periodeLabel }})
                    </td>
                </tr>
                <tr class="eq-row-item">
                    <td class="ps-4">
                        <span class="fw-semibold text-success">(+) Laba Bersih Usaha Berjalan (Net Income)</span>
                        <small class="d-block text-muted">Penambahan modal dari hasil keuntungan operasional bersih</small>
                    </td>
                    <td class="text-end text-success fw-semibold">+ {{ number_format($currentProfit, 0, ',', '.') }}</td>
                    <td></td>
                </tr>
                <tr class="eq-row-item">
                    <td class="ps-4">
                        <span class="fw-semibold text-danger">(-) Pengambilan Prive Pemilik (Owner's Drawings)</span>
                        <small class="d-block text-muted">Penarikan dana/modal untuk kepentingan pribadi pemilik</small>
                    </td>
                    <td class="text-end text-danger fw-semibold">- {{ number_format($priveVal, 0, ',', '.') }}</td>
                    <td></td>
                </tr>
                <tr class="eq-row-subtotal">
                    <td class="ps-3 fw-bold {{ $netMovement >= 0 ? 'text-success' : 'text-danger' }}">
                        Net Perubahan Ekuitas Periode Ini
                    </td>
                    <td></td>
                    <td class="text-end fw-bold {{ $netMovement >= 0 ? 'text-success' : 'text-danger' }}">
                        {{ $netMovement < 0 ? '- ' : '+ ' }}Rp {{ number_format(abs($netMovement), 0, ',', '.') }}
                    </td>
                </tr>

                {{-- BAGIAN III: TOTAL EKUITAS AKHIR --}}
                <tr class="eq-sec-header">
                    <td colspan="3">
                        <i class="mdi mdi-shield-check-outline me-1 text-primary"></i> III. Total Ekuitas Modal Akhir
                    </td>
                </tr>
                <tr class="eq-row-grand-total">
                    <td class="ps-3">
                        <span class="text-uppercase fw-bold text-dark">JUMLAH TOTAL EKUITAS AKHIR PERIODE (ENDING EQUITY)</span>
                        <small class="d-block text-muted fw-normal">Modal Awal + Laba Bersih - Prive (Posisi per {{ $endString }})</small>
                    </td>
                    <td class="text-end">
                        <span class="badge {{ $growthPct >= 0 ? 'bg-label-success' : 'bg-label-danger' }} rounded-pill small fw-bold">
                            {{ $growthPct >= 0 ? '+' : '' }}{{ number_format($growthPct, 1) }}% Growth
                        </span>
                    </td>
                    <td class="text-end text-dark fw-bolder fs-6">
                        Rp {{ number_format($totalekuitas, 0, ',', '.') }}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- Official Signatures Block --}}
    <div class="signature-section">
        <div class="row g-3">
            <div class="col-4">
                <div class="signature-box">
                    <div class="small fw-semibold text-muted text-uppercase mb-1">Dibuat Oleh:</div>
                    <div class="signature-space"></div>
                    <div class="fw-bold text-dark border-top pt-1 text-truncate">Staff Accounting</div>
                    <small class="text-muted">Finance &amp; Tax</small>
                </div>
            </div>
            <div class="col-4">
                <div class="signature-box">
                    <div class="small fw-semibold text-muted text-uppercase mb-1">Diperiksa Oleh:</div>
                    <div class="signature-space"></div>
                    <div class="fw-bold text-dark border-top pt-1 text-truncate">Finance Manager</div>
                    <small class="text-muted">Head of Finance</small>
                </div>
            </div>
            <div class="col-4">
                <div class="signature-box">
                    <div class="small fw-semibold text-muted text-uppercase mb-1">Disetujui Oleh:</div>
                    <div class="signature-space"></div>
                    <div class="fw-bold text-dark border-top pt-1 text-truncate">Direktur Utama</div>
                    <small class="text-muted">PT. Reftech Jaya Optima</small>
                </div>
            </div>
        </div>
    </div>
</div>
