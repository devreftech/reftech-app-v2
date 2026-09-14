@extends('layouts.sales.app')
@section('title', 'Rekonsiliasi Bank (Bank Reconciliation)')
@section('no-container') @endsection
@section('content')
<div class="container-fluid px-4 py-3">
    {{-- Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center py-2 mb-3 gap-3">
        <div>
            <h4 class="fw-bold mb-1">
                <span class="text-muted fw-light">Finance / Kas &amp; Bank /</span> Rekonsiliasi Bank
            </h4>
            <p class="text-muted mb-0 small">
                <i class="mdi mdi-checkbox-marked-circle-auto-outline me-1"></i> Pencocokan mutasi kas &amp; bank di sistem dengan rekening koran asli (Bank Statement)
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('bank.index') }}" class="btn btn-label-secondary btn-sm">
                <i class="mdi mdi-arrow-left me-1"></i> Kembali ke Kas &amp; Bank
            </a>
        </div>
    </div>

    {{-- Filter Card --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('finance.reconciliation.index') }}" class="row g-3 align-items-end">
                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold small">Pilih Akun Bank</label>
                    <select name="bank_id" class="form-select select2" onchange="this.form.submit()">
                        @foreach ($banks as $b)
                            <option value="{{ $b->id }}" {{ $selectedBankId == $b->id ? 'selected' : '' }}>
                                {{ $b->bank }} - {{ $b->no_rek }} ({{ $b->name }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-3">
                    <label class="form-label fw-semibold small">Bulan Periode</label>
                    <select name="month" class="form-select select2" onchange="this.form.submit()">
                        @foreach ($months as $num => $name)
                            <option value="{{ $num }}" {{ $month == $num ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-3">
                    <label class="form-label fw-semibold small">Tahun</label>
                    <select name="year" class="form-select select2" onchange="this.form.submit()">
                        @foreach ($years as $y)
                            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-2 d-grid">
                    <button type="submit" class="btn btn-primary">
                        <i class="mdi mdi-filter-outline me-1"></i> Muat Data
                    </button>
                </div>
            </form>
        </div>
    </div>

    @if ($selectedBank)
        <form id="reconciliation-form" method="POST" action="{{ route('finance.reconciliation.save') }}">
            @csrf
            <input type="hidden" name="id_bank" value="{{ $selectedBank->id }}">
            <input type="hidden" name="period_year" value="{{ $year }}">
            <input type="hidden" name="period_month" value="{{ $month }}">

            {{-- Summary & Reconcile Metrics --}}
            <div class="row g-3 mb-4">
                {{-- Saldo Buku Sistem --}}
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #f8f9ff 0%, #edf0ff 100%); border-left: 5px solid #696cff !important;">
                        <div class="card-body p-3">
                            <span class="text-uppercase fw-bold text-primary small" style="font-size: 11px;">
                                <i class="mdi mdi-book-open-outline me-1"></i> Saldo Buku Sistem
                            </span>
                            <h4 class="fw-bolder text-primary mb-1 mt-2">Rp {{ number_format($selectedBank->saldo, 0, ',', '.') }}</h4>
                            <small class="text-muted" style="font-size: 11px;">Saldo tercatat di sistem per saat ini</small>
                        </div>
                    </div>
                </div>

                {{-- Saldo Rekening Koran (Statement) --}}
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); border-left: 5px solid #22c55e !important;">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <span class="text-uppercase fw-bold text-success small" style="font-size: 11px;">
                                    <i class="mdi mdi-file-document-check-outline me-1"></i> Saldo Rekening Koran
                                </span>
                            </div>
                            <div class="input-group input-group-sm mt-1">
                                <span class="input-group-text bg-white">Rp</span>
                                <input type="number" step="any" id="statement_balance" name="statement_balance" 
                                       class="form-control fw-bold text-success fs-6" 
                                       value="{{ $statementBalance }}" 
                                       placeholder="Ketik saldo akhir koran" required>
                            </div>
                            <small class="text-muted d-block mt-1" style="font-size: 10px;">Saldo akhir menurut rekening koran bank</small>
                        </div>
                    </div>
                </div>

                {{-- Status Selisih (Difference) --}}
                <div class="col-12 col-sm-6 col-xl-3">
                    @php
                        $bookBal = (float) $selectedBank->saldo;
                        $diff = $statementBalance - $bookBal;
                        $isBalanced = abs($diff) < 1;
                    @endphp
                    <div class="card border-0 shadow-sm h-100" id="card-status-diff" style="background: {{ $isBalanced ? 'linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%)' : 'linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%)' }}; border-left: 5px solid {{ $isBalanced ? '#22c55e' : '#ef4444' }} !important;">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <span class="text-uppercase fw-bold small" style="font-size: 11px;">
                                    <i class="mdi mdi-scale-balance me-1"></i> Selisih (Difference)
                                </span>
                                <span class="badge rounded-pill {{ $isBalanced ? 'bg-success' : 'bg-danger' }}" id="badge-reconcile-status">
                                    {{ $isBalanced ? 'Klop (Balanced)' : 'Ada Selisih' }}
                                </span>
                            </div>
                            <h4 class="fw-bolder mb-1 mt-1 {{ $isBalanced ? 'text-success' : 'text-danger' }}" id="text-difference">
                                Rp {{ number_format(abs($diff), 0, ',', '.') }}
                            </h4>
                            <small class="text-muted d-block" id="text-diff-hint" style="font-size: 11px;">
                                {{ $isBalanced ? 'Mutasi sistem klop dengan bank' : 'Perlu pemeriksaan transaksi' }}
                            </small>
                        </div>
                    </div>
                </div>

                {{-- Total Mutasi Periode --}}
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #faf5ff 0%, #f3e8ff 100%); border-left: 5px solid #a855f7 !important;">
                        <div class="card-body p-3">
                            <span class="text-uppercase fw-bold small" style="color: #7e22ce; font-size: 11px;">
                                <i class="mdi mdi-swap-vertical me-1"></i> Mutasi {{ $months[$month] }} {{ $year }}
                            </span>
                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <small class="text-success fw-bold">+ Rp {{ number_format($totalDebit, 0, ',', '.') }}</small>
                                <small class="text-danger fw-bold">- Rp {{ number_format($totalCredit, 0, ',', '.') }}</small>
                            </div>
                            <small class="text-muted d-block mt-1" style="font-size: 11px;">{{ count($mutations) }} Total transaksi mutasi</small>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Table Mutations Checklist --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent border-bottom py-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div>
                        <h6 class="card-title mb-0 fw-bold text-dark">
                            <i class="mdi mdi-checkbox-marked-outline me-2 text-primary"></i> Daftar Mutasi Transaksi Bank
                        </h6>
                        <small class="text-muted">Centang transaksi yang sudah terverifikasi muncul di rekening koran bank</small>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-primary btn-sm" id="btn-check-all">
                            <i class="mdi mdi-check-all me-1"></i> Centang Semua
                        </button>
                        <button type="submit" name="status" value="draft" class="btn btn-label-secondary btn-sm">
                            <i class="mdi mdi-content-save-outline me-1"></i> Simpan Draft
                        </button>
                        <button type="submit" name="status" value="reconciled" class="btn btn-primary btn-sm">
                            <i class="mdi mdi-check-decagram-outline me-1"></i> Selesaikan Rekonsiliasi
                        </button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="font-size: 13px;">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 40px;" class="text-center">Klop</th>
                                <th style="width: 100px;">Tanggal</th>
                                <th style="width: 120px;">Kategori</th>
                                <th style="width: 160px;">No. Referensi</th>
                                <th>Pihak Terkait</th>
                                <th>Keterangan</th>
                                <th class="text-end text-success" style="width: 140px;">Masuk (Debit)</th>
                                <th class="text-end text-danger" style="width: 140px;">Keluar (Kredit)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($mutations as $m)
                                <tr class="{{ $m->is_reconciled ? 'table-success bg-opacity-25' : '' }}">
                                    <td class="text-center">
                                        <input type="checkbox" name="reconciled_items[]" value="{{ $m->key }}" 
                                               class="form-check-input item-check" 
                                               {{ $m->is_reconciled ? 'checked' : '' }}>
                                    </td>
                                    <td>{{ Carbon\Carbon::parse($m->date)->format('d/m/Y') }}</td>
                                    <td>
                                        <span class="badge {{ $m->badge_class }} rounded-pill" style="font-size: 10px;">
                                            {{ $m->category }}
                                        </span>
                                    </td>
                                    <td class="fw-semibold text-dark">{{ $m->ref }}</td>
                                    <td>{{ $m->party }}</td>
                                    <td><small class="text-muted">{{ $m->description }}</small></td>
                                    <td class="text-end text-success fw-semibold">
                                        {{ $m->debit > 0 ? '+ ' . number_format($m->debit, 0, ',', '.') : '-' }}
                                    </td>
                                    <td class="text-end text-danger fw-semibold">
                                        {{ $m->credit > 0 ? '- ' . number_format($m->credit, 0, ',', '.') : '-' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4 text-muted">
                                        Tidak ada transaksi mutasi pada rekening bank ini untuk periode {{ $months[$month] }} {{ $year }}.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot class="table-light fw-bold">
                            <tr>
                                <td colspan="6" class="text-end">TOTAL MUTASI PERIODE INI:</td>
                                <td class="text-end text-success">+ Rp {{ number_format($totalDebit, 0, ',', '.') }}</td>
                                <td class="text-end text-danger">- Rp {{ number_format($totalCredit, 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="card-footer bg-light border-top p-3">
                    <div class="row g-2 align-items-center">
                        <div class="col-12 col-md-8">
                            <input type="text" name="notes" class="form-control form-control-sm" 
                                   value="{{ $notes }}" placeholder="Catatan rekonsiliasi (opsional, misal: biaya admin belum terdebit, dll)">
                        </div>
                        <div class="col-12 col-md-4 text-md-end">
                            <button type="submit" class="btn btn-primary btn-sm px-4">
                                <i class="mdi mdi-check-bold me-1"></i> Simpan Rekonsiliasi
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    @endif
</div>
@endsection

@push('script')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const checkAllBtn = document.getElementById('btn-check-all');
        const checkboxes = document.querySelectorAll('.item-check');
        const statementInput = document.getElementById('statement_balance');
        const bookBalance = {{ $selectedBank ? (float)$selectedBank->saldo : 0 }};
        const textDifference = document.getElementById('text-difference');
        const badgeStatus = document.getElementById('badge-reconcile-status');
        const cardStatus = document.getElementById('card-status-diff');
        const textDiffHint = document.getElementById('text-diff-hint');

        if (checkAllBtn) {
            checkAllBtn.addEventListener('click', function () {
                const allChecked = Array.from(checkboxes).every(c => c.checked);
                checkboxes.forEach(c => {
                    c.checked = !allChecked;
                    const row = c.closest('tr');
                    if (c.checked) {
                        row.classList.add('table-success', 'bg-opacity-25');
                    } else {
                        row.classList.remove('table-success', 'bg-opacity-25');
                    }
                });
            });
        }

        checkboxes.forEach(c => {
            c.addEventListener('change', function () {
                const row = this.closest('tr');
                if (this.checked) {
                    row.classList.add('table-success', 'bg-opacity-25');
                } else {
                    row.classList.remove('table-success', 'bg-opacity-25');
                }
            });
        });

        function updateDiff() {
            if (!statementInput) return;
            const val = parseFloat(statementInput.value) || 0;
            const diff = Math.abs(val - bookBalance);
            const isBalanced = diff < 1;

            if (textDifference) {
                textDifference.textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(diff);
                textDifference.className = 'fw-bolder mb-1 mt-1 ' + (isBalanced ? 'text-success' : 'text-danger');
            }

            if (badgeStatus) {
                badgeStatus.textContent = isBalanced ? 'Klop (Balanced)' : 'Ada Selisih';
                badgeStatus.className = 'badge rounded-pill ' + (isBalanced ? 'bg-success' : 'bg-danger');
            }

            if (cardStatus) {
                cardStatus.style.background = isBalanced 
                    ? 'linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%)' 
                    : 'linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%)';
                cardStatus.style.borderLeft = isBalanced 
                    ? '5px solid #22c55e !important' 
                    : '5px solid #ef4444 !important';
            }

            if (textDiffHint) {
                textDiffHint.textContent = isBalanced 
                    ? 'Mutasi sistem klop dengan bank' 
                    : 'Perlu pemeriksaan transaksi';
            }
        }

        if (statementInput) {
            statementInput.addEventListener('input', updateDiff);
        }
    });
</script>
@endpush
