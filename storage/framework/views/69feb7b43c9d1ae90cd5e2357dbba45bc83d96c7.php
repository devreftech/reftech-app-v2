<h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Dashboard /</span> Finance</h4>

<!-- KPI Cards -->
<div class="row mb-2">
    <div class="col-sm-6 col-lg-3 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <div class="avatar avatar-md me-3">
                        <div class="avatar-initial bg-label-success rounded"><i class="mdi mdi-wallet-outline mdi-24px"></i></div>
                    </div>
                    <div>
                        <small class="text-muted d-block">Cash Position</small>
                        <h5 class="mb-0">Total Cash Available</h5>
                    </div>
                </div>
                <span class="badge bg-label-secondary">Under Development</span>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <div class="avatar avatar-md me-3">
                        <div class="avatar-initial bg-label-info rounded"><i class="mdi mdi-account-cash-outline mdi-24px"></i></div>
                    </div>
                    <div>
                        <small class="text-muted d-block">Outstanding AR (Piutang)</small>
                        <h5 class="mb-0">Rp <?php echo e(number_format($financeOutstandingAR, 0, ',', '.')); ?></h5>
                    </div>
                </div>
                <small class="text-muted">Total Tempo payment belum lunas</small>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <div class="avatar avatar-md me-3">
                        <div class="avatar-initial bg-label-warning rounded"><i class="mdi mdi-file-document-outline mdi-24px"></i></div>
                    </div>
                    <div>
                        <small class="text-muted d-block">Outstanding AP (Hutang)</small>
                        <h5 class="mb-0">Total Outstanding</h5>
                    </div>
                </div>
                <span class="badge bg-label-secondary">Under Development</span>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <div class="avatar avatar-md me-3">
                        <div class="avatar-initial bg-label-primary rounded"><i class="mdi mdi-chart-line mdi-24px"></i></div>
                    </div>
                    <div>
                        <small class="text-muted d-block">Net Profit (YTD)</small>
                        <h5 class="mb-0">Rp <?php echo e(number_format($financeNetProfitYTD, 0, ',', '.')); ?></h5>
                    </div>
                </div>
                <small class="text-muted">Margin <?php echo e($financeMarginYTD); ?>%</small>
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
                <small class="text-muted">Rp <?php echo e(number_format($financeOutstandingAR, 0, ',', '.')); ?></small>
            </div>
            <div class="card-body">
                <div id="financeAgingReceivableChart"></div>
            </div>
        </div>
    </div>
    <div class="col-lg-6 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0">Aging Payable (Hutang)</h5>
            </div>
            <div class="card-body d-flex align-items-center justify-content-center" style="min-height: 260px;">
                <span class="badge bg-label-secondary">Under Development</span>
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
                <?php if($financeMonthlyTarget > 0): ?>
                    <small class="text-muted">
                        Target bulan ini Rp <?php echo e(number_format($financeMonthlyTarget, 0, ',', '.')); ?>

                        &bull;
                        <span class="badge bg-label-<?php echo e($financeRevenueAchievement >= 100 ? 'success' : ($financeRevenueAchievement >= 70 ? 'warning' : 'danger')); ?>">
                            <?php echo e($financeRevenueAchievement); ?>% achieved
                        </span>
                    </small>
                <?php else: ?>
                    <small class="text-muted">Actual bulanan (Target: <span class="badge bg-label-secondary">belum diset di Sales Target</span>)</small>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <div id="financeRevenueChart"></div>
            </div>
        </div>
    </div>
    <div class="col-lg-4 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0">Expense</h5>
                <small class="text-muted">Actual bulanan (Budget: <span class="badge bg-label-secondary">Under Development</span>)</small>
            </div>
            <div class="card-body">
                <div id="financeExpenseChart"></div>
            </div>
        </div>
    </div>
    <div class="col-lg-4 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0">Cash & Bank Balance</h5>
            </div>
            <div class="card-body d-flex align-items-center justify-content-center" style="min-height: 260px;">
                <span class="badge bg-label-secondary">Under Development</span>
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
                <small class="text-muted">YTD (Jan - <?php echo e(\Carbon\Carbon::now()->translatedFormat('M Y')); ?>)</small>
            </div>
            <div class="card-body">
                <table class="table table-borderless mb-0">
                    <tbody>
                        <tr>
                            <td class="ps-0">Revenue</td>
                            <td class="text-end pe-0">Rp <?php echo e(number_format($financeRevenueYTD, 0, ',', '.')); ?></td>
                        </tr>
                        <tr>
                            <td class="ps-0">COGS</td>
                            <td class="text-end pe-0">(Rp <?php echo e(number_format($financeCOGSYTD, 0, ',', '.')); ?>)</td>
                        </tr>
                        <tr class="border-top">
                            <td class="ps-0 fw-semibold">Gross Profit</td>
                            <td class="text-end pe-0 fw-semibold text-success">Rp <?php echo e(number_format($financeGrossProfitYTD, 0, ',', '.')); ?></td>
                        </tr>
                        <tr>
                            <td class="ps-0">Expense</td>
                            <td class="text-end pe-0">(Rp <?php echo e(number_format($financeExpenseYTD, 0, ',', '.')); ?>)</td>
                        </tr>
                        <tr class="border-top">
                            <td class="ps-0 fw-bold">Net Profit</td>
                            <td class="text-end pe-0 fw-bold text-success">Rp <?php echo e(number_format($financeNetProfitYTD, 0, ',', '.')); ?></td>
                        </tr>
                        <tr>
                            <td class="ps-0">Margin</td>
                            <td class="text-end pe-0"><?php echo e($financeMarginYTD); ?>%</td>
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
                            <?php $__empty_1 = true; $__currentLoopData = $financeRecentActivity; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $activity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td><?php echo e(\Carbon\Carbon::parse($activity->tanggal)->translatedFormat('d M Y')); ?></td>
                                    <td><span class="badge bg-label-primary"><?php echo e($activity->tipe); ?></span></td>
                                    <td><?php echo e($activity->ref); ?></td>
                                    <td class="text-end">Rp <?php echo e(number_format($activity->nominal, 0, ',', '.')); ?></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="4" class="text-center text-muted">Belum ada aktivitas.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
    $chartLabels = [];
    $chartSeries = [];
    $top5Sum = 0;
    
    foreach ($financeKeyAccounts->take(5) as $ka) {
        $chartLabels[] = $ka->company;
        $chartSeries[] = (int)$ka->total_po;
        $top5Sum += $ka->total_po;
    }
    
    $othersSum = $financeRevenueYTD - $top5Sum;
    if ($othersSum > 0) {
        $chartLabels[] = 'Lainnya';
        $chartSeries[] = (int)$othersSum;
    }
