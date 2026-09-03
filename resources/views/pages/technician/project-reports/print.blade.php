<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daily Report Form - {{ $report->job_name }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/fonts/materialdesignicons.css" />
    <style>
        @page {
            size: A4 portrait;
            margin: 8mm 10mm 8mm 10mm;
        }

        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        body, table, th, td, div, p, span, h1, h2, h3, h4, h5, h6, strong, b, input, button {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif !important;
            font-size: 9.5pt;
            color: #000;
            background: #fff;
            margin: 0;
            padding: 0;
            line-height: 1.25;
        }

        .report-page {
            width: 100%;
            max-width: 210mm;
            margin: 0 auto;
            background: #fff;
        }

        /* PRINT BUTTON BAR (Hidden when printing) */
        .no-print-bar {
            background: #2b3445;
            padding: 12px 20px;
            color: #fff;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.15);
        }

        .btn-print {
            background: #007bff;
            color: white;
            border: none;
            padding: 8px 18px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 600;
        }

        .btn-back {
            background: #6c757d;
            color: white;
            text-decoration: none;
            padding: 8px 16px;
            border-radius: 5px;
            font-size: 13px;
        }

        @media print {
            .no-print-bar {
                display: none !important;
            }
            body {
                padding: 0;
            }
            .page-break {
                page-break-before: always;
            }
        }

        /* HEADER TITLE */
        .main-title {
            text-align: center;
            font-size: 17pt;
            font-weight: 900;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
            text-transform: uppercase;
        }

        /* HEADER BOXES */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            border: 2px solid #000;
            margin-bottom: 0px;
        }

        .header-table td {
            border: 1.5px solid #000;
            padding: 4px 8px;
            vertical-align: top;
        }

        .header-title-cell {
            font-weight: bold;
            text-align: center;
            font-size: 9.5pt;
            height: 20px;
        }

        .logo-container {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            padding: 6px 0;
        }

        .reftech-brand {
            font-family: 'Arial Black', Impact, sans-serif;
            font-size: 20pt;
            font-weight: 900;
            letter-spacing: 1px;
            line-height: 1;
            color: #000;
        }

        .reftech-sub {
            font-size: 7pt;
            font-weight: bold;
            letter-spacing: 0.5px;
            margin-top: 2px;
        }

        /* LAPORAN HARIAN INFO GRID */
        .sub-header-title {
            text-align: center;
            font-weight: bold;
            font-size: 11pt;
            padding: 3px;
            border-bottom: 1.5px solid #000;
            background: #fff;
        }

        .info-grid-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9pt;
        }

        .info-grid-table td {
            padding: 2px 4px;
            vertical-align: top;
            border: none;
        }

        /* SECTION TABLES */
        .section-header {
            font-weight: bold;
            font-size: 9.5pt;
            margin-top: 4px;
            margin-bottom: 2px;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            border: 1.5px solid #000;
            margin-bottom: 4px;
            font-size: 8.5pt;
        }

        .data-table th, .data-table td {
            border: 1px solid #000;
            padding: 2.5px 5px;
            vertical-align: middle;
        }

        .data-table th {
            text-align: center;
            font-weight: bold;
            background-color: #fff;
            font-size: 8.5pt;
        }

        .text-center {
            text-align: center;
        }

        /* TWO COLUMN CONTAINER */
        .two-col-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 4px;
        }

        .two-col-table td {
            vertical-align: top;
            padding: 0;
            border: none;
        }

        .col-left {
            width: 50%;
            padding-right: 3px !important;
        }

        .col-right {
            width: 50%;
            padding-left: 3px !important;
        }

        /* WEATHER SECTION */
        .weather-box {
            border: 1.5px solid #000;
            padding: 4px 8px;
            height: 100%;
            min-height: 140px;
        }

        .weather-row {
            display: flex;
            align-items: center;
            margin-bottom: 5px;
            font-size: 8.5pt;
        }

        .checkbox-square {
            width: 16px;
            height: 16px;
            border: 1.5px solid #000;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-right: 8px;
            font-weight: bold;
            font-size: 11pt;
            line-height: 1;
        }

        .weather-label {
            width: 75px;
            font-weight: bold;
        }

        .weather-time {
            font-size: 8.5pt;
        }

        /* TEXT BOX SECTIONS */
        .note-box {
            border: 1.5px solid #000;
            padding: 4px 6px;
            margin-bottom: 4px;
            min-height: 48px;
            font-size: 8.5pt;
        }

        .note-box-title {
            font-weight: bold;
            font-size: 8.5pt;
            margin-bottom: 2px;
            display: block;
        }

        .note-box-content {
            white-space: pre-line;
            font-size: 8.5pt;
            min-height: 25px;
        }

        /* SIGNATURE SECTION */
        .signature-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
            margin-bottom: 15px;
        }

        .signature-table td {
            width: 50%;
            vertical-align: top;
            border: none;
            padding: 0;
        }

        .sign-box {
            border: 1.5px solid #000;
            height: 100px;
            padding: 4px 8px;
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .sign-title {
            font-weight: bold;
            font-size: 9pt;
        }

        .sign-name {
            font-weight: bold;
            font-size: 8.5pt;
            text-decoration: underline;
        }

        .sign-img {
            max-height: 55px;
            max-width: 150px;
            object-fit: contain;
            display: block;
            margin: auto 0;
        }

        /* DOCUMENTATION PHOTO SECTION (PLACED AFTER CONTRACTOR SIGNATURE) */
        .photo-section-title {
            font-weight: bold;
            font-size: 11pt;
            border-bottom: 2px solid #000;
            padding-bottom: 4px;
            margin-top: 15px;
            margin-bottom: 12px;
            text-transform: uppercase;
            text-align: center;
        }

        .photo-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            justify-content: flex-start;
        }

        .photo-item {
            width: 48.5%;
            border: 1.5px solid #000;
            padding: 6px;
            background: #fff;
            page-break-inside: avoid;
            margin-bottom: 8px;
        }

        .photo-img {
            width: 100%;
            height: 210px;
            object-fit: cover;
            border: 1px solid #ccc;
            display: block;
        }

        .photo-caption {
            font-size: 8.5pt;
            font-weight: 600;
            margin-top: 4px;
            text-align: center;
        }
    </style>
