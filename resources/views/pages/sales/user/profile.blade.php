@extends('layouts.sales.app')
@section('title', 'My Profile')
@section('content')
    @php
        $userAvatar = $user->image ? url('/') . '/' . $user->image : asset('assets/img/avatars/1.png');

        // Smart Quotes Queries
        $sqHot          = \App\Models\UnitQuotation::where('id_sales', $user->id)->where('status', 'hot_prospect')->count();
        $sqNego         = \App\Models\UnitQuotation::where('id_sales', $user->id)->whereIn('status', ['negotiation', 'revision'])->count();
        $sqPo           = \App\Models\UnitQuotation::where('id_sales', $user->id)->where('status', 'po_received')->count();
        $sqDraft        = \App\Models\UnitQuotation::where('id_sales', $user->id)->whereIn('status', ['draft', 'sent'])->count();
        $sqLoss         = \App\Models\UnitQuotation::where('id_sales', $user->id)->where('status', 'loss')->count();
        $totalSmartQuotes = \App\Models\UnitQuotation::where('id_sales', $user->id)->count();

        // Legacy Quotes Queries
        $lqHot          = \App\Models\Quotation::where('id_sales', $user->id)->whereIn('status', [70, 75, 80, 90])->count();
        $lqNego         = \App\Models\Quotation::where('id_sales', $user->id)->whereIn('status', [30, 40, 50, 60])->count();
        $lqPo           = \App\Models\Quotation::where('id_sales', $user->id)->where('status', 100)->count();
        $lqDraft        = \App\Models\Quotation::where('id_sales', $user->id)->whereIn('status', [0, 10, 20])->count();
        $lqLoss         = \App\Models\Quotation::where('id_sales', $user->id)->where('status', '<', 0)->count();
        $totalLegacyQuotes = \App\Models\Quotation::where('id_sales', $user->id)->count();

        // Combined Totals
        $countHotProspect = $sqHot + $lqHot;
        $countNegotiation = $sqNego + $lqNego;
        $countPoReceived  = $sqPo + $lqPo;
        $countDraft       = $sqDraft + $lqDraft;
        $countLoss        = $sqLoss + $lqLoss;
        $totalQuotations  = $totalSmartQuotes + $totalLegacyQuotes;

        $totalClients   = \App\Models\Client::where('id_sales', $user->id)->count();
        $totalCustomers = \App\Models\Client::where('id_sales', $user->id)->whereIn('role', ['Customers', 'Customer'])->count();
        $totalDonePo    = $countPoReceived;

        // Type Breakdown (Combined Smart Quote + Legacy Quote)
        $typeUnit      = \App\Models\UnitQuotation::where('id_sales', $user->id)->where('type', 'Unit')->count()
                       + \App\Models\Quotation::where('id_sales', $user->id)->where('no_quote', 'LIKE', '%-U/%')->count();
        $typeParts     = \App\Models\UnitQuotation::where('id_sales', $user->id)->where('type', 'Parts')->count()
                       + \App\Models\Quotation::where('id_sales', $user->id)->where('no_quote', 'LIKE', '%-P/%')->count();
        $typeService   = \App\Models\UnitQuotation::where('id_sales', $user->id)->where('type', 'Service')->count()
                       + \App\Models\Quotation::where('id_sales', $user->id)->where('no_quote', 'LIKE', '%-S/%')->count();
        $typeRental    = \App\Models\UnitQuotation::where('id_sales', $user->id)->where('type', 'Rental')->count()
                       + \App\Models\Quotation::where('id_sales', $user->id)->where('no_quote', 'LIKE', '%-R/%')->count();
        $typeProject   = \App\Models\UnitQuotation::where('id_sales', $user->id)->where('type', 'Project')->count()
                       + \App\Models\Quotation::where('id_sales', $user->id)->where('no_quote', 'LIKE', '%-PR/%')->count();
        $typePiping    = \App\Models\UnitQuotation::where('id_sales', $user->id)->where('type', 'Piping')->count()
                       + \App\Models\Quotation::where('id_sales', $user->id)->where('no_quote', 'LIKE', '%-PIP/%')->count();
        $typeAirAudit  = \App\Models\UnitQuotation::where('id_sales', $user->id)->where('type', 'Air Audit')->count()
                       + \App\Models\Quotation::where('id_sales', $user->id)->where('no_quote', 'LIKE', '%-AA/%')->count();

        // Payment Templates & Clients for logged-in sales user
        $paymentTemplates = \App\Models\SalesPaymentTemplate::with('client')->where('id_sales', $user->id)->orderBy('is_default', 'desc')->orderBy('name')->get();
        $salesClients = \App\Models\Client::where('id_sales', $user->id)->orderBy('company')->get();
    @endphp

    {{-- Breadcrumb --}}
    <h4 class="fw-bold py-3 mb-3"><span class="text-muted fw-light">User /</span> Profile</h4>

    {{-- Hero Profile Banner Card --}}
    <div class="card mb-4 border-0 shadow-sm overflow-hidden" style="border-radius: 16px;">
        <div style="background: linear-gradient(135deg, #666cff 0%, #4f46e5 100%); height: 130px; position: relative;">
            <div class="position-absolute end-0 bottom-0 opacity-25 p-3">
                <i class="mdi mdi-account-circle-outline text-white" style="font-size: 140px; margin-right: -20px; margin-bottom: -40px;"></i>
            </div>
        </div>
        <div class="card-body pt-0 pb-4">
            <div class="d-flex flex-column flex-md-row align-items-center align-items-md-end gap-3" style="margin-top: -50px;">
                <div class="position-relative">
                    <img src="{{ $userAvatar }}" alt="{{ $user->name }}" class="rounded-circle border border-4 border-white shadow" style="width: 110px; height: 110px; object-fit: cover;">
                    <span class="position-absolute bottom-0 end-0 p-1 bg-success border border-2 border-white rounded-circle" title="Active Account" style="width: 16px; height: 16px;"></span>
                </div>
                <div class="flex-grow-1 text-center text-md-start">
                    <h4 class="fw-bold mb-1 text-dark">{{ $user->name }}</h4>
                    <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-md-start gap-2 mb-2">
                        <span class="badge bg-label-primary px-2.5 py-1 fw-semibold"><i class="mdi mdi-shield-account-outline me-1"></i>{{ $user->role }}</span>
                        @if($user->code)
                            <span class="badge bg-label-warning px-2.5 py-1 fw-semibold"><i class="mdi mdi-ticket-confirmation-outline me-1"></i>Code: {{ $user->code }}</span>
                        @endif
                        @if($user->area)
                            <span class="badge bg-label-info px-2.5 py-1 fw-semibold"><i class="mdi mdi-map-marker-outline me-1"></i>{{ $user->area }}</span>
                        @endif
                        <span class="badge bg-label-secondary px-2.5 py-1 fw-semibold">
                            <i class="mdi mdi-calendar-blank-outline me-1"></i>Joined {{ $user->created_date ? \Carbon\Carbon::parse($user->created_date)->format('M Y') : ($user->created_at ? $user->created_at->format('M Y') : '-') }}
                        </span>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('profile.edit', Auth::user()->id) }}" class="btn btn-primary shadow-sm">
                        <i class="mdi mdi-cog-outline me-1"></i> Account Settings
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- KPI Summary Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100 profile-kpi-card">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted fw-semibold small text-uppercase tracking-wider">Total Quotation</span>
                        <div class="avatar avatar-sm">
                            <span class="avatar-initial rounded-3 bg-label-primary">
                                <i class="mdi mdi-file-document-outline mdi-20px"></i>
                            </span>
                        </div>
                    </div>
                    <h3 class="mb-1 fw-bold text-dark">{{ number_format($totalQuotations) }}</h3>
                    <span class="text-muted small"><i class="mdi mdi-check-circle-outline me-1 text-primary"></i>Smart & Legacy Quote</span>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100 profile-kpi-card">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted fw-semibold small text-uppercase tracking-wider">Done PO</span>
                        <div class="avatar avatar-sm">
                            <span class="avatar-initial rounded-3 bg-label-success">
                                <i class="mdi mdi-cart-check mdi-20px"></i>
                            </span>
                        </div>
                    </div>
                    <h3 class="mb-1 fw-bold text-dark">{{ number_format($totalDonePo) }}</h3>
                    <span class="text-muted small"><i class="mdi mdi-trending-up me-1 text-success"></i>Completed Orders</span>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100 profile-kpi-card">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted fw-semibold small text-uppercase tracking-wider">Total Client</span>
                        <div class="avatar avatar-sm">
                            <span class="avatar-initial rounded-3 bg-label-info">
                                <i class="mdi mdi-domain mdi-20px"></i>
                            </span>
                        </div>
                    </div>
                    <h3 class="mb-1 fw-bold text-dark">{{ number_format($totalClients) }}</h3>
                    <span class="text-muted small"><i class="mdi mdi-account-group-outline me-1 text-info"></i>Assigned Clients</span>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100 profile-kpi-card">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted fw-semibold small text-uppercase tracking-wider">Total Customer</span>
                        <div class="avatar avatar-sm">
                            <span class="avatar-initial rounded-3 bg-label-warning">
                                <i class="mdi mdi-account-star-outline mdi-20px"></i>
                            </span>
                        </div>
                    </div>
                    <h3 class="mb-1 fw-bold text-dark">{{ number_format($totalCustomers) }}</h3>
                    <span class="text-muted small"><i class="mdi mdi-check-decagram-outline me-1 text-warning"></i>Customer Accounts</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Content Section --}}
    <div class="row g-4">
        {{-- Left Column: User Bio & Info & Payment Templates --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
                <div class="card-header bg-transparent border-bottom py-3 d-flex align-items-center justify-content-between">
                    <h6 class="card-title mb-0 fw-bold text-dark">
                        <i class="mdi mdi-account-card-details-outline me-2 text-primary fs-5"></i>Personal Details
                    </h6>
                    <span class="badge bg-label-success rounded-pill">Active</span>
                </div>
                <div class="card-body py-3">
                    <ul class="list-unstyled mb-0">
                        <li class="d-flex align-items-center py-2 border-bottom">
                            <i class="mdi mdi-account-outline text-muted fs-5 me-3"></i>
                            <div class="flex-grow-1">
                                <span class="text-muted small d-block">Full Name</span>
                                <span class="fw-semibold text-dark">{{ $user->name }}</span>
                            </div>
                        </li>
                        <li class="d-flex align-items-center py-2 border-bottom">
                            <i class="mdi mdi-ticket-confirmation-outline text-muted fs-5 me-3"></i>
                            <div class="flex-grow-1">
                                <span class="text-muted small d-block">Sales Code</span>
                                <span class="fw-semibold text-dark">{{ $user->code ?? '-' }}</span>
                            </div>
                        </li>
                        <li class="d-flex align-items-center py-2 border-bottom">
                            <i class="mdi mdi-email-outline text-muted fs-5 me-3"></i>
                            <div class="flex-grow-1">
                                <span class="text-muted small d-block">Email Address</span>
                                <span class="fw-semibold text-dark">{{ $user->email }}</span>
                            </div>
                        </li>
                        <li class="d-flex align-items-center py-2 border-bottom">
                            <i class="mdi mdi-phone-outline text-muted fs-5 me-3"></i>
                            <div class="flex-grow-1">
                                <span class="text-muted small d-block">Contact Phone</span>
                                <span class="fw-semibold text-dark">{{ $user->phone ?? '-' }}</span>
                            </div>
                        </li>
                        <li class="d-flex align-items-center py-2 border-bottom">
                            <i class="mdi mdi-map-marker-outline text-muted fs-5 me-3"></i>
                            <div class="flex-grow-1">
                                <span class="text-muted small d-block">Sales Area</span>
                                <span class="fw-semibold text-dark">{{ $user->area ?? '-' }}</span>
                            </div>
                        </li>
                        <li class="d-flex align-items-center py-2">
                            <i class="mdi mdi-shield-account-outline text-muted fs-5 me-3"></i>
                            <div class="flex-grow-1">
                                <span class="text-muted small d-block">Role Access</span>
                                <span class="badge bg-label-primary rounded-pill">{{ $user->role }}</span>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>

            {{-- Payment Templates Card --}}
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
                <div class="card-header bg-transparent border-bottom py-3 d-flex align-items-center justify-content-between">
                    <h6 class="card-title mb-0 fw-bold text-dark">
                        <i class="mdi mdi-credit-card-outline me-2 text-primary fs-5"></i>Payment Templates
                    </h6>
                    <button type="button" class="btn btn-sm btn-primary" id="btn-add-payment-template">
                        <i class="mdi mdi-plus me-1"></i> Tambah
                    </button>
                </div>
                <div class="card-body py-3">
                    <div id="payment-templates-list">
                        @forelse($paymentTemplates as $pt)
                            <div class="p-3 rounded border mb-3 position-relative bg-light-subtle">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <h6 class="fw-bold text-dark mb-0">{{ $pt->name }}</h6>
                                    <div>
                                        @if($pt->is_default)
                                            <span class="badge bg-success rounded-pill me-1"><i class="mdi mdi-check-circle-outline me-1"></i>Default</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="mb-2">
                                    @php $cList = $pt->clients_list; @endphp
                                    @if($cList->count() > 0)
                                        @foreach($cList as $cItem)
                                            <span class="badge bg-label-info rounded-pill me-1 mb-1" title="Khusus Client: {{ $cItem->company }}">
                                                <i class="mdi mdi-domain me-1"></i>{{ $cItem->company }}
                                            </span>
                                        @endforeach
                                    @else
                                        <span class="badge bg-label-secondary rounded-pill">Semua Client</span>
                                    @endif
                                </div>
                                <p class="text-muted small mb-2 text-wrap" style="white-space: pre-line;">{{ $pt->payment_term }}</p>
                                @php
                                    $cIds = $pt->client_ids ?: ($pt->id_client ? [(int)$pt->id_client] : []);
                                @endphp
                                <div class="d-flex gap-2 justify-content-end pt-1 border-top">
                                    @if(!$pt->is_default)
                                        <button type="button" class="btn btn-xs btn-label-success btn-set-default-template" data-id="{{ $pt->id }}">
                                            Set Default
                                        </button>
                                    @endif
                                    <button type="button" class="btn btn-xs btn-label-primary btn-edit-template" 
                                        data-id="{{ $pt->id }}" 
                                        data-name="{{ $pt->name }}" 
                                        data-term="{{ $pt->payment_term }}" 
                                        data-clients="{{ json_encode($cIds) }}" 
                                        data-default="{{ $pt->is_default ? 1 : 0 }}">
                                        Edit
                                    </button>
                                    <button type="button" class="btn btn-xs btn-label-danger btn-delete-template" data-id="{{ $pt->id }}">
                                        Hapus
                                    </button>
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-muted py-3">
                                <i class="mdi mdi-credit-card-remove-outline fs-3 d-block mb-1"></i>
                                Belum ada template payment tersimpan.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Quick Links Card --}}
            <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-header bg-transparent border-bottom py-3">
                    <h6 class="card-title mb-0 fw-bold text-dark">
                        <i class="mdi mdi-link-variant me-2 text-primary fs-5"></i>Quick Actions
                    </h6>
                </div>
                <div class="card-body py-3">
                    <div class="d-grid gap-2">
                        <a href="{{ route('unit-quotation.create') }}" class="btn btn-outline-primary btn-sm text-start py-2">
                            <i class="mdi mdi-sparkles text-warning me-2 fs-6"></i> Create Smart Quote
                        </a>
                        <a href="{{ route('quotation.index') }}" class="btn btn-outline-secondary btn-sm text-start py-2">
                            <i class="mdi mdi-file-document-outline me-2 fs-6"></i> Quotation Dashboard
                        </a>
                        <a href="{{ route('profile.edit', Auth::user()->id) }}" class="btn btn-outline-secondary btn-sm text-start py-2">
                            <i class="mdi mdi-cog-outline me-2 fs-6"></i> Account Settings
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Column: Sales Performance & Pipeline Overview --}}
        <div class="col-lg-8">
            {{-- Card 1: Quotation Pipeline Status --}}
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
                <div class="card-header bg-transparent border-bottom py-3 d-flex align-items-center justify-content-between">
                    <h6 class="card-title mb-0 fw-bold text-dark">
                        <i class="mdi mdi-chart-box-outline me-2 text-primary fs-5"></i>Quotation Pipeline Status
                    </h6>
                    <span class="badge bg-label-primary rounded-pill">All Quotation Formats</span>
                </div>
                <div class="card-body py-4">
                    <div class="row g-3">
                        <div class="col-sm-6 col-md-4">
                            <div class="p-3 rounded border bg-light-subtle text-center">
                                <div class="avatar avatar-sm mx-auto mb-2">
                                    <span class="avatar-initial rounded-circle bg-label-danger"><i class="mdi mdi-fire mdi-20px"></i></span>
                                </div>
                                <h4 class="fw-bold mb-1 text-dark">{{ number_format($countHotProspect) }}</h4>
                                <span class="text-muted small fw-semibold">Hot Prospect</span>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-4">
                            <div class="p-3 rounded border bg-light-subtle text-center">
                                <div class="avatar avatar-sm mx-auto mb-2">
                                    <span class="avatar-initial rounded-circle bg-label-warning"><i class="mdi mdi-swap-horizontal mdi-20px"></i></span>
                                </div>
                                <h4 class="fw-bold mb-1 text-dark">{{ number_format($countNegotiation) }}</h4>
                                <span class="text-muted small fw-semibold">Nego & Revisi</span>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-4">
                            <div class="p-3 rounded border bg-light-subtle text-center">
                                <div class="avatar avatar-sm mx-auto mb-2">
                                    <span class="avatar-initial rounded-circle bg-label-success"><i class="mdi mdi-cart-check mdi-20px"></i></span>
                                </div>
                                <h4 class="fw-bold mb-1 text-dark">{{ number_format($countPoReceived) }}</h4>
                                <span class="text-muted small fw-semibold">PO Received</span>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-4">
                            <div class="p-3 rounded border bg-light-subtle text-center">
                                <div class="avatar avatar-sm mx-auto mb-2">
                                    <span class="avatar-initial rounded-circle bg-label-info"><i class="mdi mdi-file-document-edit-outline mdi-20px"></i></span>
                                </div>
                                <h4 class="fw-bold mb-1 text-dark">{{ number_format($countDraft) }}</h4>
                                <span class="text-muted small fw-semibold">Draft / Sent</span>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-4">
                            <div class="p-3 rounded border bg-light-subtle text-center">
                                <div class="avatar avatar-sm mx-auto mb-2">
                                    <span class="avatar-initial rounded-circle bg-label-secondary"><i class="mdi mdi-close-circle-outline mdi-20px"></i></span>
                                </div>
                                <h4 class="fw-bold mb-1 text-dark">{{ number_format($countLoss) }}</h4>
                                <span class="text-muted small fw-semibold">Order Loss</span>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-4">
                            <div class="p-3 rounded border bg-primary-subtle text-center">
                                <div class="avatar avatar-sm mx-auto mb-2">
                                    <span class="avatar-initial rounded-circle bg-primary text-white"><i class="mdi mdi-file-multiple-outline mdi-20px"></i></span>
                                </div>
                                <h4 class="fw-bold mb-1 text-primary">{{ number_format($totalQuotations) }}</h4>
                                <span class="text-primary small fw-semibold">Total Quotations</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Card 2: Quotation Types Breakdown --}}
            <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-header bg-transparent border-bottom py-3 d-flex align-items-center justify-content-between">
                    <h6 class="card-title mb-0 fw-bold text-dark">
                        <i class="mdi mdi-shape-outline me-2 text-primary fs-5"></i>Quotation Type Distribution
                    </h6>
                    <span class="badge bg-label-info rounded-pill">All Offer Categories</span>
                </div>
                <div class="card-body py-3">
                    <div class="row g-3">
                        <div class="col-6 col-sm-4 col-md-3">
                            <div class="d-flex align-items-center p-2 rounded border">
                                <span class="badge bg-primary me-2 px-2 py-1 fs-6">U</span>
                                <div>
                                    <span class="text-muted small d-block">Unit</span>
                                    <span class="fw-bold text-dark">{{ number_format($typeUnit) }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-sm-4 col-md-3">
                            <div class="d-flex align-items-center p-2 rounded border">
                                <span class="badge bg-success me-2 px-2 py-1 fs-6">P</span>
                                <div>
                                    <span class="text-muted small d-block">Parts</span>
                                    <span class="fw-bold text-dark">{{ number_format($typeParts) }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-sm-4 col-md-3">
                            <div class="d-flex align-items-center p-2 rounded border">
                                <span class="badge bg-info me-2 px-2 py-1 fs-6">S</span>
                                <div>
                                    <span class="text-muted small d-block">Service</span>
                                    <span class="fw-bold text-dark">{{ number_format($typeService) }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-sm-4 col-md-3">
                            <div class="d-flex align-items-center p-2 rounded border">
                                <span class="badge bg-warning me-2 px-2 py-1 fs-6">R</span>
                                <div>
                                    <span class="text-muted small d-block">Rental</span>
                                    <span class="fw-bold text-dark">{{ number_format($typeRental) }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-sm-4 col-md-3">
                            <div class="d-flex align-items-center p-2 rounded border">
                                <span class="badge bg-danger me-2 px-2 py-1 fs-6">PR</span>
                                <div>
                                    <span class="text-muted small d-block">Project</span>
                                    <span class="fw-bold text-dark">{{ number_format($typeProject) }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-sm-4 col-md-3">
                            <div class="d-flex align-items-center p-2 rounded border">
                                <span class="badge bg-secondary me-2 px-2 py-1 fs-6">PIP</span>
                                <div>
                                    <span class="text-muted small d-block">Piping</span>
                                    <span class="fw-bold text-dark">{{ number_format($typePiping) }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-sm-4 col-md-3">
                            <div class="d-flex align-items-center p-2 rounded border">
                                <span class="badge bg-dark me-2 px-2 py-1 fs-6">AA</span>
                                <div>
                                    <span class="text-muted small d-block">Air Audit</span>
                                    <span class="fw-bold text-dark">{{ number_format($typeAirAudit) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Add / Edit Payment Template --}}
    <div class="modal fade" id="modal-payment-template" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="form-payment-template">
                    @csrf
                    <input type="hidden" id="pt-id" name="id">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold" id="modal-payment-template-title">Tambah Template Payment</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold" for="pt-name">Nama Template / Label <span class="text-danger">*</span></label>
                            <input type="text" id="pt-name" name="name" class="form-control" placeholder="Contoh: Cash Before Delivery, DP 50% BP 50%" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold" for="pt-client">Khusus Client tertentu (Opsional - Multi Select)</label>
                            <select id="pt-client" name="client_ids[]" class="select2 form-select" multiple="multiple" style="width: 100%;">
                                @foreach($salesClients as $sc)
                                    <option value="{{ $sc->id }}">{{ $sc->company }}</option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">Ketik & pilih 1 atau lebih Client. Jika dikosongkan, berlaku untuk semua Client.</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold" for="pt-term">Detail Format Payment Term <span class="text-danger">*</span></label>
                            <textarea id="pt-term" name="payment_term" class="form-control" rows="3" placeholder="Ketik rincian skema pembayaran..." required></textarea>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="pt-default" name="is_default" value="1">
                            <label class="form-check-label fw-semibold" for="pt-default">Jadikan sebagai Template Default Utama</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary" id="btn-save-payment-template">
                            <i class="mdi mdi-content-save me-1"></i> Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('after-style')
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/select2/select2.css" />
    <style>
        .profile-kpi-card {
            border-radius: 12px !important;
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
            background: #ffffff;
            border: 1px solid rgba(0, 0, 0, 0.05) !important;
        }
        .profile-kpi-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 24px rgba(0, 0, 0, 0.08) !important;
        }
        .tracking-wider {
            letter-spacing: 0.5px;
            font-size: 0.725rem;
        }
    </style>
@endpush

@push('after-script')
    <script src="{{ asset('assets') }}/vendor/libs/select2/select2.js"></script>
    <script>
        $(document).ready(function () {
            var $modal = $('#modal-payment-template');
            var $form = $('#form-payment-template');

            if ($.fn.select2 && $('#pt-client').length) {
                $('#pt-client').select2({
                    dropdownParent: $modal,
                    placeholder: 'Cari & pilih client (bisa lebih dari 1)...',
                    allowClear: true
                });
            }

            $(document).on('click', '#btn-add-payment-template', function () {
                if ($form.length) {
                    $form[0].reset();
                    $('#pt-id').val('');
                    if ($.fn.select2 && $('#pt-client').length) {
                        $('#pt-client').val(null).trigger('change');
                    }
                    $('#modal-payment-template-title').text('Tambah Template Payment');
                }
                $modal.modal('show');
            });

            $(document).on('click', '.btn-edit-template', function () {
                var id = $(this).data('id');
                var name = $(this).data('name');
                var term = $(this).data('term');
                var clients = $(this).data('clients') || [];
                var isDefault = $(this).data('default');

                $('#pt-id').val(id);
                $('#pt-name').val(name);
                $('#pt-term').val(term);
                if ($.fn.select2 && $('#pt-client').length) {
                    $('#pt-client').val(clients).trigger('change');
                }
                $('#pt-default').prop('checked', isDefault == 1);
                $('#modal-payment-template-title').text('Edit Template Payment');
                $modal.modal('show');
            });

            $form.on('submit', function (e) {
                e.preventDefault();
                var id = $('#pt-id').val();
                var url = id ? ('/sales-payment-templates/' + id) : '/sales-payment-templates';
                var method = id ? 'PUT' : 'POST';

                var formData = {
                    _token: '{{ csrf_token() }}',
                    name: $('#pt-name').val(),
                    payment_term: $('#pt-term').val(),
                    client_ids: $('#pt-client').val() || [],
                    is_default: $('#pt-default').is(':checked') ? 1 : 0
                };

                $.ajax({
                    url: url,
                    type: method,
                    data: formData,
                    success: function (res) {
                        $modal.modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: res.message,
                            timer: 1500,
                            showConfirmButton: false
                        }).then(function () {
                            location.reload();
                        });
                    },
                    error: function (xhr) {
                        var errMsg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Gagal menyimpan template.';
                        Swal.fire({ icon: 'error', title: 'Gagal', text: errMsg });
                    }
                });
            });

            $(document).on('click', '.btn-delete-template', function () {
                var id = $(this).data('id');
                Swal.fire({
                    title: 'Hapus Template Payment?',
                    text: 'Template ini akan dihapus permanen.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Hapus',
                    cancelButtonText: 'Batal'
                }).then(function (result) {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '/sales-payment-templates/' + id,
                            type: 'DELETE',
                            data: { _token: '{{ csrf_token() }}' },
                            success: function (res) {
                                Swal.fire({ icon: 'success', title: 'Terhapus', text: res.message, timer: 1500, showConfirmButton: false }).then(function () {
                                    location.reload();
                                });
                            },
                            error: function () {
                                Swal.fire({ icon: 'error', title: 'Gagal', text: 'Gagal menghapus template.' });
                            }
                        });
                    }
                });
            });

            $(document).on('click', '.btn-set-default-template', function () {
                var id = $(this).data('id');
                $.ajax({
                    url: '/sales-payment-templates/' + id + '/set-default',
                    type: 'POST',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function (res) {
                        Swal.fire({ icon: 'success', title: 'Berhasil', text: res.message, timer: 1500, showConfirmButton: false }).then(function () {
                            location.reload();
                        });
                    }
                });
            });
        });
    </script>
@endpush
