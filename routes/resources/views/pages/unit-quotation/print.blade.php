@extends('layouts.sales.app')
@section('title', $quote->no_quote)

<div class="invoice-print p-4">
    <div class="container-fluid flex-grow-1 container-p-y">

        {{-- Header --}}
        <div class="d-flex justify-content-between flex-xl-row flex-md-column flex-sm-row flex-column mb-2">
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
                    <p class="mb-0">022 54417653 &nbsp;|&nbsp; info@reftech.id</p>
                </div>
            </div>
            <div class="text-end">
                <h3 class="fw-bold mb-1" style="letter-spacing:1px;">QUOTATION</h3>
                <p class="mb-1 fw-semibold" style="font-size:14px;">#{{ $quote->no_quote }}</p>
                <p class="mb-0 text-muted" style="font-size:12px;">{{ $quote->date?->format('d F Y') }}</p>
            </div>
        </div>

        <hr class="my-3">

        {{-- Quote To --}}
        <div class="d-flex justify-content-between mb-3" style="font-size:13px;">
            <div>
                <p class="mb-0 fw-semibold text-uppercase" style="font-size:10px; letter-spacing:.5px; color:#888;">Quote To</p>
                <p class="mb-0 fw-bold" style="font-size:14px;">{{ $quote->client?->company ?? '-' }}</p>
                <p class="mb-0">{{ $quote->pic?->name_pic ?? '-' }}</p>
                <p class="mb-0 text-muted">{{ $quote->pic?->phone_pic ?? '-' }}</p>
                @if ($quote->client?->email)
                    <p class="mb-0 text-muted">{{ $quote->client->email }}</p>
                @endif
            </div>
            <div class="text-end">
                <p class="mb-0 fw-semibold text-uppercase" style="font-size:10px; letter-spacing:.5px; color:#888;">From</p>
                <p class="mb-0 fw-bold" style="font-size:14px;">PT Reftech Jaya Optima</p>
                @if ($quote->no_pr)
                    <p class="mb-0 text-muted">No. PR: {{ $quote->no_pr }}</p>
                @endif
                @if ($quote->attn)
                    <p class="mb-0 text-muted">Attn: {{ $quote->attn }}</p>
                @endif
            </div>
        </div>

        <p class="mb-3" style="font-size:12px; color:#555;">
            Sir/Madam, We are pleased to offer the under-mentioned as per conditions and details described as following:
        </p>

        {{-- Items Table --}}
        @php
            $specLabels = [
                'brand'=>'Brand','model'=>'Model','type_unit'=>'Type',
                'bar'=>'Max Pressure','air_cap'=>'Air Capacity','power'=>'Motor Power',
                'voltage'=>'Voltage','connect'=>'Drive','cooling'=>'Cooling Method',
                'exhaust'=>'Connection','refrigerant_type'=>'Refrigerant Type','pdp'=>'PDP',
                'filtration'=>'Filtration','oil_content'=>'Oil Content','grade'=>'Grade',
                'capacity'=>'Capacity','material'=>'Material','test_pressure'=>'Test Pressure',
                'inlet_pressure'=>'Inlet Pressure','outlet_pressure'=>'Outlet Pressure',
                'inlet_cap'=>'Inlet Capacity (LP)','outlet_cap'=>'Outlet Capacity (HP)',
                'dimension'=>'Dimension','weight'=>'Weight',
            ];
            $specUnits = [
                'bar'=>' Bar','air_cap'=>' m³/min','test_pressure'=>' Bar',
                'inlet_pressure'=>' Bar','outlet_pressure'=>' Bar',
                'inlet_cap'=>' m³/min','outlet_cap'=>' m³/min',
                'weight'=>' Kg','capacity'=>' Liter',
            ];
            $hasDisc = $quote->details->where('disc', '>', 0)->count() > 0;
        @endphp

        <table class="table table-bordered m-0 mb-0" style="width:100%; font-size:12px;">
            <thead class="table-light text-center" style="font-size:11px;">
                <tr>
                    <th style="width:3%">No.</th>
                    <th style="width:{{ $hasDisc ? '44%' : '49%' }}">Item Description</th>
                    <th style="width:9%">Qty</th>
                    <th style="width:18%">Price (IDR)</th>
                    @if ($hasDisc)
                        <th style="width:6%">Disc</th>
                    @endif
                    <th style="width:{{ $hasDisc ? '20%' : '21%' }}">Total (IDR)</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($quote->details as $i => $item)
                    <tr>
                        <td class="align-top text-center">{{ $i + 1 }}</td>
                        <td class="align-top">
                            @if ($item->type === 'unit' && $item->unit)
                                <p class="mb-1 fw-semibold" style="font-size:12px;">
                                    {{ $item->label ?: ($item->unit->brand . ' ' . $item->unit->sku . ($item->unit->model ? ' — ' . $item->unit->model : '')) }}
                                </p>
                                @php $specs = $item->getSpecVisibleArray(); @endphp
                                @if (!empty($specs))
                                    <div style="font-size:10px; color:#555; margin-top:3px;">
                                        @foreach ($specs as $field)
                                            @if ($field === 'unit') @continue @endif
                                            @php $val = $item->unit->$field ?? null; @endphp
                                            @if ($val && isset($specLabels[$field]))
                                                <div style="display:flex; padding:1px 0;">
                                                    <span style="color:#888; min-width:110px; flex-shrink:0;">{{ $specLabels[$field] }}</span>
                                                    <span>: {{ $val }}{{ $specUnits[$field] ?? '' }}</span>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                @endif
                            @else
                                <p class="mb-0 fw-semibold" style="font-size:12px;">{{ $item->label }}</p>
                                @if ($item->description)
                                    <p class="mb-0 text-muted" style="font-size:10px;">{{ $item->description }}</p>
                                @endif
                            @endif
                        </td>
                        <td class="align-top text-center">{{ $item->qty }} {{ $item->info_qty ?? 'Unit' }}</td>
                        <td class="align-top text-end">{{ number_format($item->price, 0, '', '.') }}</td>
                        @if ($hasDisc)
                            <td class="align-top text-center">{{ $item->disc > 0 ? $item->disc . '%' : '-' }}</td>
                        @endif
                        <td class="align-top text-end">{{ number_format($item->amount, 0, '', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Note + Financial Summary (di luar tabel) --}}
        @php
            $afterDisc = $quote->diskon > 0
                ? $quote->subtotal - ($quote->subtotal * $quote->diskon / 100)
                : $quote->subtotal;
        @endphp
        <div class="d-flex justify-content-between align-items-start mt-3 mb-4" style="gap:16px;">

            {{-- Note (kiri) --}}
            <div style="flex:1; font-size:12px;">
                @if ($quote->note)
                    <p class="mb-1 fw-semibold" style="font-size:11px; color:#888; text-transform:uppercase; letter-spacing:.4px;">Note</p>
                    <p class="mb-0" style="white-space:pre-wrap; color:#333;">{{ $quote->note }}</p>
                @endif
            </div>

            {{-- Financial Summary (kanan) --}}
            <div style="min-width:240px; font-size:12px;">
                <table style="width:100%; border-collapse:collapse;">
                    <tr>
                        <td style="padding:3px 8px 3px 0; color:#555;">Subtotal</td>
                        <td style="padding:3px 0; text-align:right; font-weight:500;">Rp {{ number_format($quote->subtotal, 0, '', '.') }}</td>
                    </tr>
                    @if ($quote->diskon > 0)
                        <tr>
                            <td style="padding:3px 8px 3px 0; color:#555;">Discount {{ $quote->diskon }}%</td>
                            <td style="padding:3px 0; text-align:right; font-weight:500;">- Rp {{ number_format($quote->subtotal * $quote->diskon / 100, 0, '', '.') }}</td>
                        </tr>
                        <tr>
                            <td style="padding:3px 8px 3px 0; color:#555;">After Discount</td>
                            <td style="padding:3px 0; text-align:right; font-weight:500;">Rp {{ number_format($afterDisc, 0, '', '.') }}</td>
                        </tr>
                    @endif
                    <tr>
                        <td style="padding:3px 8px 3px 0; color:#555;">Tax {{ $quote->tax ? '(11%)' : '' }}</td>
                        <td style="padding:3px 0; text-align:right; font-weight:500;">
                            {{ $quote->tax ? 'Rp ' . number_format($quote->tax_amount, 0, '', '.') : '-' }}
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="padding:4px 0 0;"><hr style="margin:0; border-color:#ccc;"></td>
                    </tr>
                    <tr style="background:#FFF9C4;">
                        <td style="padding:6px 8px 6px 6px; font-weight:700; font-size:13px; color:#333;">TOTAL PRICE</td>
                        <td style="padding:6px 6px 6px 0; text-align:right; font-weight:700; font-size:13px; color:#333;">Rp {{ number_format($quote->total, 0, '', '.') }}</td>
                    </tr>
                </table>
            </div>
        </div>

        {{-- T&C --}}
        <div class="mb-4" style="font-size:12px;">
            <p class="mb-2 fw-semibold" style="font-size:11px; text-transform:uppercase; letter-spacing:.5px; color:#888;">Term & Condition</p>
            <table style="width:100%; border-collapse:collapse;">
                <tr>
                    <td style="width:160px; padding:2px 0; color:#555; vertical-align:top;">Validity of Quotation</td>
                    <td style="padding:2px 0; vertical-align:top;">: {{ $quote->validity ?? '-' }}</td>
                </tr>
                <tr>
                    <td style="padding:2px 0; color:#555; vertical-align:top;">Price</td>
                    <td style="padding:2px 0; vertical-align:top;">: {{ $quote->pricing ?? '-' }}</td>
                </tr>
                <tr>
                    <td style="padding:2px 0; color:#555; vertical-align:top;">Delivery Process</td>
                    <td style="padding:2px 0; vertical-align:top;">: {{ $quote->delivery_process ?? '-' }}</td>
                </tr>
                <tr>
                    <td style="padding:2px 0; color:#555; vertical-align:top;">Payment</td>
                    <td style="padding:2px 0; vertical-align:top;">: {{ $quote->payment ?? '-' }}</td>
                </tr>
            </table>
        </div>

        {{-- Footer --}}
        <hr class="my-3">
        <div class="d-flex justify-content-between align-items-center" style="font-size:12px; color:#555;">
            <div>
                <p class="mb-0">For further inquiries, please contact:</p>
                <p class="mb-0 fw-semibold" style="color:#333;">{{ $quote->sales?->name }}</p>
                @if ($quote->sales?->phone)
                    <p class="mb-0">{{ $quote->sales->phone }}</p>
                @endif
            </div>
            <div class="text-end" style="font-size:11px; color:#aaa;">
                <p class="mb-0">PT Reftech Jaya Optima</p>
                <p class="mb-0">{{ $quote->date?->format('d F Y') }}</p>
            </div>
        </div>

    </div>
</div>

@push('after-style')
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/css/pages/app-invoice-print.css" />
@endpush
@push('after-script')
    <script src="{{ asset('assets') }}/js/app-invoice-print.js"></script>
@endpush
