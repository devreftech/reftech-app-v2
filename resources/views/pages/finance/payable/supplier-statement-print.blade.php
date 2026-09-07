<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kartu Hutang - {{ $selectedSupplier->supplier }} ({{ Carbon\Carbon::parse($startDate)->format('d-m-Y') }} s/d {{ Carbon\Carbon::parse($endDate)->format('d-m-Y') }})</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
            margin: 20px;
            line-height: 1.4;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid #222;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }
        .company-title {
            font-size: 18px;
            font-weight: bold;
            color: #1a365d;
        }
        .doc-title {
            font-size: 16px;
            font-weight: bold;
            text-align: right;
            text-transform: uppercase;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 20px;
            background: #f8f9fa;
            padding: 12px;
            border-radius: 4px;
        }
        .info-row {
            display: flex;
            margin-bottom: 4px;
        }
        .info-label {
            width: 130px;
            font-weight: bold;
            color: #555;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th {
            background-color: #f1f3f5;
            border: 1px solid #dee2e6;
            padding: 8px;
            text-align: left;
            font-size: 11px;
            text-transform: uppercase;
        }
        td {
            border: 1px solid #dee2e6;
            padding: 6px 8px;
        }
        .text-end { text-align: right; }
        .text-center { text-align: center; }
        .fw-bold { font-weight: bold; }
        .summary-box {
            display: flex;
            justify-content: flex-end;
            margin-top: 10px;
        }
        .summary-table {
            width: 350px;
        }
        .summary-table td {
            padding: 5px 8px;
        }
        .signatures {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            margin-top: 50px;
            text-align: center;
        }
        .sign-space {
            height: 60px;
        }
        @media print {
            body { margin: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 15px;">
        <button onclick="window.print()" style="padding: 8px 16px; background: #0066cc; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: bold;">
            🖨️ Cetak / Simpan PDF
        </button>
    </div>

    <div class="header">
        <div>
            <div class="company-title">PT REFTECH REFRIGERATION INDONESIA</div>
            <div style="font-size: 11px; color: #666;">Jl. Sukamenak No. 123, Bandung, Jawa Barat | Telp: (022) 1234567</div>
        </div>
        <div>
            <div class="doc-title">KARTU HUTANG SUPPLIER</div>
            <div style="font-size: 11px; text-align: right; color: #555;">(Statement of Account)</div>
        </div>
    </div>

    <div class="info-grid">
        <div>
            <div class="info-row">
                <div class="info-label">Nama Supplier:</div>
                <div class="fw-bold">{{ $selectedSupplier->supplier }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">NPWP:</div>
                <div>{{ $selectedSupplier->npwp ?? '-' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Alamat / Telp:</div>
                <div>{{ $selectedSupplier->address ?? '-' }} / {{ $selectedSupplier->phone ?? '-' }}</div>
            </div>
        </div>
        <div>
            <div class="info-row">
                <div class="info-label">Periode Laporan:</div>
                <div class="fw-bold">{{ Carbon\Carbon::parse($startDate)->format('d/m/Y') }} s/d {{ Carbon\Carbon::parse($endDate)->format('d/m/Y') }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Tanggal Cetak:</div>
                <div>{{ Carbon\Carbon::now()->format('d/m/Y H:i') }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Mata Uang:</div>
                <div>IDR (Rupiah)</div>
            </div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 80px;">Tanggal</th>
                <th style="width: 70px;" class="text-center">Tipe</th>
                <th style="width: 130px;">No. Referensi</th>
                <th>Keterangan / Deskripsi</th>
                <th style="width: 120px;" class="text-end">Pembelian (Dr)</th>
                <th style="width: 120px;" class="text-end">Pembayaran (Cr)</th>
                <th style="width: 130px;" class="text-end">Saldo Hutang</th>
            </tr>
        </thead>
        <tbody>
            <tr style="background: #fafafa; font-weight: bold;">
                <td>{{ Carbon\Carbon::parse($startDate)->format('d/m/Y') }}</td>
                <td class="text-center">AWAL</td>
                <td>-</td>
                <td>Saldo Awal Hutang per {{ Carbon\Carbon::parse($startDate)->format('d/m/Y') }}</td>
                <td class="text-end">-</td>
                <td class="text-end">-</td>
                <td class="text-end">Rp {{ number_format($openingBalance, 0, ',', '.') }}</td>
            </tr>
            @forelse ($transactions as $t)
                <tr>
                    <td>{{ Carbon\Carbon::parse($t->date)->format('d/m/Y') }}</td>
                    <td class="text-center" style="font-size: 10px;">{{ $t->type }}</td>
                    <td>{{ $t->ref }}</td>
                    <td>{{ $t->description }}</td>
                    <td class="text-end">{{ $t->debit > 0 ? 'Rp ' . number_format($t->debit, 0, ',', '.') : '-' }}</td>
                    <td class="text-end">{{ $t->credit > 0 ? 'Rp ' . number_format($t->credit, 0, ',', '.') : '-' }}</td>
                    <td class="text-end fw-bold">Rp {{ number_format($t->balance, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center" style="padding: 20px; color: #888;">Tidak ada mutasi hutang pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr style="font-weight: bold; background: #f1f3f5;">
                <td colspan="4" class="text-end">TOTAL MUTASI:</td>
                <td class="text-end">Rp {{ number_format($totalDebit, 0, ',', '.') }}</td>
                <td class="text-end">Rp {{ number_format($totalCredit, 0, ',', '.') }}</td>
                <td class="text-end" style="color: #b91c1c;">Rp {{ number_format($endingBalance, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="summary-box">
        <table class="summary-table">
            <tr>
                <td class="fw-bold">Saldo Awal</td>
                <td class="text-end">Rp {{ number_format($openingBalance, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="fw-bold">Total Penambahan (Pembelian)</td>
                <td class="text-end">+ Rp {{ number_format($totalDebit, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="fw-bold">Total Pengurangan (Pembayaran)</td>
                <td class="text-end">- Rp {{ number_format($totalCredit, 0, ',', '.') }}</td>
            </tr>
            <tr style="background: #fee2e2; font-weight: bold; font-size: 13px;">
                <td>SISA SALDO HUTANG AKHIR</td>
                <td class="text-end" style="color: #991b1b;">Rp {{ number_format($endingBalance, 0, ',', '.') }}</td>
            </tr>
        </table>
    </div>

    <div class="signatures">
        <div>
            <div>Dibuat Oleh,</div>
            <div class="sign-space"></div>
            <div class="fw-bold">( Staff Finance / AP )</div>
        </div>
        <div>
            <div>Diperiksa Oleh,</div>
            <div class="sign-space"></div>
            <div class="fw-bold">( Accounting Head )</div>
        </div>
        <div>
            <div>Disetujui Supplier,</div>
            <div class="sign-space"></div>
            <div class="fw-bold">( {{ $selectedSupplier->supplier }} )</div>
        </div>
    </div>
</body>
</html>
