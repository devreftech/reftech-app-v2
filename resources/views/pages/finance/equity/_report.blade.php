{{-- Konten laporan Equity Statement, dipakai bareng oleh print.blade.php & detail.blade.php --}}

{{-- Letterhead --}}
<div class="text-center mb-4 pb-3" style="border-bottom: 2px solid #696cff;">
    <h4 class="fw-bold mb-1">PT. REFTECH JAYA OPTIMA</h4>
    <span class="badge bg-label-primary px-3 py-2 fs-6 rounded-pill mb-2">Perubahan Modal (Standar)</span>
    <p class="text-muted mb-0">Dari <strong>{{ $startString }}</strong> ke <strong>{{ $endString }}</strong></p>
</div>

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
                <td colspan="2" class="fw-medium"><span class="lvl-0">Ekuitas</span></td>
            </tr>
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
            <tr class="table-light">
                <td class="fw-bold"><span class="lvl-1">Jumlah Ekuitas</span></td>
                <td class="fw-bold border-top text-end">{{ number_format($totalekuitas ?? 0, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>
</div>
