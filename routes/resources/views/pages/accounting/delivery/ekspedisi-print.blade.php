@extends('layouts.sales.app')
@section('title', $invoice->no_invoice)
<div class="invoice-print p-4">
    <div class="container-fluid flex-grow-1 container-p-y">

        @if ($invoice->flag == 'Reftech')
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
                    <p class="mb-1 fw-bolder">PT Reftech Jaya Optima</p>
                    <div style="font-size: 10px">
                        <p class="mb-1">Taman Kopo Indah V, Ruko Sommerville No. 31</p>
                        <p class="mb-1">Bandung – Jawa Barat 40218</p>
                        <p class="mb-1">
                            <i class="mdi mdi-phone-outline scaleX-n1-rtl me-1 mdi-14px"></i>022 54417653
                            {{ '   ' }}<i
                                class="mdi mdi-email-outline scaleX-n1-rtl me-1 mdi-14px"></i>admin@reftech.id
                        </p>
                        <p class="mb-1">
                        </p>
                    </div>
                </div>
                <div class="text-end">
                    <h1 class="fw-bold" style="color: blue;">Delivery Order</h1>
                    <div>
                        <span class="fw-bolder">#{{ $invoice->no_invoice }}</span>
                    </div>
                </div>
            </div>
        @else
            <div class="d-flex justify-content-between flex-xl-row flex-md-column flex-sm-row flex-column">
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
                <div class="text-end">
                    <h1 class="fw-bold" style="color: blue;">Delivery Order</h1>
                    <div>
                        <span class="fw-bolder">#{{ $invoice->no_invoice }}</span>
                    </div>
                </div>
            </div>
        @endif
        <div class="mb-4">
            @php
                if ($invoice->doTo == '1') {
                    $address = $quote->pic->client->address;
                } else {
                    $address = $quote->pic->client->subAddress;
                }
            @endphp
            <div class="table-responsive text-nowrap">
                <table class="table table-bordered" style="border: 1px solid black;">
                    <tr>
                        <td colspan="2" style="vertical-align: top; width: 50%;">
                            <div class="row">
                                <div class="col-4 fw-medium">
                                    <p class="mb-1" style="font-size: 15px">Customers </p>
                                    <p class="mb-1">Adress</p>
                                </div>
                                <div class="col-8">
                                    <p class="mb-1" style="font-size: 15px">: {{ $quote->pic->client->company }}</p>
                                    @if ($invoice->invoiceTo == '1')
                                        <pre
                                            style="font-size: 13px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; max-width: 100%; overflow-x: auto; white-space: pre-wrap;">: {{ $address }}</pre>
                                    @else
                                        <pre
                                            style="font-size: 13px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; max-width: 100%; overflow-x: auto; white-space: pre-wrap;">: {{ $address }}</pre>
                                    @endif
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td style=" background-color: #F9F9F9;" class="text-center">
                            <p class="fs-5 text-black fw-medium m-0">Purchase Order :</p>
                        </td>
                        <td style=" background-color: #F9F9F9;" class="text-center">
                            <p class="fs-5 text-black fw-medium m-0">Shipment Date :</p>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-center">
                            <pre
                                style="font-size: 13px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; max-width: 100%; overflow-x: auto; white-space: pre-wrap;">{{ $invoice->no_po }}</pre>
                        </td>
                        <td class="text-center">
                            <pre
                                style="font-size: 13px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; max-width: 100%; overflow-x: auto; white-space: pre-wrap;">{{ Carbon\Carbon::parse($invoice->date)->format('d-m-Y') }}</pre>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="mb-2">
            <table class="table table-bordered m-0" style="border: 1px solid rgb(60, 60, 60)">
                <thead class="table-light border-top">
                    <tr>
                        <th>No.</th>
                        <th>Part Number</th>
                        <th style="width: 40%">Description</th>
                        <th>Qty</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $no = 0;
                        $qty = 0;
                    @endphp
                    @foreach ($dQuote as $product)
                        @php
                            $no++;
                        @endphp
                        <tr style="font-size: 13px">
                            <td class="align-top">{{ $no }}</td>
                            <td class="text-nowrap align-top">
                                <p class="mb-0 fw-semibold" style="font-size: 12px">
                                    {{ $product->equivalent->pn }}
                                </p>

                            </td>
                            <td>
                                123123123
                                @if ($product->view == '1')
                                    <a href="{{ $product->equivalent->image }}" target="_blank"
                                        class=" underline-line">Description Click Here</a>
                                @else
                                    <pre class="mb-0"
                                        style="font-size: 10px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; max-width: 100%; overflow-x: auto; white-space: pre-wrap;">{{ $product->detail_product }}</pre>
                                @endif
                            </td>
                            <td class="align-top">{{ $product->qty }} {{ $product->info_qty }} </td>
                        </tr>
                        @php
                            $qty += $product->qty;
                        @endphp
                    @endforeach
                    <tr>
                        <td colspan="3"></td>
                        <td>{{ $qty }} {{ $product->info_qty }} </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="row">
            <div class="col-4 my-5 text-center">
                <div class="pb-5"></div>
                <p class="fw-bold mx-3 mb-0" style="border-top: 1px solid black ">PT. Reftech Jaya Optima</p>
                <p>Shipper</p>
            </div>
            <div class="col-4"></div>
            <div class="col-4 my-5 text-center">
                <div class="pb-5"></div>
                <p class="fw-bold mx-3 mb-0" style="border-top: 1px solid black ">
                    {{ $quote->pic->client->company }}</p>
                <p>Recieved</p>
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
