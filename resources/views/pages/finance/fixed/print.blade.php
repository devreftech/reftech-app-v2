@extends('layouts.sales.app')
@section('title', 'Print Opname')
<div class="invoice-print p-4">
    <div class="container-fluid flex-grow-1 container-p-y">
        <div class="text-center">
            <h3>PT. REFTECH JAYA OPTIMA</h3>
            <h2 class="text-danger">Laba/Rugi (Standar)</h2>
            <h4>Dari {{ $startString }} ke {{ $endString }}</h4>
        </div>
        <hr>
        {{-- <h5 class="text-muted">*note: yang belum diinput dijadikan 0;</h5> --}}
        @php
            $incomeCharge = $incomeSum - $chargeSum;
            $subtotal = $poSum - $modalSum;
            $total = $subtotal - $expenseSum + $incomeCharge;
        @endphp
        <div class="mb-2">
            <table class="table table-borderless m-0" style="width: 100%">
                <thead class="table-light border-top">
                    <tr>
                        <th>Description</th>
                        <th>{{ $startStringYear }}-{{ $endString }}</th>
                    </tr>
                </thead>
                <tbody class="text-dark">
                    <tr>
                        <td colspan="2" class="fw-medium">Pendapatan</td>
                    </tr>
                    <tr>
                        <td class="fw-medium">Pendapatan</td>
                        <td class="fw-medium">{{ number_format($poSum, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td>Penjualan</td>
                        <td>{{ number_format($poSum, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td>Potongan Penjualan</td>
                        <td class="text-danger">- 0
                            {{-- {{ number_format($poCount,0,',','.')}} --}}
                        </td>
                    </tr>
                    <tr>
                        <td class="fw-medium">Jumlah Pendapatan</td>
                        <td class="fw-medium border-top">{{ number_format($poSum, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td colspan="2" class="fw-medium">Harga Pokok Penjualan</td>
                    </tr>
                    <tr>
                        <td class="fw-medium">Harga Pokok Penjualan</td>
                        <td class="fw-medium">{{ number_format($modalSum, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td>Harga Barang Dagang</td>
                        <td>{{ number_format($modalSum, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td class="fw-medium">Jumlah Harga Pokok Penjualan</td>
                        <td class="fw-medium border-top">{{ number_format($modalSum, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold">LABA KOTOR</td>
                        <td class="fw-bold border-top">{{ number_format($poSum, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td colspan="2" class="fw-medium">Beban Operasi</td>
                    </tr>
                    @foreach ($allExpense as $item)
                        <tr>
                            <td>{{ $item->account->name }}</td>
                            <td>{{ number_format($item->amount, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                    <tr>
                        <td class="fw-medium">Jumlah Beban Operasi</td>
                        <td class="fw-medium border-top">{{ number_format($expenseSum ?? 0, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td class="fw-medium">PENDAPATAN OPERASI</td>
                        <td class="fw-medium border-top">{{ number_format($subtotal - $expenseSum, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td colspan="2" class="fw-medium">Pendapatan Beban Lain</td>
                    </tr>
                    <tr>
                        <td colspan="2" class="fw-medium">Pendapatan Lain Lain</td>
                    </tr>
                    @foreach ($allIncome as $item)
                        <tr>
                            <td>{{ $item->description }}</td>
                            <td>{{ number_format($item->amount, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                    <tr>
                        <td class="fw-medium">Jumlah Pendapatan Lain</td>
                        <td class="fw-medium border-top">{{ number_format($incomeSum ?? 0, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td colspan="2" class="fw-medium">Beban Lain Lain</td>
                    </tr>
                    @foreach ($allCharge as $item)
                        <tr>
                            <td>{{ $item->description }}</td>
                            <td>{{ number_format($item->amount, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                    <tr>
                        <td class="fw-medium">Jumlah Beban Lain</td>
                        <td class="fw-medium border-top">{{ number_format($chargeSum ?? 0, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td class="fw-medium">Jumlah Pendapatan Beban Lain</td>
                        <td class="fw-medium border-top">{{ number_format($incomeCharge ?? 0, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td class="fw-medium">LABA(RUGI) BERSIH</td>
                        <td class="fw-medium border-top">{{ number_format($total ?? 0, 0, ',', '.') }}</td>
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
