@extends('layouts.sales.app')
@section('title', $quote->no_quote . ($quote->client?->company ? ' - ' . $quote->client->company : ''))
<div class="invoice-print">
    <div class="container-fluid flex-grow-1">

        {{-- Header --}}
        <div class="header-row d-flex justify-content-between align-items-start mb-0 pb-1" style="display:flex !important; flex-direction:row !important; justify-content:space-between !important; align-items:flex-start !important;">
            <div class="pb-1">
                @if ($quote->client?->info === 'Kojisha')
                    <div class="d-flex svg-illustration align-items-center gap-2 mb-2">
                        <span class="app-brand-logo demo">
                            <span style="color: var(--bs-primary)">
                                <img src="{{ asset('/asset') }}/logo/Kojisha-Log.png" alt="" width="60%">
                            </span>
                        </span>
                    </div>
                    <p class="mb-1 fw-bolder" style="font-size: 15px">PT Kojisha Innotiv Indonesia</p>
                    <div style="font-size: 12px; color: #555;">
                        <p class="mb-0">Jl. Nancep No. 45A, Setu</p>
                        <p class="mb-0">Cibitung - Kab. Bekasi 17320</p>
                        <p class="mb-0"><i class="mdi mdi-phone-outline me-1" style="font-size:11px;"></i>+62 812-1000-0997 &nbsp;|&nbsp; <i class="mdi mdi-email-outline me-1" style="font-size:11px;"></i>admin@kojisha.com</p>
                    </div>
                @else
                    <div class="d-flex svg-illustration align-items-center gap-2 mb-2">
                        <span class="app-brand-logo demo">
                            <span style="color: var(--bs-primary)">
                                <img src="{{ asset('/asset') }}/logo/Reftech-Log.png" alt="" width="60%">
                            </span>
                        </span>
                    </div>
                    <p class="mb-1 fw-bolder" style="font-size: 15px">PT Reftech Jaya Optima</p>
                    <div style="font-size: 12px; color: #555;">
                        <p class="mb-0">Taman Kopo Indah V, Soho Sommerville No. 31</p>
                        <p class="mb-0">Bandung – Jawa Barat 40218</p>
                        <p class="mb-0"><i class="mdi mdi-phone-outline me-1" style="font-size:11px;"></i>022 54417653 &nbsp;|&nbsp; <i class="mdi mdi-email-outline me-1" style="font-size:11px;"></i>info@reftech.id &nbsp;|&nbsp; <i class="mdi mdi-web me-1" style="font-size:11px;"></i>www.reftech.id</p>
                        <p class="mb-0 mt-1" style="font-size:10.5px; color:#444; font-weight:500;">
                            <i class="mdi mdi-certificate-outline me-1 text-primary"></i><span class="fw-bold" style="color:#696cff;">ISO Certified:</span> ISO 9001:2015 &nbsp;|&nbsp; ISO 14001:2015 &nbsp;|&nbsp; ISO 45001:2018
                        </p>
                    </div>
                @endif
            </div>
            <div class="text-end" style="align-self: flex-start;">
                <h3 class="fw-bold mb-1 mt-0" style="letter-spacing:2px; color:#696cff; margin-top:0 !important; line-height:1.1;">QUOTATION</h3>
                <p class="mb-1 fw-bold text-dark" style="font-size:16px;">#{{ $quote->no_quote }}</p>
                <p class="mb-1 fw-bold" style="font-size:13px; color:#0f172a !important;">
                    <i class="mdi mdi-calendar-blank-outline me-1 text-primary"></i>{{ $quote->date?->format('d-m-Y') }}
                </p>
                @if (!$quote->hide_title && $quote->title)
                    <div class="mt-1.5 mb-1" style="display: flex; justify-content: flex-end;">
                        <span class="d-inline-flex align-items-center px-2.5 py-1 rounded" style="background: #eef2ff; color: #3730a3; border: 1px solid #c7d2fe; font-size: 11.5px; font-weight: 600; line-height: 1.35; max-width: 320px; text-align: right;">
                            <i class="mdi mdi-bookmark-outline text-primary me-1" style="font-size: 13px;"></i>
                            <span>{{ $quote->title }}</span>
                        </span>
                    </div>
                @endif
                @if ($quote->no_pr)
                    <div class="mt-1" style="display: flex; justify-content: flex-end;">
                        <span class="d-inline-flex align-items-center px-2 py-0.5 rounded" style="background: #f8fafc; color: #475569; border: 1px solid #e2e8f0; font-size: 10.5px; font-weight: 500;">
                            <i class="mdi mdi-file-document-outline me-1" style="font-size: 11px;"></i>
                            <span>No. PR: {{ $quote->no_pr }}</span>
                        </span>
                    </div>
                @endif
            </div>
        </div>

        {{-- Accent Divider --}}
        <div style="height:3px; background:linear-gradient(90deg,#696cff 0%,#9c9eff 60%,#e0e0e0 100%); border-radius:2px; margin:14px 0 18px;"></div>

        {{-- Quote To + Quotation Info (2 box berdampingan seimbang) --}}
        <div style="display:flex !important; display:-webkit-flex !important; align-items:stretch !important; gap:16px; margin-bottom:18px; font-size:12px;">
            <div style="flex:1; display:flex; flex-direction:column; align-self:stretch; border:1px solid #dcdcdc; border-radius:6px; padding:12px 16px; background:#fafafa;">
                <p class="mb-1 fw-bold text-uppercase" style="font-size:10px; letter-spacing:.5px; color:#555;">Quote To</p>
                <p class="mb-1 fw-bold" style="font-size:13.5px; color:#111;">
                    {{ $quote->client?->company ?? '-' }}
                    @if ($quote->plant)
                        <span class="badge bg-label-primary ms-1" style="font-size:9.5px; vertical-align:middle;">{{ strtoupper($quote->plant->name) }}</span>
                    @endif
                </p>
                @php
                    $contactParts = [];
                    if ($quote->pic?->name_pic) {
                        $contactParts[] = '<i class="mdi mdi-account-outline me-1" style="font-size:11px; color:#444;"></i><span style="color:#222; font-weight:500;">' . e($quote->pic->name_pic) . '</span>';
                    }
                    if ($quote->pic?->phone_pic) {
                        $contactParts[] = '<i class="mdi mdi-phone-outline me-1" style="font-size:11px; color:#444;"></i><span style="color:#222; font-weight:500;">' . e($quote->pic->phone_pic) . '</span>';
                    }
                    if ($quote->client?->email) {
                        $contactParts[] = '<i class="mdi mdi-email-outline me-1" style="font-size:11px; color:#444;"></i><span style="color:#222; font-weight:500;">' . e($quote->client->email) . '</span>';
                    }
                @endphp

                @if (count($contactParts) > 0)
                    <p class="mb-1" style="font-size:11.5px; color:#333;">
                        {!! implode(' &nbsp;|&nbsp; ', $contactParts) !!}
                    </p>
                @endif
                @if ($quote->address || $quote->plant)
                    <div class="mb-0" style="display:flex; align-items:flex-start; font-size:11.5px; color:#222;">
                        <i class="mdi mdi-map-marker-outline me-1" style="font-size:11px; color:#444; line-height:1.4; flex-shrink:0;"></i><span style="font-weight:500; line-height:1.4;">{{ $quote->address ?? $quote->plant?->address }} {{ $quote->plant ? '(' . $quote->plant->name . ')' : '' }}</span>
                    </div>
                @endif
            </div>
            <div style="min-width:250px; display:flex; flex-direction:column; align-self:stretch; border:1px solid #dcdcdc; border-radius:6px; padding:12px 16px; background:#fafafa;">
                <p class="mb-1 fw-bold text-uppercase" style="font-size:10px; letter-spacing:.5px; color:#555;">Prepared By</p>
                <p class="mb-1 fw-bold" style="font-size:13.5px; color:#111;">{{ $quote->sales?->name ?? 'Alifya Syahrani' }}</p>
                <p class="mb-1 fw-medium" style="font-size:11.5px; color:#444;">
                    <i class="mdi mdi-briefcase-outline me-1" style="font-size:11px; color:#444;"></i>{{ $quote->sales?->title ?? 'Sales Engineer' }}
                </p>
                @if ($quote->sales?->email || $quote->sales?->phone)
                    <p class="mb-0" style="font-size:11.5px; color:#222;">
                        @if ($quote->sales?->phone)
                            <i class="mdi mdi-phone-outline me-1" style="font-size:11px; color:#444;"></i><span style="font-weight:500;">{{ $quote->sales->phone }}</span>
                        @endif
                        @if ($quote->sales?->phone && $quote->sales?->email) &nbsp;|&nbsp; @endif
                        @if ($quote->sales?->email)
                            <i class="mdi mdi-email-outline me-1" style="font-size:11px; color:#444;"></i><span style="font-weight:500;">{{ $quote->sales->email }}</span>
                        @endif
                    </p>
                @endif
            </div>
        </div>

        <p class="mb-3" style="font-size:12px; color:#777; font-style:italic;">
            Dear Sir/Madam, Please find bellow our price quotation for the following :
        </p>

        {{-- Items Table + Financial Summary — per Opsi kalau quotation ini
             punya >1 opsi perbandingan harga, atau 1x aja kalau biasa. --}}
        @if ($quote->options->isNotEmpty())
            @foreach ($quote->options as $i => $option)
                @if ($i > 0)
                    <div style="border-top:2px dashed #d0d0ff; margin:24px 0 16px;"></div>
                @endif
                @if ($quote->options->count() > 1)
                <div style="display:flex; align-items:center; gap:8px; margin-bottom:8px;">
                    <span class="badge" style="background:#696cff; color:#fff; font-size:10px; padding:3px 8px; border-radius:10px;">Opsi {{ $i + 1 }}</span>
                    <h6 style="margin:0; font-weight:700; color:#111; font-size:14px;">{{ $option->title }}</h6>
                </div>
                @endif
                @include('pages.unit-quotation.partials.option-table-print', ['items' => $option->details, 'optTotals' => $option])
            @endforeach
        @else
            @include('pages.unit-quotation.partials.option-table-print', ['items' => $quote->details, 'optTotals' => $quote])
        @endif

            {{-- Note (full-width, di bawah financial summary) --}}
            @if ($quote->note)
            <div style="border:1px solid #e0e0e0; border-left:3px solid #696cff; border-radius:6px; padding:10px 14px; font-size:11px; color:#333; margin-bottom:14px; background:#fafafa; page-break-inside: avoid !important; break-inside: avoid !important;">
                <p class="mb-1 fw-semibold" style="font-size:10px; color:#888; text-transform:uppercase; letter-spacing:.5px;">Remarks</p>
                @php
                    $noteLines = explode("\n", str_replace("\r", "", $quote->note));
                @endphp
                <div style="font-size:11px; color:#222; line-height:1.5;">
                    @foreach ($noteLines as $line)
                        @php
                            $trimmed = trim($line);
                        @endphp
                        @if (empty($trimmed))
                            <div style="height:3px;"></div>
                        @else
                            @php
                                $hasBullet = preg_match('/^([•\-\*]|\d+[\.\)])\s*(.*)/u', $trimmed, $matches);
                            @endphp
                            @if ($hasBullet && !empty($matches[1]) && !empty($matches[2]))
                                <div style="display:flex; align-items:flex-start; margin-bottom:3px;">
                                    <span style="flex-shrink:0; min-width:20px; color:#696cff; font-weight:600;">{{ $matches[1] }}</span>
                                    <span style="flex:1;">{{ $matches[2] }}</span>
                                </div>
                            @else
                                <div style="margin-bottom:3px;">{{ $line }}</div>
                            @endif
                        @endif
                    @endforeach
                </div>
            </div>
            @endif

            {{-- T&C --}}
            <div style="border:1px solid #e0e0e0; border-radius:6px; padding:12px 16px; font-size:11px; background:#fff; margin-bottom:16px; page-break-inside: avoid !important; break-inside: avoid !important;">
                <p class="mb-2 fw-semibold" style="font-size:10px; text-transform:uppercase; letter-spacing:.5px; color:#888;">Term &amp; Condition</p>
                <table style="width:100%; border-collapse:collapse; font-size:11px;">
                    <tr>
                        <td style="width:150px; padding:3px 0; color:#555; vertical-align:top;">Validity of Quotation</td>
                        <td style="padding:3px 0; color:#222; vertical-align:top;">: {{ $quote->validity ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td style="padding:3px 0; color:#555; vertical-align:top;">Price</td>
                        <td style="padding:3px 0; color:#222; vertical-align:top;">: {{ $quote->pricing ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td style="padding:3px 0; color:#555; vertical-align:top;">Payment</td>
                        <td style="padding:3px 0; color:#222; vertical-align:top;">: {{ $quote->payment ?? '-' }}</td>
                    </tr>
                    @if ($quote->warranty)
                        <tr>
                            <td style="padding:3px 0; color:#555; vertical-align:top;">Warranty</td>
                            <td style="padding:3px 0; color:#222; vertical-align:top;">: {{ $quote->warranty }}</td>
                        </tr>
                    @endif
                    @php
                        $deliveryLines = array_filter(preg_split('/\r?\n/', $quote->delivery_process ?? '-'), fn($l) => trim($l) !== '');
                        $deliveryText = count($deliveryLines) > 1
                            ? implode("\n", array_map(fn($l) => '• ' . trim($l), $deliveryLines))
                            : ($quote->delivery_process ?? '-');
                    @endphp
                    <tr>
                        <td style="padding:3px 0; color:#555; vertical-align:top;">Delivery Process</td>
                        <td style="padding:3px 0; color:#222; vertical-align:top;">
                            <div style="display:flex; align-items:flex-start;">
                                <span style="flex-shrink:0;">:&nbsp;</span>
                                <span style="white-space:pre-line;">{{ $deliveryText }}</span>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>

            {{-- Signature Section Removed --}}

            {{-- Footer --}}
            <div style="border-top:2px solid #eeeeff; padding-top:14px; margin-top:12px;">
                <p class="text-center mb-2" style="font-size:11px; color:#aaa; font-style:italic;">
                    Thank you for your business. We look forward to your continued partnership.
                </p>
                <div class="d-flex justify-content-between align-items-end" style="font-size:12px; color:#555;">
                    <div>
                        <p class="mb-0 fw-bold" style="font-size:11px; color:#696cff; text-transform:uppercase; letter-spacing:.5px;">Compressed Air Solution :</p>
                        <p class="mb-0 fw-medium" style="font-size:11px; color:#444;">
                            Sales &nbsp;|&nbsp; Rental &nbsp;|&nbsp; Maintenance &nbsp;|&nbsp; Air Audit &nbsp;|&nbsp; Installation
                        </p>
                    </div>
                    <div class="text-end" style="font-size:11px; color:#aaa;">
                        <p class="mb-0 fw-semibold" style="color:#696cff; font-size:12px;">PT Reftech Jaya Optima</p>
                        <p class="mb-0" style="color:#666; font-weight:500;">www.reftech.id/quotation &nbsp;|&nbsp; {{ $quote->date?->format('d F Y') }}</p>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

@push('after-style')
    <style>
        /* Theme's .table tbody td rule forces vertical-align:middle !important with higher
           specificity than the .align-top utility class — override it here for the item table. */
        table.items-top-align-table tbody td {
            vertical-align: top !important;
        }
        @page {
            size: A4 portrait !important;
            margin: 15mm 15mm 15mm 15mm !important;
        }
        @media print {
            @page {
                size: A4 portrait !important;
                margin: 15mm 15mm 15mm 15mm !important;
            }
            html, body, .layout-wrapper, .layout-container, .layout-page, .content-wrapper, .container-fluid {
                padding: 0 !important;
                margin: 0 !important;
                background: #fff !important;
                width: 100% !important;
                max-width: 100% !important;
                box-shadow: none !important;
                position: static !important;
                overflow: visible !important;
                height: auto !important;
                min-height: auto !important;
            }
            .layout-menu, .layout-navbar, .content-backdrop, footer, .layout-menu-toggle {
                display: none !important;
            }
            .invoice-print {
                padding: 0 !important;
                margin: 0 !important;
                width: 100% !important;
                box-sizing: border-box !important;
            }
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                color-adjust: exact !important;
            }
            .table {
                width: 100% !important;
                border-collapse: collapse !important;
            }
            .table thead {
                display: table-header-group !important;
            }
            .table tbody tr {
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }
            .table-section-header {
                page-break-after: avoid !important;
                break-after: avoid !important;
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }
            .keep-together {
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }
            .signature-section {
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }
        }
        @media screen {
            .invoice-print {
                max-width: 920px;
                margin: 24px auto;
                padding: 1.2cm 1.5cm !important;
                background: #fff;
                box-shadow: 0 4px 24px rgba(0,0,0,0.07);
                border-radius: 8px;
            }
            .container-fluid {
                padding: 0 !important;
            }
        }
    </style>
@endpush
@push('after-script')
    <script>
        document.title = @json($quote->no_quote . ($quote->client?->company ? ' - ' . $quote->client->company : ''));
    </script>
    <script src="{{ asset('assets') }}/js/app-invoice-print.js"></script>
@endpush
