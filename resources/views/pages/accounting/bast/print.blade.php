@extends('layouts.sales.app')
@section('title', 'BAST - ' . $bast->no_bast . ($bast->customer_name ? ' - ' . $bast->customer_name : ''))

@php
    $isReftech = $bast->entity === 'Reftech';
    $entityFullName = $isReftech ? 'PT Reftech Jaya Optima' : 'PT Kojisha Innotiv Indonesia';
@endphp

<div class="invoice-print">
    <div class="container-fluid flex-grow-1">

        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-start pb-3 mb-3"
            style="border-bottom: 2px solid #dee2e6; display: flex !important; flex-direction: row !important; justify-content: space-between !important; align-items: flex-start !important;">
            <div class="mb-0 pb-1">
                @if ($isReftech)
                    <div class="d-flex svg-illustration align-items-center gap-2 mb-1">
                        <span class="app-brand-logo demo">
                            <span style="color: var(--bs-primary)">
                                <img class="text-md"
                                    src="{{ url('https://reftech.id/wp-content/uploads/2021/10/Reftech-Logo-Hitam.png') }}"
                                    alt="" srcset="" width="55%">
                            </span>
                        </span>
                    </div>
                    <p class="mb-0 text-uppercase fw-bold" style="font-size: 11.5px; color: #4f46e5; letter-spacing: 0.5px; line-height: 1.2;">
                        COMPRESSED AIR SOLUTION
                    </p>
                    <p class="mb-1" style="font-size: 9.5px; font-weight: 600; color: #475569;">
                        Sales &nbsp;|&nbsp; Service &nbsp;|&nbsp; Rental &nbsp;|&nbsp; Measurement Air Audit
                    </p>
                    <div style="font-size: 9px; color: #475569; font-weight: 500;">
                        <i class="mdi mdi-certificate-outline me-1 text-primary"></i>
                        <span class="fw-bold" style="color: #696cff;">ISO Certified:</span> 
                        ISO 9001:2015 &nbsp;|&nbsp; ISO 14001:2015 &nbsp;|&nbsp; ISO 45001:2018
                    </div>
                @else
                    <div class="d-flex svg-illustration align-items-center gap-2 mb-2">
                        <span class="app-brand-logo demo">
                            <span style="color: var(--bs-primary)">
                                <img class="text-md" src="{{ asset('/asset') }}/logo/Kojisha-Log.png" alt=""
                                    srcset="" width="55%">
                            </span>
                        </span>
                    </div>
                @endif
            </div>
            <div class="text-end" style="padding-top: 8px;">
                @if ($isReftech)
                    <p class="fw-bolder text-uppercase" style="font-size: 16px; color: #4f46e5; letter-spacing: 0.3px; line-height: 1.2; margin-bottom: 5px !important;">PT REFTECH JAYA OPTIMA</p>
                    <div style="font-size: 10px; line-height: 1.35; color: #334155; font-weight: 500;">
                        <p class="mb-0">Taman Kopo Indah V, Ruko Sommerville No. 31</p>
                        <p class="mb-0">Bandung – Jawa Barat 40218</p>
                        <p class="mb-0 text-nowrap" style="white-space: nowrap;">
                            <i class="mdi mdi-phone-outline scaleX-n1-rtl me-1 mdi-14px text-primary"></i>022 54417653{{ '  |  ' }}<i class="mdi mdi-email-outline scaleX-n1-rtl me-1 mdi-14px text-primary"></i>admin@reftech.id{{ '  |  ' }}<i class="mdi mdi-web scaleX-n1-rtl me-1 mdi-14px text-primary"></i>www.reftech.id
                        </p>
                    </div>
                @else
                    <p class="fw-bolder text-uppercase" style="font-size: 16px; color: #4f46e5; letter-spacing: 0.3px; line-height: 1.2; margin-bottom: 5px !important;">PT KOJISHA INNOTIV INDONESIA</p>
                    <div style="font-size: 10px; line-height: 1.35; color: #334155; font-weight: 500;">
                        <p class="mb-0">Jl. Nancep No. 45A, Setu</p>
                        <p class="mb-0">Cibitung - Kab. Bekasi 17320</p>
                        <p class="mb-0 text-nowrap" style="white-space: nowrap;">
                            <i class="mdi mdi-phone-outline scaleX-n1-rtl me-1 mdi-14px text-primary"></i>+62 812-1000-0997
                            {{ '   ' }}<i
                                class="mdi mdi-email-outline scaleX-n1-rtl me-1 mdi-14px text-primary"></i>admin@kojisha.com
                        </p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Title --}}
        <div class="text-center mb-4">
            <h4 class="fw-bold mb-1 text-uppercase" style="color: #4f46e5; font-size: 18px; letter-spacing: 0.5px;">Berita Acara Serah Terima Pekerjaan</h4>
            <div class="fw-bold" style="font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; color: #4f46e5; font-size: 18px; letter-spacing: 0.5px;">{{ $bast->no_bast }}</div>
        </div>

        <p class="mb-3" style="font-size: 14px; line-height: 1.6;">
            Bersama dengan ini kami <strong class="text-uppercase">{{ $entityFullName }}</strong>, telah menyelesaikan pekerjaan hingga
            <strong>SELESAI</strong> untuk pekerjaan sbb :
        </p>

        <div class="border rounded p-3 text-center fw-bold text-uppercase mb-3" style="font-size: 18px;">
            {{ $bast->work_title }}
        </div>

        <table class="mb-2" style="font-size: 14px; width: 100%;">
            <tr>
                <td style="width: 250px; padding: 5px 0;">Tanggal Pekerjaan</td>
                <td style="width: 20px; padding: 5px 0;">:</td>
                <td style="padding: 5px 0;">{{ $bast->work_date->format('d-m-Y') }}</td>
            </tr>
            <tr>
                <td style="padding: 5px 0;">Sesuai PO/ kontrak no.</td>
                <td style="padding: 5px 0;">:</td>
                <td style="padding: 5px 0;">{{ $bast->po_number ?: '-' }}</td>
            </tr>
            <tr>
                <td style="padding: 5px 0;">Terhadap unit-unit sebagai berikut</td>
                <td style="padding: 5px 0;">:</td>
                <td style="padding: 5px 0;"></td>
            </tr>
        </table>

        <table class="table table-bordered mb-3" style="font-size: 14px;">
            <thead>
                <tr>
                    <th style="width: 8%;">No.</th>
                    <th>Unit</th>
                    <th>Serial No.</th>
                    <th style="width: 15%;">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($bast->units as $unit)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $unit->unit_name }}</td>
                        <td>{{ $unit->serial_no }}</td>
                        <td>{{ $unit->qty }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <p class="mb-2" style="font-size: 14px; font-weight: 600;">
            Hasil Pengecekan Pada Saat Test Running :
        </p>

        <div class="mb-3">
            <textarea class="form-control" rows="4" style="font-size: 14px; line-height: 1.5; border: 1px solid #ced4da; width: 100%; resize: none; background: #fff;" readonly>{{ $bast->test_running_result }}</textarea>
        </div>

        <p class="mb-1" style="font-size: 14px;">
            Demikian <strong>BERITA ACARA SERAH TERIMA PEKERJAAN</strong> ini di tanda tangani oleh kedua belah pihak :
        </p>

        <table class="table-borderless mb-3 ms-2" style="font-size: 14px; line-height: 1.6; width: auto;">
            <tr>
                <td style="width: 180px; padding: 2px 0;">• Pelaksana pekerjaan</td>
                <td style="width: 20px; padding: 2px 0;">:</td>
                <td style="padding: 2px 0;"><strong>{{ $entityFullName }}</strong></td>
            </tr>
            <tr>
                <td style="padding: 2px 0;">• Pemberi pekerjaan</td>
                <td style="padding: 2px 0;">:</td>
                <td style="padding: 2px 0;"><strong>{{ $bast->customer_name }}</strong></td>
            </tr>
        </table>

        <p class="mb-3" style="font-size: 14px; line-height: 1.6;">
            Dengan ini segala hal yang berhubungan dengan pekerjaan tersebut diatas dinyatakan
            <strong>SELESAI</strong>.
        </p>

        {{-- Signature --}}
        <div class="d-flex justify-content-between pt-4 mt-2" style="font-size: 14px; page-break-inside: avoid !important;">
            <div class="text-center" style="width: 42%;">
                <p class="mb-1" style="font-size: 14px;">Pelaksana pekerjaan</p>
                <p class="fw-bold text-uppercase mb-0" style="font-size: 14px;">{{ $entityFullName }}</p>
                <div style="height: 95px;"></div>
                <p class="mb-0" style="font-size: 14px;">( ........................................ )</p>
                <small class="text-muted">Project / Service</small>
            </div>
            <div class="text-center" style="width: 42%;">
                <p class="mb-1" style="font-size: 14px;">Pemberi pekerjaan</p>
                <p class="fw-bold text-uppercase mb-0" style="font-size: 14px;">{{ $bast->customer_name }}</p>
                <div style="height: 95px;"></div>
                <p class="mb-0" style="font-size: 14px;">( ........................................ )</p>
            </div>
        </div>

    </div>
</div>

@push('after-style')
    <style>
        /* Theme's .table tbody td rule forces vertical-align:middle !important with higher
           specificity than the .align-top utility class — override it here for the item table. */
        table.items-top-align-table tbody td {
            vertical-align: top !important;
        }
        @page {
            size: A4 portrait !important;
            margin: 15mm 15mm 15mm 15mm !important;
        }
        @media print {
            @page {
                size: A4 portrait !important;
                margin: 15mm 15mm 15mm 15mm !important;
            }
            html, body, .layout-wrapper, .layout-container, .layout-page, .content-wrapper, .container-fluid {
                padding: 0 !important;
                margin: 0 !important;
                background: #fff !important;
                width: 100% !important;
                max-width: 100% !important;
                box-shadow: none !important;
                position: static !important;
                overflow: visible !important;
                height: auto !important;
                min-height: auto !important;
            }
            .layout-menu, .layout-navbar, .content-backdrop, footer, .layout-menu-toggle {
                display: none !important;
            }
            .invoice-print {
                padding: 0 !important;
                margin: 0 !important;
                width: 100% !important;
                box-sizing: border-box !important;
            }
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                color-adjust: exact !important;
            }
            .table {
                width: 100% !important;
                border-collapse: collapse !important;
            }
            .table thead {
                display: table-header-group !important;
            }
            .table tbody tr {
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }
            .table-section-header {
                page-break-after: avoid !important;
                break-after: avoid !important;
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }
            .keep-together {
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }
            .signature-section {
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }
        }
        @media screen {
            .invoice-print {
                max-width: 920px;
                margin: 24px auto;
                padding: 30px !important;
                background: #fff;
                box-shadow: 0 4px 24px rgba(0,0,0,0.07);
                border-radius: 8px;
            }
            .container-fluid {
                padding: 0 !important;
            }
        }
    </style>
@endpush
@push('after-script')
    <script>
        document.title = @json('BAST - ' . $bast->no_bast . ($bast->customer_name ? ' - ' . $bast->customer_name : ''));
    </script>
    <script src="{{ asset('assets') }}/js/app-invoice-print.js"></script>
@endpush
