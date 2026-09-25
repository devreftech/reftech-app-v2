@extends('layouts.sales.app')
@section('title', 'Batch Payroll: ' . $payroll->title)

@section('content')
    {{-- ── TOP HEADER & ACTIONS ────────────────────────────────────────── --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 text-muted small">
                    <li class="breadcrumb-item"><a href="{{ route('employees.index') }}" class="text-muted"><i class="mdi mdi-account-group-outline me-1"></i>HR Management</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('hr.payrolls.index') }}" class="text-muted">Payroll</a></li>
                    <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">{{ $payroll->code }}</li>
                </ol>
            </nav>
            <div class="d-flex flex-wrap align-items-center gap-2">
                <h4 class="fw-bold mb-0 text-heading">{{ $payroll->title }}</h4>
                <span class="badge bg-label-secondary font-monospace px-2 py-1">{{ $payroll->code }}</span>
                @php
                    $statusConfig = match ($payroll->status) {
                        'Paid' => ['color' => 'success', 'icon' => 'mdi-check-all', 'label' => 'Sudah Dibayar (Paid)'],
                        'Approved' => ['color' => 'primary', 'icon' => 'mdi-shield-check-outline', 'label' => 'Disetujui (Approved)'],
                        'Confirmed' => ['color' => 'info', 'icon' => 'mdi-check-circle-outline', 'label' => 'Terkonfirmasi (Confirmed)'],
                        default => ['color' => 'warning', 'icon' => 'mdi-file-document-edit-outline', 'label' => 'Draft'],
                    };
                @endphp
                <span class="badge bg-label-{{ $statusConfig['color'] }} px-2 py-1 d-inline-flex align-items-center">
                    <i class="mdi {{ $statusConfig['icon'] }} me-1"></i>{{ $statusConfig['label'] }}
                </span>
            </div>
        </div>

        <div class="d-flex align-items-center gap-2 flex-wrap">
            @if ($payroll->status !== 'Paid')
                <form action="{{ route('hr.payrolls.resync', $payroll->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Sinkronkan ulang data presensi, lembur, dan master gaji terbaru untuk seluruh karyawan pada batch ini?');">
                    @csrf
                    <button type="submit" class="btn btn-label-warning shadow-xs" title="Tarik ulang data presensi, lembur, dan absensi terbaru">
                        <i class="mdi mdi-refresh me-1"></i> Sinkronkan Ulang
                    </button>
                </form>
            @endif

            {{-- Status transition action form --}}
            @if ($payroll->status === 'Draft')
                <form action="{{ route('hr.payrolls.status', $payroll->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="Confirmed">
                    <button type="submit" class="btn btn-info shadow-xs">
                        <i class="mdi mdi-check-circle-outline me-1"></i> Konfirmasi Batch
                    </button>
                </form>
            @elseif ($payroll->status === 'Confirmed')
                <form action="{{ route('hr.payrolls.status', $payroll->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="Approved">
                    <button type="submit" class="btn btn-primary shadow-xs">
                        <i class="mdi mdi-shield-check-outline me-1"></i> Setujui Payroll (Approve)
                    </button>
                </form>
            @endif

            {{-- Integrasi Finance Expense Actions --}}
            @if ($payroll->expense_id)
                <a href="{{ route('expense.show', $payroll->expense_id) }}" class="btn btn-outline-success shadow-xs">
                    <i class="mdi mdi-receipt-text-check me-1"></i> Voucher Finance
                </a>
            @else
                @if (in_array($payroll->status, ['Confirmed', 'Approved', 'Paid']))
                    <button type="button" class="btn btn-dark shadow-xs" data-bs-toggle="modal" data-bs-target="#modalPostExpense">
                        <i class="mdi mdi-bank-transfer me-1"></i> Posting ke Finance
                    </button>
                @endif

                @if ($payroll->status === 'Approved')
                    <form action="{{ route('hr.payrolls.status', $payroll->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Tandai seluruh gaji pada batch ini sudah ditransfer (Paid)?');">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="Paid">
                        <button type="submit" class="btn btn-success shadow-xs">
                            <i class="mdi mdi-cash-check me-1"></i> Tandai Sudah Ditransfer
                        </button>
                    </form>
                @endif
            @endif

            <a href="{{ route('hr.payrolls.index') }}" class="btn btn-label-secondary shadow-xs">
                <i class="mdi mdi-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>

    {{-- ── EXPENSE INTEGRATION BANNER ──────────────────────────────────── --}}
    @if ($payroll->expense_id)
        <div class="card border-0 shadow-sm mb-4 border-start border-success border-4" style="background: linear-gradient(to right, #f0fdf4, #ffffff);">
            <div class="card-body p-3 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-label-success d-flex align-items-center justify-content-center shadow-xs flex-shrink-0" style="width: 48px; height: 48px;">
                        <i class="mdi mdi-receipt-text-check fs-3 text-success"></i>
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2">
                            <h6 class="mb-0 fw-bold text-success">Terintegrasi dengan Finance Expense</h6>
                            <span class="badge bg-success font-monospace" style="font-size: 0.7rem;">TERBUKU</span>
                        </div>
                        <div class="text-muted small mt-1">
                            Voucher No: <strong class="text-dark font-monospace">{{ $payroll->expense?->no_expense }}</strong> &bull;
                            Sumber Bank: <strong>{{ $payroll->expense?->bank?->bank ?? 'Bank' }}</strong> ({{ $payroll->expense?->bank?->no_rek ?? '-' }}) &bull;
                            Tanggal: {{ $payroll->expense?->date ? \Carbon\Carbon::parse($payroll->expense->date)->translatedFormat('d F Y') : '-' }}
                        </div>
                    </div>
                </div>
                <a href="{{ route('expense.show', $payroll->expense_id) }}" class="btn btn-sm btn-success shadow-xs text-nowrap">
                    <i class="mdi mdi-open-in-new me-1"></i> Buka Voucher Finance
                </a>
            </div>
        </div>
    @endif

    {{-- ── ALERTS ──────────────────────────────────────────────────────── --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-xs border-0 mb-4" role="alert">
            <i class="mdi mdi-check-circle-outline me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- ── EXECUTIVE METRICS KPI (4 CARDS) ─────────────────────────────── --}}
    <div class="row g-3 mb-4">
        {{-- Total Gaji Pokok --}}
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3 d-flex flex-column justify-content-between">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small fw-semibold">Total Gaji Pokok</span>
                        <div class="avatar avatar-xs rounded bg-label-primary d-flex align-items-center justify-content-center">
                            <i class="mdi mdi-cash-multiple text-primary fs-6"></i>
                        </div>
                    </div>
                    <div>
                        <h4 class="fw-bold text-heading mb-1 font-monospace">Rp {{ number_format($payroll->total_basic, 0, ',', '.') }}</h4>
                        <div class="d-flex align-items-center text-muted small" style="font-size: 0.78rem;">
                            <i class="mdi mdi-account-group-outline me-1"></i>
                            <span>{{ $payroll->items->count() }} Karyawan terdaftar</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Total Tunjangan & Lembur --}}
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3 d-flex flex-column justify-content-between">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small fw-semibold">Tunjangan &amp; Lembur</span>
                        <div class="avatar avatar-xs rounded bg-label-info d-flex align-items-center justify-content-center">
                            <i class="mdi mdi-trending-up text-info fs-6"></i>
                        </div>
                    </div>
                    <div>
                        <h4 class="fw-bold text-info mb-1 font-monospace">Rp {{ number_format($payroll->total_allowance + $payroll->total_overtime, 0, ',', '.') }}</h4>
                        <div class="d-flex align-items-center text-muted small" style="font-size: 0.78rem;">
                            <span class="badge bg-label-success me-1 font-monospace" style="font-size: 0.7rem;">Lembur: Rp {{ number_format($payroll->total_overtime, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Total Potongan --}}
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3 d-flex flex-column justify-content-between">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small fw-semibold">Total Potongan (BPJS/Absen)</span>
                        <div class="avatar avatar-xs rounded bg-label-danger d-flex align-items-center justify-content-center">
                            <i class="mdi mdi-cash-minus text-danger fs-6"></i>
                        </div>
                    </div>
                    <div>
                        <h4 class="fw-bold text-danger mb-1 font-monospace">-Rp {{ number_format($payroll->total_deductions, 0, ',', '.') }}</h4>
                        <div class="d-flex align-items-center text-muted small" style="font-size: 0.78rem;">
                            <span>BPJS, Absensi &amp; Lainnya</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Total Gaji Bersih (Net) - Hero Card --}}
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100 text-white" style="background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);">
                <div class="card-body p-3 d-flex flex-column justify-content-between">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="small fw-semibold text-white-50">Total Gaji Bersih (Net)</span>
                        <div class="avatar avatar-xs rounded d-flex align-items-center justify-content-center" style="background: rgba(255,255,255,0.2);">
                            <i class="mdi mdi-wallet-outline text-white fs-6"></i>
                        </div>
                    </div>
                    <div>
                        <h4 class="fw-bold text-white mb-1 font-monospace">Rp {{ number_format($payroll->total_net_amount, 0, ',', '.') }}</h4>
                        <div class="small text-white-50" style="font-size: 0.78rem;">
                            <i class="mdi mdi-calendar-check me-1"></i>
                            <span>Cair: {{ $payroll->payment_date ? \Carbon\Carbon::parse($payroll->payment_date)->format('d/m/Y') : 'Belum diatur' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── PAYROLL ITEMS TABLE CARD (MATCHING INVOICE / PAYROLL INDEX STYLE) ── --}}
    <div class="card">
        <div class="card-header border-bottom py-3 d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2">
            <div>
                <h5 class="card-title mb-0 fw-bold d-flex align-items-center text-heading">
                    <i class="mdi mdi-table-account text-primary me-2"></i>
                    Rincian Slip Gaji Karyawan
                </h5>
                <small class="text-muted">Daftar penerimaan gaji individu per karyawan untuk periode batch ini</small>
            </div>
            <span class="badge rounded-pill bg-primary font-monospace px-3 py-1">
                {{ $payroll->items->count() }} Karyawan
            </span>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table id="payrollItemsTable" class="table table-bordered table-hover align-middle mb-0 w-100">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 120px;">No. Slip</th>
                            <th class="text-center" style="min-width: 200px;">Karyawan</th>
                            <th class="text-center" style="min-width: 160px;">Divisi &amp; Posisi</th>
                            <th class="text-center">Gaji Pokok</th>
                            <th class="text-center">Tunjangan</th>
                            <th class="text-center">Lembur</th>
                            <th class="text-center">Potongan</th>
                            <th class="text-center">Gaji Bersih (Net)</th>
                            <th class="text-center">Status</th>
                            <th class="text-center" style="width: 110px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($payroll->items as $item)
                            @php
                                $emp = $item->employee;
                                $user = $emp->user;
                                $empName = $user?->name ?? ($emp->nik ? 'Karyawan ' . $emp->nik : 'Karyawan #' . $emp->id);
                                
                                // Generate Initials
                                $words = array_filter(explode(' ', trim($empName)));
                                $inits = '';
                                foreach (array_slice($words, 0, 2) as $w) {
                                    $inits .= strtoupper(substr($w, 0, 1));
                                }
                                if (empty($inits)) $inits = 'KR';
                                $colorPalette = ['primary', 'success', 'warning', 'info', 'danger'];
                                $paletteColor = $colorPalette[abs(crc32($empName)) % count($colorPalette)];

                                $totDeduction = $item->deduction_absence + $item->deduction_bpjs + $item->deduction_other;
                            @endphp
                            <tr>
                                <td class="text-center">
                                    <span class="badge bg-light text-primary border font-monospace px-2 py-1">
                                        {{ $item->slip_number }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm me-2 flex-shrink-0">
                                            @if ($user?->image && file_exists(public_path($user->image)))
                                                <img src="{{ asset($user->image) }}" alt="{{ $empName }}" class="rounded-circle shadow-xs">
                                            @else
                                                <span class="avatar-initial rounded-circle bg-label-{{ $paletteColor }} fw-bold" style="font-size: 0.75rem;">
                                                    {{ $inits }}
                                                </span>
                                            @endif
                                        </div>
                                        <div>
                                            <div class="fw-bold text-heading text-truncate" style="max-width: 180px;">{{ $empName }}</div>
                                            <small class="text-muted font-monospace">NIK: {{ $emp->nik ?? '-' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="small fw-semibold text-heading">{{ $emp->department?->name ?? 'Tanpa Divisi' }}</div>
                                    <small class="text-muted">{{ $emp->position?->name ?? '-' }}</small>
                                </td>
                                <td>
                                    <div class="d-flex justify-content-between font-monospace small">
                                        <span>Rp.</span>
                                        <span>{{ number_format($item->basic_salary, 0, ',', '.') }}</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex justify-content-between font-monospace small text-info">
                                        <span>+Rp.</span>
                                        <span>{{ number_format($item->total_allowance, 0, ',', '.') }}</span>
                                    </div>
                                </td>
                                <td>
                                    @if ($item->overtime_pay > 0)
                                        <div class="d-flex justify-content-between font-monospace small text-success">
                                            <span>+Rp.</span>
                                            <span>{{ number_format($item->overtime_pay, 0, ',', '.') }}</span>
                                        </div>
                                    @else
                                        <div class="text-center text-muted font-monospace small">-</div>
                                    @endif
                                </td>
                                <td>
                                    @if ($totDeduction > 0)
                                        <div class="d-flex justify-content-between font-monospace small text-danger">
                                            <span>-Rp.</span>
                                            <span>{{ number_format($totDeduction, 0, ',', '.') }}</span>
                                        </div>
                                    @else
                                        <div class="text-center text-muted font-monospace small">Rp 0</div>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex justify-content-between font-monospace fw-bold text-heading">
                                        <span>Rp.</span>
                                        <span>{{ number_format($item->net_salary, 0, ',', '.') }}</span>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-label-{{ $item->payment_status === 'Paid' ? 'success' : 'secondary' }} rounded-pill px-2">
                                        {{ $item->payment_status }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('hr.payrolls.slip', $item->id) }}" target="_blank" class="btn btn-xs btn-label-primary shadow-xs d-inline-flex align-items-center">
                                        <i class="mdi mdi-printer me-1"></i> Slip
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ── MODAL POSTING KE FINANCE EXPENSE ──────────────────────────────── --}}
    @if (!$payroll->expense_id)
        <div class="modal fade" id="modalPostExpense" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <form action="{{ route('hr.payrolls.post-expense', $payroll->id) }}" method="POST">
                        @csrf
                        <div class="modal-header bg-primary text-white py-3">
                            <h5 class="modal-title text-white fw-bold">
                                <i class="mdi mdi-cash-register me-1"></i> Posting Payroll ke Finance Expense
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-4">
                            <div class="alert alert-info py-2 px-3 mb-3 small">
                                <i class="mdi mdi-information-outline me-1"></i>
                                Sistem akan otomatis mencatat pengeluaran kas (Voucher Expense) dan memotong saldo rekening Bank yang dipilih.
                            </div>

                            <div class="card bg-label-primary border-0 p-3 mb-3 text-center">
                                <span class="text-muted small fw-semibold text-uppercase">Total Gaji Bersih Dibukukan</span>
                                <h3 class="fw-bold text-primary mb-0 font-monospace">Rp {{ number_format($payroll->total_net_amount, 0, ',', '.') }}</h3>
                                <small class="text-muted">{{ $payroll->items->count() }} Karyawan &bull; Periode {{ $payroll->title }}</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Pilih Rekening Bank Pencairan <span class="text-danger">*</span></label>
                                <select name="id_bank" class="form-select" required>
                                    <option value="">-- Pilih Bank Sumber --</option>
                                    @foreach ($banks as $b)
                                        <option value="{{ $b->id }}">
                                            {{ $b->bank }} - {{ $b->no_rek }} (Saldo: Rp {{ number_format($b->saldo, 0, ',', '.') }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Akun Beban COA (Chart of Accounts) <span class="text-danger">*</span></label>
                                <select name="id_account" class="form-select" required>
                                    @foreach ($accounts as $acc)
                                        <option value="{{ $acc->id }}" {{ ($acc->code == '6200-002' || str_contains(strtolower($acc->name), 'gaji')) ? 'selected' : '' }}>
                                            {{ $acc->code }} - {{ $acc->name }} ({{ $acc->category }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Tanggal Pembukuan / Transfer <span class="text-danger">*</span></label>
                                <input type="date" name="payment_date" class="form-control" value="{{ $payroll->payment_date ? \Carbon\Carbon::parse($payroll->payment_date)->toDateString() : date('Y-m-d') }}" required>
                            </div>

                            <div class="mb-0">
                                <label class="form-label fw-bold">Catatan / Memo Voucher</label>
                                <textarea name="memo" class="form-control" rows="2">Pembayaran Payroll Gaji Karyawan - {{ $payroll->title }} ({{ $payroll->code }})</textarea>
                            </div>
                        </div>
                        <div class="modal-footer bg-light py-2">
                            <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary" onclick="return confirm('Konfirmasi: Posting batch payroll ini ke Finance Expense dan potong saldo bank?');">
                                <i class="mdi mdi-check-circle-outline me-1"></i> Posting &amp; Potong Saldo Bank
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endsection

@push('after-style')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/animate-css/animate.css">
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-rowgroup-bs5/rowgroup.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.css" />
    <style>
        *, *::before, *::after,
        body,
        h1, h2, h3, h4, h5, h6,
        .h1, .h2, .h3, .h4, .h5, .h6,
        p, span, a, label, input, select, textarea, button,
        table, th, td, tr, thead, tbody,
        .card, .card-title, .card-header, .card-body, .card-footer,
        .breadcrumb, .breadcrumb-item,
        .badge, .btn, .nav, .nav-link, .modal, .modal-title, .modal-body, .modal-footer,
        .dropdown-menu, .dropdown-item,
        .dataTables_wrapper, .dataTables_info, .dataTables_paginate, .paginate_button,
        .form-control, .form-select, .form-label, .form-text,
        .alert, .tooltip, .popover {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif !important;
            letter-spacing: -0.011em;
        }

        .font-monospace,
        code,
        kbd,
        samp {
            font-family: 'SFMono-Regular', Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace !important;
        }
    </style>
@endpush

@push('after-script')
    <script src="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.js"></script>
@endpush

@push('script')
    <script>
        $(document).ready(function () {
            $('#payrollItemsTable').DataTable({
                responsive: true,
                order: [],
                lengthMenu: [10, 25, 50, 100],
                displayLength: 25,
                columnDefs: [
                    { targets: 9, orderable: false, searchable: false, className: 'text-center' }
                ],
                language: {
                    search: "Cari Karyawan / No. Slip:",
                    searchPlaceholder: "Ketik kata kunci...",
                    lengthMenu: "Tampilkan _MENU_ data",
                    info: "Menampilkan _START_ s/d _END_ dari _TOTAL_ data",
                    infoEmpty: "Menampilkan 0 data",
                    infoFiltered: "(disaring dari _MAX_ total data)",
                    zeroRecords: "Data tidak ditemukan",
                    paginate: {
                        first: "Awal",
                        last: "Akhir",
                        next: "Berikutnya",
                        previous: "Sebelumnya"
                    }
                }
            });

            $('[data-bs-toggle="tooltip"]').tooltip();
        });
    </script>
@endpush
