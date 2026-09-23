@extends('layouts.sales.app')
@section('title', 'Rapor KPI E-Commerce Saya')

@push('after-style')
<style>
    .kpi-hero-card {
        background: linear-gradient(135deg, #696cff 0%, #3f42c9 100%);
        color: #ffffff;
        border-radius: 0.75rem;
    }
    .kpi-stat-box {
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(5px);
        border-radius: 0.5rem;
        padding: 1rem;
    }
    .kpi-big-grade {
        width: 75px;
        height: 75px;
        border-radius: 50%;
        background: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 2rem;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
</style>
@endpush

@section('content')
<div class="container-fluid flex-grow-1 container-p-y">

    {{-- Header & Period Selector --}}
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1">
                <i class="mdi mdi-certificate-outline me-2 text-primary"></i>Rapor KPI E-Commerce Saya
            </h4>
            <p class="text-muted mb-0">Rincian performa, pencapaian target, dan evaluasi bulanan Anda</p>
        </div>

        @if ($assignments->isNotEmpty())
            <div class="d-flex align-items-center gap-2">
                <label class="fw-semibold text-muted small text-nowrap"><i class="mdi mdi-history me-1"></i>Pilih Periode:</label>
                <form action="{{ route('ecommerce.my-kpi') }}" method="GET">
                    <select name="assignment_id" class="form-select form-select-sm" onchange="this.form.submit()">
                        @foreach ($assignments as $a)
                            <option value="{{ $a->id }}" {{ $selectedAssignment && $selectedAssignment->id == $a->id ? 'selected' : '' }}>
                                {{ $a->period->period_label }} (Skor: {{ number_format($a->total_score, 1) }} - {{ $a->grade ?? 'N/A' }})
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>
        @endif
    </div>

    @if (!$selectedAssignment)
        <div class="card border-0 shadow-sm text-center py-5">
            <div class="card-body">
                <i class="mdi mdi-calendar-search text-muted" style="font-size: 4rem;"></i>
                <h5 class="fw-bold mt-3 mb-1">Belum Ada Rapor KPI yang Ditugaskan</h5>
                <p class="text-muted mb-0">Periode KPI E-Commerce belum dibuka oleh Supervisor/Admin. Silakan hubungi atasan Anda.</p>
            </div>
        </div>
    @else
        {{-- Hero KPI Scorecard --}}
        <div class="card kpi-hero-card mb-4 border-0 shadow-lg">
            <div class="card-body p-4">
                <div class="row align-items-center g-3">
                    <div class="col-lg-7 col-12">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="avatar avatar-lg">
                                @if (Auth::user()->image)
                                    <img src="{{ asset('storage/' . Auth::user()->image) }}" alt="{{ Auth::user()->name }}" class="rounded-circle border border-2 border-white">
                                @else
                                    <span class="avatar-initial rounded-circle bg-white text-primary fw-bold fs-4">
                                        {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                                    </span>
                                @endif
                            </div>
                            <div>
                                <h4 class="text-white mb-0 fw-bold">{{ Auth::user()->name }}</h4>
                                <span class="badge bg-white text-primary mt-1">E-Commerce Specialist</span>
                                <span class="text-white opacity-75 small ms-2">• Periode: <strong>{{ $selectedAssignment->period->period_label }}</strong></span>
                            </div>
                        </div>

                        <div class="row g-2 pt-2">
                            <div class="col-4">
                                <div class="kpi-stat-box text-center">
                                    <small class="d-block text-white opacity-75 mb-1">Status Rapor</small>
                                    <span class="badge bg-{{ $selectedAssignment->status == 'published' ? 'success' : 'warning' }} text-uppercase">
                                        {{ $selectedAssignment->status }}
                                    </span>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="kpi-stat-box text-center">
                                    <small class="d-block text-white opacity-75 mb-1">Total Indikator</small>
                                    <span class="fw-bold fs-6">{{ $selectedAssignment->items->count() }} Metrik</span>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="kpi-stat-box text-center">
                                    <small class="d-block text-white opacity-75 mb-1">Evaluator</small>
                                    <span class="fw-bold small text-truncate d-block">{{ $selectedAssignment->evaluator->name ?? 'Tim Management' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-5 col-12">
                        <div class="bg-white bg-opacity-10 rounded p-4 text-center border border-white border-opacity-25">
                            <span class="text-white opacity-75 small text-uppercase fw-semibold d-block mb-1">Overall KPI Score</span>
                            <div class="d-flex align-items-center justify-content-center gap-3 my-2">
                                <h1 class="display-4 fw-bold text-white mb-0">{{ number_format($selectedAssignment->total_score, 1) }}</h1>
                                <span class="text-white opacity-75 fs-4">/ 100</span>
                                <div class="kpi-big-grade ms-2 text-{{ $selectedAssignment->grade == 'A' ? 'success' : ($selectedAssignment->grade == 'B' ? 'info' : ($selectedAssignment->grade == 'C' ? 'warning' : 'danger')) }}">
                                    {{ $selectedAssignment->grade ?? '-' }}
                                </div>
                            </div>
                            <small class="text-white opacity-75">
                                @if ($selectedAssignment->grade == 'A')
                                    🌟 Performa Sangat Unggul (Exceeds Expectations)
                                @elseif ($selectedAssignment->grade == 'B')
                                    👍 Performa Baik (Meets Expectations)
                                @elseif ($selectedAssignment->grade == 'C')
                                    ⚠️ Performa Cukup (Needs Improvement)
                                @else
                                    Perlu Peningkatan Signifikan
                                @endif
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Evaluator General Notes (Jika Ada) --}}
        @if ($selectedAssignment->notes)
            <div class="card mb-4 border-primary border-start border-4 shadow-sm">
                <div class="card-body p-3">
                    <div class="d-flex align-items-start gap-3">
                        <div class="avatar avatar-sm">
                            <span class="avatar-initial rounded-circle bg-label-primary">
                                <i class="mdi mdi-comment-quote"></i>
                            </span>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1 fw-bold text-primary">Catatan &amp; Masukan dari Evaluator:</h6>
                            <p class="mb-0 text-muted fst-italic">"{{ $selectedAssignment->notes }}"</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Detail Breakdown Table --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-light d-flex justify-content-between align-items-center py-3">
                <h6 class="mb-0 fw-bold"><i class="mdi mdi-table-large me-2"></i>Rincian Pencapaian Tiap Indikator KPI</h6>
                <span class="badge bg-label-primary">Periode: {{ $selectedAssignment->period->period_label }}</span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 40px;">No</th>
                            <th>Nama Indikator</th>
                            <th>Tipe</th>
                            <th class="text-end">Target</th>
                            <th class="text-end">Realisasi</th>
                            <th style="width: 150px;">Capaian (%)</th>
                            <th class="text-end">Bobot</th>
                            <th class="text-end">Poin Skor</th>
                            <th>Catatan / Feedback</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($selectedAssignment->items as $idx => $item)
                            <tr>
                                <td class="text-center">{{ $idx + 1 }}</td>
                                <td>
                                    <div class="fw-bold">{{ $item->kpi_name }}</div>
                                    <small class="text-muted">Satuan: <code>{{ $item->unit }}</code></small>
                                </td>
                                <td>
                                    <span class="badge bg-label-{{ $item->type_badge_color }} text-capitalize">
                                        {{ $item->kpi_type }}
                                    </span>
                                </td>
                                <td class="text-end fw-semibold">
                                    @if ($item->unit == 'IDR')
                                        Rp {{ number_format($item->target, 0, ',', '.') }}
                                    @else
                                        {{ number_format($item->target, 0) }}
                                    @endif
                                </td>
                                <td class="text-end fw-bold text-primary">
                                    @if ($item->unit == 'IDR')
                                        Rp {{ number_format($item->actual_final, 0, ',', '.') }}
                                    @else
                                        {{ number_format($item->actual_final, 0) }}
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="progress flex-grow-1" style="height: 6px;">
                                            <div class="progress-bar bg-{{ $item->achievement_rate >= 100 ? 'success' : ($item->achievement_rate >= 75 ? 'primary' : 'warning') }}" 
                                                 role="progressbar" 
                                                 style="width: {{ min(100, $item->achievement_rate) }}%"></div>
                                        </div>
                                        <span class="small fw-bold">{{ number_format($item->achievement_rate, 1) }}%</span>
                                    </div>
                                </td>
                                <td class="text-end text-muted">{{ number_format($item->weight, 1) }}%</td>
                                <td class="text-end fw-bold text-success fs-6">+{{ number_format($item->score, 1) }}</td>
                                <td>
                                    @if ($item->evaluator_notes)
                                        <span class="small text-muted"><i class="mdi mdi-message-text-outline me-1"></i>{{ $item->evaluator_notes }}</span>
                                    @else
                                        <span class="text-muted opacity-50 small">-</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light">
                        <tr class="fw-bold">
                            <td colspan="6" class="text-end">TOTAL:</td>
                            <td class="text-end">{{ number_format($selectedAssignment->items->sum('weight'), 1) }}%</td>
                            <td class="text-end text-primary fs-5">{{ number_format($selectedAssignment->total_score, 1) }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    @endif

</div>
@endsection
