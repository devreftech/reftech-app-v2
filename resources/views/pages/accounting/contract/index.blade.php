@extends('layouts.sales.app')
@section('title', 'Selling Contract')
@section('content')
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Accounting /</span> Selling Contract
    </h4>

    <div class="card">
        <div class="card-header py-2">
            <ul class="nav nav-tabs card-header-tabs border-0 m-0" id="contract-tab-nav" role="tablist">
                <li class="nav-item">
                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-request" type="button">
                        <i class="mdi mdi-file-clock-outline me-1"></i>Request
                        @if ($requestContract >= 1)
                            <span class="badge rounded-pill bg-danger ms-1">{{ $requestContract }}</span>
                        @endif
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-selling" type="button">
                        <i class="mdi mdi-file-sign me-1"></i>Selling Contract
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-order" type="button">
                        <i class="mdi mdi-file-check-outline me-1"></i>Confirm Order
                    </button>
                </li>
            </ul>
        </div>

        <div class="tab-content p-0">

            {{-- ── Tab Request ─────────────────────────────────────────── --}}
            <div class="tab-pane fade show active" id="tab-request" role="tabpanel">
                <div class="d-flex align-items-center gap-3 p-3 border-bottom">
                    <label class="form-label mb-0 fw-medium">Tahun:</label>
                    <select id="filter-year-request" class="form-select form-select-sm" style="width:auto">
                        <option value="all">Semua</option>
                        @for ($y = now()->year; $y >= 2022; $y--)
                            <option value="{{ $y }}" {{ $y == now()->year ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <div class="card-datatable table-responsive pt-0">
                    <table class="datatable-request-contract table table-striped">
                        <thead>
                            <tr>
                                <th></th>
                                <th>ID</th>
                                <th>No. Contract</th>
                                <th>Company</th>
                                <th>Total Price</th>
                                <th>Date</th>
                                <th>Sales</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>

            {{-- ── Tab Selling Contract ─────────────────────────────────── --}}
            <div class="tab-pane fade" id="tab-selling" role="tabpanel">
                <div class="d-flex align-items-center gap-3 p-3 border-bottom">
                    <label class="form-label mb-0 fw-medium">Tahun:</label>
                    <select id="filter-year-selling" class="form-select form-select-sm" style="width:auto">
                        <option value="all">Semua</option>
                        @for ($y = now()->year; $y >= 2022; $y--)
                            <option value="{{ $y }}" {{ $y == now()->year ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                    <label class="form-label mb-0 fw-medium ms-2">PPN:</label>
                    <select id="filter-tax-selling" class="form-select form-select-sm" style="width:auto">
                        <option value="all">Semua</option>
                        <option value="ppn">PPN</option>
                        <option value="non-ppn">Non PPN</option>
                    </select>
                </div>
                <div class="card-datatable table-responsive pt-0">
                    <table class="datatable-selling-contract-tab table table-striped">
                        <thead>
                            <tr>
                                <th></th>
                                <th>ID</th>
                                <th>Selling No.</th>
                                <th>Company</th>
                                <th>Total Price</th>
                                <th>Date</th>
                                <th>Sales</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>

            {{-- ── Tab Confirm Order ───────────────────────────────────── --}}
            <div class="tab-pane fade" id="tab-order" role="tabpanel">
                <div class="d-flex align-items-center gap-3 p-3 border-bottom">
                    <label class="form-label mb-0 fw-medium">Tahun:</label>
                    <select id="filter-year-order" class="form-select form-select-sm" style="width:auto">
                        <option value="all">Semua</option>
                        @for ($y = now()->year; $y >= 2022; $y--)
                            <option value="{{ $y }}" {{ $y == now()->year ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                    <label class="form-label mb-0 fw-medium ms-2">PPN:</label>
                    <select id="filter-tax-order" class="form-select form-select-sm" style="width:auto">
                        <option value="all">Semua</option>
                        <option value="ppn">PPN</option>
                        <option value="non-ppn">Non PPN</option>
                    </select>
                </div>
                <div class="card-datatable table-responsive pt-0">
                    <table class="datatable-confirm-order-tab table table-striped">
                        <thead>
                            <tr>
                                <th></th>
                                <th>ID</th>
                                <th>Order No.</th>
                                <th>Company</th>
                                <th>Total Price</th>
                                <th>Date</th>
                                <th>Sales</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>

        </div>
    </div>

    {{-- Modals accept contract (dibutuhkan oleh tab Request) --}}
    @foreach ($contracts as $contract)
        @if ($contract->id_unit_quotation)
            @php $result = $formattedNumberSC ?? str_pad(1, 3, '0', STR_PAD_LEFT); @endphp
            @include('components.modal.accounting.accept-contract-unit')
        @else
            @php
                $result = '';
                if ($contract->type == 'Selling' && $contract->quotation?->tax == '0') {
                    $sellingNonTax = $contract;
                } elseif ($contract->type == 'Selling' && $contract->quotation?->tax == '11') {
                    $sellingTax = $contract;
                } elseif ($contract->type == 'Order' && $contract->quotation?->tax == '0') {
                    $orderNonTax = $contract;
                } elseif ($contract->type == 'Order' && $contract->quotation?->tax == '11') {
                    $orderTax = $contract;
                }
                if (isset($sellingTax))      $result = $formattedNumberSP;
                elseif (isset($sellingNonTax)) $result = $formattedNumberSNP;
                elseif (isset($orderTax))      $result = $formattedNumberCP;
                elseif (isset($orderNonTax))   $result = $formattedNumberCNP;
            @endphp
            @include('components.modal.accounting.accept-contract')
        @endif
    @endforeach

@endsection

@push('after-style')
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/animate-css/animate.css">
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-rowgroup-bs5/rowgroup.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/formvalidation/dist/css/formValidation.min.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.css" />
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
    <script src="{{ asset('assets') }}/includes/table-request-contract.js"></script>
    <script src="{{ asset('assets') }}/includes/table-selling-contract-tab.js"></script>
    <script src="{{ asset('assets') }}/includes/table-confirm-order-tab.js"></script>
    <script src="{{ asset('assets') }}/js/extended-ui-sweetalert2.js"></script>
@endpush
