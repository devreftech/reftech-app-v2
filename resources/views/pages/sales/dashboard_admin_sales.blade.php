    <style>
        .nav-tabs.nav-tabs-widget .nav-link {
            height: auto !important;
            min-height: 92px;
            padding-top: .5rem !important;
            padding-bottom: .5rem !important;
        }
    </style>
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
            <div class="card clean-card mb-3">
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

                    <div class="d-flex flex-wrap gap-2 mt-3">
                        <a href="{{ route('report.monthly') }}" class="btn btn-xs btn-label-primary rounded-pill px-3 waves-effect fw-semibold">
                            <i class="mdi mdi-chart-line me-1"></i> Report Sales
                        </a>
                    </div>
                </div>
                <img src="{{ asset('assets') }}/img/illustrations/trophy.png"
                    class="position-absolute bottom-0 end-0 me-3" height="140" alt="view sales">
            </div>
            <div class="card clean-card">
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
            <div class="card clean-card h-100 shadow-sm">
                <div class="card-header d-flex align-items-center justify-content-between py-3 border-bottom">
                    <div class="d-flex align-items-center gap-2">
                        <div class="avatar avatar-sm bg-label-primary rounded p-1 d-flex align-items-center justify-content-center">
                            <i class="mdi mdi-finance fs-4"></i>
                        </div>
                        <div>
                            <h5 class="mb-0 fw-bold text-heading" style="font-size: 1.05rem;">Sales Overview</h5>
                            <small class="text-muted" style="font-size: 0.75rem;">Performa Tim, Aktivitas &amp; Pipeline Finansial</small>
                        </div>
                    </div>
                    <span class="badge bg-label-primary rounded-pill px-3 py-1" style="font-size: 0.72rem;">
                        <i class="mdi mdi-calendar-month-outline me-1"></i> {{ \Carbon\Carbon::now()->isoFormat('MMMM Y') }}
                    </span>
                </div>
                <div class="card-body pb-3 pt-3">
                    {{-- Roster Horizontal Slider Tabs --}}
                    <ul class="nav nav-tabs nav-tabs-widget pb-2 gap-2 d-flex flex-nowrap overflow-auto border-0" role="tablist" style="scrollbar-width: thin; scroll-behavior: smooth;">
                        @foreach ($sales as $user)
                            @if ($user->id == 23) @continue @endif
                            @php
                                $isActive = $user->id == ($firstSales->id ?? 1);
                                $roster = $user->currentRoster;
                                $displayName = ($roster && !empty($roster->display_name)) ? $roster->display_name : ($user->id == 16 ? 'Team E-Commerce' : Str::words($user->name, 1, ''));
                                $displayArea = ($roster && !empty($roster->subtitle)) ? $roster->subtitle : ($user->id == 16 ? 'Online' : ($user->latestRole->area ?? 'Sales'));
                            @endphp
                            <li class="nav-item change-sales text-center flex-shrink-0" role="presentation" data-id="{{ $user->id }}">
                                <a class="nav-link btn {{ $isActive ? 'active' : '' }} d-flex flex-column align-items-center justify-content-center p-2 rounded-3 border"
                                   role="tab" data-bs-toggle="tab" data-bs-target="#navs-sales-{{ $user->id }}"
                                   aria-controls="navs-sales-{{ $user->id }}" aria-selected="{{ $isActive ? 'true' : 'false' }}"
                                   style="min-width: 90px; transition: all 0.25s ease;">
                                    <div class="position-relative mb-1">
                                        <img src="{{ url('') . '/' . $user->image }}" alt="{{ $displayName }}"
                                            class="rounded-circle border"
                                            style="width: 44px; height: 44px; object-fit: cover; border-width: 2px !important;">
                                        @if ($user->id == 16)
                                            <span class="position-absolute bottom-0 end-0 badge rounded-pill bg-primary p-1" style="transform: translate(25%, 25%);">
                                                <i class="mdi mdi-cart fs-6 text-white" style="font-size: 10px !important;"></i>
                                            </span>
                                        @endif
                                    </div>
                                    <span class="fw-semibold text-dark text-truncate d-block mt-1" style="max-width: 82px; font-size: 0.76rem; line-height: 1.2;">
                                        {{ $displayName }}
                                    </span>
                                    <small class="text-muted text-truncate d-block" style="max-width: 82px; font-size: 0.68rem; line-height: 1.2;">
                                        {{ $displayArea }}
                                    </small>
                                </a>
                            </li>
                        @endforeach
                        <li class="nav-item change-sales text-center flex-shrink-0" role="presentation" data-id="project">
                            <a class="nav-link btn d-flex flex-column align-items-center justify-content-center p-2 rounded-3 border"
                               role="tab" data-bs-toggle="tab" data-bs-target="#navs-sales-project"
                               aria-controls="navs-sales-project" aria-selected="false"
                               style="min-width: 90px; transition: all 0.25s ease;">
                                <div class="position-relative mb-1">
                                    <div class="rounded-circle border bg-label-primary d-flex align-items-center justify-content-center"
                                        style="width: 44px; height: 44px; border-width: 2px !important;">
                                        <i class="mdi mdi-briefcase-outline fs-4"></i>
                                    </div>
                                </div>
                                <span class="fw-semibold text-dark text-truncate d-block mt-1" style="max-width: 82px; font-size: 0.76rem; line-height: 1.2;">
                                    Sales Project
                                </span>
                                <small class="text-muted text-truncate d-block" style="max-width: 82px; font-size: 0.68rem; line-height: 1.2;">
                                    Admin/SM
                                </small>
                            </a>
                        </li>
                        <li class="nav-item change-sales text-center flex-shrink-0" role="presentation" data-id="marketing">
                            <a class="nav-link btn d-flex flex-column align-items-center justify-content-center p-2 rounded-3 border"
                               role="tab" data-bs-toggle="tab" data-bs-target="#navs-sales-marketing"
                               aria-controls="navs-sales-marketing" aria-selected="false"
                               style="min-width: 90px; transition: all 0.25s ease;">
                                <div class="position-relative mb-1">
                                    <div class="rounded-circle border bg-label-info d-flex align-items-center justify-content-center"
                                        style="width: 44px; height: 44px; border-width: 2px !important;">
                                        <i class="mdi mdi-bullhorn-outline fs-4"></i>
                                    </div>
                                </div>
                                <span class="fw-semibold text-dark text-truncate d-block mt-1" style="max-width: 82px; font-size: 0.76rem; line-height: 1.2;">
                                    Marketing Team
                                </span>
                                <small class="text-muted text-truncate d-block" style="max-width: 82px; font-size: 0.68rem; line-height: 1.2;">
                                    Support
                                </small>
                            </a>
                        </li>
                    </ul>

                    {{-- Tab Contents --}}
                    <div class="tab-content p-0 mt-3">
                        @php
                            $item = 0;
                        @endphp
                        @foreach ($sales as $user)
                            @if ($user->id == 23) @continue @endif
                            @php
                                $titleName = $user->id == 16 ? 'Team E-Commerce' : $user->name;
                            @endphp
                            <div class="tab-pane fade{{ $user->id == ($firstSales->id ?? 1) ? ' show active' : '' }}"
                                id="navs-sales-{{ $user->id }}" role="tabpanel">
                                <div>
                                    <div data-id="{{ $item }}">
                                        <!-- Header Profile Banner -->
                                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 p-3 bg-light rounded-3 border border-dashed mb-3">
                                            <div class="d-flex align-items-center gap-3">
                                                <img src="{{ url('') . '/' . $user->image }}" alt="{{ $titleName }}"
                                                    class="rounded-circle border shadow-xs" style="width: 48px; height: 48px; object-fit: cover; border-width: 2px !important;">
                                                <div>
                                                    <div class="d-flex align-items-center gap-2">
                                                        <h5 class="mb-0 fw-bold text-dark">{{ $titleName }}</h5>
                                                        <span class="badge bg-label-primary rounded-pill px-2 py-1" style="font-size: 0.7rem;">
                                                            {{ $user->id == 16 ? 'E-Commerce' : 'Active Sales' }}
                                                        </span>
                                                    </div>
                                                    <small class="text-muted d-block mt-1">
                                                        <i class="mdi mdi-map-marker-outline me-1"></i>{{ $user->id == 16 ? 'Divisi Online & Marketplace' : ($user->latestRole->area ?? 'Area Penjualan') }}
                                                    </small>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row g-3">
                                            @if ($user->id == 16 || $user->id == 23)
                                                <!-- Panel Kiri: E-Commerce Operations Hub (Grid 2 Kolom) -->
                                                <div class="col-12 col-md-7">
                                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                                        <span class="fw-bold text-muted text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                                                            <i class="mdi mdi-store-outline me-1 text-primary"></i> Operasional E-Commerce
                                                        </span>
                                                        <span class="badge bg-label-info rounded-pill" style="font-size: 0.65rem;">Metrics</span>
                                                    </div>
                                                    <div class="row g-2">
                                                        <!-- Upload Product -->
                                                        <div class="col-6">
                                                            <div class="p-2 border rounded-3 bg-body-tertiary h-100">
                                                                <div class="d-flex align-items-center justify-content-between mb-1">
                                                                    <div class="d-flex align-items-center gap-1">
                                                                        <span class="badge bg-label-info p-1 rounded"><i class="mdi mdi-reproduction"></i></span>
                                                                        <span class="fw-semibold text-dark" style="font-size: 0.75rem;">Upload Product</span>
                                                                    </div>
                                                                    <span class="badge bg-label-info rounded-pill filtered-percent-product" style="font-size: 9px;">0%</span>
                                                                </div>
                                                                <div class="d-flex align-items-baseline gap-1 mt-1">
                                                                    <h6 class="mb-0 fw-bold text-dark filtered-product">0</h6>
                                                                    <small class="text-muted filtered-target-product" style="font-size: 0.7rem;">/ 100</small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!-- Upload Video -->
                                                        <div class="col-6">
                                                            <div class="p-2 border rounded-3 bg-body-tertiary h-100">
                                                                <div class="d-flex align-items-center justify-content-between mb-1">
                                                                    <div class="d-flex align-items-center gap-1">
                                                                        <span class="badge bg-label-secondary p-1 rounded"><i class="mdi mdi-video-outline"></i></span>
                                                                        <span class="fw-semibold text-dark" style="font-size: 0.75rem;">Upload Video</span>
                                                                    </div>
                                                                    <span class="badge bg-label-secondary rounded-pill filtered-percent-video" style="font-size: 9px;">0%</span>
                                                                </div>
                                                                <div class="d-flex align-items-baseline gap-1 mt-1">
                                                                    <h6 class="mb-0 fw-bold text-dark filtered-video">0</h6>
                                                                    <small class="text-muted filtered-target-video" style="font-size: 0.7rem;">/ 100%</small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!-- CRM -->
                                                        <div class="col-6">
                                                            <div class="p-2 border rounded-3 bg-body-tertiary h-100">
                                                                <div class="d-flex align-items-center justify-content-between mb-1">
                                                                    <div class="d-flex align-items-center gap-1">
                                                                        <span class="badge bg-label-primary p-1 rounded"><i class="mdi mdi-account-multiple-outline"></i></span>
                                                                        <span class="fw-semibold text-dark" style="font-size: 0.75rem;">CRM</span>
                                                                    </div>
                                                                    <span class="badge bg-label-primary rounded-pill filtered-percent-crm" style="font-size: 9px;">0%</span>
                                                                </div>
                                                                <div class="d-flex align-items-baseline gap-1 mt-1">
                                                                    <h6 class="mb-0 fw-bold text-dark filtered-crm">{{ $user->id == ($firstSales->id ?? 1) ? $filteredCRM : 0 }}</h6>
                                                                    <small class="text-muted filtered-target-crm" style="font-size: 0.7rem;">/ {{ $targetCrm[$user->id] ?? 0 }}</small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!-- Status Product -->
                                                        <div class="col-6">
                                                            <div class="p-2 border rounded-3 bg-body-tertiary h-100">
                                                                <div class="d-flex align-items-center justify-content-between mb-1">
                                                                    <div class="d-flex align-items-center gap-1">
                                                                        <span class="badge bg-label-warning p-1 rounded"><i class="mdi mdi-package-variant-closed-check"></i></span>
                                                                        <span class="fw-semibold text-dark" style="font-size: 0.75rem;">Status Product</span>
                                                                    </div>
                                                                    <span class="badge bg-label-warning rounded-pill filtered-percent-status" style="font-size: 9px;">0%</span>
                                                                </div>
                                                                <div class="d-flex align-items-baseline gap-1 mt-1">
                                                                    <h6 class="mb-0 fw-bold text-dark filtered-status">0</h6>
                                                                    <small class="text-muted filtered-target-status" style="font-size: 0.7rem;">/ 5.0</small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!-- Delivery Status -->
                                                        <div class="col-6">
                                                            <div class="p-2 border rounded-3 bg-body-tertiary h-100">
                                                                <div class="d-flex align-items-center justify-content-between mb-1">
                                                                    <div class="d-flex align-items-center gap-1">
                                                                        <span class="badge bg-label-info p-1 rounded"><i class="mdi mdi-truck-delivery-outline"></i></span>
                                                                        <span class="fw-semibold text-dark" style="font-size: 0.75rem;">Delivery Status</span>
                                                                    </div>
                                                                    <span class="badge bg-label-info rounded-pill filtered-percent-delivery" style="font-size: 9px;">0%</span>
                                                                </div>
                                                                <div class="d-flex align-items-baseline gap-1 mt-1">
                                                                    <h6 class="mb-0 fw-bold text-dark filtered-delivery">0</h6>
                                                                    <small class="text-muted filtered-target-delivery" style="font-size: 0.7rem;">/ 5.0</small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!-- Chat Response -->
                                                        <div class="col-6">
                                                            <div class="p-2 border rounded-3 bg-body-tertiary h-100">
                                                                <div class="d-flex align-items-center justify-content-between mb-1">
                                                                    <div class="d-flex align-items-center gap-1">
                                                                        <span class="badge bg-label-danger p-1 rounded"><i class="mdi mdi-account-heart-outline"></i></span>
                                                                        <span class="fw-semibold text-dark" style="font-size: 0.75rem;">Chat Response</span>
                                                                    </div>
                                                                    <span class="badge bg-label-danger rounded-pill filtered-percent-response" style="font-size: 9px;">0%</span>
                                                                </div>
                                                                <div class="d-flex align-items-baseline gap-1 mt-1">
                                                                    <h6 class="mb-0 fw-bold text-dark filtered-response">0</h6>
                                                                    <small class="text-muted filtered-target-response" style="font-size: 0.7rem;">/ 100%</small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!-- Store Rating -->
                                                        <div class="col-12">
                                                            <div class="p-2 border rounded-3 bg-body-tertiary">
                                                                <div class="d-flex align-items-center justify-content-between mb-1">
                                                                    <div class="d-flex align-items-center gap-1">
                                                                        <span class="badge bg-label-warning p-1 rounded"><i class="mdi mdi-star"></i></span>
                                                                        <span class="fw-semibold text-dark" style="font-size: 0.75rem;">Store Rating</span>
                                                                    </div>
                                                                    <span class="badge bg-label-warning rounded-pill filtered-percent-rating" style="font-size: 9px;">0%</span>
                                                                </div>
                                                                <div class="d-flex align-items-baseline gap-1 mt-1">
                                                                    <h6 class="mb-0 fw-bold text-dark filtered-rating">0</h6>
                                                                    <small class="text-muted filtered-target-rating" style="font-size: 0.7rem;">/ 5.0</small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @else
                                                <!-- Panel Kiri: Regular Sales Operational Hub (Grid 2 Kolom) -->
                                                <div class="col-12 col-md-7">
                                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                                        <span class="fw-bold text-muted text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                                                            <i class="mdi mdi-clipboard-pulse-outline me-1 text-primary"></i> Aktivitas &amp; Leads
                                                        </span>
                                                        <span class="badge bg-label-primary rounded-pill" style="font-size: 0.65rem;">Operational</span>
                                                    </div>
                                                    <div class="row g-2">
                                                        @if ($user->id == 1 || $user->id == 2 || $user->id == 32)
                                                            @php
                                                                $salesTargetLeads = ($targetSales[$item][0] ?? null)?->leads ?? 0;
                                                                $currentLeads = $user->id == ($firstSales->id ?? 1) ? $filteredLeads : 0;
                                                                $targetLeads = $salesTargetLeads > 0 ? ($currentLeads / $salesTargetLeads) * 100 : 0;
                                                            @endphp
                                                            <!-- New Leads -->
                                                            <div class="col-6">
                                                                <div class="p-2 border rounded-3 bg-body-tertiary h-100">
                                                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                                                        <div class="d-flex align-items-center gap-1">
                                                                            <span class="badge bg-label-secondary p-1 rounded"><i class="mdi mdi-account-multiple-plus-outline"></i></span>
                                                                            <span class="fw-semibold text-dark" style="font-size: 0.75rem;">New Leads</span>
                                                                        </div>
                                                                        <span class="badge bg-label-secondary rounded-pill filtered-percent-leads" style="font-size: 9px;">{{ round($targetLeads) }}%</span>
                                                                    </div>
                                                                    <div class="d-flex align-items-baseline gap-1 mt-1">
                                                                        <h6 class="mb-0 fw-bold text-dark filtered-leads">{{ $user->id == ($firstSales->id ?? 1) ? $filteredLeads : 0 }}</h6>
                                                                        <small class="text-muted filtered-target-leads" style="font-size: 0.7rem;">/ {{ $salesTargetLeads }}</small>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <!-- Daily Call -->
                                                            <div class="col-6">
                                                                <div class="p-2 border rounded-3 bg-body-tertiary h-100">
                                                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                                                        <div class="d-flex align-items-center gap-1">
                                                                            <span class="badge bg-label-info p-1 rounded"><i class="mdi mdi-phone-outline"></i></span>
                                                                            <span class="fw-semibold text-dark" style="font-size: 0.75rem;">Daily Call</span>
                                                                        </div>
                                                                    </div>
                                                                    <div class="d-flex align-items-baseline gap-1 mt-1">
                                                                        <h6 class="mb-0 fw-bold text-dark filtered-dc">{{ $user->id == ($firstSales->id ?? 1) ? $filteredDC : 0 }}</h6>
                                                                        <small class="text-muted" style="font-size: 0.7rem;">Call</small>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endif

                                                        <!-- CRM Tile -->
                                                        @php
                                                            $crmDenominator = $targetCrm[$user->id] ?? 0;
                                                            $currentCRM = $user->id == ($firstSales->id ?? 1) ? $filteredCRM : 0;
                                                            $targetCRM = $crmDenominator > 0 ? ($currentCRM / $crmDenominator) * 100 : 0;
                                                        @endphp
                                                        <div class="col-6">
                                                            <div class="p-2 border rounded-3 bg-body-tertiary h-100">
                                                                <div class="d-flex align-items-center justify-content-between mb-1">
                                                                    <div class="d-flex align-items-center gap-1">
                                                                        <span class="badge bg-label-primary p-1 rounded"><i class="mdi mdi-account-multiple-outline"></i></span>
                                                                        <span class="fw-semibold text-dark" style="font-size: 0.75rem;">CRM</span>
                                                                    </div>
                                                                    @if ($user->id != 3)
                                                                        <span class="badge bg-label-primary rounded-pill filtered-percent-crm" style="font-size: 9px;">{{ round($targetCRM) }}%</span>
                                                                    @endif
                                                                </div>
                                                                <div class="d-flex align-items-baseline gap-1 mt-1">
                                                                    <h6 class="mb-0 fw-bold text-dark filtered-crm">{{ $user->id == ($firstSales->id ?? 1) ? $filteredCRM : 0 }}</h6>
                                                                    <small class="text-muted filtered-target-crm" style="font-size: 0.7rem;">/ {{ $crmDenominator }}</small>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!-- Quotation Tile -->
                                                        <div class="col-6">
                                                            <div class="p-2 border rounded-3 bg-body-tertiary h-100">
                                                                <div class="d-flex align-items-center justify-content-between mb-1">
                                                                    <div class="d-flex align-items-center gap-1">
                                                                        <span class="badge bg-label-warning p-1 rounded"><i class="mdi mdi-email-multiple-outline"></i></span>
                                                                        <span class="fw-semibold text-dark" style="font-size: 0.75rem;">Quotation</span>
                                                                    </div>
                                                                </div>
                                                                <div class="d-flex align-items-baseline gap-1 mt-1">
                                                                    <h6 class="mb-0 fw-bold text-dark filtered-quote">{{ $user->id == ($firstSales->id ?? 1) ? $filteredQuote : 0 }}</h6>
                                                                    <small class="text-muted" style="font-size: 0.7rem;">Dibuat</small>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!-- Prospect Tile -->
                                                        <div class="col-6">
                                                            <div class="p-2 border rounded-3 bg-body-tertiary h-100">
                                                                <div class="d-flex align-items-center justify-content-between mb-1">
                                                                    <div class="d-flex align-items-center gap-1">
                                                                        <span class="badge bg-label-info p-1 rounded"><i class="mdi mdi-target"></i></span>
                                                                        <span class="fw-semibold text-dark" style="font-size: 0.75rem;">Prospect</span>
                                                                    </div>
                                                                </div>
                                                                <div class="d-flex align-items-baseline gap-1 mt-1">
                                                                    <h6 class="mb-0 fw-bold text-dark filtered-prospect-sales">{{ $user->id == ($firstSales->id ?? 1) ? $filteredProspect : 0 }}</h6>
                                                                    <small class="text-muted" style="font-size: 0.7rem;">Lead</small>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!-- Closing Tile (Quotation yang jadi PO) -->
                                                        <div class="col-6">
                                                            <div class="p-2 border rounded-3 bg-body-tertiary h-100 border-success border-opacity-25">
                                                                <div class="d-flex align-items-center justify-content-between mb-1">
                                                                    <div class="d-flex align-items-center gap-1">
                                                                        <span class="badge bg-label-success p-1 rounded"><i class="mdi mdi-check-decagram-outline"></i></span>
                                                                        <span class="fw-semibold text-success" style="font-size: 0.75rem;">Closing PO</span>
                                                                    </div>
                                                                </div>
                                                                <div class="d-flex align-items-baseline gap-1 mt-1">
                                                                    <h6 class="mb-0 fw-bold text-success filtered-po-count">{{ $user->id == ($firstSales->id ?? 1) ? $filteredPO : 0 }}</h6>
                                                                    <small class="text-muted" style="font-size: 0.7rem;">PO Won</small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif

                                            <!-- Panel Kanan: Financial Pipeline Funnel -->
                                            <div class="col-12 col-md-5">
                                                <div class="p-3 border rounded-3 bg-body-tertiary h-100 d-flex flex-column justify-content-between">
                                                    <div>
                                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                                            <span class="fw-bold text-muted text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                                                                <i class="mdi mdi-cash-sync me-1 text-primary"></i> Financial Pipeline
                                                            </span>
                                                            <span class="badge bg-label-primary rounded-pill" style="font-size: 0.65rem;">Funnel</span>
                                                        </div>
                                                        <div class="d-flex flex-column gap-2">
                                                            <!-- 1. Quotation Generated -->
                                                            <div class="d-flex align-items-center justify-content-between p-2 bg-white rounded-3 border shadow-xs">
                                                                <div class="d-flex align-items-center gap-2">
                                                                    <span class="badge bg-label-secondary p-1 rounded"><i class="mdi mdi-cart"></i></span>
                                                                    <small class="fw-semibold text-dark" style="font-size: 0.75rem;">Quotation</small>
                                                                </div>
                                                                <span class="fw-bold text-dark admin-total-quotation" style="font-size: 0.8rem;">
                                                                    Rp {{ $user->id == ($firstSales->id ?? 1) ? number_format($totalQuotation, 0, ',', '.') : 0 }}
                                                                </span>
                                                            </div>
                                                            <!-- 2. Prospect -->
                                                            <div class="d-flex align-items-center justify-content-between p-2 bg-white rounded-3 border shadow-xs">
                                                                <div class="d-flex align-items-center gap-2">
                                                                    <span class="badge bg-label-info p-1 rounded"><i class="mdi mdi-cart-arrow-down"></i></span>
                                                                    <small class="fw-semibold text-dark" style="font-size: 0.75rem;">Prospect</small>
                                                                </div>
                                                                <span class="fw-bold text-dark admin-total-prospect" style="font-size: 0.8rem;">
                                                                    Rp {{ $user->id == ($firstSales->id ?? 1) ? number_format($totalProspect, 0, ',', '.') : 0 }}
                                                                </span>
                                                            </div>
                                                            <!-- 3. Hot Prospect -->
                                                            <div class="d-flex align-items-center justify-content-between p-2 bg-white rounded-3 border shadow-xs">
                                                                <div class="d-flex align-items-center gap-2">
                                                                    <span class="badge bg-label-warning p-1 rounded"><i class="mdi mdi-cart-heart"></i></span>
                                                                    <small class="fw-semibold text-dark" style="font-size: 0.75rem;">Hot Prospect</small>
                                                                </div>
                                                                <span class="fw-bold text-warning admin-total-hot-prospect" style="font-size: 0.8rem;">
                                                                    Rp {{ $user->id == ($firstSales->id ?? 1) ? number_format($totalHotProspect, 0, ',', '.') : 0 }}
                                                                </span>
                                                            </div>
                                                            <!-- 4. PO Received (Closing) -->
                                                            <div class="d-flex align-items-center justify-content-between p-2 bg-white rounded-3 border border-success border-opacity-50 shadow-xs">
                                                                <div class="d-flex align-items-center gap-2">
                                                                    <span class="badge bg-label-success p-1 rounded"><i class="mdi mdi-cart-plus"></i></span>
                                                                    <div>
                                                                        <small class="fw-bold text-success d-block" style="font-size: 0.75rem;">PO Received</small>
                                                                        @php
                                                                            $salesTargetTotal = ($targetSales[$item][0] ?? null)?->total ?? 0;
                                                                            $currentPO = $user->id == ($firstSales->id ?? 1) ? $totalPO : 0;
                                                                            $targetPO = $salesTargetTotal > 0 ? ($currentPO / $salesTargetTotal) * 100 : 0;
                                                                            $color = $targetPO <= 80 ? 'danger' : ($targetPO <= 100 ? 'warning' : 'success');
                                                                        @endphp
                                                                        <span class="badge bg-label-{{ $color }} rounded-pill admin-target-total-po" style="font-size: 8px;">{{ round($targetPO) }}%</span>
                                                                    </div>
                                                                </div>
                                                                <span class="fw-bold text-success admin-total-po" style="font-size: 0.85rem;">
                                                                    Rp {{ $user->id == ($firstSales->id ?? 1) ? number_format($totalPO, 0, ',', '.') : 0 }}
                                                                </span>
                                                            </div>
                                                            <!-- 5. Quotation Loss -->
                                                            <div class="d-flex align-items-center justify-content-between p-2 bg-white rounded-3 border shadow-xs">
                                                                <div class="d-flex align-items-center gap-2">
                                                                    <span class="badge bg-label-danger p-1 rounded"><i class="mdi mdi-cart-minus"></i></span>
                                                                    <small class="fw-semibold text-muted" style="font-size: 0.75rem;">Loss</small>
                                                                </div>
                                                                <span class="fw-semibold text-danger admin-total-loss" style="font-size: 0.8rem;">
                                                                    Rp {{ $user->id == ($firstSales->id ?? 1) ? number_format($totalLoss, 0, ',', '.') : 0 }}
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Modern Action Buttons Toolbar -->
                                            <div class="col-12 mt-2 pt-2 border-top">
                                                @php
                                                    $month = date('m');
                                                    $year = date('Y');
                                                    $dateNow = $month . '-' . $year;
                                                @endphp
                                                <div class="row g-2">
                                                    <div class="col-3">
                                                        <a class="btn btn-label-warning btn-sm d-grid w-100"
                                                            type="button" data-bs-toggle="modal"
                                                            data-bs-target="#overview-sales-{{ $user->id }}">
                                                            <i class="mdi mdi-information-outline me-1"></i> Info
                                                        </a>
                                                    </div>
                                                    <div class="col-4">
                                                        <a class="btn btn-label-primary btn-sm d-grid w-100"
                                                            href="{{ route('detail-overview.semester', ['sales' => $user->id, 'date' => $dateNow]) }}">
                                                            <i class="mdi mdi-eye-outline me-1"></i> Detail Overview
                                                        </a>
                                                    </div>
                                                    <div class="col-5">
                                                        <a class="btn btn-label-secondary btn-sm d-grid w-100"
                                                            href="{{ route('overview.semester', $user->id) }}">
                                                            <i class="mdi mdi-chart-box-outline me-1"></i> Semester Overview
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @php
                                    $item++;
                                @endphp
                            </div>
                        @endforeach

                        {{-- Sales Project Tab Pane --}}
                        <div class="tab-pane fade" id="navs-sales-project" role="tabpanel">
                            <div>
                                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 p-3 bg-light rounded-3 border border-dashed mb-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="rounded-circle border bg-label-primary d-flex align-items-center justify-content-center shadow-xs"
                                            style="width: 48px; height: 48px; border-width: 2px !important;">
                                            <i class="mdi mdi-briefcase-outline fs-4"></i>
                                        </div>
                                        <div>
                                            <div class="d-flex align-items-center gap-2">
                                                <h5 class="mb-0 fw-bold text-dark">Sales Project</h5>
                                                <span class="badge bg-label-primary rounded-pill px-2 py-1" style="font-size: 0.7rem;">
                                                    Project Division
                                                </span>
                                            </div>
                                            <small class="text-muted d-block mt-1">Quotation gabungan yang diterbitkan Admin &amp; Sales Manager</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="row g-3">
                                    <div class="col-12 col-md-6">
                                        <div class="p-3 border rounded-3 bg-body-tertiary h-100">
                                            <div class="d-flex align-items-center gap-2 mb-2">
                                                <span class="badge bg-label-info p-1 rounded"><i class="mdi mdi-email-multiple-outline"></i></span>
                                                <span class="fw-semibold text-dark" style="font-size: 0.85rem;">Quotation Diterbitkan</span>
                                            </div>
                                            <h3 class="mb-0 fw-bold text-dark">{{ $projectQuoteCount }}</h3>
                                            <small class="text-muted">Total quotation project bulan ini</small>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="p-3 border rounded-3 bg-body-tertiary h-100 border-success border-opacity-25">
                                            <div class="d-flex align-items-center gap-2 mb-2">
                                                <span class="badge bg-label-success p-1 rounded"><i class="mdi mdi-cash-multiple"></i></span>
                                                <span class="fw-semibold text-dark" style="font-size: 0.85rem;">Total Nilai Nominal</span>
                                            </div>
                                            <h3 class="mb-0 fw-bold text-success">Rp {{ number_format($projectQuoteNominal, 0, ',', '.') }}</h3>
                                            <small class="text-muted">Total akumulasi nilai quotation bulan ini</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Marketing Team Tab Pane --}}
                        <div class="tab-pane fade" id="navs-sales-marketing" role="tabpanel">
                            <div>
                                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 p-3 bg-light rounded-3 border border-dashed mb-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="rounded-circle border bg-label-info d-flex align-items-center justify-content-center shadow-xs"
                                            style="width: 48px; height: 48px; border-width: 2px !important;">
                                            <i class="mdi mdi-bullhorn-outline fs-4"></i>
                                        </div>
                                        <div>
                                            <div class="d-flex align-items-center gap-2">
                                                <h5 class="mb-0 fw-bold text-dark">Marketing Team</h5>
                                                <span class="badge bg-label-info rounded-pill px-2 py-1" style="font-size: 0.7rem;">
                                                    Support &amp; Marketing
                                                </span>
                                            </div>
                                            <small class="text-muted d-block mt-1">Performa gabungan seluruh akun Technical Support &amp; Marketing</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="row g-3">
                                    <div class="col-12 col-md-6">
                                        <div class="p-3 border rounded-3 bg-body-tertiary h-100">
                                            <div class="d-flex align-items-center justify-content-between mb-2">
                                                <span class="fw-bold text-muted text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                                                    <i class="mdi mdi-clipboard-list-outline me-1 text-primary"></i> Activity Breakdown
                                                </span>
                                                <span class="badge bg-label-info rounded-pill" style="font-size: 0.65rem;">Activities</span>
                                            </div>
                                            <div class="d-flex flex-column gap-2">
                                                <div class="d-flex align-items-center justify-content-between p-2 bg-white rounded-3 border shadow-xs">
                                                    <small class="text-dark fw-semibold">Prospect</small>
                                                    <span class="fw-bold">{{ $marketingAgg['prospect'] ?? 0 }}</span>
                                                </div>
                                                <div class="d-flex align-items-center justify-content-between p-2 bg-white rounded-3 border shadow-xs">
                                                    <small class="text-dark fw-semibold">Provided</small>
                                                    <span class="fw-bold">{{ $marketingAgg['provided'] ?? 0 }}</span>
                                                </div>
                                                <div class="d-flex align-items-center justify-content-between p-2 bg-white rounded-3 border shadow-xs">
                                                    <small class="text-dark fw-semibold">Quotation</small>
                                                    <span class="fw-bold">{{ $marketingAgg['quoteCount'] ?? 0 }}</span>
                                                </div>
                                                <div class="d-flex align-items-center justify-content-between p-2 bg-white rounded-3 border shadow-xs">
                                                    <small class="text-dark fw-semibold">Not Provided</small>
                                                    <span class="fw-bold">{{ $marketingAgg['notProvided'] ?? 0 }}</span>
                                                </div>
                                                <div class="d-flex align-items-center justify-content-between p-2 bg-white rounded-3 border border-success border-opacity-50 shadow-xs">
                                                    <small class="text-success fw-bold">Purchase Order</small>
                                                    <span class="fw-bold text-success">{{ $marketingAgg['poCount'] ?? 0 }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="p-3 border rounded-3 bg-body-tertiary h-100">
                                            <div class="d-flex align-items-center justify-content-between mb-2">
                                                <span class="fw-bold text-muted text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                                                    <i class="mdi mdi-cash-multiple me-1 text-primary"></i> Financial Pipeline
                                                </span>
                                                <span class="badge bg-label-primary rounded-pill" style="font-size: 0.65rem;">Funnel</span>
                                            </div>
                                            <div class="d-flex flex-column gap-2">
                                                <div class="d-flex align-items-center justify-content-between p-2 bg-white rounded-3 border shadow-xs">
                                                    <div class="d-flex align-items-center gap-2">
                                                        <span class="badge bg-label-secondary p-1 rounded"><i class="mdi mdi-cart"></i></span>
                                                        <small class="text-dark fw-semibold">Quotation</small>
                                                    </div>
                                                    <span class="fw-bold text-dark">Rp {{ number_format($marketingAgg['quoteNominal'] ?? 0, 0, ',', '.') }}</span>
                                                </div>
                                                <div class="d-flex align-items-center justify-content-between p-2 bg-white rounded-3 border shadow-xs">
                                                    <div class="d-flex align-items-center gap-2">
                                                        <span class="badge bg-label-warning p-1 rounded"><i class="mdi mdi-cart-heart"></i></span>
                                                        <small class="text-dark fw-semibold">Hot Prospect</small>
                                                    </div>
                                                    <span class="fw-bold text-warning">Rp {{ number_format($marketingAgg['hotProspectNominal'] ?? 0, 0, ',', '.') }}</span>
                                                </div>
                                                <div class="d-flex align-items-center justify-content-between p-2 bg-white rounded-3 border border-success border-opacity-50 shadow-xs">
                                                    <div class="d-flex align-items-center gap-2">
                                                        <span class="badge bg-label-success p-1 rounded"><i class="mdi mdi-cart-plus"></i></span>
                                                        <small class="text-success fw-bold">Purchase Order</small>
                                                    </div>
                                                    <span class="fw-bold text-success">Rp {{ number_format($marketingAgg['poNominal'] ?? 0, 0, ',', '.') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 mt-2 pt-2 border-top">
                                        <div class="row g-2">
                                            <div class="col-6">
                                                <a class="btn btn-label-info btn-sm d-grid w-100"
                                                    href="{{ route('reports.support', ['year' => date('Y'), 'month' => date('m')]) }}">
                                                    <i class="mdi mdi-eye-outline me-1"></i> Detail Laporan Support
                                                </a>
                                            </div>
                                            <div class="col-6">
                                                <a class="btn btn-label-secondary btn-sm d-grid w-100"
                                                    href="{{ route('report.monthly', ['year' => date('Y'), 'month' => date('m')]) }}">
                                                    <i class="mdi mdi-chart-box-outline me-1"></i> Monthly Report
                                                </a>
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
        <div class="col-12">
            <div class="card mb-3">
                <div class="card-datatable table-responsive pt-0">
                    <table class="datatable-prospect-quote table table-bordered">
                        <thead>
                            <tr>
                                <th>Quote No.</th>
                                <th>Company</th>
                                <th>Total Price</th>
                                <th>Type</th>
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
    @php
        $item = 0;
    @endphp
    @foreach ($dataOverview as $overview)
        @include('components.modal.overview')
    @endforeach