?>

<!-- Key Accounts Leaderboard -->
<div class="row mb-2">
    <div class="col-lg-8 mb-4">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0">Top 10 Key Accounts (YTD)</h5>
                <small class="text-muted">Tahun <?php echo e(\Carbon\Carbon::now()->year); ?></small>
            </div>
            <div class="card-body">
                <div class="table-responsive text-nowrap">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Pelanggan</th>
                                <th class="text-end">Total PO (Value)</th>
                                <th class="text-center">Jumlah PO</th>
                                <th class="text-end">Kontribusi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $financeKeyAccounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $ka): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td><?php echo e($index + 1); ?></td>
                                    <td>
                                        <a href="<?php echo e(route('detail.customers', $ka->id)); ?>" class="fw-semibold">
                                            <?php echo e($ka->company); ?>

                                        </a>
                                    </td>
                                    <td class="text-end text-success fw-semibold">
                                        Rp <?php echo e(number_format($ka->total_po, 0, ',', '.')); ?>

                                    </td>
                                    <td class="text-center"><?php echo e($ka->count_po); ?></td>
                                    <td class="text-end">
                                        <?php echo e($financeRevenueYTD > 0 ? round(($ka->total_po / $financeRevenueYTD) * 100, 1) : 0); ?>%
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted">Belum ada data transaksi.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0">Key Accounts Contribution</h5>
                <small class="text-muted">Pangsa Pendapatan YTD</small>
            </div>
            <div class="card-body d-flex flex-column justify-content-between" style="min-height: 290px;">
                <?php if($financeRevenueYTD > 0): ?>
                    <div id="financeKeyAccountsChart" class="my-auto"></div>
                <?php else: ?>
                    <div class="text-center text-muted my-auto">Belum ada data pendapatan tahunan.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>


