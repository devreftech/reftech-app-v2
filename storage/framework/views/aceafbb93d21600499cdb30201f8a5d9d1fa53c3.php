<h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Dashboard /</span> Workshop</h4>

<?php
    $statusMeta = [
        'OK' => ['label' => 'Unit OK', 'color' => 'success', 'icon' => 'mdi-check-circle-outline'],
        'Rental' => ['label' => 'On Rental', 'color' => 'primary', 'icon' => 'mdi-truck-outline'],
        'Service' => ['label' => 'Service', 'color' => 'warning', 'icon' => 'mdi-wrench-outline'],
        'Breakdown' => ['label' => 'Breakdown', 'color' => 'danger', 'icon' => 'mdi-alert-circle-outline'],
        'Reserved' => ['label' => 'Reserved', 'color' => 'info', 'icon' => 'mdi-bookmark-outline'],
    ];
?>

<!-- KPI Cards -->
<div class="row mb-2">
    <div class="col-sm-6 col-lg-3 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <div class="avatar avatar-md me-3">
                        <div class="avatar-initial bg-label-primary rounded"><i class="mdi mdi-cube-outline mdi-24px"></i></div>
                    </div>
                    <div>
                        <small class="text-muted d-block">Unit Fixed Asset (Mesin)</small>
                        <h5 class="mb-0"><?php echo e($workshopTotalUnit); ?> Total Unit</h5>
                    </div>
                </div>
                <small class="text-muted">Nilai perolehan Rp <?php echo e(number_format($workshopTotalNilaiAset, 0, ',', '.')); ?></small>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <div class="avatar avatar-md me-3">
                        <div class="avatar-initial bg-label-warning rounded"><i class="mdi mdi-wrench-outline mdi-24px"></i></div>
                    </div>
                    <div>
                        <small class="text-muted d-block">Perlu Perhatian</small>
                        <h5 class="mb-0"><?php echo e($workshopStatusCounts['Service'] + $workshopStatusCounts['Breakdown']); ?> Unit</h5>
                    </div>
                </div>
                <small class="text-muted">Service <?php echo e($workshopStatusCounts['Service']); ?> &bull; Breakdown <?php echo e($workshopStatusCounts['Breakdown']); ?></small>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <div class="avatar avatar-md me-3">
                        <div class="avatar-initial bg-label-info rounded"><i class="mdi mdi-clipboard-check-outline mdi-24px"></i></div>
                    </div>
                    <div>
                        <small class="text-muted d-block">Dalam Proses QC</small>
                        <h5 class="mb-0"><?php echo e($workshopQcChecking); ?> Unit</h5>
                    </div>
                </div>
                <small class="text-muted">Lolos QC <?php echo e($workshopQcOk); ?> &bull; Reject <?php echo e($workshopQcReject); ?></small>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <div class="avatar avatar-md me-3">
                        <div class="avatar-initial bg-label-secondary rounded"><i class="mdi mdi-recycle-variant mdi-24px"></i></div>
                    </div>
                    <div>
                        <small class="text-muted d-block">Kondisi Unit</small>
                        <h5 class="mb-0"><?php echo e($workshopKondisiBaru); ?> Baru / <?php echo e($workshopKondisiSecond); ?> Second</h5>
                    </div>
                </div>
                <small class="text-muted">Berdasarkan data acquisition</small>
            </div>
        </div>
    </div>
</div>

