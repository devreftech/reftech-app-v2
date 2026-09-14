<h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Dashboard /</span> Finance</h4>

<!-- KPI Cards -->
<div class="row mb-2">
    <div class="col-sm-6 col-md-4 col-lg mb-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <div class="avatar avatar-md me-3">
                        <div class="avatar-initial bg-label-success rounded"><i class="mdi mdi-wallet-outline mdi-24px"></i></div>
                    </div>
                    <div class="flex-grow-1">
                        <div class="d-flex align-items-center justify-content-between">
                            <small class="text-muted d-block">Cash Position</small>
                            <a href="{{ route('bank.index') }}" class="small text-primary text-decoration-none" title="Buka Master Kas & Bank">
                                <i class="mdi mdi-open-in-new"></i>
                            </a>
                        </div>
                        <h5 class="mb-0">Rp {{ number_format($financeCashPosition ?? 0, 0, ',', '.') }}</h5>
                    </div>
                </div>
                <div class="d-flex align-items-center justify-content-between">
                    <small class="text-muted" title="Reftech: Rp {{ number_format($financeCashReftech, 0, ',', '.') }} | Kojisha: Rp {{ number_format($financeCashKojisha, 0, ',', '.') }}">{{ $financeActiveBankCount ?? 0 }} Rekening (Reftech &amp; Kojisha)</small>
                    <a href="{{ route('bank.index') }}" class="badge bg-label-success text-decoration-none">
                        Kas &amp; Bank &rarr;
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-md-4 col-lg mb-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <div class="avatar avatar-md me-3">
                        <div class="avatar-initial bg-label-info rounded"><i class="mdi mdi-account-cash-outline mdi-24px"></i></div>
                    </div>
                    <div>
                        <small class="text-muted d-block">Outstanding AR (Piutang)</small>
                        <h5 class="mb-0">Rp {{ number_format($financeOutstandingAR, 0, ',', '.') }}</h5>
                    </div>
                </div>
                <small class="text-muted">Total Tempo payment belum lunas</small>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-md-4 col-lg mb-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <div class="avatar avatar-md me-3">
                        <div class="avatar-initial bg-label-warning rounded"><i class="mdi mdi-file-document-outline mdi-24px"></i></div>
                    </div>
                    <div class="flex-grow-1">
                        <div class="d-flex align-items-center justify-content-between">
                            <small class="text-muted d-block">Outstanding AP (Hutang)</small>
                            <a href="{{ route('payable.index_aging') }}" class="small text-primary text-decoration-none" title="Buka Aging Report AP">
                                <i class="mdi mdi-open-in-new"></i>
                            </a>
                        </div>
                        <h5 class="mb-0">Rp {{ number_format($financeOutstandingAP, 0, ',', '.') }}</h5>
                    </div>
                </div>
                <div class="d-flex align-items-center justify-content-between">
                    <small class="text-muted">Total Supplier Bill belum lunas</small>
                    <a href="{{ route('payable.index_aging') }}" class="badge bg-label-warning text-decoration-none">
                        Aging AP &rarr;
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-md-4 col-lg mb-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <div class="avatar avatar-md me-3">
                        <div class="avatar-initial bg-label-danger rounded"><i class="mdi mdi-cash-minus mdi-24px"></i></div>
                    </div>
                    <div>
                        <small class="text-muted d-block">Expense (Bulan Ini)</small>
                        <h5 class="mb-0">Rp {{ number_format($financeExpenseMonth, 0, ',', '.') }}</h5>
                    </div>
                </div>
                <small class="text-muted">Total pengeluaran operasional</small>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-md-4 col-lg mb-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <div class="avatar avatar-md me-3">
                        <div class="avatar-initial bg-label-primary rounded"><i class="mdi mdi-chart-line mdi-24px"></i></div>
                    </div>
                    <div>
                        <small class="text-muted d-block">Net Profit (YTD)</small>
                        <h5 class="mb-0">Rp {{ number_format($financeNetProfitYTD, 0, ',', '.') }}</h5>
                    </div>
                </div>
                <small class="text-muted">Margin {{ $financeMarginYTD }}%</small>
            </div>
        </div>
    </div>
</div>

