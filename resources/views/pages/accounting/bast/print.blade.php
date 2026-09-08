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
            style="border-bottom: 2px solid #cbd5e1; display: flex !important; flex-direction: row !important; justify-content: space-between !important; align-items: flex-start !important;">
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
                    <p class="mb-0 text-uppercase fw-bold text-brand-primary" style="font-size: 11.5px; color: #4f46e5 !important; letter-spacing: 0.5px; line-height: 1.2;">
                        COMPRESSED AIR SOLUTION
                    </p>
                    <p class="mb-1" style="font-size: 9.5px; font-weight: 600; color: #000000;">
                        Sales &nbsp;|&nbsp; Service &nbsp;|&nbsp; Rental &nbsp;|&nbsp; Measurement Air Audit
                    </p>
                    <div style="font-size: 9px; color: #000000; font-weight: 500;">
                        <i class="mdi mdi-certificate-outline me-1 text-primary"></i>
                        <span class="fw-bold" style="color: #696cff !important;">ISO Certified:</span> 
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
                    <p class="fw-bolder text-uppercase text-brand-primary" style="font-size: 16px; color: #4f46e5 !important; letter-spacing: 0.3px; line-height: 1.2; margin-bottom: 5px !important;">PT REFTECH JAYA OPTIMA</p>
                    <div style="font-size: 10px; line-height: 1.35; color: #000000; font-weight: 500;">
                        <p class="mb-0" style="color: #000000;">Taman Kopo Indah V, Soho Sommerville No. 31</p>
                        <p class="mb-0" style="color: #000000;">Bandung – Jawa Barat 40218</p>
                        <p class="mb-0 text-nowrap" style="white-space: nowrap; color: #000000;">
                            <i class="mdi mdi-phone-outline scaleX-n1-rtl me-1 mdi-14px text-primary"></i>022 54417653{{ '  |  ' }}<i class="mdi mdi-email-outline scaleX-n1-rtl me-1 mdi-14px text-primary"></i>admin@reftech.id{{ '  |  ' }}<i class="mdi mdi-web scaleX-n1-rtl me-1 mdi-14px text-primary"></i>www.reftech.id
                        </p>
                    </div>
                @else
                    <p class="fw-bolder text-uppercase text-brand-primary" style="font-size: 16px; color: #4f46e5 !important; letter-spacing: 0.3px; line-height: 1.2; margin-bottom: 5px !important;">PT KOJISHA INNOTIV INDONESIA</p>
                    <div style="font-size: 10px; line-height: 1.35; color: #000000; font-weight: 500;">
                        <p class="mb-0" style="color: #000000;">Jl. Nancep No. 45A, Setu</p>
                        <p class="mb-0" style="color: #000000;">Cibitung - Kab. Bekasi 17320</p>
                        <p class="mb-0 text-nowrap" style="white-space: nowrap; color: #000000;">
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
            <h4 class="fw-bold mb-1 text-uppercase text-brand-primary" style="color: #4f46e5 !important; font-size: 18px; letter-spacing: 0.5px;">
                {{ $bast->type === 'Rental' ? 'Berita Acara Serah Terima Unit Rental' : 'Berita Acara Serah Terima Pekerjaan' }}
            </h4>
            <div class="fw-bold text-brand-primary" style="font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; color: #4f46e5 !important; font-size: 18px; letter-spacing: 0.5px;">{{ $bast->no_bast }}</div>
        </div>

        @if ($bast->type === 'Rental')
            <p class="mb-3 text-dark-content" style="font-size: 14px; line-height: 1.6; color: #000000;">
                Bersama dengan ini kami <strong class="text-uppercase" style="color: #000000;">{{ $entityFullName }}</strong>, telah melakukan pengiriman, instalasi, dan commissioning serta <strong style="color: #000000;">MENYERAHKAN UNIT RENTAL</strong> dalam kondisi baik dan siap beroperasi kepada <strong style="color: #000000;">{{ $bast->customer_name }}</strong> untuk unit sbb :
            </p>
        @else
            <p class="mb-3 text-dark-content" style="font-size: 14px; line-height: 1.6; color: #000000;">
                Bersama dengan ini kami <strong class="text-uppercase" style="color: #000000;">{{ $entityFullName }}</strong>, telah menyelesaikan pekerjaan hingga
                <strong style="color: #000000;">SELESAI</strong> untuk pekerjaan sbb :
            </p>
        @endif

        <div class="border rounded p-3 text-center fw-bold text-uppercase mb-3" style="font-size: 18px; color: #000000; border: 1.5px solid #000000 !important;">
            {{ $bast->work_title }}
        </div>

        <table class="mb-2" style="font-size: 14px; width: 100%; color: #000000;">
            @if ($bast->type === 'Rental')
                <tr>
                    <td style="width: 250px; padding: 5px 0; color: #000000;">Tanggal Commissioning</td>
                    <td style="width: 20px; padding: 5px 0; color: #000000;">:</td>
                    <td style="padding: 5px 0; color: #000000; font-weight: 600;">
                        @if ($bast->work_date)
                            {{ $bast->work_date->format('d-m-Y') }}
                        @else
                            <span style="display: inline-block; min-width: 220px; border-bottom: 1.5px dotted #000; height: 16px; vertical-align: middle;"></span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td style="padding: 5px 0; color: #000000;">Masa Rental</td>
                    <td style="padding: 5px 0; color: #000000;">:</td>
                    <td style="padding: 5px 0; color: #000000; font-weight: 600;">
                        @if ($bast->rental_start_date && $bast->rental_end_date)
                            {{ $bast->rental_start_date->format('d-m-Y') }} s/d {{ $bast->rental_end_date->format('d-m-Y') }}
                        @elseif ($bast->rental_start_date)
                            {{ $bast->rental_start_date->format('d-m-Y') }} s/d <span style="display: inline-block; min-width: 130px; border-bottom: 1.5px dotted #000; height: 16px; vertical-align: middle;"></span>
                        @elseif ($bast->rental_end_date)
                            <span style="display: inline-block; min-width: 130px; border-bottom: 1.5px dotted #000; height: 16px; vertical-align: middle;"></span> s/d {{ $bast->rental_end_date->format('d-m-Y') }}
                        @else
                            <span style="display: inline-block; min-width: 130px; border-bottom: 1.5px dotted #000; height: 16px; vertical-align: middle;"></span> s/d <span style="display: inline-block; min-width: 130px; border-bottom: 1.5px dotted #000; height: 16px; vertical-align: middle;"></span>
                        @endif
                    </td>
                </tr>
            @else
                <tr>
                    <td style="width: 250px; padding: 5px 0; color: #000000;">Tanggal Pekerjaan</td>
                    <td style="width: 20px; padding: 5px 0; color: #000000;">:</td>
                    <td style="padding: 5px 0; color: #000000; font-weight: 600;">
                        @if ($bast->work_date)
                            {{ $bast->work_date->format('d-m-Y') }}
                        @else
                            <span style="display: inline-block; min-width: 220px; border-bottom: 1.5px dotted #000; height: 16px; vertical-align: middle;"></span>
                        @endif
                    </td>
                </tr>
            @endif
            <tr>
                <td style="padding: 5px 0; color: #000000;">Sesuai PO/ kontrak no.</td>
                <td style="padding: 5px 0; color: #000000;">:</td>
                <td style="padding: 5px 0; color: #000000; font-weight: 600;">{{ $bast->po_number ?: '-' }}</td>
            </tr>
            <tr>
                <td style="padding: 5px 0; color: #000000;">Terhadap unit-unit sebagai berikut</td>
                <td style="padding: 5px 0; color: #000000;">:</td>
                <td style="padding: 5px 0; color: #000000;"></td>
            </tr>
        </table>

        <table class="table table-bordered mb-3" style="font-size: 14px; color: #000000; border: 1.5px solid #000000;">
            <thead>
                <tr style="background-color: #f8f9fa;">
                    <th style="width: 8%; color: #000000; font-weight: 700; border: 1px solid #000000; text-align: center;">No.</th>
                    <th style="color: #000000; font-weight: 700; border: 1px solid #000000;">Unit</th>
                    <th style="color: #000000; font-weight: 700; border: 1px solid #000000;">Serial No.</th>
                    <th style="width: 15%; color: #000000; font-weight: 700; border: 1px solid #000000; text-align: center;">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($bast->units as $unit)
                    <tr>
                        <td style="color: #000000; border: 1px solid #000000; text-align: center;">{{ $loop->iteration }}</td>
                        <td style="color: #000000; border: 1px solid #000000;">{{ $unit->unit_name }}</td>
                        <td style="color: #000000; border: 1px solid #000000;">{{ $unit->serial_no ?: '-' }}</td>
                        <td style="color: #000000; border: 1px solid #000000; text-align: center;">{{ $unit->qty }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <p class="mb-2" style="font-size: 14px; font-weight: 700; color: #000000;">
            Hasil Pengecekan Pada Saat Test Running :
        </p>

        <div class="mb-3">
            <textarea class="form-control" rows="4" style="font-size: 14px; line-height: 1.5; border: 1.5px solid #000000; width: 100%; resize: none; background: #fff; color: #000000; font-weight: 500;" readonly>{{ $bast->test_running_result }}</textarea>
        </div>

        @if ($bast->type === 'Rental')
            <p class="mb-1" style="font-size: 14px; color: #000000;">
                Demikian <strong style="color: #000000;">BERITA ACARA SERAH TERIMA UNIT RENTAL</strong> ini di tanda tangani oleh kedua belah pihak :
            </p>

            <table class="table-borderless mb-3 ms-2" style="font-size: 14px; line-height: 1.6; width: auto; color: #000000;">
                <tr>
                    <td style="width: 220px; padding: 2px 0; color: #000000;">• Yang Menyerahkan (Penyedia)</td>
                    <td style="width: 20px; padding: 2px 0; color: #000000;">:</td>
                    <td style="padding: 2px 0; color: #000000;"><strong style="color: #000000;">{{ $entityFullName }}</strong></td>
                </tr>
                <tr>
                    <td style="padding: 2px 0; color: #000000;">• Yang Menerima (Penyewa)</td>
                    <td style="width: 20px; padding: 2px 0; color: #000000;">:</td>
                    <td style="padding: 2px 0; color: #000000;"><strong style="color: #000000;">{{ $bast->customer_name }}</strong></td>
                </tr>
            </table>

            <p class="mb-3" style="font-size: 14px; line-height: 1.6; color: #000000;">
                Dengan ini unit rental tersebut di atas dinyatakan telah <strong style="color: #000000;">DITERIMA DALAM KONDISI BAIK &amp; SIAP BEROPERASI</strong>. Unit tetap merupakan milik/aset <strong style="color: #000000;">{{ $entityFullName }}</strong> dan akan diambil/ditarik kembali setelah masa sewa/rental berakhir.
            </p>
        @else
            <p class="mb-1" style="font-size: 14px; color: #000000;">
                Demikian <strong style="color: #000000;">BERITA ACARA SERAH TERIMA PEKERJAAN</strong> ini di tanda tangani oleh kedua belah pihak :
            </p>

            <table class="table-borderless mb-3 ms-2" style="font-size: 14px; line-height: 1.6; width: auto; color: #000000;">
                <tr>
                    <td style="width: 180px; padding: 2px 0; color: #000000;">• Pelaksana pekerjaan</td>
                    <td style="width: 20px; padding: 2px 0; color: #000000;">:</td>
                    <td style="padding: 2px 0; color: #000000;"><strong style="color: #000000;">{{ $entityFullName }}</strong></td>
                </tr>
                <tr>
                    <td style="padding: 2px 0; color: #000000;">• Pemberi pekerjaan</td>
                    <td style="width: 20px; padding: 2px 0; color: #000000;">:</td>
                    <td style="padding: 2px 0; color: #000000;"><strong style="color: #000000;">{{ $bast->customer_name }}</strong></td>
                </tr>
            </table>

            <p class="mb-3" style="font-size: 14px; line-height: 1.6; color: #000000;">
                Dengan ini segala hal yang berhubungan dengan pekerjaan tersebut diatas dinyatakan
                <strong style="color: #000000;">SELESAI</strong>.
            </p>
        @endif

        {{-- Signature --}}
        <div class="d-flex justify-content-between pt-4 mt-2 signature-section" style="font-size: 14px; color: #000000; page-break-inside: avoid !important;">
            <div class="text-center" style="width: 42%;">
                <p class="fw-bold text-uppercase mb-0" style="font-size: 14px; color: #000000;">{{ $entityFullName }}</p>
                @if ($bast->sign)
                    <div class="d-flex align-items-center justify-content-center" style="height: 85px; margin: 5px 0;">
                        <img src="{{ asset($bast->sign) }}" alt="Hand Sign" style="max-height: 80px; max-width: 175px; object-fit: contain;">
                    </div>
                    <p class="mb-0 fw-bold" style="font-size: 14px; color: #000000;">( <u>{{ $isReftech ? 'Ariep Rachman' : 'Dedeh Sulastri' }}</u> )</p>
                @else
                    <div style="height: 95px;"></div>
                    <p class="mb-0" style="font-size: 14px; color: #000000; font-weight: 600;">( ........................................ )</p>
                @endif
            </div>
            <div class="text-center" style="width: 42%;">
                <p class="fw-bold text-uppercase mb-0" style="font-size: 14px; color: #000000;">{{ $bast->customer_name }}</p>
                @if ($bast->customer_signature)
                    <div class="d-flex align-items-center justify-content-center position-relative" style="height: 85px; margin: 5px 0;">
                        <img src="{{ asset($bast->customer_signature) }}" alt="Customer Signature" style="max-height: 80px; max-width: 175px; object-fit: contain; z-index: 2;">
                        @if ($bast->customer_signed_stamp)
                            <img src="{{ asset($bast->customer_signed_stamp) }}" alt="Stamp" style="position: absolute; max-height: 70px; opacity: 0.75; z-index: 1; transform: rotate(-5deg);">
                        @endif
                    </div>
                    <p class="mb-0 fw-bold" style="font-size: 14px; color: #000000;">( <u>{{ $bast->customer_signer_name }}</u> )</p>
                    @if ($bast->customer_signer_position)
                        <small class="d-block" style="font-size: 11px; color: #000000;">{{ $bast->customer_signer_position }}</small>
                    @endif
                @else
                    <div style="height: 95px;"></div>
                    <p class="mb-0" style="font-size: 14px; color: #000000; font-weight: 600;">( ........................................ )</p>
                @endif
            </div>
        </div>

    </div>
</div>

@push('after-style')
    <style>
        .invoice-print {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            color: #000000 !important;
        }
        .invoice-print p,
        .invoice-print span:not([class*="text-primary"]):not([style*="color: #696cff"]):not([style*="color:#696cff"]),
        .invoice-print td,
        .invoice-print th,
        .invoice-print strong,
        .invoice-print small {
            color: #000000;
        }
        .invoice-print .text-brand-primary {
            color: #4f46e5 !important;
            -webkit-text-fill-color: #4f46e5 !important;
        }
        .invoice-print .table,
        .invoice-print .table th,
        .invoice-print .table td,
        .invoice-print .table-bordered {
            border-color: #000000 !important;
            color: #000000 !important;
        }
        .invoice-print textarea.form-control {
            color: #000000 !important;
            -webkit-text-fill-color: #000000 !important;
            opacity: 1 !important;
            background-color: #ffffff !important;
            border: 1.5px solid #000000 !important;
        }
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
            .text-brand-primary {
                color: #4f46e5 !important;
                -webkit-text-fill-color: #4f46e5 !important;
            }
            .table {
                width: 100% !important;
                border-collapse: collapse !important;
                border-color: #000000 !important;
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
