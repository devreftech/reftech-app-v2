@extends('layouts.sales.app')
@section('title', 'My Dashboard')
@section('content')
    @if (Auth::user()->role == 'Sales')
        @if (Auth::user()->id == 16 || Auth::user()->id == 23)
            <div class="row gy-4 mb-4">
                <!-- Congratulations card -->
                <div class="col-md-3 col-12">
                    <div class="card h-100">
                        <div class="card-body text-nowrap">
                            <h4 class="card-title mb-1 d-flex gap-2 flex-wrap">
                                Rise to the Top!
                                <strong>{{ Auth::user()->name }}</strong>🎉
                            </h4>
                            <p class="pb-0">Keep closing and lead the leaderboard.</p>
                            <h4 class="text-primary mb-1">Rp. {{ $formattedTotalPrice }}</h4>
                            @php
                                $jumlah_target = 0;
                                $jumlah_target = ($target?->total > 0) ? ($poTotalPrice / $target->total) * 100 : 0;
                                $formatted_jumlah_target = number_format($jumlah_target, 3);
                            @endphp
                            <p class="mb-2 pb-1">{{ $formatted_jumlah_target }}% of target 🚀</p>
                            <a href="javascript:;" class="btn btn-sm btn-primary waves-effect waves-light">View Sales</a>
                        </div>
                        <img src="{{ asset('assets') }}/img/illustrations/trophy.png"
                            class="position-absolute bottom-0 end-0 me-3" height="140" alt="view sales">
                    </div>
                </div>
                <!--/ Congratulations card -->
                <div class="col-md-9 col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5>Key Performance Indicator</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4 col-12">
                                    <div class="row mb-3">
                                        <div class="col-4" style="padding-right: 0;">
                                            <div class="card bg-primary text-white w-100 cursor-pointer"
                                                data-bs-toggle="modal" data-bs-target="#newProduct">
                                                <h5 class="card-title text-white text-center my-4">
                                                    <i class="menu-icon tf-icons mdi mdi-reproduction m-0 fs-1"></i>
                                                </h5>
                                            </div>
                                        </div>
                                        <div class="col-8 d-flex flex-column justify-content-between">
                                            <div class="card shadow-none bg-label-primary border-primary border-2">
                                                <h5 class="card-title text-center my-2">
                                                    New Product
                                                </h5>
                                            </div>
                                            <div class="card shadow-none bg-label-primary border-primary border-2 mt-auto"
                                                style="border: dashed;">
                                                <h5 class="card-title text-center my-2">
                                                    {{ $productCount }} / 100
                                                </h5>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-4" style="padding-right: 0;">
                                            <div class="card bg-warning text-white w-100 cursor-pointer"
                                                data-bs-toggle="modal" data-bs-target="#accurData">
                                                <h5 class="card-title text-white text-center my-4">
                                                    <i
                                                        class="menu-icon tf-icons mdi mdi-package-variant-closed-check m-0 fs-1"></i>
                                                </h5>
                                            </div>
                                        </div>
                                        <div class="col-8 d-flex flex-column justify-content-between">
                                            <div class="card shadow-none bg-label-warning border-warning border-2">
                                                <h5 class="card-title text-center my-2">
                                                    Akurasi Data
                                                </h5>
                                            </div>
                                            <div class="card shadow-none bg-label-warning border-warning border-2 mt-auto"
                                                style="border: dashed;">
                                                <h5 class="card-title text-center my-2">
                                                    @if (@$akurasiCount[0])
                                                        @php
                                                            $dataAkurasi = $akurasiCount->count();
                                                            $persenAkurasi = 0;
                                                            $jumlahAkurasi = 0;
                                                        @endphp
                                                        @foreach ($akurasiCount as $item)
                                                            @php
                                                                $jumlahAkurasi += $item->average;
                                                            @endphp
                                                        @endforeach
                                                        @php
                                                            $jumlahAkurasi / $dataAkurasi;
                                                            $persenAkurasi = ($jumlahAkurasi / 5) * 100;
                                                        @endphp
                                                    @endif
                                                    {{ @$persenAkurasi ?? 0 }} %
                                                </h5>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-3 align-items-stretch cursor-pointer">
                                        <div class="col-4" style="padding-right: 0;">
                                            <div class="card border-warning bg-warning border-1 w-100 h-100"
                                                data-bs-toggle="modal" data-bs-target="#delivery">
                                                <h5 class="card-title text-center text-white my-4">
                                                    <i
                                                        class="menu-icon tf-icons mdi mdi-truck-delivery-outline m-0 fs-1"></i>
                                                </h5>
                                            </div>
                                        </div>

                                        <div class="col-8 d-flex flex-column justify-content-between">
                                            <div class="card bg-label-warning border-warning border-1 shadow-none">
                                                <h5 class="card-title text-center my-2">
                                                    Delivery & Success
                                                </h5>
                                            </div>
                                            <div class="card bg-label-warning border-warning border-2 shadow-none mt-auto"
                                                style="border-style: dashed;">
                                                <h5 class="card-title text-center my-2">
                                                    @if (@$deliveryCount[0])
                                                        @php
                                                            $dataDelivery = $deliveryCount->count();
                                                            $persenDelivery = 0;
                                                            $jumlahDelivery = 0;
                                                        @endphp
                                                        @foreach ($deliveryCount as $item)
                                                            @php
                                                                $jumlahDelivery += $item->average;
                                                            @endphp
                                                        @endforeach
                                                        @php
                                                            $jumlahDelivery / $dataDelivery;
                                                            $persenDelivery = ($jumlahDelivery / 5) * 100;
                                                        @endphp
                                                    @endif
                                                    {{ @$persenDelivery ?? 0 }} %
                                                </h5>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 col-12">
                                    <div class="row mb-3">
                                        <div class="col-4" style="padding-right: 0;">
                                            <div class="card bg-warning text-white w-100 cursor-pointer"
                                                data-bs-toggle="modal" data-bs-target="#response">
                                                <h5 class="card-title text-white text-center my-4">
                                                    <i
                                                        class="menu-icon tf-icons mdi mdi-account-heart-outline m-0 fs-1"></i>
                                                </h5>
                                            </div>
                                        </div>
                                        <div class="col-8 d-flex flex-column justify-content-between">
                                            <div class="card shadow-none bg-label-warning border-warning border-2">
                                                <h5 class="card-title text-center my-2">
                                                    Response Chat
                                                </h5>
                                            </div>
                                            <div class="card shadow-none bg-label-warning border-warning border-2 mt-auto"
                                                style="border: dashed;">
                                                <h5 class="card-title text-center my-2">
                                                    @if (@$responseCount[0])
                                                        @php
                                                            $dataResponse = $responseCount->count();
                                                            $persenResponse = 0;
                                                            $jumlahResponse = 0;
                                                        @endphp
                                                        @foreach ($responseCount as $item)
                                                            @php
                                                                $jumlahResponse += $item->average;
                                                            @endphp
                                                        @endforeach
                                                        @php

                                                            $persenResponse = $jumlahResponse / $dataResponse;
                                                        @endphp
                                                    @endif
                                                    {{ @$persenResponse ?? 0 }} %
                                                </h5>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-4" style="padding-right: 0;">
                                            <div class="card bg-warning text-white w-100 cursor-pointer"
                                                data-bs-toggle="modal" data-bs-target="#rating">
                                                <h5 class="card-title text-white text-center my-4">
                                                    <i class="menu-icon tf-icons mdi mdi-monitor-star m-0 fs-1"></i>
                                                </h5>
                                            </div>
                                        </div>
                                        <div class="col-8 d-flex flex-column justify-content-between">
                                            <div class="card shadow-none bg-label-warning border-warning border-2">
                                                <h5 class="card-title text-center my-2">
                                                    Score Toko
                                                </h5>
                                            </div>
                                            <div class="card shadow-none bg-label-warning border-warning border-2 mt-auto"
                                                style="border: dashed;">
                                                <h5 class="card-title text-center my-2">
                                                    @if (@$ratingCount[0])
                                                        @php
                                                            $dataRating = $ratingCount->count();
                                                            $persenRating = 0;
                                                            $jumlahRating = 0;
                                                        @endphp
                                                        @foreach ($ratingCount as $item)
                                                            @php
                                                                $jumlahRating += $item->average;
                                                            @endphp
                                                        @endforeach
                                                        @php
                                                            $persenRating = $jumlahRating / $dataRating;
                                                        @endphp
                                                    @endif
                                                    Rating {{ @$persenRating ?? 0 }}
                                                </h5>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-3 align-items-stretch">
                                        <div class="col-4" style="padding-right: 0;">
                                            <div class="card bg-warning border-warning text-white border-1 w-100 h-100 cursor-pointer"
                                                data-bs-toggle="modal" data-bs-target="#customer">
                                                <h5 class="card-title text-white text-center my-4">
                                                    <i class="menu-icon tf-icons mdi mdi-cart-check m-0 fs-1"></i>
                                                </h5>
                                            </div>
                                        </div>

                                        <div class="col-8 d-flex flex-column justify-content-between">
                                            <div class="card bg-label-warning border-warning border-2 shadow-none">
                                                <h5 class="card-title text-center my-2">
                                                    Customer Care
                                                </h5>
                                            </div>
                                            <div class="card bg-label-warning border-warning border-2 shadow-none mt-auto"
                                                style="border-style: dashed;">
                                                <h5 class="card-title text-center my-2">
                                                    @if (@$customerCount[0])
                                                        @php
                                                            $dataCustomer = $customerCount->count();
                                                            $persenCustomer = 0;
                                                            $jumlahCustomer = 0;
                                                        @endphp
                                                        @foreach ($customerCount as $item)
                                                            @php
                                                                $jumlahCustomer += $item->average;
                                                            @endphp
                                                        @endforeach
                                                        @php
                                                            $jumlahCustomer / $dataCustomer;
                                                            $persenCustomer = ($jumlahCustomer / 5) * 100;
                                                        @endphp
                                                    @endif
                                                    {{ @$persenCustomer ?? 0 }} %
                                                </h5>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 col-12">
                                    <div class="row mb-3">
                                        <div class="col-4" style="padding-right: 0;">
                                            <div class="card bg-primary text-white w-100 cursor-pointer"
                                                data-bs-toggle="modal" data-bs-target="#SWin">
                                                <h5 class="card-title text-white text-center my-4">
                                                    <i class="menu-icon tf-icons mdi mdi-whatsapp m-0 fs-1"></i>
                                                </h5>
                                            </div>
                                        </div>
                                        <div class="col-8 d-flex flex-column justify-content-between">
                                            <div class="card shadow-none bg-label-primary border-primary border-2">
                                                <h5 class="card-title text-center my-2">
                                                    Update SW (3/Days)
                                                </h5>
                                            </div>
                                            <div class="card shadow-none bg-label-primary border-primary border-2 mt-auto"
                                                style="border: dashed;">
                                                <h5 class="card-title text-center my-2">
                                                    @if (@$SWCount[0])
                                                        @php
                                                            $dataSW = $SWCount->count();
                                                            $persenSW = 0;
                                                            $jumlahSW = 0;
                                                        @endphp
                                                        @foreach ($SWCount as $item)
                                                            @php
                                                                $jumlahSW += $item->airend;
                                                                $jumlahSW += $item->kojisha;
                                                            @endphp
                                                        @endforeach
                                                        @php
                                                            $persenSW = $jumlahSW / $dataSW;
                                                        @endphp
                                                    @endif
                                                    {{ @$persenSW ?? 0 }} / {{ Auth::user()->id == 16 ? '120' : '60' }}
                                                </h5>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-4" style="padding-right: 0;">
                                            <div class="card bg-primary text-white w-100 cursor-pointer"
                                                data-bs-toggle="modal" data-bs-target="#video">
                                                <h5 class="card-title text-white text-center my-4">
                                                    <i class="menu-icon tf-icons mdi mdi-video-outline m-0 fs-1"></i>
                                                </h5>
                                            </div>
                                        </div>
                                        <div class="col-8 d-flex flex-column justify-content-between">
                                            <div class="card shadow-none bg-label-primary border-primary border-2">
                                                <h5 class="card-title text-center my-2">
                                                    Video ( 1/Days )
                                                </h5>
                                            </div>
                                            <div class="card shadow-none bg-label-primary border-primary border-2 mt-auto"
                                                style="border: dashed;">
                                                <h5 class="card-title text-center my-2">
                                                    @if (@$videoCount[0])
                                                        @php
                                                            $dataVideo = $videoCount->count();
                                                            $persenVideo = 0;
                                                            $jumlahVideo = 0;
                                                        @endphp
                                                        @foreach ($videoCount as $item)
                                                            @php
                                                                if ($item->ig) {
                                                                    $jumlahVideo += 30;
                                                                }
                                                                if ($item->tiktok) {
                                                                    $jumlahVideo += 30;
                                                                }
                                                                if ($item->tokped) {
                                                                    $jumlahVideo += 40;
                                                                }
                                                            @endphp
                                                        @endforeach
                                                        @php
                                                            $persenVideo = $jumlahVideo / $dataVideo;
                                                        @endphp
                                                    @endif
                                                    {{ @$persenVideo ?? 0 }} %
                                                </h5>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-3 align-items-stretch">
                                        <div class="col-4" style="padding-right: 0;">
                                            <div class="card border-success bg-success border-1 w-100 h-100">
                                                <h5 class="card-title text-white text-center my-4">
                                                    <i class="menu-icon tf-icons mdi mdi-cart-plus  m-0 fs-1"></i>
                                                </h5>
                                            </div>
                                        </div>

                                        <div class="col-8 d-flex flex-column justify-content-between">
                                            <div class="card border-success bg-label-success border-1 shadow-none">
                                                <h5 class="card-title text-center my-2">
                                                    Purchase Order
                                                </h5>
                                            </div>
                                            <div class="card border-success bg-label-success border-2 shadow-none mt-auto"
                                                style="border-style: dashed;">
                                                <h5 class="card-title text-center my-2">
                                                    {{ $POCount }}
                                                </h5>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @php
                $salesID = Auth::user()->id;
            @endphp
            @include('components.modal.onlineSales.new-product')
            @include('components.modal.onlineSales.akurasi-data')
            @include('components.modal.onlineSales.delivery')
            @include('components.modal.onlineSales.response')
            @include('components.modal.onlineSales.rating')
            @include('components.modal.onlineSales.customer')
            @include('components.modal.onlineSales.sw')
            @include('components.modal.onlineSales.video')
        @endif
        <div class="row gy-4 mb-4">
            <!-- Congratulations card -->
            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                <div class="card h-100">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h4 class="card-title m-0 me-2">Rank Sales Team 🏆</h4>
                    </div>
                    <div class="card-body">
                        <ul class="p-0 m-0">
                            <li class="d-flex mb-3 justify-content-between pb-2">
                                <h6 class="mb-0 small"></h6>
                                <h6 class="mb-0 small"></h6>
                            </li>
                            <hr>
                            @php
                                $no = 1;
                            @endphp
                            @foreach ($sorted as $sale)
                                @php
                                    switch ($no) {
                                        case 1:
                                            $color = 'warning'; // Kuning / Orange
                                            break;
                                        case 2:
                                            $color = 'success'; // Hijau
                                            break;
                                        case 3:
                                            $color = 'info'; // Biru
                                            break;
                                        case 4:
                                            $color = 'secondary'; // Abu-abu
                                            break;
                                        case 5:
                                            $color = 'primary'; // custom (kalau ada)
                                            break;
                                        case 6:
                                            $color = 'danger'; // Merah
                                            break;
                                        case 7:
                                            $color = 'dark'; // Hitam
                                            break;
                                        default:
                                            $color = 'primary';
                                            break;
                                    }
                                @endphp
                                <li class="d-flex mb-4">
                                    <span style="font-size:16px;"
                                        class="badge bg-label-{{ $color }} d-inline-flex align-items-center justify-content-center "><b>
                                        # {{ $no }}</b>
                                    </span>
                                    <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                        <div class="mx-2 me-2">
                                            <h6 class="mb-0">{{ $sale['name'] }}
                                                @if ($no == 1)
                                                    <span class="text-warning">
                                                        <i class="mdi mdi-crown mdi-24px"></i>
                                                    </span>
                                                @endif
                                            </h6>
                                            <small class="text-muted">{{ $sale['area'] }}</small>
                                        </div>
                                        <div style="font-size:16px" class="badge bg-label-{{ $color }} rounded-pill">
                                            {{ $sale['percentage'] }}%
                                        </div>
                                    </div>
                                </li>
                                @php
                                    $no++;
                                @endphp
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-xl-8 col-lg-8 col-md-8 col-sm-12">
                <div class="card">
                    <div class="card-body text-nowrap">
                        <h4 class="card-title mb-1 d-flex gap-2 flex-wrap">
                            Rise to the Top! <strong>{{ Auth::user()->name }}</strong>🎉
                        </h4>
                        <p class="pb-0">Keep closing and lead the leaderboard.</p>
                        <h4 class="text-primary mb-1">Rp. {{ $formattedTotalPrice }}</h4>
                        @php
                            $jumlah_target = 0;
                            $jumlah_target = ($target?->total > 0) ? ($poTotalPrice / $target->total) * 100 : 0;
                            $formatted_jumlah_target = number_format($jumlah_target, 3);
                        @endphp
                        <p class="mb-2 pb-1">{{ $formatted_jumlah_target }}% of target 🚀</p>
                        <a href="https://reftech.my.id/reports" class="btn btn-sm btn-primary waves-effect waves-light">View Sales</a>
                    </div>
                    <img src="{{ asset('assets') }}/img/illustrations/trophy.png"
                        class="position-absolute bottom-0 end-0 me-3" height="140" alt="view sales">
                </div>

                        <h4 class="card-title mb-1 d-flex gap-2 flex-wrap text-primary" style="margin-top:25px">
                            <b>Key Performance Indicator</b>
                        </h4>


                @if (Auth::user()->id != 16 || Auth::user()->id != 23)
            <div class="row gy-4 mb-4" style="margin-top:1px">
                @if (Auth::user()->id != '4')
                    <div class="col">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                                    <div class="avatar">
                                        <div class="avatar-initial bg-label-secondary rounded">
                                            <i class="mdi mdi-account-multiple-plus-outline mdi-24px"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-info mt-4 pt-1">
                                    <h4 class="mb-2">
                                        {{ $leads->count() }}

                                        <small class="text-muted fs-tiny">/
                                            {{-- @if ($jumlahData > 4)
                                        {{ round($target->leads + $target->leads / 4) }}
                                    @elseif($jumlahData == 4)
                                        {{ round(num: $target->leads) }}
                                    @endif --}}

                                            {{ $target?->leads ?? 0 }}
                                        </small>
                                    </h4>
                                    <div class="badge bg-label-secondary rounded-pill">+New Leads</div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                <!--/ Total New Leads chart -->
                <!-- Total Leads -->
                <div class="col">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                                <div class="avatar">
                                    <div class="avatar-initial bg-label-info rounded">
                                        <i class="mdi mdi-phone-outline mdi-24px"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="card-info mt-4 pt-1">
                                <h4 class="mb-2">
                                    {{ $dailyCall }}
                                    @if (Auth::user()->id != 3 && Auth::user()->id != 4)
                                        <small class="text-muted fs-tiny">/
                                            {{ $target?->dc ?? 0 }}
                                            @php
                                                if (is_array($weekPerMonth)) {
                                                    $jumlahData = count($weekPerMonth);
                                                }
                                            @endphp
                                        </small>
                                    @endif
                                </h4>
                                <div class="badge bg-label-secondary rounded-pill">Daily Call</div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--/ Total Leads -->
                <!-- Total Expenses -->
                <div class="col">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                                <div class="avatar">
                                    <div class="avatar-initial bg-label-primary rounded">
                                        <i class="mdi mdi-account-multiple-outline mdi-24px"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="card-info mt-4 pt-1">
                                <h4 class="mb-2">
                                    {{ $customers }}
                                    <small class="text-muted fs-tiny">/
                                        {{ $jumlahCustomer }}
                                        {{-- @if ($jumlahData > 4)
                                        {{ round($target->crm + $target->crm / 4) }}
                                    @elseif($jumlahData == 4)
                                        {{ round($target->crm) }}
                                    @endif --}}
                                    </small>
                                </h4>
                                <div class="badge bg-label-secondary rounded-pill">CRM Existing</div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--/ Total Expenses -->
                <!-- Total Profit chart -->
                <div class="col">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                                <div class="avatar">
                                    <div class="avatar-initial bg-label-warning rounded">
                                        <i class="mdi mdi-email-multiple-outline mdi-24px"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="card-info mt-4 pt-1">
                                <h4 class="mb-2">
                                    {{ $quotation->count() }}
                                    {{-- <small class="text-muted fs-tiny">/
                                        {{ $target->quote }}
                                    </small> --}}
                                </h4>
                                <div class="badge bg-label-secondary rounded-pill">Quotation</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                                <div class="avatar">
                                    <div class="avatar-initial bg-label-success rounded">
                                        <i class="mdi mdi-cart-plus mdi-24px"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="card-info mt-4 pt-1">
                                <h4 class="mb-2">
                                    {{ $po->count() }}
                                </h4>
                                <div class="badge bg-label-secondary rounded-pill">Purchase Order</div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--/ Total Profit chart -->
                {{-- @endif --}}
            </div>
                @endif
            </div>

            <!--/ Congratulations card -->
            <!-- Total New Leads chart -->
        </div>

        <div class="row gy-4 mb-4">
            {{-- Prospect Table --}}
            <div class="col-12">
                <div class="card">
                    <div class="card-datatable table-responsive pt-0">
                        <table class="datatable-prospect-quote-sales table table-striped">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th>ID</th>
                                    <th>Company</th>
                                    <th>Title</th>
                                    <th>Price</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
            {{-- End:: Prospect Table --}}
        </div>

        {{-- <div class="card mb-4">
            <div class="card-datatable table-responsive pt-0">
                <table class="datatable-notulen table table-striped">
                    <thead>
                        <tr>
                            <th></th>
                            <th></th>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Desc</th>
                            <th>Level</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div> --}}


        <div class="card app-calendar-wrapper">
            <div class="row gy-4">
                <!-- Calendar Sidebar -->
                <div class="col app-calendar-sidebar pt-1" id="app-calendar-sidebar">
                    <div class="p-3 pb-2 my-sm-0 mb-3">
                        <div class="d-grid">
                            <button class="btn btn-primary btn-toggle-sidebar" data-bs-toggle="offcanvas"
                                data-bs-target="#addEventSidebar" aria-controls="addEventSidebar">
                                <i class="mdi mdi-plus me-1"></i>
                                <span class="align-middle">Add Event</span>
                            </button>
                        </div>
                    </div>
                    <div class="p-4">
                        <!-- inline calendar (flatpicker) -->
                        <div class="inline-calendar"></div>

                        <hr class="container-m-nx my-4" />

                        <!-- Filter -->
                        <div class="mb-4">
                            <small class="text-small text-muted text-uppercase align-middle">Filter</small>
                        </div>

                        <div class="form-check form-check-secondary mb-3">
                            <input class="form-check-input select-all" type="checkbox" id="selectAll" data-value="all"
                                checked />
                            <label class="form-check-label" for="selectAll">View All</label>
                        </div>

                        <div class="app-calendar-events-filter">
                            <div class="form-check form-check-primary mb-3">
                                <input class="form-check-input input-filter" type="checkbox" id="select-business"
                                    data-value="Business" checked />
                                <label class="form-check-label" for="select-business">Leads</label>
                            </div>
                            <div class="form-check form-check-warning mb-3">
                                <input class="form-check-input input-filter" type="checkbox" id="select-holiday"
                                    data-value="Holiday" checked />
                                <label class="form-check-label" for="select-holiday">Customers</label>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /Calendar Sidebar -->

                <!-- Calendar & Modal -->
                <div class="col app-calendar-content">
                    <div class="card shadow-none border-0 border-start rounded-0">
                        <div class="card-body pb-0">
                            <!-- FullCalendar -->
                            <div id="calendar"></div>
                        </div>
                    </div>
                    <div class="app-overlay"></div>
                    <!-- FullCalendar Offcanvas -->
                    <div class="offcanvas offcanvas-end event-sidebar" tabindex="-1" id="addEventSidebar"
                        aria-labelledby="addEventSidebarLabel">
                        <div class="offcanvas-header">
                            <h5 class="offcanvas-title" id="addEventSidebarLabel">Add Event</h5>
                            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"
                                aria-label="Close"></button>
                        </div>
                        <div class="offcanvas-body">
                            <form class="event-form pt-0" id="eventForm" onsubmit="return false">
                                {{-- <div class="form-floating form-floating-outline mb-4">
                                    <input type="text" class="form-control" id="eventTitle" name="eventTitle"
                                        placeholder="Event Title" />
                                    <label for="eventTitle">Client</label>
                                </div> --}}
                                <div class="form-floating form-floating-outline mb-4 select2-primary">
                                    <select class="select2 select-event-guests form-select" id="eventClient"
                                        name="eventGuests">
                                        @foreach ($clients as $client)
                                            {{-- data-avatar="1.png" --}}
                                            <option value="{{ $client->id }}">{{ $client->company }}</option>
                                        @endforeach
                                    </select>
                                    <label for="eventGuests">Client</label>
                                </div>
                                <div class="form-floating form-floating-outline mb-4">
                                    <input type="text" class="form-control" id="eventStartDate" name="eventStartDate"
                                        placeholder="Start Date" />
                                    <label for="eventStartDate">Date</label>
                                </div>
                                <div class="form-floating form-floating-outline mb-4">
                                    <input type="text" class="form-control" id="eventEndDate" name="eventEndDate"
                                        placeholder="End Date" />
                                    <label for="eventEndDate">Follow Up Date</label>
                                </div>
                                <div class="form-floating form-floating-outline mb-4">
                                    <select class="form-select" id="selectAction" aria-label="Default select example"
                                        name="action">
                                        <option disabled>----- Choose Action -----</option>
                                        <option value="Phone Office">Phone Office</option>
                                        <option value="WhatsApp">WhatsApp</option>
                                    </select>
                                    <label for="selectAction">Action</label>
                                </div>
                                <div class="form-floating form-floating-outline mb-4">
                                    <select class="form-select" id="selectStatus" aria-label="Default select example"
                                        name="status">
                                        <option disabled>----- Choose Status -----</option>
                                        <option value="Responded">Responded</option>
                                        <option value="Not Respon">Not Responded</option>
                                    </select>
                                    <label for="selectStatus">Status</label>
                                </div>
                                <div class="form-floating form-floating-outline mb-4">
                                    <select class="form-select" id="selectIssue" aria-label="Default select example"
                                        name="issues">
                                        @foreach ($issue as $issues)
                                            <option value="{{ $issues->id }}">{{ $issues->issue }}</option>
                                        @endforeach
                                    </select>
                                    <label for="selectIssue">Status</label>
                                </div>
                                <div class="form-floating form-floating-outline mb-4">
                                    <textarea class="form-control" name="eventNote" id="eventNote"></textarea>
                                    <label for="eventNote">Note</label>
                                </div>
                                <input class="form-control" type="text" name="eventComp" id="eventComp"
                                    value="" hidden>
                                <div class="form-floating mb-4">
                                    <p id="eventNoteBefore"></p>
                                </div>
                                {{-- <div class="form-floating form-floating-outline mb-4">
                                    <input type="url" class="form-control" id="eventURL" name="eventURL"
                                        placeholder="https://www.google.com" />
                                    <label for="eventURL">Event URL</label>
                                </div>
                                <div class="form-floating form-floating-outline mb-4">
                                    <input type="text" class="form-control" id="eventLocation" name="eventLocation"
                                        placeholder="Enter Location" />
                                    <label for="eventLocation">Location</label>
                                </div> --}}
                                <div class="mb-3 d-flex justify-content-sm-between justify-content-start my-4 gap-2">
                                    <div class="d-flex">
                                        <button type="submit"
                                            class="btn btn-primary btn-add-event me-sm-2 me-1">Add</button>
                                        <button type="reset" class="btn btn-label-secondary btn-cancel me-sm-0 me-1"
                                            data-bs-dismiss="offcanvas">
                                            Cancel
                                        </button>
                                    </div>
                                    {{-- <button class="btn btn-label-danger btn-delete-event d-none">Delete</button> --}}
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- /Calendar & Modal -->
            </div>

        </div>

        @foreach ($prospects as $prospect)
            @include('components.modal.prospect.confirm')
        @endforeach


        <!-- Prospect Dashboard -->
    @elseif (Auth::user()->role == 'Support')
        <div class="row g-4 mb-4">
            <!-- Monthly Achievement -->
            <div class="col-12 col-xl-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h4 class="card-title mb-1">Bebenangan Prospek Sasih Ieu 🎉</h4>
                        <p class="text-muted mb-2">{{ now()->format('F Y') }}</p>
                        <h4 class="text-primary mb-2">{{ $prospect }} Prospek</h4>
                        @php
                            $progress  = $targetProspect > 0 ? round(($prospect / $targetProspect) * 100, 1) : 0;
                            $remaining = $targetProspect - $prospect;
                        @endphp
                        <p class="mb-2">
                            Targetna: {{ $targetProspect }} | {{ $progress }}% bebenangan
                            ({{ $remaining > 0 ? 'sesa ' . $remaining . ' deui' : 'Alhamdulillah Rengse 🎯' }})
                        </p>
                        <div class="progress" style="height:6px;">
                            <div class="progress-bar {{ $progress >= 80 ? 'bg-success' : ($progress >= 50 ? 'bg-primary' : 'bg-warning') }}"
                                 role="progressbar" style="width: {{ $progress }}%;"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- KPI GRID -->
            @if (Auth::user()->id != '4')
                <!-- Prospect Masuk -->
                <div class="col-6 col-md-4 col-xl d-flex">
                    <div class="card w-100 h-100">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <div class="avatar">
                                    <div class="avatar-initial bg-label-secondary rounded">
                                        <i class="mdi mdi-account-multiple-plus-outline mdi-24px"></i>
                                    </div>
                                </div>
                                <h3 class="mb-0 fw-semibold">{{ $prospect }}</h3>
                            </div>
                            <div class="text-muted small mb-2">
                                {{ $diffProspect >= 0 ? '+' . $diffProspect : $diffProspect }} ti sasih kamari
                            </div>
                            <div class="mt-auto">
                                <span class="badge bg-label-secondary rounded-pill">Prospek Anu Lebet</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Provided -->
                <div class="col-6 col-md-4 col-xl d-flex">
                    <div class="card w-100 h-100">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <div class="avatar">
                                    <div class="avatar-initial bg-label-info rounded">
                                        <i class="mdi mdi-phone-outline mdi-24px"></i>
                                    </div>
                                </div>
                                <h3 class="mb-0 fw-semibold">{{ $provided }}</h3>
                            </div>
                            <div class="text-muted small mb-2">
                                {{ $prospect > 0 ? round(($provided / $prospect) * 100, 1) : 0 }}% ti prospek
                            </div>
                            <div class="mt-auto">
                                <span class="badge bg-label-secondary rounded-pill">Anu Diladangan</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quotation -->
                <div class="col-6 col-md-4 col-xl d-flex">
                    <div class="card w-100 h-100">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <div class="avatar">
                                    <div class="avatar-initial bg-label-warning rounded">
                                        <i class="mdi mdi-email-multiple-outline mdi-24px"></i>
                                    </div>
                                </div>
                                <h3 class="mb-0 fw-semibold">{{ $quotation }}</h3>
                            </div>
                            <div class="text-muted small mb-2">
                                {{ $prospect > 0 ? round(($quotation / $prospect) * 100, 1) : 0 }}% ti anu diladangan
                            </div>
                            <div class="mt-auto">
                                <span class="badge bg-label-secondary rounded-pill">Anu Ditawaran</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Purchase Order -->
                <div class="col-6 col-md-4 col-xl d-flex">
                    <div class="card w-100 h-100">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <div class="avatar">
                                    <div class="avatar-initial bg-label-success rounded">
                                        <i class="mdi mdi-cart-plus mdi-24px"></i>
                                    </div>
                                </div>
                                <h3 class="mb-0 fw-semibold">{{ $po }}</h3>
                            </div>
                            <div class="text-muted small mb-2">
                                {{ $closingRate }}% ti anu ditawaran
                            </div>
                            <div class="mt-auto">
                                <span class="badge bg-label-secondary rounded-pill">Anu Jadi Meser</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Loss -->
                <div class="col-6 col-md-4 col-xl d-flex">
                    <div class="card w-100 h-100">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <div class="avatar">
                                    <div class="avatar-initial bg-label-danger rounded">
                                        <i class="mdi mdi-close-circle-outline mdi-24px"></i>
                                    </div>
                                </div>
                                <h3 class="mb-0 fw-semibold">{{ $loss }}</h3>
                            </div>
                            <div class="text-muted small mb-2">
                                {{ $prospect > 0 ? round(($loss / $prospect) * 100, 1) : 0 }}% ti anu ditawaran
                            </div>
                            <div class="mt-auto">
                                <span class="badge bg-label-secondary rounded-pill">Anu Bedo</span>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

    <!-- End Support Dashboard -->
