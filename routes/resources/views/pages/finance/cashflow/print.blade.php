@extends('layouts.sales.app')
@section('title', 'Print Opname')
<div class="invoice-print p-4">
    <div class="container-fluid flex-grow-1 container-p-y">
        <div class="text-center">
            <h3>PT. REFTECH JAYA OPTIMA</h3>
            <h2 class="text-danger">Arus Kas (Metode Langsung)</h2>
            <h4>Dari {{ $startString }} ke {{ $endString }}</h4>
        </div>
        <hr>
        <div class="mb-2">
            {{-- @php
                $totalLancar = $piutang + $asset + $ppnMas;
                $totalTetap = $totalFixed - $grandTotalPenyusutan;
                $totalAktiva = $totalLancar + $totalTetap;
            @endphp --}}
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
                                Arus Kas dari Aktivitas Operasi
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span class="lvl-1">
                                Kas dari Penjualan
                            </span>
                        </td>
                        <td>{{ number_format($quotation, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td class="fw-medium">
                            <span class="lvl-2">
                                Pendapatan Lain Lain
                            </span>
                        </td>
                        <td class="fw-medium">{{ number_format($income, 0, ',', '.') }}</td>
                    </tr>
                    @foreach ($pendapatan as $item)
                        <tr>
                            <td>
                                <span class="lvl-3">
                                    {{ $item->description }}
                                </span>
                            </td>
                            <td>{{ number_format($item->amount, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                    <tr>
                        <td>
                            <span class="lvl-1">
                                Kas Untuk Pembelian
                            </span>
                        </td>
                        <td>TBA</td>
                    </tr>
                    @foreach ($expensePerAccount as $item)
                        <tr>
                            <td>
                                <span class="lvl-3">
                                    {{ $item->name }}
                                </span>
                            </td>
                            <td class="text-danger">-{{ number_format($item->total_amount, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                    <tr>
                        <td>
                            <span class="lvl-1">
                                Biaya Lain Lain
                            </span>
                        </td>
                        <td>TBA</td>
                    </tr>
                    @foreach ($biaya as $item)
                        <tr>
                            <td>
                                <span class="lvl-3">
                                    {{ $item->description }}
                                </span>
                            </td>
                            <td class="text-danger">-{{ number_format($item->amount, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                    <tr>
                        <td>
                            <span class="lvl-1">
                                Laba/Rugi Penghentian Aktiva Tetap
                            </span>
                        </td>
                        <td>TBA</td>
                    </tr>
                    <tr>
                        <td class="fw-medium">
                            <span class="lvl-2">
                                Laba(Rugi) Operasi sebelum berubah di Operasi Aktiva dan Kewajiban
                            </span>
                        </td>
                        <td class="fw-medium border-top">TBA</td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <span class="lvl-1">
                                Berkurang(Bertambah) pada Operasi Aktiva
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td class="fw-medium">
                            <span class="lvl-2">
                                Persediaan Barang Dagang
                            </span>
                        </td>
                        <td class="fw-medium border-top">TBA</td>
                    </tr>
                    <tr>
                        <td>
                            <span class="lvl-3">
                                Screw Compressor 132 KW
                            </span>
                        </td>
                        <td>TBA</td>
                    </tr>
                    <tr>
                        <td>
                            <span class="lvl-3">
                                Piutang Lain-lain IDR
                            </span>
                        </td>
                        <td>{{ number_format($piutang, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td>
                            <span class="lvl-3">
                                PPN Masukan
                            </span>
                        </td>
                        <td>{{ number_format($ppnMas, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td>
                            <span class="lvl-3">
                                Transakasi Aktiva Tetap
                            </span>
                        </td>
                        <td class="text-danger">TBA</td>
                    </tr>
                    <tr>
                        <td class="fw-bolder">
                            <span class="lvl-2">
                                Akumulasi Penyusutan
                            </span>
                        </td>
                        <td class="text-danger">TBA</td>
                    </tr>
                    <tr>
                        <td>
                            <span class="lvl-2">
                                Akum. Peny. Mesin
                            </span>
                        </td>
                        <td class="text-danger">TBA</td>
                    </tr>
                    <tr>
                        <td>
                            <span class="lvl-1">
                                Jumlah Berkurang(Bertambah) pada Operasi Aktiva
                            </span>
                        </td>
                        <td class="text-danger border-top">TBA</td>
                    </tr>
                    <tr>
                        <td>
                            <span class="lvl-1">
                                Bertambah (berkurang) pada Operasi Kewajiban
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span class="lvl-2">
                                PPN Keluaran
                            </span>
                        </td>
                        <td class="border-top">-{{ number_format($ppnKel, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td>
                            <span class="lvl-1">
                                Jumlah Bertambah (berkurang) pada Operasi Kewajiban
                            </span>
                        </td>
                        <td class="text-danger border-top">-{{ number_format($ppnKel, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td>
                            <span class="lvl-1">
                                Kas bersih (dipakai)/ dihasilkan oleh Aktivitas Operasi
                            </span>
                        </td>
                        <td class="border-top">TBA</td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <span class="lvl-0">
                                Arus Kas dari Aktivitas Investasi
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span class="lvl-1">
                                Akumulasi Penyusutan
                            </span>
                        </td>
                        <td class="text-danger">TBA</td>
                    </tr>
                    <tr>
                        <td>
                            <span class="lvl-3">
                                Peralatan Kantor
                            </span>
                        </td>
                        <td>TBA</td>
                    </tr>
                    <tr>
                        <td>
                            <span class="lvl-3">
                                Transaksi Aktiva Tetap
                            </span>
                        </td>
                        <td class="text-danger">TBA</td>
                    </tr>
                    <tr>
                        <td>
                            <span class="lvl-3">
                                Tools
                            </span>
                        </td>
                        <td class="text-danger">TBA</td>
                    </tr>
                    <tr>
                        <td>
                            <span class="lvl-3">
                                Mesin Compressor
                            </span>
                        </td>
                        <td>TBA</td>
                    </tr>
                    <tr>
                        <td class="fw-medium">
                            <span class="lvl-2">
                                Kas bersih yg dihasilkan / (dipakai) oleh Aktivitas Investasi
                            </span>
                        </td>
                        <td class="fw-medium border-top">TBA</td>
                    </tr>
                    <tr>
                        <td colspan="2" class="fw-bold fs-4">Arus Kas dari Aktivitas Pendanaan</td>
                    </tr>
                    <tr>
                        <td colspan="2" class="fw-bolder fs-5">Ekuitas</td>
                    </tr>
                    <tr>
                        <td>Dividen</td>
                        <td class="text-danger">- {{ number_format($prive, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td class="fw-medium"> Kas bersih yg dihasilkan dari / (dipakai) oleh Aktivitas Pendanaan</td>
                        <td class="fw-medium border-top text-danger">-TBA</td>
                    </tr>
                    <tr>
                        <td class="fw-medium"> Kas bersih dihasilkan oleh / (dipakai) di Period ini</td>
                        <td class="fw-medium border-top">TBA</td>
                    </tr>
                    <tr>
                        <td class="fw-medium"> Kas & Setara Kas pada Awal Periode</td>
                        <td class="fw-medium border-top">TBA</td>
                    </tr>
                    <tr>
                        <td class="fw-medium"> Kas & Setara Kas pada Akhir Periode</td>
                        <td class="fw-medium border-top">TBA</td>
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
@endpush
@push('after-script')
    <script src="{{ asset('assets') }}/js/app-invoice-print.js"></script>
@endpush
