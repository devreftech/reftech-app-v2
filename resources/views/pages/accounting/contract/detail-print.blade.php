@php
    $isKojisha  = ($quote->pic?->client?->info ?? '') === 'Kojisha' || $sellcon->type == 'Order';
    $docHeading = $sellcon->type == 'Order' ? 'CONFIRM ORDER' : 'SELLING CONTRACT';
    $docNoun    = $sellcon->type == 'Order' ? 'Confirm Order' : 'Selling Contract';
    $entityName = $isKojisha ? 'PT Kojisha Innotiv Indonesia' : 'PT Reftech Jaya Optima';
    $hasTax     = $quote->tax != '0';
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $docHeading }} — {{ $sellcon->no_contract }}</title>

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
        .contract-sheet {
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

        /* Signatures */
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

    {{-- Screen Toolbar --}}
    <div class="screen-toolbar">
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-primary px-2.5 py-1">{{ $docHeading }}</span>
            <span class="fw-semibold">#{{ $sellcon->no_contract }}</span>
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
    <div class="contract-sheet">
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
                <div class="doc-date">Date: {{ Carbon\Carbon::parse($sellcon->date)->format('d-m-Y') }}</div>
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
                                        {{ $product->equivalent->brand }} {{ $product->equivalent->pn }}
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

        {{-- Totals Section (Right Aligned) --}}
        @php
            $afterDisc = $quote->diskon > 0 ? $quote->subtotal - $quote->diskon : $quote->subtotal;
            $vat = $quote->tax != 0 ? ($afterDisc * $quote->tax) / 100 : 0;
        @endphp
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

        {{-- Thank you for your business Note (Moved to bottom) --}}
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
                    @if ($sellcon->isSignedByCustomer() && $sellcon->customer_signature)
                        <img src="{{ asset($sellcon->customer_signature) }}" alt="Customer Signature" style="max-height: 52px; max-width: 140px; object-fit: contain;">
                    @else
                        {{-- Blank area for physical stamp & sign --}}
                    @endif
                </div>
                <div class="signature-name">
                    {{ $sellcon->isSignedByCustomer() ? $sellcon->customer_signer_name : ($quote->pic?->name_pic ?: '..............................') }}
                </div>
                <div class="signature-role">
                    {{ $sellcon->isSignedByCustomer() ? ($sellcon->customer_signer_position ?: ($quote->pic?->client?->company ?? '-')) : ($quote->pic?->client?->company ?? '-') }}
                </div>
                @if ($sellcon->isSignedByCustomer() && $sellcon->signed_at)
                    <div style="font-size: 8.5px; color: #16a34a; font-weight: 600; margin-top: 2px;">
                        ✓ Signed on {{ date('d-m-Y H:i', strtotime($sellcon->signed_at)) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

</body>
</html>
