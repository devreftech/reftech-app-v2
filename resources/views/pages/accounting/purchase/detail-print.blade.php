@extends('layouts.sales.app')
@section('title', 'Purchase Order - ' . $purchase->no_po)
@php
    $totalPph = $totalPph ?? 0;
    $hasDisc = $dPurchase->contains(fn($item) => $item->disc > 0);
@endphp
<div class="invoice-print p-4">
    <div class="container-fluid flex-grow-1">

        {{-- Header --}}
        <div class="d-flex justify-content-between flex-xl-row flex-md-column flex-sm-row flex-column gap-3 mb-0">
            @if ($purchase->vat > 0)
                <div class="mb-xl-0 pb-1">
                    <div class="d-flex svg-illustration align-items-center gap-2 mb-3">
                        <span class="app-brand-logo demo">
                            <img src="{{ asset('/asset') }}/logo/Reftech-Log.png" alt="Reftech Logo" width="180">
                        </span>
                    </div>
                    <div class="d-flex flex-row align-items-start gap-4 mt-2" style="font-size: 11px;">
                        <div class="info" style="max-width: 260px;">
                            <p class="mb-1 fw-bold text-dark" style="font-size: 11.5px;">
                                <i class="mdi mdi-office-building-outline me-1 text-primary"></i>ALAMAT KANTOR
                            </p>
                            <p class="mb-1 text-muted" style="line-height: 1.4;">Taman Kopo Indah V, Soho Sommerville No. 31, Bandung – Jawa Barat 40218</p>
                            <p class="mb-0 text-muted">
                                <i class="mdi mdi-phone-outline me-1 text-primary"></i>022 54417653 &nbsp;|&nbsp; <i class="mdi mdi-email-outline me-1 text-primary"></i>info@reftech.id
                            </p>
                        </div>
                        <div class="npwp_add" style="max-width: 280px;">
                            <p class="mb-1 fw-bold text-dark" style="font-size: 11.5px;">
                                <i class="mdi mdi-file-document-outline me-1 text-primary"></i>ALAMAT NPWP
                            </p>
                            <p class="mb-1 text-muted" style="line-height: 1.4;">Komp. Negia Kencana Residence Blok B, No.2 Pasanggrahan, Ujung Berung Kota Bandung - Jawa Barat 40199</p>
                            <div class="px-2 py-0.5 rounded-0" style="background:#eef0ff; border:1px solid #d0d0ff; font-size:10.5px; font-weight:600; color:#3d3d8f; display:inline-block; border-radius:0 !important;">
                                <i class="mdi mdi-card-account-details-outline me-1"></i>NPWP: 0737285718429000
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="mb-xl-0 pb-1">
                    <div class="d-flex svg-illustration align-items-center gap-2 mb-3">
                        <span class="app-brand-logo demo">
                            <img src="{{ asset('/asset') }}/logo/Reftech-Log.png" alt="Reftech Logo" width="180">
                        </span>
                    </div>
                    <p class="mb-1 fw-bold text-dark" style="font-size:14px;">PT Reftech Jaya Optima</p>
                    <p class="mb-1 text-muted" style="font-size:11.5px; line-height:1.4;">Taman Kopo Indah V, Soho Sommerville No. 31, Bandung – Jawa Barat 40218</p>
                    <p class="mb-0 text-muted" style="font-size:11.5px;">
                        <i class="mdi mdi-phone-outline me-1 text-primary"></i>022 54417653 &nbsp;|&nbsp; <i class="mdi mdi-email-outline me-1 text-primary"></i>info@reftech.id
                    </p>
                </div>
            @endif

            <div class="text-end" style="white-space: nowrap; flex-shrink: 0;">
                <h1 class="fw-bold invoice-title-heading text-nowrap" style="color: #2529fa; letter-spacing: 1.5px; white-space: nowrap; font-size: 22px; margin-bottom: 4px;">PURCHASE ORDER</h1>
                <p class="mb-1 fw-bold text-dark text-nowrap" style="font-size:14px; white-space: nowrap;">#{{ $purchase->no_po }}</p>
                <p class="mb-1 text-muted small text-nowrap" style="white-space: nowrap;">
                    <i class="mdi mdi-calendar-blank-outline me-1 text-primary"></i>{{ $purchase->date ? Carbon\Carbon::parse($purchase->date)->format('d-m-Y') : '-' }}
                </p>
            </div>
        </div>

        {{-- Accent Divider --}}
        <div style="height:2px; background:linear-gradient(90deg,#696cff 0%,#9c9eff 60%,#e0e0e0 100%); border-radius:2px; margin:16px 0 18px;"></div>

        {{-- Vendor / Supplier + Ship To Boxes --}}
        <div style="display:flex !important; align-items:stretch !important; gap:14px; margin-bottom:18px; font-size:12px;">
            {{-- Card 1: Vendor / Supplier --}}
            <div style="flex:1.4; display:flex; flex-direction:column; align-self:stretch; border:1px solid #e0e0e0; border-left:4px solid #696cff; border-radius:4px; padding:12px 16px; background:#fcfcfc;">
                <div class="d-flex justify-content-between align-items-center mb-2 pb-1" style="border-bottom: 1px dashed #e4e4e4;">
                    <span class="fw-bold text-uppercase" style="font-size:10.5px; letter-spacing:0.6px; color:#696cff;">
                        <i class="mdi mdi-domain me-1"></i>Vendor / Supplier
                    </span>
                </div>

                <p class="mb-2 fw-bold text-dark" style="font-size:14px; line-height:1.3;">
                    {{ $purchase->company ?: '-' }}
                </p>

                <div style="display:grid; grid-template-columns: auto 1fr; gap:4px 12px; font-size:11.5px; color:#333;">
                    @if ($purchase->attn)
                        <span class="text-muted" style="white-space:nowrap;"><i class="mdi mdi-account-outline me-1 text-primary"></i>Attn</span>
                        <span class="fw-medium text-dark">: {{ $purchase->attn }}</span>
                    @endif

                    @if ($purchase->phone || $purchase->mobile)
                        <span class="text-muted" style="white-space:nowrap;"><i class="mdi mdi-phone-in-talk-outline me-1 text-primary"></i>Phone</span>
                        <span class="fw-medium text-dark">: {{ $purchase->phone ?: $purchase->mobile }}</span>
                    @endif

                    @if ($purchase->email)
                        <span class="text-muted" style="white-space:nowrap;"><i class="mdi mdi-email-outline me-1 text-primary"></i>Email</span>
                        <span class="fw-medium text-dark">: {{ $purchase->email }}</span>
                    @endif

                    @if ($purchase->address)
                        <span class="text-muted" style="white-space:nowrap;"><i class="mdi mdi-map-marker-outline me-1 text-primary"></i>Address</span>
                        <div class="fw-medium text-dark" style="line-height:1.4; display:flex; align-items:flex-start;">
                            <span style="flex-shrink:0; margin-right:4px;">:</span>
                            <span style="flex:1;">{{ $purchase->address }}</span>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Card 2: Ship To & Terms --}}
            <div style="min-width:240px; flex:1; display:flex; flex-direction:column; align-self:stretch; border:1px solid #e0e0e0; border-left:4px solid #8592a3; border-radius:4px; padding:12px 16px; background:#fcfcfc;">
                <div class="mb-2 pb-1" style="border-bottom: 1px dashed #e4e4e4;">
                    <span class="fw-bold text-uppercase" style="font-size:10.5px; letter-spacing:0.6px; color:#566a7f;">
                        <i class="mdi mdi-truck-delivery-outline me-1"></i>Ship To & Terms
                    </span>
                </div>

                <div style="font-size:11.5px; color:#333;" class="my-auto">
                    <div class="d-flex align-items-center mb-2 pb-1" style="border-bottom:1px dashed #f0f0f0;">
                        <span class="text-muted" style="min-width:65px;"><i class="mdi mdi-office-building-outline me-1 text-primary"></i>Ship To</span>
                        <span class="fw-bold text-dark">: PT Reftech Jaya Optima</span>
                    </div>
                    <div class="d-flex align-items-center mb-2 pb-1" style="border-bottom:1px dashed #f0f0f0;">
                        <span class="text-muted" style="min-width:65px;"><i class="mdi mdi-truck-outline me-1 text-primary"></i>Delivery</span>
                        <span class="fw-medium text-dark">: {{ $purchase->delivery ?: '-' }}</span>
                    </div>
                    <div class="d-flex align-items-center">
                        <span class="text-muted" style="min-width:65px;"><i class="mdi mdi-credit-card-outline me-1 text-primary"></i>Payment</span>
                        <span class="fw-medium text-dark">: {{ $purchase->payment ?: '-' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <p class="mb-2" style="font-size:11.5px; color:#666; font-style:italic;">
            Dear Sir/Madam, Please find below our official Purchase Order for the following items :
        </p>

        {{-- Items Table --}}
        <div>
            <table class="table table-bordered items-top-align-table m-0" style="border: 1px solid rgb(60,60,60); width: 100%;">
                <thead class="table-light">
                    <tr>
                        <th class="text-center align-middle" style="width:5%; font-weight:700;">No.</th>
                        <th class="text-center align-middle" style="width:45%; font-weight:700;">Item Description</th>
                        <th class="text-center align-middle" style="width:12%; font-weight:700; white-space:nowrap;">Qty</th>
                        <th class="text-center align-middle" style="width:18%; font-weight:700; white-space:nowrap;">Price (IDR)</th>
                        @if ($hasDisc)
                            <th class="text-center align-middle" style="width:7%; font-weight:700;">Disc</th>
                        @endif
                        <th class="text-center align-middle" style="width:13%; font-weight:700; white-space:nowrap;">Amount (IDR)</th>
                    </tr>
                </thead>
                <tbody>
                    @php $no = 0; @endphp
                    @foreach ($dPurchase as $product)
                        @php $no++; @endphp
                        <tr style="font-size: 12.5px;">
                            <td class="text-center align-top py-2">{{ $no }}</td>
                            <td class="align-top py-2">
                                <p class="mb-0 fw-semibold text-dark" style="font-size: 12px;">{{ $product->product }}</p>
                            </td>
                            <td class="text-center align-top py-2 text-dark"><span class="fw-bold">{{ $product->qty }}</span> {{ $product->info_qty }}</td>
                            <td class="text-end align-top py-2 text-nowrap text-dark" style="white-space:nowrap;">{{ number_format($product->price, 0, '', '.') }}</td>
                            @if ($hasDisc)
                                <td class="text-center align-top py-2 text-dark">{{ $product->disc ? $product->disc . '%' : '-' }}</td>
                            @endif
                            <td class="text-end align-top py-2 fw-semibold text-nowrap text-dark" style="white-space:nowrap;">{{ number_format($product->amount, 0, '', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Note + Summary --}}
        @php
            $tax = ($purchase->total * 11) / 100;
            $noTax = $purchase->total - ($purchase->total * 11) / 100;
            $dpp = ($noTax * 11) / 12;
        @endphp
        <div class="row g-3 mt-1 mb-4">
            <div class="col-6">
                <div class="p-3 h-100" style="border:1px solid #e0e0e0; border-left:4px solid #696cff; border-radius:4px; background:#fcfcfc; font-size:12px;">
                    <p class="fw-bold mb-1 text-uppercase" style="font-size:10.5px; letter-spacing:.5px; color:#696cff;">
                        <i class="mdi mdi-note-text-outline me-1"></i> Note / Catatan
                    </p>
                    <p class="mb-0 text-dark" style="font-style:italic;">{{ $purchase->note ?: 'Tidak ada catatan khusus.' }}</p>
                </div>
            </div>
            <div class="col-6">
                <div class="table-responsive">
                    <table class="table table-bordered m-0" style="font-size: 12px; border-color: #c5c5c5;">
                        <tbody>
                            <tr>
                                <td class="text-end fw-semibold text-uppercase py-1_5 px-3" style="border-color: #c5c5c5; background: #ffffff; color: #333; vertical-align: middle;">SUBTOTAL</td>
                                <td class="py-1_5 px-3" style="border-color: #c5c5c5; background: #ffffff; color: #111; vertical-align: middle; width: 55%;">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span>Rp</span>
                                        <span class="fw-semibold">{{ number_format($purchase->subtotal, 0, '', '.') }}</span>
                                    </div>
                                </td>
                            </tr>
                            @if ($purchase->diskon > 0)
                                <tr>
                                    <td class="text-end fw-semibold text-uppercase py-1_5 px-3" style="border-color: #c5c5c5; background: #ffffff; color: #333; vertical-align: middle;">DISCOUNT</td>
                                    <td class="py-1_5 px-3 text-danger" style="border-color: #c5c5c5; background: #ffffff; vertical-align: middle;">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span>- Rp</span>
                                            <span class="fw-semibold">{{ number_format($purchase->diskon, 0, '', '.') }}</span>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                            @if ($purchase->vat > 0)
                                <tr>
                                    <td class="text-end fw-semibold text-uppercase py-1_5 px-3" style="border-color: #c5c5c5; background: #ffffff; color: #333; vertical-align: middle;">DPP NILAI LAIN</td>
                                    <td class="py-1_5 px-3" style="border-color: #c5c5c5; background: #ffffff; color: #111; vertical-align: middle;">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span>Rp</span>
                                            <span class="fw-semibold">{{ $dpp == '0' ? '0' : number_format($dpp, 0, '', '.') }}</span>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-end fw-semibold text-uppercase py-1_5 px-3" style="border-color: #c5c5c5; background: #ffffff; color: #333; vertical-align: middle;">VAT 12%</td>
                                    <td class="py-1_5 px-3" style="border-color: #c5c5c5; background: #ffffff; color: #111; vertical-align: middle;">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span>Rp</span>
                                            <span class="fw-semibold">{{ $tax == '0' ? '0' : number_format($tax, 0, '', '.') }}</span>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                            @if ($purchase->delivery_cost > 0)
                                <tr>
                                    <td class="text-end fw-semibold text-uppercase py-1_5 px-3" style="border-color: #c5c5c5; background: #ffffff; color: #333; vertical-align: middle;">DELIVERY COST</td>
                                    <td class="py-1_5 px-3" style="border-color: #c5c5c5; background: #ffffff; color: #111; vertical-align: middle;">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span>Rp</span>
                                            <span class="fw-semibold">{{ number_format($purchase->delivery_cost, 0, '', '.') }}</span>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                            @if ($totalPph > 0)
                                <tr>
                                    <td class="text-end fw-semibold text-uppercase py-1_5 px-3" style="border-color: #c5c5c5; background: #ffffff; color: #333; vertical-align: middle;">TOTAL PPH</td>
                                    <td class="py-1_5 px-3 text-danger" style="border-color: #c5c5c5; background: #ffffff; vertical-align: middle;">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span>- Rp</span>
                                            <span class="fw-semibold">{{ number_format($totalPph, 0, '', '.') }}</span>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                            <tr style="background: yellow; border-top: 2px solid #e6c300;">
                                <td class="text-end fw-bolder text-uppercase py-2 px-3" style="border-color: #c5c5c5; background: yellow !important; color: #000; font-size: 13px; vertical-align: middle;">TOTAL PRICE</td>
                                <td class="py-2 px-3 fw-bold" style="border-color: #c5c5c5; background: yellow !important; color: #000; font-size: 13px; vertical-align: middle;">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="fw-bold">Rp</span>
                                        <span class="fw-bold fs-6">{{ $purchase->total == '0' ? '0' : number_format($purchase->total, 0, '', '.') }}</span>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Signatures --}}
        <div class="row pt-3 text-center" style="font-size:12px;">
            <div class="col-6">
                <p class="fw-bold mb-1" style="color:#333;">Authorized By,</p>
                <div class="my-1 d-flex justify-content-center align-items-center" style="height:70px;">
                    <img src="{{ url('') . '/asset/sign/ttdAngel.jpg' }}" alt="TTD Angel" height="70" style="width:auto !important;height:70px;">
                </div>
                <p class="fw-bold mb-0" style="color:#111; border-bottom:1px solid #ddd; display:inline-block; padding-bottom:2px;">PT Reftech Jaya Optima</p>
            </div>
            <div class="col-6">
                <p class="fw-bold mb-1" style="color:#333;">Accepted By Vendor,</p>
                <div class="my-1" style="height:70px;"></div>
                <p class="fw-bold mb-0" style="color:#111; border-bottom:1px solid #ddd; display:inline-block; padding-bottom:2px;">{{ $purchase->attn ?: '-' }}</p>
                <p class="text-muted mb-0" style="font-size:11px;">{{ $purchase->company }}</p>
            </div>
        </div>
    </div>
</div>
@push('after-style')
    <!-- Page CSS -->
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/css/pages/app-invoice-print-header.css" />
    <style>
        .invoice-print .text-end h1.invoice-title-heading { color: #2529fa !important; }
        table.items-top-align-table tbody td {
            vertical-align: top !important;
        }
        @media print {
            @page {
                size: A4 portrait !important;
                margin: 10mm 12mm 10mm 12mm !important;
            }
            .invoice-print {
                padding-top: 15px !important;
            }
            .invoice-print .text-end h1.invoice-title-heading { color: #2529fa !important; }
            .invoice-print div { overflow: visible !important; }
            .invoice-print table { width: 100% !important; }
            .invoice-print td, .invoice-print th { overflow-wrap: break-word !important; }
            .invoice-print table td { color: #333 !important; }
        }

        @media screen {
            .invoice-print {
                margin-top: 24px !important;
                padding-top: 24px !important;
            }
            .invoice-print table td { color: #333 !important; }
        }
    </style>
@endpush
@push('after-script')
    <script src="{{ asset('assets') }}/js/app-invoice-print.js"></script>
@endpush
