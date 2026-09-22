@extends('layouts.sales.app')
@section('title', 'Evaluasi KPI: ' . $assignment->user->name)

@section('content')
<div class="container-fluid flex-grow-1 container-p-y">

    {{-- Breadcrumb & Back --}}
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
        <div>
            <a href="{{ route('ecommerce.kpi.index', ['year' => $assignment->period->year, 'month' => $assignment->period->month]) }}" class="btn btn-sm btn-outline-secondary mb-2">
                <i class="mdi mdi-arrow-left me-1"></i>Kembali ke Daftar KPI
            </a>
            <h4 class="fw-bold mb-1">
                <i class="mdi mdi-clipboard-check-outline me-2 text-primary"></i>Evaluasi KPI: {{ $assignment->user->name }}
            </h4>
            <p class="text-muted mb-0">Periode: <strong>{{ $assignment->period->period_label }}</strong> | Role: E-Commerce</p>
        </div>
        <div class="d-flex align-items-center gap-3 bg-white p-3 rounded border shadow-sm">
            <div class="text-end">
                <span class="text-muted small d-block">Live Total Score</span>
                <h3 class="mb-0 fw-bold text-primary" id="header-total-score">{{ number_format($assignment->total_score, 1) }} <small class="fs-6 text-muted">/ 100</small></h3>
            </div>
            <div class="badge bg-label-primary fs-5 p-2 px-3 fw-bold" id="header-grade-badge">{{ $assignment->grade ?? '-' }}</div>
        </div>
    </div>

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-4">
            <i class="mdi mdi-alert-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form action="{{ route('ecommerce.kpi.save-evaluation', $assignment->id) }}" method="POST" id="kpiEvaluationForm">
        @csrf

        {{-- Main Table --}}
        <div class="card mb-4 border-0 shadow-sm">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold"><i class="mdi mdi-format-list-numbered me-2"></i>Matriks Indikator &amp; Pembobotan</h6>
                <span class="small text-muted">Nilai aktual sistem ditarik otomatis dari transaksi dan sales online</span>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle mb-0" id="kpiItemsTable">
                    <thead class="table-light">
                        <tr class="text-nowrap">
                            <th style="width: 40px;">No</th>
                            <th style="min-width: 220px;">Indikator KPI</th>
                            <th style="width: 90px;">Tipe</th>
                            <th style="width: 140px;">Target</th>
                            <th style="width: 130px;">Aktual Sistem</th>
                            <th style="width: 150px;">Aktual Final</th>
                            <th style="width: 110px;">Capaian (%)</th>
                            <th style="width: 110px;">Bobot (%)</th>
                            <th style="width: 100px;">Skor</th>
                            <th style="min-width: 200px;">Catatan / Feedback Evaluator</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($assignment->items as $idx => $item)
                            <tr data-item-id="{{ $item->id }}">
                                <td class="text-center">{{ $idx + 1 }}</td>
                                <td>
                                    <div class="fw-bold">{{ $item->kpi_name }}</div>
                                    <small class="text-muted d-block">Satuan: <code>{{ $item->unit }}</code></small>
                                </td>
                                <td>
                                    <span class="badge bg-label-{{ $item->type_badge_color }} text-capitalize">
                                        {{ $item->kpi_type }}
                                    </span>
                                </td>
                                <td>
                                    <div class="input-group input-group-sm">
                                        <input type="number" step="any" min="0" name="items[{{ $item->id }}][target]" 
                                               class="form-control text-end item-target" 
                                               value="{{ $item->target }}" required>
                                    </div>
                                </td>
                                <td class="text-end bg-light">
                                    @if ($item->actual_system !== null)
                                        <span class="fw-semibold text-muted">
                                            @if ($item->unit == 'IDR')
                                                Rp {{ number_format($item->actual_system, 0, ',', '.') }}
                                            @else
                                                {{ number_format($item->actual_system, 0) }}
                                            @endif
                                        </span>
                                    @else
                                        <span class="text-muted font-italic">-</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="input-group input-group-sm">
                                        <input type="number" step="any" min="0" name="items[{{ $item->id }}][actual_final]" 
                                               class="form-control text-end fw-bold text-primary item-actual" 
                                               value="{{ $item->actual_final }}" required>
                                    </div>
                                </td>
                                <td class="text-end">
                                    <span class="item-achievement fw-semibold">{{ number_format($item->achievement_rate, 1) }}%</span>
                                </td>
                                <td>
                                    <div class="input-group input-group-sm">
                                        <input type="number" step="0.1" min="0" max="100" name="items[{{ $item->id }}][weight]" 
                                               class="form-control text-end fw-semibold item-weight" 
                                               value="{{ $item->weight }}" required>
                                        <span class="input-group-text">%</span>
                                    </div>
                                </td>
                                <td class="text-end fw-bold text-success item-score">
                                    {{ number_format($item->score, 1) }}
                                </td>
                                <td>
                                    <input type="text" name="items[{{ $item->id }}][evaluator_notes]" 
                                           class="form-control form-control-sm" 
                                           placeholder="Tulis catatan..." 
                                           value="{{ $item->evaluator_notes }}">
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light">
                        <tr class="fw-bold">
                            <td colspan="7" class="text-end">TOTAL:</td>
                            <td class="text-end">
                                <span id="total-weight-display">{{ number_format($assignment->items->sum('weight'), 1) }}%</span>
                            </td>
                            <td class="text-end text-primary fs-6" id="total-score-display">
                                {{ number_format($assignment->total_score, 1) }}
                            </td>
                            <td>
                                <span id="weight-alert" class="small text-danger d-none">Total bobot harus tepat 100%!</span>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        {{-- Evaluator General Notes & Status --}}
        <div class="row g-4 mb-4">
            <div class="col-md-8 col-12">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-header bg-light">
                        <h6 class="mb-0 fw-bold"><i class="mdi mdi-comment-text-outline me-2"></i>Catatan &amp; Arahan Evaluator untuk Employee</h6>
                    </div>
                    <div class="card-body">
                        <textarea name="notes" rows="4" class="form-control" placeholder="Tulis ringkasan feedback performa, apresiasi, atau area yang perlu ditingkatkan untuk employee ini...">{{ old('notes', $assignment->notes) }}</textarea>
                    </div>
                </div>
            </div>

            <div class="col-md-4 col-12">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-header bg-light">
                        <h6 class="mb-0 fw-bold"><i class="mdi mdi-cog-outline me-2"></i>Status Evaluasi</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Status Penilaian</label>
                            <select name="status" class="form-select" id="assignmentStatus">
                                <option value="draft" {{ $assignment->status == 'draft' ? 'selected' : '' }}>Draft (Belum Selesai)</option>
                                <option value="review" {{ $assignment->status == 'review' ? 'selected' : '' }}>Under Review (Dalam Peninjauan)</option>
                                <option value="published" {{ $assignment->status == 'published' ? 'selected' : '' }}>Published (Tampil di Rapor Employee)</option>
                            </select>
                        </div>
                        <div class="alert alert-info py-2 px-3 small mb-0">
                            <i class="mdi mdi-information-outline me-1"></i>Status <strong>Published</strong> akan mengunci nilai dan menampilkannya di dashboard rapor employee.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="d-flex justify-content-between align-items-center bg-white p-3 rounded border shadow-sm">
            <a href="{{ route('ecommerce.kpi.index', ['year' => $assignment->period->year, 'month' => $assignment->period->month]) }}" class="btn btn-outline-secondary">
                Batal
            </a>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary px-4">
                    <i class="mdi mdi-content-save me-1"></i>Simpan Penilaian KPI
                </button>
            </div>
        </div>

    </form>

