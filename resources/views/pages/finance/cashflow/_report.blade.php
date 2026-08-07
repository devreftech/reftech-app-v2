{{-- Konten laporan Cashflow Statement, dipakai bareng oleh print.blade.php & detail.blade.php --}}

{{-- Letterhead --}}
<div class="text-center mb-4 pb-3" style="border-bottom: 2px solid #696cff;">
    <h4 class="fw-bold mb-1">PT. REFTECH JAYA OPTIMA</h4>
    <span class="badge bg-label-primary px-3 py-2 fs-6 rounded-pill mb-2">Arus Kas (Metode Langsung)</span>
    <p class="text-muted mb-0">Dari <strong>{{ $startString }}</strong> ke <strong>{{ $endString }}</strong></p>
</div>

@php
    $kasPembelian = -$expenseSum;
    $biayaLainLain = -$outcome;
    $labaOperasiSebelumPerubahan = $quotation + $income + $kasPembelian + $biayaLainLain + $labaRugiDisposal;
    $kasBersihInvestasi = $disposalProceeds - $assetPurchase;
    $kasBersihPendanaan = -$prive;
@endphp

<div class="table-responsive">
    <table class="table table-sm m-0" style="width: 100%">
        <thead>
            <tr class="table-light">
                <th>Description</th>
                <th class="text-end">Balance</th>
            </tr>
        </thead>
        <tbody class="text-dark">
            <tr>
                <td colspan="2"><span class="lvl-0">Arus Kas dari Aktivitas Operasi</span></td>
            </tr>
            <tr>
                <td><span class="lvl-1">Kas dari Penjualan</span></td>
                <td class="text-end">{{ number_format($quotation, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="fw-medium"><span class="lvl-2">Pendapatan Lain Lain</span></td>
                <td class="fw-medium text-end">{{ number_format($income, 0, ',', '.') }}</td>
            </tr>
            @foreach ($pendapatan as $item)
                <tr>
                    <td><span class="lvl-3">{{ $item->description }}</span></td>
                    <td class="text-end">{{ number_format($item->amount, 0, ',', '.') }}</td>
                </tr>
            @endforeach
            <tr>
                <td><span class="lvl-1">Kas Untuk Pembelian</span></td>
                <td class="text-danger text-end">{{ number_format($kasPembelian, 0, ',', '.') }}</td>
            </tr>
            @foreach ($expensePerAccount as $item)
                <tr>
                    <td><span class="lvl-3">{{ $item->name }}</span></td>
                    <td class="text-danger text-end">-{{ number_format($item->total_amount, 0, ',', '.') }}</td>
                </tr>
            @endforeach
            <tr>
                <td><span class="lvl-1">Biaya Lain Lain</span></td>
                <td class="text-danger text-end">{{ number_format($biayaLainLain, 0, ',', '.') }}</td>
            </tr>
            @foreach ($biaya as $item)
                <tr>
                    <td><span class="lvl-3">{{ $item->description }}</span></td>
                    <td class="text-danger text-end">-{{ number_format($item->amount, 0, ',', '.') }}</td>
                </tr>
            @endforeach
            <tr>
                <td><span class="lvl-1">Laba/Rugi Penghentian Aktiva Tetap</span></td>
                <td class="text-end {{ $labaRugiDisposal < 0 ? 'text-danger' : '' }}">{{ number_format($labaRugiDisposal, 0, ',', '.') }}</td>
            </tr>
            <tr class="bg-light">
                <td class="fw-medium"><span class="lvl-2">Laba(Rugi) Operasi sebelum berubah di Operasi Aktiva dan Kewajiban</span></td>
                <td class="fw-medium border-top text-end">{{ number_format($labaOperasiSebelumPerubahan, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td colspan="2"><span class="lvl-1">Berkurang(Bertambah) pada Operasi Aktiva</span></td>
            </tr>
            <tr>
                <td class="fw-medium"><span class="lvl-2">Persediaan Barang Dagang</span></td>
                <td class="fw-medium border-top text-end">TBA</td>
            </tr>
            <tr>
                <td colspan="2">
                    <span class="lvl-3 text-muted fst-italic" style="font-size: 11px;">
                        Menunggu histori mutasi stok (belum ada ledger nilai stok per tanggal)
                    </span>
                </td>
            </tr>
            <tr>
                <td><span class="lvl-3">Piutang Lain-lain IDR</span></td>
                <td class="text-end">{{ number_format($piutang, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td><span class="lvl-3">PPN Masukan</span></td>
                <td class="text-end">{{ number_format($ppnMas, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="fw-bolder"><span class="lvl-2">Akumulasi Penyusutan (periode ini)</span></td>
                <td class="text-end">{{ number_format($penyusutanPeriode, 0, ',', '.') }}</td>
            </tr>
            <tr class="bg-light">
                <td><span class="lvl-1">Jumlah Berkurang(Bertambah) pada Operasi Aktiva</span></td>
                <td class="text-danger border-top text-end">TBA</td>
            </tr>
            <tr>
                <td colspan="2"><span class="lvl-1">Bertambah (berkurang) pada Operasi Kewajiban</span></td>
            </tr>
            <tr>
                <td><span class="lvl-2">PPN Keluaran</span></td>
                <td class="border-top text-end">-{{ number_format($ppnKel, 0, ',', '.') }}</td>
            </tr>
            <tr class="bg-light">
                <td><span class="lvl-1">Jumlah Bertambah (berkurang) pada Operasi Kewajiban</span></td>
                <td class="text-danger border-top text-end">-{{ number_format($ppnKel, 0, ',', '.') }}</td>
            </tr>
            <tr class="table-light">
                <td class="fw-bold"><span class="lvl-1">Kas bersih (dipakai)/ dihasilkan oleh Aktivitas Operasi</span></td>
                <td class="fw-bold border-top text-end">TBA</td>
            </tr>
            <tr>
                <td colspan="2" class="pt-3"><span class="lvl-0">Arus Kas dari Aktivitas Investasi</span></td>
            </tr>
            <tr>
                <td><span class="lvl-1">Pembelian Aktiva Tetap</span></td>
                <td class="text-end {{ $assetPurchase > 0 ? 'text-danger' : '' }}">-{{ number_format($assetPurchase, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td><span class="lvl-1">Penerimaan dari Penjualan / Disposal Aktiva Tetap</span></td>
                <td class="text-end">{{ number_format($disposalProceeds, 0, ',', '.') }}</td>
            </tr>
            <tr class="table-light">
                <td class="fw-bold"><span class="lvl-2">Kas bersih yg dihasilkan / (dipakai) oleh Aktivitas Investasi</span></td>
                <td class="fw-bold border-top text-end">{{ number_format($kasBersihInvestasi, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td colspan="2" class="pt-3"><span class="lvl-0">Arus Kas dari Aktivitas Pendanaan</span></td>
            </tr>
            <tr>
                <td colspan="2"><span class="lvl-1">Ekuitas</span></td>
            </tr>
            <tr>
                <td><span class="lvl-2">Dividen</span></td>
                <td class="text-danger text-end">- {{ number_format($prive, 0, ',', '.') }}</td>
            </tr>
            <tr class="table-light">
                <td class="fw-bold"><span class="lvl-2">Kas bersih yg dihasilkan dari / (dipakai) oleh Aktivitas Pendanaan</span></td>
                <td class="fw-bold border-top text-end {{ $kasBersihPendanaan < 0 ? 'text-danger' : '' }}">{{ number_format($kasBersihPendanaan, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="fw-medium"><span class="lvl-1">Kas bersih dihasilkan oleh / (dipakai) di Period ini</span></td>
                <td class="fw-medium border-top text-end">TBA</td>
            </tr>
            <tr>
                <td class="fw-medium"><span class="lvl-1">Kas &amp; Setara Kas pada Awal Periode</span></td>
                <td class="fw-medium border-top text-end">TBA</td>
            </tr>
            <tr>
                <td class="fw-medium"><span class="lvl-1">Kas &amp; Setara Kas pada Akhir Periode</span></td>
                <td class="fw-medium border-top text-end">TBA</td>
            </tr>
        </tbody>
    </table>
</div>

<p class="text-muted small fst-italic mt-2 mb-0" style="font-size: 11px;">
    Baris "TBA" di atas menunggu tabel mutasi kas (ledger bank) — saat ini sistem cuma
    menyimpan saldo bank berjalan, belum ada saldo historis per tanggal.
</p>
