<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Neraca - PT REFTECH JAYA OPTIMA ({{ $startString }} s/d {{ $endString }})</title>

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
            margin: 6mm 8mm 6mm 8mm;
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
            max-width: 960px;
            margin: 20px auto;
            background: #ffffff;
            padding: 24px 28px;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            transition: max-width 0.25s ease-in-out;
        }

        body.is-landscape .print-container {
            max-width: 1140px;
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
            height: 50px;
        }

        /* Grand Total styling on screen */
        .bs-row-grand-total td {
            background-color: #f1f5f9 !important;
            color: #0f172a !important;
            font-weight: 800 !important;
            border-top: 2px solid #0f172a !important;
            border-bottom: 4px double #0f172a !important;
        }
        .bs-row-grand-total td * {
            color: #0f172a !important;
        }

        /* ==================== PRINT STYLES (ANTI-CLIPPING & CLEAN PAGE FIT) ==================== */
        @media print {
            html, body {
                width: 100% !important;
                height: auto !important;
                background: #ffffff !important;
                margin: 0 !important;
                padding: 0 !important;
                font-size: 8.5pt !important;
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
            .balance-letterhead {
                margin-bottom: 6px !important;
                padding-bottom: 4px !important;
            }
            .balance-letterhead h4 {
                font-size: 11.5pt !important;
                margin-bottom: 1px !important;
            }
            .balance-letterhead h5 {
                font-size: 9.5pt !important;
                margin-bottom: 1px !important;
            }
            .balance-letterhead p {
                font-size: 7.5pt !important;
                margin-bottom: 0 !important;
            }

            /* 2-Column Side by Side Layout */
            .balance-sheet-grid {
                display: flex !important;
                flex-wrap: wrap !important;
                margin-left: -3px !important;
                margin-right: -3px !important;
            }
            .balance-sheet-grid .balance-col {
                flex: 0 0 50% !important;
                max-width: 50% !important;
                width: 50% !important;
                padding-left: 3px !important;
                padding-right: 3px !important;
            }
            .balance-sheet-grid.mode-stafel .balance-col {
                flex: 0 0 100% !important;
                max-width: 100% !important;
                width: 100% !important;
                padding-left: 0 !important;
                padding-right: 0 !important;
            }

            /* Cards */
            .balance-card {
                border: 1px solid #475569 !important;
                box-shadow: none !important;
                page-break-inside: auto !important;
                break-inside: auto !important;
                border-radius: 4px !important;
                height: auto !important;
            }
            .balance-card .card-header {
                padding: 3.5px 6px !important;
                font-size: 8.5pt !important;
                font-weight: 700 !important;
                background-color: #f1f5f9 !important;
                color: #000000 !important;
                border-bottom: 1px solid #64748b !important;
            }
            .balance-card .card-header .badge {
                border: 1px solid #475569 !important;
                color: #000000 !important;
                background: transparent !important;
                font-size: 7.5pt !important;
                padding: 1px 5px !important;
            }

            /* Table and Columns Layout */
            .bs-table {
                table-layout: fixed !important;
                width: 100% !important;
                font-size: 8.2pt !important;
            }
            .bs-table col.col-bs-label {
                width: 63% !important;
            }
            .bs-table col.col-bs-amount {
                width: 37% !important;
            }
            .bs-table td {
                padding: 1.5px 3.5px !important;
                line-height: 1.22 !important;
                vertical-align: middle !important;
            }
            .bs-table td.bs-amount {
                text-align: right !important;
                white-space: nowrap !important;
                font-variant-numeric: tabular-nums !important;
                letter-spacing: -0.3px !important;
                font-weight: 500 !important;
            }

            /* Row styles */
            .bs-sec-header td {
                padding-top: 3.5px !important;
                padding-bottom: 3.5px !important;
                font-size: 8pt !important;
                font-weight: 700 !important;
                background-color: #e2e8f0 !important;
                color: #000000 !important;
                border-top: 1px solid #64748b !important;
                border-bottom: 1px solid #64748b !important;
            }
            .bs-group-header td {
                padding-top: 2px !important;
                padding-bottom: 1.5px !important;
                font-size: 7.8pt !important;
                font-weight: 600 !important;
                background-color: #f8fafc !important;
                color: #000000 !important;
            }
            .bs-row-subtotal td {
                padding-top: 2px !important;
                padding-bottom: 2px !important;
                font-size: 8pt !important;
                font-weight: 600 !important;
                border-top: 1px dashed #94a3b8 !important;
                border-bottom: 1px solid #cbd5e1 !important;
                color: #000000 !important;
            }
            .bs-row-total-cat td {
                padding-top: 3.5px !important;
                padding-bottom: 3.5px !important;
                font-size: 8.3pt !important;
                font-weight: 700 !important;
                background-color: #edf2f7 !important;
                color: #000000 !important;
                border-top: 1.5px solid #000000 !important;
                border-bottom: 1.5px solid #000000 !important;
            }
            .bs-row-grand-total td {
                padding-top: 4px !important;
                padding-bottom: 4px !important;
                font-size: 8.8pt !important;
                font-weight: 800 !important;
                background-color: #f1f5f9 !important;
                color: #000000 !important;
                border-top: 2px solid #000000 !important;
                border-bottom: 3.5px double #000000 !important;
            }
            .bs-row-grand-total td * {
                color: #000000 !important;
            }

            /* Spacer row inside pasiva table */
            .bs-spacer-row {
                display: none !important;
            }

            /* Signatures in Print */
            .signature-section {
                margin-top: 8px !important;
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
                height: 32px !important;
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

            /* Landscape specific adjustments */
            body.is-landscape .bs-table {
                font-size: 8.6pt !important;
            }
            body.is-landscape .bs-table td {
                padding: 2px 4.5px !important;
            }
            body.is-landscape .signature-space {
                height: 36px !important;
            }
            body.is-landscape .signature-section {
                margin-top: 10px !important;
            }
        }
    </style>
</head>
<body>

    <div class="container-fluid" style="max-width: 1020px;" id="pageWrapper">
        {{-- Floating No-Print Toolbar --}}
        <div class="print-toolbar no-print d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div class="d-flex align-items-center gap-2">
                <i class="mdi mdi-printer-check text-primary fs-3"></i>
                <div>
                    <strong class="d-block text-dark" style="font-size: 13px;">Pratinjau Cetak Neraca (Balance Sheet)</strong>
                    <span class="text-muted small" style="font-size: 11px;">PT. REFTECH JAYA OPTIMA &bull; {{ $startString }} s/d {{ $endString }}</span>
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

                {{-- Layout Mode: Skontro (2 Kolom) vs Stafel (1 Kolom) --}}
                <div class="btn-group btn-group-sm" role="group" aria-label="Format Kolom">
                    <button type="button" class="btn btn-outline-secondary active" id="btnSkontro" onclick="togglePrintLayout('skontro')" title="2 Kolom Side-by-Side">
                        <i class="mdi mdi-view-column-outline me-1"></i> 2 Kolom
                    </button>
                    <button type="button" class="btn btn-outline-secondary" id="btnStafel" onclick="togglePrintLayout('stafel')" title="1 Kolom Bertumpuk">
                        <i class="mdi mdi-view-agenda-outline me-1"></i> 1 Kolom
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
            {{-- Include Shared Financial Report Template --}}
            @include('pages.finance.balance._report', ['isPrintMode' => true])

            {{-- Signature Approval Section (3 Tanda Tangan Resmi) --}}
            <div class="signature-section mt-4 pt-1">
                <div class="row g-2">
                    <div class="col-4">
                        <div class="signature-box">
                            <div class="fw-bold text-muted small text-uppercase sig-title">Disiapkan Oleh:</div>
                            <div class="text-muted sig-role">Staff Accounting &amp; Finance</div>
                            <div class="signature-space"></div>
                            <div class="fw-bold text-dark border-top pt-1 sig-name">( .................................................. )</div>
                            <div class="text-muted small sig-date">Tanggal: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/ 2026</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="signature-box">
                            <div class="fw-bold text-muted small text-uppercase sig-title">Diperiksa Oleh:</div>
                            <div class="text-muted sig-role">Finance &amp; Accounting Manager</div>
                            <div class="signature-space"></div>
                            <div class="fw-bold text-dark border-top pt-1 sig-name">( .................................................. )</div>
                            <div class="text-muted small sig-date">Tanggal: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/ 2026</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="signature-box">
                            <div class="fw-bold text-muted small text-uppercase sig-title">Disetujui Oleh:</div>
                            <div class="text-muted sig-role">Direktur Utama</div>
                            <div class="signature-space"></div>
                            <div class="fw-bold text-dark border-top pt-1 sig-name">( .................................................. )</div>
                            <div class="text-muted small sig-date">Tanggal: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/ 2026</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Footer Note --}}
            <div class="print-footer-note mt-3 pt-2 border-top d-flex justify-content-between text-muted small">
                <span>Dicetak pada: {{ now()->translatedFormat('d F Y, H:i') }} WIB &bull; Oleh: {{ Auth::user()->name ?? 'Finance' }}</span>
                <span>PT. Reftech Jaya Optima &bull; Laporan Posisi Keuangan (Neraca)</span>
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
                style.textContent = '@page { size: A4 landscape; margin: 6mm 8mm 6mm 8mm; }';
                document.body.classList.add('is-landscape');
                btnLandscape.classList.add('active');
                btnPortrait.classList.remove('active');
                if (pageWrapper) pageWrapper.style.maxWidth = '1180px';
            } else {
                style.textContent = '@page { size: A4 portrait; margin: 6mm 8mm 6mm 8mm; }';
                document.body.classList.remove('is-landscape');
                btnPortrait.classList.add('active');
                btnLandscape.classList.remove('active');
                if (pageWrapper) pageWrapper.style.maxWidth = '1020px';
            }
        }

        function togglePrintLayout(mode) {
            const grid = document.getElementById('balanceSheetGrid');
            const colAktiva = document.getElementById('colAktiva');
            const colPasiva = document.getElementById('colPasiva');
            const btnSkontro = document.getElementById('btnSkontro');
            const btnStafel = document.getElementById('btnStafel');

            if (!colAktiva || !colPasiva) return;

            if (mode === 'stafel') {
                if (grid) grid.classList.add('mode-stafel');
                colAktiva.className = 'col-12 balance-col mb-3';
                colPasiva.className = 'col-12 balance-col';
                btnStafel.classList.add('active');
                btnSkontro.classList.remove('active');
            } else {
                if (grid) grid.classList.remove('mode-stafel');
                colAktiva.className = 'col-md-6 balance-col';
                colPasiva.className = 'col-md-6 balance-col';
                btnSkontro.classList.add('active');
                btnStafel.classList.remove('active');
            }
        }
    </script>
</body>
</html>
