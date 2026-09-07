<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buku Kas Kecil - {{ $bank->bank }} ({{ $startDate }} s/d {{ $endDate }})</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@mdi/font@7.2.96/css/materialdesignicons.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 12px;
            color: #222;
            background-color: #f4f6f9;
        }
        .report-container {
            max-width: 1000px;
            margin: 20px auto;
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            border: 1px solid #e5e7eb;
        }
        .table-statement th, .table-statement td {
            padding: 6px 10px;
            vertical-align: middle;
            font-size: 11.5px;
        }
        .total-row {
            background-color: #f8f9fa !important;
            border-top: 2px solid #000 !important;
            border-bottom: 2px solid #000 !important;
            font-weight: bold;
        }
        @media print {
            body {
                background: #fff;
                padding: 0;
            }
            .report-container {
                box-shadow: none;
                border: none;
                margin: 0;
                padding: 10px;
                max-width: 100%;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>

    <div class="container my-3 no-print text-center">
        <button onclick="window.print()" class="btn btn-primary px-4 me-2 shadow-sm">
            <i class="mdi mdi-printer me-1"></i> Cetak Laporan
        </button>
        <button onclick="window.close()" class="btn btn-secondary px-3 shadow-sm">
            <i class="mdi mdi-close me-1"></i> Tutup
        </button>
    </div>

    <div class="report-container">
        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-3">
            <div>
                <h5 class="fw-bold mb-0 text-primary">PT. REFRIGERASI TEKNIK INDONESIA</h5>
                <small class="text-muted d-block">HVAC &amp; Industrial Refrigeration Solution</small>
                <small class="text-muted" style="font-size: 11px;">Finance &amp; Accounting Department</small>
            </div>
            <div class="text-end">
                <h5 class="fw-bold mb-0 text-dark text-uppercase">BUKU KAS KECIL (PETTY CASH)</h5>
                <small class="text-muted font-monospace">
                    Periode: {{ \Carbon\Carbon::parse($startDate)->translatedFormat('d F Y') }} s/d {{ \Carbon\Carbon::parse($endDate)->translatedFormat('d F Y') }}
                </small>
            </div>
        </div>

        {{-- Account Meta --}}
        <div class="row g-2 mb-3 p-3 bg-light rounded border" style="font-size: 12px;">
            <div class="col-6">
                <table class="table table-sm table-borderless mb-0">
                    <tr>
                        <td class="text-muted fw-semibold" style="width: 140px;">Akun Kasir / Bank:</td>
                        <td class="fw-bold text-dark">{{ $bank->bank }} ({{ $bank->no_rek }})</td>
                    </tr>
                    <tr>
                        <td class="text-muted fw-semibold">PIC Pemegang Kasir:</td>
                        <td class="fw-bold text-dark">{{ $bank->pic?->name ?? 'Kasir' }} ({{ $bank->pic?->nip ?? '-' }})</td>
                    </tr>
                    <tr>
                        <td class="text-muted fw-semibold">Atas Nama:</td>
                        <td class="fw-bold text-dark">{{ $bank->atas_nama }}</td>
                    </tr>
                </table>
            </div>
            <div class="col-6">
                <table class="table table-sm table-borderless mb-0">
                    <tr>
                        <td class="text-muted fw-semibold" style="width: 140px;">Batas Plafond:</td>
                        <td class="fw-bold text-dark">{{ $bank->plafond > 0 ? 'Rp ' . number_format($bank->plafond, 0, ',', '.') : 'Tidak Dibatasi' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted fw-semibold">Saldo Awal Periode:</td>
                        <td class="fw-bold text-dark">Rp {{ number_format($openingBalance, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted fw-semibold">Saldo Akhir Periode:</td>
                        <td class="fw-bold text-primary">Rp {{ number_format($closingBalance, 0, ',', '.') }}</td>
                    </tr>
                </table>
            </div>
        </div>

        {{-- Ledger Table --}}
        <table class="table table-bordered table-statement mb-3">
            <thead class="table-light">
                <tr class="text-center">
                    <th style="width: 75px;">Tgl</th>
                    <th style="width: 110px;">No. Voucher</th>
                    <th style="width: 130px;">Kategori</th>
                    <th>Uraian / Keperluan</th>
                    <th style="width: 120px;">Penerima / Sumber</th>
                    <th style="width: 100px;" class="text-end">Debet (-)</th>
                    <th style="width: 100px;" class="text-end">Kredit (+)</th>
                    <th style="width: 110px;" class="text-end">Saldo</th>
                </tr>
            </thead>
            <tbody>
                {{-- Opening Balance Row --}}
                <tr class="bg-light fst-italic">
                    <td class="text-center">{{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }}</td>
                    <td class="text-center">-</td>
                    <td>-</td>
                    <td class="fw-bold">SALDO AWAL (OPENING BALANCE)</td>
                    <td>-</td>
                    <td class="text-end">-</td>
                    <td class="text-end">-</td>
                    <td class="text-end fw-bold">Rp {{ number_format($openingBalance, 0, ',', '.') }}</td>
                </tr>

                @forelse($ledger as $item)
                    <tr>
                        <td class="text-center text-nowrap">{{ \Carbon\Carbon::parse($item->date)->format('d/m/Y') }}</td>
                        <td class="text-center font-monospace">{{ $item->voucher_number }}</td>
                        <td>{{ $item->category }}</td>
                        <td>{{ $item->description }}</td>
                        <td>{{ $item->type === 'disbursement' ? ($item->recipient ?: '-') : ($item->source_bank ?: 'Bank Kantor') }}</td>
                        <td class="text-end text-nowrap {{ $item->out > 0 ? 'text-danger fw-semibold' : '' }}">
                            {{ $item->out > 0 ? 'Rp ' . number_format($item->out, 0, ',', '.') : '-' }}
                        </td>
                        <td class="text-end text-nowrap {{ $item->in > 0 ? 'text-primary fw-semibold' : '' }}">
                            {{ $item->in > 0 ? 'Rp ' . number_format($item->in, 0, ',', '.') : '-' }}
                        </td>
                        <td class="text-end text-nowrap fw-bold">
                            Rp {{ number_format($item->running_balance, 0, ',', '.') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-3 text-muted">
                            <em>Tidak ada transaksi mutasi kas kecil pada periode ini.</em>
                        </td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <td colspan="5" class="text-end">TOTAL MUTASI PERIODE INI:</td>
                    <td class="text-end text-danger text-nowrap">Rp {{ number_format($totalOut, 0, ',', '.') }}</td>
                    <td class="text-end text-primary text-nowrap">Rp {{ number_format($totalIn, 0, ',', '.') }}</td>
                    <td class="text-end text-nowrap fw-bolder">Rp {{ number_format($closingBalance, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>

        {{-- Signatures --}}
        <div class="row g-2 text-center pt-4" style="font-size: 11.5px;">
            <div class="col-4">
                <div class="border rounded p-2">
                    <div class="fw-bold text-muted">Pemegang Kasir,</div>
                    <div style="height: 60px;" class="d-flex align-items-end justify-content-center">
                        <span class="border-bottom border-dark w-75 pb-1 fw-semibold">{{ $bank->pic?->name ?? 'Kasir' }}</span>
                    </div>
                    <small class="text-muted">Kasir Operasional</small>
                </div>
            </div>
            <div class="col-4">
                <div class="border rounded p-2">
                    <div class="fw-bold text-muted">Diperiksa Oleh,</div>
                    <div style="height: 60px;" class="d-flex align-items-end justify-content-center">
                        <span class="border-bottom border-dark w-75 pb-1 fw-semibold">Accounting &amp; Finance</span>
                    </div>
                    <small class="text-muted">Finance Head</small>
                </div>
            </div>
            <div class="col-4">
                <div class="border rounded p-2">
                    <div class="fw-bold text-muted">Disetujui Oleh,</div>
                    <div style="height: 60px;" class="d-flex align-items-end justify-content-center">
                        <span class="border-bottom border-dark w-75 pb-1 fw-semibold">Direksi</span>
                    </div>
                    <small class="text-muted">Direktur Utama</small>
                </div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="d-flex justify-content-between align-items-center mt-4 pt-2 border-top text-muted" style="font-size: 10px;">
            <span>Dicetak pada: {{ \Carbon\Carbon::now()->translatedFormat('d F Y H:i:s') }}</span>
            <span>Reftech ERP System - Modul Petty Cash Management</span>
        </div>
    </div>

</body>
</html>
