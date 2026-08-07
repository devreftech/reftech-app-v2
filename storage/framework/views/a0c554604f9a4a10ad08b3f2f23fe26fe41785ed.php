
<?php $__env->startSection('title', 'Sales Overview'); ?>
<?php $__env->startSection('content'); ?>
    <?php
        $fullPercent  = $totalTarget > 0 ? round(($poTotal / ($totalTarget * 6)) * 100, 1) : 0;
        $pctColor     = $fullPercent >= 100 ? 'success' : ($fullPercent >= 80 ? 'warning' : 'danger');
        $semesterLabel = $report->semester == 1 ? 'January – June' : 'July – December';
        $s1Report     = $semester->where('year', $report->year)->where('semester', 1)->first();
        $s2Report     = $semester->where('year', $report->year)->where('semester', 2)->first();
    ?>
    <div class="d-flex align-items-start justify-content-between flex-wrap gap-3 mb-4">

        
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-label-primary fs-6 px-3 py-2">
                    <i class="mdi mdi-chart-areaspline me-1"></i> Semester <?php echo e($report->semester); ?>

                </span>
                <span class="text-muted fw-semibold"><?php echo e($report->year); ?></span>
                <span class="text-muted">•</span>
                <small class="text-muted"><?php echo e($semesterLabel); ?></small>
            </div>
            <h4 class="fw-bold mb-1 text-heading">Sales Overview Report</h4>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-<?php echo e($pctColor); ?> rounded-pill px-3">
                    <?php echo e($fullPercent); ?>% of target achieved
                </span>
                <small class="text-muted">Rp <?php echo e(number_format($poTotal, 0, ',', '.')); ?> of Rp <?php echo e(number_format($totalTarget * 6, 0, ',', '.')); ?></small>
            </div>
        </div>

        
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <div class="btn-group" role="group">
                <?php if($s1Report): ?>
                    <a href="<?php echo e(route('report.semester', $s1Report->id)); ?>"
                       class="btn btn-sm waves-effect <?php echo e($report->semester == 1 ? 'btn-primary' : 'btn-outline-primary'); ?>">
                        Semester 1
                    </a>
                <?php endif; ?>
                <?php if($s2Report): ?>
                    <a href="<?php echo e(route('report.semester', $s2Report->id)); ?>"
                       class="btn btn-sm waves-effect <?php echo e($report->semester == 2 ? 'btn-primary' : 'btn-outline-primary'); ?>">
                        Semester 2
                    </a>
                <?php endif; ?>
            </div>
            <div class="btn-group">
                <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle waves-effect"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="mdi mdi-calendar me-1"></i> <?php echo e($report->year); ?>

                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <?php $__currentLoopData = $semester->pluck('year')->unique()->sortDesc(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $yr): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li>
                            <a class="dropdown-item waves-effect <?php echo e($yr == $report->year ? 'active' : ''); ?>"
                                href="<?php echo e(route('report.year', $yr)); ?>"><?php echo e($yr); ?></a>
                        </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        </div>

    </div>
    <?php
        $winRate        = $quoteOnCount > 0 ? round(($poCount   / $quoteOnCount) * 100, 1) : 0;
        $lossRate       = $quoteOnCount > 0 ? round(($lossCount / $quoteOnCount) * 100, 1) : 0;
        $mktContrib     = $poTotal > 0 ? round(($poTotalSupport / $poTotal) * 100, 1) : 0;
        $winColor       = $winRate  >= 50 ? 'success' : ($winRate  >= 30 ? 'warning' : 'danger');
        $lossColor      = $lossRate <= 20 ? 'success' : ($lossRate <= 40 ? 'warning' : 'danger');
        $mktColor       = $mktContrib >= 30 ? 'success' : ($mktContrib >= 15 ? 'warning' : 'secondary');
        $cards = [
            [
                'label'  => 'Purchase Order',
                'icon'   => 'mdi-cart-plus',
                'color'  => 'success',
                'amount' => 'Rp ' . number_format($poTotal, 0, ',', '.'),
                'sub'    => $poCount . ' transactions',
            ],
            [
                'label'  => 'Total Quotation',
                'icon'   => 'mdi-cart',
                'color'  => 'primary',
                'amount' => 'Rp ' . number_format($quoteOnTotal, 0, ',', '.'),
                'sub'    => $quoteOnCount . ' transactions',
            ],
            [
                'label'  => 'Active Quotation',
                'icon'   => 'mdi-cart-outline',
                'color'  => 'info',
                'amount' => 'Rp ' . number_format($quoteTotal, 0, ',', '.'),
                'sub'    => $quoteCount . ' transactions',
            ],
            [
                'label'  => 'Loss',
                'icon'   => 'mdi-cart-minus',
                'color'  => 'danger',
                'amount' => 'Rp ' . number_format($lossTotal, 0, ',', '.'),
                'sub'    => $lossCount . ' transactions',
            ],
            [
                'label'  => 'Convertion Rate',
                'icon'   => 'mdi-trophy-outline',
                'color'  => $winColor,
                'amount' => $winRate . '%',
                'sub'    => $poCount . ' PO of ' . $quoteOnCount . ' quotations',
            ],
            [
                'label'  => 'Loss Rate',
                'icon'   => 'mdi-trending-down',
                'color'  => $lossColor,
                'amount' => $lossRate . '%',
                'sub'    => $lossCount . ' loss of ' . $quoteOnCount . ' quotations',
            ],
            [
                'label'  => 'Marketing Contribution',
                'icon'   => 'mdi-handshake-outline',
                'color'  => $mktColor,
                'amount' => 'Rp ' . number_format($poTotalSupport, 0, ',', '.'),
                'sub'    => $mktContrib . '% of total semester PO',
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
        <?php $__currentLoopData = $cards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $card): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
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
        $bulanLabelShort = [
            1=>'Jan',2=>'Feb',3=>'Mar',4=>'Apr',5=>'May',6=>'Jun',
            7=>'Jul',8=>'Aug',9=>'Sep',10=>'Oct',11=>'Nov',12=>'Dec',
        ];
        $startMonthChart = $report->semester == 1 ? 1 : 7;
        $ecommerceIdsChart = [16, 23];

        $chartLabels  = [];
        $chartSeries  = [];
        $combinedTotal = array_fill(0, 6, 0);

        for ($i = 0; $i < 6; $i++) {
            $chartLabels[] = $bulanLabelShort[$startMonthChart + $i];
        }

        // Regular sales
        foreach (array_filter($data, fn($s) => !in_array($s['id'], $ecommerceIdsChart)) as $s) {
            $monthlyData = [];
            for ($i = 0; $i < 6; $i++) {
                $val = $s['jumlah'][$i]['total'] ?? 0;
                $monthlyData[] = $val;
                $combinedTotal[$i] += $val;
            }
            $chartSeries[] = ['name' => $s['name'], 'data' => $monthlyData];
        }

        // E-Commerce team digabung
        $ecoMembers = array_filter($data, fn($s) => in_array($s['id'], $ecommerceIdsChart));
        if (count($ecoMembers) > 0) {
            $ecoData = array_fill(0, 6, 0);
            foreach ($ecoMembers as $m) {
                for ($i = 0; $i < 6; $i++) {
                    $val = $m['jumlah'][$i]['total'] ?? 0;
                    $ecoData[$i] += $val;
                    $combinedTotal[$i] += $val;
                }
            }
            $chartSeries[] = ['name' => 'E-Commerce', 'data' => array_values($ecoData)];
        }

        $chartTargetLine = array_fill(0, 6, $totalTarget);

        $pctByMonth = [];
        foreach (array_values($combinedTotal) as $total) {
            $pctByMonth[] = $totalTarget > 0 ? round($total / $totalTarget * 100) : 0;
        }
    ?>
    <div class="card mb-4">
        <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div>
                <h5 class="mb-0">Sales Chart — Semester <?php echo e($report->semester); ?> · <?php echo e($report->year); ?></h5>
                <small class="text-muted">
                    <?php echo e($report->semester == 1 ? 'January – June' : 'July – December'); ?> <?php echo e($report->year); ?> &bull; All Sales
                </small>
            </div>
            <div class="text-end">
                <?php
                    $totalSemester = array_sum($combinedTotal);
                    $pctSemester   = $totalTarget > 0 ? round(($totalSemester / ($totalTarget * 6)) * 100, 1) : 0;
                    $badgeSem      = $pctSemester >= 100 ? 'success' : ($pctSemester >= 80 ? 'warning' : 'danger');
                ?>
                <span class="badge bg-label-<?php echo e($badgeSem); ?> fs-6">
                    Rp <?php echo e(number_format($totalSemester, 0, ',', '.')); ?> &nbsp;|&nbsp; <?php echo e($pctSemester); ?>%
                </span>
            </div>
        </div>
        <div class="card-body">
            <div id="chartPenjualanSemester"></div>
            <div class="d-flex mt-1" style="padding-left:72px; padding-right:10px;">
                <?php $__currentLoopData = $pctByMonth; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pct): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $badgePct = $pct >= 100 ? 'success' : ($pct >= 80 ? 'warning' : 'danger');
                    ?>
                    <div class="flex-fill text-center">
                        <span class="badge rounded-pill bg-<?php echo e($badgePct); ?> px-2"><?php echo e($pct); ?>%</span>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </div>

    
    <div class="card mt-4">
        <div class="card-header d-flex align-items-center justify-content-between py-3">
            <div class="d-flex align-items-center gap-2 p-1 rounded"
                 style="background:rgba(105,108,255,0.08); border:1px solid rgba(105,108,255,0.2);">
                <button class="btn btn-sm fw-semibold px-4 py-2 active-tab-btn active"
                        id="tab-sales-btn" data-bs-toggle="tab" data-bs-target="#tab-sales"
                        type="button" role="tab"
                        style="border-radius:6px; transition:all .2s;">
                    <i class="mdi mdi-account-group-outline me-1"></i> Sales Team
                </button>
                <button class="btn btn-sm fw-semibold px-4 py-2 active-tab-btn"
                        id="tab-marketing-btn" data-bs-toggle="tab" data-bs-target="#tab-marketing"
                        type="button" role="tab"
                        style="border-radius:6px; transition:all .2s;">
                    <i class="mdi mdi-handshake-outline me-1"></i> Marketing
                    <?php if($smktProspectCount > 0): ?>
                        <span class="badge bg-primary ms-1 rounded-pill"><?php echo e($smktProspectCount); ?></span>
                    <?php endif; ?>
                </button>
            </div>
            <small class="text-muted d-none d-md-block">
                Semester <?php echo e($report->semester); ?> · <?php echo e($report->year); ?>

            </small>
        </div>
        <div class="tab-content">

            
            <div class="tab-pane fade show active p-3" id="tab-sales" role="tabpanel">
                <div class="row">
        <?php
            $bulanMap = [
                1 => 'January',
                2 => 'February',
                3 => 'March',
                4 => 'April',
                5 => 'May',
                6 => 'June',
                7 => 'July',
                8 => 'August',
                9 => 'September',
                10 => 'October',
                11 => 'November',
                12 => 'December',
            ];
        ?>
        <?php
            $ecommerceIds = [16, 23];
            $ecommerceMembers = array_values(array_filter($data, fn($s) => in_array($s['id'], $ecommerceIds)));
            $regularSales = array_values(array_filter($data, fn($s) => !in_array($s['id'], $ecommerceIds)));

            // Gabungkan data bulanan Tim E-Commerce
            $teamTotal       = array_sum(array_column($ecommerceMembers, 'total'));
            $teamTarget      = array_sum(array_column($ecommerceMembers, 'target'));
            $teamMktProspect = array_sum(array_column($ecommerceMembers, 'mktProspect'));
            $teamMktQuote    = array_sum(array_column($ecommerceMembers, 'mktQuote'));
            $teamMktPo       = array_sum(array_column($ecommerceMembers, 'mktPo'));
            $teamJumlah = [];
            foreach ($ecommerceMembers as $member) {
                foreach ($member['jumlah'] as $j) {
                    $bulan = $j['bulan'];
                    if (!isset($teamJumlah[$bulan])) $teamJumlah[$bulan] = 0;
                    $teamJumlah[$bulan] += $j['total'];
                }
            }
            ksort($teamJumlah);
            $teamJumlahArr = [];
            foreach ($teamJumlah as $b => $t) {
                $teamJumlahArr[] = ['bulan' => $b, 'total' => $t];
            }
        ?>
        
        <?php $__currentLoopData = $regularSales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sale): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="col-6 col-lg-4 mb-3">
                <div class="h-100 d-flex flex-column rounded border p-3">
                    <div class="flex-grow-1">
                        <div class="row mb-4">
                            <div class="col-3 p-0">
                                <img src="<?php echo e(url('') . '/' . $sale['image']); ?>" alt="" srcset=""
                                    class="rounded" style="width : 100%; height:100%;">
                            </div>
                            <div class="col-9">
                                <h4 class="badge bg-label-primary w-100 text-center fs-4 fw-semibold"><?php echo e($sale['name']); ?></h4>
                                <h5 class="text-center">Rp <?php echo e(number_format($sale['total'], 0, ',', '.')); ?></h5>
                                <?php if(($sale['mktProspect'] ?? 0) > 0): ?>
                                    <?php
                                        $sMktRateQ  = $sale['mktProspect'] > 0 ? round(($sale['mktQuote'] / $sale['mktProspect']) * 100, 0) : 0;
                                        $sMktRatePo = $sale['mktQuote']    > 0 ? round(($sale['mktPo']    / $sale['mktQuote'])    * 100, 0) : 0;
                                    ?>
                                    <div class="d-flex align-items-center justify-content-center gap-1 flex-wrap mt-1">
                                        <span class="badge bg-label-secondary" style="font-size:0.68rem">
                                            <i class="mdi mdi-handshake-outline"></i> <?php echo e($sale['mktProspect']); ?> prospect
                                        </span>
                                        <span class="text-muted" style="font-size:0.65rem">→<?php echo e($sMktRateQ); ?>%→</span>
                                        <span class="badge bg-label-primary" style="font-size:0.68rem"><?php echo e($sale['mktQuote']); ?> quote</span>
                                        <span class="text-muted" style="font-size:0.65rem">→<?php echo e($sMktRatePo); ?>%→</span>
                                        <span class="badge bg-label-success" style="font-size:0.68rem"><?php echo e($sale['mktPo']); ?> PO</span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="container">
                            <div class="row">
                                <?php $__currentLoopData = $sale['jumlah']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="col-4 mb-2">
                                        <div class="me-2">
                                            <h6 class="mb-0"><?php echo e($bulanMap[$item['bulan']]); ?></h6>
                                        </div>
                                    </div>
                                    <div class="col-6 mb-2">
                                        <p class="fw-semibold text-heading text-end p-0 m-0">Rp
                                            <?php echo e(number_format($item['total'], 0, ',', '.')); ?></p>
                                    </div>
                                    <div class="col-2 mb-2">
                                        <?php
                                            $persenanSales =
                                                $sale['target'] > 0
                                                    ? round(($item['total'] / $sale['target']) * 100, 2)
                                                    : 0;
                                            if ($persenanSales >= 100) {
                                                $label = 'success';
                                            } elseif ($persenanSales >= 90) {
                                                $label = 'warning';
                                            } else {
                                                $label = 'danger';
                                            }
                                        ?>
                                        <div class="badge bg-label-<?php echo e($label); ?> rounded-pill">
                                            <?php echo e($persenanSales); ?>%</div>
                                    </div>
                                    <hr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                    </div>
                    <a href="<?php echo e(route('overview-sales.semester', [$report->id, $sale['id']])); ?>"
                        class="btn btn-primary d-grid w-100 mt-auto">Details</a>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        

        
        <?php if(count($ecommerceMembers) > 0): ?>
            <div class="col-6 col-lg-4 mb-3">
                <div class="h-100 rounded border border-warning p-3">
                    <div>
                        <div class="row mb-4">
                            <div class="col-3 p-0">
                                <?php $mainMember = collect($ecommerceMembers)->firstWhere('id', 16); ?>
                                <img src="<?php echo e(url('') . '/' . $mainMember['image']); ?>" alt=""
                                    class="rounded" style="width:100%; height:100%;">
                            </div>
                            <div class="col-9">
                                <h4 class="badge bg-label-warning w-100 text-center fs-4 fw-semibold">Team E-Commerce</h4>
                                <h5 class="text-center">Rp <?php echo e(number_format($teamTotal, 0, ',', '.')); ?></h5>
                                <div class="d-flex flex-wrap gap-1 justify-content-center mt-1">
                                    <?php $__currentLoopData = $ecommerceMembers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $member): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <span class="badge bg-label-secondary small"><?php echo e($member['name']); ?></span>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                                <?php if($teamMktProspect > 0): ?>
                                    <?php
                                        $tMktRateQ  = $teamMktProspect > 0 ? round(($teamMktQuote / $teamMktProspect) * 100, 0) : 0;
                                        $tMktRatePo = $teamMktQuote    > 0 ? round(($teamMktPo    / $teamMktQuote)    * 100, 0) : 0;
                                    ?>
                                    <div class="d-flex align-items-center justify-content-center gap-1 flex-wrap mt-1">
                                        <span class="badge bg-label-secondary" style="font-size:0.68rem">
                                            <i class="mdi mdi-handshake-outline"></i> <?php echo e($teamMktProspect); ?> prospect
                                        </span>
                                        <span class="text-muted" style="font-size:0.65rem">→<?php echo e($tMktRateQ); ?>%→</span>
                                        <span class="badge bg-label-primary" style="font-size:0.68rem"><?php echo e($teamMktQuote); ?> quote</span>
                                        <span class="text-muted" style="font-size:0.65rem">→<?php echo e($tMktRatePo); ?>%→</span>
                                        <span class="badge bg-label-success" style="font-size:0.68rem"><?php echo e($teamMktPo); ?> PO</span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="container">
                            <div class="row">
                                <?php $__currentLoopData = $teamJumlahArr; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="col-4 mb-2">
                                        <h6 class="mb-0"><?php echo e($bulanMap[$item['bulan']]); ?></h6>
                                    </div>
                                    <div class="col-6 mb-2">
                                        <p class="fw-semibold text-heading text-end p-0 m-0">Rp
                                            <?php echo e(number_format($item['total'], 0, ',', '.')); ?></p>
                                    </div>
                                    <div class="col-2 mb-2">
                                        <?php
                                            $pct = $teamTarget > 0 ? round(($item['total'] / $teamTarget) * 100, 2) : 0;
                                            $lbl = $pct >= 100 ? 'success' : ($pct >= 90 ? 'warning' : 'danger');
                                        ?>
                                        <div class="badge bg-label-<?php echo e($lbl); ?> rounded-pill"><?php echo e($pct); ?>%</div>
                                    </div>
                                    <hr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="col-6 col-lg-4 mb-3">
            <div class="h-100 d-flex flex-column rounded border border-success p-3">
                <div class="flex-grow-1">
                    <div class="row mb-4">
                        <div class="col-3 p-0">
                            <img src="<?php echo e(url('') . '/' . $support->image); ?>" alt="" srcset="" class="rounded"
                                style="width : 100%; height:100%;">
                        </div>
                        <div class="col-9">
                            <h4 class="badge bg-label-success w-100 text-center fs-4 fw-semibold"><?php echo e($support->name); ?></h4>
                            <h5 class="text-center text fw-semibold">Rp <?php echo e(number_format($poTotalSupport, 0, ',', '.')); ?>

                            </h5>
                        </div>
                    </div>
                    <div class="container">
                        <div class="row">
                            <?php $__currentLoopData = $dataSupport; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="col-4 mb-2">
                                    <div class="me-2">
                                        <h6 class="mb-0"><?php echo e($bulanMap[$item->bulan]); ?></h6>
                                    </div>
                                </div>
                                <div class="col-8 mb-2">
                                    <p class="fw-semibold text-heading text-end p-0 m-0">Rp
                                        <?php echo e(number_format($item->total, 0, ',', '.')); ?></p>

                                </div>
                                <hr>
                                
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                </div>
                <a href="<?php echo e(route('overview-sales.semester', [$report->id, $support->id])); ?>"
                    class="btn btn-success d-grid w-100 mt-auto">Detail</a>
            </div>
        </div>
                </div>
            </div>

            
            <div class="tab-pane fade p-3" id="tab-marketing" role="tabpanel">
    
    <?php
        $smktProspectToQuote = $smktProspectCount > 0 ? round(($smktQuoteCount / $smktProspectCount) * 100, 1) : 0;
        $smktQuoteToPo       = $smktQuoteCount   > 0 ? round(($smktPoCount   / $smktQuoteCount)   * 100, 1) : 0;
        $smktStatusPending   = $smktProspectByStatus->pending   ?? 0;
        $smktStatusProvided  = $smktProspectByStatus->provided  ?? 0;
        $smktStatusNoProvide = $smktProspectByStatus->no_provide ?? 0;
        $smktPctPending      = $smktProspectCount > 0 ? round(($smktStatusPending   / $smktProspectCount) * 100, 1) : 0;
        $smktPctProvided     = $smktProspectCount > 0 ? round(($smktStatusProvided  / $smktProspectCount) * 100, 1) : 0;
        $smktPctNoProvide    = $smktProspectCount > 0 ? round(($smktStatusNoProvide / $smktProspectCount) * 100, 1) : 0;
        $smktSourceIcons = [
            'IG'          => ['icon' => 'mdi-instagram',        'color' => 'danger'],
            'Instagram'   => ['icon' => 'mdi-instagram',        'color' => 'danger'],
            'WhatsApp'    => ['icon' => 'mdi-whatsapp',         'color' => 'success'],
            'LinkedIn'    => ['icon' => 'mdi-linkedin',         'color' => 'info'],
            'Website'     => ['icon' => 'mdi-web',              'color' => 'primary'],
            'Indotrading' => ['icon' => 'mdi-store-outline',    'color' => 'warning'],
            'Tokopedia'   => ['icon' => 'mdi-shopping-outline', 'color' => 'success'],
            'OLX'         => ['icon' => 'mdi-tag-outline',      'color' => 'warning'],
            'Google'      => ['icon' => 'mdi-google',           'color' => 'danger'],
            'Google Ads'  => ['icon' => 'mdi-google',           'color' => 'danger'],
            'Meta Ads'    => ['icon' => 'mdi-facebook',         'color' => 'primary'],
            'Facebook'    => ['icon' => 'mdi-facebook',         'color' => 'primary'],
            'Other'       => ['icon' => 'mdi-dots-horizontal',  'color' => 'secondary'],
        ];
        $smktCategoryIcons = [
            'Service Compressor'   => ['icon' => 'mdi-wrench-outline',       'color' => 'info'],
            'Rental Compressor'    => ['icon' => 'mdi-car-wrench',           'color' => 'warning'],
            'Sparepart Compressor' => ['icon' => 'mdi-cog-outline',          'color' => 'secondary'],
            'Instalasi Piping'     => ['icon' => 'mdi-pipe',                 'color' => 'primary'],
            'Air Audit'            => ['icon' => 'mdi-clipboard-check-outline','color' => 'success'],
            'Fire System'          => ['icon' => 'mdi-fire',                 'color' => 'danger'],
            'HVAC System'          => ['icon' => 'mdi-air-conditioner',      'color' => 'info'],
            'Unit Baru/Second'     => ['icon' => 'mdi-package-variant',      'color' => 'primary'],
        ];
    ?>
    <div>
        <p class="fw-semibold text-muted mb-4" style="font-size:0.85rem">
            <i class="mdi mdi-information-outline me-1"></i>
            Marketing team contribution — Semester <?php echo e($report->semester); ?> · <?php echo e($report->year); ?> · Funnel: Prospect → Quotation → PO
        </p>

            
            <div class="row g-3 align-items-center justify-content-center">
                <div class="col-12 col-md-3">
                    <div class="card border shadow-none bg-label-secondary h-100 text-center">
                        <div class="card-body py-4">
                            <div class="avatar mx-auto mb-3">
                                <div class="avatar-initial bg-secondary rounded">
                                    <i class="mdi mdi-account-search-outline mdi-24px"></i>
                                </div>
                            </div>
                            <h2 class="fw-bold mb-1"><?php echo e($smktProspectCount); ?></h2>
                            <p class="mb-0 fw-semibold">Prospect</p>
                            <small class="text-muted">Submitted by marketing this semester</small>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-1 text-center d-flex flex-column align-items-center justify-content-center">
                    <i class="mdi mdi-arrow-right mdi-36px text-muted d-none d-md-block"></i>
                    <i class="mdi mdi-arrow-down mdi-36px text-muted d-block d-md-none"></i>
                    <small class="badge bg-label-primary mt-1"><?php echo e($smktProspectToQuote); ?>%</small>
                </div>
                <div class="col-12 col-md-3">
                    <div class="card border shadow-none bg-label-primary h-100 text-center">
                        <div class="card-body py-4">
                            <div class="avatar mx-auto mb-3">
                                <div class="avatar-initial bg-primary rounded">
                                    <i class="mdi mdi-file-document-outline mdi-24px"></i>
                                </div>
                            </div>
                            <h2 class="fw-bold mb-1"><?php echo e($smktQuoteCount); ?></h2>
                            <p class="mb-0 fw-semibold">Quotation</p>
                            <?php if($smktQuoteTotal > 0): ?>
                                <small class="text-muted">Rp <?php echo e(number_format($smktQuoteTotal, 0, ',', '.')); ?></small>
                            <?php else: ?>
                                <small class="text-muted">—</small>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-1 text-center d-flex flex-column align-items-center justify-content-center">
                    <i class="mdi mdi-arrow-right mdi-36px text-muted d-none d-md-block"></i>
                    <i class="mdi mdi-arrow-down mdi-36px text-muted d-block d-md-none"></i>
                    <small class="badge bg-label-success mt-1"><?php echo e($smktQuoteToPo); ?>%</small>
                </div>
                <div class="col-12 col-md-3">
                    <div class="card border shadow-none bg-label-success h-100 text-center">
                        <div class="card-body py-4">
                            <div class="avatar mx-auto mb-3">
                                <div class="avatar-initial bg-success rounded">
                                    <i class="mdi mdi-cart-check mdi-24px"></i>
                                </div>
                            </div>
                            <h2 class="fw-bold mb-1"><?php echo e($smktPoCount); ?></h2>
                            <p class="mb-0 fw-semibold">Purchase Order</p>
                            <?php if($smktPoTotal > 0): ?>
                                <small class="text-muted">Rp <?php echo e(number_format($smktPoTotal, 0, ',', '.')); ?></small>
                            <?php else: ?>
                                <small class="text-muted">—</small>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            
            <hr class="my-4">
            <p class="fw-semibold mb-3 text-heading">
                <i class="mdi mdi-clipboard-list-outline me-1"></i> Prospect Follow-up Status
            </p>
            <div class="row g-3 mb-2">
                <div class="col-12 col-md-4">
                    <div class="d-flex align-items-center gap-3 p-3 rounded border">
                        <div class="avatar flex-shrink-0">
                            <div class="avatar-initial bg-label-warning rounded">
                                <i class="mdi mdi-clock-outline mdi-24px"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between">
                                <span class="fw-semibold">Pending</span>
                                <span class="fw-bold text-warning"><?php echo e($smktStatusPending); ?></span>
                            </div>
                            <div class="progress mt-1" style="height:5px">
                                <div class="progress-bar bg-warning" style="width:<?php echo e($smktPctPending); ?>%"></div>
                            </div>
                            <small class="text-muted"><?php echo e($smktPctPending); ?>% not yet followed up</small>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="d-flex align-items-center gap-3 p-3 rounded border">
                        <div class="avatar flex-shrink-0">
                            <div class="avatar-initial bg-label-success rounded">
                                <i class="mdi mdi-check-circle-outline mdi-24px"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between">
                                <span class="fw-semibold">Provided</span>
                                <span class="fw-bold text-success"><?php echo e($smktStatusProvided); ?></span>
                            </div>
                            <div class="progress mt-1" style="height:5px">
                                <div class="progress-bar bg-success" style="width:<?php echo e($smktPctProvided); ?>%"></div>
                            </div>
                            <small class="text-muted"><?php echo e($smktPctProvided); ?>% forwarded to sales</small>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="d-flex align-items-center gap-3 p-3 rounded border">
                        <div class="avatar flex-shrink-0">
                            <div class="avatar-initial bg-label-danger rounded">
                                <i class="mdi mdi-close-circle-outline mdi-24px"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between">
                                <span class="fw-semibold">No Provide</span>
                                <span class="fw-bold text-danger"><?php echo e($smktStatusNoProvide); ?></span>
                            </div>
                            <div class="progress mt-1" style="height:5px">
                                <div class="progress-bar bg-danger" style="width:<?php echo e($smktPctNoProvide); ?>%"></div>
                            </div>
                            <small class="text-muted"><?php echo e($smktPctNoProvide); ?>% not continued</small>
                        </div>
                    </div>
                </div>
            </div>
            <?php if($smktLossCount > 0): ?>
                <div class="alert alert-danger d-flex align-items-center gap-2 py-2 mt-2 mb-0" role="alert">
                    <i class="mdi mdi-alert-outline"></i>
                    <span>
                        <strong><?php echo e($smktLossCount); ?> loss quotation(s)</strong> from marketing leads this semester
                        <?php if($smktLossTotal > 0): ?>
                            — worth <strong>Rp <?php echo e(number_format($smktLossTotal, 0, ',', '.')); ?></strong>
                        <?php endif; ?>
                    </span>
                </div>
            <?php endif; ?>

            
            <?php if($smktPerPerson->isNotEmpty()): ?>
                <hr class="my-4">
                <p class="fw-semibold mb-3 text-heading">
                    <i class="mdi mdi-account-group-outline me-1"></i> Per Marketing Person
                </p>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Marketing</th>
                                <th class="text-center">Total Prospect</th>
                                <th class="text-center">Provided</th>
                                <th class="text-center">Pending</th>
                                <th class="text-center">No Provide</th>
                                <th class="text-end pe-3">Provide Rate</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $smktPerPerson; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $smktProvideRate = $p->total > 0 ? round(($p->provided / $p->total) * 100, 1) : 0;
                                    $smktRateColor   = $smktProvideRate >= 70 ? 'success' : ($smktProvideRate >= 40 ? 'warning' : 'danger');
                                ?>
                                <tr>
                                    <td class="ps-3">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="avatar avatar-sm">
                                                <img src="<?php echo e(url('') . '/' . $p->image); ?>"
                                                     alt="<?php echo e($p->name); ?>"
                                                     class="rounded-circle"
                                                     style="width:36px;height:36px;object-fit:cover">
                                            </div>
                                            <span class="fw-semibold"><?php echo e($p->name); ?></span>
                                        </div>
                                    </td>
                                    <td class="text-center"><span class="badge bg-label-secondary rounded-pill"><?php echo e($p->total); ?></span></td>
                                    <td class="text-center"><span class="badge bg-label-success rounded-pill"><?php echo e($p->provided); ?></span></td>
                                    <td class="text-center"><span class="badge bg-label-warning rounded-pill"><?php echo e($p->pending); ?></span></td>
                                    <td class="text-center"><span class="badge bg-label-danger rounded-pill"><?php echo e($p->no_provide); ?></span></td>
                                    <td class="text-end pe-3">
                                        <div class="d-flex align-items-center justify-content-end gap-2">
                                            <div class="progress" style="width:60px;height:6px">
                                                <div class="progress-bar bg-<?php echo e($smktRateColor); ?>"
                                                     style="width:<?php echo e(min($smktProvideRate, 100)); ?>%"></div>
                                            </div>
                                            <span class="badge bg-label-<?php echo e($smktRateColor); ?> rounded-pill" style="min-width:48px">
                                                <?php echo e($smktProvideRate); ?>%
                                            </span>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>

            
            <?php if($smktProspectBySource->isNotEmpty() || $smktProspectByCategory->isNotEmpty()): ?>
                <hr class="my-4">
                <div class="row g-4">
                    
                    <?php if($smktProspectBySource->isNotEmpty()): ?>
                        <?php $smktMaxSource = $smktProspectBySource->max('total'); ?>
                        <div class="col-12 col-lg-6">
                            <div class="accordion" id="accordionSource">
                                <div class="accordion-item border-0">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed fw-semibold px-0" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#collapseSource">
                                            <i class="mdi mdi-antenna me-2"></i> Prospect Source
                                            <span class="badge bg-label-secondary ms-2"><?php echo e($smktProspectBySource->count()); ?></span>
                                        </button>
                                    </h2>
                                    <div id="collapseSource" class="accordion-collapse collapse">
                                        <div class="accordion-body px-0 pt-2">
                                            <div class="d-flex flex-column gap-3">
                                                <?php $__currentLoopData = $smktProspectBySource; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $src): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <?php
                                                        $si       = $smktSourceIcons[$src->source] ?? ['icon' => 'mdi-dots-horizontal', 'color' => 'secondary'];
                                                        $pct      = $smktMaxSource > 0 ? round(($src->total / $smktMaxSource) * 100) : 0;
                                                        $ofT      = $smktProspectCount > 0 ? round(($src->total / $smktProspectCount) * 100, 1) : 0;
                                                        $isWebDom = $src->source === 'Website' && $smktProspectByDomain->isNotEmpty();
                                                    ?>
                                                    <div>
                                                        <div class="d-flex align-items-center gap-3"
                                                             <?php if($isWebDom): ?>
                                                                 role="button" data-bs-toggle="collapse"
                                                                 data-bs-target="#collapseWebsiteDomainAdmin"
                                                                 aria-expanded="false" aria-controls="collapseWebsiteDomainAdmin"
                                                                 style="cursor:pointer"
                                                             <?php endif; ?>>
                                                            <div class="avatar avatar-sm flex-shrink-0">
                                                                <div class="avatar-initial bg-label-<?php echo e($si['color']); ?> rounded">
                                                                    <i class="mdi <?php echo e($si['icon']); ?>"></i>
                                                                </div>
                                                            </div>
                                                            <div class="flex-grow-1">
                                                                <div class="d-flex justify-content-between mb-1">
                                                                    <span class="fw-semibold" style="font-size:0.85rem">
                                                                        <?php echo e($src->source); ?>

                                                                        <?php if($isWebDom): ?>
                                                                            <i class="mdi mdi-chevron-down toggle-chevron text-muted" style="font-size:0.9rem"></i>
                                                                        <?php endif; ?>
                                                                    </span>
                                                                    <span class="text-muted" style="font-size:0.82rem"><?php echo e($src->total); ?> <small>(<?php echo e($ofT); ?>%)</small></span>
                                                                </div>
                                                                <div class="progress" style="height:6px">
                                                                    <div class="progress-bar bg-<?php echo e($si['color']); ?>" style="width:<?php echo e($pct); ?>%"></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <?php if($isWebDom): ?>
                                                            <?php
                                                                $smktMaxDomain   = $smktProspectByDomain->max('total');
                                                                $smktDomainTotal = $smktProspectByDomain->sum('total');
                                                            ?>
                                                            <div class="collapse" id="collapseWebsiteDomainAdmin">
                                                                <div class="d-flex flex-column gap-2 mt-2 ps-5">
                                                                    <?php $__currentLoopData = $smktProspectByDomain; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dom): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                        <?php
                                                                            $dPct = $smktMaxDomain > 0 ? round(($dom->total / $smktMaxDomain) * 100) : 0;
                                                                            $dOfT = $smktDomainTotal > 0 ? round(($dom->total / $smktDomainTotal) * 100, 1) : 0;
                                                                        ?>
                                                                        <div>
                                                                            <div class="d-flex justify-content-between mb-1">
                                                                                <span class="text-muted" style="font-size:0.76rem"><?php echo e($dom->domain); ?></span>
                                                                                <span class="text-muted" style="font-size:0.72rem"><?php echo e($dom->total); ?> <small>(<?php echo e($dOfT); ?>%)</small></span>
                                                                            </div>
                                                                            <div class="progress" style="height:4px">
                                                                                <div class="progress-bar bg-primary" style="width:<?php echo e($dPct); ?>%"></div>
                                                                            </div>
                                                                        </div>
                                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                                </div>
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    
                    <?php if($smktProspectByCategory->isNotEmpty()): ?>
                        <?php $smktMaxCategory = $smktProspectByCategory->max('total'); ?>
                        <div class="col-12 col-lg-6">
                            <div class="accordion" id="accordionCategory">
                                <div class="accordion-item border-0">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed fw-semibold px-0" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#collapseCategory">
                                            <i class="mdi mdi-tag-multiple-outline me-2"></i> Prospect Category
                                            <span class="badge bg-label-secondary ms-2"><?php echo e($smktProspectByCategory->count()); ?></span>
                                        </button>
                                    </h2>
                                    <div id="collapseCategory" class="accordion-collapse collapse">
                                        <div class="accordion-body px-0 pt-2">
                                            <div class="d-flex flex-column gap-3">
                                                <?php $__currentLoopData = $smktProspectByCategory; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <?php
                                                        $ci = $smktCategoryIcons[$cat->category] ?? ['icon' => 'mdi-shape-outline', 'color' => 'secondary'];
                                                        $pct = $smktMaxCategory > 0 ? round(($cat->total / $smktMaxCategory) * 100) : 0;
                                                        $ofT = $smktProspectCount > 0 ? round(($cat->total / $smktProspectCount) * 100, 1) : 0;
                                                    ?>
                                                    <div class="d-flex align-items-center gap-3">
                                                        <div class="avatar avatar-sm flex-shrink-0">
                                                            <div class="avatar-initial bg-label-<?php echo e($ci['color']); ?> rounded">
                                                                <i class="mdi <?php echo e($ci['icon']); ?>"></i>
                                                            </div>
                                                        </div>
                                                        <div class="flex-grow-1">
                                                            <div class="d-flex justify-content-between mb-1">
                                                                <span class="fw-semibold" style="font-size:0.85rem"><?php echo e($cat->category); ?></span>
                                                                <span class="text-muted" style="font-size:0.82rem"><?php echo e($cat->total); ?> <small>(<?php echo e($ofT); ?>%)</small></span>
                                                            </div>
                                                            <div class="progress" style="height:6px">
                                                                <div class="progress-bar bg-<?php echo e($ci['color']); ?>" style="width:<?php echo e($pct); ?>%"></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

        </div>

            </div>

        </div>
    </div>

    <?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sale): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php echo $__env->make('components.modal.overview.report', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('before-style'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/apex-charts/apex-charts.css" />
    <style>
        .active-tab-btn {
            color: #6d6b77;
            background: transparent;
            border: none;
        }
        .active-tab-btn.active,
        .active-tab-btn:focus {
            color: #696cff;
            background: #fff;
            box-shadow: 0 2px 8px rgba(105,108,255,0.2);
        }
        .active-tab-btn:hover:not(.active) {
            background: rgba(255,255,255,0.6);
            color: #696cff;
        }
        [data-bs-toggle="collapse"] .toggle-chevron { transition: transform .2s; }
        [data-bs-toggle="collapse"]:not(.collapsed) .toggle-chevron { transform: rotate(180deg); }
    </style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('page-script'); ?>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/apex-charts/apexcharts.js"></script>
    <script>
        (function () {
            const isDark       = document.documentElement.classList.contains('dark-style');
            const labelColor   = isDark ? '#a8aaae' : '#6d6b77';
            const borderColor  = isDark ? '#404152' : '#dbdade';
            const cardColor    = isDark ? '#2f3349' : '#fff';

            const formatRp = val => {
                if (val >= 1_000_000_000) return 'Rp ' + (val / 1_000_000_000).toFixed(1) + 'B';
                if (val >= 1_000_000)     return 'Rp ' + (val / 1_000_000).toFixed(1) + 'M';
                return 'Rp ' + val.toLocaleString('id-ID');
            };

            const salesSeries = <?php echo json_encode($chartSeries, 15, 512) ?>;
            const targetLine  = <?php echo json_encode($chartTargetLine, 15, 512) ?>;
            const labels      = <?php echo json_encode($chartLabels, 15, 512) ?>;

            // Warna khusus untuk tooltip (beda per sales)
            const tooltipColors = [
                '#696cff','#03c3ec','#71dd37','#ffab00','#ff3e1d',
                '#26c6da','#8c57ff','#20c997',
            ];

            const combinedData = <?php echo json_encode(array_values($combinedTotal), 15, 512) ?>;

            // 1 bar utuh per bulan (total semua sales) + target line
            const allSeries = [
                { name: 'Total Penjualan', type: 'bar', data: combinedData },
                { name: 'Monthly Target',  type: 'line', data: targetLine },
            ];

            const barPrimary  = '#696cff';
            const targetColor = '#ff4c51';
            const chartColors = [barPrimary, targetColor];

            const chartEl = document.querySelector('#chartPenjualanSemester');
            if (!chartEl) return;

            new ApexCharts(chartEl, {
                chart: {
                    type: 'bar',
                    height: 340,
                    toolbar: { show: false },
                    parentHeightOffset: 0,
                },
                series: allSeries,
                plotOptions: {
                    bar: { borderRadius: 4, columnWidth: '50%' },
                },
                dataLabels: { enabled: false },
                stroke: {
                    width: allSeries.map((s, i) => i === allSeries.length - 1 ? 2 : 0),
                    curve: 'smooth',
                    dashArray: allSeries.map((s, i) => i === allSeries.length - 1 ? 5 : 0),
                },
                markers: {
                    size: allSeries.map((s, i) => i === allSeries.length - 1 ? 4 : 0),
                    strokeWidth: 2,
                    colors: [cardColor],
                    strokeColors: targetColor,
                },
                colors: chartColors,
                dataLabels: { enabled: false },
                legend: {
                    show: true,
                    position: 'top',
                    labels: { colors: labelColor },
                },
                xaxis: {
                    categories: labels,
                    labels: { style: { colors: labelColor, fontSize: '13px' } },
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
                    padding: { top: 10, bottom: -5 },
                },
                tooltip: {
                    shared: true,
                    intersect: false,
                    custom: function({ series: apexSeries, dataPointIndex, w }) {
                        const month      = labels[dataPointIndex];
                        const grandTotal = combinedData[dataPointIndex] || 0;
                        const targetVal  = targetLine[dataPointIndex]   || 0;

                        // Breakdown per sales dari data original
                        let rows = '';
                        salesSeries.forEach((s, i) => {
                            const val = s.data[dataPointIndex] || 0;
                            if (val <= 0) return;
                            const color = tooltipColors[i] ?? '#696cff';
                            rows += `<div style="display:flex;align-items:center;gap:8px;padding:3px 0">
                                        <span style="width:10px;height:10px;border-radius:50%;background:${color};flex-shrink:0"></span>
                                        <span style="flex:1;color:#6d6b77;font-size:12px">${s.name}</span>
                                        <span style="font-size:12px">Rp ${val.toLocaleString('id-ID')}</span>
                                     </div>`;
                        });

                        const pct      = targetVal > 0 ? Math.round(grandTotal / targetVal * 100) : 0;
                        const pctColor = pct >= 100 ? '#71dd37' : pct >= 80 ? '#ffab00' : '#ff4c51';

                        const totalRow = `<div style="display:flex;align-items:center;gap:8px;padding:4px 0;border-top:1px solid #dbdade;margin-top:4px">
                                              <span style="width:10px;height:10px;border-radius:2px;background:#444;flex-shrink:0"></span>
                                              <span style="flex:1;font-weight:600;font-size:12px">Total Penjualan</span>
                                              <span style="font-weight:600;font-size:12px">Rp ${grandTotal.toLocaleString('id-ID')}</span>
                                              <span style="margin-left:6px;padding:1px 6px;border-radius:10px;background:${pctColor};color:#fff;font-size:11px;font-weight:700">${pct}%</span>
                                          </div>`;

                        const targetRow = `<div style="display:flex;align-items:center;gap:8px;padding:3px 0">
                                               <span style="width:10px;height:2px;background:${targetColor};flex-shrink:0;display:inline-block"></span>
                                               <span style="flex:1;color:#6d6b77;font-size:12px">Target Bulanan</span>
                                               <span style="font-size:12px">Rp ${targetVal.toLocaleString('id-ID')}</span>
                                           </div>`;

                        return `<div style="padding:10px 12px;min-width:230px;font-family:inherit">
                                    <div style="font-weight:600;border-bottom:1px solid #dbdade;padding-bottom:6px;margin-bottom:4px">${month}</div>
                                    ${rows}
                                    ${totalRow}
                                    ${targetRow}
                                </div>`;
                    },
                },
            }).render();
        })();
    </script>
    <script>
        document.querySelectorAll('.active-tab-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                const target = document.querySelector(this.dataset.bsTarget);
                if (!target) return;

                // hide all panes
                document.querySelectorAll('.tab-pane').forEach(p => {
                    p.classList.remove('show', 'active');
                });
                // deactivate all btns
                document.querySelectorAll('.active-tab-btn').forEach(b => {
                    b.classList.remove('active');
                });

                // activate clicked
                this.classList.add('active');
                target.classList.add('show', 'active');
            });
        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.sales.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u877155683/domains/reftech.my.id/reftech.my.id/resources/views/pages/admin/report.blade.php ENDPATH**/ ?>