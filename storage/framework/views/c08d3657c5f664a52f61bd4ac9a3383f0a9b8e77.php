
<?php $__env->startSection('title', 'Report'); ?>
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
        $winRate   = $quoteOnCount > 0 ? round(($poCount / $quoteOnCount) * 100, 1) : 0;
        $lossRate  = $quoteOnCount > 0 ? round(($lossCount / $quoteOnCount) * 100, 1) : 0;
        $winColor  = $winRate  >= 50 ? 'success' : ($winRate  >= 30 ? 'warning' : 'danger');
        $lossColor = $lossRate <= 20 ? 'success' : ($lossRate <= 40 ? 'warning' : 'danger');
    ?>

    
    <div class="d-flex align-items-start justify-content-between flex-wrap gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-heading">Report</h4>
            <span class="text-muted">
                <?php if($mode === 'monthly'): ?>
                    <?php echo e($bulanMap[$month]); ?> <?php echo e($year); ?>

                <?php else: ?>
                    Semester <?php echo e($semester); ?> (<?php echo e($semester == 1 ? 'January – June' : 'July – December'); ?>) &bull; <?php echo e($year); ?>

                <?php endif; ?>
                &bull; My Report
            </span>
        </div>

        <div class="d-flex align-items-center gap-2 flex-wrap">
            
            <div class="btn-group" role="group">
                <a href="<?php echo e(route('reports.support', ['year' => $year, 'month' => $month])); ?>"
                   class="btn btn-sm waves-effect <?php echo e($mode === 'monthly' ? 'btn-primary' : 'btn-outline-primary'); ?>">
                    Monthly
                </a>
                <a href="<?php echo e(route('reports.support', ['year' => $year, 'semester' => 1])); ?>"
                   class="btn btn-sm waves-effect <?php echo e($mode === 'semester' && $semester == 1 ? 'btn-primary' : 'btn-outline-primary'); ?>">
                    Semester 1
                </a>
                <a href="<?php echo e(route('reports.support', ['year' => $year, 'semester' => 2])); ?>"
                   class="btn btn-sm waves-effect <?php echo e($mode === 'semester' && $semester == 2 ? 'btn-primary' : 'btn-outline-primary'); ?>">
                    Semester 2
                </a>
            </div>

            <?php if($mode === 'monthly'): ?>
                
                <a href="<?php echo e(route('reports.support', ['year' => $prevYear, 'month' => $prevMonth])); ?>"
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
                                   href="<?php echo e(route('reports.support', ['year' => $year, 'month' => $m])); ?>">
                                    <?php echo e($bulanMap[$m]); ?>

                                </a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </div>
            <?php endif; ?>

            
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
                               href="<?php echo e($mode === 'monthly'
                                    ? route('reports.support', ['year' => $yr, 'month' => $month])
                                    : route('reports.support', ['year' => $yr, 'semester' => $semester])); ?>">
                                <?php echo e($yr); ?>

                            </a>
                        </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>

            <?php if($mode === 'monthly'): ?>
                
                <a href="<?php echo e(route('reports.support', ['year' => $nextYear, 'month' => $nextMonth])); ?>"
                   class="btn btn-sm btn-outline-secondary waves-effect">
                    <i class="mdi mdi-chevron-right"></i>
                </a>
            <?php endif; ?>
        </div>
    </div>

    
    <?php
        $cards = [
            ['label' => 'Purchase Order',   'icon' => 'mdi-cart-plus',      'color' => 'success',
             'amount' => 'Rp ' . number_format($poTotal, 0, ',', '.'),      'sub' => $poCount . ' transactions'],
            ['label' => 'Active Quotation','icon' => 'mdi-cart-outline',   'color' => 'primary',
             'amount' => 'Rp ' . number_format($quoteTotal, 0, ',', '.'),   'sub' => $quoteCount . ' quotations'],
            ['label' => 'Loss',            'icon' => 'mdi-cart-minus',     'color' => 'danger',
             'amount' => 'Rp ' . number_format($lossTotal, 0, ',', '.'),    'sub' => $lossCount . ' transactions'],
            ['label' => 'Win Rate',        'icon' => 'mdi-trophy-outline', 'color' => $winColor,
             'amount' => $winRate . '%',   'sub' => $poCount . ' PO of ' . $quoteOnCount . ' quotations'],
            ['label' => 'Loss Rate',       'icon' => 'mdi-trending-down',  'color' => $lossColor,
             'amount' => $lossRate . '%',  'sub' => $lossCount . ' loss of ' . $quoteOnCount . ' quotations'],
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

    
    <?php
        $prospectToQuote = $mktProspectCount > 0 ? round(($mktQuoteCount / $mktProspectCount) * 100, 1) : 0;
        $quoteToPoRate   = $mktQuoteCount   > 0 ? round(($mktPoCount   / $mktQuoteCount)   * 100, 1) : 0;
        $periodLabel = $mode === 'monthly'
            ? $bulanMap[$month] . ' ' . $year
            : 'Semester ' . $semester . ' &middot; ' . $year;
    ?>
    <div class="card mt-4">
        <div class="card-header">
            <h5 class="mb-0">Marketing Report</h5>
            <small class="text-muted">Marketing team contribution — <?php echo $periodLabel; ?> · Funnel: Prospect → Quotation → PO</small>
        </div>
        <div class="card-body">
            <div class="row g-3 align-items-center justify-content-center">

                
                <div class="col-12 col-md-3">
                    <div class="card border shadow-none bg-label-secondary h-100 text-center">
                        <div class="card-body py-4">
                            <div class="avatar mx-auto mb-3">
                                <div class="avatar-initial bg-secondary rounded">
                                    <i class="mdi mdi-account-search-outline mdi-24px"></i>
                                </div>
                            </div>
                            <h2 class="fw-bold mb-1"><?php echo e($mktProspectCount); ?></h2>
                            <p class="mb-0 fw-semibold">Prospect</p>
                            <small class="text-muted">Submitted by marketing this <?php echo e($mode === 'monthly' ? 'month' : 'semester'); ?></small>
                        </div>
                    </div>
                </div>

                
                <div class="col-12 col-md-1 text-center d-flex flex-column align-items-center justify-content-center">
                    <i class="mdi mdi-arrow-right mdi-36px text-muted d-none d-md-block"></i>
                    <i class="mdi mdi-arrow-down mdi-36px text-muted d-block d-md-none"></i>
                    <small class="badge bg-label-primary mt-1"><?php echo e($prospectToQuote); ?>%</small>
                </div>

                
                <div class="col-12 col-md-3">
                    <div class="card border shadow-none bg-label-primary h-100 text-center">
                        <div class="card-body py-4">
                            <div class="avatar mx-auto mb-3">
                                <div class="avatar-initial bg-primary rounded">
                                    <i class="mdi mdi-file-document-outline mdi-24px"></i>
                                </div>
                            </div>
                            <h2 class="fw-bold mb-1"><?php echo e($mktQuoteCount); ?></h2>
                            <p class="mb-0 fw-semibold">Quotation</p>
                            <?php if($mktQuoteTotal > 0): ?>
                                <small class="text-muted">Rp <?php echo e(number_format($mktQuoteTotal, 0, ',', '.')); ?></small>
                            <?php else: ?>
                                <small class="text-muted">—</small>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                
                <div class="col-12 col-md-1 text-center d-flex flex-column align-items-center justify-content-center">
                    <i class="mdi mdi-arrow-right mdi-36px text-muted d-none d-md-block"></i>
                    <i class="mdi mdi-arrow-down mdi-36px text-muted d-block d-md-none"></i>
                    <small class="badge bg-label-success mt-1"><?php echo e($quoteToPoRate); ?>%</small>
                </div>

                
                <div class="col-12 col-md-3">
                    <div class="card border shadow-none bg-label-success h-100 text-center">
                        <div class="card-body py-4">
                            <div class="avatar mx-auto mb-3">
                                <div class="avatar-initial bg-success rounded">
                                    <i class="mdi mdi-cart-check mdi-24px"></i>
                                </div>
                            </div>
                            <h2 class="fw-bold mb-1"><?php echo e($mktPoCount); ?></h2>
                            <p class="mb-0 fw-semibold">Purchase Order</p>
                            <?php if($mktPoTotal > 0): ?>
                                <small class="text-muted">Rp <?php echo e(number_format($mktPoTotal, 0, ',', '.')); ?></small>
                            <?php else: ?>
                                <small class="text-muted">—</small>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

            </div>

            
            <hr class="my-4">
            <?php
                $statusPending   = $mktProspectByStatus->pending   ?? 0;
                $statusProvided  = $mktProspectByStatus->provided  ?? 0;
                $statusNoProvide = $mktProspectByStatus->no_provide ?? 0;
                $pctPending      = $mktProspectCount > 0 ? round(($statusPending   / $mktProspectCount) * 100, 1) : 0;
                $pctProvided     = $mktProspectCount > 0 ? round(($statusProvided  / $mktProspectCount) * 100, 1) : 0;
                $pctNoProvide    = $mktProspectCount > 0 ? round(($statusNoProvide / $mktProspectCount) * 100, 1) : 0;
            ?>
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
                                <span class="fw-bold text-warning"><?php echo e($statusPending); ?></span>
                            </div>
                            <div class="progress mt-1" style="height:5px">
                                <div class="progress-bar bg-warning" style="width:<?php echo e($pctPending); ?>%"></div>
                            </div>
                            <small class="text-muted"><?php echo e($pctPending); ?>% not yet followed up</small>
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
                                <span class="fw-bold text-success"><?php echo e($statusProvided); ?></span>
                            </div>
                            <div class="progress mt-1" style="height:5px">
                                <div class="progress-bar bg-success" style="width:<?php echo e($pctProvided); ?>%"></div>
                            </div>
                            <small class="text-muted"><?php echo e($pctProvided); ?>% forwarded to sales</small>
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
                                <span class="fw-bold text-danger"><?php echo e($statusNoProvide); ?></span>
                            </div>
                            <div class="progress mt-1" style="height:5px">
                                <div class="progress-bar bg-danger" style="width:<?php echo e($pctNoProvide); ?>%"></div>
                            </div>
                            <small class="text-muted"><?php echo e($pctNoProvide); ?>% not continued</small>
                        </div>
                    </div>
                </div>
            </div>

            
            <?php if($mktLossCount > 0): ?>
                <div class="alert alert-danger d-flex align-items-center gap-2 py-2 mt-2 mb-0" role="alert">
                    <i class="mdi mdi-alert-outline"></i>
                    <span>
                        <strong><?php echo e($mktLossCount); ?> loss quotation(s)</strong> from marketing leads this <?php echo e($mode === 'monthly' ? 'month' : 'semester'); ?>

                        <?php if($mktLossTotal > 0): ?>
                            — worth <strong>Rp <?php echo e(number_format($mktLossTotal, 0, ',', '.')); ?></strong>
                        <?php endif; ?>
                    </span>
                </div>
            <?php endif; ?>

            
            <?php if($mktPerPerson->isNotEmpty()): ?>
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
                            <?php $__currentLoopData = $mktPerPerson; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $provideRate = $p->total > 0 ? round(($p->provided / $p->total) * 100, 1) : 0;
                                    $rateColor   = $provideRate >= 70 ? 'success' : ($provideRate >= 40 ? 'warning' : 'danger');
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
                                    <td class="text-center">
                                        <span class="badge bg-label-secondary rounded-pill"><?php echo e($p->total); ?></span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-label-success rounded-pill"><?php echo e($p->provided); ?></span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-label-warning rounded-pill"><?php echo e($p->pending); ?></span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-label-danger rounded-pill"><?php echo e($p->no_provide); ?></span>
                                    </td>
                                    <td class="text-end pe-3">
                                        <div class="d-flex align-items-center justify-content-end gap-2">
                                            <div class="progress" style="width:60px;height:6px">
                                                <div class="progress-bar bg-<?php echo e($rateColor); ?>"
                                                     style="width:<?php echo e(min($provideRate, 100)); ?>%"></div>
                                            </div>
                                            <span class="badge bg-label-<?php echo e($rateColor); ?> rounded-pill" style="min-width:48px">
                                                <?php echo e($provideRate); ?>%
                                            </span>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>

            <?php
                $sourceIcons = [
                    'IG'          => ['icon' => 'mdi-instagram',          'color' => 'danger'],
                    'Instagram'   => ['icon' => 'mdi-instagram',          'color' => 'danger'],
                    'WhatsApp'    => ['icon' => 'mdi-whatsapp',           'color' => 'success'],
                    'LinkedIn'    => ['icon' => 'mdi-linkedin',           'color' => 'info'],
                    'Website'     => ['icon' => 'mdi-web',                'color' => 'primary'],
                    'Indotrading' => ['icon' => 'mdi-store-outline',      'color' => 'warning'],
                    'Tokopedia'   => ['icon' => 'mdi-shopping-outline',   'color' => 'success'],
                    'OLX'         => ['icon' => 'mdi-tag-outline',        'color' => 'warning'],
                    'Google'      => ['icon' => 'mdi-google',             'color' => 'danger'],
                    'Google Ads'  => ['icon' => 'mdi-google',             'color' => 'danger'],
                    'Meta Ads'    => ['icon' => 'mdi-facebook',           'color' => 'primary'],
                    'Facebook'    => ['icon' => 'mdi-facebook',           'color' => 'primary'],
                    'Other'       => ['icon' => 'mdi-help-circle-outline','color' => 'secondary'],
                ];
                $categoryIcons = [
                    'Service Compressor'   => ['icon' => 'mdi-wrench-outline',         'color' => 'primary'],
                    'Rental Compressor'    => ['icon' => 'mdi-calendar-clock-outline', 'color' => 'info'],
                    'Sparepart Compressor' => ['icon' => 'mdi-cog-outline',            'color' => 'warning'],
                    'Instalasi Piping'     => ['icon' => 'mdi-pipe',                   'color' => 'secondary'],
                    'Air Audit'            => ['icon' => 'mdi-clipboard-check-outline','color' => 'success'],
                    'Fire System'          => ['icon' => 'mdi-fire-extinguisher',      'color' => 'danger'],
                    'HVAC System'          => ['icon' => 'mdi-air-conditioner',        'color' => 'info'],
                    'Unit Baru/Second'     => ['icon' => 'mdi-package-variant-closed', 'color' => 'success'],
                    'Uncategorized'        => ['icon' => 'mdi-help-circle-outline',    'color' => 'secondary'],
                ];
            ?>

            
            <?php if($mktProspectBySource->isNotEmpty() || $mktProspectByCategory->isNotEmpty() || $mktProspectByArea->isNotEmpty()): ?>
                <hr class="my-4">
                <div class="row g-4">

                    
                    <?php if($mktProspectBySource->isNotEmpty()): ?>
                        <div class="col-12 col-lg-4">
                            <p class="fw-semibold mb-3 text-heading">
                                <i class="mdi mdi-source-branch me-1"></i> Prospect Source
                            </p>
                            <?php $maxSrc = $mktProspectBySource->max('total'); ?>
                            <div class="d-flex flex-column gap-3">
                                <?php $__currentLoopData = $mktProspectBySource; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $src): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php
                                        $s        = $sourceIcons[$src->source] ?? $sourceIcons['Other'];
                                        $pct      = $maxSrc > 0 ? round(($src->total / $maxSrc) * 100) : 0;
                                        $ofTotal  = $mktProspectCount > 0 ? round(($src->total / $mktProspectCount) * 100, 1) : 0;
                                        $isWebDom = $src->source === 'Website' && $mktProspectByDomain->isNotEmpty();
                                    ?>
                                    <div>
                                        <div class="d-flex align-items-center gap-3"
                                             <?php if($isWebDom): ?>
                                                 role="button" data-bs-toggle="collapse"
                                                 data-bs-target="#collapseWebsiteDomainSupport"
                                                 aria-expanded="false" aria-controls="collapseWebsiteDomainSupport"
                                                 style="cursor:pointer"
                                             <?php endif; ?>>
                                            <div class="avatar avatar-sm flex-shrink-0">
                                                <div class="avatar-initial bg-label-<?php echo e($s['color']); ?> rounded">
                                                    <i class="mdi <?php echo e($s['icon']); ?>"></i>
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
                                                    <span class="text-muted" style="font-size:0.82rem">
                                                        <?php echo e($src->total); ?> <small>(<?php echo e($ofTotal); ?>%)</small>
                                                    </span>
                                                </div>
                                                <div class="progress" style="height:6px">
                                                    <div class="progress-bar bg-<?php echo e($s['color']); ?>" style="width:<?php echo e($pct); ?>%"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php if($isWebDom): ?>
                                            <?php
                                                $maxDomain   = $mktProspectByDomain->max('total');
                                                $domainTotal = $mktProspectByDomain->sum('total');
                                            ?>
                                            <div class="collapse" id="collapseWebsiteDomainSupport">
                                                <div class="d-flex flex-column gap-2 mt-2 ps-5">
                                                    <?php $__currentLoopData = $mktProspectByDomain; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dom): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <?php
                                                            $dPct = $maxDomain > 0 ? round(($dom->total / $maxDomain) * 100) : 0;
                                                            $dOfT = $domainTotal > 0 ? round(($dom->total / $domainTotal) * 100, 1) : 0;
                                                        ?>
                                                        <div>
                                                            <div class="d-flex justify-content-between mb-1">
                                                                <span class="text-muted" style="font-size:0.76rem"><?php echo e($dom->domain); ?></span>
                                                                <span class="text-muted" style="font-size:0.72rem">
                                                                    <?php echo e($dom->total); ?> <small>(<?php echo e($dOfT); ?>%)</small>
                                                                </span>
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
                    <?php endif; ?>

                    
                    <?php if($mktProspectByCategory->isNotEmpty()): ?>
                        <div class="col-12 col-lg-4">
                            <p class="fw-semibold mb-3 text-heading">
                                <i class="mdi mdi-tag-multiple-outline me-1"></i> Prospect Category
                            </p>
                            <?php $maxCat = $mktProspectByCategory->max('total'); ?>
                            <div class="d-flex flex-column gap-3">
                                <?php $__currentLoopData = $mktProspectByCategory; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php
                                        $c       = $categoryIcons[$cat->category] ?? $categoryIcons['Uncategorized'];
                                        $pct     = $maxCat > 0 ? round(($cat->total / $maxCat) * 100) : 0;
                                        $ofTotal = $mktProspectCount > 0 ? round(($cat->total / $mktProspectCount) * 100, 1) : 0;
                                    ?>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="avatar avatar-sm flex-shrink-0">
                                            <div class="avatar-initial bg-label-<?php echo e($c['color']); ?> rounded">
                                                <i class="mdi <?php echo e($c['icon']); ?>"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="d-flex justify-content-between mb-1">
                                                <span class="fw-semibold" style="font-size:0.85rem"><?php echo e($cat->category); ?></span>
                                                <span class="text-muted" style="font-size:0.82rem">
                                                    <?php echo e($cat->total); ?> <small>(<?php echo e($ofTotal); ?>%)</small>
                                                </span>
                                            </div>
                                            <div class="progress" style="height:6px">
                                                <div class="progress-bar bg-<?php echo e($c['color']); ?>" style="width:<?php echo e($pct); ?>%"></div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    
                    <?php if($mktProspectByArea->isNotEmpty()): ?>
                        <div class="col-12 col-lg-4">
                            <p class="fw-semibold mb-3 text-heading">
                                <i class="mdi mdi-map-marker-outline me-1"></i> Prospect Area
                                <small class="text-muted fw-normal">(<?php echo e($mktProspectByArea->count()); ?> areas)</small>
                            </p>
                            <?php $maxArea = $mktProspectByArea->max('total'); ?>
                            <div class="d-flex flex-column gap-3" id="area-list">
                                <?php $__currentLoopData = $mktProspectByArea; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $ar): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php
                                        $pct     = $maxArea > 0 ? round(($ar->total / $maxArea) * 100) : 0;
                                        $ofTotal = $mktProspectCount > 0 ? round(($ar->total / $mktProspectCount) * 100, 1) : 0;
                                    ?>
                                    <div class="d-flex align-items-center gap-3 area-item <?php echo e($i >= 10 ? 'd-none' : ''); ?>"
                                         data-index="<?php echo e($i); ?>">
                                        <div class="avatar avatar-sm flex-shrink-0">
                                            <div class="avatar-initial bg-label-primary rounded">
                                                <i class="mdi mdi-map-marker-outline"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="d-flex justify-content-between mb-1">
                                                <span class="fw-semibold" style="font-size:0.85rem"><?php echo e($ar->area); ?></span>
                                                <span class="text-muted" style="font-size:0.82rem">
                                                    <?php echo e($ar->total); ?> <small>(<?php echo e($ofTotal); ?>%)</small>
                                                </span>
                                            </div>
                                            <div class="progress" style="height:6px">
                                                <div class="progress-bar bg-primary" style="width:<?php echo e($pct); ?>%"></div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                            <?php if($mktProspectByArea->count() > 10): ?>
                                <button type="button" id="btn-load-more-area"
                                    class="btn btn-sm btn-outline-primary waves-effect mt-3 w-100">
                                    <i class="mdi mdi-chevron-down me-1"></i>
                                    Show <?php echo e($mktProspectByArea->count() - 10); ?> more areas
                                </button>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                </div>
            <?php endif; ?>

        </div>
    </div>
<?php $__env->startPush('before-style'); ?>
<style>
    [data-bs-toggle="collapse"] .toggle-chevron { transition: transform .2s; }
    [data-bs-toggle="collapse"]:not(.collapsed) .toggle-chevron { transform: rotate(180deg); }
</style>
<?php $__env->stopPush(); ?>
<?php $__env->startPush('after-script'); ?>
<script>
    document.getElementById('btn-load-more-area')?.addEventListener('click', function () {
        document.querySelectorAll('.area-item.d-none').forEach(el => el.classList.remove('d-none'));
        this.remove();
    });
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.sales.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u877155683/domains/reftech.my.id/reftech.my.id/resources/views/pages/support/report/index.blade.php ENDPATH**/ ?>