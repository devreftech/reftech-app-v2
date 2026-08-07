@extends('layouts.sales.app')
@section('title', 'Detail Leads')
@section('content')
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Clients / Leads /</span> Details {{ $existing->company }}
    </h4>

    <div class="card border">
        <div class="card-header py-2">
            <ul class="nav nav-tabs card-header-tabs border-0 m-0" id="leads-detail-tab-nav" role="tablist">
                <li class="nav-item">
                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-leads-detail"
                        type="button">
                        <i class="menu-icon tf-icons mdi mdi-account-details-outline me-1"></i>Detail
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-leads-crm" type="button">
                        <i class="menu-icon tf-icons mdi mdi-phone-outline me-1"></i>Daily Call
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-leads-quotation"
                        type="button">
                        <i class="menu-icon tf-icons mdi mdi-file-document-outline me-1"></i>Quotation
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-leads-service"
                        type="button">
                        <i class="menu-icon tf-icons mdi mdi-wrench-outline me-1"></i>Service
                    </button>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content p-0">

                {{-- ==================== TAB 1: DETAIL ==================== --}}
                <div class="tab-pane fade show active" id="tab-leads-detail" role="tabpanel">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="border rounded p-3 h-100">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="fw-bold mb-0">Details</h5>
                                    <div class="d-flex gap-2">
                                        <a type="button" data-bs-toggle="modal"
                                            data-bs-target="#updateLeads{{ $existing->id }}">
                                            <button type="button" class="btn btn-sm btn-label-primary">Edit</button>
                                        </a>
                                        <a href="#" data-id="{{ $existing->id }}"
                                            class="btn btn-sm btn-label-danger delete-leads">Delete</a>
                                        <a href="#" data-id="{{ $existing->id }}"
                                            class="btn btn-sm btn-label-info convert-customers">Convert Cust</a>
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
                <div class="tab-pane fade" id="tab-leads-quotation" role="tabpanel">
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



                {{-- ==================== TAB 4: DAILY CALL ==================== --}}
                <div class="tab-pane fade" id="tab-leads-crm" role="tabpanel">
                    <div class="row">
                        <div class="col-md-12 my-3">
                            <div class="border rounded p-3">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="fw-bold mb-0">Daily Call History</h5>
                                    <div class="d-flex gap-2">
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
                                            data-bs-target="#createAction{{ $leads->id }}">
                                            + New Action
                                        </button>
                                    </div>
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
                                    <p class="text-center text-muted mb-0">Belum ada Daily Call History.</p>
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
                <div class="tab-pane fade" id="tab-leads-service" role="tabpanel">
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
                                <ul class="nav nav-pills nav-fill mb-3 border-bottom pb-2" id="service-subtabs" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active fw-semibold" id="subtab-service-btn" data-bs-toggle="pill" data-bs-target="#subtab-service" type="button" role="tab" aria-controls="subtab-service" aria-selected="true">
                                            <i class="mdi mdi-wrench-outline me-1"></i>Service
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link fw-semibold" id="subtab-visit-btn" data-bs-toggle="pill" data-bs-target="#subtab-visit" type="button" role="tab" aria-controls="subtab-visit" aria-selected="false">
                                            <i class="mdi mdi-map-marker-path me-1"></i>Visit
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link fw-semibold" id="subtab-general-btn" data-bs-toggle="pill" data-bs-target="#subtab-general" type="button" role="tab" aria-controls="subtab-general" aria-selected="false">
                                            <i class="mdi mdi-clipboard-check-outline me-1"></i>General Check
                                        </button>
                                    </li>
                                </ul>

                                {{-- Sub-Tab Content Panes --}}
                                <div class="tab-content p-0" id="service-subtabs-content">
                                    {{-- Sub-tab 1: Service History --}}
                                    <div class="tab-pane fade show active" id="subtab-service" role="tabpanel" aria-labelledby="subtab-service-btn">
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
                                    <div class="tab-pane fade" id="subtab-visit" role="tabpanel" aria-labelledby="subtab-visit-btn">
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
                                    <div class="tab-pane fade" id="subtab-general" role="tabpanel" aria-labelledby="subtab-general-btn">
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

            </div>
        </div>
    </div>

    @include('pages.sales.clients.leads.form')
    @include('components.modal.pic.leads.form-create')
    @include('components.modal.machine.form')
    @include('components.modal.req-visit.form-create')
    @include('components.modal.plant.form-create')
    @include('pages.sales.activities.form')
    @include('pages.sales.activities.form-visit')
    @foreach ($charge as $pic)
        @include('components.modal.pic.leads.form-update')
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

    <form action="{{ route('leads.update', $existing->id) }}" method="post">
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
    <script src="{{ asset('assets') }}/includes/table-machine-client.js"></script>
    <script src="{{ asset('assets') }}/includes/table-pic-client.js"></script>
    <script src="{{ asset('assets') }}/includes/table-pic-client-sales.js"></script>
    <script src="{{ asset('assets') }}/includes/table-service-history.js"></script>
    <script src="{{ asset('assets') }}/includes/table-general-history.js"></script>
    <script src="{{ asset('assets') }}/includes/table-visit-history.js"></script>
    <script src="{{ asset('assets') }}/js/extended-ui-sweetalert2.js"></script>
    <script src="{{ asset('assets') }}/js/forms-selects.js"></script>
@endpush
@push('script')
    <script>
        // Re-adjust DataTables column widths when switching tabs
        $('#leads-detail-tab-nav button[data-bs-toggle="tab"]').on('shown.bs.tab', function() {
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

        $(document).on('click', '.delete-leads', function() {
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
                        'url': '{{ url('leads') }}/' + id,
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
                                    window.location.href = '/leads';
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

        $(document).on('click', '.convert-customers', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, Convert it!",
                customClass: {
                    confirmButton: "btn btn-primary me-3 waves-effect waves-light",
                    cancelButton: "btn btn-label-secondary waves-effect",
                },
                buttonsStyling: false,
            }).then(function(result) {
                if (result.value) {
                    $.ajax({
                        'url': '{{ url('leads') }}/convert/' + id,
                        'type': 'POST',
                        'data': {
                            '_method': 'POST',
                            '_token': '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response == 1) {
                                Swal.fire({
                                    icon: "success",
                                    title: "Converted!",
                                    text: "Your file has been converted.",
                                    customClass: {
                                        confirmButton: "btn btn-success waves-effect",
                                    },
                                })
                                window.setTimeout(function() {
                                    window.location.href = '/existing/' + id;
                                }, 2000);
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: 'Data Failed to Convert!'
                                });
                            }
                        }
                    });
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    Swal.fire({
                        title: "Cancelled",
                        text: "Your Convert is cancelled :)",
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
        $('#service-subtabs button[data-bs-toggle="pill"]').on('shown.bs.tab', function() {
            $.fn.dataTable.tables({
                visible: true,
                api: true
            }).columns.adjust().responsive.recalc();
        });
    </script>
@endpush
