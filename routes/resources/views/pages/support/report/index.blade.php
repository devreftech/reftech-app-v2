@extends('layouts.sales.app')
@section('title', 'Marketing Report')
@section('content')
    @php
        $semesterLabel = $report->semester == 1 ? 'January – June' : 'July – December';
        $s1Report      = $semester->where('year', $report->year)->where('semester', 1)->first();
        $s2Report      = $semester->where('year', $report->year)->where('semester', 2)->first();
    @endphp

    {{-- Header --}}
    <div class="d-flex align-items-start justify-content-between flex-wrap gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-label-primary fs-6 px-3 py-2">
                    <i class="mdi mdi-chart-areaspline me-1"></i> Semester {{ $report->semester }}
                </span>
                <span class="text-muted fw-semibold">{{ $report->year }}</span>
                <span class="text-muted">•</span>
                <small class="text-muted">{{ $semesterLabel }}</small>
            </div>
            <h4 class="fw-bold mb-1 text-heading">Marketing Report</h4>
        </div>

        {{-- Toggle Semester + Pilih Tahun --}}
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <div class="btn-group" role="group">
                @if ($s1Report)
                    <a href="{{ route('reports.support.semester', $s1Report->id) }}"
                       class="btn btn-sm waves-effect {{ $report->semester == 1 ? 'btn-primary' : 'btn-outline-primary' }}">
                        Semester 1
                    </a>
                @endif
                @if ($s2Report)
                    <a href="{{ route('reports.support.semester', $s2Report->id) }}"
                       class="btn btn-sm waves-effect {{ $report->semester == 2 ? 'btn-primary' : 'btn-outline-primary' }}">
                        Semester 2
                    </a>
                @endif
            </div>
            <div class="btn-group">
                <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle waves-effect"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="mdi mdi-calendar me-1"></i> {{ $report->year }}
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    @foreach ($semester->pluck('year')->unique()->sortDesc() as $yr)
                        @php
                            $yrReport = $semester->where('year', $yr)->where('semester', $report->semester)->first()
                                ?? $semester->where('year', $yr)->first();
                        @endphp
                        @if ($yrReport)
                            <li>
                                <a class="dropdown-item waves-effect {{ $yr == $report->year ? 'active' : '' }}"
                                    href="{{ route('reports.support.semester', $yrReport->id) }}">{{ $yr }}</a>
                            </li>
                        @endif
                    @endforeach
                </ul>
            </div>
        </div>
    </div>

    {{-- ===== MARKETING REPORT ===== --}}
    @php
        $smktProspectToQuote = $smktProspectCount > 0 ? round(($smktQuoteCount / $smktProspectCount) * 100, 1) : 0;
        $smktQuoteToPo       = $smktQuoteCount   > 0 ? round(($smktPoCount   / $smktQuoteCount)   * 100, 1) : 0;
        $smktStatusPending   = $smktProspectByStatus->pending    ?? 0;
        $smktStatusProvided  = $smktProspectByStatus->provided   ?? 0;
        $smktStatusNoProvide = $smktProspectByStatus->no_provide ?? 0;
        $smktPctPending      = $smktProspectCount > 0 ? round(($smktStatusPending   / $smktProspectCount) * 100, 1) : 0;
        $smktPctProvided     = $smktProspectCount > 0 ? round(($smktStatusProvided  / $smktProspectCount) * 100, 1) : 0;
        $smktPctNoProvide    = $smktProspectCount > 0 ? round(($smktStatusNoProvide / $smktProspectCount) * 100, 1) : 0;
        $smktSourceIcons = [
            'IG'          => ['icon' => 'mdi-instagram',        'color' => 'danger'],
            'Instagram'   => ['icon' => 'mdi-instagram',        'color' => 'danger'],
            'WhatsApp'    => ['icon' => 'mdi-whatsapp',         'color' => 'success'],
            'LinkedIn'    => ['icon' => 'mdi-linkedin',         'color' => 'info'],
            'Website'     => ['icon' => 'mdi-web',              'color' => 'primary'],
            'Indotrading' => ['icon' => 'mdi-store-outline',    'color' => 'warning'],
            'Tokopedia'   => ['icon' => 'mdi-shopping-outline', 'color' => 'success'],
            'OLX'         => ['icon' => 'mdi-tag-outline',      'color' => 'warning'],
            'Google'      => ['icon' => 'mdi-google',           'color' => 'danger'],
            'Google Ads'  => ['icon' => 'mdi-google',           'color' => 'danger'],
            'Meta Ads'    => ['icon' => 'mdi-facebook',         'color' => 'primary'],
            'Facebook'    => ['icon' => 'mdi-facebook',         'color' => 'primary'],
            'Other'       => ['icon' => 'mdi-dots-horizontal',  'color' => 'secondary'],
        ];
        $smktCategoryIcons = [
            'Service Compressor'   => ['icon' => 'mdi-wrench-outline',          'color' => 'info'],
            'Rental Compressor'    => ['icon' => 'mdi-car-wrench',              'color' => 'warning'],
            'Sparepart Compressor' => ['icon' => 'mdi-cog-outline',             'color' => 'secondary'],
            'Instalasi Piping'     => ['icon' => 'mdi-pipe',                    'color' => 'primary'],
            'Air Audit'            => ['icon' => 'mdi-clipboard-check-outline', 'color' => 'success'],
            'Fire System'          => ['icon' => 'mdi-fire',                    'color' => 'danger'],
            'HVAC System'          => ['icon' => 'mdi-air-conditioner',         'color' => 'info'],
            'Unit Baru/Second'     => ['icon' => 'mdi-package-variant',         'color' => 'primary'],
        ];
    @endphp

    <div class="card mt-4">
        <div class="card-header d-flex align-items-start justify-content-between flex-wrap gap-2">
            <div>
                <h5 class="mb-0">Marketing Report</h5>
                <small class="text-muted">
                    Marketing team contribution —
                    @if ($selectedMonth)
                        {{ \Carbon\Carbon::create($report->year, $selectedMonth)->format('F') }} · {{ $report->year }}
                    @else
                        Semester {{ $report->semester }} · {{ $report->year }}
                    @endif
                    · Funnel: Prospect → Quotation → PO
                </small>
            </div>
            <div class="btn-group">
                <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle waves-effect"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="mdi mdi-calendar-month me-1"></i>
                    {{ $selectedMonth ? \Carbon\Carbon::create($report->year, $selectedMonth)->format('F') : 'Semua Bulan' }}
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item waves-effect {{ !$selectedMonth ? 'active' : '' }}"
                           href="{{ route('reports.support.semester', $report->id) }}">Semua Bulan</a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    @foreach ($semesterMonths as $mn)
                        <li>
                            <a class="dropdown-item waves-effect {{ $selectedMonth == $mn ? 'active' : '' }}"
                               href="{{ route('reports.support.semester', $report->id) }}?month={{ $mn }}">
                                {{ \Carbon\Carbon::create($report->year, $mn)->format('F') }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
        <div class="card-body">

            {{-- Funnel --}}
            <div class="row g-3 align-items-center justify-content-center">
                <div class="col-12 col-md-3">
                    <div class="card border shadow-none bg-label-secondary h-100 text-center">
                        <div class="card-body py-4">
                            <div class="avatar mx-auto mb-3">
                                <div class="avatar-initial bg-secondary rounded">
                                    <i class="mdi mdi-account-search-outline mdi-24px"></i>
                                </div>
                            </div>
                            <h2 class="fw-bold mb-1">{{ $smktProspectCount }}</h2>
                            <p class="mb-0 fw-semibold">Prospect</p>
                            <small class="text-muted">Submitted by marketing this semester</small>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-1 text-center d-flex flex-column align-items-center justify-content-center">
                    <i class="mdi mdi-arrow-right mdi-36px text-muted d-none d-md-block"></i>
                    <i class="mdi mdi-arrow-down mdi-36px text-muted d-block d-md-none"></i>
                    <small class="badge bg-label-primary mt-1">{{ $smktProspectToQuote }}%</small>
                </div>
                <div class="col-12 col-md-3">
                    <div class="card border shadow-none bg-label-primary h-100 text-center">
                        <div class="card-body py-4">
                            <div class="avatar mx-auto mb-3">
                                <div class="avatar-initial bg-primary rounded">
                                    <i class="mdi mdi-file-document-outline mdi-24px"></i>
                                </div>
                            </div>
                            <h2 class="fw-bold mb-1">{{ $smktQuoteCount }}</h2>
                            <p class="mb-0 fw-semibold">Quotation</p>
                            @if ($smktQuoteTotal > 0)
                                <small class="text-muted">Rp {{ number_format($smktQuoteTotal, 0, ',', '.') }}</small>
                            @else
                                <small class="text-muted">—</small>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-1 text-center d-flex flex-column align-items-center justify-content-center">
                    <i class="mdi mdi-arrow-right mdi-36px text-muted d-none d-md-block"></i>
                    <i class="mdi mdi-arrow-down mdi-36px text-muted d-block d-md-none"></i>
                    <small class="badge bg-label-success mt-1">{{ $smktQuoteToPo }}%</small>
                </div>
                <div class="col-12 col-md-3">
                    <div class="card border shadow-none bg-label-success h-100 text-center">
                        <div class="card-body py-4">
                            <div class="avatar mx-auto mb-3">
                                <div class="avatar-initial bg-success rounded">
                                    <i class="mdi mdi-cart-check mdi-24px"></i>
                                </div>
                            </div>
                            <h2 class="fw-bold mb-1">{{ $smktPoCount }}</h2>
                            <p class="mb-0 fw-semibold">Purchase Order</p>
                            @if ($smktPoTotal > 0)
                                <small class="text-muted">Rp {{ number_format($smktPoTotal, 0, ',', '.') }}</small>
                            @else
                                <small class="text-muted">—</small>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Status Prospect --}}
            <hr class="my-4">
            <p class="fw-semibold mb-3 text-heading">
                <i class="mdi mdi-clipboard-list-outline me-1"></i> Prospect Follow-up Status
            </p>
            <div class="row g-3 mb-2">
                <div class="col-12 col-md-4">
                    <div class="d-flex align-items-center gap-3 p-3 rounded border">
                        <div class="avatar flex-shrink-0">
                            <div class="avatar-initial bg-label-warning rounded">
                                <i class="mdi mdi-clock-outline mdi-24px"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between">
                                <span class="fw-semibold">Pending</span>
                                <span class="fw-bold text-warning">{{ $smktStatusPending }}</span>
                            </div>
                            <div class="progress mt-1" style="height:5px">
                                <div class="progress-bar bg-warning" style="width:{{ $smktPctPending }}%"></div>
                            </div>
                            <small class="text-muted">{{ $smktPctPending }}% not yet followed up</small>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="d-flex align-items-center gap-3 p-3 rounded border">
                        <div class="avatar flex-shrink-0">
                            <div class="avatar-initial bg-label-success rounded">
                                <i class="mdi mdi-check-circle-outline mdi-24px"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between">
                                <span class="fw-semibold">Provided</span>
                                <span class="fw-bold text-success">{{ $smktStatusProvided }}</span>
                            </div>
                            <div class="progress mt-1" style="height:5px">
                                <div class="progress-bar bg-success" style="width:{{ $smktPctProvided }}%"></div>
                            </div>
                            <small class="text-muted">{{ $smktPctProvided }}% forwarded to sales</small>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="d-flex align-items-center gap-3 p-3 rounded border">
                        <div class="avatar flex-shrink-0">
                            <div class="avatar-initial bg-label-danger rounded">
                                <i class="mdi mdi-close-circle-outline mdi-24px"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between">
                                <span class="fw-semibold">No Provide</span>
                                <span class="fw-bold text-danger">{{ $smktStatusNoProvide }}</span>
                            </div>
                            <div class="progress mt-1" style="height:5px">
                                <div class="progress-bar bg-danger" style="width:{{ $smktPctNoProvide }}%"></div>
                            </div>
                            <small class="text-muted">{{ $smktPctNoProvide }}% not continued</small>
                        </div>
                    </div>
                </div>
            </div>
            @if ($smktLossCount > 0)
                <div class="alert alert-danger d-flex align-items-center gap-2 py-2 mt-2 mb-0" role="alert">
                    <i class="mdi mdi-alert-outline"></i>
                    <span>
                        <strong>{{ $smktLossCount }} loss quotation(s)</strong> from marketing leads this semester
                        @if ($smktLossTotal > 0)
                            — worth <strong>Rp {{ number_format($smktLossTotal, 0, ',', '.') }}</strong>
                        @endif
                    </span>
                </div>
            @endif

            {{-- Per Marketing Person --}}
            @if ($smktPerPerson->isNotEmpty())
                <hr class="my-4">
                <p class="fw-semibold mb-3 text-heading">
                    <i class="mdi mdi-account-group-outline me-1"></i> Per Marketing Person
                </p>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Marketing</th>
                                <th class="text-center">Total Prospect</th>
                                <th class="text-center">Provided</th>
                                <th class="text-center">Pending</th>
                                <th class="text-center">No Provide</th>
                                <th class="text-end pe-3">Provide Rate</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($smktPerPerson as $p)
                                @php
                                    $smktProvideRate = $p->total > 0 ? round(($p->provided / $p->total) * 100, 1) : 0;
                                    $smktRateColor   = $smktProvideRate >= 70 ? 'success' : ($smktProvideRate >= 40 ? 'warning' : 'danger');
                                @endphp
                                <tr>
                                    <td class="ps-3">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="avatar avatar-sm">
                                                <img src="{{ url('') . '/' . $p->image }}"
                                                     alt="{{ $p->name }}"
                                                     class="rounded-circle"
                                                     style="width:36px;height:36px;object-fit:cover">
                                            </div>
                                            <span class="fw-semibold">{{ $p->name }}</span>
                                        </div>
                                    </td>
                                    <td class="text-center"><span class="badge bg-label-secondary rounded-pill">{{ $p->total }}</span></td>
                                    <td class="text-center"><span class="badge bg-label-success rounded-pill">{{ $p->provided }}</span></td>
                                    <td class="text-center"><span class="badge bg-label-warning rounded-pill">{{ $p->pending }}</span></td>
                                    <td class="text-center"><span class="badge bg-label-danger rounded-pill">{{ $p->no_provide }}</span></td>
                                    <td class="text-end pe-3">
                                        <div class="d-flex align-items-center justify-content-end gap-2">
                                            <div class="progress" style="width:60px;height:6px">
                                                <div class="progress-bar bg-{{ $smktRateColor }}"
                                                     style="width:{{ min($smktProvideRate, 100) }}%"></div>
                                            </div>
                                            <span class="badge bg-label-{{ $smktRateColor }} rounded-pill" style="min-width:48px">
                                                {{ $smktProvideRate }}%
                                            </span>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            {{-- Sumber & Kategori --}}
            @if ($smktProspectBySource->isNotEmpty() || $smktProspectByCategory->isNotEmpty())
                <hr class="my-4">
                <div class="row g-4">
                    {{-- Sumber --}}
                    @if ($smktProspectBySource->isNotEmpty())
                        @php $smktMaxSource = $smktProspectBySource->max('total'); @endphp
                        <div class="col-12 col-lg-6">
                            <div class="accordion" id="accordionSource">
                                <div class="accordion-item border-0">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed fw-semibold px-0" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#collapseSource">
                                            <i class="mdi mdi-antenna me-2"></i> Prospect Source
                                            <span class="badge bg-label-secondary ms-2">{{ $smktProspectBySource->count() }}</span>
                                        </button>
                                    </h2>
                                    <div id="collapseSource" class="accordion-collapse collapse">
                                        <div class="accordion-body px-0 pt-2">
                                            <div class="d-flex flex-column gap-3">
                                                @foreach ($smktProspectBySource as $src)
                                                    @php
                                                        $si  = $smktSourceIcons[$src->source] ?? ['icon' => 'mdi-dots-horizontal', 'color' => 'secondary'];
                                                        $pct = $smktMaxSource > 0 ? round(($src->total / $smktMaxSource) * 100) : 0;
                                                        $ofT = $smktProspectCount > 0 ? round(($src->total / $smktProspectCount) * 100, 1) : 0;
                                                    @endphp
                                                    <div class="d-flex align-items-center gap-3">
                                                        <div class="avatar avatar-sm flex-shrink-0">
                                                            <div class="avatar-initial bg-label-{{ $si['color'] }} rounded">
                                                                <i class="mdi {{ $si['icon'] }}"></i>
                                                            </div>
                                                        </div>
                                                        <div class="flex-grow-1">
                                                            <div class="d-flex justify-content-between mb-1">
                                                                <span class="fw-semibold" style="font-size:0.85rem">{{ $src->source }}</span>
                                                                <span class="text-muted" style="font-size:0.82rem">{{ $src->total }} <small>({{ $ofT }}%)</small></span>
                                                            </div>
                                                            <div class="progress" style="height:6px">
                                                                <div class="progress-bar bg-{{ $si['color'] }}" style="width:{{ $pct }}%"></div>
                                                            </div>
                                                            @if ($src->source === 'Website' && isset($smktWebsiteByDomain) && $smktWebsiteByDomain->isNotEmpty())
                                                                <div class="mt-2 d-flex flex-column gap-1">
                                                                    @foreach ($smktWebsiteByDomain as $dom)
                                                                        @php $domPct = $src->total > 0 ? round(($dom->total / $src->total) * 100, 1) : 0; @endphp
                                                                        <div class="d-flex justify-content-between text-muted ps-2" style="font-size:0.78rem">
                                                                            <span>↳ {{ $dom->domain }}</span>
                                                                            <span>{{ $dom->total }} <small>({{ $domPct }}%)</small></span>
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Kategori --}}
                    @if ($smktProspectByCategory->isNotEmpty())
                        @php $smktMaxCategory = $smktProspectByCategory->max('total'); @endphp
                        <div class="col-12 col-lg-6">
                            <div class="accordion" id="accordionCategory">
                                <div class="accordion-item border-0">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed fw-semibold px-0" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#collapseCategory">
                                            <i class="mdi mdi-tag-multiple-outline me-2"></i> Prospect Category
                                            <span class="badge bg-label-secondary ms-2">{{ $smktProspectByCategory->count() }}</span>
                                        </button>
                                    </h2>
                                    <div id="collapseCategory" class="accordion-collapse collapse">
                                        <div class="accordion-body px-0 pt-2">
                                            <div class="d-flex flex-column gap-3">
                                                @foreach ($smktProspectByCategory as $cat)
                                                    @php
                                                        $ci  = $smktCategoryIcons[$cat->category] ?? ['icon' => 'mdi-shape-outline', 'color' => 'secondary'];
                                                        $pct = $smktMaxCategory > 0 ? round(($cat->total / $smktMaxCategory) * 100) : 0;
                                                        $ofT = $smktProspectCount > 0 ? round(($cat->total / $smktProspectCount) * 100, 1) : 0;
                                                    @endphp
                                                    <div class="d-flex align-items-center gap-3">
                                                        <div class="avatar avatar-sm flex-shrink-0">
                                                            <div class="avatar-initial bg-label-{{ $ci['color'] }} rounded">
                                                                <i class="mdi {{ $ci['icon'] }}"></i>
                                                            </div>
                                                        </div>
                                                        <div class="flex-grow-1">
                                                            <div class="d-flex justify-content-between mb-1">
                                                                <span class="fw-semibold" style="font-size:0.85rem">{{ $cat->category }}</span>
                                                                <span class="text-muted" style="font-size:0.82rem">{{ $cat->total }} <small>({{ $ofT }}%)</small></span>
                                                            </div>
                                                            <div class="progress" style="height:6px">
                                                                <div class="progress-bar bg-{{ $ci['color'] }}" style="width:{{ $pct }}%"></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            @endif

        </div>
    </div>
@endsection
