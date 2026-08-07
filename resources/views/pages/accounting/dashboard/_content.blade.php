<h4 class="fw-bold py-3 mb-1"><span class="text-muted fw-light">Dashboard /</span> Accounting</h4>
<p class="text-muted mb-4">Ringkasan aktivitas akuntansi</p>

<!-- Row 1: status cards -->
<div class="row mb-2">
    <div class="col-sm-6 col-lg-3 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <div class="avatar avatar-md me-3">
                        <div class="avatar-initial bg-label-primary rounded"><i class="mdi mdi-file-document-edit-outline mdi-24px"></i></div>
                    </div>
                    <div>
                        <small class="text-muted d-block">Customer Invoice - Belum Dibuat</small>
                        <h5 class="mb-0">{{ $acctInvoiceBelumDibuatCount }}</h5>
                    </div>
                </div>
                <small class="text-muted">Total Rp {{ number_format($acctInvoiceBelumDibuatTotal, 0, ',', '.') }}</small><br>
                <a href="{{ route('invoice.request') }}" class="small">Lihat Detail <i class="mdi mdi-arrow-right"></i></a>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <div class="avatar avatar-md me-3">
                        <div class="avatar-initial bg-label-warning rounded"><i class="mdi mdi-file-clock-outline mdi-24px"></i></div>
                    </div>
                    <div>
                        <small class="text-muted d-block">Supplier Bill - Belum Lunas</small>
                        <h5 class="mb-0">{{ $acctApUnpaidCount }}</h5>
                    </div>
                </div>
                <small class="text-muted">Total Rp {{ number_format($acctApUnpaidTotal, 0, ',', '.') }}</small><br>
                <a href="{{ route('payable.index_invoice') }}" class="small">Lihat Detail <i class="mdi mdi-arrow-right"></i></a>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <div class="avatar avatar-md me-3">
                        <div class="avatar-initial bg-label-success rounded"><i class="mdi mdi-file-check-outline mdi-24px"></i></div>
                    </div>
                    <div>
                        <small class="text-muted d-block">Supplier Bill - Lunas (Bulan Ini)</small>
                        <h5 class="mb-0">{{ $acctApPaidMonthCount }}</h5>
                    </div>
                </div>
                <small class="text-muted">Total Rp {{ number_format($acctApPaidMonthTotal, 0, ',', '.') }}</small><br>
                <a href="{{ route('payable.index_invoice') }}" class="small">Lihat Detail <i class="mdi mdi-arrow-right"></i></a>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <div class="avatar avatar-md me-3">
                        <div class="avatar-initial bg-label-info rounded"><i class="mdi mdi-office-building-outline mdi-24px"></i></div>
                    </div>
                    <div>
                        <small class="text-muted d-block">Fixed Asset - Baru (Bulan Ini)</small>
                        <h5 class="mb-0">{{ $acctFixedAssetMonthCount }}</h5>
                    </div>
                </div>
                <small class="text-muted">Total Rp {{ number_format($acctFixedAssetMonthTotal, 0, ',', '.') }}</small><br>
                <a href="{{ route('fixed.index') }}" class="small">Lihat Detail <i class="mdi mdi-arrow-right"></i></a>
            </div>
        </div>
    </div>
</div>

<!-- Journal Entry banner (module not built yet) -->
<div class="row mb-2">
    <div class="col-12 mb-2">
        <div class="alert alert-secondary d-flex align-items-center justify-content-between mb-0" role="alert">
            <div>
                <i class="mdi mdi-book-open-page-variant-outline mdi-18px me-1"></i>
                <strong>Journal Entry</strong> &mdash; modul buku besar / jurnal umum belum tersedia sebagai fitur tersendiri.
            </div>
            <span class="badge bg-label-secondary">Under Construction</span>
        </div>
    </div>
</div>

