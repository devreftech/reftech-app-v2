@extends('layouts.sales.app')
@section('title', 'Detail Overview Sales')
@section('content')
    <h4 class="fw-bold py-3 mb-4">
        Detail Overview {{ $user->name }}, {{ $dates }}
    </h4>
    <div class="card mb-3">
        <div class="card-body">
            <div class="row">
                <div class="col-12 col-md-3">
                    <img src="{{ url('') . '/' . $user->image }}" alt="" srcset="" class="h-100 w-100">
                </div>
                <div class="col-12 col-md-9">
                    @if ($user->id != '16')
                        @if ($user->role == 'Sales')
                            <div class="row">
                                <div class="col-12">
                                    <h4>{{ $user->name }}</h4>
                                </div>
                                <div class="col-4">
                                    <p class="fw-medium fs-normal">Key Performance Indicator</p>
                                    <div class="d-flex mb-2 gap-2">
                                        <a href="#activities">
                                            <div class="avatar">
                                                <div class="avatar-initial bg-label-secondary rounded">
                                                    <i class="mdi mdi-account-multiple-plus-outline mdi-24px"></i>
                                                </div>
                                            </div>
                                        </a>
                                        <div class="card-info">
                                            <h5 class="mb-0">{{ $totalLeads }}<span
                                                    class="text-muted fs-tiny fw-normal">/{{ $target->leads ?? '-' }}</span>
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
                                                    class="text-muted fs-tiny fw-normal">/{{ $target->dc ?? '-' }}</span>
                                            </h5>
                                            <small
                                                class="text-muted">{{ $user->id == '1' ? 'New Leads' : 'Daily Call' }}</small>
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
                                                    class="text-muted fs-tiny fw-normal">/{{ $jumlahCustomer }}</span>
                                            </h5>
                                            <small class="text-muted">CRM</small>
                                        </div>
                                    </div>
                                    @php
                                        $lastDetail = $user->detail->last();
                                    @endphp
                                    @if ($lastDetail && ($lastDetail->area == 'Bekasi' || $lastDetail->area == 'Jabodetabek' || $lastDetail->area == 'Jawa Barat'))
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
                                                        class="text-muted fs-tiny fw-normal">/{{ $target->visit ?? '-' }}</span>
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
                                                    class="text-muted fs-tiny fw-normal">/{{ $target->quote ?? '-' }}</span>
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
                                            <h5 class="mb-0">{{ $totalPO }}
                                            </h5>
                                            <small class="text-muted">Purchase Order</small>
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
                                        <h5 class="mb-0">{{ $totalLoss }}
                                        </h5>
                                        <small class="text-muted">Loss Quotation</small>
                                    </div>
                                </div> --}}
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
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="d-flex mb-2 gap-2">
                                            <div class="avatar">
                                                <div class="avatar-initial bg-label-danger rounded">
                                                    <i class="mdi mdi-cart-minus mdi-24px"></i>
                                                </div>
                                            </div>
                                            <div class="card-info">
                                                <h5 class="mb-0">
                                                    Rp
                                                    {{ number_format($amountQuoteLoss, 2, ',', '.') }}
                                                </h5>
                                                <small class="text-muted">Loss Quotation</small>
                                            </div>
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
                                        <a href="#activities">
                                            <div class="avatar">
                                                <div class="avatar-initial bg-label-info rounded">
                                                    <i class="mdi mdi-phone-outline mdi-24px"></i>
                                                </div>
                                            </div>
                                        </a>
                                        <div class="card-info">
                                            <h5 class="mb-0">{{ $filteredProspect }} <span
                                                    class="text-muted fs-tiny fw-normal">/{{ $target->dc ?? '-' }}</span>
                                            </h5>
                                            <small class="text-muted">Prospect</small>
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
                                            <h5 class="mb-0">{{ $filteredProvide }}<span
                                                    class="text-muted fs-tiny fw-normal">/{{ $jumlahCustomer }}</span>
                                            </h5>
                                            <small class="text-muted">Provided</small>
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
                                            <h5 class="mb-0">{{ $filteredProspectQuote }}<span
                                                    class="text-muted fs-tiny fw-normal">/{{ $target->quote ?? '-' }}</span>
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
                                            <h5 class="mb-0">{{ $filteredProspectQuote }}
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
                                                    {{ number_format($totalProspectPO, 2, ',', '.') }}
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
                                                    {{ number_format($totalProspectQuote, 2, ',', '.') }}
                                                </h5>
                                                <small class="text-muted">Quotation</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @else
                        <div class="row mb-3">
                            <div class="col-8">
                                <div class="badge bg-primary w-100">
                                    <h4 class="text-white text-center my-3">Achievement</h4>
                                </div>
                            </div>
                            <div class="col-4">
                                @php
                                    $jumlah_target = [];
                                    if (isset($target->total) && $target->total != 0) {
                                        $jumlah_target = ($amountSales / $target->total) * 100;
                                    } else {
                                        $jumlah_target = 0;
                                    }
                                @endphp
                                <div class="badge bg-primary w-100">
                                    <h4 class="text-white text-center my-3">
                                        {{ number_format($jumlah_target, 3) }}%
                                    </h4>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-4 mb-3">
                                <div class="badge bg-label-dark w-100">
                                    <h5 class="my-3 text-start">Quotation</h5>
                                </div>
                            </div>
                            <div class="col-2 mb-3">
                                <div class="card shadow-none border-secondary border-2 h-100">
                                    <h5 class="text-center my-3">{{ $totalQuote }}</h5>
                                </div>
                            </div>
                            <div class="col-6 mb-3">
                                <div class="card shadow-none border-secondary border-2 h-100">
                                    <h5 class="text-end my-3 mx-2">Rp {{ number_format($amountQuote, 2, ',', '.') }}</h5>
                                </div>
                            </div>
                            <div class="col-4 mb-3">
                                <div class="badge bg-label-dark w-100">
                                    <h5 class="my-3 text-start">Purchase Order</h5>
                                </div>
                            </div>
                            <div class="col-2 mb-3">
                                <div class="card shadow-none border-secondary border-2 h-100">
                                    <h5 class="text-center my-3">{{ $totalPO }}</h5>
                                </div>
                            </div>
                            <div class="col-6 mb-3">
                                <div class="card shadow-none border-secondary border-2 h-100">
                                    <h5 class="text-end my-3 mx-2">Rp {{ number_format($amountSales, 2, ',', '.') }}</h5>
                                </div>
                            </div>
                            {{-- <div class="col-4 mb-3">
                                <div class="badge bg-label-dark w-100">
                                    <h5 class="my-3 text-start">Hot Prospect</h5>
                                </div>
                            </div>
                            <div class="col-2 mb-3">
                                <div class="card shadow-none border-secondary border-2 h-100">
                                    <h5 class="text-center my-3">{{ $totalProspectPO }}</h5>
                                </div>
                            </div>
                            <div class="col-6 mb-3">
                                <div class="card shadow-none border-secondary border-2 h-100">
                                    <h5 class="text-end my-3 mx-2">Rp {{ number_format($amountProspect, 2, ',', '.') }}
                                    </h5>
                                </div>
                            </div> --}}
                            <div class="col-4 mb-3">
                                <div class="badge bg-label-dark w-100">
                                    <h5 class="my-3 text-start">Loss Quotation</h5>
                                </div>
                            </div>
                            <div class="col-2 mb-3">
                                <div class="card shadow-none border-secondary border-2 h-100">
                                    <h5 class="text-center my-3">{{ $totalLoss }}</h5>
                                </div>
                            </div>
                            <div class="col-6 mb-3">
                                <div class="card shadow-none border-secondary border-2 h-100">
                                    <h5 class="text-end my-3 mx-2">Rp {{ number_format($amountQuoteLoss, 2, ',', '.') }}
                                    </h5>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @if ($user->id == '16')
        <div class="card mb-3">
            <div class="card-header">
                <h5>Key Performance Indicator</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 col-12">
                        <div class="row mb-3">
                            <div class="col-4" style="padding-right: 0;">
                                <div class="card bg-primary text-white w-100" data-bs-toggle="modal"
                                    data-bs-target="#newProduct">
                                    <h5 class="card-title
                                    text-white text-center my-4">
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
                                <div class="card bg-warning text-white w-100">
                                    <h5 class="card-title text-white text-center my-4">
                                        <i class="menu-icon tf-icons mdi mdi-package-variant-closed-check m-0 fs-1"></i>
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
                                <div class="card border-warning bg-warning border-1 w-100 h-100">
                                    <h5 class="card-title text-center text-white my-4">
                                        <i class="menu-icon tf-icons mdi mdi-truck-delivery-outline m-0 fs-1"></i>
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
                                <div class="card bg-warning text-white w-100 ">
                                    <h5 class="card-title text-white text-center my-4">
                                        <i class="menu-icon tf-icons mdi mdi-account-heart-outline m-0 fs-1"></i>
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
                                <div class="card bg-warning text-white w-100">
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
                                <div class="card bg-warning border-warning text-white border-1 w-100 h-100">
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
                                                $jumlahCustomers = 0;
                                            @endphp
                                            @foreach ($customerCount as $item)
                                                @php
                                                    $jumlahCustomers += $item->average;
                                                @endphp
                                            @endforeach
                                            @php
                                                $jumlahCustomers / $dataCustomer;
                                                $persenCustomer = ($jumlahCustomers / 5) * 100;
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
                                <div class="card bg-primary text-white w-100">
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
                                        {{ @$persenSW ?? 0 }} /
                                        {{ Auth::user()->id == 16 ? '120' : '60' }}
                                    </h5>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-4" style="padding-right: 0;">
                                <div class="card bg-primary text-white w-100">
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
                                        <i class="menu-icon tf-icons mdi mdi-account-group  m-0 fs-1"></i>
                                    </h5>
                                </div>
                            </div>

                            <div class="col-8 d-flex flex-column justify-content-between">
                                <div class="card border-success bg-label-success border-1 shadow-none">
                                    <h5 class="card-title text-center my-2">
                                        CRM
                                    </h5>
                                </div>
                                <div class="card border-success bg-label-success border-2 shadow-none mt-auto"
                                    style="border-style: dashed;">
                                    <h5 class="card-title text-center my-2">{{ $totalCRM }}<span
                                            class="text-muted fs-tiny fw-normal">/{{ $jumlahCustomer }}</span>
                                    </h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
    @if ($user->role == 'Sales')
        <div class="row">
            @if ($user->id != '16')
                <div class="col-12 col-md-6 mb-3">
                    <div class="card" id="activities">
                        <div class="card-datatable table-responsive pt-0">
                            <table class="datatable-overview-call table table-striped" id="dataTableCrm">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th></th>
                                        <th>ID</th>
                                        <th>Company</th>
                                        <th>Status</th>
                                        <th>Note</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6 mb-3">
                    <div class="card">
                        <div class="card-datatable table-responsive pt-0">
                            <table class="datatable-overview-crm table table-striped" id="dataTableCrm">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th></th>
                                        <th>ID</th>
                                        <th>Company</th>
                                        <th>Status</th>
                                        <th>Note</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
            <div class="col-12 mb-3">
                <div class="card" id="quote">
                    <div class="card-datatable table-responsive pt-0">
                        <table class="datatable-overview-quotation table table-striped">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th>ID</th>
                                    <th>Quote No.</th>
                                    <th>Company</th>
                                    <th>Total Price</th>
                                    <th>Description</th>
                                    <th>Date Quotation</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-12 mb-3">
                <div class="card" id="quote">
                    <div class="card-datatable table-responsive pt-0">
                        <table class="datatable-overview-po table table-striped">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th>ID</th>
                                    <th>Quote No.</th>
                                    <th>Company</th>
                                    <th>Description</th>
                                    <th>Date PO</th>
                                    <th>Total Price</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-12 mb-3">
                <div class="card" id="quote">
                    <div class="card-datatable table-responsive pt-0">
                        <table class="datatable-overview-loss table table-striped">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th>ID</th>
                                    <th>Quote No.</th>
                                    <th>Company</th>
                                    <th>Description</th>
                                    <th>Date</th>
                                    <th>Total Price</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
            {{-- @if ($user->id == '16')
                <div class="col-12 mb-3">
                    <div class="card" id="quote">
                        <div class="card-datatable table-responsive pt-0">
                            <table class="datatable-overview-loss table table-striped">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th></th>
                                        <th>ID</th>
                                        <th>Quote No.</th>
                                        <th>Company</th>
                                        <th>Description</th>
                                        <th>Date PO</th>
                                        <th>Total Price</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            @endif --}}
            {{-- <div class="col-12 mb-3">
            <div class="card" id="po">
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
                                $key = 0;
                            @endphp
                            @forelse ($quotation as $quote)
                                @php
                                    $totalQ = $quote->nett;
                                    $totalP += $totalQ;
                                @endphp
                                <tr>
                                    <td class="fw-medium">
                                        <a class="text-black"
                                            href="{{ route('quotation.show', $quote->id) }}">{{ $quote->no_quote }}</a>
                                    </td>
                                    <td>{{ $quote->pic->client->company }}</td>
                                    <td>{{ $quote->title }}</td>
                                    <td>{{ \Carbon\Carbon::parse($quote->po_date)->format('d-m-Y') }}</td>
                                    <td class="text-end">Rp
                                        {{ number_format($quote->nett, 0, '', '.') }}</td>
                                </tr>
                                @php
                                    $key++;
                                @endphp
                            @empty
                                <td colspan="5" class="text-center">Kamu belum punya quotation</td>
                            @endforelse
                            <tr class="bg-label-secondary">
                                <td colspan="3">
                                </td>
                                <td><strong>Total</strong></td>
                                <td class="text-end"><strong>Rp {{ number_format($totalP, 0, '', '.') }}</strong>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div> --}}
        </div>
    @else
        <div class="row">
            <div class="col-12 mb-3">
                <div class="card" id="quote">
                    <div class="card-datatable table-responsive pt-0">
                        <table class="datatable-overview-po-prospect table table-striped">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th>ID</th>
                                    <th>Quote No.</th>
                                    <th>Company</th>
                                    <th>Description</th>
                                    <th>Date PO</th>
                                    <th>Total Price</th>
                                    <th>Sales</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endif
    @include('components.modal.onlineSales.kpi.new-product')
