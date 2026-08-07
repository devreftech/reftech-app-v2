@extends('layouts.sales.app')
@section('title', $invoice->no_invoice ?? 'Invoice Unit')
<div class="invoice-print p-4">
    <div class="container-fluid flex-grow-1 container-p-y">

        {{-- Header --}}
        <div class="d-flex justify-content-{{ !$quote->tax ? 'end' : 'between' }} flex-xl-row flex-md-column flex-sm-row flex-column">
            @if ($quote->tax)
                <div class="mb-xl-0 pb-1">
                    <div class="d-flex svg-illustration align-items-center gap-2 mb-4">
                        <span class="app-brand-logo demo">
                            <span style="color: var(--bs-primary)">
                                <img class="text-md" src="{{ asset('/asset') }}/logo/Reftech-Log.png" alt="" srcset="" width="60%">
                            </span>
                        </span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <div class="info">
                            <p class="mb-1 fw-bolder">Office Address :</p>
                            <div style="font-size: 10px">
                                <p class="mb-1">Taman Kopo Indah V, Ruko Sommerville No. 31</p>
                                <p class="mb-1">Bandung – Jawa Barat 40218</p>
                                <p class="mb-1">
                                    <i class="mdi mdi-phone-outline scaleX-n1-rtl me-1 mdi-14px"></i>022 54417653
                                    &nbsp;&nbsp;<i class="mdi mdi-email-outline scaleX-n1-rtl me-1 mdi-14px"></i>info@reftech.id
                                </p>
                            </div>
                        </div>
                        <div class="npwp_add">
                            <p class="mb-1 fw-bolder">NPWP Address :</p>
                            <pre style="font-size: 10px; font-family: Inter, sans-serif; max-width: 250px; overflow-x: auto; white-space: pre-wrap;">Komp. Negia Kencana Residence Blok B, No.2 Pasanggrahan, Ujung Berung Kota Bandung - Jawa Barat 40199</pre>
                            <p class="mb-1 text-black fw-medium p-1" style="background-color: rgb(224, 221, 255); font-size: 10px;">
                                NPWP : 73.728.571.8-429.000
                            </p>
                        </div>
                    </div>
                </div>
            @endif
            <div class="text-end">
                <h1 class="fw-bold" style="color: blue;">INVOICE</h1>
                <div>
                    <span class="fw-bolder" style="font-size: 18px">{{ $invoice->no_invoice }}</span>
                </div>
                <div class="mt-1">
                    <span class="fw-medium">{{ $invoice->date ? \Carbon\Carbon::parse($invoice->date)->format('d-m-Y') : '-' }}</span>
                </div>
            </div>
        </div>

        <hr>

        {{-- Invoice To --}}
        <h5>Invoice To</h5>
        <div class="table-responsive text-nowrap">
            <table class="table table-bordered" style="border: 1px solid black;">
                <tr>
                    <td rowspan="3" style="vertical-align: top; width: 60%;">
                        <div class="row">
                            <div class="col-2 fw-medium"><p class="mb-1">Bill To</p></div>
                            <div class="col-10">
                                <p class="mb-1 fw-bolder">: {{ $quote->client?->company }}</p>
                            </div>
                            <div class="col-2 fw-medium"><p class="mb-1">PIC</p></div>
                            <div class="col-10">
                                <p class="mb-1 fw-bolder">: {{ $quote->pic?->name_pic ?? $quote->attn }}</p>
                            </div>
                            <div class="col-2 fw-medium"><p class="mb-1">NPWP</p></div>
                            <div class="col-10">
                                <p class="mb-1">: {{ $quote->client?->npwp }}</p>
                            </div>
                            <div class="col-2 fw-medium"><p class="mb-1">Phone</p></div>
                            <div class="col-10">
                                <p class="mb-1">: {{ $quote->client?->phone }}</p>
                            </div>
                            <div class="col-2 fw-medium"><p class="mb-1">Address</p></div>
                            <div class="col-10">
                                @if ($invoice->invoiceTo == '1')
                                    <pre style="font-size: 13px; font-family: Inter, sans-serif; max-width: 100%; overflow-x: auto; white-space: pre-wrap;">: {{ $quote->client?->address }}</pre>
                                @else
                                    <pre style="font-size: 13px; font-family: Inter, sans-serif; max-width: 100%; overflow-x: auto; white-space: pre-wrap;">: {{ $quote->client?->subAddress }}</pre>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td><p>Purchase Order</p></td>
                    <td><p class="fs-6 text-black fw-bold m-0">{{ $quote->po_number }}</p></td>
                </tr>
                <tr>
                    <td colspan="2" style="background-color: #F9F9F9;" class="text-center">
                        <p class="fs-5 text-black fw-medium m-0">Term Of Payment:</p>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" class="text-center">
                        <pre style="font-size: 14px; font-family: Inter;">{{ $invoice->term ?? $quote->payment_method }}</pre>
                    </td>
                </tr>
            </table>
        </div>

        {{-- Items --}}
        @php
            $afterDisc = $quote->subtotal - ($quote->subtotal * $quote->diskon / 100);
            $bgColor   = 'rgb(224, 248, 248)';
        @endphp
        <div class="table-responsive">
            <table class="table table-bordered m-0" style="border: 1px solid rgb(60,60,60)">
                <thead class="table-light">
                    <tr>
                        <th>No.</th>
                        <th>Item</th>
                        <th>Price</th>
                        <th>Qty</th>
                        <th style="width:5%">Disc</th>
                        @if ($quote->tax)
                            <th style="width:15%">DPP</th>
                        @endif
                        <th style="width:20%">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($quote->details as $i => $detail)
                        @php $dpp = $quote->tax ? ($detail->amount * 11 / 12) : 0; @endphp
                        <tr style="font-size: 13px">
                            <td class="align-top">{{ $i + 1 }}</td>
                            <td class="text-nowrap align-top">
                                @if ($detail->type === 'unit' && $detail->unit)
                                    <p class="mb-0 fw-medium" style="font-size: 12px">{{ $detail->label ?: ($detail->unit->brand . ' ' . $detail->unit->model) }}</p>
                                    <small class="text-muted">{{ $detail->unit->type }}</small>
                                @else
                                    <p class="mb-0 fw-medium" style="font-size: 12px">{{ $detail->label }}</p>
                                    @if ($detail->description)
                                        <pre class="mb-0" style="font-size: 10px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; max-width: 100%; overflow-x: auto; white-space: pre-wrap;">{{ $detail->description }}</pre>
                                    @endif
                                @endif
                            </td>
                            <td class="align-top text-end">RP {{ number_format($detail->price, 0, '', '.') }}</td>
                            <td class="align-top">{{ $detail->qty }} {{ $detail->info_qty }}</td>
                            <td class="align-top">{{ $detail->disc > 0 ? $detail->disc . '%' : '-' }}</td>
                            @if ($quote->tax)
                                <td class="align-top text-end">RP {{ number_format($dpp, 0, '', '.') }}</td>
                            @endif
                            <td class="align-top text-end">RP {{ number_format($detail->amount, 0, '', '.') }}</td>
                        </tr>
                    @endforeach

                    {{-- Totals --}}
                    <tr class="fw-medium" style="font-size: 13px">
                        <td colspan="{{ $quote->tax ? 3 : 2 }}" rowspan="9" style="border-bottom: none !important;"></td>
                        <td colspan="3" class="text-end pl-4 py-0" style="padding-right: 10px !important;">
                            <p class="m-0">{{ ($quote->tax || $totalPph > 0) ? 'Subtotal' : 'Total' }}</p>
                        </td>
                        <td class="pr-4 py-0" style="padding-left: 0 !important;">
                            <p class="text-end m-0">RP {{ number_format($quote->subtotal, 0, '', '.') }}</p>
                        </td>
                    </tr>
                    @if ($quote->diskon > 0)
                        <tr class="fw-medium" style="font-size: 13px">
                            <td colspan="3" class="text-end py-0" style="padding-right: 10px !important;">
                                <p class="m-0">Discount ({{ $quote->diskon }}%)</p>
                            </td>
                            <td class="pr-4 py-0" style="padding-left: 0 !important;">
                                <p class="m-0 text-end">RP {{ number_format($quote->subtotal * $quote->diskon / 100, 0, '', '.') }}</p>
                            </td>
                        </tr>
                        <tr class="fw-medium" style="font-size: 13px">
                            <td colspan="3" class="text-end py-0" style="padding-right: 10px !important;">
                                <p class="m-0">Total After Discount</p>
                            </td>
                            <td class="pr-4 py-0" style="padding-left: 0 !important;">
                                <p class="m-0 text-end">RP {{ number_format($afterDisc, 0, '', '.') }}</p>
                            </td>
                        </tr>
                    @endif
                    @if ($quote->tax)
                        <tr class="fw-medium" style="font-size: 13px">
                            <td colspan="3" class="text-end pl-4 py-0" style="padding-right: 10px !important;">
                                <p class="m-0">DPP Atas PPN</p>
                            </td>
                            <td class="pr-4 py-0" style="padding-left: 0 !important;">
                                <p class="text-end m-0">RP {{ number_format($afterDisc * 11 / 12, 0, '', '.') }}</p>
                            </td>
                        </tr>
                        <tr class="fw-medium py-0" style="font-size: 13px">
                            <td colspan="3" class="text-end py-0" style="padding-right: 10px !important;">
                                <p class="m-0">VAT 12%</p>
                            </td>
                            <td class="pr-4 py-0" style="padding-left: 0 !important;">
                                <p class="m-0 text-end">RP {{ number_format($quote->tax_amount, 0, '', '.') }}</p>
                            </td>
                        </tr>
                    @endif
                    @if ($totalPph > 0)
                        <tr class="fw-medium py-0" style="font-size: 13px">
                            <td colspan="3" class="text-end py-0" style="padding-right: 10px !important;">
                                <p class="m-0">PPH</p>
                            </td>
                            <td class="pr-4 py-0" style="padding-left: 0 !important;">
                                <p class="m-0 text-end">RP {{ number_format($totalPph, 0, '', '.') }}</p>
                            </td>
                        </tr>
                    @endif
                    @if ($quote->tax || $totalPph > 0)
                        <tr class="fw-medium py-0" style="font-size: 13px">
                            <td colspan="3" class="text-end py-0"
                                style="background-color: {{ $bgColor }}; padding-left: 20px; padding-right: 10px;">
                                <p class="m-0 fw-bold">Total</p>
                            </td>
                            <td class="pr-4 py-0" style="background-color: {{ $bgColor }}; padding-right: 20px;">
                                <p class="m-0 text-end fw-bold">RP {{ number_format($quote->total, 0, '', '.') }}</p>
                            </td>
                        </tr>
                    @endif

                    @if (in_array($invoice->type, ['DP', 'BP']))
                        <tr style="font-size: 13px; background: #fff3cd;">
                            <td colspan="3" class="text-end py-1 fw-bold" style="padding-right: 10px !important;">
                                <p class="m-0">
                                    {{ $invoice->type === 'DP' ? 'Tagihan DP (' . floatval($invoice->percent) . '%)' : 'Tagihan BP (' . floatval($invoice->percent) . '% sisa)' }}
                                </p>
                            </td>
                            <td class="py-1 fw-bold" style="padding-left: 0 !important; padding-right: 20px;">
                                <p class="m-0 text-end">RP {{ number_format($totalAfterPph, 0, '', '.') }}</p>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        {{-- Terbilang --}}
        <p class="fw-medium mt-2 p-2" style="background-color: rgb(248,248,248); width: 70%;">
            Say amount: # {{ $terbilang }} Rupiah
        </p>

        {{-- Bank & TTD --}}
        <div class="row mt-5">
            <div class="col-6 m-4">
                <h5 class="my-4">Payment by Transfer or Giro shall be made in Full amount to :</h5>
                <div class="row">
                    <div class="col-3 fw-medium">
                        <p class="mb-1">Payable to</p>
                        <p class="mb-1">Acc Name</p>
                        <p class="mb-1">Acc No.</p>
                        <p class="mb-1">Swift Code</p>
                    </div>
                    @if ($quote->tax)
                        <div class="col">
                            <p class="mb-1">: Bank BCA (IDR)</p>
                            <p class="mb-1">: PT. REFTECH JAYA OPTIMA</p>
                            <p class="mb-1">: 008 - 6289 - 789</p>
                            <p class="mb-1">: CENAIDJA</p>
                        </div>
                    @else
                        <div class="col">
                            <p class="mb-1">: Bank BCA (IDR)</p>
                            <p class="mb-1">: ARIEP RACHMAN</p>
                            <p class="mb-1">: 166 - 2242 - 271</p>
                            <p class="mb-1">: -</p>
                        </div>
                    @endif
                </div>
            </div>
            <div class="col"></div>
            <div class="col-4 my-5 text-center">
                <p>Bandung, {{ $invoice->date ? \Carbon\Carbon::parse($invoice->date)->locale('ID')->translatedFormat('d F Y') : \Carbon\Carbon::now()->locale('ID')->translatedFormat('d F Y') }}</p>
                @if ($quote->tax)
                    <p class="fs-normal fw-bolder">PT. Reftech Jaya Optima</p>
                @endif
                @if (isset($invoice->sign))
                    <img src="{{ url('') . '/' . $invoice->sign }}" alt="" height="77">
                @else
                    <div style="padding: 40px 0;"></div>
                @endif
                <p class="pt-3 fw-bolder">Ariep Rachman</p>
                <p>Director</p>
            </div>
        </div>

    </div>
</div>
@push('after-style')
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/css/pages/app-invoice-print-header.css" />
@endpush
@push('after-script')
    <script src="{{ asset('assets') }}/js/app-invoice-print.js"></script>
@endpush
