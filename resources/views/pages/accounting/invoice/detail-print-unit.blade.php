@extends('layouts.sales.app')
@section('title', $invoice->no_invoice ?? 'Invoice Unit')
<div class="invoice-print p-4">
    <div class="container-fluid flex-grow-1 container-p-y">

        {{-- Header (Option 2: Office & NPWP Address) --}}
        <div class="d-flex justify-content-between flex-xl-row flex-md-column flex-sm-row flex-column {{ !$quote->tax ? 'justify-content-end' : '' }} gap-3 mb-0">
            @if ($quote->tax)
                <div class="mb-xl-0 pb-1">
                    <div class="d-flex svg-illustration align-items-center gap-2 mb-3">
                        <span class="app-brand-logo demo">
                            <img src="{{ asset('/asset') }}/logo/Reftech-Log.png" alt="Reftech Logo" width="180">
                        </span>
                    </div>
                    <div class="d-flex flex-row align-items-start gap-4 mt-2" style="font-size: 11px;">
                        <div class="info" style="max-width: 260px;">
                            <p class="mb-1 fw-bold text-dark" style="font-size: 11.5px;">
                                <i class="mdi mdi-office-building-outline me-1 text-primary"></i><span class="i18n" data-en="OFFICE ADDRESS :">ALAMAT KANTOR</span>
                            </p>
                            <p class="mb-1 text-muted" style="line-height: 1.4;">Taman Kopo Indah V, Soho Sommerville No. 31, Bandung – Jawa Barat 40218</p>
                            <p class="mb-0 text-muted">
                                <i class="mdi mdi-phone-outline me-1 text-primary"></i>022 54417653 &nbsp;|&nbsp; <i class="mdi mdi-email-outline me-1 text-primary"></i>accounting@reftech.id
                            </p>
                        </div>
                        <div class="npwp_add" style="max-width: 280px;">
                            <p class="mb-1 fw-bold text-dark" style="font-size: 11.5px;">
                                <i class="mdi mdi-file-document-outline me-1 text-primary"></i><span class="i18n" data-en="NPWP ADDRESS :">ALAMAT NPWP</span>
                            </p>
                            <p class="mb-1 text-muted" style="line-height: 1.4;">Komp. Negia Kencana Residence Blok B, No.2 Pasanggrahan, Ujung Berung Kota Bandung - Jawa Barat 40199</p>
                            <div class="px-2 py-0.5 rounded-0" style="background:#eef0ff; border:1px solid #d0d0ff; font-size:10.5px; font-weight:600; color:#3d3d8f; display:inline-block; border-radius:0 !important;">
                                <i class="mdi mdi-card-account-details-outline me-1"></i>NPWP: 73.728.571.8-429.000
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
                    <p class="mb-0 text-muted" style="font-size:11px;">Taman Kopo Indah V, Soho Sommerville No. 31, Bandung – Jawa Barat 40218</p>
                </div>
            @endif

            <div class="text-end">
                <h1 class="fw-bold invoice-title-heading" style="color: #2529fa; letter-spacing: 2px;">INVOICE</h1>
                <p class="mb-1 fw-bold text-dark" style="font-size:14px;">#{{ $invoice->no_invoice }}</p>
                <p class="mb-1 text-muted small">{{ $invoice->date ? \Carbon\Carbon::parse($invoice->date)->format('d-m-Y') : '-' }}</p>
            </div>
        </div>

        <div style="height:2px; background:linear-gradient(90deg,#696cff 0%,#9c9eff 60%,#e0e0e0 100%); border-radius:2px; margin:16px 0 18px;"></div>

        {{-- Invoice To + Document Info Box --}}
        <div style="display:flex !important; align-items:stretch !important; gap:14px; margin-bottom:18px; font-size:12px;">
            {{-- Card 1: Invoice To --}}
            <div style="flex:1.4; display:flex; flex-direction:column; align-self:stretch; border:1px solid #e0e0e0; border-left:4px solid #696cff; border-radius:4px; padding:12px 16px; background:#fcfcfc;">
                <div class="d-flex justify-content-between align-items-center mb-2 pb-1" style="border-bottom: 1px dashed #e4e4e4;">
                    <span class="fw-bold text-uppercase" style="font-size:10.5px; letter-spacing:0.6px; color:#696cff;">
                        <i class="mdi mdi-domain me-1"></i>Invoice To
                    </span>
                    @if ($quote->client?->npwp)
                        <span class="px-2 py-0.5 rounded-0" style="font-size:10px; font-weight:600; background:#f0f2ff; color:#43497a; border:1px solid #d5d9ff;">
                            <i class="mdi mdi-card-account-details-outline me-1"></i>NPWP: {{ $quote->client->npwp }}
                        </span>
                    @endif
                </div>

                <p class="mb-2 fw-bold text-dark" style="font-size:14px; line-height:1.3;">
                    {{ $quote->client?->company ?? '-' }}
                </p>

                @php
                    $picName = $quote->pic?->name_pic ?? $quote->attn;
                    $targetAddress = $invoice->invoiceTo == '1' ? ($quote->client?->address ?? '-') : ($quote->client?->subAddress ?? '-');
                @endphp

                <div style="display:grid; grid-template-columns: auto 1fr; gap:4px 12px; font-size:11.5px; color:#333;">
                    @if ($picName)
                        <span class="text-muted" style="white-space:nowrap;"><i class="mdi mdi-account-outline me-1 text-primary"></i>Attn / PIC</span>
                        <span class="fw-medium text-dark">
                            : {{ $picName }}
                            @if ($quote->pic?->phone_pic)
                                <span class="text-muted ms-1">({{ $quote->pic->phone_pic }})</span>
                            @endif
                        </span>
                    @endif

                    @if ($quote->client?->phone)
                        <span class="text-muted" style="white-space:nowrap;"><i class="mdi mdi-phone-in-talk-outline me-1 text-primary"></i>Office Phone</span>
                        <span class="fw-medium text-dark">: {{ $quote->client->phone }}</span>
                    @endif

                    @if ($targetAddress && $targetAddress !== '-')
                        <span class="text-muted" style="white-space:nowrap;"><i class="mdi mdi-map-marker-outline me-1 text-primary"></i>Address</span>
                        <span class="fw-medium text-dark" style="line-height:1.4;">: {{ $targetAddress }}</span>
                    @endif
                </div>
            </div>

            {{-- Card 2: Payment Terms & Info --}}
            <div style="min-width:240px; flex:1; display:flex; flex-direction:column; align-self:stretch; border:1px solid #e0e0e0; border-left:4px solid #8592a3; border-radius:4px; padding:12px 16px; background:#fcfcfc;">
                <div class="mb-2 pb-1" style="border-bottom: 1px dashed #e4e4e4;">
                    <span class="fw-bold text-uppercase" style="font-size:10.5px; letter-spacing:0.6px; color:#566a7f;">
                        <i class="mdi mdi-file-document-outline me-1"></i>Payment Terms &amp; Info
                    </span>
                </div>

                <div style="font-size:11.5px; color:#333;" class="my-auto">
                    <div class="d-flex align-items-center mb-2 pb-1" style="border-bottom:1px dashed #f0f0f0;">
                        <span class="text-muted" style="min-width:60px;"><i class="mdi mdi-clipboard-text-outline me-1 text-primary"></i>PO No</span>
                        <span class="fw-bold text-dark">: {{ $quote->po_number ?? '-' }}</span>
                    </div>
                    <div class="mt-2">
                        <div class="fw-medium text-dark mb-1">
                            <i class="mdi mdi-clock-outline me-1 text-primary"></i>Term of Payment :
                        </div>
                        <div class="ps-2 ms-1" style="border-left:3px solid #696cff; margin-top:4px;">
                            <div class="fw-bold text-dark ps-2" style="font-size:11.5px; line-height:1.45; white-space:pre-line;"><i class="mdi mdi-chevron-right text-primary me-1" style="font-size:14px;"></i>{{ $invoice->term ?? $quote->payment_method ?? '-' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Items --}}
        @php
            $afterDisc  = $quote->subtotal - $quote->discount_amount;
            $bgColor    = 'rgb(224, 248, 248)';
            $hasDisc    = $quote->details->where('disc', '>', 0)->count() > 0;
            $labelSpan  = $quote->tax ? ($hasDisc ? 3 : 2) : 3;
            $amountSpan = ($quote->tax || $hasDisc) ? 2 : 1;
        @endphp
        <div>
            <table class="table table-bordered m-0" style="border: 1px solid rgb(60,60,60); width: 100%;">
                <thead class="table-light">
                    <tr>
                        <th class="text-center align-middle" style="width:1%">No.</th>
                        <th class="text-center align-middle" style="width:{{ $quote->tax ? '38%' : '45%' }}"><span class="i18n" data-en="DESCRIPTION">DESKRIPSI</span></th>
                        <th class="text-center align-middle"><span class="i18n" data-en="PRICE">HARGA</span></th>
                        <th class="text-center align-middle" style="width:1%; white-space:nowrap">Qty</th>
                        @if ($hasDisc)
                            <th class="text-center align-middle">Disc</th>
                        @endif
                        @if ($quote->tax)
                            <th class="text-center align-middle">DPP</th>
                        @endif
                        <th class="text-center align-middle"><span class="i18n" data-en="TOTAL PRICE">TOTAL HARGA</span></th>
                    </tr>
                </thead>
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
                        'bar'=>' Bar','air_cap'=>' m³/min',
                        'filtration'=>' µm','oil_content'=>' ppm',
                        'test_pressure'=>' Bar','inlet_pressure'=>' Bar','outlet_pressure'=>' Bar',
                        'inlet_cap'=>' m³/min','outlet_cap'=>' m³/min',
                        'weight'=>' Kg','capacity'=>' Liter',
                    ];
                    $specLabelsOverride = [
                        'AIR RECEIVER TANK' => ['bar'=>'Max. Pressure','grade'=>'T Plate','cooling'=>'Certification'],
                        'FILTRATION SYSTEM'  => ['air_cap'=>'Flowrate','material'=>'Element','connect'=>'Drain'],
                    ];
                @endphp
                <tbody>
                    @php
                        $itemNo      = 1;
                        $headerCount = 0;
                        $totCols     = 5 + ($hasDisc ? 1 : 0) + ($quote->tax ? 1 : 0);
                    @endphp
                    @foreach ($quote->details as $detail)
                        @if ($detail->type === 'header' || $detail->type === 'heading')
                            @php
                                $lbl = trim($detail->label ?? '');
                                if (!preg_match('/^[A-Z0-9][\.\)]/i', $lbl)) {
                                    $lbl = chr(65 + ($headerCount % 26)) . '. ' . $lbl;
                                }
                                $headerCount++;
                            @endphp
                            <tr style="background:#f0f0ff; border-top:1.5px solid #d0d0ff; border-bottom:1.5px solid #d0d0ff;">
                                <td colspan="{{ $totCols }}" class="fw-bold text-uppercase py-2 px-3 text-primary" style="font-size:12px; letter-spacing:0.5px;">
                                    <i class="mdi mdi-bookmark-outline me-1"></i> {{ $lbl }}
                                </td>
                            </tr>
                        @else
                            @php $dpp = $quote->tax ? ($detail->amount * 11 / 12) : 0; @endphp
                            <tr style="font-size: 13px">
                                <td class="align-top text-center">{{ $itemNo++ }}</td>
                                <td class="align-top">
                                    @if ($detail->type === 'unit' && $detail->unit)
                                        <p class="mb-1 fw-medium" style="font-size: 12px">{{ $detail->label ?: ($detail->unit->brand . ' ' . $detail->unit->model) }}</p>
                                        @php
                                            $specs       = $detail->getSpecVisibleArray();
                                            $category    = $detail->unit->unit ?? '';
                                            $catOverride = $specLabelsOverride[$category] ?? [];
                                        @endphp
                                        @if (!empty($specs) && $invoice->show_spec)
                                            <div style="font-size:10px; color:#555; margin-top:3px;">
                                                @foreach ($specs as $field)
                                                    @if ($field === 'unit') @continue @endif
                                                    @php $val = $detail->unit->$field ?? null; @endphp
                                                    @if ($val && isset($specLabels[$field]))
                                                        <div style="display:flex; padding:1px 0;">
                                                            <span style="color:#888; min-width:110px; flex-shrink:0;">{{ $catOverride[$field] ?? $specLabels[$field] }}</span>
                                                            <span>: {{ $val }}{{ $specUnits[$field] ?? '' }}</span>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                        @endif
                                    @elseif ($detail->type === 'equivalent' || $detail->type === 'sparepart' || $detail->id_equivalent || $detail->equivalent)
                                         @if ($detail->equivalent)
                                             @php
                                                 $brandPn = trim(($detail->equivalent->brand ?? '') . ($detail->equivalent->pn ? ' - ' . $detail->equivalent->pn : ''));
                                                 $subDesc = $detail->label;
                                                 if (empty($subDesc) || $subDesc === $brandPn) {
                                                     $subDesc = optional($detail->equivalent->product)->description ?? optional($detail->equivalent->product)->name;
                                                 }
                                             @endphp
                                             <p class="mb-0 fw-bold text-dark" style="font-size: 12px">
                                                 {{ $brandPn ?: $detail->label }}
                                             </p>
                                             @if ($subDesc && $subDesc !== $brandPn)
                                                 <div style="font-size: 12px; color: #333333; font-weight: 500; margin-top: 2px; line-height: 1.4;">{{ $subDesc }}</div>
                                             @endif
                                         @else
                                             <p class="mb-0 fw-bold text-dark" style="font-size: 12px">{{ $detail->label }}</p>
                                         @endif
                                    @else
                                        <p class="mb-0 fw-bold text-dark" style="font-size: 12px">{{ $detail->label ?: '-' }}</p>
                                    @endif

                                     @if ($detail->description)
                                         <div style="font-size: 11px; color: #444; white-space: pre-line; margin-top: 3px; line-height: 1.4;">{{ $detail->description }}</div>
                                     @endif
                                </td>
                                <td class="align-top text-end">{{ number_format($detail->price, 0, '', '.') }}</td>
                                <td class="align-top text-center" style="white-space:nowrap">{{ (float) $detail->qty }} {{ $detail->info_qty }}</td>
                                @if ($hasDisc)
                                    <td class="align-top text-center">{{ $detail->disc > 0 ? (float)$detail->disc . '%' : '-' }}</td>
                                @endif
                                @if ($quote->tax)
                                    <td class="align-top text-end">{{ number_format($dpp, 0, '', '.') }}</td>
                                @endif
                                <td class="align-top text-end fw-semibold">{{ number_format($detail->amount, 0, ',', '.') }}</td>
                            </tr>
                        @endif
                    @endforeach

                    {{-- Finance Summary --}}
                    <tr class="fw-medium" style="font-size: 13px">
                        <td colspan="{{ $quote->tax ? 2 : 1 }}" rowspan="9" style="border: none !important;"></td>
                        <td colspan="{{ $labelSpan }}" class="text-end py-0" style="padding-right: 10px !important;">
                            <p class="m-0">{{ ($quote->tax || $totalPph > 0) ? 'Subtotal' : 'Total' }}</p>
                        </td>
                        <td colspan="{{ $amountSpan }}" class="py-0 text-end" style="padding-right: 10px !important;">
                            <p class="m-0">Rp {{ number_format($quote->subtotal, 0, '', '.') }}</p>
                        </td>
                    </tr>
                    @if ($quote->diskon > 0)
                        <tr class="fw-medium" style="font-size: 13px">
                            <td colspan="{{ $labelSpan }}" class="text-end py-0" style="padding-right: 10px !important;">
                                <p class="m-0">Discount{{ $quote->discount_label ? ' (' . $quote->discount_label . ')' : '' }}</p>
                            </td>
                            <td colspan="{{ $amountSpan }}" class="py-0 text-end" style="padding-right: 10px !important;">
                                <p class="m-0">- Rp {{ number_format($quote->discount_amount, 0, '', '.') }}</p>
                            </td>
                        </tr>
                        <tr class="fw-medium" style="font-size: 13px">
                            <td colspan="{{ $labelSpan }}" class="text-end py-0" style="padding-right: 10px !important;">
                                <p class="m-0">Total After Discount</p>
                            </td>
                            <td colspan="{{ $amountSpan }}" class="py-0 text-end" style="padding-right: 10px !important;">
                                <p class="m-0">Rp {{ number_format($afterDisc, 0, '', '.') }}</p>
                            </td>
                        </tr>
                    @endif
                    @if ($quote->tax)
                        <tr class="fw-medium" style="font-size: 13px">
                            <td colspan="{{ $labelSpan }}" class="text-end py-0" style="padding-right: 10px !important;">
                                <p class="m-0"><span class="i18n" data-en="DPP on PPN">DPP Atas PPN</span></p>
                            </td>
                            <td colspan="{{ $amountSpan }}" class="py-0 text-end" style="padding-right: 10px !important;">
                                <p class="m-0">Rp {{ number_format($afterDisc * 11 / 12, 0, '', '.') }}</p>
                            </td>
                        </tr>
                        <tr class="fw-medium" style="font-size: 13px">
                            <td colspan="{{ $labelSpan }}" class="text-end py-0" style="padding-right: 10px !important;">
                                <p class="m-0">PPN 12%</p>
                            </td>
                            <td colspan="{{ $amountSpan }}" class="py-0 text-end" style="padding-right: 10px !important;">
                                <p class="m-0">Rp {{ number_format($quote->tax_amount, 0, '', '.') }}</p>
                            </td>
                        </tr>
                    @endif
                    @if ($quote->shipping > 0)
                        <tr class="fw-medium" style="font-size: 13px">
                            <td colspan="{{ $labelSpan }}" class="text-end py-0" style="padding-right: 10px !important;">
                                <p class="m-0">Shipping Cost</p>
                            </td>
                            <td colspan="{{ $amountSpan }}" class="py-0 text-end" style="padding-right: 10px !important;">
                                <p class="m-0">Rp {{ number_format($quote->shipping, 0, '', '.') }}</p>
                            </td>
                        </tr>
                    @endif
                    @if ($totalPph > 0)
                        <tr class="fw-medium" style="font-size: 13px">
                            <td colspan="{{ $labelSpan }}" class="text-end py-0" style="padding-right: 10px !important;">
                                <p class="m-0">PPH</p>
                            </td>
                            <td colspan="{{ $amountSpan }}" class="py-0 text-end" style="padding-right: 10px !important;">
                                <p class="m-0">Rp {{ number_format($totalPph, 0, '', '.') }}</p>
                            </td>
                        </tr>
                    @endif
                    @php
                        $showTagihanBreakdown = floatval($invoice->percent) < 100 || in_array($invoice->type, ['DP', 'BP', 'Balance Payment', 'Down Payment']);
                    @endphp
                    @if ($quote->tax || $totalPph > 0 || $showTagihanBreakdown)
                        <tr class="fw-medium" style="font-size: 13px">
                            <td colspan="{{ $labelSpan }}" class="text-end py-2" style="background-color:{{ $bgColor }}; padding-right: 10px !important;">
                                <p class="m-0 fw-bold">TOTAL</p>
                            </td>
                            <td colspan="{{ $amountSpan }}" class="py-2 text-end" style="background-color:{{ $bgColor }}; padding-right: 10px !important;">
                                <p class="m-0 fw-bold">Rp {{ number_format($showTagihanBreakdown ? $quote->total : $totalAfterPph, 0, '', '.') }}</p>
                            </td>
                        </tr>
                    @endif
                    @if ($showTagihanBreakdown)
                        @if (in_array($invoice->type, ['BP', 'Balance Payment']))
                            @php
                                $dpPercent = 100 - floatval($invoice->percent);
                                $dpAmount  = round($quote->total * $dpPercent / 100);
                            @endphp
                            @if ($dpAmount > 0)
                                <tr class="fw-medium" style="font-size: 13px">
                                    <td colspan="{{ $labelSpan }}" class="text-end py-1" style="padding-right: 10px !important; color:#dc3545;">
                                        <p class="m-0"><span class="i18n" data-en="DP Already Paid ({{ $dpPercent }}%)">DP Telah Dibayar ({{ $dpPercent }}%)</span></p>
                                    </td>
                                    <td colspan="{{ $amountSpan }}" class="py-1 text-end" style="padding-right: 10px !important; color:#dc3545;">
                                        <p class="m-0">Rp {{ number_format($dpAmount, 0, '', '.') }}</p>
                                    </td>
                                </tr>
                            @endif
                        @endif
                        @php
                            $billingType    = in_array($invoice->type, ['BP', 'Balance Payment']) ? 'BP' : (in_array($invoice->type, ['DP', 'Down Payment']) ? 'DP' : $invoice->type);
                            $billingLabelId = 'TAGIHAN ' . $billingType . ' (' . floatval($invoice->percent) . '%)';
                            $billingLabelEn = 'AMOUNT DUE - ' . $billingType . ' (' . floatval($invoice->percent) . '%)';
                        @endphp
                        <tr style="font-size: 13px; background:yellow; border-top:2px solid #e6c300;">
                            <td colspan="{{ $labelSpan }}" class="text-end py-2 fw-bold" style="padding-right: 10px !important; color:#000;">
                                <p class="m-0 fw-bold"><span class="i18n" data-en="{{ $billingLabelEn }}">{{ $billingLabelId }}</span></p>
                            </td>
                            <td colspan="{{ $amountSpan }}" class="py-2 fw-bold text-end" style="padding-right: 10px !important; color:#000;">
                                <p class="m-0 fw-bold">Rp {{ number_format($totalAfterPph, 0, '', '.') }}</p>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        {{-- Terbilang Box --}}
        <div class="mt-3 mb-3 px-1 py-2 rounded-0" style="background:#f0f2ff; border: 1px dashed #696cff; display:inline-block; width:70%; border-radius:0 !important;">
            <div style="display:flex; align-items:center; gap:8px;">
                <span style="font-weight:700; color:#696cff; font-size:12px;"><span class="i18n" data-en="Say Amount :">Terbilang :</span></span>
                <span style="font-weight:700; color:#111; font-size:12.5px;" class="i18n" data-en="# {{ $terbilangEn }} Rupiah"># {{ $terbilang }} Rupiah</span>
            </div>
        </div>

        {{-- Bank & TTD --}}
        <div style="display:flex; gap:0; margin-top:16px;">
            <div style="flex:0 0 55%;">
                <div style="padding:12px 14px; border:1px solid #e0e0f0; border-radius:0 !important; background:#fafafa; font-size:11.5px;">
                    <p style="font-weight:700; margin-bottom:8px; color:#222; font-size:12px;">
                        <span style="color:#696cff; margin-right:4px;">&#9650;</span> <span class="i18n" data-en="Payment : Bank Transfer / Giro">Pembayaran : Transfer / Giro</span>
                    </p>
                    <table style="width:100%; border-collapse:collapse;">
                        @if ($quote->tax)
                            <tr>
                                <td style="padding:2px 0; color:#555; width:90px;">Bank Name</td>
                                <td style="padding:2px 0; font-weight:600; color:#111;">: Bank BCA (IDR)</td>
                            </tr>
                            <tr>
                                <td style="padding:2px 0; color:#555;">Acc Name</td>
                                <td style="padding:2px 0; font-weight:700; color:#696cff;">: PT REFTECH JAYA OPTIMA</td>
                            </tr>
                            <tr>
                                <td style="padding:2px 0; color:#555;">Acc No.</td>
                                <td style="padding:2px 0; font-weight:700; color:#111;">: 008 - 6289 - 789</td>
                            </tr>
                            <tr>
                                <td style="padding:2px 0; color:#555;">Swift Code</td>
                                <td style="padding:2px 0; font-weight:500; color:#333;">: CENAIDJA</td>
                            </tr>
                        @else
                            <tr>
                                <td style="padding:2px 0; color:#555; width:90px;">Bank Name</td>
                                <td style="padding:2px 0; font-weight:600; color:#111;">: Bank BCA (IDR)</td>
                            </tr>
                            <tr>
                                <td style="padding:2px 0; color:#555;">Acc Name</td>
                                <td style="padding:2px 0; font-weight:700; color:#696cff;">: ARIEP RACHMAN</td>
                            </tr>
                            <tr>
                                <td style="padding:2px 0; color:#555;">Acc No.</td>
                                <td style="padding:2px 0; font-weight:700; color:#111;">: 166 - 2242 - 271</td>
                            </tr>
                        @endif
                    </table>
                </div>
            </div>
            <div style="flex:1;"></div>
            <div style="flex:0 0 30%; text-align:center; padding-top:8px;">
                @php
                    $signDateBase = $invoice->date ? \Carbon\Carbon::parse($invoice->date) : \Carbon\Carbon::now();
                    $signDateId   = $signDateBase->copy()->locale('id')->translatedFormat('d F Y');
                    $signDateEn   = $signDateBase->copy()->locale('en')->translatedFormat('d F Y');
                @endphp
                <p style="margin-bottom:4px; color:#777; font-size:11px;">Bandung, <span class="i18n" data-en="{{ $signDateEn }}">{{ $signDateId }}</span></p>
                @if ($quote->tax)
                    <p style="font-weight:700; font-size:12px; margin-bottom:4px; color:#222;">PT. Reftech Jaya Optima</p>
                @endif
                @if (isset($invoice->sign))
                    <div style="margin:8px 0;">
                        <img src="{{ url('') . '/' . $invoice->sign }}" alt="Signature" height="70">
                    </div>
                @else
                    <div style="padding:55px 0;"></div>
                @endif
                <p style="font-weight:700; font-size:13px; color:#111; border-bottom:1px solid #ddd; display:inline-block; padding-bottom:2px; margin-bottom:2px;">Ariep Rachman</p>
                <p style="color:#777; font-size:11px; margin:0;">Director</p>
            </div>
        </div>

    </div>
