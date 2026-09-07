<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekening Koran - {{ $bank->bank }} ({{ Carbon\Carbon::parse($startDate)->format('d/m/Y') }} - {{ Carbon\Carbon::parse($endDate)->format('d/m/Y') }})</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@mdi/font@6.5.95/css/materialdesignicons.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 12px;
            color: #333;
            background: #fff;
        }
        .statement-header {
            border-bottom: 2px solid #1e3a8a;
            padding-bottom: 12px;
            margin-bottom: 15px;
        }
        .company-title {
            font-size: 18px;
            font-weight: 800;
            color: #1e3a8a;
            letter-spacing: 0.5px;
        }
        .report-title {
            font-size: 16px;
            font-weight: 700;
            color: #111;
            text-transform: uppercase;
        }
        .table-custom {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
        }
        .table-custom th {
            background-color: #1e3a8a;
            color: #fff;
            padding: 7px 8px;
            text-align: left;
            font-weight: 600;
            border: 1px solid #172554;
        }
        .table-custom td {
            padding: 6px 8px;
            border: 1px solid #e2e8f0;
            vertical-align: middle;
        }
        .table-custom tr.opening-row {
            background-color: #f8fafc;
            font-weight: 600;
        }
        .table-custom tr:nth-child(even):not(.opening-row):not(.footer-row) {
            background-color: #fbfcfe;
        }
        .table-custom tr.footer-row {
            background-color: #f1f5f9;
            font-weight: bold;
            border-top: 2px solid #cbd5e1;
        }
        .metric-box {
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 8px 12px;
            background: #f8fafc;
        }
        .signature-box {
            margin-top: 40px;
            page-break-inside: avoid;
        }
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                padding: 0;
                font-size: 11px;
            }
            .table-custom th {
                background-color: #1e3a8a !important;
                color: #fff !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .metric-box, .table-custom tr.opening-row, .table-custom tr.footer-row {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body class="p-4">
    {{-- Action Bar --}}
    <div class="no-print d-flex justify-content-between align-items-center mb-4 p-3 bg-light rounded border">
        <div>
            <h5 class="mb-0 fw-bold">Pratinjau Cetak Rekening Koran / Buku Bank</h5>
            <small class="text-muted">Pastikan ukuran kertas diatur ke <strong>A4 Portrait</strong>.</small>
        </div>
        <div class="d-flex gap-2">
            <button onclick="window.print()" class="btn btn-primary btn-sm px-3">
                <i class="mdi mdi-printer me-1"></i> Cetak Rekening Koran
            </button>
            <button onclick="window.close()" class="btn btn-secondary btn-sm px-3">
                Tutup
            </button>
        </div>
    </div>

    {{-- Company Header --}}
    <div class="statement-header">
        <div class="row align-items-center">
            <div class="col-7">
                <div class="company-title">PT. REFRIGERASI TEKNIK INDONESIA</div>
                <div class="text-muted small">
                    Ruko Grand Galaxy City Blok RSN 7 No. 15, Bekasi Selatan, Jawa Barat<br>
                    Telp: (021) 8273 4567 | Email: finance@reftech.co.id
                </div>
            </div>
            <div class="col-5 text-end">
                <div class="report-title">BUKU BANK / REKENING KORAN</div>
                <div class="fw-semibold text-muted">BANK STATEMENT REPORT</div>
                <div class="small mt-1 text-secondary">
                    Periode: <strong>{{ Carbon\Carbon::parse($startDate)->format('d/m/Y') }}</strong> s/d <strong>{{ Carbon\Carbon::parse($endDate)->format('d/m/Y') }}</strong>
                </div>
            </div>
        </div>
    </div>

    {{-- Bank Details & Metrics --}}
    <div class="row g-3 mb-3">
        <div class="col-7">
            <table class="table table-sm table-borderless mb-0" style="font-size: 11px;">
                <tr>
                    <td class="text-muted" style="width: 120px;">Nama Bank</td>
                    <td class="fw-bold">: {{ $bank->bank }}</td>
                </tr>
                <tr>
                    <td class="text-muted">Nomor Rekening</td>
                    <td class="fw-bold">: {{ $bank->no_rek }}</td>
                </tr>
                <tr>
                    <td class="text-muted">Atas Nama</td>
                    <td>: {{ $bank->atas_nama ?: 'PT. Refrigerasi Teknik Indonesia' }}</td>
                </tr>
                @if($bank->branch)
                    <tr>
                        <td class="text-muted">Kantor Cabang</td>
                        <td>: KCP {{ $bank->branch }}</td>
                    </tr>
                @endif
            </table>
        </div>
        <div class="col-5">
            <div class="row g-2">
                <div class="col-6">
                    <div class="metric-box">
                        <small class="text-muted d-block text-uppercase" style="font-size: 9px;">Saldo Awal</small>
                        <span class="fw-bold">Rp {{ number_format($openingBalance, 0, ',', '.') }}</span>
                    </div>
                </div>
                <div class="col-6">
                    <div class="metric-box">
                        <small class="text-muted d-block text-uppercase" style="font-size: 9px;">Kredit (+)</small>
                        <span class="fw-bold text-success">Rp {{ number_format($totalIn, 0, ',', '.') }}</span>
                    </div>
                </div>
                <div class="col-6">
                    <div class="metric-box">
                        <small class="text-muted d-block text-uppercase" style="font-size: 9px;">Debet (-)</small>
                        <span class="fw-bold text-danger">Rp {{ number_format($totalOut, 0, ',', '.') }}</span>
                    </div>
                </div>
                <div class="col-6">
                    <div class="metric-box" style="background: #eff6ff; border-color: #bfdbfe;">
                        <small class="d-block text-uppercase text-primary" style="font-size: 9px;">Saldo Akhir Bank</small>
                        <span class="fw-bolder text-primary">Rp {{ number_format($closingBalance, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Ledger Table --}}
    <table class="table-custom mb-4">
        <thead>
            <tr>
                <th style="width: 90px;" class="text-center text-nowrap">Tanggal</th>
                <th style="width: 120px;">Modul</th>
                <th style="width: 130px;">No. Referensi</th>
                <th>Deskripsi / Keterangan</th>
                <th style="width: 110px;" class="text-end">Kredit (+)</th>
                <th style="width: 110px;" class="text-end">Debet (-)</th>
                <th style="width: 120px;" class="text-end">Saldo Berjalan</th>
            </tr>
        </thead>
        <tbody>
            {{-- Saldo Awal --}}
            <tr class="opening-row">
                <td class="text-center text-nowrap">{{ Carbon\Carbon::parse($startDate)->format('d/m/Y') }}</td>
                <td class="text-center"><span class="badge bg-light text-dark border" style="font-size: 9px;">AWAL</span></td>
                <td class="text-muted">-</td>
                <td><em>SALDO AWAL KAS &amp; BANK (Sebelum {{ Carbon\Carbon::parse($startDate)->format('d/m/Y') }})</em></td>
                <td class="text-end">-</td>
                <td class="text-end">-</td>
                <td class="text-end fw-bold">Rp {{ number_format($openingBalance, 0, ',', '.') }}</td>
            </tr>

            @forelse ($ledger as $item)
                <tr>
                    <td class="text-center text-nowrap">{{ Carbon\Carbon::parse($item->date)->format('d/m/Y') }}</td>
                    <td class="text-center">
                        <span class="badge bg-light text-dark border" style="font-size: 9px;">
                            {{ $item->module }}
                        </span>
                    </td>
                    <td class="fw-semibold">{{ $item->ref_no }}</td>
                    <td>{{ $item->description }}</td>
                    <td class="text-end text-success text-nowrap">
                        @if ($item->in > 0)
                            <span>Rp {{ number_format($item->in, 0, ',', '.') }}</span>
                            <span class="badge bg-primary text-white ms-1" style="font-size: 8px; padding: 2px 4px;">KR</span>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td class="text-end text-danger text-nowrap">
                        @if ($item->out > 0)
                            <span>Rp {{ number_format($item->out, 0, ',', '.') }}</span>
                            <span class="badge bg-danger text-white ms-1" style="font-size: 8px; padding: 2px 4px;">DB</span>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td class="text-end fw-bold text-dark text-nowrap">
                        Rp {{ number_format($item->running_balance, 0, ',', '.') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center py-3 text-muted">
                        <em>Tidak ada mutasi transaksi pada rentang tanggal yang dipilih.</em>
                    </td>
                </tr>
            @endforelse

            {{-- Summary Footer Row --}}
            <tr class="footer-row align-middle" style="border-top: 2px solid #333;">
                <td colspan="4" class="text-end text-uppercase fw-bold text-nowrap" style="font-size: 11.5px; padding: 8px 6px;">Total Mutasi &amp; Saldo Akhir:</td>
                <td class="text-end text-success fw-bold text-nowrap" style="font-size: 12.5px; font-weight: 800; padding: 8px 6px;">
                    <span>Rp {{ number_format($totalIn, 0, ',', '.') }}</span>
                    <span class="badge bg-primary text-white ms-1" style="font-size: 9px; padding: 2px 4px;">KR</span>
                </td>
                <td class="text-end text-danger fw-bold text-nowrap" style="font-size: 12.5px; font-weight: 800; padding: 8px 6px;">
                    <span>Rp {{ number_format($totalOut, 0, ',', '.') }}</span>
                    <span class="badge bg-danger text-white ms-1" style="font-size: 9px; padding: 2px 4px;">DB</span>
                </td>
                <td class="text-end text-primary fw-bold text-nowrap" style="font-size: 13px; font-weight: 800; padding: 8px 6px;">
                    Rp {{ number_format($closingBalance, 0, ',', '.') }}
                </td>
            </tr>
        </tbody>
    </table>

    {{-- Signature Section --}}
    <div class="signature-box">
        <div class="row text-center">
            <div class="col-6">
                <p class="mb-1 text-muted">Disiapkan Oleh,</p>
                <div style="height: 60px;"></div>
                <p class="fw-bold mb-0 text-decoration-underline">( Finance &amp; Treasury Staff )</p>
                <small class="text-muted">Bagian Keuangan &amp; Kasir</small>
            </div>
            <div class="col-6">
                <p class="mb-1 text-muted">Diperiksa &amp; Disetujui Oleh,</p>
                <div style="height: 60px;"></div>
                <p class="fw-bold mb-0 text-decoration-underline">( Finance Manager )</p>
                <small class="text-muted">Finance &amp; Accounting Head</small>
            </div>
        </div>
    </div>
</body>
</html>
