@extends('layouts.sales.app')
@section('title', 'Delivery Order ' . $delivery->do_number)
@section('content')
    @php
        $isReftech    = ($delivery->entity_display ?? 'Reftech') === 'Reftech';
        $companyName  = $isReftech ? 'PT Reftech Jaya Optima' : 'PT Kojisha Innotiv Indonesia';
        $doNumber     = $delivery->do_number;
        $customerName = $delivery->customer_name_display;
        $address      = $delivery->address_display;
        $poNumber     = $delivery->po_number_display;
        $deliveryDate = $delivery->date ? \Carbon\Carbon::parse($delivery->date)->format('d-m-Y') : '-';
    @endphp

    <style>
        .delivery-format-switch-wrapper {
            background: #ffffff;
            border-radius: 12px;
            padding: 10px 16px;
            border: 1px solid #e7e7e8;
            box-shadow: 0 2px 6px 0 rgba(67, 89, 113, 0.06);
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 1.25rem;
        }
        .delivery-format-switch {
            display: inline-flex;
            align-items: center;
            background: #f1f3f6;
            padding: 4px;
            border-radius: 10px;
            border: 1px solid #e3e6eb;
        }
        .delivery-format-switch .format-tab-btn {
            position: relative;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 18px;
            font-size: 13px;
            font-weight: 600;
            color: #697a8d;
            background: transparent;
            border: 1px solid transparent;
            border-radius: 7px;
            cursor: pointer;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            user-select: none;
            text-decoration: none;
            outline: none;
        }
        .delivery-format-switch .format-tab-btn i {
            font-size: 17px;
            line-height: 1;
            transition: transform 0.2s ease;
        }
        .delivery-format-switch .format-tab-btn:hover:not(.active) {
            color: #384551;
            background: rgba(255, 255, 255, 0.65);
        }
        .delivery-format-switch .format-tab-btn.active {
            color: #696cff;
            background: #ffffff;
            border-color: rgba(105, 108, 255, 0.12);
            box-shadow: 0 2px 6px 0 rgba(67, 89, 113, 0.12);
        }
        .delivery-format-switch .format-tab-btn.active i {
            color: #696cff;
            transform: scale(1.08);
        }
        .delivery-format-switch .active-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background-color: #696cff;
            display: inline-block;
            box-shadow: 0 0 0 2px rgba(105, 108, 255, 0.25);
        }
    </style>

    <div class="row invoice-preview">
        {{-- Invoice / DO Container with Tab Switcher --}}
        <div class="col-xl-9 col-md-8 col-12 mb-md-0 mb-4">
            {{-- Modern Sleek Segmented Switcher --}}
            <div class="delivery-format-switch-wrapper">
                <div class="d-flex align-items-center gap-3">
                    <div class="avatar avatar-sm bg-label-primary rounded d-flex align-items-center justify-content-center">
                        <i class="mdi mdi-swap-horizontal mdi-20px"></i>
                    </div>
                    <div>
                        <div class="fw-semibold text-heading" style="font-size: 13px;">Format Surat Jalan</div>
                        <div class="text-muted" style="font-size: 11px;">Pilih format untuk melihat preview & hasil cetak</div>
                    </div>
                </div>

                <div class="delivery-format-switch" role="tablist">
                    <button type="button" 
                            class="format-tab-btn {{ strtolower($delivery->type) == 'ekspedisi' ? 'active' : '' }}" 
                            id="tab-ekspedisi-btn" 
                            data-bs-toggle="pill" 
                            data-bs-target="#tab-ekspedisi-pane" 
                            role="tab" 
                            aria-controls="tab-ekspedisi-pane" 
                            aria-selected="{{ strtolower($delivery->type) == 'ekspedisi' ? 'true' : 'false' }}">
                        <i class="mdi mdi-truck-fast-outline"></i>
                        <span>Format Ekspedisi</span>
                        @if (strtolower($delivery->type) == 'ekspedisi')
                            <span class="active-dot" title="Tipe Asli Database"></span>
                        @endif
                    </button>
                    <button type="button" 
                            class="format-tab-btn {{ strtolower($delivery->type) != 'ekspedisi' ? 'active' : '' }}" 
                            id="tab-teknisi-btn" 
                            data-bs-toggle="pill" 
                            data-bs-target="#tab-teknisi-pane" 
                            role="tab" 
                            aria-controls="tab-teknisi-pane" 
                            aria-selected="{{ strtolower($delivery->type) != 'ekspedisi' ? 'true' : 'false' }}">
                        <i class="mdi mdi-account-wrench-outline"></i>
                        <span>Format Teknisi</span>
                        @if (strtolower($delivery->type) != 'ekspedisi')
                            <span class="active-dot" title="Tipe Asli Database"></span>
                        @endif
                    </button>
                </div>
            </div>

            {{-- Tab Content Panes --}}
            <div class="tab-content p-0 border-0 shadow-none bg-transparent" id="deliveryTabContent">
                {{-- Format Ekspedisi Tab Pane --}}
                <div class="tab-pane fade {{ strtolower($delivery->type) == 'ekspedisi' ? 'show active' : '' }}" id="tab-ekspedisi-pane" role="tabpanel" aria-labelledby="tab-ekspedisi-btn">
                    <div class="card invoice-preview-card">
                        <div class="card-body">
                            @if ($isReftech)
                                <div class="d-flex justify-content-between flex-xl-row flex-md-column flex-sm-row flex-column">
                                    <div class="mb-xl-0 pb-1">
                                        <div class="d-flex svg-illustration align-items-center gap-2 mb-4">
                                            <span class="app-brand-logo demo">
                                                <span style="color: var(--bs-primary)">
                                                    <img class="text-md" src="{{ asset('/asset') }}/logo/Reftech-Log.png"
                                                        alt="Reftech Logo" width="160">
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
                                                    class="mdi mdi-email-outline scaleX-n1-rtl me-1 mdi-14px"></i>accounting@reftech.id
                                            </p>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <h1 class="fw-bold" style="color: blue;">Delivery Order</h1>
                                        <div>
                                            <span class="fw-bolder fs-5">#{{ $doNumber }}</span>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="d-flex justify-content-between flex-xl-row flex-md-column flex-sm-row flex-column">
                                    <div class="mb-xl-0 pb-1">
                                        <div class="d-flex svg-illustration align-items-center gap-2 mb-2">
                                            <span class="app-brand-logo demo">
                                                <span style="color: var(--bs-primary)">
                                                    <img class="text-md" src="{{ asset('/asset') }}/logo/Logo-update-size.png"
                                                        alt="Kojisha Logo" width="160">
                                                </span>
                                            </span>
                                        </div>
                                        <p class="mb-1 fw-bolder">PT Kojisha Innotiv Indonesia</p>
                                        <div style="font-size: 10px">
                                            <p class="mb-1">Jl. Nancep No. 45A, Setu</p>
                                            <p class="mb-1">Cibitung - Kab. Bekasi 17320</p>
                                            <p class="mb-1">
                                                <i class="mdi mdi-phone-outline scaleX-n1-rtl me-1 mdi-14px"></i>+62 812-1000-0997
                                                {{ ' | ' }}<i
                                                    class="mdi mdi-email-outline scaleX-n1-rtl me-1 mdi-14px"></i>admin@kojisha.com
                                            </p>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <h1 class="fw-bold" style="color: blue;">Delivery Order</h1>
                                        <div>
                                            <span class="fw-bolder fs-5">#{{ $doNumber }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="card-body mb-3">
                            <div class="table-responsive text-nowrap">
                                <table class="table table-bordered" style="border: 1px solid black;">
                                    <tr>
                                        <td colspan="2" style="vertical-align: top; width: 50%;">
                                            <div class="row">
                                                <div class="col-4 fw-medium">
                                                    <p class="mb-1">Customers </p>
                                                    <p class="mb-1">Address</p>
                                                </div>
                                                <div class="col-8">
                                                    <p class="mb-1 fw-bold">: {{ $customerName }}</p>
                                                    <pre style="font-size: 13px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; max-width: 100%; overflow-x: auto; white-space: pre-wrap;">: {{ $address }}</pre>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="background-color: #F9F9F9;" class="text-center">
                                            <p class="fs-5 text-black fw-medium m-0">Purchase Order :</p>
                                        </td>
                                        <td style="background-color: #F9F9F9;" class="text-center">
                                            <p class="fs-5 text-black fw-medium m-0">Shipment Date :</p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-center">
                                            <pre style="font-size: 13px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; max-width: 100%; overflow-x: auto; white-space: pre-wrap;">{{ $poNumber }}</pre>
                                        </td>
                                        <td class="text-center">
                                            <pre style="font-size: 13px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; max-width: 100%; overflow-x: auto; white-space: pre-wrap;">{{ $deliveryDate }}</pre>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div class="card-body mb-3">
                            <div class="table-responsive mb-4">
                                <table class="table table-bordered m-0" style="border: 1px solid rgb(60, 60, 60)">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 5%">No.</th>
                                            <th style="width: 45%">Description</th>
                                            <th style="width: 25%">Keterangan</th>
                                            <th style="width: 25%">Qty</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $no = 0;
                                            $qty = 0;
                                        @endphp
                                        @foreach ($dDelivery as $product)
                                            @php
                                                $no++;
                                                $qty += (float) $product->qty;
                                            @endphp
                                            <tr style="font-size: 13px">
                                                <td class="align-top text-center">{{ $no }}</td>
                                                <td class="align-top">
                                                    <p class="mb-0 fw-semibold" style="font-size: 13px">
                                                        {{ $product->product ?: $product->desc }}
                                                    </p>
                                                </td>
                                                <td class="align-top">
                                                    <p class="mb-0 text-muted" style="font-size: 12px">
                                                        {{ $product->product ? $product->desc : '' }}
                                                    </p>
                                                </td>
                                                <td class="align-top fw-bold">{{ $product->qty }} {{ $product->info_qty }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="row">
                                <div class="col-4 my-4 text-center">
                                    <div class="pb-5"></div>
                                    <p class="fw-bold mx-3 mb-0" style="border-top: 1px solid black ">{{ $companyName }}</p>
                                    <p class="text-muted small">Shipper</p>
                                </div>
                                <div class="col-4"></div>
                                <div class="col-4 my-4 text-center">
                                    <div class="pb-5"></div>
                                    <p class="fw-bold mx-3 mb-0" style="border-top: 1px solid black ">{{ $customerName }}</p>
                                    <p class="text-muted small">Received</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Format Teknisi Tab Pane (Exact match with sj-print 2239) --}}
                <div class="tab-pane fade {{ strtolower($delivery->type) != 'ekspedisi' ? 'show active' : '' }}" id="tab-teknisi-pane" role="tabpanel" aria-labelledby="tab-teknisi-btn">
                    <div class="card invoice-preview-card">
                        <div class="card-body">
                            <div class="table-responsive mb-4">
                                <table class="table table-bordered m-0" style="border: 1px solid #000">
                                    <tbody>
                                        <tr>
                                            <td colspan="3" class="py-1">
                                                <div class="row">
                                                    <div class="col-8">
                                                        <h5 class="fw-bold mb-0">Delivery Order</h5>
                                                    </div>
                                                    <div class="col-4">
                                                        <p class="mb-0"><span class="fw-bold">D.O. No :</span> {{ $doNumber }}</p>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" class="py-0">
                                                <div class="row">
                                                    <div class="col-6">
                                                        @if ($isReftech)
                                                            <div class="d-flex justify-content-between flex-xl-row flex-md-column flex-sm-row flex-column">
                                                                <div class="mb-xl-0 pb-1">
                                                                    <div class="d-flex svg-illustration align-items-center gap-2">
                                                                        <span class="app-brand-logo demo">
                                                                            <span style="color: var(--bs-primary)">
                                                                                <img class="text-md" src="{{ asset('/asset') }}/logo/Reftech-Log.png" alt="" srcset="" width="60%">
                                                                            </span>
                                                                        </span>
                                                                    </div>
                                                                    <p class="mb-1 mx-2 fw-bolder">PT Reftech Jaya Optima</p>
                                                                    <div class="mx-2" style="font-size: 10px">
                                                                        <p class="mb-1">Taman Kopo Indah V, Ruko Sommerville No. 31</p>
                                                                        <p class="mb-1">Bandung – Jawa Barat 40218</p>
                                                                        <p class="mb-1">
                                                                            <i class="mdi mdi-phone-outline scaleX-n1-rtl me-1 mdi-14px"></i>022 54417653
                                                                            {{ '   ' }}<i class="mdi mdi-email-outline scaleX-n1-rtl me-1 mdi-14px"></i>accounting@reftech.id
                                                                        </p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @else
                                                            <div class="d-flex justify-content-between flex-xl-row flex-md-column flex-sm-row flex-column">
                                                                <div class="mb-xl-0 pb-1">
                                                                    <div class="d-flex svg-illustration align-items-center gap-2">
                                                                        <span class="app-brand-logo demo">
                                                                            <img class="text-md" src="{{ asset('/asset') }}/logo/Logo-update-size.png" alt="Kojisha Logo" srcset="" width="140">
                                                                        </span>
                                                                    </div>
                                                                    <p class="mb-1 mx-2 fw-bolder">PT Kojisha Innotiv Indonesia</p>
                                                                    <div class="mx-2" style="font-size: 10px">
                                                                        <p class="mb-1">Jl. Nancep No. 45A, Setu</p>
                                                                        <p class="mb-1">Cibitung - Kab. Bekasi 17320</p>
                                                                        <p class="mb-1">
                                                                            <i class="mdi mdi-phone-outline scaleX-n1-rtl me-1 mdi-14px"></i>+62 812-1000-0997
                                                                            {{ '   ' }}<i class="mdi mdi-email-outline scaleX-n1-rtl me-1 mdi-14px"></i>admin@kojisha.com
                                                                        </p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="row mt-3" style="font-size: 13px">
                                                            <div class="col-4 text-end">
                                                                <p class="mb-1">Date</p>
                                                                <p class="mb-1">Order No</p>
                                                                <p class="mb-1">Customer</p>
                                                                <p class="mb-1">Delivery To</p>
                                                            </div>
                                                            <div class="col-8">
                                                                <p class="mb-1">:
                                                                    @if ($delivery->date)
                                                                        {{ \Carbon\Carbon::parse($delivery->date)->format('d-m-Y') }}
                                                                    @else
                                                                        <span style="display:inline-block; min-width:90px; border-bottom:1px solid #000;">&nbsp;</span>
                                                                    @endif
                                                                </p>
                                                                <p class="mb-1">: {{ $poNumber }}</p>
                                                                <p class="mb-1">: {{ $customerName }}</p>
                                                                <p class="mb-1">: {{ $address }}</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="text-center">No</th>
                                            <th class="text-center">Qty</th>
                                            <th class="text-center" style="width: 80%">Description</th>
                                        </tr>
                                        @php $itemNo = 1; @endphp
                                        @foreach ($dDelivery as $product)
                                            <tr style="font-size: 13px;">
                                                <td class="align-top py-1 text-center">{{ $itemNo++ }}</td>
                                                <td class="align-top py-1 text-center" style="white-space:nowrap;">{{ (float) $product->qty }} {{ $product->info_qty }}</td>
                                                <td class="align-top py-1">
                                                    <p class="mb-0 fw-semibold">{{ $product->product ?: $product->desc }}</p>
                                                    @if ($product->product && $product->desc)
                                                        <p class="mb-0 text-muted small">{{ $product->desc }}</p>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                        <tr>
                                            <td colspan="3">
                                                <div class="row mb-3">
                                                    <div class="col-4 mt-4 text-center">
                                                        <div style="height:56px;" class="d-flex align-items-center justify-content-center">
                                                            @if ($delivery->sign)
                                                                <img src="{{ asset($delivery->sign) }}" alt="Shipper" style="max-height:54px; max-width:140px; object-fit:contain;">
                                                            @endif
                                                        </div>
                                                        <p class="fw-bold mx-3 mb-0" style="border-top: 1px solid black; padding-top: 3px;">Shipper</p>
                                                        <small class="text-muted d-block" style="font-size:10px;">{{ $companyName }}</small>
                                                    </div>
                                                    <div class="col-4"></div>
                                                    <div class="col-4 mt-4 text-center">
                                                        @if ($delivery->customer_signature)
                                                            <div class="d-flex align-items-center justify-content-center position-relative" style="height:56px;">
                                                                <img src="{{ asset($delivery->customer_signature) }}" alt="Customer Signature" style="max-height:54px; max-width:140px; object-fit:contain; z-index:2;">
                                                                @if ($delivery->customer_signed_stamp)
                                                                    <img src="{{ asset($delivery->customer_signed_stamp) }}" alt="Stamp" style="position:absolute; max-height:46px; opacity:0.75; transform:rotate(-5deg); z-index:1;">
                                                                @endif
                                                            </div>
                                                            <p class="fw-bold mx-3 mb-0 text-dark" style="border-top: 1px solid black; padding-top: 3px;">
                                                                ( <u>{{ $delivery->customer_signer_name }}</u> )
                                                            </p>
                                                            @if ($delivery->customer_signer_position)
                                                                <small class="text-muted d-block" style="font-size:10px;">{{ $delivery->customer_signer_position }}</small>
                                                            @endif
                                                            <small class="text-muted d-block" style="font-size:9.5px;">Received &bull; {{ $delivery->customer_signed_at ? \Carbon\Carbon::parse($delivery->customer_signed_at)->format('d/m/Y') : '' }}</small>
                                                        @else
                                                            <div style="height:56px;"></div>
                                                            <p class="fw-bold mx-3 mb-0" style="border-top: 1px solid black; padding-top: 3px;">Received</p>
                                                            <small class="text-muted d-block" style="font-size:10px;">{{ $customerName }}</small>
                                                        @endif
                                                    </div>
                                                </div>
                                                <p class="mb-0">Distribusi : Putih dan Pink → Pelanggan, <span class="fw-bold">Kuning → Accounting {{ $isReftech ? 'PT. Reftech' : 'PT. Kojisha' }}</span></p>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sidebar Actions --}}
        <div class="col-xl-3 col-md-4 col-12 invoice-actions">
            <div class="card mb-3 shadow-sm">
                <div class="card-body">
                    <a class="btn btn-primary d-grid w-100 mb-2 waves-effect d-flex align-items-center justify-content-center gap-1" target="_blank"
                        id="btnPrintDelivery"
                        href="{{ route('delivery.print_manual', ['id' => $delivery->id, 'format' => strtolower($delivery->type ?? 'ekspedisi')]) }}">
                        <i class="mdi mdi-printer me-1"></i> Cetak Surat Jalan
                    </a>
                    <a href="{{ route('delivery.edit', $delivery->id) }}" class="btn btn-warning d-grid w-100 mb-2 waves-effect d-flex align-items-center justify-content-center gap-1">
                        <i class="mdi mdi-pencil-outline me-1"></i> Edit Surat Jalan
                    </a>
                    <a href="{{ route('delivery.index') }}" class="btn btn-outline-secondary d-grid w-100 waves-effect d-flex align-items-center justify-content-center gap-1">
                        <i class="mdi mdi-arrow-left me-1"></i> Kembali ke List DO
                    </a>
                </div>
            </div>
            <div class="card mb-3 shadow-sm">
                <div class="card-body">
                    <h6 class="fw-bold mb-2">Info Surat Jalan</h6>
                    <ul class="list-unstyled mb-0 small">
                        <li class="mb-1"><span class="text-muted">Nomor DO:</span> <span class="fw-semibold">{{ $doNumber }}</span></li>
                        <li class="mb-1"><span class="text-muted">Customer:</span> <span class="fw-semibold">{{ $customerName }}</span></li>
                        <li class="mb-1"><span class="text-muted">Tanggal:</span> <span class="fw-semibold">{{ $deliveryDate }}</span></li>
                        <li class="mb-1"><span class="text-muted">Jenis:</span> <span class="badge bg-label-info">{{ ucfirst($delivery->type) }}</span></li>
                        <li class="mb-1"><span class="text-muted">Entitas:</span> <span class="badge bg-label-primary">{{ $delivery->entity_display }}</span></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var printBtn = document.getElementById('btnPrintDelivery');
            var ekspedisiTab = document.getElementById('tab-ekspedisi-btn');
            var teknisiTab = document.getElementById('tab-teknisi-btn');
            var basePrintUrl = "{{ route('delivery.print_manual', $delivery->id) }}";

            if (ekspedisiTab && teknisiTab && printBtn) {
                ekspedisiTab.addEventListener('shown.bs.tab', function () {
                    printBtn.href = basePrintUrl + '?format=ekspedisi';
                });
                teknisiTab.addEventListener('shown.bs.tab', function () {
                    printBtn.href = basePrintUrl + '?format=teknisi';
                });
            }
        });
    </script>
    @endpush
@endsection