@elseif (Auth::user()->role == 'Admin')
    <div class="row gy-4 mb-4">
        <div class="col-12 col-lg-4">

            @php
                $pctAdmin     = $targetAllSales > 0 ? round(($poTotalPriceAdmin / $targetAllSales) * 100, 1) : 0;
                $pctAdmColor  = $pctAdmin >= 100 ? 'success' : ($pctAdmin >= 80 ? 'warning' : 'danger');
                $pctAdmBar    = min($pctAdmin, 100);
                $today        = \Carbon\Carbon::now();
                $semesterNow  = \App\Models\SalesReports::where('semester', $today->month > 6 ? 2 : 1)
                                    ->where('year', $today->year)->first();
            @endphp
            <div class="card mb-3">
                <div class="card-body" style="padding-right: 10rem;">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge bg-label-primary">
                            <i class="mdi mdi-chart-line"></i> Monthly
                        </span>
                        <small class="text-muted">{{ $today->locale('id')->translatedFormat('F Y') }}</small>
                    </div>
                    <h5 class="card-title mb-1">Sales Performance</h5>
                    <h3 class="text-primary fw-bold mb-0">Rp. {{ $formattedTotalPriceAdmin }}</h3>
                    <small class="text-muted">Target: Rp. {{ number_format($targetAllSales, 0, ',', '.') }}</small>

                    <div class="my-2">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <small class="text-muted fw-semibold">Pencapaian Target</small>
                            <small class="fw-bold text-{{ $pctAdmColor }}">{{ $pctAdmin }}%</small>
                        </div>
                        <div class="progress" style="height:6px;border-radius:4px;">
                            <div class="progress-bar bg-{{ $pctAdmColor }}"
                                 style="width:{{ $pctAdmBar }}%;border-radius:4px;"></div>
                        </div>
                    </div>

                    @if ($semesterNow)
                        <a href="{{ route('report.semester', $semesterNow) }}"
                           class="btn btn-sm btn-primary waves-effect waves-light mt-1">
                            <i class="mdi mdi-chart-areaspline me-1"></i> View Sales
                        </a>
                    @endif
                </div>
                <img src="{{ asset('assets') }}/img/illustrations/trophy.png"
                    class="position-absolute bottom-0 end-0 me-3" height="140" alt="view sales">
            </div>
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title m-0 me-2">Rank Sales Team 🏆</h5>
                </div>
                <div class="card-body">
                    <ul class="p-0 m-0"><hr>
                        @php
                            $no = 1;
                        @endphp
                        @foreach ($sorted as $sale)
                            @php
                                switch ($no) {
                                    case 1:
                                        $color = 'warning'; // Kuning / Orange
                                        break;
                                    case 2:
                                        $color = 'success'; // Hijau
                                        break;
                                    case 3:
                                        $color = 'info'; // Biru
                                        break;
                                    case 4:
                                        $color = 'secondary'; // Abu-abu
                                        break;
                                    case 5:
                                        $color = 'primary'; // custom (kalau ada)
                                        break;
                                    case 6:
                                        $color = 'danger'; // Merah
                                        break;
                                    case 7:
                                        $color = 'dark'; // Hitam
                                        break;
                                    default:
                                        $color = 'primary';
                                        break;
                                }
                            @endphp
                            <li class="d-flex align-items-start mb-3" style="list-style:none;">
                                <span class="badge bg-label-{{ $color }} d-inline-flex align-items-center justify-content-center flex-shrink-0"
                                      style="min-width:36px;font-size:13px;">
                                    #{{ $no }}
                                </span>
                                <div class="ms-2 w-100">
                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                        <div>
                                            <span class="fw-semibold" style="font-size:0.875rem;">
                                                {{ $sale['name'] }}
                                                @if ($no == 1)
                                                    <i class="mdi mdi-crown text-warning ms-1"></i>
                                                @endif
                                            </span>
                                            <small class="text-muted d-block" style="font-size:0.7rem;">{{ $sale['area'] }}</small>
                                        </div>
                                        <span class="badge bg-label-{{ $color }} rounded-pill" style="font-size:12px;">
                                            {{ $sale['percentage'] }}%
                                        </span>
                                    </div>
                                    <div class="progress" style="height:4px;border-radius:4px;">
                                        <div class="progress-bar bg-{{ $color }}"
                                             style="width:{{ min($sale['percentage'], 100) }}%;border-radius:4px;"></div>
                                    </div>
                                </div>
                            </li>
                            @php
                                $no++;
                            @endphp
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-8">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between">
                    <div class="card-title m-0">
                        <h5 class="mb-0">Sales Overview</h5>
                    </div>
                </div>
                <div class="card-body pb-3">
                    <ul class="nav nav-tabs nav-tabs-widget pb-3 gap-2 d-flex flex-nowrap" role="tablist">
                        @foreach ($sales as $user)
                            <li class="nav-item change-sales" role="presentation" style="width: 15%;height: 15%;"
                                data-id="{{ $user->id }}">
                                <img class="nav-link btn {{ $user->id == 1 ? 'active' : '' }} d-flex flex-column align-items-center justify-content-center"
                                    role="tab" data-bs-toggle="tab" data-bs-target="#navs-sales-{{ $user->id }}"
                                    aria-controls="navs-sales-{{ $user->id }}" aria-selected="true"
                                    src="{{ url('') . '/' . $user->image }}" alt="" srcset=""
                                    style="width : 75px !important; height:75px !important; object-fit: cover; padding: 10px;">
                            </li>
                        @endforeach
                        <span class="tab-slider" style="left: 0px; width: 112px; bottom: 0px;"></span>
                    </ul>
                    <div class="tab-content p-0 ms-0 ms-sm-2">
                        @php
                            $item = 0;
                        @endphp
                        @foreach ($sales as $user)
                            <div class="tab-pane fade{{ $user->id == 1 ? ' show active' : '' }}"
                                id="navs-sales-{{ $user->id }}" role="tabpanel">
                                <div class="mb-3">
                                    <div data-id="{{ $item }}">
                                        <div class="card-header">
                                            <div class="row">
                                                <div class="col-12 mb-3">
                                                    <div class="d-flex justify-content-between">
                                                        <h4 class="">{{ $user->name }}'s Overview</h4>
                                                    </div>
                                                </div>
                                                @if ($user->role == 'Sales')
                                                    @if ($user->id == 16 || $user->id == 23)
                                                        <div class="col-6">
                                                            <div class="row mb-2">
                                                                <div class="col-2">
                                                                    <div class="avatar">
                                                                        <div class="avatar-initial bg-label-info rounded">
                                                                            <i class="mdi mdi-reproduction mdi-24px"></i>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-2 d-flex align-items-center p-0">
                                                                    <h4 class="filtered-percent-product fs-5 m-0">
                                                                        0 %</h4>
                                                                </div>
                                                                <div class="col-8">

                                                                    <div class="card-info">
                                                                        <h5 class="mb-0">Upload Product</h5>
                                                                        <small>
                                                                            <span class="filtered-product">
                                                                                0
                                                                            </span>
                                                                            <span
                                                                                class="filtered-target-product text-muted fs-tiny fw-normal">/
                                                                                100</span>
                                                                        </small>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row mb-2">
                                                                <div class="col-2">
                                                                    <div class="avatar">
                                                                        <div
                                                                            class="avatar-initial bg-label-whatsapp rounded">
                                                                            <i class="mdi mdi-whatsapp mdi-24px"></i>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-2 d-flex align-items-center p-0">
                                                                    <h4 class="filtered-percent-sw fs-5 m-0">
                                                                        0 %</h4>
                                                                </div>
                                                                <div class="col-8">

                                                                    <div class="card-info">
                                                                        <h5 class="mb-0">Upload SW</h5>
                                                                        <small>
                                                                            <span class="filtered-sw">
                                                                                0
                                                                            </span>
                                                                            <span
                                                                                class="filtered-target-sw text-muted fs-tiny fw-normal">/
                                                                                {{ $user->id == 16 ? '120' : '60' }}</span>
                                                                        </small>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row mb-2">
                                                                <div class="col-2">
                                                                    <div class="avatar">
                                                                        <div
                                                                            class="avatar-initial bg-label-secondary rounded">
                                                                            <i class="mdi mdi-video-outline mdi-24px"></i>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-2 d-flex align-items-center p-0">
                                                                    <h4 class="filtered-percent-video fs-5 m-0">
                                                                        0 %</h4>
                                                                </div>
                                                                <div class="col-8">
                                                                    <div class="card-info">
                                                                        <h5 class="mb-0">Upload Video</h5>
                                                                        <small>
                                                                            <span class="filtered-video">
                                                                                0
                                                                            </span>
                                                                            <span
                                                                                class="filtered-target-video text-muted fs-tiny fw-normal">/
                                                                                100%</span>
                                                                        </small>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row mb-2">
                                                                <div class="col-2">
                                                                    <div class="avatar">
                                                                        <div
                                                                            class="avatar-initial bg-label-primary rounded">
                                                                            <i
                                                                                class="mdi mdi-account-multiple-outline mdi-24px"></i>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-2 d-flex align-items-center p-0">
                                                                    <h4 class="filtered-percent-crm fs-5 m-0">
                                                                        0 %</h4>
                                                                </div>
                                                                <div class="col-8">
                                                                    <div class="card-info">
                                                                        <h5 class="mb-0">CRM</h5>
                                                                        <small>
                                                                            <span class="filtered-crm">
                                                                                {{ $filteredCRM }}
                                                                            </span>
                                                                            <span
                                                                                class="filtered-target-crm text-muted fs-tiny fw-normal">/{{ $targetCrm[$user->id] ?? 0 }}</span>
                                                                        </small>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row mb-2">
                                                                <div class="col-2">
                                                                    <div class="avatar">
                                                                        <div
                                                                            class="avatar-initial bg-label-warning rounded">
                                                                            <i
                                                                                class="mdi mdi-package-variant-closed-check mdi-24px"></i>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-2 d-flex align-items-center p-0">
                                                                    <h4 class="filtered-percent-status fs-5 m-0">
                                                                        0 %</h4>
                                                                </div>
                                                                <div class="col-8">

                                                                    <div class="card-info">
                                                                        <h5 class="mb-0">Status Product</h5>
                                                                        <small>
                                                                            <span class="filtered-status">
                                                                                0
                                                                            </span>
                                                                            <span
                                                                                class="filtered-target-status text-muted fs-tiny fw-normal">/
                                                                                5,0</span>
                                                                        </small>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-6">
                                                            <div class="row mb-2">
                                                                <div class="col-2">
                                                                    <div class="avatar">
                                                                        <div
                                                                            class="avatar-initial bg-label-facebook rounded">
                                                                            <i
                                                                                class="mdi mdi-truck-delivery-outline mdi-24px"></i>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-2 d-flex align-items-center p-0">
                                                                    <h4 class="filtered-percent-delivery fs-5 m-0">
                                                                        0 %</h4>
                                                                </div>
                                                                <div class="col-8">

                                                                    <div class="card-info">
                                                                        <h5 class="mb-0">Delivery Status</h5>
                                                                        <small>
                                                                            <span class="filtered-delivery">
                                                                                0
                                                                            </span>
                                                                            <span
                                                                                class="filtered-target-delivery text-muted fs-tiny fw-normal">/
                                                                                5,0</span>
                                                                        </small>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row mb-2">
                                                                <div class="col-2">
                                                                    <div class="avatar">
                                                                        <div class="avatar-initial bg-label-dark rounded">
                                                                            <i class="mdi mdi-cart-check mdi-24px"></i>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-2 d-flex align-items-center p-0">
                                                                    <h4 class="filtered-percent-customer fs-5 m-0">
                                                                        0 %</h4>
                                                                </div>
                                                                <div class="col-8">

                                                                    <div class="card-info">
                                                                        <h5 class="mb-0">Customer Care</h5>
                                                                        <small>
                                                                            <span class="filtered-customer">
                                                                                0
                                                                            </span>
                                                                            <span
                                                                                class="filtered-target-customer text-muted fs-tiny fw-normal">/
                                                                                5,0</span>
                                                                        </small>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row mb-2">
                                                                <div class="col-2">
                                                                    <div class="avatar">
                                                                        <div
                                                                            class="avatar-initial bg-label-pinterest rounded">
                                                                            <i
                                                                                class="mdi mdi-account-heart-outline mdi-24px"></i>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-2 d-flex align-items-center p-0">
                                                                    <h4 class="filtered-percent-response fs-5 m-0">
                                                                        0 %</h4>
                                                                </div>
                                                                <div class="col-8">

                                                                    <div class="card-info">
                                                                        <h5 class="mb-0">Chat Response</h5>
                                                                        <small>
                                                                            <span class="filtered-response">
                                                                                0
                                                                            </span>
                                                                            <span
                                                                                class="filtered-target-response text-muted fs-tiny fw-normal">/
                                                                                100%</span>
                                                                        </small>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row mb-2">
                                                                <div class="col-2">
                                                                    <div class="avatar">
                                                                        <div
                                                                            class="avatar-initial bg-label-reddit rounded">
                                                                            <i class="mdi mdi-monitor-star mdi-24px"></i>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-2 d-flex align-items-center p-0">
                                                                    <h4 class="filtered-percent-rating fs-5 m-0">
                                                                        0 %</h4>
                                                                </div>
                                                                <div class="col-8">

                                                                    <div class="card-info">
                                                                        <h5 class="mb-0">Store Performance</h5>
                                                                        <small>
                                                                            <span class="filtered-rating">
                                                                                0
                                                                            </span>
                                                                            <span
                                                                                class="filtered-target-rating text-muted fs-tiny fw-normal">/
                                                                                5.0</span>
                                                                        </small>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row mb-2">
                                                                <div class="col-2">
                                                                    <div class="avatar">
                                                                        <div
                                                                            class="avatar-initial bg-label-success rounded">
                                                                            <i class="mdi mdi-cart-plus mdi-24px"></i>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-2 d-flex align-items-center p-0">
                                                                    <h4 class="admin-target-total-po fs-5 m-0">
                                                                        0 %</h4>
                                                                </div>
                                                                <div class="col-8">

                                                                    <div class="card-info">
                                                                        <h5 class="mb-0">Purchase Order</h5>
                                                                        <small>
                                                                            <span class="admin-total-po">
                                                                                Rp 0
                                                                            </span>
                                                                        </small>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            {{-- <div class="row mb-2">
                                                                    <div class="col-2">
                                                                        <div class="avatar">
                                                                            <div
                                                                                class="avatar-initial bg-label-success rounded">
                                                                                <i class="mdi mdi-cart-plus mdi-24px"></i>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-10">
                                                                        <div class="card-info">
                                                                            <h5 class="mb-0">
                                                                                <span class="admin-total-po">
                                                                                    Rp
                                                                                    {{ number_format($totalPO, 0, ',', '.') }}
                                                                                </span>
                                                                                @php
                                                                                    $targetPO =
                                                                                        ($totalPO /
                                                                                            $targetSales[$item][0]
                                                                                                ->total) *
                                                                                        100;
                                                                                    if ($targetPO <= 80) {
                                                                                        $color = 'danger';
                                                                                    } elseif ($targetPO <= 100) {
                                                                                        $color = 'warning';
                                                                                    } else {
                                                                                        $color = 'success';
                                                                                    }
                                                                                @endphp
                                                                                <span
                                                                                    class="admin-target-total-po bg-label-{{ $color }} fs-tiny fw-normal">{{ round($targetPO) }}
                                                                                    %</span>
                                                                            </h5>
                                                                            <small class="text-muted">Purchase
                                                                                Order</small>
                                                                        </div>
                                                                    </div>
                                                                </div> --}}
                                                        </div>
                                                    @else
                                                        <div class="col-6">
                                                            @if ($user->id == 1 || $user->id == 2 || $user->id == 32)
                                                                <div class="row mb-2">
                                                                    <div class="col-2">
                                                                        <div class="avatar">
                                                                            <div
                                                                                class="avatar-initial bg-label-secondary rounded">
                                                                                <i
                                                                                    class="mdi mdi-account-multiple-plus-outline mdi-24px"></i>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-2 d-flex align-items-center">
                                                                        @php
                                                                            $salesTargetLeads = ($targetSales[$item][0] ?? null)?->leads ?? 0;
                                                                            $targetLeads = $salesTargetLeads > 0 ? ($filteredLeads / $salesTargetLeads) * 100 : 0;
                                                                        @endphp
                                                                        <h4 class="filtered-percent-leads fs-5 m-0">
                                                                            {{ round($targetLeads) }} %</h4>
                                                                    </div>
                                                                    <div class="col-8">

                                                                        <div class="card-info">
                                                                            <h5 class="mb-0">
                                                                                <span class="filtered-leads">
                                                                                    {{ $filteredLeads }}
                                                                                </span>
                                                                                <span
                                                                                    class="filtered-target-leads text-muted fs-tiny fw-normal">/
                                                                                    {{ ($targetSales[$item][0] ?? null)?->leads ?? 0 }}</span>
                                                                            </h5>
                                                                            <small class="text-muted">New Leads</small>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="row mb-2">
                                                                    <div class="col-2">
                                                                        <div class="avatar">
                                                                            <div
                                                                                class="avatar-initial bg-label-info rounded">
                                                                                <i
                                                                                    class="mdi mdi-phone-outline mdi-24px"></i>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-2 d-flex align-items-center">
                                                                        @php
                                                                            $salesTargetDC = ($targetSales[$item][0] ?? null)?->dc ?? 0;
                                                                            $targetDC = $salesTargetDC > 0 ? ($filteredDC / $salesTargetDC) * 100 : 0;
                                                                        @endphp
                                                                        {{-- <h4 class="filtered-percent-dc fs-5 m-0">
                                                                                {{ round($targetDC) }} %</h4> --}}
                                                                    </div>
                                                                    <div class="col-8">

                                                                        <div class="card-info">
                                                                            <h5 class="mb-0">
                                                                                <span class="filtered-dc">
                                                                                    {{ $filteredDC }}
                                                                                </span>
                                                                                {{-- <span
                                                                                        class="filtered-target-dc text-muted fs-tiny fw-normal">/{{ $targetSales[$item][0]->dc }}</span> --}}
                                                                            </h5>
                                                                            <small class="text-muted">
                                                                                Daily Call
                                                                            </small>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                            <div class="row mb-2">
                                                                <div class="col-2">
                                                                    <div class="avatar">
                                                                        <div
                                                                            class="avatar-initial bg-label-primary rounded">
                                                                            <i
                                                                                class="mdi mdi-account-multiple-outline mdi-24px"></i>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-2 d-flex align-items-center">
                                                                    @php
                                                                        $crmDenominator = $targetCrm[$user->id] ?? 0;
                                                                        $targetCRM = $crmDenominator > 0
                                                                            ? ($filteredCRM / $crmDenominator) * 100
                                                                            : 0;
                                                                    @endphp
                                                                    @if ($user->id != 3)
                                                                        <h4 class="filtered-percent-crm fs-5 m-0">
                                                                            {{ round($targetCRM) }} %</h4>
                                                                    @endif
                                                                </div>
                                                                <div class="col-8">
                                                                    <div class="card-info">
                                                                        @if ($user->id == 3)
                                                                            -
                                                                        @else
                                                                            <h5 class="mb-0">
                                                                                <span class="filtered-crm">
                                                                                    {{ $filteredCRM }}
                                                                                </span>
                                                                                <span
                                                                                    class="filtered-target-crm text-muted fs-tiny fw-normal">/{{ $targetCrm[$user->id] ?? 0 }}</span>
                                                                            </h5>
                                                                        @endif
                                                                        <small class="text-muted">CRM</small>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            @php
                                                                $lastDetail = $user->detail->last();
                                                            @endphp
                                                            <div class="row mb-2">
                                                                <div class="col-2">
                                                                    <div class="avatar">
                                                                        <div
                                                                            class="avatar-initial bg-label-warning rounded">
                                                                            <i
                                                                                class="mdi mdi-email-multiple-outline mdi-24px"></i>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-2 d-flex align-items-center">
                                                                    {{-- @php
                                                                            $targetQuote =
                                                                                ($filteredQuote /
                                                                                    $targetSales[$item][0]->quote) *
                                                                                100;
                                                                        @endphp
                                                                        <h4 class="filtered-percent-quote fs-5 m-0">
                                                                            {{ round($targetQuote) }} %</h4> --}}
                                                                </div>
                                                                <div class="col-8">

                                                                    <div class="card-info">
                                                                        <h5 class="mb-0">
                                                                            <span class="filtered-quote">
                                                                                {{ $filteredQuote }}
                                                                            </span>
                                                                            {{-- <span
                                                                                    class="filtered-target-quote text-muted fs-tiny fw-normal">/{{ $targetSales[$item][0]->quote }}</span> --}}
                                                                        </h5>
                                                                        <small class="text-muted">Quotation</small>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row mb-2">
                                                                <div class="col-2">
                                                                    <div class="avatar">
                                                                        <div
                                                                            class="avatar-initial bg-label-success rounded">
                                                                            <i class="mdi mdi-cart-plus mdi-24px"></i>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-2 d-flex align-items-center">
                                                                    {{-- @php
                                                                            $targetProspect =
                                                                                $allProspect > 0
                                                                                    ? ($filteredProspect /
                                                                                            $allProspect) *
                                                                                        100
                                                                                    : 0;
                                                                        @endphp
                                                                        <h4
                                                                            class="filtered-percent-prospect-sales fs-5 m-0">
                                                                            {{ round($targetProspect) }} %</h4> --}}
                                                                </div>
                                                                <div class="col-8">

                                                                    <div class="card-info">
                                                                        <h5 class="mb-0">
                                                                            <span class="filtered-prospect-sales">
                                                                                {{ $filteredProspect }}
                                                                            </span>
                                                                            {{-- <span
                                                                                    class="filtered-all-prospect text-muted fs-tiny fw-normal">/{{ $allProspect }}</span> --}}
                                                                        </h5>
                                                                        <small class="text-muted">Prospect</small>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-6">
                                                            <div class="row mb-2">
                                                                <div class="col-2">
                                                                    <div class="avatar">
                                                                        <div
                                                                            class="avatar-initial bg-label-secondary rounded">
                                                                            <i class="mdi mdi-cart mdi-24px"></i>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-10">

                                                                    <div class="card-info">
                                                                        <h5 class="mb-0">
                                                                            <span class="admin-total-quotation">
                                                                                Rp
                                                                                {{ number_format($totalQuotation, 0, ',', '.') }}
                                                                            </span>
                                                                        </h5>
                                                                        <small class="text-muted">Quotation</small>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row mb-2">
                                                                <div class="col-2">
                                                                    <div class="avatar">
                                                                        <div class="avatar-initial bg-label-info rounded">
                                                                            <i
                                                                                class="mdi mdi-cart-arrow-down mdi-24px"></i>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-10">

                                                                    <div class="card-info">
                                                                        <h5 class="mb-0">
                                                                            <span class="admin-total-prospect">
                                                                                Rp
                                                                                {{ number_format($totalProspect, 0, ',', '.') }}
                                                                            </span>
                                                                        </h5>
                                                                        <small class="text-muted">Prospect</small>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row mb-2">
                                                                <div class="col-2">
                                                                    <div class="avatar">
                                                                        <div
                                                                            class="avatar-initial bg-label-warning rounded">
                                                                            <i class="mdi mdi-cart-heart mdi-24px"></i>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-10">

                                                                    <div class="card-info">
                                                                        <h5 class="mb-0">
                                                                            <span class="admin-total-hot-prospect">
                                                                                Rp
                                                                                {{ number_format($totalHotProspect, 0, ',', '.') }}
                                                                            </span>
                                                                        </h5>
                                                                        <small class="text-muted">Hot Prospect</small>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row mb-2">
                                                                <div class="col-2">
                                                                    <div class="avatar">
                                                                        <div
                                                                            class="avatar-initial bg-label-danger rounded">
                                                                            <i class="mdi mdi-cart-minus mdi-24px"></i>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-10">

                                                                    <div class="card-info">
                                                                        <h5 class="mb-0">
                                                                            <span class="admin-total-loss">
                                                                                Rp
                                                                                {{ number_format($totalLoss, 0, ',', '.') }}
                                                                            </span>
                                                                        </h5>
                                                                        <small class="text-muted">Quotation
                                                                            Loss</small>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row mb-2">
                                                                <div class="col-2">
                                                                    <div class="avatar">
                                                                        <div
                                                                            class="avatar-initial bg-label-success rounded">
                                                                            <i class="mdi mdi-cart-plus mdi-24px"></i>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-10">

                                                                    <div class="card-info">
                                                                        <h5 class="mb-0">
                                                                            <span class="admin-total-po">
                                                                                Rp
                                                                                {{ number_format($totalPO, 0, ',', '.') }}
                                                                            </span>
                                                                            @php
                                                                                $salesTargetTotal = ($targetSales[$item][0] ?? null)?->total ?? 0;
                                                                                $targetPO = $salesTargetTotal > 0 ? ($totalPO / $salesTargetTotal) * 100 : 0;
                                                                                if ($targetPO <= 80) {
                                                                                    $color = 'danger';
                                                                                } elseif ($targetPO <= 100) {
                                                                                    $color = 'warning';
                                                                                } else {
                                                                                    $color = 'success';
                                                                                }
                                                                            @endphp
                                                                            <span
                                                                                class="admin-target-total-po bg-label-{{ $color }} fs-tiny fw-normal">{{ round($targetPO) }}
                                                                                %</span>
                                                                        </h5>
                                                                        <small class="text-muted">Purchase
                                                                            Order</small>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                    <div class="col-12">
                                                        <div class="row mt-3">
                                                            @php
                                                                $month = date('m');
                                                                $year = date('Y');
                                                                $dateNow = $month . '-' . $year;
                                                            @endphp
                                                            <div class="col-2">
                                                                <a class="btn btn-warning d-grid w-100 waves-effect text-white h-100"
                                                                    type="button" data-bs-toggle="modal"
                                                                    data-bs-target="#overview-sales-{{ $user->id }}">
                                                                    Info
                                                                </a>
                                                            </div>
                                                            <div class="col-4">
                                                                <a class="btn btn-facebook d-grid w-100 waves-effect h-100"
                                                                    href="{{ route('detail-overview.semester', ['sales' => $user->id, 'date' => $dateNow]) }}">
                                                                    Detail
                                                                </a>
                                                            </div>
                                                            <div class="col-6">
                                                                <a class="btn btn-secondary d-grid w-100 waves-effect h-100"
                                                                    href="{{ route('overview.semester', $user->id) }}">
                                                                    Semester
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @else
                                                    <div class="col-6">
                                                        <div class="row mb-2">
                                                            <div class="col-2">
                                                                <div class="avatar">
                                                                    <div class="avatar-initial bg-label-info rounded">
                                                                        <i class="mdi mdi-phone-outline mdi-24px"></i>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-2 d-flex align-items-center">
                                                                <h4 class="filtered-percent-prospect fs-5 m-0">
                                                                    0 %</h4>
                                                            </div>
                                                            <div class="col-8">
                                                                <div class="card-info">
                                                                    <h5 class="mb-0">
                                                                        <span class="filtered-prospect">
                                                                            {{ $filteredProspect }}
                                                                        </span>
                                                                        <span class="text-muted fs-tiny fw-normal">/
                                                                            100</span>
                                                                    </h5>
                                                                    <small class="text-muted">Prospect</small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row mb-2">
                                                            <div class="col-2">
                                                                <div class="avatar">
                                                                    <div class="avatar-initial bg-label-primary rounded">
                                                                        <i
                                                                            class="mdi mdi-account-multiple-outline mdi-24px"></i>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-2 d-flex align-items-center">
                                                                <h4 class="filtered-percent-provided fs-5 m-0">
                                                                    0 %</h4>
                                                            </div>
                                                            <div class="col-8">
                                                                <div class="card-info">
                                                                    <h5 class="mb-0">
                                                                        <span class="filtered-provided">
                                                                            0
                                                                        </span>
                                                                        <span
                                                                            class="filtered-all-prospect-provided text-muted fs-tiny fw-normal">/{{ $allProspect }}</span>
                                                                    </h5>
                                                                    <small class="text-muted">Provided</small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row mb-2">
                                                            <div class="col-2">
                                                                <div class="avatar">
                                                                    <div class="avatar-initial bg-label-warning rounded">
                                                                        <i
                                                                            class="mdi mdi-email-multiple-outline mdi-24px"></i>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-2 d-flex align-items-center">
                                                                <h4 class="filtered-percent-quote-prospect fs-5 m-0">
                                                                    0 %</h4>
                                                            </div>
                                                            <div class="col-8">
                                                                <div class="card-info">
                                                                    <h5 class="mb-0">
                                                                        <span class="filtered-quote-prospect">
                                                                            0
                                                                        </span>
                                                                        <span
                                                                            class="filtered-all-quote-prospect text-muted fs-tiny fw-normal">/{{ $allProspect }}</span>
                                                                    </h5>
                                                                    <small class="text-muted">Quotation</small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row mb-2">
                                                            <div class="col-2">
                                                                <div class="avatar">
                                                                    <div class="avatar-initial bg-label-danger rounded">
                                                                        <i
                                                                            class="mdi mdi-account-alert-outline mdi-24px"></i>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-2 d-flex align-items-center">
                                                                <h4 class="filtered-percent-not-provided fs-5 m-0">
                                                                    0 %</h4>
                                                            </div>
                                                            <div class="col-8">
                                                                <div class="card-info">
                                                                    <h5 class="mb-0">
                                                                        <span class="filtered-not-provided">
                                                                            0
                                                                        </span>
                                                                        <span
                                                                            class="filtered-all-prospect-not-provided text-muted fs-tiny fw-normal">/{{ $allProspect }}</span>
                                                                    </h5>
                                                                    <small class="text-muted">Not Provided</small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row mb-2">
                                                            <div class="col-2">
                                                                <div class="avatar">
                                                                    <div class="avatar-initial bg-label-success rounded">
                                                                        <i class="mdi mdi-cart-plus mdi-24px"></i>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-2 d-flex align-items-center">
                                                                <h4 class="filtered-percent-po-prospect fs-5 m-0">
                                                                    0 %</h4>
                                                            </div>
                                                            <div class="col-8">
                                                                <div class="card-info">
                                                                    <h5 class="mb-0">
                                                                        <span class="filtered-po-prospect">
                                                                            0
                                                                        </span>
                                                                        <span
                                                                            class="filtered-all-po-prospect text-muted fs-tiny fw-normal">/0</span>
                                                                    </h5>
                                                                    <small class="text-muted">Purchase Order</small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="row mb-2">
                                                            <div class="col-2">
                                                                <div class="avatar">
                                                                    <div class="avatar-initial bg-label-primary rounded">
                                                                        <i class="mdi mdi-cart mdi-24px"></i>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-2 d-flex align-items-center">
                                                                <h4 class="total-prospect-quotation-percent fs-5 m-0">
                                                                    0 %</h4>
                                                            </div>
                                                            <div class="col-8">
                                                                <div class="card-info">
                                                                    <h5 class="mb-0">
                                                                        <span class="total-prospect-quotation">
                                                                            0
                                                                        </span>
                                                                    </h5>
                                                                    <small class="text-muted">Quotation</small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row mb-2">
                                                            <div class="col-2">
                                                                <div class="avatar">
                                                                    <div class="avatar-initial bg-label-warning rounded">
                                                                        <i class="mdi mdi-cart-heart mdi-24px"></i>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-2 d-flex align-items-center">
                                                                <h4 class="total-prospect-po-percent fs-5 m-0">
                                                                    0 %</h4>
                                                            </div>
                                                            <div class="col-8">
                                                                <div class="card-info">
                                                                    <h5 class="mb-0">
                                                                        <span class="total-prospect-hot">
                                                                            0
                                                                        </span>
                                                                    </h5>
                                                                    <small class="text-muted">Hot Prospect</small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row mb-2">
                                                            <div class="col-2">
                                                                <div class="avatar">
                                                                    <div class="avatar-initial bg-label-success rounded">
                                                                        <i class="mdi mdi-cart-plus mdi-24px"></i>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-2 d-flex align-items-center">
                                                                <h4 class="total-prospect-po-percent fs-5 m-0">
                                                                    0 %</h4>
                                                            </div>
                                                            <div class="col-8">
                                                                <div class="card-info">
                                                                    <h5 class="mb-0">
                                                                        <span class="total-prospect-po">
                                                                            0
                                                                        </span>
                                                                    </h5>
                                                                    <small class="text-muted">Purchase Order</small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row mb-2">
                                                            <div class="col-2">
                                                                <div class="avatar">
                                                                    <div class="avatar-initial bg-label-success rounded">
                                                                        <i class="mdi mdi-cart-plus mdi-24px"></i>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-2 d-flex align-items-center">
                                                                <h4 class="-percent m-0">
                                                                    0 %</h4>
                                                            </div>
                                                            <div class="col-8">
                                                                <div class="card-info">
                                                                    <h5 class="mb-0">
                                                                        <span class="">
                                                                            -
                                                                        </span>
                                                                    </h5>
                                                                    <small class="text-muted">-</small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row mb-2">
                                                            <div class="col-2">
                                                                <div class="avatar">
                                                                    <div class="avatar-initial bg-label-success rounded">
                                                                        <i class="mdi mdi-cart-plus mdi-24px"></i>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-2 d-flex align-items-center">
                                                                <h4 class="-percent m-0">
                                                                    0 %</h4>
                                                            </div>
                                                            <div class="col-8">
                                                                <div class="card-info">
                                                                    <h5 class="mb-0">
                                                                        <span class="">
                                                                            -
                                                                        </span>
                                                                    </h5>
                                                                    <small class="text-muted">-</small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        {{-- <div class="d-flex align-items-center gap-2">
                                                                <div class="d-flex mb-2 gap-2">
                                                                    <div class="avatar">
                                                                        <div
                                                                            class="avatar-initial bg-label-warning rounded">
                                                                            <i
                                                                                class="mdi mdi-email-alert-outline mdi-24px"></i>
                                                                        </div>
                                                                    </div>
                                                                    <div class="card-info">
                                                                        <h5 class="mb-0 total-prospect">Rp
                                                                            {{ number_format($totalProspect, 2, ',', '.') }}
                                                                        </h5>
                                                                        <small class="text-muted">Hot Prospect</small>
                                                                    </div>
                                                                </div>
                                                            </div> --}}
                                                    </div>
                                                    <div class="row mt-3">
                                                        @php
                                                            $month = date('m');
                                                            $year = date('Y');
                                                            $dateNow = $month . '-' . $year;
                                                        @endphp
                                                        <div class="col-2">
                                                            <a class="btn btn-warning d-grid w-100 waves-effect h-100"
                                                                type="button" data-bs-toggle="modal"
                                                                data-bs-target="#overview-sales-{{ $item }}">
                                                                Info
                                                            </a>
                                                        </div>
                                                        <div class="col-4">
                                                            <a class="btn btn-facebook d-grid w-100 waves-effect h-100"
                                                                href="{{ route('detail-overview.semester', ['sales' => $user->id, 'date' => $dateNow]) }}">
                                                                Detail
                                                            </a>
                                                        </div>
                                                        <div class="col-6">
                                                            <a class="btn btn-secondary d-grid w-100 waves-effect h-100"
                                                                href="{{ route('overview.semester', $user->id) }}">
                                                                Semester
                                                            </a>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @php
                                    $item++;
                                @endphp
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="card mb-3">
                <div class="card-datatable table-responsive pt-0">
                    <table class="datatable-prospect-quote table table-bordered">
                        <thead>
                            <tr>
                                <th>Quote No.</th>
                                <th>Company</th>
                                <th>Total Price</th>
                                <th>Description</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th class="text-center" style="width:48px;"></th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
    {{-- <div class="card mb-4">
            <div class="card-datatable table-responsive pt-0">
                <table class="datatable-notulen table table-striped">
                    <thead>
                        <tr>
                            <th></th>
                            <th></th>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Desc</th>
                            <th>Level</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div> --}}
    @php
        $item = 0;
    @endphp
    @foreach ($dataOverview as $overview)
        @include('components.modal.overview')
    @endforeach
