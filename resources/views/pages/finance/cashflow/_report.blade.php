{{-- Konten Laporan Cashflow Statement (Arus Kas - Metode Langsung) --}}
{{-- Dipakai bersama oleh detail.blade.php & print.blade.php --}}

@php
    $isPrint = $isPrintMode ?? false;
    $periodYear = $year ?? \Carbon\Carbon::parse($startDate)->year;
    
    // Perhitungan Aktivitas Operasi
    $kasMasukOperasi = ($quotation ?? 0) + ($income ?? 0);
    $kasKeluarOperasi = ($expenseSum ?? 0) + ($outcome ?? 0);
    $labaRugiDisp = $labaRugiDisposal ?? 0;
    $netOperasiKas = $kasMasukOperasi - $kasKeluarOperasi;
    $netOperasiDenganDisposal = $netOperasiKas + $labaRugiDisp;
    
    // Perhitungan Aktivitas Investasi
    $proceeds = $disposalProceeds ?? 0;
    $purchase = $assetPurchase ?? 0;
    $kasBersihInvestasi = $proceeds - $purchase;
    
    // Perhitungan Aktivitas Pendanaan
    $priveVal = $prive ?? 0;
    $kasBersihPendanaan = -$priveVal;
    
    // Total Kenaikan/Penurunan Kas Bersih Periode Berjalan
    $totalNetPerubahanKas = $netOperasiKas + $kasBersihInvestasi + $kasBersihPendanaan;
@endphp

