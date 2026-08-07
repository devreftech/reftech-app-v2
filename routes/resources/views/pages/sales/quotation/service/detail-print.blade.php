@extends('layouts.sales.app')
@section('title', $quote->no_quote)
@php
    if ($quote->pic->client->info == 'Reftech') {
        $bgColor = 'rgb(224, 248, 248)';
    } else {
        $bgColor = 'rgb(255, 232, 210)';
    }
@endphp
<div class="invoice-print p-2">
    <div class="container-fluid flex-grow-1 container-p-y">
        @if ($quote->pic->client->info == 'Reftech')
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
                    <p class="mb-1 fw-bolder" style="font-size: 16px">PT Reftech Jaya Optima</p>
                    <div style="font-size: 13px">
                        <p class="mb-1">Taman Kopo Indah V, Soho Sommerville No. 31</p>
                        <p class="mb-1">Bandung – Jawa Barat 40218</p>
                        <p class="mb-1">
                            <i class="mdi mdi-phone-outline scaleX-n1-rtl me-1 mdi-12px"></i>022 54417653
                            {{ '   ' }}<i class="mdi mdi-email-outline scaleX-n1-rtl me-1 mdi-12px"></i>info@reftech.id
                            <i class="mdi mdi-web scaleX-n1-rtl me-1 mdi-12px"></i>www.reftech.id
                        </p>
                        <p class="mb-1">
                        </p>
                    </div>
                </div>
                <div class="text-end">
                    <h3 class="fw-bold">QUOTATION</h3>
                    <div>
                        <span class="fw-bolder">#{{ $quote->no_quote }}</span>
                    </div>
                    @if ($quote->num_rev >= 1)
                        <div class="mt-1">
                            <span class="fw-bolder py-1 px-2"
                                style="background-color: {{ $bgColor }}; border-radius: 10px;">REV -
                                {{ $quote->num_rev }}</span>
                        </div>
                    @endif
                    <div class="mt-1">
                        <span
                            class="text-muted">{{ $quote->status == '25' ? 'DRAFT' : ($quote->status == '50' ? 'SEND' : ($quote->status == '75' ? 'NEGOTIATION' : ($quote->status == '100' ? 'DONE PO' : ($quote->status == '0' ? 'LOSS' : '')))) }}</span>
                    </div>
                    <div class="mt-1">
                        <span
                            class="text-muted">{{ Carbon\Carbon::parse($quote->estimated_date)->format('d-m-Y') }}</span>
                    </div>
                    <div class="mt-1">
                        <span class=" fw-medium fs-6 badge bg-info text-black">{{ $quote->title }}</span>
                    </div>
                </div>
            </div>
        @else
            <div class="d-flex justify-content-between flex-xl-row flex-md-column flex-sm-row flex-column">
                <div class="mb-xl-0 pb-1">
                    <div class="d-flex svg-illustration align-items-center gap-2 mb-2">
                        <span class="app-brand-logo demo">
                            <span style="color: var(--bs-primary)">
                                <img class="text-md" src="{{ asset('/asset') }}/logo/Kojisha-Log.png" alt=""
                                    srcset="" width="60%">
                            </span>
                        </span>
                    </div>
                    <p class="mb-1 fw-bolder">PT Kojisha Innotiv Indonesia</p>
                    <div style="font-size: 13px">
                        <p class="mb-1">Jl. Nancep No. 45A, Setu</p>
                        <p class="mb-1">Cibitung - Kab. Bekasi 17320</p>
                        <p class="mb-1">
                            <i class="mdi mdi-phone-outline scaleX-n1-rtl me-1 mdi-14px"></i>+62 812-1000-0997
                            {{ '  ' }}<i
                                class="mdi mdi-email-outline scaleX-n1-rtl me-1 mdi-14px"></i>admin@kojisha.com
                        </p>
                    </div>
                </div>
                <div class="text-end">
                    <h3 class="fw-bold">QUOTATION</h3>
                    <div>
                        <span class="fw-bolder">#{{ $quote->no_quote }}</span>
                    </div>
                    @if ($quote->num_rev >= 1)
                        <div class="mt-1">
                            <span class="fw-bolder py-1 px-2"
                                style="background-color: {{ $bgColor }}; border-radius: 10px;">REV -
                                {{ $quote->num_rev }}</span>
                        </div>
                    @endif
                    <div class="mt-1">
                        <span
                            class="text-muted">{{ $quote->status == '25' ? 'DRAFT' : ($quote->status == '50' ? 'SEND' : ($quote->status == '75' ? 'NEGOTIATION' : ($quote->status == '100' ? 'DONE PO' : ($quote->status == '0' ? 'LOSS' : '')))) }}</span>
                    </div>
                    <div class="mt-1">
                        <span
                            class="text-muted">{{ Carbon\Carbon::parse($quote->estimated_date)->format('d-m-Y') }}</span>
                    </div>
                </div>
            </div>
        @endif
        <hr>
        <div class="mb-4">
            <div class="row">
                <div class="col-6">
                    <h6 class="fw-semibold fs-4 mb-3">Quote to:</h6>
                </div>
                <div class="col-6 mb-2">
                </div>
            </div>
            <div class="row">
                <div class="col-2 fw-medium">
                    <p class="mb-1">Company </p>
                    <p class="mb-1">PIC Name</p>
                    <p class="mb-1">Phone </p>
                </div>
                <div class="col-4">
                    <p class="mb-1">: {{ $quote->pic->client->company }}</p>
                    <p class="mb-1">: {{ $quote->pic->name_pic }}</p>
                    <p class="mb-1">: {{ $quote->pic->phone_pic }}</p>
                </div>
                <div class="col-2 fw-medium text-end">
                    <p class="mb-1">Seller :</p>
                    <p class="mb-1">No PR :</p>
                    <p class="mb-1">Email :</p>
                </div>
                <div class="col-4 text-end">
                    <p class="mb-1">
                        {{ $quote->pic->client->info == 'Reftech' ? 'PT Reftech Jaya Optima' : 'PT Kojisha Innotiv Indonesia' }}
                    </p>
                    <p class="mb-1"> {{ $quote->no_pr ?? '-' }}</p>
                    <p class="mb-1"> {{ $quote->pic->client->email }}</p>
                </div>
            </div>
        </div>

        <p>Sir/Madam, We are pleased to offer the following items:
        </p>
        <div class="mb-2">
            <table class="table table-bordered m-0" style="width: 100%">
                <thead class="table-light border-top">
                    <tr>
                        <th class="text-center align-middle" style="width: 1%">No.</th>
                        <th class="text-center align-middle" style="width: 40%">Item Description</th>
                        <th class="text-center align-middle">Qty</th>
                        <th class="text-center align-middle">Price (IDR)</th>
                        <th class="text-center align-middle">Disc</th>
                        <th class="text-center align-middle">Total Price (IDR)</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $abjad = 64;
                        $totalBeforeDisc = $quote->subtotal + $totalDisc;
                    @endphp
                    @foreach ($subQuote as $subJudul)
                        @php
                            $no = 0;
                            $abjad++;
                        @endphp
                        <tr style="font-size: 13px border-bottom:none !important;" class="border-top">
                            <td class="align-top" style="border-bottom:none !important; background-color: #f0f0f0;">
                                <p class="fw-bold mb-0">{{ chr($abjad) }}</p>
                            </td>
                            <td class="text-nowrap align-top" colspan="5"
                                style="border-bottom:none !important; background-color: #f0f0f0;">
                                <p class="fw-bold mb-0">{{ $subJudul->subtitle }}</p>
                            </td>
                        </tr>
                        @foreach ($subJudul->detail as $product)
                            <tr style="font-size: 10px; border-bottom:none !important; border-top:none !important;">
                                <td class="align-top py-1" style="border-bottom:none !important;">
                                    @php
                                        $no++;
                                    @endphp
                                    <p class="fw-bold mb-0">{{ $no }}</p>
                                </td>
                                <td class="text-wrap align-top py-1" style="border-bottom:none !important;">
                                    <p class="fw-bold mb-0" >{{ $product->product }}</p>
                                    @if ($product->detail != '-')
                                        <pre class="fw-bold mb-0"
                                            style="font-size: 11px; font-family: Inter, sans-serif; max-width: 100%; overflow-x: auto; white-space: pre-wrap;">{{ $product->detail }}</pre>
                                    @endif
                                </td>
                                <td class="align-top py-1" style="border-bottom:none !important;">
                                    <p class="mb-0">{{ $product->qty }} {{ $product->info_qty }}</p>
                                </td>
                                <td class="align-top py-1 text-end" style="border-bottom:none !important;">
                                    <p class="mb-0">
                                        {{ $product->amount == 0 ? 'FOC' : number_format($product->price, 0, '', '.') }}
                                    </p>
                                </td>
                                <td class="align-top py-1 text-center" style="border-bottom:none !important;">
                                    <p class="mb-0">{{ $product->amount == 0 ? '-' : $product->disc . ' %' }}</p>
                                </td>
                                <td class="align-top py-1 text-end" style="border-bottom:none !important;">
                                    <p class="mb-0">
                                        {{ $product->amount == 0 ? 'FOC' : number_format($product->amount, 0, '', '.') }}
                                    </p>
                                </td>
                            </tr>
                        @endforeach
                    @endforeach
                    <tr>
                        <td colspan="5" class="text-end"> Subtotal :</td>
                        <td class="text-end"> {{ number_format($quote->subtotal, 0, '', '.') }}</td>
                    </tr>
                    @if ($quote->diskon != 0)
                        <tr>
                            <td colspan="5" class="text-end"> Discount :</td>
                            <td class="text-end"> {{ number_format($quote->diskon, 0, '', '.') }}</td>
                        </tr>
                        <tr>
                            <td colspan="5" class="text-end"> Total After Discount :</td>
                            <td class="text-end"> RP
                                {{ number_format($quote->subtotal - $quote->diskon, 0, '', '.') }}</td>
                        </tr>
                    @endif
                    {{-- <tr>
                        <td colspan="4"></td>
                        <td class="text-end"> Total After Discount :</td>
                        <td class="text-end"> {{ number_format($quote->subtotal, 0, '', '.') }}</td>
                    </tr> --}}
                    @if ($quote->tax != 0)
                        <tr>
                            <td colspan="5" class="text-end"> Total Tax :</td>
                            <td class="text-end">{{ number_format($tax, 0, '', '.') }}</td>
                        </tr>
                    @endif
                    @if ($quote->shipping != 0)
                        <tr>
                            <td colspan="3"></td>
                            <td class="text-end"> Shipping Cost :</td>
                            <td colspan="2" class="text-end">{{ number_format($quote->shipping, 0, '', '.') }}</td>
                        </tr>
                    @endif
                    <tr class="border-top">
                        <td colspan="5" class="px-4 border-right" style="background-color: #E7FF00">
                            <p class="fw-semibold mb-0 text-black text-end">TOTAL PRICE</p>
                        </td>
                        <td class="text-end px-4 border-left" style="background-color: #E7FF00">
                            <p class="fw-semibold mb-0 text-end text-black">RP 
                                {{ number_format($quote->harga_total, 0, '', '.') }}</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="mt-2">
            <h6>Note :</h6>
            <pre class="mb-0"
                style="font-size: 14px; font-family:inter; max-width: 100%; overflow-x: auto; white-space: pre-wrap; text-align: justify; background-color: #f8f8f8; padding: 10px;">{{ $quote->termncon[0]->note }}</pre>
        </div>
        <div class="termncon">
            <h5 class="my-4">Term & Condition</h5>
            <div class="row">
                <div class="col-3 fw-medium">
                    <p class="mb-1">Validity Of Quotation</p>
                    <p class="mb-1">Price </p>
                    <p class="mb-1">Delivery Process </p>
                    <p class="mb-1">Payment </p>
                    <p class="mb-1">Warranty </p>
                </div>
                <div class="col">
                    <p class="mb-1">: {{ $quote->termncon[0]->validity }}</p>
                    <p class="mb-1">: {{ $quote->termncon[0]->pricing }}</p>
                    <p class="mb-1">: {{ $quote->termncon[0]->delivery_process }}</p>
                    <p class="mb-1">: {{ $quote->termncon[0]->payment }}</p>
                    <p class="mb-1">: {{ $quote->termncon[0]->warranty }}</p>
                </div>
            </div>
            <p class="text-center mb-0 mt-2">if you have any questions about this quotation, please contact :</p>
            <p class="text-center mb-0 fw-semibold mb-0 text-end text-black ">{{ $quote->sales->name }} {{ $quote->sales->phone }}</p>
        </div>
        <div class="facilities">
            {{-- <h3 class="text-center mb-3"> Service Facilities</h3>
            <ul>
                <li>
                    <h5>WORKSHOP LOCATION:</h5>
                </li>
                <li style="list-style-type: none;">
                    <div class="row">
                        <div class="col-6">
                            <img src="{{ asset('asset') }}/facilities/Team.jpg" alt="team image"
                                class="d-block w-100" id="uploadedAvatar">
                        </div>
                        <div class="col-6">
                            <p class="fs-3 mb-0"> Address:</p>
                            <p class="fs-5">Jl. Nancep No.45, Cibening, Kec. Setu,
                                Kabupaten Bekasi, Jawa Barat 17320</p>
                        </div>
                    </div>
                </li>
                <li class="mt-2">
                    <h5>TOOLS & HEAVY MACHIENARIES AT WORKSHOP FACILITES:</h5>
                </li>
                <li style="list-style-type: none;">
                    <table class="table-bordered">
                        <tr>
                            <td>
                                <img src="{{ asset('asset') }}/facilities/Workbench-2.jpg" alt="workbench"
                                    class="d-block w-100 h-75" id="uploadedAvatar">
                            </td>
                            <td>
                                <img src="{{ asset('asset') }}/facilities/Workbench.jpg" alt="workbench"
                                    class="d-block w-100 h-75" id="uploadedAvatar">
                            </td>
                        </tr>
                        <tr>
                            <td class="text-center">
                                <p class="fw-bolder">Airend Workbench -Multi puosed</p>
                                <p>(Large Cap. : tested up to 315 kW Airend) - 1 Unit</p>
                            </td>
                            <td class="text-center">
                                <p class="fw-bolder">Airend Workbench – Multi puosed</p>
                                <p>(Medium Cap.: tested 15 - 37 kW Airend) - 1 Unit</p>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <img src="{{ asset('asset') }}/facilities/hydraulic-Jack.jpg" alt="hydraulic"
                                    class="d-block w-100 h-75" id="uploadedAvatar">
                            </td>
                            <td>
                                <img src="{{ asset('asset') }}/facilities/Heater-Bearing.jpg" alt="heater"
                                    class="d-block w-100 h-75" id="uploadedAvatar">
                            </td>
                        </tr>
                        <tr>
                            <td class="text-center">
                                <p class="fw-bolder">Hydraulic jack kapasitas 50ton - <span class="fw-normal">1
                                        Unit</span></p>
                            </td>
                            <td class="text-center">
                                <p class="fw-bolder">Heater Bearing induction</p>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <img src="{{ asset('asset') }}/facilities/Crane.jpg" alt="crane"
                                    class="d-block w-100 h-75" id="uploadedAvatar">
                            </td>
                            <td>
                                <img src="{{ asset('asset') }}/facilities/Flow-Meter.jpg" alt="flow meter"
                                    class="d-block w-100 h-75" id="uploadedAvatar">
                            </td>
                        </tr>
                        <tr>
                            <td class="text-center">
                                <p class="fw-bolder">Crane Capacity 3 ton - <span class="fw-normal">1 Unit</span>
                                </p>
                            </td>
                            <td class="text-center">
                                <p class="fw-bolder">Flow Meter - <span class="fw-normal">1 Set</span></p>
                            </td>
                        </tr>
                    </table>
                </li>
                <li> INSTRUMENT MEASUREMENT:</li>
                <li style="list-style-type: none;">
                    <table class="table-bordered">
                        <tr>
                            <td>
                                <img src="{{ asset('asset') }}/facilities/Vibration-Meter.jpg" alt="vibration meter"
                                    class="d-block w-100 h-75" id="uploadedAvatar">
                            </td>
                            <td>
                                <img src="{{ asset('asset') }}/facilities/Dew-Point-Meter.jpg" alt="Dew meter"
                                    class="d-block w-100 h-75" id="uploadedAvatar">
                            </td>
                        </tr>
                        <tr>
                            <td class="text-center">
                                <p class="fw-bolder">Vibration Meter</p>
                            </td>
                            <td class="text-center">
                                <p class="fw-bolder">Pressure Dewpoint Meter</p>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <img src="{{ asset('asset') }}/facilities/Megger-Tester.jpg" alt="hydraulic"
                                    class="d-block w-100 h-75" id="uploadedAvatar">
                            </td>
                            <td>
                                <img src="{{ asset('asset') }}/facilities/Dial-Gauge.jpg" alt="heater"
                                    class="d-block w-100 h-75" id="uploadedAvatar">
                            </td>
                        </tr>
                        <tr>
                            <td class="text-center">
                                <p class="fw-bolder">Megger Tester</p>
                            </td>
                            <td class="text-center">
                                <p class="fw-bolder">Dial Gauge</p>
                            </td>
                        </tr>
                    </table>
                </li>
            </ul> --}}

            <img src="{{ asset('asset') }}/facilities/1.jpg" alt="heater" class="m-0 d-block w-100 h-100"
                id="uploadedAvatar">
            <img src="{{ asset('asset') }}/facilities/2.jpg" alt="heater" class="m-0 d-block w-100 h-100"
                id="uploadedAvatar">
            <img src="{{ asset('asset') }}/facilities/3.jpg" alt="heater" class="m-0 d-block w-100 h-100"
                id="uploadedAvatar">
            <img src="{{ asset('asset') }}/facilities/4.jpg" alt="heater" class="m-0 d-block w-100 h-100"
                id="uploadedAvatar">
            <img src="{{ asset('asset') }}/facilities/5.jpg" alt="heater" class="m-0 d-block w-100 h-100"
                id="uploadedAvatar">
            <img src="{{ asset('asset') }}/facilities/6.jpg" alt="heater" class="m-0 d-block w-100 h-100"
                id="uploadedAvatar">
            <img src="{{ asset('asset') }}/facilities/7.jpg" alt="heater" class="m-0 d-block w-100 h-100"
                id="uploadedAvatar">
            <img src="{{ asset('asset') }}/facilities/8.jpg" alt="heater" class="m-0 d-block w-100 h-100"
                id="uploadedAvatar">
        </div>
        {{-- <div class="ketenagakerjaan">
            <h3 class="text-center">SERTIFIKAT KETENAGAKERJAAN</h3>
        </div>
        <div class="k3-teknisi">
            <h3 class="text-center">SERTIFIKAT K3 TEKNISI</h3>
        </div> --}}
    </div>
</div>
@push('after-style')
    <!-- Page CSS -->
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/css/pages/app-service-print.css" />
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
