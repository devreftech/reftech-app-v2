@extends('layouts.sales.app')
@section('title', 'Overview Sales')
@section('content')
    <div class="row">
        @php
            $item = 0;
        @endphp
        @foreach ($sales as $sale)
            <div class="col-lg-6 mb-3">
                <div class="card" data-id="{{ $item }}">
                    <div class="card-header">
                        <div class="d-flex justify-content-between">
                            <h4 class="mb-2">{{ $sale->name }}</h4>
                            <div class="dropdown">
                                <button class="btn p-0" type="button" id="salesOverview" data-bs-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false">
                                    <i class="mdi mdi-dots-vertical mdi-24px"></i>
                                </button>
                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="salesOverview" style="">
                                    <a class="dropdown-item waves-effect" href="javascript:void(0);">Refresh</a>
                                    <a class="dropdown-item waves-effect" href="javascript:void(0);">Share</a>
                                    <a class="dropdown-item waves-effect" href="javascript:void(0);">Update</a>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <h5 class="mb-0 fw-normal">Total Sales <span class="fs-4">Rp
                                    {{ number_format($totalPO[$item], 2, ',', '.') }}</span></h5>
                            @php
                                $jumlah_target = [];
                                foreach ($totalPO as $key => $value) {
                                    if (isset($targett[$key]) && $targett[$key] != 0) {
                                        $jumlah_target[$key] = ($value / $targett[$key]) * 100;
                                        $formatted_jumlah_target[$key] = number_format($jumlah_target[$key], 3);
                                    } else {
                                        $jumlah_target[$key] = 0;
                                    }
                                }
                            @endphp
                            <div class="d-flex align-items-center text-success">
                                <p class="mb-0"> {{ $formatted_jumlah_target[$item] }}%</p>
                            </div>
                        </div>
                        <div class="d-flex align-items-center">
                            <h5 class="fw-normal">Forecast <span class="fs-4">Rp {{ $totalForecast[$item] }}</span></h5>
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
                                <h5 class="mb-0">{{ $filteredLeads[$item] }}</h5>
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
                                <h5 class="mb-0">{{ $filteredDC[$item] }}</h5>
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
                                <h5 class="mb-0">{{ $filteredCRM[$item] }}</h5>
                                <small class="text-muted">CRM</small>
                            </div>
                        </div>
                        <div class="d-flex gap-2">
                            <div class="avatar">
                                <div class="avatar-initial bg-label-warning rounded">
                                    <i class="mdi mdi-email-multiple-outline mdi-24px"></i>
                                </div>
                            </div>
                            <div class="card-info">
                                <h5 class="mb-0">{{ $filteredQuote[$item] }}</h5>
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
                                <h5 class="mb-0">{{ $filteredPO[$item] }}</h5>
                                <small class="text-muted">PO</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @php
                $item++;
            @endphp
        @endforeach
    </div>
@endsection