<!-- Unit Monitoring Donut + Status Table -->
<div class="row mb-2">
    <div class="col-lg-5 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0">Unit Fixed Asset Monitoring</h5>
                <small class="text-muted">Status ketersediaan unit workshop</small>
            </div>
            <div class="card-body">
                <div id="workshopStatusChart"></div>
                <ul class="list-unstyled mb-0 mt-2">
                    <?php $__currentLoopData = $statusMeta; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $meta): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li class="d-flex align-items-center justify-content-between py-1">
                            <span><i class="mdi <?php echo e($meta['icon']); ?> text-<?php echo e($meta['color']); ?> me-2"></i><?php echo e($meta['label']); ?></span>
                            <span class="fw-semibold"><?php echo e($workshopStatusCounts[$key]); ?></span>
                        </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php if($workshopStatusOther > 0): ?>
                        <li class="d-flex align-items-center justify-content-between py-1">
                            <span><i class="mdi mdi-help-circle-outline text-secondary me-2"></i>Lainnya / Belum Diklasifikasi</span>
                            <span class="fw-semibold"><?php echo e($workshopStatusOther); ?></span>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
    <div class="col-lg-7 mb-4">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0">Unit dengan Status Service / Breakdown</h5>
                <a href="<?php echo e(route('fixed.index')); ?>" class="btn btn-sm btn-outline-primary">Lihat semua unit</a>
            </div>
            <div class="card-body">
                <div class="table-responsive text-nowrap">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Kode Asset</th>
                                <th>Equipment</th>
                                <th>Serial Number</th>
                                <th>Kondisi</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            <?php $__empty_1 = true; $__currentLoopData = $workshopAttentionUnits; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $unit): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td><?php echo e($unit->code); ?></td>
                                    <td><?php echo e($unit->unit ? trim($unit->unit->brand . ' ' . $unit->unit->model) : '-'); ?></td>
                                    <td><?php echo e($unit->serial_number ?: '-'); ?></td>
                                    <td><?php echo e($unit->kondisi ?: '-'); ?></td>
                                    <td>
                                        <span class="badge bg-label-<?php echo e($statusMeta[$unit->status_unit]['color'] ?? 'secondary'); ?>">
                                            <?php echo e($statusMeta[$unit->status_unit]['label'] ?? $unit->status_unit); ?>

                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted">Tidak ada unit dalam status Service / Breakdown.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Unit Terbaru -->
<div class="row mb-2">
    <div class="col-12 mb-4">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0">Unit Fixed Asset Terbaru</h5>
                <a href="<?php echo e(route('unit-acquisition.index')); ?>" class="btn btn-sm btn-outline-primary">Lihat Unit Acquisition</a>
            </div>
            <div class="card-body">
                <div class="table-responsive text-nowrap">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Kode Asset</th>
                                <th>Equipment</th>
                                <th>Kondisi</th>
                                <th>QC Status</th>
                                <th>Status Unit</th>
                                <th>Tanggal Masuk</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            <?php $__empty_1 = true; $__currentLoopData = $workshopRecentUnits; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $unit): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td><?php echo e($unit->code); ?></td>
                                    <td><?php echo e($unit->unit ? trim($unit->unit->brand . ' ' . $unit->unit->model) : '-'); ?></td>
                                    <td><?php echo e($unit->kondisi ?: '-'); ?></td>
                                    <td>
                                        <?php if($unit->qc_status === 'checking'): ?>
                                            <span class="badge bg-label-warning">Dalam Pengecekan</span>
                                        <?php elseif($unit->qc_status === 'ok'): ?>
                                            <span class="badge bg-label-success">OK</span>
                                        <?php elseif($unit->qc_status === 'reject'): ?>
                                            <span class="badge bg-label-danger">Reject</span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if($unit->status_unit): ?>
                                            <span class="badge bg-label-<?php echo e($statusMeta[$unit->status_unit]['color'] ?? 'secondary'); ?>">
                                                <?php echo e($statusMeta[$unit->status_unit]['label'] ?? $unit->status_unit); ?>

                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo e(\Carbon\Carbon::parse($unit->created_at)->translatedFormat('d M Y')); ?></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted">Belum ada data unit.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Vehicle Maintenance -->