<div class="cashflow-report-document {{ $isPrint ? 'p-0' : 'p-2' }}">
    {{-- Letterhead / Kop Laporan Resmi --}}
    <div class="report-header text-center mb-4 pb-3" style="border-bottom: 2px solid #2b3445;">
        <div class="d-flex justify-content-between align-items-center mb-1">
            <div class="text-start">
                <span class="text-uppercase fw-bold tracking-wider text-muted small" style="font-size: 11px; letter-spacing: 1px;">Dokumen Keuangan Resmi</span>
            </div>
            <div class="text-end">
                <span class="badge {{ $isPrint ? 'border text-dark' : 'bg-label-primary' }} px-2 py-1 small" style="font-size: 10px;">
                    PSAK No. 2 (Metode Langsung)
                </span>
            </div>
        </div>

        <h3 class="fw-bolder mb-1 text-dark text-uppercase tracking-tight" style="font-weight: 800; letter-spacing: -0.5px;">
            PT. REFTECH JAYA OPTIMA
        </h3>
        <h5 class="fw-bold text-primary text-uppercase mb-1" style="font-weight: 700; letter-spacing: 0.5px;">
            LAPORAN ARUS KAS (CASH FLOW STATEMENT)
        </h5>
        <div class="d-flex align-items-center justify-content-center gap-2 text-muted small">
            <span><i class="mdi mdi-calendar-range me-1"></i>Periode: <strong>{{ $startString }}</strong> s/d <strong>{{ $endString }}</strong></span>
            <span>&bull;</span>
            <span>Mata Uang: <strong>Rupiah (IDR)</strong></span>
        </div>
    </div>

    {{-- Tabel Laporan Arus Kas --}}
    <div class="table-responsive mb-4">
        <table class="table table-sm table-borderless align-middle mb-0" style="font-size: 13px; width: 100%;">
            <thead>
                <tr style="border-bottom: 2px solid #2b3445; background-color: #f8fafc;">
                    <th class="py-2 text-uppercase fw-bolder text-dark" style="width: 70%; letter-spacing: 0.5px;">Keterangan / Arus Transaksi Kas</th>
                    <th class="py-2 text-end text-uppercase fw-bolder text-dark" style="width: 30%; letter-spacing: 0.5px;">Nominal (IDR)</th>
                </tr>
            </thead>
            <tbody>
                {{-- ========================================== --}}
                {{-- I. ARUS KAS DARI AKTIVITAS OPERASI --}}
                {{-- ========================================== --}}
                <tr class="table-section-head">
                    <td colspan="2" class="pt-3 pb-1">
                        <span class="fw-bolder text-uppercase text-dark d-flex align-items-center gap-1" style="font-size: 13.5px;">
                            <i class="mdi mdi-cash-register text-primary"></i> I. ARUS KAS DARI AKTIVITAS OPERASI
                        </span>
                    </td>
                </tr>

                {{-- Penerimaan Kas Operasi --}}
                <tr class="sub-head">
                    <td colspan="2" class="ps-3 pt-2 text-primary fw-bold" style="font-size: 12.5px;">
                        Penerimaan Kas Operasional:
                    </td>
                </tr>
                <tr>
                    <td class="ps-4">
                        <span class="fw-medium text-dark">&bull; Penerimaan Kas dari Pelanggan (Penjualan / PO Selesai)</span>
                    </td>
                    <td class="text-end fw-semibold text-success">
                        {{ number_format($quotation, 0, ',', '.') }}
                    </td>
                </tr>
                <tr>
                    <td class="ps-4">
                        <span class="fw-medium text-dark">&bull; Pendapatan Operasional Lain-lain</span>
                    </td>
                    <td class="text-end fw-semibold text-success">
                        {{ number_format($income, 0, ',', '.') }}
                    </td>
                </tr>
                @if(isset($pendapatan) && count($pendapatan) > 0)
                    @foreach ($pendapatan as $item)
                        <tr class="item-detail">
                            <td class="ps-5 text-muted small" style="font-size: 11.5px;">
                                &ndash; {{ $item->description ?? 'Pendapatan Lain' }}
                            </td>
                            <td class="text-end text-muted small" style="font-size: 11.5px;">
                                {{ number_format($item->amount, 0, ',', '.') }}
                            </td>
                        </tr>
                    @endforeach
                @endif
                <tr style="border-top: 1px dashed #cbd5e1;">
                    <td class="ps-3 fw-bold text-dark">
                        Total Penerimaan Kas Operasi (A)
                    </td>
                    <td class="text-end fw-bold text-success">
                        Rp {{ number_format($kasMasukOperasi, 0, ',', '.') }}
                    </td>
                </tr>

                {{-- Pengeluaran Kas Operasi --}}
                <tr class="sub-head">
                    <td colspan="2" class="ps-3 pt-3 text-danger fw-bold" style="font-size: 12.5px;">
                        Pengeluaran Kas Operasional:
                    </td>
                </tr>
                <tr>
                    <td class="ps-4">
                        <span class="fw-medium text-dark">&bull; Kas untuk Pembelian Barang &amp; Beban Operasional Usaha</span>
                    </td>
                    <td class="text-end fw-semibold text-danger">
                        ({{ number_format($expenseSum, 0, ',', '.') }})
                    </td>
                </tr>
                @if(isset($expensePerAccount) && count($expensePerAccount) > 0)
                    @foreach ($expensePerAccount as $item)
                        <tr class="item-detail">
                            <td class="ps-5 text-muted small" style="font-size: 11.5px;">
                                &ndash; {{ $item->name ?? 'Beban Akun' }}
                            </td>
                            <td class="text-end text-muted small" style="font-size: 11.5px;">
                                ({{ number_format($item->total_amount, 0, ',', '.') }})
                            </td>
                        </tr>
                    @endforeach
                @endif
                <tr>
                    <td class="ps-4">
                        <span class="fw-medium text-dark">&bull; Beban / Biaya Operasional Lain-lain</span>
                    </td>
                    <td class="text-end fw-semibold text-danger">
                        ({{ number_format($outcome, 0, ',', '.') }})
                    </td>
                </tr>
                @if(isset($biaya) && count($biaya) > 0)
                    @foreach ($biaya as $item)
                        <tr class="item-detail">
                            <td class="ps-5 text-muted small" style="font-size: 11.5px;">
                                &ndash; {{ $item->description ?? 'Biaya Lain' }}
                            </td>
                            <td class="text-end text-muted small" style="font-size: 11.5px;">
                                ({{ number_format($item->amount, 0, ',', '.') }})
                            </td>
                        </tr>
                    @endforeach
                @endif
                <tr style="border-top: 1px dashed #cbd5e1;">
                    <td class="ps-3 fw-bold text-dark">
                        Total Pengeluaran Kas Operasi (B)
                    </td>
                    <td class="text-end fw-bold text-danger">
                        (Rp {{ number_format($kasKeluarOperasi, 0, ',', '.') }})
                    </td>
                </tr>

                {{-- Penyesuaian Disposal & Modal Kerja --}}
                @if($labaRugiDisp != 0)
                <tr>
                    <td class="ps-3 pt-2">
                        <span class="fw-medium text-dark">&bull; Laba / (Rugi) Pelepasan Aktiva Tetap (Disposal)</span>
                    </td>
                    <td class="text-end fw-semibold {{ $labaRugiDisp < 0 ? 'text-danger' : 'text-success' }}">
                        {{ $labaRugiDisp < 0 ? '(' . number_format(abs($labaRugiDisp), 0, ',', '.') . ')' : number_format($labaRugiDisp, 0, ',', '.') }}
                    </td>
                </tr>
                @endif

                {{-- Penyesuaian Modal Kerja Operasi (Working Capital) --}}
                <tr class="sub-head">
                    <td colspan="2" class="ps-3 pt-2 text-muted fw-bold small" style="font-size: 11.5px;">
                        Posisi Terkait Operasional &amp; Pajak:
                    </td>
                </tr>
                <tr>
                    <td class="ps-4 text-muted small">
                        &bull; Piutang Penjualan Tempo Berjalan
                    </td>
                    <td class="text-end text-muted small">
                        {{ number_format($piutang, 0, ',', '.') }}
                    </td>
                </tr>
                <tr>
                    <td class="ps-4 text-muted small">
                        &bull; PPN Masukan Berjalan
                    </td>
                    <td class="text-end text-muted small">
                        {{ number_format($ppnMas, 0, ',', '.') }}
                    </td>
                </tr>
                <tr>
                    <td class="ps-4 text-muted small">
                        &bull; PPN Keluaran Berjalan
                    </td>
                    <td class="text-end text-muted small">
                        ({{ number_format($ppnKel, 0, ',', '.') }})
                    </td>
                </tr>
                <tr>
                    <td class="ps-4 text-muted small">
                        &bull; Beban Penyusutan Aktiva Tetap (Non-Kas Add-back)
                    </td>
                    <td class="text-end text-muted small">
                        +{{ number_format($penyusutanPeriode ?? 0, 0, ',', '.') }}
                    </td>
                </tr>

                {{-- Subtotal Kas Bersih Operasi --}}
                <tr style="border-top: 1.5px solid #2b3445; border-bottom: 1.5px solid #2b3445; background-color: #f1f5f9;">
                    <td class="py-2 ps-3 fw-bold text-dark">
                        ARUS KAS BERSIH DARI AKTIVITAS OPERASI (OCF)
                    </td>
                    <td class="py-2 text-end fw-bolder fs-6 {{ $netOperasiKas >= 0 ? 'text-primary' : 'text-danger' }}">
                        {{ $netOperasiKas < 0 ? '(Rp ' . number_format(abs($netOperasiKas), 0, ',', '.') . ')' : 'Rp ' . number_format($netOperasiKas, 0, ',', '.') }}
                    </td>
                </tr>

                {{-- ========================================== --}}
                {{-- II. ARUS KAS DARI AKTIVITAS INVESTASI --}}
                {{-- ========================================== --}}
                <tr class="table-section-head">
                    <td colspan="2" class="pt-4 pb-1">
                        <span class="fw-bolder text-uppercase text-dark d-flex align-items-center gap-1" style="font-size: 13.5px;">
                            <i class="mdi mdi-domain text-info"></i> II. ARUS KAS DARI AKTIVITAS INVESTASI
                        </span>
                    </td>
                </tr>
                <tr>
                    <td class="ps-4">
                        <span class="fw-medium text-dark">&bull; Penerimaan Kas dari Penjualan / Pelepasan Aktiva Tetap</span>
                    </td>
                    <td class="text-end fw-semibold {{ $proceeds > 0 ? 'text-success' : 'text-muted' }}">
                        {{ number_format($proceeds, 0, ',', '.') }}
                    </td>
                </tr>
                <tr>
                    <td class="ps-4">
                        <span class="fw-medium text-dark">&bull; Pengeluaran Kas untuk Perolehan / Pembelian Aktiva Tetap</span>
                    </td>
                    <td class="text-end fw-semibold {{ $purchase > 0 ? 'text-danger' : 'text-muted' }}">
                        {{ $purchase > 0 ? '(' . number_format($purchase, 0, ',', '.') . ')' : '0' }}
                    </td>
                </tr>

                {{-- Subtotal Kas Bersih Investasi --}}
                <tr style="border-top: 1.5px solid #2b3445; border-bottom: 1.5px solid #2b3445; background-color: #f1f5f9;">
                    <td class="py-2 ps-3 fw-bold text-dark">
                        ARUS KAS BERSIH DARI / (DIGUNAKAN UNTUK) AKTIVITAS INVESTASI
                    </td>
                    <td class="py-2 text-end fw-bolder fs-6 {{ $kasBersihInvestasi >= 0 ? 'text-dark' : 'text-danger' }}">
                        {{ $kasBersihInvestasi < 0 ? '(Rp ' . number_format(abs($kasBersihInvestasi), 0, ',', '.') . ')' : 'Rp ' . number_format($kasBersihInvestasi, 0, ',', '.') }}
                    </td>
                </tr>

                {{-- ========================================== --}}
                {{-- III. ARUS KAS DARI AKTIVITAS PENDANAAN --}}
                {{-- ========================================== --}}
                <tr class="table-section-head">
                    <td colspan="2" class="pt-4 pb-1">
                        <span class="fw-bolder text-uppercase text-dark d-flex align-items-center gap-1" style="font-size: 13.5px;">
                            <i class="mdi mdi-cash-refund text-warning"></i> III. ARUS KAS DARI AKTIVITAS PENDANAAN
                        </span>
                    </td>
                </tr>
                <tr>
                    <td class="ps-4">
                        <span class="fw-medium text-dark">&bull; Penarikan Modal Pemilik (Prive / Dividen)</span>
                    </td>
                    <td class="text-end fw-semibold {{ $priveVal > 0 ? 'text-danger' : 'text-muted' }}">
                        {{ $priveVal > 0 ? '(' . number_format($priveVal, 0, ',', '.') . ')' : '0' }}
                    </td>
                </tr>

                {{-- Subtotal Kas Bersih Pendanaan --}}
                <tr style="border-top: 1.5px solid #2b3445; border-bottom: 1.5px solid #2b3445; background-color: #f1f5f9;">
                    <td class="py-2 ps-3 fw-bold text-dark">
                        ARUS KAS BERSIH DARI / (DIGUNAKAN UNTUK) AKTIVITAS PENDANAAN
                    </td>
                    <td class="py-2 text-end fw-bolder fs-6 {{ $kasBersihPendanaan >= 0 ? 'text-dark' : 'text-danger' }}">
                        {{ $kasBersihPendanaan < 0 ? '(Rp ' . number_format(abs($kasBersihPendanaan), 0, ',', '.') . ')' : 'Rp ' . number_format($kasBersihPendanaan, 0, ',', '.') }}
                    </td>
                </tr>

                {{-- ========================================== --}}
                {{-- IV. TOTAL KENAIKAN / PENURUNAN KAS BERSIH --}}
                {{-- ========================================== --}}
                <tr>
                    <td colspan="2" class="pt-3"></td>
                </tr>
                <tr style="border-top: 2px solid #2b3445; border-bottom: 3px double #2b3445; background-color: #e2e8f0;">
                    <td class="py-3 ps-3 fw-bolder text-dark fs-6 text-uppercase">
                        KENAIKAN / (PENURUNAN) BERSIH KAS &amp; SETARA KAS (I + II + III)
                    </td>
                    <td class="py-3 text-end fw-bolder fs-5 {{ $totalNetPerubahanKas >= 0 ? 'text-primary' : 'text-danger' }}">
                        {{ $totalNetPerubahanKas < 0 ? '(Rp ' . number_format(abs($totalNetPerubahanKas), 0, ',', '.') . ')' : 'Rp ' . number_format($totalNetPerubahanKas, 0, ',', '.') }}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- Catatan Rekonsiliasi Saldo Kas (Info Box) --}}
    <div class="p-3 mb-4 rounded-3 border {{ $isPrint ? 'bg-white' : 'bg-light' }}" style="font-size: 11.5px;">
        <div class="fw-bold text-dark mb-1 d-flex align-items-center gap-1">
            <i class="mdi mdi-information-outline text-info"></i> Catatan Pelaporan Keuangan (Notes to Cash Flow):
        </div>
        <ul class="text-muted mb-0 ps-3">
            <li>Laporan arus kas disusun menggunakan <strong>Metode Langsung (Direct Method)</strong> sesuai PSAK No. 2 yang mengklasifikasikan arus kas ke dalam aktivitas operasi, investasi, dan pendanaan.</li>
            <li>Arus kas masuk operasi mencakup seluruh penerimaan penjualan PO yang telah divalidasi dan pendapatan operasional lainnya.</li>
            <li>Arus kas keluar operasi mencakup pengeluaran kas aktual untuk pengadaan barang, operasional, dan beban administrasi.</li>
            <li>Saldo kas historis pembuka/penutup periode berjalan akan otomatis disinkronkan secara kontinu melalui buku kas bank berjalan (Bank Ledger).</li>
        </ul>
    </div>

    {{-- 3 Official Signature Blocks --}}
    <div class="signature-section mt-5 pt-3" style="page-break-inside: avoid;">
        <div class="row text-center">
            <div class="col-4">
                <p class="text-muted small mb-1">Dibuat Oleh,</p>
                <div class="signature-space" style="height: 70px;"></div>
                <div class="fw-bold text-dark text-decoration-underline text-uppercase" style="font-size: 12.5px;">
                    Staff Finance &amp; Accounting
                </div>
                <span class="text-muted small" style="font-size: 10.5px;">Bagian Keuangan</span>
            </div>
            <div class="col-4">
                <p class="text-muted small mb-1">Diperiksa Oleh,</p>
                <div class="signature-space" style="height: 70px;"></div>
                <div class="fw-bold text-dark text-decoration-underline text-uppercase" style="font-size: 12.5px;">
                    Finance Supervisor
                </div>
                <span class="text-muted small" style="font-size: 10.5px;">Pemeriksa Laporan</span>
            </div>
            <div class="col-4">
                <p class="text-muted small mb-1">Disetujui Oleh,</p>
                <div class="signature-space" style="height: 70px;"></div>
                <div class="fw-bold text-dark text-decoration-underline text-uppercase" style="font-size: 12.5px;">
                    Direktur Utama
                </div>
                <span class="text-muted small" style="font-size: 10.5px;">PT. Reftech Jaya Optima</span>
            </div>
        </div>
        <div class="text-center mt-3 text-muted small" style="font-size: 10px;">
            Dicetak secara sistem ERP pada: {{ now()->translatedFormat('l, d F Y - H:i') }} WIB
        </div>
    </div>
</div>
