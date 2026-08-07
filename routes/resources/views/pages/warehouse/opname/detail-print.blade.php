@extends('layouts.sales.app')
@section('title', 'Print Opname')
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
                <h3 class="fw-bold">Stock Opname</h3>
                <div>
                    <span class="fw-bolder mb-1">#PERIODE CATURWULAN - {{ $opname->periode }}</span>
                </div>
                <span class="fw-medium">Petugas Gudang - {{ $opname->user->name }}</span>
                <div class="mt-1">
                    <span class="text-muted">{{ Carbon\Carbon::parse($opname->date)->format('d-m-Y') }}</span>
                </div>
            </div>
        </div>

        <hr>
        <h5 class="text-muted">*note: yang belum diinput dijadikan 0;</h5>
        <div class="mb-2">
            <table class="table table-borderless m-0" style="width: 100%">
                <thead class="table-light border-top">
                    <tr>
                        <th>No.</th>
                        <th>Item</th>
                        <th>Desc</th>
                        <th>Stock Web</th>
                        <th>Stock Gudang</th>
                        <th>Stock Sebelumnya</th>
                        <th>Selisih</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $no = 0;
                    @endphp
                    @foreach ($detailOpname as $products)
                        <tr style="font-size: 13px">
                            <td>{{ $loop->iteration }}</td>
                            <td class="text-start">
                                <p class="mb-0 fw-semibold">{{ $products->replacement }}</p>
                            </td>
                            <td>
                                <pre class="mb-0" style="font-size: 13px;">{{ $products->description }}</pre>
                            </td>
                            <td>
                                {{ $products->stock_sistem }} {{ $products->unit }}
                            </td>
                            <td>
                                {{ $products->stock_gudang }} {{ $products->unit }}
                            </td>
                            <td>
                                {{ $products->prev_qty }} {{ $products->unit }}
                            </td>
                            <td>
                                {{ $products->selisih }} {{ $products->unit }}
                            </td>
                        </tr>
                    @endforeach
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
