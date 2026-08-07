@extends('layouts.sales.app')
@section('title', 'Print Opname')
<div class="invoice-print p-4">
    <div class="container-fluid flex-grow-1 container-p-y">
        <div class="text-center">
            <h3>PT. REFTECH JAYA OPTIMA</h3>
            <h2 class="text-danger">Perubahan Modal (Standar)</h2>
            <h4>Dari {{ $startString }} ke {{ $endString }}</h4>
        </div>
        <hr>
        <div class="mb-2">
            <table class="table table-borderless m-0" style="width: 100%">
                <thead class="table-light border-top">
                    <tr>
                        <th>Description</th>
                        <th>Balance</th>
                    </tr>
                </thead>
                <tbody class="text-dark">
                    <tr>
                        <td colspan="2" class="fw-medium">Ekuitas</td>
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
