@php
    $isSigned   = $service->isSignedByCustomer();
    $isKojisha  = optional($service->pic?->client)->info === 'Kojisha';
    $entityName = $isKojisha ? 'PT Kojisha Innotiv Indonesia' : 'PT Reftech Jaya Optima';
    $client     = $service->pic?->client;
    $pic        = $service->pic;
    $technician = $service->technician;
    $machine    = $service->machine;
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SERVICE REPORT — {{ $service->no_service ?: 'Report #' . $service->id }}</title>

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
            color: #ffffff;
        }

        .btn-portal-outline {
            background-color: transparent;
            border-color: #cbd5e1;
            color: #334155;
        }

        .btn-portal-outline:hover {
            background-color: #f8fafc;
            border-color: #94a3b8;
        }

        /* Container */
        .portal-container {
            max-width: 920px;
            margin: 24px auto;
            padding: 0 16px;
        }

        /* Paper Document Sheet */
        .document-paper {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            padding: 36px 40px;
            margin-bottom: 24px;
        }

        @media (max-width: 768px) {
            .document-paper {
                padding: 20px 16px;
            }
            .portal-navbar {
                padding: 10px 14px;
            }
        }

        /* Header */
        .doc-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding-bottom: 18px;
            border-bottom: 2px solid #0f172a;
            gap: 20px;
        }

        .brand-info {
            flex: 1;
        }

        .brand-logo img {
            max-height: 48px;
            width: auto;
            object-fit: contain;
            margin-bottom: 6px;
        }

        .brand-name {
            font-size: 14px;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 2px;
        }

        .brand-address {
            font-size: 11px;
            color: #475569;
            line-height: 1.45;
        }

        .doc-title-block {
            text-align: right;
            flex-shrink: 0;
        }

        .doc-title {
            font-size: 22px;
            font-weight: 800;
            letter-spacing: 0.5px;
            color: #0284c7;
            margin-bottom: 2px;
        }

        .doc-number {
            font-size: 13px;
            font-weight: 700;
            color: #0f172a;
            font-family: monospace;
        }

        .doc-date {
            font-size: 11px;
            color: #64748b;
            margin-top: 2px;
        }

        /* Info Grid */
        .info-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
            margin: 18px 0;
        }

        @media (max-width: 640px) {
            .info-section {
                grid-template-columns: 1fr;
            }
        }

        .info-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 12px 14px;
        }

        .info-card-title {
            font-size: 10.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #0284c7;
            margin-bottom: 8px;
            padding-bottom: 4px;
            border-bottom: 1px solid #e2e8f0;
        }

        .info-card-company {
            font-size: 13.5px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 4px;
        }

        .info-row {
            display: flex;
            font-size: 11.5px;
            line-height: 1.5;
            color: #334155;
            margin-bottom: 2px;
        }

        .info-row .label {
            width: 105px;
            color: #64748b;
            flex-shrink: 0;
        }

        .info-row .value {
            font-weight: 600;
            color: #0f172a;
        }

        /* Content Sections */
        .content-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            margin-bottom: 14px;
            overflow: hidden;
        }

        .content-card-header {
            background: #f8fafc;
            padding: 8px 14px;
            font-size: 11.5px;
            font-weight: 700;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .content-card-body {
            padding: 12px 14px;
            font-size: 12px;
            color: #334155;
            line-height: 1.6;
            white-space: pre-wrap;
        }

        /* Photo Gallery */
        .photo-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 12px;
            padding: 12px 14px;
        }

        .photo-item {
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            overflow: hidden;
            background: #f8fafc;
            text-align: center;
        }

        .photo-item img {
            width: 100%;
            height: 130px;
            object-fit: cover;
            display: block;
        }

        .photo-caption {
            padding: 6px;
            font-size: 10.5px;
            font-weight: 600;
            color: #475569;
            background: #ffffff;
            border-top: 1px solid #e2e8f0;
        }

        /* Signature Section in Sheet */
        .signatures-wrapper {
            display: flex;
            justify-content: space-between;
            margin-top: 24px;
            padding-top: 16px;
            border-top: 1px solid #e2e8f0;
            gap: 20px;
        }

        @media (max-width: 640px) {
            .signatures-wrapper {
                flex-direction: column;
            }
        }

        .signature-box {
            flex: 1;
            text-align: center;
            padding: 10px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
        }

        .signature-label {
            font-size: 11px;
            color: #64748b;
            margin-bottom: 4px;
            font-weight: 600;
        }

        .signature-img-wrap {
            height: 80px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 6px 0;
        }

        .signature-name {
            font-weight: 700;
            color: #0f172a;
            font-size: 12px;
            border-top: 1px dashed #cbd5e1;
            padding-top: 6px;
            display: inline-block;
            min-width: 180px;
        }

        .signature-role {
            font-size: 10.5px;
            color: #64748b;
        }

        /* Signature Action Card (Portal Interactive) */
        .sign-action-card {
            background: #ffffff;
            border: 2px solid #0284c7;
            border-radius: 12px;
            box-shadow: 0 8px 30px rgba(2, 132, 199, 0.12);
            padding: 28px 32px;
            margin-top: 24px;
        }

        @media (max-width: 768px) {
            .sign-action-card {
                padding: 20px 16px;
            }
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
            margin-bottom: 20px;
            line-height: 1.45;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
            margin-bottom: 16px;
        }

        @media (max-width: 640px) {
            .form-grid {
                grid-template-columns: 1fr;
            }
        }

        .form-group label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            color: #334155;
            margin-bottom: 6px;
        }

        .form-group input[type="text"],
        .form-group input[type="file"] {
            width: 100%;
            padding: 9px 12px;
            font-size: 13px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
            background: #ffffff;
            color: #0f172a;
        }

        .form-group input[type="text"]:focus {
            border-color: #0284c7;
            box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.15);
        }

        .canvas-label-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 6px;
        }

        .canvas-container {
            position: relative;
            background: #ffffff;
            border: 2px dashed #94a3b8;
            border-radius: 8px;
            width: 100%;
            height: 200px;
            touch-action: none;
            margin-bottom: 6px;
            overflow: hidden;
            transition: border-color 0.2s ease, border-style 0.2s ease;
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
            user-select: none;
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
            transition: all 0.2s;
        }

        .btn-canvas-tool:hover {
            background: #e2e8f0;
            color: #0f172a;
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
            margin-top: 2px;
            width: 16px;
            height: 16px;
            accent-color: #0284c7;
            cursor: pointer;
            flex-shrink: 0;
        }

        .btn-submit-sign {
            width: 100%;
            background: #0284c7;
            color: #ffffff;
            font-size: 14px;
            font-weight: 700;
            padding: 13px 24px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.2s ease;
            box-shadow: 0 4px 14px rgba(2, 132, 199, 0.25);
        }

        .btn-submit-sign:hover {
            background: #0369a1;
            box-shadow: 0 6px 18px rgba(2, 132, 199, 0.35);
        }

        .btn-submit-sign:disabled {
            background: #94a3b8;
            cursor: not-allowed;
            box-shadow: none;
        }

        .signed-banner {
            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
            border: 1.5px solid #86efac;
            border-radius: 12px;
            padding: 28px 24px;
            text-align: center;
            margin-top: 24px;
        }
    </style>
