@extends('layouts.sales.app')
@section('title', 'Executive Project Cockpit - ' . ($project->company ?? 'Detail Project'))
@section('no-container') @endsection

@section('content')
<div class="container-fluid flex-grow-1 container-p-y px-3 px-md-4">

    <!-- Top Action & Design Switcher Bar -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4 pb-2 border-bottom">
        <div class="d-flex align-items-center flex-wrap gap-2">
            <a href="{{ route('pending-po.sales-order', ['tab' => 'project-monitoring']) }}" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-1 shadow-2xs">
                <i class="mdi mdi-arrow-left"></i> Kembali ke List Project
            </a>
            <span class="badge bg-label-primary px-3 py-1.5 fw-semibold fs-7 rounded-pill">
                <i class="mdi mdi-shield-crown-outline me-1"></i> Executive & Financial Cockpit
            </span>
        </div>

        <div class="d-flex flex-wrap align-items-center gap-2">
            @if ($kanbanTask && $kanbanTask->board_id)
                <a href="{{ route('kanban.boards.show', $kanbanTask->board_id) }}" target="_blank" class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1 shadow-2xs">
                    <i class="mdi mdi-view-week-outline"></i> Kanban Board
                </a>
            @endif

            <button type="button" class="btn btn-sm btn-primary d-inline-flex align-items-center gap-1 shadow-2xs" data-bs-toggle="modal" data-bs-target="#addExpenseModal">
                <i class="mdi mdi-plus-circle-outline"></i> + Catat Pengeluaran
            </button>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <div class="d-flex align-items-center">
                <i class="mdi mdi-check-circle-outline fs-4 me-2"></i>
                <div>{{ session('success') }}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <div class="d-flex align-items-center">
                <i class="mdi mdi-alert-circle-outline fs-4 me-2"></i>
                <div>{{ session('error') }}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Project Identity Card (Clean Modern Header) -->
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 14px; border-left: 5px solid #696cff !important;">
        <div class="card-body p-4">
            <div class="row align-items-center g-3">
                <div class="col-lg-8 col-12">
                    <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                        <span class="badge bg-label-primary text-uppercase px-2.5 py-1 fw-bold fs-7">
                            <i class="mdi mdi-cube-outline me-1"></i> {{ $project->quote_type ?? 'PROJECT' }}
                        </span>

                        @if ($project->status == 6)
                            <span class="badge bg-label-success fw-bold px-2.5 py-1"><i class="mdi mdi-check-circle me-1"></i> Selesai (Completed)</span>
                        @elseif ($project->status == 0)
                            <span class="badge bg-label-secondary fw-bold px-2.5 py-1"><i class="mdi mdi-clock-outline me-1"></i> Order Baru</span>
                        @else
                            <span class="badge bg-label-warning fw-bold px-2.5 py-1"><i class="mdi mdi-progress-wrench me-1"></i> In Progress</span>
                        @endif

                        @if ($kanbanTask && $kanbanTask->column)
                            <span class="badge bg-label-info fw-semibold px-2.5 py-1">
                                <i class="mdi mdi-view-column-outline me-1"></i> Kanban: {{ $kanbanTask->column->title }}
                            </span>
                        @endif

                        <span class="text-muted small ms-1">
                            <i class="mdi mdi-calendar-blank-outline"></i> {{ $project->date ? \Carbon\Carbon::parse($project->date)->format('d M Y') : '-' }}
                        </span>
                    </div>

                    <h3 class="fw-bold text-dark mb-2" style="letter-spacing: -0.5px;">{{ $project->company ?? '-' }}</h3>

                    <div class="d-flex flex-wrap align-items-center gap-3 text-muted small">
                        <div><i class="mdi mdi-barcode text-primary me-1"></i> No Project: <strong class="text-dark">{{ $project->no_pending ?? '-' }}</strong></div>
                        <span class="text-muted-50">•</span>
                        <div><i class="mdi mdi-file-document-outline text-primary me-1"></i> PO: <strong class="text-dark">{{ $project->no_po ?? '-' }}</strong></div>
                        <span class="text-muted-50">•</span>
                        <div>
                            <i class="mdi mdi-file-sign text-primary me-1"></i> Quote:
                            <button type="button"
                                class="btn btn-link p-0 fw-bold text-dark text-decoration-none border-0 align-baseline"
                                data-bs-toggle="modal" data-bs-target="#scopeItemsModal"
                                title="Lihat Lingkup Pekerjaan & Item Kontrak"
                                style="font-size: inherit;">
                                {{ $project->no_quote ?? '-' }}
                                <i class="mdi mdi-open-in-new ms-1 text-primary" style="font-size: 12px;"></i>
                            </button>
                        </div>
                        <span class="text-muted-50">•</span>
                        <div><i class="mdi mdi-account-tie-outline text-primary me-1"></i> Sales: <strong class="text-dark">{{ $project->sales_name ?? '-' }}</strong></div>
                    </div>
                </div>

                <div class="col-lg-4 col-12 text-lg-end text-start">
                    <div class="d-inline-flex flex-column align-items-lg-end align-items-start">
                        <span class="text-muted small text-uppercase fw-bold mb-1" style="font-size: 11px; letter-spacing: 0.5px;">Margin Profitabilitas</span>
                        <div class="d-flex align-items-center gap-2">
                            <span class="display-6 fw-bold {{ $profit >= 0 ? 'text-success' : 'text-danger' }}" style="line-height: 1;">
                                {{ number_format($margin, 1) }}%
                            </span>
                            <span class="badge {{ $profit >= 0 ? 'bg-success' : 'bg-danger' }} rounded-pill px-2.5 py-1 small">
                                <i class="mdi {{ $profit >= 0 ? 'mdi-arrow-top-right' : 'mdi-arrow-bottom-right' }}"></i>
                                {{ $profit >= 0 ? 'Surplus' : 'Defisit' }}
                            </span>
                        </div>
                        <div class="small text-muted mt-1">
                            Laba Bersih: <strong class="{{ $profit >= 0 ? 'text-success' : 'text-danger' }}">Rp {{ number_format($profit, 0, '', '.') }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 4 KPI Performance Cards Strip -->
    <div class="row g-3 mb-4">
        <!-- Revenue Card -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
                <div class="card-body p-3.5">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small fw-bold text-uppercase" style="font-size: 11px; letter-spacing: 0.5px;">Nilai Kontrak / PO</span>
                        <div class="avatar avatar-sm bg-label-primary rounded-circle d-flex align-items-center justify-content-center">
                            <i class="mdi mdi-cash-multiple fs-5"></i>
                        </div>
                    </div>
                    <h4 class="fw-bold text-dark mb-1">Rp {{ number_format($project->revenue, 0, '', '.') }}</h4>
                    <span class="text-muted small" style="font-size: 11.5px;">Revenue bersih sebelum pajak</span>
                </div>
            </div>
        </div>

        <!-- Total HPP Card -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
                <div class="card-body p-3.5">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small fw-bold text-uppercase" style="font-size: 11px; letter-spacing: 0.5px;">Total HPP (Realisasi)</span>
                        <div class="avatar avatar-sm bg-label-danger rounded-circle d-flex align-items-center justify-content-center">
                            <i class="mdi mdi-calculator-variant-outline fs-5"></i>
                        </div>
                    </div>
                    <h4 class="fw-bold text-danger mb-1">Rp {{ number_format($totalCost, 0, '', '.') }}</h4>
                    @php
                        $costBurn = $project->revenue > 0 ? min(100, max(0, ($totalCost / $project->revenue) * 100)) : 0;
                    @endphp
                    <span class="text-muted small" style="font-size: 11.5px;">
                        Burn rate: <strong class="text-danger">{{ number_format($costBurn, 1) }}%</strong> dari kontrak
                    </span>
                </div>
            </div>
        </div>

        <!-- Net Profit Card -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
                <div class="card-body p-3.5">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small fw-bold text-uppercase" style="font-size: 11px; letter-spacing: 0.5px;">Net Profit (Nominal)</span>
                        <div class="avatar avatar-sm {{ $profit >= 0 ? 'bg-label-success' : 'bg-label-danger' }} rounded-circle d-flex align-items-center justify-content-center">
                            <i class="mdi {{ $profit >= 0 ? 'mdi-trending-up' : 'mdi-trending-down' }} fs-5"></i>
                        </div>
                    </div>
                    <h4 class="fw-bold {{ $profit >= 0 ? 'text-success' : 'text-danger' }} mb-1">
                        Rp {{ number_format($profit, 0, '', '.') }}
                    </h4>
                    <span class="text-muted small" style="font-size: 11.5px;">
                        {{ $profit >= 0 ? 'Sisa margin kas surplus' : 'Biaya melebihi kontrak PO' }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Margin & Status Bar Card -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
                <div class="card-body p-3.5">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small fw-bold text-uppercase" style="font-size: 11px; letter-spacing: 0.5px;">Margin Rasio</span>
                        <div class="avatar avatar-sm bg-label-info rounded-circle d-flex align-items-center justify-content-center">
                            <i class="mdi mdi-chart-timeline-variant fs-5"></i>
                        </div>
                    </div>
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <span class="small fw-semibold text-danger">HPP {{ number_format($costBurn, 0) }}%</span>
                        <span class="small fw-semibold text-success">Net {{ number_format(max(0, 100 - $costBurn), 0) }}%</span>
                    </div>
                    <div class="progress rounded-pill mb-1" style="height: 8px; background-color: #e2e8f0;">
                        <div class="progress-bar bg-danger" style="width: {{ $costBurn }}%;"></div>
                        <div class="progress-bar bg-success" style="width: {{ max(0, 100 - $costBurn) }}%;"></div>
                    </div>
                    <span class="text-muted small" style="font-size: 11px;">Kontrol efisiensi anggaran</span>
                </div>
            </div>
        </div>
    </div>

    <!-- ============================================================ -->
    <!-- TAB NAVIGATION -->
    <!-- ============================================================ -->
    <div class="card clean-card border-0 shadow-sm mb-0" style="border-radius: 12px 12px 0 0; border-bottom: none;">
        <div class="card-body p-0">
            <ul class="nav nav-pills nav-pills-custom flex-wrap gap-2 border-0 px-4 py-3" id="projectCockpitTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active d-flex align-items-center gap-2"
                        id="tab-financial-tab"
                        data-bs-toggle="tab"
                        data-bs-target="#tab-financial"
                        type="button" role="tab"
                        aria-controls="tab-financial"
                        aria-selected="true">
                        <i class="mdi mdi-finance"></i>
                        <span>Keuangan &amp; Operasional</span>
                        <span class="badge rounded-pill bg-label-primary">{{ $expenses->count() }}</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link d-flex align-items-center gap-2"
                        id="tab-daily-tab"
                        data-bs-toggle="tab"
                        data-bs-target="#tab-daily"
                        type="button" role="tab"
                        aria-controls="tab-daily"
                        aria-selected="false">
                        <i class="mdi mdi-clipboard-list-outline"></i>
                        <span>Laporan Harian</span>
                        <span class="badge rounded-pill {{ $dailyReports->isEmpty() ? 'bg-label-secondary' : 'bg-label-info' }}">{{ $dailyReports->count() }}</span>
                    </button>
                </li>
            </ul>
        </div>
    </div>

    <!-- ============================================================ -->
    <!-- TAB CONTENT -->
    <!-- ============================================================ -->
    <div class="tab-content" id="projectCockpitTabContent">

        <!-- ======================================================== -->
        <!-- TAB 1: KEUANGAN & OPERASIONAL (Split Cockpit) -->
        <!-- ======================================================== -->
        <div class="tab-pane fade show active" id="tab-financial" role="tabpanel" aria-labelledby="tab-financial-tab">
            <div class="row g-4 pt-4">

                <!-- LEFT COLUMN: MAIN WORKSPACE (8 COLS) -->
                <div class="col-lg-8 col-12">

                    <!-- Card: Real-time Expense Ledger (Pengeluaran Terkoneksi) -->
                    <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
                        <div class="card-header bg-white border-bottom py-3">
                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-2">
                                <div>
                                    <h5 class="card-title m-0 fw-bold text-dark d-flex align-items-center gap-2">
                                        <i class="mdi mdi-book-open-outline text-primary fs-4"></i>
                                        Buku Pengeluaran Operasional Proyek
                                    </h5>
                                    <p class="text-muted small m-0">Terkoneksi langsung dengan kartu Kanban dan data pengeluaran project monitoring</p>
                                </div>
                                <button type="button" class="btn btn-sm btn-primary d-inline-flex align-items-center gap-1 shadow-2xs" data-bs-toggle="modal" data-bs-target="#addExpenseModal">
                                    <i class="mdi mdi-plus"></i> + Catat Pengeluaran
                                </button>
                            </div>

                            <!-- Category Filter Pills -->
                            <div class="d-flex flex-wrap align-items-center gap-1.5 pt-2">
                                <button type="button" class="btn btn-xs btn-primary expense-filter-pill active" data-cat="all">
                                    Semua ({{ $expenses->count() }})
                                </button>
                                <button type="button" class="btn btn-xs btn-outline-secondary expense-filter-pill" data-cat="Material">
                                    Material ({{ $expenses->where('category', 'Material')->count() }})
                                </button>
                                <button type="button" class="btn btn-xs btn-outline-secondary expense-filter-pill" data-cat="Transport">
                                    Transport ({{ $expenses->where('category', 'Transport')->count() }})
                                </button>
                                <button type="button" class="btn btn-xs btn-outline-secondary expense-filter-pill" data-cat="Akomodasi">
                                    Akomodasi ({{ $expenses->where('category', 'Akomodasi')->count() }})
                                </button>
                                <button type="button" class="btn btn-xs btn-outline-secondary expense-filter-pill" data-cat="Konsumsi">
                                    Konsumsi ({{ $expenses->where('category', 'Konsumsi')->count() }})
                                </button>
                                <button type="button" class="btn btn-xs btn-outline-secondary expense-filter-pill" data-cat="Alat">
                                    Alat ({{ $expenses->where('category', 'Alat')->count() }})
                                </button>
                                <button type="button" class="btn btn-xs btn-outline-secondary expense-filter-pill" data-cat="Lain-lain">
                                    Lain-lain ({{ $expenses->where('category', 'Lain-lain')->count() }})
                                </button>
                            </div>
                        </div>

                        <div class="table-responsive text-nowrap">
                            <table class="table table-hover align-middle mb-0" id="tableExpenseLedger" style="font-size: 13px;">
                                <thead class="table-light">
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Deskripsi Pengeluaran</th>
                                        <th>Kategori</th>
                                        <th>Payment Info</th>
                                        <th class="text-end">Nominal (Rp)</th>
                                        <th class="text-center">Nota</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($expenses as $exp)
                                        <tr class="expense-row" data-category="{{ $exp->category }}">
                                            <td class="text-muted fw-semibold">
                                                {{ $exp->date ? \Carbon\Carbon::parse($exp->date)->format('d/m/Y') : '-' }}
                                            </td>
                                            <td>
                                                <div class="fw-semibold text-dark">{{ $exp->name }}</div>
                                                <div class="text-muted small" style="font-size: 11px;">
                                                    Oleh: {{ $exp->user->name ?? 'User #' . $exp->id_user }}
                                                    @if ($exp->id_kanban_task)
                                                        <span class="badge bg-label-info ms-1" style="font-size: 9px;">Kanban</span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                @php
                                                    $catBadges = [
                                                        'Transport'   => 'bg-label-info',
                                                        'Akomodasi'   => 'bg-label-warning',
                                                        'Konsumsi'    => 'bg-label-success',
                                                        'Material'    => 'bg-label-primary',
                                                        'Alat'        => 'bg-label-secondary',
                                                        'Lain-lain'   => 'bg-label-dark',
                                                    ];
                                                    $bClass = $catBadges[$exp->category] ?? 'bg-label-secondary';
                                                @endphp
                                                <span class="badge {{ $bClass }} px-2 py-1">
                                                    {{ $exp->category }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="text-muted small text-truncate d-inline-block" style="max-width: 140px;" title="{{ $exp->payment_info ?? '-' }}">
                                                    {{ $exp->payment_info ?: '-' }}
                                                </span>
                                            </td>
                                            <td class="text-end fw-bold text-danger">
                                                Rp {{ number_format($exp->amount, 0, '', '.') }}
                                            </td>
                                            <td class="text-center">
                                                @if ($exp->receipt)
                                                    @php
                                                        $rUrl = asset($exp->receipt);
                                                        $isPdf = Str::endsWith(strtolower($exp->receipt), '.pdf');
                                                    @endphp
                                                    <a href="{{ $rUrl }}" target="_blank" class="btn btn-xs btn-outline-primary rounded-pill px-2 py-0.5" title="Lihat Bukti">
                                                        <i class="mdi {{ $isPdf ? 'mdi-file-pdf-box' : 'mdi-image-outline' }} me-0.5"></i> Nota
                                                    </a>
                                                @else
                                                    <span class="text-muted small" style="font-size: 11px;">-</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <div class="d-flex align-items-center justify-content-center gap-1">
                                                    <button type="button" class="btn btn-icon btn-xs btn-outline-warning rounded-circle" data-bs-toggle="modal" data-bs-target="#editExpenseModal{{ $exp->id }}" title="Edit Biaya">
                                                        <i class="mdi mdi-pencil-outline"></i>
                                                    </button>
                                                    <form action="{{ route('project-monitoring.destroy-expense', $exp->id) }}" method="post" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengeluaran ini?');" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-icon btn-xs btn-outline-danger rounded-circle" title="Hapus Biaya">
                                                            <i class="mdi mdi-delete-outline"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr id="noExpenseRow">
                                            <td colspan="7" class="text-center py-4 text-muted">
                                                <i class="mdi mdi-receipt-text-outline fs-2 d-block mb-1 opacity-50"></i>
                                                Belum ada pengeluaran operasional yang dicatat.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                @if ($expenses->isNotEmpty())
                                    <tfoot class="table-light fw-bold">
                                        <tr>
                                            <td colspan="4" class="text-end text-uppercase">Total Pengeluaran Lapangan:</td>
                                            <td class="text-end text-danger fs-6">
                                                Rp {{ number_format($expenses->sum('amount'), 0, '', '.') }}
                                            </td>
                                            <td colspan="2"></td>
                                        </tr>
                                    </tfoot>
                                @endif
                            </table>
                        </div>
                    </div>

                    <!-- Card: Material Purchases (Approved Purchase Requests) -->
                    <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                        <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="card-title m-0 fw-bold text-dark d-flex align-items-center gap-2">
                                    <i class="mdi mdi-cart-check text-success fs-5"></i>
                                    Pembelian Material PR Approved (Procurement)
                                </h6>
                                <p class="text-muted small m-0">Item material resmi yang disetujui lewat modul Purchase Request</p>
                            </div>
                            <span class="badge bg-label-success px-2.5 py-1">
                                {{ $purchases->where('status', '3')->count() }} PR Approved
                            </span>
                        </div>

                        <div class="table-responsive text-nowrap">
                            <table class="table table-hover align-middle mb-0" style="font-size: 13px;">
                                <thead class="table-light">
                                    <tr>
                                        <th>No PR</th>
                                        <th>Deskripsi Item Material</th>
                                        <th class="text-center">Qty</th>
                                        <th class="text-end">Nominal (Rp)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $hasPr = false; @endphp
                                    @foreach ($purchases->where('status', '3') as $pr)
                                        @foreach ($pr->details as $dPr)
                                            @php $hasPr = true; @endphp
                                            <tr>
                                                <td class="fw-semibold text-primary">
                                                    {{ $pr->no_pr ?? '#' . $pr->id }}
                                                </td>
                                                <td>
                                                    <div class="fw-semibold text-dark">{{ $dPr->equivalent->product->name ?? $dPr->item }}</div>
                                                    <div class="text-muted small" style="font-size: 11px;">Supplier: {{ $pr->supplier->name ?? '-' }}</div>
                                                </td>
                                                <td class="text-center fw-bold">{{ $dPr->qty }}</td>
                                                <td class="text-end fw-semibold text-danger">
                                                    Rp {{ number_format($dPr->amount, 0, '', '.') }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endforeach

                                    @if (!$hasPr)
                                        <tr>
                                            <td colspan="4" class="text-center py-3 text-muted">
                                                Tidak ada pengeluaran PR material yang disetujui.
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div> <!-- closes col-lg-8 -->

                <!-- RIGHT COLUMN: STICKY AUDIT & KANBAN SIDEBAR (4 COLS) -->
                <div class="col-lg-4 col-12">

                    <!-- Card: Financial Health & Profit Breakdown -->
                    <div class="card w-100 border-0 shadow-sm mb-4 overflow-hidden" style="border-radius: 14px;">
                        <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
                            <h6 class="card-title m-0 fw-bold text-dark d-flex align-items-center gap-2">
                                <i class="mdi mdi-shield-check-outline text-primary fs-5"></i>
                                Ringkasan Kesehatan Finansial
                            </h6>
                            @if ($profit >= 0)
                                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-1 small fw-semibold">
                                    <i class="mdi mdi-check-circle-outline me-0.5"></i> On Track
                                </span>
                            @else
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2.5 py-1 small fw-semibold">
                                    <i class="mdi mdi-alert-circle-outline me-0.5"></i> Defisit
                                </span>
                            @endif
                        </div>
                        <div class="card-body p-3.5">
                            <!-- Progress / Burn Rate Dial Box -->
                            <div class="p-3 rounded-3 mb-3" style="background: #f8fafc; border: 1px solid #edf2f7;">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="text-muted small fw-semibold text-uppercase" style="font-size: 11px; letter-spacing: 0.5px;">Realisasi HPP vs Kontrak</span>
                                    <span class="fw-bold fs-6 {{ $costBurn > 100 ? 'text-danger' : ($costBurn > 80 ? 'text-warning' : 'text-primary') }}">
                                        {{ number_format($costBurn, 1) }}%
                                    </span>
                                </div>
                                <div class="progress rounded-pill mb-2" style="height: 8px; background-color: #e2e8f0;">
                                    <div class="progress-bar {{ $costBurn > 100 ? 'bg-danger' : ($costBurn > 80 ? 'bg-warning' : 'bg-primary') }}" role="progressbar" style="width: {{ min(100, $costBurn) }}%;"></div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center text-muted small" style="font-size: 11px;">
                                    <span>HPP: <strong class="text-dark">Rp {{ number_format($totalCost, 0, '', '.') }}</strong></span>
                                    <span>Margin: <strong class="{{ $profit >= 0 ? 'text-success' : 'text-danger' }}">Rp {{ number_format($profit, 0, '', '.') }}</strong></span>
                                </div>
                            </div>

                            <!-- Clean Metric Rows -->
                            <div class="d-flex flex-column gap-2 mb-3">
                                <div class="d-flex align-items-center justify-content-between p-2 rounded-2 border border-light-subtle bg-white">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="avatar avatar-xs bg-label-danger rounded-circle d-flex align-items-center justify-content-center" style="width: 28px; height: 28px;">
                                            <i class="mdi mdi-package-variant-closed" style="font-size: 14px;"></i>
                                        </div>
                                        <span class="text-secondary small fw-semibold">Biaya Material</span>
                                    </div>
                                    <strong class="text-danger" style="font-size: 13.5px;">Rp {{ number_format($materialCost, 0, '', '.') }}</strong>
                                </div>

                                <div class="d-flex align-items-center justify-content-between p-2 rounded-2 border border-light-subtle bg-white">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="avatar avatar-xs bg-label-warning rounded-circle d-flex align-items-center justify-content-center" style="width: 28px; height: 28px;">
                                            <i class="mdi mdi-gas-station-outline" style="font-size: 14px;"></i>
                                        </div>
                                        <span class="text-secondary small fw-semibold">Operasional Lapangan</span>
                                    </div>
                                    <strong class="text-danger" style="font-size: 13.5px;">Rp {{ number_format($generalCost, 0, '', '.') }}</strong>
                                </div>
                            </div>

                            <!-- Net Profit Highlight Box -->
                            <div class="p-3 rounded-3 {{ $profit >= 0 ? 'bg-success-subtle border border-success-subtle' : 'bg-danger-subtle border border-danger-subtle' }}">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="small fw-bold text-uppercase {{ $profit >= 0 ? 'text-success' : 'text-danger' }}" style="font-size: 11px; letter-spacing: 0.5px;">
                                        Profit Bersih (Net)
                                    </span>
                                    <span class="badge {{ $profit >= 0 ? 'bg-success text-white' : 'bg-danger text-white' }} px-2 py-0.5 rounded-pill" style="font-size: 10.5px; font-weight: 700;">
                                        Margin {{ number_format($margin, 1) }}%
                                    </span>
                                </div>
                                <div class="d-flex align-items-baseline justify-content-between">
                                    <span class="fs-4 fw-bold {{ $profit >= 0 ? 'text-success' : 'text-danger' }}">
                                        Rp {{ number_format($profit, 0, '', '.') }}
                                    </span>
                                    <span class="small text-muted" style="font-size: 11px;">
                                        {{ $profit >= 0 ? 'Surplus Kas' : 'Defisit Anggaran' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card: Live Expense Activity Feed (Audit Trail) -->
                    <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
                        <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="card-title m-0 fw-bold text-dark d-flex align-items-center gap-2">
                                    <i class="mdi mdi-history text-info fs-5"></i>
                                    Audit Activity Feed
                                </h6>
                                <span class="text-muted small" style="font-size: 11px;">Riwayat penambahan, edit & hapus biaya</span>
                            </div>
                            <span class="badge bg-secondary rounded-pill px-2">{{ $activityLogs->count() }}</span>
                        </div>

                        <div class="card-body p-3" style="max-height: 420px; overflow-y: auto;">
                            @if ($activityLogs->isEmpty())
                                <div class="text-center py-4 text-muted">
                                    <i class="mdi mdi-timeline-clock-outline fs-2 d-block mb-1 opacity-50"></i>
                                    <p class="small mb-0">Belum ada riwayat aktivitas pengeluaran.</p>
                                </div>
                            @else
                                <div class="activity-feed" style="position: relative; padding-left: 18px; border-left: 2px solid #e2e8f0; margin-left: 8px;">
                                    @foreach ($activityLogs as $log)
                                        @php
                                            $dotColor = 'secondary';
                                            if ($log->action === 'created') $dotColor = 'success';
                                            elseif ($log->action === 'updated') $dotColor = 'warning';
                                            elseif ($log->action === 'deleted') $dotColor = 'danger';

                                            $props = is_array($log->properties) ? $log->properties : (json_decode($log->properties, true) ?? []);
                                        @endphp
                                        <div class="feed-item mb-3 position-relative">
                                            <span class="position-absolute rounded-circle bg-{{ $dotColor }} shadow-2xs" style="left: -23px; top: 4px; width: 12px; height: 12px;"></span>

                                            <div>
                                                <div class="d-flex justify-content-between align-items-baseline mb-0.5">
                                                    <strong class="text-dark small" style="font-size: 12px;">
                                                        {{ $log->user->name ?? 'User #' . $log->user_id }}
                                                    </strong>
                                                    <span class="text-muted" style="font-size: 10px;">
                                                        {{ $log->created_at ? $log->created_at->diffForHumans() : '' }}
                                                    </span>
                                                </div>
                                                <p class="mb-1 text-secondary small" style="font-size: 11.5px; line-height: 1.35;">
                                                    {{ $log->description }}
                                                </p>

                                                @if (!empty($props['old_values']['amount']) || !empty($props['new_values']['amount']))
                                                    <div class="p-1.5 rounded bg-light border" style="font-size: 10.5px;">
                                                        <span class="text-danger text-decoration-line-through">
                                                            Rp {{ number_format(floatval($props['old_values']['amount'] ?? 0), 0, '', '.') }}
                                                        </span>
                                                        <i class="mdi mdi-arrow-right mx-1 text-muted"></i>
                                                        <strong class="text-success">
                                                            Rp {{ number_format(floatval($props['new_values']['amount'] ?? 0), 0, '', '.') }}
                                                        </strong>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Card: Kanban Hub & Field Engineer Status -->
                    <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
                        <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                            <h6 class="card-title m-0 fw-bold text-dark d-flex align-items-center gap-2">
                                <i class="mdi mdi-view-week text-primary fs-5"></i>
                                Status Kartu Kanban Lapangan
                            </h6>
                            @if ($kanbanTask && $kanbanTask->board_id)
                                <a href="{{ route('kanban.boards.show', $kanbanTask->board_id) }}" target="_blank" class="btn btn-xs btn-outline-primary rounded-pill px-2">
                                    Buka Board
                                </a>
                            @endif
                        </div>
                        <div class="card-body p-3.5" style="font-size: 13px;">
                            @if ($kanbanTask)
                                <div class="mb-2.5">
                                    <strong class="text-dark d-block mb-1">{{ $kanbanTask->title }}</strong>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge bg-label-info">{{ $kanbanTask->column->title ?? 'Tahapan Proyek' }}</span>
                                        <span class="text-muted small">Due: {{ $kanbanTask->due_date ? \Carbon\Carbon::parse($kanbanTask->due_date)->format('d M Y') : '-' }}</span>
                                    </div>
                                </div>

                                <!-- Checklists -->
                                @if ($kanbanTask->checklists->isNotEmpty())
                                    @php
                                        $cCount = $kanbanTask->checklists->where('is_completed', 1)->count();
                                        $tCount = $kanbanTask->checklists->count();
                                        $pVal = $tCount > 0 ? round(($cCount / $tCount) * 100) : 0;
                                    @endphp
                                    <div class="mb-3 pt-2 border-top">
                                        <div class="d-flex justify-content-between small fw-semibold mb-1">
                                            <span>Checklist Teknisi</span>
                                            <span class="text-success">{{ $cCount }}/{{ $tCount }} ({{ $pVal }}%)</span>
                                        </div>
                                        <div class="progress rounded-pill" style="height: 6px;">
                                            <div class="progress-bar bg-success" style="width: {{ $pVal }}%;"></div>
                                        </div>
                                    </div>
                                @endif

                                <!-- Assigned Engineers -->
                                <div class="mb-2 pt-2 border-top">
                                    <span class="text-muted small d-block mb-1">Tim Teknisi / PIC:</span>
                                    <div class="d-flex flex-wrap gap-1.5">
                                        @forelse ($kanbanTask->assignees as $ass)
                                            <span class="badge bg-light text-dark border px-2 py-1 small">
                                                <i class="mdi mdi-account text-primary"></i> {{ $ass->name }}
                                            </span>
                                        @empty
                                            <span class="text-muted small">Belum ditugaskan</span>
                                        @endforelse
                                    </div>
                                </div>

                                <!-- BAST Link if present -->
                                @if ($kanbanTask->bast)
                                    <div class="mt-3 p-2.5 rounded bg-success-subtle border border-success-subtle d-flex justify-content-between align-items-center">
                                        <span class="small fw-semibold text-success"><i class="mdi mdi-certificate"></i> BAST Ditandatangani</span>
                                        <a href="{{ route('bast.show', $kanbanTask->bast->id) }}" target="_blank" class="btn btn-xs btn-success">
                                            Lihat
                                        </a>
                                    </div>
                                @endif
                            @else
                                <div class="text-center py-3 text-muted">
                                    <p class="small mb-0">Project ini belum ditautkan ke kartu Kanban.</p>
                                </div>
                            @endif
                        </div>
                    </div>

                </div> <!-- closes col-lg-4 -->

            </div> <!-- closes row tab-1 -->
        </div> <!-- closes tab-pane tab-financial -->

        <!-- ======================================================== -->
        <!-- TAB 2: LAPORAN HARIAN (Daily Report) -->
        <!-- ======================================================== -->
        <div class="tab-pane fade" id="tab-daily" role="tabpanel" aria-labelledby="tab-daily-tab">
            <div class="pt-4">

                <!-- Card: Daily Project Reports (full width) -->
                <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                    <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="card-title m-0 fw-bold text-dark d-flex align-items-center gap-2">
                                <i class="mdi mdi-clipboard-list-outline text-info fs-5"></i>
                                Daily Project Report
                            </h6>
                            <p class="text-muted small m-0">Laporan harian progres pekerjaan lapangan terhubung dengan kartu Kanban</p>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-label-info px-2.5 py-1">{{ $dailyReports->count() }} Laporan</span>
                        </div>
                    </div>

                    @if ($dailyReports->isEmpty())
                        <div class="card-body text-center py-5 text-muted">
                            <i class="mdi mdi-clipboard-off-outline fs-1 d-block mb-3 opacity-40"></i>
                            <p class="fw-semibold mb-1">Belum Ada Laporan Harian</p>
                            <p class="small mb-0">Daily Report akan muncul di sini setelah dibuat melalui kartu Kanban yang terhubung dengan project ini.</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0" style="font-size: 13px;">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-4" style="width: 48px;">No</th>
                                        <th>Tanggal &amp; Info</th>
                                        <th>Rencana / Pencapaian Hari Ini</th>
                                        <th class="text-center" style="width: 90px;">Cuaca</th>
                                        <th class="text-center" style="width: 110px;">Status</th>
                                        <th class="text-center" style="width: 80px;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($dailyReports as $rIdx => $dr)
                                        <tr>
                                            <td class="ps-4 text-muted fw-semibold">{{ $rIdx + 1 }}</td>
                                            <td>
                                                <div class="fw-semibold text-dark">
                                                    {{ $dr->report_date ? \Carbon\Carbon::parse($dr->report_date)->format('d M Y') : '-' }}
                                                </div>
                                                <div class="text-muted small" style="font-size: 11px;">
                                                    @if ($dr->day_name)
                                                        {{ $dr->day_name }}
                                                        @if ($dr->day_number)
                                                            &mdash; <span class="badge bg-label-secondary" style="font-size: 9.5px;">Hari ke-{{ $dr->day_number }}</span>
                                                        @endif
                                                    @endif
                                                </div>
                                                <div class="text-muted small" style="font-size: 11px;">
                                                    <i class="mdi mdi-account-outline me-0.5"></i>
                                                    {{ $dr->creator->name ?? '-' }}
                                                </div>
                                            </td>
                                            <td style="max-width: 380px;">
                                                @if ($dr->planning_today)
                                                    <div class="small text-secondary mb-1" style="font-size: 12px; line-height: 1.35;">
                                                        <span class="fw-semibold text-primary">Rencana:</span>
                                                        {{ \Illuminate\Support\Str::limit($dr->planning_today, 120) }}
                                                    </div>
                                                @endif
                                                @if ($dr->achievement_today)
                                                    <div class="small text-secondary" style="font-size: 12px; line-height: 1.35;">
                                                        <span class="fw-semibold text-success">Realisasi:</span>
                                                        {{ \Illuminate\Support\Str::limit($dr->achievement_today, 120) }}
                                                    </div>
                                                @endif
                                                @if (!$dr->planning_today && !$dr->achievement_today)
                                                    <span class="text-muted small">-</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <div class="d-flex flex-wrap justify-content-center gap-1">
                                                    @if ($dr->weather_cerah)
                                                        <span title="Cerah"><i class="mdi mdi-weather-sunny text-warning fs-5"></i></span>
                                                    @endif
                                                    @if ($dr->weather_mendung)
                                                        <span title="Mendung"><i class="mdi mdi-weather-cloudy text-secondary fs-5"></i></span>
                                                    @endif
                                                    @if ($dr->weather_hujan)
                                                        <span title="Hujan"><i class="mdi mdi-weather-rainy text-info fs-5"></i></span>
                                                    @endif
                                                    @if ($dr->weather_dll)
                                                        <span title="Lainnya"><i class="mdi mdi-weather-windy text-muted fs-5"></i></span>
                                                    @endif
                                                    @if (!$dr->weather_cerah && !$dr->weather_mendung && !$dr->weather_hujan && !$dr->weather_dll)
                                                        <span class="text-muted small">-</span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                @if ($dr->status === 'approved')
                                                    <span class="badge bg-label-success px-2 py-1">Approved</span>
                                                @elseif ($dr->status === 'completed')
                                                    <span class="badge bg-label-primary px-2 py-1">Completed</span>
                                                @else
                                                    <span class="badge bg-label-secondary px-2 py-1">Draft</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <a href="{{ route('project-reports.show', $dr->id) }}"
                                                   target="_blank"
                                                   class="btn btn-icon btn-xs btn-outline-info rounded-circle"
                                                   title="Lihat Detail Report">
                                                    <i class="mdi mdi-eye-outline"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

            </div>
        </div> <!-- closes tab-pane tab-daily -->

    </div> <!-- closes tab-content -->

</div> <!-- closes container-fluid -->

<!-- ======================================================== -->
<!-- MODALS (ADD & EDIT EXPENSES) -->
<!-- ======================================================== -->

<!-- Add Expense Modal -->
<div class="modal fade" id="addExpenseModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content text-start border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title text-white d-flex align-items-center gap-2">
                    <i class="mdi mdi-plus-circle-outline"></i> Catat Pengeluaran Proyek Baru
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('project-monitoring.store-expense', $project->id) }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label for="name_d2" class="form-label fw-semibold">Deskripsi Pengeluaran <span class="text-danger">*</span></label>
                            <input type="text" id="name_d2" name="name" class="form-control" placeholder="Contoh: Tiket Kereta Teknisi / Beli Material Pipa Tambahan / Konsumsi Harian" required />
                        </div>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6 col-12 mb-3">
                            <label for="category_d2" class="form-label fw-semibold">Kategori <span class="text-danger">*</span></label>
                            <select id="category_d2" name="category" class="form-select" required>
                                <option value="" disabled selected>Pilih Kategori...</option>
                                <option value="Transport">Transport (BBM, Tol, Tiket)</option>
                                <option value="Akomodasi">Akomodasi (Hotel/Mess/Penginapan)</option>
                                <option value="Konsumsi">Konsumsi Lapangan</option>
                                <option value="Material">Material Lapangan</option>
                                <option value="Alat">Sewa / Beli Alat</option>
                                <option value="Lain-lain">Lain-lain</option>
                            </select>
                        </div>
                        <div class="col-md-6 col-12 mb-3">
                            <label for="date_d2" class="form-label fw-semibold">Tanggal Pengeluaran <span class="text-danger">*</span></label>
                            <input type="date" id="date_d2" name="date" class="form-control" value="{{ now()->format('Y-m-d') }}" required />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label for="payment_info_d2" class="form-label fw-semibold">Informasi Pembayaran / Rekening Tujuan</label>
                            <input type="text" id="payment_info_d2" name="payment_info" class="form-control" placeholder="Contoh: BCA 1234567890 a.n Toko XYZ / Cash" />
                        </div>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6 col-12 mb-3">
                            <label for="amount_d2" class="form-label fw-semibold">Nominal Biaya (Rp) <span class="text-danger">*</span></label>
                            <input type="text" id="amount_d2" name="amount" class="form-control fs-5 fw-bold text-danger" placeholder="Contoh: 150.000" onkeyup="formatRupiahInput(this)" required />
                        </div>
                        <div class="col-md-6 col-12 mb-3">
                            <label for="receipt_d2" class="form-label fw-semibold">Upload Bukti Nota (Gambar/PDF)</label>
                            <input type="file" id="receipt_d2" name="receipt" class="form-control" accept="image/*,application/pdf" />
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary d-inline-flex align-items-center gap-1">
                        <i class="mdi mdi-content-save-outline"></i> Simpan Pengeluaran
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Expense Modals for each expense row -->
@foreach ($expenses as $exp)
    <div class="modal fade" id="editExpenseModal{{ $exp->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content text-start border-0 shadow">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title fw-bold d-flex align-items-center gap-2">
                        <i class="mdi mdi-pencil-outline"></i> Edit Pengeluaran: {{ $exp->name }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('project-monitoring.update-expense', $exp->id) }}" method="post" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="modal-body p-4">
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label for="name_edit_d2_{{ $exp->id }}" class="form-label fw-semibold">Deskripsi Pengeluaran <span class="text-danger">*</span></label>
                                <input type="text" id="name_edit_d2_{{ $exp->id }}" name="name" class="form-control" value="{{ $exp->name }}" required />
                            </div>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6 col-12 mb-3">
                                <label for="category_edit_d2_{{ $exp->id }}" class="form-label fw-semibold">Kategori <span class="text-danger">*</span></label>
                                <select id="category_edit_d2_{{ $exp->id }}" name="category" class="form-select" required>
                                    <option value="Transport" {{ $exp->category == 'Transport' ? 'selected' : '' }}>Transport</option>
                                    <option value="Akomodasi" {{ $exp->category == 'Akomodasi' ? 'selected' : '' }}>Akomodasi (Hotel/Mess)</option>
                                    <option value="Konsumsi" {{ $exp->category == 'Konsumsi' ? 'selected' : '' }}>Konsumsi</option>
                                    <option value="Material" {{ $exp->category == 'Material' ? 'selected' : '' }}>Material Lapangan</option>
                                    <option value="Alat" {{ $exp->category == 'Alat' ? 'selected' : '' }}>Sewa / Beli Alat</option>
                                    <option value="Lain-lain" {{ $exp->category == 'Lain-lain' ? 'selected' : '' }}>Lain-lain</option>
                                </select>
                            </div>
                            <div class="col-md-6 col-12 mb-3">
                                <label for="date_edit_d2_{{ $exp->id }}" class="form-label fw-semibold">Tanggal Pengeluaran <span class="text-danger">*</span></label>
                                <input type="date" id="date_edit_d2_{{ $exp->id }}" name="date" class="form-control" value="{{ $exp->date ? \Carbon\Carbon::parse($exp->date)->format('Y-m-d') : '' }}" required />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label for="payment_info_edit_d2_{{ $exp->id }}" class="form-label fw-semibold">Informasi Pembayaran / No Rekening</label>
                                <input type="text" id="payment_info_edit_d2_{{ $exp->id }}" name="payment_info" class="form-control" value="{{ $exp->payment_info }}" />
                            </div>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6 col-12 mb-3">
                                <label for="amount_edit_d2_{{ $exp->id }}" class="form-label fw-semibold">Nominal Biaya (Rp) <span class="text-danger">*</span></label>
                                <input type="text" id="amount_edit_d2_{{ $exp->id }}" name="amount" class="form-control fs-5 fw-bold text-danger" value="{{ number_format($exp->amount, 0, ',', '.') }}" onkeyup="formatRupiahInput(this)" required />
                            </div>
                            <div class="col-md-6 col-12 mb-3">
                                <label for="receipt_edit_d2_{{ $exp->id }}" class="form-label fw-semibold">Ganti Bukti Nota (Opsional)</label>
                                <input type="file" id="receipt_edit_d2_{{ $exp->id }}" name="receipt" class="form-control" accept="image/*,application/pdf" />
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-warning fw-semibold">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach

<!-- Modal: Lingkup Pekerjaan & Item Kontrak (Revenue) -->
<div class="modal fade" id="scopeItemsModal" tabindex="-1" aria-labelledby="scopeItemsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content border-0 shadow">
            <div class="modal-header" style="background: linear-gradient(135deg, #696cff 0%, #9155fd 100%);">
                <div>
                    <h5 class="modal-title text-white fw-bold d-flex align-items-center gap-2" id="scopeItemsModalLabel">
                        <i class="mdi mdi-clipboard-text-outline"></i>
                        Lingkup Pekerjaan &amp; Item Kontrak
                    </h5>
                    <p class="text-white-50 mb-0 small">{{ $project->no_quote ?? '-' }} &mdash; {{ $project->company ?? '-' }}</p>
                </div>
                <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-0">
                <!-- Summary Strip -->
                <div class="d-flex flex-wrap align-items-center gap-3 px-4 py-3 border-bottom" style="background: #f8fafc;">
                    <div class="d-flex align-items-center gap-2">
                        <div class="avatar avatar-sm bg-label-primary rounded-circle d-flex align-items-center justify-content-center">
                            <i class="mdi mdi-format-list-numbered fs-5"></i>
                        </div>
                        <div>
                            <div class="text-muted" style="font-size: 11px;">Total Item</div>
                            <div class="fw-bold text-dark">{{ count($quoteItems) }} Item</div>
                        </div>
                    </div>
                    <div class="vr"></div>
                    <div class="d-flex align-items-center gap-2">
                        <div class="avatar avatar-sm bg-label-success rounded-circle d-flex align-items-center justify-content-center">
                            <i class="mdi mdi-cash-multiple fs-5"></i>
                        </div>
                        <div>
                            <div class="text-muted" style="font-size: 11px;">Total Nilai Kontrak</div>
                            <div class="fw-bold text-success">Rp {{ number_format($project->revenue, 0, '', '.') }}</div>
                        </div>
                    </div>
                    <div class="vr"></div>
                    <div class="d-flex align-items-center gap-2">
                        <div class="avatar avatar-sm bg-label-info rounded-circle d-flex align-items-center justify-content-center">
                            <i class="mdi mdi-account-tie-outline fs-5"></i>
                        </div>
                        <div>
                            <div class="text-muted" style="font-size: 11px;">Sales</div>
                            <div class="fw-bold text-dark">{{ $project->sales_name ?? '-' }}</div>
                        </div>
                    </div>
                </div>

                <!-- Table -->
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="font-size: 13.5px;">
                        <thead style="background: #f1f5f9; position: sticky; top: 0; z-index: 1;">
                            <tr>
                                <th class="ps-4" style="width: 48px;">No</th>
                                <th>Item / Jasa / Deskripsi</th>
                                <th class="text-center" style="width: 90px;">Qty</th>
                                <th class="text-end" style="width: 170px;">Harga Satuan (Rp)</th>
                                <th class="text-end pe-4" style="width: 190px;">Subtotal (Rp)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $i = 1; @endphp
                            @forelse ($quoteItems as $it)
                                <tr>
                                    <td class="ps-4 text-muted fw-semibold">{{ $i++ }}</td>
                                    <td>
                                        <div class="fw-semibold text-dark">{{ $it->item_name }}</div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-label-secondary px-2 py-1">
                                            {{ $it->qty }} {{ $it->unit ?? 'Unit' }}
                                        </span>
                                    </td>
                                    <td class="text-end text-muted">
                                        Rp {{ number_format($it->price, 0, '', '.') }}
                                    </td>
                                    <td class="text-end pe-4 fw-bold text-dark">
                                        Rp {{ number_format($it->amount, 0, '', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">
                                        <i class="mdi mdi-clipboard-off-outline fs-2 d-block mb-2 opacity-40"></i>
                                        Tidak ada data item penawaran.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot style="background: #f8fafc;">
                            <tr>
                                <td colspan="4" class="text-end fw-bold text-uppercase text-muted pe-3" style="font-size: 12px; letter-spacing: 0.5px;">Total Nilai Kontrak (Revenue):</td>
                                <td class="text-end pe-4">
                                    <span class="fs-5 fw-bold text-success">Rp {{ number_format($project->revenue, 0, '', '.') }}</span>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <div class="modal-footer bg-light border-top">
                <span class="text-muted small me-auto">
                    <i class="mdi mdi-information-outline me-1"></i>
                    Data berdasarkan quotation yang telah disetujui customer
                </span>
                @php
                    if (!empty($project->id_unit_quotation)) {
                        $quoteDetailUrl = route('unit-quotation.show', $project->id_unit_quotation);
                    } elseif (!empty($project->id_quotation)) {
                        if ($project->quote_type === 'Service') {
                            $quoteDetailUrl = route('show-service.quotation', $project->id_quotation);
                        } elseif ($project->quote_type === 'Overhaul') {
                            $quoteDetailUrl = route('show-overhaul.quotation', $project->id_quotation);
                        } else {
                            $quoteDetailUrl = route('quotation.show', $project->id_quotation);
                        }
                    } else {
                        $quoteDetailUrl = null;
                    }
                @endphp
                @if ($quoteDetailUrl)
                    <a href="{{ $quoteDetailUrl }}" target="_blank" class="btn btn-primary d-inline-flex align-items-center gap-1">
                        <i class="mdi mdi-open-in-new"></i> Buka Detail Penawaran
                    </a>
                @endif
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Compatibility Modals -->
@include('components.modal.pending.project', ['pending' => $project])
@include('components.modal.pending.status', ['pending' => $project])

@endsection

@push('after-style')
<style>
    #projectCockpitTabs .nav-link {
        border-radius: 8px;
        padding: 10px 18px;
        font-weight: 500;
        color: #566a7f;
        transition: all 0.2s ease;
        background: #ffffff;
        border: 1px solid #e0e4e8;
    }

    #projectCockpitTabs .nav-link:hover {
        background: rgba(105, 108, 255, 0.08);
        color: #696cff;
        border-color: #696cff;
    }

    #projectCockpitTabs .nav-link.active {
        background: #696cff;
        color: #ffffff;
        border-color: #696cff;
        box-shadow: 0 4px 12px rgba(105, 108, 255, 0.35);
    }

    #projectCockpitTabs .nav-link.active .badge {
        background: rgba(255, 255, 255, 0.25) !important;
        color: #ffffff !important;
    }
</style>
@endpush

@push('after-script')
<script>
    function formatRupiahInput(input) {
        let value = input.value.replace(/[^,\d]/g, '').toString();
        let split = value.split(',');
        let sisa = split[0].length % 3;
        let rupiah = split[0].substr(0, sisa);
        let ribuan = split[0].substr(sisa).match(/\d{3}/gi);

        if (ribuan) {
            let separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }

        rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
        input.value = rupiah;
    }

    // Client-side quick category filter for Expense Ledger table
    document.addEventListener('DOMContentLoaded', function () {
        const filterPills = document.querySelectorAll('.expense-filter-pill');
        const rows = document.querySelectorAll('#tableExpenseLedger .expense-row');

        filterPills.forEach(pill => {
            pill.addEventListener('click', function () {
                filterPills.forEach(p => {
                    p.classList.remove('btn-primary', 'active');
                    p.classList.add('btn-outline-secondary');
                });
                this.classList.remove('btn-outline-secondary');
                this.classList.add('btn-primary', 'active');

                const selectedCat = this.getAttribute('data-cat');
                rows.forEach(row => {
                    const rowCat = row.getAttribute('data-category');
                    if (selectedCat === 'all' || rowCat === selectedCat) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        });
    });
</script>
@endpush
