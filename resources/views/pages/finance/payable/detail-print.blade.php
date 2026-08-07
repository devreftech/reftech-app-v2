@extends('layouts.sales.app')
@section('title', $payable->memo)
<div class="invoice-print p-4">
    <div class="container-fluid flex-grow-1 container-p-y">
        <div class="card-body mb-3">
            <div class="d-flex justify-content-between flex-xl-row flex-md-column flex-sm-row flex-column">
                <div class="row">
                    <h4>Payable of {{ $payable->memo }}</h4>
                    <div class="col-4 fw-medium">
                        <p class="mb-1">no_voucher </p>
                        <p class="mb-1">no_cheque</p>
                        <p class="mb-1">bank</p>
                    </div>
                    <div class="col-8">
                        <p class="mb-1">: {{ $payable->no_voucher }}</p>
                        <p class="mb-1">: {{ $payable->no_cheque }}</p>
                        <p class="mb-1">: {{ $payable->bank->bank }} {{ $payable->bank->no_rek }}</p>
                    </div>
                </div>
                <div class="text-end">
                    <div class="mb-1">
                        <span class="text-muted">{{ Carbon\Carbon::parse($payable->date)->format('d-m-Y') }}</span>
                    </div>
                    <div>
                        <span class="fw-bolderr">{{ $payable->payee }}</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="mb-2">
            <table class="table table-borderless m-0" style="width: 100%">

                <thead class="table-light border-top">
                    <tr>
                        <th>No.</th>
                        <th>Code</th>
                        <th>Account</th>
                        <th>Memo</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $no = 0;
                    @endphp
                    @foreach ($detailPayable as $detail)
                        @php
                            $no++;
                        @endphp
                        <tr style="font-size: 13px">
                            <td class="align-top">{{ $no }}</td>
                            <td class="align-top">
                                <p class="mb-0 fw-semibold">
                                    {{ $detail->account->code }}
                                </p>
                            </td>
                            <td class="align-top">
                                <p>
                                    {{ $detail->account->name }}
                                </p>
                            </td>
                            <td class="align-top">
                                {{ $detail->memo }}
                            </td>
                            <td class="align-top">RP {{ number_format($detail->amount, 0, '', '.') }}</td>
                        </tr>
                    @endforeach
                    <tr style="font-size: 13px">
                        <td colspan="3" style="border:none;"></td>
                        <td>Total</td>
                        <td>: RP {{ number_format($payable->amount, 0, '', '.') }}</td>
                    </tr>
                </tbody>
            </table>
            <p class="fs-5 fw-medium mt-2 p-2" style="background-color: rgb(248, 248, 248); width:100%;"> Say
                amount: #
                {{ $terbilang }} Rupiah</p>
        </div>
    </div>
</div>
@push('after-style')
    <!-- Page CSS -->
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/css/pages/app-invoice-print.css" />
    <link rel="stylesheet" href="style.css">
@endpush
@push('after-script')
    <script src="{{ asset('assets') }}/js/app-invoice-print.js"></script>
@endpush
