@extends('layouts.sales.app')
@section('title', 'Service reports')

<div class="invoice-print p-4">
    <div class="container-fluid flex-grow-1 container-p-y">
        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-start flex-xl-row flex-md-column flex-sm-row flex-column pb-3 mb-3"
            style="border-bottom: 2px solid #dee2e6;">
            @if ($service->pic->client->info == 'Reftech')
                <div class="mb-xl-0 pb-1">
                    <div class="d-flex svg-illustration align-items-center gap-2 mb-3">
                        <span class="app-brand-logo demo">
                            <span style="color: var(--bs-primary)">
                                <img class="text-md"
                                    src="{{ url('https://reftech.id/wp-content/uploads/2021/10/Reftech-Logo-Hitam.png') }}"
                                    alt="" srcset="" width="55%">
                            </span>
                        </span>
                    </div>
                    <p class="mb-1 fw-bolder">PT Reftech Jaya Optima</p>
                    <div class="text-muted" style="font-size: 10px">
                        <p class="mb-1">Taman Kopo Indah V, Ruko Sommerville No. 31</p>
                        <p class="mb-1">Bandung – Jawa Barat 40218</p>
                        <p class="mb-1">
                            <i class="mdi mdi-phone-outline scaleX-n1-rtl me-1 mdi-14px"></i>022
                            54417653{{ '  |  ' }}<i
                                class="mdi mdi-email-outline scaleX-n1-rtl me-1 mdi-14px"></i>admin@reftech.id
                        </p>
                    </div>
                </div>
            @else
                <div class="mb-xl-0 pb-1">
                    <div class="d-flex svg-illustration align-items-center gap-2 mb-2">
                        <span class="app-brand-logo demo">
                            <span style="color: var(--bs-primary)">
                                <img class="text-md" src="{{ asset('/asset') }}/logo/Kojisha-Log.png" alt=""
                                    srcset="" width="55%">
                            </span>
                        </span>
                    </div>
                    <p class="mb-1 fw-bolder">PT Kojisha Innotiv Indonesia</p>
                    <div class="text-muted" style="font-size: 10px">
                        <p class="mb-1">Jl. Nancep No. 45A, Setu</p>
                        <p class="mb-1">Cibitung - Kab. Bekasi 17320</p>
                        <p class="mb-1">
                            <i class="mdi mdi-phone-outline scaleX-n1-rtl me-1 mdi-14px"></i>+62 812-1000-0997
                            {{ '   ' }}<i
                                class="mdi mdi-email-outline scaleX-n1-rtl me-1 mdi-14px"></i>admin@kojisha.com
                        </p>
                    </div>
                </div>
            @endif
            <div class="text-end">
                <h3 class="fw-bold mb-1">SERVICE REPORT</h3>
                <div>
                    <span class="fw-bolder">#{{ $service->no_service }}</span>
                </div>
                <div class="mt-1">
                    <span class="text-muted">{{ \Carbon\Carbon::parse($service->date)->format('d-m-Y') }}</span>
                </div>
                <div class="mt-2">
                @php
                    $badgeClass = '';
                    $label = $service->type;

                    switch ($service->type) {
                        case 'Visit':
                            $badgeClass = 'success';
                            break;
                        case 'Service':
                            $badgeClass = 'danger';
                            break;
                        case 'General':
                            $badgeClass = 'primary';
                            $label = 'General Check';
                            break;
                        default:
                            $badgeClass = '';
                            break;
                    }
                @endphp
                <span class="badge fs-6 rounded-pill bg-label-{{ $badgeClass }}">{{ $label }}</span>
                </div>
            </div>
        </div>

        {{-- Customer & Unit Info --}}
        <div class="row mb-3 g-3">
            <div class="col-md-6" style="flex: 0 0 50%; max-width: 50%;">
                <div class="border rounded p-3 h-100">
                    <h6 class="fw-bold text-uppercase text-muted mb-2" style="font-size: 12px; letter-spacing: .5px;">
                        Customer Information</h6>
                    <div class="row">
                        <div class="col-4 fw-medium">
                            <p class="mb-1">Company</p>
                            <p class="mb-1">User</p>
                            <p class="mb-0">Address</p>
                        </div>
                        <div class="col-8">
                            <p class="mb-1">: {{ $service->pic->client->company }}</p>
                            <p class="mb-1">: {{ $service->pic->name_pic }}</p>
                            <p class="mb-0">: {{ $service->pic->client->area }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6" style="flex: 0 0 50%; max-width: 50%;">
                <div class="border rounded p-3 h-100">
                    <h6 class="fw-bold text-uppercase text-muted mb-2" style="font-size: 12px; letter-spacing: .5px;">
                        Unit Information</h6>
                    <div class="row">
                        <div class="col-4 fw-medium">
                            <p class="mb-1">Unit Type</p>
                            <p class="mb-1">Serial Number</p>
                            <p class="mb-0">Running & Load</p>
                        </div>
                        <div class="col-8">
                            <p class="mb-1">: {{ $service->machine?->unit?->brand ?? '-' }}@if($service->machine?->unit?->unit?->model && $service->machine?->unit?->unit?->model !== '-') {{ $service->machine?->unit?->unit?->model }}@endif{{ $service->machine->desc ? ' - ' . $service->machine->desc : '' }}</p>
                            <p class="mb-1">: {{ $service->machine->serial }}
                                {{ $service->machine->tag ? '| ' . $service->machine->tag : '' }}
                                {{ $service->machine->location ? '| ' . $service->machine->location : '' }}</p>
                            <p class="mb-0">: {{ $service->running }} | {{ $service->load }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Job Description --}}
        <div class="border rounded p-3 mb-3">
            <div class="row" style="font-size: 18px">
                <div class="col-3 fw-medium">
                    <p class="mb-0">Job Description</p>
                </div>
                <div class="col">
                    <p class="mb-0">: {{ $service->jobdesc }}</p>
                </div>
            </div>
        </div>

        {{-- Description & Recommendation --}}
        <div class="row mb-3 g-3">
            <div class="col-md-8" style="flex: 0 0 60%; max-width: 60%;">
                <div class="border rounded p-3 h-100">
                    <h6 class="fw-bold text-uppercase text-muted mb-2" style="font-size: 12px; letter-spacing: .5px;">
                        Description</h6>
                    <pre class="mb-0"
                        style="font-family: 'Inter', Tahoma, Geneva, Verdana, sans-serif; max-width: 100%; overflow-x: auto; white-space: pre-wrap;">{{ $service->desc }}</pre>
                </div>
            </div>
            <div class="col-md-4" style="flex: 0 0 40%; max-width: 40%;">
                <div class="border rounded p-3 h-100">
                    <h6 class="fw-bold text-uppercase text-muted mb-2" style="font-size: 12px; letter-spacing: .5px;">
                        Recomendation</h6>
                    <pre class="mb-0"
                        style="font-family: 'Inter', Tahoma, Geneva, Verdana, sans-serif; max-width: 100%; overflow-x: auto; white-space: pre-wrap;">{{ $service->recomendation }}</pre>
                </div>
            </div>
        </div>

        {{-- Picture --}}
        <div class="mb-4">
            <h6 class="fw-bold text-uppercase text-muted mb-2" style="font-size: 12px; letter-spacing: .5px;">Picture
            </h6>
            <div class="row g-2 justify-content-center">
                @foreach ($pict as $picture)
                    <div class="col-4 text-center">
                        <div class="border rounded p-1 mx-auto" style="max-width: 220px;">
                            <img src="{{ url('') . '/' . $picture->picture }}" alt="" srcset=""
                                style="width: 100%; aspect-ratio: 1 / 1; object-fit: cover; border-radius: 4px;"
                                class="img-reports">
                        </div>
                        <p class="mt-1 mb-0" style="font-size: 12px;">{{ $picture->keterangan }}</p>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Signature --}}
        <div class="row mt-4 pt-3" style="border-top: 1px solid #dee2e6;">
            <div class="col-4 text-center">
                <p class="mb-4">
                    {{ $service->pic->client->info == 'Reftech' ? 'PT Reftech Jaya Optima' : 'PT Kojisha Innotiv Indonesia' }}
                </p>
                <div class="d-flex align-items-end justify-content-center mb-1" style="height: 70px;">
                    @if (isset($service->technician->sign))
                        <img src="{{ url('') . '/' . $service->technician->sign }}" alt="" srcset=""
                            height="70">
                    @endif
                </div>
                <div style="border-top: 1px solid #333; width: 70%; margin: 0 auto;"></div>
                <p class="mt-2 mb-0">( {{ $service->technician->name }} )</p>
            </div>
            <div class="col-4"></div>
            <div class="col-4 text-center">
                <p class="mb-4">{{ $service->pic->client->company }}</p>
                <div class="d-flex align-items-end justify-content-center mb-1" style="height: 70px;">
                    @if (isset($service->sign_client))
                        <img src="{{ url('') . '/' . $service->sign_client }}" alt="" srcset="" height="70">
                    @endif
                </div>
                <div style="border-top: 1px solid #333; width: 70%; margin: 0 auto;"></div>
                <p class="mt-2 mb-0">( {{ $service->pic->name_pic }} )</p>
            </div>
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
