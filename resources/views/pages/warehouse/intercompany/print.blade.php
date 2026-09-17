<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Barang Keluar Kojisha (Intercompany PO) - {{ $monthNames[$selectedMonth] }} {{ $selectedYear }}</title>
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/core.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/theme-default.css') }}" />
    <style>
        body {
            background: #fff;
            color: #000;
            font-size: 11px;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }
        .header-title {
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 2px;
        }
        .header-sub {
            font-size: 11px;
            color: #555;
            margin-bottom: 15px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 6px 8px;
            vertical-align: middle;
        }
        th {
            background-color: #f5f5f5 !important;
            font-weight: bold;
            text-align: center;
            font-size: 10.5px;
        }
        .text-end { text-align: right; }
        .text-center { text-align: center; }
        .fw-bold { font-weight: bold; }
        @media print {
            .no-print { display: none !important; }
            body { padding: 0; }
            table { page-break-inside: auto; }
            tr { page-break-inside: avoid; page-break-after: auto; }
        }
    </style>
</head>
<body>
    <div class="no-print mb-3 text-end">
        <button onclick="window.print()" class="btn btn-primary btn-sm">
            <i class="mdi mdi-printer"></i> Cetak / Simpan PDF
        </button>
        <button onclick="window.close()" class="btn btn-secondary btn-sm ms-1">Tutup</button>
    </div>

    {{-- Company Header --}}
    <div style="display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 2px solid #000; padding-bottom: 10px;">
        <div>
            <div style="font-size: 16px; font-weight: bold; color: #d9534f;">PT. KOJISHA INNOTIV INDONESIA</div>
            <div style="font-size: 11px; color: #333;">Laporan Pengeluaran Spare Part Penjualan Kojisha dari Gudang Reftech</div>
            <div style="font-size: 10px; color: #666;">Untuk Penerbitan Purchase Order (PO) Intercompany ke PT. Reftech Jaya Optima</div>
        </div>
        <div style="text-align: right;">
            <div style="font-size: 12px; font-weight: bold;">Periode: {{ $monthNames[$selectedMonth] }} {{ $selectedYear }}</div>
            <div style="font-size: 10px; color: #666;">Dicetak pada: {{ now()->translatedFormat('d F Y, H:i') }}</div>
        </div>
    </div>

    {{-- Table of items --}}
    <table>
        <thead>
            <tr>
                <th style="width: 30px;">No.</th>
                <th style="width: 130px;">No. BK &amp; Tanggal</th>
                <th style="width: 110px;">Ref. Invoice/PO</th>
                <th style="width: 170px;">Customer (Tujuan)</th>
                <th style="width: 50px;">Asal</th>
                <th>Nama Barang / Part Number</th>
                <th style="width: 45px;">Qty</th>
                <th style="width: 90px;" class="text-end">HPP Satuan (Rp)</th>
                <th style="width: 105px;" class="text-end">Subtotal HPP (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @php
                $grandTotalQty = 0;
                $grandTotalAmount = 0;
            @endphp
            @forelse($items as $idx => $item)
                @php
                    $bk = $item->productOut;
                    $partNumber = $item->serialProduct?->pn ?? ($item->detailProduct?->product?->part_number ?? ($item->detailProduct?->replacement ?? '-'));
                    $brand = $item->serialProduct?->brand ?? ($item->detailProduct?->product?->brand ?? '');
                    $desc = $item->detailProduct?->product?->description ?? ($item->detailProduct?->replacement ?? '-');
                    $unitHpp = (float) ($item->detailProduct?->modal ?? ($item->detailProduct?->hpp ?? 0));
                    $qty = (int) ($item->qty ?? 1);
                    $subtotal = $unitHpp * $qty;

                    $grandTotalQty += $qty;
                    $grandTotalAmount += $subtotal;
                @endphp
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td>
                        <div class="fw-bold">{{ $bk->no_product_out ?: 'BK #' . $bk->id }}</div>
                        <div style="font-size: 9.5px; color: #666;">{{ \Carbon\Carbon::parse($bk->date)->format('d/m/Y') }}</div>
                    </td>
                    <td>
                        <div>{{ $bk->invoice ?: '-' }}</div>
                        @if($bk->po)
                            <div style="font-size: 9.5px; color: #666;">PO: {{ $bk->po }}</div>
                        @endif
                    </td>
                    <td>
                        <div style="font-size: 10.5px;">{{ trim($bk->detail_client) ?: '-' }}</div>
                    </td>
                    <td class="text-center fw-bold">{{ $item->warehouse ?: 'BDG' }}</td>
                    <td>
                        <div class="fw-bold">{{ $brand ? $brand . ' ' : '' }}{{ $partNumber }}</div>
                        <div style="font-size: 9.5px; color: #555;">{{ $desc }}</div>
                    </td>
                    <td class="text-center fw-bold">{{ $qty }}</td>
                    <td class="text-end">{{ number_format($unitHpp, 0, '', '.') }}</td>
                    <td class="text-end fw-bold">{{ number_format($subtotal, 0, '', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center" style="padding: 20px;">
                        Tidak ada catatan barang keluar Kojisha untuk periode ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
        @if($items->isNotEmpty())
        <tfoot>
            <tr style="background-color: #f9f9f9; font-weight: bold;">
                <td colspan="6" class="text-end" style="padding: 8px;">TOTAL KESELURUHAN ({{ $items->count() }} Baris Item):</td>
                <td class="text-center">{{ number_format($grandTotalQty, 0, '', '.') }}</td>
                <td></td>
                <td class="text-end" style="font-size: 12px; color: #000;">
                    Rp {{ number_format($grandTotalAmount, 0, '', '.') }}
                </td>
            </tr>
        </tfoot>
        @endif
    </table>

    {{-- Signatures --}}
    <div style="margin-top: 35px; display: flex; justify-content: space-between; text-align: center;">
        <div style="width: 200px;">
            <div style="margin-bottom: 50px;">Dibuat Oleh (Kojisha),</div>
            <div style="border-top: 1px solid #000; padding-top: 3px; font-weight: bold;">( {{ Auth::user()->name ?? 'Admin / Logistik' }} )</div>
        </div>
        <div style="width: 200px;">
            <div style="margin-bottom: 50px;">Diperiksa Oleh (Finance),</div>
            <div style="border-top: 1px solid #000; padding-top: 3px; font-weight: bold;">( &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; )</div>
        </div>
        <div style="width: 200px;">
            <div style="margin-bottom: 50px;">Diterima Oleh (Reftech),</div>
            <div style="border-top: 1px solid #000; padding-top: 3px; font-weight: bold;">( &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; )</div>
        </div>
    </div>
</body>
</html>