<!-- Row 2: KPI boxes -->
<div class="row mb-2">
    <div class="col-sm-6 col-lg-3 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <div class="avatar avatar-md me-3">
                        <div class="avatar-initial bg-label-danger rounded"><i class="mdi mdi-briefcase-outline mdi-24px"></i></div>
                    </div>
                    <div>
                        <small class="text-muted d-block">Account Payable Outstanding</small>
                    </div>
                </div>
                <h5 class="mb-1">Rp {{ number_format($acctApUnpaidTotal, 0, ',', '.') }}</h5>
                <small class="text-muted">Total {{ $acctApSupplierCount }} Supplier</small><br>
                <a href="{{ route('payable.index_invoice') }}" class="small">Lihat Aging AP <i class="mdi mdi-arrow-right"></i></a>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <div class="avatar avatar-md me-3">
                        <div class="avatar-initial bg-label-success rounded"><i class="mdi mdi-wallet-outline mdi-24px"></i></div>
                    </div>
                    <div>
                        <small class="text-muted d-block">Account Receivable Outstanding</small>
                    </div>
                </div>
                <h5 class="mb-1">Rp {{ number_format($acctArOutstanding, 0, ',', '.') }}</h5>
                <small class="text-muted">Total {{ $acctArCustomerCount }} Customer</small><br>
                <a href="{{ route('payment_index.aging') }}" class="small">Lihat Aging AR <i class="mdi mdi-arrow-right"></i></a>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <div class="avatar avatar-md me-3">
                        <div class="avatar-initial bg-label-warning rounded"><i class="mdi mdi-chart-areaspline mdi-24px"></i></div>
                    </div>
                    <div>
                        <small class="text-muted d-block">Expense (Bulan Ini)</small>
                    </div>
                </div>
                <h5 class="mb-1">Rp {{ number_format($acctExpenseMonth, 0, ',', '.') }}</h5>
                <small class="{{ $acctExpenseChangePct <= 0 ? 'text-success' : 'text-danger' }}">
                    <i class="mdi {{ $acctExpenseChangePct <= 0 ? 'mdi-arrow-down' : 'mdi-arrow-up' }}"></i>
                    {{ abs($acctExpenseChangePct) }}% dari bulan lalu
                </small>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <div class="avatar avatar-md me-3">
                        <div class="avatar-initial bg-label-primary rounded"><i class="mdi mdi-chart-donut mdi-24px"></i></div>
                    </div>
                    <div>
                        <small class="text-muted d-block">COGS (Bulan Ini)</small>
                    </div>
                </div>
                <h5 class="mb-1">Rp {{ number_format($acctCogsMonth, 0, ',', '.') }}</h5>
                <small class="{{ $acctCogsChangePct <= 0 ? 'text-success' : 'text-danger' }}">
                    <i class="mdi {{ $acctCogsChangePct <= 0 ? 'mdi-arrow-down' : 'mdi-arrow-up' }}"></i>
                    {{ abs($acctCogsChangePct) }}% dari bulan lalu
                </small>
            </div>
        </div>
    </div>
</div>

<!-- Row 3: Aging AP / AR -->
<div class="row mb-2">
    <div class="col-lg-6 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0">Account Payable Aging</h5>
                <small class="text-muted">Belum ada tanggal jatuh tempo tercatat pada Supplier Bill</small>
            </div>
            <div class="card-body d-flex align-items-center justify-content-center" style="min-height: 260px;">
                <span class="badge bg-label-secondary">Under Construction</span>
            </div>
        </div>
    </div>
    <div class="col-lg-6 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0">Account Receivable Aging</h5>
                <small class="text-muted">Total Rp {{ number_format($acctArOutstanding, 0, ',', '.') }}</small>
            </div>
            <div class="card-body">
                <div id="acctAgingArChart"></div>
            </div>
        </div>
    </div>
</div>

