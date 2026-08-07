@extends('layouts.sales.app')
@section('title', $suo->no_invoice_booking)
<div class="invoice-print p-4">
    <div class="container-fluid flex-grow-1 container-p-y">

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
                    <p class="mb-1"></p>
                </div>
            </div>
            <div class="text-end">
                <h1 class="fw-bold title-invoice" style="color: blue;">Delivery Order</h1>
                <div>
                    <span class="fw-bolder">#{{ $suo->no_invoice_booking }}</span>
                </div>
            </div>
        </div>

        <div class="mb-4">
            @php
                if ($client) {
                    $address = $delivery->destination == '1' ? $client->address : $client->subAddress;
                } else {
                    $address = $suo->address;
                }
            @endphp
            <div class="table-responsive text-nowrap">
                <table class="table table-bordered" style="border: 1px solid black;">
                    <tr>
                        <td colspan="2" style="vertical-align: top; width: 50%;">
                            <div class="row">
                                <div class="col-3 fw-medium">
                                    <p class="mb-1" style="font-size: 15px">Customers </p>
                                    <p class="mb-1">Adress</p>
                                </div>
                                <div class="col-9">
                                    <p class="mb-1" style="font-size: 15px">: {{ $suo->company }}</p>
                                    <pre style="font-size: 13px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; max-width: 100%; overflow-x: auto; white-space: pre-wrap;">: {{ $address }}</pre>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td style="background-color: #F9F9F9;" class="text-center">
                            <p class="fs-5 text-black fw-medium m-0">No. SUO :</p>
                        </td>
                        <td style="background-color: #F9F9F9;" class="text-center">
                            <p class="fs-5 text-black fw-medium m-0">Shipment Date :</p>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-center">
                            <pre style="font-size: 13px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; max-width: 100%; overflow-x: auto; white-space: pre-wrap;">{{ $suo->no_suo }}</pre>
                        </td>
                        <td class="text-center">
                            <pre style="font-size: 13px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; max-width: 100%; overflow-x: auto; white-space: pre-wrap;">{{ \Carbon\Carbon::parse($delivery->date)->format('d-m-Y') }}</pre>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="mb-2">
            <table class="table table-bordered m-0" style="border: 1px solid rgb(60, 60, 60)">
                <thead class="table-light">
                    <tr>
                        <th>No.</th>
                        <th>Part / Item</th>
                        <th style="width: 40%">Description</th>
                        <th>Qty</th>
                    </tr>
                </thead>
                <tbody>
                    @php $totalQty = 0; @endphp
                    @foreach ($suo->detail as $i => $item)
                        <tr style="font-size: 13px">
                            <td class="align-top">{{ $i + 1 }}</td>
                            <td class="text-nowrap align-top">
                                <p class="mb-0 fw-semibold" style="font-size: 12px">{{ $item->item_name }}</p>
                            </td>
                            <td>
                                <p class="mb-0" style="font-size: 12px">{{ $item->notes ?? '' }}</p>
                            </td>
                            <td class="align-top">{{ $item->qty }} {{ $item->unit }}</td>
                        </tr>
                        @php $totalQty += $item->qty; @endphp
                    @endforeach
                    <tr>
                        <td colspan="3"></td>
                        <td>{{ $totalQty }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="row">
            <div class="col-4 my-5 text-center">
                <div class="pb-5"></div>
                <p class="fw-bold mx-3 mb-0" style="border-top: 1px solid black">PT. Reftech Jaya Optima</p>
                <p>Shipper</p>
            </div>
            <div class="col-4"></div>
            <div class="col-4 my-5 text-center">
                <div class="pb-5"></div>
                <p class="fw-bold mx-3 mb-0" style="border-top: 1px solid black">{{ $suo->company }}</p>
                <p>Recieved</p>
            </div>
        </div>

    </div>
</div>
@push('after-style')
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/css/pages/app-invoice-print-do.css" />
@endpush
@push('after-script')
    <script src="{{ asset('assets') }}/js/app-invoice-print.js"></script>
@endpush
