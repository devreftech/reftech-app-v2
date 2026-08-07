@extends('layouts.sales.app')
@section('title', 'Print Opname')
<div class="invoice-print p-4">
    <div class="container-fluid flex-grow-1 container-p-y">
        <div class="text-center">
            <h3>PT. REFTECH JAYA OPTIMA</h3>
            <h2 class="text-danger">Neraca (Standar)</h2>
            <h4>Dari {{ $startString }} ke {{ $endString }}</h4>
        </div>
        <hr>
        <div class="mb-2">
            @php
                $totalLancar = $piutang + $asset + $ppnMas;
                $totalTetap = $totalFixed - $grandTotalPenyusutan;
                $totalAktiva = $totalLancar + $totalTetap;
            @endphp
            <table class="table table-borderless m-0" style="width: 100%">
                <thead class="table-light border-top">
                    <tr>
                        <th>Description</th>
                        <th>Balance</th>
                    </tr>
                </thead>
                <tbody class="text-dark">
                    <tr>
                        <td colspan="2">
                            <span class="lvl-0">
                                Aktiva
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <span class="lvl-1">
                                Aktiva Lancar
                            </span>
                        </td>
                    </tr>
                    @php
                        $capPalembang = 425000000;
                        $modPalembang = 575000000;
                    @endphp
                    <tr>
                        <td>
                            <span class="lvl-2">
                                Bank
                            </span>
                        </td>
                        <td class="fw-medium">{{ number_format($bank->saldo, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td>
                            <span class="lvl-3">
                                BCA IDR
                            </span>
                        </td>
                        <td>{{ number_format($bank->saldo, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td>
                            <span class="lvl-3">
                                Capital Cabang Palembang
                            </span>
                        </td>
                        <td>{{ number_format($capPalembang, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td>
                            <span class="lvl-3">
                                Modal Cabang Palembang
                            </span>
                        </td>
                        <td>{{ number_format($modPalembang, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td class="fw-medium">
                            <span class="lvl-2">
                                Jumlah Kas dan Bank
                            </span>
                        </td>
                        <td class="fw-medium border-top">{{ number_format($bank->saldo + $capPalembang + $modPalembang, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <span class="lvl-1">
                                Piutang Dagang
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td class="fw-medium">
                            <span class="lvl-2">
                                Piutang Usaha
                            </span>
                        </td>
                        <td class="fw-medium">{{ number_format($piutang, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td>
                            <span class="lvl-3">
                                Piutang Usaha
                            </span>
                        </td>
                        <td>{{ number_format($piutang, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td class="fw-medium">
                            <span class="lvl-2">
                                Jumlah Piutang Dagang
                            </span>
                        </td>
                        <td class="fw-medium border-top">{{ number_format($piutang, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <span class="lvl-1">
                                Persediaan
                            </span>

                        </td>
                    </tr>
                    <tr>
                        <td class="fw-medium">
                            <span class="lvl-2">
                                Persediaan Barang Dagang
                            </span>
                        </td>
                        <td class="fw-medium">{{ number_format($asset, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td>
                            <span class="lvl-3">
                                Persediaan Barang Dagang
                            </span>
                        </td>
                        <td>{{ number_format($asset, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td class="fw-medium">
                            <span class="lvl-2">
                                Jumlah Persediaan
                            </span>
                        </td>
                        <td class="fw-medium border-top">{{ number_format($asset, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <span class="lvl-1">
                                Aktiva Lancar Lainnya
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td class="fw-medium">
                            <span class="lvl-2">
                                PPN Masukan
                            </span>
                        </td>
                        <td class="fw-medium">{{ number_format($ppnMas, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td class="fw-medium">
                            <span class="lvl-2">
                                Jumlah Aktiva Lancar Lainnya
                            </span>
                        </td>
                        <td class="fw-medium border-top">{{ number_format($ppnMas, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td class="fw-medium">
                            <span class="lvl-2">
                                Jumlah Aktiva Lancar
                            </span>
                        </td>
                        <td class="fw-medium border-top">{{ number_format($totalLancar, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <span class="lvl-0">
                                Aktiva Tetap
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <span class="lvl-1">
                                Nilai Histori
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td class="fw-medium">
                            <span class="lvl-2">
                                Aset Tetap
                            </span>
                        </td>
                        <td class="fw-medium border-top">{{ number_format($totalFixed, 0, ',', '.') }}</td>
                    </tr>
                    @foreach ($fixedAsset as $item)
                        <tr>
                            <td>
                                <span class="lvl-3">
                                    {{ $item->type }}
                                </span>
                            </td>
                            <td>{{ number_format($item->total_amount, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                    <tr>
                        <td class="fw-medium">
                            <span class="lvl-2">
                                Jumlah Nilai Histori
                            </span>
                        </td>
                        <td class="fw-medium border-top">{{ number_format($totalFixed ?? 0, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <span class="lvl-1">
                                Akumulasi Penyusutan
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td class="fw-medium">
                            <span class="lvl-2">
                                Akumulasi Penyusutan
                            </span>
                        </td>
                        <td class="fw-medium border-top">{{ number_format($grandTotalPenyusutan, 0, ',', '.') }}</td>
                    </tr>
                    @foreach ($penyusutan as $item)
                        <tr>
                            <td>
                                <span class="lvl-3">
                                    Akum. Penys. {{ $item['type'] }}
                                </span>
                            </td>
                            <td class="text-danger"> - {{ number_format($item['total_penyusutan'], 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                    <tr>
                        <td class="fw-medium">
                            <span class="lvl-2">
                                Jumlah Akumulasi Penyusutan
                            </span>
                        </td>
                        <td class="text-danger border-top">{{ number_format($grandTotalPenyusutan ?? 0, 0, ',', '.') }}
                        </td>
                    </tr>
                    <tr>
                        <td class="fw-medium">
                            <span class="lvl-2">
                                Jumlah Aktiva Tetap
                            </span>
                        </td>
                        <td class="fw-medium border-top">{{ number_format($totalTetap ?? 0, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <span class="lvl-1">
                                OTHER ASSETS
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td class="fw-medium">
                            <span class="lvl-2">
                                Jumlah OTHER ASSETS
                            </span>
                        </td>
                        <td class="fw-medium border-top">{{ number_format(0, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td class="fw-medium">
                            <span class="lvl-2">
                                Jumlah Aktiva
                            </span>
                        </td>
                        <td class="fw-medium border-top">{{ number_format($totalAktiva, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <span class="lvl-1">
                                Kewajiban dan Ekuitas
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <span class="lvl-1">
                                Kewajiban
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <span class="lvl-1">
                                Kewajiban Lancar
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <span class="lvl-1">
                                Hutang Dagang
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td class="fw-medium">
                            <span class="lvl-2">
                                Jumlah Hutang Dagang
                            </span>
                        </td>
                        <td class="fw-medium border-top">{{ number_format(0, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td colspan="2" class="fw-medium">
                            <span class="lvl-1">
                                Kewajiban Lancar Lain
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span class="lvl-3">
                                PPN Keluaran
                            </span>
                        </td>
                        <td>{{ number_format($ppnKel, 0, ',', '.') }}</td>
                    </tr>
                    <td class="fw-medium">
                        <span class="lvl-2">
                            Jumlah Kewajiban Lancar Lain
                        </span>
                    </td>
                    <td class="fw-medium border-top">{{ number_format($ppnKel ?? 0, 0, ',', '.') }}</td>
                    </tr>
                    </tr>
                    <td class="fw-medium">
                        <span class="lvl-2">
                            Jumlah Kewajiban Lancar
                        </span>
                    </td>
                    <td class="fw-medium border-top">{{ number_format($ppnKel ?? 0, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td colspan="2" class="fw-medium">
                            <span class="lvl-1">
                                Kewajiban Jangka Panjang
                            </span>
                        </td>
                    </tr>
                    </tr>
                    <td class="fw-medium">
                        <span class="lvl-2">
                            Jumlah Kewajiban Jangka Panjang
                        </span>
                    </td>
                    <td class="fw-medium border-top">{{ number_format(0, 0, ',', '.') }}</td>
                    </tr>
                    </tr>
                    <td class="fw-medium">
                        <span class="lvl-2">
                            Jumlah Kewajiban
                        </span>
                    </td>
                    <td class="fw-medium border-top">{{ number_format($ppnKel ?? 0, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td colspan="2" class="fw-medium">
                            <span class="lvl-1">
                                Ekuitas
                            </span>
                        </td>
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
                        <td class="fw-medium">
                            <span class="lvl-2">
                                Ekuitas
                            </span>
                        </td>
                        <td class="fw-medium">{{ number_format($ekuitas, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td>
                            <span class="lvl-3">
                                Modal
                            </span>
                        </td>
                        <td>{{ number_format(250000000, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td>
                            <span class="lvl-3">
                                Laba Ditahan
                            </span>
                        </td>
                        <td>{{ number_format($labaTahunLalu, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td>
                            <span class="lvl-3">
                                Prive
                            </span>
                        </td>
                        <td class="text-danger">- {{ number_format($prive, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td>
                            <span class="lvl-3">
                                Laba Tahun Sebelumnya
                            </span>
                        </td>
                        <td>{{ number_format($sebelumnya, 0, ',', '.') }}</td>
                        {{-- <td>{{ number_format($sebelumnya, 0, ',', '.') }}</td> --}}
                    </tr>
                    <tr>
                        <td>
                            <span class="lvl-3">
                                OPENING BALANCE EQUITY
                            </span>
                        </td>
                        <td>{{ number_format(0, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td>
                            <span class="lvl-3">
                                Laba {{ @$month ? 'Bulan' : 'Tahun' }} Ini
                            </span>
                        </td>
                        <td>{{ number_format(@$month ? $labaBulanIni : $labaTahunIni, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td class="fw-medium">
                            <span class="lvl-2">
                                Jumlah Ekuitas
                            </span>
                        </td>
                        <td class="fw-medium border-top">{{ number_format($totalekuitas ?? 0, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td class="fw-medium">
                            <span class="lvl-2">
                                Jumlah Ekuitas Dan Kewajiban
                            </span>
                        </td>
                        <td class="fw-medium border-top">{{ number_format($ekujiban ?? 0, 0, ',', '.') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@push('after-style')
    <!-- Page CSS -->
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/css/pages/app-invoice-print-income.css" />
    <link rel="stylesheet" href="style.css">
    <style>
    </style>
@endpush
@push('after-script')
    <script src="{{ asset('assets') }}/js/app-invoice-print.js"></script>
@endpush
