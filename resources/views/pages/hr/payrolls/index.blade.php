@extends('layouts.sales.app')
@section('title', 'Payroll & Penggajian Karyawan - HRM')

@section('content')
    {{-- ── TOP HEADER ──────────────────────────────────────────────────── --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 text-muted small">
                    <li class="breadcrumb-item">
                        <a href="{{ route('employees.index') }}" class="text-muted">
                            <i class="mdi mdi-account-group-outline me-1"></i>HR Management
                        </a>
                    </li>
                    <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">Payroll &amp; Penggajian</li>
                </ol>
            </nav>
            <h4 class="fw-bold mb-0 text-heading">
                Payroll &amp; Penggajian Karyawan
            </h4>
        </div>

        <div class="d-flex align-items-center gap-2 flex-wrap">
            <button type="button" class="btn btn-primary shadow-xs" data-bs-toggle="modal" data-bs-target="#modalGeneratePayroll">
                <i class="mdi mdi-calculator me-1"></i> Generate Batch Payroll Baru
            </button>
            <a href="{{ route('employees.index') }}" class="btn btn-label-secondary shadow-xs">
                <i class="mdi mdi-arrow-left me-1"></i> Hub Karyawan
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-xs border-0 mb-4" role="alert">
            <i class="mdi mdi-check-circle-outline me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-xs border-0 mb-4" role="alert">
            <i class="mdi mdi-alert-circle me-1"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- ── STATS ROW ───────────────────────────────────────────────────── --}}
    <div class="row g-3 mb-4">
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-semibold d-block mb-1">Total Periode Payroll {{ $year }}</span>
                        <h3 class="fw-bold text-primary mb-0">{{ $stats['total_periods'] }} Batch</h3>
                        <span class="small text-muted">Periode penggajian bulanan</span>
                    </div>
                    <div class="avatar avatar-md rounded bg-label-primary d-flex align-items-center justify-content-center">
                        <i class="mdi mdi-calendar-check fs-3"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-semibold d-block mb-1">Total Pencairan Gaji Terbayar</span>
                        <h4 class="fw-bold text-success mb-0 font-monospace">Rp {{ number_format($stats['total_paid_disbursement'], 0, ',', '.') }}</h4>
                        <span class="small text-muted">Status: Paid (Sudah Ditransfer)</span>
                    </div>
                    <div class="avatar avatar-md rounded bg-label-success d-flex align-items-center justify-content-center">
                        <i class="mdi mdi-cash-check fs-3"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-semibold d-block mb-1">Menunggu Approval / Konfirmasi</span>
                        <h3 class="fw-bold text-warning mb-0">{{ $stats['pending_approval'] }}</h3>
                        <span class="small text-muted">Draft siap diverifikasi</span>
                    </div>
                    <div class="avatar avatar-md rounded bg-label-warning d-flex align-items-center justify-content-center">
                        <i class="mdi mdi-clock-alert-outline fs-3"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── MAIN TABBED CONTAINER (MATCHING INVOICE PAGE STYLE) ─────────── --}}
    <div class="card">
        <div class="card-header py-2">
            <ul class="nav nav-tabs card-header-tabs border-0 m-0" id="payroll-tab-nav" role="tablist">
                <li class="nav-item">
                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-payroll-periods" type="button">
                        <i class="mdi mdi-file-document-multiple-outline me-1"></i>Daftar Periode Gaji Bulanan
                        <span class="badge rounded-pill bg-primary ms-1" id="badge-payroll-periods">{{ $payrolls->count() }}</span>
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-payroll-salary" type="button">
                        <i class="mdi mdi-account-cash-outline me-1"></i>Master Komponen Gaji Pokok &amp; Tunjangan
                        <span class="badge rounded-pill bg-info ms-1" id="badge-payroll-salary">{{ $employees->count() }}</span>
                    </button>
                </li>
            </ul>
        </div>

        <div class="card-body p-0">
            <div class="tab-content p-0">

                {{-- Tab 1: Periode Gaji Bulanan --}}
                <div class="tab-pane fade show active" id="tab-payroll-periods">
                    <div class="table-responsive">
                        <table id="tbl-payroll-periods" class="table table-bordered table-hover align-middle mb-0 w-100" data-badge="badge-payroll-periods">
                            <thead>
                                <tr>
                                    <th class="text-center">Kode &amp; Periode</th>
                                    <th class="text-center">Rentang Tanggal</th>
                                    <th class="text-center">Tanggal Cair</th>
                                    <th class="text-center">Jumlah Pegawai</th>
                                    <th class="text-center">Total Gaji Pokok</th>
                                    <th class="text-center">Total Net Dibayarkan</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($payrolls as $pay)
                                    @php
                                        $badge = match ($pay->status) {
                                            'Paid' => 'success',
                                            'Approved' => 'primary',
                                            'Confirmed' => 'info',
                                            default => 'warning',
                                        };
                                    @endphp
                                    <tr>
                                        <td>
                                            <div class="fw-bold text-heading">{{ $pay->title }}</div>
                                            <span class="text-muted small font-monospace">{{ $pay->code }}</span>
                                        </td>
                                        <td class="text-center">
                                            <div class="small">
                                                {{ \Carbon\Carbon::parse($pay->start_date)->format('d M') }} - {{ \Carbon\Carbon::parse($pay->end_date)->format('d M Y') }}
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <div class="small fw-semibold">
                                                {{ $pay->payment_date ? \Carbon\Carbon::parse($pay->payment_date)->translatedFormat('d F Y') : '-' }}
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-label-secondary font-monospace">{{ $pay->items_count }} Orang</span>
                                        </td>
                                        <td>
                                            <div class="d-flex justify-content-between font-monospace">
                                                <span>Rp.</span>
                                                <span>{{ number_format($pay->total_basic, 0, ',', '.') }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex justify-content-between font-monospace fw-bold text-heading">
                                                <span>Rp.</span>
                                                <span>{{ number_format($pay->total_net_amount, 0, ',', '.') }}</span>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-label-{{ $badge }}">{{ $pay->status }}</span>
                                            @if ($pay->expense_id)
                                                <div class="mt-1">
                                                    <a href="{{ route('expense.show', $pay->expense_id) }}" class="badge bg-label-dark text-decoration-none" target="_blank" title="Buka Voucher Finance Expense">
                                                        <i class="mdi mdi-receipt-text-outline me-1"></i>{{ $pay->expense?->no_expense ?? 'EXP #' . $pay->expense_id }}
                                                    </a>
                                                </div>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('hr.payrolls.show', $pay->id) }}" class="btn btn-sm btn-label-primary">
                                                <i class="mdi mdi-eye-outline me-1"></i> Buka Batch
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Tab 2: Master Komponen Gaji Pokok & Tunjangan --}}
                <div class="tab-pane fade" id="tab-payroll-salary">
                    {{-- Bulk Action Toolbar --}}
                    <div class="px-3 py-2 bg-label-primary border-bottom d-flex flex-wrap align-items-center justify-content-between gap-2" id="bulkSalaryToolbar" style="display: none !important;">
                        <div class="d-flex align-items-center gap-2">
                            <i class="mdi mdi-checkbox-multiple-marked-outline text-primary fs-5"></i>
                            <span class="fw-bold text-heading small">
                                <span id="selectedCountBadge" class="badge bg-primary rounded-pill font-monospace px-2 py-1 me-1">0</span>
                                Karyawan Terpilih
                            </span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <button type="button" class="btn btn-sm btn-primary shadow-xs" data-bs-toggle="modal" data-bs-target="#modalBulkSalaryAdjustment">
                                <i class="mdi mdi-trending-up me-1"></i> Atur Kenaikan Gaji Massal (Persentase / Nominal)
                            </button>
                            <button type="button" class="btn btn-sm btn-label-secondary" id="btnUncheckAllSalary">
                                Batal
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table id="tbl-payroll-salary" class="table table-bordered table-hover align-middle mb-0 w-100" data-badge="badge-payroll-salary">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width: 38px;">
                                        <input type="checkbox" class="form-check-input" id="check-all-salary" title="Pilih Semua">
                                    </th>
                                    <th class="text-center">Karyawan</th>
                                    <th class="text-center">Gaji Pokok</th>
                                    <th class="text-center">Tunj. Transport</th>
                                    <th class="text-center">Tunj. Makan</th>
                                    <th class="text-center">Tunj. Jabatan</th>
                                    <th class="text-center">BPJS Total</th>
                                    <th class="text-center">Total Gaji Bruto</th>
                                    <th class="text-center">Rekening Bank</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($employees as $emp)
                                    @php
                                        $empName = $emp->user?->name ?? ($emp->nik ? 'Karyawan ' . $emp->nik : 'Karyawan #' . $emp->id);
                                        $sal = $emp->salary;
                                        $bpjs = ($sal?->bpjs_kesehatan ?? 0) + ($sal?->bpjs_ketenagakerjaan ?? 0);
                                    @endphp
                                    <tr>
                                        <td class="text-center">
                                            <input type="checkbox" class="form-check-input check-salary-item" value="{{ $emp->id }}" data-name="{{ $empName }}" data-salary="{{ $sal?->basic_salary ?? 0 }}">
                                        </td>
                                        <td>
                                            <div class="fw-bold text-heading">{{ $empName }}</div>
                                            <div class="text-muted small">{{ $emp->department?->name ?? '-' }} &bull; {{ $emp->position?->name ?? '-' }}</div>
                                        </td>
                                        <td>
                                            <div class="d-flex justify-content-between font-monospace">
                                                <span>Rp.</span>
                                                <span>{{ number_format($sal?->basic_salary ?? 0, 0, ',', '.') }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex justify-content-between font-monospace small">
                                                <span>Rp.</span>
                                                <span>{{ number_format($sal?->transport_allowance ?? 0, 0, ',', '.') }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex justify-content-between font-monospace small">
                                                <span>Rp.</span>
                                                <span>{{ number_format($sal?->meal_allowance ?? 0, 0, ',', '.') }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex justify-content-between font-monospace small">
                                                <span>Rp.</span>
                                                <span>{{ number_format($sal?->position_allowance ?? 0, 0, ',', '.') }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex justify-content-between font-monospace small text-danger">
                                                <span>-Rp.</span>
                                                <span>{{ number_format($bpjs, 0, ',', '.') }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex justify-content-between font-monospace fw-bold text-success">
                                                <span>Rp.</span>
                                                <span>{{ number_format($sal?->total_gross ?? 0, 0, ',', '.') }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="small fw-semibold text-heading">{{ $sal?->bank_name ?? '-' }}</div>
                                            <div class="small font-monospace text-muted">{{ $sal?->bank_account_number ?? '-' }}</div>
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex align-items-center justify-content-center gap-1">
                                                <button type="button" class="btn btn-sm btn-icon btn-label-info shadow-xs" data-bs-toggle="modal" data-bs-target="#modalHistorySalary-{{ $emp->id }}" title="Riwayat Kenaikan Gaji: {{ $empName }}">
                                                    <i class="mdi mdi-history"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-icon btn-label-primary shadow-xs" data-bs-toggle="modal" data-bs-target="#modalEditSalary-{{ $emp->id }}" title="Edit / Naikkan Gaji: {{ $empName }}">
                                                    <i class="mdi mdi-pencil-outline"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>{{-- /tab-content --}}
        </div>
    </div>

    {{-- ── MODAL EDIT GAJI & RIWAYAT (OUTSIDE TABLE TO PREVENT CLIPPING) ── --}}
    @foreach ($employees as $emp)
        @php
            $empName = $emp->user?->name ?? ($emp->nik ? 'Karyawan ' . $emp->nik : 'Karyawan #' . $emp->id);
            $sal = $emp->salary;
        @endphp

        {{-- MODAL 1: EDIT / ATUR KOMPONEN GAJI --}}
        <div class="modal fade" id="modalEditSalary-{{ $emp->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <form action="{{ route('hr.employees.salary', $emp->id) }}" method="POST" class="modal-content border-0 shadow text-start">
                    @csrf
                    <div class="modal-header border-bottom bg-light">
                        <div>
                            <h5 class="modal-title fw-bold mb-0">Atur Komponen Gaji</h5>
                            <small class="text-muted">{{ $empName }} &bull; {{ $emp->department?->name ?? '-' }}</small>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-semibold">Gaji Pokok (IDR) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" name="basic_salary" class="form-control font-monospace format-rupiah" value="{{ number_format($sal?->basic_salary ?? 5500000, 0, ',', '.') }}" placeholder="0" required>
                                </div>
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-semibold">Tunjangan Transport</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" name="transport_allowance" class="form-control font-monospace format-rupiah" value="{{ number_format($sal?->transport_allowance ?? 500000, 0, ',', '.') }}" placeholder="0">
                                </div>
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-semibold">Tunjangan Makan</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" name="meal_allowance" class="form-control font-monospace format-rupiah" value="{{ number_format($sal?->meal_allowance ?? 650000, 0, ',', '.') }}" placeholder="0">
                                </div>
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-semibold">Tunjangan Jabatan</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" name="position_allowance" class="form-control font-monospace format-rupiah" value="{{ number_format($sal?->position_allowance ?? 750000, 0, ',', '.') }}" placeholder="0">
                                </div>
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-semibold">Tunjangan Lainnya</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" name="other_allowance" class="form-control font-monospace format-rupiah" value="{{ number_format($sal?->other_allowance ?? 0, 0, ',', '.') }}" placeholder="0">
                                </div>
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-semibold text-danger">Potongan BPJS Kesehatan</label>
                                <div class="input-group">
                                    <span class="input-group-text text-danger">Rp</span>
                                    <input type="text" name="bpjs_kesehatan" class="form-control font-monospace format-rupiah" value="{{ number_format($sal?->bpjs_kesehatan ?? 55000, 0, ',', '.') }}" placeholder="0">
                                </div>
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-semibold text-danger">Potongan BPJS Ketenagakerjaan</label>
                                <div class="input-group">
                                    <span class="input-group-text text-danger">Rp</span>
                                    <input type="text" name="bpjs_ketenagakerjaan" class="form-control font-monospace format-rupiah" value="{{ number_format($sal?->bpjs_ketenagakerjaan ?? 110000, 0, ',', '.') }}" placeholder="0">
                                </div>
                            </div>
                            <div class="col-4">
                                <label class="form-label fw-semibold">Nama Bank</label>
                                <input type="text" name="bank_name" class="form-control" value="{{ $sal?->bank_name ?? 'BCA' }}" placeholder="BCA / Mandiri">
                            </div>
                            <div class="col-8">
                                <label class="form-label fw-semibold">No. Rekening</label>
                                <input type="text" name="bank_account_number" class="form-control font-monospace" value="{{ $sal?->bank_account_number ?? '' }}">
                            </div>

                            {{-- Parameter Pencatatan Riwayat / Kenaikan --}}
                            <div class="col-12 border-top pt-3 mt-2">
                                <span class="text-muted small fw-bold text-uppercase d-block mb-2">Parameter Riwayat &amp; Kenaikan Gaji</span>
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-semibold">Tanggal Berlaku <span class="text-danger">*</span></label>
                                <input type="date" name="effective_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-semibold">Tipe Perubahan</label>
                                <select name="change_type" class="form-select">
                                    <option value="Kenaikan Tahunan" selected>Kenaikan Tahunan</option>
                                    <option value="Promosi Jabatan">Promosi Jabatan</option>
                                    <option value="Evaluasi Kinerja">Evaluasi Kinerja</option>
                                    <option value="Penyesuaian UMR">Penyesuaian UMR</option>
                                    <option value="Kenaikan Berkala">Kenaikan Berkala</option>
                                    <option value="Penetapan Awal">Penetapan Awal</option>
                                    <option value="Lainnya">Lainnya</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Catatan / Alasan Kenaikan</label>
                                <input type="text" name="notes" class="form-control" placeholder="Contoh: Kenaikan gaji tahunan per evaluasi KPI">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top bg-light">
                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Komponen Gaji</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- MODAL 2: RIWAYAT KENAIKAN GAJI KARYAWAN --}}
        <div class="modal fade" id="modalHistorySalary-{{ $emp->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content border-0 shadow text-start">
                    <div class="modal-header border-bottom bg-light">
                        <div class="d-flex align-items-center gap-2">
                            <div class="avatar avatar-sm rounded bg-label-info d-flex align-items-center justify-content-center">
                                <i class="mdi mdi-history fs-5"></i>
                            </div>
                            <div>
                                <h5 class="modal-title fw-bold mb-0">Riwayat Kenaikan &amp; Perubahan Gaji</h5>
                                <small class="text-muted">{{ $empName }} &bull; {{ $emp->department?->name ?? '-' }} &bull; {{ $emp->position?->name ?? '-' }}</small>
                            </div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        {{-- Current Salary Overview Banner --}}
                        <div class="card bg-label-primary border-0 p-3 mb-4">
                            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2">
                                <div>
                                    <span class="text-muted small fw-semibold text-uppercase">Gaji Pokok &amp; Total Bruto Saat Ini</span>
                                    <div class="d-flex align-items-baseline gap-2 mt-1">
                                        <h4 class="fw-bold text-primary mb-0 font-monospace">Rp {{ number_format($sal?->basic_salary ?? 0, 0, ',', '.') }}</h4>
                                        <span class="badge bg-primary font-monospace">Gross: Rp {{ number_format($sal?->total_gross ?? 0, 0, ',', '.') }}</span>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-sm btn-primary shadow-xs" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#modalEditSalary-{{ $emp->id }}">
                                    <i class="mdi mdi-trending-up me-1"></i> Atur Kenaikan Gaji
                                </button>
                            </div>
                        </div>

                        {{-- Salary Histories Table --}}
                        <h6 class="fw-bold mb-2 text-heading d-flex align-items-center">
                            <i class="mdi mdi-timeline-text-outline text-info me-1"></i> Log Riwayat Kenaikan (Per Tahun / Bulan)
                        </h6>

                        @if ($emp->salaryHistories->count() > 0)
                            <div class="table-responsive border rounded">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Tanggal Berlaku</th>
                                            <th>Tipe / Alasan</th>
                                            <th>Gaji Pokok Baru</th>
                                            <th>Total Bruto</th>
                                            <th>Kenaikan</th>
                                            <th>Diupdate Oleh &amp; Catatan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($emp->salaryHistories as $hist)
                                            @php
                                                $typeBadge = match ($hist->change_type) {
                                                    'Kenaikan Tahunan' => 'primary',
                                                    'Promosi Jabatan' => 'success',
                                                    'Evaluasi Kinerja' => 'info',
                                                    'Penyesuaian UMR' => 'warning',
                                                    'Penetapan Awal' => 'secondary',
                                                    default => 'dark',
                                                };
                                            @endphp
                                            <tr>
                                                <td>
                                                    <div class="fw-bold text-heading">
                                                        {{ \Carbon\Carbon::parse($hist->effective_date)->translatedFormat('d M Y') }}
                                                    </div>
                                                    <small class="text-muted font-monospace">{{ \Carbon\Carbon::parse($hist->effective_date)->diffForHumans() }}</small>
                                                </td>
                                                <td>
                                                    <span class="badge bg-label-{{ $typeBadge }}">{{ $hist->change_type }}</span>
                                                </td>
                                                <td>
                                                    <div class="font-monospace fw-semibold text-heading">
                                                        Rp {{ number_format($hist->basic_salary, 0, ',', '.') }}
                                                    </div>
                                                    @if ($hist->previous_basic_salary > 0)
                                                        <small class="text-muted font-monospace d-block">
                                                            Sebelumnya: Rp {{ number_format($hist->previous_basic_salary, 0, ',', '.') }}
                                                        </small>
                                                    @endif
                                                </td>
                                                <td class="font-monospace fw-bold text-success">
                                                    Rp {{ number_format($hist->total_gross, 0, ',', '.') }}
                                                </td>
                                                <td>
                                                    @if ($hist->increment_amount > 0)
                                                        <span class="badge bg-label-success font-monospace">
                                                            +Rp {{ number_format($hist->increment_amount, 0, ',', '.') }}
                                                            @if ($hist->increment_percentage > 0)
                                                                (+{{ $hist->increment_percentage }}%)
                                                            @endif
                                                        </span>
                                                    @elseif ($hist->increment_amount < 0)
                                                        <span class="badge bg-label-danger font-monospace">
                                                            -Rp {{ number_format(abs($hist->increment_amount), 0, ',', '.') }}
                                                        </span>
                                                    @else
                                                        <span class="badge bg-label-secondary font-monospace">0</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="small fw-semibold text-heading">{{ $hist->creator?->name ?? 'HR System' }}</div>
                                                    @if ($hist->notes)
                                                        <div class="small text-muted fst-italic mt-1" style="max-width: 220px;">
                                                            "{{ $hist->notes }}"
                                                        </div>
                                                    @else
                                                        <span class="text-muted small">-</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4 border rounded bg-light">
                                <div class="avatar avatar-md rounded-circle bg-label-secondary mx-auto mb-2 d-flex align-items-center justify-content-center">
                                    <i class="mdi mdi-history text-secondary fs-4"></i>
                                </div>
                                <h6 class="fw-bold mb-1">Belum Ada Riwayat Perubahan Gaji</h6>
                                <p class="text-muted small mb-0">Setiap kali Anda mengatur atau menaikkan komponen gaji karyawan, riwayat perubahan akan otomatis tercatat di sini.</p>
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer border-top bg-light py-2">
                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    {{-- ── MODAL ATUR KENAIKAN GAJI MASSAL (BULK ADJUSTMENT) ───────────── --}}
    <div class="modal fade" id="modalBulkSalaryAdjustment" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form action="{{ route('hr.employees.salary.bulk') }}" method="POST" id="formBulkSalaryAdjustment" class="modal-content border-0 shadow text-start">
                @csrf
                <div id="bulkSelectedEmployeesInputs"></div>

                <div class="modal-header border-bottom bg-light">
                    <div class="d-flex align-items-center gap-2">
                        <div class="avatar avatar-sm rounded bg-label-primary d-flex align-items-center justify-content-center">
                            <i class="mdi mdi-trending-up fs-5"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold mb-0">Atur Kenaikan Gaji Massal</h5>
                            <small class="text-muted"><span id="bulkModalSelectedCount">0</span> Karyawan Terpilih</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-4">
                    {{-- Selected Employees Summary Banner --}}
                    <div class="alert alert-primary d-flex align-items-center gap-2 mb-3 py-2 px-3">
                        <i class="mdi mdi-information-outline fs-4"></i>
                        <div class="small">
                            Kenaikan akan diterapkan langsung ke Gaji Pokok untuk <strong id="bulkSelectedText">0 Karyawan</strong> terpilih &amp; otomatis tercatat di log riwayat.
                        </div>
                    </div>

                    <div class="row g-3">
                        {{-- Skema Kenaikan: Radio Tab / Button Options --}}
                        <div class="col-12">
                            <label class="form-label fw-bold text-heading">Pilih Skema Kenaikan <span class="text-danger">*</span></label>
                            <div class="row g-2">
                                <div class="col-6">
                                    <div class="form-check custom-option custom-option-basic border rounded p-2 text-center h-100 cursor-pointer" id="optPercentageCard">
                                        <input class="form-check-input d-none" type="radio" name="adjustment_type" id="typePercentage" value="percentage" checked>
                                        <label class="form-check-label w-100 cursor-pointer mb-0" for="typePercentage">
                                            <i class="mdi mdi-percent-outline fs-4 text-primary d-block mb-1"></i>
                                            <span class="fw-bold d-block text-heading">Persentase (%)</span>
                                            <small class="text-muted">Misal +5%, +10%</small>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-check custom-option custom-option-basic border rounded p-2 text-center h-100 cursor-pointer" id="optNominalCard">
                                        <input class="form-check-input d-none" type="radio" name="adjustment_type" id="typeNominal" value="nominal">
                                        <label class="form-check-label w-100 cursor-pointer mb-0" for="typeNominal">
                                            <i class="mdi mdi-cash-multiple fs-4 text-success d-block mb-1"></i>
                                            <span class="fw-bold d-block text-heading">Nominal Rupiah (Rp)</span>
                                            <small class="text-muted">Misal +Rp 500.000</small>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Input Nominal / Persentase --}}
                        <div class="col-12" id="wrapperPercentage">
                            <label class="form-label fw-semibold">Nilai Kenaikan Persentase <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="mdi mdi-trending-up text-primary"></i></span>
                                <input type="number" step="0.1" min="0.1" id="inputPercentageValue" class="form-control" placeholder="Contoh: 10 (untuk 10%)" value="5">
                                <span class="input-group-text fw-bold">%</span>
                            </div>
                            <small class="text-muted">Gaji pokok baru akan dibulatkan otomatis ke ribuan terdekat.</small>
                        </div>

                        <div class="col-12 d-none" id="wrapperNominal">
                            <label class="form-label fw-semibold">Nilai Kenaikan Nominal (Rupiah) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="text" id="inputNominalValue" class="form-control font-monospace format-rupiah" placeholder="Contoh: 500.000" disabled>
                            </div>
                            <small class="text-muted">Nominal akan langsung ditambahkan ke Gaji Pokok saat ini.</small>
                        </div>

                        {{-- Tanggal Berlaku --}}
                        <div class="col-6">
                            <label class="form-label fw-semibold">Tanggal Berlaku <span class="text-danger">*</span></label>
                            <input type="date" name="effective_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>

                        {{-- Tipe Perubahan --}}
                        <div class="col-6">
                            <label class="form-label fw-semibold">Tipe Perubahan <span class="text-danger">*</span></label>
                            <select name="change_type" class="form-select" required>
                                <option value="Kenaikan Tahunan" selected>Kenaikan Tahunan</option>
                                <option value="Evaluasi Kinerja">Evaluasi Kinerja</option>
                                <option value="Penyesuaian UMR">Penyesuaian UMR</option>
                                <option value="Promosi Jabatan">Promosi Jabatan</option>
                                <option value="Kenaikan Berkala">Kenaikan Berkala</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </div>

                        {{-- Catatan / Keterangan --}}
                        <div class="col-12">
                            <label class="form-label fw-semibold">Catatan Internal / Keterangan</label>
                            <input type="text" name="notes" class="form-control" placeholder="Contoh: Kenaikan gaji tahunan per evaluasi performa 2026">
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-top bg-light">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btnSubmitBulkSalary">
                        <i class="mdi mdi-check-all me-1"></i> Terapkan Kenaikan Gaji
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ── MODAL GENERATE PAYROLL BARU ──────────────────────────────────── --}}
    <div class="modal fade" id="modalGeneratePayroll" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 520px;">
            <form action="{{ route('hr.payrolls.store') }}" method="POST" class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                @csrf
                <div style="height: 4px; background: linear-gradient(90deg, #6366f1 0%, #3b82f6 100%);"></div>
                <div class="modal-header border-bottom py-3 px-4 bg-light">
                    <h5 class="modal-title fw-bold text-dark font-16 mb-0 d-flex align-items-center">
                        <i class="mdi mdi-calculator me-2 text-primary fs-4"></i> Generate Batch Payroll Bulanan
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    {{-- Info Banner Dynamic Cutoff Presensi --}}
                    @if (isset($cutoffPeriod))
                    <div class="alert alert-primary border-primary border-opacity-25 bg-label-primary p-3 rounded-3 mb-3">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <span class="font-11 text-uppercase fw-bold text-primary">
                                <i class="mdi mdi-calendar-sync me-1"></i>Siklus Cut-Off Presensi Dinamis
                            </span>
                            <span class="badge bg-primary font-10">Tgl 28 Cut-off</span>
                        </div>
                        <div class="font-12 fw-bold text-dark">
                            {{ $cutoffPeriod['start_date']->translatedFormat('d M Y') }} s/d {{ $cutoffPeriod['end_date']->translatedFormat('d M Y') }}
                        </div>
                        <small class="text-muted font-10 d-block mt-1">
                            *Denda keterlambatan bertingkat, lembur, dan alpa ditarik otomatis dari rentang hari kerja di atas.
                        </small>
                    </div>
                    @endif

                    <div class="row g-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold">Bulan Periode</label>
                            <select name="period_month" class="form-select" required>
                                @for ($m = 1; $m <= 12; $m++)
                                    <option value="{{ $m }}" @selected(date('n') == $m)>
                                        {{ \Carbon\Carbon::createFromDate(null, $m, 1)->translatedFormat('F') }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Tahun</label>
                            <input type="number" name="period_year" class="form-control" value="{{ date('Y') }}" min="2020" max="2035" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Tanggal Rencana Pencairan (Gajian)</label>
                            <input type="date" name="payment_date" class="form-control" value="{{ date('Y-m-28') }}" required>
                            <div class="form-text small">Jadwal gajian reguler Reftech adalah <strong>setiap tanggal 28</strong>.</div>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Catatan Internal Payroll</label>
                            <textarea name="notes" class="form-control" rows="2" placeholder="Catatan opsional periode ini..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top bg-light py-2.5 px-4 d-flex justify-content-between">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4 fw-bold shadow-xs">
                        <i class="mdi mdi-play-circle-outline me-1"></i> Mulai Generate Payroll
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('after-style')
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/animate-css/animate.css">
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-rowgroup-bs5/rowgroup.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.css" />
@endpush

@push('after-script')
    <script src="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.js"></script>
@endpush

@push('script')
    <script>
        $(document).ready(function () {
            // Init DataTable for Payroll Periods
            var dtPayrollPeriods = $('#tbl-payroll-periods').DataTable({
                responsive: true,
                order: [[0, 'desc']],
                lengthMenu: [10, 25, 50, 100],
                displayLength: 10,
                columnDefs: [
                    { targets: 7, orderable: false, searchable: false, className: 'text-center' }
                ],
                language: {
                    search: "Cari Periode / Kode:",
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

            // Init DataTable for Employee Salary Components
            var dtPayrollSalary = $('#tbl-payroll-salary').DataTable({
                responsive: true,
                order: [[1, 'asc']],
                lengthMenu: [10, 25, 50, 100],
                displayLength: 25,
                columnDefs: [
                    { targets: [0, 9], orderable: false, searchable: false, className: 'text-center' }
                ],
                language: {
                    search: "Cari Karyawan / Bank:",
                    searchPlaceholder: "Ketik nama / rekening...",
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

            // Recalculate columns & responsive layout on tab switch (same as invoice page)
            $('#payroll-tab-nav button[data-bs-toggle="tab"]').on('shown.bs.tab', function () {
                $.fn.dataTable.tables({ visible: true, api: true }).columns.adjust().responsive.recalc();
            });

            // Bulk Salary Checkbox Management
            function updateBulkSalaryToolbar() {
                var checkedBoxes = $('#tbl-payroll-salary tbody .check-salary-item:checked');
                var count = checkedBoxes.length;
                var totalVisible = $('#tbl-payroll-salary tbody .check-salary-item').length;

                $('#selectedCountBadge').text(count);
                $('#bulkModalSelectedCount').text(count);
                $('#bulkSelectedText').text(count + ' Karyawan');

                if (count > 0) {
                    $('#bulkSalaryToolbar').slideDown(150);
                } else {
                    $('#bulkSalaryToolbar').slideUp(150);
                }

                if (totalVisible > 0 && count === totalVisible) {
                    $('#check-all-salary').prop('checked', true).prop('indeterminate', false);
                } else if (count > 0) {
                    $('#check-all-salary').prop('checked', false).prop('indeterminate', true);
                } else {
                    $('#check-all-salary').prop('checked', false).prop('indeterminate', false);
                }
            }

            // Check All header toggle
            $('#check-all-salary').on('change', function () {
                var isChecked = $(this).is(':checked');
                var rows = dtPayrollSalary.rows({ search: 'applied' }).nodes();
                $('.check-salary-item', rows).prop('checked', isChecked);
                updateBulkSalaryToolbar();
            });

            // Individual row checkbox toggle
            $(document).on('change', '.check-salary-item', function () {
                updateBulkSalaryToolbar();
            });

            // Cancel / Uncheck all button
            $('#btnUncheckAllSalary').on('click', function () {
                var rows = dtPayrollSalary.rows().nodes();
                $('.check-salary-item', rows).prop('checked', false);
                $('#check-all-salary').prop('checked', false).prop('indeterminate', false);
                updateBulkSalaryToolbar();
            });

            // Redraw / Page change on DataTable -> update check all state
            dtPayrollSalary.on('draw', function () {
                updateBulkSalaryToolbar();
            });

            // Radio options for Bulk Adjustment Type (Percentage vs Nominal)
            $('input[name="adjustment_type"]').on('change', function () {
                var val = $(this).val();
                if (val === 'percentage') {
                    $('#optPercentageCard').addClass('border-primary bg-label-primary');
                    $('#optNominalCard').removeClass('border-primary bg-label-primary');
                    $('#wrapperPercentage').removeClass('d-none');
                    $('#wrapperNominal').addClass('d-none');
                    $('#inputPercentageValue').prop('disabled', false);
                    $('#inputNominalValue').prop('disabled', true);
                } else {
                    $('#optNominalCard').addClass('border-primary bg-label-primary');
                    $('#optPercentageCard').removeClass('border-primary bg-label-primary');
                    $('#wrapperNominal').removeClass('d-none');
                    $('#wrapperPercentage').addClass('d-none');
                    $('#inputNominalValue').prop('disabled', false);
                    $('#inputPercentageValue').prop('disabled', true);
                }
            });

            // Initial highlight for percentage
            $('#optPercentageCard').addClass('border-primary bg-label-primary');

            $('#optPercentageCard, #optNominalCard').on('click', function () {
                $(this).find('input[type="radio"]').prop('checked', true).trigger('change');
            });

            // Modal bulk adjustment show event -> inject selected employee IDs
            $('#modalBulkSalaryAdjustment').on('show.bs.modal', function () {
                var checkedBoxes = $('#tbl-payroll-salary tbody .check-salary-item:checked');
                var container = $('#bulkSelectedEmployeesInputs');
                container.empty();

                if (checkedBoxes.length === 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Pilih Karyawan',
                        text: 'Silakan centang minimal satu karyawan terlebih dahulu pada tabel.',
                        customClass: { confirmButton: 'btn btn-primary' },
                        buttonsStyling: false
                    });
                    return false;
                }

                checkedBoxes.each(function () {
                    var empId = $(this).val();
                    container.append('<input type="hidden" name="employee_ids[]" value="' + empId + '">');
                });
            });

            // Form Submit handler: inject adjustment_value
            $('#formBulkSalaryAdjustment').on('submit', function (e) {
                var type = $('input[name="adjustment_type"]:checked').val();
                var value = type === 'percentage' ? $('#inputPercentageValue').val() : $('#inputNominalValue').val();

                if (!value || parseFloat(value.toString().replace(/[^0-9.]/g, '')) <= 0) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'warning',
                        title: 'Nilai Kenaikan Belum Diisi',
                        text: 'Silakan masukkan nilai kenaikan gaji yang valid.',
                        customClass: { confirmButton: 'btn btn-primary' },
                        buttonsStyling: false
                    });
                    return false;
                }

                // Add or update hidden input for adjustment_value
                $('#formBulkSalaryAdjustment input[name="adjustment_value"]').remove();
                $('#formBulkSalaryAdjustment').append('<input type="hidden" name="adjustment_value" value="' + value + '">');
            });

            // Format nominal input with thousand separators (000.000)
            $(document).on('input keyup', '.format-rupiah', function () {
                var raw = $(this).val().replace(/[^0-9]/g, '');
                if (raw === '') {
                    $(this).val('');
                } else {
                    $(this).val(parseInt(raw, 10).toLocaleString('id-ID'));
                }
            });

            $('[data-bs-toggle="tooltip"]').tooltip();
        });
    </script>
@endpush
