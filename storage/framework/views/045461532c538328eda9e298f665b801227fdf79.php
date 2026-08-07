
<?php $__env->startSection('title', 'report'); ?>
<?php $__env->startSection('content'); ?>
    <?php if(Auth::user()->role == 'Sales'): ?>
        <?php
            $user = Auth::user();
            $lastDetail = $user->detail->last();
            $userArea = $lastDetail->area ?? ($user->latestRole->area ?? 'Sales Area');
            $targetTotal = $target->total ?? 0;
            $salesPercentage = $targetTotal > 0 ? round(($amountSales / $targetTotal) * 100, 1) : 0;
            $salesColor = $salesPercentage >= 100 ? 'success' : ($salesPercentage >= 80 ? 'warning' : 'danger');
            $today = \Carbon\Carbon::now();
        ?>

        <!-- Executive Header Card with Filter Options (Matching Overview Page Style) -->
        <div class="card clean-card mb-4 overflow-hidden position-relative">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="badge bg-label-primary fs-6 px-3 py-2">
                                <i class="mdi mdi-calendar-text me-1"></i> Periode Laporan
                            </span>
                            <span class="text-muted fw-semibold fs-5">
                                <?php echo e(\Carbon\Carbon::createFromDate($yearNow, $monthNow, 1)->locale('id')->translatedFormat('F Y')); ?>

                            </span>
                        </div>
                        <h4 class="fw-bold mb-0 text-dark">Executive Sales Performance Report</h4>
                        <small class="text-muted">
                            Laporan Performa & Operational KPI &mdash; <strong><?php echo e($user->name); ?></strong> (<?php echo e($userArea); ?>)
                        </small>
                    </div>

                    <!-- Filter Options in 1 Single Bar: Prev | Selection Bulan | Selection Tahun | Next -->
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <form action="<?php echo e(url('/reports')); ?>" method="GET" id="filterReportForm" class="d-flex align-items-center flex-nowrap mb-0 gap-2">
                            <div class="input-group input-group-sm flex-nowrap">
                                <!-- Prev Month Button -->
                                <a href="<?php echo e(url('/reports')); ?>?month=<?php echo e($prevMonth); ?>&year=<?php echo e($prevYear); ?>" 
                                   class="btn btn-outline-primary waves-effect" 
                                   data-bs-toggle="tooltip" title="Bulan Sebelumnya">
                                    <i class="mdi mdi-chevron-left me-1"></i> Prev
                                </a>

                                <!-- Month Select Dropdown -->
                                <select name="month" class="form-select border-primary text-primary fw-semibold" style="min-width: 125px;" onchange="document.getElementById('filterReportForm').submit()">
                                    <?php for($m = 1; $m <= 12; $m++): ?>
                                        <option value="<?php echo e($m); ?>" <?php echo e($m == $monthNow ? 'selected' : ''); ?>>
                                            <?php echo e(\Carbon\Carbon::createFromDate(2026, $m, 1)->locale('id')->translatedFormat('F')); ?>

                                        </option>
                                    <?php endfor; ?>
                                </select>

                                <!-- Year Select Dropdown -->
                                <select name="year" class="form-select border-primary text-primary fw-semibold" style="min-width: 95px;" onchange="document.getElementById('filterReportForm').submit()">
                                    <?php $__currentLoopData = $yearsList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $y): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($y); ?>" <?php echo e($y == $yearNow ? 'selected' : ''); ?>>
                                            <?php echo e($y); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>

                                <!-- Next Month Button -->
                                <a href="<?php echo e(url('/reports')); ?>?month=<?php echo e($nextMonth); ?>&year=<?php echo e($nextYear); ?>" 
                                   class="btn btn-outline-primary waves-effect" 
                                   data-bs-toggle="tooltip" title="Bulan Berikutnya">
                                    Next <i class="mdi mdi-chevron-right ms-1"></i>
                                </a>
                            </div>

                            <?php if($monthNow != now()->month || $yearNow != now()->year): ?>
                                <a href="<?php echo e(url('/reports')); ?>" class="btn btn-sm btn-outline-secondary waves-effect" data-bs-toggle="tooltip" title="Kembali ke Bulan Sekarang">
                                    <i class="mdi mdi-reload me-1"></i> Sekarang
                                </a>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>

                <hr class="my-3 text-muted opacity-25">

                <!-- Sales Target & Profile Info Row -->
                <div class="row align-items-center gy-3 pt-1">
                    <div class="col-12 col-md-auto text-center text-md-start">
                        <div class="position-relative d-inline-block">
                            <img src="<?php echo e(url('') . '/' . $user->image); ?>" alt="<?php echo e($user->name); ?>"
                                class="rounded-circle border border-3 border-primary shadow-xs"
                                style="width: 68px; height: 68px; object-fit: cover;">
                            <span class="position-absolute bottom-0 end-0 p-1 bg-success border border-2 border-white rounded-circle">
                                <span class="visually-hidden">Online</span>
                            </span>
                        </div>
                    </div>
                    <div class="col-12 col-md">
                        <div class="d-flex align-items-center gap-2 flex-wrap mb-1 justify-content-center justify-content-md-start">
                            <h5 class="mb-0 fw-bold text-dark"><?php echo e($user->name); ?></h5>
                            <span class="badge bg-label-primary rounded-pill px-3 py-1">
                                <i class="mdi mdi-map-marker-outline me-1"></i> <?php echo e($userArea); ?>

                            </span>
                        </div>
                        <p class="text-muted mb-0 small text-center text-md-start">
                            Target Bulanan & Pencapaian Sales &mdash; <strong><?php echo e(\Carbon\Carbon::createFromDate($yearNow, $monthNow, 1)->locale('id')->translatedFormat('F Y')); ?></strong>
                        </p>
                    </div>
                    <div class="col-12 col-md-auto text-center text-md-end border-start-md ps-md-4">
                        <small class="text-muted fw-semibold d-block mb-1">Target Sales Bulanan</small>
                        <h4 class="text-primary fw-bold mb-0">Rp <?php echo e(number_format($targetTotal, 0, ',', '.')); ?></h4>
                        <span class="badge bg-label-<?php echo e($salesColor); ?> rounded-pill px-3 py-1 mt-1">
                            Pencapaian: <?php echo e($salesPercentage); ?>%
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="row gy-4 mb-4">
            <!-- Left Column: KPI Operational Micro Cards -->
            <div class="col-12 col-xl-7">
                <div class="card clean-card h-100">
                    <div class="card-header d-flex align-items-center justify-content-between pb-2">
                        <h5 class="card-title mb-0 d-flex align-items-center gap-2">
                            <i class="mdi mdi-chart-box-outline text-primary fs-4"></i> Operational KPI Summary
                        </h5>
                        <small class="text-muted">Target Performa Bulan Ini</small>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <!-- New Leads -->
                            <?php
                                $tLeads = $target->leads ?? 0;
                                $pLeads = $tLeads > 0 ? round(($totalLeads / $tLeads) * 100) : 0;
                            ?>
                            <div class="col-6 col-md-4">
                                <a href="#activities" class="text-decoration-none">
                                    <div class="p-3 border rounded-3 bg-body-tertiary h-100 transition-all hover-shadow">
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <span class="badge bg-label-secondary p-2 rounded-circle">
                                                <i class="mdi mdi-account-multiple-plus-outline fs-5"></i>
                                            </span>
                                            <span class="badge bg-label-secondary rounded-pill fs-tiny"><?php echo e($pLeads); ?>%</span>
                                        </div>
                                        <small class="text-muted d-block fw-semibold" style="font-size: 0.78rem;">New Leads</small>
                                        <div class="d-flex align-items-baseline gap-1 mt-1">
                                            <h5 class="mb-0 fw-bold text-dark"><?php echo e($totalLeads); ?></h5>
                                            <small class="text-muted" style="font-size: 0.7rem;">/ <?php echo e($tLeads); ?></small>
                                        </div>
                                    </div>
                                </a>
                            </div>

                            <!-- Daily Call -->
                            <?php
                                $tDc = $target->dc ?? 0;
                                $pDc = $tDc > 0 ? round(($totalDC / $tDc) * 100) : 0;
                            ?>
                            <div class="col-6 col-md-4">
                                <a href="#activities" class="text-decoration-none">
                                    <div class="p-3 border rounded-3 bg-body-tertiary h-100 transition-all hover-shadow">
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <span class="badge bg-label-info p-2 rounded-circle">
                                                <i class="mdi mdi-phone-outline fs-5"></i>
                                            </span>
                                            <span class="badge bg-label-info rounded-pill fs-tiny"><?php echo e($pDc); ?>%</span>
                                        </div>
                                        <small class="text-muted d-block fw-semibold" style="font-size: 0.78rem;">Daily Call</small>
                                        <div class="d-flex align-items-baseline gap-1 mt-1">
                                            <h5 class="mb-0 fw-bold text-dark"><?php echo e($totalDC); ?></h5>
                                            <small class="text-muted" style="font-size: 0.7rem;">/ <?php echo e($tDc); ?></small>
                                        </div>
                                    </div>
                                </a>
                            </div>

                            <!-- CRM -->
                            <?php
                                $tCrm = $target->crm ?? 0;
                                $pCrm = $tCrm > 0 ? round(($totalCRM / $tCrm) * 100) : 0;
                            ?>
                            <div class="col-6 col-md-4">
                                <a href="#activities" class="text-decoration-none">
                                    <div class="p-3 border rounded-3 bg-body-tertiary h-100 transition-all hover-shadow">
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <span class="badge bg-label-primary p-2 rounded-circle">
                                                <i class="mdi mdi-account-multiple-outline fs-5"></i>
                                            </span>
                                            <span class="badge bg-label-primary rounded-pill fs-tiny"><?php echo e($pCrm); ?>%</span>
                                        </div>
                                        <small class="text-muted d-block fw-semibold" style="font-size: 0.78rem;">CRM</small>
                                        <div class="d-flex align-items-baseline gap-1 mt-1">
                                            <h5 class="mb-0 fw-bold text-dark"><?php echo e($totalCRM); ?></h5>
                                            <small class="text-muted" style="font-size: 0.7rem;">/ <?php echo e($tCrm); ?></small>
                                        </div>
                                    </div>
                                </a>
                            </div>

                            <!-- Visit (if applicable) -->
                            <?php if(in_array($userArea, ['Bekasi', 'Jabodetabek', 'Jawa Barat'])): ?>
                                <?php
                                    $tVisit = $target->visit ?? 0;
                                    $pVisit = $tVisit > 0 ? round(($totalVisit / $tVisit) * 100) : 0;
                                ?>
                                <div class="col-6 col-md-4">
                                    <a href="#activities" class="text-decoration-none">
                                        <div class="p-3 border rounded-3 bg-body-tertiary h-100 transition-all hover-shadow">
                                            <div class="d-flex align-items-center justify-content-between mb-2">
                                                <span class="badge bg-label-danger p-2 rounded-circle">
                                                    <i class="mdi mdi-office-building-marker-outline fs-5"></i>
                                                </span>
                                                <span class="badge bg-label-danger rounded-pill fs-tiny"><?php echo e($pVisit); ?>%</span>
                                            </div>
                                            <small class="text-muted d-block fw-semibold" style="font-size: 0.78rem;">Visit</small>
                                            <div class="d-flex align-items-baseline gap-1 mt-1">
                                                <h5 class="mb-0 fw-bold text-dark"><?php echo e($totalVisit); ?></h5>
                                                <small class="text-muted" style="font-size: 0.7rem;">/ <?php echo e($tVisit); ?></small>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            <?php endif; ?>

                            <!-- Quotation -->
                            <?php
                                $tQuote = $target->quote ?? 0;
                                $pQuote = $tQuote > 0 ? round(($totalQuote / $tQuote) * 100) : 0;
                            ?>
                            <div class="col-6 col-md-4">
                                <a href="#activities" class="text-decoration-none">
                                    <div class="p-3 border rounded-3 bg-body-tertiary h-100 transition-all hover-shadow">
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <span class="badge bg-label-warning p-2 rounded-circle">
                                                <i class="mdi mdi-email-multiple-outline fs-5"></i>
                                            </span>
                                            <span class="badge bg-label-warning rounded-pill fs-tiny"><?php echo e($pQuote); ?>%</span>
                                        </div>
                                        <small class="text-muted d-block fw-semibold" style="font-size: 0.78rem;">Quotation</small>
                                        <div class="d-flex align-items-baseline gap-1 mt-1">
                                            <h5 class="mb-0 fw-bold text-dark"><?php echo e($totalQuote); ?></h5>
                                            <small class="text-muted" style="font-size: 0.7rem;">/ <?php echo e($tQuote); ?></small>
                                        </div>
                                    </div>
                                </a>
                            </div>

                            <!-- Purchase Order -->
                            <div class="col-6 col-md-4">
                                <a href="#po" class="text-decoration-none">
                                    <div class="p-3 border rounded-3 bg-body-tertiary h-100 transition-all hover-shadow">
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <span class="badge bg-label-success p-2 rounded-circle">
                                                <i class="mdi mdi-cart-plus fs-5"></i>
                                            </span>
                                            <span class="badge bg-label-success rounded-pill fs-tiny">Closed</span>
                                        </div>
                                        <small class="text-muted d-block fw-semibold" style="font-size: 0.78rem;">Purchase Order</small>
                                        <div class="d-flex align-items-baseline gap-1 mt-1">
                                            <h5 class="mb-0 fw-bold text-success"><?php echo e($totalPO); ?></h5>
                                            <small class="text-muted" style="font-size: 0.7rem;">PO Closed</small>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Financial Achievement Summary -->
            <div class="col-12 col-xl-5">
                <div class="card clean-card h-100 d-flex flex-column justify-content-between">
                    <div class="card-header pb-2">
                        <div class="d-flex align-items-center justify-content-between">
                            <h5 class="card-title mb-0 d-flex align-items-center gap-2">
                                <i class="mdi mdi-cash-multiple text-success fs-4"></i> Financial Pipeline
                            </h5>
                            <span class="badge bg-label-success rounded-pill px-3 py-1">Pencapaian Omset</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Premium Clean Design Card -->
                        <div class="p-3 border rounded-3 bg-body-tertiary mb-3 position-relative overflow-hidden"
                             style="border-left: 4px solid #10b981 !important;">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-label-success p-2 rounded-circle">
                                        <i class="mdi mdi-check-circle-outline fs-5"></i>
                                    </span>
                                    <div>
                                        <small class="text-uppercase fw-bold text-muted d-block" style="font-size: 0.68rem; letter-spacing: 0.6px;">
                                            Total Closed Sales (PO)
                                        </small>
                                    </div>
                                </div>
                                <span class="badge bg-label-<?php echo e($salesColor); ?> rounded-pill px-3 py-1 fw-bold fs-tiny">
                                    <i class="mdi mdi-trending-up me-1"></i> <?php echo e($salesPercentage); ?>% Target
                                </span>
                            </div>

                            <div class="my-2">
                                <h3 class="fw-bold text-dark mb-0" style="font-size: 1.6rem; letter-spacing: -0.5px;">
                                    Rp <?php echo e(number_format($amountSales, 0, ',', '.')); ?>

                                </h3>
                            </div>

                            <!-- Clean Progress Bar -->
                            <div class="pt-1">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <small class="text-muted" style="font-size: 0.7rem;">Pencapaian Target Sales</small>
                                    <small class="fw-bold text-<?php echo e($salesColor); ?>" style="font-size: 0.7rem;"><?php echo e($salesPercentage); ?>%</small>
                                </div>
                                <div class="progress" style="height: 6px; border-radius: 4px; background-color: rgba(0,0,0,0.06);">
                                    <div class="progress-bar bg-<?php echo e($salesColor); ?>" style="width: <?php echo e(min($salesPercentage, 100)); ?>%; border-radius: 4px;"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Pipeline breakdown micro list -->
                        <div class="d-flex flex-column gap-2">
                            <div class="d-flex align-items-center justify-content-between p-3 border rounded-3 bg-body-tertiary">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-label-primary p-2 rounded"><i class="mdi mdi-email-multiple-outline"></i></span>
                                    <div>
                                        <h6 class="mb-0 fw-bold text-dark" style="font-size: 0.85rem;">Quotation Pipeline</h6>
                                        <small class="text-muted" style="font-size: 0.7rem;">Nilai Penawaran Terbit</small>
                                    </div>
                                </div>
                                <span class="fw-bold text-dark" style="font-size: 0.9rem;">
                                    Rp <?php echo e(number_format($amountQuote, 0, ',', '.')); ?>

                                </span>
                            </div>

                            <div class="d-flex align-items-center justify-content-between p-3 border rounded-3 bg-body-tertiary">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-label-warning p-2 rounded"><i class="mdi mdi-email-alert-outline"></i></span>
                                    <div>
                                        <h6 class="mb-0 fw-bold text-dark" style="font-size: 0.85rem;">Hot Prospect Value</h6>
                                        <small class="text-muted" style="font-size: 0.7rem;">Estimasi Closing Prospek</small>
                                    </div>
                                </div>
                                <span class="fw-bold text-warning" style="font-size: 0.9rem;">
                                    Rp <?php echo e(number_format($amountProspect, 0, ',', '.')); ?>

                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Weekly Activity Matrix Card -->
        <div class="card clean-card mb-4" id="activities">
            <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                <h5 class="card-title mb-0 d-flex align-items-center gap-2">
                    <i class="mdi mdi-calendar-range text-primary fs-4"></i> Detail Aktivitas Mingguan (Weekly Performance Matrix)
                </h5>
                <span class="badge bg-label-primary px-3 py-1 rounded-pill">
                    User: <?php echo e(Auth::user()->name); ?>

                </span>
            </div>
            <div class="table-responsive text-nowrap">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="fw-bold text-dark">Deskripsi Aktivitas</th>
                            <?php
                                $allWeek = 1;
                            ?>
                            <?php $__currentLoopData = $dataQuote; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $week): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <th class="text-center fw-bold">
                                    <span class="badge bg-label-secondary px-3 py-1 rounded-pill">Minggu <?php echo e($allWeek); ?></span>
                                </th>
                                <?php
                                    $allWeek += 1;
                                ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <th class="text-center fw-bold">Total</th>
                            <th class="text-center fw-bold">Persentase Target</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        <!-- New Leads Row -->
                        <tr>
                            <td class="fw-semibold text-dark">
                                <span class="badge bg-label-secondary p-1 rounded me-2"><i class="mdi mdi-account-multiple-plus-outline"></i></span>
                                New Leads
                            </td>
                            <?php
                                $totalLeadsFullWeek = 0;
                            ?>
                            <?php $__currentLoopData = $dataLeads; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $week): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <td class="text-center font-monospace"><?php echo e($week['total']); ?></td>
                                <?php
                                    $totalLeadsFullWeek += $week['total'];
                                ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <td class="text-center fw-bold text-dark"><?php echo e($totalLeadsFullWeek); ?></td>
                            <td class="text-center">
                                <?php
                                    $targetL = $target->leads ?? 0;
                                    $denomL = is_array($dataLeads) && count($dataLeads) > 4 ? ($targetL + $targetL / 4) : $targetL;
                                    $pctL = $denomL > 0 ? round(($totalLeadsFullWeek / $denomL) * 100) : 0;
                                    $colorL = $pctL >= 100 ? 'success' : ($pctL >= 80 ? 'warning' : 'danger');
                                ?>
                                <span class="badge bg-label-<?php echo e($colorL); ?> rounded-pill px-3 py-1 fw-bold"><?php echo e($pctL); ?>%</span>
                            </td>
                        </tr>

                        <!-- Daily Call Row -->
                        <tr>
                            <td class="fw-semibold text-dark">
                                <span class="badge bg-label-info p-1 rounded me-2"><i class="mdi mdi-phone-outline"></i></span>
                                Daily Call
                            </td>
                            <?php
                                $totalDCFullWeek = 0;
                            ?>
                            <?php $__currentLoopData = $dataDc; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $week): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <td class="text-center font-monospace"><?php echo e($week['total']); ?></td>
                                <?php
                                    $totalDCFullWeek += $week['total'];
                                ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <td class="text-center fw-bold text-dark"><?php echo e($totalDCFullWeek); ?></td>
                            <td class="text-center">
                                <?php
                                    $targetD = $target->dc ?? 0;
                                    $denomD = is_array($dataDc) && count($dataDc) > 4 ? ($targetD + $targetD / 4) : $targetD;
                                    $pctD = $denomD > 0 ? round(($totalDCFullWeek / $denomD) * 100) : 0;
                                    $colorD = $pctD >= 100 ? 'success' : ($pctD >= 80 ? 'warning' : 'danger');
                                ?>
                                <span class="badge bg-label-<?php echo e($colorD); ?> rounded-pill px-3 py-1 fw-bold"><?php echo e($pctD); ?>%</span>
                            </td>
                        </tr>

                        <!-- CRM Row -->
                        <tr>
                            <td class="fw-semibold text-dark">
                                <span class="badge bg-label-primary p-1 rounded me-2"><i class="mdi mdi-account-multiple-outline"></i></span>
                                CRM
                            </td>
                            <?php
                                $totalCrmFullWeek = 0;
                            ?>
                            <?php $__currentLoopData = $dataCRM; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $week): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <td class="text-center font-monospace"><?php echo e($week['total']); ?></td>
                                <?php
                                    $totalCrmFullWeek += $week['total'];
                                ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <td class="text-center fw-bold text-dark"><?php echo e($totalCrmFullWeek); ?></td>
                            <td class="text-center">
                                <?php
                                    $targetC = $target->crm ?? 0;
                                    $denomC = is_array($dataCRM) && count($dataCRM) > 4 ? ($targetC + $targetC / 4) : $targetC;
                                    $pctC = $denomC > 0 ? round(($totalCrmFullWeek / $denomC) * 100) : 0;
                                    $colorC = $pctC >= 100 ? 'success' : ($pctC >= 80 ? 'warning' : 'danger');
                                ?>
                                <span class="badge bg-label-<?php echo e($colorC); ?> rounded-pill px-3 py-1 fw-bold"><?php echo e($pctC); ?>%</span>
                            </td>
                        </tr>

                        <!-- Visit Row (If Applicable) -->
                        <?php if(in_array($userArea, ['Bekasi', 'Jabodetabek', 'Jawa Barat'])): ?>
                            <tr>
                                <td class="fw-semibold text-dark">
                                    <span class="badge bg-label-danger p-1 rounded me-2"><i class="mdi mdi-office-building-marker-outline"></i></span>
                                    Visit / Presentation
                                </td>
                                <?php
                                    $totalVisitFullWeek = 0;
                                ?>
                                <?php $__currentLoopData = $dataVisit; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $week): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <td class="text-center font-monospace"><?php echo e($week['total']); ?></td>
                                    <?php
                                        $totalVisitFullWeek += $week['total'];
                                    ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <td class="text-center fw-bold text-dark"><?php echo e($totalVisitFullWeek); ?></td>
                                <td class="text-center">
                                    <?php
                                        $targetV = $target->visit ?? 0;
                                        $denomV = is_array($dataVisit) && count($dataVisit) > 4 ? ($targetV + $targetV / 4) : $targetV;
                                        $pctV = $denomV > 0 ? round(($totalVisitFullWeek / $denomV) * 100) : 0;
                                        $colorV = $pctV >= 100 ? 'success' : ($pctV >= 80 ? 'warning' : 'danger');
                                    ?>
                                    <span class="badge bg-label-<?php echo e($colorV); ?> rounded-pill px-3 py-1 fw-bold"><?php echo e($pctV); ?>%</span>
                                </td>
                            </tr>
                        <?php endif; ?>

                        <!-- Quotation Row -->
                        <tr>
                            <td class="fw-semibold text-dark">
                                <span class="badge bg-label-warning p-1 rounded me-2"><i class="mdi mdi-email-multiple-outline"></i></span>
                                Quotation
                            </td>
                            <?php
                                $totalQuoteFullWeek = 0;
                            ?>
                            <?php $__currentLoopData = $dataQuote; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $week): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <td class="text-center font-monospace"><?php echo e($week['total']); ?></td>
                                <?php
                                    $totalQuoteFullWeek += $week['total'];
                                ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <td class="text-center fw-bold text-dark"><?php echo e($totalQuoteFullWeek); ?></td>
                            <td class="text-center">
                                <?php
                                    $targetQ = $target->quote ?? 0;
                                    $denomQ = is_array($dataQuote) && count($dataQuote) > 4 ? ($targetQ + $targetQ / 4) : $targetQ;
                                    $pctQ = $denomQ > 0 ? round(($totalQuoteFullWeek / $denomQ) * 100) : 0;
                                    $colorQ = $pctQ >= 100 ? 'success' : ($pctQ >= 80 ? 'warning' : 'danger');
                                ?>
                                <span class="badge bg-label-<?php echo e($colorQ); ?> rounded-pill px-3 py-1 fw-bold"><?php echo e($pctQ); ?>%</span>
                            </td>
                        </tr>

                        <!-- Purchase Order Row -->
                        <tr>
                            <td class="fw-semibold text-dark">
                                <span class="badge bg-label-success p-1 rounded me-2"><i class="mdi mdi-cart-plus"></i></span>
                                Purchase Order (PO)
                            </td>
                            <?php
                                $totalPoFullWeek = 0;
                            ?>
                            <?php $__currentLoopData = $dataPo; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $week): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <td class="text-center font-monospace"><?php echo e($week['total']); ?></td>
                                <?php
                                    $totalPoFullWeek += $week['total'];
                                ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <td class="text-center fw-bold text-success"><?php echo e($totalPoFullWeek); ?></td>
                            <td class="text-center">
                                <span class="badge bg-label-success rounded-pill px-3 py-1 fw-bold">Active</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Purchase Order Records Table Card -->
        <div class="card clean-card mb-4" id="po">
            <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                <h5 class="card-title mb-0 d-flex align-items-center gap-2">
                    <i class="mdi mdi-file-document-check text-success fs-4"></i> Rincian Closing Purchase Order (PO Received)
                </h5>
                <span class="badge bg-label-success px-3 py-1 rounded-pill fw-bold">
                    <?php echo e(count($quotation) + count($unitQuotationPO)); ?> PO Total
                </span>
            </div>
            <div class="table-responsive text-nowrap">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="fw-bold">No. Quotation</th>
                            <th class="fw-bold">Perusahaan / Customer</th>
                            <th class="fw-bold">Judul Penawaran</th>
                            <th class="fw-bold">Tanggal PO</th>
                            <th class="fw-bold text-end">Nilai Nett (Rp)</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        <?php
                            $totalP = 0;
                        ?>
                        <?php $__currentLoopData = $quotation; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $quote): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $totalQ = $quote['nett'];
                                $totalP += $totalQ;
                            ?>
                            <tr>
                                <td>
                                    <span class="fw-bold text-dark"><?php echo e($quote->no_quote); ?></span>
                                </td>
                                <td><?php echo e($quote->pic->client->company ?? '-'); ?></td>
                                <td><?php echo e($quote->title); ?></td>
                                <td>
                                    <span class="badge bg-label-secondary">
                                        <?php echo e(\Carbon\Carbon::parse($quote->estimated_date)->format('d-m-Y')); ?>

                                    </span>
                                </td>
                                <td class="text-end fw-semibold text-dark">Rp <?php echo e(number_format($quote->nett, 0, ',', '.')); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php $__currentLoopData = $unitQuotationPO; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $uq): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $uqNett  = $uq->total - $uq->tax_amount;
                                $totalP += $uqNett;
                                $uqDate  = $uq->statusHistory->first()?->created_at;
                            ?>
                            <tr>
                                <td>
                                    <a href="<?php echo e(route('unit-quotation.show', $uq->id)); ?>" class="fw-bold text-primary">
                                        <?php echo e($uq->no_quote); ?>

                                    </a>
                                    <span class="badge bg-label-info ms-1">Smart</span>
                                </td>
                                <td><?php echo e($uq->client?->company ?? '-'); ?></td>
                                <td><?php echo e($uq->title ?? '-'); ?></td>
                                <td>
                                    <span class="badge bg-label-secondary">
                                        <?php echo e($uqDate ? \Carbon\Carbon::parse($uqDate)->format('d-m-Y') : '-'); ?>

                                    </span>
                                </td>
                                <td class="text-end fw-semibold text-dark">Rp <?php echo e(number_format($uqNett, 0, ',', '.')); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <tr class="bg-body-tertiary">
                            <td colspan="3" class="border-0"></td>
                            <td class="fw-bold text-dark fs-6">Total Nominal Closing:</td>
                            <td class="text-end fw-bold text-success fs-5">Rp <?php echo e(number_format($totalP, 0, ',', '.')); ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    <?php elseif(Auth::user()->role == 'Admin'): ?>
        <div class="container-fluid flex-grow-1 container-p-y">
            
            <div class="card mb-4">
                <h5 class="card-header">Assigned: Miss Regita</h5>
                <div class="table-responsive text-nowrap">
                    <table class="table">
                        <thead class="table-light">
                            <tr>
                                <th>Description</th>
                                <th>Week I</th>
                                <th>Week II</th>
                                <th>Week III</th>
                                <th>Week IV</th>
                                <th>Week V</th>
                                <th>Total</th>
                                <th>Presentase</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            <tr>
                                <td>
                                    <strong>Daily Call</strong>
                                </td>
                                <td>50</td>
                                <td>51</td>
                                <td>45</td>
                                <td>50</td>
                                <td>55</td>
                                <td>306</td>
                                <td>101%</td>
                            </tr>
                            <tr>
                                <td>
                                    <strong>Presentation / Visit</strong>
                                </td>
                                <td>50</td>
                                <td>51</td>
                                <td>45</td>
                                <td>50</td>
                                <td>55</td>
                                <td>306</td>
                                <td>101%</td>
                            </tr>
                            <tr>
                                <td>
                                    <strong>Quotation</strong>
                                </td>
                                <td>50</td>
                                <td>51</td>
                                <td>45</td>
                                <td>50</td>
                                <td>55</td>
                                <td>306</td>
                                <td>101%</td>
                            </tr>
                            <tr>
                                <td>
                                    <strong>Prucashing Order</strong>
                                </td>
                                <td>50</td>
                                <td>51</td>
                                <td>45</td>
                                <td>50</td>
                                <td>55</td>
                                <td>306</td>
                                <td>101%</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="row mb-5">
                <div class="col-md-6">
                    <div class="card mb-4">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">
                                <div class="d-flex justify-content-between">
                                    <p class="fw-semibold m-0"> Total Quotation</p>
                                    <p class="text-muted m-0">50</p>
                                </div>
                            </li>
                            <li class="list-group-item">
                                <div class="d-flex justify-content-between">
                                    <p class="fw-semibold m-0"> Total PO</p>
                                    <p class="text-muted m-0">50</p>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            
            <div class="card mb-4">
                <h5 class="card-header">Assigned: Miss Yolan</h5>
                <div class="table-responsive text-nowrap">
                    <table class="table">
                        <thead class="table-light">
                            <tr>
                                <th>Description</th>
                                <th>Week I</th>
                                <th>Week II</th>
                                <th>Week III</th>
                                <th>Week IV</th>
                                <th>Week V</th>
                                <th>Total</th>
                                <th>Presentase</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            <tr>
                                <td>
                                    <strong>Daily Call</strong>
                                </td>
                                <td>50</td>
                                <td>51</td>
                                <td>45</td>
                                <td>50</td>
                                <td>55</td>
                                <td>306</td>
                                <td>101%</td>
                            </tr>
                            <tr>
                                <td>
                                    <strong>Presentation / Visit</strong>
                                </td>
                                <td>50</td>
                                <td>51</td>
                                <td>45</td>
                                <td>50</td>
                                <td>55</td>
                                <td>306</td>
                                <td>101%</td>
                            </tr>
                            <tr>
                                <td>
                                    <strong>Quotation</strong>
                                </td>
                                <td>50</td>
                                <td>51</td>
                                <td>45</td>
                                <td>50</td>
                                <td>55</td>
                                <td>306</td>
                                <td>101%</td>
                            </tr>
                            <tr>
                                <td>
                                    <strong>Prucashing Order</strong>
                                </td>
                                <td>50</td>
                                <td>51</td>
                                <td>45</td>
                                <td>50</td>
                                <td>55</td>
                                <td>306</td>
                                <td>101%</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="row mb-5">
                <div class="col-md-6">
                    <div class="card mb-4">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">
                                <div class="d-flex justify-content-between">
                                    <p class="fw-semibold m-0"> Total Quotation</p>
                                    <p class="text-muted m-0">50</p>
                                </div>
                            </li>
                            <li class="list-group-item">
                                <div class="d-flex justify-content-between">
                                    <p class="fw-semibold m-0"> Total PO</p>
                                    <p class="text-muted m-0">50</p>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            
            <div class="card mb-4">
                <h5 class="card-header">Assigned: Mister Ari</h5>
                <div class="table-responsive text-nowrap">
                    <table class="table">
                        <thead class="table-light">
                            <tr>
                                <th>Description</th>
                                <th>Week I</th>
                                <th>Week II</th>
                                <th>Week III</th>
                                <th>Week IV</th>
                                <th>Week V</th>
                                <th>Total</th>
                                <th>Presentase</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            <tr>
                                <td>
                                    <strong>Daily Call</strong>
                                </td>
                                <td>50</td>
                                <td>51</td>
                                <td>45</td>
                                <td>50</td>
                                <td>55</td>
                                <td>306</td>
                                <td>101%</td>
                            </tr>
                            <tr>
                                <td>
                                    <strong>Presentation / Visit</strong>
                                </td>
                                <td>50</td>
                                <td>51</td>
                                <td>45</td>
                                <td>50</td>
                                <td>55</td>
                                <td>306</td>
                                <td>101%</td>
                            </tr>
                            <tr>
                                <td>
                                    <strong>Quotation</strong>
                                </td>
                                <td>50</td>
                                <td>51</td>
                                <td>45</td>
                                <td>50</td>
                                <td>55</td>
                                <td>306</td>
                                <td>101%</td>
                            </tr>
                            <tr>
                                <td>
                                    <strong>Prucashing Order</strong>
                                </td>
                                <td>50</td>
                                <td>51</td>
                                <td>45</td>
                                <td>50</td>
                                <td>55</td>
                                <td>306</td>
                                <td>101%</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="row mb-5">
                <div class="col-md-6">
                    <div class="card mb-4">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">
                                <div class="d-flex justify-content-between">
                                    <p class="fw-semibold m-0"> Total Quotation</p>
                                    <p class="text-muted m-0">50</p>
                                </div>
                            </li>
                            <li class="list-group-item">
                                <div class="d-flex justify-content-between">
                                    <p class="fw-semibold m-0"> Total PO</p>
                                    <p class="text-muted m-0">50</p>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            
            <div class="card mb-4">
                <h5 class="card-header">Assigned: Mister Yusuf</h5>
                <div class="table-responsive text-nowrap">
                    <table class="table">
                        <thead class="table-light">
                            <tr>
                                <th>Description</th>
                                <th>Week I</th>
                                <th>Week II</th>
                                <th>Week III</th>
                                <th>Week IV</th>
                                <th>Week V</th>
                                <th>Total</th>
                                <th>Presentase</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            <tr>
                                <td>
                                    <strong>Daily Call</strong>
                                </td>
                                <td>50</td>
                                <td>51</td>
                                <td>45</td>
                                <td>50</td>
                                <td>55</td>
                                <td>306</td>
                                <td>101%</td>
                            </tr>
                            <tr>
                                <td>
                                    <strong>Presentation / Visit</strong>
                                </td>
                                <td>50</td>
                                <td>51</td>
                                <td>45</td>
                                <td>50</td>
                                <td>55</td>
                                <td>306</td>
                                <td>101%</td>
                            </tr>
                            <tr>
                                <td>
                                    <strong>Quotation</strong>
                                </td>
                                <td>50</td>
                                <td>51</td>
                                <td>45</td>
                                <td>50</td>
                                <td>55</td>
                                <td>306</td>
                                <td>101%</td>
                            </tr>
                            <tr>
                                <td>
                                    <strong>Prucashing Order</strong>
                                </td>
                                <td>50</td>
                                <td>51</td>
                                <td>45</td>
                                <td>50</td>
                                <td>55</td>
                                <td>306</td>
                                <td>101%</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="card mb-4">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">
                                <div class="d-flex justify-content-between">
                                    <p class="fw-semibold m-0"> Total Quotation</p>
                                    <p class="text-muted m-0">50</p>
                                </div>
                            </li>
                            <li class="list-group-item">
                                <div class="d-flex justify-content-between">
                                    <p class="fw-semibold m-0"> Total PO</p>
                                    <p class="text-muted m-0">50</p>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.sales.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u877155683/domains/reftech.my.id/reftech.my.id/resources/views/pages/sales/report/index.blade.php ENDPATH**/ ?>