<!-- Row 4: Supplier bill per status / Beban per akun -->
<div class="row mb-2">
    <div class="col-lg-5 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0">Supplier Bill per Status</h5>
            </div>
            <div class="card-body">
                <div id="acctSupplierBillStatusChart"></div>
            </div>
        </div>
    </div>
    <div class="col-lg-7 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0">Beban per Akun (Bulan Ini)</h5>
            </div>
            <div class="card-body">
                @if ($acctExpenseByAccount->isEmpty())
                    <div class="d-flex align-items-center justify-content-center" style="min-height: 220px;">
                        <span class="text-muted">Belum ada beban tercatat bulan ini.</span>
                    </div>
                @else
                    <div id="acctExpenseByAccountChart"></div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Row 5: Dokumen terbaru / Tugas -->
<div class="row mb-2">
    <div class="col-lg-7 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0">Dokumen Terbaru</h5>
            </div>
            <div class="card-body">
                <ul class="nav nav-pills mb-3" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button type="button" class="nav-link active waves-effect waves-light" role="tab" data-bs-toggle="tab" data-bs-target="#acct-doc-bill">Supplier Bill</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button type="button" class="nav-link waves-effect waves-light" role="tab" data-bs-toggle="tab" data-bs-target="#acct-doc-invoice">Customer Invoice</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button type="button" class="nav-link waves-effect waves-light" role="tab" data-bs-toggle="tab" data-bs-target="#acct-doc-journal">Journal</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button type="button" class="nav-link waves-effect waves-light" role="tab" data-bs-toggle="tab" data-bs-target="#acct-doc-expense">Expense</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button type="button" class="nav-link waves-effect waves-light" role="tab" data-bs-toggle="tab" data-bs-target="#acct-doc-fa">FA</button>
                    </li>
                </ul>
                <div class="tab-content p-0">
                    <div class="tab-pane fade show active" id="acct-doc-bill" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-sm mb-0">
                                <tbody>
                                    @forelse ($acctRecentSupplierBill as $bill)
                                        <tr>
                                            <td>
                                                <a href="{{ route('product-in.show', $bill->id) }}" class="fw-semibold text-primary-hover d-block">{{ $bill->no_product_in }}</a>
                                                <small class="text-muted">{{ $bill->supplier }}</small>
                                            </td>
                                            <td class="text-end">
                                                Rp {{ number_format($bill->total ?? 0, 0, ',', '.') }}<br>
                                                <span class="badge bg-label-{{ $bill->accept == '1' ? 'success' : 'warning' }}">{{ $bill->accept == '1' ? 'Lunas' : 'Belum Lunas' }}</span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td class="text-center text-muted">Belum ada data.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="acct-doc-invoice" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-sm mb-0">
                                <tbody>
                                    @forelse ($acctRecentCustomerInvoice as $inv)
                                        <tr>
                                            <td>
                                                <a href="{{ route('invoice.show', $inv->id) }}" class="fw-semibold text-primary-hover d-block">{{ $inv->no_invoice }}</a>
                                                <small class="text-muted">{{ $inv->company }}</small>
                                            </td>
                                            <td class="text-end">Rp {{ number_format($inv->harga_total ?? 0, 0, ',', '.') }}</td>
                                        </tr>
                                    @empty
                                        <tr><td class="text-center text-muted">Belum ada data.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="acct-doc-journal" role="tabpanel">
                        <div class="d-flex align-items-center justify-content-center py-4">
                            <span class="badge bg-label-secondary">Under Construction &mdash; modul Journal Entry belum tersedia</span>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="acct-doc-expense" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-sm mb-0">
                                <tbody>
                                    @forelse ($acctRecentExpense as $exp)
                                        <tr>
                                            <td>
                                                <a href="{{ route('expense.show', $exp->id) }}" class="fw-semibold text-primary-hover d-block">{{ $exp->no_expense }}</a>
                                                <small class="text-muted">{{ $exp->memo }}</small>
                                            </td>
                                            <td class="text-end">Rp {{ number_format($exp->amount ?? 0, 0, ',', '.') }}</td>
                                        </tr>
                                    @empty
                                        <tr><td class="text-center text-muted">Belum ada data.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="acct-doc-fa" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-sm mb-0">
                                <tbody>
                                    @forelse ($acctRecentFixedAsset as $fa)
                                        <tr>
                                            <td>
                                                <a href="{{ route('fixed.show', $fa->id) }}" class="fw-semibold text-primary-hover d-block">{{ $fa->code }}</a>
                                                <small class="text-muted">{{ $fa->desc }}</small>
                                            </td>
                                            <td class="text-end">Rp {{ number_format($fa->total ?? 0, 0, ',', '.') }}</td>
                                        </tr>
                                    @empty
                                        <tr><td class="text-center text-muted">Belum ada data.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-5 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0">Tugas Accounting</h5>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex align-items-center justify-content-between px-0">
                        <span><i class="mdi mdi-file-document-edit-outline text-primary me-2"></i>Buat Customer Invoice</span>
                        <a href="{{ route('invoice.request') }}" class="badge bg-label-primary text-decoration-none">{{ $acctInvoiceBelumDibuatCount }}</a>
                    </li>
                    <li class="list-group-item d-flex align-items-center justify-content-between px-0">
                        <span><i class="mdi mdi-file-clock-outline text-warning me-2"></i>Review Supplier Bill</span>
                        <a href="{{ route('payable.index_invoice') }}" class="badge bg-label-warning text-decoration-none">{{ $acctApUnpaidCount }}</a>
                    </li>
                    <li class="list-group-item d-flex align-items-center justify-content-between px-0">
                        <span><i class="mdi mdi-account-cash-outline text-info me-2"></i>Review Piutang Tempo (AR)</span>
                        <a href="{{ route('payment_index.aging') }}" class="badge bg-label-info text-decoration-none">{{ count($acctTempoPayments) }}</a>
                    </li>
                    <li class="list-group-item d-flex align-items-center justify-content-between px-0">
                        <span><i class="mdi mdi-office-building-outline text-success me-2"></i>Fixed Asset Baru Bulan Ini</span>
                        <a href="{{ route('fixed.index') }}" class="badge bg-label-success text-decoration-none">{{ $acctFixedAssetMonthCount }}</a>
                    </li>
                    <li class="list-group-item d-flex align-items-center justify-content-between px-0">
                        <span><i class="mdi mdi-bank-outline text-secondary me-2"></i>Rekonsiliasi Bank</span>
                        <span class="badge bg-label-secondary">Under Construction</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Row 6: Shortcut -->
