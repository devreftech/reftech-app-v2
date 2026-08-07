<?php $__env->startSection('title', 'Report Tahunan'); ?>
<?php $__env->startSection('content'); ?>
    <?php
        $fullPercent = $totalTarget > 0 ? round(($poTotal / ($totalTarget * 12)) * 100, 1) : 0;
        $bulanMap = [
            1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'Mei', 6 => 'Jun',
            7 => 'Jul', 8 => 'Ags', 9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des',
        ];

        // Team E-Commerce grouping (ID 16 & 23)
        $ecommerceIds     = [16, 23];
        $ecommerceMembers = array_values(array_filter($data, fn($s) => in_array($s['id'], $ecommerceIds)));
        $regularSales     = array_values(array_filter($data, fn($s) => !in_array($s['id'], $ecommerceIds)));
        $teamTarget       = array_sum(array_column($ecommerceMembers, 'target'));
        $teamJumlah       = [];
        for ($m = 1; $m <= 12; $m++) {
            $teamJumlah[$m] = array_sum(array_map(fn($s) => $s['jumlah'][$m] ?? 0, $ecommerceMembers));
        }
        $teamTotal    = array_sum($teamJumlah);
        $teamMainMember = collect($ecommerceMembers)->firstWhere('id', 16);
        $teamImage    = $teamMainMember ? $teamMainMember['image'] : '';
    ?>

    
    <?php
        $pctColor = $fullPercent >= 100 ? 'success' : ($fullPercent >= 80 ? 'warning' : 'danger');
    ?>
    <div class="d-flex align-items-start justify-content-between flex-wrap gap-3 mb-4">

        
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-label-secondary fs-6 px-3 py-2">
                    <i class="mdi mdi-calendar-text me-1"></i> Tahunan
                </span>
                <span class="text-muted fw-semibold"><?php echo e($year); ?></span>
                <span class="text-muted">•</span>
                <small class="text-muted">Januari – Desember</small>
            </div>
            <h4 class="fw-bold mb-1 text-heading">Overview Report Penjualan</h4>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-<?php echo e($pctColor); ?> rounded-pill px-3">
                    <?php echo e($fullPercent); ?>% pencapaian target
                </span>
                <small class="text-muted">Rp <?php echo e(number_format($poTotal, 0, ',', '.')); ?> dari Rp <?php echo e(number_format($totalTarget * 12, 0, ',', '.')); ?></small>
            </div>
        </div>

        
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <div class="btn-group" role="group">
                <?php if($reportS1): ?>
                    <a href="<?php echo e(route('report.semester', $reportS1->id)); ?>"
                       class="btn btn-sm btn-outline-primary waves-effect">
                        <i class="mdi mdi-numeric-1-circle-outline me-1"></i>Semester 1
                    </a>
                <?php endif; ?>
                <?php if($reportS2): ?>
                    <a href="<?php echo e(route('report.semester', $reportS2->id)); ?>"
                       class="btn btn-sm btn-outline-primary waves-effect">
                        <i class="mdi mdi-numeric-2-circle-outline me-1"></i>Semester 2
                    </a>
                <?php endif; ?>
            </div>
            <div class="btn-group">
                <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle waves-effect"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="mdi mdi-calendar me-1"></i> <?php echo e($year); ?>

                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <?php $__currentLoopData = $yearList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $yr): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li>
                            <a class="dropdown-item waves-effect <?php echo e($yr == $year ? 'active' : ''); ?>"
                                href="<?php echo e(route('report.year', $yr)); ?>"><?php echo e($yr); ?></a>
                        </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        </div>

    </div>

    
    <?php
        $winRate       = $quoteOnCount > 0 ? round(($poCount   / $quoteOnCount) * 100, 1) : 0;
        $lossRate      = $quoteOnCount > 0 ? round(($lossCount / $quoteOnCount) * 100, 1) : 0;
        $mktContrib    = $poTotal > 0 ? round(($poTotalSupport / $poTotal) * 100, 1) : 0;
        $winColor      = $winRate  >= 50 ? 'success' : ($winRate  >= 30 ? 'warning' : 'danger');
        $lossColor     = $lossRate <= 20 ? 'success' : ($lossRate <= 40 ? 'warning' : 'danger');
        $mktColor      = $mktContrib >= 30 ? 'success' : ($mktContrib >= 15 ? 'warning' : 'secondary');
        $yearCards = [
            [
                'label'  => 'Purchase Order',
                'icon'   => 'mdi-cart-plus',
                'color'  => 'success',
                'amount' => 'Rp ' . number_format($poTotal, 0, ',', '.'),
                'sub'    => $poCount . ' transaksi',
            ],
            [
                'label'  => 'Total Quotation',
                'icon'   => 'mdi-cart',
                'color'  => 'primary',
                'amount' => 'Rp ' . number_format($quoteOnTotal, 0, ',', '.'),
                'sub'    => $quoteOnCount . ' transaksi',
            ],
            [
                'label'  => 'Quotation Aktif',
                'icon'   => 'mdi-cart-outline',
                'color'  => 'info',
                'amount' => 'Rp ' . number_format($quoteTotal, 0, ',', '.'),
                'sub'    => $quoteCount . ' transaksi',
            ],
            [
                'label'  => 'Loss',
                'icon'   => 'mdi-cart-minus',
                'color'  => 'danger',
                'amount' => 'Rp ' . number_format($lossTotal, 0, ',', '.'),
                'sub'    => $lossCount . ' transaksi',
            ],
            [
                'label'  => 'Convertion Rate',
                'icon'   => 'mdi-trophy-outline',
                'color'  => $winColor,
                'amount' => $winRate . '%',
                'sub'    => $poCount . ' PO dari ' . $quoteOnCount . ' quotation',
            ],
            [
                'label'  => 'Loss Rate',
                'icon'   => 'mdi-trending-down',
                'color'  => $lossColor,
                'amount' => $lossRate . '%',
                'sub'    => $lossCount . ' loss dari ' . $quoteOnCount . ' quotation',
            ],
            [
                'label'  => 'Marketing Contribution',
                'icon'   => 'mdi-handshake-outline',
                'color'  => $mktColor,
                'amount' => 'Rp ' . number_format($poTotalSupport, 0, ',', '.'),
                'sub'    => $mktContrib . '% dari total PO tahunan',
            ],
            [
                'label'  => 'Marketing Quotation',
                'icon'   => 'mdi-file-document-outline',
                'color'  => 'secondary',
                'amount' => 'Rp ' . number_format($quoteTotalSupport, 0, ',', '.'),
                'sub'    => $quoteCountSupport . ' quotation',
            ],
        ];
    ?>
    <div class="row mb-4 g-3">
        <?php $__currentLoopData = $yearCards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $card): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="col-6 col-md-4 col-lg-3">
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

    
    <?php
        $s1Labels = ['Jan','Feb','Mar','Apr','Mei','Jun'];
        $s2Labels = ['Jul','Ags','Sep','Okt','Nov','Des'];
        $s1Totals = [];
        $s2Totals = [];
        for ($m = 1; $m <= 6; $m++) {
            $s1Totals[] = array_sum(array_column(array_map(fn($s) => ['v' => $s['jumlah'][$m]], $data), 'v'));
        }
        for ($m = 7; $m <= 12; $m++) {
            $s2Totals[] = array_sum(array_column(array_map(fn($s) => ['v' => $s['jumlah'][$m]], $data), 'v'));
        }
        $s1TargetLine = $totalTarget > 0 ? array_fill(0, 6, $totalTarget) : array_fill(0, 6, 0);
        $s2TargetLine = $totalTarget > 0 ? array_fill(0, 6, $totalTarget) : array_fill(0, 6, 0);
    ?>
    <div class="row mb-4">
        <div class="col-md-6 mb-3">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0">Semester 1 — Total Penjualan per Bulan</h5>
                    <small class="text-muted">Januari – Juni <?php echo e($year); ?></small>
                </div>
                <div class="card-body">
                    <div id="chartS1"></div>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0">Semester 2 — Total Penjualan per Bulan</h5>
                    <small class="text-muted">Juli – Desember <?php echo e($year); ?></small>
                </div>
                <div class="card-body">
                    <div id="chartS2"></div>
                </div>
            </div>
        </div>
    </div>

    
    <div class="card mb-4">
        <div class="card-header d-flex align-items-center justify-content-between">
            <div>
                <h5 class="mb-0">Semester 1</h5>
                <small class="text-muted">Januari – Juni <?php echo e($year); ?></small>
            </div>
            <?php
                $totalS1All = array_sum(array_map(fn($s) => array_sum(array_slice($s['jumlah'], 0, 6, true)), $data));
                $pctS1All   = $totalTarget > 0 ? round(($totalS1All / ($totalTarget * 6)) * 100, 1) : 0;
            ?>
            <span class="badge bg-label-<?php echo e($pctS1All >= 100 ? 'success' : ($pctS1All >= 80 ? 'warning' : 'danger')); ?> fs-6">
                Total: Rp <?php echo e(number_format($totalS1All, 0, ',', '.')); ?> &nbsp;|&nbsp; <?php echo e($pctS1All); ?>%
            </span>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th style="min-width:170px">Sales</th>
                        <?php for($m = 1; $m <= 6; $m++): ?>
                            <th class="text-center"><?php echo e($bulanMap[$m]); ?></th>
                        <?php endfor; ?>
                        <th class="text-center">Total S1</th>
                        <th class="text-center">% Target</th>
                        <th class="text-center">Detail</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        // Susun rows S1 (regular sales + Team E-Commerce), urutkan berdasarkan % pencapaian
                        $s1Rows = [];
                        foreach ($regularSales as $sale) {
                            $t = 0;
                            for ($m = 1; $m <= 6; $m++) $t += $sale['jumlah'][$m];
                            $p = $sale['target'] > 0 ? round(($t / ($sale['target'] * 6)) * 100, 1) : 0;
                            $s1Rows[] = ['isTeam' => false, 'sale' => $sale, 'total' => $t, 'pct' => $p];
                        }
                        if (count($ecommerceMembers) > 0) {
                            $t = 0;
                            for ($m = 1; $m <= 6; $m++) $t += $teamJumlah[$m];
                            $p = $teamTarget > 0 ? round(($t / ($teamTarget * 6)) * 100, 1) : 0;
                            $s1Rows[] = ['isTeam' => true, 'total' => $t, 'pct' => $p];
                        }
                        usort($s1Rows, fn($a, $b) => $b['pct'] <=> $a['pct']);
                    ?>
                    <?php $__currentLoopData = $s1Rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if($row['isTeam']): ?>
                            <?php
                                $teamS1Total = $row['total'];
                                $teamS1Pct   = $row['pct'];
                                $teamS1Lbl   = $teamS1Pct >= 100 ? 'success' : ($teamS1Pct >= 80 ? 'warning' : 'danger');
                            ?>
                            <tr class="table-warning">
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="<?php echo e(url('') . '/' . $teamImage); ?>" class="rounded-circle"
                                            width="30" height="30" style="object-fit:cover;">
                                        <div>
                                            <div class="fw-semibold">Team E-Commerce</div>
                                            <div class="d-flex gap-1 mt-1">
                                                <?php $__currentLoopData = $ecommerceMembers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $member): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <span class="badge bg-label-secondary" style="font-size:0.65rem"><?php echo e($member['name']); ?></span>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <?php for($m = 1; $m <= 6; $m++): ?>
                                    <?php
                                        $nomS1m  = $teamJumlah[$m];
                                        $pctMoS1 = $teamTarget > 0 ? round(($nomS1m / $teamTarget) * 100, 1) : 0;
                                        $barS1   = min($pctMoS1, 100);
                                        $clrS1   = $pctMoS1 >= 100 ? 'success' : ($pctMoS1 >= 80 ? 'warning' : 'danger');
                                    ?>
                                    <td style="min-width:110px">
                                        <div class="text-end text-nowrap small">
                                            <?php echo e($nomS1m > 0 ? number_format($nomS1m, 0, ',', '.') : '—'); ?>

                                        </div>
                                        <?php if($nomS1m > 0): ?>
                                            <div class="d-flex align-items-center gap-1 mt-1">
                                                <div class="progress flex-grow-1" style="height:4px">
                                                    <div class="progress-bar bg-<?php echo e($clrS1); ?>" style="width:<?php echo e($barS1); ?>%"></div>
                                                </div>
                                                <small class="text-<?php echo e($clrS1); ?> fw-semibold" style="font-size:0.7rem"><?php echo e($pctMoS1); ?>%</small>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                <?php endfor; ?>
                                <td class="text-end text-nowrap fw-bold"><?php echo e(number_format($teamS1Total, 0, ',', '.')); ?></td>
                                <td class="text-center"><span class="badge bg-label-<?php echo e($teamS1Lbl); ?>"><?php echo e($teamS1Pct); ?>%</span></td>
                                <td class="text-center">
                                    <?php if($reportS1): ?>
                                        <div class="dropdown">
                                            <button class="btn btn-icon btn-sm btn-outline-primary waves-effect dropdown-toggle"
                                                type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="mdi mdi-eye-outline"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <?php $__currentLoopData = $ecommerceMembers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $member): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <li>
                                                        <a class="dropdown-item"
                                                            href="<?php echo e(route('overview-sales.semester', [$reportS1->id, $member['id']])); ?>">
                                                            <?php echo e($member['name']); ?>

                                                        </a>
                                                    </li>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </ul>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-muted">—</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php
                                $sale    = $row['sale'];
                                $totalS1 = $row['total'];
                                $pctS1   = $row['pct'];
                                $lblS1   = $pctS1 >= 100 ? 'success' : ($pctS1 >= 80 ? 'warning' : 'danger');
                            ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="<?php echo e(url('') . '/' . $sale['image']); ?>" class="rounded-circle"
                                            width="30" height="30" style="object-fit:cover;">
                                        <span class="fw-semibold"><?php echo e($sale['name']); ?></span>
                                    </div>
                                </td>
                                <?php for($m = 1; $m <= 6; $m++): ?>
                                    <?php
                                        $nomS1m  = $sale['jumlah'][$m];
                                        $pctMoS1 = $sale['target'] > 0 ? round(($nomS1m / $sale['target']) * 100, 1) : 0;
                                        $barS1   = min($pctMoS1, 100);
                                        $clrS1   = $pctMoS1 >= 100 ? 'success' : ($pctMoS1 >= 80 ? 'warning' : 'danger');
                                    ?>
                                    <td style="min-width:110px">
                                        <div class="text-end text-nowrap small">
                                            <?php echo e($nomS1m > 0 ? number_format($nomS1m, 0, ',', '.') : '—'); ?>

                                        </div>
                                        <?php if($nomS1m > 0): ?>
                                            <div class="d-flex align-items-center gap-1 mt-1">
                                                <div class="progress flex-grow-1" style="height:4px">
                                                    <div class="progress-bar bg-<?php echo e($clrS1); ?>"
                                                        style="width:<?php echo e($barS1); ?>%"></div>
                                                </div>
                                                <small class="text-<?php echo e($clrS1); ?> fw-semibold"
                                                    style="font-size:0.7rem"><?php echo e($pctMoS1); ?>%</small>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                <?php endfor; ?>
                                <td class="text-end text-nowrap fw-bold"><?php echo e(number_format($totalS1, 0, ',', '.')); ?></td>
                                <td class="text-center">
                                    <span class="badge bg-label-<?php echo e($lblS1); ?>"><?php echo e($pctS1); ?>%</span>
                                </td>
                                <td class="text-center">
                                    <?php if($reportS1): ?>
                                        <a href="<?php echo e(route('overview-sales.semester', [$reportS1->id, $sale['id']])); ?>"
                                            class="btn btn-icon btn-sm btn-outline-primary waves-effect"
                                            title="Lihat Detail Semester 1">
                                            <i class="mdi mdi-eye-outline"></i>
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">—</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    
                    <?php
                        $supportS1 = 0;
                        for ($m = 1; $m <= 6; $m++) $supportS1 += $dataSupport[$m];
                    ?>
                    <tr class="table-secondary">
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <img src="<?php echo e(url('') . '/' . $support->image); ?>" class="rounded-circle"
                                    width="30" height="30" style="object-fit:cover;">
                                <span class="fw-semibold"><?php echo e($support->name); ?></span>
                                <span class="badge bg-label-info ms-1">Marketing</span>
                            </div>
                        </td>
                        <?php for($m = 1; $m <= 6; $m++): ?>
                            <td class="text-end text-nowrap">
                                <?php echo e($dataSupport[$m] > 0 ? number_format($dataSupport[$m], 0, ',', '.') : '—'); ?>

                            </td>
                        <?php endfor; ?>
                        <td class="text-end text-nowrap fw-bold"><?php echo e(number_format($supportS1, 0, ',', '.')); ?></td>
                        <td class="text-center text-muted">—</td>
                        <td class="text-center">
                            <?php if($reportS1): ?>
                                <a href="<?php echo e(route('overview-sales.semester', [$reportS1->id, $support->id])); ?>"
                                    class="btn btn-icon btn-sm btn-outline-primary waves-effect"
                                    title="Lihat Detail Semester 1">
                                    <i class="mdi mdi-eye-outline"></i>
                                </a>
                            <?php else: ?>
                                <span class="text-muted">—</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    
    <div class="card mb-4">
        <div class="card-header d-flex align-items-center justify-content-between">
            <div>
                <h5 class="mb-0">Semester 2</h5>
                <small class="text-muted">Juli – Desember <?php echo e($year); ?></small>
            </div>
            <?php
                $totalS2All = array_sum(array_map(fn($s) => array_sum(array_slice($s['jumlah'], 6, 6, true)), $data));
                $pctS2All   = $totalTarget > 0 ? round(($totalS2All / ($totalTarget * 6)) * 100, 1) : 0;
            ?>
            <span class="badge bg-label-<?php echo e($pctS2All >= 100 ? 'success' : ($pctS2All >= 80 ? 'warning' : 'danger')); ?> fs-6">
                Total: Rp <?php echo e(number_format($totalS2All, 0, ',', '.')); ?> &nbsp;|&nbsp; <?php echo e($pctS2All); ?>%
            </span>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th style="min-width:170px">Sales</th>
                        <?php for($m = 7; $m <= 12; $m++): ?>
                            <th class="text-center"><?php echo e($bulanMap[$m]); ?></th>
                        <?php endfor; ?>
                        <th class="text-center">Total S2</th>
                        <th class="text-center">% Target</th>
                        <th class="text-center">Detail</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        // Susun rows S2 (regular sales + Team E-Commerce), urutkan berdasarkan % pencapaian
                        $s2Rows = [];
                        foreach ($regularSales as $sale) {
                            $t = 0;
                            for ($m = 7; $m <= 12; $m++) $t += $sale['jumlah'][$m];
                            $p = $sale['target'] > 0 ? round(($t / ($sale['target'] * 6)) * 100, 1) : 0;
                            $s2Rows[] = ['isTeam' => false, 'sale' => $sale, 'total' => $t, 'pct' => $p];
                        }
                        if (count($ecommerceMembers) > 0) {
                            $t = 0;
                            for ($m = 7; $m <= 12; $m++) $t += $teamJumlah[$m];
                            $p = $teamTarget > 0 ? round(($t / ($teamTarget * 6)) * 100, 1) : 0;
                            $s2Rows[] = ['isTeam' => true, 'total' => $t, 'pct' => $p];
                        }
                        usort($s2Rows, fn($a, $b) => $b['pct'] <=> $a['pct']);
                    ?>
                    <?php $__currentLoopData = $s2Rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if($row['isTeam']): ?>
                            <?php
                                $teamS2Total = $row['total'];
                                $teamS2Pct   = $row['pct'];
                                $teamS2Lbl   = $teamS2Pct >= 100 ? 'success' : ($teamS2Pct >= 80 ? 'warning' : 'danger');
                            ?>
                            <tr class="table-warning">
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="<?php echo e(url('') . '/' . $teamImage); ?>" class="rounded-circle"
                                            width="30" height="30" style="object-fit:cover;">
                                        <div>
                                            <div class="fw-semibold">Team E-Commerce</div>
                                            <div class="d-flex gap-1 mt-1">
                                                <?php $__currentLoopData = $ecommerceMembers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $member): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <span class="badge bg-label-secondary" style="font-size:0.65rem"><?php echo e($member['name']); ?></span>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <?php for($m = 7; $m <= 12; $m++): ?>
                                    <?php
                                        $nomS2m  = $teamJumlah[$m];
                                        $pctMoS2 = $teamTarget > 0 ? round(($nomS2m / $teamTarget) * 100, 1) : 0;
                                        $barS2   = min($pctMoS2, 100);
                                        $clrS2   = $pctMoS2 >= 100 ? 'success' : ($pctMoS2 >= 80 ? 'warning' : 'danger');
                                    ?>
                                    <td style="min-width:110px">
                                        <div class="text-end text-nowrap small">
                                            <?php echo e($nomS2m > 0 ? number_format($nomS2m, 0, ',', '.') : '—'); ?>

                                        </div>
                                        <?php if($nomS2m > 0): ?>
                                            <div class="d-flex align-items-center gap-1 mt-1">
                                                <div class="progress flex-grow-1" style="height:4px">
                                                    <div class="progress-bar bg-<?php echo e($clrS2); ?>" style="width:<?php echo e($barS2); ?>%"></div>
                                                </div>
                                                <small class="text-<?php echo e($clrS2); ?> fw-semibold" style="font-size:0.7rem"><?php echo e($pctMoS2); ?>%</small>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                <?php endfor; ?>
                                <td class="text-end text-nowrap fw-bold"><?php echo e(number_format($teamS2Total, 0, ',', '.')); ?></td>
                                <td class="text-center"><span class="badge bg-label-<?php echo e($teamS2Lbl); ?>"><?php echo e($teamS2Pct); ?>%</span></td>
                                <td class="text-center">
                                    <?php if($reportS2): ?>
                                        <div class="dropdown">
                                            <button class="btn btn-icon btn-sm btn-outline-primary waves-effect dropdown-toggle"
                                                type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="mdi mdi-eye-outline"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <?php $__currentLoopData = $ecommerceMembers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $member): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <li>
                                                        <a class="dropdown-item"
                                                            href="<?php echo e(route('overview-sales.semester', [$reportS2->id, $member['id']])); ?>">
                                                            <?php echo e($member['name']); ?>

                                                        </a>
                                                    </li>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </ul>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-muted">—</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php
                                $sale    = $row['sale'];
                                $totalS2 = $row['total'];
                                $pctS2   = $row['pct'];
                                $lblS2   = $pctS2 >= 100 ? 'success' : ($pctS2 >= 80 ? 'warning' : 'danger');
                            ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="<?php echo e(url('') . '/' . $sale['image']); ?>" class="rounded-circle"
                                            width="30" height="30" style="object-fit:cover;">
                                        <span class="fw-semibold"><?php echo e($sale['name']); ?></span>
                                    </div>
                                </td>
                                <?php for($m = 7; $m <= 12; $m++): ?>
                                    <?php
                                        $nomS2m  = $sale['jumlah'][$m];
                                        $pctMoS2 = $sale['target'] > 0 ? round(($nomS2m / $sale['target']) * 100, 1) : 0;
                                        $barS2   = min($pctMoS2, 100);
                                        $clrS2   = $pctMoS2 >= 100 ? 'success' : ($pctMoS2 >= 80 ? 'warning' : 'danger');
                                    ?>
                                    <td style="min-width:110px">
                                        <div class="text-end text-nowrap small">
                                            <?php echo e($nomS2m > 0 ? number_format($nomS2m, 0, ',', '.') : '—'); ?>

                                        </div>
                                        <?php if($nomS2m > 0): ?>
                                            <div class="d-flex align-items-center gap-1 mt-1">
                                                <div class="progress flex-grow-1" style="height:4px">
                                                    <div class="progress-bar bg-<?php echo e($clrS2); ?>"
                                                        style="width:<?php echo e($barS2); ?>%"></div>
                                                </div>
                                                <small class="text-<?php echo e($clrS2); ?> fw-semibold"
                                                    style="font-size:0.7rem"><?php echo e($pctMoS2); ?>%</small>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                <?php endfor; ?>
                                <td class="text-end text-nowrap fw-bold"><?php echo e(number_format($totalS2, 0, ',', '.')); ?></td>
                                <td class="text-center">
                                    <span class="badge bg-label-<?php echo e($lblS2); ?>"><?php echo e($pctS2); ?>%</span>
                                </td>
                                <td class="text-center">
                                    <?php if($reportS2): ?>
                                        <a href="<?php echo e(route('overview-sales.semester', [$reportS2->id, $sale['id']])); ?>"
                                            class="btn btn-icon btn-sm btn-outline-primary waves-effect"
                                            title="Lihat Detail Semester 2">
                                            <i class="mdi mdi-eye-outline"></i>
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">—</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    
                    <?php
                        $supportS2 = 0;
                        for ($m = 7; $m <= 12; $m++) $supportS2 += $dataSupport[$m];
                    ?>
                    <tr class="table-secondary">
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <img src="<?php echo e(url('') . '/' . $support->image); ?>" class="rounded-circle"
                                    width="30" height="30" style="object-fit:cover;">
                                <span class="fw-semibold"><?php echo e($support->name); ?></span>
                                <span class="badge bg-label-info ms-1">Marketing</span>
                            </div>
                        </td>
                        <?php for($m = 7; $m <= 12; $m++): ?>
                            <td class="text-end text-nowrap">
                                <?php echo e($dataSupport[$m] > 0 ? number_format($dataSupport[$m], 0, ',', '.') : '—'); ?>

                            </td>
                        <?php endfor; ?>
                        <td class="text-end text-nowrap fw-bold"><?php echo e(number_format($supportS2, 0, ',', '.')); ?></td>
                        <td class="text-center text-muted">—</td>
                        <td class="text-center">
                            <?php if($reportS2): ?>
                                <a href="<?php echo e(route('overview-sales.semester', [$reportS2->id, $support->id])); ?>"
                                    class="btn btn-icon btn-sm btn-outline-primary waves-effect"
                                    title="Lihat Detail Semester 2">
                                    <i class="mdi mdi-eye-outline"></i>
                                </a>
                            <?php else: ?>
                                <span class="text-muted">—</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                </tbody>
            </table>
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
            const isDark       = document.documentElement.classList.contains('dark-style');
            const labelColor   = isDark ? '#a8aaae' : '#6d6b77';
            const headingColor = isDark ? '#cfd3ec' : '#444564';
            const borderColor  = isDark ? '#404152' : '#dbdade';
            const cardColor    = isDark ? '#2f3349' : '#fff';

            const formatRp = val => {
                if (val >= 1_000_000_000) return 'Rp ' + (val / 1_000_000_000).toFixed(1) + 'M';
                if (val >= 1_000_000)     return 'Rp ' + (val / 1_000_000).toFixed(1) + 'jt';
                return 'Rp ' + val.toLocaleString('id-ID');
            };

            const baseOptions = (labels, salesData, targetData) => ({
                chart: {
                    type: 'bar',
                    height: 280,
                    toolbar: { show: false },
                    parentHeightOffset: 0,
                },
                series: [
                    { name: 'Total Penjualan', type: 'bar',  data: salesData },
                    { name: 'Target Bulanan',  type: 'line', data: targetData },
                ],
                colors: ['#696cff', '#ff4c51'],
                plotOptions: {
                    bar: {
                        borderRadius: 6,
                        columnWidth: '45%',
                    },
                },
                stroke: {
                    width: [0, 2],
                    curve: 'smooth',
                    dashArray: [0, 5],
                },
                markers: {
                    size: [0, 4],
                    strokeWidth: 2,
                    colors: [cardColor],
                    strokeColors: '#ff4c51',
                },
                dataLabels: {
                    enabled: true,
                    enabledOnSeries: [0],
                    formatter: function (val, opts) {
                        if (val === 0) return '';
                        const target = targetData[opts.dataPointIndex];
                        if (!target || target === 0) return '';
                        const pct = Math.round((val / target) * 100);
                        return pct + '%';
                    },
                    style: {
                        fontSize: '11px',
                        fontWeight: 'bold',
                        colors: ['#fff'],
                    },
                    background: { enabled: false },
                    offsetY: -20,
                },
                legend: {
                    show: true,
                    position: 'top',
                    labels: { colors: labelColor },
                },
                xaxis: {
                    categories: labels,
                    labels: {
                        style: { colors: labelColor, fontSize: '13px' },
                    },
                    axisBorder: { show: false },
                    axisTicks: { show: false },
                },
                yaxis: {
                    labels: {
                        formatter: formatRp,
                        style: { colors: labelColor, fontSize: '11px' },
                    },
                },
                grid: {
                    borderColor,
                    strokeDashArray: 5,
                    padding: { top: -10, bottom: -5 },
                },
                tooltip: {
                    y: { formatter: val => 'Rp ' + val.toLocaleString('id-ID') },
                },
            });

            const s1Data     = <?php echo json_encode($s1Totals, 15, 512) ?>;
            const s2Data     = <?php echo json_encode($s2Totals, 15, 512) ?>;
            const s1Target   = <?php echo json_encode($s1TargetLine, 15, 512) ?>;
            const s2Target   = <?php echo json_encode($s2TargetLine, 15, 512) ?>;
            const s1Labels   = <?php echo json_encode($s1Labels, 15, 512) ?>;
            const s2Labels   = <?php echo json_encode($s2Labels, 15, 512) ?>;

            const chartS1El = document.querySelector('#chartS1');
            if (chartS1El) {
                new ApexCharts(chartS1El, baseOptions(s1Labels, s1Data, s1Target)).render();
            }
            const chartS2El = document.querySelector('#chartS2');
            if (chartS2El) {
                new ApexCharts(chartS2El, baseOptions(s2Labels, s2Data, s2Target)).render();
            }
        })();
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.sales.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u877155683/domains/reftech.my.id/reftech.my.id/resources/views/pages/admin/report-year.blade.php ENDPATH**/ ?>