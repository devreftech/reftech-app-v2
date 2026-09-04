@php
    $isKojisha = ($quote->client?->info ?? $invoice->flag) === 'Kojisha';
    $senderName = auth()->user()?->name ?? 'Staff Accounting';
    $senderRole = auth()->user()?->role ?? 'Accounting';
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sampul — {{ $invoice->no_invoice ?? '#' . $invoice->id }}</title>
    
    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/materialdesignicons.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/core.css') }}" />

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            background-color: #f1f5f9;
            color: #0f172a;
            font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            font-size: 13px;
            line-height: 1.4;
            padding: 24px;
        }

        .screen-toolbar {
            max-width: 270mm;
            margin: 0 auto 20px auto;
            background: #1e293b;
            color: #ffffff;
            border-radius: 10px;
            padding: 12px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
        }

        .screen-toolbar .btn-print {
            background-color: #0284c7;
            color: #ffffff;
            border: none;
            padding: 8px 20px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 13px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            text-decoration: none;
            transition: background 0.2s;
        }

        .screen-toolbar .btn-print:hover {
            background-color: #0369a1;
        }

        .screen-toolbar .btn-close-window {
            background-color: transparent;
            color: #cbd5e1;
            border: 1px solid #475569;
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 13px;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s;
        }

        .screen-toolbar .btn-close-window:hover {
            background-color: #334155;
            color: #ffffff;
        }

        /* Printable Sheet Canvas */
        .sampul-sheet {
            width: 270mm;
            min-height: 180mm;
            margin: 0 auto;
            background: #ffffff;
            padding: 14mm 18mm;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
            position: relative;
        }

        .kop-section {
            border-bottom: 2px solid #000000;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }

        .dokumen-stamp {
            border: 2.5px solid #000000;
            border-radius: 6px;
            padding: 6px 18px;
            text-transform: uppercase;
            letter-spacing: 0.15em;
            font-weight: 800;
            font-size: 20px;
            color: #000000;
            display: inline-block;
        }

        .box-from {
            border: 1.5px solid #333333;
            border-radius: 8px;
            padding: 10px 14px;
            display: inline-block;
            min-width: 250px;
            background: #ffffff;
        }

        .box-to-wrap {
            width: 100%;
            margin-top: 5.3cm;
            margin-bottom: 15px;
            border-collapse: collapse;
        }

        .box-to {
            border: 2px solid #000000;
            border-radius: 12px;
            padding: 16px 20px;
            background: #ffffff;
            width: 100%;
            box-sizing: border-box;
        }

        .box-to table {
            width: 100%;
            border-collapse: collapse;
        }

        .box-to table td {
            padding: 4px 6px;
            vertical-align: top;
            font-size: 13.5px;
            color: #000000;
        }

        .doc-footer {
            border-top: 1px dashed #666666;
            padding-top: 12px;
            margin-top: 25px;
            clear: both;
            font-size: 11px;
            color: #555555;
        }

        /* PRINT CONFIGURATION */
        @page {
            size: A4 landscape;
            margin: 10mm 15mm;
        }

        @media print {
            body {
                background: #ffffff !important;
                padding: 0 !important;
                margin: 0 !important;
            }

            .screen-toolbar {
                display: none !important;
            }

            .sampul-sheet {
                border: none !important;
                box-shadow: none !important;
                padding: 0 !important;
                margin: 0 !important;
                width: 100% !important;
                max-width: 100% !important;
                border-radius: 0 !important;
            }
        }
    </style>
</head>
<body>

    <!-- On-screen Control Toolbar (Hidden when printing) -->
    <div class="screen-toolbar">
        <div style="display: flex; align-items: center; gap: 10px;">
            <i class="mdi mdi-printer-outline" style="font-size: 22px; color: #38bdf8;"></i>
            <div>
                <div style="font-weight: 700; font-size: 14px;">Pratinjau Cetak Sampul Dokumen</div>
                <div style="font-size: 11px; color: #94a3b8;">Format: A4 Landscape &bull; {{ $invoice->no_invoice ?? 'Draft' }}</div>
            </div>
        </div>
        <div style="display: flex; align-items: center; gap: 8px;">
            <button class="btn-print" onclick="window.print();">
                <i class="mdi mdi-printer"></i> Cetak Sekarang (Print)
            </button>
            <button class="btn-close-window" onclick="window.close();">
                <i class="mdi mdi-close"></i> Tutup
            </button>
        </div>
    </div>

    <!-- Printable Sheet Canvas -->
    <div class="sampul-sheet">
        {{-- KOP SURAT / HEADER --}}
        <div class="kop-section">
            <table style="width: 100%;">
                <tr>
                    <td style="vertical-align: middle; width: 80px;">
                        @if ($isKojisha)
                            <img src="{{ asset('/asset/logo/Logo-update-size.png') }}" alt="Kojisha Logo" style="height: 48px; object-fit: contain;">
                        @else
                            <img src="{{ asset('/asset/logo/Reftech-Log.png') }}" alt="Reftech Logo" style="height: 46px; object-fit: contain;">
                        @endif
                    </td>
                    <td style="vertical-align: top; padding-left: 15px;">
                        <h4 style="margin: 0; font-weight: 800; color: #000; font-size: 17px; letter-spacing: 0.04em;">
                            {{ $isKojisha ? 'PT KOJISHA INNOTIV INDONESIA' : 'PT REFTECH JAYA OPTIMA' }}
                        </h4>
                        @unless ($isKojisha)
                            <div style="font-size: 11px; font-weight: 700; margin: 3px 0;">
                                <span style="color: #dc2626;">COMPRESSOR</span> &bull;
                                <span style="color: #16a34a;">SPAREPART</span> &bull;
                                <span style="color: #4b5563;">RENTAL</span> &bull;
                                <span style="color: #0284c7;">SERVICE</span>
                            </div>
                        @endunless
                        <div style="font-size: 11px; line-height: 1.4; color: #222;">
                            @if ($isKojisha)
                                <div>Jl. Nancep No. 45A, Setu, Cibitung – Kab. Bekasi 17320</div>
                                <div>Telp: +62 812-1000-0997 &nbsp;|&nbsp; Email: admin@kojisha.com</div>
                            @else
                                <div>Taman Kopo Indah V, Soho Sommerville No. 31, Bandung – Jawa Barat 40218</div>
                                <div>Telp: (022) 54417653 &nbsp;|&nbsp; Email: accounting@reftech.id &nbsp;|&nbsp; www.reftech.id</div>
                            @endif
                        </div>
                    </td>
                    <td style="vertical-align: middle; text-align: right; width: 140px;">
                        <div class="dokumen-stamp">
                            DOKUMEN
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        {{-- FROM & META SECTION --}}
        <table style="width: 100%; margin-top: 15px;">
            <tr>
                <td style="vertical-align: top; width: 50%;">
                    <div class="box-from">
                        <div style="font-size: 10px; font-weight: 700; text-transform: uppercase; color: #555; margin-bottom: 3px;">
                            FROM (Pengirim):
                        </div>
                        <div style="border-left: 2px solid #0284c7; padding-left: 8px;">
                            <div style="font-weight: 700; font-size: 13px; color: #000;">Mr. {{ $senderName }}</div>
                            <div style="font-size: 11px; color: #444;">{{ $senderRole }} &bull; {{ $isKojisha ? 'PT Kojisha Innotiv Indonesia' : 'PT Reftech Jaya Optima' }}</div>
                        </div>
                    </div>
                </td>
                <td style="vertical-align: top; text-align: right; width: 50%; font-size: 13px; color: #333;">
                    @if ($quote->po_number ?? $invoice->no_po)
                        <div><strong>PO No:</strong> <span style="font-weight: 700; color: #000; font-size: 14px;">{{ $quote->po_number ?? $invoice->no_po }}</span></div>
                    @endif
                </td>
            </tr>
        </table>

        {{-- TO / RECIPIENT BOX --}}
        @php
            $recipientPic = null;
            $recipientAddress = null;

            if (isset($pendingPO) && $pendingPO) {
                $isCombined = (bool) $pendingPO->combine_shipping_and_parts;
                if ($isCombined) {
                    // Pengiriman Disatukan: Pakai shipping recipient & shipping address
                    $recipientPic = $pendingPO->shipping_recipient;
                    if (($pendingPO->shipping_address_type ?? 'customer') === 'manual' && $pendingPO->shipping_address_manual) {
                        $recipientAddress = $pendingPO->shipping_address_manual;
                    }
                } else {
                    // Pengiriman Dokumen Dipisah: Pakai doc recipient & doc address
                    $recipientPic = $pendingPO->doc_recipient;
                    if (($pendingPO->doc_address_type ?? 'customer') === 'manual' && $pendingPO->doc_address_manual) {
                        $recipientAddress = $pendingPO->doc_address_manual;
                    }
                }
            }

            $toCompany = $quote->client?->company ?? '-';
            $toPicName = $recipientPic?->name_pic ?? $quote->pic?->name_pic ?? $quote->attn ?? '-';
            $toPicPhone = $recipientPic?->phone ?? $recipientPic?->phone_pic ?? $quote->pic?->phone_pic ?? $quote->pic?->phone ?? $quote->client?->phone ?? '-';

            if (!$recipientAddress) {
                if ($invoice->invoiceTo == '2' && $quote->client?->subAddress) {
                    $recipientAddress = $quote->client->subAddress;
                } else {
                    $recipientAddress = $quote->client?->address ?? '-';
                }
            }
        @endphp
        <table class="box-to-wrap">
            <tr>
                <td style="width: 40%;"></td>
                <td style="width: 60%; vertical-align: top;">
                    <div class="box-to">
                        <div style="border-bottom: 1.5px solid #000; padding-bottom: 5px; margin-bottom: 8px; font-weight: 800; font-size: 14px; text-transform: uppercase; letter-spacing: 0.05em;">
                            KEPADA YTH. (TO):
                        </div>
                        <table>
                            <tr>
                                <td style="width: 100px; font-weight: 600; color: #444;">Perusahaan</td>
                                <td style="width: 10px;">:</td>
                                <td style="font-weight: 700; font-size: 14px; color: #000;">{{ $toCompany }}</td>
                            </tr>
                            <tr>
                                <td style="font-weight: 600; color: #444;">Alamat</td>
                                <td>:</td>
                                <td style="font-weight: 500; line-height: 1.35;">{{ $recipientAddress }}</td>
                            </tr>
                            <tr>
                                <td style="font-weight: 600; color: #444;">Attn. (PIC)</td>
                                <td>:</td>
                                <td style="font-weight: 700; color: #0284c7;">{{ $toPicName }}</td>
                            </tr>
                            <tr>
                                <td style="font-weight: 600; color: #444;">Telepon / HP</td>
                                <td>:</td>
                                <td style="font-weight: 500;">{{ $toPicPhone }}</td>
                            </tr>
                        </table>
                    </div>
                </td>
            </tr>
        </table>

        {{-- FOOTER META --}}
        <div class="doc-footer">
            <table style="width: 100%;">
                <tr>
                    <td style="text-align: right; font-style: italic;">
                        Mohon konfirmasi setelah dokumen diterima dengan baik.
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <script>
        window.addEventListener('DOMContentLoaded', function () {
            // Automatically open print dialog
            setTimeout(function() {
                window.print();
            }, 500);
        });
    </script>
</body>
</html>
