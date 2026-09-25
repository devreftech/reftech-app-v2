@extends('layouts.sales.app')
@section('title', $bonus->title . ' - HRM')

@push('after-style')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<style>
    .bonus-show-root {
        font-family: 'Inter', system-ui, -apple-system, sans-serif !important;
        letter-spacing: -0.01em;
    }
    .bonus-show-root h1, .bonus-show-root h2, .bonus-show-root h3, .bonus-show-root h4, .bonus-show-root h5, .bonus-show-root h6 {
        font-family: 'Inter', system-ui, -apple-system, sans-serif !important;
        letter-spacing: -0.02em;
    }
    .bonus-summary-card {
        border-radius: 16px;
        border: 1px solid rgba(226, 232, 240, 0.8) !important;
        background: #ffffff;
    }
    .bonus-recipient-row {
        transition: background-color 0.15s ease;
    }
    .bonus-recipient-row:hover {
        background-color: #f8fafc !important;
    }
    .component-badge-add {
        background-color: #ecfdf5;
        color: #059669;
        border: 1px solid rgba(16, 185, 129, 0.2);
    }
    .component-badge-ded {
        background-color: #fef2f2;
        color: #dc2626;
        border: 1px solid rgba(239, 68, 68, 0.2);
    }
    .font-10 { font-size: 10px !important; }
    .font-11 { font-size: 11px !important; }
    .font-12 { font-size: 12px !important; }
    .font-13 { font-size: 13px !important; }
    .font-14 { font-size: 14px !important; }
    .font-15 { font-size: 15px !important; }
</style>
@endpush