@elseif(Auth::user()->role == 'Logistic')
    <h4 class="fw-bold py-3 mb-4">
        Product
    </h4>
    <div class="card mb-4">
        <div class="card-widget-separator-wrapper">
            <div class="card-body card-widget-separator">
                <div class="row gy-4 gy-sm-1">
                    <div class="col-sm-6 col-lg-3">
                        <div
                            class="d-flex justify-content-between align-items-start card-widget-1 border-end pb-3 pb-sm-0">
                            <div>
                                <p class="mb-2">Comodity</p>
                                <h4 class="mb-2">{{ $commodity }}</h4>
                                <p class="mb-0"><span class="badge rounded-pill bg-label-success"></span></p>
                            </div>
                            <div class="avatar me-sm-4">
                                <span class="avatar-initial rounded bg-label-secondary">
                                    <i class="mdi mdi-home-outline mdi-24px"></i>
                                </span>
                            </div>
                        </div>
                        <hr class="d-none d-sm-block d-lg-none me-4">
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <div
                            class="d-flex justify-content-between align-items-start card-widget-2 border-end pb-3 pb-sm-0">
                            <div>
                                <p class="mb-2">Equivalent</p>
                                <h4 class="mb-2">{{ $sproduct }}</h4>
                                <p class="mb-0"><span class="badge rounded-pill bg-label-success"></span></p>
                            </div>
                            <div class="avatar me-lg-4">
                                <span class="avatar-initial rounded bg-label-secondary">
                                    <i class="mdi mdi-laptop mdi-24px"></i>
                                </span>
                            </div>
                        </div>
                        <hr class="d-none d-sm-block d-lg-none">
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <div
                            class="d-flex justify-content-between align-items-start border-end pb-3 pb-sm-0 card-widget-3">
                            <div>
                                <p class="mb-2">Pruchase Order</p>
                                <h4 class="mb-2">1</h4>
                                <p class="mb-0"><span class="badge rounded-pill bg-label-success"></span></p>
                            </div>
                            <div class="avatar me-sm-4">
                                <span class="avatar-initial rounded bg-label-secondary">
                                    <i class="mdi mdi-wallet-giftcard mdi-24px"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="mb-2">Loss Order</p>
                                <h4 class="mb-2">2</h4>
                                <p class="mb-0"><span class="badge rounded-pill bg-label-danger"></span></p>
                            </div>
                            <div class="avatar">
                                <span class="avatar-initial rounded bg-label-secondary">
                                    <i class="mdi mdi-currency-usd mdi-24px"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card mb-3">
        <div class="card-datatable table-responsive pt-0">
            <table class="datatable-product table table-striped">
                <thead>
                    <tr>
                        <th></th>
                        <th></th>
                        <th>ID</th>
                        <th>SKU</th>
                        <th>Part Number</th>
                        {{-- <th>Brand</th> --}}
                        {{-- <th>Price</th> --}}
                        <th>Desc</th>
                        <th>Dimension</th>
                        <th>G/O</th>
                        <th>Stock BDG</th>
                        <th>Stock BKS</th>
                        <th>stock Pending</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    <div class="card mb-4">
        <div class="card-datatable table-responsive pt-0">
            <table class="datatable-notulen table table-striped">
                <thead>
                    <tr>
                        <th></th>
                        <th></th>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Desc</th>
                        <th>Level</th>
                        <th>Date</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    @include('components.modal.warehouse.product.form')