<div class="row mb-2">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Shortcut</h5>
            </div>
            <div class="card-body">
                <div class="row text-center g-3">
                    <div class="col-6 col-md-2">
                        <a href="{{ route('invoice.request') }}" class="text-decoration-none">
                            <div class="avatar avatar-lg mx-auto mb-2"><div class="avatar-initial bg-label-primary rounded"><i class="mdi mdi-file-document-edit-outline mdi-24px"></i></div></div>
                            <small class="d-block">Buat Customer Invoice</small>
                        </a>
                    </div>
                    <div class="col-6 col-md-2">
                        <a href="{{ route('payable.index_invoice') }}" class="text-decoration-none">
                            <div class="avatar avatar-lg mx-auto mb-2"><div class="avatar-initial bg-label-warning rounded"><i class="mdi mdi-file-document-outline mdi-24px"></i></div></div>
                            <small class="d-block">Lihat Supplier Bill</small>
                        </a>
                    </div>
                    <div class="col-6 col-md-2">
                        <a href="javascript:void(0)" class="text-decoration-none opacity-50" title="Under Construction">
                            <div class="avatar avatar-lg mx-auto mb-2"><div class="avatar-initial bg-label-secondary rounded"><i class="mdi mdi-book-open-page-variant-outline mdi-24px"></i></div></div>
                            <small class="d-block">Buat Journal Entry</small>
                        </a>
                    </div>
                    <div class="col-6 col-md-2">
                        <a href="{{ route('expense.create') }}" class="text-decoration-none">
                            <div class="avatar avatar-lg mx-auto mb-2"><div class="avatar-initial bg-label-danger rounded"><i class="mdi mdi-cash-minus mdi-24px"></i></div></div>
                            <small class="d-block">Buat Expense</small>
                        </a>
                    </div>
                    <div class="col-6 col-md-2">
                        <a href="{{ route('fixed.create') }}" class="text-decoration-none">
                            <div class="avatar avatar-lg mx-auto mb-2"><div class="avatar-initial bg-label-info rounded"><i class="mdi mdi-office-building-outline mdi-24px"></i></div></div>
                            <small class="d-block">Fixed Asset Baru</small>
                        </a>
                    </div>
                    <div class="col-6 col-md-2">
                        <a href="{{ route('report.finance') }}" class="text-decoration-none">
                            <div class="avatar avatar-lg mx-auto mb-2"><div class="avatar-initial bg-label-success rounded"><i class="mdi mdi-chart-box-outline mdi-24px"></i></div></div>
                            <small class="d-block">Laporan Keuangan</small>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('before-style')
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/apex-charts/apex-charts.css" />
@endpush

