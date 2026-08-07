@extends('layouts.sales.app')
@section('title', $purchase->no_po)
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
                            class="mdi mdi-email-outline scaleX-n1-rtl me-1 mdi-14px"></i>info@reftech.id
                    </p>
                    <p class="mb-1">
                    </p>
                </div>
            </div>
            <div class="text-end">
                <h3 class="fw-bold">Purchase Order</h3>
                <div>
                    <span class="fw-bolder">#{{ $purchase->no_po }}</span>
                </div>
                <div class="mt-1">
                    <span class="text-muted">{{ Carbon\Carbon::parse($purchase->date)->format('d-m-Y') }}</span>
                </div>
            </div>
        </div>

        <hr class="my-0">
        <div class="card-body mb-3">
            <div class="row">
                <div class="col-6">
                    <h6 class="fw-semibold fs-4 mb-3">Vendor:</h6>
                </div>
                <div class="col-6 mb-2">
                </div>
            </div>
            <div class="row">
                <div class="col-2 fw-medium">
                    <p class="mb-1">ATTN </p>
                    <p class="mb-1">Company </p>
                    <p class="mb-1">Phone </p>
                    <p class="mb-1">Address</p>
                </div>
                <div class="col-4">
                    <p class="mb-1">: {{ $purchase->attn }}</p>
                    <p class="mb-1">: {{ $purchase->company }}</p>
                    <p class="mb-1">: {{ $purchase->phone }}</p>
                    <p class="mb-1">: {{ $purchase->address }}</p>
                </div>
                <div class="col-3 fw-medium text-end">
                    <p class="mb-1">Mobile :</p>
                    <p class="mb-1">Email :</p>
                    <p class="mb-1">Payment :</p>
                    <p class="mb-1">Delivery Time :</p>
                </div>
                <div class="col-3 text-end">
                    <p class="mb-1"> {{ $purchase->mobile ?? '-' }}</p>
                    <p class="mb-1"> {{ $purchase->email ?? '-' }}</p>
                    <p class="mb-1"> {{ $purchase->payment ?? '-' }}</p>
                    <p class="mb-1"> {{ $purchase->delivery ?? '-' }}</p>
                </div>
            </div>
        </div>
        <div class="mb-5">
            <table class="table table-borderless m-0" style="width: 100%">

                <thead class="table-light border-top">
                    <tr>
                        <th>No.</th>
                        <th>Item</th>
                        <th>Qty</th>
                        <th>Price</th>
                        <th>Disc</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $no = 0;
                    @endphp
                    @foreach ($dPurchase as $product)
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
                            <td class="align-top">{{ $product->qty }} {{ $product->info_qty }} </td>
                            <td class="align-top text-end">RP {{ number_format($product->price, 0, '', '.') }}</td>
                            <td class="align-top">{{ $product->disc }} % </td>
                            <td class="align-top text-end">RP {{ number_format($product->amount, 0, '', '.') }}
                            </td>
                        </tr>
                    @endforeach
                    <tr style="border-bottom: #FFFFFF;">
                        <td colspan="4" class="align-top px-4 py-5">
                            <div class="row">
                                <div class="col-3 fw-medium">
                                    <p class="mb-1">Note </p>
                                </div>
                                <div class="col">
                                    <p class="mb-1">: {{ $purchase->note }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="text-end px-4 py-5">
                            <p class="mb-2">Subtotal:</p>
                            <p class="mb-2">Diskon:</p>
                            <p class="mb-2">Total:</p>
                            @if ($purchase->vat > 0)
                                <p class="mb-2">DPP Nilai Lain :</p>
                                <p class="mb-2">VAT 12% :</p>
                                <p class="mb-2 fw-bolder">Total Price :</p>
                            @endif
                        </td>
                        @php
                            $tax = ($purchase->total * 11) / 100;
                            $noTax = $purchase->total - ($purchase->total * 11) / 100;
                            $dpp = ($noTax * 11) / 12;
                        @endphp
                        <td class="px-4 py-5">
                            <p class="fw-semibold mb-2 text-end">RP
                                {{ number_format($purchase->subtotal, 0, '', '.') }}</p>
                            <p class="fw-semibold mb-2 text-end">RP
                                {{ number_format($purchase->diskon, 0, '', '.') }}</p>
                            <p class="fw-semibold mb-2 text-end">RP
                                {{ number_format($purchase->vat > 0 ? $noTax : $purchase->total, 0, '', '.') }}</p>
                            @if ($purchase->vat > 0)
                                <p class="fw-semibold mb-2 text-end">
                                    {{ $dpp == '0' ? '0' : 'RP ' . number_format($dpp, 0, '', '.') }}</p>
                                <p class="fw-semibold mb-2 text-end">
                                    {{ $tax == '0' ? '0' : 'RP ' . number_format($tax, 0, '', '.') }}</p>
                                <p class="fw-semibold mb-2 text-end">
                                    {{ $purchase->total == '0' ? '0' : 'RP ' . number_format($purchase->total, 0, '', '.') }}
                                </p>
                            @endif
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="row">
            <div class="col-4 text-center">
                <p class="fs-normal fw-bolder">Authorized By.</p>
                <img src="{{ url('') . '/asset/sign/ttdAngel.jpg' }}" alt="" srcset="" height="77">
                <p class="pt-3">Reftech Jaya Optima</p>
            </div>
            <div class="col-4"></div>
            <div class="col-4 text-center">
                <p class="fs-normal fw-bolder">Accepted By Vendor.</p>
                <div class="pb-5"></div>
                <p class="pt-3 mb-0 mt-2">{{ $purchase->attn }}</p>
                <p>{{ $purchase->company }}</p>
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