<div class="row mb-2">
    <div class="col-12">
        <div class="d-flex align-items-center gap-2 mb-2">
            <h5 class="mb-0">Vehicle Maintenance (Kendaraan)</h5>
        </div>
    </div>
    <div class="col-lg-4 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0">Vehicle Maintenance Overview</h5>
                <small class="text-muted">Servis, STNK/Pajak, Ganti Kaleng 5 Th</small>
            </div>
            <div class="card-body">
                <div id="workshopVehicleChart"></div>
                <ul class="list-unstyled mb-0 mt-2">
                    <li class="d-flex align-items-center justify-content-between py-1">
                        <span><i class="mdi mdi-wrench-outline text-warning me-2"></i>Perlu Servis</span>
                        <span class="fw-semibold"><?php echo e($workshopVehicleServisDue); ?></span>
                    </li>
                    <li class="d-flex align-items-center justify-content-between py-1">
                        <span><i class="mdi mdi-file-document-outline text-info me-2"></i>STNK/Pajak Jatuh Tempo</span>
                        <span class="fw-semibold"><?php echo e($workshopVehiclePajakDue); ?></span>
                    </li>
                    <li class="d-flex align-items-center justify-content-between py-1">
                        <span><i class="mdi mdi-card-account-details-outline text-danger me-2"></i>Ganti Kaleng (5 Th) Jatuh Tempo</span>
                        <span class="fw-semibold"><?php echo e($workshopVehicleKalengDue); ?></span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="col-lg-8 mb-4">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <div>
                    <h5 class="mb-0">Upcoming Schedule Kendaraan</h5>
                    <small class="text-muted"><?php echo e($workshopVehicleOverdueCount); ?> kendaraan overdue perlu segera ditindaklanjuti</small>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive text-nowrap">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Kendaraan</th>
                                <th>Servis Berikutnya</th>
                                <th>STNK/Pajak</th>
                                <th>Ganti Kaleng (5 Th)</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            <?php $__empty_1 = true; $__currentLoopData = $workshopVehicles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td>
                                        <span class="fw-semibold"><?php echo e($v->plat); ?></span>
                                        <div class="text-muted small"><?php echo e($v->jenis); ?></div>
                                    </td>
                                    <td>
                                        <?php echo e($v->servis_berikutnya ? $v->servis_berikutnya->translatedFormat('d M Y') : '-'); ?>

                                        <span class="badge bg-label-<?php echo e($v->servis_status['color']); ?> ms-1"><?php echo e($v->servis_status['label']); ?></span>
                                    </td>
                                    <td>
                                        <?php echo e($v->pajak_due ? $v->pajak_due->translatedFormat('d M Y') : '-'); ?>

                                        <span class="badge bg-label-<?php echo e($v->pajak_status['color']); ?> ms-1"><?php echo e($v->pajak_status['label']); ?></span>
                                    </td>
                                    <td>
                                        <?php echo e($v->ganti_kaleng_due ? $v->ganti_kaleng_due->translatedFormat('d M Y') : '-'); ?>

                                        <span class="badge bg-label-<?php echo e($v->ganti_kaleng_status['color']); ?> ms-1"><?php echo e($v->ganti_kaleng_status['label']); ?></span>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?php echo e($v->overall_status['color']); ?>"><?php echo e($v->overall_status['label']); ?></span>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="5" class="text-center">Belum ada data kendaraan (Fixed Asset kategori Kendaraan).</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
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

            const statusEl = document.querySelector('#workshopStatusChart');
            if (statusEl) {
                const labels = <?php echo json_encode(array_column($statusMeta, 'label'), 512) ?>;
                const series = <?php echo json_encode(array_values($workshopStatusCounts), 15, 512) ?>;
                new ApexCharts(statusEl, {
                    chart: { type: 'donut', height: 260 },
                    labels: labels,
                    series: series,
                    colors: ['#71dd37', '#696cff', '#ffab00', '#ff3e1d', '#03c3ec'],
                    legend: { show: false },
                    dataLabels: { enabled: true, formatter: (val, opts) => opts.w.config.series[opts.seriesIndex] },
                    tooltip: { y: { formatter: (val) => val + ' unit' } },
                    plotOptions: {
                        pie: {
                            donut: {
                                labels: {
                                    show: true,
                                    total: {
                                        show: true,
                                        label: 'Total Unit',
                                        color: labelColor,
                                    },
                                },
                            },
                        },
                    },
                }).render();
            }

            const vehicleEl = document.querySelector('#workshopVehicleChart');
            if (vehicleEl) {
                const vehicleCounts = <?php echo json_encode($workshopVehicleOverviewCounts, 15, 512) ?>;
                new ApexCharts(vehicleEl, {
                    chart: { type: 'donut', height: 220 },
                    labels: Object.keys(vehicleCounts),
                    series: Object.values(vehicleCounts),
                    colors: ['#71dd37', '#ffab00', '#03c3ec', '#ff3e1d'],
                    legend: { position: 'bottom', labels: { colors: labelColor } },
                    dataLabels: { enabled: true, formatter: (val, opts) => opts.w.config.series[opts.seriesIndex] },
                    tooltip: { y: { formatter: (val) => val + ' kendaraan' } },
                }).render();
            }
        })();
    </script>
<?php $__env->stopPush(); ?>
<?php /**PATH /home/u877155683/domains/reftech.my.id/reftech.my.id/resources/views/pages/workshop/dashboard/_content.blade.php ENDPATH**/ ?>