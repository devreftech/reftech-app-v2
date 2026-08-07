@extends('layouts.sales.app')
@section('title', 'Invoice')
@section('content')
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light"><a href="{{ route('invoice.index') }}" class="text-muted">Accounting / Invoice</a> /</span>
        {{ $invoice->no_invoice ?? '#' . $invoice->id }}
    </h4>
    <div class="row invoice-preview">
        {{-- Invoice --}}
        <div class="col-xl-9 col-md-8 col-12 mb-md-0 mb-4">
            <div class="card invoice-preview-card" style="position: relative; overflow: hidden;">
                @if (@$lastPayment->level == 1)
                    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%) rotate(-35deg); font-size: 160px; font-weight: 900; color: rgba(40, 167, 69, 0.10); pointer-events: none; z-index: 0; letter-spacing: 12px; white-space: nowrap; user-select: none;">
                        PAID
                    </div>
                @elseif (@$lastPayment->level == 0 && @$lastPayment->file == null)
                    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%) rotate(-35deg); font-size: 140px; font-weight: 900; color: rgba(220, 53, 69, 0.10); pointer-events: none; z-index: 0; letter-spacing: 12px; white-space: nowrap; user-select: none;">
                        UNPAID
                    </div>
                @elseif (@$lastPayment->level == 0 && @$lastPayment->file != null)
                    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%) rotate(-35deg); font-size: 140px; font-weight: 900; color: rgba(255, 193, 7, 0.15); pointer-events: none; z-index: 0; letter-spacing: 12px; white-space: nowrap; user-select: none;">
                        PENDING
                    </div>
                @endif
                <div class="card-body" style="position: relative; z-index: 1;">
                    @if ($quote->pic->client->info == 'Reftech')
                        <div
                            class="d-flex justify-content-between flex-xl-row flex-md-column flex-sm-row flex-column {{ $quote->tax == 0 ? 'float-end' : '' }}">
                            @if ($quote->tax != 0)
                                <div class="mb-xl-0 pb-1">
                                    <div class="d-flex svg-illustration align-items-center gap-2 mb-4">
                                        <span class="app-brand-logo demo">
                                            <span style="color: var(--bs-primary)">
                                                <img class="text-md" src="{{ asset('/asset') }}/logo/Reftech-Log.png"
                                                    alt="" srcset="" width="60%">
                                            </span>
                                        </span>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <div class="info">
                                            <p class="mb-1 fw-bolder">Office Address :</p>
                                            <div style="font-size: 10px">
                                                <p class="mb-1">Taman Kopo Indah V, Ruko Sommerville No. 31</p>
                                                <p class="mb-1">Bandung – Jawa Barat 40218</p>
                                                <p class="mb-1">
                                                    <i class="mdi mdi-phone-outline scaleX-n1-rtl me-1 mdi-14px"></i>022
                                                    54417653
                                                    {{ '   ' }}<i
                                                        class="mdi mdi-email-outline scaleX-n1-rtl me-1 mdi-14px"></i>info@reftech.id
                                                </p>
                                                <p class="mb-1">
                                                </p>
                                            </div>
                                        </div>
                                        <div class="npwp_add">
                                            <p class="mb-1 fw-bolder">NPWP Address :</p>
                                            <pre
                                                style="font-size: 10px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; max-width: 250px; overflow-x: auto; white-space: pre-wrap;">Komp. Negia Kencana Residence Blok B, No.2 Pasanggrahan, Ujung Berung Kota Bandung - Jawa Barat 40199</pre>
                                            <p class="mb-1 text-black fw-medium p-1"
                                                style="background-color: rgb(224, 221, 255); font-size: 10px">NPWP :
                                                73.728.571.8-429.000</p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            <div class="text-end">
                                <h1 class="fw-bold" style="color: blue;">INVOICE</h1>
                                <div>
                                    <span class="fw-bolder">#{{ $invoice->no_invoice }}</span>
                                </div>
                                <div class="mt-1">
                                    <span
                                        class="text-black">{{ Carbon\Carbon::parse($invoice->date)->format('d-m-Y') }}</span>
                                </div>
                                @php
                                    if (@$lastPayment->level == 0) {
                                        if (@$lastPayment->file == null) {
                                            $warna = 'bg-label-danger text-danger';
                                            $text = 'Waiting Payment';
                                        } else {
                                            $warna = 'bg-label-warning text-warning';
                                            $text = 'Awaiting Verification';
                                        }
                                    } elseif (@$lastPayment->level == 1) {
                                        $warna = 'bg-label-success text-success';
                                        $text = 'Verified';
                                    } else {
                                        $warna = 'bg-label-dark text-dark';
                                        $text = 'belum di Payment';
                                    }

                                @endphp
                                <h6 class="mt-1 badge {{ $warna }} rounded">
                                    {{ $text }}
                                </h6>
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

                                    <div class="d-flex justify-content-between">
                                        <div class="info">
                                            <p class="mb-1 fw-bolder">Office Address :</p>
                                            <div style="font-size: 10px">
                                                <p class="mb-1">Jl. Nancep No. 45A, Setu</p>
                                                <p class="mb-1">Cibitung - Kab. Bekasi 17320</p>
                                                <p class="mb-1">
                                                    <i class="mdi mdi-phone-outline scaleX-n1-rtl me-1 mdi-14px"></i>+62
                                                    812-1000-0997
                                                    {{ ' | ' }}<i
                                                        class="mdi mdi-email-outline scaleX-n1-rtl me-1 mdi-14px"></i>admin@kojisha.com
                                                </p>
                                            </div>
                                        </div>
                                        <div class="npwp_add">
                                            <p class="mb-1 fw-bolder">NPWP Address :</p>
                                            <pre
                                                style="font-size: 10px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; max-width: 250px; overflow-x: auto; white-space: pre-wrap;">Jl. Nancep No. 45, Setu Cisaat RT. 001 RW. 003 Cibening, Setu</pre>
                                            <p class="mb-1 text-black fw-medium p-1"
                                                style="background-color: rgb(255, 235, 221)">NPWP : 96.484.859.2-413.000</p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            <div class="text-end">
                                <h1 class="fw-bold" style="color: rgb(175, 29, 23);">INVOICE</h1>
                                <div>
                                    <span class="fw-bolder">#{{ $invoice->no_invoice }}</span>
                                </div>
                                <div class="mt-1">
                                    <span
                                        class="text-muted">{{ Carbon\Carbon::parse($invoice->date)->format('d-m-Y') }}</span>
                                </div>
                                @php
                                    if (@$lastPayment->level == 0) {
                                        if (@$lastPayment->file == null) {
                                            $warna = 'text-danger';
                                            $text = 'Waiting Payment';
                                        } else {
                                            $warna = 'text-warning';
                                            $text = 'Awaiting Verification';
                                        }
                                    } elseif (@$lastPayment->level == 1) {
                                        $warna = 'text-success';
                                        $text = 'Verified';
                                    } else {
                                        $warna = 'text-dark';
                                        $text = 'belum di Payment';
                                    }

                                @endphp
                                <h6 class="mt-1 {{ $warna }}">
                                    {{ $text }}
                                </h6>
                            </div>
                        </div>
                    @endif
                </div>
                <hr class="my-0">
                <div class="card-body mb-3">
                    @php

                        if ($invoice->invoiceTo == '1') {
                            $address = $quote->pic->client->address;
                        } else {
                            $address = $quote->pic->client->subAddress;
                        }
                    @endphp
                    <h5>Invoice To</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered" style="border: 1px solid black;">
                            <tr>
                                <td rowspan="3" style="vertical-align: top; width: 50%;">
                                    <div class="row">
                                        <div class="col-4 fw-medium">
                                            <p class="mb-1">Bill To </p>
                                        </div>
                                        <div class="col-8">
                                            <pre style="font-size: 14px; font-family: Inter;" class="mb-1 fw-bolder">: {{ $quote->pic->client->company }}</pre>
                                        </div>
                                        <div class="col-4 fw-medium">
                                            <p class="mb-1">PIC </p>
                                        </div>
                                        <div class="col-8">
                                            <p class="mb-1">: {{ $quote->pic->name_pic }}</p>
                                        </div>
                                        <div class="col-4 fw-medium">
                                            <p class="mb-1">NPWP </p>
                                        </div>
                                        <div class="col-8">
                                            <p class="mb-1">: {{ $quote->pic->client->npwp }}</p>
                                        </div>
                                        <div class="col-4 fw-medium">
                                            <p class="mb-1">Phone </p>
                                        </div>
                                        <div class="col-8">
                                            <p class="mb-1">: {{ $quote->pic->client->phone }}</p>
                                        </div>
                                        <div class="col-4 fw-medium">
                                            <p class="mb-1">Address</p>
                                        </div>
                                        <div class="col-8">
                                            @if ($invoice->invoiceTo == '1')
                                                <pre style="font-size: 14px; font-family: Inter; max-width: 100%; overflow-x: auto; white-space: pre-wrap;">: {{ $quote->pic->client->address }}</pre>
                                            @else
                                                <pre style="font-size: 14px; font-family: Inter; max-width: 100%; overflow-x: auto; white-space: pre-wrap;">: {{ $quote->pic->client->subAddress }}</pre>
                                            @endif
                                        </div>
                                    </div>
                                    {{-- <p>Bill To : {{ $quote->pic->client->company }}</p>
                                    <p>Phone : {{ $quote->pic->client->phone }}</p>
                                    <p>Address : {{ $address }}</p> --}}
                                </td>
                                <td>
                                    <p>Purchase Order :</p>
                                </td>
                                <td>
                                    <p>{{ $invoice->no_po }}</p>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" style=" background-color: #F9F9F9;" class="text-center">
                                    <p class="fs-5 text-black fw-medium m-0">Term Of Payment:</p>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" class="text-center">
                                    <pre style="font-size: 14px; font-family: Inter;">{{ $invoice->term }}</pre>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                @if ($quote->type == 'Sparepart')
                    <div class="table-responsive">
                        <table class="table table-bordered m-0" style="border: 1px solid rgb(60, 60, 60)">
                            <thead class="table-light">
                                <tr>
                                    <th>No.</th>
                                    <th>Item</th>
                                    <th>Price</th>
                                    <th>Qty</th>
                                    <th style="width: 5%">Disc</th>
                                    @if ($quote->tax != 0)
                                        <th style="width: 15%">DPP</th>
                                    @endif
                                    <th style="width:20%">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    // $totalPph = 0;
                                    $no = 0;
                                @endphp
                                @foreach ($dquote as $product)
                                    @php
                                        $no++;
                                        // $pph = ($product->amount * $product->pph) / 100;
                                        // $totalPph += $pph;
                                        $dpp = ($product->amount * 11) / 12;
                                    @endphp
                                    <tr style="font-size: 13px">
                                        <td class="align-top">{{ $no }}</td>
                                        <td class="text-nowrap align-top">
                                            <p class="mb-0 fw-medium" style="font-size: 12px">
                                                {{ $product->equivalent->brand }} {{ $product->equivalent->pn }}
                                            </p>
                                            <pre class="mb-0"
                                                style="font-size: 10px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; max-width: 100%; overflow-x: auto; white-space: pre-wrap;">{{ $product->detail_product }}</pre>
                                        </td>
                                        <td class="align-top text-end">RP {{ number_format($product->price, 0, '', '.') }}
                                        </td>
                                        <td class="align-top">{{ $product->qty }} {{ $product->info_qty }} </td>
                                        <td class="align-top">{{ $product->disc }}%</td>
                                        @if ($quote->tax != 0)
                                            <td class="align-top text-end">RP {{ number_format($dpp, 0, '', '.') }}
                                        @endif
                                        <td class="align-top text-end">RP
                                            {{ number_format($product->amount, 0, '', '.') }}

                                        </td>
                                    </tr>
                                @endforeach
                                <tr class="fw-medium" style="font-size: 13px">
                                    <td colspan="{{ $quote->tax != 0 ? '3' : '2' }}" rowspan="9" id="dynamicRows"
                                        style="border-bottom :none !important;">
                                    </td>
                                    <td colspan="3" id="price" class="text-end pl-4 py-0"
                                        style="padding-right: 10px !important;">
                                        <p class="m-0">
                                            {{ $quote->tax != 0 || $invoice->pph != 0 || $quote->shipping != 0 ? 'Subtotal' : 'Total' }}
                                        </p>
                                        {{-- <p class="m-0">Total</p> --}}
                                    </td>
                                    <td id="price" class="pr-4 py-0" style="padding-left: 0 !important;">
                                        <p class="text-end m-0">RP
                                            {{ number_format($quote->subtotal, 0, '', '.') }}</p>
                                    </td>
                                </tr>
                                @php
                                    if ($quote->pic->client->info == 'Reftech') {
                                        $bgColor = 'rgb(224, 248, 248)';
                                    } else {
                                        $bgColor = 'rgb(255, 232, 210)';
                                    }
                                @endphp
                                @if ($invoice->type == 'CT')
                                    @if ($quote->diskon != 0)
                                        <tr class="fw-medium" style="font-size: 13px">
                                            <td colspan="3" class="text-end py-0"
                                                style="padding-right: 10px !important;">
                                                <p class="m-0">Discount</p>
                                            </td>
                                            <td class="pr-4 py-0" style="padding-left: 0 !important;">
                                                <p class="m-0 text-end">RP
                                                    {{ number_format($quote->diskon, 0, '', '.') }}
                                                </p>
                                            </td>
                                        </tr>
                                        <tr class="fw-medium" style="font-size: 13px">
                                            <td colspan="3" class="text-end py-0"
                                                style="padding-right: 10px !important;">
                                                <p class="m-0">Total After Discount</p>
                                            </td>
                                            <td class="pr-4 py-0" style="padding-left: 0 !important;">
                                                <p class="m-0 text-end">RP
                                                    {{ number_format($afterDisc, 0, '', '.') }}
                                                </p>
                                            </td>
                                        </tr>
                                        @if ($quote->tax != 0)
                                            <tr class="fw-medium" style="font-size: 13px">
                                                <td colspan="3" id="price" class="text-end pl-4 py-0"
                                                    style="padding-right: 10px !important;">
                                                    <p class="m-0">
                                                        DPP Atas PPN
                                                    </p>
                                                </td>
                                                <td id="price" class="pr-4 py-0" style="padding-left: 0 !important;">
                                                    @php
                                                        $dpp = ($afterDisc * 11) / 12;
                                                    @endphp
                                                    <p class="text-end m-0">RP
                                                        {{ number_format($dpp, 0, '', '.') }}</p>
                                                </td>
                                            </tr>
                                        @endif
                                    @else
                                        @if ($quote->tax != 0)
                                            <tr class="fw-medium" style="font-size: 13px">
                                                <td colspan="3" id="price" class="text-end pl-4 py-0"
                                                    style="padding-right: 10px !important;">
                                                    <p class="m-0">
                                                        DPP Atas PPN
                                                    </p>
                                                </td>
                                                <td id="price" class="pr-4 py-0" style="padding-left: 0 !important;">
                                                    @php
                                                        $dpp = ($quote->subtotal * 11) / 12;
                                                    @endphp
                                                    <p class="text-end m-0">RP
                                                        {{ number_format($dpp, 0, '', '.') }}</p>
                                                </td>
                                            </tr>
                                        @endif
                                    @endif
                                    @if ($quote->tax != 0 || $totalPph > 0)
                                        <tr class="fw-medium py-0" style="font-size: 13px">
                                            <td colspan="3" class="text-end py-0"
                                                style="padding-right: 10px !important;">
                                                <p class="m-0">VAT {{ $quote->tax == '11' ? '12%' : '' }}</p>
                                            </td>
                                            <td class="pr-4 py-0" style="padding-left: 0 !important;">
                                                <p class="m-0 text-end">
                                                    {{ $tax == '0' ? '0' : 'RP ' . number_format($tax, 0, '', '.') }}</p>
                                            </td>
                                        </tr>
                                        @if ($totalPph > 0)
                                            <tr class="fw-medium py-0" style="font-size: 13px">
                                                <td colspan="3" class="text-end py-0"
                                                    style="padding-right: 10px !important;">
                                                    <p class="m-0">PPH</p>
                                                </td>
                                                <td class="pr-4 py-0" style="padding-left: 0 !important;">
                                                    <p class="m-0 text-end">
                                                        {{ $totalPph == '0' ? '0' : 'RP ' . number_format($totalPph, 0, '', '.') }}
                                                    </p>
                                                </td>
                                            </tr>
                                        @endif
                                    @endif
                                    @if ($quote->shipping != 0)
                                        <tr class="fw-medium" style="font-size: 13px">
                                            <td colspan="3" class="text-end py-0"
                                                style="padding-right: 10px !important;">
                                                <p class="m-0">Shipping Cost</p>
                                            </td>
                                            <td class="pr-4 py-0" style="padding-left: 0 !important;">
                                                <p class="m-0 text-end">RP
                                                    {{ number_format($quote->shipping, 0, '', '.') }}
                                                </p>
                                            </td>
                                        </tr>
                                    @endif
                                    @if ($quote->tax != 0 || $totalPph > 0 || $quote->shipping != 0)
                                        <tr class="fw-medium py-0" style="font-size: 13px">
                                            <td colspan="3" class="text-end py-0"
                                                style="background-color: {{ $bgColor }}; padding-left:20px; padding-right:10px;">
                                                <p class="m-0 fw-bold">Total</p>
                                            </td>
                                            <td class="pr-4 py-0"
                                                style="background-color: {{ $bgColor }}; padding-right:20px;">
                                                <p class="m-0 text-end fw-bold">
                                                    {{ 'RP ' . number_format($quote->harga_total - $totalPph, 0, '', '.') }}
                                                </p>
                                            </td>
                                        </tr>
                                    @endif
                                @else
                                    @if ($quote->diskon != 0)
                                        <tr class="fw-medium" style="font-size: 13px">
                                            <td colspan="2" class="text-end py-0"
                                                style="padding-right: 10px !important;">
                                                <p class="m-0">Discount</p>
                                            </td>
                                            <td class="pr-4 py-0" style="padding-left: 0 !important;">
                                                <p class="m-0 text-end">RP
                                                    {{ number_format($quote->diskon, 0, '', '.') }}
                                                </p>
                                            </td>
                                        </tr>
                                        <tr class="fw-medium" style="font-size: 13px">
                                            <td colspan="2" class="text-end py-0"
                                                `style="padding-right: 10px !important;">
                                                <p class="m-0">Total After Discount</p>
                                            </td>
                                            <td class="pr-4 py-0" style="padding-left: 0 !important;">
                                                <p class="m-0 text-end">RP
                                                    {{ number_format($afterDisc, 0, '', '.') }}
                                                </p>
                                            </td>
                                        </tr>
                                    @endif
                                    @foreach ($payments as $pay)
                                        @php
                                            $amount = $pay->amount / (1 + $quote->tax / 100);
                                            $vat = $amount * ($quote->tax / 100);
                                            $payamount = $pay->amount;
                                            $totalwithpph = $pay->amount - $totalPph;
                                        @endphp
                                        <tr class="fw-medium" style="font-size: 13px">
                                            <td colspan="3" class="text-end py-0 px-0">
                                                <p class="m-0"
                                                    style="{{ $loop->last ? 'background-color: yellow;' : '' }} padding-left:20px; padding-right:10px;">
                                                    {{ $pay->note }}
                                                    {{ $pay->percent }}%:</p>
                                            </td>
                                            <td class="px-0 py-0" style="padding-left: 0 !important;">
                                                <p class="fw-medium m-0 text-end"
                                                    style="{{ $loop->last ? 'background-color: yellow;' : '' }} padding-right:20px;">
                                                    RP
                                                    {{ number_format($amount, 0, '', '.') }}</p>
                                            </td>
                                        </tr>
                                    @endforeach
                                    @if ($quote->tax != 0)
                                        <tr class="fw-medium" style="font-size: 13px">
                                            <td colspan="3" id="price" class="text-end pl-4 py-0"
                                                style="padding-right: 10px !important;">
                                                <p class="m-0">
                                                    DPP Atas PPN
                                                </p>
                                            </td>
                                            <td id="price" class="pr-4 py-0" style="padding-left: 0 !important;">
                                                @php
                                                    $dpp = ($amount * 11) / 12;
                                                @endphp
                                                <p class="text-end m-0">RP
                                                    {{ number_format($dpp, 0, '', '.') }}</p>
                                            </td>
                                        </tr>
                                        <tr class="fw-medium py-0" style="font-size: 13px">
                                            <td colspan="3" class="text-end py-0"
                                                style="padding-right: 10px !important;">
                                                <p class="m-0">VAT {{ $quote->tax == '11' ? '12%' : '' }}</p>
                                            </td>
                                            <td class="pr-4 py-0" style="padding-left: 0 !important;">
                                                <p class="m-0 text-end">
                                                    {{ $vat == '0' ? '0' : 'RP ' . number_format($vat, 0, '', '.') }}</p>
                                            </td>
                                        </tr>
                                        @if ($totalPph > 0)
                                            <tr class="fw-medium py-0" style="font-size: 13px">
                                                <td colspan="3" class="text-end py-0"
                                                    style="padding-right: 10px !important;">
                                                    <p class="m-0">PPH</p>
                                                </td>
                                                <td class="pr-4 py-0" style="padding-left: 0 !important;">
                                                    <p class="m-0 text-end">
                                                        {{ $totalPph == '0' ? '0' : 'RP ' . number_format($totalPph, 0, '', '.') }}
                                                    </p>
                                                </td>
                                            </tr>
                                        @endif
                                        @if ($quote->shipping != 0)
                                            <tr class="fw-medium" style="font-size: 13px">
                                                <td colspan="3" class="text-end py-0"
                                                    style="padding-right: 10px !important;">
                                                    <p class="m-0">Shipping Cost</p>
                                                </td>
                                                <td class="pr-4 py-0" style="padding-left: 0 !important;">
                                                    <p class="m-0 text-end">RP
                                                        {{ number_format($quote->shipping, 0, '', '.') }}
                                                    </p>
                                                </td>
                                            </tr>
                                        @endif
                                        <tr class="fw-medium py-0" style="font-size: 13px">
                                            <td colspan="3" class="text-end py-0"
                                                style="background-color: {{ $bgColor }}; padding-left:20px; padding-right:10px;">
                                                <p class="m-0 fw-bold">Total Include VAT</p>
                                            </td>
                                            <td class="pr-4 py-0"
                                                style="background-color: {{ $bgColor }}; padding-right:20px;">
                                                <p class="m-0 text-end fw-bold">
                                                    Rp {{ number_format($totalwithpph, 0, '', '.') }}
                                                </p>
                                            </td>
                                        </tr>
                                    @else
                                        @if ($quote->shipping != 0)
                                            <tr class="fw-medium" style="font-size: 13px">
                                                <td colspan="3" class="text-end py-0"
                                                    style="padding-right: 10px !important;">
                                                    <p class="m-0">Shipping Cost</p>
                                                </td>
                                                <td class="pr-4 py-0" style="padding-left: 0 !important;">
                                                    <p class="m-0 text-end">RP
                                                        {{ number_format($quote->shipping, 0, '', '.') }}
                                                    </p>
                                                </td>
                                            </tr>
                                        @endif
                                        <tr class="fw-medium py-0" style="font-size: 13px">
                                            <td colspan="3" class="text-end py-0"
                                                style="background-color: {{ $bgColor }}; padding-left:20px; padding-right:10px;">
                                                <p class="m-0">Total</p>
                                            </td>
                                            <td class="pr-4 py-0"
                                                style="background-color: {{ $bgColor }}; padding-right:20px;">
                                                <p class="m-0 text-end fw-bold">
                                                    {{ number_format($payamount, 0, '', '.') }}
                                                </p>
                                            </td>
                                        </tr>
                                    @endif
                                @endif
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered m-0"
                            style="border: 1px solid rgb(60, 60, 60);border-collapse: collapse;">
                            <thead class="table-light border-top">
                                <tr>
                                    <th style="width: 1%">No.</th>
                                    <th style="width: 35%">Item Description</th>
                                    <th style="width: 15%">Price</th>
                                    <th style="width: 10%">Qty</th>
                                    <th>Disc</th>
                                    @if ($quote->tax != 0)
                                        <th style="width: 15%">DPP</th>
                                    @endif
                                    <th style="width: 20%">Total Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $abjad = 64;
                                    // $totalPph = 0;
                                @endphp
                                @foreach ($subQuote as $subJudul)
                                    @php
                                        $no = 0;
                                        $abjad++;
                                    @endphp
                                    <tr style="font-size: 13px border-bottom:none !important;" class="border-top">
                                        <td class="align-top"
                                            style="border-bottom:none !important; background-color: #f0f0f0;">
                                            <p class="fw-bold mb-0">{{ chr($abjad) }}</p>
                                        </td>
                                        <td class="text-nowrap align-top" colspan="{{ $quote->tax != 0 ? '6' : '5' }}"
                                            style="border-bottom:none !important; background-color: #f0f0f0;">
                                            <p class="fw-bold mb-0">{{ $subJudul->subtitle }}</p>
                                        </td>
                                    </tr>
                                    @foreach ($subJudul->detail as $product)
                                        @php
                                            // $pph = ($product->amount * $product->pph) / 100;
                                            // $totalPph += $pph;
                                            $dpp = ($product->amount * 11) / 12;
                                        @endphp
                                        <tr
                                            style="font-size: 13px; border-bottom:none !important; border-top:none !important;">
                                            <td class="align-top py-1" style="border-bottom:none !important;">
                                                @php
                                                    $no++;
                                                @endphp
                                                <p class="mb-1">{{ $no }}</p>
                                            </td>
                                            <td class="text-wrap align-middle py-1"
                                                style="border-bottom:none !important;">
                                                <p class="mb-0">{{ $product->product }}</p>
                                                {{-- @if (@$product->detail != '-')
                                                    <pre class="mb-0"
                                                        style="font-size: 13px; font-family: Inter, sans-serif; max-width: 100%; overflow-x: auto; white-space: pre-wrap;">{{ $product->detail }}</pre>
                                                @endif --}}
                                            </td>
                                            <td class="align-top py-1 text-end" style="border-bottom:none !important;">
                                                <p class="mb-0">RP {{ number_format($product->price, 0, '', '.') }}</p>
                                            </td>
                                            <td class="align-top py-1" style="border-bottom:none !important;">
                                                <p class="mb-0">{{ $product->qty }} {{ $product->info_qty }}</p>
                                            </td>
                                            <td class="align-top py-1 text-end" style="border-bottom:none !important;">
                                                <p class="mb-0">{{ $product->disc }} %</p>
                                            </td>
                                            @if ($quote->tax != 0)
                                                <td class="align-top py-1 text-end"
                                                    style="border-bottom:none !important;">
                                                    <p class="mb-0">RP {{ number_format($dpp, 0, '', '.') }}</p>
                                                </td>
                                            @endif
                                            <td class="align-top py-1 text-end" style="border-bottom:none !important;">
                                                <p class="mb-0">RP {{ number_format($product->amount, 0, '', '.') }}</p>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endforeach
                                <tr class="fw-medium" style="font-size: 13px">
                                    <td colspan="{{ $quote->tax != 0 ? '4' : '3' }}" rowspan="9" id="dynamicRows"
                                        style="border-bottom :none !important;">
                                    </td>
                                    <td colspan="2" id="price" class="text-end pl-4 py-0"
                                        style="padding-right: 10px !important;">
                                        <p class="m-0">
                                            {{ $quote->tax != 0 || $invoice->pph != 0 || $quote->shipping != 0 ? 'Subtotal' : 'Total' }}
                                        </p>
                                        {{-- <p class="m-0">Total</p> --}}
                                    </td>
                                    <td id="price" class="pr-4 py-0" style="padding-left: 0 !important;">
                                        <p class="text-end m-0">RP
                                            {{ number_format($quote->subtotal, 0, '', '.') }}</p>
                                    </td>
                                </tr>
                                @php
                                    if ($quote->pic->client->info == 'Reftech') {
                                        $bgColor = 'rgb(224, 248, 248)';
                                    } else {
                                        $bgColor = 'rgb(255, 232, 210)';
                                    }
                                @endphp
                                @if ($invoice->type == 'CT')
                                    @if ($quote->diskon != 0)
                                        <tr class="fw-medium" style="font-size: 13px">
                                            <td colspan="2" class="text-end py-0"
                                                style="padding-right: 10px !important;">
                                                <p class="m-0">Discount</p>
                                            </td>
                                            <td class="pr-4 py-0" style="padding-left: 0 !important;">
                                                <p class="m-0 text-end">RP
                                                    {{ number_format($quote->diskon, 0, '', '.') }}
                                                </p>
                                            </td>
                                        </tr>
                                        <tr class="fw-medium" style="font-size: 13px">
                                            <td colspan="2" class="text-end py-0"
                                                style="padding-right: 10px !important;">
                                                <p class="m-0">Total After Discount</p>
                                            </td>
                                            <td class="pr-4 py-0" style="padding-left: 0 !important;">
                                                <p class="m-0 text-end">RP
                                                    {{ number_format($afterDisc, 0, '', '.') }}
                                                </p>
                                            </td>
                                        </tr>
                                        @if ($quote->tax != 0)
                                            <tr class="fw-medium" style="font-size: 13px">
                                                <td colspan="2" id="price" class="text-end pl-4 py-0"
                                                    style="padding-right: 10px !important;">
                                                    <p class="m-0">
                                                        DPP Atas PPN
                                                    </p>
                                                </td>
                                                <td id="price" class="pr-4 py-0" style="padding-left: 0 !important;">
                                                    @php
                                                        $dpp = ($afterDisc * 11) / 12;
                                                    @endphp
                                                    <p class="text-end m-0">RP
                                                        {{ number_format($dpp, 0, '', '.') }}</p>
                                                </td>
                                            </tr>
                                        @endif
                                    @else
                                        @if ($quote->tax != 0)
                                            <tr class="fw-medium" style="font-size: 13px">
                                                <td colspan="2" id="price" class="text-end pl-4 py-0"
                                                    style="padding-right: 10px !important;">
                                                    <p class="m-0">
                                                        DPP Atas PPN
                                                    </p>
                                                </td>
                                                <td id="price" class="pr-4 py-0" style="padding-left: 0 !important;">
                                                    @php
                                                        $dpp = ($quote->subtotal * 11) / 12;
                                                    @endphp
                                                    <p class="text-end m-0">RP
                                                        {{ number_format($dpp, 0, '', '.') }}</p>
                                                </td>
                                            </tr>
                                        @endif
                                    @endif
                                    @if ($quote->tax != 0 || $totalPph > 0)
                                        <tr class="fw-medium py-0" style="font-size: 13px">
                                            <td colspan="2" class="text-end py-0"
                                                style="padding-right: 10px !important;">
                                                <p class="m-0">VAT {{ $quote->tax == '11' ? '12%' : '' }}</p>
                                            </td>
                                            <td class="pr-4 py-0" style="padding-left: 0 !important;">
                                                <p class="m-0 text-end">
                                                    {{ $tax == '0' ? '0' : 'RP ' . number_format($tax, 0, '', '.') }}</p>
                                            </td>
                                        </tr>
                                        @if ($totalPph > 0)
                                            <tr class="fw-medium py-0" style="font-size: 13px">
                                                <td colspan="2" class="text-end py-0"
                                                    style="padding-right: 10px !important;">
                                                    <p class="m-0">PPH</p>
                                                </td>
                                                <td class="pr-4 py-0" style="padding-left: 0 !important;">
                                                    <p class="m-0 text-end">
                                                        {{ $totalPph == '0' ? '0' : 'RP ' . number_format($totalPph, 0, '', '.') }}
                                                    </p>
                                                </td>
                                            </tr>
                                        @endif
                                    @endif
                                    @if ($quote->shipping != 0)
                                        <tr class="fw-medium" style="font-size: 13px">
                                            <td colspan="2" class="text-end py-0"
                                                style="padding-right: 10px !important;">
                                                <p class="m-0">Shipping Cost</p>
                                            </td>
                                            <td class="pr-4 py-0" style="padding-left: 0 !important;">
                                                <p class="m-0 text-end">RP
                                                    {{ number_format($quote->shipping, 0, '', '.') }}
                                                </p>
                                            </td>
                                        </tr>
                                    @endif
                                    @if ($quote->tax != 0 || $totalPph > 0 || $quote->shipping != 0)
                                        <tr class="fw-medium py-0" style="font-size: 13px">
                                            <td colspan="2" class="text-end py-0"
                                                style="background-color: {{ $bgColor }}; padding-left:20px; padding-right:10px;">
                                                <p class="m-0 fw-bold">Total</p>
                                            </td>
                                            <td class="pr-4 py-0"
                                                style="background-color: {{ $bgColor }}; padding-right:20px;">
                                                <p class="m-0 text-end fw-bold">
                                                    {{ 'RP ' . number_format($quote->harga_total - $totalPph, 0, '', '.') }}
                                                </p>
                                            </td>
                                        </tr>
                                    @endif
                                @else
                                    @if ($quote->diskon != 0)
                                        <tr class="fw-medium" style="font-size: 13px">
                                            <td colspan="2" class="text-end py-0"
                                                style="padding-right: 10px !important;">
                                                <p class="m-0">Discount</p>
                                            </td>
                                            <td class="pr-4 py-0" style="padding-left: 0 !important;">
                                                <p class="m-0 text-end">RP
                                                    {{ number_format($quote->diskon, 0, '', '.') }}
                                                </p>
                                            </td>
                                        </tr>
                                        <tr class="fw-medium" style="font-size: 13px">
                                            <td colspan="2" class="text-end py-0"
                                                `style="padding-right: 10px !important;">
                                                <p class="m-0">Total After Discount</p>
                                            </td>
                                            <td class="pr-4 py-0" style="padding-left: 0 !important;">
                                                <p class="m-0 text-end">RP
                                                    {{ number_format($afterDisc, 0, '', '.') }}
                                                </p>
                                            </td>
                                        </tr>
                                    @endif
                                    @foreach ($payments as $pay)
                                        @php
                                            $amount = $pay->amount / (1 + $quote->tax / 100);
                                            $vat = $amount * ($quote->tax / 100);
                                            $payamount = $pay->amount;
                                            $totalwithpph = $pay->amount - $totalPph;
                                        @endphp
                                        <tr class="fw-medium" style="font-size: 13px">
                                            <td colspan="2" class="text-end py-0 px-0">
                                                <p class="m-0"
                                                    style="{{ $loop->last ? 'background-color: yellow;' : '' }} padding-left:20px; padding-right:10px;">
                                                    {{ $pay->note }}
                                                    {{ $pay->percent }}%:</p>
                                            </td>
                                            <td class="px-0 py-0" style="padding-left: 0 !important;">
                                                <p class="fw-medium m-0 text-end"
                                                    style="{{ $loop->last ? 'background-color: yellow;' : '' }} padding-right:20px;">
                                                    RP
                                                    {{ number_format($amount, 0, '', '.') }}</p>
                                            </td>
                                        </tr>
                                    @endforeach
                                    @if ($quote->tax != 0)
                                        <tr class="fw-medium" style="font-size: 13px">
                                            <td colspan="2" id="price" class="text-end pl-4 py-0"
                                                style="padding-right: 10px !important;">
                                                <p class="m-0">
                                                    DPP Atas PPN
                                                </p>
                                            </td>
                                            <td id="price" class="pr-4 py-0" style="padding-left: 0 !important;">
                                                @php
                                                    $dpp = ($amount * 11) / 12;
                                                @endphp
                                                <p class="text-end m-0">RP
                                                    {{ number_format($dpp, 0, '', '.') }}</p>
                                            </td>
                                        </tr>
                                        <tr class="fw-medium py-0" style="font-size: 13px">
                                            <td colspan="2" class="text-end py-0"
                                                style="padding-right: 10px !important;">
                                                <p class="m-0">VAT {{ $quote->tax == '11' ? '12%' : '' }}</p>
                                            </td>
                                            <td class="pr-4 py-0" style="padding-left: 0 !important;">
                                                <p class="m-0 text-end">
                                                    {{ $vat == '0' ? '0' : 'RP ' . number_format($vat, 0, '', '.') }}</p>
                                            </td>
                                        </tr>
                                        @if ($totalPph > 0)
                                            <tr class="fw-medium py-0" style="font-size: 13px">
                                                <td colspan="2" class="text-end py-0"
                                                    style="padding-right: 10px !important;">
                                                    <p class="m-0">PPH</p>
                                                </td>
                                                <td class="pr-4 py-0" style="padding-left: 0 !important;">
                                                    <p class="m-0 text-end">
                                                        {{ $totalPph == '0' ? '0' : 'RP ' . number_format($totalPph, 0, '', '.') }}
                                                    </p>
                                                </td>
                                            </tr>
                                        @endif
                                        @if ($quote->shipping != 0)
                                            <tr class="fw-medium" style="font-size: 13px">
                                                <td colspan="2" class="text-end py-0"
                                                    style="padding-right: 10px !important;">
                                                    <p class="m-0">Shipping Cost</p>
                                                </td>
                                                <td class="pr-4 py-0" style="padding-left: 0 !important;">
                                                    <p class="m-0 text-end">RP
                                                        {{ number_format($quote->shipping, 0, '', '.') }}
                                                    </p>
                                                </td>
                                            </tr>
                                        @endif
                                        <tr class="fw-medium py-0" style="font-size: 13px">
                                            <td colspan="2" class="text-end py-0"
                                                style="background-color: {{ $bgColor }}; padding-left:20px; padding-right:10px;">
                                                <p class="m-0 fw-bold">Total Include VAT</p>
                                            </td>
                                            <td class="pr-4 py-0"
                                                style="background-color: {{ $bgColor }}; padding-right:20px;">
                                                <p class="m-0 text-end fw-bold">
                                                    Rp {{ number_format($totalwithpph, 0, '', '.') }}
                                                </p>
                                            </td>
                                        </tr>
                                    @else
                                        @if ($quote->shipping != 0)
                                            <tr class="fw-medium" style="font-size: 13px">
                                                <td colspan="2" class="text-end py-0"
                                                    style="padding-right: 10px !important;">
                                                    <p class="m-0">Shipping Cost</p>
                                                </td>
                                                <td class="pr-4 py-0" style="padding-left: 0 !important;">
                                                    <p class="m-0 text-end">RP
                                                        {{ number_format($quote->shipping, 0, '', '.') }}
                                                    </p>
                                                </td>
                                            </tr>
                                        @endif
                                        <tr class="fw-medium py-0" style="font-size: 13px">
                                            <td colspan="2" class="text-end py-0"
                                                style="background-color: {{ $bgColor }}; padding-left:20px; padding-right:10px;">
                                                <p class="m-0">Total</p>
                                            </td>
                                            <td class="pr-4 py-0"
                                                style="background-color: {{ $bgColor }}; padding-right:20px;">
                                                <p class="m-0 text-end fw-bold">
                                                    {{ number_format($payamount, 0, '', '.') }}
                                                </p>
                                            </td>
                                        </tr>
                                    @endif
                                    {{-- @elseif ($invoice->type == 'DP')
                                    @php
                                        $amount1 = $payments[0]->amount / (1 + $quote->tax / 100);
                                        $vat = $amount1 * ($quote->tax / 100);
                                    @endphp
                                    @if ($quote->diskon != 0)
                                        <tr class="fw-medium" style="font-size: 13px">
                                            <td colspan="2" class="text-end py-0"
                                                style="padding-right: 10px !important;">
                                                <p class="m-0">Discount</p>
                                            </td>
                                            <td class="pr-4 py-0" style="padding-left: 0 !important;">
                                                <p class="m-0 text-end">RP
                                                    {{ number_format($quote->diskon, 0, '', '.') }}
                                                </p>
                                            </td>
                                        </tr>
                                        <tr class="fw-medium" style="font-size: 13px">
                                            <td colspan="2" class="text-end py-0"
                                                `style="padding-right: 10px !important;">
                                                <p class="m-0">Total After Discount</p>
                                            </td>
                                            <td class="pr-4 py-0" style="padding-left: 0 !important;">
                                                <p class="m-0 text-end">RP
                                                    {{ number_format($afterDisc, 0, '', '.') }}
                                                </p>
                                            </td>
                                        </tr>
                                    @endif
                                    <tr class="fw-medium" style="font-size: 13px">
                                        <td colspan="2" class="text-end py-0 px-0">
                                            <p class="m-0"
                                                style="background-color: yellow; padding-left:20px; padding-right:10px;">
                                                {{ $payments[0]->note }}
                                                {{ $payments[0]->percent }}%:</p>
                                        </td>
                                        <td class="px-0 py-0" style="padding-left: 0 !important;">
                                            <p class="fw-medium m-0 text-end"
                                                style="background-color: yellow; padding-right:20px;">
                                                RP
                                                {{ number_format($amount1, 0, '', '.') }}</p>
                                        </td>
                                    </tr>
                                    @if ($quote->tax != 0)
                                        <tr class="fw-medium" style="font-size: 13px">
                                            <td colspan="2" id="price" class="text-end pl-4 py-0"
                                                style="padding-right: 10px !important;">
                                                <p class="m-0">
                                                    DPP Atas PPN
                                                </p>
                                            </td>
                                            <td id="price" class="pr-4 py-0" style="padding-left: 0 !important;">
                                                @php
                                                    $dpp = ($amount1 * 11) / 12;
                                                @endphp
                                                <p class="text-end m-0">RP
                                                    {{ number_format($dpp, 0, '', '.') }}</p>
                                            </td>
                                        </tr>
                                        <tr class="fw-medium py-0" style="font-size: 13px">
                                            <td colspan="2" class="text-end py-0"
                                                style="padding-right: 10px !important;">
                                                <p class="m-0">VAT {{ $quote->tax == '11' ? '12%' : '' }}</p>
                                            </td>
                                            <td class="pr-4 py-0" style="padding-left: 0 !important;">
                                                <p class="m-0 text-end">
                                                    {{ $vat == '0' ? '0' : 'RP ' . number_format($vat, 0, '', '.') }}</p>
                                            </td>
                                        </tr>
                                        @if ($totalPph > 0)
                                            <tr class="fw-medium py-0" style="font-size: 13px">
                                                <td colspan="2" class="text-end py-0"
                                                    style="padding-right: 10px !important;">
                                                    <p class="m-0">PPH</p>
                                                </td>
                                                <td class="pr-4 py-0" style="padding-left: 0 !important;">
                                                    <p class="m-0 text-end">
                                                        {{ $totalPph == '0' ? '0' : 'RP ' . number_format($totalPph, 0, '', '.') }}
                                                    </p>
                                                </td>
                                            </tr>
                                        @endif
                                        @php
                                            $totalwithpph = $payments[0]->amount - $totalPph;
                                        @endphp
                                        @if ($quote->shipping != 0)
                                            <tr class="fw-medium" style="font-size: 13px">
                                                <td colspan="2" class="text-end py-0"
                                                    style="padding-right: 10px !important;">
                                                    <p class="m-0">Shipping Cost</p>
                                                </td>
                                                <td class="pr-4 py-0" style="padding-left: 0 !important;">
                                                    <p class="m-0 text-end">RP
                                                        {{ number_format($quote->shipping, 0, '', '.') }}
                                                    </p>
                                                </td>
                                            </tr>
                                        @endif
                                        <tr class="fw-medium py-0" style="font-size: 13px">
                                            <td colspan="2" class="text-end py-0"
                                                style="background-color: {{ $bgColor }}; padding-left:20px; padding-right:10px;">
                                                <p class="m-0 fw-bold">Total Include VAT</p>
                                            </td>
                                            <td class="pr-4 py-0"
                                                style="background-color: {{ $bgColor }}; padding-right:20px;">
                                                <p class="m-0 text-end fw-bold">
                                                    Rp {{ number_format($totalwithpph, 0, '', '.') }}
                                                </p>
                                            </td>
                                        </tr>
                                    @else
                                        @if ($quote->shipping != 0)
                                            <tr class="fw-medium" style="font-size: 13px">
                                                <td colspan="2" class="text-end py-0"
                                                    style="padding-right: 10px !important;">
                                                    <p class="m-0">Shipping Cost</p>
                                                </td>
                                                <td class="pr-4 py-0" style="padding-left: 0 !important;">
                                                    <p class="m-0 text-end">RP
                                                        {{ number_format($quote->shipping, 0, '', '.') }}
                                                    </p>
                                                </td>
                                            </tr>
                                        @endif
                                        <tr class="fw-medium py-0" style="font-size: 13px">
                                            <td colspan="2" class="text-end py-0"
                                                style="background-color: {{ $bgColor }}; padding-left:20px; padding-right:10px;">
                                                <p class="m-0">Total</p>
                                            </td>
                                            <td class="pr-4 py-0"
                                                style="background-color: {{ $bgColor }}; padding-right:20px;">
                                                <p class="m-0 text-end fw-bold">
                                                    {{ number_format($payments[0]->amount, 0, '', '.') }}
                                                </p>
                                            </td>
                                        </tr>
                                    @endif
                                @elseif ($invoice->type == 'BP')
                                    @php
                                        $amount1 = $payments[0]->amount / (1 + $quote->tax / 100);
                                        $amount2 = $payments[1]?->amount / (1 + $quote->tax / 100);
                                        $vat = $amount2 * ($quote->tax / 100);
                                    @endphp
                                    @if ($quote->diskon != 0)
                                        <tr class="fw-medium" style="font-size: 13px">
                                            <td colspan="2" class="text-end py-0"
                                                style="padding-right: 10px !important;">
                                                <p class="m-0">Discount</p>
                                            </td>
                                            <td class="pr-4 py-0" style="padding-left: 0 !important;">
                                                <p class="m-0 text-end">RP
                                                    {{ number_format($quote->diskon, 0, '', '.') }}
                                                </p>
                                            </td>
                                        </tr>
                                        <tr class="fw-medium" style="font-size: 13px">
                                            <td colspan="2" class="text-end py-0"
                                                style="padding-right: 10px !important;">
                                                <p class="m-0">Total After Discount</p>
                                            </td>
                                            <td class="pr-4 py-0" style="padding-left: 0 !important;">
                                                <p class="m-0 text-end">RP
                                                    {{ number_format($afterDisc, 0, '', '.') }}
                                                </p>
                                            </td>
                                        </tr>
                                    @endif
                                    <tr class="fw-medium" style="font-size: 13px">
                                        <td colspan="2" class="text-end py-0" style="padding-right: 10px !important;">
                                            <p class="m-0">
                                                {{ $payments[0]->note }}
                                                {{ $payments[0]->percent }}%:</p>
                                        </td>
                                        <td class="pr-4 py-0" style="padding-left: 0 !important;">
                                            <p class="m-0 text-end">
                                                RP
                                                {{ number_format($amount1, 0, '', '.') }}</p>
                                        </td>
                                    </tr>
                                    <tr class="fw-medium" style="font-size: 13px">
                                        <td colspan="2" class="text-end py-0 px-0">
                                            <p class="m-0"
                                                style="background-color: yellow; padding-left:20px; padding-right:10px;">
                                                {{ $payments[1]?->note }}
                                                {{ $payments[1]?->percent }}%:</p>
                                        </td>
                                        <td class="px-0 py-0" style="padding-left: 0 !important;">
                                            <p class="m-0 text-end" style="background-color: yellow; padding-right:20px;">
                                                RP
                                                {{ number_format($amount2, 0, '', '.') }}</p>
                                        </td>
                                    </tr>
                                    @if ($totalPph > 0)
                                        <tr class="fw-medium py-0" style="font-size: 13px">
                                            <td colspan="2" class="text-end py-0"
                                                style="padding-right: 10px !important;">
                                                <p class="m-0">PPH</p>
                                            </td>
                                            <td class="pr-4 py-0" style="padding-left: 0 !important;">
                                                <p class="m-0 text-end">
                                                    {{ $totalPph == '0' ? '0' : 'RP ' . number_format($totalPph, 0, '', '.') }}
                                                </p>
                                            </td>
                                        </tr>
                                    @endif
                                    @php
                                        $totalwithpph = $payments[1]?->amount - $totalPph;
                                    @endphp
                                    @if ($quote->tax != 0)
                                        <tr class="fw-medium" style="font-size: 13px">
                                            <td colspan="2" id="price" class="text-end pl-4 py-0"
                                                style="padding-right: 10px !important;">
                                                <p class="m-0">
                                                    DPP Atas PPN
                                                </p>
                                            </td>
                                            <td id="price" class="pr-4 py-0" style="padding-left: 0 !important;">
                                                @php
                                                    $dpp = ($amount2 * 11) / 12;
                                                @endphp
                                                <p class="text-end m-0">RP
                                                    {{ number_format($dpp, 0, '', '.') }}</p>
                                            </td>
                                        </tr>
                                        <tr class="fw-medium py-0" style="font-size: 13px">
                                            <td colspan="2" class="text-end py-0"
                                                style="padding-right: 10px !important;">
                                                <p class="m-0">VAT {{ $quote->tax == '11' ? '12%' : '' }}</p>
                                            </td>
                                            <td class="pr-4 py-0" style="padding-left: 0 !important;">
                                                <p class="m-0 text-end">
                                                    {{ $vat == '0' ? '0' : 'RP ' . number_format($vat, 0, '', '.') }}</p>
                                            </td>
                                        </tr>
                                        @if ($quote->shipping != 0)
                                            <tr class="fw-medium" style="font-size: 13px">
                                                <td colspan="2" class="text-end py-0"
                                                    style="padding-right: 10px !important;">
                                                    <p class="m-0">Shipping Cost</p>
                                                </td>
                                                <td class="pr-4 py-0" style="padding-left: 0 !important;">
                                                    <p class="m-0 text-end">RP
                                                        {{ number_format($quote->shipping, 0, '', '.') }}
                                                    </p>
                                                </td>
                                            </tr>
                                        @endif
                                        <tr class="fw-medium py-0" style="font-size: 13px">
                                            <td colspan="2" class="text-end py-0"
                                                style="background-color: {{ $bgColor }}; padding-left:20px; padding-right:10px;">
                                                <p class="m-0">Total Include VAT</p>
                                            </td>
                                            <td class="pr-4 py-0"
                                                style="background-color: {{ $bgColor }}; padding-right:20px;">
                                                <p class="m-0 text-end fw-bold">
                                                    RP {{ number_format($totalwithpph, 0, '', '.') }}
                                                </p>
                                            </td>
                                        </tr>
                                    @else
                                        @if ($quote->shipping != 0)
                                            <tr class="fw-medium" style="font-size: 13px">
                                                <td colspan="2" class="text-end py-0"
                                                    style="padding-right: 10px !important;">
                                                    <p class="m-0">Shipping Cost</p>
                                                </td>
                                                <td class="pr-4 py-0" style="padding-left: 0 !important;">
                                                    <p class="m-0 text-end">RP
                                                        {{ number_format($quote->shipping, 0, '', '.') }}
                                                    </p>
                                                </td>
                                            </tr>
                                        @endif
                                        <tr class="fw-medium py-0" style="font-size: 13px">
                                            <td colspan="2" class="text-end py-0"
                                                style="background-color: {{ $bgColor }}; padding-left:20px; padding-right:10px;">
                                                <p class="m-0">Total</p>
                                            </td>
                                            <td class="pr-4 py-0"
                                                style="background-color: {{ $bgColor }}; padding-right:20px;">
                                                <p class="m-0 text-end fw-bold">
                                                    Rp {{ number_format($totalwithpph, 0, '', '.') }}
                                                </p>
                                            </td>
                                        </tr>
                                    @endif --}}

                                @endif
                            </tbody>
                        </table>
                    </div>
                @endif

                @if ($invoice->type == 'CT')
                    <p class="fw-medium mt-2 p-2" style="background-color: rgb(248, 248, 248); width:70%;"> Say
                        amount: #
                        {{ $fullPrice }} Rupiah</p>
                @elseif ($invoice->type == 'DP')
                    <p class="fw-medium mt-2 p-2" style="background-color: rgb(248, 248, 248); width:70%;"> Say
                        amount: #
                        {{ $priceDp }} Rupiah</p>
                @elseif ($invoice->type == 'BP')
                    <p class="fw-medium mt-2 p-2" style="background-color: rgb(248, 248, 248); width:70%;"> Say
                        amount: #
                        {{ $priceBp }} Rupiah</p>
                @endif
                <div class="row mt-5">
                    <div class="col-6 m-4">
                        <h5 class="my-4">Payment by Transfer or Giro shall be made in Full amount to :</h5>
                        <div class="row">
                            <div class="col-3 fw-medium">
                                <p class="mb-1">Payable to</p>
                                <p class="mb-1">Acc Name </p>
                                <p class="mb-1">Acc No. </p>
                                <p class="mb-1">Swift Code </p>
                            </div>
                            @if ($quote->pic->client->info == 'Reftech' && $invoice->quote->tax == 0)
                                <div class="col">
                                    <p class="mb-1">: Bank BCA (IDR)</p>
                                    <p class="mb-1">: ARIEP RACHMAN</p>
                                    <p class="mb-1">: 166 - 2242 - 271</p>
                                    <p class="mb-1">: -</p>
                                </div>
                            @elseif ($quote->pic->client->info == 'Reftech' && $invoice->quote->tax > 0)
                                <div class="col">
                                    <p class="mb-1">: Bank BCA (IDR)</p>
                                    <p class="mb-1">: PT. REFTECH JAYA OPTIMA</p>
                                    <p class="mb-1">: 008 - 6289 - 789</p>
                                    <p class="mb-1">: CENAIDJA</p>
                                </div>
                            @elseif ($quote->pic->client->info == 'Kojisha' && $invoice->quote->tax == 0)
                                <div class="col">
                                    <p class="mb-1">: Bank BCA (IDR)</p>
                                    <p class="mb-1">: REGITA DWI MELINDA</p>
                                    <p class="mb-1">: 1560239137</p>
                                    <p class="mb-1">: - </p>
                                </div>
                            @elseif ($quote->pic->client->info == 'Kojisha' && $invoice->quote->tax > 0)
                                <div class="col">
                                    <p class="mb-1">: Bank BCA (IDR)</p>
                                    <p class="mb-1">: KOJISHA INNOTIV INDONESIA PT</p>
                                    <p class="mb-1">: 5223876543</p>
                                    <p class="mb-1">: - </p>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="col"></div>
                    @if ($quote->pic->client->info == 'Reftech')
                        <div class="col-4 my-5 text-center">
                            <p>Bandung,
                                {{ Carbon\Carbon::parse($invoice->date)->locale('ID')->translatedFormat('d F Y') }}
                            </p>
                            @if ($quote->tax != '0')
                                <p class="fs-normal fw-bolder">PT. Reftech Jaya Optima</p>
                            @endif
                            @if (isset($invoice->sign))
                                <img src="{{ url('') . '/' . $invoice->sign }}" alt="" srcset=""
                                    height="77">
                            @else
                                <div style="padding: 40px 0;"></div>
                            @endif
                            {{-- <div class="pb-5"></div> --}}
                            <p class="pt-3 fw-bolder">Ariep Rachman</p>
                            <p>Director</p>
                        </div>
                    @else
                        <div class="col-4 my-5 text-center">
                            <p>Bekasi, {{ Carbon\Carbon::parse($invoice->date)->format('d F Y') }}</p>
                            @if ($quote->tax != '0')
                                <p class="fs-normal fw-bolder">PT. Kojisha Innotiv Indonesia </p>
                            @endif
                            @if (isset($invoice->sign))
                                <img src="{{ url('') . '/' . $invoice->sign }}" alt="" srcset=""
                                    height="77">
                            @else
                                <div style="padding: 40px 0;"></div>
                            @endif
                            {{-- <div class="pb-5"></div> --}}
                            <p class="pt-3 fw-bolder">Dedeh Sulastri</p>
                            <p>Director</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        {{-- End: Invoice --}}
        {{-- Button Invoice --}}
        @if (Auth::user()->role != 'Logistic')
            <div class="col-xl-3 col-md-4 col-12 invoice-actions">

                {{-- 1. Primary Actions --}}
                <div class="card mb-3">
                    <div class="card-body d-grid gap-2">
                        @if (Auth::user()->role == 'Admin' || Auth::user()->role == 'Accounting')
                            <div class="btn-group w-100">
                                <button type="button" class="btn btn-primary w-100 waves-effect dropdown-toggle"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    Download
                                </button>
                                <ul class="dropdown-menu w-100">
                                    <li>
                                        <a class="dropdown-item" target="_blank"
                                            href="{{ route('print.invoice', $invoice->id) }}">
                                            <i class="mdi mdi-file-document-outline me-1"></i> Invoice
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item"
                                            href="{{ route('invoice.label_detail', $invoice->id) }}">
                                            <i class="mdi mdi-printer-outline me-1"></i> Sampul
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        @else
                            <a class="btn btn-primary w-100 waves-effect" target="_blank"
                                href="{{ route('print.invoice', $invoice->id) }}">Download</a>
                        @endif
                        <button class="btn btn-outline-secondary w-100 waves-effect" id="backButton">Back</button>
                        @php
                            $viewQuotationUrl = match($quote->type) {
                                'Service'  => route('show-service.quotation', $quote->id),
                                'Overhaul' => route('show-overhaul.quotation', $quote->id),
                                default    => route('quotation.show', $quote->id),
                            };
                        @endphp
                        <a class="btn btn-outline-info w-100 waves-effect"
                            href="{{ $viewQuotationUrl }}">View Quotation</a>
                    </div>
                </div>

                {{-- 2. Invoice --}}
                <div class="card mb-3">
                    <div class="card-header py-2 px-3">
                        <small class="text-uppercase text-muted fw-semibold">Invoice</small>
                    </div>
                    <div class="card-body d-grid gap-2">
                        <button type="button" class="btn btn-outline-secondary w-100 waves-effect"
                            data-bs-toggle="modal" data-bs-target="#descView">Change Description</button>
                        <button type="button" class="btn btn-outline-secondary w-100 waves-effect"
                            data-bs-toggle="modal" data-bs-target="#changeDate">Change Date</button>
                        @if (Auth::user()->role == 'Admin')
                            <button type="button" class="btn btn-outline-secondary w-100 waves-effect"
                                data-bs-toggle="modal" data-bs-target="#editInvoiceModal">Edit No Invoice / Term</button>
                        @endif
                        @if (@$lastPayment->type == 'Tempo' && @$lastPayment->due_date == null)
                            <button type="button" class="btn btn-warning w-100 waves-effect"
                                data-bs-toggle="modal" data-bs-target="#dueDate">Isi Due Date</button>
                        @endif
                        <a href="#" class="btn btn-outline-danger w-100 waves-effect delete-invoice"
                            data-id="{{ $quote->id }}">Delete Invoice</a>
                    </div>
                </div>

                {{-- 3. Tax / PPH --}}
                <div class="card mb-3">
                    <div class="card-header py-2 px-3">
                        <small class="text-uppercase text-muted fw-semibold">Tax / PPH</small>
                    </div>
                    <div class="card-body d-grid gap-2">
                        @if ($totalPph23 > 0)
                            <a href="#"
                                class="btn btn-danger w-100 waves-effect {{ $quote->type == 'Sparepart' ? 'delete-pph' : 'delete-pph-service' }}"
                                data-id="{{ $invoice->id }}">Delete PPH 23</a>
                        @else
                            <button type="button" class="btn btn-outline-info w-100 waves-effect"
                                data-bs-toggle="modal" data-bs-target="#addPph">Input PPH 23</button>
                        @endif
                        @if ($invoice->pph > 0)
                            <a href="#" class="btn btn-danger w-100 waves-effect delete-pph-manual"
                                data-id="{{ $invoice->id }}">Delete PPH Manual</a>
                        @else
                            <button type="button" class="btn btn-outline-secondary w-100 waves-effect"
                                data-bs-toggle="modal" data-bs-target="#addPphManual">Input PPH Manual</button>
                        @endif
                    </div>
                </div>

                {{-- 4. Hand Sign --}}
                <div class="card mb-3">
                    <div class="card-header py-2 px-3">
                        <small class="text-uppercase text-muted fw-semibold">Hand Sign</small>
                    </div>
                    <div class="card-body">
                        @if (isset($invoice->sign))
                            <a href="#" class="btn btn-danger w-100 waves-effect delete-hand-sign"
                                data-id="{{ $invoice->id }}">Delete Hand Sign</a>
                        @else
                            <a href="#" class="btn btn-outline-secondary w-100 waves-effect input-hand-sign"
                                data-id="{{ $invoice->id }}">Input Hand Sign</a>
                        @endif
                    </div>
                </div>

                {{-- 5. Payment --}}
                <div class="card mb-3">
                    <div class="card-header py-2 px-3">
                        <small class="text-uppercase text-muted fw-semibold">Payment</small>
                    </div>
                    <div class="card-body d-grid gap-2">
                        <div class="d-flex justify-content-between align-items-center px-1">
                            <span class="text-muted small">Remaining</span>
                            <span class="fw-semibold">Rp {{ number_format($remaining, 0, '.', ',') }}</span>
                        </div>
                        <button type="button" class="btn btn-outline-secondary w-100 waves-effect waves-light"
                            data-bs-toggle="modal" data-bs-target="#detailPayment">Detail Payment</button>
                        @if (Auth::user()->role == 'Admin' || Auth::user()->role == 'Accounting')
                            @if ($invoice->status_p == 0)
                                <button type="button" class="btn btn-primary w-100 waves-effect waves-light"
                                    data-bs-toggle="modal" data-bs-target="#confirmPayment">Confirm Payment</button>
                            @else
                                <a href="#" class="btn btn-danger w-100 waves-effect undo-payment"
                                    data-id="{{ $invoice->id }}">Undo Confirm Payment</a>
                            @endif
                            <button type="button" class="btn btn-outline-secondary w-100 waves-effect"
                                data-bs-toggle="modal" data-bs-target="#addExpense">Input Expense</button>
                            @if ($expense->isNotEmpty())
                                <button type="button" class="btn btn-outline-secondary w-100 waves-effect"
                                    data-bs-toggle="modal" data-bs-target="#detailExpense">Detail Expense</button>
                            @endif
                        @endif
                    </div>
                </div>

                {{-- 6. Delivery Order (Admin / Accounting only) --}}
                @if (Auth::user()->role == 'Admin' || Auth::user()->role == 'Accounting')
                    <div class="card mb-3">
                        <div class="card-header py-2 px-3">
                            <small class="text-uppercase text-muted fw-semibold">Delivery Order</small>
                        </div>
                        <div class="card-body d-grid gap-2">
                            <div class="row g-2">
                                <div class="col-6">
                                    <button type="button" class="btn btn-outline-success w-100 btn-sm waves-effect"
                                        data-bs-toggle="modal" data-bs-target="#doTeknisi">DO Teknisi</button>
                                </div>
                                <div class="col-6">
                                    <button type="button" class="btn btn-outline-success w-100 btn-sm waves-effect"
                                        data-bs-toggle="modal" data-bs-target="#doEkspedisi">DO Ekspedisi</button>
                                </div>
                                <div class="col-6">
                                    <a href="{{ route('delivery.create_manual_teknisi', $invoice->id) }}"
                                        class="btn btn-outline-secondary w-100 btn-sm waves-effect">DO Manual Teknisi</a>
                                </div>
                                <div class="col-6">
                                    <a href="{{ route('delivery.create_manual_ekspedisi', $invoice->id) }}"
                                        class="btn btn-outline-secondary w-100 btn-sm waves-effect">DO Manual Ekspedisi</a>
                                </div>
                            </div>
                            @php $eks = 0; $tek = 0; @endphp
                            @if ($doTek->count() >= 1 || $doEks->count() >= 1)
                                <hr class="my-1">
                                <small class="text-muted d-block">Existing DO</small>
                                @foreach ($doTek as $teknisi)
                                    @php $tek++; @endphp
                                    <a class="btn btn-outline-success btn-sm w-100 waves-effect"
                                        href="{{ route('delivery.show', $teknisi->id) }}">DO Teknisi ({{ $tek }})</a>
                                @endforeach
                                @foreach ($doEks as $ekspedisi)
                                    @php $eks++; @endphp
                                    <a class="btn btn-outline-success btn-sm w-100 waves-effect"
                                        href="{{ route('delivery.show', $ekspedisi->id) }}">DO Ekspedisi ({{ $eks }})</a>
                                @endforeach
                            @endif
                            @php $eksMan = 0; $tekMan = 0; @endphp
                            @if ($doTekMan->count() >= 1 || $doEksMan->count() >= 1)
                                <hr class="my-1">
                                <small class="text-muted d-block">Existing DO Manual</small>
                                @foreach ($doTekMan as $tekMannisi)
                                    @php $tekMan++; @endphp
                                    <a class="btn btn-outline-secondary btn-sm w-100 waves-effect"
                                        href="{{ route('delivery.show_manual', $tekMannisi->id) }}">DO Manual Teknisi ({{ $tekMan }})</a>
                                @endforeach
                                @foreach ($doEksMan as $eksManpedisi)
                                    @php $eksMan++; @endphp
                                    <a class="btn btn-outline-secondary btn-sm w-100 waves-effect"
                                        href="{{ route('delivery.show_manual', $eksManpedisi->id) }}">DO Manual Ekspedisi ({{ $eksMan }})</a>
                                @endforeach
                            @endif
                        </div>
                    </div>
                @endif

                {{-- Edit Invoice Modal --}}
                <div class="modal fade" id="editInvoiceModal" tabindex="-1" aria-labelledby="editInvoiceModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editInvoiceModalLabel">Edit No Invoice & Term of Payment</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form method="POST" action="{{ route('invoice.update', $invoice->id) }}">
                                @csrf
                                @method('PUT')
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label for="invoiceNumber" class="form-label">No Invoice</label>
                                        <input type="text" class="form-control" id="invoiceNumber" name="invoice" value="{{ old('invoice', $invoice->no_invoice) }}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="termPayment" class="form-label">Term of Payment</label>
                                        <textarea class="form-control" id="termPayment" name="payment" rows="4" required>{{ old('payment', $invoice->term) }}</textarea>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                {{-- End: Button Invoice --}}
            </div>
        @else
            <div class="col-xl-3 col-md-4 col-12 invoice-actions">
                <div class="card">
                    <div class="card-body">
                        @if (@$pOut)
                            <a class="btn btn-primary d-grid w-100 mb-3 waves-effect"
                                href="{{ route('product-out.show', $pOut->id) }}">
                                Go To Product Out
                            </a>
                        @else
                            <a class="btn btn-primary d-grid w-100 mb-3 waves-effect"
                                href="{{ route('product-out.invoice', $invoice->id) }}">
                                Create Product Out
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        @endif
        @include('components.modal.quotation.detail-payment')
        @include('components.modal.accounting.sign')
        @include('components.modal.invoice.date')
        @include('components.modal.invoice.desc')
        @include('components.modal.invoice.pph')
        @include('components.modal.invoice.pph-manual')
        @include('components.modal.invoice.confirm')
        @include('components.modal.invoice.expense')
        @include('components.modal.invoice.due-date')
        @include('components.modal.invoice.detail-expense')
        @include('components.modal.accounting.delivery.create-teknisi')
        @include('components.modal.accounting.delivery.create-ekspedisi')
    @endsection
    @push('after-style')
        <!-- Page CSS -->
        <link rel="stylesheet" href="{{ asset('assets') }}/vendor/css/pages/app-invoice.css" />
        <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.css" />
    @endpush
    @push('after-script')
        <script src="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.js"></script>
    @endpush
    @push('page-script')
        <script src="{{ asset('assets') }}/js/extended-ui-sweetalert2.js"></script>
    @endpush
    @push('script')
        <script>
            function formatNumber(n) {
                return n.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ".")
            }

            $(".invoice-item-price-label").on('keyup', function() {
                var input = $(this)
                var input_val = input.val();

                // original length
                var original_len = input_val.length;

                // add commas to number
                // remove all non-digits
                input_val = formatNumber(input_val);
                input_val = input_val;

                // send updated string to input
                input.val(input_val);
                var nomorInt = parseFloat(input_val.replace(/[.,]/g, ''));
                console.log(nomorInt);
                $(`#pricy`).val(nomorInt);
            });

            $(".invoice-item-pph-manual-label").on('keyup', function() {
                var input = $(this)
                var input_val = input.val();

                // original length
                var original_len = input_val.length;

                // add commas to number
                // remove all non-digits
                input_val = formatNumber(input_val);
                input_val = input_val;

                // send updated string to input
                input.val(input_val);
                var nomorInt = parseFloat(input_val.replace(/[.,]/g, ''));
                console.log(nomorInt);
                $(`#pphManual`).val(nomorInt);
            });

            // $(document).on('click', '.delete-contract', function() {
            //     var id = $(this).data('id');
            //     var quoteId = $(this).data('quote');
            //     Swal.fire({
            //         title: "Are you sure?",
            //         text: "You won't be able to revert this!",
            //         icon: "warning",
            //         showCancelButton: true,
            //         confirmButtonText: "Yes, delete it!",
            //         customClass: {
            //             confirmButton: "btn btn-primary me-3 waves-effect waves-light",
            //             cancelButton: "btn btn-label-secondary waves-effect",
            //         },
            //         buttonsStyling: false,
            //     }).then(function(result) {
            //         if (result.value) {
            //             $.ajax({
            //                 'url': '{{ url('contract') }}/' + id,
            //                 'type': 'POST',
            //                 'data': {
            //                     '_method': 'DELETE',
            //                     '_token': '{{ csrf_token() }}'
            //                 },
            //                 success: function(response) {
            //                     if (response == 1) {
            //                         Swal.fire({
            //                             icon: "success",
            //                             title: "Deleted!",
            //                             text: "Your file has been deleted.",
            //                             customClass: {
            //                                 confirmButton: "btn btn-success waves-effect",
            //                             },
            //                         })
            //                         window.setTimeout(function() {
            //                             window.location.href = '/quotation/' + quoteId;
            //                         }, 2000);
            //                     } else {
            //                         Swal.fire({
            //                             icon: 'error',
            //                             title: 'Oops...',
            //                             text: 'Data Failed to Delete!'
            //                         });
            //                     }
            //                 }
            //             });
            //         } else if (result.dismiss === Swal.DismissReason.cancel) {
            //             Swal.fire({
            //                 title: "Cancelled",
            //                 text: "Your imaginary file is safe :)",
            //                 icon: "error",
            //                 customClass: {
            //                     confirmButton: "btn btn-success waves-effect",
            //                 },
            //             });
            //         }
            //     });
            // });
            $('#backButton').click(function() {
                window.history.back();
            });
            $(() => {

                const dateInput = document.getElementById('dateInput');
                const resetCheckbox = document.getElementById('checkDate');

                // Saat checkbox di-check
                resetCheckbox.addEventListener('change', function() {
                    if (this.checked) {
                        dateInput.value = ''; // Hapus nilai date
                    }
                });

                // Saat input tanggal diisi
                dateInput.addEventListener('input', function() {
                    if (this.value) {
                        resetCheckbox.checked = false; // Uncheck checkbox
                    }
                });
                $('#formFileMultiple').on('change', function() {
                    var files = this.files;
                    var dynamicInputsContainer = $('#dynamicInputsContainer');
                    dynamicInputsContainer.empty();

                    // Hanya mengambil satu file (file pertama)
                    var file = files[0];
                    console.log(file);
                    const previewContainer = document.getElementById('image-preview');
                    previewContainer.innerHTML = '';

                    const reader = new FileReader();

                    reader.onload = function(e) {
                        const imageContainer = document.createElement('div');
                        const imageElement = document.createElement('img');
                        imageContainer.className = 'image-container'; // Tambahkan kelas sesuai kebutuhan

                        // Set maksimum lebar dan tinggi untuk gambar
                        imageElement.style.maxWidth =
                            '800px'; // Ganti dengan nilai maksimum lebar yang Anda inginkan
                        imageElement.style.maxHeight =
                            '500px'; // Ganti dengan nilai maksimum tinggi yang Anda inginkan

                        imageElement.src = e.target.result;

                        imageContainer.appendChild(imageElement);
                        previewContainer.appendChild(imageContainer);
                    };

                    reader.readAsDataURL(file);
                });
            });
            $(document).on('click', '.delete-hand-sign', function() {
                var id = $(this).data('id');
                Swal.fire({
                    title: "Are you sure?",
                    text: "You won't be able to revert this!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Yes, delete it!",
                    customClass: {
                        confirmButton: "btn btn-primary me-3 waves-effect waves-light",
                        cancelButton: "btn btn-label-secondary waves-effect",
                    },
                    buttonsStyling: false,
                }).then(function(result) {
                    if (result.value) {
                        $.ajax({
                            'url': '{{ url('invoice') }}/del-sign/' + id,
                            'type': 'POST',
                            'data': {
                                '_method': 'DELETE',
                                '_token': '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response == 1) {
                                    Swal.fire({
                                        icon: "success",
                                        title: "Deleted!",
                                        text: "Your file has been deleted.",
                                        customClass: {
                                            confirmButton: "btn btn-success waves-effect",
                                        },
                                    })
                                    window.setTimeout(function() {
                                        window.location.href = '/invoice/' + id;
                                    }, 2000);
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Oops...',
                                        text: 'Data Failed to Delete!'
                                    });
                                }
                            }
                        });
                    } else if (result.dismiss === Swal.DismissReason.cancel) {
                        Swal.fire({
                            title: "Cancelled",
                            text: "Your imaginary file is safe :)",
                            icon: "error",
                            customClass: {
                                confirmButton: "btn btn-success waves-effect",
                            },
                        });
                    }
                });
            });
            $(document).on('click', '.input-hand-sign', function() {
                var id = $(this).data('id');
                Swal.fire({
                    title: "Are you sure?",
                    text: "You won't be able to revert this!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Yes, input it!",
                    customClass: {
                        confirmButton: "btn btn-primary me-3 waves-effect waves-light",
                        cancelButton: "btn btn-label-secondary waves-effect",
                    },
                    buttonsStyling: false,
                }).then(function(result) {
                    if (result.value) {
                        $.ajax({
                            'url': '{{ url('invoice') }}/sign/' + id,
                            'type': 'POST',
                            'data': {
                                '_method': 'POST',
                                '_token': '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response == 1) {
                                    Swal.fire({
                                        icon: "success",
                                        title: "Inputed!",
                                        text: "Your image has been Inputed.",
                                        customClass: {
                                            confirmButton: "btn btn-success waves-effect",
                                        },
                                    })
                                    window.setTimeout(function() {
                                        window.location.href = '/invoice/' + id;
                                    }, 2000);
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Oops...',
                                        text: 'Data Failed to Input!'
                                    });
                                }
                            }
                        });
                    } else if (result.dismiss === Swal.DismissReason.cancel) {
                        Swal.fire({
                            title: "Cancelled",
                            text: "Yout Image cancelled to input :)",
                            icon: "error",
                            customClass: {
                                confirmButton: "btn btn-success waves-effect",
                            },
                        });
                    }
                });
            });
            $(document).on('click', '.delete-invoice', function() {
                var id = $(this).data('id');
                Swal.fire({
                    title: "Are you sure?",
                    text: "You won't be able to revert this!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Yes, delete it!",
                    customClass: {
                        confirmButton: "btn btn-primary me-3 waves-effect waves-light",
                        cancelButton: "btn btn-label-secondary waves-effect",
                    },
                    buttonsStyling: false,
                }).then(function(result) {
                    if (result.value) {
                        $.ajax({
                            'url': '{{ url('invoice') }}/' + id,
                            'type': 'POST',
                            'data': {
                                '_method': 'DELETE',
                                '_token': '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response == 1) {
                                    Swal.fire({
                                        icon: "success",
                                        title: "Deleted!",
                                        text: "Your file has been deleted.",
                                        customClass: {
                                            confirmButton: "btn btn-success waves-effect",
                                        },
                                    })
                                    window.setTimeout(function() {
                                        window.location.href = '/quotation';
                                    }, 2000);
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Oops...',
                                        text: 'Data Failed to Delete!'
                                    });
                                }
                            }
                        });
                    } else if (result.dismiss === Swal.DismissReason.cancel) {
                        Swal.fire({
                            title: "Cancelled",
                            text: "Your imaginary file is safe :)",
                            icon: "error",
                            customClass: {
                                confirmButton: "btn btn-success waves-effect",
                            },
                        });
                    }
                });
            });
            $(document).on('click', '.delete-pph', function() {
                var id = $(this).data('id');
                Swal.fire({
                    title: "Are you sure?",
                    text: "You won't be able to revert this!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Yes, delete it!",
                    customClass: {
                        confirmButton: "btn btn-primary me-3 waves-effect waves-light",
                        cancelButton: "btn btn-label-secondary waves-effect",
                    },
                    buttonsStyling: false,
                }).then(function(result) {
                    if (result.value) {
                        $.ajax({
                            'url': '{{ url('invoice') }}/del-pph/' + id,
                            'type': 'POST',
                            'data': {
                                '_method': 'DELETE',
                                '_token': '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response == 1) {
                                    Swal.fire({
                                        icon: "success",
                                        title: "Deleted!",
                                        text: "Your file has been deleted.",
                                        customClass: {
                                            confirmButton: "btn btn-success waves-effect",
                                        },
                                    })
                                    window.setTimeout(function() {
                                        window.location.href = '/invoice/' + id;
                                    }, 2000);
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Oops...',
                                        text: 'Data Failed to Delete!'
                                    });
                                }
                            }
                        });
                    } else if (result.dismiss === Swal.DismissReason.cancel) {
                        Swal.fire({
                            title: "Cancelled",
                            text: "Your imaginary file is safe :)",
                            icon: "error",
                            customClass: {
                                confirmButton: "btn btn-success waves-effect",
                            },
                        });
                    }
                });
            });
            $(document).on('click', '.delete-pph-service', function() {
                var id = $(this).data('id');
                Swal.fire({
                    title: "Are you sure?",
                    text: "You won't be able to revert this!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Yes, delete it!",
                    customClass: {
                        confirmButton: "btn btn-primary me-3 waves-effect waves-light",
                        cancelButton: "btn btn-label-secondary waves-effect",
                    },
                    buttonsStyling: false,
                }).then(function(result) {
                    if (result.value) {
                        $.ajax({
                            'url': '{{ url('invoice') }}/del-pph-service/' + id,
                            'type': 'POST',
                            'data': {
                                '_method': 'DELETE',
                                '_token': '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response == 1) {
                                    Swal.fire({
                                        icon: "success",
                                        title: "Deleted!",
                                        text: "Your file has been deleted.",
                                        customClass: {
                                            confirmButton: "btn btn-success waves-effect",
                                        },
                                    })
                                    window.setTimeout(function() {
                                        window.location.href = '/invoice/' + id;
                                    }, 2000);
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Oops...',
                                        text: 'Data Failed to Delete!'
                                    });
                                }
                            }
                        });
                    } else if (result.dismiss === Swal.DismissReason.cancel) {
                        Swal.fire({
                            title: "Cancelled",
                            text: "Your imaginary file is safe :)",
                            icon: "error",
                            customClass: {
                                confirmButton: "btn btn-success waves-effect",
                            },
                        });
                    }
                });
            });
            $(document).on('click', '.delete-pph-manual', function() {
                var id = $(this).data('id');
                Swal.fire({
                    title: "Are you sure?",
                    text: "You won't be able to revert this!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Yes, delete it!",
                    customClass: {
                        confirmButton: "btn btn-primary me-3 waves-effect waves-light",
                        cancelButton: "btn btn-label-secondary waves-effect",
                    },
                    buttonsStyling: false,
                }).then(function(result) {
                    if (result.value) {
                        $.ajax({
                            'url': '{{ url('invoice') }}/delete_pph_manual/' + id,
                            'type': 'POST',
                            'data': {
                                '_method': 'PATCH',
                                '_token': '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response == 1) {
                                    Swal.fire({
                                        icon: "success",
                                        title: "Deleted!",
                                        text: "Your file has been deleted.",
                                        customClass: {
                                            confirmButton: "btn btn-success waves-effect",
                                        },
                                    })
                                    window.setTimeout(function() {
                                        window.location.href = '/invoice/' + id;
                                    }, 2000);
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Oops...',
                                        text: 'Data Failed to Delete!'
                                    });
                                }
                            }
                        });
                    } else if (result.dismiss === Swal.DismissReason.cancel) {
                        Swal.fire({
                            title: "Cancelled",
                            text: "Your imaginary file is safe :)",
                            icon: "error",
                            customClass: {
                                confirmButton: "btn btn-success waves-effect",
                            },
                        });
                    }
                });
            });
            $(document).on('click', '.delete-expense', function() {
                var id = $(this).data('id');
                var invoice = $(this).data('invoice');
                Swal.fire({
                    title: "Are you sure?",
                    text: "You won't be able to revert this!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Yes, delete it!",
                    customClass: {
                        confirmButton: "btn btn-primary me-3 waves-effect waves-light",
                        cancelButton: "btn btn-label-secondary waves-effect",
                    },
                    buttonsStyling: false,
                }).then(function(result) {
                    if (result.value) {
                        $.ajax({
                            'url': '{{ url('invoice') }}/del-expense/' + id,
                            'type': 'POST',
                            'data': {
                                '_method': 'DELETE',
                                '_token': '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response == 1) {
                                    Swal.fire({
                                        icon: "success",
                                        title: "Deleted!",
                                        text: "Your file has been deleted.",
                                        customClass: {
                                            confirmButton: "btn btn-success waves-effect",
                                        },
                                    })
                                    window.setTimeout(function() {
                                        window.location.href = '/invoice/' + invoice;
                                    }, 2000);
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Oops...',
                                        text: 'Data Failed to Delete!'
                                    });
                                }
                            }
                        });
                    } else if (result.dismiss === Swal.DismissReason.cancel) {
                        Swal.fire({
                            title: "Cancelled",
                            text: "Your imaginary file is safe :)",
                            icon: "error",
                            customClass: {
                                confirmButton: "btn btn-success waves-effect",
                            },
                        });
                    }
                });
            });
            $(document).on('click', '.confirm-payments', function() {
                var id = $(this).data('id');
                var quote = $(this).data('quote');
                var invoice = $(this).data('invoice');
                Swal.fire({
                    title: "Are you sure Confirm this payment?",
                    text: "You won't be able to revert this!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Yes, Confirm it!",
                    customClass: {
                        confirmButton: "btn btn-primary me-3 waves-effect waves-light",
                        cancelButton: "btn btn-label-secondary waves-effect",
                    },
                    buttonsStyling: false,
                }).then(function(result) {
                    if (result.value) {
                        $.ajax({
                            'url': '{{ url('quotation') }}/' + id + '/confirm_payment',
                            'type': 'POST',
                            'data': {
                                '_method': 'POST',
                                '_token': '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response == 1) {
                                    Swal.fire({
                                        icon: "success",
                                        title: "Confirmed!",
                                        text: "Your file has been confirmed.",
                                        customClass: {
                                            confirmButton: "btn btn-success waves-effect",
                                        },
                                    })
                                    window.setTimeout(function() {
                                        window.location.href = '/invoice/' + invoice;
                                    }, 2000);
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Oops...',
                                        text: 'Data Failed to confirm!'
                                    });
                                }
                            }
                        });
                    } else if (result.dismiss === Swal.DismissReason.cancel) {
                        Swal.fire({
                            title: "Cancelled",
                            text: "Your imaginary file is safe :)",
                            icon: "error",
                            customClass: {
                                confirmButton: "btn btn-success waves-effect",
                            },
                        });
                    }
                });
            });

            $(document).on('click', '.undo-payment', function() {
                var id = $(this).data('id');
                Swal.fire({
                    title: "Are you sure?",
                    text: "You won't be able to revert this!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Yes, Undo it!",
                    customClass: {
                        confirmButton: "btn btn-primary me-3 waves-effect waves-light",
                        cancelButton: "btn btn-label-secondary waves-effect",
                    },
                    buttonsStyling: false,
                }).then(function(result) {
                    if (result.value) {
                        $.ajax({
                            'url': '{{ url('invoice') }}/undo_confirm_payment/' + id,
                            'type': 'POST',
                            'data': {
                                '_token': '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response == 1) {
                                    Swal.fire({
                                        icon: "success",
                                        title: "Undone!",
                                        text: "Your file has been undone.",
                                        customClass: {
                                            confirmButton: "btn btn-success waves-effect",
                                        },
                                    })
                                    window.setTimeout(function() {
                                        window.location.href = '/invoice/' + id;
                                    }, 2000);
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Oops...',
                                        text: 'Data Failed to Undo!'
                                    });
                                }
                            }
                        });
                    } else if (result.dismiss === Swal.DismissReason.cancel) {
                        Swal.fire({
                            title: "Cancelled",
                            text: "Your imaginary file is safe :)",
                            icon: "error",
                            customClass: {
                                confirmButton: "btn btn-success waves-effect",
                            },
                        });
                    }
                });
            });
        </script>
    @endpush
