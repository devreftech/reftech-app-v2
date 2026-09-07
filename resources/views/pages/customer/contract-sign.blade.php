@php
    $isSigned   = $contract->isSignedByCustomer();
    $sellcon    = $contract;
    $docHeading = $sellcon->type == 'Order' ? 'CONFIRM ORDER' : 'SELLING CONTRACT';
    $docNoun    = $sellcon->type == 'Order' ? 'Confirm Order' : 'Selling Contract';

    if ($isUnit) {
        $isKojisha  = $unitQuote->isKojisha();
        $entityName = $isKojisha ? 'PT Kojisha Innotiv Indonesia' : 'PT Reftech Jaya Optima';
        $hasTax     = (bool) $unitQuote->tax;
        $client     = $unitQuote->client;
        $pic        = $unitQuote->pic;
        $sales      = $unitQuote->sales;
        $afterDisc  = $unitQuote->diskon > 0 ? $unitQuote->subtotal - $unitQuote->discount_amount : $unitQuote->subtotal;
    } else {
        $isKojisha  = ($quote->pic?->client?->info ?? '') === 'Kojisha' || $sellcon->type == 'Order';
        $entityName = $isKojisha ? 'PT Kojisha Innotiv Indonesia' : 'PT Reftech Jaya Optima';
        $hasTax     = $quote->tax != '0';
        $client     = $quote->pic?->client;
        $pic        = $quote->pic;
        $sales      = $quote->sales;
        $afterDisc  = $quote->diskon > 0 ? $quote->subtotal - $quote->diskon : $quote->subtotal;
        $vat        = $quote->tax != 0 ? ($afterDisc * $quote->tax) / 100 : 0;
    }
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $docHeading }} — {{ $sellcon->no_contract ?: 'Contract #' . $sellcon->id }}</title>

    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@mdi/font@7.4.47/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/core.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.css">

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
            font-size: 12px;
            line-height: 1.45;
            -webkit-font-smoothing: antialiased;
            padding-bottom: 60px;
        }

        /* Top Sticky Navbar */
        .portal-navbar {
            position: sticky;
            top: 0;
            z-index: 1000;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid #e2e8f0;
            padding: 12px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
        }

        .portal-navbar .nav-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .portal-navbar .doc-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 4px 10px;
            font-size: 11.5px;
            font-weight: 700;
            border-radius: 9999px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .badge-pending {
            background-color: #fef3c7;
            color: #92400e;
        }

        .badge-signed {
            background-color: #dcfce7;
            color: #166534;
        }

        .portal-navbar .doc-info-title {
            font-size: 14px;
            font-weight: 700;
            color: #0f172a;
        }

        .portal-navbar .doc-info-subtitle {
            font-size: 11.5px;
            color: #64748b;
        }

        .portal-navbar .nav-actions {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .btn-portal {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            font-size: 13px;
            font-weight: 600;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s ease;
            border: 1px solid transparent;
        }

        .btn-portal-primary {
            background-color: #0284c7;
            color: #ffffff;
        }

        .btn-portal-primary:hover {
            background-color: #0369a1;
        }

        .btn-portal-success {
            background-color: #16a34a;
            color: #ffffff;
        }

        .btn-portal-success:hover {
            background-color: #15803d;
        }

        .btn-portal-outline {
            background-color: #ffffff;
            border-color: #cbd5e1;
            color: #334155;
        }

        .btn-portal-outline:hover {
            background-color: #f8fafc;
            border-color: #94a3b8;
        }

        .btn-portal-danger {
            background-color: #fff1f2;
            border-color: #fecdd3;
            color: #e11d48;
        }

        .btn-portal-danger:hover {
            background-color: #ffe4e6;
            border-color: #fda4af;
            color: #be123c;
        }

        /* Container Layout */
        .portal-container {
            max-width: 210mm;
            margin: 24px auto;
            padding: 0 16px;
        }

        /* Printable Sheet Canvas - Identical to detail-print */
        .contract-sheet {
            width: 100%;
            min-height: 297mm;
            margin: 0 auto;
            background: #ffffff;
            padding: 14mm 16mm;
            border: 1px solid #cbd5e1;
            border-radius: 4px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
            position: relative;
        }

        /* Header */
        .doc-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding-bottom: 12px;
            border-bottom: 2px solid #0f172a;
        }

        .brand-info {
            max-width: 60%;
        }

        .brand-logo img {
            max-height: 48px;
            width: auto;
            object-fit: contain;
        }

        .brand-name {
            font-weight: 700;
            font-size: 14px;
            color: #0f172a;
            margin-top: 4px;
        }

        .brand-address {
            font-size: 11px;
            color: #475569;
            line-height: 1.5;
            margin-top: 2px;
        }

        .doc-title-block {
            text-align: right;
        }

        .doc-title {
            font-size: 20px;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            margin-bottom: 2px;
        }

        .doc-number {
            font-size: 13px;
            font-weight: 700;
            color: #0284c7;
        }

        .doc-date {
            font-size: 11px;
            color: #64748b;
            margin-top: 2px;
        }

        /* Info Section (Quote To & Order Info) */
        .info-section {
            display: flex;
            gap: 14px;
            margin: 14px 0;
        }

        .info-card {
            flex: 1;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 10px 14px;
        }

        .info-card-title {
            font-size: 10.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #0284c7;
            margin-bottom: 6px;
            padding-bottom: 4px;
            border-bottom: 1px solid #e2e8f0;
        }

        .info-card-company {
            font-size: 13px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 4px;
        }

        .info-row {
            display: flex;
            font-size: 11px;
            line-height: 1.5;
            color: #334155;
            margin-bottom: 2px;
        }

        .info-row .label {
            width: 75px;
            color: #64748b;
            flex-shrink: 0;
        }

        .info-row .value {
            font-weight: 500;
        }

        /* Items Table */
        .table-responsive-wrapper {
            width: 100%;
            overflow-x: auto;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 14px 0 10px 0;
            font-size: 11.5px;
        }

        .items-table thead th {
            background-color: #f1f5f9;
            color: #334155;
            font-weight: 700;
            font-size: 10.5px;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            padding: 8px 10px;
            border-top: 1px solid #cbd5e1;
            border-bottom: 2px solid #cbd5e1;
        }

        .items-table tbody td {
            padding: 8px 10px;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: top;
        }

        .items-table .subtitle-row td {
            background-color: #f1f5f9;
            font-weight: 700;
            color: #0f172a;
            padding: 6px 10px;
        }

        .items-table .item-title {
            font-weight: 700;
            color: #0f172a;
            font-size: 12px;
            margin-bottom: 2px;
        }

        .items-table .item-desc {
            font-size: 10.5px;
            color: #475569;
            line-height: 1.45;
            white-space: pre-wrap;
        }

        .items-table .spec-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 2px 10px;
            margin-top: 3px;
            font-size: 10px;
            color: #334155;
        }

        /* Totals Block (Right Aligned) */
        .totals-section {
            display: flex;
            justify-content: flex-end;
            margin-top: 4px;
            margin-bottom: 12px;
        }

        .totals-table {
            width: 300px;
            border-collapse: collapse;
            font-size: 11.5px;
        }

        .totals-table td {
            padding: 3px 0;
        }

        .totals-table .label {
            color: #64748b;
        }

        .totals-table .val {
            text-align: right;
            font-weight: 600;
            color: #0f172a;
        }

        .totals-table .grand-total-row td {
            padding-top: 8px;
            border-top: 1.5px solid #0f172a;
            font-weight: 800;
            font-size: 13px;
            color: #0f172a;
        }

        .totals-table .grand-total-row .val {
            font-size: 14px;
            color: #0284c7;
        }

        /* Single Term & Condition Card */
        .terms-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            margin-top: 12px;
            overflow: hidden;
        }

        .terms-card-header {
            background: #f1f5f9;
            padding: 6px 12px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #0f172a;
            border-bottom: 1px solid #e2e8f0;
        }

        .terms-card-body {
            padding: 8px 12px;
            display: flex;
            flex-direction: column;
            gap: 3px;
        }

        .term-row {
            display: flex;
            font-size: 11px;
            line-height: 1.5;
            color: #334155;
        }

        .term-row .term-label {
            width: 140px;
            color: #64748b;
            flex-shrink: 0;
        }

        .term-row .term-sep {
            width: 14px;
            color: #64748b;
            flex-shrink: 0;
        }

        .term-row .term-val {
            font-weight: 500;
            color: #0f172a;
        }

        /* Thank you note below */
        .business-thanks-note {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-left: 3px solid #0284c7;
            border-radius: 4px;
            padding: 8px 12px;
            margin-top: 12px;
            font-size: 11px;
            color: #475569;
        }

        .business-thanks-note .thanks-title {
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 2px;
        }

        .business-thanks-note .thanks-desc {
            line-height: 1.4;
        }

        /* Signatures Section */
        .signature-section {
            display: flex;
            justify-content: space-between;
            margin-top: 24px;
            page-break-inside: avoid;
        }

        .signature-box {
            width: 45%;
            text-align: center;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 10px 14px;
        }

        .signature-label {
            font-size: 11.5px;
            font-weight: 600;
            color: #475569;
            margin-bottom: 6px;
        }

        .signature-img-wrap {
            height: 65px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 4px 0;
        }

        .signature-img-wrap img {
            max-height: 60px;
            max-width: 140px;
            object-fit: contain;
        }

        .signature-name {
            font-size: 12px;
            font-weight: 700;
            color: #0f172a;
            border-top: 1px solid #cbd5e1;
            padding-top: 4px;
            margin-top: 4px;
        }

        .signature-role {
            font-size: 10.5px;
            color: #64748b;
        }

        /* Signature Action Card (Portal Interactive) */
        .sign-action-card {
            background: #ffffff;
            border: 2px solid #0284c7;
            border-radius: 10px;
            box-shadow: 0 8px 30px rgba(2, 132, 199, 0.12);
            padding: 24px;
            margin-top: 24px;
        }

        .sign-card-title {
            font-size: 16px;
            font-weight: 800;
            color: #0f172a;
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 4px;
        }

        .sign-card-subtitle {
            font-size: 12px;
            color: #64748b;
            margin-bottom: 16px;
        }

        .canvas-container {
            position: relative;
            background: #ffffff;
            border: 2px dashed #94a3b8;
            border-radius: 8px;
            width: 100%;
            height: 200px;
            touch-action: none;
            margin-bottom: 8px;
            overflow: hidden;
        }

        .canvas-container.has-drawn {
            border-style: solid;
            border-color: #0284c7;
        }

        #signature-pad {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            cursor: crosshair;
        }

        .canvas-placeholder-hint {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: #94a3b8;
            font-size: 13px;
            font-weight: 500;
            pointer-events: none;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .canvas-tools {
            display: flex;
            justify-content: flex-end;
            gap: 8px;
            margin-bottom: 16px;
        }

        .btn-canvas-tool {
            background: #f1f5f9;
            border: 1px solid #cbd5e1;
            padding: 4px 10px;
            font-size: 11.5px;
            font-weight: 600;
            color: #475569;
            border-radius: 4px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .btn-canvas-tool:hover {
            background: #e2e8f0;
            color: #0f172a;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-bottom: 16px;
        }

        .form-group label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            color: #334155;
            margin-bottom: 4px;
        }

        .form-group input {
            width: 100%;
            padding: 8px 12px;
            font-size: 13px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            outline: none;
            transition: border-color 0.2s;
        }

        .form-group input:focus {
            border-color: #0284c7;
            box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.15);
        }

        .agreement-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 10px 12px;
            margin-bottom: 16px;
            display: flex;
            align-items: flex-start;
            gap: 8px;
            font-size: 11.5px;
            color: #334155;
            line-height: 1.4;
        }

        .agreement-box input[type="checkbox"] {
            margin-top: 2px;
            cursor: pointer;
        }

        .btn-submit-sign {
            width: 100%;
            background: #0284c7;
            color: #ffffff;
            font-size: 14px;
            font-weight: 700;
            padding: 12px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.2s;
            box-shadow: 0 4px 12px rgba(2, 132, 199, 0.25);
        }

        .btn-submit-sign:hover {
            background: #0369a1;
        }

        .btn-submit-sign:disabled {
            background: #94a3b8;
            cursor: not-allowed;
            box-shadow: none;
        }

        /* Success Card (When already signed) */
        .success-signed-card {
            background: #f0fdf4;
            border: 1.5px solid #86efac;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            margin-top: 24px;
        }

        .success-signed-icon {
            width: 48px;
            height: 48px;
            background: #dcfce7;
            color: #16a34a;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
            margin-bottom: 8px;
        }

        .success-signed-title {
            font-size: 16px;
            font-weight: 800;
            color: #166534;
            margin-bottom: 4px;
        }

        .success-signed-desc {
            font-size: 12px;
            color: #374151;
            line-height: 1.5;
            max-width: 500px;
            margin: 0 auto 16px auto;
        }

        /* Responsive Breakpoints */
        @media (max-width: 768px) {
            body {
                padding-bottom: 40px;
            }

            .portal-container {
                padding: 0 8px;
                margin: 12px auto;
            }

            .contract-sheet {
                padding: 12px 10px;
            }

            .doc-header {
                flex-direction: column;
                gap: 10px;
            }

            .brand-info {
                max-width: 100%;
            }

            .doc-title-block {
                text-align: left;
            }

            .info-section {
                flex-direction: column;
                gap: 10px;
            }

            .form-grid {
                grid-template-columns: 1fr;
            }

            .signature-section {
                flex-direction: column;
                gap: 12px;
            }

            .signature-box {
                width: 100%;
            }

            .totals-table {
                width: 100%;
            }
        }

        /* Print Media Styles */
        @media print {
            body {
                background: #ffffff;
                padding: 0;
                font-size: 11px;
            }

            .portal-navbar, #section-sign {
                display: none !important;
            }

            .portal-container {
                max-width: 100%;
                margin: 0;
                padding: 0;
            }

            .contract-sheet {
                width: 100%;
                min-height: auto;
                margin: 0;
                padding: 0;
                border: none;
                box-shadow: none;
            }

            @page {
                size: A4 portrait;
                margin: 12mm 14mm 12mm 14mm;
            }
        }
    </style>
