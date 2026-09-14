<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Laba Rugi - PT REFTECH JAYA OPTIMA ({{ $startString }} s/d {{ $endString }})</title>

    <!-- Favicon & Stylesheets -->
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon/favicon.png') }}" />
    
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/materialdesignicons.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/rtl/core.css') }}" class="template-customizer-core-css" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/rtl/theme-default.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/demo.css') }}" />

    <!-- Dynamic Print Page Size / Orientation -->
    <style id="printOrientationStyle">
        @page {
            size: A4 portrait;
            margin: 8mm 10mm 8mm 10mm;
        }
    </style>

    <style>
        body, .print-container, .bs-table, table, th, td, h4, h5, h6, span, div, p, input, button {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif !important;
        }

        body {
            background-color: #f1f5f9;
            color: #1e293b;
            font-size: 12px;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        .print-container {
            max-width: 880px;
            margin: 20px auto;
            background: #ffffff;
            padding: 26px 32px;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            transition: max-width 0.25s ease-in-out;
        }

        body.is-landscape .print-container {
            max-width: 1080px;
        }

        /* Toolbar */
        .print-toolbar {
            position: sticky;
            top: 15px;
            z-index: 100;
            background: rgba(255, 255, 255, 0.96);
            backdrop-filter: blur(8px);
            padding: 10px 18px;
            border-radius: 8px;
            border: 1px solid #cbd5e1;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.07);
            margin-bottom: 20px;
        }

        /* Financial Statement Table Styling */
        .bs-table {
            width: 100%;
            table-layout: fixed !important;
            border-collapse: collapse;
            font-size: 12px;
        }
        .bs-table td {
            padding: 4px 8px;
            vertical-align: middle;
            line-height: 1.35;
        }
        .bs-sec-header td {
            background-color: #f1f5f9;
            font-weight: 700;
            color: #1e293b;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding-top: 6px;
            padding-bottom: 6px;
            border-top: 1px solid #cbd5e1;
            border-bottom: 1px solid #cbd5e1;
        }
        .bs-group-header td {
            font-weight: 600;
            color: #334155;
            font-size: 11.5px;
            background-color: #fafbfd;
            padding-top: 5px;
            padding-bottom: 4px;
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
            font-size: 11.5px;
            font-weight: 600;
            padding-top: 4.5px;
            padding-bottom: 4.5px;
        }
        .bs-row-highlight td {
            background-color: #f0f7ff;
            border-top: 2px solid #3b82f6;
            border-bottom: 2px solid #3b82f6;
            color: #1e40af !important;
            font-size: 12px;
            font-weight: 700;
            padding-top: 6px;
            padding-bottom: 6px;
        }
        .bs-row-highlight td * {
            color: #1e40af !important;
        }
        .bs-row-grand-total td {
            background-color: #f1f5f9 !important;
            color: #0f172a !important;
            font-weight: 800 !important;
            border-top: 2px solid #0f172a !important;
            border-bottom: 4px double #0f172a !important;
            font-size: 13px;
            padding-top: 8px !important;
            padding-bottom: 8px !important;
        }
        .bs-row-grand-total td * {
            color: #0f172a !important;
        }
        .bs-amount {
            font-variant-numeric: tabular-nums;
            white-space: nowrap;
            text-align: right;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, monospace;
        }

        /* Signature block on screen */
        .signature-section {
            margin-top: 25px;
        }
        .signature-box {
            text-align: center;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            padding: 10px 12px;
            background-color: #fafbfd;
        }
        .signature-space {
            height: 48px;
        }

        /* ==================== PRINT STYLES ==================== */
        @media print {
            html, body {
                width: 100% !important;
                height: auto !important;
                background: #ffffff !important;
                margin: 0 !important;
                padding: 0 !important;
                font-size: 9pt !important;
            }

            .container-fluid {
                padding: 0 !important;
                margin: 0 !important;
                max-width: 100% !important;
                width: 100% !important;
            }

            .print-container {
                max-width: 100% !important;
                width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                box-shadow: none !important;
                border: none !important;
                border-radius: 0 !important;
            }

            .no-print {
                display: none !important;
            }

            /* Letterhead in Print */
            .income-letterhead {
                margin-bottom: 8px !important;
                padding-bottom: 5px !important;
            }
            .income-letterhead h4 {
                font-size: 13pt !important;
                margin-bottom: 2px !important;
            }
            .income-letterhead h5 {
                font-size: 10.5pt !important;
                margin-bottom: 2px !important;
            }
            .income-letterhead p {
                font-size: 8pt !important;
                margin-bottom: 0 !important;
            }

            .card-income {
                border: 1px solid #475569 !important;
                box-shadow: none !important;
                page-break-inside: auto !important;
                break-inside: auto !important;
                border-radius: 4px !important;
            }

            .bs-table {
                font-size: 8.5pt !important;
            }
            .bs-table td {
                padding: 2px 4px !important;
                line-height: 1.25 !important;
            }
            .bs-sec-header td {
                padding-top: 3.5px !important;
                padding-bottom: 3.5px !important;
                font-size: 8pt !important;
                background-color: #e2e8f0 !important;
                color: #000000 !important;
                border-top: 1px solid #64748b !important;
                border-bottom: 1px solid #64748b !important;
            }
            .bs-group-header td {
                padding-top: 2.5px !important;
                padding-bottom: 2px !important;
                font-size: 8pt !important;
                background-color: #f8fafc !important;
                color: #000000 !important;
            }
            .bs-row-subtotal td {
                padding-top: 2px !important;
                padding-bottom: 2px !important;
                font-size: 8pt !important;
                color: #000000 !important;
                border-top: 1px dashed #94a3b8 !important;
                border-bottom: 1px solid #cbd5e1 !important;
            }
            .bs-row-highlight td {
                padding-top: 3.5px !important;
                padding-bottom: 3.5px !important;
                font-size: 8.5pt !important;
                background-color: #f1f5f9 !important;
                color: #000000 !important;
                border-top: 1.5px solid #000000 !important;
                border-bottom: 1.5px solid #000000 !important;
            }
            .bs-row-highlight td * {
                color: #000000 !important;
            }
            .bs-row-grand-total td {
                padding-top: 5px !important;
                padding-bottom: 5px !important;
                font-size: 9.5pt !important;
                background-color: #f1f5f9 !important;
                color: #000000 !important;
                border-top: 2px solid #000000 !important;
                border-bottom: 3.5px double #000000 !important;
            }
            .bs-row-grand-total td * {
                color: #000000 !important;
            }

            /* Signatures in Print */
            .signature-section {
                margin-top: 10px !important;
                padding-top: 0 !important;
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }
            .signature-section .row {
                margin-left: -3px !important;
                margin-right: -3px !important;
            }
            .signature-section .col-4 {
                padding-left: 3px !important;
                padding-right: 3px !important;
            }
            .signature-box {
                padding: 4px 6px !important;
                border: 1px solid #64748b !important;
                background: transparent !important;
                border-radius: 4px !important;
            }
            .signature-space {
                height: 34px !important;
            }
            .signature-box .sig-title {
                font-size: 7.2pt !important;
                font-weight: 700 !important;
                margin-bottom: 1px !important;
            }
            .signature-box .sig-role {
                font-size: 6.8pt !important;
                color: #475569 !important;
            }
            .signature-box .sig-name {
                font-size: 7.2pt !important;
                font-weight: 700 !important;
                padding-top: 2px !important;
            }
            .signature-box .sig-date {
                font-size: 6.5pt !important;
                color: #475569 !important;
            }

            /* Print Footer Note */
            .print-footer-note {
                margin-top: 6px !important;
                padding-top: 3px !important;
                font-size: 6.8pt !important;
            }

            /* Landscape specific */
            body.is-landscape .bs-table {
                font-size: 9pt !important;
            }
            body.is-landscape .bs-table td {
                padding: 2.5px 5px !important;
            }
            body.is-landscape .signature-space {
                height: 38px !important;
            }
        }
    </style>
</head>
<body>

    @php
        $incomeCharge = ($incomeSum ?? 0) - ($chargeSum ?? 0);
        $subtotal = ($poSum ?? 0) - ($modalSum ?? 0); // Laba Kotor
        $operatingProfit = $subtotal - ($expenseSum ?? 0); // Laba Operasi
        $total = $operatingProfit + $incomeCharge; // Laba Bersih
        $grossMargin = ($poSum ?? 0) > 0 ? ($subtotal / $poSum) * 100 : 0;
        $netMargin = ($poSum ?? 0) > 0 ? ($total / $poSum) * 100 : 0;
        $periodStartFormatted = \Carbon\Carbon::parse($startDate)->translatedFormat('d F Y');
        $periodEndFormatted = \Carbon\Carbon::parse($endDate)->translatedFormat('d F Y');
    @endphp

    <div class="container-fluid" style="max-width: 960px;" id="pageWrapper">
        {{-- Floating No-Print Toolbar --}}
        <div class="print-toolbar no-print d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div class="d-flex align-items-center gap-2">
                <i class="mdi mdi-printer-check text-primary fs-3"></i>
                <div>
                    <strong class="d-block text-dark" style="font-size: 13px;">Pratinjau Cetak Laba Rugi (Income Statement)</strong>
                    <span class="text-muted small" style="font-size: 11px;">PT. REFTECH JAYA OPTIMA &bull; {{ $periodStartFormatted }} s/d {{ $periodEndFormatted }}</span>
                </div>
            </div>

            <div class="d-flex flex-wrap align-items-center gap-2">
                {{-- Orientation Switch: Portrait vs Landscape --}}
                <div class="btn-group btn-group-sm" role="group" aria-label="Orientasi Kertas">
                    <button type="button" class="btn btn-outline-secondary active" id="btnPortrait" onclick="setOrientation('portrait')" title="Format Portrait">
                        <i class="mdi mdi-book-open-outline me-1"></i> Portrait
                    </button>
                    <button type="button" class="btn btn-outline-secondary" id="btnLandscape" onclick="setOrientation('landscape')" title="Format Landscape">
                        <i class="mdi mdi-book-open-page-variant-outline me-1"></i> Landscape
                    </button>
                </div>

                <button type="button" class="btn btn-secondary btn-sm" onclick="window.close()">
                    <i class="mdi mdi-close me-1"></i> Tutup
                </button>
                <button type="button" class="btn btn-primary btn-sm shadow-sm fw-bold px-3" onclick="window.print()">
                    <i class="mdi mdi-printer-outline me-1"></i> Cetak / Simpan PDF
                </button>
            </div>
        </div>

        {{-- Main Sheet Container --}}
        <div class="print-container">
            {{-- Letterhead Perusahaan (Simple & Clean) --}}
            <div class="income-letterhead text-center mb-3 pb-3 border-bottom">
                <h4 class="fw-bold text-dark mb-1" style="letter-spacing: 0.5px;">PT. REFTECH JAYA OPTIMA</h4>
                <h5 class="fw-bold text-primary mb-1">Laporan Laba Rugi (Standar)</h5>
                <p class="text-muted mb-0 small">Dari <strong>{{ $periodStartFormatted }}</strong> ke <strong>{{ $periodEndFormatted }}</strong></p>
            </div>

            {{-- Card Table Wrapper --}}
            <div class="card border shadow-none card-income mb-3">
                <div class="card-body p-0">
                    <table class="table table-sm bs-table mb-0">
                        <colgroup>
                            <col class="col-bs-label" style="width: 68%;">
                            <col class="col-bs-amount" style="width: 32%;">
                        </colgroup>
                        <tbody>
                            {{-- ==================== A. PENDAPATAN USAHA ==================== --}}
                            <tr class="bs-sec-header">
                                <td colspan="2">
                                    <i class="mdi mdi-cash-plus me-1"></i> A. PENDAPATAN USAHA (REVENUES)
                                </td>
                            </tr>
                            <tr class="bs-row-item">
                                <td class="ps-4">
                                    <span class="d-inline-flex align-items-center gap-1">
                                        <i class="mdi mdi-file-document-outline text-muted small"></i> Penjualan Produk &amp; Unit PO
                                    </span>
                                </td>
                                <td class="bs-amount">{{ number_format($poSum ?? 0, 0, ',', '.') }}</td>
                            </tr>
                            <tr class="bs-row-item">
                                <td class="ps-4 text-muted">
                                    <span class="d-inline-flex align-items-center gap-1">
                                        <i class="mdi mdi-tag-outline text-muted small"></i> Potongan Penjualan &amp; Retur
                                    </span>
                                </td>
                                <td class="bs-amount text-muted">0</td>
                            </tr>
                            <tr class="bs-row-subtotal">
                                <td class="ps-3 fw-semibold">Jumlah Pendapatan Usaha Bersih</td>
                                <td class="bs-amount fw-semibold text-primary">{{ number_format($poSum ?? 0, 0, ',', '.') }}</td>
                            </tr>

                            {{-- ==================== B. BEBAN POKOK PENJUALAN ==================== --}}
                            <tr class="bs-sec-header">
                                <td colspan="2">
                                    <i class="mdi mdi-cart-arrow-down me-1"></i> B. BEBAN POKOK PENJUALAN (COST OF GOODS SOLD)
                                </td>
                            </tr>
                            <tr class="bs-row-item">
                                <td class="ps-4">
                                    <span class="d-inline-flex align-items-center gap-1">
                                        <i class="mdi mdi-barcode text-muted small"></i> Beban Pokok Barang Terjual (HPP Serial Unit)
                                    </span>
                                </td>
                                <td class="bs-amount text-danger">- {{ number_format($modalSum ?? 0, 0, ',', '.') }}</td>
                            </tr>
                            <tr class="bs-row-subtotal">
                                <td class="ps-3 fw-semibold text-danger">Jumlah Beban Pokok Penjualan (HPP)</td>
                                <td class="bs-amount fw-semibold text-danger">- {{ number_format($modalSum ?? 0, 0, ',', '.') }}</td>
                            </tr>

                            {{-- ==================== LABA KOTOR ==================== --}}
                            <tr class="bs-row-highlight">
                                <td class="fw-bold">
                                    <i class="mdi mdi-chart-bell-curve me-1 text-primary"></i> LABA KOTOR (GROSS PROFIT)
                                    <span class="badge bg-label-primary rounded-pill ms-2 small" style="font-size: 10px;">Margin: {{ number_format($grossMargin, 1) }}%</span>
                                </td>
                                <td class="bs-amount fw-bold fs-6 text-primary">{{ number_format($subtotal, 0, ',', '.') }}</td>
                            </tr>

                            {{-- ==================== C. BEBAN OPERASIONAL ==================== --}}
                            <tr class="bs-sec-header">
                                <td colspan="2">
                                    <i class="mdi mdi-office-building-cog me-1"></i> C. BEBAN OPERASIONAL (OPERATING EXPENSES)
                                </td>
                            </tr>
                            @forelse ($allExpense as $item)
                                <tr class="bs-row-item">
                                    <td class="ps-4">
                                        <span class="d-inline-flex align-items-center gap-1">
                                            <i class="mdi mdi-chevron-right text-muted small"></i> {{ optional($item->account)->name ?? $item->description ?? 'Beban Operasional' }}
                                        </span>
                                    </td>
                                    <td class="bs-amount text-danger">- {{ number_format($item->amount, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr class="bs-row-item">
                                    <td class="ps-4 text-muted">- Tidak ada beban operasional tercatat pada periode ini -</td>
                                    <td class="bs-amount text-muted">0</td>
                                </tr>
                            @endforelse
                            <tr class="bs-row-subtotal">
                                <td class="ps-3 fw-semibold text-danger">Jumlah Beban Operasional</td>
                                <td class="bs-amount fw-semibold text-danger">- {{ number_format($expenseSum ?? 0, 0, ',', '.') }}</td>
                            </tr>

                            {{-- ==================== LABA OPERASIONAL ==================== --}}
                            <tr class="bs-row-subtotal" style="background-color: #edf2f7; border-top: 1.5px solid #cbd5e1; border-bottom: 1.5px solid #cbd5e1;">
                                <td class="fw-bold text-dark ps-3">
                                    <i class="mdi mdi-equal-box me-1 text-secondary"></i> LABA / (RUGI) OPERASIONAL (OPERATING INCOME)
                                </td>
                                <td class="bs-amount fw-bold {{ $operatingProfit >= 0 ? 'text-dark' : 'text-danger' }}">
                                    {{ $operatingProfit < 0 ? '- ' : '' }}{{ number_format(abs($operatingProfit), 0, ',', '.') }}
                                </td>
                            </tr>

                            {{-- ==================== D. PENDAPATAN & BEBAN LAIN-LAIN ==================== --}}
                            <tr class="bs-sec-header">
                                <td colspan="2">
                                    <i class="mdi mdi-swap-horizontal me-1"></i> D. PENDAPATAN &amp; BEBAN LAIN-LAIN (OTHER INCOME &amp; CHARGES)
                                </td>
                            </tr>

                            {{-- D.1 Pendapatan Lain-lain --}}
                            <tr class="bs-group-header">
                                <td colspan="2">1. Pendapatan Lain-lain (Other Income)</td>
                            </tr>
                            @forelse ($allIncome as $item)
                                <tr class="bs-row-item">
                                    <td class="ps-4">
                                        <span class="d-inline-flex align-items-center gap-1">
                                            <i class="mdi mdi-plus-circle-outline text-success small"></i> {{ $item->description }}
                                        </span>
                                    </td>
                                    <td class="bs-amount text-success">{{ number_format($item->amount, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr class="bs-row-item">
                                    <td class="ps-4 text-muted">- Tidak ada pendapatan lain-lain -</td>
                                    <td class="bs-amount text-muted">0</td>
                                </tr>
                            @endforelse
                            <tr class="bs-row-subtotal">
                                <td class="ps-3 fw-semibold">Jumlah Pendapatan Lain-lain</td>
                                <td class="bs-amount fw-semibold text-success">{{ number_format($incomeSum ?? 0, 0, ',', '.') }}</td>
                            </tr>

                            {{-- D.2 Beban Lain-lain --}}
                            <tr class="bs-group-header">
                                <td colspan="2">2. Beban Lain-lain (Other Charges)</td>
                            </tr>
                            @forelse ($allCharge as $item)
                                <tr class="bs-row-item">
                                    <td class="ps-4">
                                        <span class="d-inline-flex align-items-center gap-1">
                                            <i class="mdi mdi-minus-circle-outline text-danger small"></i> {{ $item->description }}
                                        </span>
                                    </td>
                                    <td class="bs-amount text-danger">- {{ number_format($item->amount, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr class="bs-row-item">
                                    <td class="ps-4 text-muted">- Tidak ada beban lain-lain -</td>
                                    <td class="bs-amount text-muted">0</td>
                                </tr>
                            @endforelse
                            <tr class="bs-row-subtotal">
                                <td class="ps-3 fw-semibold text-danger">Jumlah Beban Lain-lain</td>
                                <td class="bs-amount fw-semibold text-danger">- {{ number_format($chargeSum ?? 0, 0, ',', '.') }}</td>
                            </tr>

                            <tr class="bs-row-subtotal" style="border-top: 1px solid #cbd5e1;">
                                <td class="ps-3 fw-semibold">Total Pendapatan (Beban) Lain-lain Bersih</td>
                                <td class="bs-amount fw-semibold {{ $incomeCharge >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ $incomeCharge < 0 ? '- ' : '' }}{{ number_format(abs($incomeCharge), 0, ',', '.') }}
                                </td>
                            </tr>

                            {{-- ==================== GRAND TOTAL: LABA (RUGI) BERSIH ==================== --}}
                            <tr class="bs-row-grand-total">
                                <td class="fw-bolder">
                                    <i class="mdi mdi-check-decagram me-1 text-primary"></i> JUMLAH LABA / (RUGI) BERSIH PERIODE BERJALAN
                                    <span class="badge bg-label-success rounded-pill ms-2 small" style="font-size: 10px;">Net Margin: {{ number_format($netMargin, 1) }}%</span>
                                </td>
                                <td class="bs-amount fw-bolder fs-6">
                                    {{ $total < 0 ? '- ' : '' }}Rp {{ number_format(abs($total), 0, ',', '.') }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Signature Approval Section (3 Tanda Tangan Resmi Korporat) --}}
            <div class="signature-section mt-4 pt-1">
                <div class="row g-2">
                    <div class="col-4">
                        <div class="signature-box">
                            <div class="fw-bold text-muted small text-uppercase sig-title">Disiapkan Oleh:</div>
                            <div class="text-muted sig-role">Staff Accounting &amp; Finance</div>
                            <div class="signature-space"></div>
                            <div class="fw-bold text-dark border-top pt-1 sig-name">( .................................................. )</div>
                            <div class="text-muted small sig-date">Tanggal: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/ {{ \Carbon\Carbon::parse($endDate)->format('Y') }}</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="signature-box">
                            <div class="fw-bold text-muted small text-uppercase sig-title">Diperiksa Oleh:</div>
                            <div class="text-muted sig-role">Finance &amp; Accounting Manager</div>
                            <div class="signature-space"></div>
                            <div class="fw-bold text-dark border-top pt-1 sig-name">( .................................................. )</div>
                            <div class="text-muted small sig-date">Tanggal: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/ {{ \Carbon\Carbon::parse($endDate)->format('Y') }}</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="signature-box">
                            <div class="fw-bold text-muted small text-uppercase sig-title">Disetujui Oleh:</div>
                            <div class="text-muted sig-role">Direktur Utama</div>
                            <div class="signature-space"></div>
                            <div class="fw-bold text-dark border-top pt-1 sig-name">( .................................................. )</div>
                            <div class="text-muted small sig-date">Tanggal: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/ {{ \Carbon\Carbon::parse($endDate)->format('Y') }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Print Footer Note --}}
            <div class="print-footer-note mt-3 pt-2 border-top d-flex justify-content-between text-muted small">
                <span>Dicetak pada: {{ now()->translatedFormat('d F Y, H:i') }} WIB &bull; Oleh: {{ Auth::user()->name ?? 'Finance' }}</span>
                <span>PT. Reftech Jaya Optima &bull; Laporan Laba Rugi Komprehensif (Income Statement)</span>
            </div>
        </div>
    </div>

    <script>
        function setOrientation(orientation) {
            const style = document.getElementById('printOrientationStyle');
            const btnPortrait = document.getElementById('btnPortrait');
            const btnLandscape = document.getElementById('btnLandscape');
            const pageWrapper = document.getElementById('pageWrapper');

            if (orientation === 'landscape') {
                style.textContent = '@page { size: A4 landscape; margin: 8mm 10mm 8mm 10mm; }';
                document.body.classList.add('is-landscape');
                btnLandscape.classList.add('active');
                btnPortrait.classList.remove('active');
                if (pageWrapper) pageWrapper.style.maxWidth = '1120px';
            } else {
                style.textContent = '@page { size: A4 portrait; margin: 8mm 10mm 8mm 10mm; }';
                document.body.classList.remove('is-landscape');
                btnPortrait.classList.add('active');
                btnLandscape.classList.remove('active');
                if (pageWrapper) pageWrapper.style.maxWidth = '960px';
            }
        }
    </script>
</body>
</html>