</div>
@endsection

@push('after-script')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const table = document.getElementById('kpiItemsTable');

    function calculateRow(row) {
        const targetInput = row.querySelector('.item-target');
        const actualInput = row.querySelector('.item-actual');
        const weightInput = row.querySelector('.item-weight');
        const achievementSpan = row.querySelector('.item-achievement');
        const scoreTd = row.querySelector('.item-score');

        const target = parseFloat(targetInput.value) || 0;
        const actual = parseFloat(actualInput.value) || 0;
        const weight = parseFloat(weightInput.value) || 0;

        let achievement = 0;
        if (target > 0) {
            achievement = (actual / target) * 100;
            if (achievement > 100) achievement = 100; // Capping 100%
        }

        const score = (achievement * weight) / 100;

        achievementSpan.textContent = achievement.toFixed(1) + '%';
        scoreTd.textContent = score.toFixed(1);

        return { weight, score };
    }

    function calculateAll() {
        let totalWeight = 0;
        let totalScore = 0;

        const rows = table.querySelectorAll('tbody tr');
        rows.forEach(row => {
            const { weight, score } = calculateRow(row);
            totalWeight += weight;
            totalScore += score;
        });

        // Update displays
        document.getElementById('total-weight-display').textContent = totalWeight.toFixed(1) + '%';
        document.getElementById('total-score-display').textContent = totalScore.toFixed(1);
        document.getElementById('header-total-score').innerHTML = totalScore.toFixed(1) + ' <small class="fs-6 text-muted">/ 100</small>';

        // Grade calculation
        let grade = 'D';
        if (totalScore >= 90) grade = 'A';
        else if (totalScore >= 80) grade = 'B';
        else if (totalScore >= 70) grade = 'C';

        const gradeBadge = document.getElementById('header-grade-badge');
        gradeBadge.textContent = grade;
        gradeBadge.className = 'badge fs-5 p-2 px-3 fw-bold bg-label-' + (grade === 'A' ? 'success' : (grade === 'B' ? 'info' : (grade === 'C' ? 'warning' : 'danger')));

        // Check weight validity
        const weightAlert = document.getElementById('weight-alert');
        if (Math.abs(totalWeight - 100.0) > 0.01) {
            weightAlert.classList.remove('d-none');
            document.getElementById('total-weight-display').classList.add('text-danger');
        } else {
            weightAlert.classList.add('d-none');
            document.getElementById('total-weight-display').classList.remove('text-danger');
            document.getElementById('total-weight-display').classList.add('text-success');
        }
    }

    // Attach event listeners to all inputs
    table.querySelectorAll('.item-target, .item-actual, .item-weight').forEach(input => {
        input.addEventListener('input', calculateAll);
    });

    // Initial calculation
    calculateAll();
});
</script>
@endpush