</head>

<body>
    {{-- Top Action Bar --}}
    <div class="no-print-bar">
        <div>
            <strong>Daily Report Form Reftech</strong> &mdash; {{ $report->job_name }}
        </div>
        <div>
            <a href="{{ route('project-reports.show', $report->id) }}" class="btn-back">&larr; Kembali ke Detail</a>
            <button onclick="window.print()" class="btn-print" style="margin-left: 8px;">
                <i class="mdi mdi-printer"></i> Cetak Dokumen / PDF
            </button>
        </div>
    </div>

    <div class="report-page">
        <!-- TITLE -->
        <div class="main-title">DAILY REPORT FORM</div>

        <!-- HEADER BOXES -->
        <table class="header-table">
            <tr>
                <td style="width: 50%;" class="header-title-cell">PEMBERI TUGAS</td>
                <td style="width: 50%;" class="header-title-cell">KONTRAKTOR PELAKSANA</td>
            </tr>
            <tr>
                <td style="height: 60px; vertical-align: middle; text-align: center;">
                    @if ($report->client)
                        <div style="font-weight: bold; font-size: 13pt;">{{ $report->client->company }}</div>
                        <div style="font-size: 8pt; color: #333;">{{ $report->client->address ?? '' }}</div>
                    @else
                        <div style="font-weight: bold; font-size: 12pt;">CLIENT / OWNER</div>
                    @endif
                </td>
                <td style="height: 60px; vertical-align: middle; text-align: center;">
                    <div class="logo-container">
                        @if ($report->client && $report->client->info == 'Kojisha')
                            <img src="{{ asset('/asset') }}/logo/Kojisha-Log.png" alt="Logo Kojisha"
                                style="max-height: 48px; max-width: 190px; object-fit: contain;">
                        @else
                            <img src="{{ url('https://reftech.id/wp-content/uploads/2021/10/Reftech-Logo-Hitam.png') }}"
                                alt="Logo Reftech" style="max-height: 48px; max-width: 200px; object-fit: contain;"
                                onerror="this.onerror=null; this.src='{{ asset('/asset/logo/Reftech-Log.png') }}';">
                        @endif
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="padding: 0;">
                    <div class="sub-header-title">LAPORAN HARIAN</div>
                    <table class="info-grid-table">
                        <tr>
                            <td style="width: 18%;">Nama Pekerjaan</td>
                            <td style="width: 2%;">:</td>
                            <td colspan="4" style="font-weight: bold; font-size: 9.5pt;">{{ $report->job_name }}</td>
                        </tr>
                        <tr>
                            <td style="width: 18%;">No. Surat Perjanjian</td>
                            <td style="width: 2%;">:</td>
                            <td style="width: 42%;">{{ $report->contract_no ?: '-' }}</td>

                            <td style="width: 14%;">Tanggal</td>
                            <td style="width: 2%;">:</td>
                            <td style="width: 22%;">{{ $report->report_date ? $report->report_date->format('d/m/Y') : '-' }}</td>
                        </tr>
                        <tr>
                            <td>Kontraktor Pelaksana</td>
                            <td>:</td>
                            <td style="font-weight: bold;">{{ $report->contractor_name }}</td>

                            <td>Hari</td>
                            <td>:</td>
                            <td>{{ $report->day_name ?: '-' }}</td>
                        </tr>
                        <tr>
                            <td>Hari Ke</td>
                            <td>:</td>
                            <td style="font-weight: bold;">{{ $report->day_number ?: '-' }}</td>

                            <td>Sisa Waktu</td>
                            <td>:</td>
                            <td>{{ $report->days_remaining ?: '-' }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <!-- SECTION A: PEKERJAAN -->
        <div class="section-header">A. &nbsp; PEKERJAAN</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 5%;">NO.</th>
                    <th style="width: 45%;">JENIS PEKERJAAN</th>
                    <th style="width: 25%;">LOKASI PEKERJAAN</th>
                    <th style="width: 25%;">KETERANGAN</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($report->tasks as $idx => $t)
                    <tr>
                        <td class="text-center">{{ $idx + 1 }}</td>
                        <td>{{ $t->task_name }}</td>
                        <td>{{ $t->location }}</td>
                        <td>{{ $t->notes }}</td>
                    </tr>
                @empty
                    <tr>
                        <td class="text-center">&nbsp;</td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- SECTION B & C -->
        <table class="two-col-table">
            <tr>
                <!-- B. BAHAN / MATERIAL -->
                <td class="col-left">
                    <div class="section-header">B. &nbsp; BAHAN/MATERIAL</div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th style="width: 8%;">NO.</th>
                                <th style="width: 92%;">JENIS BAHAN YANG DIGUNAKAN</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($report->materials as $idx => $m)
                                <tr>
                                    <td class="text-center">{{ $idx + 1 }}</td>
                                    <td>{{ $m->material_name }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="text-center">&nbsp;</td>
                                    <td></td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </td>

                <!-- C. PERALATAN YANG DIGUNAKAN -->
                <td class="col-right">
                    <div class="section-header">C. &nbsp; PERALATAN YANG DIGUNAKAN</div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th style="width: 8%;">NO.</th>
                                <th style="width: 54%;">NAMA PERALATAN</th>
                                <th style="width: 18%;">JUMLAH</th>
                                <th style="width: 20%;">SATUAN</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($report->equipments as $idx => $eq)
                                <tr>
                                    <td class="text-center">{{ $idx + 1 }}</td>
                                    <td>{{ $eq->equipment_name }}</td>
                                    <td class="text-center">{{ $eq->qty }}</td>
                                    <td class="text-center">{{ $eq->unit }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="text-center">&nbsp;</td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </td>
            </tr>
        </table>

        <!-- SECTION D & E -->
        <table class="two-col-table">
            <tr>
                <!-- D. TENAGA KERJA -->
                <td class="col-left">
                    <div class="section-header">D. &nbsp; TENAGA KERJA</div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th style="width: 8%;">NO.</th>
                                <th style="width: 62%;">JABATAN</th>
                                <th style="width: 30%;">JUMLAH ORANG</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($report->manpowers as $idx => $mp)
                                <tr>
                                    <td class="text-center">{{ $idx + 1 }}.</td>
                                    <td>{{ $mp->position }}</td>
                                    <td class="text-center">{{ $mp->manpower_count ? $mp->manpower_count . ' Orang' : '' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="text-center">&nbsp;</td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </td>

                <!-- E. CUACA -->
                <td class="col-right">
                    <div class="section-header">E. &nbsp; CUACA</div>
                    <div class="weather-box">
                        <div style="font-weight: bold; text-align: center; border-bottom: 1px solid #000; padding-bottom: 3px; margin-bottom: 8px;">
                            LAPORAN CUACA
                        </div>

                        <div class="weather-row">
                            <span class="checkbox-square">{{ $report->weather_cerah ? '✓' : '' }}</span>
                            <span class="weather-label">CERAH</span>
                            <span class="weather-time">JAM : {{ $report->weather_cerah_time ?: '08:00 s/d 17:00' }}</span>
                        </div>

                        <div class="weather-row">
                            <span class="checkbox-square">{{ $report->weather_hujan ? '✓' : '' }}</span>
                            <span class="weather-label">HUJAN</span>
                            <span class="weather-time">JAM : {{ $report->weather_hujan_time ?: '14:30 - 17:00' }}</span>
                        </div>

                        <div class="weather-row">
                            <span class="checkbox-square">{{ $report->weather_mendung ? '✓' : '' }}</span>
                            <span class="weather-label">MENDUNG</span>
                            <span class="weather-time">JAM : {{ $report->weather_mendung_time ?: '14:00 - 17:00' }}</span>
                        </div>

                        <div class="weather-row">
                            <span class="checkbox-square">{{ $report->weather_dll ? '✓' : '' }}</span>
                            <span class="weather-label">DLL</span>
                            <span class="weather-time">JAM : {{ $report->weather_dll_time ?: '.............. s/d ..............' }}</span>
                        </div>
                    </div>
                </td>
            </tr>
        </table>

        <!-- TEXT EVALUATION BOXES -->
        <div class="note-box">
            <span class="note-box-title">PLANNING HARI INI:</span>
            <div class="note-box-content">{{ $report->planning_today }}</div>
        </div>

        <div class="note-box">
            <span class="note-box-title">PENCAPAIAN HARI INI:</span>
            <div class="note-box-content">{{ $report->achievement_today }}</div>
        </div>

        <div class="note-box">
            <span class="note-box-title">KENDALA :</span>
            <div class="note-box-content">{{ $report->issues_constraints }}</div>
        </div>

        <div class="note-box">
            <span class="note-box-title">RENCANA PEKERJAAN HARI BERIKUTNYA:</span>
            <div class="note-box-content">{{ $report->next_plan }}</div>
        </div>

        <!-- SIGNATURES -->
        <table class="signature-table">
            <tr>
                <td style="padding-right: 4px;">
                    <div class="sign-box">
                        <div class="sign-title">Pemberi Tugas</div>
                        @if ($report->client_sign)
                            <img src="{{ Storage::disk('public')->url($report->client_sign) }}" class="sign-img" alt="Sign Client" />
                        @endif
                        <div class="sign-name">{{ $report->client_pic_name ?: '                                        ' }}</div>
                    </div>
                </td>
                <td style="padding-left: 4px;">
                    <div class="sign-box">
                        <div>
                            <div class="sign-title">Kontraktor Pelaksana</div>
                            <div style="font-size: 8pt; font-weight: bold;">{{ $report->contractor_name }}</div>
                        </div>
                        @if ($report->contractor_sign)
                            <img src="{{ Storage::disk('public')->url($report->contractor_sign) }}" class="sign-img" alt="Sign Contractor" />
                        @endif
                        <div class="sign-name">{{ $report->contractor_pic_name ?: ($report->creator ? $report->creator->name : '                                        ') }}</div>
                    </div>
                </td>
            </tr>
        </table>

        <!-- LAMPIRAN FOTO DOKUMENTASI (POSISI SETELAH KONTRAKTOR PELAKSANA) -->
        @if ($report->photos->count() > 0)
            <div class="page-break"></div>
            <div class="photo-section-title">
                DOKUMENTASI FOTO PEKERJAAN LAPANGAN
            </div>
            <div class="photo-grid">
                @foreach ($report->photos as $idx => $photo)
                    <div class="photo-item">
                        <img src="{{ $photo->url }}" class="photo-img" alt="Dokumentasi {{ $idx + 1 }}" />
                        <div class="photo-caption">
                            {{ $photo->caption ?: 'Dokumentasi ' . ($idx + 1) }}
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</body>

</html>
