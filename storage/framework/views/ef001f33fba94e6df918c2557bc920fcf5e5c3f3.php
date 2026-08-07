<?php $__env->startSection('title', 'Analisis Profitabilitas Proyek'); ?>
<?php $__env->startSection('no-container'); ?> <?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <div class="container-fluid flex-grow-1 container-p-y">
        <div class="card mb-4 text-white border-0 overflow-hidden position-relative shadow-sm" style="background: linear-gradient(135deg, #696cff 0%, #3f42b3 100%) !important;">
            <!-- Subtle background circle decorations -->
            <div class="position-absolute translate-middle" style="top: 0; right: 0; width: 250px; height: 250px; border-radius: 50%; background: rgba(255,255,255,0.08); z-index: 1;"></div>
            <div class="position-absolute translate-middle" style="bottom: -50px; left: -50px; width: 150px; height: 150px; border-radius: 50%; background: rgba(255,255,255,0.05); z-index: 1;"></div>
            <div class="card-body p-4 position-relative" style="z-index: 2;">
                <div class="row align-items-center">
                    <div class="col-md-12">
                        <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                            <span class="badge bg-white text-primary fw-bold text-uppercase px-3 py-1.5 fs-7" style="border-radius: 5px;">Project Monitoring</span>
                            <?php if($project->status == 6): ?>
                                <span class="badge bg-success text-white fw-bold"><i class="mdi mdi-check-circle me-1"></i> Proyek Selesai (Done)</span>
                            <?php else: ?>
                                <span class="badge bg-warning text-dark fw-bold"><i class="mdi mdi-progress-wrench me-1"></i> Dalam Proses (In Progress)</span>
                            <?php endif; ?>
                        </div>
                        <h3 class="fw-bold mb-1 text-white"><?php echo e($project->company); ?></h3>
                        <p class="mb-0 opacity-80 small">
                            <i class="mdi mdi-tag-outline me-1"></i> No Project: <span class="fw-semibold text-white"><?php echo e($project->no_pending); ?></span>
                            <span class="mx-2">|</span>
                            <i class="mdi mdi-calendar-blank-outline me-1"></i> Tanggal: <span class="fw-semibold text-white"><?php echo e(\Carbon\Carbon::parse($project->date)->format('d M Y')); ?></span>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <?php if(session('success')): ?>
            <div class="alert alert-success alert-dismissible" role="alert">
                <?php echo e(session('success')); ?>

                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php
            $categories = [
                'Service PM' => [
                    1 => 'Pengecekan Spare Part',
                    2 => 'Penjadwalan',
                    3 => 'In Progress',
                    4 => 'Selesai'
                ],
                'Overhaul' => [
                    1 => 'Pengecekan Spare Part',
                    2 => 'Penjadwalan',
                    3 => 'In Progress',
                    4 => 'Selesai'
                ],
                'Rental' => [
                    1 => 'Pengecekan Unit',
                    2 => 'Jadwal Pickup Unit',
                    3 => 'In Progress / Commissioning',
                    4 => 'Pickup Kembali Unit',
                    5 => 'Selesai'
                ],
                'Unit' => [
                    1 => 'Pengecekan Stok Unit',
                    2 => 'Jadwal Pickup',
                    3 => 'Jadwal Commissioning',
                    4 => 'Selesai'
                ],
                'Piping' => [
                    1 => 'Pengecekan Material',
                    2 => 'Kirim Material',
                    3 => 'Progress',
                    4 => 'Commissioning',
                    5 => 'Selesai'
                ]
            ];

            $currentCategory = $project->project_category ?? 'Service PM';
            $steps = $categories[$currentCategory] ?? $categories['Service PM'];
            $totalSteps = count($steps);

            if ($project->status == 6) {
                $currentStep = $totalSteps;
            } elseif ($project->status == 0) {
                $currentStep = 1;
            } else {
                $currentStep = $project->project_status_step ?? 1;
            }
        ?>

        <!-- Project Progress Tracker -->
        <?php
            $totalSteps = count($steps);
            $progressPercent = $totalSteps > 1 ? (($currentStep - 1) / ($totalSteps - 1)) * 100 : 0;
        ?>
        <div class="ppt-card mb-4">
            <!-- Header -->
            <div class="ppt-header">
                <div class="ppt-header-left">
                    <div class="ppt-icon-wrap">
                        <i class="mdi mdi-map-marker-path"></i>
                    </div>
                    <div>
                        <h5 class="ppt-title">Project Progress Tracker</h5>
                        <p class="ppt-subtitle">
                            <?php if(in_array(Auth::user()->role, ['Admin', 'Sales', 'Coordinator'])): ?>
                                Klik pada langkah untuk memperbarui progress proyek.
                            <?php else: ?>
                                Menampilkan progress proyek secara real-time.
                            <?php endif; ?>
                        </p>
                    </div>
                </div>
                <div class="ppt-header-right">
                    <form action="<?php echo e(route('project-monitoring.update-status-step', $project->id)); ?>" method="POST" id="form-update-category" class="ppt-category-form">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="project_status_step" value="1">
                        <div class="ppt-category-wrap">
                            <i class="mdi mdi-tag-outline ppt-category-icon"></i>
                            <select name="project_category" class="ppt-category-select" onchange="this.form.submit()"
                                <?php echo e(!in_array(Auth::user()->role, ['Admin', 'Sales', 'Coordinator']) ? 'disabled' : ''); ?>>
                                <option value="Service PM" <?php echo e($currentCategory == 'Service PM' ? 'selected' : ''); ?>>Service PM</option>
                                <option value="Overhaul" <?php echo e($currentCategory == 'Overhaul' ? 'selected' : ''); ?>>Overhaul / Rebearing</option>
                                <option value="Rental" <?php echo e($currentCategory == 'Rental' ? 'selected' : ''); ?>>Rental</option>
                                <option value="Unit" <?php echo e($currentCategory == 'Unit' ? 'selected' : ''); ?>>Unit Only</option>
                                <option value="Piping" <?php echo e($currentCategory == 'Piping' ? 'selected' : ''); ?>>Piping</option>
                            </select>
                        </div>
                    </form>
                    <div class="ppt-progress-badge">
                        <span class="ppt-progress-label">Step <?php echo e($currentStep); ?> / <?php echo e($totalSteps); ?></span>
                        <span class="ppt-progress-pct"><?php echo e(number_format($progressPercent, 0)); ?>%</span>
                    </div>
                </div>
            </div>


            <!-- Stepper -->
            <div class="ppt-stepper-wrap">
                <div class="ppt-connector-track">
                    <div class="ppt-connector-fill" style="width: <?php echo e($progressPercent); ?>%"></div>
                </div>
                <?php $__currentLoopData = $steps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stepNum => $stepLabel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $isDone   = $stepNum < $currentStep;
                        $isActive = $stepNum == $currentStep;
                        $isPending= $stepNum > $currentStep;
                    ?>
                    <div class="ppt-step <?php echo e($isDone ? 'ppt-step--done' : ($isActive ? 'ppt-step--active' : 'ppt-step--pending')); ?>">
                        <form action="<?php echo e(route('project-monitoring.update-status-step', $project->id)); ?>" method="POST" class="ppt-step-form">
                            <?php echo csrf_field(); ?>
                            <input type="hidden" name="project_category" value="<?php echo e($currentCategory); ?>">
                            <input type="hidden" name="project_status_step" value="<?php echo e($stepNum); ?>">
                            <button type="submit" class="ppt-step-btn"
                                <?php echo e(!in_array(Auth::user()->role, ['Admin', 'Sales', 'Coordinator']) ? 'disabled' : ''); ?>

                                title="<?php echo e($stepLabel); ?>">
                                <?php if($isDone): ?>
                                    <i class="mdi mdi-check"></i>
                                <?php elseif($isActive): ?>
                                    <span class="ppt-step-num"><?php echo e($stepNum); ?></span>
                                    <span class="ppt-step-pulse"></span>
                                <?php else: ?>
                                    <span class="ppt-step-num"><?php echo e($stepNum); ?></span>
                                <?php endif; ?>
                            </button>
                        </form>
                        <div class="ppt-step-label">
                            <?php if($isActive): ?>
                                <span class="ppt-step-badge">Sekarang</span>
                            <?php endif; ?>
                            <span class="ppt-step-text"><?php echo e($stepLabel); ?></span>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>

        <div class="row">
            <!-- Project Information & KPI Column -->
            <div class="col-12 <?php echo e(in_array(Auth::user()->role, ['Admin', 'Finance', 'Accounting']) ? 'col-lg-4' : 'col-lg-12'); ?> mb-4">
                <!-- Info Card -->
                <div class="card mb-4 shadow-sm border-0">
                    <div class="card-header pb-2 border-bottom py-3">
                        <h5 class="card-title mb-0 fw-bold text-primary"><i class="mdi mdi-information-outline me-2"></i> Informasi Proyek</h5>
                    </div>
                    <div class="card-body pt-3">
                        <div class="d-flex align-items-center mb-3 p-2 rounded hover-light">
                            <div class="avatar avatar-sm me-3" style="width: 38px; height: 38px; display: flex; align-items: center; justify-content: center; border-radius: 8px; background-color: rgba(105, 108, 255, 0.08); color: #696cff;">
                                <i class="mdi mdi-briefcase-variant-outline fs-5"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block" style="font-size: 11px;">No Project</small>
                                <span class="fw-semibold text-dark"><?php echo e($project->no_pending); ?></span>
                            </div>
                        </div>
                        <div class="d-flex align-items-center mb-3 p-2 rounded hover-light">
                            <div class="avatar avatar-sm me-3" style="width: 38px; height: 38px; display: flex; align-items: center; justify-content: center; border-radius: 8px; background-color: rgba(3, 195, 236, 0.08); color: #03c3ec;">
                                <i class="mdi mdi-domain fs-5"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block" style="font-size: 11px;">Customer</small>
                                <span class="fw-semibold text-dark"><?php echo e($project->company); ?></span>
                            </div>
                        </div>
                        <div class="d-flex align-items-center mb-3 p-2 rounded hover-light">
                            <div class="avatar avatar-sm me-3" style="width: 38px; height: 38px; display: flex; align-items: center; justify-content: center; border-radius: 8px; background-color: rgba(113, 221, 55, 0.08); color: #71dd37;">
                                <i class="mdi mdi-file-document-outline fs-5"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block" style="font-size: 11px;">No Penawaran</small>
                                <span class="fw-semibold text-dark"><?php echo e($project->no_quote); ?></span>
                            </div>
                        </div>
                        <div class="d-flex align-items-center mb-3 p-2 rounded hover-light">
                            <div class="avatar avatar-sm me-3" style="width: 38px; height: 38px; display: flex; align-items: center; justify-content: center; border-radius: 8px; background-color: rgba(255, 171, 0, 0.08); color: #ffab00;">
                                <i class="mdi mdi-account-tie-outline fs-5"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block" style="font-size: 11px;">Sales PIC</small>
                                <span class="fw-semibold text-dark"><?php echo e($project->sales_name); ?></span>
                            </div>
                        </div>
                        <div class="d-flex align-items-center mb-3 p-2 rounded hover-light">
                            <div class="avatar avatar-sm me-3" style="width: 38px; height: 38px; display: flex; align-items: center; justify-content: center; border-radius: 8px; background-color: rgba(255, 62, 29, 0.08); color: #ff3e1d;">
                                <i class="mdi mdi-account-outline fs-5"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block" style="font-size: 11px;">Customer PIC</small>
                                <span class="fw-semibold text-dark"><?php echo e($project->pic_name); ?></span>
                            </div>
                        </div>
                        
                        <!-- Project Status and Update Action -->
                        <div class="mt-4 pt-3 border-top">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="text-muted small fw-semibold">Status Project:</span>
                                <?php
                                    switch ($project->status) {
                                        case 1: $statusName = 'On Check'; $statusBadge = 'bg-label-warning'; break;
                                        case 2: $statusName = 'Ready Stock'; $statusBadge = 'bg-label-info'; break;
                                        case 3: $statusName = 'Kurang'; $statusBadge = 'bg-label-danger'; break;
                                        case 4: $statusName = 'Pre-Order'; $statusBadge = 'bg-label-primary'; break;
                                        case 5: $statusName = 'Delivery Process'; $statusBadge = 'bg-label-linkedin'; break;
                                        case 6: $statusName = 'Done'; $statusBadge = 'bg-label-success'; break;
                                        case 7: $statusName = 'Cancel'; $statusBadge = 'bg-label-danger'; break;
                                        default: $statusName = 'In Progress'; $statusBadge = 'bg-label-secondary'; break;
                                    }
                                ?>
                                <span class="badge <?php echo e($statusBadge); ?> fw-semibold fs-7"><?php echo e($statusName); ?></span>
                            </div>
                            
                            <?php if($project->status != '6' && $project->status != '8' && $project->status != '9'): ?>
                                <button type="button" class="btn btn-primary w-100 hover-elevate fw-semibold mb-2" data-bs-toggle="modal" data-bs-target="#statusEdit" <?php echo e(auth::user()->role != 'Sales' ? '' : 'disabled'); ?>>
                                    <i class="mdi mdi-square-edit-outline me-1"></i> Update Status Project
                                </button>
                                <form action="<?php echo e(route('pending-po.changeType', $project->id)); ?>" method="POST">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" class="btn btn-outline-warning w-100 hover-elevate fw-semibold" onclick="return confirm('Apakah Anda yakin ingin memindahkan proyek ini ke Sales Order?')">
                                        <i class="mdi mdi-swap-horizontal me-1"></i> Move to Sales Order
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabs Section Column -->
            <?php if(in_array(Auth::user()->role, ['Admin', 'Finance', 'Accounting'])): ?>
            <div class="col-12 col-lg-8 mb-4">
                <div class="nav-align-top mb-4">
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item">
                            <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#navs-financial" aria-controls="navs-financial" aria-selected="true">
                                <i class="mdi mdi-finance me-1"></i> Keuangan (Financial)
                            </button>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <!-- Financial Details Tab -->
                        <div class="tab-pane fade show active" id="navs-financial" role="tabpanel">
                            
                            <!-- Kesehatan Keuangan (Financial Health Summary) -->
                            <div class="card bg-label-primary mb-4 border-0 shadow-none">
                                <div class="card-body p-4">
                                    <h5 class="card-title text-primary fw-bold mb-4"><i class="mdi mdi-chart-line me-2"></i> Ringkasan Kesehatan Keuangan</h5>
                                    <div class="row g-3">
                                        <div class="col-12 col-md-4">
                                            <div class="p-3 bg-white rounded border border-light shadow-sm">
                                                <span class="text-muted small d-block mb-1">Total Pendapatan (Revenue)</span>
                                                <h4 class="fw-bold text-success mb-0">Rp <?php echo e(number_format($project->revenue, 0, ',', '.')); ?></h4>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-4">
                                            <div class="p-3 bg-white rounded border border-light shadow-sm">
                                                <span class="text-muted small d-block mb-1">Total Biaya (COGS)</span>
                                                <h4 class="fw-bold text-danger mb-0">Rp <?php echo e(number_format($totalCost, 0, ',', '.')); ?></h4>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-4">
                                            <div class="p-3 bg-white rounded border border-light shadow-sm">
                                                <span class="text-muted small d-block mb-1">Estimasi Net Profit</span>
                                                <h4 class="fw-bold <?php echo e($profit >= 0 ? 'text-primary' : 'text-danger'); ?> mb-0">
                                                    Rp <?php echo e(number_format($profit, 0, ',', '.')); ?>

                                                </h4>
                                            </div>
                                        </div>
                                    </div>
                                    <hr class="my-4 opacity-50">
                                    
                                    <!-- Progress Bar of Cost Ratio -->
                                    <?php
                                        $costRatio = $project->revenue > 0 ? ($totalCost / $project->revenue) * 100 : 0;
                                        $profitRatio = $project->revenue > 0 ? ($profit / $project->revenue) * 100 : 0;
                                    ?>
                                    <div>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span class="text-muted small fw-semibold">Rasio Pengeluaran vs Margin Keuntungan</span>
                                            <span class="badge bg-success-subtle text-success fs-7" style="background-color: rgba(113, 221, 55, 0.15); color: #71dd37; padding: 4px 8px; border-radius: 4px;"><?php echo e(number_format($profitRatio, 1)); ?>% Margin</span>
                                        </div>
                                        <div class="progress rounded-pill" style="height: 12px; overflow: hidden; background-color: rgba(0,0,0,0.05);">
                                            <div class="progress-bar bg-danger animate-bar" role="progressbar" style="width: <?php echo e($costRatio); ?>%" aria-valuenow="<?php echo e($costRatio); ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                            <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo e($profitRatio); ?>%" aria-valuenow="<?php echo e($profitRatio); ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                        <div class="d-flex justify-content-between mt-2 text-muted small" style="font-size: 11px;">
                                            <span>Rasio Biaya: <?php echo e(number_format($costRatio, 1)); ?>%</span>
                                            <span>Target: > 20% Margin</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Inner navigation pills for sub-tabs -->
                            <div class="nav-align-top">
                                <ul class="nav nav-pills mb-3" role="tablist">
                                    <li class="nav-item">
                                        <button type="button" class="nav-link active btn-sm" role="tab" data-bs-toggle="pill" data-bs-target="#subnavs-revenue" aria-controls="subnavs-revenue" aria-selected="true">
                                            <i class="mdi mdi-bank-transfer-in me-1"></i> Pendapatan (Quotation)
                                        </button>
                                    </li>
                                    <li class="nav-item">
                                        <button type="button" class="nav-link btn-sm" role="tab" data-bs-toggle="pill" data-bs-target="#subnavs-purchases" aria-controls="subnavs-purchases" aria-selected="false">
                                            <i class="mdi mdi-cart-outline me-1"></i> Pembelian Barang (PR)
                                        </button>
                                    </li>
                                    <li class="nav-item">
                                        <button type="button" class="nav-link btn-sm" role="tab" data-bs-toggle="pill" data-bs-target="#subnavs-expenses" aria-controls="subnavs-expenses" aria-selected="false">
                                            <i class="mdi mdi-cash-multiple me-1"></i> Biaya Operasional
                                        </button>
                                    </li>
                                </ul>
                                <div class="tab-content p-0 border-0 shadow-none bg-transparent">
                                    <!-- Revenue Details Tab -->
                                    <div class="tab-pane fade show active" id="subnavs-revenue" role="tabpanel">
                            <h6 class="fw-semibold mb-3">Item Penawaran yang Disetujui</h6>
                            <div class="table-responsive text-nowrap border rounded">
                                <table class="table table-striped mb-0">
                                    <thead>
                                        <tr class="table-primary">
                                            <th>Nama Barang / Jasa</th>
                                            <th class="text-center">Qty</th>
                                            <th>Satuan</th>
                                            <th class="text-end">Harga Satuan</th>
                                            <th class="text-end">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__empty_1 = true; $__currentLoopData = $quoteItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                            <tr>
                                                <td class="text-wrap"><?php echo e($item->item_name); ?></td>
                                                <td class="text-center"><?php echo e($item->qty); ?></td>
                                                <td><?php echo e($item->unit ?? 'Unit'); ?></td>
                                                <td class="text-end">Rp <?php echo e(number_format($item->price, 0, ',', '.')); ?></td>
                                                <td class="text-end">Rp <?php echo e(number_format($item->amount, 0, ',', '.')); ?></td>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                            <tr>
                                                <td colspan="5" class="text-center">Tidak ada rincian penawaran.</td>
                                            </tr>
                                        <?php endif; ?>
                                        <tr class="table-light">
                                            <td colspan="4" class="text-end fw-bold">Nett Pendapatan:</td>
                                            <td class="text-end fw-bold text-success">Rp <?php echo e(number_format($project->revenue, 0, ',', '.')); ?></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Purchase requests Tab -->
                        <div class="tab-pane fade" id="subnavs-purchases" role="tabpanel">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="fw-semibold m-0">Log Pembelian Barang & Spare Part</h6>
                                <span class="badge bg-label-danger">Total Biaya PR: Rp <?php echo e(number_format($materialCost, 0, ',', '.')); ?></span>
                            </div>
                            <div class="table-responsive text-nowrap border rounded">
                                <table class="table table-striped mb-0">
                                    <thead>
                                        <tr class="table-warning">
                                            <th>No PR</th>
                                            <th>Commodity / Part</th>
                                            <th class="text-center">Qty</th>
                                            <th>Status</th>
                                            <th class="text-end">Harga Beli</th>
                                            <th class="text-end">Total Nominal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__empty_1 = true; $__currentLoopData = $purchases; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pr): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                            <tr>
                                                <td>
                                                    <span class="text-primary fw-semibold"><?php echo e($pr->no_pr); ?></span>
                                                </td>
                                                <td class="text-wrap">
                                                    <?php if($pr->equivalent): ?>
                                                        <?php echo e($pr->equivalent->product->commodity ?? '-'); ?>

                                                        (<?php echo e($pr->equivalent->pn ?? ''); ?>)
                                                    <?php else: ?>
                                                        -
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-center"><?php echo e($pr->qty); ?></td>
                                                <td>
                                                    <?php if($pr->status == '3'): ?>
                                                        <span class="badge bg-success">Received</span>
                                                    <?php elseif($pr->status == '2'): ?>
                                                        <span class="badge bg-info">Ordered</span>
                                                    <?php elseif($pr->status == '1'): ?>
                                                        <span class="badge bg-warning">Approved</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-secondary">Pending</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-end">
                                                    <?php if($pr->price): ?>
                                                        Rp <?php echo e(number_format($pr->price, 0, ',', '.')); ?>

                                                    <?php else: ?>
                                                        -
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-end">
                                                    <?php if($pr->amount): ?>
                                                        Rp <?php echo e(number_format($pr->amount, 0, ',', '.')); ?>

                                                    <?php else: ?>
                                                        -
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                            <tr>
                                                <td colspan="6" class="text-center">Belum ada pengajuan pembelian barang (PR) untuk proyek ini.</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Operational Expenses Tab -->
                        <div class="tab-pane fade" id="subnavs-expenses" role="tabpanel">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="fw-semibold m-0">Pengeluaran Operasional & Lapangan</h6>
                                <button type="button" class="btn btn-primary btn-sm waves-effect" data-bs-toggle="modal" data-bs-target="#addExpenseModal">
                                    <i class="mdi mdi-plus me-1"></i> Catat Biaya Baru
                                </button>
                            </div>

                            <!-- Summary of Expenses -->
                            <div class="row mb-3">
                                <div class="col-md-6 mb-2">
                                    <div class="p-3 border rounded bg-light">
                                        <small class="text-muted d-block">Biaya Operasional Lapangan</small>
                                        <span class="h5 fw-bold text-danger">Rp <?php echo e(number_format($generalCost, 0, ',', '.')); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <div class="p-3 border rounded bg-light">
                                        <small class="text-muted d-block">Biaya Pengiriman (Ongkir Resi)</small>
                                        <span class="h5 fw-bold text-danger">Rp <?php echo e(number_format($shippingCost, 0, ',', '.')); ?></span>
                                    </div>
                                </div>
                            </div>

                            <div class="table-responsive text-nowrap border rounded mb-3">
                                <table class="table table-striped mb-0">
                                    <thead>
                                        <tr class="table-info">
                                            <th>Tanggal</th>
                                            <th>Nama Pengeluaran</th>
                                            <th>Kategori</th>
                                            <th>Oleh</th>
                                            <th class="text-end">Nominal</th>
                                            <th class="text-center">Nota</th>
                                            <?php if(in_array(Auth::user()->role, ['Admin', 'Accounting', 'Finance'])): ?>
                                                <th class="text-center">Aksi</th>
                                            <?php endif; ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__empty_1 = true; $__currentLoopData = $expenses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $exp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                            <tr>
                                                <td><?php echo e(\Carbon\Carbon::parse($exp->date)->format('d-M-Y')); ?></td>
                                                <td class="text-wrap" style="max-width: 150px;"><?php echo e($exp->name); ?></td>
                                                <td>
                                                    <span class="badge bg-label-info"><?php echo e($exp->category); ?></span>
                                                </td>
                                                <td><?php echo e($exp->user->name); ?></td>
                                                <td class="text-end">Rp <?php echo e(number_format($exp->amount, 0, ',', '.')); ?></td>
                                                <td class="text-center">
                                                    <?php if($exp->receipt): ?>
                                                        <a href="<?php echo e(asset($exp->receipt)); ?>" target="_blank" class="text-primary fs-4">
                                                            <i class="mdi mdi-file-image-outline"></i>
                                                        </a>
                                                    <?php else: ?>
                                                        -
                                                    <?php endif; ?>
                                                </td>
                                                <?php if(in_array(Auth::user()->role, ['Admin', 'Accounting', 'Finance'])): ?>
                                                    <td class="text-center">
                                                        <form action="<?php echo e(route('project-monitoring.destroy-expense', $exp->id)); ?>" method="post" onsubmit="return confirm('Apakah Anda yakin ingin menghapus biaya ini?')">
                                                            <?php echo csrf_field(); ?>
                                                            <?php echo method_field('DELETE'); ?>
                                                            <button type="submit" class="btn btn-xs btn-outline-danger p-1">
                                                                <i class="mdi mdi-delete-outline"></i>
                                                            </button>
                                                        </form>
                                                    </td>
                                                <?php endif; ?>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                            <tr>
                                                <td colspan="7" class="text-center">Belum ada pengeluaran operasional yang dicatat.</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>
        </div>

    <!-- Standalone Cek Barang (Logistik) Card -->
    <div class="card mb-4 shadow-sm border">
        <div class="card-header bg-light py-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h5 class="m-0 fw-bold"><i class="mdi mdi-checkbox-marked-circle-outline text-primary me-1"></i> Pengecekan Spare Part & Unit Proyek (Logistik)</h5>
            <?php if(in_array(Auth::user()->role, ['Admin', 'Logistic', 'Coordinator'])): ?>
                <button type="button" class="btn btn-primary btn-sm waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#replacementEdit">
                    <i class="mdi mdi-square-edit-outline me-1"></i> Update Status & Stock
                </button>
            <?php else: ?>
                <button type="button" class="btn btn-secondary btn-sm" disabled>
                    <i class="mdi mdi-lock-outline me-1"></i> Update (Logistic/Admin Only)
                </button>
            <?php endif; ?>
        </div>
        <div class="card-body pt-3">
            <div class="table-responsive text-nowrap border rounded">
                <table class="table table-striped mb-0">
                    <thead>
                        <tr class="table-info">
                            <th style="width: 5%">No</th>
                            <th style="width: 25%">Item</th>
                            <th style="width: 20%">Equivalent / Replacement</th>
                            <th style="width: 8%" class="text-center">Qty</th>
                            <th style="width: 12%">Status</th>
                            <th style="width: 8%" class="text-center">BDG</th>
                            <th style="width: 8%" class="text-center">BKS</th>
                            <th style="width: 14%">Note</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $abjad = 64; ?>
                        <?php $__currentLoopData = $subQuote; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subJudul): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $no = 1;
                                $abjad++;
                            ?>
                            <tr class="table-light border-top">
                                <td class="fw-bold text-center"><?php echo e(chr($abjad)); ?></td>
                                <td colspan="7" class="fw-bold"><?php echo e($subJudul->subtitle); ?></td>
                            </tr>
                            <?php $__currentLoopData = $subJudul->detail; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    switch (@$product->pending[0]->status) {
                                        case 1: $status = 'On Check'; $badge = 'bg-label-warning'; break;
                                        case 2: $status = 'Ready Stock'; $badge = 'bg-label-info'; break;
                                        case 3: $status = 'Kurang'; $badge = 'bg-label-danger'; break;
                                        case 4: $status = 'Pre-Order'; $badge = 'bg-label-primary'; break;
                                        case 5: $status = 'Delivery Process'; $badge = 'bg-label-linkedin'; break;
                                        case 6: $status = 'Done'; $badge = 'bg-label-success'; break;
                                        case 7: $status = 'Cancel'; $badge = 'bg-label-danger'; break;
                                        default: $status = 'Belum Di Cek'; $badge = 'bg-label-secondary'; break;
                                    }
                                ?>
                                <tr>
                                    <td class="text-center"><?php echo e($no); ?></td>
                                    <td class="text-wrap" style="max-width: 200px;"><?php echo e($product->product); ?></td>
                                    <td>
                                        <?php if($product->pending[0]->id_equivalent && $product->pending[0]->equivalent): ?>
                                            <span class="fw-semibold text-primary">
                                                <?php echo e($product->pending[0]->equivalent->brand); ?> <?php echo e($product->pending[0]->equivalent->pn); ?>

                                            </span>
                                            <small class="text-muted d-block" style="font-size: 11px;">
                                                <?php echo e($product->pending[0]->equivalent->product?->commodity); ?> (<?php echo e($product->pending[0]->equivalent->product?->go == 'Replacement' ? 'R' : 'G'); ?>)
                                            </small>
                                        <?php else: ?>
                                            <span class="text-muted small">Belum dipetakan ke stok</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center"><?php echo e($product->qty); ?> <?php echo e($product->info_qty); ?></td>
                                    <td>
                                        <span class="badge <?php echo e($project->status == 6 ? 'bg-label-success' : $badge); ?>">
                                            <?php echo e($project->status == 6 ? 'Done' : $status); ?>

                                        </span>
                                    </td>
                                    <td class="text-center"><?php echo e($product->pending[0]->bdg ?? 0); ?></td>
                                    <td class="text-center"><?php echo e($product->pending[0]->bks ?? 0); ?></td>
                                    <td class="text-wrap" style="max-width: 150px;"><?php echo e($product->pending[0]->note ?? '-'); ?></td>
                                </tr>
                                <?php $no++; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div> <!-- closes container-xxl -->

    <!-- Add Expense Modal -->
    <div class="modal fade" id="addExpenseModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalCenterTitle">Catat Pengeluaran Proyek Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="<?php echo e(route('project-monitoring.store-expense', $project->id)); ?>" method="post" enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label for="name" class="form-label">Deskripsi Pengeluaran</label>
                                <input type="text" id="name" name="name" class="form-control" placeholder="Contoh: Tiket Kereta Teknisi / Bensin Mobil" required />
                            </div>
                        </div>
                        <div class="row g-2">
                            <div class="col-6 mb-3">
                                <label for="category" class="form-label">Kategori</label>
                                <select id="category" name="category" class="form-select" required>
                                    <option value="" disabled selected>Pilih Kategori...</option>
                                    <option value="Transport">Transport</option>
                                    <option value="Akomodasi">Akomodasi (Hotel/Penginapan)</option>
                                    <option value="Konsumsi">Konsumsi</option>
                                    <option value="Material">Material Lapangan</option>
                                    <option value="Alat">Sewa Alat</option>
                                    <option value="Lain-lain">Lain-lain</option>
                                </select>
                            </div>
                            <div class="col-6 mb-3">
                                <label for="date" class="form-label">Tanggal Pengeluaran</label>
                                <input type="date" id="date" name="date" class="form-control" value="<?php echo e(now()->format('Y-m-d')); ?>" required />
                            </div>
                        </div>
                        <div class="row g-2">
                            <div class="col-6 mb-3">
                                <label for="amount" class="form-label">Nominal Biaya (Rp)</label>
                                <input type="number" id="amount" name="amount" class="form-control" min="0" placeholder="Contoh: 150000" required />
                            </div>
                            <div class="col-6 mb-3">
                                <label for="receipt" class="form-label">Upload Nota / Receipt (Gambar/PDF)</label>
                                <input type="file" id="receipt" name="receipt" class="form-control" accept="image/*,application/pdf" />
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Biaya</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Replacement Stock Checking Modal -->
    <?php echo $__env->make('components.modal.pending.project', ['pending' => $project], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <!-- Update Project Status Modal -->
    <?php echo $__env->make('components.modal.pending.status', ['pending' => $project], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <?php $__env->startPush('after-style'); ?>
        <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/select2/select2.css" />
        <style>
            .hover-light {
                transition: background-color 0.2s ease-in-out;
            }
            .hover-light:hover {
                background-color: rgba(0, 0, 0, 0.02) !important;
            }
            .hover-elevate {
                transition: all 0.25s ease;
            }
            .hover-elevate:hover {
                transform: translateY(-2px);
                box-shadow: 0 4px 12px rgba(0,0,0,0.1) !important;
            }
            .btn-white {
                background-color: #ffffff !important;
                color: #696cff !important;
            }
            .btn-white:hover {
                background-color: #f1f1f1 !important;
                color: #4f52b3 !important;
            }

            /* ===== PROJECT PROGRESS TRACKER (PPT) ===== */
            .ppt-card {
                background: #fff;
                border-radius: 16px;
                box-shadow: 0 2px 20px rgba(105, 108, 255, 0.08), 0 1px 4px rgba(0,0,0,0.04);
                border: 1px solid rgba(105, 108, 255, 0.1);
                overflow: hidden;
            }

            /* Header */
            .ppt-header {
                display: flex;
                align-items: center;
                justify-content: space-between;
                flex-wrap: wrap;
                gap: 12px;
                padding: 20px 24px 16px;
                border-bottom: 1px solid rgba(105, 108, 255, 0.08);
                background: linear-gradient(135deg, rgba(105,108,255,0.03) 0%, rgba(255,255,255,0) 100%);
            }
            .ppt-header-left {
                display: flex;
                align-items: center;
                gap: 14px;
            }
            .ppt-icon-wrap {
                width: 44px;
                height: 44px;
                border-radius: 12px;
                background: linear-gradient(135deg, #696cff 0%, #4547a9 100%);
                display: flex;
                align-items: center;
                justify-content: center;
                color: #fff;
                font-size: 20px;
                flex-shrink: 0;
                box-shadow: 0 4px 12px rgba(105, 108, 255, 0.35);
            }
            .ppt-title {
                margin: 0 0 2px;
                font-size: 15px;
                font-weight: 700;
                color: #2c2d5b;
                letter-spacing: -0.2px;
            }
            .ppt-subtitle {
                margin: 0;
                font-size: 12px;
                color: #8f9aaf;
                line-height: 1.4;
            }
            .ppt-header-right {
                display: flex;
                align-items: center;
                gap: 12px;
                flex-wrap: wrap;
            }

            /* Category selector */
            .ppt-category-form { margin: 0; }
            .ppt-category-wrap {
                display: flex;
                align-items: center;
                gap: 8px;
                background: #f4f5fb;
                border: 1px solid rgba(105, 108, 255, 0.15);
                border-radius: 10px;
                padding: 6px 12px;
                transition: border-color 0.2s;
            }
            .ppt-category-wrap:focus-within {
                border-color: #696cff;
                box-shadow: 0 0 0 3px rgba(105, 108, 255, 0.12);
            }
            .ppt-category-icon {
                color: #696cff;
                font-size: 15px;
            }
            .ppt-category-select {
                border: none;
                background: transparent;
                font-size: 13px;
                font-weight: 600;
                color: #2c2d5b;
                outline: none;
                cursor: pointer;
                padding: 0;
                min-width: 140px;
            }
            .ppt-category-select:disabled {
                opacity: 0.6;
                cursor: default;
            }

            /* Progress badge */
            .ppt-progress-badge {
                display: flex;
                flex-direction: column;
                align-items: flex-end;
                line-height: 1.2;
            }
            .ppt-progress-label {
                font-size: 11px;
                color: #8f9aaf;
                font-weight: 500;
            }
            .ppt-progress-pct {
                font-size: 18px;
                font-weight: 800;
                color: #696cff;
                letter-spacing: -0.5px;
            }

            /* Thin top progress bar */
            .ppt-progress-bar-wrap {
                padding: 0 24px;
                background: transparent;
            }
            .ppt-progress-bar-track {
                height: 3px;
                background: rgba(105, 108, 255, 0.1);
                border-radius: 99px;
                overflow: hidden;
            }
            .ppt-progress-bar-fill {
                height: 100%;
                background: linear-gradient(90deg, #696cff 0%, #9b5de5 100%);
                border-radius: 99px;
                transition: width 0.6s cubic-bezier(0.4, 0, 0.2, 1);
            }

            /* Stepper area */
            .ppt-stepper-wrap {
                position: relative;
                display: flex;
                align-items: flex-start;
                justify-content: space-between;
                padding: 28px 24px 28px;
            }

            /* Connector line (behind steps) */
            .ppt-connector-track {
                position: absolute;
                top: 46px; /* vertically centered to step buttons */
                left: calc(24px + 28px); /* align start to center of first step btn */
                right: calc(24px + 28px);
                height: 3px;
                background: #e8eaf2;
                border-radius: 99px;
                z-index: 1;
                overflow: hidden;
            }
            .ppt-connector-fill {
                height: 100%;
                background: linear-gradient(90deg, #696cff 0%, #9b5de5 100%);
                border-radius: 99px;
                transition: width 0.7s cubic-bezier(0.4, 0, 0.2, 1);
            }

            /* Individual step */
            .ppt-step {
                display: flex;
                flex-direction: column;
                align-items: center;
                text-align: center;
                flex: 1;
                position: relative;
                z-index: 2;
            }
            .ppt-step-form { margin: 0; }

            /* Step button */
            .ppt-step-btn {
                position: relative;
                width: 48px;
                height: 48px;
                border-radius: 50%;
                border: 3px solid #e8eaf2;
                background: #fff;
                font-size: 15px;
                font-weight: 700;
                color: #b0bac9;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                transition: all 0.25s ease;
                overflow: visible;
                padding: 0;
                box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            }
            .ppt-step-btn:hover:not(:disabled) {
                transform: scale(1.12);
                box-shadow: 0 4px 16px rgba(105, 108, 255, 0.22);
                border-color: #696cff;
                color: #696cff;
            }

            /* Done step */
            .ppt-step--done .ppt-step-btn {
                background: linear-gradient(135deg, #28c76f 0%, #00b383 100%);
                border-color: #28c76f;
                color: #fff;
                box-shadow: 0 4px 12px rgba(40, 199, 111, 0.3);
            }

            /* Active step */
            .ppt-step--active .ppt-step-btn {
                background: linear-gradient(135deg, #696cff 0%, #4547a9 100%);
                border-color: #696cff;
                color: #fff;
                box-shadow: 0 0 0 5px rgba(105, 108, 255, 0.18), 0 4px 16px rgba(105, 108, 255, 0.4);
            }

            /* Pulse ring animation on active step */
            .ppt-step-pulse {
                position: absolute;
                inset: -6px;
                border-radius: 50%;
                border: 2px solid rgba(105, 108, 255, 0.5);
                animation: ppt-pulse 1.8s ease-out infinite;
                pointer-events: none;
            }
            @keyframes ppt-pulse {
                0%   { transform: scale(1);   opacity: 0.7; }
                100% { transform: scale(1.55); opacity: 0; }
            }

            /* Step labels */
            .ppt-step-label {
                margin-top: 10px;
                display: flex;
                flex-direction: column;
                align-items: center;
                gap: 3px;
            }
            .ppt-step-badge {
                display: inline-block;
                font-size: 9px;
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                color: #696cff;
                background: rgba(105, 108, 255, 0.1);
                padding: 2px 7px;
                border-radius: 99px;
            }
            .ppt-step-text {
                font-size: 11px;
                font-weight: 500;
                line-height: 1.3;
                max-width: 90px;
                color: #8f9aaf;
            }
            .ppt-step--done .ppt-step-text { color: #28c76f; font-weight: 600; }
            .ppt-step--active .ppt-step-text { color: #696cff; font-weight: 700; }

            /* Responsive for small screens */
            @media (max-width: 576px) {
                .ppt-header { padding: 16px; }
                .ppt-stepper-wrap { padding: 20px 12px; }
                .ppt-connector-track {
                    left: calc(12px + 24px);
                    right: calc(12px + 24px);
                }
                .ppt-step-btn { width: 38px; height: 38px; font-size: 13px; }
                .ppt-connector-track { top: 41px; }
                .ppt-step-text { font-size: 10px; max-width: 60px; }
                .ppt-progress-badge { display: none; }
            }
        </style>
    <?php $__env->stopPush(); ?>

    <?php $__env->startPush('after-script'); ?>
        <script src="<?php echo e(asset('assets')); ?>/vendor/libs/select2/select2.js"></script>
        <script>
            $(document).ready(function() {
                if ($('#replacementEdit .select2').length) {
                    $('#replacementEdit .select2').each(function() {
                        $(this).select2({
                            dropdownParent: $('#replacementEdit')
                        });
                    });
                }
            });
        </script>
    <?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.sales.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u877155683/domains/reftech.my.id/reftech.my.id/resources/views/pages/project-monitoring/show.blade.php ENDPATH**/ ?>