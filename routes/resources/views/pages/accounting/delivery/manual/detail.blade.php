@extends('layouts.sales.app')
@section('title', 'Delivery Order')
@section('content')
    <div class="row invoice-preview">
        {{-- Invoice --}}
        @if ($delivery->type == 'ekspedisi')
            <div class="col-xl-9 col-md-8 col-12 mb-md-0 mb-4">
                <div class="card invoice-preview-card">
                    <div class="card-body">
                        @if ($invoice->flag == 'Reftech')
                            <div class="d-flex justify-content-between flex-xl-row flex-md-column flex-sm-row flex-column">
                                <div class="mb-xl-0 pb-1">
                                    <div class="d-flex svg-illustration align-items-center gap-2 mb-4">
                                        <span class="app-brand-logo demo">
                                            <span style="color: var(--bs-primary)">
                                                <img class="text-md" src="{{ asset('/asset') }}/logo/Reftech-Log.png"
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
                                            <i class="mdi mdi-phone-outline scaleX-n1-rtl me-1 mdi-14px"></i>+62
                                            812-1000-0997
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
                            if ($delivery->destination == '1') {
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
                                            style="font-size: 13px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; max-width: 100%; overflow-x: auto; white-space: pre-wrap;">{{ Carbon\Carbon::parse($delivery->date)->format('d-m-Y') }}</pre>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div class="table-responsive mb-5">
                        <table class="table table-bordered m-0" style="border: 1px solid rgb(60, 60, 60)">
                            {{-- @if ($invoice->quote->type == 'Sparepart') --}}
                            <thead class="table-light">
                                <tr>
                                    <th>No.</th>
                                    {{-- <th>Part Number</th> --}}
                                    <th style="width: 40%">Description</th>
                                    <th>Qty</th>
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
                                        @endphp
                                        <tr style="font-size: 13px">
                                            <td class="align-top">{{ $no }}</td>
                                            <td class="text-nowrap align-top">
                                                <p class="mb-0 fw-semibold" style="font-size: 12px">
                                                    {{ $product->product }}
                                                </p>
                                            </td>
                                            {{-- <td>
                                                <pre class="mb-0"
                                                    style="font-size: 10px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; max-width: 100%; overflow-x: auto; white-space: pre-wrap;">{{ $product->view == '0' ? $product->desc : '' }}</pre>
                                            </td> --}}
                                            <td class="align-top">{{ $product->qty }} {{ $product->info_qty }} </td>
                                        </tr>
                                        @php
                                            $qty += $product->qty;
                                        @endphp
                                    @endforeach
                                    <tr>
                                        <td colspan="2"></td>
                                        <td>{{ $qty }} {{ $product->info_qty }} </td>
                                    </tr>
                                </tbody>
                            {{-- @else
                            <thead class="table-light">
                                <tr>
                                    <th>No.</th>
                                    <th>Description</th>
                                    <th>Qty</th>
                                </tr>
                            </thead>
                                <tbody>

                                    @php
                                        $abjad = 64;
                                        $totalPph = 0;
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
                                            <td class="text-nowrap align-top" colspan="3"
                                                style="border-bottom:none !important; background-color: #f0f0f0;">
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
                                                    <p class="mb-0">{{ $no }}</p>
                                                </td>
                                                <td class="text-nowrap align-top py-1"
                                                    style="border-bottom:none !important;">
                                                    <p class="mb-0">{{ $product->product }}</p>
                                                </td>
                                                <td class="align-top py-1" style="border-bottom:none !important;">
                                                    <p class="mb-0">{{ $product->qty }} {{ $product->info_qty }}</p>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endforeach
                                </tbody>
                            @endif --}}
                        </table>
                    </div>
                    <div class="row">
                        <div class="col-4 my-5 text-center">
                            <div class="pb-5"></div>
                            @if ($delivery->invoice->flag == 'Reftech')
                                <p class="fw-bold mx-3 mb-0" style="border-top: 1px solid black ">PT. Reftech Jaya Optima
                                </p>
                            @else
                                <p class="fw-bold mx-3 mb-0" style="border-top: 1px solid black ">PT. Kojisha Innotiv
                                    Indonesia
                                </p>
                            @endif
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
        @else
            <div class="col-xl-9 col-md-8 col-12 mb-md-0 mb-4">
                <div class="card invoice-preview-card">
                    <div class="card-body">
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
                                                        {{ $invoice->no_invoice }}</p>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" class="py-0">
                                            <div class="row">
                                                <div class="col-6">
                                                    @if ($invoice->flag == 'Reftech')
                                                        <div
                                                            class="d-flex justify-content-between flex-xl-row flex-md-column flex-sm-row flex-column">
                                                            <div class="mb-xl-0 pb-1">
                                                                <div
                                                                    class="d-flex svg-illustration align-items-center gap-2">
                                                                    <span class="app-brand-logo demo">
                                                                        <span style="color: var(--bs-primary)">
                                                                            <img class="text-md"
                                                                                src="{{ asset('/asset') }}/logo/Reftech-Log.png"
                                                                                alt="" srcset=""
                                                                                width="60%">
                                                                        </span>
                                                                    </span>
                                                                </div>
                                                                <p class="mb-1 mx-2 fw-bolder">PT Reftech Jaya Optima</p>
                                                                <div class="mx-2" style="font-size: 10px">
                                                                    <p class="mb-1">Taman Kopo Indah V, Ruko Sommerville
                                                                        No.
                                                                        31</p>
                                                                    <p class="mb-1">Bandung – Jawa Barat 40218</p>
                                                                    <p class="mb-1">
                                                                        <i
                                                                            class="mdi mdi-phone-outline scaleX-n1-rtl me-1 mdi-14px"></i>022
                                                                        54417653
                                                                        {{ '   ' }}<i
                                                                            class="mdi mdi-email-outline scaleX-n1-rtl me-1 mdi-14px"></i>admin@reftech.id
                                                                    </p>
                                                                    <p class="mb-1">
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @else
                                                        <div
                                                            class="d-flex justify-content-between flex-xl-row flex-md-column flex-sm-row flex-column">
                                                            <div class="mb-xl-0 pb-1">
                                                                <div
                                                                    class="d-flex svg-illustration align-items-center gap-2 mb-2">
                                                                    <span class="app-brand-logo demo">
                                                                        <span style="color: var(--bs-primary)">
                                                                            <img class="text-md"
                                                                                src="{{ asset('/asset') }}/logo/Logo-update-size.png"
                                                                                alt="" srcset=""
                                                                                width="60%">
                                                                        </span>
                                                                    </span>
                                                                </div>
                                                                <p class="mb-1 fw-bolder">PT Kojisha Innotiv Indonesia</p>
                                                                <div style="font-size: 10px">
                                                                    <p class="mb-1">Jl. Nancep No. 45A, Setu</p>
                                                                    <p class="mb-1">Cibitung - Kab. Bekasi 17320</p>
                                                                    <p class="mb-1">
                                                                        <i
                                                                            class="mdi mdi-phone-outline scaleX-n1-rtl me-1 mdi-14px"></i>+62
                                                                        812-1000-0997
                                                                        {{ ' | ' }}<i
                                                                            class="mdi mdi-email-outline scaleX-n1-rtl me-1 mdi-14px"></i>admin@kojisha.com
                                                                    </p>
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
                                                            <p class="mb-1">: {{ $delivery->date }}</p>
                                                            <p class="mb-1">: {{ $invoice->no_po }}</p>
                                                            <p class="mb-1">: {{ $quote->pic->client->company }}</p>
                                                            @if ($delivery->destination == '1')
                                                                <p class="mb-1">: {{ $quote->pic->client->address }}</p>
                                                            @else
                                                                <p class="mb-1">: {{ $quote->pic->client->subAddress }}
                                                                </p>
                                                            @endif
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
                                    {{-- @if ($quote->type == 'Sparepart') --}}
                                        @php
                                            $no = 0;
                                        @endphp
                                        <tr style="font-size: 13px">
                                            <td class="text-nowrap align-top">
                                                @foreach ($dDelivery as $product)
                                                    @php
                                                        $no++;
                                                    @endphp
                                                    <p class="mb-0 fw-semibold">
                                                        {{ $no }}
                                                    </p>
                                                @endforeach
                                            </td>
                                            <td class="text-nowrap align-top">
                                                @foreach ($dDelivery as $product)
                                                    <p class="mb-0 fw-semibold">
                                                        {{ $product->qty }} {{ $product->info_qty }}
                                                    </p>
                                                @endforeach
                                            </td>
                                            <td class="text-nowrap align-top">
                                                @foreach ($dDelivery as $product)
                                                    <p class="mb-0 fw-semibold">
                                                        {{ $product->product }}
                                                        {{ $product->desc ?? '' }}
                                                    </p>
                                                @endforeach
                                            </td>
                                        </tr>
                                    {{-- @else
                                        @php
                                            $abjad = 64;
                                            $totalPph = 0;
                                        @endphp
                                        @foreach ($subQuote as $subJudul)
                                            @php
                                                $no = 0;
                                                $abjad++;
                                            @endphp
                                            <tr style="font-size: 13px;">
                                                <td class="align-top" style="background-color: #f0f0f0;">
                                                    <p class="fw-bold mb-0">{{ chr($abjad) }}</p>
                                                </td>
                                                <td class="text-nowrap align-top" colspan="2"
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
                                                        <p class="mb-0">{{ $no }}</p>
                                                    </td>
                                                    <td class="align-top py-1" style="border-bottom:none !important;">
                                                        <p class="mb-0">{{ $product->qty }} {{ $product->info_qty }}
                                                        </p>
                                                    </td>
                                                    <td class="text-nowrap align-top py-1"
                                                        style="border-bottom:none !important;">
                                                        <p class="mb-0">{{ $product->product }}</p>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endforeach
                                    @endif --}}
                                    <tr>
                                        <td colspan="3">
                                            <div class="row mb-3">
                                                <div class="col-4 mt-5 text-center">
                                                    <div class="pb-5"></div>
                                                    <p class="fw-bold mx-3 mb-0" style="border-top: 1px solid black ">
                                                        Shipper</p>
                                                </div>
                                                <div class="col-4"></div>
                                                <div class="col-4 mt-5 text-center">
                                                    <div class="pb-5"></div>
                                                    <p class="fw-bold mx-3 mb-0" style="border-top: 1px solid black ">
                                                        Recieved</p>
                                                </div>
                                            </div>
                                            <p class="mb-0">Distribusi : Putih dan Pink → Pelanggan, <span
                                                    class="fw-bold">Kuning → Accounting
                                                    {{ $delivery->invoice->flag == 'Reftech' ? 'PT. Reftech' : 'PT. Kojisha' }}</span>
                                            </p>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        {{-- End: Invoice --}}
        {{-- Button Invocie --}}
        <div class="col-xl-3 col-md-4 col-12 invoice-actions">
            <div class="card mb-3">
                <div class="card-body">
                    <a class="btn btn-primary d-grid w-100 mb-3 waves-effect" target="_blank"
                        href="{{ route('delivery.print_manual', $delivery->id) }}">
                        Download
                    </a>
                    <a type="button" data-bs-toggle="modal" data-bs-target="#descView"
                        class="d-grid w-100 waves-effect mb-3">
                        <button type="button" class="btn btn-outline-primary">
                            Change Description Product
                        </button>
                    </a>
                    <a href="#" class="btn btn-outline-danger d-grid w-100 waves-effect delete-delivery mb-3"
                        data-id="{{ $delivery->id }}" data-in="{{ $invoice->id }}">Delete</a>
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
        {{-- @include('components.modal.delivery.desc') --}}
        @include('components.modal.accounting.delivery.change-date')
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
            $(() => {

                $(document).on('click', '.delete-delivery', function() {
                    var id = $(this).data('id');
                    var invoice = $(this).data('in');
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
                                'url': '{{ url('delivery') }}/' + id,
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
                                            window.location.href = '/invoice/' +
                                                invoice;
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
            });
        </script>
    @endpush
