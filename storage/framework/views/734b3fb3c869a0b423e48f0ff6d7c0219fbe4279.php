<?php $__env->startSection('title', 'Year Reports'); ?>
<?php $__env->startSection('content'); ?>
    <?php if(in_array(Auth::user()->role, ['Sales', 'Admin', 'Super Admin'])): ?>
        <?php
            $user = $user ?? Auth::user();
            $lastDetail = $user->detail->last();
            $userArea = $lastDetail->area ?? ($user->latestRole->area ?? 'Sales Area');
            $isAdminView = Auth::user()->role !== 'Sales';

            // Calculate total PO & Forecast for semester
            $semesterTotalPO = 0;
            $semesterTotalForecast = 0;
            $semesterTotalQuoteNominal = 0;
            $semesterTotalLeads = 0;
            $semesterTotalDC = 0;
            $semesterTotalCRM = 0;
            $semesterTotalVisit = 0;
            $semesterTotalQuote = 0;

            $chartMonths = [];
            $chartPOData = [];
            $chartQuoteNominalData = [];

            if (isset($getDC) && is_array($getDC)) {
                foreach ($getDC as $idx => $dcItem) {
                    $monthIdx = (int) $idx;
                    $poVal = (float) ($getTotalPO[$monthIdx]['total'] ?? 0);
                    $quoteNominalVal = (float) ($getTotalQuoteNominal[$monthIdx]['total'] ?? 0);

                    $semesterTotalPO += $poVal;
                    $semesterTotalForecast += (float) ($getTotalForecast[$monthIdx]['total'] ?? 0);
                    $semesterTotalQuoteNominal += $quoteNominalVal;
                    $semesterTotalLeads += $getLeads[$monthIdx]['total'] ?? 0;
                    $semesterTotalDC += $dcItem['total'] ?? 0;
                    $semesterTotalCRM += $getCRM[$monthIdx]['total'] ?? 0;
                    $semesterTotalVisit += $getVisit[$monthIdx]['total'] ?? 0;
                    $semesterTotalQuote += $getQuote[$monthIdx]['total'] ?? 0;

                    $chartMonths[] = $dcItem['month'];
                    $chartPOData[] = $poVal;
                    $chartQuoteNominalData[] = $quoteNominalVal;
                }
            }

            // $targett dari controller adalah target BULANAN. Untuk mode 'full' (S1+S2),
            // controller sudah mengalikan x2 (jadi setara 2 bulan), sehingga di sini cukup
            // dikalikan count($getDC)/2 (=6) supaya total jadi target 12 bulan yang benar.
            // Untuk mode semester biasa, dikalikan count($getDC) (=6 bulan) langsung.
            $targettPeriod = $targett * ($report->semester === 'full' ? count($getDC) / 2 : count($getDC));
            $salesPercentage = $targettPeriod > 0 ? round(($semesterTotalPO / $targettPeriod) * 100, 1) : 0;
            $salesColor = $salesPercentage >= 100 ? 'success' : ($salesPercentage >= 80 ? 'warning' : 'danger');
        ?>

        <!-- Header Card with S1/S2 Toggle & Year Filter Dropdown -->
        <div class="card clean-card mb-4">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="badge bg-label-primary fs-6 px-3 py-2">
                                <i class="mdi mdi-chart-areaspline me-1"></i>
                                <?php echo e($report->semester == 'full' ? 'Full Year (S1+S2)' : 'Semester ' . $report->semester); ?>

                            </span>
                            <span class="text-muted fw-semibold fs-5"><?php echo e($report->year); ?></span>
                            <span class="text-muted">•</span>
                            <small class="text-muted fw-semibold">
                                <?php echo e($report->semester == 'full' ? 'Januari – Desember (12 Bulan)' : ($report->semester == 1 ? 'Januari – Juni' : 'Juli – Desember')); ?>

                            </small>
                        </div>
                        <h4 class="fw-bold mb-0 text-dark">Executive Sales Overview</h4>
                        <small class="text-muted">
                            Laporan Performa Sales &mdash; <strong><?php echo e($user->name); ?></strong> (<?php echo e($userArea); ?>)
                        </small>
                    </div>

                    <!-- Filter Options (Like Admin Report Page) -->
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <!-- Semester & Full Year Toggle Buttons -->
                        <div class="btn-group" role="group" aria-label="Pilih Semester">
                            <?php if($s1Report): ?>
                                <a href="<?php echo e($isAdminView ? url('/overview/' . $s1Report->id . '/' . $user->id) : url('/overview?report_id=' . $s1Report->id)); ?>"
                                   class="btn btn-sm waves-effect <?php echo e($report->semester == 1 ? 'btn-primary' : 'btn-outline-primary'); ?>">
                                    Semester 1
                                </a>
                            <?php endif; ?>
                            <?php if($s2Report): ?>
                                <a href="<?php echo e($isAdminView ? url('/overview/' . $s2Report->id . '/' . $user->id) : url('/overview?report_id=' . $s2Report->id)); ?>"
                                   class="btn btn-sm waves-effect <?php echo e($report->semester == 2 ? 'btn-primary' : 'btn-outline-primary'); ?>">
                                    Semester 2
                                </a>
                            <?php endif; ?>
                            <a href="<?php echo e($isAdminView ? url('/overview/full_' . $report->year . '/' . $user->id) : url('/overview?report_id=full_' . $report->year)); ?>"
                               class="btn btn-sm waves-effect <?php echo e($report->semester == 'full' ? 'btn-primary' : 'btn-outline-primary'); ?>">
                                <i class="mdi mdi-calendar-blank-multiple me-1"></i> Full Year (S1+S2)
                            </a>
                        </div>

                        <!-- Year Filter Dropdown -->
                        <div class="btn-group">
                            <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle waves-effect"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="mdi mdi-calendar me-1"></i> <?php echo e($report->year); ?>

                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <?php $__currentLoopData = $yearsList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $yr): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php
                                        if ($report->semester == 'full') {
                                            $targetUrl = $isAdminView
                                                ? url('/overview/full_' . $yr . '/' . $user->id)
                                                : url('/overview') . '?report_id=full_' . $yr;
                                        } else {
                                            $targetRep = $allReports->where('year', $yr)->where('semester', $report->semester)->first()
                                                ?? $allReports->where('year', $yr)->first();
                                            $targetUrl = $targetRep
                                                ? ($isAdminView ? url('/overview/' . $targetRep->id . '/' . $user->id) : url('/overview') . '?report_id=' . $targetRep->id)
                                                : '#';
                                        }
                                    ?>
                                    <li>
                                        <a class="dropdown-item waves-effect <?php echo e($yr == $report->year ? 'active' : ''); ?>"
                                           href="<?php echo e($targetUrl); ?>">
                                            Tahun <?php echo e($yr); ?>

                                        </a>
                                    </li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Semester Summary Metrics -->
        <div class="row gy-4 mb-4">
            <!-- Omset Sales PO -->
            <div class="col-12 col-md-4">
                <div class="card clean-card h-100">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="badge bg-label-success p-2 rounded-circle">
                                <i class="mdi mdi-cart-plus fs-4"></i>
                            </span>
                            <span class="badge bg-<?php echo e($salesColor); ?> rounded-pill px-3 py-1"><?php echo e($salesPercentage); ?>% Target</span>
                        </div>
                        <small class="text-muted fw-semibold text-uppercase d-block" style="font-size: 0.75rem;">Total Sales (PO Received)</small>
                        <h3 class="fw-bold text-dark mb-1 mt-1">Rp <?php echo e(number_format($semesterTotalPO, 0, ',', '.')); ?></h3>
                        <div class="progress mt-2" style="height: 6px; border-radius: 4px;">
                            <div class="progress-bar bg-<?php echo e($salesColor); ?>" style="width: <?php echo e(min($salesPercentage, 100)); ?>%; border-radius: 4px;"></div>
                        </div>
                        <small class="text-muted mt-2 d-block fs-tiny">Target <?php echo e($report->semester === 'full' ? 'Tahunan' : 'Semester'); ?>: Rp <?php echo e(number_format($targettPeriod, 0, ',', '.')); ?></small>
                    </div>
                </div>
            </div>

            <!-- Total Quotation Card -->
            <div class="col-12 col-md-4">
                <div class="card clean-card h-100">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="badge bg-label-primary p-2 rounded-circle">
                                <i class="mdi mdi-email-multiple-outline fs-4"></i>
                            </span>
                            <span class="badge bg-label-primary rounded-pill px-3 py-1">Quotation</span>
                        </div>
                        <small class="text-muted fw-semibold text-uppercase d-block" style="font-size: 0.75rem;">Total Quotation</small>
                        <h3 class="fw-bold text-primary mb-1 mt-1">Rp <?php echo e(number_format($semesterTotalQuoteNominal, 0, ',', '.')); ?></h3>
                        <small class="text-muted d-block mt-2 fs-tiny">Akumulasi Penawaran Terbit 6 Bulan</small>
                    </div>
                </div>
            </div>

            <!-- Operational Cumulative Stats -->
            <div class="col-12 col-md-4">
                <div class="card clean-card h-100">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="badge bg-label-info p-2 rounded-circle">
                                <i class="mdi mdi-chart-timeline-variant fs-4"></i>
                            </span>
                            <span class="badge bg-label-info rounded-pill px-3 py-1">Aktivitas</span>
                        </div>
                        <small class="text-muted fw-semibold text-uppercase d-block" style="font-size: 0.75rem;">Total Aktivitas Sales</small>
                        <div class="row g-2 mt-1">
                            <div class="col-6">
                                <small class="text-muted d-block">Leads:</small>
                                <span class="fw-bold text-dark fs-6"><?php echo e($semesterTotalLeads); ?></span>
                            </div>
                            <div class="col-6">
                                <small class="text-muted d-block">Daily Call:</small>
                                <span class="fw-bold text-dark fs-6"><?php echo e($semesterTotalDC); ?></span>
                            </div>
                            <div class="col-6">
                                <small class="text-muted d-block">CRM:</small>
                                <span class="fw-bold text-dark fs-6"><?php echo e($semesterTotalCRM); ?></span>
                            </div>
                            <div class="col-6">
                                <small class="text-muted d-block">Quotation:</small>
                                <span class="fw-bold text-dark fs-6"><?php echo e($semesterTotalQuote); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sales Revenue Trend Chart Card -->
        <div class="card clean-card mb-4">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0 d-flex align-items-center gap-2">
                    <i class="mdi mdi-chart-bell-curve-cumulative text-primary fs-4"></i> Grafik Penjualan Sales & Nominal Quotation (Semester <?php echo e($report->semester); ?> - <?php echo e($report->year); ?>)
                </h5>
                <span class="badge bg-label-primary rounded-pill px-3 py-1">Tren Bulanan</span>
            </div>
            <div class="card-body">
                <div id="salesOverviewTrendChart" style="min-height: 320px;"></div>
            </div>
        </div>

        <!-- Performance Table Section (Replaces Heavy Monthly Cards) -->
        <h5 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
            <i class="mdi mdi-table-large text-primary fs-4"></i> Tabel Performa Sales Per Semester
        </h5>

        <?php if($report->semester == 'full'): ?>
            <!-- Full Year: 2 Tables (Semester 1 & Semester 2) -->
            <div class="row gy-4 mb-4">
                <?php
                    $semesters = [
                        1 => ['title' => 'Semester 1 Overview (Januari – Juni)', 'range' => [1, 6], 'badgeClass' => 'bg-primary'],
                        2 => ['title' => 'Semester 2 Overview (Juli – Desember)', 'range' => [7, 12], 'badgeClass' => 'bg-info']
                    ];
                ?>
                <?php $__currentLoopData = $semesters; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $semNum => $semMeta): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="col-12">
                        <div class="card clean-card mb-4">
                            <div class="card-header bg-body-tertiary border-bottom py-3 d-flex align-items-center justify-content-between">
                                <h6 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                                    <i class="mdi mdi-calendar-text text-primary"></i> <?php echo e($semMeta['title']); ?>

                                </h6>
                                <span class="badge <?php echo e($semMeta['badgeClass']); ?> rounded-pill px-3">Semester <?php echo e($semNum); ?></span>
                            </div>
                            <div class="table-responsive text-nowrap">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr class="text-uppercase" style="font-size: 0.72rem;">
                                            <th>Bulan</th>
                                            <th class="text-end">Sales PO</th>
                                            <th class="text-end">Nominal Quotation</th>
                                            <th class="text-center">Leads</th>
                                            <th class="text-center">Call</th>
                                            <th class="text-center">CRM</th>
                                            <?php if(in_array($userArea, ['Bekasi', 'Jabodetabek', 'Jawa Barat'])): ?>
                                                <th class="text-center">Visit</th>
                                            <?php endif; ?>
                                            <th class="text-center">Quote</th>
                                            <th class="text-center">PO</th>
                                            <th class="text-center">Detail</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                            $sPO = 0; $sFc = 0; $sLd = 0; $sDc = 0; $sCrm = 0; $sVis = 0; $sQt = 0; $sPoCnt = 0;
                                        ?>
                                        <?php for($item = $semMeta['range'][0]; $item <= $semMeta['range'][1]; $item++): ?>
                                            <?php
                                                $DC = $getDC[$item] ?? null;
                                                if (!$DC) continue;

                                                $monthPO = $getTotalPO[$item]['total'] ?? 0;
                                                $monthForecast = $getTotalQuoteNominal[$item]['total'] ?? 0;
                                                $monthLeads = $getLeads[$item]['total'] ?? 0;
                                                $monthDC = $DC['total'] ?? 0;
                                                $monthCRM = $getCRM[$item]['total'] ?? 0;
                                                $monthVisit = $getVisit[$item]['total'] ?? 0;
                                                $monthQuote = $getQuote[$item]['total'] ?? 0;
                                                $monthPOCount = $getPO[$item]['total'] ?? 0;

                                                $sPO += $monthPO;
                                                $sFc += $monthForecast;
                                                $sLd += $monthLeads;
                                                $sDc += $monthDC;
                                                $sCrm += $monthCRM;
                                                $sVis += $monthVisit;
                                                $sQt += $monthQuote;
                                                $sPoCnt += $monthPOCount;
                                            ?>
                                            <tr>
                                                <td class="fw-semibold text-dark"><?php echo e($DC['month']); ?></td>
                                                <td class="text-end fw-bold text-success">Rp <?php echo e(number_format($monthPO, 0, ',', '.')); ?></td>
                                                <td class="text-end text-primary fw-semibold">Rp <?php echo e(number_format($monthForecast, 0, ',', '.')); ?></td>
                                                <td class="text-center"><?php echo e($monthLeads); ?></td>
                                                <td class="text-center"><?php echo e($monthDC); ?></td>
                                                <td class="text-center"><?php echo e($monthCRM); ?></td>
                                                <?php if(in_array($userArea, ['Bekasi', 'Jabodetabek', 'Jawa Barat'])): ?>
                                                    <td class="text-center"><?php echo e($monthVisit); ?></td>
                                                <?php endif; ?>
                                                <td class="text-center"><?php echo e($monthQuote); ?></td>
                                                <td class="text-center fw-bold text-success"><?php echo e($monthPOCount); ?></td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-xs btn-primary rounded-pill px-2 py-1"
                                                        data-bs-toggle="modal" data-bs-target="#overviewPO<?php echo e($DC['monthKey']); ?>">
                                                        <i class="mdi mdi-eye-outline"></i>
                                                    </button>
                                                    <?php echo $__env->make('components.modal.overview.totalPo', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                                </td>
                                            </tr>
                                        <?php endfor; ?>
                                    </tbody>
                                    <tfoot class="table-light fw-bold">
                                        <tr>
                                            <td>Total S<?php echo e($semNum); ?></td>
                                            <td class="text-end text-success">Rp <?php echo e(number_format($sPO, 0, ',', '.')); ?></td>
                                            <td class="text-end text-primary">Rp <?php echo e(number_format($sFc, 0, ',', '.')); ?></td>
                                            <td class="text-center"><?php echo e($sLd); ?></td>
                                            <td class="text-center"><?php echo e($sDc); ?></td>
                                            <td class="text-center"><?php echo e($sCrm); ?></td>
                                            <?php if(in_array($userArea, ['Bekasi', 'Jabodetabek', 'Jawa Barat'])): ?>
                                                <td class="text-center"><?php echo e($sVis); ?></td>
                                            <?php endif; ?>
                                            <td class="text-center"><?php echo e($sQt); ?></td>
                                            <td class="text-center text-success"><?php echo e($sPoCnt); ?></td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php else: ?>
            <!-- Single Semester Table -->
            <div class="card clean-card mb-4">
                <div class="card-header bg-body-tertiary border-bottom py-3 d-flex align-items-center justify-content-between">
                    <h5 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                        <i class="mdi mdi-calendar-month text-primary"></i> Tabel Performa Bulanan &mdash; Semester <?php echo e($report->semester); ?> (<?php echo e($report->year); ?>)
                    </h5>
                    <span class="badge bg-label-primary rounded-pill px-3 py-1">6 Bulan</span>
                </div>
                <div class="table-responsive text-nowrap">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr class="text-uppercase" style="font-size: 0.72rem;">
                                <th>Bulan</th>
                                <th class="text-end">Total Sales (PO)</th>
                                <th class="text-end">Nominal Quotation</th>
                                <th class="text-center">Leads</th>
                                <th class="text-center">Daily Call</th>
                                <th class="text-center">CRM</th>
                                <?php if(in_array($userArea, ['Bekasi', 'Jabodetabek', 'Jawa Barat'])): ?>
                                    <th class="text-center">Visit</th>
                                <?php endif; ?>
                                <th class="text-center">Quotation</th>
                                <th class="text-center">PO Closed</th>
                                <th class="text-center">% Target</th>
                                <th class="text-center">Rincian PO</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $sPO = 0; $sFc = 0; $sLd = 0; $sDc = 0; $sCrm = 0; $sVis = 0; $sQt = 0; $sPoCnt = 0;
                            ?>
                            <?php $__currentLoopData = $getDC; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $monthKey => $DC): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $item = (int) $monthKey;
                                    $monthPO = $getTotalPO[$item]['total'] ?? 0;
                                    $monthForecast = $getTotalQuoteNominal[$item]['total'] ?? 0;
                                    $monthLeads = $getLeads[$item]['total'] ?? 0;
                                    $monthDC = $DC['total'] ?? 0;
                                    $monthCRM = $getCRM[$item]['total'] ?? 0;
                                    $monthVisit = $getVisit[$item]['total'] ?? 0;
                                    $monthQuote = $getQuote[$item]['total'] ?? 0;
                                    $monthPOCount = $getPO[$item]['total'] ?? 0;
                                    $monthPct = $targett > 0 ? round(($monthPO / $targett) * 100, 1) : 0;

                                    $sPO += $monthPO;
                                    $sFc += $monthForecast;
                                    $sLd += $monthLeads;
                                    $sDc += $monthDC;
                                    $sCrm += $monthCRM;
                                    $sVis += $monthVisit;
                                    $sQt += $monthQuote;
                                    $sPoCnt += $monthPOCount;
                                ?>
                                <tr>
                                    <td class="fw-semibold text-dark"><?php echo e($DC['month']); ?></td>
                                    <td class="text-end fw-bold text-success">Rp <?php echo e(number_format($monthPO, 0, ',', '.')); ?></td>
                                    <td class="text-end text-primary fw-semibold">Rp <?php echo e(number_format($monthForecast, 0, ',', '.')); ?></td>
                                    <td class="text-center"><?php echo e($monthLeads); ?></td>
                                    <td class="text-center"><?php echo e($monthDC); ?></td>
                                    <td class="text-center"><?php echo e($monthCRM); ?></td>
                                    <?php if(in_array($userArea, ['Bekasi', 'Jabodetabek', 'Jawa Barat'])): ?>
                                        <td class="text-center"><?php echo e($monthVisit); ?></td>
                                    <?php endif; ?>
                                    <td class="text-center"><?php echo e($monthQuote); ?></td>
                                    <td class="text-center fw-bold text-success"><?php echo e($monthPOCount); ?></td>
                                    <td class="text-center">
                                        <span class="badge bg-label-<?php echo e($monthPct >= 100 ? 'success' : ($monthPct >= 80 ? 'warning' : 'danger')); ?> rounded-pill">
                                            <?php echo e($monthPct); ?>%
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-xs btn-primary rounded-pill px-3 py-1"
                                            data-bs-toggle="modal" data-bs-target="#overviewPO<?php echo e($DC['monthKey']); ?>">
                                            <i class="mdi mdi-eye-outline me-1"></i> Rincian PO
                                        </button>
                                        <?php echo $__env->make('components.modal.overview.totalPo', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                        <tfoot class="table-light fw-bold">
                            <tr>
                                <td>Total Semester <?php echo e($report->semester); ?></td>
                                <td class="text-end text-success">Rp <?php echo e(number_format($sPO, 0, ',', '.')); ?></td>
                                <td class="text-end text-primary">Rp <?php echo e(number_format($sFc, 0, ',', '.')); ?></td>
                                <td class="text-center"><?php echo e($sLd); ?></td>
                                <td class="text-center"><?php echo e($sDc); ?></td>
                                <td class="text-center"><?php echo e($sCrm); ?></td>
                                <?php if(in_array($userArea, ['Bekasi', 'Jabodetabek', 'Jawa Barat'])): ?>
                                    <td class="text-center"><?php echo e($sVis); ?></td>
                                <?php endif; ?>
                                <td class="text-center"><?php echo e($sQt); ?></td>
                                <td class="text-center text-success"><?php echo e($sPoCnt); ?></td>
                                <td class="text-center">
                                    <?php
                                        $semesterTargett = $targett * count($getDC);
                                        $totPct = $semesterTargett > 0 ? round(($sPO / $semesterTargett) * 100, 1) : 0;
                                    ?>
                                    <span class="badge bg-<?php echo e($totPct >= 100 ? 'success' : ($totPct >= 80 ? 'warning' : 'danger')); ?> rounded-pill">
                                        <?php echo e($totPct); ?>%
                                    </span>
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        <?php endif; ?>
    <?php elseif(Auth::user()->role == 'Admin'): ?>
        <div class="row">
            <?php
                $item = 0;
            ?>
            <?php $__currentLoopData = $sales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sale): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="col-6 col-lg-4 mb-3">
                    <a href="<?php echo e(Route('overview.semester', $sale->id)); ?>" class="text-decoration-none text-black">
                        <div class="card">
                            <div class="row">
                                <div class="col-4">
                                    <img src="<?php echo e(url('') . '/' . $sale->image); ?>" alt="" srcset=""
                                        class="rounded-circle" style="width : 100%; height:100%;">
                                </div>
                                <div class="col-8 m-auto">
                                    <?php
                                        $lastDetail = $sale->detail->last();
                                    ?>
                                    <h3><?php echo e($sale->name); ?></h3>
                                    <p><?php echo e($lastDetail->area ?? '-'); ?></p>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                
                <?php
                    $item++;
                ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <div class="col-6 col-lg-4 mb-3">
                <a href="<?php echo e(Route('overview.semester', $support->id)); ?>" class="text-decoration-none text-black">
                    <div class="card">
                        <div class="row">
                            <div class="col-4">
                                <img src="<?php echo e(url('') . '/' . $support->image); ?>" alt="" srcset=""
                                    class="rounded-circle" style="width : 100%; height:100%;">
                            </div>
                            <div class="col-8 m-auto">
                                
                                <h3><?php echo e($support->name); ?></h3>
                                <p>Online</p>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    <?php endif; ?>
    <?php echo $__env->make('pages.warehouse.reports.form', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('after-style'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
    <link rel="stylesheet"
        href="<?php echo e(asset('assets')); ?>/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.css" />
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css" />
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/animate-css/animate.css">
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/datatables-rowgroup-bs5/rowgroup.bootstrap5.css" />
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/formvalidation/dist/css/formValidation.min.css" />
<?php $__env->stopPush(); ?>

<?php $__env->startPush('after-script'); ?>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/moment/moment.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/flatpickr/flatpickr.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/formvalidation/dist/js/FormValidation.min.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('page-script'); ?>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/apex-charts/apexcharts.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var chartEl = document.querySelector('#salesOverviewTrendChart');
            if (chartEl) {
                var options = {
                    series: [{
                        name: 'Total Sales (PO Received)',
                        data: <?php echo json_encode($chartPOData ?? [], 15, 512) ?>
                    }, {
                        name: 'Total Nominal Quotation Dibuat',
                        data: <?php echo json_encode($chartQuoteNominalData ?? [], 15, 512) ?>
                    }],
                    chart: {
                        type: 'area',
                        height: 320,
                        toolbar: { show: false },
                        zoom: { enabled: false }
                    },
                    colors: ['#28c76f', '#666cff'],
                    dataLabels: { enabled: false },
                    stroke: { curve: 'smooth', width: 3 },
                    fill: {
                        type: 'gradient',
                        gradient: {
                            shadeIntensity: 1,
                            opacityFrom: 0.45,
                            opacityTo: 0.05,
                            stops: [0, 95, 100]
                        }
                    },
                    xaxis: {
                        categories: <?php echo json_encode($chartMonths ?? [], 15, 512) ?>,
                        axisBorder: { show: false }
                    },
                    yaxis: {
                        labels: {
                            formatter: function(val) {
                                return 'Rp ' + new Intl.NumberFormat('id-ID').format(val);
                            }
                        }
                    },
                    tooltip: {
                        y: {
                            formatter: function(val) {
                                return 'Rp ' + new Intl.NumberFormat('id-ID').format(val);
                            }
                        }
                    },
                    grid: { borderColor: '#f1f1f1' }
                };
                var chart = new ApexCharts(chartEl, options);
                chart.render();
            }
        });
    </script>
    <script src="<?php echo e(asset('assets')); ?>/js/tables-datatables-basic.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/includes/table-overview.js"></script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.sales.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u877155683/domains/reftech.my.id/reftech.my.id/resources/views/pages/overview.blade.php ENDPATH**/ ?>