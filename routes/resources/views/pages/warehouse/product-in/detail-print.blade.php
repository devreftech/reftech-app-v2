@extends('layouts.sales.app')
@section('title', $product->invoice)
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
                        {{ '  |  ' }}<i
                            class="mdi mdi-email-outline scaleX-n1-rtl me-1 mdi-14px"></i>admin@reftech.id
                    </p>
                    <p class="mb-1">
                    </p>
                </div>
            </div>
            <div class="text-end">
                <h3 class="fw-bold">Barang Masuk</h3>
                <div>
                    <span class="fw-bolder">#{{ $product->invoice }}</span>
                </div>
                <div class="mt-1">
                    <span class="text-muted">{{ Carbon\Carbon::parse($product->date)->format('d-m-Y') }}</span>
                </div>
            </div>
        </div>

        <hr>
        <div class="card-body mb-3">
            <div class="row">
                <div class="col-4 col-lg-2 fw-medium">
                    <p class="mb-1">Supplier </p>
                    <p class="mb-1">Note</p>
                </div>
                <div class="col-8">
                    <p class="mb-1">: {{ $product->supplier }}</p>
                    <p class="mb-1">: {{ $product->note }}</p>
                </div>
            </div>
        </div>
        <div class="mb-2">
            <table class="table table-borderless m-0" style="width: 100%">

                <thead class="table-light border-top">
                    <tr>
                        <th>No.</th>
                        <th>Item</th>
                        <th>Qty</th>
                        <th>Modal</th>
                        <th>Discount</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $no = 0;
                    @endphp
                    @foreach ($detail as $products)
                        @php
                            $no++;
                        @endphp
                        <tr style="font-size: 13px">
                            <td class="align-top">{{ $no }}</td>
                            <td class="text-nowrap align-top">
                                <p class="mb-0 fw-semibold" style="font-size: 12px">
                                    {{ $products->detailProduct->replacement }}
                                </p>
                                <pre class="mb-0"
                                    style="font-size: 10px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; max-width: 100%; overflow-x: auto; white-space: pre-wrap;">{{ $products->detailProduct->product->description }}</pre>
                            </td>
                            <td class="align-top">{{ $products->qty }} {{ $products->detailProduct->product->unit }}
                            </td>
                            @if (Auth::user()->role == 'Logistic')
                                <td class="align-top">RP {{ str_repeat('*', strlen((string) $products->modal)) }}
                                </td>
                                <td class="align-top">RP {{ str_repeat('*', strlen((string) $products->disc)) }}
                                </td>
                                <td class="align-top">RP {{ str_repeat('*', strlen((string) $products->amount)) }}
                                </td>
                            @else
                                <td class="align-top">RP {{ number_format($products->modal, 0, '', '.') }}</td>
                                <td class="align-top">RP {{ number_format($products->disc, 0, '', '.') }}</td>
                                <td class="align-top">RP {{ number_format($products->amount, 0, '', '.') }}</td>
                            @endif
                        </tr>
                    @endforeach
                    <tr style="font-size: 13px">
                        <td colspan="4" style="border:none;"></td>
                        <td>Subtotal</td>
                        @if (Auth::user()->role == 'Logistic')
                            <td>: RP {{ str_repeat('*', strlen((string) $product->subtotal)) }}</td>
                        @else
                            <td>: RP {{ number_format($product->subtotal, 0, '', '.') }}</td>
                        @endif
                    </tr>
                    <tr style="font-size: 13px">
                        <td colspan="4" style="border:none;"></td>
                        <td>Tax {{ $product->tax == '11' ? '11%' : '' }}</td>
                        @if (Auth::user()->role == 'Logistic')
                            <td>: RP {{ str_repeat('*', strlen((string) $tax)) }}</td>
                        @else
                            <td>: RP {{ number_format($tax, 0, '', '.') }}</td>
                        @endif
                    </tr>
                    <tr style="font-size: 13px;">
                        <td colspan="4" style="border:none;"></td>
                        <td>Shipping</td>
                        <td>: RP {{ number_format($product->shipping, 0, '', '.') }}</td>
                    </tr>
                    <tr style="font-size: 13px">
                        <td colspan="4" style="border:none;"></td>
                        <td style="border:none;" class="total">Total</td>
                        @if (Auth::user()->role == 'Logistic')
                            <td style="border:none;" class="total">: RP
                                {{ str_repeat('*', strlen((string) $product->total)) }}</td>
                        @else
                            <td style="border:none;" class="total">: RP
                                {{ number_format($product->total, 0, '', '.') }}</td>
                        @endif
                    </tr>
                </tbody>
            </table>
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
