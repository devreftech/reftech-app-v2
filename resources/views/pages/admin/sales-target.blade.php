@extends('layouts.sales.app')
@section('title', 'Sales Management')

@push('after-style')
<style>
    .year-tab { cursor: pointer; transition: all .15s; }
    .year-tab.active { background: #696cff !important; color: #fff !important; border-color: #696cff !important; }
    .input-annual { min-width: 170px; }
    .col-auto-val { color: #566a7f; font-size: .85rem; white-space: nowrap; text-align: right; display: block; }
    .col-num-header { text-align: right; }
    .trend-up   { color: #71dd37; }
    .trend-down { color: #ff3e1d; }
    .trend-flat { color: #a8aaae; }
    .history-table th { font-size: .78rem; background: #f5f5f9; }
    .history-table td { font-size: .82rem; vertical-align: middle; }
    tfoot.total-row td { background: #f0f0ff; font-weight: 600; }
    .nav-tabs .nav-link { font-weight: 500; font-size: 0.95rem; }
    .nav-tabs .nav-link.active { font-weight: 600; }
    .badge-status-active { background-color: #e8fadf; color: #71dd37; }
    .badge-status-resigned { background-color: #ffe0db; color: #ff3e1d; }
    .badge-status-cuti { background-color: #fff1d6; color: #ffab00; }
    .badge-status-transferred { background-color: #e1f0ff; color: #03c3ec; }
    .kpi-score-circle {
        width: 65px;
        height: 65px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 1.25rem;
    }
    .badge-grade-a { background-color: #e8fadf; color: #71dd37; border: 1px solid #71dd37; }
    .badge-grade-b { background-color: #e1f0ff; color: #03c3ec; border: 1px solid #03c3ec; }
    .badge-grade-c { background-color: #fff1d6; color: #ffab00; border: 1px solid #ffab00; }
    .badge-grade-d { background-color: #ffe0db; color: #ff3e1d; border: 1px solid #ff3e1d; }
</style>
@endpush

@section('content')
<div class="container-fluid flex-grow-1 container-p-y">

    {{-- Header --}}
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1">
                <i class="mdi mdi-account-group-outline me-2 text-primary"></i>Sales Management
            </h4>
            <p class="text-muted mb-0">Kelola target tahunan, seleksi tim sales aktif di dashboard, konfigurasi KPI, dan riwayat perputaran tim (turnover)</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modal-add-year">
                <i class="mdi mdi-calendar-plus me-1"></i>Tambah Tahun
            </button>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4">
            <i class="mdi mdi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Navigation Tabs --}}
    <div class="card mb-4 border-0 shadow-none bg-transparent">
        <ul class="nav nav-tabs nav-fill bg-white border rounded p-1 mb-3" role="tablist" id="salesManagementTabs">
            <li class="nav-item">
                <button class="nav-link active py-3" data-bs-toggle="tab" data-bs-target="#tab-targets" type="button" role="tab">
                    <i class="mdi mdi-bullseye-arrow me-2 fs-5"></i>
                    <strong>Target &amp; Active Roster ({{ $currentYear }})</strong>
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link py-3" data-bs-toggle="tab" data-bs-target="#tab-kpi" type="button" role="tab">
                    <i class="mdi mdi-chart-box-outline me-2 fs-5"></i>
                    <strong>Pengaturan KPI Sales ({{ $currentYear }})</strong>
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link py-3" data-bs-toggle="tab" data-bs-target="#tab-history" type="button" role="tab">
                    <i class="mdi mdi-history me-2 fs-5"></i>
                    <strong>Riwayat Tim &amp; Turnover (All Time)</strong>
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link py-3" data-bs-toggle="tab" data-bs-target="#tab-kpi-ecommerce" type="button" role="tab" id="salesManagementKpiEcommerceTabBtn">
                    <i class="mdi mdi-shopping-outline me-2 fs-5"></i>
                    <strong>KPI E-Commerce</strong>
                </button>
            </li>
        </ul>
    </div>

    <div class="tab-content p-0 border-0 bg-transparent">

        {{-- ========================================================================= --}}
        {{-- ── TAB 1: TARGET & ACTIVE ROSTER ──────────────────────────────────────── --}}
        {{-- ========================================================================= --}}
        <div class="tab-pane fade show active" id="tab-targets" role="tabpanel">

            {{-- Year Selector Tabs --}}
            <div class="d-flex flex-wrap gap-2 mb-4">
                @foreach ($years as $y)
                    @php
                        $growth      = $yearGrowth[$y] ?? null;
                        $isActive    = $y == $currentYear;
                        $growthColor = $growth === null ? '' : ($growth >= 0 ? 'text-success' : 'text-danger');
                        $growthIcon  = $growth === null ? '' : ($growth > 0 ? '↑' : ($growth < 0 ? '↓' : '→'));
                    @endphp
                    <a href="{{ route('sales-target.index', ['year' => $y]) }}"
                       class="btn btn-sm btn-outline-secondary year-tab {{ $isActive ? 'active' : '' }}"
                       style="line-height:1.2; padding-top:6px; padding-bottom:6px;">
                        <div>{{ $y }}</div>
                        @if ($growth !== null)
                            <div class="small {{ $isActive ? 'text-white opacity-75' : $growthColor }}" style="font-size:.7rem">
                                {{ $growthIcon }} {{ $growth > 0 ? '+' : '' }}{{ $growth }}%
                            </div>
                        @else
                            <div class="small text-muted" style="font-size:.7rem">—</div>
                        @endif
                    </a>
                @endforeach
            </div>

            {{-- ── Target Agregat (tanpa breakdown per-sales) ── --}}
            @php
                $s1 = $semesterRecords['1'] ?? null;
                $s2 = $semesterRecords['2'] ?? null;
                $existingAnnualAggregate = (($s1->target ?? 0) + ($s2->target ?? 0));
                $hasPerSalesHistory = $yearTargets->isNotEmpty();
            @endphp

            <div class="card mb-4 border shadow-sm">
                <div class="card-header pb-2">
                    <h5 class="card-title mb-0">
                        Target Agregat Tim — {{ $currentYear }}
                        <span class="badge bg-label-secondary ms-2 fw-normal" style="font-size:.75rem">tanpa breakdown per-sales</span>
                    </h5>
                    <small class="text-muted">
                        Gunakan ini untuk tahun historis (2024, 2025) atau jika target tim tidak perlu dipecah per individu.
                        @if ($hasPerSalesHistory)
                            <span class="text-warning ms-2"><i class="mdi mdi-alert-outline"></i> Tahun ini sudah ada target per-sales — menyimpan agregat akan menimpa nilai semester.</span>
                        @endif
                    </small>
                </div>
                <div class="card-body">
                    <form action="{{ route('sales-target.save-aggregate', $currentYear) }}" method="POST" id="form-aggregate">
                        @csrf
                        <div class="row g-3 align-items-end">
                            <div class="col-12 col-md-4">
                                <label class="form-label fw-semibold">Target / Tahun <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" class="form-control" id="agg-display"
                                        value="{{ $existingAnnualAggregate > 0 ? number_format($existingAnnualAggregate, 0, ',', '.') : '' }}"
                                        placeholder="20.700.000.000" autocomplete="off">
                                    <input type="hidden" name="target_annual" id="agg-hidden"
                                        value="{{ $existingAnnualAggregate > 0 ? $existingAnnualAggregate : '' }}">
                                </div>
                            </div>
                            <div class="col-6 col-md-2">
                                <label class="form-label text-muted small">Semester 1 (÷2)</label>
                                <div class="fw-semibold text-end" id="agg-s1">
                                    {{ $existingAnnualAggregate > 0 ? 'Rp '.number_format(intval($existingAnnualAggregate/2),0,',','.') : '—' }}
                                </div>
                            </div>
                            <div class="col-6 col-md-2">
                                <label class="form-label text-muted small">Semester 2 (÷2)</label>
                                <div class="fw-semibold text-end" id="agg-s2">
                                    {{ $existingAnnualAggregate > 0 ? 'Rp '.number_format(intval($existingAnnualAggregate/2),0,',','.') : '—' }}
                                </div>
                            </div>
                            <div class="col-6 col-md-2">
                                <label class="form-label text-muted small">/ Bulan (÷12)</label>
                                <div class="text-muted text-end" id="agg-monthly">
                                    {{ $existingAnnualAggregate > 0 ? 'Rp '.number_format(intval($existingAnnualAggregate/12),0,',','.') : '—' }}
                                </div>
                            </div>
                            <div class="col-6 col-md-2 d-flex justify-content-end align-items-end">
                                <button type="submit" class="btn btn-outline-primary w-100">
                                    <i class="mdi mdi-content-save-outline me-1"></i>Simpan Agregat
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Target & Active Roster Form --}}
            <form action="{{ route('sales-target.save-year', $currentYear) }}" method="POST" id="form-targets">
                @csrf

                <div class="card border shadow-sm">
                    <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <div>
                            <h5 class="card-title mb-0">Active Roster &amp; Target Tim — {{ $currentYear }}</h5>
                            <small class="text-muted">Centang checkbox <strong>Aktif di Dashboard</strong> untuk sales yang dimunculkan pada card Sales Overview dashboard admin.</small>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modal-add-sales">
                                <i class="mdi mdi-account-plus me-1"></i>Tambah Sales
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="mdi mdi-content-save-outline me-1"></i>Simpan Target &amp; Roster {{ $currentYear }}
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table align-middle mb-0" id="target-table">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4 text-center" style="width: 70px" title="Centang jika sales ini aktif ditampilkan di Dashboard">
                                        <div class="form-check d-flex justify-content-center m-0">
                                            <span class="small fw-semibold">Aktif</span>
                                        </div>
                                    </th>
                                    <th style="min-width:180px">Nama Sales</th>
                                    <th style="min-width:140px">Tipe Sales</th>
                                    <th style="min-width:140px">
                                        Subtitle / Area
                                        <span class="text-muted fw-normal small">(Dashboard)</span>
                                    </th>
                                    <th style="min-width:180px">
                                        Target / Tahun
                                        <span class="text-muted fw-normal small">(input)</span>
                                    </th>
                                    <th class="text-end" style="min-width:130px">Semester 1 <span class="text-muted fw-normal small">(÷2)</span></th>
                                    <th class="text-end" style="min-width:130px">Semester 2 <span class="text-muted fw-normal small">(÷2)</span></th>
                                    <th class="text-end" style="min-width:120px">/ Bulan <span class="text-muted fw-normal small">(÷12)</span></th>
                                    <th class="text-end" style="min-width:85px">% Kontribusi</th>
                                    <th class="pe-4 text-center" style="min-width:60px">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($salesUsers as $user)
                                    @php
                                        $record = $yearRecords[$user->id] ?? null;
                                        $savedAnnual = $record?->target_annual ?? ($yearTargets[$user->id] ?? 0);
                                        $isRosterActive = $record ? (bool)$record->is_active_roster : ($user->active == '1');
                                        $salesType = $record?->sales_type ?? (in_array($user->id, [16, 23]) ? 'ecommerce' : ($user->id == 4 ? 'crm' : 'field'));
                                        $subtitle = $record?->subtitle ?? ($salesType == 'ecommerce' ? 'Online' : ($user->latestRole->area ?? ''));
                                    @endphp
                                    <tr data-user-id="{{ $user->id }}" class="{{ !$isRosterActive ? 'opacity-75 bg-light' : '' }}">
                                        <td class="ps-4 text-center">
                                            <div class="form-check form-switch d-flex justify-content-center m-0">
                                                <input class="form-check-input roster-toggle" type="checkbox"
                                                    name="is_roster[{{ $user->id }}]" value="1"
                                                    id="roster-{{ $user->id }}" {{ $isRosterActive ? 'checked' : '' }}
                                                    data-user-id="{{ $user->id }}">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                @if ($user->image)
                                                    <img src="{{ url('') . '/' . $user->image }}"
                                                        class="rounded-circle" width="34" height="34"
                                                        style="object-fit:cover" alt="{{ $user->name }}">
                                                @else
                                                    <span class="avatar-initial rounded-circle bg-label-primary d-flex align-items-center justify-content-center fw-bold"
                                                        style="width:34px;height:34px;font-size:13px;flex-shrink:0">
                                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                                    </span>
                                                @endif
                                                <div>
                                                    <span class="fw-semibold d-block" style="font-size:.88rem">{{ $user->name }}</span>
                                                    <small class="text-muted">
                                                        @if ($user->active == '1')
                                                            <span class="badge bg-label-success py-0" style="font-size: 0.65rem;">User Aktif</span>
                                                        @else
                                                            <span class="badge bg-label-danger py-0" style="font-size: 0.65rem;">Nonaktif / Resigned</span>
                                                        @endif
                                                    </small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <select name="sales_type[{{ $user->id }}]" class="form-select form-select-sm sales-type-select">
                                                <option value="field" {{ $salesType == 'field' ? 'selected' : '' }}>🎯 Direct / Field</option>
                                                <option value="crm" {{ $salesType == 'crm' ? 'selected' : '' }}>🔄 CRM / Retention</option>
                                                <option value="ecommerce" {{ $salesType == 'ecommerce' ? 'selected' : '' }}>🌐 E-Commerce</option>
                                            </select>
                                        </td>
                                        <td>
                                            <input type="text" name="subtitles[{{ $user->id }}]" class="form-control form-control-sm"
                                                value="{{ $subtitle }}" placeholder="mis. Bekasi / Online">
                                        </td>
                                        <td>
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text">Rp</span>
                                                <input type="text" class="form-control input-annual"
                                                    value="{{ $savedAnnual > 0 ? number_format($savedAnnual, 0, ',', '.') : '' }}"
                                                    placeholder="0" autocomplete="off">
                                                <input type="hidden" name="targets[{{ $user->id }}]"
                                                    class="input-annual-hidden" value="{{ $savedAnnual }}">
                                            </div>
                                        </td>
                                        <td class="text-end"><span class="col-auto-val col-s1">{{ $savedAnnual > 0 ? 'Rp '.number_format(intval($savedAnnual/2),0,',','.') : '—' }}</span></td>
                                        <td class="text-end"><span class="col-auto-val col-s2">{{ $savedAnnual > 0 ? 'Rp '.number_format(intval($savedAnnual/2),0,',','.') : '—' }}</span></td>
                                        <td class="text-end"><span class="col-auto-val col-monthly">{{ $savedAnnual > 0 ? 'Rp '.number_format(intval($savedAnnual/12),0,',','.') : '—' }}</span></td>
                                        <td class="text-end"><span class="col-auto-val col-pct fw-semibold">
                                            {{ ($teamTargetThisYear > 0 && $savedAnnual > 0) ? number_format($savedAnnual / $teamTargetThisYear * 100, 1).'%' : '—' }}
                                        </span></td>
                                        <td class="pe-4 text-center">
                                            <div class="d-inline-flex gap-1">
                                                <button type="button" class="btn btn-sm btn-icon btn-outline-primary btn-edit-sales"
                                                    data-user-id="{{ $user->id }}"
                                                    data-name="{{ $user->name }}"
                                                    data-email="{{ $user->email }}"
                                                    data-phone="{{ $user->phone ?? '' }}"
                                                    data-subtitle="{{ $subtitle }}"
                                                    data-sales-type="{{ $salesType }}"
                                                    data-annual="{{ $savedAnnual }}"
                                                    data-is-roster="{{ $isRosterActive ? '1' : '0' }}"
                                                    data-status="{{ $record?->status ?? ($user->active == '1' ? 'active' : 'resigned') }}"
                                                    data-join-date="{{ $record?->join_date ? $record->join_date->format('Y-m-d') : ($user->date_in ? \Carbon\Carbon::parse($user->date_in)->format('Y-m-d') : '') }}"
                                                    data-resign-date="{{ $record?->resign_date ? $record->resign_date->format('Y-m-d') : '' }}"
                                                    data-notes="{{ $record?->notes ?? '' }}"
                                                    data-update-url="{{ route('sales-target.update-sales', ['year' => $currentYear, 'userId' => $user->id]) }}"
                                                    title="Edit Data Sales &amp; Target">
                                                    <i class="mdi mdi-pencil-outline"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-icon btn-outline-secondary btn-history"
                                                    data-user-id="{{ $user->id }}" data-user-name="{{ $user->name }}" title="Lihat Histori Target">
                                                    <i class="mdi mdi-history"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-icon btn-outline-danger btn-remove-sales"
                                                    data-user-id="{{ $user->id }}"
                                                    data-user-name="{{ $user->name }}"
                                                    data-remove-url="{{ route('sales-target.remove-sales', ['year' => $currentYear, 'userId' => $user->id]) }}"
                                                    title="Hapus Sales dari Roster {{ $currentYear }}">
                                                    <i class="mdi mdi-trash-can-outline"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center text-muted py-4">
                                            <i class="mdi mdi-account-off-outline d-block fs-3 mb-1 text-secondary"></i>
                                            Belum ada sales di dalam roster tahun {{ $currentYear }}. Klik tombol <strong>"+ Tambah Sales ke Roster"</strong> untuk menambahkan.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot class="total-row">
                                <tr>
                                    <td></td>
                                    <td class="ps-4" colspan="2"><strong>Total Tim ({{ $currentYear }})</strong></td>
                                    <td>
                                        <span class="text-primary fw-bold" id="total-annual">
                                            {{ $teamTargetThisYear > 0 ? 'Rp '.number_format($teamTargetThisYear,0,',','.') : '—' }}
                                        </span>
                                    </td>
                                    <td class="text-end"><span id="total-s1" class="col-auto-val">{{ $teamTargetThisYear > 0 ? 'Rp '.number_format(intval($teamTargetThisYear/2),0,',','.') : '—' }}</span></td>
                                    <td class="text-end"><span id="total-s2" class="col-auto-val">{{ $teamTargetThisYear > 0 ? 'Rp '.number_format(intval($teamTargetThisYear/2),0,',','.') : '—' }}</span></td>
                                    <td class="text-end"><span id="total-monthly" class="col-auto-val">{{ $teamTargetThisYear > 0 ? 'Rp '.number_format(intval($teamTargetThisYear/12),0,',','.') : '—' }}</span></td>
                                    <td class="text-end text-muted small">100%</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </form>
        </div>

        {{-- ========================================================================= --}}
        {{-- ── TAB 2: PENGATURAN KPI SALES ────────────────────────────────────────── --}}
        {{-- ========================================================================= --}}
        <div class="tab-pane fade" id="tab-kpi" role="tabpanel">
            <div class="card border shadow-sm">
                <div class="card-header pb-2 d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <h5 class="card-title mb-0">Konfigurasi &amp; Penyesuaian KPI — Tahun {{ $currentYear }}</h5>
                        <small class="text-muted">Atur metrik KPI individual. Kolom yang dibiarkan kosong / dimatikan akan bernilai <em>Null / Not Applicable</em> dan tidak memicu error.</small>
                    </div>
                    <button type="submit" form="form-kpi" class="btn btn-primary">
                        <i class="mdi mdi-content-save-outline me-1"></i>Simpan Pengaturan KPI
                    </button>
                </div>

                <div class="card-body p-0">
                    <form action="{{ route('sales-target.save-kpi', $currentYear) }}" method="POST" id="form-kpi">
                        @csrf
                        <div class="table-responsive">
                            <table class="table align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-4" style="min-width: 180px">Nama Sales</th>
                                        <th style="min-width: 140px">Tipe Sales</th>
                                        <th class="text-center" style="min-width: 130px">
                                            Akusisi New Leads?
                                            <span class="d-block small text-muted font-normal">(Section KPI)</span>
                                        </th>
                                        <th style="min-width: 130px">Target Call / Bln</th>
                                        <th style="min-width: 130px">Target Visit / Bln</th>
                                        <th style="min-width: 130px">Target Leads / Bln</th>
                                        <th class="pe-4" style="min-width: 200px">Catatan Khusus KPI</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($salesUsers as $user)
                                        @php
                                            $rec = $yearRecords[$user->id] ?? null;
                                            $kpi = $rec?->kpi_config ?? [];
                                            $hasLeads = $kpi['has_new_leads'] ?? ($user->id != 4);
                                        @endphp
                                        <tr>
                                            <td class="ps-4">
                                                <div class="fw-semibold">{{ $user->name }}</div>
                                                <small class="text-muted">{{ $user->email }}</small>
                                            </td>
                                            <td>
                                                <span class="badge bg-label-info">
                                                    {{ ucfirst($rec?->sales_type ?? (in_array($user->id, [16, 23]) ? 'ecommerce' : ($user->id == 4 ? 'crm' : 'field'))) }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <div class="form-check form-switch d-flex justify-content-center">
                                                    <input class="form-check-input" type="checkbox"
                                                        name="kpi[{{ $user->id }}][has_new_leads]" value="1"
                                                        id="kpi-leads-{{ $user->id }}" {{ $hasLeads ? 'checked' : '' }}>
                                                </div>
                                            </td>
                                            <td>
                                                <input type="number" class="form-control form-control-sm"
                                                    name="kpi[{{ $user->id }}][target_calls]"
                                                    value="{{ $kpi['target_calls'] ?? '' }}"
                                                    placeholder="Opsional (null)">
                                            </td>
                                            <td>
                                                <input type="number" class="form-control form-control-sm"
                                                    name="kpi[{{ $user->id }}][target_visits]"
                                                    value="{{ $kpi['target_visits'] ?? '' }}"
                                                    placeholder="Opsional (null)">
                                            </td>
                                            <td>
                                                <input type="number" class="form-control form-control-sm"
                                                    name="kpi[{{ $user->id }}][target_leads]"
                                                    value="{{ $kpi['target_leads'] ?? '' }}"
                                                    placeholder="Opsional (null)">
                                            </td>
                                            <td class="pe-4">
                                                <input type="text" class="form-control form-control-sm"
                                                    name="kpi[{{ $user->id }}][notes]"
                                                    value="{{ $kpi['notes'] ?? ($rec?->notes ?? '') }}"
                                                    placeholder="Contoh: Fokus CRM Cipacing & Dwipapuri">
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center text-muted py-4">
                                                Belum ada sales dalam roster tahun {{ $currentYear }}.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- ========================================================================= --}}
        {{-- ── TAB 3: RIWAYAT TIM & TURNOVER ──────────────────────────────────────── --}}
        {{-- ========================================================================= --}}
        <div class="tab-pane fade" id="tab-history" role="tabpanel">
            <div class="card border shadow-sm">
                <div class="card-header pb-2">
                    <h5 class="card-title mb-0">Riwayat Lifecycle &amp; Turnover Tim Sales</h5>
                    <small class="text-muted">Catatan status kepegawaian (Aktif, Resigned, Cuti, Mutasi), tanggal bergabung/keluar, dan histori pencapaian target tahun ke tahun.</small>
                </div>

                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Nama Sales</th>
                                <th>Status Kepegawaian</th>
                                <th>Tanggal Masuk</th>
                                <th>Tanggal Keluar</th>
                                <th>Masa Kerja / Tenure</th>
                                <th>Histori Target Tahunan</th>
                                <th class="pe-4 text-center">Kelola Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($allSalesUsers as $user)
                                @php
                                    $userHistories = $allHistories[$user->id] ?? collect();
                                    $latestHist = $userHistories->sortByDesc('year')->first();
                                    $status = $latestHist?->status ?? ($user->active == '1' ? 'active' : 'resigned');
                                    $joinDate = $latestHist?->join_date ?? ($user->date_in ? \Carbon\Carbon::parse($user->date_in) : null);
                                    $resignDate = $latestHist?->resign_date ?? null;

                                    $tenure = '—';
                                    if ($joinDate) {
                                        $endDate = $resignDate ?? \Carbon\Carbon::now();
                                        $years = $joinDate->diffInYears($endDate);
                                        $months = $joinDate->copy()->addYears($years)->diffInMonths($endDate);
                                        $tenure = "{$years} thn {$months} bln";
                                    }
                                @endphp
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center gap-2">
                                            @if ($user->image)
                                                <img src="{{ url('') . '/' . $user->image }}"
                                                    class="rounded-circle" width="34" height="34"
                                                    style="object-fit:cover" alt="{{ $user->name }}">
                                            @else
                                                <span class="avatar-initial rounded-circle bg-label-secondary d-flex align-items-center justify-content-center fw-bold"
                                                    style="width:34px;height:34px;font-size:13px;flex-shrink:0">
                                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                                </span>
                                            @endif
                                            <div>
                                                <div class="fw-semibold">{{ $user->name }}</div>
                                                <small class="text-muted">{{ $user->email }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if ($status === 'active')
                                            <span class="badge badge-status-active">
                                                <i class="mdi mdi-check-circle-outline me-1"></i>Aktif
                                            </span>
                                        @elseif ($status === 'resigned')
                                            <span class="badge badge-status-resigned">
                                                <i class="mdi mdi-account-remove-outline me-1"></i>Resigned / Keluar
                                            </span>
                                        @elseif ($status === 'cuti')
                                            <span class="badge badge-status-cuti">
                                                <i class="mdi mdi-pause-circle-outline me-1"></i>Cuti
                                            </span>
                                        @else
                                            <span class="badge badge-status-transferred">
                                                <i class="mdi mdi-swap-horizontal me-1"></i>Mutasi
                                            </span>
                                        @endif
                                    </td>
                                    <td>{{ $joinDate ? $joinDate->format('d M Y') : '—' }}</td>
                                    <td>{{ $resignDate ? $resignDate->format('d M Y') : '—' }}</td>
                                    <td><span class="badge bg-label-secondary">{{ $tenure }}</span></td>
                                    <td>
                                        <div class="d-flex flex-wrap gap-1">
                                            @forelse ($userHistories as $h)
                                                @if ($h->target_annual > 0)
                                                    <span class="badge bg-label-primary" title="Target {{ $h->year }}: Rp {{ number_format($h->target_annual, 0, ',', '.') }}">
                                                        {{ $h->year }}: Rp {{ number_format($h->target_annual / 1000000000, 1) }}M
                                                    </span>
                                                @endif
                                            @empty
                                                <span class="text-muted small">—</span>
                                            @endforelse
                                        </div>
                                    </td>
                                    <td class="pe-4 text-center">
                                        <button type="button" class="btn btn-sm btn-outline-primary btn-edit-employee"
                                            data-user-id="{{ $user->id }}"
                                            data-user-name="{{ $user->name }}"
                                            data-status="{{ $status }}"
                                            data-join-date="{{ $joinDate ? $joinDate->format('Y-m-d') : '' }}"
                                            data-resign-date="{{ $resignDate ? $resignDate->format('Y-m-d') : '' }}"
                                            data-notes="{{ $latestHist?->notes ?? '' }}">
                                            <i class="mdi mdi-pencil me-1"></i>Edit
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- ========================================================================= --}}
        {{-- ── TAB 4: KPI E-COMMERCE ──────────────────────────────────────────────── --}}
        {{-- ========================================================================= --}}
        <div class="tab-pane fade" id="tab-kpi-ecommerce" role="tabpanel">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-3">
                <p class="text-muted mb-0">Evaluasi performa, pencapaian target, dan pembobotan nilai khusus tim E-Commerce</p>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal-create-kpi-period">
                    <i class="mdi mdi-calendar-plus me-1"></i>Buka Periode KPI Baru
                </button>
            </div>

            {{-- Filter Periode --}}
            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-body p-3">
                    <form action="{{ route('sales-target.index') }}" method="GET" class="row g-3 align-items-center" id="form-kpi-ecommerce-filter">
                        <input type="hidden" name="year" value="{{ $currentYear }}">
                        <input type="hidden" name="active_tab" value="tab-kpi-ecommerce">
                        <div class="col-auto">
                            <label class="fw-semibold text-muted small"><i class="mdi mdi-calendar-month me-1"></i>Pilih Periode:</label>
                        </div>
                        <div class="col-auto">
                            <select name="kpi_month" class="form-select form-select-sm" onchange="this.form.submit()">
                                @for ($m = 1; $m <= 12; $m++)
                                    @php
                                        $mName = \Carbon\Carbon::createFromDate(2026, $m, 1)->locale('id')->isoFormat('MMMM');
                                    @endphp
                                    <option value="{{ $m }}" {{ $kpiCurrentMonth == $m ? 'selected' : '' }}>{{ $mName }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-auto">
                            <select name="kpi_year" class="form-select form-select-sm" onchange="this.form.submit()">
                                @foreach ($kpiYears as $y)
                                    <option value="{{ $y }}" {{ $kpiCurrentYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                                @endforeach
                            </select>
                        </div>
                        @if ($kpiPeriod)
                            <div class="col-auto ms-auto">
                                <span class="badge bg-label-{{ $kpiPeriod->status == 'published' ? 'success' : ($kpiPeriod->status == 'review' ? 'warning' : 'primary') }} p-2">
                                    <i class="mdi mdi-information-outline me-1"></i>Status Periode: {{ strtoupper($kpiPeriod->status) }}
                                </span>
                            </div>
                        @endif
                    </form>
                </div>
            </div>

            {{-- Sub-tabs --}}
            <div class="nav-align-top mb-4">
                <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item">
                        <button type="button" class="nav-link active py-3" role="tab" data-bs-toggle="tab" data-bs-target="#kpi-ec-assignments">
                            <i class="mdi mdi-account-star me-2"></i><strong>Penilaian Tim E-Commerce ({{ $kpiPeriod ? $kpiPeriod->period_label : 'Periode Belum Dibuat' }})</strong>
                        </button>
                    </li>
                    <li class="nav-item">
                        <button type="button" class="nav-link py-3" role="tab" data-bs-toggle="tab" data-bs-target="#kpi-ec-templates">
                            <i class="mdi mdi-format-list-checks me-2"></i><strong>Master Template Indikator</strong>
                        </button>
                    </li>
                </ul>

                <div class="tab-content bg-white p-4 border border-top-0 rounded-bottom">

                    {{-- Sub-tab 1: Penilaian Tim --}}
                    <div class="tab-pane fade show active" id="kpi-ec-assignments" role="tabpanel">
                        @if (!$kpiPeriod)
                            <div class="text-center py-5">
                                <div class="mb-3">
                                    <i class="mdi mdi-calendar-blank text-muted" style="font-size: 4rem;"></i>
                                </div>
                                <h5 class="fw-bold mb-1">Periode {{ \Carbon\Carbon::createFromDate($kpiCurrentYear, $kpiCurrentMonth, 1)->locale('id')->isoFormat('MMMM YYYY') }} Belum Dibuka</h5>
                                <p class="text-muted mb-4">Klik tombol di bawah ini untuk membuka periode dan meng-generate KPI otomatis untuk tim E-Commerce.</p>
                                <form action="{{ route('ecommerce.kpi.period.create') }}" method="POST" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="year" value="{{ $kpiCurrentYear }}">
                                    <input type="hidden" name="month" value="{{ $kpiCurrentMonth }}">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="mdi mdi-plus-circle me-1"></i>Buka Periode Ini Sekarang
                                    </button>
                                </form>
                            </div>
                        @elseif ($kpiAssignments->isEmpty())
                            <div class="alert alert-warning mb-0">
                                <i class="mdi mdi-alert me-2"></i>Belum ada assignment yang terdaftar pada periode ini. Pastikan terdapat user sales dengan tipe <code>ecommerce</code> di data roster.
                            </div>
                        @else
                            <div class="row g-4">
                                @foreach ($kpiAssignments as $assignment)
                                    <div class="col-lg-6 col-12">
                                        <div class="card border shadow-none h-100">
                                            <div class="card-body">
                                                <div class="d-flex align-items-center justify-content-between mb-3">
                                                    <div class="d-flex align-items-center gap-3">
                                                        <div class="avatar avatar-md">
                                                            @if ($assignment->user->image)
                                                                <img src="{{ asset('storage/' . $assignment->user->image) }}" alt="{{ $assignment->user->name }}" class="rounded-circle">
                                                            @else
                                                                <span class="avatar-initial rounded-circle bg-label-primary font-weight-bold">
                                                                    {{ strtoupper(substr($assignment->user->name, 0, 2)) }}
                                                                </span>
                                                            @endif
                                                        </div>
                                                        <div>
                                                            <h5 class="mb-0 fw-bold">{{ $assignment->user->name }}</h5>
                                                            <small class="text-muted">{{ $assignment->user->email }}</small>
                                                        </div>
                                                    </div>
                                                    <div class="text-end">
                                                        <span class="badge bg-label-{{ $assignment->status_badge_color }} text-capitalize px-3 py-2">
                                                            {{ $assignment->status }}
                                                        </span>
                                                    </div>
                                                </div>

                                                <hr class="my-3">

                                                {{-- Skor & Predikat --}}
                                                <div class="row align-items-center bg-light rounded p-3 mb-3 mx-0">
                                                    <div class="col-8">
                                                        <span class="text-muted small d-block mb-1">Total Nilai KPI Akhir</span>
                                                        <h3 class="mb-0 fw-bold text-primary">
                                                            {{ number_format($assignment->total_score, 1) }}
                                                            <span class="fs-6 text-muted fw-normal">/ 100</span>
                                                        </h3>
                                                        <small class="text-muted">
                                                            Grade: <strong class="badge bg-{{ $assignment->grade_badge_color }}">{{ $assignment->grade ?? '-' }}</strong>
                                                            @if ($assignment->evaluated_at)
                                                                • Dinilai: {{ $assignment->evaluated_at->format('d M Y') }}
                                                            @endif
                                                        </small>
                                                    </div>
                                                    <div class="col-4 text-end">
                                                        <div class="kpi-score-circle ms-auto {{ $assignment->grade == 'A' ? 'badge-grade-a' : ($assignment->grade == 'B' ? 'badge-grade-b' : ($assignment->grade == 'C' ? 'badge-grade-c' : 'badge-grade-d')) }}">
                                                            {{ $assignment->grade ?? '-' }}
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- Summary 4 Indikator Utama --}}
                                                <div class="table-responsive mb-3">
                                                    <table class="table table-sm table-borderless small mb-0">
                                                        <tbody>
                                                            @foreach ($assignment->items->take(4) as $item)
                                                                <tr>
                                                                    <td class="text-truncate" style="max-width: 180px;">
                                                                        <span class="badge bg-label-{{ $item->type_badge_color }} me-1">{{ substr(strtoupper($item->kpi_type), 0, 1) }}</span>
                                                                        {{ $item->kpi_name }}
                                                                    </td>
                                                                    <td class="text-end fw-semibold">
                                                                        @if ($item->unit == 'IDR')
                                                                            Rp {{ number_format($item->actual_final, 0, ',', '.') }}
                                                                        @else
                                                                            {{ number_format($item->actual_final, 0) }} {{ $item->unit }}
                                                                        @endif
                                                                    </td>
                                                                    <td class="text-end text-muted">
                                                                        {{ number_format($item->achievement_rate, 0) }}%
                                                                    </td>
                                                                    <td class="text-end fw-bold text-primary">
                                                                        +{{ number_format($item->score, 1) }}
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                    @if ($assignment->items->count() > 4)
                                                        <div class="text-center mt-2">
                                                            <small class="text-muted">+ {{ $assignment->items->count() - 4 }} indikator lainnya...</small>
                                                        </div>
                                                    @endif
                                                </div>

                                                {{-- Action Buttons --}}
                                                <div class="d-flex gap-2 pt-2 border-top">
                                                    <form action="{{ route('ecommerce.kpi.sync', $assignment->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-outline-secondary" title="Tarik data actual terbaru dari sistem">
                                                            <i class="mdi mdi-refresh me-1"></i>Sync Data
                                                        </button>
                                                    </form>
                                                    <a href="{{ route('ecommerce.kpi.evaluate', $assignment->id) }}" class="btn btn-sm btn-primary flex-grow-1">
                                                        <i class="mdi mdi-pencil-box-outline me-1"></i>Beri Nilai &amp; Review
                                                    </a>
                                                    @if ($assignment->status != 'published')
                                                        <form action="{{ route('ecommerce.kpi.publish', $assignment->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin mem-publish nilai KPI untuk {{ $assignment->user->name }}? Nilai ini akan tampil pada rapor employee.');">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-outline-success">
                                                                <i class="mdi mdi-check-decagram-outline me-1"></i>Publish
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    {{-- Sub-tab 2: Master Template Indikator --}}
                    <div class="tab-pane fade" id="kpi-ec-templates" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h6 class="mb-0 fw-bold">Daftar Indikator Standar KPI E-Commerce</h6>
                                <small class="text-muted">Indikator yang otomatis diterapkan ketika periode KPI baru dibuka</small>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 50px;">No</th>
                                        <th>Nama Indikator</th>
                                        <th>Tipe</th>
                                        <th>Satuan</th>
                                        <th class="text-end">Target Default</th>
                                        <th class="text-end">Bobot Default</th>
                                        <th>Handler Kalkulasi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($kpiTemplates as $idx => $t)
                                        <tr>
                                            <td>{{ $idx + 1 }}</td>
                                            <td>
                                                <div class="fw-semibold">{{ $t->name }}</div>
                                                <small class="text-muted">{{ $t->description }}</small>
                                            </td>
                                            <td>
                                                <span class="badge bg-label-{{ $t->type == 'automatic' ? 'primary' : ($t->type == 'hybrid' ? 'warning' : 'info') }}">
                                                    {{ ucfirst($t->type) }}
                                                </span>
                                            </td>
                                            <td><code>{{ $t->unit }}</code></td>
                                            <td class="text-end fw-semibold">
                                                @if ($t->unit == 'IDR')
                                                    Rp {{ number_format($t->default_target, 0, ',', '.') }}
                                                @else
                                                    {{ number_format($t->default_target, 0) }}
                                                @endif
                                            </td>
                                            <td class="text-end fw-bold text-primary">{{ number_format($t->default_weight, 1) }}%</td>
                                            <td><span class="badge bg-light text-dark border font-monospace">{{ $t->calculation_handler ?? '-' }}</span></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <td colspan="5" class="fw-bold text-end">Total Bobot Standar:</td>
                                        <td class="text-end fw-bold text-success">{{ number_format($kpiTemplates->sum('default_weight'), 1) }}%</td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>

    {{-- Info Alert --}}
    <div class="alert alert-info d-flex align-items-start mt-4" role="alert">
        <i class="mdi mdi-information-outline me-3 mt-1 fs-5 flex-shrink-0"></i>
        <div class="small">
            <strong>Koneksi Dashboard Terpadu:</strong> Perubahan status aktif sales pada tab <em>Target &amp; Active Roster</em> akan langsung menyesuaikan daftar sales di Card <strong>Sales Overview</strong> dan Leaderboard pada Dashboard Admin.
        </div>
    </div>

</div>

{{-- Modal: Edit Status Kepegawaian Sales --}}
<div class="modal fade" id="modal-edit-employee" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="mdi mdi-account-edit me-2"></i>Status Kepegawaian — <span id="emp-sales-name">—</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('sales-target.save-history') }}" method="POST">
                @csrf
                <input type="hidden" name="user_id" id="emp-user-id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Status Tim <span class="text-danger">*</span></label>
                        <select name="status" id="emp-status" class="form-select" required>
                            <option value="active">🟢 Aktif</option>
                            <option value="resigned">🔴 Resigned / Keluar</option>
                            <option value="cuti">🟡 Cuti Sementara</option>
                            <option value="transferred">🔵 Mutasi / Pindah Divisi</option>
                        </select>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label">Tanggal Masuk</label>
                            <input type="date" name="join_date" id="emp-join-date" class="form-control">
                        </div>
                        <div class="col-6">
                            <label class="form-label">Tanggal Keluar (Jika Resign)</label>
                            <input type="date" name="resign_date" id="emp-resign-date" class="form-control">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Catatan</label>
                        <textarea name="notes" id="emp-notes" class="form-control" rows="2" placeholder="Alasan resign, handover akun, atau catatan kepegawaian..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="mdi mdi-content-save-outline me-1"></i>Simpan Status
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal: Histori Target Sales --}}
<div class="modal fade" id="modal-history" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="mdi mdi-history me-2"></i>
                    Histori Target — <span id="history-sales-name">—</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <div class="table-responsive">
                    <table class="table history-table align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4">Tahun</th>
                                <th>Target / Tahun</th>
                                <th>Semester 1 &amp; 2</th>
                                <th>/ Bulan</th>
                                <th>% Kontribusi</th>
                                <th class="pe-4">Tipe Sales</th>
                            </tr>
                        </thead>
                        <tbody id="history-tbody">
                            <tr><td colspan="6" class="text-center text-muted py-4">Belum ada histori.</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

{{-- Modal: Tambah Sales ke Roster --}}
<div class="modal fade" id="modal-add-sales" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="mdi mdi-account-plus me-2 text-primary"></i>Tambah Sales — Roster {{ $currentYear }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('sales-target.add-sales', $currentYear) }}" method="POST" id="form-add-sales">
                @csrf
                <div class="modal-body">
                    {{-- Mode Selection --}}
                    <div class="mb-3 p-2 bg-light rounded border">
                        <label class="form-label fw-bold d-block mb-2 text-dark">Pilih Sumber Sales:</label>
                        <div class="d-flex gap-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="mode" id="mode-existing" value="existing" checked>
                                <label class="form-check-label fw-semibold" for="mode-existing">
                                    Dari Akun yang Sudah Ada
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="mode" id="mode-new" value="new">
                                <label class="form-check-label fw-semibold" for="mode-new">
                                    Buat Akun Sales Baru
                                </label>
                            </div>
                        </div>
                    </div>

                    {{-- Existing User Section --}}
                    <div id="section-existing-user">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Pilih Pengguna <span class="text-danger">*</span></label>
                            <select name="user_id" id="add-existing-user" class="form-select">
                                <option value="">-- Pilih Sales / Karyawan --</option>
                                @forelse ($allExistingUsers as $u)
                                    <option value="{{ $u->id }}">
                                        {{ $u->name }} ({{ $u->email }}) — Role: {{ $u->role }} {{ $u->active == '1' ? '🟢' : '🔴' }}
                                    </option>
                                @empty
                                    <option value="" disabled>Semua user yang terdaftar sudah ada di roster tahun {{ $currentYear }}</option>
                                @endforelse
                            </select>
                        </div>
                    </div>

                    {{-- New User Section --}}
                    <div id="section-new-user" style="display: none;">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nama Lengkap Sales <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="add-new-name" class="form-control" placeholder="mis. Budi Santoso">
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label fw-semibold">Email Login <span class="text-danger">*</span></label>
                                <input type="email" name="email" id="add-new-email" class="form-control" placeholder="sales@reftech.co.id">
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-semibold">Password <span class="text-danger">*</span></label>
                                <input type="password" name="password" id="add-new-password" class="form-control" placeholder="Min. 6 Karakter">
                            </div>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label">No. Telepon / WA</label>
                                <input type="text" name="phone" id="add-new-phone" class="form-control" placeholder="08123456789">
                            </div>
                            <div class="col-6">
                                <label class="form-label">Tanggal Masuk</label>
                                <input type="date" name="join_date" id="add-new-join-date" class="form-control" value="{{ date('Y-m-d') }}">
                            </div>
                        </div>
                    </div>

                    <hr class="my-3">

                    {{-- Common Target & Roster Fields --}}
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold">Tipe Sales <span class="text-danger">*</span></label>
                            <select name="sales_type" id="add-sales-type" class="form-select" required>
                                <option value="field">🎯 Direct / Field</option>
                                <option value="crm">🔄 CRM / Retention</option>
                                <option value="ecommerce">🌐 E-Commerce</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Subtitle / Area (Dashboard)</label>
                            <input type="text" name="subtitle" id="add-subtitle" class="form-control" placeholder="mis. Cikarang / Online">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Target Tahunan {{ $currentYear }} (Rp)</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="text" class="form-control" id="add-target-display" placeholder="mis. 2.500.000.000" autocomplete="off">
                            <input type="hidden" name="target_annual" id="add-target-hidden" value="0">
                        </div>
                        <small class="text-muted d-block mt-1">Target bulanan otomatis dibagi 12.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="mdi mdi-check-circle me-1"></i>Tambahkan ke Roster
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal: Edit Data Sales & Target --}}
<div class="modal fade" id="modal-edit-sales" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="mdi mdi-account-edit me-2 text-primary"></i>Edit Sales &amp; Target — <span id="edit-sales-title-name">—</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="" method="POST" id="form-edit-sales">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="edit-name" class="form-control" required>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" id="edit-email" class="form-control" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label">No. Telepon</label>
                            <input type="text" name="phone" id="edit-phone" class="form-control" placeholder="08123456789">
                        </div>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold">Tipe Sales <span class="text-danger">*</span></label>
                            <select name="sales_type" id="edit-sales-type" class="form-select" required>
                                <option value="field">🎯 Direct / Field</option>
                                <option value="crm">🔄 CRM / Retention</option>
                                <option value="ecommerce">🌐 E-Commerce</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Subtitle / Area (Dashboard)</label>
                            <input type="text" name="subtitle" id="edit-subtitle" class="form-control" placeholder="mis. Bandung / Direct">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Target Tahunan {{ $currentYear }} (Rp)</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="text" class="form-control" id="edit-target-display" placeholder="0" autocomplete="off">
                            <input type="hidden" name="target_annual" id="edit-target-hidden" value="0">
                        </div>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold">Status Kepegawaian <span class="text-danger">*</span></label>
                            <select name="status" id="edit-status" class="form-select" required>
                                <option value="active">🟢 Aktif</option>
                                <option value="resigned">🔴 Resigned / Keluar</option>
                                <option value="cuti">🟡 Cuti Sementara</option>
                                <option value="transferred">🔵 Mutasi / Pindah Divisi</option>
                            </select>
                        </div>
                        <div class="col-6 d-flex align-items-end pb-1">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_active_roster" value="1" id="edit-is-roster">
                                <label class="form-check-label fw-semibold small" for="edit-is-roster">
                                    Aktif di Dashboard Roster
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label">Tanggal Masuk</label>
                            <input type="date" name="join_date" id="edit-join-date" class="form-control">
                        </div>
                        <div class="col-6">
                            <label class="form-label">Tanggal Resign (Jika Keluar)</label>
                            <input type="date" name="resign_date" id="edit-resign-date" class="form-control">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Password Baru <span class="text-muted small fw-normal">(Kosongkan jika tidak diubah)</span></label>
                        <input type="password" name="password" id="edit-password" class="form-control" placeholder="Ganti Password...">
                    </div>

                    <div class="mb-2">
                        <label class="form-label">Catatan</label>
                        <textarea name="notes" id="edit-notes" class="form-control" rows="2" placeholder="Catatan perputaran tim atau penugasan..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="mdi mdi-content-save-outline me-1"></i>Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Hidden Form for Delete/Remove from Roster --}}
<form id="form-remove-sales" action="" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
    <input type="hidden" name="deactivate_user" id="remove-deactivate-user" value="0">
</form>

{{-- Modal: Buka Periode KPI E-Commerce Baru --}}
<div class="modal fade" id="modal-create-kpi-period" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('ecommerce.kpi.period.create') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold"><i class="mdi mdi-calendar-plus me-2 text-primary"></i>Buka Periode KPI E-Commerce</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted small">Membuka periode baru akan meng-generate matriks KPI otomatis bagi seluruh tim sales E-Commerce dan langsung menarik aktual data dari sistem.</p>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Bulan</label>
                        <select name="month" class="form-select" required>
                            @for ($m = 1; $m <= 12; $m++)
                                @php
                                    $mName = \Carbon\Carbon::createFromDate(2026, $m, 1)->locale('id')->isoFormat('MMMM');
                                @endphp
                                <option value="{{ $m }}" {{ $kpiCurrentMonth == $m ? 'selected' : '' }}>{{ $mName }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tahun</label>
                        <input type="number" name="year" class="form-control" value="{{ $kpiCurrentYear }}" min="2020" max="2099" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Buka &amp; Inisialisasi Periode</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal: Tambah Tahun --}}
<div class="modal fade" id="modal-add-year" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered" style="max-width:360px">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Tahun Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('sales-target.add-year') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <label for="input-new-year" class="form-label">Tahun <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" id="input-new-year" name="year"
                        min="2020" max="2099" placeholder="{{ date('Y') + 1 }}" required>
                    <div class="form-text">Akan membuat Semester 1 &amp; 2 untuk tahun tersebut.</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="mdi mdi-plus me-1"></i>Tambah
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('after-script')
<script>
(function () {
    // ── Aktifkan tab sesuai query string ?active_tab= (mis. setelah filter KPI E-Commerce disubmit) ──
    const activeTabParam = new URLSearchParams(window.location.search).get('active_tab');
    if (activeTabParam) {
        const trigger = document.querySelector(`#salesManagementTabs button[data-bs-target="#${activeTabParam}"]`);
        if (trigger) {
            new bootstrap.Tab(trigger).show();
        }
    }

    // ── Histori data dari server ──────────────────────────────────────────────
    const allHistories = @json($allHistories);
    const teamByYear   = @json($teamTargetByYear);

    // ── Aggregate form auto-calc ──────────────────────────────────────────────
    const aggDisplay = document.getElementById('agg-display');
    if (aggDisplay) {
        aggDisplay.addEventListener('input', function () {
            const raw = parseRaw(this.value);
            this.value = raw > 0 ? raw.toLocaleString('id-ID') : '';
            document.getElementById('agg-hidden').value   = raw || '';
            document.getElementById('agg-s1').textContent = raw > 0 ? fmt(Math.floor(raw / 2))  : '—';
            document.getElementById('agg-s2').textContent = raw > 0 ? fmt(Math.floor(raw / 2))  : '—';
            document.getElementById('agg-monthly').textContent = raw > 0 ? fmt(Math.floor(raw / 12)) : '—';
        });
    }

    // ── Helpers ───────────────────────────────────────────────────────────────
    function parseRaw(val) {
        return parseInt(String(val).replace(/\./g, '').replace(/,/g, '').replace(/[^0-9]/g, '')) || 0;
    }

    function fmt(num) {
        if (!num || num === 0) return '—';
        return 'Rp ' + Math.round(num).toLocaleString('id-ID');
    }

    function pct(part, total) {
        if (!total || total === 0) return '—';
        return (part / total * 100).toFixed(1) + '%';
    }

    // ── Auto-calc: update semua baris + total ─────────────────────────────────
    function recalcAll() {
        let total = 0;

        document.querySelectorAll('.input-annual').forEach(inp => {
            const val = parseRaw(inp.value);
            total += val;
        });

        document.querySelectorAll('#target-table tbody tr[data-user-id]').forEach(row => {
            const inp = row.querySelector('.input-annual');
            if (!inp) return;
            const val = parseRaw(inp.value);
            row.querySelector('.col-s1').textContent      = val > 0 ? fmt(Math.floor(val / 2))  : '—';
            row.querySelector('.col-s2').textContent      = val > 0 ? fmt(Math.floor(val / 2))  : '—';
            row.querySelector('.col-monthly').textContent = val > 0 ? fmt(Math.floor(val / 12)) : '—';
            row.querySelector('.col-pct').textContent     = val > 0 ? pct(val, total) : '—';
        });

        const totalAnn = document.getElementById('total-annual');
        if (totalAnn) totalAnn.textContent = total > 0 ? fmt(total) : '—';
        const totalS1 = document.getElementById('total-s1');
        if (totalS1) totalS1.textContent = total > 0 ? fmt(Math.floor(total / 2)) : '—';
        const totalS2 = document.getElementById('total-s2');
        if (totalS2) totalS2.textContent = total > 0 ? fmt(Math.floor(total / 2)) : '—';
        const totalMo = document.getElementById('total-monthly');
        if (totalMo) totalMo.textContent = total > 0 ? fmt(Math.floor(total / 12)) : '—';
    }

    // Bind event input ke setiap kolom target tahunan
    document.querySelectorAll('.input-annual').forEach(inp => {
        inp.addEventListener('input', function () {
            const raw = parseRaw(this.value);
            this.value = raw > 0 ? raw.toLocaleString('id-ID') : '';
            this.closest('tr').querySelector('.input-annual-hidden').value = raw;
            recalcAll();
        });
    });

    // Toggle active roster styling
    document.querySelectorAll('.roster-toggle').forEach(toggle => {
        toggle.addEventListener('change', function () {
            const row = this.closest('tr');
            if (this.checked) {
                row.classList.remove('opacity-75', 'bg-light');
            } else {
                row.classList.add('opacity-75', 'bg-light');
            }
        });
    });

    // ── Modal Histori Target ──────────────────────────────────────────────────
    document.querySelectorAll('.btn-history').forEach(btn => {
        btn.addEventListener('click', function () {
            const userId   = this.dataset.userId;
            const userName = this.dataset.userName;
            const records  = (allHistories[userId] || []).slice().sort((a, b) => b.year - a.year);

            document.getElementById('history-sales-name').textContent = userName;
            const tbody = document.getElementById('history-tbody');
            tbody.innerHTML = '';

            if (records.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted py-4">Belum ada histori target untuk sales ini.</td></tr>';
            } else {
                records.forEach(r => {
                    const annual  = parseInt(r.target_annual) || 0;
                    const sem     = annual > 0 ? Math.floor(annual / 2) : 0;
                    const monthly = annual > 0 ? Math.floor(annual / 12) : 0;
                    const teamTot = teamByYear[r.year] || 0;
                    const pctVal  = (teamTot > 0 && annual > 0) ? (annual / teamTot * 100).toFixed(1) + '%' : '—';

                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td class="ps-4 fw-semibold">${r.year}</td>
                        <td class="fw-bold">${annual > 0 ? fmt(annual) : '—'}</td>
                        <td>${sem > 0 ? fmt(sem) : '—'}</td>
                        <td>${monthly > 0 ? fmt(monthly) : '—'}</td>
                        <td>${pctVal}</td>
                        <td class="pe-4"><span class="badge bg-label-primary">${r.sales_type || 'field'}</span></td>
                    `;
                    tbody.appendChild(tr);
                });
            }

            const modal = new bootstrap.Modal(document.getElementById('modal-history'));
            modal.show();
        });
    });

    // ── Modal Edit Employee History ───────────────────────────────────────────
    document.querySelectorAll('.btn-edit-employee').forEach(btn => {
        btn.addEventListener('click', function () {
            document.getElementById('emp-user-id').value = this.dataset.userId;
            document.getElementById('emp-sales-name').textContent = this.dataset.userName;
            document.getElementById('emp-status').value = this.dataset.status;
            document.getElementById('emp-join-date').value = this.dataset.joinDate;
            document.getElementById('emp-resign-date').value = this.dataset.resignDate;
            document.getElementById('emp-notes').value = this.dataset.notes;

            const modal = new bootstrap.Modal(document.getElementById('modal-edit-employee'));
            modal.show();
        });
    });

    // ── Modal Tambah Sales (Switch Mode Existing vs New) ──────────────────────
    document.querySelectorAll('input[name="mode"]').forEach(radio => {
        radio.addEventListener('change', function () {
            const isNew = this.value === 'new';
            document.getElementById('section-existing-user').style.display = isNew ? 'none' : 'block';
            document.getElementById('section-new-user').style.display = isNew ? 'block' : 'none';
            if (isNew) {
                document.getElementById('add-new-name').setAttribute('required', 'required');
                document.getElementById('add-new-email').setAttribute('required', 'required');
                document.getElementById('add-new-password').setAttribute('required', 'required');
                document.getElementById('add-existing-user').removeAttribute('required');
            } else {
                document.getElementById('add-existing-user').setAttribute('required', 'required');
                document.getElementById('add-new-name').removeAttribute('required');
                document.getElementById('add-new-email').removeAttribute('required');
                document.getElementById('add-new-password').removeAttribute('required');
            }
        });
    });

    // Auto-formatting target display di Modal Add Sales
    const addTgtDisplay = document.getElementById('add-target-display');
    if (addTgtDisplay) {
        addTgtDisplay.addEventListener('input', function() {
            const raw = parseRaw(this.value);
            this.value = raw > 0 ? raw.toLocaleString('id-ID') : '';
            document.getElementById('add-target-hidden').value = raw;
        });
    }

    // Auto-formatting target display di Modal Edit Sales
    const editTgtDisplay = document.getElementById('edit-target-display');
    if (editTgtDisplay) {
        editTgtDisplay.addEventListener('input', function() {
            const raw = parseRaw(this.value);
            this.value = raw > 0 ? raw.toLocaleString('id-ID') : '';
            document.getElementById('edit-target-hidden').value = raw;
        });
    }

    // ── Modal Edit Data Sales & Target ────────────────────────────────────────
    document.querySelectorAll('.btn-edit-sales').forEach(btn => {
        btn.addEventListener('click', function () {
            const form = document.getElementById('form-edit-sales');
            form.action = this.dataset.updateUrl;

            document.getElementById('edit-sales-title-name').textContent = this.dataset.name;
            document.getElementById('edit-name').value = this.dataset.name;
            document.getElementById('edit-email').value = this.dataset.email;
            document.getElementById('edit-phone').value = this.dataset.phone || '';
            document.getElementById('edit-sales-type').value = this.dataset.salesType || 'field';
            document.getElementById('edit-subtitle').value = this.dataset.subtitle || '';
            
            const annual = parseInt(this.dataset.annual) || 0;
            document.getElementById('edit-target-display').value = annual > 0 ? annual.toLocaleString('id-ID') : '';
            document.getElementById('edit-target-hidden').value = annual;

            document.getElementById('edit-status').value = this.dataset.status || 'active';
            document.getElementById('edit-is-roster').checked = this.dataset.isRoster === '1';
            document.getElementById('edit-join-date').value = this.dataset.joinDate || '';
            document.getElementById('edit-resign-date').value = this.dataset.resignDate || '';
            document.getElementById('edit-notes').value = this.dataset.notes || '';
            document.getElementById('edit-password').value = '';

            const modal = new bootstrap.Modal(document.getElementById('modal-edit-sales'));
            modal.show();
        });
    });

    // ── Hapus / Remove Sales dari Roster (SweetAlert2) ─────────────────────────
    document.querySelectorAll('.btn-remove-sales').forEach(btn => {
        btn.addEventListener('click', function () {
            const userName = this.dataset.userName;
            const removeUrl = this.dataset.removeUrl;

            Swal.fire({
                title: `Hapus ${userName}?`,
                html: `
                    <p class="mb-3 text-muted">Apakah Anda yakin ingin menghapus sales <strong>${userName}</strong> dari Roster &amp; Target tahun <strong>{{ $currentYear }}</strong>?</p>
                    <div class="form-check text-start p-2 bg-light rounded border d-inline-block">
                        <input class="form-check-input" type="checkbox" id="swal-deactivate-user" value="1">
                        <label class="form-check-label small" for="swal-deactivate-user">
                            Sekaligus nonaktifkan akun sales secara global
                        </label>
                    </div>
                `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: '<i class="mdi mdi-trash-can me-1"></i>Ya, Hapus dari Roster',
                cancelButtonText: 'Batal',
                customClass: {
                    confirmButton: 'btn btn-danger me-2 waves-effect',
                    cancelButton: 'btn btn-outline-secondary waves-effect'
                },
                buttonsStyling: false,
            }).then(result => {
                if (result.isConfirmed) {
                    const deactivate = document.getElementById('swal-deactivate-user')?.checked ? '1' : '0';
                    const form = document.getElementById('form-remove-sales');
                    form.action = removeUrl;
                    document.getElementById('remove-deactivate-user').value = deactivate;
                    form.submit();
                }
            });
        });
    });
})();
</script>
@endpush
