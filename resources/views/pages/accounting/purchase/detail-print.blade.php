@extends('layouts.sales.app')
@section('title', $purchase->no_po)
<div class="invoice-print p-4">
    <div class="container-fluid flex-grow-1 container-p-y">

        {{-- Letterhead --}}
        <div class="d-flex justify-content-between align-items-start pb-3 mb-4 border-bottom border-2 border-primary">
            <div class="mb-xl-0 pb-1">
                <div class="d-flex svg-illustration align-items-center gap-2 mb-3">
                    <span class="app-brand-logo demo">
                        <span style="color: var(--bs-primary)">
                            <img class="text-md" src="{{ asset('/asset') }}/logo/Reftech-Log.png" alt=""
                                srcset="" width="60%">
                        </span>
                    </span>
                </div>
                <p class="mb-1 fw-bolder">PT Reftech Jaya Optima</p>
                <div style="font-size: 11px" class="text-muted">
                    <p class="mb-1">Taman Kopo Indah V, Soho Sommerville No. 31</p>
                    <p class="mb-1">Bandung – Jawa Barat 40218</p>
                    <p class="mb-0">
                        <i class="mdi mdi-phone-outline scaleX-n1-rtl me-1 mdi-14px"></i>022 54417653
                        {{ '   ' }}<i
                            class="mdi mdi-email-outline scaleX-n1-rtl me-1 mdi-14px"></i>info@reftech.id
                    </p>
                </div>
            </div>
            <div class="text-end">
                <h4 class="fw-bold text-primary mb-1">PURCHASE ORDER</h4>
                <p class="mb-0 fw-semibold">No. {{ $purchase->no_po }}</p>
                <p class="mb-0 text-muted">{{ Carbon\Carbon::parse($purchase->date)->format('d F Y') }}</p>
            </div>
        </div>

        {{-- Vendor Info --}}
        <div class="border rounded p-3 mb-4">
            <p class="text-uppercase text-muted small fw-semibold mb-2">Vendor</p>
            <div class="row">
                <div class="col-6">
                    <table style="font-size: 13px">
                        <tr>
                            <td class="fw-medium pe-2" style="width: 90px">ATTN</td>
                            <td>: {{ $purchase->attn }}</td>
                        </tr>
                        <tr>
                            <td class="fw-medium pe-2">Company</td>
                            <td>: {{ $purchase->company }}</td>
                        </tr>
                        <tr>
                            <td class="fw-medium pe-2">Phone</td>
                            <td>: {{ $purchase->phone }}</td>
                        </tr>
                        <tr>
                            <td class="fw-medium pe-2">Address</td>
                            <td>: {{ $purchase->address }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-6">
                    <table style="font-size: 13px">
                        <tr>
                            <td class="fw-medium pe-2" style="width: 110px">Mobile</td>
                            <td>: {{ $purchase->mobile ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="fw-medium pe-2">Email</td>
                            <td>: {{ $purchase->email ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="fw-medium pe-2">Payment</td>
                            <td>: {{ $purchase->payment ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="fw-medium pe-2">Delivery Time</td>
                            <td>: {{ $purchase->delivery ?? '-' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        {{-- Item Table --}}
        <div class="mb-4">
            <table class="table table-bordered m-0" style="width: 100%">
                <thead class="table-light border-top text-center">
                    <tr>
                        <th style="width: 1%">No.</th>
                        <th style="width: 40%">Item Description</th>
                        <th style="width: 12%">Qty</th>
                        <th style="width: 20%">Price (IDR)</th>
                        <th style="width: 1%">Disc</th>
                        <th style="width: 26%">Amount (IDR)</th>
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
                            <td class="align-top text-center">{{ $no }}</td>
                            <td class="text-wrap align-top">
                                <p class="mb-0 fw-semibold" style="font-size: 12px">{{ $product->product }}</p>
                            </td>
                            <td class="align-top text-center">{{ $product->qty }} {{ $product->info_qty }}</td>
                            <td class="align-top text-end">{{ number_format($product->price, 0, '', '.') }}</td>
                            <td class="align-top text-center">{{ $product->disc }}%</td>
                            <td class="align-top text-end">{{ number_format($product->amount, 0, '', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Note + Financial Summary --}}
        @php
            $tax = ($purchase->total * 11) / 100;
            $noTax = $purchase->total - ($purchase->total * 11) / 100;
            $dpp = ($noTax * 11) / 12;
        @endphp
        <div class="row mb-4">
            <div class="col-7">
                <p class="text-uppercase text-muted small fw-semibold mb-2">Note</p>
                <p class="mb-0" style="font-size: 13px">{{ $purchase->note ?: '-' }}</p>
            </div>
            <div class="col-5">
                <div class="border rounded p-3">
                    <div class="d-flex justify-content-between py-1">
                        <span>Subtotal</span>
                        <span>RP {{ number_format($purchase->subtotal, 0, '', '.') }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-1">
                        <span>Discount</span>
                        <span>RP {{ number_format($purchase->diskon, 0, '', '.') }}</span>
                    </div>
                    <div
                        class="d-flex justify-content-between py-1 {{ $purchase->vat > 0 ? '' : 'fw-bold fs-5 text-primary' }}">
                        <span>Total</span>
                        <span>RP
                            {{ number_format($purchase->vat > 0 ? $noTax : $purchase->total, 0, '', '.') }}</span>
                    </div>
                    @if ($purchase->vat > 0)
                        <div class="d-flex justify-content-between py-1">
                            <span>Other Value Tax Base (DPP)</span>
                            <span>{{ $dpp == '0' ? '0' : 'RP ' . number_format($dpp, 0, '', '.') }}</span>
                        </div>
                        <div class="d-flex justify-content-between py-1">
                            <span>VAT 12%</span>
                            <span>{{ $tax == '0' ? '0' : 'RP ' . number_format($tax, 0, '', '.') }}</span>
                        </div>
                        <hr class="my-2">
                        <div class="d-flex justify-content-between fw-bold fs-5 text-primary">
                            <span>Total Price</span>
                            <span>{{ $purchase->total == '0' ? '0' : 'RP ' . number_format($purchase->total, 0, '', '.') }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Signatures --}}
        <div class="row mt-5">
            <div class="col-4 text-center">
                <p class="fs-normal fw-bolder mb-5">Authorized By.</p>
                <img src="{{ url('') . '/asset/sign/ttdAngel.jpg' }}" alt="" srcset="" height="77">
                <p class="border-top pt-2 mt-2 mb-0">Reftech Jaya Optima</p>
            </div>
            <div class="col-4"></div>
            <div class="col-4 text-center">
                <p class="fs-normal fw-bolder mb-5">Accepted By Vendor.</p>
                <div style="height: 77px"></div>
                <p class="border-top pt-2 mt-2 mb-0">{{ $purchase->attn }}</p>
                <p class="mb-0">{{ $purchase->company }}</p>
            </div>
        </div>
    </div>
</div>
@push('after-style')
    <!-- Page CSS -->
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/css/pages/app-invoice-print.css" />
@endpush
@push('after-script')
    <script src="{{ asset('assets') }}/js/app-invoice-print.js"></script>
@endpush
