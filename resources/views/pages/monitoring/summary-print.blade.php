@extends('layouts.sales.app')
@section('title', 'Summary')

@php
    $months = [
        1 => 'Januari',
        2 => 'Februari',
        3 => 'Maret',
        4 => 'April',
        5 => 'Mei',
        6 => 'Juni',
        7 => 'Juli',
        8 => 'Agustus',
        9 => 'September',
        10 => 'Oktober',
        11 => 'November',
        12 => 'Desember',
    ];
@endphp
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
                <div>
                    <h3 class="fw-bold">SUMMARY PEKERJAAN</h3>
                </div>
            </div>
        </div>
        <hr class="my-2">
        <h4>Summary Maintenance {{ $months[$month] }}</h4>
        <div class="table-responsive text-nowrap">
            <table class="table table-bordered">
                <thead class="table-light">
                    <th style="vertical-align: middle;">Date</th>
                    <th style="vertical-align: middle;">Unit</th>
                    <th style="vertical-align: middle;">Tag</th>
                    <th style="vertical-align: middle;">Location</th>
                    <th style="vertical-align: middle;">Maintenance Description</th>
                    <th style="vertical-align: middle;">PIC</th>
                </thead>
                <tbody>
                    @php
                        $no = 0;
                    @endphp
                    @forelse ($mainlog as $item)
                        @php
                            $no++;
                        @endphp
                        <tr>
                            <td>{{ $item->date }}</td>
                            <td>{{ $item->brand_type }}</td>
                            <td>{{ $item->tag }}</td>
                            <td>{{ $item->location }}</td>
                            <td>
                                <pre class="mb-1"
                                    style="font-size: 15px; font-family: 'Inter', Tahoma, Geneva, Verdana, sans-serif; max-width: 100%; overflow-x: auto;">{{ $item->desc }}</pre>
                            </td>
                            <td>{{ $item->teknisi->name }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center">Belum Ada Maintenance Log
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
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
