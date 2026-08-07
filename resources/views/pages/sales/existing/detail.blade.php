@extends('layouts.sales.app')
@section('title', 'Detail Existing')
@section('content')
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">CRM Existing /</span> Details {{ $existing->company }}
    </h4>

    <div class="card border">
        <div class="card-header py-2">
            <ul class="nav nav-tabs card-header-tabs border-0 m-0" id="existing-detail-tab-nav" role="tablist">
                <li class="nav-item">
                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-existing-detail"
                        type="button">
                        <i class="menu-icon tf-icons mdi mdi-account-details-outline me-1"></i>Detail
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-existing-crm" type="button">
                        <i class="menu-icon tf-icons mdi mdi-phone-outline me-1"></i>CRM Activity
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-existing-quotation"
                        type="button">
                        <i class="menu-icon tf-icons mdi mdi-file-document-outline me-1"></i>Quotation
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-existing-po" type="button">
                        <i class="menu-icon tf-icons mdi mdi-cart-outline me-1"></i>Purchase Order
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-existing-service"
                        type="button">
                        <i class="menu-icon tf-icons mdi mdi-wrench-outline me-1"></i>Service
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-existing-forecast"
                        type="button">
                        <i class="menu-icon tf-icons mdi mdi-chart-line me-1"></i>Forecast
                    </button>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content p-0">

                {{-- ==================== TAB 1: DETAIL ==================== --}}
                <div class="tab-pane fade show active" id="tab-existing-detail" role="tabpanel">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="border rounded p-3 h-100">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="fw-bold mb-0">Details</h5>
                                    <div class="d-flex gap-2">
                                        <a type="button" data-bs-toggle="modal"
                                            data-bs-target="#updateExisting{{ $existing->id }}">
                                            <button type="button" class="btn btn-sm btn-label-primary">Edit</button>
                                        </a>
                                        <a href="#" data-id="{{ $existing->id }}"
                                            class="btn btn-sm btn-label-danger delete-existing">Delete</a>
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-4 text-muted">Office / Factory</div>
                                    <div class="col-8">{{ $existing->address }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-4 text-muted">Area</div>
                                    <div class="col-8">{{ $existing->area }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-4 text-muted">Phone</div>
                                    <div class="col-8">{{ $existing->phone }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-4 text-muted">Email</div>
                                    <div class="col-8">{{ $existing->email }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-4 text-muted">Unit</div>
                                    <div class="col-8">{{ $existing->unit }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-4 text-muted">Mobile</div>
                                    <div class="col-8">{{ $existing->mobile }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-4 text-muted">R/U</div>
                                    <div class="col-8">{{ $existing->ru }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-4 text-muted">Source</div>
                                    <div class="col-8">{{ $existing->source }}</div>
                                </div>
                                <div class="row">
                                    <div class="col-4 text-muted">Assigned</div>
                                    <div class="col-8">{{ $existing->sales->name }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="border rounded p-3 h-100">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="fw-bold mb-0">PIC</h5>
                                    <a type="button" data-bs-toggle="modal" data-bs-target="#createPic">
                                        <button type="button" class="btn btn-primary">
                                            + New PIC
                                        </button>
                                    </a>
                                </div>
                                <div class="card-datatable table-responsive pt-0">
                                    <table
                                        class="datatable-pic-client{{ Auth::user()->role == 'Sales' ? '-sales' : '' }} table table-bordered">
                                        <thead>
                                            <tr>
                                                <th></th>
                                                <th></th>
                                                <th>ID</th>
                                                <th>Name</th>
                                                <th>Position</th>
                                                <th>Phone</th>
                                                <th>Email</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 mt-3">
                            <div class="card border shadow-none mb-0">
                                <div class="card-header bg-lighter py-2 d-flex justify-content-between align-items-center">
                                    <h6 class="fw-bold mb-0 text-primary">
                                        <i class="mdi mdi-file-certificate-outline me-1"></i>NPWP & Tax Details
                                    </h6>
                                    <a type="button" data-bs-toggle="modal" data-bs-target="#editNpwpDetails">
                                        <button type="button" class="btn btn-sm btn-label-primary">Edit</button>
                                    </a>
                                </div>
                                <div class="card-body py-3">
                                    <div class="row mb-2">
                                        <div class="col-md-2 text-muted fw-medium">No. NPWP</div>
                                        <div class="col-md-10 fw-semibold">{{ $existing->npwp ?? '-' }}</div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-2 text-muted fw-medium">Alamat NPWP</div>
                                        <div class="col-md-10">{{ $existing->subAddress ?? '-' }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 my-3">
                            <div class="border rounded p-3">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="fw-bold mb-0">Plant</h5>
                                    <a type="button" data-bs-toggle="modal" data-bs-target="#createPlant">
                                        <button type="button" class="btn btn-primary">
                                            + Tambah Plant
                                        </button>
                                    </a>
                                </div>
                                @forelse ($plants as $plant)
                                    <div
                                        class="d-flex justify-content-between align-items-start py-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                                        <div>
                                            <p class="fw-medium mb-1">{{ $plant->name }}</p>
                                            <p class="text-muted mb-0">{{ $plant->address }}</p>
                                        </div>
                                        <div class="d-flex gap-2 flex-shrink-0 ms-2">
                                            <a type="button" data-bs-toggle="modal"
                                                data-bs-target="#updatePlant-{{ $plant->id }}">
                                                <button type="button" class="btn btn-sm btn-label-primary">Edit</button>
                                            </a>
                                            <a href="#" data-id="{{ $plant->id }}"
                                                class="btn btn-sm btn-label-danger delete-plant">Delete</a>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-center text-muted mb-0">Belum ada Plant.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ==================== TAB 2: QUOTATION ==================== --}}
                <div class="tab-pane fade" id="tab-existing-quotation" role="tabpanel">
                    <div class="border rounded p-3 mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold mb-0">Quotation Aktif / Berjalan</h5>
                            <a href="{{ route('quotation.create') }}" type="button" class="btn btn-primary">
                                + New Quotation
                            </a>
                        </div>
                        <div class="card-datatable table-responsive pt-0">
                            <table class="datatable-quotation-active table table-bordered">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th></th>
                                        <th>ID</th>
                                        <th>Quote No.</th>
                                        <th>Total Price</th>
                                        <th>Description</th>
                                        <th>Date Quotation</th>
                                        <th>Status</th>
                                        <th>Date Expired</th>
                                        <th>Stats</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                    <div class="border rounded p-3 mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold mb-0">Quotation Loss</h5>
                        </div>
                        <div class="card-datatable table-responsive pt-0">
                            <table class="datatable-quotation-loss table table-bordered">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th></th>
                                        <th>ID</th>
                                        <th>Quote No.</th>
                                        <th>Total Price</th>
                                        <th>Description</th>
                                        <th>Date Quotation</th>
                                        <th>Status</th>
                                        <th>Date Expired</th>
                                        <th>Stats</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                    <div class="border rounded p-3">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold mb-0">Quotation Archive (Done PO)</h5>
                        </div>
                        <div class="card-datatable table-responsive pt-0">
                            <table class="datatable-quotation-archive table table-bordered">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th></th>
                                        <th>ID</th>
                                        <th>Quote No.</th>
                                        <th>Total Price</th>
                                        <th>Description</th>
                                        <th>Date Quotation</th>
                                        <th>Status</th>
                                        <th>Date Expired</th>
                                        <th>Stats</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- ==================== TAB 3: PURCHASE ORDER (KEY ACCOUNT) ==================== --}}
                <div class="tab-pane fade" id="tab-existing-po" role="tabpanel">
                    <div class="border rounded p-3 mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold mb-0">Key Account Summary</h5>
                            <div class="form-floating form-floating-outline" style="min-width: 160px">
                                <select id="poYearFilter" class="form-select">
                                    <option value="">All Time</option>
                                    @foreach ($poYears as $year)
                                        <option value="{{ $year }}" @selected($year == $yearsNow)>{{ $year }}</option>
                                    @endforeach
                                </select>
                                <label for="poYearFilter">Tahun</label>
                            </div>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="border rounded p-3 text-center h-100">
                                    <p class="text-muted mb-1">Total Revenue</p>
                                    <h4 class="fw-bold mb-0" id="poTotalRevenue">Rp 0</h4>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="border rounded p-3 text-center h-100">
                                    <p class="text-muted mb-1">Total PO</p>
                                    <h4 class="fw-bold mb-0" id="poTotalCount">0</h4>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="border rounded p-3 text-center h-100">
                                    <p class="text-muted mb-1">Avg. Deal Size</p>
                                    <h4 class="fw-bold mb-0" id="poAvgDeal">Rp 0</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="border rounded p-3">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold mb-0">Riwayat Purchase Order</h5>
                        </div>
                        <div class="card-datatable table-responsive pt-0">
                            <table class="datatable-po-history table table-bordered" id="dataTablePo">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th></th>
                                        <th>ID</th>
                                        <th>No Quote</th>
                                        <th>Deskripsi</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                        <th>Total Price</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- ==================== TAB 4: CRM ACTIVITY ==================== --}}
                <div class="tab-pane fade" id="tab-existing-crm" role="tabpanel">
                    <div class="border rounded p-3 mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold mb-0">CRM Existing</h5>
                            @if (Auth::user()->role == 'Sales')
                                <div class="d-flex gap-2">
                                    {{-- Request Visit dinonaktifkan sementara --}}
                                    {{-- <a type="button" data-bs-toggle="modal" data-bs-target="#createReqVisit">
                                        <button type="button" class="btn btn-label-instagram">
                                            + Request Visit
                                        </button>
                                    </a> --}}

                                    @php
                                        $emailPic = 0;
                                    @endphp

                                    @foreach ($charge as $pic)
                                        @php
                                            if ($pic->email_pic != null && $pic->email_pic != '-') {
                                                $emailPic++;
                                            }
                                        @endphp
                                    @endforeach

                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                        data-bs-target="#createAction{{ $existing->id }}"
                                        @if ($emailPic <= 0 || $existing->unit == null || $existing->unit == '-') disabled @endif>
                                        + New Action
                                    </button>
                                </div>
                            @endif
                        </div>
                        <div class="table-responsive text-nowrap">
                            <table class="table table-bordered">
                                <thead>
                                    <tr class="text-center">
                                        @php
                                            if ($monthNow <= 6) {
                                                $bulan = array_keys($crmhis);
                                                $mon1 = count($crmhis['January ' . $yearsNow]);
                                                $mon2 = count($crmhis['February ' . $yearsNow]);
                                                $mon3 = count($crmhis['March ' . $yearsNow]);
                                                $mon4 = count($crmhis['April ' . $yearsNow]);
                                                $mon5 = count($crmhis['May ' . $yearsNow]);
                                                $mon6 = count($crmhis['June ' . $yearsNow]);
                                            } elseif ($monthNow >= 7) {
                                                $bulan = array_keys($crmhis);
                                                $mon1 = count($crmhis['July ' . $yearsNow]);
                                                $mon3 = count($crmhis['August ' . $yearsNow]);
                                                $mon4 = count($crmhis['September ' . $yearsNow]);
                                                $mon5 = count($crmhis['October ' . $yearsNow]);
                                                $mon2 = count($crmhis['November ' . $yearsNow]);
                                                $mon6 = count($crmhis['December ' . $yearsNow]);
                                            }
                                        @endphp
                                        @foreach ($bulan as $data => $data_bulan)
                                            <th
                                                colspan="{{ $data_bulan == 'January ' . $yearsNow || $data_bulan == 'July ' . $yearsNow ? $mon1 : '' }}{{ $data_bulan == 'February ' . $yearsNow || $data_bulan == 'August ' . $yearsNow ? $mon2 : '' }}{{ $data_bulan == 'March ' . $yearsNow || $data_bulan == 'September ' . $yearsNow ? $mon3 : '' }}{{ $data_bulan == 'April ' . $yearsNow || $data_bulan == 'October ' . $yearsNow ? $mon4 : '' }}{{ $data_bulan == 'May ' . $yearsNow || $data_bulan == 'November ' . $yearsNow ? $mon5 : '' }}{{ $data_bulan == 'June ' . $yearsNow || $data_bulan == 'December ' . $yearsNow ? $mon6 : '' }}">
                                                {{ $data_bulan }}</th>
                                        @endforeach
                                    </tr>
                                    @if ($monthNow <= 6)
                                        <tr>
                                            @php
                                                $weeks = 0;
                                            @endphp
                                            @foreach ($crmhis['January ' . $yearsNow] as $data)
                                                @php
                                                    $weeks += 1;
                                                @endphp
                                                <th>Week {{ $weeks }}</th>
                                            @endforeach
                                            @php
                                                $weeks = 0;
                                            @endphp
                                            @foreach ($crmhis['February ' . $yearsNow] as $data)
                                                @php
                                                    $weeks += 1;
                                                @endphp
                                                <th>Week {{ $weeks }}</th>
                                            @endforeach
                                            @php
                                                $weeks = 0;
                                            @endphp
                                            @foreach ($crmhis['March ' . $yearsNow] as $data)
                                                @php
                                                    $weeks += 1;
                                                @endphp
                                                <th>Week {{ $weeks }}</th>
                                            @endforeach
                                            @php
                                                $weeks = 0;
                                            @endphp
                                            @foreach ($crmhis['April ' . $yearsNow] as $data)
                                                @php
                                                    $weeks += 1;
                                                @endphp
                                                <th>Week {{ $weeks }}</th>
                                            @endforeach
                                            @php
                                                $weeks = 0;
                                            @endphp
                                            @foreach ($crmhis['May ' . $yearsNow] as $data)
                                                @php
                                                    $weeks += 1;
                                                @endphp
                                                <th>Week {{ $weeks }}</th>
                                            @endforeach
                                            @php
                                                $weeks = 0;
                                            @endphp
                                            @foreach ($crmhis['June ' . $yearsNow] as $data)
                                                @php
                                                    $weeks += 1;
                                                @endphp
                                                <th>Week {{ $weeks }}</th>
                                            @endforeach
                                        </tr>
                                    @elseif($monthNow >= 7)
                                        <tr>
                                            @php
                                                $weeks = 0;
                                            @endphp
                                            @foreach ($crmhis['July ' . $yearsNow] as $data)
                                                @php
                                                    $weeks += 1;
                                                @endphp
                                                <th>Week {{ $weeks }}</th>
                                            @endforeach
                                            @php
                                                $weeks = 0;
                                            @endphp
                                            @foreach ($crmhis['August ' . $yearsNow] as $data)
                                                @php
                                                    $weeks += 1;
                                                @endphp
                                                <th>Week {{ $weeks }}</th>
                                            @endforeach
                                            @php
                                                $weeks = 0;
                                            @endphp
                                            @foreach ($crmhis['September ' . $yearsNow] as $data)
                                                @php
                                                    $weeks += 1;
                                                @endphp
                                                <th>Week {{ $weeks }}</th>
                                            @endforeach
                                            @php
                                                $weeks = 0;
                                            @endphp
                                            @foreach ($crmhis['October ' . $yearsNow] as $data)
                                                @php
                                                    $weeks += 1;
                                                @endphp
                                                <th>Week {{ $weeks }}</th>
                                            @endforeach
                                            @php
                                                $weeks = 0;
                                            @endphp
                                            @foreach ($crmhis['November ' . $yearsNow] as $data)
                                                @php
                                                    $weeks += 1;
                                                @endphp
                                                <th>Week {{ $weeks }}</th>
                                            @endforeach
                                            @php
                                                $weeks = 0;
                                            @endphp
                                            @foreach ($crmhis['December ' . $yearsNow] as $data)
                                                @php
                                                    $weeks += 1;
                                                @endphp
                                                <th>Week {{ $weeks }}</th>
                                            @endforeach
                                        </tr>
                                    @endif
                                </thead>
                                <tbody>
                                    <tr>
                                        @foreach ($crmhis as $item)
                                            @foreach ($item as $minggu)
                                                <td data-bs-toggle="tooltip" data-bs-placement="top"
                                                    data-bs-custom-class="tooltip-primary"
                                                    data-bs-original-title="{{ $minggu['note'][0] }}">
                                                    {{ $minggu['data'][0] }}
                                                </td>
                                            @endforeach
                                        @endforeach
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="row">
                        {{-- Request Visit dinonaktifkan sementara
                        <div class="col-md-12 my-3">
                            <div class="border rounded p-3">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="fw-bold mb-0">Request Visit</h5>
                                </div>
                                <div class="card-datatable table-responsive pt-0">
                                    <table class="datatable-visit table table-bordered" id="dataTableCrm">
                                        <thead>
                                            <tr>
                                                <th></th>
                                                <th></th>
                                                <th>ID</th>
                                                <th>machine</th>
                                                <th>Date Req</th>
                                                <th>Date</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                        --}}
                        <div class="col-md-12 my-3">
                            <div class="border rounded p-3">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="fw-bold mb-0">CRM History</h5>
                                </div>
                                @if ($activityTimeline->count())
                                    <ul class="timeline mb-0 ms-1" id="crmHistoryTimeline">
                                        @foreach ($activityTimeline as $index => $history)
                                            <li class="timeline-item timeline-item-transparent clearfix crm-history-item @if ($index >= 10) d-none @endif">
                                                <span
                                                    class="timeline-point timeline-point-{{ $history['color'] }}"></span>
                                                <div class="timeline-event">
                                                    <div class="timeline-header mb-1">
                                                        <h6 class="mb-0">
                                                            {{ $history['title'] }}
                                                            @if ($history['no_quote'])
                                                                <a href="{{ $history['url'] }}"
                                                                    class="ms-1">{{ $history['no_quote'] }}</a>
                                                            @endif
                                                        </h6>
                                                        <small class="text-muted">
                                                            {{ $history['date']->diffInDays(\Carbon\Carbon::now()) > 7 ? $history['date']->format('d M Y') : $history['date']->diffForHumans() }}
                                                        </small>
                                                    </div>
                                                    <span
                                                        class="badge bg-label-{{ $history['color'] }} mb-2">{{ $history['category'] }}</span>
                                                    <p class="mb-0">
                                                        <span class="fw-medium">{{ $history['status'] }}</span>
                                                        @if ($history['note'])
                                                            — {{ $history['note'] }}
                                                        @endif
                                                    </p>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                    @if ($activityTimeline->count() > 10)
                                        <div class="text-center mt-3">
                                            <button type="button" class="btn btn-label-primary btn-sm"
                                                id="crmHistoryLoadMore">
                                                Load More
                                            </button>
                                        </div>
                                    @endif
                                @else
                                    <p class="text-center text-muted mb-0">Belum ada CRM History.</p>
                                @endif
                            </div>
                        </div>
                        @if (optional(Auth::user()->detail->first())->area == 'Bekasi' ||
                                optional(Auth::user()->detail->first())->area == 'Jabodetabek' ||
                                (optional(Auth::user()->detail->first())->area == 'Jawa Barat' && Auth::user()->role == 'Sales'))
                            <div class="col-md-12 my-3">
                                <div class="border rounded p-3">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h5 class="fw-bold mb-0">Visit History</h5>
                                        <a type="button" data-bs-toggle="modal" data-bs-target="#createActionVisit">
                                            <button type="button" class="btn btn-primary">
                                                + New Action
                                            </button>
                                        </a>
                                    </div>
                                    <div class="table-responsive text-nowrap">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Action</th>
                                                    <th>Status</th>
                                                    <th>note</th>
                                                </tr>
                                            </thead>
                                            <tbody class="table-border-bottom-0">
                                                @forelse ($visit as $visits)
                                                    <tr>
                                                        <td>
                                                            {{ \Carbon\Carbon::parse($visits->date)->format('d-m-Y') }}
                                                        </td>
                                                        <td>
                                                            {{ $visits->action }}
                                                        </td>
                                                        <td>
                                                            {{ $visits->status }}
                                                        </td>
                                                        <td>
                                                            {{ $visits->note }}
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="4" class="text-center">
                                                            Kamu belum punya Visit.
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- ==================== TAB 5: SERVICE ==================== --}}
                <div class="tab-pane fade" id="tab-existing-service" role="tabpanel">
                    <div class="row">
                        <div class="col-md-12 my-3">
                            <div class="border rounded p-3">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="fw-bold mb-0">Machine</h5>
                                    <a type="button" data-bs-toggle="modal" data-bs-target="#createMachine">
                                        <button type="button" class="btn btn-primary">
                                            + Create New machine
                                        </button>
                                    </a>
                                </div>
                                <div class="card-datatable table-responsive pt-0">
                                    <table class="datatable-machine-client table table-bordered">
                                        <thead>
                                            <tr>
                                                <th></th>
                                                <th></th>
                                                <th>ID</th>
                                                <th>Category</th>
                                                <th>Brand</th>
                                                <th>Type</th>
                                                <th>SN</th>
                                                <th>Tag</th>
                                                <th>Location</th>
                                                <th>Service Report</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 my-3">
                            <div class="border rounded p-3 bg-white">
                                <div class="mb-3">
                                    <h5 class="fw-bold mb-1">Riwayat Laporan Servis</h5>
                                    <p class="text-muted small mb-0">Pilih kategori riwayat laporan di bawah untuk melihat detail servis teknisi.</p>
                                </div>

                                {{-- Sub-Nav Pills Navigation --}}
                                <ul class="nav nav-pills nav-fill mb-3 border-bottom pb-2" id="service-subtabs-existing" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active fw-semibold" id="subtab-existing-service-btn" data-bs-toggle="pill" data-bs-target="#subtab-existing-service" type="button" role="tab" aria-controls="subtab-existing-service" aria-selected="true">
                                            <i class="mdi mdi-wrench-outline me-1"></i>Service
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link fw-semibold" id="subtab-existing-visit-btn" data-bs-toggle="pill" data-bs-target="#subtab-existing-visit" type="button" role="tab" aria-controls="subtab-existing-visit" aria-selected="false">
                                            <i class="mdi mdi-map-marker-path me-1"></i>Visit
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link fw-semibold" id="subtab-existing-general-btn" data-bs-toggle="pill" data-bs-target="#subtab-existing-general" type="button" role="tab" aria-controls="subtab-existing-general" aria-selected="false">
                                            <i class="mdi mdi-clipboard-check-outline me-1"></i>General Check
                                        </button>
                                    </li>
                                </ul>

                                {{-- Sub-Tab Content Panes --}}
                                <div class="tab-content p-0" id="service-subtabs-existing-content">
                                    {{-- Sub-tab 1: Service History --}}
                                    <div class="tab-pane fade show active" id="subtab-existing-service" role="tabpanel" aria-labelledby="subtab-existing-service-btn">
                                        <div class="card-datatable table-responsive pt-0">
                                            <table class="datatable-service-history table table-bordered w-100" id="dataTableServiceHistory">
                                                <thead>
                                                    <tr>
                                                        <th></th>
                                                        <th></th>
                                                        <th>ID</th>
                                                        <th>No Service</th>
                                                        <th>Unit</th>
                                                        <th>Teknisi</th>
                                                        <th>Date</th>
                                                    </tr>
                                                </thead>
                                            </table>
                                        </div>
                                    </div>

                                    {{-- Sub-tab 2: Service Visit History --}}
                                    <div class="tab-pane fade" id="subtab-existing-visit" role="tabpanel" aria-labelledby="subtab-existing-visit-btn">
                                        <div class="card-datatable table-responsive pt-0">
                                            <table class="datatable-visit-history table table-bordered w-100" id="dataTableServiceVisitHistory">
                                                <thead>
                                                    <tr>
                                                        <th></th>
                                                        <th></th>
                                                        <th>ID</th>
                                                        <th>No Service</th>
                                                        <th>Unit</th>
                                                        <th>Teknisi</th>
                                                        <th>Date</th>
                                                    </tr>
                                                </thead>
                                            </table>
                                        </div>
                                    </div>

                                    {{-- Sub-tab 3: General Checkup History --}}
                                    <div class="tab-pane fade" id="subtab-existing-general" role="tabpanel" aria-labelledby="subtab-existing-general-btn">
                                        <div class="card-datatable table-responsive pt-0">
                                            <table class="datatable-general-history table table-bordered w-100" id="dataTableGeneralHistory">
                                                <thead>
                                                    <tr>
                                                        <th></th>
                                                        <th></th>
                                                        <th>ID</th>
                                                        <th>No Service</th>
                                                        <th>Unit</th>
                                                        <th>Teknisi</th>
                                                        <th>Date</th>
                                                    </tr>
                                                </thead>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ==================== TAB 6: FORECAST ==================== --}}
                <div class="tab-pane fade" id="tab-existing-forecast" role="tabpanel">
                    <div class="row">
                        <div class="col-md-12 my-3">
                            <form action="{{ route('forecast.setup.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="redirect_back" value="1">
                                
                                <!-- Forecast Settings Section -->
                                <div class="border rounded p-4 mb-4 bg-white shadow-sm" style="border-radius: 12px !important;">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div>
                                            <h5 class="fw-bold mb-0 text-dark"><i class="mdi mdi-cog-outline me-1"></i> Pengaturan Jadwal Forecast Unit</h5>
                                            <p class="text-muted mb-0 small">Atur jadwal rencana servis dan tipe PM untuk unit kompresor angin customer ini.</p>
                                        </div>
                                        @if(in_array(Auth::user()->role, ['Admin', 'Sales Manager']))
                                        <button type="submit" class="btn btn-primary px-4" style="border-radius: 8px; font-weight: 600;">
                                            <i class="mdi mdi-content-save-outline me-1"></i> Simpan Jadwal Forecast
                                        </button>
                                        @endif
                                    </div>
                                    
                                    @php
                                        $compressorMachines = $machines->filter(function($m) {
                                            return $m->unit && $m->unit->unit && strcasecmp($m->unit->unit->unit, 'AIR COMPRESSOR SCREW') === 0;
                                        });
                                        $isSales = !in_array(Auth::user()->role, ['Admin', 'Sales Manager']);
                                    @endphp

                                    <div class="table-responsive">
                                        <table class="table table-hover table-bordered align-middle m-0" style="font-size: 0.85rem;">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Unit Kompresor</th>
                                                    <th>Status & Tipe</th>
                                                    <th>Visit 1 (PM & Tanggal)</th>
                                                    <th>Visit 2 (PM & Tanggal)</th>
                                                    <th>Visit 3 (PM & Tanggal)</th>
                                                    <th>Visit 4 (PM & Tanggal - Opsional)</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($compressorMachines as $machine)
                                                <tr>
                                                    <td>
                                                        <span class="fw-bold text-dark d-block" style="font-size: 0.9rem;">{{ $machine->unit->brand ?? '-' }} {{ $machine->unit->unit->model ?? '-' }}</span>
                                                        <small class="text-muted d-block">{{ $machine->desc }}</small>
                                                        <small class="text-secondary font-mono d-block">S/N: {{ $machine->serial }} | kW: {{ $machine->unit->unit->power ?? '-' }}</small>
                                                    </td>
                                                    <td>
                                                        <select class="form-select form-select-sm mb-1" name="machines[{{ $machine->id }}][is_forecasted]" style="width: 130px; border-radius: 6px;" {{ $isSales ? 'disabled' : '' }}>
                                                            <option value="1" {{ $machine->is_forecasted ? 'selected' : '' }}>Forecast Aktif</option>
                                                            <option value="0" {{ !$machine->is_forecasted ? 'selected' : '' }}>Non-Aktif</option>
                                                        </select>
                                                        <select class="form-select form-select-sm" name="machines[{{ $machine->id }}][forecast_type]" style="width: 130px; border-radius: 6px;" {{ $isSales ? 'disabled' : '' }}>
                                                            <option value="parts" {{ $machine->forecast_type == 'parts' ? 'selected' : '' }}>Parts Only</option>
                                                            <option value="regular_service" {{ $machine->forecast_type == 'regular_service' ? 'selected' : '' }}>Regular Service</option>
                                                            <option value="contract" {{ $machine->forecast_type == 'contract' ? 'selected' : '' }}>Service Contract</option>
                                                        </select>
                                                        <input type="hidden" name="machines[{{ $machine->id }}][last_service_date]" value="{{ $machine->last_service_date }}">
                                                        @if($isSales)
                                                            <input type="hidden" name="machines[{{ $machine->id }}][is_forecasted]" value="{{ $machine->is_forecasted ? '1' : '0' }}">
                                                            <input type="hidden" name="machines[{{ $machine->id }}][forecast_type]" value="{{ $machine->forecast_type }}">
                                                        @endif
                                                    </td>
                                                    <!-- Visit 1 -->
                                                    <td>
                                                        <select class="form-select form-select-sm mb-1" name="machines[{{ $machine->id }}][visit_1_type]" style="width: 125px; border-radius: 6px;" {{ $isSales ? 'disabled' : '' }}>
                                                            <option value="" {{ is_null($machine->visit_1_type) ? 'selected' : '' }}>-- Jenis PM --</option>
                                                            <option value="PM1" {{ $machine->visit_1_type == 'PM1' ? 'selected' : '' }}>PM1 (Minor)</option>
                                                            <option value="PM2" {{ $machine->visit_1_type == 'PM2' ? 'selected' : '' }}>PM2 (Major)</option>
                                                        </select>
                                                        <input type="date" class="form-control form-control-sm" 
                                                               name="machines[{{ $machine->id }}][visit_1_date]" 
                                                               value="{{ $machine->visit_1_date ? \Carbon\Carbon::parse($machine->visit_1_date)->format('Y-m-d') : '' }}"
                                                               style="width: 125px; border-radius: 6px;"
                                                               {{ $isSales ? 'disabled' : '' }}>
                                                        @if($isSales)
                                                            <input type="hidden" name="machines[{{ $machine->id }}][visit_1_type]" value="{{ $machine->visit_1_type }}">
                                                            <input type="hidden" name="machines[{{ $machine->id }}][visit_1_date]" value="{{ $machine->visit_1_date }}">
                                                        @endif
                                                    </td>
                                                    <!-- Visit 2 -->
                                                    <td>
                                                        <select class="form-select form-select-sm mb-1" name="machines[{{ $machine->id }}][visit_2_type]" style="width: 125px; border-radius: 6px;" {{ $isSales ? 'disabled' : '' }}>
                                                            <option value="" {{ is_null($machine->visit_2_type) ? 'selected' : '' }}>-- Jenis PM --</option>
                                                            <option value="PM1" {{ $machine->visit_2_type == 'PM1' ? 'selected' : '' }}>PM1 (Minor)</option>
                                                            <option value="PM2" {{ $machine->visit_2_type == 'PM2' ? 'selected' : '' }}>PM2 (Major)</option>
                                                        </select>
                                                        <input type="date" class="form-control form-control-sm" 
                                                               name="machines[{{ $machine->id }}][visit_2_date]" 
                                                               value="{{ $machine->visit_2_date ? \Carbon\Carbon::parse($machine->visit_2_date)->format('Y-m-d') : '' }}"
                                                               style="width: 125px; border-radius: 6px;"
                                                               {{ $isSales ? 'disabled' : '' }}>
                                                        @if($isSales)
                                                            <input type="hidden" name="machines[{{ $machine->id }}][visit_2_type]" value="{{ $machine->visit_2_type }}">
                                                            <input type="hidden" name="machines[{{ $machine->id }}][visit_2_date]" value="{{ $machine->visit_2_date }}">
                                                        @endif
                                                    </td>
                                                    <!-- Visit 3 -->
                                                    <td>
                                                        <select class="form-select form-select-sm mb-1" name="machines[{{ $machine->id }}][visit_3_type]" style="width: 125px; border-radius: 6px;" {{ $isSales ? 'disabled' : '' }}>
                                                            <option value="" {{ is_null($machine->visit_3_type) ? 'selected' : '' }}>-- Jenis PM --</option>
                                                            <option value="PM1" {{ $machine->visit_3_type == 'PM1' ? 'selected' : '' }}>PM1 (Minor)</option>
                                                            <option value="PM2" {{ $machine->visit_3_type == 'PM2' ? 'selected' : '' }}>PM2 (Major)</option>
                                                        </select>
                                                        <input type="date" class="form-control form-control-sm" 
                                                               name="machines[{{ $machine->id }}][visit_3_date]" 
                                                               value="{{ $machine->visit_3_date ? \Carbon\Carbon::parse($machine->visit_3_date)->format('Y-m-d') : '' }}"
                                                               style="width: 125px; border-radius: 6px;"
                                                               {{ $isSales ? 'disabled' : '' }}>
                                                        @if($isSales)
                                                            <input type="hidden" name="machines[{{ $machine->id }}][visit_3_type]" value="{{ $machine->visit_3_type }}">
                                                            <input type="hidden" name="machines[{{ $machine->id }}][visit_3_date]" value="{{ $machine->visit_3_date }}">
                                                        @endif
                                                    </td>
                                                    <!-- Visit 4 -->
                                                    <td>
                                                        <select class="form-select form-select-sm mb-1" name="machines[{{ $machine->id }}][visit_4_type]" style="width: 125px; border-radius: 6px;" {{ $isSales ? 'disabled' : '' }}>
                                                            <option value="" {{ is_null($machine->visit_4_type) ? 'selected' : '' }}>-- Jenis PM --</option>
                                                            <option value="PM1" {{ $machine->visit_4_type == 'PM1' ? 'selected' : '' }}>PM1 (Minor)</option>
                                                            <option value="PM2" {{ $machine->visit_4_type == 'PM2' ? 'selected' : '' }}>PM2 (Major)</option>
                                                        </select>
                                                        <input type="date" class="form-control form-control-sm" 
                                                               name="machines[{{ $machine->id }}][visit_4_date]" 
                                                               value="{{ $machine->visit_4_date ? \Carbon\Carbon::parse($machine->visit_4_date)->format('Y-m-d') : '' }}"
                                                               style="width: 125px; border-radius: 6px;"
                                                               {{ $isSales ? 'disabled' : '' }}>
                                                        @if($isSales)
                                                            <input type="hidden" name="machines[{{ $machine->id }}][visit_4_type]" value="{{ $machine->visit_4_type }}">
                                                            <input type="hidden" name="machines[{{ $machine->id }}][visit_4_date]" value="{{ $machine->visit_4_date }}">
                                                        @endif
                                                    </td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="6" class="text-center py-4 text-muted">Client ini belum memiliki unit kompresor terdaftar untuk di-forecast.</td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </form>
                            
                            <!-- Breakdown Rencana Forecast Client Section -->
                            <div class="border rounded p-4 bg-white shadow-sm mt-4" style="border-radius: 12px !important;">
                                <h5 class="fw-bold mb-1 text-dark"><i class="mdi mdi-calculator-variant-outline me-1"></i> Breakdown Rencana Forecast Nilai Servis</h5>
                                <p class="text-muted mb-3 small">Detail nominal estimasi part & jasa berdasarkan jadwal yang Anda simpan di atas untuk tahun berjalan.</p>
                                
                                @php
                                    $clientDetails = [];
                                    foreach($compressorMachines as $machine) {
                                        if (!$machine->is_forecasted) continue;
                                        
                                        // Load rates
                                        $power = $machine->unit->unit->power ?? null;
                                        $servicePrices = null;
                                        if ($power) {
                                            $normalizedPower = \App\Http\Controllers\ForecastController::normalizePower($power);
                                            $servicePrices = \App\Models\PowerServicePrice::where('power', $normalizedPower)->first();
                                        }
                                        
                                        // Load PM template items (manually-curated per unit + level)
                                        $spareparts = collect();
                                        if ($machine->unit && $machine->unit->unit) {
                                            $spareparts = \App\Models\UnitPmTemplateItem::where('id_unit', $machine->unit->unit->id)
                                                ->where('type', 'part')
                                                ->with('equivalent')
                                                ->get();
                                        }
                                        
                                        $visits = [
                                            ['num' => 1, 'date' => $machine->visit_1_date, 'type' => $machine->visit_1_type],
                                            ['num' => 2, 'date' => $machine->visit_2_date, 'type' => $machine->visit_2_type],
                                            ['num' => 3, 'date' => $machine->visit_3_date, 'type' => $machine->visit_3_type],
                                            ['num' => 4, 'date' => $machine->visit_4_date, 'type' => $machine->visit_4_type],
                                        ];
                                        
                                        foreach($visits as $v) {
                                            if (empty($v['date']) || empty($v['type'])) continue;
                                            
                                            $pmLevel = $v['type'];
                                            
                                            // Calculate parts
                                            $partsTotal = 0;
                                            $includedParts = [];
                                            foreach($spareparts as $sp) {
                                                if ($sp->level != $pmLevel) continue;
                                                $sub = ($sp->qty * $sp->price);
                                                $partsTotal += $sub;
                                                $includedParts[] = [
                                                    'pn' => $sp->equivalent->pn ?? '-',
                                                    'brand' => $sp->equivalent->brand ?? '-',
                                                    'description' => $sp->description ?: $sp->label,
                                                    'qty' => $sp->qty,
                                                    'price' => $sp->price,
                                                    'subtotal' => $sub,
                                                    'pm_level' => $sp->level
                                                ];
                                            }
                                            
                                            // Calculate service
                                            $serviceFee = 0;
                                            if ($machine->forecast_type == 'regular_service' && $servicePrices) {
                                                switch ($pmLevel) {
                                                    case 'PM1': $serviceFee = $servicePrices->price_pm1; break;
                                                    case 'PM2': $serviceFee = $servicePrices->price_pm2; break;
                                                }
                                            }
                                            
                                            $clientDetails[] = [
                                                'brand' => $machine->unit->brand ?? '-',
                                                'model' => $machine->unit->unit->model ?? '-',
                                                'serial' => $machine->serial,
                                                'visit' => 'Visit ' . $v['num'] . ' (' . $pmLevel . ')',
                                                'date' => \Carbon\Carbon::parse($v['date'])->format('d-m-Y'),
                                                'raw_date' => $v['date'],
                                                'forecast_type' => $machine->forecast_type,
                                                'parts_cost' => $partsTotal,
                                                'service_fee' => $serviceFee,
                                                'total' => $partsTotal + $serviceFee,
                                                'parts_detail' => $includedParts
                                            ];
                                        }
                                    }
                                    
                                    // Sort clientDetails chronologically
                                    usort($clientDetails, function($a, $b) {
                                        return strtotime($a['raw_date']) - strtotime($b['raw_date']);
                                    });
                                @endphp
                                
                                <div class="table-responsive">
                                    <table class="table table-hover table-striped align-middle m-0" style="font-size: 0.85rem;">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Unit Kompresor</th>
                                                <th>Kunjungan</th>
                                                <th>Tanggal Rencana</th>
                                                <th>Tipe Forecast</th>
                                                <th class="text-end">Estimasi Part</th>
                                                <th class="text-end">Estimasi Jasa</th>
                                                <th class="text-end fw-bold">Total Forecast</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($clientDetails as $index => $detail)
                                            @php
                                                $badgeType = 'bg-label-primary';
                                                if($detail['forecast_type'] == 'parts') $badgeType = 'bg-label-warning';
                                                if($detail['forecast_type'] == 'contract') $badgeType = 'bg-label-success';
                                            @endphp
                                            <tr>
                                                <td>
                                                    <strong>{{ $detail['brand'] }} {{ $detail['model'] }}</strong>
                                                    <span class="text-muted d-block small">S/N: {{ $detail['serial'] }}</span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-label-secondary font-mono">{{ $detail['visit'] }}</span>
                                                </td>
                                                <td>
                                                    <span class="fw-semibold text-secondary">{{ $detail['date'] }}</span>
                                                </td>
                                                <td>
                                                    <span class="badge {{ $badgeType }}" style="font-size: 0.7rem;">
                                                        {{ $detail['forecast_type'] == 'regular_service' ? 'Regular Service' : ($detail['forecast_type'] == 'parts' ? 'Parts Only' : 'Contract') }}
                                                    </span>
                                                </td>
                                                <td class="text-end text-muted">
                                                    @if($detail['parts_cost'] > 0)
                                                    <a href="javascript:void(0)" class="text-decoration-underline text-primary fw-semibold" data-bs-toggle="modal" data-bs-target="#partsDetailModal-{{ $index }}">
                                                        Rp {{ number_format($detail['parts_cost'], 0, ',', '.') }}
                                                    </a>
                                                    @else
                                                    Rp 0
                                                    @endif
                                                </td>
                                                <td class="text-end text-muted">Rp {{ number_format($detail['service_fee'], 0, ',', '.') }}</td>
                                                <td class="text-end text-primary fw-bold">Rp {{ number_format($detail['total'], 0, ',', '.') }}</td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="7" class="text-center py-4 text-muted">Belum ada kunjungan forecast yang terjadwal.</td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Column for History Archives -->
                    <div class="col-md-12 mt-4">
                        <div class="card border">
                            <div class="card-header bg-light py-3 d-flex align-items-center justify-content-between">
                                <h6 class="m-0 fw-bold text-secondary"><i class="mdi mdi-archive-clock-outline me-1"></i> Arsip Rencana Forecast Tahunan</h6>
                                <span class="badge bg-label-info">History Log</span>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover table-striped align-middle m-0" style="font-size: 0.85rem;">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Unit Kompresor</th>
                                                <th class="text-center">Tahun</th>
                                                <th>Tipe Forecast</th>
                                                <th>Rencana Kunjungan (PM1/PM2 - Tanggal)</th>
                                                <th class="text-center">Status Aktif</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php $hasHistory = false; @endphp
                                            @foreach($compressorMachines as $mac)
                                                @if($mac->forecastHistories && $mac->forecastHistories->count() > 0)
                                                    @foreach($mac->forecastHistories->sortByDesc('year') as $hist)
                                                        @php $hasHistory = true; @endphp
                                                        <tr>
                                                            <td>
                                                                <strong>{{ $mac->unit->brand ?? '-' }} {{ $mac->unit->unit->model ?? '-' }}</strong>
                                                                <span class="text-muted d-block small">S/N: {{ $mac->serial }}</span>
                                                            </td>
                                                            <td class="text-center">
                                                                <span class="badge bg-label-secondary fw-bold">{{ $hist->year }}</span>
                                                            </td>
                                                            <td>
                                                                <span class="badge bg-label-primary" style="font-size: 0.7rem;">
                                                                    {{ $hist->forecast_type == 'regular_service' ? 'Regular Service' : ($hist->forecast_type == 'parts' ? 'Parts Only' : 'Contract') }}
                                                                </span>
                                                            </td>
                                                            <td>
                                                                <div class="d-flex flex-wrap gap-2">
                                                                    @for($v = 1; $v <= 4; $v++)
                                                                        @php
                                                                            $vType = $hist->{"visit_{$v}_type"};
                                                                            $vDate = $hist->{"visit_{$v}_date"};
                                                                        @endphp
                                                                        @if(!empty($vDate))
                                                                            <span class="badge bg-light text-dark border p-2">
                                                                                <strong class="text-info">V{{ $v }} ({{ $vType }})</strong>: 
                                                                                {{ \Carbon\Carbon::parse($vDate)->format('d-m-Y') }}
                                                                            </span>
                                                                        @endif
                                                                    @endfor
                                                                </div>
                                                            </td>
                                                            <td class="text-center">
                                                                @if($hist->is_forecasted)
                                                                    <span class="badge bg-label-success rounded-pill">Aktif</span>
                                                                @else
                                                                    <span class="badge bg-label-danger rounded-pill">Nonaktif</span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @endif
                                            @endforeach
                                            
                                            @if(!$hasHistory)
                                            <tr>
                                                <td colspan="5" class="text-center py-4 text-muted">Belum ada arsip riwayat forecast untuk customer ini.</td>
                                            </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    @if(!empty($clientDetails))
        @foreach($clientDetails as $index => $detail)
            @if($detail['parts_cost'] > 0)
            <!-- Parts Detail Modal -->
            <div class="modal fade" id="partsDetailModal-{{ $index }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-light py-3">
                            <h5 class="modal-title fw-bold text-dark mb-0">
                                <i class="mdi mdi-package-variant-closed me-1 text-primary"></i> Detail Estimasi Part: {{ $detail['brand'] }} {{ $detail['model'] }}
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-0">
                            <div class="p-3 bg-lighter border-bottom">
                                <span class="badge bg-label-info me-1">{{ $detail['visit'] }}</span>
                                <span class="text-secondary small">S/N: {{ $detail['serial'] }} | Rencana Tanggal: {{ $detail['date'] }}</span>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover table-striped align-middle m-0" style="font-size: 0.85rem;">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Part Number (PN)</th>
                                            <th>Description</th>
                                            <th>Level</th>
                                            <th class="text-center">Qty</th>
                                            <th class="text-end">Harga Satuan</th>
                                            <th class="text-end fw-bold">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($detail['parts_detail'] as $part)
                                        <tr>
                                            <td>
                                                <span class="font-mono fw-bold text-dark">{{ $part['pn'] }}</span>
                                                <small class="text-muted d-block">{{ $part['brand'] }}</small>
                                            </td>
                                            <td>{{ $part['description'] }}</td>
                                            <td>
                                                <span class="badge bg-label-secondary" style="font-size: 0.7rem;">{{ $part['pm_level'] }}</span>
                                            </td>
                                            <td class="text-center fw-semibold">{{ $part['qty'] }}</td>
                                            <td class="text-end text-muted">Rp {{ number_format($part['price'], 0, ',', '.') }}</td>
                                            <td class="text-end text-primary fw-bold">Rp {{ number_format($part['subtotal'], 0, ',', '.') }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="table-light">
                                        <tr>
                                            <td colspan="5" class="text-end fw-bold text-dark">Total Estimasi Part:</td>
                                            <td class="text-end fw-bold text-primary" style="font-size: 0.95rem;">Rp {{ number_format($detail['parts_cost'], 0, ',', '.') }}</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        <div class="modal-footer bg-light py-2">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        @endforeach
    @endif

    @include('pages.sales.existing.form')
    @include('components.modal.pic.existing.form-create')
    @include('components.modal.machine.form')
    @include('components.modal.req-visit.form-create')
    @include('components.modal.plant.form-create')
    @include('pages.sales.activities.form-existing')
    @include('pages.sales.activities.form-visit')
    @foreach ($charge as $pic)
        @include('components.modal.pic.existing.form-update')
    @endforeach
    @foreach ($machines as $machine)
        @include('components.modal.machine.form-edit')
    @endforeach
    @foreach ($plants as $plant)
        @include('components.modal.plant.form-update')
    @endforeach

    <div class="modal fade" id="machineReportsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="machineReportsModalTitle">Service Report</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="machineReportsList">
                        <p class="text-center text-muted mb-0">Memuat...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <form action="{{ route('customers.update', $existing->id) }}" method="post">
        @csrf
        @method('patch')
        <input type="hidden" name="company" value="{{ $existing->company }}">
        <input type="hidden" name="email" value="{{ $existing->email }}">
        <input type="hidden" name="phone" value="{{ $existing->phone }}">
        <input type="hidden" name="ru" value="{{ $existing->ru }}">
        <input type="hidden" name="unit" value="{{ $existing->unit }}">
        <input type="hidden" name="source" value="{{ $existing->source }}">
        <input type="hidden" name="mobile" value="{{ $existing->mobile }}">
        <input type="hidden" name="address" value="{{ $existing->address }}">
        <input type="hidden" name="area" value="{{ $existing->area }}">
        <input type="hidden" name="web" value="{{ $existing->web }}">
        @if (Auth::user()->id == 1 || Auth::user()->id == 16)
            <input type="hidden" name="info" value="{{ $existing->info }}">
        @endif

        <div class="modal fade" id="editNpwpDetails" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-md" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Edit NPWP & Tax Details</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-2 mb-3">
                            <div class="col-12 mb-2">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" id="npwpInput" class="form-control npwp-number-only" name="npwp"
                                        placeholder="16 Digit No. NPWP" value="{{ old('npwp', $existing->npwp) }}" 
                                        inputmode="numeric" pattern="\d{16}" minlength="16" maxlength="16"
                                        title="No. NPWP harus persis 16 digit angka" required>
                                    <label for="npwpInput">No. NPWP (16 Digit)</label>
                                </div>
                            </div>
                            <div class="col-12 mb-2">
                                <div class="form-floating form-floating-outline">
                                    <textarea class="form-control h-px-100" name="subAddress" id="subAddressInput"
                                        placeholder="Alamat NPWP">{{ old('subAddress', $existing->subAddress) }}</textarea>
                                    <label for="subAddressInput">Alamat NPWP</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary waves-effect"
                            data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary waves-effect waves-light">Save changes</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
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
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/select2/select2.css" />
@endpush
@push('after-script')
    <script src="{{ asset('assets') }}/vendor/libs/moment/moment.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/flatpickr/flatpickr.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/formvalidation/dist/js/FormValidation.min.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/select2/select2.js"></script>
@endpush
@push('page-script')
    <script src="{{ asset('assets') }}/js/tables-datatables-basic.js"></script>
    <script src="{{ asset('assets') }}/includes/table-quotation-client.js"></script>
    {{-- CRM History diganti jadi timeline server-side, gak perlu DataTables lagi --}}
    {{-- <script src="{{ asset('assets') }}/includes/table-crm-history.js"></script> --}}
    <script src="{{ asset('assets') }}/includes/table-po-history.js"></script>
    <script src="{{ asset('assets') }}/includes/table-machine-client.js"></script>
    <script src="{{ asset('assets') }}/includes/table-pic-client.js"></script>
    <script src="{{ asset('assets') }}/includes/table-pic-client-sales.js"></script>
    <script src="{{ asset('assets') }}/includes/table-service-history.js"></script>
    <script src="{{ asset('assets') }}/includes/table-general-history.js"></script>
    <script src="{{ asset('assets') }}/includes/table-visit-history.js"></script>
    {{-- Request Visit dinonaktifkan sementara --}}
    {{-- <script src="{{ asset('assets') }}/includes/table-req-visit.js"></script> --}}
    <script src="{{ asset('assets') }}/js/extended-ui-sweetalert2.js"></script>
    <script src="{{ asset('assets') }}/js/forms-selects.js"></script>
@endpush
@push('script')
    <script>
        // Re-adjust DataTables column widths when switching tabs, since tables
        // initialized inside a hidden tab-pane render with collapsed widths.
        $('#existing-detail-tab-nav button[data-bs-toggle="tab"]').on('shown.bs.tab', function() {
            $.fn.dataTable.tables({
                visible: true,
                api: true
            }).columns.adjust().responsive.recalc();
        });

        $(document).on('click', '#crmHistoryLoadMore', function() {
            $('#crmHistoryTimeline .crm-history-item.d-none').removeClass('d-none');
            $(this).parent().remove();
        });

        $(document).on('click', '.delete-pic', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, delete it!",
                customClass: {
                    confirmButton: "btn btn-primary me-3 waves-effect waves-light",
                    cancelButton: "btn btn-label-secondary waves-effect",
                },
                buttonsStyling: false,
            }).then(function(result) {
                if (result.value) {
                    $.ajax({
                        'url': '{{ url('pic') }}/' + id,
                        'type': 'POST',
                        'data': {
                            '_method': 'DELETE',
                            '_token': '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response == 1) {
                                Swal.fire({
                                    icon: "success",
                                    title: "Deleted!",
                                    text: "Your file has been deleted.",
                                    customClass: {
                                        confirmButton: "btn btn-success waves-effect",
                                    },
                                })
                                window.setTimeout(function() {
                                    location.reload();
                                }, 2000);
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: 'Data Failed to Delete!'
                                });
                            }
                        }
                    });
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    Swal.fire({
                        title: "Cancelled",
                        text: "Your imaginary file is safe :)",
                        icon: "error",
                        customClass: {
                            confirmButton: "btn btn-success waves-effect",
                        },
                    });
                }
            });
        });
        $(document).on('click', '.delete-machine', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, delete it!",
                customClass: {
                    confirmButton: "btn btn-primary me-3 waves-effect waves-light",
                    cancelButton: "btn btn-label-secondary waves-effect",
                },
                buttonsStyling: false,
            }).then(function(result) {
                if (result.value) {
                    $.ajax({
                        'url': '{{ url('machine') }}/' + id,
                        'type': 'POST',
                        'data': {
                            '_method': 'DELETE',
                            '_token': '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response == 1) {
                                Swal.fire({
                                    icon: "success",
                                    title: "Deleted!",
                                    text: "Your file has been deleted.",
                                    customClass: {
                                        confirmButton: "btn btn-success waves-effect",
                                    },
                                })
                                window.setTimeout(function() {
                                    location.reload();
                                }, 2000);
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: 'Data Failed to Delete!'
                                });
                            }
                        }
                    });
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    Swal.fire({
                        title: "Cancelled",
                        text: "Your imaginary file is safe :)",
                        icon: "error",
                        customClass: {
                            confirmButton: "btn btn-success waves-effect",
                        },
                    });
                }
            });
        });
        $(document).on('click', '.delete-plant', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, delete it!",
                customClass: {
                    confirmButton: "btn btn-primary me-3 waves-effect waves-light",
                    cancelButton: "btn btn-label-secondary waves-effect",
                },
                buttonsStyling: false,
            }).then(function(result) {
                if (result.value) {
                    $.ajax({
                        'url': '{{ url('plant') }}/' + id,
                        'type': 'POST',
                        'data': {
                            '_method': 'DELETE',
                            '_token': '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response == 1) {
                                Swal.fire({
                                    icon: "success",
                                    title: "Deleted!",
                                    text: "Your file has been deleted.",
                                    customClass: {
                                        confirmButton: "btn btn-success waves-effect",
                                    },
                                })
                                window.setTimeout(function() {
                                    location.reload();
                                }, 2000);
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: 'Data Failed to Delete!'
                                });
                            }
                        }
                    });
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    Swal.fire({
                        title: "Cancelled",
                        text: "Your imaginary file is safe :)",
                        icon: "error",
                        customClass: {
                            confirmButton: "btn btn-success waves-effect",
                        },
                    });
                }
            });
        });
        $(document).on('click', '.delete-existing', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, delete it!",
                customClass: {
                    confirmButton: "btn btn-primary me-3 waves-effect waves-light",
                    cancelButton: "btn btn-label-secondary waves-effect",
                },
                buttonsStyling: false,
            }).then(function(result) {
                if (result.value) {
                    $.ajax({
                        'url': '{{ url('existing') }}/' + id,
                        'type': 'POST',
                        'data': {
                            '_method': 'DELETE',
                            '_token': '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response == 1) {
                                Swal.fire({
                                    icon: "success",
                                    title: "Deleted!",
                                    text: "Your file has been deleted.",
                                    customClass: {
                                        confirmButton: "btn btn-success waves-effect",
                                    },
                                })
                                window.setTimeout(function() {
                                    window.location.href = '/existing';
                                }, 2000);
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: 'Data Failed to Delete!'
                                });
                            }
                        }
                    });
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    Swal.fire({
                        title: "Cancelled",
                        text: "Your imaginary file is safe :)",
                        icon: "error",
                        customClass: {
                            confirmButton: "btn btn-success waves-effect",
                        },
                    });
                }
            });
        });

        $(document).on('input', '.npwp-number-only', function () {
            this.value = this.value.replace(/[^0-9]/g, '').slice(0, 16);
        });

        // Re-adjust DataTables column widths when switching subtabs inside Tab Service
        $('#service-subtabs-existing button[data-bs-toggle="pill"]').on('shown.bs.tab', function() {
            $.fn.dataTable.tables({
                visible: true,
                api: true
            }).columns.adjust().responsive.recalc();
        });
    </script>
@endpush