@elseif(Auth::user()->role == 'Coordinator')
    <h4 class="fw-3">Request Visit</h4>
    <div class="card mb-3">
        <div class="card-datatable table-responsive pt-0">
            <table class="datatable-visit-coordinator table table-striped">
                <thead>
                    <tr>
                        <th></th>
                        <th></th>
                        <th>ID</th>
                        <th>company</th>
                        <th>Machine</th>
                        <th>Date Request</th>
                        <th>Sales</th>
                        <th>Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    <h4 class="fw-3">Visit Schedule</h4>
    <div class="card mb-3">
        <div class="card-datatable table-responsive pt-0">
            <table class="datatable-visit-accept table table-striped">
                <thead>
                    <tr>
                        <th></th>
                        <th></th>
                        <th>ID</th>
                        <th>company</th>
                        <th>Machine</th>
                        <th>Date</th>
                        <th>Sales</th>
                        <th>Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    <div class="card mb-4">
        <div class="card-datatable table-responsive pt-0">
            <table class="datatable-notulen table table-striped">
                <thead>
                    <tr>
                        <th></th>
                        <th></th>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Desc</th>
                        <th>Level</th>
                        <th>Date</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    @foreach ($visits as $visit)
        @include('components.modal.req-visit.form-accept')
    @endforeach
    @foreach ($visited as $visit)
        @include('components.modal.req-visit.form-visited')
    @endforeach
