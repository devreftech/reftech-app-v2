@extends('layouts.sales.app')
@section('title', 'Aging Report')
@section('no-container') @endsection
@section('content')
    <div class="container-fluid px-4 py-3">
        {{-- Page Header --}}
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center py-2 mb-3 gap-3">
            <div>
                <h4 class="fw-bold mb-1">
                    <span class="text-muted fw-light">Finance / Account Receivable (AR) /</span> Aging Report
                </h4>
                <p class="text-muted mb-0 small">
                    <i class="mdi mdi-calendar-clock-outline me-1"></i> Analisis umur piutang, keterlambatan pembayaran klien, dan jatuh tempo kredit tempo
                </p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('payment_index.invoice') }}" class="btn btn-label-primary btn-sm">
                    <i class="mdi mdi-receipt-text-outline me-1"></i> Sales Invoice
                </a>
                <a href="{{ route('payment_index.payment') }}" class="btn btn-label-primary btn-sm">
                    <i class="mdi mdi-cash-check me-1"></i> Payment Receipt
                </a>
            </div>
        </div>

        <div class="aging-metrics-pane" data-tab="general">
            <div class="row g-3 mb-4">
                <!-- Total Outstanding -->
                <div class="col-12 col-md-4">
                    <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #f8f9ff 0%, #edf0ff 100%); border-left: 5px solid #696cff !important; cursor: pointer;"
                        data-bs-toggle="modal" data-bs-target="#detailOutstanding" title="Klik untuk lihat detail outstanding">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="text-uppercase fw-bold text-primary small" style="letter-spacing: .5px;">
                                    <i class="mdi mdi-receipt-text me-1"></i> Total Outstanding
                                </span>
                                <div class="avatar avatar-xs bg-label-primary rounded p-1 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                    <i class="mdi mdi-receipt-text fs-6"></i>
                                </div>
                            </div>
                            <h3 class="fw-bolder text-primary fs-4 mb-1" id="aging-general-outstanding">Rp.
                                {{ number_format($invoice->sum('amount'), 0, ',', '.') }}
                            </h3>
                            <div class="table-responsive text-nowrap border-top mt-2 pt-2" style="border-color: rgba(105, 108, 255, 0.15) !important;">
                                <table class="table table-sm mb-0 table-borderless" style="font-size: 11.5px;">
                                    <tbody>
                                        <tr>
                                            <td class="ps-0 py-0 text-muted">PPN</td>
                                            <td class="pe-0 py-0 text-end fw-semibold text-primary" id="aging-general-outstanding-ppn">Rp. {{ number_format($invoice->filter(fn($i) => $i->tax)->sum('amount'), 0, ',', '.') }}</td>
                                        </tr>
                                        <tr>
                                            <td class="ps-0 py-0 text-muted">Non-PPN</td>
                                            <td class="pe-0 py-0 text-end fw-semibold text-secondary" id="aging-general-outstanding-nonppn">Rp. {{ number_format($invoice->filter(fn($i) => !$i->tax)->sum('amount'), 0, ',', '.') }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Overdue -->
                <div class="col-12 col-md-4">
                    <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #fff8f8 0%, #ffeded 100%); border-left: 5px solid #ff3e1d !important; cursor: pointer;"
                        data-bs-toggle="modal" data-bs-target="#detailOverdue" title="Klik untuk lihat detail overdue">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="text-uppercase fw-bold text-danger small" style="letter-spacing: .5px;">
                                    <i class="mdi mdi-clock-alert-outline me-1"></i> Total Overdue
                                </span>
                                <div class="avatar avatar-xs bg-label-danger rounded p-1 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                    <i class="mdi mdi-clock-alert-outline fs-6"></i>
                                </div>
                            </div>
                            <h3 class="fw-bolder text-danger fs-4 mb-1" id="aging-general-overdue">Rp.
                                {{ number_format($overdue->sum('amount'), 0, ',', '.') }}
                            </h3>
                            <div class="table-responsive text-nowrap border-top mt-2 pt-2" style="border-color: rgba(255, 62, 29, 0.15) !important;">
                                <table class="table table-sm mb-0 table-borderless" style="font-size: 11.5px;">
                                    <tbody>
                                        <tr>
                                            <td class="ps-0 py-0 text-muted">PPN</td>
                                            <td class="pe-0 py-0 text-end fw-semibold text-danger" id="aging-general-overdue-ppn">Rp. {{ number_format($overdue->filter(fn($i) => $i->tax)->sum('amount'), 0, ',', '.') }}</td>
                                        </tr>
                                        <tr>
                                            <td class="ps-0 py-0 text-muted">Non-PPN</td>
                                            <td class="pe-0 py-0 text-end fw-semibold text-secondary" id="aging-general-overdue-nonppn">Rp. {{ number_format($overdue->filter(fn($i) => !$i->tax)->sum('amount'), 0, ',', '.') }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Current -->
                <div class="col-12 col-md-4">
                    <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #f3fdf6 0%, #e8f9ee 100%); border-left: 5px solid #28a745 !important; cursor: pointer;"
                        data-bs-toggle="modal" data-bs-target="#detailOnDue" title="Klik untuk lihat detail invoice current (belum jatuh tempo)">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="text-uppercase fw-bold text-success small" style="letter-spacing: .5px;">
                                    <i class="mdi mdi-calendar-check-outline me-1"></i> Total Current
                                </span>
                                <div class="avatar avatar-xs bg-label-success rounded p-1 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                    <i class="mdi mdi-calendar-check-outline fs-6"></i>
                                </div>
                            </div>
                            <h3 class="fw-bolder text-success fs-4 mb-1" id="aging-general-ondue">Rp.
                                {{ number_format($ondue->sum('amount'), 0, ',', '.') }}
                            </h3>
                            <div class="table-responsive text-nowrap border-top mt-2 pt-2" style="border-color: rgba(40, 167, 69, 0.15) !important;">
                                <table class="table table-sm mb-0 table-borderless" style="font-size: 11.5px;">
                                    <tbody>
                                        <tr>
                                            <td class="ps-0 py-0 text-muted">PPN</td>
                                            <td class="pe-0 py-0 text-end fw-semibold text-success" id="aging-general-ondue-ppn">Rp. {{ number_format($ondue->filter(fn($i) => $i->tax)->sum('amount'), 0, ',', '.') }}</td>
                                        </tr>
                                        <tr>
                                            <td class="ps-0 py-0 text-muted">Non-PPN</td>
                                            <td class="pe-0 py-0 text-end fw-semibold text-secondary" id="aging-general-ondue-nonppn">Rp. {{ number_format($ondue->filter(fn($i) => !$i->tax)->sum('amount'), 0, ',', '.') }}</td>
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
            <div class="row g-3 mb-4">
                <div class="col-12 col-md-4">
                    <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #f8f9ff 0%, #edf0ff 100%); border-left: 5px solid #696cff !important;">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="text-uppercase fw-bold text-primary small" style="letter-spacing: .5px;">
                                    <i class="mdi mdi-receipt-text me-1"></i> Total Outstanding
                                </span>
                                <div class="avatar avatar-xs bg-label-primary rounded p-1 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                    <i class="mdi mdi-receipt-text fs-6"></i>
                                </div>
                            </div>
                            <h3 class="fw-bolder text-primary fs-4 mb-1" id="aging-reftech-outstanding">Rp.
                                {{ number_format($invoice->where('info', 'Reftech')->sum('amount'), 0, ',', '.') }}
                            </h3>
                            <small class="text-muted" style="font-size: 11px;">Sisa saldo piutang Reftech yang belum dilunasi</small>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #fff8f8 0%, #ffeded 100%); border-left: 5px solid #ff3e1d !important;">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="text-uppercase fw-bold text-danger small" style="letter-spacing: .5px;">
                                    <i class="mdi mdi-clock-alert-outline me-1"></i> Total Overdue
                                </span>
                                <div class="avatar avatar-xs bg-label-danger rounded p-1 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                    <i class="mdi mdi-clock-alert-outline fs-6"></i>
                                </div>
                            </div>
                            <h3 class="fw-bolder text-danger fs-4 mb-1" id="aging-reftech-overdue">Rp.
                                {{ number_format($overdue->where('info', 'Reftech')->sum('amount'), 0, ',', '.') }}
                            </h3>
                            <small class="text-muted" style="font-size: 11px;">Piutang Reftech yang telah melewati jatuh tempo</small>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #f3fdf6 0%, #e8f9ee 100%); border-left: 5px solid #28a745 !important;">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="text-uppercase fw-bold text-success small" style="letter-spacing: .5px;">
                                    <i class="mdi mdi-calendar-check-outline me-1"></i> Total Current
                                </span>
                                <div class="avatar avatar-xs bg-label-success rounded p-1 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                    <i class="mdi mdi-calendar-check-outline fs-6"></i>
                                </div>
                            </div>
                            <h3 class="fw-bolder text-success fs-4 mb-1" id="aging-reftech-ondue">Rp.
                                {{ number_format($ondue->where('info', 'Reftech')->sum('amount'), 0, ',', '.') }}
                            </h3>
                            <small class="text-muted" style="font-size: 11px;">Piutang Reftech berjalan yang belum jatuh tempo</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="aging-metrics-pane d-none" data-tab="kojisha">
            <div class="row g-3 mb-4">
                <div class="col-12 col-md-4">
                    <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #f8f9ff 0%, #edf0ff 100%); border-left: 5px solid #696cff !important;">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="text-uppercase fw-bold text-primary small" style="letter-spacing: .5px;">
                                    <i class="mdi mdi-receipt-text me-1"></i> Total Outstanding
                                </span>
                                <div class="avatar avatar-xs bg-label-primary rounded p-1 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                    <i class="mdi mdi-receipt-text fs-6"></i>
                                </div>
                            </div>
                            <h3 class="fw-bolder text-primary fs-4 mb-1" id="aging-kojisha-outstanding">Rp.
                                {{ number_format($invoice->where('info', 'Kojisha')->sum('amount'), 0, ',', '.') }}
                            </h3>
                            <small class="text-muted" style="font-size: 11px;">Sisa saldo piutang Kojisha yang belum dilunasi</small>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #fff8f8 0%, #ffeded 100%); border-left: 5px solid #ff3e1d !important;">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="text-uppercase fw-bold text-danger small" style="letter-spacing: .5px;">
                                    <i class="mdi mdi-clock-alert-outline me-1"></i> Total Overdue
                                </span>
                                <div class="avatar avatar-xs bg-label-danger rounded p-1 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                    <i class="mdi mdi-clock-alert-outline fs-6"></i>
                                </div>
                            </div>
                            <h3 class="fw-bolder text-danger fs-4 mb-1" id="aging-kojisha-overdue">Rp.
                                {{ number_format($overdue->where('info', 'Kojisha')->sum('amount'), 0, ',', '.') }}
                            </h3>
                            <small class="text-muted" style="font-size: 11px;">Piutang Kojisha yang telah melewati jatuh tempo</small>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #f3fdf6 0%, #e8f9ee 100%); border-left: 5px solid #28a745 !important;">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="text-uppercase fw-bold text-success small" style="letter-spacing: .5px;">
                                    <i class="mdi mdi-calendar-check-outline me-1"></i> Total Current
                                </span>
                                <div class="avatar avatar-xs bg-label-success rounded p-1 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                    <i class="mdi mdi-calendar-check-outline fs-6"></i>
                                </div>
                            </div>
                            <h3 class="fw-bolder text-success fs-4 mb-1" id="aging-kojisha-ondue">Rp.
                                {{ number_format($ondue->where('info', 'Kojisha')->sum('amount'), 0, ',', '.') }}
                            </h3>
                            <small class="text-muted" style="font-size: 11px;">Piutang Kojisha berjalan yang belum jatuh tempo</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="aging-metrics-pane d-none" data-tab="ahmad">
            <div class="row g-3 mb-4">
                <div class="col-12 col-md-4">
                    <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #f8f9ff 0%, #edf0ff 100%); border-left: 5px solid #696cff !important;">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="text-uppercase fw-bold text-primary small" style="letter-spacing: .5px;">
                                    <i class="mdi mdi-receipt-text me-1"></i> Total Outstanding
                                </span>
                                <div class="avatar avatar-xs bg-label-primary rounded p-1 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                    <i class="mdi mdi-receipt-text fs-6"></i>
                                </div>
                            </div>
                            <h3 class="fw-bolder text-primary fs-4 mb-1" id="aging-ahmad-outstanding">Rp.
                                {{ number_format($invoice->whereIn('id_sales', [2, 3, 4, 32])->sum('amount'), 0, ',', '.') }}
                            </h3>
                            <small class="text-muted" style="font-size: 11px;">Sisa saldo piutang Yusuf yang belum dilunasi</small>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #fff8f8 0%, #ffeded 100%); border-left: 5px solid #ff3e1d !important;">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="text-uppercase fw-bold text-danger small" style="letter-spacing: .5px;">
                                    <i class="mdi mdi-clock-alert-outline me-1"></i> Total Overdue
                                </span>
                                <div class="avatar avatar-xs bg-label-danger rounded p-1 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                    <i class="mdi mdi-clock-alert-outline fs-6"></i>
                                </div>
                            </div>
                            <h3 class="fw-bolder text-danger fs-4 mb-1" id="aging-ahmad-overdue">Rp.
                                {{ number_format($overdue->whereIn('id_sales', [2, 3, 4, 32])->sum('amount'), 0, ',', '.') }}
                            </h3>
                            <small class="text-muted" style="font-size: 11px;">Piutang Yusuf yang telah melewati jatuh tempo</small>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #f3fdf6 0%, #e8f9ee 100%); border-left: 5px solid #28a745 !important;">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="text-uppercase fw-bold text-success small" style="letter-spacing: .5px;">
                                    <i class="mdi mdi-calendar-check-outline me-1"></i> Total Current
                                </span>
                                <div class="avatar avatar-xs bg-label-success rounded p-1 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                    <i class="mdi mdi-calendar-check-outline fs-6"></i>
                                </div>
                            </div>
                            <h3 class="fw-bolder text-success fs-4 mb-1" id="aging-ahmad-ondue">Rp.
                                {{ number_format($ondue->whereIn('id_sales', [2, 3, 4, 32])->sum('amount'), 0, ',', '.') }}
                            </h3>
                            <small class="text-muted" style="font-size: 11px;">Piutang Yusuf berjalan yang belum jatuh tempo</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="aging-metrics-pane d-none" data-tab="rayi">
            <div class="row g-3 mb-4">
                <div class="col-12 col-md-4">
                    <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #f8f9ff 0%, #edf0ff 100%); border-left: 5px solid #696cff !important;">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="text-uppercase fw-bold text-primary small" style="letter-spacing: .5px;">
                                    <i class="mdi mdi-receipt-text me-1"></i> Total Outstanding
                                </span>
                                <div class="avatar avatar-xs bg-label-primary rounded p-1 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                    <i class="mdi mdi-receipt-text fs-6"></i>
                                </div>
                            </div>
                            <h3 class="fw-bolder text-primary fs-4 mb-1" id="aging-rayi-outstanding">Rp.
                                {{ number_format($invoice->whereIn('id_sales', [1, 16, 23])->sum('amount'), 0, ',', '.') }}
                            </h3>
                            <small class="text-muted" style="font-size: 11px;">Sisa saldo piutang Rayi yang belum dilunasi</small>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #fff8f8 0%, #ffeded 100%); border-left: 5px solid #ff3e1d !important;">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="text-uppercase fw-bold text-danger small" style="letter-spacing: .5px;">
                                    <i class="mdi mdi-clock-alert-outline me-1"></i> Total Overdue
                                </span>
                                <div class="avatar avatar-xs bg-label-danger rounded p-1 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                    <i class="mdi mdi-clock-alert-outline fs-6"></i>
                                </div>
                            </div>
                            <h3 class="fw-bolder text-danger fs-4 mb-1" id="aging-rayi-overdue">Rp.
                                {{ number_format($overdue->whereIn('id_sales', [1, 16, 23])->sum('amount'), 0, ',', '.') }}
                            </h3>
                            <small class="text-muted" style="font-size: 11px;">Piutang Rayi yang telah melewati jatuh tempo</small>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #f3fdf6 0%, #e8f9ee 100%); border-left: 5px solid #28a745 !important;">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="text-uppercase fw-bold text-success small" style="letter-spacing: .5px;">
                                    <i class="mdi mdi-calendar-check-outline me-1"></i> Total Current
                                </span>
                                <div class="avatar avatar-xs bg-label-success rounded p-1 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                    <i class="mdi mdi-calendar-check-outline fs-6"></i>
                                </div>
                            </div>
                            <h3 class="fw-bolder text-success fs-4 mb-1" id="aging-rayi-ondue">Rp.
                                {{ number_format($ondue->whereIn('id_sales', [1, 16, 23])->sum('amount'), 0, ',', '.') }}
                            </h3>
                            <small class="text-muted" style="font-size: 11px;">Piutang Rayi berjalan yang belum jatuh tempo</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Bar -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-3">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div class="d-flex align-items-center gap-2">
                        <i class="mdi mdi-filter-variant text-primary fs-5"></i>
                        <h6 class="fw-bold mb-0 text-dark">Filter Aging</h6>
                    </div>
                    <div class="d-flex flex-wrap align-items-center gap-3">
                        <div class="d-flex align-items-center gap-2">
                            <label class="form-label mb-0 text-muted small fw-semibold text-nowrap">Filter Tahun:</label>
                            <select class="form-select form-select-sm" id="aging-year-filter" style="min-width:140px;">
                                <option value="all">Semua Tahun</option>
                                @for ($y = now()->year; $y >= 2022; $y--)
                                    <option value="{{ $y }}" {{ $y == now()->year ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <label class="form-label mb-0 text-muted small fw-semibold text-nowrap">Filter Sales:</label>
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
                        <span class="text-muted fw-semibold me-1 small"><i class="mdi mdi-lightning-bolt text-warning me-1"></i>Quick Status Filter:</span>
                        <button type="button" class="btn btn-xs btn-label-primary aging-quick-filter active" data-filter="all">Semua</button>
                        <button type="button" class="btn btn-xs btn-label-success aging-quick-filter" data-filter="current"><i class="mdi mdi-check-circle-outline me-1"></i>Current / On Due</button>
                        <button type="button" class="btn btn-xs btn-label-danger aging-quick-filter" data-filter="overdue"><i class="mdi mdi-alert-circle-outline me-1"></i>Overdue (>0 Hari)</button>
                        <button type="button" class="btn btn-xs btn-danger text-white aging-quick-filter" data-filter="30"><i class="mdi mdi-fire me-1"></i>Overdue >30 Hari (Kritis)</button>
                        <button type="button" class="btn btn-xs btn-label-secondary aging-quick-filter" data-filter="nodue"><i class="mdi mdi-help-circle-outline me-1"></i>Belum Set Due Date</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Main Table Container with Tabs --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-bottom py-2">
                <ul class="nav nav-tabs card-header-tabs border-0 m-0 flex-nowrap overflow-auto" id="aging-ar-tab-nav" role="tablist">
                    <li class="nav-item">
                        <button type="button" class="nav-link active py-2 px-3 fw-semibold" id="nav-aging-general" role="tab"
                            data-bs-toggle="tab" data-bs-target="#navs-pills-top-general" aria-controls="navs-pills-top-general"
                            aria-selected="true" data-metrics-tab="general">
                            <i class="mdi mdi-view-list-outline me-1"></i>General
                            <span class="badge rounded-pill bg-label-primary ms-1" id="badge-aging-general">-</span>
                        </button>
                    </li>
                    <li class="nav-item">
                        <button type="button" class="nav-link py-2 px-3 fw-semibold" id="nav-aging-reftech" role="tab" data-bs-toggle="tab"
                            data-bs-target="#navs-pills-top-reftech" aria-controls="navs-pills-top-reftech"
                            aria-selected="false" tabindex="-1" data-metrics-tab="reftech">
                            <i class="mdi mdi-file-document-outline me-1"></i>Reftech
                            <span class="badge rounded-pill bg-label-info ms-1" id="badge-aging-reftech">-</span>
                        </button>
                    </li>
                    <li class="nav-item">
                        <button type="button" class="nav-link py-2 px-3 fw-semibold" id="nav-aging-kojisha" role="tab" data-bs-toggle="tab"
                            data-bs-target="#navs-pills-top-kojisha" aria-controls="navs-pills-top-kojisha"
                            aria-selected="false" tabindex="-1" data-metrics-tab="kojisha">
                            <i class="mdi mdi-file-document-multiple-outline me-1"></i>Kojisha
                            <span class="badge rounded-pill bg-label-info ms-1" id="badge-aging-kojisha">-</span>
                        </button>
                    </li>
                    <li class="nav-item">
                        <button type="button" class="nav-link py-2 px-3 fw-semibold" id="nav-aging-ahmad" role="tab" data-bs-toggle="tab"
                            data-bs-target="#navs-pills-top-ahmad" aria-controls="navs-pills-top-ahmad" aria-selected="false"
                            tabindex="-1" data-metrics-tab="ahmad">
                            <i class="mdi mdi-account-outline me-1"></i>Yusuf
                            <span class="badge rounded-pill bg-label-secondary ms-1" id="badge-aging-ahmad">-</span>
                        </button>
                    </li>
                    <li class="nav-item">
                        <button type="button" class="nav-link py-2 px-3 fw-semibold" id="nav-aging-rayi" role="tab" data-bs-toggle="tab"
                            data-bs-target="#navs-pills-top-rayi" aria-controls="navs-pills-top-rayi" aria-selected="false"
                            tabindex="-1" data-metrics-tab="rayi">
                            <i class="mdi mdi-account-outline me-1"></i>Rayi
                            <span class="badge rounded-pill bg-label-secondary ms-1" id="badge-aging-rayi">-</span>
                        </button>
                    </li>
                </ul>
            </div>
            <div class="card-body p-3">
                <div class="tab-content border-0 p-0 m-0">
                    <div class="tab-pane fade show active" id="navs-pills-top-general" role="tabpanel">
                        <div class="card-datatable table-responsive pt-0">
                            <table class="datatable-aging-report-ar table table-hover border-top" data-badge="badge-aging-general">
                                <thead class="table-light">
                                    <tr>
                                        <th class="fw-semibold text-dark">Invoice &amp; PO</th>
                                        <th class="fw-semibold text-dark">Customer &amp; Sales</th>
                                        <th class="fw-semibold text-dark text-end">Nilai Invoice</th>
                                        <th class="fw-semibold text-dark text-center">Jatuh Tempo &amp; Aging</th>
                                        <th class="fw-semibold text-dark text-center">Reminder &amp; Action</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="navs-pills-top-reftech" role="tabpanel">
                        <div class="card-datatable table-responsive pt-0">
                            <table class="datatable-aging-report-reftech table table-hover border-top" data-badge="badge-aging-reftech">
                                <thead class="table-light">
                                    <tr>
                                        <th class="fw-semibold text-dark">Invoice &amp; PO</th>
                                        <th class="fw-semibold text-dark">Customer &amp; Sales</th>
                                        <th class="fw-semibold text-dark text-end">Nilai Invoice</th>
                                        <th class="fw-semibold text-dark text-center">Jatuh Tempo &amp; Aging</th>
                                        <th class="fw-semibold text-dark text-center">Reminder &amp; Action</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="navs-pills-top-kojisha" role="tabpanel">
                        <div class="card-datatable table-responsive pt-0">
                            <table class="datatable-aging-report-kojisha table table-hover border-top" data-badge="badge-aging-kojisha">
                                <thead class="table-light">
                                    <tr>
                                        <th class="fw-semibold text-dark">Invoice &amp; PO</th>
                                        <th class="fw-semibold text-dark">Customer &amp; Sales</th>
                                        <th class="fw-semibold text-dark text-end">Nilai Invoice</th>
                                        <th class="fw-semibold text-dark text-center">Jatuh Tempo &amp; Aging</th>
                                        <th class="fw-semibold text-dark text-center">Reminder &amp; Action</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="navs-pills-top-ahmad" role="tabpanel">
                        <div class="card-datatable table-responsive pt-0">
                            <table class="datatable-aging-report-ahmad table table-hover border-top" data-badge="badge-aging-ahmad">
                                <thead class="table-light">
                                    <tr>
                                        <th class="fw-semibold text-dark">Invoice &amp; PO</th>
                                        <th class="fw-semibold text-dark">Customer &amp; Sales</th>
                                        <th class="fw-semibold text-dark text-end">Nilai Invoice</th>
                                        <th class="fw-semibold text-dark text-center">Jatuh Tempo &amp; Aging</th>
                                        <th class="fw-semibold text-dark text-center">Reminder &amp; Action</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="navs-pills-top-rayi" role="tabpanel">
                        <div class="card-datatable table-responsive pt-0">
                            <table class="datatable-aging-report-rayi table table-hover border-top" data-badge="badge-aging-rayi">
                                <thead class="table-light">
                                    <tr>
                                        <th class="fw-semibold text-dark">Invoice &amp; PO</th>
                                        <th class="fw-semibold text-dark">Customer &amp; Sales</th>
                                        <th class="fw-semibold text-dark text-end">Nilai Invoice</th>
                                        <th class="fw-semibold text-dark text-center">Jatuh Tempo &amp; Aging</th>
                                        <th class="fw-semibold text-dark text-center">Reminder &amp; Action</th>
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
        .nav-tabs .nav-link {
            border: 1px solid transparent;
            border-top-left-radius: 8px;
            border-top-right-radius: 8px;
            color: #6c757d;
            transition: all 0.2s;
        }
        .nav-tabs .nav-link:hover {
            color: #696cff;
        }
        .nav-tabs .nav-link.active {
            border-color: #e0e2e8 #e0e2e8 #fff !important;
            background-color: #ffffff;
            color: #696cff !important;
            font-weight: 700;
        }
        table.dataTable thead th {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: .5px;
        }
        table.dataTable input.form-control {
            font-size: 11px;
            padding: 4px 8px;
            border-radius: 4px;
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

        // Initialize DataTable inside Detail Modals (Outstanding, Overdue, OnDue)
        $('#detailOutstanding, #detailOverdue, #detailOnDue').on('shown.bs.modal', function () {
            var $table = $(this).find('table');
            if (!$.fn.DataTable.isDataTable($table)) {
                $table.DataTable({
                    pageLength: 10,
                    lengthMenu: [10, 25, 50, 100],
                    language: {
                        search: "",
                        searchPlaceholder: "Cari invoice / customer...",
                        lengthMenu: "Tampilkan _MENU_ data",
                        info: "Menampilkan _START_ - _END_ dari _TOTAL_ invoice",
                        infoEmpty: "Tidak ada data",
                        zeroRecords: "Data tidak ditemukan",
                        paginate: {
                            first: '<i class="mdi mdi-chevron-double-left"></i>',
                            last: '<i class="mdi mdi-chevron-double-right"></i>',
                            next: '<i class="mdi mdi-chevron-right"></i>',
                            previous: '<i class="mdi mdi-chevron-left"></i>'
                        }
                    },
                    order: [[0, 'asc']],
                    dom: '<"d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3"lf>rt<"d-flex flex-wrap justify-content-between align-items-center gap-2 mt-3"ip>'
                });
            } else {
                $table.DataTable().columns.adjust().responsive.recalc();
            }
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
