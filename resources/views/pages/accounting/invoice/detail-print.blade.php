    @extends('layouts.sales.app')
    @section('title', $invoice->no_invoice)
    <div class="invoice-print p-4">
        <div class="container-fluid flex-grow-1 container-p-y">
            @if ($quote->pic->client->info == 'Reftech')
                <div
                    class="d-flex justify-content-{{ $quote->tax == 0 ? 'end' : 'between' }} flex-xl-row flex-md-column flex-sm-row flex-column">
                    @if ($quote->tax != 0)
                        <div class="mb-xl-0 pb-1">
                            <div class="d-flex svg-illustration align-items-center gap-2 mb-4">
                                <span class="app-brand-logo demo">
                                    <span style="color: var(--bs-primary)">
                                        <img class="text-md" src="{{ asset('/asset') }}/logo/Reftech-Log.png"
                                            alt="" srcset="" width="60%">
                                    </span>
                                </span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <div class="info">
                                    <p class="mb-1 fw-bolder">Office Address :</p>
                                    <div style="font-size: 10px">
                                        <p class="mb-1">Taman Kopo Indah V, Soho Sommerville No. 31</p>
                                        <p class="mb-1">Bandung – Jawa Barat 40218</p>
                                        <p class="mb-1">
                                            <i class="mdi mdi-phone-outline scaleX-n1-rtl me-1 mdi-14px"></i>022
                                            54417653
                                            {{ '   ' }}<i
                                                class="mdi mdi-email-outline scaleX-n1-rtl me-1 mdi-14px"></i>accounting@reftech.id
                                        </p>
                                        <p class="mb-1">
                                        </p>
                                    </div>
                                </div>
                                <div class="npwp_add">
                                    <p class="mb-1 fw-bolder">NPWP Address :</p>
                                    <pre
                                        style="font-size: 10px; font-family: Inter, sans-serif; max-width: 250px; overflow-x: auto; white-space: pre-wrap;">Komp. Negla Kencana Residence Blok B, No.2 Pasanggrahan, Ujung Berung Kota Bandung - Jawa Barat 40199</pre>
                                    <p class="mb-1 text-black fw-medium p-1"
                                        style="background-color: rgb(224, 221, 255); font-size :10px;">
                                        NPWP : 73.728.571.8-429.000</p>
                                </div>
                            </div>
                        </div>
                    @endif
                    <div class="text-end">
                        <h1 class="fw-bold" style="color: #2529fa; letter-spacing: 2px;">INVOICE</h1>
                        <div>
                            <span class="fw-bolder" style="font-size:18px">{{ $invoice->no_invoice }}</span>
                        </div>
                        <div class="mt-1">
                            <span class="fw-medium">{{ Carbon\Carbon::parse($invoice->date)->format('d-m-Y') }}</span>
                        </div>
                    </div>
                </div>
            @else
                <div
                    class="d-flex justify-content-between flex-xl-row flex-md-column flex-sm-row flex-column {{ $quote->tax == 0 ? 'float-end' : '' }}">
                    @if ($quote->tax != 0)
                        <div class="mb-xl-0 pb-1">
                            <div class="d-flex svg-illustration align-items-center gap-2 mb-2">
                                <span class="app-brand-logo demo">
                                    <span style="color: var(--bs-primary)">
                                        <img class="text-md" src="{{ asset('/asset') }}/logo/Logo-update-size.png"
                                            alt="" srcset="" width="60%">
                                    </span>
                                </span>
                            </div>

                            <div class="d-flex justify-content-between">
                                <div class="info">
                                    <p class="mb-1 fw-bolder">Office Address :</p>
                                    <div style="font-size: 10px">
                                        <p class="mb-1">Jl. Nancep No. 45A, Setu</p>
                                        <p class="mb-1">Cibitung - Kab. Bekasi 17320</p>
                                        <p class="mb-1">
                                            <i class="mdi mdi-phone-outline scaleX-n1-rtl me-1 mdi-14px"></i>+62
                                            812-1000-0997
                                            {{ ' | ' }}<i
                                                class="mdi mdi-email-outline scaleX-n1-rtl me-1 mdi-14px"></i>admin@kojisha.com
                                    </div>
                                </div>
                                <div class="npwp_add">
                                    <p class="mb-1 fw-bolder">NPWP Address :</p>
                                    <pre
                                        style="font-size: 10px; font-family: Inter, sans-serif; max-width: 250px; overflow-x: auto; white-space: pre-wrap;">Jl. Nancep No. 45, Setu Cisaat RT. 001 RW. 003 Cibening, Setu</pre>
                                    </p>
                                    <p class="mb-1 text-black fw-medium p-1"
                                        style="background-color: rgb(255, 235, 221)">
                                        NPWP : 96.484.859.2-413.000</p>
                                </div>
                            </div>
                        </div>
                    @endif
                    <div class="text-end">
                        <h1 class="fw-bold" style="color: #696cff; letter-spacing: 2px;">INVOICE</h1>
                        <div>
                            <span class="fw-bolder">#{{ $invoice->no_invoice }}</span>
                        </div>
                        <div class="mt-1">
                            <span class="text-muted">{{ Carbon\Carbon::parse($invoice->date)->format('d-m-Y') }}</span>
                        </div>
                    </div>
                </div>
            @endif
            <hr>
            <h5>Invoice To</h5>
            <div class="table-responsive text-nowrap">
                <table class="table table-bordered" style="border: 1px solid black;">
                    <tr>
                        <td rowspan="3" style="vertical-align: top; width: 60%;">
                            <div class="row">
                                <div class="col-2 fw-medium">
                                    <p class="mb-1">Bill To </p>
                                </div>
                                <div class="col-10">
                                    <p class="mb-1 fw-bolder">: {{ $quote->pic->client->company }}</p>
                                </div>
                                <div class="col-2 fw-medium">
                                    <p class="mb-1">PIC </p>
                                </div>
                                <div class="col-10">
                                    <p class="mb-1 fw-bolder">: {{ $quote->pic->name_pic }}</p>
                                </div>
                                <div class="col-2 fw-medium">
                                    <p class="mb-1">NPWP </p>
                                </div>
                                <div class="col-10">
                                    <p class="mb-1">: {{ $quote->pic->client->npwp }}</p>
                                </div>
                                <div class="col-2 fw-medium">
                                    <p class="mb-1">Phone </p>
                                </div>
                                <div class="col-10">
                                    <p class="mb-1">: {{ $quote->pic->client->phone }}</p>
                                </div>
                                <div class="col-2 fw-medium">
                                    <p class="mb-1">Address</p>
                                </div>
                                <div class="col-10">
                                    @if ($invoice->invoiceTo == '1')
                                        <pre
                                            style="font-size: 13px; font-family: Inter, sans-serif; max-width: 100%; overflow-x: auto; white-space: pre-wrap;">: {{ $quote->pic->client->address }}</pre>
                                    @else
                                        <pre
                                            style="font-size: 13px; font-family: Inter, sans-serif; max-width: 100%; overflow-x: auto; white-space: pre-wrap;">: {{ $quote->pic->client->subAddress }}</pre>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            <p>Purchase Order</p>
                        </td>
                        <td>
                            <p class="fs-6 text-black fw-bold m-0">{{ $invoice->no_po }}</p>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style=" background-color: #F9F9F9;" class="text-center">
                            <p class="fs-6 text-black fw-bold m-0">Term of Payment</p>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" class="text-center" style="height: 10px">
                            <pre class="mb-0"
                                style="font-size: 13px; font-family: Inter, sans-serif; max-width: 100%; overflow-x: auto; white-space: pre-wrap;">{{ $invoice->term }}</pre>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="mb-2">
                @if ($quote->type == 'Sparepart')
                    @php
                        $hasDisc     = $dquote->where('disc', '>', 0)->count() > 0;
                        $labelColspan = $hasDisc ? 3 : 2;
                    @endphp
                    <table class="table table-bordered m-0"
                        style="border: 1px solid rgb(60, 60, 60); border-collapse: collapse;">
                        <thead class="table-light text-center">
                            <tr>
                                <th style="width: 1%">No.</th>
                                <th style="width: 35%">Item Description</th>
                                <th style="width: 15%">Price</th>
                                <th style="width: 10%">Qty</th>
                                @if ($hasDisc)
                                    <th style="width: 4%">Disc</th>
                                @endif
                                @if ($quote->tax != 0)
                                    <th style="width: 15%">DPP</th>
                                @endif
                                <th style="width: 25%">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $totalPph = $invoice->pph ?? 0;
                                $no = 1;
                            @endphp
                            @foreach ($dquote as $product)
                                <tr style="font-size: 13px; border: none;">
                                    <td class="align-top" style="padding-bottom: 0px;">
                                        <p>
                                            {{ $no }}
                                        </p>
                                        @php
                                            $no++;
                                            $pph = ($product->amount * $product->pph) / 100;
                                            $totalPph += $pph;
                                            $dpp = ($product->amount * 11) / 12;
                                        @endphp
                                    </td>
                                    <td class="text-wrap align-top" style="padding-bottom: 0px;">
                                        <p class="mb-0 fw-semibold" style="font-size: 12px">
                                            {{ $product->equivalent->brand }} {{ $product->equivalent->pn }}
                                        </p>
                                        @if ($product->view == '1')
                                            <a href="{{ $product->equivalent->image }}" target="_blank"
                                                class=" underline-line">Description Click Here</a>
                                        @else
                                            <pre class="mb-0"
                                                style="font-size: 13px; font-family: Inter, sans-serif; max-width: 100%; overflow-x: auto; white-space: pre-wrap;">{{ $product->detail_product }}</pre>
                                        @endif
                                    </td>
                                    <td class="align-top text-end" style="padding-bottom: 0px;">
                                        <p>
                                            {{ number_format($product->price, 0, '', '.') }}
                                        </p>
                                    </td>
                                    <td class="align-top" style="padding-bottom: 0px;">
                                        <p>
                                            {{ $product->qty }} {{ $product->info_qty }}
                                        </p>
                                    </td>
                                    @if ($hasDisc)
                                        <td class="align-top">
                                            <p>
                                                {{ $product->disc }} %
                                            </p>
                                        </td>
                                    @endif
                                    @if ($quote->tax != 0)
                                        <td class="align-top text-end" style="padding-bottom: 0px;">
                                            <p>
                                                {{ number_format($dpp, 0, '', '.') }}
                                            </p>
                                        </td>
                                    @endif
                                    <td class="align-top text-end" style="padding-bottom: 0px;">
                                        <p>
                                            {{ number_format($product->amount, 0, '', '.') }}
                                        </p>
                                    </td>
                                </tr>
                            @endforeach

                            <tr class="fw-medium" style="font-size: 13px">
                                <td colspan="{{ $quote->tax != 0 ? '3' : '2' }}" rowspan="9" id="dynamicRows"
                                    style="border-bottom :none !important;">
                                </td>
                                <td colspan="{{ $labelColspan }}" id="price" class="text-end pl-4 py-0"
                                    style="padding-right: 10px !important;">
                                    <p class="m-0">
                                        {{ $quote->tax != 0 || $invoice->pph != 0 || $quote->shipping != 0 ? 'Subtotal' : 'Total' }}
                                    </p>
                                </td>
                                <td id="price" class="pr-4 py-0" style="padding-left: 0 !important;">
                                    <p class="text-end m-0">RP
                                        {{ number_format($quote->subtotal, 0, '', '.') }}</p>
                                </td>
                            </tr>
                            @php
                                if ($quote->pic->client->info == 'Reftech') {
                                    $bgColor = 'rgb(224, 248, 248)';
                                } else {
                                    $bgColor = 'rgb(255, 232, 210)';
                                }
                            @endphp

                            @if ($invoice->type == 'CT')
                                @if ($quote->diskon != 0)
                                    <tr class="fw-medium" style="font-size: 13px">
                                        <td colspan="{{ $labelColspan }}" class="text-end py-0"
                                            style="padding-right: 10px !important;">
                                            <p class="m-0">Discount</p>
                                        </td>
                                        <td class="pr-4 py-0" style="padding-left: 0 !important;">
                                            <p class="m-0 text-end">RP
                                                {{ number_format($quote->diskon, 0, '', '.') }}
                                            </p>
                                        </td>
                                    </tr>
                                    <tr class="fw-medium" style="font-size: 13px">
                                        <td colspan="{{ $labelColspan }}" class="text-end py-0"
                                            style="padding-right: 10px !important;">
                                            <p class="m-0">Total After Discount</p>
                                        </td>
                                        <td class="pr-4 py-0" style="padding-left: 0 !important;">
                                            <p class="m-0 text-end">RP
                                                {{ number_format($afterDisc, 0, '', '.') }}
                                            </p>
                                        </td>
                                    </tr>
                                    @if ($quote->tax != 0)
                                        <tr class="fw-medium" style="font-size: 13px">
                                            <td colspan="{{ $labelColspan }}" id="price" class="text-end pl-4 py-0"
                                                style="padding-right: 10px !important;">
                                                <p class="m-0">
                                                    DPP Atas PPN
                                                </p>
                                            </td>
                                            <td id="price" class="pr-4 py-0" style="padding-left: 0 !important;">
                                                @php
                                                    $dpp = ($afterDisc * 11) / 12;
                                                @endphp
                                                <p class="text-end m-0">RP
                                                    {{ number_format($dpp, 0, '', '.') }}</p>
                                            </td>
                                        </tr>
                                    @endif
                                @else
                                    @if ($quote->tax != 0)
                                        <tr class="fw-medium" style="font-size: 13px">
                                            <td colspan="{{ $labelColspan }}" id="price" class="text-end pl-4 py-0"
                                                style="padding-right: 10px !important;">
                                                <p class="m-0">
                                                    DPP Atas PPN
                                                </p>
                                            </td>
                                            <td id="price" class="pr-4 py-0" style="padding-left: 0 !important;">
                                                @php
                                                    $dpp = ($quote->subtotal * 11) / 12;
                                                @endphp
                                                <p class="text-end m-0">RP
                                                    {{ number_format($dpp, 0, '', '.') }}</p>
                                            </td>
                                        </tr>
                                    @endif
                                @endif
                                @if ($quote->tax != 0 || $totalPph > 0)
                                    <tr class="fw-medium py-0" style="font-size: 13px">
                                        <td colspan="{{ $labelColspan }}" class="text-end py-0"
                                            style="padding-right: 10px !important;">
                                            <p class="m-0">VAT {{ $quote->tax == '11' ? '12%' : '' }}</p>
                                        </td>
                                        <td class="pr-4 py-0" style="padding-left: 0 !important;">
                                            <p class="m-0 text-end">
                                                {{ $tax == '0' ? '0' : 'RP ' . number_format($tax, 0, '', '.') }}</p>
                                        </td>
                                    </tr>
                                    @if ($totalPph > 0)
                                        <tr class="fw-medium py-0" style="font-size: 13px">
                                            <td colspan="{{ $labelColspan }}" class="text-end py-0"
                                                style="padding-right: 10px !important;">
                                                <p class="m-0">PPH</p>
                                            </td>
                                            <td class="pr-4 py-0" style="padding-left: 0 !important;">
                                                <p class="m-0 text-end">
                                                    {{ $totalPph == '0' ? '0' : 'RP ' . number_format($totalPph, 0, '', '.') }}
                                                </p>
                                            </td>
                                        </tr>
                                    @endif
                                @endif
                                @if ($quote->shipping != 0)
                                    <tr class="fw-medium" style="font-size: 13px">
                                        <td colspan="{{ $labelColspan }}" class="text-end py-0"
                                            style="padding-right: 10px !important;">
                                            <p class="m-0">Shipping Cost</p>
                                        </td>
                                        <td class="pr-4 py-0" style="padding-left: 0 !important;">
                                            <p class="m-0 text-end">RP
                                                {{ number_format($quote->shipping, 0, '', '.') }}
                                            </p>
                                        </td>
                                    </tr>
                                @endif
                                @if ($quote->tax != 0 || $totalPph > 0 || $quote->shipping != 0)
                                    <tr class="fw-medium py-0" style="font-size: 13px;">
                                        <td colspan="{{ $labelColspan }}" class="text-end py-0"
                                            style="background-color: {{ $bgColor }}; padding-left:20px; padding-right:10px;">
                                            <p class="m-0 fw-bold">TOTAL</p>
                                        </td>
                                        <td class="pr-4 py-0"
                                            style="background-color: {{ $bgColor }}; padding-right:20px;">
                                            <p class="m-0 text-end fw-bold">
                                                {{ 'RP ' . number_format($quote->harga_total - $totalPph, 0, '', '.') }}
                                            </p>
                                        </td>
                                    </tr>
                                @endif
                            @elseif ($invoice->type == 'DP')
                                @php
                                    $amount1 = $payments[0]->amount / (1 + $quote->tax / 100);
                                    $vat = $amount1 * ($quote->tax / 100);
                                    $totalwithpph = $payments[0]->amount - $totalPph;
                                @endphp
                                @if ($quote->diskon != 0)
                                    <tr class="fw-medium" style="font-size: 13px">
                                        <td colspan="{{ $labelColspan }}" class="text-end py-0"
                                            style="padding-right: 10px !important;">
                                            <p class="m-0">Discount</p>
                                        </td>
                                        <td class="pr-4 py-0" style="padding-left: 0 !important;">
                                            <p class="m-0 text-end">RP
                                                {{ number_format($quote->diskon, 0, '', '.') }}
                                            </p>
                                        </td>
                                    </tr>
                                    <tr class="fw-medium" style="font-size: 13px">
                                        <td colspan="{{ $labelColspan }}" class="text-end py-0"
                                            style="padding-right: 10px !important;">
                                            <p class="m-0">Total After Discount</p>
                                        </td>
                                        <td class="pr-4 py-0" style="padding-left: 0 !important;">
                                            <p class="m-0 text-end">RP
                                                {{ number_format($afterDisc, 0, '', '.') }}
                                            </p>
                                        </td>
                                    </tr>
                                @endif
                                <tr class="fw-medium" style="font-size: 13px">
                                    <td colspan="{{ $labelColspan }}" class="text-end py-0 px-0">
                                        <p class="m-0"
                                            style="background-color: yellow; padding-left:20px; padding-right:10px;">
                                            {{ $payments[0]->note }}
                                            {{ $payments[0]->percent }}%:</p>
                                    </td>
                                    <td class="px-0 py-0" style="padding-left: 0 !important;">
                                        <p class="fw-medium m-0 text-end"
                                            style="background-color: yellow; padding-right:20px;">
                                            RP
                                            {{ number_format($amount1, 0, '', '.') }}</p>
                                    </td>
                                </tr>
                                @if ($quote->tax != 0)
                                    <tr class="fw-medium" style="font-size: 13px">
                                        <td colspan="{{ $labelColspan }}" id="price" class="text-end pl-4 py-0"
                                            style="padding-right: 10px !important;">
                                            <p class="m-0">
                                                DPP Atas PPN
                                            </p>
                                        </td>
                                        <td id="price" class="pr-4 py-0" style="padding-left: 0 !important;">
                                            @php
                                                $dpp = ($amount1 * 11) / 12;
                                            @endphp
                                            <p class="text-end m-0">RP
                                                {{ number_format($dpp, 0, '', '.') }}</p>
                                        </td>
                                    </tr>
                                    <tr class="fw-medium py-0" style="font-size: 13px">
                                        <td colspan="{{ $labelColspan }}" class="text-end py-0"
                                            style="padding-right: 10px !important;">
                                            <p class="m-0">VAT {{ $quote->tax == '11' ? '12%' : '' }}</p>
                                        </td>
                                        <td class="pr-4 py-0" style="padding-left: 0 !important;">
                                            <p class="m-0 text-end">
                                                {{ $vat == '0' ? '0' : 'RP ' . number_format($vat, 0, '', '.') }}</p>
                                        </td>
                                    </tr>
                                    @if ($totalPph > 0)
                                        <tr class="fw-medium py-0" style="font-size: 13px">
                                            <td colspan="{{ $labelColspan }}" class="text-end py-0"
                                                style="padding-right: 10px !important;">
                                                <p class="m-0">PPH</p>
                                            </td>
                                            <td class="pr-4 py-0" style="padding-left: 0 !important;">
                                                <p class="m-0 text-end">
                                                    {{ $totalPph == '0' ? '0' : 'RP ' . number_format($totalPph, 0, '', '.') }}
                                                </p>
                                            </td>
                                        </tr>
                                    @endif
                                    @if ($quote->shipping != 0)
                                        <tr class="fw-medium" style="font-size: 13px">
                                            <td colspan="{{ $labelColspan }}" class="text-end py-0"
                                                style="padding-right: 10px !important;">
                                                <p class="m-0">Shipping Cost</p>
                                            </td>
                                            <td class="pr-4 py-0" style="padding-left: 0 !important;">
                                                <p class="m-0 text-end">RP
                                                    {{ number_format($quote->shipping, 0, '', '.') }}
                                                </p>
                                            </td>
                                        </tr>
                                    @endif
                                    <tr class="fw-medium py-0" style="font-size: 13px">
                                        <td colspan="{{ $labelColspan }}" class="text-end py-0"
                                            style="background-color: {{ $bgColor }}; padding-left:20px; padding-right:10px;">
                                            <p class="m-0 fw-bold">Total Include VAT</p>
                                        </td>
                                        <td class="pr-4 py-0"
                                            style="background-color: {{ $bgColor }}; padding-right:20px;">
                                            <p class="m-0 text-end fw-bold">
                                                Rp {{ number_format($totalwithpph, 0, '', '.') }}
                                            </p>
                                        </td>
                                    </tr>
                                @else
                                    @if ($quote->shipping != 0)
                                        <tr class="fw-medium" style="font-size: 13px">
                                            <td colspan="{{ $labelColspan }}" class="text-end py-0"
                                                style="padding-right: 10px !important;">
                                                <p class="m-0">Shipping Cost</p>
                                            </td>
                                            <td class="pr-4 py-0" style="padding-left: 0 !important;">
                                                <p class="m-0 text-end">RP
                                                    {{ number_format($quote->shipping, 0, '', '.') }}
                                                </p>
                                            </td>
                                        </tr>
                                    @endif
                                    <tr class="fw-medium py-0" style="font-size: 13px">
                                        <td colspan="{{ $labelColspan }}" class="text-end py-0"
                                            style="background-color: {{ $bgColor }}; padding-left:20px; padding-right:10px;">
                                            <p class="m-0">Total</p>
                                        </td>
                                        <td class="pr-4 py-0"
                                            style="background-color: {{ $bgColor }}; padding-right:20px;">
                                            <p class="m-0 text-end fw-bold">
                                                {{ number_format($payments[0]->amount, 0, '', '.') }}
                                            </p>
                                        </td>
                                    </tr>
                                @endif
                            @elseif ($invoice->type == 'BP')
                                @php
                                    $amount1 = $payments[0]->amount / (1 + $quote->tax / 100);
                                    $amount2 = $payments[1]?->amount / (1 + $quote->tax / 100);
                                    $vat = $amount2 * ($quote->tax / 100);
                                @endphp
                                @if ($quote->diskon != 0)
                                    <tr class="fw-medium" style="font-size: 13px">
                                        <td colspan="{{ $labelColspan }}" class="text-end py-0"
                                            style="padding-right: 10px !important;">
                                            <p class="m-0">Discount</p>
                                        </td>
                                        <td class="pr-4 py-0" style="padding-left: 0 !important;">
                                            <p class="m-0 text-end">RP
                                                {{ number_format($quote->diskon, 0, '', '.') }}
                                            </p>
                                        </td>
                                    </tr>
                                    <tr class="fw-medium" style="font-size: 13px">
                                        <td colspan="{{ $labelColspan }}" class="text-end py-0"
                                            style="padding-right: 10px !important;">
                                            <p class="m-0">Total After Discount</p>
                                        </td>
                                        <td class="pr-4 py-0" style="padding-left: 0 !important;">
                                            <p class="m-0 text-end">RP
                                                {{ number_format($afterDisc, 0, '', '.') }}
                                            </p>
                                        </td>
                                    </tr>
                                @endif
                                <tr class="fw-medium" style="font-size: 13px">
                                    <td colspan="{{ $labelColspan }}" class="text-end py-0" style="padding-right: 10px !important;">
                                        <p class="m-0">
                                            {{ $payments[0]->note }}
                                            {{ $payments[0]->percent }}%:</p>
                                    </td>
                                    <td class="pr-4 py-0" style="padding-left: 0 !important;">
                                        <p class="m-0 text-end">
                                            RP
                                            {{ number_format($amount1, 0, '', '.') }}</p>
                                    </td>
                                </tr>
                                <tr class="fw-medium" style="font-size: 13px">
                                    <td colspan="{{ $labelColspan }}" class="text-end py-0 px-0">
                                        <p class="m-0"
                                            style="background-color: yellow; padding-left:20px; padding-right:10px;">
                                            {{ $payments[1]?->note }}
                                            {{ $payments[1]?->percent }}%:</p>
                                    </td>
                                    <td class="px-0 py-0" style="padding-left: 0 !important;">
                                        <p class="m-0 text-end" style="background-color: yellow; padding-right:20px;">
                                            RP
                                            {{ number_format($amount2, 0, '', '.') }}</p>
                                    </td>
                                </tr>
                                @if ($totalPph > 0)
                                    <tr class="fw-medium py-0" style="font-size: 13px">
                                        <td colspan="{{ $labelColspan }}" class="text-end py-0"
                                            style="padding-right: 10px !important;">
                                            <p class="m-0">PPH</p>
                                        </td>
                                        <td class="pr-4 py-0" style="padding-left: 0 !important;">
                                            <p class="m-0 text-end">
                                                {{ $totalPph == '0' ? '0' : 'RP ' . number_format($totalPph, 0, '', '.') }}
                                            </p>
                                        </td>
                                    </tr>
                                @endif
                                @php
                                    $totalwithpph = $payments[1]?->amount - $totalPph;
                                @endphp
                                @if ($quote->tax != 0)
                                    <tr class="fw-medium" style="font-size: 13px">
                                        <td colspan="{{ $labelColspan }}" id="price" class="text-end pl-4 py-0"
                                            style="padding-right: 10px !important;">
                                            <p class="m-0">
                                                DPP Atas PPN
                                            </p>
                                        </td>
                                        <td id="price" class="pr-4 py-0" style="padding-left: 0 !important;">
                                            @php
                                                $dpp = ($amount2 * 11) / 12;
                                            @endphp
                                            <p class="text-end m-0">RP
                                                {{ number_format($dpp, 0, '', '.') }}</p>
                                        </td>
                                    </tr>
                                    <tr class="fw-medium py-0" style="font-size: 13px">
                                        <td colspan="{{ $labelColspan }}" class="text-end py-0"
                                            style="padding-right: 10px !important;">
                                            <p class="m-0">VAT {{ $quote->tax == '11' ? '12%' : '' }}</p>
                                        </td>
                                        <td class="pr-4 py-0" style="padding-left: 0 !important;">
                                            <p class="m-0 text-end">
                                                {{ $vat == '0' ? '0' : 'RP ' . number_format($vat, 0, '', '.') }}</p>
                                        </td>
                                    </tr>
                                    @if ($quote->shipping != 0)
                                        <tr class="fw-medium" style="font-size: 13px">
                                            <td colspan="{{ $labelColspan }}" class="text-end py-0"
                                                style="padding-right: 10px !important;">
                                                <p class="m-0">Shipping Cost</p>
                                            </td>
                                            <td class="pr-4 py-0" style="padding-left: 0 !important;">
                                                <p class="m-0 text-end">RP
                                                    {{ number_format($quote->shipping, 0, '', '.') }}
                                                </p>
                                            </td>
                                        </tr>
                                    @endif
                                    <tr class="fw-medium py-0" style="font-size: 13px">
                                        <td colspan="{{ $labelColspan }}" class="text-end py-0"
                                            style="background-color: {{ $bgColor }}; padding-left:20px; padding-right:10px;">
                                            <p class="m-0 fw-bold">Total Include VAT</p>
                                        </td>
                                        <td class="pr-4 py-0"
                                            style="background-color: {{ $bgColor }}; padding-right:20px;">
                                            <p class="m-0 text-end fw-bold">
                                                Rp {{ number_format($totalwithpph, 0, '', '.') }}
                                            </p>
                                        </td>
                                    </tr>
                                @else
                                    @if ($quote->shipping != 0)
                                        <tr class="fw-medium" style="font-size: 13px">
                                            <td colspan="{{ $labelColspan }}" class="text-end py-0"
                                                style="padding-right: 10px !important;">
                                                <p class="m-0">Shipping Cost</p>
                                            </td>
                                            <td class="pr-4 py-0" style="padding-left: 0 !important;">
                                                <p class="m-0 text-end">RP
                                                    {{ number_format($quote->shipping, 0, '', '.') }}
                                                </p>
                                            </td>
                                        </tr>
                                    @endif
                                    <tr class="fw-medium py-0" style="font-size: 13px">
                                        <td colspan="{{ $labelColspan }}" class="text-end py-0"
                                            style="background-color: {{ $bgColor }}; padding-left:20px; padding-right:10px;">
                                            <p class="m-0">Total</p>
                                        </td>
                                        <td class="pr-4 py-0"
                                            style="background-color: {{ $bgColor }}; padding-right:20px;">
                                            <p class="m-0 text-end fw-bold">
                                                {{ number_format($payments[1]?->amount, 0, '', '.') }}
                                            </p>
                                        </td>
                                    </tr>
                                @endif
                            @else
                                @if ($quote->diskon != 0)
                                    <tr class="fw-medium" style="font-size: 13px">
                                        <td colspan="{{ $labelColspan }}" class="text-end py-0"
                                            style="padding-right: 10px !important;">
                                            <p class="m-0">Discount</p>
                                        </td>
                                        <td class="pr-4 py-0" style="padding-left: 0 !important;">
                                            <p class="m-0 text-end">RP
                                                {{ number_format($quote->diskon, 0, '', '.') }}
                                            </p>
                                        </td>
                                    </tr>
                                    <tr class="fw-medium" style="font-size: 13px">
                                        <td colspan="{{ $labelColspan }}" class="text-end py-0"
                                            style="padding-right: 10px !important;">
                                            <p class="m-0">Total After Discount</p>
                                        </td>
                                        <td class="pr-4 py-0" style="padding-left: 0 !important;">
                                            <p class="m-0 text-end">RP
                                                {{ number_format($afterDisc, 0, '', '.') }}
                                            </p>
                                        </td>
                                    </tr>
                                @endif
                                @foreach ($payments as $pay)
                                    @php
                                        $amount = $pay->amount / (1 + $quote->tax / 100);
                                        $vat = $amount * ($quote->tax / 100);
                                        $payamount = $pay->amount;
                                        $totalwithpph = $pay->amount - $totalPph;
                                    @endphp
                                    @if (count($payments) > 1 || $invoice->type != 'CT')
                                        <tr class="fw-medium" style="font-size: 13px">
                                            <td colspan="{{ $labelColspan }}" class="text-end py-0 px-0">
                                                <p class="m-0"
                                                    style="{{ $loop->last ? 'background-color: yellow;' : '' }} padding-left:20px; padding-right:10px;">
                                                    {{ $pay->note }}
                                                    {{ $pay->percent }}%:</p>
                                            </td>
                                            <td class="px-0 py-0" style="padding-left: 0 !important;">
                                                <p class="fw-medium m-0 text-end"
                                                    style="{{ $loop->last ? 'background-color: yellow;' : '' }} padding-right:20px;">
                                                    RP
                                                    {{ number_format($amount, 0, '', '.') }}</p>
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                                @if ($quote->tax != 0)
                                    <tr class="fw-medium" style="font-size: 13px">
                                        <td colspan="{{ $labelColspan }}" id="price" class="text-end pl-4 py-0"
                                            style="padding-right: 10px !important;">
                                            <p class="m-0">
                                                DPP Atas PPN
                                            </p>
                                        </td>
                                        <td id="price" class="pr-4 py-0" style="padding-left: 0 !important;">
                                            @php
                                                $dpp = ($amount * 11) / 12;
                                            @endphp
                                            <p class="text-end m-0">RP
                                                {{ number_format($dpp, 0, '', '.') }}</p>
                                        </td>
                                    </tr>
                                    <tr class="fw-medium py-0" style="font-size: 13px">
                                        <td colspan="{{ $labelColspan }}" class="text-end py-0"
                                            style="padding-right: 10px !important;">
                                            <p class="m-0">VAT {{ $quote->tax == '11' ? '12%' : '' }}</p>
                                        </td>
                                        <td class="pr-4 py-0" style="padding-left: 0 !important;">
                                            <p class="m-0 text-end">
                                                {{ $vat == '0' ? '0' : 'RP ' . number_format($vat, 0, '', '.') }}</p>
                                        </td>
                                    </tr>
                                    @if ($totalPph > 0)
                                        <tr class="fw-medium py-0" style="font-size: 13px">
                                            <td colspan="{{ $labelColspan }}" class="text-end py-0"
                                                style="padding-right: 10px !important;">
                                                <p class="m-0">PPH</p>
                                            </td>
                                            <td class="pr-4 py-0" style="padding-left: 0 !important;">
                                                <p class="m-0 text-end">
                                                    {{ $totalPph == '0' ? '0' : 'RP ' . number_format($totalPph, 0, '', '.') }}
                                                </p>
                                            </td>
                                        </tr>
                                    @endif
                                    @if ($quote->shipping != 0)
                                        <tr class="fw-medium" style="font-size: 13px">
                                            <td colspan="{{ $labelColspan }}" class="text-end py-0"
                                                style="padding-right: 10px !important;">
                                                <p class="m-0">Shipping Cost</p>
                                            </td>
                                            <td class="pr-4 py-0" style="padding-left: 0 !important;">
                                                <p class="m-0 text-end">RP
                                                    {{ number_format($quote->shipping, 0, '', '.') }}
                                                </p>
                                            </td>
                                        </tr>
                                    @endif
                                    <tr class="fw-medium py-0" style="font-size: 13px">
                                        <td colspan="{{ $labelColspan }}" class="text-end py-0"
                                            style="background-color: {{ $bgColor }}; padding-left:20px; padding-right:10px;">
                                            <p class="m-0 fw-bold">Total Include VAT</p>
                                        </td>
                                        <td class="pr-4 py-0"
                                            style="background-color: {{ $bgColor }}; padding-right:20px;">
                                            <p class="m-0 text-end fw-bold">
                                                Rp {{ number_format($totalwithpph, 0, '', '.') }}
                                            </p>
                                        </td>
                                    </tr>
                                @else
                                    @if ($quote->shipping != 0)
                                        <tr class="fw-medium" style="font-size: 13px">
                                            <td colspan="{{ $labelColspan }}" class="text-end py-0"
                                                style="padding-right: 10px !important;">
                                                <p class="m-0">Shipping Cost</p>
                                            </td>
                                            <td class="pr-4 py-0" style="padding-left: 0 !important;">
                                                <p class="m-0 text-end">RP
                                                    {{ number_format($quote->shipping, 0, '', '.') }}
                                                </p>
                                            </td>
                                        </tr>
                                    @endif
                                    <tr class="fw-medium py-0" style="font-size: 13px">
                                        <td colspan="{{ $labelColspan }}" class="text-end py-0"
                                            style="background-color: {{ $bgColor }}; padding-left:20px; padding-right:10px;">
                                            <p class="m-0">Total</p>
                                        </td>
                                        <td class="pr-4 py-0"
                                            style="background-color: {{ $bgColor }}; padding-right:20px;">
                                            <p class="m-0 text-end fw-bold">
                                                {{ number_format($payamount, 0, '', '.') }}
                                            </p>
                                        </td>
                                    </tr>
                                @endif
                            @endif
                        </tbody>
                    </table>
                @else
                    @php
                        $hasDisc        = $subQuote->flatMap(fn($s) => $s->detail)->where('disc', '>', 0)->count() > 0;
                        $labelColspan   = $hasDisc ? 2 : 1;
                        $subtitleColspan = ($quote->tax != 0 ? 6 : 5) - ($hasDisc ? 0 : 1);
                    @endphp
                    <table class="table table-bordered m-0"
                        style="border: 1px solid rgb(60, 60, 60); border-collapse: collapse;">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 1%">No.</th>
                                <th style="width: 35%">Item Description</th>
                                <th style="width: 15%">Price</th>
                                <th>Qty</th>
                                @if ($hasDisc)
                                    <th>Disc</th>
                                @endif
                                @if ($quote->tax != 0)
                                    <th style="width: 15%">DPP</th>
                                @endif
                                <th style="width: 25%">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                             @php
                                 $totalPph = $invoice->pph ?? 0;
                             @endphp
                            @php
                                $abjad = 64;
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
                                    <td class="text-nowrap align-top" colspan="{{ $subtitleColspan }}"
                                        style="border-bottom:none !important; background-color: #f0f0f0;">
                                        <p class="fw-bold mb-0">{{ $subJudul->subtitle }}</p>
                                    </td>
                                </tr>
                                @foreach ($subJudul->detail as $product)
                                    @php
                                        $no++;
                                        $pph = ($product->amount * $product->pph) / 100;
                                        $totalPph += $pph;
                                        $dpp = ($product->amount * 11) / 12;
                                    @endphp
                                    <tr style="font-size: 13px; border-bottom:none !important;">
                                        <td class="align-top" style="border-bottom:none !important;">
                                            <p class="mb-1">{{ $no }}</p>
                                        </td>
                                        <td class="text-nowrap align-top"
                                            style="border-bottom:none !important;">
                                            <pre class="mb-0"
                                                style="font-size: 13px; font-family: Inter, sans-serif; max-width: 100%; overflow-x: auto; white-space: pre-wrap;">{{ $product->product }}</pre>
                                        </td>
                                        <td class="align-top text-end" style="padding-bottom: 0px;">
                                            <p>
                                                {{ number_format($product->price, 0, '', '.') }}
                                            </p>
                                        </td>
                                        <td class="align-top" style="border-bottom:none !important;">
                                            <p class="mb-0">{{ $product->qty }} {{ $product->info_qty }}</p>
                                        </td>
                                        @if ($hasDisc)
                                            <td class="align-top" style="border-bottom:none !important;">
                                                <p class="mb-0">{{ $product->disc }} %</p>
                                            </td>
                                        @endif
                                        @if ($quote->tax != 0)
                                            <td class="align-top text-end" style="padding-bottom: 0px;">
                                                <p>
                                                    {{ number_format($dpp, 0, '', '.') }}
                                                </p>
                                            </td>
                                        @endif
                                        <td class="align-top text-end" style="border-bottom:none !important;">
                                            <p class="mb-0">RP {{ number_format($product->amount, 0, '', '.') }}</p>
                                        </td>
                                    </tr>
                                @endforeach
                            @endforeach

                            <tr class="fw-medium" style="font-size: 13px">
                                <td colspan="{{ $quote->tax != 0 ? '4' : '3' }}" rowspan="9" id="dynamicRows"
                                    style="border-bottom :none !important;">
                                </td>
                                <td colspan="{{ $labelColspan }}" id="price" class="text-end pl-4 py-0"
                                    style="padding-right: 10px !important;">
                                    <p class="m-0">
                                        {{ $quote->tax != 0 || $invoice->pph != 0 || $quote->shipping != 0 ? 'Subtotal' : 'Total' }}
                                    </p>
                                </td>
                                <td id="price" class="pr-4 py-0" style="padding-left: 0 !important;">
                                    <p class="text-end m-0">RP
                                        {{ number_format($quote->subtotal, 0, '', '.') }}</p>
                                </td>
                            </tr>
                            @php
                                if ($quote->pic->client->info == 'Reftech') {
                                    $bgColor = 'rgb(224, 248, 248)';
                                } else {
                                    $bgColor = 'rgb(255, 232, 210)';
                                }
                            @endphp
                            @if ($invoice->type == 'CT')
                                @if ($quote->diskon != 0)
                                    <tr class="fw-medium" style="font-size: 13px">
                                        <td colspan="{{ $labelColspan }}" class="text-end py-0"
                                            style="padding-right: 10px !important;">
                                            <p class="m-0">Discount</p>
                                        </td>
                                        <td class="pr-4 py-0" style="padding-left: 0 !important;">
                                            <p class="m-0 text-end">RP
                                                {{ number_format($quote->diskon, 0, '', '.') }}
                                            </p>
                                        </td>
                                    </tr>
                                    <tr class="fw-medium" style="font-size: 13px">
                                        <td colspan="{{ $labelColspan }}" class="text-end py-0"
                                            style="padding-right: 10px !important;">
                                            <p class="m-0">Total After Discount</p>
                                        </td>
                                        <td class="pr-4 py-0" style="padding-left: 0 !important;">
                                            <p class="m-0 text-end">RP
                                                {{ number_format($afterDisc, 0, '', '.') }}
                                            </p>
                                        </td>
                                    </tr>
                                    @if ($quote->tax != 0)
                                        <tr class="fw-medium" style="font-size: 13px">
                                            <td colspan="{{ $labelColspan }}" id="price" class="text-end pl-4 py-0"
                                                style="padding-right: 10px !important;">
                                                <p class="m-0">
                                                    DPP Atas PPN
                                                </p>
                                            </td>
                                            <td id="price" class="pr-4 py-0" style="padding-left: 0 !important;">
                                                @php
                                                    $dpp = ($afterDisc * 11) / 12;
                                                @endphp
                                                <p class="text-end m-0">RP
                                                    {{ number_format($dpp, 0, '', '.') }}</p>
                                            </td>
                                        </tr>
                                    @endif
                                @else
                                    @if ($quote->tax != 0)
                                        <tr class="fw-medium" style="font-size: 13px">
                                            <td colspan="{{ $labelColspan }}" id="price" class="text-end pl-4 py-0"
                                                style="padding-right: 10px !important;">
                                                <p class="m-0">
                                                    DPP Atas PPN
                                                </p>
                                            </td>
                                            <td id="price" class="pr-4 py-0" style="padding-left: 0 !important;">
                                                @php
                                                    $dpp = ($quote->subtotal * 11) / 12;
                                                @endphp
                                                <p class="text-end m-0">RP
                                                    {{ number_format($dpp, 0, '', '.') }}</p>
                                            </td>
                                        </tr>
                                    @endif
                                @endif
                                @if ($quote->tax != 0 || $totalPph > 0)
                                    <tr class="fw-medium py-0" style="font-size: 13px">
                                        <td colspan="{{ $labelColspan }}" class="text-end py-0"
                                            style="padding-right: 10px !important;">
                                            <p class="m-0">VAT {{ $quote->tax == '11' ? '12%' : '' }}</p>
                                        </td>
                                        <td class="pr-4 py-0" style="padding-left: 0 !important;">
                                            <p class="m-0 text-end">
                                                {{ $tax == '0' ? '0' : 'RP ' . number_format($tax, 0, '', '.') }}</p>
                                        </td>
                                    </tr>
                                    @if ($totalPph > 0)
                                        <tr class="fw-medium py-0" style="font-size: 13px">
                                            <td colspan="{{ $labelColspan }}" class="text-end py-0"
                                                style="padding-right: 10px !important;">
                                                <p class="m-0">PPH</p>
                                            </td>
                                            <td class="pr-4 py-0" style="padding-left: 0 !important;">
                                                <p class="m-0 text-end">
                                                    {{ $totalPph == '0' ? '0' : 'RP ' . number_format($totalPph, 0, '', '.') }}
                                                </p>
                                            </td>
                                        </tr>
                                    @endif
                                @endif
                                @if ($quote->shipping != 0)
                                    <tr class="fw-medium" style="font-size: 13px">
                                        <td colspan="{{ $labelColspan }}" class="text-end py-0"
                                            style="padding-right: 10px !important;">
                                            <p class="m-0">Shipping Cost</p>
                                        </td>
                                        <td class="pr-4 py-0" style="padding-left: 0 !important;">
                                            <p class="m-0 text-end">RP
                                                {{ number_format($quote->shipping, 0, '', '.') }}
                                            </p>
                                        </td>
                                    </tr>
                                @endif
                                @if ($quote->tax != 0 || $totalPph > 0 || $quote->shipping != 0)
                                    <tr class="fw-medium py-0" style="font-size: 13px">
                                        <td colspan="{{ $labelColspan }}" class="text-end py-0"
                                            style="background-color: {{ $bgColor }}; padding-left:20px; padding-right:10px;">
                                            <p class="m-0 fw-bold">TOTAL</p>
                                        </td>
                                        <td class="pr-4 py-0"
                                            style="background-color: {{ $bgColor }}; padding-right:20px;">
                                            <p class="m-0 text-end fw-bold">
                                                {{ 'RP ' . number_format($quote->harga_total - $totalPph, 0, '', '.') }}
                                            </p>
                                        </td>
                                    </tr>
                                @endif
                            @elseif ($invoice->type == 'DP')
                                @php
                                    $amount1 = $payments[0]->amount / (1 + $quote->tax / 100);
                                    $vat = $amount1 * ($quote->tax / 100);
                                    $totalwithpph = $payments[0]->amount - $totalPph;
                                @endphp
                                @if ($quote->diskon != 0)
                                    <tr class="fw-medium" style="font-size: 13px">
                                        <td colspan="{{ $labelColspan }}" class="text-end py-0"
                                            style="padding-right: 10px !important;">
                                            <p class="m-0">Discount</p>
                                        </td>
                                        <td class="pr-4 py-0" style="padding-left: 0 !important;">
                                            <p class="m-0 text-end">RP
                                                {{ number_format($quote->diskon, 0, '', '.') }}
                                            </p>
                                        </td>
                                    </tr>
                                    <tr class="fw-medium" style="font-size: 13px">
                                        <td colspan="{{ $labelColspan }}" class="text-end py-0"
                                            style="padding-right: 10px !important;">
                                            <p class="m-0">Total After Discount</p>
                                        </td>
                                        <td class="pr-4 py-0" style="padding-left: 0 !important;">
                                            <p class="m-0 text-end">RP
                                                {{ number_format($afterDisc, 0, '', '.') }}
                                            </p>
                                        </td>
                                    </tr>
                                @endif
                                <tr class="fw-medium" style="font-size: 13px">
                                    <td colspan="{{ $labelColspan }}" class="text-end py-0 px-0">
                                        <p class="m-0"
                                            style="background-color: yellow; padding-left:20px; padding-right:10px;">
                                            {{ $payments[0]->note }}
                                            {{ $payments[0]->percent }}%:</p>
                                    </td>
                                    <td class="px-0 py-0" style="padding-left: 0 !important;">
                                        <p class="fw-medium m-0 text-end"
                                            style="background-color: yellow; padding-right:20px;">
                                            RP
                                            {{ number_format($amount1, 0, '', '.') }}</p>
                                    </td>
                                </tr>
                                @if ($quote->tax != 0)
                                    <tr class="fw-medium" style="font-size: 13px">
                                        <td colspan="{{ $labelColspan }}" id="price" class="text-end pl-4 py-0"
                                            style="padding-right: 10px !important;">
                                            <p class="m-0">
                                                DPP Atas PPN
                                            </p>
                                        </td>
                                        <td id="price" class="pr-4 py-0" style="padding-left: 0 !important;">
                                            @php
                                                $dpp = ($amount1 * 11) / 12;
                                            @endphp
                                            <p class="text-end m-0">RP
                                                {{ number_format($dpp, 0, '', '.') }}</p>
                                        </td>
                                    </tr>
                                    <tr class="fw-medium py-0" style="font-size: 13px">
                                        <td colspan="{{ $labelColspan }}" class="text-end py-0"
                                            style="padding-right: 10px !important;">
                                            <p class="m-0">VAT {{ $quote->tax == '11' ? '12%' : '' }}</p>
                                        </td>
                                        <td class="pr-4 py-0" style="padding-left: 0 !important;">
                                            <p class="m-0 text-end">
                                                {{ $vat == '0' ? '0' : 'RP ' . number_format($vat, 0, '', '.') }}</p>
                                        </td>
                                    </tr>
                                    @if ($totalPph > 0)
                                        <tr class="fw-medium py-0" style="font-size: 13px">
                                            <td colspan="{{ $labelColspan }}" class="text-end py-0"
                                                style="padding-right: 10px !important;">
                                                <p class="m-0">PPH</p>
                                            </td>
                                            <td class="pr-4 py-0" style="padding-left: 0 !important;">
                                                <p class="m-0 text-end">
                                                    {{ $totalPph == '0' ? '0' : 'RP ' . number_format($totalPph, 0, '', '.') }}
                                                </p>
                                            </td>
                                        </tr>
                                    @endif
                                    @if ($quote->shipping != 0)
                                        <tr class="fw-medium" style="font-size: 13px">
                                            <td colspan="{{ $labelColspan }}" class="text-end py-0"
                                                style="padding-right: 10px !important;">
                                                <p class="m-0">Shipping Cost</p>
                                            </td>
                                            <td class="pr-4 py-0" style="padding-left: 0 !important;">
                                                <p class="m-0 text-end">RP
                                                    {{ number_format($quote->shipping, 0, '', '.') }}
                                                </p>
                                            </td>
                                        </tr>
                                    @endif
                                    <tr class="fw-medium py-0" style="font-size: 13px">
                                        <td colspan="{{ $labelColspan }}" class="text-end py-0"
                                            style="background-color: {{ $bgColor }}; padding-left:20px; padding-right:10px;">
                                            <p class="m-0 fw-bold">Total Include VAT</p>
                                        </td>
                                        <td class="pr-4 py-0"
                                            style="background-color: {{ $bgColor }}; padding-right:20px;">
                                            <p class="m-0 text-end fw-bold">
                                                Rp {{ number_format($totalwithpph, 0, '', '.') }}
                                            </p>
                                        </td>
                                    </tr>
                                @else
                                    @if ($quote->shipping != 0)
                                        <tr class="fw-medium" style="font-size: 13px">
                                            <td colspan="{{ $labelColspan }}" class="text-end py-0"
                                                style="padding-right: 10px !important;">
                                                <p class="m-0">Shipping Cost</p>
                                            </td>
                                            <td class="pr-4 py-0" style="padding-left: 0 !important;">
                                                <p class="m-0 text-end">RP
                                                    {{ number_format($quote->shipping, 0, '', '.') }}
                                                </p>
                                            </td>
                                        </tr>
                                    @endif
                                    <tr class="fw-medium py-0" style="font-size: 13px">
                                        <td colspan="{{ $labelColspan }}" class="text-end py-0"
                                            style="background-color: {{ $bgColor }}; padding-left:20px; padding-right:10px;">
                                            <p class="m-0">Total</p>
                                        </td>
                                        <td class="pr-4 py-0"
                                            style="background-color: {{ $bgColor }}; padding-right:20px;">
                                            <p class="m-0 text-end fw-bold">
                                                {{ number_format($payments[0]->amount, 0, '', '.') }}
                                            </p>
                                        </td>
                                    </tr>
                                @endif
                            @elseif ($invoice->type == 'BP')
                                @php
                                    $amount1 = $payments[0]->amount / (1 + $quote->tax / 100);
                                    $amount2 = $payments[1]?->amount / (1 + $quote->tax / 100);
                                    $vat = $amount2 * ($quote->tax / 100);
                                @endphp
                                @if ($quote->diskon != 0)
                                    <tr class="fw-medium" style="font-size: 13px">
                                        <td colspan="{{ $labelColspan }}" class="text-end py-0"
                                            style="padding-right: 10px !important;">
                                            <p class="m-0">Discount</p>
                                        </td>
                                        <td class="pr-4 py-0" style="padding-left: 0 !important;">
                                            <p class="m-0 text-end">RP
                                                {{ number_format($quote->diskon, 0, '', '.') }}
                                            </p>
                                        </td>
                                    </tr>
                                    <tr class="fw-medium" style="font-size: 13px">
                                        <td colspan="{{ $labelColspan }}" class="text-end py-0"
                                            style="padding-right: 10px !important;">
                                            <p class="m-0">Total After Discount</p>
                                        </td>
                                        <td class="pr-4 py-0" style="padding-left: 0 !important;">
                                            <p class="m-0 text-end">RP
                                                {{ number_format($afterDisc, 0, '', '.') }}
                                            </p>
                                        </td>
                                    </tr>
                                @endif
                                <tr class="fw-medium" style="font-size: 13px">
                                    <td colspan="{{ $labelColspan }}" class="text-end py-0" style="padding-right: 10px !important;">
                                        <p class="m-0">
                                            {{ $payments[0]->note }}
                                            {{ $payments[0]->percent }}%:</p>
                                    </td>
                                    <td class="pr-4 py-0" style="padding-left: 0 !important;">
                                        <p class="m-0 text-end">
                                            RP
                                            {{ number_format($amount1, 0, '', '.') }}</p>
                                    </td>
                                </tr>
                                <tr class="fw-medium" style="font-size: 13px">
                                    <td colspan="{{ $labelColspan }}" class="text-end py-0 px-0">
                                        <p class="m-0"
                                            style="background-color: yellow; padding-left:20px; padding-right:10px;">
                                            {{ $payments[1]?->note }}
                                            {{ $payments[1]?->percent }}%:</p>
                                    </td>
                                    <td class="px-0 py-0" style="padding-left: 0 !important;">
                                        <p class="m-0 text-end" style="background-color: yellow; padding-right:20px;">
                                            RP
                                            {{ number_format($amount2, 0, '', '.') }}</p>
                                    </td>
                                </tr>
                                @if ($totalPph > 0)
                                    <tr class="fw-medium py-0" style="font-size: 13px">
                                        <td colspan="{{ $labelColspan }}" class="text-end py-0"
                                            style="padding-right: 10px !important;">
                                            <p class="m-0">PPH</p>
                                        </td>
                                        <td class="pr-4 py-0" style="padding-left: 0 !important;">
                                            <p class="m-0 text-end">
                                                {{ $totalPph == '0' ? '0' : 'RP ' . number_format($totalPph, 0, '', '.') }}
                                            </p>
                                        </td>
                                    </tr>
                                @endif
                                @php
                                    $totalwithpph = $payments[1]?->amount - $totalPph;
                                @endphp
                                @if ($quote->tax != 0)
                                    <tr class="fw-medium" style="font-size: 13px">
                                        <td colspan="{{ $labelColspan }}" id="price" class="text-end pl-4 py-0"
                                            style="padding-right: 10px !important;">
                                            <p class="m-0">
                                                DPP Atas PPN
                                            </p>
                                        </td>
                                        <td id="price" class="pr-4 py-0" style="padding-left: 0 !important;">
                                            @php
                                                $dpp = ($amount2 * 11) / 12;
                                            @endphp
                                            <p class="text-end m-0">RP
                                                {{ number_format($dpp, 0, '', '.') }}</p>
                                        </td>
                                    </tr>
                                    <tr class="fw-medium py-0" style="font-size: 13px">
                                        <td colspan="{{ $labelColspan }}" class="text-end py-0"
                                            style="padding-right: 10px !important;">
                                            <p class="m-0">VAT {{ $quote->tax == '11' ? '12%' : '' }}</p>
                                        </td>
                                        <td class="pr-4 py-0" style="padding-left: 0 !important;">
                                            <p class="m-0 text-end">
                                                {{ $vat == '0' ? '0' : 'RP ' . number_format($vat, 0, '', '.') }}</p>
                                        </td>
                                    </tr>
                                    @if ($quote->shipping != 0)
                                        <tr class="fw-medium" style="font-size: 13px">
                                            <td colspan="{{ $labelColspan }}" class="text-end py-0"
                                                style="padding-right: 10px !important;">
                                                <p class="m-0">Shipping Cost</p>
                                            </td>
                                            <td class="pr-4 py-0" style="padding-left: 0 !important;">
                                                <p class="m-0 text-end">RP
                                                    {{ number_format($quote->shipping, 0, '', '.') }}
                                                </p>
                                            </td>
                                        </tr>
                                    @endif
                                    <tr class="fw-medium py-0" style="font-size: 13px">
                                        <td colspan="{{ $labelColspan }}" class="text-end py-0"
                                            style="background-color: {{ $bgColor }}; padding-left:20px; padding-right:10px;">
                                            <p class="m-0 fw-bold">Total Include VAT</p>
                                        </td>
                                        <td class="pr-4 py-0"
                                            style="background-color: {{ $bgColor }}; padding-right:20px;">
                                            <p class="m-0 text-end fw-bold">
                                                Rp {{ number_format($totalwithpph, 0, '', '.') }}
                                            </p>
                                        </td>
                                    </tr>
                                @else
                                    @if ($quote->shipping != 0)
                                        <tr class="fw-medium" style="font-size: 13px">
                                            <td colspan="{{ $labelColspan }}" class="text-end py-0"
                                                style="padding-right: 10px !important;">
                                                <p class="m-0">Shipping Cost</p>
                                            </td>
                                            <td class="pr-4 py-0" style="padding-left: 0 !important;">
                                                <p class="m-0 text-end">RP
                                                    {{ number_format($quote->shipping, 0, '', '.') }}
                                                </p>
                                            </td>
                                        </tr>
                                    @endif
                                    <tr class="fw-medium py-0" style="font-size: 13px">
                                        <td colspan="{{ $labelColspan }}" class="text-end py-0"
                                            style="background-color: {{ $bgColor }}; padding-left:20px; padding-right:10px;">
                                            <p class="m-0">Total</p>
                                        </td>
                                        <td class="pr-4 py-0"
                                            style="background-color: {{ $bgColor }}; padding-right:20px;">
                                            <p class="m-0 text-end fw-bold">
                                                {{ number_format($payments[1]?->amount, 0, '', '.') }}
                                            </p>
                                        </td>
                                    </tr>
                                @endif
                            @else
                                @php
                                    $payAmountVal = $payments[0]->amount ?? 0;
                                    $taxRate = $quote->tax ?? 0;
                                    $amount = $taxRate != 0 ? $payAmountVal / (1 + $taxRate / 100) : $payAmountVal;
                                    $vat = $amount * ($taxRate / 100);
                                    $payamount = $payAmountVal;
                                    $totalwithpph = $payAmountVal - $totalPph;
                                @endphp
                                @if ($quote->tax != 0)
                                    <tr class="fw-medium" style="font-size: 13px">
                                        <td colspan="{{ $labelColspan }}" id="price" class="text-end pl-4 py-0"
                                            style="padding-right: 10px !important;">
                                            <p class="m-0">
                                                DPP Atas PPN
                                            </p>
                                        </td>
                                        <td id="price" class="pr-4 py-0" style="padding-left: 0 !important;">
                                            @php
                                                $dpp = ($amount * 11) / 12;
                                            @endphp
                                            <p class="text-end m-0">RP
                                                {{ number_format($dpp, 0, '', '.') }}</p>
                                        </td>
                                    </tr>
                                    <tr class="fw-medium py-0" style="font-size: 13px">
                                        <td colspan="{{ $labelColspan }}" class="text-end py-0"
                                            style="padding-right: 10px !important;">
                                            <p class="m-0">VAT {{ $quote->tax == '11' ? '12%' : '' }}</p>
                                        </td>
                                        <td class="pr-4 py-0" style="padding-left: 0 !important;">
                                            <p class="m-0 text-end">
                                                {{ $vat == '0' ? '0' : 'RP ' . number_format($vat, 0, '', '.') }}</p>
                                        </td>
                                    </tr>
                                    @if ($totalPph > 0)
                                        <tr class="fw-medium py-0" style="font-size: 13px">
                                            <td colspan="{{ $labelColspan }}" class="text-end py-0"
                                                style="padding-right: 10px !important;">
                                                <p class="m-0">PPH</p>
                                            </td>
                                            <td class="pr-4 py-0" style="padding-left: 0 !important;">
                                                <p class="m-0 text-end">
                                                    {{ $totalPph == '0' ? '0' : 'RP ' . number_format($totalPph, 0, '', '.') }}
                                                </p>
                                            </td>
                                        </tr>
                                    @endif
                                    @if ($quote->shipping != 0)
                                        <tr class="fw-medium" style="font-size: 13px">
                                            <td colspan="{{ $labelColspan }}" class="text-end py-0"
                                                style="padding-right: 10px !important;">
                                                <p class="m-0">Shipping Cost</p>
                                            </td>
                                            <td class="pr-4 py-0" style="padding-left: 0 !important;">
                                                <p class="m-0 text-end">RP
                                                    {{ number_format($quote->shipping, 0, '', '.') }}
                                                </p>
                                            </td>
                                        </tr>
                                    @endif
                                    <tr class="fw-medium py-0" style="font-size: 13px">
                                        <td colspan="{{ $labelColspan }}" class="text-end py-0"
                                            style="background-color: {{ $bgColor }}; padding-left:20px; padding-right:10px;">
                                            <p class="m-0 fw-bold">Total Include VAT</p>
                                        </td>
                                        <td class="pr-4 py-0"
                                            style="background-color: {{ $bgColor }}; padding-right:20px;">
                                            <p class="m-0 text-end fw-bold">
                                                Rp {{ number_format($totalwithpph, 0, '', '.') }}
                                            </p>
                                        </td>
                                    </tr>
                                @else
                                    @if ($quote->shipping != 0)
                                        <tr class="fw-medium" style="font-size: 13px">
                                            <td colspan="{{ $labelColspan }}" class="text-end py-0"
                                                style="padding-right: 10px !important;">
                                                <p class="m-0">Shipping Cost</p>
                                            </td>
                                            <td class="pr-4 py-0" style="padding-left: 0 !important;">
                                                <p class="m-0 text-end">RP
                                                    {{ number_format($quote->shipping, 0, '', '.') }}
                                                </p>
                                            </td>
                                        </tr>
                                    @endif
                                    <tr class="fw-medium py-0" style="font-size: 13px">
                                        <td colspan="{{ $labelColspan }}" class="text-end py-0"
                                            style="background-color: {{ $bgColor }}; padding-left:20px; padding-right:10px;">
                                            <p class="m-0">Total</p>
                                        </td>
                                        <td class="pr-4 py-0"
                                            style="background-color: {{ $bgColor }}; padding-right:20px;">
                                            <p class="m-0 text-end fw-bold">
                                                {{ number_format($payamount, 0, '', '.') }}
                                            </p>
                                        </td>
                                    </tr>
                                @endif
                            @endif
                        </tbody>
                    </table>
                @endif
            </div>

            @if ($invoice->type == 'CT')
                <p class="fw-medium mt-2 p-2" style="background-color: rgb(248, 248, 248); width:70%;"> Say
                    amount: #
                    {{ $fullPrice }} Rupiah</p>
            @elseif ($invoice->type == 'DP')
                <p class="fw-medium mt-2 p-2" style="background-color: rgb(248, 248, 248); width:70%;"> Say
                    amount: #
                    {{ $priceDp }} Rupiah</p>
            @elseif ($invoice->type == 'BP')
                <p class="fw-medium mt-2 p-2" style="background-color: rgb(248, 248, 248); width:70%;"> Say
                    amount: #
                    {{ $priceBp }} Rupiah</p>
            @endif
            <div class="row">
                <div class="col-7">
                    <p class="mt-4 fw-bold fs-6">Payment by Transfer or Giro shall be made in Full amount to :</p>
                    <div class="row">
                        <div class="col-3 fw-medium">
                            <p class="mb-1">Payable to</p>
                            <p class="mb-1">Acc Name </p>
                            <p class="mb-1">Acc No. </p>
                            <p class="mb-1">Swift Code </p>
                        </div>
                        @if ($quote->pic->client->info == 'Reftech' && $invoice->quote->tax == 0)
                            <div class="col">
                                <p class="mb-1">: Bank BCA (IDR)</p>
                                <p class="mb-1">: ARIEP RACHMAN</p>
                                <p class="mb-1">: 166 - 2242 - 271</p>
                                <p class="mb-1">: -</p>
                            </div>
                        @elseif ($quote->pic->client->info == 'Reftech' && $invoice->quote->tax > 0)
                            <div class="col">
                                <p class="mb-1">: Bank BCA (IDR)</p>
                                <p class="mb-1">: PT. REFTECH JAYA OPTIMA</p>
                                <p class="mb-1">: 008 - 6289 - 789</p>
                                <p class="mb-1">: CENAIDJA</p>
                            </div>
                        @elseif ($quote->pic->client->info == 'Kojisha' && $invoice->quote->tax == 0)
                            <div class="col">
                                <p class="mb-1">: Bank BCA (IDR)</p>
                                <p class="mb-1">: REGITA DWI MELINDA</p>
                                <p class="mb-1">: 1560239137</p>
                                <p class="mb-1">: - </p>
                            </div>
                        @elseif ($quote->pic->client->info == 'Kojisha' && $invoice->quote->tax > 0)
                            <div class="col">
                                <p class="mb-1">: Bank BCA (IDR)</p>
                                <p class="mb-1">: KOJISHA INNOTIV INDONESIA PT</p>
                                <p class="mb-1">: 5223876543</p>
                                <p class="mb-1">: - </p>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="col"></div>
                @if ($quote->pic->client->info == 'Reftech')
                    <div class="col-4 mt-4 text-center">
                        <p class="{{ $quote->tax != 0 ? 'mb-0' : 'mb-3' }}">Bandung,
                            {{ Carbon\Carbon::parse($invoice->date)->locale('ID')->translatedFormat('d F Y') }}</p>
                        @if ($quote->tax != 0)
                            <p class="fs-normal fw-bolder">PT Reftech Jaya Optima</p>
                        @endif
                        @if (isset($invoice->sign))
                            <img src="{{ url('') . '/' . $invoice->sign }}" alt="" srcset=""
                                height="77">
                        @else
                            <div style="padding: 40px 0;"></div>
                        @endif
                        <p class="pt-3 fw-bolder mb-0">Ariep Rachman</p>
                        <p>Director</p>
                    </div>
                @else
                    <div class="col-4 mt-4 text-center">
                        <p class="mb-0">Bekasi, {{ Carbon\Carbon::parse($invoice->date)->format('d F Y') }}</p>
                        @if ($quote->tax != 0)
                            <p class="fs-normal fw-bolder">PT Kojisha Innotiv Indonesia </p>
                        @endif
                        @if (isset($invoice->sign))
                            <img src="{{ url('') . '/' . $invoice->sign }}" alt="" srcset=""
                                height="77">
                        @else
                            <div style="padding: 40px 0;"></div>
                        @endif
                        <p class="pt-3 fw-bolder mb-0">Dedeh Sulastri</p>
                        <p>Director</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
    @push('after-style')
        <!-- Page CSS -->
        <link rel="stylesheet" href="{{ asset('assets') }}/vendor/css/pages/app-invoice-print-header.css" />
        <link rel="stylesheet" href="style.css">
    @endpush
    @push('after-script')
        <script src="{{ asset('assets') }}/js/app-invoice-print.js"></script>
    @endpush
    @push('script')
        <script>
            $(document).ready(function() {
                // Ambil tinggi dari elemen <pre>
                var preHeight = $('#notePre').outerHeight();
                // Atur tinggi elemen <p> menjadi sama dengan tinggi elemen <pre>
                $('#noteParagraph').css('height', preHeight + 'px');
            });
        </script>
    @endpush
