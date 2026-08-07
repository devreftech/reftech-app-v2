@extends('layouts.sales.app')
@section('title', $sellcon->no_contract)
<div class="invoice-print contract-print p-4 text-black">
    <div class="container-fluid flex-grow-1 container-p-y">

        {{-- ===================== HEADER ===================== --}}
        @if ($sellcon->type == 'Selling')
            <div class="doc-header {{ $quote->tax == 0 ? 'justify-content-end' : '' }}">
                @if ($quote->tax != '0')
                    <div class="brand">
                        <div class="brand-logo mb-2">
                            <img src="{{ asset('/asset') }}/logo/Reftech-Log.png" alt="Reftech" width="150">
                        </div>
                        <p class="brand-name mb-1">PT Reftech Jaya Optima</p>
                        <div class="brand-address">
                            <p class="mb-1">Taman Kopo Indah V, Soho Sommerville No. 31</p>
                            <p class="mb-1">Bandung – Jawa Barat 40218</p>
                            <p class="mb-0">
                                <i class="mdi mdi-phone-outline scaleX-n1-rtl me-1 mdi-13px"></i>022 54417653
                                {{ '   ' }}<i class="mdi mdi-email-outline scaleX-n1-rtl me-1 mdi-13px"></i>info@reftech.id
                            </p>
                        </div>
                    </div>
                @endif
                <div class="doc-meta">
                    <h3 class="doc-title">Selling Contract</h3>
                    <div class="doc-number">#{{ $sellcon->no_contract }}</div>
                    <div class="doc-date">{{ Carbon\Carbon::parse($sellcon->date)->format('d-m-Y') }}</div>
                </div>
            </div>
        @else
            <div class="doc-header {{ $quote->tax == 0 ? 'justify-content-end' : '' }}">
                @if ($quote->tax != '0')
                    <div class="brand">
                        <div class="brand-logo mb-2">
                            <img src="{{ asset('/asset') }}/logo/Logo-update-size.png" alt="Kojisha" width="150">
                        </div>
                        <p class="brand-name mb-1">PT Kojisha Innotiv Indonesia</p>
                        <div class="brand-address">
                            <p class="mb-1">Jl. Nancep No. 45A, Setu</p>
                            <p class="mb-1">Cibitung - Kab. Bekasi 17320</p>
                            <p class="mb-0">
                                <i class="mdi mdi-phone-outline scaleX-n1-rtl me-1 mdi-13px"></i>+62 812-1000-0997
                                {{ ' | ' }}<i class="mdi mdi-email-outline scaleX-n1-rtl me-1 mdi-13px"></i>admin@kojisha.com
                            </p>
                        </div>
                    </div>
                @endif
                <div class="doc-meta">
                    <h3 class="doc-title">Confirm Order</h3>
                    <div class="doc-number">#{{ $sellcon->no_contract }}</div>
                    <div class="doc-date">{{ Carbon\Carbon::parse($quote->estimated_date)->format('d-m-Y') }}</div>
                </div>
            </div>
        @endif

        <hr class="divider">

        {{-- ===================== QUOTE TO ===================== --}}
        <div class="panel mb-3">
            <p class="panel-title">Quote To</p>
            <div class="d-flex justify-content-between flex-wrap">
                <div class="info-grid">
                    <span class="label">Company</span>
                    <span class="value">: {{ $quote->pic->client->company }}</span>
                    <span class="label">Name PIC</span>
                    <span class="value">: {{ $quote->pic->name_pic }}</span>
                    <span class="label">Phone</span>
                    <span class="value">: {{ $quote->pic->client->phone }}</span>
                </div>
                <div class="info-grid">
                    <span class="label">Seller</span>
                    <span class="value">:
                        @if ($quote->tax != '0')
                            {{ $sellcon->type == 'Selling' ? 'PT Reftech Jaya Optima' : 'PT Kojisha Innotiv Indonesia' }}
                        @else
                            {{ $quote->sales_name }}
                        @endif
                    </span>
                    <span class="label">Email</span>
                    <span class="value">: {{ $quote->pic->client->email }}</span>
                </div>
            </div>
        </div>

        {{-- ===================== ITEMS ===================== --}}
        @if ($quote->type == 'Sparepart')
            <div class="mb-3">
                <table class="table table-borderless items-table m-0" style="width: 100%">
                    <thead class="text-center">
                        <tr>
                            <th class="no">No.</th>
                            <th class="item text-nowrap">Item</th>
                            <th class="price">Price (IDR)</th>
                            <th class="qty">Qty</th>
                            <th class="disc">Disc</th>
                            <th class="amount">Amount (IDR)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $no = 0; @endphp
                        @foreach ($dquote as $product)
                            @php $no++; @endphp
                            <tr>
                                <td class="align-middle">{{ $no }}</td>
                                <td class="text-nowrap align-top">
                                    <p class="mb-0 fw-semibold item-title">
                                        @if ($product->id_equivalent == '0')
                                            -
                                        @else
                                            {{ $product->equivalent->brand }} {{ $product->equivalent->pn }}
                                        @endif
                                    </p>
                                    <pre class="mb-0 item-detail">{{ $product->detail_product }}</pre>
                                </td>
                                <td class="align-top text-end">RP {{ number_format($product->price, 0, '', '.') }}</td>
                                <td class="align-top">{{ $product->qty }} {{ $product->info_qty }}</td>
                                <td class="align-top">{{ $product->disc }}%</td>
                                <td class="align-top text-end">RP {{ number_format($product->amount, 0, '', '.') }}</td>
                            </tr>
                        @endforeach
                        <tr class="totals-row">
                            <td colspan="3" rowspan="2" class="align-top pt-4"></td>
                            <td colspan="2" class="text-end pt-4 pb-0">
                                <p class="mb-2">Subtotal:</p>
                                @if ($quote->diskon != 0)
                                    <p class="mb-2">Discount:</p>
                                    <p class="mb-2">Total After Discount:</p>
                                @endif
                                <p class="mb-2">{{ $quote->tax == '11' ? 'Vat (11%)' : 'Vat' }}:</p>
                                <p class="mb-2">Shipping Cost:</p>
                            </td>
                            @php
                                if ($quote->diskon > 0) {
                                    $afterDisc = $quote->subtotal - $quote->diskon;
                                } else {
                                    $afterDisc = $quote->subtotal;
                                }

                                if ($quote->tax > 0) {
                                    $vat = ($afterDisc * $quote->tax) / 100;
                                } else {
                                    $vat = 0;
                                }
                            @endphp
                            <td colspan="2" class="text-end pt-4 pb-0">
                                <p class="fw-semibold mb-2">RP {{ number_format($quote->subtotal, 0, '', '.') }}</p>
                                @if ($quote->diskon != 0)
                                    <p class="fw-semibold mb-2">RP {{ number_format($quote->diskon, 0, '', '.') }}</p>
                                    <p class="fw-semibold mb-2">RP {{ number_format($quote->subtotal - $quote->diskon, 0, '', '.') }}</p>
                                @endif
                                <p class="fw-semibold mb-2">{{ $tax == '0' ? '0' : 'RP ' . number_format($vat, 0, '', '.') }}</p>
                                <p class="fw-semibold mb-2">RP {{ number_format($quote->shipping, 0, '', '.') }}</p>
                            </td>
                        </tr>
                        <tr class="grand-total">
                            <td colspan="2">
                                <p class="mb-0 text-end">Total:</p>
                            </td>
                            <td colspan="2">
                                <p class="fw-semibold mb-0 text-end">Rp {{ number_format($quote->harga_total, 0, '', '.') }}</p>
                            </td>
                        </tr>
                        <tr class="spacer-row">
                            <td colspan="5"></td>
                        </tr>
                        <tr class="note-row">
                            <td colspan="3" rowspan="2" class="align-top pt-4"></td>
                            <td colspan="2" class="note text-end align-top">
                                <p class="mb-0">Note:</p>
                            </td>
                            <td colspan="2" class="note">
                                <pre class="fw-semibold mb-0">{{ $quote->termncon[0]->note }}</pre>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="panel mb-3">
                <p class="panel-title">Term &amp; Condition</p>
                <div class="info-grid info-grid-wide">
                    <span class="label">Validity Of Quotation</span>
                    <span class="value">: {{ $quote->termncon[0]->validity }}</span>
                    <span class="label">Price</span>
                    <span class="value">: {{ $quote->termncon[0]->pricing }}</span>
                    <span class="label">Delivery Process</span>
                    <span class="value">: {{ $quote->termncon[0]->delivery_process }}</span>
                    <span class="label">Payment</span>
                    <span class="value">: {{ $quote->termncon[0]->payment }}</span>
                </div>
            </div>
        @else
            <div class="mb-3">
                <table class="table table-bordered items-table m-0" style="width: 100%">
                    <thead class="text-center">
                        <tr class="align-middle">
                            <th class="no" style="width: 1%;">No.</th>
                            <th style="width: 49%;">Item</th>
                            <th class="price" style="width: 10%;">Price (IDR)</th>
                            <th class="qty" style="width: 10%;">Qty</th>
                            <th class="disc" style="width: 5%;">Disc</th>
                            <th style="width: 15%;">Amount (IDR)</th>
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
                                <td class="align-middle">
                                    <p class="fw-bold mb-0">{{ chr($abjad) }}</p>
                                </td>
                                <td class="text-nowrap align-middle" colspan="5">
                                    <p class="fw-bold mb-0">{{ $subJudul->subtitle }}</p>
                                </td>
                            </tr>
                            @foreach ($subJudul->detail as $product)
                                @php $no++; @endphp
                                <tr class="detail-row">
                                    <td class="align-top py-1">
                                        <p class="mb-1">{{ $no }}</p>
                                    </td>
                                    <td class="align-top">
                                        <p class="mb-1">{{ $product->product }}</p>
                                        @if ($product->detail != '-')
                                            <pre class="mb-0 item-detail">{{ $product->detail }}</pre>
                                        @endif
                                    </td>
                                    <td class="align-top py-1 text-end">
                                        <p class="mb-0">{{ number_format($product->price, 0, '', '.') }}</p>
                                    </td>
                                    <td class="align-top py-1">
                                        <p class="mb-0">{{ $product->qty }} {{ $product->info_qty }}</p>
                                    </td>
                                    <td class="align-top py-1 text-end">
                                        <p class="mb-0">{{ number_format($product->disc, 0, '', '.') }}%</p>
                                    </td>
                                    <td class="align-top py-1 text-end">
                                        <p class="mb-0">{{ number_format($product->amount, 0, '', '.') }}</p>
                                    </td>
                                </tr>
                            @endforeach
                        @endforeach
                        <tr class="grand-total">
                            <td colspan="4" class="border-right">
                                <p class="fw-bold mb-0 text-black text-end">
                                    TOTAL PRICE, {{ $quote->tax != 0 ? 'INCLUDE' : 'EXCLUDE' }} VAT 11%
                                </p>
                            </td>
                            <td colspan="2" class="text-end border-left">
                                <p class="fw-bold mb-0 text-end text-black">RP {{ number_format($quote->harga_total, 0, '', '.') }}</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="panel mb-3">
                <p class="panel-title">Term &amp; Condition</p>
                <div class="info-grid info-grid-wide">
                    <span class="label">Validity of Quote</span>
                    <span class="value">: {{ $quote->termncon[0]->validity }}</span>
                    <span class="label">Price</span>
                    <span class="value">: {{ $quote->termncon[0]->pricing }}</span>
                    <span class="label">Delivery Process</span>
                    <span class="value">: {{ $quote->termncon[0]->delivery_process }}</span>
                    <span class="label">Payment</span>
                    <span class="value">: {{ $quote->termncon[0]->payment }}</span>
                    <span class="label">Warranty</span>
                    <span class="value">: {{ $quote->termncon[0]->warranty }}</span>
                </div>
            </div>
        @endif

        {{-- ===================== SIGNATURES ===================== --}}
        @if ($sellcon->type == 'Selling')
            <div class="signature-row">
                <div class="signature-col">
                    <p class="signature-label">Authorized By,</p>
                    @if ($quote->tax != '0')
                        <img src="{{ asset('/asset') }}/contract/sign-irene.jpeg" alt="Signature">
                    @else
                        <img src="{{ asset('/asset') }}/sign/ttdirene.jpg" alt="Signature">
                    @endif
                    <p class="signature-name">Mrs. Irene</p>
                    @if ($quote->tax != '0')
                        <p class="signature-role">PT. Reftech Jaya Optima</p>
                    @endif
                </div>
                <div class="signature-col"></div>
                <div class="signature-col">
                    <p class="signature-label">Accepted By Customer,</p>
                    <div class="signature-spacer"></div>
                    <p class="signature-name">{{ $quote->pic->name_pic }}</p>
                    <p class="signature-role">{{ $quote->pic->client->company }}</p>
                </div>
            </div>
        @else
            <div class="signature-row">
                <div class="signature-col">
                    <p class="signature-label">Authorized By,</p>
                    <img src="{{ asset('/asset') }}/contract/sign-dedeh.png" alt="Signature">
                    <p class="signature-name">Dedeh Sulastri</p>
                    <p class="signature-role">Director</p>
                </div>
                <div class="signature-col"></div>
                <div class="signature-col">
                    <p class="signature-label">Accepted By Customer,</p>
                    <div class="signature-spacer"></div>
                    <p class="signature-name">{{ $quote->pic->name_pic }}</p>
                    <p class="signature-role">{{ $quote->pic->client->company }}</p>
                </div>
            </div>
        @endif
    </div>
