@php
    $isSigned   = $bast->isSignedByCustomer();
    $isReftech  = $bast->entity === 'Reftech';
    $entityFullName = $isReftech ? 'PT Reftech Jaya Optima' : 'PT Kojisha Innotiv Indonesia';
    $docHeading = $bast->type === 'Rental' ? 'Berita Acara Serah Terima Unit Rental' : 'Berita Acara Serah Terima Pekerjaan';
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $docHeading }} — {{ $bast->no_bast ?: 'BAST #' . $bast->id }}</title>

    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/fonts/materialdesignicons.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/fonts/fontawesome.css" />

    <!-- Core Theme CSS (Matching App & Print layouts) -->
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/css/rtl/core.css" class="template-customizer-core-css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/css/rtl/theme-default.css" />
    <link rel="stylesheet" href="{{ asset('assets/css/demo.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.css">

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            background-color: #f1f5f9;
            color: #000000;
            font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            font-size: 14px;
            line-height: 1.5;
            -webkit-font-smoothing: antialiased;
            padding-bottom: 80px;
            margin: 0;
        }

        /* Top Sticky Navbar */
        .portal-navbar {
            position: sticky;
            top: 0;
            z-index: 1000;
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid #e2e8f0;
            padding: 12px 24px;
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
            gap: 5px;
            padding: 4px 12px;
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
            padding: 8px 18px;
            font-size: 13px;
            font-weight: 600;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s ease;
            border: 1px solid transparent;
        }

        .btn-portal-primary {
            background-color: #4f46e5;
            color: #ffffff;
        }

        .btn-portal-primary:hover {
            background-color: #4338ca;
            color: #ffffff;
        }

        .btn-portal-success {
            background-color: #16a34a;
            color: #ffffff;
        }

        .btn-portal-success:hover {
            background-color: #15803d;
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

        /* Document Wrapper */
        .doc-wrapper {
            max-width: 920px;
            margin: 24px auto;
            padding: 0 16px;
        }

        /* Document Sheet matching Print styling exactly */
        .invoice-print {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            color: #000000 !important;
            max-width: 920px;
            margin: 0 auto;
            padding: 35px 40px !important;
            background: #fff;
            box-shadow: 0 4px 24px rgba(0,0,0,0.07);
            border-radius: 8px;
        }

        .invoice-print p,
        .invoice-print span:not([class*="text-primary"]):not([style*="color: #696cff"]):not([style*="color:#696cff"]):not(.badge),
        .invoice-print td,
        .invoice-print th,
        .invoice-print strong,
        .invoice-print small {
            color: #000000;
        }

        .invoice-print .text-brand-primary {
            color: #4f46e5 !important;
            -webkit-text-fill-color: #4f46e5 !important;
        }

        .invoice-print .table,
        .invoice-print .table th,
        .invoice-print .table td,
        .invoice-print .table-bordered {
            border-color: #000000 !important;
            color: #000000 !important;
        }

        .invoice-print textarea.form-control {
            color: #000000 !important;
            -webkit-text-fill-color: #000000 !important;
            opacity: 1 !important;
            background-color: #ffffff !important;
            border: 1.5px solid #000000 !important;
        }

        /* Signature Section Styles (Strict 2-column flex layout) */
        .signature-row {
            display: flex !important;
            flex-direction: row !important;
            justify-content: space-between !important;
            align-items: flex-start !important;
            width: 100% !important;
        }

        .signature-col {
            width: 42% !important;
            text-align: center !important;
        }

        /* Banner Notification */
        .sign-alert-banner {
            max-width: 920px;
            margin: 16px auto 0;
            padding: 0 16px;
        }

        /* Signature Action Card Below Document (Exact Contract Sign pattern) */
        .sign-action-card {
            background: #ffffff;
            border: 2px solid #4f46e5;
            border-radius: 10px;
            box-shadow: 0 8px 30px rgba(79, 70, 229, 0.12);
            padding: 26px 30px;
            margin-top: 24px;
        }

        .sign-card-title {
            font-size: 17px;
            font-weight: 800;
            color: #0f172a;
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 4px;
        }

        .sign-card-subtitle {
            font-size: 12.5px;
            color: #64748b;
            margin-bottom: 18px;
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
            font-size: 13px;
            font-weight: 500;
            pointer-events: none;
            display: flex;
            align-items: center;
            gap: 6px;
            user-select: none;
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
            padding: 5px 12px;
            font-size: 12px;
            font-weight: 600;
            color: #475569;
            border-radius: 5px;
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
            gap: 16px;
            margin-bottom: 16px;
        }

        @media (max-width: 640px) {
            .form-grid {
                grid-template-columns: 1fr;
            }
        }

        .form-group label {
            display: block;
            font-size: 12.5px;
            font-weight: 600;
            color: #334155;
            margin-bottom: 6px;
        }

        .form-group input {
            width: 100%;
            padding: 9px 12px;
            font-size: 13.5px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            outline: none;
            transition: border-color 0.2s;
        }

        .form-group input:focus {
            border-color: #4f46e5;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.15);
        }

        .agreement-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 12px 14px;
            margin-bottom: 20px;
            display: flex;
            align-items: flex-start;
            gap: 10px;
            font-size: 12px;
            color: #334155;
            line-height: 1.45;
        }

        .agreement-box input[type="checkbox"] {
            margin-top: 3px;
            cursor: pointer;
            transform: scale(1.15);
        }

        .btn-submit-sign {
            width: 100%;
            background: #4f46e5;
            color: #ffffff;
            font-size: 14.5px;
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
            box-shadow: 0 4px 14px rgba(79, 70, 229, 0.25);
        }

        .btn-submit-sign:hover {
            background: #4338ca;
        }

        .btn-submit-sign:disabled {
            background: #94a3b8;
            cursor: not-allowed;
            box-shadow: none;
        }

        /* Success Card (When already signed) */
        .success-signed-card {
            background: #ffffff;
            border: 2px solid #22c55e;
            border-radius: 10px;
            box-shadow: 0 8px 30px rgba(34, 197, 94, 0.12);
            padding: 30px 24px;
            margin-top: 24px;
            text-align: center;
        }

        .success-signed-icon {
            width: 56px;
            height: 56px;
            background: #dcfce7;
            color: #16a34a;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 30px;
            margin: 0 auto 14px;
        }

        .success-signed-title {
            font-size: 18px;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 6px;
        }

        .success-signed-desc {
            font-size: 13px;
            color: #475569;
            max-width: 620px;
            margin: 0 auto;
            line-height: 1.55;
        }

        /* Print Override */
        @media print {
            @page {
                size: A4 portrait !important;
                margin: 15mm 15mm 15mm 15mm !important;
            }
            .portal-navbar, .sign-alert-banner, #section-sign, .d-print-none {
                display: none !important;
            }
            body {
                background: #fff !important;
                padding: 0 !important;
            }
            .doc-wrapper {
                max-width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
            }
            .invoice-print {
                box-shadow: none !important;
                border: none !important;
                padding: 0 !important;
                max-width: 100% !important;
                margin: 0 !important;
            }
        }
    </style>
