<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kartu Piutang - {{ $client->company }} ({{ Carbon\Carbon::parse($startDate)->format('d/m/Y') }} - {{ Carbon\Carbon::parse($endDate)->format('d/m/Y') }})</title>
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
            border-bottom: 2px solid #20407d;
            padding-bottom: 12px;
            margin-bottom: 15px;
        }
        .company-title {
            font-size: 18px;
            font-weight: 800;
            color: #20407d;
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
            background-color: #20407d;
            color: #fff;
            padding: 7px 8px;
            text-align: left;
            font-weight: 600;
            border: 1px solid #1a3568;
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
                background-color: #20407d !important;
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
    {{-- Action Bar (Print / Close) --}}
    <div class="no-print d-flex justify-content-between align-items-center mb-4 p-3 bg-light rounded border">
        <div>
            <h5 class="mb-0 fw-bold">Pratinjau Cetak Kartu Piutang Customer</h5>
            <small class="text-muted">Pastikan ukuran kertas diatur ke <strong>A4 Portrait</strong>.</small>
        </div>
        <div class="d-flex gap-2">
            <button onclick="window.print()" class="btn btn-primary btn-sm px-3">
                <i class="mdi mdi-printer me-1"></i> Cetak Dokumen
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
                <div class="report-title">KARTU PIUTANG CUSTOMER</div>
                <div class="fw-semibold text-muted">STATEMENT OF ACCOUNT (SOA)</div>
                <div class="small mt-1 text-secondary">
                    Periode: <strong>{{ Carbon\Carbon::parse($startDate)->format('d/m/Y') }}</strong> s/d <strong>{{ Carbon\Carbon::parse($endDate)->format('d/m/Y') }}</strong>
                </div>
            </div>
        </div>
    </div>

    {{-- Client Details & Metrics --}}
    <div class="row g-3 mb-3">
        <div class="col-7">
            <table class="table table-sm table-borderless mb-0" style="font-size: 11px;">
                <tr>
                    <td class="text-muted" style="width: 110px;">Nama Customer</td>
                    <td class="fw-bold">: {{ $client->company }}</td>
                </tr>
                @if($client->ru)
                    <tr>
                        <td class="text-muted">Unit / RU</td>
                        <td>: {{ $client->ru }}</td>
                    </tr>
                @endif
                @if($client->address)
                    <tr>
                        <td class="text-muted">Alamat</td>
                        <td>: {{ $client->address }}</td>
                    </tr>
                @endif
                @if($client->phone || $client->mobile)
                    <tr>
                        <td class="text-muted">Kontak / Telp</td>
                        <td>: {{ $client->phone ?: $client->mobile }}</td>
                    </tr>
                @endif
                @if($client->npwp)
                    <tr>
                        <td class="text-muted">NPWP</td>
                        <td>: {{ $client->npwp }}</td>
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
                        <small class="text-muted d-block text-uppercase" style="font-size: 9px;">Total Faktur (+)</small>
                        <span class="fw-bold text-primary">Rp {{ number_format($totalDebit, 0, ',', '.') }}</span>
                    </div>
                </div>
                <div class="col-6">
                    <div class="metric-box">
                        <small class="text-muted d-block text-uppercase" style="font-size: 9px;">Total Pelunasan (-)</small>
                        <span class="fw-bold text-success">Rp {{ number_format($totalCredit, 0, ',', '.') }}</span>
                    </div>
                </div>
                <div class="col-6">
                    <div class="metric-box" style="background: {{ $closingBalance > 0 ? '#fef2f2' : '#f0fdf4' }}; border-color: {{ $closingBalance > 0 ? '#fecaca' : '#bbf7d0' }};">
                        <small class="d-block text-uppercase {{ $closingBalance > 0 ? 'text-danger' : 'text-success' }}" style="font-size: 9px;">Saldo Akhir Piutang</small>
                        <span class="fw-bolder {{ $closingBalance > 0 ? 'text-danger' : 'text-success' }}">Rp {{ number_format($closingBalance, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Ledger Table --}}
    <table class="table-custom mb-4">
        <thead>
            <tr>
                <th style="width: 80px;" class="text-center">Tanggal</th>
                <th style="width: 140px;">No. Referensi / Inv</th>
                <th style="width: 100px;">No. PO Klien</th>
                <th>Deskripsi Transaksi</th>
                <th style="width: 110px;" class="text-end">Tagihan / Debit (+)</th>
                <th style="width: 110px;" class="text-end">Pelunasan / Kredit (-)</th>
                <th style="width: 120px;" class="text-end">Saldo Piutang</th>
            </tr>
        </thead>
        <tbody>
            {{-- Saldo Awal --}}
            <tr class="opening-row">
                <td class="text-center">{{ Carbon\Carbon::parse($startDate)->format('d/m/Y') }}</td>
                <td colspan="3"><em>SALDO AWAL PIUTANG (Sebelum {{ Carbon\Carbon::parse($startDate)->format('d/m/Y') }})</em></td>
                <td class="text-end">-</td>
                <td class="text-end">-</td>
                <td class="text-end fw-bold">Rp {{ number_format($openingBalance, 0, ',', '.') }}</td>
            </tr>

            @forelse ($ledger as $item)
                <tr>
                    <td class="text-center">{{ Carbon\Carbon::parse($item['date'])->format('d/m/Y') }}</td>
                    <td class="fw-semibold">
                        {{ $item['ref_no'] }}
                        @if($item['type'] === 'DEBIT')
                            <span class="badge bg-light text-dark border ms-1" style="font-size: 9px;">INV</span>
                        @else
                            <span class="badge bg-light text-success border ms-1" style="font-size: 9px;">PAY</span>
                        @endif
                    </td>
                    <td>{{ $item['po_number'] ?: '-' }}</td>
                    <td>
                        {{ $item['description'] }}
                        @if($item['bank_name'])
                            <br><small class="text-muted">Via: {{ $item['bank_name'] }}</small>
                        @endif
                    </td>
                    <td class="text-end text-primary">
                        {{ $item['debit'] > 0 ? 'Rp ' . number_format($item['debit'], 0, ',', '.') : '-' }}
                    </td>
                    <td class="text-end text-success">
                        {{ $item['credit'] > 0 ? 'Rp ' . number_format($item['credit'], 0, ',', '.') : '-' }}
                    </td>
                    <td class="text-end fw-bold {{ $item['running_balance'] > 0 ? 'text-danger' : 'text-dark' }}">
                        Rp {{ number_format($item['running_balance'], 0, ',', '.') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center py-3 text-muted">
                        <em>Tidak ada mutasi tagihan atau pembayaran pada rentang tanggal yang dipilih.</em>
                    </td>
                </tr>
            @endforelse

            {{-- Summary Footer Row --}}
            <tr class="footer-row">
                <td colspan="4" class="text-end text-uppercase">Total Mutasi &amp; Saldo Akhir:</td>
                <td class="text-end text-primary">Rp {{ number_format($totalDebit, 0, ',', '.') }}</td>
                <td class="text-end text-success">Rp {{ number_format($totalCredit, 0, ',', '.') }}</td>
                <td class="text-end {{ $closingBalance > 0 ? 'text-danger' : 'text-success' }} fs-6">
                    Rp {{ number_format($closingBalance, 0, ',', '.') }}
                </td>
            </tr>
        </tbody>
    </table>

    {{-- Terbilang & Catatan --}}
    <div class="row g-3 mb-4">
        <div class="col-8">
            <div class="p-2 border rounded bg-light" style="font-size: 11px;">
                <strong>Catatan / Keterangan:</strong>
                <p class="mb-0 text-muted mt-1">
                    1. Dokumen ini adalah rekapitulasi sah pencatatan mutasi piutang antara PT. Refrigerasi Teknik Indonesia dan {{ $client->company }}.<br>
                    2. Pembayaran resmi hanya diakui jika ditransfer ke rekening bank atas nama PT. Refrigerasi Teknik Indonesia.<br>
                    3. Apabila terdapat selisih pencatatan, mohon konfirmasi ke Bagian Keuangan dalam 7 (tujuh) hari kerja.
                </p>
            </div>
        </div>
        <div class="col-4">
            <div class="p-2 border rounded text-end" style="font-size: 11px;">
                <span class="text-muted">Status Piutang:</span><br>
                @if($closingBalance <= 0)
                    <span class="badge bg-success fs-6 mt-1 px-3 py-1">LUNAS / NIHIL</span>
                @else
                    <span class="badge bg-danger fs-6 mt-1 px-3 py-1">BELUM LUNAS</span>
                @endif
            </div>
        </div>
    </div>

    {{-- Signature Section --}}
    <div class="signature-box">
        <div class="row text-center">
            <div class="col-4">
                <p class="mb-1 text-muted">Disiapkan Oleh,</p>
                <div style="height: 60px;"></div>
                <p class="fw-bold mb-0 text-decoration-underline">( AR &amp; Billing Staff )</p>
                <small class="text-muted">Account Receivable</small>
            </div>
            <div class="col-4">
                <p class="mb-1 text-muted">Diperiksa &amp; Disetujui,</p>
                <div style="height: 60px;"></div>
                <p class="fw-bold mb-0 text-decoration-underline">( Finance Manager )</p>
                <small class="text-muted">Finance &amp; Accounting</small>
            </div>
            <div class="col-4">
                <p class="mb-1 text-muted">Diterima / Dikonfirmasi Oleh,</p>
                <div style="height: 60px;"></div>
                <p class="fw-bold mb-0 text-decoration-underline">{{ $client->company }}</p>
                <small class="text-muted">Customer Representative</small>
            </div>
        </div>
    </div>
</body>
</html>
