@extends('layouts.sales.app')
@section('title', 'Overview Sales')
@section('content')
    <h4 class="fw-bold py-3 mb-4">
        Overview Semester {{ $report->semester }}, {{ $report->year }}
    </h4>
    <div class="row">
        @php
            if ($report->semester == '1') {
                $item = 1;
            } else {
                $item = 7;
            }

        @endphp
        @foreach ($getDC as $DC)
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
                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="salesOverview" style="">
                                    <a class="dropdown-item waves-effect" data-bs-toggle="modal"
                                        data-bs-target="#overviewPO{{ $DC['monthKey'] }}">Detail</a>
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
                                <small
                                    class="text-muted">{{ Auth::user()->id == '1' ? 'New Leads' : 'Daily Call' }}</small>
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
                    </div>
                </div>
            </div>
            @include('components.modal.overview.totalPo')
            @php
                $item++;
            @endphp
        @endforeach
    </div>
@endsection
