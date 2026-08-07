@extends('layouts.sales.app')
@section('title', $sellcon->no_contract)
<div class="invoice-print p-4 text-black">
    <div class="container-fluid flex-grow-1 container-p-y">
        @if ($sellcon->type == 'Selling')
            <div
                class="d-flex justify-content-between flex-xl-row flex-md-column flex-sm-row flex-column {{ $quote->tax == 0 ? 'float-end' : '' }}">
                @if ($quote->tax != '0')
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
                @endif
                <div class="text-end">
                    <h3 class="fw-bold">SELLING CONTRACT</h3>
                    <div>
                        <span class="fw-bolder">#{{ $sellcon->no_contract }}</span>
                    </div>
                    <div class="mt-1">
                        <span class="text-muted">{{ Carbon\Carbon::parse($sellcon->date)->format('d-m-Y') }}</span>
                    </div>
                </div>
            </div>
        @else
            <div
                class="d-flex justify-content-between flex-xl-row flex-md-column flex-sm-row flex-column {{ $quote->tax == 0 ? 'float-end' : '' }}">
                @if ($quote->tax != '0')
                    <div class="mb-xl-0 pb-1">
                        <div class="d-flex svg-illustration align-items-center gap-2 mb-2">
                            <span class="app-brand-logo demo">
                                <span style="color: var(--bs-primary)">
                                    <img class="text-md" src="{{ asset('/asset') }}/logo/Logo-update-size.png"
                                        alt="" srcset="" width="60%">
                                </span>
                            </span>
                        </div>
                        <p class="mb-1 fw-bolder">PT Kojisha Innotiv Indonesia</p>
                        <div style="font-size: 10px">
                            <p class="mb-1">Jl. Nancep No. 45A, Setu</p>
                            <p class="mb-1">Cibitung - Kab. Bekasi 17320</p>
                            <p class="mb-1">
                                <i class="mdi mdi-phone-outline scaleX-n1-rtl me-1 mdi-14px"></i>+62 812-1000-0997
                                {{ ' | ' }}<i
                                    class="mdi mdi-email-outline scaleX-n1-rtl me-1 mdi-14px"></i>admin@kojisha.com
                            </p>
                        </div>
                    </div>
                @endif
                <div class="text-end">
                    <h3 class="fw-bold">Confirm Order</h3>
                    <div>
                        <span class="fw-bolder">#{{ $sellcon->no_contract }}</span>
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
                    <h6 class="fw-semibold fs-4 mb-3">Quote to:</h6>
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
                    <p class="mb-1">: {{ $quote->pic->client->phone }}</p>
                </div>
                <div class="col-2 fw-medium text-end">
                    <p class="mb-1">Seller :</p>
                    <p class="mb-1">Email :</p>
                </div>
                <div class="col-4 text-end">
                    <p class="mb-1">
                        @if ($quote->tax != '0')
                            {{ $sellcon->type == 'Selling' ? 'PT Reftech Jaya Optima' : 'PT Kojisha Innotiv Indonesia' }}
                        @else
                            {{ $quote->sales_name }}
                        @endif
                    </p>
                    <p class="mb-1"> {{ $quote->pic->client->email }}</p>
                </div>
            </div>
        </div>
        @if ($quote->type == 'Sparepart')
            <div class="mb-2">
                <table class="table table-borderless m-0" style="width: 100%">
                    <thead class="table-light border-top text-center">
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
                        @php
                            $no = 0;
                        @endphp
                        @foreach ($dquote as $product)
                            @php
                                $no++;
                            @endphp
                            <tr style="font-size: 12px">
                                <td class="align-middle">{{ $no }}</td>
                                <td class="text-nowrap align-top">
                                    <p class="mb-0 fw-semibold" style="font-size: 10px">
                                        @if ($product->id_equivalent == '0')
                                            -
                                        @else
                                            {{ $product->equivalent->brand }} {{ $product->equivalent->pn }}
                                        @endif
                                    </p>
                                    <pre class="mb-0"
                                        style="font-size: 10px; font-family: 'Inter', Tahoma, Geneva, Verdana, sans-serif; max-width: 100%; overflow-x: auto; white-space: pre-wrap;">{{ $product->detail_product }}</pre>
                                </td>
                                <td class="align-top text-end">RP {{ number_format($product->price, 0, '', '.') }}</td>
                                <td class="align-top">{{ $product->qty }} {{ $product->info_qty }}</td>
                                <td class="align-top">{{ $product->disc }}%</td>
                                <td class="align-top text-end">RP {{ number_format($product->amount, 0, '', '.') }}
                                </td>
                            </tr>
                        @endforeach
                        <tr class="" style="font-size: 12px">
                            <td colspan="3" rowspan="2" class="align-top pt-4">
                            </td>
                            <td colspan="2" class="text-end pt-4 pb-0">
                                <p class="mb-2">Subtotal:</p>
                                @if ($quote->diskon != 0)
                                    <p class="mb-2">Discount:</p>
                                    <p class="mb-2">Total After Discount:</p>
                                @endif
                                <p class="mb-2">{{ $quote->tax == '11' ? 'Vat (11%)' : 'Vat' }}:</p>
                                <p class="mb-2">Shipping Cost:</p>
                            </td>
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
                                <p class="fw-semibold mb-2">RP
                                    {{ number_format($quote->subtotal, 0, '', '.') }}</p>
                                @if ($quote->diskon != 0)
                                    <p class="fw-semibold mb-2">RP
                                        {{ number_format($quote->diskon, 0, '', '.') }}
                                    <p class="fw-semibold mb-2">RP
                                        {{ number_format($quote->subtotal - $quote->diskon, 0, '', '.') }}
                                @endif
                                <p class="fw-semibold mb-2">
                                    {{ $tax == '0' ? '0' : 'RP ' . number_format($vat, 0, '', '.') }}</p>
                                </p>
                                <p class="fw-semibold mb-2">RP
                                    {{ number_format($quote->shipping, 0, '', '.') }}</p>
                            </td>
                        </tr>
                        <tr class="total" style="font-size: 12px">
                            <td colspan="2" class="">
                                <p class="mb-0 text-end">Total:</p>
                            </td>
                            <td colspan="2" class="">
                                <p class="fw-semibold mb-0 text-end">Rp
                                    {{ number_format($quote->harga_total, 0, '', '.') }}</p>
                            </td>
                        </tr>
                        <tr class="" style="height: 10px !important;">
                            <td colspan="5" class="align-top" style="font-size: 1px;"> </td>
                        </tr>
                        <tr class="" style="font-size: 12px">
                            <td colspan="3" rowspan="2" class="align-top pt-4">
                            </td>
                            <td colspan="2" class="note text-end align-top">
                                <p class="mb-0">Note:</p>
                            </td>
                            <td colspan="2" class="note">
                                <pre style="font-family: 'Inter', Tahoma, Geneva, Verdana, sans-serif; max-width: 100%; overflow-x: auto; white-space: pre-wrap; font-size: 12px;"
                                    class="fw-semibold mb-0">
                                {{ $quote->termncon[0]->note }}
                            </pre>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="card-body" style="font-size: 16px">
                <h5 class="mt-4 mb-3">Term & Condition</h5>
                <div class="row">
                    <div class="col-3 fw-medium termc p-3">
                        <p class="mb-1">Validity Of Quotation</p>
                        <p class="mb-1">Price</p>
                        <p class="mb-1">Delivery Process</p>
                        <p class="mb-1">Payment</p>
                    </div>
                    <div class="col termc p-3">
                        <p class="mb-1">: {{ $quote->termncon[0]->validity }}</p>
                        <p class="mb-1">: {{ $quote->termncon[0]->pricing }}</p>
                        <p class="mb-1">: {{ $quote->termncon[0]->delivery_process }}</p>
                        <p class="mb-1">: {{ $quote->termncon[0]->payment }}</p>
                    </div>
                </div>
            </div>
        @else
            <div class="mb-2">
                <table class="table table-bordered m-0" style="width: 100%">
                    <thead class="table-light border-top text-center">
                        <tr class="align-middle" >
                            <th class="no" style="width: 1%;">No.</th>
                            <th style="width: 49%;">Item</th>
                            <th class="price" style="width: 10%;">Price (IDR)</th>
                            <th class="qty" style="width: 10%;">Qty</th>
                            <th class="disc" style="width: 5%;">Disc</th>
                            <th style="width: 15%;">Amount (IDR)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $abjad = 64;
                        @endphp
                        @foreach ($subQuote as $subJudul)
                            @php
                                $no = 0;
                                $abjad++;
                            @endphp
                            <tr style="font-size: 12px">
                                <td class="align-middle"
                                    style="background-color: #f0f0f0;">
                                    <p class="fw-bold mb-0">{{ chr($abjad) }}</p>
                                </td>
                                <td class="text-nowrap align-middle" colspan="5"
                                    style="background-color: #f0f0f0;">
                                    <p class="fw-bold mb-0">{{ $subJudul->subtitle }}</p>
                                </td>
                            </tr>
                            @foreach ($subJudul->detail as $product)
                                <tr
                                    style="font-size: 13px; border-bottom:none !important; border-top:none !important;">
                                    <td class="align-top py-1" style="border-bottom:none !important;">
                                        @php
                                            $no++;
                                        @endphp
                                        <p class="mb-1">{{ $no }}</p>
                                    </td>
                                    <td class="align-top" style="border-bottom:none !important;">
                                        <p class="mb-1">{{ $product->product }}</p>
                                        @if ($product->detail != '-')
                                            <pre class="mb-0"
                                                style="font-size: 13px; font-family: Inter ; max-width: 100%; overflow-x: auto; white-space: pre-wrap;">{{ $product->detail }}</pre>
                                        @endif
                                    </td>
                                    <td class="align-top py-1 text-end" style="border-bottom:none !important;">
                                        <p class="mb-0">{{ number_format($product->price, 0, '', '.') }}</p>
                                    </td>
                                    <td class="align-top py-1" style="border-bottom:none !important;">
                                        <p class="mb-0">{{ $product->qty }} {{ $product->info_qty }}</p>
                                    </td>
                                    <td class="align-top py-1 text-end" style="border-bottom:none !important;">
                                        <p class="mb-0">{{ number_format($product->disc, 0, '', '.') }}%</p>
                                    </td>
                                    <td class="align-top py-1 text-end" style="border-bottom:none !important;">
                                        <p class="mb-0">{{ number_format($product->amount, 0, '', '.') }}</p>
                                    </td>
                                </tr>
                            @endforeach
                        @endforeach
                        <tr class="border-top">
                            <td colspan="4" class="px-4 border-right" style="background-color: #E7FF00;font-size: 14px;">
                                <p class="fw-bold mb-0 text-black text-end">TOTAL PRICE,
                                    {{ $quote->tax != 0 ? 'INCLUDE' : 'EXCLUDE' }} VAT 11%</p>
                            </td>
                            <td colspan="2" class="text-end px-4 border-left" style="background-color: #E7FF00;font-size: 14px;">
                                <p class="fw-bold mb-0 text-end text-black">RP
                                    {{ number_format($quote->harga_total, 0, '', '.') }}</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            {{-- <div class="card-body mb-2">
                <h6>Note :</h6>
                <pre class="mb-0"
                    style="font-size: 10px; font-family: Inter, sans-serif; max-width: 100%; overflow-x: auto; white-space: pre-wrap; text-align: justify;">{{ $quote->termncon[0]->note }}</pre>
            </div> --}}
            <div class="card-body">
                <h5 class="my-4 fw-bold">Term & Condition</h5>
                <div class="row">
                    <div class="col-2 fw-medium">
                        <p class="mb-1">Validity of Quote</p>
                        <p class="mb-1">Price </p>
                        <p class="mb-1">Delivery Process </p>
                        <p class="mb-1">Payment </p>
                        <p class="mb-1">Warranty </p>
                    </div>
                    <div class="col">
                        <p class="mb-1">: {{ $quote->termncon[0]->validity }}</p>
                        <p class="mb-1">: {{ $quote->termncon[0]->pricing }}</p>
                        <p class="mb-1">: {{ $quote->termncon[0]->delivery_process }}</p>
                        <p class="mb-1">: {{ $quote->termncon[0]->payment }}</p>
                        <p class="mb-1">: {{ $quote->termncon[0]->warranty }}</p>
                    </div>
                </div>
            </div>
        @endif


        @if ($sellcon->type == 'Selling')
            <div class="row mt-5">
                <div class="col-4 my-3 text-center">
                    <p class="fs-6 fw-medium">Authorized By,</p>
                    @if ($quote->tax != '0')
                        <img src="{{ asset('/asset') }}/contract\sign-irene.jpeg" alt="" srcset=""
                            style="width: 100px; height: 77px;">
                    @else
                        <img src="{{ asset('/asset') }}/sign\ttdirene.jpg" alt="" srcset=""
                            style="width: 100px; height: 77px;">
                    @endif
                    <p class="pt-3">Mrs. Irene</p>
                    @if ($quote->tax != '0')
                        <p>PT. Reftech Jaya Optima</p>
                    @endif
                </div>
                <div class="col-4"></div>
                <div class="col-4 my-3 text-center">
                    <p class="fs-6 fw-medium">Accepted By Customer,</p>
                    <div class="pb-5"></div>
                    <p class="pt-5">{{ $quote->pic->name_pic }}</p>
                    <p>{{ $quote->pic->client->company }}</p>
                </div>
            </div>
        @else
            <div class="row mt-5">
                <div class="col-4 my-3 text-center">
                    <p class="fs-5 fw-medium">Authorized By,</p>
                    <img src="{{ asset('/asset') }}/contract\sign-dedeh.png" alt="" srcset=""
                        style="width: 100px; height: 77px;">
                    <p class="pt-3">Dedeh Sulastri</p>
                    <p>Director</p>
                </div>
                <div class="col-4 mb-3 text-center">
                    <p class="fs-5 fw-medium">Accepted By Customer,</p>
                    <div class="pb-5"></div>
                    <p class="pt-5">{{ $quote->pic->name_pic }}</p>
                    <p>{{ $quote->pic->client->company }}</p>
                </div>
            </div>
        @endif
    </div>
</div>
@push('after-style')
    <!-- Page CSS -->
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/css/pages/app-contract-print.css" />
    <link rel="stylesheet" href="style.css">
    <style>
        p, h5, h4, h3, h2, h1{
            color: black;
        }
    </style>
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
