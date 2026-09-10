@php
    $isSigned = $delivery->isSignedByCustomer();
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Surat Jalan (Delivery Order) — #{{ $doNumber }}</title>

    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/fonts/materialdesignicons.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/fonts/fontawesome.css" />

    <!-- Core Theme CSS -->
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
        .invoice-print td,
        .invoice-print th,
        .invoice-print strong,
        .invoice-print small {
            color: #000000;
        }

        .invoice-print .table,
        .invoice-print .table th,
        .invoice-print .table td,
        .invoice-print .table-bordered {
            border-color: #000000 !important;
            color: #000000 !important;
        }

        /* Banner Notification */
        .sign-alert-banner {
            max-width: 920px;
            margin: 16px auto 0;
            padding: 0 16px;
        }

        /* Signature Action Card Below Document */
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
            font-size: 13px;
            color: #64748b;
            margin-bottom: 20px;
        }

        /* Canvas Wrapper */
        .canvas-container {
            position: relative;
            background: #ffffff;
            border: 2px dashed #94a3b8;
            border-radius: 8px;
            width: 100%;
            height: 220px;
            touch-action: none;
            cursor: crosshair;
            transition: border-color 0.2s;
        }

        .canvas-container:hover,
        .canvas-container.has-drawn {
            border-color: #4f46e5;
        }

        .canvas-container canvas {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border-radius: 6px;
        }

        .canvas-placeholder-hint {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: #94a3b8;
            pointer-events: none;
            font-size: 14px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 6px;
            user-select: none;
        }

        .canvas-tools {
            display: flex;
            justify-content: flex-end;
            margin-top: 8px;
            margin-bottom: 18px;
        }

        .btn-canvas-tool {
            background: transparent;
            border: none;
            color: #64748b;
            font-size: 12.5px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            cursor: pointer;
            padding: 4px 8px;
            border-radius: 4px;
        }

        .btn-canvas-tool:hover {
            color: #dc2626;
            background-color: #fee2e2;
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
            .invoice-print {
                padding: 20px 18px !important;
            }
            .sign-action-card {
                padding: 18px;
            }
            .portal-navbar {
                padding: 10px 14px;
            }
        }

        .form-group label {
            display: block;
            font-size: 12.5px;
            font-weight: 600;
            color: #334155;
            margin-bottom: 6px;
        }

        .form-group input[type="text"],
        .form-group input[type="file"] {
            width: 100%;
            padding: 9px 12px;
            font-size: 13.5px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            color: #0f172a;
            outline: none;
            transition: all 0.2s;
            background: #ffffff;
        }

        .form-group input[type="text"]:focus {
            border-color: #4f46e5;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }

        .agreement-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 12px 14px;
            margin-bottom: 20px;
            display: flex;
            align-items: flex-start;
            gap: 10px;
            font-size: 12.5px;
            color: #475569;
            line-height: 1.5;
        }

        .agreement-box input[type="checkbox"] {
            margin-top: 3px;
            width: 16px;
            height: 16px;
            cursor: pointer;
            accent-color: #4f46e5;
        }

        .btn-submit-sign {
            width: 100%;
            background: #4f46e5;
            color: #ffffff;
            font-size: 14.5px;
            font-weight: 700;
            padding: 12px;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.25);
            transition: all 0.2s;
        }

        .btn-submit-sign:hover {
            background: #4338ca;
            box-shadow: 0 6px 16px rgba(79, 70, 229, 0.35);
            transform: translateY(-1px);
        }

        /* Success Card when already signed */
        .success-signed-card {
            background: #f0fdf4;
            border: 2px solid #86efac;
            border-radius: 10px;
            padding: 24px;
            text-align: center;
            margin-top: 24px;
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
            font-size: 28px;
            margin: 0 auto 12px;
        }

        .success-signed-title {
            font-size: 17px;
            font-weight: 800;
            color: #166534;
            margin-bottom: 6px;
        }

        .success-signed-desc {
            font-size: 13px;
            color: #15803d;
            max-width: 580px;
            margin: 0 auto;
        }

        @media print {
            .portal-navbar,
            .sign-alert-banner,
            .sign-action-card,
            .success-signed-card {
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
                padding: 0 !important;
            }
        }
    </style>
