{{-- Konten laporan Balance Statement, dipakai bareng oleh print.blade.php & detail.blade.php --}}

{{-- Letterhead --}}
<div class="text-center mb-4 pb-3" style="border-bottom: 2px solid #696cff;">
    <h4 class="fw-bold mb-1">PT. REFTECH JAYA OPTIMA</h4>
    <span class="badge bg-label-primary px-3 py-2 fs-6 rounded-pill mb-2">Neraca (Standar)</span>
    <p class="text-muted mb-0">Dari <strong>{{ $startString }}</strong> ke <strong>{{ $endString }}</strong></p>
</div>

@php
    $totalLancar = $piutang + $asset + $ppnMas;
    $totalTetap = $totalFixed - $grandTotalPenyusutan;
    $totalAktiva = $totalLancar + $totalTetap;
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
                <td colspan="2"><span class="lvl-0">Aktiva</span></td>
            </tr>
            <tr>
                <td colspan="2"><span class="lvl-1">Aktiva Lancar</span></td>
            </tr>
            @php
                $capPalembang = 425000000;
                $modPalembang = 575000000;
            @endphp
            <tr>
                <td><span class="lvl-2">Bank</span></td>
                <td class="fw-medium text-end">{{ number_format($bank->saldo, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td><span class="lvl-3">BCA IDR</span></td>
                <td class="text-end">{{ number_format($bank->saldo, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td><span class="lvl-3">Capital Cabang Palembang</span></td>
                <td class="text-end">{{ number_format($capPalembang, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td><span class="lvl-3">Modal Cabang Palembang</span></td>
                <td class="text-end">{{ number_format($modPalembang, 0, ',', '.') }}</td>
            </tr>
            <tr class="bg-light">
                <td class="fw-medium"><span class="lvl-2">Jumlah Kas dan Bank</span></td>
                <td class="fw-medium border-top text-end">{{ number_format($bank->saldo + $capPalembang + $modPalembang, 0, ',', '.') }}</td>
            </tr>

            <tr>
                <td colspan="2"><span class="lvl-1">Piutang Dagang</span></td>
            </tr>
            <tr>
                <td class="fw-medium"><span class="lvl-2">Piutang Usaha</span></td>
                <td class="fw-medium text-end">{{ number_format($piutang, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td><span class="lvl-3">Piutang Usaha</span></td>
                <td class="text-end">{{ number_format($piutang, 0, ',', '.') }}</td>
            </tr>
            <tr class="bg-light">
                <td class="fw-medium"><span class="lvl-2">Jumlah Piutang Dagang</span></td>
                <td class="fw-medium border-top text-end">{{ number_format($piutang, 0, ',', '.') }}</td>
            </tr>

            <tr>
                <td colspan="2"><span class="lvl-1">Persediaan</span></td>
            </tr>
            <tr>
                <td class="fw-medium"><span class="lvl-2">Persediaan Barang Dagang</span></td>
                <td class="fw-medium text-end">{{ number_format($asset, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td><span class="lvl-3">Persediaan Barang Dagang</span></td>
                <td class="text-end">{{ number_format($asset, 0, ',', '.') }}</td>
            </tr>
            <tr class="bg-light">
                <td class="fw-medium"><span class="lvl-2">Jumlah Persediaan</span></td>
                <td class="fw-medium border-top text-end">{{ number_format($asset, 0, ',', '.') }}</td>
            </tr>

            <tr>
                <td colspan="2"><span class="lvl-1">Aktiva Lancar Lainnya</span></td>
            </tr>
            <tr>
                <td class="fw-medium"><span class="lvl-2">PPN Masukan</span></td>
                <td class="fw-medium text-end">{{ number_format($ppnMas, 0, ',', '.') }}</td>
            </tr>
            <tr class="bg-light">
                <td class="fw-medium"><span class="lvl-2">Jumlah Aktiva Lancar Lainnya</span></td>
                <td class="fw-medium border-top text-end">{{ number_format($ppnMas, 0, ',', '.') }}</td>
            </tr>
            <tr class="bg-light">
                <td class="fw-medium"><span class="lvl-2">Jumlah Aktiva Lancar</span></td>
                <td class="fw-medium border-top text-end">{{ number_format($totalLancar, 0, ',', '.') }}</td>
            </tr>

            <tr>
                <td colspan="2"><span class="lvl-0">Aktiva Tetap</span></td>
            </tr>
            <tr>
                <td colspan="2"><span class="lvl-1">Nilai Histori</span></td>
            </tr>
            <tr>
                <td class="fw-medium"><span class="lvl-2">Aset Tetap</span></td>
                <td class="fw-medium border-top text-end">{{ number_format($totalFixed, 0, ',', '.') }}</td>
            </tr>
            @foreach ($fixedAsset as $item)
                <tr>
                    <td><span class="lvl-3">{{ $item->type }}</span></td>
                    <td class="text-end">{{ number_format($item->total_amount, 0, ',', '.') }}</td>
                </tr>
            @endforeach
            <tr class="bg-light">
                <td class="fw-medium"><span class="lvl-2">Jumlah Nilai Histori</span></td>
                <td class="fw-medium border-top text-end">{{ number_format($totalFixed ?? 0, 0, ',', '.') }}</td>
            </tr>

            <tr>
                <td colspan="2"><span class="lvl-1">Akumulasi Penyusutan</span></td>
            </tr>
            <tr>
                <td class="fw-medium"><span class="lvl-2">Akumulasi Penyusutan</span></td>
                <td class="fw-medium border-top text-end">{{ number_format($grandTotalPenyusutan, 0, ',', '.') }}</td>
            </tr>
            @foreach ($penyusutan as $item)
                <tr>
                    <td><span class="lvl-3">Akum. Penys. {{ $item['type'] }}</span></td>
                    <td class="text-danger text-end"> - {{ number_format($item['total_penyusutan'], 0, ',', '.') }}</td>
                </tr>
            @endforeach
            <tr class="bg-light">
                <td class="fw-medium"><span class="lvl-2">Jumlah Akumulasi Penyusutan</span></td>
                <td class="text-danger border-top text-end">{{ number_format($grandTotalPenyusutan ?? 0, 0, ',', '.') }}</td>
            </tr>
            <tr class="bg-light">
                <td class="fw-medium"><span class="lvl-2">Jumlah Aktiva Tetap</span></td>
                <td class="fw-medium border-top text-end">{{ number_format($totalTetap ?? 0, 0, ',', '.') }}</td>
            </tr>

            <tr>
                <td colspan="2"><span class="lvl-1">OTHER ASSETS</span></td>
            </tr>
            <tr class="bg-light">
                <td class="fw-medium"><span class="lvl-2">Jumlah OTHER ASSETS</span></td>
                <td class="fw-medium border-top text-end">{{ number_format(0, 0, ',', '.') }}</td>
            </tr>
            <tr class="table-light">
                <td class="fw-bold"><span class="lvl-1">Jumlah Aktiva</span></td>
                <td class="fw-bold border-top text-end">{{ number_format($totalAktiva, 0, ',', '.') }}</td>
            </tr>

            <tr>
                <td colspan="2" class="pt-3"><span class="lvl-0">Kewajiban dan Ekuitas</span></td>
            </tr>
            <tr>
                <td colspan="2"><span class="lvl-1">Kewajiban</span></td>
            </tr>
            <tr>
                <td colspan="2"><span class="lvl-1">Kewajiban Lancar</span></td>
            </tr>
            <tr>
                <td colspan="2"><span class="lvl-1">Hutang Dagang</span></td>
            </tr>
            <tr class="bg-light">
                <td class="fw-medium"><span class="lvl-2">Jumlah Hutang Dagang</span></td>
                <td class="fw-medium border-top text-end">{{ number_format(0, 0, ',', '.') }}</td>
            </tr>

            <tr>
                <td colspan="2" class="fw-medium"><span class="lvl-1">Kewajiban Lancar Lain</span></td>
            </tr>
            <tr>
                <td><span class="lvl-3">PPN Keluaran</span></td>
                <td class="text-end">{{ number_format($ppnKel, 0, ',', '.') }}</td>
            </tr>
            <tr class="bg-light">
                <td class="fw-medium"><span class="lvl-2">Jumlah Kewajiban Lancar Lain</span></td>
                <td class="fw-medium border-top text-end">{{ number_format($ppnKel ?? 0, 0, ',', '.') }}</td>
            </tr>
            <tr class="bg-light">
                <td class="fw-medium"><span class="lvl-2">Jumlah Kewajiban Lancar</span></td>
                <td class="fw-medium border-top text-end">{{ number_format($ppnKel ?? 0, 0, ',', '.') }}</td>
            </tr>

            <tr>
                <td colspan="2" class="fw-medium"><span class="lvl-1">Kewajiban Jangka Panjang</span></td>
            </tr>
            <tr class="bg-light">
                <td class="fw-medium"><span class="lvl-2">Jumlah Kewajiban Jangka Panjang</span></td>
                <td class="fw-medium border-top text-end">{{ number_format(0, 0, ',', '.') }}</td>
            </tr>
            <tr class="bg-light">
                <td class="fw-medium"><span class="lvl-2">Jumlah Kewajiban</span></td>
                <td class="fw-medium border-top text-end">{{ number_format($ppnKel ?? 0, 0, ',', '.') }}</td>
            </tr>

            <tr>
                <td colspan="2" class="fw-medium"><span class="lvl-1">Ekuitas</span></td>
            </tr>
            @php
                if (@$month) {
                    $ekuitas = 250000000 + $labaTahunTahun - $prive - $labaBulanIni;
                    $totalekuitas = $ekuitas + $labaBulanIni;
                    $sebelumnya = $labaTahunTahun - $labaBulanIni;
                } else {
                    $ekuitas = 250000000 + $labaTahunTahun - $prive - $labaTahunIni;
                    $totalekuitas = $ekuitas + $labaTahunIni;
                    $sebelumnya = $labaTahunTahun - $labaTahunIni;
                }
                $ekujiban = $totalekuitas + $ppnKel;
            @endphp
            <tr>
                <td class="fw-medium"><span class="lvl-2">Ekuitas</span></td>
                <td class="fw-medium text-end">{{ number_format($ekuitas, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td><span class="lvl-3">Modal</span></td>
                <td class="text-end">{{ number_format(250000000, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td><span class="lvl-3">Laba Ditahan</span></td>
                <td class="text-end">{{ number_format($labaTahunLalu, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td><span class="lvl-3">Prive</span></td>
                <td class="text-danger text-end">- {{ number_format($prive, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td><span class="lvl-3">Laba Tahun Sebelumnya</span></td>
                <td class="text-end">{{ number_format($sebelumnya, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td><span class="lvl-3">OPENING BALANCE EQUITY</span></td>
                <td class="text-end">{{ number_format(0, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td><span class="lvl-3">Laba {{ @$month ? 'Bulan' : 'Tahun' }} Ini</span></td>
                <td class="text-end">{{ number_format(@$month ? $labaBulanIni : $labaTahunIni, 0, ',', '.') }}</td>
            </tr>
            <tr class="bg-light">
                <td class="fw-medium"><span class="lvl-2">Jumlah Ekuitas</span></td>
                <td class="fw-medium border-top text-end">{{ number_format($totalekuitas ?? 0, 0, ',', '.') }}</td>
            </tr>
            <tr class="table-light">
                <td class="fw-bold"><span class="lvl-1">Jumlah Ekuitas Dan Kewajiban</span></td>
                <td class="fw-bold border-top text-end">{{ number_format($ekujiban ?? 0, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>
</div>

{{-- Balance check --}}
@php
    $isBalanced = round($totalAktiva) === round($ekujiban ?? 0);
@endphp
<div class="mt-4 p-3 rounded-3 d-flex justify-content-between align-items-center"
    style="background: {{ $isBalanced ? 'linear-gradient(135deg, #e8f9ee 0%, #ddf5e6 100%)' : 'linear-gradient(135deg, #fdecea 0%, #fbdedb 100%)' }}; border: 1px dashed {{ $isBalanced ? '#28a745' : '#dc3545' }};">
    <div>
        <div class="fw-bold" style="color: {{ $isBalanced ? '#1e7e34' : '#a71d2a' }};">
            <i class="mdi {{ $isBalanced ? 'mdi-check-circle-outline' : 'mdi-alert-circle-outline' }} me-1"></i>
            {{ $isBalanced ? 'Balance Sheet Seimbang' : 'Balance Sheet Tidak Seimbang' }}
        </div>
        <div class="text-muted small">Jumlah Aktiva vs Jumlah Ekuitas dan Kewajiban</div>
    </div>
    <div class="text-end">
        <div class="small text-muted">Selisih</div>
        <div class="fw-bold fs-5" style="color: {{ $isBalanced ? '#1e7e34' : '#a71d2a' }};">
            {{ number_format(($totalAktiva) - ($ekujiban ?? 0), 0, ',', '.') }}
        </div>
    </div>
</div>
