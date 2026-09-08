@php
    $isKojisha  = $quote->isKojisha();
    $docHeading = 'QUOTATION';
    $entityName = $isKojisha ? 'PT Kojisha Innotiv Indonesia' : 'PT Reftech Jaya Optima';
    $hasTax     = (bool) $quote->tax;
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $docHeading }} — {{ $quote->no_quote }}{{ $quote->client?->company ? ' - ' . $quote->client->company : '' }}</title>

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
            font-size: 12px;
            line-height: 1.45;
            padding: 24px;
        }

        /* Screen Toolbar */
        .screen-toolbar {
            max-width: 210mm;
            margin: 0 auto 20px auto;
            background: #1e293b;
            color: #ffffff;
            border-radius: 8px;
            padding: 12px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.12);
        }

        .screen-toolbar .btn-print {
            background-color: #0284c7;
            color: #ffffff;
            border: none;
            padding: 8px 18px;
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
        .quote-sheet {
            width: 210mm;
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

        .brand-address p {
            margin: 0;
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

        .doc-badge-wrap {
            margin-top: 6px;
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 4px;
        }

        .doc-badge-title {
            display: inline-flex;
            align-items: center;
            padding: 3px 8px;
            border-radius: 4px;
            background: #e0f2fe;
            color: #0369a1;
            border: 1px solid #bae6fd;
            font-size: 11px;
            font-weight: 600;
            line-height: 1.35;
            max-width: 320px;
            text-align: right;
        }

        .doc-badge-pr {
            display: inline-flex;
            align-items: center;
            padding: 2px 7px;
            border-radius: 4px;
            background: #f8fafc;
            color: #475569;
            border: 1px solid #e2e8f0;
            font-size: 10.5px;
            font-weight: 500;
        }

        /* Info Section (Quote To & Quotation Info) */
        .info-section {
            display: flex;
            gap: 14px;
            margin: 14px 0 10px 0;
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
            width: 80px;
            color: #64748b;
            flex-shrink: 0;
        }

        .info-row .value {
            font-weight: 500;
        }

        .opening-greeting {
            font-size: 11.5px;
            color: #64748b;
            font-style: italic;
            margin: 10px 0 12px 0;
        }

        /* Items Table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 4px 0 10px 0;
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

        .items-table .table-section-header td {
            background-color: #f8fafc;
            border-top: 1px solid #e2e8f0;
            border-bottom: 1px solid #cbd5e1;
            padding: 6px 10px;
            font-weight: 700;
            font-size: 11px;
            color: #0284c7;
            text-transform: uppercase;
            letter-spacing: 0.4px;
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
            page-break-inside: avoid;
            break-inside: avoid;
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

        /* Remarks Note Box */
        .remarks-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-left: 3px solid #0284c7;
            border-radius: 6px;
            padding: 8px 12px;
            margin-bottom: 12px;
            font-size: 11px;
            page-break-inside: avoid;
            break-inside: avoid;
        }

        .remarks-card-title {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #0284c7;
            margin-bottom: 4px;
        }

        .remarks-card-body {
            font-size: 11px;
            color: #334155;
            line-height: 1.45;
        }

        /* Single Term & Condition Card */
        .terms-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            margin-top: 10px;
            margin-bottom: 12px;
            overflow: hidden;
            page-break-inside: avoid;
            break-inside: avoid;
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
            width: 150px;
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

        /* Signatures */
        .signature-section {
            display: flex;
            justify-content: space-between;
            margin-top: 18px;
            page-break-inside: avoid;
            break-inside: avoid;
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
            max-width: 120px;
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

        /* Business Footer */
        .business-footer {
            border-top: 1px solid #e2e8f0;
            padding-top: 10px;
            margin-top: 16px;
            font-size: 10.5px;
            color: #64748b;
            page-break-inside: avoid;
            break-inside: avoid;
        }

        .business-footer-thanks {
            text-align: center;
            font-style: italic;
            color: #64748b;
            margin-bottom: 8px;
        }

        .business-footer-info {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }

        /* Multi Option Separator */
        .option-divider {
            border-top: 2px dashed #cbd5e1;
            margin: 20px 0 16px 0;
        }

        .option-header-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 8px;
        }

        .option-badge {
            background: #0284c7;
            color: #fff;
            font-size: 10.5px;
            font-weight: 700;
            padding: 3px 10px;
            border-radius: 12px;
        }

        .option-title {
            font-size: 13px;
            font-weight: 700;
            color: #0f172a;
        }

        /* Print Media Styles */
        @media print {
            body {
                background: #ffffff;
                padding: 0;
                font-size: 11px;
            }

            .screen-toolbar {
                display: none !important;
            }

            .quote-sheet {
                width: 100%;
                min-height: auto;
                margin: 0;
                padding: 0;
                border: none;
                box-shadow: none;
            }

            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                color-adjust: exact !important;
            }

            @page {
                size: A4 portrait;
                margin: 12mm 14mm 12mm 14mm;
            }
        }
    </style>
</head>
<body>

    {{-- Screen Toolbar --}}
    <div class="screen-toolbar">
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-primary px-2.5 py-1">{{ $docHeading }}</span>
            <span class="fw-semibold">#{{ $quote->no_quote }}</span>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button onclick="window.print()" class="btn-print">
                <i class="mdi mdi-printer"></i> Print / Save as PDF
            </button>
            <button onclick="window.close()" class="btn-close-window">
                Tutup
            </button>
        </div>
    </div>

    {{-- Printable Sheet --}}
    <div class="quote-sheet">
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
                        <p>Telp: 022 54417653 &nbsp;|&nbsp; Email: info@reftech.id &nbsp;|&nbsp; www.reftech.id</p>
                        @if ($hasTax)
                            <p><strong>NPWP:</strong> 07.372.857.1-842.9000</p>
                        @endif
                        <p style="font-size: 10px; color: #64748b; margin-top: 1px;">
                            <strong>ISO Certified:</strong> ISO 9001:2015 &nbsp;|&nbsp; ISO 14001:2015 &nbsp;|&nbsp; ISO 45001:2018
                        </p>
                    @endif
                </div>
            </div>

            <div class="doc-title-block">
                <h1 class="doc-title">{{ $docHeading }}</h1>
                <div class="doc-number">#{{ $quote->no_quote }}</div>
                <div class="doc-date">Date: {{ $quote->date ? $quote->date->format('d-m-Y') : date('d-m-Y') }}</div>

                <div class="doc-badge-wrap">
                    @if (!$quote->hide_title && $quote->title)
                        <div class="doc-badge-title">
                            <i class="mdi mdi-bookmark-outline me-1"></i>
                            <span>{{ $quote->title }}</span>
                        </div>
                    @endif
                    @if ($quote->no_pr)
                        <div class="doc-badge-pr">
                            <i class="mdi mdi-file-document-outline me-1"></i>
                            <span>No. PR: {{ $quote->no_pr }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Info Section (Quote To & Quotation Info) --}}
        <div class="info-section">
            {{-- Quote To --}}
            <div class="info-card">
                <div class="info-card-title">Customer / Quote To</div>
                <div class="info-card-company">
                    {{ $quote->client?->company ?? '-' }}
                    @if ($quote->plant)
                        <span style="font-size: 10px; font-weight: 600; color: #0284c7; background: #e0f2fe; padding: 1px 6px; border-radius: 4px; vertical-align: middle;">
                            {{ strtoupper($quote->plant->name) }}
                        </span>
                    @endif
                </div>
                <div class="info-row">
                    <span class="label">Attn:</span>
                    <span class="value">{{ $quote->pic?->name_pic ?: ($quote->attn ?: '-') }}</span>
                </div>
                <div class="info-row">
                    <span class="label">Phone:</span>
                    <span class="value">{{ $quote->pic?->phone_pic ?: ($quote->client?->phone ?? '-') }}</span>
                </div>
                <div class="info-row">
                    <span class="label">Email:</span>
                    <span class="value">{{ $quote->pic?->email_pic ?: ($quote->client?->email ?? '-') }}</span>
                </div>
                <div class="info-row">
                    <span class="label">Address:</span>
                    <span class="value">{{ $quote->address ?: ($quote->plant?->address ?: ($quote->client?->address ?? '-')) }}</span>
                </div>
            </div>

            {{-- Quotation Details --}}
            <div class="info-card">
                <div class="info-card-title">Quotation Information</div>
                <div class="info-row">
                    <span class="label">Seller:</span>
                    <span class="value">{{ $entityName }}</span>
                </div>
                <div class="info-row">
                    <span class="label">Prepared By:</span>
                    <span class="value">{{ $quote->sales?->name ?? 'Sales Representative' }}</span>
                </div>
                @if ($quote->sales?->title)
                    <div class="info-row">
                        <span class="label">Position:</span>
                        <span class="value">{{ $quote->sales->title }}</span>
                    </div>
                @endif
                @if ($quote->sales?->phone || $quote->sales?->email)
                    <div class="info-row">
                        <span class="label">Contact:</span>
                        <span class="value">
                            {{ $quote->sales?->phone }}{{ ($quote->sales?->phone && $quote->sales?->email) ? ' | ' : '' }}{{ $quote->sales?->email }}
                        </span>
                    </div>
                @endif
                <div class="info-row">
                    <span class="label">Tax Status:</span>
                    <span class="value">{{ $hasTax ? 'PPN 11% (Taxable)' : 'Non-PPN (0%)' }}</span>
                </div>
            </div>
        </div>

        <div class="opening-greeting">
            Dear Sir/Madam, Please find below our price quotation for the following :
        </div>

        {{-- Render Multi-options or Single Quotation details --}}
        @if ($quote->options->isNotEmpty())
            @foreach ($quote->options as $i => $option)
                @if ($i > 0)
                    <div class="option-divider"></div>
                @endif
                @if ($quote->options->count() > 1)
                    <div class="option-header-badge">
                        <span class="option-badge">Opsi {{ $i + 1 }}</span>
                        <span class="option-title">{{ $option->title }}</span>
                    </div>
                @endif
                @include('pages.unit-quotation.partials.option-table-print', ['items' => $option->details, 'optTotals' => $option])
            @endforeach
        @else
            @include('pages.unit-quotation.partials.option-table-print', ['items' => $quote->details, 'optTotals' => $quote])
        @endif

        {{-- Remarks / Notes Box --}}
        @if ($quote->note)
            <div class="remarks-card">
                <div class="remarks-card-title">Remarks</div>
                @php
                    $noteLines = explode("\n", str_replace("\r", "", $quote->note));
                @endphp
                <div class="remarks-card-body">
                    @foreach ($noteLines as $line)
                        @php $trimmed = trim($line); @endphp
                        @if (empty($trimmed))
                            <div style="height: 3px;"></div>
                        @else
                            @php $hasBullet = preg_match('/^([•\-\*]|\d+[\.\)])\s*(.*)/u', $trimmed, $matches); @endphp
                            @if ($hasBullet && !empty($matches[1]) && !empty($matches[2]))
                                <div style="display:flex; align-items:flex-start; margin-bottom:2px;">
                                    <span style="flex-shrink:0; min-width:18px; color:#0284c7; font-weight:600;">{{ $matches[1] }}</span>
                                    <span style="flex:1;">{{ $matches[2] }}</span>
                                </div>
                            @else
                                <div style="margin-bottom:2px;">{{ $line }}</div>
                            @endif
                        @endif
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Single Term & Condition Card --}}
        <div class="terms-card">
            <div class="terms-card-header">TERM &amp; CONDITION</div>
            <div class="terms-card-body">
                <div class="term-row">
                    <span class="term-label">Validity Of Quotation</span>
                    <span class="term-sep">:</span>
                    <span class="term-val">{{ $quote->validity ?: '1 (one) Month' }}</span>
                </div>
                <div class="term-row">
                    <span class="term-label">Price</span>
                    <span class="term-sep">:</span>
                    <span class="term-val">{{ $quote->pricing ?: 'Franco Factory' }}</span>
                </div>
                <div class="term-row">
                    <span class="term-label">Payment</span>
                    <span class="term-sep">:</span>
                    <span class="term-val">{{ $quote->payment ?: 'Cash Before Delivery' }}</span>
                </div>
                @if ($quote->warranty)
                    <div class="term-row">
                        <span class="term-label">Warranty</span>
                        <span class="term-sep">:</span>
                        <span class="term-val">{{ $quote->warranty }}</span>
                    </div>
                @endif
                @php
                    $deliveryLines = array_filter(preg_split('/\r?\n/', $quote->delivery_process ?? 'Ready stock'), fn($l) => trim($l) !== '');
                    $deliveryText = count($deliveryLines) > 1
                        ? implode("\n", array_map(fn($l) => '• ' . trim($l), $deliveryLines))
                        : ($quote->delivery_process ?? 'Ready stock');
                @endphp
                <div class="term-row">
                    <span class="term-label">Delivery Process</span>
                    <span class="term-sep">:</span>
                    <span class="term-val" style="white-space: pre-line;">{{ $deliveryText }}</span>
                </div>
            </div>
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

            {{-- Confirmed & Accepted By --}}
            <div class="signature-box">
                <div class="signature-label">Confirmed &amp; Accepted By,</div>
                <div class="signature-img-wrap">
                    {{-- Blank space for customer sign & stamp --}}
                </div>
                <div class="signature-name">
                    {{ $quote->pic?->name_pic ?: ($quote->attn ?: '..............................') }}
                </div>
                <div class="signature-role">
                    {{ $quote->client?->company ?? '-' }}
                </div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="business-footer">
            <div class="business-footer-thanks">
                Thank you for your business. We look forward to your continued partnership.
            </div>
            <div class="business-footer-info">
                <div>
                    <div style="font-weight: 700; color: #0284c7; text-transform: uppercase; letter-spacing: 0.4px;">Compressed Air Solution :</div>
                    <div style="font-weight: 500; color: #475569;">
                        Sales &nbsp;|&nbsp; Rental &nbsp;|&nbsp; Maintenance &nbsp;|&nbsp; Air Audit &nbsp;|&nbsp; Installation
                    </div>
                </div>
                <div style="text-align: right; color: #94a3b8;">
                    <div style="font-weight: 600; color: #0284c7;">{{ $entityName }}</div>
                    <div>{{ $isKojisha ? 'www.kojisha.com' : 'www.reftech.id' }} &nbsp;|&nbsp; {{ $quote->date ? $quote->date->format('d F Y') : date('d F Y') }}</div>
                </div>
            </div>
        </div>

    </div>

</body>
</html>
