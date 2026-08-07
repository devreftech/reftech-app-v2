@extends('layouts.sales.app')
@section('title', 'Delivery Order')
@section('content')
    <div class="row invoice-preview">
        {{-- Invoice --}}
        <div class="col-xl-9 col-md-8 col-12 mb-md-0 mb-4">
            <div class="card invoice-preview-card">
                <div class="card-body">
                    @if ($invoice->flag == 'Reftech')
                        <div class="d-flex justify-content-between flex-xl-row flex-md-column flex-sm-row flex-column">
                            <div class="mb-xl-0 pb-1">
                                <div class="d-flex svg-illustration align-items-center gap-2 mb-4">
                                    <span class="app-brand-logo demo">
                                        <span style="color: var(--bs-primary)">
                                            <img class="text-md"
                                                src="{{ asset('/asset') }}/logo/Reftech-Log.png"
                                                alt="" srcset="" width="60%">
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
                        </div>
                    @endif
                </div>
                <div class="card-body mb-3">
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
                                            <p class="mb-1">Customers </p>
                                            <p class="mb-1">Adress</p>
                                        </div>
                                        <div class="col-8">
                                            <p class="mb-1">: {{ $quote->pic->client->company }}</p>
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
                                        style="font-size: 13px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; max-width: 100%; overflow-x: auto; white-space: pre-wrap;">{{ Carbon\Carbon::parse($invoice->dateDo)->format('d-m-Y') }}</pre>
                                </td>
                            </tr>
                        </table>
                    </div>
                    {{-- <div class="row">
                        <div class="col-6">
                            <h6 class="fw-semibold fs-4 mb-3">Invoice To:</h6>
                        </div>
                        <div class="col-6 mb-2">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-2 fw-medium">
                            <p class="mb-1">Bill To </p>
                            <p class="mb-1">Phone </p>
                            <p class="mb-1">Adress</p>
                        </div>
                        <div class="col-4">
                            <p class="mb-1">: {{ $quote->pic->client->company }}</p>
                            <p class="mb-1">: {{ $quote->pic->client->phone }}</p>
                            @if ($invoice->invoiceTo == '1')
                                <p class="mb-1">: {{ $quote->pic->client->address }}</p>
                            @else
                                <p class="mb-1">: {{ $quote->pic->client->subAddress }}</p>
                            @endif
                        </div>
                        <div class="col-6">
                            <div class="row">
                                <div class="col-6 fw-medium text-end">
                                    <p class="mb-1">Purchase Order :</p>
                                </div>
                                <div class="col-6 text-end">
                                    <p class="mb-1"> {{ $invoice->no_po }}
                                    </p>
                                </div>
                                <div class="col-12 text-center">
                                    <div class="termpay">
                                        <div class="title" style="border: 1px solid black; background-color: #F9F9F9;">
                                            <p class="fs-5 text-black fw-medium m-0">Term Of Payment:</p>
                                        </div>
                                        <div class="term" style="border: 1px solid black; border-top: 0;">
                                            <pre
                                                style="font-size: 13px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; max-width: 100%; overflow-x: auto; white-space: pre-wrap;">{{ $invoice->term }}</pre>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> --}}
                </div>
                <div class="table-responsive mb-5">
                    <table class="table table-bordered m-0" style="border: 1px solid rgb(60, 60, 60)">
                        <thead class="table-light">
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
                                        <pre class="mb-0"
                                            style="font-size: 10px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; max-width: 100%; overflow-x: auto; white-space: pre-wrap;">{{ $product->detail_product }}</pre>
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
        {{-- End: Invoice --}}
        {{-- Button Invocie --}}
        <div class="col-xl-3 col-md-4 col-12 invoice-actions">
            <div class="card mb-3">
                <div class="card-body">
                    <a class="btn btn-primary d-grid w-100 mb-3 waves-effect" target="_blank"
                        href="{{ route('invoice.print_ekspedisi', $invoice->id) }}">
                        Download
                    </a>
                    <a href="#" class="btn btn-outline-danger d-grid w-100 waves-effect delete-invoice mb-3"
                        data-id="{{ $quote->id }}">Delete</a>
                    <button class="btn btn-outline-secondary d-grid w-100 mb-3 waves-effect" id="backButton">
                        Back
                    </button>
                </div>
            </div>
            <div class="card mb-3">
                <div class="card-body">
                    <a type="button" data-bs-toggle="modal" data-bs-target="#changeDate"
                        class="d-grid w-100 waves-effect mb-3">
                        <button type="button" class="btn btn-secondary">
                            Change Date And Address
                        </button>
                    </a>
                </div>
            </div>
        </div>
        @include('components.modal.accounting.delivery.form-ekspedisi')
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
            $('#backButton').click(function() {
                window.history.back();
            });
        </script>
    @endpush