</head>
<body>

    <!-- Top Sticky Navbar -->
    <nav class="portal-navbar">
        <div class="nav-left">
            <span class="doc-badge {{ $isSigned ? 'badge-signed' : 'badge-pending' }}">
                <i class="mdi {{ $isSigned ? 'mdi-check-decagram' : 'mdi-clock-outline' }}"></i>
                {{ $isSigned ? 'Signed / Selesai' : 'Menunggu Tanda Tangan' }}
            </span>
            <div>
                <div class="doc-info-title">Service Report {{ $service->no_service ?: '#' . $service->id }}</div>
                <div class="doc-info-subtitle">{{ $client?->company ?? '-' }}</div>
            </div>
        </div>
        <div class="nav-actions">
            @if ($isSigned)
                <a href="{{ route('service-report.customer.pdf', $service->sign_token) }}" target="_blank" class="btn-portal btn-portal-primary">
                    <i class="mdi mdi-printer-outline"></i> Cetak / Download PDF
                </a>
            @else
                <a href="#signature-form" class="btn-portal btn-portal-primary">
                    <i class="mdi mdi-draw"></i> Tanda Tangan Sekarang
                </a>
            @endif
        </div>
    </nav>

    <div class="portal-container">

        <!-- Document Sheet -->
        <div class="document-paper">
            <!-- Header -->
            <div class="doc-header">
                <div class="brand-info">
                    <div class="brand-logo">
                        <img src="{{ asset('/asset') }}/logo/{{ $isKojisha ? 'Logo-update-size.png' : 'Reftech-Log.png' }}" alt="{{ $entityName }}">
                    </div>
                    <div class="brand-name">{{ $entityName }}</div>
                    <div class="brand-address">
                        @if ($isKojisha)
                            Jl. Nancep No. 45A, Setu, Cibitung - Kab. Bekasi 17320<br>
                            Telp: +62 812-1000-0997 &bull; Email: admin@kojisha.com
                        @else
                            Taman Kopo Indah V, Ruko Sommerville No. 31, Bandung – Jawa Barat 40218<br>
                            Telp: 022 54417653 &bull; Email: info@reftech.id
                        @endif
                    </div>
                </div>
                <div class="doc-title-block">
                    <h1 class="doc-title">SERVICE REPORT</h1>
                    <div class="doc-number">#{{ $service->no_service ?: 'SR-' . $service->id }}</div>
                    <div class="doc-date">Tanggal: {{ Carbon\Carbon::parse($service->date)->format('d-m-Y') }}</div>
                </div>
            </div>

            <!-- Info Section -->
            <div class="info-section">
                <!-- Customer Info -->
                <div class="info-card">
                    <div class="info-card-title">Customer / Pelanggan</div>
                    <div class="info-card-company">{{ $client?->company ?? '-' }}</div>
                    <div class="info-row">
                        <span class="label">PIC:</span>
                        <span class="value">{{ $pic?->name_pic ?? '-' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="label">Telepon:</span>
                        <span class="value">{{ $pic?->phone_pic ?? $client?->phone ?? '-' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="label">Alamat / Plant:</span>
                        <span class="value">{{ $client?->address ?? '-' }}</span>
                    </div>
                </div>

                <!-- Machine & Service Info -->
                <div class="info-card">
                    <div class="info-card-title">Informasi Mesin &amp; Servis</div>
                    <div class="info-row">
                        <span class="label">Mesin:</span>
                        <span class="value"><strong>{{ $machine?->brand ?? '-' }} {{ $machine?->model ?? '' }}</strong></span>
                    </div>
                    <div class="info-row">
                        <span class="label">Serial Number:</span>
                        <span class="value font-monospace">{{ $machine?->serial_number ?? '-' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="label">Tipe Servis:</span>
                        <span class="value">{{ $service->type }} {{ $service->pm_level ? '('.$service->pm_level.')' : '' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="label">Hour Meter:</span>
                        <span class="value">{{ $service->running ? $service->running . ' Run Hours' : '-' }} {{ $service->load ? ' / ' . $service->load . ' Load Hours' : '' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="label">Teknisi:</span>
                        <span class="value">{{ $technician?->name ?? 'Teknisi Reftech' }}</span>
                    </div>
                </div>
            </div>

            <!-- Job Description -->
            @if ($service->jobdesc)
                <div class="content-card">
                    <div class="content-card-header">
                        <i class="mdi mdi-wrench-outline text-primary"></i>
                        <span>Pekerjaan yang Dilakukan</span>
                    </div>
                    <div class="content-card-body">{{ $service->jobdesc }}</div>
                </div>
            @endif

            <!-- Findings / Description -->
            @if ($service->desc)
                <div class="content-card">
                    <div class="content-card-header">
                        <i class="mdi mdi-clipboard-text-search-outline text-primary"></i>
                        <span>Temuan &amp; Analisa Servis</span>
                    </div>
                    <div class="content-card-body">{{ $service->desc }}</div>
                </div>
            @endif

            <!-- Recommendation -->
            @if ($service->recomendation)
                <div class="content-card">
                    <div class="content-card-header">
                        <i class="mdi mdi-lightbulb-on-outline text-warning"></i>
                        <span>Rekomendasi Teknisi</span>
                    </div>
                    <div class="content-card-body">{{ $service->recomendation }}</div>
                </div>
            @endif

            <!-- Photo Gallery -->
            @if ($pict && $pict->isNotEmpty())
                <div class="content-card">
                    <div class="content-card-header">
                        <i class="mdi mdi-camera-outline text-primary"></i>
                        <span>Dokumentasi Foto Pekerjaan ({{ $pict->count() }} Foto)</span>
                    </div>
                    <div class="photo-grid">
                        @foreach ($pict as $p)
                            <div class="photo-item">
                                <a href="{{ asset($p->image) }}" target="_blank">
                                    <img src="{{ asset($p->image) }}" alt="Foto Servis">
                                </a>
                                @if ($p->title || $p->desc)
                                    <div class="photo-caption">{{ $p->title ?: $p->desc }}</div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Signatures Section in Sheet -->
            <div class="signatures-wrapper">
                <!-- Teknisi Pelaksana -->
                <div class="signature-box">
                    <div class="signature-label">Diserahkan Oleh (Teknisi),</div>
                    <div class="signature-img-wrap">
                        @if ($technician && $technician->sign)
                            <img src="{{ url('') . '/' . $technician->sign }}" alt="TTD Teknisi" style="max-height: 70px; max-width: 140px; object-fit: contain;">
                        @else
                            <span class="text-muted small" style="font-style: italic;">(Tanda Tangan Teknisi)</span>
                        @endif
                    </div>
                    <div class="signature-name">{{ $technician?->name ?? 'Teknisi' }}</div>
                    <div class="signature-role">{{ $entityName }}</div>
                </div>

                <!-- Customer / PIC -->
                <div class="signature-box">
                    <div class="signature-label">Diterima Oleh (Customer / PIC),</div>
                    <div class="signature-img-wrap">
                        @if ($isSigned)
                            <img src="{{ $service->sign_client_url }}" alt="TTD Customer" style="max-height: 70px; max-width: 140px; object-fit: contain;">
                        @else
                            <span class="text-muted small" style="font-style: italic;">(Belum Ditandatangani)</span>
                        @endif
                    </div>
                    <div class="signature-name">
                        {{ $isSigned ? ($service->customer_signer_name ?: $pic?->name_pic) : ($pic?->name_pic ?: '..............................') }}
                    </div>
                    <div class="signature-role">
                        {{ $isSigned ? ($service->customer_signer_position ?: ($client?->company ?? '-')) : ($client?->company ?? '-') }}
                    </div>
                    @if ($isSigned && $service->signed_at)
                        <div style="font-size: 9.5px; color: #16a34a; font-weight: 600; margin-top: 4px;">
                            <i class="mdi mdi-check-decagram me-0.5"></i> Ditandatangani {{ date('d-m-Y H:i', strtotime($service->signed_at)) }} WIB
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Action / TTD Form -->
        @if ($isSigned)
            <div class="signed-banner">
                <div class="d-inline-flex align-items-center justify-content-center bg-white rounded-circle p-3 shadow-sm mb-3">
                    <i class="mdi mdi-check-decagram text-success" style="font-size: 40px;"></i>
                </div>
                <h3 class="fw-bold text-dark mb-1">Laporan Servis Ini Telah Ditandatangani</h3>
                <p class="text-muted mb-3" style="max-width: 520px; margin: 0 auto; font-size: 13px; line-height: 1.5;">
                    Terima kasih atas konfirmasi Anda. Laporan servis resmi telah tercatat di sistem kami.
                </p>
                <div class="d-flex justify-content-center gap-3 flex-wrap">
                    <a href="{{ route('service-report.customer.pdf', $service->sign_token) }}" target="_blank" class="btn btn-primary d-inline-flex align-items-center gap-2 px-3 py-2">
                        <i class="mdi mdi-printer-outline fs-5"></i>
                        <span>Cetak / Download PDF Resmi</span>
                    </a>
                    <form action="{{ route('service-report.customer.sign.reset', $service->sign_token) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus tanda tangan dan menandatangani ulang?');">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger d-inline-flex align-items-center gap-1.5 px-3 py-2">
                            <i class="mdi mdi-refresh fs-5"></i>
                            <span>Tanda Tangan Ulang</span>
                        </button>
                    </form>
                </div>
            </div>
        @else
            <div class="sign-action-card" id="signature-form">
                <div class="sign-card-title">
                    <i class="mdi mdi-draw fs-4 text-primary"></i>
                    <span>Konfirmasi &amp; Bubuhi Tanda Tangan Digital</span>
                </div>
                <div class="sign-card-subtitle">
                    Silakan periksa laporan servis di atas, isi identitas Anda, dan bubuhkan tanda tangan pada kotak di bawah ini.
                </div>

                <form id="formSignServiceReport" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="signature_data" id="signatureData">

                    {{-- PIC Signer Info --}}
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="signerName">
                                Nama Lengkap PIC Penandatangan <span style="color: #dc2626;">*</span>
                            </label>
                            <input type="text" id="signerName" name="signer_name"
                                value="{{ old('signer_name', $pic?->name_pic) }}" placeholder="Contoh: Budi Santoso" required>
                        </div>
                        <div class="form-group">
                            <label for="signerPosition">
                                Jabatan / Posisi <span style="color: #64748b; font-weight: normal;">(Opsional)</span>
                            </label>
                            <input type="text" id="signerPosition" name="signer_position"
                                value="{{ old('signer_position') }}" placeholder="Contoh: Maintenance Supervisor / Ka. Bagian">
                        </div>
                    </div>

                    {{-- Signature Pad --}}
                    <div class="form-group" style="margin-bottom: 16px;">
                        <div class="canvas-label-bar">
                            <label style="margin-bottom: 0;">
                                Bubuhi Tanda Tangan Digital Anda <span style="color: #dc2626;">*</span>
                            </label>
                            <button type="button" class="btn-canvas-tool" id="btnClearCanvas">
                                <i class="mdi mdi-eraser"></i> Bersihkan
                            </button>
                        </div>

                        <div class="canvas-container" id="canvasWrapper">
                            <canvas id="signature-pad"></canvas>
                            <div class="canvas-placeholder-hint" id="canvasHint">
                                <i class="mdi mdi-draw"></i> Goreskan tanda tangan di sini (layar sentuh / mouse)
                            </div>
                        </div>
                        <small class="text-muted" style="font-size: 11px; display: block; margin-top: 2px;">
                            <i class="mdi mdi-information-outline me-0.5"></i> Anda dapat menggunakan jari di layar HP/tablet atau mouse di komputer.
                        </small>
                    </div>

                    {{-- Stamp Upload --}}
                    <div class="form-group" style="margin-bottom: 16px;">
                        <label for="stampFile">
                            Upload Cap / Stempel Perusahaan <span style="color: #64748b; font-weight: normal;">(Opsional)</span>
                        </label>
                        <input type="file" id="stampFile" name="stamp" accept="image/png, image/jpeg, image/jpg">
                    </div>

                    {{-- Agreement Box --}}
                    <div class="agreement-box">
                        <input type="checkbox" id="agreementCheck" name="agreement" value="1" required>
                        <label for="agreementCheck" style="cursor: pointer; margin-bottom: 0;">
                            Saya menyatakan bahwa pekerjaan servis / inspeksi mesin pada Service Report ini telah diperiksa, diselesaikan, dan diterima dengan baik atas nama <strong>{{ $client?->company ?? 'perusahaan' }}</strong>.
                        </label>
                    </div>

                    <button type="submit" class="btn-submit-sign" id="btnSubmitSign">
                        <i class="mdi mdi-check-circle-outline fs-5"></i>
                        <span>Setujui &amp; Bubuhi Tanda Tangan Laporan Servis</span>
                    </button>
                </form>
            </div>
        @endif

    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var canvas = document.getElementById('signature-pad');
            if (!canvas) return;

            var wrapper = document.getElementById('canvasWrapper');
            var hint = document.getElementById('canvasHint');

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
                if (hint) hint.style.display = 'none';
                wrapper.classList.add('has-drawn');
            });

            document.getElementById('btnClearCanvas').addEventListener('click', function () {
                signaturePad.clear();
                if (hint) hint.style.display = 'flex';
                wrapper.classList.remove('has-drawn');
            });

            var form = document.getElementById('formSignServiceReport');
            var btnSubmit = document.getElementById('btnSubmitSign');

            form.addEventListener('submit', function (e) {
                e.preventDefault();

                if (signaturePad.isEmpty()) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Tanda Tangan Belum Dibubuhkan',
                        text: 'Silakan goreskan tanda tangan Anda pada kotak tanda tangan yang tersedia.',
                        confirmButtonColor: '#0284c7',
                    });
                    return;
                }

                var agreement = document.getElementById('agreementCheck');
                if (!agreement.checked) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Persetujuan Diperlukan',
                        text: 'Harap centang persetujuan konfirmasi pekerjaan terlebih dahulu.',
                        confirmButtonColor: '#0284c7',
                    });
                    return;
                }

                var sigData = signaturePad.toDataURL('image/png');
                document.getElementById('signatureData').value = sigData;

                var formData = new FormData(form);
                btnSubmit.disabled = true;
                btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Memproses Tanda Tangan...';

                fetch("{{ route('service-report.customer.sign.submit', $service->sign_token) }}", {
                    method: "POST",
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
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
                    btnSubmit.innerHTML = '<i class="mdi mdi-check-circle-outline fs-5"></i><span>Setujui &amp; Bubuhi Tanda Tangan Laporan Servis</span>';

                    if (result.ok && result.data.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil Ditandatangani!',
                            text: result.data.message || 'Laporan Servis telah berhasil ditandatangani.',
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
                .catch(function () {
                    btnSubmit.disabled = false;
                    btnSubmit.innerHTML = '<i class="mdi mdi-check-circle-outline fs-5"></i><span>Setujui &amp; Bubuhi Tanda Tangan Laporan Servis</span>';
                    Swal.fire({
                        icon: 'error',
                        title: 'Terjadi Kesalahan',
                        text: 'Koneksi terputus atau terjadi kesalahan server. Silakan coba lagi.',
                        confirmButtonColor: '#0284c7',
                    });
                });
            });
        });
    </script>
</body>
</html>
