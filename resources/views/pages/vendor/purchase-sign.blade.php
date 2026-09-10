@php
    $isSigned = $purchase->isSignedByVendor();
    $totalPph = $totalPph ?? 0;
    $hasDisc  = $dPurchase->contains(fn($item) => $item->disc > 0);
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Purchase Order — {{ $purchase->no_po }}</title>

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
            color: #334155;
            border-color: #cbd5e1;
        }
        .btn-portal-secondary:hover {
            background-color: #f8fafc;
            color: #0f172a;
        }

        .btn-portal-danger {
            background-color: #fff1f2;
            color: #e11d48;
            border-color: #fecdd3;
        }
        .btn-portal-danger:hover {
            background-color: #ffe4e6;
            color: #be123c;
        }

        /* Container Layout */
        .portal-container {
            max-width: 960px;
            margin: 24px auto;
            padding: 0 16px;
        }

        /* Paper Document Wrapper */
        .paper-doc {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
            border: 1px solid #e2e8f0;
            padding: 36px 40px;
            margin-bottom: 24px;
        }

        /* Signature Action Card */
        .sign-action-card {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(79, 70, 229, 0.08);
            border: 2px solid #6366f1;
            padding: 30px;
            margin-bottom: 30px;
        }

        .sign-card-title {
            font-size: 18px;
            font-weight: 700;
            color: #1e1b4b;
            margin-bottom: 6px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .sign-card-subtitle {
            font-size: 13px;
            color: #64748b;
            margin-bottom: 20px;
        }

        /* Canvas Pad */
        .canvas-container {
            position: relative;
            background: #fafafa;
            border: 2px dashed #c7d2fe;
            border-radius: 8px;
            height: 220px;
            width: 100%;
            margin-bottom: 12px;
            touch-action: none;
            overflow: hidden;
        }

        .canvas-container canvas {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }

        .canvas-placeholder-hint {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: #94a3b8;
            font-size: 13px;
            pointer-events: none;
            user-select: none;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .canvas-tools {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 20px;
        }

        .btn-canvas-tool {
            background: #f1f5f9;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            padding: 6px 12px;
            font-size: 12px;
            font-weight: 500;
            color: #475569;
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
            gap: 16px;
            margin-bottom: 16px;
        }

        @media (max-width: 640px) {
            .form-grid {
                grid-template-columns: 1fr;
            }
            .paper-doc {
                padding: 20px;
            }
            .sign-action-card {
                padding: 20px;
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
            font-size: 13px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            background: #ffffff;
            color: #0f172a;
            outline: none;
            transition: border-color 0.2s;
        }

        .form-group input[type="text"]:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
        }

        .agreement-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 12px 14px;
            margin-bottom: 20px;
            font-size: 12.5px;
            color: #334155;
            display: flex;
            align-items: flex-start;
            gap: 10px;
        }

        .agreement-box input[type="checkbox"] {
            margin-top: 3px;
            accent-color: #4f46e5;
            cursor: pointer;
        }

        .btn-submit-sign {
            width: 100%;
            padding: 14px;
            font-size: 15px;
            font-weight: 700;
            color: #ffffff;
            background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);
            border: none;
            border-radius: 8px;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.25);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.2s ease;
        }
        .btn-submit-sign:hover {
            background: linear-gradient(135deg, #4338ca 0%, #4f46e5 100%);
            box-shadow: 0 6px 16px rgba(79, 70, 229, 0.35);
            transform: translateY(-1px);
        }

        .success-signed-card {
            background: #ecfdf5;
            border: 2px solid #a7f3d0;
            border-radius: 12px;
            padding: 24px;
            text-align: center;
            margin-bottom: 30px;
        }

        .success-signed-title {
            font-size: 16px;
            font-weight: 700;
            color: #065f46;
            margin-bottom: 6px;
        }

        .success-signed-desc {
            font-size: 13px;
            color: #047857;
            max-width: 600px;
            margin: 0 auto;
        }

        /* Document Table Styling */
        .po-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12.5px;
            margin-bottom: 20px;
        }
        .po-table th {
            background-color: #f8fafc;
            border: 1px solid #cbd5e1;
            padding: 8px 10px;
            font-weight: 700;
            color: #1e293b;
        }
        .po-table td {
            border: 1px solid #e2e8f0;
            padding: 8px 10px;
            color: #334155;
        }

        /* Print Media Styles */
        @media print {
            body {
                background: #ffffff !important;
                padding: 0 !important;
            }
            .portal-navbar,
            .sign-action-card,
            .success-signed-card {
                display: none !important;
            }
            .portal-container {
                max-width: 100% !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            .paper-doc {
                box-shadow: none !important;
                border: none !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            @page {
                size: A4 portrait;
                margin: 10mm 12mm;
            }
        }
    </style>
</head>
<body>

    {{-- Top Sticky Navbar --}}
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
                <div class="doc-info-title">Purchase Order: #{{ $purchase->no_po }}</div>
                <div class="doc-info-subtitle">
                    {{ $purchase->company ?: 'Vendor' }} &bull; {{ $purchase->date ? Carbon\Carbon::parse($purchase->date)->format('d F Y') : '-' }}
                </div>
            </div>
        </div>
        <div class="nav-actions">
            <button type="button" class="btn-portal btn-portal-secondary" onclick="window.print();">
                <i class="mdi mdi-printer"></i> Cetak / PDF
            </button>
            @if (!$isSigned)
                <a href="#section-sign" class="btn-portal btn-portal-primary" id="btnScrollToSign">
                    <i class="mdi mdi-draw-pen"></i> Bubuhi TTD
                </a>
            @endif
        </div>
    </nav>

    <div class="portal-container">

        {{-- Main Document Paper Card --}}
        <div class="paper-doc">
            {{-- Header --}}
            <div class="d-flex justify-content-between flex-xl-row flex-md-column flex-sm-row flex-column mb-0">
                <div class="mb-xl-0 pb-1">
                    <div class="d-flex svg-illustration align-items-center gap-2 mb-3">
                        <span class="app-brand-logo demo">
                            <img src="{{ asset('/asset') }}/logo/Reftech-Log.png" alt="PT Reftech Jaya Optima" width="160">
                        </span>
                    </div>
                    <p class="mb-1 fw-bolder" style="font-size: 15px; color:#111;">PT Reftech Jaya Optima</p>
                    <div style="font-size: 12px; color: #555;">
                        <p class="mb-0">Taman Kopo Indah V, Soho Sommerville No. 31</p>
                        <p class="mb-0">Bandung – Jawa Barat 40218</p>
                        <p class="mb-0"><i class="mdi mdi-phone-outline me-1" style="font-size:11px;"></i>022 54417653 &nbsp;|&nbsp; <i class="mdi mdi-email-outline me-1" style="font-size:11px;"></i>info@reftech.id &nbsp;|&nbsp; <i class="mdi mdi-web me-1" style="font-size:11px;"></i>www.reftech.id</p>
                        <p class="mb-0 mt-1" style="font-size:10.5px; color:#444; font-weight:500;">
                            <i class="mdi mdi-certificate-outline me-1 text-primary"></i><span class="fw-bold" style="color:#696cff;">ISO Certified:</span> ISO 9001:2015 &nbsp;|&nbsp; ISO 14001:2015 &nbsp;|&nbsp; ISO 45001:2018
                        </p>
                    </div>
                </div>
                <div class="text-end">
                    <h3 class="fw-bold mb-1" style="letter-spacing:2px; color:#696cff;">PURCHASE ORDER</h3>
                    <p class="mb-1 fw-bold text-dark" style="font-size:16px;">#{{ $purchase->no_po }}</p>
                    @if ($purchase->no_reference)
                        <p class="mb-1 text-muted" style="font-size:12px;">
                            <span class="fw-semibold text-dark">Ref:</span> {{ $purchase->no_reference }}
                        </p>
                    @endif
                    <p class="mb-1 fw-bold" style="font-size:13px; color:#0f172a !important;">
                        <i class="mdi mdi-calendar-blank-outline me-1 text-primary"></i>{{ Carbon\Carbon::parse($purchase->date)->format('d-m-Y') }}
                    </p>
                    <div class="mb-1 mt-1">
                        <span class="badge bg-primary px-3 py-1 fs-6">PURCHASE ORDER</span>
                    </div>
                    @if ($purchase->category)
                        <p class="mb-0 text-muted" style="font-size:11px;">Category: {{ $purchase->category }}</p>
                    @endif
                </div>
            </div>

            {{-- Accent Divider --}}
            <div style="height:3px; background:linear-gradient(90deg,#696cff 0%,#9c9eff 60%,#e0e0e0 100%); border-radius:2px; margin:14px 0 16px;"></div>

            {{-- Vendor / Supplier + Ship To / Terms Boxes --}}
            <div style="display:flex !important; align-items:stretch !important; gap:12px; margin-bottom:16px; font-size:12px;">
                {{-- Vendor Box --}}
                <div style="flex:1; display:flex; flex-direction:column; align-self:stretch; border:1px solid #dcdcdc; border-radius:6px; padding:10px 14px; background:#fafafa;">
                    <p class="mb-1 fw-bold text-uppercase" style="font-size:10px; letter-spacing:.5px; color:#555;">Vendor / Supplier</p>
                    <p class="mb-1 fw-bold" style="font-size:13.5px; color:#111;">{{ $purchase->company ?: '-' }}</p>
                    @php
                        $vendorParts = [];
                        if ($purchase->attn) {
                            $vendorParts[] = '<i class="mdi mdi-account-outline me-1" style="font-size:11px; color:#444;"></i><span style="color:#222; font-weight:500;">ATTN: ' . e($purchase->attn) . '</span>';
                        }
                        if ($purchase->mobile || $purchase->phone) {
                            $vendorParts[] = '<i class="mdi mdi-phone-outline me-1" style="font-size:11px; color:#444;"></i><span style="color:#222; font-weight:500;">' . e($purchase->mobile ?: $purchase->phone) . '</span>';
                        }
                    @endphp
                    @if (count($vendorParts) > 0)
                        <p class="mb-1" style="font-size:11.5px; color:#333;">
                            {!! implode(' &nbsp;|&nbsp; ', $vendorParts) !!}
                        </p>
                    @endif
                    @if ($purchase->address)
                        <div class="mb-0" style="display:flex; align-items:flex-start; font-size:11.5px; color:#222;">
                            <i class="mdi mdi-map-marker-outline me-1" style="font-size:11px; color:#444; line-height:1.4; flex-shrink:0;"></i><span style="font-weight:500; line-height:1.4;">{{ $purchase->address }}</span>
                        </div>
                    @endif
                </div>

                {{-- Term & Info Box --}}
                <div style="min-width:280px; max-width:400px; display:flex; flex-direction:column; align-self:stretch; border:1px solid #dcdcdc; border-radius:6px; padding:10px 14px; background:#fafafa;">
                    <p class="mb-2 fw-bold text-uppercase" style="font-size:10px; letter-spacing:.5px; color:#555;">
                        <i class="mdi mdi-information-outline me-1 text-primary"></i> Term & Info
                    </p>
                    <div style="font-size:11.5px; color:#333;" class="my-auto">
                        <div class="d-flex align-items-center mb-1 pb-1" style="border-bottom:1px dashed #e8e8e8;">
                            <span class="text-muted" style="min-width:95px;"><i class="mdi mdi-pound me-1 text-primary"></i>No. Reference</span>
                            <span class="fw-semibold text-dark">: {{ $purchase->no_reference ?: '-' }}</span>
                        </div>
                        <div class="d-flex align-items-center mb-1 pb-1" style="border-bottom:1px dashed #e8e8e8;">
                            <span class="text-muted" style="min-width:95px;"><i class="mdi mdi-truck-delivery-outline me-1 text-primary"></i>Delivery</span>
                            <span class="fw-semibold text-dark">: {{ $purchase->delivery ?: '-' }}</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <span class="text-muted" style="min-width:95px;"><i class="mdi mdi-credit-card-outline me-1 text-primary"></i>Payment</span>
                            <span class="fw-semibold text-dark">: {{ $purchase->payment ?: '-' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <p class="mb-3" style="font-size:12px; color:#777; font-style:italic;">
                Dear Sir/Madam, Please find below our official Purchase Order for the following items :
            </p>

            {{-- Items Table --}}
            <div class="table-responsive rounded border mb-3">
                <table class="table table-bordered m-0" style="width:100%; font-size:12px;">
                    <thead style="font-size:11px; background:#eeeeff; color:#3d3d8f;">
                        <tr>
                            <th class="text-center py-2" style="width:5%; font-weight:700; border-color:#d0d0ff;">No.</th>
                            <th class="text-center py-2" style="width:45%; font-weight:700; border-color:#d0d0ff;">Item Description</th>
                            <th class="text-center py-2" style="width:12%; font-weight:700; border-color:#d0d0ff;">Qty</th>
                            <th class="text-center py-2 text-nowrap" style="width:18%; font-weight:700; border-color:#d0d0ff; white-space:nowrap;">Price (IDR)</th>
                            @if ($hasDisc)
                                <th class="text-center py-2" style="width:7%; font-weight:700; border-color:#d0d0ff;">Disc</th>
                            @endif
                            <th class="text-center py-2 text-nowrap" style="width:13%; font-weight:700; border-color:#d0d0ff; white-space:nowrap;">Amount (IDR)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $itemNo = 1;
                            $headerCount = 0;
                        @endphp
                        @foreach ($dPurchase as $product)
                            @if (($product->category ?? '') === 'Header')
                                @php
                                    $lbl = trim($product->product ?? '');
                                    if (!preg_match('/^[A-Z0-9][\.\)]/i', $lbl)) {
                                        $lbl = chr(65 + ($headerCount % 26)) . '. ' . $lbl;
                                    }
                                    $headerCount++;
                                @endphp
                                <tr style="background:#f4f5fa;">
                                    <td colspan="{{ $hasDisc ? '6' : '5' }}" class="fw-bold text-dark text-uppercase px-3 py-1_5" style="font-size:11.5px; border-left:3px solid #696cff;">
                                        <i class="mdi mdi-bookmark-outline text-primary me-1"></i>{{ $lbl }}
                                    </td>
                                </tr>
                            @else
                                <tr style="font-size: 12px">
                                    <td class="text-center align-top py-2">{{ $itemNo++ }}</td>
                                    <td class="align-top py-2">
                                        <p class="mb-0 fw-semibold" style="font-size: 12px; color:#111;">
                                            {{ $product->product }}
                                        </p>
                                    </td>
                                    <td class="text-center align-top py-2">
                                        <span class="fw-bold" style="color:#222;">{{ $product->qty }}</span> {{ $product->info_qty }}
                                    </td>
                                    <td class="text-end align-top py-2 text-nowrap" style="white-space:nowrap;">
                                        {{ fmod($product->price, 1) != 0 ? number_format($product->price, 2, ',', '.') : number_format($product->price, 0, '', '.') }}
                                    </td>
                                    @if ($hasDisc)
                                        <td class="text-center align-top py-2">
                                            {{ $product->disc ? $product->disc . '%' : '-' }}
                                        </td>
                                    @endif
                                    <td class="text-end align-top py-2 fw-semibold text-nowrap" style="color:#111; white-space:nowrap;">
                                        {{ number_format($product->amount, 0, '', '.') }}
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Calculations & Notes --}}
            @php
                $hargaSebelumPpn = ($purchase->subtotal ?? 0) - ($purchase->diskon ?? 0);
                $dpp = isset($dpp) ? $dpp : (($purchase->vat ?? 0) > 0 ? $hargaSebelumPpn : 0);
                $tax = isset($tax) ? $tax : (($purchase->vat ?? 0) > 0 ? round(($dpp * $purchase->vat) / 100) : 0);
            @endphp

            {{-- Finance Summary Table --}}
            <div class="row justify-content-end mb-3">
                <div class="col-md-6 col-12">
                    <div class="table-responsive">
                        <table class="table table-bordered m-0" style="font-size: 12px; border-color: #c5c5c5;">
                            <tbody>
                                <tr>
                                    <td class="text-end fw-semibold text-uppercase py-1_5 px-3" style="border-color: #c5c5c5; background: #ffffff; color: #333; vertical-align: middle;">SUBTOTAL</td>
                                    <td class="py-1_5 px-3" style="border-color: #c5c5c5; background: #ffffff; color: #111; vertical-align: middle; width: 55%;">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span>Rp</span>
                                            <span class="fw-semibold">{{ number_format($purchase->subtotal, 0, '', '.') }}</span>
                                        </div>
                                    </td>
                                </tr>
                                @if ($purchase->diskon > 0)
                                    <tr>
                                        <td class="text-end fw-semibold text-uppercase py-1_5 px-3" style="border-color: #c5c5c5; background: #ffffff; color: #333; vertical-align: middle;">DISCOUNT</td>
                                        <td class="py-1_5 px-3 text-danger" style="border-color: #c5c5c5; background: #ffffff; vertical-align: middle;">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span>- Rp</span>
                                                <span class="fw-semibold">{{ number_format($purchase->diskon, 0, '', '.') }}</span>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-end fw-semibold text-uppercase py-1_5 px-3" style="border-color: #c5c5c5; background: #ffffff; color: #333; vertical-align: middle;">Subtotal After Disc.</td>
                                        <td class="py-1_5 px-3" style="border-color: #c5c5c5; background: #ffffff; color: #111; vertical-align: middle;">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span>Rp</span>
                                                <span class="fw-semibold">{{ number_format($purchase->subtotal - $purchase->diskon, 0, '', '.') }}</span>
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                                @if ($purchase->vat > 0)
                                    <tr>
                                        <td class="text-end fw-semibold text-uppercase py-1_5 px-3" style="border-color: #c5c5c5; background: #ffffff; color: #333; vertical-align: middle;">DPP</td>
                                        <td class="py-1_5 px-3" style="border-color: #c5c5c5; background: #ffffff; color: #111; vertical-align: middle;">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span>Rp</span>
                                                <span class="fw-semibold">{{ $dpp == '0' ? '0' : number_format($dpp, 0, '', '.') }}</span>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-end fw-semibold text-uppercase py-1_5 px-3" style="border-color: #c5c5c5; background: #ffffff; color: #333; vertical-align: middle;">PPN {{ $purchase->vat }}%</td>
                                        <td class="py-1_5 px-3" style="border-color: #c5c5c5; background: #ffffff; color: #111; vertical-align: middle;">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span>Rp</span>
                                                <span class="fw-semibold">{{ $tax == '0' ? '0' : number_format($tax, 0, '', '.') }}</span>
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                                @if ($totalPph > 0)
                                    <tr>
                                        <td class="text-end fw-semibold text-uppercase py-1_5 px-3" style="border-color: #c5c5c5; background: #ffffff; color: #333; vertical-align: middle;">TOTAL PPH</td>
                                        <td class="py-1_5 px-3 text-danger" style="border-color: #c5c5c5; background: #ffffff; vertical-align: middle;">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span>- Rp</span>
                                                <span class="fw-semibold">{{ number_format($totalPph, 0, '', '.') }}</span>
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                                <tr>
                                    <td class="text-end fw-bolder text-uppercase py-2 px-3" style="border-color: #c5c5c5; background: #ffffff; color: #000; font-size: 13px; vertical-align: middle;">TOTAL PRICE</td>
                                    <td class="py-2 px-3 fw-bold" style="border-color: #c5c5c5; background: #ffffff; color: #000; font-size: 13px; vertical-align: middle;">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="fw-bold">Rp</span>
                                            <span class="fw-bold fs-6">{{ $purchase->total == '0' ? '0' : number_format($purchase->total, 0, '', '.') }}</span>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Ship To / Franco Factory --}}
            @if ($purchase->ship_to)
                <div class="mb-3">
                    <div class="p-3 rounded border" style="background:#fafafa; font-size:12px;">
                        <p class="fw-bold mb-1 text-uppercase" style="font-size:10px; letter-spacing:.5px; color:#555;">
                            <i class="mdi mdi-map-marker-outline me-1 text-primary"></i> Ship To : Franco Factory
                        </p>
                        <p class="mb-0 text-dark fw-medium" style="line-height:1.4;">
                            {{ $purchase->ship_to }}
                        </p>
                    </div>
                </div>
            @endif

            {{-- Note / Catatan diletakkan di bawah --}}
            @if (!empty($purchase->note) && trim($purchase->note) !== '-' && trim($purchase->note) !== '')
                <div class="mb-4">
                    <div class="p-3 rounded border" style="background:#fafafa; font-size:12px;">
                        <p class="fw-bold mb-1 text-uppercase" style="font-size:10px; letter-spacing:.5px; color:#555;">
                            <i class="mdi mdi-note-text-outline me-1 text-primary"></i> Note / Catatan
                        </p>
                        <p class="mb-0" style="color:#333; font-style:italic;">
                            {{ $purchase->note }}
                        </p>
                    </div>
                </div>
            @endif

            {{-- Signatures Section --}}
            <div class="row pt-3 text-center" style="font-size:12px;">
                <div class="col-6">
                    <p class="fw-bold mb-1" style="color:#333;">Authorized By.</p>
                    <div class="my-1 d-flex justify-content-center align-items-center" style="height:75px;">
                        <img src="{{ url('') . '/asset/sign/ttdAngel.jpg' }}" alt="TTD Angel" height="70" style="max-height:70px; object-fit:contain;">
                    </div>
                    <p class="fw-bold mb-0" style="color:#111;">PT Reftech Jaya Optima</p>
                </div>
                <div class="col-6">
                    <p class="fw-bold mb-1" style="color:#333;">Accepted By Vendor.</p>
                    <div class="my-1 d-flex justify-content-center align-items-center" style="height:75px;">
                        @if ($isSigned && $purchase->vendor_signature)
                            <div style="position: relative; display: inline-block;">
                                <img src="{{ asset($purchase->vendor_signature) }}" alt="TTD Vendor" height="70" style="max-height:70px; object-fit:contain;">
                                @if ($purchase->vendor_signed_stamp)
                                    <img src="{{ asset($purchase->vendor_signed_stamp) }}" alt="Stamp Vendor" style="position:absolute; top:-5px; right:-25px; max-height:55px; opacity:0.85; pointer-events:none;">
                                @endif
                            </div>
                        @else
                            <div class="text-muted d-flex align-items-center justify-content-center border border-dashed rounded px-3" style="height:70px; width:180px; font-size:11px; background:#f9f9f9;">
                                <i class="mdi mdi-draw-pen me-1"></i> Menunggu TTD
                            </div>
                        @endif
                    </div>
                    <p class="fw-bold mb-0" style="color:#111;">
                        {{ $isSigned ? $purchase->vendor_signer_name : ($purchase->attn ?: '-') }}
                        @if ($isSigned && $purchase->vendor_signer_position)
                            <span class="text-muted fw-normal">({{ $purchase->vendor_signer_position }})</span>
                        @endif
                    </p>
                    <p class="text-muted mb-0" style="font-size:11px;">{{ $purchase->company }}</p>
                </div>
            </div>
        </div>

        {{-- Signature Submission / Status Card --}}
        <div id="section-sign">
            @if ($isSigned)
                <div class="success-signed-card">
                    <div style="font-size: 40px; color: #10b981; margin-bottom: 8px;">
                        <i class="mdi mdi-check-circle"></i>
                    </div>
                    <div class="success-signed-title">Purchase Order Telah Disetujui &amp; Ditandatangani</div>
                    <div class="success-signed-desc">
                        Dokumen ini telah resmi disetujui dan ditandatangani secara digital oleh <strong>{{ $purchase->vendor_signer_name }}</strong>{{ $purchase->vendor_signer_position ? ' (' . $purchase->vendor_signer_position . ')' : '' }} pada {{ $purchase->vendor_signed_at ? Carbon\Carbon::parse($purchase->vendor_signed_at)->format('d F Y, H:i') : '-' }} WIB.
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

                    <form id="form-sign-po" action="{{ route('purchase.vendor.sign.submit', $purchase->sign_token) }}" method="POST" enctype="multipart/form-data">
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
                                <i class="mdi mdi-eraser"></i> Hapus Goresan
                            </button>
                        </div>

                        {{-- Signer Information --}}
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="signer_name">Nama Lengkap Penandatangan <span style="color: #dc2626;">*</span></label>
                                <input type="text" id="signer_name" name="signer_name" value="{{ $purchase->attn }}" placeholder="Contoh: Bpk. Budi Santoso" required>
                            </div>
                            <div class="form-group">
                                <label for="signer_position">Jabatan / Posisi <span style="color: #64748b; font-weight: normal;">(Opsional)</span></label>
                                <input type="text" id="signer_position" name="signer_position" placeholder="Contoh: Direktur / Sales Manager">
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
                                Saya menyatakan bahwa seluruh informasi dan spesifikasi dalam Purchase Order ini telah kami terima, periksa, dan <strong>disetujui</strong> atas nama pihak <strong>{{ $purchase->company ?: 'Vendor' }}</strong>.
                            </label>
                        </div>

                        <button type="submit" class="btn-submit-sign" id="btn-submit">
                            <i class="mdi mdi-check-circle-outline"></i> Setujui &amp; Tandatangani Purchase Order
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

                        fetch("{{ route('purchase.vendor.sign.reset', $purchase->sign_token) }}", {
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
                                Swal.fire('Error', data.message || 'Gagal mereset tanda tangan.', 'error');
                            }
                        })
                        .catch(function () {
                            Swal.fire('Error', 'Terjadi kesalahan pada server.', 'error');
                        });
                    }
                });
            });

            @if (!$isSigned)
                // Inisialisasi Signature Pad Canvas
                var canvasWrapper = document.getElementById('canvas-wrapper');
                var canvas = document.getElementById('signature-pad');
                var canvasHint = document.getElementById('canvas-hint');
                var btnClear = document.getElementById('btn-clear-canvas');
                var formSign = document.getElementById('form-sign-po');
                var signatureInput = document.getElementById('signature_data');
                var btnSubmit = document.getElementById('btn-submit');

                if (canvas) {
                    var signaturePad = new SignaturePad(canvas, {
                        backgroundColor: 'rgba(255, 255, 255, 0)',
                        penColor: '#0f172a',
                        minWidth: 1.5,
                        maxWidth: 3.5,
                        throttle: 16
                    });

                    function resizeCanvas() {
                        var ratio = Math.max(window.devicePixelRatio || 1, 1);
                        var rect = canvasWrapper.getBoundingClientRect();
                        canvas.width = rect.width * ratio;
                        canvas.height = rect.height * ratio;
                        canvas.getContext("2d").scale(ratio, ratio);
                        signaturePad.clear();
                    }

                    window.addEventListener("resize", resizeCanvas);
                    resizeCanvas();

                    signaturePad.addEventListener("beginStroke", function () {
                        if (canvasHint) canvasHint.style.display = 'none';
                    });

                    if (btnClear) {
                        btnClear.addEventListener('click', function () {
                            signaturePad.clear();
                            if (canvasHint) canvasHint.style.display = 'flex';
                        });
                    }

                    if (formSign) {
                        formSign.addEventListener('submit', function (e) {
                            e.preventDefault();

                            if (signaturePad.isEmpty()) {
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Tanda Tangan Belum Dibubuhkan',
                                    text: 'Silakan goreskan tanda tangan Anda pada kotak yang disediakan.',
                                    confirmButtonColor: '#4f46e5'
                                });
                                return;
                            }

                            var signerName = document.getElementById('signer_name').value.trim();
                            if (!signerName) {
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Nama Wajib Diisi',
                                    text: 'Mohon isi nama lengkap penandatangan.',
                                    confirmButtonColor: '#4f46e5'
                                });
                                return;
                            }

                            var agreement = document.getElementById('agreement');
                            if (!agreement.checked) {
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Persetujuan Wajib Dicentang',
                                    text: 'Anda harus menyetujui pernyataan persetujuan Purchase Order.',
                                    confirmButtonColor: '#4f46e5'
                                });
                                return;
                            }

                            // Convert canvas to Data URL
                            signatureInput.value = signaturePad.toDataURL('image/png');

                            Swal.fire({
                                title: 'Konfirmasi Persetujuan',
                                text: 'Apakah Anda yakin ingin menyetujui dan menandatangani Purchase Order ini?',
                                icon: 'question',
                                showCancelButton: true,
                                confirmButtonColor: '#4f46e5',
                                cancelButtonColor: '#64748b',
                                confirmButtonText: 'Ya, Tandatangani',
                                cancelButtonText: 'Batal',
                                reverseButtons: true
                            }).then(function (result) {
                                if (result.isConfirmed) {
                                    btnSubmit.disabled = true;
                                    btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Memproses Tanda Tangan...';

                                    var formData = new FormData(formSign);

                                    fetch(formSign.action, {
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
                                                title: 'Berhasil Ditandatangani!',
                                                text: data.message,
                                                showConfirmButton: false,
                                                timer: 2000
                                            }).then(function () {
                                                window.location.href = data.redirect;
                                            });
                                        } else {
                                            btnSubmit.disabled = false;
                                            btnSubmit.innerHTML = '<i class="mdi mdi-check-circle-outline"></i> Setujui &amp; Tandatangani Purchase Order';
                                            Swal.fire('Error', data.message || 'Terjadi kesalahan saat memproses tanda tangan.', 'error');
                                        }
                                    })
                                    .catch(function () {
                                        btnSubmit.disabled = false;
                                        btnSubmit.innerHTML = '<i class="mdi mdi-check-circle-outline"></i> Setujui &amp; Tandatangani Purchase Order';
                                        Swal.fire('Error', 'Terjadi kesalahan koneksi server.', 'error');
                                    });
                                }
                            });
                        });
                    }
                }
            @endif
        });
    </script>
</body>
</html>