</head>
<body>

    <!-- Top Sticky Navbar -->
    <header class="portal-navbar">
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
                <div class="doc-info-title">Surat Jalan #{{ $doNumber }}</div>
                <div class="doc-info-subtitle">{{ $client->company ?? 'Customer' }} &bull; {{ $delivery->date ? \Carbon\Carbon::parse($delivery->date)->format('d M Y') : 'Tanggal -' }}</div>
            </div>
        </div>

        <div class="nav-actions">
            @if (!$isSigned)
                <a href="#section-sign" class="btn-portal btn-portal-primary" id="btnScrollToSign">
                    <i class="mdi mdi-draw"></i>
                    <span>Tanda Tangani</span>
                </a>
            @else
                <button type="button" class="btn-portal btn-portal-secondary" onclick="window.print();">
                    <i class="mdi mdi-printer"></i>
                    <span>Cetak / PDF</span>
                </button>
            @endif
        </div>
    </header>

    <!-- Notification Banner -->
    <div class="sign-alert-banner">
        @if ($isSigned)
            <div class="alert alert-success d-flex align-items-center mb-0" role="alert" style="background: #f0fdf4; border-color: #bbf7d0; color: #166534; border-radius: 8px;">
                <i class="mdi mdi-check-decagram fs-4 me-2"></i>
                <div style="font-size: 13px;">
                    <strong>Surat Jalan telah resmi ditandatangani secara digital</strong> oleh <strong>{{ $delivery->customer_signer_name }}</strong>{{ $delivery->customer_signer_position ? ' (' . $delivery->customer_signer_position . ')' : '' }} pada {{ $delivery->customer_signed_at ? $delivery->customer_signed_at->format('d/m/Y H:i') : '-' }} WIB.
                </div>
            </div>
        @else
            <div class="alert alert-warning d-flex align-items-center mb-0" role="alert" style="background: #fffbeb; border-color: #fde68a; color: #92400e; border-radius: 8px;">
                <i class="mdi mdi-information-outline fs-4 me-2"></i>
                <div style="font-size: 13px;">
                    Silakan periksa rincian barang yang dikirimkan pada Surat Jalan di bawah ini, kemudian bubuhi <strong>tanda tangan digital</strong> pada kotak yang tersedia di bagian bawah dokumen.
                </div>
            </div>
        @endif
    </div>

    <!-- Main Document Container -->
    <div class="doc-wrapper">

        <!-- Document Sheet (Faithfully matching Delivery Order format) -->
        <div class="invoice-print">
            
            {{-- Header --}}
            <div class="d-flex justify-content-between flex-xl-row flex-md-column flex-sm-row flex-column mb-0">
                <div class="mb-xl-0 pb-1">
                    @if ($isKojisha)
                        <div class="d-flex svg-illustration align-items-center gap-2 mb-2">
                            <span class="app-brand-logo demo">
                                <img src="{{ asset('/asset') }}/logo/Logo-update-size.png" alt="Kojisha Logo" width="160">
                            </span>
                        </div>
                        <p class="mb-1 fw-bolder" style="font-size: 14.5px;">PT Kojisha Innotiv Indonesia</p>
                        <div style="font-size: 11.5px; color: #475569; line-height: 1.4;">
                            <p class="mb-0">Jl. Nancep No. 45A, Setu</p>
                            <p class="mb-0">Cibitung - Kab. Bekasi 17320</p>
                            <p class="mb-0"><i class="mdi mdi-phone-outline me-1"></i>+62 812-1000-0997 &nbsp;|&nbsp; <i class="mdi mdi-email-outline me-1"></i>admin@kojisha.com</p>
                        </div>
                    @else
                        <div class="d-flex svg-illustration align-items-center gap-2 mb-2">
                            <span class="app-brand-logo demo">
                                <span style="color: var(--bs-primary)">
                                    <img src="{{ asset('/asset') }}/logo/Reftech-Log.png" alt="Reftech Logo" width="160">
                                </span>
                            </span>
                        </div>
                        <p class="mb-1 fw-bolder" style="font-size: 14.5px;">PT Reftech Jaya Optima</p>
                        <div style="font-size: 11.5px; color: #475569; line-height: 1.4;">
                            <p class="mb-0">Taman Kopo Indah V, Soho Sommerville No. 31</p>
                            <p class="mb-0">Bandung – Jawa Barat 40218</p>
                            <p class="mb-0"><i class="mdi mdi-phone-outline me-1"></i>022 54417653 &nbsp;|&nbsp; <i class="mdi mdi-email-outline me-1"></i>accounting@reftech.id &nbsp;|&nbsp; <i class="mdi mdi-web me-1"></i>www.reftech.id</p>
                        </div>
                    @endif
                </div>
                <div class="text-end">
                    <h3 class="fw-bold mb-1" style="letter-spacing: 2px; color: #4f46e5; font-size: 22px;">DELIVERY ORDER</h3>
                    <p class="mb-0 fw-semibold" style="font-size: 13.5px; color: #1e293b;">#{{ $doNumber }}</p>
                    <span class="badge {{ strtolower($delivery->type ?? '') === 'ekspedisi' ? 'bg-label-info' : 'bg-label-primary' }} mt-1">
                        <i class="mdi {{ strtolower($delivery->type ?? '') === 'ekspedisi' ? 'mdi-package-variant-closed' : 'mdi-account-hard-hat' }} me-1"></i>
                        {{ ucfirst($delivery->type ?? 'Pengiriman Langsung') }}
                    </span>
                </div>
            </div>

            {{-- Accent Divider --}}
            <div style="height: 3px; background: linear-gradient(90deg, #4f46e5 0%, #818cf8 60%, #e2e8f0 100%); border-radius: 2px; margin: 14px 0 18px;"></div>

            {{-- Info Cards (Deliver To & Shipment Info) --}}
            <div style="display: flex; align-items: stretch; gap: 12px; margin-bottom: 22px; font-size: 12px;">
                <div style="flex: 1; display: flex; flex-direction: column; border: 1.5px solid #000; border-radius: 6px; padding: 12px 14px; background: #fafafa;">
                    <p class="mb-1 fw-bold text-uppercase" style="font-size: 10.5px; letter-spacing: .5px; color: #555;">Deliver To / Penerima</p>
                    <p class="mb-1 fw-bold" style="font-size: 14px; color: #111;">{{ $client->company ?? '-' }}</p>
                    <p class="mb-0" style="font-size: 12px; color: #222; line-height: 1.45;">
                        <i class="mdi mdi-map-marker-outline me-1" style="font-size: 12px; color: #dc2626;"></i><span>{{ $address }}</span>
                    </p>
                </div>
                <div style="min-width: 250px; display: flex; flex-direction: column; border: 1.5px solid #000; border-radius: 6px; padding: 12px 14px; background: #fafafa;">
                    <p class="mb-1 fw-bold text-uppercase" style="font-size: 10.5px; letter-spacing: .5px; color: #555;">Shipment Info</p>
                    <p class="mb-1 fw-semibold" style="font-size: 12px; color: #222;">
                        <i class="mdi mdi-clipboard-text-outline me-1 text-primary"></i>PO / Quote: <span class="fw-bold">{{ $unitQuote->po_number ?: ($unitQuote->no_quote ?? ($invoice->no_invoice ?? '-')) }}</span>
                    </p>
                    <p class="mb-1 fw-semibold" style="font-size: 12px; color: #222;">
                        <i class="mdi mdi-calendar-outline me-1 text-primary"></i>Tanggal: 
                        <span class="fw-bold">{{ $delivery->date ? \Carbon\Carbon::parse($delivery->date)->format('d-m-Y') : '-' }}</span>
                    </p>
                    <p class="mb-0 fw-semibold" style="font-size: 12px; color: #222;">
                        <i class="mdi mdi-truck-outline me-1 text-primary"></i>Metode: <span class="fw-bold">{{ ucfirst($delivery->type ?? 'Ekspedisi') }}</span>
                    </p>
                </div>
            </div>

            {{-- Table of Goods --}}
            <div class="mb-4">
                <table class="table table-bordered m-0" style="border: 1.5px solid #000 !important; font-size: 12.5px;">
                    <thead>
                        <tr style="background: #f1f5f9;">
                            <th class="text-center" style="width: 6%; border: 1.5px solid #000 !important; font-weight: 700;">No.</th>
                            <th style="border: 1.5px solid #000 !important; font-weight: 700;">Deskripsi Barang / Sparepart</th>
                            <th class="text-center" style="width: 18%; border: 1.5px solid #000 !important; font-weight: 700;">Qty Terkirim</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $itemNo = 1; @endphp
                        @if ($delivery->detail && $delivery->detail->isNotEmpty())
                            @foreach ($delivery->detail as $item)
                                @if (($item->type ?? 'item') === 'header')
                                    <tr style="background: #f8fafc; border-top: 1.5px solid #000; border-bottom: 1.5px solid #000;">
                                        <td colspan="3" class="fw-bold text-uppercase py-2 px-3 text-primary" style="font-size: 11.5px; letter-spacing: 0.5px; border: 1.5px solid #000 !important;">
                                            <i class="mdi mdi-bookmark-outline me-1"></i> {{ $item->desc }}
                                        </td>
                                    </tr>
                                @else
                                    <tr>
                                        <td class="align-middle text-center fw-semibold" style="border: 1.5px solid #000 !important;">{{ $itemNo++ }}</td>
                                        <td class="align-middle fw-medium text-dark" style="border: 1.5px solid #000 !important;">{{ $item->view == '0' ? $item->desc : '-' }}</td>
                                        <td class="align-middle text-center fw-bold text-primary" style="border: 1.5px solid #000 !important; white-space: nowrap;">
                                            {{ (float) $item->qty }} {{ $item->info_qty }}
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        @else
                            <tr>
                                <td colspan="3" class="text-center py-3 text-muted" style="border: 1.5px solid #000 !important;">Tidak ada rincian barang.</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            {{-- Signature Boxes in Document --}}
            <div class="row pt-3" style="font-size: 13px;">
                {{-- Shipper --}}
                <div class="col-6 text-center">
                    <p class="text-uppercase fw-bold mb-1" style="font-size: 12px; color: #475569;">Shipper (Pengirim)</p>
                    <div class="d-flex align-items-center justify-content-center" style="height: 85px;">
                        @if ($delivery->sign)
                            <img src="{{ asset($delivery->sign) }}" alt="Shipper Sign" style="max-height: 75px; max-width: 170px; object-fit: contain;">
                        @else
                            <div style="height: 60px;"></div>
                        @endif
                    </div>
                    <p class="fw-bold mx-3 mb-0" style="border-top: 1.5px solid #000; padding-top: 4px; font-size: 13px;">
                        {{ $isKojisha ? 'PT Kojisha Innotiv Indonesia' : 'PT Reftech Jaya Optima' }}
                    </p>
                </div>

                {{-- Received --}}
                <div class="col-6 text-center">
                    <p class="text-uppercase fw-bold mb-1" style="font-size: 12px; color: #475569;">Received (Penerima)</p>
                    @if ($delivery->customer_signature)
                        <div class="d-flex align-items-center justify-content-center position-relative" style="height: 85px;">
                            <img src="{{ asset($delivery->customer_signature) }}" alt="Customer Signature" style="max-height: 80px; max-width: 180px; object-fit: contain; z-index: 2;">
                            @if ($delivery->customer_signed_stamp)
                                <img src="{{ asset($delivery->customer_signed_stamp) }}" alt="Stamp" style="position: absolute; max-height: 70px; opacity: 0.8; z-index: 1; transform: rotate(-6deg);">
                            @endif
                        </div>
                        <p class="fw-bold mx-3 mb-0 text-dark" style="border-top: 1.5px solid #000; padding-top: 4px; font-size: 13px;">
                            ( <u>{{ $delivery->customer_signer_name }}</u> )
                        </p>
                        @if ($delivery->customer_signer_position)
                            <small class="text-muted d-block" style="font-size: 11px;">{{ $delivery->customer_signer_position }}</small>
                        @endif
                        <small class="text-muted d-block" style="font-size: 10px;">
                            {{ $delivery->customer_signed_at ? $delivery->customer_signed_at->format('d/m/Y H:i') : '' }} WIB
                        </small>
                    @else
                        <div style="height: 85px; display: flex; align-items: center; justify-content: center;">
                            <span class="badge bg-label-warning text-uppercase" style="font-size: 10.5px;">Menunggu Tanda Tangan</span>
                        </div>
                        <p class="fw-bold mx-3 mb-0" style="border-top: 1.5px solid #000; padding-top: 4px; font-size: 13px;">
                            ( {{ $client->company ?? '........................................' }} )
                        </p>
                    @endif
                </div>
            </div>

            <div class="pt-4 mt-2 text-muted" style="font-size: 10.5px; border-top: 1px dashed #cbd5e1;">
                <p class="mb-0">Distribusi : Putih &amp; Pink &rarr; Pelanggan &nbsp;|&nbsp; <strong>Kuning &rarr; Accounting {{ $isKojisha ? 'PT Kojisha' : 'PT Reftech' }}</strong></p>
            </div>

        </div>

        {{-- Signature Action Section (Below Document) --}}
        <div id="section-sign">
            @if ($isSigned)
                <div class="success-signed-card">
                    <div class="success-signed-icon">
                        <i class="mdi mdi-check-bold"></i>
                    </div>
                    <div class="success-signed-title">Surat Jalan Telah Berhasil Ditandatangani</div>
                    <div class="success-signed-desc">
                        Dokumen ini telah resmi disetujui dan ditandatangani secara digital oleh <strong>{{ $delivery->customer_signer_name }}</strong>{{ $delivery->customer_signer_position ? ' (' . $delivery->customer_signer_position . ')' : '' }} pada {{ $delivery->customer_signed_at ? $delivery->customer_signed_at->format('d F Y, H:i') : '-' }} WIB.
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
                        <i class="mdi mdi-draw text-primary"></i>
                        Bubuhi Tanda Tangan Digital Penerima
                    </div>
                    <div class="sign-card-subtitle">
                        Silakan buat tanda tangan Anda pada area kotak di bawah menggunakan jari (Touchscreen / Smartphone) atau mouse kursor.
                    </div>

                    <form id="form-sign-delivery" action="{{ route('delivery.customer.sign.submit', $delivery->sign_token) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="signature_data" id="signature_data">

                        {{-- Canvas Pad --}}
                        <div class="canvas-container" id="canvas-wrapper">
                            <canvas id="signature-pad"></canvas>
                            <div class="canvas-placeholder-hint" id="canvas-hint">
                                <i class="mdi mdi-gesture-tap"></i> Goreskan tanda tangan penerima di sini
                            </div>
                        </div>

                        <div class="canvas-tools">
                            <button type="button" class="btn-canvas-tool" id="btn-clear-canvas">
                                <i class="mdi mdi-eraser"></i> Hapus Goresan
                            </button>
                        </div>

                        {{-- Signer Information --}}
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="signer_name">Nama Lengkap Penerima / Penandatangan <span style="color: #dc2626;">*</span></label>
                                <input type="text" id="signer_name" name="signer_name" placeholder="Contoh: Bpk. Bambang Sutrisno" required>
                            </div>
                            <div class="form-group">
                                <label for="signer_position">Jabatan / Posisi <span style="color: #64748b; font-weight: normal;">(Opsional)</span></label>
                                <input type="text" id="signer_position" name="signer_position" placeholder="Contoh: Warehouse Staff / Receiving">
                            </div>
                        </div>

                        <div class="form-group" style="margin-bottom: 18px;">
                            <label for="stamp">Upload Foto / Scan Stempel Perusahaan <span style="color: #64748b; font-weight: normal;">(Opsional)</span></label>
                            <input type="file" id="stamp" name="stamp" accept="image/png, image/jpeg, image/jpg">
                            <small class="text-muted d-block mt-1" style="font-size: 11.5px;">Format gambar PNG / JPG transparan, maksimal 3 MB.</small>
                        </div>

                        <div class="agreement-box">
                            <input type="checkbox" id="agreement" name="agreement" value="1" required>
                            <label for="agreement" style="cursor: pointer; margin-bottom: 0;">
                                Saya menyatakan bahwa seluruh barang yang tercantum pada Surat Jalan ini telah <strong>diterima dengan baik, benar, dan lengkap</strong> oleh pihak <strong>{{ $client->company ?? 'Customer' }}</strong>.
                            </label>
                        </div>

                        <button type="submit" class="btn-submit-sign" id="btn-submit">
                            <i class="mdi mdi-check-circle-outline fs-5"></i>
                            <span>Setujui &amp; Tandatangani Surat Jalan</span>
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
                    text: 'Tanda tangan penerima dan stempel akan dihapus. Anda dapat membubuhkan tanda tangan baru setelahnya.',
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

                        fetch("{{ route('delivery.customer.sign.reset', $delivery->sign_token) }}", {
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
            var form = document.getElementById('form-sign-delivery');
            var btnSubmit = document.getElementById('btn-submit');

            if (form) {
                form.addEventListener('submit', function (e) {
                    e.preventDefault();

                    if (signaturePad.isEmpty()) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Tanda Tangan Belum Ada',
                            text: 'Silakan bubuhkan goresan tanda tangan Anda pada area kotak tanda tangan terlebih dahulu.',
                            confirmButtonColor: '#4f46e5'
                        });
                        return;
                    }

                    var signerName = document.getElementById('signer_name').value.trim();
                    if (!signerName) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Nama Penandatangan Wajib',
                            text: 'Mohon isi nama lengkap penandatangan.',
                            confirmButtonColor: '#4f46e5'
                        });
                        return;
                    }

                    var agreement = document.getElementById('agreement');
                    if (!agreement.checked) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Persetujuan Diperlukan',
                            text: 'Anda harus menyetujui pernyataan konfirmasi penerimaan barang.',
                            confirmButtonColor: '#4f46e5'
                        });
                        return;
                    }

                    // Ambil Base64 PNG dari Canvas
                    var dataUrl = signaturePad.toDataURL('image/png');
                    document.getElementById('signature_data').value = dataUrl;

                    btnSubmit.disabled = true;
                    btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Memproses Tanda Tangan...';

                    var formData = new FormData(form);

                    fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json'
                        },
                        body: formData
                    })
                    .then(function (res) { return res.json(); })
                    .then(function (data) {
                        if (data.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Surat Jalan Berhasil Ditandatangani!',
                                text: data.message,
                                timer: 2000,
                                showConfirmButton: false
                            }).then(function () {
                                window.location.href = data.redirect;
                            });
                        } else {
                            btnSubmit.disabled = false;
                            btnSubmit.innerHTML = '<i class="mdi mdi-check-circle-outline fs-5"></i> <span>Setujui &amp; Tandatangani Surat Jalan</span>';
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal Menandatangani',
                                text: data.message || 'Terjadi kesalahan saat memproses data.',
                                confirmButtonColor: '#4f46e5'
                            });
                        }
                    })
                    .catch(function () {
                        btnSubmit.disabled = false;
                        btnSubmit.innerHTML = '<i class="mdi mdi-check-circle-outline fs-5"></i> <span>Setujui &amp; Tandatangani Surat Jalan</span>';
                        Swal.fire({
                            icon: 'error',
                            title: 'Error Jaringan',
                            text: 'Gagal menghubungi server. Mohon periksa koneksi internet Anda.',
                            confirmButtonColor: '#4f46e5'
                        });
                    });
                });
            }
        });
    </script>
</body>
</html>