<?php $__env->startPush('before-style'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/apex-charts/apex-charts.css" />
<?php $__env->stopPush(); ?>

<?php $__env->startPush('page-script'); ?>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/apex-charts/apexcharts.js"></script>
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
                    series: <?php echo json_encode(array_values($financeAgingBuckets), 15, 512) ?>,
                    colors: ['#71dd37', '#03c3ec', '#ffab00', '#ff3e1d'],
                    legend: { position: 'bottom', labels: { colors: labelColor } },
                    dataLabels: { enabled: true, formatter: (val) => val.toFixed(1) + '%' },
                    tooltip: { y: { formatter: formatRp } },
                }).render();
            }

            const monthLabels = <?php echo json_encode($financeMonthlyLabels, 15, 512) ?>;

            // Revenue actual vs target (Sales Target) line chart
            const revenueEl = document.querySelector('#financeRevenueChart');
            const monthlyTarget = <?php echo json_encode($financeMonthlyTargetSeries, 15, 512) ?>;
            if (revenueEl) {
                const hasTarget = monthlyTarget.some(v => v > 0);
                const revenueSeries = [{ name: 'Actual', type: 'bar', data: <?php echo json_encode($financeMonthlyRevenue, 15, 512) ?> }];
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

            // Expense actual line chart
            const expenseEl = document.querySelector('#financeExpenseChart');
            if (expenseEl) {
                new ApexCharts(expenseEl, {
                    chart: { type: 'line', height: 260, toolbar: { show: false } },
                    series: [{ name: 'Actual', data: <?php echo json_encode($financeMonthlyExpense, 15, 512) ?> }],
                    colors: ['#71dd37'],
                    stroke: { curve: 'smooth', width: 3 },
                    dataLabels: { enabled: false },
                    xaxis: { categories: monthLabels, labels: { style: { colors: labelColor } } },
                    yaxis: { labels: { formatter: formatRp, style: { colors: labelColor } } },
                    grid: { borderColor, strokeDashArray: 5 },
                    tooltip: { y: { formatter: formatRp } },
                }).render();
            }

            // Key Accounts contribution chart
            const keyAccountsEl = document.querySelector('#financeKeyAccountsChart');
            if (keyAccountsEl && <?php echo json_encode($financeRevenueYTD, 15, 512) ?> > 0) {
                new ApexCharts(keyAccountsEl, {
                    chart: { type: 'donut', height: 260 },
                    labels: <?php echo json_encode($chartLabels, 15, 512) ?>,
                    series: <?php echo json_encode($chartSeries, 15, 512) ?>,
                    colors: ['#696cff', '#03c3ec', '#71dd37', '#ffab00', '#ff3e1d', '#8592a3'],
                    legend: { position: 'bottom', labels: { colors: labelColor } },
                    dataLabels: { enabled: true, formatter: (val) => val.toFixed(1) + '%' },
                    tooltip: { y: { formatter: formatRp } },
                }).render();
            }
        })();
    </script>
<?php $__env->stopPush(); ?>
<?php /**PATH /home/u877155683/domains/reftech.my.id/reftech.my.id/resources/views/pages/finance/dashboard/_content.blade.php ENDPATH**/ ?>