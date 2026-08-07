@extends('layouts.sales.app')
@section('title', 'Service reports')

<div class="invoice-print p-4">
    <div class="container-fluid flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between flex-xl-row flex-md-column flex-sm-row flex-column">
            <div class="mb-xl-0 pb-1">
                <div class="d-flex svg-illustration align-items-center gap-2 mb-4">
                    <span class="app-brand-logo demo">
                        <span style="color: var(--bs-primary)">
                            <img class="text-md"
                                src="{{ url('https://reftech.id/wp-content/uploads/2021/10/Reftech-Logo-Hitam.png') }}"
                                alt="" srcset="" width="60%">
                        </span>
                    </span>
                </div>
                <p class="mb-1 fw-bolder">PT Reftech Jaya Optima</p>
                <div style="font-size: 10px">
                    <p class="mb-1">Taman Kopo Indah V, Ruko Sommerville No. 31</p>
                    <p class="mb-1">Bandung – Jawa Barat 40218</p>
                    <p class="mb-1">
                        <i class="mdi mdi-phone-outline scaleX-n1-rtl me-1 mdi-14px"></i>022
                        54417653{{ '  |  ' }}<i
                            class="mdi mdi-email-outline scaleX-n1-rtl me-1 mdi-14px"></i>admin@reftech.id
                    </p>
                </div>
            </div>
            <div class="text-end">
                <h3 class="fw-bold">WEEKLY MONITORING</h3>
                <div class="mt-1">
                    <span class="text-muted">WEEK - {{ request()->route('week') }}</span>
                </div>
            </div>
        </div>
    </div>
    <hr class="my-2">
    <h5>Machine Compressor </h5>
    <div class="table-responsive text-nowrap mt-4">
        <table class="table table-bordered">
            <thead class="table-light">
                <th>Unit</th>
                <th>Condition</th>
                <th>Vibration</th>
                <th>Voltage</th>
                <th>Running Ampere</th>
                <th>Cooler</th>
                <th>Coupling</th>
                <th>Compressor / Area</th>
                <th>PIC</th>
            </thead>
            <tbody>
                @foreach ($monitoringAC as $item)
                    <tr class="{{ $item->idM == $machine->id ? 'bg-label-warning' : '' }}">
                        <td>
                            {{ $item->unit->brand }} {{ $item->unit->unit->sku }} || {{ $item->tag }} -
                            {{ $item->location }}
                        </td>
                        <td>{{ $item->condition ?? '-' }}</td>
                        <td>{{ $item->vibration ?? '-' }}</td>
                        <td>{{ $item->voltage ?? '-' }}</td>
                        <td>{{ $item->ampere ?? '-' }}</td>
                        <td>
                            @if ($item->cooler == 1)
                                <i
                                    class="mdi mdi-check-circle-outline scaleX-n1-rtl text-success me-1 mdi-14px"></i>
                            @else
                                <i
                                    class="mdi mdi-alpha-x-circle-outline scaleX-n1-rtl text-danger me-1 mdi-14px"></i>
                            @endif
                        </td>
                        <td>
                            @if ($item->coupling == 1)
                                <i
                                    class="mdi mdi-check-circle-outline scaleX-n1-rtl text-success me-1 mdi-14px"></i>
                            @else
                                <i
                                    class="mdi mdi-alpha-x-circle-outline scaleX-n1-rtl text-danger me-1 mdi-14px"></i>
                            @endif
                        </td>
                        <td>
                            @if ($item->area == 1)
                                <i
                                    class="mdi mdi-check-circle-outline scaleX-n1-rtl text-success me-1 mdi-14px"></i>
                            @else
                                <i
                                    class="mdi mdi-alpha-x-circle-outline scaleX-n1-rtl text-danger me-1 mdi-14px"></i>
                            @endif
                        </td>
                        <td>{{ $item->name ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <h5 class="mt-5">Machine Dryer </h5>
    <div class="table-responsive text-nowrap mt-4">
        <table class="table table-bordered">
            <thead class="table-light">
                <th>Unit</th>
                <th>Condition</th>
                <th>Voltage</th>
                <th>Ampere</th>
                <th>Auto Drain</th>
                <th>Pre</th>
                <th>After</th>
                <th>Condensor</th>
                <th>PIC</th>
            </thead>
            <tbody>
                @foreach ($monitoringDRYER as $item)
                    <tr class="{{ $item->idM == $machine->id ? 'bg-label-warning' : '' }}">
                        <td>
                            {{ $item->unit->brand }} {{ $item->unit->unit->sku }} || {{ $item->tag }} -
                            {{ $item->location }}
                        </td>
                        <td>{{ $item->condition ?? '-' }}</td>
                        <td>{{ $item->voltage ?? '-' }}</td>
                        <td>{{ $item->ampere ?? '-' }}</td>
                        <td>{{ $item->drain ?? '-' }}</td>
                        <td>{{ $item->pre ?? '-' }}</td>
                        <td>{{ $item->after ?? '-' }}</td>
                        <td>
                            @if ($item->condensor == 1)
                                <i
                                    class="mdi mdi-check-circle-outline scaleX-n1-rtl text-success me-1 mdi-14px"></i>
                            @else
                                <i
                                    class="mdi mdi-alpha-x-circle-outline scaleX-n1-rtl text-danger me-1 mdi-14px"></i>
                            @endif
                        </td>
                        <td>{{ $item->name ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="row mt-5">
        <div class="col-4 mt-5 text-center">
            <p>PT Reftech Jaya Optima</p>
            <div class="pb-5"></div>
            <p class="pt-3">Angel Irene</p>
        </div>
        <div class="col-4"></div>
        <div class="col-4 mt-5 text-center">
            <p>PT Fajar Surya Wisesa</p>
            <div class="pb-5"></div>
            <p class="pt-3">..........................................</p>
        </div>
    </div>
</div>
</div>
@push('after-style')
    <!-- Page CSS -->
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/css/pages/app-monitoring-print.css" />
    <link rel="stylesheet" href="style.css">
@endpush
@push('after-script')
    <script src="{{ asset('assets') }}/js/app-invoice-print.js"></script>
    <script>
        window.addEventListener('beforeprint', () => {
            const rows = document.querySelectorAll('table tr');
            rows.forEach((row, index) => {
                const rect = row.getBoundingClientRect();
                if (rect.top > window.innerHeight) {
                    row.style.marginTop = '20mm';
                }
            });
        });
    </script>
@endpush
