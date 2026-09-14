@php
    $periodYear = $year ?? \Carbon\Carbon::parse($startDate)->year;
    $detailUrl = @$month
        ? route('expense-equity.detail-bulan', [$periodYear, str_pad($month, 2, '0', STR_PAD_LEFT)])
        : route('expense-equity.detail-tahun', [$periodYear]);
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Perubahan Modal - PT REFTECH JAYA OPTIMA ({{ $startString }} s/d {{ $endString }})</title>

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
        body, .print-container, .eq-table, table, th, td, h4, h5, h6, span, div, p, input, button {
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
            max-width: 920px;
            margin: 20px auto;
            background: #ffffff;
            padding: 28px 32px;
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

        @media print {
            body {
                background: #ffffff !important;
                padding: 0 !important;
            }
            .print-toolbar, .no-print {
                display: none !important;
            }
            .print-container {
                max-width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                box-shadow: none !important;
                border-radius: 0 !important;
            }
            table {
                page-break-inside: auto;
            }
            tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }
            .signature-section {
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>

    <div class="container-fluid" style="max-width: 960px;">
        {{-- Floating Toolbar (Screen only) --}}
        <div class="print-toolbar d-flex flex-wrap align-items-center justify-content-between gap-2 no-print">
            <div class="d-flex align-items-center gap-2">
                <a href="{{ $detailUrl }}" class="btn btn-sm btn-label-secondary">
                    <i class="mdi mdi-arrow-left me-1"></i> Kembali ke Detail
                </a>
                <span class="badge bg-label-primary fw-bold">Perubahan Modal (Equity)</span>
            </div>

            <div class="d-flex align-items-center gap-2">
                {{-- Orientation Switcher --}}
                <div class="btn-group btn-group-sm" role="group" aria-label="Print Orientation">
                    <button type="button" class="btn btn-outline-primary active" id="btnPortrait" onclick="setOrientation('portrait')">
                        <i class="mdi mdi-file-document-outline me-1"></i> Portrait
                    </button>
                    <button type="button" class="btn btn-outline-primary" id="btnLandscape" onclick="setOrientation('landscape')">
                        <i class="mdi mdi-file-document-outline me-1" style="transform: rotate(90deg);"></i> Landscape
                    </button>
                </div>

                {{-- Action Buttons --}}
                <button type="button" class="btn btn-sm btn-primary shadow-xs" onclick="window.print()">
                    <i class="mdi mdi-printer me-1"></i> Cetak Dokumen
                </button>
                <button type="button" class="btn btn-sm btn-label-secondary" onclick="window.close()">
                    <i class="mdi mdi-close"></i>
                </button>
            </div>
        </div>

        {{-- Document Container --}}
        <div class="print-container">
            @include('pages.finance.equity._report', ['isPrintMode' => true])

            <div class="text-center text-muted small mt-4 pt-2 border-top no-print" style="font-size: 11px;">
                Dokumen Resmi Finansial &copy; {{ date('Y') }} PT. Reftech Jaya Optima. Dicetak pada {{ now()->translatedFormat('d F Y, H:i') }}.
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script>
        function setOrientation(orientation) {
            const style = document.getElementById('printOrientationStyle');
            const btnPortrait = document.getElementById('btnPortrait');
            const btnLandscape = document.getElementById('btnLandscape');

            if (orientation === 'landscape') {
                style.innerHTML = '@page { size: A4 landscape; margin: 8mm 10mm 8mm 10mm; }';
                document.body.classList.add('is-landscape');
                btnLandscape.classList.add('active');
                btnPortrait.classList.remove('active');
            } else {
                style.innerHTML = '@page { size: A4 portrait; margin: 8mm 10mm 8mm 10mm; }';
                document.body.classList.remove('is-landscape');
                btnPortrait.classList.add('active');
                btnLandscape.classList.remove('active');
            }
        }
    </script>
</body>
</html>
