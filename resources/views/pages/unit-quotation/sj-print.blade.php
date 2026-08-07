@extends('layouts.sales.app')
@section('title', 'Surat Jalan ' . ($invoice->no_invoice ?? $unitQuote->no_quote))
@php
    $isEkspedisi = strtolower($delivery->type ?? '') === 'ekspedisi';
    $client = $unitQuote->client ?? null;
    $address = $client ? ($delivery->destination == '1' ? $client->address : $client->subAddress) : '-';
    $doNumber = $invoice->no_invoice ?? $unitQuote->no_quote;
@endphp
<div class="invoice-print p-4">
    @if ($isEkspedisi)
        <div class="container-fluid flex-grow-1 container-p-y">
            {{-- Header --}}
            <div class="d-flex justify-content-between flex-xl-row flex-md-column flex-sm-row flex-column mb-0">
                <div class="mb-xl-0 pb-1">
                    <div class="d-flex svg-illustration align-items-center gap-2 mb-3">
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
                        <p class="mb-0"><i class="mdi mdi-phone-outline me-1" style="font-size:11px;"></i>022 54417653 &nbsp;|&nbsp; <i class="mdi mdi-email-outline me-1" style="font-size:11px;"></i>accounting@reftech.id &nbsp;|&nbsp; <i class="mdi mdi-web me-1" style="font-size:11px;"></i>www.reftech.id</p>
                    </div>
                </div>
                <div class="text-end">
                    <h3 class="fw-bold mb-1" style="letter-spacing:2px; color:#696cff;">DELIVERY ORDER</h3>
                    <p class="mb-1 fw-semibold" style="font-size:14px;">#{{ $doNumber }}</p>
                    <p class="mb-0 text-muted" style="font-size:12px;">{{ \Carbon\Carbon::parse($delivery->date)->format('d F Y') }}</p>
                </div>
            </div>

            {{-- Accent Divider --}}
            <div style="height:3px; background:linear-gradient(90deg,#696cff 0%,#9c9eff 60%,#e0e0e0 100%); border-radius:2px; margin:12px 0 16px;"></div>

            <div style="display:flex; align-items:stretch; gap:12px; margin-bottom:20px; font-size:12px;">
                <div style="flex:1; display:flex; flex-direction:column; border:1px solid #dcdcdc; border-radius:6px; padding:10px 14px; background:#fafafa;">
                    <p class="mb-1 fw-bold text-uppercase" style="font-size:10px; letter-spacing:.5px; color:#555;">Deliver To</p>
                    <p class="mb-1 fw-bold" style="font-size:13.5px; color:#111;">{{ $client->company ?? '-' }}</p>
                    <p class="mb-0" style="font-size:11.5px; color:#222; line-height:1.4;">
                        <i class="mdi mdi-map-marker-outline me-1" style="font-size:11px; color:#444;"></i><span style="font-weight:500;">{{ $address }}</span>
                    </p>
                </div>
                <div style="min-width:240px; display:flex; flex-direction:column; border:1px solid #dcdcdc; border-radius:6px; padding:10px 14px; background:#fafafa;">
                    <p class="mb-1 fw-bold text-uppercase" style="font-size:10px; letter-spacing:.5px; color:#555;">Shipment Info</p>
                    <p class="mb-1 fw-semibold" style="font-size:12px; color:#222;">
                        <i class="mdi mdi-clipboard-text-outline me-1 text-primary"></i>PO / Quote No: <span class="fw-bold">{{ $unitQuote->po_number ?: $unitQuote->no_quote }}</span>
                    </p>
                    <p class="mb-0 fw-semibold" style="font-size:12px; color:#222;">
                        <i class="mdi mdi-truck-outline me-1 text-primary"></i>Jenis: <span class="fw-bold">{{ ucfirst($delivery->type) }}</span>
                    </p>
                </div>
            </div>

            <div class="mb-4">
                <table class="table table-bordered m-0" style="border: 1px solid rgb(60, 60, 60)">
                    <thead>
                        <tr style="background:#f0f2ff;">
                            <th class="text-center" style="width:6%; font-size:12px;">No.</th>
                            <th style="font-size:12px;">Description</th>
                            <th class="text-center" style="width:16%; font-size:12px;">Qty</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $itemNo = 1; @endphp
                        @foreach ($dDelivery as $item)
                            @if (($item->type ?? 'item') === 'header')
                                <tr style="background:#f0f0ff; border-top:1.5px solid #d0d0ff; border-bottom:1.5px solid #d0d0ff;">
                                    <td colspan="3" class="fw-bold text-uppercase py-2 px-3 text-primary" style="font-size:12px; letter-spacing:0.5px;">
                                        <i class="mdi mdi-bookmark-outline me-1"></i> {{ $item->desc }}
                                    </td>
                                </tr>
                            @else
                                <tr style="font-size: 13px">
                                    <td class="align-top text-center">{{ $itemNo++ }}</td>
                                    <td>
                                        <p class="mb-0 fw-medium" style="font-size: 12px">{{ $item->view == '0' ? $item->desc : '' }}</p>
                                    </td>
                                    <td class="align-top text-center" style="white-space:nowrap;">{{ (float) $item->qty }} {{ $item->info_qty }}</td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="row">
                <div class="col-4 mt-4 text-center">
                    <div style="height:56px;"></div>
                    <p class="fw-bold mx-3 mb-0" style="border-top:1px solid #999; padding-top:4px;">PT. Reftech Jaya Optima</p>
                    <p class="text-muted small mb-0">Shipper</p>
                </div>
                <div class="col-4"></div>
                <div class="col-4 mt-4 text-center">
                    <div style="height:56px;"></div>
                    <p class="fw-bold mx-3 mb-0" style="border-top:1px solid #999; padding-top:4px;">{{ $client->company ?? '-' }}</p>
                    <p class="text-muted small mb-0">Received</p>
                </div>
            </div>
        </div>
    @else
        <div class="container-fluid flex-grow-1 container-p-y">
            <div class="table-responsive mb-5">
                <table class="table table-bordered m-0" style="border: 1px solid rgb(60, 60, 60)">
                    <tbody>
                        <tr>
                            <td colspan="3" class="py-1">
                                <div class="row">
                                    <div class="col-8">
                                        <h5 class="fw-bold mb-0">Delivery Order</h5>
                                    </div>
                                    <div class="col-4">
                                        <p class="mb-0"><span class="fw-bold">D.O. No :</span>
                                            {{ $doNumber }}</p>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3" class="py-0">
                                <div class="row">
                                    <div class="col-6">
                                        <div class="d-flex justify-content-between flex-xl-row flex-md-column flex-sm-row flex-column">
                                            <div class="mb-xl-0 pb-1">
                                                <div class="d-flex svg-illustration align-items-center gap-2">
                                                    <span class="app-brand-logo demo">
                                                        <span style="color: var(--bs-primary)">
                                                            <img class="text-md"
                                                                src="{{ asset('/asset') }}/logo/Reftech-Log.png"
                                                                alt="" srcset="" width="60%">
                                                        </span>
                                                    </span>
                                                </div>
                                                <p class="mb-1 mx-2 fw-bolder">PT Reftech Jaya Optima</p>
                                                <div class="mx-2" style="font-size: 10px">
                                                    <p class="mb-1">Taman Kopo Indah V, Ruko Sommerville No. 31</p>
                                                    <p class="mb-1">Bandung – Jawa Barat 40218</p>
                                                    <p class="mb-1">
                                                        <i class="mdi mdi-phone-outline scaleX-n1-rtl me-1 mdi-14px"></i>022 54417653
                                                        {{ '   ' }}<i class="mdi mdi-email-outline scaleX-n1-rtl me-1 mdi-14px"></i>accounting@reftech.id
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="row mt-3" style="font-size: 13px">
                                            <div class="col-4 text-end">
                                                <p class="mb-1">Date</p>
                                                <p class="mb-1">Order No</p>
                                                <p class="mb-1">Customer</p>
                                                <p class="mb-1">Delivery To</p>
                                            </div>
                                            <div class="col-8">
                                                <p class="mb-1">: {{ \Carbon\Carbon::parse($delivery->date)->format('d-m-Y') }}</p>
                                                <p class="mb-1">: {{ $unitQuote->po_number ?: $unitQuote->no_quote }}</p>
                                                <p class="mb-1">: {{ $client->company ?? '-' }}</p>
                                                <p class="mb-1">: {{ $address }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th class="text-center">No</th>
                            <th class="text-center">Qty</th>
                            <th class="text-center" style="width: 80%">Description</th>
                        </tr>
                        @php $itemNo = 1; @endphp
                        @foreach ($dDelivery as $item)
                            @if (($item->type ?? 'item') === 'header')
                                <tr style="background:#f0f0ff;">
                                    <td colspan="3" class="fw-bold text-uppercase py-1" style="font-size:12px;">{{ $item->desc }}</td>
                                </tr>
                            @else
                                <tr style="font-size: 13px;">
                                    <td class="align-top py-1">{{ $itemNo++ }}</td>
                                    <td class="align-top py-1">{{ $item->qty }} {{ $item->info_qty }}</td>
                                    <td class="align-top py-1">{{ $item->view == '0' ? $item->desc : '' }}</td>
                                </tr>
                            @endif
                        @endforeach
                        <tr>
                            <td colspan="3">
                                <div class="row mb-3">
                                    <div class="col-4 mt-5 text-center">
                                        <div class="pb-5"></div>
                                        <p class="fw-bold mx-3 mb-0" style="border-top: 1px solid black ">Shipper</p>
                                    </div>
                                    <div class="col-4"></div>
                                    <div class="col-4 mt-5 text-center">
                                        <div class="pb-5"></div>
                                        <p class="fw-bold mx-3 mb-0" style="border-top: 1px solid black ">Recieved</p>
                                    </div>
                                </div>
                                <p class="mb-0">Distribusi : Putih dan Pink → Pelanggan, <span class="fw-bold">Kuning → Accounting PT. Reftech</span></p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
@push('after-style')
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/css/pages/app-invoice-print-do.css" />
@endpush
@push('after-script')
    <script src="{{ asset('assets') }}/js/app-invoice-print.js"></script>
@endpush
