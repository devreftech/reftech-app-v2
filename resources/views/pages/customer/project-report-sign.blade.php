@php
    $isSigned = $report->isSignedByCustomer();
    $client   = $report->client;
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>DAILY PROJECT REPORT — {{ $report->job_name }}</title>

    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
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
            font-size: 13px;
            line-height: 1.5;
            -webkit-font-smoothing: antialiased;
            padding-bottom: 80px;
        }

        /* Top Sticky Navbar */
        .portal-navbar {
            position: sticky;
            top: 0;
            z-index: 1000;
            background: rgba(255, 255, 255, 0.96);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid #e2e8f0;
            padding: 10px 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.04);
        }

        .portal-navbar .nav-left {
            display: flex;
            align-items: center;
            gap: 10px;
            min-width: 0;
        }

        .portal-navbar .doc-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 4px 9px;
            font-size: 11px;
            font-weight: 700;
            border-radius: 9999px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            white-space: nowrap;
            flex-shrink: 0;
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
            font-size: 13.5px;
            font-weight: 700;
            color: #0f172a;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 320px;
        }

        .portal-navbar .doc-info-subtitle {
            font-size: 11px;
            color: #64748b;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .portal-navbar .nav-actions {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-shrink: 0;
        }

        .btn-portal {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 7px 14px;
            font-size: 12.5px;
            font-weight: 600;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s ease;
            border: 1px solid transparent;
            white-space: nowrap;
        }

        .btn-portal-primary {
            background-color: #4f46e5;
            color: #ffffff;
        }

        .btn-portal-primary:hover {
            background-color: #4338ca;
            color: #ffffff;
        }

        .btn-portal-secondary {
            background-color: #ffffff;
            border-color: #cbd5e1;
            color: #334155;
        }

        .btn-portal-secondary:hover {
            background-color: #f8fafc;
            color: #0f172a;
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

        /* Container Document */
        .portal-container {
            max-width: 960px;
            margin: 18px auto 0;
            padding: 0 16px;
        }

        /* Document Paper */
        .document-paper {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            border: 1px solid #e2e8f0;
            padding: 24px 28px;
            color: #000;
        }

        /* Banner Notification */
        .sign-alert-banner {
            max-width: 960px;
            margin: 14px auto 0;
            padding: 0 16px;
        }

        /* Signature Action Card */
        .sign-action-card {
            background: #ffffff;
            border: 2px solid #4f46e5;
            border-radius: 12px;
            box-shadow: 0 8px 30px rgba(79, 70, 229, 0.12);
            padding: 22px 24px;
            margin-top: 20px;
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
            height: 210px;
            touch-action: none;
            margin-bottom: 8px;
            overflow: hidden;
        }

        .canvas-container.has-drawn {
            border-style: solid;
            border-color: #4f46e5;
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
            font-size: 12.5px;
            font-weight: 500;
            pointer-events: none;
            display: flex;
            align-items: center;
            gap: 6px;
            user-select: none;
            text-align: center;
            padding: 0 12px;
            width: 100%;
            justify-content: center;
        }

        .canvas-tools {
            display: flex;
            justify-content: flex-end;
            gap: 8px;
            margin-bottom: 14px;
        }

        .btn-canvas-tool {
            background: #f1f5f9;
            border: 1px solid #cbd5e1;
            padding: 6px 14px;
            font-size: 12px;
            font-weight: 600;
            color: #475569;
            border-radius: 6px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            transition: all 0.2s;
        }

        .btn-canvas-tool:hover {
            background: #fee2e2;
            border-color: #fca5a5;
            color: #dc2626;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
            margin-bottom: 14px;
        }

        .form-group label {
            display: block;
            font-size: 12.5px;
            font-weight: 600;
            color: #334155;
            margin-bottom: 5px;
        }

        .form-group input {
            width: 100%;
            padding: 10px 12px;
            font-size: 14px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            color: #0f172a;
            outline: none;
            transition: border-color 0.2s;
        }

        .form-group input:focus {
            border-color: #4f46e5;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }

        .form-check-custom {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            margin-bottom: 18px;
            padding: 12px 14px;
            background: #f8fafc;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
        }

        .form-check-custom input[type="checkbox"] {
            margin-top: 3px;
            width: 18px;
            height: 18px;
            cursor: pointer;
            accent-color: #4f46e5;
            flex-shrink: 0;
        }

        .form-check-custom label {
            font-size: 12.5px;
            color: #334155;
            cursor: pointer;
            line-height: 1.45;
        }

        /* 1:1 Report Styles */
        .report-page-inner {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif !important;
            font-size: 9.5pt;
            color: #000;
            background: #fff;
            line-height: 1.25;
        }

        .main-title {
            text-align: center;
            font-size: 15pt;
            font-weight: 900;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
            text-transform: uppercase;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
            border: 2px solid #000;
            margin-bottom: 0px;
        }

        .header-table td {
            border: 1.5px solid #000;
            padding: 5px 8px;
            vertical-align: top;
        }

        .header-title-cell {
            font-weight: bold;
            text-align: center;
            font-size: 9pt;
            height: 20px;
        }

        .logo-container {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            padding: 6px 0;
        }

        .reftech-brand {
            font-family: 'Arial Black', Impact, sans-serif;
            font-size: 18pt;
            font-weight: 900;
            letter-spacing: 1px;
            line-height: 1;
            color: #000;
        }

        .sub-header-title {
            text-align: center;
            font-weight: bold;
            font-size: 10.5pt;
            padding: 4px;
            border-bottom: 1.5px solid #000;
            background: #fff;
        }

        .info-grid-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9pt;
        }

        .info-grid-table td {
            border: none;
            padding: 2px 4px;
            vertical-align: top;
        }

        .label-col {
            font-weight: bold;
            width: 140px;
        }

        .colon-col {
            width: 10px;
            text-align: center;
        }

        .section-header {
            font-weight: bold;
            font-size: 9.5pt;
            margin-top: 6px;
            margin-bottom: 3px;
        }

        .table-responsive-wrapper {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            margin-bottom: 5px;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            border: 1.5px solid #000;
            font-size: 8.5pt;
            min-width: 100%;
        }

        .data-table th {
            border: 1px solid #000;
            padding: 4px;
            text-align: center;
            font-weight: bold;
            background: #f8fafc;
        }

        .data-table td {
            border: 1px solid #000;
            padding: 4px 6px;
            vertical-align: middle;
        }

        .two-col-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 2px;
        }

        .two-col-table td {
            vertical-align: top;
            padding: 0;
            border: none;
        }

        .col-left {
            width: 50%;
            padding-right: 4px !important;
        }

        .col-right {
            width: 50%;
            padding-left: 4px !important;
        }

        .weather-box {
            border: 1.5px solid #000;
            padding: 6px 8px;
            min-height: 140px;
        }

        .weather-row {
            display: flex;
            align-items: center;
            margin-bottom: 4px;
            font-size: 8.5pt;
        }

        .checkbox-square {
            width: 15px;
            height: 15px;
            border: 1.5px solid #000;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-right: 8px;
            font-weight: bold;
            font-size: 11pt;
            line-height: 1;
            flex-shrink: 0;
        }

        .weather-label {
            width: 75px;
            font-weight: bold;
            flex-shrink: 0;
        }

        .note-box {
            border: 1.5px solid #000;
            padding: 5px 8px;
            margin-bottom: 5px;
            min-height: 48px;
            font-size: 8.5pt;
            border-radius: 4px;
        }

        .note-box-title {
            font-weight: bold;
            font-size: 8.5pt;
            margin-bottom: 2px;
            display: block;
        }

        .note-box-content {
            white-space: pre-line;
            font-size: 8.5pt;
            min-height: 25px;
        }

        .signature-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
            margin-bottom: 12px;
        }

        .signature-table td {
            width: 50%;
            vertical-align: top;
            border: none;
            padding: 0;
        }

        .sign-box {
            border: 1.5px solid #000;
            min-height: 110px;
            padding: 8px 10px;
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            border-radius: 4px;
        }

        .sign-title {
            font-weight: bold;
            font-size: 9pt;
        }

        .sign-name {
            font-weight: bold;
            font-size: 8.5pt;
            text-decoration: underline;
        }

        .sign-img {
            max-height: 60px;
            max-width: 160px;
            object-fit: contain;
            display: block;
            margin: auto 0;
        }

        .photo-section-title {
            font-weight: bold;
            font-size: 11pt;
            border-bottom: 2px solid #000;
            padding-bottom: 4px;
            margin-top: 18px;
            margin-bottom: 12px;
            text-transform: uppercase;
            text-align: center;
        }

        .photo-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
        }

        .photo-item {
            border: 1.5px solid #000;
            padding: 6px;
            text-align: center;
            background: #fff;
            border-radius: 4px;
        }

        .photo-img {
            width: 100%;
            height: 180px;
            object-fit: cover;
            border: 1px solid #ccc;
            margin-bottom: 4px;
            display: block;
            border-radius: 2px;
        }

        .photo-caption {
            font-size: 8.5pt;
            font-weight: bold;
            color: #000;
            word-break: break-word;
        }

        /* Mobile Floating Quick Jump Button */
        .mobile-floating-sign-btn {
            display: none;
            position: fixed;
            bottom: 16px;
            left: 16px;
            right: 16px;
            z-index: 999;
            box-shadow: 0 4px 20px rgba(79, 70, 229, 0.4);
            border-radius: 9999px;
            padding: 12px 20px;
            font-size: 14px;
            font-weight: 700;
            background: #4f46e5;
            color: #ffffff;
            text-align: center;
            text-decoration: none;
            justify-content: center;
            align-items: center;
            gap: 6px;
            transition: transform 0.2s;
        }

        .mobile-floating-sign-btn:active {
            transform: scale(0.98);
        }

        /* ============================================================
           MOBILE RESPONSIVE OVERRIDES (max-width: 768px)
           ============================================================ */
        @media (max-width: 768px) {
            .portal-container {
                padding: 0 10px;
                margin-top: 12px;
            }

            .sign-alert-banner {
                padding: 0 10px;
            }

            .document-paper {
                padding: 16px 12px;
                border-radius: 8px;
            }

            .portal-navbar {
                padding: 8px 12px;
            }

            .portal-navbar .doc-info-title {
                max-width: 180px;
                font-size: 12.5px;
            }

            .portal-navbar .doc-badge {
                font-size: 10px;
                padding: 3px 7px;
            }

            .portal-navbar .btn-portal span.btn-text {
                display: none;
            }

            .portal-navbar .btn-portal {
                padding: 6px 10px;
            }

            .main-title {
                font-size: 13pt;
                margin-bottom: 6px;
            }

            /* Responsive Stack for Header Boxes */
            .header-table, 
            .header-table tbody, 
            .header-table tr, 
            .header-table td {
                display: block !important;
                width: 100% !important;
            }

            .header-table {
                border: 1.5px solid #000;
            }

            .header-table tr td.header-title-cell {
                display: none !important;
            }

            .header-table td {
                border: none !important;
                border-bottom: 1.5px solid #000 !important;
                padding: 8px 10px !important;
            }

            .header-table td:last-child {
                border-bottom: none !important;
            }

            .logo-container {
                padding: 2px 0;
            }

            .reftech-brand {
                font-size: 16pt;
            }

            .sub-header-title {
                font-size: 9.5pt;
                padding: 3px;
                background: #f1f5f9;
            }

            .info-grid-table td {
                padding: 2px 0;
                font-size: 8.5pt;
            }

            .label-col {
                width: 120px;
            }

            /* Responsive Two Columns Layout Stacking */
            .two-col-table, 
            .two-col-table tbody, 
            .two-col-table tr, 
            .two-col-table td.col-left, 
            .two-col-table td.col-right {
                display: block !important;
                width: 100% !important;
                padding: 0 !important;
            }

            .col-left {
                margin-bottom: 8px;
            }

            /* Scrollable Tables on Mobile */
            .data-table {
                min-width: 480px;
            }

            .weather-box {
                min-height: auto;
                padding: 8px;
                margin-bottom: 8px;
            }

            /* Signature Table Stacking */
            .signature-table, 
            .signature-table tbody, 
            .signature-table tr, 
            .signature-table td {
                display: block !important;
                width: 100% !important;
                padding: 0 !important;
            }

            .signature-table td:first-child {
                margin-bottom: 8px;
            }

            .photo-grid {
                grid-template-columns: 1fr;
            }

            .photo-img {
                height: 220px;
            }

            /* Signature Form Controls */
            .form-grid {
                grid-template-columns: 1fr;
                gap: 10px;
            }

            .sign-action-card {
                padding: 16px 14px;
                border-radius: 10px;
                margin-top: 16px;
            }

            .canvas-container {
                height: 190px;
            }

            .form-group input {
                font-size: 15px; /* Prevents auto-zoom in mobile safari/chrome */
            }

            @if (!$isSigned)
                .mobile-floating-sign-btn {
                    display: inline-flex;
                }
            @endif
        }
    </style>
