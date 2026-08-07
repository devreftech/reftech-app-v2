@extends('layouts.sales.app')
@section('title', $expense->memo)
<div class="invoice-print p-4">
    <div class="container-fluid flex-grow-1 container-p-y">

        <div class="card-body">
            <div class="d-flex justify-content-between flex-xl-row flex-md-column flex-sm-row flex-column">
                <div class="mb-xl-0 pb-1">
                    <div class="d-flex svg-illustration align-items-center gap-2 mb-4">
                        <span class="app-brand-logo demo">
                            <span style="color: var(--bs-primary)">
                                <img class="text-md" src="{{ asset('/asset') }}/logo/Reftech-Log.png" alt=""
                                    srcset="" width="60%">
                            </span>
                        </span>
                    </div>
                </div>
                <div class="text-end">
                    <h3 class="fw-bold">Transaction Journal</h3>
                    <div>
                        <span class="fw-bolder">{{ $expense->no_invoice }}</span>
                    </div>
                    <div class="mt-1">
                        <span class="text-muted">{{ Carbon\Carbon::parse($expense->date)->format('d-m-Y') }}</span>
                    </div>
                </div>
            </div>
        </div>
        <hr class="my-0">
        <div class="card-body mb-3">
            <div class="d-flex justify-content-between flex-xl-row flex-md-column flex-sm-row flex-column">
                <div class="row">
                    <h4>{{ $expense->memo }}</h4>
                    <div class="col-4 fw-medium">
                        <p class="mb-1">No Cheque.</p>
                        {{-- <p class="mb-1">bank</p> --}}
                    </div>
                    <div class="col-8">
                        <p class="mb-1">: {{ $expense->no_cheque }}</p>
                        {{-- <p class="mb-1">: {{ $expense->bank->bank }} {{ $expense->bank->no_rek }}</p> --}}
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
                        <th>Debit</th>
                        <th>Kredit</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $no = 0;
                    @endphp
                    @foreach ($detailExpense as $detail)
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
                            <td class="align-top">RP {{ number_format($detail->amount, 2, '', '.') }}</td>
                            <td class="align-top">RP 0,00</td>
                        </tr>
                    @endforeach
                    @if (@$expense->id_bank)
                        <tr style="font-size: 13px">
                            <td class="align-top">{{ $no + 1 }}</td>
                            <td class="align-top">
                                <p class="mb-0 fw-semibold">
                                    1102-003
                                </p>
                            </td>
                            <td class="align-top">BCA IDR</td>
                            <td class="align-top">Kas/Bank</td>
                            <td class="align-top"> RP 0,00</td>
                            <td> RP {{ number_format($expense->amount, 0, '', '.') }}</td>
                        </tr>
                    @endif
                    <tr style="font-size: 13px">
                        <td colspan="3" style="border:none;"></td>
                        <td>Total</td>
                        <td class="align-top"> RP {{ number_format($expense->amount, 0, '', '.') }}</td>
                        <td class="align-top"> RP
                            {{ $expense->id_bank ? number_format($expense->amount, 0, '', '.') : '0,00 ' }}</td>
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
