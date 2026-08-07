@extends('layouts.sales.app')
@section('title', $quote->no_quote)
@php
    if ($quote->pic->client->info == 'Reftech') {
        $bgColor = 'rgb(255, 212, 0)';
    } else {
        $bgColor = 'rgb(255, 212, 0)';
    }
@endphp
<div class="invoice-print p-4">
    <div class="container-fluid flex-grow-1 container-p-y">
        @if ($quote->pic->client->info == 'Reftech')
            <div class="d-flex justify-content-between flex-xl-row flex-md-column flex-sm-row flex-column">
                <div class="mb-xl-0 pb-1">
                    <div class="d-flex svg-illustration align-items-center gap-2 mb-4">
                        <span class="app-brand-logo demo">
                            <span style="color: var(--bs-primary)">
                                <img class="text-md" src="{{ asset('/asset') }}/logo/Reftech-Log.png" alt=""
                                    srcset="" width="60%">
                            </span>
                        </span>
                    </div>
                    <p class="mb-1 fw-bolder" style="font-size: 15px">PT Reftech Jaya Optima</p>
                    <div style="font-size: 13px">
                        <p class="mb-1">Taman Kopo Indah V, Soho Sommerville No. 31</p>
                        <p class="mb-1">Bandung – Jawa Barat 40218</p>
                        <p class="mb-1">
                            <i class="mdi mdi-phone-outline scaleX-n1-rtl me-1 mdi-13px"></i>022 54417653
                            {{ '   ' }}<i
                                class="mdi mdi-email-outline scaleX-n1-rtl me-1 mdi-13px"></i>info@reftech.id
                        </p>
                        <p class="mb-1">
                        </p>
                    </div>
                </div>
                <div class="text-end">
                    <h3 class="fw-bold">QUOTATION</h3>
                    <div>
                        <span class="fw-bolder">#{{ $quote->no_quote }}</span>
                    </div>
                    @if ($quote->num_rev >= 1)
                        <div class="mt-1">
                            <span class="fw-bolder py-1 px-2"
                                style="background-color: {{ $bgColor }}; border-radius: 10px;">REV -
                                {{ $quote->num_rev }}</span>
                        </div>
                    @endif
                    <div class="mt-1">
                        <span
                            class="text-muted">{{ $quote->status == '25' ? 'DRAFT' : ($quote->status == '50' ? 'SEND' : ($quote->status == '75' ? 'NEGOTIATION' : ($quote->status == '100' ? 'DONE PO' : ($quote->status == '0' ? 'LOSS' : '')))) }}</span>
                    </div>
                    <div class="mt-1">
                        <span
                            class="text-muted">{{ Carbon\Carbon::parse($quote->estimated_date)->format('d-m-Y') }}</span>
                    </div>
                </div>
            </div>
        @else
            <div class="d-flex justify-content-between flex-xl-row flex-md-column flex-sm-row flex-column">
                <div class="mb-xl-0 pb-1">
                    <div class="d-flex svg-illustration align-items-center gap-2 mb-2">
                        <span class="app-brand-logo demo">
                            <span style="color: var(--bs-primary)">
                                <img class="text-md" src="{{ asset('/asset') }}/logo/Kojisha-Log.png" alt=""
                                    srcset="" width="60%">
                            </span>
                        </span>
                    </div>
                    <p class="mb-1 fw-bolder">PT Kojisha Innotiv Indonesia</p>
                    <div style="font-size: 10px">
                        <p class="mb-1">Jl. Nancep No. 45A, Setu</p>
                        <p class="mb-1">Cibitung - Kab. Bekasi 17320</p>
                        <p class="mb-1">
                            <i class="mdi mdi-phone-outline scaleX-n1-rtl me-1 mdi-14px"></i>+62 812-1000-0997
                            {{ '  ' }}<i
                                class="mdi mdi-email-outline scaleX-n1-rtl me-1 mdi-14px"></i>admin@kojisha.com
                        </p>
                    </div>
                </div>
                <div class="text-end">
                    <h3 class="fw-bold">QUOTATION</h3>
                    <div>
                        <span class="fw-bolder">#{{ $quote->no_quote }}</span>
                    </div>
                    @if ($quote->num_rev >= 1)
                        <div class="mt-1">
                            <span class="fw-bolder py-1 px-2"
                                style="background-color: {{ $bgColor }}; border-radius: 10px;">REV -
                                {{ $quote->num_rev }}</span>
                        </div>
                    @endif
                    <div class="mt-1">
                        <span
                            class="text-muted">{{ $quote->status == '25' ? 'DRAFT' : ($quote->status == '50' ? 'SEND' : ($quote->status == '75' ? 'NEGOTIATION' : ($quote->status == '100' ? 'DONE PO' : ($quote->status == '0' ? 'LOSS' : '')))) }}</span>
                    </div> 
                    <div class="mt-1">
                        <span
                            class="text-muted">{{ Carbon\Carbon::parse($quote->estimated_date)->format('d-m-Y') }}</span>
                    </div>
                </div>
            </div>
        @endif
        <hr>
        <div class="mb-4">
            <div class="row">
                <div class="col-6">
                    <h6 class="fw-semibold fs-4 mb-3">Quote to :</h6>
                </div>
                <div class="col-6 mb-2">
                </div>
            </div>
            <div class="row">
                <div class="col-2 fw-medium">
                    <p class="mb-1">Company </p>
                    <p class="mb-1">Name PIC</p>
                    <p class="mb-1">Phone </p>
                </div>
                <div class="col-4">
                    <p class="mb-1">: {{ $quote->pic->client->company }}</p>
                    <p class="mb-1">: {{ $quote->pic->name_pic }}</p>
                    <p class="mb-1">: {{ $quote->pic->phone_pic }}</p>
                </div>
                <div class="col-2 fw-medium text-end">
                    <p class="mb-1">Seller :</p>
                    <p class="mb-1">No PR :</p>
                    <p class="mb-1">Email :</p>
                </div>
                <div class="col-4 text-end">
                    <p class="mb-1">
                        {{ $quote->pic->client->info == 'Reftech' ? 'PT Reftech Jaya Optima' : 'PT Kojisha Innotiv Indonesia' }}</p>
                    <p class="mb-1"> {{ $quote->no_pr ?? '-' }}</p>
                    <p class="mb-1"> {{ $quote->pic->client->email }}</p>
                </div>
            </div>
        </div>
        <hr>
        <p class="mb-1">Sir/Madam,
            We are pleased to offer the under – we mention as per conditions and details described as following
            :
        </p>
        <div class="mb-2">
            <table class="table table-bordered m-0" style="width: 100%">
                <thead class="table-light border-top text-center">
                    <tr>
                        <th style="width: 1%;">No.</th>
                        <th style="width: 40%">Item Description</th>
                        <th style="width: 12%">Qty</th>
                        <th style="width: 20%">Price (IDR)</th>
                        <th style="width: 1%">Disc</th>
                        <th style="width: 30%">Total Price (IDR)</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $no = 0;
                    @endphp
                    @foreach ($dquote as $product)
                        @php
                            $no++;
                        @endphp
                        <tr style="font-size: 13px">
                            <td class="align-top text-center">{{ $no }}</td>
                            <td class="text-nowrap align-top">
                                <p class="mb-0 fw-semibold" style="font-size: 12px">
                                    {{ $product->equivalent->brand }} {{ $product->equivalent->pn }}
                                </p>
                                <pre class="mb-0"
                                    style="font-size: 10px; font-family: Inter ; max-width: 100%; overflow-x: auto; white-space: pre-wrap;">{{ $product->detail_product }}</pre>
                            </td>
                            <td class="align-top text-center">{{ $product->qty }} {{ $product->info_qty }}</td>
                            <td class="align-top text-end">{{ number_format($product->price, 0, '', '.') }}</td>
                            <td class="align-top text-center">{{ $product->disc }}%</td>
                            <td class="align-top text-end">{{ number_format($product->amount, 0, '', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-end mt-3 mb-3">
            <div style="min-width:240px; font-size:12px;">
                <table style="width:100%; border-collapse:collapse;">
                    <tr>
                        <td style="padding:3px 8px 3px 0; color:#555;">Subtotal</td>
                        <td style="padding:3px 0; text-align:right; font-weight:500;">Rp {{ number_format($quote->subtotal, 0, '', '.') }}</td>
                    </tr>
                    @if ($quote->diskon != 0)
                        <tr>
                            <td style="padding:3px 8px 3px 0; color:#555;">Discount</td>
                            <td style="padding:3px 0; text-align:right; font-weight:500;">- Rp {{ number_format($quote->diskon, 0, '', '.') }}</td>
                        </tr>
                        <tr>
                            <td style="padding:3px 8px 3px 0; color:#555;">After Discount</td>
                            <td style="padding:3px 0; text-align:right; font-weight:500;">Rp {{ number_format($afterDisc, 0, '', '.') }}</td>
                        </tr>
                    @endif
                    @if ($quote->tax != 0)
                        <tr>
                            <td style="padding:3px 8px 3px 0; color:#555;">Tax</td>
                            <td style="padding:3px 0; text-align:right; font-weight:500;">Rp {{ number_format($tax, 0, '', '.') }}</td>
                        </tr>
                    @endif
                    @if ($quote->shipping != 0)
                        <tr>
                            <td style="padding:3px 8px 3px 0; color:#555;">Shipping Cost</td>
                            <td style="padding:3px 0; text-align:right; font-weight:500;">Rp {{ number_format($quote->shipping, 0, '', '.') }}</td>
                        </tr>
                    @endif
                    <tr>
                        <td colspan="2" style="padding:4px 0 0;"><hr style="margin:0; border-color:#ccc;"></td>
                    </tr>
                    <tr style="background:#FFF9C4;">
                        <td style="padding:6px 8px 6px 6px; font-weight:700; font-size:13px; color:#333;">TOTAL PRICE</td>
                        <td style="padding:6px 6px 6px 0; text-align:right; font-weight:700; font-size:13px; color:#333;">Rp {{ number_format($quote->harga_total, 0, '', '.') }}</td>
                    </tr>
                </table>
            </div>
        </div>
        @if ($quote->termncon[0]->note)
            <div class="mb-4" style="font-size:12px;">
                <p class="mb-1 fw-semibold" style="font-size:11px; color:#888; text-transform:uppercase; letter-spacing:.4px;">Note</p>
                <p class="mb-0" style="white-space:pre-wrap; color:#333;">{{ $quote->termncon[0]->note }}</p>
            </div>
        @endif

        <div class="mb-4" style="font-size:12px;">
            <p class="mb-2 fw-semibold" style="font-size:11px; text-transform:uppercase; letter-spacing:.5px; color:#888;">Term & Condition</p>
            <table style="width:100%; border-collapse:collapse;">
                <tr>
                    <td style="width:160px; padding:2px 0; color:#555; vertical-align:top;">Validity of Quotation</td>
                    <td style="padding:2px 0; vertical-align:top;">: {{ $quote->termncon[0]->validity }}</td>
                </tr>
                <tr>
                    <td style="padding:2px 0; color:#555; vertical-align:top;">Price</td>
                    <td style="padding:2px 0; vertical-align:top;">: {{ $quote->termncon[0]->pricing }}</td>
                </tr>
                <tr>
                    <td style="padding:2px 0; color:#555; vertical-align:top;">Delivery Process</td>
                    <td style="padding:2px 0; vertical-align:top;">: {{ $quote->termncon[0]->delivery_process }}</td>
                </tr>
                <tr>
                    <td style="padding:2px 0; color:#555; vertical-align:top;">Payment</td>
                    <td style="padding:2px 0; vertical-align:top;">: {{ $quote->termncon[0]->payment }}</td>
                </tr>
            </table>
        </div>
        <hr class="my-3">
        <div class="d-flex justify-content-between align-items-center" style="font-size:12px; color:#555;">
            <div>
                <p class="mb-0">For further inquiries, please contact:</p>
                @if ($quote->pic->client->info == 'Reftech')
                    <p class="mb-0 fw-semibold" style="color:#333;">{{ $quote->sales->name }}</p>
                    <p class="mb-0">{{ $quote->sales->phone }}</p>
                @else
                    <p class="mb-0 fw-semibold" style="color:#333;">+62 812-1000-0997</p>
                @endif
            </div>
            <div class="text-end" style="font-size:11px; color:#aaa;">
                <p class="mb-0">{{ $quote->pic->client->info == 'Reftech' ? 'PT Reftech Jaya Optima' : 'PT Kojisha Innotiv Indonesia' }}</p>
                <p class="mb-0">{{ Carbon\Carbon::parse($quote->estimated_date)->format('d F Y') }}</p>
            </div>
        </div>
    </div>
</div>
@push('after-style')
    <!-- Page CSS -->
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/css/pages/app-invoice-print.css" />
    <link rel="stylesheet" href="style.css">
@endpush
@push('after-script')
    <script src="{{ asset('assets') }}/js/app-invoice-print.js"></script>
@endpush
@push('script')
    <script>
        $(document).ready(function() {
            // Ambil tinggi dari elemen <pre>
            var preHeight = $('#notePre').outerHeight();
            // Atur tinggi elemen <p> menjadi sama dengan tinggi elemen <pre>
            $('#noteParagraph').css('height', preHeight + 'px');
        });
    </script>
@endpush
