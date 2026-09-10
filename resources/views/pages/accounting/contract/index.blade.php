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
                <div class="card-datatable table-responsive pt-0">
                    <table class="datatable-request-contract table">
                        <thead>
                            <tr>
                                <th></th>
                                <th>ID</th>
                                <th>No. Quotation</th>
                                <th>Company</th>
                                <th>PPN</th>
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
                <div class="d-flex align-items-center justify-content-end flex-wrap gap-3 p-3 border-bottom">
                    <div class="d-flex align-items-center gap-2">
                        <label class="form-label mb-0 fw-medium">Tahun:</label>
                        <select id="filter-year-selling" class="form-select form-select-sm" style="width:auto">
                            <option value="all">Semua</option>
                            @for ($y = now()->year; $y >= 2022; $y--)
                                <option value="{{ $y }}" {{ $y == now()->year ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                    <ul class="nav nav-pills gap-1" id="selling-subtab-nav" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button type="button" class="nav-link btn-sm py-1 px-3 active" role="tab" data-bs-toggle="tab" data-bs-target="#subtab-selling-ppn" aria-controls="subtab-selling-ppn" aria-selected="true">
                                <i class="tf-icons ti ti-file-percent me-1"></i> PPN
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button type="button" class="nav-link btn-sm py-1 px-3" role="tab" data-bs-toggle="tab" data-bs-target="#subtab-selling-non-ppn" aria-controls="subtab-selling-non-ppn" aria-selected="false">
                                <i class="tf-icons ti ti-file-x me-1"></i> Non-PPN
                            </button>
                        </li>
                    </ul>
                </div>

                <div class="tab-content p-0">
                    {{-- Subtab Selling PPN --}}
                    <div class="tab-pane fade show active" id="subtab-selling-ppn" role="tabpanel">
                        <div class="card-datatable table-responsive pt-0">
                            <table class="datatable-selling-contract-ppn table">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>ID</th>
                                        <th>Selling No.</th>
                                        <th>No. Quotation</th>
                                        <th>Company</th>
                                        <th>PPN</th>
                                        <th>Total Price</th>
                                        <th>Date</th>
                                        <th>Sales</th>
                                        <th>Approved By</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>

                    {{-- Subtab Selling Non-PPN --}}
                    <div class="tab-pane fade" id="subtab-selling-non-ppn" role="tabpanel">
                        <div class="card-datatable table-responsive pt-0">
                            <table class="datatable-selling-contract-non-ppn table">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>ID</th>
                                        <th>Selling No.</th>
                                        <th>No. Quotation</th>
                                        <th>Company</th>
                                        <th>PPN</th>
                                        <th>Total Price</th>
                                        <th>Date</th>
                                        <th>Sales</th>
                                        <th>Approved By</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── Tab Confirm Order ───────────────────────────────────── --}}
            <div class="tab-pane fade" id="tab-order" role="tabpanel">
                <div class="d-flex align-items-center justify-content-end flex-wrap gap-3 p-3 border-bottom">
                    <div class="d-flex align-items-center gap-2">
                        <label class="form-label mb-0 fw-medium">Tahun:</label>
                        <select id="filter-year-order" class="form-select form-select-sm" style="width:auto">
                            <option value="all">Semua</option>
                            @for ($y = now()->year; $y >= 2022; $y--)
                                <option value="{{ $y }}" {{ $y == now()->year ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                    <ul class="nav nav-pills gap-1" id="order-subtab-nav" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button type="button" class="nav-link btn-sm py-1 px-3 active" role="tab" data-bs-toggle="tab" data-bs-target="#subtab-order-ppn" aria-controls="subtab-order-ppn" aria-selected="true">
                                <i class="tf-icons ti ti-file-percent me-1"></i> PPN
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button type="button" class="nav-link btn-sm py-1 px-3" role="tab" data-bs-toggle="tab" data-bs-target="#subtab-order-non-ppn" aria-controls="subtab-order-non-ppn" aria-selected="false">
                                <i class="tf-icons ti ti-file-x me-1"></i> Non-PPN
                            </button>
                        </li>
                    </ul>
                </div>

                <div class="tab-content p-0">
                    {{-- Subtab Confirm Order PPN --}}
                    <div class="tab-pane fade show active" id="subtab-order-ppn" role="tabpanel">
                        <div class="card-datatable table-responsive pt-0">
                            <table class="datatable-confirm-order-ppn table">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>ID</th>
                                        <th>Order No.</th>
                                        <th>No. Quotation</th>
                                        <th>Company</th>
                                        <th>PPN</th>
                                        <th>Total Price</th>
                                        <th>Date</th>
                                        <th>Sales</th>
                                        <th>Approved By</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>

                    {{-- Subtab Confirm Order Non-PPN --}}
                    <div class="tab-pane fade" id="subtab-order-non-ppn" role="tabpanel">
                        <div class="card-datatable table-responsive pt-0">
                            <table class="datatable-confirm-order-non-ppn table">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>ID</th>
                                        <th>Order No.</th>
                                        <th>No. Quotation</th>
                                        <th>Company</th>
                                        <th>PPN</th>
                                        <th>Total Price</th>
                                        <th>Date</th>
                                        <th>Sales</th>
                                        <th>Approved By</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- Modals accept contract (dibutuhkan oleh tab Request) --}}
    @foreach ($contracts as $contract)
        @if ($contract->id_unit_quotation)
            @php
                $isOrderU = $contract->type == 'Order' || (bool) ($contract->unitQuotation?->isKojisha());
                $isPpnU   = (bool) ($contract->unitQuotation?->tax);
                $result = $isOrderU
                    ? ($isPpnU ? ($unitNumbers['nextCP'] ?? '001') : ($unitNumbers['nextCNP'] ?? '001'))
                    : ($isPpnU ? ($unitNumbers['nextSP'] ?? '001') : ($unitNumbers['nextSNP'] ?? '001'));
            @endphp
            @include('components.modal.accounting.accept-contract-unit')
        @else
            @php
                $isOrderS = $contract->type == 'Order' || (bool) ($contract->quotation?->isKojisha());
                $isPpnS   = ($contract->quotation?->tax == '11' || $contract->quotation?->tax == '1');
                if ($isOrderS) {
                    $result = $isPpnS ? $formattedNumberCP : $formattedNumberCNP;
                } else {
                    $result = $isPpnS ? $formattedNumberSP : $formattedNumberSNP;
                }
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