</div>
@push('after-style')
    <!-- Page CSS -->
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/css/pages/app-invoice-print.css" />
    <link rel="stylesheet" href="style.css">
    <style>
        .contract-print,
        .contract-print p,
        .contract-print h1,
        .contract-print h2,
        .contract-print h3,
        .contract-print h4,
        .contract-print h5,
        .contract-print h6 {
            color: #1a1a1a;
        }

        /* Header: always a top-aligned row, never lets Bootstrap's responsive
           flex-column classes or a missing stylesheet break the alignment. */
        .contract-print .doc-header {
            display: flex !important;
            flex-direction: row !important;
            flex-wrap: nowrap !important;
            align-items: flex-start !important;
            justify-content: space-between;
            gap: 24px;
        }
        .contract-print .brand-logo img {
            width: 150px;
        }
        .contract-print .brand-name {
            font-weight: 700;
            font-size: 15px;
        }
        .contract-print .brand-address {
            font-size: 12px;
            color: #555;
        }
        .contract-print .doc-meta {
            text-align: right;
            white-space: nowrap;
        }
        .contract-print .doc-title {
            font-weight: 700;
            font-size: 22px;
            letter-spacing: .3px;
            margin-bottom: 2px;
            text-transform: uppercase;
        }
        .contract-print .doc-number {
            font-weight: 700;
            font-size: 14px;
        }
        .contract-print .doc-date {
            color: #888;
            font-size: 13px;
            margin-top: 2px;
        }
        .contract-print .divider {
            margin: 16px 0 20px;
        }

        /* Panels (Quote To / Term & Condition) */
        .contract-print .panel {
            background: #f7f7f8;
            border-radius: 6px;
            padding: 16px 20px;
        }
        .contract-print .panel-title {
            font-weight: 600;
            font-size: 14px;
            margin-bottom: 10px;
        }
        .contract-print .info-grid {
            display: grid;
            grid-template-columns: max-content 1fr;
            column-gap: 10px;
            row-gap: 4px;
            font-size: 13px;
        }
        .contract-print .info-grid .label {
            font-weight: 500;
            color: #444;
        }
        .contract-print .info-grid-wide {
            grid-template-columns: 170px 1fr;
            max-width: 520px;
        }

        /* Items table */
        .contract-print .items-table thead th {
            background: #eef0f2 !important;
            color: #1a1a1a !important;
            font-weight: 600;
            font-size: 12px;
            padding: 8px 10px;
        }
        .contract-print .items-table td {
            padding: 6px 10px;
            font-size: 12px;
        }
        .contract-print .items-table .item-title {
            font-size: 11px;
        }
        .contract-print .items-table .item-detail {
            font-size: 11px;
            font-family: 'Inter', Tahoma, Geneva, Verdana, sans-serif;
            max-width: 100%;
            overflow-x: auto;
            white-space: pre-wrap;
            margin: 0;
        }
        .contract-print .items-table .subtitle-row td {
            background: #f0f0f0;
        }
        .contract-print .items-table .detail-row td {
            border-top: none !important;
            border-bottom: none !important;
        }
        .contract-print .items-table .totals-row td,
        .contract-print .items-table .note-row td {
            font-size: 12px;
        }
        .contract-print .items-table .spacer-row td {
            height: 10px;
            padding: 0;
        }
        .contract-print .items-table .note {
            background: #fbfbfb;
        }
        .contract-print .items-table .note pre {
            font-family: 'Inter', Tahoma, Geneva, Verdana, sans-serif;
            max-width: 100%;
            overflow-x: auto;
            white-space: pre-wrap;
            font-size: 12px;
        }
        .contract-print .items-table .grand-total td {
            background: #eef0f2;
            font-size: 13px;
            padding-top: 8px;
            padding-bottom: 8px;
        }
        .contract-print .items-table .grand-total.border-right {
            border-right: 1px solid rgba(0, 0, 0, .1);
        }

        /* Signatures */
        .contract-print .signature-row {
            display: flex;
            justify-content: space-between;
            margin-top: 56px;
        }
        .contract-print .signature-col {
            flex: 1;
            text-align: center;
        }
        .contract-print .signature-col img {
            width: 100px;
            height: 77px;
            object-fit: contain;
        }
        .contract-print .signature-label {
            font-weight: 500;
            font-size: 14px;
            margin-bottom: 4px;
        }
        .contract-print .signature-spacer {
            height: 77px;
        }
        .contract-print .signature-name {
            font-weight: 600;
            margin: 8px 0 0;
        }
        .contract-print .signature-role {
            margin: 0;
            font-size: 13px;
            color: #555;
        }
    </style>
@endpush
@push('after-script')
    <script src="{{ asset('assets') }}/js/app-invoice-print.js"></script>
@endpush