<!-- Aging Receivable / Payable -->
<div class="row mb-2">
    <div class="col-lg-6 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0">Aging Receivable (Piutang)</h5>
                <small class="text-muted">Rp {{ number_format($financeOutstandingAR, 0, ',', '.') }}</small>
            </div>
            <div class="card-body">
                <div id="financeAgingReceivableChart"></div>
            </div>
        </div>
    </div>
    <div class="col-lg-6 mb-4">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0">Aging Payable (Hutang)</h5>
                    <small class="text-muted">Rp {{ number_format($financeOutstandingAP, 0, ',', '.') }}</small>
                </div>
                <a href="{{ route('payable.index_aging') }}" class="small text-primary text-decoration-none" title="Buka Aging Report AP">
                    <i class="mdi mdi-open-in-new"></i>
                </a>
            </div>
            <div class="card-body">
                @if ($financeOutstandingAP > 0)
                    <div id="financeAgingPayableChart"></div>
                @else
                    <div class="d-flex align-items-center justify-content-center text-muted" style="min-height: 260px;">
                        Tidak ada hutang supplier beredar.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Revenue / Expense / Cash & Bank -->
<div class="row mb-2">
    <div class="col-lg-4 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0">Revenue</h5>
                @if ($financeMonthlyTarget > 0)
                    <small class="text-muted">
                        Target bulan ini Rp {{ number_format($financeMonthlyTarget, 0, ',', '.') }}
                        &bull;
                        <span class="badge bg-label-{{ $financeRevenueAchievement >= 100 ? 'success' : ($financeRevenueAchievement >= 70 ? 'warning' : 'danger') }}">
                            {{ $financeRevenueAchievement }}% achieved
                        </span>
                    </small>
                @else
                    <small class="text-muted">Actual bulanan (Target: <span class="badge bg-label-secondary">belum diset di Sales Target</span>)</small>
                @endif
            </div>
            <div class="card-body">
                <div id="financeRevenueChart"></div>
            </div>
        </div>
    </div>
    <div class="col-lg-4 mb-4">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0">Expense</h5>
                    @if (($financeMonthlyBudget ?? 0) > 0)
                        <small class="text-muted">
                            Budget bln ini Rp {{ number_format($financeMonthlyBudget, 0, ',', '.') }}
                            &bull;
                            <span class="badge bg-label-{{ ($financeExpenseBudgetAchievement ?? 0) > 100 ? 'danger' : (($financeExpenseBudgetAchievement ?? 0) >= 85 ? 'warning' : 'success') }}">
                                {{ $financeExpenseBudgetAchievement }}% terpakai
                            </span>
                        </small>
                    @else
                        <small class="text-muted">Actual bulanan (Budget: <a href="{{ route('finance.expense-budget.index') }}" class="badge bg-label-secondary text-decoration-none">Atur Plafon &rarr;</a>)</small>
                    @endif
                </div>
                <a href="{{ route('finance.expense-budget.index') }}" class="small text-primary text-decoration-none" title="Expense Budget Management">
                    <i class="mdi mdi-tune-vertical"></i>
                </a>
            </div>
            <div class="card-body">
                <div id="financeExpenseChart"></div>
            </div>
        </div>
    </div>
    <div class="col-lg-4 mb-4">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center pb-2">
                <div>
                    <h5 class="mb-0">Cash &amp; Bank Balance</h5>
                    <small class="text-muted">Total: Rp {{ number_format($financeCashPosition, 0, ',', '.') }}</small>
                </div>
                <a href="{{ route('bank.index') }}" class="small text-primary text-decoration-none" title="Buka Master Kas & Bank">
                    <i class="mdi mdi-open-in-new"></i>
                </a>
            </div>
            <div class="card-body pt-2">
                {{-- Pemisahan Entitas: Reftech vs Kojisha --}}
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <div class="p-2 rounded bg-label-info border border-info border-opacity-25 text-center">
                            <small class="text-muted d-block fw-semibold" style="font-size: 11px;">PT REFTECH</small>
                            <div class="fw-bold text-info" style="font-size: 13px;">Rp {{ number_format($financeCashReftech, 0, ',', '.') }}</div>
                            <small class="d-block text-muted" style="font-size: 10px;">{{ $financeBankReftech->count() }} Rekening</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-2 rounded bg-label-warning border border-warning border-opacity-25 text-center">
                            <small class="text-muted d-block fw-semibold" style="font-size: 11px;">PT KOJISHA</small>
                            <div class="fw-bold text-warning" style="font-size: 13px;">Rp {{ number_format($financeCashKojisha, 0, ',', '.') }}</div>
                            <small class="d-block text-muted" style="font-size: 10px;">{{ $financeBankKojisha->count() }} Rekening</small>
                        </div>
                    </div>
                </div>

                @if ($financeCashPosition > 0)
                    <div id="financeCashBankChart"></div>
                @else
                    <div class="d-flex align-items-center justify-content-center text-muted" style="min-height: 180px;">
                        Belum ada saldo kas/bank tercatat.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Profit Summary / Recent Activity -->
