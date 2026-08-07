@extends('layouts.sales.app')
@section('title', 'Aging Report')
@section('no-container') @endsection
@section('content')
    <div class="container-fluid px-4 py-3">
        <div class="d-flex flex-wrap align-items-center justify-content-between pb-3 mb-2">
            <h4 class="fw-bold mb-0"><span class="text-muted fw-normal">Account Receivable /</span> Aging Report</h4>
        </div>

        <div class="aging-metrics-pane" data-tab="general">
            <div class="row g-4 mb-4">
                <div class="col-12 col-md-4">
                    <div class="card metric-card border-0" style="border-top: 4px solid #6366f1 !important; cursor: pointer;"
                        data-bs-toggle="modal" data-bs-target="#detailOutstanding">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <span class="metric-label text-primary">Total Outstanding</span>
                                <div class="metric-icon-box m-0" style="width: 42px; height: 42px; border-radius: 10px; display: flex; align-items: center; justify-content: center; background-color: rgba(99, 102, 241, 0.12); color: #6366f1;">
                                    <i class="mdi mdi-receipt-text-outline mdi-24px"></i>
                                </div>
                            </div>
                            <h3 class="metric-value mb-1" style="color: #6366f1;" id="aging-general-outstanding">Rp.
                                {{ number_format($invoice->sum('amount'), 0, ',', '.') }}
                            </h3>
                            <div class="table-responsive text-nowrap border-top mt-3 pt-2">
                                <table class="table table-sm mb-0">
                                    <tbody class="table-border-bottom-0">
                                        <tr>
                                            <td class="pe-5 ps-0"><span class="text-muted">PPN</span></td>
                                            <td class="ps-5 pe-0 d-flex justify-content-end">
                                                <span class="fw-semibold" id="aging-general-outstanding-ppn">Rp. {{ number_format($invoice->filter(fn($i) => $i->tax)->sum('amount'), 0, ',', '.') }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="pe-5 ps-0"><span class="text-muted">Non-PPN</span></td>
                                            <td class="ps-5 pe-0 d-flex justify-content-end">
                                                <span class="fw-semibold" id="aging-general-outstanding-nonppn">Rp. {{ number_format($invoice->filter(fn($i) => !$i->tax)->sum('amount'), 0, ',', '.') }}</span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="card metric-card border-0" style="border-top: 4px solid #ef4444 !important; cursor: pointer;"
                        data-bs-toggle="modal" data-bs-target="#detailOverdue">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <span class="metric-label text-danger">Total Overdue</span>
                                <div class="metric-icon-box m-0" style="width: 42px; height: 42px; border-radius: 10px; display: flex; align-items: center; justify-content: center; background-color: rgba(239, 68, 68, 0.12); color: #ef4444;">
                                    <i class="mdi mdi-alert-octagon-outline mdi-24px"></i>
                                </div>
                            </div>
                            <h3 class="metric-value mb-1" style="color: #ef4444;" id="aging-general-overdue">Rp.
                                {{ number_format($overdue->sum('amount'), 0, ',', '.') }}
                            </h3>
                            <div class="table-responsive text-nowrap border-top mt-3 pt-2">
                                <table class="table table-sm mb-0">
                                    <tbody class="table-border-bottom-0">
                                        <tr>
                                            <td class="pe-5 ps-0"><span class="text-muted">PPN</span></td>
                                            <td class="ps-5 pe-0 d-flex justify-content-end">
                                                <span class="fw-semibold" id="aging-general-overdue-ppn">Rp. {{ number_format($overdue->filter(fn($i) => $i->tax)->sum('amount'), 0, ',', '.') }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="pe-5 ps-0"><span class="text-muted">Non-PPN</span></td>
                                            <td class="ps-5 pe-0 d-flex justify-content-end">
                                                <span class="fw-semibold" id="aging-general-overdue-nonppn">Rp. {{ number_format($overdue->filter(fn($i) => !$i->tax)->sum('amount'), 0, ',', '.') }}</span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="card metric-card border-0" style="border-top: 4px solid #10b981 !important; cursor: pointer;"
                        data-bs-toggle="modal" data-bs-target="#detailOnDue">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <span class="metric-label text-success">Total On Due</span>
                                <div class="metric-icon-box m-0" style="width: 42px; height: 42px; border-radius: 10px; display: flex; align-items: center; justify-content: center; background-color: rgba(16, 185, 129, 0.12); color: #10b981;">
                                    <i class="mdi mdi-calendar-check-outline mdi-24px"></i>
                                </div>
                            </div>
                            <h3 class="metric-value mb-1" style="color: #10b981;" id="aging-general-ondue">Rp.
                                {{ number_format($ondue->sum('amount'), 0, ',', '.') }}
                            </h3>
                            <div class="table-responsive text-nowrap border-top mt-3 pt-2">
                                <table class="table table-sm mb-0">
                                    <tbody class="table-border-bottom-0">
                                        <tr>
                                            <td class="pe-5 ps-0"><span class="text-muted">PPN</span></td>
                                            <td class="ps-5 pe-0 d-flex justify-content-end">
                                                <span class="fw-semibold" id="aging-general-ondue-ppn">Rp. {{ number_format($ondue->filter(fn($i) => $i->tax)->sum('amount'), 0, ',', '.') }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="pe-5 ps-0"><span class="text-muted">Non-PPN</span></td>
                                            <td class="ps-5 pe-0 d-flex justify-content-end">
                                                <span class="fw-semibold" id="aging-general-ondue-nonppn">Rp. {{ number_format($ondue->filter(fn($i) => !$i->tax)->sum('amount'), 0, ',', '.') }}</span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="aging-metrics-pane d-none" data-tab="reftech">
            <div class="row g-4 mb-4">
                <div class="col-12 col-md-4">
                    <div class="card metric-card border-0" style="border-top: 4px solid #6366f1 !important;">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <span class="metric-label text-primary">Total Outstanding</span>
                                <div class="metric-icon-box m-0" style="width: 42px; height: 42px; border-radius: 10px; display: flex; align-items: center; justify-content: center; background-color: rgba(99, 102, 241, 0.12); color: #6366f1;">
                                    <i class="mdi mdi-receipt-text-outline mdi-24px"></i>
                                </div>
                            </div>
                            <h3 class="metric-value mb-0" style="color: #6366f1;" id="aging-reftech-outstanding">Rp.
                                {{ number_format($invoice->where('info', 'Reftech')->sum('amount'), 0, ',', '.') }}
                            </h3>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="card metric-card border-0" style="border-top: 4px solid #ef4444 !important;">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <span class="metric-label text-danger">Total Overdue</span>
                                <div class="metric-icon-box m-0" style="width: 42px; height: 42px; border-radius: 10px; display: flex; align-items: center; justify-content: center; background-color: rgba(239, 68, 68, 0.12); color: #ef4444;">
                                    <i class="mdi mdi-alert-octagon-outline mdi-24px"></i>
                                </div>
                            </div>
                            <h3 class="metric-value mb-0" style="color: #ef4444;" id="aging-reftech-overdue">Rp.
                                {{ number_format($overdue->where('info', 'Reftech')->sum('amount'), 0, ',', '.') }}
                            </h3>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="card metric-card border-0" style="border-top: 4px solid #10b981 !important;">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <span class="metric-label text-success">Total On Due</span>
                                <div class="metric-icon-box m-0" style="width: 42px; height: 42px; border-radius: 10px; display: flex; align-items: center; justify-content: center; background-color: rgba(16, 185, 129, 0.12); color: #10b981;">
                                    <i class="mdi mdi-calendar-check-outline mdi-24px"></i>
                                </div>
                            </div>
                            <h3 class="metric-value mb-0" style="color: #10b981;" id="aging-reftech-ondue">Rp.
                                {{ number_format($ondue->where('info', 'Reftech')->sum('amount'), 0, ',', '.') }}
                            </h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="aging-metrics-pane d-none" data-tab="kojisha">
            <div class="row g-4 mb-4">
                <div class="col-12 col-md-4">
                    <div class="card metric-card border-0" style="border-top: 4px solid #6366f1 !important;">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <span class="metric-label text-primary">Total Outstanding</span>
                                <div class="metric-icon-box m-0" style="width: 42px; height: 42px; border-radius: 10px; display: flex; align-items: center; justify-content: center; background-color: rgba(99, 102, 241, 0.12); color: #6366f1;">
                                    <i class="mdi mdi-receipt-text-outline mdi-24px"></i>
                                </div>
                            </div>
                            <h3 class="metric-value mb-0" style="color: #6366f1;" id="aging-kojisha-outstanding">Rp.
                                {{ number_format($invoice->where('info', 'Kojisha')->sum('amount'), 0, ',', '.') }}
                            </h3>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="card metric-card border-0" style="border-top: 4px solid #ef4444 !important;">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <span class="metric-label text-danger">Total Overdue</span>
                                <div class="metric-icon-box m-0" style="width: 42px; height: 42px; border-radius: 10px; display: flex; align-items: center; justify-content: center; background-color: rgba(239, 68, 68, 0.12); color: #ef4444;">
                                    <i class="mdi mdi-alert-octagon-outline mdi-24px"></i>
                                </div>
                            </div>
                            <h3 class="metric-value mb-0" style="color: #ef4444;" id="aging-kojisha-overdue">Rp.
                                {{ number_format($overdue->where('info', 'Kojisha')->sum('amount'), 0, ',', '.') }}
                            </h3>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="card metric-card border-0" style="border-top: 4px solid #10b981 !important;">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <span class="metric-label text-success">Total On Due</span>
                                <div class="metric-icon-box m-0" style="width: 42px; height: 42px; border-radius: 10px; display: flex; align-items: center; justify-content: center; background-color: rgba(16, 185, 129, 0.12); color: #10b981;">
                                    <i class="mdi mdi-calendar-check-outline mdi-24px"></i>
                                </div>
                            </div>
                            <h3 class="metric-value mb-0" style="color: #10b981;" id="aging-kojisha-ondue">Rp.
                                {{ number_format($ondue->where('info', 'Kojisha')->sum('amount'), 0, ',', '.') }}
                            </h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="aging-metrics-pane d-none" data-tab="ahmad">
            <div class="row g-4 mb-4">
                <div class="col-12 col-md-4">
                    <div class="card metric-card border-0" style="border-top: 4px solid #6366f1 !important;">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <span class="metric-label text-primary">Total Outstanding</span>
                                <div class="metric-icon-box m-0" style="width: 42px; height: 42px; border-radius: 10px; display: flex; align-items: center; justify-content: center; background-color: rgba(99, 102, 241, 0.12); color: #6366f1;">
                                    <i class="mdi mdi-receipt-text-outline mdi-24px"></i>
                                </div>
                            </div>
                            <h3 class="metric-value mb-0" style="color: #6366f1;" id="aging-ahmad-outstanding">Rp.
                                {{ number_format($invoice->whereIn('id_sales', [2, 3, 4, 32])->sum('amount'), 0, ',', '.') }}
                            </h3>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="card metric-card border-0" style="border-top: 4px solid #ef4444 !important;">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <span class="metric-label text-danger">Total Overdue</span>
                                <div class="metric-icon-box m-0" style="width: 42px; height: 42px; border-radius: 10px; display: flex; align-items: center; justify-content: center; background-color: rgba(239, 68, 68, 0.12); color: #ef4444;">
                                    <i class="mdi mdi-alert-octagon-outline mdi-24px"></i>
                                </div>
                            </div>
                            <h3 class="metric-value mb-0" style="color: #ef4444;" id="aging-ahmad-overdue">Rp.
                                {{ number_format($overdue->whereIn('id_sales', [2, 3, 4, 32])->sum('amount'), 0, ',', '.') }}
                            </h3>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="card metric-card border-0" style="border-top: 4px solid #10b981 !important;">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <span class="metric-label text-success">Total On Due</span>
                                <div class="metric-icon-box m-0" style="width: 42px; height: 42px; border-radius: 10px; display: flex; align-items: center; justify-content: center; background-color: rgba(16, 185, 129, 0.12); color: #10b981;">
                                    <i class="mdi mdi-calendar-check-outline mdi-24px"></i>
                                </div>
                            </div>
                            <h3 class="metric-value mb-0" style="color: #10b981;" id="aging-ahmad-ondue">Rp.
                                {{ number_format($ondue->whereIn('id_sales', [2, 3, 4, 32])->sum('amount'), 0, ',', '.') }}
                            </h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="aging-metrics-pane d-none" data-tab="rayi">
            <div class="row g-4 mb-4">
                <div class="col-12 col-md-4">
                    <div class="card metric-card border-0" style="border-top: 4px solid #6366f1 !important;">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <span class="metric-label text-primary">Total Outstanding</span>
                                <div class="metric-icon-box m-0" style="width: 42px; height: 42px; border-radius: 10px; display: flex; align-items: center; justify-content: center; background-color: rgba(99, 102, 241, 0.12); color: #6366f1;">
                                    <i class="mdi mdi-receipt-text-outline mdi-24px"></i>
                                </div>
                            </div>
                            <h3 class="metric-value mb-0" style="color: #6366f1;" id="aging-rayi-outstanding">Rp.
                                {{ number_format($invoice->whereIn('id_sales', [1, 16, 23])->sum('amount'), 0, ',', '.') }}
                            </h3>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="card metric-card border-0" style="border-top: 4px solid #ef4444 !important;">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <span class="metric-label text-danger">Total Overdue</span>
                                <div class="metric-icon-box m-0" style="width: 42px; height: 42px; border-radius: 10px; display: flex; align-items: center; justify-content: center; background-color: rgba(239, 68, 68, 0.12); color: #ef4444;">
                                    <i class="mdi mdi-alert-octagon-outline mdi-24px"></i>
                                </div>
                            </div>
                            <h3 class="metric-value mb-0" style="color: #ef4444;" id="aging-rayi-overdue">Rp.
                                {{ number_format($overdue->whereIn('id_sales', [1, 16, 23])->sum('amount'), 0, ',', '.') }}
                            </h3>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="card metric-card border-0" style="border-top: 4px solid #10b981 !important;">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <span class="metric-label text-success">Total On Due</span>
                                <div class="metric-icon-box m-0" style="width: 42px; height: 42px; border-radius: 10px; display: flex; align-items: center; justify-content: center; background-color: rgba(16, 185, 129, 0.12); color: #10b981;">
                                    <i class="mdi mdi-calendar-check-outline mdi-24px"></i>
                                </div>
                            </div>
                            <h3 class="metric-value mb-0" style="color: #10b981;" id="aging-rayi-ondue">Rp.
                                {{ number_format($ondue->whereIn('id_sales', [1, 16, 23])->sum('amount'), 0, ',', '.') }}
                            </h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Bar -->
        <div class="card card-minimalist mb-4">
            <div class="card-body p-3">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div class="d-flex align-items-center gap-2">
                        <i class="mdi mdi-filter-variant text-primary mdi-24px"></i>
                        <h6 class="fw-bold mb-0 text-dark">Filter Aging</h6>
                    </div>
                    <div class="d-flex flex-wrap align-items-center gap-3">
                        <div class="d-flex align-items-center gap-2">
                            <label class="form-label mb-0 text-muted fw-semibold text-nowrap" style="font-size:0.85rem;">Filter Tahun:</label>
                            <select class="form-select form-select-sm" id="aging-year-filter" style="min-width:140px;">
                                <option value="all">Semua Tahun</option>
                                @for ($y = now()->year; $y >= 2022; $y--)
                                    <option value="{{ $y }}" {{ $y == now()->year ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <label class="form-label mb-0 text-muted fw-semibold text-nowrap" style="font-size:0.85rem;">Filter Sales:</label>
                            <select class="form-select form-select-sm" id="aging-sales-filter" style="min-width:180px;">
                                <option value="all">Semua Sales</option>
                                @if(isset($salesUsers))
                                    @foreach($salesUsers as $sUser)
                                        <option value="{{ $sUser->id }}">{{ $sUser->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="d-flex flex-wrap align-items-center gap-2 mt-2 pt-2 border-top w-100">
                        <span class="text-muted fw-semibold me-1" style="font-size:0.82rem;"><i class="mdi mdi-lightning-bolt text-warning me-1"></i>Quick Status Filter:</span>
                        <button type="button" class="btn btn-xs btn-label-primary aging-quick-filter active" data-filter="all">Semua</button>
                        <button type="button" class="btn btn-xs btn-label-success aging-quick-filter" data-filter="current"><i class="mdi mdi-check-circle-outline me-1"></i>Current / On Due</button>
                        <button type="button" class="btn btn-xs btn-label-danger aging-quick-filter" data-filter="overdue"><i class="mdi mdi-alert-circle-outline me-1"></i>Overdue (>0 Hari)</button>
                        <button type="button" class="btn btn-xs btn-danger text-white aging-quick-filter" data-filter="30"><i class="mdi mdi-fire me-1"></i>Overdue >30 Hari (Kritis)</button>
                        <button type="button" class="btn btn-xs btn-label-secondary aging-quick-filter" data-filter="nodue"><i class="mdi mdi-help-circle-outline me-1"></i>Belum Set Due Date</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="card card-minimalist">
            <div class="card-header card-minimalist-header py-2">
                <ul class="nav nav-tabs card-header-tabs border-0 m-0 flex-nowrap overflow-auto" id="aging-ar-tab-nav" role="tablist">
                    <li class="nav-item">
                        <button type="button" class="nav-link active" id="nav-aging-general" role="tab"
                            data-bs-toggle="tab" data-bs-target="#navs-pills-top-general" aria-controls="navs-pills-top-general"
                            aria-selected="true" data-metrics-tab="general">
                            <i class="mdi mdi-view-list-outline me-1"></i>General
                            <span class="badge rounded-pill bg-primary ms-1" id="badge-aging-general">-</span>
                        </button>
                    </li>
                    <li class="nav-item">
                        <button type="button" class="nav-link" id="nav-aging-reftech" role="tab" data-bs-toggle="tab"
                            data-bs-target="#navs-pills-top-reftech" aria-controls="navs-pills-top-reftech"
                            aria-selected="false" tabindex="-1" data-metrics-tab="reftech">
                            <i class="mdi mdi-file-document-outline me-1"></i>Reftech
                            <span class="badge rounded-pill bg-info ms-1" id="badge-aging-reftech">-</span>
                        </button>
                    </li>
                    <li class="nav-item">
                        <button type="button" class="nav-link" id="nav-aging-kojisha" role="tab" data-bs-toggle="tab"
                            data-bs-target="#navs-pills-top-kojisha" aria-controls="navs-pills-top-kojisha"
                            aria-selected="false" tabindex="-1" data-metrics-tab="kojisha">
                            <i class="mdi mdi-file-document-multiple-outline me-1"></i>Kojisha
                            <span class="badge rounded-pill bg-info ms-1" id="badge-aging-kojisha">-</span>
                        </button>
                    </li>
                    <li class="nav-item">
                        <button type="button" class="nav-link" id="nav-aging-ahmad" role="tab" data-bs-toggle="tab"
                            data-bs-target="#navs-pills-top-ahmad" aria-controls="navs-pills-top-ahmad" aria-selected="false"
                            tabindex="-1" data-metrics-tab="ahmad">
                            <i class="mdi mdi-account-outline me-1"></i>Yusuf
                            <span class="badge rounded-pill bg-secondary ms-1" id="badge-aging-ahmad">-</span>
                        </button>
                    </li>
                    <li class="nav-item">
                        <button type="button" class="nav-link" id="nav-aging-rayi" role="tab" data-bs-toggle="tab"
                            data-bs-target="#navs-pills-top-rayi" aria-controls="navs-pills-top-rayi" aria-selected="false"
                            tabindex="-1" data-metrics-tab="rayi">
                            <i class="mdi mdi-account-outline me-1"></i>Rayi
                            <span class="badge rounded-pill bg-secondary ms-1" id="badge-aging-rayi">-</span>
                        </button>
                    </li>
                </ul>
            </div>
            <div class="card-body p-3">
                <div class="tab-content">
                    <div class="tab-pane fade show active" id="navs-pills-top-general" role="tabpanel">
                        <div class="card-datatable table-responsive pt-0">
                            <table class="datatable-aging-report-ar table table-bordered" data-badge="badge-aging-general">
                                <thead>
                                    <tr>
                                        <th>Invoice</th>
                                        <th>Date</th>
                                        <th>No. PO</th>
                                        <th>Customer</th>
                                        <th>Total</th>
                                        <th>Due Date</th>
                                        <th>overdue</th>
                                        <th>VAT</th>
                                        <th>name</th>
                                        <th>reminder</th>
                                        <th>flag</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="navs-pills-top-reftech" role="tabpanel">
                        <div class="card-datatable table-responsive pt-0">
                            <table class="datatable-aging-report-reftech table table-bordered" data-badge="badge-aging-reftech">
                                <thead>
                                    <tr>
                                        <th>Invoice</th>
                                        <th>Date</th>
                                        <th>No. PO</th>
                                        <th>Customer</th>
                                        <th>Total</th>
                                        <th>Due Date</th>
                                        <th>overdue</th>
                                        <th>VAT</th>
                                        <th>name</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="navs-pills-top-kojisha" role="tabpanel">
                        <div class="card-datatable table-responsive pt-0">
                            <table class="datatable-aging-report-kojisha table table-bordered" data-badge="badge-aging-kojisha">
                                <thead>
                                    <tr>
                                        <th>Invoice</th>
                                        <th>Date</th>
                                        <th>No. PO</th>
                                        <th>Customer</th>
                                        <th>Total</th>
                                        <th>Due Date</th>
                                        <th>overdue</th>
                                        <th>VAT</th>
                                        <th>name</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="navs-pills-top-ahmad" role="tabpanel">
                        <div class="card-datatable table-responsive pt-0">
                            <table class="datatable-aging-report-ahmad table table-bordered" data-badge="badge-aging-ahmad">
                                <thead>
                                    <tr>
                                        <th>Invoice</th>
                                        <th>Date</th>
                                        <th>No. PO</th>
                                        <th>Customer</th>
                                        <th>Total</th>
                                        <th>Due Date</th>
                                        <th>overdue</th>
                                        <th>VAT</th>
                                        <th>name</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="navs-pills-top-rayi" role="tabpanel">
                        <div class="card-datatable table-responsive pt-0">
                            <table class="datatable-aging-report-rayi table table-bordered" data-badge="badge-aging-rayi">
                                <thead>
                                    <tr>
                                        <th>Invoice</th>
                                        <th>Date</th>
                                        <th>No. PO</th>
                                        <th>Customer</th>
                                        <th>Total</th>
                                        <th>Due Date</th>
                                        <th>overdue</th>
                                        <th>VAT</th>
                                        <th>name</th>
                                        <th>flag</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Modal Edit Due Date dari Tabel Aging --}}
    <div class="modal fade" id="modalAgingQuickDueDate" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-light py-3 border-bottom">
                    <h5 class="modal-title fw-bold text-dark mb-0">
                        <i class="mdi mdi-calendar-clock me-1 text-warning"></i> Set / Edit Tanggal Jatuh Tempo
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="quick-due-form" method="POST" action="">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="p-3 mb-3 rounded bg-light border">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted small">No. Invoice</span>
                                <span class="fw-bold text-primary" id="quick-due-inv-no">-</span>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Tanggal Invoice <span class="text-danger">*</span></label>
                            <input class="form-control" type="date" id="quick-due-inv-date" name="date" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Tanggal Jatuh Tempo (Due Date) <span class="text-danger">*</span></label>
                            <input class="form-control fw-bold border-warning text-dark" type="date" id="quick-due-date-val" name="due_date" required>
                            <div class="form-text text-muted mt-1" style="font-size: 11px;">
                                <i class="mdi mdi-information-outline me-1 text-warning"></i>
                                Tanggal dapat disesuaikan manual (misal: mempertimbangkan pengiriman via ekspedisi).
                            </div>
                        </div>
                        <div class="mb-2">
                            <label class="form-label fw-semibold">Catatan / Keterangan Penyesuaian <span class="text-muted small">(Opsional)</span></label>
                            <input type="text" class="form-control" name="note" placeholder="misal: Ditambah 5 hari pengiriman ekspedisi">
                        </div>
                    </div>
                    <div class="modal-footer bg-light py-2 border-top">
                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-warning shadow-sm">
                            <i class="mdi mdi-content-save me-1"></i> Simpan Tanggal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @include('components.modal.payment.outstanding')
    @include('components.modal.payment.overdue')
    @include('components.modal.payment.ondue')
@endsection()

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
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.css" />
    <style>
        /* Metric Card Stylings matching Sales Invoice AR */
        .metric-card {
            border-radius: 20px;
            border: 1px solid rgba(229, 231, 235, 0.6) !important;
            background: #ffffff;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02) !important;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }
        .metric-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 30px rgba(79, 70, 229, 0.08) !important;
        }
        .metric-label {
            font-size: 0.85rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .metric-value {
            font-size: 1.85rem;
            font-weight: 800;
            letter-spacing: -0.025em;
        }

        .card-minimalist {
            border: 1px solid #e0e2e8 !important;
            box-shadow: none !important;
            border-radius: 12px;
        }
        .card-minimalist-header {
            border-bottom: 1px solid #e0e2e8 !important;
            background-color: #fafbfe;
            border-top-left-radius: 12px !important;
            border-top-right-radius: 12px !important;
        }
        .nav-tabs .nav-link {
            border-radius: 6px 6px 0 0;
            font-weight: 500;
        }
        .nav-tabs .nav-link.active {
            border-color: #e0e2e8 #e0e2e8 #fff !important;
            background-color: #ffffff;
            font-weight: 600;
        }
    </style>
@endpush

@push('after-script')
    <script src="{{ asset('assets') }}/vendor/libs/moment/moment.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/flatpickr/flatpickr.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/formvalidation/dist/js/FormValidation.min.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.js"></script>
@endpush

@push('page-script')
    <script src="{{ asset('assets') }}/js/tables-datatables-basic.js"></script>
    <script src="{{ asset('assets') }}/includes/table-ar-aging-report.js"></script>
    <script src="{{ asset('assets') }}/includes/table-ar-aging-report-reftech.js"></script>
    <script src="{{ asset('assets') }}/includes/table-ar-aging-report-kojisha.js"></script>
    <script src="{{ asset('assets') }}/includes/table-ar-aging-report-ahmad.js"></script>
    <script src="{{ asset('assets') }}/includes/table-ar-aging-report-rayi.js"></script>
    <script src="{{ asset('assets') }}/js/extended-ui-sweetalert2.js"></script>
@endpush
@push('script')
    <script>
        // Initialize Bootstrap tooltips using jQuery
        $(document).ready(function() {
            const initTooltips = () => {
                const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
                const tooltipList = [...tooltipTriggerList].map(el => new bootstrap.Tooltip(el));
            };
            initTooltips();
        });

        $(document).on('draw.dt', function (e) {
            var $tbl = $(e.target);
            var badgeId = $tbl.data('badge');
            if (badgeId) {
                var api = $tbl.DataTable();
                $('#' + badgeId).text(api.page.info().recordsTotal);
            }
        });

        $('#aging-ar-tab-nav button[data-bs-toggle="tab"]').on('shown.bs.tab', function () {
            $.fn.dataTable.tables({ visible: true, api: true }).columns.adjust().responsive.recalc();
            var metricsTab = $(this).data('metrics-tab');
            $('.aging-metrics-pane').addClass('d-none');
            $('.aging-metrics-pane[data-tab="' + metricsTab + '"]').removeClass('d-none');
        });

        window.agingYearFilter = $('#aging-year-filter').val() || 'all';
        window.agingSalesFilter = $('#aging-sales-filter').val() || 'all';
        window.agingDataTables = window.agingDataTables || {};

        function loadAgingSummary() {
            var year = window.agingYearFilter || 'all';
            var salesId = window.agingSalesFilter || 'all';
            $.ajax({
                url: '/db/aging/summary',
                type: 'GET',
                data: { year: year, sales_id: salesId },
                success: function (res) {
                    var fmt = function (n) { return 'Rp. ' + new Intl.NumberFormat('id-ID').format(n || 0); };

                    $('#aging-general-outstanding').text(fmt(res.general.outstanding.total));
                    $('#aging-general-outstanding-ppn').text(fmt(res.general.outstanding.ppn));
                    $('#aging-general-outstanding-nonppn').text(fmt(res.general.outstanding.non_ppn));
                    $('#aging-general-overdue').text(fmt(res.general.overdue.total));
                    $('#aging-general-overdue-ppn').text(fmt(res.general.overdue.ppn));
                    $('#aging-general-overdue-nonppn').text(fmt(res.general.overdue.non_ppn));
                    $('#aging-general-ondue').text(fmt(res.general.ondue.total));
                    $('#aging-general-ondue-ppn').text(fmt(res.general.ondue.ppn));
                    $('#aging-general-ondue-nonppn').text(fmt(res.general.ondue.non_ppn));

                    ['reftech', 'kojisha', 'ahmad', 'rayi'].forEach(function (tab) {
                        $('#aging-' + tab + '-outstanding').text(fmt(res[tab].outstanding.total));
                        $('#aging-' + tab + '-overdue').text(fmt(res[tab].overdue.total));
                        $('#aging-' + tab + '-ondue').text(fmt(res[tab].ondue.total));
                    });
                }
            });
        }

        $(document).ready(function () {
            loadAgingSummary();
        });

        $('#aging-year-filter, #aging-sales-filter').on('change', function () {
            window.agingYearFilter = $('#aging-year-filter').val() || 'all';
            window.agingSalesFilter = $('#aging-sales-filter').val() || 'all';
            loadAgingSummary();
            Object.values(window.agingDataTables).forEach(function (dt) {
                dt.ajax.reload();
            });
        });
    </script>
@endpush