@elseif(Auth::user()->role == 'ServiceM')
    <div class="nav-align-top mb-4">
        <ul class="nav nav-pills mb-3" role="tablist">
            <li class="nav-item" role="presentation">
                <button type="button" class="nav-link active waves-effect waves-light" role="tab"
                    data-bs-toggle="tab" data-bs-target="#navs-pills-top-new" aria-controls="navs-pills-top-new"
                    aria-selected="true">
                    New
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button type="button" class="nav-link waves-effect waves-light" role="tab" data-bs-toggle="tab"
                    data-bs-target="#navs-pills-top-progress" aria-controls="navs-pills-top-progress"
                    aria-selected="false" tabindex="-1">
                    Progress
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button type="button" class="nav-link waves-effect waves-light" role="tab" data-bs-toggle="tab"
                    data-bs-target="#navs-pills-top-delivery" aria-controls="navs-pills-top-delivery"
                    aria-selected="false" tabindex="-1">
                    Delivery
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button type="button" class="nav-link waves-effect waves-light" role="tab" data-bs-toggle="tab"
                    data-bs-target="#navs-pills-top-done" aria-controls="navs-pills-top-done" aria-selected="false"
                    tabindex="-1">
                    Done
                </button>
            </li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane fade show active" id="navs-pills-top-new" role="tabpanel">
                <div class="card-datatable pt-0">
                    <table
                        class="datatable-new-order-search{{ auth::user()->role == 'Sales' ? '' : '-admin' }} table table-bordered">
                        <thead>
                            @if (Auth::user()->role == 'Admin' || Auth::user()->role == 'Logistic' || Auth::user()->role == 'ServiceM')
                                <tr>
                                    <th>No SO</th>
                                    <th>Date</th>
                                    <th>Category</th>
                                    <th>Customer</th>
                                    <th>Description</th>
                                    <th>Status</th>
                                    <th>Payment</th>
                                    <th>area</th>
                                    <th>Delivery</th>
                                    <th>Sales</th>
                                    <th>Team</th>
                                </tr>
                            @endif
                            @if (Auth::user()->role == 'Sales')
                                <tr>
                                    <th>No SO</th>
                                    <th>Date</th>
                                    <th>PO No.</th>
                                    <th>Customer</th>
                                    <th>Part Desc</th>
                                    <th>Status</th>
                                    <th>Payment</th>
                                    <th>Delivery</th>
                                </tr>
                            @endif
                        </thead>
                    </table>
                </div>
            </div>
            <div class="tab-pane fade" id="navs-pills-top-progress" role="tabpanel">

                <div class="card-datatable pt-0">
                    <table
                        class="datatable-sales-list-search{{ auth::user()->role == 'Sales' ? '' : '-admin' }} table table-bordered">
                        <thead>
                            @if (Auth::user()->role == 'Admin' || Auth::user()->role == 'Logistic' || Auth::user()->role == 'ServiceM')
                                <tr>
                                    <th>No SO</th>
                                    <th>Date</th>
                                    <th>Category</th>
                                    <th>Customer</th>
                                    <th>Description</th>
                                    <th>Status</th>
                                    <th>Payment</th>
                                    <th>area</th>
                                    <th>Delivery</th>
                                    <th>Sales</th>
                                    <th>Team</th>
                                </tr>
                            @endif
                            @if (Auth::user()->role == 'Sales')
                                <tr>
                                    <th>Date</th>
                                    <th>PO No.</th>
                                    <th>Customer</th>
                                    <th>Part Desc</th>
                                    <th>Status</th>
                                    <th>Payment</th>
                                    <th>Delivery</th>
                                </tr>
                            @endif
                        </thead>
                    </table>
                </div>
            </div>
            <div class="tab-pane fade" id="navs-pills-top-delivery" role="tabpanel">
                <div class="card-datatable pt-0">
                    <table
                        class="datatable-sales-delivery-search{{ auth::user()->role == 'Sales' ? '' : '-admin' }} table table-bordered">
                        <thead>
                            <tr>
                                <th>PO Date</th>
                                @if (Auth::user()->role == 'Sales')
                                    <th>PO No.</th>
                                @endif
                                <th>Customer</th>
                                <th>Part Desc</th>
                                <th>Status</th>
                                <th>Payment</th>
                                <th>Delivery</th>
                                @if (Auth::user()->role == 'Admin' || Auth::user()->role == 'Logistic' || Auth::user()->role == 'ServiceM')
                                    <th>Sales</th>
                                    <th>Team</th>
                                @endif
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
            <div class="tab-pane fade" id="navs-pills-top-done  " role="tabpanel">
                <div class="card-datatable pt-0">
                    <table
                        class="datatable-sales-completed-search{{ auth::user()->role == 'Sales' ? '' : '-admin' }} table table-bordered">
                        <thead>
                            <tr>
                                <th>PO Date</th>
                                @if (Auth::user()->role == 'Sales')
                                    <th>PO No.</th>
                                @endif
                                <th>Customer</th>
                                <th>Part Desc</th>
                                <th>Status</th>
                                <th>Payment</th>
                                <th>Delivery</th>
                                @if (Auth::user()->role == 'Admin' || Auth::user()->role == 'Logistic' || Auth::user()->role == 'ServiceM')
                                    <th>Sales</th>
                                    <th>Team</th>
                                @endif
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
@elseif(Auth::user()->role == 'Technician')
    <div class="card mb-3">
        <div class="card-datatable table-responsive pt-0">
            <table class="datatable-notulen table table-striped">
                <thead>
                    <tr>
                        <th></th>
                        <th></th>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Desc</th>
                        <th>Level</th>
                        <th>Date</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