</head>
<body>

    {{-- Top Sticky Navbar --}}
    <header class="portal-navbar">
        <div class="nav-left">
            <div>
                <div class="d-flex align-items-center gap-2">
                    <span class="doc-info-title">{{ $docNoun }}</span>
                    @if ($isSigned)
                        <span class="doc-badge badge-signed">
                            <i class="mdi mdi-check-circle"></i> Sudah Ditandatangani
                        </span>
                    @else
                        <span class="doc-badge badge-pending">
                            <i class="mdi mdi-clock-outline"></i> Menunggu Tanda Tangan
                        </span>
                    @endif
                </div>
                <div class="doc-info-subtitle">{{ $sellcon->no_contract ?: 'Contract #' . $sellcon->id }} &bull; {{ $client->company ?? 'Customer' }}</div>
            </div>
        </div>

        <div class="nav-actions">
            @if ($isSigned)
                <a href="{{ route('contract.customer.pdf', $sellcon->sign_token) }}" target="_blank" class="btn-portal btn-portal-success">
                    <i class="mdi mdi-download"></i> Download PDF Resmi
                </a>
            @else
                <a href="#section-sign" class="btn-portal btn-portal-primary">
                    <i class="mdi mdi-draw"></i> Tanda Tangan Sekarang
                </a>
            @endif
        </div>
    </header>

    <div class="portal-container">

        {{-- Printable Sheet (Exact match to detail-print / detail-print-unit) --}}
        <div class="contract-sheet">
            @if ($isUnit)
                {{-- ==================== UNIT QUOTATION SELLING CONTRACT / CONFIRM ORDER ==================== --}}
                {{-- Header --}}
                <div class="doc-header">
                    <div class="brand-info">
                        <div class="brand-logo">
                            <img src="{{ asset('/asset') }}/logo/{{ $isKojisha ? 'Kojisha-Log.png' : 'Reftech-Log.png' }}" alt="{{ $entityName }}">
                        </div>
                        <div class="brand-name">{{ $entityName }}</div>
                        <div class="brand-address">
                            @if ($isKojisha)
                                <p>Jl. Nancep No. 45A, Setu, Cibitung - Kab. Bekasi 17320</p>
                                <p>Telp: +62 812-1000-0997 &nbsp;|&nbsp; Email: admin@kojisha.com</p>
                                @if ($hasTax)
                                    <p><strong>NPWP:</strong> 96.484.859.2-413.000</p>
                                @endif
                            @else
                                <p>Taman Kopo Indah V, Ruko Sommerville No. 31, Bandung – Jawa Barat 40218</p>
                                <p>Telp: 022 54417653 &nbsp;|&nbsp; Email: info@reftech.id</p>
                                @if ($hasTax)
                                    <p><strong>NPWP:</strong> 07.372.857.1-842.9000</p>
                                @endif
                            @endif
                        </div>
                    </div>

                    <div class="doc-title-block">
                        <h1 class="doc-title">{{ $docHeading }}</h1>
                        <div class="doc-number">#{{ $sellcon->no_contract }}</div>
                        <div class="doc-date">Date: {{ $sellcon->date ? Carbon\Carbon::parse($sellcon->date)->format('d-m-Y') : date('d-m-Y') }}</div>
                    </div>
                </div>

                {{-- Info Section --}}
                <div class="info-section">
                    {{-- Quote To --}}
                    <div class="info-card">
                        <div class="info-card-title">Customer / Quote To</div>
                        <div class="info-card-company">{{ $unitQuote->client?->company ?? '-' }}</div>
                        <div class="info-row">
                            <span class="label">Attn:</span>
                            <span class="value">{{ $unitQuote->pic?->name_pic ?? ($unitQuote->attn ?: '-') }}</span>
                        </div>
                        <div class="info-row">
                            <span class="label">Phone:</span>
                            <span class="value">{{ $unitQuote->pic?->phone_pic ?? $unitQuote->client?->phone ?? '-' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="label">Email:</span>
                            <span class="value">{{ $unitQuote->pic?->email_pic ?? $unitQuote->client?->email ?? '-' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="label">Address:</span>
                            <span class="value">{{ $unitQuote->address ?: ($unitQuote->client?->address ?? '-') }}</span>
                        </div>
                    </div>

                    {{-- Contract Details --}}
                    <div class="info-card">
                        <div class="info-card-title">Order Information</div>
                        <div class="info-row">
                            <span class="label">Quotation:</span>
                            <span class="value"><strong>{{ $unitQuote->no_quote }}</strong></span>
                        </div>
                        <div class="info-row">
                            <span class="label">Seller:</span>
                            <span class="value">{{ $entityName }}</span>
                        </div>
                        <div class="info-row">
                            <span class="label">Sales:</span>
                            <span class="value">{{ $unitQuote->sales?->name ?? 'Sales Representative' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="label">Tax Status:</span>
                            <span class="value">{{ $hasTax ? 'PPN 11% (Taxable)' : 'Non-PPN (0%)' }}</span>
                        </div>
                        @if ($unitQuote->title)
                            <div class="info-row">
                                <span class="label">Subject:</span>
                                <span class="value">{{ $unitQuote->title }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Items Table --}}
                @php
                    $specLabels = [
                        'brand'=>'Brand','model'=>'Model','type_unit'=>'Type',
                        'bar'=>'Max Pressure','air_cap'=>'Air Capacity','power'=>'Motor Power',
                        'voltage'=>'Voltage','connect'=>'Drive','cooling'=>'Cooling Method',
                        'exhaust'=>'Connection','refrigerant_type'=>'Refrigerant Type','pdp'=>'PDP',
                        'filtration'=>'Filtration','oil_content'=>'Oil Content','grade'=>'Grade',
                        'capacity'=>'Capacity','material'=>'Material','test_pressure'=>'Test Pressure',
                        'inlet_pressure'=>'Inlet Pressure','outlet_pressure'=>'Outlet Pressure',
                        'inlet_cap'=>'Inlet Capacity (LP)','outlet_cap'=>'Outlet Capacity (HP)',
                        'dimension'=>'Dimension','weight'=>'Weight',
                    ];
                    $specUnits = [
                        'bar'=>' Bar','air_cap'=>' m³/min','test_pressure'=>' Bar',
                        'inlet_pressure'=>' Bar','outlet_pressure'=>' Bar',
                        'inlet_cap'=>' m³/min','outlet_cap'=>' m³/min',
                        'weight'=>' Kg','capacity'=>' Liter',
                    ];
                @endphp

                <div class="table-responsive-wrapper">
                    <table class="items-table">
                        <thead>
                            <tr>
                                <th style="width: 4%; text-align: center;">No</th>
                                <th style="width: 52%;">Item Description</th>
                                <th style="width: 10%; text-align: center;">Qty</th>
                                <th style="width: 17%; text-align: right;">Price (IDR)</th>
                                <th style="width: 17%; text-align: right;">Amount (IDR)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($unitQuote->details as $i => $item)
                                <tr>
                                    <td style="text-align: center; color: #64748b;">{{ $i + 1 }}</td>
                                    <td>
                                        @if ($item->type === 'unit' && $item->unit)
                                            <div class="item-title">{{ $item->label ?: ($item->unit->brand . ' ' . $item->unit->model) }}</div>
                                            @php $specs = $item->getSpecVisibleArray(); @endphp
                                            @if (!empty($specs))
                                                <div class="spec-grid">
                                                    @foreach ($specs as $field)
                                                        @if ($field === 'unit') @continue @endif
                                                        @php $val = $item->unit->$field ?? null; @endphp
                                                        @if ($val && isset($specLabels[$field]))
                                                            <div>
                                                                <span style="color:#64748b;">{{ $specLabels[$field] }}:</span>
                                                                <strong>{{ $val }}{{ $specUnits[$field] ?? '' }}</strong>
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            @endif
                                        @else
                                            <div class="item-title">{{ $item->label }}</div>
                                            @if ($item->description && $item->description !== $item->label)
                                                <div class="item-desc">{{ $item->description }}</div>
                                            @endif
                                        @endif
                                    </td>
                                    <td style="text-align: center; font-weight: 600;">
                                        {{ (float)$item->qty == (int)$item->qty ? (int)$item->qty : $item->qty }} {{ $item->info_qty ?? 'Unit' }}
                                    </td>
                                    <td style="text-align: right;">
                                        {{ number_format($item->price, 0, '', '.') }}
                                    </td>
                                    <td style="text-align: right; font-weight: 700;">
                                        {{ number_format($item->amount, 0, '', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Totals Section (Right Aligned) --}}
                <div class="totals-section">
                    <table class="totals-table">
                        <tr>
                            <td class="label">Subtotal:</td>
                            <td class="val">Rp {{ number_format($unitQuote->subtotal, 0, '', '.') }}</td>
                        </tr>
                        @if ($unitQuote->diskon > 0)
                            <tr>
                                <td class="label" style="color: #dc2626;">Discount {{ $unitQuote->discount_label ? '(' . $unitQuote->discount_label . ')' : '' }}:</td>
                                <td class="val" style="color: #dc2626;">- Rp {{ number_format($unitQuote->discount_amount, 0, '', '.') }}</td>
                            </tr>
                            <tr>
                                <td class="label">After Discount:</td>
                                <td class="val">Rp {{ number_format($afterDisc, 0, '', '.') }}</td>
                            </tr>
                        @endif
                        @if ($hasTax)
                            <tr>
                                <td class="label">VAT / PPN (11%):</td>
                                <td class="val">Rp {{ number_format($unitQuote->tax_amount, 0, '', '.') }}</td>
                            </tr>
                        @endif
                        @if ($unitQuote->shipping > 0)
                            <tr>
                                <td class="label">Shipping:</td>
                                <td class="val">Rp {{ number_format($unitQuote->shipping, 0, '', '.') }}</td>
                            </tr>
                        @endif
                        <tr class="grand-total-row">
                            <td class="label">{{ $hasTax ? 'TOTAL (INC PPN):' : 'TOTAL (EXC PPN):' }}</td>
                            <td class="val">Rp {{ number_format($unitQuote->total, 0, '', '.') }}</td>
                        </tr>
                    </table>
                </div>

                {{-- Single Term & Condition Card --}}
                <div class="terms-card">
                    <div class="terms-card-header">TERM &amp; CONDITION</div>
                    <div class="terms-card-body">
                        <div class="term-row">
                            <span class="term-label">Validity Of Quotation</span>
                            <span class="term-sep">:</span>
                            <span class="term-val">{{ $unitQuote->validity ?: '1 (one) Month' }}</span>
                        </div>
                        <div class="term-row">
                            <span class="term-label">Price</span>
                            <span class="term-sep">:</span>
                            <span class="term-val">{{ $unitQuote->pricing ?: 'Franco Factory' }}</span>
                        </div>
                        <div class="term-row">
                            <span class="term-label">Delivery Process</span>
                            <span class="term-sep">:</span>
                            <span class="term-val">{{ $unitQuote->delivery_process ?: 'Ready stock' }}</span>
                        </div>
                        <div class="term-row">
                            <span class="term-label">Payment</span>
                            <span class="term-sep">:</span>
                            <span class="term-val">{{ $unitQuote->payment ?: 'Cash Before Delivery' }}</span>
                        </div>
                        @if ($unitQuote->note)
                            <div class="term-row">
                                <span class="term-label">Note</span>
                                <span class="term-sep">:</span>
                                <span class="term-val">{{ $unitQuote->note }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Thank you for your business Note --}}
                <div class="business-thanks-note">
                    <p class="thanks-title">Thank you for your business!</p>
                    <p class="thanks-desc mb-0">Dokumen ini merupakan konfirmasi kesepakatan penjualan/pesanan resmi antara <strong>{{ $entityName }}</strong> dan <strong>{{ $unitQuote->client?->company ?? 'Customer' }}</strong>.</p>
                </div>

                {{-- Signatures Section --}}
                <div class="signature-section">
                    {{-- Authorized By --}}
                    <div class="signature-box">
                        <div class="signature-label">Authorized By,</div>
                        <div class="signature-img-wrap">
                            @if ($isKojisha)
                                <img src="{{ asset('/asset') }}/sign/kojisha-nm.jpeg" alt="Signature Kojisha">
                            @else
                                @if ($hasTax)
                                    <img src="{{ asset('/asset') }}/contract/sign-irene.jpeg" alt="Signature Irene">
                                @else
                                    <img src="{{ asset('/asset') }}/sign/ttdirene.jpg" alt="Signature Irene">
                                @endif
                            @endif
                        </div>
                        <div class="signature-name">{{ $isKojisha ? 'Dedeh Sulastri' : 'Mrs. Irene' }}</div>
                        <div class="signature-role">{{ $entityName }}</div>
                    </div>

                    {{-- Accepted By Customer --}}
                    <div class="signature-box">
                        <div class="signature-label">Accepted By Customer,</div>
                        <div class="signature-img-wrap">
                            @if ($isSigned && $sellcon->customer_signature)
                                <img src="{{ asset($sellcon->customer_signature) }}" alt="Customer Signature">
                            @else
                                <div style="color: #94a3b8; font-size: 11px; font-style: italic;">
                                    (Bubuhi tanda tangan pada formulir di bawah)
                                </div>
                            @endif
                        </div>
                        <div class="signature-name">
                            {{ $isSigned ? $sellcon->customer_signer_name : ($unitQuote->pic?->name_pic ?: ($unitQuote->attn ?: '..............................')) }}
                        </div>
                        <div class="signature-role">
                            {{ $isSigned ? ($sellcon->customer_signer_position ?: ($unitQuote->client?->company ?? '-')) : ($unitQuote->client?->company ?? '-') }}
                        </div>
                        @if ($isSigned && $sellcon->signed_at)
                            <div style="font-size: 8.5px; color: #16a34a; font-weight: 600; margin-top: 2px;">
                                ✓ Signed on {{ date('d-m-Y H:i', strtotime($sellcon->signed_at)) }}
                            </div>
                        @endif
                    </div>
                </div>

            @else
                {{-- ==================== SERVICE / SPAREPART SELLING CONTRACT / CONFIRM ORDER ==================== --}}
                {{-- Header --}}
                <div class="doc-header">
                    <div class="brand-info">
                        <div class="brand-logo">
                            @if ($sellcon->type == 'Selling')
                                <img src="{{ asset('/asset') }}/logo/Reftech-Log.png" alt="PT Reftech Jaya Optima">
                            @else
                                <img src="{{ asset('/asset') }}/logo/Logo-update-size.png" alt="PT Kojisha Innotiv Indonesia">
                            @endif
                        </div>
                        <div class="brand-name">{{ $entityName }}</div>
                        <div class="brand-address">
                            @if ($sellcon->type == 'Selling')
                                <p>Taman Kopo Indah V, Ruko Sommerville No. 31, Bandung – Jawa Barat 40218</p>
                                <p>Telp: 022 54417653 &nbsp;|&nbsp; Email: info@reftech.id</p>
                                @if ($hasTax)
                                    <p><strong>NPWP:</strong> 07.372.857.1-842.9000</p>
                                @endif
                            @else
                                <p>Jl. Nancep No. 45A, Setu, Cibitung - Kab. Bekasi 17320</p>
                                <p>Telp: +62 812-1000-0997 &nbsp;|&nbsp; Email: admin@kojisha.com</p>
                                @if ($hasTax)
                                    <p><strong>NPWP:</strong> 96.484.859.2-413.000</p>
                                @endif
                            @endif
                        </div>
                    </div>

                    <div class="doc-title-block">
                        <h1 class="doc-title">{{ $docHeading }}</h1>
                        <div class="doc-number">#{{ $sellcon->no_contract }}</div>
                        <div class="doc-date">Date: {{ $sellcon->date ? Carbon\Carbon::parse($sellcon->date)->format('d-m-Y') : date('d-m-Y') }}</div>
                    </div>
                </div>

                {{-- Info Section --}}
                <div class="info-section">
                    {{-- Quote To --}}
                    <div class="info-card">
                        <div class="info-card-title">Customer / Quote To</div>
                        <div class="info-card-company">{{ $quote->pic?->client?->company ?? '-' }}</div>
                        <div class="info-row">
                            <span class="label">Attn:</span>
                            <span class="value">{{ $quote->pic?->name_pic ?? '-' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="label">Phone:</span>
                            <span class="value">{{ $quote->pic?->phone_pic ?? $quote->pic?->client?->phone ?? '-' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="label">Email:</span>
                            <span class="value">{{ $quote->pic?->email_pic ?? $quote->pic?->client?->email ?? '-' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="label">Address:</span>
                            <span class="value">{{ $quote->pic?->client?->address ?? '-' }}</span>
                        </div>
                    </div>

                    {{-- Contract Details --}}
                    <div class="info-card">
                        <div class="info-card-title">Order Information</div>
                        <div class="info-row">
                            <span class="label">Quotation:</span>
                            <span class="value"><strong>{{ $quote->no_quote }}</strong></span>
                        </div>
                        <div class="info-row">
                            <span class="label">Seller:</span>
                            <span class="value">{{ $entityName }}</span>
                        </div>
                        <div class="info-row">
                            <span class="label">Sales:</span>
                            <span class="value">{{ $quote->sales?->name ?? '-' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="label">Tax Status:</span>
                            <span class="value">{{ $hasTax ? 'PPN ' . $quote->tax . '% (Taxable)' : 'Non-PPN (0%)' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="label">Type:</span>
                            <span class="value">{{ $quote->type }}</span>
                        </div>
                    </div>
                </div>

                {{-- Items Table --}}
                <div class="table-responsive-wrapper">
                    @if ($quote->type == 'Sparepart')
                        <table class="items-table">
                            <thead>
                                <tr>
                                    <th style="width: 4%; text-align: center;">No</th>
                                    <th style="width: 50%;">Item Description</th>
                                    <th style="width: 16%; text-align: right;">Price (IDR)</th>
                                    <th style="width: 8%; text-align: center;">Qty</th>
                                    <th style="width: 6%; text-align: center;">Disc</th>
                                    <th style="width: 16%; text-align: right;">Amount (IDR)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $no = 0; @endphp
                                @foreach ($dquote as $product)
                                    @php $no++; @endphp
                                    <tr>
                                        <td style="text-align: center; color: #64748b;">{{ $no }}</td>
                                        <td>
                                            <div class="item-title">
                                                @if ($product->id_equivalent == '0')
                                                    -
                                                @else
                                                    {{ $product->equivalent->brand ?? '' }} {{ $product->equivalent->pn ?? '' }}
                                                @endif
                                            </div>
                                            <div class="item-desc">{{ $product->detail_product }}</div>
                                        </td>
                                        <td style="text-align: right;">
                                            {{ $product->amount == 0 ? 'SBO' : number_format($product->price, 0, '', '.') }}
                                        </td>
                                        <td style="text-align: center; font-weight: 600;">
                                            {{ $product->qty }} {{ $product->info_qty }}
                                        </td>
                                        <td style="text-align: center;">
                                            {{ $product->disc }}%
                                        </td>
                                        <td style="text-align: right; font-weight: 700;">
                                            {{ number_format($product->amount, 0, '', '.') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <table class="items-table">
                            <thead>
                                <tr>
                                    <th style="width: 4%; text-align: center;">No</th>
                                    <th style="width: 52%;">Item Description</th>
                                    <th style="width: 10%; text-align: center;">Qty</th>
                                    <th style="width: 17%; text-align: right;">Price (IDR)</th>
                                    <th style="width: 17%; text-align: right;">Amount (IDR)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $abjad = 64; @endphp
                                @foreach ($subQuote as $subJudul)
                                    @php
                                        $no = 0;
                                        $abjad++;
                                    @endphp
                                    <tr class="subtitle-row">
                                        <td style="text-align: center; font-weight: 700; color: #0284c7;">{{ chr($abjad) }}</td>
                                        <td colspan="4" style="font-weight: 700;">{{ $subJudul->subtitle }}</td>
                                    </tr>
                                    @foreach ($subJudul->detail as $product)
                                        @php $no++; @endphp
                                        <tr>
                                            <td style="text-align: center; color: #64748b;">{{ $no }}</td>
                                            <td>
                                                <div class="item-title">{{ $product->product }}</div>
                                                @if ($product->detail != '-')
                                                    <div class="item-desc">{{ $product->detail }}</div>
                                                @endif
                                            </td>
                                            <td style="text-align: center; font-weight: 600;">
                                                {{ $product->qty }} {{ $product->info_qty }}
                                            </td>
                                            <td style="text-align: right;">
                                                {{ number_format($product->price, 0, '', '.') }}
                                            </td>
                                            <td style="text-align: right; font-weight: 700;">
                                                {{ number_format($product->amount, 0, '', '.') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>

                {{-- Totals Section (Right Aligned) --}}
                <div class="totals-section">
                    <table class="totals-table">
                        <tr>
                            <td class="label">Subtotal:</td>
                            <td class="val">Rp {{ number_format($quote->subtotal, 0, '', '.') }}</td>
                        </tr>
                        @if ($quote->diskon > 0)
                            <tr>
                                <td class="label" style="color: #dc2626;">Discount:</td>
                                <td class="val" style="color: #dc2626;">- Rp {{ number_format($quote->diskon, 0, '', '.') }}</td>
                            </tr>
                            <tr>
                                <td class="label">After Discount:</td>
                                <td class="val">Rp {{ number_format($afterDisc, 0, '', '.') }}</td>
                            </tr>
                        @endif
                        @if ($hasTax)
                            <tr>
                                <td class="label">VAT / Tax ({{ $quote->tax }}%):</td>
                                <td class="val">Rp {{ number_format($vat, 0, '', '.') }}</td>
                            </tr>
                        @endif
                        @if ($quote->shipping > 0)
                            <tr>
                                <td class="label">Shipping:</td>
                                <td class="val">Rp {{ number_format($quote->shipping, 0, '', '.') }}</td>
                            </tr>
                        @endif
                        <tr class="grand-total-row">
                            <td class="label">{{ $hasTax ? 'TOTAL (INC PPN):' : 'TOTAL (EXC PPN):' }}</td>
                            <td class="val">Rp {{ number_format($quote->harga_total, 0, '', '.') }}</td>
                        </tr>
                    </table>
                </div>

                {{-- Single Term & Condition Card --}}
                @if (isset($quote->termncon[0]))
                    <div class="terms-card">
                        <div class="terms-card-header">TERM &amp; CONDITION</div>
                        <div class="terms-card-body">
                            <div class="term-row">
                                <span class="term-label">Validity of Quote</span>
                                <span class="term-sep">:</span>
                                <span class="term-val">{{ $quote->termncon[0]->validity ?: '-' }}</span>
                            </div>
                            <div class="term-row">
                                <span class="term-label">Price</span>
                                <span class="term-sep">:</span>
                                <span class="term-val">{{ $quote->termncon[0]->pricing ?: '-' }}</span>
                            </div>
                            <div class="term-row">
                                <span class="term-label">Delivery Process</span>
                                <span class="term-sep">:</span>
                                <span class="term-val">{{ $quote->termncon[0]->delivery_process ?: '-' }}</span>
                            </div>
                            <div class="term-row">
                                <span class="term-label">Payment</span>
                                <span class="term-sep">:</span>
                                <span class="term-val">{{ $quote->termncon[0]->payment ?: '-' }}</span>
                            </div>
                            @if (!empty($quote->termncon[0]->note))
                                <div class="term-row">
                                    <span class="term-label">Note</span>
                                    <span class="term-sep">:</span>
                                    <span class="term-val">{{ $quote->termncon[0]->note }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- Thank you for your business Note --}}
                <div class="business-thanks-note">
                    <p class="thanks-title">Thank you for your business!</p>
                    <p class="thanks-desc mb-0">Dokumen ini merupakan konfirmasi kesepakatan penjualan/pesanan resmi antara <strong>{{ $entityName }}</strong> dan <strong>{{ $quote->pic?->client?->company ?? 'Customer' }}</strong>.</p>
                </div>

                {{-- Signatures Section --}}
                <div class="signature-section">
                    {{-- Authorized By --}}
                    <div class="signature-box">
                        <div class="signature-label">Authorized By,</div>
                        <div class="signature-img-wrap">
                            @if ($sellcon->type == 'Selling')
                                @if ($hasTax)
                                    <img src="{{ asset('/asset') }}/contract/sign-irene.jpeg" alt="Signature Irene">
                                @else
                                    <img src="{{ asset('/asset') }}/sign/ttdirene.jpg" alt="Signature Irene">
                                @endif
                            @else
                                <img src="{{ asset('/asset') }}/contract/sign-dedeh.png" alt="Signature Dedeh">
                            @endif
                        </div>
                        <div class="signature-name">{{ $sellcon->type == 'Selling' ? 'Mrs. Irene' : 'Dedeh Sulastri' }}</div>
                        <div class="signature-role">{{ $entityName }}</div>
                    </div>

                    {{-- Accepted By Customer --}}
                    <div class="signature-box">
                        <div class="signature-label">Accepted By Customer,</div>
                        <div class="signature-img-wrap">
                            @if ($isSigned && $sellcon->customer_signature)
                                <img src="{{ asset($sellcon->customer_signature) }}" alt="Customer Signature">
                            @else
                                <div style="color: #94a3b8; font-size: 11px; font-style: italic;">
                                    (Bubuhi tanda tangan pada formulir di bawah)
                                </div>
                            @endif
                        </div>
                        <div class="signature-name">
                            {{ $isSigned ? $sellcon->customer_signer_name : ($quote->pic?->name_pic ?: '..............................') }}
                        </div>
                        <div class="signature-role">
                            {{ $isSigned ? ($sellcon->customer_signer_position ?: ($quote->pic?->client?->company ?? '-')) : ($quote->pic?->client?->company ?? '-') }}
                        </div>
                        @if ($isSigned && $sellcon->signed_at)
                            <div style="font-size: 8.5px; color: #16a34a; font-weight: 600; margin-top: 2px;">
                                ✓ Signed on {{ date('d-m-Y H:i', strtotime($sellcon->signed_at)) }}
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        {{-- Interactive Customer Signature Section --}}
        <div id="section-sign">
            @if ($isSigned)
                <div class="success-signed-card">
                    <div class="success-signed-icon">
                        <i class="mdi mdi-check-bold"></i>
                    </div>
                    <div class="success-signed-title">Kontrak Telah Ditandatangani</div>
                    <div class="success-signed-desc">
                        Dokumen ini telah resmi disetujui dan ditandatangani secara digital oleh <strong>{{ $sellcon->customer_signer_name }}</strong>{{ $sellcon->customer_signer_position ? ' (' . $sellcon->customer_signer_position . ')' : '' }} pada {{ date('d F Y, H:i', strtotime($sellcon->signed_at)) }} WIB.
                    </div>
                    <div style="display: flex; gap: 10px; justify-content: center; flex-wrap: wrap; margin-top: 18px;">
                        <a href="{{ route('contract.customer.pdf', $sellcon->sign_token) }}" target="_blank" class="btn-portal btn-portal-success" style="padding: 10px 20px; font-size: 13.5px;">
                            <i class="mdi mdi-file-pdf-box"></i> Unduh Salinan PDF Kontrak
                        </a>
                        <button type="button" class="btn-portal btn-portal-danger" id="btn-customer-reset-sign" style="padding: 10px 18px; font-size: 13.5px;">
                            <i class="mdi mdi-refresh"></i> Tanda Tangani Ulang / Reset TTD
                        </button>
                    </div>
                </div>
            @else
                <div class="sign-action-card">
                    <div class="sign-card-title">
                        <i class="mdi mdi-draw-pen text-primary"></i>
                        Bubuhi Tanda Tangan Digital
                    </div>
                    <div class="sign-card-subtitle">
                        Silakan buat tanda tangan Anda pada area kotak di bawah menggunakan jari (Touchscreen) atau mouse kursor.
                    </div>

                    <form id="form-sign-contract" action="{{ route('contract.customer.sign.submit', $sellcon->sign_token) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="signature_data" id="signature_data">

                        {{-- Canvas Pad --}}
                        <div class="canvas-container" id="canvas-wrapper">
                            <canvas id="signature-pad"></canvas>
                            <div class="canvas-placeholder-hint" id="canvas-hint">
                                <i class="mdi mdi-gesture-tap"></i> Goreskan tanda tangan di sini
                            </div>
                        </div>

                        <div class="canvas-tools">
                            <button type="button" class="btn-canvas-tool" id="btn-clear-canvas">
                                <i class="mdi mdi-eraser"></i> Hapus
                            </button>
                        </div>

                        {{-- Signer Information --}}
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="signer_name">Nama Lengkap Penandatangan <span style="color: #dc2626;">*</span></label>
                                <input type="text" id="signer_name" name="signer_name" value="{{ $isUnit ? ($unitQuote->pic?->name_pic ?? ($unitQuote->attn ?? '')) : ($quote->pic?->name_pic ?? '') }}" placeholder="Contoh: Budi Santoso" required>
                            </div>
                            <div class="form-group">
                                <label for="signer_position">Jabatan / Posisi <span style="color: #64748b; font-weight: normal;">(Opsional)</span></label>
                                <input type="text" id="signer_position" name="signer_position" placeholder="Contoh: Direktur / Procurement Manager">
                            </div>
                        </div>

                        <div class="form-group" style="margin-bottom: 16px;">
                            <label for="stamp">Upload Stempel Perusahaan (Opsional)</label>
                            <input type="file" id="stamp" name="stamp" accept="image/png, image/jpeg">
                        </div>

                        <div class="agreement-box">
                            <input type="checkbox" id="agreement" name="agreement" value="1" required>
                            <label for="agreement" style="cursor: pointer;">
                                Saya menyatakan bahwa seluruh informasi di atas adalah benar dan saya memiliki wewenang yang sah untuk menyetujui dan menandatangani kontrak penjualan ini atas nama <strong>{{ $client->company ?? 'perusahaan' }}</strong>.
                            </label>
                        </div>

                        <button type="submit" class="btn-submit-sign" id="btn-submit">
                            <i class="mdi mdi-check-circle-outline"></i> Setujui &amp; Tandatangani Kontrak
                        </button>
                    </form>
                </div>
            @endif
        </div>

    </div>

    {{-- Scripts --}}
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Handler Reset Tanda Tangan oleh Customer jika ada kesalahan
            var btnCustomerReset = document.getElementById('btn-customer-reset-sign');
            if (btnCustomerReset) {
                btnCustomerReset.addEventListener('click', function () {
                    Swal.fire({
                        title: 'Tanda Tangani Ulang?',
                        text: 'Tanda tangan saat ini akan dihapus dan Anda dapat membubuhkan tanda tangan baru.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#e11d48',
                        cancelButtonColor: '#64748b',
                        confirmButtonText: '<i class="mdi mdi-delete-outline"></i> Ya, Hapus & TTD Ulang',
                        cancelButtonText: 'Batal',
                        reverseButtons: true
                    }).then(function (result) {
                        if (result.isConfirmed) {
                            Swal.fire({
                                title: 'Mereset Tanda Tangan...',
                                text: 'Mohon tunggu sebentar...',
                                allowOutsideClick: false,
                                didOpen: function () {
                                    Swal.showLoading();
                                }
                            });

                            fetch("{{ route('contract.customer.sign.reset', $sellcon->sign_token) }}", {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                    'Accept': 'application/json',
                                    'Content-Type': 'application/json'
                                }
                            })
                            .then(function (res) { return res.json(); })
                            .then(function (data) {
                                if (data.status === 'success') {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Berhasil Direset',
                                        text: data.message,
                                        timer: 1500,
                                        showConfirmButton: false
                                    }).then(function () {
                                        window.location.reload();
                                    });
                                } else {
                                    Swal.fire('Gagal', data.message || 'Terjadi kesalahan.', 'error');
                                }
                            })
                            .catch(function () {
                                Swal.fire('Error', 'Gagal menghubungi server.', 'error');
                            });
                        }
                    });
                });
            }

            var canvas = document.getElementById('signature-pad');
            if (!canvas) return;

            var wrapper = document.getElementById('canvas-wrapper');
            var hint = document.getElementById('canvas-hint');
            var signaturePad = new SignaturePad(canvas, {
                backgroundColor: 'rgba(255, 255, 255, 0)',
                penColor: '#0f172a',
                velocityFilterWeight: 0.7,
                minWidth: 1.5,
                maxWidth: 3.5,
            });

            function resizeCanvas() {
                var ratio = Math.max(window.devicePixelRatio || 1, 1);
                canvas.width = wrapper.clientWidth * ratio;
                canvas.height = wrapper.clientHeight * ratio;
                canvas.getContext("2d").scale(ratio, ratio);
                signaturePad.clear();
            }

            window.addEventListener("resize", resizeCanvas);
            resizeCanvas();

            signaturePad.addEventListener("beginStroke", function () {
                hint.style.display = 'none';
                wrapper.classList.add('has-drawn');
            });

            document.getElementById('btn-clear-canvas').addEventListener('click', function () {
                signaturePad.clear();
                hint.style.display = 'flex';
                wrapper.classList.remove('has-drawn');
            });

            var form = document.getElementById('form-sign-contract');
            var btnSubmit = document.getElementById('btn-submit');

            form.addEventListener('submit', function (e) {
                e.preventDefault();

                if (signaturePad.isEmpty()) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Tanda Tangan Belum Ada',
                        text: 'Silakan goreskan tanda tangan Anda pada kotak yang disediakan.',
                        confirmButtonColor: '#0284c7',
                    });
                    return;
                }

                var agreement = document.getElementById('agreement');
                if (!agreement.checked) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Persetujuan Diperlukan',
                        text: 'Harap centang persetujuan wewenang penandatanganan.',
                        confirmButtonColor: '#0284c7',
                    });
                    return;
                }

                var dataUrl = signaturePad.toDataURL('image/png');
                document.getElementById('signature_data').value = dataUrl;

                var formData = new FormData(form);

                btnSubmit.disabled = true;
                btnSubmit.innerHTML = '<i class="mdi mdi-loading mdi-spin"></i> Memproses Tanda Tangan...';

                fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                    },
                    body: formData
                })
                .then(function (res) {
                    return res.json().then(function (data) {
                        return { ok: res.ok, data: data };
                    });
                })
                .then(function (result) {
                    btnSubmit.disabled = false;
                    btnSubmit.innerHTML = '<i class="mdi mdi-check-circle-outline"></i> Setujui &amp; Tandatangani Kontrak';

                    if (result.ok && result.data.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil Ditandatangani!',
                            text: 'Terima kasih. Kontrak penjualan telah resmi ditandatangani.',
                            confirmButtonColor: '#16a34a',
                            confirmButtonText: 'Lihat Dokumen',
                        }).then(function () {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal Menyimpan',
                            text: result.data.message || 'Terjadi kesalahan saat memproses tanda tangan.',
                            confirmButtonColor: '#0284c7',
                        });
                    }
                })
                .catch(function (err) {
                    btnSubmit.disabled = false;
                    btnSubmit.innerHTML = '<i class="mdi mdi-check-circle-outline"></i> Setujui &amp; Tandatangani Kontrak';
                    Swal.fire({
                        icon: 'error',
                        title: 'Terjadi Kesalahan',
                        text: 'Koneksi terputus. Silakan coba beberapa saat lagi.',
                        confirmButtonColor: '#0284c7',
                    });
                });
            });
        });
    </script>
</body>
</html>