</div>
@push('after-style')
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/css/pages/app-invoice-print-header.css" />
    <style>
        @media print {
            @page { size: A4 portrait; margin: 10mm 12mm 10mm 12mm; }
            .invoice-print .text-end h1.invoice-title-heading { color: #2529fa !important; }
            .invoice-print div { overflow: visible !important; }
            .invoice-print table { width: 100% !important; }
            .invoice-print td, .invoice-print th { overflow-wrap: break-word !important; }
            .invoice-print table td { color: #333 !important; }
            .invoice-print pre { white-space: pre-wrap !important; word-break: break-word !important; overflow: visible !important; max-width: 100% !important; }
        }
        @media screen {
            .invoice-print table td { color: #333 !important; }
            .invoice-print pre { white-space: pre-wrap; word-break: break-word; overflow: visible; max-width: 100%; }
        }
    </style>
@endpush
@push('after-script')
    <script src="{{ asset('assets') }}/js/app-invoice-print.js"></script>
@endpush
@push('script')
<script>
    // Invoice language follows whatever was picked on the invoice detail page (passed via ?lang=en)
    document.addEventListener('DOMContentLoaded', function () {
        var lang = new URLSearchParams(window.location.search).get('lang');
        if (lang !== 'en') {
            return;
        }
        document.querySelectorAll('.i18n').forEach(function (el) {
            if (el.dataset.en) {
                el.textContent = el.dataset.en;
            }
        });
    });
</script>
@endpush