</head>
<body>

    <!-- Sticky Navbar -->
    <nav class="portal-navbar">
        <div class="nav-left">
            @if ($isSigned)
                <span class="doc-badge badge-signed">
                    <i class="mdi mdi-check-circle"></i> Sudah Ditandatangani
                </span>
            @else
                <span class="doc-badge badge-pending">
                    <i class="mdi mdi-clock-outline"></i> Menunggu Tanda Tangan
                </span>
            @endif
            <div>
                <div class="doc-info-title">{{ $docHeading }}</div>
                <div class="doc-info-subtitle">{{ $bast->no_bast }} • {{ $bast->customer_name }}</div>
            </div>
        </div>

        <div class="nav-actions">
            @if (!$isSigned)
                <a href="#section-sign" class="btn-portal btn-portal-primary" id="btnScrollToSign">
                    <i class="mdi mdi-draw"></i>
                    <span>Bubuhi Tanda Tangan</span>
                </a>
            @else
                <button type="button" class="btn-portal btn-portal-danger btn-trigger-reset">
                    <i class="mdi mdi-delete-outline"></i>
                    <span>Hapus Tanda Tangan</span>
                </button>
                <button type="button" class="btn-portal btn-portal-secondary" onclick="window.print();">
                    <i class="mdi mdi-printer"></i>
                    <span>Cetak / Simpan PDF</span>
                </button>
            @endif
        </div>
    </nav>

    <!-- Signed Alert Banner -->
    @if ($isSigned)
        <div class="sign-alert-banner">
            <div class="alert alert-success d-flex align-items-center justify-content-between p-3 border-0 shadow-sm rounded-3 mb-0" style="background-color: #ecfdf5; color: #065f46;">
                <div class="d-flex align-items-center gap-2">
                    <i class="mdi mdi-check-decagram fs-4 text-success"></i>
                    <div>
                        <strong class="d-block" style="font-size: 13.5px;">Dokumen Telah Ditandatangani Customer</strong>
                        <span style="font-size: 12px;">Ditandatangani oleh <strong>{{ $bast->customer_signer_name }}</strong>{{ $bast->customer_signer_position ? ' (' . $bast->customer_signer_position . ')' : '' }} pada {{ $bast->customer_signed_at ? $bast->customer_signed_at->format('d F Y, H:i') : '-' }} WIB</span>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-sm btn-outline-danger btn-trigger-reset">
                        <i class="mdi mdi-delete-outline me-1"></i> Hapus Tanda Tangan
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-success" onclick="window.print();">
                        <i class="mdi mdi-printer me-1"></i> Cetak Dokumen
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Document Paper (Exact match with Print layout) -->
    <div class="doc-wrapper">
        <div class="invoice-print">

            {{-- Header --}}
            <div class="d-flex justify-content-between align-items-start pb-3 mb-3"
                style="border-bottom: 2px solid #cbd5e1; display: flex !important; flex-direction: row !important; justify-content: space-between !important; align-items: flex-start !important;">
                <div class="mb-0 pb-1">
                    @if ($isReftech)
                        <div class="d-flex svg-illustration align-items-center gap-2 mb-1" style="display: flex !important; align-items: center !important;">
                            <span class="app-brand-logo demo">
                                <span style="color: var(--bs-primary)">
                                    <img class="text-md"
                                        src="{{ url('https://reftech.id/wp-content/uploads/2021/10/Reftech-Logo-Hitam.png') }}"
                                        alt="Reftech Logo" width="55%">
                                </span>
                            </span>
                        </div>
                        <p class="mb-0 text-uppercase fw-bold text-brand-primary" style="font-size: 11.5px; color: #4f46e5 !important; letter-spacing: 0.5px; line-height: 1.2;">
                            COMPRESSED AIR SOLUTION
                        </p>
                        <p class="mb-1" style="font-size: 9.5px; font-weight: 600; color: #000000;">
                            Sales &nbsp;|&nbsp; Service &nbsp;|&nbsp; Rental &nbsp;|&nbsp; Measurement Air Audit
                        </p>
                        <div style="font-size: 9px; color: #000000; font-weight: 500;">
                            <i class="mdi mdi-certificate-outline me-1 text-primary"></i>
                            <span class="fw-bold" style="color: #696cff !important;">ISO Certified:</span> 
                            ISO 9001:2015 &nbsp;|&nbsp; ISO 14001:2015 &nbsp;|&nbsp; ISO 45001:2018
                        </div>
                    @else
                        <div class="d-flex svg-illustration align-items-center gap-2 mb-2" style="display: flex !important; align-items: center !important;">
                            <span class="app-brand-logo demo">
                                <span style="color: var(--bs-primary)">
                                    <img class="text-md" src="{{ asset('/asset') }}/logo/Kojisha-Log.png" alt="Kojisha Logo" width="55%">
                                </span>
                            </span>
                        </div>
                    @endif
                </div>
                <div class="text-end" style="text-align: right !important; padding-top: 8px;">
                    @if ($isReftech)
                        <p class="fw-bolder text-uppercase text-brand-primary" style="font-size: 16px; color: #4f46e5 !important; letter-spacing: 0.3px; line-height: 1.2; margin-bottom: 5px !important;">PT REFTECH JAYA OPTIMA</p>
                        <div style="font-size: 10px; line-height: 1.35; color: #000000; font-weight: 500;">
                            <p class="mb-0" style="color: #000000;">Taman Kopo Indah V, Soho Sommerville No. 31</p>
                            <p class="mb-0" style="color: #000000;">Bandung – Jawa Barat 40218</p>
                            <p class="mb-0 text-nowrap" style="white-space: nowrap; color: #000000;">
                                <i class="mdi mdi-phone-outline scaleX-n1-rtl me-1 mdi-14px text-primary"></i>022 54417653{{ '  |  ' }}<i class="mdi mdi-email-outline scaleX-n1-rtl me-1 mdi-14px text-primary"></i>admin@reftech.id{{ '  |  ' }}<i class="mdi mdi-web scaleX-n1-rtl me-1 mdi-14px text-primary"></i>www.reftech.id
                            </p>
                        </div>
                    @else
                        <p class="fw-bolder text-uppercase text-brand-primary" style="font-size: 16px; color: #4f46e5 !important; letter-spacing: 0.3px; line-height: 1.2; margin-bottom: 5px !important;">PT KOJISHA INNOTIV INDONESIA</p>
                        <div style="font-size: 10px; line-height: 1.35; color: #000000; font-weight: 500;">
                            <p class="mb-0" style="color: #000000;">Jl. Nancep No. 45A, Setu</p>
                            <p class="mb-0" style="color: #000000;">Cibitung - Kab. Bekasi 17320</p>
                            <p class="mb-0 text-nowrap" style="white-space: nowrap; color: #000000;">
                                <i class="mdi mdi-phone-outline scaleX-n1-rtl me-1 mdi-14px text-primary"></i>+62 812-1000-0997
                                {{ '   ' }}<i class="mdi mdi-email-outline scaleX-n1-rtl me-1 mdi-14px text-primary"></i>admin@kojisha.com
                            </p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Title --}}
            <div class="text-center mb-4" style="text-align: center !important; margin-bottom: 1.5rem !important;">
                <h4 class="fw-bold mb-1 text-uppercase text-brand-primary" style="color: #4f46e5 !important; font-size: 18px; letter-spacing: 0.5px; font-weight: 700 !important; margin-bottom: 0.25rem !important;">
                    {{ $bast->type === 'Rental' ? 'Berita Acara Serah Terima Unit Rental' : 'Berita Acara Serah Terima Pekerjaan' }}
                </h4>
                <div class="fw-bold text-brand-primary" style="font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; color: #4f46e5 !important; font-size: 18px; letter-spacing: 0.5px; font-weight: 700 !important;">{{ $bast->no_bast }}</div>
            </div>

            @if ($bast->type === 'Rental')
                <p class="mb-3 text-dark-content" style="font-size: 14px; line-height: 1.6; color: #000000; margin-bottom: 1rem !important;">
                    Bersama dengan ini kami <strong class="text-uppercase" style="color: #000000;">{{ $entityFullName }}</strong>, telah melakukan pengiriman, instalasi, dan commissioning serta <strong style="color: #000000;">MENYERAHKAN UNIT RENTAL</strong> dalam kondisi baik dan siap beroperasi kepada <strong style="color: #000000;">{{ $bast->customer_name }}</strong> untuk unit sbb :
                </p>
            @else
                <p class="mb-3 text-dark-content" style="font-size: 14px; line-height: 1.6; color: #000000; margin-bottom: 1rem !important;">
                    Bersama dengan ini kami <strong class="text-uppercase" style="color: #000000;">{{ $entityFullName }}</strong>, telah menyelesaikan pekerjaan hingga
                    <strong style="color: #000000;">SELESAI</strong> untuk pekerjaan sbb :
                </p>
            @endif

            <div class="border rounded p-3 text-center fw-bold text-uppercase mb-3" style="font-size: 18px; color: #000000; border: 1.5px solid #000000 !important; text-align: center !important; padding: 1rem !important; border-radius: 0.375rem !important; font-weight: 700 !important; margin-bottom: 1rem !important;">
                {{ $bast->work_title }}
            </div>

            <table class="mb-2" style="font-size: 14px; width: 100%; color: #000000; border-collapse: collapse;">
                @if ($bast->type === 'Rental')
                    <tr>
                        <td style="width: 250px; padding: 5px 0; color: #000000;">Tanggal Commissioning</td>
                        <td style="width: 20px; padding: 5px 0; color: #000000;">:</td>
                        <td style="padding: 5px 0; color: #000000; font-weight: 600;">
                            @if ($bast->work_date)
                                {{ $bast->work_date->format('d-m-Y') }}
                            @else
                                <span style="display: inline-block; min-width: 220px; border-bottom: 1.5px dotted #000; height: 16px; vertical-align: middle;"></span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 5px 0; color: #000000;">Masa Rental</td>
                        <td style="padding: 5px 0; color: #000000;">:</td>
                        <td style="padding: 5px 0; color: #000000; font-weight: 600;">
                            @if ($bast->rental_start_date && $bast->rental_end_date)
                                {{ $bast->rental_start_date->format('d-m-Y') }} s/d {{ $bast->rental_end_date->format('d-m-Y') }}
                            @elseif ($bast->rental_start_date)
                                {{ $bast->rental_start_date->format('d-m-Y') }} s/d <span style="display: inline-block; min-width: 130px; border-bottom: 1.5px dotted #000; height: 16px; vertical-align: middle;"></span>
                            @elseif ($bast->rental_end_date)
                                <span style="display: inline-block; min-width: 130px; border-bottom: 1.5px dotted #000; height: 16px; vertical-align: middle;"></span> s/d {{ $bast->rental_end_date->format('d-m-Y') }}
                            @else
                                <span style="display: inline-block; min-width: 130px; border-bottom: 1.5px dotted #000; height: 16px; vertical-align: middle;"></span> s/d <span style="display: inline-block; min-width: 130px; border-bottom: 1.5px dotted #000; height: 16px; vertical-align: middle;"></span>
                            @endif
                        </td>
                    </tr>
                @else
                    <tr>
                        <td style="width: 250px; padding: 5px 0; color: #000000;">Tanggal Pekerjaan</td>
                        <td style="width: 20px; padding: 5px 0; color: #000000;">:</td>
                        <td style="padding: 5px 0; color: #000000; font-weight: 600;">
                            @if ($bast->work_date)
                                {{ $bast->work_date->format('d-m-Y') }}
                            @else
                                <span style="display: inline-block; min-width: 220px; border-bottom: 1.5px dotted #000; height: 16px; vertical-align: middle;"></span>
                            @endif
                        </td>
                    </tr>
                @endif
                <tr>
                    <td style="padding: 5px 0; color: #000000;">Sesuai PO/ kontrak no.</td>
                    <td style="padding: 5px 0; color: #000000;">:</td>
                    <td style="padding: 5px 0; color: #000000; font-weight: 600;">{{ $bast->po_number ?: '-' }}</td>
                </tr>
                <tr>
                    <td style="padding: 5px 0; color: #000000;">Terhadap unit-unit sebagai berikut</td>
                    <td style="padding: 5px 0; color: #000000;">:</td>
                    <td style="padding: 5px 0; color: #000000;"></td>
                </tr>
            </table>

            <table class="table table-bordered mb-3" style="font-size: 14px; color: #000000; border: 1.5px solid #000000; width: 100%; border-collapse: collapse; margin-bottom: 1rem !important;">
                <thead>
                    <tr style="background-color: #f8f9fa;">
                        <th style="width: 8%; color: #000000; font-weight: 700; border: 1px solid #000000; text-align: center; padding: 8px;">NO.</th>
                        <th style="color: #000000; font-weight: 700; border: 1px solid #000000; padding: 8px;">UNIT</th>
                        <th style="color: #000000; font-weight: 700; border: 1px solid #000000; padding: 8px;">SERIAL NO.</th>
                        <th style="width: 15%; color: #000000; font-weight: 700; border: 1px solid #000000; text-align: center; padding: 8px;">JUMLAH</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($bast->units as $unit)
                        <tr>
                            <td style="color: #000000; border: 1px solid #000000; text-align: center; padding: 8px;">{{ $loop->iteration }}</td>
                            <td style="color: #000000; border: 1px solid #000000; padding: 8px;">{{ $unit->unit_name }}</td>
                            <td style="color: #000000; border: 1px solid #000000; padding: 8px;">{{ $unit->serial_no ?: '-' }}</td>
                            <td style="color: #000000; border: 1px solid #000000; text-align: center; padding: 8px;">{{ $unit->qty }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <p class="mb-2" style="font-size: 14px; font-weight: 700; color: #000000; margin-bottom: 0.5rem !important;">
                Hasil Pengecekan Pada Saat Test Running :
            </p>

            <div class="mb-3" style="margin-bottom: 1rem !important;">
                <textarea class="form-control" rows="4" style="font-size: 14px; line-height: 1.5; border: 1.5px solid #000000; width: 100%; resize: none; background: #fff; color: #000000; font-weight: 500; border-radius: 0.375rem; padding: 10px;" readonly>{{ $bast->test_running_result }}</textarea>
            </div>

            @if ($bast->type === 'Rental')
                <p class="mb-1" style="font-size: 14px; color: #000000; margin-bottom: 0.25rem !important;">
                    Demikian <strong style="color: #000000;">BERITA ACARA SERAH TERIMA UNIT RENTAL</strong> ini di tanda tangani oleh kedua belah pihak :
                </p>

                <table class="table-borderless mb-3 ms-2" style="font-size: 14px; line-height: 1.6; width: auto; color: #000000; margin-bottom: 1rem !important;">
                    <tr>
                        <td style="width: 220px; padding: 2px 0; color: #000000;">• Yang Menyerahkan (Penyedia)</td>
                        <td style="width: 20px; padding: 2px 0; color: #000000;">:</td>
                        <td style="padding: 2px 0; color: #000000;"><strong style="color: #000000;">{{ $entityFullName }}</strong></td>
                    </tr>
                    <tr>
                        <td style="width: 220px; padding: 2px 0; color: #000000;">• Yang Menerima (Penyewa)</td>
                        <td style="width: 20px; padding: 2px 0; color: #000000;">:</td>
                        <td style="padding: 2px 0; color: #000000;"><strong style="color: #000000;">{{ $bast->customer_name }}</strong></td>
                    </tr>
                </table>

                <p class="mb-3" style="font-size: 14px; line-height: 1.6; color: #000000; margin-bottom: 1rem !important;">
                    Dengan ini unit rental tersebut di atas dinyatakan telah <strong style="color: #000000;">DITERIMA DALAM KONDISI BAIK &amp; SIAP BEROPERASI</strong>. Unit tetap merupakan milik/aset <strong style="color: #000000;">{{ $entityFullName }}</strong> dan akan diambil/ditarik kembali setelah masa sewa/rental berakhir.
                </p>
            @else
                <p class="mb-1" style="font-size: 14px; color: #000000; margin-bottom: 0.25rem !important;">
                    Demikian <strong style="color: #000000;">BERITA ACARA SERAH TERIMA PEKERJAAN</strong> ini di tanda tangani oleh kedua belah pihak :
                </p>

                <table class="table-borderless mb-3 ms-2" style="font-size: 14px; line-height: 1.6; width: auto; color: #000000; margin-bottom: 1rem !important;">
                    <tr>
                        <td style="width: 180px; padding: 2px 0; color: #000000;">• Pelaksana pekerjaan</td>
                        <td style="width: 20px; padding: 2px 0; color: #000000;">:</td>
                        <td style="padding: 2px 0; color: #000000;"><strong style="color: #000000;">{{ $entityFullName }}</strong></td>
                    </tr>
                    <tr>
                        <td style="width: 180px; padding: 2px 0; color: #000000;">• Pemberi pekerjaan</td>
                        <td style="width: 20px; padding: 2px 0; color: #000000;">:</td>
                        <td style="padding: 2px 0; color: #000000;"><strong style="color: #000000;">{{ $bast->customer_name }}</strong></td>
                    </tr>
                </table>

                <p class="mb-3" style="font-size: 14px; line-height: 1.6; color: #000000; margin-bottom: 1rem !important;">
                    Dengan ini segala hal yang berhubungan dengan pekerjaan tersebut diatas dinyatakan
                    <strong style="color: #000000;">SELESAI</strong>.
                </p>
            @endif

            {{-- Signature Section (Strict 2-column flex layout identical to Print layout) --}}
            <div class="signature-row pt-4 mt-2 signature-section" style="font-size: 14px; color: #000000; display: flex !important; flex-direction: row !important; justify-content: space-between !important; align-items: flex-start !important; width: 100% !important; margin-top: 1.5rem !important; padding-top: 1rem !important;">
                
                {{-- Reftech / Kojisha Column --}}
                <div class="signature-col" style="width: 42% !important; text-align: center !important;">
                    <p class="fw-bold text-uppercase mb-0" style="font-size: 14px; color: #000000; font-weight: 700 !important; margin-bottom: 0 !important;">{{ $entityFullName }}</p>
                    @if ($bast->sign)
                        <div class="d-flex align-items-center justify-content-center" style="height: 85px; margin: 5px 0; display: flex !important; align-items: center !important; justify-content: center !important;">
                            <img src="{{ asset($bast->sign) }}" alt="Hand Sign" style="max-height: 80px; max-width: 175px; object-fit: contain;">
                        </div>
                        <p class="mb-0 fw-bold" style="font-size: 14px; color: #000000; font-weight: 700 !important; margin-bottom: 0 !important;">( <u>{{ $isReftech ? 'Ariep Rachman' : 'Dedeh Sulastri' }}</u> )</p>
                    @else
                        <div style="height: 95px;"></div>
                        <p class="mb-0" style="font-size: 14px; color: #000000; font-weight: 600; margin-bottom: 0 !important;">( ........................................ )</p>
                    @endif
                </div>

                {{-- Customer Column --}}
                <div class="signature-col" style="width: 42% !important; text-align: center !important;">
                    <p class="fw-bold text-uppercase mb-0" style="font-size: 14px; color: #000000; font-weight: 700 !important; margin-bottom: 0 !important;">{{ $bast->customer_name }}</p>
                    @if ($bast->customer_signature)
                        <div class="d-flex align-items-center justify-content-center position-relative" style="height: 85px; margin: 5px 0; display: flex !important; align-items: center !important; justify-content: center !important; position: relative !important;">
                            <img src="{{ asset($bast->customer_signature) }}" alt="Customer Signature" style="max-height: 80px; max-width: 175px; object-fit: contain; z-index: 2;">
                            @if ($bast->customer_signed_stamp)
                                <img src="{{ asset($bast->customer_signed_stamp) }}" alt="Stamp" style="position: absolute; max-height: 70px; opacity: 0.75; z-index: 1; transform: rotate(-5deg);">
                            @endif
                        </div>
                        <p class="mb-0 fw-bold" style="font-size: 14px; color: #000000; font-weight: 700 !important; margin-bottom: 0 !important;">( <u>{{ $bast->customer_signer_name }}</u> )</p>
                        @if ($bast->customer_signer_position)
                            <small class="d-block" style="font-size: 11px; color: #000000; display: block !important;">{{ $bast->customer_signer_position }}</small>
                        @endif
                    @else
                        <div style="height: 95px;"></div>
                        <p class="mb-0" style="font-size: 14px; color: #000000; font-weight: 600; margin-bottom: 0 !important;">( ........................................ )</p>
                    @endif
                </div>

            </div>

        </div>

        {{-- Interactive Customer Signature Section Below Document --}}
        <div id="section-sign">
            @if ($isSigned)
                <div class="success-signed-card">
                    <div class="success-signed-icon">
                        <i class="mdi mdi-check-bold"></i>
                    </div>
                    <div class="success-signed-title">Berita Acara Serah Terima (BAST) Telah Ditandatangani</div>
                    <div class="success-signed-desc">
                        Dokumen ini telah resmi disetujui dan ditandatangani secara digital oleh <strong>{{ $bast->customer_signer_name }}</strong>{{ $bast->customer_signer_position ? ' (' . $bast->customer_signer_position . ')' : '' }} pada {{ $bast->customer_signed_at ? $bast->customer_signed_at->format('d F Y, H:i') : '-' }} WIB.
                    </div>
                    <div style="display: flex; gap: 10px; justify-content: center; flex-wrap: wrap; margin-top: 20px;">
                        <button type="button" class="btn-portal btn-portal-secondary" onclick="window.print();" style="padding: 10px 20px; font-size: 13.5px;">
                            <i class="mdi mdi-printer"></i> Cetak / Simpan PDF
                        </button>
                        <button type="button" class="btn-portal btn-portal-danger btn-trigger-reset" style="padding: 10px 18px; font-size: 13.5px;">
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

                    <form id="form-sign-bast" action="{{ route('bast.customer.sign.submit', $bast->sign_token) }}" method="POST" enctype="multipart/form-data">
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
                                <input type="text" id="signer_name" name="signer_name" placeholder="Contoh: Bpk. Hendra Gunawan" required>
                            </div>
                            <div class="form-group">
                                <label for="signer_position">Jabatan / Posisi <span style="color: #64748b; font-weight: normal;">(Opsional)</span></label>
                                <input type="text" id="signer_position" name="signer_position" placeholder="Contoh: Site Manager / Maintenance Head">
                            </div>
                        </div>

                        <div class="form-group" style="margin-bottom: 16px;">
                            <label for="stamp">Upload Stempel Perusahaan (Opsional)</label>
                            <input type="file" id="stamp" name="stamp" accept="image/png, image/jpeg, image/jpg">
                            <small class="text-muted d-block mt-1" style="font-size: 11.5px;">Format JPG / PNG transparan, maks 3 MB.</small>
                        </div>

                        <div class="agreement-box">
                            <input type="checkbox" id="agreement" name="agreement" value="1" required>
                            <label for="agreement" style="cursor: pointer; margin-bottom: 0;">
                                Saya menyatakan bahwa unit / pekerjaan yang tercantum pada Berita Acara Serah Terima ini telah diperiksa, diuji coba / commissioning, dan <strong>diterima dengan baik</strong> oleh pihak <strong>{{ $bast->customer_name }}</strong>.
                            </label>
                        </div>

                        <button type="submit" class="btn-submit-sign" id="btn-submit">
                            <i class="mdi mdi-check-circle-outline"></i> Setujui &amp; Tandatangani BAST
                        </button>
                    </form>
                </div>
            @endif
        </div>

    </div>

    <!-- Scripts -->
    <script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/bootstrap.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Smooth scroll to sign section
            var btnScroll = document.getElementById('btnScrollToSign');
            if (btnScroll) {
                btnScroll.addEventListener('click', function(e) {
                    e.preventDefault();
                    var target = document.getElementById('section-sign');
                    if (target) {
                        target.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                });
            }

            // Handler Reset / Hapus Tanda Tangan
            $(document).on('click', '.btn-trigger-reset', function(e) {
                e.preventDefault();
                Swal.fire({
                    title: 'Hapus Tanda Tangan?',
                    text: 'Tanda tangan customer dan stempel akan dihapus. Anda dapat membubuhkan tanda tangan baru setelahnya.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: '<i class="mdi mdi-delete-outline me-1"></i> Ya, Hapus &amp; TTD Ulang',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then(function(result) {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Mereset Tanda Tangan...',
                            text: 'Mohon tunggu sebentar...',
                            allowOutsideClick: false,
                            didOpen: function() {
                                Swal.showLoading();
                            }
                        });

                        fetch("{{ route('bast.customer.sign.reset', $bast->sign_token) }}", {
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

            // Canvas Signature Pad
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

            var btnClear = document.getElementById('btn-clear-canvas');
            if (btnClear) {
                btnClear.addEventListener('click', function () {
                    signaturePad.clear();
                    hint.style.display = 'flex';
                    wrapper.classList.remove('has-drawn');
                });
            }

            // Submit Form
            var form = document.getElementById('form-sign-bast');
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
                            text: 'Harap centang persetujuan serah terima BAST.',
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
                        btnSubmit.innerHTML = '<i class="mdi mdi-check-circle-outline"></i> Setujui &amp; Tandatangani BAST';

                        if (result.ok && result.data.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil Ditandatangani!',
                                text: 'Terima kasih. Berita Acara Serah Terima telah resmi ditandatangani.',
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
                        btnSubmit.innerHTML = '<i class="mdi mdi-check-circle-outline"></i> Setujui &amp; Tandatangani BAST';
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
