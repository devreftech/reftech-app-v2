@extends('layouts.sales.app')
@section('title', 'BAST - ' . $bast->no_bast)

@php
    $isReftech = $bast->entity === 'Reftech';
    $entityFullName = $isReftech ? 'PT. Reftech Jaya Optima' : 'PT. Kojisha Innotiv Indonesia';
@endphp

<div class="invoice-print p-4">
    <div class="container-fluid flex-grow-1 container-p-y">
        {{-- Header (format sama seperti Service Report) --}}
        <div class="d-flex justify-content-between align-items-start flex-xl-row flex-md-column flex-sm-row flex-column pb-3 mb-3"
            style="border-bottom: 2px solid #dee2e6;">
            @if ($isReftech)
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
                <div class="mt-1">
                    <span class="text-muted">{{ $bast->work_date->format('d-m-Y') }}</span>
                </div>
            </div>
        </div>

        {{-- Title --}}
        <div class="text-center mb-4">
            <h4 class="fw-bold mb-1 text-uppercase">Berita Acara Serah Terima Pekerjaan</h4>
            <div class="fw-bold">{{ $bast->no_bast }}</div>
        </div>

        <p class="mb-3">
            Bersama dengan ini kami {{ $entityFullName }}, telah menyelesaikan pekerjaan hingga
            <strong>SELESAI</strong> untuk pekerjaan sbb :
        </p>

        <div class="border rounded p-3 text-center fw-bold text-uppercase mb-3" style="font-size: 16px;">
            {{ $bast->work_title }}
        </div>

        <table class="mb-3" style="font-size: 14px;">
            <tr>
                <td style="width: 220px;">Tanggal Pekerjaan</td>
                <td style="width: 20px;">:</td>
                <td>{{ $bast->work_date->format('d-m-Y') }}</td>
            </tr>
            <tr>
                <td>Sesuai PO/ kontrak no.</td>
                <td>:</td>
                <td>{{ $bast->po_number ?: '-' }}</td>
            </tr>
            <tr>
                <td>Terhadap unit-unit sebagai berikut</td>
                <td>:</td>
                <td></td>
            </tr>
        </table>

        <table class="table table-bordered mb-3">
            <thead>
                <tr>
                    <th style="width: 8%;">No.</th>
                    <th>Unit</th>
                    <th>Serial No.</th>
                    <th style="width: 15%;">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($bast->units as $index => $unit)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $unit->unit_name }}</td>
                        <td>{{ $unit->serial_no ?: '-' }}</td>
                        <td>{{ $unit->qty }}</td>
                    </tr>
                @empty
                    <tr>
                        <td>&nbsp;</td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <p class="mb-1">Hasil pengecekan pada saat test running :</p>
        <div class="border rounded p-3 mb-4" style="min-height: 90px; white-space: pre-wrap; font-size: 14px;">{{ $bast->test_running_result }}</div>

        <p class="mb-2">
            Demikian <strong>BERITA ACARA SERAH TERIMA PEKERJAAN</strong> ini di tandatangani oleh kedua belah pihak :
        </p>
        <ul class="mb-3">
            <li>Pelaksana pekerjaan&nbsp; : <strong>{{ $entityFullName }}</strong></li>
            <li>Pemberi pekerjaan&nbsp; : <strong>{{ $bast->customer_name }}</strong></li>
        </ul>
        <p class="mb-5">
            Dengan ini segala hal yang berhubungan dengan pekerjaan tersebut di atas dinyatakan
            <strong>SELESAI</strong>
        </p>

        {{-- Signature --}}
        <div class="row mt-5 pt-3">
            <div class="col-6 text-center">
                <p class="fw-bold mb-5">{{ $entityFullName }}</p>
                <div style="border-top: 1px solid #333; width: 60%; margin: 0 auto;"></div>
            </div>
            <div class="col-6 text-center">
                <p class="fw-bold mb-5">{{ $bast->customer_name }}</p>
                <div style="border-top: 1px solid #333; width: 60%; margin: 0 auto;"></div>
            </div>
        </div>
    </div>
</div>

@push('after-style')
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/css/pages/app-invoice-print.css" />
@endpush
@push('after-script')
    <script src="{{ asset('assets') }}/js/app-invoice-print.js"></script>
@endpush
