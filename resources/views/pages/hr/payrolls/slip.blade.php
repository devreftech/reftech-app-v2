<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Slip Gaji - {{ $item->slip_number }} - {{ $item->employee?->user?->name ?? 'Karyawan' }}</title>
    
    <!-- Fonts & Core Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=JetBrains+Mono:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/core.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/materialdesignicons.css') }}" />

    <style>
        :root {
            --slate-900: #0f172a;
            --slate-800: #1e293b;
            --slate-700: #334155;
            --slate-600: #475569;
            --slate-500: #64748b;
            --slate-200: #e2e8f0;
            --slate-100: #f1f5f9;
            --slate-50: #f8fafc;
        }

        * {
            box-sizing: border-box;
        }

        body {
            background-color: #f1f5f9;
            color: var(--slate-800);
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            margin: 0;
            padding: 0;
            -webkit-font-smoothing: antialiased;
            font-size: 13px;
            line-height: 1.5;
        }

        /* Top Action Bar (Screen Only) */
        .slip-action-bar {
            background: #ffffff;
            border-bottom: 1px solid var(--slate-200);
            padding: 0.75rem 1.5rem;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        /* Sheet Wrapper */
        .slip-wrapper {
            padding: 2rem 1rem;
            display: flex;
            justify-content: center;
        }

        /* Simple A4 Sheet */
        .slip-sheet {
            background: #ffffff;
            width: 210mm;
            min-height: 297mm;
            padding: 16mm 18mm 16mm 18mm;
            box-shadow: 0 4px 20px rgba(15, 23, 42, 0.06);
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        /* Header Simple */
        .company-logo {
            max-height: 44px;
            width: auto;
            object-fit: contain;
        }

        .company-name {
            font-size: 1.15rem;
            font-weight: 700;
            color: var(--slate-900);
            letter-spacing: -0.2px;
            margin: 0 0 2px 0;
        }

        .company-desc {
            font-size: 0.75rem;
            color: var(--slate-500);
            line-height: 1.4;
            margin: 0;
        }

        .slip-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--slate-900);
            letter-spacing: 0.5px;
            text-transform: uppercase;
            margin: 0;
        }

        .slip-meta-no {
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--slate-600);
            margin-top: 2px;
        }

        .header-line {
            height: 2px;
            background-color: var(--slate-800);
            margin: 1.25rem 0 1.25rem 0;
        }

        /* Employee Metadata Box (Simple & Clean) */
        .emp-info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 1.25rem;
            font-size: 0.82rem;
        }

        .emp-info-table td {
            padding: 0.35rem 0.5rem;
            vertical-align: top;
        }

        .emp-info-table td.label {
            color: var(--slate-500);
            width: 16%;
            font-weight: 500;
        }

        .emp-info-table td.separator {
            width: 2%;
            color: var(--slate-400);
        }

        .emp-info-table td.val {
            color: var(--slate-900);
            font-weight: 600;
            width: 32%;
        }

        /* Table Rincian Gaji (Penerimaan & Potongan) */
        .breakdown-container {
            display: flex;
            gap: 1.25rem;
            margin-bottom: 1.25rem;
        }

        .breakdown-box {
            flex: 1;
            border: 1px solid var(--slate-200);
            border-radius: 4px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .breakdown-head {
            background-color: var(--slate-100);
            border-bottom: 1px solid var(--slate-200);
            padding: 0.5rem 0.75rem;
            font-weight: 700;
            font-size: 0.78rem;
            color: var(--slate-800);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .salary-list-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.82rem;
            flex-grow: 1;
        }

        .salary-list-table tr {
            border-bottom: 1px solid #f1f5f9;
        }

        .salary-list-table tr:last-child {
            border-bottom: none;
        }

        .salary-list-table td {
            padding: 0.45rem 0.75rem;
        }

        .salary-list-table td.amount {
            font-family: 'JetBrains Mono', monospace;
            text-align: right;
            font-weight: 600;
            white-space: nowrap;
        }

        .breakdown-foot {
            background-color: var(--slate-50);
            border-top: 1px solid var(--slate-200);
            padding: 0.5rem 0.75rem;
            font-weight: 700;
            font-size: 0.82rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        /* Simple Take Home Pay Card */
        .thp-summary {
            background-color: var(--slate-50);
            border: 1.5px solid var(--slate-700);
            border-radius: 4px;
            padding: 0.85rem 1.25rem;
            margin-bottom: 1.75rem;
        }

        .thp-title {
            font-size: 0.78rem;
            font-weight: 700;
            color: var(--slate-600);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
        }

        .thp-nominal {
            font-family: 'JetBrains Mono', monospace;
            font-size: 1.6rem;
            font-weight: 700;
            color: var(--slate-900);
            letter-spacing: -0.5px;
            line-height: 1.2;
        }

        .thp-spell {
            font-size: 0.75rem;
            color: var(--slate-600);
            font-style: italic;
            margin-top: 0.35rem;
            padding-top: 0.35rem;
            border-top: 1px dashed var(--slate-200);
        }

        /* Signatures Simple */
        .signatures-grid {
            display: flex;
            justify-content: space-between;
            margin-top: 1.5rem;
            padding: 0 1rem;
        }

        .signature-item {
            text-align: center;
            width: 28%;
        }

        .signature-role {
            font-size: 0.78rem;
            color: var(--slate-600);
            margin-bottom: 3.5rem;
        }

        .signature-line {
            height: 1px;
            background-color: var(--slate-400);
            margin-bottom: 0.35rem;
        }

        .signature-name {
            font-size: 0.82rem;
            font-weight: 700;
            color: var(--slate-900);
        }

        .signature-date {
            font-size: 0.7rem;
            color: var(--slate-500);
        }

        /* Footer Simple */
        .slip-footer {
            border-top: 1px solid var(--slate-200);
            padding-top: 0.65rem;
            font-size: 0.7rem;
            color: var(--slate-500);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        /* Privacy Blur */
        .blur-amount .amount,
        .blur-amount .thp-nominal {
            filter: blur(5px);
            transition: filter 0.2s ease;
        }

        /* ── PRINT RULES ────────────────────────────── */
        @media print {
            @page {
                size: A4 portrait;
                margin: 10mm 12mm;
            }

            body {
                background: #ffffff !important;
                color: #000000 !important;
            }

            .slip-action-bar,
            .no-print {
                display: none !important;
            }

            .slip-wrapper {
                padding: 0 !important;
                margin: 0 !important;
            }

            .slip-sheet {
                width: 100% !important;
                min-height: auto !important;
                padding: 0 !important;
                box-shadow: none !important;
                border: none !important;
            }

            .thp-summary {
                background-color: #f8fafc !important;
                border: 1.5px solid #000000 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .breakdown-head,
            .breakdown-foot {
                background-color: #f1f5f9 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .header-line {
                background-color: #000000 !important;
            }
        }
    </style>
</head>
<body>

@php
    // Helper Terbilang Bahasa Indonesia
    if (!function_exists('penyebutTerbilang')) {
        function penyebutTerbilang($nilai) {
            $nilai = abs($nilai);
            $huruf = array("", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");
            $temp = "";
            if ($nilai < 12) {
                $temp = " ". $huruf[$nilai];
            } else if ($nilai < 20) {
                $temp = penyebutTerbilang($nilai - 10). " belas";
            } else if ($nilai < 100) {
                $temp = penyebutTerbilang((int)($nilai/10))." puluh". penyebutTerbilang($nilai % 10);
            } else if ($nilai < 200) {
                $temp = " seratus" . penyebutTerbilang($nilai - 100);
            } else if ($nilai < 1000) {
                $temp = penyebutTerbilang((int)($nilai/100)) . " ratus" . penyebutTerbilang($nilai % 100);
            } else if ($nilai < 2000) {
                $temp = " seribu" . penyebutTerbilang($nilai - 1000);
            } else if ($nilai < 1000000) {
                $temp = penyebutTerbilang((int)($nilai/1000)) . " ribu" . penyebutTerbilang($nilai % 1000);
            } else if ($nilai < 1000000000) {
                $temp = penyebutTerbilang((int)($nilai/1000000)) . " juta" . penyebutTerbilang($nilai % 1000000);
            } else if ($nilai < 1000000000000) {
                $temp = penyebutTerbilang((int)($nilai/1000000000)) . " milyar" . penyebutTerbilang(fmod($nilai,1000000000));
            } else if ($nilai < 1000000000000000) {
                $temp = penyebutTerbilang((int)($nilai/1000000000000)) . " trilyun" . penyebutTerbilang(fmod($nilai,1000000000000));
            }
            return $temp;
        }
    }

    if (!function_exists('terbilangRupiah')) {
        function terbilangRupiah($angka) {
            if ($angka <= 0) return "Nol Rupiah";
            return ucwords(trim(penyebutTerbilang($angka))) . " Rupiah";
        }
    }

    $emp = $item->employee;
    $empName = $emp?->user?->name ?? ($emp?->nik ? 'Karyawan ' . $emp->nik : 'Karyawan #' . $item->employee_id);
    $totAllowance = $item->total_allowance + $item->overtime_pay;
    $totGross = $item->basic_salary + $totAllowance;
    $totDeduction = $item->deduction_absence + $item->deduction_bpjs + $item->deduction_other;
    $terbilangText = terbilangRupiah($item->net_salary);
    $periodMonth = \Carbon\Carbon::createFromDate($item->payroll->period_year, $item->payroll->period_month, 1)->translatedFormat('F Y');
@endphp

<!-- Screen Action Bar -->
<div class="slip-action-bar no-print">
    <div class="container-fluid d-flex flex-wrap align-items-center justify-content-between gap-2">
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('hr.payrolls.show', $item->payroll_id) }}" class="btn btn-sm btn-outline-secondary">
                <i class="mdi mdi-arrow-left me-1"></i> Kembali ke Payroll
            </a>
            <span class="text-muted small d-none d-md-inline">| Slip: <strong class="text-dark">{{ $item->slip_number }}</strong> &bull; {{ $empName }}</span>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-sm btn-outline-secondary" id="togglePrivacyBtn" onclick="togglePrivacy()">
                <i class="mdi mdi-eye-off-outline me-1"></i> Sembunyikan Nominal
            </button>
            <button type="button" class="btn btn-sm btn-dark shadow-xs" onclick="window.print()">
                <i class="mdi mdi-printer me-1"></i> Cetak / Simpan PDF
            </button>
        </div>
    </div>
</div>

<!-- Main Sheet -->
<div class="slip-wrapper" id="slipWrapper">
    <div class="slip-sheet">
        <div>
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-start">
                <div class="d-flex align-items-center gap-3">
                    <img src="{{ asset('asset/logo/Reftech-Log.png') }}" alt="Reftech Logo" class="company-logo" onerror="this.style.display='none'">
                    <div>
                        <h4 class="company-name">PT. REFTECH JAYA OPTIMA</h4>
                        <p class="company-desc">
                            Taman Kopo Indah V, Ruko Sommerville No. 31, Bandung – Jawa Barat 40218<br>
                            Telp: (022) 54417653 &bull; Email: hr@reftech.id &bull; Website: www.reftech.id
                        </p>
                    </div>
                </div>
                <div class="text-end">
                    <h3 class="slip-title">SLIP GAJI</h3>
                    <div class="slip-meta-no">{{ $item->slip_number }}</div>
                    <div class="mt-1">
                        <span class="badge {{ $item->payment_status === 'Paid' ? 'bg-label-success' : 'bg-label-warning' }} text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                            {{ $item->payment_status === 'Paid' ? 'LUNAS / DITRANSFER' : $item->payment_status }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Divider Line -->
            <div class="header-line"></div>

            <!-- Employee Info Table (Simple 2 Columns) -->
            <table class="emp-info-table">
                <tr>
                    <td class="label">Nama Pegawai</td>
                    <td class="separator">:</td>
                    <td class="val">{{ $empName }}</td>

                    <td class="label">Periode Gaji</td>
                    <td class="separator">:</td>
                    <td class="val">{{ $periodMonth }}</td>
                </tr>
                <tr>
                    <td class="label">NIK</td>
                    <td class="separator">:</td>
                    <td class="val font-monospace">{{ $emp?->nik ?? '-' }}</td>

                    <td class="label">Tanggal Bayar</td>
                    <td class="separator">:</td>
                    <td class="val">{{ $item->payroll->payment_date ? \Carbon\Carbon::parse($item->payroll->payment_date)->translatedFormat('d F Y') : '-' }}</td>
                </tr>
                <tr>
                    <td class="label">Jabatan</td>
                    <td class="separator">:</td>
                    <td class="val">{{ $emp?->position?->name ?? 'Staff' }}</td>

                    <td class="label">Kehadiran</td>
                    <td class="separator">:</td>
                    <td class="val">{{ $item->attendance_days }} Hari Kerja @if($item->overtime_hours > 0) &bull; Lembur {{ $item->overtime_hours }} Jam @endif</td>
                </tr>
                <tr>
                    <td class="label">Departemen</td>
                    <td class="separator">:</td>
                    <td class="val">{{ $emp?->department?->name ?? 'Operasional' }}</td>

                    <td class="label">Transfer Bank</td>
                    <td class="separator">:</td>
                    <td class="val font-monospace">
                        {{ $item->meta_data['bank_name'] ?? 'BCA' }} - {{ $item->meta_data['bank_account_number'] ?? '-' }}
                    </td>
                </tr>
            </table>

            <!-- Breakdown: Earnings & Deductions -->
            <div class="breakdown-container">
                <!-- Column 1: Penerimaan -->
                <div class="breakdown-box">
                    <div class="breakdown-head">
                        <span>Penerimaan (Earnings)</span>
                        <span class="text-muted font-monospace">IDR</span>
                    </div>
                    <table class="salary-list-table">
                        <tbody>
                            <tr>
                                <td>Gaji Pokok</td>
                                <td class="amount">Rp {{ number_format($item->basic_salary, 0, ',', '.') }}</td>
                            </tr>
                            @if ($item->position_allowance > 0)
                            <tr>
                                <td>Tunjangan Jabatan</td>
                                <td class="amount">Rp {{ number_format($item->position_allowance, 0, ',', '.') }}</td>
                            </tr>
                            @endif
                            @if ($item->transport_allowance > 0)
                            <tr>
                                <td>Tunjangan Transportasi</td>
                                <td class="amount">Rp {{ number_format($item->transport_allowance, 0, ',', '.') }}</td>
                            </tr>
                            @endif
                            @if ($item->meal_allowance > 0)
                            <tr>
                                <td>Tunjangan Makan</td>
                                <td class="amount">Rp {{ number_format($item->meal_allowance, 0, ',', '.') }}</td>
                            </tr>
                            @endif
                            @if ($item->other_allowance > 0)
                            <tr>
                                <td>Tunjangan Lainnya</td>
                                <td class="amount">Rp {{ number_format($item->other_allowance, 0, ',', '.') }}</td>
                            </tr>
                            @endif
                            @if ($item->overtime_pay > 0)
                            <tr>
                                <td>Upah Lembur ({{ $item->overtime_hours }} Jam)</td>
                                <td class="amount">Rp {{ number_format($item->overtime_pay, 0, ',', '.') }}</td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                    <div class="breakdown-foot">
                        <span>Total Penghasilan (A)</span>
                        <span class="amount">Rp {{ number_format($totGross, 0, ',', '.') }}</span>
                    </div>
                </div>

                <!-- Column 2: Potongan -->
                <div class="breakdown-box">
                    <div class="breakdown-head">
                        <span>Potongan (Deductions)</span>
                        <span class="text-muted font-monospace">IDR</span>
                    </div>
                    <table class="salary-list-table">
                        <tbody>
                            @if ($item->deduction_bpjs > 0)
                            <tr>
                                <td>Potongan BPJS (Kes &amp; TK)</td>
                                <td class="amount text-danger">-Rp {{ number_format($item->deduction_bpjs, 0, ',', '.') }}</td>
                            </tr>
                            @endif
                            @if ($item->deduction_absence > 0)
                            <tr>
                                <td>Potongan Kehadiran ({{ $item->absence_days }} Hari)</td>
                                <td class="amount text-danger">-Rp {{ number_format($item->deduction_absence, 0, ',', '.') }}</td>
                            </tr>
                            @endif
                            @if ($item->deduction_other > 0)
                            <tr>
                                <td>Potongan Lain-lain / Kasbon</td>
                                <td class="amount text-danger">-Rp {{ number_format($item->deduction_other, 0, ',', '.') }}</td>
                            </tr>
                            @endif
                            @if ($totDeduction == 0)
                            <tr>
                                <td colspan="2" class="text-center text-muted py-4 small">
                                    Tidak ada potongan pada periode ini
                                </td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                    <div class="breakdown-foot">
                        <span>Total Potongan (B)</span>
                        <span class="amount {{ $totDeduction > 0 ? 'text-danger' : '' }}">
                            {{ $totDeduction > 0 ? '-Rp ' . number_format($totDeduction, 0, ',', '.') : 'Rp 0' }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Take Home Pay Simple Card -->
            <div class="thp-summary">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="thp-title">Gaji Bersih Diterima (Take Home Pay = A - B)</div>
                        <div class="thp-nominal">Rp {{ number_format($item->net_salary, 0, ',', '.') }}</div>
                    </div>
                    <div class="text-end">
                        <span class="badge bg-label-dark font-monospace text-uppercase" style="font-size: 0.72rem;">
                            Bank: {{ $item->meta_data['bank_name'] ?? 'BCA' }}
                        </span>
                    </div>
                </div>
                <div class="thp-spell">
                    Terbilang: # {{ $terbilangText }} #
                </div>
            </div>

            <!-- Signatures Simple -->
            <div class="signatures-grid">
                <div class="signature-item">
                    <div class="signature-role">Dibuat Oleh,</div>
                    <div class="signature-line"></div>
                    <div class="signature-name">HRD Department</div>
                    <div class="signature-date">PT. Reftech Jaya Optima</div>
                </div>

                <div class="signature-item">
                    <div class="signature-role">Disetujui Oleh,</div>
                    <div class="signature-line"></div>
                    <div class="signature-name">Finance Manager</div>
                    <div class="signature-date">PT. Reftech Jaya Optima</div>
                </div>

                <div class="signature-item">
                    <div class="signature-role">Diterima Oleh,</div>
                    <div class="signature-line"></div>
                    <div class="signature-name">{{ $empName }}</div>
                    <div class="signature-date">Karyawan</div>
                </div>
            </div>
        </div>

        <!-- Legal Disclaimer Simple Footer -->
        <div class="slip-footer">
            <div>
                Dokumen ini dicetak otomatis oleh HRMS RefTech dan sah sebagai bukti pembayaran gaji.
            </div>
            <div class="font-monospace">
                Dicetak: {{ now()->translatedFormat('d/m/Y H:i') }} WIB
            </div>
        </div>
    </div>
</div>

<script>
    function togglePrivacy() {
        const wrapper = document.getElementById('slipWrapper');
        const btn = document.getElementById('togglePrivacyBtn');
        if (wrapper.classList.contains('blur-amount')) {
            wrapper.classList.remove('blur-amount');
            btn.innerHTML = '<i class="mdi mdi-eye-off-outline me-1"></i> Sembunyikan Nominal';
        } else {
            wrapper.classList.add('blur-amount');
            btn.innerHTML = '<i class="mdi mdi-eye-outline me-1"></i> Tampilkan Nominal';
        }
    }
</script>

</body>
</html>
