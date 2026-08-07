@extends('layouts.sales.app')
@section('title', 'report')
@section('content')
    @if (Auth::user()->role == 'Sales')
        <div class="card mb-3">
            <div class="card-body">
                <div class="row">
                    <div class="col-12 col-md-3">
                        <img src="{{ url('') . '/' . Auth::user()->image }}" alt="" srcset="" class="h-100 w-100">
                    </div>
                    <div class="col-12 col-md-9">
                        <div class="row">
                            <div class="col-12">
                                <h4>{{ Auth::user()->name }}</h4>
                            </div>
                            <div class="col-4">
                                <p class="fw-medium fs-normal">Key Performance Indicator</p>
                                <div class="d-flex mb-2 gap-2">
                                    <a href="#po">
                                        <div class="avatar">
                                            <div class="avatar-initial bg-label-secondary rounded">
                                                <i class="mdi mdi-account-multiple-plus-outline mdi-24px"></i>
                                            </div>
                                        </div>
                                    </a>
                                    <div class="card-info">
                                        <h5 class="mb-0">{{ $totalLeads }}<span
                                                class="text-muted fs-tiny fw-normal">/{{ $target->leads }}</span>
                                        </h5>
                                        <small class="text-muted">New Leads</small>
                                    </div>
                                </div>
                                <div class="d-flex mb-2 gap-2">
                                    <a href="#activities">
                                        <div class="avatar">
                                            <div class="avatar-initial bg-label-info rounded">
                                                <i class="mdi mdi-phone-outline mdi-24px"></i>
                                            </div>
                                        </div>
                                    </a>
                                    <div class="card-info">
                                        <h5 class="mb-0">{{ $totalDC }} <span
                                                class="text-muted fs-tiny fw-normal">/{{ $target->dc }}</span>
                                        </h5>
                                        <small class="text-muted">Daily Call</small>
                                    </div>
                                </div>
                                <div class="d-flex mb-2 gap-2">
                                    <a href="#activities">
                                        <div class="avatar">
                                            <div class="avatar-initial bg-label-primary rounded">
                                                <i class="mdi mdi-account-multiple-outline mdi-24px"></i>
                                            </div>
                                        </div>
                                    </a>
                                    <div class="card-info">
                                        <h5 class="mb-0">{{ $totalCRM }}<span
                                                class="text-muted fs-tiny fw-normal">/{{ $target->crm }}</span>
                                        </h5>
                                        <small class="text-muted">CRM</small>
                                    </div>
                                </div>
                                @php
                                    $lastDetail = Auth::user()->detail->last();
                                @endphp
                                @if ($lastDetail->area == 'Bekasi' || $lastDetail->area == 'Jabodetabek' || $lastDetail->area == 'Jawa Barat')
                                    <div class="d-flex mb-2 gap-2">
                                        <a href="#activities">
                                            <div class="avatar">
                                                <div class="avatar-initial bg-label-danger rounded">
                                                    <i class="mdi mdi-office-building-marker-outline mdi-24px"></i>
                                                </div>
                                            </div>
                                        </a>
                                        <div class="card-info">
                                            <h5 class="mb-0">{{ $totalVisit }}<span
                                                    class="text-muted fs-tiny fw-normal">/{{ $target->visit }}</span>
                                            </h5>
                                            <small class="text-muted">Visit</small>
                                        </div>
                                    </div>
                                @endif
                                <div class="d-flex mb-2 gap-2">
                                    <a href="#quote">
                                        <div class="avatar">
                                            <div class="avatar-initial bg-label-warning rounded">
                                                <i class="mdi mdi-email-multiple-outline mdi-24px"></i>
                                            </div>
                                        </div>
                                    </a>
                                    <div class="card-info">
                                        <h5 class="mb-0">{{ $totalQuote }}<span
                                                class="text-muted fs-tiny fw-normal">/{{ $target->quote }}</span>
                                        </h5>
                                        <small class="text-muted">Quotation</small>
                                    </div>
                                </div>
                                <div class="d-flex mb-2 gap-2">
                                    <a href="#po">
                                        <div class="avatar">
                                            <div class="avatar-initial bg-label-success rounded">
                                                <i class="mdi mdi-cart-plus mdi-24px"></i>
                                            </div>
                                        </div>
                                    </a>
                                    <div class="card-info">
                                        <h5 class="mb-0">{{ $totalLeads }}
                                        </h5>
                                        <small class="text-muted">Purchase Order</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-8">
                                <p class="fw-medium fs-normal">Achievement</p>

                                <div class="d-flex align-items-center gap-2">
                                    <div class="d-flex mb-2 gap-2">
                                        <div class="avatar">
                                            <div class="avatar-initial bg-label-success rounded">
                                                <i class="mdi mdi-cart-plus mdi-24px"></i>
                                            </div>
                                        </div>
                                        <div class="card-info">
                                            <h5 class="mb-0">Rp
                                                {{ number_format($amountSales, 2, ',', '.') }}
                                                @php
                                                    $jumlah_target = [];
                                                    if (isset($target->total) && $target->total != 0) {
                                                        $jumlah_target = ($amountSales / $target->total) * 100;
                                                    } else {
                                                        $jumlah_target = 0;
                                                    }
                                                @endphp
                                                <span class="text-success mb-0">
                                                    {{ number_format($jumlah_target, 3) }}%
                                                </span>
                                            </h5>
                                            <small class="text-muted">Total Sales</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="d-flex mb-2 gap-2">
                                        <div class="avatar">
                                            <div class="avatar-initial bg-label-primary rounded">
                                                <i class="mdi mdi-email-multiple-outline mdi-24px"></i>
                                            </div>
                                        </div>
                                        <div class="card-info">
                                            <h5 class="mb-0">
                                                Rp
                                                {{ number_format($amountQuote, 2, ',', '.') }}
                                            </h5>
                                            <small class="text-muted">Quotation</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="d-flex mb-2 gap-2">
                                        <div class="avatar">
                                            <div class="avatar-initial bg-label-warning rounded">
                                                <i class="mdi mdi-email-alert-outline mdi-24px"></i>
                                            </div>
                                        </div>
                                        <div class="card-info">
                                            <h5 class="mb-0">Rp {{ number_format($amountProspect, 2, ',', '.') }}
                                            </h5>
                                            <small class="text-muted">Hot Prospect</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card mb-4" id="activities">
            <h5 class="card-header">Assigned: {{ Auth::user()->name }}</h5>
            <div class="table-responsive text-nowrap">
                <table class="table">
                    <thead class="table-light">
                        <tr>
                            <th>Description</th>
                            @php
                                $allWeek = 1;
                            @endphp
                            @foreach ($dataQuote as $week)
                                <th>Week {{ $allWeek }}</th>
                                @php
                                    $allWeek += 1;
                                @endphp
                            @endforeach
                            <th>Total</th>
                            <th>Presentase</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        <tr>
                            <td>
                                <strong>New Leads</strong>
                            </td>
                            @php
                                $totalLeadsFullWeek = 0;
                            @endphp
                            @foreach ($dataLeads as $week)
                                <td>{{ $week['total'] }}</td>
                                @php
                                    $totalLeadsFullWeek += $week['total'];
                                @endphp
                            @endforeach
                            <td>{{ $totalLeadsFullWeek }}</td>
                            <td>
                                @php
                                    if (is_array($dataLeads)) {
                                        $jumlahData = count($dataLeads);
                                    }
                                @endphp
                                @if ($jumlahData > 4)
                                    {{ round(($totalLeadsFullWeek / ($target->leads + $target->leads / 4)) * 100) }} %
                                @elseif($jumlahData == 4)
                                    {{ round(($totalLeadsFullWeek / $target->leads) * 100) }} %
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <strong>Daily Call</strong>
                            </td>
                            @php
                                $totalDCFullWeek = 0;
                            @endphp
                            @foreach ($dataDc as $week)
                                <td>{{ $week['total'] }}</td>
                                @php
                                    $totalDCFullWeek += $week['total'];
                                @endphp
                            @endforeach
                            <td>{{ $totalDCFullWeek }}</td>
                            <td>
                                @php
                                    if (is_array($dataDc)) {
                                        $jumlahData = count($dataDc);
                                    }
                                @endphp
                                @if ($jumlahData > 4)
                                    {{ round(($totalDCFullWeek / ($target->dc + $target->dc / 4)) * 100) }} %
                                @elseif($jumlahData == 4)
                                    {{ round(($totalDCFullWeek / $target->dc) * 100) }} %
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <strong>CRM</strong>
                            </td>
                            @php
                                $totalCrmFullWeek = 0;
                            @endphp
                            @foreach ($dataCRM as $week)
                                <td>{{ $week['total'] }}</td>
                                @php
                                    $totalCrmFullWeek += $week['total'];
                                @endphp
                            @endforeach
                            <td>{{ $totalCrmFullWeek }}</td>
                            <td>
                                @if ($jumlahData > 4)
                                    {{ round(($totalCrmFullWeek / ($target->crm + $target->crm / 4)) * 100) }} %
                                @elseif($jumlahData == 4)
                                    {{ round(($totalCrmFullWeek / $target->crm) * 100) }} %
                                @endif
                            </td>
                        </tr>
                        @if (Auth::user()->detail[0]->area == 'Bekasi')
                            <tr>
                                <td>
                                    <strong>Visit</strong>
                                </td>
                                @php
                                    $totalVisitFullWeek = 0;
                                @endphp
                                @foreach ($dataVisit as $week)
                                    <td>{{ $week['total'] }}</td>
                                    @php
                                        $totalVisitFullWeek += $week['total'];
                                    @endphp
                                @endforeach
                                <td>{{ $totalVisitFullWeek }}</td>
                                <td>
                                    @if ($jumlahData > 4)
                                        {{ round(($totalVisitFullWeek / ($target->visit + $target->visit / 4)) * 100) }} %
                                    @elseif($jumlahData == 4)
                                        {{ round(($totalVisitFullWeek / $target->visit) * 100) }} %
                                    @endif
                                </td>
                            </tr>
                        @endif
                        <tr>
                            <td>
                                <strong>Quotation</strong>
                            </td>
                            @php
                                $totalQuoteFullWeek = 0;
                            @endphp
                            @foreach ($dataQuote as $week)
                                <td>{{ $week['total'] }}</td>
                                @php
                                    $totalQuoteFullWeek += $week['total'];
                                @endphp
                            @endforeach
                            <td>{{ $totalQuoteFullWeek }}</td>
                            <td>
                                @if ($jumlahData > 4)
                                    {{ round(($totalQuoteFullWeek / ($target->quote + $target->quote / 4)) * 100) }} %
                                @elseif($jumlahData == 4)
                                    {{ round(($totalQuoteFullWeek / $target->quote) * 100) }} %
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <strong>Prucashing Order</strong>
                            </td>
                            @php
                                $totalPoFullWeek = 0;
                            @endphp
                            @foreach ($dataPo as $week)
                                <td>{{ $week['total'] }}</td>
                                @php
                                    $totalPoFullWeek += $week['total'];
                                @endphp
                            @endforeach
                            <td>{{ $totalPoFullWeek }}</td>
                            <td>
                                {{-- @if ($jumlahData > 4)
                                    {{ round(($totalPoFullWeek / ($target->leads + $target->leads / 4)) * 100) }} %
                                @elseif($jumlahData == 4)
                                    {{ round(($totalPoFullWeek / $target->leads) * 100) }} %
                                @endif --}}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="row" id="quote">
            <div class="col-md-6">
                <div class="card mb-4">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">
                            <div class="d-flex justify-content-between">
                                <p class="fw-semibold m-0"> Total Quotation</p>
                                <p class="text-muted m-0">{{ $totalQuoteFullWeek }}</p>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="d-flex justify-content-between">
                                <p class="fw-semibold m-0"> Total PO</p>
                                <p class="text-muted m-0">{{ $totalPoFullWeek }}</p>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="card mb-4" id="po">
            <h5 class="card-header">Total PO</h5>
            <div class="table-responsive text-nowrap">
                <table class="table">
                    <thead class="table-light">
                        <tr>
                            <th>PO No.</th>
                            <th>Company</th>
                            <th>Title</th>
                            <th>PO Date</th>
                            <th>Price</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @php
                            $totalP = 0;
                        @endphp
                        @foreach ($quotation as $quote)
                            @php
                                $totalQ = $quote['nett'];
                                $totalP += $totalQ;
                            @endphp
                            <tr>
                                <td>
                                    <strong>{{ $quote->no_quote }}</strong>
                                </td>
                                <td>{{ $quote->pic->client->company }}</td>
                                <td>{{ $quote->title }}</td>
                                <td>{{ \Carbon\Carbon::parse($quote->estimated_date)->format('d-m-Y') }}</td>
                                <td class="text-end">Rp {{ number_format($quote->nett, 0, '', '.') }}</td>
                            </tr>
                        @endforeach
                        @foreach ($unitQuotationPO as $uq)
                            @php
                                $uqNett  = $uq->total - $uq->tax_amount;
                                $totalP += $uqNett;
                                $uqDate  = $uq->statusHistory->first()?->created_at;
                            @endphp
                            <tr>
                                <td>
                                    <a href="{{ route('unit-quotation.show', $uq->id) }}" class="fw-bold">
                                        {{ $uq->no_quote }}
                                    </a>
                                    <span class="badge bg-label-danger ms-1">Unit</span>
                                </td>
                                <td>{{ $uq->client?->company ?? '-' }}</td>
                                <td>{{ $uq->title ?? '-' }}</td>
                                <td>{{ $uqDate ? \Carbon\Carbon::parse($uqDate)->format('d-m-Y') : '-' }}</td>
                                <td class="text-end">Rp {{ number_format($uqNett, 0, '', '.') }}</td>
                            </tr>
                        @endforeach
                        <tr class="bg-label-secondary">
                            <td colspan="3">
                            </td>
                            <td><strong>Total</strong></td>
                            <td class="text-end"><strong>Rp {{ number_format($totalP, 0, '', '.') }}</strong></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    @elseif (Auth::user()->role == 'Admin')
        <div class="container-xxl flex-grow-1 container-p-y">
            {{-- Regita --}}
            <div class="card mb-4">
                <h5 class="card-header">Assigned: Miss Regita</h5>
                <div class="table-responsive text-nowrap">
                    <table class="table">
                        <thead class="table-light">
                            <tr>
                                <th>Description</th>
                                <th>Week I</th>
                                <th>Week II</th>
                                <th>Week III</th>
                                <th>Week IV</th>
                                <th>Week V</th>
                                <th>Total</th>
                                <th>Presentase</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            <tr>
                                <td>
                                    <strong>Daily Call</strong>
                                </td>
                                <td>50</td>
                                <td>51</td>
                                <td>45</td>
                                <td>50</td>
                                <td>55</td>
                                <td>306</td>
                                <td>101%</td>
                            </tr>
                            <tr>
                                <td>
                                    <strong>Presentation / Visit</strong>
                                </td>
                                <td>50</td>
                                <td>51</td>
                                <td>45</td>
                                <td>50</td>
                                <td>55</td>
                                <td>306</td>
                                <td>101%</td>
                            </tr>
                            <tr>
                                <td>
                                    <strong>Quotation</strong>
                                </td>
                                <td>50</td>
                                <td>51</td>
                                <td>45</td>
                                <td>50</td>
                                <td>55</td>
                                <td>306</td>
                                <td>101%</td>
                            </tr>
                            <tr>
                                <td>
                                    <strong>Prucashing Order</strong>
                                </td>
                                <td>50</td>
                                <td>51</td>
                                <td>45</td>
                                <td>50</td>
                                <td>55</td>
                                <td>306</td>
                                <td>101%</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="row mb-5">
                <div class="col-md-6">
                    <div class="card mb-4">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">
                                <div class="d-flex justify-content-between">
                                    <p class="fw-semibold m-0"> Total Quotation</p>
                                    <p class="text-muted m-0">50</p>
                                </div>
                            </li>
                            <li class="list-group-item">
                                <div class="d-flex justify-content-between">
                                    <p class="fw-semibold m-0"> Total PO</p>
                                    <p class="text-muted m-0">50</p>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            {{-- Yolan --}}
            <div class="card mb-4">
                <h5 class="card-header">Assigned: Miss Yolan</h5>
                <div class="table-responsive text-nowrap">
                    <table class="table">
                        <thead class="table-light">
                            <tr>
                                <th>Description</th>
                                <th>Week I</th>
                                <th>Week II</th>
                                <th>Week III</th>
                                <th>Week IV</th>
                                <th>Week V</th>
                                <th>Total</th>
                                <th>Presentase</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            <tr>
                                <td>
                                    <strong>Daily Call</strong>
                                </td>
                                <td>50</td>
                                <td>51</td>
                                <td>45</td>
                                <td>50</td>
                                <td>55</td>
                                <td>306</td>
                                <td>101%</td>
                            </tr>
                            <tr>
                                <td>
                                    <strong>Presentation / Visit</strong>
                                </td>
                                <td>50</td>
                                <td>51</td>
                                <td>45</td>
                                <td>50</td>
                                <td>55</td>
                                <td>306</td>
                                <td>101%</td>
                            </tr>
                            <tr>
                                <td>
                                    <strong>Quotation</strong>
                                </td>
                                <td>50</td>
                                <td>51</td>
                                <td>45</td>
                                <td>50</td>
                                <td>55</td>
                                <td>306</td>
                                <td>101%</td>
                            </tr>
                            <tr>
                                <td>
                                    <strong>Prucashing Order</strong>
                                </td>
                                <td>50</td>
                                <td>51</td>
                                <td>45</td>
                                <td>50</td>
                                <td>55</td>
                                <td>306</td>
                                <td>101%</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="row mb-5">
                <div class="col-md-6">
                    <div class="card mb-4">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">
                                <div class="d-flex justify-content-between">
                                    <p class="fw-semibold m-0"> Total Quotation</p>
                                    <p class="text-muted m-0">50</p>
                                </div>
                            </li>
                            <li class="list-group-item">
                                <div class="d-flex justify-content-between">
                                    <p class="fw-semibold m-0"> Total PO</p>
                                    <p class="text-muted m-0">50</p>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            {{-- Ari --}}
            <div class="card mb-4">
                <h5 class="card-header">Assigned: Mister Ari</h5>
                <div class="table-responsive text-nowrap">
                    <table class="table">
                        <thead class="table-light">
                            <tr>
                                <th>Description</th>
                                <th>Week I</th>
                                <th>Week II</th>
                                <th>Week III</th>
                                <th>Week IV</th>
                                <th>Week V</th>
                                <th>Total</th>
                                <th>Presentase</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            <tr>
                                <td>
                                    <strong>Daily Call</strong>
                                </td>
                                <td>50</td>
                                <td>51</td>
                                <td>45</td>
                                <td>50</td>
                                <td>55</td>
                                <td>306</td>
                                <td>101%</td>
                            </tr>
                            <tr>
                                <td>
                                    <strong>Presentation / Visit</strong>
                                </td>
                                <td>50</td>
                                <td>51</td>
                                <td>45</td>
                                <td>50</td>
                                <td>55</td>
                                <td>306</td>
                                <td>101%</td>
                            </tr>
                            <tr>
                                <td>
                                    <strong>Quotation</strong>
                                </td>
                                <td>50</td>
                                <td>51</td>
                                <td>45</td>
                                <td>50</td>
                                <td>55</td>
                                <td>306</td>
                                <td>101%</td>
                            </tr>
                            <tr>
                                <td>
                                    <strong>Prucashing Order</strong>
                                </td>
                                <td>50</td>
                                <td>51</td>
                                <td>45</td>
                                <td>50</td>
                                <td>55</td>
                                <td>306</td>
                                <td>101%</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="row mb-5">
                <div class="col-md-6">
                    <div class="card mb-4">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">
                                <div class="d-flex justify-content-between">
                                    <p class="fw-semibold m-0"> Total Quotation</p>
                                    <p class="text-muted m-0">50</p>
                                </div>
                            </li>
                            <li class="list-group-item">
                                <div class="d-flex justify-content-between">
                                    <p class="fw-semibold m-0"> Total PO</p>
                                    <p class="text-muted m-0">50</p>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            {{-- Yusuf --}}
            <div class="card mb-4">
                <h5 class="card-header">Assigned: Mister Yusuf</h5>
                <div class="table-responsive text-nowrap">
                    <table class="table">
                        <thead class="table-light">
                            <tr>
                                <th>Description</th>
                                <th>Week I</th>
                                <th>Week II</th>
                                <th>Week III</th>
                                <th>Week IV</th>
                                <th>Week V</th>
                                <th>Total</th>
                                <th>Presentase</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            <tr>
                                <td>
                                    <strong>Daily Call</strong>
                                </td>
                                <td>50</td>
                                <td>51</td>
                                <td>45</td>
                                <td>50</td>
                                <td>55</td>
                                <td>306</td>
                                <td>101%</td>
                            </tr>
                            <tr>
                                <td>
                                    <strong>Presentation / Visit</strong>
                                </td>
                                <td>50</td>
                                <td>51</td>
                                <td>45</td>
                                <td>50</td>
                                <td>55</td>
                                <td>306</td>
                                <td>101%</td>
                            </tr>
                            <tr>
                                <td>
                                    <strong>Quotation</strong>
                                </td>
                                <td>50</td>
                                <td>51</td>
                                <td>45</td>
                                <td>50</td>
                                <td>55</td>
                                <td>306</td>
                                <td>101%</td>
                            </tr>
                            <tr>
                                <td>
                                    <strong>Prucashing Order</strong>
                                </td>
                                <td>50</td>
                                <td>51</td>
                                <td>45</td>
                                <td>50</td>
                                <td>55</td>
                                <td>306</td>
                                <td>101%</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="card mb-4">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">
                                <div class="d-flex justify-content-between">
                                    <p class="fw-semibold m-0"> Total Quotation</p>
                                    <p class="text-muted m-0">50</p>
                                </div>
                            </li>
                            <li class="list-group-item">
                                <div class="d-flex justify-content-between">
                                    <p class="fw-semibold m-0"> Total PO</p>
                                    <p class="text-muted m-0">50</p>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection
