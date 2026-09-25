<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Jalan Work Order - {{ $wo->productOut->no_product_out ?? $wo->no_wo }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/materialdesignicons.css') }}" />
    <style>
        @page {
            size: A4 portrait;
            margin: 15mm 12mm 15mm 12mm;
        }

        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        body, input, textarea, button, table, td, th {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif !important;
        }

        body {
            font-size: 12px;
            line-height: 1.45;
            color: #1e293b;
            background: #fff;
            margin: 0;
            padding: 20px;
        }

        .no-print-bar {
            background: #1e293b;
            color: #fff;
            padding: 12px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-radius: 8px;
            margin-bottom: 25px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        .btn-print {
            background: #2563eb;
            color: white;
            border: none;
            padding: 8px 18px;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            text-decoration: none;
            font-size: 13px;
        }
        .btn-print:hover {
            background: #1d4ed8;
        }

        .btn-back {
            background: #475569;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            font-size: 13px;
        }
        .btn-back:hover {
            background: #334155;
        }

        .sj-container {
            max-width: 800px;
            margin: 0 auto;
            border: 1px solid #e2e8f0;
            padding: 30px;
            background: #fff;
            border-radius: 4px;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            border-bottom: 2px solid #1e293b;
            padding-bottom: 15px;
        }

        .header-table td {
            vertical-align: top;
        }

        .company-logo {
            max-height: 50px;
            width: auto;
            margin-bottom: 6px;
        }

        .company-name {
            font-size: 14px;
            font-weight: bold;
            color: #0f172a;
            margin: 0 0 2px 0;
        }

        .company-address {
            font-size: 10px;
            color: #64748b;
            margin: 0;
            line-height: 1.3;
        }

        .doc-title-box {
            text-align: right;
        }

        .doc-title {
            font-size: 20px;
            font-weight: 800;
            color: #1e3a8a;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin: 0 0 4px 0;
        }

        .doc-no {
            font-size: 13px;
            font-weight: bold;
            color: #0f172a;
            font-family: monospace;
            background: #f1f5f9;
            padding: 3px 8px;
            border-radius: 4px;
            display: inline-block;
            border: 1px solid #cbd5e1;
        }

        .info-grid {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
        }

        .info-grid td {
            padding: 8px 12px;
            vertical-align: top;
            font-size: 11.5px;
        }

        .info-label {
            color: #64748b;
            font-size: 10.5px;
            text-transform: uppercase;
            font-weight: 600;
            margin-bottom: 2px;
        }

        .info-value {
            color: #0f172a;
            font-weight: 600;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .items-table th {
            background: #1e293b;
            color: #ffffff;
            font-size: 10.5px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 8px 10px;
            text-align: left;
            border: 1px solid #1e293b;
        }

        .items-table td {
            padding: 8px 10px;
            border: 1px solid #cbd5e1;
            font-size: 11.5px;
            vertical-align: middle;
        }

        .items-table tr:nth-child(even) td {
            background-color: #f8fafc;
        }

        .text-center { text-align: center; }
        .text-end { text-align: right; }
        .fw-bold { font-weight: bold; }

        .tag-badge {
            display: inline-block;
            padding: 1px 6px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: 700;
            vertical-align: middle;
            margin-left: 5px;
        }
        .tag-gen { background: #fef3c7; color: #92400e; border: 1px solid #fde68a; }
        .tag-rep { background: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; }

        .signatures-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 35px;
        }

        .signatures-table td {
            width: 33.33%;
            text-align: center;
            vertical-align: top;
            padding: 0 15px;
        }

        .sig-title {
            font-size: 11px;
            font-weight: 600;
            color: #475569;
            margin-bottom: 60px;
            text-transform: uppercase;
        }

        .sig-line {
            border-top: 1px solid #0f172a;
            margin: 0 auto 4px auto;
            width: 85%;
        }

        .sig-name {
            font-weight: bold;
            color: #0f172a;
            font-size: 11.5px;
        }

        .sig-role {
            font-size: 10px;
            color: #64748b;
        }

        .notes-box {
            border-left: 3px solid #2563eb;
            background: #f8fafc;
            padding: 8px 12px;
            font-size: 11px;
            color: #334155;
            margin-top: 15px;
        }

        @media print {
            body {
                padding: 0;
                background: #fff;
            }
            .no-print-bar {
                display: none !important;
            }
            .sj-container {
                border: none;
                padding: 0;
                max-width: 100%;
            }
        }
    </style>
</head>
<body>

    {{-- Action Bar (Hidden on Print) --}}
    <div class="no-print-bar">
        <div>
            <strong>Surat Jalan / Bukti Pengeluaran Barang Work Order</strong>
            <div style="font-size: 11px; color: #94a3b8;">No: {{ $wo->productOut->no_product_out ?? '-' }} (Ref: {{ $wo->no_wo }})</div>
        </div>
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('work-orders.show', $wo->id) }}" class="btn-back">
                <i class="mdi mdi-arrow-left"></i> Kembali ke Work Order
            </a>
            <button onclick="window.print()" class="btn-print">
                <i class="mdi mdi-printer"></i> Cetak Dokumen (A4)
            </button>
        </div>
    </div>

    {{-- Main Document Sheet --}}
    <div class="sj-container">
        {{-- Document Header --}}
        <table class="header-table">
            <tr>
                <td style="width: 55%;">
                    <img src="{{ asset('/asset/logo/Reftech-Log.png') }}" alt="PT Reftech Jaya Optima" class="company-logo">
                    <h3 class="company-name">PT. REFTECH JAYA OPTIMA</h3>
                    <p class="company-address">
                        Taman Kopo Indah V, Ruko Sommerville No. 31, Bandung – Jawa Barat 40218<br>
                        Telp: 022-54417653 | Email: admin@reftech.id | Web: www.reftech.id
                    </p>
                </td>
                <td style="width: 45%;" class="doc-title-box">
                    <div class="doc-title">SURAT JALAN</div>
                    <div style="font-size: 11px; font-weight: bold; color: #475569; margin-bottom: 5px;">BUKTI PENGELUARAN SUKU CADANG</div>
                    <div class="doc-no">{{ $wo->productOut->no_product_out ?? 'BK-WO/' . $wo->no_wo }}</div>
                    <div style="font-size: 11px; color: #64748b; margin-top: 4px;">
                        Tanggal: <strong>{{ $wo->productOut && $wo->productOut->date ? \Carbon\Carbon::parse($wo->productOut->date)->isoFormat('DD MMMM YYYY') : now()->isoFormat('DD MMMM YYYY') }}</strong>
                    </div>
                </td>
            </tr>
        </table>

        {{-- Information Grid --}}
        <table class="info-grid">
            <tr>
                <td style="width: 50%; border-right: 1px solid #e2e8f0;">
                    <div class="info-label">Penerima / Pemohon:</div>
                    <div class="info-value" style="font-size: 13px;">{{ $wo->technician->name ?? ($wo->creator->name ?? 'Teknisi Workshop') }}</div>
                    <div style="color: #64748b; font-size: 11px; margin-top: 2px;">
                        Divisi: <strong>Workshop &amp; Service Maintenance</strong>
                    </div>
                </td>
                <td style="width: 50%;">
                    <div class="info-label">Referensi &amp; Unit Peruntukan:</div>
                    <div class="info-value">
                        No. WO: <span style="font-family: monospace; color: #1e3a8a;">{{ $wo->no_wo }}</span>
                    </div>
                    <div style="color: #334155; font-size: 11px; margin-top: 2px;">
                        Mesin: <strong>{{ $wo->fixedAsset->code ?? '-' }}</strong> — {{ $wo->fixedAsset->unit->brand ?? ($wo->fixedAsset->unit_brand ?: ($wo->fixedAsset->desc ?: 'Mesin')) }}
                        @if ($wo->fixedAsset && $wo->fixedAsset->serial_number)
                            (SN: {{ $wo->fixedAsset->serial_number }})
                        @endif
                    </div>
                </td>
            </tr>
        </table>

        {{-- Table Suku Cadang --}}
        <table class="items-table">
            <thead>
                <tr>
                    <th class="text-center" style="width: 45px;">No.</th>
                    <th>SPAREPART</th>
                    <th class="text-center" style="width: 90px;">Qty</th>
                    <th class="text-center" style="width: 90px;">Satuan</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($wo->items as $idx => $item)
                    @php
                        $dp = $item->detailProduct;
                        $p = $item->product ?? $dp?->product;
                        $ser = $p?->serial?->first();
                        $brand = $ser?->brand ?? ($p?->brand ?? '');
                        $pn = $ser?->pn ?? ($p?->pn ?? ($dp?->replacement ?: '-'));
                        $desc = $p?->detail_desc ?: ($p?->description ?: ($p?->commodity ?: ($item->item_name ?: 'Suku Cadang')));
                        $goRaw = $p?->go ?: 'Genuine';
                        $goCode = strtoupper(substr(trim($goRaw), 0, 1));
                        $unit = $item->unit ?: ($p?->unit ?: 'Pcs');
                        $qty = (float) ($item->qty_issued > 0 ? $item->qty_issued : ($item->qty_approved > 0 ? $item->qty_approved : $item->qty_requested));
                    @endphp
                    <tr>
                        <td class="text-center">{{ $idx + 1 }}</td>
                        <td>
                            <span class="fw-bold text-dark font-monospace" style="font-size: 12px;">{{ $brand ? $brand . ' — ' : '' }}{{ $pn }}</span>
                            <span style="color: #64748b; margin: 0 4px;">-</span>
                            <span class="text-dark">{{ $desc }}</span>
                            <span class="tag-badge {{ $goCode === 'R' ? 'tag-rep' : 'tag-gen' }}">
                                [{{ $goCode }}] {{ $goCode === 'R' ? 'Replacement' : 'Genuine' }}
                            </span>
                        </td>
                        <td class="text-center fw-bold text-dark" style="font-size: 13px;">
                            {{ $qty }}
                        </td>
                        <td class="text-center" style="color: #475569; font-weight: 500;">
                            {{ $unit }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center" style="padding: 20px; color: #94a3b8;">
                            Tidak ada suku cadang terdaftar pada Work Order ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Notes Section --}}
        <div class="notes-box">
            <strong>Catatan Pengeluaran Gudang:</strong><br>
            {{ $wo->productOut->note ?? ($wo->warehouse_note ?? 'Barang dikeluarkan untuk kebutuhan pemeliharaan unit mesin internal Reftech.') }}
        </div>

        {{-- Signature Section --}}
        <table class="signatures-table">
            <tr>
                <td>
                    <div class="sig-title">Diserahkan Oleh (Gudang)</div>
                    <div class="sig-line"></div>
                    <div class="sig-name">{{ $wo->warehouseUser->name ?? ($wo->productOut->user->name ?? 'Petugas Gudang') }}</div>
                    <div class="sig-role">Warehouse / Logistics</div>
                </td>
                <td>
                    <div class="sig-title">Diterima Oleh (Teknisi)</div>
                    <div class="sig-line"></div>
                    <div class="sig-name">{{ $wo->technician->name ?? ($wo->creator->name ?? 'Teknisi Workshop') }}</div>
                    <div class="sig-role">Service &amp; Maintenance</div>
                </td>
                <td>
                    <div class="sig-title">Mengetahui / Accounting</div>
                    <div class="sig-line"></div>
                    <div class="sig-name">{{ $wo->accountingUser->name ?? 'Finance / Accounting' }}</div>
                    <div class="sig-role">Otorisasi Anggaran</div>
                </td>
            </tr>
        </table>
    </div>

</body>
</html>