<div class="row mb-2">
    <div class="col-lg-5 mb-4">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0">Profit Summary</h5>
                <small class="text-muted">YTD (Jan - {{ \Carbon\Carbon::now()->translatedFormat('M Y') }})</small>
            </div>
            <div class="card-body">
                <table class="table table-borderless mb-0">
                    <tbody>
                        <tr>
                            <td class="ps-0">Revenue</td>
                            <td class="text-end pe-0">Rp {{ number_format($financeRevenueYTD, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td class="ps-0">COGS</td>
                            <td class="text-end pe-0">(Rp {{ number_format($financeCOGSYTD, 0, ',', '.') }})</td>
                        </tr>
                        <tr class="border-top">
                            <td class="ps-0 fw-semibold">Gross Profit</td>
                            <td class="text-end pe-0 fw-semibold text-success">Rp {{ number_format($financeGrossProfitYTD, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td class="ps-0">Expense</td>
                            <td class="text-end pe-0">(Rp {{ number_format($financeExpenseYTD, 0, ',', '.') }})</td>
                        </tr>
                        <tr class="border-top">
                            <td class="ps-0 fw-bold">Net Profit</td>
                            <td class="text-end pe-0 fw-bold text-success">Rp {{ number_format($financeNetProfitYTD, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td class="ps-0">Margin</td>
                            <td class="text-end pe-0">{{ $financeMarginYTD }}%</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-7 mb-4">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0">Aktivitas Terbaru</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive text-nowrap">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Tipe</th>
                                <th>No. Dokumen</th>
                                <th class="text-end">Nominal</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @forelse ($financeRecentActivity as $activity)
                                @php
                                    $badgeClass = match ($activity->tipe) {
                                        'Invoice' => 'bg-label-success',
                                        'Payment' => 'bg-label-info',
                                        'Expense' => 'bg-label-danger',
                                        default   => 'bg-label-primary'
                                    };
                                    
                                    $detailUrl = match ($activity->tipe) {
                                        'Invoice' => route('invoice.show', $activity->doc_id),
                                        'Payment' => route('payment_detail.payment', $activity->doc_id),
                                        'Expense' => route('expense.show', $activity->doc_id),
                                        default   => '#'
                                    };
                                @endphp
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($activity->tanggal)->translatedFormat('d M Y') }}</td>
                                    <td><span class="badge {{ $badgeClass }}">{{ $activity->tipe }}</span></td>
                                    <td>
                                        @if ($detailUrl !== '#')
                                            <a href="{{ $detailUrl }}" class="text-body fw-semibold text-primary-hover">{{ $activity->ref }}</a>
                                        @else
                                            {{ $activity->ref }}
                                        @endif
                                    </td>
                                    <td class="text-end">Rp {{ number_format($activity->nominal, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">Belum ada aktivitas.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
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
            const borderColor = isDark ? '#404152' : '#dbdade';

            const formatRp = val => {
                if (val >= 1_000_000_000) return 'Rp ' + (val / 1_000_000_000).toFixed(1) + 'B';
                if (val >= 1_000_000) return 'Rp ' + (val / 1_000_000).toFixed(1) + 'M';
                return 'Rp ' + val.toLocaleString('id-ID');
            };

            // Aging Receivable donut
            const agingEl = document.querySelector('#financeAgingReceivableChart');
            if (agingEl) {
                new ApexCharts(agingEl, {
                    chart: { type: 'donut', height: 260 },
                    labels: ['Current', '1 - 30 Hari', '31 - 60 Hari', '> 60 Hari'],
                    series: @json(array_values($financeAgingBuckets)),
                    colors: ['#71dd37', '#03c3ec', '#ffab00', '#ff3e1d'],
                    legend: { position: 'bottom', labels: { colors: labelColor } },
                    dataLabels: { enabled: true, formatter: (val) => val.toFixed(1) + '%' },
                    tooltip: { y: { formatter: formatRp } },
                }).render();
            }

            // Aging Payable donut
            const agingPayableEl = document.querySelector('#financeAgingPayableChart');
            if (agingPayableEl && @json($financeOutstandingAP) > 0) {
                new ApexCharts(agingPayableEl, {
                    chart: { type: 'donut', height: 260 },
                    labels: ['Current (0-30 Hari)', '31 - 60 Hari', '61 - 90 Hari', '> 90 Hari'],
                    series: @json(array_values($financeAgingPayableBuckets)),
                    colors: ['#71dd37', '#03c3ec', '#ffab00', '#ff3e1d'],
                    legend: { position: 'bottom', labels: { colors: labelColor } },
                    dataLabels: { enabled: true, formatter: (val) => val.toFixed(1) + '%' },
                    tooltip: { y: { formatter: formatRp } },
                }).render();
            }

            // Cash & Bank Balance Donut Chart
            const cashBankEl = document.querySelector('#financeCashBankChart');
            if (cashBankEl && @json($financeCashPosition) > 0) {
                new ApexCharts(cashBankEl, {
                    chart: { type: 'donut', height: 210 },
                    labels: @json($financeBankChartLabels),
                    series: @json($financeBankChartSeries),
                    colors: ['#696cff', '#03c3ec', '#71dd37', '#ffab00', '#8592a3', '#ff3e1d'],
                    legend: { position: 'bottom', labels: { colors: labelColor }, fontSize: '11px' },
                    dataLabels: { enabled: true, formatter: (val) => val.toFixed(1) + '%' },
                    tooltip: { y: { formatter: formatRp } },
                }).render();
            }

            const monthLabels = @json($financeMonthlyLabels);

            // Revenue actual vs target (Sales Target) line chart
            const revenueEl = document.querySelector('#financeRevenueChart');
            const monthlyTarget = @json($financeMonthlyTargetSeries);
            if (revenueEl) {
                const hasTarget = monthlyTarget.some(v => v > 0);
                const revenueSeries = [{ name: 'Actual', type: 'bar', data: @json($financeMonthlyRevenue) }];
                if (hasTarget) {
                    revenueSeries.push({ name: 'Target', type: 'line', data: monthlyTarget });
                }
                new ApexCharts(revenueEl, {
                    chart: { height: 260, toolbar: { show: false } },
                    series: revenueSeries,
                    colors: ['#696cff', '#ff4c51'],
                    plotOptions: { bar: { borderRadius: 4, columnWidth: '50%' } },
                    stroke: { width: hasTarget ? [0, 3] : [0], curve: 'smooth', dashArray: hasTarget ? [0, 5] : [0] },
                    markers: { size: hasTarget ? [0, 4] : [0], strokeWidth: 2, colors: ['#fff'], strokeColors: '#ff4c51' },
                    dataLabels: { enabled: false },
                    legend: hasTarget ? { show: true, position: 'top', labels: { colors: labelColor } } : { show: false },
                    xaxis: { categories: monthLabels, labels: { style: { colors: labelColor } } },
                    yaxis: { labels: { formatter: formatRp, style: { colors: labelColor } } },
                    grid: { borderColor, strokeDashArray: 5 },
                    tooltip: { shared: true, y: { formatter: formatRp } },
                }).render();
            }

            // Expense actual vs budget line chart
            const expenseEl = document.querySelector('#financeExpenseChart');
            const monthlyBudget = @json($financeMonthlyBudgetSeries ?? []);
            if (expenseEl) {
                const hasBudget = Array.isArray(monthlyBudget) && monthlyBudget.some(v => v > 0);
                const expenseSeries = [{ name: 'Actual', type: 'line', data: @json($financeMonthlyExpense) }];
                if (hasBudget) {
                    expenseSeries.push({ name: 'Budget Plafon', type: 'line', data: monthlyBudget });
                }
                new ApexCharts(expenseEl, {
                    chart: { height: 260, toolbar: { show: false } },
                    series: expenseSeries,
                    colors: ['#71dd37', '#ff3e1d'],
                    stroke: { width: hasBudget ? [3, 2] : [3], curve: 'smooth', dashArray: hasBudget ? [0, 5] : [0] },
                    markers: { size: hasBudget ? [3, 0] : [3] },
                    dataLabels: { enabled: false },
                    legend: hasBudget ? { show: true, position: 'top', labels: { colors: labelColor } } : { show: false },
                    xaxis: { categories: monthLabels, labels: { style: { colors: labelColor } } },
                    yaxis: { labels: { formatter: formatRp, style: { colors: labelColor } } },
                    grid: { borderColor, strokeDashArray: 5 },
                    tooltip: { shared: true, y: { formatter: formatRp } },
                }).render();
            }

        })();
    </script>
@endpush