</head>
<body>

    <!-- STICKY TOP NAVBAR -->
    <header class="portal-navbar">
        <div class="nav-left">
            @if ($isSigned)
                <span class="doc-badge badge-signed">
                    <i class="mdi mdi-check-decagram"></i> Signed
                </span>
            @else
                <span class="doc-badge badge-pending">
                    <i class="mdi mdi-clock-outline"></i> Pending TTD
                </span>
            @endif
            <div>
                <div class="doc-info-title">{{ $report->job_name }}</div>
                <div class="doc-info-subtitle">
                    {{ $report->report_number ?: 'Daily Report' }} &bull; {{ $report->report_date ? $report->report_date->format('d/m/Y') : '-' }} (Hari ke-{{ $report->day_number ?: '-' }})
                </div>
            </div>
        </div>
        <div class="nav-actions">
            <a href="{{ route('project-reports.print', $report->id) }}" target="_blank" class="btn-portal btn-portal-secondary" title="Cetak / Print PDF">
                <i class="mdi mdi-printer"></i>
                <span class="btn-text">Cetak PDF</span>
            </a>
            @if (!$isSigned)
                <a href="#sign-card" class="btn-portal btn-portal-primary" title="Tanda Tangan">
                    <i class="mdi mdi-draw-pen"></i>
                    <span class="btn-text">Tanda Tangan</span>
                </a>
            @endif
        </div>
    </header>

    <!-- SIGNED BANNER / STATUS -->
    @if ($isSigned)
        <div class="sign-alert-banner">
            <div class="alert alert-success d-flex align-items-center justify-content-between flex-wrap gap-2 shadow-sm border-0" style="background: #dcfce7; color: #14532d; border-radius: 10px; padding: 14px 16px;">
                <div class="d-flex align-items-center gap-2.5">
                    <i class="mdi mdi-shield-check text-success fs-3"></i>
                    <div>
                        <div class="fw-bold" style="font-size: 13.5px;">Laporan Proyek Telah Resmi Ditandatangani</div>
                        <div style="font-size: 11.5px; color: #166534;">
                            Ditandatangani oleh <strong>{{ $report->customer_signer_name }}</strong>
                            @if ($report->customer_signer_position)
                                ({{ $report->customer_signer_position }})
                            @endif
                            <br class="d-block d-sm-none">
                            pada {{ \Carbon\Carbon::parse($report->customer_signed_at)->format('d F Y, H:i') }} WIB
                            @if ($report->customer_ip)
                                &bull; <span class="text-muted">IP: {{ $report->customer_ip }}</span>
                            @endif
                        </div>
                    </div>
                </div>
                <form action="{{ route('project-reports.customer.sign.reset', $report->sign_token) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus tanda tangan ini dan menandatangani ulang?');">
                    @csrf
                    <button type="submit" class="btn-portal btn-portal-danger btn-sm mt-1 mt-sm-0">
                        <i class="mdi mdi-refresh"></i> Tanda Tangan Ulang
                    </button>
                </form>
            </div>
        </div>
    @endif

    <!-- MAIN REPORT DOCUMENT CONTAINER -->
    <main class="portal-container">
        <div class="document-paper">
            <div class="report-page-inner">
                <div class="main-title">DAILY REPORT FORM</div>

                <!-- HEADER TABLE -->
                <table class="header-table">
                    <tr>
                        <td style="width: 25%;" class="header-title-cell">KONTRAKTOR PELAKSANA</td>
                        <td style="width: 50%;" class="header-title-cell">LAPORAN HARIAN</td>
                        <td style="width: 25%;" class="header-title-cell">FORMULIR</td>
                    </tr>
                    <tr>
                        <td style="vertical-align: middle; text-align: center; padding: 6px;">
                            <div class="logo-container">
                                <span class="reftech-brand">REFTECH</span>
                            </div>
                        </td>
                        <td style="padding: 0;">
                            <div class="sub-header-title">DATA PROYEK</div>
                            <div style="padding: 6px 8px;">
                                <table class="info-grid-table">
                                    <tr>
                                        <td class="label-col">Nama Pekerjaan</td>
                                        <td class="colon-col">:</td>
                                        <td><strong>{{ $report->job_name }}</strong></td>
                                    </tr>
                                    <tr>
                                        <td class="label-col">No. Perjanjian</td>
                                        <td class="colon-col">:</td>
                                        <td>{{ $report->contract_no ?: '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="label-col">Pemberi Tugas</td>
                                        <td class="colon-col">:</td>
                                        <td><strong>{{ $report->client ? $report->client->company : 'Client / Owner' }}</strong></td>
                                    </tr>
                                    <tr>
                                        <td class="label-col">Kontraktor</td>
                                        <td class="colon-col">:</td>
                                        <td>{{ $report->contractor_name }}</td>
                                    </tr>
                                </table>
                            </div>
                        </td>
                        <td style="padding: 6px 8px;">
                            <table class="info-grid-table">
                                <tr>
                                    <td style="width: 75px; font-weight: bold;">Hari</td>
                                    <td class="colon-col">:</td>
                                    <td>{{ $report->day_name ?: '-' }}</td>
                                </tr>
                                <tr>
                                    <td style="font-weight: bold;">Tanggal</td>
                                    <td class="colon-col">:</td>
                                    <td>{{ $report->report_date ? $report->report_date->format('d/m/Y') : '-' }}</td>
                                </tr>
                                <tr>
                                    <td style="font-weight: bold;">Hari Ke</td>
                                    <td class="colon-col">:</td>
                                    <td>{{ $report->day_number ?: '-' }}</td>
                                </tr>
                                <tr>
                                    <td style="font-weight: bold;">Sisa Waktu</td>
                                    <td class="colon-col">:</td>
                                    <td>{{ $report->days_remaining ?: '-' }}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>

                <!-- A. PEKERJAAN & B. MATERIAL -->
                <table class="two-col-table">
                    <tr>
                        <!-- A. PEKERJAAN -->
                        <td class="col-left">
                            <div class="section-header">A. &nbsp; PEKERJAAN</div>
                            <div class="table-responsive-wrapper">
                                <table class="data-table">
                                    <thead>
                                        <tr>
                                            <th style="width: 8%;">NO.</th>
                                            <th style="width: 47%;">JENIS PEKERJAAN</th>
                                            <th style="width: 25%;">LOKASI</th>
                                            <th style="width: 20%;">KETERANGAN</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($report->tasks as $idx => $task)
                                            <tr>
                                                <td class="text-center">{{ $idx + 1 }}.</td>
                                                <td>{{ $task->task_name }}</td>
                                                <td>{{ $task->location ?: '-' }}</td>
                                                <td>{{ $task->notes ?: '-' }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td class="text-center">&nbsp;</td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </td>

                        <!-- B. MATERIAL -->
                        <td class="col-right">
                            <div class="section-header">B. &nbsp; MATERIAL / BAHAN</div>
                            <div class="table-responsive-wrapper">
                                <table class="data-table">
                                    <thead>
                                        <tr>
                                            <th style="width: 10%;">NO.</th>
                                            <th style="width: 90%;">JENIS MATERIAL / BAHAN</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($report->materials as $idx => $mat)
                                            <tr>
                                                <td class="text-center">{{ $idx + 1 }}.</td>
                                                <td>{{ $mat->material_name }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td class="text-center">&nbsp;</td>
                                                <td></td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </td>
                    </tr>
                </table>

                <!-- C. PERALATAN -->
                <div class="section-header">C. &nbsp; PERALATAN</div>
                <div class="table-responsive-wrapper">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th style="width: 5%;">NO.</th>
                                <th style="width: 45%;">JENIS PERALATAN</th>
                                <th style="width: 15%;">JUMLAH</th>
                                <th style="width: 15%;">SATUAN</th>
                                <th style="width: 20%;">KETERANGAN</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($report->equipments as $idx => $eq)
                                <tr>
                                    <td class="text-center">{{ $idx + 1 }}.</td>
                                    <td>{{ $eq->equipment_name }}</td>
                                    <td class="text-center">{{ $eq->quantity }}</td>
                                    <td class="text-center">{{ $eq->unit }}</td>
                                    <td>{{ $eq->notes ?: '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="text-center">&nbsp;</td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- D. TENAGA KERJA & E. CUACA -->
                <table class="two-col-table">
                    <tr>
                        <!-- D. TENAGA KERJA -->
                        <td class="col-left">
                            <div class="section-header">D. &nbsp; TENAGA KERJA</div>
                            <div class="table-responsive-wrapper">
                                <table class="data-table">
                                    <thead>
                                        <tr>
                                            <th style="width: 10%;">NO.</th>
                                            <th style="width: 60%;">JABATAN</th>
                                            <th style="width: 30%;">JUMLAH</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($report->manpowers as $idx => $mp)
                                            <tr>
                                                <td class="text-center">{{ $idx + 1 }}.</td>
                                                <td>{{ $mp->position }}</td>
                                                <td class="text-center">{{ $mp->manpower_count ? $mp->manpower_count . ' Orang' : '-' }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td class="text-center">&nbsp;</td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </td>

                        <!-- E. CUACA -->
                        <td class="col-right">
                            <div class="section-header">E. &nbsp; CUACA</div>
                            <div class="weather-box">
                                <div style="font-weight: bold; text-align: center; border-bottom: 1px solid #000; padding-bottom: 3px; margin-bottom: 6px;">
                                    LAPORAN CUACA
                                </div>

                                <div class="weather-row">
                                    <span class="checkbox-square">{{ $report->weather_cerah ? '✓' : '' }}</span>
                                    <span class="weather-label">CERAH</span>
                                    <span class="weather-time">JAM : {{ $report->weather_cerah_time ?: '08:00 s/d 17:00' }}</span>
                                </div>

                                <div class="weather-row">
                                    <span class="checkbox-square">{{ $report->weather_hujan ? '✓' : '' }}</span>
                                    <span class="weather-label">HUJAN</span>
                                    <span class="weather-time">JAM : {{ $report->weather_hujan_time ?: '14:30 - 17:00' }}</span>
                                </div>

                                <div class="weather-row">
                                    <span class="checkbox-square">{{ $report->weather_mendung ? '✓' : '' }}</span>
                                    <span class="weather-label">MENDUNG</span>
                                    <span class="weather-time">JAM : {{ $report->weather_mendung_time ?: '14:00 - 17:00' }}</span>
                                </div>

                                <div class="weather-row">
                                    <span class="checkbox-square">{{ $report->weather_dll ? '✓' : '' }}</span>
                                    <span class="weather-label">DLL</span>
                                    <span class="weather-time">JAM : {{ $report->weather_dll_time ?: '.............. s/d ..............' }}</span>
                                </div>
                            </div>
                        </td>
                    </tr>
                </table>

                <!-- TEXT EVALUATION BOXES -->
                <div class="note-box" style="border-left: 3px solid #0284c7;">
                    <span class="note-box-title text-primary">PLANNING HARI INI:</span>
                    <div class="note-box-content">{{ $report->planning_today ?: '-' }}</div>
                </div>

                <div class="note-box" style="border-left: 3px solid #16a34a;">
                    <span class="note-box-title text-success">PENCAPAIAN HARI INI:</span>
                    <div class="note-box-content">{{ $report->achievement_today ?: '-' }}</div>
                </div>

                <div class="note-box" style="border-left: 3px solid #dc2626;">
                    <span class="note-box-title text-danger">KENDALA :</span>
                    <div class="note-box-content">{{ $report->issues_constraints ?: '-' }}</div>
                </div>

                <div class="note-box" style="border-left: 3px solid #6366f1;">
                    <span class="note-box-title text-info">RENCANA PEKERJAAN HARI BERIKUTNYA:</span>
                    <div class="note-box-content">{{ $report->next_plan ?: '-' }}</div>
                </div>

                <!-- SIGNATURES DISPLAY -->
                <table class="signature-table">
                    <tr>
                        <td style="padding-right: 4px;">
                            <div class="sign-box">
                                <div>
                                    <div class="sign-title">Pemberi Tugas</div>
                                    <div style="font-size: 8pt; color: #555;">{{ $report->client ? $report->client->company : 'Client / Owner' }}</div>
                                </div>
                                <div style="text-align: center; position: relative; min-height: 55px; display: flex; align-items: center; justify-content: center; margin: 4px 0;">
                                    @if ($report->customer_signature)
                                        <div style="position: relative; display: inline-block;">
                                            <img src="{{ asset($report->customer_signature) }}" class="sign-img" alt="Sign Client" />
                                            @if ($report->customer_signed_stamp)
                                                <img src="{{ asset($report->customer_signed_stamp) }}" alt="Stamp Client" style="position: absolute; right: -15px; top: -5px; max-height: 50px; opacity: 0.85; pointer-events: none;" />
                                            @endif
                                        </div>
                                    @elseif ($report->client_sign)
                                        <img src="{{ Storage::disk('public')->exists($report->client_sign) ? Storage::disk('public')->url($report->client_sign) : asset($report->client_sign) }}" class="sign-img" alt="Sign Client" />
                                    @else
                                        <span style="font-size: 8pt; color: #999; font-style: italic;">( Belum Ditandatangani )</span>
                                    @endif
                                </div>
                                <div class="sign-name">
                                    {{ $report->customer_signer_name ?: ($report->client_pic_name ?: '________________________') }}
                                    @if ($report->customer_signer_position)
                                        <span style="font-weight: normal; font-size: 7.5pt; text-decoration: none; display: block; color: #555;">({{ $report->customer_signer_position }})</span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td style="padding-left: 4px;">
                            <div class="sign-box">
                                <div>
                                    <div class="sign-title">Kontraktor Pelaksana</div>
                                    <div style="font-size: 8pt; font-weight: bold;">{{ $report->contractor_name }}</div>
                                </div>
                                <div style="text-align: center; min-height: 55px; display: flex; align-items: center; justify-content: center; margin: 4px 0;">
                                    @if ($report->contractor_sign)
                                        <img src="{{ Storage::disk('public')->exists($report->contractor_sign) ? Storage::disk('public')->url($report->contractor_sign) : asset($report->contractor_sign) }}" class="sign-img" alt="Sign Contractor" />
                                    @else
                                        <span style="font-size: 8pt; color: #999; font-style: italic;">( Belum Ditandatangani )</span>
                                    @endif
                                </div>
                                <div class="sign-name">
                                    {{ $report->contractor_pic_name ?: ($report->creator ? $report->creator->name : '________________________') }}
                                </div>
                            </div>
                        </td>
                    </tr>
                </table>

                <!-- FOTO DOKUMENTASI -->
                @if ($report->photos->count() > 0)
                    <div class="photo-section-title">
                        DOKUMENTASI FOTO PEKERJAAN LAPANGAN
                    </div>
                    <div class="photo-grid">
                        @foreach ($report->photos as $idx => $photo)
                            <div class="photo-item">
                                <img src="{{ $photo->url }}" class="photo-img" alt="Dokumentasi {{ $idx + 1 }}" />
                                <div class="photo-caption">
                                    {{ $photo->caption ?: 'Dokumentasi ' . ($idx + 1) }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <!-- INTERACTIVE SIGN CARD (Shown only if not signed yet) -->
        @if (!$isSigned)
            <div class="sign-action-card" id="sign-card">
                <div class="sign-card-title">
                    <i class="mdi mdi-draw-pen text-primary"></i>
                    Bubuhi Tanda Tangan Digital
                </div>
                <div class="sign-card-subtitle">
                    Silakan bubuhkan tanda tangan Anda pada kanvas di bawah ini dan lengkapi data identitas sebagai bukti pengesahan Daily Project Report.
                </div>

                <form id="form-sign-report" action="{{ route('project-reports.customer.sign.submit', $report->sign_token) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="signature_data" id="signature_data">

                    <!-- Canvas Signature Area -->
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark d-block mb-1">
                            Goreskan Tanda Tangan Anda di Sini: <span class="text-danger">*</span>
                        </label>
                        <div class="canvas-container" id="canvas-wrapper">
                            <canvas id="signature-pad"></canvas>
                            <div class="canvas-placeholder-hint" id="canvas-hint">
                                <i class="mdi mdi-gesture-tap"></i> Goreskan tanda tangan di area ini (Touchscreen / Mouse)
                            </div>
                        </div>
                        <div class="canvas-tools">
                            <button type="button" class="btn-canvas-tool" id="btn-clear-canvas">
                                <i class="mdi mdi-trash-can-outline"></i> Hapus &amp; Ulangi
                            </button>
                        </div>
                    </div>

                    <!-- Signer Details -->
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="signer_name">Nama Lengkap Penandatangan <span class="text-danger">*</span></label>
                            <input type="text" id="signer_name" name="signer_name" class="form-control"
                                placeholder="Contoh: Budi Santoso"
                                value="{{ old('signer_name', $report->client_pic_name) }}" required>
                        </div>
                        <div class="form-group">
                            <label for="signer_position">Jabatan / Posisi</label>
                            <input type="text" id="signer_position" name="signer_position" class="form-control"
                                placeholder="Contoh: Project Manager / Owner PIC"
                                value="{{ old('signer_position') }}">
                        </div>
                    </div>

                    <!-- Optional Stamp Upload -->
                    <div class="form-group mb-3">
                        <label for="stamp">Stempel Perusahaan / Cap Basah (Opsional)</label>
                        <input type="file" id="stamp" name="stamp" class="form-control" accept="image/png, image/jpeg, image/jpg">
                        <small class="text-muted mt-1 d-block" style="font-size: 11px;">Format file PNG / JPG, maksimal 3 MB. Disarankan latar transparan.</small>
                    </div>

                    <!-- Agreement Checkbox -->
                    <div class="form-check-custom">
                        <input type="checkbox" id="agreement" name="agreement" value="1" required>
                        <label for="agreement">
                            Saya dengan ini menyatakan bahwa seluruh rincian pekerjaan, material, peralatan, tenaga kerja, cuaca, dan catatan dalam Daily Report ini telah diperiksa dan disetujui sesuai dengan kondisi aktual di lapangan.
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" id="btn-submit" class="btn-portal btn-portal-primary w-100 justify-content-center py-2_5 fs-6 fw-bold" style="padding: 12px 18px; border-radius: 8px;">
                        <i class="mdi mdi-check-circle-outline me-1"></i> Setujui &amp; Tandatangani Daily Report
                    </button>
                </form>
            </div>

            <!-- Floating Mobile Button to Jump to Sign Card -->
            <a href="#sign-card" class="mobile-floating-sign-btn" id="floating-sign-btn">
                <i class="mdi mdi-draw-pen"></i> Tanda Tangan Sekarang
            </a>
        @endif
    </main>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
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
                var clientWidth = wrapper.clientWidth;
                var clientHeight = wrapper.clientHeight;
                
                if (clientWidth === 0 || clientHeight === 0) return;

                canvas.width = clientWidth * ratio;
                canvas.height = clientHeight * ratio;
                canvas.getContext("2d").scale(ratio, ratio);
                signaturePad.clear();
                if (hint) hint.style.display = 'flex';
                if (wrapper) wrapper.classList.remove('has-drawn');
            }

            window.addEventListener("resize", resizeCanvas);
            window.addEventListener("orientationchange", function () {
                setTimeout(resizeCanvas, 200);
            });
            setTimeout(resizeCanvas, 100);

            signaturePad.addEventListener("beginStroke", function () {
                if (hint) hint.style.display = 'none';
                if (wrapper) wrapper.classList.add('has-drawn');
            });

            var btnClear = document.getElementById('btn-clear-canvas');
            if (btnClear) {
                btnClear.addEventListener('click', function () {
                    signaturePad.clear();
                    if (hint) hint.style.display = 'flex';
                    if (wrapper) wrapper.classList.remove('has-drawn');
                });
            }

            // Hide floating button when sign card is in viewport
            var signCard = document.getElementById('sign-card');
            var floatingBtn = document.getElementById('floating-sign-btn');
            if (signCard && floatingBtn && 'IntersectionObserver' in window) {
                var observer = new IntersectionObserver(function (entries) {
                    entries.forEach(function (entry) {
                        if (entry.isIntersecting) {
                            floatingBtn.style.display = 'none';
                        } else {
                            if (window.innerWidth <= 768) {
                                floatingBtn.style.display = 'inline-flex';
                            }
                        }
                    });
                }, { threshold: 0.1 });
                observer.observe(signCard);
            }

            // Submit Form
            var form = document.getElementById('form-sign-report');
            var btnSubmit = document.getElementById('btn-submit');

            if (form) {
                form.addEventListener('submit', function (e) {
                    e.preventDefault();

                    if (signaturePad.isEmpty()) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Tanda Tangan Belum Ada',
                            text: 'Silakan goreskan tanda tangan Anda pada kotak yang disediakan.',
                            confirmButtonColor: '#4f46e5',
                        });
                        return;
                    }

                    var agreement = document.getElementById('agreement');
                    if (!agreement.checked) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Persetujuan Diperlukan',
                            text: 'Harap centang persetujuan konfirmasi Daily Report.',
                            confirmButtonColor: '#4f46e5',
                        });
                        return;
                    }

                    var dataUrl = signaturePad.toDataURL('image/png');
                    document.getElementById('signature_data').value = dataUrl;

                    var formData = new FormData(form);

                    btnSubmit.disabled = true;
                    btnSubmit.innerHTML = '<i class="mdi mdi-loading mdi-spin me-1"></i> Memproses Tanda Tangan...';

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
                        btnSubmit.innerHTML = '<i class="mdi mdi-check-circle-outline me-1"></i> Setujui &amp; Tandatangani Daily Report';

                        if (result.ok && result.data.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil Ditandatangani!',
                                text: 'Terima kasih. Daily Project Report telah resmi disetujui & ditandatangani.',
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
                                confirmButtonColor: '#4f46e5',
                            });
                        }
                    })
                    .catch(function (err) {
                        btnSubmit.disabled = false;
                        btnSubmit.innerHTML = '<i class="mdi mdi-check-circle-outline me-1"></i> Setujui &amp; Tandatangani Daily Report';
                        Swal.fire({
                            icon: 'error',
                            title: 'Terjadi Kesalahan',
                            text: 'Koneksi terputus. Silakan coba beberapa saat lagi.',
                            confirmButtonColor: '#4f46e5',
                        });
                    });
                });
            }
        });
    </script>
</body>
</html>