@elseif(Auth::user()->role == 'Client')
    <div class="row">
        <div class="col-12">
            @if (auth::user()->level == '1')
                <div class="card mb-3">
                    <div class="card-body">
                        <h5> Machine </h5>
                        <div class="card-datatable table-responsive pt-0">
                            <table class="datatable-client-compressor table table-striped">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th></th>
                                        <th>ID</th>
                                        <th>Brand</th>
                                        <th>Unit</th>
                                        <th>Tag</th>
                                        <th>Location</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            @else
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="card-datatable table-responsive pt-0">
                            <table class="datatable-machine-monitoring table table-striped">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>ID</th>
                                        <th>Status</th>
                                        <th>Brand</th>
                                        <th>Type</th>
                                        <th>Unit</th>
                                        <th>SN</th>
                                        <th>PIC</th>
                                        <th>Time</th>
                                        <th>Detail</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="card-datatable table-responsive pt-0">
                            <table class="datatable-issue-monitoring table table-striped">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>ID</th>
                                        <th>Date</th>
                                        <th>Brand</th>
                                        <th>Type</th>
                                        <th>SN</th>
                                        <th>Description</th>
                                        <th>PIC</th>
                                        <th>Accept</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@elseif(Auth::user()->role == 'Accounting')
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Dashboard /</span> Overview</h4>

    <div class="nav-align-top mb-4">
        <ul class="nav nav-pills mb-3" role="tablist">
            <li class="nav-item" role="presentation">
                <button type="button" class="nav-link active waves-effect waves-light" role="tab"
                    data-bs-toggle="tab" data-bs-target="#navs-pills-top-1" aria-controls="navs-pills-top-1"
                    aria-selected="true">
                    Semester 1
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button type="button" class="nav-link waves-effect waves-light" role="tab" data-bs-toggle="tab"
                    data-bs-target="#navs-pills-top-2" aria-controls="navs-pills-top-2" aria-selected="false"
                    tabindex="-1">
                    Semester 2
                </button>
            </li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane fade show active" id="navs-pills-top-1" role="tabpanel">
                <div class="row mb-3">
                    <!-- General Statistics -->
                    <div class="col-lg-6 col-xl-4 mb-4 ">
                        <div class="card h-100">
                            <div class="card-header d-flex align-items-center justify-content-between">
                                <div class="ms-2">
                                    <h4 class="mb-0">All Companies</h4>
                                    <small class="text-muted">Invoice & AR Summary</small>
                                </div>
                                <div class="dropdown">
                                    <button class="btn p-0" type="button" id="generalStatistics"
                                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="mdi mdi-dots-vertical mdi-24px"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="generalStatistics">
                                        <a class="dropdown-item" href="javascript:void(0);">Last 28 Days</a>
                                        <a class="dropdown-item" href="javascript:void(0);">Last Month</a>
                                        <a class="dropdown-item" href="javascript:void(0);">Last Year</a>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body pb-3">
                                <div class="mb-4 mt-2">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-md">
                                            <div class="avatar-initial bg-label-primary rounded">
                                                <img src="../../assets//svg/icons/credit-card.svg" alt="credit-card"
                                                    class="w-px-30" />
                                            </div>
                                        </div>
                                        <div class="ms-3">
                                            <h3 class="mb-0">Rp.
                                                {{ number_format($allInvoice1->sum('harga_total'), 0, ',', '.') }}</h3>
                                            <small class="text-muted">October</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="table-responsive text-nowrap">
                                    <table class="table">
                                        <tbody class="table-border-bottom-0">
                                            <tr>
                                                <td class="ps-0 pe-5">
                                                    <span class="badge badge-dot bg-success me-2"></span>
                                                    <span class="text-heading">PAID</span>
                                                </td>
                                                <td class="ps-5 d-flex justify-content-end">
                                                    <span class="text-heading fw-semibold">Rp.
                                                        {{ number_format($paidInvoice1->sum('amount'), 0, ',', '.') }}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="ps-0 pe-5">
                                                    <span class="badge badge-dot bg-primary me-2"></span><span
                                                        class="text-heading">UNPAID</span>
                                                </td>
                                                <td class="ps-5 d-flex justify-content-end">
                                                    <span class="text-heading fw-semibold">Rp.
                                                        {{ number_format($unpaidGeneral1, 0, ',', '.') }}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="ps-0 pe-5">
                                                    <span class="badge badge-dot bg-warning me-2"></span><span
                                                        class="text-heading">OUTSTANDING</span>
                                                </td>
                                                <td class="ps-5 d-flex justify-content-end">
                                                    <span class="text-heading fw-semibold">Rp.
                                                        {{ number_format($outstandingInvoice1->sum('amount'), 0, ',', '.') }}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="ps-0 pe-5">
                                                    <span class="badge badge-dot bg-danger me-2"></span><span
                                                        class="text-heading">OVERDUE</span>
                                                </td>
                                                <td class="ps-5 d-flex justify-content-end">
                                                    <span class="text-heading fw-semibold">Rp.
                                                        {{ number_format($overdueInvoice1->sum('amount'), 0, ',', '.') }}</span>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--/ General Statistics -->
                    <!-- General Statistics -->
                    <div class="col-lg-6 col-xl-4 mb-4">
                        <div class="card h-100">
                            <div class="card-header d-flex align-items-center justify-content-between">
                                <div class="ms-2">
                                    <h4 class="mb-0">Reftech</h4>
                                    <small class="text-muted">Invoice & AR Summary</small>
                                </div>
                                <div class="dropdown">
                                    <button class="btn p-0" type="button" id="generalStatistics"
                                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="mdi mdi-dots-vertical mdi-24px"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="generalStatistics">
                                        <a class="dropdown-item" href="javascript:void(0);">Last 28 Days</a>
                                        <a class="dropdown-item" href="javascript:void(0);">Last Month</a>
                                        <a class="dropdown-item" href="javascript:void(0);">Last Year</a>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body pb-3">
                                <div class="mb-4 mt-2">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-md">
                                            <div class="avatar-initial bg-label-primary rounded">
                                                <img src="../../assets//svg/icons/credit-card.svg" alt="credit-card"
                                                    class="w-px-30" />
                                            </div>
                                        </div>
                                        <div class="ms-3">
                                            <h3 class="mb-0">Rp.
                                                {{ number_format($allInvoice1->where('info', 'Reftech')->sum('harga_total'), 0, ',', '.') }}
                                            </h3>
                                            <small class="text-muted">October</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="table-responsive text-nowrap">
                                    <table class="table">
                                        <tbody class="table-border-bottom-0">
                                            <tr>
                                                <td class="ps-0 pe-5">
                                                    <span class="badge badge-dot bg-success me-2"></span>
                                                    <span class="text-heading">PAID</span>
                                                </td>
                                                <td class="ps-5 d-flex justify-content-end">
                                                    <span class="text-heading fw-semibold">Rp.
                                                        {{ number_format($paidInvoice1->where('info', 'Reftech')->sum('amount'), 0, ',', '.') }}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="ps-0 pe-5">
                                                    <span class="badge badge-dot bg-primary me-2"></span><span
                                                        class="text-heading">UNPAID</span>
                                                </td>
                                                <td class="ps-5 d-flex justify-content-end">
                                                    <span class="text-heading fw-semibold">Rp.
                                                        {{ number_format($unpaidReftech1, 0, ',', '.') }}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="ps-0 pe-5">
                                                    <span class="badge badge-dot bg-warning me-2"></span><span
                                                        class="text-heading">OUTSTANDING</span>
                                                </td>
                                                <td class="ps-5 d-flex justify-content-end">
                                                    <span class="text-heading fw-semibold">Rp.
                                                        {{ number_format($outstandingInvoice1->where('info', 'Reftech')->sum('amount'), 0, ',', '.') }}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="ps-0 pe-5">
                                                    <span class="badge badge-dot bg-danger me-2"></span><span
                                                        class="text-heading">OVERDUE</span>
                                                </td>
                                                <td class="ps-5 d-flex justify-content-end">
                                                    <span class="text-heading fw-semibold">Rp.
                                                        {{ number_format($overdueInvoice1->where('info', 'Reftech')->sum('amount'), 0, ',', '.') }}</span>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--/ General Statistics -->
                    <!-- General Statistics -->
                    <div class="col-lg-6 col-xl-4 mb-4">
                        <div class="card h-100">
                            <div class="card-header d-flex align-items-center justify-content-between">
                                <div class="ms-2">
                                    <h4 class="mb-0">Kojisha</h4>
                                    <small class="text-muted">Invoice & AR Summary</small>
                                </div>
                                <div class="dropdown">
                                    <button class="btn p-0" type="button" id="generalStatistics"
                                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="mdi mdi-dots-vertical mdi-24px"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="generalStatistics">
                                        <a class="dropdown-item" href="javascript:void(0);">Last 28 Days</a>
                                        <a class="dropdown-item" href="javascript:void(0);">Last Month</a>
                                        <a class="dropdown-item" href="javascript:void(0);">Last Year</a>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body pb-3">
                                <div class="mb-4 mt-2">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-md">
                                            <div class="avatar-initial bg-label-primary rounded">
                                                <img src="../../assets//svg/icons/credit-card.svg" alt="credit-card"
                                                    class="w-px-30" />
                                            </div>
                                        </div>
                                        <div class="ms-3">
                                            <h3 class="mb-0">Rp.
                                                {{ number_format($allInvoice1->where('info', 'Kojisha')->sum('harga_total'), 0, ',', '.') }}
                                            </h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="table-responsive text-nowrap">
                                    <table class="table">
                                        <tbody class="table-border-bottom-0">
                                            <tr>
                                                <td class="ps-0 pe-5">
                                                    <span class="badge badge-dot bg-success me-2"></span>
                                                    <span class="text-heading">PAID</span>
                                                </td>
                                                <td class="ps-5 d-flex justify-content-end">
                                                    <span class="text-heading fw-semibold">Rp.
                                                        {{ number_format($paidInvoice1->where('info', 'Kojisha')->sum('amount'), 0, ',', '.') }}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="ps-0 pe-5">
                                                    <span class="badge badge-dot bg-primary me-2"></span><span
                                                        class="text-heading">UNPAID</span>
                                                </td>
                                                <td class="ps-5 d-flex justify-content-end">
                                                    <span class="text-heading fw-semibold">Rp.
                                                        {{ number_format($unpaidKojisha1, 0, ',', '.') }}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="ps-0 pe-5">
                                                    <span class="badge badge-dot bg-warning me-2"></span><span
                                                        class="text-heading">OUTSTANDING</span>
                                                </td>
                                                <td class="ps-5 d-flex justify-content-end">
                                                    <span class="text-heading fw-semibold">Rp.
                                                        {{ number_format($outstandingInvoice1->where('info', 'Kojisha')->sum('amount'), 0, ',', '.') }}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="ps-0 pe-5">
                                                    <span class="badge badge-dot bg-danger me-2"></span><span
                                                        class="text-heading">OVERDUE</span>
                                                </td>
                                                <td class="ps-5 d-flex justify-content-end">
                                                    <span class="text-heading fw-semibold">Rp.
                                                        {{ number_format($overdueInvoice1->where('info', 'Kojisha')->sum('amount'), 0, ',', '.') }}</span>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--/ General Statistics -->
                </div>
            </div>
            <div class="tab-pane fade" id="navs-pills-top-2" role="tabpanel">
                <div class="row mb-3">
                    <!-- General Statistics -->
                    <div class="col-lg-6 col-xl-4 mb-4 ">
                        <div class="card h-100">
                            <div class="card-header d-flex align-items-center justify-content-between">
                                <div class="ms-2">
                                    <h4 class="mb-0">All Companies</h4>
                                    <small class="text-muted">Invoice & AR Summary</small>
                                </div>
                                <div class="dropdown">
                                    <button class="btn p-0" type="button" id="generalStatistics"
                                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="mdi mdi-dots-vertical mdi-24px"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="generalStatistics">
                                        <a class="dropdown-item" href="javascript:void(0);">Last 28 Days</a>
                                        <a class="dropdown-item" href="javascript:void(0);">Last Month</a>
                                        <a class="dropdown-item" href="javascript:void(0);">Last Year</a>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body pb-3">
                                <div class="mb-4 mt-2">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-md">
                                            <div class="avatar-initial bg-label-primary rounded">
                                                <img src="../../assets//svg/icons/credit-card.svg" alt="credit-card"
                                                    class="w-px-30" />
                                            </div>
                                        </div>
                                        <div class="ms-3">
                                            <h3 class="mb-0">Rp.
                                                {{ number_format($allInvoice2->sum('harga_total'), 0, ',', '.') }}
                                            </h3>
                                            <small class="text-muted">October</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="table-responsive text-nowrap">
                                    <table class="table">
                                        <tbody class="table-border-bottom-0">
                                            <tr>
                                                <td class="ps-0 pe-5">
                                                    <span class="badge badge-dot bg-success me-2"></span>
                                                    <span class="text-heading">PAID</span>
                                                </td>
                                                <td class="ps-5 d-flex justify-content-end">
                                                    <span class="text-heading fw-semibold">Rp.
                                                        {{ number_format($paidInvoice2->sum('amount'), 0, ',', '.') }}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="ps-0 pe-5">
                                                    <span class="badge badge-dot bg-primary me-2"></span><span
                                                        class="text-heading">UNPAID</span>
                                                </td>
                                                <td class="ps-5 d-flex justify-content-end">
                                                    <span class="text-heading fw-semibold">Rp.
                                                        {{ number_format($unpaidGeneral2, 0, ',', '.') }}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="ps-0 pe-5">
                                                    <span class="badge badge-dot bg-warning me-2"></span><span
                                                        class="text-heading">OUTSTANDING</span>
                                                </td>
                                                <td class="ps-5 d-flex justify-content-end">
                                                    <span class="text-heading fw-semibold">Rp.
                                                        {{ number_format($outstandingInvoice2->sum('amount'), 0, ',', '.') }}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="ps-0 pe-5">
                                                    <span class="badge badge-dot bg-danger me-2"></span><span
                                                        class="text-heading">OVERDUE</span>
                                                </td>
                                                <td class="ps-5 d-flex justify-content-end">
                                                    <span class="text-heading fw-semibold">Rp.
                                                        {{ number_format($overdueInvoice2->sum('amount'), 0, ',', '.') }}</span>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--/ General Statistics -->
                    <!-- General Statistics -->
                    <div class="col-lg-6 col-xl-4 mb-4">
                        <div class="card h-100">
                            <div class="card-header d-flex align-items-center justify-content-between">
                                <div class="ms-2">
                                    <h4 class="mb-0">Reftech</h4>
                                    <small class="text-muted">Invoice & AR Summary</small>
                                </div>
                                <div class="dropdown">
                                    <button class="btn p-0" type="button" id="generalStatistics"
                                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="mdi mdi-dots-vertical mdi-24px"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="generalStatistics">
                                        <a class="dropdown-item" href="javascript:void(0);">Last 28 Days</a>
                                        <a class="dropdown-item" href="javascript:void(0);">Last Month</a>
                                        <a class="dropdown-item" href="javascript:void(0);">Last Year</a>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body pb-3">
                                <div class="mb-4 mt-2">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-md">
                                            <div class="avatar-initial bg-label-primary rounded">
                                                <img src="../../assets//svg/icons/credit-card.svg" alt="credit-card"
                                                    class="w-px-30" />
                                            </div>
                                        </div>
                                        <div class="ms-3">
                                            <h3 class="mb-0">Rp.
                                                {{ number_format($allInvoice2->where('info', 'Reftech')->sum('harga_total'), 0, ',', '.') }}
                                            </h3>
                                            <small class="text-muted">October</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="table-responsive text-nowrap">
                                    <table class="table">
                                        <tbody class="table-border-bottom-0">
                                            <tr>
                                                <td class="ps-0 pe-5">
                                                    <span class="badge badge-dot bg-success me-2"></span>
                                                    <span class="text-heading">PAID</span>
                                                </td>
                                                <td class="ps-5 d-flex justify-content-end">
                                                    <span class="text-heading fw-semibold">Rp.
                                                        {{ number_format($paidInvoice2->where('info', 'Reftech')->sum('amount'), 0, ',', '.') }}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="ps-0 pe-5">
                                                    <span class="badge badge-dot bg-primary me-2"></span><span
                                                        class="text-heading">UNPAID</span>
                                                </td>
                                                <td class="ps-5 d-flex justify-content-end">
                                                    <span class="text-heading fw-semibold">Rp.
                                                        {{ number_format($unpaidReftech2, 0, ',', '.') }}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="ps-0 pe-5">
                                                    <span class="badge badge-dot bg-warning me-2"></span><span
                                                        class="text-heading">OUTSTANDING</span>
                                                </td>
                                                <td class="ps-5 d-flex justify-content-end">
                                                    <span class="text-heading fw-semibold">Rp.
                                                        {{ number_format($outstandingInvoice2->where('info', 'Reftech')->sum('amount'), 0, ',', '.') }}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="ps-0 pe-5">
                                                    <span class="badge badge-dot bg-danger me-2"></span><span
                                                        class="text-heading">OVERDUE</span>
                                                </td>
                                                <td class="ps-5 d-flex justify-content-end">
                                                    <span class="text-heading fw-semibold">Rp.
                                                        {{ number_format($overdueInvoice2->where('info', 'Reftech')->sum('amount'), 0, ',', '.') }}</span>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--/ General Statistics -->
                    <!-- General Statistics -->
                    <div class="col-lg-6 col-xl-4 mb-4">
                        <div class="card h-100">
                            <div class="card-header d-flex align-items-center justify-content-between">
                                <div class="ms-2">
                                    <h4 class="mb-0">Kojisha</h4>
                                    <small class="text-muted">Invoice & AR Summary</small>
                                </div>
                                <div class="dropdown">
                                    <button class="btn p-0" type="button" id="generalStatistics"
                                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="mdi mdi-dots-vertical mdi-24px"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="generalStatistics">
                                        <a class="dropdown-item" href="javascript:void(0);">Last 28 Days</a>
                                        <a class="dropdown-item" href="javascript:void(0);">Last Month</a>
                                        <a class="dropdown-item" href="javascript:void(0);">Last Year</a>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body pb-3">
                                <div class="mb-4 mt-2">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-md">
                                            <div class="avatar-initial bg-label-primary rounded">
                                                <img src="../../assets//svg/icons/credit-card.svg" alt="credit-card"
                                                    class="w-px-30" />
                                            </div>
                                        </div>
                                        <div class="ms-3">
                                            <h3 class="mb-0">Rp.
                                                {{ number_format($allInvoice2->where('info', 'Kojisha')->sum('harga_total'), 0, ',', '.') }}
                                            </h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="table-responsive text-nowrap">
                                    <table class="table">
                                        <tbody class="table-border-bottom-0">
                                            <tr>
                                                <td class="ps-0 pe-5">
                                                    <span class="badge badge-dot bg-success me-2"></span>
                                                    <span class="text-heading">PAID</span>
                                                </td>
                                                <td class="ps-5 d-flex justify-content-end">
                                                    <span class="text-heading fw-semibold">Rp.
                                                        {{ number_format($paidInvoice2->where('info', 'Kojisha')->sum('amount'), 0, ',', '.') }}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="ps-0 pe-5">
                                                    <span class="badge badge-dot bg-primary me-2"></span><span
                                                        class="text-heading">UNPAID</span>
                                                </td>
                                                <td class="ps-5 d-flex justify-content-end">
                                                    <span class="text-heading fw-semibold">Rp.
                                                        {{ number_format($unpaidKojisha2, 0, ',', '.') }}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="ps-0 pe-5">
                                                    <span class="badge badge-dot bg-warning me-2"></span><span
                                                        class="text-heading">OUTSTANDING</span>
                                                </td>
                                                <td class="ps-5 d-flex justify-content-end">
                                                    <span class="text-heading fw-semibold">Rp.
                                                        {{ number_format($outstandingInvoice2->where('info', 'Kojisha')->sum('amount'), 0, ',', '.') }}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="ps-0 pe-5">
                                                    <span class="badge badge-dot bg-danger me-2"></span><span
                                                        class="text-heading">OVERDUE</span>
                                                </td>
                                                <td class="ps-5 d-flex justify-content-end">
                                                    <span class="text-heading fw-semibold">Rp.
                                                        {{ number_format($overdueInvoice2->where('info', 'Kojisha')->sum('amount'), 0, ',', '.') }}</span>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--/ General Statistics -->
                </div>
            </div>
        </div>
    </div>

    <div class="card app-calendar-wrapper">
        <div class="row gy-4">
            <!-- Calendar Sidebar -->
            <div class="col app-calendar-sidebar pt-1" id="app-calendar-sidebar">
                {{-- <div class="p-3 pb-2 my-sm-0 mb-3">
                        <div class="d-grid">
                            <button class="btn btn-primary btn-toggle-sidebar" data-bs-toggle="offcanvas"
                                data-bs-target="#addEventSidebar" aria-controls="addEventSidebar">
                                <i class="mdi mdi-plus me-1"></i>
                                <span class="align-middle">Add Event</span>
                            </button>
                        </div>
                    </div> --}}
                <div class="p-4">
                    <!-- inline calendar (flatpicker) -->
                    <div class="inline-calendar"></div>

                    <hr class="container-m-nx my-4" />

                    <!-- Filter -->
                    <div class="mb-4">
                        <small class="text-small text-muted text-uppercase align-middle">Filter</small>
                    </div>

                    <div class="form-check form-check-secondary mb-3">
                        <input class="form-check-input select-all" type="checkbox" id="selectAll" data-value="all"
                            checked />
                        <label class="form-check-label" for="selectAll">View All</label>
                    </div>

                    <div class="app-calendar-events-filter">
                        <div class="form-check form-check-danger mb-3">
                            <input class="form-check-input input-filter" type="checkbox" id="select-business"
                                data-value="Business" checked />
                            <label class="form-check-label" for="select-business">Due Date</label>
                        </div>
                        <div class="form-check form-check-primary mb-3">
                            <input class="form-check-input input-filter" type="checkbox" id="select-holiday"
                                data-value="Holiday" checked />
                            <label class="form-check-label" for="select-holiday">Reftech</label>
                        </div>
                        <div class="form-check form-check-secondary mb-3">
                            <input class="form-check-input input-filter" type="checkbox" id="select-personal"
                                data-value="Personal" checked />
                            <label class="form-check-label" for="select-personal">Kojisha</label>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /Calendar Sidebar -->

            <!-- Calendar & Modal -->
            <div class="col app-calendar-content">
                <div class="card shadow-none border-0 border-start rounded-0">
                    <div class="card-body pb-0">
                        <!-- FullCalendar -->
                        <div id="calendar"></div>
                    </div>
                </div>
                <div class="app-overlay"></div>
                <!-- FullCalendar Offcanvas -->
                <div class="offcanvas offcanvas-end event-sidebar" tabindex="-1" id="addEventSidebar"
                    aria-labelledby="addEventSidebarLabel">
                    <div class="offcanvas-header">
                        <h5 class="offcanvas-title" id="addEventSidebarLabel">Add Event</h5>
                        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"
                            aria-label="Close"></button>
                    </div>
                    <div class="offcanvas-body">
                        <form class="event-form pt-0" id="eventForm" onsubmit="return false">
                            {{-- <div class="form-floating form-floating-outline mb-4">
                                    <input type="text" class="form-control" id="eventTitle" name="eventTitle"
                                        placeholder="Event Title" />
                                    <label for="eventTitle">Client</label>
                                </div> --}}
                            <div class="form-floating form-floating-outline mb-4">
                                <input type="text" class="form-control" id="eventEndDate" name="eventEndDate"
                                    placeholder="End Date" />
                                <label for="eventEndDate">Follow Up Date</label>
                            </div>
                            <div class="form-floating form-floating-outline mb-4">
                                <textarea class="form-control" name="eventNote" id="eventNote"></textarea>
                                <label for="eventNote">Reminder Note</label>
                            </div>
                            <input class="form-control" type="text" name="eventComp" id="eventComp"
                                value="" hidden>
                            <div class="form-floating mb-4">
                                <p id="eventNoteBefore"></p>
                            </div>
                            <div class="mb-3 d-flex justify-content-sm-between justify-content-start my-4 gap-2">
                                <div class="d-flex">
                                    <button type="submit"
                                        class="btn btn-primary btn-add-event me-sm-2 me-1">Add</button>
                                    <button type="reset" class="btn btn-label-secondary btn-cancel me-sm-0 me-1"
                                        data-bs-dismiss="offcanvas">
                                        Cancel
                                    </button>
                                </div>
                                {{-- <button class="btn btn-label-danger btn-delete-event d-none">Delete</button> --}}
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- /Calendar & Modal -->
        </div>

    </div>

    <h5>Request</h5>
    <div class="card mb-3">
        <div class="table-responsive text-nowrap">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Invoice</th>
                        <th>Customer</th>
                        <th>Nominal</th>
                        <th>Date</th>
                        <th>Follow Up</th>
                        <th>Reminder</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($reminder as $item)
                        <tr>
                            <td>{{ $item->no_invoice }}</td>
                            <td>{{ $item->company }}</td>
                            <td>Rp. {{ number_format($item->amount, 0, ',', '.') }}</td>
                            <td>{{ $item->date }}</td>
                            <td>{{ $item->date_fu }}</td>
                            <td>{{ $item->reminder }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">Kamu Belum Melakukan Reminder.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <h5>Recent Invoice</h5>
    <div class="card mb-3">
        <div class="table-responsive text-nowrap">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>No. Invoice</th>
                        <th>Customer</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Flag</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Selling Contract</td>
                        <td>PT. XYZ</td>
                        <td>Rp. 1.000.000</td>
                        <td>xx-xx-xxxx</td>
                        <td>Full Paid</td>
                        <td>RJO</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    @endif
    @foreach ($notulens as $notulen)
        @include('components.modal.notulen.detail')
    @endforeach
@endsection
@push('after-style')
    {{-- All --}}
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
    <link rel="stylesheet"
        href="{{ asset('assets') }}/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
    <link rel="stylesheet"
        href="{{ asset('assets') }}/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/css/pages/app-calendar.css" />

    {{-- sales --}}
    @if (Auth::user()->role == 'Sales' || Auth::user()->role == 'Support')
    @endif

    {{-- admin --}}
    @if (Auth::user()->role == 'Admin')
        <link rel="stylesheet" href="{{ asset('assets') }}/vendor/css/pages/cards-statistics.css" />
        <link rel="stylesheet" href="{{ asset('assets') }}/vendor/css/pages/cards-analytics.css" />
        <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/apex-charts/apex-charts.css" />
    @endif

    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/fullcalendar/fullcalendar.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/flatpickr/flatpickr.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/swiper/swiper.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/select2/select2.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/quill/editor.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/formvalidation/dist/css/formValidation.min.css" />

    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/animate-css/animate.css" />
@endpush

@push('after-script')
    <!-- Vendors JS -->
    <script src="{{ asset('assets') }}/vendor/libs/formvalidation/dist/js/FormValidation.min.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/select2/select2.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/flatpickr/flatpickr.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/fullcalendar/fullcalendar.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/moment/moment.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/apex-charts/apexcharts.js"></script>
    {{-- sales --}}
    {{-- sales --}}
    @if (Auth::user()->role == 'Sales' || Auth::user()->role == 'Support')
        <script></script>
    @endif
@endpush
@push('page-script')
    <!-- Page JS -->
    <script src="{{ asset('assets') }}/js/ui-modals.js"></script>

    @if (Auth::user()->role == 'Sales' || Auth::user()->role == 'Support')
        <script src="{{ asset('assets') }}/js/tables-datatables-basic.js"></script>
        <script src="{{ asset('assets') }}/js/dashboards-crm.js"></script>
        <script src="{{ asset('assets') }}/js/app-calendar.js"></script>
        <script src="{{ asset('assets') }}/includes/chart/card-monthly.js"></script>
        <script src="{{ asset('assets') }}/vendor/libs/moment/moment.js"></script>
        <script src="{{ asset('assets') }}/includes/table-hot-prospect-sales.js"></script>
        <script src="{{ asset('assets') }}/includes/table-req-visit-sales.js"></script>
    @endif

    @if (Auth::user()->role == 'Accounting')
        <script src="{{ asset('assets') }}/js/app-calendar-accounting.js"></script>
    @endif
    @if (Auth::user()->role == 'Coordinator')
        <script src="{{ asset('assets') }}/includes/table-req-visit-accept.js"></script>
        <script src="{{ asset('assets') }}/includes/table-req-visit-service.js"></script>
    @endif
    <script src="{{ asset('assets') }}/includes/table-hot-prospect.js"></script>
    @if (Auth::user()->role == 'Admin')
        <script src="{{ asset('assets') }}/includes/table-hot-prospect-dashboard.js"></script>
    @endif

    <script src="{{ asset('assets') }}/includes/table-product-sales.js"></script>
    {{-- <script src="{{ asset('assets') }}/includes/table-product.js"></script> --}}
    <script src="{{ asset('assets') }}/includes/table-product-logistic.js"></script>

    <script src="{{ asset('assets') }}/includes/table-reports-admin.js"></script>

    <script src="{{ asset('assets') }}/includes/table-reports.js"></script>
    <script src="{{ asset('assets') }}/includes/table-reports-monitor.js"></script>
    <script src="{{ asset('assets') }}/includes/table-notulen.js"></script>
    <script src="{{ asset('assets') }}/includes/table-search-sales-list-admin.js"></script>
    <script src="{{ asset('assets') }}/includes/table-search-new-order-admin.js"></script>
    <script src="{{ asset('assets') }}/includes/table-search-sales-delivery-admin.js"></script>
    <script src="{{ asset('assets') }}/includes/table-search-sales-completed-admin.js"></script>
    <script src="{{ asset('assets') }}/js/app-calendar-events.js"></script>

    <script src="{{ asset('assets') }}/includes/table-client-compressor.js"></script>
    <script src="{{ asset('assets') }}/includes/table-monitoring-machine-dashboard.js"></script>
    <script src="{{ asset('assets') }}/includes/table-monitoring-issue-dashboard.js"></script>
    {{-- @if (Auth::user()->role == 'Admin') --}}
    <script>
        function formatNumber(n) {
            return n.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ".")
        }

        function validateFloatInputAkurasi(input) {
            let value = input.value;

            // Ganti titik ke koma otomatis
            value = value.replace('.', ',');

            // Hapus karakter selain angka dan koma
            value = value.replace(/[^0-9,]/g, '');

            // Hapus koma lebih dari satu
            let parts = value.split(',');
            if (parts.length > 2) {
                value = parts[0] + ',' + parts[1];
            }

            // Batasi maksimum 5,0
            if (value !== '') {
                let number = parseFloat(value.replace(',', '.'));
                if (!isNaN(number) && number > 5) {
                    value = '5,0';
                }
            }

            input.value = value;

            // Ambil nilai dari kedua input
            let airendEl = document.getElementById('airend');
            let kojishaEl = document.getElementById('kojisha');
            let averageEl = document.getElementById('average');
            let averageTextEl = document.getElementById('averageText');

            if (airendEl && kojishaEl && averageEl) {
                let airend = airendEl.value.replace(',', '.');
                let kojisha = kojishaEl.value.replace(',', '.');

                let a = parseFloat(airend);
                let b = parseFloat(kojisha);

                if (!isNaN(a) && !isNaN(b)) {
                    let avg = (a + b) / 2;
                    let avgStr = avg.toFixed(1).replace('.', ',');
                    averageEl.value = avgStr;
                    averageTextEl.textContent = avgStr;
                } else {
                    averageEl.value = '';
                    averageTextEl.textContent = '';
                }
            }
        }

        function validateFloatInputDelivery(input) {
            let value = input.value;

            // Ganti titik ke koma otomatis
            value = value.replace('.', ',');

            // Hapus karakter selain angka dan koma
            value = value.replace(/[^0-9,]/g, '');

            // Hapus koma lebih dari satu
            let parts = value.split(',');
            if (parts.length > 2) {
                value = parts[0] + ',' + parts[1];
            }

            // Batasi maksimum 5,0
            if (value !== '') {
                let number = parseFloat(value.replace(',', '.'));
                if (!isNaN(number) && number > 5) {
                    value = '5,0';
                }
            }

            input.value = value;

            // Ambil nilai dari kedua input
            let airendEl = document.getElementById('airendDelivery');
            let kojishaEl = document.getElementById('kojishaDelivery');
            let averageEl = document.getElementById('averageDelivery');
            let averageTextEl = document.getElementById('averageDeliveryText');

            if (airendEl && kojishaEl && averageEl) {
                let airend = airendEl.value.replace(',', '.');
                let kojisha = kojishaEl.value.replace(',', '.');

                let a = parseFloat(airend);
                let b = parseFloat(kojisha);

                if (!isNaN(a) && !isNaN(b)) {
                    let avg = (a + b) / 2;
                    let avgStr = avg.toFixed(1).replace('.', ',');
                    averageEl.value = avgStr;
                    averageTextEl.textContent = avgStr;
                } else {
                    averageEl.value = '';
                    averageTextEl.textContent = '';
                }
            }
        }

        function validateFloatInputResponse(input) {
            let value = input.value;

            // Ganti titik ke koma otomatis
            value = value.replace('.', ',');

            // Hapus karakter selain angka dan koma
            value = value.replace(/[^0-9,]/g, '');

            // Hapus koma lebih dari satu
            let parts = value.split(',');
            if (parts.length > 2) {
                value = parts[0] + ',' + parts[1];
            }

            // Batasi maksimum 5,0
            if (value !== '') {
                let number = parseFloat(value.replace(',', '.'));
                if (!isNaN(number) && number > 100) {
                    value = '100';
                }
            }

            input.value = value;

            // Ambil nilai dari kedua input
            let airendEl = document.getElementById('airendResponse');
            let kojishaEl = document.getElementById('kojishaResponse');
            let averageEl = document.getElementById('averageResponse');
            let averageTextEl = document.getElementById('averageResponseText');

            if (airendEl && kojishaEl && averageEl) {
                let airend = airendEl.value.replace(',', '.');
                let kojisha = kojishaEl.value.replace(',', '.');

                let a = parseFloat(airend);
                let b = parseFloat(kojisha);

                if (!isNaN(a) && !isNaN(b)) {
                    let avg = (a + b) / 2;
                    let avgStr = avg.toFixed(1).replace('.', ',');
                    averageEl.value = avgStr;
                    averageTextEl.textContent = avgStr + '%';
                } else {
                    averageEl.value = '';
                    averageTextEl.textContent = '';
                }
            }
        }

        function validateFloatInputRating(input) {
            let value = input.value;

            // Ganti titik ke koma otomatis
            value = value.replace('.', ',');

            // Hapus karakter selain angka dan koma
            value = value.replace(/[^0-9,]/g, '');

            // Hapus koma lebih dari satu
            let parts = value.split(',');
            if (parts.length > 2) {
                value = parts[0] + ',' + parts[1];
            }

            // Batasi maksimum 5,0
            if (value !== '') {
                let number = parseFloat(value.replace(',', '.'));
                if (!isNaN(number) && number > 5) {
                    value = '5,0';
                }
            }

            input.value = value;

            // Ambil nilai dari kedua input
            let airendEl = document.getElementById('airendRating');
            let kojishaEl = document.getElementById('kojishaRating');
            let averageEl = document.getElementById('averageRating');
            let averageTextEl = document.getElementById('averageRatingText');

            if (airendEl && kojishaEl && averageEl) {
                let airend = airendEl.value.replace(',', '.');
                let kojisha = kojishaEl.value.replace(',', '.');

                let a = parseFloat(airend);
                let b = parseFloat(kojisha);

                if (!isNaN(a) && !isNaN(b)) {
                    let avg = (a + b) / 2;
                    let avgStr = avg.toFixed(1).replace('.', ',');
                    averageEl.value = avgStr;
                    averageTextEl.textContent = avgStr;
                } else {
                    averageEl.value = '';
                    averageTextEl.textContent = '';
                }
            }
        }

        function validateFloatInputCustomer(input) {
            let value = input.value;

            // Ganti titik ke koma otomatis
            value = value.replace('.', ',');

            // Hapus karakter selain angka dan koma
            value = value.replace(/[^0-9,]/g, '');

            // Hapus koma lebih dari satu
            let parts = value.split(',');
            if (parts.length > 2) {
                value = parts[0] + ',' + parts[1];
            }

            // Batasi maksimum 5,0
            if (value !== '') {
                let number = parseFloat(value.replace(',', '.'));
                if (!isNaN(number) && number > 5) {
                    value = '5,0';
                }
            }

            input.value = value;

            // Ambil nilai dari kedua input
            let airendEl = document.getElementById('airendCustomer');
            let kojishaEl = document.getElementById('kojishaCustomer');
            let averageEl = document.getElementById('averageCustomer');
            let averageTextEl = document.getElementById('averageCustomerText');

            if (airendEl && kojishaEl && averageEl) {
                let airend = airendEl.value.replace(',', '.');
                let kojisha = kojishaEl.value.replace(',', '.');

                let a = parseFloat(airend);
                let b = parseFloat(kojisha);

                if (!isNaN(a) && !isNaN(b)) {
                    let avg = (a + b) / 2;
                    let avgStr = avg.toFixed(1).replace('.', ',');
                    averageEl.value = avgStr;
                    averageTextEl.textContent = avgStr;
                } else {
                    averageEl.value = '';
                    averageTextEl.textContent = '';
                }
            }
        }

        function validateMaxInput(input) {
            let value = input.value;

            // Ganti titik ke koma otomatis
            value = value.replace('.', ',');

            // Hapus karakter selain angka dan koma
            value = value.replace(/[^0-9,]/g, '');

            // Hapus koma lebih dari satu
            let parts = value.split(',');
            if (parts.length > 2) {
                value = parts[0] + ',' + parts[1];
            }

            // Batasi maksimum 5,0
            if (value !== '') {
                let number = parseFloat(value.replace(',', '.'));
                if (!isNaN(number) && number > 3) {
                    value = 3;
                }
            }

            input.value = value;
        }

        $('.change-sales').on('click', function(ev) {
            var id = $(this).data('id');
            console.log('sales ini ber id : ' + id);

            // Ajax Sales Kiri
            $.ajax({
                url: '/dashboard/filteredLeads/' + id,
                type: 'GET',
                success: function(response, id) {
                    // console.log(id);
                    $(`.filtered-leads`).text(response);
                }
            });
            $.ajax({
                url: '/dashboard/filteredPercentLeads/' + id,
                type: 'GET',
                success: function(response) {
                    $(`.filtered-percent-leads`).text(response + '%');
                }
            });
            $.ajax({
                url: '/dashboard/filteredTargetLeads/' + id,
                type: 'GET',
                success: function(response) {
                    $(`.filtered-target-leads`).text('/ ' + response.leads);
                    console.log(response.leads);
                }
            });
            $.ajax({
                url: '/dashboard/filteredDc/' + id,
                type: 'GET',
                success: function(response) {
                    $(`.filtered-dc`).text(response);
                }
            });
            $.ajax({
                url: '/dashboard/filteredPercentDc/' + id,
                type: 'GET',
                success: function(response) {
                    $(`.filtered-percent-dc`).text(response + '%');
                }
            });
            $.ajax({
                url: '/dashboard/filteredTargetDc/' + id,
                type: 'GET',
                success: function(response) {
                    $(`.filtered-target-dc`).text('/ ' + response.dc);
                }
            });
            $.ajax({
                url: '/dashboard/filteredCRM/' + id,
                type: 'GET',
                success: function(response) {
                    $(`.filtered-crm`).text(response);
                }
            });
            $.ajax({
                url: '/dashboard/filteredPercentCRM/' + id,
                type: 'GET',
                success: function(response) {
                    $(`.filtered-percent-crm`).text(response + ' %');
                }
            });
            $.ajax({
                url: '/dashboard/filteredTargetCRM/' + id,
                type: 'GET',
                success: function(response) {
                    $(`.filtered-target-crm`).text('/ ' + response);
                }
            });
            $.ajax({
                url: '/dashboard/filteredQuote/' + id,
                type: 'GET',
                success: function(response) {
                    $(`.filtered-quote`).text(response);
                }
            });
            $.ajax({
                url: '/dashboard/filteredPercentQuote/' + id,
                type: 'GET',
                success: function(response) {
                    $(`.filtered-percent-quote`).text(response + '%');
                }
            });
            $.ajax({
                url: '/dashboard/filteredTargetQuote/' + id,
                type: 'GET',
                success: function(response) {
                    $(`.filtered-target-quote`).text('/ ' + response.quote);
                }
            });
            $.ajax({
                url: '/dashboard/filteredProspectAdmin/' + id,
                type: 'GET',
                success: function(response) {
                    $(`.filtered-prospect-sales`).text(response);
                }
            });
            $.ajax({
                url: '/dashboard/filteredPercentProspectAdmin/' + id,
                type: 'GET',
                success: function(response) {
                    $(`.filtered-percent-prospect-sales`).text(response + '%');
                }
            });
            $.ajax({
                url: '/dashboard/filteredAllProspect/' + id,
                type: 'GET',
                success: function(response) {
                    $(`.filtered-all-prospect`).text('/ ' + response);
                }
            });

            // Ajax Sales Kanan
            $.ajax({
                url: '/dashboard/totalQuotation/' + id,
                type: 'GET',
                success: function(response) {
                    total = formatNumber(response);
                    $(`.admin-total-quotation`).text('Rp ' + total);
                }
            });
            $.ajax({
                url: '/dashboard/totalProspect/' + id,
                type: 'GET',
                success: function(response) {
                    total = formatNumber(response);
                    $(`.admin-total-prospect`).text('Rp ' + total);
                }
            });
            $.ajax({
                url: '/dashboard/totalHotProspect/' + id,
                type: 'GET',
                success: function(response) {
                    total = formatNumber(response);
                    $(`.admin-total-hot-prospect`).text('Rp ' + total);
                }
            });
            $.ajax({
                url: '/dashboard/totalLoss/' + id,
                type: 'GET',
                success: function(response) {
                    total = formatNumber(response);
                    $(`.admin-total-loss`).text('Rp ' + total);
                }
            });
            $.ajax({
                url: '/dashboard/totalPo/' + id,
                type: 'GET',
                success: function(response) {
                    total = formatNumber(response);
                    $(`.admin-total-po`).text('Rp ' + total);
                }
            });
            $.ajax({
                url: '/dashboard/totalTargetPo/' + id,
                type: 'GET',
                success: function(response) {
                    total = formatNumber(response);
                    let color = 'danger';
                    if (response > 80 && response <= 100) {
                        color = 'warning';
                    } else if (response > 100) {
                        color = 'success';
                    }

                    // Update class
                    const $el = $(`.admin-target-total-po`);
                    $el.removeClass(
                            'bg-label-danger bg-label-warning bg-label-success')
                        .addClass(`bg-label-${color}`);
                    $(`.admin-target-total-po`).text(response + ' %');
                }
            });

            $.ajax({
                url: '/dashboard/target/' + id,
                type: 'GET',
                success: function(response) {
                    var targetPercentage = (response / 100).toFixed(3);
                    $(`.target-po`).text(targetPercentage + '%');
                    console.log(targetPercentage);
                }
            });
            $.ajax({
                url: '/dashboard/filteredProspectQuote/' + id,
                type: 'GET',
                success: function(response) {
                    $(`.filtered-prospect-quotation`).text(response);
                }
            });
            $.ajax({
                url: '/dashboard/filteredProspectPO/' + id,
                type: 'GET',
                success: function(response) {
                    $(`.filtered-prospect-po`).text(response);
                }
            });
            $.ajax({
                url: '/dashboard/totalProspectPO/' + id,
                type: 'GET',
                success: function(response) {
                    $(`.total-prospect-po`).text('Rp ' + response);
                }
            });
            // Ajax Online Sales
            $.ajax({
                url: '/dashboard/filteredProduct/' + id,
                type: 'GET',
                success: function(response) {
                    $(`.filtered-product`).text(response);
                    $(`.filtered-percent-product`).text(response + ' %');
                }
            });
            $.ajax({
                url: '/dashboard/filteredSW/' + id,
                type: 'GET',
                success: function(response) {
                    const value = parseFloat(response) || 0;
                    $(`.filtered-sw`).text(value);

                    let percent;
                    if (id === 16) {
                        percent = (value / 120) * 100;
                    } else {
                        percent = (value / 60) * 100;
                    }

                    $(`.filtered-percent-sw`).text(percent.toFixed(1) + ' %');
                }
            });
            $.ajax({
                url: '/dashboard/filteredVideo/' + id,
                type: 'GET',
                success: function(response) {
                    $(`.filtered-video`).text(response);
                    $(`.filtered-percent-video`).text(response + ' %');
                }
            });
            $.ajax({
                url: '/dashboard/filteredStat/' + id,
                type: 'GET',
                success: function(response) {
                    $(`.filtered-status`).text(response);
                    const value = parseFloat(response) || 0;
                    let percent;
                    percent = value / 5 * 100;
                    $(`.filtered-percent-status`).text(percent.toFixed(1) + ' %');
                }
            });
            $.ajax({
                url: '/dashboard/filteredDelivery/' + id,
                type: 'GET',
                success: function(response) {
                    $(`.filtered-delivery`).text(response);
                    const value = parseFloat(response) || 0;
                    let percent;
                    percent = value / 5 * 100;
                    $(`.filtered-percent-delivery`).text(percent.toFixed(1) + ' %');
                }
            });
            $.ajax({
                url: '/dashboard/filteredCustomer/' + id,
                type: 'GET',
                success: function(response) {
                    $(`.filtered-customer`).text(response);
                    const value = parseFloat(response) || 0;
                    let percent;
                    percent = value / 5 * 100;
                    $(`.filtered-percent-customer`).text(percent.toFixed(1) + ' %');
                }
            });
            $.ajax({
                url: '/dashboard/filteredResponse/' + id,
                type: 'GET',
                success: function(response) {
                    $(`.filtered-response`).text(response);
                    $(`.filtered-percent-response`).text(response + ' %');
                }
            });
            $.ajax({
                url: '/dashboard/filteredRating/' + id,
                type: 'GET',
                success: function(response) {
                    $(`.filtered-rating`).text(response);
                    const value = parseFloat(response) || 0;
                    let percent;
                    percent = value / 5 * 100;
                    $(`.filtered-percent-rating`).text(percent.toFixed(1) + ' %');
                }
            });

            // Ajax Support
            $.ajax({
                url: '/dashboard/filteredProspect/' + id,
                type: 'GET',
                success: function(response) {
                    $(`.filtered-prospect`).text(response);
                    $(`.filtered-percent-prospect`).text(response + ' %');
                }
            });
            $.ajax({
                url: '/dashboard/filteredProvide/' + id,
                type: 'GET',
                success: function(response) {
                    $(`.filtered-provided`).text(response.provide);
                    $(`.filtered-percent-provided`).text(response.percent + ' %');
                    $(`.filtered-all-prospect-provided`).text(response.prospect);
                }
            });
            $.ajax({
                url: '/dashboard/filteredProspectQuote/' + id,
                type: 'GET',
                success: function(response) {
                    $(`.filtered-quote-prospect`).text(response.quotation);
                    $(`.filtered-percent-quote-prospect`).text(response.percent + ' %');
                    $(`.filtered-all-quote-prospect`).text(response.provide);
                }
            });
            $.ajax({
                url: '/dashboard/filteredNotProvide/' + id,
                type: 'GET',
                success: function(response) {
                    $(`.filtered-not-provided`).text(response.provide);
                    $(`.filtered-percent-not-provided`).text(response.percent + ' %');
                    $(`.filtered-all-prospect-not-provided`).text(response.prospect);
                }
            });
            $.ajax({
                url: '/dashboard/filteredProspectPO/' + id,
                type: 'GET',
                success: function(response) {
                    $(`.filtered-po-prospect`).text(response.po);
                    $(`.filtered-percent-po-prospect`).text(response.percent + ' %');
                    $(`.filtered-all-po-prospect`).text(response.quotation);
                }
            });
            $.ajax({
                url: '/dashboard/totalProspectQuote/' + id,
                type: 'GET',
                success: function(response) {
                    $(`.total-prospect-quotation`).text('Rp ' + response);
                }
            });
            $.ajax({
                url: '/dashboard/totalProspectProspect/' + id,
                type: 'GET',
                success: function(response) {
                    $(`.total-prospect-hot`).text('Rp ' + response);
                }
            });
            $.ajax({
                url: '/dashboard/totalProspectPO/' + id,
                type: 'GET',
                success: function(response) {
                    $(`.total-prospect-po`).text('Rp ' + response);
                }
            });

        });

        $(document).ready(function() {
            $('[data-bs-toggle="tooltip"]').tooltip();
            $('.checkPlanning').on('change', function() {
                let isChecked = $(this).is(':checked');

                // Tambah atau hapus class alert-success
                if (isChecked) {
                    $('.alert-planning').addClass('alert-success');
                } else {
                    $('.alert-planning').removeClass('alert-success');
                }

                $.ajax({
                    url: '/check-planning', // sesuaikan dengan route kamu
                    type: 'POST',
                    data: {
                        planing: isChecked ? 1 : 0,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        console.log('Planning status updated:', response);
                    },
                    error: function(xhr) {
                        console.error('Terjadi error:', xhr.responseText);
                    }
                });
            });

            $('.checkSync').on('change', function() {
                let isChecked = $(this).is(':checked');

                // Tambah atau hapus class alert-success
                if (isChecked) {
                    $('.alert-sync').addClass('alert-success');
                } else {
                    $('.alert-sync').removeClass('alert-success');
                }

                $.ajax({
                    url: '/check-sync', // sesuaikan dengan route kamu
                    type: 'POST',
                    data: {
                        sync: isChecked ? 1 : 0,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        console.log('sync status updated:', response);
                    },
                    error: function(xhr) {
                        console.error('Terjadi error:', xhr.responseText);
                    }
                });
            });

            $('.checkAbnormal').on('change', function() {
                let isChecked = $(this).is(':checked');

                // Tambah atau hapus class alert-success
                if (isChecked) {
                    $('.alert-abnormal').addClass('alert-success');
                } else {
                    $('.alert-abnormal').removeClass('alert-success');
                }

                $.ajax({
                    url: '/check-abnormal', // sesuaikan dengan route kamu
                    type: 'POST',
                    data: {
                        abnormal: isChecked ? 1 : 0,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        console.log('abnormal status updated:', response);
                    },
                    error: function(xhr) {
                        console.error('Terjadi error:', xhr.responseText);
                    }
                });
            });

            $('.checkLog').on('change', function() {
                let isChecked = $(this).is(':checked');

                // Tambah atau hapus class alert-success
                if (isChecked) {
                    $('.alert-log').addClass('alert-success');
                } else {
                    $('.alert-log').removeClass('alert-success');
                }

                $.ajax({
                    url: '/check-log', // sesuaikan dengan route kamu
                    type: 'POST',
                    data: {
                        log: isChecked ? 1 : 0,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        console.log('log status updated:', response);
                    },
                    error: function(xhr) {
                        console.error('Terjadi error:', xhr.responseText);
                    }
                });
            });

            $('.checkTimeline').on('change', function() {
                let isChecked = $(this).is(':checked');

                // Tambah atau hapus class alert-success
                if (isChecked) {
                    $('.alert-timeline').addClass('alert-success');
                } else {
                    $('.alert-timeline').removeClass('alert-success');
                }

                $.ajax({
                    url: '/check-timeline', // sesuaikan dengan route kamu
                    type: 'POST',
                    data: {
                        timeline: isChecked ? 1 : 0,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        console.log('timeline status updated:', response);
                    },
                    error: function(xhr) {
                        console.error('Terjadi error:', xhr.responseText);
                    }
                });
            });

            $('.checkPreventive').on('change', function() {
                let isChecked = $(this).is(':checked');

                // Tambah atau hapus class alert-success
                if (isChecked) {
                    $('.alert-preventive').addClass('alert-success');
                } else {
                    $('.alert-preventive').removeClass('alert-success');
                }

                $.ajax({
                    url: '/check-preventive', // sesuaikan dengan route kamu
                    type: 'POST',
                    data: {
                        preventive: isChecked ? 1 : 0,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        console.log('preventive status updated:', response);
                    },
                    error: function(xhr) {
                        console.error('Terjadi error:', xhr.responseText);
                    }
                });
            });

            $(document).on('click', '.accept-issue', function() {
                var id = $(this).data('id');
                Swal.fire({
                    title: "Are you sure?",
                    text: "You won't be able to revert this!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Yes, Accept it!",
                    customClass: {
                        confirmButton: "btn btn-primary me-3 waves-effect waves-light",
                        cancelButton: "btn btn-label-secondary waves-effect",
                    },
                    buttonsStyling: false,
                }).then(function(result) {
                    if (result.value) {
                        $.ajax({
                            'url': '{{ url('monitoring-client') }}/accept-issue/' + id,
                            'type': 'POST',
                            'data': {
                                '_token': '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response == 1) {
                                    Swal.fire({
                                        icon: "success",
                                        title: "Accepted!",
                                        text: "Your file has been Accepted.",
                                        customClass: {
                                            confirmButton: "btn btn-success waves-effect",
                                        },
                                    })
                                    window.setTimeout(function() {
                                        window.location.href = '/';
                                    }, 2000);
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Oops...',
                                        text: 'Data Failed to Accept!'
                                    });
                                }
                            }
                        });
                    } else if (result.dismiss === Swal.DismissReason.cancel) {
                        Swal.fire({
                            title: "Cancelled",
                            text: "You Cancel Accept :)",
                            icon: "error",
                            customClass: {
                                confirmButton: "btn btn-success waves-effect",
                            },
                        });
                    }
                });
            });

        });
    </script>
    {{-- @endif --}}
    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script> --}}

@endpush
