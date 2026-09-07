<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kwitansi Pembayaran - {{ $kwitansiNumber }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@mdi/font@6.5.95/css/materialdesignicons.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f3f4f6;
            color: #1f2937;
        }
        .kwitansi-container {
            max-width: 820px;
            margin: 30px auto;
            background: #ffffff;
            border: 2px solid #1e3a8a;
            border-radius: 8px;
            padding: 35px 40px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.08);
            position: relative;
        }
        .kwitansi-container::before {
            content: "";
            position: absolute;
            top: 6px;
            left: 6px;
            right: 6px;
            bottom: 6px;
            border: 1px dashed #93c5fd;
            border-radius: 4px;
            pointer-events: none;
        }
        .header-kwitansi {
            border-bottom: 2px solid #1e3a8a;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }
        .title-kwitansi {
            font-size: 24px;
            font-weight: 800;
            letter-spacing: 2px;
            color: #1e3a8a;
            text-transform: uppercase;
        }
        .badge-number {
            background-color: #eff6ff;
            border: 1px solid #bfdbfe;
            color: #1e40af;
            font-weight: 700;
            padding: 5px 12px;
            border-radius: 4px;
            font-size: 13px;
        }
        .row-item {
            display: flex;
            align-items: baseline;
            margin-bottom: 14px;
            font-size: 13.5px;
        }
        .label-kwitansi {
            width: 210px;
            font-weight: 600;
            color: #4b5563;
            flex-shrink: 0;
        }
        .colon {
            width: 15px;
            font-weight: 600;
            color: #4b5563;
            flex-shrink: 0;
        }
        .value-kwitansi {
            flex-grow: 1;
            border-bottom: 1px dotted #9ca3af;
            padding-bottom: 3px;
            color: #111827;
        }
        .terbilang-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-left: 4px solid #3b82f6;
            padding: 10px 14px;
            border-radius: 4px;
            font-style: italic;
            font-weight: 600;
            color: #1e3a8a;
            margin: 15px 0;
        }
        .amount-box {
            background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);
            color: #ffffff;
            padding: 12px 24px;
            border-radius: 6px;
            display: inline-block;
            font-size: 20px;
            font-weight: 800;
            letter-spacing: 0.5px;
            box-shadow: 0 4px 10px rgba(37,99,235,0.25);
        }
        .watermark-paid {
            position: absolute;
            right: 60px;
            top: 45%;
            transform: translateY(-50%) rotate(-18deg);
            border: 4px solid #16a34a;
            color: #16a34a;
            font-size: 38px;
            font-weight: 900;
            text-transform: uppercase;
            padding: 6px 20px;
            border-radius: 8px;
            opacity: 0.25;
            letter-spacing: 4px;
            pointer-events: none;
        }
        .signature-area {
            margin-top: 30px;
        }
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background: #fff;
                padding: 0;
            }
            .kwitansi-container {
                box-shadow: none;
                margin: 0;
                max-width: 100%;
                border: 2px solid #1e3a8a !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .amount-box {
                background: #1e3a8a !important;
                color: #fff !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body class="py-4">
    {{-- Action Bar --}}
    <div class="no-print d-flex justify-content-between align-items-center mb-3 mx-auto" style="max-width: 820px;">
        <div class="d-flex align-items-center gap-2">
            <a href="javascript:history.back()" class="btn btn-outline-secondary btn-sm">
                <i class="mdi mdi-arrow-left me-1"></i> Kembali
            </a>
            <span class="text-muted small">Pratinjau Bukti Kwitansi / Tanda Terima Resmi</span>
        </div>
        <div class="d-flex gap-2">
            <button onclick="window.print()" class="btn btn-primary btn-sm px-3">
                <i class="mdi mdi-printer me-1"></i> Cetak Kwitansi
            </button>
        </div>
    </div>

    <div class="kwitansi-container">
        {{-- Watermark Status --}}
        @if($payment->level == 1)
            <div class="watermark-paid">LUNAS / VERIFIED</div>
        @endif

        {{-- Header --}}
        <div class="header-kwitansi">
            <div class="row align-items-center">
                <div class="col-7">
                    <h5 class="fw-bold mb-0 text-primary" style="font-size: 16px; letter-spacing: 0.5px;">PT. REFRIGERASI TEKNIK INDONESIA</h5>
                    <small class="text-muted d-block" style="font-size: 11px;">
                        Ruko Grand Galaxy City Blok RSN 7 No. 15, Bekasi Selatan, Jawa Barat<br>
                        Telp: (021) 8273 4567 | NPWP: 72.123.456.7-432.000
                    </small>
                </div>
                <div class="col-5 text-end">
                    <div class="title-kwitansi">KWITANSI</div>
                    <div class="mt-1">
                        <span class="badge-number">No: {{ $kwitansiNumber }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Content Rows --}}
        <div class="mt-4">
            <div class="row-item">
                <div class="label-kwitansi">Telah Terima Dari</div>
                <div class="colon">:</div>
                <div class="value-kwitansi fw-bold text-uppercase">
                    {{ $client ? $client->company : '-' }}
                </div>
            </div>

            <div class="row-item">
                <div class="label-kwitansi">Uang Sejumlah</div>
                <div class="colon">:</div>
                <div class="value-kwitansi" style="border-bottom: none;">
                    <div class="terbilang-box">
                        # {{ $terbilang }} Rupiah #
                    </div>
                </div>
            </div>

            <div class="row-item">
                <div class="label-kwitansi">Untuk Pembayaran</div>
                <div class="colon">:</div>
                <div class="value-kwitansi">
                    <strong>{{ $paymentType }}</strong> 
                    @if($invoiceNumber)
                        - Inv No. <strong>{{ $invoiceNumber }}</strong>
                    @endif
                    @if($poNumber)
                        (PO No: <strong>{{ $poNumber }}</strong>)
                    @endif
                    @if($payment->remarks)
                        - <em>{{ $payment->remarks }}</em>
                    @endif
                </div>
            </div>

            @if($payment->bank)
                <div class="row-item">
                    <div class="label-kwitansi">Metode / Bank Masuk</div>
                    <div class="colon">:</div>
                    <div class="value-kwitansi">
                        Transfer Rekening {{ $payment->bank->nama_bank }} (A/N: {{ $payment->bank->atas_nama }} - Rek: {{ $payment->bank->no_rekening }})
                    </div>
                </div>
            @endif

            <div class="row-item">
                <div class="label-kwitansi">Tanggal Pembayaran</div>
                <div class="colon">:</div>
                <div class="value-kwitansi">
                    {{ $payment->payment_date ? Carbon\Carbon::parse($payment->payment_date)->translatedFormat('d F Y') : Carbon\Carbon::parse($payment->created_at)->translatedFormat('d F Y') }}
                </div>
            </div>
        </div>

        {{-- Bottom Section (Amount & Signature) --}}
        <div class="signature-area pt-3">
            <div class="row align-items-center">
                <div class="col-6">
                    <div class="text-muted small mb-1 fw-semibold">Jumlah Terbilang:</div>
                    <div class="amount-box">
                        Rp {{ number_format($amount, 0, ',', '.') }},-
                    </div>
                    <div class="mt-2 text-muted small" style="font-size: 11px;">
                        * Kwitansi ini sah apabila bukti transfer bank telah terverifikasi.
                    </div>
                </div>
                <div class="col-6 text-center">
                    <div class="text-muted small">
                        Bekasi, {{ $payment->created_at ? Carbon\Carbon::parse($payment->created_at)->translatedFormat('d F Y') : Carbon\Carbon::now()->translatedFormat('d F Y') }}
                    </div>
                    <div class="fw-semibold small text-muted">Bagian Keuangan &amp; Akuntansi</div>
                    <div style="height: 65px;"></div>
                    <div class="fw-bold text-decoration-underline" style="font-size: 13px;">( Finance &amp; Accounting )</div>
                    <small class="text-muted" style="font-size: 10px;">PT. Refrigerasi Teknik Indonesia</small>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