@endsection


@push('after-style')
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
    <link rel="stylesheet"
        href="{{ asset('assets') }}/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
    <link rel="stylesheet"
        href="{{ asset('assets') }}/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/animate-css/animate.css">
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-rowgroup-bs5/rowgroup.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/formvalidation/dist/css/formValidation.min.css" />
@endpush

@push('after-script')
    <script src="{{ asset('assets') }}/vendor/libs/moment/moment.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/flatpickr/flatpickr.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/formvalidation/dist/js/FormValidation.min.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
@endpush

@push('page-script')
    <script src="{{ asset('assets') }}/js/tables-datatables-basic.js"></script>
    <script src="{{ asset('assets') }}/includes/table-overview-call.js"></script>
    <script src="{{ asset('assets') }}/includes/table-overview-crm.js"></script>
    <script src="{{ asset('assets') }}/includes/table-overview-quotation.js"></script>
    <script src="{{ asset('assets') }}/includes/table-overview-po.js"></script>
    <script src="{{ asset('assets') }}/includes/table-overview-loss.js"></script>
    <script src="{{ asset('assets') }}/includes/table-overview-po-prospect.js"></script>
@endpush

@push('script')
    <script>
        // Initialize Bootstrap tooltips using jQuery
        $(document).ready(function() {
            $('[data-bs-toggle="tooltip"]').tooltip();
        });
    </script>
@endpush