@section('content')
<div class="bonus-show-root">
    {{-- ── 1. HEADER & ACTION TOOLBAR ─────────────────────────────────────── --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 text-muted small">
                    <li class="breadcrumb-item">
                        <a href="{{ route('employees.index') }}" class="text-muted">
                            <i class="mdi mdi-account-group-outline me-1"></i>HR Management
                        </a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('hr.bonuses.index') }}" class="text-muted">Bonus Semester</a>
                    </li>
                    <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">{{ $bonus->code }}</li>
                </ol>
            </nav>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <h4 class="fw-bold mb-0 text-dark">
                    {{ $bonus->title }}
                </h4>
                @php
                    $statusBadge = match($bonus->status) {
                        'Paid' => 'success',
                        'Approved' => 'primary',
                        'Confirmed' => 'info',
                        default => 'warning',
                    };
                @endphp
                <span class="badge bg-label-{{ $statusBadge }} rounded-pill px-3 py-1 font-12 fw-bold">
                    <i class="mdi {{ $bonus->status === 'Paid' ? 'mdi-check-all' : ($bonus->status === 'Approved' ? 'mdi-check' : 'mdi-clock-outline') }} me-1"></i>
                    Status: {{ $bonus->status }}
                </span>
                <span class="badge bg-label-dark rounded-pill px-2.5 py-1 font-11 font-monospace">
                    {{ $bonus->code }}
                </span>
            </div>
            <span class="text-muted font-13">
                Periode: <strong>Semester {{ $bonus->semester }} Tahun {{ $bonus->year }}</strong> &bull; Dibuat oleh {{ $bonus->generator?->name ?? 'HR Admin' }}
            </span>
        </div>

        <div class="d-flex align-items-center gap-2 flex-wrap">
            <a href="{{ route('hr.bonuses.index') }}" class="btn btn-label-secondary rounded-3 shadow-xs px-3 py-2 font-13">
                <i class="mdi mdi-arrow-left me-1"></i> Kembali ke Daftar
            </a>

            @if ($bonus->status === 'Draft')
                <form action="{{ route('hr.bonuses.status', $bonus->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="Confirmed">
                    <button type="submit" class="btn btn-label-info rounded-3 shadow-xs px-3.5 py-2 font-13 fw-semibold">
                        <i class="mdi mdi-check me-1"></i> Konfirmasi Batch (Confirmed)
                    </button>
                </form>
            @endif

            @if (in_array($bonus->status, ['Draft', 'Confirmed']))
                <form action="{{ route('hr.bonuses.status', $bonus->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Setujui batch bonus semester ini? Setelah disetujui, batch siap dibukukan ke Finance Expense.');">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="Approved">
                    <button type="submit" class="btn btn-primary rounded-3 shadow-xs px-3.5 py-2 font-13 fw-bold">
                        <i class="mdi mdi-check-decagram-outline me-1"></i> Setujui Batch (Approve)
                    </button>
                </form>
            @endif

            @if ($bonus->status === 'Approved' && !$bonus->expense_id)
                <button type="button" class="btn btn-success rounded-3 shadow-xs px-3.5 py-2 font-13 fw-bold animate__animated animate__pulse animate__infinite" data-bs-toggle="modal" data-bs-target="#modalPostToExpense">
                    <i class="mdi mdi-cash-check me-1.5 fs-6"></i> Posting ke Finance Expense (Bayar)
                </button>
            @endif

            @if ($bonus->status === 'Paid')
                <span class="badge bg-success rounded-pill px-3 py-2 font-12 fw-bold shadow-xs">
                    <i class="mdi mdi-check-all me-1"></i> Lunas / Terbayar ({{ $bonus->expense?->no_expense ?? 'EXP' }})
                </span>
            @endif

            @if ($bonus->status !== 'Paid')
                <button type="button" class="btn btn-outline-primary rounded-3 shadow-xs px-3 py-2 font-13" data-bs-toggle="modal" data-bs-target="#modalAddEmployee">
                    <i class="mdi mdi-account-plus me-1"></i> + Tambah Karyawan
                </button>
            @endif
        </div>
    </div>

    {{-- ── 2. FLASH NOTIFICATIONS ─────────────────────────────────────────── --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-xs mb-4 d-flex align-items-center" role="alert">
            <div class="avatar avatar-xs rounded-circle bg-success text-white d-flex align-items-center justify-content-center me-2.5 flex-shrink-0">
                <i class="mdi mdi-check font-14"></i>
            </div>
            <span class="font-13 fw-semibold">{{ session('success') }}</span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-xs mb-4 d-flex align-items-center" role="alert">
            <div class="avatar avatar-xs rounded-circle bg-danger text-white d-flex align-items-center justify-content-center me-2.5 flex-shrink-0">
                <i class="mdi mdi-alert-circle-outline font-14"></i>
            </div>
            <span class="font-13 fw-semibold">{{ session('error') }}</span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- ── 3. SUMMARY CARDS ───────────────────────────────────────────────── --}}
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card bonus-summary-card p-3.5 h-100">
                <span class="text-muted font-11 text-uppercase fw-bold d-block mb-1">Total Bonus Bersih</span>
                <h3 class="fw-bolder text-primary mb-1 font-monospace" id="headerNetBonus">
                    Rp {{ number_format($bonus->total_net_amount, 0, ',', '.') }}
                </h3>
                <span class="text-muted font-11">Total pengeluaran bonus batch ini</span>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card bonus-summary-card p-3.5 h-100">
                <span class="text-muted font-11 text-uppercase fw-bold d-block mb-1">Total Penambahan (+)</span>
                <h4 class="fw-bolder text-success mb-1 font-monospace" id="headerAddition">
                    + Rp {{ number_format($bonus->total_addition, 0, ',', '.') }}
                </h4>
                <span class="text-muted font-11">Bonus penjualan, KPI &amp; insentif</span>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card bonus-summary-card p-3.5 h-100">
                <span class="text-muted font-11 text-uppercase fw-bold d-block mb-1">Total Pengurangan (-)</span>
                <h4 class="fw-bolder text-danger mb-1 font-monospace" id="headerDeduction">
                    - Rp {{ number_format($bonus->total_deduction, 0, ',', '.') }}
                </h4>
                <span class="text-muted font-11">Penyesuaian / potongan penalti</span>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card bonus-summary-card p-3.5 h-100">
                <span class="text-muted font-11 text-uppercase fw-bold d-block mb-1">Penerima &amp; Voucher Expense</span>
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <h4 class="fw-bolder text-dark mb-0 font-monospace">
                        {{ $bonus->recipients->count() }} <span class="font-13 text-muted fw-normal">Org</span>
                    </h4>
                    @if ($bonus->expense)
                        <span class="badge bg-label-success rounded-pill font-11">
                            <i class="mdi mdi-receipt me-1"></i>{{ $bonus->expense->no_expense }}
                        </span>
                    @else
                        <span class="badge bg-label-secondary rounded-pill font-11">Belum Posting</span>
                    @endif
                </div>
                <span class="text-muted font-11">
                    {{ $bonus->approved_by ? 'Disetujui: ' . ($bonus->approver?->name ?? 'Manajemen') : 'Menunggu Approval' }}
                </span>
            </div>
        </div>
    </div>

    {{-- ── 4. RECIPIENT LIST TABLE ────────────────────────────────────────── --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden" style="border: 1px solid rgba(226, 232, 240, 0.8) !important;">
        <div class="card-header bg-white border-bottom py-3 px-4 d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
            <div>
                <h6 class="card-title mb-0 fw-bold d-flex align-items-center">
                    <i class="mdi mdi-account-cash-outline text-primary me-2"></i> Rincian Komponen Bonus Karyawan
                </h6>
                <span class="text-muted font-12">Setiap karyawan memiliki baris komponen dinamis yang dapat ditambah, diedit namanya, dan diisi nominalnya</span>
            </div>

            <div class="d-flex align-items-center gap-2">
                <input type="text" id="searchRecipientInput" class="form-control form-control-sm rounded-pill font-13" placeholder="🔍 Cari nama / departemen..." style="min-width: 220px;">
            </div>
        </div>

        <div class="table-responsive text-nowrap">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 22%;">Karyawan &amp; Jabatan</th>
                        <th style="width: 46%;">Rincian Komponen Dinamis (Nama &amp; Nilai)</th>
                        <th style="width: 14%;" class="text-end">Total Bonus Bersih</th>
                        <th style="width: 8%;" class="text-center">Status</th>
                        <th style="width: 10%;" class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody id="recipientTableBody">
                    @forelse ($bonus->recipients as $rec)
                        @php
                            $emp = $rec->employee;
                            $uName = $emp?->user?->name ?? ($emp?->nik ? 'Karyawan ' . $emp->nik : 'Karyawan #' . $rec->employee_id);
                            $dept = $emp?->department?->name ?? '-';
                            $pos = $emp?->position?->name ?? ($emp?->user?->role ?? '-');
                            $items = $rec->items;
                        @endphp
                        <tr class="bonus-recipient-row" id="row-recipient-{{ $rec->id }}" data-search="{{ strtolower($uName . ' ' . $dept . ' ' . $pos) }}">
                            <td>
                                <div class="d-flex align-items-center gap-2.5">
                                    <div class="avatar avatar-sm rounded-circle bg-label-primary d-flex align-items-center justify-content-center text-primary fw-bold font-12 flex-shrink-0">
                                        {{ strtoupper(substr($uName, 0, 2)) }}
                                    </div>
                                    <div>
                                        <span class="fw-bold text-dark font-13 d-block">{{ $uName }}</span>
                                        <span class="text-muted font-11">{{ $dept }} &bull; {{ $pos }}</span>
                                        @if ($emp?->salary)
                                            <span class="font-10 text-muted d-block">Gaji Pokok: Rp {{ number_format($emp->salary->basic_salary, 0, ',', '.') }}</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex flex-wrap gap-1.5 align-items-center" id="items-preview-{{ $rec->id }}">
                                    @forelse ($items as $item)
                                        @if ($item->amount > 0)
                                            @if ($item->type === 'addition')
                                                <span class="badge component-badge-add rounded-pill font-11 px-2.5 py-1">
                                                    <i class="mdi mdi-plus me-0.5"></i><strong>{{ $item->name }}</strong>: Rp {{ number_format($item->amount, 0, ',', '.') }}
                                                </span>
                                            @else
                                                <span class="badge component-badge-ded rounded-pill font-11 px-2.5 py-1">
                                                    <i class="mdi mdi-minus me-0.5"></i><strong>{{ $item->name }}</strong>: Rp {{ number_format($item->amount, 0, ',', '.') }}
                                                </span>
                                            @endif
                                        @endif
                                    @empty
                                        <span class="text-muted font-11 fst-italic">Belum ada komponen diisi (Rp 0)</span>
                                    @endforelse

                                    @if ($items->where('amount', '>', 0)->count() == 0)
                                        <span class="text-muted font-11 fst-italic">Belum ada komponen diisi (Rp 0)</span>
                                    @endif
                                </div>
                                @if ($rec->notes)
                                    <span class="font-11 text-muted d-block mt-1">
                                        <i class="mdi mdi-note-text-outline me-1"></i>{{ $rec->notes }}
                                    </span>
                                @endif
                            </td>
                            <td class="text-end">
                                <h6 class="fw-bolder text-primary mb-0 font-monospace font-14" id="net-bonus-display-{{ $rec->id }}">
                                    Rp {{ number_format($rec->net_bonus, 0, ',', '.') }}
                                </h6>
                                <span class="font-10 text-muted d-block" id="subtotal-breakdown-{{ $rec->id }}">
                                    +{{ number_format($rec->total_addition, 0, ',', '.') }} | -{{ number_format($rec->total_deduction, 0, ',', '.') }}
                                </span>
                            </td>
                            <td class="text-center">
                                @if ($rec->payment_status === 'Paid')
                                    <span class="badge bg-label-success rounded-pill font-10 px-2 py-0.5">Paid</span>
                                @else
                                    <span class="badge bg-label-warning rounded-pill font-10 px-2 py-0.5">Draft</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="d-flex align-items-center justify-content-end gap-1">
                                    <button type="button" class="btn btn-sm btn-primary rounded-3 font-12 fw-semibold px-2.5 py-1 btn-edit-recipient" 
                                        data-recipient-id="{{ $rec->id }}" 
                                        data-employee-name="{{ $uName }}"
                                        data-department="{{ $dept }}"
                                        data-notes="{{ $rec->notes }}"
                                        data-items="{{ json_encode($rec->items) }}"
                                        data-is-paid="{{ $bonus->status === 'Paid' ? '1' : '0' }}">
                                        <i class="mdi mdi-pencil me-1"></i> Edit
                                    </button>

                                    <a href="{{ route('hr.bonuses.slip', $rec->id) }}" target="_blank" class="btn btn-sm btn-label-secondary rounded-3 px-2 py-1" title="Lihat & Cetak Slip Bonus">
                                        <i class="mdi mdi-printer font-14"></i>
                                    </a>

                                    @if ($bonus->status !== 'Paid')
                                        <form action="{{ route('hr.bonuses.recipients.destroy', $rec->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus karyawan {{ $uName }} dari batch bonus ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-label-danger rounded-3 px-2 py-1" title="Hapus Karyawan">
                                                <i class="mdi mdi-close font-14"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <p class="mb-2">Belum ada karyawan terdaftar dalam batch bonus ini.</p>
                                <button type="button" class="btn btn-sm btn-primary rounded-3 px-3 py-1.5" data-bs-toggle="modal" data-bs-target="#modalAddEmployee">
                                    <i class="mdi mdi-plus me-1"></i> Tambah Karyawan Sekarang
                                </button>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- ── 5. MODAL KELOLA KOMPONEN DINAMIS KARYAWAN ───────────────────────────── --}}
<div class="modal fade" id="modalEditRecipientItems" tabindex="-1" aria-labelledby="modalEditRecipientItemsLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div style="height: 5px; background: linear-gradient(90deg, #3b82f6 0%, #6366f1 50%, #8b5cf6 100%); width: 100%;"></div>
            
            <div class="modal-header border-bottom py-3 px-4 bg-light d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <div class="avatar avatar-sm rounded-circle bg-label-primary d-flex align-items-center justify-content-center">
                        <i class="mdi mdi-table-edit text-primary fs-5"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-dark font-15 mb-0" id="modalEditRecipientTitle">
                            Kelola Komponen Bonus
                        </h5>
                        <span class="text-muted font-11" id="modalEditRecipientSubtitle">Edit nama item komponen &amp; nominal bonus</span>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="formEditRecipientItems" method="POST">
                @csrf
                <input type="hidden" id="editRecipientId" name="recipient_id">
                
                <div class="modal-body p-4">
                    {{-- Quick Template Presets --}}
                    <div class="mb-3">
                        <label class="form-label font-11 fw-bold text-uppercase text-muted d-block mb-1.5">
                            ⚡ Tambah Cepat Template Komponen:
                        </label>
                        <div class="d-flex flex-wrap gap-1.5">
                            <button type="button" class="btn btn-xs btn-label-primary rounded-pill font-11 px-2.5 py-1 btn-quick-preset" data-name="Bonus Capaian Penjualan" data-type="addition">
                                + Bonus Penjualan
                            </button>
                            <button type="button" class="btn btn-xs btn-label-primary rounded-pill font-11 px-2.5 py-1 btn-quick-preset" data-name="Insentif Evaluasi Kinerja" data-type="addition">
                                + Evaluasi Kinerja
                            </button>
                            <button type="button" class="btn btn-xs btn-label-primary rounded-pill font-11 px-2.5 py-1 btn-quick-preset" data-name="Insentif Kedisiplinan & Presensi" data-type="addition">
                                + Kedisiplinan &amp; Presensi
                            </button>
                            <button type="button" class="btn btn-xs btn-label-success rounded-pill font-11 px-2.5 py-1 btn-quick-preset" data-name="Bonus Milestone Project" data-type="addition">
                                + Bonus Project
                            </button>
                            <button type="button" class="btn btn-xs btn-label-info rounded-pill font-11 px-2.5 py-1 btn-quick-preset" data-name="Penyesuaian Khusus Direksi" data-type="addition">
                                + Reward Khusus
                            </button>
                            <button type="button" class="btn btn-xs btn-label-danger rounded-pill font-11 px-2.5 py-1 btn-quick-preset" data-name="Potongan Penyesuaian" data-type="deduction">
                                - Potongan Penalti
                            </button>
                        </div>
                    </div>

                    {{-- Dynamic Items Container --}}
                    <div class="card border rounded-3 overflow-hidden mb-3">
                        <div class="card-header bg-light py-2 px-3 border-bottom d-flex align-items-center justify-content-between">
                            <span class="font-12 fw-bold text-dark text-uppercase">Daftar Komponen Bonus</span>
                            <button type="button" class="btn btn-xs btn-primary rounded-pill px-2.5 py-1 font-11" id="btnAddNewItemRow">
                                <i class="mdi mdi-plus me-1"></i> Tambah Baris
                            </button>
                        </div>
                        <div class="card-body p-3">
                            <div id="dynamicItemsList" class="d-flex flex-column gap-2.5">
                                {{-- Rows populated via JavaScript --}}
                            </div>
                        </div>
                    </div>

                    {{-- Live Calculation Box --}}
                    <div class="card bg-label-primary border-0 p-3 rounded-3 mb-3">
                        <div class="row align-items-center">
                            <div class="col-4 border-end">
                                <span class="font-11 text-muted text-uppercase d-block">Subtotal Penambahan</span>
                                <h6 class="fw-bold text-success mb-0 font-monospace font-13" id="modalSubtotalAddition">+ Rp 0</h6>
                            </div>
                            <div class="col-4 border-end">
                                <span class="font-11 text-muted text-uppercase d-block">Subtotal Pengurangan</span>
                                <h6 class="fw-bold text-danger mb-0 font-monospace font-13" id="modalSubtotalDeduction">- Rp 0</h6>
                            </div>
                            <div class="col-4">
                                <span class="font-11 text-muted text-uppercase d-block fw-bold text-primary">Total Bonus Bersih</span>
                                <h5 class="fw-bolder text-primary mb-0 font-monospace" id="modalTotalNetBonus">Rp 0</h5>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <label class="form-label font-12 fw-bold text-muted text-uppercase mb-1">Catatan Khusus Karyawan (Opsional)</label>
                        <input type="text" name="notes" id="modalRecipientNotes" class="form-control form-control-sm rounded-3 font-13" placeholder="Catatan atau keterangan perincian...">
                    </div>
                </div>

                <div class="modal-footer bg-light py-2.5 px-4 border-top d-flex justify-content-between">
                    <button type="button" class="btn btn-label-secondary font-12 rounded-3 px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary font-12 fw-bold rounded-3 px-4 shadow-xs" id="btnSaveRecipientItems">
                        <i class="mdi mdi-content-save-outline me-1"></i> Simpan Komponen Bonus
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ── 6. MODAL TAMBAH KARYAWAN ───────────────────────────────────────────── --}}
<div class="modal fade" id="modalAddEmployee" tabindex="-1" aria-labelledby="modalAddEmployeeLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 440px;">
        <form action="{{ route('hr.bonuses.add-employee', $bonus->id) }}" method="POST" class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            @csrf
            <div class="modal-header border-bottom py-3 px-4 bg-white d-flex align-items-center justify-content-between">
                <h5 class="modal-title fw-bold text-dark font-15 mb-0" id="modalAddEmployeeLabel">
                    Tambah Karyawan Penerima
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <label class="form-label font-12 fw-bold text-muted text-uppercase mb-1">Pilih Karyawan <span class="text-danger">*</span></label>
                <select name="employee_id" class="form-select rounded-3 font-13" required>
                    <option value="">- Pilih Karyawan Aktif -</option>
                    @foreach ($availableEmployees as $empOpt)
                        <option value="{{ $empOpt->id }}">
                            {{ $empOpt->user?->name ?? $empOpt->nik }} ({{ $empOpt->department?->name ?? 'HR' }})
                        </option>
                    @endforeach
                </select>
                @if ($availableEmployees->isEmpty())
                    <span class="text-muted font-11 d-block mt-2">Seluruh karyawan aktif sudah terdaftar dalam batch ini.</span>
                @endif
            </div>
            <div class="modal-footer bg-light py-2.5 px-4 border-top d-flex justify-content-between">
                <button type="button" class="btn btn-label-secondary font-12 rounded-3 px-3" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary font-12 fw-bold rounded-3 px-3.5 shadow-xs" @disabled($availableEmployees->isEmpty())>
                    <i class="mdi mdi-plus me-1"></i> Tambahkan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ── 7. MODAL POSTING KE FINANCE EXPENSE ─────────────────────────────────── --}}
@if ($bonus->status === 'Approved' && !$bonus->expense_id)
<div class="modal fade" id="modalPostToExpense" tabindex="-1" aria-labelledby="modalPostToExpenseLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 520px;">
        <form action="{{ route('hr.bonuses.post-expense', $bonus->id) }}" method="POST" class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            @csrf
            <div style="height: 5px; background: linear-gradient(90deg, #10b981 0%, #059669 100%); width: 100%;"></div>
            
            <div class="modal-header border-bottom py-3 px-4 bg-white d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <div class="avatar avatar-sm rounded-circle bg-label-success d-flex align-items-center justify-content-center">
                        <i class="mdi mdi-bank-transfer text-success fs-5"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-dark font-15 mb-0" id="modalPostToExpenseLabel">
                            Posting Pengeluaran ke Finance Expense
                        </h5>
                        <span class="text-muted font-11">Pencatatan voucher pembayaran &amp; pemotongan saldo kas/bank</span>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-4">
                <div class="card bg-label-success border-0 p-3 rounded-3 mb-3 text-center">
                    <span class="font-11 text-muted text-uppercase fw-bold d-block">Total Bonus Yang Akan Dibayarkan</span>
                    <h3 class="fw-bolder text-success mb-0 font-monospace">Rp {{ number_format($bonus->total_net_amount, 0, ',', '.') }}</h3>
                    <span class="text-muted font-11">Untuk {{ $bonus->recipients->count() }} karyawan penerima</span>
                </div>

                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label font-12 fw-bold text-muted text-uppercase mb-1">Pilih Rekening Bank Sumber Dana <span class="text-danger">*</span></label>
                        <select name="id_bank" class="form-select rounded-3 font-13" required>
                            <option value="">- Pilih Akun Kas / Bank -</option>
                            @foreach ($banks as $bank)
                                <option value="{{ $bank->id }}">
                                    {{ $bank->bank }} (Saldo: Rp {{ number_format($bank->saldo, 0, ',', '.') }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12">
                        <label class="form-label font-12 fw-bold text-muted text-uppercase mb-1">Akun Beban / COA Expense <span class="text-danger">*</span></label>
                        <select name="id_account" class="form-select rounded-3 font-13" required>
                            <option value="">- Pilih COA Akun Beban -</option>
                            @foreach ($accounts as $acc)
                                <option value="{{ $acc->id }}" @selected(str_contains(strtolower($acc->account), 'bonus') || str_contains(strtolower($acc->account), 'insentif') || str_contains(strtolower($acc->account), 'gaji'))>
                                    {{ $acc->code }} - {{ $acc->account }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-6">
                        <label class="form-label font-12 fw-bold text-muted text-uppercase mb-1">Tanggal Pembayaran <span class="text-danger">*</span></label>
                        <input type="date" name="payment_date" class="form-control rounded-3 font-13" value="{{ date('Y-m-d') }}" required>
                    </div>

                    <div class="col-6">
                        <label class="form-label font-12 fw-bold text-muted text-uppercase mb-1">Kode Batch</label>
                        <input type="text" class="form-control rounded-3 font-13 bg-light" value="{{ $bonus->code }}" readonly>
                    </div>

                    <div class="col-12">
                        <label class="form-label font-12 fw-bold text-muted text-uppercase mb-1">Memo / Keterangan Voucher</label>
                        <input type="text" name="memo" class="form-control rounded-3 font-13" value="Pembayaran Bonus Semester Karyawan - {{ $bonus->title }} ({{ $bonus->code }})">
                    </div>
                </div>
            </div>

            <div class="modal-footer bg-light py-2.5 px-4 border-top d-flex justify-content-between">
                <button type="button" class="btn btn-label-secondary font-12 rounded-3 px-3" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-success font-12 fw-bold rounded-3 px-4 shadow-xs" onclick="return confirm('Konfirmasi pencatatan pengeluaran bonus ke Finance Expense? Saldo bank akan dipotong secara instan.');">
                    <i class="mdi mdi-check-circle me-1"></i> Proses Posting Expense (Paid)
                </button>
            </div>
        </form>
    </div>
</div>
@endif

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Search Filter
        var searchInput = document.getElementById('searchRecipientInput');
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                var query = this.value.toLowerCase().trim();
                var rows = document.querySelectorAll('.bonus-recipient-row');
                rows.forEach(function(row) {
                    var text = row.getAttribute('data-search') || '';
                    if (!query || text.includes(query)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        }

        // Modal Edit Dynamic Items Logic
        var modalEl = document.getElementById('modalEditRecipientItems');
        var bsModal = modalEl ? new bootstrap.Modal(modalEl) : null;
        var container = document.getElementById('dynamicItemsList');
        var form = document.getElementById('formEditRecipientItems');
        var isPaidMode = false;

        function formatRupiah(num) {
            return new Intl.NumberFormat('id-ID').format(Math.max(0, num));
        }

        function recalculateModalTotals() {
            var totalAdd = 0;
            var totalDed = 0;
            var rows = container.querySelectorAll('.item-row');
            rows.forEach(function(row) {
                var type = row.querySelector('.item-type').value;
                var amount = parseFloat(row.querySelector('.item-amount').value) || 0;
                if (type === 'addition') {
                    totalAdd += amount;
                } else {
                    totalDed += amount;
                }
            });
            var net = Math.max(0, totalAdd - totalDed);

            document.getElementById('modalSubtotalAddition').textContent = '+ Rp ' + formatRupiah(totalAdd);
            document.getElementById('modalSubtotalDeduction').textContent = '- Rp ' + formatRupiah(totalDed);
            document.getElementById('modalTotalNetBonus').textContent = 'Rp ' + formatRupiah(net);
        }

        function createItemRow(id, name, type, amount, notes) {
            var index = container.querySelectorAll('.item-row').length;
            var div = document.createElement('div');
            div.className = 'item-row d-flex align-items-center gap-2 p-2 rounded-3 bg-white border';
            div.innerHTML = `
                <input type="hidden" name="items[${index}][id]" value="${id || ''}">
                <div class="flex-grow-1">
                    <input type="text" name="items[${index}][name]" class="form-control form-control-sm rounded-3 font-12 fw-semibold item-name" value="${name || ''}" placeholder="Nama Komponen (misal: Bonus Penjualan)" required ${isPaidMode ? 'readonly' : ''}>
                </div>
                <div style="width: 140px;">
                    <select name="items[${index}][type]" class="form-select form-select-sm rounded-3 font-12 item-type" ${isPaidMode ? 'disabled' : ''}>
                        <option value="addition" ${type === 'addition' ? 'selected' : ''}>➕ Penambahan</option>
                        <option value="deduction" ${type === 'deduction' ? 'selected' : ''}>➖ Pengurangan</option>
                    </select>
                </div>
                <div style="width: 150px;">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text font-11 px-1.5">Rp</span>
                        <input type="number" step="any" min="0" name="items[${index}][amount]" class="form-control form-control-sm rounded-end font-12 font-monospace item-amount" value="${amount || 0}" required ${isPaidMode ? 'readonly' : ''}>
                    </div>
                </div>
                ${!isPaidMode ? `
                    <button type="button" class="btn btn-xs btn-label-danger rounded-circle p-1 btn-remove-row" title="Hapus Baris">
                        <i class="mdi mdi-close font-14"></i>
                    </button>
                ` : ''}
            `;

            div.querySelector('.item-amount').addEventListener('input', recalculateModalTotals);
            div.querySelector('.item-type').addEventListener('change', recalculateModalTotals);
            
            var removeBtn = div.querySelector('.btn-remove-row');
            if (removeBtn) {
                removeBtn.addEventListener('click', function() {
                    div.remove();
                    recalculateModalTotals();
                });
            }

            container.appendChild(div);
        }

        // Quick Presets Click
        document.querySelectorAll('.btn-quick-preset').forEach(function(btn) {
            btn.addEventListener('click', function() {
                if (isPaidMode) return;
                var pName = this.getAttribute('data-name');
                var pType = this.getAttribute('data-type');
                createItemRow('', pName, pType, 0, '');
                recalculateModalTotals();
            });
        });

        // Add Row Button
        var btnAddRow = document.getElementById('btnAddNewItemRow');
        if (btnAddRow) {
            btnAddRow.addEventListener('click', function() {
                if (isPaidMode) return;
                createItemRow('', '', 'addition', 0, '');
                recalculateModalTotals();
            });
        }

        // Open Edit Modal for a Recipient
        document.querySelectorAll('.btn-edit-recipient').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var recId = this.getAttribute('data-recipient-id');
                var empName = this.getAttribute('data-employee-name');
                var dept = this.getAttribute('data-department');
                var notes = this.getAttribute('data-notes') || '';
                var items = JSON.parse(this.getAttribute('data-items') || '[]');
                isPaidMode = this.getAttribute('data-is-paid') === '1';

                document.getElementById('editRecipientId').value = recId;
                document.getElementById('modalEditRecipientTitle').textContent = 'Komponen Bonus: ' + empName;
                document.getElementById('modalEditRecipientSubtitle').textContent = dept + ' • Edit rincian penambahan & potongan bonus';
                document.getElementById('modalRecipientNotes').value = notes;
                
                var saveBtn = document.getElementById('btnSaveRecipientItems');
                if (saveBtn) saveBtn.style.display = isPaidMode ? 'none' : '';

                container.innerHTML = '';
                if (items && items.length > 0) {
                    items.forEach(function(item) {
                        createItemRow(item.id, item.name, item.type, item.amount, item.notes);
                    });
                } else {
                    createItemRow('', 'Bonus Capaian Penjualan', 'addition', 0, '');
                    createItemRow('', 'Insentif Evaluasi Kinerja', 'addition', 0, '');
                }

                recalculateModalTotals();
                if (bsModal) bsModal.show();
            });
        });

        // Form Submit via AJAX for Instant Live UI Updates
        if (form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                var recId = document.getElementById('editRecipientId').value;
                var saveBtn = document.getElementById('btnSaveRecipientItems');
                var submitUrl = '{{ url("hr/bonuses/recipients") }}/' + recId + '/items';

                var formData = new FormData(form);

                saveBtn.disabled = true;
                saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...';

                fetch(submitUrl, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    body: formData
                })
                .then(function(res) { return res.json(); })
                .then(function(data) {
                    saveBtn.disabled = false;
                    saveBtn.innerHTML = '<i class="mdi mdi-content-save-outline me-1"></i> Simpan Komponen Bonus';

                    if (data && data.success) {
                        if (bsModal) bsModal.hide();

                        // Live Update Recipient Row in Table
                        var rec = data.recipient;
                        var bonusData = data.bonus;

                        // Update badges in row
                        var previewDiv = document.getElementById('items-preview-' + rec.id);
                        if (previewDiv && rec.items) {
                            var badgesHtml = '';
                            var activeCount = 0;
                            rec.items.forEach(function(it) {
                                if (parseFloat(it.amount) > 0) {
                                    activeCount++;
                                    if (it.type === 'addition') {
                                        badgesHtml += '<span class="badge component-badge-add rounded-pill font-11 px-2.5 py-1 me-1 mb-1">' +
                                            '<i class="mdi mdi-plus me-0.5"></i><strong>' + it.name + '</strong>: Rp ' + formatRupiah(it.amount) +
                                            '</span>';
                                    } else {
                                        badgesHtml += '<span class="badge component-badge-ded rounded-pill font-11 px-2.5 py-1 me-1 mb-1">' +
                                            '<i class="mdi mdi-minus me-0.5"></i><strong>' + it.name + '</strong>: Rp ' + formatRupiah(it.amount) +
                                            '</span>';
                                    }
                                }
                            });
                            if (activeCount === 0) {
                                badgesHtml = '<span class="text-muted font-11 fst-italic">Belum ada komponen diisi (Rp 0)</span>';
                            }
                            previewDiv.innerHTML = badgesHtml;
                        }

                        // Update Net Bonus display in row
                        var netEl = document.getElementById('net-bonus-display-' + rec.id);
                        if (netEl) netEl.textContent = 'Rp ' + rec.net_bonus_formatted;

                        var subtotalEl = document.getElementById('subtotal-breakdown-' + rec.id);
                        if (subtotalEl) subtotalEl.textContent = '+' + rec.total_addition_formatted + ' | -' + rec.total_deduction_formatted;

                        // Update data-items in Edit button for next click
                        var editBtn = document.querySelector(`.btn-edit-recipient[data-recipient-id="${rec.id}"]`);
                        if (editBtn) {
                            editBtn.setAttribute('data-items', JSON.stringify(rec.items));
                            editBtn.setAttribute('data-notes', formData.get('notes') || '');
                        }

                        // Update Header Summary Cards
                        if (bonusData) {
                            document.getElementById('headerNetBonus').textContent = 'Rp ' + bonusData.total_net_amount_formatted;
                            document.getElementById('headerAddition').textContent = '+ Rp ' + bonusData.total_addition_formatted;
                            document.getElementById('headerDeduction').textContent = '- Rp ' + bonusData.total_deduction_formatted;
                        }

                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil Disimpan!',
                                text: 'Komponen bonus karyawan telah diperbarui secara realtime.',
                                timer: 1500,
                                showConfirmButton: false
                            });
                        }
                    } else {
                        alert(data.message || 'Gagal menyimpan komponen.');
                    }
                })
                .catch(function(err) {
                    saveBtn.disabled = false;
                    saveBtn.innerHTML = '<i class="mdi mdi-content-save-outline me-1"></i> Simpan Komponen Bonus';
                    console.error('Save error:', err);
                    alert('Terjadi kesalahan saat menyimpan data.');
                });
            });
        }
    });
</script>
@endsection
