<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Opname Q{{ $opname->periode }} - {{ $opname->year ?? date('Y', strtotime($opname->date)) }}</title>
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/css/core.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/css/pages/app-invoice-print.css" />
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
            background: #fff;
            padding: 20px;
        }
        @media print {
            body {
                padding: 0;
            }
            .no-print {
                display: none !important;
            }
            .page-break {
                page-break-after: always;
            }
        }
        .table-print {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
        }
        .table-print th, .table-print td {
            border: 1px solid #ccc;
            padding: 5px 7px;
            vertical-align: middle;
        }
        .table-print th {
            background-color: #f5f5f5;
            text-align: center;
            font-weight: 700;
        }
        .bg-bdg {
            background-color: #e8f4fd !important;
        }
        .bg-bks {
            background-color: #fef7ea !important;
        }
        .signature-box {
            border-top: 1px solid #333;
            width: 160px;
            margin: 60px auto 0 auto;
            text-align: center;
            font-weight: bold;
            font-size: 11px;
        }
    </style>
</head>
<body>
    <div class="no-print mb-4 text-end">
        <button type="button" class="btn btn-primary" onclick="window.print();">
            <i class="mdi mdi-printer me-1"></i> Cetak / Simpan PDF
        </button>
        <button type="button" class="btn btn-secondary" onclick="window.close();">
            Tutup
        </button>
    </div>

    {{-- Company & Document Header --}}
    <div class="d-flex justify-content-between align-items-start border-bottom pb-3 mb-3">
        <div>
            <h4 class="fw-bold mb-1 text-primary">PT REFTECH JAYA OPTIMA</h4>
            <p class="mb-0 text-muted small">Taman Kopo Indah V, Ruko Sommerville No. 31, Bandung – Jawa Barat 40218</p>
            <p class="mb-0 text-muted small">Telp: 022 54417653 | Email: admin@reftech.id</p>
        </div>
        <div class="text-end">
            <h4 class="fw-bold mb-0">BERITA ACARA STOCK OPNAME</h4>
            <div class="fw-bold text-primary mt-1">PERIODE QUARTER {{ $opname->periode }} - {{ $opname->year ?? date('Y', strtotime($opname->date)) }}</div>
            <div class="text-muted small">No. Sesi: #{{ $opname->id }} | Tanggal: {{ \Carbon\Carbon::parse($opname->date)->translatedFormat('d F Y') }}</div>
            <div class="text-muted small">Petugas Sesi: {{ $opname->user->name ?? '-' }}</div>
        </div>
    </div>

    {{-- Table --}}
    <table class="table-print mb-4">
        <thead>
            <tr>
                <th rowspan="2" style="width: 30px;">No</th>
                <th rowspan="2" style="min-width: 180px;">SKU / Replacement</th>
                <th rowspan="2" style="min-width: 140px;">Deskripsi</th>
                <th colspan="3" class="bg-bdg">Gudang Bandung (BDG)</th>
                <th colspan="3" class="bg-bks">Gudang Bekasi (BKS)</th>
                <th colspan="2">Konsolidasi Total</th>
                <th rowspan="2" style="min-width: 100px;">Catatan</th>
            </tr>
            <tr>
                {{-- BDG --}}
                <th class="bg-bdg" style="width: 45px;">Sis</th>
                <th class="bg-bdg" style="width: 45px;">Fisik</th>
                <th class="bg-bdg" style="width: 45px;">Selisih</th>
                {{-- BKS --}}
                <th class="bg-bks" style="width: 45px;">Sis</th>
                <th class="bg-bks" style="width: 45px;">Fisik</th>
                <th class="bg-bks" style="width: 45px;">Selisih</th>
                {{-- Total --}}
                <th style="width: 45px;">Fisik</th>
                <th style="width: 50px;">Selisih</th>
            </tr>
        </thead>
        <tbody>
            @forelse($detailOpname as $index => $item)
                @php
                    $sistemBdg = (int)($item->sistem_bdg ?? 0);
                    $sistemBks = (int)($item->sistem_bks ?? 0);
                    $fisikBdg = $item->fisik_bdg !== null ? (int)$item->fisik_bdg : '-';
                    $fisikBks = $item->fisik_bks !== null ? (int)$item->fisik_bks : '-';
                    $selisihBdg = $item->fisik_bdg !== null ? ((int)$item->fisik_bdg - $sistemBdg) : '-';
                    $selisihBks = $item->fisik_bks !== null ? ((int)$item->fisik_bks - $sistemBks) : '-';
                    $fisikTotal = $item->fisik_total !== null ? (int)$item->fisik_total : '-';
                    $selisihTotal = $item->selisih_total !== null ? (int)$item->selisih_total : '-';
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="fw-bold">
                        {{ $item->replacement }}
                        @if($item->go)
                            <span class="text-muted">({{ substr($item->go, 0, 1) }})</span>
                        @endif
                    </td>
                    <td>{{ $item->description ?? '-' }}</td>
                    {{-- BDG --}}
                    <td class="text-center bg-bdg">{{ $sistemBdg }}</td>
                    <td class="text-center bg-bdg fw-bold">{{ $fisikBdg }}</td>
                    <td class="text-center bg-bdg {{ is_numeric($selisihBdg) && $selisihBdg != 0 ? 'text-danger fw-bold' : '' }}">
                        {{ is_numeric($selisihBdg) && $selisihBdg > 0 ? '+'.$selisihBdg : $selisihBdg }}
                    </td>
                    {{-- BKS --}}
                    <td class="text-center bg-bks">{{ $sistemBks }}</td>
                    <td class="text-center bg-bks fw-bold">{{ $fisikBks }}</td>
                    <td class="text-center bg-bks {{ is_numeric($selisihBks) && $selisihBks != 0 ? 'text-danger fw-bold' : '' }}">
                        {{ is_numeric($selisihBks) && $selisihBks > 0 ? '+'.$selisihBks : $selisihBks }}
                    </td>
                    {{-- Konsolidasi --}}
                    <td class="text-center fw-bold">{{ $fisikTotal }}</td>
                    <td class="text-center {{ is_numeric($selisihTotal) && $selisihTotal != 0 ? 'text-danger fw-bold' : '' }}">
                        {{ is_numeric($selisihTotal) && $selisihTotal > 0 ? '+'.$selisihTotal : $selisihTotal }}
                    </td>
                    <td class="small">{{ $item->note ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="12" class="text-center py-4 text-muted">Tidak ada data produk pada stock opname ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Signatures --}}
    <div class="row mt-4 pt-3 text-center" style="page-break-inside: avoid;">
        <div class="col-4">
            <p class="mb-0 text-muted small">Petugas Gudang BDG</p>
            <div class="signature-box">( PIC Bandung )</div>
        </div>
        <div class="col-4">
            <p class="mb-0 text-muted small">Petugas Gudang BKS</p>
            <div class="signature-box">( PIC Bekasi )</div>
        </div>
        <div class="col-4">
            <p class="mb-0 text-muted small">Warehouse Supervisor / Manager</p>
            <div class="signature-box">( Kepala Gudang )</div>
        </div>
    </div>
</body>
</html>
