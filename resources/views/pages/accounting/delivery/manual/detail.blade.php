@extends('layouts.sales.app')
@section('title', 'Delivery Order ' . $delivery->do_number)
@section('content')
    @php
        $isReftech    = ($delivery->entity_display ?? 'Reftech') === 'Reftech';
        $companyName  = $isReftech ? 'PT Reftech Jaya Optima' : 'PT Kojisha Innotiv Indonesia';
        $doNumber     = $delivery->do_number;
        $customerName = $delivery->customer_name_display;
        $address      = $delivery->address_display;
        $poNumber     = $delivery->po_number_display;
        $deliveryDate = $delivery->date ? \Carbon\Carbon::parse($delivery->date)->format('d-m-Y') : '-';
    @endphp

    <div class="row invoice-preview">
        {{-- Invoice / DO Container --}}
        @if (strtolower($delivery->type) == 'ekspedisi')
            <div class="col-xl-9 col-md-8 col-12 mb-md-0 mb-4">
                <div class="card invoice-preview-card">
                    <div class="card-body">
                        @if ($isReftech)
                            <div class="d-flex justify-content-between flex-xl-row flex-md-column flex-sm-row flex-column">
                                <div class="mb-xl-0 pb-1">
                                    <div class="d-flex svg-illustration align-items-center gap-2 mb-4">
                                        <span class="app-brand-logo demo">
                                            <span style="color: var(--bs-primary)">
                                                <img class="text-md" src="{{ asset('/asset') }}/logo/Reftech-Log.png"
                                                    alt="Reftech Logo" width="160">
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
                                                class="mdi mdi-email-outline scaleX-n1-rtl me-1 mdi-14px"></i>accounting@reftech.id
                                        </p>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <h1 class="fw-bold" style="color: blue;">Delivery Order</h1>
                                    <div>
                                        <span class="fw-bolder fs-5">#{{ $doNumber }}</span>
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
                                                    alt="Kojisha Logo" width="160">
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
                                        <span class="fw-bolder fs-5">#{{ $doNumber }}</span>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="card-body mb-3">
                        <div class="table-responsive text-nowrap">
                            <table class="table table-bordered" style="border: 1px solid black;">
                                <tr>
                                    <td colspan="2" style="vertical-align: top; width: 50%;">
                                        <div class="row">
                                            <div class="col-4 fw-medium">
                                                <p class="mb-1">Customers </p>
                                                <p class="mb-1">Address</p>
                                            </div>
                                            <div class="col-8">
                                                <p class="mb-1 fw-bold">: {{ $customerName }}</p>
                                                <pre style="font-size: 13px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; max-width: 100%; overflow-x: auto; white-space: pre-wrap;">: {{ $address }}</pre>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="background-color: #F9F9F9;" class="text-center">
                                        <p class="fs-5 text-black fw-medium m-0">Purchase Order :</p>
                                    </td>
                                    <td style="background-color: #F9F9F9;" class="text-center">
                                        <p class="fs-5 text-black fw-medium m-0">Shipment Date :</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-center">
                                        <pre style="font-size: 13px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; max-width: 100%; overflow-x: auto; white-space: pre-wrap;">{{ $poNumber }}</pre>
                                    </td>
                                    <td class="text-center">
                                        <pre style="font-size: 13px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; max-width: 100%; overflow-x: auto; white-space: pre-wrap;">{{ $deliveryDate }}</pre>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div class="card-body mb-3">
                        <div class="table-responsive mb-4">
                            <table class="table table-bordered m-0" style="border: 1px solid rgb(60, 60, 60)">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 5%">No.</th>
                                        <th style="width: 45%">Description</th>
                                        <th style="width: 25%">Keterangan</th>
                                        <th style="width: 25%">Qty</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $no = 0;
                                        $qty = 0;
                                    @endphp
                                    @foreach ($dDelivery as $product)
                                        @php
                                            $no++;
                                            $qty += (float) $product->qty;
                                        @endphp
                                        <tr style="font-size: 13px">
                                            <td class="align-top text-center">{{ $no }}</td>
                                            <td class="align-top">
                                                <p class="mb-0 fw-semibold" style="font-size: 13px">
                                                    {{ $product->product ?: $product->desc }}
                                                </p>
                                            </td>
                                            <td class="align-top">
                                                <p class="mb-0 text-muted" style="font-size: 12px">
                                                    {{ $product->product ? $product->desc : '' }}
                                                </p>
                                            </td>
                                            <td class="align-top fw-bold">{{ $product->qty }} {{ $product->info_qty }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="row">
                            <div class="col-4 my-4 text-center">
                                <div class="pb-5"></div>
                                <p class="fw-bold mx-3 mb-0" style="border-top: 1px solid black ">{{ $companyName }}</p>
                                <p class="text-muted small">Shipper</p>
                            </div>
                            <div class="col-4"></div>
                            <div class="col-4 my-4 text-center">
                                <div class="pb-5"></div>
                                <p class="fw-bold mx-3 mb-0" style="border-top: 1px solid black ">{{ $customerName }}</p>
                                <p class="text-muted small">Received</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            {{-- Type Teknisi --}}
            <div class="col-xl-9 col-md-8 col-12 mb-md-0 mb-4">
                <div class="card invoice-preview-card">
                    <div class="card-body">
                        <div class="table-responsive mb-4">
                            <table class="table table-bordered m-0" style="border: 1px solid rgb(60, 60, 60)">
                                <tbody>
                                    <tr>
                                        <td colspan="3" class="py-1">
                                            <div class="row">
                                                <div class="col-8">
                                                    <h5 class="fw-bold mb-0">Delivery Order</h5>
                                                </div>
                                                <div class="col-4 text-end">
                                                    <p class="mb-0"><span class="fw-bold">D.O. No :</span> {{ $doNumber }}</p>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" class="py-0">
                                            <div class="row">
                                                <div class="col-6">
                                                    @if ($isReftech)
                                                        <div class="d-flex justify-content-between flex-xl-row flex-md-column flex-sm-row flex-column py-2">
                                                            <div class="mb-xl-0 pb-1">
                                                                <div class="d-flex svg-illustration align-items-center gap-2">
                                                                    <span class="app-brand-logo demo">
                                                                        <img class="text-md" src="{{ asset('/asset') }}/logo/Reftech-Log.png" alt="" width="140">
                                                                    </span>
                                                                </div>
                                                                <p class="mb-1 mx-2 fw-bolder">PT Reftech Jaya Optima</p>
                                                                <div class="mx-2" style="font-size: 10px">
                                                                    <p class="mb-1">Taman Kopo Indah V, Ruko Sommerville No. 31, Bandung</p>
                                                                    <p class="mb-1">022 54417653 | accounting@reftech.id</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @else
                                                        <div class="d-flex justify-content-between flex-xl-row flex-md-column flex-sm-row flex-column py-2">
                                                            <div class="mb-xl-0 pb-1">
                                                                <div class="d-flex svg-illustration align-items-center gap-2 mb-2">
                                                                    <span class="app-brand-logo demo">
                                                                        <img class="text-md" src="{{ asset('/asset') }}/logo/Logo-update-size.png" alt="" width="140">
                                                                    </span>
                                                                </div>
                                                                <p class="mb-1 fw-bolder">PT Kojisha Innotiv Indonesia</p>
                                                                <div style="font-size: 10px">
                                                                    <p class="mb-1">Jl. Nancep No. 45A, Setu, Cibitung - Kab. Bekasi</p>
                                                                    <p class="mb-1">+62 812-1000-0997 | admin@kojisha.com</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif
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
                                                            <p class="mb-1">: {{ $deliveryDate }}</p>
                                                            <p class="mb-1">: {{ $poNumber }}</p>
                                                            <p class="mb-1 fw-bold">: {{ $customerName }}</p>
                                                            <p class="mb-1">: {{ $address }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr class="table-light">
                                        <th class="text-center" style="width: 8%">No</th>
                                        <th class="text-center" style="width: 20%">Qty</th>
                                        <th class="text-center" style="width: 72%">Description</th>
                                    </tr>
                                    @php
                                        $no = 0;
                                    @endphp
                                    @foreach ($dDelivery as $product)
                                        @php
                                            $no++;
                                        @endphp
                                        <tr style="font-size: 13px">
                                            <td class="text-center align-top">{{ $no }}</td>
                                            <td class="text-center align-top fw-bold">{{ $product->qty }} {{ $product->info_qty }}</td>
                                            <td class="align-top">
                                                <p class="mb-0 fw-semibold">{{ $product->product }}</p>
                                                @if ($product->desc)
                                                    <p class="mb-0 text-muted small">{{ $product->desc }}</p>
                                                @endif
                                            </td>
                                        </tr>
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
                                                    <p class="fw-bold mx-3 mb-0" style="border-top: 1px solid black ">Received</p>
                                                </div>
                                            </div>
                                            <p class="mb-0 small text-muted">Distribusi : Putih dan Pink &rarr; Pelanggan, <span class="fw-bold">Kuning &rarr; Accounting {{ $companyName }}</span></p>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Sidebar Actions --}}
        <div class="col-xl-3 col-md-4 col-12 invoice-actions">
            <div class="card mb-3 shadow-sm">
                <div class="card-body">
                    <a class="btn btn-primary d-grid w-100 mb-2 waves-effect d-flex align-items-center justify-content-center gap-1" target="_blank"
                        href="{{ route('delivery.print_manual', $delivery->id) }}">
                        <i class="mdi mdi-printer me-1"></i> Cetak Surat Jalan
                    </a>
                    <a href="{{ route('delivery.index') }}" class="btn btn-outline-secondary d-grid w-100 waves-effect d-flex align-items-center justify-content-center gap-1">
                        <i class="mdi mdi-arrow-left me-1"></i> Kembali ke List DO
                    </a>
                </div>
            </div>
            <div class="card mb-3 shadow-sm">
                <div class="card-body">
                    <h6 class="fw-bold mb-2">Info Surat Jalan</h6>
                    <ul class="list-unstyled mb-0 small">
                        <li class="mb-1"><span class="text-muted">Nomor DO:</span> <span class="fw-semibold">{{ $doNumber }}</span></li>
                        <li class="mb-1"><span class="text-muted">Customer:</span> <span class="fw-semibold">{{ $customerName }}</span></li>
                        <li class="mb-1"><span class="text-muted">Tanggal:</span> <span class="fw-semibold">{{ $deliveryDate }}</span></li>
                        <li class="mb-1"><span class="text-muted">Jenis:</span> <span class="badge bg-label-info">{{ ucfirst($delivery->type) }}</span></li>
                        <li class="mb-1"><span class="text-muted">Entitas:</span> <span class="badge bg-label-primary">{{ $delivery->entity_display }}</span></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection
