@extends('layouts.sales.app')
@section('title', 'Bonus & Insentif Semesteran - HRM')

@push('after-style')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<style>
    .bonus-modern-root {
        font-family: 'Inter', system-ui, -apple-system, sans-serif !important;
        letter-spacing: -0.01em;
    }
    .bonus-modern-root h1, .bonus-modern-root h2, .bonus-modern-root h3, .bonus-modern-root h4, .bonus-modern-root h5, .bonus-modern-root h6 {
        font-family: 'Inter', system-ui, -apple-system, sans-serif !important;
        letter-spacing: -0.02em;
    }
    .bonus-kpi-card {
        border-radius: 16px;
        transition: transform 0.2s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.2s ease;
        border: 1px solid rgba(226, 232, 240, 0.8) !important;
        background: #ffffff;
    }
    .bonus-kpi-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.03) !important;
    }
    .bonus-icon-box {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .bonus-table-wrapper {
        border-radius: 16px;
        overflow: hidden;
        border: 1px solid rgba(226, 232, 240, 0.8);
        background: #ffffff;
    }
    .bonus-table thead th {
        background-color: #f8fafc;
        color: #475569;
        font-weight: 700;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        padding: 0.95rem 1rem;
        border-bottom: 1px solid #e2e8f0;
    }
    .bonus-table tbody td {
        padding: 0.9rem 1rem;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.85rem;
    }
    .bonus-table tbody tr:hover td {
        background-color: #f8fafc !important;
    }
    .font-11 { font-size: 11px !important; }
    .font-12 { font-size: 12px !important; }
    .font-13 { font-size: 13px !important; }
    .font-14 { font-size: 14px !important; }
</style>
@endpush

@section('content')
<div class="bonus-modern-root">
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
                    <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">Bonus &amp; Insentif Semester</li>
                </ol>
            </nav>
            <div class="d-flex align-items-center gap-2">
                <h4 class="fw-bold mb-0 text-dark">
                    Bonus &amp; Insentif Semesteran Karyawan
                </h4>
                <span class="badge bg-label-primary rounded-pill px-2.5 py-1 font-11">
                    <i class="mdi mdi-gift-outline me-1"></i>Dynamic Items Engine
                </span>
            </div>
            <span class="text-muted font-13">Pengelolaan alokasi bonus semester (Capaian Penjualan, KPI Kinerja, Kedisiplinan &amp; Insentif Khusus)</span>
        </div>

        <div class="d-flex align-items-center gap-2 flex-wrap">
            <button type="button" class="btn btn-primary rounded-3 shadow-xs px-3.5 py-2 font-13 fw-bold d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#modalNewBonusBatch">
                <i class="mdi mdi-plus-circle me-1.5 fs-6"></i> Buat Batch Bonus Semester
            </button>
            <a href="{{ route('hr.payrolls.index') }}" class="btn btn-label-secondary rounded-3 shadow-xs px-3 py-2 font-13">
                <i class="mdi mdi-cash-multiple me-1"></i> Ke Modul Payroll
            </a>
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

    {{-- ── 3. KPI SUMMARY CARDS ───────────────────────────────────────────── --}}
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card bonus-kpi-card h-100">
                <div class="card-body p-3.5 d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted font-11 text-uppercase fw-bold d-block mb-1">Total Alokasi Bonus ({{ $year }})</span>
                        <h4 class="fw-bolder text-dark mb-0 font-monospace">
                            Rp {{ number_format($stats['total_net_amount'], 0, ',', '.') }}
                        </h4>
                        <span class="text-muted font-11">Dari {{ $stats['total_batches'] }} batch semester</span>
                    </div>
                    <div class="bonus-icon-box bg-label-primary text-primary">
                        <i class="mdi mdi-cash-plus fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card bonus-kpi-card h-100">
                <div class="card-body p-3.5 d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted font-11 text-uppercase fw-bold d-block mb-1">Total Dibayarkan (Paid)</span>
                        <h4 class="fw-bolder text-success mb-0 font-monospace">
                            Rp {{ number_format($stats['total_paid_amount'], 0, ',', '.') }}
                        </h4>
                        <span class="text-muted font-11">Telah dibukukan ke Expense</span>
                    </div>
                    <div class="bonus-icon-box bg-label-success text-success">
                        <i class="mdi mdi-check-decagram-outline fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card bonus-kpi-card h-100">
                <div class="card-body p-3.5 d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted font-11 text-uppercase fw-bold d-block mb-1">Penerima Terdaftar</span>
                        <h4 class="fw-bolder text-info mb-0">
                            {{ $stats['total_recipients'] }} <span class="font-14 text-muted fw-normal">Karyawan</span>
                        </h4>
                        <span class="text-muted font-11">Dari total {{ $activeEmployeesCount }} karyawan aktif</span>
                    </div>
                    <div class="bonus-icon-box bg-label-info text-info">
                        <i class="mdi mdi-account-star-outline fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card bonus-kpi-card h-100">
                <div class="card-body p-3.5 d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted font-11 text-uppercase fw-bold d-block mb-1">Status Integrasi</span>
                        <h5 class="fw-bolder text-dark mb-0 d-flex align-items-center gap-1.5">
                            <i class="mdi mdi-check-circle text-success font-16"></i> Terhubung Expense
                        </h5>
                        <span class="text-muted font-11">Otomatis potong saldo Bank</span>
                    </div>
                    <div class="bonus-icon-box bg-label-warning text-warning">
                        <i class="mdi mdi-bank-transfer fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── 4. FILTER TOOLBAR ──────────────────────────────────────────────── --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4" style="border: 1px solid rgba(226, 232, 240, 0.8) !important;">
        <div class="card-body p-3">
            <form action="{{ route('hr.bonuses.index') }}" method="GET" class="row g-2.5 align-items-center">
                <div class="col-12 col-sm-6 col-md-3">
                    <label class="form-label font-11 fw-bold text-uppercase text-muted mb-1">Tahun Periode</label>
                    <select name="year" class="form-select form-select-sm rounded-3 font-13" onchange="this.form.submit()">
                        @foreach ($availableYears as $yr)
                            <option value="{{ $yr }}" @selected($year == $yr)>Tahun {{ $yr }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 col-sm-6 col-md-3">
                    <label class="form-label font-11 fw-bold text-uppercase text-muted mb-1">Semester</label>
                    <select name="semester" class="form-select form-select-sm rounded-3 font-13" onchange="this.form.submit()">
                        <option value="">Semua Semester</option>
                        <option value="1" @selected($semester === '1')>Semester 1 (Jan - Jun)</option>
                        <option value="2" @selected($semester === '2')>Semester 2 (Jul - Des)</option>
                    </select>
                </div>

                <div class="col-12 col-sm-6 col-md-3">
                    <label class="form-label font-11 fw-bold text-uppercase text-muted mb-1">Status Batch</label>
                    <select name="status" class="form-select form-select-sm rounded-3 font-13" onchange="this.form.submit()">
                        <option value="">Semua Status</option>
                        <option value="Draft" @selected($status === 'Draft')>Draft (Penyusunan)</option>
                        <option value="Confirmed" @selected($status === 'Confirmed')>Confirmed</option>
                        <option value="Approved" @selected($status === 'Approved')>Approved (Disetujui)</option>
                        <option value="Paid" @selected($status === 'Paid')>Paid (Dibukukan ke Expense)</option>
                    </select>
                </div>

                <div class="col-12 col-sm-6 col-md-3 d-flex align-items-end gap-2 pt-2 pt-md-0">
                    <a href="{{ route('hr.bonuses.index') }}" class="btn btn-sm btn-label-secondary rounded-3 font-13 w-100">
                        <i class="mdi mdi-refresh me-1"></i> Reset Filter
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- ── 5. DATA TABLE BATCH BONUS ──────────────────────────────────────── --}}
    <div class="bonus-table-wrapper shadow-xs">
        <div class="table-responsive text-nowrap">
            <table class="table bonus-table mb-0 align-middle">
                <thead>
                    <tr>
                        <th>Batch &amp; Kode</th>
                        <th>Periode Semester</th>
                        <th>Total Karyawan</th>
                        <th>Total Penambahan</th>
                        <th>Total Pengurangan</th>
                        <th>Total Bonus Bersih</th>
                        <th>Status</th>
                        <th>Voucher Expense</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($bonuses as $b)
                        @php
                            $statusBadge = match($b->status) {
                                'Paid' => 'success',
                                'Approved' => 'primary',
                                'Confirmed' => 'info',
                                default => 'warning',
                            };
                            $semesterLabel = $b->semester == 1 ? 'Semester 1 (Jan - Jun)' : 'Semester 2 (Jul - Des)';
                        @endphp
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2.5">
                                    <div class="avatar avatar-sm rounded-3 bg-label-primary d-flex align-items-center justify-content-center text-primary fw-bold font-12">
                                        S{{ $b->semester }}
                                    </div>
                                    <div>
                                        <a href="{{ route('hr.bonuses.show', $b->id) }}" class="fw-bold text-dark font-14 text-decoration-none hover-text-primary">
                                            {{ $b->title }}
                                        </a>
                                        <span class="font-11 text-muted d-block font-monospace">{{ $b->code }}</span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="fw-semibold text-heading font-13">{{ $semesterLabel }}</span>
                                <span class="text-muted font-11 d-block">Tahun {{ $b->year }}</span>
                            </td>
                            <td>
                                <span class="badge bg-label-dark rounded-pill font-11 px-2.5 py-1">
                                    <i class="mdi mdi-account-group me-1"></i>{{ $b->recipients_count }} Karyawan
                                </span>
                            </td>
                            <td>
                                <span class="text-success fw-bold font-monospace font-13">
                                    + Rp {{ number_format($b->total_addition, 0, ',', '.') }}
                                </span>
                            </td>
                            <td>
                                <span class="text-danger fw-semibold font-monospace font-13">
                                    - Rp {{ number_format($b->total_deduction, 0, ',', '.') }}
                                </span>
                            </td>
                            <td>
                                <span class="fw-bolder text-primary font-monospace font-14">
                                    Rp {{ number_format($b->total_net_amount, 0, ',', '.') }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-label-{{ $statusBadge }} rounded-pill font-11 px-2.5 py-1">
                                    <i class="mdi {{ $b->status === 'Paid' ? 'mdi-check-all' : ($b->status === 'Approved' ? 'mdi-check' : 'mdi-clock-outline') }} me-1"></i>
                                    {{ $b->status }}
                                </span>
                                @if ($b->payment_date)
                                    <span class="text-muted font-10 d-block mt-0.5">Tgl: {{ \Carbon\Carbon::parse($b->payment_date)->translatedFormat('d M Y') }}</span>
                                @endif
                            </td>
                            <td>
                                @if ($b->expense)
                                    <span class="badge bg-label-success rounded-pill font-11">
                                        <i class="mdi mdi-receipt me-1"></i>{{ $b->expense->no_expense }}
                                    </span>
                                    <span class="text-muted font-10 d-block">{{ $b->expense->bank?->bank ?? 'Bank' }}</span>
                                @else
                                    <span class="text-muted font-11">-</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="d-flex align-items-center justify-content-end gap-1.5">
                                    <a href="{{ route('hr.bonuses.show', $b->id) }}" class="btn btn-sm btn-primary rounded-3 font-12 fw-semibold px-2.5 py-1" title="Buka Rincian & Kelola Komponen Karyawan">
                                        <i class="mdi mdi-table-edit me-1"></i> Kelola Komponen
                                    </a>
                                    @if ($b->status !== 'Paid')
                                        <form action="{{ route('hr.bonuses.destroy', $b->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus batch bonus ini? Data komponen yang telah diinput akan terhapus.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-label-danger rounded-3 px-2 py-1" title="Hapus Batch">
                                                <i class="mdi mdi-trash-can-outline font-14"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">
                                <div class="avatar avatar-lg rounded-circle bg-label-secondary mx-auto mb-2 d-flex align-items-center justify-content-center">
                                    <i class="mdi mdi-gift-outline fs-3 text-muted"></i>
                                </div>
                                <h6 class="fw-bold mb-1 text-dark">Belum Ada Batch Bonus Semester</h6>
                                <p class="font-12 text-muted mb-3">Klik tombol di bawah untuk membuat batch perhitungan bonus semester baru.</p>
                                <button type="button" class="btn btn-sm btn-primary rounded-3 px-3 py-1.5" data-bs-toggle="modal" data-bs-target="#modalNewBonusBatch">
                                    <i class="mdi mdi-plus me-1"></i> Buat Batch Baru
                                </button>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($bonuses->hasPages())
            <div class="card-footer bg-white border-top py-3 px-4">
                {{ $bonuses->links() }}
            </div>
        @endif
    </div>
</div>

{{-- ── 6. MODAL BUAT BATCH BONUS BARU ─────────────────────────────────────── --}}
<div class="modal fade" id="modalNewBonusBatch" tabindex="-1" aria-labelledby="modalNewBonusBatchLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 520px;">
        <form action="{{ route('hr.bonuses.store') }}" method="POST" class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            @csrf
            <div style="height: 5px; background: linear-gradient(90deg, #6366f1 0%, #3b82f6 50%, #06b6d4 100%); width: 100%;"></div>
            
            <div class="modal-header border-bottom py-3 px-4 bg-white d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <div class="avatar avatar-sm rounded-circle bg-label-primary d-flex align-items-center justify-content-center">
                        <i class="mdi mdi-gift-outline text-primary fs-5"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-dark font-15 mb-0" id="modalNewBonusBatchLabel">
                            Buat Batch Bonus Semester Baru
                        </h5>
                        <span class="text-muted font-11">Inisialisasi tabel komponen perhitungan bonus karyawan</span>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-4">
                <div class="row g-3">
                    <div class="col-6">
                        <label class="form-label font-12 fw-bold text-muted text-uppercase mb-1">Pilih Semester <span class="text-danger">*</span></label>
                        <select name="semester" id="newBatchSemester" class="form-select rounded-3 font-13" required onchange="updateDefaultTitle()">
                            <option value="1" @selected(date('n') <= 6)>Semester 1 (Jan - Jun)</option>
                            <option value="2" @selected(date('n') > 6)>Semester 2 (Jul - Des)</option>
                        </select>
                    </div>

                    <div class="col-6">
                        <label class="form-label font-12 fw-bold text-muted text-uppercase mb-1">Tahun <span class="text-danger">*</span></label>
                        <input type="number" name="year" id="newBatchYear" class="form-control rounded-3 font-13" value="{{ date('Y') }}" min="2020" max="2099" required onchange="updateDefaultTitle()">
                    </div>

                    <div class="col-12">
                        <label class="form-label font-12 fw-bold text-muted text-uppercase mb-1">Judul / Nama Batch <span class="text-danger">*</span></label>
                        <input type="text" name="title" id="newBatchTitle" class="form-control rounded-3 font-13" value="Bonus Kinerja &amp; Penjualan Semester {{ date('n') <= 6 ? '1' : '2' }} {{ date('Y') }}" required placeholder="Contoh: Bonus Kinerja & Penjualan Semester 1 2026">
                    </div>

                    <div class="col-12">
                        <label class="form-label font-12 fw-bold text-muted text-uppercase mb-1">Catatan / Keterangan (Opsional)</label>
                        <textarea name="notes" class="form-control rounded-3 font-13" rows="2" placeholder="Catatan internal manajemen atau memo..."></textarea>
                    </div>

                    <div class="col-12">
                        <div class="card bg-label-primary border-0 p-3 rounded-3">
                            <div class="form-check custom-option custom-option-basic mb-0 p-0 border-0">
                                <label class="form-check-label d-flex align-items-start gap-2 cursor-pointer w-100 mb-0" for="checkIncludeAll">
                                    <input class="form-check-input mt-1" type="checkbox" name="include_all_employees" value="1" id="checkIncludeAll" checked>
                                    <div>
                                        <span class="fw-bold text-dark font-13 d-block">Sertakan Semua Karyawan Aktif ({{ $activeEmployeesCount }} Orang)</span>
                                        <span class="text-muted font-11 d-block">Otomatis memasukkan seluruh staf aktif dengan template komponen default (Bonus Penjualan, Evaluasi Kinerja, Kedisiplinan) siap isi.</span>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer bg-light py-2.5 px-4 border-top d-flex justify-content-between">
                <button type="button" class="btn btn-label-secondary font-12 rounded-3 px-3" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary font-12 fw-bold rounded-3 px-3.5 shadow-xs">
                    <i class="mdi mdi-check me-1"></i> Buat &amp; Buka Perhitungan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function updateDefaultTitle() {
        var sem = document.getElementById('newBatchSemester').value;
        var yr = document.getElementById('newBatchYear').value;
        var titleInput = document.getElementById('newBatchTitle');
        if (titleInput) {
            titleInput.value = 'Bonus Kinerja & Penjualan Semester ' + sem + ' ' + yr;
        }
    }
</script>
@endsection
