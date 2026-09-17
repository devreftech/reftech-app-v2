@extends('layouts.sales.app')
@section('title', 'Purchase Order - ' . $purchase->no_po)
@php
    $totalPph = $totalPph ?? 0;
    $hasDisc = $dPurchase->contains(fn($item) => $item->disc > 0);
    $isKojisha = method_exists($purchase, 'isKojisha') ? $purchase->isKojisha() : (str_contains($purchase->no_po ?? '', 'KII') || stripos($purchase->note ?? '', 'Kojisha') !== false);
    $themeColor = $isKojisha ? '#e11d48' : '#2529fa';
    $themeGradient = $isKojisha ? 'linear-gradient(90deg,#e11d48 0%,#fb7185 60%,#e0e0e0 100%)' : 'linear-gradient(90deg,#696cff 0%,#9c9eff 60%,#e0e0e0 100%)';
    $themeBorder = $isKojisha ? '#e11d48' : '#696cff';
@endphp
<div class="invoice-print p-4">
    <div class="container-fluid flex-grow-1">

        {{-- Header --}}
        <div class="d-flex justify-content-between flex-xl-row flex-md-column flex-sm-row flex-column gap-3 mb-0">
            @if ($isKojisha)
                <div class="mb-xl-0 pb-1">
                    <div class="d-flex svg-illustration align-items-center gap-2 mb-3">
                        <span class="app-brand-logo demo">
                            <img src="{{ asset('/asset') }}/logo/Kojisha-Log.png" alt="Kojisha Logo" width="160">
                        </span>
                    </div>
                    <p class="mb-1 fw-bold text-dark" style="font-size:14px;">PT Kojisha Innotiv Indonesia</p>
                    <p class="mb-1 text-muted" style="font-size:11.5px; line-height:1.4;">Jl. Nancep No. 45A, Setu, Cibitung - Kab. Bekasi 17320</p>
                    <p class="mb-0 text-muted" style="font-size:11.5px;">
                        <i class="mdi mdi-phone-outline me-1" style="color:#e11d48;"></i>+62 812-1000-0997 &nbsp;|&nbsp; <i class="mdi mdi-email-outline me-1" style="color:#e11d48;"></i>admin@kojisha.com
                    </p>
                </div>
            @elseif ($purchase->vat > 0)
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
                            <p class="mb-1 text-muted" style="line-height: 1.4;">Komp. Negla Kencana Residence Blok B, No.2 Pasanggrahan, Ujung Berung Kota Bandung - Jawa Barat 40199</p>
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
                <h1 class="fw-bold invoice-title-heading text-nowrap" style="color: {{ $themeColor }}; letter-spacing: 1.5px; white-space: nowrap; font-size: 22px; margin-bottom: 4px;">PURCHASE ORDER</h1>
                <p class="mb-1 fw-bold text-dark text-nowrap" style="font-size:14px; white-space: nowrap;">#{{ $purchase->no_po }}</p>
                @if ($purchase->no_reference)
                    <p class="mb-1 text-muted small text-nowrap" style="white-space: nowrap; font-size:11.5px;">
                        <span class="fw-semibold text-dark">Ref:</span> {{ $purchase->no_reference }}
                    </p>
                @endif
                <p class="mb-1 text-muted small text-nowrap" style="white-space: nowrap;">
                    <i class="mdi mdi-calendar-blank-outline me-1" style="color: {{ $themeColor }};"></i>{{ $purchase->date ? Carbon\Carbon::parse($purchase->date)->format('d-m-Y') : '-' }}
                </p>
            </div>
        </div>

        {{-- Accent Divider --}}
        <div style="height:2px; background: {{ $themeGradient }}; border-radius:2px; margin:16px 0 18px;"></div>

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

                    @if ($purchase->mobile || $purchase->phone)
                        <span class="text-muted" style="white-space:nowrap;"><i class="mdi mdi-phone-in-talk-outline me-1 text-primary"></i>Phone</span>
                        <span class="fw-medium text-dark">: {{ $purchase->mobile ?: $purchase->phone }}</span>
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

            {{-- Card 2: Term & Info --}}
            <div style="min-width:260px; max-width:400px; flex:1; display:flex; flex-direction:column; align-self:stretch; border:1px solid #e0e0e0; border-left:4px solid #696cff; border-radius:4px; padding:12px 16px; background:#fcfcfc;">
                <div class="d-flex justify-content-between align-items-center mb-2 pb-1" style="border-bottom: 1px dashed #e4e4e4;">
                    <span class="fw-bold text-uppercase" style="font-size:10.5px; letter-spacing:0.6px; color:#696cff;">
                        <i class="mdi mdi-information-outline me-1"></i>Term & Info
                    </span>
                </div>

                <div style="display:grid; grid-template-columns: auto 1fr; gap:4px 12px; font-size:11.5px; color:#333;">
                    <span class="text-muted" style="white-space:nowrap;"><i class="mdi mdi-pound me-1 text-primary"></i>No. Reference</span>
                    <span class="fw-medium text-dark">: {{ $purchase->no_reference ?: '-' }}</span>

                    <span class="text-muted" style="white-space:nowrap;"><i class="mdi mdi-truck-outline me-1 text-primary"></i>Delivery</span>
                    <span class="fw-medium text-dark">: {{ $purchase->delivery ?: '-' }}</span>

                    <span class="text-muted" style="white-space:nowrap;"><i class="mdi mdi-credit-card-outline me-1 text-primary"></i>Payment</span>
                    <span class="fw-medium text-dark">: {{ $purchase->payment ?: '-' }}</span>
                </div>
            </div>
        </div>

        <p class="mb-2" style="font-size:11.5px; color:#666; font-style:italic;">
            Dear Sir/Madam, Please find below our official Purchase Order for the following items :
        </p>

        {{-- Items Table --}}
        <div class="mb-3">
            <table class="table table-bordered items-top-align-table m-0" style="width: 100%; border-collapse: collapse;">
                <thead style="font-size:11px; background:#eeeeff; color:#3d3d8f; -webkit-print-color-adjust:exact; print-color-adjust:exact;">
                    <tr>
                        <th class="text-center align-middle" style="width:5%; font-weight:700;">No.</th>
                        <th class="text-center align-middle" style="width:45%; font-weight:700;">Item Description</th>
                        <th class="text-center align-middle" style="width:{{ $hasDisc ? '10%' : '11%' }}; font-weight:700; white-space:nowrap;">Qty</th>
                        <th class="text-center align-middle" style="width:{{ $hasDisc ? '16%' : '17%' }}; font-weight:700; white-space:nowrap;">Price (IDR)</th>
                        @if ($hasDisc)
                            <th class="text-center align-middle" style="width:7%; font-weight:700;">Disc</th>
                        @endif
                        <th class="text-center align-middle" style="width:{{ $hasDisc ? '17%' : '22%' }}; font-weight:700; white-space:nowrap;">Amount (IDR)</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $itemNo = 1;
                        $headerCount = 0;
                    @endphp
                    @foreach ($dPurchase as $product)
                        @if (($product->category ?? '') === 'Header')
                            @php
                                $lbl = trim($product->product ?? '');
                                if (!preg_match('/^[A-Z0-9][\.\)]/i', $lbl)) {
                                    $lbl = chr(65 + ($headerCount % 26)) . '. ' . $lbl;
                                }
                                $headerCount++;
                            @endphp
                            <tr style="background:#f4f5fa !important; -webkit-print-color-adjust:exact; print-color-adjust:exact;">
                                <td colspan="{{ $hasDisc ? '6' : '5' }}" class="fw-bold text-dark text-uppercase px-3" style="padding: 5px 12px; font-size:11.5px; border-left:3px solid #696cff !important;">
                                    <i class="mdi mdi-bookmark-outline text-primary me-1"></i>{{ $lbl }}
                                </td>
                            </tr>
                        @else
                            <tr class="compact-item-row">
                                <td class="text-center align-top px-2">{{ $itemNo++ }}</td>
                                <td class="align-top px-2">
                                    <p class="mb-0 fw-semibold text-dark" style="word-break: break-word; line-height: 1.35;">{!! nl2br(e($product->product)) !!}</p>
                                </td>
                                <td class="text-center align-top px-2 text-dark"><span class="fw-bold">{{ $product->qty }}</span> {{ $product->info_qty }}</td>
                                <td class="text-end align-top px-2 text-nowrap text-dark" style="white-space:nowrap;">{{ fmod($product->price, 1) != 0 ? number_format($product->price, 2, ',', '.') : number_format($product->price, 0, '', '.') }}</td>
                                @if ($hasDisc)
                                    <td class="text-center align-top px-2 text-dark">{{ $product->disc ? $product->disc . '%' : '-' }}</td>
                                @endif
                                <td class="text-end align-top px-2 fw-semibold text-nowrap text-dark" style="white-space:nowrap;">{{ number_format($product->amount, 0, '', '.') }}</td>
                            </tr>
                        @endif
                    @endforeach
                    {{-- Finance Summary Rows --}}
                    @php
                        $hargaSebelumPpn = ($purchase->subtotal ?? 0) - ($purchase->diskon ?? 0);
                        $dpp = isset($dpp) && $dpp > 0 ? $dpp : (($purchase->vat ?? 0) > 0 ? round(($hargaSebelumPpn * 11) / 12) : 0);
                        $tax = isset($tax) && $tax > 0 ? $tax : (($purchase->vat ?? 0) > 0 ? round(($dpp * 12) / 100) : 0);
                        $summaryRows = 2 + ($purchase->diskon > 0 ? 2 : 0) + ($purchase->vat > 0 ? 2 : 0) + ($totalPph > 0 ? 1 : 0);
                    @endphp
                    <tr class="compact-item-row">
                        <td colspan="2" rowspan="{{ $summaryRows }}" class="summary-empty-space" style="border-left: hidden !important; border-bottom: hidden !important; border-top: 1px solid rgb(60,60,60) !important; border-right: 1px solid rgb(60,60,60) !important; background: #ffffff !important; padding: 0 !important;"></td>
                        <td colspan="{{ $hasDisc ? '3' : '2' }}" class="text-end fw-semibold text-uppercase px-2" style="border: 1px solid rgb(60,60,60); background: #ffffff; color: #333; vertical-align: middle;">
                            SUBTOTAL
                        </td>
                        <td class="px-2" style="border: 1px solid rgb(60,60,60); background: #ffffff; color: #111; vertical-align: middle;">
                            <div class="d-flex justify-content-between align-items-center">
                                <span>Rp</span>
                                <span class="fw-semibold">{{ number_format($purchase->subtotal, 0, '', '.') }}</span>
                            </div>
                        </td>
                    </tr>
                    @if ($purchase->diskon > 0)
                        <tr class="compact-item-row">
                            <td colspan="{{ $hasDisc ? '3' : '2' }}" class="text-end fw-semibold text-uppercase px-2" style="border: 1px solid rgb(60,60,60); background: #ffffff; color: #333; vertical-align: middle;">
                                DISCOUNT
                            </td>
                            <td class="px-2 text-danger" style="border: 1px solid rgb(60,60,60); background: #ffffff; vertical-align: middle;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span>- Rp</span>
                                    <span class="fw-semibold">{{ number_format($purchase->diskon, 0, '', '.') }}</span>
                                </div>
                            </td>
                        </tr>
                        <tr class="compact-item-row">
                            <td colspan="{{ $hasDisc ? '3' : '2' }}" class="text-end fw-semibold text-uppercase px-2" style="border: 1px solid rgb(60,60,60); background: #ffffff; color: #333; vertical-align: middle;">
                                Subtotal After Disc.
                            </td>
                            <td class="px-2" style="border: 1px solid rgb(60,60,60); background: #ffffff; color: #111; vertical-align: middle;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span>Rp</span>
                                    <span class="fw-semibold">{{ number_format($purchase->subtotal - $purchase->diskon, 0, '', '.') }}</span>
                                </div>
                            </td>
                        </tr>
                    @endif
                    @if ($purchase->vat > 0)
                        <tr class="compact-item-row">
                            <td colspan="{{ $hasDisc ? '3' : '2' }}" class="text-end fw-semibold text-uppercase px-2" style="border: 1px solid rgb(60,60,60); background: #ffffff; color: #333; vertical-align: middle;">
                                DPP NILAI LAIN
                            </td>
                            <td class="px-2" style="border: 1px solid rgb(60,60,60); background: #ffffff; color: #111; vertical-align: middle;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span>Rp</span>
                                    <span class="fw-semibold">{{ $dpp == '0' ? '0' : number_format($dpp, 0, '', '.') }}</span>
                                </div>
                            </td>
                        </tr>
                        <tr class="compact-item-row">
                            <td colspan="{{ $hasDisc ? '3' : '2' }}" class="text-end fw-semibold text-uppercase px-2" style="border: 1px solid rgb(60,60,60); background: #ffffff; color: #333; vertical-align: middle;">
                                PPN 12%
                            </td>
                            <td class="px-2" style="border: 1px solid rgb(60,60,60); background: #ffffff; color: #111; vertical-align: middle;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span>Rp</span>
                                    <span class="fw-semibold">{{ $tax == '0' ? '0' : number_format($tax, 0, '', '.') }}</span>
                                </div>
                            </td>
                        </tr>
                    @endif
                    @if ($totalPph > 0)
                        <tr class="compact-item-row">
                            <td colspan="{{ $hasDisc ? '3' : '2' }}" class="text-end fw-semibold text-uppercase px-2" style="border: 1px solid rgb(60,60,60); background: #ffffff; color: #333; vertical-align: middle;">
                                TOTAL PPH
                            </td>
                            <td class="px-2 text-danger" style="border: 1px solid rgb(60,60,60); background: #ffffff; vertical-align: middle;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span>- Rp</span>
                                    <span class="fw-semibold">{{ number_format($totalPph, 0, '', '.') }}</span>
                                </div>
                            </td>
                        </tr>
                    @endif
                    <tr class="compact-item-row">
                        <td colspan="{{ $hasDisc ? '3' : '2' }}" class="text-end fw-bolder text-uppercase px-2" style="border: 1px solid rgb(60,60,60); background: yellow !important; color: #000; font-size: 13px; vertical-align: middle;">
                            TOTAL PRICE
                        </td>
                        <td class="px-2 fw-bold" style="border: 1px solid rgb(60,60,60); background: yellow !important; color: #000; font-size: 13px; vertical-align: middle;">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-bold">Rp</span>
                                <span class="fw-bold">{{ $purchase->total == '0' ? '0' : number_format($purchase->total, 0, '', '.') }}</span>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- Ship To / Franco Factory --}}
        @if ($purchase->ship_to)
            <div class="mb-2">
                <div class="p-3" style="border:1px solid #e0e0e0; border-left:4px solid #8592a3; border-radius:4px; background:#fcfcfc; font-size:12px;">
                    <p class="fw-bold mb-1 text-uppercase" style="font-size:10.5px; letter-spacing:.5px; color:#566a7f;">
                        <i class="mdi mdi-map-marker-outline me-1"></i> Ship To : Franco Factory
                    </p>
                    <p class="mb-0 text-dark fw-medium" style="line-height:1.4;">{{ $purchase->ship_to }}</p>
                </div>
            </div>
        @endif

        {{-- Note / Catatan diletakkan di bawah --}}
        @if (!empty($purchase->note) && trim($purchase->note) !== '-' && trim($purchase->note) !== '')
            <div class="mb-3">
                <div class="p-3" style="border:1px solid #e0e0e0; border-left:4px solid #696cff; border-radius:4px; background:#fcfcfc; font-size:12px;">
                    <p class="fw-bold mb-1 text-uppercase" style="font-size:10.5px; letter-spacing:.5px; color:#696cff;">
                        <i class="mdi mdi-note-text-outline me-1"></i> Note / Catatan
                    </p>
                    <p class="mb-0 text-dark" style="font-style:italic;">{{ $purchase->note }}</p>
                </div>
            </div>
        @endif

        {{-- Signatures --}}
        <div class="row pt-3 text-center" style="font-size:12px;">
            <div class="col-6">
                <p class="fw-bold mb-1" style="color:#333;">Authorized By,</p>
                @if ($isKojisha)
                    <div class="my-1 d-flex justify-content-center align-items-center" style="height:70px;">
                        {{-- TTD Kosong sementara untuk Kojisha --}}
                    </div>
                    <p class="fw-bold mb-0" style="color:#111;">PT Kojisha Innotiv Indonesia</p>
                    <div style="border-top: 1px solid #333; width: 200px; margin: 4px auto 3px auto;"></div>
                    <p class="text-muted mb-0" style="font-size:11px;">Dedeh Sulastri</p>
                @else
                    <div class="my-1 d-flex justify-content-center align-items-center" style="height:70px;">
                        <img src="{{ url('') . '/asset/sign/ttdAngel.jpg' }}" alt="TTD Angel" height="70" style="width:auto !important;height:70px;">
                    </div>
                    <p class="fw-bold mb-0" style="color:#111;">PT Reftech Jaya Optima</p>
                    <div style="border-top: 1px solid #333; width: 200px; margin: 4px auto 3px auto;"></div>
                    <p class="text-muted mb-0" style="font-size:11px;">Angel Irene</p>
                @endif
            </div>
            <div class="col-6">
                <p class="fw-bold mb-1" style="color:#333;">Accepted By Vendor,</p>
                <div class="my-1 d-flex justify-content-center align-items-center" style="height:70px;">
                    @if ($purchase->isSignedByVendor())
                        <div style="position: relative; display: inline-block;">
                            <img src="{{ asset($purchase->vendor_signature) }}" alt="TTD Vendor" height="70" style="width:auto !important; max-height:70px; object-fit:contain;">
                            @if ($purchase->vendor_signed_stamp)
                                <img src="{{ asset($purchase->vendor_signed_stamp) }}" alt="Stamp Vendor" style="position:absolute; top:-5px; right:-25px; max-height:50px; opacity:0.85; pointer-events:none;">
                            @endif
                        </div>
                    @else
                        <div style="height:70px;"></div>
                    @endif
                </div>
                @php
                    $isIndividual = ($purchase->supplier?->type === 'Individual') || ($purchase->type === 'Individual');
                    $hasAttn = !empty($purchase->attn) && trim($purchase->attn) !== '-';
                    if ($purchase->isSignedByVendor()) {
                        $picName = $purchase->vendor_signer_name;
                    } elseif ($isIndividual) {
                        $picName = $hasAttn ? $purchase->attn : ($purchase->company ?: ($purchase->supplier?->supplier ?: '-'));
                    } else {
                        $picName = $hasAttn ? $purchase->attn : '-';
                    }
                @endphp
                @if (!$isIndividual && !empty($purchase->company))
                    <p class="fw-bold mb-0" style="color:#111;">{{ $purchase->company }}</p>
                @endif
                <div style="border-top: 1px solid #333; width: 200px; margin: 4px auto 3px auto;"></div>
                <p class="text-muted mb-0" style="font-size:11px;">
                    {{ $picName }}
                    @if ($purchase->isSignedByVendor() && $purchase->vendor_signer_position)
                        <span>({{ $purchase->vendor_signer_position }})</span>
                    @endif
                </p>
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
        table.items-top-align-table tbody tr.compact-item-row td {
            padding-top: 7px !important;
            padding-bottom: 7px !important;
            font-size: 11.5px !important;
            line-height: 1.3 !important;
        }
        table.items-top-align-table tbody tr.compact-item-row td p {
            font-size: 11.5px !important;
            line-height: 1.3 !important;
            margin-bottom: 0 !important;
        }
        table.items-top-align-table {
            border: 1px solid rgb(60,60,60) !important;
            border-bottom: none !important;
            border-left: none !important;
        }
        table.items-top-align-table thead th:first-child,
        table.items-top-align-table tbody tr td:first-child:not(.summary-empty-space) {
            border-left: 1px solid rgb(60,60,60) !important;
        }
        .summary-empty-space {
            border-left: hidden !important;
            border-bottom: hidden !important;
            border-top: 1px solid rgb(60,60,60) !important;
            border-right: 1px solid rgb(60,60,60) !important;
            background: #ffffff !important;
            padding: 0 !important;
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
