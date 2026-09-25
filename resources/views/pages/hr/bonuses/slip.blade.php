<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Slip Rincian Bonus - {{ $recipient->employee->user?->name ?? 'Karyawan' }} ({{ $recipient->bonus->code }})</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@mdi/font@7.2.96/css/materialdesignicons.min.css">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background-color: #f1f5f9;
            color: #1e293b;
            padding: 30px 15px;
            font-size: 13px;
            line-height: 1.5;
        }
        .slip-card {
            max-width: 800px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 10px 25px -5px rgba(0,0,0,0.05), 0 8px 10px -6px rgba(0,0,0,0.03);
            border: 1px solid #e2e8f0;
            overflow: hidden;
        }
        .slip-header {
            padding: 24px 30px;
            border-bottom: 2px dashed #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .company-title {
            font-size: 18px;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -0.5px;
        }
        .slip-title {
            font-size: 14px;
            font-weight: 700;
            color: #4f46e5;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .slip-body {
            padding: 24px 30px;
        }
        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px 24px;
            background: #f8fafc;
            border-radius: 12px;
            padding: 16px 20px;
            border: 1px solid #e2e8f0;
            margin-bottom: 24px;
        }
        .info-item span:first-child {
            display: block;
            font-size: 11px;
            color: #64748b;
            text-transform: uppercase;
            font-weight: 600;
        }
        .info-item span:last-child {
            font-size: 13px;
            font-weight: 700;
            color: #1e293b;
        }
        .table-section {
            margin-bottom: 24px;
        }
        .table-section-title {
            font-size: 12px;
            font-weight: 700;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }
        th {
            background-color: #f8fafc;
            color: #475569;
            font-size: 11px;
            text-transform: uppercase;
            font-weight: 700;
            padding: 10px 14px;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
        }
        td {
            padding: 10px 14px;
            font-size: 13px;
            border-bottom: 1px solid #f1f5f9;
        }
        .text-right {
            text-align: right;
        }
        .font-mono {
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
        }
        .text-success { color: #16a34a; }
        .text-danger { color: #dc2626; }
        .text-primary { color: #4f46e5; }
        .total-box {
            background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%);
            color: #ffffff;
            border-radius: 12px;
            padding: 16px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }
        .total-box-label {
            font-size: 12px;
            text-transform: uppercase;
            font-weight: 700;
            letter-spacing: 0.5px;
            opacity: 0.9;
        }
        .total-box-amount {
            font-size: 22px;
            font-weight: 800;
            letter-spacing: -0.5px;
        }
        .signatures {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-top: 30px;
            text-align: center;
        }
        .sig-box {
            padding: 10px;
        }
        .sig-role {
            font-size: 11px;
            color: #64748b;
            text-transform: uppercase;
            font-weight: 600;
            margin-bottom: 50px;
        }
        .sig-name {
            font-size: 12px;
            font-weight: 700;
            border-top: 1px solid #cbd5e1;
            padding-top: 6px;
        }
        .print-toolbar {
            max-width: 800px;
            margin: 0 auto 20px auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            text-decoration: none;
        }
        .btn-primary { background: #4f46e5; color: #ffffff; }
        .btn-secondary { background: #e2e8f0; color: #334155; }
        @media print {
            body { background: #ffffff; padding: 0; }
            .print-toolbar { display: none; }
            .slip-card { box-shadow: none; border: 1px solid #cbd5e1; }
        }
    </style>
</head>
<body>
    @php
        $emp = $recipient->employee;
        $uName = $emp?->user?->name ?? ($emp?->nik ? 'Karyawan ' . $emp->nik : 'Karyawan #' . $recipient->employee_id);
        $dept = $emp?->department?->name ?? '-';
        $pos = $emp?->position?->name ?? ($emp?->user?->role ?? '-');
        $bonus = $recipient->bonus;
        $additions = $recipient->items->where('type', 'addition');
        $deductions = $recipient->items->where('type', 'deduction');
    @endphp

    <div class="print-toolbar">
        <a href="{{ route('hr.bonuses.show', $bonus->id) }}" class="btn btn-secondary">
            <i class="mdi mdi-arrow-left"></i> Kembali ke Batch
        </a>
        <button onclick="window.print()" class="btn btn-primary">
            <i class="mdi mdi-printer"></i> Cetak Slip Bonus
        </button>
    </div>

    <div class="slip-card">
        <div class="slip-header">
            <div>
                <div class="company-title">PT. REFRIGERATION TECHNOLOGY INDONESIA</div>
                <span style="font-size: 11px; color: #64748b;">Specialist Refrigeration &amp; HVAC Engineering</span>
            </div>
            <div style="text-align: right;">
                <div class="slip-title">Slip Rincian Bonus Semester</div>
                <span class="font-mono" style="font-size: 12px; font-weight: 700; color: #0f172a;">{{ $bonus->code }}</span>
            </div>
        </div>

        <div class="slip-body">
            <div class="info-grid">
                <div class="info-item">
                    <span>Nama Karyawan</span>
                    <span>{{ $uName }}</span>
                </div>
                <div class="info-item">
                    <span>Departemen / Jabatan</span>
                    <span>{{ $dept }} &bull; {{ $pos }}</span>
                </div>
                <div class="info-item">
                    <span>Periode Bonus</span>
                    <span>Semester {{ $bonus->semester }} Tahun {{ $bonus->year }}</span>
                </div>
                <div class="info-item">
                    <span>Status Pembayaran</span>
                    <span>{{ $recipient->payment_status === 'Paid' ? 'Lunas / Terbayar (' . ($bonus->expense?->no_expense ?? 'EXP') . ')' : 'Draft Perhitungan' }}</span>
                </div>
            </div>

            {{-- 1. Penambahan --}}
            <div class="table-section">
                <div class="table-section-title text-success">
                    <i class="mdi mdi-plus-circle-outline"></i> A. Komponen Penambahan (Bonus &amp; Insentif)
                </div>
                <table>
                    <thead>
                        <tr>
                            <th style="width: 5%;">No</th>
                            <th style="width: 65%;">Nama Komponen</th>
                            <th style="width: 30%;" class="text-right">Nominal (Rp)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($additions as $idx => $it)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td style="font-weight: 600;">{{ $it->name }}</td>
                                <td class="text-right font-mono text-success fw-bold">
                                    + Rp {{ number_format($it->amount, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" style="text-align: center; color: #94a3b8;">Tidak ada komponen penambahan</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr style="background: #f8fafc; font-weight: 700;">
                            <td colspan="2" style="text-align: right;">Total Penambahan (+) :</td>
                            <td class="text-right font-mono text-success">
                                Rp {{ number_format($recipient->total_addition, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            {{-- 2. Pengurangan (Jika ada) --}}
            @if ($deductions->isNotEmpty())
            <div class="table-section">
                <div class="table-section-title text-danger">
                    <i class="mdi mdi-minus-circle-outline"></i> B. Komponen Pengurangan (Potongan / Penyesuaian)
                </div>
                <table>
                    <thead>
                        <tr>
                            <th style="width: 5%;">No</th>
                            <th style="width: 65%;">Nama Komponen</th>
                            <th style="width: 30%;" class="text-right">Nominal (Rp)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($deductions as $idx => $it)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td style="font-weight: 600;">{{ $it->name }}</td>
                                <td class="text-right font-mono text-danger fw-bold">
                                    - Rp {{ number_format($it->amount, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr style="background: #f8fafc; font-weight: 700;">
                            <td colspan="2" style="text-align: right;">Total Pengurangan (-) :</td>
                            <td class="text-right font-mono text-danger">
                                Rp {{ number_format($recipient->total_deduction, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            @endif

            {{-- Total Box --}}
            <div class="total-box">
                <div>
                    <div class="total-box-label">Total Bonus Bersih Diterima (Take Home Bonus)</div>
                    @if ($recipient->notes)
                        <div style="font-size: 11px; opacity: 0.85; margin-top: 2px;">Catatan: {{ $recipient->notes }}</div>
                    @endif
                </div>
                <div class="total-box-amount font-mono">
                    Rp {{ number_format($recipient->net_bonus, 0, ',', '.') }}
                </div>
            </div>

            {{-- Signatures --}}
            <div class="signatures">
                <div class="sig-box">
                    <div class="sig-role">Disusun Oleh</div>
                    <div class="sig-name">{{ $bonus->generator?->name ?? 'HR Department' }}</div>
                </div>
                <div class="sig-box">
                    <div class="sig-role">Disetujui Oleh</div>
                    <div class="sig-name">{{ $bonus->approver?->name ?? 'Direksi / Finance' }}</div>
                </div>
                <div class="sig-box">
                    <div class="sig-role">Diterima Oleh</div>
                    <div class="sig-name">{{ $uName }}</div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
