
<?php $__env->startSection('title', 'Overview Sales'); ?>
<?php $__env->startSection('content'); ?>

    <?php
        $now         = \Carbon\Carbon::now();
        $semesterNow = \App\Models\SalesReports::where('semester', $now->month > 6 ? 2 : 1)
                           ->where('year', $now->year)->first();
        $bulanLabel  = $now->locale('id')->translatedFormat('F Y');
    ?>

    
    <div class="d-flex align-items-start justify-content-between flex-wrap gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-label-primary fs-6 px-3 py-2">
                    <i class="mdi mdi-view-dashboard-outline me-1"></i> Dashboard
                </span>
                <span class="text-muted fw-semibold"><?php echo e($bulanLabel); ?></span>
            </div>
            <h4 class="fw-bold mb-1 text-heading">Overview Kinerja Sales</h4>
            <small class="text-muted">Data pencapaian target bulan <?php echo e($bulanLabel); ?></small>
        </div>
        <?php if($semesterNow): ?>
            <a href="<?php echo e(route('report.semester', $semesterNow->id)); ?>"
               class="btn btn-sm btn-outline-primary waves-effect align-self-center">
                <i class="mdi mdi-chart-areaspline me-1"></i> Lihat Report Semester
            </a>
        <?php endif; ?>
    </div>

    
    <div class="row g-3">
        <?php $__currentLoopData = $sales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $sale): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
                $po       = $totalPO[$i] ?? 0;
                $target   = $targett[$i] ?? 0;
                $pct      = $target > 0 ? round(($po / $target) * 100, 1) : 0;
                $pctColor = $pct >= 100 ? 'success' : ($pct >= 80 ? 'warning' : 'danger');
                $barWidth = min($pct, 100);
                $forecast = $totalForecast[$i] ?? '0';

                $stats = [
                    ['icon' => 'mdi-account-multiple-plus-outline', 'color' => 'secondary', 'val' => $filteredLeads[$i] ?? 0, 'label' => 'Leads'],
                    ['icon' => 'mdi-phone-outline',                  'color' => 'info',      'val' => $filteredDC[$i]    ?? 0, 'label' => 'Daily Call'],
                    ['icon' => 'mdi-account-multiple-outline',       'color' => 'primary',   'val' => $filteredCRM[$i]   ?? 0, 'label' => 'CRM'],
                    ['icon' => 'mdi-map-marker-outline',             'color' => 'warning',   'val' => $filteredVisit[$i] ?? 0, 'label' => 'Visit'],
                    ['icon' => 'mdi-email-multiple-outline',         'color' => 'info',      'val' => $filteredQuote[$i] ?? 0, 'label' => 'Quotation'],
                    ['icon' => 'mdi-cart-plus',                      'color' => 'success',   'val' => $filteredPO[$i]    ?? 0, 'label' => 'PO'],
                ];
            ?>
            <div class="col-12 col-lg-6">
                <div class="card h-100">
                    <div class="card-body pb-2">

                        
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="d-flex align-items-center gap-3">
                                <img src="<?php echo e(url('') . '/' . $sale->image); ?>" alt="<?php echo e($sale->name); ?>"
                                    class="rounded-circle border"
                                    style="width:48px;height:48px;object-fit:cover;">
                                <div>
                                    <h5 class="mb-0 fw-semibold"><?php echo e($sale->name); ?></h5>
                                    <small class="text-muted">Target: Rp <?php echo e(number_format($target, 0, ',', '.')); ?></small>
                                </div>
                            </div>
                            <span class="badge bg-<?php echo e($pctColor); ?> rounded-pill px-3 py-2 fs-6">
                                <?php echo e($pct); ?>%
                            </span>
                        </div>

                        
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <small class="text-muted fw-semibold">Total PO Bulan Ini</small>
                                <small class="fw-bold text-<?php echo e($pctColor); ?>">
                                    Rp <?php echo e(number_format($po, 0, ',', '.')); ?>

                                </small>
                            </div>
                            <div class="progress" style="height:8px;border-radius:4px;">
                                <div class="progress-bar bg-<?php echo e($pctColor); ?>"
                                     role="progressbar"
                                     style="width:<?php echo e($barWidth); ?>%;border-radius:4px;"
                                     aria-valuenow="<?php echo e($barWidth); ?>" aria-valuemin="0" aria-valuemax="100">
                                </div>
                            </div>
                        </div>

                        
                        <div class="d-flex align-items-center gap-2 px-3 py-2 rounded mb-3"
                             style="background:rgba(105,108,255,.06);">
                            <i class="mdi mdi-trending-up text-primary"></i>
                            <small class="text-muted">Forecast</small>
                            <small class="fw-semibold ms-auto">Rp <?php echo e($forecast); ?></small>
                        </div>

                        
                        <div class="row g-2">
                            <?php $__currentLoopData = $stats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="col-4">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="avatar avatar-sm">
                                            <div class="avatar-initial bg-label-<?php echo e($stat['color']); ?> rounded">
                                                <i class="mdi <?php echo e($stat['icon']); ?> mdi-18px"></i>
                                            </div>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 fw-bold"><?php echo e($stat['val']); ?></h6>
                                            <small class="text-muted" style="font-size:0.7rem;"><?php echo e($stat['label']); ?></small>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>

                    </div>

                    
                    <?php if($semesterNow): ?>
                        <div class="card-footer py-2">
                            <a href="<?php echo e(route('overview-sales.semester', [$semesterNow->id, $sale->id])); ?>"
                               class="btn btn-sm btn-outline-primary w-100 waves-effect">
                                <i class="mdi mdi-eye-outline me-1"></i> Lihat Detail
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.sales.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u877155683/domains/reftech.my.id/reftech.my.id/resources/views/pages/admin/overview.blade.php ENDPATH**/ ?>