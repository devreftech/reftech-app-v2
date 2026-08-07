<?php $__env->startSection('title', 'Reports & Overview'); ?>
<?php $__env->startSection('content'); ?>
    <?php
        $bulanMap = [
            1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
            5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
            9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December',
        ];
        $prevMonth = $month == 1 ? 12 : $month - 1;
        $prevYear  = $month == 1 ? $year - 1 : $year;
        $nextMonth = $month == 12 ? 1 : $month + 1;
        $nextYear  = $month == 12 ? $year + 1 : $year;
        $winColor  = $winRate  >= 50 ? 'success' : ($winRate  >= 30 ? 'warning' : 'danger');
        $lossColor = $lossRate <= 20 ? 'success' : ($lossRate <= 40 ? 'warning' : 'danger');
        $achievementColor = $targetAchievement >= 100 ? 'success' : ($targetAchievement >= 70 ? 'warning' : 'danger');
    ?>

    
    <div class="d-flex align-items-start justify-content-between flex-wrap gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-heading">Reports & Overview</h4>
            <span class="text-muted"><?php echo e($bulanMap[$month]); ?> <?php echo e($year); ?> &bull; Company-wide</span>
        </div>

        <div class="d-flex align-items-center gap-2 flex-wrap">
            <a href="<?php echo e(route('report.finance', [$prevYear, $prevMonth])); ?>"
               class="btn btn-sm btn-outline-secondary waves-effect">
                <i class="mdi mdi-chevron-left"></i>
            </a>

            <div class="dropdown">
                <button type="button"
                    class="btn btn-sm btn-outline-secondary dropdown-toggle waves-effect"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    <?php echo e($bulanMap[$month]); ?>

                </button>
                <ul class="dropdown-menu">
                    <?php for($m = 1; $m <= 12; $m++): ?>
                        <li>
                            <a class="dropdown-item waves-effect <?php echo e($m == $month ? 'active' : ''); ?>"
                               href="<?php echo e(route('report.finance', [$year, $m])); ?>">
                                <?php echo e($bulanMap[$m]); ?>

                            </a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </div>

            <div class="dropdown">
                <button type="button"
                    class="btn btn-sm btn-outline-secondary dropdown-toggle waves-effect"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    <?php echo e($year); ?>

                </button>
                <ul class="dropdown-menu">
                    <?php $__currentLoopData = $yearList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $yr): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li>
                            <a class="dropdown-item waves-effect <?php echo e($yr == $year ? 'active' : ''); ?>"
                               href="<?php echo e(route('report.finance', [$yr, $month])); ?>">
                                <?php echo e($yr); ?>

                            </a>
                        </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>

            <a href="<?php echo e(route('report.finance', [$nextYear, $nextMonth])); ?>"
               class="btn btn-sm btn-outline-secondary waves-effect">
                <i class="mdi mdi-chevron-right"></i>
            </a>
        </div>
    </div>

    
    <?php
        $cards = [
            ['label' => 'Purchase Order',    'icon' => 'mdi-cart-plus',      'color' => 'success',
             'amount' => 'Rp ' . number_format($poTotal, 0, ',', '.'),       'sub' => $poCount . ' transactions'],
            ['label' => 'Active Quotation',  'icon' => 'mdi-cart-outline',   'color' => 'primary',
             'amount' => 'Rp ' . number_format($quoteTotal, 0, ',', '.'),    'sub' => $quoteCount . ' quotations'],
            ['label' => 'Loss',              'icon' => 'mdi-cart-minus',     'color' => 'danger',
             'amount' => 'Rp ' . number_format($lossTotal, 0, ',', '.'),     'sub' => $lossCount . ' transactions'],
            ['label' => 'Win Rate',          'icon' => 'mdi-trophy-outline', 'color' => $winColor,
             'amount' => $winRate . '%',     'sub' => $poCount . ' PO of ' . $quoteOnCount . ' quotations'],
            ['label' => 'Loss Rate',         'icon' => 'mdi-trending-down',  'color' => $lossColor,
             'amount' => $lossRate . '%',    'sub' => $lossCount . ' loss of ' . $quoteOnCount . ' quotations'],
            ['label' => 'Target Achievement','icon' => 'mdi-target',        'color' => $achievementColor,
             'amount' => $targetAchievement . '%', 'sub' => $monthlyTarget > 0
                ? 'Rp ' . number_format($poTotal, 0, ',', '.') . ' of Rp ' . number_format($monthlyTarget, 0, ',', '.') . ' (monthly)'
                : 'Target ' . $year . ' belum diset'],
        ];
    ?>
    <div class="row mb-4 g-3">
        <?php $__currentLoopData = $cards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $card): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="col-6 col-md-4 col-lg">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="avatar">
                                <div class="avatar-initial bg-label-<?php echo e($card['color']); ?> rounded">
                                    <i class="mdi <?php echo e($card['icon']); ?> mdi-24px"></i>
                                </div>
                            </div>
                            <div class="text-end">
                                <p class="mb-0 fw-semibold text-heading" style="font-size:0.82rem"><?php echo e($card['label']); ?></p>
                                <small class="text-muted"><?php echo e($card['sub']); ?></small>
                            </div>
                        </div>
                        <h4 class="fw-bold mb-0 text-<?php echo e($card['color']); ?>"><?php echo e($card['amount']); ?></h4>
                    </div>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <div>
                <h5 class="mb-0">PO Trend <?php echo e($year); ?></h5>
                <small class="text-muted">Purchase Order actual per bulan vs rata-rata target bulanan</small>
            </div>
        </div>
        <div class="card-body">
            <div id="financeTrendChart"></div>
        </div>
    </div>

<?php $__env->stopSection(); ?>

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

            const trendLabels = <?php echo json_encode($trendLabels, 15, 512) ?>;
            const trendPoTotal = <?php echo json_encode($trendPoTotal, 15, 512) ?>;
            const monthlyTarget = <?php echo e($trendMonthlyTarget); ?>;
            const trendTarget = trendLabels.map(() => monthlyTarget);

            const chartEl = document.querySelector('#financeTrendChart');
            if (chartEl) {
                new ApexCharts(chartEl, {
                    chart: { height: 320, toolbar: { show: false } },
                    series: [
                        { name: 'PO Actual', type: 'bar', data: trendPoTotal },
                        { name: 'Monthly Target', type: 'line', data: trendTarget },
                    ],
                    colors: ['#696cff', '#ff4c51'],
                    stroke: { width: [0, 2], curve: 'smooth', dashArray: [0, 5] },
                    plotOptions: { bar: { borderRadius: 4, columnWidth: '50%' } },
                    dataLabels: { enabled: false },
                    markers: { size: [0, 4], strokeWidth: 2, colors: ['#fff'], strokeColors: '#ff4c51' },
                    legend: { show: true, position: 'top', labels: { colors: labelColor } },
                    xaxis: {
                        categories: trendLabels,
                        labels: { style: { colors: labelColor, fontSize: '13px' } },
                        axisBorder: { show: false },
                        axisTicks: { show: false },
                    },
                    yaxis: { labels: { formatter: formatRp, style: { colors: labelColor, fontSize: '11px' } } },
                    grid: { borderColor, strokeDashArray: 5 },
                    tooltip: { shared: true, y: { formatter: formatRp } },
                }).render();
            }
        })();
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.sales.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u877155683/domains/reftech.my.id/reftech.my.id/resources/views/pages/finance/reports/index.blade.php ENDPATH**/ ?>