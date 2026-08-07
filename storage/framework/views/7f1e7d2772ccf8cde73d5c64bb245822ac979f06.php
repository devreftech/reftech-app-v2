    <style>
        .nav-tabs.nav-tabs-widget .nav-link {
            height: auto !important;
            min-height: 92px;
            padding-top: .5rem !important;
            padding-bottom: .5rem !important;
        }
    </style>
    <div class="row gy-4 mb-4">
        <div class="col-12 col-lg-4">

            <?php
                $pctAdmin     = $targetAllSales > 0 ? round(($poTotalPriceAdmin / $targetAllSales) * 100, 1) : 0;
                $pctAdmColor  = $pctAdmin >= 100 ? 'success' : ($pctAdmin >= 80 ? 'warning' : 'danger');
                $pctAdmBar    = min($pctAdmin, 100);
                $today        = \Carbon\Carbon::now();
                $semesterNow  = \App\Models\SalesReports::where('semester', $today->month > 6 ? 2 : 1)
                                    ->where('year', $today->year)->first();
            ?>
            <div class="card clean-card mb-3">
                <div class="card-body" style="padding-right: 10rem;">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge bg-label-primary">
                            <i class="mdi mdi-chart-line"></i> Monthly
                        </span>
                        <small class="text-muted"><?php echo e($today->locale('id')->translatedFormat('F Y')); ?></small>
                    </div>
                    <h5 class="card-title mb-1">Sales Performance</h5>
                    <h3 class="text-primary fw-bold mb-0">Rp. <?php echo e($formattedTotalPriceAdmin); ?></h3>
                    <small class="text-muted">Target: Rp. <?php echo e(number_format($targetAllSales, 0, ',', '.')); ?></small>

                    <div class="my-2">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <small class="text-muted fw-semibold">Pencapaian Target</small>
                            <small class="fw-bold text-<?php echo e($pctAdmColor); ?>"><?php echo e($pctAdmin); ?>%</small>
                        </div>
                        <div class="progress" style="height:6px;border-radius:4px;">
                            <div class="progress-bar bg-<?php echo e($pctAdmColor); ?>"
                                 style="width:<?php echo e($pctAdmBar); ?>%;border-radius:4px;"></div>
                        </div>
                    </div>

                    <?php if($semesterNow): ?>
                        <a href="<?php echo e(route('report.semester', $semesterNow)); ?>"
                           class="btn btn-sm btn-primary waves-effect waves-light mt-1">
                            <i class="mdi mdi-chart-areaspline me-1"></i> View Sales
                        </a>
                    <?php endif; ?>
                </div>
                <img src="<?php echo e(asset('assets')); ?>/img/illustrations/trophy.png"
                    class="position-absolute bottom-0 end-0 me-3" height="140" alt="view sales">
            </div>
            <div class="card clean-card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title m-0 me-2">Rank Sales Team 🏆</h5>
                </div>
                <div class="card-body">
                    <ul class="p-0 m-0"><hr>
                        <?php
                            $no = 1;
                        ?>
                        <?php $__currentLoopData = $sorted; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sale): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                switch ($no) {
                                    case 1:
                                        $color = 'warning'; // Kuning / Orange
                                        break;
                                    case 2:
                                        $color = 'success'; // Hijau
                                        break;
                                    case 3:
                                        $color = 'info'; // Biru
                                        break;
                                    case 4:
                                        $color = 'secondary'; // Abu-abu
                                        break;
                                    case 5:
                                        $color = 'primary'; // custom (kalau ada)
                                        break;
                                    case 6:
                                        $color = 'danger'; // Merah
                                        break;
                                    case 7:
                                        $color = 'dark'; // Hitam
                                        break;
                                    default:
                                        $color = 'primary';
                                        break;
                                }
                            ?>
                            <li class="d-flex align-items-start mb-3" style="list-style:none;">
                                <span class="badge bg-label-<?php echo e($color); ?> d-inline-flex align-items-center justify-content-center flex-shrink-0"
                                      style="min-width:36px;font-size:13px;">
                                    #<?php echo e($no); ?>

                                </span>
                                <div class="ms-2 w-100">
                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                        <div>
                                            <span class="fw-semibold" style="font-size:0.875rem;">
                                                <?php echo e($sale['name']); ?>

                                                <?php if($no == 1): ?>
                                                    <i class="mdi mdi-crown text-warning ms-1"></i>
                                                <?php endif; ?>
                                            </span>
                                            <small class="text-muted d-block" style="font-size:0.7rem;"><?php echo e($sale['area']); ?></small>
                                        </div>
                                        <span class="badge bg-label-<?php echo e($color); ?> rounded-pill" style="font-size:12px;">
                                            <?php echo e($sale['percentage']); ?>%
                                        </span>
                                    </div>
                                    <div class="progress" style="height:4px;border-radius:4px;">
                                        <div class="progress-bar bg-<?php echo e($color); ?>"
                                             style="width:<?php echo e(min($sale['percentage'], 100)); ?>%;border-radius:4px;"></div>
                                    </div>
                                </div>
                            </li>
                            <?php
                                $no++;
                            ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-8">
            <div class="card clean-card h-100">
                <div class="card-header d-flex justify-content-between">
                    <div class="card-title m-0">
                        <h5 class="mb-0">Sales Overview</h5>
                    </div>
                </div>
                <div class="card-body pb-3">
                    <ul class="nav nav-tabs nav-tabs-widget pb-3 gap-3 d-flex flex-nowrap overflow-auto" role="tablist" style="scrollbar-width: thin;">
                        <?php $__currentLoopData = $sales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if($user->id == 23): ?> <?php continue; ?> <?php endif; ?>
                            <?php
                                $isActive = $user->id == ($firstSales->id ?? 1);
                                $displayName = $user->id == 16 ? 'Team E-Commerce' : Str::words($user->name, 1, '');
                                $displayArea = $user->id == 16 ? 'Online' : ($user->latestRole->area ?? 'Sales');
                            ?>
                            <li class="nav-item change-sales text-center flex-shrink-0" role="presentation" data-id="<?php echo e($user->id); ?>">
                                <a class="nav-link btn <?php echo e($isActive ? 'active' : ''); ?> d-flex flex-column align-items-center justify-content-center p-2 rounded-3"
                                   role="tab" data-bs-toggle="tab" data-bs-target="#navs-sales-<?php echo e($user->id); ?>"
                                   aria-controls="navs-sales-<?php echo e($user->id); ?>" aria-selected="<?php echo e($isActive ? 'true' : 'false'); ?>"
                                   style="transition: all 0.2s ease;">
                                    <div class="position-relative mb-1">
                                        <img src="<?php echo e(url('') . '/' . $user->image); ?>" alt="<?php echo e($displayName); ?>"
                                            class="rounded-circle border"
                                            style="width: 48px; height: 48px; object-fit: cover; border-width: 2px !important;">
                                    </div>
                                    <span class="fw-semibold text-dark text-truncate d-block" style="max-width: 85px; font-size: 0.78rem; line-height: 1.2;">
                                        <?php echo e($displayName); ?>

                                    </span>
                                    <small class="text-muted text-truncate d-block" style="max-width: 85px; font-size: 0.7rem; line-height: 1.2;">
                                        <?php echo e($displayArea); ?>

                                    </small>
                                </a>
                            </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                    <div class="tab-content p-0 ms-0 ms-sm-2">
                        <?php
                            $item = 0;
                        ?>
                        <?php $__currentLoopData = $sales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if($user->id == 23): ?> <?php continue; ?> <?php endif; ?>
                            <?php
                                $titleName = $user->id == 16 ? 'Team E-Commerce' : $user->name;
                            ?>
                            <div class="tab-pane fade<?php echo e($user->id == ($firstSales->id ?? 1) ? ' show active' : ''); ?>"
                                id="navs-sales-<?php echo e($user->id); ?>" role="tabpanel">
                                <div class="mb-3">
                                    <div data-id="<?php echo e($item); ?>">
                                        <!-- Header Tab Pane -->
                                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 pb-2 pt-2 border-bottom mb-3">
                                            <div class="d-flex align-items-center gap-3">
                                                <img src="<?php echo e(url('') . '/' . $user->image); ?>" alt="<?php echo e($titleName); ?>"
                                                    class="rounded-circle border" style="width: 44px; height: 44px; object-fit: cover; border-width: 2px !important;">
                                                <div>
                                                    <h5 class="mb-0 fw-bold text-dark"><?php echo e($titleName); ?>'s Performance</h5>
                                                    <small class="text-muted d-block"><?php echo e($user->id == 16 ? 'Online Division' : ($user->latestRole->area ?? 'Sales Area')); ?></small>
                                                </div>
                                            </div>
                                            <div>
                                                <span class="badge bg-label-primary px-3 py-2 rounded-pill">
                                                    <i class="mdi mdi-account-check-outline me-1"></i> <?php echo e($user->id == 16 ? 'Team E-Commerce' : 'Active Sales'); ?>

                                                </span>
                                            </div>
                                        </div>

                                        <?php if($user->role == 'Sales'): ?>
                                            <div class="row g-3">
                                                <?php if($user->id == 16 || $user->id == 23): ?>
                                                    <!-- Panel Kiri: E-Commerce Ops Tiles (Grid 2 Kolom) -->
                                                    <div class="col-12 col-md-7">
                                                        <div class="row g-2">
                                                            <!-- Upload Product -->
                                                            <div class="col-6">
                                                                <div class="p-2 border rounded-3 bg-body-tertiary">
                                                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                                                        <div class="d-flex align-items-center gap-1">
                                                                            <span class="badge bg-label-info p-1 rounded"><i class="mdi mdi-reproduction"></i></span>
                                                                            <span class="fw-semibold text-dark" style="font-size: 0.78rem;">Upload Product</span>
                                                                        </div>
                                                                        <span class="badge bg-label-info rounded-pill filtered-percent-product" style="font-size: 9px;">0%</span>
                                                                    </div>
                                                                    <div class="d-flex align-items-baseline gap-1 mt-1">
                                                                        <h6 class="mb-0 fw-bold text-dark filtered-product">0</h6>
                                                                        <small class="text-muted filtered-target-product" style="font-size: 0.7rem;">/ 100</small>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <!-- Upload SW -->
                                                            <div class="col-6">
                                                                <div class="p-2 border rounded-3 bg-body-tertiary">
                                                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                                                        <div class="d-flex align-items-center gap-1">
                                                                            <span class="badge bg-label-success p-1 rounded"><i class="mdi mdi-whatsapp"></i></span>
                                                                            <span class="fw-semibold text-dark" style="font-size: 0.78rem;">Upload SW</span>
                                                                        </div>
                                                                        <span class="badge bg-label-success rounded-pill filtered-percent-sw" style="font-size: 9px;">0%</span>
                                                                    </div>
                                                                    <div class="d-flex align-items-baseline gap-1 mt-1">
                                                                        <h6 class="mb-0 fw-bold text-dark filtered-sw">0</h6>
                                                                        <small class="text-muted filtered-target-sw" style="font-size: 0.7rem;">/ <?php echo e($user->id == 16 ? '120' : '60'); ?></small>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <!-- Upload Video -->
                                                            <div class="col-6">
                                                                <div class="p-2 border rounded-3 bg-body-tertiary">
                                                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                                                        <div class="d-flex align-items-center gap-1">
                                                                            <span class="badge bg-label-secondary p-1 rounded"><i class="mdi mdi-video-outline"></i></span>
                                                                            <span class="fw-semibold text-dark" style="font-size: 0.78rem;">Upload Video</span>
                                                                        </div>
                                                                        <span class="badge bg-label-secondary rounded-pill filtered-percent-video" style="font-size: 9px;">0%</span>
                                                                    </div>
                                                                    <div class="d-flex align-items-baseline gap-1 mt-1">
                                                                        <h6 class="mb-0 fw-bold text-dark filtered-video">0</h6>
                                                                        <small class="text-muted filtered-target-video" style="font-size: 0.7rem;">/ 100%</small>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <!-- CRM -->
                                                            <div class="col-6">
                                                                <div class="p-2 border rounded-3 bg-body-tertiary">
                                                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                                                        <div class="d-flex align-items-center gap-1">
                                                                            <span class="badge bg-label-primary p-1 rounded"><i class="mdi mdi-account-multiple-outline"></i></span>
                                                                            <span class="fw-semibold text-dark" style="font-size: 0.78rem;">CRM</span>
                                                                        </div>
                                                                        <span class="badge bg-label-primary rounded-pill filtered-percent-crm" style="font-size: 9px;">0%</span>
                                                                    </div>
                                                                    <div class="d-flex align-items-baseline gap-1 mt-1">
                                                                        <h6 class="mb-0 fw-bold text-dark filtered-crm"><?php echo e($user->id == ($firstSales->id ?? 1) ? $filteredCRM : 0); ?></h6>
                                                                        <small class="text-muted filtered-target-crm" style="font-size: 0.7rem;">/ <?php echo e($targetCrm[$user->id] ?? 0); ?></small>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <!-- Status Product -->
                                                            <div class="col-6">
                                                                <div class="p-2 border rounded-3 bg-body-tertiary">
                                                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                                                        <div class="d-flex align-items-center gap-1">
                                                                            <span class="badge bg-label-warning p-1 rounded"><i class="mdi mdi-package-variant-closed-check"></i></span>
                                                                            <span class="fw-semibold text-dark" style="font-size: 0.78rem;">Status Product</span>
                                                                        </div>
                                                                        <span class="badge bg-label-warning rounded-pill filtered-percent-status" style="font-size: 9px;">0%</span>
                                                                    </div>
                                                                    <div class="d-flex align-items-baseline gap-1 mt-1">
                                                                        <h6 class="mb-0 fw-bold text-dark filtered-status">0</h6>
                                                                        <small class="text-muted filtered-target-status" style="font-size: 0.7rem;">/ 5.0</small>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <!-- Delivery Status -->
                                                            <div class="col-6">
                                                                <div class="p-2 border rounded-3 bg-body-tertiary">
                                                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                                                        <div class="d-flex align-items-center gap-1">
                                                                            <span class="badge bg-label-info p-1 rounded"><i class="mdi mdi-truck-delivery-outline"></i></span>
                                                                            <span class="fw-semibold text-dark" style="font-size: 0.78rem;">Delivery Status</span>
                                                                        </div>
                                                                        <span class="badge bg-label-info rounded-pill filtered-percent-delivery" style="font-size: 9px;">0%</span>
                                                                    </div>
                                                                    <div class="d-flex align-items-baseline gap-1 mt-1">
                                                                        <h6 class="mb-0 fw-bold text-dark filtered-delivery">0</h6>
                                                                        <small class="text-muted filtered-target-delivery" style="font-size: 0.7rem;">/ 5.0</small>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <!-- Customer Care -->
                                                            <div class="col-6">
                                                                <div class="p-2 border rounded-3 bg-body-tertiary">
                                                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                                                        <div class="d-flex align-items-center gap-1">
                                                                            <span class="badge bg-label-dark p-1 rounded"><i class="mdi mdi-cart-check"></i></span>
                                                                            <span class="fw-semibold text-dark" style="font-size: 0.78rem;">Customer Care</span>
                                                                        </div>
                                                                        <span class="badge bg-label-dark rounded-pill filtered-percent-customer" style="font-size: 9px;">0%</span>
                                                                    </div>
                                                                    <div class="d-flex align-items-baseline gap-1 mt-1">
                                                                        <h6 class="mb-0 fw-bold text-dark filtered-customer">0</h6>
                                                                        <small class="text-muted filtered-target-customer" style="font-size: 0.7rem;">/ 5.0</small>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <!-- Chat Response -->
                                                            <div class="col-6">
                                                                <div class="p-2 border rounded-3 bg-body-tertiary">
                                                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                                                        <div class="d-flex align-items-center gap-1">
                                                                            <span class="badge bg-label-danger p-1 rounded"><i class="mdi mdi-account-heart-outline"></i></span>
                                                                            <span class="fw-semibold text-dark" style="font-size: 0.78rem;">Chat Response</span>
                                                                        </div>
                                                                        <span class="badge bg-label-danger rounded-pill filtered-percent-response" style="font-size: 9px;">0%</span>
                                                                    </div>
                                                                    <div class="d-flex align-items-baseline gap-1 mt-1">
                                                                        <h6 class="mb-0 fw-bold text-dark filtered-response">0</h6>
                                                                        <small class="text-muted filtered-target-response" style="font-size: 0.7rem;">/ 100%</small>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <!-- Store Rating -->
                                                            <div class="col-6">
                                                                <div class="p-2 border rounded-3 bg-body-tertiary">
                                                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                                                        <div class="d-flex align-items-center gap-1">
                                                                            <span class="badge bg-label-warning p-1 rounded"><i class="mdi mdi-monitor-star"></i></span>
                                                                            <span class="fw-semibold text-dark" style="font-size: 0.78rem;">Store Rating</span>
                                                                        </div>
                                                                        <span class="badge bg-label-warning rounded-pill filtered-percent-rating" style="font-size: 9px;">0%</span>
                                                                    </div>
                                                                    <div class="d-flex align-items-baseline gap-1 mt-1">
                                                                        <h6 class="mb-0 fw-bold text-dark filtered-rating">0</h6>
                                                                        <small class="text-muted filtered-target-rating" style="font-size: 0.7rem;">/ 5.0</small>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php else: ?>
                                                    <!-- Panel Kiri: Regular Sales Operational Tiles (Grid 2 Kolom) -->
                                                    <div class="col-12 col-md-7">
                                                        <div class="row g-2">
                                                            <?php if($user->id == 1 || $user->id == 2 || $user->id == 32): ?>
                                                                <?php
                                                                    $salesTargetLeads = ($targetSales[$item][0] ?? null)?->leads ?? 0;
                                                                    $currentLeads = $user->id == ($firstSales->id ?? 1) ? $filteredLeads : 0;
                                                                    $targetLeads = $salesTargetLeads > 0 ? ($currentLeads / $salesTargetLeads) * 100 : 0;
                                                                ?>
                                                                <!-- New Leads -->
                                                                <div class="col-6">
                                                                    <div class="p-2 border rounded-3 bg-body-tertiary">
                                                                        <div class="d-flex align-items-center justify-content-between mb-1">
                                                                            <div class="d-flex align-items-center gap-1">
                                                                                <span class="badge bg-label-secondary p-1 rounded"><i class="mdi mdi-account-multiple-plus-outline"></i></span>
                                                                                <span class="fw-semibold text-dark" style="font-size: 0.78rem;">New Leads</span>
                                                                            </div>
                                                                            <span class="badge bg-label-secondary rounded-pill filtered-percent-leads" style="font-size: 9px;"><?php echo e(round($targetLeads)); ?>%</span>
                                                                        </div>
                                                                        <div class="d-flex align-items-baseline gap-1 mt-1">
                                                                            <h6 class="mb-0 fw-bold text-dark filtered-leads"><?php echo e($user->id == ($firstSales->id ?? 1) ? $filteredLeads : 0); ?></h6>
                                                                            <small class="text-muted filtered-target-leads" style="font-size: 0.7rem;">/ <?php echo e($salesTargetLeads); ?></small>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <!-- Daily Call -->
                                                                <div class="col-6">
                                                                    <div class="p-2 border rounded-3 bg-body-tertiary">
                                                                        <div class="d-flex align-items-center justify-content-between mb-1">
                                                                            <div class="d-flex align-items-center gap-1">
                                                                                <span class="badge bg-label-info p-1 rounded"><i class="mdi mdi-phone-outline"></i></span>
                                                                                <span class="fw-semibold text-dark" style="font-size: 0.78rem;">Daily Call</span>
                                                                            </div>
                                                                        </div>
                                                                        <div class="d-flex align-items-baseline gap-1 mt-1">
                                                                            <h6 class="mb-0 fw-bold text-dark filtered-dc"><?php echo e($user->id == ($firstSales->id ?? 1) ? $filteredDC : 0); ?></h6>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            <?php endif; ?>

                                                            <!-- CRM Tile -->
                                                            <?php
                                                                $crmDenominator = $targetCrm[$user->id] ?? 0;
                                                                $currentCRM = $user->id == ($firstSales->id ?? 1) ? $filteredCRM : 0;
                                                                $targetCRM = $crmDenominator > 0 ? ($currentCRM / $crmDenominator) * 100 : 0;
                                                            ?>
                                                            <div class="col-6">
                                                                <div class="p-2 border rounded-3 bg-body-tertiary">
                                                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                                                        <div class="d-flex align-items-center gap-1">
                                                                            <span class="badge bg-label-primary p-1 rounded"><i class="mdi mdi-account-multiple-outline"></i></span>
                                                                            <span class="fw-semibold text-dark" style="font-size: 0.78rem;">CRM</span>
                                                                        </div>
                                                                        <?php if($user->id != 3): ?>
                                                                            <span class="badge bg-label-primary rounded-pill filtered-percent-crm" style="font-size: 9px;"><?php echo e(round($targetCRM)); ?>%</span>
                                                                        <?php endif; ?>
                                                                    </div>
                                                                    <div class="d-flex align-items-baseline gap-1 mt-1">
                                                                        <h6 class="mb-0 fw-bold text-dark filtered-crm"><?php echo e($user->id == ($firstSales->id ?? 1) ? $filteredCRM : 0); ?></h6>
                                                                        <small class="text-muted filtered-target-crm" style="font-size: 0.7rem;">/ <?php echo e($crmDenominator); ?></small>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <!-- Quotation Tile -->
                                                            <div class="col-6">
                                                                <div class="p-2 border rounded-3 bg-body-tertiary">
                                                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                                                        <div class="d-flex align-items-center gap-1">
                                                                            <span class="badge bg-label-warning p-1 rounded"><i class="mdi mdi-email-multiple-outline"></i></span>
                                                                            <span class="fw-semibold text-dark" style="font-size: 0.78rem;">Quotation</span>
                                                                        </div>
                                                                    </div>
                                                                    <div class="d-flex align-items-baseline gap-1 mt-1">
                                                                        <h6 class="mb-0 fw-bold text-dark filtered-quote"><?php echo e($user->id == ($firstSales->id ?? 1) ? $filteredQuote : 0); ?></h6>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <!-- Prospect Tile -->
                                                            <div class="col-6">
                                                                <div class="p-2 border rounded-3 bg-body-tertiary">
                                                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                                                        <div class="d-flex align-items-center gap-1">
                                                                            <span class="badge bg-label-success p-1 rounded"><i class="mdi mdi-cart-plus"></i></span>
                                                                            <span class="fw-semibold text-dark" style="font-size: 0.78rem;">Prospect</span>
                                                                        </div>
                                                                    </div>
                                                                    <div class="d-flex align-items-baseline gap-1 mt-1">
                                                                        <h6 class="mb-0 fw-bold text-dark filtered-prospect-sales"><?php echo e($user->id == ($firstSales->id ?? 1) ? $filteredProspect : 0); ?></h6>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>

                                                <!-- Panel Kanan: Financial Pipeline Summary -->
                                                <div class="col-12 col-md-5">
                                                    <div class="p-3 border rounded-3 bg-body-tertiary h-100 d-flex flex-column justify-content-between">
                                                        <div>
                                                            <div class="d-flex align-items-center justify-content-between mb-2">
                                                                <span class="fw-bold text-muted text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.5px;">Financial Pipeline</span>
                                                                <i class="mdi mdi-cash-multiple text-primary"></i>
                                                            </div>
                                                            <div class="d-flex flex-column gap-2">
                                                                <div class="d-flex align-items-center justify-content-between p-2 bg-white rounded border">
                                                                    <div class="d-flex align-items-center gap-2">
                                                                        <span class="badge bg-label-secondary p-1 rounded"><i class="mdi mdi-cart"></i></span>
                                                                        <small class="fw-semibold text-dark" style="font-size: 0.75rem;">Quotation</small>
                                                                    </div>
                                                                    <span class="fw-bold text-dark admin-total-quotation" style="font-size: 0.8rem;">
                                                                        Rp <?php echo e($user->id == ($firstSales->id ?? 1) ? number_format($totalQuotation, 0, ',', '.') : 0); ?>

                                                                    </span>
                                                                </div>
                                                                <div class="d-flex align-items-center justify-content-between p-2 bg-white rounded border">
                                                                    <div class="d-flex align-items-center gap-2">
                                                                        <span class="badge bg-label-info p-1 rounded"><i class="mdi mdi-cart-arrow-down"></i></span>
                                                                        <small class="fw-semibold text-dark" style="font-size: 0.75rem;">Prospect</small>
                                                                    </div>
                                                                    <span class="fw-bold text-dark admin-total-prospect" style="font-size: 0.8rem;">
                                                                        Rp <?php echo e($user->id == ($firstSales->id ?? 1) ? number_format($totalProspect, 0, ',', '.') : 0); ?>

                                                                    </span>
                                                                </div>
                                                                <div class="d-flex align-items-center justify-content-between p-2 bg-white rounded border">
                                                                    <div class="d-flex align-items-center gap-2">
                                                                        <span class="badge bg-label-warning p-1 rounded"><i class="mdi mdi-cart-heart"></i></span>
                                                                        <small class="fw-semibold text-dark" style="font-size: 0.75rem;">Hot Prospect</small>
                                                                    </div>
                                                                    <span class="fw-bold text-warning admin-total-hot-prospect" style="font-size: 0.8rem;">
                                                                        Rp <?php echo e($user->id == ($firstSales->id ?? 1) ? number_format($totalHotProspect, 0, ',', '.') : 0); ?>

                                                                    </span>
                                                                </div>
                                                                <div class="d-flex align-items-center justify-content-between p-2 bg-white rounded border">
                                                                    <div class="d-flex align-items-center gap-2">
                                                                        <span class="badge bg-label-success p-1 rounded"><i class="mdi mdi-cart-plus"></i></span>
                                                                        <div>
                                                                            <small class="fw-semibold text-dark d-block" style="font-size: 0.75rem;">PO Received</small>
                                                                            <?php
                                                                                $salesTargetTotal = ($targetSales[$item][0] ?? null)?->total ?? 0;
                                                                                $currentPO = $user->id == ($firstSales->id ?? 1) ? $totalPO : 0;
                                                                                $targetPO = $salesTargetTotal > 0 ? ($currentPO / $salesTargetTotal) * 100 : 0;
                                                                                $color = $targetPO <= 80 ? 'danger' : ($targetPO <= 100 ? 'warning' : 'success');
                                                                            ?>
                                                                            <span class="badge bg-label-<?php echo e($color); ?> rounded-pill admin-target-total-po" style="font-size: 8px;"><?php echo e(round($targetPO)); ?>%</span>
                                                                        </div>
                                                                    </div>
                                                                    <span class="fw-bold text-success admin-total-po" style="font-size: 0.8rem;">
                                                                        Rp <?php echo e($user->id == ($firstSales->id ?? 1) ? number_format($totalPO, 0, ',', '.') : 0); ?>

                                                                    </span>
                                                                </div>
                                                                <div class="d-flex align-items-center justify-content-between p-2 bg-white rounded border">
                                                                    <div class="d-flex align-items-center gap-2">
                                                                        <span class="badge bg-label-danger p-1 rounded"><i class="mdi mdi-cart-minus"></i></span>
                                                                        <small class="fw-semibold text-dark" style="font-size: 0.75rem;">Quotation Loss</small>
                                                                    </div>
                                                                    <span class="fw-bold text-danger admin-total-loss" style="font-size: 0.8rem;">
                                                                        Rp <?php echo e($user->id == ($firstSales->id ?? 1) ? number_format($totalLoss, 0, ',', '.') : 0); ?>

                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Action Buttons Row -->
                                                <div class="col-12 mt-2">
                                                    <?php
                                                        $month = date('m');
                                                        $year = date('Y');
                                                        $dateNow = $month . '-' . $year;
                                                    ?>
                                                    <div class="row g-2">
                                                        <div class="col-2">
                                                            <a class="btn btn-warning btn-sm d-grid w-100 text-white"
                                                                type="button" data-bs-toggle="modal"
                                                                data-bs-target="#overview-sales-<?php echo e($user->id); ?>">
                                                                <i class="mdi mdi-information-outline me-1"></i> Info
                                                            </a>
                                                        </div>
                                                        <div class="col-4">
                                                            <a class="btn btn-facebook btn-sm d-grid w-100"
                                                                href="<?php echo e(route('detail-overview.semester', ['sales' => $user->id, 'date' => $dateNow])); ?>">
                                                                <i class="mdi mdi-eye-outline me-1"></i> Detail
                                                            </a>
                                                        </div>
                                                        <div class="col-6">
                                                            <a class="btn btn-secondary btn-sm d-grid w-100"
                                                                href="<?php echo e(route('overview.semester', $user->id)); ?>">
                                                                <i class="mdi mdi-chart-box-outline me-1"></i> Semester Overview
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php else: ?>
                                            <!-- Non-Sales (Support/Admin) Role Content -->
                                            <div class="row g-3">
                                                <div class="col-12 col-md-6">
                                                    <div class="p-3 border rounded-3 bg-body-tertiary">
                                                        <small class="fw-bold text-muted text-uppercase d-block mb-2" style="font-size: 0.7rem;">Activity Breakdown</small>
                                                        <div class="d-flex flex-column gap-2">
                                                            <div class="d-flex align-items-center justify-content-between p-2 bg-white rounded border">
                                                                <small class="text-dark fw-semibold">Prospect</small>
                                                                <span class="fw-bold filtered-prospect"><?php echo e($user->id == ($firstSales->id ?? 1) ? $filteredProspect : 0); ?> / 100</span>
                                                            </div>
                                                            <div class="d-flex align-items-center justify-content-between p-2 bg-white rounded border">
                                                                <small class="text-dark fw-semibold">Provided</small>
                                                                <span class="fw-bold filtered-provided">0 / <?php echo e($user->id == ($firstSales->id ?? 1) ? $allProspect : 0); ?></span>
                                                            </div>
                                                            <div class="d-flex align-items-center justify-content-between p-2 bg-white rounded border">
                                                                <small class="text-dark fw-semibold">Quotation</small>
                                                                <span class="fw-bold filtered-quote-prospect">0 / <?php echo e($user->id == ($firstSales->id ?? 1) ? $allProspect : 0); ?></span>
                                                            </div>
                                                            <div class="d-flex align-items-center justify-content-between p-2 bg-white rounded border">
                                                                <small class="text-dark fw-semibold">Not Provided</small>
                                                                <span class="fw-bold filtered-not-provided">0 / <?php echo e($user->id == ($firstSales->id ?? 1) ? $allProspect : 0); ?></span>
                                                            </div>
                                                            <div class="d-flex align-items-center justify-content-between p-2 bg-white rounded border">
                                                                <small class="text-dark fw-semibold">Purchase Order</small>
                                                                <span class="fw-bold filtered-po-prospect">0 / 0</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-md-6">
                                                    <div class="p-3 border rounded-3 bg-body-tertiary">
                                                        <small class="fw-bold text-muted text-uppercase d-block mb-2" style="font-size: 0.7rem;">Financial Pipeline</small>
                                                        <div class="d-flex flex-column gap-2">
                                                            <div class="d-flex align-items-center justify-content-between p-2 bg-white rounded border">
                                                                <small class="text-dark fw-semibold">Quotation</small>
                                                                <span class="fw-bold total-prospect-quotation">0</span>
                                                            </div>
                                                            <div class="d-flex align-items-center justify-content-between p-2 bg-white rounded border">
                                                                <small class="text-dark fw-semibold">Hot Prospect</small>
                                                                <span class="fw-bold total-prospect-hot">0</span>
                                                            </div>
                                                            <div class="d-flex align-items-center justify-content-between p-2 bg-white rounded border">
                                                                <small class="text-dark fw-semibold">Purchase Order</small>
                                                                <span class="fw-bold total-prospect-po">0</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-12 mt-2">
                                                    <?php
                                                        $month = date('m');
                                                        $year = date('Y');
                                                        $dateNow = $month . '-' . $year;
                                                    ?>
                                                    <div class="row g-2">
                                                        <div class="col-2">
                                                            <a class="btn btn-warning btn-sm d-grid w-100 text-white"
                                                                type="button" data-bs-toggle="modal"
                                                                data-bs-target="#overview-sales-<?php echo e($item); ?>">
                                                                <i class="mdi mdi-information-outline me-1"></i> Info
                                                            </a>
                                                        </div>
                                                        <div class="col-4">
                                                            <a class="btn btn-facebook btn-sm d-grid w-100"
                                                                href="<?php echo e(route('detail-overview.semester', ['sales' => $user->id, 'date' => $dateNow])); ?>">
                                                                <i class="mdi mdi-eye-outline me-1"></i> Detail
                                                            </a>
                                                        </div>
                                                        <div class="col-6">
                                                            <a class="btn btn-secondary btn-sm d-grid w-100"
                                                                href="<?php echo e(route('overview.semester', $user->id)); ?>">
                                                                <i class="mdi mdi-chart-box-outline me-1"></i> Semester Overview
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <?php
                                    $item++;
                                ?>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="card mb-3">
                <div class="card-datatable table-responsive pt-0">
                    <table class="datatable-prospect-quote table table-bordered">
                        <thead>
                            <tr>
                                <th>Quote No.</th>
                                <th>Company</th>
                                <th>Total Price</th>
                                <th>Description</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th class="text-center" style="width:48px;"></th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <?php
        $item = 0;
    ?>
    <?php $__currentLoopData = $dataOverview; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $overview): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php echo $__env->make('components.modal.overview', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php /**PATH /home/u877155683/domains/reftech.my.id/reftech.my.id/resources/views/pages/sales/dashboard_admin_sales.blade.php ENDPATH**/ ?>