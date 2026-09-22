@extends('layouts.sales.app')
@section('title', 'KPI E-Commerce')

@push('after-style')
<style>
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
                <i class="mdi mdi-shopping-outline me-2 text-primary"></i>KPI E-Commerce
            </h4>
            <p class="text-muted mb-0">Evaluasi performa, pencapaian target, dan pembobotan nilai khusus tim E-Commerce</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal-create-period">
                <i class="mdi mdi-calendar-plus me-1"></i>Buka Periode KPI Baru
            </button>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <i class="mdi mdi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            <i class="mdi mdi-alert-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Filter Periode --}}
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-body p-3">
            <form action="{{ route('ecommerce.kpi.index') }}" method="GET" class="row g-3 align-items-center">
                <div class="col-auto">
                    <label class="fw-semibold text-muted small"><i class="mdi mdi-calendar-month me-1"></i>Pilih Periode:</label>
                </div>
                <div class="col-auto">
                    <select name="month" class="form-select form-select-sm" onchange="this.form.submit()">
                        @for ($m = 1; $m <= 12; $m++)
                            @php
                                $mName = \Carbon\Carbon::createFromDate(2026, $m, 1)->locale('id')->isoFormat('MMMM');
                            @endphp
                            <option value="{{ $m }}" {{ $currentMonth == $m ? 'selected' : '' }}>{{ $mName }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-auto">
                    <select name="year" class="form-select form-select-sm" onchange="this.form.submit()">
                        @foreach ($years as $y)
                            <option value="{{ $y }}" {{ $currentYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>
                </div>
                @if ($period)
                    <div class="col-auto ms-auto">
                        <span class="badge bg-label-{{ $period->status == 'published' ? 'success' : ($period->status == 'review' ? 'warning' : 'primary') }} p-2">
                            <i class="mdi mdi-information-outline me-1"></i>Status Periode: {{ strtoupper($period->status) }}
                        </span>
                    </div>
                @endif
            </form>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="nav-align-top mb-4">
        <ul class="nav nav-tabs" role="tablist">
            <li class="nav-item">
                <button type="button" class="nav-link active py-3" role="tab" data-bs-toggle="tab" data-bs-target="#navs-assignments">
                    <i class="mdi mdi-account-star me-2"></i><strong>Penilaian Tim E-Commerce ({{ $period ? $period->period_label : 'Periode Belum Dibuat' }})</strong>
                </button>
            </li>
            <li class="nav-item">
                <button type="button" class="nav-link py-3" role="tab" data-bs-toggle="tab" data-bs-target="#navs-templates">
                    <i class="mdi mdi-format-list-checks me-2"></i><strong>Master Template Indikator</strong>
                </button>
            </li>
        </ul>

        <div class="tab-content bg-white p-4 border border-top-0 rounded-bottom">
            
            {{-- TAB 1: Penilaian Tim --}}
            <div class="tab-pane fade show active" id="navs-assignments" role="tabpanel">
                @if (!$period)
                    <div class="text-center py-5">
                        <div class="mb-3">
                            <i class="mdi mdi-calendar-blank text-muted" style="font-size: 4rem;"></i>
                        </div>
                        <h5 class="fw-bold mb-1">Periode {{ \Carbon\Carbon::createFromDate($currentYear, $currentMonth, 1)->locale('id')->isoFormat('MMMM YYYY') }} Belum Dibuka</h5>
                        <p class="text-muted mb-4">Klik tombol di bawah ini untuk membuka periode dan meng-generate KPI otomatis untuk tim E-Commerce.</p>
                        <form action="{{ route('ecommerce.kpi.period.create') }}" method="POST" class="d-inline">
                            @csrf
                            <input type="hidden" name="year" value="{{ $currentYear }}">
                            <input type="hidden" name="month" value="{{ $currentMonth }}">
                            <button type="submit" class="btn btn-primary">
                                <i class="mdi mdi-plus-circle me-1"></i>Buka Periode Ini Sekarang
                            </button>
                        </form>
                    </div>
                @elseif ($assignments->isEmpty())
                    <div class="alert alert-warning mb-0">
                        <i class="mdi mdi-alert me-2"></i>Belum ada assignment yang terdaftar pada periode ini. Pastikan terdapat user sales dengan tipe <code>ecommerce</code> di data roster.
                    </div>
                @else
                    <div class="row g-4">
                        @foreach ($assignments as $assignment)
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

            {{-- TAB 2: Master Template Indikator --}}
            <div class="tab-pane fade" id="navs-templates" role="tabpanel">
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
                            @foreach ($templates as $idx => $t)
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
                                <td class="text-end fw-bold text-success">{{ number_format($templates->sum('default_weight'), 1) }}%</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

        </div>
    </div>

</div>

{{-- Modal Buka Periode Baru --}}
<div class="modal fade" id="modal-create-period" tabindex="-1" aria-hidden="true">
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
                                <option value="{{ $m }}" {{ $currentMonth == $m ? 'selected' : '' }}>{{ $mName }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tahun</label>
                        <input type="number" name="year" class="form-control" value="{{ $currentYear }}" min="2020" max="2099" required>
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
@endsection