@push('page-script')
    <script src="{{ asset('assets') }}/vendor/libs/apex-charts/apexcharts.js"></script>
    <script>
        (function () {
            const isDark = document.documentElement.classList.contains('dark-style');
            const labelColor = isDark ? '#a8aaae' : '#6d6b77';

            const formatRp = val => {
                if (val >= 1_000_000_000) return 'Rp ' + (val / 1_000_000_000).toFixed(1) + 'B';
                if (val >= 1_000_000) return 'Rp ' + (val / 1_000_000).toFixed(1) + 'M';
                return 'Rp ' + val.toLocaleString('id-ID');
            };

            const agingArEl = document.querySelector('#acctAgingArChart');
            if (agingArEl) {
                new ApexCharts(agingArEl, {
                    chart: { type: 'donut', height: 260 },
                    labels: ['Current', '1 - 30 Hari', '31 - 60 Hari', '> 60 Hari'],
                    series: @json(array_values($acctArAgingBuckets)),
                    colors: ['#71dd37', '#03c3ec', '#ffab00', '#ff3e1d'],
                    legend: { position: 'bottom', labels: { colors: labelColor } },
                    dataLabels: { enabled: true, formatter: (val) => val.toFixed(1) + '%' },
                    tooltip: { y: { formatter: formatRp } },
                }).render();
            }

            const billStatusEl = document.querySelector('#acctSupplierBillStatusChart');
            if (billStatusEl) {
                new ApexCharts(billStatusEl, {
                    chart: { type: 'donut', height: 260 },
                    labels: ['Lunas', 'Belum Lunas'],
                    series: [{{ (int) $acctApPaidTotalAll }}, {{ (int) $acctApUnpaidCount }}],
                    colors: ['#71dd37', '#ffab00'],
                    legend: { position: 'bottom', labels: { colors: labelColor } },
                    dataLabels: { enabled: true },
                }).render();
            }

            const expenseByAccountEl = document.querySelector('#acctExpenseByAccountChart');
            if (expenseByAccountEl) {
                new ApexCharts(expenseByAccountEl, {
                    chart: { type: 'bar', height: 260, toolbar: { show: false } },
                    plotOptions: { bar: { horizontal: true, borderRadius: 4, barHeight: '55%' } },
                    series: [{ name: 'Beban', data: @json($acctExpenseByAccount->pluck('total')) }],
                    xaxis: {
                        categories: @json($acctExpenseByAccount->pluck('name')),
                        labels: { formatter: formatRp, style: { colors: labelColor } },
                    },
                    yaxis: { labels: { style: { colors: labelColor } } },
                    colors: ['#696cff'],
                    dataLabels: { enabled: false },
                    tooltip: { y: { formatter: formatRp } },
                }).render();
            }
        })();
    </script>
@endpush
