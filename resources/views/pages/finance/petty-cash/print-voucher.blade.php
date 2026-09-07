<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voucher Kas - {{ $tx->voucher_number }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@mdi/font@7.2.96/css/materialdesignicons.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 13px;
            color: #222;
            background-color: #f4f6f9;
        }
        .voucher-container {
            max-width: 800px;
            margin: 20px auto;
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            border: 1px solid #e5e7eb;
        }
        .voucher-title {
            font-size: 18px;
            font-weight: 800;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        .table-voucher th, .table-voucher td {
            padding: 8px 12px;
            vertical-align: middle;
        }
        .signature-box {
            height: 75px;
        }
        @media print {
            body {
                background: #fff;
                padding: 0;
            }
            .voucher-container {
                box-shadow: none;
                border: none;
                margin: 0;
                padding: 15px;
                max-width: 100%;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>

    <div class="container my-3 no-print text-center">
        <button onclick="window.print()" class="btn btn-primary px-4 me-2 shadow-sm">
            <i class="mdi mdi-printer me-1"></i> Cetak Voucher
        </button>
        <button onclick="window.close()" class="btn btn-secondary px-3 shadow-sm">
            <i class="mdi mdi-close me-1"></i> Tutup
        </button>
    </div>

    @php
        function terbilang($angka) {
            $angka = abs((float)$angka);
            $baca = array("", "Satu", "Dua", "Tiga", "Empat", "Lima", "Enam", "Tujuh", "Delapan", "Sembilan", "Sepuluh", "Sebelas");
            $terbilang = "";
            if ($angka < 12) {
                $terbilang = " " . $baca[$angka];
            } else if ($angka < 20) {
                $terbilang = terbilang($angka - 10) . " Belas";
            } else if ($angka < 100) {
                $terbilang = terbilang(floor($angka / 10)) . " Puluh" . terbilang($angka % 10);
            } else if ($angka < 200) {
                $terbilang = " Seratus" . terbilang($angka - 100);
            } else if ($angka < 1000) {
                $terbilang = terbilang(floor($angka / 100)) . " Ratus" . terbilang($angka % 100);
            } else if ($angka < 2000) {
                $terbilang = " Seribu" . terbilang($angka - 1000);
            } else if ($angka < 1000000) {
                $terbilang = terbilang(floor($angka / 1000)) . " Ribu" . terbilang($angka % 1000);
            } else if ($angka < 1000000000) {
                $terbilang = terbilang(floor($angka / 1000000)) . " Juta" . terbilang($angka % 1000000);
            } else if ($angka < 1000000000000) {
                $terbilang = terbilang(floor($angka / 1000000000)) . " Milyar" . terbilang(fmod($angka, 1000000000));
            }
            return $terbilang;
        }
    @endphp

    <div class="voucher-container">
        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-3">
            <div>
                <h5 class="fw-bold mb-0 text-primary">PT. REFRIGERASI TEKNIK INDONESIA</h5>
                <small class="text-muted d-block">HVAC &amp; Industrial Refrigeration Solution</small>
                <small class="text-muted" style="font-size: 11px;">Jl. Raya No. 123, Bandung | Finance &amp; Accounting Division</small>
            </div>
            <div class="text-end">
                <div class="voucher-title {{ $tx->type === 'disbursement' ? 'text-danger' : 'text-success' }}">
                    {{ $tx->type === 'disbursement' ? 'BUKTI KAS KELUAR (BKK)' : 'BUKTI KAS MASUK (BKM)' }}
                </div>
                <div class="fw-bold fs-6 font-monospace mt-1 text-dark">{{ $tx->voucher_number }}</div>
            </div>
        </div>

        {{-- Meta Info --}}
        <div class="row g-2 mb-3" style="font-size: 12.5px;">
            <div class="col-6">
                <table class="table table-sm table-borderless mb-0">
                    <tr>
                        <td class="text-muted fw-semibold" style="width: 120px;">Akun Kas / Bank:</td>
                        <td class="fw-bold text-dark">{{ $tx->bank->bank ?? 'Kas Kecil' }} ({{ $tx->bank->no_rek ?? '-' }})</td>
                    </tr>
                    <tr>
                        <td class="text-muted fw-semibold">PIC Pemegang:</td>
                        <td class="fw-bold text-dark">{{ $tx->bank->pic?->name ?? ($tx->creator?->name ?? 'Kasir') }}</td>
                    </tr>
                    @if($tx->type === 'topup' && $tx->sourceBank)
                        <tr>
                            <td class="text-muted fw-semibold">Sumber Dana:</td>
                            <td class="fw-bold text-dark">{{ $tx->sourceBank->bank }} ({{ $tx->sourceBank->no_rek }})</td>
                        </tr>
                    @endif
                </table>
            </div>
            <div class="col-6">
                <table class="table table-sm table-borderless mb-0">
                    <tr>
                        <td class="text-muted fw-semibold" style="width: 120px;">Tanggal:</td>
                        <td class="fw-bold text-dark">{{ $tx->date->translatedFormat('d F Y') }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted fw-semibold">{{ $tx->type === 'disbursement' ? 'Dibayarkan Kepada:' : 'Diterima Dari:' }}</td>
                        <td class="fw-bold text-dark">{{ $tx->recipient ?: '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted fw-semibold">Kategori:</td>
                        <td class="fw-bold text-dark">{{ $tx->category }}</td>
                    </tr>
                </table>
            </div>
        </div>

        {{-- Detail Table --}}
        <table class="table table-bordered table-voucher mb-3">
            <thead class="table-light">
                <tr style="font-size: 12px;">
                    <th style="width: 40px;" class="text-center">No.</th>
                    <th>Rincian / Uraian Keperluan</th>
                    <th style="width: 200px;" class="text-end">Jumlah (Rp)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="text-center">1</td>
                    <td>
                        <div class="fw-semibold text-dark">{{ $tx->description }}</div>
                        <small class="text-muted">Kategori: {{ $tx->category }} &bull; Diinput oleh: {{ $tx->creator?->name ?? 'Kasir' }}</small>
                    </td>
                    <td class="text-end fw-bold fs-6">
                        Rp {{ number_format($tx->amount, 0, ',', '.') }}
                    </td>
                </tr>
            </tbody>
            <tfoot>
                <tr class="table-light">
                    <td colspan="2" class="text-end fw-bold">TOTAL NOMINAL:</td>
                    <td class="text-end fw-bolder fs-6 {{ $tx->type === 'disbursement' ? 'text-danger' : 'text-primary' }}">
                        Rp {{ number_format($tx->amount, 0, ',', '.') }}
                    </td>
                </tr>
            </tfoot>
        </table>

        {{-- Terbilang Box --}}
        <div class="p-2 mb-4 rounded border bg-light" style="font-size: 12px;">
            <span class="text-muted fw-bold">Terbilang:</span>
            <span class="fst-italic fw-semibold text-dark">{{ trim(terbilang($tx->amount)) }} Rupiah</span>
        </div>

        {{-- Signature Section --}}
        <div class="row g-2 text-center pt-2" style="font-size: 12px;">
            <div class="col-3">
                <div class="border rounded p-2">
                    <div class="fw-bold text-muted small">Disiapkan Oleh,</div>
                    <div class="signature-box d-flex align-items-end justify-content-center">
                        <span class="border-bottom border-dark w-75 pb-1 fw-semibold text-dark">{{ $tx->creator?->name ?? 'Kasir' }}</span>
                    </div>
                    <small class="text-muted">Kasir / Pembuat</small>
                </div>
            </div>
            <div class="col-3">
                <div class="border rounded p-2">
                    <div class="fw-bold text-muted small">Diperiksa Oleh,</div>
                    <div class="signature-box d-flex align-items-end justify-content-center">
                        <span class="border-bottom border-dark w-75 pb-1 fw-semibold text-dark">{{ $tx->bank->pic?->name ?? 'Head Finance' }}</span>
                    </div>
                    <small class="text-muted">Finance / PIC</small>
                </div>
            </div>
            <div class="col-3">
                <div class="border rounded p-2">
                    <div class="fw-bold text-muted small">Disetujui Oleh,</div>
                    <div class="signature-box d-flex align-items-end justify-content-center">
                        <span class="border-bottom border-dark w-75 pb-1 fw-semibold text-dark">( ......................... )</span>
                    </div>
                    <small class="text-muted">Direksi / Manager</small>
                </div>
            </div>
            <div class="col-3">
                <div class="border rounded p-2">
                    <div class="fw-bold text-muted small">{{ $tx->type === 'disbursement' ? 'Penerima Uang,' : 'Penyetor Uang,' }}</div>
                    <div class="signature-box d-flex align-items-end justify-content-center">
                        <span class="border-bottom border-dark w-75 pb-1 fw-semibold text-dark">{{ $tx->recipient ?: '( ......................... )' }}</span>
                    </div>
                    <small class="text-muted">{{ $tx->type === 'disbursement' ? 'Penerima' : 'Penyetor' }}</small>
                </div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="d-flex justify-content-between align-items-center mt-4 pt-2 border-top text-muted" style="font-size: 10px;">
            <span>Dicetak pada: {{ \Carbon\Carbon::now()->translatedFormat('d F Y H:i:s') }}</span>
            <span>Reftech ERP System - Modul Petty Cash</span>
        </div>
    </div>

</body>
</html>
