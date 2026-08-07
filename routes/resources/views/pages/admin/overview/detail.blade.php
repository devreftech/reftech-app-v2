@extends('layouts.sales.app')
@section('title', 'Overview Sales')
@section('content')
    <h4 class="fw-bold py-3 mb-4">
        Overview Semester {{ $report->semester }}, {{ $report->year }}
    </h4>

    <div class="card mb-3">
        <div class="card-body">
            <div class="row">
                <div class="col-12 col-md-4">
                    <img src="{{ url('') . '/' . $user->image }}" alt="" srcset="" class="h-100 w-100">
                </div>
                <div class="col-12 col-md-8">
                    @if ($user->role == 'Sales')
                        <div class="row">
                            <div class="col-12">
                                <h4>{{ $user->name }}</h4>
                            </div>
                            <div class="col-4">
                                <p class="fw-medium fs-normal">Key Performance Indicator</p>
                                <div class="d-flex gap-2 mb-2">
                                    <div class="avatar">
                                        <div class="avatar-initial bg-label-secondary rounded">
                                            <i class="mdi mdi-account-multiple-plus-outline mdi-24px"></i>
                                        </div>
                                    </div>
                                    <div class="card-info">
                                        @php
                                            $targetLeads6 = ($target->leads ?? 0) * 6;
                                        @endphp
                                        <h5 class="mb-0">{{ $totalLeadsSemester }} / <span
                                                class="fw-lighter fs-tiny">{{ $targetLeads6 ?: '0' }}</span>
                                            <span
                                                class="bg-label-secondary rounded">{{ $targetLeads6 ? round(($totalLeadsSemester * 100) / $targetLeads6, 2) : '0' }}
                                                %</span>
                                        </h5>
                                        <small class="text-muted">Leads</small>
                                    </div>
                                </div>
                                <div class="d-flex gap-2 mb-2">
                                    <div class="avatar">
                                        <div class="avatar-initial bg-label-info rounded">
                                            <i class="mdi mdi-phone-outline mdi-24px"></i>
                                        </div>
                                    </div>
                                    <div class="card-info">
                                        @php
                                            $targetDc6 = ($target->dc ?? 0) * 6;
                                        @endphp
                                        <h5 class="mb-0">{{ $totalDCSemester }} / <span
                                                class="fw-lighter fs-tiny">{{ $targetDc6 ?: '0' }}</span>
                                            <span
                                                class="bg-label-info rounded">{{ $targetDc6 ? round(($totalDCSemester * 100) / $targetDc6, 2) : '0' }}
                                                %</span>
                                        </h5>
                                        <small class="text-muted">Daily Call</small>
                                    </div>
                                </div>
                                <div class="d-flex gap-2 mb-2">
                                    <div class="avatar">
                                        <div class="avatar-initial bg-label-primary rounded">
                                            <i class="mdi mdi-account-multiple-outline mdi-24px"></i>
                                        </div>
                                    </div>
                                    <div class="card-info">
                                        <h5 class="mb-0">{{ $totalCRMSemester }} / <span
                                                class="fw-lighter fs-tiny">{{ $averageCRM ?? '0' }}</span>
                                            <span
                                                class="bg-label-primary rounded">{{ $averageCRM ? round(($totalCRMSemester * 100) / $averageCRM, 2) : '0' }}
                                                %</span>
                                        </h5>
                                        <small class="text-muted">CRM</small>
                                    </div>
                                </div>
                                <div class="d-flex mb-2 gap-2">
                                    <a href="#quote">
                                        <div class="avatar">
                                            <div class="avatar-initial bg-label-warning rounded">
                                                <i class="mdi mdi-email-multiple-outline mdi-24px"></i>
                                            </div>
                                        </div>
                                    </a>
                                    <div class="card-info">
                                        <h5 class="mb-0">{{ $quoteSemester }}
                                            {{-- / <span
                                                class="fw-lighter fs-tiny">{{ $target->quote ? $target->quote * 6 : '0' }}
                                            </span>
                                            <span
                                                class="bg-label-warning rounded">{{ round(($quoteSemester * 100) / ($target->quote * 6), 2) }}
                                                %</span> --}}
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
                                        <h5 class="mb-0">{{ $POSemester }}
                                        </h5>
                                        <small class="text-muted">Purchase Order</small>
                                    </div>
                                </div>
                                <div class="d-flex mb-2 gap-2">
                                    <a href="#po">
                                        <div class="avatar">
                                            <div class="avatar-initial bg-label-danger rounded">
                                                <i class="mdi mdi-cart-minus mdi-24px"></i>
                                            </div>
                                        </div>
                                    </a>
                                    <div class="card-info">
                                        <h5 class="mb-0">{{ $lossSemester }}
                                        </h5>
                                        <small class="text-muted">Loss Quotation</small>
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
                                                {{ number_format($totalPOSemester, 2, ',', '.') }}
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
                                                {{ number_format($totalQuoteSemester, 2, ',', '.') }}
                                            </h5>
                                            <small class="text-muted">Quotation</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="d-flex mb-2 gap-2">
                                        <div class="avatar">
                                            <div class="avatar-initial bg-label-danger rounded">
                                                <i class="mdi mdi-cart-minus mdi-24px"></i>
                                            </div>
                                        </div>
                                        <div class="card-info">
                                            <h5 class="mb-0">Rp {{ number_format($totalLossSemester, 2, ',', '.') }}
                                            </h5>
                                            <small class="text-muted">Loss Quotation</small>
                                        </div>
                                    </div>
                                </div>
                                <p class="fw-medium fs-normal">Percentage Achievement</p>
                                @php
                                    $percentPOTotal = ($targett * 6) ? ($totalPOSemester * 100) / ($targett * 6) : 0;
                                @endphp
                                <div class="d-flex mb-2 gap-2">
                                    <a href="#po">
                                        <div class="avatar">
                                            <div class="avatar-initial bg-label-success rounded">
                                                <i class="mdi mdi-cart-plus mdi-24px"></i>
                                            </div>
                                        </div>
                                    </a>
                                    <div class="card-info">
                                        <h5 class="mb-0">{{ number_format($percentPOTotal, 2) }} %
                                        </h5>
                                        <small class="text-muted">Percentage PO on Target</small>
                                    </div>
                                </div>
                                {{-- <div class="d-flex mb-2 gap-2">
                                    <a href="#po">
                                        <div class="avatar">
                                            <div class="avatar-initial bg-label-danger rounded">
                                                <i class="mdi mdi-cart-minus mdi-24px"></i>
                                            </div>
                                        </div>
                                    </a>
                                    <div class="card-info">
                                        <h5 class="mb-0">{{ number_format($percentLoss,2) }} %
                                        </h5>
                                        <small class="text-muted">Loss Quotation</small>
                                    </div>
                                </div> --}}

                                <p class="fw-medium fs-normal">Percentage On Quotation</p>
                                @php
                                    $percentPO = $quoteSemester ? ($POSemester * 100) / $quoteSemester : 0;
                                    $percentLoss = $quoteSemester ? ($lossSemester * 100) / $quoteSemester : 0;
                                @endphp
                                <div class="d-flex mb-2 gap-2">
                                    <a href="#po">
                                        <div class="avatar">
                                            <div class="avatar-initial bg-label-success rounded">
                                                <i class="mdi mdi-cart-plus mdi-24px"></i>
                                            </div>
                                        </div>
                                    </a>
                                    <div class="card-info">
                                        <h5 class="mb-0">{{ number_format($percentPO, 2) }} %
                                        </h5>
                                        <small class="text-muted">Purchase Order</small>
                                    </div>
                                </div>
                                <div class="d-flex mb-2 gap-2">
                                    <a href="#po">
                                        <div class="avatar">
                                            <div class="avatar-initial bg-label-danger rounded">
                                                <i class="mdi mdi-cart-minus mdi-24px"></i>
                                            </div>
                                        </div>
                                    </a>
                                    <div class="card-info">
                                        <h5 class="mb-0">{{ number_format($percentLoss, 2) }} %
                                        </h5>
                                        <small class="text-muted">Loss Quotation</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="row">
                            <div class="col-12">
                                <h4>{{ $user->name }}</h4>
                            </div>
                            <div class="col-4">
                                <p class="fw-medium fs-normal">Key Performance Indicator</p>
                                <div class="d-flex mb-2 gap-2">
                                    <a href="#quote">
                                        <div class="avatar">
                                            <div class="avatar-initial bg-label-warning rounded">
                                                <i class="mdi mdi-email-multiple-outline mdi-24px"></i>
                                            </div>
                                        </div>
                                    </a>
                                    <div class="card-info">
                                        <h5 class="mb-0">{{ $quoteSemesterProspect }}
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
                                        <h5 class="mb-0">{{ $POSemesterProspect }}
                                        </h5>
                                        <small class="text-muted">Purchase Order</small>
                                    </div>
                                </div>
                                <div class="d-flex mb-2 gap-2">
                                    <a href="#po">
                                        <div class="avatar">
                                            <div class="avatar-initial bg-label-danger rounded">
                                                <i class="mdi mdi-cart-minus mdi-24px"></i>
                                            </div>
                                        </div>
                                    </a>
                                    <div class="card-info">
                                        <h5 class="mb-0">{{ $lossSemesterProspect }}
                                        </h5>
                                        <small class="text-muted">Loss Quotation</small>
                                    </div>
                                </div>
                                <p class="fw-medium fs-normal">Percentage Per Quotation</p>
                                @php
                                    $percentPO = $quoteSemesterProspect
                                        ? ($POSemester * 100) / $quoteSemesterProspect
                                        : 0;
                                    $percentLoss = $quoteSemesterProspect
                                        ? ($lossSemester * 100) / $quoteSemesterProspect
                                        : 0;
                                @endphp
                                <div class="d-flex mb-2 gap-2">
                                    <a href="#po">
                                        <div class="avatar">
                                            <div class="avatar-initial bg-label-success rounded">
                                                <i class="mdi mdi-cart-plus mdi-24px"></i>
                                            </div>
                                        </div>
                                    </a>
                                    <div class="card-info">
                                        <h5 class="mb-0">{{ number_format($percentPO, 2) }} %
                                        </h5>
                                        <small class="text-muted">Purchase Order</small>
                                    </div>
                                </div>
                                <div class="d-flex mb-2 gap-2">
                                    <a href="#po">
                                        <div class="avatar">
                                            <div class="avatar-initial bg-label-danger rounded">
                                                <i class="mdi mdi-cart-minus mdi-24px"></i>
                                            </div>
                                        </div>
                                    </a>
                                    <div class="card-info">
                                        <h5 class="mb-0">{{ number_format($percentLoss, 2) }} %
                                        </h5>
                                        <small class="text-muted">Loss Quotation</small>
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
                                                {{ number_format($totalPOSemesterProspect, 2, ',', '.') }}
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
                                                {{ number_format($totalQuoteSemesterProspect, 2, ',', '.') }}
                                            </h5>
                                            <small class="text-muted">Quotation</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="d-flex mb-2 gap-2">
                                        <div class="avatar">
                                            <div class="avatar-initial bg-label-danger rounded">
                                                <i class="mdi mdi-cart-minus mdi-24px"></i>
                                            </div>
                                        </div>
                                        <div class="card-info">
                                            <h5 class="mb-0">Rp
                                                {{ number_format($totalLossSemesterProspect, 2, ',', '.') }}
                                            </h5>
                                            <small class="text-muted">Loss Quotation</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        @php
            if ($report->semester == '1') {
                $item = 1;
            } else {
                $item = 7;
            }
        @endphp
        @if ($user->role == 'Sales')
            @foreach ($getDC as $DC)
                @php
                    $dateRep = $item . '-' . $report->year;

                @endphp
                <div class="col-lg-6 mb-3">
                    <div class="card" data-id="{{ $item }}">
                        <div class="card-header">
                            <div class="d-flex justify-content-between">
                                <h4 class="mb-2">{{ $DC['month'] }} Overview</h4>
                                <div class="dropdown">
                                    <button class="btn p-0" type="button" id="salesOverview" data-bs-toggle="dropdown"
                                        aria-haspopup="true" aria-expanded="false">
                                        <i class="mdi mdi-dots-vertical mdi-24px"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="salesOverview"
                                        style="">
                                        <a href="{{ route('detail-overview.semester', ['sales' => $user->id, 'date' => $dateRep]) }}"
                                            class="dropdown-item waves-effect">Detail</a>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <h5 class="mb-0 fw-normal">Total Sales <span class="fs-4">Rp
                                        {{ number_format($getTotalPO[$item]['total'], 2, ',', '.') }}</span></h5>
                                @php
                                    $jumlah_target = [];
                                    foreach ($getTotalPO as $key => $value) {
                                        if ($targett != 0) {
                                            $jumlah_target[$key] = ($value['total'] / $targett) * 100;
                                            $formatted_jumlah_target[$key] = number_format($jumlah_target[$key], 3);
                                        } else {
                                            $jumlah_target[$key] = 0;
                                            $formatted_jumlah_target[$key] = number_format($jumlah_target[$key], 3);
                                        }
                                    }
                                @endphp
                                <div class="d-flex align-items-center text-success">
                                    <p class="mb-0"> {{ $formatted_jumlah_target[$item] }}%</p>
                                </div>
                            </div>
                            <div class="d-flex align-items-center">
                                <h5 class="fw-normal">Quotation <span class="fs-4">Rp
                                        {{ number_format($getTotalForecast[$item]['total'], 2, ',', '.') }}</span></h5>
                                {{-- <div class="d-flex align-items-center text-success">
                                <p class="mb-0">+18%</p>
                                <i class="mdi mdi-chevron-up"></i>
                            </div> --}}
                            </div>
                        </div>
                        <div class="card-body d-flex justify-content-between flex-wrap gap-3">
                            <div class="d-flex gap-2">
                                <div class="avatar">
                                    <div class="avatar-initial bg-label-secondary rounded">
                                        <i class="mdi mdi-account-multiple-plus-outline mdi-24px"></i>
                                    </div>
                                </div>
                                <div class="card-info">
                                    <h5 class="mb-0">{{ $getLeads[$item]['total'] }}</h5>
                                    <small class="text-muted">Leads</small>
                                </div>
                            </div>
                            <div class="d-flex gap-2">
                                <div class="avatar">
                                    <div class="avatar-initial bg-label-info rounded">
                                        <i class="mdi mdi-phone-outline mdi-24px"></i>
                                    </div>
                                </div>
                                <div class="card-info">
                                    <h5 class="mb-0">{{ $DC['total'] }}</h5>
                                    <small class="text-muted">Daily Call</small>
                                </div>
                            </div>
                            <div class="d-flex gap-2">
                                <div class="avatar">
                                    <div class="avatar-initial bg-label-primary rounded">
                                        <i class="mdi mdi-account-multiple-outline mdi-24px"></i>
                                    </div>
                                </div>
                                <div class="card-info">
                                    <h5 class="mb-0">{{ $getCRM[$item]['total'] }}</h5>
                                    <small class="text-muted">CRM</small>
                                </div>
                            </div>
                            @if (Auth::user()->detail[0]->area == 'Bekasi' ||
                                    Auth::user()->detail[0]->area == 'Jabodetabek' ||
                                    Auth::user()->detail[0]->area == 'Jawa Barat')
                                <div class="d-flex gap-2">
                                    <div class="avatar">
                                        <div class="avatar-initial bg-label-danger rounded">
                                            <i class="mdi mdi-office-building-marker-outline mdi-24px"></i>
                                        </div>
                                    </div>
                                    <div class="card-info">
                                        <h5 class="mb-0">{{ $getVisit[$item]['total'] }}</h5>
                                        <small class="text-muted">Visit</small>
                                    </div>
                                </div>
                            @endif
                            <div class="d-flex gap-2">
                                <div class="avatar">
                                    <div class="avatar-initial bg-label-warning rounded">
                                        <i class="mdi mdi-email-multiple-outline mdi-24px"></i>
                                    </div>
                                </div>
                                <div class="card-info">
                                    <h5 class="mb-0">{{ $getQuote[$item]['total'] }}</h5>
                                    <small class="text-muted">Quotation</small>
                                </div>
                            </div>
                            <div class="d-flex gap-2">
                                <div class="avatar">
                                    <div class="avatar-initial bg-label-success rounded">
                                        <i class="mdi mdi-cart-plus mdi-24px"></i>
                                    </div>
                                </div>
                                <div class="card-info">
                                    <h5 class="mb-0">{{ $getPO[$item]['total'] }}</h5>
                                    <small class="text-muted">PO</small>
                                </div>
                            </div>
                            <div class="d-flex gap-2">
                                <div class="avatar">
                                    <div class="avatar-initial bg-label-danger rounded">
                                        <i class="mdi mdi-cart-minus mdi-24px"></i>
                                    </div>
                                </div>
                                <div class="card-info">
                                    <h5 class="mb-0">{{ $getLoss[$item]['total'] }}</h5>
                                    <small class="text-muted">Loss</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @include('components.modal.overview.totalPo')
                @php
                    $item++;
                @endphp
            @endforeach
        @else
            @foreach ($getProspect as $prospect)
                @php
                    $dateRep = $item . '-' . $report->year;
                @endphp
                <div class="col-lg-6 mb-3">
                    <div class="card" data-id="{{ $item }}">
                        <div class="card-header">
                            <div class="d-flex justify-content-between">
                                <h4 class="mb-2">{{ $prospect['month'] }} Overview</h4>
                                <div class="dropdown">
                                    <button class="btn p-0" type="button" id="salesOverview" data-bs-toggle="dropdown"
                                        aria-haspopup="true" aria-expanded="false">
                                        <i class="mdi mdi-dots-vertical mdi-24px"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="salesOverview"
                                        style="">
                                        <a href="{{ route('detail-overview.semester', ['sales' => $user->id, 'date' => $dateRep]) }}"
                                            class="dropdown-item waves-effect">Detail</a>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <h5 class="mb-0 fw-normal">Total Sales <span class="fs-4">Rp
                                        {{ number_format($getTotalPOProspect[$item]['total'], 2, ',', '.') }}</span></h5>
                                {{-- @php
                                    $jumlah_target = [];
                                    foreach ($getTotalPO as $key => $value) {
                                        if ($targett != 0) {
                                            $jumlah_target[$key] = ($value['total'] / $targett) * 100;
                                            $formatted_jumlah_target[$key] = number_format($jumlah_target[$key], 3);
                                        } else {
                                            $jumlah_target[$key] = 0;
                                            $formatted_jumlah_target[$key] = number_format($jumlah_target[$key], 3);
                                        }
                                    }
                                @endphp
                                <div class="d-flex align-items-center text-success">
                                    <p class="mb-0"> {{ $formatted_jumlah_target[$item] }}%</p>
                                </div> --}}
                            </div>
                            <div class="d-flex align-items-center">
                                <h5 class="fw-normal">Quotation <span class="fs-4">Rp
                                        {{ number_format($getTotalForecastProspect[$item]['total'], 2, ',', '.') }}</span>
                                </h5>
                                {{-- <div class="d-flex align-items-center text-success">
                                <p class="mb-0">+18%</p>
                                <i class="mdi mdi-chevron-up"></i>
                            </div> --}}
                            </div>
                        </div>
                        <div class="card-body d-flex justify-content-between flex-wrap gap-3">
                            <div class="d-flex gap-2">
                                <div class="avatar">
                                    <div class="avatar-initial bg-label-secondary rounded">
                                        <i class="mdi mdi-account-multiple-plus-outline mdi-24px"></i>
                                    </div>
                                </div>
                                <div class="card-info">
                                    <h5 class="mb-0">{{ $getProspect[$item]['total'] }}</h5>
                                    <small class="text-muted">Prospect</small>
                                </div>
                            </div>
                            <div class="d-flex gap-2">
                                <div class="avatar">
                                    <div class="avatar-initial bg-label-info rounded">
                                        <i class="mdi mdi-phone-outline mdi-24px"></i>
                                    </div>
                                </div>
                                <div class="card-info">
                                    <h5 class="mb-0">{{ $getProspectProvide[$item]['total'] }}</h5>
                                    <small class="text-muted">Provide</small>
                                </div>
                            </div>
                            <div class="d-flex gap-2">
                                <div class="avatar">
                                    <div class="avatar-initial bg-label-warning rounded">
                                        <i class="mdi mdi-email-multiple-outline mdi-24px"></i>
                                    </div>
                                </div>
                                <div class="card-info">
                                    <h5 class="mb-0">{{ $getQuoteProspect[$item]['total'] }}</h5>
                                    <small class="text-muted">Quotation</small>
                                </div>
                            </div>
                            <div class="d-flex gap-2">
                                <div class="avatar">
                                    <div class="avatar-initial bg-label-success rounded">
                                        <i class="mdi mdi-cart-plus mdi-24px"></i>
                                    </div>
                                </div>
                                <div class="card-info">
                                    <h5 class="mb-0">{{ $getPOProspect[$item]['total'] }}</h5>
                                    <small class="text-muted">PO</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @include('components.modal.overview.totalPo')
                @php
                    $item++;
                @endphp
            @endforeach
        @endif
    </div>
@endsection
