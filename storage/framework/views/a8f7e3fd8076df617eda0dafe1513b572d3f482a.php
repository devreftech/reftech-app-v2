
<?php $__env->startSection('title', 'Detail Overview Sales'); ?>
<?php $__env->startSection('content'); ?>
    <h4 class="fw-bold py-3 mb-4">
        Detail Overview <?php echo e($user->name); ?>, <?php echo e($dates); ?>

    </h4>
    <div class="card mb-3">
        <div class="card-body">
            <div class="row">
                <div class="col-12 col-md-3">
                    <img src="<?php echo e(url('') . '/' . $user->image); ?>" alt="" srcset="" class="h-100 w-100">
                </div>
                <div class="col-12 col-md-9">
                    <?php if($user->id != '16'): ?>
                        <?php if($user->role == 'Sales'): ?>
                            <div class="row">
                                <div class="col-12">
                                    <h4><?php echo e($user->name); ?></h4>
                                </div>
                                <div class="col-4">
                                    <p class="fw-medium fs-normal">Key Performance Indicator</p>
                                    <div class="d-flex mb-2 gap-2">
                                        <a href="#activities">
                                            <div class="avatar">
                                                <div class="avatar-initial bg-label-secondary rounded">
                                                    <i class="mdi mdi-account-multiple-plus-outline mdi-24px"></i>
                                                </div>
                                            </div>
                                        </a>
                                        <div class="card-info">
                                            <h5 class="mb-0"><?php echo e($totalLeads); ?><span
                                                    class="text-muted fs-tiny fw-normal">/<?php echo e($target->leads ?? '-'); ?></span>
                                            </h5>
                                            <small class="text-muted">New Leads</small>
                                        </div>
                                    </div>
                                    <div class="d-flex mb-2 gap-2">
                                        <a href="#activities">
                                            <div class="avatar">
                                                <div class="avatar-initial bg-label-info rounded">
                                                    <i class="mdi mdi-phone-outline mdi-24px"></i>
                                                </div>
                                            </div>
                                        </a>
                                        <div class="card-info">
                                            <h5 class="mb-0"><?php echo e($totalDC); ?> <span
                                                    class="text-muted fs-tiny fw-normal">/<?php echo e($target->dc ?? '-'); ?></span>
                                            </h5>
                                            <small
                                                class="text-muted"><?php echo e($user->id == '1' ? 'New Leads' : 'Daily Call'); ?></small>
                                        </div>
                                    </div>
                                    <div class="d-flex mb-2 gap-2">
                                        <a href="#activities">
                                            <div class="avatar">
                                                <div class="avatar-initial bg-label-primary rounded">
                                                    <i class="mdi mdi-account-multiple-outline mdi-24px"></i>
                                                </div>
                                            </div>
                                        </a>
                                        <div class="card-info">
                                            <h5 class="mb-0"><?php echo e($totalCRM); ?><span
                                                    class="text-muted fs-tiny fw-normal">/<?php echo e($jumlahCustomer); ?></span>
                                            </h5>
                                            <small class="text-muted">CRM</small>
                                        </div>
                                    </div>
                                    <?php
                                        $lastDetail = $user->detail->last();
                                    ?>
                                    <?php if($lastDetail && ($lastDetail->area == 'Bekasi' || $lastDetail->area == 'Jabodetabek' || $lastDetail->area == 'Jawa Barat')): ?>
                                        <div class="d-flex mb-2 gap-2">
                                            <a href="#activities">
                                                <div class="avatar">
                                                    <div class="avatar-initial bg-label-danger rounded">
                                                        <i class="mdi mdi-office-building-marker-outline mdi-24px"></i>
                                                    </div>
                                                </div>
                                            </a>
                                            <div class="card-info">
                                                <h5 class="mb-0"><?php echo e($totalVisit); ?><span
                                                        class="text-muted fs-tiny fw-normal">/<?php echo e($target->visit ?? '-'); ?></span>
                                                </h5>
                                                <small class="text-muted">Visit</small>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                    <div class="d-flex mb-2 gap-2">
                                        <a href="#quote">
                                            <div class="avatar">
                                                <div class="avatar-initial bg-label-warning rounded">
                                                    <i class="mdi mdi-email-multiple-outline mdi-24px"></i>
                                                </div>
                                            </div>
                                        </a>
                                        <div class="card-info">
                                            <h5 class="mb-0"><?php echo e($totalQuote); ?><span
                                                    class="text-muted fs-tiny fw-normal">/<?php echo e($target->quote ?? '-'); ?></span>
                                            </h5>
                                            <small class="text-muted">Quotation</small>
                                        </div>
                                    </div>
                                    <div class="d-flex mb-2 gap-2">
                                        <a href="#po">
                                            <div class="avatar">
                                                <div class="avatar-initial bg-label-success rounded">
                                                    <i class="mdi mdi-cart-plus mdi-24px"></i>
                                                </div>
                                            </div>
                                        </a>
                                        <div class="card-info">
                                            <h5 class="mb-0"><?php echo e($totalPO); ?>

                                            </h5>
                                            <small class="text-muted">Purchase Order</small>
                                        </div>
                                    </div>
                                    
                                </div>
                                <div class="col-8">
                                    <p class="fw-medium fs-normal">Achievement</p>

                                    <div class="d-flex align-items-center gap-2">
                                        <div class="d-flex mb-2 gap-2">
                                            <div class="avatar">
                                                <div class="avatar-initial bg-label-success rounded">
                                                    <i class="mdi mdi-cart-plus mdi-24px"></i>
                                                </div>
                                            </div>
                                            <div class="card-info">
                                                <h5 class="mb-0">Rp
                                                    <?php echo e(number_format($amountSales, 2, ',', '.')); ?>

                                                    <?php
                                                        $jumlah_target = [];
                                                        if (isset($target->total) && $target->total != 0) {
                                                            $jumlah_target = ($amountSales / $target->total) * 100;
                                                        } else {
                                                            $jumlah_target = 0;
                                                        }
                                                    ?>
                                                    <span class="text-success mb-0">
                                                        <?php echo e(number_format($jumlah_target, 3)); ?>%
                                                    </span>
                                                </h5>
                                                <small class="text-muted">Total Sales</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="d-flex mb-2 gap-2">
                                            <div class="avatar">
                                                <div class="avatar-initial bg-label-primary rounded">
                                                    <i class="mdi mdi-email-multiple-outline mdi-24px"></i>
                                                </div>
                                            </div>
                                            <div class="card-info">
                                                <h5 class="mb-0">
                                                    Rp
                                                    <?php echo e(number_format($amountQuote, 2, ',', '.')); ?>

                                                </h5>
                                                <small class="text-muted">Quotation</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="d-flex mb-2 gap-2">
                                            <div class="avatar">
                                                <div class="avatar-initial bg-label-warning rounded">
                                                    <i class="mdi mdi-email-alert-outline mdi-24px"></i>
                                                </div>
                                            </div>
                                            <div class="card-info">
                                                <h5 class="mb-0">Rp <?php echo e(number_format($amountProspect, 2, ',', '.')); ?>

                                                </h5>
                                                <small class="text-muted">Hot Prospect</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="d-flex mb-2 gap-2">
                                            <div class="avatar">
                                                <div class="avatar-initial bg-label-danger rounded">
                                                    <i class="mdi mdi-cart-minus mdi-24px"></i>
                                                </div>
                                            </div>
                                            <div class="card-info">
                                                <h5 class="mb-0">
                                                    Rp
                                                    <?php echo e(number_format($amountQuoteLoss, 2, ',', '.')); ?>

                                                </h5>
                                                <small class="text-muted">Loss Quotation</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="row">
                                <div class="col-12">
                                    <h4><?php echo e($user->name); ?></h4>
                                </div>
                                <div class="col-4">
                                    <p class="fw-medium fs-normal">Key Performance Indicator</p>
                                    <div class="d-flex mb-2 gap-2">
                                        <a href="#activities">
                                            <div class="avatar">
                                                <div class="avatar-initial bg-label-info rounded">
                                                    <i class="mdi mdi-phone-outline mdi-24px"></i>
                                                </div>
                                            </div>
                                        </a>
                                        <div class="card-info">
                                            <h5 class="mb-0"><?php echo e($filteredProspect); ?> <span
                                                    class="text-muted fs-tiny fw-normal">/<?php echo e($target->dc ?? '-'); ?></span>
                                            </h5>
                                            <small class="text-muted">Prospect</small>
                                        </div>
                                    </div>
                                    <div class="d-flex mb-2 gap-2">
                                        <a href="#activities">
                                            <div class="avatar">
                                                <div class="avatar-initial bg-label-primary rounded">
                                                    <i class="mdi mdi-account-multiple-outline mdi-24px"></i>
                                                </div>
                                            </div>
                                        </a>
                                        <div class="card-info">
                                            <h5 class="mb-0"><?php echo e($filteredProvide); ?><span
                                                    class="text-muted fs-tiny fw-normal">/<?php echo e($jumlahCustomer); ?></span>
                                            </h5>
                                            <small class="text-muted">Provided</small>
                                        </div>
                                    </div>
                                    <div class="d-flex mb-2 gap-2">
                                        <a href="#quote">
                                            <div class="avatar">
                                                <div class="avatar-initial bg-label-warning rounded">
                                                    <i class="mdi mdi-email-multiple-outline mdi-24px"></i>
                                                </div>
                                            </div>
                                        </a>
                                        <div class="card-info">
                                            <h5 class="mb-0"><?php echo e($filteredProspectQuote); ?><span
                                                    class="text-muted fs-tiny fw-normal">/<?php echo e($target->quote ?? '-'); ?></span>
                                            </h5>
                                            <small class="text-muted">Quotation</small>
                                        </div>
                                    </div>
                                    <div class="d-flex mb-2 gap-2">
                                        <a href="#po">
                                            <div class="avatar">
                                                <div class="avatar-initial bg-label-success rounded">
                                                    <i class="mdi mdi-cart-plus mdi-24px"></i>
                                                </div>
                                            </div>
                                        </a>
                                        <div class="card-info">
                                            <h5 class="mb-0"><?php echo e($filteredProspectQuote); ?>

                                            </h5>
                                            <small class="text-muted">Purchase Order</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-8">
                                    <p class="fw-medium fs-normal">Achievement</p>

                                    <div class="d-flex align-items-center gap-2">
                                        <div class="d-flex mb-2 gap-2">
                                            <div class="avatar">
                                                <div class="avatar-initial bg-label-success rounded">
                                                    <i class="mdi mdi-cart-plus mdi-24px"></i>
                                                </div>
                                            </div>
                                            <div class="card-info">
                                                <h5 class="mb-0">Rp
                                                    <?php echo e(number_format($totalProspectPO, 2, ',', '.')); ?>

                                                </h5>
                                                <small class="text-muted">Total Sales</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="d-flex mb-2 gap-2">
                                            <div class="avatar">
                                                <div class="avatar-initial bg-label-primary rounded">
                                                    <i class="mdi mdi-email-multiple-outline mdi-24px"></i>
                                                </div>
                                            </div>
                                            <div class="card-info">
                                                <h5 class="mb-0">
                                                    Rp
                                                    <?php echo e(number_format($totalProspectQuote, 2, ',', '.')); ?>

                                                </h5>
                                                <small class="text-muted">Quotation</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="row mb-3">
                            <div class="col-8">
                                <div class="badge bg-primary w-100">
                                    <h4 class="text-white text-center my-3">Achievement</h4>
                                </div>
                            </div>
                            <div class="col-4">
                                <?php
                                    $jumlah_target = [];
                                    if (isset($target->total) && $target->total != 0) {
                                        $jumlah_target = ($amountSales / $target->total) * 100;
                                    } else {
                                        $jumlah_target = 0;
                                    }
                                ?>
                                <div class="badge bg-primary w-100">
                                    <h4 class="text-white text-center my-3">
                                        <?php echo e(number_format($jumlah_target, 3)); ?>%
                                    </h4>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-4 mb-3">
                                <div class="badge bg-label-dark w-100">
                                    <h5 class="my-3 text-start">Quotation</h5>
                                </div>
                            </div>
                            <div class="col-2 mb-3">
                                <div class="card shadow-none border-secondary border-2 h-100">
                                    <h5 class="text-center my-3"><?php echo e($totalQuote); ?></h5>
                                </div>
                            </div>
                            <div class="col-6 mb-3">
                                <div class="card shadow-none border-secondary border-2 h-100">
                                    <h5 class="text-end my-3 mx-2">Rp <?php echo e(number_format($amountQuote, 2, ',', '.')); ?></h5>
                                </div>
                            </div>
                            <div class="col-4 mb-3">
                                <div class="badge bg-label-dark w-100">
                                    <h5 class="my-3 text-start">Purchase Order</h5>
                                </div>
                            </div>
                            <div class="col-2 mb-3">
                                <div class="card shadow-none border-secondary border-2 h-100">
                                    <h5 class="text-center my-3"><?php echo e($totalPO); ?></h5>
                                </div>
                            </div>
                            <div class="col-6 mb-3">
                                <div class="card shadow-none border-secondary border-2 h-100">
                                    <h5 class="text-end my-3 mx-2">Rp <?php echo e(number_format($amountSales, 2, ',', '.')); ?></h5>
                                </div>
                            </div>
                            
                            <div class="col-4 mb-3">
                                <div class="badge bg-label-dark w-100">
                                    <h5 class="my-3 text-start">Loss Quotation</h5>
                                </div>
                            </div>
                            <div class="col-2 mb-3">
                                <div class="card shadow-none border-secondary border-2 h-100">
                                    <h5 class="text-center my-3"><?php echo e($totalLoss); ?></h5>
                                </div>
                            </div>
                            <div class="col-6 mb-3">
                                <div class="card shadow-none border-secondary border-2 h-100">
                                    <h5 class="text-end my-3 mx-2">Rp <?php echo e(number_format($amountQuoteLoss, 2, ',', '.')); ?>

                                    </h5>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <?php if($user->id == '16'): ?>
        <div class="card mb-3">
            <div class="card-header">
                <h5>Key Performance Indicator</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 col-12">
                        <div class="row mb-3">
                            <div class="col-4" style="padding-right: 0;">
                                <div class="card bg-primary text-white w-100" data-bs-toggle="modal"
                                    data-bs-target="#newProduct">
                                    <h5 class="card-title
                                    text-white text-center my-4">
                                        <i class="menu-icon tf-icons mdi mdi-reproduction m-0 fs-1"></i>
                                    </h5>
                                </div>
                            </div>
                            <div class="col-8 d-flex flex-column justify-content-between">
                                <div class="card shadow-none bg-label-primary border-primary border-2">
                                    <h5 class="card-title text-center my-2">
                                        New Product
                                    </h5>
                                </div>
                                <div class="card shadow-none bg-label-primary border-primary border-2 mt-auto"
                                    style="border: dashed;">
                                    <h5 class="card-title text-center my-2">
                                        <?php echo e($productCount); ?> / 100
                                    </h5>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-4" style="padding-right: 0;">
                                <div class="card bg-warning text-white w-100">
                                    <h5 class="card-title text-white text-center my-4">
                                        <i class="menu-icon tf-icons mdi mdi-package-variant-closed-check m-0 fs-1"></i>
                                    </h5>
                                </div>
                            </div>
                            <div class="col-8 d-flex flex-column justify-content-between">
                                <div class="card shadow-none bg-label-warning border-warning border-2">
                                    <h5 class="card-title text-center my-2">
                                        Akurasi Data
                                    </h5>
                                </div>
                                <div class="card shadow-none bg-label-warning border-warning border-2 mt-auto"
                                    style="border: dashed;">
                                    <h5 class="card-title text-center my-2">
                                        <?php if(@$akurasiCount[0]): ?>
                                            <?php
                                                $dataAkurasi = $akurasiCount->count();
                                                $persenAkurasi = 0;
                                                $jumlahAkurasi = 0;
                                            ?>
                                            <?php $__currentLoopData = $akurasiCount; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <?php
                                                    $jumlahAkurasi += $item->average;
                                                ?>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            <?php
                                                $jumlahAkurasi / $dataAkurasi;
                                                $persenAkurasi = ($jumlahAkurasi / 5) * 100;
                                            ?>
                                        <?php endif; ?>
                                        <?php echo e(@$persenAkurasi ?? 0); ?> %
                                    </h5>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3 align-items-stretch cursor-pointer">
                            <div class="col-4" style="padding-right: 0;">
                                <div class="card border-warning bg-warning border-1 w-100 h-100">
                                    <h5 class="card-title text-center text-white my-4">
                                        <i class="menu-icon tf-icons mdi mdi-truck-delivery-outline m-0 fs-1"></i>
                                    </h5>
                                </div>
                            </div>

                            <div class="col-8 d-flex flex-column justify-content-between">
                                <div class="card bg-label-warning border-warning border-1 shadow-none">
                                    <h5 class="card-title text-center my-2">
                                        Delivery & Success
                                    </h5>
                                </div>
                                <div class="card bg-label-warning border-warning border-2 shadow-none mt-auto"
                                    style="border-style: dashed;">
                                    <h5 class="card-title text-center my-2">
                                        <?php if(@$deliveryCount[0]): ?>
                                            <?php
                                                $dataDelivery = $deliveryCount->count();
                                                $persenDelivery = 0;
                                                $jumlahDelivery = 0;
                                            ?>
                                            <?php $__currentLoopData = $deliveryCount; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <?php
                                                    $jumlahDelivery += $item->average;
                                                ?>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            <?php
                                                $jumlahDelivery / $dataDelivery;
                                                $persenDelivery = ($jumlahDelivery / 5) * 100;
                                            ?>
                                        <?php endif; ?>
                                        <?php echo e(@$persenDelivery ?? 0); ?> %
                                    </h5>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-12">
                        <div class="row mb-3">
                            <div class="col-4" style="padding-right: 0;">
                                <div class="card bg-warning text-white w-100 ">
                                    <h5 class="card-title text-white text-center my-4">
                                        <i class="menu-icon tf-icons mdi mdi-account-heart-outline m-0 fs-1"></i>
                                    </h5>
                                </div>
                            </div>
                            <div class="col-8 d-flex flex-column justify-content-between">
                                <div class="card shadow-none bg-label-warning border-warning border-2">
                                    <h5 class="card-title text-center my-2">
                                        Response Chat
                                    </h5>
                                </div>
                                <div class="card shadow-none bg-label-warning border-warning border-2 mt-auto"
                                    style="border: dashed;">
                                    <h5 class="card-title text-center my-2">
                                        <?php if(@$responseCount[0]): ?>
                                            <?php
                                                $dataResponse = $responseCount->count();
                                                $persenResponse = 0;
                                                $jumlahResponse = 0;
                                            ?>
                                            <?php $__currentLoopData = $responseCount; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <?php
                                                    $jumlahResponse += $item->average;
                                                ?>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            <?php

                                                $persenResponse = $jumlahResponse / $dataResponse;
                                            ?>
                                        <?php endif; ?>
                                        <?php echo e(@$persenResponse ?? 0); ?> %
                                    </h5>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-4" style="padding-right: 0;">
                                <div class="card bg-warning text-white w-100">
                                    <h5 class="card-title text-white text-center my-4">
                                        <i class="menu-icon tf-icons mdi mdi-monitor-star m-0 fs-1"></i>
                                    </h5>
                                </div>
                            </div>
                            <div class="col-8 d-flex flex-column justify-content-between">
                                <div class="card shadow-none bg-label-warning border-warning border-2">
                                    <h5 class="card-title text-center my-2">
                                        Score Toko
                                    </h5>
                                </div>
                                <div class="card shadow-none bg-label-warning border-warning border-2 mt-auto"
                                    style="border: dashed;">
                                    <h5 class="card-title text-center my-2">
                                        <?php if(@$ratingCount[0]): ?>
                                            <?php
                                                $dataRating = $ratingCount->count();
                                                $persenRating = 0;
                                                $jumlahRating = 0;
                                            ?>
                                            <?php $__currentLoopData = $ratingCount; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <?php
                                                    $jumlahRating += $item->average;
                                                ?>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            <?php
                                                $persenRating = $jumlahRating / $dataRating;
                                            ?>
                                        <?php endif; ?>
                                        Rating <?php echo e(@$persenRating ?? 0); ?>

                                    </h5>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3 align-items-stretch">
                            <div class="col-4" style="padding-right: 0;">
                                <div class="card bg-warning border-warning text-white border-1 w-100 h-100">
                                    <h5 class="card-title text-white text-center my-4">
                                        <i class="menu-icon tf-icons mdi mdi-cart-check m-0 fs-1"></i>
                                    </h5>
                                </div>
                            </div>

                            <div class="col-8 d-flex flex-column justify-content-between">
                                <div class="card bg-label-warning border-warning border-2 shadow-none">
                                    <h5 class="card-title text-center my-2">
                                        Customer Care
                                    </h5>
                                </div>
                                <div class="card bg-label-warning border-warning border-2 shadow-none mt-auto"
                                    style="border-style: dashed;">
                                    <h5 class="card-title text-center my-2">
                                        <?php if(@$customerCount[0]): ?>
                                            <?php
                                                $dataCustomer = $customerCount->count();
                                                $persenCustomer = 0;
                                                $jumlahCustomers = 0;
                                            ?>
                                            <?php $__currentLoopData = $customerCount; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <?php
                                                    $jumlahCustomers += $item->average;
                                                ?>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            <?php
                                                $jumlahCustomers / $dataCustomer;
                                                $persenCustomer = ($jumlahCustomers / 5) * 100;
                                            ?>
                                        <?php endif; ?>
                                        <?php echo e(@$persenCustomer ?? 0); ?> %
                                    </h5>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-12">
                        <div class="row mb-3">
                            <div class="col-4" style="padding-right: 0;">
                                <div class="card bg-primary text-white w-100">
                                    <h5 class="card-title text-white text-center my-4">
                                        <i class="menu-icon tf-icons mdi mdi-whatsapp m-0 fs-1"></i>
                                    </h5>
                                </div>
                            </div>
                            <div class="col-8 d-flex flex-column justify-content-between">
                                <div class="card shadow-none bg-label-primary border-primary border-2">
                                    <h5 class="card-title text-center my-2">
                                        Update SW (3/Days)
                                    </h5>
                                </div>
                                <div class="card shadow-none bg-label-primary border-primary border-2 mt-auto"
                                    style="border: dashed;">
                                    <h5 class="card-title text-center my-2">
                                        <?php if(@$SWCount[0]): ?>
                                            <?php
                                                $dataSW = $SWCount->count();
                                                $persenSW = 0;
                                                $jumlahSW = 0;
                                            ?>
                                            <?php $__currentLoopData = $SWCount; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <?php
                                                    $jumlahSW += $item->airend;
                                                    $jumlahSW += $item->kojisha;
                                                ?>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            <?php
                                                $persenSW = $jumlahSW / $dataSW;
                                            ?>
                                        <?php endif; ?>
                                        <?php echo e(@$persenSW ?? 0); ?> /
                                        <?php echo e(Auth::user()->id == 16 ? '120' : '60'); ?>

                                    </h5>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-4" style="padding-right: 0;">
                                <div class="card bg-primary text-white w-100">
                                    <h5 class="card-title text-white text-center my-4">
                                        <i class="menu-icon tf-icons mdi mdi-video-outline m-0 fs-1"></i>
                                    </h5>
                                </div>
                            </div>
                            <div class="col-8 d-flex flex-column justify-content-between">
                                <div class="card shadow-none bg-label-primary border-primary border-2">
                                    <h5 class="card-title text-center my-2">
                                        Video ( 1/Days )
                                    </h5>
                                </div>
                                <div class="card shadow-none bg-label-primary border-primary border-2 mt-auto"
                                    style="border: dashed;">
                                    <h5 class="card-title text-center my-2">
                                        <?php if(@$videoCount[0]): ?>
                                            <?php
                                                $dataVideo = $videoCount->count();
                                                $persenVideo = 0;
                                                $jumlahVideo = 0;
                                            ?>
                                            <?php $__currentLoopData = $videoCount; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <?php
                                                    if ($item->ig) {
                                                        $jumlahVideo += 30;
                                                    }
                                                    if ($item->tiktok) {
                                                        $jumlahVideo += 30;
                                                    }
                                                    if ($item->tokped) {
                                                        $jumlahVideo += 40;
                                                    }
                                                ?>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            <?php
                                                $persenVideo = $jumlahVideo / $dataVideo;
                                            ?>
                                        <?php endif; ?>
                                        <?php echo e(@$persenVideo ?? 0); ?> %
                                    </h5>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3 align-items-stretch">
                            <div class="col-4" style="padding-right: 0;">
                                <div class="card border-success bg-success border-1 w-100 h-100">
                                    <h5 class="card-title text-white text-center my-4">
                                        <i class="menu-icon tf-icons mdi mdi-account-group  m-0 fs-1"></i>
                                    </h5>
                                </div>
                            </div>

                            <div class="col-8 d-flex flex-column justify-content-between">
                                <div class="card border-success bg-label-success border-1 shadow-none">
                                    <h5 class="card-title text-center my-2">
                                        CRM
                                    </h5>
                                </div>
                                <div class="card border-success bg-label-success border-2 shadow-none mt-auto"
                                    style="border-style: dashed;">
                                    <h5 class="card-title text-center my-2"><?php echo e($totalCRM); ?><span
                                            class="text-muted fs-tiny fw-normal">/<?php echo e($jumlahCustomer); ?></span>
                                    </h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
    <?php if($user->role == 'Sales'): ?>
        <div class="row">
            <?php if($user->id != '16'): ?>
                <div class="col-12 col-md-6 mb-3">
                    <div class="card" id="activities">
                        <div class="card-datatable table-responsive pt-0">
                            <table class="datatable-overview-call table table-striped" id="dataTableCrm">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th></th>
                                        <th>ID</th>
                                        <th>Company</th>
                                        <th>Status</th>
                                        <th>Note</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6 mb-3">
                    <div class="card">
                        <div class="card-datatable table-responsive pt-0">
                            <table class="datatable-overview-crm table table-striped" id="dataTableCrm">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th></th>
                                        <th>ID</th>
                                        <th>Company</th>
                                        <th>Status</th>
                                        <th>Note</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            <div class="col-12 mb-3">
                <div class="card" id="quote">
                    <div class="card-datatable table-responsive pt-0">
                        <table class="datatable-overview-quotation table table-striped">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th>ID</th>
                                    <th>Quote No.</th>
                                    <th>Company</th>
                                    <th>Total Price</th>
                                    <th>Description</th>
                                    <th>Date Quotation</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-12 mb-3">
                <div class="card" id="quote">
                    <div class="card-datatable table-responsive pt-0">
                        <table class="datatable-overview-po table table-striped">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th>ID</th>
                                    <th>Quote No.</th>
                                    <th>Company</th>
                                    <th>Description</th>
                                    <th>Date PO</th>
                                    <th>Total Price</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-12 mb-3">
                <div class="card" id="quote">
                    <div class="card-datatable table-responsive pt-0">
                        <table class="datatable-overview-loss table table-striped">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th>ID</th>
                                    <th>Quote No.</th>
                                    <th>Company</th>
                                    <th>Description</th>
                                    <th>Date</th>
                                    <th>Total Price</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
            
            
        </div>
    <?php else: ?>
        <div class="row">
            <div class="col-12 mb-3">
                <div class="card" id="quote">
                    <div class="card-datatable table-responsive pt-0">
                        <table class="datatable-overview-po-prospect table table-striped">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th>ID</th>
                                    <th>Quote No.</th>
                                    <th>Company</th>
                                    <th>Description</th>
                                    <th>Date PO</th>
                                    <th>Total Price</th>
                                    <th>Sales</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
    <?php echo $__env->make('components.modal.onlineSales.kpi.new-product', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>


<?php $__env->startPush('after-style'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
    <link rel="stylesheet"
        href="<?php echo e(asset('assets')); ?>/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
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
    <script src="<?php echo e(asset('assets')); ?>/js/tables-datatables-basic.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/includes/table-overview-call.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/includes/table-overview-crm.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/includes/table-overview-quotation.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/includes/table-overview-po.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/includes/table-overview-loss.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/includes/table-overview-po-prospect.js"></script>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('script'); ?>
    <script>
        // Initialize Bootstrap tooltips using jQuery
        $(document).ready(function() {
            $('[data-bs-toggle="tooltip"]').tooltip();
        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.sales.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u877155683/domains/reftech.my.id/reftech.my.id/resources/views/pages/admin/overview/kpi.blade.php ENDPATH**/ ?>